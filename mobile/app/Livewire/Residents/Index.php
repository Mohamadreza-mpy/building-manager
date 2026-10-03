<?php

namespace App\Livewire\Residents;

use App\Exceptions\ApiException;
use App\Services\ResidentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $search = '';

    public array $residents = [];

    public ?string $errorMessage = null;

    public function mount(ResidentService $service): void
    {
        $this->loadResidents($service);
    }

    public function searchResidents(ResidentService $service): void
    {
        $this->search = trim($this->search);
        $this->loadResidents($service);
    }

    public function resetSearch(ResidentService $service): void
    {
        $this->search = '';
        $this->loadResidents($service);
    }

    public function render(): View
    {
        return view('livewire.residents.index');
    }

    private function loadResidents(ResidentService $service): void
    {
        $this->errorMessage = null;

        try {
            $this->residents = array_map(fn ($resident) => $resident->toArray(), $service->all($this->search));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
