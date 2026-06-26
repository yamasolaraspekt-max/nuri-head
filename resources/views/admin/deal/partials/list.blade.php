@php
    $dealWorkflowStages = collect($dealWorkflowStages ?? []);
    if ($dealWorkflowStages->isEmpty()) {
        $dealWorkflowStages = collect([
            (object) ['key' => 'open', 'label' => 'Offen', 'name' => 'Offen', 'color' => '#f59e0b', 'is_default' => true],
            (object) ['key' => 'confirm', 'label' => 'Bestätigt', 'name' => 'Bestätigt', 'color' => '#10b981', 'is_default' => false],
            (object) ['key' => 'inconfirm', 'label' => 'Unbestätigt', 'name' => 'Unbestätigt', 'color' => '#ef4444', 'is_default' => false],
            (object) ['key' => 'pause', 'label' => 'Pausiert', 'name' => 'Pausiert', 'color' => '#f59e0b', 'is_default' => false],
            (object) ['key' => 'cancel', 'label' => 'Absage', 'name' => 'Absage', 'color' => '#ef4444', 'is_default' => false],
        ]);
    }

    $dealWorkflowLabelMap = $dealWorkflowLabelMap ?? $dealWorkflowStages->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->label ?? $stage->name ?? $stage->key)])->all();
    $dealWorkflowColorMap = $dealWorkflowColorMap ?? $dealWorkflowStages->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->color ?? '#93c21c')])->all();
@endphp

<style>
    .deal-update-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 6px;
        padding: 2px 7px;
        border-radius: 999px;
        border: 1px solid #d9ef9d;
        background: #f4fae7;
        color: #55720d;
        font-size: 9px;
        font-weight: 900;
        vertical-align: middle;
        white-space: nowrap;
    }

    .deal-update-badge.fresh {
        background: #93c21c;
        color: #fff;
        border-color: #93c21c;
        animation: dealFreshPulse 2s infinite;
    }

    .deal-latest-change-line {
        color: #55720d;
        font-weight: 800;
    }

    .deal-inline-status-select {
        width: 100%;
        min-width: 135px;
        height: 32px;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--status-color, #93c21c) 55%, #ffffff);
        background: color-mix(in srgb, var(--status-color, #93c21c) 12%, #ffffff);
        color: var(--status-color, #55720d);
        font-size: 11px;
        font-weight: 900;
        padding: 0 8px;
        outline: none;
        cursor: pointer;
    }

    .deal-inline-status-select:focus {
        box-shadow: 0 0 0 3px rgba(147, 194, 28, .18);
        border-color: #93c21c;
    }

    @keyframes dealFreshPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(147, 194, 28, .45);
        }

        70% {
            box-shadow: 0 0 0 7px rgba(147, 194, 28, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(147, 194, 28, 0);
        }
    }

    /* ===== Compact enterprise list layout fix ===== */
    .deal-list-compact .deal-table-head,
    .deal-list-compact .deal-item-row {
        grid-template-columns:
            30px
            82px
            minmax(145px, .85fr)
            minmax(145px, .85fr)
            112px
            92px
            112px
            48px
            54px
            minmax(178px, 1.05fr)
            48px !important;
        gap: 6px !important;
    }

    .deal-list-compact .deal-item-row {
        min-height: 62px;
        padding: 7px 8px;
    }

    .deal-list-compact .deal-cell {
        overflow: hidden;
        min-width: 0 !important;
    }

    .deal-list-compact .deal-cell.status-cell,
    .deal-list-compact .deal-cell.action-cell {
        overflow: visible !important;
    }

    .deal-list-compact .deal-ttl {
        font-size: 12px;
        line-height: 1.15;
        margin-bottom: 2px;
    }

    .deal-list-compact .deal-subt {
        font-size: 9px;
        line-height: 1.25;
    }

    .deal-list-compact .deal-subt > div {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .deal-list-compact .deal-date-line {
        font-size: 9px;
        line-height: 1.25;
        margin-bottom: 2px;
        max-width: 100%;
    }

    .deal-list-compact .deal-update-badge {
        margin-left: 0;
        margin-top: 3px;
        max-width: 100%;
        padding: 2px 6px;
        font-size: 8px;
    }

    .deal-list-compact .deal-latest-change-line {
        display: flex;
        align-items: center;
        gap: 3px;
        margin-top: 3px;
        max-width: 100%;
        color: #55720d;
        font-size: 9px;
        font-weight: 800;
    }

    .deal-list-compact .deal-inline-status-select {
        min-width: 160px;
        height: 34px;
        padding: 0 9px;
        font-size: 10px;
    }

    .deal-list-compact .deal-status-meta {
        margin-top: 4px;
        color: #6b7280;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .deal-list-compact .deal-service-box {
        gap: 5px;
    }

    .deal-list-compact .deal-service-badge,
    .deal-list-compact .deal-profile {
        width: 26px;
        height: 26px;
        flex-basis: 26px;
    }

    .deal-list-compact .deal-price {
        font-size: 11px;
    }

    
    .dropdown-menu.deal-dropdown-fixed {
        position: fixed !important;
        z-index: 2147483000 !important;
        display: block !important;
        min-width: 235px !important;
        max-width: 280px !important;
        padding: 8px 0;
        border: 1px solid var(--deal-border, #e5e7eb);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
    }
    .dropdown-menu.deal-dropdown-fixed .dropdown-item > a {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 9px 12px !important;
        color: var(--deal-text, #111827) !important;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 800;
        white-space: nowrap;
    }
@media (max-width: 1400px) {
        .deal-list-compact .deal-table-wrap {
            overflow-x: auto;
        }

        .deal-list-compact .deal-table-head,
        .deal-list-compact .deal-item-row {
            min-width: 1120px !important;
            grid-template-columns:
                30px 80px 145px 145px 112px 92px 108px 48px 54px 178px 48px !important;
        }
    }

    @media (max-width: 1199px) {
        .deal-list-compact .deal-table-head {
            display: none;
        }

        .deal-list-compact .deal-item-row {
            min-width: 0 !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .deal-list-compact .deal-inline-status-select {
            min-width: 100%;
        }
    }

</style>

<div class="deal-toolbar">
    <form action="{{ route('deal.details') }}" method="GET" class="w-100">
        <div class="deal-toolbar-left">
            <div class="deal-filter-block search">
                <label class="deal-filter-label">Suche</label>
                <input type="text" name="search" value="{{ request('search') }}" class="deal-input"
                    placeholder="Suche...">
            </div>

            <div class="deal-filter-block">
                <label class="deal-filter-label">Status</label>
                <select name="status" class="deal-select">
                    <option value="">Alle Unterphasen</option>
                    @foreach($dealWorkflowStages as $workflowStage)
                        <option value="{{ $workflowStage->key }}" {{ request('status') == $workflowStage->key ? 'selected' : '' }}>
                            {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                        </option>
                    @endforeach
                    <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Abgeschlossen
                    </option>
                    <option value="Junk" {{ request('status') == 'Junk' ? 'selected' : '' }}>Junk</option>
                </select>
            </div>

            <div class="deal-filter-block">
                <label class="deal-filter-label">Ansicht</label>
                <select name="filter" id="filter" class="deal-select">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Alle Angebote</option>
                    <option value="my" {{ request('filter', 'my') == 'my' ? 'selected' : '' }}>Meine Angebote</option>
                </select>
            </div>

            <div class="deal-filter-block">
                <label class="deal-filter-label">Sortieren nach</label>
                <select name="sort_by" class="deal-select">
                    <option value="latest_change" {{ request('sort_by', 'latest_change') == 'latest_change' ? 'selected' : '' }}>Neueste Änderung</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Erstellt am
                    </option>
                    <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Auftrag geändert
                    </option>
                    <option value="customer" {{ request('sort_by') == 'customer' ? 'selected' : '' }}>Kunde</option>
                    <option value="product" {{ request('sort_by') == 'product' ? 'selected' : '' }}>Produkt</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Kanban Status</option>
                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Auftragssumme</option>
                    <option value="city" {{ request('sort_by') == 'city' ? 'selected' : '' }}>Ort</option>
                </select>
            </div>

            <div class="deal-filter-block">
                <label class="deal-filter-label">Richtung</label>
                <select name="sort_dir" class="deal-select">
                    <option value="desc" {{ request('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>Neueste zuerst
                    </option>
                    <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Älteste zuerst</option>
                </select>
            </div>
        </div>

        <div class="deal-toolbar-right">
            <button class="deal-btn-soft" type="submit">
                <i class="fa fa-search"></i>
                Suchen
            </button>

            @if(request()->hasAny(['search', 'status', 'filter']))
                <a href="{{ route('deal.details') }}" class="deal-btn-soft">
                    Zurücksetzen
                </a>
            @endif
        </div>
    </form>
</div>

<div class="deal-bulk-toolbar" id="dealBulkToolbar">
    <div class="deal-bulk-left">
        <label class="deal-check-label">
            <input type="checkbox" id="selectAllDeals">
            <span>Alle auswählen</span>
        </label>

        <span class="deal-selected-count">
            <strong id="selectedDealsCount">0</strong> ausgewählt
        </span>
    </div>

    <div class="deal-bulk-right">
        <select id="bulkAction" class="deal-select" style="max-width:190px;">
            <option value="">Aktion wählen</option>
            <option value="delete">Bulk löschen</option>
            <option value="junk">Bulk Junk</option>
            <option value="unjunk">Bulk Un-Junk</option>
            <option value="restore">Bulk wiederherstellen</option>
            <option value="status">Status ändern</option>
        </select>

        <select id="bulkStatus" class="deal-select" style="max-width:190px; display:none;">
            <option value="">Unterphase wählen</option>
            @foreach($dealWorkflowStages as $workflowStage)
                <option value="{{ $workflowStage->key }}">
                    {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                </option>
            @endforeach
            <option value="complete">Abgeschlossen</option>
            <option value="Junk">Junk</option>
        </select>

        <button type="button" class="deal-btn" id="runBulkAction">
            <i class="fa fa-check"></i>
            Ausführen
        </button>
    </div>
</div>

<style>
    /* ===== Deal no-overlap card list ===== */
    .deal-list-card-wrap{
        padding:12px;
        background:#f8fafc;
        border-radius:18px;
    }
    .deal-list-card-head{
        display:grid;
        grid-template-columns: 32px 1.7fr 1.2fr 220px 106px;
        gap:12px;
        align-items:center;
        padding:10px 12px;
        color:#6b7280;
        font-size:10px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.05em;
    }
    .deal-list-card-stack{
        display:flex;
        flex-direction:column;
        gap:10px;
    }
    .deal-row-card{
        position:relative;
        display:grid;
        grid-template-columns: 32px minmax(0, 1.75fr) minmax(0, 1.12fr) 220px 106px;
        gap:12px;
        align-items:stretch;
        padding:12px;
        border:1px solid var(--deal-border, #e5e7eb);
        border-left:4px solid var(--row-status-color, #93c21c);
        border-radius:16px;
        background:#fff;
        box-shadow:0 1px 2px rgba(15,23,42,.04);
        transition:.18s ease;
    }
    .deal-row-card:hover{
        border-color:#d1d5db;
        box-shadow:0 10px 24px rgba(15,23,42,.08);
        transform:translateY(-1px);
        z-index:20;
    }
    .deal-row-select{display:flex;align-items:center;justify-content:center;}
    .deal-row-main{min-width:0;display:flex;flex-direction:column;gap:8px;}
    .deal-row-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;min-width:0;}
    .deal-row-titlebox{min-width:0;}
    .deal-row-no{display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:999px;background:#eff6ff;color:#74b2d4;font-size:10px;font-weight:900;white-space:nowrap;}
    .deal-row-customer{display:flex;align-items:center;gap:7px;min-width:0;}
    .deal-row-customer .deal-ttl{font-size:14px;line-height:1.2;margin:0;min-width:0;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-location{margin-top:3px;color:#6b7280;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-update{display:flex;align-items:center;gap:6px;min-width:0;}
    .deal-update-badge{display:inline-flex;align-items:center;gap:4px;max-width:150px;padding:3px 7px;border-radius:999px;border:1px solid #d9ef9d;background:#f4fae7;color:#55720d;font-size:9px;font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-update-badge.fresh{background:#93c21c;color:#fff;border-color:#93c21c;animation:dealFreshPulse 2s infinite;}
    .deal-latest-change-line{min-width:0;color:#55720d;font-size:10px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-meta{display:grid;grid-template-columns: repeat(4, minmax(0,1fr));gap:6px;}
    .deal-mini{min-width:0;padding:7px 9px;border:1px solid #eef2f7;border-radius:11px;background:#f9fafb;}
    .deal-mini-label{display:block;margin-bottom:2px;color:#9ca3af;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.04em;}
    .deal-mini-value{display:block;color:#111827;font-size:11px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-product{min-width:0;display:flex;align-items:center;gap:10px;padding:9px 10px;border:1px solid #eef2f7;border-radius:13px;background:#fbfdff;}
    .deal-service-badge{width:34px;height:34px;flex:0 0 34px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:#93c21c;color:#fff;font-size:12px;font-weight:900;}
    .deal-profile{width:32px;height:32px;flex:0 0 32px;border:2px solid #93c21c;border-radius:999px;object-fit:cover;}
    .deal-product-text{min-width:0;}
    .deal-product-text .deal-ttl{font-size:13px;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-product-text .deal-subt{font-size:10px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-status{min-width:0;display:flex;flex-direction:column;justify-content:center;gap:7px;padding:9px 10px;border:1px solid color-mix(in srgb, var(--row-status-color, #93c21c) 28%, #e5e7eb);border-radius:13px;background:color-mix(in srgb, var(--row-status-color, #93c21c) 7%, #fff);}
    .deal-status-caption{display:flex;align-items:center;justify-content:space-between;gap:6px;color:#6b7280;font-size:9px;font-weight:900;text-transform:uppercase;}
    .deal-inline-status-select{width:100%;min-width:0;height:36px;border-radius:999px;border:1px solid color-mix(in srgb, var(--row-status-color, #93c21c) 55%, #fff);background:#fff;color:var(--row-status-color, #55720d);font-size:11px;font-weight:900;padding:0 9px;outline:none;cursor:pointer;}
    .deal-inline-status-select:focus{box-shadow:0 0 0 3px rgba(147,194,28,.18);border-color:#93c21c;}
    .deal-status-meta{color:#6b7280;font-size:10px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .deal-row-actions{display:flex;align-items:center;justify-content:flex-end;gap:6px;min-width:0;}
    .deal-row-actions .deal-btn-ic{width:32px;height:32px;min-width:32px;}
    .deal-action-cluster{display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:flex-end;}
    .deal-row-actions .dropdown-menu{z-index:999999;}
    .deal-badge-count{position:absolute;top:-6px;right:-6px;min-width:16px;height:16px;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;border-radius:999px;background:#ef4444;color:#fff;font-size:9px;font-weight:900;}
    @keyframes dealFreshPulse{0%{box-shadow:0 0 0 0 rgba(147,194,28,.42)}70%{box-shadow:0 0 0 7px rgba(147,194,28,0)}100%{box-shadow:0 0 0 0 rgba(147,194,28,0)}}
    @media (max-width: 1380px){
        .deal-list-card-head{display:none;}
        .deal-row-card{grid-template-columns: 30px minmax(0,1fr) 220px 104px;}
        .deal-row-product{grid-column:2 / 3;}
        .deal-row-status{grid-column:3 / 4;grid-row:1 / span 2;}
        .deal-row-actions{grid-column:4 / 5;grid-row:1 / span 2;align-items:center;}
        .deal-row-meta{grid-template-columns:repeat(3,minmax(0,1fr));}
    }
    @media (max-width: 992px){
        .deal-row-card{grid-template-columns:1fr;}
        .deal-row-select{position:absolute;top:14px;right:14px;}
        .deal-row-top{padding-right:38px;}
        .deal-row-product,.deal-row-status,.deal-row-actions{grid-column:auto;grid-row:auto;}
        .deal-row-actions{justify-content:flex-start;}
        .deal-row-meta{grid-template-columns:repeat(2,minmax(0,1fr));}
    }
    @media (max-width: 560px){.deal-row-meta{grid-template-columns:1fr}.deal-row-top{flex-direction:column}.deal-update-badge{max-width:100%;}.deal-row-actions{align-items:stretch}.deal-action-cluster{justify-content:flex-start}.deal-inline-status-select{height:40px}}
</style>

<div class="deal-list-card-wrap">
    <div class="deal-list-card-head">
        <div><input type="checkbox" id="selectAllDealsHead"></div>
        <div>Kunde / Änderung</div>
        <div>Produkt / Verantwortlich</div>
        <div>Kanban Status</div>
        <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="deal-list-card-stack">
        @forelse($data as $item)
            @php
                $services = [
                    'complete' => 'Komplettlösung',
                    'montage' => 'Montage',
                    'product' => 'Produkt',
                    'plan' => 'Planung',
                    'maintenance' => 'Wartung',
                    'repair' => 'Reparatur',
                    'others' => 'Sonstiges',
                ];

                $service = $services[$item->service] ?? ($item->service ?? 'Nicht gesetzt');
                $gender = $item->emp_gender ?? $item->gender ?? null;
                $defaultImage = $gender === 'Male' ? asset('images/gender/male.png') : asset('images/gender/female.png');
                $employeeImage = (!empty($item->emp_image) && file_exists(public_path('images/employee/' . $item->emp_image)))
                    ? asset('images/employee/' . $item->emp_image)
                    : $defaultImage;

                $checkedBy = DB::table('employees')->where('id', $item->checked_by)->select('name', 'lastname')->first();
                $reviewedBy = DB::table('employees')->where('id', $item->reviewer_id)->select('name', 'lastname')->first();

                $statusLabel = $dealWorkflowLabelMap[$item->status] ?? match ($item->status) {
                    'complete' => 'Abgeschlossen',
                    'Junk' => 'Junk',
                    default => ucfirst(str_replace('_', ' ', (string) $item->status)),
                };
                $statusColor = $dealWorkflowColorMap[$item->status] ?? match ($item->status) {
                    'confirm', 'complete' => '#10b981',
                    'inconfirm', 'Junk', 'cancel' => '#ef4444',
                    'open', 'pause' => '#f59e0b',
                    default => '#93c21c',
                };

                $latestChangeAt = $item->latest_change_at ?? $item->updated_at ?? $item->created_at ?? null;
                $latestChangeSource = $item->latest_change_source ?? 'Auftrag';
                $latestChangeText = $item->latest_change_text ?? 'Auftrag wurde geändert';
                $isFreshChange = $latestChangeAt ? \Carbon\Carbon::parse($latestChangeAt)->greaterThanOrEqualTo(now()->subDays(3)) : false;

                $auftragNo = $item->order_number ?? $item->deal_no ?? $item->offer_number ?? ('#' . $item->id);
                $customerDisplayName = trim(($item->name ?? '') . ' ' . ($item->lastname ?? '')) ?: ($item->firma ?? 'Unbekannter Kunde');
                $priceValue = is_numeric($item->price ?? null) ? number_format((float) $item->price, 2, ',', '.') . ' €' : ($item->price ?? 'unbekannt');
                $createdDate = !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') : '–';
                $signDate = $item->sign_date ?: '–';
                $confirmedDate = $item->confirmed_at ?: '–';

                $offerRecord = null;
                $offerDetailId = null;
                $folderId = $item->offer_folder_id ?? null;

                if (!empty($item->offer_id)) {
                    $offerRecord = DB::table('offers')->select('id')->where('id', $item->offer_id)->first();
                }

                if (!$offerRecord) {
                    $offerRecord = DB::table('offers')
                        ->select('id')
                        ->where('customer_id', $item->customer_id)
                        ->where('product_id', $item->product_id)
                        ->where('alternative_id', $item->alternative_id)
                        ->orderByDesc('id')
                        ->first();
                }

                if ($offerRecord) {
                    $detail = DB::table('offer_details')->select('id', 'offer_folder_id')->where('offer_id', $offerRecord->id)->orderByDesc('id')->first();
                    $offerDetailId = $detail->id ?? null;
                    $folderId = $folderId ?: ($detail->offer_folder_id ?? null);
                }

                $canUpdateCustomer = DB::table('user_rolls')
                    ->where('user_rolls.user_id', '=', auth()->user()->name)
                    ->where('user_rolls.item_id', '=', 'Customer')
                    ->where('user_rolls.is_update', '=', 'on')
                    ->exists();

                $canDeleteCustomer = DB::table('user_rolls')
                    ->where('user_rolls.user_id', '=', auth()->user()->name)
                    ->where('user_rolls.item_id', '=', 'Customer')
                    ->where('user_rolls.is_delete', '=', 'on')
                    ->exists();
            @endphp

            <div id="deal-{{ $item->id }}" class="deal-item deal-row-card" style="--row-status-color: {{ $statusColor }};">
                <div class="deal-row-select">
                    <input type="checkbox" class="deal-row-checkbox" value="{{ $item->id }}">
                </div>

                <div class="deal-row-main">
                    <div class="deal-row-top">
                        <div class="deal-row-titlebox">
                            <a href="{{ route('deal.profile', $item->id) }}" class="deal-link" title="Auftrag Profil öffnen">
                                <div class="deal-row-customer">
                                    <span class="deal-row-no">{{ $auftragNo }}</span>
                                    <span class="deal-ttl">{{ $customerDisplayName }}</span>
                                </div>
                                <div class="deal-row-location">
                                    <i class="feather icon-map-pin"></i>
                                    {{ $item->city ?? 'Ort unbekannt' }}
                                    @if(!empty($item->postcode)) · {{ $item->postcode }} @endif
                                </div>
                            </a>
                        </div>

                        @if($latestChangeAt)
                            <div class="deal-row-update">
                                <span class="deal-update-badge {{ $isFreshChange ? 'fresh' : '' }}">
                                    {{ $isFreshChange ? 'Neu' : 'Update' }} · {{ $latestChangeSource }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="deal-subt">
                        @if($latestChangeAt)
                            <div class="deal-latest-change-line">
                                <i class="feather icon-zap"></i>
                                <span>{{ $latestChangeText }} · {{ \Carbon\Carbon::parse($latestChangeAt)->format('d.m.Y H:i') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="deal-row-meta">
                        <div class="deal-mini">
                            <span class="deal-mini-label">Summe</span>
                            <span class="deal-mini-value editable-cell price-cell" data-field="price" data-id="{{ $item->id }}">{{ $priceValue }}</span>
                        </div>
                        <div class="deal-mini">
                            <span class="deal-mini-label">Erstellt</span>
                            <span class="deal-mini-value">{{ $createdDate }}</span>
                        </div>
                        <div class="deal-mini">
                            <span class="deal-mini-label">Sign / Best.</span>
                            <span class="deal-mini-value">{{ $signDate }} / {{ $confirmedDate }}</span>
                        </div>
                        <div class="deal-mini">
                            <span class="deal-mini-label">Prüfung</span>
                            <span class="deal-mini-value">
                                {{ $checkedBy ? trim($checkedBy->name . ' ' . $checkedBy->lastname) : 'Nicht definiert' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="deal-row-product">
                    <div class="deal-service-badge">{{ $item->initial ?? '?' }}</div>
                    <img src="{{ $employeeImage }}" alt="Profile" class="deal-profile" data-toggle="tooltip" data-original-title="{{ ($item->emp_name ?? null) && ($item->emp_lastname ?? null) ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                    <div class="deal-product-text">
                        <div class="deal-ttl">{{ $item->article_group ?? 'Produkt' }}</div>
                        <div class="deal-subt">{{ $service }}</div>
                        @if($reviewedBy)
                            <div class="deal-subt">Review: {{ $reviewedBy->name }} {{ $reviewedBy->lastname }}</div>
                        @endif
                    </div>
                </div>

                <div class="deal-row-status">
                    <div class="deal-status-caption">
                        <span>Kanban Status</span>
                        <i class="feather icon-refresh-cw"></i>
                    </div>
                    <select class="deal-inline-status-select js-deal-list-status" data-deal-id="{{ $item->id }}" data-current-status="{{ $item->status }}">
                        @foreach($dealWorkflowStages as $workflowStage)
                            <option value="{{ $workflowStage->key }}" @selected((string) $workflowStage->key === (string) $item->status)>
                                {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                            </option>
                        @endforeach
                        <option value="complete" @selected((string) $item->status === 'complete')>Abgeschlossen</option>
                        <option value="Junk" @selected((string) $item->status === 'Junk')>Junk</option>
                    </select>
                    <div class="deal-status-meta">{{ $latestChangeAt ? \Carbon\Carbon::parse($latestChangeAt)->diffForHumans() : $statusLabel }}</div>
                </div>

                <div class="deal-row-actions">
                    <div class="deal-action-cluster">
                        <button type="button" class="deal-btn-ic warning open-notes-sidebar position-relative"
                            data-deal-id="{{ $item->id }}"
                            data-customer-id="{{ $item->customer_id }}"
                            data-alternative-id="{{ $item->alternative_id }}"
                            data-product-id="{{ $item->product_id }}"
                            title="Notizen">
                            <i class="fa fa-sticky-note-o"></i>
                            @if(($item->notes_count ?? 0) > 0)
                                <span class="deal-badge-count">{{ $item->notes_count }}</span>
                            @endif
                        </button>

                        <button type="button" class="deal-btn-ic primary open-upload-sidebar position-relative"
                            data-customer-id="{{ $item->customer_id }}"
                            data-alternative-id="{{ $item->alternative_id }}"
                            data-product-id="{{ $item->product_id }}"
                            data-item-id="{{ $item->id }}"
                            title="Dokumente">
                            <i class="fa fa-picture-o"></i>
                            @if(($item->files_count ?? 0) > 0)
                                <span class="deal-badge-count">{{ $item->files_count }}</span>
                            @endif
                        </button>

                        <a href="{{ route('deal.profile', $item->id) }}" class="deal-btn-ic success" title="Auftrag Profil">
                            <i class="feather icon-file-text"></i>
                        </a>

                        <button type="button" class="deal-btn-ic open-deal-history-sidebar" data-deal-id="{{ $item->id }}" data-order-number="{{ $auftragNo }}" title="Historie">
                            <i class="feather icon-clock"></i>
                        </button>

                        <div class="btn-group dropup dropdown-icon-wrapper">
                            <button type="button" class="deal-btn-ic deal-menu-toggle" data-deal-menu-toggle="1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Menü">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <span class="dropdown-item"><a href="{{ url('new_lead_profile/' . $item->customer_id) }}" class="text-primary"><i class="feather icon-user primary"></i> Kundenprofil</a></span>
                                @if(!empty($folderId))
                                    <span class="dropdown-item"><a href="{{ route('admin.offers.folders.show', $folderId) }}" class="text-primary"><i class="feather icon-folder primary"></i> Angebot Ordner</a></span>
                                @endif
                                @if(!empty($offerDetailId))
                                    <span class="dropdown-item"><a href="{{ route('deal.material.list', $offerDetailId) }}" class="text-success"><i class="feather icon-layers"></i> Materialliste</a></span>
                                @endif
                                @if($canUpdateCustomer)
                                    <span class="dropdown-item"><a href="{{ url('/deal_delete/' . $item->id) }}" class="text-danger" onclick="return confirm('Auftrag wirklich löschen?')"><i class="feather icon-trash danger"></i> Löschen</a></span>
                                @endif
                                @if($canDeleteCustomer)
                                    @if($item->status !== 'Junk')
                                        <span class="dropdown-item"><a href="{{ url('/deal_junk/' . $item->id) }}" class="text-danger" onclick="return confirm('Auftrag als Junk setzen?')"><i class="fa fa-power-off danger"></i> Junk</a></span>
                                    @else
                                        <span class="dropdown-item"><a href="{{ url('/deal_unjunk/' . $item->id) }}" class="text-primary" onclick="return confirm('Auftrag aus Junk wiederherstellen?')"><i class="fa fa-power-off primary"></i> Un-Junk</a></span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="gallery-container mt-2 d-flex" id="gallery-container-{{ $item->id }}"></div>
                </div>
            </div>
        @empty
            <div class="deal-empty">Keine Aufträge gefunden.</div>
        @endforelse
    </div>
</div>
