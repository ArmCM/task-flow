<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Auth\Jwt;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JwtTest extends TestCase
{
    #[Test]
    public function itRoundTripsAPayload(): void
    {
        $token = (new Jwt)->generateToken(['user_id' => 7, 'email' => 'test@example.com']);

        $decoded = (new Jwt)->verifyToken($token);

        $this->assertSame(7, $decoded->user_id);
        $this->assertSame('test@example.com', $decoded->email);
        $this->assertGreaterThan($decoded->iat, $decoded->exp);
    }

    #[Test]
    public function itRejectsAnExpiredToken(): void
    {
        $token = (new Jwt)->generateToken(['user_id' => 7], ttl: -10);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessageMatches('/^Invalid token: Expired token/');

        (new Jwt)->verifyToken($token);
    }

    #[Test]
    public function itRejectsATokenSignedWithAnotherSecret(): void
    {
        $token = \Firebase\JWT\JWT::encode(['user_id' => 7], 'another-secret', 'HS256');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        (new Jwt)->verifyToken($token);
    }

    #[Test]
    public function itRejectsGarbage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        (new Jwt)->verifyToken('not.a.jwt');
    }
}
