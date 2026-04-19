<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_PAID = 'paid';
    public const string STATUS_SHIPPED = 'shipped';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_CANCELED = 'canceled';

    public const array STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_SHIPPED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELED,
    ];

    public const array STATUS_LABELS = [
        self::STATUS_PENDING => 'Ожидает оплаты',
        self::STATUS_PAID => 'Оплачен',
        self::STATUS_SHIPPED => 'Отправлен',
        self::STATUS_COMPLETED => 'Завершен',
        self::STATUS_CANCELED => 'Отменен',
    ];

    public const string PAYMENT_METHOD_CASH = 'cash';
    public const string PAYMENT_METHOD_CARD = 'card';

    public const array PAYMENT_METHOD_LABELS = [
        self::PAYMENT_METHOD_CASH => 'Наличными при получении',
        self::PAYMENT_METHOD_CARD => 'Картой при получении',
    ];

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_amount',
        'total_quantity',
        'status',
        'payment_method',
        'payment_status',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'comment',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHOD_LABELS[$this->payment_method] ?? $this->payment_method;
    }

    public function canEditItems(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCanceled(): bool
    {
        return !in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELED,
        ], true);
    }
}
