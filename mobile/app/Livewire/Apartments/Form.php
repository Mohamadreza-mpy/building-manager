<?php

namespace App\Livewire\Apartments;

use App\Exceptions\ApiException;
use App\Services\ApartmentService;
use App\Services\OwnerService;
use App\Services\ResidentService;
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

    public string $ownerId = '';

    public array $owners = [];

    public string $residentId = '';

    public array $residents = [];

    public ?string $errorMessage = null;

    public function mount(ApartmentService $service, OwnerService $owners, ResidentService $residents, ?int $building = null, ?int $apartment = null): void
    {
        try {
            $this->owners = array_map(fn ($owner) => $owner->toArray(), $owners->all());
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        try {
            $this->residents = array_map(fn ($resident) => $resident->toArray(), $residents->all());
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        if ($apartment) {
            $item = $service->find($apartment);
            $this->apartmentId = $item->id;
            $this->buildingId = $item->buildingId;
            $this->number = $item->number;
            $this->floor = $item->floor === null ? '' : (string) $item->floor;
            $this->area = $item->area === null ? '' : (string) $item->area;
            $this->ownerId = $item->ownerId === null ? '' : (string) $item->ownerId;
            $this->residentId = $item->residentId === null ? '' : (string) $item->residentId;

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
            'ownerId' => ['nullable', 'integer'],
            'residentId' => ['nullable', 'integer'],
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
            'owner_id' => filled($data['ownerId']) ? (int) $data['ownerId'] : null,
            'resident_id' => filled($data['residentId']) ? (int) $data['residentId'] : null,
        ];

        try {
            $item = $this->apartmentId
                ? $service->update($this->apartmentId, $payload)
                : $service->create($this->buildingId, $payload);

            session()->flash('success_message', $this->apartmentId ? 'واحد با موفقیت ویرایش شد.' : 'واحد با موفقیت ایجاد شد.');
            $this->redirectRoute('apartments.show', ['apartment' => $item->id], navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                $field = $field === 'owner_id' ? 'ownerId' : $field;
                $field = $field === 'resident_id' ? 'residentId' : $field;
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
