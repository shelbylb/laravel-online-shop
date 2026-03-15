@if(empty($items))
    <div class="alert alert-info text-center py-5">
        <div class="mb-3">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1">
                <circle cx="9" cy="21" r="1.5" fill="#6c757d" stroke="none"/>
                <circle cx="20" cy="21" r="1.5" fill="#6c757d" stroke="none"/>
                <path d="M1 1h4l2.5 13h13l2-8H6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3 class="h5 text-muted mb-3">Корзина пуста</h3>
        <p class="text-muted mb-4">Добавьте товары в корзину, чтобы оформить заказ</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            Перейти в каталог
        </a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
            <tr>
                <th class="ps-4">Товар</th>
                <th class="text-center">Цена</th>
                <th class="text-center">Количество</th>
                <th class="text-center">Сумма</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                @php($product = $item['product'])
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="rounded me-3"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px;">
                                    <span class="small text-muted">Нет фото</span>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-decoration-none text-dark fw-medium">
                                    {{ $product->name }}
                                </a>
                                @if($product->stock === 0)
                                    <span class="badge bg-danger ms-2">Нет в наличии</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center fw-medium">
                        {{ number_format($product->price, 0, ',', ' ') }} ₽
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <!-- Уменьшить количество -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="update"
                                  data-product-name="{{ $product->name }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button type="submit"
                                        class="btn btn-outline-secondary btn-sm"
                                        style="width: 32px; height: 32px;"
                                    @disabled($item['quantity'] <= 1)>
                                    −
                                </button>
                            </form>

                            <!-- Поле для ввода количества -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="set"
                                  data-product-name="{{ $product->name }}">
                                @csrf
                                @method('PATCH')
                                <input type="number"
                                       name="quantity"
                                       value="{{ $item['quantity'] }}"
                                       min="1"
                                       max="{{ $product->stock }}"
                                       step="1"
                                       class="form-control form-control-sm text-center"
                                       style="width: 70px;">
                            </form>

                            <!-- Увеличить количество -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="update"
                                  data-product-name="{{ $product->name }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button type="submit"
                                        class="btn btn-outline-secondary btn-sm"
                                        style="width: 32px; height: 32px;"
                                    @disabled($item['quantity'] >= $product->stock || $product->stock === 0)>
                                    +
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="text-center fw-medium">
                        {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                    </td>
                    <td class="text-center">
                        <form method="POST"
                              action="{{ route('cart.items.destroy', $product) }}"
                              data-ajax-cart="1"
                              data-product-name="{{ $product->name }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-link text-danger p-0"
                                    title="Удалить из корзины">
                                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Итоговая сумма -->
    <div class="row mt-4">
        <div class="col-md-6 offset-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Товаров:</span>
                        <span class="fw-medium">{{ $totalQuantity }} шт.</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Общая сумма:</span>
                        <span class="fw-bold fs-5">{{ number_format($totalPrice, 0, ',', ' ') }} ₽</span>
                    </div>
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary flex-fill">
                            Продолжить покупки
                        </a>
                        @auth
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary flex-fill">
                                Оформить заказ
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary flex-fill">
                                Войти для оформления
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
