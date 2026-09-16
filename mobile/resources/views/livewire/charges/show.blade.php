<main class="module-shell">
    <header class="module-header">
        <a href="{{ $isResident ? route('charges.mine') : ($item ? route('charges.index', $item['building_id']) : route('home')) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">جزئیات شارژ</p><h1>{{ $item['title'] ?? 'شارژ' }}</h1></div>
        <span></span>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    @if ($item)
        <section class="charge-detail-card">
            <div class="payment-status payment-{{ $item['status'] }}"><span>وضعیت پرداخت</span><strong>{{ $item['status_label'] }}</strong></div>
            <div class="total-amount"><span>مبلغ شارژ</span><strong>{{ $this->formatAmount($item['amount']) }} <small>ریال</small></strong></div>
            <dl>
                <div><dt>عنوان</dt><dd>{{ $item['title'] }}</dd></div>
                <div><dt>واحد</dt><dd>واحد {{ $item['apartment']['number'] ?? '—' }}</dd></div>
                <div><dt>ماه شارژ</dt><dd>{{ $item['month'] }}</dd></div>
                <div><dt>تاریخ پرداخت</dt><dd>{{ $this->formatDate($item['paid_at']) }}</dd></div>
            </dl>
        </section>
    @endif
</main>
