<?php

namespace App\Http\Middlewares;

use Core\Contracts\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Traits\ApiResponses;

class AuthMiddleware implements MiddlewareInterface
{
    use ApiResponses;

    public function handle(Request $request, callable $next): Response
    {
        $token = $request->authorizationHeader();

        if ($token !== 'Bearer secret123') {
            $this->unauthorized('not authorized');
        }

        return $next($request);
    }
}
