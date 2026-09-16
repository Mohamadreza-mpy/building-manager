<?php

namespace App\Livewire\Charges;

use App\Exceptions\ApiException;
use App\Services\ApartmentService;
use App\Services\ChargeService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public int $buildingId;

    public array $apartments = [];

    public string $apartmentId = '';

    public string $title = '';

    public string $month = '';

    public string $amount = '';

    public ?string $errorMessage = null;

    public function mount(ApartmentService $apartments, int $building): void
    {
        $this->buildingId = $building;
        $this->month = now()->format('Y-m');

        try {
            $this->apartments = array_map(fn ($item) => $item->toArray(), $apartments->all($building));
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function save(ChargeService $service): void
    {
        $data = $this->validate([
            'apartmentId' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:150'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ], [
            'apartmentId.required' => 'انتخاب واحد الزامی است.',
            'apartmentId.integer' => 'واحد انتخاب‌شده معتبر نیست.',
            'title.required' => 'وارد کردن عنوان شارژ الزامی است.',
            'title.max' => 'عنوان شارژ نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
            'month.required' => 'وارد کردن ماه شارژ الزامی است.',
            'month.date_format' => 'ماه شارژ معتبر نیست.',
            'amount.required' => 'وارد کردن مبلغ الزامی است.',
            'amount.numeric' => 'مبلغ باید عدد باشد.',
            'amount.min' => 'مبلغ باید بیشتر از صفر باشد.',
        ]);

        try {
            $charge = $service->create($this->buildingId, [
                'apartment_id' => (int) $data['apartmentId'],
                'title' => $data['title'],
                'month' => $data['month'],
                'amount' => (float) $data['amount'],
            ]);

            session()->flash('success_message', 'شارژ با موفقیت ایجاد شد.');
            $this->redirectRoute('charges.show', ['charge' => $charge->id], navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                $field = $field === 'apartment_id' ? 'apartmentId' : $field;
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.charges.form');
    }
}
