<?php

namespace App\Traits;

use Core\Response;

trait ApiResponses
{
    protected array $response = [];

    protected function success(string $message, array $data = [], int $statusCode = 200): void
    {
        $this->response = [
            'status' => 'success',
            'message' => $message,
        ];

        if (notEmpty($data)) {
            $this->response['data'] = $data;
        }

        if ($this->hasPagination($data)) {
            $this->response['data'] = $data['data'];
            $this->response['meta'] = $data['meta'];
            $this->response['links'] = $data['links'];
        }

        $this->json($this->response, $statusCode);
    }

    protected function hasPagination(array $data): bool
    {
        return isset($data['data'], $data['links'], $data['meta']);
    }

    protected function ok(string $message = 'success', array $data = []): void
    {
        $this->success($message, $data, Response::HTTP_OK);
    }

    protected function created(string $message = 'created', array $data = []): void
    {
        $this->success($message, $data, Response::HTTP_CREATED);
    }

    protected function noContent(string $message = 'no content', array $data = []): void
    {
        $this->success($message, $data, Response::HTTP_NO_CONTENT);
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

    protected function badRequest(string $message): void
    {
        $this->error(400, $message);
    }

    protected function unauthorized(string $message = 'Unauthorized access'): void
    {
        $this->error(401, $message);
    }

    protected function forbidden(string $message = 'Access denied'): void
    {
        $this->error(403, $message);
    }

    protected function notFound(string $message = 'Resource not found'): void
    {
        $this->error(404, $message);
    }

    protected function unprocessableEntity(array $errors, string $message = 'Invalid data'): void
    {
        $this->error(422, $message, $errors);
    }

    protected function internalServerError(string $message = 'Internal server error'): void
    {
        $this->error(500, $message);
    }

    protected function json(array $data, int $statusCode = 200)
    {
        return Response::json($data, $statusCode);
    }
}
