<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
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

        // Проверяем, существует ли таблица categories
        if (!Schema::hasTable('categories')) {
            $this->command->error('❌ Таблица categories не существует!');
            $this->command->info('💡 Сначала запустите миграции: php artisan migrate');
            return;
        }

        // Проверяем, не пустая ли уже таблица
        $existingCount = DB::table('categories')->count();
        if ($existingCount > 0) {
            $this->command->warn("⚠️ Таблица categories уже содержит {$existingCount} записей.");

            // Проверяем, есть ли нужные нам категории
            $requiredSlugs = ['boots', 'shoes', 'sneakers'];
            $missing = [];

            foreach ($requiredSlugs as $slug) {
                if (!DB::table('categories')->where('slug', $slug)->exists()) {
                    $missing[] = $slug;
                }
            }

            if (empty($missing)) {
                $this->command->info('✅ Все необходимые категории уже существуют.');
                $this->showCategoriesTable();
                return;
            }

            $this->command->info('Создаем отсутствующие категории: ' . implode(', ', $missing));
        }

        // Создаем основные категории обуви
        $categories = [
            [
                'name' => 'сапоги',
                'slug' => 'boots',
                'description' => 'Стильные и удобные сапоги для любого сезона',
            ],
            [
                'name' => 'туфли',
                'slug' => 'shoes',
                'description' => 'Элегантные туфли для формальных и повседневных образов',
            ],
            [
                'name' => 'кроссовки',
                'slug' => 'sneakers',
                'description' => 'Удобные кроссовки для спорта и активного отдыха',
            ],
            [
                'name' => 'босоножки',
                'slug' => 'sandals',
                'description' => 'Легкие босоножки для летнего сезона',
            ],
            [
                'name' => 'кеды',
                'slug' => 'sneakers-low',
                'description' => 'Классические кеды для повседневной носки',
            ],
            [
                'name' => 'лоферы',
                'slug' => 'loafers',
                'description' => 'Стильная обувь без шнуровки',
            ],
            [
                'name' => 'мокасины',
                'slug' => 'moccasins',
                'description' => 'Удобные мокасины из натуральной кожи',
            ],
            [
                'name' => 'угги',
                'slug' => 'ugg',
                'description' => 'Теплые угги для холодной погоды',
            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($categories as $categoryData) {
            // Проверяем, не существует ли уже категория с таким slug
            if (DB::table('categories')->where('slug', $categoryData['slug'])->exists()) {
                $skippedCount++;
                continue;
            }

            // Создаем категорию
            Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
            ]);

            $createdCount++;
            $this->command->info("   ✓ Создана категория: {$categoryData['name']} ({$categoryData['slug']})");
        }

        $this->command->info("\n📊 Результат:");
        $this->command->info("   Создано новых категорий: {$createdCount}");
        $this->command->info("   Пропущено (уже существовали): {$skippedCount}");
        $this->command->info("   Всего категорий в базе: " . DB::table('categories')->count());

        // Показываем таблицу категорий
        $this->showCategoriesTable();

        $this->command->info('✅ CategorySeeder выполнен успешно!');
    }

    /**
     * Показать содержимое таблицы categories
     */
    private function showCategoriesTable(): void
    {
        $this->command->info("\n📋 Таблица categories:");

        $categories = DB::table('categories')
            ->select('id', 'name', 'slug', 'created_at')
            ->orderBy('id')
            ->get();

        if ($categories->isEmpty()) {
            $this->command->warn('   Таблица пуста');
            return;
        }

        $headers = ['ID', 'Название', 'Slug', 'Создано'];
        $rows = [];

        foreach ($categories as $category) {
            $rows[] = [
                $category->id,
                $category->name,
                $category->slug,
                $category->created_at,
            ];
        }

        $this->command->table($headers, $rows);
    }

    /**
     * Создать только 3 основные категории (сапоги, туфли, кроссовки)
     */
    public function createBasicCategories(): void
    {
        $basicCategories = [
            ['name' => 'сапоги', 'slug' => 'boots'],
            ['name' => 'туфли', 'slug' => 'shoes'],
            ['name' => 'кроссовки', 'slug' => 'sneakers'],
        ];

        foreach ($basicCategories as $category) {
            // Создаем только если не существует
            if (!DB::table('categories')->where('slug', $category['slug'])->exists()) {
                Category::create($category);
                $this->command->info("Создана категория: {$category['name']}");
            }
        }
    }

    /**
     * Создать категории используя фабрику
     */
    public function createWithFactory(int $count = 10): void
    {
        $this->command->info("Создание {$count} категорий через фабрику...");

        // Создаем 3 основные категории
        Category::factory()->boots()->create();
        Category::factory()->shoes()->create();
        Category::factory()->sneakers()->create();

        // Создаем остальные случайные категории
        if ($count > 3) {
            Category::factory()->count($count - 3)->shoeCategory()->create();
        }

        $this->command->info("Создано {$count} категорий через фабрику");
    }
}
