<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

/**
 * Minimal curl client. Deliberately dependency-free: the abandoned e2e attempt
 * imported GuzzleHttp\Client, which only worked because guzzle happened to sit
 * in vendor/ -- it was never in composer.json, so the suite broke on a fresh
 * `composer install`.
 */
final class ApiClient
{
    public function __construct(
        private string $baseUri,
        private ?string $token = null,
        private ?string $rawAuthorization = null,
    ) {}

    public function withToken(?string $token): self
    {
        return new self($this->baseUri, $token);
    }

    public function withoutToken(): self
    {
        return new self($this->baseUri);
    }

    /** Sends the Authorization header verbatim, for malformed-header cases. */
    public function withRawAuthorization(string $header): self
    {
        return new self($this->baseUri, null, $header);
    }

    public function get(string $uri, array $query = [], ?array $body = null): ApiResponse
    {
        return $this->request('GET', $uri, $query, $body);
    }

    public function post(string $uri, ?array $body = null): ApiResponse
    {
        return $this->request('POST', $uri, [], $body);
    }

    public function patch(string $uri, ?array $body = null): ApiResponse
    {
        return $this->request('PATCH', $uri, [], $body);
    }

    public function delete(string $uri): ApiResponse
    {
        return $this->request('DELETE', $uri, [], null);
    }

    private function request(string $method, string $uri, array $query, ?array $body): ApiResponse
    {
        $url = $this->baseUri . $uri . ($query ? '?' . http_build_query($query) : '');

        $headers = ['Accept: application/json'];

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        if ($this->rawAuthorization !== null) {
            $headers[] = 'Authorization: ' . $this->rawAuthorization;
        } elseif ($this->token !== null) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $handle = curl_init($url);

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
        ]);

        if ($body !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $responseBody = curl_exec($handle);

        if ($responseBody === false) {
            $error = curl_error($handle);
            curl_close($handle);

            throw new RuntimeException("Request to [$method] $url failed: $error");
        }

        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        return new ApiResponse($status, (string) $responseBody);
    }
}
