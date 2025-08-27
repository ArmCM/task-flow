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
        $this->error(Response::BAD_REQUEST, $message);
    }

    protected function unauthorized(string $message = 'Unauthorized access'): void
    {
        $this->error(Response::HTTP_UNAUTHORIZED, $message);
    }

    protected function forbidden(string $message = 'Access denied'): void
    {
        $this->error(Response::FORBIDDEN, $message);
    }

    protected function notFound(string $message = 'Resource not found'): void
    {
        $this->error(Response::NOT_FOUND, $message);
    }

    protected function unprocessableEntity(array $errors, string $message = 'Invalid data'): void
    {
        $this->error(Response::UNPROCESSABLE_CONTENT, $message, $errors);
    }

    protected function internalServerError(string $message = 'Internal server error'): void
    {
        $this->error(Response::INTERNAL_SERVER_ERROR, $message);
    }

    protected function hasPagination(array $data): bool
    {
        return isset($data['data'], $data['links'], $data['meta']);
    }

    protected function json(array $data, int $statusCode = 200): null
    {
        return Response::json($data, $statusCode);
    }
}
