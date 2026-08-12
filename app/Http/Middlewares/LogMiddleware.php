<?php

namespace App\Http\Middlewares;

use Core\Contracts\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Traits\ApiResponses;

class LogMiddleware implements MiddlewareInterface
{
    use ApiResponses;

    public function handle(Request $request, callable $next): Response
    {
        return $next($request);
    }
}
