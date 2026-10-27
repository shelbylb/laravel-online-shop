<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductService
{

    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function getProductPageData(Product $product): Product|array
    {
        return $product;
    }

    /**
     * Возвращает список товаров с контролируемой пагинацией.
     */
    public function getProducts(ProductFilterDto $dto, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = Product::query();

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true)
            ? $dto->per_page
            : 10;

        Cache::add('products.catalog.version', 1);

        $version = (int) Cache::get('products.catalog.version');

        $cacheKey = 'products.catalog.v' . $version . '.' . md5(json_encode([
                'category_id' => $categoryId,
                'sort' => $dto->sort,
                'page' => request()->integer('page', 1),
                'q' => $dto->q,
                'min_price' => $dto->min_price,
                'max_price' => $dto->max_price,
                'in_stock' => $dto->in_stock,
                'per_page' => $perPage,
            ]));


        /**
         * 1) Поиск (базовый)
         * Ищем по name и sku.
         *
         * Важно: оборачиваем OR-условия в where(function),
         * чтобы они не ломали остальные фильтры.
         */
        if ($dto->q) {
            $q = $dto->q;

            $query->where(function ($subQuery) use ($q) {
                $subQuery
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('sku', 'like', '%' . $q . '%');
            });
        }

        /**
         * 2) Фильтр по цене
         */
        if ($dto->min_price !== null) {
            $query->where('price', '>=', $dto->min_price);
        }

        if ($dto->max_price !== null) {
            $query->where('price', '<=', $dto->max_price);
        }

        /**
         * 3) Только в наличии
         */
        if ($dto->in_stock) {
            $query->where('stock', '>', 0);
        }

        /**
         * 4) Сортировка (только через whitelist)
         */
        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;

            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;

            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        /**
         * Вторичная сортировка (чтобы порядок был стабильным, если значения одинаковые)
         * Например: два товара с одинаковой ценой.
         */
        $query->orderByDesc('id');

        /**
         * 5) Пагинация + сохранение query string
         * withQueryString() — чтобы при переключении страниц фильтры НЕ сбрасывались.
         */
        return Cache::remember($cacheKey, 300, function () use ($query, $perPage) {
            return $query->paginate($perPage)->withQueryString();
        });
    }

    public function getMaxProductPrice(): int
    {
        return(int) Cache::remember('products.max_price', 300, function () {
            return (int) (Product::query()->max('price') ?? 0);
        });
    }
}

