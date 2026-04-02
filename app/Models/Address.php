<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Поля, разрешенные для массового присвоения
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'country',
        'city',
        'street',
        'house',
        'apartment',
        'is_default',
    ];

    /**
     * Поля, которые должны быть преобразованы к определенным типам
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Атрибуты, которые нужно скрыть из JSON
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * Получить пользователя, которому принадлежит адрес
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить полный адрес одной строкой
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->country,
            $this->city,
            $this->street,
            $this->house ? 'д. ' . $this->house : null,
            $this->apartment ? 'кв. ' . $this->apartment : null
        ];

        // Удаляем null значения
        $parts = array_filter($parts);

        return implode(', ', $parts);
    }

    /**
     * Проверить, является ли адрес основным для пользователя
     */
    public function isDefaultForUser(): bool
    {
        return $this->is_default;
    }

    /**
     * Сделать этот адрес основным для пользователя
     */
    public function makeDefault(): bool
    {
        // Сбрасываем флаг is_default у всех адресов пользователя
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Устанавливаем этот адрес как основной
        $this->is_default = true;
        return $this->save();
    }

    /**
     * Локальный скоуп для получения только основных адресов
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Локальный скоуп для получения адресов пользователя
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Локальный скоуп для поиска по городу
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }

    /**
     * Локальный скоуп для поиска по стране
     */
    public function scopeInCountry($query, $country)
    {
        return $query->where('country', 'LIKE', "%{$country}%");
    }
}
