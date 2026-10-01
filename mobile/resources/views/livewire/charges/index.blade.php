<main class="module-shell">
    <header class="module-header">
        <a href="{{ ($isResident || $isOwner) ? route('home') : route('buildings.show', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">{{ ($isResident || $isOwner) ? 'حساب من' : ($buildingName ?: 'ساختمان') }}</p><h1>{{ ($isResident || $isOwner) ? 'شارژهای من' : 'شارژها' }}</h1></div>
        <button type="button" class="icon-button" wire:click="refreshCharges" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage)
        <div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshCharges">تلاش دوباره</button></div>
    @endif

    @unless($isResident || $isOwner)
        <a href="{{ route('charges.create', $buildingId) }}" wire:navigate class="primary-button add-button">+ ثبت شارژ جدید</a>
    @endunless

    <section class="charge-list">
        @forelse ($charges as $charge)
            <a href="{{ route('charges.show', $charge['id']) }}" wire:navigate class="charge-card">
                <div class="charge-card-top">
                    <div><strong>{{ $charge['title'] }}</strong><span>واحد {{ $charge['apartment']['number'] ?? '—' }} · {{ $charge['month'] }}</span></div>
                    <span class="status-badge status-{{ $charge['status'] }}">{{ $charge['status_label'] }}</span>
                </div>
                <div class="charge-amount"><strong>{{ $this->formatAmount($charge['amount']) }}</strong><span>ریال</span><b>‹</b></div>
            </a>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>شارژی برای نمایش وجود ندارد.</strong><span>{{ ($isResident || $isOwner) ? 'هنوز شارژی برای واحد شما ثبت نشده است.' : 'اولین شارژ ساختمان را ثبت کنید.' }}</span></div>@endunless
        @endforelse
    </section>
</main>
