<?php

namespace App\DTOs\User;

use App\Http\Requests\Admin\UserIndexRequest;

class UserFilterDTO
{
    public function __construct(
        public ?string $search,
        public ?string $role,
    ) {
    }

    public static function fromRequest(UserIndexRequest $request): self
    {
        return new self(
            search: $request->filled('search') ? trim((string) $request->input('search')) : null,
            role: $request->filled('role') ? (string) $request->input('role') : null,
        );
    }
}
