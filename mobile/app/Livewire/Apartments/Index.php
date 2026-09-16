<?php

namespace App\Livewire\Apartments;

use App\Exceptions\ApiException;
use App\Services\ApartmentService;
use App\Services\BuildingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public int $buildingId;

    public string $buildingName = '';

    public array $apartments = [];

    public ?string $errorMessage = null;

    public function mount(ApartmentService $apartments, BuildingService $buildings, int $building): void
    {
        $this->buildingId = $building;
        $this->loadData($apartments, $buildings);
    }

    public function refreshApartments(ApartmentService $apartments, BuildingService $buildings): void
    {
        $this->loadData($apartments, $buildings);
    }

    public function render(): View
    {
        return view('livewire.apartments.index');
    }

    private function loadData(ApartmentService $apartments, BuildingService $buildings): void
    {
        $this->errorMessage = null;

        try {
            $this->buildingName = $buildings->find($this->buildingId)->name;
            $this->apartments = array_map(fn ($item) => $item->toArray(), $apartments->all($this->buildingId));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
