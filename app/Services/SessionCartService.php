<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class SessionCartService
{
    private const string SESSION_KEY = 'cart.items';

    private function getRaw(): array
    {
        $raw = session()->get(self::SESSION_KEY, []);

        if (!is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $result[$productId] = $quantity;
        }

        return $result;
    }

    private function putRaw(array $raw): void
    {
        session()?->put(self::SESSION_KEY, $raw);
    }

    public function getItems(): array
    {
        $raw = $this->getRaw();
        $productIds = array_map('intval', array_keys($raw));

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($raw as $productId => $quantity) {
            $product = $products->get((int) $productId);
            if (!$product) {
                $this->removeById((int) $productId);
                continue;
            }

            $quantity = max(1, (int) $quantity);
            $unitPrice = (string) $product->price;
            $subtotal = bcmul($unitPrice, (string) $quantity, 2);

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        return $items;
    }

    public function getTotalQuantity(): int
    {
        return (int) collect($this->getRaw())->sum();
    }

    public function getTotalPrice(): string
    {
        $total = '0.00';
        foreach ($this->getItems() as $item) {
            $total = bcadd($total, $item['subtotal'], 2);
        }

        return $total;
    }

    public function add(Product $product, int $quantity = 1)
    {
        $quantity = max(1, $quantity);

        $raw = $this->getRaw();
        $productId = $product->id;
        $current = (int) ($raw[$product->id] ?? 0);
        $next = $current + $quantity;

        if ($product->stock === 0) {
            $this->removeById($product->id);
            return [
                'success' => false,
                'message' => 'Товар закончился',
                'product_name' => $product->name
            ];
        }

        $next = min($next, $product->stock);

        $raw[$product->id] = $next;

        $this->putRaw($raw);

        // Определяем, достигнут ли максимум
        $max_reached = ($next >= $product->stock);

        // Формируем сообщение
        if ($current >= $product->stock) {
            $message = "Больше нельзя добавить. В наличии только {$product->stock} шт.";
            $success = false;
        } elseif ($next > $current) {
            if ($max_reached && $current < $product->stock) {
                $message = "Добавлено до максимума. В корзине {$next} шт.";
            } else {
                $message = 'Товар добавлен в корзину';
            }
            $success = true;
        } else {
            $message = 'Количество не изменилось';
            $success = true;
        }

        return [
            'success' => $success,
            'quantity' => $next,
            'message' => $message,
            'product_name' => $product->name,
            'max_reached' => $max_reached,
            'current_quantity' => $next,
            'max_allowed' => $product->stock
        ];
    }

    public function setQuantity(Product $product, int $quantity): int
    {
        $quantity = max(0, $quantity);

        if ($product->stock <= 0 || $quantity === 0) {
            $this->removeById($product->id);
            return 0;
        }

        $quantity = min($quantity, $product->stock);

        $raw = $this->getRaw();
        $raw[$product->id] = $quantity;
        $this->putRaw($raw);

        return $quantity;
    }

    public function remove(Product $product): void
    {
        $this->removeById($product->id);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function removeById(int $productId): void
    {
        $raw = $this->getRaw();
        unset($raw[$productId]);
        $this->putRaw($raw);
    }

    public function getProductQuantity(Product $product): int
    {
        $raw = $this->getRaw();

        return (int) ($raw[$product->id] ?? 0);
    }

    public function getQuantities(): array
    {
        return $this->getRaw();
    }

}




