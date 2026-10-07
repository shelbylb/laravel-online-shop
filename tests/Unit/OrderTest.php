<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Order;

class OrderTest extends TestCase
{
    public function test_pending_order_can_be_canceled(): void
    {
        // Arrange
        $order = new Order();
        $order->status = Order::STATUS_PENDING;

        // Act
        $result = $order->canBeCanceled();

        // Assert
        $this->assertTrue($result);

    }

    public function test_completed_order_cannot_be_canceled(): void
    {
        // Arrange
        $order = new Order();
        $order->status = Order::STATUS_COMPLETED;

        // Act
        $result = $order->canBeCanceled();


        // Assert
        $this->assertFalse($result);

    }
}
