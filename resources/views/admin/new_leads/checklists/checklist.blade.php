@php
    /*
     * F2 — v1/v2-robuste Normalisierung (präsentationsnah, keine Fachlogik).
     * Erzeugt $rows: eine render-fertige Feldliste, egal ob v1 (name + CSV-Options)
     * oder v2 (key + {value,label}-Array). Robust gegen fehlendes $filled_values
     * (2. Aufrufer ProductFormulaController::show gibt keins mit).
     */
    $filledValues = is_array($filled_values ?? null) ? $filled_values : [];

    $asArray = function ($raw) {
        if (is_array($raw)) return $raw;
        if (is_string($raw) && $raw !== '') { $d = json_decode($raw, true); return is_array($d) ? $d : []; }
        return [];
    };

    $normOptions = function ($raw) {
        $out = [];
        if (is_array($raw)) {
            foreach ($raw as $o) {
                if (is_array($o) && array_key_exists('value', $o)) {
                    $out[] = ['value' => (string) $o['value'], 'label' => (string) ($o['label'] ?? $o['value'])];
                } elseif (is_string($o) && trim($o) !== '') {
                    $out[] = ['value' => trim($o), 'label' => trim($o)];
                }
            }
        } elseif (is_string($raw) && $raw !== '') {
            foreach (explode(',', $raw) as $o) {
                $o = trim($o);
                if ($o !== '') $out[] = ['value' => $o, 'label' => $o];
            }
        }
        return $out;
    };

    $rows = [];
    foreach ($asArray($formula->fields ?? null) as $f) {
        $f = (array) $f;
        $identity = $f['key'] ?? $f['name'] ?? null; // v2: key · v1: name
        if (!$identity) continue;
        $rows[] = [
            'id'       => $identity,
            'label'    => $f['label'] ?? $identity,
            'type'     => strtolower((string) ($f['type'] ?? 'text')),
            'required' => !empty($f['required']),
            'options'  => $normOptions($f['options'] ?? null),
            'unit'     => (string) ($f['unit'] ?? ''),
            'min'      => $f['min'] ?? '',
            'max'      => $f['max'] ?? '',
            'pattern'   => (string) ($f['pattern'] ?? ''),   // v1-Legacy (Validierungs-Hook)
            'subfields' => (string) ($f['subfields'] ?? ''), // v1-Legacy (multi-group)
            'formula'   => (string) ($f['formula'] ?? ''),   // v1-Legacy (formula, Live-Calc-Hook)
            'value'    => $filledValues[$identity] ?? ($f['defaultValue'] ?? ''),
        ];
    }

    $totalFields = count($rows);
    $filledCount = 0;
    foreach ($rows as $r) {
        $v = $r['value'];
        $has = is_array($v)
            ? collect($v)->filter(fn ($i) => $i !== null && $i !== '')->isNotEmpty()
            : ($v !== null && $v !== '');
        if ($has) $filledCount++;
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
            @if(empty($rows))
                <div class="ff-empty">
                    Keine Felder für dieses Formular vorhanden.
                </div>
            @else
                <div class="ff-field-grid">
                    @foreach($rows as $r)
                        @php
                            $id = $r['id'];
                            $type = $r['type'];
                            $value = $r['value'];
                            $required = $r['required'];
                            // Render-Bucket → HTML-Input-Typ (v2-Zahlen-/Geometrietypen → number).
                            $htmlType = in_array($type, ['number','integer','decimal','area','length','volume','power'], true) ? 'number'
                                : ($type === 'email' ? 'email' : ($type === 'date' ? 'date' : 'text'));
                            $step = $type === 'integer' ? '1'
                                : (in_array($type, ['decimal','area','length','volume','power'], true) ? 'any' : null);
                        @endphp

                        <div class="ff-field">
                            <div class="ff-label-row">
                                <label class="ff-label">
                                    {{ $r['label'] }}
                                    @if($required)<span class="ff-required">*</span>@endif
                                </label>
                                <span class="ff-type">{{ $type }}{{ $r['unit'] !== '' ? ' · '.$r['unit'] : '' }}</span>
                            </div>

                            @if(in_array($type, ['text','email','plz','date','number','integer','decimal','area','length','volume','power'], true))
                                <input
                                    type="{{ $htmlType }}"
                                    class="ff-input"
                                    name="{{ $id }}"
                                    @if($r['min'] !== '') min="{{ $r['min'] }}" data-min="{{ $r['min'] }}" @endif
                                    @if($r['max'] !== '') max="{{ $r['max'] }}" data-max="{{ $r['max'] }}" @endif
                                    @if($r['pattern'] !== '') data-pattern="{{ $r['pattern'] }}" @endif
                                    @if($step) step="{{ $step }}" @endif
                                    @if($required) required @endif
                                    value="{{ is_array($value) ? '' : $value }}"
                                >

                            @elseif($type === 'textarea')
                                <textarea class="ff-textarea" name="{{ $id }}" @if($required) required @endif>{{ is_array($value) ? '' : $value }}</textarea>

                            @elseif($type === 'select')
                                <select class="ff-select" name="{{ $id }}" @if($required) required @endif>
                                    <option value="">Bitte wählen</option>
                                    @foreach($r['options'] as $opt)
                                        <option value="{{ $opt['value'] }}" {{ (string) $value === (string) $opt['value'] ? 'selected' : '' }}>
                                            {{ $opt['label'] }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($type === 'multiselect')
                                @php $selected = is_array($value) ? array_map('strval', $value) : ($value !== '' ? [(string) $value] : []); @endphp
                                <select class="ff-select" name="{{ $id }}[]" multiple @if($required) required @endif>
                                    @foreach($r['options'] as $opt)
                                        <option value="{{ $opt['value'] }}" {{ in_array((string) $opt['value'], $selected, true) ? 'selected' : '' }}>
                                            {{ $opt['label'] }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif(in_array($type, ['checkbox','boolean','consent'], true))
                                <label class="ff-checkbox">
                                    <input
                                        type="checkbox"
                                        name="{{ $id }}"
                                        value="1"
                                        {{ ($value == 1 || $value === true || $value === 'on' || $value === '1') ? 'checked' : '' }}
                                        @if($required) required @endif
                                    >
                                    {{ $type === 'consent' ? 'Zustimmung' : 'Aktiv' }}
                                </label>

                            @elseif(in_array($type, ['file','image'], true))
                                <input type="file" class="ff-input" name="{{ $id }}" @if($type === 'image') accept="image/*" @endif @if($required) required @endif>

                            @elseif($type === 'formula')
                                {{-- v1-Legacy: Live-Calc-Hooks (formula-field/data-formula) erhalten --}}
                                <input type="text" class="ff-input formula-field" name="{{ $id }}" data-formula="{{ $r['formula'] }}" value="{{ is_array($value) ? '' : $value }}" readonly>

                            @elseif($type === 'calculation')
                                {{-- v2: nur Anzeige, keine Berechnung (Scope) --}}
                                <input type="text" class="ff-input" name="{{ $id }}" value="{{ is_array($value) ? '' : $value }}" readonly>

                            @elseif($type === 'multi-group')
                                {{-- v1-Legacy --}}
                                <div class="ff-multi">
                                    @foreach(array_filter(array_map('trim', explode(',', $r['subfields']))) as $subName)
                                        <input type="text" class="ff-input" placeholder="{{ $subName }}" name="{{ $id }}[]">
                                    @endforeach
                                </div>

                            @else
                                <input type="text" class="ff-input" name="{{ $id }}" value="{{ is_array($value) ? '' : $value }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>