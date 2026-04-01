@extends('admin.layouts.app')

@section('title', 'Attendance Analytics')

@section('style')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* === SAME DESIGN SYSTEM (rr-*) === */
        .rr-wrap {
            --rr-primary: #73b2d4;
            --rr-primary-dark: #5da0c2;
            --rr-primary-light: #c0d8ea;
            --rr-secondary: #93c21c;
            --rr-secondary-light: #cfe09b;

            --rr-text-dark: #2c3e50;
            --rr-text-muted: #7f8c8d;
            --rr-bg-body: #f0f4f8;
            --rr-surface: #ffffff;
            --rr-border: #e2e8f0;

            font-family: 'Outfit', sans-serif;
            padding: 20px;
            background-color: var(--rr-bg-body);
        }
        .rr-wrap * { box-sizing: border-box; }
        .rr-container { max-width: 1400px; margin: 0 auto; }

        .rr-flex-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
        .rr-mb-2 { margin-bottom:.5rem; }
        .rr-mb-4 { margin-bottom:1.5rem; }
        .rr-me-2 { margin-right:.5rem; }
        .rr-gap-2 { gap:.5rem; }
        .rr-text-muted { color: var(--rr-text-muted); }
        .rr-small { font-size:.875rem; }
        .rr-fw-bold { font-weight:700; }
        .rr-fw-medium { font-weight:500; }
        .rr-text-right { text-align:right; }
        .rr-text-center { text-align:center; }
        .rr-wrap h3 { font-size:1.5rem; font-weight:700; color: var(--rr-text-dark); margin:0; }

        .rr-stats-grid {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .rr-stat-card {
            background: var(--rr-surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(115, 178, 212, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            display:flex; flex-direction:column; justify-content:space-between;
        }
        .rr-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(115, 178, 212, 0.15); }
        .rr-stat-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; }
        .rr-stat-icon {
            width:48px; height:48px; border-radius:12px;
            display:flex; align-items:center; justify-content:center;
            font-size:1.25rem;
        }
        .rr-stat-value { font-size:1.75rem; font-weight:700; line-height:1; margin-bottom:.25rem; color: var(--rr-text-dark); }
        .rr-stat-label { font-size:.875rem; color: var(--rr-text-muted); font-weight:500; }

        .rr-main-card {
            background: var(--rr-surface);
            border-radius: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            overflow: hidden;
        }

        .rr-filter-bar { background:#f8fafc; border-bottom:1px solid var(--rr-border); padding: 1.5rem; }
        .rr-filter-grid {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap:1rem;
            align-items:end;
        }
        .rr-filter-group { display:flex; flex-direction:column; }
        .rr-filter-grid > * { min-width:0; }

        .rr-filter-label {
            font-size:.75rem;
            text-transform:uppercase;
            letter-spacing:.5px;
            font-weight:700;
            color:#a0aec0;
            margin-bottom:6px;
        }

        .rr-input-wrapper { position:relative; display:flex; align-items:center; }
        .rr-input-icon { position:absolute; left:1rem; color: var(--rr-text-muted); pointer-events:none; }

        .rr-form-control, .rr-form-select {
            width:100%;
            border:1px solid var(--rr-border);
            border-radius:10px;
            padding:.6rem 1rem;
            font-size:.95rem;
            font-family:inherit;
            background:#fff;
            outline:none;
            transition: border-color .2s, box-shadow .2s;
            height:42px;
            color: var(--rr-text-dark);
        }
        .rr-has-icon { padding-left: 2.5rem; }
        .rr-form-control:focus, .rr-form-select:focus {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, 0.2);
        }

        .rr-btn {
            border:none;
            border-radius:10px;
            padding:.5rem 1rem;
            font-weight:500;
            cursor:pointer;
            font-family:inherit;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            transition: all .2s;
            text-decoration:none;
            height:42px;
            background:#fff;
            font-size:1rem;
        }
        .rr-btn-sm { height:32px; font-size:.85rem; padding:0 .75rem; }
        .rr-btn-primary-soft { background: var(--rr-primary); color:#fff; }
        .rr-btn-primary-soft:hover { background: var(--rr-primary-dark); transform: translateY(-1px); color:#fff; }
        .rr-btn-light { background:#fff; border:1px solid var(--rr-border); color: var(--rr-text-dark); }
        .rr-btn-light:hover { background:#f8fafc; }

        .rr-btn-icon {
            width:32px; height:32px; border-radius:8px;
            display:inline-flex; align-items:center; justify-content:center;
            color: var(--rr-text-muted);
            background: transparent;
            border:1px solid var(--rr-border);
            cursor:pointer;
            transition: all .2s;
        }
        .rr-btn-icon:hover { border-color: var(--rr-primary); color: var(--rr-primary); background: #f0f7fb; }

        .rr-table-wrapper { overflow-x:auto; width:100%; }
        .rr-table { width:100%; border-collapse:collapse; min-width: 1100px; }
        .rr-table th {
            background:#fff;
            color: var(--rr-text-muted);
            font-weight:600;
            font-size:.8rem;
            text-transform:uppercase;
            letter-spacing:.5px;
            padding:1.25rem 1.5rem;
            border-bottom:2px solid var(--rr-border);
            text-align:left;
        }
        .rr-table td {
            padding:1.1rem 1.5rem;
            vertical-align:middle;
            border-bottom:1px solid var(--rr-border);
            background:#fff;
            color: var(--rr-text-dark);
        }
        .rr-table tr:hover td { background:#fcfdfe; }

        .rr-user-cell { display:flex; align-items:center; gap:.75rem; }
        .rr-avatar {
            width:36px; height:36px; border-radius:50%;
            background: linear-gradient(135deg, var(--rr-primary-light), #fff);
            color: var(--rr-primary);
            display:flex; align-items:center; justify-content:center;
            font-weight:600; font-size:.9rem;
            border:2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex-shrink:0;
        }
        .rr-text-truncate { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:280px; display:block; }

        .rr-table-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--rr-border);
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        /* Sidebar */
        .rr-sidebar-backdrop {
            position:fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.3);
            z-index: 9998;
            opacity:0; visibility:hidden;
            transition: opacity .3s;
        }
        .rr-sidebar-backdrop.active { opacity:1; visibility:visible; }
        .rr-sidebar {
            position:fixed; top:0; right:0;
            width:100%; max-width: 520px;
            height:100%;
            background:#fff;
            z-index: 9999;
            box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            transform: translateX(100%);
            transition: transform .3s ease-in-out;
            display:flex; flex-direction:column;
        }
        .rr-sidebar.active { transform: translateX(0); }
        .rr-sidebar-header {
            padding: 1.25rem 1.5rem;
            border-bottom:1px solid var(--rr-border);
            display:flex; justify-content:space-between; align-items:center;
        }
        .rr-sidebar-body { padding: 1.25rem 1.5rem; overflow-y:auto; flex:1; }
        .rr-close-btn { background:none; border:none; font-size:1.25rem; color: var(--rr-text-muted); cursor:pointer; }

        .rr-highlight-box {
            background: linear-gradient(135deg, var(--rr-primary-light) 0%, #fff 100%);
            border-radius: 12px;
            padding: 1rem;
            border-left: 4px solid var(--rr-primary);
            margin-bottom: 1rem;
        }

        .rr-pill {
            display:inline-flex; align-items:center; gap:8px;
            padding: .35rem .7rem;
            border-radius: 999px;
            border: 1px solid var(--rr-border);
            background:#fff;
            color: var(--rr-text-dark);
            font-size: .78rem;
            font-weight: 600;
        }

        .rr-map {
            width:100%;
            height: 280px;
            border-radius: 12px;
            border: 1px solid var(--rr-border);
            overflow:hidden;
            background: #f8fafc;
        }

        pre.rr-pre {
            background:#0b1220;
            color:#d6e4ff;
            padding: 12px;
            border-radius: 12px;
            overflow:auto;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,.08);
        }

        /* Select2 */
        .rr-wrap .select2-container { width: 100% !important; }
        .rr-wrap .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid var(--rr-border);
            border-radius: 10px;
            display:flex; align-items:center;
            padding-left: 10px;
        }
        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: var(--rr-text-dark);
        }

        @media (max-width: 768px) {
            .rr-wrap { padding: 10px; }
            .rr-table-footer { flex-direction:column; gap: 1rem; align-items:flex-start; }
        }
    </style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="rr-wrap">
    <div class="rr-container">

        <!-- Header -->
        <div class="rr-flex-row rr-mb-4">
            <div>
                <h3 class="rr-mb-2">Attendance Analytics</h3>
                <span class="rr-text-muted rr-small">Filter by month, range, or day and inspect check-in/out locations</span>
            </div>

            <button class="rr-btn rr-btn-light" type="button" id="att-today">
                <i class="fa-solid fa-calendar-day rr-me-2"></i>Today
            </button>
        </div>

        <!-- Stats -->
        <div class="rr-stats-grid">
            <div class="rr-stat-card">
                <div class="rr-stat-header">
                    <div>
                        <div class="rr-stat-value" id="att-stat-total">—</div>
                        <div class="rr-stat-label">Logs total</div>
                    </div>
                    <div class="rr-stat-icon" style="background: var(--rr-primary-light); color: var(--rr-primary);">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                </div>
                <div class="rr-small rr-text-muted rr-fw-medium" id="att-stat-range">—</div>
            </div>

            <div class="rr-stat-card">
                <div class="rr-stat-header">
                    <div>
                        <div class="rr-stat-value" id="att-stat-emp">—</div>
                        <div class="rr-stat-label">Employees</div>
                    </div>
                    <div class="rr-stat-icon" style="background: rgba(147,194,28,.18); color: #6a8f14;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="rr-small rr-text-muted rr-fw-medium">Unique in range</div>
            </div>

            <div class="rr-stat-card">
                <div class="rr-stat-header">
                    <div>
                        <div class="rr-stat-value" id="att-stat-in">—</div>
                        <div class="rr-stat-label">Check-ins</div>
                    </div>
                    <div class="rr-stat-icon" style="background: rgba(16,185,129,.14); color: #059669;">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </div>
                </div>
                <div class="rr-small rr-text-muted rr-fw-medium" id="att-stat-miss-in">—</div>
            </div>

            <div class="rr-stat-card">
                <div class="rr-stat-header">
                    <div>
                        <div class="rr-stat-value" id="att-stat-out">—</div>
                        <div class="rr-stat-label">Check-outs</div>
                    </div>
                    <div class="rr-stat-icon" style="background: rgba(245,158,11,.15); color:#b45309;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                </div>
                <div class="rr-small rr-text-muted rr-fw-medium" id="att-stat-avg">—</div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="rr-main-card">
            <div class="rr-filter-bar">
                <div class="rr-filter-grid">
                    <!-- Search -->
                    <div class="rr-filter-group" style="grid-column: span 2; min-width: 250px;">
                        <label class="rr-filter-label">Search employee</label>
                        <div class="rr-input-wrapper">
                            <i class="fa-solid fa-search rr-input-icon"></i>
                            <input id="att-q" type="text" class="rr-form-control rr-has-icon" placeholder="Name / Lastname...">
                        </div>
                    </div>

                    <!-- Employee -->
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Employee</label>
                        <select id="att-employee" class="rr-form-select">
                            <option value="">All</option>
                            @foreach(($employees ?? []) as $e)
                                @php $full = trim(($e->name ?? '').' '.($e->lastname ?? '')); @endphp
                                <option value="{{ $e->id }}">{{ $full ?: ('Employee #'.$e->id) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Status (in/out)</label>
                        <select id="att-status" class="rr-form-select">
                            <option value="">All</option>
                            <option value="login">login</option>
                            <option value="logout">logout</option>
                            <option value="work_start">work_start</option>
                            <option value="work_end">work_end</option>
                            <option value="break_start">break_start</option>
                            <option value="break_end">break_end</option>
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Month</label>
                        <input id="att-month" type="month" class="rr-form-control">
                    </div>

                    <!-- From / To -->
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">From</label>
                        <input id="att-from" type="date" class="rr-form-control">
                    </div>
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">To</label>
                        <input id="att-to" type="date" class="rr-form-control">
                    </div>

                    <!-- Reset -->
                    <div class="rr-filter-group">
                        <button id="att-reset" class="rr-btn rr-btn-light w-100" type="button">
                            <i class="fa-solid fa-rotate-left rr-me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="rr-table-wrapper">
                <table class="rr-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 2rem;">Employee</th>
                            <th>Date</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Reason / Meta</th>
                            <th class="rr-text-right" style="padding-right: 2rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="att-tbody">
                        <tr>
                            <td colspan="7" class="rr-text-center" style="padding:2rem;color:var(--rr-text-muted);">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="rr-table-footer">
                <div class="rr-text-muted rr-small rr-fw-medium" id="att-meta">Loading...</div>
                <div class="rr-flex-row rr-gap-2" style="justify-content:flex-end;">
                    <button class="rr-btn rr-btn-light rr-btn-sm" id="att-prev" type="button">Prev</button>
                    <button class="rr-btn rr-btn-light rr-btn-sm" id="att-next" type="button">Next</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Sidebar -->
<div class="rr-sidebar-backdrop" id="attBackdrop"></div>
<div class="rr-sidebar" id="attSidebar">
    <div class="rr-sidebar-header">
        <div>
            <div class="rr-fw-bold" style="font-size: 1.05rem;">Attendance Details</div>
            <small class="rr-text-muted" id="attSideSub">—</small>
        </div>
        <button class="rr-close-btn" id="attCloseBtn" type="button"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="rr-sidebar-body">
        <div class="rr-highlight-box">
            <div class="rr-fw-bold" id="attSideEmp">—</div>
            <div class="rr-small rr-text-muted" id="attSideDate">—</div>

            <div style="margin-top: .75rem; display:flex; flex-wrap:wrap; gap:.5rem;">
                <span class="rr-pill"><i class="fa-solid fa-right-to-bracket"></i> <span id="attSideIn">—</span></span>
                <span class="rr-pill"><i class="fa-solid fa-right-from-bracket"></i> <span id="attSideOut">—</span></span>
                <span class="rr-pill"><i class="fa-solid fa-hourglass-half"></i> <span id="attSideDur">—</span></span>
            </div>

            <div style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:.5rem;">
                <span class="rr-pill"><i class="fa-solid fa-tag"></i> IN: <span id="attSideInStatus">—</span></span>
                <span class="rr-pill"><i class="fa-solid fa-tag"></i> OUT: <span id="attSideOutStatus">—</span></span>
            </div>
        </div>

        <div class="rr-mb-4">
            <div class="rr-fw-bold rr-mb-2">Location (Map)</div>
            <div class="rr-map" id="attMap"></div>
            <div class="rr-small rr-text-muted" style="margin-top: .5rem;" id="attMapHint">—</div>
        </div>

        <div class="rr-mb-4">
            <div class="rr-fw-bold rr-mb-2">Reason</div>
            <div class="rr-small" id="attSideReason" style="color: var(--rr-text-dark);">—</div>
        </div>

        <div class="rr-mb-4">
            <div class="rr-fw-bold rr-mb-2">Meta (IP / UA / JSON)</div>
            <div class="rr-small rr-text-muted" id="attSideIpUa">—</div>
            <pre class="rr-pre rr-pre" id="attSideMeta">{}</pre>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- jQuery + Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @if(!empty($googleMapsKey))
        <script>
            window.__ATT_MAPS_READY = false;
            window.initAttendanceMaps = function () { window.__ATT_MAPS_READY = true; };
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initAttendanceMaps" async defer></script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const API = {
                fetch: "{{ route('admin.attendance.analytics.fetch') }}"
            };

            const els = {
                q: document.getElementById('att-q'),
                employee: document.getElementById('att-employee'),
                status: document.getElementById('att-status'),
                month: document.getElementById('att-month'),
                from: document.getElementById('att-from'),
                to: document.getElementById('att-to'),
                reset: document.getElementById('att-reset'),
                today: document.getElementById('att-today'),

                tbody: document.getElementById('att-tbody'),
                meta: document.getElementById('att-meta'),
                prev: document.getElementById('att-prev'),
                next: document.getElementById('att-next'),

                statTotal: document.getElementById('att-stat-total'),
                statEmp: document.getElementById('att-stat-emp'),
                statIn: document.getElementById('att-stat-in'),
                statOut: document.getElementById('att-stat-out'),
                statRange: document.getElementById('att-stat-range'),
                statMissIn: document.getElementById('att-stat-miss-in'),
                statAvg: document.getElementById('att-stat-avg'),

                // Sidebar
                backdrop: document.getElementById('attBackdrop'),
                sidebar: document.getElementById('attSidebar'),
                closeBtn: document.getElementById('attCloseBtn'),

                sideSub: document.getElementById('attSideSub'),
                sideEmp: document.getElementById('attSideEmp'),
                sideDate: document.getElementById('attSideDate'),
                sideIn: document.getElementById('attSideIn'),
                sideOut: document.getElementById('attSideOut'),
                sideDur: document.getElementById('attSideDur'),
                sideInStatus: document.getElementById('attSideInStatus'),
                sideOutStatus: document.getElementById('attSideOutStatus'),
                sideReason: document.getElementById('attSideReason'),
                sideIpUa: document.getElementById('attSideIpUa'),
                sideMeta: document.getElementById('attSideMeta'),

                map: document.getElementById('attMap'),
                mapHint: document.getElementById('attMapHint'),
            };

            const state = { page: 1, perPage: 20, total: 0, hasMore: false, loading: false, timer: null };
            let lastRows = []; // keep for sidebar

            // Select2 init
            $(els.employee).select2({ width: '100%', placeholder: 'All', allowClear: true })
                .on('change', () => debouncedReload());

            function escapeHtml(s) {
                return String(s ?? '')
                    .replaceAll('&','&amp;')
                    .replaceAll('<','&lt;')
                    .replaceAll('>','&gt;')
                    .replaceAll('"','&quot;')
                    .replaceAll("'","&#039;");
            }

            function initials(name) {
                const n = String(name || '').trim();
                if (!n) return '?';
                const p = n.split(/\s+/).filter(Boolean);
                return ((p[0]?.[0] || '') + (p[1]?.[0] || '')).toUpperCase() || (p[0]?.[0] || '?').toUpperCase();
            }

            function buildFetchUrl() {
                const u = new URL(API.fetch, window.location.origin);
                const params = {
                    q: (els.q.value || '').trim(),
                    employee_id: $(els.employee).val() || '',
                    status: els.status.value || '',
                    month: els.month.value || '',
                    from: els.from.value || '',
                    to: els.to.value || '',
                    page: state.page,
                    per_page: state.perPage,
                };
                Object.entries(params).forEach(([k,v]) => { if (v !== '' && v != null) u.searchParams.set(k, v); });
                return u.toString();
            }

            function renderEmpty(msg) {
                els.tbody.innerHTML = `<tr><td colspan="7" class="rr-text-center" style="padding:2rem;color:var(--rr-text-muted);">${escapeHtml(msg)}</td></tr>`;
            }

            function setStats(j) {
                const s = j?.stats || {};
                const r = j?.range || {};
                els.statTotal.textContent = String(s.total ?? 0);
                els.statEmp.textContent = String(s.unique_employees ?? 0);
                els.statIn.textContent = String(s.with_check_in ?? 0);
                els.statOut.textContent = String(s.with_check_out ?? 0);

                els.statRange.textContent = (r.from && r.to) ? `Range: ${r.from} → ${r.to}` : '—';
                els.statMissIn.textContent = `Missing IN: ${s.missing_check_in ?? 0} / Missing OUT: ${s.missing_check_out ?? 0}`;
                els.statAvg.textContent = `Avg hours: ${s.avg_hours ?? 0}`;
            }

            function render(rows, pagination) {
                const data = Array.isArray(rows) ? rows : [];
                lastRows = data;

                state.total = pagination?.total || 0;
                state.hasMore = !!pagination?.has_more;

                if (!data.length) {
                    renderEmpty('No results found');
                    els.meta.textContent = '0 entries';
                    els.prev.disabled = state.page <= 1;
                    els.next.disabled = !state.hasMore;
                    return;
                }

                const start = ((state.page - 1) * state.perPage) + 1;
                const end = Math.min(state.page * state.perPage, state.total);
                els.meta.textContent = `Showing ${start}-${end} of ${state.total}`;

                els.prev.disabled = state.page <= 1;
                els.next.disabled = !state.hasMore;

                els.tbody.innerHTML = data.map(row => {
                    const emp = row.employee_name || '—';
                    const av = initials(emp);
                    const date = row.date || '—';

                    const inTime = row.check_in ? row.check_in.slice(11, 19) : '—';
                    const outTime = row.check_out ? row.check_out.slice(11, 19) : '—';

                    const inStatus = row.check_in_status || '—';
                    const outStatus = row.check_out_status || '—';

                    const reason = row.reason ? String(row.reason) : '';
                    const ip = row.ip ? `IP: ${row.ip}` : '';
                    const ua = row.ua ? `UA: ${row.ua}` : '';
                    const metaShort = [reason, ip, ua].filter(Boolean).join(' | ');
                    const metaDisp = metaShort.length > 90 ? metaShort.slice(0, 90) + '…' : metaShort;

                    return `
                        <tr>
                            <td style="padding-left:2rem;">
                                <div class="rr-user-cell">
                                    <div class="rr-avatar">${escapeHtml(av)}</div>
                                    <div>
                                        <div class="rr-fw-medium rr-small">${escapeHtml(emp)}</div>
                                        <div class="rr-small rr-text-muted">#${escapeHtml(row.employee_id)}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="rr-small rr-text-muted">${escapeHtml(date)}</td>
                            <td>
                                <div class="rr-small">${escapeHtml(inTime)}</div>
                                <div class="rr-small rr-text-muted">${escapeHtml(row.check_in_location || '')}</div>
                            </td>
                            <td>
                                <div class="rr-small">${escapeHtml(outTime)}</div>
                                <div class="rr-small rr-text-muted">${escapeHtml(row.check_out_location || '')}</div>
                            </td>
                            <td>
                                <div class="rr-pill" style="margin-bottom:6px;"><i class="fa-solid fa-tag"></i> IN: ${escapeHtml(inStatus)}</div><br>
                                <div class="rr-pill"><i class="fa-solid fa-tag"></i> OUT: ${escapeHtml(outStatus)}</div>
                            </td>
                            <td title="${escapeHtml(metaShort)}">
                                <span class="rr-small rr-text-muted rr-text-truncate">${escapeHtml(metaDisp || '—')}</span>
                            </td>
                            <td class="rr-text-right" style="padding-right:2rem;">
                                <button class="rr-btn-icon att-view" type="button" data-id="${escapeHtml(row.id)}" title="View details">
                                    <i class="fa-solid fa-location-dot"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                document.querySelectorAll('.att-view').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = Number(btn.getAttribute('data-id'));
                        const row = lastRows.find(x => Number(x.id) === id);
                        if (row) openSidebar(row);
                    });
                });
            }

            function openSidebar(row) {
                els.sidebar.classList.add('active');
                els.backdrop.classList.add('active');

                els.sideEmp.textContent = row.employee_name || '—';
                els.sideDate.textContent = row.date || '—';
                els.sideSub.textContent = `Log #${row.id}`;

                els.sideIn.textContent = row.check_in ? row.check_in : '—';
                els.sideOut.textContent = row.check_out ? row.check_out : '—';
                els.sideDur.textContent = (row.duration_hours != null) ? `${row.duration_hours} h` : '—';

                els.sideInStatus.textContent = row.check_in_status || '—';
                els.sideOutStatus.textContent = row.check_out_status || '—';

                els.sideReason.textContent = row.reason ? String(row.reason) : '—';

                const ip = row.ip ? `IP: ${row.ip}` : '';
                const ua = row.ua ? `UA: ${row.ua}` : '';
                els.sideIpUa.textContent = [ip, ua].filter(Boolean).join(' | ') || '—';

                els.sideMeta.textContent = JSON.stringify(row.meta || {}, null, 2);

                renderMap(row);
            }

            function closeSidebar() {
                els.sidebar.classList.remove('active');
                els.backdrop.classList.remove('active');
            }

            els.closeBtn.addEventListener('click', closeSidebar);
            els.backdrop.addEventListener('click', closeSidebar);

            // Google Map render
            let mapInstance = null;
            let mapMarkers = [];

            function clearMarkers() {
                mapMarkers.forEach(m => m.setMap(null));
                mapMarkers = [];
            }

            function renderMap(row) {
                els.mapHint.textContent = '—';

                const hasMaps = (typeof google !== 'undefined' && google?.maps) || (window.__ATT_MAPS_READY === true);
                if (!hasMaps) {
                    els.map.innerHTML = `<div style="padding:14px;" class="rr-small rr-text-muted">
                        Google Maps not loaded. Set GOOGLE_MAPS_KEY / services.google.maps_key.
                    </div>`;
                    els.mapHint.textContent = 'No map key or script not loaded.';
                    return;
                }

                // Reset container content if previously replaced
                if (!els.map.querySelector('.gm-style')) {
                    els.map.innerHTML = '';
                }

                const inLat = row.check_in_lat != null ? Number(row.check_in_lat) : null;
                const inLng = row.check_in_lng != null ? Number(row.check_in_lng) : null;
                const outLat = row.check_out_lat != null ? Number(row.check_out_lat) : null;
                const outLng = row.check_out_lng != null ? Number(row.check_out_lng) : null;

                const points = [];
                if (Number.isFinite(inLat) && Number.isFinite(inLng)) points.push({ lat: inLat, lng: inLng, label: 'IN' });
                if (Number.isFinite(outLat) && Number.isFinite(outLng)) points.push({ lat: outLat, lng: outLng, label: 'OUT' });

                if (!points.length) {
                    els.map.innerHTML = `<div style="padding:14px;" class="rr-small rr-text-muted">No location saved for this log.</div>`;
                    els.mapHint.textContent = 'check_in_lat/lng or check_out_lat/lng are NULL.';
                    return;
                }

                const center = points[0];

                if (!mapInstance) {
                    mapInstance = new google.maps.Map(els.map, {
                        center,
                        zoom: 14,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false
                    });
                } else {
                    mapInstance.setCenter(center);
                    mapInstance.setZoom(14);
                }

                clearMarkers();

                const bounds = new google.maps.LatLngBounds();

                points.forEach(p => {
                    const marker = new google.maps.Marker({
                        position: { lat: p.lat, lng: p.lng },
                        map: mapInstance,
                        title: p.label
                    });
                    mapMarkers.push(marker);
                    bounds.extend(marker.getPosition());
                });

                if (points.length > 1) {
                    mapInstance.fitBounds(bounds);
                }

                const inLoc = row.check_in_location ? `IN: ${row.check_in_location}` : '';
                const outLoc = row.check_out_location ? `OUT: ${row.check_out_location}` : '';
                els.mapHint.textContent = [inLoc, outLoc].filter(Boolean).join(' | ') || 'Coordinates available.';
            }

            async function load() {
                if (state.loading) return;
                state.loading = true;

                renderEmpty('Loading...');
                els.meta.textContent = 'Loading...';

                try {
                    const res = await fetch(buildFetchUrl(), { headers: { 'Accept': 'application/json' } });
                    const j = await res.json().catch(() => ({}));
                    if (!res.ok || !j.ok) throw new Error(j?.message || 'Fetch failed');

                    setStats(j);
                    render(j.rows, j.pagination);
                } catch (e) {
                    renderEmpty('Failed to load');
                    els.meta.textContent = '';
                } finally {
                    state.loading = false;
                }
            }

            function debouncedReload() {
                clearTimeout(state.timer);
                state.timer = setTimeout(() => {
                    state.page = 1;
                    load();
                }, 250);
            }

            // Inputs
            els.q.addEventListener('input', debouncedReload);
            els.status.addEventListener('change', debouncedReload);

            // Month overrides range for user convenience:
            els.month.addEventListener('change', () => {
                if (els.month.value) { els.from.value = ''; els.to.value = ''; }
                debouncedReload();
            });
            els.from.addEventListener('change', () => {
                if (els.from.value || els.to.value) els.month.value = '';
                debouncedReload();
            });
            els.to.addEventListener('change', () => {
                if (els.from.value || els.to.value) els.month.value = '';
                debouncedReload();
            });

            els.reset.addEventListener('click', () => {
                els.q.value = '';
                $(els.employee).val(null).trigger('change');
                els.status.value = '';
                els.month.value = '';
                els.from.value = '';
                els.to.value = '';
                state.page = 1;
                load();
            });

            els.today.addEventListener('click', () => {
                const d = new Date();
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth()+1).padStart(2,'0');
                const dd = String(d.getDate()).padStart(2,'0');
                const today = `${yyyy}-${mm}-${dd}`;

                els.month.value = '';
                els.from.value = today;
                els.to.value = today;
                state.page = 1;
                load();
            });

            els.prev.addEventListener('click', () => {
                if (state.page <= 1) return;
                state.page--;
                load();
            });

            els.next.addEventListener('click', () => {
                if (!state.hasMore) return;
                state.page++;
                load();
            });

            // Initial default month = current month
            (function setDefaultMonth() {
                const d = new Date();
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth()+1).padStart(2,'0');
                els.month.value = `${yyyy}-${mm}`;
            })();

            load();
        });
    </script>
@endsection
