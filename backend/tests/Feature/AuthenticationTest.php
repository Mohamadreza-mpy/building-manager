<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_register(): void
    {
        $this->postJson('/api/auth/register', ['name' => 'کاربر', 'mobile' => '09121111111', 'password' => '123456', 'password_confirmation' => '123456'])->assertCreated()->assertJsonPath('success', true)->assertJsonPath('data.user.role', 'resident')->assertJsonStructure(['data' => ['token']]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['mobile' => '09122222222', 'password' => '123456']);
        $token = $this->postJson('/api/auth/login', ['mobile' => $user->mobile, 'password' => '123456'])->assertOk()->json('data.token');
        $this->withToken($token)->postJson('/api/auth/logout')->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
