<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    public const string TYPE_PAYMENT = 'payment';
    public const string TYPE_REFUND = 'refund';

    public const string STATUS_PENDING = 'pending';
    public const string STATUS_SUCCEEDED = 'succeeded';
    public const string STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'order_payment_id',
        'external_receipt_id',
        'type',
        'status',
        'send_to_customer',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'send_to_customer' => 'boolean',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function orderPayment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class);
    }

    public function isPayment(): bool
    {
        return $this->type === self::TYPE_PAYMENT;
    }

    public function isRefund(): bool
    {
        return $this->type === self::TYPE_REFUND;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }
}
