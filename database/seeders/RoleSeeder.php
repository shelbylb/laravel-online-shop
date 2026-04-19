<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->firstOrCreate(
            ['slug' => Role::ROLE_USER],
            ['name' => 'Пользователь']
        );

        Role::query()->firstOrCreate(
            ['slug' => Role::ROLE_ADMIN],
            ['name' => 'Администратор']
        );

        Role::query()->firstOrCreate(
            ['slug' => Role::ROLE_MANAGER],
            ['name' => 'Менеджер']
        );
    }
}
