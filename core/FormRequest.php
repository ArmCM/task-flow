<?php

namespace Core;

use Core\Traits\ApiResponses;

abstract class FormRequest
{
    use ApiResponses;

    abstract protected static function rules(): array;

    public static function validate(array $request): array
    {
        $validationRules = new ValidationRules($request, static::rules());

        if (!$validationRules->verify()) {
            (new static)->unprocessableEntity($validationRules->errors());
        }

        return $validationRules->validated();
    }
}
