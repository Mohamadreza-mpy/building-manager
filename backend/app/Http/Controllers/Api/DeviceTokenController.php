<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', Rule::in(['android', 'ios'])],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $token = DeviceToken::query()->updateOrCreate(
            ['token' => $data['token']],
            ['user_id' => $request->user()->id, 'platform' => $data['platform'] ?? null, 'device_name' => $data['device_name'] ?? null, 'last_seen_at' => now()],
        );

        return response()->json(['success' => true, 'message' => 'دستگاه برای دریافت اعلان ثبت شد.', 'data' => ['id' => $token->id]]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:255']]);
        $request->user()->deviceTokens()->where('token', $data['token'])->delete();

        return response()->json(['success' => true, 'message' => 'دستگاه از اعلان‌ها خارج شد.', 'data' => null]);
    }
}
