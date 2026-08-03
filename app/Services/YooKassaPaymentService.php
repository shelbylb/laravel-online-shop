<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use YooKassa\Client;
use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Request\Receipts\ReceiptResponseInterface;

class YooKassaPaymentService
{
    private Client $client;

    public function __construct(private readonly OrderService $orderService)
    {
        $shopId = (string) config('services.yookassa.shop_id');
        $secretKey = (string) config('services.yookassa.secret_key');

        if ($shopId === '' || $secretKey === '') {
            throw new RuntimeException('Не настроены учётные данные YooKassa.');
        }

        $this->client = new Client();
        $this->client->setAuth($shopId, $secretKey);
    }

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        $order->loadMissing(['user', 'items']);

        $existingPayment = $order->payments()
            ->where('provider', OrderPayment::PROVIDER_YOOKASSA)
            ->whereIn('status', [
                OrderPayment::STATUS_PENDING,
                OrderPayment::STATUS_WAITING_FOR_CAPTURE,
                OrderPayment::STATUS_SUCCEEDED,
            ])
            ->latest('id')
            ->first();

        if ($existingPayment?->external_payment_id) {
            return $existingPayment;
        }

        $idempotenceKey = $existingPayment?->idempotence_key ?? (string) Str::uuid();
        $requestPayload = $this->buildPaymentPayload($order);

        $localPayment = $existingPayment ?? $order->payments()->create([
            'provider' => OrderPayment::PROVIDER_YOOKASSA,
            'status' => OrderPayment::STATUS_PENDING,
            'amount' => $order->total_amount,
            'currency' => (string) config('services.yookassa.currency', 'RUB'),
            'idempotence_key' => $idempotenceKey,
            'request_payload' => $requestPayload,
        ]);

        try {
            $externalPayment = $this->client->createPayment($requestPayload, $idempotenceKey);

            if (!$externalPayment) {
                throw new RuntimeException('YooKassa не вернула данные созданного платежа.');
            }

            return $this->synchronizePayment($localPayment, $externalPayment);
        } catch (Throwable $exception) {
            $localPayment->update(['error_message' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function handleWebhook(array $payload): OrderPayment
    {
        $paymentId = data_get($payload, 'object.id') ?? data_get($payload, 'payment_id');

        if (!is_string($paymentId) || $paymentId === '') {
            throw new RuntimeException('Webhook YooKassa не содержит идентификатор платежа.');
        }

        $localPayment = OrderPayment::query()
            ->where('provider', OrderPayment::PROVIDER_YOOKASSA)
            ->where('external_payment_id', $paymentId)
            ->firstOrFail();

        return $this->synchronizePayment($localPayment, $this->fetchPayment($paymentId));
    }

    public function fetchPayment(string $paymentId): PaymentInterface
    {
        $payment = $this->client->getPaymentInfo($paymentId);

        if (!$payment) {
            throw new RuntimeException("Платёж YooKassa {$paymentId} не найден.");
        }

        return $payment;
    }

    public function synchronizePayment(OrderPayment $localPayment, PaymentInterface $externalPayment): OrderPayment
    {
        $externalPaymentId = $externalPayment->getId();
        $status = $externalPayment->getStatus();

        if (!$externalPaymentId || !in_array($status, OrderPayment::STATUSES, true)) {
            throw new RuntimeException('YooKassa вернула платёж с неизвестным идентификатором или статусом.');
        }

        if ($localPayment->external_payment_id && $localPayment->external_payment_id !== $externalPaymentId) {
            throw new RuntimeException('Идентификатор платежа YooKassa не совпадает с локальным платежом.');
        }

        $externalAmount = $externalPayment->getAmount();
        if (!$externalAmount
            || bccomp((string) $localPayment->amount, (string) $externalAmount->getValue(), 2) !== 0
            || $localPayment->currency !== $externalAmount->getCurrency()) {
            throw new RuntimeException('Сумма или валюта платежа YooKassa не совпадает с локальным платежом.');
        }

        $responsePayload = $this->objectToArray($externalPayment);
        if ((string) data_get($responsePayload, 'metadata.order_id') !== (string) $localPayment->order_id) {
            throw new RuntimeException('Платёж YooKassa относится к другому заказу.');
        }

        $externalReceipt = $this->fetchReceiptForPayment($externalPaymentId);

        return DB::transaction(function () use (
            $localPayment,
            $externalPayment,
            $externalPaymentId,
            $externalReceipt,
            $responsePayload,
            $status,
        ) {
            $payment = OrderPayment::query()->lockForUpdate()->findOrFail($localPayment->id);
            $confirmation = $externalPayment->getConfirmation();
            $confirmationUrl = $confirmation && method_exists($confirmation, 'getConfirmationUrl')
                ? $confirmation->getConfirmationUrl()
                : null;

            $payment->update([
                'external_payment_id' => $externalPaymentId,
                'status' => $status,
                'confirmation_url' => $confirmationUrl ?: $payment->confirmation_url,
                'response_payload' => $responsePayload,
                'paid_at' => $status === OrderPayment::STATUS_SUCCEEDED ? ($payment->paid_at ?? now()) : $payment->paid_at,
                'canceled_at' => $status === OrderPayment::STATUS_CANCELED ? ($payment->canceled_at ?? now()) : $payment->canceled_at,
                'error_message' => null,
            ]);

            $this->synchronizeEmbeddedReceipt(
                $payment,
                $externalPayment->getReceiptRegistration(),
                $externalReceipt,
            );

            if ($status === OrderPayment::STATUS_SUCCEEDED) {
                $this->orderService->markAsPaid($payment->order);
            }

            return $payment->refresh();
        });
    }

    public function createReceipt(OrderPayment $payment, string $type = PaymentReceipt::TYPE_PAYMENT): PaymentReceipt
    {
        if (!$payment->external_payment_id) {
            throw new RuntimeException('Нельзя создать чек до создания платежа в YooKassa.');
        }

        $payment->loadMissing(['order.user', 'order.items']);
        $requestPayload = [
            'type' => $type,
            'payment_id' => $payment->external_payment_id,
            ...$this->buildReceiptPayload($payment->order),
            'send' => true,
            'settlements' => [[
                'type' => (string) config('services.yookassa.receipts.settlement_type', 'cashless'),
                'amount' => [
                    'value' => (string) $payment->amount,
                    'currency' => $payment->currency,
                ],
            ]],
        ];

        $receipt = $payment->receipts()->create([
            'type' => $type,
            'status' => PaymentReceipt::STATUS_PENDING,
            'send_to_customer' => true,
            'request_payload' => $requestPayload,
        ]);

        try {
            $externalReceipt = $this->client->createReceipt($requestPayload, (string) Str::uuid());

            if (!$externalReceipt) {
                throw new RuntimeException('YooKassa не вернула данные созданного чека.');
            }

            $receipt->update([
                'external_receipt_id' => $externalReceipt->getId(),
                'status' => $externalReceipt->getStatus(),
                'response_payload' => $this->objectToArray($externalReceipt),
                'error_message' => null,
            ]);

            return $receipt->refresh();
        } catch (Throwable $exception) {
            $receipt->update(['error_message' => $exception->getMessage()]);
            throw $exception;
        }
    }

    private function buildPaymentPayload(Order $order): array
    {
        return [
            'amount' => [
                'value' => (string) $order->total_amount,
                'currency' => (string) config('services.yookassa.currency', 'RUB'),
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payments.yookassa.return', $order),
            ],
            'description' => 'Оплата заказа #' . $order->id,
            'metadata' => ['order_id' => $order->id],
            'receipt' => $this->buildReceiptPayload($order),
        ];
    }

    private function buildReceiptPayload(Order $order): array
    {
        $customer = array_filter([
            'email' => $order->user?->email,
            'phone' => $order->recipient_phone,
        ]);

        if ($customer === []) {
            throw new RuntimeException('Для чека требуется email или телефон покупателя.');
        }

        return [
            'customer' => $customer,
            'items' => $order->items->map(fn ($item) => [
                'description' => Str::limit($item->product_name, 128, ''),
                'quantity' => (string) $item->quantity,
                'amount' => [
                    'value' => (string) $item->product_price,
                    'currency' => (string) config('services.yookassa.currency', 'RUB'),
                ],
                'vat_code' => (int) config('services.yookassa.receipts.vat_code', 1),
                'payment_mode' => (string) config('services.yookassa.receipts.payment_mode', 'full_payment'),
                'payment_subject' => (string) config('services.yookassa.receipts.payment_subject', 'commodity'),
            ])->values()->all(),
            'tax_system_code' => (int) config('services.yookassa.receipts.tax_system_code', 1),
        ];
    }

    private function fetchReceiptForPayment(string $externalPaymentId): ?ReceiptResponseInterface
    {
        try {
            $receipts = $this->client->getReceipts([
                'payment_id' => $externalPaymentId,
                'limit' => 1,
            ]);

            foreach ($receipts->getItems() as $receipt) {
                return $receipt;
            }
        } catch (Throwable $exception) {
            Log::warning('Failed to fetch YooKassa receipt', [
                'external_payment_id' => $externalPaymentId,
                'error' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    private function synchronizeEmbeddedReceipt(
        OrderPayment $payment,
        ?string $receiptRegistration,
        ?ReceiptResponseInterface $externalReceipt,
    ): void {
        $requestPayload = data_get($payment->request_payload, 'receipt');

        if (!is_array($requestPayload)) {
            return;
        }

        $status = $externalReceipt?->getStatus() ?? $receiptRegistration;
        if (!in_array($status, [
            PaymentReceipt::STATUS_PENDING,
            PaymentReceipt::STATUS_SUCCEEDED,
            PaymentReceipt::STATUS_CANCELED,
        ], true)) {
            $status = PaymentReceipt::STATUS_PENDING;
        }

        $receipt = $payment->receipts()->firstOrNew([
            'type' => PaymentReceipt::TYPE_PAYMENT,
        ]);

        $receipt->fill([
            'external_receipt_id' => $externalReceipt?->getId()
                ?? $receipt->external_receipt_id,
            'status' => $status,
            'send_to_customer' => true,
            'request_payload' => $requestPayload,
            'response_payload' => $externalReceipt
                ? $this->objectToArray($externalReceipt)
                : ['receipt_registration' => $receiptRegistration],
            'error_message' => null,
        ]);

        $receipt->save();
    }

    private function objectToArray(object $object): array
    {
        return json_decode(
            json_encode($object, JSON_THROW_ON_ERROR),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
