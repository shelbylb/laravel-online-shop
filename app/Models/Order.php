<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_PAID = 'paid';
    public const string STATUS_SHIPPED = 'shipped';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_CANCELED = 'canceled';

    public const array STATUS_LABELS = [
        self::STATUS_PENDING => 'Ожидает оплаты',
        self::STATUS_PAID => 'Оплачен',
        self::STATUS_SHIPPED => 'Отправлен',
        self::STATUS_COMPLETED => 'Завершен',
        self::STATUS_CANCELED => 'Отменен',
    ];

    public const string PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_CARD = 'card';

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
        'comment'
    ];

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

}
