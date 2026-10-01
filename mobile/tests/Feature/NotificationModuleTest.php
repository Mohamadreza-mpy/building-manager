<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Notifications\Index;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class NotificationModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_notifications_show_unread_count_and_event_content(): void
    {
        Http::fake(['*/notifications' => Http::response($this->success([
            $this->notification('one', null, 'charge_created'),
            $this->notification('two', '2026-10-01T11:00:00Z', 'announcement_created'),
        ]))]);

        Livewire::test(Index::class)
            ->assertSet('unreadCount', 1)
            ->assertSee('شارژ جدید')
            ->assertSee('خواندن همه');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        Http::fake(function (Request $request) {
            if ($request->method() === 'PUT') {
                return Http::response($this->success($this->notification('one', '2026-10-01T12:00:00Z', 'charge_created')));
            }

            return Http::response($this->success([$this->notification('one', null, 'charge_created')]));
        });

        Livewire::test(Index::class)
            ->assertSet('unreadCount', 1)
            ->call('markAsRead', 'one')
            ->assertSet('unreadCount', 0);

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT' && str_ends_with($request->url(), '/notifications/one/read'));
    }

    public function test_push_token_event_registers_device_with_backend(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/notifications')) {
                return Http::response($this->success([]));
            }

            return Http::response($this->success(['id' => 1]));
        });

        Livewire::test(Index::class)
            ->call('handlePushToken', 'native-device-token', 'building-manager-push')
            ->assertSet('pushMessage', 'اعلان‌های دستگاه با موفقیت فعال شدند.');

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/device-tokens')
            && $request['token'] === 'native-device-token');
    }

    public function test_push_setup_explains_missing_firebase_configuration(): void
    {
        config()->set('push.enabled', false);
        Http::fake(['*/notifications' => Http::response($this->success([]))]);

        Livewire::test(Index::class)
            ->call('enablePushNotifications')
            ->assertSet('pushMessage', 'زیرساخت Push آماده است؛ برای فعال‌سازی نسخه نهایی باید تنظیمات Firebase اضافه شود.');
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function notification(string $id, ?string $readAt, string $type): array
    {
        return [
            'id' => $id,
            'type' => $type,
            'title' => $type === 'charge_created' ? 'شارژ جدید' : 'اطلاعیه جدید',
            'body' => 'یک پیام جدید برای شما ثبت شد.',
            'meta' => $type === 'charge_created' ? ['charge_id' => 5] : [],
            'read_at' => $readAt,
            'created_at' => '2026-10-01T10:00:00Z',
        ];
    }
}
