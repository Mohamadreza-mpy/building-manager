<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('home') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">{{ $isResident ? 'حساب من' : 'مدیریت' }}</p><h1>{{ $isResident ? 'درخواست‌های من' : 'درخواست‌های ساکنان' }}</h1></div>
        <button type="button" class="icon-button" wire:click="refreshRequests" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if(session('success_message'))<div class="alert alert-success">{{ session('success_message') }}</div>@endif
    @if($errorMessage)<div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshRequests">تلاش دوباره</button></div>@endif

    @if($isResident)<a href="{{ route('requests.create') }}" wire:navigate class="primary-button add-button">+ ثبت درخواست جدید</a>@endif

    <section class="request-list">
        @forelse($requests as $request)
            <article class="request-card">
                <header><div><strong>{{ $request['title'] }}</strong><span>واحد {{ $request['apartment']['number'] ?? '—' }} · {{ $this->formatDate($request['created_at']) }}</span></div><span class="request-status request-{{ $request['status'] }}">{{ $request['status_label'] }}</span></header>
                <p>{{ $request['description'] }}</p>
                @if($request['response'])<div class="manager-response"><strong>پاسخ مدیر</strong><span>{{ $request['response'] }}</span></div>@endif
                @unless($isResident)<a href="{{ route('requests.respond', $request['id']) }}" wire:navigate>{{ $request['status'] === 'pending' ? 'بررسی درخواست' : 'ویرایش پاسخ' }} ‹</a>@endunless
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>درخواستی وجود ندارد.</strong><span>{{ $isResident ? 'درخواست جدید خود را از این بخش ثبت کنید.' : 'هنوز درخواستی از طرف ساکنان ثبت نشده است.' }}</span></div>@endunless
        @endforelse
    </section>
</main>
