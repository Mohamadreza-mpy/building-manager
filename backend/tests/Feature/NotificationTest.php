<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_and_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new InAppNotification('charge_created', 'شارژ جدید', 'یک شارژ جدید ثبت شد.', ['charge_id' => 5]));
        $notification = $user->notifications()->firstOrFail();
        Sanctum::actingAs($user);

        $this->getJson('/api/notifications')->assertOk()->assertJsonPath('data.0.read_at', null);
        $this->putJson("/api/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.id', $notification->id);
        $this->assertNotNull($notification->refresh()->read_at);
    }

    public function test_user_can_register_and_remove_push_device_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/device-tokens', ['token' => 'device-token-123', 'platform' => 'android'])
            ->assertOk();
        $this->assertDatabaseHas('device_tokens', ['user_id' => $user->id, 'token' => 'device-token-123']);

        $this->deleteJson('/api/device-tokens', ['token' => 'device-token-123'])->assertOk();
        $this->assertDatabaseMissing('device_tokens', ['token' => 'device-token-123']);
    }
}
