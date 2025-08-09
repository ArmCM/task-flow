<?php

namespace Core;

use Exception;

class Container
{
    protected array $bindings = [];
    protected array $singletons = [];
    protected array $instances = [];

    public function bind($key, $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    public function singleton($key, $resolver): void
    {
        $this->singletons[$key] = $resolver;
    }

    /**
     * @throws Exception
     */
    public function resolve($key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (isset($this->singletons[$key])) {
            $this->instances[$key] = call_user_func($this->singletons[$key]);

            return $this->instances[$key];
        }

        if (isset($this->bindings[$key])) {
            return call_user_func($this->bindings[$key]);
        }

        throw new Exception("Not matching binding found for $key");
    }
}
