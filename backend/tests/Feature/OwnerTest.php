<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_owner_and_assign_multiple_apartments(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج آفتاب']);
        Sanctum::actingAs($manager);

        $ownerId = $this->postJson('/api/owners', [
            'name' => 'مالک نمونه',
            'mobile' => '09121111111',
            'password' => '123456',
            'password_confirmation' => '123456',
        ])->assertCreated()->assertJsonPath('data.role', 'owner')->json('data.id');

        $this->assertDatabaseHas('users', ['id' => $ownerId, 'role' => 'owner', 'created_by' => $manager->id]);

        foreach (['۱', '۲'] as $number) {
            $this->postJson("/api/buildings/{$building->id}/apartments", [
                'number' => $number,
                'owner_id' => $ownerId,
            ])->assertCreated()->assertJsonPath('data.owner.id', $ownerId);
        }

        $this->assertDatabaseCount('apartments', 2);
    }

    public function test_manager_only_lists_owners_in_their_scope(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $visible = User::factory()->create(['role' => 'owner', 'created_by' => $manager->id]);
        $hidden = User::factory()->create(['role' => 'owner', 'created_by' => $otherManager->id]);
        Sanctum::actingAs($manager);

        $this->getJson('/api/owners')
            ->assertOk()
            ->assertJsonFragment(['id' => $visible->id])
            ->assertJsonMissing(['id' => $hidden->id]);
    }

    public function test_manager_cannot_assign_another_managers_owner(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'owner', 'created_by' => $otherManager->id]);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Sanctum::actingAs($manager);

        $this->postJson("/api/buildings/{$building->id}/apartments", ['number' => '۱', 'owner_id' => $owner->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('owner_id');
    }
}
