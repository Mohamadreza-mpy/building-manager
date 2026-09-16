<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Charges\Form;
use App\Livewire\Charges\Index;
use App\Livewire\Charges\Show;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class ChargeModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_sees_building_charges_and_payment_status(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('manager')));
            }
            if (str_ends_with($request->url(), '/buildings/2')) {
                return Http::response($this->success($this->building()));
            }

            return Http::response($this->success([$this->charge(8, 'pending')]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('شارژ ماهانه')
            ->assertSee('در انتظار پرداخت')
            ->assertSee('ثبت شارژ جدید');
    }

    public function test_resident_sees_own_charges(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('resident')));
            }

            return Http::response($this->success([$this->charge(9, 'paid')]));
        });

        Livewire::test(Index::class)
            ->assertSee('شارژهای من')
            ->assertSee('پرداخت‌شده')
            ->assertDontSee('ثبت شارژ جدید');

        Http::assertSent(fn (Request $request) => $request->method() === 'GET' && str_ends_with($request->url(), '/charges'));
    }

    public function test_manager_can_create_charge_for_an_apartment(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'GET') {
                return Http::response($this->success([$this->apartment()]));
            }

            return Http::response($this->success($this->charge(12, 'pending')), 201);
        });

        Livewire::test(Form::class, ['building' => 2])
            ->set('apartmentId', '5')
            ->set('title', 'شارژ مهر')
            ->set('month', '2026-10')
            ->set('amount', '5000000')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('charges.show', 12));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/buildings/2/charges')
            && $request['apartment_id'] === 5
            && $request['month'] === '2026-10'
            && $request['amount'] === 5000000.0);
    }

    public function test_charge_detail_displays_amount_and_status(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('resident')));
            }

            return Http::response($this->success($this->charge(15, 'overdue')));
        });

        Livewire::test(Show::class, ['charge' => 15])
            ->assertSee('سررسید گذشته')
            ->assertSee('۵,۰۰۰,۰۰۰')
            ->assertSee('پرداخت نشده');
    }

    public function test_manager_cannot_open_resident_charge_list_route(): void
    {
        Http::fake(['*/auth/me' => Http::response($this->success($this->user('manager')))]);

        $this->get(route('charges.mine'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error_message', 'این بخش مخصوص ساکنان است.');
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function user(string $role): array
    {
        return ['id' => 2, 'name' => 'کاربر آزمایشی', 'mobile' => '09120000002', 'email' => null, 'role' => $role];
    }

    private function building(): array
    {
        return ['id' => 2, 'name' => 'ساختمان آفتاب', 'address' => null, 'total_units' => 8, 'apartments_count' => 1, 'manager' => null];
    }

    private function apartment(): array
    {
        return ['id' => 5, 'building_id' => 2, 'resident_id' => 3, 'number' => '۱۰۱', 'floor' => 1, 'area' => 80, 'resident' => ['id' => 3, 'name' => 'ساکن'], 'building' => $this->building()];
    }

    private function charge(int $id, string $status): array
    {
        return ['id' => $id, 'building_id' => 2, 'apartment_id' => 5, 'title' => 'شارژ ماهانه', 'month' => '2026-10', 'amount' => '5000000.00', 'status' => $status, 'paid_at' => $status === 'paid' ? '2026-10-05T10:30:00Z' : null, 'apartment' => $this->apartment()];
    }
}
