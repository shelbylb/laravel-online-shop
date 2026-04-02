@extends('layouts.app')

@section('title', 'Заказ #' . $order->order_number)

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Мои заказы</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Заказ #{{ $order->order_number }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Информация о заказе -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">Информация о заказе</h5>
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
                            <span class="badge bg-{{ $statusClass }} fs-6 px-3 py-2">
                            {{ $order->status_label }}
                        </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="text-muted small">Номер заказа:</span>
                                    <div class="fw-medium">#{{ $order->order_number }}</div>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Дата заказа:</span>
                                    <div>{{ $order->created_at }}</div>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Способ оплаты:</span>
                                    <div>{{ $order->payment_method_label }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="text-muted small">Получатель:</span>
                                    <div>{{ $order->recipient_name }}</div>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Телефон:</span>
                                    <div>{{ $order->recipient_phone }}</div>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Адрес доставки:</span>
                                    <div>{{ $order->shipping_address }}</div>
                                </div>
                            </div>
                        </div>

                        @if($order->comment)
                            <div class="mt-2">
                                <span class="text-muted small">Комментарий к заказу:</span>
                                <div class="bg-light p-2 rounded">{{ $order->comment }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Итоговая информация -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Итог заказа</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Товаров:</span>
                            <span class="fw-medium">{{ $order->total_quantity }} шт.</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Сумма заказа:</span>
                            <span class="fw-bold fs-4">{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</span>
                        </div>

                        <hr>

                        @if(in_array($order->status, ['pending', 'paid']))
                            <form method="POST" action="{{ route('orders.cancel', $order->id) }}"
                                  onsubmit="return confirm('Вы уверены, что хотите отменить этот заказ?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    Отменить заказ
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100 mt-2">
                            Продолжить покупки
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Товары в заказе -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Товары в заказе</h5>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light">
                                <tr>
                                    <th>Товар</th>
                                    <th class="text-center">Цена</th>
                                    <th class="text-center">Количество</th>
                                    <th class="text-center">Сумма</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <span class="fw-medium">{{ $item['product_name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item['product_price'], 0, ',', ' ') }} ₽
                                        </td>
                                        <td class="text-center">{{ $item['quantity'] }} шт.</td>
                                        <td class="text-center fw-medium">
                                            {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Итого:</td>
                                    <td class="text-center fw-bold fs-5">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} ₽
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
