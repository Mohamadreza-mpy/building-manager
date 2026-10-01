<?php

namespace App\Livewire\Announcements;

use App\Exceptions\ApiException;
use App\Services\AnnouncementService;
use App\Services\AuthService;
use App\Services\BuildingService;
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

    public array $announcements = [];

    public ?string $errorMessage = null;

    public function mount(AnnouncementService $announcements, BuildingService $buildings, AuthService $auth, int $building): void
    {
        $this->buildingId = $building;
        $this->isResident = $auth->currentUser()?->role === 'resident';
        $this->loadAnnouncements($announcements, $buildings);
    }

    public function refreshAnnouncements(AnnouncementService $announcements, BuildingService $buildings): void
    {
        $this->loadAnnouncements($announcements, $buildings);
    }

    public function render(): View
    {
        return view('livewire.announcements.index');
    }

    public function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d H:i') : '—';
    }

    private function loadAnnouncements(AnnouncementService $announcements, BuildingService $buildings): void
    {
        $this->errorMessage = null;

        try {
            $this->buildingName = $this->isResident ? 'ساختمان من' : $buildings->find($this->buildingId)->name;
            $this->announcements = array_map(fn ($item) => $item->toArray(), $announcements->all($this->buildingId));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
