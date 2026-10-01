<main class="module-shell">
    <header class="module-header">
        <a href="{{ $item ? route('apartments.index', $item['building_id']) : route('buildings.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">جزئیات واحد</p><h1>واحد {{ $item['number'] ?? '—' }}</h1></div>
        <a href="{{ route('apartments.edit', $apartmentId) }}" wire:navigate class="text-action">ویرایش</a>
    </header>

    @if (session('success_message')) <div class="alert alert-success">{{ session('success_message') }}</div> @endif
    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    @if ($item)
        <section class="detail-card">
            <div class="detail-hero"><div class="unit-badge">{{ $item['number'] }}</div><strong>واحد {{ $item['number'] }}</strong></div>
            <dl>
                <div><dt>ساختمان</dt><dd>{{ $item['building']['name'] ?? 'ساختمان' }}</dd></div>
                <div><dt>طبقه</dt><dd>{{ $item['floor'] ?? 'ثبت نشده' }}</dd></div>
                <div><dt>مساحت</dt><dd>{{ isset($item['area']) ? $item['area'].' مترمربع' : 'ثبت نشده' }}</dd></div>
                <div><dt>مالک</dt><dd>{{ $item['owner']['name'] ?? 'تخصیص داده نشده' }}</dd></div>
                <div><dt>ساکن</dt><dd>{{ $item['resident']['name'] ?? 'تخصیص داده نشده' }}</dd></div>
            </dl>
        </section>

        <section class="danger-zone">
            @if ($confirmingDelete)
                <p>از حذف واحد «{{ $item['number'] }}» مطمئن هستید؟ این عملیات قابل بازگشت نیست.</p>
                <div><button type="button" class="danger-button" wire:click="delete" wire:loading.attr="disabled">بله، حذف شود</button><button type="button" class="cancel-button" wire:click="$set('confirmingDelete', false)">انصراف</button></div>
            @else
                <button type="button" class="danger-outline" wire:click="$set('confirmingDelete', true)">حذف واحد</button>
            @endif
        </section>
    @endif
</main>
