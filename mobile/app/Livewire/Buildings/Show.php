<?php

namespace App\Livewire\Buildings;

use App\Exceptions\ApiException;
use App\Services\BuildingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public int $buildingId;

    public array $item = [];

    public bool $confirmingDelete = false;

    public ?string $errorMessage = null;

    public function mount(BuildingService $service, int $building): void
    {
        $this->buildingId = $building;
        $this->loadBuilding($service);
    }

    public function delete(BuildingService $service): void
    {
        try {
            $service->delete($this->buildingId);
            session()->flash('success_message', 'ساختمان با موفقیت حذف شد.');
            $this->redirectRoute('buildings.index', navigate: true);
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->confirmingDelete = false;
        }
    }

    public function render(): View
    {
        return view('livewire.buildings.show');
    }

    private function loadBuilding(BuildingService $service): void
    {
        try {
            $this->item = $service->find($this->buildingId)->toArray();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
