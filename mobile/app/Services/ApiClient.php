<?php

namespace App\Services;

use App\Contracts\TokenStorage;
use App\Exceptions\ApiException;
use App\Exceptions\UnauthorizedApiException;
use App\Http\ApiResponse;
use App\State\AuthState;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    public function __construct(
        private readonly TokenStorage $tokenStorage,
        private readonly AuthState $authState,
    ) {}

    public function get(string $uri, array $query = []): ApiResponse
    {
        return $this->request('GET', $uri, ['query' => $query]);
    }

    public function post(string $uri, array $data = []): ApiResponse
    {
        return $this->request('POST', $uri, ['json' => $data]);
    }

    public function put(string $uri, array $data = []): ApiResponse
    {
        return $this->request('PUT', $uri, ['json' => $data]);
    }

    public function delete(string $uri): ApiResponse
    {
        return $this->request('DELETE', $uri, []);
    }

    public function postMultipart(string $uri, array $data, ?string $filePath = null, ?string $mimeType = null, ?string $fileName = null): ApiResponse
    {
        $request = $this->client(json: false);

        if ($filePath) {
            $stream = fopen($filePath, 'r');

            if ($stream === false) {
                throw new ApiException('تصویر رسید قابل خواندن نیست.');
            }

            $request->attach('image', $stream, $fileName ?? basename($filePath), [
                'Content-Type' => $mimeType ?? 'image/jpeg',
            ]);
        }

        try {
            $response = $request->post(ltrim($uri, '/'), $data);
        } catch (ConnectionException) {
            throw new ApiException('ارتباط با سرور برقرار نشد. اتصال اینترنت و آدرس سرور را بررسی کنید.');
        }

        return $this->parseResponse($response);
    }

    private function request(string $method, string $uri, array $options): ApiResponse
    {
        try {
            $response = $this->client()->send($method, ltrim($uri, '/'), $options);
        } catch (ConnectionException) {
            throw new ApiException('ارتباط با سرور برقرار نشد. اتصال اینترنت و آدرس سرور را بررسی کنید.');
        }

        return $this->parseResponse($response);
    }

    private function parseResponse(Response $response): ApiResponse
    {
        $payload = $response->json();
        $payload = is_array($payload) ? $payload : [];

        if ($response->status() === 401) {
            $this->tokenStorage->forget();
            $this->authState->clear();

            throw new UnauthorizedApiException(
                message: 'نشست شما منقضی شده است. دوباره وارد شوید.',
                status: 401,
            );
        }

        if ($response->failed() || ! ($payload['success'] ?? false)) {
            throw new ApiException(
                message: (string) ($payload['message'] ?? 'خطایی در پردازش درخواست رخ داد.'),
                errors: is_array($payload['errors'] ?? null) ? $payload['errors'] : [],
                status: $response->status(),
            );
        }

        return new ApiResponse(
            success: true,
            message: (string) ($payload['message'] ?? ''),
            data: $payload['data'] ?? null,
            status: $response->status(),
        );
    }

    private function client(bool $json = true): PendingRequest
    {
        $request = Http::baseUrl((string) config('api.base_url'))
            ->acceptJson()
            ->timeout((int) config('api.timeout'));

        if ($json) {
            $request->asJson();
        }

        $token = $this->tokenStorage->get();

        return $token ? $request->withToken($token) : $request;
    }
}
