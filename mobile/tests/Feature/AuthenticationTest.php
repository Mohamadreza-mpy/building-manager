<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Auth\Login;
use App\Services\AuthService;
use App\Services\SecureTokenStorage;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Native\Mobile\Facades\SecureStorage;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private InMemoryTokenStorage $tokens;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokens = new InMemoryTokenStorage;
        $this->app->instance(TokenStorage::class, $this->tokens);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/home')->assertRedirect('/login');
        $this->get('/login')
            ->assertOk()
            ->assertSee('ورود به حساب کاربری')
            ->assertSee('شماره موبایل');
    }

    public function test_browser_preview_uses_session_storage_instead_of_native_bridge(): void
    {
        config(['nativephp-internal.running' => false]);
        $storage = new SecureTokenStorage;

        $storage->store('browser-token');

        $this->assertSame('browser-token', $storage->get());

        $storage->forget();

        $this->assertNull($storage->get());
    }

    public function test_jump_falls_back_to_session_when_secure_storage_is_unavailable(): void
    {
        config(['nativephp-internal.running' => false]);
        putenv('JUMP_BRIDGE_PORT=3002');
        SecureStorage::shouldReceive('set')->once()->andReturnFalse();
        SecureStorage::shouldReceive('get')->once()->andReturnNull();

        try {
            $storage = new SecureTokenStorage;
            $storage->store('jump-token');

            $this->assertSame('jump-token', $storage->get());
        } finally {
            putenv('JUMP_BRIDGE_PORT');
            session()->forget('building_manager_auth_token');
        }
    }

    public function test_user_can_login_through_backend_api(): void
    {
        Http::fake([
            '*/auth/login' => Http::response([
                'success' => true,
                'message' => 'ورود با موفقیت انجام شد.',
                'data' => [
                    'token' => 'sanctum-token',
                    'user' => $this->userPayload(),
                ],
            ]),
        ]);

        Livewire::test(Login::class)
            ->set('mobile', '09120000002')
            ->set('password', '123456')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertSame('sanctum-token', $this->tokens->token);
        $this->assertSame('manager', session('building_manager_auth_user.role'));

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/auth/login')
            && $request['mobile'] === '09120000002'
            && $request['password'] === '123456');
    }

    public function test_login_errors_are_shown_in_persian(): void
    {
        Http::fake([
            '*/auth/login' => Http::response([
                'success' => false,
                'message' => 'شماره موبایل یا رمز عبور نادرست است.',
                'errors' => ['mobile' => ['شماره موبایل یا رمز عبور نادرست است.']],
            ], 422),
        ]);

        Livewire::test(Login::class)
            ->set('mobile', '09120000002')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertSet('errorMessage', 'شماره موبایل یا رمز عبور نادرست است.')
            ->assertHasErrors('mobile');

        $this->assertNull($this->tokens->token);
    }

    public function test_expired_token_is_removed(): void
    {
        $this->tokens->token = 'expired-token';

        Http::fake([
            '*/auth/me' => Http::response([
                'success' => false,
                'message' => 'احراز هویت انجام نشده است.',
                'errors' => [],
            ], 401),
        ]);

        $this->get('/home')
            ->assertRedirect('/login')
            ->assertSessionHas('auth_error', 'نشست شما منقضی شده است. دوباره وارد شوید.');

        $this->assertNull($this->tokens->token);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->tokens->token = 'valid-token';
        session(['building_manager_auth_user' => $this->userPayload()]);

        Http::fake([
            '*/auth/logout' => Http::response(['success' => true, 'message' => 'خروج با موفقیت انجام شد.', 'data' => null]),
        ]);

        app(AuthService::class)->logout();

        $this->assertNull($this->tokens->token);
        $this->assertNull(session('building_manager_auth_user'));
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer valid-token'));
    }

    private function userPayload(): array
    {
        return [
            'id' => 2,
            'name' => 'مدیر ساختمان',
            'mobile' => '09120000002',
            'email' => null,
            'role' => 'manager',
        ];
    }
}
