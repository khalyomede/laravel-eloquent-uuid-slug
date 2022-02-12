<?php

namespace Khalyomede\EloquentUuidSlug;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Ramsey\Uuid\Uuid;

trait Sluggable
{
    protected string $slugColumn;
    protected bool $slugWithDashes;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->slugColumn = 'slug';
        $this->slugWithDashes = false;
    }

    public function getRouteKeyName(): string
    {
        return $this->slugColumn();
    }

    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where($this->slugColumn(), $slug);
    }

    public static function addSlugColumn(Blueprint $table): ColumnDefinition
    {
        $instance = new static();

        return $table->uuid($instance->slugColumn())
            ->unique();
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $slugColumn = $model->slugColumn();

            if (!$model->isDirty($slugColumn)) {
                $model->{$slugColumn} = $model->getNewSlug();
            }
        });
    }

    protected function slugColumn(): string
    {
        return $this->slugColumn;
    }

    protected function slugWithDashes(): bool
    {
        return $this->slugWithDashes;
    }

    private function getNewSlug(): string
    {
        $value = Uuid::uuid4();

        if (!$this->slugWithDashes()) {
            $value = $value->getHex();
        }

        return $value->toString();
    }
}
