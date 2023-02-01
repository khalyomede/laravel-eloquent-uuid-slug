<?php

namespace Khalyomede\EloquentUuidSlug\Builder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model
 *
 * @extends Builder<TModelClass>
 */
final class SluggableBuilder extends Builder
{
    /**
     * @return self<TModelClass>
     */
    public function withSlug(string $slug): self
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        $this->where($this->model->slugColumn(), $slug);

        return $this;
    }

    /**
     * @return ?TModelClass
     */
    public function findBySlug(string $slug)
    {
        return $this->firstBySlug($slug);
    }

    /**
     * @return TModelClass
     */
    public function findBySlugOrFail(string $slug)
    {
        return $this->firstBySlugOrFail($slug);
    }

    /**
     * @return ?TModelClass
     */
    public function firstBySlug(string $slug)
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        return $this->where($this->model->slugColumn(), $slug)->first();
    }

    /**
     * @return TModelClass
     */
    public function firstBySlugOrFail(string $slug)
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::slugColumn(). */
        return $this->where($this->model->slugColumn(), $slug)->firstOrFail();
    }
}
