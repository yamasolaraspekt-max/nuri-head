@php
    $workflowStages = collect($dealWorkflowStages ?? []);

    if ($workflowStages->isEmpty()) {
        $workflowStages = collect([
            (object) ['key' => 'open', 'label' => 'Offen', 'name' => 'Offen', 'color' => '#f59e0b', 'is_default' => true],
            (object) ['key' => 'confirm', 'label' => 'Bestätigt', 'name' => 'Bestätigt', 'color' => '#10b981', 'is_default' => false],
            (object) ['key' => 'inconfirm', 'label' => 'Unbestätigt', 'name' => 'Unbestätigt', 'color' => '#ef4444', 'is_default' => false],
            (object) ['key' => 'pause', 'label' => 'Pausiert', 'name' => 'Pausiert', 'color' => '#f59e0b', 'is_default' => false],
            (object) ['key' => 'cancel', 'label' => 'Absage', 'name' => 'Absage', 'color' => '#ef4444', 'is_default' => false],
        ]);
    }

    $workflowKeys = $workflowStages->pluck('key')->map(fn($key) => strtolower((string) $key))->values()->all();
    $defaultStage = $workflowStages->firstWhere('is_default', true) ?: $workflowStages->first();
    $defaultKey = strtolower((string) ($defaultStage->key ?? 'open'));

    $normalizeDealKanbanStatus = function ($status) use ($workflowKeys, $defaultKey) {
        $status = strtolower(trim((string) $status));

        if ($status !== '' && in_array($status, $workflowKeys, true)) {
            return $status;
        }

        $aliasGroups = [
            'open' => ['open', 'offen', 'auftrag_erhalten', 'auftragspruefung', 'auftrag_pruefung'],
            'confirm' => ['confirm', 'confirmed', 'bestaetigt', 'bestatigt', 'geprueft', 'gepruft', 'auftrag_bestaetigt'],
            'inconfirm' => ['inconfirm', 'unconfirmed', 'unbestaetigt', 'unbestatigt', 'klaerung', 'daten_fehlen'],
            'pause' => ['pause', 'paused', 'pausiert', 'on_hold', 'warten'],
            'cancel' => ['cancel', 'cancelled', 'storniert', 'abgesagt', 'absage'],
        ];

        $aliases = $aliasGroups[$status] ?? [$status];

        foreach ($aliases as $alias) {
            if (in_array($alias, $workflowKeys, true)) {
                return $alias;
            }
        }

        foreach ($workflowKeys as $key) {
            foreach ($aliases as $alias) {
                if ($alias !== '' && str_contains($key, $alias)) {
                    return $key;
                }
            }
        }

        return $defaultKey;
    };

    $itemsForKanban = $data instanceof \Illuminate\Pagination\AbstractPaginator ? collect($data->items()) : collect($data);
@endphp

<style>
    .deal-kanban-controls {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding: 12px;
        border: 1px solid var(--deal-border, #e5e7eb);
        border-radius: 16px;
        background: #fff;
        margin-bottom: 14px;
    }

    .deal-kanban-control {
        min-width: 190px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .deal-kanban-control.search {
        min-width: 280px;
    }

    .deal-kanban-control label {
        margin: 0;
        font-size: 11px;
        font-weight: 900;
        color: var(--deal-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .deal-kanban-board {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(340px, 1fr);
        gap: 16px;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .deal-kanban-column {
        border: 1px solid var(--deal-border, #e5e7eb);
        border-radius: 22px;
        background: linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
        min-height: 480px;
        overflow: hidden;
        box-shadow: var(--deal-shadow, 0 1px 2px rgba(0, 0, 0, .05));
    }

    .deal-kanban-column-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--deal-border, #e5e7eb);
        background: #fff;
    }

    .deal-kanban-title {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        font-size: 14px;
        font-weight: 900;
        color: #111827;
    }

    .deal-kanban-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        flex: 0 0 auto;
    }

    .deal-kanban-count {
        min-width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--deal-primary-soft, #f4fae7);
        border: 1px solid #d8ec9d;
        color: #55720d;
        font-size: 12px;
        font-weight: 900;
    }

    .deal-kanban-list {
        min-height: 390px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .deal-kanban-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #fff;
        overflow: hidden;
        cursor: grab;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        transition: all .18s ease;
    }

    .deal-kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .10);
        border-color: #cbd5e1;
    }

    .deal-kanban-card-top {
        padding: 15px 16px 13px;
        border-bottom: 1px solid #eef2f7;
        background:
            radial-gradient(circle at top right, rgba(147, 194, 28, .10), transparent 30%),
            linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
    }

    .deal-kanban-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .deal-kanban-no {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 999px;
        background: #eff6ff;
        color: #74b2d4;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .deal-kanban-customer {
        margin-top: 10px;
        font-size: 16px;
        line-height: 1.25;
        font-weight: 900;
        color: #111827;
    }

    .deal-kanban-sub {
        margin-top: 5px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }

    .deal-kanban-body {
        padding: 14px 16px;
    }

    .deal-kanban-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .deal-kanban-meta-card {
        border: 1px solid #eef2f7;
        border-radius: 14px;
        background: #fafafa;
        padding: 10px;
        min-width: 0;
    }

    .deal-kanban-label {
        font-size: 10px;
        font-weight: 900;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    .deal-kanban-value {
        margin-top: 4px;
        font-size: 13px;
        font-weight: 900;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .deal-kanban-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px 15px;
    }

    .deal-kanban-iconbar {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .deal-kanban-avatar {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        object-fit: cover;
        border: 2px solid #93c21c;
    }
</style>

<div class="deal-kanban-controls">
    <div class="deal-kanban-control search">
        <label>Suche</label>
        <input type="text" id="kanban-search" class="deal-input" placeholder="Kunde, Ort, Produkt, Auftragsnummer...">
    </div>

    <div class="deal-kanban-control">
        <label>Ansicht</label>
        <select id="kanban-filter" class="deal-select">
            <option value="all">Alle Aufträge</option>
            <option value="my">Meine Aufträge</option>
        </select>
    </div>

    <div class="deal-kanban-control">
        <label>Produkt</label>
        <select id="kanban-product" class="deal-select">
            <option value="">Alle Produkte</option>
            @foreach(($products ?? collect()) as $product)
                <option value="{{ $product->id }}">{{ $product->article_group }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="deal-kanban-board" id="kanbanBoard">
    @foreach($workflowStages as $stage)
        @php
            $stageKey = strtolower((string) $stage->key);
            $stageItems = $itemsForKanban->filter(fn($item) => $normalizeDealKanbanStatus($item->status ?? null) === $stageKey);
        @endphp

        <div class="deal-kanban-column kanban-column" data-status="{{ $stageKey }}">
            <div class="deal-kanban-column-head">
                <div class="deal-kanban-title">
                    <span class="deal-kanban-dot" style="background: {{ $stage->color ?? '#93c21c' }}"></span>
                    <span>{{ $stage->label ?? $stage->name ?? $stageKey }}</span>
                </div>
                <span class="deal-kanban-count">{{ $stageItems->count() }}</span>
            </div>

            <div class="deal-kanban-list kanban-list" data-status="{{ $stageKey }}">
                @include('admin.deal.partials.kanban_column', [
                    'data' => $stageItems,
                    'status' => $stageKey,
                    'statusKey' => $stageKey,
                    'dealWorkflowStages' => $workflowStages,
                ])
                    </div>
                </div>
    @endforeach
</div>
