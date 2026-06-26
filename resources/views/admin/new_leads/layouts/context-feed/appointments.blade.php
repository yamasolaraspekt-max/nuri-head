@php
    $totalAppointments = $appointments instanceof \Illuminate\Support\Collection
        ? $appointments->flatten(1)->count()
        : 0;

    function ma_context_value($model, array $fields, $fallback = '—')
    {
        foreach ($fields as $field) {
            if (isset($model->{$field}) && $model->{$field} !== '') {
                return $model->{$field};
            }
        }
        return $fallback;
    }
@endphp

@if($totalAppointments < 1)
    <div class="ma-feed-empty">
        <strong>Keine Termine gefunden.</strong>
        <div class="mt-1 small">
            Für diesen Kunden wurden keine Einträge in <code>main_appointments</code> gefunden.
        </div>
    </div>
@else
    @foreach($appointments as $dateKey => $items)
        <div class="ma-feed-date-title mb-2 mt-2">
            <strong>
                @if($dateKey === 'ohne-datum')
                    Ohne Datum
                @else
                    {{ \Illuminate\Support\Carbon::parse($dateKey)->format('d.m.Y') }}
                @endif
            </strong>
            <small class="text-muted">{{ $items->count() }} Termin(e)</small>
        </div>

        @foreach($items as $appointment)
            @php
                $products = collect($appointment->context_products ?? []);
                $reports = collect($appointment->reports ?? []);
                $comments = collect($appointment->comments ?? []);
                $employees = collect($appointment->employees ?? []);
            @endphp

            <div class="ma-feed-card" data-feed-card data-appointment-id="{{ $appointment->id }}">
                <button type="button" class="ma-feed-head" data-feed-collapse>
                    <span class="ma-note-type-icon bg-blue">
                        <i data-feather="calendar"></i>
                    </span>

                    <span class="flex-grow-1 min-w-0">
                        <span class="ma-feed-title d-block">
                            {{ $appointment->context_title ?? ('Termin #' . $appointment->id) }}
                        </span>

                        <span class="ma-feed-meta d-block">
                            {{ $appointment->context_date_label ?? 'Ohne Datum' }}
                            · {{ $appointment->context_time_label ?? 'Ohne Uhrzeit' }}
                            · ID #{{ $appointment->id }}
                        </span>

                        <span class="ma-feed-preview d-block">
                            {{ ma_context_value($appointment, ['city', 'place', 'location', 'address'], 'Keine Adresse/Ort hinterlegt') }}
                        </span>
                    </span>
                </button>

                <div class="ma-feed-body">
                    <div class="ma-feed-mini-row">
                        <span><i data-feather="clock"></i> Zeit</span>
                        <small>{{ $appointment->context_date_label ?? 'Ohne Datum' }} ·
                            {{ $appointment->context_time_label ?? 'Ohne Uhrzeit' }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="map-pin"></i> Ort</span>
                        <small>{{ ma_context_value($appointment, ['city', 'place', 'location', 'address'], '—') }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="user"></i> Kunde / Objekt</span>
                        <small>
                            Kunde: {{ $appointment->customer_id ?? $ctx['customerId'] }}
                            @if($appointment->other_id ?? null)
                                · Objekt: {{ $appointment->other_id }}
                            @elseif($ctx['alternativeId'])
                                · Objekt: {{ $ctx['alternativeId'] }}
                            @endif
                        </small>
                    </div>

                    @if(!empty($appointment->description) || !empty($appointment->note) || !empty($appointment->comment))
                        <div class="ma-feed-comment mt-2">
                            <strong>Beschreibung</strong>
                            <div class="mt-1">
                                {!! nl2br(e(ma_context_value($appointment, ['description', 'note', 'comment'], ''))) !!}
                            </div>
                        </div>
                    @endif

                 

                    @if($employees->isNotEmpty())
                        <div class="mt-2">
                            <strong class="d-block mb-1">Mitarbeiter</strong>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($employees as $employee)
                                    <span class="badge badge-pill">
                                        {{ $employee->name ?? $employee->first_name ?? ('#' . ($employee->id ?? '')) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($reports->isNotEmpty())
                        <div class="mt-2">
                            <strong class="d-block mb-1">Berichte</strong>
                            @foreach($reports as $report)
                                <div class="ma-feed-comment">
                                    <div class="d-flex justify-content-between gap-2 flex-wrap">
                                        <strong>{{ $report->type ?? 'Bericht' }}</strong>
                                        <small>{{ optional($report->report_date)->format('d.m.Y H:i') ?: optional($report->created_at)->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <div class="mt-1">{!! nl2br(e($report->report ?? '')) !!}</div>
                                    @if(!empty($report->next_step))
                                        <div class="mt-1 small"><strong>Nächster Schritt:</strong> {{ $report->next_step }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($comments->isNotEmpty())
                        <div class="mt-2">
                            <strong class="d-block mb-1">Kommentare</strong>
                            @foreach($comments as $comment)
                                <div class="ma-feed-comment">
                                    <div class="small text-muted mb-1">
                                        {{ optional($comment->created_at)->format('d.m.Y H:i') }}
                                    </div>
                                    {!! nl2br(e($comment->comment ?? $comment->description ?? '')) !!}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endforeach
@endif