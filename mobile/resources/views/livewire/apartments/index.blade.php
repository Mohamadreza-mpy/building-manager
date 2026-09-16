<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('buildings.show', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">{{ $buildingName ?: 'ساختمان' }}</p><h1>واحدها</h1></div>
        <button type="button" class="icon-button" wire:click="refreshApartments" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage)
        <div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshApartments">تلاش دوباره</button></div>
    @endif

    <a href="{{ route('apartments.create', $buildingId) }}" wire:navigate class="primary-button add-button">+ افزودن واحد</a>

    <section class="apartment-list">
        @forelse ($apartments as $apartment)
            <a href="{{ route('apartments.show', $apartment['id']) }}" wire:navigate class="apartment-card">
                <div class="unit-badge">{{ $apartment['number'] }}</div>
                <div class="apartment-card-body">
                    <strong>واحد {{ $apartment['number'] }}</strong>
                    <span>
                        {{ isset($apartment['floor']) ? 'طبقه '.$apartment['floor'] : 'طبقه ثبت نشده' }}
                        @if(isset($apartment['area'])) · {{ $apartment['area'] }} مترمربع @endif
                    </span>
                    <small>{{ $apartment['resident']['name'] ?? 'بدون ساکن' }}</small>
                </div>
                <b>‹</b>
            </a>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>واحدی ثبت نشده است.</strong><span>اولین واحد این ساختمان را اضافه کنید.</span></div>@endunless
        @endforelse
    </section>
</main>
