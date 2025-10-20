<?php

//return [
//    'connection' => $_ENV['DB_CONNECTION'] ?? 'mysql', // o 'sqlite'
//    'host'       => $_ENV['DB_HOST'] ?? '127.0.0.1',
//    'port'       => $_ENV['DB_PORT'] ?? '3306',
//    'database'   => $_ENV['DB_DATABASE'] ?? 'taskflow',
//    'username'   => $_ENV['DB_USERNAME'] ?? 'root',
//    'password'   => $_ENV['DB_PASSWORD'] ?? '',
//    'charset'    => 'utf8mb4',
//];

return (function () {
    $env = $_ENV['APP_ENV'] ?? 'production';

    if ($env === 'testing') {
        $path = __DIR__ . '/../tests/database.sqlite';
        return [
            'driver' => 'sqlite',
            'dsn' => "sqlite:$path",
            'username' => null,
            'password' => null,
        ];
    }

    return [
        'driver' => $_ENV['DB_CONNECTION'] ?? 'mysql',
        'dsn' => sprintf(
            '%s:host=%s;dbname=%s;port=%s;charset=%s',
            $_ENV['DB_CONNECTION'] ?? 'mysql',
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            $_ENV['DB_DATABASE'] ?? 'taskflow',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        ),
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ];
})();
