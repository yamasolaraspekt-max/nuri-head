@extends('admin.layouts.app')
@section('title', 'Artikel-Gruppen')

@section('style')
<style>
:root{
    --bg:#f3f4f6;
    --card:#fff;
    --text:#111827;
    --muted:#6b7280;
    --border:#e5e7eb;
    --primary:var(--sa-accent);
    --primary-hover:var(--sa-accent-hover);
    --primary-soft:var(--sa-accent-light);
    --danger:#ef4444;
    --danger-soft:#fef2f2;
    --shadow:0 8px 24px -18px rgba(0,0,0,.35);
}

.ag-wrap { 
    color: var(--text);
    font-family: Inter, system-ui, -apple-system, sans-serif;
}
.ag-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.ag-title{
    font-size:26px;
    font-weight:900;
    letter-spacing:-.03em;
}

.ag-sub{
    color:var(--muted);
    font-size:14px;
    margin-top:4px;
}

.ag-btn{
    border:0;
    background:var(--primary);
    color:#fff;
    border-radius:10px;
    padding:10px 15px;
    font-weight:900;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
}

.ag-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
}

.ag-btn-soft{
    border:1px solid var(--border);
    background:#fff;
    color:var(--text);
    border-radius:10px;
    padding:10px 14px;
    font-weight:800;
    cursor:pointer;
    text-decoration:none;
}

.ag-btn-soft:hover{
    background:#f9fafb;
    color:var(--text);
    text-decoration:none;
}

.ag-icon-btn{
    width:36px;
    height:36px;
    border-radius:9px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    color:var(--muted);
}

.ag-icon-btn.edit{
    background:var(--primary-soft);
    color:var(--primary);
    border-color:#d8edaa;
}

.ag-icon-btn.danger{
    background:var(--danger-soft);
    color:var(--danger);
    border-color:#fecaca;
}

.ag-stats{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
}

.ag-stat{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow);
}

.ag-stat-label{
    font-size:11px;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:.07em;
    font-weight:900;
}

.ag-stat-value{
    font-size:24px;
    font-weight:900;
    margin-top:5px;
}

.ag-toolbar{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:14px;
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:end;
    margin-bottom:18px;
}

.ag-field{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.ag-field.search{
    flex:1;
    min-width:280px;
}

.ag-label{
    font-size:11px;
    color:var(--muted);
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.ag-input{
    width:100%;
    border:1px solid var(--border);
    background:#f9fafb;
    border-radius:10px;
    padding:10px 12px;
    outline:none;
    font-size:14px;
}

.ag-input:focus{
    border-color:var(--primary);
    background:#fff;
    box-shadow:0 0 0 3px var(--primary-soft);
}

.ag-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:18px;
    overflow:hidden;
    box-shadow:var(--shadow);
}

.ag-table-wrap{
    overflow:auto;
}

.ag-table{
    width:100%;
    min-width:1000px;
    border-collapse:collapse;
}

.ag-table th,
.ag-table td{
    padding:13px 14px;
    border-bottom:1px solid var(--border);
    vertical-align:middle;
}

.ag-table th{
    background:#fafafa;
    color:var(--muted);
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.06em;
    font-weight:900;
}

.ag-table th a{
    color:var(--muted);
    text-decoration:none;
}

.ag-table th a.active{
    color:var(--primary);
}

.ag-avatar{
    width:46px;
    height:46px;
    border-radius:12px;
    border:1px solid var(--border);
    background:var(--primary-soft);
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    overflow:hidden;
}

.ag-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.ag-name{
    font-weight:900;
}

.ag-muted{
    color:var(--muted);
    font-size:12px;
    margin-top:3px;
}

.ag-actions{
    display:flex;
    justify-content:flex-end;
    gap:7px;
}

.ag-sub-row{
    display:none;
    background:#f9fafb;
}

.ag-sub-row.open{
    display:table-row;
}

.ag-sub-box{
    padding:16px;
}

.ag-sub-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:12px;
}

.ag-sub-table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    overflow:hidden;
}

.ag-sub-table th,
.ag-sub-table td{
    padding:10px;
    border-bottom:1px solid var(--border);
    font-size:13px;
}

.ag-inline-form{
    display:grid;
    grid-template-columns:1fr 1.5fr 1fr 1fr auto;
    gap:10px;
    align-items:end;
}

.ag-empty{
    padding:50px;
    text-align:center;
    color:var(--muted);
}

.ag-pagination{
    margin-top:16px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px;
}

.ag-modal-backdrop{
    position:fixed;
    inset:0;
    background:rgba(17,24,39,.58);
    backdrop-filter:blur(3px);
    z-index:2000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
}

.ag-modal-backdrop.open{
    display:flex;
}

.ag-modal{
    width:100%;
    max-width:720px;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.ag-modal-head,
.ag-modal-foot{
    padding:16px 18px;
    background:#fafafa;
    border-bottom:1px solid var(--border);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
}

.ag-modal-foot{
    border-top:1px solid var(--border);
    border-bottom:0;
    justify-content:flex-end;
}

.ag-modal-title{
    font-size:16px;
    font-weight:900;
    margin:0;
}

.ag-modal-body{
    padding:18px;
    max-height:70vh;
    overflow:auto;
}

.ag-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:15px;
}

.ag-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:3000;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.ag-toast{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px 14px;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
    min-width:280px;
}

@media(max-width:1000px){
    .ag-stats{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media(max-width:700px){
    .ag-wrap{
        margin-top:90px;
        padding:0 16px;
    }

    .ag-stats{
        grid-template-columns:1fr;
    }

    .ag-grid,
    .ag-inline-form{
        grid-template-columns:1fr;
    }
}
</style>
@endsection

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
$items = collect($isPaginator ? $data->items() : $data);

$stats = $stats ?? [
    'total' => $isPaginator ? $data->total() : $items->count(),
    'with_image' => $items->whereNotNull('image')->count(),
    'with_range' => $items->filter(fn($item) => !is_null($item->min_value) || !is_null($item->max_value))->count(),
    'sub_total' => $items->sum(fn($item) => $item->subArticleGroups->count()),
];

$currentSort = request('sort', $sort ?? 'article_group');
$currentDirection = request('direction', $direction ?? 'asc');
@endphp

@section('content')
<div class="ag-wrap">

    <div class="ag-head">
        <div>
            <div class="ag-title">ARTIKEL-GRUPPEN</div>
            <div class="ag-sub">Gruppen, Wertebereiche und Sub-Artikelgruppen verwalten.</div>
        </div>

        <button type="button" class="ag-btn" onclick="openModal('createGroupModal')">
            + Neue Gruppe
        </button>
    </div>

    <div class="ag-stats">
        <div class="ag-stat">
            <div class="ag-stat-label">Gesamt</div>
            <div class="ag-stat-value">{{ $stats['total'] }}</div>
        </div>

        <div class="ag-stat">
            <div class="ag-stat-label">Mit Bild</div>
            <div class="ag-stat-value">{{ $stats['with_image'] }}</div>
        </div>

        <div class="ag-stat">
            <div class="ag-stat-label">Mit Bereich</div>
            <div class="ag-stat-value">{{ $stats['with_range'] }}</div>
        </div>

        <div class="ag-stat">
            <div class="ag-stat-label">Sub-Gruppen</div>
            <div class="ag-stat-value">{{ $stats['sub_total'] }}</div>
        </div>
    </div>

    <form id="filterForm" class="ag-toolbar" action="{{ route('article_group.index') }}" method="GET">
        <input type="hidden" name="sort" id="sortInput" value="{{ $currentSort }}">
        <input type="hidden" name="direction" id="directionInput" value="{{ $currentDirection }}">

        <div class="ag-field search">
            <label class="ag-label">Suche</label>
            <input type="text" class="ag-input" name="search" value="{{ request('search') }}" placeholder="Gruppe, Sub-Gruppe, Initial oder Wert">
        </div>

        <div class="ag-field">
            <label class="ag-label">Min-Wert ≥</label>
            <input type="number" step="0.01" class="ag-input" name="min_value" value="{{ request('min_value') }}">
        </div>

        <div class="ag-field">
            <label class="ag-label">Max-Wert ≤</label>
            <input type="number" step="0.01" class="ag-input" name="max_value" value="{{ request('max_value') }}">
        </div>

        <button type="submit" class="ag-btn-soft">Anwenden</button>
        <a href="{{ route('article_group.index') }}" class="ag-btn-soft">Zurücksetzen</a>
    </form>

    <div class="ag-card">
        @if($items->isEmpty())
            <div class="ag-empty">Keine Artikel-Gruppen gefunden.</div>
        @else
            <div class="ag-table-wrap">
                <table class="ag-table">
                    <thead>
                        <tr>
                            <th style="width:55px;"></th>
                            <th style="width:80px;">ID</th>
                            <th style="width:90px;">Foto</th>
                            <th style="width:120px;">
                                <a href="#" class="js-sort-link {{ $currentSort === 'initial' ? 'active' : '' }}" data-sort="initial">
                                    Initial {!! $currentSort === 'initial' ? ($currentDirection === 'asc' ? '↑' : '↓') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="#" class="js-sort-link {{ $currentSort === 'article_group' ? 'active' : '' }}" data-sort="article_group">
                                    Artikel-Gruppe {!! $currentSort === 'article_group' ? ($currentDirection === 'asc' ? '↑' : '↓') : '' !!}
                                </a>
                            </th>
                            <th style="text-align:right;width:130px;">
                                <a href="#" class="js-sort-link {{ $currentSort === 'min_value' ? 'active' : '' }}" data-sort="min_value">
                                    Min {!! $currentSort === 'min_value' ? ($currentDirection === 'asc' ? '↑' : '↓') : '' !!}
                                </a>
                            </th>
                            <th style="text-align:right;width:130px;">
                                <a href="#" class="js-sort-link {{ $currentSort === 'max_value' ? 'active' : '' }}" data-sort="max_value">
                                    Max {!! $currentSort === 'max_value' ? ($currentDirection === 'asc' ? '↑' : '↓') : '' !!}
                                </a>
                            </th>
                            <th style="text-align:right;width:150px;">Aktion</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($data as $group)
                            <tr>
                                <td>
                                    <button type="button" class="ag-icon-btn js-toggle-sub" data-target="sub-row-{{ $group->id }}">
                                        ↓
                                    </button>
                                </td>

                                <td>{{ $group->id }}</td>

                                <td>
                                    <div class="ag-avatar">
                                        @if($group->image)
                                            <img src="{{ asset('images/articles/' . $group->image) }}" alt="{{ $group->article_group }}">
                                        @else
                                            {{ mb_strtoupper(mb_substr($group->initial ?: $group->article_group, 0, 2)) }}
                                        @endif
                                    </div>
                                </td>

                                <td>{{ $group->initial ?: '—' }}</td>

                                <td>
                                    <div class="ag-name">{{ $group->article_group }}</div>
                                    <div class="ag-muted">
                                        {{ $group->subArticleGroups->count() }} Sub-Artikelgruppen
                                    </div>

                                    @if(!($group->can_delete ?? true))
                                        <div class="ag-muted" style="color:#b45309;font-weight:800;">
                                            In Verwendung · {{ $group->usage_count ?? 0 }} Verknüpfung(en)
                                        </div>
                                    @endif
                                </td>

                                <td style="text-align:right;">
                                    {{ !is_null($group->min_value) ? number_format($group->min_value, 2, ',', '.') : '—' }}
                                </td>

                                <td style="text-align:right;">
                                    {{ !is_null($group->max_value) ? number_format($group->max_value, 2, ',', '.') : '—' }}
                                </td>

                                <td>
                                    <div class="ag-actions">
                                        <button
                                            type="button"
                                            class="ag-icon-btn edit js-edit-group"
                                            data-id="{{ $group->id }}"
                                            data-initial="{{ e($group->initial) }}"
                                            data-article-group="{{ e($group->article_group) }}"
                                            data-min-value="{{ $group->min_value }}"
                                            data-max-value="{{ $group->max_value }}"
                                        >
                                            ✎
                                        </button>

                                        @if($group->can_delete ?? true)
                                            <a href="{{ route('article_group.destroy', $group->id) }}"
                                               class="ag-icon-btn danger js-ajax-delete"
                                               data-confirm="Möchten Sie diese Artikel-Gruppe wirklich löschen?">
                                                ×
                                            </a>
                                        @else
                                            <button type="button" class="ag-icon-btn" title="{{ $group->delete_warning ?? 'Löschen nicht möglich' }}">
                                                !
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <tr class="ag-sub-row" id="sub-row-{{ $group->id }}">
                                <td colspan="8">
                                    <div class="ag-sub-box">
                                        <div class="ag-sub-head">
                                            <div>
                                                <strong>Sub-Artikelgruppen</strong>
                                                <div class="ag-muted">{{ $group->article_group }}</div>
                                            </div>

                                            <input type="text"
                                                   class="ag-input js-sub-search"
                                                   data-target="sub-table-{{ $group->id }}"
                                                   placeholder="In dieser Gruppe suchen …"
                                                   style="max-width:280px;">
                                        </div>

                                        <table class="ag-sub-table" id="sub-table-{{ $group->id }}">
                                            <thead>
                                                <tr>
                                                    <th style="width:70px;">ID</th>
                                                    <th style="width:120px;">Initial</th>
                                                    <th>Sub-Artikel</th>
                                                    <th style="width:150px;">Wert</th>
                                                    <th style="width:130px;">Status</th>
                                                    <th style="width:120px;text-align:right;">Aktion</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($group->subArticleGroups as $sub)
                                                    <tr>
                                                        <td>{{ $sub->id }}</td>
                                                        <td>{{ $sub->initial ?: '—' }}</td>
                                                        <td>{{ $sub->sub_article }}</td>
                                                        <td>{{ $sub->value ?: '—' }}</td>
                                                        <td>{{ $sub->status ?: '—' }}</td>
                                                        <td>
                                                            <div class="ag-actions">
                                                                <button
                                                                    type="button"
                                                                    class="ag-icon-btn edit js-edit-sub"
                                                                    data-id="{{ $sub->id }}"
                                                                    data-initial="{{ e($sub->initial) }}"
                                                                    data-sub-article="{{ e($sub->sub_article) }}"
                                                                    data-value="{{ e($sub->value) }}"
                                                                    data-status="{{ e($sub->status) }}"
                                                                >
                                                                    ✎
                                                                </button>

                                                                <a href="{{ route('sub_article_group.destroy', $sub->id) }}"
                                                                   class="ag-icon-btn danger js-ajax-delete"
                                                                   data-confirm="Möchten Sie diese Sub-Artikelgruppe wirklich löschen?">
                                                                    ×
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" style="text-align:center;color:#6b7280;">
                                                            Noch keine Sub-Artikelgruppen vorhanden.
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <tr>
                                                    <td colspan="6">
                                                        <form class="js-ajax-form" method="POST" action="{{ route('sub_article_group.store') }}">
                                                            @csrf
                                                            <input type="hidden" name="article_group_id" value="{{ $group->id }}">

                                                            <div class="ag-inline-form">
                                                                <div>
                                                                    <label class="ag-label">Initial</label>
                                                                    <input type="text" class="ag-input" name="initial">
                                                                </div>

                                                                <div>
                                                                    <label class="ag-label">Sub-Artikel</label>
                                                                    <input type="text" class="ag-input" name="sub_article" required>
                                                                </div>

                                                                <div>
                                                                    <label class="ag-label">Wert</label>
                                                                    <input type="text" class="ag-input" name="value">
                                                                </div>

                                                                <div>
                                                                    <label class="ag-label">Status</label>
                                                                    <input type="text" class="ag-input" name="status">
                                                                </div>

                                                                <button type="submit" class="ag-btn">Hinzufügen</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
        <div class="ag-pagination">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                <div style="font-size:12px;color:#6b7280;">
                    Zeige <strong>{{ $data->firstItem() ?? 0 }}</strong>
                    bis <strong>{{ $data->lastItem() ?? 0 }}</strong>
                    von <strong>{{ $data->total() }}</strong> Einträgen
                </div>

                <div>
                    {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Create Group Modal --}}
<div class="ag-modal-backdrop" id="createGroupModal">
    <div class="ag-modal">
        <div class="ag-modal-head">
            <h3 class="ag-modal-title">Neue Artikel-Gruppe</h3>
            <button type="button" class="ag-icon-btn" onclick="closeModal('createGroupModal')">×</button>
        </div>

        <form class="js-ajax-form" method="POST" action="{{ route('article_group.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="ag-modal-body">
                <div class="ag-grid">
                    <div>
                        <label class="ag-label">Initial</label>
                        <input type="text" name="initial" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Artikel-Gruppe *</label>
                        <input type="text" name="article_group" class="ag-input" required>
                    </div>

                    <div>
                        <label class="ag-label">Min-Wert</label>
                        <input type="number" step="0.01" name="min_value" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Max-Wert</label>
                        <input type="number" step="0.01" name="max_value" class="ag-input">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label class="ag-label">Foto</label>
                        <input type="file" name="image" class="ag-input">
                    </div>
                </div>
            </div>

            <div class="ag-modal-foot">
                <button type="button" class="ag-btn-soft" onclick="closeModal('createGroupModal')">Abbrechen</button>
                <button type="submit" class="ag-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

{{-- One Reusable Edit Group Modal --}}
<div class="ag-modal-backdrop" id="editGroupModal">
    <div class="ag-modal">
        <div class="ag-modal-head">
            <h3 class="ag-modal-title">Artikel-Gruppe bearbeiten</h3>
            <button type="button" class="ag-icon-btn" onclick="closeModal('editGroupModal')">×</button>
        </div>

        <form class="js-ajax-form" method="POST" action="{{ route('article_group.update') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="id" id="edit_group_id">

            <div class="ag-modal-body">
                <div class="ag-grid">
                    <div>
                        <label class="ag-label">Initial</label>
                        <input type="text" name="initial" id="edit_group_initial" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Artikel-Gruppe *</label>
                        <input type="text" name="article_group" id="edit_group_article_group" class="ag-input" required>
                    </div>

                    <div>
                        <label class="ag-label">Min-Wert</label>
                        <input type="number" step="0.01" name="min_value" id="edit_group_min_value" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Max-Wert</label>
                        <input type="number" step="0.01" name="max_value" id="edit_group_max_value" class="ag-input">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label class="ag-label">Foto</label>
                        <input type="file" name="image" class="ag-input">
                    </div>
                </div>
            </div>

            <div class="ag-modal-foot">
                <button type="button" class="ag-btn-soft" onclick="closeModal('editGroupModal')">Abbrechen</button>
                <button type="submit" class="ag-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

{{-- One Reusable Edit Sub Modal --}}
<div class="ag-modal-backdrop" id="editSubModal">
    <div class="ag-modal">
        <div class="ag-modal-head">
            <h3 class="ag-modal-title">Sub-Artikelgruppe bearbeiten</h3>
            <button type="button" class="ag-icon-btn" onclick="closeModal('editSubModal')">×</button>
        </div>

        <form class="js-ajax-form" method="POST" action="{{ route('sub_article_group.update') }}">
            @csrf

            <input type="hidden" name="id" id="edit_sub_id">

            <div class="ag-modal-body">
                <div class="ag-grid">
                    <div>
                        <label class="ag-label">Initial</label>
                        <input type="text" name="initial" id="edit_sub_initial" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Sub-Artikel *</label>
                        <input type="text" name="sub_article" id="edit_sub_sub_article" class="ag-input" required>
                    </div>

                    <div>
                        <label class="ag-label">Wert</label>
                        <input type="text" name="value" id="edit_sub_value" class="ag-input">
                    </div>

                    <div>
                        <label class="ag-label">Status</label>
                        <input type="text" name="status" id="edit_sub_status" class="ag-input">
                    </div>
                </div>
            </div>

            <div class="ag-modal-foot">
                <button type="button" class="ag-btn-soft" onclick="closeModal('editSubModal')">Abbrechen</button>
                <button type="submit" class="ag-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

<div class="ag-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;

    el.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;

    el.classList.remove('open');

    if (!document.querySelector('.ag-modal-backdrop.open')) {
        document.body.style.overflow = '';
    }
}

function toast(type, message) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const el = document.createElement('div');
    el.className = 'ag-toast';
    el.innerHTML = `
        <strong>${type === 'ok' ? 'Erfolgreich' : 'Hinweis'}</strong>
        <div style="font-size:13px;color:#374151;margin-top:4px;">${message}</div>
    `;

    wrap.appendChild(el);

    setTimeout(() => {
        try { el.remove(); } catch(e) {}
    }, 3500);
}

function submitFilterForm() {
    const form = document.getElementById('filterForm');
    if (form) form.submit();
}

document.addEventListener('click', function(e) {
    const backdrop = e.target.closest('.ag-modal-backdrop');

    if (e.target.classList.contains('ag-modal-backdrop')) {
        e.target.classList.remove('open');
        if (!document.querySelector('.ag-modal-backdrop.open')) {
            document.body.style.overflow = '';
        }
        return;
    }

    const toggleBtn = e.target.closest('.js-toggle-sub');
    if (toggleBtn) {
        const row = document.getElementById(toggleBtn.dataset.target);
        if (row) row.classList.toggle('open');
        return;
    }

    const sortLink = e.target.closest('.js-sort-link');
    if (sortLink) {
        e.preventDefault();

        const sortInput = document.getElementById('sortInput');
        const directionInput = document.getElementById('directionInput');

        const newSort = sortLink.dataset.sort;
        let newDirection = 'asc';

        if (sortInput.value === newSort) {
            newDirection = directionInput.value === 'asc' ? 'desc' : 'asc';
        }

        sortInput.value = newSort;
        directionInput.value = newDirection;

        submitFilterForm();
        return;
    }

    const editGroup = e.target.closest('.js-edit-group');
    if (editGroup) {
        document.getElementById('edit_group_id').value = editGroup.dataset.id || '';
        document.getElementById('edit_group_initial').value = editGroup.dataset.initial || '';
        document.getElementById('edit_group_article_group').value = editGroup.dataset.articleGroup || '';
        document.getElementById('edit_group_min_value').value = editGroup.dataset.minValue || '';
        document.getElementById('edit_group_max_value').value = editGroup.dataset.maxValue || '';

        openModal('editGroupModal');
        return;
    }

    const editSub = e.target.closest('.js-edit-sub');
    if (editSub) {
        document.getElementById('edit_sub_id').value = editSub.dataset.id || '';
        document.getElementById('edit_sub_initial').value = editSub.dataset.initial || '';
        document.getElementById('edit_sub_sub_article').value = editSub.dataset.subArticle || '';
        document.getElementById('edit_sub_value').value = editSub.dataset.value || '';
        document.getElementById('edit_sub_status').value = editSub.dataset.status || '';

        openModal('editSubModal');
        return;
    }

    const deleteBtn = e.target.closest('.js-ajax-delete');
    if (deleteBtn) {
        e.preventDefault();

        if (!confirm(deleteBtn.dataset.confirm || 'Wirklich löschen?')) return;

        fetch(deleteBtn.href, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            let data = {};
            try {
                data = await response.json();
            } catch(e) {}

            if (!response.ok) {
                throw new Error(data.message || 'Aktion fehlgeschlagen.');
            }

            toast('ok', data.message || 'Eintrag wurde gelöscht.');
            window.location.reload();
        })
        .catch(error => {
            toast('bad', error.message || 'Löschen nicht möglich.');
        });

        return;
    }
});

document.addEventListener('input', function(e) {
    const input = e.target.closest('.js-sub-search');
    if (!input) return;

    const table = document.getElementById(input.dataset.target);
    if (!table) return;

    const term = input.value.toLowerCase().trim();

    table.querySelectorAll('tbody tr').forEach(row => {
        if (row.querySelector('form')) {
            row.style.display = '';
            return;
        }

        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;

    document.querySelectorAll('.ag-modal-backdrop.open').forEach(el => {
        el.classList.remove('open');
    });

    document.body.style.overflow = '';
});
</script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Artikelgruppen',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush