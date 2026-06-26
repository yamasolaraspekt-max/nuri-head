{{-- resources/views/admin/user/admin_user.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Admin Benutzer')

@section('style')
  <style>
    :root {
      --ua-bg: #f3f4f6;
      --ua-card: #ffffff;
      --ua-text: #1f2937;
      --ua-muted: #6b7280;
      --ua-border: #e5e7eb;

      --ua-green: #93c21c;
      --ua-green-soft: #cfe09b;
      --ua-blue: #74b2d4;
      --ua-blue-soft: #c0d8ea;
      --ua-purple: #c08eea;
      --ua-purple-soft: rgba(192, 142, 234, .14);
      --ua-orange: #f59e0b;
      --ua-orange-soft: #fffbeb;
      --ua-red: #ef4444;
      --ua-red-soft: #fef2f2;

      --ua-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
      --ua-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
      --ua-radius: 16px;
      --ua-transition: all .2s ease-in-out;
    }

    body {
      background: var(--ua-bg) !important;
    }

    .ua-wrap {
      font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--ua-text);
      margin: 20px auto;
      padding-right: 79px;
    }

    .ua-titlebar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
      padding: 16px;
      border: 1px solid rgba(116, 178, 212, .35);
      border-radius: var(--ua-radius);
      background:
        linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(147, 194, 28, .12)),
        #fff;
      box-shadow: var(--ua-shadow-sm);
      flex-wrap: wrap;
    }

    .ua-title {
      font-size: 26px;
      font-weight: 900;
      letter-spacing: -.025em;
      color: #0f172a;
      text-transform: uppercase;
    }

    .ua-sub {
      font-size: 14px;
      color: var(--ua-muted);
      margin-top: 4px;
    }

    .ua-inline {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .ua-ic {
      width: 18px;
      height: 18px;
      display: block;
    }

    .ua-ic-sm {
      width: 16px;
      height: 16px;
      display: block;
    }

    .ua-btn,
    .ua-btn-light,
    .ua-btn-danger {
      border: 0;
      padding: 10px 14px;
      border-radius: 12px;
      font-weight: 900;
      cursor: pointer;
      transition: var(--ua-transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      line-height: 1.2;
      white-space: nowrap;
    }

    .ua-btn {
      background: var(--ua-green);
      color: #fff;
      box-shadow: 0 10px 22px -14px rgba(147, 194, 28, .75);
    }

    .ua-btn:hover {
      filter: brightness(.96);
      color: #fff;
      text-decoration: none;
    }

    .ua-btn-light {
      background: rgba(116, 178, 212, .14);
      color: #0b5b7a;
      border: 1px solid rgba(116, 178, 212, .35);
    }

    .ua-btn-light:hover {
      background: rgba(116, 178, 212, .20);
      color: #0b5b7a;
    }

    .ua-btn-danger {
      background: var(--ua-red);
      color: #fff;
    }

    .ua-btn-danger:hover {
      background: #dc2626;
      color: #fff;
    }

    .ua-btn-ic {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      border: 1px solid var(--ua-border);
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      cursor: pointer;
      transition: var(--ua-transition);
    }

    .ua-btn-ic:hover {
      background: #f8fafc;
      color: #0f172a;
      border-color: #d1d5db;
    }

    .ua-btn-ic.brand {
      border-color: rgba(116, 178, 212, .35);
      background: rgba(116, 178, 212, .12);
      color: #0b5b7a;
    }

    .ua-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 14px;
      margin-bottom: 16px;
    }

    .ua-stat {
      background: var(--ua-card);
      border: 1px solid var(--ua-border);
      border-radius: var(--ua-radius);
      padding: 14px;
      box-shadow: var(--ua-shadow-sm);
      transition: var(--ua-transition);
      position: relative;
      overflow: hidden;
    }

    .ua-stat:before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--ua-blue), var(--ua-green), var(--ua-orange));
    }

    .ua-stat:hover {
      transform: translateY(-2px);
      box-shadow: var(--ua-shadow);
    }

    .ua-stat-l {
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      color: var(--ua-muted);
      letter-spacing: .06em;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ua-dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      display: inline-block;
    }

    .ua-stat-v {
      font-size: 28px;
      font-weight: 900;
      line-height: 1;
      color: #0f172a;
    }

    .ua-toolbar {
      background: var(--ua-card);
      border: 1px solid rgba(116, 178, 212, .28);
      border-radius: var(--ua-radius);
      padding: 12px 14px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      box-shadow: var(--ua-shadow-sm);
    }

    .ua-fg {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .ua-sep {
      width: 1px;
      height: 26px;
      background: var(--ua-border);
      margin: 0 4px;
    }

    .ua-input {
      background-color: #f9fafb;
      border: 1px solid var(--ua-border);
      border-radius: 12px;
      padding: 10px 12px 10px 40px;
      font-size: 14px;
      outline: none;
      transition: var(--ua-transition);
      min-width: 280px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: 12px center;
      background-size: 16px;
    }

    .ua-input:focus {
      background-color: #fff;
      border-color: rgba(116, 178, 212, .9);
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .22);
    }

    .ua-select {
      padding: 10px 36px 10px 12px;
      border-radius: 12px;
      border: 1px solid var(--ua-border);
      background-color: #fff;
      font-size: 13px;
      cursor: pointer;
      outline: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 14px;
    }

    .ua-select:focus {
      border-color: rgba(147, 194, 28, .9);
      box-shadow: 0 0 0 3px rgba(147, 194, 28, .22);
    }

    .ua-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .ua-item {
      background: var(--ua-card);
      border: 1px solid var(--ua-border);
      border-radius: var(--ua-radius);
      padding: 14px;
      display: grid;
      gap: 14px;
      align-items: center;
      grid-template-columns: 56px 1fr 260px 220px 220px;
      transition: var(--ua-transition);
      position: relative;
    }

    .ua-item:hover {
      border-color: rgba(147, 194, 28, .70);
      box-shadow: var(--ua-shadow);
    }

    .ua-item:after {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: linear-gradient(180deg, var(--ua-blue), var(--ua-green));
      border-top-left-radius: var(--ua-radius);
      border-bottom-left-radius: var(--ua-radius);
      opacity: .60;
    }

    .ua-avatar {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(116, 178, 212, .20), rgba(192, 142, 234, .10));
      overflow: hidden;
      border: 1px solid rgba(116, 178, 212, .22);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .ua-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .ua-main {
      min-width: 0;
    }

    .ua-name {
      font-weight: 900;
      font-size: 15px;
      margin: 0 0 4px 0;
      color: #0f172a;
    }

    .ua-subt {
      font-size: 13px;
      color: var(--ua-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .ua-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .ua-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 900;
      border: 1px solid var(--ua-border);
      background: #fff;
      color: #111827;
    }

    .ua-tag.ok {
      background: rgba(147, 194, 28, .14);
      color: #4d6a08;
      border-color: rgba(147, 194, 28, .28);
    }

    .ua-tag.bad {
      background: rgba(245, 158, 11, .14);
      color: #9a5b00;
      border-color: rgba(245, 158, 11, .25);
    }

    .ua-tag.info {
      background: rgba(116, 178, 212, .16);
      color: #0b5b7a;
      border-color: rgba(116, 178, 212, .30);
    }

    .ua-tag.purple {
      background: var(--ua-purple-soft);
      color: #6a2c8f;
      border-color: rgba(192, 142, 234, .28);
    }

    .ua-created {
      font-size: 12px;
      color: var(--ua-muted);
    }

    .ua-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .ua-mini {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      border: 1px solid var(--ua-border);
      background: #fff;
      cursor: pointer;
      transition: var(--ua-transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #111827;
    }

    .ua-mini:hover {
      background: #f8fafc;
      border-color: #d1d5db;
    }

    .ua-mini.primary {
      background: rgba(116, 178, 212, .16);
      border-color: rgba(116, 178, 212, .28);
      color: #0b5b7a;
    }

    .ua-mini.warn {
      background: rgba(245, 158, 11, .16);
      border-color: rgba(245, 158, 11, .25);
      color: #9a5b00;
    }

    .ua-mini.purple {
      background: var(--ua-purple-soft);
      border-color: rgba(192, 142, 234, .28);
      color: #6a2c8f;
    }

    .ua-mini.danger {
      background: rgba(239, 68, 68, .12);
      border-color: rgba(239, 68, 68, .22);
      color: #b91c1c;
    }

    .ua-pager {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      margin-top: 14px;
    }

    .ua-pager span {
      font-size: 13px;
      color: var(--ua-muted);
    }

    .ua-empty {
      text-align: center;
      padding: 60px 20px;
      color: var(--ua-muted);
      background: #fff;
      border: 1px dashed var(--ua-border);
      border-radius: var(--ua-radius);
      font-weight: 800;
    }

    /* Modal */
    .ua-bd {
      position: fixed;
      inset: 0;
      background: rgba(17, 24, 39, .55);
      backdrop-filter: blur(2px);
      z-index: 99999;
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px;
    }

    .ua-bd.open {
      opacity: 1;
      pointer-events: auto;
    }

    .ua-modal {
      width: 100%;
      max-width: 560px;
      background: #fff;
      border: 1px solid rgba(116, 178, 212, .25);
      border-radius: 16px;
      box-shadow: var(--ua-shadow);
      overflow: hidden;
      transform: translateY(10px) scale(.99);
      transition: transform .2s ease;
    }

    .ua-bd.open .ua-modal {
      transform: translateY(0) scale(1);
    }

    .ua-mh {
      padding: 14px 16px;
      border-bottom: 1px solid rgba(116, 178, 212, .22);
      background: linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(147, 194, 28, .10));
      display: flex;
      justify-content: space-between;
      gap: 10px;
      align-items: flex-start;
    }

    .ua-mt {
      font-weight: 900;
      margin: 0;
      color: #0f172a;
    }

    .ua-ms {
      font-size: 12px;
      color: var(--ua-muted);
      margin-top: 3px;
    }

    .ua-mx {
      width: 40px;
      height: 40px;
      flex: 0 0 auto;
    }

    .ua-mb {
      padding: 16px;
    }

    .ua-mf {
      padding: 14px 16px;
      border-top: 1px solid var(--ua-border);
      background: #fafafa;
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .ua-field {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 12px;
    }

    .ua-lbl {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .ua-inp,
    .ua-sel {
      width: 100%;
      border: 1px solid var(--ua-border);
      border-radius: 12px;
      padding: 10px 12px;
      font-size: 14px;
      outline: none;
      transition: var(--ua-transition);
      background: #fff;
    }

    .ua-inp:focus,
    .ua-sel:focus {
      border-color: rgba(147, 194, 28, .85);
      box-shadow: 0 0 0 3px rgba(147, 194, 28, .20);
    }

    .ua-err {
      display: none;
      margin-top: 10px;
      padding: 10px;
      border-radius: 12px;
      border: 1px solid rgba(239, 68, 68, .25);
      background: rgba(239, 68, 68, .10);
      color: #b91c1c;
      font-size: 12px;
      white-space: pre-wrap;
      font-weight: 800;
    }

    .ua-err.show {
      display: block;
    }

    .ua-note {
      font-size: 12px;
      color: var(--ua-muted);
    }

    body.ua-modal-open {
      overflow: hidden !important;
    }

    @media(max-width: 1100px) {
      .ua-item {
        grid-template-columns: 56px 1fr;
        grid-template-rows: auto auto auto auto;
        gap: 10px;
      }

      .ua-meta,
      .ua-created,
      .ua-actions {
        grid-column: 2;
      }

      .ua-actions {
        justify-content: flex-start;
      }
    }

    @media(max-width: 768px) {
      .ua-wrap {
        padding: 18px;
        margin: 0;
      }

      .ua-title {
        font-size: 21px;
      }

      .ua-input {
        min-width: 100%;
      }

      .ua-fg,
      .ua-toolbar {
        width: 100%;
      }

      .ua-select {
        width: 100%;
      }
    }
  </style>
@endsection

@section('content')
  <div class="ua-wrap" id="ua-root">
    <div class="ua-titlebar">
      <div>
        <div class="ua-title">Admin Benutzer</div>
        <div class="ua-sub">Benutzer, Rollen, Status und Passwörter verwalten.</div>
      </div>

      <div class="ua-inline">
        <button class="ua-btn-ic brand" id="ua-refresh" type="button" title="Aktualisieren" aria-label="Aktualisieren">
          <i class="feather icon-refresh-cw"></i>
        </button>

        <button class="ua-btn" id="ua-open-add" type="button">
          <i class="feather icon-plus"></i>
          Admin hinzufügen
        </button>
      </div>
    </div>

    <div class="ua-stats" id="ua-stats"></div>

    <div class="ua-toolbar" role="region" aria-label="Filter">
      <div class="ua-fg">
        <input class="ua-input" id="ua-search" placeholder="Name oder E-Mail suchen..." autocomplete="off">

        <div class="ua-sep" aria-hidden="true"></div>

        <select class="ua-select" id="ua-status" aria-label="Status">
          <option value="all">Alle Status</option>
          <option value="active">Nur aktiv</option>
          <option value="inactive">Nur inaktiv</option>
        </select>

        <select class="ua-select" id="ua-admin" aria-label="Rolle">
          <option value="1">Admins</option>
          <option value="2">Eingeschränkt</option>
          <option value="all">Alle Rollen</option>
        </select>
      </div>

      <div class="ua-fg">
        <select class="ua-select" id="ua-sort" aria-label="Sortierung">
          <option value="newest">Neueste zuerst</option>
          <option value="oldest">Älteste zuerst</option>
          <option value="name_asc">Name A–Z</option>
          <option value="name_desc">Name Z–A</option>
          <option value="email_asc">E-Mail A–Z</option>
          <option value="email_desc">E-Mail Z–A</option>
        </select>

        <select class="ua-select" id="ua-perpage" aria-label="Pro Seite">
          <option value="12">12 / Seite</option>
          <option value="24">24 / Seite</option>
          <option value="48">48 / Seite</option>
        </select>
      </div>
    </div>

    <div id="ua-list" class="ua-list" aria-live="polite">
      <div class="ua-empty">Wird geladen...</div>
    </div>

    <div class="ua-pager" role="navigation" aria-label="Pagination">
      <button class="ua-btn-ic" id="ua-prev" type="button" title="Zurück" aria-label="Zurück" disabled>
        <i class="feather icon-chevron-left"></i>
      </button>

      <span id="ua-page">Seite 1</span>

      <button class="ua-btn-ic" id="ua-next" type="button" title="Weiter" aria-label="Weiter" disabled>
        <i class="feather icon-chevron-right"></i>
      </button>
    </div>
  </div>

  {{-- ADD / EDIT MODAL --}}
  <div class="ua-bd" id="ua-modal" aria-hidden="true">
    <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-modal-title">
      <div class="ua-mh">
        <div>
          <h3 class="ua-mt" id="ua-modal-title">Admin hinzufügen</h3>
          <div class="ua-ms" id="ua-modal-sub">—</div>
        </div>

        <button class="ua-btn-ic ua-mx" id="ua-modal-x" type="button" aria-label="Schließen">
          <i class="feather icon-x"></i>
        </button>
      </div>

      <div class="ua-mb">
        <div class="ua-field">
          <label class="ua-lbl" for="ua-f-employee">Mitarbeiter</label>
          <select class="ua-sel" id="ua-f-employee">
            <option value="">Bitte Mitarbeiter wählen</option>
            @foreach($employee as $e)
              <option value="{{ $e->id }}">
                {{ $e->name }} {{ $e->lastname }}
              </option>
            @endforeach
          </select>
          <div class="ua-note">Wird in <b>users.name</b> als Mitarbeiter-ID gespeichert.</div>
        </div>

        <div class="ua-field">
          <label class="ua-lbl" for="ua-f-email">E-Mail</label>
          <input class="ua-inp" id="ua-f-email" type="email" placeholder="email@example.com">
        </div>

        <div class="ua-field" id="ua-pass-wrap">
          <label class="ua-lbl" for="ua-f-pass">Passwort</label>
          <input class="ua-inp" id="ua-f-pass" type="password" placeholder="mind. 8 Zeichen">
        </div>

        <div class="ua-field">
          <label class="ua-lbl" for="ua-f-role">Rolle</label>
          <select class="ua-sel" id="ua-f-role">
            <option value="1">Admin</option>
            <option value="2">Eingeschränkt</option>
          </select>
        </div>

        <div class="ua-field">
          <label style="display:flex;gap:10px;align-items:center;font-weight:800">
            <input type="checkbox" id="ua-f-active" checked>
            Aktiv
          </label>
        </div>

        <div class="ua-err" id="ua-modal-err"></div>
      </div>

      <div class="ua-mf">
        <button class="ua-btn-light" id="ua-modal-cancel" type="button">
          <i class="feather icon-x"></i>
          Abbrechen
        </button>

        <button class="ua-btn" id="ua-modal-save" type="button">
          <i class="feather icon-save"></i>
          Speichern
        </button>
      </div>
    </div>
  </div>

  {{-- DELETE MODAL --}}
  <div class="ua-bd" id="ua-del" aria-hidden="true">
    <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-del-title">
      <div class="ua-mh">
        <div>
          <h3 class="ua-mt" id="ua-del-title">Benutzer löschen?</h3>
          <div class="ua-ms" id="ua-del-sub">—</div>
        </div>

        <button class="ua-btn-ic ua-mx" id="ua-del-x" type="button" aria-label="Schließen">
          <i class="feather icon-x"></i>
        </button>
      </div>

      <div class="ua-mb">
        <div style="font-size:13px;color:#374151;line-height:1.5">
          Dieser Benutzer wird dauerhaft gelöscht.
        </div>

        <div class="ua-err" id="ua-del-err"></div>
      </div>

      <div class="ua-mf">
        <button class="ua-btn-light" id="ua-del-cancel" type="button">
          <i class="feather icon-x"></i>
          Abbrechen
        </button>

        <button class="ua-btn-danger" id="ua-del-confirm" type="button">
          <i class="feather icon-trash-2"></i>
          Löschen
        </button>
      </div>
    </div>
  </div>

  {{-- PASSWORD MODAL --}}
  <div class="ua-bd" id="ua-pass" aria-hidden="true">
    <div class="ua-modal" role="dialog" aria-modal="true" aria-labelledby="ua-pass-title">
      <div class="ua-mh">
        <div>
          <h3 class="ua-mt" id="ua-pass-title">Passwort ändern</h3>
          <div class="ua-ms" id="ua-pass-sub">—</div>
        </div>

        <button class="ua-btn-ic ua-mx" id="ua-pass-x" type="button" aria-label="Schließen">
          <i class="feather icon-x"></i>
        </button>
      </div>

      <div class="ua-mb">
        <div class="ua-field">
          <label class="ua-lbl" for="ua-pass-new">Neues Passwort</label>
          <input class="ua-inp" id="ua-pass-new" type="password" placeholder="mind. 8 Zeichen">
        </div>

        <div class="ua-field">
          <label class="ua-lbl" for="ua-pass-confirm">Passwort bestätigen</label>
          <input class="ua-inp" id="ua-pass-confirm" type="password" placeholder="wiederholen">
        </div>

        <div class="ua-err" id="ua-pass-err"></div>
      </div>

      <div class="ua-mf">
        <button class="ua-btn-light" id="ua-pass-cancel" type="button">
          <i class="feather icon-x"></i>
          Abbrechen
        </button>

        <button class="ua-btn" id="ua-pass-save" type="button">
          <i class="feather icon-lock"></i>
          Aktualisieren
        </button>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    (function () {
      'use strict';

      const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      const ENDPOINTS = {
        fetch: @json(route('admin.users.fetch')),
        store: @json(route('admin.users.store')),
        update: function (id) {
          return @json(url('/admin/users')) + '/' + encodeURIComponent(id);
        },
        destroy: function (id) {
          return @json(url('/admin/users')) + '/' + encodeURIComponent(id);
        },
        toggleActive: function (id) {
          return @json(url('/admin/users')) + '/' + encodeURIComponent(id) + '/toggle-active';
        },
        toggleAdmin: function (id) {
          return @json(url('/admin/users')) + '/' + encodeURIComponent(id) + '/toggle-admin';
        },
        password: function (id) {
          return @json(url('/admin/users')) + '/' + encodeURIComponent(id) + '/password';
        }
      };

      const $ = function (selector, root = document) {
        return root.querySelector(selector);
      };

      const $$ = function (selector, root = document) {
        return Array.from(root.querySelectorAll(selector));
      };

      const DOM = {
        stats: $('#ua-stats'),
        list: $('#ua-list'),
        refresh: $('#ua-refresh'),
        search: $('#ua-search'),
        status: $('#ua-status'),
        admin: $('#ua-admin'),
        sort: $('#ua-sort'),
        perpage: $('#ua-perpage'),
        prev: $('#ua-prev'),
        next: $('#ua-next'),
        page: $('#ua-page'),
        openAdd: $('#ua-open-add'),

        modalBD: $('#ua-modal'),
        modalX: $('#ua-modal-x'),
        modalCancel: $('#ua-modal-cancel'),
        modalSave: $('#ua-modal-save'),
        modalTitle: $('#ua-modal-title'),
        modalSub: $('#ua-modal-sub'),
        modalErr: $('#ua-modal-err'),
        fEmployee: $('#ua-f-employee'),
        fEmail: $('#ua-f-email'),
        fPassWrap: $('#ua-pass-wrap'),
        fPass: $('#ua-f-pass'),
        fRole: $('#ua-f-role'),
        fActive: $('#ua-f-active'),

        delBD: $('#ua-del'),
        delX: $('#ua-del-x'),
        delCancel: $('#ua-del-cancel'),
        delConfirm: $('#ua-del-confirm'),
        delSub: $('#ua-del-sub'),
        delErr: $('#ua-del-err'),

        passBD: $('#ua-pass'),
        passX: $('#ua-pass-x'),
        passCancel: $('#ua-pass-cancel'),
        passSave: $('#ua-pass-save'),
        passSub: $('#ua-pass-sub'),
        passErr: $('#ua-pass-err'),
        passNew: $('#ua-pass-new'),
        passConfirm: $('#ua-pass-confirm')
      };

      const state = {
        page: 1,
        per_page: 12,
        q: '',
        status: 'all',
        admin: '1',
        sort: 'newest',
        items: [],
        activeId: null,
        mode: 'create',
        delId: null,
        passId: null,
        isLoading: false
      };

      function refreshIcons() {
        if (window.feather) {
          window.feather.replace();
        }
      }

      function esc(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function debounce(fn, ms = 300) {
        let timer = null;

        return function (...args) {
          clearTimeout(timer);
          timer = setTimeout(function () {
            fn.apply(null, args);
          }, ms);
        };
      }

      function roleLabel(value) {
        value = parseInt(value || 0, 10);

        if (value === 1) return 'Admin';
        if (value === 2) return 'Eingeschränkt';

        return 'Benutzer';
      }

      function parseLaravelError(rawText) {
        try {
          const json = JSON.parse(rawText || '{}');
          const message = json.message || 'Validierung fehlgeschlagen.';

          if (json.errors && typeof json.errors === 'object') {
            const lines = Object.values(json.errors).flat();
            return lines.join('\n') || message;
          }

          return message;
        } catch (error) {
          return rawText ? String(rawText).slice(0, 700) : 'Ein Fehler ist aufgetreten.';
        }
      }

      async function api(url, method = 'GET', data = null) {
        const headers = {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        };

        const options = {
          method: method,
          headers: headers
        };

        if (method !== 'GET') {
          headers['Content-Type'] = 'application/json';

          if (CSRF) {
            headers['X-CSRF-TOKEN'] = CSRF;
          }

          options.body = JSON.stringify(data || {});
        }

        const response = await fetch(url, options);
        const text = await response.text().catch(function () {
          return '';
        });

        if (!response.ok) {
          const error = new Error('HTTP ' + response.status);
          error.status = response.status;
          error.raw = text;
          throw error;
        }

        try {
          return JSON.parse(text || '{}');
        } catch (error) {
          return {};
        }
      }

      function setButtonLoading(button, isLoading, loadingText, normalText) {
        if (!button) return;

        button.disabled = isLoading;
        button.innerHTML = isLoading
          ? '<i class="feather icon-loader"></i> ' + loadingText
          : normalText;

        refreshIcons();
      }

      function showError(element, message) {
        if (!element) return;

        element.textContent = message || '';
        element.classList.toggle('show', !!message);
      }

      function openModal(element) {
        if (!element) return;

        element.classList.add('open');
        element.setAttribute('aria-hidden', 'false');
        document.body.classList.add('ua-modal-open');

        setTimeout(function () {
          const firstInput = element.querySelector('input, select, button');
          if (firstInput) firstInput.focus();
        }, 80);

        refreshIcons();
      }

      function closeModal(element) {
        if (!element) return;

        element.classList.remove('open');
        element.setAttribute('aria-hidden', 'true');

        const stillOpen = document.querySelector('.ua-bd.open');

        if (!stillOpen) {
          document.body.classList.remove('ua-modal-open');
          document.body.style.removeProperty('overflow');
          document.body.style.removeProperty('padding-right');
        }
      }

      function closeAllModals() {
        $$('.ua-bd.open').forEach(function (modal) {
          closeModal(modal);
        });
      }

      function renderStats(stats) {
        const rows = [
          { key: 'total', label: 'Gesamt', color: 'var(--ua-blue)' },
          { key: 'active', label: 'Aktiv', color: 'var(--ua-green)' },
          { key: 'inactive', label: 'Inaktiv', color: 'var(--ua-orange)' },
          { key: 'new_7d', label: 'Neu / 7 Tage', color: 'var(--ua-purple)' }
        ];

        DOM.stats.innerHTML = rows.map(function (row) {
          return `
                  <div class="ua-stat">
                      <div class="ua-stat-l">
                          <span class="ua-dot" style="background:${row.color}"></span>
                          ${row.label}
                      </div>
                      <div class="ua-stat-v">${Number(stats?.[row.key] || 0)}</div>
                  </div>
              `;
        }).join('');
      }

      function setPager(pagination) {
        const page = Number(pagination?.page || state.page);
        const perPage = Number(pagination?.per_page || state.per_page);
        const total = Number(pagination?.total || 0);
        const pages = Math.max(1, Math.ceil(total / Math.max(1, perPage)));

        DOM.page.textContent = 'Seite ' + page + ' von ' + pages;
        DOM.prev.disabled = page <= 1;
        DOM.next.disabled = !pagination?.has_more;
      }

      function employeeFullName(item) {
        return String((item.emp_name || '—') + ' ' + (item.emp_lastname || '')).trim();
      }

      function renderList(items) {
        if (!items || !items.length) {
          DOM.list.innerHTML = '<div class="ua-empty">Keine Benutzer gefunden.</div>';
          refreshIcons();
          return;
        }

        DOM.list.innerHTML = items.map(function (item) {
          const id = Number(item.id || 0);
          const image = item.emp_image
            ? @json(asset('images/employee')) + '/' + esc(item.emp_image)
            : @json(asset('images/employee/users.png'));

          const fullName = esc(employeeFullName(item));
          const email = esc(item.email || '—');
          const role = roleLabel(item.is_admin);
          const isActive = parseInt(item.is_active || 0, 10) === 1;

          const roleTag = '<span class="ua-tag info">' + esc(role) + '</span>';
          const activeTag = isActive
            ? '<span class="ua-tag ok">Aktiv</span>'
            : '<span class="ua-tag bad">Inaktiv</span>';

          return `
                  <div class="ua-item" data-id="${id}">
                      <div class="ua-avatar">
                          <img src="${image}" alt="">
                      </div>

                      <div class="ua-main">
                          <div class="ua-name">${fullName}</div>
                          <div class="ua-subt">${email}</div>
                          <div class="ua-subt">Mitarbeiter-ID in users.name: <b>${esc(item.employee_id || '')}</b></div>
                      </div>

                      <div class="ua-meta">
                          ${roleTag}
                          ${activeTag}
                      </div>

                      <div class="ua-created">
                          Erstellt: <b>${esc(item.created_at || '—')}</b>
                      </div>

                      <div class="ua-actions">
                          <button class="ua-mini primary" type="button" title="Bearbeiten" data-action="edit">
                              <i class="feather icon-edit"></i>
                          </button>

                          <button class="ua-mini warn" type="button" title="Aktiv/Inaktiv wechseln" data-action="toggleActive">
                              <i class="feather icon-power"></i>
                          </button>

                          <button class="ua-mini purple" type="button" title="Rolle wechseln" data-action="toggleAdmin">
                              <i class="feather icon-repeat"></i>
                          </button>

                          <button class="ua-mini" type="button" title="Passwort ändern" data-action="password">
                              <i class="feather icon-key"></i>
                          </button>

                          <button class="ua-mini danger" type="button" title="Löschen" data-action="delete">
                              <i class="feather icon-trash-2"></i>
                          </button>
                      </div>
                  </div>
              `;
        }).join('');

        refreshIcons();
      }

      async function loadUsers() {
        if (state.isLoading) return;

        state.isLoading = true;
        DOM.list.innerHTML = '<div class="ua-empty">Wird geladen...</div>';

        try {
          const query = new URLSearchParams({
            q: state.q,
            status: state.status,
            admin: state.admin,
            sort: state.sort,
            page: state.page,
            per_page: state.per_page
          }).toString();

          const separator = ENDPOINTS.fetch.includes('?') ? '&' : '?';
          const data = await api(ENDPOINTS.fetch + separator + query, 'GET');

          state.items = Array.isArray(data.items) ? data.items : [];

          renderStats(data.stats || {});
          renderList(state.items);
          setPager(data.pagination || {});
        } catch (error) {
          DOM.list.innerHTML = '<div class="ua-empty" style="color:var(--ua-red)">Benutzer konnten nicht geladen werden.</div>';
        } finally {
          state.isLoading = false;
        }
      }

      function openCreate() {
        state.mode = 'create';
        state.activeId = null;

        DOM.modalTitle.textContent = 'Admin hinzufügen';
        DOM.modalSub.textContent = 'Neuen Benutzer erstellen';
        DOM.fPassWrap.style.display = '';
        DOM.fEmployee.value = '';
        DOM.fEmail.value = '';
        DOM.fPass.value = '';
        DOM.fRole.value = '1';
        DOM.fActive.checked = true;

        showError(DOM.modalErr, '');
        openModal(DOM.modalBD);
      }

      function openEdit(id) {
        const row = state.items.find(function (item) {
          return Number(item.id) === Number(id);
        });

        if (!row) return;

        state.mode = 'edit';
        state.activeId = id;

        DOM.modalTitle.textContent = 'Benutzer bearbeiten';
        DOM.modalSub.textContent = employeeFullName(row);
        DOM.fPassWrap.style.display = 'none';
        DOM.fEmployee.value = String(row.employee_id || '');
        DOM.fEmail.value = row.email || '';
        DOM.fRole.value = String(row.is_admin || '1');
        DOM.fActive.checked = parseInt(row.is_active || 0, 10) === 1;

        showError(DOM.modalErr, '');
        openModal(DOM.modalBD);
      }

      function openDelete(id) {
        const row = state.items.find(function (item) {
          return Number(item.id) === Number(id);
        });

        state.delId = id;
        DOM.delSub.textContent = row ? employeeFullName(row) : 'Benutzer #' + id;

        showError(DOM.delErr, '');
        openModal(DOM.delBD);
      }

      function openPassword(id) {
        const row = state.items.find(function (item) {
          return Number(item.id) === Number(id);
        });

        state.passId = id;
        DOM.passSub.textContent = row ? employeeFullName(row) : 'Benutzer #' + id;
        DOM.passNew.value = '';
        DOM.passConfirm.value = '';

        showError(DOM.passErr, '');
        openModal(DOM.passBD);
      }

      function validateUserPayload(payload, needsPassword) {
        if (!payload.employee_id) return 'Bitte Mitarbeiter auswählen.';
        if (!payload.email) return 'Bitte E-Mail eingeben.';

        if (needsPassword && String(payload.password || '').length < 8) {
          return 'Das Passwort muss mindestens 8 Zeichen haben.';
        }

        return '';
      }

      function bindEvents() {
        DOM.refresh.addEventListener('click', loadUsers);
        DOM.openAdd.addEventListener('click', openCreate);

        DOM.search.addEventListener('input', debounce(function (event) {
          state.q = event.target.value || '';
          state.page = 1;
          loadUsers();
        }, 300));

        DOM.status.addEventListener('change', function (event) {
          state.status = event.target.value || 'all';
          state.page = 1;
          loadUsers();
        });

        DOM.admin.addEventListener('change', function (event) {
          state.admin = event.target.value || '1';
          state.page = 1;
          loadUsers();
        });

        DOM.sort.addEventListener('change', function (event) {
          state.sort = event.target.value || 'newest';
          state.page = 1;
          loadUsers();
        });

        DOM.perpage.addEventListener('change', function (event) {
          state.per_page = parseInt(event.target.value, 10) || 12;
          state.page = 1;
          loadUsers();
        });

        DOM.prev.addEventListener('click', function () {
          state.page = Math.max(1, state.page - 1);
          loadUsers();
        });

        DOM.next.addEventListener('click', function () {
          state.page += 1;
          loadUsers();
        });

        DOM.list.addEventListener('click', async function (event) {
          const button = event.target.closest('button[data-action]');
          if (!button) return;

          const item = event.target.closest('.ua-item');
          const id = Number(item?.dataset?.id || 0);
          const action = button.dataset.action;

          if (!id) return;

          try {
            if (action === 'edit') {
              openEdit(id);
              return;
            }

            if (action === 'delete') {
              openDelete(id);
              return;
            }

            if (action === 'password') {
              openPassword(id);
              return;
            }

            if (action === 'toggleActive') {
              await api(ENDPOINTS.toggleActive(id), 'POST', {});
              await loadUsers();
              return;
            }

            if (action === 'toggleAdmin') {
              await api(ENDPOINTS.toggleAdmin(id), 'POST', {});
              await loadUsers();
            }
          } catch (error) {
            alert(error.status === 422 ? parseLaravelError(error.raw) : 'Aktion fehlgeschlagen.');
          }
        });

        [DOM.modalX, DOM.modalCancel].forEach(function (button) {
          button.addEventListener('click', function () {
            closeModal(DOM.modalBD);
          });
        });

        DOM.modalBD.addEventListener('click', function (event) {
          if (event.target === DOM.modalBD) {
            closeModal(DOM.modalBD);
          }
        });

        [DOM.delX, DOM.delCancel].forEach(function (button) {
          button.addEventListener('click', function () {
            closeModal(DOM.delBD);
          });
        });

        DOM.delBD.addEventListener('click', function (event) {
          if (event.target === DOM.delBD) {
            closeModal(DOM.delBD);
          }
        });

        [DOM.passX, DOM.passCancel].forEach(function (button) {
          button.addEventListener('click', function () {
            closeModal(DOM.passBD);
          });
        });

        DOM.passBD.addEventListener('click', function (event) {
          if (event.target === DOM.passBD) {
            closeModal(DOM.passBD);
          }
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            closeAllModals();
          }
        });

        DOM.modalSave.addEventListener('click', saveUser);
        DOM.delConfirm.addEventListener('click', deleteUser);
        DOM.passSave.addEventListener('click', savePassword);
      }

      async function saveUser() {
        showError(DOM.modalErr, '');

        const payload = {
          employee_id: parseInt(DOM.fEmployee.value, 10) || null,
          email: String(DOM.fEmail.value || '').trim(),
          is_admin: String(DOM.fRole.value || '1'),
          is_active: DOM.fActive.checked ? 1 : 0
        };

        const needsPassword = state.mode === 'create';

        if (needsPassword) {
          payload.password = String(DOM.fPass.value || '').trim();
        }

        const validationError = validateUserPayload(payload, needsPassword);

        if (validationError) {
          showError(DOM.modalErr, validationError);
          return;
        }

        const normalText = '<i class="feather icon-save"></i> Speichern';
        setButtonLoading(DOM.modalSave, true, 'Speichern...', normalText);

        try {
          if (state.mode === 'create') {
            await api(ENDPOINTS.store, 'POST', payload);
          } else {
            await api(ENDPOINTS.update(state.activeId), 'PUT', payload);
          }

          closeModal(DOM.modalBD);
          await loadUsers();
        } catch (error) {
          showError(DOM.modalErr, error.status === 422 ? parseLaravelError(error.raw) : 'Speichern fehlgeschlagen.');
        } finally {
          setButtonLoading(DOM.modalSave, false, 'Speichern...', normalText);
        }
      }

      async function deleteUser() {
        showError(DOM.delErr, '');

        const normalText = '<i class="feather icon-trash-2"></i> Löschen';
        setButtonLoading(DOM.delConfirm, true, 'Löschen...', normalText);

        try {
          await api(ENDPOINTS.destroy(state.delId), 'DELETE', {});
          closeModal(DOM.delBD);
          await loadUsers();
        } catch (error) {
          showError(DOM.delErr, error.status === 422 ? parseLaravelError(error.raw) : 'Löschen fehlgeschlagen.');
        } finally {
          setButtonLoading(DOM.delConfirm, false, 'Löschen...', normalText);
        }
      }

      async function savePassword() {
        showError(DOM.passErr, '');

        const password = String(DOM.passNew.value || '').trim();
        const confirmation = String(DOM.passConfirm.value || '').trim();

        if (password.length < 8) {
          showError(DOM.passErr, 'Das Passwort muss mindestens 8 Zeichen haben.');
          return;
        }

        if (password !== confirmation) {
          showError(DOM.passErr, 'Die Passwörter stimmen nicht überein.');
          return;
        }

        const normalText = '<i class="feather icon-lock"></i> Aktualisieren';
        setButtonLoading(DOM.passSave, true, 'Aktualisieren...', normalText);

        try {
          await api(ENDPOINTS.password(state.passId), 'POST', {
            password: password,
            password_confirmation: confirmation
          });

          closeModal(DOM.passBD);
          await loadUsers();
        } catch (error) {
          showError(DOM.passErr, error.status === 422 ? parseLaravelError(error.raw) : 'Passwort konnte nicht geändert werden.');
        } finally {
          setButtonLoading(DOM.passSave, false, 'Aktualisieren...', normalText);
        }
      }

      document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        refreshIcons();
        loadUsers();
      });
    })();
  </script>
@endsection