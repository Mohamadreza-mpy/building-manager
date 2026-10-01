<?php

namespace App\Livewire;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public array $user = [];

    public ?string $errorMessage = null;

    public array $dashboard = [];

    public bool $isResident = false;

    public bool $isOwner = false;

    public ?string $updatedAt = null;

    public function mount(AuthService $auth, DashboardService $dashboardService): void
    {
        $user = $auth->currentUser(refresh: true);

        if ($user) {
            $this->user = $user->toArray();
            $this->user['role_label'] = $user->roleLabel();
        }

        $this->loadDashboard($dashboardService);

        $this->errorMessage ??= session()->pull('error_message');
    }

    public function refreshDashboard(DashboardService $dashboardService): void
    {
        $this->loadDashboard($dashboardService);
    }

    public function logout(AuthService $auth): void
    {
        try {
            $auth->logout();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        $this->redirectRoute('login', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.home');
    }

    public function number(int|string|null $value): string
    {
        $formatted = number_format((float) ($value ?? 0), 0, '.', ',');

        return strtr($formatted, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    private function loadDashboard(DashboardService $service): void
    {
        $this->errorMessage = null;

        try {
            $dashboard = $service->load();
            $this->dashboard = $dashboard->data;
            $this->isResident = $dashboard->isResident();
            $this->isOwner = $dashboard->isOwner();
            $this->updatedAt = now()->format('H:i');
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }
}
