<?php ($perPage = $dto->per_page ?? 10); ?>
<?php ($q = $dto->q ?? ''); ?>
<?php ($minPrice = $dto->min_price ?? ''); ?>
<?php ($maxPrice = $dto->max_price ?? ''); ?>
<?php ($inStock = $dto->in_stock ?? false); ?>
<?php ($sort = $dto->sort ?? 'new'); ?>
<?php ($maxProductPricePlaceholder = $maxPrice ? ('до ' . $maxPrice) : 'максимальная цена товара'); ?>



<?php $__env->startSection('title', 'Каталог товаров'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .product-catalog-image {
            height: 220px;
            width: 100%;
            object-fit: contain;
            display: block;
        }

        .card.h-100 {
            overflow: hidden;
            transition: 0.2s ease;
        }

        .card.h-100:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            min-height: 48px;
        }

        .catalog-cart-control {
            width: 100%;
        }

        .add-to-cart-btn {
            min-height: 44px;
        }

        .quantity-box {
            width: 100%;
            min-height: 44px;
            background: #f3f4f6;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
        }

        .qty-btn {
            width: 48px;
            height: 44px;
            border: none;
            background: #dbe7ff;
            color: #0d6efd;
            font-size: 30px;
            line-height: 1;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .qty-btn:hover {
            background: #cddcff;
        }

        .qty-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .qty-value {
            flex: 1;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            color: #222;
            user-select: none;
        }
    </style>

    <div class="container py-4">
        <h1 class="h3 mb-3">Каталог товаров</h1>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

        <form method="GET" action="<?php echo e(route('products.index')); ?>" class="card card-body mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="q" class="form-label mb-1">Поиск</label>
                    <input type="text"
                           name="q"
                           id="q"
                           value="<?php echo e($q); ?>"
                           class="form-control"
                           placeholder="Название или артикул">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="min_price" class="form-label mb-1">Цена от</label>
                    <input type="number"
                           name="min_price"
                           id="min_price"
                           value="<?php echo e($minPrice); ?>"
                           placeholder="от 100"
                           min="0"
                           step="100"
                           class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="max_price" class="form-label mb-1">до</label>
                    <input type="number"
                           name="max_price"
                           id="max_price"
                           value="<?php echo e($maxPrice); ?>"
                           placeholder="<?php echo e($maxProductPricePlaceholder); ?>"
                           min="0"
                           step="100"
                           class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="sort" class="form-label mb-1">Сортировка</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="new" <?php if($sort === 'new'): echo 'selected'; endif; ?>>Сначала новые</option>
                        <option value="price_asc" <?php if($sort === 'price_asc'): echo 'selected'; endif; ?>>Цена: по возрастанию</option>
                        <option value="price_desc" <?php if($sort === 'price_desc'): echo 'selected'; endif; ?>>Цена: по убыванию</option>
                        <option value="name_asc" <?php if($sort === 'name_asc'): echo 'selected'; endif; ?>>Название: А → Я</option>
                        <option value="name_desc" <?php if($sort === 'name_desc'): echo 'selected'; endif; ?>>Название: Я → А</option>
                        <option value="stock_desc" <?php if($sort === 'stock_desc'): echo 'selected'; endif; ?>>Наличие: больше → меньше</option>
                        <option value="stock_asc" <?php if($sort === 'stock_asc'): echo 'selected'; endif; ?>>Наличие: меньше → больше</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="per_page" class="form-label mb-1">На странице</label>
                    <select name="per_page" id="per_page" class="form-select">
                        <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option); ?>" <?php if($perPage == $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    <div class="form-check">
                        <input type="checkbox"
                               name="in_stock"
                               id="in_stock"
                               value="1"
                               class="form-check-input"
                            <?php if($inStock): echo 'checked'; endif; ?>>
                        <label for="in_stock" class="form-check-label">Только в наличии</label>
                    </div>
                </div>

                <div class="col-12 col-lg-8 mt-2 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Применить</button>
                    <a href="<?php echo e(route('products.index', ['per_page' => $perPage])); ?>"
                       class="btn btn-outline-secondary">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if($product->image): ?>
                            <img src="<?php echo e(asset('storage/' . $product->image)); ?>"
                                 class="card-img-top product-catalog-image"
                                 alt="<?php echo e($product->name); ?>">
                        <?php else: ?>
                            <div class="card-img-top product-catalog-image d-flex align-items-center justify-content-center bg-light">
                                <span class="text-muted">Нет изображения</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo e($product->name); ?></h5>

                            <p class="fw-semibold mb-3">
                                <?php echo e(number_format($product->price, 0, ',', ' ')); ?> ₽
                            </p>

                            <?php if($product->stock > 0): ?>
                                <span class="badge bg-success mb-2">
                                    В наличии: <?php echo e($product->stock); ?> шт.
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger mb-2">Нет в наличии</span>
                            <?php endif; ?>

                            <?php ($detailsUrl = Route::has('products.show') ? route('products.show', $product) : '#'); ?>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="<?php echo e($detailsUrl); ?>" class="btn btn-outline-primary flex-fill">
                                    Подробнее
                                </a>

                                <div class="flex-fill">
                                    <?php if($product->stock > 0): ?>
                                        <div class="catalog-cart-control"
                                             data-product-id="<?php echo e($product->id); ?>"
                                             data-store-url="<?php echo e(route('cart.items.store', $product)); ?>"
                                             data-increase-url="<?php echo e(route('cart.items.increase', $product)); ?>"
                                             data-decrease-url="<?php echo e(route('cart.items.decrease', $product)); ?>">

                                            <button type="button" class="btn btn-primary w-100 add-to-cart-btn">
                                                В корзину
                                            </button>

                                            <div class="quantity-box d-none">
                                                <button type="button" class="qty-btn qty-decrease">−</button>
                                                <span class="qty-value">1</span>
                                                <button type="button" class="qty-btn qty-increase">+</button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary w-100" disabled>
                                            Нет в наличии
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <p class="text-center text-muted py-5">По заданным условиям товары не найдены.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($products->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            function updateCartBadge(cartCount) {
                if (typeof setCartCount === 'function') {
                    setCartCount(cartCount);
                }
            }

            function showNotification(message, type = 'success') {
                const container = document.getElementById('notification-container');
                if (!container) return;

                const notification = document.createElement('div');
                notification.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
                notification.setAttribute('role', 'alert');
                notification.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
                    </div>
                `;

                container.appendChild(notification);

                const closeButton = notification.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function () {
                        notification.remove();
                    });
                }

                setTimeout(() => {
                    notification.remove();
                }, 2500);
            }

            function setControlLoading(control, isLoading) {
                const addBtn = control.querySelector('.add-to-cart-btn');
                const decreaseBtn = control.querySelector('.qty-decrease');
                const increaseBtn = control.querySelector('.qty-increase');

                if (addBtn) addBtn.disabled = isLoading;
                if (decreaseBtn) decreaseBtn.disabled = isLoading;
                if (increaseBtn) increaseBtn.disabled = isLoading;
            }

            function updateControlState(control, quantity) {
                const addBtn = control.querySelector('.add-to-cart-btn');
                const qtyBox = control.querySelector('.quantity-box');
                const qtyValue = control.querySelector('.qty-value');

                if (!addBtn || !qtyBox || !qtyValue) return;

                if (quantity > 0) {
                    qtyValue.textContent = quantity;
                    addBtn.classList.add('d-none');
                    qtyBox.classList.remove('d-none');
                } else {
                    addBtn.classList.remove('d-none');
                    qtyBox.classList.add('d-none');
                }
            }

            async function postJson(url, method = 'POST', body = null) {
                const options = {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                };

                if (body) {
                    options.body = body;
                }

                const response = await fetch(url, options);
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            }

            async function loadCartQuantities() {
                try {
                    const response = await fetch('<?php echo e(route('cart.items.quantities')); ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!data.success) return;

                    document.querySelectorAll('.catalog-cart-control').forEach(control => {
                        const productId = control.dataset.productId;
                        const quantity = data.quantities[productId] ?? 0;
                        updateControlState(control, quantity);
                    });

                    updateCartBadge(data.cartCount);
                } catch (error) {
                    console.error('Ошибка загрузки корзины:', error);
                }
            }

            document.querySelectorAll('.catalog-cart-control').forEach(control => {
                const addBtn = control.querySelector('.add-to-cart-btn');
                const increaseBtn = control.querySelector('.qty-increase');
                const decreaseBtn = control.querySelector('.qty-decrease');

                if (addBtn) {
                    addBtn.addEventListener('click', async function () {
                        try {
                            setControlLoading(control, true);

                            const formData = new FormData();
                            formData.append('quantity', '1');

                            const data = await postJson(control.dataset.storeUrl, 'POST', formData);

                            updateControlState(control, data.quantity ?? 1);
                            updateCartBadge(data.cartCount);

                            if (data.message) {
                                showNotification(data.message, data.success === false ? 'danger' : 'success');
                            }
                        } catch (error) {
                            showNotification(error.message ?? 'Ошибка при добавлении товара', 'danger');
                        } finally {
                            setControlLoading(control, false);
                        }
                    });
                }

                if (increaseBtn) {
                    increaseBtn.addEventListener('click', async function () {
                        try {
                            setControlLoading(control, true);

                            const data = await postJson(control.dataset.increaseUrl, 'POST');

                            if (data.success === false) {
                                showNotification(data.message ?? 'Нельзя увеличить количество', 'danger');
                                return;
                            }

                            updateControlState(control, data.quantity ?? 0);
                            updateCartBadge(data.cartCount);

                            if (data.message && data.max_reached) {
                                showNotification(data.message, 'warning');
                            }
                        } catch (error) {
                            showNotification(error.message ?? 'Ошибка при увеличении количества', 'danger');
                        } finally {
                            setControlLoading(control, false);
                        }
                    });
                }

                if (decreaseBtn) {
                    decreaseBtn.addEventListener('click', async function () {
                        try {
                            setControlLoading(control, true);

                            const data = await postJson(control.dataset.decreaseUrl, 'POST');

                            updateControlState(control, data.quantity ?? 0);
                            updateCartBadge(data.cartCount);

                            if (data.message && (data.quantity ?? 0) > 0) {
                                showNotification(data.message, 'success');
                            }
                        } catch (error) {
                            showNotification(error.message ?? 'Ошибка при уменьшении количества', 'danger');
                        } finally {
                            setControlLoading(control, false);
                        }
                    });
                }
            });

            loadCartQuantities();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/products/index.blade.php ENDPATH**/ ?>