<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\OrderIndexRequest;
use Spatie\LaravelData\Data;

class OrderFiltersDto extends Data
{
    public function __construct(
        public ?string $q,
        public ?string $status,
    ) {
    }

    public static function fromRequest(OrderIndexRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            q: isset($validated['q']) && $validated['q'] !== '' ? trim($validated['q']) : null,
            status: $validated['status'] ?? null,
        );
    }
}
