<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Figtree, sans-serif;
        }

        .page-wrapper {
            min-height: 100vh;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    @include('layouts.navigation')

    <main class="container py-4">
        @yield('content')
    </main>
</div>

@php($cartCount = session()->has('cart.items') ? collect(session('cart.items', []))->sum() : 0)

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function setCartCount(count) {
            const counters = document.querySelectorAll('[data-cart-counter]');

            counters.forEach(counter => {
                counter.textContent = count;

                if (count > 0) {
                    counter.style.display = 'inline-flex';
                    counter.classList.remove('d-none');
                } else {
                    counter.style.display = 'none';
                    counter.classList.add('d-none');
                }
            });
        }

        function showNotification(message, type = 'danger') {
            let container = document.getElementById('notification-container');

            if (!container) {
                container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1050';
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
            notification.setAttribute('role', 'alert');
            notification.setAttribute('aria-live', 'assertive');
            notification.setAttribute('aria-atomic', 'true');

            notification.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Закрыть"></button>
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
            }, 3000);
        }

        async function submitCartForm(form) {
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

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (data.message) {
                const messageType = data.success === false ? 'danger' : 'success';
                const productName = data.product_name || form.getAttribute('data-product-name') || 'Товар';

                if (data.message === 'Товар закончился') {
                    showNotification(`${productName} закончился.`, 'danger');

                    const button = form.querySelector('button');
                    if (button) {
                        button.disabled = true;
                    }

                    const cardBody = form.closest('.card-body');
                    if (cardBody && !cardBody.querySelector('.badge.bg-danger.mb-2')) {
                        const stockBadge = document.createElement('span');
                        stockBadge.className = 'badge bg-danger mb-2';
                        stockBadge.textContent = 'Нет в наличии';

                        const title = cardBody.querySelector('h5');
                        if (title) {
                            cardBody.insertBefore(stockBadge, title.nextSibling);
                        }
                    }
                } else {
                    showNotification(data.message, messageType);
                }
            }

            if (typeof data.cartCount !== 'undefined') {
                setCartCount(data.cartCount);
            }

            const cartContent = document.getElementById('cart-content');
            if (cartContent && typeof data.html === 'string') {
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

        document.addEventListener('DOMContentLoaded', function () {
            setCartCount({{ $cartCount }});
        });
    </script>
</body>
</html>
