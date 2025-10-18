<?php

namespace App\Http\Controllers;

use App\Auth\Jwt;
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

        $jwt = new Jwt();

        $token = $jwt->generateToken([
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);

        $this->ok('find resource', [
            'id' => $user['id'],
            'email' => $user['email'],
            'token' => $token,
        ]);
    }
}
