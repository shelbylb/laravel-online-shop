<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
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

        return redirect()->route('orders.show', $result['order'])
            ->with('success', 'Заказ успешно оформлен!');
    }
}
