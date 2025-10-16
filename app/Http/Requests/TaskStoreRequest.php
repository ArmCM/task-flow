<?php

namespace App\Http\Requests;

use Core\FormRequest;

class TaskStoreRequest extends FormRequest
{
    protected static function rules (): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string|max:200',
            'expiration_date' => 'required|date',
        ];
    }
}
