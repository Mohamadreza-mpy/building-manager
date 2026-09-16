<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register($request->validated());

        return response()->json(['success' => true, 'message' => 'ثبت‌نام با موفقیت انجام شد.', 'data' => ['user' => new UserResource($result['user']), 'token' => $result['token']]], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login($request->validated());

        return response()->json(['success' => true, 'message' => 'ورود با موفقیت انجام شد.', 'data' => ['user' => new UserResource($result['user']), 'token' => $result['token']]]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'اطلاعات کاربر دریافت شد.', 'data' => new UserResource($request->user())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json(['success' => true, 'message' => 'خروج با موفقیت انجام شد.', 'data' => null]);
    }
}
