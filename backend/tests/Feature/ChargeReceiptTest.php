<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChargeReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_list_charges_and_submit_receipt_for_owned_apartment(): void
    {
        Storage::fake('public');
        [$owner, $manager, $charge] = $this->chargeFixture();
        Sanctum::actingAs($owner);

        $this->getJson('/api/charges')->assertOk()->assertJsonPath('data.0.id', $charge->id);

        $response = $this->postJson("/api/charges/{$charge->id}/receipt", [
            'image' => UploadedFile::fake()->image('receipt.jpg')->size(800),
        ])->assertOk()->assertJsonPath('data.status', 'pending');

        $path = Charge::find($charge->id)->payment_receipt;
        Storage::disk('public')->assertExists($path);
        $response->assertJsonPath('data.receipt_submitted_at', fn ($value) => is_string($value));

        $this->postJson("/api/charges/{$charge->id}/receipt", [
            'image' => UploadedFile::fake()->create('receipt.heic', 700, 'image/heic'),
        ])->assertOk();

        Sanctum::actingAs($manager);
        $this->getJson("/api/charges/{$charge->id}")->assertOk()->assertJsonPath('data.payment_receipt_url', fn ($value) => str_contains($value, '/storage/charge-receipts/'));
    }

    public function test_receipt_must_have_allowed_format_and_be_under_one_megabyte(): void
    {
        Storage::fake('public');
        [$owner, , $charge] = $this->chargeFixture();
        Sanctum::actingAs($owner);

        $this->postJson("/api/charges/{$charge->id}/receipt", ['image' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf')])
            ->assertUnprocessable()->assertJsonValidationErrors('image');

        $this->postJson("/api/charges/{$charge->id}/receipt", ['image' => UploadedFile::fake()->image('receipt.png')->size(1025)])
            ->assertUnprocessable()->assertJsonValidationErrors('image');
    }

    public function test_non_owner_cannot_submit_receipt(): void
    {
        Storage::fake('public');
        [, $manager, $charge] = $this->chargeFixture();
        Sanctum::actingAs($manager);

        $this->postJson("/api/charges/{$charge->id}/receipt", ['image' => UploadedFile::fake()->image('receipt.jpg')])->assertForbidden();
    }

    private function chargeFixture(): array
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $owner = User::factory()->create(['role' => 'owner', 'created_by' => $manager->id]);
        $building = Building::create(['manager_id' => $manager->id, 'name' => 'برج']);
        $apartment = Apartment::create(['building_id' => $building->id, 'owner_id' => $owner->id, 'number' => '۱']);
        $charge = Charge::create(['building_id' => $building->id, 'apartment_id' => $apartment->id, 'title' => 'شارژ', 'month' => '2026-10-01', 'amount' => 500000, 'status' => 'pending']);

        return [$owner, $manager, $charge];
    }
}
