<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class OrderPolicy
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

    public function view(User $user, Order $order): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasRole(Role::ROLE_MANAGER);
    }

    public function cancel(User $user, Order $order): bool
    {
        return false;
    }

    public function editItems(User $user, Order $order): bool
    {
        return false;
    }

    public function updateItems(User $user, Order $order): bool
    {
        return false;
    }
}
