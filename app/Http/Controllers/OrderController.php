<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        private readonly YooKassaPaymentService $paymentService,
    ) {
    }

    public function index(): View
    {
        $orders = $this->orderService->getUserOrders(10);

        return view('orders.index', compact('orders'));
    }

    public function show(int $id): View|RedirectResponse
    {
        $order = $this->orderService->getOrderDetails($id);

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Заказ не найден');
        }

        return view('orders.show', compact('order'));
    }

    public function pay(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 404);

        if ($order->payment_method !== Order::PAYMENT_METHOD_YOOKASSA) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Для этого заказа выбрана оплата при получении.');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Этот заказ уже оплачен или недоступен для оплаты.');
        }

        try {
            $payment = $this->paymentService->createPaymentForOrder($order);
        } catch (Throwable $exception) {
            Log::error('Failed to retry YooKassa payment', [
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Не удалось сформировать ссылку на оплату. Попробуйте ещё раз позже.');
        }

        if (!$payment->confirmation_url) {
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Статус оплаты заказа обновлён.');
        }

        return redirect()->away($payment->confirmation_url);
    }

    public function cancel(int $id): RedirectResponse
    {
        $result = $this->orderService->cancelOrder($id);

        if ($result['success']) {
            return redirect()->route('orders.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->route('orders.show', $id)
            ->with('error', $result['message']);
    }
}
