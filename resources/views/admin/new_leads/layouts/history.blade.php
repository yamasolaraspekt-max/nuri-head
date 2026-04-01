<style>
    /* 1. Wrapper: Forces a specific height based on screen size so internal scrolling works */
    .history-wrapper {
        display: flex;
        flex-direction: column;
        /* Calculate height: 100vh - (approx height of top nav + customer nav + padding) */
        height: calc(100vh - 240px); 
        min-height: 400px;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden; /* Stop outer scroll */
        position: relative;
    }

    /* 2. Header: Fixed at the top */
    .history-header {
        flex: 0 0 auto; /* Do not shrink */
        background: #fff;
        padding: 15px;
        border-bottom: 1px solid #e3e6f0;
        z-index: 10;
    }

    /* 3. Body: The Scrollable Part */
    .history-body {
        flex: 1 1 auto; /* Take remaining space */
        overflow-y: auto; /* Enable vertical scrolling here */
        overflow-x: hidden;
        padding: 20px;
        background-color: #f8f9fa;
        
        /* Scrollbar Styling */
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f1f5f9;
    }

    .history-body::-webkit-scrollbar {
        width: 8px;
    }
    .history-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .history-body::-webkit-scrollbar-thumb {
        background-color: #cbd5e0;
        border-radius: 4px;
    }

    /* --- Timeline Design --- */
    .history-timeline {
        position: relative;
        list-style: none;
        padding: 0;
        margin: 0;
        max-width: 100%;
    }

    /* The Vertical Line */
    .history-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 24px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 60px;
    }

    .timeline-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        font-size: 18px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .timeline-icon i {
        /* Feather icons font-size */
        font-size: 20px;
    }

    .timeline-content {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        position: relative;
    }
    
    /* Arrow pointing left */
    .timeline-content::before {
        content: " ";
        position: absolute;
        top: 18px;
        right: 100%;
        margin-top: -8px;
        border-width: 8px;
        border-style: solid;
        border-color: transparent #e2e8f0 transparent transparent;
    }
    .timeline-content::after {
        content: " ";
        position: absolute;
        top: 18px;
        right: 100%;
        margin-top: -7px;
        border-width: 7px;
        border-style: solid;
        border-color: transparent #fff transparent transparent;
    }

    /* Change Tables */
    .changes-table { width: 100%; font-size: 13px; margin-top: 8px; }
    .changes-table td { padding: 4px 0; vertical-align: top; }
    .field-name { font-weight: 600; color: #64748b; width: 140px; }
    
    .old-value { text-decoration: line-through; color: #ef4444; margin-right: 6px; font-size: 0.9em; opacity: 0.8; }
    .new-value { color: #10b981; font-weight: 600; }
    .info-text { color: #334155; font-style: italic; }

    /* Time Gap Badge */
    .time-gap {
        display: inline-block;
        background: #e2e8f0;
        color: #64748b;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        left: 25px; /* align center with line */
        transform: translateX(-50%);
        z-index: 2;
    }
</style>
  

 <div class="history-wrapper" 
     data-current-cid="{{ $logs->first()->new_leads_id ?? $filters['customer_id'] ?? '' }}"
     data-current-aid="{{ $filters['alternative_id'] ?? '' }}"
     data-current-pid="{{ $filters['product_id'] ?? '' }}">
    
    <div class="history-header">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0"><i class="feather icon-search text-muted"></i></span>
                    </div>
                    {{-- ID is crucial for the JS to find this input --}}
                    <input type="text" 
                           id="historySearchText" 
                           class="form-control border-left-0" 
                           placeholder="Suche..." 
                           value="{{ $filters['search_text'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <input type="date" 
                       id="historySearchDate" 
                       class="form-control" 
                       value="{{ $filters['search_date'] ?? '' }}">
            </div>
            <div class="col-md-3 text-right">
                 <button class="btn btn-outline-secondary btn-block" id="btnResetHistory">
                    <i class="feather icon-refresh-cw"></i> Reset
                 </button>
            </div>
        </div>
    </div>

    <div class="history-body">
        @if($logs->isEmpty())
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                <i class="feather icon-inbox mb-3" style="font-size: 40px; opacity: 0.5;"></i>
                <h6>Keine Einträge gefunden</h6>
                <p class="small">Bitte ändern Sie Ihre Suchfilter.</p>
            </div>
        @else
            <ul class="history-timeline">
                @foreach($logs as $index => $log)
                    @php
                        // Time Gap Logic
                        $nextLog = $logs[$index + 1] ?? null;
                        $timeGap = null;
                        if ($nextLog) {
                            $curr = \Carbon\Carbon::parse($log->created_at);
                            $prev = \Carbon\Carbon::parse($nextLog->created_at);
                            if ($curr->diffInHours($prev) > 4) {
                                $timeGap = $prev->diffForHumans($curr, true, true) . ' später';
                            }
                        }
                        
                        $iconColor = match($log->event_type) {
                            'created' => '#10b981',
                            'deleted' => '#ef4444',
                            default   => '#f59e0b',
                        };
                        $iconClass = match($log->event_type) {
                            'created' => 'icon-plus',
                            'deleted' => 'icon-trash-2',
                            default   => 'icon-edit-2',
                        };
                    @endphp

                    <li class="timeline-item">
                        <div class="timeline-icon" style="border-color: {{ $iconColor }}; color: {{ $iconColor }};">
                            <i class="feather {{ $iconClass }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">{{ $log->user_name ?? 'System' }}</h6>
                                    <div class="small text-muted">
                                        {{ ucfirst($log->event_type) }} 
                                        @if(str_contains($log->model_type, 'NewLeads')) Lead 
                                        @elseif(str_contains($log->model_type, 'LeadProductList')) Produkt 
                                        @elseif(str_contains($log->model_type, 'LeadAlternativeAdd')) Objekt 
                                        @elseif(str_contains($log->model_type, 'CustomerNote')) Notiz 
                                        @else Datensatz @endif
                                    </div>
                                </div>
                                <div class="text-right small text-muted">
                                    <div><i class="feather icon-calendar mr-1"></i>{{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y') }}</div>
                                    <div><i class="feather icon-clock mr-1"></i>{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</div>
                                </div>
                            </div>

                            @if(!empty($log->changes))
                                <table class="changes-table">
                                    @foreach($log->changes as $field => $val)
                                        <tr>
                                            <td class="field-name">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                            <td>
                                                @if(is_array($val))
                                                    @if(isset($val['info']))
                                                        <span class="info-text">{{ $val['info'] }}</span>
                                                    @elseif(isset($val['from']) || isset($val['to']))
                                                        @if(isset($val['from']) && !is_array($val['from']))
                                                            <span class="old-value">{{ Str::limit($val['from'], 30) }}</span> 
                                                            <i class="feather icon-arrow-right text-muted mx-1" style="font-size: 10px;"></i>
                                                        @endif
                                                        <span class="new-value">{{ is_array($val['to'] ?? '') ? 'Array' : ($val['to'] ?? '') }}</span>
                                                    @else
                                                        <span class="text-muted small">Details in DB</span>
                                                    @endif
                                                @else
                                                    <span class="text-dark">{{ $val }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <span class="small text-muted font-italic">Aktion ausgeführt.</span>
                            @endif
                        </div>
                    </li>

                    @if($timeGap)
                        <div class="text-center"><span class="time-gap">{{ $timeGap }}</span></div>
                    @endif
                @endforeach
                
                <li class="timeline-item mb-0">
                    <div class="timeline-icon" style="background:#f1f5f9; border:none; width: 20px; height: 20px; left: 15px;"></div>
                    <div class="text-muted small pl-2 pt-1">Ende der Historie</div>
                </li>
            </ul>
        @endif
    </div>
</div>