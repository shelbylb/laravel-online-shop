<?php

namespace App\DTOs\User;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserUpsertDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public array $roles,
    ) {
    }

    public static function fromStoreRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            roles: $request->input('roles', []),
        );
    }

    public static function fromUpdateRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            password: $request->filled('password') ? $request->string('password')->toString() : null,
            roles: $request->input('roles', []),
        );
    }
}
