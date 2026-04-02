<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'country' => ['required_without:address_id', 'string', 'max:100'],
            'city' => ['required_without:address_id', 'string', 'max:100'],
            'street' => ['required_without:address_id', 'string', 'max:200'],
            'house' => ['required_without:address_id', 'string', 'max:20'],
            'apartment' => ['nullable', 'string', 'max:20'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'regex:/^[\d\s\+\(\)\-]+$/', 'max:20'],
            'payment_method' => ['required', Rule::in(['card', 'cash'])],
            'set_as_default' => ['boolean'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'country.required_without' => 'Укажите страну',
            'city.required_without' => 'Укажите город',
            'street.required_without' => 'Укажите улицу',
            'house.required_without' => 'Укажите номер дома',
            'recipient_name.required' => 'Укажите имя получателя',
            'recipient_phone.required' => 'Укажите телефон получателя',
            'recipient_phone.regex' => 'Введите корректный номер телефона',
            'payment_method.required' => 'Выберите способ оплаты',
        ];
    }
}
