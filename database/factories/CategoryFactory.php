<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    /**
     * Создать категорию "сапоги"
     */
    public function boots(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'сапоги',
            'slug' => 'boots',
        ]);
    }

    /**
     * Создать категорию "туфли"
     */
    public function shoes(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'туфли',
            'slug' => 'shoes',
        ]);
    }

    /**
     * Создать категорию "кроссовки"
     */
    public function sneakers(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'кроссовки',
            'slug' => 'sneakers',
        ]);
    }

    /**
     * Создать случайную категорию обуви
     */
    public function shoeCategory(): static
    {
        $categories = [
            ['name' => 'ботинки', 'slug' => 'boots'],
            ['name' => 'туфли', 'slug' => 'shoes'],
            ['name' => 'кроссовки', 'slug' => 'sneakers'],
            ['name' => 'сандалии', 'slug' => 'sandals'],
            ['name' => 'шлепанцы', 'slug' => 'flipflops'],
            ['name' => 'кеды', 'slug' => 'sneakers'],
            ['name' => 'лоферы', 'slug' => 'loafers'],
            ['name' => 'оксфорды', 'slug' => 'oxfords'],
            ['name' => 'мокасины', 'slug' => 'moccasins'],
            ['name' => 'балетки', 'slug' => 'ballet-flats'],
        ];

        $category = $this->faker->randomElement($categories);

        return $this->state(fn (array $attributes) => [
            'name' => $category['name'],
            'slug' => $category['slug'],
        ]);
    }
}
