@extends('admin.layouts.app')

@section('title', 'Formular testen')

@section('style')
<style>
    .form-preview label { font-weight: bold; }
    .form-preview input, .form-preview select, .form-preview textarea { margin-bottom: 15px; width: 100%; }
    .is-invalid { border-color: red; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">FORMULARE</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('product-formula') }}">Liste</a></li>
                        <li class="breadcrumb-item active">Teste</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <h2>Testansicht – {{ $formula->section_name }}</h2>
            <form id="testForm" class="form-preview border p-3 mt-3 bg-white shadow">
                @foreach (json_decode($formula->fields, true) as $field)
                    <div class="mb-3">
                        <label>{{ $field['label'] }} {!! isset($field['required']) && $field['required'] ? '<span style="color:red">*</span>' : '' !!}</label>

                        @php
                            $value = $field['defaultValue'] ?? '';
                            $required = isset($field['required']) && $field['required'];
                            $attributes = [
                                'name' => $field['name'],
                                'class' => 'form-control',
                                'data-min' => $field['min'] ?? '',
                                'data-max' => $field['max'] ?? '',
                                'data-pattern' => $field['pattern'] ?? '',
                                'required' => $required ? true : null,
                                'value' => $value,
                            ];
                        @endphp

                        @if (in_array($field['type'], ['text', 'number', 'date']))
                            <input type="{{ $field['type'] }}" {!! collect($attributes)->map(fn($v, $k) => $v !== '' && $v !== null ? "$k=\"$v\"" : '')->implode(' ') !!}>

                        @elseif ($field['type'] === 'textarea')
                            <textarea {!! collect($attributes)->except('value')->map(fn($v, $k) => $v !== '' ? "$k=\"$v\"" : '')->implode(' ') !!}>{{ $value }}</textarea>

                        @elseif ($field['type'] === 'select')
                            <select {!! collect($attributes)->except('value')->map(fn($v, $k) => $v !== '' ? "$k=\"$v\"" : '')->implode(' ') !!}>
                                @foreach (explode(',', $field['options'] ?? '') as $opt)
                                    <option value="{{ trim($opt) }}" {{ trim($opt) == $value ? 'selected' : '' }}>{{ trim($opt) }}</option>
                                @endforeach
                            </select>

                        @elseif ($field['type'] === 'checkbox')
                            <input type="checkbox" name="{{ $field['name'] }}" {{ $value ? 'checked' : '' }}>

                        @elseif ($field['type'] === 'file')
                            <input type="file" name="{{ $field['name'] }}">

                        @elseif ($field['type'] === 'multi-group')
                            @foreach (explode(',', $field['subfields'] ?? '') as $sub)
                                <input type="text" class="form-control mb-1" placeholder="{{ trim($sub) }}" name="{{ $field['name'] }}[]">
                            @endforeach

                        @elseif ($field['type'] === 'formula')
                            <input type="text" class="form-control formula-field"
                                   name="{{ $field['name'] }}"
                                   data-formula="{{ $field['formula'] ?? '' }}"
                                   readonly>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-success">Test Absenden</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
            const fnKeys = Object.keys(fns);
            const fnVals = Object.values(fns);
            const valKeys = Object.keys(values);
            const valVals = valKeys.map(k => values[k] ?? 0);

            const fn = new Function(...fnKeys, ...valKeys, `return ${formula}`);
            return fn(...fnVals, ...valVals);
        } catch (e) {
            console.warn('Formula error:', formula, e);
            return 'Fehler';
        }
    }

    function collectFormValues() {
        const values = {};
        document.querySelectorAll('#testForm input, #testForm select, #testForm textarea').forEach(input => {
            if (!input.name || input.classList.contains('formula-field') || input.type === 'submit') return;

            values[input.name] = input.type === 'checkbox'
                ? (input.checked ? 1 : 0)
                : (input.value !== '' && !isNaN(input.value) ? parseFloat(input.value) : 0);
        });
        return values;
    }

    function evaluateFormulas() {
        const values = collectFormValues();

        document.querySelectorAll('.formula-field').forEach(field => {
            const formula = field.dataset.formula;
            const result = evaluateFormula(formula, values);
            field.value = isNaN(result) ? 'Fehler' : result;
        });
    }

    function validateForm() {
        let valid = true;
        document.querySelectorAll('#testForm input, #testForm select, #testForm textarea').forEach(input => {
            const val = input.value;
            const min = input.dataset.min;
            const max = input.dataset.max;
            const pattern = input.dataset.pattern;

            input.classList.remove('is-invalid');

            if (input.required && !val) {
                input.classList.add('is-invalid');
                valid = false;
            }
            if (min && parseFloat(val) < parseFloat(min)) {
                input.classList.add('is-invalid');
                valid = false;
            }
            if (max && parseFloat(val) > parseFloat(max)) {
                input.classList.add('is-invalid');
                valid = false;
            }
            if (pattern && !(new RegExp(pattern).test(val))) {
                input.classList.add('is-invalid');
                valid = false;
            }
        });
        return valid;
    }

    document.querySelector('#testForm').addEventListener('input', evaluateFormulas);
    document.querySelector('#testForm').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) {
            Swal.fire('Fehler', 'Bitte überprüfen Sie Ihre Eingaben.', 'error');
            return;
        }

        const formData = new FormData(this);

        fetch('{{ route('product.formula.test.submit') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Erfolg', 'Formular erfolgreich getestet!', 'success');
                } else {
                    Swal.fire('Fehler', data.message || 'Serverfehler.', 'error');
                }
            })
            .catch(() => Swal.fire('Fehler', 'Serverfehler beim Senden.', 'error'));
    });
</script>

@endsection
