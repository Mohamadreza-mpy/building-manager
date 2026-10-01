<main class="dashboard-shell">
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">داشبورد</p>
            <h1>سلام، {{ $user['name'] ?? 'کاربر' }}</h1>
            <p>{{ $user['role_label'] ?? 'کاربر' }} · آخرین بروزرسانی {{ $updatedAt ?? '—' }}</p>
        </div>
        <button type="button" class="icon-button" wire:click="refreshDashboard" wire:loading.attr="disabled" wire:target="refreshDashboard" aria-label="بروزرسانی داشبورد">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.65 6.35A8 8 0 1 0 20 12h-2a6 6 0 1 1-1.76-4.24L13 11h8V3l-3.35 3.35Z"/></svg>
        </button>
    </header>

    <div wire:loading.flex wire:target="refreshDashboard" class="dashboard-loading"><i class="spinner dark"></i><span>در حال بروزرسانی…</span></div>

    @if ($errorMessage)
        <div class="alert alert-error dashboard-alert" role="alert">
            <span>{{ $errorMessage }}</span>
            <button type="button" wire:click="refreshDashboard">تلاش دوباره</button>
        </div>
    @endif

    @if ($isResident)
        <a href="{{ route('notifications.index') }}" wire:navigate class="module-link">
            <div class="card-icon violet">●</div>
            <div><strong>اعلان‌ها</strong><span>رویدادها و پیام‌های جدید</span></div>
            <b>‹</b>
        </a>
        <a href="{{ route('requests.index') }}" wire:navigate class="module-link">
            <div class="card-icon rose">✓</div>
            <div><strong>درخواست‌های من</strong><span>ثبت درخواست و مشاهده پاسخ مدیر</span></div>
            <b>‹</b>
        </a>
        <a href="{{ route('charges.mine') }}" wire:navigate class="module-link">
            <div class="card-icon amber">﷼</div>
            <div><strong>شارژهای من</strong><span>مشاهده مبالغ و وضعیت پرداخت</span></div>
            <b>‹</b>
        </a>
        @if($dashboard['current_apartment']['building']['id'] ?? null)
            <a href="{{ route('announcements.index', $dashboard['current_apartment']['building']['id']) }}" wire:navigate class="module-link">
                <div class="card-icon violet">●</div>
                <div><strong>اطلاعیه‌های ساختمان</strong><span>مشاهده پیام‌های مدیر ساختمان</span></div>
                <b>‹</b>
            </a>
            <a href="{{ route('expenses.index', $dashboard['current_apartment']['building']['id']) }}" wire:navigate class="module-link">
                <div class="card-icon rose">−</div>
                <div><strong>هزینه‌های ساختمان</strong><span>مشاهده هزینه‌ها و رسیدها</span></div>
                <b>‹</b>
            </a>
        @endif

        <section class="apartment-hero">
            @if ($dashboard['current_apartment'] ?? null)
                <div class="card-icon blue">⌂</div>
                <div>
                    <span>واحد فعلی من</span>
                    <strong>واحد {{ $dashboard['current_apartment']['number'] }}</strong>
                    <small>{{ $dashboard['current_apartment']['building']['name'] ?? 'ساختمان' }}@if(isset($dashboard['current_apartment']['floor'])) · طبقه {{ $this->number($dashboard['current_apartment']['floor']) }}@endif</small>
                </div>
            @else
                <div class="empty-inline"><strong>هنوز واحدی به شما اختصاص داده نشده است.</strong><span>برای پیگیری با مدیر ساختمان تماس بگیرید.</span></div>
            @endif
        </section>

        <section class="stats-grid" aria-label="خلاصه وضعیت ساکن">
            <article class="stat-card"><div class="card-icon amber">﷼</div><span>شارژ پرداخت‌نشده</span><strong>{{ $this->number($dashboard['unpaid_charges_count'] ?? 0) }}</strong><small>{{ $this->number($dashboard['unpaid_charges_total'] ?? 0) }} ریال</small></article>
            <article class="stat-card"><div class="card-icon violet">●</div><span>اطلاعیه‌ها</span><strong>{{ $this->number($dashboard['announcements_count'] ?? 0) }}</strong><small>اطلاعیه ساختمان</small></article>
            <article class="stat-card"><div class="card-icon rose">✓</div><span>درخواست‌های باز</span><strong>{{ $this->number($dashboard['pending_requests_count'] ?? 0) }}</strong><small>از {{ $this->number($dashboard['requests_count'] ?? 0) }} درخواست</small></article>
        </section>

        <section class="dashboard-section">
            <div class="section-heading"><h2>آخرین اطلاعیه‌ها</h2></div>
            <div class="announcement-list">
                @forelse ($dashboard['recent_announcements'] ?? [] as $announcement)
                    <article><i></i><div><strong>{{ $announcement['title'] }}</strong><span>اطلاعیه ساختمان</span></div></article>
                @empty
                    <div class="empty-state">اطلاعیه‌ای برای نمایش وجود ندارد.</div>
                @endforelse
            </div>
        </section>
    @else
        <a href="{{ route('notifications.index') }}" wire:navigate class="module-link">
            <div class="card-icon violet">●</div>
            <div><strong>اعلان‌ها</strong><span>رویدادها و پیام‌های جدید</span></div>
            <b>‹</b>
        </a>
        <a href="{{ route('requests.index') }}" wire:navigate class="module-link">
            <div class="card-icon rose">!</div>
            <div><strong>درخواست‌های ساکنان</strong><span>بررسی و پاسخ‌گویی به درخواست‌ها</span></div>
            <b>‹</b>
        </a>
        <a href="{{ route('buildings.index') }}" wire:navigate class="module-link">
            <div class="card-icon blue">⌂</div>
            <div><strong>مدیریت ساختمان‌ها</strong><span>مشاهده، افزودن و ویرایش ساختمان‌ها</span></div>
            <b>‹</b>
        </a>

        <section class="stats-grid manager-grid" aria-label="خلاصه وضعیت مدیریت">
            <article class="stat-card"><div class="card-icon blue">⌂</div><span>ساختمان‌ها</span><strong>{{ $this->number($dashboard['buildings_count'] ?? 0) }}</strong><small>ساختمان تحت مدیریت</small></article>
            <article class="stat-card"><div class="card-icon green">▦</div><span>واحدها</span><strong>{{ $this->number($dashboard['apartments_count'] ?? 0) }}</strong><small>واحد ثبت‌شده</small></article>
            <article class="stat-card"><div class="card-icon rose">!</div><span>درخواست‌های منتظر</span><strong>{{ $this->number($dashboard['pending_requests_count'] ?? 0) }}</strong><small>نیازمند بررسی</small></article>
            <article class="stat-card wide"><div class="card-icon amber">﷼</div><span>شارژ ماه جاری</span><strong>{{ $this->number($dashboard['monthly_charges_total'] ?? 0) }} ریال</strong><small>{{ $this->number($dashboard['monthly_charges_count'] ?? 0) }} شارژ صادرشده</small></article>
        </section>

        @if (($dashboard['buildings_count'] ?? 0) === 0)
            <section class="empty-state large"><strong>هنوز ساختمانی ثبت نشده است.</strong><span>از بخش مدیریت ساختمان‌ها اولین ساختمان را اضافه کنید.</span></section>
        @endif
    @endif

    <footer class="dashboard-footer">
        <button type="button" class="logout-button" wire:click="logout" wire:loading.attr="disabled" wire:target="logout">خروج از حساب</button>
    </footer>
</main>
