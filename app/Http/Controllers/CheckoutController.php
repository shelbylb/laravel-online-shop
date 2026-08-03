<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\YooKassaPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        private readonly YooKassaPaymentService $paymentService,
    ) {
    }

    /**
     * Отображение страницы оформления заказа
     */
    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста');
        }

        // Получаем полные данные о товарах
        $productIds = array_keys($cart['items']);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Формируем структуру, ожидаемую в шаблоне
        $cartItems = [];
        $totalPrice = 0;
        $totalQuantity = 0;

        foreach ($cart['items'] as $productId => $quantity) {
            if ($products->has($productId)) {
                $product = $products->get($productId);
                $subtotal = $product->price * $quantity;

                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];

                $totalPrice += $subtotal;
                $totalQuantity += $quantity;
            }
        }

        $user = Auth::user();
        $addresses = $user->addresses()->get();

        // Передаем в шаблон переменную cart с нужной структурой
        return view('checkout.index', [
            'cart' => [
                'items' => $cartItems,
                'total_price' => $totalPrice,
                'total_quantity' => $totalQuantity
            ],
            'addresses' => $addresses,
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $result = $this->checkoutService->processOrder($request->validated());

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        /** @var Order $order */
        $order = $result['order'];

        if ($order->payment_method === Order::PAYMENT_METHOD_CASH) {
            return redirect()->route('orders.index')
                ->with('success', 'Заказ успешно оформлен!');
        }

        try {
            $payment = $this->paymentService->createPaymentForOrder($order);
        } catch (Throwable $exception) {
            Log::error('Failed to create YooKassa payment', [
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Заказ создан, но не удалось перейти к оплате. Попробуйте ещё раз.');
        }

        if (!$payment->confirmation_url) {
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Статус оплаты заказа обновлён.');
        }

        return redirect()->away($payment->confirmation_url);
    }
}
