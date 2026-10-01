<?php

namespace App\Livewire\Residents;

use App\Exceptions\ApiException;
use App\Services\ResidentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public string $name = '';

    public string $mobile = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public ?string $errorMessage = null;

    public function save(ResidentService $service): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'regex:/^09\d{9}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'same:passwordConfirmation'],
        ], [
            'name.required' => 'وارد کردن نام ساکن الزامی است.',
            'mobile.required' => 'وارد کردن شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل معتبر نیست.',
            'email.email' => 'ایمیل معتبر نیست.',
            'password.required' => 'وارد کردن رمز اولیه الزامی است.',
            'password.min' => 'رمز اولیه باید حداقل ۶ کاراکتر باشد.',
            'password.same' => 'تکرار رمز اولیه مطابقت ندارد.',
        ]);

        try {
            $service->create([
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'email' => filled($data['email']) ? $data['email'] : null,
                'password' => $data['password'],
                'password_confirmation' => $this->passwordConfirmation,
            ]);
            session()->flash('success_message', 'ساکن با موفقیت ثبت شد و اکنون قابل تخصیص به واحد است.');
            $this->redirectRoute('residents.index', navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                $field = $field === 'password_confirmation' ? 'passwordConfirmation' : $field;
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.residents.form');
    }
}
