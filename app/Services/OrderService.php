<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\Jobs\SendOrderShippedNotificationJob;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

class OrderService
{
    /**
     * Получить заказы текущего пользователя с пагинацией
     */
    public function getUserOrders(int $perPage = 10): LengthAwarePaginator
    {
        $user = Auth::user();

        $orders = Order::with(['items', 'payments'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Преобразуем в DTO
        $orders->getCollection()->transform(function ($order) {
            return $this->transformToDTO($order);
        });

        return $orders;
    }

    /**
     * Получить детали конкретного заказа
     */
    public function getOrderDetails(int $orderId): ?OrderData
    {
        $user = Auth::user();

        $order = Order::with(['items', 'payments'])
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return null;
        }

        return $this->transformToDTO($order);
    }

    /**
     * Получить все заказы (для админа)
     */
    public function getAllOrders(int $perPage = 20): LengthAwarePaginator
    {
        $orders = Order::with(['user', 'items', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $orders->getCollection()->transform(function ($order) {
            return $this->transformToDTO($order);
        });

        return $orders;
    }

    /**
     * Обновить статус заказа
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $order = Order::find($orderId);

        if (!$order) {
            return false;
        }

        $previousStatus = $order->status;
        $order->status = $status;
        $saved = $order->save();

        if ($saved && $previousStatus !== Order::STATUS_SHIPPED && $status === Order::STATUS_SHIPPED) {
            SendOrderShippedNotificationJob::dispatch($order->id);
        }

        return $saved;
    }

    public function markAsPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (in_array($lockedOrder->status, [
                Order::STATUS_ACCEPTED,
                Order::STATUS_PAID,
                Order::STATUS_SHIPPED,
                Order::STATUS_COMPLETED,
            ], true)) {
                return $lockedOrder;
            }

            if ($lockedOrder->status === Order::STATUS_CANCELED) {
                throw new RuntimeException('Нельзя отметить отменённый заказ оплаченным.');
            }

            $lockedOrder->update([
                'status' => Order::STATUS_ACCEPTED,
            ]);

            return $lockedOrder->refresh();
        });
    }

    /**
     * Отменить заказ (только если статус pending или paid)
     */
    public function cancelOrder(int $orderId): array
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return ['success' => false, 'message' => 'Заказ не найден'];
        }

        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_PAID])) {
            return ['success' => false, 'message' => 'Этот заказ нельзя отменить'];
        }

        // Возвращаем товары на склад
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        $order->status = Order::STATUS_CANCELED;
        $order->save();

        return ['success' => true, 'message' => 'Заказ успешно отменен'];
    }

    /**
     * Трансформация модели в DTO
     */
    private function transformToDTO(Order $order): OrderData
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'product_price' => (float) $item->product_price,
                'subtotal' => (float) $item->subtotal,
            ];
        }

        // Передаем items в конструктор через fromModel
        return OrderData::fromModel($order, $items);
    }
}
