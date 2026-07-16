@extends('admin.layouts.app')

@section('title', 'Formular testen')

@section('style')
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

    .ft-wrap{ 
        color:var(--text);
        font-family:Inter, system-ui, -apple-system, sans-serif;
    }

    .ft-head{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:20px;
    }

    .ft-title{
        font-size:26px;
        font-weight:900;
        letter-spacing:-.03em;
    }

    .ft-sub{
        color:var(--muted);
        font-size:14px;
        margin-top:4px;
    }

    .ft-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        align-items:center;
    }

    .ft-btn{
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

    .ft-btn:hover{
        background:var(--primary-hover);
        color:#fff;
        text-decoration:none;
    }

    .ft-btn-soft{
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

    .ft-btn-soft:hover{
        background:#f9fafb;
        color:var(--text);
        text-decoration:none;
    }

    .ft-grid{
        display:grid;
        grid-template-columns:minmax(0,1fr) 320px;
        gap:18px;
        align-items:start;
    }

    .ft-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:18px;
        overflow:hidden;
        box-shadow:var(--shadow);
    }

    .ft-card-head{
        padding:16px 18px;
        background:#fafafa;
        border-bottom:1px solid var(--border);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }

    .ft-card-title{
        font-size:16px;
        font-weight:900;
        margin:0;
    }

    .ft-card-sub{
        color:var(--muted);
        font-size:12px;
        margin-top:3px;
    }

    .ft-card-body{
        padding:18px;
    }

    .ft-form{
        display:grid;
        gap:14px;
    }

    .ft-field{
        border:1px solid var(--border);
        background:#fff;
        border-radius:16px;
        padding:15px;
        box-shadow:0 6px 18px -18px rgba(0,0,0,.45);
    }

    .ft-label-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom:9px;
    }

    .ft-label{
        font-size:13px;
        color:#374151;
        font-weight:900;
        margin:0;
    }

    .ft-required{
        color:var(--danger);
        font-weight:900;
    }

    .ft-badge{
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
        white-space:nowrap;
    }

    .ft-badge.info{
        background:var(--info-soft);
        color:var(--info);
        border-color:#c0d8ea;
    }

    .ft-badge.warning{
        background:var(--warning-soft);
        color:var(--warning);
        border-color:#fde68a;
    }

    .ft-input,
    .ft-select,
    .ft-textarea{
        width:100%;
        border:1px solid var(--border);
        background:#f9fafb;
        border-radius:10px;
        padding:10px 12px;
        outline:none;
        font-size:14px;
        min-height:42px;
    }

    .ft-textarea{
        min-height:110px;
        resize:vertical;
    }

    .ft-input:focus,
    .ft-select:focus,
    .ft-textarea:focus{
        border-color:var(--primary);
        background:#fff;
        box-shadow:0 0 0 3px var(--primary-soft);
    }

    .ft-input[readonly]{
        background:var(--info-soft);
        color:#0f172a;
        font-weight:900;
    }

    .ft-checkbox{
        display:inline-flex;
        align-items:center;
        gap:10px;
        background:#f9fafb;
        border:1px solid var(--border);
        border-radius:12px;
        padding:10px 12px;
        font-weight:800;
        color:#374151;
    }

    .ft-checkbox input{
        width:18px;
        height:18px;
    }

    .ft-multi-group{
        display:grid;
        gap:10px;
    }

    .ft-invalid,
    .is-invalid{
        border-color:var(--danger) !important;
        background:var(--danger-soft) !important;
    }

    .ft-error-text{
        display:none;
        color:var(--danger);
        font-size:12px;
        font-weight:800;
        margin-top:7px;
    }

    .ft-field.has-error .ft-error-text{
        display:block;
    }

    .ft-side{
        position:sticky;
        top:92px;
        display:grid;
        gap:18px;
    }

    .ft-stat{
        display:grid;
        gap:12px;
    }

    .ft-stat-item{
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        padding:14px;
    }

    .ft-stat-label{
        color:var(--muted);
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.07em;
        font-weight:900;
    }

    .ft-stat-value{
        font-size:22px;
        font-weight:900;
        margin-top:4px;
    }

    .ft-help{
        color:var(--muted);
        font-size:13px;
        line-height:1.6;
    }

    .ft-empty{
        padding:50px;
        text-align:center;
        color:var(--muted);
    }

    @media(max-width:1000px){
        .ft-grid{
            grid-template-columns:1fr;
        }

        .ft-side{
            position:relative;
            top:auto;
        }
    }

    @media(max-width:700px){
        .ft-wrap{
            margin-top:90px;
            padding:0 16px 32px;
        }

        .ft-head{
            align-items:stretch;
        }

        .ft-actions,
        .ft-actions .ft-btn,
        .ft-actions .ft-btn-soft{
            width:100%;
        }

        .ft-card-head{
            align-items:flex-start;
        }

        .ft-label-row{
            flex-direction:column;
            align-items:flex-start;
        }
    }
    </style>
@endsection

@php
$decodedFields = json_decode($formula->fields, true) ?: [];
$fieldCount = count($decodedFields);
$requiredCount = collect($decodedFields)->filter(fn($field) => !empty($field['required']))->count();
$formulaCount = collect($decodedFields)->filter(fn($field) => ($field['type'] ?? '') === 'formula')->count();
@endphp

@section('content')
    <div class="ft-wrap">

        <div class="ft-head">
            <div>
                <div class="ft-title">FORMULAR TESTEN</div>
                <div class="ft-sub">Testansicht – {{ $formula->section_name }}</div>
            </div>

            <div class="ft-actions">
                <a href="{{ url('product-formula') }}" class="ft-btn-soft">← Zurück zur Liste</a>
                <button type="submit" form="testForm" class="ft-btn">Test Absenden</button>
            </div>
        </div>

        <div class="ft-grid">

            <div class="ft-card">
                <div class="ft-card-head">
                    <div>
                        <h3 class="ft-card-title">{{ $formula->section_name }}</h3>
                        <div class="ft-card-sub">Felder ausfüllen, Formeln prüfen und Test absenden.</div>
                    </div>

                    <span class="ft-badge info">{{ $fieldCount }} Feld{{ $fieldCount === 1 ? '' : 'er' }}</span>
                </div>

                <div class="ft-card-body">
                    @if(empty($decodedFields))
                        <div class="ft-empty">
                            Für dieses Formular sind noch keine Felder vorhanden.
                        </div>
                    @else
                        <form id="testForm" class="ft-form">
                            @foreach($decodedFields as $field)
                                @php
        $type = $field['type'] ?? 'text';
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? $name;
        $value = $field['defaultValue'] ?? '';
        $required = !empty($field['required']);

        $baseAttributes = [
            'name' => $name,
            'data-min' => $field['min'] ?? '',
            'data-max' => $field['max'] ?? '',
            'data-pattern' => $field['pattern'] ?? '',
            'required' => $required ? true : null,
        ];

        $attributeString = collect($baseAttributes)
            ->map(fn($v, $k) => $v !== '' && $v !== null ? $k . '="' . e($v) . '"' : '')
            ->implode(' ');
                                @endphp

                                <div class="ft-field" data-field-wrapper>
                                    <div class="ft-label-row">
                                        <label class="ft-label">
                                            {{ $label }}
                                            @if($required)
                                                <span class="ft-required">*</span>
                                            @endif
                                        </label>

                                        <span class="ft-badge {{ $type === 'formula' ? 'warning' : '' }}">
                                            {{ $type }}
                                        </span>
                                    </div>

                                    @if(in_array($type, ['text', 'number', 'date']))
                                        <input
                                            type="{{ $type }}"
                                            class="ft-input"
                                            value="{{ $value }}"
                                            {!! $attributeString !!}
                                        >

                                    @elseif($type === 'textarea')
                                        <textarea class="ft-textarea" {!! $attributeString !!}>{{ $value }}</textarea>

                                    @elseif($type === 'select')
                                        <select class="ft-select" {!! $attributeString !!}>
                                            <option value="">Bitte wählen</option>
                                            @foreach(explode(',', $field['options'] ?? '') as $opt)
                                                @php $option = trim($opt); @endphp
                                                @if($option !== '')
                                                    <option value="{{ $option }}" {{ $option == $value ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>

                                    @elseif($type === 'checkbox')
                                        <label class="ft-checkbox">
                                            <input
                                                type="checkbox"
                                                name="{{ $name }}"
                                                data-min="{{ $field['min'] ?? '' }}"
                                                data-max="{{ $field['max'] ?? '' }}"
                                                data-pattern="{{ $field['pattern'] ?? '' }}"
                                                {{ $required ? 'required' : '' }}
                                                {{ $value ? 'checked' : '' }}
                                            >
                                            Aktiv
                                        </label>

                                    @elseif($type === 'file')
                                        <input
                                            type="file"
                                            class="ft-input"
                                            name="{{ $name }}"
                                            {{ $required ? 'required' : '' }}
                                        >

                                    @elseif($type === 'multi-group')
                                        <div class="ft-multi-group">
                                            @foreach(explode(',', $field['subfields'] ?? '') as $sub)
                                                @php $subName = trim($sub); @endphp
                                                @if($subName !== '')
                                                    <input
                                                        type="text"
                                                        class="ft-input"
                                                        placeholder="{{ $subName }}"
                                                        name="{{ $name }}[]"
                                                        {{ $required ? 'required' : '' }}
                                                    >
                                                @endif
                                            @endforeach
                                        </div>

                                    @elseif($type === 'formula')
                                        <input
                                            type="text"
                                            class="ft-input formula-field"
                                            name="{{ $name }}"
                                            data-formula="{{ $field['formula'] ?? '' }}"
                                            readonly
                                        >

                                    @else
                                        <input
                                            type="text"
                                            class="ft-input"
                                            value="{{ $value }}"
                                            {!! $attributeString !!}
                                        >
                                    @endif

                                    <div class="ft-error-text">
                                        Bitte prüfen Sie dieses Feld.
                                    </div>
                                </div>
                            @endforeach

                            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                <button type="submit" class="ft-btn">Test Absenden</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="ft-side">
                <div class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <h3 class="ft-card-title">Übersicht</h3>
                            <div class="ft-card-sub">Schnelle Kontrolle des Formulars.</div>
                        </div>
                    </div>

                    <div class="ft-card-body">
                        <div class="ft-stat">
                            <div class="ft-stat-item">
                                <div class="ft-stat-label">Felder</div>
                                <div class="ft-stat-value">{{ $fieldCount }}</div>
                            </div>

                            <div class="ft-stat-item">
                                <div class="ft-stat-label">Pflichtfelder</div>
                                <div class="ft-stat-value">{{ $requiredCount }}</div>
                            </div>

                            <div class="ft-stat-item">
                                <div class="ft-stat-label">Formeln</div>
                                <div class="ft-stat-value">{{ $formulaCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <h3 class="ft-card-title">Hinweis</h3>
                            <div class="ft-card-sub">Formel-Test</div>
                        </div>
                    </div>

                    <div class="ft-card-body">
                        <div class="ft-help">
                            Zahlenfelder werden automatisch für Formeln verwendet. Verfügbare Funktionen:
                            <strong>add</strong>, <strong>sub</strong>, <strong>mul</strong>,
                            <strong>div</strong>, <strong>round</strong>, <strong>min</strong>,
                            <strong>max</strong>, <strong>toNum</strong>.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/form-safe-eval.js') }}"></script>{{-- FS-07: sicherer Evaluator statt new Function --}}

    <script>
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

    function collectFormValues() {
        const values = {};

        document.querySelectorAll('#testForm input, #testForm select, #testForm textarea').forEach(input => {
            if (!input.name || input.classList.contains('formula-field') || input.type === 'submit' || input.type === 'file') {
                return;
            }

            if (input.name.endsWith('[]')) {
                const cleanName = input.name.replace('[]', '');

                if (!Array.isArray(values[cleanName])) {
                    values[cleanName] = [];
                }

                values[cleanName].push(input.value);
                return;
            }

            values[input.name] = input.type === 'checkbox'
                ? (input.checked ? 1 : 0)
                : (input.value !== '' && !isNaN(input.value) ? parseFloat(input.value) : input.value);
        });

        return values;
    }

    function evaluateFormulas() {
        const values = collectFormValues();

        document.querySelectorAll('.formula-field').forEach(field => {
            const formula = field.dataset.formula || '';
            const result = evaluateFormula(formula, values);

            field.value = isNaN(result) && result !== 0 ? 'Fehler' : result;
        });
    }

    function setFieldError(input, hasError) {
        const wrapper = input.closest('[data-field-wrapper]');

        input.classList.toggle('is-invalid', hasError);

        if (wrapper) {
            wrapper.classList.toggle('has-error', hasError);
        }
    }

    function validateForm() {
        let valid = true;

        document.querySelectorAll('#testForm input, #testForm select, #testForm textarea').forEach(input => {
            if (input.type === 'file') {
                setFieldError(input, false);
                return;
            }

            const val = input.type === 'checkbox' ? (input.checked ? '1' : '') : input.value;
            const min = input.dataset.min;
            const max = input.dataset.max;
            const pattern = input.dataset.pattern;

            let hasError = false;

            if (input.required && !val) {
                hasError = true;
            }

            if (min !== '' && min != null && val !== '' && !isNaN(val) && parseFloat(val) < parseFloat(min)) {
                hasError = true;
            }

            if (max !== '' && max != null && val !== '' && !isNaN(val) && parseFloat(val) > parseFloat(max)) {
                hasError = true;
            }

            if (pattern && val && !(new RegExp(pattern).test(val))) {
                hasError = true;
            }

            setFieldError(input, hasError);

            if (hasError) {
                valid = false;
            }
        });

        return valid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const testForm = document.querySelector('#testForm');

        if (!testForm) return;

        evaluateFormulas();

        testForm.addEventListener('input', function() {
            evaluateFormulas();
        });

        testForm.addEventListener('change', function() {
            evaluateFormulas();
        });

        testForm.addEventListener('submit', function(e) {
            e.preventDefault();

            evaluateFormulas();

            if (!validateForm()) {
                Swal.fire('Fehler', 'Bitte überprüfen Sie Ihre Eingaben.', 'error');
                return;
            }

            const submitButtons = document.querySelectorAll('button[form="testForm"], #testForm button[type="submit"]');

            submitButtons.forEach(btn => {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = 'Wird gesendet...';
            });

            const formData = new FormData(this);

            fetch('{{ route('product.formula.test.submit') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                let data = {};

                try {
                    data = await response.json();
                } catch(e) {}

                if (!response.ok) {
                    throw new Error(data.message || 'Serverfehler.');
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Erfolg', 'Formular erfolgreich getestet!', 'success');
                    return;
                }

                Swal.fire('Fehler', data.message || 'Serverfehler.', 'error');
            })
            .catch(error => {
                Swal.fire('Fehler', error.message || 'Serverfehler beim Senden.', 'error');
            })
            .finally(() => {
                submitButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = btn.dataset.originalText || 'Test Absenden';
                });
            });
        });
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
                label: 'Testen',
                url: "{{ url()->current() }}",
                clickable: false
            },

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush