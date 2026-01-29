<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
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

        // Проверяем существование таблиц
        if (!Schema::hasTable('products')) {
            $this->command->error('❌ Таблица products не существует!');
            $this->command->info('💡 Сначала запустите миграции: php artisan migrate');
            return;
        }

        if (!Schema::hasTable('categories')) {
            $this->command->error('❌ Таблица categories не существует!');
            $this->command->info('💡 Сначала запустите CategorySeeder');
            return;
        }

        // Проверяем, есть ли категории
        $categoryCount = DB::table('categories')->count();
        if ($categoryCount === 0) {
            $this->command->warn('⚠️ Таблица categories пуста! Создаем основные категории...');
            $this->createBasicCategories();
        }

        // Проверяем, не пустая ли уже таблица products
        $existingProducts = DB::table('products')->count();
        if ($existingProducts > 0) {
            $this->command->warn("⚠️ Таблица products уже содержит {$existingProducts} записей.");

            $answer = $this->command->choice('Что делать?', ['Пропустить', 'Очистить и создать заново', 'Добавить к существующим'], 0);

            if ($answer === 'Пропустить') {
                $this->command->info('✅ Пропускаем создание товаров.');
                $this->showProductStatistics();
                return;
            }

            if ($answer === 'Очистить и создать заново') {
                DB::table('products')->truncate();
                $this->command->info('🗑️ Таблица products очищена.');
                $existingProducts = 0;
            }
        }

        $this->command->info('🎨 Создание товаров...');

        // Выбираем метод создания
        $method = $this->command->choice('Выберите метод создания:', [
            'Создать конкретные 10 товаров (3 сапога, 4 туфли, 3 кроссовки)',
            'Использовать фабрику для создания товаров',
            'Создать только товары для основных категорий',
        ], 0);

        switch ($method) {
            case 'Создать конкретные 10 товаров (3 сапога, 4 туфли, 3 кроссовки)':
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
     * Создать основные категории если их нет
     */
    private function createBasicCategories(): void
    {
        $basicCategories = [
            ['name' => 'сапоги', 'slug' => 'boots'],
            ['name' => 'туфли', 'slug' => 'shoes'],
            ['name' => 'кроссовки', 'slug' => 'sneakers'],
        ];

        foreach ($basicCategories as $category) {
            if (!DB::table('categories')->where('slug', $category['slug'])->exists()) {
                DB::table('categories')->insert([
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("   ✓ Создана категория: {$category['name']}");
            }
        }
    }

    /**
     * Создать конкретные 10 товаров
     */
    private function createSpecificProducts(): void
    {
        // Получаем ID категорий
        $bootsCategory = DB::table('categories')->where('slug', 'boots')->first();
        $shoesCategory = DB::table('categories')->where('slug', 'shoes')->first();
        $sneakersCategory = DB::table('categories')->where('slug', 'sneakers')->first();

        if (!$bootsCategory || !$shoesCategory || !$sneakersCategory) {
            $this->command->error('❌ Не найдены необходимые категории!');
            return;
        }

        $products = [
            // Сапоги (3 товара)
            [
                'name' => 'Зимние утепленные сапоги Timberland',
                'description' => 'Теплые сапоги на меху для холодной зимы. Водонепроницаемые, с противоскользящей подошвой. Натуральная кожа, удобная колодка.',
                'price' => 12999.99,
                'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=640&h=480&fit=crop',
                'category_id' => $bootsCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Резиновые сапоги Hunter Original',
                'description' => 'Водонепроницаемые сапоги для дождливой погоды. Легкие и удобные для длительных прогулок. Классический дизайн.',
                'price' => 7499.50,
                'image' => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=640&h=480&fit=crop',
                'category_id' => $bootsCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ковбойские сапоги Ariat Heritage',
                'description' => 'Стильные сапоги в ковбойском стиле из натуральной кожи. Удобные для повседневной носки. Прочная подошва.',
                'price' => 15899.00,
                'image' => 'https://images.unsplash.com/photo-1608256246203-29f5a8acb774?w=640&h=480&fit=crop',
                'category_id' => $bootsCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Туфли (4 товара)
            [
                'name' => 'Классические черные туфли Geox Uomo',
                'description' => 'Туфли для делового стиля. Натуральная кожа, дышащая подошва. Идеальны для офиса и важных встреч.',
                'price' => 8999.00,
                'image' => 'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=640&h=480&fit=crop',
                'category_id' => $shoesCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Коричневые броги Clarks Originals',
                'description' => 'Стильные туфли с перфорацией. Идеальны для офиса и повседневной носки. Высокое качество изготовления.',
                'price' => 11200.00,
                'image' => 'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=640&h=480&fit=crop',
                'category_id' => $shoesCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Лоферы Ecco Soft 7',
                'description' => 'Удобные туфли без шнуровки. Мягкая стелька, гибкая подошва. Подходят для повседневной носки.',
                'price' => 9800.00,
                'image' => 'https://images.unsplash.com/photo-1564584217132-2271feaeb3c5?w=640&h=480&fit=crop',
                'category_id' => $shoesCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Оксфорды Salvatore Ferragamo',
                'description' => 'Формальные туфли со шнуровкой. Премиум качество, ручная работа. Идеальны для особых случаев.',
                'price' => 24500.00,
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=640&h=480&fit=crop',
                'category_id' => $shoesCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Кроссовки (3 товара)
            [
                'name' => 'Беговые кроссовки Nike Air Max 270',
                'description' => 'Легкие кроссовки для бега с амортизацией. Технология Air для комфорта. Современный дизайн.',
                'price' => 13999.00,
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=640&h=480&fit=crop',
                'category_id' => $sneakersCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Баскетбольные кроссовки Adidas Harden Vol. 6',
                'description' => 'Высокие кроссовки для баскетбола с поддержкой голеностопа. Технология Boost для амортизации.',
                'price' => 15999.00,
                'image' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=640&h=480&fit=crop',
                'category_id' => $sneakersCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Повседневные кроссовки New Balance 574',
                'description' => 'Удобные кроссовки для повседневной носки. Стильный дизайн, комфорт на весь день. Классическая модель.',
                'price' => 8999.00,
                'image' => 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=640&h=480&fit=crop',
                'category_id' => $sneakersCategory->id,
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

        // Создаем распределение по категориям
        $categories = DB::table('categories')->get();

        if ($categories->isEmpty()) {
            $this->command->error('❌ Нет категорий для создания товаров!');
            return;
        }

        // Создаем товары
        Product::factory()->count($count)->create();

        $this->command->info("✅ Создано {$count} товаров через фабрику");
    }

    /**
     * Создать товары только для основных категорий
     */
    private function createProductsForBasicCategories(): void
    {
        $bootsCategory = DB::table('categories')->where('slug', 'boots')->first();
        $shoesCategory = DB::table('categories')->where('slug', 'shoes')->first();
        $sneakersCategory = DB::table('categories')->where('slug', 'sneakers')->first();

        if (!$bootsCategory || !$shoesCategory || !$sneakersCategory) {
            $this->command->error('❌ Не найдены основные категории!');
            return;
        }

        // Создаем товары для каждой категории
        $bootsCount = 3;
        $shoesCount = 4;
        $sneakersCount = 3;

        $this->command->info("Создание товаров для категорий:");
        $this->command->info("  - Сапоги: {$bootsCount} товара");
        $this->command->info("  - Туфли: {$shoesCount} товара");
        $this->command->info("  - Кроссовки: {$sneakersCount} товара");

        // Создаем товары для сапог
        if ($bootsCount > 0) {
            Product::factory()->count($bootsCount)->boots()->create([
                'category_id' => $bootsCategory->id,
            ]);
            $this->command->info("  ✓ Создано {$bootsCount} товаров для категории 'сапоги'");
        }

        // Создаем товары для туфель
        if ($shoesCount > 0) {
            Product::factory()->count($shoesCount)->shoes()->create([
                'category_id' => $shoesCategory->id,
            ]);
            $this->command->info("  ✓ Создано {$shoesCount} товаров для категории 'туфли'");
        }

        // Создаем товары для кроссовок
        if ($sneakersCount > 0) {
            Product::factory()->count($sneakersCount)->sneakers()->create([
                'category_id' => $sneakersCategory->id,
            ]);
            $this->command->info("  ✓ Создано {$sneakersCount} товаров для категории 'кроссовки'");
        }

        $total = $bootsCount + $shoesCount + $sneakersCount;
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
            // Статистика по категориям
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

            // Общая статистика цен
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
     * Создать товары-примеры (для быстрого тестирования)
     */
    public function createSampleProducts(): void
    {
        $this->command->info('Создание примеров товаров...');

        // Получаем или создаем категории
        $bootsId = DB::table('categories')->where('slug', 'boots')->value('id');
        $shoesId = DB::table('categories')->where('slug', 'shoes')->value('id');
        $sneakersId = DB::table('categories')->where('slug', 'sneakers')->value('id');

        // Создаем по 2 товара в каждую категорию
        $sampleProducts = [
            [
                'name' => 'Теплые зимние сапоги',
                'description' => 'Отличные сапоги для холодной зимы',
                'price' => 5999.99,
                'category_id' => $bootsId,
            ],
            [
                'name' => 'Кожаные туфли',
                'description' => 'Стильные туфли для офиса',
                'price' => 4599.00,
                'category_id' => $shoesId,
            ],
            [
                'name' => 'Спортивные кроссовки',
                'description' => 'Удобные кроссовки для тренировок',
                'price' => 3999.00,
                'category_id' => $sneakersId,
            ],
        ];

        foreach ($sampleProducts as $product) {
            Product::create($product);
            $this->command->info("   ✓ Создан: {$product['name']}");
        }

        $this->command->info('✅ Примеры товаров созданы');
    }
}
