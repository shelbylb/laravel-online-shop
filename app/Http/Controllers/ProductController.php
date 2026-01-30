<?php

namespace App\Http\Controllers;

use App\DTOs\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(ProductFilterRequest $request)
    {
        // 1) Из validated данных собираем DTO
        $dto = ProductFilterDto::fromRequest($request);

        // 2) Сервис строит запрос
        $products = $this->productService->getProducts($dto);

        // 3) Для подсказки диапазона цен (placeholder)
        $maxPrice = $this->productService->getMaxProductPrice();

        return view('products.index', compact('products', 'dto', 'maxPrice'));
    }

    public function show(Product $product): Factory|View
    {
        $product = $productService->getProductPageData($product);

        return view('products.show', [
            'product' => $product,
        ]);
    }
}
