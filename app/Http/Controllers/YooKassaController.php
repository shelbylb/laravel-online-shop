<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class YooKassaController extends Controller
{
    public function __construct(
        private readonly YooKassaPaymentService $paymentService,
    ) {
    }

    public function return(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $payment = $order->payments()
            ->where('provider', OrderPayment::PROVIDER_YOOKASSA)
            ->whereNotNull('external_payment_id')
            ->latest('id')
            ->first();

        if (!$payment) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Платёж для заказа не найден.');
        }

        try {
            $payment = $this->paymentService->synchronizePayment(
                $payment,
                $this->paymentService->fetchPayment($payment->external_payment_id),
            );
        } catch (Throwable $exception) {
            Log::error('Failed to synchronize YooKassa payment after return', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Не удалось проверить статус оплаты. Попробуйте ещё раз позже.');
        }

        return match ($payment->status) {
            OrderPayment::STATUS_SUCCEEDED => redirect()->route('orders.show', $order->id)
                ->with('success', 'Заказ успешно оплачен.'),
            OrderPayment::STATUS_CANCELED => redirect()->route('orders.show', $order->id)
                ->with('error', 'Оплата была отменена.'),
            default => redirect()->route('orders.show', $order->id)
                ->with('info', 'Платёж обрабатывается. Статус обновится автоматически.'),
        };
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        try {
            $this->paymentService->handleWebhook($payload);
        } catch (Throwable $exception) {
            Log::error('Failed to handle YooKassa webhook', [
                'external_payment_id' => data_get($payload, 'object.id'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
