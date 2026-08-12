<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiTestCase;
use Tests\Support\TestDatabase;

final class AuthTest extends ApiTestCase
{
    #[Test]
    public function itRegistersAUser(): void
    {
        $response = $this->api->withoutToken()->post('/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'analytical',
        ]);

        $this->assertSame(200, $response->status);
        $this->assertSame('user registered successfully', $response->message());

        $user = TestDatabase::findUserByEmail('ada@example.com');

        $this->assertNotFalse($user);
        $this->assertSame('Ada Lovelace', $user['name']);
    }

    #[Test]
    public function itRejectsAnInvalidRegistration(): void
    {
        $response = $this->api->withoutToken()->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $this->assertSame(422, $response->status);
        $this->assertSame('Invalid data', $response->message());
        $this->assertSame(['name', 'email', 'password'], array_keys($response->errors()));
        $this->assertFalse(TestDatabase::findUserByEmail('not-an-email'));
    }

    #[Test]
    public function itIssuesATokenOnLogin(): void
    {
        $response = $this->api->withoutToken()->get('/login', [], [
            'email' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
        ]);

        $this->assertSame(200, $response->status);

        $data = $response->data();

        $this->assertSame($this->userId, $data['id']);
        $this->assertSame(self::USER_EMAIL, $data['email']);
        $this->assertNotEmpty($data['token']);
        $this->assertCount(3, explode('.', $data['token']));
    }

    #[Test]
    public function itRejectsWrongCredentials(): void
    {
        $response = $this->api->withoutToken()->get('/login', [], [
            'email' => self::USER_EMAIL,
            'password' => 'wrong-password',
        ]);

        $this->assertSame(404, $response->status);
    }

    #[Test]
    public function itRequiresAnAuthorizationHeaderOnProtectedRoutes(): void
    {
        $response = $this->api->withoutToken()->get('/tasks');

        $this->assertSame(401, $response->status);
        $this->assertSame('Missing or invalid authorization header', $response->message());
    }

    #[Test]
    public function itRejectsAMalformedAuthorizationHeader(): void
    {
        $response = $this->api->withRawAuthorization('Token ' . $this->token)->get('/tasks');

        $this->assertSame(401, $response->status);
        $this->assertSame('Missing or invalid authorization header', $response->message());
    }

    #[Test]
    public function itRejectsAnInvalidToken(): void
    {
        $response = $this->api->withToken('not.a.jwt')->get('/tasks');

        $this->assertSame(401, $response->status);
        $this->assertStringStartsWith('Invalid token:', $response->message());
    }

    #[Test]
    public function itReturns404ForAnUnknownRoute(): void
    {
        // Auth runs before routing, so reaching the router at all needs a token.
        $response = $this->api->get('/nope');

        $this->assertSame(404, $response->status);
        $this->assertSame('Route not found for [GET] /nope', $response->message());
    }
}
