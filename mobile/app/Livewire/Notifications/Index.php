<?php

namespace App\Livewire\Notifications;

use App\Exceptions\ApiException;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\PushNotification\TokenGenerated;
use Native\Mobile\Facades\PushNotifications;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public array $notifications = [];

    public int $unreadCount = 0;

    public ?string $errorMessage = null;

    public ?string $pushMessage = null;

    public function mount(NotificationService $service): void
    {
        $this->loadNotifications($service);
    }

    public function refreshNotifications(NotificationService $service): void
    {
        $this->loadNotifications($service);
    }

    public function markAsRead(NotificationService $service, string $id): void
    {
        try {
            $updated = $service->markAsRead($id)->toArray();
            $this->notifications = array_map(fn (array $item) => $item['id'] === $id ? $updated : $item, $this->notifications);
            $this->updateUnreadCount();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function markAllAsRead(NotificationService $service): void
    {
        try {
            foreach ($this->notifications as $index => $item) {
                if ($item['read_at'] === null) {
                    $this->notifications[$index] = $service->markAsRead($item['id'])->toArray();
                }
            }
            $this->updateUnreadCount();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function enablePushNotifications(): void
    {
        if (! config('push.enabled')) {
            $this->pushMessage = 'زیرساخت Push آماده است؛ برای فعال‌سازی نسخه نهایی باید تنظیمات Firebase اضافه شود.';

            return;
        }

        PushNotifications::enroll()->id('building-manager-push')->enroll();
        $this->pushMessage = 'درخواست دسترسی اعلان به دستگاه ارسال شد.';
    }

    #[OnNative(TokenGenerated::class)]
    public function handlePushToken(NotificationService $service, string $token, ?string $id = null): void
    {
        if ($id !== null && $id !== 'building-manager-push') {
            return;
        }

        try {
            $service->registerDevice($token);
            $this->pushMessage = 'اعلان‌های دستگاه با موفقیت فعال شدند.';
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.notifications.index');
    }

    public function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d H:i') : '—';
    }

    public function targetFor(array $notification): ?string
    {
        return match ($notification['type']) {
            'charge_created' => isset($notification['meta']['charge_id']) ? route('charges.show', $notification['meta']['charge_id']) : null,
            'request_answered' => route('requests.index'),
            default => null,
        };
    }

    private function loadNotifications(NotificationService $service): void
    {
        $this->errorMessage = null;

        try {
            $this->notifications = array_map(fn ($item) => $item->toArray(), $service->all());
            $this->updateUnreadCount();
        } catch (ApiException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    private function updateUnreadCount(): void
    {
        $this->unreadCount = count(array_filter($this->notifications, fn (array $item) => $item['read_at'] === null));
    }
}
