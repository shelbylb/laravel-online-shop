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

        $adminRoleId = Role::query()->where('slug', Role::ROLE_ADMIN)->value('id');
        $managerRoleId = Role::query()->where('slug', Role::ROLE_MANAGER)->value('id');

        $adminsCount = $adminRoleId
            ? User::query()->where('role_id', $adminRoleId)->count()
            : 0;

        $managersCount = $managerRoleId
            ? User::query()->where('role_id', $managerRoleId)->count()
            : 0;

        return new DashboardDTO(
            usersCount: $usersCount,
            adminsCount: $adminsCount,
            managersCount: $managersCount,
        );
    }
}
