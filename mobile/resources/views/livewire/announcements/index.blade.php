<main class="module-shell">
    <header class="module-header">
        <a href="{{ $isResident ? route('home') : route('buildings.show', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">{{ $buildingName }}</p><h1>اطلاعیه‌ها</h1></div>
        <button type="button" class="icon-button" wire:click="refreshAnnouncements" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if(session('success_message'))<div class="alert alert-success">{{ session('success_message') }}</div>@endif
    @if($errorMessage)<div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshAnnouncements">تلاش دوباره</button></div>@endif

    @unless($isResident)<a href="{{ route('announcements.create', $buildingId) }}" wire:navigate class="primary-button add-button">+ انتشار اطلاعیه</a>@endunless

    <section class="full-announcement-list">
        @forelse($announcements as $announcement)
            <article class="announcement-card">
                <header><div class="card-icon violet">●</div><div><strong>{{ $announcement['title'] }}</strong><span>{{ $announcement['creator']['name'] ?? 'مدیر ساختمان' }} · {{ $this->formatDate($announcement['created_at']) }}</span></div></header>
                <p>{{ $announcement['body'] }}</p>
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>اطلاعیه‌ای منتشر نشده است.</strong><span>{{ $isResident ? 'در حال حاضر پیام جدیدی از مدیر ساختمان ندارید.' : 'اولین اطلاعیه ساختمان را منتشر کنید.' }}</span></div>@endunless
        @endforelse
    </section>
</main>
