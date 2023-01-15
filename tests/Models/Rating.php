<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Khalyomede\EloquentUuidSlug\Sluggable;
use Tests\Database\Factories\RatingFactory;

final class Rating extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * @return BelongsTo<Product, self>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory(): RatingFactory
    {
        return RatingFactory::new();
    }
}
