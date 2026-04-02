<?php

namespace App\Services;

use App\DTOs\CheckoutData;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function processOrder(array $validatedData): array
    {
        try {
            DB::beginTransaction();

            $cart = session()->get('cart', []);

            if (empty($cart['items'])) {
                return [
                    'success' => false,
                    'message' => 'Корзина пуста'
                ];
            }

            $user = Auth::user();
            $checkoutData = CheckoutData::fromArray($validatedData);

            $address = $this->getDeliveryAddress($user, $checkoutData);
            $this->checkStock($cart['items']);
            $order = $this->createOrder($user, $address, $cart, $checkoutData);
            $this->createOrderItems($order, $cart['items']);

            session()->forget('cart');
            session()->forget('cart_count');

            DB::commit();

            return [
                'success' => true,
                'order' => $order,
                'message' => 'Заказ успешно оформлен'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data' => $validatedData ?? []
            ]);

            return [
                'success' => false,
                'message' => 'Произошла ошибка при оформлении заказа. Попробуйте позже.'
            ];
        }
    }

    private function getDeliveryAddress($user, CheckoutData $data): ?Address
    {
        // Если address_id есть и это не пустая строка, ищем существующий адрес
        if (!empty($data->address_id) && is_numeric($data->address_id)) {
            return Address::where('id', $data->address_id)
                ->where('user_id', $user->id)
                ->first();
        }


        // Создаем новый адрес
        $address = Address::create([
            'user_id' => $user->id,
            'country' => $data->country,
            'city' => $data->city,
            'street' => $data->street,
            'house' => $data->house,
            'apartment' => $data->apartment,
            'is_default' => $data->set_as_default ?? false,
        ]);

        return $address;
    }

    private function checkStock(array $items): void
    {
        $productIds = array_keys($items);
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $productId => $quantity) {
            if ($products->has($productId)) {
                $product = $products->get($productId);

                if ($product->stock < $quantity) {
                    throw new \Exception("Товар '{$product->name}' недоступен в запрошенном количестве. Доступно: {$product->stock}");
                }
            } else {
                throw new \Exception("Товар с ID {$productId} не найден");
            }
        }
    }

    private function createOrder($user, ?Address $address, array $cart, CheckoutData $data): Order
    {
        $orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . date('Ymd');

        // Получаем товары для расчета итогов
        $productIds = array_keys($cart['items']);
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $totalAmount = 0;
        $totalQuantity = 0;

        foreach ($cart['items'] as $productId => $quantity) {
            if ($products->has($productId)) {
                $totalAmount += $products->get($productId)->price * $quantity;
                $totalQuantity += $quantity;
            }
        }

        $shippingAddress = $address
            ? $address->full_address
            : "{$data->country}, {$data->city}, {$data->street}, д. {$data->house}" . ($data->apartment ? ", кв. {$data->apartment}" : "");

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address?->id,
            'order_number' => $orderNumber,
            'total_amount' => $totalAmount,
            'total_quantity' => $totalQuantity,
            'status' => 'pending',
            'payment_method' => $data->payment_method,
            'payment_status' => 'pending',
            'recipient_name' => $data->recipient_name,
            'recipient_phone' => $data->recipient_phone,
            'shipping_address' => $shippingAddress,
            'comment' => $data->comment,
        ]);

        return $order;
    }

    private function createOrderItems(Order $order, array $items): void
    {
        // Получаем ID всех товаров из корзины
        $productIds = array_keys($items);

        // Загружаем товары из базы данных
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $productId => $quantity) {
            if ($products->has($productId)) {
                $product = $products->get($productId);

                // Уменьшаем количество товара на складе
                $product->decrement('stock', $quantity);

                // Создаем позицию заказа
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'product_price' => $product->price,
                    'subtotal' => $product->price * $quantity,
                ]);
            }
        }
    }
}
