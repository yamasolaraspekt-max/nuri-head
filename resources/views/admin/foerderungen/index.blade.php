@extends('admin.layouts.app')
@section('title')Förderungen @stop

@php
    $typBadge = [
        'KfW' => 'badge-light-info', 'BAFA' => 'badge-light-success', 'Land' => 'badge-light-warning',
        'Kommune' => 'badge-light-secondary', 'EU' => 'badge-light-primary', 'sonstiger' => 'badge-light-secondary',
    ];
    $statusBadge = [
        'geplant' => 'badge-light-secondary', 'beantragt' => 'badge-light-info', 'bewilligt' => 'badge-light-success',
        'ausgezahlt' => 'badge-light-success', 'abgelehnt' => 'badge-light-danger',
    ];
@endphp

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Förderungen</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Finanzen &amp; Förderung</a></li>
                                <li class="breadcrumb-item active">Förderungen</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            {{-- KPI-Karten --}}
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar bg-light-primary mr-2"><div class="avatar-content"><i data-feather="layers"></i></div></div>
                            <div>
                                <h4 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h4>
                                <small class="text-muted">Programme</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar bg-light-info mr-2"><div class="avatar-content"><i data-feather="check-circle"></i></div></div>
                            <div>
                                <h4 class="font-weight-bolder mb-0">{{ $stats['beantragt'] }}</h4>
                                <small class="text-muted">Beantragt/Bewilligt</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar bg-light-success mr-2"><div class="avatar-content"><i data-feather="award"></i></div></div>
                            <div>
                                <h4 class="font-weight-bolder mb-0">{{ $stats['ausgezahlt'] }}</h4>
                                <small class="text-muted">Ausgezahlt</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar bg-light-warning mr-2"><div class="avatar-content"><i data-feather="dollar-sign"></i></div></div>
                            <div>
                                <h4 class="font-weight-bolder mb-0">{{ number_format($stats['volumen'], 0, ',', '.') }} €</h4>
                                <small class="text-muted">Fördervolumen</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Werkzeugleiste: Suche/Filter (GET) + Neu --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('foerderungen.index') }}" class="form-inline">
                        <div class="form-group mr-1 mb-1">
                            <input type="text" name="search" value="{{ $filter['search'] }}" class="form-control" placeholder="Programm, Projekt oder Antragsnr.">
                        </div>
                        <div class="form-group mr-1 mb-1">
                            <select name="foerdertyp" class="form-control">
                                <option value="">Alle Typen</option>
                                @foreach($typen as $t)
                                    <option value="{{ $t }}" @selected($filter['foerdertyp'] === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-1 mb-1">
                            <select name="antragsstatus" class="form-control">
                                <option value="">Alle Status</option>
                                @foreach($statusList as $s)
                                    <option value="{{ $s }}" @selected($filter['antragsstatus'] === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-1 mb-1 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="mitArchivierten" name="mit_archivierten" value="1" @checked($filter['mit_archivierten'])>
                            <label class="custom-control-label" for="mitArchivierten">Archivierte</label>
                        </div>
                        <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Filtern</button>
                        <a href="{{ route('foerderungen.index') }}" class="btn btn-outline-secondary mr-1 mb-1">Zurücksetzen</a>
                        <button type="button" class="btn btn-success mb-1 ml-auto waves-effect waves-light" id="btnNeueFoerderung">
                            <i data-feather="plus"></i> Neue Förderung
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tabelle --}}
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Förderprogramm</th>
                                <th>Typ</th>
                                <th>Förderbetrag</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Projekt</th>
                                <th class="text-right">Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fundings as $f)
                                <tr @if($f->archived_at) class="text-muted" @endif>
                                    <td>
                                        <span class="font-weight-bold">{{ $f->bezeichnung }}</span>
                                        @if($f->antragsnummer)<br><small class="text-muted">{{ $f->antragsnummer }}</small>@endif
                                        @if($f->archived_at)<span class="badge badge-light-warning ml-1">archiviert</span>@endif
                                    </td>
                                    <td><span class="badge {{ $typBadge[$f->foerdertyp] ?? 'badge-light-secondary' }}">{{ $f->foerdertyp ?: '–' }}</span></td>
                                    <td class="font-weight-bold">{{ number_format((float) $f->betrag, 0, ',', '.') }} €</td>
                                    <td><span class="badge {{ $statusBadge[$f->antragsstatus] ?? 'badge-light-secondary' }}">{{ ucfirst($f->antragsstatus ?: '–') }}</span></td>
                                    <td>{{ optional($f->deadline)->format('d.m.Y') ?: '–' }}</td>
                                    <td>{{ $f->projekt_ref ?: '–' }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit"
                                            data-id="{{ $f->id }}"
                                            data-bezeichnung="{{ $f->bezeichnung }}"
                                            data-foerdertyp="{{ $f->foerdertyp }}"
                                            data-betrag="{{ $f->betrag }}"
                                            data-antragsstatus="{{ $f->antragsstatus }}"
                                            data-deadline="{{ optional($f->deadline)->format('Y-m-d') }}"
                                            data-projekt_ref="{{ $f->projekt_ref }}"
                                            data-notiz="{{ $f->notiz }}"
                                            title="Bearbeiten"><i data-feather="edit-2"></i></button>
                                        @if($f->archived_at)
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-restore" data-id="{{ $f->id }}" title="Wiederherstellen"><i data-feather="rotate-ccw"></i></button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-warning btn-archive" data-id="{{ $f->id }}" data-name="{{ $f->bezeichnung }}" title="Archivieren"><i data-feather="archive"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">Keine Förderungen gefunden.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($fundings->hasPages())
                    <div class="card-body">{{ $fundings->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->

{{-- Anlegen/Bearbeiten-Modal --}}
<div class="modal fade text-left" id="foerderungModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="foerderungModalTitle">Neue Förderung</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="foerderungForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="f_id">
                    <div class="form-group">
                        <label>Förderprogramm <span class="text-danger">*</span></label>
                        <input type="text" name="bezeichnung" id="f_bezeichnung" class="form-control" placeholder="z. B. KfW 270 – Erneuerbare Energien">
                    </div>
                    <div class="form-group" id="f_antragsnummer_wrap">
                        <label>Antragsnummer <small class="text-muted">(leer = automatisch FOE-…)</small></label>
                        <input type="text" name="antragsnummer" id="f_antragsnummer" class="form-control" placeholder="automatisch">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Typ</label>
                            <select name="foerdertyp" id="f_foerdertyp" class="form-control">
                                @foreach($typen as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Antragsstatus</label>
                            <select name="antragsstatus" id="f_antragsstatus" class="form-control">
                                @foreach($statusList as $s)<option value="{{ $s }}">{{ ucfirst($s) }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Förderbetrag (€)</label>
                            <input type="number" min="0" step="0.01" name="betrag" id="f_betrag" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Deadline</label>
                            <input type="date" name="deadline" id="f_deadline" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Projekt</label>
                        <input type="text" name="projekt_ref" id="f_projekt_ref" class="form-control" placeholder="optional">
                    </div>
                    <div class="form-group">
                        <label>Notiz</label>
                        <textarea name="notiz" id="f_notiz" rows="2" class="form-control" placeholder="optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="f_save_btn">Speichern</button>
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    const csrf = '{{ csrf_token() }}';
    const baseUrl = '{{ url('foerderungen') }}';

    function clearErrors() {
        $('#foerderungForm .is-invalid').removeClass('is-invalid');
        $('#foerderungForm .invalid-feedback').remove();
    }

    function openModal(edit) {
        clearErrors();
        $('#foerderungForm')[0].reset();
        $('#f_id').val('');
        if (edit) {
            const d = edit;
            $('#foerderungModalTitle').text('Förderung bearbeiten');
            $('#f_id').val(d.id);
            $('#f_bezeichnung').val(d.bezeichnung || '');
            $('#f_foerdertyp').val(d.foerdertyp || 'KfW');
            $('#f_betrag').val(d.betrag || '');
            $('#f_antragsstatus').val(d.antragsstatus || 'geplant');
            $('#f_deadline').val(d.deadline || '');
            $('#f_projekt_ref').val(d.projekt_ref || '');
            $('#f_notiz').val(d.notiz || '');
            $('#f_antragsnummer_wrap').hide();
        } else {
            $('#foerderungModalTitle').text('Neue Förderung');
            $('#f_foerdertyp').val('KfW');
            $('#f_antragsstatus').val('geplant');
            $('#f_antragsnummer_wrap').show();
        }
        $('#foerderungModal').modal('show');
    }

    $('#btnNeueFoerderung').on('click', () => openModal(null));

    $('.btn-edit').on('click', function () {
        openModal($(this).data());
    });

    $('#foerderungForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        const id = $('#f_id').val();
        const url = id ? baseUrl + '/' + id : baseUrl;
        const data = $(this).serializeArray();
        if (id) data.push({ name: '_method', value: 'PUT' });
        $('#f_save_btn').prop('disabled', true).text('Speichern…');

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': csrf },
            success: function (res) {
                $('#foerderungModal').modal('hide');
                toastr.success(res.message || 'Gespeichert.');
                setTimeout(() => location.reload(), 600);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (field, msgs) {
                        const input = $('#f_' + field);
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback d-block">' + msgs[0] + '</div>');
                    });
                } else {
                    toastr.error('Speichern fehlgeschlagen.');
                }
            },
            complete: function () {
                $('#f_save_btn').prop('disabled', false).text('Speichern');
            }
        });
    });

    $('.btn-archive').on('click', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Förderung archivieren?',
            html: '<b>' + name + '</b> wird ausgeblendet. Die Förderhistorie bleibt erhalten.',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Archivieren', cancelButtonText: 'Abbrechen',
            customClass: { confirmButton: 'btn btn-warning', cancelButton: 'btn btn-outline-secondary ml-1' },
            buttonsStyling: false,
        }).then((r) => {
            if (!r.value) return;
            $.ajax({
                url: baseUrl + '/' + id, method: 'POST',
                data: { _method: 'DELETE', _token: csrf },
                success: function (res) { toastr.success(res.message || 'Archiviert.'); setTimeout(() => location.reload(), 600); },
                error: function () { toastr.error('Archivieren fehlgeschlagen.'); }
            });
        });
    });

    $('.btn-restore').on('click', function () {
        const id = $(this).data('id');
        $.ajax({
            url: baseUrl + '/' + id + '/restore', method: 'POST',
            data: { _token: csrf },
            success: function (res) { toastr.success(res.message || 'Wiederhergestellt.'); setTimeout(() => location.reload(), 600); },
            error: function () { toastr.error('Wiederherstellen fehlgeschlagen.'); }
        });
    });

    if (window.feather) { feather.replace(); }
});
</script>
@endsection
