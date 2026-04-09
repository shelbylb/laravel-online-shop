<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\Data;

class ProductDto extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock,
        public string $sku,
        public string $status,
        public ?int $category_id,
        public mixed $image,
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            price: (float) $validated['price'],
            stock: (int) $validated['stock'],
            sku: $validated['sku'],
            status: $validated['status'],
            category_id: isset($validated['category_id']) ? (int) $validated['category_id'] : null,
            image: $request->file('image'),
        );
    }

    public function toProductData(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->category_id,
        ];
    }
}
