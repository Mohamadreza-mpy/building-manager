<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('charges.index', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">شارژ ساختمان</p><h1>ثبت شارژ جدید</h1></div>
        <span></span>
    </header>

    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group">
            <label for="apartmentId">واحد <span class="required">*</span></label>
            <div class="input-wrap select-wrap @error('apartmentId') has-error @enderror">
                <select id="apartmentId" wire:model="apartmentId">
                    <option value="">انتخاب واحد</option>
                    @foreach($apartments as $apartment)<option value="{{ $apartment['id'] }}">واحد {{ $apartment['number'] }}{{ isset($apartment['floor']) ? ' · طبقه '.$apartment['floor'] : '' }}</option>@endforeach
                </select>
            </div>
            @error('apartmentId') <p class="field-error">{{ $message }}</p> @enderror
            @if(!$apartments && !$errorMessage)<p class="field-hint">ابتدا باید برای ساختمان یک واحد ثبت کنید.</p>@endif
        </div>
        <div class="field-group">
            <label for="title">عنوان شارژ <span class="required">*</span></label>
            <div class="input-wrap @error('title') has-error @enderror"><input id="title" wire:model="title" placeholder="مثلاً شارژ ماهانه مهر" autocomplete="off"></div>
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="month">ماه شارژ <span class="required">*</span></label>
            <div class="input-wrap @error('month') has-error @enderror"><input id="month" wire:model="month" type="month"></div>
            @error('month') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="amount">مبلغ (ریال) <span class="required">*</span></label>
            <div class="input-wrap @error('amount') has-error @enderror"><input id="amount" wire:model="amount" type="number" min="1" inputmode="decimal" placeholder="مثلاً ۵۰۰۰۰۰۰"></div>
            @error('amount') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button class="primary-button" type="submit" @disabled(!$apartments) wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">ثبت شارژ</span>
            <span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ثبت…</span>
        </button>
    </form>
</main>
