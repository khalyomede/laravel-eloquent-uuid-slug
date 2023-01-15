<?php

namespace Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Models\Cart;

/**
 * @extends Factory<Cart>
 */
final class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * @return array{name: string}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
