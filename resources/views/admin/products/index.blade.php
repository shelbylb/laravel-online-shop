@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Товары</h1>

            @can('create', \App\Models\Product::class)
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    Добавить товар
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="Поиск по названию или SKU"
                    value="{{ request('q') }}"
                >
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-primary">Найти</button>
            </div>
            <div class="col-md-auto">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Сбросить</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Изображение</th>
                    <th>Название</th>
                    <th>SKU</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th>Статус</th>
                    <th>Категория</th>
                    <th>Создан</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img
                                    src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                    style="width: 70px; height: 70px; object-fit: contain;"
                                >
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ number_format((float) $product->price, 2, '.', ' ') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if($product->status === \App\Models\Product::STATUS_ACTIVE)
                                <span class="badge bg-success">active</span>
                            @else
                                <span class="badge bg-secondary">inactive</span>
                            @endif
                        </td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>{{ $product->created_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                @can('view', $product)
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                        Смотреть
                                    </a>
                                @endcan

                                @can('update', $product)
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        Редактировать
                                    </a>
                                @endcan

                                @can('delete', $product)
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Удалить товар?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Удалить
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Товары не найдены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
@endsection
