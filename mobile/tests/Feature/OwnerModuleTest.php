<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Owners\Form;
use App\Livewire\Owners\Index;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class OwnerModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_can_see_owner_list(): void
    {
        Http::fake(['*/owners' => Http::response($this->success([$this->owner()]))]);

        Livewire::test(Index::class)->assertSee('مالک نمونه')->assertSee('09121111111')->assertSee('2 واحد تحت مالکیت');
    }

    public function test_manager_can_create_owner(): void
    {
        Http::fake(['*/owners' => Http::response($this->success($this->owner()), 201)]);

        Livewire::test(Form::class)
            ->set('name', 'مالک نمونه')->set('mobile', '09121111111')
            ->set('password', '123456')->set('passwordConfirmation', '123456')
            ->call('save')->assertHasNoErrors()->assertRedirect(route('owners.index'));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/owners')
            && $request['password_confirmation'] === '123456');
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function owner(): array
    {
        return ['id' => 5, 'name' => 'مالک نمونه', 'mobile' => '09121111111', 'email' => null, 'role' => 'owner', 'owned_apartments_count' => 2];
    }
}
