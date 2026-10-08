@props(['product'])

@if($product->stock > 0)
    <div class="catalog-cart-control"
         data-product-id="{{ $product->id }}"
         data-store-url="{{ route('cart.items.store', $product) }}"
         data-increase-url="{{ route('cart.items.increase', $product) }}"
         data-decrease-url="{{ route('cart.items.decrease', $product) }}">

        <button type="button" class="btn btn-primary add-to-cart-btn">
            Добавить в корзину
        </button>

        <div class="quantity-box d-none">
            <button type="button" class="qty-btn qty-decrease">−</button>
            <span class="qty-value">1</span>
            <button type="button" class="qty-btn qty-increase">+</button>
        </div>
    </div>
@else
    <button type="button" class="btn btn-secondary" disabled>
        Нет в наличии
    </button>
@endif
