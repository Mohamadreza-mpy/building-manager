<main class="module-shell">
    <header class="module-header">
        <a href="{{ route('owners.index') }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">مالکین</p><h1>ثبت مالک جدید</h1></div>
        <span></span>
    </header>

    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group"><label for="name">نام و نام خانوادگی <span class="required">*</span></label><div class="input-wrap @error('name') has-error @enderror"><input id="name" wire:model="name" autocomplete="name"></div>@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="mobile">شماره موبایل <span class="required">*</span></label><div class="input-wrap @error('mobile') has-error @enderror"><input id="mobile" wire:model="mobile" inputmode="tel" dir="ltr" placeholder="09120000000"></div>@error('mobile')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="email">ایمیل</label><div class="input-wrap @error('email') has-error @enderror"><input id="email" wire:model="email" type="email" dir="ltr"></div>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="password">رمز اولیه <span class="required">*</span></label><div class="input-wrap @error('password') has-error @enderror"><input id="password" wire:model="password" type="password" dir="ltr"></div>@error('password')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-group"><label for="passwordConfirmation">تکرار رمز اولیه <span class="required">*</span></label><div class="input-wrap @error('passwordConfirmation') has-error @enderror"><input id="passwordConfirmation" wire:model="passwordConfirmation" type="password" dir="ltr"></div>@error('passwordConfirmation')<p class="field-error">{{ $message }}</p>@enderror</div>
        <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">ثبت مالک</span><span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ثبت…</span></button>
    </form>
</main>
