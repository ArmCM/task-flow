<?php

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

function basePath($path): string
{
    return BASE_PATH . $path;
}

function notEmpty($value): bool
{
    return !empty($value);
}
