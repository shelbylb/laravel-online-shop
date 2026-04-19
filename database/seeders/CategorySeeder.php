<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Запуск CategorySeeder...');

        if (!Schema::hasTable('categories')) {
            $this->command->error('❌ Таблица categories не существует!');
            $this->command->info('💡 Сначала запустите миграции: php artisan migrate');
            return;
        }

        $categories = [
            [
                'name' => 'Сумки',
                'slug' => 'bags',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Рюкзаки',
                'slug' => 'backpacks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Клатчи',
                'slug' => 'clutches',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($categories as $category) {
            $existing = DB::table('categories')
                ->where('slug', $category['slug'])
                ->first();

            if ($existing) {
                DB::table('categories')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $category['name'],
                        'updated_at' => now(),
                    ]);

                $updatedCount++;
                $this->command->info("   ↻ Обновлена категория: {$category['name']}");
            } else {
                DB::table('categories')->insert($category);
                $createdCount++;
                $this->command->info("   ✓ Создана категория: {$category['name']}");
            }
        }

        $this->command->info("✅ CategorySeeder завершён. Создано: {$createdCount}, обновлено: {$updatedCount}");
    }
}
