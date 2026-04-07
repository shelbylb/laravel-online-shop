<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>403 | Доступ запрещён</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #111827;
        }

        .box {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 500px;
        }

        h1 {
            font-size: 48px;
            margin: 0 0 12px;
        }

        p {
            margin: 0 0 24px;
            color: #6b7280;
        }

        a {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="box">
    <h1>403</h1>
    <p>У вас нет доступа к этой странице.</p>
    <a href="{{ url()->previous() }}">Назад</a>
</div>
</body>
</html>
