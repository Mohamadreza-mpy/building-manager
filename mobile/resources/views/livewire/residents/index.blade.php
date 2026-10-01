<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مدیریت</p><h1>ساکنین</h1></div>
        <button type="button" class="icon-button" wire:click="searchResidents" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="searchResidents" class="form-card form-stack">
        <div class="field-group"><label for="search">جستجوی ساکن</label><div class="input-wrap"><input id="search" wire:model="search" placeholder="نام یا شماره موبایل"></div></div>
        <div class="button-row"><button class="primary-button" type="submit">جستجو</button>@if($search)<button type="button" class="cancel-button" wire:click="resetSearch">پاک کردن</button>@endif</div>
    </form>

    <a href="{{ route('residents.create') }}" wire:navigate class="primary-button add-button">+ ثبت ساکن جدید</a>

    <section class="apartment-list">
        @forelse ($residents as $resident)
            <article class="apartment-card">
                <div class="unit-badge">{{ mb_substr($resident['name'], 0, 1) }}</div>
                <div class="apartment-card-body">
                    <strong>{{ $resident['name'] }}</strong>
                    <span>{{ $resident['mobile'] }}@if($resident['email']) · {{ $resident['email'] }}@endif</span>
                    <small>{{ $resident['apartments_count'] }} واحد محل سکونت</small>
                </div>
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>ساکنی ثبت نشده است.</strong><span>ساکن جدید را ثبت و سپس از فرم واحد به او اختصاص دهید.</span></div>@endunless
        @endforelse
    </section>
</main>
