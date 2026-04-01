{{-- resources/views/admin/dashboard/employee/partials/overdue48h.blade.php --}}

@once
@push('style')
<style>
  :root{
    --app-bg:#f3f4f6; --card-bg:#fff; --text-main:#1f2937; --text-muted:#6b7280; --border:#e5e7eb;
    --primary:#2563eb; --primary-hover:#1d4ed8; --primary-light:#eff6ff;
    --success:#10b981; --success-light:#ecfdf5;
    --warning:#f59e0b; --warning-light:#fffbeb;
    --danger:#ef4444; --danger-hover:#dc2626; --danger-light:#fef2f2;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:12px; --transition:all .2s ease-in-out;
  }

  .oc-wrap{font-family:Inter,system-ui,-apple-system,sans-serif;color:var(--text-main);max-width:1400px;margin:0 auto}
  .oc-header{margin-bottom:18px}
  .oc-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:14px}
  .oc-title{font-size:24px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
  .oc-btn-ic{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;transition:var(--transition)}
  .oc-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db}
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin-bottom:18px}
  .oc-stat{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow-sm);transition:var(--transition);display:flex;flex-direction:column}
  .oc-stat:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
  .oc-stat-l{font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin-bottom:8px;display:flex;align-items:center;gap:8px}
  .oc-dot{width:8px;height:8px;border-radius:999px;display:inline-block}
  .oc-stat-v{font-size:28px;font-weight:800;line-height:1;color:var(--text-main)}

  .oc-toolbar{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;margin-bottom:16px;box-shadow:var(--shadow-sm)}
  .oc-fg{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
  .oc-sep{width:1px;height:24px;background:var(--border);margin:0 4px}
  .oc-input{
    background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 36px;font-size:14px;outline:none;transition:var(--transition);min-width:240px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:10px center;background-size:16px
  }
  .oc-input:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
  .oc-select{
    padding:8px 32px 8px 12px;border-radius:8px;border:1px solid var(--border);
    background-color:#fff;font-size:13px;cursor:pointer;outline:none;appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 10px center;background-size:14px
  }
  .oc-pill{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;padding:6px 12px;border-radius:999px;border:1px solid var(--border);background:#fff;cursor:pointer;user-select:none;transition:var(--transition)}
  .oc-pill:hover{background:#f9fafb;border-color:#d1d5db}
  .oc-pill input{accent-color:var(--primary);width:14px;height:14px}

  .oc-list{display:flex;flex-direction:column;gap:12px}
  .oc-item{
    background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);
    padding:16px;display:grid;gap:16px;align-items:center;grid-template-columns:48px 1fr 200px 220px 176px;
    transition:var(--transition);position:relative
  }
  .oc-item:hover{border-color:var(--primary);box-shadow:var(--shadow);z-index:10}
  @media(max-width:1100px){
    .oc-item{grid-template-columns:48px 1fr;grid-template-rows:auto auto auto auto;gap:12px}
    .oc-meta,.oc-time,.oc-actions{grid-column:2}
    .oc-actions{justify-self:start;margin-top:8px}
  }
  .oc-ic{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;color:var(--text-muted)}
  .oc-ic svg{width:24px;height:24px}
  .type-inquiry{background:var(--primary-light);color:var(--primary)}
  .type-task{background:var(--success-light);color:var(--success)}
  .type-ticket{background:var(--warning-light);color:var(--warning)}
  .type-lead{background:#f3e8ff;color:#9333ea}
  .type-appointment{background:#e0f2fe;color:#0284c7}

  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .oc-sum{display:inline-block;font-size:11px;margin-top:6px;background:#f3f4f6;padding:2px 8px;border-radius:6px;color:var(--text-muted);max-width:fit-content}

  .oc-meta{display:flex;flex-wrap:wrap;align-items:flex-start}
  .oc-tag{display:inline-flex;align-items:center;padding:4px 8px;border-radius:8px;font-size:11px;font-weight:900;text-transform:uppercase;background:#f3f4f6;color:#4b5563;margin-right:6px;margin-bottom:6px;gap:6px}
  .oc-tag.ok{background:var(--success-light);color:var(--success)}
  .oc-tag.warn{background:var(--warning-light);color:var(--warning)}
  .oc-tag.bad{background:var(--danger-light);color:var(--danger)}
  .oc-time{display:flex;flex-direction:column;gap:4px}
  .oc-od{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:900;padding:5px 10px;border-radius:999px;width:fit-content}
  .od-high{background:var(--danger-light);color:var(--danger);border:1px solid rgba(239,68,68,.2)}
  .od-med{background:var(--warning-light);color:var(--warning);border:1px solid rgba(245,158,11,.2)}
  .od-ok{background:#ecfeff;color:#0891b2;border:1px solid rgba(8,145,178,.18)}

  .oc-actions{display:flex;gap:8px;justify-content:flex-end}
  .oc-pager{display:flex;justify-content:center;align-items:center;gap:12px;margin-top:18px}
  .oc-pager span{font-size:13px;color:var(--text-muted)}

  .oc-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.4);backdrop-filter:blur(2px);z-index:999;opacity:0;pointer-events:none;transition:opacity .25s}
  .oc-backdrop.open{opacity:1;pointer-events:auto}
  .oc-drawer{
    position:fixed;top:0;right:0;bottom:0;width:100%;max-width:520px;background:#fff;box-shadow:-10px 0 30px rgba(0,0,0,.1);
    transform:translateX(100%);transition:transform .3s cubic-bezier(.16,1,.3,1);z-index:1000;display:flex;flex-direction:column
  }
  .oc-backdrop.open .oc-drawer{transform:translateX(0)}
  .oc-dh{padding:20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;gap:12px;background:#f9fafb}
  .oc-db{flex:1;overflow-y:auto;padding:20px}
  .oc-df{padding:16px 20px;border-top:1px solid var(--border);background:#f9fafb}
  .oc-tl{padding-left:10px}
  .oc-tli{position:relative;padding-left:24px;padding-bottom:18px;border-left:2px solid var(--border)}
  .oc-tli:last-child{border-left-color:transparent;padding-bottom:0}
  .oc-tld{position:absolute;left:-6px;top:2px;width:10px;height:10px;background:#fff;border:2px solid var(--primary);border-radius:50%}
  .oc-tldt{font-size:11px;color:var(--text-muted);margin-bottom:4px;font-weight:800}
  .oc-tlc{background:#f9fafb;border:1px solid var(--border);border-radius:10px;padding:10px;font-size:13px;color:#374151;white-space:pre-wrap}
  .oc-bdg{font-size:10px;padding:2px 6px;background:#fff;border:1px solid var(--border);border-radius:8px;color:var(--text-muted);margin-right:6px}
  .oc-ta{width:100%;min-height:100px;border:1px solid var(--border);border-radius:10px;padding:10px;font-size:13px;outline:none;margin-bottom:10px}
  .oc-ta:focus{border-color:var(--primary);box-shadow:0 0 0 2px var(--primary-light)}
  .oc-btn{width:100%;background:var(--primary);color:#fff;border:none;padding:10px;border-radius:10px;font-weight:900;cursor:pointer;transition:var(--transition)}
  .oc-btn:hover{background:var(--primary-hover)}
  .oc-btn:disabled{opacity:.7;cursor:not-allowed}
  .oc-btn.danger{background:var(--danger)}
  .oc-btn.danger:hover{background:var(--danger-hover)}
  .hidden{display:none !important}

  /* =========================================================================
     SKIP MODAL
     ========================================================================= */
  .oc-modal-backdrop{
    position:fixed;inset:0;z-index:1100;
    background:rgba(17,24,39,.55);
    backdrop-filter:blur(3px);
    opacity:0;pointer-events:none;
    transition:opacity .22s ease;
    display:flex;align-items:center;justify-content:center;
    padding:18px;
  }
  .oc-modal-backdrop.open{opacity:1;pointer-events:auto}
  .oc-modal{
    width:100%;max-width:520px;background:#fff;border:1px solid rgba(229,231,235,.9);
    border-radius:16px;box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }
  .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}
  .oc-modal-h{
    display:flex;gap:12px;align-items:flex-start;justify-content:space-between;
    padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa;
  }
  .oc-modal-hl{display:flex;gap:12px;align-items:flex-start;min-width:0}
  .oc-modal-ic{
    width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;
    border:1px solid rgba(239,68,68,.18);
    background:var(--danger-light);color:var(--danger);
    flex:0 0 auto;
  }
  .oc-modal-ic svg{width:22px;height:22px}
  .oc-modal-ttl{font-weight:900;font-size:15px;line-height:1.2;margin:0;color:#111827}
  .oc-modal-sub{font-size:12px;color:var(--text-muted);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .oc-modal-b{padding:16px 18px}
  .oc-modal-note{font-size:13px;color:#374151;line-height:1.55;margin:0}
  .oc-modal-meta{
    margin-top:12px;
    background:#f9fafb;border:1px solid var(--border);border-radius:12px;
    padding:12px;font-size:12px;color:#374151;
  }
  .oc-modal-meta b{color:#111827}
  .oc-modal-f{
    padding:14px 18px;border-top:1px solid var(--border);background:#fafafa;
    display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;
  }
  .oc-btn-sm{
    border-radius:10px;border:1px solid var(--border);background:#fff;color:#111827;
    padding:10px 12px;font-weight:900;cursor:pointer;transition:var(--transition);
    display:inline-flex;align-items:center;gap:8px;
  }
  .oc-btn-sm:hover{background:#f9fafb;border-color:#d1d5db}
  .oc-btn-sm.danger{
    background:var(--danger);border-color:transparent;color:#fff;
  }
  .oc-btn-sm.danger:hover{background:var(--danger-hover)}
  .oc-btn-sm:disabled{opacity:.7;cursor:not-allowed}

  /* =========================================================================
     TOAST
     ========================================================================= */
  .oc-toast-wrap{
    position:fixed;right:16px;bottom:16px;z-index:1200;
    display:flex;flex-direction:column;gap:10px;
    pointer-events:none;
  }
  .oc-toast{
    pointer-events:auto;
    min-width:280px;max-width:360px;
    background:#fff;border:1px solid var(--border);border-radius:14px;
    box-shadow:var(--shadow);
    padding:12px 12px;
    display:flex;gap:10px;align-items:flex-start;
    animation:ocToastIn .22s ease forwards;
  }
  @keyframes ocToastIn{
    from{transform:translateY(10px);opacity:0}
    to{transform:translateY(0);opacity:1}
  }
  .oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}
  .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .oc-toast-ic.warn{background:var(--warning-light);color:var(--warning)}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .oc-toast-ttl{font-weight:900;font-size:12px;margin:0;color:#111827}
  .oc-toast-msg{font-size:12px;color:#374151;margin:2px 0 0 0;line-height:1.4}
  .oc-toast-x{margin-left:auto}

  /* =========================================================================
    FULL TITLE MODAL + TRUNCATED TITLE
    ========================================================================= */
  .oc-ttl-btn{
    all:unset;
    display:block;
    font-weight:800;
    font-size:15px;
    margin-bottom:4px;
    color:#111827;
    cursor:pointer;
    max-width:100%;
  }
  .oc-ttl-btn:hover{ text-decoration:underline; }
  .oc-ttl-btn:focus{
    outline:2px solid rgba(37,99,235,.45);
    outline-offset:2px;
    border-radius:8px;
  }

  .oc-modal-ic.info{
    border:1px solid rgba(37,99,235,.18);
    background:var(--primary-light);
    color:var(--primary);
  }

  /* Bulk selection styles */
.oc-item.selected { border-color: var(--primary); background: var(--primary-light); }
.oc-bulk-bar {
    position: fixed; bottom: 114px; left: 50%; transform: translateX(-50%) translateY(100px);
    background: #2c3e50; color: #fff; padding: 12px 24px; border-radius: 999px;
    display: flex; align-items: center; gap: 16px; z-index: 900;
    transition: transform .3s cubic-bezier(.16,1,.3,1); box-shadow: var(--shadow);
}
.oc-bulk-bar.open { transform: translateX(-50%) translateY(0); }
.oc-checkbox-wrap { display: flex; align-items: center; justify-content: center; width: 48px; }
.oc-cb-custom { width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); }
/* Update grid to include checkbox column (32px) */
.oc-item {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    display: grid;
    gap: 16px;
    align-items: center;
    /* Added 32px column for checkbox at start */
    grid-template-columns: 32px 48px 1fr 200px 220px 176px;
    transition: var(--transition);
    position: relative;
}

/* Checkbox wrapper centered in its grid cell */
.oc-checkbox-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 48px; /* Match icon height for vertical centering */
}

.oc-cb-custom {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary);
}

/* Mobile responsive update */
@media(max-width: 1100px) {
    .oc-item {
        /* Adjust mobile grid: [Check] [Icon] [Content] */
        grid-template-columns: 32px 48px 1fr;
        grid-template-rows: auto auto auto auto;
        gap: 12px;
    }
    .oc-meta, .oc-time, .oc-actions {
        grid-column: 3; /* Move to 3rd column because of added checkbox */
    }
    .oc-actions {
        justify-self: start;
        margin-top: 8px;
    }
}
</style>
@endpush
@endonce

<div class="oc-wrap mt-4" id="oc-overdue" data-hours="48">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">Überfälliger Bericht</div>
        <div class="oc-sub">Einträge ohne Aktivität oder Bericht seit über 48 Stunden.</div>
      </div>

      <button class="oc-btn-ic" id="oc-refresh" type="button" title="Aktualisieren" aria-label="Aktualisieren">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
      </button>
    </div>

    <div class="oc-stats" id="oc-stats" aria-live="polite"></div>
  </div>

  <div class="oc-toolbar" role="region" aria-label="Filter">
  
    <div class="oc-fg">
      <input class="oc-input" id="oc-search" placeholder="Suche nach Titel, Kunde..." autocomplete="off" />
        <label class="oc-pill" title="Alle auswählen">
          <input type="checkbox" id="oc-select-all"> Alle
      </label>
      <div class="oc-sep" aria-hidden="true"></div>

      <label class="oc-pill"><input type="checkbox" class="oc-type" value="inquiry" checked> Anfragen</label>
      <label class="oc-pill"><input type="checkbox" class="oc-type" value="task" checked> Aufgaben</label>
      <label class="oc-pill"><input type="checkbox" class="oc-type" value="appointment" checked> Termine</label>
      <label class="oc-pill"><input type="checkbox" class="oc-type" value="ticket" checked> Tickets</label>
      <label class="oc-pill"><input type="checkbox" class="oc-type" value="lead" checked> Leads</label>
    </div>

    <div class="oc-fg">
      <select class="oc-select" id="oc-sort" aria-label="Sortierung">
        <option value="oldest">Älteste Aktivität</option>
        <option value="most_overdue">Am meisten überfällig</option>
        <option value="newest">Neueste Aktivität</option>
      </select>

      <select class="oc-select" id="oc-perpage" aria-label="Einträge pro Seite">
        <option value="12">12 / Seite</option>
        <option value="24">24 / Seite</option>
        <option value="48">48 / Seite</option>
      </select>
    </div>
  </div>

  <div id="oc-list" class="oc-list" aria-live="polite">
    <div style="text-align:center;padding:60px;color:var(--text-muted)">Lädt...</div>
  </div>

  <div class="oc-pager" role="navigation" aria-label="Pagination">
    <button class="oc-btn-ic" id="oc-prev" type="button" title="Zurück" aria-label="Zurück" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <span id="oc-page">Seite 1</span>
    <button class="oc-btn-ic" id="oc-next" type="button" title="Weiter" aria-label="Weiter" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>
</div>

{{-- Drawer --}}
<div class="oc-backdrop" id="oc-backdrop" aria-hidden="true">
  <aside class="oc-drawer" role="dialog" aria-modal="true" aria-labelledby="oc-dtitle">
    <div class="oc-dh">
      <div style="min-width:0">
        <h3 id="oc-dtitle" style="font-weight:900;font-size:16px;color:var(--text-main);line-height:1.2;margin:0">Titel</h3>
        <div id="oc-dsub" style="font-size:12px;color:var(--text-muted);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></div>
      </div>

      <button class="oc-btn-ic" id="oc-dclose" type="button" title="Schließen" aria-label="Schließen" style="width:32px;height:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="oc-db" id="oc-dbody"></div>

    <div class="oc-df hidden" id="oc-reportbox">
      <div style="font-weight:900;font-size:13px;margin-bottom:8px">Neuen Bericht verfassen</div>
      <textarea class="oc-ta" id="oc-report" placeholder="Ergebnis oder Notiz hier eingeben..."></textarea>
      <button class="oc-btn" id="oc-save" type="button">Bericht speichern</button>
    </div>
  </aside>
</div>

{{-- Skip Modal --}}
<div class="oc-modal-backdrop" id="oc-skip-modal" aria-hidden="true">
  <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="oc-skip-title" aria-describedby="oc-skip-desc">
    <div class="oc-modal-h">
      <div class="oc-modal-hl">
        <div class="oc-modal-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          </svg>
        </div>
        <div style="min-width:0">
          <h3 id="oc-skip-title" class="oc-modal-ttl">Bericht überspringen?</h3>
          <div id="oc-skip-sub" class="oc-modal-sub">—</div>
        </div>
      </div>

      <button class="oc-btn-ic" id="oc-skip-close" type="button" title="Schließen" aria-label="Schließen" style="width:32px;height:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="oc-modal-b">
      <p id="oc-skip-desc" class="oc-modal-note">
        Dieser Eintrag wird als <b>„Bericht übersprungen“</b> gespeichert. Dadurch gilt er als aktualisiert und verschwindet aus der 48h-Überfällig-Liste.
      </p>

      <div class="oc-modal-meta" id="oc-skip-meta">
        <div><b>Typ:</b> —</div>
        <div style="margin-top:6px"><b>Titel:</b> —</div>
      </div>

      {{-- Reason template UI (frontend) --}}
      <div style="margin-top:12px">
        <div style="font-weight:900;font-size:12px;margin-bottom:6px;color:#111827">Grund (Vorlage)</div>
        <select id="oc-skip-template" class="oc-select" style="width:100%">
          <option value="">— Vorlage wählen —</option>
          <option value="customer_not_reachable">Kunde nicht erreichbar</option>
          <option value="waiting_external">Warte auf Rückmeldung (extern)</option>
          <option value="internal_clarification">Interne Klärung läuft</option>
          <option value="rescheduled">Follow-up verschoben</option>
          <option value="duplicate">Doppelt / bereits erledigt</option>
          <option value="other">Sonstiges</option>
        </select>
      </div>

      <div style="margin-top:10px">
        <div style="font-weight:900;font-size:12px;margin-bottom:6px;color:#111827">Grund (Text)</div>
        <textarea id="oc-skip-reason" class="oc-ta" placeholder="Pflichtfeld: Grund eingeben (mind. 3 Zeichen)…"></textarea>

      </div>

      {{-- Backend 422 / error reason --}}
      <div id="oc-skip-error" class="hidden"
           style="margin-top:10px;padding:10px;border-radius:12px;border:1px solid rgba(239,68,68,.25);background:var(--danger-light);color:var(--danger);font-size:12px;white-space:pre-wrap"></div>
    </div>

    <div class="oc-modal-f">
      <button class="oc-btn-sm" id="oc-skip-cancel" type="button">Abbrechen</button>
      <button class="oc-btn-sm danger" id="oc-skip-confirm" type="button">Überspringen</button>
    </div>
  </div>
</div>

{{-- Full Title Modal --}}
<div class="oc-modal-backdrop" id="oc-title-modal" aria-hidden="true">
  <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="oc-title-modal-title" aria-describedby="oc-title-modal-body">
    <div class="oc-modal-h">
      <div class="oc-modal-hl">
        <div class="oc-modal-ic info" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10z"/>
          </svg>
        </div>
        <div style="min-width:0">
          <h3 id="oc-title-modal-title" class="oc-modal-ttl">Titel</h3>
          <div id="oc-title-modal-sub" class="oc-modal-sub">—</div>
        </div>
      </div>

      <button class="oc-btn-ic" id="oc-title-modal-close" type="button" title="Schließen" aria-label="Schließen" style="width:32px;height:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="oc-modal-b">
      <div class="oc-modal-meta" id="oc-title-modal-body" style="white-space:pre-wrap;word-break:break-word"></div>
    </div>

    <div class="oc-modal-f">
      <button class="oc-btn-sm" id="oc-title-modal-ok" type="button">OK</button>
    </div>
  </div>
</div>


<div class="oc-bulk-bar" id="oc-bulk-bar">
    <span id="oc-bulk-count" style="font-weight:700; font-size:14px">0 ausgewählt</span>
    <div style="width:1px; height:20px; background:rgba(255,255,255,0.2)"></div>
    <button class="oc-btn-sm" id="oc-bulk-reminder-btn" style="background:#eba825; color:#fff; border:none">
      Bulk Erinnerung
    </button>

    <button class="oc-btn-sm" id="oc-bulk-report-btn" style="background:#8fc73e; color:#fff; border:none">
        Bulk Bericht
    </button>
    <button class="oc-btn-sm" id="oc-bulk-cancel" style="background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.3)">
        Abbrechen
    </button>
</div>

{{-- Reminder Modal --}}
<div class="oc-modal-backdrop" id="oc-reminder-modal" aria-hidden="true">
  <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="oc-reminder-title" aria-describedby="oc-reminder-desc">
    <div class="oc-modal-h">
      <div class="oc-modal-hl">
        <div class="oc-modal-ic info" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a3 3 0 0 0 6 0"/>
          </svg>
        </div>
        <div style="min-width:0">
          <h3 id="oc-reminder-title" class="oc-modal-ttl">Remind me later</h3>
          <div id="oc-reminder-sub" class="oc-modal-sub">—</div>
        </div>
      </div>

      <button class="oc-btn-ic" id="oc-reminder-close" type="button" title="Schließen" aria-label="Schließen" style="width:32px;height:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="oc-modal-b">
      <p id="oc-reminder-desc" class="oc-modal-note">
        Erinnerung setzen für den/die ausgewählten Eintrag/Einträge.
      </p>

      <div style="margin-top:12px">
        <div style="font-weight:900;font-size:12px;margin-bottom:6px;color:#111827">Wann erinnern?</div>
        <select id="oc-reminder-preset" class="oc-select" style="width:100%">
          <option value="30">In 30 Minuten</option>
          <option value="120">In 2 Stunden</option>
          <option value="240">In 4 Stunden</option>
          <option value="tomorrow_09">Morgen 09:00</option>
          <option value="next_week_09">Nächste Woche 09:00</option>
          <option value="custom">Benutzerdefiniert</option>
        </select>
      </div>

      <div id="oc-reminder-custom-wrap" class="hidden" style="margin-top:10px">
        <div style="font-weight:900;font-size:12px;margin-bottom:6px;color:#111827">Benutzerdefiniert (Datum/Uhrzeit)</div>
        <input id="oc-reminder-dt" type="datetime-local" class="oc-input" style="width:100%; padding-left:12px; background-image:none" />
      </div>

      <div style="margin-top:10px">
        <div style="font-weight:900;font-size:12px;margin-bottom:6px;color:#111827">Notiz (optional)</div>
        <textarea id="oc-reminder-note" class="oc-ta" placeholder="Optional..."></textarea>
      </div>

      <div id="oc-reminder-error" class="hidden"
           style="margin-top:10px;padding:10px;border-radius:12px;border:1px solid rgba(239,68,68,.25);background:var(--danger-light);color:var(--danger);font-size:12px;white-space:pre-wrap"></div>
    </div>

    <div class="oc-modal-f">
      <button class="oc-btn-sm" id="oc-reminder-cancel" type="button">Abbrechen</button>
      <button class="oc-btn-sm" id="oc-reminder-save" type="button" style="background:var(--primary);border-color:transparent;color:#fff">Speichern</button>
    </div>
  </div>
</div>


{{-- Bulk Report Drawer (similar to single drawer) --}}
<div class="oc-backdrop" id="oc-bulk-backdrop" aria-hidden="true">
  <aside class="oc-drawer" style="max-width: 450px">
    <div class="oc-dh">
        <h3 style="font-weight:900; margin:0">Sammelbericht</h3>
        <button class="oc-btn-ic" id="oc-bulk-dclose" type="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="oc-db">
        <div id="oc-bulk-list-preview" style="margin-bottom:20px; font-size:12px; color:var(--text-muted)"></div>
        <textarea class="oc-ta" id="oc-bulk-report-text" placeholder="Bericht für alle ausgewählten Einträge..."></textarea>
    </div>
    <div class="oc-df">
        <button class="oc-btn" id="oc-bulk-save">Berichte für alle speichern</button>
    </div>
  </aside>
</div>

{{-- Toast container --}}
<div class="oc-toast-wrap" id="oc-toast-wrap" aria-live="polite" aria-atomic="true"></div>

@once
@push('scripts')
 <script>
(() => {
  "use strict";

  /* =========================================================================
     1) BOOT
     ========================================================================= */
  const ROOT = document.getElementById("oc-overdue");
  if (!ROOT) return;

  const ENDPOINTS = {
    fetch:          @json(route("admin.overdue.fetch")),
    history:        @json(route("admin.overdue.history")),
    reports:        @json(route("admin.overdue.reports.list")),
    store:          @json(route("admin.overdue.reports.store")),
    skip:           @json(route("admin.overdue.skip")),
    bulk:           @json(route("admin.overdue.reports.bulk")),
    reminderUpsert: @json(route("admin.overdue.reminders.upsert")),
    reminderBulk:   @json(route("admin.overdue.reminders.bulk")),
  };

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  /* =========================================================================
     2) HELPERS
     ========================================================================= */
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  const esc = (s) => String(s ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");

  const debounce = (fn, ms = 250) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  const fmtDT = (s) => {
    if (!s) return "—";
    try {
      const d = new Date(String(s).replace(" ", "T"));
      return d.toLocaleString();
    } catch (_) {
      return String(s);
    }
  };

  function parseLaravel422(rawText) {
    try {
      const j = JSON.parse(rawText || "{}");
      const msg = j.message || "Validierung fehlgeschlagen.";
      if (j.errors && typeof j.errors === "object") {
        const lines = Object.values(j.errors).flat();
        return { message: msg, details: lines.join("\n") };
      }
      return { message: msg, details: "" };
    } catch (_) {
      return { message: "Fehler", details: String(rawText || "").slice(0, 500) };
    }
  }

  /* =========================================================================
     3) DOM CACHE
     ========================================================================= */
  const DOM = {
    list: $("#oc-list"),
    stats: $("#oc-stats"),

    refresh: $("#oc-refresh"),
    prev: $("#oc-prev"),
    next: $("#oc-next"),
    page: $("#oc-page"),
    search: $("#oc-search"),
    sort: $("#oc-sort"),
    perpage: $("#oc-perpage"),
    typeChecks: $$(".oc-type"),

    toastWrap: $("#oc-toast-wrap"),

    // Drawer
    backdrop: $("#oc-backdrop"),
    dClose: $("#oc-dclose"),
    dTitle: $("#oc-dtitle"),
    dSub: $("#oc-dsub"),
    dBody: $("#oc-dbody"),
    reportBox: $("#oc-reportbox"),
    reportText: $("#oc-report"),
    save: $("#oc-save"),

    // Skip Modal
    skipBD: $("#oc-skip-modal"),
    skipOK: $("#oc-skip-confirm"),
    skipNO: $("#oc-skip-cancel"),
    skipX: $("#oc-skip-close"),
    skipErr: $("#oc-skip-error"),
    skipTpl: $("#oc-skip-template"),
    skipTxt: $("#oc-skip-reason"),
    skipSub: $("#oc-skip-sub"),
    skipMeta: $("#oc-skip-meta"),

    // Title Modal
    titleBD: $("#oc-title-modal"),
    titleX: $("#oc-title-modal-close"),
    titleOK: $("#oc-title-modal-ok"),
    titleTTL: $("#oc-title-modal-title"),
    titleSub: $("#oc-title-modal-sub"),
    titleBody: $("#oc-title-modal-body"),

    // Bulk
    selectAll: $("#oc-select-all"),
    bulkBar: $("#oc-bulk-bar"),
    bulkCount: $("#oc-bulk-count"),
    bulkBtn: $("#oc-bulk-report-btn"),
    bulkCancel: $("#oc-bulk-cancel"),
    bulkBackdrop: $("#oc-bulk-backdrop"),
    bulkDClose: $("#oc-bulk-dclose"),
    bulkSave: $("#oc-bulk-save"),
    bulkText: $("#oc-bulk-report-text"),
    bulkListPreview: $("#oc-bulk-list-preview"),
    bulkReminderBtn: $("#oc-bulk-reminder-btn"),

    // Reminder Modal
    reminderBD: $("#oc-reminder-modal"),
    reminderX: $("#oc-reminder-close"),
    reminderCancel: $("#oc-reminder-cancel"),
    reminderSave: $("#oc-reminder-save"),
    reminderSub: $("#oc-reminder-sub"),
    reminderPreset: $("#oc-reminder-preset"),
    reminderCustomWrap: $("#oc-reminder-custom-wrap"),
    reminderDT: $("#oc-reminder-dt"),
    reminderNote: $("#oc-reminder-note"),
    reminderErr: $("#oc-reminder-error"),
  };

  /* =========================================================================
     4) STATE
     ========================================================================= */
  const DEFAULT_TYPES = ["inquiry", "task", "appointment", "ticket", "lead"];

  const state = {
    page: 1,
    per_page: 48, // ✅ default 48
    q: "",
    sort: "oldest",
    types: DEFAULT_TYPES.slice(),
    last: [],
    active: null,
    skipTarget: null,

    // reminder
    reminderTarget: null, // {mode:'single'|'bulk', items:[{type,id,title}], title:string}
  };

  /* =========================================================================
     5) NETWORK
     ========================================================================= */
  const controllers = new Map();

  function abortKey(key) {
    const c = controllers.get(key);
    if (c) {
      try { c.abort(); } catch (_) {}
      controllers.delete(key);
    }
  }

  async function api(key, url, data = {}, method = "GET") {
    abortKey(key);

    const controller = new AbortController();
    controllers.set(key, controller);

    const headers = {
      "Accept": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    };

    const opts = { method, headers, signal: controller.signal };
    let finalUrl = url;

    if (method === "GET") {
      const payload = { ...data };
      if (Array.isArray(payload.types)) payload.types = payload.types.join(",");
      const qs = new URLSearchParams(payload).toString();
      if (qs) finalUrl += (finalUrl.includes("?") ? "&" : "?") + qs;
    } else {
      headers["Content-Type"] = "application/json";
      if (CSRF) headers["X-CSRF-TOKEN"] = CSRF;
      opts.body = JSON.stringify(data);
    }

    let res, txt = "";
    try {
      res = await fetch(finalUrl, opts);
      txt = await res.text().catch(() => "");
    } finally {
      if (controllers.get(key) === controller) controllers.delete(key);
    }

    if (!res.ok) {
      const err = new Error(`HTTP ${res.status}`);
      err.status = res.status;
      err.raw = txt;
      throw err;
    }

    try { return JSON.parse(txt || "{}"); } catch (_) { return {}; }
  }

  /* =========================================================================
     6) TOAST
     ========================================================================= */
  function toast(kind, title, msg, ttlMs = 3200) {
    const wrap = DOM.toastWrap;
    if (!wrap) return;

    const icons = {
      ok:   `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
      bad:  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 18L18 6M6 6l12 12"/></svg>`
    };

    const el = document.createElement("div");
    el.className = "oc-toast";
    el.innerHTML = `
      <div class="oc-toast-ic ${esc(kind)}">${icons[kind] || icons.bad}</div>
      <div style="min-width:0">
        <p class="oc-toast-ttl">${esc(title || "")}</p>
        <p class="oc-toast-msg">${esc(msg || "")}</p>
      </div>
      <button class="oc-btn-ic oc-toast-x" type="button" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    `;

    wrap.appendChild(el);

    const kill = () => { try { el.remove(); } catch (_) {} };
    el.querySelector(".oc-toast-x")?.addEventListener("click", kill);
    setTimeout(kill, ttlMs);
  }

  /* =========================================================================
     7) LABELS / ICONS / FORMAT
     ========================================================================= */
  const typeLabel = (t) => ({
    inquiry: "Anfrage",
    task: "Aufgabe",
    appointment: "Termin",
    ticket: "Ticket",
    lead: "Lead",
  }[t] || t);

  const iconSvg = (type) => {
    const map = {
      inquiry: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 10h.01M12 10h.01M16 10h.01"/><path d="M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>`,
      task: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><path d="M9 5a2 2 0 002 2h2a2 2 0 002-2"/><path d="M9 5a2 2 0 012-2h2a2 2 0 012 2"/><path d="M9 14l2 2 4-4"/></svg>`,
      appointment: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3"/><path d="M5 11h14"/><path d="M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
      ticket: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/><path d="M15 7v2m0 4v2m0 4v2"/></svg>`,
      lead: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
    };
    return map[type] || map.inquiry;
  };

  const statusTone = (v) => {
    const k = String(v ?? "").toLowerCase().trim();
    if (["done","completed","complete","closed","verified","approved","success","won","published","active"].includes(k)) return "ok";
    if (["pending","in_progress","progress","working","running","paused","on_hold"].includes(k)) return "warn";
    if (["rejected","declined","failed","lost","cancel","canceled"].includes(k)) return "bad";
    return "";
  };

  const tStatusComposite = (v) => {
    const raw = String(v ?? "").trim();
    if (!raw) return "—";
    return raw.split("•").map(p => {
      const s = p.trim();
      return s ? (s.charAt(0).toUpperCase() + s.slice(1)) : "";
    }).filter(Boolean).join(" • ");
  };

  const recordTitle = (i) => i.task_title || i.record_title || i.title || i.name || i.subject || "—";
  const recordSubtitle = (i) => i.description || i.subtitle || "";

  const severity = (h) => (h >= 72 ? "od-high" : (h >= 48 ? "od-med" : "od-ok"));

  const formatOverdue = (h) => {
    const hours = Math.max(0, Math.floor(Number(h || 0)));
    const d = Math.floor(hours / 24);
    const r = hours % 24;
    return d > 0 ? `${d}T ${r}Std.` : `${hours} Std.`;
  };

  const reminderBadge = (rem) => {
    if (!rem) return "";
    const st = String(rem.status || "active");
    const at = rem.next_remind_at ? fmtDT(rem.next_remind_at) : "—";
    const tone = (st === "done" || st === "canceled") ? "ok" : (st === "snoozed" ? "warn" : "");
    return `<span class="oc-tag ${tone}" title="Reminder">${esc(st)} • ${esc(at)}</span>`;
  };

  /* =========================================================================
     8) TITLE MODAL (TRUNCATED TEXT)
     ========================================================================= */
  function openTitleModal(title, subtitle, body) {
    if (DOM.titleTTL) DOM.titleTTL.textContent = title || "Details";
    if (DOM.titleSub) DOM.titleSub.textContent = subtitle || "";
    if (DOM.titleBody) DOM.titleBody.textContent = body || "";
    DOM.titleBD?.classList.add("open");
  }

  function closeTitleModal() {
    DOM.titleBD?.classList.remove("open");
  }

  DOM.titleX?.addEventListener("click", closeTitleModal);
  DOM.titleOK?.addEventListener("click", closeTitleModal);
  DOM.titleBD?.addEventListener("click", (e) => { if (e.target === DOM.titleBD) closeTitleModal(); });

  /* =========================================================================
     9) BULK BAR
     ========================================================================= */
  function updateBulkBar() {
    const selected = $$(".oc-item-cb:checked");
    const count = selected.length;

    if (DOM.bulkCount) DOM.bulkCount.textContent = `${count} ausgewählt`;
    DOM.bulkBar?.classList.toggle("open", count > 0);
  }

  function clearBulkSelection() {
    if (DOM.selectAll) DOM.selectAll.checked = false;
    $$(".oc-item-cb").forEach(cb => {
      cb.checked = false;
      cb.closest(".oc-item")?.classList.remove("selected");
    });
    updateBulkBar();
  }

  DOM.selectAll?.addEventListener("change", (e) => {
    const on = !!e.target.checked;
    $$(".oc-item-cb").forEach(cb => {
      cb.checked = on;
      cb.closest(".oc-item")?.classList.toggle("selected", on);
    });
    updateBulkBar();
  });

  DOM.bulkCancel?.addEventListener("click", clearBulkSelection);

  // Bulk report modal open
  DOM.bulkBtn?.addEventListener("click", () => {
    const selected = $$(".oc-item-cb:checked");
    if (!selected.length) return;

    const names = selected.slice(0, 3).map(cb => cb.dataset.title || "—").join(", ");
    const remaining = selected.length - 3;

    if (DOM.bulkListPreview) {
      DOM.bulkListPreview.textContent = `Betrifft: ${names}${remaining > 0 ? " und " + remaining + " weitere" : ""}`;
    }

    DOM.bulkBackdrop?.classList.add("open");
  });

  DOM.bulkDClose?.addEventListener("click", () => DOM.bulkBackdrop?.classList.remove("open"));
  DOM.bulkBackdrop?.addEventListener("click", (e) => { if (e.target === DOM.bulkBackdrop) DOM.bulkBackdrop.classList.remove("open"); });

  // Submit bulk report
  DOM.bulkSave?.addEventListener("click", async () => {
    const text = (DOM.bulkText?.value || "").trim();
    if (!text || text.length < 3) return toast("warn", "Hinweis", "Bericht ist zu kurz (mind. 3 Zeichen).");

    const items = $$(".oc-item-cb:checked").map(cb => ({
      id: Number(cb.dataset.id),
      type: String(cb.dataset.type || ""),
    })).filter(x => x.id > 0 && x.type);

    if (!items.length) return;

    DOM.bulkSave.disabled = true;
    const oldTxt = DOM.bulkSave.textContent;
    DOM.bulkSave.textContent = "Speichere...";

    try {
      await api("bulk", ENDPOINTS.bulk, { items, report: text }, "POST");
      toast("ok", "Erfolg", `${items.length} Berichte erfolgreich gespeichert.`);

      DOM.bulkBackdrop?.classList.remove("open");
      if (DOM.bulkText) DOM.bulkText.value = "";
      clearBulkSelection();

      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Sammelbericht konnte nicht gespeichert werden.");
      }
    } finally {
      DOM.bulkSave.disabled = false;
      DOM.bulkSave.textContent = oldTxt;
    }
  });

  /* =========================================================================
     10) REMINDER MODAL
     ========================================================================= */
  function openReminderModalSingle(type, id, title) {
    state.reminderTarget = {
      mode: "single",
      items: [{ type, id, title }],
      title: `${typeLabel(type)} • ${title || ""}`
    };

    if (DOM.reminderSub) DOM.reminderSub.textContent = state.reminderTarget.title;
    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");
    if (DOM.reminderNote) DOM.reminderNote.value = "";
    if (DOM.reminderPreset) DOM.reminderPreset.value = "120";
    if (DOM.reminderCustomWrap) DOM.reminderCustomWrap.classList.add("hidden");
    if (DOM.reminderDT) DOM.reminderDT.value = "";

    DOM.reminderBD?.classList.add("open");
  }

  function openReminderModalBulk(items) {
    const names = items.slice(0, 3).map(x => x.title || "—").join(", ");
    const more = items.length - 3;

    state.reminderTarget = {
      mode: "bulk",
      items,
      title: `Bulk • ${names}${more > 0 ? " und " + more + " weitere" : ""}`
    };

    if (DOM.reminderSub) DOM.reminderSub.textContent = state.reminderTarget.title;
    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");
    if (DOM.reminderNote) DOM.reminderNote.value = "";
    if (DOM.reminderPreset) DOM.reminderPreset.value = "120";
    if (DOM.reminderCustomWrap) DOM.reminderCustomWrap.classList.add("hidden");
    if (DOM.reminderDT) DOM.reminderDT.value = "";

    DOM.reminderBD?.classList.add("open");
  }

  function closeReminderModal() {
    DOM.reminderBD?.classList.remove("open");
  }

  function presetToPayload(preset) {
    const now = new Date();
    const mk = (d) => d.toISOString();

    if (/^\d+$/.test(preset)) {
      return { minutes: parseInt(preset, 10), next_remind_at: null };
    }

    if (preset === "tomorrow_09") {
      const d = new Date(now);
      d.setDate(d.getDate() + 1);
      d.setHours(9, 0, 0, 0);
      return { minutes: null, next_remind_at: mk(d) };
    }

    if (preset === "next_week_09") {
      const d = new Date(now);
      d.setDate(d.getDate() + 7);
      d.setHours(9, 0, 0, 0);
      return { minutes: null, next_remind_at: mk(d) };
    }

    return { minutes: null, next_remind_at: null };
  }

  DOM.reminderPreset?.addEventListener("change", (e) => {
    const v = e.target.value;
    const show = (v === "custom");
    DOM.reminderCustomWrap?.classList.toggle("hidden", !show);
  });

  DOM.reminderX?.addEventListener("click", closeReminderModal);
  DOM.reminderCancel?.addEventListener("click", closeReminderModal);
  DOM.reminderBD?.addEventListener("click", (e) => { if (e.target === DOM.reminderBD) closeReminderModal(); });

  DOM.reminderSave?.addEventListener("click", async () => {
    const target = state.reminderTarget;
    if (!target?.items?.length) return;

    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");

    const preset = DOM.reminderPreset?.value || "120";
    const note = (DOM.reminderNote?.value || "").trim();

    let payload = presetToPayload(preset);

    if (preset === "custom") {
      const dt = DOM.reminderDT?.value;
      if (!dt) {
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = "Bitte Datum/Uhrzeit auswählen.";
          DOM.reminderErr.classList.remove("hidden");
        }
        return;
      }
      payload = { minutes: null, next_remind_at: new Date(dt).toISOString() };
    }

    DOM.reminderSave.disabled = true;
    const oldTxt = DOM.reminderSave.textContent;
    DOM.reminderSave.textContent = "Speichere...";

    try {
      if (target.mode === "single") {
        const it = target.items[0];
        await api("reminder_upsert", ENDPOINTS.reminderUpsert, {
          type: it.type,
          id: it.id,
          minutes: payload.minutes,
          next_remind_at: payload.next_remind_at,
          note
        }, "POST");
        toast("ok", "Reminder gesetzt", "Erinnerung gespeichert.");
      } else {
        await api("reminder_bulk", ENDPOINTS.reminderBulk, {
          items: target.items.map(x => ({ type: x.type, id: x.id })),
          minutes: payload.minutes,
          next_remind_at: payload.next_remind_at,
          note
        }, "POST");
        toast("ok", "Bulk Reminder", `${target.items.length} Erinnerungen gespeichert.`);
      }

      closeReminderModal();
      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = info.details || info.message;
          DOM.reminderErr.classList.remove("hidden");
        }
      } else {
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = "Fehler beim Speichern.";
          DOM.reminderErr.classList.remove("hidden");
        }
      }
    } finally {
      DOM.reminderSave.disabled = false;
      DOM.reminderSave.textContent = oldTxt;
    }
  });

  // Bulk reminder button -> open modal
  DOM.bulkReminderBtn?.addEventListener("click", () => {
    const items = $$(".oc-item-cb:checked").map(cb => ({
      type: String(cb.dataset.type || ""),
      id: Number(cb.dataset.id || 0),
      title: String(cb.dataset.title || "")
    })).filter(x => x.type && x.id > 0);

    if (!items.length) return;
    openReminderModalBulk(items);
  });

  /* =========================================================================
     11) RENDERING
     ========================================================================= */
  function renderStats(s) {
    if (!DOM.stats) return;
    const conf = [
      { k: "total", l: "Gesamt",    c: "#111827" },
      { k: "inquiry", l: "Anfragen", c: "var(--primary)" },
      { k: "task", l: "Aufgaben",   c: "var(--success)" },
      { k: "appointment", l: "Termine", c: "#0ea5e9" },
      { k: "ticket", l: "Tickets",  c: "var(--warning)" },
      { k: "lead", l: "Leads",      c: "#9333ea" },
    ];

    DOM.stats.innerHTML = conf.map(x => `
      <div class="oc-stat">
        <div class="oc-stat-l"><span class="oc-dot" style="background:${x.c}"></span>${x.l}</div>
        <div class="oc-stat-v">${Number(s?.[x.k] ?? 0)}</div>
      </div>
    `).join("");
  }

  function setPager(p) {
    if (!DOM.page || !DOM.prev || !DOM.next) return;

    const page = Number(p?.page ?? state.page);
    const per = Number(p?.per_page ?? state.per_page);
    const total = Number(p?.total ?? 0);
    const pages = Math.max(1, Math.ceil(total / Math.max(1, per)));

    DOM.page.textContent = `Seite ${page} von ${pages}`;
    DOM.prev.disabled = page <= 1;
    DOM.next.disabled = !p?.has_more;
  }

  // Reports list renderer (simple, safe)
  function renderReports(rows) {
    if (!rows?.length) return `<div style="padding:12px;color:var(--text-muted)">Keine Berichte vorhanden.</div>`;
    return `<div class="oc-tl">${
      rows.map(r => `
        <div class="oc-tli">
          <div class="oc-tld"></div>
          <div class="oc-tldt">${esc(r.created_at || "—")}</div>
          <div style="font-weight:900;font-size:14px;margin-bottom:4px;color:#111827">${esc(r.employee_name || "—")}</div>
          <div class="oc-tlc">${esc(r.report || "")}</div>
        </div>
      `).join("")
    }</div>`;
  }

  // History renderer (placeholder -> you can keep your detailed one if you want)
  function renderHistory(type, res) {
    const h = res?.history || {};
    let html = `<div class="oc-tl">`;
    const changes = Array.isArray(h.changes) ? h.changes : [];
    if (!changes.length) {
      html += `<div style="padding:12px;color:var(--text-muted)">Kein Verlauf vorhanden.</div>`;
    } else {
      html += changes.map(c => `
        <div class="oc-tli">
          <div class="oc-tld"></div>
          <div class="oc-tldt">${esc(c.created_at || "—")}</div>
          <div style="font-weight:900;font-size:14px;margin-bottom:4px;color:#111827">${esc(c.title || c.status || "Änderung")}</div>
          ${c.note ? `<div class="oc-tlc">${esc(c.note)}</div>` : ``}
        </div>
      `).join("");
    }
    html += `</div>`;
    return html;
  }

  function renderList(items) {
    if (!DOM.list) return;

    if (!items?.length) {
      DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">Alles erledigt! Keine überfälligen Einträge.</div>`;
      return;
    }

    DOM.list.innerHTML = items.map(item => {
      const type = String(item.type || "").trim();
      const safeId = Number(item.id || 0);

      const title = recordTitle(item);
      const subtitle = recordSubtitle(item);

      const stDe = tStatusComposite(item.status);
      const stTone = statusTone(item.status);

      const sev = severity(item.overdue_hours);
      const timeTxt = formatOverdue(item.overdue_hours);

      const url = item.link || "#";

      const rem = item.reminder || null;
      const showRemindBtn = !rem; // ✅ button shown only until reminder exists

      // truncate long content -> show title modal on click
      const summary = String(item.changed_summary || "—");
      const summaryShort = summary.length > 120 ? summary.slice(0, 120) + "…" : summary;
      const summaryIsLong = summary.length > 120;

      return `
        <div class="oc-item" data-id="${safeId}" data-type="${esc(type)}">

          <div class="oc-checkbox-wrap">
            <input type="checkbox" class="oc-item-cb oc-cb-custom"
              data-id="${safeId}"
              data-type="${esc(type)}"
              data-title="${esc(title)}">
          </div>

          <div class="oc-ic type-${esc(type)}" title="${esc(typeLabel(type))}">
            ${iconSvg(type)}
          </div>

          <div class="oc-main">
            <div class="oc-ttl">${esc(title)}</div>
            <div class="oc-subt">${esc(subtitle)}</div>

            <div class="oc-sum" ${summaryIsLong ? `data-oc-action="open_title" data-oc-title="Zusammenfassung" data-oc-sub="${esc(typeLabel(type))} • ${esc(title)}" data-oc-body="${esc(summary)}"` : ""} style="${summaryIsLong ? "cursor:pointer" : ""}">
              ${esc(summaryShort)}
            </div>
          </div>

          <div class="oc-meta">
            <span class="oc-tag ${stTone}">${esc(stDe)}</span>
            ${reminderBadge(rem)}
          </div>

          <div class="oc-time">
            <span class="oc-od ${sev}">
              <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 8v4l3 3"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              ${esc(timeTxt)}
            </span>
            <span style="font-size:11px;color:var(--text-muted)">Zuletzt: ${esc(item.last_activity_at || "—")}</span>
          </div>

          <div class="oc-actions">
            <a href="${esc(url)}" target="_blank" class="oc-btn-ic primary" title="Öffnen" rel="noopener">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>
                <path d="M14 4h6m0 0v6m0-6L10 14"/>
              </svg>
            </a>

            ${showRemindBtn ? `
              <button class="oc-btn-ic" type="button"
                data-oc-action="remind"
                data-oc-type="${esc(type)}"
                data-oc-id="${safeId}"
                data-oc-title="${esc(title)}"
                title="Remind me later">
                <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                  <path d="M9 17a3 3 0 0 0 6 0"/>
                </svg>
              </button>
            ` : ``}

            <button class="oc-btn-ic" type="button"
              data-oc-action="reports"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Berichte">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12h6m-6 4h6"/><path d="M7 3h6l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
              </svg>
            </button>

            <button class="oc-btn-ic" type="button"
              data-oc-action="history"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Verlauf">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v5h5"/><path d="M3.2 12a9 9 0 1 0 2.6-6.4"/><path d="M12 7v5l3 3"/>
              </svg>
            </button>

            <button class="oc-btn-ic danger" type="button"
              data-oc-action="skip"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Überspringen">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h12"/><path d="M13 6l6 6-6 6"/>
              </svg>
            </button>
          </div>
        </div>
      `;
    }).join("");

    updateBulkBar();
  }

  /* =========================================================================
     12) CORE LOAD
     ========================================================================= */
  async function load() {
    if (DOM.list) DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">Lädt...</div>`;

    // reset selection on reload
    if (DOM.selectAll) DOM.selectAll.checked = false;
    updateBulkBar();

    try {
      const data = await api("fetch", ENDPOINTS.fetch, {
        q: state.q,
        sort: state.sort,
        page: state.page,
        per_page: state.per_page,
        types: state.types?.length ? state.types : DEFAULT_TYPES,
      }, "GET");

      state.last = Array.isArray(data?.items) ? data.items : [];
      renderStats(data?.stats || {});
      renderList(state.last);
      setPager(data?.pagination || {});
    } catch (e) {
      if (DOM.list) DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--danger)">Fehler beim Laden.</div>`;
      toast("bad", "Fehler", "Daten konnten nicht geladen werden.");
    }
  }

  /* =========================================================================
     13) TOOLBAR EVENTS
     ========================================================================= */
  DOM.refresh?.addEventListener("click", () => load());

  DOM.prev?.addEventListener("click", () => {
    state.page = Math.max(1, state.page - 1);
    load();
  });

  DOM.next?.addEventListener("click", () => {
    state.page += 1;
    load();
  });

  DOM.search?.addEventListener("input", debounce((e) => {
    state.q = e.target.value || "";
    state.page = 1;
    load();
  }, 300));

  DOM.sort?.addEventListener("change", (e) => {
    state.sort = e.target.value || "oldest";
    state.page = 1;
    load();
  });

  DOM.perpage?.addEventListener("change", (e) => {
    state.per_page = parseInt(e.target.value, 10) || 48;
    state.page = 1;
    load();
  });

  DOM.typeChecks.forEach(cb => cb.addEventListener("change", () => {
    state.types = $$(".oc-type:checked").map(x => x.value);
    state.page = 1;
    load();
  }));

  /* =========================================================================
     14) LIST EVENTS (CHECKBOX + ACTIONS)
     ========================================================================= */
  DOM.list?.addEventListener("change", (e) => {
    if (!e.target.classList.contains("oc-item-cb")) return;
    const row = e.target.closest(".oc-item");
    row?.classList.toggle("selected", !!e.target.checked);
    if (!e.target.checked && DOM.selectAll) DOM.selectAll.checked = false;
    updateBulkBar();
  });

  DOM.list?.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-oc-action]");
    if (!btn) return;

    const action = btn.dataset.ocAction;

    // title modal open
    if (action === "open_title") {
      openTitleModal(btn.dataset.ocTitle, btn.dataset.ocSub, btn.dataset.ocBody);
      return;
    }

    const type = btn.dataset.ocType;
    const id = Number(btn.dataset.ocId || 0);
    const title = btn.dataset.ocTitle || "";

    if (!action || !type || !id) return;

    state.active = { type, id, title };

    // Reports
    if (action === "reports") {
      if (DOM.dTitle) DOM.dTitle.textContent = "Berichte";
      if (DOM.dSub) DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
      DOM.backdrop?.classList.add("open");
      DOM.reportBox?.classList.remove("hidden");
      if (DOM.reportText) DOM.reportText.value = "";

      try {
        const res = await api("reports", ENDPOINTS.reports, { type, id }, "GET");
        if (DOM.dBody) DOM.dBody.innerHTML = renderReports(res?.rows || []);
      } catch (_) {
        toast("bad", "Fehler", "Berichte konnten nicht geladen werden.");
      }
      return;
    }

    // History
    if (action === "history") {
      if (DOM.dTitle) DOM.dTitle.textContent = "Verlauf";
      if (DOM.dSub) DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
      DOM.backdrop?.classList.add("open");
      DOM.reportBox?.classList.add("hidden");

      try {
        const res = await api("history", ENDPOINTS.history, { type, id }, "GET");
        if (DOM.dBody) DOM.dBody.innerHTML = renderHistory(type, res);
      } catch (_) {
        toast("bad", "Fehler", "Verlauf konnte nicht geladen werden.");
      }
      return;
    }

    // Skip
    if (action === "skip") {
      state.skipTarget = { type, id, title };

      if (DOM.skipSub) DOM.skipSub.textContent = `${typeLabel(type)} • ${title}`;
      if (DOM.skipMeta) {
        DOM.skipMeta.innerHTML = `
          <div><b>Typ:</b> ${esc(typeLabel(type))}</div>
          <div style="margin-top:6px"><b>Titel:</b> ${esc(title || "—")}</div>
        `;
      }

      if (DOM.skipErr) DOM.skipErr.classList.add("hidden");
      if (DOM.skipTxt) DOM.skipTxt.value = "";
      if (DOM.skipTpl) DOM.skipTpl.value = "";
      DOM.skipBD?.classList.add("open");
      return;
    }

    // Reminder
    if (action === "remind") {
      openReminderModalSingle(type, id, title);
      return;
    }
  });

  /* =========================================================================
     15) DRAWER EVENTS
     ========================================================================= */
  DOM.dClose?.addEventListener("click", () => DOM.backdrop?.classList.remove("open"));
  DOM.backdrop?.addEventListener("click", (e) => { if (e.target === DOM.backdrop) DOM.backdrop.classList.remove("open"); });

  DOM.save?.addEventListener("click", async () => {
    const text = (DOM.reportText?.value || "").trim();
    if (text.length < 3) return toast("warn", "Zu kurz", "Bitte mehr Text eingeben.");

    if (!state.active?.type || !state.active?.id) return;

    DOM.save.disabled = true;

    try {
      await api("store", ENDPOINTS.store, { type: state.active.type, id: state.active.id, report: text }, "POST");
      toast("ok", "Gespeichert", "Bericht gespeichert.");
      if (DOM.reportText) DOM.reportText.value = "";

      // refresh drawer reports
      const res = await api("reports", ENDPOINTS.reports, { type: state.active.type, id: state.active.id }, "GET");
      if (DOM.dBody) DOM.dBody.innerHTML = renderReports(res?.rows || []);

      // refresh list
      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Konnte nicht speichern.");
      }
    } finally {
      DOM.save.disabled = false;
    }
  });

  /* =========================================================================
     16) SKIP MODAL EVENTS
     ========================================================================= */
  function closeSkip() { DOM.skipBD?.classList.remove("open"); }

  DOM.skipX?.addEventListener("click", closeSkip);
  DOM.skipNO?.addEventListener("click", closeSkip);
  DOM.skipBD?.addEventListener("click", (e) => { if (e.target === DOM.skipBD) closeSkip(); });

  DOM.skipOK?.addEventListener("click", async () => {
    const reason = (DOM.skipTxt?.value || "").trim();
    if (reason.length < 3) return toast("warn", "Grund fehlt", "Bitte Grund eingeben.");

    if (!state.skipTarget?.type || !state.skipTarget?.id) return;

    DOM.skipOK.disabled = true;

    try {
      await api("skip", ENDPOINTS.skip, {
        type: state.skipTarget.type,
        id: state.skipTarget.id,
        skip_reason: reason,
      }, "POST");

      toast("ok", "Übersprungen", "Eintrag entfernt.");
      closeSkip();
      if (DOM.skipTxt) DOM.skipTxt.value = "";
      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Konnte nicht überspringen.");
      }
    } finally {
      DOM.skipOK.disabled = false;
    }
  });

  /* =========================================================================
     17) INIT
     ========================================================================= */
  load();

})();
</script>


@endpush
@endonce
