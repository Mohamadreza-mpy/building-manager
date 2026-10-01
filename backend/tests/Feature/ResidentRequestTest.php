<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\ResidentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResidentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_create_request_for_own_apartment(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        $apartment = Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۱']);
        Sanctum::actingAs($resident);

        $this->postJson('/api/requests', ['apartment_id' => $apartment->id, 'title' => 'خرابی آسانسور', 'description' => 'آسانسور متوقف شده است.'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('requests', ['apartment_id' => $apartment->id, 'title' => 'خرابی آسانسور']);
    }

    public function test_manager_can_respond_to_building_request(): void
    {
        Notification::fake();
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        $apartment = Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۱']);
        $request = ResidentRequest::create(['apartment_id' => $apartment->id, 'title' => 'خرابی', 'description' => 'شرح']);
        Sanctum::actingAs($manager);

        $this->putJson("/api/requests/{$request->id}", ['status' => 'completed', 'response' => 'مشکل برطرف شد.'])
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.response', 'مشکل برطرف شد.');
    }
}
