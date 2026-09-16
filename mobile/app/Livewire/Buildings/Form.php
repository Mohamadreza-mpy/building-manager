<?php

namespace App\Livewire\Buildings;

use App\Exceptions\ApiException;
use App\Services\BuildingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?int $buildingId = null;

    public string $name = '';

    public string $address = '';

    public string $totalUnits = '';

    public ?string $errorMessage = null;

    public function mount(BuildingService $service, ?int $building = null): void
    {
        if ($building) {
            $item = $service->find($building);
            $this->buildingId = $item->id;
            $this->name = $item->name;
            $this->address = $item->address ?? '';
            $this->totalUnits = $item->totalUnits === null ? '' : (string) $item->totalUnits;
        }
    }

    public function save(BuildingService $service): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'totalUnits' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'وارد کردن نام ساختمان الزامی است.',
            'name.max' => 'نام ساختمان نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'totalUnits.integer' => 'تعداد واحدها باید عدد صحیح باشد.',
            'totalUnits.min' => 'تعداد واحدها باید حداقل یک باشد.',
        ]);

        $payload = ['name' => $data['name'], 'address' => filled($data['address']) ? $data['address'] : null, 'total_units' => filled($data['totalUnits']) ? (int) $data['totalUnits'] : null];

        try {
            $building = $this->buildingId ? $service->update($this->buildingId, $payload) : $service->create($payload);
            session()->flash('success_message', $this->buildingId ? 'ساختمان با موفقیت ویرایش شد.' : 'ساختمان با موفقیت ایجاد شد.');
            $this->redirectRoute('buildings.show', ['building' => $building->id], navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                $field = $field === 'total_units' ? 'totalUnits' : $field;
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.buildings.form');
    }
}
