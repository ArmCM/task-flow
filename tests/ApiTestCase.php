<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\ApiClient;
use Tests\Support\TestDatabase;
use Tests\Support\TestServer;
use Throwable;

/**
 * Base class for tests that go through the HTTP layer.
 *
 * Everything in this API answers through Core\Response::json(), which ends in
 * exit() -- calling a controller in-process would kill PHPUnit itself. So the
 * only way to cover controllers, middleware and policies is over real HTTP
 * against a spawned server. Pure classes (validation, JWT, router, policies)
 * are covered in tests/Unit instead, where no response is ever emitted.
 */
abstract class ApiTestCase extends TestCase
{
    protected const USER_NAME = 'Test User';
    protected const USER_EMAIL = 'test@example.com';
    protected const USER_PASSWORD = 'secret123';

    protected ApiClient $api;
    protected int $userId;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $server = TestServer::instance();
        $server->forgetErrorOutput();

        TestDatabase::truncate();

        $this->userId = TestDatabase::insertUser(self::USER_NAME, self::USER_EMAIL, self::USER_PASSWORD);

        $this->api = new ApiClient($server->baseUri());
        $this->token = $this->login(self::USER_EMAIL, self::USER_PASSWORD);
        $this->api = $this->api->withToken($this->token);
    }

    protected function login(string $email, string $password): string
    {
        $response = $this->api->withoutToken()->get('/login', [], [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertSame(200, $response->status, "Login failed: {$response->body}");

        return (string) $response->data()['token'];
    }

    /** Surfaces server-side warnings/errors, which are logged instead of printed. */
    protected function onNotSuccessfulTest(Throwable $t): never
    {
        $serverOutput = TestServer::instance()->newErrorOutput();

        if ($serverOutput !== '') {
            fwrite(STDERR, "\n--- server log for {$this->name()} ---\n$serverOutput\n\n");
        }

        throw $t;
    }
}
