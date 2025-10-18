<?php

namespace Core;

use App\Exceptions\RouterException;

class Router {
    protected array $routes = [];

    public function get($uri, $controller): Router|static
    {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller)
    {
        return $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller)
    {
        return $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller)
    {
        return $this->add('PUT', $uri, $controller);
    }

    public function add($method, $uri, $controller): static
    {
        $this->routes[] = compact('method', 'uri', 'controller');

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function route(string $uri, string $method): mixed
    {
        $route = $this->findRoute($uri, strtoupper($method));

        if (!$route) {
            throw new RouterException("Route not found for [$method] $uri", Response::NOT_FOUND);
        }

        return $this->dispatch($route['controller'], $route['params'] ?? []);
    }

    protected function findRoute(string $uri, string $method): ?array
    {
        foreach ($this->routes as $route) {
            if ($method !== $route['method']) {
                continue;
            }

            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $route['params'] = $matches;
                return $route;
            }
        }

        return null;
    }

    protected function dispatch(callable|array $controller, array $params = []): mixed
    {
        [$class, $method] = $controller;

        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new RouterException("Controller or method not found", Response::INTERNAL_SERVER_ERROR);
        }

        try {
            $instance = App::resolve($class);
        } catch (\Exception $exception) {
            $instance = new $class();
        }

        return call_user_func_array([$instance, $method], $params);
    }
}
