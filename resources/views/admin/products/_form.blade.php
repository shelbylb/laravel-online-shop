<div class="mb-3">
    <label for="name" class="form-label">Название</label>
    <input
        type="text"
        name="name"
        id="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $product->name ?? '') }}"
        required
    >
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Описание</label>
    <textarea
        name="description"
        id="description"
        rows="4"
        class="form-control @error('description') is-invalid @enderror"
    >{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="price" class="form-label">Цена</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            id="price"
            class="form-control @error('price') is-invalid @enderror"
            value="{{ old('price', $product->price ?? '') }}"
            required
        >
        @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="stock" class="form-label">Остаток</label>
        <input
            type="number"
            min="0"
            name="stock"
            id="stock"
            class="form-control @error('stock') is-invalid @enderror"
            value="{{ old('stock', $product->stock ?? 0) }}"
            required
        >
        @error('stock')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input
            type="text"
            name="sku"
            id="sku"
            class="form-control @error('sku') is-invalid @enderror"
            value="{{ old('sku', $product->sku ?? '') }}"
            required
        >
        @error('sku')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Статус</label>
        <select
            name="status"
            id="status"
            class="form-select @error('status') is-invalid @enderror"
            required
        >
            <option value="{{ \App\Models\Product::STATUS_ACTIVE }}"
                @selected(old('status', $product->status ?? \App\Models\Product::STATUS_ACTIVE) === \App\Models\Product::STATUS_ACTIVE)>
                active
            </option>
            <option value="{{ \App\Models\Product::STATUS_INACTIVE }}"
                @selected(old('status', $product->status ?? \App\Models\Product::STATUS_ACTIVE) === \App\Models\Product::STATUS_INACTIVE)>
                inactive
            </option>
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Категория</label>
    <select
        name="category_id"
        id="category_id"
        class="form-select @error('category_id') is-invalid @enderror"
    >
        <option value="">Без категории</option>
        @foreach($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">Изображение</label>
    <input
        type="file"
        name="image"
        id="image"
        class="form-control @error('image') is-invalid @enderror"
        accept="image/*"
    >
    @error('image')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(!empty($product->image))
    <div class="mb-3">
        <p class="mb-2">Текущее изображение:</p>
        <img
            src="{{ asset('storage/' . $product->image) }}"
            alt="{{ $product->name }}"
            style="max-width: 200px; height: auto;"
        >
    </div>
@endif

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Отмена</a>
</div>
