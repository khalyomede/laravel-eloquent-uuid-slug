<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;
use Tests\Models\Cart;
use Tests\Models\Post;
use Tests\Models\Product;
use Tests\Models\Rating;
use Tests\TestCase;

final class SluggableTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testSlugAutoFilledWhenCreatingModel(): void
    {
        $name = $this->faker->name();

        $product = Product::create(['name' => $name]);
        $product = $product->refresh();

        assert($product instanceof Product);

        $this->assertEquals($name, $product->name);
        $this->assertTrue(Uuid::isValid($product->slug));
    }

    public function testItDoesNotOverrideExistingSlug(): void
    {
        $name = $this->faker->name();
        $slug = $this->faker->slug();

        $product = Product::create(['slug' => $slug, 'name' => $name]);
        $product = $product->refresh();

        assert($product instanceof Product);

        $this->assertEquals($slug, $product->slug);
        $this->assertEquals($name, $product->name);
    }

    public function testItDoesNotOverrideExistingSlugWithCustomName(): void
    {
        $name = $this->faker->name();
        $code = $this->faker->slug();

        $cart = Cart::create(['code' => $code, 'name' => $name]);
        $cart = $cart->refresh();

        assert($cart instanceof Cart);

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
        Route::get('/product/{product}', fn (Product $product): HttpResponse => Response::make($product->name, 200, ['Content-Type' => 'text/plain']))
            ->middleware(SubstituteBindings::class);

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
        Route::get('/cart/{cart}', fn (Cart $cart): HttpResponse => Response::make($cart->name, 200, ['Content-Type' => 'text/plain']))
            ->middleware(SubstituteBindings::class);

        $this->get("/cart/{$cart->code}")
            ->assertOk()
            ->assertSee($cart->name);
    }

    public function testItCanGetModelBySlugUsingScope(): void
    {
        $product = Product::factory()
            ->create();

        assert($product instanceof Product);

        $found = Product::withSlug($product->slug)->firstOrFail();

        assert($found instanceof Product);

        $notFound = Product::withSlug($this->faker->slug())->first();

        $this->assertEquals($product->id, $found->id);
        $this->assertNull($notFound);
    }

    public function testItCanGetModelBySlugUsingScopeWithCustomSlugColumnName(): void
    {
        $cart = Cart::factory()
            ->create();

        assert($cart instanceof Cart);

        $found = Cart::withSlug($cart->code)->firstOrFail();

        assert($found instanceof Cart);

        $notFound = Cart::withSlug($this->faker->slug())->first();

        $this->assertEquals($cart->id, $found->id);
        $this->assertNull($notFound);
    }

    public function testItCanFindModelBySlug(): void
    {
        $cart = Cart::factory()
            ->create();

        self::assertNull(Cart::findBySlug($this->faker->slug()));
        self::assertInstanceOf(Cart::class, Cart::findBySlug($cart->code));
    }

    public function testItCanFindModelBySlugOrThrowsException(): void
    {
        $code = $this->faker->slug();

        Cart::factory()
            ->create([
                "code" => $code,
            ]);

        $cart = Cart::findBySlugOrFail($code);

        assert($cart instanceof Cart);

        self::assertEquals($code, $cart->code);

        $this->expectException(ModelNotFoundException::class);

        Cart::findBySlugOrFail($this->faker->slug());
    }

    public function testCanReplicateSluggable(): void
    {
        $cart = Cart::factory()
            ->create();

        $newCart = $cart->replicate();
        $newCart->save();

        self::assertNotEquals($cart->id, $newCart->id);
        self::assertNotEquals($cart->code, $newCart->code);
        self::assertEquals($cart->name, $newCart->name);
    }

    public function testFirstBySlugFindsModelIfItExistsBySlug(): void
    {
        $product = Product::factory()
            ->create();

        $found = Product::firstBySlugOrFail($product->slug);

        self::assertTrue($found->is($product));
    }

    public function testFirstBySlugFindsModelIfItExistsBySlugEvenWhenSlugColumnHasBeenCustomized(): void
    {
        $cart = Cart::factory()
            ->create();

        $found = Cart::firstBySlugOrFail($cart->code);

        self::assertTrue($found->is($cart));
    }

    public function testChainedFirstBySlugFindsModelIfItExistsBySlug(): void
    {
        $product = Product::factory()
            ->create();

        $found = Product::where("id", ">=", 1)
            ->firstBySlug($product->slug);

        assert($found instanceof Product);

        self::assertTrue($found->is($product));
    }

    public function testChainedFirstBySlugFindsModelIfItExistsBySlugEventWhenSlugColumnHasBeenCustomized(): void
    {
        $cart = Cart::factory()
            ->create();

        $found = Cart::where("id", ">=", 1)
            ->firstBySlug($cart->code);

        assert($found instanceof Cart);

        self::assertTrue($found->is($cart));
    }

    public function testFirstBySlugReturnsNullIfNoModelsMatchSlug(): void
    {
        self::assertNull(Product::firstBySlug($this->faker->uuid()));
    }

    public function testFirstBySlugReturnsNullIfNoModelsMatchCustomizedSlug(): void
    {
        self::assertNull(Cart::firstBySlug($this->faker->uuid()));
    }

    public function testFindBySlugWorksOnHasManyRelationship(): void
    {
        $product = Product::factory()
            ->has(Rating::factory()->count(3))
            ->create();

        $firstRating = $product->ratings->first();

        $found = $product->ratings()->findBySlug($firstRating->slug);

        self::assertTrue($found->is($firstRating));
    }

    public function testFindBySlugReturnsNullOnHasManyRelationshipIfNoModelMatchesTheSlug(): void
    {
        $product = Product::factory()
            ->has(Rating::factory()->count(3))
            ->create();

        self::assertNull($product->ratings()->findBySlug($this->faker->uuid()));
    }

    public function testFindBySlugOrFailWorksOnHasManyRelationship(): void
    {
        $product = Product::factory()
            ->has(Rating::factory()->count(3))
            ->create();

        $firstRating = $product->ratings->first();

        $found = $product->ratings()->findBySlugOrFail($firstRating->slug);

        self::assertTrue($found->is($firstRating));
    }

    public function testFindBySlugOrFailRaisesExceptionOnHasManyRelationshipIfNoModelsMatchSLug(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $product = Product::factory()
            ->has(Rating::factory()->count(3))
            ->create();

        $product->ratings()->findBySlugOrFail($this->faker->uuid());
    }
}
