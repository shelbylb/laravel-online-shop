<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use Spatie\LaravelData\Data;

class OrderStatusUpdateDto extends Data
{
    public function __construct(
        public string $status,
    ) {
    }

    public static function fromRequest(UpdateOrderStatusRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            status: $validated['status'],
        );
    }
}
