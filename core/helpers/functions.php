<?php

function getUri()
{
    return parse_url($_SERVER['REQUEST_URI'])['path'];
}

function getRequestMethod(): string
{
    return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
}

function jsonEncode(string|array $data, int $status, ?array $options = []): void
{
    http_response_code($status);
    header('Content-Type: application/json');

    $payload = is_array($data) ? $data : ['message' => $data, 'options' => $options];

    if (!empty($options)) {
        $payload = array_merge($payload, $options);
    }

    echo json_encode($payload);
    exit;
}
