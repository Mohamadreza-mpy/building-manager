<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_apartment_and_assign_resident(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident', 'created_by' => $manager->id]);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Sanctum::actingAs($manager);
        $this->postJson("/api/buildings/{$building->id}/apartments", ['number' => '۱', 'floor' => 1, 'resident_id' => $resident->id])->assertCreated()->assertJsonPath('data.resident.id', $resident->id);
        $this->assertDatabaseHas('apartments', ['building_id' => $building->id, 'resident_id' => $resident->id]);
    }
}
