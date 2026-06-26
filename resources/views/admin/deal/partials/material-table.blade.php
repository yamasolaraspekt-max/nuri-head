@php
$rows = collect($rows ?? []);
$source = $source ?? 'offer';
$isCompare = $source === 'compare';

$tableMode = $tableMode ?? 'default';
$isOrderMode = $tableMode === 'order';

$employees = collect($employees ?? []);
$distributors = collect($distributors ?? []);
$brands = collect($brands ?? []);

$statusLabels = $statusLabels ?? [
    'lager' => 'Im Lager',
    'bestellen' => 'Bestellen',
    'teilweise' => 'Teilweise',
    'unbekannt' => 'Unbekannt',
];

$changeLabels = $changeLabels ?? [
    'same' => 'Unverändert',
    'changed' => 'Geändert',
    'added' => 'Neu',
    'removed' => 'Entfernt',
];

$formatQty = function ($value) {
    return number_format((float) ($value ?? 0), 2, ',', '.');
};

$unitOf = function ($material) {
    return $material['measure']
        ?? $material['unit']
        ?? $material['found_unit']
        ?? 'Stk.';
};

$dateValue = function ($value) {
    if (empty($value)) {
        return '';
    }

    try {
        return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
    } catch (\Throwable $e) {
        return '';
    }
};

$dateTimeValue = function ($value) {
    if (empty($value)) {
        return '';
    }

    try {
        return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i');
    } catch (\Throwable $e) {
        return '';
    }
};

$dateTimeFormat = function ($value, string $format = 'd.m.Y H:i') {
    if (empty($value)) {
        return '—';
    }

    try {
        return \Illuminate\Support\Carbon::parse($value)->format($format);
    } catch (\Throwable $e) {
        return (string) $value;
    }
};
@endphp

<style>
    .material-table-wrap {
        width: 100%;
        overflow-x: auto;
        background: #fff;
    }

    .material-table {
        width: 100%;
        min-width:
            {{ $isOrderMode ? '1550px' : '1000px' }}
        ;
        border-collapse: collapse;
        font-size: 12px;
    }

    .material-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        padding: 10px 12px;
        border-bottom: 2px solid var(--mat-border-strong);
        white-space: nowrap;
        text-align: left;
    }

    .material-table tbody td {
        padding: 12px;
        border-bottom: 1px solid var(--mat-border);
        vertical-align: top;
        color: var(--mat-text);
    }

    .material-table tbody tr:hover {
        background: #f8fafc;
    }

    .material-table tbody tr.is-order-editing {
        background: #fffdf4;
    }

    .material-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--mat-text);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .material-subtext {
        font-size: 11px;
        color: var(--mat-muted);
        line-height: 1.4;
    }

    .cell-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        margin-right: 4px;
    }

    .material-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .material-pill.lager,
    .material-pill.added,
    .material-pill.approved {
        background: var(--mat-success-soft);
        color: #059669;
        border-color: #a7f3d0;
    }

    .material-pill.bestellen,
    .material-pill.removed,
    .material-pill.not-approved {
        background: var(--mat-danger-soft);
        color: #dc2626;
        border-color: #fecaca;
    }

    .material-pill.teilweise {
        background: var(--mat-warning-soft);
        color: #d97706;
        border-color: #fde68a;
    }

    .material-pill.unbekannt,
    .material-pill.same {
        background: var(--mat-bg);
        color: var(--mat-muted);
        border-color: var(--mat-border);
    }

    .material-pill.changed {
        background: var(--mat-blue-soft);
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .material-pill.source-offer {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    .material-pill.source-feinaufmass {
        background: var(--mat-purple-soft);
        color: var(--mat-purple);
        border-color: #ddd6fe;
    }

    .material-pill.order-open {
        background: var(--mat-danger-soft);
        color: #dc2626;
        border-color: #fecaca;
    }

    .material-pill.order-ordered {
        background: var(--mat-warning-soft);
        color: #d97706;
        border-color: #fde68a;
    }

    .material-pill.order-delivered {
        background: var(--mat-success-soft);
        color: #059669;
        border-color: #a7f3d0;
    }

    .qty-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 150px;
        margin-bottom: 2px;
        gap: 8px;
    }

    .material-qty-old {
        color: var(--mat-muted);
        text-decoration: line-through;
    }

    .material-qty-new {
        font-weight: 700;
        color: var(--mat-text);
    }

    .material-qty-plus {
        color: #059669;
        font-weight: 600;
    }

    .material-qty-minus {
        color: #dc2626;
        font-weight: 600;
    }

    .stack-gap {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .flex-row-gap {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .material-img {
        width: 44px;
        height: 44px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid var(--mat-border-strong);
        background: #fff;
    }

    .order-input,
    .order-select,
    .order-textarea {
        width: 100%;
        min-height: 30px;
        border: 1px solid var(--mat-border-strong);
        border-radius: 4px;
        padding: 5px 7px;
        font-size: 12px;
        background: #fff;
        color: var(--mat-text);
        outline: none;
    }

    .order-input:focus,
    .order-select:focus,
    .order-textarea:focus {
        border-color: var(--mat-primary);
        box-shadow: 0 0 0 2px var(--mat-primary-soft);
    }

    .order-textarea {
        min-height: 54px;
        resize: vertical;
    }

    .order-display-box {
        border: 1px solid var(--mat-border);
        background: #f8fafc;
        border-radius: 4px;
        padding: 8px;
        min-height: 44px;
    }

    .order-display-line {
        font-size: 12px;
        color: var(--mat-text);
        line-height: 1.45;
    }

    .order-display-muted {
        font-size: 11px;
        color: var(--mat-muted);
    }

    .order-edit-panel {
        display: none;
    }

    tr.is-order-editing .order-edit-panel {
        display: flex;
    }

    tr.is-order-editing .order-read-panel {
        display: none;
    }

    .order-action-row {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .order-mini-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        padding: 0 10px;
        border-radius: 4px;
        border: 1px solid var(--mat-border-strong);
        background: #fff;
        color: var(--mat-text);
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.15s ease;
    }

    .order-mini-btn:hover {
        background: #f8fafc;
    }

    .order-mini-btn.ok {
        border-color: #93c21c;
        color: #4d7c0f;
    }

    .order-mini-btn.cancel {
        border-color: #ef4444;
        color: #b91c1c;
    }

    .order-mini-btn[disabled] {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .material-small-input {
        width: 100px;
        height: 28px;
        border: 1px solid var(--mat-border-strong);
        border-radius: var(--mat-radius-sm);
        padding: 4px 8px;
        font-size: 12px;
        outline: none;
    }

    .material-small-input:focus {
        border-color: var(--mat-primary);
        box-shadow: 0 0 0 2px var(--mat-primary-soft);
    }

    .material-note {
        width: 100%;
        min-height: 40px;
        border: 1px solid var(--mat-border-strong);
        border-radius: var(--mat-radius-sm);
        padding: 6px 8px;
        font-size: 12px;
        resize: vertical;
        outline: none;
    }
</style>

<div class="material-table-wrap">
    @if($rows->count())
        <table class="material-table">
            <thead>
                <tr>
                    <th class="no-print" style="width: 36px;">
                        <input type="checkbox"
                            class="material-check js-bulk-check-all"
                            data-table-id="{{ $tableId ?? 'table' }}">
                    </th>
                    <th style="width: 50px;">Bild</th>
                    <th style="min-width: 240px;">Material & Info</th>
                    <th style="min-width: 150px;">Menge</th>

                    @if($isOrderMode)
                        <th style="min-width: 160px;">Bestellmenge</th>
                        <th style="min-width: 260px;">Bestellung von</th>
                        <th style="min-width: 260px;">Bestelldaten</th>
                        <th style="min-width: 260px;">Lieferung</th>
                        <th class="no-print" style="min-width: 230px;">Notiz & Aktion</th>
                    @else
                        <th style="min-width: 180px;">System-Inventar</th>
                        <th style="min-width: 210px;">Lagerprüfung (Eingabe)</th>
                        <th style="min-width: 150px;">Status & Freigabe</th>
                        <th class="no-print" style="min-width: 190px;">Notiz & Aktion</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $material)
                                @php
                    $itemKey = (string) ($material['item_key'] ?? '');

                    $changeType = array_key_exists($material['change_type'] ?? '', $changeLabels)
                        ? $material['change_type']
                        : 'same';

                    $oldQty = (float) ($material['old_qty'] ?? 0);

                    $newQty = (float) (
                        $material['new_qty']
                        ?? $material['verbrauch_qty']
                        ?? $material['qty']
                        ?? 0
                    );

                    $deltaQty = $newQty - $oldQty;

                    if ($isCompare && $changeType === 'removed') {
                        $requiredQty = 0.0;
                    } else {
                        $requiredQty = (float) (
                            $material['required_qty']
                            ?? ($isCompare ? $newQty : ($material['qty'] ?? $newQty))
                            ?? 0
                        );
                    }

                    $qty = (float) ($material['qty'] ?? $newQty);
                    $unit = $unitOf($material);

                    $foundQtyForStatus = (float) (
                        $material['found_qty']
                        ?? $material['stock_qty']
                        ?? data_get($material, 'stock_allocation.lager')
                        ?? $material['inventory_qty']
                        ?? 0
                    );

                    $savedOrderQty = $material['order_qty']
                        ?? $material['missing_qty']
                        ?? data_get($material, 'stock_allocation.bestellen')
                        ?? null;

                    if ($isCompare && $changeType === 'removed') {
                        $missingQty = 0.0;
                        $stockStatus = $foundQtyForStatus > 0 ? 'lager' : 'unbekannt';
                    } else {
                        $missingQty = $savedOrderQty !== null
                            ? (float) $savedOrderQty
                            : max($requiredQty - $foundQtyForStatus, 0);

                        if ($foundQtyForStatus >= $requiredQty && $requiredQty > 0) {
                            $stockStatus = 'lager';
                        } elseif ($foundQtyForStatus > 0 && $foundQtyForStatus < $requiredQty) {
                            $stockStatus = 'teilweise';
                        } elseif ($requiredQty > 0) {
                            $stockStatus = 'bestellen';
                        } else {
                            $stockStatus = array_key_exists($material['stock_status'] ?? '', $statusLabels)
                                ? $material['stock_status']
                                : 'unbekannt';
                        }
                    }

                    if (!array_key_exists($stockStatus, $statusLabels)) {
                        $stockStatus = 'unbekannt';
                    }

                    $isApproved = (bool) ($material['approved'] ?? false);
                    $reason = trim((string) ($material['delta_reason'] ?? $material['note'] ?? ''));

                    $changeTypeLabel = $changeLabels[$changeType] ?? ucfirst($changeType);

                    $sourceClass = ($source === 'feinaufmass' || $changeType === 'added')
                        ? 'source-feinaufmass'
                        : 'source-offer';

                    $sourceText = match (true) {
                        $changeType === 'added' => 'Nur Feinaufmaß',
                        $changeType === 'removed' => 'Nur Angebot',
                        $isCompare => 'Ang. → Fein.',
                        $source === 'feinaufmass' => 'Feinaufmaß',
                        default => 'Angebot',
                    };

                    $location = $material['location'] ?? $material['location_details'] ?? [];

                    if (!is_array($location)) {
                        $location = [];
                    }

                    $checkedBy = $material['updated_by_data'] ?? $material['checked_by'] ?? null;
                    $checkedName = $material['updated_by_name'] ?? (is_array($checkedBy) ? ($checkedBy['name'] ?? null) : null);
                    $checkedAt = $material['updated_at'] ?? $material['checked_at'] ?? null;
                    $inventoryDetails = collect($material['inventory_details'] ?? []);

                    $orderData = $material['order_data']
                        ?? $material['purchase_order']
                        ?? $material['order_details']
                        ?? [];

                    if (!is_array($orderData)) {
                        $orderData = [];
                    }

                    $orderQty = (float) (
                        $orderData['order_qty']
                        ?? $material['order_qty']
                        ?? $material['missing_qty']
                        ?? data_get($material, 'stock_allocation.bestellen')
                        ?? $material['inventory_missing_qty']
                        ?? $missingQty
                        ?? 0
                    );

                    if ($orderQty <= 0 && $requiredQty > 0) {
                        $orderQty = max($requiredQty - $foundQtyForStatus, 0);
                    }

                    $orderStatusRaw = $orderData['order_status']
                        ?? $orderData['status']
                        ?? $material['purchase_status']
                        ?? $material['order_status']
                        ?? 'open';

                    $orderStatus = in_array($orderStatusRaw, ['ordered', 'bestellt'], true)
                        ? 'ordered'
                        : (in_array($orderStatusRaw, ['delivered', 'geliefert', 'lager'], true)
                            ? 'delivered'
                            : 'open');

                    $orderStatusLabel = match ($orderStatus) {
                        'ordered' => 'Bestellt',
                        'delivered' => 'Geliefert',
                        default => 'Offen',
                    };

                    $orderStatusClass = match ($orderStatus) {
                        'ordered' => 'order-ordered',
                        'delivered' => 'order-delivered',
                        default => 'order-open',
                    };

                    $orderNumber = $orderData['order_no']
                        ?? $orderData['purchase_order_no']
                        ?? $material['order_no']
                        ?? $material['purchase_order_no']
                        ?? '';

                    $sourceType = $orderData['source_type']
                        ?? $material['source_type']
                        ?? (!empty($material['distributor_id']) ? 'distributor' : 'manual');

                    if (!in_array($sourceType, ['distributor', 'brand', 'manual'], true)) {
                        $sourceType = 'manual';
                    }

                    $selectedDistributorId = $orderData['distributor_id']
                        ?? $material['distributor_id']
                        ?? null;

                    $selectedBrandId = $orderData['brand_id']
                        ?? $material['brand_id']
                        ?? null;

                    $manualSourceName = $orderData['manual_source_name']
                        ?? $material['manual_source_name']
                        ?? '';

                    $sourceDisplayName = '—';

                    if ($sourceType === 'distributor') {
                        $selectedDistributor = $distributors->firstWhere('id', (int) $selectedDistributorId);
                        $sourceDisplayName = $selectedDistributor?->name
                            ?? $selectedDistributor?->short_name
                            ?? $orderData['source_name']
                            ?? $material['distributor_name']
                            ?? '—';
                    } elseif ($sourceType === 'brand') {
                        $selectedBrand = $brands->firstWhere('id', (int) $selectedBrandId);
                        $sourceDisplayName = $selectedBrand?->name
                            ?? $orderData['source_name']
                            ?? '—';
                    } else {
                        $sourceDisplayName = $manualSourceName
                            ?: ($orderData['source_name'] ?? '—');
                    }

                    $sourceTypeLabel = match ($sourceType) {
                        'distributor' => 'Distributor',
                        'brand' => 'Hersteller / Marke',
                        default => 'Manuell / Andere',
                    };

                    $orderedEmployeeId = $orderData['ordered_by_employee_id']
                        ?? $orderData['ordered_by']
                        ?? $material['ordered_by_employee_id']
                        ?? $material['ordered_by']
                        ?? null;

                    $orderedEmployeeName = '—';

                    if (!empty($orderedEmployeeId)) {
                        $orderedEmployee = $employees->firstWhere('id', (int) $orderedEmployeeId);

                        if ($orderedEmployee) {
                            $orderedEmployeeName = trim(($orderedEmployee->name ?? '') . ' ' . ($orderedEmployee->lastname ?? '')) ?: ('#' . $orderedEmployee->id);
                        }
                    }

                    $orderedAtRaw = $orderData['ordered_at']
                        ?? $material['ordered_at']
                        ?? null;

                    $deliveryAtRaw = $orderData['expected_delivery_at']
                        ?? $orderData['delivery_at']
                        ?? $orderData['liefertermin']
                        ?? $material['expected_delivery_at']
                        ?? $material['delivery_at']
                        ?? null;

                    $deliveryTarget = $orderData['delivery_target']
                        ?? $orderData['delivery_to']
                        ?? $material['delivery_target']
                        ?? 'company';

                    if (!in_array($deliveryTarget, ['customer', 'company', 'warehouse', 'firma', 'lager', 'kunde'], true)) {
                        $deliveryTarget = 'company';
                    }

                    $deliveryTargetForInput = match ($deliveryTarget) {
                        'customer', 'kunde' => 'customer',
                        default => 'company',
                    };

                    $deliveryTargetLabel = match ($deliveryTargetForInput) {
                        'customer' => 'Kunde / Baustelle',
                        default => 'Firma / Lager',
                    };

                    $deliveryAddress = $orderData['delivery_address']
                        ?? $material['delivery_address']
                        ?? $material['customer_address']
                        ?? '';

                    $orderNote = $orderData['note']
                        ?? $material['note']
                        ?? $material['lager_note']
                        ?? $reason
                        ?? '';
                                @endphp

                                <tr data-item-key="{{ $itemKey }}">
                                    <td class="no-print">
                                        <input type="checkbox" class="material-check js-bulk-row-check" data-item-key="{{ $itemKey }}">
                                    </td>

                                    <td>
                                        @if(!empty($material['img']))
                                            <img src="{{ $material['img'] }}" alt="Material Image" class="material-img">
                                        @else
                                            <div class="material-img d-flex align-items-center justify-content-center"
                                                style="background: var(--mat-bg);">
                                                <span style="color: var(--mat-muted); font-size: 16px;">📦</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="material-name">
                                            {{ $material['name'] ?? 'Unbekanntes Material' }}
                                        </div>

                                        <div class="material-subtext">
                                            <span class="cell-label">Art-Nr:</span>
                                            <strong style="color:var(--mat-text);">
                                                {{ $material['article_no'] ?: '—' }}
                                            </strong>
                                            <br>
                                            <span class="cell-label">Lief:</span>
                                            {{ $material['distributor_name'] ?: ($material['supplier_name'] ?: '—') }}
                                        </div>

                                        @if(!empty($material['master_set_name']) || !empty($material['section_title']))
                                            <div class="material-subtext" style="margin-top: 4px;">
                                                @if(!empty($material['master_set_name']))
                                                    <div>
                                                        <span class="cell-label">Set:</span>
                                                        {{ $material['master_set_name'] }}
                                                    </div>
                                                @endif

                                                @if(!empty($material['section_title']))
                                                    <div>
                                                        <span class="cell-label">Abschn:</span>
                                                        {{ $material['section_title'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if($isCompare)
                                            <div class="flex-row-gap" style="margin-top: 6px;">
                                                <span class="material-pill {{ $changeType }}">
                                                    {{ $changeTypeLabel }}
                                                </span>
                                                <span class="material-pill {{ $sourceClass }}">
                                                    {{ $sourceText }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($isCompare)
                                            <div class="qty-row">
                                                <span class="cell-label">Angebot:</span>
                                                <span class="material-qty-old">{{ $formatQty($oldQty) }}</span>
                                            </div>

                                            <div class="qty-row">
                                                <span class="cell-label">Feinaufmaß:</span>
                                                <span class="material-qty-new">{{ $formatQty($newQty) }} {{ $unit }}</span>
                                            </div>

                                            <div class="qty-row"
                                                style="border-top: 1px solid var(--mat-border); padding-top: 4px; margin-top: 2px;">
                                                <span class="cell-label">Diff:</span>

                                                @if($deltaQty > 0)
                                                    <span class="material-qty-plus">+{{ $formatQty($deltaQty) }}</span>
                                                @elseif($deltaQty < 0)
                                                    <span class="material-qty-minus">{{ $formatQty($deltaQty) }}</span>
                                                @else
                                                    <span class="material-pill same" style="padding: 0 4px;">0</span>
                                                @endif
                                            </div>
                                        @else
                                            <div style="font-weight: 700; font-size: 13px;">
                                                {{ $formatQty($qty) }} {{ $unit }}
                                            </div>
                                        @endif
                                    </td>

                                    @if($isOrderMode)
                                        <td>
                                            <div class="order-read-panel stack-gap">
                                                <div class="order-display-box">
                                                    <div class="order-display-line">
                                                        <span class="cell-label">Zu bestellen:</span>
                                                        <strong>{{ $formatQty($orderQty) }} {{ $unit }}</strong>
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Status:</span>
                                                        <span class="material-pill {{ $orderStatusClass }}">
                                                            {{ $orderStatusLabel }}
                                                        </span>
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Im Lager:</span>
                                                        {{ $formatQty($foundQtyForStatus) }} {{ $unit }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="order-edit-panel stack-gap">
                                                <div>
                                                    <label class="cell-label">Zu bestellen</label>
                                                    <input type="number" step="0.01" min="0" class="order-input js-order-detail-qty"
                                                        value="{{ $orderQty }}" data-item-key="{{ $itemKey }}">
                                                </div>

                                                <div>
                                                    <label class="cell-label">Bestellstatus</label>
                                                    <select class="order-select js-order-detail-status" data-item-key="{{ $itemKey }}">
                                                        <option value="open" {{ $orderStatus === 'open' ? 'selected' : '' }}>Offen</option>
                                                        <option value="ordered" {{ $orderStatus === 'ordered' ? 'selected' : '' }}>Bestellt
                                                        </option>
                                                        <option value="delivered" {{ $orderStatus === 'delivered' ? 'selected' : '' }}>Geliefert
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <input type="hidden" class="js-required-qty" value="{{ $requiredQty }}"
                                                data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-found-qty" value="{{ $foundQtyForStatus }}"
                                                data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-found-unit" value="{{ $unit }}" data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-stock-status" value="{{ $stockStatus }}"
                                                data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-stock-qty" value="{{ $foundQtyForStatus }}"
                                                data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-order-qty" value="{{ $orderQty }}" data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-current-lager-qty" value="{{ $foundQtyForStatus }}"
                                                data-item-key="{{ $itemKey }}">
                                            <input type="hidden" class="js-current-order-qty" value="{{ $orderQty }}"
                                                data-item-key="{{ $itemKey }}">
                                        </td>

                                        <td>
                                            <div class="order-read-panel stack-gap">
                                                <div class="order-display-box">
                                                    <div class="order-display-line">
                                                        <span class="cell-label">Quelle:</span>
                                                        <strong>{{ $sourceTypeLabel }}</strong>
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Name:</span>
                                                        {{ $sourceDisplayName }}
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Bestell-Nr.:</span>
                                                        {{ $orderNumber ?: '—' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="order-edit-panel stack-gap">
                                                <div>
                                                    <label class="cell-label">Quelle</label>
                                                    <select class="order-select js-order-source-type" data-item-key="{{ $itemKey }}">
                                                        <option value="distributor" {{ $sourceType === 'distributor' ? 'selected' : '' }}>
                                                            Distributor</option>
                                                        <option value="brand" {{ $sourceType === 'brand' ? 'selected' : '' }}>Hersteller / Marke
                                                        </option>
                                                        <option value="manual" {{ $sourceType === 'manual' ? 'selected' : '' }}>Manuell / Andere
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="js-order-distributor-box" data-item-key="{{ $itemKey }}"
                                                    style="{{ $sourceType === 'distributor' ? '' : 'display:none;' }}">
                                                    <label class="cell-label">Distributor</label>
                                                    <select class="order-select js-order-distributor-id" data-item-key="{{ $itemKey }}">
                                                        <option value="">Bitte wählen</option>
                                                        @foreach($distributors as $distributor)
                                                            <option value="{{ $distributor->id }}" {{ (string) $selectedDistributorId === (string) $distributor->id ? 'selected' : '' }}>
                                                                {{ $distributor->name ?? $distributor->short_name ?? ('#' . $distributor->id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="js-order-brand-box" data-item-key="{{ $itemKey }}"
                                                    style="{{ $sourceType === 'brand' ? '' : 'display:none;' }}">
                                                    <label class="cell-label">Hersteller / Marke</label>
                                                    <select class="order-select js-order-brand-id" data-item-key="{{ $itemKey }}">
                                                        <option value="">Bitte wählen</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ (string) $selectedBrandId === (string) $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->name ?? ('#' . $brand->id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="js-order-manual-box" data-item-key="{{ $itemKey }}"
                                                    style="{{ $sourceType === 'manual' ? '' : 'display:none;' }}">
                                                    <label class="cell-label">Name / Firma</label>
                                                    <input type="text" class="order-input js-order-manual-source"
                                                        value="{{ $manualSourceName }}" data-item-key="{{ $itemKey }}"
                                                        placeholder="z.B. Bauhaus, Amazon, anderer Lieferant">
                                                </div>

                                                <div>
                                                    <label class="cell-label">Bestell-Nr.</label>
                                                    <input type="text" class="order-input js-order-no" value="{{ $orderNumber }}"
                                                        data-item-key="{{ $itemKey }}" placeholder="Bestellnummer">
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="order-read-panel stack-gap">
                                                <div class="order-display-box">
                                                    <div class="order-display-line">
                                                        <span class="cell-label">Bestellt durch:</span>
                                                        {{ $orderedEmployeeName }}
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Bestellt am:</span>
                                                        {{ $dateTimeFormat($orderedAtRaw) }}
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Liefertermin:</span>
                                                        {{ $dateTimeFormat($deliveryAtRaw, 'd.m.Y') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="order-edit-panel stack-gap">
                                                <div>
                                                    <label class="cell-label">Bestellt durch</label>
                                                    <select class="order-select js-order-employee-id" data-item-key="{{ $itemKey }}">
                                                        <option value="">Bitte wählen</option>
                                                        @foreach($employees as $employee)
                                                            @php
                            $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
                                                            @endphp
                                                            <option value="{{ $employee->id }}" {{ (string) $orderedEmployeeId === (string) $employee->id ? 'selected' : '' }}>
                                                                {{ $employeeName ?: ('#' . $employee->id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="cell-label">Bestellt am</label>
                                                    <input type="datetime-local" class="order-input js-order-ordered-at"
                                                        value="{{ $dateTimeValue($orderedAtRaw) }}" data-item-key="{{ $itemKey }}">
                                                </div>

                                                <div>
                                                    <label class="cell-label">Liefertermin</label>
                                                    <input type="date" class="order-input js-order-delivery-at"
                                                        value="{{ $dateValue($deliveryAtRaw) }}" data-item-key="{{ $itemKey }}">
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="order-read-panel stack-gap">
                                                <div class="order-display-box">
                                                    <div class="order-display-line">
                                                        <span class="cell-label">Lieferung an:</span>
                                                        {{ $deliveryTargetLabel }}
                                                    </div>

                                                    <div class="order-display-line">
                                                        <span class="cell-label">Adresse:</span>
                                                        {{ $deliveryAddress ?: '—' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="order-edit-panel stack-gap">
                                                <div>
                                                    <label class="cell-label">Lieferung an</label>
                                                    <select class="order-select js-order-delivery-target" data-item-key="{{ $itemKey }}">
                                                        <option value="company" {{ $deliveryTargetForInput === 'company' ? 'selected' : '' }}>
                                                            Firma / Lager</option>
                                                        <option value="customer" {{ $deliveryTargetForInput === 'customer' ? 'selected' : '' }}>
                                                            Kunde / Baustelle</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="cell-label">Lieferadresse</label>
                                                    <textarea class="order-textarea js-order-delivery-address" data-item-key="{{ $itemKey }}"
                                                        placeholder="Lieferadresse eingeben...">{{ $deliveryAddress }}</textarea>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="no-print">
                                            <div class="stack-gap">
                                                @if($isCompare && $reason)
                                                    <div class="material-subtext"
                                                        style="background: var(--mat-warning-soft); padding: 4px; border-radius: 4px;">
                                                        <strong>Grund:</strong> {{ $reason }}
                                                    </div>
                                                @endif

                                                <div class="order-read-panel">
                                                    @if($orderNote)
                                                        <div class="order-display-box">
                                                            <div class="order-display-line">
                                                                <span class="cell-label">Notiz:</span>
                                                                {{ $orderNote }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="order-display-muted">Keine Bestellnotiz.</div>
                                                    @endif
                                                </div>

                                                <div class="order-edit-panel stack-gap">
                                                    <textarea class="material-note js-note" data-item-key="{{ $itemKey }}"
                                                        placeholder="Bestellnotiz eingeben...">{{ $orderNote }}</textarea>
                                                </div>

                                                <input type="hidden" class="js-location-label" value="{{ $location['location_label'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-room-name" value="{{ $location['room_name'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-room-number" value="{{ $location['room_number'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-rack-name" value="{{ $location['rack_name'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-row" value="{{ $location['row'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-column" value="{{ $location['column'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">
                                                <input type="hidden" class="js-shelf" value="{{ $location['shelf'] ?? '' }}"
                                                    data-item-key="{{ $itemKey }}">

                                                <div class="order-action-row">
                                                    <button type="button" class="order-mini-btn js-edit-order-row"
                                                        data-item-key="{{ $itemKey }}">
                                                        Edit
                                                    </button>

                                                    <button type="button" class="order-mini-btn ok js-save-order-details"
                                                        data-item-key="{{ $itemKey }}" style="display:none;">
                                                        OK
                                                    </button>

                                                    <button type="button" class="order-mini-btn cancel js-cancel-order-edit"
                                                        data-item-key="{{ $itemKey }}" style="display:none;">
                                                        Cancel
                                                    </button>
                                                </div>

                                                <button type="button" class="material-btn-soft js-move-allocation" data-action="found_in_lager"
                                                    data-item-key="{{ $itemKey }}"
                                                    style="width: 100%; justify-content: center; height: 32px; border-color:#10b981; color:#047857;">
                                                    Als geliefert / Im Lager speichern
                                                </button>

                                                <button type="button" class="material-btn-soft js-move-allocation"
                                                    data-action="reset_allocation" data-item-key="{{ $itemKey }}"
                                                    style="width: 100%; justify-content: center; height: 32px;">
                                                    Bestellung zurücksetzen
                                                </button>
                                            </div>
                                        </td>
                                    @else
                                        <td>
                                            @if($inventoryDetails->count())
                                                <div class="stack-gap">
                                                    @foreach($inventoryDetails as $detail)
                                                        <div
                                                            style="border:1px solid var(--mat-border); border-radius:4px; padding:6px; background:#f8fafc;">
                                                            <div style="font-weight:600; color:#059669; font-size:11px;">
                                                                {{ $formatQty($detail['quantity'] ?? 0) }} {{ $unit }}
                                                            </div>

                                                            <div class="material-subtext">
                                                                {{ $detail['location_label'] ?? 'Kein Ort' }}
                                                                <br>
                                                                <span style="font-size:10px;">
                                                                    ({{ $detail['rack_name'] ?? '-' }}/{{ $detail['row'] ?? '-' }}/{{ $detail['shelf'] ?? '-' }})
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="material-subtext">Kein Inventar hinterlegt</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="stack-gap">
                                                <div style="display:flex; gap:6px;">
                                                    <div style="flex: 2;">
                                                        <div class="cell-label" style="margin-bottom:2px;">Gefunden</div>
                                                        <input type="number" step="0.01" min="0" class="material-small-input js-found-qty"
                                                            value="{{ $foundQtyForStatus > 0 ? $foundQtyForStatus : '' }}"
                                                            data-item-key="{{ $itemKey }}" style="width: 100%;">
                                                    </div>

                                                    <div style="flex: 1;">
                                                        <div class="cell-label" style="margin-bottom:2px;">Einh</div>
                                                        <input type="text" class="material-small-input js-found-unit"
                                                            value="{{ $material['found_unit'] ?? $unit }}" data-item-key="{{ $itemKey }}"
                                                            style="width: 100%;">
                                                    </div>
                                                </div>

                                                <input type="hidden" class="js-required-qty" value="{{ $requiredQty }}"
                                                    data-item-key="{{ $itemKey }}">

                                                <div>
                                                    <div class="cell-label" style="margin-bottom:2px;">Lagerort / Regal / Fach</div>

                                                    <input type="text" class="material-small-input js-location-label"
                                                        value="{{ $location['location_label'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                        placeholder="Ort / Beschreibung" style="width: 100%; margin-bottom: 4px;">

                                                    <div style="display:flex; gap:4px; margin-bottom:4px;">
                                                        <input type="text" class="material-small-input js-room-name"
                                                            value="{{ $location['room_name'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                            placeholder="Raum" style="width: 50%;">

                                                        <input type="text" class="material-small-input js-room-number"
                                                            value="{{ $location['room_number'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                            placeholder="Raum-Nr." style="width: 50%;">
                                                    </div>

                                                    <div style="display:flex; gap:4px;">
                                                        <input type="text" class="material-small-input js-rack-name"
                                                            value="{{ $location['rack_name'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                            placeholder="Reg" style="width: 33%;">

                                                        <input type="text" class="material-small-input js-row"
                                                            value="{{ $location['row'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                            placeholder="Reihe" style="width: 33%;">

                                                        <input type="text" class="material-small-input js-shelf"
                                                            value="{{ $location['shelf'] ?? '' }}" data-item-key="{{ $itemKey }}"
                                                            placeholder="Fach" style="width: 33%;">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="stack-gap">
                                                <div>
                                                    <span class="cell-label">Status:</span><br>
                                                    <span class="material-pill {{ $stockStatus }} js-status-label"
                                                        data-item-key="{{ $itemKey }}">
                                                        {{ $statusLabels[$stockStatus] ?? ucfirst($stockStatus) }}
                                                    </span>

                                                    <input type="hidden" class="js-stock-status" value="{{ $stockStatus }}"
                                                        data-item-key="{{ $itemKey }}">
                                                    <input type="hidden" class="js-stock-qty" value="{{ $foundQtyForStatus }}"
                                                        data-item-key="{{ $itemKey }}">
                                                    <input type="hidden" class="js-current-lager-qty" value="{{ $foundQtyForStatus }}"
                                                        data-item-key="{{ $itemKey }}">
                                                    <input type="hidden" class="js-current-order-qty" value="{{ $missingQty }}"
                                                        data-item-key="{{ $itemKey }}">
                                                </div>

                                                <div>
                                                    <span class="cell-label">Fehlt:</span><br>
                                                    <span class="material-pill {{ $missingQty > 0 ? 'bestellen' : 'lager' }} js-missing-label"
                                                        data-item-key="{{ $itemKey }}">
                                                        {{ $formatQty($missingQty) }} {{ $unit }}
                                                    </span>

                                                    <input type="hidden" class="js-order-qty" value="{{ $missingQty }}"
                                                        data-item-key="{{ $itemKey }}">
                                                </div>

                                                @if($isCompare)
                                                    <div>
                                                        <span class="cell-label">Freigabe:</span><br>
                                                        @if($isApproved)
                                                            <span class="material-pill approved">Ja</span>
                                                        @else
                                                            <span class="material-pill not-approved">Nein</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($checkedName)
                                                    <div style="margin-top: 4px; border-top: 1px solid var(--mat-border); padding-top: 4px;">
                                                        <span class="cell-label">Geprüft von:</span>
                                                        <div style="font-weight: 600; font-size: 11px;">
                                                            {{ $checkedName }}
                                                        </div>

                                                        @if($checkedAt)
                                                            <div class="material-subtext">
                                                                {{ $dateTimeFormat($checkedAt) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="no-print">
                                            <div class="stack-gap">
                                                @if($isCompare && $reason)
                                                    <div class="material-subtext"
                                                        style="background: var(--mat-warning-soft); padding: 4px; border-radius: 4px;">
                                                        <strong>Grund:</strong> {{ $reason }}
                                                    </div>
                                                @endif

                                                <textarea class="material-note js-note" data-item-key="{{ $itemKey }}"
                                                    placeholder="Notiz eingeben...">{{ $material['note'] ?? ($material['lager_note'] ?? $reason) }}</textarea>

                                                <button type="button" class="material-btn-soft js-move-allocation" data-action="found_in_lager"
                                                    data-item-key="{{ $itemKey }}"
                                                    style="width: 100%; justify-content: center; height: 32px; border-color:#10b981; color:#047857;">
                                                    Gefunden / Lager speichern
                                                </button>

                                                <button type="button" class="material-btn-soft js-move-allocation" data-action="move_to_order"
                                                    data-item-key="{{ $itemKey }}"
                                                    style="width: 100%; justify-content: center; height: 32px; border-color:#ef4444; color:#b91c1c;">
                                                    Zur Bestellung
                                                </button>

                                                <button type="button" class="material-btn-soft js-move-allocation"
                                                    data-action="reset_allocation" data-item-key="{{ $itemKey }}"
                                                    style="width: 100%; justify-content: center; height: 32px;">
                                                    Rückgängig / Neu setzen
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 60px 20px; text-align: center; color: var(--mat-muted);">
            <div style="font-size: 24px; margin-bottom: 8px;">📭</div>
            Keine Materialien für diese Ansicht gefunden.
        </div>
    @endif
</div>