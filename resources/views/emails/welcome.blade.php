<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
<h2>Здравствуйте, {{ $user->name }}!</h2>

<p>Спасибо за подтверждение email.</p>

<p>Добро пожаловать в наш магазин! Теперь вам доступно оформление заказов.</p>

<p>
    <a href="{{ route('products.index') }}"
       style="display:inline-block;padding:10px 16px;background:#111827;color:#fff;text-decoration:none;border-radius:6px;">
        Перейти в каталог
    </a>
</p>

<p>Спасибо, что вы с нами!</p>
</body>
</html>
