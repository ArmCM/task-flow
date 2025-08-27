<?php
declare(strict_types=1);

namespace Core;

final class Request
{
    public function __construct(
        private array $server,
        private array $get,
        private array $post,
        private array $inputJson,
        private array $files,
    ){}

    public static function capture(): self
    {
        $json = json_decode(file_get_contents('php://input') ?: '[]', true);

        if (!is_array($json)) $json = [];

        return new self($_SERVER, $_GET, $_POST, $json, $_FILES);
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        return rtrim($path, '/') ?? '/';
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function json(): array
    {
        return $this->inputJson;
    }

    public function params($param = null): array|string
    {
        if (notEmpty($param)) {
            return $this->get[$param] ?? '';
        }

        return $this->get;
    }
}
