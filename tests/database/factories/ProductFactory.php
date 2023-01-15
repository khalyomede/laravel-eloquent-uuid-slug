<?php

namespace Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Models\Product;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array{name: string}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
