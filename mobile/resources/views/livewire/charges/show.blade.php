<main class="module-shell">
    <header class="module-header">
        <a href="{{ ($isResident || $isOwner) ? route('charges.mine') : ($item ? route('charges.index', $item['building_id']) : route('home')) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
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

        @if($item['payment_receipt_url'] ?? null)
            <section class="detail-card">
                <div class="section-heading"><h2>رسید پرداخت</h2><span>{{ $item['status'] === 'paid' ? 'تأیید شده' : 'در انتظار بررسی مدیر' }}</span></div>
                <a href="{{ $item['payment_receipt_url'] }}" target="_blank" rel="noopener"><img src="{{ $item['payment_receipt_url'] }}" alt="تصویر رسید پرداخت" style="width:100%;max-height:420px;object-fit:contain;border-radius:16px"></a>
                @if($item['receipt_submitted_at'])<small>زمان ارسال: {{ $this->formatDate($item['receipt_submitted_at']) }}</small>@endif
            </section>
        @endif

        @if($isOwner && $item['status'] !== 'paid')
            <form wire:submit="submitReceipt" class="form-card form-stack">
                <div class="section-heading"><h2>{{ ($item['payment_receipt_url'] ?? null) ? 'جایگزینی رسید' : 'پرداخت شارژ' }}</h2></div>
                <p>تصویر رسید بانکی را با فرمت JPG، PNG یا HEIC و حجم کمتر از ۱ مگابایت انتخاب کنید.</p>
                <div class="field-group">
                    <label for="receipt">تصویر رسید <span class="required">*</span></label>
                    <div class="input-wrap @error('receipt') has-error @enderror"><input id="receipt" wire:model="receipt" type="file" accept=".jpg,.jpeg,.png,.heic,image/jpeg,image/png,image/heic"></div>
                    @error('receipt')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="primary-button" wire:loading.attr="disabled" wire:target="submitReceipt,receipt"><span wire:loading.remove wire:target="submitReceipt">ارسال رسید پرداخت</span><span wire:loading.flex wire:target="submitReceipt" class="loading-label"><i class="spinner"></i>در حال ارسال…</span></button>
            </form>
        @endif
    @endif
</main>
