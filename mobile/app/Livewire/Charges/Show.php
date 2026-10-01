<?php

namespace App\Livewire\Charges;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use App\Services\ChargeService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.app')]
class Show extends Component
{
    use WithFileUploads;

    public int $chargeId;

    public array $item = [];

    public bool $isResident = false;

    public bool $isOwner = false;

    public $receipt = null;

    public ?string $errorMessage = null;

    public function mount(ChargeService $service, AuthService $auth, int $charge): void
    {
        $this->chargeId = $charge;
        $role = $auth->currentUser()?->role;
        $this->isResident = $role === 'resident';
        $this->isOwner = $role === 'owner';

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

    public function submitReceipt(ChargeService $service): void
    {
        $this->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,heic', 'max:1024'],
        ], [
            'receipt.required' => 'انتخاب تصویر رسید الزامی است.',
            'receipt.mimes' => 'فرمت رسید فقط باید JPG، PNG یا HEIC باشد.',
            'receipt.max' => 'حجم تصویر رسید باید کمتر از ۱ مگابایت باشد.',
        ]);

        try {
            $this->item = $service->submitReceipt($this->chargeId, $this->receipt->getRealPath(), $this->receipt->getMimeType(), $this->receipt->getClientOriginalName())->toArray();
            $this->receipt = null;
            session()->flash('success_message', 'رسید پرداخت ثبت شد و پس از بررسی مدیر تأیید می‌شود.');
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                $field = $field === 'image' ? 'receipt' : $field;
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }
            $this->errorMessage = $exception->getMessage();
        }
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
