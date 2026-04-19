<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }

    /**
     * Сумки
     */
    public function bags(): static
    {
        return $this->state(fn () => [
            'name' => 'Сумки',
            'slug' => 'bags',
        ]);
    }

    /**
     * Рюкзаки
     */
    public function backpacks(): static
    {
        return $this->state(fn () => [
            'name' => 'Рюкзаки',
            'slug' => 'backpacks',
        ]);
    }

    /**
     * Клатчи
     */
    public function clutches(): static
    {
        return $this->state(fn () => [
            'name' => 'Клатчи',
            'slug' => 'clutches',
        ]);
    }
}
