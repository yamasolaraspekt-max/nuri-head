@extends('admin.layouts.app')@section('title', 'Papierkorb bereinigen')@php
$pageTitle = 'PAPIERKORB BEREINIGEN';

$tables = collect($tables ?? []);
$totalTables = $totalTables ?? $tables->count();
$tablesWithGarbage = $tablesWithGarbage ?? $tables->where('count', '>', 0)->count();
$totalGarbage = $totalGarbage ?? $tables->sum('count');

$mode = $mode ?? request('mode', 'older');
$months = $months ?? (int) request('months', 2);
$all = $all ?? $mode === 'all';
$olderThan = $olderThan ?? ($all ? null : now()->subMonths($months));
@endphp

@once
    @push('style')
        <style>
          :root {
            --app-bg:#f3f4f6;
            --card-bg:#ffffff;
            --text-main:#1f2937;
            --text-muted:#6b7280;
            --border:#e5e7eb;
            --primary:var(--sa-accent);
            --primary-hover:var(--sa-accent-hover);
            --primary-light:var(--sa-accent-light);
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
            --radius:14px;
            --transition:all .2s ease-in-out;
          }

          .oc-wrap {
              font-family: Inter, system-ui, -apple-system, sans-serif;
              color: var(--text-main); 
          }

          .oc-header{margin-bottom:18px;}
          .oc-titlebar{
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:12px;
            margin-bottom:16px;
            flex-wrap:wrap;
          }
          .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
          .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
          .oc-breadcrumb{
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
            font-size:13px;
            color:var(--text-muted);
          }
          .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
          .oc-breadcrumb a:hover{color:var(--text-main);}
          .oc-breadcrumb span.current{color:#111827;font-weight:800;}

          .oc-inline-actions{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
          }

          .oc-btn{
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
          .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

          .oc-btn.danger{
            background:var(--danger);
            color:white !important;
          }
          .oc-btn.danger:hover{
            background:var(--danger-hover);
          }

          .oc-btn-soft{
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
          .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

          .oc-btn-ic{
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
          .oc-btn-ic:hover{
            background:#f9fafb;
            color:var(--text-main);
            border-color:#d1d5db;
            text-decoration:none;
          }
          .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
          .oc-btn-ic.primary:hover{border-color:var(--primary)}
          .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
          .oc-btn-ic.warning:hover{border-color:#f59e0b}
          .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
          .oc-btn-ic.success:hover{border-color:var(--success)}
          .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
          .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

          .oc-analytics{
            display:grid;
            grid-template-columns:repeat(4, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
          }
          @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
          @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

          .oc-stat{
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

          .oc-stat-icon{
            width:48px;
            height:48px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
          }
          .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
          .oc-stat-icon.active{background:var(--success-light);color:var(--success)}
          .oc-stat-icon.warning{background:var(--warning-light);color:#d97706}
          .oc-stat-icon.danger{background:var(--danger-light);color:var(--danger)}

          .oc-stat-meta{min-width:0}
          .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
          .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
          .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

          .oc-toolbar{
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

          .oc-toolbar-left,.oc-toolbar-right{
            display:flex;
            align-items:flex-end;
            gap:12px;
            flex-wrap:wrap;
          }
          .oc-toolbar-left{flex:1;}

          .oc-filter-block{
            display:flex;
            flex-direction:column;
            gap:6px;
            min-width:180px;
          }
          .oc-filter-label{
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
          }

          .oc-input-form,.oc-select{
            width:100%;
            padding:10px 12px;
            border-radius:8px;
            border:1px solid var(--border);
            background:#fff;
            font-size:14px;
            outline:none;
            transition:var(--transition);
          }
          .oc-input-form:focus,.oc-select:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light);
          }

          .oc-card{
            background:#fff;
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:var(--shadow-sm);
            overflow:hidden;
          }

          .oc-list-head{
            display:grid;
            grid-template-columns:54px minmax(250px,1.4fr) 130px 170px 170px 190px;
            gap:14px;
            align-items:center;
            padding:16px 16px 10px 16px;
            color:var(--text-muted);
            font-size:11px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
          }
          @media(max-width:1280px){ .oc-list-head{display:none;} }

          .oc-list{
            display:flex;
            flex-direction:column;
            gap:12px;
            padding:0 0 16px 0;
          }

          .oc-item{
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:var(--radius);
            transition:var(--transition);
            overflow:hidden;
            margin:0 16px;
          }
          .oc-item:hover{
            border-color:var(--primary);
            box-shadow:var(--shadow);
          }

          .oc-item-row{
            padding:16px;
            display:grid;
            gap:16px;
            align-items:center;
            grid-template-columns:54px minmax(250px,1.4fr) 130px 170px 170px 190px;
          }
          @media(max-width:1280px){ .oc-item-row{grid-template-columns:1fr;} }

          .oc-cell{min-width:0}
          .oc-cell-title{
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            text-transform:uppercase;
            margin-bottom:4px;
            display:none;
          }
          @media(max-width:1280px){ .oc-cell-title{display:block;} }

          .oc-main{display:flex;flex-direction:column;min-width:0}
          .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
          .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

          .oc-count-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:54px;
            height:36px;
            padding:0 12px;
            border-radius:10px;
            background:var(--danger-light);
            color:var(--danger);
            font-size:13px;
            font-weight:900;
          }
          .oc-count-badge.clean{
            background:var(--success-light);
            color:var(--success);
          }

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
          .oc-status-pill.green{background:#ecfdf5;color:#047857;}
          .oc-status-pill.red{background:#fef2f2;color:#b91c1c;}
          .oc-status-pill.orange{background:#fffbeb;color:#b45309;}

          .oc-actions{
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
            flex-wrap:wrap;
          }

          .oc-empty{
            text-align:center;
            padding:60px;
            color:var(--text-muted);
            background:#fff;
            border:1px dashed var(--border);
            border-radius:16px;
            margin:16px;
          }

          .oc-danger-box{
            background:#fff;
            border:1px solid rgba(239,68,68,.25);
            border-radius:16px;
            padding:16px;
            box-shadow:var(--shadow-sm);
            margin-bottom:16px;
            display:flex;
            justify-content:space-between;
            gap:16px;
            align-items:center;
            flex-wrap:wrap;
          }

          .oc-danger-title{
            font-size:15px;
            font-weight:900;
            color:#991b1b;
            margin-bottom:4px;
          }

          .oc-danger-sub{
            font-size:13px;
            color:#7f1d1d;
          }

          .oc-toast-wrap{
            position:fixed;
            right:20px;
            bottom:20px;
            z-index:9999;
            display:flex;
            flex-direction:column;
            gap:10px;
            pointer-events:none;
          }
          .oc-toast{
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
            animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
          }
          @keyframes ocToastIn{
            from{transform:translateX(100%);opacity:0}
            to{transform:translateX(0);opacity:1}
          }
          .oc-toast-ic{
            width:34px;
            height:34px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
          }
          .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
          .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
          .oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
          .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
          .oc-toast-x{
            margin-left:auto;
            background:transparent;
            border:none;
            cursor:pointer;
            color:var(--text-muted);
          }
        </style>
    @endpush
@endonce

@section('content')
    <div class="oc-wrap">
      <div class="oc-header">
        <div class="oc-titlebar">
          <div>
            <div class="oc-title">{{ $pageTitle }}</div>
            <div class="oc-sub">
              Verwalten und löschen Sie dauerhaft alle weich gelöschten Datensätze aus Tabellen mit
              <strong>deleted_at</strong>.
            </div>

            <div class="oc-breadcrumb">
              <a href="{{ url('/employee_dashboard') }}">Home</a>
              <span>›</span>
              <span class="current">{{ $pageTitle }}</span>
            </div>
          </div>

          <div class="oc-inline-actions">
            <a href="{{ route('admin.garbage.index', ['mode' => $mode, 'months' => $months]) }}" class="oc-btn-soft">
              Aktualisieren
            </a>
          </div>
        </div>
      </div>

      <div class="oc-analytics">
        <div class="oc-stat">
          <div class="oc-stat-icon total">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
          </div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Gesamt</div>
            <div class="oc-stat-value">{{ $totalTables }}</div>
            <div class="oc-stat-sub">Tabellen mit deleted_at</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon warning">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v4m0 4h.01M10.3 4.3 2.8 17.5A2 2 0 0 0 4.5 20.5h15a2 2 0 0 0 1.7-3L13.7 4.3a2 2 0 0 0-3.4 0Z"/>
            </svg>
          </div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Mit Papierkorb</div>
            <div class="oc-stat-value">{{ $tablesWithGarbage }}</div>
            <div class="oc-stat-sub">Tabellen mit gelöschten Daten</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon danger">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6m2 0-.8 13.2A2 2 0 0 1 15.2 21H8.8a2 2 0 0 1-2-1.8L6 6m4 4v7m4-7v7"/>
            </svg>
          </div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Datensätze</div>
            <div class="oc-stat-value">{{ $totalGarbage }}</div>
            <div class="oc-stat-sub">Zum endgültigen Löschen</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon active">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Modus</div>
            <div class="oc-stat-value" style="font-size:18px;">
              {{ $all ? 'Alle' : $months . ' Monate' }}
            </div>
            <div class="oc-stat-sub">
              {{ $all ? 'Alle weich gelöschten Daten' : 'Älter als Auswahl' }}
            </div>
          </div>
        </div>
      </div>

      <form action="{{ route('admin.garbage.index') }}" method="GET" class="oc-toolbar">
        <div class="oc-toolbar-left">
          <div class="oc-filter-block">
            <label class="oc-filter-label">Löschmodus</label>
            <select name="mode" class="oc-select">
              <option value="older" @selected($mode === 'older')>Nur ältere Datensätze</option>
              <option value="all" @selected($mode === 'all')>Alle weich gelöschten Datensätze</option>
            </select>
          </div>

          <div class="oc-filter-block">
            <label class="oc-filter-label">Älter als Monate</label>
            <input type="number" name="months" value="{{ $months }}" min="1" max="120" class="oc-input-form">
          </div>

          <div class="oc-filter-block">
            <label class="oc-filter-label">Aktueller Filter</label>
            <div class="oc-input-form" style="background:#f9fafb;">
              @if($all)
                Alle weich gelöschten Datensätze
              @else
                Gelöscht vor {{ $olderThan ? \Carbon\Carbon::parse($olderThan)->format('d.m.Y H:i') : '-' }}
              @endif
            </div>
          </div>
        </div>

        <div class="oc-toolbar-right">
          <button class="oc-btn-soft" type="submit">Anzeigen</button>
          <a href="{{ route('admin.garbage.index') }}" class="oc-btn-soft">Zurücksetzen</a>
        </div>
      </form>

      @if(session('success'))
        <div class="oc-danger-box" style="border-color:rgba(16,185,129,.25);">
          <div>
            <div class="oc-danger-title" style="color:#047857;">Erfolgreich</div>
            <div class="oc-danger-sub" style="color:#047857;">{{ session('success') }}</div>
          </div>
        </div>
      @endif

      @if(session('error'))
        <div class="oc-danger-box">
          <div>
            <div class="oc-danger-title">Fehler</div>
            <div class="oc-danger-sub">{{ session('error') }}</div>
          </div>
        </div>
      @endif

      <div class="oc-danger-box">
        <div>
          <div class="oc-danger-title">Gefährliche Aktion</div>
          <div class="oc-danger-sub">
            Diese Aktion löscht Datensätze endgültig. Danach ist keine Wiederherstellung über Laravel SoftDeletes mehr möglich.
          </div>
        </div>

        <form method="POST"
              action="{{ route('admin.garbage.all.delete') }}"
              onsubmit="return confirm('Sind Sie sicher? Alle passenden weich gelöschten Datensätze werden endgültig gelöscht.');">
          @csrf
          @method('DELETE')

          <input type="hidden" name="mode" value="{{ $mode }}">
          <input type="hidden" name="months" value="{{ $months }}">

          <button type="submit" class="oc-btn danger">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6m2 0-.8 13.2A2 2 0 0 1 15.2 21H8.8a2 2 0 0 1-2-1.8L6 6m4 4v7m4-7v7"/>
            </svg>
            Alle passenden löschen
          </button>
        </form>
      </div>

      <form method="POST"
            action="{{ route('admin.garbage.bulk.delete') }}"
            onsubmit="return confirm('Ausgewählte Tabellen endgültig bereinigen?');"
            class="oc-card">
        @csrf
        @method('DELETE')

        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="months" value="{{ $months }}">

        <div class="oc-toolbar" style="border:none;border-bottom:1px solid var(--border);border-radius:0;margin-bottom:0;box-shadow:none;">
          <div>
            <div class="oc-title" style="font-size:20px;">Weich gelöschte Tabellen</div>
            <div class="oc-sub">Wählen Sie einzelne Tabellen aus oder löschen Sie eine Tabelle direkt.</div>
          </div>

          <div class="oc-toolbar-right">
            <button type="button" class="oc-btn-soft" onclick="toggleAllGarbageTables(true)">Alle auswählen</button>
            <button type="button" class="oc-btn-soft" onclick="toggleAllGarbageTables(false)">Auswahl löschen</button>
            <button type="submit" class="oc-btn danger">Ausgewählte löschen</button>
          </div>
        </div>

        <div class="oc-list-head">
          <div>
            <input type="checkbox" onclick="toggleAllGarbageTables(this.checked)">
          </div>
          <div>Tabelle</div>
          <div>Datensätze</div>
          <div>Älteste Löschung</div>
          <div>Neueste Löschung</div>
          <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="oc-list">
          @forelse($tables as $row)
            @php
  $count = (int) ($row['count'] ?? 0);
  $tableName = $row['table'] ?? '';
  $oldest = !empty($row['oldest_deleted_at']) ? \Carbon\Carbon::parse($row['oldest_deleted_at'])->format('d.m.Y H:i') : '—';
  $newest = !empty($row['newest_deleted_at']) ? \Carbon\Carbon::parse($row['newest_deleted_at'])->format('d.m.Y H:i') : '—';
            @endphp

            <div class="oc-item" style="{{ $count === 0 ? 'opacity:.62;' : '' }}">
              <div class="oc-item-row">
                <div class="oc-cell">
                  <div class="oc-cell-title">Auswahl</div>
                  @if($count > 0)
                    <input type="checkbox"
                           name="tables[]"
                           value="{{ $tableName }}"
                           class="garbage-table-checkbox">
                  @else
                    <span class="oc-status-pill green">Sauber</span>
                  @endif
                </div>

                <div class="oc-cell">
                  <div class="oc-cell-title">Tabelle</div>
                  <div class="oc-main">
                    <div class="oc-ttl">{{ $tableName }}</div>
                    <div class="oc-subt">
                      @if($count > 0)
                        Enthält weich gelöschte Datensätze
                      @else
                        Keine weich gelöschten Datensätze gefunden
                      @endif
                    </div>
                  </div>
                </div>

                <div class="oc-cell">
                  <div class="oc-cell-title">Datensätze</div>
                  <span class="oc-count-badge {{ $count === 0 ? 'clean' : '' }}">{{ $count }}</span>
                </div>

                <div class="oc-cell">
                  <div class="oc-cell-title">Älteste Löschung</div>
                  <div class="oc-main">
                    <div class="oc-ttl" style="font-size:14px;">{{ $oldest }}</div>
                  </div>
                </div>

                <div class="oc-cell">
                  <div class="oc-cell-title">Neueste Löschung</div>
                  <div class="oc-main">
                    <div class="oc-ttl" style="font-size:14px;">{{ $newest }}</div>
                  </div>
                </div>

                <div class="oc-cell">
                  <div class="oc-cell-title">Aktionen</div>
                  <div class="oc-actions">
                    @if($count > 0)
                          <button type="submit"
                                  formaction="{{ route('admin.garbage.table.delete') }}"
                                  formmethod="POST"
                                  name="table"
                                  value="{{ $tableName }}"
                                  class="oc-btn-ic danger"
                                  title="Diese Tabelle bereinigen"
                                  onclick="return confirm('Tabelle {{ $tableName }} endgültig bereinigen?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M3 6h18M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6m2 0-.8 13.2A2 2 0 0 1 15.2 21H8.8a2 2 0 0 1-2-1.8L6 6m4 4v7m4-7v7"/>
                            </svg>
                          </button>
                    @else
                          <span class="oc-status-pill green">Keine Aktion</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="oc-empty">Keine Tabellen mit deleted_at gefunden.</div>
          @endforelse
        </div>
      </form>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
    @push('scripts')
        <script>
          function toggleAllGarbageTables(checked) {
            document.querySelectorAll('.garbage-table-checkbox').forEach(function (checkbox) {
              checkbox.checked = checked;
            });
          }

          function toast(kind, title, msg) {
            const wrap = document.getElementById('toast-wrap');
            if (!wrap) return;

            const icons = {
              ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
              bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
            };

            const el = document.createElement('div');
            el.className = 'oc-toast';
            el.innerHTML = `
              <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
              <div style="flex:1;">
                <p class="oc-toast-ttl">${title}</p>
                <p class="oc-toast-msg">${msg}</p>
              </div>
              <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
            `;
            wrap.appendChild(el);
            setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
          }

          @if(session('success'))
            toast('ok', 'Erfolgreich', @json(session('success')));
          @endif

          @if(session('error'))
            toast('bad', 'Fehler', @json(session('error')));
          @endif
        </script>
    @endpush
@endonce

@push('scripts')
  <script>
    window.GlobalBreadcrumbs =[
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      }, 
      {
        label: 'Papierkorb reinigen',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush