<?php

function basePath($path): string
{
    return BASE_PATH . $path;
}

function notEmpty($value): bool
{
    return !empty($value);
}

/**
 * Reads a configuration value from the environment.
 *
 * The real environment wins over .env: phpdotenv is created immutable and only
 * writes to $_ENV/$_SERVER (never putenv), so a variable exported by the shell
 * -- how the test suite points the server at the test database -- is never
 * overwritten by the .env file. getenv() is checked first because php.ini's
 * variables_order does not include "E" here, leaving $_ENV empty for real
 * environment variables.
 */
function env(string $key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    return $value;
}
