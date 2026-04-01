@extends('admin.layouts.app')

@section('title') Formular Builder @endsection

@section('style')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/customer_product.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
    .field-group { border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .field-group label { font-weight: 600; }
    .tab-content > .tab-pane:not(.active) { display: none; }
    .json-output { background: #f8f9fa; padding: 10px; border: 1px solid #ccc; white-space: pre-wrap; }
    .error-output { color: #d9534f; margin-top: 10px; }
    #formPreview input, #formPreview select, #formPreview textarea { margin-bottom: 10px; }
    .multi-group-wrapper .d-flex input { flex: 1; }
</style>
@endsection

@section('content')
    {{-- CSRF meta handled by layout's <head> --}}

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title">Produkt Formulare</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Formular</li>
                    </ol>
                </div>
            </div>

            <div class="content-body">
                {{-- Hidden with the product_id --}}
                <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
                <input type="hidden" name="id" id="id" value="{{ $id }}">

                <div class="container mt-2">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="sectionName" class="form-control" placeholder="Section Name">
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-success" onclick="exportToJson()">Export JSON</button>
                            <button class="btn btn-primary" onclick="saveForm()">Save Form</button>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Field Label</label>
                        <input type="text" id="fieldLabel" class="form-control">

                        <label>Field Name</label>
                        <input type="text" id="fieldName" class="form-control">

                        <label>Field Type</label>
                        <select id="fieldType" class="form-control" onchange="toggleFieldTypeOptions()">
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

                        <div id="selectOptions" class="mt-2" style="display: none;">
                            <label>Options (comma separated)</label>
                            <input type="text" id="options" class="form-control">
                        </div>

                        <div id="formulaField" class="mt-2" style="display: none;">
                            <label>Formula</label>
                            <input type="text" id="formula" class="form-control" placeholder="e.g., price * quantity">
                        </div>

                        <div id="multiGroupFields" class="mt-2" style="display: none;">
                            <label>Multi-Group Subfields</label>
                            <input type="text" id="multiFields" class="form-control" placeholder="e.g. width,height,depth">
                        </div>

                        <label class="mt-2">Default Value</label>
                        <input type="text" id="defaultValue" class="form-control">

                        <label class="mt-2">Min</label>
                        <input type="number" id="minValue" class="form-control">

                        <label class="mt-2">Max</label>
                        <input type="number" id="maxValue" class="form-control">

                        <label class="mt-2">Regex Pattern</label>
                        <input type="text" id="pattern" class="form-control" placeholder="e.g. ^\\d{4}$">

                        <label class="mt-2">Advanced Condition (JS)</label>
                        <input type="text" id="advancedCondition" class="form-control" placeholder="e.g. salary > 2000">

                        <div class="form-check mt-2">
                            <input type="checkbox" id="required" class="form-check-input">
                            <label class="form-check-label" for="required">Required</label>
                        </div>

                        <button class="btn btn-primary mt-3" onclick="addFieldToSection()">Add Field</button>
                    </div>

                    <h4 class="mt-4">Live Form Preview</h4>
                    <ul class="nav nav-tabs" id="sectionTabs"></ul>
                    <div class="tab-content" id="tabContent"></div>

                    <h4 class="mt-4">JSON Output</h4>
                    <textarea id="jsonOutput" class="json-output form-control" readonly></textarea>
                    <div id="errorOutput" class="error-output"></div>

                    <div class="import-area">  
                        <h5>Import JSON</h5>  
                        <textarea id="importJson" class="form-control" rows="8" placeholder="Paste JSON here"></textarea>  
                        <button class="btn btn-secondary mt-2" onclick="importFromJson()">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // --------------------- GLOBAL VARIABLES ---------------------
    const fieldsBySection = {};

    // --------------------- MATH DSL FUNCTIONS ---------------------
    function toNum(val) {
        return val === '' || val == null || isNaN(val) ? 0 : Number(val);
    }
    function add(a, b) { return toNum(a) + toNum(b); }
    function sub(a, b) { return toNum(a) - toNum(b); }
    function mul(a, b) { return toNum(a) * toNum(b); }
    function div(a, b) { const d = toNum(b); return d === 0 ? 0 : toNum(a) / d; }
    function round(v, p = 0) { return Math.round(toNum(v) * 10 ** p) / 10 ** p; }
    function min(...args) { return Math.min(...args.map(toNum)); }
    function max(...args) { return Math.max(...args.map(toNum)); }

    function evaluateFormula(formula, values) {
        try {
            const fns = { add, sub, mul, div, round, min, max, toNum };
            const keys = Object.keys(values);
            const vals = keys.map(k => values[k] ?? 0);
            const fn = new Function(...Object.keys(fns), ...keys, `return ${formula}`);
            return fn(...Object.values(fns), ...vals);
        } catch (e) {
            console.warn('Formula error:', formula, e);
            return 'Error';
        }
    }

    // --------------------- FORM LOGIC ---------------------
    function collectFormData() {
        const formData = {};
        document.querySelectorAll('#tabContent input, #tabContent select, #tabContent textarea').forEach(input => {
            if (!input.name) return;
            formData[input.name] = input.type === 'checkbox' ? input.checked : input.value;
        });
        return formData;
    }

    function updateFormulas() {
        const values = collectFormData();
        document.querySelectorAll('#tabContent input[readonly]').forEach(input => {
            const field = Object.values(fieldsBySection).flat().find(f => f.name === input.name && f.type === 'formula');
            if (field?.formula) {
                input.value = evaluateFormula(field.formula, values);
            }
        });
    }

    function toggleFieldTypeOptions() {
        const type = document.getElementById('fieldType').value;
        document.getElementById('selectOptions').style.display = type === 'select' ? 'block' : 'none';
        document.getElementById('formulaField').style.display = type === 'formula' ? 'block' : 'none';
        document.getElementById('multiGroupFields').style.display = type === 'multi-group' ? 'block' : 'none';
    }

    // --------------------- RENDER & EDIT ---------------------
    function renderPreview() {
        const sectionTabs = document.getElementById('sectionTabs');
        const tabContent = document.getElementById('tabContent');
        sectionTabs.innerHTML = '';
        tabContent.innerHTML = '';
        const formData = collectFormData();

        Object.entries(fieldsBySection).forEach(([section, fields], index) => {
            const tabId = `tab-${section}`;
            sectionTabs.innerHTML += `<li class="nav-item">
                <button class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab" data-bs-target="#${tabId}">${section}</button>
            </li>`;

            const tabPane = document.createElement('div');
            tabPane.className = `tab-pane fade ${index === 0 ? 'show active' : ''}`;
            tabPane.id = tabId;

            const container = document.createElement('div');
            container.className = 'sortable-container';
            container.dataset.section = section;

            fields.forEach((field, i) => {
                if (field.advancedCondition) {
                    try {
                        const keys = Object.keys(formData);
                        const vals = Object.values(formData);
                        const fn = new Function(...keys, `return ${field.advancedCondition}`);
                        if (!fn(...vals)) return;
                    } catch (e) { return; }
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3 draggable-field';
                wrapper.dataset.index = i;

                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = field.label;
                wrapper.appendChild(label);

                const input = document.createElement('input');
                input.className = 'form-control';
                input.name = field.name;
                input.type = field.type === 'checkbox' ? 'checkbox' : (field.type || 'text');

                if (field.type === 'formula') {
                    input.readOnly = true;
                    input.value = evaluateFormula(field.formula, formData);
                } else if (field.type === 'checkbox') {
                    input.checked = formData[field.name] || false;
                } else {
                    input.value = formData[field.name] || field.defaultValue || '';
                }

                input.addEventListener('input', updateFormulas);
                wrapper.appendChild(input);
                container.appendChild(wrapper);
            });

            tabPane.appendChild(container);
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

        updateFormulas();
    }

    function editField(section, index) {
        const f = fieldsBySection[section][index];
        document.getElementById('sectionName').value = section;
        document.getElementById('fieldLabel').value = f.label;
        document.getElementById('fieldName').value = f.name;
        document.getElementById('fieldType').value = f.type;
        document.getElementById('defaultValue').value = f.defaultValue || '';
        document.getElementById('options').value = f.options || '';
        document.getElementById('formula').value = f.formula || '';
        document.getElementById('multiFields').value = f.subfields || '';
        document.getElementById('minValue').value = f.min || '';
        document.getElementById('maxValue').value = f.max || '';
        document.getElementById('pattern').value = f.pattern || '';
        document.getElementById('advancedCondition').value = f.advancedCondition || '';
        document.getElementById('required').checked = f.required || false;
        toggleFieldTypeOptions();
        fieldsBySection[section].splice(index, 1);
        renderPreview();
    }

    // --------------------- EXPORT / SAVE / ADD ---------------------
    function addFieldToSection() {
        const section = document.getElementById('sectionName').value.trim();
        if (!section) return alert('Enter a section name.');

        const field = {
            label: document.getElementById('fieldLabel').value,
            name: document.getElementById('fieldName').value,
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

        if (!fieldsBySection[section]) fieldsBySection[section] = [];
        fieldsBySection[section].push(field);
        renderPreview();
    }

    function exportToJson() {
        const updated = {};
        Object.entries(fieldsBySection).forEach(([section, fields]) => {
            updated[section] = fields.map(field => {
                const f = { ...field };
                const input = document.querySelector(`[name="${f.name}"]`);
                if (input) {
                    f.defaultValue = input.type === 'checkbox' ? input.checked : input.value;
                }
                return f;
            });
        });
        Object.assign(fieldsBySection, updated);
        document.getElementById('jsonOutput').textContent = JSON.stringify(fieldsBySection, null, 2);
        document.getElementById('errorOutput').innerHTML = '';
    }

    function saveForm() {
        const payload = {
            product_id: document.getElementById('product_id').value,
            id: document.getElementById('id').value,
            formulas: fieldsBySection
        };

        fetch("{{ route('product-formula.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (res.status === 422) return res.json().then(err => { throw err.errors; });
            if (!res.headers.get('content-type').includes('application/json')) {
                return res.text().then(text => { throw new Error('Expected JSON:\n' + text); });
            }
            return res.json();
        })
        .then(result => {
            if (result.success) {
                window.location.href = "{{ route('product.formula.index') }}";
            } else {
                document.getElementById('errorOutput').textContent = 'Error: ' + (result.message || 'Unknown error');
            }
        })
        .catch(err => {
            if (typeof err === 'object' && !Array.isArray(err)) {
                let html = '<ul>';
                Object.values(err).forEach(group => {
                    group.forEach(msg => html += `<li>${msg}</li>`);
                });
                html += '</ul>';
                document.getElementById('errorOutput').innerHTML = html;
            } else {
                document.getElementById('errorOutput').textContent = 'Network error:\n' + (err.message || err);
            }
        });
    }

    // --------------------- INIT ---------------------
    document.addEventListener('DOMContentLoaded', renderPreview);
</script>

@endsection