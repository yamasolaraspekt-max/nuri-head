@extends('admin.layouts.app')
@section('title', 'User Rollen')

@once
  @push('style')
    <style>
      :root{
        --ur-bg:#f3f4f6;--ur-card:#fff;--ur-text:#111827;--ur-muted:#6b7280;--ur-border:#e5e7eb;
        --ur-primary:var(--sa-accent);--ur-primary-dark:var(--sa-accent-hover);--ur-primary-soft:var(--sa-accent-light);
        --ur-blue:#74b2d4;--ur-blue-soft:#eff6ff;--ur-green:#10b981;--ur-green-soft:#ecfdf5;
        --ur-red:#ef4444;--ur-red-soft:#fef2f2;--ur-yellow:#f59e0b;--ur-yellow-soft:#fffbeb;
        --ur-shadow:0 10px 25px -14px rgb(0 0 0 / .28),0 4px 10px -6px rgb(0 0 0 / .14);
        --ur-radius:16px;--ur-trans:all .2s ease;
      }
      .ur-wrap{font-family:Inter,system-ui,-apple-system,sans-serif;color:var(--ur-text)}
      .ur-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px}
      .ur-title{font-size:26px;font-weight:900;letter-spacing:-.03em;color:#111827}
      .ur-sub{font-size:14px;color:var(--ur-muted);margin-top:4px}
      .ur-breadcrumb{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:10px;font-size:13px;color:var(--ur-muted)}
      .ur-breadcrumb a{color:var(--ur-muted);text-decoration:none;font-weight:800}.ur-breadcrumb a:hover{color:#111827}.ur-breadcrumb .current{color:#111827;font-weight:900}
      .ur-btn{border:0;background:var(--ur-primary);color:#fff;border-radius:12px;padding:10px 15px;font-weight:900;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:var(--ur-trans);text-decoration:none}.ur-btn:hover{background:var(--ur-primary-dark);color:#fff;text-decoration:none}.ur-btn:disabled{opacity:.6;cursor:not-allowed}
      .ur-btn-soft{border:1px solid var(--ur-border);background:#fff;color:#111827;border-radius:12px;padding:10px 14px;font-weight:900;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:var(--ur-trans);text-decoration:none}.ur-btn-soft:hover{background:#f9fafb;color:#111827;text-decoration:none}.ur-btn-red{background:var(--ur-red);color:#fff}.ur-btn-red:hover{background:#dc2626;color:#fff}
      .ur-btn-icon{width:36px;height:36px;border-radius:10px;border:1px solid var(--ur-border);background:#fff;color:var(--ur-muted);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--ur-trans)}.ur-btn-icon:hover{background:#f9fafb;color:#111827}.ur-btn-icon.primary{background:var(--ur-primary-soft);border-color:#d9edb1;color:var(--ur-primary-dark)}.ur-btn-icon.red{background:var(--ur-red-soft);border-color:#fecaca;color:var(--ur-red)}
      .ur-analytics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}@media(max-width:1100px){.ur-analytics{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:680px){.ur-analytics{grid-template-columns:1fr}}
      .ur-stat{background:#fff;border:1px solid var(--ur-border);border-radius:var(--ur-radius);padding:16px;display:flex;gap:12px;align-items:center;box-shadow:0 1px 2px rgb(0 0 0 / .04)}.ur-stat-ic{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center}.ur-stat-ic.blue{background:var(--ur-blue-soft);color:var(--ur-blue)}.ur-stat-ic.green{background:var(--ur-green-soft);color:var(--ur-green)}.ur-stat-ic.yellow{background:var(--ur-yellow-soft);color:#d97706}.ur-stat-ic.gray{background:#f3f4f6;color:#6b7280}.ur-stat-label{font-size:11px;font-weight:900;color:var(--ur-muted);text-transform:uppercase;letter-spacing:.06em}.ur-stat-value{font-size:24px;font-weight:900;line-height:1.1;margin-top:4px}.ur-stat-sub{font-size:12px;color:var(--ur-muted);margin-top:4px}
      .ur-toolbar{background:#fff;border:1px solid var(--ur-border);border-radius:var(--ur-radius);padding:14px;display:flex;gap:12px;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px;box-shadow:0 1px 2px rgb(0 0 0 / .04)}.ur-toolbar-left,.ur-toolbar-right{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}.ur-toolbar-left{flex:1}.ur-filter{display:flex;flex-direction:column;gap:6px;min-width:180px}.ur-filter.search{flex:1;min-width:280px}.ur-filter label{font-size:11px;font-weight:900;color:var(--ur-muted);letter-spacing:.06em;text-transform:uppercase}.ur-input{width:100%;min-width:180px;background:#f9fafb;border:1px solid var(--ur-border);border-radius:10px;padding:10px 12px;font-size:14px;outline:0;transition:var(--ur-trans)}.ur-input:focus{background:#fff;border-color:var(--ur-primary);box-shadow:0 0 0 3px var(--ur-primary-soft)}.ur-search{padding-left:36px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");background-size:16px;background-repeat:no-repeat;background-position:11px center}
      .ur-card{background:#fff;border:1px solid var(--ur-border);border-radius:var(--ur-radius);box-shadow:0 1px 2px rgb(0 0 0 / .04);overflow:hidden}.ur-list{display:flex;flex-direction:column;gap:12px;padding:14px}.ur-empty{text-align:center;padding:54px 18px;color:var(--ur-muted);border:1px dashed var(--ur-border);border-radius:16px;background:#fff}
      .ur-user{border:1px solid var(--ur-border);border-radius:16px;overflow:hidden;background:#fff;transition:var(--ur-trans)}.ur-user:hover{border-color:#d7e9ad;box-shadow:var(--ur-shadow)}.ur-user-head{padding:14px;display:grid;grid-template-columns:minmax(250px,1.4fr) 110px 140px 150px auto;gap:12px;align-items:center;cursor:pointer}@media(max-width:980px){.ur-user-head{grid-template-columns:1fr}.ur-user-actions{justify-content:flex-start!important}}
      .ur-main{display:flex;gap:12px;align-items:center;min-width:0}.ur-avatar{width:44px;height:44px;border-radius:14px;background:var(--ur-primary-soft);color:var(--ur-primary-dark);display:flex;align-items:center;justify-content:center;font-weight:900;flex:0 0 auto}.ur-name{font-weight:900;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ur-email{font-size:13px;color:var(--ur-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ur-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;font-size:12px;font-weight:900;padding:6px 10px;white-space:nowrap}.ur-pill.blue{background:var(--ur-blue-soft);color:#1d4ed8}.ur-pill.gray{background:#f3f4f6;color:#4b5563}.ur-pill.green{background:var(--ur-green-soft);color:#047857}.ur-pill.red{background:var(--ur-red-soft);color:#b91c1c}.ur-count{font-size:13px;color:var(--ur-muted);font-weight:800}.ur-user-actions{display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap}.ur-chevron{transition:var(--ur-trans)}.ur-user.open .ur-chevron{transform:rotate(180deg)}
      .ur-user-body{display:none;border-top:1px solid var(--ur-border);background:#fafafa}.ur-user.open .ur-user-body{display:block}.ur-perm-toolbar{padding:12px 14px;display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap}.ur-perm-table{padding:0 14px 14px;overflow:auto}.ur-table{width:100%;border-collapse:separate;border-spacing:0 8px;min-width:760px}.ur-table th{font-size:11px;text-align:left;color:var(--ur-muted);text-transform:uppercase;letter-spacing:.06em;padding:0 10px}.ur-table td{background:#fff;border-top:1px solid var(--ur-border);border-bottom:1px solid var(--ur-border);padding:10px}.ur-table td:first-child{border-left:1px solid var(--ur-border);border-radius:12px 0 0 12px}.ur-table td:last-child{border-right:1px solid var(--ur-border);border-radius:0 12px 12px 0}.ur-module-title{font-weight:900;color:#111827}.ur-module-key{font-size:12px;color:var(--ur-muted);margin-top:2px}.ur-check{width:20px;height:20px;accent-color:var(--ur-primary);cursor:pointer}.ur-check-cell{text-align:center}.ur-mini-actions{display:flex;gap:6px;justify-content:flex-end}.ur-muted{color:var(--ur-muted);font-size:13px}
      .ur-modal-backdrop{position:fixed;inset:0;z-index:1300;background:rgba(17,24,39,.58);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;padding:18px;opacity:0;pointer-events:none;transition:opacity .22s ease}.ur-modal-backdrop.open{opacity:1;pointer-events:auto}.ur-modal{width:100%;max-width:980px;max-height:90vh;background:#fff;border-radius:18px;box-shadow:var(--ur-shadow);border:1px solid var(--ur-border);overflow:hidden;transform:translateY(10px) scale(.985);transition:var(--ur-trans)}.ur-modal-backdrop.open .ur-modal{transform:translateY(0) scale(1)}.ur-modal-h{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--ur-border);background:#fafafa}.ur-modal-title{font-size:17px;font-weight:900;margin:0}.ur-modal-b{padding:18px;max-height:68vh;overflow:auto}.ur-modal-f{border-top:1px solid var(--ur-border);background:#fafafa;padding:14px 18px;display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap}.ur-form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}@media(max-width:760px){.ur-form-row{grid-template-columns:1fr}}.ur-form-group label{display:block;font-size:13px;font-weight:900;margin-bottom:6px;color:#374151}.ur-matrix-actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}.ur-matrix{border:1px solid var(--ur-border);border-radius:16px;overflow:hidden}.ur-matrix-row{display:grid;grid-template-columns:minmax(180px,1.4fr) repeat(4,100px);gap:0;border-bottom:1px solid var(--ur-border);align-items:center}.ur-matrix-row:last-child{border-bottom:0}.ur-matrix-head{background:#f9fafb;font-size:11px;font-weight:900;color:var(--ur-muted);text-transform:uppercase;letter-spacing:.06em}.ur-matrix-cell{padding:11px 12px}.ur-matrix-check{text-align:center}@media(max-width:760px){.ur-matrix{overflow:auto}.ur-matrix-row{min-width:620px}}
      .ur-pagination{margin-top:16px;background:#fff;border:1px solid var(--ur-border);border-radius:14px;padding:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}.ur-page-buttons{display:flex;gap:6px;flex-wrap:wrap}.ur-page-btn{border:1px solid var(--ur-border);background:#fff;color:#111827;border-radius:10px;padding:8px 12px;font-weight:900;cursor:pointer}.ur-page-btn.active{background:var(--ur-primary);border-color:var(--ur-primary);color:#fff}.ur-page-btn:disabled{opacity:.5;cursor:not-allowed}.ur-toast-wrap{position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}.ur-toast{pointer-events:auto;min-width:280px;max-width:380px;background:#fff;border:1px solid var(--ur-border);border-radius:14px;box-shadow:var(--ur-shadow);padding:12px;display:flex;gap:10px;align-items:flex-start}.ur-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}.ur-toast-ic.ok{background:var(--ur-green-soft);color:var(--ur-green)}.ur-toast-ic.bad{background:var(--ur-red-soft);color:var(--ur-red)}.ur-toast-title{font-weight:900;font-size:13px;margin:0;color:#111827}.ur-toast-msg{font-size:12px;color:#374151;margin:4px 0 0;line-height:1.4}.ur-toast-x{border:0;background:transparent;color:var(--ur-muted);cursor:pointer;margin-left:auto}
    </style>
  @endpush
@endonce

@section('content')
  <div class="ur-wrap">
    <div class="ur-titlebar">
      <div>
        <div class="ur-title">USER ROLLEN</div>
        <div class="ur-sub">Berechtigungen pro Benutzer gesammelt verwalten. Module einfach anhaken und alle Rechte zusammen speichern.</div>
        <div class="ur-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a><span>›</span><span class="current">User Rollen</span>
        </div>
      </div>
      @if(auth()->user()->hasPermission('Users', 'add'))
        <button type="button" class="ur-btn" onclick="openBulkModal()">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          Benutzer-Rechte setzen
        </button>
      @endif
    </div>

    <div class="ur-analytics">
      <div class="ur-stat"><div class="ur-stat-ic blue"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg></div><div><div class="ur-stat-label">Gesamt</div><div class="ur-stat-value" id="statTotal">0</div><div class="ur-stat-sub">Berechtigungen insgesamt</div></div></div>
      <div class="ur-stat"><div class="ur-stat-ic green"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div><div><div class="ur-stat-label">Benutzer</div><div class="ur-stat-value" id="statUsers">0</div><div class="ur-stat-sub">Benutzer mit Rechten</div></div></div>
      <div class="ur-stat"><div class="ur-stat-ic yellow"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M7 12h10M10 17h4"/></svg></div><div><div class="ur-stat-label">Module</div><div class="ur-stat-value" id="statModules">0</div><div class="ur-stat-sub">Module verwendet</div></div></div>
      <div class="ur-stat"><div class="ur-stat-ic gray"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.5L18.5 21 12 16.8 5.5 21l2-7.5L2 9h7l3-7z"/></svg></div><div><div class="ur-stat-label">Admins</div><div class="ur-stat-value" id="statAdmins">0</div><div class="ur-stat-sub">Admins mit Rechten</div></div></div>
    </div>

    <div class="ur-toolbar">
      <div class="ur-toolbar-left">
        <div class="ur-filter search"><label>Suche</label><input type="text" class="ur-input ur-search" id="filterSearch" placeholder="Name, E-Mail oder Modul suchen"></div>
        <div class="ur-filter"><label>Benutzer</label><select class="ur-input" id="filterUser"><option value="">Alle Benutzer</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->display_name }} — {{ $user->email }}</option>@endforeach</select></div>
        <div class="ur-filter"><label>Modul</label><select class="ur-input" id="filterModule"><option value="">Alle Module</option>@foreach($modules as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
        <div class="ur-filter"><label>Rolle</label><select class="ur-input" id="filterRole"><option value="">Alle Rollen</option><option value="admin">Admin</option><option value="user">User</option></select></div>
        <div class="ur-filter"><label>Pro Seite</label><select class="ur-input" id="filterPerPage"><option value="10" selected>10</option><option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></div>
      </div>
      <div class="ur-toolbar-right">
        <button class="ur-btn-soft" type="button" onclick="loadUserRolls(1)">Suchen</button>
        <button class="ur-btn-soft" type="button" onclick="resetFilters()">Zurücksetzen</button>
      </div>
    </div>

    <div class="ur-card">
      <div class="ur-list" id="userRollList"><div class="ur-empty">Berechtigungen werden geladen...</div></div>
    </div>

    <div class="ur-pagination" id="paginationWrap" style="display:none;">
      <div class="ur-muted" id="paginationMeta"></div>
      <div class="ur-page-buttons" id="paginationButtons"></div>
    </div>
  </div>

  <div class="ur-modal-backdrop" id="bulkModal">
    <div class="ur-modal">
      <div class="ur-modal-h">
        <h3 class="ur-modal-title" id="bulkModalTitle">Benutzer-Rechte setzen</h3>
        <button class="ur-btn-icon" type="button" onclick="closeModal('bulkModal')"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <form id="bulkForm">
        @csrf
        <div class="ur-modal-b">
          <div class="ur-form-row">
            <div class="ur-form-group">
              <label>Benutzer *</label>
              <select id="bulkUserId" class="ur-input" required>
                <option value="">Bitte wählen</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}">{{ $user->display_name }} — {{ $user->email }}</option>
                @endforeach
              </select>
            </div>
            <div class="ur-form-group">
              <label>Schnell-Aktionen</label>
              <div class="ur-matrix-actions">
                <button type="button" class="ur-btn-soft" onclick="checkColumn('is_read', true)">Alle Lesen</button>
                <button type="button" class="ur-btn-soft" onclick="checkAllMatrix(true)">Alles markieren</button>
                <button type="button" class="ur-btn-soft" onclick="checkAllMatrix(false)">Alles entfernen</button>
              </div>
            </div>
          </div>
          <div class="ur-matrix" id="bulkMatrix"></div>
        </div>
        <div class="ur-modal-f">
          <button type="button" class="ur-btn-soft" onclick="closeModal('bulkModal')">Abbrechen</button>
          <button type="submit" class="ur-btn" id="bulkSubmitBtn">Alles speichern</button>
        </div>
      </form>
    </div>
  </div>

  <div class="ur-toast-wrap" id="toastWrap"></div>
@endsection

@once
  @push('scripts')
    <script>
      const USER_ROLL_AJAX_URL = @json(route('user-rolls.ajax'));
      const USER_ROLL_STORE_URL = @json(route('user-rolls.store'));
      const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
      const MODULES = @json(collect($modules)->map(fn($label, $key) => ['item_id' => $key, 'module_label' => $label])->values());

      let currentPage = 1;
      let currentCan = { add:false, update:false, delete:false };
      let lastRows = [];

      function escapeHtml(value){return String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
      function openModal(id){document.getElementById(id)?.classList.add('open');}
      function closeModal(id){document.getElementById(id)?.classList.remove('open');}
      function initials(name){return String(name || '?').split(' ').filter(Boolean).slice(0,2).map(x=>x[0]).join('').toUpperCase() || '?';}
      function boolIcon(value){return value ? '<span class="ur-pill green">✓</span>' : '<span class="ur-pill red">×</span>';}
      function rolePill(role){return role === 'Admin' ? '<span class="ur-pill blue">Admin</span>' : '<span class="ur-pill gray">User</span>';}
      function toast(kind,title,msg){const wrap=document.getElementById('toastWrap');if(!wrap)return;const el=document.createElement('div');el.className='ur-toast';const icon=kind==='bad'?'M6 18L18 6M6 6l12 12':'M20 6L9 17l-5-5';el.innerHTML=`<div class="ur-toast-ic ${kind}"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="${icon}"/></svg></div><div style="flex:1"><p class="ur-toast-title">${escapeHtml(title)}</p><p class="ur-toast-msg">${escapeHtml(msg)}</p></div><button class="ur-toast-x" onclick="this.parentElement.remove()">×</button>`;wrap.appendChild(el);setTimeout(()=>{try{el.remove()}catch(e){}},4500);}

      function buildQuery(page=1){const p=new URLSearchParams();p.set('page',page);p.set('search',document.getElementById('filterSearch').value||'');p.set('user_id',document.getElementById('filterUser').value||'');p.set('module',document.getElementById('filterModule').value||'');p.set('role',document.getElementById('filterRole').value||'');p.set('per_page',document.getElementById('filterPerPage').value||'10');return p;}

      async function loadUserRolls(page=1){
        currentPage=page;
        const list=document.getElementById('userRollList');
        list.innerHTML='<div class="ur-empty">Berechtigungen werden geladen...</div>';
        try{
          const res=await fetch(`${USER_ROLL_AJAX_URL}?${buildQuery(page).toString()}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
          const data=await res.json();
          if(!res.ok || !data.success) throw new Error(data.message || 'Fehler beim Laden.');
          currentCan=data.can || currentCan;
          lastRows=data.rows || [];
          renderAnalytics(data.analytics || {});
          renderRows(lastRows);
          renderPagination(data.pagination || {});
        }catch(error){
          list.innerHTML='<div class="ur-empty">Fehler beim Laden der Berechtigungen.</div>';
          toast('bad','Fehler',error.message || 'Berechtigungen konnten nicht geladen werden.');
        }
      }

      function renderAnalytics(a){document.getElementById('statTotal').innerText=a.total??0;document.getElementById('statUsers').innerText=a.users_with_permissions??0;document.getElementById('statModules').innerText=a.modules_used??0;document.getElementById('statAdmins').innerText=a.admins??0;}

      function renderRows(rows){
        const list=document.getElementById('userRollList');
        if(!rows.length){list.innerHTML='<div class="ur-empty">Keine Benutzer gefunden.</div>';return;}
        list.innerHTML=rows.map(row=>{
          const perms=row.permissions || [];
          const permissionRows=perms.length ? perms.map(p=>`
            <tr>
              <td><div class="ur-module-title">${escapeHtml(p.module_label)}</div><div class="ur-module-key">${escapeHtml(p.item_id)}</div></td>
              <td class="ur-check-cell">${boolIcon(p.is_read)}</td>
              <td class="ur-check-cell">${boolIcon(p.is_add)}</td>
              <td class="ur-check-cell">${boolIcon(p.is_update)}</td>
              <td class="ur-check-cell">${boolIcon(p.is_delete)}</td>
              <td><div class="ur-mini-actions">${currentCan.delete ? `<button type="button" class="ur-btn-icon red" title="Löschen" onclick="deletePermission(${p.id})"><svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6M4 7h16"/></svg></button>` : ''}</div></td>
            </tr>`).join('') : `<tr><td colspan="6"><div class="ur-muted">Dieser Benutzer hat aktuell keine gespeicherten Berechtigungen.</div></td></tr>`;
          return `
            <div class="ur-user" id="userRow${row.user_id}">
              <div class="ur-user-head" onclick="toggleUserRow(event, ${row.user_id})">
                <div class="ur-main"><div class="ur-avatar">${escapeHtml(initials(row.user_name))}</div><div style="min-width:0"><div class="ur-name">${escapeHtml(row.user_name)}</div><div class="ur-email">${escapeHtml(row.email || 'Keine E-Mail')}</div></div></div>
                <div>${rolePill(row.role)}</div>
                <div>${row.is_active ? '<span class="ur-pill green">Aktiv</span>' : '<span class="ur-pill red">Inaktiv</span>'}</div>
                <div class="ur-count">${escapeHtml(row.permission_count)} Rechte</div>
                <div class="ur-user-actions">
                  ${(currentCan.add || currentCan.update) ? `<button type="button" class="ur-btn-soft" onclick="event.stopPropagation(); openBulkModal(${row.user_id})">Bearbeiten</button>` : ''}
                  <button type="button" class="ur-btn-icon"><svg class="ur-chevron" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                </div>
              </div>
              <div class="ur-user-body">
                <div class="ur-perm-toolbar"><div class="ur-muted">Alle gespeicherten Module für diesen Benutzer</div>${(currentCan.add || currentCan.update) ? `<button type="button" class="ur-btn" onclick="openBulkModal(${row.user_id})">Rechte-Matrix öffnen</button>` : ''}</div>
                <div class="ur-perm-table"><table class="ur-table"><thead><tr><th>Modul</th><th style="text-align:center">Lesen</th><th style="text-align:center">Erstellen</th><th style="text-align:center">Ändern</th><th style="text-align:center">Löschen</th><th style="text-align:right">Aktion</th></tr></thead><tbody>${permissionRows}</tbody></table></div>
              </div>
            </div>`;
        }).join('');
      }

      function toggleUserRow(event,userId){if(event.target.closest('button') && !event.target.closest('.ur-user-head > .ur-user-actions > .ur-btn-icon')) return;document.getElementById(`userRow${userId}`)?.classList.toggle('open');}

      function renderPagination(p){const wrap=document.getElementById('paginationWrap'),meta=document.getElementById('paginationMeta'),buttons=document.getElementById('paginationButtons');if(!p.total || p.last_page<=1){wrap.style.display='none';buttons.innerHTML='';return;}wrap.style.display='flex';meta.innerHTML=`Zeige <strong>${p.from ?? 0}</strong> bis <strong>${p.to ?? 0}</strong> von <strong>${p.total ?? 0}</strong> Benutzern`;let html=`<button class="ur-page-btn" ${p.current_page<=1?'disabled':''} onclick="loadUserRolls(${p.current_page-1})">Zurück</button>`;const start=Math.max(1,p.current_page-2),end=Math.min(p.last_page,p.current_page+2);for(let i=start;i<=end;i++){html+=`<button class="ur-page-btn ${i===p.current_page?'active':''}" onclick="loadUserRolls(${i})">${i}</button>`;}html+=`<button class="ur-page-btn" ${p.current_page>=p.last_page?'disabled':''} onclick="loadUserRolls(${p.current_page+1})">Weiter</button>`;buttons.innerHTML=html;}

      function resetFilters(){document.getElementById('filterSearch').value='';document.getElementById('filterUser').value='';document.getElementById('filterModule').value='';document.getElementById('filterRole').value='';document.getElementById('filterPerPage').value='10';loadUserRolls(1);}

      function matrixFromRow(userId){const row=lastRows.find(r=>String(r.user_id)===String(userId));const map={};(row?.matrix || row?.permissions || []).forEach(p=>map[p.item_id]=p);return MODULES.map(m=>({item_id:m.item_id,module_label:m.module_label,is_read:!!map[m.item_id]?.is_read,is_add:!!map[m.item_id]?.is_add,is_update:!!map[m.item_id]?.is_update,is_delete:!!map[m.item_id]?.is_delete}));}

      function openBulkModal(userId=null){
        const select=document.getElementById('bulkUserId');
        select.value=userId ? String(userId) : (document.getElementById('filterUser').value || '');
        renderBulkMatrix(select.value ? matrixFromRow(select.value) : MODULES.map(m=>({...m,is_read:false,is_add:false,is_update:false,is_delete:false})));
        openModal('bulkModal');
      }

      function renderBulkMatrix(matrix){
        const wrap=document.getElementById('bulkMatrix');
        wrap.innerHTML=`<div class="ur-matrix-row ur-matrix-head"><div class="ur-matrix-cell">Modul</div><div class="ur-matrix-cell ur-matrix-check">Lesen</div><div class="ur-matrix-cell ur-matrix-check">Erstellen</div><div class="ur-matrix-cell ur-matrix-check">Ändern</div><div class="ur-matrix-cell ur-matrix-check">Löschen</div></div>` + matrix.map(p=>`
          <div class="ur-matrix-row" data-item-id="${escapeHtml(p.item_id)}">
            <div class="ur-matrix-cell"><div class="ur-module-title">${escapeHtml(p.module_label)}</div><div class="ur-module-key">${escapeHtml(p.item_id)}</div></div>
            <div class="ur-matrix-cell ur-matrix-check"><input class="ur-check matrix-check" type="checkbox" data-field="is_read" ${p.is_read?'checked':''}></div>
            <div class="ur-matrix-cell ur-matrix-check"><input class="ur-check matrix-check" type="checkbox" data-field="is_add" ${p.is_add?'checked':''}></div>
            <div class="ur-matrix-cell ur-matrix-check"><input class="ur-check matrix-check" type="checkbox" data-field="is_update" ${p.is_update?'checked':''}></div>
            <div class="ur-matrix-cell ur-matrix-check"><input class="ur-check matrix-check" type="checkbox" data-field="is_delete" ${p.is_delete?'checked':''}></div>
          </div>`).join('');
      }

      document.getElementById('bulkUserId').addEventListener('change',function(){renderBulkMatrix(this.value ? matrixFromRow(this.value) : MODULES.map(m=>({...m,is_read:false,is_add:false,is_update:false,is_delete:false})));});
      function checkColumn(field,state){document.querySelectorAll(`#bulkMatrix .matrix-check[data-field="${field}"]`).forEach(cb=>cb.checked=state);}
      function checkAllMatrix(state){document.querySelectorAll('#bulkMatrix .matrix-check').forEach(cb=>cb.checked=state);}
      function collectMatrix(){return Array.from(document.querySelectorAll('#bulkMatrix .ur-matrix-row[data-item-id]')).map(row=>{const item_id=row.dataset.itemId;const data={item_id,is_read:false,is_add:false,is_update:false,is_delete:false};row.querySelectorAll('.matrix-check').forEach(cb=>data[cb.dataset.field]=cb.checked);return data;});}

      document.getElementById('bulkForm').addEventListener('submit',async function(e){
        e.preventDefault();
        const userId=document.getElementById('bulkUserId').value;
        if(!userId){toast('bad','Benutzer fehlt','Bitte zuerst einen Benutzer auswählen.');return;}
        const btn=document.getElementById('bulkSubmitBtn');const old=btn.innerText;btn.disabled=true;btn.innerText='Speichert...';
        try{
          const res=await fetch(USER_ROLL_STORE_URL,{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF_TOKEN},body:JSON.stringify({_token:CSRF_TOKEN,user_id:userId,permissions:collectMatrix()})});
          const data=await res.json();
          if(!res.ok || !data.success) throw new Error(data.message || validationMessage(data.errors) || 'Speichern fehlgeschlagen.');
          closeModal('bulkModal');toast('ok','Gespeichert',data.message || 'Berechtigungen wurden gespeichert.');loadUserRolls(currentPage);
        }catch(error){toast('bad','Fehler',error.message || 'Berechtigungen konnten nicht gespeichert werden.');}
        finally{btn.disabled=false;btn.innerText=old;}
      });

      function validationMessage(errors){if(!errors)return null;const first=Object.keys(errors)[0];if(!first)return null;return Array.isArray(errors[first])?errors[first][0]:errors[first];}
      async function deletePermission(id){if(!confirm('Möchten Sie diese Berechtigung wirklich löschen?')) return;try{const res=await fetch(`${USER_ROLL_STORE_URL}/${id}`,{method:'DELETE',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF_TOKEN}});const data=await res.json();if(!res.ok || !data.success) throw new Error(data.message || 'Löschen fehlgeschlagen.');toast('bad','Gelöscht',data.message || 'Berechtigung wurde gelöscht.');loadUserRolls(currentPage);}catch(error){toast('bad','Fehler',error.message || 'Berechtigung konnte nicht gelöscht werden.');}}

      document.addEventListener('click',e=>{if(e.target.classList.contains('ur-modal-backdrop')) e.target.classList.remove('open');});
      document.addEventListener('keydown',e=>{if(e.key==='Escape') document.querySelectorAll('.ur-modal-backdrop.open').forEach(el=>el.classList.remove('open'));});
      document.getElementById('filterSearch').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();loadUserRolls(1);}});
      ['filterUser','filterModule','filterRole','filterPerPage'].forEach(id=>document.getElementById(id).addEventListener('change',()=>loadUserRolls(1)));
      document.addEventListener('DOMContentLoaded',()=>loadUserRolls(1));
    </script>
  @endpush
@endonce
