<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponses;
use Core\App;
use Core\Request;

class UserRegisterController
{
    use ApiResponses;

    public function store()
    {
        $request = App::resolve(Request::class);

        (new User)->store($request->json());

        $this->ok('user registered successfully');
    }
}
