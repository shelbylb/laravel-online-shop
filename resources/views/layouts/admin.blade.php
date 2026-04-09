<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Админ-панель' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

{{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #222;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #111827;
            color: #fff;
            padding: 24px 16px;
        }

        .sidebar-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 24px;
        }

        .sidebar-user {
            margin-bottom: 24px;
            padding: 12px;
            background: rgba(255,255,255,0.06);
            border-radius: 10px;
            font-size: 14px;
        }

        .sidebar nav a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #2563eb;
        }

        .content {
            padding: 32px;
        }

        .page-title {
            margin-top: 0;
            margin-bottom: 24px;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            margin-top: 10px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary { background: #2563eb; color: #fff; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-danger  { background: #dc2626; color: #fff; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-secondary { background: #6b7280; color: #fff; }

        .btn-sm {
            padding: 8px 10px;
            font-size: 13px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 12px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .text-danger {
            color: #dc2626;
            font-size: 14px;
            margin-top: 6px;
        }

        .page-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 1fr 220px auto auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .cards-grid,
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-title">Админ-панель</div>

        <div class="sidebar-user">
            <div><strong>{{ auth()->user()->name }}</strong></div>
            <div>{{ auth()->user()->email }}</div>
        </div>

        <nav>
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                Пользователи
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                Товары
            </a>

            <a href="{{ url('/') }}">
                На сайт
            </a>
        </nav>
    </aside>

    <main class="content">
        @yield('content')
    </main>
</div>
</body>
</html>
