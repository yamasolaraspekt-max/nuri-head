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
                        <li class="breadcrumb-item">Formular</li>
                        <li class="breadcrumb-item active">Bearbeiten</li>
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
                            <input type="text" id="sectionName" class="form-control" placeholder="Section Name" value="{{$formulas->section_name}}">
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
// Dynamic Form Builder with Tabs, Conditions, Formula, and Drag & Drop Support (Edit Page)
const fieldsBySection = {};

function toNum(val) {
    return val === '' || val == null || isNaN(val) ? 0 : Number(val);
}
function add(a, b) { return toNum(a) + toNum(b); }
function sub(a, b) { return toNum(a) - toNum(b); }
function mul(a, b) { return toNum(a) * toNum(b); }
function div(a, b) {
    const denominator = toNum(b);
    return denominator === 0 ? 0 : toNum(a) / denominator;
}
function round(value, precision = 0) {
    const factor = Math.pow(10, precision);
    return Math.round(toNum(value) * factor) / factor;
}
function min(...args) { return Math.min(...args.map(toNum)); }
function max(...args) { return Math.max(...args.map(toNum)); }

function evaluateFormula(formula, values) {
    try {
        const mathFns = { add, sub, mul, div, round, min, max, toNum };
        const keys = Object.keys(values);
        const vals = keys.map(k => values[k] ?? 0);
        const fn = new Function(...Object.keys(mathFns), ...keys, `return ${formula}`);
        return fn(...Object.values(mathFns), ...vals);
    } catch (e) {
        console.warn('Formula error:', formula, e);
        return 'Error';
    }
}

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
        const formulaField = Object.values(fieldsBySection).flat().find(f => f.name === input.name && f.type === 'formula');
        if (formulaField && formulaField.formula) {
            input.value = evaluateFormula(formulaField.formula, values);
        }
    });
}

function editField(section, index) {
    const field = fieldsBySection[section][index];
    document.getElementById('sectionName').value = section;
    document.getElementById('fieldLabel').value = field.label;
    document.getElementById('fieldName').value = field.name;
    document.getElementById('fieldType').value = field.type;
    document.getElementById('defaultValue').value = field.defaultValue || '';
    document.getElementById('options').value = field.options || '';
    document.getElementById('formula').value = field.formula || '';
    document.getElementById('multiFields').value = field.subfields || '';
    document.getElementById('minValue').value = field.min || '';
    document.getElementById('maxValue').value = field.max || '';
    document.getElementById('pattern').value = field.pattern || '';
    document.getElementById('advancedCondition').value = field.advancedCondition || '';
    document.getElementById('required').checked = field.required || false;
    toggleFieldTypeOptions();

    fieldsBySection[section].splice(index, 1);
    renderPreview();
}

function renderPreview() {
    const sectionTabs = document.getElementById('sectionTabs');
    const tabContent = document.getElementById('tabContent');
    sectionTabs.innerHTML = '';
    tabContent.innerHTML = '';
    const formData = collectFormData();

    Object.entries(fieldsBySection).forEach(([section, fields], index) => {
        const tabId = `tab-${section}`;
        sectionTabs.innerHTML += `<li class="nav-item"><button class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab" data-bs-target="#${tabId}">${section}</button></li>`;

        const tabPane = document.createElement('div');
        tabPane.className = `tab-pane fade ${index === 0 ? 'show active' : ''}`;
        tabPane.id = tabId;

        const container = document.createElement('div');
        container.className = 'sortable-container';
        container.dataset.section = section;

        fields.forEach((field, i) => {
            try {
                if (field.advancedCondition) {
                    const fn = new Function(...Object.keys(formData), `return ${field.advancedCondition}`);
                    if (!fn(...Object.values(formData))) return;
                }
            } catch {}

            const wrapper = document.createElement('div');
            wrapper.className = 'mb-3 draggable-field';
            wrapper.dataset.index = i;
            wrapper.innerHTML = `<label class="form-label">${field.label}</label>
                <button type="button" class="btn btn-sm btn-warning float-end" onclick="editField('${section}', ${i})">Edit</button>`;

            let input;

            switch (field.type) {
                case 'select':
                    input = document.createElement('select');
                    input.className = 'form-control';
                    input.name = field.name;
                    (field.options || '').split(',').forEach(opt => {
                        const option = document.createElement('option');
                        option.value = option.textContent = opt.trim();
                        input.appendChild(option);
                    });
                    break;
                case 'textarea':
                    input = document.createElement('textarea');
                    input.className = 'form-control';
                    input.name = field.name;
                    break;
                case 'checkbox':
                    input = document.createElement('input');
                    input.type = 'checkbox';
                    input.className = 'form-check-input';
                    input.name = field.name;
                    input.checked = !!formData[field.name];
                    break;
                case 'formula':
                    input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.name = field.name;
                    input.readOnly = true;
                    input.value = evaluateFormula(field.formula, formData);
                    break;
                default:
                    input = document.createElement('input');
                    input.type = field.type || 'text';
                    input.className = 'form-control';
                    input.name = field.name;
                    input.value = formData[field.name] || '';
            }

            if (field.defaultValue && !input.value) input.value = field.defaultValue;
            if (field.pattern) input.pattern = field.pattern;
            if (field.required) input.required = true;
            if (field.type !== 'formula') input.addEventListener('input', updateFormulas);

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
                const section = evt.from.dataset.section;
                const moved = fieldsBySection[section].splice(evt.oldIndex, 1)[0];
                fieldsBySection[section].splice(evt.newIndex, 0, moved);
                renderPreview();
            }
        });
    });

    updateFormulas();
}

function toggleFieldTypeOptions() {
    const type = document.getElementById('fieldType').value;
    document.getElementById('selectOptions').style.display = (type === 'select') ? 'block' : 'none';
    document.getElementById('formulaField').style.display = (type === 'formula') ? 'block' : 'none';
    document.getElementById('multiGroupFields').style.display = (type === 'multi-group') ? 'block' : 'none';
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

function saveForm() {
    const payload = {
        product_id: document.getElementById('product_id').value,
        id: document.getElementById('id').value,
        formulas: fieldsBySection
    };

    fetch("{{ route('product.formula.update') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.status === 422 ? res.json().then(d => { throw d.errors }) : res.json())
    .then(result => {
        if (result.success) {
            window.location.href = "{{ route('product.formula.index') }}";
        } else {
            document.getElementById('errorOutput').textContent = 'Error: ' + (result.message || 'Unknown error');
        }
    })
    .catch(err => {
        const out = document.getElementById('errorOutput');
        if (typeof err === 'object' && !Array.isArray(err)) {
            out.innerHTML = '<ul>' + Object.values(err).flat().map(m => `<li>${m}</li>`).join('') + '</ul>';
        } else {
            out.textContent = 'Network or JSON error:\n' + (err.message || err);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @if ($formulas && $formulas->fields)
        try {
            const existing = @json(json_decode($formulas->fields));
            const section = "{{ $formulas->section_name }}";
            fieldsBySection[section] = existing;
        } catch (e) {
            console.error('Failed to parse existing fields', e);
        }
    @endif

    if (window.initialFieldsBySection) {
        Object.assign(fieldsBySection, window.initialFieldsBySection);
    }
    renderPreview();
});
</script>

@endsection