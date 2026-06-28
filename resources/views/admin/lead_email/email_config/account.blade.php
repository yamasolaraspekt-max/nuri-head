@extends('admin.layouts.app')
@section('title', 'Lead E-Mail-Konten')

@php
    $totalCount = $accounts->count();
    $publishedCount = $accounts->where('status', 'Published')->count();
    $unpublishedCount = $accounts->where('status', '!=', 'Published')->count();
    $testedCount = $accounts->filter(fn($a) => filled($a->test))->count();
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
    --danger-light:#fef2f2;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap{font-family:Inter,system-ui,-apple-system,sans-serif;color:var(--text-main);max-width:1500px;margin:20px auto;padding:39px;padding-right:79px;}
  .oc-header{margin-bottom:18px;margin-top:103px;}
  .oc-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
  .oc-breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:10px;font-size:13px;color:var(--text-muted);}
  .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
  .oc-breadcrumb span.current{color:#111827;font-weight:800;}
  .oc-btn{background:var(--primary);color:#fff;border:none;padding:10px 16px;border-radius:10px;font-weight:900;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;gap:8px;text-decoration:none;}
  .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}
  .oc-btn-soft{background:#fff;color:var(--text-main);border:1px solid var(--border);padding:10px 14px;border-radius:10px;font-weight:800;cursor:pointer;transition:var(--transition);text-decoration:none;}
  .oc-btn-ic{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;transition:var(--transition);text-decoration:none;}
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-analytics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
  @media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media(max-width:700px){.oc-analytics{grid-template-columns:1fr;}}
  .oc-stat{background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:16px;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:12px;min-height:92px;}
  .oc-stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.published{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.type{background:#f3f4f6;color:#6b7280}
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}
  .oc-toolbar{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;justify-content:space-between;margin-bottom:16px;box-shadow:var(--shadow-sm);}
  .oc-toolbar-left,.oc-toolbar-right{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
  .oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:170px;}
  .oc-filter-block.search{flex:1;min-width:280px;}
  .oc-filter-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-input{background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:10px 12px 10px 36px;font-size:14px;outline:none;transition:var(--transition);min-width:240px;width:100%;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center;background-size:16px;}
  .oc-input:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}
  .oc-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow-sm);overflow:hidden;}
  .oc-list-head{display:grid;grid-template-columns:80px minmax(180px,1fr) minmax(180px,1fr) 130px 150px 150px 210px;gap:14px;align-items:center;padding:16px 16px 10px 16px;color:var(--text-muted);font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;}
  @media(max-width:1280px){.oc-list-head{display:none;}}
  .oc-list{display:flex;flex-direction:column;gap:12px;padding:0 0 16px 0;}
  .oc-item{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);transition:var(--transition);overflow:hidden;margin:0 16px;}
  .oc-item:hover{border-color:var(--primary);box-shadow:var(--shadow);}
  .oc-item-row{padding:16px;display:grid;gap:16px;align-items:center;grid-template-columns:80px minmax(180px,1fr) minmax(180px,1fr) 130px 150px 150px 210px;}
  @media(max-width:1280px){.oc-item-row{grid-template-columns:1fr;}}
  .oc-cell-title{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;display:none;}
  @media(max-width:1280px){.oc-cell-title{display:block;}}
  .oc-id-badge{display:inline-flex;align-items:center;justify-content:center;min-width:54px;height:36px;padding:0 12px;border-radius:10px;background:var(--blue-light);color:var(--blue);font-size:13px;font-weight:900;}
  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .oc-status-pill{display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;white-space:nowrap;}
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;}
  .oc-modal-backdrop{position:fixed;inset:0;z-index:1200;background:rgba(17,24,39,.55);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .22s ease;display:flex;align-items:center;justify-content:center;padding:18px;}
  .oc-modal-backdrop.open{opacity:1;pointer-events:auto;}
  .oc-modal{width:100%;max-width:760px;background:#fff;border:1px solid rgba(229,231,235,.9);border-radius:16px;box-shadow:var(--shadow);transform:translateY(12px) scale(.985);transition:transform .22s ease;overflow:hidden;}
  .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}
  .oc-modal-h{display:flex;gap:12px;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa;}
  .oc-modal-ttl{font-weight:900;font-size:16px;line-height:1.2;margin:0;color:#111827;}
  .oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto;}
  .oc-modal-f{padding:14px 18px;border-top:1px solid var(--border);background:#fafafa;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}
  .oc-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;}
  @media(max-width:760px){.oc-form-grid{grid-template-columns:1fr;}}
  .oc-form-group{margin-bottom:16px;}
  .oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
  .oc-input-form,.oc-select{width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:#fff;font-size:14px;outline:none;transition:var(--transition);}
  .oc-input-form:focus,.oc-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}
  .oc-empty{text-align:center;padding:60px;color:var(--text-muted);background:#fff;border:1px dashed var(--border);border-radius:16px;margin:16px;}
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
    <div class="oc-header">
        <div class="oc-titlebar">
            <div>
                <div class="oc-title">LEAD E-MAIL-KONTEN</div>
                <div class="oc-sub">Verwalten Sie IMAP-Konten, Tests und Aktivierung zentral.</div>
                <div class="oc-breadcrumb">
                    <a href="{{ url('/') }}">Home</a>
                    <span>›</span>
                    <span class="current">Lead E-Mail-Konten</span>
                </div>
            </div>

            <div>
                <button type="button" class="oc-btn" onclick="openModal('createAccountModal')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Neues Konto
                </button>
            </div>
        </div>
    </div>

    <div class="oc-analytics">
        <div class="oc-stat">
            <div class="oc-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </div>
            <div>
                <div class="oc-stat-label">Gesamt</div>
                <div class="oc-stat-value">{{ $totalCount }}</div>
                <div class="oc-stat-sub">Alle Konten</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon published">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <div>
                <div class="oc-stat-label">Aktiv</div>
                <div class="oc-stat-value">{{ $publishedCount }}</div>
                <div class="oc-stat-sub">Veröffentlichte Konten</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon unpublished">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="oc-stat-label">Inaktiv</div>
                <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                <div class="oc-stat-sub">Nicht aktive Konten</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon type">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.971-4.029 9-9 9s-9-4.029-9-9 4.029-9 9-9 9 4.029 9 9z"/></svg>
            </div>
            <div>
                <div class="oc-stat-label">Getestet</div>
                <div class="oc-stat-value">{{ $testedCount }}</div>
                <div class="oc-stat-sub">Mit Testergebnis</div>
            </div>
        </div>
    </div>

    <form action="{{ route('lead-email-accounts.index') }}" method="GET" class="oc-toolbar">
        <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
                <label class="oc-filter-label">Suche</label>
                <input
                    type="text"
                    name="search"
                    class="oc-input"
                    placeholder="Suche nach Label oder E-Mail"
                    value="{{ request('search') }}"
                >
            </div>
        </div>

        <div class="oc-toolbar-right">
            <button class="oc-btn-soft" type="submit">Suchen</button>
            @if(request('search'))
                <a href="{{ route('lead-email-accounts.index') }}" class="oc-btn-soft">Zurücksetzen</a>
            @endif
        </div>
    </form>

    <div class="oc-card">
        <div class="oc-list-head">
            <div>ID</div>
            <div>Label</div>
            <div>E-Mail / Host</div>
            <div>Status</div>
            <div>Letzter Test</div>
            <div>Port</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="oc-list">
            @forelse($accounts as $account)
                @php
                    $statusClass = $account->status === 'Published' ? 'green' : 'orange';
                    $statusLabel = $account->status === 'Published' ? 'Aktiv' : 'Inaktiv';
                @endphp

                <div class="oc-item">
                    <div class="oc-item-row">
                        <div class="oc-cell">
                            <div class="oc-cell-title">ID</div>
                            <span class="oc-id-badge">#{{ $account->id }}</span>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Label</div>
                            <div class="oc-main">
                                <div class="oc-ttl">{{ $account->label }}</div>
                                <div class="oc-subt">Verschlüsselung: {{ $account->encryption }}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">E-Mail / Host</div>
                            <div class="oc-main">
                                <div class="oc-ttl">{{ $account->email }}</div>
                                <div class="oc-subt">{{ $account->host }}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Status</div>
                            <a href="javascript:void(0);"
                               class="toggle-status oc-status-pill {{ $statusClass }}"
                               data-id="{{ $account->id }}"
                               data-status="{{ $account->status }}">
                                {{ $statusLabel }}
                            </a>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Letzter Test</div>
                            <div id="test-result-{{ $account->id }}" class="oc-main">
                                <div class="oc-subt">{!! $account->test ?? '<span class="text-muted">Nicht getestet</span>' !!}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Port</div>
                            <div class="oc-main">
                                <div class="oc-ttl">{{ $account->port }}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Aktionen</div>
                            <div class="oc-actions">
                                <button type="button"
                                        class="oc-btn-ic primary js-open-edit"
                                        title="Bearbeiten"
                                        data-id="{{ $account->id }}"
                                        data-label="{{ $account->label }}"
                                        data-email="{{ $account->email }}"
                                        data-host="{{ $account->host }}"
                                        data-port="{{ $account->port }}"
                                        data-encryption="{{ $account->encryption }}"
                                        data-status="{{ $account->status }}">
                                    <i class="feather icon-edit"></i>
                                </button>

                                <button type="button"
                                        class="oc-btn-ic success test-email-btn"
                                        title="Verbindung testen"
                                        data-id="{{ $account->id }}">
                                    <i class="feather icon-send"></i>
                                </button>

                                <form action="{{ route('lead-email-accounts.destroy', $account->id) }}"
                                      method="POST"
                                      class="delete-form d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="oc-btn-ic danger" title="Löschen">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="oc-empty">Keine E-Mail-Konten gefunden.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="oc-modal-backdrop" id="createAccountModal">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Neues E-Mail-Konto hinzufügen</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('createAccountModal')">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <form action="{{ route('lead-email-accounts.store') }}" method="POST">
            @csrf
            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    @include('admin.lead_email.email_config._form', ['data' => null])
                </div>
            </div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('createAccountModal')">Abbrechen</button>
                <button type="submit" class="oc-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

<div class="oc-modal-backdrop" id="editAccountModal">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">E-Mail-Konto bearbeiten</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('editAccountModal')">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <form id="editAccountForm" method="POST">
            @csrf
            @method('PUT')
            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    <div class="oc-form-group">
                        <label class="oc-label">Label</label>
                        <input type="text" name="label" id="edit_label" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">E-Mail</label>
                        <input type="email" name="email" id="edit_email" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Passwort</label>
                        <input type="password" name="password" id="edit_password" class="oc-input-form" placeholder="•••• (leer lassen = unverändert)" autocomplete="new-password">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Host</label>
                        <input type="text" name="host" id="edit_host" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Port</label>
                        <input type="number" name="port" id="edit_port" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Verschlüsselung</label>
                        <select name="encryption" id="edit_encryption" class="oc-select" required>
                            <option value="ssl">ssl</option>
                            <option value="tls">tls</option>
                            <option value="none">none</option>
                        </select>
                    </div>

                    <div class="oc-form-group" style="grid-column:1/-1;">
                        <label class="oc-label">Status</label>
                        <select name="status" id="edit_status" class="oc-select" required>
                            <option value="Published">Published</option>
                            <option value="Unpublished">Unpublished</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('editAccountModal')">Abbrechen</button>
                <button type="submit" class="oc-btn">Aktualisieren</button>
            </div>
        </form>
    </div>
</div>
@endsection

@once
@push('scripts')
<script>
function openModal(id){
    document.getElementById(id)?.classList.add('open');
}
function closeModal(id){
    document.getElementById(id)?.classList.remove('open');
}

document.addEventListener('click', function(e){
    if(e.target.classList.contains('oc-modal-backdrop')){
        e.target.classList.remove('open');
    }
});

document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
});

function showOcToast(kind, title, msg){
    let wrap = document.getElementById('oc-toast-wrap');
    if(!wrap){
        wrap = document.createElement('div');
        wrap.id = 'oc-toast-wrap';
        wrap.className = 'oc-toast-wrap';
        document.body.appendChild(wrap);
    }

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
    setTimeout(() => { try { el.remove(); } catch(e){} }, 4500);
}

$(document).ready(function(){
    @if(Session::has('updated_msg'))
        showOcToast('ok', 'Aktualisiert', @json(session('updated_msg')));
    @endif
    @if(Session::has('save_msg'))
        showOcToast('ok', 'Gespeichert', @json(session('save_msg')));
    @endif
    @if(Session::has('delete_msg'))
        showOcToast('bad', 'Gelöscht', @json(session('delete_msg')));
    @endif

    $('.toggle-status').click(function() {
        const el = $(this);
        const id = el.data('id');

        $.ajax({
            url: '/admin/lead-email-accounts/toggle-status/' + id,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                const active = response.new_status === 'Published';
                el.data('status', response.new_status);
                el.removeClass('green orange');
                el.addClass(active ? 'green' : 'orange');
                el.text(active ? 'Aktiv' : 'Inaktiv');
                showOcToast('ok', 'Status', 'Status wurde aktualisiert.');
            },
            error: function() {
                showOcToast('bad', 'Fehler', 'Status konnte nicht aktualisiert werden.');
            }
        });
    });

    $('.test-email-btn').on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Verbindung testen?',
            text: 'Die IMAP-Verbindung wird geprüft.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, testen',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/admin/lead-email-accounts/test/' + id, {
                    _token: '{{ csrf_token() }}'
                }, function (response) {
                    if (response.success) {
                        showOcToast('ok', 'Erfolgreich', response.message);
                        $('#test-result-' + id).html('<div class="oc-subt">✅ Erfolgreich</div>');
                    } else {
                        showOcToast('bad', 'Fehlgeschlagen', response.message);
                        $('#test-result-' + id).html('<div class="oc-subt">❌ Fehlgeschlagen</div>');
                    }
                }).fail(function (xhr) {
                    showOcToast('bad', 'Serverfehler', xhr.responseText || 'Verbindung konnte nicht geprüft werden.');
                    $('#test-result-' + id).html('<div class="oc-subt">❌ Fehler beim Verbinden</div>');
                });
            }
        });
    });

    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.js-open-edit');
        if(!btn) return;

        const id = btn.dataset.id;
        document.getElementById('editAccountForm').action = `/admin/lead-email-accounts/${id}`;
        document.getElementById('edit_label').value = btn.dataset.label || '';
        document.getElementById('edit_email').value = btn.dataset.email || '';
        document.getElementById('edit_password').value = ''; // Passwort nie vorbefuellen (nur bei Eingabe neu setzen)
        document.getElementById('edit_host').value = btn.dataset.host || '';
        document.getElementById('edit_port').value = btn.dataset.port || '';
        document.getElementById('edit_encryption').value = btn.dataset.encryption || 'ssl';
        document.getElementById('edit_status').value = btn.dataset.status || 'Published';

        openModal('editAccountModal');
    });
});
</script>
@endpush
@endonce