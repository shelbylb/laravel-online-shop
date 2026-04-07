<?php

namespace App\DTOs\Admin;

class DashboardDTO
{
    public function __construct(
        public int $usersCount,
        public int $adminsCount,
        public int $managersCount,
    ) {
    }

}
