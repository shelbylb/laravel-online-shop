<?php ($perPage = $dto->per_page ?? 10); ?>
<?php ($q = $dto->q ?? ''); ?>
<?php ($minPrice = $dto->min_price ?? ''); ?>
<?php ($maxPrice = $dto->max_price ?? ''); ?>
<?php ($inStock = $dto->in_stock ?? false); ?>
<?php ($sort = $dto->sort ?? 'new'); ?>
<?php ($maxProductPricePlaceholder = $maxPrice ? ('до ' . $maxPrice) : 'максимальная цена товара'); ?>



<?php $__env->startSection('title', 'Каталог товаров'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">
        <h1 class="h3 mb-3">Каталог товаров</h1>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

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
                                 class="card-img-top"
                                 alt="<?php echo e($product->name); ?>">
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                 style="height: 180px;">
                                <span class="text-muted">Нет изображения</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo e($product->name); ?></h5>
                            <p class="fw-semibold mb-3"><?php echo e(number_format($product->price, 0, ',', ' ')); ?> ₽</p>

                            <?php ($detailsUrl = Route::has('products.show') ? route('products.show', $product) : '#'); ?>
                            <a href="<?php echo e($detailsUrl); ?>" class="btn btn-outline-primary mt-auto">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p>По заданным условиям товары не найдены.</p>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($products->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/products/index.blade.php ENDPATH**/ ?>