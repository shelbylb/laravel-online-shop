<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Список брендов обуви
        $brands = ['Nike', 'Adidas', 'Puma', 'Reebok', 'New Balance', 'Timberland',
            'Geox', 'Ecco', 'Salomon', 'Clarks', 'Skechers', 'Vans', 'Converse',
            'Dr. Martens', 'CAT', 'Steve Madden', 'Birkenstock', 'Crocs'];

        // Типы обуви по категориям
        $shoeTypes = [
            'boots' => ['Зимние', 'Резиновые', 'Ковбойские', 'Утепленные', 'Военные', 'Демисезонные'],
            'shoes' => ['Классические', 'Офисные', 'Вечерние', 'Оксфорды', 'Лоферы', 'Броги', 'Дерби'],
            'sneakers' => ['Беговые', 'Баскетбольные', 'Повседневные', 'Тренировочные', 'Теннисные', 'Треккинговые'],
        ];

        $modelNames = ['Air Max', 'Classic', 'Pro', 'Lite', 'Comfort', 'Elite', 'Runner',
            'Walker', 'Dress', 'Casual', 'Sport', 'Premium', 'Ultra', 'Flex'];

        $brand = $this->faker->randomElement($brands);
        $model = $this->faker->randomElement($modelNames);
        $type = $this->faker->word;

        $name = "{$brand} {$type} {$model}";

        // Получаем случайную категорию или создаем новую
        $category = Category::inRandomOrder()->first() ?? Category::factory();

        return [
            'name' => $name,
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 1500, 35000),
            'image' => $this->generateProductImage($brand, $type),
            'category_id' => $category instanceof Category ? $category->id : $category,
        ];
    }

    /**
     * Генерация URL изображения продукта
     */
    private function generateProductImage(string $brand, string $type): string
    {
        // Используем Unsplash для реалистичных изображений обуви
        $unsplashThemes = [
            'shoes' => ['shoes', 'footwear', 'sneakers', 'boots'],
            'sneakers' => ['sneakers', 'athletic-shoes', 'running-shoes'],
            'boots' => ['boots', 'winter-boots', 'leather-boots'],
        ];

        $theme = $this->faker->randomElement($unsplashThemes['shoes']);
        $width = 640;
        $height = 480;

        return "https://images.unsplash.com/photo-" . $this->faker->regexify('[0-9]{10}') .
            "?w={$width}&h={$height}&fit=crop&crop=entropy&q=80";
    }

    /**
     * Создать товар в категории "сапоги"
     */
    public function boots(): static
    {
        $bootNames = [
            'Зимние утепленные сапоги',
            'Резиновые сапоги для рыбалки',
            'Ковбойские кожаные сапоги',
            'Военные тактические ботинки',
            'Осенние демисезонные сапоги',
            'Горнолыжные ботинки',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($bootNames),
            'description' => $this->generateBootsDescription(),
            'price' => $this->faker->randomFloat(2, 3000, 25000),
        ]);
    }

    /**
     * Описание для сапог
     */
    private function generateBootsDescription(): string
    {
        $materials = ['натуральная кожа', 'замша', 'нубук', 'резина', 'полиуретан'];
        $features = ['водонепроницаемые', 'утепленные мехом', 'противоскользящие',
            'ортопедические', 'дышащие', 'износостойкие'];
        $uses = ['для зимы', 'для дождливой погоды', 'для охоты и рыбалки',
            'для работы', 'для походов', 'повседневные'];

        $material = $this->faker->randomElement($materials);
        $feature = $this->faker->randomElement($features);
        $use = $this->faker->randomElement($uses);

        return "Качественные сапоги из {$material}. {$feature}, идеально подходят {$use}. " .
            "Удобная колодка, надежная подошва, стильный дизайн.";
    }

    /**
     * Создать товар в категории "туфли"
     */
    public function shoes(): static
    {
        $shoeNames = [
            'Классические офисные туфли',
            'Вечерние лаковые туфли',
            'Оксфорды ручной работы',
            'Лоферы из натуральной кожи',
            'Дерби для делового стиля',
            'Броги с перфорацией',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($shoeNames),
            'description' => $this->generateShoesDescription(),
            'price' => $this->faker->randomFloat(2, 2500, 20000),
        ]);
    }

    /**
     * Описание для туфель
     */
    private function generateShoesDescription(): string
    {
        $styles = ['делового', 'вечернего', 'кэжуал', 'классического', 'современного'];
        $materials = ['натуральная кожа', 'лакированная кожа', 'замша', 'текстиль'];
        $details = ['ручная работа', 'качественная строчка', 'удобная стелька',
            'амортизирующая подошва', 'фирменная подкладка'];

        $style = $this->faker->randomElement($styles);
        $material = $this->faker->randomElement($materials);
        $detail = $this->faker->randomElement($details);

        return "Элегантные туфли {$style} стиля из {$material}. {$detail}. " .
            "Идеально подходят для офиса, важных встреч и торжественных мероприятий.";
    }

    /**
     * Создать товар в категории "кроссовки"
     */
    public function sneakers(): static
    {
        $sneakerNames = [
            'Беговые кроссовки с амортизацией',
            'Баскетбольные кроссовки высокой посадки',
            'Повседневные спортивные кроссовки',
            'Тренировочные кроссовки для фитнеса',
            'Ультралегкие кроссовки для бега',
            'Стильные кроссовки для улицы',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($sneakerNames),
            'description' => $this->generateSneakersDescription(),
            'price' => $this->faker->randomFloat(2, 2000, 15000),
        ]);
    }

    /**
     * Описание для кроссовок
     */
    private function generateSneakersDescription(): string
    {
        $technologies = ['амортизирующая', 'дышащая', 'энергвозвращающая', 'стабилизирующая'];
        $activities = ['для бега', 'для тренировок в зале', 'для баскетбола',
            'для повседневной носки', 'для активного отдыха'];
        $features = ['легкий вес', 'хорошая вентиляция', 'усиленная поддержка стопы',
            'износостойкая подошва', 'ортопедическая стелька'];

        $tech = $this->faker->randomElement($technologies);
        $activity = $this->faker->randomElement($activities);
        $feature = $this->faker->randomElement($features);

        return "Современные кроссовки с {$tech} технологией, предназначенные {$activity}. " .
            "{$feature}. Обеспечивают комфорт и безопасность во время занятий спортом.";
    }

    /**
     * Указать конкретную цену
     */
    public function withPrice(float $min, float $max): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, $min, $max),
        ]);
    }

    /**
     * Указать конкретную категорию
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Создать товары распродажи (со скидкой)
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $attributes['price'] * 0.7, // 30% скидка
        ]);
    }

    /**
     * Создать премиум товары
     */
    public function premium(): static
    {
        $premiumBrands = ['Gucci', 'Prada', 'Louis Vuitton', 'Balenciaga', 'Dior', 'Saint Laurent'];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($premiumBrands) . ' ' . $attributes['name'],
            'price' => $this->faker->randomFloat(2, 15000, 100000),
            'description' => 'Премиум качество, дизайнерская коллекция. ' . $attributes['description'],
        ]);
    }
}
