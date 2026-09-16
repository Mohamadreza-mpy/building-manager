<?php

namespace App\Livewire\Buildings;

use App\Exceptions\ApiException;
use App\Services\BuildingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public array $buildings = [];

    public ?string $errorMessage = null;

    public function mount(BuildingService $service): void
    {
        $this->loadBuildings($service);
    }

    public function refreshBuildings(BuildingService $service): void
    {
        $this->loadBuildings($service);
    }

    public function render(): View
    {
        return view('livewire.buildings.index');
    }

    private function loadBuildings(BuildingService $service): void
    {
        $this->errorMessage = null;

        try {
            $this->buildings = array_map(fn ($building) => $building->toArray(), $service->all());
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
