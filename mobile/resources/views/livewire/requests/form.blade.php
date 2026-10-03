<main class="module-shell">
    <header class="module-header"><a href="{{ route('requests.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a><div><p class="eyebrow">درخواست ساکن</p><h1>ثبت درخواست جدید</h1></div><span></span></header>
    @if($errorMessage)<div class="alert alert-error">{{ $errorMessage }}</div>@endif
    <form wire:submit="save" class="form-card form-stack">
        @if($apartmentId)<div class="selected-apartment"><span>واحد درخواست</span><strong>{{ $apartmentLabel }}</strong></div>@else<div class="alert alert-error">برای حساب شما واحدی به‌عنوان ساکن تخصیص داده نشده است. از مدیر ساختمان بخواهید ساکن را به واحدتان اختصاص دهد تا ثبت درخواست فعال شود.</div>@endif
        <div class="field-group"><label for="title">عنوان درخواست <span class="required">*</span></label><div class="input-wrap @error('title') has-error @enderror"><input id="title" wire:model="title" placeholder="مثلاً خرابی آسانسور"></div>@error('title')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="description">شرح درخواست <span class="required">*</span></label><div class="input-wrap textarea-wrap @error('description') has-error @enderror"><textarea id="description" wire:model="description" rows="6" placeholder="جزئیات درخواست را کامل بنویسید"></textarea></div>@error('description')<p class="field-error">{{ $message }}</p>@enderror</div>
        <button class="primary-button" type="submit" @disabled(!$apartmentId) wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">ثبت درخواست</span><span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ثبت…</span></button>
    </form>
</main>
