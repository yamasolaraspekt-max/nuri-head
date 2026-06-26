@php
    $fields = json_decode($formula->fields, true) ?: [];
    $totalFields = count($fields);
    $filledCount = 0;

    foreach ($fields as $f) {
        $fieldName = $f['name'] ?? null;

        if (!$fieldName) {
            continue;
        }

        $val = $filled_values[$fieldName] ?? null;

        if (is_array($val)) {
            $hasValue = collect($val)->filter(fn ($item) => $item !== null && $item !== '')->isNotEmpty();
        } else {
            $hasValue = $val !== null && $val !== '';
        }

        if ($hasValue) {
            $filledCount++;
        }
    }

    $progress = $totalFields ? round(($filledCount / $totalFields) * 100) : 0;
@endphp

@once
<style>
:root{
    --ff-card:#fff;
    --ff-text:#111827;
    --ff-muted:#6b7280;
    --ff-border:#e5e7eb;
    --ff-primary:#93c21c;
    --ff-primary-hover:#7baa18;
    --ff-primary-soft:#f4fae7;
    --ff-danger:#ef4444;
    --ff-danger-soft:#fef2f2;
    --ff-info:#74b2d4;
    --ff-info-soft:#eef7fc;
    --ff-shadow:0 8px 24px -18px rgba(0,0,0,.35);
}

.ff-accordion{
    background:var(--ff-card);
    border:1px solid var(--ff-border);
    border-radius:18px;
    box-shadow:var(--ff-shadow);
    overflow:hidden;
    margin-bottom:14px;
}

.ff-head{
    width:100%;
    border:0;
    background:#fff;
    padding:14px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    cursor:pointer;
    text-align:left;
}

.ff-head:hover{
    background:#fafafa;
}

.ff-title-wrap{
    display:flex;
    align-items:center;
    gap:12px;
    min-width:0;
}

.ff-toggle{
    width:36px;
    height:36px;
    border-radius:10px;
    border:1px solid #d8edaa;
    background:var(--ff-primary-soft);
    color:var(--ff-primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    flex:0 0 auto;
}

.ff-title{
    font-size:15px;
    font-weight:900;
    color:var(--ff-text);
    line-height:1.2;
    word-break:break-word;
}

.ff-sub{
    margin-top:4px;
    color:var(--ff-muted);
    font-size:12px;
    font-weight:700;
}

.ff-progress-wrap{
    width:240px;
    max-width:34%;
    flex:0 0 auto;
}

.ff-progress-meta{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:8px;
    color:var(--ff-muted);
    font-size:11px;
    font-weight:900;
    margin-bottom:6px;
}

.ff-progress{
    height:10px;
    width:100%;
    background:#f3f4f6;
    border:1px solid var(--ff-border);
    border-radius:999px;
    overflow:hidden;
}

.ff-progress-bar{
    height:100%;
    width:0;
    background:linear-gradient(90deg, var(--ff-primary), var(--ff-info));
    border-radius:999px;
    transition:width .25s ease;
}

.ff-body{
    border-top:1px solid var(--ff-border);
    background:#fafafa;
    padding:16px;
}

.ff-field-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
}

.ff-field{
    background:#fff;
    border:1px solid var(--ff-border);
    border-radius:16px;
    padding:14px;
}

.ff-label-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
    margin-bottom:9px;
}

.ff-label{
    margin:0;
    color:#374151;
    font-size:13px;
    font-weight:900;
}

.ff-required{
    color:var(--ff-danger);
    font-weight:900;
}

.ff-type{
    display:inline-flex;
    align-items:center;
    border-radius:999px;
    padding:4px 9px;
    background:var(--ff-primary-soft);
    color:var(--ff-primary);
    border:1px solid #d8edaa;
    font-size:10px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.04em;
    white-space:nowrap;
}

.ff-input,
.ff-select,
.ff-textarea{
    width:100%;
    border:1px solid var(--ff-border);
    background:#f9fafb;
    border-radius:10px;
    padding:10px 12px;
    outline:none;
    font-size:14px;
    min-height:42px;
}

.ff-textarea{
    min-height:105px;
    resize:vertical;
}

.ff-input:focus,
.ff-select:focus,
.ff-textarea:focus{
    border-color:var(--ff-primary);
    background:#fff;
    box-shadow:0 0 0 3px var(--ff-primary-soft);
}

.ff-input[readonly]{
    background:var(--ff-info-soft);
    color:#0f172a;
    font-weight:900;
}

.ff-checkbox{
    display:inline-flex;
    align-items:center;
    gap:10px;
    background:#f9fafb;
    border:1px solid var(--ff-border);
    border-radius:12px;
    padding:10px 12px;
    font-weight:800;
    color:#374151;
}

.ff-checkbox input{
    width:18px;
    height:18px;
}

.ff-multi{
    display:grid;
    gap:10px;
}

.ff-empty{
    background:#fff;
    border:1px dashed var(--ff-border);
    border-radius:16px;
    padding:24px;
    text-align:center;
    color:var(--ff-muted);
    font-weight:800;
}

@media(max-width:900px){
    .ff-field-grid{
        grid-template-columns:1fr;
    }

    .ff-head{
        align-items:flex-start;
        flex-direction:column;
    }

    .ff-progress-wrap{
        width:100%;
        max-width:100%;
    }
}
</style>
@endonce

<div id="accordion-{{ $formula->id }}" class="ff-accordion accordion-section">
    <button
        type="button"
        class="ff-head"
        data-toggle="collapse"
        data-target="#collapse-{{ $formula->id }}"
        aria-expanded="false"
        aria-controls="collapse-{{ $formula->id }}"
    >
        <div class="ff-title-wrap">
            <span class="ff-toggle">↓</span>

            <div>
                <div class="ff-title">{{ $formula->section_name }}</div>
                <div class="ff-sub">
                    {{ $filledCount }} von {{ $totalFields }} Feld{{ $totalFields === 1 ? '' : 'ern' }} ausgefüllt
                </div>
            </div>
        </div>

        <div class="ff-progress-wrap">
            <div class="ff-progress-meta">
                <span>Fortschritt</span>
                <span>{{ $progress }}%</span>
            </div>

            <div class="ff-progress">
                <div
                    class="ff-progress-bar"
                    role="progressbar"
                    style="width: {{ $progress }}%;"
                    aria-valuenow="{{ $progress }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
        </div>
    </button>

    <div
        id="collapse-{{ $formula->id }}"
        class="collapse"
        aria-labelledby="heading-{{ $formula->id }}"
        data-parent="#accordion-{{ $formula->id }}"
    >
        <div class="ff-body">
            @if(empty($fields))
                <div class="ff-empty">
                    Keine Felder für dieses Formular vorhanden.
                </div>
            @else
                <div class="ff-field-grid">
                    @foreach($fields as $field)
                        @php
                            $type = $field['type'] ?? 'text';
                            $name = $field['name'] ?? '';
                            $label = $field['label'] ?? $name;
                            $value = $filled_values[$name] ?? ($field['defaultValue'] ?? '');
                            $required = !empty($field['required']);

                            $attributes = [
                                'name' => $name,
                                'data-min' => $field['min'] ?? '',
                                'data-max' => $field['max'] ?? '',
                                'data-pattern' => $field['pattern'] ?? '',
                                'required' => $required ? true : null,
                                'value' => $value,
                            ];

                            $attributeString = collect($attributes)
                                ->map(fn($v, $k) => $v !== '' && $v !== null ? $k.'="'.e($v).'"' : '')
                                ->implode(' ');

                            $attributeStringWithoutValue = collect($attributes)
                                ->except('value')
                                ->map(fn($v, $k) => $v !== '' && $v !== null ? $k.'="'.e($v).'"' : '')
                                ->implode(' ');
                        @endphp

                        <div class="ff-field">
                            <div class="ff-label-row">
                                <label class="ff-label">
                                    {{ $label }}
                                    @if($required)
                                        <span class="ff-required">*</span>
                                    @endif
                                </label>

                                <span class="ff-type">{{ $type }}</span>
                            </div>

                            @if(in_array($type, ['text', 'number', 'date']))
                                <input
                                    type="{{ $type }}"
                                    class="ff-input"
                                    {!! $attributeString !!}
                                >

                            @elseif($type === 'textarea')
                                <textarea class="ff-textarea" {!! $attributeStringWithoutValue !!}>{{ $value }}</textarea>

                            @elseif($type === 'select')
                                <select class="ff-select" {!! $attributeStringWithoutValue !!}>
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
                                <label class="ff-checkbox">
                                    <input
                                        type="checkbox"
                                        name="{{ $name }}"
                                        {{ $required ? 'required' : '' }}
                                        {{ $value == 1 || $value === true || $value === 'on' ? 'checked' : '' }}
                                    >
                                    Aktiv
                                </label>

                            @elseif($type === 'file')
                                <input
                                    type="file"
                                    class="ff-input"
                                    name="{{ $name }}"
                                    {{ $required ? 'required' : '' }}
                                >

                            @elseif($type === 'multi-group')
                                <div class="ff-multi">
                                    @foreach(explode(',', $field['subfields'] ?? '') as $sub)
                                        @php $subName = trim($sub); @endphp

                                        @if($subName !== '')
                                            <input
                                                type="text"
                                                class="ff-input"
                                                placeholder="{{ $subName }}"
                                                name="{{ $name }}[]"
                                            >
                                        @endif
                                    @endforeach
                                </div>

                            @elseif($type === 'formula')
                                <input
                                    type="text"
                                    class="ff-input formula-field"
                                    name="{{ $name }}"
                                    data-formula="{{ $field['formula'] ?? '' }}"
                                    value="{{ $value }}"
                                    readonly
                                >

                            @else
                                <input
                                    type="text"
                                    class="ff-input"
                                    {!! $attributeString !!}
                                >
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>