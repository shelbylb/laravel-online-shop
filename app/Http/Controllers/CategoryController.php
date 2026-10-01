<?php

namespace App\Http\Controllers;

use App\DTOs\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Services\ProductService;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category, ProductFilterRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $dto = ProductFilterDto::fromRequest($request);

        $products = $this->productService->getProducts($dto, $category->id);

        $maxPrice = $this->productService->getMaxProductPrice();

        return view('products.index', [
            'products' => $products,
            'category' => $category,
            'dto'      => $dto,
            'maxPrice' => $maxPrice,
        ]);
    }
}
