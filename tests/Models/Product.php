<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * @return HasMany<Rating>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    protected function slugWithDashes(): bool
    {
        return true;
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
