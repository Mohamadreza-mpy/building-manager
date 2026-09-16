<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_expense_with_receipt(): void
    {
        Storage::fake('public');
        $manager = User::factory()->create(['role' => 'manager']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Sanctum::actingAs($manager);

        $response = $this->post("/api/buildings/{$building->id}/expenses", [
            'title' => 'تعمیر آسانسور',
            'amount' => 10000000,
            'category' => 'تعمیرات',
            'image' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

        $response->assertCreated()->assertJsonPath('data.title', 'تعمیر آسانسور');
        $path = $response->json('data.image_url');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists('receipts/'.basename($path));
    }

    public function test_resident_can_view_expenses_for_own_building(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        Apartment::create(['building_id' => $building->id, 'resident_id' => $resident->id, 'number' => '۱']);
        $building->expenses()->create(['title' => 'نظافت', 'amount' => 500000, 'category' => 'خدمات', 'created_by' => $manager->id]);
        Sanctum::actingAs($resident);

        $this->getJson("/api/buildings/{$building->id}/expenses")
            ->assertOk()
            ->assertJsonPath('data.0.title', 'نظافت');
    }
}
