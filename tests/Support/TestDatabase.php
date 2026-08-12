<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use RuntimeException;

/**
 * Owns the test database: creates it, loads database/schema.sql and empties the
 * tables between tests. Reuses config/database.php so the suite and the server
 * process can never disagree about which database they are talking to.
 */
final class TestDatabase
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::connect();
        }

        return self::$connection;
    }

    public static function truncate(): void
    {
        $connection = self::connection();

        $connection->exec('SET FOREIGN_KEY_CHECKS = 0');
        $connection->exec('TRUNCATE TABLE tasks');
        $connection->exec('TRUNCATE TABLE users');
        $connection->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    public static function insertUser(string $name, string $email, string $password): int
    {
        $statement = self::connection()->prepare(
            'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
        );

        $statement->execute([':name' => $name, ':email' => $email, ':password' => $password]);

        return (int) self::connection()->lastInsertId();
    }

    public static function insertTask(array $attributes): int
    {
        $attributes += [
            'title' => 'Seeded task',
            'description' => 'Seeded description',
            'status' => 'pending',
            'expiration_date' => '2026-12-31',
            'user_id' => null,
        ];

        $statement = self::connection()->prepare(
            'INSERT INTO tasks (title, description, status, expiration_date, user_id)
             VALUES (:title, :description, :status, :expiration_date, :user_id)'
        );

        $statement->execute([
            ':title' => $attributes['title'],
            ':description' => $attributes['description'],
            ':status' => $attributes['status'],
            ':expiration_date' => $attributes['expiration_date'],
            ':user_id' => $attributes['user_id'],
        ]);

        return (int) self::connection()->lastInsertId();
    }

    public static function findTask(int $id): array|false
    {
        $statement = self::connection()->prepare('SELECT * FROM tasks WHERE id = :id');

        $statement->execute([':id' => $id]);

        return $statement->fetch();
    }

    public static function findUserByEmail(string $email): array|false
    {
        $statement = self::connection()->prepare('SELECT * FROM users WHERE email = :email');

        $statement->execute([':email' => $email]);

        return $statement->fetch();
    }

    private static function connect(): PDO
    {
        $config = require BASE_PATH . 'config/database.php';

        if (!str_contains($config['dsn'], 'dbname=' . $config['database'])) {
            throw new RuntimeException('Unexpected database configuration for the test suite.');
        }

        if ($config['database'] === env('DB_DATABASE', 'taskflow')) {
            throw new RuntimeException(
                "Refusing to run: the test database is the same as the development one ({$config['database']}). "
                . 'Set DB_TEST_DATABASE in .env.'
            );
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Connect without a database first so the suite can create it on a fresh checkout.
        $serverDsn = preg_replace('/dbname=[^;]*;?/', '', $config['dsn']);

        $server = new PDO($serverDsn, $config['username'], $config['password'], $options);
        $server->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}`");

        $connection = new PDO($config['dsn'], $config['username'], $config['password'], $options);
        $connection->exec((string) file_get_contents(BASE_PATH . 'database/schema.sql'));

        return $connection;
    }
}
