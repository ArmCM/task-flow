<?php

declare(strict_types=1);

namespace Tests\Support;

final class ApiResponse
{
    public function __construct(
        public readonly int $status,
        public readonly string $body,
    ) {}

    /** The raw body as sent over the wire. */
    public function raw(): array
    {
        return json_decode($this->body, true) ?? [];
    }

    /** The payload as built by the controllers (Core\Response::json sends it verbatim). */
    public function payload(): array|string
    {
        return $this->raw();
    }

    public function data(): array
    {
        $payload = $this->payload();

        return is_array($payload) ? (array) ($payload['data'] ?? []) : [];
    }

    public function meta(): array
    {
        $payload = $this->payload();

        return is_array($payload) ? (array) ($payload['meta'] ?? []) : [];
    }

    public function errors(): array
    {
        $payload = $this->payload();

        return is_array($payload) ? (array) ($payload['errors'] ?? []) : [];
    }

    public function message(): string
    {
        $payload = $this->payload();

        return is_array($payload) ? (string) ($payload['message'] ?? '') : (string) $payload;
    }
}
