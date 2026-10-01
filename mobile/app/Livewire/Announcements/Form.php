<?php

namespace App\Livewire\Announcements;

use App\Exceptions\ApiException;
use App\Services\AnnouncementService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public int $buildingId;

    public string $title = '';

    public string $body = '';

    public ?string $errorMessage = null;

    public function mount(int $building): void
    {
        $this->buildingId = $building;
    }

    public function save(AnnouncementService $service): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
        ], [
            'title.required' => 'وارد کردن عنوان اطلاعیه الزامی است.',
            'title.max' => 'عنوان اطلاعیه نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
            'body.required' => 'وارد کردن متن اطلاعیه الزامی است.',
        ]);

        try {
            $service->create($this->buildingId, $data);
            session()->flash('success_message', 'اطلاعیه با موفقیت منتشر شد.');
            $this->redirectRoute('announcements.index', ['building' => $this->buildingId], navigate: true);
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
        return view('livewire.announcements.form');
    }
}
