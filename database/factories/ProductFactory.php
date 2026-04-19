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
        $brands = [
            'Michael Kors', 'Guess', 'Furla', 'Lacoste', 'Coach', 'Aldo',
            'Mango', 'Zara', 'Tommy Hilfiger', 'Calvin Klein', 'Herschel',
            'Nike', 'Adidas', 'Puma', 'Samsonite'
        ];

        $bagTypes = [
            'Сумка', 'Рюкзак', 'Клатч', 'Шоппер', 'Сумка через плечо',
            'Дорожная сумка', 'Мини-сумка', 'Городской рюкзак'
        ];

        $modelNames = [
            'Classic', 'Urban', 'Premium', 'Daily', 'Elegant',
            'Travel', 'Soft', 'Modern', 'Signature', 'Style'
        ];

        $brand = $this->faker->randomElement($brands);
        $type = $this->faker->randomElement($bagTypes);
        $model = $this->faker->randomElement($modelNames);

        $name = "{$brand} {$type} {$model}";

        $category = Category::inRandomOrder()->first() ?? Category::factory();

        return [
            'name' => $name,
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->randomFloat(2, 2000, 30000),
            'image' => $this->generateProductImage(),
            'category_id' => $category instanceof Category ? $category->id : $category,
            'sku' => strtoupper('BAG-' . Str::random(8)),
            'stock' => $this->faker->numberBetween(0, 50),
            'status' => defined(Product::class . '::STATUS_ACTIVE')
                ? Product::STATUS_ACTIVE
                : 'active',
        ];
    }

    /**
     * Генерация URL изображения продукта
     */
    private function generateProductImage(): string
    {
        $images = [
            'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1591561954557-26941169b49e?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1559563458-527698bf5295?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?w=640&h=480&fit=crop',
        ];

        return $this->faker->randomElement($images);
    }

    /**
     * Создать товар в категории "сумки"
     */
    public function bags(): static
    {
        $names = [
            'Кожаная сумка',
            'Повседневная сумка',
            'Сумка через плечо',
            'Шоппер',
            'Деловая сумка',
            'Мини-сумка',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($names) . ' ' . $this->faker->randomElement(['Classic', 'Urban', 'Premium']),
            'description' => $this->generateBagsDescription(),
            'price' => $this->faker->randomFloat(2, 3000, 20000),
            'sku' => strtoupper('BAG-' . Str::random(8)),
        ]);
    }

    private function generateBagsDescription(): string
    {
        $materials = ['экокожи', 'натуральной кожи', 'текстиля', 'замши'];
        $features = ['вместительная', 'стильная', 'практичная', 'легкая', 'удобная'];
        $uses = ['для офиса', 'для города', 'для прогулок', 'для ежедневного использования'];

        $material = $this->faker->randomElement($materials);
        $feature = $this->faker->randomElement($features);
        $use = $this->faker->randomElement($uses);

        return "Качественная сумка из {$material}. {$feature}, отлично подходит {$use}. Удобные ручки, надежная фурнитура и современный дизайн.";
    }

    /**
     * Создать товар в категории "рюкзаки"
     */
    public function backpacks(): static
    {
        $names = [
            'Городской рюкзак',
            'Спортивный рюкзак',
            'Туристический рюкзак',
            'Повседневный рюкзак',
            'Рюкзак для учебы',
            'Компактный рюкзак',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($names) . ' ' . $this->faker->randomElement(['Pro', 'Travel', 'Classic']),
            'description' => $this->generateBackpacksDescription(),
            'price' => $this->faker->randomFloat(2, 3500, 25000),
            'sku' => strtoupper('BACKPACK-' . Str::random(6)),
        ]);
    }

    private function generateBackpacksDescription(): string
    {
        $materials = ['полиэстера', 'нейлона', 'экокожи', 'водоотталкивающей ткани'];
        $features = ['эргономичная спинка', 'мягкие лямки', 'вместительное отделение', 'множество карманов'];
        $uses = ['для города', 'для путешествий', 'для учебы', 'для тренировок'];

        $material = $this->faker->randomElement($materials);
        $feature = $this->faker->randomElement($features);
        $use = $this->faker->randomElement($uses);

        return "Удобный рюкзак из {$material}. {$feature}, отлично подходит {$use}. Практичный дизайн и комфорт при ежедневном использовании.";
    }

    /**
     * Создать товар в категории "клатчи"
     */
    public function clutches(): static
    {
        $names = [
            'Вечерний клатч',
            'Клатч-конверт',
            'Элегантный клатч',
            'Мини-клатч',
            'Праздничный клатч',
            'Классический клатч',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($names) . ' ' . $this->faker->randomElement(['Elegant', 'Shine', 'Signature']),
            'description' => $this->generateClutchesDescription(),
            'price' => $this->faker->randomFloat(2, 2500, 12000),
            'sku' => strtoupper('CLUTCH-' . Str::random(6)),
        ]);
    }

    private function generateClutchesDescription(): string
    {
        $materials = ['экокожи', 'лакированного материала', 'замши', 'текстиля'];
        $features = ['компактный размер', 'стильная отделка', 'удобный ремешок', 'элегантная форма'];
        $uses = ['для вечерних мероприятий', 'для праздников', 'для свиданий', 'для торжественных выходов'];

        $material = $this->faker->randomElement($materials);
        $feature = $this->faker->randomElement($features);
        $use = $this->faker->randomElement($uses);

        return "Элегантный клатч из {$material}. {$feature}, прекрасно подходит {$use}. Подчеркивает образ и вмещает все необходимое.";
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
     * Создать товары распродажи
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => round($attributes['price'] * 0.7, 2),
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
            'sku' => strtoupper('PREMIUM-' . Str::random(6)),
        ]);
    }
}
