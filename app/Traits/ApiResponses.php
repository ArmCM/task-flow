<?php

namespace App\Traits;

use Core\Response;

trait ApiResponses
{
    protected array $response = [];

    protected function success(string $message, array $data = [], int $statusCode = 200, array $options = []): void
    {
        $this->response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data['data'] ?? $data,
            'options' => $options,
        ];

        if ($this->hasPagination($data)) {
            $this->response['meta'] = $data['meta'];
            $this->response['links'] = $data['links'];
        }

        $this->json($this->response, $statusCode);
    }

    protected function hasPagination(array $data): bool
    {
        return isset($data['links'], $data['meta']);
    }

    protected function error(int $statusCode, string $message, array|null $errors = null): void
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        $this->json($response, $statusCode);
    }

    protected function ok(string $message, array $data = []): void
    {
        $this->success($message, $data, Response::HTTP_OK);
    }

    protected function created(string $message, array $data = []): void
    {
        $this->success($message, $data, Response::HTTP_CREATED);
    }

    protected function noContent(): void
    {
        http_response_code(Response::HTTP_NO_CONTENT);
        exit;
    }

    protected function badRequest(string $message): void
    {
        $this->error(400, $message);
    }

    protected function unauthorized(string $message = 'Acceso no autorizado'): void
    {
        $this->error(401, $message);
    }

    protected function forbidden(string $message = 'Acceso denegado'): void
    {
        $this->error(403, $message);
    }

    protected function notFound(string $message = 'Recurso no encontrado'): void
    {
        $this->error(404, $message);
    }

    protected function unprocessableEntity(array $errors, string $message = 'Datos no válidos'): void
    {
        $this->error(422, $message, $errors);
    }

    protected function internalServerError(string $message = 'Error interno del servidor'): void
    {
        $this->error(500, $message);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        jsonEncode($data, $statusCode);
    }
}
