<?php

namespace App\Livewire\Requests;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\ResidentRequestService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $isResident = false;

    public array $requests = [];

    public ?string $errorMessage = null;

    public function mount(ResidentRequestService $requests, AuthService $auth): void
    {
        $this->isResident = $auth->currentUser()?->role === 'resident';
        $this->loadRequests($requests);
    }

    public function refreshRequests(ResidentRequestService $requests): void
    {
        $this->loadRequests($requests);
    }

    public function render(): View
    {
        return view('livewire.requests.index');
    }

    public function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d H:i') : '—';
    }

    private function loadRequests(ResidentRequestService $service): void
    {
        $this->errorMessage = null;

        try {
            $this->requests = array_map(fn ($item) => $item->toArray(), $service->all());
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
