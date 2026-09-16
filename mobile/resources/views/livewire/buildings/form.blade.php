<main class="module-shell">
    <header class="module-header">
        <a href="{{ $buildingId ? route('buildings.show', $buildingId) : route('buildings.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">ساختمان</p><h1>{{ $buildingId ? 'ویرایش ساختمان' : 'ساختمان جدید' }}</h1></div>
        <span></span>
    </header>

    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group">
            <label for="name">نام ساختمان <span class="required">*</span></label>
            <div class="input-wrap @error('name') has-error @enderror"><input id="name" wire:model="name" placeholder="مثلاً ساختمان آفتاب" autocomplete="off"></div>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="address">نشانی</label>
            <div class="input-wrap textarea-wrap @error('address') has-error @enderror"><textarea id="address" wire:model="address" rows="4" placeholder="نشانی کامل ساختمان"></textarea></div>
            @error('address') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="totalUnits">تعداد کل واحدها</label>
            <div class="input-wrap @error('totalUnits') has-error @enderror"><input id="totalUnits" wire:model="totalUnits" type="number" min="1" inputmode="numeric" placeholder="مثلاً ۱۲"></div>
            @error('totalUnits') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">{{ $buildingId ? 'ذخیره تغییرات' : 'ایجاد ساختمان' }}</span>
            <span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ذخیره…</span>
        </button>
    </form>
</main>
