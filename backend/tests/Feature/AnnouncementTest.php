<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_publish_announcement(): void
    {
        Notification::fake();
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۱']);
        Sanctum::actingAs($manager);

        $this->postJson("/api/buildings/{$building->id}/announcements", [
            'title' => 'قطع موقت آب',
            'body' => 'آب ساختمان فردا قطع خواهد شد.',
        ])->assertCreated()->assertJsonPath('data.title', 'قطع موقت آب');

        $this->assertDatabaseHas('announcements', ['building_id' => $building->id, 'title' => 'قطع موقت آب']);
    }

    public function test_resident_can_read_own_building_announcements(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۱']);
        $building->announcements()->create(['title' => 'جلسه ساختمان', 'body' => 'جلسه جمعه برگزار می‌شود.', 'created_by' => $manager->id]);
        Sanctum::actingAs($resident);

        $this->getJson("/api/buildings/{$building->id}/announcements")
            ->assertOk()
            ->assertJsonPath('data.0.body', 'جلسه جمعه برگزار می‌شود.');
    }
}
