@extends('admin.layouts.app')

@section('title', 'Moser WP Import')

@section('style')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<style>
    .import-container { max-width: 1000px; margin: 2rem auto; padding: 1.5rem; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .dropzone-box { border: 2px dashed #cbd5e1; background: #f8fafc; padding: 40px; text-align: center; cursor: pointer; border-radius: 6px; }
    .dropzone-box:hover { border-color: #3b82f6; background: #eff6ff; }
    .table-wrapper { overflow-x: auto; margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 6px; }
    table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    th { background: #f1f5f9; font-weight: 600; color: #475569; }
    .mapping-select { width: 100%; padding: 4px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem; }
    .btn-primary { background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-primary:disabled { background: #94a3b8; cursor: not-allowed; }
    .status-msg { margin-top: 10px; font-weight: 600; }
    .text-success { color: #16a34a; }
    .text-error { color: #dc2626; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="import-container">
            <h2 style="margin-bottom: 1rem;">Moser Wärmepumpe Import</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Lade die "Auftragsliste Wärmepumpe" hoch. Das System erstellt automatisch Leads, Häuser und Deals (Stage: Deal, Produkt: 16).
            </p>

            <div id="wpDropzone" class="dropzone-box">
                <div class="dz-message">
                    <span style="font-size: 1.1rem; color: #475569;">CSV Datei hier ablegen oder klicken</span>
                </div>
            </div>
            
            <div id="msgBox" class="status-msg"></div>

            <div id="previewSection" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; margin:0;">Spalten zuweisen</h3>
                    <button id="startImportBtn" class="btn-primary">Import Starten</button>
                </div>

                <div class="table-wrapper">
                    <table id="previewTable">
                        </table>
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

    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const previewSec = document.getElementById('previewSection');
        const table = document.getElementById('previewTable');
        const msgBox = document.getElementById('msgBox');
        const startBtn = document.getElementById('startImportBtn');
        
        let currentFile = null;

        // Configuration for expected columns
        const dbFields = {
            'ignore': '--- Ignorieren ---',
            'title': 'Anrede (Title)',
            'full_name': 'Name (wird getrennt)',
            'street': 'Straße',
            'postcode': 'PLZ',
            'city': 'Ort'
        };

        // Initialize Dropzone
        const dz = new Dropzone("#wpDropzone", {
            url: "#", // No auto upload
            autoProcessQueue: false,
            maxFiles: 1,
            acceptedFiles: ".csv,.txt",
            addedfile: function(file) {
                if(this.files.length > 1) this.removeFile(this.files[0]);
                currentFile = file;
                uploadPreview(file);
            }
        });

        function uploadPreview(file) {
            msgBox.textContent = "Lade Vorschau...";
            msgBox.className = "status-msg";
            
            const fd = new FormData();
            fd.append('file', file);

            fetch('{{ route("admin.leads.moser_wp.preview") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                renderTable(data);
                msgBox.textContent = "";
                previewSec.style.display = 'block';
            })
            .catch(err => {
                msgBox.textContent = "Fehler beim Lesen: " + err;
                msgBox.className = "status-msg text-error";
            });
        }

        function renderTable(data) {
            const headers = data.headers;
            const rows = data.rows;

            let html = '<thead><tr>';
            
            // Render Headers + Selects
            headers.forEach((h, index) => {
                // Auto-select logic based on name similarity
                let selected = 'ignore';
                const lowerH = h.toLowerCase();
                
                if(lowerH.includes('anrede')) selected = 'title';
                else if(lowerH.includes('name') && !lowerH.includes('kurz')) selected = 'full_name';
                else if(lowerH.includes('straße')) selected = 'street';
                else if(lowerH.includes('plz')) selected = 'postcode';
                else if(lowerH.includes('ort')) selected = 'city';

                // Build Select Options
                let options = '';
                for (const [key, label] of Object.entries(dbFields)) {
                    options += `<option value="${key}" ${key === selected ? 'selected' : ''}>${label}</option>`;
                }

                html += `
                    <th style="min-width: 150px;">
                        <div style="margin-bottom:5px;">${h}</div>
                        <select class="mapping-select" data-index="${index}">
                            ${options}
                        </select>
                    </th>
                `;
            });
            html += '</tr></thead><tbody>';

            // Render Preview Rows
            rows.forEach(row => {
                html += '<tr>';
                row.forEach(cell => {
                    html += `<td>${cell || ''}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody>';

            table.innerHTML = html;
        }

        startBtn.addEventListener('click', function() {
            if(!currentFile) return;

            startBtn.disabled = true;
            startBtn.textContent = "Import läuft...";
            
            // Gather Mapping
            const map = {};
            document.querySelectorAll('.mapping-select').forEach(sel => {
                map[sel.getAttribute('data-index')] = sel.value;
            });

            const fd = new FormData();
            fd.append('file', currentFile);
            
            // Append map as array/object
            for (const [key, value] of Object.entries(map)) {
                fd.append(`col_map[${key}]`, value);
            }

            fetch('{{ route("admin.leads.moser_wp.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                alert(`Import Fertig!\nErstellt: ${data.stats.created}`);
                window.location.reload();
            })
            .catch(err => {
                alert("Fehler beim Import.");
                console.error(err);
                startBtn.disabled = false;
                startBtn.textContent = "Import Starten";
            });
        });
    });
</script>
@endsection