<?php

namespace Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Models\Product;
use Tests\Models\Rating;

/**
 * @extends Factory<Rating>
 */
final class RatingFactory extends Factory
{
    protected $model = Rating::class;

    /**
     * @return array{product_id: Factory<Product>, content: string}
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'content' => $this->faker->text(),
        ];
    }
}
