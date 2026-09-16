<?php

namespace App\Livewire\Expenses;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\BuildingService;
use App\Services\ExpenseService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public int $buildingId;

    public string $buildingName = '';

    public bool $isResident = false;

    public array $expenses = [];

    public ?string $errorMessage = null;

    public function mount(ExpenseService $expenses, BuildingService $buildings, AuthService $auth, int $building): void
    {
        $this->buildingId = $building;
        $this->isResident = $auth->currentUser()?->role === 'resident';
        $this->loadExpenses($expenses, $buildings);
    }

    public function refreshExpenses(ExpenseService $expenses, BuildingService $buildings): void
    {
        $this->loadExpenses($expenses, $buildings);
    }

    public function render(): View
    {
        return view('livewire.expenses.index');
    }

    public function formatAmount(int|float|string|null $amount): string
    {
        return strtr(number_format((float) ($amount ?? 0), 0, '.', ','), ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    public function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d') : '—';
    }

    private function loadExpenses(ExpenseService $expenses, BuildingService $buildings): void
    {
        $this->errorMessage = null;

        try {
            $this->buildingName = $this->isResident ? 'ساختمان من' : $buildings->find($this->buildingId)->name;
            $this->expenses = array_map(fn ($item) => $item->toArray(), $expenses->all($this->buildingId));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
