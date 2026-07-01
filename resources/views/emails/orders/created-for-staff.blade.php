<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформлен заказ {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
<h2>Оформлен заказ {{ $order->order_number }}</h2>

<p>
    Пользователь: {{ $order->user?->name ?? 'Не указан' }}
    @if ($order->user?->email)
        ({{ $order->user->email }})
    @endif
</p>

@include('emails.orders.partials.items-table', ['order' => $order])
</body>
</html>
