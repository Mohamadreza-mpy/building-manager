<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مدیریت</p><h1>ساختمان‌ها</h1></div>
        <button type="button" class="icon-button" wire:click="refreshBuildings" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage)
        <div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshBuildings">تلاش دوباره</button></div>
    @endif

    <a href="{{ route('buildings.create') }}" wire:navigate class="primary-button add-button">+ افزودن ساختمان</a>

    <section class="building-list">
        @forelse ($buildings as $building)
            <a href="{{ route('buildings.show', $building['id']) }}" wire:navigate class="building-card">
                <div class="card-icon blue">⌂</div>
                <div class="building-card-body">
                    <strong>{{ $building['name'] }}</strong>
                    <span>{{ $building['address'] ?: 'نشانی ثبت نشده' }}</span>
                    <small>{{ $building['apartments_count'] }} واحد ثبت‌شده @if($building['total_units']) از {{ $building['total_units'] }} واحد @endif</small>
                </div>
                <b>‹</b>
            </a>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>ساختمانی پیدا نشد.</strong><span>برای شروع، اولین ساختمان را اضافه کنید.</span></div>@endunless
        @endforelse
    </section>
</main>
