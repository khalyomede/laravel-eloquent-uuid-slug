# Laravel Eloquent UUID slug

## Summary

- [About](#about)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Examples](#examples)
- [Compatibility table](#compatibility-table)
- [Alternatives](#alternatives)
- [Tests](#tests)

## About

By default, when getting a model from a controller using [Route Model Binding](https://laravel.com/docs/8.x/routing#route-model-binding), Laravel will try to find a model using the parameter in your route, and associate it to the default identifier of the related table (most of the time, this is the "id" key).

```php
// routes/web.php

use App\Models\Cart;
use Illuminate\Support\Facades\Route;

// --> What you see
Route::get("/cart/{cart}", function(Cart $cart) {
  // $cart ready to be used
});

// --> What happens behind the scene
Route::get("/cart/{cart}", function(string $identifier) {
  $cart = Cart::findOrFail($identifier);

  // $cart ready to be used
});
```

This means if you offer the possibility to view your cart, you will expose the route _/cart/12_ for example. This is not ideal in terms of security because you now expose your cart database identifier, and if you forgot or made a mistake into your cart's [policy](https://laravel.com/docs/8.x/authorization#creating-policies), a malicious user can access the cart of other users (_/cart/41_).

In this context UUID are very useful because:
- They offer a good way to create random, hard to predict identifiers
- Can be manually generated [from the code](https://github.com/ramsey/uuid)
- Are [not likely](https://en.wikipedia.org/wiki/Universally_unique_identifier#Collisions) to collide

The best scenarios would be to expose this uuid instead of your database auto incremented identifier, like _/cart/e22b86bcb8e24cfea13856a0766bfef2_.

The goal of this package is to simplify at best this task for you.

## Features

- Provide a trait to configure your Route Model Binding to use a slug column
- Provide an helper to create the slug column on your migration, according to your configuration
- Provide a scope to find your model by the slug column
- Allow you to customize the name of the slug column

## Requirements

- This package relies on these methods **on your model**, and if you override them the logic might not be guarranteed to keep working:
  - [`public function getRouteKeyName()`](https://laravel.com/docs/8.x/routing#customizing-the-default-key-name)

## Installation

- [1. Install the package](#1-install-the-package)
- [2. Setup your model](#2-setup-your-model)
- [3. Add the slug column in your migration](#3-add-the-slug-column-in-your-migration)

### 1. Install the package

```bash
composer require khalyomede/laravel-eloquent-uuid-slug
```

### 2. Setup your model

On the model of your choice, use the `Sluggable` trait.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;

class Cart extends Model
{
  use Sluggable;
}
```

### 3. Add the slug column in your migration

The `Sluggable` trait offers the method `Sluggable::addSlugColumn()` to make this step a breeze for you.

```php
use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateCartsTable extends Migration
{
  public function up(): void
  {
    Schema::create('carts', function (Blueprint $table): void {
      $table->id();
      $table->string('name');

      Cart::addSlugColumn($table);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::drop('carts');
  }
}
```

If you do not want the `Sluggable::addSlugColumn(Blueprint)` to add SQL constraints (unique index and non nullable column), use its counterpart method `Sluggable::addUnconstrainedSlugColumn(Blueprint)`.

```php
use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateCartsTable extends Migration
{
  public function up(): void
  {
    Schema::create('carts', function (Blueprint $table): void {
      $table->id();
      $table->string('name');

      Cart::addUnconstrainedSlugColumn($table);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::drop('carts');
  }
}
```

## Examples

- [1. Configure the slug column name](#1-configure-the-slug-column-name)
- [2. Use dashes for the generated UUID](#2-use-dashes-for-the-generated-uuid)
- [3. Custom route model binding for specific routes](#3-custom-route-model-binding-for-specific-routes)
- [4. Customize the slug column in your migration](#4-customize-the-slug-column-in-your-migration)
- [5. Retreive a model by its slug](#5-retreive-a-model-by-its-slug)
- [6. Dropping the slug column](#6-dropping-the-slug-column)
- [7. Validate a value exists by slug](#7-validate-a-value-exists-by-slug)

### 1. Configure the slug column name

By default the `Sluggable` trait will assume the name of the slug column is `slug`. Here is how to provide one that you prefer.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;

class Cart extends Model
{
  use Sluggable;

  public function slugColumn(): string
  {
    return 'code';
  }
}
```

### 2. Use dashes for the generated UUID

By default, the `Sluggable` trait will configure the UUID generator to remove dashes, to help make shorter URLs. If you prefer to keep them, here is how you can do it.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;

class Cart extends Model
{
  use Sluggable;

  public function slugWithDashes(): bool
  {
    return true;
  }
}
```

### 3. Custom route model binding for specific routes

By default, all your models that use the `Sluggable` trait will retreive their model using the slug column when performing [Route Model Binding](https://laravel.com/docs/8.x/routing#route-model-binding).

If you would like to bypass it for specific routes, you can [customize the column used to retreive your model](https://laravel.com/docs/8.x/routing#customizing-the-default-key-name) occasionally.

For example, this is how to retreive your Cart model using its id for a specific route.

```php
// routes/web.php

use App\Models\Cart;
use Illuminate\Support\Facades\Route;

// --> What you see
Route::get("/cart/{cart:id}", function(Cart $cart) {
  // $cart ready to be used
});
```

As a **final resort**, if this method does not work, you can always fallback to get the raw data from your route, and perform fetching your model yourself:

```php
// routes/web.php

use App\Models\Cart;
use Illuminate\Support\Facades\Route;

// --> What you see
Route::get("/cart/{cart}", function(string $identifier) {
  $cart = Cart::findOrFail($identifier);

  // $cart ready to be used
});
```

### 4. Customize the slug column in your migration

You can use all the [available column modifiers](https://laravel.com/docs/8.x/migrations#column-modifiers) right after calling the method `Sluggable::addSlugColumn()`, to re-order the column or add some comments for example.

```php
use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateCartsTable extends Migration
{
  public function up(): void
  {
    Schema::create('carts', function (Blueprint $table): void {
      $table->id();
      $table->string('name');

      Cart::addSlugColumn($table)
        ->after('name')
        ->comment('Auto-generated by a package.');

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::drop('carts');
  }
}
```

### 5. Retreive a model by its slug

To help you manually fetching a model by its slug, you can use the `Sluggable::scopeWithSlug()` scope to do it. It follows your configuration, so no matter how you named your slug column it will still work.

```php
// routes/web.php

use App\Models\Cart;
use Illuminate\Support\Facades\Route;

Route::get("/cart/{cart}", function(string $identifier) {
  $cart = Cart::withSlug($identifier)->firstOrFail();

  // $cart ready to be used
});
```

The `Sluggable::findBySlug()`, `Sluggable::findBySlugOrFail()`, `Sluggable::firstBySlug()` or `Sluggable::firstBySlugOrFail()` methods also exist as a shorthand:

```php
// routes/web.php

use App\Models\Cart;
use Illuminate\Support\Facades\Route;

Route::get("/cart/{cart}", function(string $identifier) {
  $cart = Cart::findBySlugOrFail($identifier);
  $cart = Cart::findBySlug($identifier);
  $cart = Cart::where("id", ">=", 1)->firstBySlug($identifier);
  $cart = Cart::where("id", ">=", 1)->firstBySlugOrFail($identifier);

  // $cart ready to be used
});
```

### 6. Dropping the slug column

You can use `Sluggable::dropSlugColumn(Blueprint)` when you want to drop only the slug column on an existing table. Please follow the complete instructions on the "down" method since there is some gotchas to deal with.

```php
use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class DropSlugColumnOnCartsTable extends Migration
{
  public function up(): void
  {
    Schema::create('carts', function (Blueprint $table): void {
      Cart::dropSlugColumn($table);
    });
  }

  public function down(): void
  {
    Schema::table('posts', function (Blueprint $table): void {
      Cart::addUnconstrainedSlugColumn($table);
    });

    Schema::table('posts', function (Blueprint $table): void {
      Cart::fillEmptySlugs();
      Cart::constrainSlugColumn($table);
    });
  }
}
```

### 7. Validate a value exists by slug

You can validate a model exists by the slug column you defined. This is equivalent to calling the existing "exists" rule:

```php
"post_id" => "exists:posts,slug"
```

But without having to manually specify the slug column (it is fetched automatically according to wether you customized the name or not).

```php
// app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Khalyomede\EloquentUuidSlug\Rules\ExistsBySlug;

class PostController extends Controller
{
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "post_id" => ["required", new ExistsBySlug(Post::class)],
    ]);

    // ...
  }
}
```

## Compatibility table

The table below shows the compatibility across Laravel, PHP and this package **current version**. For the compatibility regarding this package previous version, please browse another tag.

| Laravel version | PHP version | Compatible |
|-----------------|-------------|------------|
| 10.*            | 8.2.*       | ✅         |
| 10.*            | 8.1.*       | ❌         |
| 9.*             | 8.2.*       | ❌         |
| 9.*             | 8.1.*       | ❌         |
| 9.*             | 8.0.*       | ❌         |
| 8.*             | 8.2.*       | ❌         |
| 8.*             | 8.1.*       | ❌         |
| 8.*             | 8.0.*       | ❌         |
| 8.*             | 7.4.*       | ❌         |
| 8.*             | 7.3.*       | ❌         |
| 7.x             | *           | ❌         |

To counter-check these results, you can use the Docker containers (see _docker-compose.yml_ file) to run the tests described in the [Tests](#tests) section.

## Alternatives

I created this package mostly to practice creating a tested laravel package, and toying with my first Github Workflow. There is some [others high quality packages](https://packagist.org/?query=laravel%20uuid) out there so make sure to take a look at them!

## Tests

```bash
composer run test
composer run analyse
composer run check
composer run lint
composer run scan
composer run updates
```

Or

```bash
composer run all
```
