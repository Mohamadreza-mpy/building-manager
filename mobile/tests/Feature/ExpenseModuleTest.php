<?php

namespace Tests\Feature;

use App\Contracts\TokenStorage;
use App\Livewire\Expenses\Form;
use App\Livewire\Expenses\Index;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Support\InMemoryTokenStorage;
use Tests\TestCase;

class ExpenseModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $tokens = new InMemoryTokenStorage;
        $tokens->token = 'valid-token';
        $this->app->instance(TokenStorage::class, $tokens);
    }

    public function test_manager_sees_building_expenses_and_receipts(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('manager')));
            }
            if (str_ends_with($request->url(), '/buildings/2')) {
                return Http::response($this->success($this->building()));
            }

            return Http::response($this->success([$this->expense()]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('تعمیر آسانسور')
            ->assertSee('مشاهده رسید')
            ->assertSee('ثبت هزینه جدید');
    }

    public function test_resident_sees_expenses_without_requesting_building_detail(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/auth/me')) {
                return Http::response($this->success($this->user('resident')));
            }

            return Http::response($this->success([$this->expense()]));
        });

        Livewire::test(Index::class, ['building' => 2])
            ->assertSee('تعمیر آسانسور')
            ->assertDontSee('ثبت هزینه جدید');

        Http::assertNotSent(fn (Request $request) => str_ends_with($request->url(), '/buildings/2'));
    }

    public function test_manager_can_create_expense_with_receipt(): void
    {
        Http::fake(['*/buildings/2/expenses' => Http::response($this->success($this->expense()), 201)]);

        Livewire::test(Form::class, ['building' => 2])
            ->set('title', 'تعمیر آسانسور')
            ->set('category', 'تعمیرات')
            ->set('amount', '10000000')
            ->set('description', 'تعویض قطعه')
            ->set('receipt', UploadedFile::fake()->image('receipt.jpg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('expenses.index', 2));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/buildings/2/expenses')
            && collect($request->data())->contains(fn (array $part) => $part['name'] === 'title' && $part['contents'] === 'تعمیر آسانسور')
            && $request->hasFile('image'));
    }

    public function test_expense_form_validates_required_fields(): void
    {
        Http::fake();

        Livewire::test(Form::class, ['building' => 2])
            ->call('save')
            ->assertHasErrors(['title', 'category', 'amount']);

        Http::assertNothingSent();
    }

    private function success(mixed $data): array
    {
        return ['success' => true, 'message' => 'عملیات موفق بود.', 'data' => $data];
    }

    private function user(string $role): array
    {
        return ['id' => 2, 'name' => 'کاربر آزمایشی', 'mobile' => '09120000002', 'email' => null, 'role' => $role];
    }

    private function building(): array
    {
        return ['id' => 2, 'name' => 'ساختمان آفتاب', 'address' => null, 'total_units' => 8, 'apartments_count' => 1, 'manager' => null];
    }

    private function expense(): array
    {
        return ['id' => 7, 'building_id' => 2, 'title' => 'تعمیر آسانسور', 'amount' => '10000000.00', 'category' => 'تعمیرات', 'description' => 'تعویض قطعه', 'image_url' => 'http://api.test/storage/receipts/test.jpg', 'created_by' => 2, 'creator' => ['id' => 2, 'name' => 'مدیر'], 'created_at' => '2026-09-16T10:00:00Z'];
    }
}
