<?php

namespace App\Livewire;

use App\Exceptions\ApiException;
use App\Services\AuthService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public array $user = [];

    public ?string $errorMessage = null;

    public function mount(AuthService $auth): void
    {
        $user = $auth->currentUser(refresh: true);

        if ($user) {
            $this->user = $user->toArray();
            $this->user['role_label'] = $user->roleLabel();
        }
    }

    public function logout(AuthService $auth): void
    {
        try {
            $auth->logout();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        $this->redirectRoute('login', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.home');
    }
}
