<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Список заказов пользователя
     */
    public function index(): View
    {
        $orders = $this->orderService->getUserOrders(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Детали конкретного заказа
     */
    public function show(int $id): View|RedirectResponse
    {
        $order = $this->orderService->getOrderDetails($id);

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Заказ не найден');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Отмена заказа
     */
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
