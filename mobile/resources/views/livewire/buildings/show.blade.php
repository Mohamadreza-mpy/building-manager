<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('buildings.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">جزئیات ساختمان</p><h1>{{ $item['name'] ?? 'ساختمان' }}</h1></div>
        <a href="{{ route('buildings.edit', $buildingId) }}" wire:navigate class="text-action">ویرایش</a>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    @if ($item)
        <section class="detail-card">
            <div class="detail-hero"><div class="card-icon blue">⌂</div><strong>{{ $item['name'] }}</strong></div>
            <dl>
                <div><dt>نشانی</dt><dd>{{ $item['address'] ?: 'ثبت نشده' }}</dd></div>
                <div><dt>تعداد کل واحدها</dt><dd>{{ $item['total_units'] ?? 'ثبت نشده' }}</dd></div>
                <div><dt>واحدهای ثبت‌شده</dt><dd>{{ $item['apartments_count'] }}</dd></div>
                <div><dt>مدیر</dt><dd>{{ $item['manager']['name'] ?? 'ثبت نشده' }}</dd></div>
            </dl>
        </section>

        <a href="{{ route('apartments.index', $buildingId) }}" wire:navigate class="module-link detail-module-link">
            <div class="card-icon green">▦</div>
            <div><strong>مدیریت واحدها</strong><span>مشاهده و ثبت واحدهای این ساختمان</span></div>
            <b>‹</b>
        </a>
        <a href="{{ route('charges.index', $buildingId) }}" wire:navigate class="module-link detail-module-link charge-module-link">
            <div class="card-icon amber">﷼</div>
            <div><strong>مدیریت شارژها</strong><span>ثبت و مشاهده شارژهای ساختمان</span></div>
            <b>‹</b>
        </a>

        <section class="danger-zone">
            @if ($confirmingDelete)
                <p>از حذف «{{ $item['name'] }}» مطمئن هستید؟ این عملیات قابل بازگشت نیست.</p>
                <div><button type="button" class="danger-button" wire:click="delete" wire:loading.attr="disabled">بله، حذف شود</button><button type="button" class="cancel-button" wire:click="$set('confirmingDelete', false)">انصراف</button></div>
            @else
                <button type="button" class="danger-outline" wire:click="$set('confirmingDelete', true)">حذف ساختمان</button>
            @endif
        </section>
    @endif
</main>
