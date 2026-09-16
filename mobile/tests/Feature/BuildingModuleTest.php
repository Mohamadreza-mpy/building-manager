<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Buildings\Form;
use App\Livewire\Buildings\Index;
use App\Livewire\Buildings\Show;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class BuildingModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_building_list_displays_api_results(): void
    {
        Http::fake(['*/buildings' => Http::response($this->success([
            $this->building(1, 'ساختمان آفتاب'),
            $this->building(2, 'برج باران'),
        ]))]);

        Livewire::test(Index::class)
            ->assertSee('ساختمان آفتاب')
            ->assertSee('برج باران')
            ->assertSee('افزودن ساختمان');
    }

    public function test_manager_can_create_a_building(): void
    {
        Http::fake(['*/buildings' => Http::response($this->success($this->building(9, 'ساختمان نیلوفر')), 201)]);

        Livewire::test(Form::class)
            ->set('name', 'ساختمان نیلوفر')
            ->set('address', 'تهران، خیابان بهار')
            ->set('totalUnits', '8')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('buildings.show', 9));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/buildings')
            && $request['name'] === 'ساختمان نیلوفر'
            && $request['total_units'] === 8);
    }

    public function test_manager_can_edit_a_building(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'GET') {
                return Http::response($this->success($this->building(4, 'ساختمان قدیم')));
            }

            return Http::response($this->success($this->building(4, 'ساختمان جدید')));
        });

        Livewire::test(Form::class, ['building' => 4])
            ->assertSet('name', 'ساختمان قدیم')
            ->set('name', 'ساختمان جدید')
            ->call('save')
            ->assertRedirect(route('buildings.show', 4));

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && str_ends_with($request->url(), '/buildings/4')
            && $request['name'] === 'ساختمان جدید');
    }

    public function test_manager_can_delete_a_building(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'DELETE') {
                return Http::response($this->success(null));
            }

            return Http::response($this->success($this->building(7, 'ساختمان حذف‌شدنی')));
        });

        Livewire::test(Show::class, ['building' => 7])
            ->assertSee('ساختمان حذف‌شدنی')
            ->call('delete')
            ->assertRedirect(route('buildings.index'));

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE' && str_ends_with($request->url(), '/buildings/7'));
    }

    public function test_resident_is_redirected_from_building_management(): void
    {
        Http::fake(['*/auth/me' => Http::response($this->success([
            'id' => 3, 'name' => 'ساکن', 'mobile' => '09120000003', 'email' => null, 'role' => 'resident',
        ]))]);

        $this->get(route('buildings.index'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error_message');
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function building(int $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'address' => 'تهران، خیابان نمونه',
            'total_units' => 8,
            'apartments_count' => 3,
            'manager' => ['id' => 2, 'name' => 'مدیر ساختمان'],
        ];
    }
}
