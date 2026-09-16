<?php

namespace App\Livewire\Expenses;

use App\Exceptions\ApiException;
use App\Services\ExpenseService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\Gallery\MediaSelected;
use Native\Mobile\Facades\Camera;

#[Layout('components.layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public int $buildingId;

    public string $title = '';

    public string $amount = '';

    public string $category = '';

    public string $description = '';

    public $receipt = null;

    public ?string $nativeReceiptPath = null;

    public ?string $nativeReceiptMime = null;

    public ?string $nativeReceiptName = null;

    public ?string $errorMessage = null;

    public function mount(int $building): void
    {
        $this->buildingId = $building;
    }

    public function takeReceiptPhoto(): void
    {
        $this->errorMessage = null;

        if (! Camera::getPhoto()->id('expense-receipt')->start()) {
            $this->errorMessage = 'دوربین در این محیط در دسترس نیست. از گزینه انتخاب تصویر استفاده کنید.';
        }
    }

    public function pickReceiptImage(): void
    {
        $this->errorMessage = null;

        if (! Camera::pickImages('image')->id('expense-receipt')->single()->start()) {
            $this->errorMessage = 'گالری در این محیط در دسترس نیست. از انتخاب فایل استفاده کنید.';
        }
    }

    #[OnNative(PhotoTaken::class)]
    public function handlePhotoTaken(string $path, string $mimeType = 'image/jpeg', ?string $id = null): void
    {
        if ($id === null || $id === 'expense-receipt') {
            $this->setNativeReceipt($path, $mimeType);
        }
    }

    #[OnNative(MediaSelected::class)]
    public function handleMediaSelected(bool $success, array $files = [], int $count = 0, ?string $error = null, bool $cancelled = false, ?string $id = null): void
    {
        if (! $success || $cancelled || ($id !== null && $id !== 'expense-receipt')) {
            return;
        }

        $file = $files[0] ?? null;

        if (is_array($file) && isset($file['path'])) {
            $this->setNativeReceipt((string) $file['path'], (string) ($file['mimeType'] ?? 'image/jpeg'));
        }
    }

    public function removeReceipt(): void
    {
        $this->receipt = null;
        $this->nativeReceiptPath = null;
        $this->nativeReceiptMime = null;
        $this->nativeReceiptName = null;
        $this->resetValidation('receipt');
    }

    public function save(ExpenseService $service): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'receipt' => ['nullable', 'image', 'max:5120'],
        ], [
            'title.required' => 'وارد کردن عنوان هزینه الزامی است.',
            'title.max' => 'عنوان هزینه نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
            'amount.required' => 'وارد کردن مبلغ الزامی است.',
            'amount.numeric' => 'مبلغ باید عدد باشد.',
            'amount.min' => 'مبلغ باید بیشتر از صفر باشد.',
            'category.required' => 'وارد کردن دسته‌بندی الزامی است.',
            'receipt.image' => 'رسید باید یک تصویر باشد.',
            'receipt.max' => 'حجم تصویر رسید نباید بیشتر از ۵ مگابایت باشد.',
        ]);

        $filePath = $this->nativeReceiptPath;
        $mimeType = $this->nativeReceiptMime;
        $fileName = $this->nativeReceiptName;

        if ($this->receipt) {
            $filePath = $this->receipt->getRealPath();
            $mimeType = $this->receipt->getMimeType();
            $fileName = $this->receipt->getClientOriginalName();
        }

        try {
            $service->create($this->buildingId, [
                'title' => $data['title'],
                'amount' => (float) $data['amount'],
                'category' => $data['category'],
                'description' => filled($data['description']) ? $data['description'] : null,
            ], $filePath, $mimeType, $fileName);

            session()->flash('success_message', 'هزینه با موفقیت ثبت شد.');
            $this->redirectRoute('expenses.index', ['building' => $this->buildingId], navigate: true);
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

    public function render(): View
    {
        return view('livewire.expenses.form');
    }

    private function setNativeReceipt(string $path, string $mimeType): void
    {
        if (! is_file($path) || ! str_starts_with($mimeType, 'image/')) {
            $this->errorMessage = 'تصویر انتخاب‌شده معتبر نیست.';

            return;
        }

        if (filesize($path) > 5 * 1024 * 1024) {
            $this->errorMessage = 'حجم تصویر رسید نباید بیشتر از ۵ مگابایت باشد.';

            return;
        }

        $this->receipt = null;
        $this->nativeReceiptPath = $path;
        $this->nativeReceiptMime = $mimeType;
        $this->nativeReceiptName = basename($path);
        $this->errorMessage = null;
    }
}
