<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTOs\Admin\OrderFiltersDto;
use App\DTOs\Admin\OrderItemsUpdateDto;
use App\DTOs\Admin\OrderStatusUpdateDto;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function getPaginatedOrders(OrderFiltersDto $dto, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::query()
            ->with('user')
            ->withCount('items')
            ->orderByDesc('id');

        if ($dto->q) {
            $search = $dto->q;

            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery
                    ->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $search . '%')
                    ->orWhere('recipient_phone', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getOrder(Order $order): Order
    {
        return $order->load([
            'user',
            'items.product',
        ]);
    }

    public function updateStatus(Order $order, OrderStatusUpdateDto $dto): Order
    {
        $order->update([
            'status' => $dto->status,
        ]);

        return $order->refresh();
    }

    public function cancel(Order $order): Order
    {
        if (!$order->canBeCanceled()) {
            throw new RuntimeException('Этот заказ нельзя отменить.');
        }

        $order->update([
            'status' => Order::STATUS_CANCELED,
        ]);

        return $order->refresh();
    }

    public function getEditableOrder(Order $order): Order
    {
        return $order->load([
            'user',
            'items.product',
        ]);
    }

    public function updateItems(Order $order, OrderItemsUpdateDto $dto): Order
    {
        if (!$order->canEditItems()) {
            throw new RuntimeException('Состав заказа можно менять только у заказа со статусом "Ожидает оплаты".');
        }

        DB::transaction(function () use ($order, $dto) {
            $itemsById = $order->items()
                ->get()
                ->keyBy('id');

            foreach ($dto->items as $itemData) {
                /** @var OrderItem|null $item */
                $item = $itemsById->get($itemData['id']);

                if (!$item) {
                    continue;
                }

                $quantity = $itemData['quantity'];

                if ($quantity <= 0) {
                    $item->delete();
                    continue;
                }

                $subtotal = bcmul((string) $item->product_price, (string) $quantity, 2);

                $item->update([
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            $this->recalculateTotals($order);
        });

        return $order->refresh()->load([
            'user',
            'items.product',
        ]);
    }

    private function recalculateTotals(Order $order): void
    {
        $totals = $order->items()
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as total_amount')
            ->first();

        $order->update([
            'total_quantity' => (int) ($totals->total_quantity ?? 0),
            'total_amount' => $totals->total_amount ?? 0,
        ]);
    }
}
