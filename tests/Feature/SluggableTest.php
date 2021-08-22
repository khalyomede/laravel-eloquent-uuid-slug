<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;
use Tests\Models\Cart;
use Tests\Models\Product;
use Tests\TestCase;

final class SluggableTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testSlugAutoFilledWhenCreatingModel(): void
    {
        $name = $this->faker->name();

        /**
         * @phpstan-ignore-next-line Cannot find create method.
         */
        $product = Product::create(['name' => $name]);
        $product = $product->refresh();

        $this->assertEquals($name, $product->name);
        $this->assertTrue(Uuid::isValid($product->slug));
    }

    public function testItDoesNotOverrideExistingSlug(): void
    {
        $name = $this->faker->name();
        $slug = $this->faker->slug();

        /**
         * @phpstan-ignore-next-line Cannot find create method.
         */
        $product = Product::create(['slug' => $slug, 'name' => $name]);
        $product = $product->refresh();

        $this->assertEquals($slug, $product->slug);
        $this->assertEquals($name, $product->name);
    }

    public function testItDoesNotOverrideExistingSlugWithCustomName(): void
    {
        $name = $this->faker->name();
        $code = $this->faker->slug();

        /**
         * @phpstan-ignore-next-line Cannot find create method.
         */
        $cart = Cart::create(['code' => $code, 'name' => $name]);
        $cart = $cart->refresh();

        $this->assertEquals($code, $cart->code);
        $this->assertEquals($name, $cart->name);
    }

    public function testItCorrectlyFetchesTheModelUsingCustomRouteResolveBinding(): void
    {
        /**
         * @var Product
         */
        $product = Product::factory()
            ->create();

        // Text plain response to avoid faker generated names that might contain entity encodable character, which would force to decode them down the test.
        Route::get('/product/{product}', fn (Product $product) => Response::make($product->name, 200, ['Content-Type' => 'text/plain']))
            ->middleware("bindings");

        $this->get("/product/{$product->slug}")
            ->assertOk()
            ->assertSee($product->name);
    }

    public function testItCorrectlyFetchesTheModelUsingCustomRouteResolveBindingWithCustomSlugColumnName(): void
    {
        /**
         * @var Cart
         */
        $cart = Cart::factory()
            ->create();

        // Text plain response to avoid faker generated names that might contain entity encodable character, which would force to decode them down the test.
        Route::get('/cart/{cart}', fn (Cart $cart) => Response::make($cart->name, 200, ['Content-Type' => 'text/plain']))
            ->middleware("bindings");

        $this->get("/cart/{$cart->code}")
            ->assertOk()
            ->assertSee($cart->name);
    }

    public function testItCanGetModelBySlugUsingScope(): void
    {
        /**
         * @var Product
         */
        $product = Product::factory()
            ->create();

        /**
         * @phpstan-ignore-next-line Scopes are not well comprehended.
         */
        $found = Product::withSlug($product->slug)->firstOrFail()->id;

        /**
         * @phpstan-ignore-next-line Scopes are not well comprehended.
         */
        $notFound = Product::withSlug($this->faker->slug())->first();

        $this->assertEquals($product->id, $found);
        $this->assertNull($notFound);
    }

    public function testItCanGetModelBySlugUsingScopeWithCustomSlugColumnName(): void
    {
        /**
         * @var Cart
         */
        $cart = Cart::factory()
            ->create();

        /**
         * @phpstan-ignore-next-line Scopes are not well comprehended.
         */
        $found = Cart::withSlug($cart->code)->firstOrFail()->id;

        /**
         * @phpstan-ignore-next-line Scopes are not well comprehended.
         */
        $notFound = Cart::withSlug($this->faker->slug())->first();

        $this->assertEquals($cart->id, $found);
        $this->assertNull($notFound);
    }
}
