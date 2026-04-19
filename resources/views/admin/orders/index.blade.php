@extends('layouts.admin')

@section('content')
    <h1 class="page-title">Заказы</h1>

    <div class="card" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="filters-grid">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="q" class="form-label">Поиск</label>
                <input
                    type="text"
                    name="q"
                    id="q"
                    class="form-control"
                    placeholder="Номер заказа, имя, телефон, email"
                    value="{{ request('q') }}"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="status" class="form-label">Статус</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Все статусы</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Найти</button>
            </div>

            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Сбросить</a>
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Номер</th>
                <th>Клиент</th>
                <th>Получатель</th>
                <th>Телефон</th>
                <th>Товаров</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Оплата</th>
                <th>Создан</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>
                        <div>{{ $order->user?->name ?? '—' }}</div>
                        <div style="font-size: 12px; color: #6b7280;">{{ $order->user?->email ?? '—' }}</div>
                    </td>
                    <td>{{ $order->recipient_name ?: '—' }}</td>
                    <td>{{ $order->recipient_phone ?: '—' }}</td>
                    <td>{{ $order->total_quantity }}</td>
                    <td>{{ number_format((float) $order->total_amount, 2, '.', ' ') }}</td>
                    <td>
                        <span class="badge">{{ $order->status_label }}</span>
                    </td>
                    <td>{{ $order->payment_method_label }}</td>
                    <td>{{ $order->created_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary btn-sm">
                            Открыть
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Заказы не найдены.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $orders->links() }}
    </div>
@endsection
