<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\OrderFiltersDto;
use App\DTOs\Admin\OrderItemsUpdateDto;
use App\DTOs\Admin\OrderStatusUpdateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelOrderRequest;
use App\Http\Requests\Admin\OrderIndexRequest;
use App\Http\Requests\Admin\UpdateOrderItemsRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\Admin\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function index(OrderIndexRequest $request): View
    {
        $this->authorize('viewAny', Order::class);

        $dto = OrderFiltersDto::fromRequest($request);
        $orders = $this->orderService->getPaginatedOrders($dto);

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $dto,
            'statuses' => Order::STATUS_LABELS,
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('admin.orders.show', [
            'order' => $this->orderService->getOrder($order),
            'statuses' => Order::STATUS_LABELS,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $dto = OrderStatusUpdateDto::fromRequest($request);
        $this->orderService->updateStatus($order, $dto);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Статус заказа успешно обновлён.');
    }

    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancel($order);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Заказ успешно отменён.');
    }

    public function editItems(Order $order): View
    {
        $this->authorize('editItems', $order);

        return view('admin.orders.edit-items', [
            'order' => $this->orderService->getEditableOrder($order),
        ]);
    }

    public function updateItems(UpdateOrderItemsRequest $request, Order $order): RedirectResponse
    {
        $dto = OrderItemsUpdateDto::fromRequest($request);

        try {
            $this->orderService->updateItems($order, $dto);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.orders.edit-items', $order)
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Состав заказа успешно обновлён.');
    }
}
