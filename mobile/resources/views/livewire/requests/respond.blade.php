<main class="module-shell">
    <header class="module-header"><a href="{{ route('requests.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a><div><p class="eyebrow">پاسخ مدیر</p><h1>{{ $item['title'] ?? 'درخواست' }}</h1></div><span></span></header>
    @if($errorMessage)<div class="alert alert-error">{{ $errorMessage }}</div>@endif
    @if($item)
        <section class="request-summary"><div><span>واحد</span><strong>{{ $item['apartment']['number'] ?? '—' }}</strong></div><p>{{ $item['description'] }}</p></section>
        <form wire:submit="save" class="form-card form-stack">
            <div class="field-group"><label for="status">وضعیت درخواست <span class="required">*</span></label><div class="input-wrap select-wrap @error('status') has-error @enderror"><select id="status" wire:model="status"><option value="processing">در حال بررسی</option><option value="completed">تکمیل‌شده</option><option value="rejected">ردشده</option></select></div>@error('status')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="field-group"><label for="response">پاسخ مدیر</label><div class="input-wrap textarea-wrap @error('response') has-error @enderror"><textarea id="response" wire:model="response" rows="6" placeholder="نتیجه بررسی یا توضیحات را بنویسید"></textarea></div>@error('response')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="publish-note"><span>●</span><p>با ثبت پاسخ، برای ساکن واحد اعلان درون‌برنامه‌ای ارسال می‌شود.</p></div>
            <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">ثبت پاسخ</span><span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ثبت…</span></button>
        </form>
    @endif
</main>
