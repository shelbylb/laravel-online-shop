<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTOs\ProductDto;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getPaginatedProducts(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category')
            ->orderByDesc('id');

        if ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getProduct(Product $product): Product
    {
        return $product->load('category');
    }

    public function getCategories(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    public function create(ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image instanceof UploadedFile) {
            $data['image'] = $dto->image->store('products', 'public');
        }

        return Product::query()->create($data);
    }

    public function update(Product $product, ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image instanceof UploadedFile) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $dto->image->store('products', 'public');
        }

        $product->update($data);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}
