<?php

namespace App\Livewire\Apartments;

use App\Exceptions\ApiException;
use App\Services\ApartmentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public int $apartmentId;

    public array $item = [];

    public bool $confirmingDelete = false;

    public ?string $errorMessage = null;

    public function mount(ApartmentService $service, int $apartment): void
    {
        $this->apartmentId = $apartment;
        $this->loadApartment($service);
    }

    public function delete(ApartmentService $service): void
    {
        try {
            $service->delete($this->apartmentId);
            session()->flash('success_message', 'واحد با موفقیت حذف شد.');
            $this->redirectRoute('apartments.index', ['building' => $this->item['building_id']], navigate: true);
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->confirmingDelete = false;
        }
    }

    public function render(): View
    {
        return view('livewire.apartments.show');
    }

    private function loadApartment(ApartmentService $service): void
    {
        try {
            $this->item = $service->find($this->apartmentId)->toArray();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
