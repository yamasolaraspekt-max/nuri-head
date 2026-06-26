@extends('admin.layouts.app')

@section('title')
Unterphasen - {{ $stage->name }}
@stop

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .kss-page {
        padding: 18px;
    }

    .kss-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .kss-title-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .kss-eyebrow {
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .kss-title {
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
    }

    .kss-subtitle {
        font-size: 13px;
        color: #64748b;
    }

    .kss-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .kss-btn {
        border: 1px solid #dbeafe;
        background: #fff;
        color: #334155;
        border-radius: 12px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: .15s ease;
    }

    .kss-btn:hover {
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .kss-btn-primary {
        border-color: #93c21c;
        background: #93c21c;
        color: #fff;
    }

    .kss-btn-primary:hover {
        background: #80ad18;
        color: #fff;
    }

    .kss-btn-danger {
        border-color: #fecaca;
        color: #b91c1c;
    }

    .kss-btn-danger:hover {
        background: #fef2f2;
        color: #991b1b;
    }

    .kss-btn-warning {
        border-color: #fde68a;
        color: #92400e;
    }

    .kss-btn-warning:hover {
        background: #fffbeb;
    }

    .kss-layout {
        display: grid;
        grid-template-columns: 390px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .kss-layout {
            grid-template-columns: 1fr;
        }
    }

    .kss-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .kss-panel-head {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .kss-panel-title {
        margin: 0;
        font-size: 15px;
        font-weight: 900;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .kss-panel-body {
        padding: 16px;
    }

    .kss-form-group {
        margin-bottom: 13px;
    }

    .kss-label {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: #334155;
        margin-bottom: 5px;
    }

    .kss-input,
    .kss-select {
        width: 100%;
        height: 40px;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        padding: 0 11px;
        font-size: 13px;
        color: #0f172a;
        background: #fff;
        outline: none;
    }

    .kss-input:focus,
    .kss-select:focus {
        border-color: #93c21c;
        box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
    }

    .kss-help {
        margin-top: 4px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.4;
    }

    .kss-check-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #334155;
        margin-top: 8px;
    }

    .kss-check-row input {
        width: 16px;
        height: 16px;
    }

    .kss-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .kss-item {
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 15px;
        padding: 12px;
        display: grid;
        grid-template-columns: 34px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        transition: .15s ease;
    }

    .kss-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
    }

    .kss-item.is-inactive {
        opacity: .58;
        background: #f8fafc;
    }

    .kss-sort-handle {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 900;
        cursor: grab;
        user-select: none;
    }

    .kss-item-main {
        min-width: 0;
    }

    .kss-item-title-row {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .kss-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        display: inline-block;
        background: #93c21c;
        box-shadow: 0 0 0 3px rgba(147, 194, 28, .14);
    }

    .kss-item-title {
        font-size: 14px;
        font-weight: 900;
        color: #0f172a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .kss-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 3px 8px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .kss-badge-default {
        background: #ecfccb;
        color: #3f6212;
    }

    .kss-badge-active {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .kss-badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .kss-item-meta {
        margin-top: 4px;
        font-size: 11px;
        color: #64748b;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .kss-item-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        flex-wrap: wrap;
    }

    .kss-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #334155;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        transition: .15s ease;
    }

    .kss-icon-btn:hover {
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .kss-icon-danger {
        color: #b91c1c;
        border-color: #fecaca;
    }

    .kss-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        padding: 28px;
        text-align: center;
        color: #64748b;
        font-weight: 800;
    }

    .kss-alert {
        border-radius: 14px;
        padding: 11px 13px;
        margin-bottom: 14px;
        font-size: 13px;
        font-weight: 800;
    }

    .kss-alert-success {
        background: #ecfccb;
        color: #3f6212;
        border: 1px solid #bef264;
    }

    .kss-alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .kss-error-list {
        margin: 0 0 14px 0;
        padding: 11px 14px 11px 30px;
        border-radius: 14px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 13px;
        font-weight: 800;
    }

    .kss-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .45);
        z-index: 1090;
        display: none;
    }

    .kss-modal-backdrop.show {
        display: block;
    }

    .kss-modal {
        position: fixed;
        inset: 0;
        z-index: 1091;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .kss-modal.show {
        display: flex;
    }

    .kss-modal-card {
        width: 520px;
        max-width: 96vw;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 22px 70px rgba(15, 23, 42, .28);
        overflow: hidden;
    }

    .kss-modal-head {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .kss-modal-title {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: #0f172a;
    }

    .kss-modal-body {
        padding: 16px;
    }

    .kss-modal-foot {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }
</style>
@stop

@section('content')
<div class="kss-page">
    <div class="kss-header">
        <div class="kss-title-wrap">
            <div class="kss-eyebrow">Kanban Einstellungen</div>
            <h1 class="kss-title">Unterphasen für: {{ $stage->name }}</h1>
            <div class="kss-subtitle">
                Jede Unterphase gehört nur zu dieser Hauptphase.
                Beispiel: Angebot → Angebot versendet.
            </div>
        </div>

        <div class="kss-actions">
            <a href="{{ url()->previous() }}" class="kss-btn">
                <i class="feather icon-arrow-left"></i>
                Zurück
            </a>

            <button type="button" class="kss-btn kss-btn-primary" onclick="openCreateModal()">
                <i class="feather icon-plus"></i>
                Neue Unterphase
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="kss-alert kss-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="kss-alert kss-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <ul class="kss-error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <div class="kss-layout">
        <div class="kss-panel">
            <div class="kss-panel-head">
                <h3 class="kss-panel-title">
                    <i class="feather icon-plus-circle"></i>
                    Schnell erstellen
                </h3>
            </div>

            <div class="kss-panel-body">
                <form method="POST" action="{{ route('admin.kanban.stages.sub-stages.store', $stage->id) }}">
                    @csrf

                    <div class="kss-form-group">
                        <label class="kss-label">Name</label>
                        <input type="text" name="name" class="kss-input" value="{{ old('name') }}"
                            placeholder="z.B. Angebot versendet" required>
                    </div>

                    <div class="kss-form-group">
                        <label class="kss-label">Key</label>
                        <input type="text" name="key" class="kss-input" value="{{ old('key') }}"
                            placeholder="optional, wird automatisch erzeugt">
                        <div class="kss-help">
                            Beispiel: angebot_versendet. Leer lassen ist okay.
                        </div>
                    </div>

                    <div class="kss-form-group">
                        <label class="kss-label">Farbe</label>
                        <input type="text" name="color" class="kss-input" value="{{ old('color', '#93c21c') }}"
                            placeholder="#93c21c">
                    </div>

                    <div class="kss-form-group">
                        <label class="kss-label">Icon</label>
                        <input type="text" name="icon" class="kss-input" value="{{ old('icon', 'list') }}"
                            placeholder="list">
                        <div class="kss-help">
                            Lucide/Feather Name ohne Prefix, z.B. list, send, check-circle.
                        </div>
                    </div>

                    <div class="kss-form-group">
                        <label class="kss-label">Sortierung</label>
                        <input type="number" name="sort_order" class="kss-input" value="{{ old('sort_order', 10) }}"
                            min="0">
                    </div>

                    <label class="kss-check-row">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        Als Standard-Unterphase verwenden
                    </label>

                    <label class="kss-check-row">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        Aktiv
                    </label>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="kss-btn kss-btn-primary" style="width: 100%;">
                            <i class="feather icon-save"></i>
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="kss-panel">
            <div class="kss-panel-head">
                <h3 class="kss-panel-title">
                    <i class="feather icon-list"></i>
                    Unterphasen
                </h3>

                <button type="button" class="kss-btn" onclick="saveOrder()">
                    <i class="feather icon-save"></i>
                    Reihenfolge speichern
                </button>
            </div>

            <div class="kss-panel-body">
                @if($subStages->count())
                    <div class="kss-list" id="subStageList">
                        @foreach($subStages as $subStage)
                            <div class="kss-item {{ $subStage->is_active ? '' : 'is-inactive' }}" data-id="{{ $subStage->id }}">
                                <div class="kss-sort-handle" title="Sortieren">
                                    <i class="feather icon-menu"></i>
                                </div>

                                <div class="kss-item-main">
                                    <div class="kss-item-title-row">
                                        <span class="kss-color-dot"
                                            style="background: {{ $subStage->color ?: '#93c21c' }}"></span>

                                        <span class="kss-item-title">
                                            {{ $subStage->name }}
                                        </span>

                                        @if($subStage->is_default)
                                            <span class="kss-badge kss-badge-default">
                                                Standard
                                            </span>
                                        @endif

                                        @if($subStage->is_active)
                                            <span class="kss-badge kss-badge-active">
                                                Aktiv
                                            </span>
                                        @else
                                            <span class="kss-badge kss-badge-inactive">
                                                Inaktiv
                                            </span>
                                        @endif
                                    </div>

                                    <div class="kss-item-meta">
                                        <span>Key: {{ $subStage->key }}</span>
                                        <span>Icon: {{ $subStage->icon ?: '-' }}</span>
                                        <span>Sort: {{ $subStage->sort_order }}</span>
                                    </div>
                                </div>

                                <div class="kss-item-actions">
                                    <button type="button" class="kss-icon-btn" title="Bearbeiten"
                                        onclick='openEditModal(@json($subStage))'>
                                        <i class="feather icon-edit-2"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.kanban.sub-stages.default', $subStage->id) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="kss-icon-btn" title="Als Standard setzen">
                                            <i class="feather icon-star"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.kanban.sub-stages.toggle', $subStage->id) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="kss-icon-btn kss-btn-warning"
                                            title="{{ $subStage->is_active ? 'Deaktivieren' : 'Aktivieren' }}">
                                            @if($subStage->is_active)
                                                <i class="feather icon-eye-off"></i>
                                            @else
                                                <i class="feather icon-eye"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.kanban.sub-stages.destroy', $subStage->id) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Diese Unterphase wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="kss-icon-btn kss-icon-danger" title="Löschen">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="kss-empty">
                        Noch keine Unterphasen vorhanden.
                        <br>
                        Erstelle die erste Unterphase für {{ $stage->name }}.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="kss-modal-backdrop" id="kssModalBackdrop" onclick="closeModal()"></div>

<div class="kss-modal" id="kssModal">
    <div class="kss-modal-card">
        <div class="kss-modal-head">
            <h3 class="kss-modal-title" id="kssModalTitle">Unterphase bearbeiten</h3>

            <button type="button" class="kss-icon-btn" onclick="closeModal()">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <form method="POST" id="kssModalForm">
            @csrf
            <input type="hidden" name="_method" id="kssModalMethod" value="POST">

            <div class="kss-modal-body">
                <div class="kss-form-group">
                    <label class="kss-label">Name</label>
                    <input type="text" name="name" id="modalName" class="kss-input" required>
                </div>

                <div class="kss-form-group">
                    <label class="kss-label">Key</label>
                    <input type="text" name="key" id="modalKey" class="kss-input">
                </div>

                <div class="kss-form-group">
                    <label class="kss-label">Farbe</label>
                    <input type="text" name="color" id="modalColor" class="kss-input">
                </div>

                <div class="kss-form-group">
                    <label class="kss-label">Icon</label>
                    <input type="text" name="icon" id="modalIcon" class="kss-input">
                </div>

                <div class="kss-form-group">
                    <label class="kss-label">Sortierung</label>
                    <input type="number" name="sort_order" id="modalSortOrder" class="kss-input" min="0">
                </div>

                <label class="kss-check-row">
                    <input type="checkbox" name="is_default" id="modalIsDefault" value="1">
                    Als Standard-Unterphase verwenden
                </label>

                <label class="kss-check-row">
                    <input type="checkbox" name="is_active" id="modalIsActive" value="1">
                    Aktiv
                </label>
            </div>

            <div class="kss-modal-foot">
                <button type="button" class="kss-btn" onclick="closeModal()">
                    Abbrechen
                </button>

                <button type="submit" class="kss-btn kss-btn-primary">
                    <i class="feather icon-save"></i>
                    Speichern
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('script')
<script>
    const KSS = {
        stageId: @json($stage->id),
        storeUrl: @json(route('admin.kanban.stages.sub-stages.store', $stage->id)),
        reorderUrl: @json(route('admin.kanban.stages.sub-stages.reorder', $stage->id)),
        updateUrlTemplate: @json(route('admin.kanban.sub-stages.update', ['subStage' => '__ID__'])),
        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    };

    function refreshFeather() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    }

    function openCreateModal() {
        const form = document.getElementById('kssModalForm');

        document.getElementById('kssModalTitle').textContent = 'Neue Unterphase';
        form.action = KSS.storeUrl;
        document.getElementById('kssModalMethod').value = 'POST';

        document.getElementById('modalName').value = '';
        document.getElementById('modalKey').value = '';
        document.getElementById('modalColor').value = '#93c21c';
        document.getElementById('modalIcon').value = 'list';
        document.getElementById('modalSortOrder').value = '10';
        document.getElementById('modalIsDefault').checked = false;
        document.getElementById('modalIsActive').checked = true;

        showModal();
    }

    function openEditModal(subStage) {
        const form = document.getElementById('kssModalForm');

        document.getElementById('kssModalTitle').textContent = 'Unterphase bearbeiten';
        form.action = KSS.updateUrlTemplate.replace('__ID__', subStage.id);
        document.getElementById('kssModalMethod').value = 'PUT';

        document.getElementById('modalName').value = subStage.name || '';
        document.getElementById('modalKey').value = subStage.key || '';
        document.getElementById('modalColor').value = subStage.color || '#93c21c';
        document.getElementById('modalIcon').value = subStage.icon || 'list';
        document.getElementById('modalSortOrder').value = subStage.sort_order || 10;
        document.getElementById('modalIsDefault').checked = !!subStage.is_default;
        document.getElementById('modalIsActive').checked = !!subStage.is_active;

        showModal();
    }

    function showModal() {
        document.getElementById('kssModalBackdrop').classList.add('show');
        document.getElementById('kssModal').classList.add('show');

        setTimeout(() => {
            document.getElementById('modalName')?.focus();
        }, 80);

        refreshFeather();
    }

    function closeModal() {
        document.getElementById('kssModalBackdrop').classList.remove('show');
        document.getElementById('kssModal').classList.remove('show');
    }

    function saveOrder() {
        const list = document.getElementById('subStageList');

        if (!list) {
            return;
        }

        const items = Array.from(list.querySelectorAll('.kss-item')).map((item, index) => {
            return {
                id: Number(item.dataset.id),
                sort_order: (index + 1) * 10
            };
        });

        fetch(KSS.reorderUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': KSS.csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items })
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Reihenfolge konnte nicht gespeichert werden.');
                }

                alert(data.message || 'Reihenfolge wurde gespeichert.');
                window.location.reload();
            })
            .catch(error => {
                alert(error.message || 'Fehler beim Speichern.');
            });
    }

    function bootSimpleDragSort() {
        const list = document.getElementById('subStageList');

        if (!list) {
            return;
        }

        let dragged = null;

        list.querySelectorAll('.kss-item').forEach(item => {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function () {
                dragged = item;
                item.style.opacity = '0.45';
            });

            item.addEventListener('dragend', function () {
                dragged = null;
                item.style.opacity = '';
            });

            item.addEventListener('dragover', function (event) {
                event.preventDefault();

                if (!dragged || dragged === item) {
                    return;
                }

                const rect = item.getBoundingClientRect();
                const next = (event.clientY - rect.top) > rect.height / 2;

                list.insertBefore(dragged, next ? item.nextSibling : item);
            });
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        bootSimpleDragSort();
        refreshFeather();
    });
</script>
@stop