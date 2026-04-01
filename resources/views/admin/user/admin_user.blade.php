{{-- resources/views/admin/user/admin_user.blade.php (AJAX version) --}}
@extends('admin.layouts.app')
@section('title','Admin Users')

@once
@push('style')
<style>
  :root{
    --app-bg:#f3f4f6;
    --card-bg:#fff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;

    /* Brand palette */
    --green:#93c21c;
    --blue:#74b2d4;
    --purple:#c08ea;
    --orange:#f59e0b;
    --red:#ef4444;

    --green-light:#cfe09b;
    --blue-light:#cfe7ea;
    --purple-light:rgba(192,142,234,.14);
    --orange-light:#fffbeb;

    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;

    /* ✅ adjust if your navbar is taller */
    --nav-offset: 132px;
  }

  body{ background:var(--app-bg); }

  /* ✅ more top space so header + Add button are not hidden under navbar */
.ua-wrap {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main);
    max-width: 94%;
    padding: 24px 30px 0px 69px;
}

  @media(max-width:900px){
    :root{ --nav-offset: 156px; }
  }

  .ua-titlebar{
    display:flex;align-items:flex-end;justify-content:space-between;gap:12px;
    margin-bottom:14px;
    padding:14px 14px;
    border:1px solid rgba(116,178,212,.35);
    border-radius:var(--radius);
    background:
      linear-gradient(135deg, rgba(116,178,212,.18), rgba(147,194,28,.12)),
      #fff;
    box-shadow:var(--shadow-sm);
    margin-top:82px
  }
  .ua-title{font-size:24px;font-weight:900;letter-spacing:-.025em;color:#0f172a}
  .ua-sub{font-size:14px;color:var(--text-muted);margin-top:4px}

  /* icon sizing */
  .ua-ic{width:18px;height:18px;display:block}
  .ua-ic-sm{width:16px;height:16px;display:block}

  .ua-btn-ic{
    width:40px;height:40px;border-radius:12px;border:1px solid var(--border);
    background:#fff;display:inline-flex;align-items:center;justify-content:center;
    color:#64748b;cursor:pointer;transition:var(--transition)
  }
  .ua-btn-ic:hover{background:#f8fafc;color:#0f172a;border-color:#d1d5db}
  .ua-btn-ic.brand{
    border-color:rgba(116,178,212,.35);
    background:rgba(116,178,212,.12);
    color:#0b5b7a;
  }

  .ua-btn{
    border:none;background:var(--green);color:#fff;
    padding:10px 14px;border-radius:12px;
    font-weight:900;cursor:pointer;transition:var(--transition);
    display:inline-flex;align-items:center;gap:8px;
    box-shadow:0 10px 22px -14px rgba(147,194,28,.75);
    white-space:nowrap;
  }
  .ua-btn:hover{filter:brightness(.95)}
  .ua-btn.light{
    background:rgba(116,178,212,.14);
    color:#0b5b7a;
    border:1px solid rgba(116,178,212,.35);
    box-shadow:none;
  }
  .ua-btn.light:hover{background:rgba(116,178,212,.20)}

  .ua-stats{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
    gap:14px;margin-bottom:16px
  }
  .ua-stat{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:14px;
    box-shadow:var(--shadow-sm);
    transition:var(--transition);
    position:relative;overflow:hidden;
  }
  .ua-stat:before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:3px;
    background:linear-gradient(90deg,var(--blue),var(--green),var(--orange));
    opacity:.95;
  }
  .ua-stat:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
  .ua-stat-l{
    font-size:12px;font-weight:900;text-transform:uppercase;
    color:var(--text-muted);letter-spacing:.06em;margin-bottom:8px;
    display:flex;align-items:center;gap:8px
  }
  .ua-dot{width:8px;height:8px;border-radius:999px;display:inline-block}
  .ua-stat-v{font-size:28px;font-weight:900;line-height:1;color:#0f172a}

  .ua-toolbar{
    background:var(--card-bg);
    border:1px solid rgba(116,178,212,.28);
    border-radius:var(--radius);
    padding:12px 14px;
    display:flex;flex-wrap:wrap;gap:12px;
    align-items:center;justify-content:space-between;
    margin-bottom:14px;
    box-shadow:var(--shadow-sm);
  }
  .ua-fg{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
  .ua-sep{width:1px;height:26px;background:var(--border);margin:0 4px}

  .ua-input{
    background:#f9fafb;border:1px solid var(--border);border-radius:12px;
    padding:10px 12px 10px 40px;font-size:14px;outline:none;transition:var(--transition);
    min-width:280px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:12px center;background-size:16px
  }
  .ua-input:focus{
    background:#fff;border-color:rgba(116,178,212,.9);
    box-shadow:0 0 0 3px rgba(116,178,212,.22);
  }

  .ua-select{
    padding:10px 36px 10px 12px;border-radius:12px;border:1px solid var(--border);
    background-color:#fff;font-size:13px;cursor:pointer;outline:none;appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 12px center;background-size:14px
  }
  .ua-select:focus{
    border-color:rgba(147,194,28,.9);
    box-shadow:0 0 0 3px rgba(147,194,28,.22);
  }

  .ua-list{display:flex;flex-direction:column;gap:12px}
  .ua-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:14px;
    display:grid;gap:14px;align-items:center;
    grid-template-columns:56px 1fr 260px 220px 220px;
    transition:var(--transition);
    position:relative;
  }
  .ua-item:hover{border-color:rgba(147,194,28,.70);box-shadow:var(--shadow)}
  .ua-item:after{
    content:"";position:absolute;left:0;top:0;bottom:0;width:4px;
    background:linear-gradient(180deg,var(--blue),var(--green));
    border-top-left-radius:var(--radius);
    border-bottom-left-radius:var(--radius);
    opacity:.60;
  }
  @media(max-width:1100px){
    .ua-item{grid-template-columns:56px 1fr;grid-template-rows:auto auto auto auto;gap:10px}
    .ua-meta,.ua-created,.ua-actions{grid-column:2}
    .ua-actions{justify-self:start}
  }

  .ua-avatar{
    width:56px;height:56px;border-radius:16px;
    background:linear-gradient(135deg, rgba(116,178,212,.20), rgba(192,142,234,.10));
    overflow:hidden;border:1px solid rgba(116,178,212,.22);
    display:flex;align-items:center;justify-content:center;
  }
  .ua-avatar img{width:100%;height:100%;object-fit:cover}
  .ua-main{min-width:0}
  .ua-name{font-weight:900;font-size:15px;margin:0 0 4px 0;color:#0f172a}
  .ua-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

  .ua-meta{display:flex;flex-wrap:wrap;gap:8px}
  .ua-tag{
    display:inline-flex;align-items:center;gap:6px;
    padding:6px 10px;border-radius:999px;
    font-size:12px;font-weight:900;
    border:1px solid var(--border);
    background:#fff;color:#111827;
  }
  .ua-tag.ok{background:rgba(147,194,28,.14);color:#4d6a08;border-color:rgba(147,194,28,.28)}
  .ua-tag.bad{background:rgba(245,158,11,.14);color:#9a5b00;border-color:rgba(245,158,11,.25)}
  .ua-tag.info{background:rgba(116,178,212,.16);color:#0b5b7a;border-color:rgba(116,178,212,.30)}
  .ua-tag.purple{background:var(--purple-light);color:#6a2c8f;border-color:rgba(192,142,234,.28)}

  .ua-created{font-size:12px;color:var(--text-muted)}

  .ua-actions{display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap}
  .ua-mini{
    width:40px;height:40px;border-radius:12px;
    border:1px solid var(--border);
    background:#fff;cursor:pointer;transition:var(--transition);
    display:inline-flex;align-items:center;justify-content:center;
    color:#111827;
  }
  .ua-mini:hover{background:#f8fafc;border-color:#d1d5db}
  .ua-mini.primary{background:rgba(116,178,212,.16);border-color:rgba(116,178,212,.28);color:#0b5b7a}
  .ua-mini.warn{background:rgba(245,158,11,.16);border-color:rgba(245,158,11,.25);color:#9a5b00}
  .ua-mini.purple{background:var(--purple-light);border-color:rgba(192,142,234,.28);color:#6a2c8f}
  .ua-mini.danger{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.22);color:#b91c1c}

  .ua-pager{display:flex;justify-content:center;align-items:center;gap:12px;margin-top:14px}
  .ua-pager span{font-size:13px;color:var(--text-muted)}

  /* modal */
  .ua-bd{
    position:fixed;inset:0;background:rgba(17,24,39,.55);
    backdrop-filter:blur(2px);z-index:9999; /* ensure above nav */
    opacity:0;pointer-events:none;transition:opacity .2s ease;
    display:flex;align-items:center;justify-content:center;padding:18px
  }
  .ua-bd.open{opacity:1;pointer-events:auto}
  .ua-modal{
    width:100%;max-width:560px;background:#fff;
    border:1px solid rgba(116,178,212,.25);
    border-radius:16px;box-shadow:var(--shadow);overflow:hidden;
    transform:translateY(10px) scale(.99);transition:transform .2s ease
  }
  .ua-bd.open .ua-modal{transform:translateY(0) scale(1)}
  .ua-mh{
    padding:14px 16px;
    border-bottom:1px solid rgba(116,178,212,.22);
    background:linear-gradient(135deg, rgba(116,178,212,.18), rgba(147,194,28,.10));
    display:flex;justify-content:space-between;gap:10px;align-items:flex-start
  }
  .ua-mt{font-weight:900;margin:0;color:#0f172a}
  .ua-mx{width:40px;height:40px}
  .ua-mb{padding:16px}
  .ua-mf{padding:14px 16px;border-top:1px solid var(--border);background:#fafafa;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap}
  .ua-field{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
  .ua-lbl{font-size:12px;font-weight:900;color:#0f172a}
  .ua-inp, .ua-sel{
    width:100%;border:1px solid var(--border);
    border-radius:12px;padding:10px 12px;font-size:14px;outline:none;
    transition:var(--transition);background:#fff
  }
  .ua-inp:focus,.ua-sel:focus{
    border-color:rgba(147,194,28,.85);
    box-shadow:0 0 0 3px rgba(147,194,28,.20)
  }
  .ua-err{
    display:none;margin-top:10px;padding:10px;border-radius:12px;
    border:1px solid rgba(239,68,68,.25);
    background:rgba(239,68,68,.10);
    color:#b91c1c;font-size:12px;white-space:pre-wrap
  }
  .ua-err.show{display:block}

  /* Small helper row for modal buttons */
  .ua-inline{display:flex;gap:10px;align-items:center}

</style>
@endpush
@endonce

@section('content')
<div class="ua-wrap" id="ua-root">
  <div class="ua-titlebar">
    <div>
      <div class="ua-title">Administrators</div>
      <div class="ua-sub">AJAX • Search • Sort • Filter • Analytics</div>
    </div>

    <div class="ua-inline">
      <button class="ua-btn-ic brand" id="ua-refresh" type="button" title="Refresh" aria-label="Refresh">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
      </button>

      <button class="ua-btn" id="ua-open-add" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
        </svg>
        Add Admin
      </button>
    </div>
  </div>

  <div class="ua-stats" id="ua-stats"></div>

  <div class="ua-toolbar" role="region" aria-label="Filters">
    <div class="ua-fg">
      <input class="ua-input" id="ua-search" placeholder="Search name / email..." autocomplete="off" />
      <div class="ua-sep" aria-hidden="true"></div>

      <select class="ua-select" id="ua-status" aria-label="Status">
        <option value="all">All Status</option>
        <option value="active">Active only</option>
        <option value="inactive">Inactive only</option>
      </select>

      <select class="ua-select" id="ua-admin" aria-label="Role">
        <option value="1">Admins</option>
        <option value="2">Limited</option>
        <option value="all">All Roles</option>
      </select>
    </div>

    <div class="ua-fg">
      <select class="ua-select" id="ua-sort" aria-label="Sort">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
        <option value="name_asc">Name A→Z</option>
        <option value="name_desc">Name Z→A</option>
        <option value="email_asc">Email A→Z</option>
        <option value="email_desc">Email Z→A</option>
      </select>

      <select class="ua-select" id="ua-perpage" aria-label="Per page">
        <option value="12">12 / page</option>
        <option value="24">24 / page</option>
        <option value="48">48 / page</option>
      </select>
    </div>
  </div>

  <div id="ua-list" class="ua-list" aria-live="polite">
    <div style="text-align:center;padding:60px;color:var(--text-muted)">Loading...</div>
  </div>

  <div class="ua-pager" role="navigation" aria-label="Pagination">
    <button class="ua-btn-ic" id="ua-prev" type="button" title="Prev" aria-label="Prev" disabled>
      <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
    <span id="ua-page">Page 1</span>
    <button class="ua-btn-ic" id="ua-next" type="button" title="Next" aria-label="Next" disabled>
      <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </button>
  </div>
</div>

{{-- ADD/EDIT MODAL --}}
<div class="ua-bd" id="ua-modal" aria-hidden="true">
  <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-modal-title">
    <div class="ua-mh">
      <div>
        <h3 class="ua-mt" id="ua-modal-title">Add Admin</h3>
        <div style="font-size:12px;color:var(--text-muted)" id="ua-modal-sub">—</div>
      </div>
      <button class="ua-btn-ic ua-mx" id="ua-modal-x" type="button" aria-label="Close">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="ua-mb">
      <div class="ua-field">
        <div class="ua-lbl">Employee</div>
        <select class="ua-sel" id="ua-f-employee">
          @foreach($employee as $e)
            <option value="{{ $e->id }}">{{ $e->name }} {{ $e->lastname }}</option>
          @endforeach
        </select>
        <div style="font-size:12px;color:var(--text-muted)">Saved into <b>users.name</b> as employee id.</div>
      </div>

      <div class="ua-field">
        <div class="ua-lbl">Email</div>
        <input class="ua-inp" id="ua-f-email" type="email" placeholder="email@example.com">
      </div>

      <div class="ua-field" id="ua-pass-wrap">
        <div class="ua-lbl">Password</div>
        <input class="ua-inp" id="ua-f-pass" type="password" placeholder="min 8 chars">
      </div>

      <div class="ua-field">
        <div class="ua-lbl">Role</div>
        <select class="ua-sel" id="ua-f-role">
          <option value="1">Admin</option>
          <option value="2">Limited</option>
        </select>
      </div>

      <div class="ua-field">
        <label style="display:flex;gap:10px;align-items:center;font-weight:800">
          <input type="checkbox" id="ua-f-active" checked>
          Active
        </label>
      </div>

      <div class="ua-err" id="ua-modal-err"></div>
    </div>

    <div class="ua-mf">
      <button class="ua-btn light" id="ua-modal-cancel" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Cancel
      </button>
      <button class="ua-btn" id="ua-modal-save" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h11l5 5v9a2 2 0 01-2 2z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-8H7v8"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v4h8"/>
        </svg>
        Save
      </button>
    </div>
  </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="ua-bd" id="ua-del" aria-hidden="true">
  <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-del-title">
    <div class="ua-mh">
      <div>
        <h3 class="ua-mt" id="ua-del-title">Delete user?</h3>
        <div style="font-size:12px;color:var(--text-muted)" id="ua-del-sub">—</div>
      </div>
      <button class="ua-btn-ic ua-mx" id="ua-del-x" type="button" aria-label="Close">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="ua-mb">
      <div style="font-size:13px;color:#374151;line-height:1.5">
        This will permanently delete the user record.
      </div>
      <div class="ua-err" id="ua-del-err"></div>
    </div>
    <div class="ua-mf">
      <button class="ua-btn light" id="ua-del-cancel" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Cancel
      </button>
      <button class="ua-btn" id="ua-del-confirm" type="button" style="background:var(--red)">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 16h10l1-16"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v7M14 11v7"/>
        </svg>
        Delete
      </button>
    </div>
  </div>
</div>

{{-- PASSWORD MODAL --}}
<div class="ua-bd" id="ua-pass" aria-hidden="true">
  <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-pass-title">
    <div class="ua-mh">
      <div>
        <h3 class="ua-mt" id="ua-pass-title">Change password</h3>
        <div style="font-size:12px;color:var(--text-muted)" id="ua-pass-sub">—</div>
      </div>
      <button class="ua-btn-ic ua-mx" id="ua-pass-x" type="button" aria-label="Close">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="ua-mb">
      <div class="ua-field">
        <div class="ua-lbl">New password</div>
        <input class="ua-inp" id="ua-pass-new" type="password" placeholder="min 8 chars">
      </div>
      <div class="ua-field">
        <div class="ua-lbl">Confirm password</div>
        <input class="ua-inp" id="ua-pass-confirm" type="password" placeholder="repeat">
      </div>
      <div class="ua-err" id="ua-pass-err"></div>
    </div>
    <div class="ua-mf">
      <button class="ua-btn light" id="ua-pass-cancel" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Cancel
      </button>
      <button class="ua-btn" id="ua-pass-save" type="button">
        <svg class="ua-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V8a5 5 0 0110 0v2"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h12v11H6z"/>
        </svg>
        Update
      </button>
    </div>
  </div>
</div>

@once
@push('scripts')
<script>
(() => {
  "use strict";

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const ENDPOINTS = {
    fetch: @json(route('admin.users.fetch')),
    store: @json(route('admin.users.store')),
    update: (id) => @json(url('/admin/users')) + '/' + id,
    destroy:(id) => @json(url('/admin/users')) + '/' + id,
    toggleActive:(id) => @json(url('/admin/users')) + '/' + id + '/toggle-active',
    toggleAdmin:(id) => @json(url('/admin/users')) + '/' + id + '/toggle-admin',
    password:(id) => @json(url('/admin/users')) + '/' + id + '/password',
  };

  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const esc = (s) => String(s ?? "")
    .replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;").replaceAll("'", "&#039;");

  const debounce = (fn, ms=300) => { let t; return (...a) => { clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };

  function parseLaravel422(rawText) {
    try {
      const j = JSON.parse(rawText || "{}");
      const msg = j.message || "Validation failed.";
      if (j.errors && typeof j.errors === "object") {
        const lines = Object.values(j.errors).flat();
        return { message: msg, details: lines.join("\n") };
      }
      return { message: msg, details: "" };
    } catch (_) {
      return { message: "Error", details: String(rawText || "").slice(0, 700) };
    }
  }

  async function api(url, method="GET", data=null) {
    const headers = { "Accept":"application/json", "X-Requested-With":"XMLHttpRequest" };
    const opts = { method, headers };

    if (method !== "GET") {
      headers["Content-Type"] = "application/json";
      if (CSRF) headers["X-CSRF-TOKEN"] = CSRF;
      opts.body = JSON.stringify(data || {});
    }

    const res = await fetch(url, opts);
    const txt = await res.text().catch(()=> "");
    if (!res.ok) {
      const err = new Error(`HTTP ${res.status}`);
      err.status = res.status;
      err.raw = txt;
      throw err;
    }
    try { return JSON.parse(txt || "{}"); } catch (_) { return {}; }
  }

  // Inline SVG icons (keeps markup clean + consistent)
  const ICON = {
    edit: `<svg class="ua-ic-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
    </svg>`,
    power: `<svg class="ua-ic-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v10"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 4.8a8 8 0 108.9 0"/>
    </svg>`,
    swap: `<svg class="ua-ic-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M16 3h5v5"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 8l-6-6"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 21H3v-5"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16l6 6"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0113.66-5.66"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 01-13.66 5.66"/>
    </svg>`,
    key: `<svg class="ua-ic-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 2l-2 2m-2 2l-2 2"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M7 14a4 4 0 105.657-5.657A4 4 0 007 14z"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M11 12l4 4h3v3h3v3"/>
    </svg>`,
    trash: `<svg class="ua-ic-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 16h10l1-16"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v7M14 11v7"/>
    </svg>`
  };

  // DOM
  const DOM = {
    stats: $("#ua-stats"),
    list: $("#ua-list"),
    refresh: $("#ua-refresh"),
    search: $("#ua-search"),
    status: $("#ua-status"),
    admin: $("#ua-admin"),
    sort: $("#ua-sort"),
    perpage: $("#ua-perpage"),
    prev: $("#ua-prev"),
    next: $("#ua-next"),
    page: $("#ua-page"),

    openAdd: $("#ua-open-add"),

    // add/edit
    modalBD: $("#ua-modal"),
    modalX: $("#ua-modal-x"),
    modalCancel: $("#ua-modal-cancel"),
    modalSave: $("#ua-modal-save"),
    modalTitle: $("#ua-modal-title"),
    modalSub: $("#ua-modal-sub"),
    modalErr: $("#ua-modal-err"),
    fEmployee: $("#ua-f-employee"),
    fEmail: $("#ua-f-email"),
    fPassWrap: $("#ua-pass-wrap"),
    fPass: $("#ua-f-pass"),
    fRole: $("#ua-f-role"),
    fActive: $("#ua-f-active"),

    // delete
    delBD: $("#ua-del"),
    delX: $("#ua-del-x"),
    delCancel: $("#ua-del-cancel"),
    delConfirm: $("#ua-del-confirm"),
    delSub: $("#ua-del-sub"),
    delErr: $("#ua-del-err"),

    // password
    passBD: $("#ua-pass"),
    passX: $("#ua-pass-x"),
    passCancel: $("#ua-pass-cancel"),
    passSave: $("#ua-pass-save"),
    passSub: $("#ua-pass-sub"),
    passErr: $("#ua-pass-err"),
    passNew: $("#ua-pass-new"),
    passConfirm: $("#ua-pass-confirm"),
  };

  // STATE
  const state = {
    page: 1,
    per_page: 12,
    q: "",
    status: "all",
    admin: "1",
    sort: "newest",
    items: [],
    activeId: null,
    mode: "create", // create|edit
    delId: null,
    passId: null,
  };

  function roleLabel(v){
    v = parseInt(v || 0, 10);
    if (v === 1) return "Admin";
    if (v === 2) return "Limited";
    return "User";
  }

  function renderStats(s){
    const conf = [
      { k:"total",   l:"Total Admins", c:"var(--blue)" },
      { k:"active",  l:"Active",       c:"var(--green)" },
      { k:"inactive",l:"Inactive",     c:"var(--orange)" },
      { k:"new_7d",  l:"New (7 days)", c:"var(--purple)" },
    ];

    DOM.stats.innerHTML = conf.map(x => `
      <div class="ua-stat">
        <div class="ua-stat-l"><span class="ua-dot" style="background:${x.c}"></span>${x.l}</div>
        <div class="ua-stat-v">${Number(s?.[x.k] ?? 0)}</div>
      </div>
    `).join("");
  }

  function setPager(p){
    const page = Number(p?.page ?? state.page);
    const per  = Number(p?.per_page ?? state.per_page);
    const total= Number(p?.total ?? 0);
    const pages = Math.max(1, Math.ceil(total / Math.max(1, per)));

    DOM.page.textContent = `Page ${page} of ${pages}`;
    DOM.prev.disabled = page <= 1;
    DOM.next.disabled = !p?.has_more;
  }

  function renderList(items){
    if (!items?.length){
      DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">No users found.</div>`;
      return;
    }

    DOM.list.innerHTML = items.map(i => {
      const id = Number(i.id || 0);
      const img = i.emp_image
        ? `{{ asset('images/employee') }}/${esc(i.emp_image)}`
        : `{{ asset('images/employee/users.png') }}`;

      const full = `${esc(i.emp_name || "—")} ${esc(i.emp_lastname || "")}`.trim();
      const email = esc(i.email || "—");
      const role = roleLabel(i.is_admin);
      const active = parseInt(i.is_active || 0, 10) === 1;

      const tagRole = `<span class="ua-tag info">${esc(role)}</span>`;
      const tagActive = active
        ? `<span class="ua-tag ok">Active</span>`
        : `<span class="ua-tag bad">Inactive</span>`;

      return `
        <div class="ua-item" data-id="${id}">
          <div class="ua-avatar"><img src="${img}" alt=""></div>

          <div class="ua-main">
            <div class="ua-name">${full}</div>
            <div class="ua-subt">${email}</div>
            <div class="ua-subt">Employee ID (stored in users.name): <b>${esc(i.employee_id)}</b></div>
          </div>

          <div class="ua-meta">
            ${tagRole}
            ${tagActive}
          </div>

          <div class="ua-created">
            Created: <b>${esc(i.created_at || "—")}</b>
          </div>

          <div class="ua-actions">
            <button class="ua-mini primary" title="Edit" aria-label="Edit" data-action="edit">${ICON.edit}</button>
            <button class="ua-mini warn" title="Toggle Active" aria-label="Toggle Active" data-action="toggleActive">${ICON.power}</button>
            <button class="ua-mini purple" title="Toggle Role Admin/Limited" aria-label="Toggle Role" data-action="toggleAdmin">${ICON.swap}</button>
            <button class="ua-mini" title="Password" aria-label="Change Password" data-action="password">${ICON.key}</button>
            <button class="ua-mini danger" title="Delete" aria-label="Delete" data-action="delete">${ICON.trash}</button>
          </div>
        </div>
      `;
    }).join("");
  }

  async function load(){
    DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">Loading...</div>`;
    try{
      const qs = new URLSearchParams({
        q: state.q,
        status: state.status,
        admin: state.admin,
        sort: state.sort,
        page: state.page,
        per_page: state.per_page,
      }).toString();

      const data = await api(ENDPOINTS.fetch + (ENDPOINTS.fetch.includes('?') ? '&' : '?') + qs, "GET");
      state.items = Array.isArray(data?.items) ? data.items : [];
      renderStats(data?.stats || {});
      renderList(state.items);
      setPager(data?.pagination || {});
    } catch(e){
      DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--red)">Failed to load.</div>`;
    }
  }

  // MODALS
  function openBD(el){ el.classList.add('open'); el.setAttribute('aria-hidden','false'); }
  function closeBD(el){ el.classList.remove('open'); el.setAttribute('aria-hidden','true'); }

  function showErr(el, msg){
    el.textContent = msg || '';
    el.classList.toggle('show', !!msg);
  }

  function openCreate(){
    state.mode = "create";
    state.activeId = null;

    DOM.modalTitle.textContent = "Add Admin";
    DOM.modalSub.textContent = "Create a new user";
    DOM.fPassWrap.style.display = "";
    DOM.fPass.value = "";
    DOM.fEmail.value = "";
    DOM.fActive.checked = true;
    DOM.fRole.value = "1";
    showErr(DOM.modalErr, "");
    openBD(DOM.modalBD);
  }

  function openEdit(id){
    const row = state.items.find(x => Number(x.id) === Number(id));
    if (!row) return;

    state.mode = "edit";
    state.activeId = id;

    DOM.modalTitle.textContent = "Edit User";
    DOM.modalSub.textContent = `${(row.emp_name||'—')} ${(row.emp_lastname||'')}`.trim();

    DOM.fPassWrap.style.display = "none";
    DOM.fEmail.value = row.email || "";
    DOM.fEmployee.value = String(row.employee_id || "");
    DOM.fRole.value = String(row.is_admin || "1");
    DOM.fActive.checked = parseInt(row.is_active || 0, 10) === 1;

    showErr(DOM.modalErr, "");
    openBD(DOM.modalBD);
  }

  function openDelete(id){
    const row = state.items.find(x => Number(x.id) === Number(id));
    state.delId = id;
    DOM.delSub.textContent = row ? `${(row.emp_name||'—')} ${(row.emp_lastname||'')}`.trim() : `User #${id}`;
    showErr(DOM.delErr, "");
    openBD(DOM.delBD);
  }

  function openPassword(id){
    const row = state.items.find(x => Number(x.id) === Number(id));
    state.passId = id;
    DOM.passSub.textContent = row ? `${(row.emp_name||'—')} ${(row.emp_lastname||'')}`.trim() : `User #${id}`;
    DOM.passNew.value = "";
    DOM.passConfirm.value = "";
    showErr(DOM.passErr, "");
    openBD(DOM.passBD);
  }

  // EVENTS
  DOM.refresh.addEventListener('click', () => load());
  DOM.openAdd.addEventListener('click', () => openCreate());

  DOM.search.addEventListener('input', debounce((e) => {
    state.q = e.target.value || "";
    state.page = 1;
    load();
  }, 300));

  DOM.status.addEventListener('change', (e) => {
    state.status = e.target.value || "all";
    state.page = 1;
    load();
  });

  DOM.admin.addEventListener('change', (e) => {
    state.admin = e.target.value || "1";
    state.page = 1;
    load();
  });

  DOM.sort.addEventListener('change', (e) => {
    state.sort = e.target.value || "newest";
    state.page = 1;
    load();
  });

  DOM.perpage.addEventListener('change', (e) => {
    state.per_page = parseInt(e.target.value, 10) || 12;
    state.page = 1;
    load();
  });

  DOM.prev.addEventListener('click', () => {
    state.page = Math.max(1, state.page - 1);
    load();
  });

  DOM.next.addEventListener('click', () => {
    state.page += 1;
    load();
  });

  DOM.list.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const item = e.target.closest('.ua-item');
    const id = Number(item?.dataset?.id || 0);
    const action = btn.dataset.action;
    if (!id) return;

    try{
      if (action === "edit") return openEdit(id);
      if (action === "delete") return openDelete(id);
      if (action === "password") return openPassword(id);

      if (action === "toggleActive"){
        await api(ENDPOINTS.toggleActive(id), "POST", {});
        await load();
        return;
      }

      if (action === "toggleAdmin"){
        await api(ENDPOINTS.toggleAdmin(id), "POST", {});
        await load();
        return;
      }
    }catch(err){
      if (err?.status === 422){
        const info = parseLaravel422(err.raw);
        alert(info.details || info.message);
      } else {
        alert("Action failed.");
      }
    }
  });

  // modal close handlers
  [DOM.modalX, DOM.modalCancel].forEach(x => x.addEventListener('click', () => closeBD(DOM.modalBD)));
  DOM.modalBD.addEventListener('click', (e) => { if (e.target === DOM.modalBD) closeBD(DOM.modalBD); });

  [DOM.delX, DOM.delCancel].forEach(x => x.addEventListener('click', () => closeBD(DOM.delBD)));
  DOM.delBD.addEventListener('click', (e) => { if (e.target === DOM.delBD) closeBD(DOM.delBD); });

  [DOM.passX, DOM.passCancel].forEach(x => x.addEventListener('click', () => closeBD(DOM.passBD)));
  DOM.passBD.addEventListener('click', (e) => { if (e.target === DOM.passBD) closeBD(DOM.passBD); });

  // save create/edit
  DOM.modalSave.addEventListener('click', async () => {
    showErr(DOM.modalErr, "");

    const payload = {
      employee_id: parseInt(DOM.fEmployee.value, 10),
      email: (DOM.fEmail.value || "").trim(),
      is_admin: String(DOM.fRole.value || "1"),
      is_active: DOM.fActive.checked ? 1 : 0,
    };

    if (!payload.employee_id) return showErr(DOM.modalErr, "Employee is required.");
    if (!payload.email) return showErr(DOM.modalErr, "Email is required.");

    try{
      if (state.mode === "create"){
        const pass = (DOM.fPass.value || "").trim();
        if (pass.length < 8) return showErr(DOM.modalErr, "Password must be at least 8 characters.");
        payload.password = pass;

        await api(ENDPOINTS.store, "POST", payload);
      } else {
        await api(ENDPOINTS.update(state.activeId), "PUT", payload);
      }

      closeBD(DOM.modalBD);
      await load();
    } catch(err){
      if (err?.status === 422){
        const info = parseLaravel422(err.raw);
        showErr(DOM.modalErr, info.details || info.message);
      } else {
        showErr(DOM.modalErr, "Save failed.");
      }
    }
  });

  // confirm delete
  DOM.delConfirm.addEventListener('click', async () => {
    showErr(DOM.delErr, "");
    try{
      await api(ENDPOINTS.destroy(state.delId), "DELETE", {});
      closeBD(DOM.delBD);
      await load();
    } catch(err){
      if (err?.status === 422){
        const info = parseLaravel422(err.raw);
        showErr(DOM.delErr, info.details || info.message);
      } else {
        showErr(DOM.delErr, "Delete failed.");
      }
    }
  });

  // save password
  DOM.passSave.addEventListener('click', async () => {
    showErr(DOM.passErr, "");
    const p1 = (DOM.passNew.value || "").trim();
    const p2 = (DOM.passConfirm.value || "").trim();

    if (p1.length < 8) return showErr(DOM.passErr, "Password must be at least 8 characters.");
    if (p1 !== p2) return showErr(DOM.passErr, "Passwords do not match.");

    try{
      await api(ENDPOINTS.password(state.passId), "POST", {
        password: p1,
        password_confirmation: p2
      });

      closeBD(DOM.passBD);
      await load();
    } catch(err){
      if (err?.status === 422){
        const info = parseLaravel422(err.raw);
        showErr(DOM.passErr, info.details || info.message);
      } else {
        showErr(DOM.passErr, "Update failed.");
      }
    }
  });

  // INIT
  load();
})();
</script>
@endpush
@endonce
@endsection