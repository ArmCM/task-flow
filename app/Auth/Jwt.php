<?php

namespace App\Auth;

use Exception;
use Firebase\JWT\JWT as BaseJWT;
use Firebase\JWT\Key;

class Jwt
{
    private string $secret;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'default_secret';
    }

    public function generateToken(array $payload, int $ttl = 86400): string
    {
        $issuedAt = time();

        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $ttl,
        ]);

        return BaseJWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken(string $token): object
    {
        try {
            return BaseJWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (Exception $exception) {
            throw new Exception('Invalid token: ' . $exception->getMessage(), 401);
        }
    }
}
