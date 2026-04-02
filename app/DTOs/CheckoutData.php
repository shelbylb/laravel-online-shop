<?php

namespace App\DTOs;

class CheckoutData
{
    public function __construct(
        public readonly ?int $address_id,
        public readonly string $country,
        public readonly string $city,
        public readonly string $street,
        public readonly string $house,
        public readonly ?string $apartment,
        public readonly string $recipient_name,
        public readonly string $recipient_phone,
        public readonly string $payment_method,
        public readonly bool $set_as_default,
        public readonly ?string $comment = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            address_id: $data['address_id'] ?? null,
            country: $data['country'] ?? '',
            city: $data['city'] ?? '',
            street: $data['street'] ?? '',
            house: $data['house'] ?? '',
            apartment: $data['apartment'] ?? null,
            recipient_name: $data['recipient_name'] ?? '',
            recipient_phone: $data['recipient_phone'] ?? '',
            payment_method: $data['payment_method'] ?? 'card',
            set_as_default: $data['set_as_default'] ?? false,
            comment: $data['comment'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'address_id' => $this->address_id,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'house' => $this->house,
            'apartment' => $this->apartment,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'payment_method' => $this->payment_method,
            'set_as_default' => $this->set_as_default,
            'comment' => $this->comment,
        ];
    }
}
