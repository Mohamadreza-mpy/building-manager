<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Residents\Form;
use App\Livewire\Residents\Index;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class ResidentModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_can_see_resident_list(): void
    {
        Http::fake(['*/residents' => Http::response($this->success([$this->resident()]))]);
        Livewire::test(Index::class)->assertSee('ساکن نمونه')->assertSee('09123333333')->assertSee('1 واحد محل سکونت');
    }

    public function test_manager_can_create_resident(): void
    {
        Http::fake(['*/residents' => Http::response($this->success($this->resident()), 201)]);
        Livewire::test(Form::class)
            ->set('name', 'ساکن نمونه')->set('mobile', '09123333333')
            ->set('password', '123456')->set('passwordConfirmation', '123456')
            ->call('save')->assertHasNoErrors()->assertRedirect(route('residents.index'));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/residents') && $request['password_confirmation'] === '123456');
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function resident(): array
    {
        return ['id' => 6, 'name' => 'ساکن نمونه', 'mobile' => '09123333333', 'email' => null, 'role' => 'resident', 'apartments_count' => 1];
    }
}
