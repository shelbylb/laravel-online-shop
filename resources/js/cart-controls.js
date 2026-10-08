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
            const container = document.querySelector('[data-cart-quantities-url]');

            if (!container) {
                return;
            }
            const url = container.dataset.cartQuantitiesUrl;

            const response = await fetch(url, {
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
