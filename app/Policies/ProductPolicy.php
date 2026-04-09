<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;

class ProductPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole(Role::ROLE_ADMIN)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function delete(User $user, Product $product): bool
    {
        return false;
    }
}
