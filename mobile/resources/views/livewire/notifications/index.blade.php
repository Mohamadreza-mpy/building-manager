<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مرکز پیام‌ها</p><h1>اعلان‌ها @if($unreadCount)<span class="unread-counter">{{ $unreadCount }}</span>@endif</h1></div>
        <button type="button" class="icon-button" wire:click="refreshNotifications" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if($errorMessage)<div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshNotifications">تلاش دوباره</button></div>@endif
    @if($pushMessage)<div class="alert push-alert">{{ $pushMessage }}</div>@endif

    <section class="notification-actions">
        <button type="button" wire:click="enablePushNotifications">فعال‌سازی اعلان دستگاه</button>
        @if($unreadCount)<button type="button" wire:click="markAllAsRead" wire:loading.attr="disabled">خواندن همه</button>@endif
    </section>

    <section class="notification-list">
        @forelse($notifications as $notification)
            <article class="notification-card {{ $notification['read_at'] ? 'is-read' : 'is-unread' }}">
                <button type="button" class="notification-main" wire:click="markAsRead('{{ $notification['id'] }}')">
                    <span class="notification-dot"></span>
                    <div><strong>{{ $notification['title'] }}</strong><p>{{ $notification['body'] }}</p><small>{{ $this->formatDate($notification['created_at']) }}</small></div>
                </button>
                @if($this->targetFor($notification))<a href="{{ $this->targetFor($notification) }}" wire:navigate class="notification-link" wire:click="markAsRead('{{ $notification['id'] }}')">مشاهده جزئیات ‹</a>@endif
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>اعلان جدیدی ندارید.</strong><span>رویدادهای شارژ، اطلاعیه و پاسخ درخواست‌ها اینجا نمایش داده می‌شوند.</span></div>@endunless
        @endforelse
    </section>
</main>
