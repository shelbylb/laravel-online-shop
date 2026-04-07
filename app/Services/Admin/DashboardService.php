<?php

namespace App\Services\Admin;

use App\DTOs\Admin\DashboardDTO;
use App\Models\Role;
use App\Models\User;

class DashboardService
{
    public function getDashboardData(): DashboardDTO
    {
        $usersCount = User::query()->count();

        $adminsCount = User::query()
            ->whereHas('roles', fn ($query) => $query->where('slug', Role::ROLE_ADMIN))
            ->count();

        $managersCount = User::query()
            ->whereHas('roles', fn ($query) => $query->where('slug', Role::ROLE_MANAGER))
            ->count();

        return new DashboardDTO(
            usersCount: $usersCount,
            adminsCount: $adminsCount,
            managersCount: $managersCount,
        );
    }
}
