<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_building(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        Sanctum::actingAs($manager);
        $this->postJson('/api/buildings', ['name' => 'ساختمان آفتاب', 'total_units' => 4])->assertCreated()->assertJsonPath('data.manager.id', $manager->id);
        $this->assertDatabaseHas('buildings', ['manager_id' => $manager->id]);
    }

    public function test_resident_cannot_create_building(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'resident']));
        $this->postJson('/api/buildings', ['name' => 'غیرمجاز'])->assertForbidden()->assertJsonPath('success', false);
    }

    public function test_admin_can_see_all_buildings(): void
    {
        $managers = User::factory()->count(2)->create(['role' => 'manager']);
        foreach ($managers as $manager) {
            Building::create(['manager_id' => $manager->id, 'name' => 'ساختمان']);
        } Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->getJson('/api/buildings')->assertOk()->assertJsonCount(2, 'data');
    }
}
