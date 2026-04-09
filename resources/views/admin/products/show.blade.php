@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Просмотр товара</h1>

            <div class="d-flex gap-2">
                @can('update', $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        Редактировать
                    </a>
                @endcan

                <a href="{{ route('admin.products.index', $product) }}" class="btn btn-secondary">
                    Назад
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($product->image)
                    <div class="mb-4">
                        <img
                            src="{{ asset('storage/' . $product->image) }}"
                            alt="{{ $product->name }}"
                            style="max-width: 250px; height: auto;"
                        >
                    </div>
                @endif

                <p><strong>ID:</strong> {{ $product->id }}</p>
                <p><strong>Название:</strong> {{ $product->name }}</p>
                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                <p><strong>Цена:</strong> {{ number_format((float) $product->price, 2, '.', ' ') }}</p>
                <p><strong>Остаток:</strong> {{ $product->stock }}</p>
                <p><strong>Статус:</strong> {{ $product->status }}</p>
                <p><strong>Категория:</strong> {{ $product->category?->name ?? 'Без категории' }}</p>
                <p><strong>Описание:</strong></p>
                <div class="border rounded p-3 mb-3">
                    {{ $product->description ?: '—' }}
                </div>
                <p><strong>Создан:</strong> {{ $product->created_at?->format('d.m.Y H:i') }}</p>
                <p><strong>Обновлён:</strong> {{ $product->updated_at?->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
@endsection
