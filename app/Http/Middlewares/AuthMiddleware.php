<?php

namespace App\Http\Middlewares;

use App\Auth\Jwt;
use Core\Contracts\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Traits\ApiResponses;
use Exception;

class AuthMiddleware implements MiddlewareInterface
{
    use ApiResponses;

    private array $except = [
        '/login',
        '/register',
    ];

    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->path(), $this->except)) {
            return $next($request);
        }

        $authorization = $request->authorizationHeader();

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            $this->unauthorized('Missing or invalid authorization header');
        }

        $token = str_replace('Bearer ', '', $authorization);

        try {
            $jwt = new Jwt();

            $decoded = $jwt->verifyToken($token);

            $request->setUser((array) $decoded);
        } catch (Exception $exception) {
            $this->unauthorized($exception->getMessage());
        }

        return $next($request);
    }
}
