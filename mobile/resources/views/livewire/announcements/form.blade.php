<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('announcements.index', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">اطلاعیه ساختمان</p><h1>انتشار اطلاعیه</h1></div><span></span>
    </header>

    @if($errorMessage)<div class="alert alert-error">{{ $errorMessage }}</div>@endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group">
            <label for="title">عنوان اطلاعیه <span class="required">*</span></label>
            <div class="input-wrap @error('title') has-error @enderror"><input id="title" wire:model="title" placeholder="مثلاً قطع موقت آب" autocomplete="off"></div>
            @error('title')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <div class="field-group">
            <label for="body">متن اطلاعیه <span class="required">*</span></label>
            <div class="input-wrap textarea-wrap @error('body') has-error @enderror"><textarea id="body" wire:model="body" rows="7" placeholder="متن کامل اطلاعیه را وارد کنید"></textarea></div>
            @error('body')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <div class="publish-note"><span>●</span><p>پس از انتشار، برای ساکنان این ساختمان اعلان درون‌برنامه‌ای ایجاد می‌شود.</p></div>
        <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">انتشار اطلاعیه</span><span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال انتشار…</span></button>
    </form>
</main>
