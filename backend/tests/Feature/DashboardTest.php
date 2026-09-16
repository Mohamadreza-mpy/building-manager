<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\ResidentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_receives_scoped_dashboard_summary(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'آفتاب']);
        $apartment = Apartment::create(['building_id' => $building->id, 'number' => '۱']);
        Charge::create(['building_id' => $building->id, 'apartment_id' => $apartment->id, 'title' => 'شارژ', 'month' => now()->startOfMonth(), 'amount' => 1000]);
        ResidentRequest::create(['apartment_id' => $apartment->id, 'title' => 'خرابی', 'description' => 'شرح']);
        Sanctum::actingAs($manager);

        $this->getJson('/api/dashboard')->assertOk()
            ->assertJsonPath('data.buildings_count', 1)
            ->assertJsonPath('data.apartments_count', 1)
            ->assertJsonPath('data.pending_requests_count', 1)
            ->assertJsonPath('data.monthly_charges_count', 1);
    }

    public function test_resident_receives_personal_dashboard_summary(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'آفتاب']);
        $apartment = Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۲']);
        Charge::create(['building_id' => $building->id, 'apartment_id' => $apartment->id, 'title' => 'شارژ', 'month' => now()->startOfMonth(), 'amount' => 2000]);
        Announcement::create(['building_id' => $building->id, 'title' => 'اطلاعیه', 'body' => 'متن', 'created_by' => $manager->id]);
        Sanctum::actingAs($resident);

        $this->getJson('/api/dashboard')->assertOk()
            ->assertJsonPath('data.current_apartment.number', '۲')
            ->assertJsonPath('data.unpaid_charges_count', 1)
            ->assertJsonPath('data.announcements_count', 1);
    }
}
