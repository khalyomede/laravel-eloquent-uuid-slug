<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;
use Tests\Database\Factories\CartFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 */
final class Cart extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = [];

    protected function slugWithDashes(): bool
    {
        return true;
    }

    protected function slugColumn(): string
    {
        return 'code';
    }

    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }
}
