<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مدیریت</p><h1>مالکین</h1></div>
        <button type="button" class="icon-button" wire:click="searchOwners" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="searchOwners" class="form-card form-stack">
        <div class="field-group">
            <label for="search">جستجوی مالک</label>
            <div class="input-wrap"><input id="search" wire:model="search" placeholder="نام یا شماره موبایل"></div>
        </div>
        <div class="button-row"><button class="primary-button" type="submit">جستجو</button>@if($search)<button type="button" class="cancel-button" wire:click="resetSearch">پاک کردن</button>@endif</div>
    </form>

    <a href="{{ route('owners.create') }}" wire:navigate class="primary-button add-button">+ ثبت مالک جدید</a>

    <section class="apartment-list">
        @forelse ($owners as $owner)
            <article class="apartment-card">
                <div class="unit-badge">{{ mb_substr($owner['name'], 0, 1) }}</div>
                <div class="apartment-card-body">
                    <strong>{{ $owner['name'] }}</strong>
                    <span>{{ $owner['mobile'] }}@if($owner['email']) · {{ $owner['email'] }}@endif</span>
                    <small>{{ $owner['owned_apartments_count'] }} واحد تحت مالکیت</small>
                </div>
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>مالکی ثبت نشده است.</strong><span>مالک جدید را ثبت و سپس از فرم واحد به او اختصاص دهید.</span></div>@endunless
        @endforelse
    </section>
</main>
