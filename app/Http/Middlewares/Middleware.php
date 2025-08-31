<?php

namespace App\Http\Middlewares;

use App\Contracts\MiddlewareInterface;
use Core\Request;
use Core\Response;

class Middleware
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function process(Request $request, callable $finalHandler): Response
    {
        $dispatcher = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($middleware, $next) {
                    return $middleware->handle($request, $next);
                };
            },
            $finalHandler
        );

        return $dispatcher($request);
    }
}
