@php
    $cartCount = session()->has('cart.items')
        ? collect(session('cart.items', []))->sum()
        : (session('cart_count', 0));
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <x-application-logo style="width: 32px; height: 32px;" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('dashboard')) active fw-semibold @endif"
                       href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('products.*')) active fw-semibold @endif"
                       href="{{ route('products.index') }}">
                        Каталог
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('orders.*')) active fw-semibold @endif"
                           href="{{ route('orders.index') }}">
                            Мои заказы
                        </a>
                    </li>
                @endauth

                @auth
                    @if(auth()->user()->hasAnyRole([\App\Models\Role::ROLE_ADMIN, \App\Models\Role::ROLE_MANAGER]))
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.*')) active fw-semibold @endif"
                               href="{{ route('admin.dashboard') }}">
                                Админка
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('cart.index') }}"
                   class="btn btn-outline-secondary position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" class="me-1">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Корзина

                    <span data-cart-counter
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="display: {{ $cartCount > 0 ? 'inline-flex' : 'none' }};">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    Мои заказы
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    Профиль
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            Регистрация
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
