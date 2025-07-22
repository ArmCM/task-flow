<?php

namespace Core;

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
            throw new \Exception("Route not found for [$method] $uri");
        }

        return $this->dispatch($route['controller']);
    }

    protected function findRoute(string $uri, string $method): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                return $route;
            }
        }

        return null;
    }

    protected function dispatch(callable|array $controller): mixed
    {
        if (is_array($controller)) {
            [$class, $method] = $controller;
            $instance = new $class;

            return $instance->$method();
        }

        return call_user_func($controller);
    }
}
