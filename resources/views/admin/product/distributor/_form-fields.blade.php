<div class="oc-form-grid">
    <div class="oc-form-group">
        <label class="oc-label">Name *</label>
        <input type="text" name="name" class="oc-input-form" value="{{ old('name') }}" required>
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Kurzname</label>
        <input type="text" name="short_name" class="oc-input-form" value="{{ old('short_name') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Kontonummer</label>
        <input type="text" name="account_number" class="oc-input-form" value="{{ old('account_number') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Status</label>
        <select name="status" class="oc-input-form">
            <option value="Unpublished" @selected(old('status') === 'Unpublished')>Unveröffentlicht</option>
            <option value="Published" @selected(old('status') === 'Published')>Veröffentlicht</option>
            <option value="active" @selected(old('status') === 'active')>Aktiv</option>
            <option value="inactive" @selected(old('status') === 'inactive')>Inaktiv</option>
        </select>
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Straße</label>
        <input type="text" name="street" class="oc-input-form" value="{{ old('street') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">PLZ</label>
        <input type="text" name="postal_code" class="oc-input-form" value="{{ old('postal_code') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Stadt</label>
        <input type="text" name="city" class="oc-input-form" value="{{ old('city') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Telefon</label>
        <input type="text" name="phone" class="oc-input-form" value="{{ old('phone') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Mobil</label>
        <input type="text" name="mobile" class="oc-input-form" value="{{ old('mobile') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">E-Mail</label>
        <input type="email" name="email" class="oc-input-form" value="{{ old('email') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Skonto %</label>
        <input type="number" step="0.01" min="0" max="100" name="cash_discount" class="oc-input-form"
            value="{{ old('cash_discount') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Zahlungsziel</label>
        <input type="text" name="payment_terms" class="oc-input-form" value="{{ old('payment_terms') }}">
    </div>

    <div class="oc-form-group">
        <label class="oc-label">Logo</label>
        <input type="file" name="image" class="oc-input-form" accept="image/*">
        <div class="oc-help">JPG, PNG, WEBP oder SVG bis 4 MB.</div>
    </div>
</div>