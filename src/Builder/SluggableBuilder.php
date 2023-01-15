<?php

namespace Khalyomede\EloquentUuidSlug\Builder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Builder<Model>
 */
final class SluggableBuilder extends Builder
{
    public function withSlug(string $slug): self
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        $this->where($this->model->slugColumn(), $slug);

        return $this;
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->firstBySlug($slug);
    }

    public function findBySlugOrFail(string $slug): Model
    {
        return $this->firstBySlugOrFail($slug);
    }

    public function firstBySlug(string $slug): ?Model
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        return $this->where($this->model->slugColumn(), $slug)->first();
    }

    public function firstBySlugOrFail(string $slug): Model
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        return $this->where($this->model->slugColumn(), $slug)->firstOrFail();
    }
}
