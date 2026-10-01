<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResidentAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_resident_and_assign_to_apartment(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج آفتاب']);
        Sanctum::actingAs($manager);

        $residentId = $this->postJson('/api/residents', [
            'name' => 'ساکن نمونه', 'mobile' => '09123333333',
            'password' => '123456', 'password_confirmation' => '123456',
        ])->assertCreated()->assertJsonPath('data.role', 'resident')->json('data.id');

        $this->assertDatabaseHas('users', ['id' => $residentId, 'role' => 'resident', 'created_by' => $manager->id]);
        $this->postJson("/api/buildings/{$building->id}/apartments", ['number' => '۱', 'resident_id' => $residentId])
            ->assertCreated()->assertJsonPath('data.resident.id', $residentId);
    }

    public function test_manager_only_lists_residents_in_their_scope(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $visible = User::factory()->create(['role' => 'resident', 'created_by' => $manager->id]);
        $hidden = User::factory()->create(['role' => 'resident', 'created_by' => $otherManager->id]);
        Sanctum::actingAs($manager);

        $this->getJson('/api/residents')->assertOk()
            ->assertJsonFragment(['id' => $visible->id])->assertJsonMissing(['id' => $hidden->id]);
    }

    public function test_manager_cannot_assign_another_managers_resident(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident', 'created_by' => $otherManager->id]);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Sanctum::actingAs($manager);

        $this->postJson("/api/buildings/{$building->id}/apartments", ['number' => '۱', 'resident_id' => $resident->id])
            ->assertUnprocessable()->assertJsonValidationErrors('resident_id');
    }
}
