<?php
declare(strict_types=1);

namespace Core;

final class Request
{
    private array $params = [];

    public function __construct(
        private array $server,
        private array $get,
        private array $post,
        private array $inputJson,
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

//    public function getRequestMethod(): string
//    {
//        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
//    }

    public function json(): array
    {
        return $this->inputJson;
    }
}
