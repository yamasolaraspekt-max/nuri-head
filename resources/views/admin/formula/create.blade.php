@extends('admin.layouts.app')

@section('title', 'Formular Builder')

@section('style')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/customer_product.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

<style>
:root{
    --bg:#f3f4f6;
    --card:#fff;
    --text:#111827;
    --muted:#6b7280;
    --border:#e5e7eb;
    --primary:var(--sa-accent);
    --primary-hover:var(--sa-accent-hover);
    --primary-soft:var(--sa-accent-light);
    --danger:#ef4444;
    --danger-soft:#fef2f2;
    --warning:#f59e0b;
    --warning-soft:#fffbeb;
    --info:#74b2d4;
    --info-soft:#eef7fc;
    --shadow:0 8px 24px -18px rgba(0,0,0,.35);
}

.fb-wrap{ 
    color:var(--text);
    font-family:Inter, system-ui, -apple-system, sans-serif;
}

.fb-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.fb-title{
    font-size:26px;
    font-weight:900;
    letter-spacing:-.03em;
}

.fb-sub{
    color:var(--muted);
    font-size:14px;
    margin-top:4px;
}

.fb-actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.fb-btn{
    border:0;
    background:var(--primary);
    color:#fff;
    border-radius:10px;
    padding:10px 15px;
    font-weight:900;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:40px;
}

.fb-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
}

.fb-btn-soft{
    border:1px solid var(--border);
    background:#fff;
    color:var(--text);
    border-radius:10px;
    padding:10px 14px;
    font-weight:800;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:40px;
}

.fb-btn-soft:hover{
    background:#f9fafb;
    color:var(--text);
    text-decoration:none;
}

.fb-btn-danger{
    border:1px solid #fecaca;
    background:var(--danger-soft);
    color:var(--danger);
    border-radius:10px;
    padding:9px 12px;
    font-weight:900;
    cursor:pointer;
}

.fb-btn-warning{
    border:1px solid #fde68a;
    background:var(--warning-soft);
    color:var(--warning);
    border-radius:10px;
    padding:9px 12px;
    font-weight:900;
    cursor:pointer;
}

.fb-icon-btn{
    width:36px;
    height:36px;
    border-radius:9px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    color:var(--muted);
    font-weight:900;
    text-decoration:none;
}

.fb-icon-btn:hover{
    background:#f9fafb;
    color:var(--text);
    text-decoration:none;
}

.fb-grid{
    display:grid;
    grid-template-columns:420px minmax(0,1fr);
    gap:18px;
    align-items:start;
}

.fb-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:18px;
    box-shadow:var(--shadow);
    overflow:hidden;
}

.fb-card-head{
    padding:16px 18px;
    background:#fafafa;
    border-bottom:1px solid var(--border);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.fb-card-title{
    font-size:16px;
    font-weight:900;
    margin:0;
}

.fb-card-sub{
    color:var(--muted);
    font-size:12px;
    margin-top:3px;
}

.fb-card-body{
    padding:18px;
}

.fb-builder{
    position:sticky;
    top:92px;
}

.fb-form-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:13px;
}

.fb-two{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.fb-field{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.fb-label{
    font-size:11px;
    color:var(--muted);
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.fb-input,
.fb-select,
.fb-textarea{
    width:100%;
    border:1px solid var(--border);
    background:#f9fafb;
    border-radius:10px;
    padding:10px 12px;
    outline:none;
    font-size:14px;
    min-height:40px;
}

.fb-textarea{
    min-height:130px;
    resize:vertical;
    font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.fb-input:focus,
.fb-select:focus,
.fb-textarea:focus{
    border-color:var(--primary);
    background:#fff;
    box-shadow:0 0 0 3px var(--primary-soft);
}

.fb-check{
    display:flex;
    align-items:center;
    gap:10px;
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:12px;
    padding:10px 12px;
    font-weight:800;
    color:#374151;
}

.fb-check input{
    width:17px;
    height:17px;
}

.fb-toolbar{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:14px;
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:end;
    margin-bottom:18px;
}

.fb-toolbar .fb-field{
    flex:1;
    min-width:260px;
}

.fb-main{
    display:grid;
    gap:18px;
}

.fb-tabs{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    padding:0;
    margin:0 0 16px;
    list-style:none;
    border:0;
}

.fb-tab-btn{
    border:1px solid var(--border);
    background:#fff;
    color:var(--muted);
    border-radius:999px;
    padding:9px 13px;
    font-weight:900;
    cursor:pointer;
}

.fb-tab-btn.active{
    background:var(--primary);
    border-color:var(--primary);
    color:#fff;
}

.fb-tab-pane{
    display:none;
}

.fb-tab-pane.active{
    display:block;
}

.fb-section-box{
    border:1px solid var(--border);
    border-radius:16px;
    background:#fff;
    overflow:hidden;
}

.fb-section-head{
    padding:14px 16px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    display:flex;
    justify-content:space-between;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
}

.fb-section-title{
    font-weight:900;
}

.fb-section-count{
    color:var(--muted);
    font-size:12px;
    font-weight:800;
}

.sortable-container{
    padding:14px;
    display:grid;
    gap:12px;
    min-height:70px;
}

.draggable-field{
    border:1px solid var(--border);
    background:#fff;
    border-radius:14px;
    padding:13px;
    box-shadow:0 6px 18px -18px rgba(0,0,0,.45);
}

.draggable-field:hover{
    border-color:#d1d5db;
}

.sortable-ghost{
    opacity:.45;
    background:var(--primary-soft);
}

.fb-preview-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    margin-bottom:8px;
}

.fb-preview-label{
    font-weight:900;
    color:#374151;
}

.fb-preview-name{
    color:var(--muted);
    font-size:12px;
    margin-top:2px;
}

.fb-preview-actions{
    display:flex;
    align-items:center;
    gap:7px;
}

.fb-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:5px 10px;
    font-size:11px;
    font-weight:900;
    background:var(--primary-soft);
    color:var(--primary);
    border:1px solid #d8edaa;
}

.fb-badge.info{
    background:var(--info-soft);
    color:var(--info);
    border-color:#c0d8ea;
}

.fb-badge.warning{
    background:var(--warning-soft);
    color:var(--warning);
    border-color:#fde68a;
}

.fb-preview-control{
    margin-top:10px;
}

.fb-preview-control input[type="checkbox"]{
    width:18px;
    height:18px;
}

.fb-empty{
    padding:50px;
    text-align:center;
    color:var(--muted);
}

.fb-empty-small{
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    padding:24px;
    text-align:center;
    color:var(--muted);
}

.fb-json{
    display:grid;
    grid-template-columns:1fr;
    gap:14px;
}

.fb-json-output{
    min-height:220px;
    white-space:pre;
}

.fb-error{
    color:var(--danger);
    background:var(--danger-soft);
    border:1px solid #fecaca;
    border-radius:12px;
    padding:12px;
    font-size:13px;
    font-weight:800;
    display:none;
}

.fb-error.show{
    display:block;
}

.fb-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:3000;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.fb-toast{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px 14px;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
    min-width:280px;
}

.hidden-option{
    display:none !important;
}

.multi-group-wrapper{
    display:grid;
    gap:10px;
}

.multi-group-row{
    display:flex;
    gap:10px;
    align-items:center;
}

.multi-group-row input{
    flex:1;
}

@media(max-width:1100px){
    .fb-grid{
        grid-template-columns:1fr;
    }

    .fb-builder{
        position:relative;
        top:auto;
    }
}

@media(max-width:700px){
    .fb-wrap{
        margin-top:90px;
        padding:0 16px 32px;
    }

    .fb-head{
        align-items:stretch;
    }

    .fb-actions,
    .fb-actions .fb-btn,
    .fb-actions .fb-btn-soft{
        width:100%;
    }

    .fb-two{
        grid-template-columns:1fr;
    }

    .fb-toolbar{
        align-items:stretch;
    }

    .fb-toolbar .fb-field{
        min-width:100%;
    }

    .fb-toolbar .fb-btn,
    .fb-toolbar .fb-btn-soft{
        width:100%;
    }
}
</style>
@endsection

@section('content')
<div class="fb-wrap">

    <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
    <input type="hidden" name="id" id="id" value="{{ $id }}">

    <div class="fb-head">
        <div>
            <div class="fb-title">FORMULAR BUILDER</div>
            <div class="fb-sub">Produkt-Formular erstellen, Felder sortieren, Formeln testen und JSON speichern.</div>
        </div>

        <div class="fb-actions">
            <a href="{{ route('product.formula.index') }}" class="fb-btn-soft">← Zurück</a>
            <button type="button" class="fb-btn-soft" onclick="exportToJson()">JSON exportieren</button>
            <button type="button" class="fb-btn" onclick="saveForm()">Formular speichern</button>
        </div>
    </div>

    <div class="fb-toolbar">
        <div class="fb-field">
            <label class="fb-label">Section Name</label>
            <input type="text" id="sectionName" class="fb-input" placeholder="z.B. Kundendaten, Maße, Berechnung">
        </div>

        <button type="button" class="fb-btn-soft" onclick="clearBuilderForm()">Felder leeren</button>
        <button type="button" class="fb-btn" onclick="addFieldToSection()">+ Field hinzufügen</button>
    </div>

    <div class="fb-grid">

        {{-- Builder --}}
        <div class="fb-card fb-builder">
            <div class="fb-card-head">
                <div>
                    <h3 class="fb-card-title">Field Builder</h3>
                    <div class="fb-card-sub">Neues Feld definieren oder bestehendes Feld bearbeiten.</div>
                </div>
            </div>

            <div class="fb-card-body">
                <div class="fb-form-grid">

                    <div class="fb-field">
                        <label class="fb-label">Field Label</label>
                        <input type="text" id="fieldLabel" class="fb-input" placeholder="z.B. Breite">
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Field Name</label>
                        <input type="text" id="fieldName" class="fb-input" placeholder="z.B. width">
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Field Type</label>
                        <select id="fieldType" class="fb-select" onchange="toggleFieldTypeOptions()">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="formula">Formula</option>
                            <option value="textarea">Textarea</option>
                            <option value="date">Date</option>
                            <option value="file">File</option>
                            <option value="multi-group">Multi Group</option>
                        </select>
                    </div>

                    <div id="selectOptions" class="fb-field hidden-option">
                        <label class="fb-label">Options</label>
                        <input type="text" id="options" class="fb-input" placeholder="comma separated: Ja,Nein,Vielleicht">
                    </div>

                    <div id="formulaField" class="fb-field hidden-option">
                        <label class="fb-label">Formula</label>
                        <input type="text" id="formula" class="fb-input" placeholder="e.g. mul(width, height)">
                    </div>

                    <div id="multiGroupFields" class="fb-field hidden-option">
                        <label class="fb-label">Multi-Group Subfields</label>
                        <input type="text" id="multiFields" class="fb-input" placeholder="e.g. width,height,depth">
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Default Value</label>
                        <input type="text" id="defaultValue" class="fb-input">
                    </div>

                    <div class="fb-two">
                        <div class="fb-field">
                            <label class="fb-label">Min</label>
                            <input type="number" id="minValue" class="fb-input">
                        </div>

                        <div class="fb-field">
                            <label class="fb-label">Max</label>
                            <input type="number" id="maxValue" class="fb-input">
                        </div>
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Regex Pattern</label>
                        <input type="text" id="pattern" class="fb-input" placeholder="e.g. ^\\d{4}$">
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Advanced Condition JS</label>
                        <input type="text" id="advancedCondition" class="fb-input" placeholder="e.g. salary > 2000">
                    </div>

                    <label class="fb-check">
                        <input type="checkbox" id="required">
                        Required
                    </label>

                    <button type="button" class="fb-btn" onclick="addFieldToSection()">+ Add Field</button>
                </div>
            </div>
        </div>

        {{-- Main Preview --}}
        <div class="fb-main">

            <div class="fb-card">
                <div class="fb-card-head">
                    <div>
                        <h3 class="fb-card-title">Live Form Preview</h3>
                        <div class="fb-card-sub">Felder können per Drag & Drop sortiert werden.</div>
                    </div>

                    <span class="fb-badge info" id="fieldCounter">0 Felder</span>
                </div>

                <div class="fb-card-body">
                    <ul class="fb-tabs" id="sectionTabs"></ul>
                    <div id="tabContent"></div>
                </div>
            </div>

            <div class="fb-card">
                <div class="fb-card-head">
                    <div>
                        <h3 class="fb-card-title">JSON Output</h3>
                        <div class="fb-card-sub">Exportierte Struktur des Formulars.</div>
                    </div>

                    <button type="button" class="fb-btn-soft" onclick="copyJsonOutput()">Kopieren</button>
                </div>

                <div class="fb-card-body">
                    <textarea id="jsonOutput" class="fb-textarea fb-json-output" readonly></textarea>
                    <div id="errorOutput" class="fb-error"></div>
                </div>
            </div>

            <div class="fb-card">
                <div class="fb-card-head">
                    <div>
                        <h3 class="fb-card-title">Import JSON</h3>
                        <div class="fb-card-sub">Vorhandene JSON-Struktur einfügen und laden.</div>
                    </div>
                </div>

                <div class="fb-card-body">
                    <div class="fb-json">
                        <textarea id="importJson" class="fb-textarea" rows="8" placeholder="Paste JSON here"></textarea>
                        <div>
                            <button type="button" class="fb-btn-soft" onclick="importFromJson()">Importieren</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="fb-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/form-safe-eval.js') }}"></script>{{-- FS-07: sicherer Evaluator statt new Function --}}

<script>
const fieldsBySection = {};

function toast(type, message) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const el = document.createElement('div');
    el.className = 'fb-toast';
    el.innerHTML = `
        <strong>${type === 'ok' ? 'Erfolgreich' : 'Hinweis'}</strong>
        <div style="font-size:13px;color:#374151;margin-top:4px;">${message}</div>
    `;

    wrap.appendChild(el);

    setTimeout(() => {
        try { el.remove(); } catch(e) {}
    }, 3500);
}

function showError(message) {
    const errorOutput = document.getElementById('errorOutput');
    if (!errorOutput) return;

    errorOutput.classList.add('show');
    errorOutput.innerHTML = message;
}

function clearError() {
    const errorOutput = document.getElementById('errorOutput');
    if (!errorOutput) return;

    errorOutput.classList.remove('show');
    errorOutput.innerHTML = '';
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function slugName(value) {
    return String(value || '')
        .trim()
        .toLowerCase()
        .replace(/[^\wäöüß]+/gi, '_')
        .replace(/^_+|_+$/g, '');
}

function toNum(val) {
    return val === '' || val == null || isNaN(val) ? 0 : Number(val);
}

function add(a, b) {
    return toNum(a) + toNum(b);
}

function sub(a, b) {
    return toNum(a) - toNum(b);
}

function mul(a, b) {
    return toNum(a) * toNum(b);
}

function div(a, b) {
    const d = toNum(b);
    return d === 0 ? 0 : toNum(a) / d;
}

function round(v, p = 0) {
    return Math.round(toNum(v) * 10 ** p) / 10 ** p;
}

function min(...args) {
    return Math.min(...args.map(toNum));
}

function max(...args) {
    return Math.max(...args.map(toNum));
}

function evaluateFormula(formula, values) {
    // FS-07: eval-frei über den sicheren Evaluator (kein new Function).
    return window.FormSafeEval.evalArithmetic(formula, values);
}

function collectFormData() {
    const formData = {};

    document.querySelectorAll('#tabContent input, #tabContent select, #tabContent textarea').forEach(input => {
        if (!input.name) return;

        if (input.type === 'checkbox') {
            formData[input.name] = input.checked;
            return;
        }

        formData[input.name] = input.value;
    });

    return formData;
}

function updateFormulas() {
    const values = collectFormData();

    document.querySelectorAll('#tabContent input[data-formula-field="1"]').forEach(input => {
        const field = Object.values(fieldsBySection)
            .flat()
            .find(f => f.name === input.name && f.type === 'formula');

        if (field?.formula) {
            input.value = evaluateFormula(field.formula, values);
        }
    });
}

function updateFieldCounter() {
    const total = Object.values(fieldsBySection).reduce((sum, fields) => sum + fields.length, 0);
    const counter = document.getElementById('fieldCounter');

    if (counter) {
        counter.textContent = `${total} Feld${total === 1 ? '' : 'er'}`;
    }
}

function toggleFieldTypeOptions() {
    const type = document.getElementById('fieldType').value;

    document.getElementById('selectOptions').classList.toggle('hidden-option', type !== 'select');
    document.getElementById('formulaField').classList.toggle('hidden-option', type !== 'formula');
    document.getElementById('multiGroupFields').classList.toggle('hidden-option', type !== 'multi-group');
}

function activateTab(tabId) {
    document.querySelectorAll('.fb-tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.target === tabId);
    });

    document.querySelectorAll('.fb-tab-pane').forEach(pane => {
        pane.classList.toggle('active', pane.id === tabId);
    });
}

function renderFieldInput(field, formData) {
    const value = formData[field.name] ?? field.defaultValue ?? '';

    if (field.type === 'select') {
        const options = String(field.options || '')
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);

        return `
            <select class="fb-select" name="${escapeHtml(field.name)}" ${field.required ? 'required' : ''}>
                <option value="">Bitte wählen</option>
                ${options.map(option => `
                    <option value="${escapeHtml(option)}" ${String(value) === String(option) ? 'selected' : ''}>
                        ${escapeHtml(option)}
                    </option>
                `).join('')}
            </select>
        `;
    }

    if (field.type === 'textarea') {
        return `
            <textarea class="fb-textarea" name="${escapeHtml(field.name)}" ${field.required ? 'required' : ''}>${escapeHtml(value)}</textarea>
        `;
    }

    if (field.type === 'checkbox') {
        return `
            <label class="fb-check" style="display:inline-flex;">
                <input type="checkbox" name="${escapeHtml(field.name)}" ${value ? 'checked' : ''}>
                Aktiv
            </label>
        `;
    }

    if (field.type === 'formula') {
        return `
            <input
                class="fb-input"
                name="${escapeHtml(field.name)}"
                type="text"
                data-formula-field="1"
                value="${escapeHtml(evaluateFormula(field.formula || '0', formData))}"
                readonly
            >
        `;
    }

    if (field.type === 'file') {
        return `
            <input
                class="fb-input"
                name="${escapeHtml(field.name)}"
                type="file"
                ${field.required ? 'required' : ''}
            >
        `;
    }

    if (field.type === 'multi-group') {
        const subfields = String(field.subfields || '')
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);

        return `
            <div class="multi-group-wrapper">
                ${subfields.map(sub => `
                    <div class="multi-group-row">
                        <label class="fb-label" style="min-width:90px;margin:0;">${escapeHtml(sub)}</label>
                        <input
                            class="fb-input"
                            name="${escapeHtml(field.name)}_${escapeHtml(sub)}"
                            type="text"
                            placeholder="${escapeHtml(sub)}"
                        >
                    </div>
                `).join('')}
            </div>
        `;
    }

    return `
        <input
            class="fb-input"
            name="${escapeHtml(field.name)}"
            type="${escapeHtml(field.type || 'text')}"
            value="${escapeHtml(value)}"
            ${field.required ? 'required' : ''}
            ${field.min !== '' && field.min != null ? `min="${escapeHtml(field.min)}"` : ''}
            ${field.max !== '' && field.max != null ? `max="${escapeHtml(field.max)}"` : ''}
            ${field.pattern ? `pattern="${escapeHtml(field.pattern)}"` : ''}
        >
    `;
}

function renderPreview() {
    const sectionTabs = document.getElementById('sectionTabs');
    const tabContent = document.getElementById('tabContent');

    sectionTabs.innerHTML = '';
    tabContent.innerHTML = '';

    const sections = Object.entries(fieldsBySection);
    const formData = collectFormData();

    if (!sections.length) {
        tabContent.innerHTML = `
            <div class="fb-empty">
                Noch keine Felder vorhanden. Links eine Section und ein Field erstellen.
            </div>
        `;
        updateFieldCounter();
        return;
    }

    sections.forEach(([section, fields], index) => {
        const safeId = `tab-${slugName(section) || index}`;

        const tabItem = document.createElement('li');
        tabItem.innerHTML = `
            <button type="button" class="fb-tab-btn ${index === 0 ? 'active' : ''}" data-target="${safeId}">
                ${escapeHtml(section)}
            </button>
        `;
        sectionTabs.appendChild(tabItem);

        const tabPane = document.createElement('div');
        tabPane.className = `fb-tab-pane ${index === 0 ? 'active' : ''}`;
        tabPane.id = safeId;

        tabPane.innerHTML = `
            <div class="fb-section-box">
                <div class="fb-section-head">
                    <div>
                        <div class="fb-section-title">${escapeHtml(section)}</div>
                        <div class="fb-section-count">${fields.length} Feld${fields.length === 1 ? '' : 'er'}</div>
                    </div>

                    <button type="button" class="fb-btn-danger" onclick="deleteSection('${escapeHtml(section)}')">
                        Section löschen
                    </button>
                </div>

                <div class="sortable-container" data-section="${escapeHtml(section)}"></div>
            </div>
        `;

        const container = tabPane.querySelector('.sortable-container');

        fields.forEach((field, i) => {
            if (field.advancedCondition) {
                // FS-07: eval-freie Sichtbarkeitsprüfung (kein new Function).
                if (!window.FormSafeEval.evalCondition(field.advancedCondition, formData)) return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'draggable-field';
            wrapper.dataset.index = i;

            wrapper.innerHTML = `
                <div class="fb-preview-top">
                    <div>
                        <div class="fb-preview-label">${escapeHtml(field.label || 'Ohne Label')}</div>
                        <div class="fb-preview-name">
                            ${escapeHtml(field.name || 'ohne_name')}
                            · <span class="fb-badge">${escapeHtml(field.type || 'text')}</span>
                            ${field.required ? '<span class="fb-badge warning">Required</span>' : ''}
                        </div>
                    </div>

                    <div class="fb-preview-actions">
                        <button type="button" class="fb-icon-btn" onclick="editField('${escapeHtml(section)}', ${i})" title="Bearbeiten">✎</button>
                        <button type="button" class="fb-icon-btn" onclick="duplicateField('${escapeHtml(section)}', ${i})" title="Duplizieren">⧉</button>
                        <button type="button" class="fb-icon-btn" onclick="deleteField('${escapeHtml(section)}', ${i})" title="Löschen">×</button>
                    </div>
                </div>

                <div class="fb-preview-control">
                    ${renderFieldInput(field, formData)}
                </div>
            `;

            container.appendChild(wrapper);
        });

        tabContent.appendChild(tabPane);

        Sortable.create(container, {
            animation: 150,
            handle: '.draggable-field',
            ghostClass: 'sortable-ghost',
            onEnd(evt) {
                const sectionName = evt.from.dataset.section;
                const item = fieldsBySection[sectionName].splice(evt.oldIndex, 1)[0];
                fieldsBySection[sectionName].splice(evt.newIndex, 0, item);
                renderPreview();
            }
        });
    });

    updateFieldCounter();
    updateFormulas();
}

function getCurrentFieldData() {
    const label = document.getElementById('fieldLabel').value.trim();
    const manualName = document.getElementById('fieldName').value.trim();

    return {
        label: label,
        name: manualName || slugName(label),
        type: document.getElementById('fieldType').value,
        defaultValue: document.getElementById('defaultValue').value,
        options: document.getElementById('options').value,
        formula: document.getElementById('formula').value,
        subfields: document.getElementById('multiFields').value,
        min: document.getElementById('minValue').value,
        max: document.getElementById('maxValue').value,
        pattern: document.getElementById('pattern').value,
        advancedCondition: document.getElementById('advancedCondition').value,
        required: document.getElementById('required').checked
    };
}

function addFieldToSection() {
    clearError();

    const section = document.getElementById('sectionName').value.trim();
    const field = getCurrentFieldData();

    if (!section) {
        toast('bad', 'Bitte zuerst einen Section Name eingeben.');
        return;
    }

    if (!field.label) {
        toast('bad', 'Bitte Field Label eingeben.');
        return;
    }

    if (!field.name) {
        toast('bad', 'Bitte Field Name eingeben.');
        return;
    }

    if (!fieldsBySection[section]) {
        fieldsBySection[section] = [];
    }

    fieldsBySection[section].push(field);

    renderPreview();
    exportToJson(false);
    clearBuilderForm(false);

    toast('ok', 'Field wurde hinzugefügt.');
}

function editField(section, index) {
    const f = fieldsBySection[section]?.[index];

    if (!f) return;

    document.getElementById('sectionName').value = section;
    document.getElementById('fieldLabel').value = f.label || '';
    document.getElementById('fieldName').value = f.name || '';
    document.getElementById('fieldType').value = f.type || 'text';
    document.getElementById('defaultValue').value = f.defaultValue || '';
    document.getElementById('options').value = f.options || '';
    document.getElementById('formula').value = f.formula || '';
    document.getElementById('multiFields').value = f.subfields || '';
    document.getElementById('minValue').value = f.min || '';
    document.getElementById('maxValue').value = f.max || '';
    document.getElementById('pattern').value = f.pattern || '';
    document.getElementById('advancedCondition').value = f.advancedCondition || '';
    document.getElementById('required').checked = !!f.required;

    toggleFieldTypeOptions();

    fieldsBySection[section].splice(index, 1);

    if (!fieldsBySection[section].length) {
        delete fieldsBySection[section];
    }

    renderPreview();
    exportToJson(false);

    toast('ok', 'Field wurde in den Builder geladen.');
}

function duplicateField(section, index) {
    const f = fieldsBySection[section]?.[index];

    if (!f) return;

    const copy = {
        ...f,
        label: `${f.label || 'Field'} Kopie`,
        name: `${f.name || 'field'}_copy`
    };

    fieldsBySection[section].splice(index + 1, 0, copy);

    renderPreview();
    exportToJson(false);

    toast('ok', 'Field wurde dupliziert.');
}

function deleteField(section, index) {
    if (!confirm('Dieses Field wirklich löschen?')) return;

    fieldsBySection[section].splice(index, 1);

    if (!fieldsBySection[section].length) {
        delete fieldsBySection[section];
    }

    renderPreview();
    exportToJson(false);

    toast('ok', 'Field wurde gelöscht.');
}

function deleteSection(section) {
    if (!confirm('Diese Section und alle Felder wirklich löschen?')) return;

    delete fieldsBySection[section];

    renderPreview();
    exportToJson(false);

    toast('ok', 'Section wurde gelöscht.');
}

function clearBuilderForm(keepSection = true) {
    if (!keepSection) {
        document.getElementById('fieldLabel').value = '';
        document.getElementById('fieldName').value = '';
    } else {
        document.getElementById('sectionName').value = '';
        document.getElementById('fieldLabel').value = '';
        document.getElementById('fieldName').value = '';
    }

    document.getElementById('fieldType').value = 'text';
    document.getElementById('defaultValue').value = '';
    document.getElementById('options').value = '';
    document.getElementById('formula').value = '';
    document.getElementById('multiFields').value = '';
    document.getElementById('minValue').value = '';
    document.getElementById('maxValue').value = '';
    document.getElementById('pattern').value = '';
    document.getElementById('advancedCondition').value = '';
    document.getElementById('required').checked = false;

    toggleFieldTypeOptions();
}

function exportToJson(showToast = true) {
    const updated = {};

    Object.entries(fieldsBySection).forEach(([section, fields]) => {
        updated[section] = fields.map(field => {
            const f = { ...field };
            const input = document.querySelector(`[name="${CSS.escape(f.name)}"]`);

            if (input && f.type !== 'file') {
                f.defaultValue = input.type === 'checkbox' ? input.checked : input.value;
            }

            return f;
        });
    });

    Object.keys(fieldsBySection).forEach(key => delete fieldsBySection[key]);
    Object.assign(fieldsBySection, updated);

    document.getElementById('jsonOutput').value = JSON.stringify(fieldsBySection, null, 2);
    clearError();

    if (showToast) {
        toast('ok', 'JSON wurde exportiert.');
    }
}

function importFromJson() {
    clearError();

    const raw = document.getElementById('importJson').value.trim();

    if (!raw) {
        toast('bad', 'Bitte JSON einfügen.');
        return;
    }

    try {
        const parsed = JSON.parse(raw);

        if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
            throw new Error('JSON muss ein Objekt mit Sections sein.');
        }

        Object.keys(fieldsBySection).forEach(key => delete fieldsBySection[key]);
        Object.assign(fieldsBySection, parsed);

        renderPreview();
        exportToJson(false);

        toast('ok', 'JSON wurde importiert.');
    } catch (error) {
        showError(escapeHtml(error.message || 'Ungültiges JSON.'));
    }
}

function copyJsonOutput() {
    const textarea = document.getElementById('jsonOutput');

    if (!textarea.value.trim()) {
        exportToJson(false);
    }

    textarea.select();
    textarea.setSelectionRange(0, 999999);

    try {
        document.execCommand('copy');
        toast('ok', 'JSON wurde kopiert.');
    } catch (e) {
        toast('bad', 'Kopieren nicht möglich.');
    }
}

function saveForm() {
    clearError();
    exportToJson(false);

    const payload = {
        product_id: document.getElementById('product_id').value,
        id: document.getElementById('id').value,
        formulas: fieldsBySection
    };

    fetch("{{ route('product-formula.save') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const contentType = res.headers.get('content-type') || '';

        if (res.status === 422) {
            const err = await res.json();
            throw err.errors || err;
        }

        if (!contentType.includes('application/json')) {
            const text = await res.text();
            throw new Error('Expected JSON:\n' + text);
        }

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'Speichern fehlgeschlagen.');
        }

        return data;
    })
    .then(result => {
        if (result.success) {
            toast('ok', 'Formular wurde gespeichert.');
            window.location.href = "{{ route('product.formula.index') }}";
            return;
        }

        showError('Error: ' + escapeHtml(result.message || 'Unknown error'));
    })
    .catch(err => {
        if (typeof err === 'object' && !Array.isArray(err) && !(err instanceof Error)) {
            let html = '<ul style="margin:0;padding-left:18px;">';

            Object.values(err).forEach(group => {
                if (Array.isArray(group)) {
                    group.forEach(msg => html += `<li>${escapeHtml(msg)}</li>`);
                } else {
                    html += `<li>${escapeHtml(group)}</li>`;
                }
            });

            html += '</ul>';
            showError(html);
            return;
        }

        showError('Network error:<br>' + escapeHtml(err.message || err));
    });
}

document.addEventListener('click', function(e) {
    const tabBtn = e.target.closest('.fb-tab-btn');

    if (tabBtn) {
        activateTab(tabBtn.dataset.target);
    }
});

document.addEventListener('input', function(e) {
    if (e.target.closest('#tabContent input, #tabContent select, #tabContent textarea')) {
        updateFormulas();
    }

    if (e.target.id === 'fieldLabel' && !document.getElementById('fieldName').value.trim()) {
        document.getElementById('fieldName').value = slugName(e.target.value);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    toggleFieldTypeOptions();
    renderPreview();
    exportToJson(false);
});
</script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Produktliste',
                url: "{{ url('product') }}",
            },
            {
                label: 'Produkt-Formulare',
                url: "{{ url('product-formula') }}",
                clickable: false
            },

            {
                label: 'Nue Anlegen',
                url: "{{ url()->current() }}",
                clickable: false
            },

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush