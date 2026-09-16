<?php

namespace App\Livewire\Auth;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Login extends Component
{
    public string $mobile = '';

    public string $password = '';

    public ?string $errorMessage = null;

    public function mount(AuthService $auth): mixed
    {
        $this->errorMessage = session('auth_error');

        if ($auth->hasToken()) {
            try {
                if ($auth->currentUser(refresh: true)) {
                    return $this->redirectRoute('home', navigate: true);
                }
            } catch (ApiException) {
                // The API client clears invalid sessions. The login form remains available.
            }
        }

        return null;
    }

    public function login(AuthService $auth): void
    {
        $validated = $this->validate(
            [
                'mobile' => ['required', 'regex:/^09\d{9}$/'],
                'password' => ['required', 'string', 'min:6'],
            ],
            [
                'mobile.required' => 'وارد کردن شماره موبایل الزامی است.',
                'mobile.regex' => 'شماره موبایل معتبر نیست.',
                'password.required' => 'وارد کردن رمز عبور الزامی است.',
                'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
            ],
            [
                'mobile' => 'شماره موبایل',
                'password' => 'رمز عبور',
            ],
        );

        $this->errorMessage = null;

        try {
            $auth->login($validated['mobile'], $validated['password']);
            $this->password = '';
            $this->redirectRoute('home', navigate: true);
        } catch (ApiException $exception) {
            foreach ($exception->errors as $field => $messages) {
                foreach ((array) $messages as $message) {
                    $this->addError($field, (string) $message);
                }
            }

            $this->errorMessage = $exception->getMessage();
            $this->password = '';
        }
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
