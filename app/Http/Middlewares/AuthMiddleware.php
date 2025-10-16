<?php

namespace App\Http\Middlewares;

use Core\Contracts\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Traits\ApiResponses;

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

        $token = $request->authorizationHeader();

        if ($token !== 'Bearer secret123') {
            $this->unauthorized('not authorized');
        }

        return $next($request);
    }
}
