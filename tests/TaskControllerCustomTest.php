<?php

use Core\Database;
use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskControllerCustomTest extends TestCase
{
    protected Database $db;
    private static $serverProcess;
    private static string $dbPath;

    public static function setUpBeforeClass(): void
    {
        $_ENV['APP_ENV'] = 'testing';

        self::$dbPath = __DIR__ . '/database.sqlite';

        if (!is_dir(dirname(self::$dbPath))) {
            mkdir(dirname(self::$dbPath), 0777, true);
        }

        if (file_exists(self::$dbPath)) {
            unlink(self::$dbPath);
        }

        $pdo = new PDO('sqlite:' . self::$dbPath);
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT,
                password TEXT
            );
        ");
        $pdo->exec("
            CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT,
                description TEXT,
                status TEXT DEFAULT 'pending',
                due_date TEXT,
                user_id INTEGER
            );
        ");
        $pdo->exec("
            INSERT INTO users (email, password) VALUES ('test@test.com', 'secret');
        ");

        $command = sprintf(
            'php -S localhost:8080 -t public > /dev/null 2>&1 & echo $!'
        );

        self::$serverProcess = exec($command);

        sleep(1);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$serverProcess) {
            exec('kill ' . self::$serverProcess);
        }

        if (file_exists(self::$dbPath)) {
            unlink(self::$dbPath);
        }
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client([
            'base_uri' => 'http://localhost:8080',
            'http_errors' => false
        ]);
    }

    #[Test]
    public function createTaskFlow()
    {
        eval(\Psy\sh());
        $response = $this->client->get('/login', [
            'json' => [
                'email' => 'test@test.com',
                'password' => 'secret',
            ],
        ]);
        eval(\Psy\sh());


        //$loginBody = json_decode($loginResponse->getBody(), true);
        //$this->assertArrayHasKey('token', $loginBody);

        //$token = $loginBody['token'];

        // 2️⃣ Crear tarea usando Request con Authorization header
//        $taskRequest = new Request(
//            server: [
//                'REQUEST_METHOD' => 'POST',
//                'REQUEST_URI' => '/tasks',
//                'HTTP_AUTHORIZATION' => "Bearer $token",
//            ],
//            get: [],        // query params
//            post: [],       // form data
//            inputJson: [    // el body JSON
//                'title' => 'Test task',
//                'description' => 'E2E test',
//                'status' => 'pending',
//                'due_date' => '2025-12-31',
//            ],
//            files: []       // archivos
//        );
//
//        $taskResponse = $this->router->route('/tasks', 'POST', $taskRequest);
//
//        $this->assertEquals(201, $taskResponse->getStatusCode());
//
//        $taskBody = json_decode($taskResponse->getBody(), true);
//        $this->assertEquals('Test task', $taskBody['title']);
//        $this->assertEquals('pending', $taskBody['status']);
    }
}
