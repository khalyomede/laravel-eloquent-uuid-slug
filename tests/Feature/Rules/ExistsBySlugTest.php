<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Khalyomede\EloquentUuidSlug\Rules\ExistsBySlug;
use Tests\Models\Product;
use Tests\TestCase;

final class ExistsBySlugTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testCanValidateModelExistsBySlug(): void
    {
        $post = Product::factory()->create();

        $validator = Validator::make([
            "product_id" => $post->slug,
        ], [
            "product_id" => ["required", new ExistsBySlug(Product::class)],
        ]);

        self::assertFalse($validator->fails());
    }

    public function testCanInvalidateModelThatDoNotExistBySlug(): void
    {
        $validator = Validator::make([
            "product_id" => $this->faker->uuid(),
        ], [
            "product_id" => ["required", new ExistsBySlug(Product::class)],
        ]);

        self::assertTrue($validator->fails());
    }
}
