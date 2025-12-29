<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Регистрация доступна всем (даже неавторизованным)
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Добавлена точка с запятой
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at') // Игнорируем удаленных
            ],
            'password'   => ['required', 'string', 'min:8', 'confirmed'], // confirmed проверяет пароль на совпадение в поле проверки пароля
        ];
    }

    /**
     * Возвращает сообщение об ошибке на отдельное взятое правило
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Введите имя',
            'first_name.string'   => 'Имя должно быть строкой',
            'first_name.max'      => 'Имя не должно превышать 255 символов',

            'email.required'      => 'Введите email',
            'email.email'         => 'Неверный формат email',
            'email.max'           => 'Email не должен превышать 255 символов',
            'email.unique'        => 'Пользователь с таким email уже зарегистрирован.',

            'password.required'   => 'Введите пароль',
            'password.string'     => 'Пароль должен быть строкой',
            'password.min'        => 'Пароль должен быть не менее 8 символов',
            'password.confirmed'  => 'Пароли не совпадают',
        ];
    }
}
