<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Core\App;
use Core\Request;
use Core\Traits\ApiResponses;

class UserRegisterController
{
    use ApiResponses;

    public function store()
    {
        $request = App::resolve(Request::class);

        $validatedRequest = UserRegisterRequest::validate($request->json());

        (new User)->store($validatedRequest);

        $this->ok('user registered successfully');
    }
}
