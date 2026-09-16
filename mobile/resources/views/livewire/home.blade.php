<main class="home-shell">
    <section class="home-card">
        <div class="success-icon" aria-hidden="true">✓</div>
        <p class="eyebrow">ورود موفق</p>
        <h1>سلام، {{ $user['name'] ?? 'کاربر' }}</h1>
        <p class="muted">ارتباط برنامه با API برقرار است و نشست شما با موفقیت بازیابی شد.</p>

        @if ($errorMessage)
            <div class="alert alert-error" role="alert">{{ $errorMessage }}</div>
        @endif

        <dl class="profile-summary">
            <div><dt>شماره موبایل</dt><dd dir="ltr">{{ $user['mobile'] ?? '—' }}</dd></div>
            <div><dt>نقش کاربری</dt><dd>{{ $user['role_label'] ?? 'کاربر' }}</dd></div>
        </dl>

        <div class="phase-note">
            <strong>فاز اول کامل است</strong>
            <span>داشبورد و امکانات مدیریتی در فازهای بعدی اضافه می‌شوند.</span>
        </div>

        <button type="button" class="secondary-button" wire:click="logout" wire:loading.attr="disabled" wire:target="logout">
            <span wire:loading.remove wire:target="logout">خروج از حساب</span>
            <span wire:loading wire:target="logout" class="loading-label"><i class="spinner dark"></i> در حال خروج…</span>
        </button>
    </section>
</main>
