<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderCreatedNotificationsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $orderId
    ) {
        $this->onConnection('rabbitmq');
        $this->onQueue('orders.notifications.created');
    }

    public function handle(OrderNotificationService $notificationService): void
    {
        $order = Order::query()
            ->with(['user', 'items'])
            ->find($this->orderId);

        if (!$order) {
            Log::error("Order not found id:{$this->orderId}");
            return;
        }

        $notificationService->sendOrderCreated($order);
    }
}
