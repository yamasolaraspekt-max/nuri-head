{{-- resources/views/admin/new_leads/layouts/invoice.blade.php --}}
@php
    $productInvoices = collect($product_invoices ?? []);
    $generalInvoices = collect($general_invoices ?? []);

    $productCount = $productInvoices->count();
    $generalCount = $generalInvoices->count();

    $statusMap = [
        'draft' => 'Entwurf',
        'sent' => 'Gesendet',
        'paid' => 'Bezahlt',
        'overdue' => 'Überfällig',
        'cancelled' => 'Storniert',
    ];

    $statusIconMap = [
        'draft' => 'fa-pen',
        'sent' => 'fa-paper-plane',
        'paid' => 'fa-circle-check',
        'overdue' => 'fa-triangle-exclamation',
        'cancelled' => 'fa-ban',
    ];

    $tabUid = 'nlInvTabs_' . ($customer->id ?? 'x') . '_' . (int) $alternative_id . '_' . (int) $product_id;
    $prodId = $tabUid . '_prod';
    $genId = $tabUid . '_gen';

    $uploadTpl = route('admin.invoices.files.upload', ['invoice' => '__ID__']);
    $deleteTpl = route('admin.invoices.files.delete', ['file' => '__ID__']);
    $downloadTpl = route('admin.invoices.files.download', ['file' => '__ID__']);
    $viewTpl = route('admin.invoices.files.view', ['file' => '__ID__']);

    $customerName = trim(
        (string) ($customer->firma ?? '') . ' ' .
        (string) ($customer->lastname ?? '') . ' ' .
        (string) ($customer->name ?? '')
    );

    if ($customerName === '') {
        $customerName = 'Kunde #' . ($customer->id ?? '');
    }

    $statusStyle = function ($status) {
        $s = strtolower((string) ($status ?? 'draft'));

        return match ($s) {
            'paid' => 'background:rgba(22,163,74,.12);color:#166534;border-color:rgba(22,163,74,.22);',
            'sent' => 'background:rgba(2,132,199,.10);color:#075985;border-color:rgba(2,132,199,.20);',
            'overdue' => 'background:rgba(220,38,38,.10);color:#991b1b;border-color:rgba(220,38,38,.20);',
            'cancelled' => 'background:rgba(100,116,139,.12);color:#334155;border-color:rgba(100,116,139,.20);',
            default => 'background:rgba(116,178,212,.16);color:#0b4e68;border-color:rgba(116,178,212,.24);',
        };
    };

    $fileNameOf = function ($file) {
        return $file->original_name ?: ($file->stored_name ?: 'datei.pdf');
    };

    $formatDate = function ($date) {
        if (!$date) return '—';

        try {
            return \Illuminate\Support\Carbon::parse($date)->format('d.m.Y');
        } catch (\Throwable $e) {
            return '—';
        }
    };

    $formatMoney = function ($amount) {
        return number_format((float) ($amount ?? 0), 2, ',', '.') . ' €';
    };

    $sumTotal = function ($rows) {
        return collect($rows)->sum(fn($inv) => (float) ($inv->total_amount ?? 0));
    };

    $sumPaid = function ($rows) {
        return collect($rows)->where('status', 'paid')->sum(fn($inv) => (float) ($inv->total_amount ?? 0));
    };

    $sumOpen = function ($rows) {
        return collect($rows)
            ->reject(fn($inv) => in_array(strtolower((string) ($inv->status ?? 'draft')), ['paid', 'cancelled', 'draft'], true))
            ->sum(fn($inv) => (float) ($inv->total_amount ?? 0));
    };

    $allInvoices = $productInvoices->concat($generalInvoices);
@endphp

<div class="nl-invoice-panel"
     id="nlInvoicePanel_{{ $tabUid }}"
     data-upload-url="{{ $uploadTpl }}"
     data-delete-file-url="{{ $deleteTpl }}"
     data-download-file-url="{{ $downloadTpl }}"
     data-view-file-url="{{ $viewTpl }}">

    <style>
        .nl-invoice-panel{
            --nl-primary:var(--sa-accent);
            --nl-primary-dark:#5fa2c6;
            --nl-green:#93c21c;
            --nl-green-dark:#84b119;
            --nl-border:#e2e8f0;
            --nl-text:#0f172a;
            --nl-muted:#64748b;
            --nl-soft:#f8fafc;
            --nl-danger:#dc2626;
            --nl-radius:18px;
            padding:18px;
            color:var(--nl-text);
            font-family:inherit;
        }

        .nl-invoice-panel *,
        .nl-invoice-panel *::before,
        .nl-invoice-panel *::after{
            box-sizing:border-box;
        }

        .nl-inv-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:14px;
        }

        .nl-inv-title{
            font-size:20px;
            font-weight:900;
            color:var(--nl-text);
            line-height:1.2;
        }

        .nl-inv-sub{
            margin-top:5px;
            color:var(--nl-muted);
            font-weight:700;
            font-size:13px;
            line-height:1.6;
        }

        .nl-inv-sub span{
            color:var(--nl-text);
            font-weight:900;
        }

        .nl-inv-actions{
            display:flex;
            gap:8px;
            align-items:center;
            flex-wrap:wrap;
        }

        .nl-inv-summary{
            display:grid;
            grid-template-columns:repeat(4,minmax(160px,1fr));
            gap:10px;
            margin-bottom:14px;
        }

        .nl-inv-summary-card{
            border:1px solid rgba(226,232,240,.95);
            background:#fff;
            border-radius:16px;
            padding:13px 14px;
            box-shadow:0 8px 24px rgba(2,6,23,.045);
        }

        .nl-inv-summary-label{
            color:var(--nl-muted);
            font-size:12px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .nl-inv-summary-value{
            margin-top:6px;
            font-size:18px;
            color:var(--nl-text);
            font-weight:900;
            line-height:1.2;
        }

        .nl-inv-tabs input{
            display:none;
        }

        .nl-inv-tabbar{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            margin-top:8px;
        }

        .nl-inv-tabbtn{
            border:1px solid var(--nl-border);
            background:#fff;
            border-radius:12px;
            padding:10px 12px;
            font-weight:900;
            color:var(--nl-text);
            display:inline-flex;
            gap:10px;
            align-items:center;
            cursor:pointer;
            user-select:none;
            transition:all .15s ease;
        }

        .nl-inv-tabbtn:hover{
            border-color:var(--nl-primary);
            background:rgba(116,178,212,.08);
        }

        .nl-inv-tabbtn .nl-count{
            font-weight:900;
            padding:2px 8px;
            border-radius:999px;
            background:#f1f5f9;
            border:1px solid var(--nl-border);
            color:#334155;
            font-size:12px;
        }

        #{{ $prodId }}:checked ~ .nl-inv-tabbar label[for="{{ $prodId }}"],
        #{{ $genId }}:checked  ~ .nl-inv-tabbar label[for="{{ $genId }}"]{
            background:var(--nl-primary);
            border-color:var(--nl-primary);
            color:#fff;
        }

        #{{ $prodId }}:checked ~ .nl-inv-tabbar label[for="{{ $prodId }}"] .nl-count,
        #{{ $genId }}:checked  ~ .nl-inv-tabbar label[for="{{ $genId }}"] .nl-count{
            background:rgba(255,255,255,.22);
            border-color:rgba(255,255,255,.35);
            color:#fff;
        }

        .nl-inv-panels{
            margin-top:14px;
        }

        .nl-inv-panels .nl-panel{
            display:none;
        }

        #{{ $prodId }}:checked ~ .nl-inv-panels .nl-panel-prod{
            display:block;
        }

        #{{ $genId }}:checked ~ .nl-inv-panels .nl-panel-gen{
            display:block;
        }

        .nl-inv-card{
            background:#fff;
            border:1px solid rgba(226,232,240,.95);
            border-radius:var(--nl-radius);
            overflow:hidden;
            box-shadow:0 10px 30px rgba(2,6,23,.06);
        }

        .nl-inv-head{
            padding:12px 14px;
            border-bottom:1px solid var(--nl-border);
            background:var(--nl-soft);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            flex-wrap:wrap;
        }

        .nl-inv-head-title{
            font-weight:900;
            color:var(--nl-text);
        }

        .nl-inv-head-meta{
            color:var(--nl-muted);
            font-weight:800;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .nl-inv-table-wrap{
            overflow:auto;
        }

        .nl-inv-table{
            width:100%;
            border-collapse:collapse;
            min-width:1180px;
        }

        .nl-inv-table th{
            text-align:left;
            padding:12px 14px;
            color:var(--nl-muted);
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.06em;
            font-weight:900;
            border-bottom:1px solid var(--nl-border);
            white-space:nowrap;
            background:#fff;
        }

        .nl-inv-table td{
            padding:12px 14px;
            border-bottom:1px solid #eef2f7;
            vertical-align:middle;
            background:#fff;
        }

        .nl-inv-table tr:hover td{
            background:#fbfdff;
        }

        .nl-inv-empty{
            padding:22px;
            color:var(--nl-muted);
            font-weight:800;
            display:flex;
            gap:10px;
            align-items:center;
        }

        .nl-inv-empty i{
            color:var(--nl-primary);
        }

        .nl-inv-btn{
            text-decoration:none;
            border:1px solid var(--nl-border);
            background:#fff;
            border-radius:10px;
            padding:8px 10px;
            font-weight:900;
            color:var(--nl-text);
            display:inline-flex;
            gap:8px;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            font-family:inherit;
            font-size:13px;
            line-height:1;
            white-space:nowrap;
            transition:all .15s ease;
        }

        .nl-inv-btn:hover{
            background:#f8fafc;
            border-color:var(--nl-primary);
            color:#0b4e68;
        }

        .nl-inv-btn-primary{
            background:var(--nl-green);
            border-color:var(--nl-green);
            color:#fff;
        }

        .nl-inv-btn-primary:hover{
            background:var(--nl-green-dark);
            border-color:var(--nl-green-dark);
            color:#fff;
        }

        .nl-inv-file-tools{
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
            flex-wrap:wrap;
        }

        .nl-inv-pdf-list{
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
            min-width:230px;
        }

        .nl-inv-pdf-chip{
            border:1px solid var(--nl-border);
            background:#fff;
            border-radius:999px;
            padding:7px 9px;
            display:inline-flex;
            align-items:center;
            gap:7px;
            color:var(--nl-text);
            font-weight:900;
            font-size:12px;
            cursor:pointer;
            max-width:265px;
            transition:all .15s ease;
            text-decoration:none;
        }

        .nl-inv-pdf-chip:hover{
            border-color:var(--nl-primary);
            background:rgba(116,178,212,.08);
            color:#0b4e68;
        }

        .nl-inv-pdf-chip > i{
            color:var(--nl-danger);
        }

        .nl-inv-pdf-actions{
            display:inline-flex;
            gap:5px;
            align-items:center;
            margin-left:2px;
        }

        .nl-inv-pdf-mini{
            width:24px;
            height:24px;
            border-radius:999px;
            border:1px solid var(--nl-border);
            background:#fff;
            color:var(--nl-muted);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:all .15s ease;
            text-decoration:none;
            padding:0;
            font-size:12px;
        }

        .nl-inv-pdf-mini:hover{
            border-color:var(--nl-primary);
            color:#0b4e68;
            background:rgba(116,178,212,.08);
        }

        .nl-inv-pdf-mini.danger:hover{
            border-color:rgba(220,38,38,.35);
            color:#b91c1c;
            background:rgba(220,38,38,.06);
        }

        .nl-inv-trunc{
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            min-width:0;
            display:inline-block;
            max-width:170px;
            vertical-align:bottom;
        }

        .nl-inv-status{
            display:inline-flex;
            align-items:center;
            gap:6px;
            border:1px solid;
            border-radius:999px;
            padding:5px 9px;
            font-size:12px;
            font-weight:900;
            white-space:nowrap;
        }

        .nl-inv-no-files{
            color:var(--nl-muted);
            font-weight:800;
            font-size:13px;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }

        .nl-inv-picked{
            margin-top:7px;
            color:var(--nl-muted);
            font-weight:800;
            font-size:12px;
            text-align:right;
        }

        .nl-pdf-backdrop{
            position:fixed;
            inset:0;
            background:rgba(2,6,23,.62);
            z-index:100060;
            opacity:0;
            visibility:hidden;
            transition:opacity .18s ease, visibility .18s ease;
            backdrop-filter:blur(3px);
        }

        .nl-pdf-backdrop.active{
            opacity:1;
            visibility:visible;
        }

        .nl-pdf-modal{
            position:fixed;
            inset:4vh 3vw;
            background:#fff;
            border-radius:22px;
            box-shadow:0 30px 90px rgba(2,6,23,.42);
            z-index:100061;
            display:flex;
            flex-direction:column;
            transform:translateY(10px) scale(.98);
            opacity:0;
            visibility:hidden;
            transition:opacity .18s ease, transform .18s ease, visibility .18s ease;
            overflow:hidden;
            border:1px solid rgba(255,255,255,.24);
        }

        .nl-pdf-modal.active{
            opacity:1;
            visibility:visible;
            transform:translateY(0) scale(1);
        }

        .nl-pdf-head{
            padding:.9rem 1rem;
            border-bottom:1px solid var(--nl-border);
            background:linear-gradient(135deg, rgba(116,178,212,.14), rgba(147,194,28,.08));
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            flex-wrap:wrap;
        }

        .nl-pdf-title{
            font-weight:900;
            min-width:0;
            display:flex;
            gap:.6rem;
            align-items:center;
            color:var(--nl-text);
        }

        .nl-pdf-title i{
            color:var(--nl-danger);
        }

        .nl-pdf-body{
            flex:1;
            background:#0b1220;
            min-height:60vh;
        }

        .nl-pdf-body iframe{
            width:100%;
            height:100%;
            min-height:60vh;
            border:0;
            background:#0b1220;
        }

        @media(max-width:980px){
            .nl-inv-summary{
                grid-template-columns:repeat(2,minmax(160px,1fr));
            }
        }

        @media(max-width:760px){
            .nl-invoice-panel{
                padding:12px;
            }

            .nl-inv-summary{
                grid-template-columns:1fr;
            }

            .nl-inv-top{
                align-items:stretch;
            }

            .nl-inv-actions{
                width:100%;
            }

            .nl-inv-actions .nl-inv-btn{
                flex:1;
            }

            .nl-pdf-modal{
                inset:1.5vh 1.5vw;
                border-radius:16px;
            }

            .nl-inv-file-tools{
                justify-content:flex-start;
            }

            .nl-inv-picked{
                text-align:left;
            }
        }
    </style>

    <div class="nl-inv-top">
        <div style="min-width:0;">
            <div class="nl-inv-title">
                <i class="fa-solid fa-file-invoice" style="color:#74b2d4;"></i>
                Rechnungen
            </div>

            <div class="nl-inv-sub">
                Kunde:
                <span>{{ $customerName }}</span>
                &nbsp;•&nbsp; Alternative:
                <span>#{{ (int) $alternative_id }}</span>
                &nbsp;•&nbsp; Produkt:
                <span>{{ (int) $product_id > 0 ? '#' . (int) $product_id : 'Alle' }}</span>
            </div>
        </div>

        <div class="nl-inv-actions">
            <a href="{{ route('admin.invoices.index') }}?customer_id={{ $customer->id }}&object_id={{ (int) $alternative_id }}&product_id={{ (int) $product_id }}"
               class="nl-inv-btn">
                <i class="fa-solid fa-arrow-up-right-from-square" style="color:#64748b;"></i>
                Vollansicht
            </a>
        </div>
    </div>

    <div class="nl-inv-summary">
        <div class="nl-inv-summary-card">
            <div class="nl-inv-summary-label">Gesamt</div>
            <div class="nl-inv-summary-value">{{ $productCount + $generalCount }}</div>
        </div>

        <div class="nl-inv-summary-card">
            <div class="nl-inv-summary-label">Rechnungsbetrag</div>
            <div class="nl-inv-summary-value">{{ $formatMoney($sumTotal($allInvoices)) }}</div>
        </div>

        <div class="nl-inv-summary-card">
            <div class="nl-inv-summary-label">Bezahlt</div>
            <div class="nl-inv-summary-value">{{ $formatMoney($sumPaid($allInvoices)) }}</div>
        </div>

        <div class="nl-inv-summary-card">
            <div class="nl-inv-summary-label">Offen</div>
            <div class="nl-inv-summary-value">{{ $formatMoney($sumOpen($allInvoices)) }}</div>
        </div>
    </div>

    <div class="nl-inv-tabs" id="{{ $tabUid }}">
        <input type="radio" name="{{ $tabUid }}_tab" id="{{ $prodId }}" checked>
        <input type="radio" name="{{ $tabUid }}_tab" id="{{ $genId }}">

        <div class="nl-inv-tabbar">
            <label class="nl-inv-tabbtn" for="{{ $prodId }}">
                <i class="fa-solid fa-file-invoice" style="opacity:.85;"></i>
                Produkt-Rechnungen
                <span class="nl-count">{{ $productCount }}</span>
            </label>

            <label class="nl-inv-tabbtn" for="{{ $genId }}">
                <i class="fa-solid fa-layer-group" style="opacity:.85;"></i>
                Allgemeine Rechnungen
                <span class="nl-count">{{ $generalCount }}</span>
            </label>
        </div>

        <div class="nl-inv-panels">
            @foreach([
                [
                    'class' => 'nl-panel-prod',
                    'count' => $productCount,
                    'rows' => $productInvoices,
                    'empty' => 'Keine Produkt-Rechnungen gefunden.',
                ],
                [
                    'class' => 'nl-panel-gen',
                    'count' => $generalCount,
                    'rows' => $generalInvoices,
                    'empty' => 'Keine allgemeinen Rechnungen gefunden.',
                ],
            ] as $panel)
                <div class="nl-panel {{ $panel['class'] }}">
                    <div class="nl-inv-card">
                        <div class="nl-inv-head">
                            <div class="nl-inv-head-title">Treffer: {{ $panel['count'] }}</div>
                            <div class="nl-inv-head-meta">Letzte zuerst</div>
                        </div>

                        @if(!$panel['count'])
                            <div class="nl-inv-empty">
                                <i class="fa-solid fa-circle-info"></i>
                                {{ $panel['empty'] }}
                            </div>
                        @else
                            <div class="nl-inv-table-wrap">
                                <table class="nl-inv-table">
                                    <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Nr.</th>
                                        <th>Typ</th>
                                        <th>Status</th>
                                        <th style="text-align:right;">Gesamt</th>
                                        <th>PDFs</th>
                                        <th style="text-align:right;">Aktion</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($panel['rows'] as $inv)
                                        @php
                                            $s = strtolower((string) ($inv->status ?? 'draft'));
                                            $statusLabel = $statusMap[$s] ?? $s;
                                            $statusIcon = $statusIconMap[$s] ?? 'fa-circle';
                                        @endphp

                                        <tr data-nl-invoice-row="{{ $inv->id }}">
                                            <td style="font-weight:800;color:#0f172a;">
                                                {{ $formatDate($inv->issue_date ?? null) }}
                                            </td>

                                            <td style="font-weight:900;color:#0f172a;">
                                                {{ $inv->invoice_no ?? '—' }}
                                            </td>

                                            <td style="font-weight:800;color:#0f172a;">
                                                {{ $inv->type ?? '—' }}
                                            </td>

                                            <td style="font-weight:900;color:#334155;">
                                                <span class="nl-inv-status" style="{{ $statusStyle($s) }}">
                                                    <i class="fa-solid {{ $statusIcon }}"></i>
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>

                                            <td style="text-align:right;font-weight:900;color:#0f172a;">
                                                {{ $formatMoney($inv->total_amount ?? 0) }}
                                            </td>

                                            <td>
                                                <div class="nl-inv-pdf-list" data-nl-files-list="{{ $inv->id }}">
                                                    @forelse($inv->files ?? [] as $file)
                                                        @php
                                                            $fileName = $fileNameOf($file);
                                                            $fileViewUrl = route('admin.invoices.files.view', ['file' => $file->id]);
                                                            $fileDownloadUrl = route('admin.invoices.files.download', ['file' => $file->id]);
                                                        @endphp

                                                        <button type="button"
                                                                class="nl-inv-pdf-chip"
                                                                data-nl-local-open-pdf
                                                                data-file-id="{{ $file->id }}"
                                                                data-file-name="{{ e($fileName) }}"
                                                                data-view-url="{{ $fileViewUrl }}"
                                                                data-download-url="{{ $fileDownloadUrl }}">
                                                            <i class="fa-solid fa-file-pdf"></i>

                                                            <span class="nl-inv-trunc" title="{{ $fileName }}">
                                                                {{ $fileName }}
                                                            </span>

                                                            <span class="nl-inv-pdf-actions">
                                                                <a class="nl-inv-pdf-mini"
                                                                   href="{{ $fileDownloadUrl }}"
                                                                   title="Herunterladen"
                                                                   onclick="event.stopPropagation();">
                                                                    <i class="fa-solid fa-download"></i>
                                                                </a>

                                                                <span type="button"
                                                                      class="nl-inv-pdf-mini danger"
                                                                      title="Löschen"
                                                                      data-nl-delete-file="{{ $file->id }}"
                                                                      data-invoice-id="{{ $inv->id }}"
                                                                      onclick="event.stopPropagation();">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </span>
                                                            </span>
                                                        </button>
                                                    @empty
                                                        <span class="nl-inv-no-files" data-nl-no-files>
                                                            <i class="fa-regular fa-file-pdf"></i>
                                                            Keine PDF
                                                        </span>
                                                    @endforelse
                                                </div>
                                            </td>

                                            <td style="text-align:right;">
                                                <div class="nl-inv-file-tools">
                                                    <input type="file"
                                                           accept="application/pdf"
                                                           multiple
                                                           data-nl-file-input="{{ $inv->id }}"
                                                           style="display:none;">

                                                    <button type="button"
                                                            class="nl-inv-btn nl-inv-btn-primary"
                                                            data-nl-pick-files="{{ $inv->id }}">
                                                        <i class="fa-solid fa-paperclip"></i>
                                                        PDF wählen
                                                    </button>

                                                    <button type="button"
                                                            class="nl-inv-btn"
                                                            data-nl-upload-files="{{ $inv->id }}">
                                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                                        Hochladen
                                                    </button>

                                                    <a href="{{ route('admin.invoices.index') }}?open={{ $inv->id }}"
                                                       class="nl-inv-btn"
                                                       onclick="event.stopPropagation();">
                                                        <i class="fa-solid fa-folder-open" style="color:#64748b;"></i>
                                                        Öffnen
                                                    </a>
                                                </div>

                                                <div class="nl-inv-picked" data-nl-picked-hint="{{ $inv->id }}"></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PDF Viewer Modal --}}
    <div class="nl-pdf-backdrop" data-nl-pdf-backdrop></div>

    <div class="nl-pdf-modal" data-nl-pdf-modal>
        <div class="nl-pdf-head">
            <div class="nl-pdf-title">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="nl-inv-trunc" data-nl-pdf-title>PDF</span>
            </div>

            <div style="display:flex;gap:8px;align-items:center;justify-content:flex-end;flex-wrap:wrap;">
                <a class="nl-inv-btn" href="#" target="_blank" rel="noopener" data-nl-pdf-newtab>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    Neuer Tab
                </a>

                <a class="nl-inv-btn" href="#" data-nl-pdf-download>
                    <i class="fa-solid fa-download"></i>
                    Herunterladen
                </a>

                <button class="nl-inv-btn" type="button" data-nl-pdf-close>
                    <i class="fa-solid fa-xmark"></i>
                    Schließen
                </button>
            </div>
        </div>

        <div class="nl-pdf-body">
            <iframe src="about:blank" data-nl-pdf-frame></iframe>
        </div>
    </div>

    
</div>