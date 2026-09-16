<main class="module-shell">
    <header class="module-header">
        <a href="{{ $isResident ? route('home') : route('buildings.show', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">{{ $buildingName }}</p><h1>هزینه‌ها</h1></div>
        <button type="button" class="icon-button" wire:click="refreshExpenses" wire:loading.attr="disabled" aria-label="بروزرسانی">↻</button>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage)<div class="alert alert-error dashboard-alert"><span>{{ $errorMessage }}</span><button wire:click="refreshExpenses">تلاش دوباره</button></div>@endif

    @unless($isResident)<a href="{{ route('expenses.create', $buildingId) }}" wire:navigate class="primary-button add-button">+ ثبت هزینه جدید</a>@endunless

    <section class="expense-list">
        @forelse($expenses as $expense)
            <article class="expense-card">
                <div class="expense-card-head">
                    <div class="card-icon rose">−</div>
                    <div><strong>{{ $expense['title'] }}</strong><span>{{ $expense['category'] }} · {{ $this->formatDate($expense['created_at']) }}</span></div>
                    <p><b>{{ $this->formatAmount($expense['amount']) }}</b><small> ریال</small></p>
                </div>
                @if($expense['description'])<p class="expense-description">{{ $expense['description'] }}</p>@endif
                <footer>
                    <span>ثبت‌کننده: {{ $expense['creator']['name'] ?? 'مدیر ساختمان' }}</span>
                    @if($expense['image_url'])<a href="{{ $expense['image_url'] }}" target="_blank" rel="noopener">مشاهده رسید</a>@else<span>بدون رسید</span>@endif
                </footer>
            </article>
        @empty
            @unless($errorMessage)<div class="empty-state large"><strong>هزینه‌ای ثبت نشده است.</strong><span>{{ $isResident ? 'هنوز هزینه‌ای برای ساختمان ثبت نشده است.' : 'اولین هزینه ساختمان را ثبت کنید.' }}</span></div>@endunless
        @endforelse
    </section>
</main>
