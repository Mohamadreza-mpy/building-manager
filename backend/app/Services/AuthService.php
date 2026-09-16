<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        unset($data['password_confirmation']);
        $data['role'] = 'resident';
        $user = User::create($data);

        return ['user' => $user, 'token' => $user->createToken('mobile-app')->plainTextToken];
    }

    public function login(array $data): array
    {
        $user = User::where('mobile', $data['mobile'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['mobile' => ['شماره موبایل یا رمز عبور نادرست است.']]);
        }

return ['user' => $user, 'token' => $user->createToken('mobile-app')->plainTextToken];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
