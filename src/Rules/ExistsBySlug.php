<?php

namespace Khalyomede\EloquentUuidSlug\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Khalyomede\EloquentUuidSlug\Sluggable;
use ReflectionClass;

final class ExistsBySlug implements Rule
{
    /**
     * @param class-string<Model> $model
     */
    public function __construct(protected string $model)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $reflection = new ReflectionClass($this->model);
        $model = $reflection->newInstance();

        assert($model instanceof Model);
        /** @phpstan-ignore-next-line Call to function assert() with false will always evaluate to false. */
        assert($model instanceof Sluggable);

        return $model->withSlug($value)->exists();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return strval(__('validation.exists'));
    }
}
