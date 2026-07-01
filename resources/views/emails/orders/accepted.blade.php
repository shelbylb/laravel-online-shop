<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ принят</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
<h2>Заказ принят</h2>

<p>Здравствуйте!</p>
<p>Товары уже готовятся к отправке. Отслеживать статус доставки можно в личном кабинете.</p>

@include('emails.orders.partials.items-table', ['order' => $order])
</body>
</html>
