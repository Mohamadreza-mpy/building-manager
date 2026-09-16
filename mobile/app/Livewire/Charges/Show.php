<?php

namespace App\Livewire\Charges;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\ChargeService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public int $chargeId;

    public array $item = [];

    public bool $isResident = false;

    public ?string $errorMessage = null;

    public function mount(ChargeService $service, AuthService $auth, int $charge): void
    {
        $this->chargeId = $charge;
        $this->isResident = $auth->currentUser()?->role === 'resident';

        try {
            $this->item = $service->find($charge)->toArray();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.charges.show');
    }

    public function formatAmount(int|float|string|null $amount): string
    {
        return strtr(number_format((float) ($amount ?? 0), 0, '.', ','), ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    public function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d H:i') : 'پرداخت نشده';
    }
}
