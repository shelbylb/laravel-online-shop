@extends('layouts.admin')

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom: 0;">
            Редактирование состава заказа {{ $order->order_number }}
        </h1>

        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
            Назад к заказу
        </a>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <p><strong>Статус заказа:</strong> {{ $order->status_label }}</p>
        <p><strong>Получатель:</strong> {{ $order->recipient_name ?: '—' }}</p>
        <p><strong>Текущая сумма:</strong> {{ number_format((float) $order->total_amount, 2, '.', ' ') }}</p>
        <p><strong>Текущее количество товаров:</strong> {{ $order->total_quantity }}</p>
    </div>

    <div class="card">
        @if(!$order->canEditItems())
            <div class="alert" style="background: #fee2e2; color: #991b1b;">
                Состав заказа можно менять только у заказа со статусом «Ожидает оплаты».
            </div>
        @else
            <form action="{{ route('admin.orders.items.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.orders._items_form')
            </form>
        @endif
    </div>
@endsection
