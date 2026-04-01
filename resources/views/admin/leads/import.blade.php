@extends('admin.layouts.app')

@section('title', 'Lead-Import')

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <style>
        .lead-import-wrapper { margin: 1rem; }
        .dropzone-simple {
            border: 2px dashed #cbd5e1; padding: 20px; text-align: center;
            cursor: pointer; background: #f8fafc; border-radius: 8px;
        }
        .dropzone-simple.dz-drag-hover { border-color: #3b82f6; background: #eff6ff; }
        .hidden-col { display: none; }
        #csvError { font-size: 12px; margin-top: 8px; color: #b91c1c; }
        #csvPreviewTable { border-collapse: collapse; width: 100%; font-size: 11px; margin-top: 12px; }
        #csvPreviewTable th, #csvPreviewTable td {
            border: 1px solid #e5e7eb; padding: 4px 6px; vertical-align: top;
        }
        #csvPreviewTable th { background: #f3f4f6; text-align: left; }
        .col-meta-row select { font-size: 10px; max-width: 100px; }
        .small-label { font-size: 10px; color: #6b7280; display: block; }
        .target-label { color: #059669; font-weight: 600; font-size: 10px; }
    </style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="lead-import-wrapper">

                <h1 style="font-size: 18px; margin-bottom: 6px;">Lead-Import (Moser CSV)</h1>
                <p style="font-size: 12px; color: #4b5563; margin-bottom: 12px;">
                    Lade die CSV-Datei hoch. <br>
                    <strong>Automatische Regeln:</strong> <br>
                    &bull; "Typ" wird in Kunde/Privatkunde umgewandelt.<br>
                    &bull; "Konto" wird als Moser-ID gespeichert.<br>
                    &bull; Zu jedem Lead wird ein Objekt "Privatehaus" erstellt.
                </p>

                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-bottom:12px;">
                    <div>
                        <label class="small-label">Kontaktperson (Mitarbeiter)</label>
                        <select id="contactPersonSelect" style="font-size:11px; padding:4px;">
                            <option value="">– Automatisch / Keine –</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:inline-flex; align-items:center; gap:4px; font-size:11px;">
                            <input type="checkbox" id="checkDuplicates" checked>
                            <span>Duplikate prüfen (Email oder Name+Adresse)</span>
                        </label>
                    </div>
                </div>

                <form action="#" class="dropzone dropzone-simple" id="leadImportDropzone">
                    @csrf
                    <div class="dz-message">
                        <div>CSV hier ablegen.</div>
                        <div id="csvFilename" style="font-size: 11px; color:#374151; margin-top: 4px;"></div>
                    </div>
                </form>

                <div id="csvError" class="hidden"></div>

                <div id="csvPreviewSection" style="margin-top: 16px; display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size: 12px; font-weight: 600;">Vorschau (Erste 20 Zeilen)</span>
                        <button type="button" id="importConfirmBtn" 
                            style="font-size: 11px; padding:6px 10px; background:#059669; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                            Import Starten
                        </button>
                    </div>
                    
                    <div style="overflow-x:auto;">
                        <table id="csvPreviewTable"></table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    document.addEventListener('DOMContentLoaded', function () {
        const dzElement      = document.getElementById('leadImportDropzone');
        const errorEl        = document.getElementById('csvError');
        const previewSec     = document.getElementById('csvPreviewSection');
        const previewTable   = document.getElementById('csvPreviewTable');
        const importBtn      = document.getElementById('importConfirmBtn');
        const csrfToken      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        let currentFile      = null;

        function renderPreview(data) {
            const headers = data.headers || [];
            const rows    = data.rows || [];
            const mapping = data.mapping || [];

            let html = '<thead><tr>';
            
            // Header Names
            headers.forEach(h => html += `<th>${h}</th>`);
            html += '</tr><tr class="col-meta-row">';

            // Mapping Row
            headers.forEach((h, idx) => {
                const target = mapping[idx];
                const parts  = target ? target.split('.') : [null, null];
                const table  = parts[0] || '';
                const col    = parts[1] || '';
                
                html += `<th data-col-index="${idx}" data-table="${table}" data-column="${col}">`;
                
                if (target) {
                    html += `<span class="target-label">Ziel: ${col}</span>`;
                } else {
                    html += `<span class="small-label" style="color:#ef4444;">Ignoriert</span>`;
                }

                // Action Select
                html += `<div style="margin-top:2px;">
                    <select class="col-action" data-col-index="${idx}">
                        <option value="import" ${target ? 'selected' : ''}>Import</option>
                        <option value="ignore" ${!target ? 'selected' : ''}>Ignorieren</option>
                    </select>
                </div>`;
                html += `</th>`;
            });
            html += '</tr></thead><tbody>';

            // Data Rows
            rows.forEach(row => {
                html += '<tr>';
                headers.forEach((_, idx) => {
                    html += `<td>${row[idx] || ''}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody>';

            previewTable.innerHTML = html;
            previewSec.style.display = 'block';
        }

        function buildConfig() {
            const config = [];
            previewTable.querySelectorAll('thead tr.col-meta-row th').forEach((th) => {
                const idx    = th.getAttribute('data-col-index');
                const table  = th.getAttribute('data-table');
                const col    = th.getAttribute('data-column');
                const action = th.querySelector('.col-action').value;

                config.push({
                    index: idx,
                    table: table,
                    column: col,
                    ignore: (action === 'ignore')
                });
            });
            return config;
        }

        function uploadAndPreview(file) {
            errorEl.style.display = 'none';
            const fd = new FormData();
            fd.append('file', file);

            fetch('{{ route('admin.leads.import.preview') }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: fd
            })
            .then(res => res.json())
            .then(data => {
                if(data.message && !data.headers) throw new Error(data.message);
                renderPreview(data);
            })
            .catch(err => {
                errorEl.textContent = err.message || 'Fehler beim Laden.';
                errorEl.style.display = 'block';
            });
        }

        const dz = new Dropzone('#leadImportDropzone', {
            url: '#', autoProcessQueue: false, maxFiles: 1, acceptedFiles: '.csv,.txt'
        });

        dz.on('addedfile', file => {
            currentFile = file;
            uploadAndPreview(file);
        });

        importBtn.addEventListener('click', () => {
            if(!currentFile) return;
            importBtn.textContent = 'Speichere...';
            importBtn.disabled = true;

            const fd = new FormData();
            fd.append('file', currentFile);
            fd.append('config', JSON.stringify(buildConfig()));
            fd.append('contact_person_id', document.getElementById('contactPersonSelect').value);
            fd.append('check_duplicates', document.getElementById('checkDuplicates').checked ? 1 : 0);

            fetch('{{ route('admin.leads.import.confirm') }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: fd
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message + `\nLeads: ${data.created_leads}\nObjekte: ${data.created_objects}\nDuplikate: ${data.duplicates_matched}`);
                window.location.reload();
            })
            .catch(err => alert('Fehler: ' + err.message))
            .finally(() => {
                importBtn.textContent = 'Import Starten';
                importBtn.disabled = false;
            });
        });
    });
</script>
@endsection