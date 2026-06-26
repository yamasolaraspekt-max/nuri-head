@extends('admin.layouts.app')
@section('title', 'Notiz Kategorie')

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
$items = $isPaginator ? collect($data->items()) : collect($data);

$totalCount = $isPaginator ? $data->total() : $items->count();
$typedCount = (int) $items->filter(fn($item) => !empty($item->type))->count();
$uniqueColors = (int) $items->pluck('color')->filter()->unique()->count();
@endphp

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root{
        --app-bg:#f3f4f6;
        --card-bg:#ffffff;
        --text-main:#1f2937;
        --text-muted:#6b7280;
        --border:#e5e7eb;
        --primary:#93c21c;
        --primary-hover:#7baa18;
        --primary-light:#f4fae7;
        --blue:#74b2d4;
        --blue-light:#eff6ff;
        --success:#10b981;
        --success-light:#ecfdf5;
        --warning:#f59e0b;
        --warning-light:#fffbeb;
        --danger:#ef4444;
        --danger-hover:#dc2626;
        --danger-light:#fef2f2;
        --gray:#6b7280;
        --gray-light:#f3f4f6;
        --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
        --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
        --radius:16px;
        --transition:all .2s ease-in-out;
    }

    .nc-wrap{
        font-family:Inter, system-ui, -apple-system, sans-serif;
        color:var(--text-main); 
    }

    .nc-header{
        margin-bottom:18px; 
    }

    .nc-titlebar{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
    }

    .nc-title{
        font-size:26px;
        font-weight:800;
        letter-spacing:-.025em;
        color:#111827;
    }

    .nc-sub{
        font-size:14px;
        color:var(--text-muted);
        margin-top:4px;
    }

    .nc-breadcrumb{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
        margin-top:10px;
        font-size:13px;
        color:var(--text-muted);
    }

    .nc-breadcrumb a{
        color:var(--text-muted);
        text-decoration:none;
        font-weight:700;
    }

    .nc-breadcrumb a:hover{
        color:var(--text-main);
    }

    .nc-breadcrumb span.current{
        color:#111827;
        font-weight:800;
    }

    .nc-btn{
        background:var(--primary);
        color:#fff;
        border:none;
        padding:10px 16px;
        border-radius:10px;
        font-weight:900;
        cursor:pointer;
        transition:var(--transition);
        display:inline-flex;
        align-items:center;
        gap:8px;
        text-decoration:none;
    }

    .nc-btn:hover{
        background:var(--primary-hover);
        color:#fff;
        text-decoration:none;
    }

    .nc-btn-soft{
        background:#fff;
        color:var(--text-main);
        border:1px solid var(--border);
        padding:10px 14px;
        border-radius:10px;
        font-weight:800;
        cursor:pointer;
        transition:var(--transition);
        text-decoration:none;
    }

    .nc-btn-soft:hover{
        background:#f9fafb;
        color:var(--text-main);
        text-decoration:none;
    }

    .nc-btn-ic{
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color:var(--text-muted);
        cursor:pointer;
        transition:var(--transition);
        text-decoration:none;
    }

    .nc-btn-ic:hover{
        background:#f9fafb;
        color:var(--text-main);
        border-color:#d1d5db;
        text-decoration:none;
    }

    .nc-btn-ic.primary{
        color:var(--primary);
        border-color:var(--primary-light);
        background:var(--primary-light);
    }

    .nc-btn-ic.primary:hover{
        border-color:var(--primary);
    }

    .nc-btn-ic.danger{
        color:var(--danger);
        border-color:rgba(239,68,68,.18);
        background:var(--danger-light);
    }

    .nc-btn-ic.danger:hover{
        border-color:rgba(239,68,68,.35);
    }

    .nc-analytics{
        display:grid;
        grid-template-columns:repeat(3, minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
    }

    @media(max-width:1024px){
        .nc-analytics{grid-template-columns:1fr;}
    }

    .nc-stat{
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:16px;
        padding:16px;
        box-shadow:var(--shadow-sm);
        display:flex;
        align-items:center;
        gap:12px;
        min-height:92px;
    }

    .nc-stat-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .nc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
    .nc-stat-icon.type{background:var(--success-light);color:var(--success)}
    .nc-stat-icon.color{background:var(--warning-light);color:#d97706}

    .nc-stat-meta{min-width:0}
    .nc-stat-label{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .nc-stat-value{
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.1;
        margin-top:4px;
    }

    .nc-stat-sub{
        font-size:12px;
        color:var(--text-muted);
        margin-top:4px;
    }

    .nc-toolbar{
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:var(--radius);
        padding:14px 16px;
        display:flex;
        flex-wrap:wrap;
        gap:14px;
        align-items:flex-end;
        justify-content:space-between;
        margin-bottom:16px;
        box-shadow:var(--shadow-sm);
    }

    .nc-toolbar-left,.nc-toolbar-right{
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
    }

    .nc-toolbar-left{flex:1}

    .nc-filter-block{
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:170px;
    }

    .nc-filter-block.search{
        flex:1;
        min-width:280px;
    }

    .nc-filter-label{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .nc-input{
        background:#f9fafb;
        border:1px solid var(--border);
        border-radius:8px;
        padding:10px 12px 10px 36px;
        font-size:14px;
        outline:none;
        transition:var(--transition);
        min-width:240px;
        width:100%;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat:no-repeat;
        background-position:10px center;
        background-size:16px;
    }

    .nc-input:focus{
        background:#fff;
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
    }

    .nc-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        box-shadow:var(--shadow-sm);
        overflow:hidden;
    }

    .nc-list-head{
        display:grid;
        grid-template-columns:80px minmax(240px,1.6fr) minmax(160px,1fr) minmax(180px,1fr) 150px;
        gap:14px;
        align-items:center;
        padding:16px 16px 10px 16px;
        color:var(--text-muted);
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    @media(max-width:1180px){
        .nc-list-head{display:none;}
    }

    .nc-list{
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:0 0 16px 0;
    }

    .nc-item{
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:var(--radius);
        transition:var(--transition);
        overflow:hidden;
        margin:0 16px;
    }

    .nc-item:hover{
        border-color:var(--primary);
        box-shadow:var(--shadow);
    }

    .nc-item-row{
        padding:16px;
        display:grid;
        gap:16px;
        align-items:center;
        grid-template-columns:80px minmax(240px,1.6fr) minmax(160px,1fr) minmax(180px,1fr) 150px;
    }

    @media(max-width:1180px){
        .nc-item-row{grid-template-columns:1fr;}
    }

    .nc-cell{min-width:0}

    .nc-cell-title{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        margin-bottom:4px;
        display:none;
    }

    @media(max-width:1180px){
        .nc-cell-title{display:block;}
    }

    .nc-id-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:54px;
        height:36px;
        padding:0 12px;
        border-radius:10px;
        background:var(--blue-light);
        color:var(--blue);
        font-size:13px;
        font-weight:900;
    }

    .nc-main{
        display:flex;
        flex-direction:column;
        min-width:0;
    }

    .nc-ttl{
        font-weight:800;
        font-size:15px;
        margin-bottom:4px;
        color:#111827;
    }

    .nc-subt{
        font-size:13px;
        color:var(--text-muted);
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .nc-type-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
        background:#f3f4f6;
        color:#374151;
    }

    .nc-color-box{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .nc-color-preview{
        width:20px;
        height:20px;
        border-radius:999px;
        border:2px solid rgba(0,0,0,.08);
        flex:0 0 auto;
    }

    .nc-color-code{
        font-size:13px;
        font-weight:700;
        color:#374151;
    }

    .nc-actions{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
        flex-wrap:wrap;
    }

    .nc-empty{
        text-align:center;
        padding:60px;
        color:var(--text-muted);
        background:#fff;
        border:1px dashed var(--border);
        border-radius:16px;
        margin:16px;
    }

    .nc-pagination{
        margin-top:18px;
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        padding:14px 16px;
        box-shadow:var(--shadow-sm);
    }

    .nc-pagination .pagination{
        margin:0;
        display:flex;
        flex-wrap:wrap;
        gap:6px;
    }

    .nc-pagination .page-item .page-link{
        border-radius:10px !important;
        border:1px solid var(--border);
        color:var(--text-main);
        padding:8px 12px;
        line-height:1.1;
        box-shadow:none !important;
    }

    .nc-pagination .page-item.active .page-link{
        background:var(--primary);
        border-color:var(--primary);
        color:#fff;
    }

    .nc-pagination .page-item.disabled .page-link{
        color:#9ca3af;
        background:#f9fafb;
    }

    .nc-modal-backdrop{
        position:fixed;
        inset:0;
        z-index:1200;
        background:rgba(17,24,39,.55);
        backdrop-filter:blur(3px);
        opacity:0;
        pointer-events:none;
        transition:opacity .22s ease;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:18px;
    }

    .nc-modal-backdrop.open{
        opacity:1;
        pointer-events:auto;
    }

    .nc-modal{
        width:100%;
        max-width:620px;
        background:#fff;
        border:1px solid rgba(229,231,235,.9);
        border-radius:16px;
        box-shadow:var(--shadow);
        transform:translateY(12px) scale(.985);
        transition:transform .22s ease;
        overflow:hidden;
    }

    .nc-modal-backdrop.open .nc-modal{
        transform:translateY(0) scale(1);
    }

    .nc-modal-h{
        display:flex;
        gap:12px;
        align-items:center;
        justify-content:space-between;
        padding:16px 18px;
        border-bottom:1px solid var(--border);
        background:#fafafa;
    }

    .nc-modal-ttl{
        font-weight:900;
        font-size:16px;
        line-height:1.2;
        margin:0;
        color:#111827;
    }

    .nc-modal-b{
        padding:20px 18px;
        max-height:72vh;
        overflow-y:auto;
    }

    .nc-modal-f{
        padding:14px 18px;
        border-top:1px solid var(--border);
        background:#fafafa;
        display:flex;
        gap:10px;
        justify-content:flex-end;
        flex-wrap:wrap;
    }

    .nc-form-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(0,1fr));
        gap:16px;
    }

    @media(max-width:760px){
        .nc-form-grid{grid-template-columns:1fr;}
    }

    .nc-form-group{margin-bottom:16px;}

    .nc-label{
        display:block;
        font-size:13px;
        font-weight:700;
        color:var(--text-main);
        margin-bottom:6px;
    }

    .nc-input-form{
        width:100%;
        padding:10px 12px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        font-size:14px;
        outline:none;
        transition:var(--transition);
    }

    .nc-input-form:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
    }

    .nc-help{
        font-size:12px;
        color:var(--text-muted);
        margin-top:6px;
    }

    .nc-toast-wrap{
        position:fixed;
        right:20px;
        bottom:20px;
        z-index:9999;
        display:flex;
        flex-direction:column;
        gap:10px;
        pointer-events:none;
    }

    .nc-toast{
        pointer-events:auto;
        min-width:280px;
        max-width:360px;
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        box-shadow:var(--shadow);
        padding:12px;
        display:flex;
        gap:10px;
        align-items:flex-start;
        animation:ncToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
    }

    @keyframes ncToastIn{
        from{transform:translateX(100%);opacity:0}
        to{transform:translateX(0);opacity:1}
    }

    .nc-toast-ic{
        width:34px;
        height:34px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .nc-toast-ic.ok{background:var(--success-light);color:var(--success)}
    .nc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
    .nc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
    .nc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
    .nc-toast-x{
        margin-left:auto;
        background:transparent;
        border:none;
        cursor:pointer;
        color:var(--text-muted);
    }
</style>
@endsection

@section('content')
<div class="nc-wrap">
    <div class="nc-header">
        <div class="nc-titlebar">
            <div>
                <div class="nc-title">NOTIZ KATEGORIE</div>
                <div class="nc-sub">Verwalten Sie Kategorien, Typen und Farben für persönliche Notizen zentral.</div>

                <div class="nc-breadcrumb">
                    <a href="{{ url('/') }}">Home</a>
                    <span>›</span>
                    <span class="current">Notiz Kategorie</span>
                </div>
            </div>

            <div>
                <button type="button" class="nc-btn" onclick="openNcModal('createCategoryModal')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Neu hinzufügen
                </button>
            </div>
        </div>
    </div>

    <div class="nc-analytics">
        <div class="nc-stat">
            <div class="nc-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </div>
            <div class="nc-stat-meta">
                <div class="nc-stat-label">Gesamt</div>
                <div class="nc-stat-value">{{ $totalCount }}</div>
                <div class="nc-stat-sub">Kategorien insgesamt</div>
            </div>
        </div>

        <div class="nc-stat">
            <div class="nc-stat-icon type">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M7 12h10M10 17h4"/>
                </svg>
            </div>
            <div class="nc-stat-meta">
                <div class="nc-stat-label">Mit Typ</div>
                <div class="nc-stat-value">{{ $typedCount }}</div>
                <div class="nc-stat-sub">Typisierte Kategorien</div>
            </div>
        </div>

        <div class="nc-stat">
            <div class="nc-stat-icon color">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3c4.97 0 9 3.582 9 8 0 2.5-1.5 4-4 4h-1a2 2 0 0 0-2 2c0 .552.224 1.052.586 1.414A2 2 0 0 1 13.172 22H12C7.03 22 3 18.418 3 14c0-1.61.53-3.11 1.438-4.344C5.51 5.907 8.48 3 12 3z"/>
                    <circle cx="7.5" cy="11.5" r="1"/>
                    <circle cx="12" cy="8.5" r="1"/>
                    <circle cx="16.5" cy="11.5" r="1"/>
                </svg>
            </div>
            <div class="nc-stat-meta">
                <div class="nc-stat-label">Farben</div>
                <div class="nc-stat-value">{{ $uniqueColors }}</div>
                <div class="nc-stat-sub">Unterschiedliche Farbwerte</div>
            </div>
        </div>
    </div>

    <form action="{{ route('note.category.view')}}" method="GET" class="nc-toolbar">
        <div class="nc-toolbar-left">
            <div class="nc-filter-block search">
                <label class="nc-filter-label">Suche</label>
                <input
                    type="text"
                    class="nc-input"
                    placeholder="Suche nach Kategoriename, Typ oder Farbe"
                    name="search"
                    value="{{ request('search') }}"
                >
            </div>
        </div>

        <div class="nc-toolbar-right">
            <button class="nc-btn-soft" type="submit">Suchen</button>
            @if(request('search'))
                <a href="{{ route('note.category.view') }}" class="nc-btn-soft">Zurücksetzen</a>
            @endif
        </div>
    </form>

    <div class="nc-card">
        <div class="nc-list-head">
            <div>ID</div>
            <div>Kategoriename</div>
            <div>Typ</div>
            <div>Farbe</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="nc-list">
            @forelse($data as $item)
                <div class="nc-item">
                    <div class="nc-item-row">
                        <div class="nc-cell">
                            <div class="nc-cell-title">ID</div>
                            <span class="nc-id-badge">#{{ $item->id }}</span>
                        </div>

                        <div class="nc-cell">
                            <div class="nc-cell-title">Kategoriename</div>
                            <div class="nc-main">
                                <div class="nc-ttl">{{ $item->category_name }}</div>
                                <div class="nc-subt">Kategorie für Notizen</div>
                            </div>
                        </div>

                        <div class="nc-cell">
                            <div class="nc-cell-title">Typ</div>
                            <span class="nc-type-pill">{{ $item->type ?: 'Normal' }}</span>
                        </div>

                        <div class="nc-cell">
                            <div class="nc-cell-title">Farbe</div>
                            <div class="nc-color-box">
                                <span class="nc-color-preview" style="background:{{ $item->color }};"></span>
                                <span class="nc-color-code">{{ $item->color }}</span>
                            </div>
                        </div>

                        <div class="nc-cell">
                            <div class="nc-cell-title">Aktionen</div>
                            <div class="nc-actions">
                                <button
                                    type="button"
                                    class="nc-btn-ic primary js-open-edit"
                                    title="Bearbeiten"
                                    data-id="{{ $item->id }}"
                                    data-category_name="{{ $item->category_name }}"
                                    data-type="{{ $item->type }}"
                                    data-color="{{ $item->color }}"
                                >
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="nc-btn-ic danger js-open-delete"
                                    title="Löschen"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->category_name }}"
                                    data-url="{{ url('/note_category_destroy') . '/' . $item->id }}"
                                >
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="nc-empty">Keine Kategorien gefunden.</div>
            @endforelse
        </div>
    </div>

    @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
        <div class="nc-pagination">
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

<div class="nc-modal-backdrop" id="createCategoryModal">
    <div class="nc-modal">
        <div class="nc-modal-h">
            <h3 class="nc-modal-ttl">Neue Kategorie anlegen</h3>
            <button class="nc-btn-ic" type="button" onclick="closeNcModal('createCategoryModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('note.category.store')}}">
            @csrf
            <div class="nc-modal-b">
                <div class="nc-form-grid">
                    <div class="nc-form-group">
                        <label class="nc-label">Kategoriename *</label>
                        <input type="text" class="nc-input-form" name="category_name" value="{{ old('category_name') }}" required>
                        @if ($errors->has('category_name'))
                            <p style="color:red;margin-top:6px;">{!! $errors->first('category_name') !!}</p>
                        @endif
                    </div>

                    <div class="nc-form-group">
                        <label class="nc-label">Typ *</label>
                        <input type="text" class="nc-input-form" name="type" value="{{ old('type', 'Normal') }}" required>
                        @if ($errors->has('type'))
                            <p style="color:red;margin-top:6px;">{!! $errors->first('type') !!}</p>
                        @endif
                    </div>

                    <div class="nc-form-group" style="grid-column:1 / -1;">
                        <label class="nc-label">Farbe *</label>
                        <input type="color" class="nc-input-form" name="color" value="{{ old('color', '#93c21c') }}" required style="height:48px;padding:6px;">
                        <div class="nc-help">Wählen Sie die Farbe für diese Notiz-Kategorie.</div>
                        @if ($errors->has('color'))
                            <p style="color:red;margin-top:6px;">{!! $errors->first('color') !!}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="nc-modal-f">
                <button type="button" class="nc-btn-soft" onclick="closeNcModal('createCategoryModal')">Abbrechen</button>
                <button type="submit" class="nc-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

<div class="nc-modal-backdrop" id="editCategoryModal">
    <div class="nc-modal">
        <div class="nc-modal-h">
            <h3 class="nc-modal-ttl">Kategorie bearbeiten</h3>
            <button class="nc-btn-ic" type="button" onclick="closeNcModal('editCategoryModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('note.category.update') }}">
            @csrf
            <div class="nc-modal-b">
                <input type="hidden" name="id" id="edit_id">

                <div class="nc-form-grid">
                    <div class="nc-form-group">
                        <label class="nc-label">Kategoriename *</label>
                        <input type="text" class="nc-input-form" name="category_name" id="edit_category_name" required>
                    </div>

                    <div class="nc-form-group">
                        <label class="nc-label">Typ *</label>
                        <input type="text" class="nc-input-form" name="type" id="edit_type" required>
                    </div>

                    <div class="nc-form-group" style="grid-column:1 / -1;">
                        <label class="nc-label">Farbe *</label>
                        <input type="color" class="nc-input-form" name="color" id="edit_color" required style="height:48px;padding:6px;">
                    </div>
                </div>
            </div>

            <div class="nc-modal-f">
                <button type="button" class="nc-btn-soft" onclick="closeNcModal('editCategoryModal')">Abbrechen</button>
                <button type="submit" class="nc-btn">Aktualisieren</button>
            </div>
        </form>
    </div>
</div>

<div class="nc-modal-backdrop" id="deleteCategoryModal">
    <div class="nc-modal" style="max-width:520px;">
        <div class="nc-modal-h">
            <h3 class="nc-modal-ttl">Datensatz löschen</h3>
            <button class="nc-btn-ic" type="button" onclick="closeNcModal('deleteCategoryModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="nc-modal-b">
            <p style="margin:0 0 8px 0;font-weight:800;color:#111827;">Möchten Sie diesen Datensatz wirklich löschen?</p>
            <p style="margin:0;color:#6b7280;">
                Kategorie: <strong id="delete_category_name">-</strong>
            </p>
        </div>

        <div class="nc-modal-f">
            <button type="button" class="nc-btn-soft" onclick="closeNcModal('deleteCategoryModal')">Abbrechen</button>
            <a href="#" id="delete_category_link" class="nc-btn" style="background:var(--danger);">Löschen</a>
        </div>
    </div>
</div>

<div class="nc-toast-wrap" id="nc-toast-wrap"></div>
@endsection

@section('script')
<script>
    function openNcModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('open');
    }

    function closeNcModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('open');
    }

    function ncToast(kind, title, msg) {
        const wrap = document.getElementById('nc-toast-wrap');
        if (!wrap) return;

        const icons = {
            ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
            bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
        };

        const el = document.createElement('div');
        el.className = 'nc-toast';
        el.innerHTML = `
            <div class="nc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
            <div style="flex:1;">
                <p class="nc-toast-ttl">${title}</p>
                <p class="nc-toast-msg">${msg}</p>
            </div>
            <button class="nc-toast-x" onclick="this.parentElement.remove()">×</button>
        `;
        wrap.appendChild(el);

        setTimeout(() => {
            try { el.remove(); } catch (e) {}
        }, 4000);
    }

    document.addEventListener('click', function(e){
        if (e.target.classList.contains('nc-modal-backdrop')) {
            e.target.classList.remove('open');
        }

        const editBtn = e.target.closest('.js-open-edit');
        if (editBtn) {
            document.getElementById('edit_id').value = editBtn.dataset.id || '';
            document.getElementById('edit_category_name').value = editBtn.dataset.category_name || '';
            document.getElementById('edit_type').value = editBtn.dataset.type || '';
            document.getElementById('edit_color').value = editBtn.dataset.color || '#93c21c';
            openNcModal('editCategoryModal');
            return;
        }

        const deleteBtn = e.target.closest('.js-open-delete');
        if (deleteBtn) {
            document.getElementById('delete_category_name').textContent = deleteBtn.dataset.name || '-';
            document.getElementById('delete_category_link').setAttribute('href', deleteBtn.dataset.url || '#');
            openNcModal('deleteCategoryModal');
        }
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            document.querySelectorAll('.nc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
        }
    });

    @if(Session::has('update_msg'))
        ncToast('ok', 'Aktualisiert', @json(session('update_msg')));
    @endif

    @if(Session::has('updated_msg'))
        ncToast('ok', 'Aktualisiert', @json(session('updated_msg')));
    @endif

    @if(Session::has('save_msg'))
        ncToast('ok', 'Gespeichert', @json(session('save_msg')));
    @endif

    @if(Session::has('delete_msg'))
        ncToast('bad', 'Gelöscht', @json(session('delete_msg')));
    @endif

    @if ($errors->any())
        openNcModal('createCategoryModal');
    @endif
</script>
@endsection

@push('scripts')
        <script>
            window.GlobalBreadcrumbs =[
                {
                    label: 'Dashboard',
                    url: "{{ url('/') }}"
                },
                {
                    label: 'Notiz Kategorie',
                    url: "{{ url()->current()}}",
                    clickable: false
                }

            ];

            if (window.setGlobalBreadcrumbs) {
                window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
            }
        </script>
@endpush