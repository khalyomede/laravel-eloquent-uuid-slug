<?php

namespace Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Models\Post;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array{title: string}
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name(),
        ];
    }
}
