<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مدیریت</p><h1>مالکین</h1></div>
        <button type="button" class="icon-button" wire:click="searchOwners" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <a href="{{ route('owners.create') }}" wire:navigate class="primary-button add-button">+ ثبت مالک جدید</a>

    <form wire:submit="searchOwners" class="form-card form-stack" role="search">
        <div class="field-group">
            <label for="search">جستجوی مالک</label>
            <div class="input-wrap"><input id="search" wire:model="search" placeholder="نام یا شماره موبایل" autocomplete="off"></div>
        </div>
        <div class="search-actions">
            <button class="primary-button search-button" type="submit" wire:loading.attr="disabled" wire:target="searchOwners">
                <span wire:loading.remove wire:target="searchOwners" class="search-button-content"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"></circle><path d="m15.5 15.5 4.2 4.2"></path></svg>جستجو</span>
                <span wire:loading.flex wire:target="searchOwners" class="loading-label"><i class="spinner"></i>در حال جستجو…</span>
            </button>
            @if($search)<button type="button" class="cancel-button search-clear" wire:click="resetSearch" wire:loading.attr="disabled" wire:target="resetSearch">پاک کردن</button>@endif
        </div>
    </form>

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
            @unless($errorMessage)<div class="empty-state large"><strong>{{ $search ? 'مالکی با این مشخصات پیدا نشد.' : 'مالکی ثبت نشده است.' }}</strong><span>{{ $search ? 'نام یا شماره موبایل را بررسی کنید یا جستجو را پاک کنید.' : 'مالک جدید را ثبت و سپس از فرم واحد به او اختصاص دهید.' }}</span></div>@endunless
        @endforelse
    </section>
</main>
