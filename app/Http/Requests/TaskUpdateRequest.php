<?php

namespace App\Http\Requests;

use Core\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    protected static function rules (): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string|max:200',
            'status' => 'required|string|in:pending,in_progress,completed',
            'expiration_date' => 'required|date',
        ];
    }
}
