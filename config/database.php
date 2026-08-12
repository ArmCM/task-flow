<?php

$driver = env('DB_CONNECTION', 'mysql');

$database = env('APP_ENV') === 'testing'
    ? env('DB_TEST_DATABASE', 'taskflow_test')
    : env('DB_DATABASE', 'taskflow');

return [
    'dsn' => sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $driver,
        env('DB_HOST', '127.0.0.1'),
        env('DB_PORT', '3306'),
        $database,
        env('DB_CHARSET', 'utf8mb4')
    ),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'database' => $database,
];
