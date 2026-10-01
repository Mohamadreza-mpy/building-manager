<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Requests\Form;
use App\Livewire\Requests\Index;
use App\Livewire\Requests\Respond;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class ResidentRequestModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_resident_sees_own_requests_and_manager_response(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('resident')));
            }

            return Http::response($this->success([$this->residentRequest('completed')]));
        });

        Livewire::test(Index::class)
            ->assertSee('خرابی آسانسور')
            ->assertSee('تکمیل‌شده')
            ->assertSee('مشکل برطرف شد.')
            ->assertSee('ثبت درخواست جدید');
    }

    public function test_manager_sees_requests_and_response_action(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('manager')));
            }

            return Http::response($this->success([$this->residentRequest('pending')]));
        });

        Livewire::test(Index::class)
            ->assertSee('درخواست‌های ساکنان')
            ->assertSee('در انتظار بررسی')
            ->assertSee('بررسی درخواست')
            ->assertDontSee('ثبت درخواست جدید');
    }

    public function test_resident_can_create_request_for_dashboard_apartment(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/dashboard')) {
                return Http::response($this->success(['role' => 'resident', 'current_apartment' => $this->apartment()]));
            }

            return Http::response($this->success($this->residentRequest('pending')), 201);
        });

        Livewire::test(Form::class)
            ->assertSet('apartmentId', 5)
            ->set('title', 'خرابی آسانسور')
            ->set('description', 'آسانسور متوقف شده است.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('requests.index'));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/requests')
            && $request['apartment_id'] === 5);
    }

    public function test_manager_can_respond_to_request(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'GET') {
                return Http::response($this->success([$this->residentRequest('pending')]));
            }

            return Http::response($this->success($this->residentRequest('completed')));
        });

        Livewire::test(Respond::class, ['residentRequest' => 7])
            ->assertSet('status', 'processing')
            ->set('status', 'completed')
            ->set('response', 'مشکل برطرف شد.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('requests.index'));

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && str_ends_with($request->url(), '/requests/7')
            && $request['status'] === 'completed');
    }

    public function test_completed_request_requires_manager_response(): void
    {
        Http::fake(['*/requests' => Http::response($this->success([$this->residentRequest('pending')]))]);

        Livewire::test(Respond::class, ['residentRequest' => 7])
            ->set('status', 'completed')
            ->set('response', '')
            ->call('save')
            ->assertHasErrors(['response' => 'required_if']);
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function user(string $role): array
    {
        return ['id' => 2, 'name' => 'کاربر آزمایشی', 'mobile' => '09120000002', 'email' => null, 'role' => $role];
    }

    private function apartment(): array
    {
        return ['id' => 5, 'building_id' => 2, 'resident_id' => 3, 'number' => '۱۰۱', 'floor' => 1, 'area' => 80, 'resident' => null, 'building' => ['id' => 2, 'name' => 'ساختمان آفتاب']];
    }

    private function residentRequest(string $status): array
    {
        return ['id' => 7, 'apartment_id' => 5, 'title' => 'خرابی آسانسور', 'description' => 'آسانسور متوقف شده است.', 'status' => $status, 'response' => $status === 'completed' ? 'مشکل برطرف شد.' : null, 'apartment' => $this->apartment(), 'created_at' => '2026-10-01T10:00:00Z', 'updated_at' => '2026-10-01T10:00:00Z'];
    }
}
