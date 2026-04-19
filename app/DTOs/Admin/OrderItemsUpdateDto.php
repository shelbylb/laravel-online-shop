<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateOrderItemsRequest;
use Spatie\LaravelData\Data;

class OrderItemsUpdateDto extends Data
{
    /**
     * @param array<int, array{id:int, quantity:int}> $items
     */
    public function __construct(
        public array $items,
    ) {
    }

    public static function fromRequest(UpdateOrderItemsRequest $request): self
    {
        $validated = $request->validated();

        $items = collect($validated['items'])
            ->map(fn (array $item) => [
                'id' => (int) $item['id'],
                'quantity' => (int) $item['quantity'],
            ])
            ->values()
            ->all();

        return new self(items: $items);
    }
}
