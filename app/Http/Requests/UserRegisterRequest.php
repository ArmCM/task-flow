<?php

namespace App\Http\Requests;

use App\Traits\ApiResponses;

class UserRegisterRequest
{
    use ApiResponses;

    private static array $errors = [];

    private static function rules (): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:7',
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

            if (str_contains($rule, 'min:7') && strlen($value) < 7) {
                self::$errors[$field][] = 'Must be at least 7 characters';
            }
        }

        if (notEmpty(self::$errors)) {
            (new static)->unprocessableEntity(self::$errors);
        }

        return $request;
    }
}
