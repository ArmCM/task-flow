<?php

namespace App\Http\Controllers;

use App\Models\User;
use Core\App;
use Core\Request;
use Core\Traits\ApiResponses;

class LoginController
{
    use ApiResponses;

    public function index()
    {
        $request = (App::resolve(Request::class))->json();

        $user = (new User)->find($request['email'], $request['password']);

        $this->ok('find resource', $user);
    }
}
