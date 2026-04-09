<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private SessionCartService $sessionCartService,
    ) {}

    public function index(): Factory|View
    {
        return view('cart.index', [
            'items' => $this->sessionCartService->getItems(),
            'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
            'totalPrice' => $this->sessionCartService->getTotalPrice(),
        ]);
    }

    public function store(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $this->sessionCartService->add($product, (int) ($data['quantity'] ?? 1));

        if (is_array($result)) {
            return $this->respond($request, $result, $product);
        }

        return $this->respond($request, null, $product);
    }

    public function update(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->sessionCartService->setQuantity($product, (int) $data['quantity']);

        return $this->respond($request, null, $product);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->sessionCartService->remove($product);

        return $this->respond($request, null, $product);
    }

    public function clear(Request $request)
    {
        $this->sessionCartService->clear();

        return $this->respond($request);
    }

    public function increase(Product $product, Request $request)
    {
        $result = $this->sessionCartService->add($product, 1);

        if (is_array($result)) {
            return $this->respond($request, $result, $product);
        }

        return $this->respond($request, null, $product);
    }

    public function decrease(Product $product, Request $request)
    {
        $currentQuantity = $this->sessionCartService->getProductQuantity($product);

        if ($currentQuantity <= 1) {
            $this->sessionCartService->remove($product);
        } else {
            $this->sessionCartService->setQuantity($product, $currentQuantity - 1);
        }

        return $this->respond($request, [
            'success' => true,
            'message' => 'Количество товара обновлено.',
        ], $product);
    }

    public function quantities(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'quantities' => $this->sessionCartService->getQuantities(),
            'cartCount' => $this->sessionCartService->getTotalQuantity(),
        ]);
    }

    private function respond(Request $request, $result = null, ?Product $product = null)
    {
        $cartCount = $this->sessionCartService->getTotalQuantity();

        session(['cart_count' => $cartCount]);

        $payload = [
            'cartCount' => $cartCount,
        ];

        if ($product) {
            $payload['product_id'] = $product->id;
            $payload['quantity'] = $this->sessionCartService->getProductQuantity($product);
        }

        if ($result) {
            if (isset($result['message'])) {
                $payload['message'] = $result['message'];
            }

            $payload['success'] = $result['success'] ?? true;

            if (isset($result['product_name'])) {
                $payload['product_name'] = $result['product_name'];
            }

            if (isset($result['max_reached'])) {
                $payload['max_reached'] = $result['max_reached'];
            }

            if (isset($result['current_quantity'])) {
                $payload['current_quantity'] = $result['current_quantity'];
            }

            if (isset($result['max_allowed'])) {
                $payload['max_allowed'] = $result['max_allowed'];
            }
        }

        if ($request->expectsJson()) {
            $payload['html'] = view('cart._content', [
                'items' => $this->sessionCartService->getItems(),
                'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
                'totalPrice' => $this->sessionCartService->getTotalPrice(),
            ])->render();

            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('cartCount', $cartCount)
            ->with('message', $payload['message'] ?? null)
            ->with('success', $payload['success'] ?? true)
            ->with('max_reached', $payload['max_reached'] ?? null)
            ->with('current_quantity', $payload['current_quantity'] ?? null)
            ->with('max_allowed', $payload['max_allowed'] ?? null)
            ->with('product_name', $payload['product_name'] ?? null);
    }
}
