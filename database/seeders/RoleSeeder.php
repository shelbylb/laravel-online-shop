<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

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
