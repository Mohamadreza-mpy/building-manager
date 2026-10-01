<?php

namespace App\Livewire\Charges;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\BuildingService;
use App\Services\ChargeService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public ?int $buildingId = null;

    public string $buildingName = '';

    public bool $isResident = false;

    public bool $isOwner = false;

    public array $charges = [];

    public ?string $errorMessage = null;

    public function mount(ChargeService $charges, AuthService $auth, BuildingService $buildings, ?int $building = null): void
    {
        $this->buildingId = $building;
        $role = $auth->currentUser()?->role;
        $this->isResident = $role === 'resident';
        $this->isOwner = $role === 'owner';
        $this->loadCharges($charges, $buildings);
    }

    public function refreshCharges(ChargeService $charges, BuildingService $buildings): void
    {
        $this->loadCharges($charges, $buildings);
    }

    public function render(): View
    {
        return view('livewire.charges.index');
    }

    public function formatAmount(int|float|string|null $amount): string
    {
        return strtr(number_format((float) ($amount ?? 0), 0, '.', ','), ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    private function loadCharges(ChargeService $charges, BuildingService $buildings): void
    {
        $this->errorMessage = null;

        try {
            if ($this->isResident || $this->isOwner) {
                $items = $charges->mine();
            } else {
                abort_unless($this->buildingId, 404);
                $this->buildingName = $buildings->find($this->buildingId)->name;
                $items = $charges->forBuilding($this->buildingId);
            }

            $this->charges = array_map(fn ($item) => $item->toArray(), $items);
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
