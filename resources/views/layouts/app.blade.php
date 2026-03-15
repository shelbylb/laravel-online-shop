<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main class="container">
        @yield('content')
    </main>
</div>

<!-- Cart Scripts -->
@php($cartCount = session()->has('cart.items') ? collect(session('cart.items', []))->sum() : 0)

<script>
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Функция для обновления счетчика корзины
    function setCartCount(count) {
        const counters = document.querySelectorAll('[data-cart-counter]');
        counters.forEach(counter => {
            counter.textContent = count;
            if (count > 0) {
                counter.style.display = 'inline-flex';
            } else {
                counter.style.display = 'none';
            }
        });

        const oldCounters = document.querySelectorAll('[data-cart-count]');
        oldCounters.forEach(counter => {
            counter.textContent = count;
            if (count > 0) {
                counter.classList.remove('d-none');
            } else {
                counter.classList.add('d-none');
            }
        });
    }

    // Функция для создания уведомления
    function showNotification(message, type = 'danger') {
        console.log('🔔 Showing notification:', message, type);

        let container = document.getElementById('notification-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1050';
            document.body.appendChild(container);
        }

        const notificationId = 'notification-' + Date.now();
        const notification = document.createElement('div');
        notification.id = notificationId;
        notification.className = `toast align-items-center text-white bg-${type} border-0 mb-2`;
        notification.setAttribute('role', 'alert');
        notification.setAttribute('aria-live', 'assertive');
        notification.setAttribute('aria-atomic', 'true');

        notification.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        container.appendChild(notification);

        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(notification, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        } else {
            notification.style.display = 'block';
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        notification.addEventListener('hidden.bs.toast', function () {
            notification.remove();
        });
    }

    async function submitCartForm(form) {
        console.log('🚀 Submitting form:', form.action);

        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        console.log('📡 Response status:', response.status);

        if (!response.ok) {
            console.log('❌ Response not OK:', response.status);
            return;
        }

        const data = await response.json();
        console.log('📦 Response data:', data);

        if (data.message) {
            const messageType = data.success === false ? 'danger' : 'success';
            const productName = data.product_name || form.getAttribute('data-product-name') || 'Товар';

            if (data.message === 'Товар закончился') {
                showNotification(`❌ ${productName} закончился!`, 'danger');

                const button = form.querySelector('button');
                if (button) {
                    button.disabled = true;
                }

                const cardBody = form.closest('.card-body');
                if (cardBody && !cardBody.querySelector('.badge.bg-danger.mb-2')) {
                    const stockBadge = document.createElement('span');
                    stockBadge.className = 'badge bg-danger mb-2';
                    stockBadge.textContent = 'Нет в наличии';
                    cardBody.insertBefore(stockBadge, cardBody.querySelector('h5').nextSibling);
                }
            } else {
                showNotification(`✅ ${data.message}`, messageType);
            }
        }

        if (typeof data.cartCount !== 'undefined') {
            console.log('🔄 Updating cart count to:', data.cartCount);
            setCartCount(data.cartCount);
        }

        const cartContent = document.getElementById('cart-content');
        if (cartContent && typeof data.html === 'string') {
            console.log('📝 Updating cart content HTML');
            cartContent.innerHTML = data.html;
        }
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.hasAttribute('data-ajax-cart')) return;
        e.preventDefault();
        submitCartForm(form);
    });

    document.addEventListener('change', function (e) {
        const input = e.target;
        if (!(input instanceof HTMLInputElement)) return;
        const form = input.closest('form[data-ajax-cart]');
        if (!form) return;
        if (form.getAttribute('data-cart-action') !== 'set') return;
        submitCartForm(form);
    });
</script>

<!-- Cart Button -->
@section('cart-button')
    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm position-relative">
        Корзина
        <span class="badge bg-secondary ms-1" data-cart-count>{{ $cartCount }}</span>
    </a>
@endsection
</body>
</html>
