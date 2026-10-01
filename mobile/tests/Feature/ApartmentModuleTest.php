<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Apartments\Form;
use App\Livewire\Apartments\Index;
use App\Livewire\Apartments\Show;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class ApartmentModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_apartment_list_displays_units_for_building(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/buildings/2')) {
                return Http::response($this->success($this->building()));
            }

            return Http::response($this->success([
                $this->apartment(10, '۱۰۱'),
                $this->apartment(11, '۱۰۲'),
            ]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('ساختمان آفتاب')
            ->assertSee('واحد ۱۰۱')
            ->assertSee('واحد ۱۰۲')
            ->assertSee('افزودن واحد');
    }

    public function test_manager_can_create_an_apartment(): void
    {
        Http::fake([
            '*/owners' => Http::response($this->success([])),
            '*/buildings/2/apartments' => Http::response($this->success($this->apartment(12, '۲۰۱')), 201),
        ]);

        Livewire::test(Form::class, ['building' => 2])
            ->set('number', '۲۰۱')
            ->set('floor', '2')
            ->set('area', '85.5')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('apartments.show', 12));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/buildings/2/apartments')
            && $request['number'] === '۲۰۱'
            && $request['floor'] === 2
            && $request['area'] === 85.5);
    }

    public function test_manager_can_edit_an_apartment(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/owners')) {
                return Http::response($this->success([]));
            }

            $number = $request->method() === 'PUT' ? '۳۰۲' : '۳۰۱';

            return Http::response($this->success($this->apartment(20, $number)));
        });

        Livewire::test(Form::class, ['apartment' => 20])
            ->assertSet('number', '۳۰۱')
            ->set('number', '۳۰۲')
            ->call('save')
            ->assertRedirect(route('apartments.show', 20));

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && str_ends_with($request->url(), '/apartments/20')
            && $request['number'] === '۳۰۲');
    }

    public function test_manager_can_delete_an_apartment(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'DELETE') {
                return Http::response($this->success(null));
            }

            return Http::response($this->success($this->apartment(30, '۴۰۱')));
        });

        Livewire::test(Show::class, ['apartment' => 30])
            ->assertSee('واحد ۴۰۱')
            ->call('delete')
            ->assertRedirect(route('apartments.index', 2));

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE' && str_ends_with($request->url(), '/apartments/30'));
    }

    public function test_apartment_form_validates_required_number(): void
    {
        Http::fake();

        Livewire::test(Form::class, ['building' => 2])
            ->set('number', '')
            ->call('save')
            ->assertHasErrors(['number' => 'required']);

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/owners'));
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function building(): array
    {
        return ['id' => 2, 'name' => 'ساختمان آفتاب', 'address' => null, 'total_units' => 8, 'apartments_count' => 2, 'manager' => null];
    }

    private function apartment(int $id, string $number): array
    {
        return [
            'id' => $id,
            'building_id' => 2,
            'owner_id' => null,
            'resident_id' => null,
            'number' => $number,
            'floor' => 1,
            'area' => 85.5,
            'resident' => null,
            'owner' => null,
            'building' => ['id' => 2, 'name' => 'ساختمان آفتاب'],
        ];
    }
}
