@extends('admin.layouts.app')
@section('title') WEBSITE LEADS @stop

@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-left mb-0">WEBSITE LEADS</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a></li>
                        <li class="breadcrumb-item active">Fusion Form Leads</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row mb-2">
                <button id="syncForms" class="btn btn-primary">🔁 Sync Forms</button>
                <button id="syncSubmissions" class="btn btn-dark">📤 Sync Submissions</button>
                <button id="syncFields" class="btn btn-warning">📝 Sync Fields</button>
                <button id="syncEntries" class="btn btn-success">📩 Sync Entries</button>
                <div id="result-box" class="alert alert-info d-none mt-1"></div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <ul id="formList" class="list-group"></ul>
                </div>
                <div class="col-md-8">
                    <input type="text" id="searchInput" class="form-control mb-2" placeholder="Search entries...">
                    <div id="entriesTableContainer"></div>
                    <div id="paginationLinks" class="mt-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
let currentFormId = null;

function syncTable(url, type) {
    $('#result-box').removeClass('d-none').text(`Syncing ${type}...`);
    $.post(url, {_token: csrfToken}, res => {
        Swal.fire('✅ Success', `${type} sync complete: ${res.count} items imported.`, 'success');
    }).fail(() => {
        Swal.fire('❌ Failed', `Could not sync ${type}.`, 'error');
    });
}

$('#syncForms').click(() => syncTable("{{ route('fusion.sync.forms') }}", 'Forms'));
$('#syncFields').click(() => syncTable("{{ route('fusion.sync.fields') }}", 'Fields'));
$('#syncEntries').click(() => syncTable("{{ route('fusion.sync.entries') }}", 'Entries'));
$('#syncSubmissions').click(() => syncTable("{{ route('fusion.sync.submissions') }}", 'Submissions'));

function loadForms() {
    $.get("{{ route('fusion.forms.list') }}", function(data) {
        let html = '';
        data.form_ids.forEach(formId => {
            const source = data.submissions.find(sub => sub.form_id === formId)?.source_url || '🌐 Unknown';
            html += `<li class="list-group-item cursor-pointer" data-id="${formId}">
                        <strong>Form ID: ${formId}</strong><br>
                        <small>${source}</small>
                    </li>`;
        });
        $('#formList').html(html);
    }).fail(() => {
        $('#formList').html('<li class="list-group-item text-danger">❌ Failed to load forms</li>');
    });
}

function createNote(fields) {
    const get = (name) => {
        const match = fields.find(f => f.name === name || f.label === name);
        return match ? match.value : '';
    };
    return `Ich habe: ${get('ich_habe')}\nStromverbrauch pro Jahr: ${get('ihr_stromverbrauch_pro_jahr')}\nHeizart: ${get('heizart')}\nEnergieverbrauch: ${get('energieverbrauch')}`;
}

function importEntry(submissionId, fields) {
    const note = createNote(fields);
    $.ajax({
        url: "{{ route('fusion.import.single') }}",
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            submission_id: submissionId,
            note: note
        },
        success: res => {
            Swal.fire('✅ Transferred', res.message, 'success');
        },
        error: err => {
            if (err.status === 409) {
                Swal.fire('⚠️ Duplicate', 'This entry already exists in inquiries.', 'warning');
            } else {
                Swal.fire('❌ Failed', 'Could not transfer entry.', 'error');
            }
            console.error(err);
        }
    });
}

function importAllEntries() {
    if (!currentFormId) return;
    $.post("{{ route('fusion.import.all') }}", {
        _token: csrfToken,
        form_id: currentFormId
    }, res => {
        Swal.fire('✅ Imported', `${res.imported} new entries imported.`, 'success');
    }).fail(err => {
        Swal.fire('❌ Import Failed', 'Could not import entries.', 'error');
        console.error(err);
    });
}

function loadEntries(formId, search = '', page = 1) {
    $.get(`{{ url('admin/fusion-forms/entries') }}/${formId}?search=${search}&page=${page}`, function(response) {
        if (!response.fields.length) {
            $('#entriesTableContainer').html('<p class="text-warning">⚠️ No fields found for this form.</p>');
            $('#paginationLinks').empty();
            return;
        }

        let table = '<div class="d-flex justify-content-end mb-1"><button class="btn btn-success btn-sm" onclick="importAllEntries()">📥 Import All</button></div>';
        table += '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Date</th>';

        response.fields.forEach(f => {
            table += `<th>${f.field_label || f.field_name}</th>`;
        });
        table += '<th>Action</th></tr></thead><tbody>';

        response.entries.forEach(entry => {
            const date = entry.created_at ? new Date(entry.created_at).toLocaleString() : '-';
            table += `<tr><td>${date}</td>`;
            const rowFields = response.fields.map(f => {
                const val = entry.fields.find(x => x.field_id == f.id)?.value || '';
                return { name: f.field_name, label: f.field_label, value: val };
            });

            response.fields.forEach(f => {
                const val = entry.fields.find(x => x.field_id == f.id)?.value || '';
                table += `<td>${val}</td>`;
            });

            table += `<td><button class="btn btn-sm btn-outline-primary" onclick='importEntry(${entry.submission_id}, ${JSON.stringify(rowFields)})'>Transfer</button></td>`;
            table += '</tr>';
        });

        table += '</tbody></table></div>';
        $('#entriesTableContainer').html(table);

        let pagination = '';
        for (let i = 1; i <= response.totalPages; i++) {
            pagination += `<button class="btn btn-sm ${i === response.currentPage ? 'btn-primary' : 'btn-outline-primary'}" onclick="loadEntries(${formId}, '${search}', ${i})">${i}</button> `;
        }
        $('#paginationLinks').html(pagination);
    }).fail(() => {
        $('#entriesTableContainer').html('<p class="text-danger">❌ Could not load entries.</p>');
    });
}

$(document).ready(function() {
    loadForms();

    $(document).on('click', '#formList li', function() {
        currentFormId = $(this).data('id');
        loadEntries(currentFormId);
    });

    $('#searchInput').on('input', function() {
        if (currentFormId) {
            loadEntries(currentFormId, $(this).val());
        }
    });
});
</script>

@endsection
