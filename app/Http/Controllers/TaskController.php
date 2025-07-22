<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;

class TaskController
{
    use ApiResponses;

    public function index()
    {
        $this->ok('hello', ['name' => 'armando']);
    }
}
