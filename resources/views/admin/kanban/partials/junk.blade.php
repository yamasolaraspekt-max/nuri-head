<style>
    .oc-wrap {
         font-family: Inter, system-ui, -apple-system, sans-serif;
        color: #1f2937;
        max-width: 1843px;
        margin: 3px auto;
        padding: 2px; 
    }

    .oc-header{margin-bottom:18px;margin-top:20px;}
    .oc-titlebar{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
    }
    .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
    .oc-sub{font-size:14px;color:#6b7280;margin-top:4px}

    .oc-breadcrumb{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
        margin-top:10px;
        font-size:13px;
        color:#6b7280;
    }
    .oc-breadcrumb a{color:#6b7280;text-decoration:none;font-weight:700;}
    .oc-breadcrumb a:hover{color:#111827;}
    .oc-breadcrumb span.current{color:#111827;font-weight:800;}

    .oc-analytics{
        display:grid;
        grid-template-columns:repeat(4, minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
    }
    @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
    @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

    .oc-stat{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:16px;
        box-shadow:0 1px 2px 0 rgb(0 0 0 / .05);
        display:flex;
        align-items:center;
        gap:12px;
        min-height:92px;
    }

    .oc-stat-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }
    .oc-stat-icon.total{background:#eff6ff;color:#74b2d4}
    .oc-stat-icon.unpublished{background:#fffbeb;color:#d97706}
    .oc-stat-icon.warning{background:#fff7ed;color:#f59e0b}
    .oc-stat-icon.type{background:#f3f4f6;color:#6b7280}

    .oc-stat-meta{min-width:0}
    .oc-stat-label{font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;}
    .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
    .oc-stat-sub{font-size:12px;color:#6b7280;margin-top:4px;}

    .oc-toolbar{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        display:flex;
        flex-wrap:wrap;
        gap:14px;
        align-items:flex-end;
        justify-content:space-between;
        margin-bottom:16px;
        box-shadow:0 1px 2px 0 rgb(0 0 0 / .05);
    }

    .oc-toolbar-left{
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
        flex:1;
    }

    .oc-filter-block{
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:170px;
        width:100%;
    }

    .oc-filter-label{
        font-size:11px;
        font-weight:800;
        color:#6b7280;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .oc-input{
        background:#f9fafb;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:10px 12px;
        font-size:14px;
        width:100%;
    }

    .oc-list-head{
        padding:16px 16px 10px 16px;
        color:#6b7280;
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .oc-list{
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:0 0 16px 0;
    }

    .oc-item{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        transition:all .2s ease-in-out;
        overflow:hidden;
        margin:0;
    }

    .oc-item:hover{
        border-color:#93c21c;
        box-shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    }

    .oc-item-row{
        padding:16px;
    }

    .oc-cell{min-width:0}
    .oc-cell-title{
        font-size:11px;
        font-weight:800;
        color:#6b7280;
        text-transform:uppercase;
        margin-bottom:4px;
        display:none;
    }

    .oc-main{display:flex;flex-direction:column;min-width:0}
    .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
    .oc-subt{font-size:13px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

    .oc-status-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
    }
    .oc-status-pill.orange{background:#fffbeb;color:#b45309;}

    .oc-actions{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
        flex-wrap:wrap;
    }

    .oc-btn-ic{
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid #e5e7eb;
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color:#6b7280;
        cursor:pointer;
        transition:all .2s ease-in-out;
        text-decoration:none;
    }

    .oc-btn-ic.success{color:#10b981;border-color:#c7f2df;background:#ecfdf5}
    .oc-btn-ic.danger{color:#ef4444;border-color:rgba(239,68,68,.18);background:#fef2f2}

    .oc-select{
        width:100%;
        padding:10px 12px;
        border-radius:8px;
        border:1px solid #e5e7eb;
        background:#fff;
        font-size:14px;
        outline:none;
        transition:all .2s ease-in-out;
    }

    .oc-empty{
        text-align:center;
        padding:60px;
        color:#6b7280;
        background:#fff;
        border:1px dashed #e5e7eb;
        border-radius:16px;
        margin:16px 0;
    }

    .oc-pagination{
        margin-top:18px;
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        box-shadow:0 1px 2px 0 rgb(0 0 0 / .05);
    }

    .oc-pagination .pagination{
        margin:0;
        display:flex;
        flex-wrap:wrap;
        gap:6px;
    }

    .oc-pagination .page-item .page-link{
        border-radius:10px !important;
        border:1px solid #e5e7eb;
        color:#1f2937;
        padding:8px 12px;
        line-height:1.1;
        box-shadow:none !important;
    }

    .oc-pagination .page-item.active .page-link{
        background:#93c21c;
        border-color:#93c21c;
        color:#fff;
    }

    .oc-pagination .page-item.disabled .page-link{
        color:#9ca3af;
        background:#f9fafb;
    }

    .junk-grid{
        display:grid;
        grid-template-columns:
            minmax(220px,1.2fr)
            minmax(220px,1.2fr)
            minmax(130px,.7fr)
            minmax(170px,.9fr)
            110px
            minmax(240px,1.3fr)
            minmax(250px,1.4fr);
        gap:14px;
        align-items:center;
    }

    .restore-select{
        min-width:210px;
        max-width:230px;
    }

    .junk-reason{
        white-space:normal !important;
        overflow:visible !important;
        text-overflow:unset !important;
        line-height:1.5;
    }

    @media (max-width: 1280px){
        .junk-grid{
            grid-template-columns:1fr !important;
        }

        .oc-list-head{
            display:none;
        }

        .oc-cell-title{
            display:block;
        }
    }
</style>
@php
    $junkCount = method_exists($junk, 'total') ? $junk->total() : count($junk);
@endphp

<div class="oc-wrap"> 

    <div class="oc-analytics">
        <div class="oc-stat">
            <div class="oc-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Gesamt</div>
                <div class="oc-stat-value">{{ $junkCount }}</div>
                <div class="oc-stat-sub">Junk-Einträge insgesamt</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon unpublished">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Status</div>
                <div class="oc-stat-value">{{ $junkCount }}</div>
                <div class="oc-stat-sub">Aktuell als Junk markiert</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon warning">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v4m0 4h.01"/>
                    <path d="M10.29 3.86l-7.5 13A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.71-3.14l-7.5-13a2 2 0 0 0-3.42 0z"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Prüfen</div>
                <div class="oc-stat-value">{{ $junkCount }}</div>
                <div class="oc-stat-sub">Wiederherstellbar oder löschbar</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon type">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <path d="M7 10l5 5 5-5"/>
                    <path d="M12 15V3"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Aktion</div>
                <div class="oc-stat-value">Restore</div>
                <div class="oc-stat-sub">Rückführung in andere Phasen</div>
            </div>
        </div>
    </div>

    <div class="oc-toolbar">
        <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
                <label class="oc-filter-label">Übersicht</label>
                <div class="oc-input" style="display:flex;align-items:center;background:#fff;padding-left:12px;">
                    Verworfenene Leads mit Grund, Produkt, Mitarbeiter und Wiederherstellung
                </div>
            </div>
        </div>
    </div>

    <div id="junkInner">
        <div class="oc-list-head junk-grid">
            <div>Kunde</div> 
            <div>Produkt</div>
            <div>Mitarbeiter</div>
            <div>Datum</div>
            <div>Grund</div>
            <div style="text-align:right;">Aktion</div>
        </div>

        <div class="oc-list">
            @forelse ($junk as $lead)
                @php
                    $reason = '-';
                    if (!empty($lead->stage_history)) {
                        $history = json_decode($lead->stage_history, true);
                        if (is_array($history)) {
                            $junkEntries = array_filter($history, function ($item) {
                                return isset($item['stage']) && $item['stage'] === 'junk';
                            });

                            if (!empty($junkEntries)) {
                                $lastJunk = end($junkEntries);
                                $reason = $lastJunk['description'] ?? '-';
                            }
                        }
                    }
                @endphp

                <div class="oc-item"
                     id="row-{{ $lead->lead_product_id }}"
                     data-customer-id="{{ $lead->customer_id }}"
                     data-alternative-id="{{ $lead->alternative_id }}"
                     data-product-id="{{ $lead->product_id }}">
                    <div class="oc-item-row junk-grid">
                        <div class="oc-cell">
                            <div class="oc-cell-title">Kunde</div>
                            <div class="oc-main">
                                <div class="oc-ttl">{{ $lead->customer_name }} {{ $lead->customer_lastname }}</div>
                                 <div class="oc-subt">
                                    {{ $lead->street ?: '—' }} {{ trim(($lead->postcode ?? '') . ' ' . ($lead->city ?? '')) ?: 'Keine Adresse' }}
                                  </div>
                            </div>
                        </div>
 
                        <div class="oc-cell">
                            <div class="oc-cell-title">Produkt</div>
                            <div class="oc-main">
                                <div class="oc-ttl" style="font-size:14px;">{{ $lead->initial ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Mitarbeiter</div>
                            <div class="oc-main">
                                <div class="oc-ttl" style="font-size:14px;">
                                    {{ $lead->employee_name }} {{ $lead->employee_lastname }}
                                </div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Datum</div>
                            <span class="oc-status-pill orange">
                                {{ \Carbon\Carbon::parse($lead->updated_at)->format('d.m.Y') }}
                            </span>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Grund</div>
                            <div class="oc-main">
                                <div class="oc-subt junk-reason">
                                    {{ $reason }}
                                </div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Aktion</div>
                            <div class="oc-actions">
                                <select class="oc-select restore-select">
                                    <option value="" disabled selected>Wiederherstellen nach…</option>
                                    <option value="lead">Lead (Qualifizierung &amp; Angebot)</option>
                                    <option value="offer">Angebot (Verkauf)</option>
                                    <option value="deal">Auftrag</option>
                                    <option value="project">Montage</option>
                                    <option value="completed">Abschluss</option>
                                    <option value="ticket">Ticket</option>
                                </select>

                                <button
                                    class="oc-btn-ic success btn-restore"
                                    data-source="junk"
                                    data-id="{{ $lead->lead_product_id }}"
                                    title="Wiederherstellen"
                                    type="button">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 12a9 9 0 1 0 3-6.708L3 8"></path>
                                        <path d="M3 3v5h5"></path>
                                    </svg>
                                </button>

                                <button
                                    class="oc-btn-ic danger btn-purge d-none"
                                    data-id="{{ $lead->lead_product_id }}"
                                    title="Endgültig löschen"
                                    type="button">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7"/>
                                        <path d="M10 11v6"/>
                                        <path d="M14 11v6"/>
                                        <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>
                                        <path d="M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="oc-empty">Keine Junk-Leads gefunden.</div>
            @endforelse
        </div>

        @if(method_exists($junk, 'links') && $junk->hasPages())
            <div class="oc-pagination">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                    <div style="font-size:12px;color:#6b7280;">
                        Zeige <strong>{{ $junk->firstItem() ?? 0 }}</strong>
                        bis <strong>{{ $junk->lastItem() ?? 0 }}</strong>
                        von <strong>{{ $junk->total() }}</strong> Einträgen
                    </div>
                    <div>
                        {{ $junk->withQueryString()->onEachSide(1)->fragment('junk')->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>