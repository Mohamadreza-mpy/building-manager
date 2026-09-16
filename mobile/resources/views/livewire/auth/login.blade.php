<main class="auth-shell">
    <section class="auth-card" aria-labelledby="login-title">
        <div class="brand-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" role="img"><path d="M4 21V7l8-4 8 4v14h-6v-5h-4v5H4Zm3-3h1v-3h8v3h1V8.9l-5-2.5-5 2.5V18Zm1-7h2V9H8v2Zm6 0h2V9h-2v2Z"/></svg>
        </div>

        <header class="auth-header">
            <p class="eyebrow">مدیریت هوشمند ساختمان</p>
            <h1 id="login-title">ورود به حساب کاربری</h1>
            <p>برای ادامه شماره موبایل و رمز عبور خود را وارد کنید.</p>
        </header>

        @if ($errorMessage)
            <div class="alert alert-error" role="alert">{{ $errorMessage }}</div>
        @endif

        <form wire:submit="login" class="form-stack" novalidate>
            <div class="field-group">
                <label for="mobile">شماره موبایل</label>
                <div class="input-wrap" @class(['has-error' => $errors->has('mobile')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm0 3v14h10V5H7Zm4 12h2v1h-2v-1Z"/></svg>
                    <input id="mobile" type="tel" inputmode="numeric" autocomplete="tel" maxlength="11" placeholder="09123456789" wire:model="mobile" dir="ltr">
                </div>
                @error('mobile') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field-group">
                <label for="password">رمز عبور</label>
                <div class="input-wrap" @class(['has-error' => $errors->has('password')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v10h14V10a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V6Zm7 12H7v-8h10v8Z"/></svg>
                    <input id="password" type="password" autocomplete="current-password" placeholder="حداقل ۶ کاراکتر" wire:model="password" dir="ltr">
                </div>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="primary-button" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">ورود</span>
                <span wire:loading wire:target="login" class="loading-label"><i class="spinner"></i> در حال ورود…</span>
            </button>
        </form>

        <p class="security-note">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 8 3v6c0 5.2-3.4 9.7-8 11-4.6-1.3-8-5.8-8-11V5l8-3Zm0 3.2L7 7v4c0 3.5 2 6.7 5 7.8 3-1.1 5-4.3 5-7.8V7l-5-1.8Z"/></svg>
            اطلاعات ورود شما به‌صورت امن نگه‌داری می‌شود.
        </p>
    </section>
</main>
