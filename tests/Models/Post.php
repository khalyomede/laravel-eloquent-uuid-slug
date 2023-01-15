<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Khalyomede\EloquentUuidSlug\Sluggable;
use Tests\Database\Factories\PostFactory;

final class Post extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = [];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}
