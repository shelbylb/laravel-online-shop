<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Запуск ProductSeeder...');

        if (!Schema::hasTable('products')) {
            $this->command->error('❌ Таблица products не существует!');
            $this->command->info('💡 Сначала запустите миграции: php artisan migrate');
            return;
        }

        if (!Schema::hasTable('categories')) {
            $this->command->error('❌ Таблица categories не существует!');
            $this->command->info('💡 Сначала запустите миграции и CategorySeeder');
            return;
        }

        $this->ensureRequiredCategoriesExist();

        $existingProducts = DB::table('products')->count();

        if ($existingProducts > 0) {
            $this->command->warn("⚠️ Таблица products уже содержит {$existingProducts} записей.");

            $answer = $this->command->choice(
                'Что делать?',
                ['Пропустить', 'Очистить и создать заново', 'Добавить к существующим'],
                0
            );

            if ($answer === 'Пропустить') {
                $this->command->info('✅ Пропускаем создание товаров.');
                $this->showProductStatistics();
                return;
            }

            if ($answer === 'Очистить и создать заново') {
                DB::statement('TRUNCATE TABLE products RESTART IDENTITY CASCADE');
                $this->command->info('🗑️ Таблица products очищена.');
            }
        }

        $this->command->info('🎨 Создание товаров...');

        $method = $this->command->choice('Выберите метод создания:', [
            'Создать конкретные 10 товаров (4 сумки, 3 рюкзака, 3 клатча)',
            'Использовать фабрику для создания товаров',
            'Создать только товары для основных категорий',
        ], 0);

        switch ($method) {
            case 'Создать конкретные 10 товаров (4 сумки, 3 рюкзака, 3 клатча)':
                $this->createSpecificProducts();
                break;

            case 'Использовать фабрику для создания товаров':
                $count = (int) $this->command->ask('Сколько товаров создать?', 20);
                $this->createWithFactory($count);
                break;

            case 'Создать только товары для основных категорий':
                $this->createProductsForBasicCategories();
                break;
        }

        $this->command->info('✅ ProductSeeder выполнен успешно!');
        $this->showProductStatistics();
    }

    /**
     * Проверка наличия обязательных категорий
     */
    private function ensureRequiredCategoriesExist(): void
    {
        $requiredSlugs = ['bags', 'backpacks', 'clutches'];

        $existing = DB::table('categories')
            ->whereIn('slug', $requiredSlugs)
            ->pluck('slug')
            ->all();

        $missing = array_diff($requiredSlugs, $existing);

        if (!empty($missing)) {
            throw new \RuntimeException(
                'Не найдены обязательные категории: ' . implode(', ', $missing) . '. Сначала запустите CategorySeeder.'
            );
        }
    }

    /**
     * Создать конкретные 10 товаров
     */
    private function createSpecificProducts(): void
    {
        $bagsCategory = DB::table('categories')->where('slug', 'bags')->first();
        $backpacksCategory = DB::table('categories')->where('slug', 'backpacks')->first();
        $clutchesCategory = DB::table('categories')->where('slug', 'clutches')->first();

        if (!$bagsCategory || !$backpacksCategory || !$clutchesCategory) {
            $this->command->error('❌ Не найдены необходимые категории!');
            return;
        }

        $products = [
            // Сумки
            [
                'name' => 'Кожаная сумка Michael Kors Jet Set',
                'description' => 'Элегантная женская сумка из экокожи. Вместительная, с удобными ручками и стильной металлической фурнитурой.',
                'price' => 12999.99,
                'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=640&h=480&fit=crop',
                'category_id' => $bagsCategory->id,
                'sku' => 'BAG-001',
                'stock' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Повседневная сумка Guess Noelle',
                'description' => 'Удобная сумка для повседневного использования. Подходит для города, работы и прогулок.',
                'price' => 8999.50,
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=640&h=480&fit=crop',
                'category_id' => $bagsCategory->id,
                'sku' => 'BAG-002',
                'stock' => 12,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Сумка через плечо Furla Metropolis',
                'description' => 'Компактная и стильная сумка через плечо. Отлично подходит для вечерних выходов и повседневного образа.',
                'price' => 15899.00,
                'image' => 'https://images.unsplash.com/photo-1591561954557-26941169b49e?w=640&h=480&fit=crop',
                'category_id' => $bagsCategory->id,
                'sku' => 'BAG-003',
                'stock' => 8,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Шоппер Lacoste Daily Classic',
                'description' => 'Вместительная сумка-шоппер для ежедневного использования. Подходит для покупок, офиса и учебы.',
                'price' => 7600.00,
                'image' => 'https://images.unsplash.com/photo-1559563458-527698bf5295?w=640&h=480&fit=crop',
                'category_id' => $bagsCategory->id,
                'sku' => 'BAG-004',
                'stock' => 14,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Рюкзаки
            [
                'name' => 'Городской рюкзак Herschel Little America',
                'description' => 'Практичный рюкзак для города и поездок. Вместительное основное отделение, удобные лямки, стильный дизайн.',
                'price' => 10999.00,
                'image' => 'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?w=640&h=480&fit=crop',
                'category_id' => $backpacksCategory->id,
                'sku' => 'BACKPACK-001',
                'stock' => 9,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Спортивный рюкзак Nike Utility Speed',
                'description' => 'Легкий и удобный рюкзак для тренировок, учебы и повседневного ношения.',
                'price' => 8200.00,
                'image' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=640&h=480&fit=crop',
                'category_id' => $backpacksCategory->id,
                'sku' => 'BACKPACK-002',
                'stock' => 11,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Туристический рюкзак Deuter Futura',
                'description' => 'Надежный рюкзак для путешествий и активного отдыха. Эргономичная спинка и множество карманов.',
                'price' => 14500.00,
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=640&h=480&fit=crop',
                'category_id' => $backpacksCategory->id,
                'sku' => 'BACKPACK-003',
                'stock' => 6,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Клатчи
            [
                'name' => 'Вечерний клатч Aldo Glam Shine',
                'description' => 'Элегантный вечерний клатч с блестящим покрытием. Подходит для торжественных мероприятий.',
                'price' => 5999.00,
                'image' => 'https://images.unsplash.com/photo-1575032617751-6ddec2089882?w=640&h=480&fit=crop',
                'category_id' => $clutchesCategory->id,
                'sku' => 'CLUTCH-001',
                'stock' => 13,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Клатч-конверт Mango Elegant',
                'description' => 'Минималистичный клатч-конверт для повседневного и вечернего образа.',
                'price' => 4200.00,
                'image' => 'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?w=640&h=480&fit=crop',
                'category_id' => $clutchesCategory->id,
                'sku' => 'CLUTCH-002',
                'stock' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Премиальный клатч Coach Signature',
                'description' => 'Стильный клатч премиум-класса с фирменным дизайном. Компактный и удобный.',
                'price' => 9800.00,
                'image' => 'https://images.unsplash.com/photo-1585487000143-2d9493f6a41e?w=640&h=480&fit=crop',
                'category_id' => $clutchesCategory->id,
                'sku' => 'CLUTCH-003',
                'stock' => 7,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $createdCount = 0;

        foreach ($products as $product) {
            if (!DB::table('products')->where('name', $product['name'])->exists()) {
                DB::table('products')->insert($product);
                $createdCount++;
                $this->command->info("   ✓ Создан товар: {$product['name']}");
            }
        }

        $this->command->info("\n📊 Создано товаров: {$createdCount} из " . count($products));
    }

    /**
     * Создать товары используя фабрику
     */
    private function createWithFactory(int $count): void
    {
        $this->command->info("Создание {$count} товаров через фабрику...");

        Product::factory()->count($count)->create();

        $this->command->info("✅ Создано {$count} товаров через фабрику");
    }

    /**
     * Создать товары только для основных категорий
     */
    private function createProductsForBasicCategories(): void
    {
        $bagsCategory = DB::table('categories')->where('slug', 'bags')->first();
        $backpacksCategory = DB::table('categories')->where('slug', 'backpacks')->first();
        $clutchesCategory = DB::table('categories')->where('slug', 'clutches')->first();

        if (!$bagsCategory || !$backpacksCategory || !$clutchesCategory) {
            $this->command->error('❌ Не найдены основные категории!');
            return;
        }

        $bagsCount = 4;
        $backpacksCount = 3;
        $clutchesCount = 3;

        $this->command->info("Создание товаров для категорий:");
        $this->command->info("  - Сумки: {$bagsCount} товара");
        $this->command->info("  - Рюкзаки: {$backpacksCount} товара");
        $this->command->info("  - Клатчи: {$clutchesCount} товара");

        Product::factory()
            ->count($bagsCount)
            ->bags()
            ->create([
                'category_id' => $bagsCategory->id,
            ]);

        $this->command->info("  ✓ Создано {$bagsCount} товаров для категории 'сумки'");

        Product::factory()
            ->count($backpacksCount)
            ->backpacks()
            ->create([
                'category_id' => $backpacksCategory->id,
            ]);

        $this->command->info("  ✓ Создано {$backpacksCount} товаров для категории 'рюкзаки'");

        Product::factory()
            ->count($clutchesCount)
            ->clutches()
            ->create([
                'category_id' => $clutchesCategory->id,
            ]);

        $this->command->info("  ✓ Создано {$clutchesCount} товаров для категории 'клатчи'");

        $total = $bagsCount + $backpacksCount + $clutchesCount;
        $this->command->info("✅ Всего создано: {$total} товаров");
    }

    /**
     * Показать статистику товаров
     */
    private function showProductStatistics(): void
    {
        $totalProducts = DB::table('products')->count();
        $totalCategories = DB::table('categories')->count();

        $this->command->info("\n📊 СТАТИСТИКА ТОВАРОВ:");
        $this->command->info("   Всего товаров: {$totalProducts}");
        $this->command->info("   Всего категорий: {$totalCategories}");

        if ($totalProducts > 0) {
            $stats = DB::select("
                SELECT
                    c.name as category_name,
                    COUNT(p.id) as product_count,
                    MIN(p.price) as min_price,
                    MAX(p.price) as max_price,
                    AVG(p.price) as avg_price
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id
                GROUP BY c.id, c.name
                ORDER BY product_count DESC
            ");

            $this->command->info("\n   Распределение по категориям:");

            $headers = ['Категория', 'Товаров', 'Мин. цена', 'Макс. цена', 'Средняя'];
            $rows = [];

            foreach ($stats as $stat) {
                $rows[] = [
                    $stat->category_name,
                    $stat->product_count,
                    number_format($stat->min_price ?? 0, 2, '.', ' ') . ' ₽',
                    number_format($stat->max_price ?? 0, 2, '.', ' ') . ' ₽',
                    number_format($stat->avg_price ?? 0, 2, '.', ' ') . ' ₽',
                ];
            }

            $this->command->table($headers, $rows);

            $priceStats = DB::select("
                SELECT
                    COUNT(*) as total,
                    MIN(price) as overall_min,
                    MAX(price) as overall_max,
                    AVG(price) as overall_avg
                FROM products
            ")[0];

            $this->command->info("\n   Общая статистика цен:");
            $this->command->info("     Самая низкая цена: " . number_format($priceStats->overall_min, 2, '.', ' ') . ' ₽');
            $this->command->info("     Самая высокая цена: " . number_format($priceStats->overall_max, 2, '.', ' ') . ' ₽');
            $this->command->info("     Средняя цена: " . number_format($priceStats->overall_avg, 2, '.', ' ') . ' ₽');
        }
    }

    /**
     * Создать товары-примеры
     */
    public function createSampleProducts(): void
    {
        $this->command->info('Создание примеров товаров...');

        $bagsId = DB::table('categories')->where('slug', 'bags')->value('id');
        $backpacksId = DB::table('categories')->where('slug', 'backpacks')->value('id');
        $clutchesId = DB::table('categories')->where('slug', 'clutches')->value('id');

        $sampleProducts = [
            [
                'name' => 'Базовая женская сумка',
                'description' => 'Удобная сумка для повседневного использования',
                'price' => 5999.99,
                'category_id' => $bagsId,
                'sku' => 'SAMPLE-BAG-001',
                'stock' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Городской рюкзак',
                'description' => 'Практичный рюкзак для города и учебы',
                'price' => 4599.00,
                'category_id' => $backpacksId,
                'sku' => 'SAMPLE-BACKPACK-001',
                'stock' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Элегантный клатч',
                'description' => 'Стильный клатч для вечерних выходов',
                'price' => 3999.00,
                'category_id' => $clutchesId,
                'sku' => 'SAMPLE-CLUTCH-001',
                'stock' => 5,
                'status' => 'active',
            ],
        ];

        foreach ($sampleProducts as $product) {
            Product::create($product);
            $this->command->info("   ✓ Создан: {$product['name']}");
        }

        $this->command->info('✅ Примеры товаров созданы');
    }
}
