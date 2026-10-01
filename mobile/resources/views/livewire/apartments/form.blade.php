<main class="module-shell">
    <header class="module-header">
        <a href="{{ $apartmentId ? route('apartments.show', $apartmentId) : route('apartments.index', $buildingId) }}" wire:navigate class="back-button" aria-label="بازگشت">→</a>
        <div><p class="eyebrow">واحد</p><h1>{{ $apartmentId ? 'ویرایش واحد' : 'واحد جدید' }}</h1></div>
        <span></span>
    </header>

    @if ($errorMessage) <div class="alert alert-error">{{ $errorMessage }}</div> @endif

    <form wire:submit="save" class="form-card form-stack">
        <div class="field-group">
            <label for="number">شماره واحد <span class="required">*</span></label>
            <div class="input-wrap @error('number') has-error @enderror"><input id="number" wire:model="number" placeholder="مثلاً ۱۰۱" autocomplete="off"></div>
            @error('number') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="floor">طبقه</label>
            <div class="input-wrap @error('floor') has-error @enderror"><input id="floor" wire:model="floor" type="number" inputmode="numeric" placeholder="مثلاً ۱"></div>
            @error('floor') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <label for="area">مساحت (مترمربع)</label>
            <div class="input-wrap @error('area') has-error @enderror"><input id="area" wire:model="area" type="number" min="0" step="0.01" inputmode="decimal" placeholder="مثلاً ۸۵"></div>
            @error('area') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <div class="section-heading"><label for="ownerId">مالک واحد</label><a href="{{ route('owners.create') }}" wire:navigate>+ مالک جدید</a></div>
            <div class="input-wrap @error('ownerId') has-error @enderror">
                <select id="ownerId" wire:model="ownerId">
                    <option value="">بدون مالک</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner['id'] }}">{{ $owner['name'] }} — {{ $owner['mobile'] }}</option>
                    @endforeach
                </select>
            </div>
            @error('ownerId') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field-group">
            <div class="section-heading"><label for="residentId">ساکن واحد</label><a href="{{ route('residents.create') }}" wire:navigate>+ ساکن جدید</a></div>
            <div class="input-wrap @error('residentId') has-error @enderror">
                <select id="residentId" wire:model="residentId">
                    <option value="">بدون ساکن</option>
                    @foreach($residents as $resident)
                        <option value="{{ $resident['id'] }}">{{ $resident['name'] }} — {{ $resident['mobile'] }}</option>
                    @endforeach
                </select>
            </div>
            @error('residentId') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">{{ $apartmentId ? 'ذخیره تغییرات' : 'ایجاد واحد' }}</span>
            <span wire:loading.flex wire:target="save" class="loading-label"><i class="spinner"></i>در حال ذخیره…</span>
        </button>
    </form>
</main>
