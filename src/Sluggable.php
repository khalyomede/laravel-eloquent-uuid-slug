<?php

namespace Khalyomede\EloquentUuidSlug;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Fluent;
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

    /**
     * Overriding Laravel's default.
     *
     * @param array<string>|null $except
     */
    public function replicate(array $except = null)
    {
        return parent::replicate(array_merge($except ?? [], [$this->slugColumn()]));
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::withSlug($slug)->first();
    }

    public static function findBySlugOrFail(string $slug): self
    {
        return self::withSlug($slug)->firstOrFail();
    }

    public static function addSlugColumn(Blueprint $table): ColumnDefinition
    {
        $instance = new static();

        return $table->uuid($instance->slugColumn())
            ->unique();
    }

    public static function addUnconstrainedSlugColumn(Blueprint $table): ColumnDefinition
    {
        $instance = new static();
        $column = $instance->slugColumn();

        return $table->uuid($column)
            ->nullable();
    }

    public static function constrainSlugColumn(BluePrint $table): ColumnDefinition
    {
        $instance = new static();

        return $table->uuid($instance->slugColumn())
            ->unique()
            ->change();
    }

    /**
     * @return Fluent<int, ColumnDefinition>
     */
    public static function dropSlugIndex(Blueprint $table): Fluent
    {
        $instance = new static();

        return $table->dropUnique([$instance->slugColumn()]);
    }

    /**
     * @return Fluent<int, ColumnDefinition>
     */
    public static function dropSlugColumn(Blueprint $table): Fluent
    {
        $instance = new static();

        // $table->dropUnique([$instance->slugColumn()]);

        return $table->dropColumn($instance->slugColumn());
    }

    public static function fillEmptySlugs(): void
    {
        $instance = new static();
        $column = $instance->slugColumn();

        /** @phpstan-ignore-next-line Models without SoftDeletes trait will raise an issue. */
        $query = $instance instanceof SoftDeletes
            ? $instance->withTrashed()
            : $instance->query();

        $query->whereNull($column)
            ->get()
            ->each(function (Model $model) use ($instance, $column): void {
                /** @phpstan-ignore-next-line */
                $model->{$column} = $instance->getNewSlug();
                $model->saveQuietly();
            });
    }

    protected static function bootSluggable(): void
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
