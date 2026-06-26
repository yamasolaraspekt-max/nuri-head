<div class="table-responsive p-3">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th class="sortable" data-sort="created_at">Datum <i class="sort-icon feather icon-chevron-up"></i></th>
                <th class="sortable" data-sort="customer_lastname">Kunde <i class="sort-icon feather icon-chevron-up"></i></th>
                <th class="sortable" data-sort="city">Ort <i class="sort-icon feather icon-chevron-up"></i></th>
                <th>Produkt</th>
                <th>Mitarbeiter</th>
                <th>Status</th>
                <th>Phase</th>
                {{-- Action Column Removed --}}
            </tr>
        </thead>
        <tbody id="kanbanTableBody">
            @forelse($leads as $lead)
                @php
                    // -- Data Preparation --
                    $s = strtolower($lead->stage ?? 'lead');
                    $ws = strtolower($lead->work_status ?? 'playing');

                    // Status Badge Logic
                    $badgeState = match(true) {
                        in_array($s, ['lead', 'offer']) => ['Offen', 'warning', 'text-dark'],
                        in_array($s, ['deal', 'project', 'completed']) => ['Zusage', 'success', ''],
                        in_array($s, ['archive', 'archiv']) => ['Archiv', 'secondary', ''],
                        default => ['Absage', 'danger', '']
                    };
                    [$txt, $tone, $extra] = $badgeState;

                    // Play/Pause Colors
                    $playColor  = $ws === 'playing' ? 'text-success' : '';
                    $pauseColor = $ws === 'paused' ? 'text-warning' : '';
                    $stopColor  = $ws === 'stopped' ? 'text-danger' : '';

                    // Priority Icon
                    $prioVal = strtolower($lead->priority ?? 'normal');
                    $prioMeta = match($prioVal) {
                        'high', 'urgent' => ['label' => 'Hoch', 'cls' => 'prio-high', 'icon' => 'alert-triangle'],
                        'low' => ['label' => 'Niedrig', 'cls' => 'prio-low', 'icon' => 'arrow-down-circle'],
                        default => ['label' => 'Normal', 'cls' => 'prio-normal', 'icon' => 'circle']
                    };

                    // Interest Icon
                    $interestIcon = match($lead->interest ?? '') {
                        'interest' => 'kaufinteresse.svg',
                        'intent' => 'kaufabsicht.svg',
                        'option' => 'kaufoption.svg',
                        default => null
                    };
                    $interestLabel = match($lead->interest ?? '') {
                        'interest' => 'Kaufinteresse',
                        'intent' => 'Kaufabsicht',
                        'option' => 'Kaufoption',
                        default => ''
                    };
                    
                    // Services / Translated Phase
                    $servicesMap = [
                        'complete' => 'Komplett', 'montage' => 'Montage', 'product' => 'Produkt',
                        'plan' => 'Planung', 'maintenance' => 'Wartung', 'repair' => 'Reparatur',
                        'emergency' => 'Notdienst', 'others' => 'Sonstiges'
                    ];

                    $ageIndicatorHtml = '';
                    if ($s === 'lead' && $lead->created_at) {
                        $targetDate = \Carbon\Carbon::parse($lead->created_at);
                        $now = \Carbon\Carbon::now();
                        $diffHours = $now->diffInHours($targetDate);

                        $isToday = $targetDate->isSameDay($now);

                        if ($diffHours > 48) {
                            $colorClass = 'age-red';
                            $title = 'Überfällig (Älter als 48 Stunden)';
                        } elseif ($diffHours > 24 || $isToday) {
                            // If it's today OR between 24-48 hours
                            $colorClass = 'age-orange';
                            $title = 'Letzter Tag (Läuft in unter 24h ab)';
                        } else {
                            $colorClass = 'age-green';
                            $title = 'Neu (Unter 24 Stunden)';
                        }

                        $ageIndicatorHtml = '<span class="age-dot '.$colorClass.'" title="'.$title.'"></span>';
                    }

                    $translatedPhase = $servicesMap[$lead->phase_section_title ?? ''] ?? $servicesMap[$lead->service ?? ''] ?? null;
                @endphp

                <tr id="row-{{ $lead->lead_product_id }}"
                    class="list-row-item"
                    data-customer-id="{{ $lead->customer_id }}"
                    data-alternative-id="{{ $lead->alternative_id }}"
                    data-product-id="{{ $lead->product_id }}"
                    data-lead-product-id="{{ $lead->lead_product_id }}"
                    data-stage="{{ $s }}"
                    data-run-state="{{ $ws }}">

                    {{-- 1. Datum --}}
                  <td class="list-date-cell">
                        {!! $ageIndicatorHtml !!}
                        {{ $lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('d.m.Y') : '-' }}
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="tooltip-trigger position-relative">
                                <i class="feather icon-{{ $prioMeta['icon'] }} prio-dot {{ $prioMeta['cls'] }}"></i>
                                <span class="custom-tooltip">Priorität: {{ $prioMeta['label'] }}</span>
                            </span>
                        </div>
                    </td>

                    {{-- 2. Kunde (Link + Icons) --}}
                    <td>
                        <a href="{{ url('/new_lead_profile/' . $lead->customer_id) }}" class="customer-link">
                            {{ $lead->customer_lastname }} {{ $lead->customer_name }}
                        </a>

                        <div class="list-action-bar">
                            <button type="button" class="btn-list-icon play {{ $playColor }}" data-run="playing" title="Start">
                                <i class="feather icon-play"></i>
                            </button>
                            <button type="button" class="btn-list-icon pause {{ $pauseColor }}" data-run="paused" title="Pause">
                                <i class="feather icon-pause"></i>
                            </button>
                            <button type="button" class="btn-list-icon stop {{ $stopColor }}" data-run="stopped" title="Stopp">
                                <i class="feather icon-square"></i>
                            </button>

                            <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                            <button type="button" class="btn-list-icon note" 
                                    data-open-notes 
                                    data-customer="{{ $lead->customer_id }}" 
                                    data-alt="{{ $lead->alternative_id }}" 
                                    data-product="{{ $lead->product_id }}"
                                    title="Notizen">
                                <i class="feather icon-message-square"></i>
                            </button>

                            <a href="{{ url('/lead/process/history/'.$lead->customer_id.'/'.$lead->alternative_id.'/'.$lead->product_id) }}" 
                               class="btn-list-icon history" 
                               data-lh-history 
                               title="Verlauf">
                                <i class="feather icon-activity"></i>
                            </a>
                        </div>
                    </td>

                    {{-- 3. Ort --}}
                    <td><i class="feather icon-map-pin text-muted"></i> {{ $lead->city ?? '' }}</td>

                    {{-- 4. Produkt --}}
                    <td>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/icons/produkt.svg') }}" style="width:26px" class="mr-1" alt="">
                                <span>{{ $lead->initial ?? '' }}</span>
                            </div>
                            @if($lead->department_name)
                                <span class="tooltip-trigger position-relative">
                                    <img src="{{ asset('images/icons/abteilung.svg') }}" style="width:30px" alt="">
                                    <span class="custom-tooltip">{{ $lead->department_name }}</span>
                                </span>
                            @endif
                            @if($translatedPhase)
                                <span class="tooltip-trigger position-relative">
                                    <img src="{{ asset('images/icons/dienstleistung.svg') }}" style="width:33px" alt="">
                                    <span class="custom-tooltip">{{ $translatedPhase }}</span>
                                </span>
                            @endif
                            @if($interestIcon)
                                <span class="tooltip-trigger position-relative">
                                    <img src="{{ asset('images/icons/'.$interestIcon) }}" style="width:20px" alt="">
                                    <span class="custom-tooltip">{{ $interestLabel }}</span>
                                </span>
                            @endif
                        </div>
                    </td>
 
                    <td>
                        @php
                            $emp   = $lead->employee ?? null;
                            $field = $lead->field_employee ?? null;

                            // team relation: adjust name to your actual relation
                            // supports: $lead->team_members OR $lead->teams
                            $team = collect($lead->team_members ?? $lead->teams ?? []);
                        @endphp

                        @if(!$emp && !$field && $team->isEmpty())
                            <small>&ndash;</small>
                        @else
                            <div class="d-flex align-items-start flex-wrap" style="gap:10px;">

                                {{-- Main employees (stacked) --}}
                                <div class="d-flex flex-column" style="gap:6px;">
                                    @if($emp && ($emp->name || $emp->lastname))
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/employee/'.($emp->image ?? 'noimage.png')) }}"
                                                width="30" height="30" class="rounded-circle mr-1" alt=""
                                                style="object-fit:cover;">
                                            <div>
                                                <div style="line-height:1.1"><strong>{{ $emp->lastname }}</strong> {{ $emp->name }}</div>
                                                <small class="text-muted">Innendienst</small>
                                            </div>
                                        </div>
                                    @endif

                                    @if($field && ($field->name || $field->lastname))
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/employee/'.($field->image ?? 'noimage.png')) }}"
                                                width="26" height="26" class="rounded-circle mr-1" alt=""
                                                style="object-fit:cover;">
                                            <div>
                                                <div style="line-height:1.1"><strong>{{ $field->lastname }}</strong> {{ $field->name }}</div>
                                                <small class="text-muted">Außendienst</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Team avatars --}}
                                @if($team->isNotEmpty())
                                    <div class="d-flex align-items-center"
                                        style="margin-top:2px; padding-left:10px; border-left:1px solid #e0e0e0;">
                                        <ul class="list-unstyled users-list m-0 d-flex align-items-center" style="gap:0; padding:0;">
                                            @foreach($team as $t)
                                                @php
                                                    $tName = trim(($t->lastname ?? '').' '.($t->name ?? '')) ?: 'Team';
                                                    $tImg  = $t->image ? asset('images/employee/'.$t->image) : asset('images/employee/noimage.png');
                                                @endphp
                                                <li class="avatar pull-up"
                                                    title="{{ $tName }}"
                                                    style="margin-left:-8px;">
                                                    <img class="media-object rounded-circle"
                                                        src="{{ $tImg }}"
                                                        width="26" height="26"
                                                        alt="{{ $tName }}"
                                                        style="border:2px solid #fff; object-fit:cover;">
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                            </div>
                        @endif
                    </td>


                    {{-- 6. Status --}}
                    <td>
                        <div><span class="badge bg-{{ $tone }} {{ $extra }}">{{ $txt }}</span></div>
                        @if($lead->latest_phase || $lead->latest_activity || $lead->done_date)
                            <div class="small mt-1 text-muted">
                                @if($lead->latest_phase) <i class="feather icon-box"></i> {{ $lead->latest_phase }}<br> @endif
                                @if($lead->latest_activity) <i class="feather icon-check-circle"></i> {{ $lead->latest_activity }}<br> @endif
                                @if($lead->done_date || $lead->updated_at) 
                                    <i class="feather icon-clock"></i> {{ \Carbon\Carbon::parse($lead->done_date ?? $lead->updated_at)->format('d.m.Y H:i') }}
                                @endif
                            </div>
                        @endif
                    </td>

                    {{-- 7. Phase Select --}}
                    <td>
                        <select class="form-control stage-select" data-id="{{ $lead->lead_product_id }}">
                            @foreach($stageNames ?? ['lead'=>'Lead','offer'=>'Verkauf','deal'=>'Auftrag','project'=>'Montage','completed'=>'Abschluss','archive'=>'Archiv'] as $key => $label)
                                <option value="{{ $key }}" {{ $s == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                {{-- Live Feed Row (Hidden initially) --}}
                <tr class="list-feed-row"
                    data-customer-id="{{ $lead->customer_id }}"
                    data-alternative-id="{{ $lead->alternative_id }}"
                    data-product-id="{{ $lead->product_id }}"
                    data-lead-product-id="{{ $lead->lead_product_id }}">
                    <td colspan="7"> <div class="live-feed-bar list-live-feed card-live-feed" data-feed-root data-feed-count="0" style="display:none; margin-top:0.4rem;">
                            </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="text-center p-3 text-muted">
                        Keine Ergebnisse gefunden.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div id="listPagination" class="d-flex justify-content-center py-2">
        @if($leads instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $leads->appends(request()->query())->links() }}
        @endif
    </div>
</div>