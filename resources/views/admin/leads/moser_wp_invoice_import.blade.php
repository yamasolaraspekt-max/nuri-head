@extends('admin.layouts.app')

@section('title', 'WP Invoice Import')

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
            <h2 style="margin-bottom: 1rem;">Moser WP Schlussrechnungs-Import</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Lade die CSV mit den Schlussrechnungsbeträgen hoch.<br>
                Automatisch: Product ID 16, Status Archive, inkl. Netto/Brutto/Beleg.
            </p>

            <div id="invoiceDropzone" class="dropzone-box">
                <div class="dz-message">
                    <span style="font-size: 1.1rem; color: #475569;">CSV hier ablegen</span>
                </div>
            </div>
            
            <div id="msgBox" class="status-msg"></div>

            <div id="previewSection" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; margin:0;">Spalten zuweisen</h3>
                    <button id="startImportBtn" class="btn-primary">Import Starten</button>
                </div>

                <div class="table-wrapper">
                    <table id="previewTable"></table>
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

        // Auto-mapping keys
        const dbFields = {
            'ignore': '--- Ignorieren ---',
            'title': 'Anrede',
            'full_name': 'Name (wird getrennt)',
            'street': 'Straße',
            'postcode': 'PLZ',
            'city': 'Ort',
            'net_amount': 'Netto Betrag',
            'gross_amount': 'Brutto Betrag',
            'tax_amount': 'Steuer Betrag',
            'receipt_date': 'Belegdatum',
            'receipt_reference': 'Beleg Nr.',
            'note': 'Kurztext'
        };

        const dz = new Dropzone("#invoiceDropzone", {
            url: "#", 
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

            fetch('{{ route("admin.leads.moser_wp_invoice.preview") }}', {
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': csrfToken }, 
                body: fd
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Fehler beim Laden der Vorschau.');
                return data;
            })
            .then(data => {
                renderTable(data);
                msgBox.textContent = "";
                previewSec.style.display = 'block';
            })
            .catch(err => {
                msgBox.textContent = "Fehler: " + err.message;
                msgBox.className = "status-msg text-error";
            });
        }

        function renderTable(data) {
            const headers = data.headers;
            const rows = data.rows;
            let html = '<thead><tr>';
            
            headers.forEach((h, index) => {
                let selected = 'ignore';
                const lowerH = h.toLowerCase();
                
                if(lowerH.includes('anrede')) selected = 'title';
                else if(lowerH.includes('name') && !lowerH.includes('kurz')) selected = 'full_name';
                else if(lowerH.includes('straße')) selected = 'street';
                else if(lowerH.includes('plz')) selected = 'postcode';
                else if(lowerH.includes('ort')) selected = 'city';
                else if(lowerH.includes('netto')) selected = 'net_amount';
                else if(lowerH.includes('brutto')) selected = 'gross_amount';
                else if(lowerH.includes('steuer')) selected = 'tax_amount';
                else if(lowerH.includes('belegdatum')) selected = 'receipt_date';
                else if(lowerH.includes('beleg')) selected = 'receipt_reference';
                else if(lowerH.includes('kurztext')) selected = 'note';

                let options = '';
                for (const [key, label] of Object.entries(dbFields)) {
                    options += `<option value="${key}" ${key === selected ? 'selected' : ''}>${label}</option>`;
                }
                html += `<th style="min-width: 140px;"><div>${h}</div><select class="mapping-select" data-index="${index}">${options}</select></th>`;
            });
            html += '</tr></thead><tbody>';
            rows.forEach(row => {
                html += '<tr>';
                row.forEach(cell => html += `<td>${cell || ''}</td>`);
                html += '</tr>';
            });
            html += '</tbody>';
            table.innerHTML = html;
        }

        startBtn.addEventListener('click', function() {
            if(!currentFile) return;
            startBtn.disabled = true;
            startBtn.textContent = "Import läuft...";
            
            const map = {};
            document.querySelectorAll('.mapping-select').forEach(sel => map[sel.getAttribute('data-index')] = sel.value);

            const fd = new FormData();
            fd.append('file', currentFile);
            for (const [key, value] of Object.entries(map)) fd.append(`col_map[${key}]`, value);

            fetch('{{ route("admin.leads.moser_wp_invoice.store") }}', {
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': csrfToken }, 
                body: fd
            })
            .then(async res => {
                const data = await res.json();
                // THIS FIXES YOUR ERROR: Check if server returned an error (500)
                if (!res.ok) throw new Error(data.message || 'Server Fehler');
                return data;
            })
            .then(data => {
                alert(`Import Fertig!\nNeu erstellt: ${data.stats.created}\nAktualisiert: ${data.stats.updated}`);
                window.location.reload();
            })
            .catch(err => {
                // Now you will see the REAL error message
                alert("Ein Fehler ist aufgetreten:\n" + err.message);
                console.error(err);
                startBtn.disabled = false; 
                startBtn.textContent = "Import Starten";
            });
        });
    });
</script>
@endsection