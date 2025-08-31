<?php

namespace App\Http\Middlewares;

use App\Contracts\MiddlewareInterface;
use App\Traits\ApiResponses;
use Core\Response;
use Core\Request;

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
