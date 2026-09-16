<?php

namespace App\Livewire\Apartments;

use App\Exceptions\ApiException;
use App\Services\ApartmentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?int $apartmentId = null;

    public int $buildingId;

    public string $number = '';

    public string $floor = '';

    public string $area = '';

    public ?string $errorMessage = null;

    public function mount(ApartmentService $service, ?int $building = null, ?int $apartment = null): void
    {
        if ($apartment) {
            $item = $service->find($apartment);
            $this->apartmentId = $item->id;
            $this->buildingId = $item->buildingId;
            $this->number = $item->number;
            $this->floor = $item->floor === null ? '' : (string) $item->floor;
            $this->area = $item->area === null ? '' : (string) $item->area;

            return;
        }

        abort_unless($building, 404);
        $this->buildingId = $building;
    }

    public function save(ApartmentService $service): void
    {
        $data = $this->validate([
            'number' => ['required', 'string', 'max:20'],
            'floor' => ['nullable', 'integer'],
            'area' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ], [
            'number.required' => 'وارد کردن شماره واحد الزامی است.',
            'number.max' => 'شماره واحد نمی‌تواند بیشتر از ۲۰ کاراکتر باشد.',
            'floor.integer' => 'طبقه باید عدد صحیح باشد.',
            'area.numeric' => 'مساحت باید عدد باشد.',
            'area.min' => 'مساحت نمی‌تواند منفی باشد.',
            'area.max' => 'مساحت بیش از حد مجاز است.',
        ]);

        $payload = [
            'number' => $data['number'],
            'floor' => filled($data['floor']) ? (int) $data['floor'] : null,
            'area' => filled($data['area']) ? (float) $data['area'] : null,
        ];

        try {
            $item = $this->apartmentId
                ? $service->update($this->apartmentId, $payload)
                : $service->create($this->buildingId, $payload);

            session()->flash('success_message', $this->apartmentId ? 'واحد با موفقیت ویرایش شد.' : 'واحد با موفقیت ایجاد شد.');
            $this->redirectRoute('apartments.show', ['apartment' => $item->id], navigate: true);
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
        return view('livewire.apartments.form');
    }
}
