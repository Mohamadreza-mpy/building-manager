<?php

namespace App\Livewire\Requests;

use App\Exceptions\ApiException;
use App\Services\ResidentRequestService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Respond extends Component
{
    public int $requestId;

    public array $item = [];

    public string $status = 'processing';

    public string $response = '';

    public ?string $errorMessage = null;

    public function mount(ResidentRequestService $service, int $residentRequest): void
    {
        $this->requestId = $residentRequest;

        try {
            $request = $service->find($residentRequest);
            $this->item = $request->toArray();
            $this->status = $request->status === 'pending' ? 'processing' : $request->status;
            $this->response = $request->response ?? '';
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function save(ResidentRequestService $service): void
    {
        $data = $this->validate([
            'status' => ['required', 'in:processing,completed,rejected'],
            'response' => ['nullable', 'string', 'required_if:status,completed,rejected'],
        ], [
            'status.required' => 'انتخاب وضعیت الزامی است.',
            'status.in' => 'وضعیت درخواست معتبر نیست.',
            'response.required_if' => 'برای این وضعیت، پاسخ مدیر الزامی است.',
        ]);

        try {
            $service->respond($this->requestId, ['status' => $data['status'], 'response' => filled($data['response']) ? $data['response'] : null]);
            session()->flash('success_message', 'پاسخ درخواست با موفقیت ثبت شد.');
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
        return view('livewire.requests.respond');
    }
}
