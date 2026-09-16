<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('expenses.index', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">هزینه ساختمان</p><h1>ثبت هزینه جدید</h1></div><span></span>
    </header>

    @if($errorMessage)<div class="alert alert-error">{{ $errorMessage }}</div>@endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group"><label for="title">عنوان هزینه <span class="required">*</span></label><div class="input-wrap @error('title') has-error @enderror"><input id="title" wire:model="title" placeholder="مثلاً تعمیر آسانسور"></div>@error('title')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="category">دسته‌بندی <span class="required">*</span></label><div class="input-wrap @error('category') has-error @enderror"><input id="category" wire:model="category" placeholder="مثلاً تعمیرات"></div>@error('category')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="amount">مبلغ (ریال) <span class="required">*</span></label><div class="input-wrap @error('amount') has-error @enderror"><input id="amount" wire:model="amount" type="number" min="1" inputmode="decimal" placeholder="مثلاً ۱۰۰۰۰۰۰۰"></div>@error('amount')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="description">توضیحات</label><div class="input-wrap textarea-wrap"><textarea id="description" wire:model="description" rows="3" placeholder="شرح اختیاری هزینه"></textarea></div></div>

        <div class="field-group">
            <label>تصویر رسید</label>
            <div class="receipt-actions"><button type="button" wire:click="takeReceiptPhoto">گرفتن عکس</button><button type="button" wire:click="pickReceiptImage">انتخاب از گالری</button></div>
            <label class="file-picker"><input type="file" wire:model="receipt" accept="image/*"><span>انتخاب فایل تصویر</span></label>
            <div wire:loading wire:target="receipt" class="field-hint">در حال آماده‌سازی تصویر…</div>
            @if($receipt || $nativeReceiptPath)<div class="receipt-selected"><span>✓ {{ $receipt ? $receipt->getClientOriginalName() : $nativeReceiptName }}</span><button type="button" wire:click="removeReceipt">حذف</button></div>@endif
            @error('receipt')<p class="field-error">{{ $message }}</p>@enderror
            <p class="field-hint">فرمت تصویر، حداکثر ۵ مگابایت</p>
        </div>

        <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">ثبت هزینه</span><span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ثبت…</span></button>
    </form>
</main>
