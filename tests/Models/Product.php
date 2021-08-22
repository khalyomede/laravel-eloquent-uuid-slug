<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;
use Tests\Database\Factories\ProductFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
final class Product extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = [];

    protected function slugWithDashes(): bool
    {
        return true;
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
