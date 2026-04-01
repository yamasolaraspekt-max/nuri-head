@php $d = $data ?? null; @endphp

<div class="col-md-6 form-group">
    <label>Label</label>
    <input type="text" name="label" class="form-control" value="{{ old('label', $d->label ?? '') }}" required>
</div>
<div class="col-md-6 form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $d->email ?? '') }}" required>
</div>
<div class="col-md-6 form-group">
    <label>Passwort</label>
    <input type="text" name="password" class="form-control" value="{{ old('password', $d->password ?? '') }}" required>
</div>
<div class="col-md-6 form-group">
    <label>Host</label>
    <input type="text" name="host" class="form-control" value="{{ old('host', $d->host ?? 'imap.solar-aspekt.de') }}" required>
</div>
<div class="col-md-6 form-group">
    <label>Port</label>
    <input type="number" name="port" class="form-control" value="{{ old('port', $d->port ?? 993) }}" required>
</div>
<div class="col-md-6 form-group">
    <label>Verschlüsselung</label>
    <select name="encryption" class="form-control">
        <option value="ssl" {{ old('encryption', $d->encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
        <option value="tls" {{ old('encryption', $d->encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
    </select>
</div>
<div class="col-md-6 form-group">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="Published" {{ old('status', $d->status ?? '') == 'Published' ? 'selected' : '' }}>Published</option>
        <option value="Unpublished" {{ old('status', $d->status ?? '') == 'Unpublished' ? 'selected' : '' }}>Unpublished</option>
    </select>
</div>
