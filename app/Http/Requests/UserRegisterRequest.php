<?php

namespace App\Http\Requests;

use Core\FormRequest;

class UserRegisterRequest extends FormRequest
{
    protected static function rules (): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:7',
        ];
    }
}
