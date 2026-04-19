@extends('layouts.admin')

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom: 0;">Заказ {{ $order->order_number }}</h1>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Назад</a>

            @can('editItems', $order)
                @if($order->canEditItems())
                    <a href="{{ route('admin.orders.edit-items', $order) }}" class="btn btn-warning">
                        Редактировать состав
                    </a>
                @endif
            @endcan
        </div>
    </div>

    <div class="cards-grid" style="margin-bottom: 20px;">
        <div class="card">
            <h3 style="margin-top: 0;">Информация о заказе</h3>
            <p><strong>ID:</strong> {{ $order->id }}</p>
            <p><strong>Номер:</strong> {{ $order->order_number }}</p>
            <p><strong>Статус:</strong> {{ $order->status_label }}</p>
            <p><strong>Способ оплаты:</strong> {{ $order->payment_method_label }}</p>
            <p><strong>Статус оплаты:</strong> {{ $order->payment_status }}</p>
            <p><strong>Создан:</strong> {{ $order->created_at?->format('d.m.Y H:i') }}</p>
            <p><strong>Обновлён:</strong> {{ $order->updated_at?->format('d.m.Y H:i') }}</p>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Клиент</h3>
            <p><strong>Имя:</strong> {{ $order->user?->name ?? '—' }}</p>
            <p><strong>Email:</strong> {{ $order->user?->email ?? '—' }}</p>
            <p><strong>Телефон:</strong> {{ $order->user?->phone ?? '—' }}</p>

            @if($order->user)
                <p style="margin-top: 16px;">
                    <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-secondary btn-sm">
                        Открыть профиль клиента
                    </a>
                </p>
            @endif
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Получатель и доставка</h3>
            <p><strong>Получатель:</strong> {{ $order->recipient_name ?: '—' }}</p>
            <p><strong>Телефон:</strong> {{ $order->recipient_phone ?: '—' }}</p>
            <p><strong>Адрес:</strong> {{ $order->shipping_address ?: '—' }}</p>
            <p><strong>Комментарий:</strong> {{ $order->comment ?: '—' }}</p>
        </div>
    </div>

    <div class="cards-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
        @can('updateStatus', $order)
            <div class="card">
                <h3 style="margin-top: 0;">Сменить статус</h3>

                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="status" class="form-label">Новый статус</label>
                        <select name="status" id="status" class="form-control" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Сохранить статус</button>
                </form>
            </div>
        @endcan

        @can('cancel', $order)
            <div class="card">
                <h3 style="margin-top: 0;">Отмена заказа</h3>

                @if($order->canBeCanceled())
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Отменить заказ?');">
                        @csrf
                        <button type="submit" class="btn btn-danger">Отменить заказ</button>
                    </form>
                @else
                    <p>Этот заказ уже нельзя отменить.</p>
                @endif
            </div>
        @endcan
    </div>

    <div class="card">
        <h3 style="margin-top: 0;">Состав заказа</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID позиции</th>
                    <th>Товар</th>
                    <th>SKU</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Подытог</th>
                </tr>
                </thead>
                <tbody>
                @forelse($order->items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product?->sku ?? '—' }}</td>
                        <td>{{ number_format((float) $item->product_price, 2, '.', ' ') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format((float) $item->subtotal, 2, '.', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">В заказе нет товаров.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <p><strong>Всего товаров:</strong> {{ $order->total_quantity }}</p>
            <p><strong>Итоговая сумма:</strong> {{ number_format((float) $order->total_amount, 2, '.', ' ') }}</p>
        </div>
    </div>
@endsection
