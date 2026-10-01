<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Announcements\Form;
use App\Livewire\Announcements\Index;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class AnnouncementModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_sees_building_announcements(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('manager')));
            }
            if (str_ends_with($request->url(), '/buildings/2')) {
                return Http::response($this->success($this->building()));
            }

            return Http::response($this->success([$this->announcement()]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('قطع موقت آب')
            ->assertSee('آب ساختمان فردا قطع خواهد شد.')
            ->assertSee('انتشار اطلاعیه');
    }

    public function test_resident_can_read_announcements_without_building_detail_request(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('resident')));
            }

            return Http::response($this->success([$this->announcement()]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('قطع موقت آب')
            ->assertDontSee('انتشار اطلاعیه');

        Http::assertNotSent(fn (Request $request) => str_ends_with($request->url(), '/buildings/2'));
    }

    public function test_manager_can_publish_announcement(): void
    {
        Http::fake(['*/buildings/2/announcements' => Http::response($this->success($this->announcement()), 201)]);

        Livewire::test(Form::class, ['building' => 2])
            ->set('title', 'قطع موقت آب')
            ->set('body', 'آب ساختمان فردا قطع خواهد شد.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('announcements.index', 2));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/buildings/2/announcements')
            && $request['title'] === 'قطع موقت آب');
    }

    public function test_announcement_form_validates_title_and_body(): void
    {
        Http::fake();

        Livewire::test(Form::class, ['building' => 2])
            ->call('save')
            ->assertHasErrors(['title', 'body']);

        Http::assertNothingSent();
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

    private function announcement(): array
    {
        return ['id' => 7, 'building_id' => 2, 'title' => 'قطع موقت آب', 'body' => 'آب ساختمان فردا قطع خواهد شد.', 'created_by' => 2, 'creator' => ['id' => 2, 'name' => 'مدیر'], 'created_at' => '2026-09-16T10:00:00Z'];
    }
}
