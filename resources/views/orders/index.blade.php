@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Мои заказы</h2>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="alert alert-info text-center py-5">
                <div class="mb-3">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1">
                        <path d="M20 7h-4.18A3 3 0 0 0 16 5.18V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v1.18A3 3 0 0 0 8.18 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zm-6-3h-4V4h4v1.18zM12 18a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </div>
                <h3 class="h5 text-muted mb-3">У вас пока нет заказов</h3>
                <p class="text-muted mb-4">Перейдите в каталог, чтобы сделать первый заказ</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    Перейти в каталог
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                    <tr>
                        <th>№ заказа</th>
                        <th>Дата</th>
                        <th class="text-center">Товаров</th>
                        <th class="text-center">Сумма</th>
                        <th class="text-center">Статус</th>
                        <th class="text-center">Способ оплаты</th>
                        <th class="text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-medium">#{{ $order->order_number }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td class="text-center">{{ $order->total_quantity }} шт.</td>
                            <td class="text-center fw-medium">{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'warning',
                                        'paid' => 'info',
                                        'shipped' => 'primary',
                                        'completed' => 'success',
                                        'canceled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="small">{{ $order->payment_method_label }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.show', $order->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Подробнее
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
