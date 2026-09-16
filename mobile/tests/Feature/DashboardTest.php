<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Home;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_dashboard_displays_management_metrics(): void
    {
        $this->fakeUser('manager');
        Http::fake(['*/dashboard' => Http::response(['success' => true, 'message' => 'دریافت شد.', 'data' => [
            'role' => 'manager', 'buildings_count' => 2, 'apartments_count' => 12, 'pending_requests_count' => 3, 'monthly_charges_count' => 10, 'monthly_charges_total' => '25000000',
        ]])]);

        Livewire::test(Home::class)
            ->assertSee('ساختمان‌ها')
            ->assertSee('واحدها')
            ->assertSee('درخواست‌های منتظر')
            ->assertSee('۲۵,۰۰۰,۰۰۰ ریال');
    }

    public function test_resident_dashboard_displays_apartment_and_personal_metrics(): void
    {
        $this->fakeUser('resident');
        Http::fake(['*/dashboard' => Http::response(['success' => true, 'message' => 'دریافت شد.', 'data' => [
            'role' => 'resident', 'current_apartment' => ['number' => '۱۰۱', 'floor' => 1, 'building' => ['name' => 'ساختمان آفتاب']], 'unpaid_charges_count' => 2, 'unpaid_charges_total' => '5000000', 'announcements_count' => 1, 'requests_count' => 3, 'pending_requests_count' => 1, 'recent_announcements' => [['id' => 1, 'title' => 'قطع موقت آب']],
        ]])]);

        Livewire::test(Home::class)
            ->assertSee('واحد ۱۰۱')
            ->assertSee('ساختمان آفتاب')
            ->assertSee('شارژ پرداخت‌نشده')
            ->assertSee('قطع موقت آب');
    }

    public function test_dashboard_can_show_error_and_retry(): void
    {
        $this->fakeUser('manager');
        Http::fake(['*/dashboard' => Http::response(['success' => false, 'message' => 'ارتباط با داشبورد برقرار نشد.', 'errors' => []], 503)]);

        Livewire::test(Home::class)
            ->assertSet('errorMessage', 'ارتباط با داشبورد برقرار نشد.')
            ->assertSee('تلاش دوباره');
    }

    private function fakeUser(string $role): void
    {
        Http::fake(['*/auth/me' => Http::response(['success' => true, 'message' => 'دریافت شد.', 'data' => [
            'id' => 2, 'name' => 'کاربر آزمایشی', 'mobile' => '09120000002', 'email' => null, 'role' => $role,
        ]])]);
    }
}
