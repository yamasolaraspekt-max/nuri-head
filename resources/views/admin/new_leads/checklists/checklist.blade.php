@php
    $fields = json_decode($formula->fields, true);
    $totalFields = count($fields);
    $filledCount = 0;
    foreach ($fields as $f) {
        $val = $filled_values[$f['name']] ?? null;
        if ($val !== null && $val !== '') $filledCount++;
    }
    $progress = $totalFields ? round(($filledCount / $totalFields) * 100) : 0;
@endphp

<div id="accordion-{{ $formula->id }}" class="card mb-2 accordion-section">
    <div class="card-header px-3 py-1 d-flex justify-content-between align-items-center" style="background: #8fc73e;">
        <button class="btn btn-link  " type="button"
                data-toggle="collapse"
                data-target="#collapse-{{ $formula->id }}"
                aria-expanded="false"
                aria-controls="collapse-{{ $formula->id }}" style=" color: white; font-weight: bold; font-size: 18px;">
            {{ $formula->section_name }}
        </button>
        <div class="d-flex flex-column align-items-end w-25">
            <div class="progress w-100" style="height: 15px;">
                <div class="progress-bar bg-primary"
                     role="progressbar"
                     style="width: {{ $progress }}%; background-color:#df004f !important;"
                     aria-valuenow="{{ $progress }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <!-- <small class="text-muted mt-1">{{ $progress }}%</small> -->
        </div>
    </div>

    <div id="collapse-{{ $formula->id }}"
         class="collapse"
         aria-labelledby="heading-{{ $formula->id }}"
         data-parent="#accordion-{{ $formula->id }}">
        <div class="card-body bg-white">
            @foreach ($fields as $field)
                @php
                    $value = $filled_values[$field['name']] ?? ($field['defaultValue'] ?? '');
                    $required = !empty($field['required']);
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

                <div class="mb-3">
                    <label>
                        {{ $field['label'] }} {!! $required ? '<span style="color:red">*</span>' : '' !!}
                    </label>

                    @if (in_array($field['type'], ['text', 'number', 'date']))
                        <input type="{{ $field['type'] }}"
                               {!! collect($attributes)->map(fn($v, $k) => $v !== '' && $v !== null ? "$k=\"$v\"" : '')->implode(' ') !!}>
                    @elseif ($field['type'] === 'textarea')
                        <textarea {!! collect($attributes)->except('value')->map(fn($v, $k) => $v !== '' ? "$k=\"$v\"" : '')->implode(' ') !!}>{{ $value }}</textarea>
                    @elseif ($field['type'] === 'select')
                        <select {!! collect($attributes)->except('value')->map(fn($v, $k) => $v !== '' ? "$k=\"$v\"" : '')->implode(' ') !!}>
                            @foreach (explode(',', $field['options'] ?? '') as $opt)
                                <option value="{{ trim($opt) }}" {{ trim($opt) == $value ? 'selected' : '' }}>{{ trim($opt) }}</option>
                            @endforeach
                        </select>
                    @elseif ($field['type'] === 'checkbox')
                        <input type="checkbox" name="{{ $field['name'] }}" {{ $value == 1 ? 'checked' : '' }}>
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
                               value="{{ $value }}"
                               readonly>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
