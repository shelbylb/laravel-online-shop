<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPayment extends Model
{
    public const string PROVIDER_YOOKASSA = 'yookassa';

    public const string STATUS_PENDING = 'pending';
    public const string STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    public const string STATUS_SUCCEEDED = 'succeeded';
    public const string STATUS_CANCELED = 'canceled';

    public const array STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_WAITING_FOR_CAPTURE,
        self::STATUS_SUCCEEDED,
        self::STATUS_CANCELED,
    ];

    protected $fillable = [
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'external_payment_id',
        'idempotence_key',
        'confirmation_url',
        'request_payload',
        'response_payload',
        'paid_at',
        'canceled_at',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isWaitingForCapture(): bool
    {
        return $this->status === self::STATUS_WAITING_FOR_CAPTURE;
    }

    public function isSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_WAITING_FOR_CAPTURE], true);
    }
}
