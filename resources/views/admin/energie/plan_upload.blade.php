@extends('admin.layouts.app')

@section('title', 'Plan-Import')

{{--
    Plan-Import (Strang Energie) – Transplantat aus wberechnung.
    Server-gerendert: Blade + Bootstrap/Vuexy, jQuery-AJAX. KEIN Alpine (CLAUDE.md).
--}}

@php
    $statusBadge = [
        'neu' => 'badge-light-warning',
        'klassifiziert' => 'badge-light-info',
        'verarbeitet' => 'badge-light-success',
        'fehler' => 'badge-light-danger',
    ];
    $typLabel = ['dwg' => 'DWG', 'dxf' => 'DXF', 'pdf' => 'PDF', 'bild' => 'Bild'];
@endphp

@section('content')
<section class="energie-plan-upload">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title mb-0">Plan-Import</h2>
            <p class="text-muted mb-0">
                Grundriss als DWG/DXF/PDF/Bild hochladen. Wird gespeichert und grob klassifiziert
                (Erkennung folgt). Nichts fließt unverifiziert in eine Heizlast — der Import landet
                später im Grundriss-Editor.
            </p>
        </div>
    </div>

    @unless ($importAktiv)
        <div class="alert alert-warning" role="alert">
            <div class="alert-body">
                Import-Service nicht konfiguriert — Dateien werden nur hochgeladen/klassifiziert,
                aber noch nicht extrahiert (<code>IMPORT_SERVICE_URL</code> setzen).
            </div>
        </div>
    @endunless

    <div class="card">
        <div class="card-header"><h4 class="card-title">Hochladen</h4></div>
        <div class="card-body">
            <form id="plan-upload-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <label class="form-label" for="datei">
                            Datei (DWG, DXF, PDF, PNG, JPG, TIFF · max 50 MB)
                        </label>
                        <input type="file" id="datei" name="datei" class="form-control"
                               accept=".dwg,.dxf,.pdf,.png,.jpg,.jpeg,.tif,.tiff" required>
                    </div>
                    <div class="col-md-4 mb-1">
                        <label class="form-label" for="heizlast_projekt_id">Projekt (optional)</label>
                        <select id="heizlast_projekt_id" name="heizlast_projekt_id" class="form-select">
                            <option value="">— ohne Projekt —</option>
                            @foreach ($projekte as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-1 d-flex align-items-end">
                        <button type="submit" id="plan-upload-btn" class="btn btn-primary w-100">
                            Hochladen
                        </button>
                    </div>
                </div>
                <p id="plan-upload-fehler" class="text-danger mb-0" style="display:none;"></p>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title">Hochgeladene Pläne</h4></div>
        <div class="card-body">
            @if ($uploads->isEmpty())
                <p class="text-muted mb-0">Noch nichts hochgeladen.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Datei</th>
                                <th>Projekt</th>
                                <th>Typ</th>
                                <th>Status</th>
                                <th class="text-end">Größe</th>
                                <th>Datum</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($uploads as $u)
                                <tr id="plan-row-{{ $u->id }}">
                                    <td class="fw-bold">{{ $u->original_name }}</td>
                                    <td class="text-muted">{{ $u->heizlastProjekt?->name ?? '—' }}</td>
                                    <td>{{ $typLabel[$u->typ] ?? ($u->typ ?? '—') }}</td>
                                    <td>
                                        <span class="badge {{ $statusBadge[$u->status] ?? 'badge-light-secondary' }}">
                                            {{ $u->status }}
                                        </span>
                                        @if ($u->status === 'fehler' && data_get($u->meta, 'fehler'))
                                            <div class="text-danger small">{{ data_get($u->meta, 'fehler') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($u->groesse_bytes / 1024, 0, ',', '.') }} KB
                                    </td>
                                    <td class="text-muted">{{ $u->created_at?->format('d.m.Y H:i') }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('energie.grundriss.editor', $u->heizlast_projekt_id ?: null) }}?upload={{ $u->id }}"
                                           class="btn btn-sm btn-outline-primary">Im Editor öffnen</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger plan-loeschen"
                                                data-id="{{ $u->id }}">Löschen</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-1 mb-0">
                    Status „neu" → der Queue-Worker klassifiziert (<code>php artisan queue:work</code>);
                    danach Seite aktualisieren.
                </p>
            @endif
        </div>
    </div>

</section>
@endsection

@push('scripts')
<script>
    (function () {
        var csrf = '{{ csrf_token() }}';
        var storeUrl = '{{ route('energie.plan-upload.store') }}';
        var destroyBase = '{{ url('admin/energie/plan-upload') }}';

        $('#plan-upload-form').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#plan-upload-btn');
            var $fehler = $('#plan-upload-fehler').hide().text('');
            var datei = $('#datei')[0].files[0];
            if (!datei) { $fehler.text('Bitte eine Datei wählen.').show(); return; }

            var fd = new FormData();
            fd.append('datei', datei);
            var projektId = $('#heizlast_projekt_id').val();
            if (projektId) { fd.append('heizlast_projekt_id', projektId); }

            $btn.prop('disabled', true).text('lädt …');
            $.ajax({
                url: storeUrl,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).done(function () {
                window.location.reload();
            }).fail(function (xhr) {
                var msg = (xhr.responseJSON && (xhr.responseJSON.message
                    || (xhr.responseJSON.errors && xhr.responseJSON.errors.datei && xhr.responseJSON.errors.datei[0])))
                    || ('Fehler ' + xhr.status);
                $fehler.text(msg).show();
            }).always(function () {
                $btn.prop('disabled', false).text('Hochladen');
            });
        });

        $('.plan-loeschen').on('click', function () {
            if (!confirm('Diesen Upload löschen?')) { return; }
            var id = $(this).data('id');
            $.ajax({
                url: destroyBase + '/' + id,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).done(function () {
                $('#plan-row-' + id).remove();
            }).fail(function (xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message) || ('Fehler ' + xhr.status));
            });
        });
    })();
</script>
@endsection
