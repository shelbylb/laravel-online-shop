<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Определяем порядок сидров - сначала те, от которых зависят другие
        $this->call([
            CategorySeeder::class,    // Категории нужны для продуктов
            ProductSeeder::class,     // Продукты нужны для корзины и заказов
            UserSeeder::class,        // Пользователи нужны для корзины и заказов
            CartSeeder::class,        // Корзины
            CartItemsSeeder::class,   // Элементы корзины
            OrderSeeder::class,       // Заказы
            OrderItemSeeder::class,   // Элементы заказов
        ]);
    }
}
