<?php

namespace Core;

class Response {
    const int HTTP_OK = 200;
    const int HTTP_CREATED = 201;
    const int HTTP_NO_CONTENT = 204;
    const int BAD_REQUEST = 400;
    const int HTTP_UNAUTHORIZED = 401;
    const int FORBIDDEN = 403;
    const int NOT_FOUND = 404;
    const int UNPROCESSABLE_CONTENT  = 422;
    const int INTERNAL_SERVER_ERROR = 500;

    public static function json(array|string $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $data,
            'message' => $status,
        ]);
        exit;
    }
}
