<?php

namespace App\Livewire\Owners;

use App\Exceptions\ApiException;
use App\Services\OwnerService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $search = '';

    public array $owners = [];

    public ?string $errorMessage = null;

    public function mount(OwnerService $service): void
    {
        $this->loadOwners($service);
    }

    public function searchOwners(OwnerService $service): void
    {
        $this->loadOwners($service);
    }

    public function resetSearch(OwnerService $service): void
    {
        $this->search = '';
        $this->loadOwners($service);
    }

    public function render(): View
    {
        return view('livewire.owners.index');
    }

    private function loadOwners(OwnerService $service): void
    {
        $this->errorMessage = null;

        try {
            $this->owners = array_map(fn ($owner) => $owner->toArray(), $service->all($this->search));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
