<?php
    $cartCount = session()->has('cart.items')
        ? collect(session('cart.items', []))->sum()
        : (session('cart_count', 0));
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo e(route('products.index')); ?>">
            <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['style' => 'width: 32px; height: 32px;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width: 32px; height: 32px;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php if(request()->routeIs('products.*')): ?> active fw-semibold <?php endif; ?>"
                       href="<?php echo e(route('products.index')); ?>">
                        Каталог
                    </a>
                </li>

                <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if(request()->routeIs('orders.*')): ?> active fw-semibold <?php endif; ?>"
                           href="<?php echo e(route('orders.index')); ?>">
                            Мои заказы
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->hasAnyRole([\App\Models\Role::ROLE_ADMIN, \App\Models\Role::ROLE_MANAGER])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if(request()->routeIs('admin.*')): ?> active fw-semibold <?php endif; ?>"
                               href="<?php echo e(route('admin.dashboard')); ?>">
                                Админка
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="<?php echo e(route('cart.index')); ?>"
                   class="btn btn-outline-secondary position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" class="me-1">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Корзина

                    <span data-cart-counter
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="display: <?php echo e($cartCount > 0 ? 'inline-flex' : 'none'); ?>;">
                        <?php echo e($cartCount); ?>

                    </span>
                </a>

                <?php if(auth()->guard()->check()): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo e(Auth::user()->name); ?>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>">
                                    Мои заказы
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                    Профиль
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item">
                                        Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">
                            Войти
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="btn btn-primary">
                            Регистрация
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH /var/www/html/resources/views/layouts/navigation.blade.php ENDPATH**/ ?>