<?php

namespace App\Http\Requests;

use App\Traits\ApiResponses;

class TaskStoreRequest
{
    use ApiResponses;

    private static array $errors = [];

    private static function rules (): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string|max:200',
            'expiration_date' => 'required|date',
        ];
    }

    public static function validate(array $request): array
    {
        foreach (self::rules() as $field => $rule) {
            $value = $request[$field];

            if (str_contains($rule, 'required') && empty($value)) {
                self::$errors[$field][] = "The field is required";
            }

            if (str_contains($rule, 'string') && notEmpty($value) && !is_string($value)) {
                self::$errors[$field][] = 'The field must be a string';
            }

            if (str_contains($rule, 'email') && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                self::$errors[$field][] = 'Invalid email';
            }

            if (str_contains($rule, 'max:200') && strlen($value) >= 200) {
                self::$errors[$field][] = 'Must be max 200 characters';
            }

            if (str_contains($rule, 'date') && notEmpty($value) && !preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                self::$errors[$field][] = 'Must be a valid date';
            }
        }

        if (notEmpty(self::$errors)) {
            (new static)->unprocessableEntity(self::$errors);
        }

        return $request;
    }
}
