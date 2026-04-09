<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ProductDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $search = $request->string('q')->toString();
        $products = $this->productService->getPaginatedProducts($search);

        return view('admin.products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create', [
            'product' => new Product(),
            'categories' => $this->productService->getCategories(),
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);
        $product = $this->productService->create($dto);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Товар успешно создан.');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        return view('admin.products.show', [
            'product' => $this->productService->getProduct($product),
        ]);
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('admin.products.edit', [
            'product' => $this->productService->getProduct($product),
            'categories' => $this->productService->getCategories(),
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);
        $this->productService->update($product, $dto);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Товар успешно обновлён.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар успешно удалён.');
    }
}
