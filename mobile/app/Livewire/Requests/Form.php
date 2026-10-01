<?php

namespace App\Livewire\Requests;

use App\Exceptions\ApiException;
use App\Services\DashboardService;
use App\Services\ResidentRequestService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?int $apartmentId = null;

    public string $apartmentLabel = '';

    public string $title = '';

    public string $description = '';

    public ?string $errorMessage = null;

    public function mount(DashboardService $dashboard): void
    {
        try {
            $apartment = $dashboard->load()->data['current_apartment'] ?? null;
            $this->apartmentId = isset($apartment['id']) ? (int) $apartment['id'] : null;
            $this->apartmentLabel = $apartment ? 'واحد '.$apartment['number'].' · '.($apartment['building']['name'] ?? 'ساختمان') : '';
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function save(ResidentRequestService $service): void
    {
        if (! $this->apartmentId) {
            $this->errorMessage = 'برای ثبت درخواست باید ابتدا یک واحد به حساب شما اختصاص داده شود.';

            return;
        }

        $data = $this->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
        ], [
            'title.required' => 'وارد کردن عنوان درخواست الزامی است.',
            'title.max' => 'عنوان درخواست نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
            'description.required' => 'وارد کردن شرح درخواست الزامی است.',
        ]);

        try {
            $service->create(['apartment_id' => $this->apartmentId, ...$data]);
            session()->flash('success_message', 'درخواست با موفقیت ثبت شد.');
            $this->redirectRoute('requests.index', navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.requests.form');
    }
}
