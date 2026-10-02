<?php

namespace App\DTOs;

use App\Models\Order;
use App\Models\OrderPayment;

class OrderData
{
    public function __construct(
        public readonly int $id,
        public readonly string $order_number,
        public readonly float $total_amount,
        public readonly int $total_quantity,
        public readonly string $status,
        public readonly string $status_label,
        public readonly string $payment_method,
        public readonly string $payment_method_label,
        public readonly string $payment_status,
        public readonly string $recipient_name,
        public readonly string $recipient_phone,
        public readonly string $shipping_address,
        public readonly ?string $comment,
        public readonly string $created_at,
        public readonly array $items = []
    ) {}

    public static function fromModel(Order $order, array $items = []): self
    {
        $payments = $order->payments->sortByDesc('id');

        $latestPaymentStatus = $payments->isEmpty()
            ? null
            : $payments->first()->status;

        return new self(
            id: $order->id,
            order_number: $order->order_number,
            total_amount: (float) $order->total_amount,
            total_quantity: $order->total_quantity,
            status: $order->status,
            status_label: $order->status_label,
            payment_method: $order->payment_method,
            payment_method_label: $order->payment_method_label,
            payment_status: $latestPaymentStatus
            ?? $order->payment_status
            ?? OrderPayment::STATUS_PENDING,
            recipient_name: $order->recipient_name,
            recipient_phone: $order->recipient_phone,
            shipping_address: $order->shipping_address,
            comment: $order->comment,
            created_at: $order->created_at->format('d.m.Y H:i'),
            items: $items,
        );
    }

}
