@extends('admin.layouts.app')
@section('title', 'Filialadressen und Mitarbeiter')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/users.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
    $branchLogo = $data->image
      ? asset('storage/branches/' . $data->image)
      : (!empty($data->logo_url) ? $data->logo_url : asset('images/icons/placeholder.svg'));

    $statusLabel = ($data->status ?? 'published') === 'published' ? 'Aktiv' : 'Inaktiv';
    $statusClass = ($data->status ?? 'published') === 'published' ? 'green' : 'orange';
  @endphp

  <style>
    :root {
      --app-bg: #f3f4f6;
      --card-bg: #ffffff;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --border: #e5e7eb;
      --primary: #93c21c;
      --primary-hover: #7baa18;
      --primary-light: #f4fae7;
      --blue: #74b2d4;
      --blue-light: #eff6ff;
      --success: #10b981;
      --success-light: #ecfdf5;
      --warning: #f59e0b;
      --warning-light: #fffbeb;
      --danger: #ef4444;
      --danger-light: #fef2f2;
      --dark: #111827;
      --gray-light: #f9fafb;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
      --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
      --radius: 14px;
      --transition: all .2s ease-in-out;
    }

    .oc-wrap {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      color: var(--text-main);
    }

    .oc-header {
      margin-bottom: 18px;
    }

    .oc-titlebar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }

    .oc-title {
      font-size: 26px;
      font-weight: 800;
      letter-spacing: -.025em;
      color: #111827
    }

    .oc-sub {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 4px
    }

    .oc-breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .oc-breadcrumb a {
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 700;
    }

    .oc-breadcrumb a:hover {
      color: var(--text-main);
    }

    .oc-breadcrumb span.current {
      color: #111827;
      font-weight: 800;
    }

    .oc-btn {
      background: var(--primary);
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    .oc-btn:hover {
      background: var(--primary-hover);
      color: #fff;
      text-decoration: none;
    }

    .oc-btn-soft {
      background: #fff;
      color: var(--text-main);
      border: 1px solid var(--border);
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 800;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .oc-btn-soft:hover {
      background: #f9fafb;
      color: var(--text-main);
      text-decoration: none;
    }

    .oc-btn-ic {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .oc-btn-ic:hover {
      background: #f9fafb;
      color: var(--text-main);
      border-color: #d1d5db;
      text-decoration: none;
    }

    .oc-btn-ic.primary {
      color: var(--primary);
      border-color: var(--primary-light);
      background: var(--primary-light)
    }

    .oc-btn-ic.primary:hover {
      border-color: var(--primary)
    }

    .oc-btn-ic.warning {
      color: #d97706;
      border-color: #fde7b0;
      background: #fffbeb
    }

    .oc-btn-ic.warning:hover {
      border-color: #f59e0b
    }

    .oc-btn-ic.success {
      color: var(--success);
      border-color: #c7f2df;
      background: var(--success-light)
    }

    .oc-btn-ic.success:hover {
      border-color: var(--success)
    }

    .oc-btn-ic.danger {
      color: var(--danger);
      border-color: rgba(239, 68, 68, .18);
      background: var(--danger-light)
    }

    .oc-btn-ic.danger:hover {
      border-color: rgba(239, 68, 68, .35)
    }

    .oc-grid-main {
      display: grid;
      grid-template-columns: minmax(0, 1.65fr) minmax(320px, .8fr);
      gap: 18px;
      align-items: start;
    }

    @media(max-width:1200px) {
      .oc-grid-main {
        grid-template-columns: 1fr;
      }
    }

    .oc-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }

    .oc-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 18px 18px 14px 18px;
      border-bottom: 1px solid var(--border);
      background: #fafafa;
      flex-wrap: wrap;
    }

    .oc-card-title {
      font-size: 16px;
      font-weight: 900;
      color: #111827;
      margin: 0;
    }

    .oc-card-body {
      padding: 18px;
    }

    .oc-hero {
      display: grid;
      grid-template-columns: 110px minmax(0, 1fr);
      gap: 18px;
      align-items: start;
    }

    @media(max-width:700px) {
      .oc-hero {
        grid-template-columns: 1fr;
      }
    }

    .oc-logo {
      width: 110px;
      height: 110px;
      border-radius: 20px;
      border: 1px solid var(--border);
      background: #fff;
      object-fit: cover;
      padding: 6px;
      box-shadow: var(--shadow-sm);
    }

    .oc-hero-title {
      font-size: 24px;
      line-height: 1.1;
      font-weight: 900;
      color: #111827;
      margin: 0 0 8px 0;
    }

    .oc-hero-sub {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .oc-status-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 900;
      white-space: nowrap;
    }

    .oc-status-pill.green {
      background: #ecfdf5;
      color: #047857;
    }

    .oc-status-pill.orange {
      background: #fffbeb;
      color: #b45309;
    }

    .oc-color-pair {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-top: 12px;
    }

    .oc-color-dot {
      width: 18px;
      height: 18px;
      border-radius: 999px;
      border: 1px solid rgba(17, 24, 39, .08);
    }

    .oc-analytics {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin-top: 18px;
    }

    @media(max-width:1100px) {
      .oc-analytics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media(max-width:600px) {
      .oc-analytics {
        grid-template-columns: 1fr;
      }
    }

    .oc-stat {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      gap: 12px;
      min-height: 92px;
    }

    .oc-stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .oc-stat-icon.total {
      background: var(--blue-light);
      color: var(--blue)
    }

    .oc-stat-icon.contact {
      background: var(--success-light);
      color: var(--success)
    }

    .oc-stat-icon.money {
      background: var(--warning-light);
      color: #d97706
    }

    .oc-stat-icon.team {
      background: #f3f4f6;
      color: #6b7280
    }

    .oc-stat-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .oc-stat-value {
      font-size: 22px;
      font-weight: 900;
      color: #111827;
      line-height: 1.1;
      margin-top: 4px;
    }

    .oc-stat-sub {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .oc-info-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    @media(max-width:760px) {
      .oc-info-grid {
        grid-template-columns: 1fr;
      }
    }

    .oc-info-box {
      border: 1px solid var(--border);
      border-radius: 14px;
      background: #fff;
      padding: 14px;
    }

    .oc-info-label {
      font-size: 11px;
      font-weight: 900;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
      margin-bottom: 6px;
    }

    .oc-info-value {
      font-size: 14px;
      font-weight: 700;
      color: #111827;
      line-height: 1.6;
      word-break: break-word;
    }

    .oc-info-value.muted {
      color: var(--text-muted);
      font-weight: 600;
    }

    .oc-address-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .oc-address-card {
      border: 1px solid var(--border);
      border-radius: 16px;
      background: #fff;
      padding: 16px;
      transition: var(--transition);
    }

    .oc-address-card:hover {
      border-color: var(--primary);
      box-shadow: var(--shadow-sm);
    }

    .oc-address-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }

    .oc-address-title {
      font-size: 15px;
      font-weight: 900;
      color: #111827;
      margin: 0 0 4px 0;
    }

    .oc-address-sub {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .oc-address-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
    }

    @media(max-width:960px) {
      .oc-address-grid {
        grid-template-columns: 1fr;
      }
    }

    .oc-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      flex-wrap: wrap;
    }

    .oc-staff-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .oc-staff-card {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 12px;
      background: #fff;
    }

    .oc-avatar {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      object-fit: cover;
      border: 1px solid var(--border);
      flex: 0 0 auto;
    }

    .oc-staff-meta {
      min-width: 0;
      flex: 1;
    }

    .oc-staff-name {
      font-size: 14px;
      font-weight: 900;
      color: #111827;
      margin: 0 0 4px 0;
    }

    .oc-staff-sub {
      font-size: 12px;
      color: var(--text-muted);
      line-height: 1.5;
      word-break: break-word;
    }

    .oc-empty {
      text-align: center;
      padding: 40px 20px;
      color: var(--text-muted);
      background: #fff;
      border: 1px dashed var(--border);
      border-radius: 16px;
    }

    .oc-link {
      color: #2563eb;
      text-decoration: none;
      word-break: break-word;
    }

    .oc-link:hover {
      text-decoration: underline;
    }

    .oc-modal-backdrop {
      position: fixed;
      inset: 0;
      z-index: 1200;
      background: rgba(17, 24, 39, .55);
      backdrop-filter: blur(3px);
      opacity: 0;
      pointer-events: none;
      transition: opacity .22s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px;
    }

    .oc-modal-backdrop.open {
      opacity: 1;
      pointer-events: auto;
    }

    .oc-modal {
      width: 100%;
      max-width: 1080px;
      background: #fff;
      border: 1px solid rgba(229, 231, 235, .9);
      border-radius: 16px;
      box-shadow: var(--shadow);
      transform: translateY(12px) scale(.985);
      transition: transform .22s ease;
      overflow: hidden;
    }

    .oc-modal-backdrop.open .oc-modal {
      transform: translateY(0) scale(1)
    }

    .oc-modal-h {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      padding: 16px 18px;
      border-bottom: 1px solid var(--border);
      background: #fafafa;
    }

    .oc-modal-ttl {
      font-weight: 900;
      font-size: 16px;
      line-height: 1.2;
      margin: 0;
      color: #111827;
    }

    .oc-modal-b {
      padding: 20px 18px;
      max-height: 78vh;
      overflow-y: auto;
    }

    .oc-modal-f {
      padding: 14px 18px;
      border-top: 1px solid var(--border);
      background: #fafafa;
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .oc-form-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.2fr) 320px;
      gap: 18px;
    }

    @media(max-width:960px) {
      .oc-form-layout {
        grid-template-columns: 1fr;
      }
    }

    .oc-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    @media(max-width:760px) {
      .oc-form-grid {
        grid-template-columns: 1fr;
      }
    }

    .oc-form-group {
      margin-bottom: 0;
    }

    .oc-form-group.full {
      grid-column: 1 / -1;
    }

    .oc-label {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 6px;
    }

    .oc-input-form,
    .oc-select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #fff;
      font-size: 14px;
      outline: none;
      transition: var(--transition);
    }

    .oc-input-form:focus,
    .oc-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    .oc-map-box {
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      background: #fff;
      min-height: 280px;
    }

    #gmp-map {
      height: 100%;
      min-height: 280px;
      width: 100%;
    }

    .invalid-feedback {
      display: block;
      margin-top: 6px;
      font-size: 12px;
      color: #dc2626;
    }

    .is-invalid {
      border-color: #ef4444 !important;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, .08) !important;
    }

    .oc-toast-wrap {
      position: fixed;
      right: 20px;
      bottom: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }

    .oc-toast {
      pointer-events: auto;
      min-width: 280px;
      max-width: 360px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 14px;
      box-shadow: var(--shadow);
      padding: 12px;
      display: flex;
      gap: 10px;
      align-items: flex-start;
      animation: ocToastIn .3s cubic-bezier(.175, .885, .32, 1.275) forwards;
    }

    @keyframes ocToastIn {
      from {
        transform: translateX(100%);
        opacity: 0
      }

      to {
        transform: translateX(0);
        opacity: 1
      }
    }

    .oc-toast-ic {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .oc-toast-ic.ok {
      background: var(--success-light);
      color: var(--success)
    }

    .oc-toast-ic.bad {
      background: var(--danger-light);
      color: var(--danger)
    }

    .oc-toast-ttl {
      font-weight: 900;
      font-size: 13px;
      margin: 0;
      color: #111827
    }

    .oc-toast-msg {
      font-size: 12px;
      color: #374151;
      margin: 4px 0 0 0;
      line-height: 1.4
    }

    .oc-toast-x {
      margin-left: auto;
      background: transparent;
      border: none;
      cursor: pointer;
      color: var(--text-muted);
    }

    .spin {
      animation: ocSpin .8s linear infinite
    }

    @keyframes ocSpin {
      to {
        transform: rotate(360deg)
      }
    }
  </style>
@endsection

@section('content')
  <div class="oc-wrap">
    <div class="oc-header">
      <div class="oc-titlebar">
        <div>
          <div class="oc-title">FILIALPROFIL</div>
          <div class="oc-sub">Übersicht über Stammdaten, Kontaktdaten, Filialadressen und Mitarbeiter.</div>

          <div class="oc-breadcrumb">
            <a href="{{ url('/employee_dashboard') }}">Home</a>
            <span>›</span>
            <a href="{{ route('branch.info') }}">Filialen</a>
            <span>›</span>
            <span class="current">{{ $data->branch }}</span>
          </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="{{ route('branch.info') }}" class="oc-btn-soft">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 18l-6-6 6-6" />
            </svg>
            Zurück
          </a>

          <button type="button" class="oc-btn-soft" onclick="openModal('editBranchModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            Filiale bearbeiten
          </button>

          <button type="button" class="oc-btn" onclick="openModal('createAddressModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 5v14M5 12h14"></path>
            </svg>
            Neue Adresse
          </button>
        </div>
      </div>
    </div>

    <div class="oc-grid-main">
      <div>
        <div class="oc-card" style="margin-bottom:18px;">
          <div class="oc-card-body">
            <div class="oc-hero">
              <div>
                <img src="{{ $branchLogo }}" alt="{{ $data->branch }}" class="oc-logo">
              </div>

              <div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                  <div>
                    <h1 class="oc-hero-title">{{ $data->branch }}</h1>
                    <div class="oc-hero-sub">
                      {{ $data->initial ?: '—' }}
                      @if($data->slug) • {{ $data->slug }} @endif
                      @if($data->city) • {{ $data->city }} @endif
                      @if($data->country) • {{ $data->country }} @endif
                    </div>
                  </div>

                  <div>
                    <span class="oc-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                  </div>
                </div>

                <div class="oc-color-pair">
                  <span class="oc-color-dot" style="background:{{ $data->color ?? '#93c21c' }}"></span>
                  <span class="oc-color-dot" style="background:{{ $data->second_color ?? '#1f2937' }}"></span>
                </div>

                <div class="oc-analytics">
                  <div class="oc-stat">
                    <div class="oc-stat-icon total">
                      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18" />
                      </svg>
                    </div>
                    <div>
                      <div class="oc-stat-label">Adressen</div>
                      <div class="oc-stat-value">{{ count($addresses) }}</div>
                      <div class="oc-stat-sub">Gespeicherte Standorte</div>
                    </div>
                  </div>

                  <div class="oc-stat">
                    <div class="oc-stat-icon contact">
                      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                          d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z" />
                      </svg>
                    </div>
                    <div>
                      <div class="oc-stat-label">Kontakt</div>
                      <div class="oc-stat-value">{{ $data->phone ? 'Ja' : '—' }}</div>
                      <div class="oc-stat-sub">{{ $data->email ?: 'Keine E-Mail' }}</div>
                    </div>
                  </div>

                  <div class="oc-stat">
                    <div class="oc-stat-icon money">
                      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <path d="M2 10h20" />
                      </svg>
                    </div>
                    <div>
                      <div class="oc-stat-label">Finanzen</div>
                      <div class="oc-stat-value">{{ $data->iban ? 'Ja' : '—' }}</div>
                      <div class="oc-stat-sub">{{ $data->bank ?: 'Keine Bankdaten' }}</div>
                    </div>
                  </div>

                  <div class="oc-stat">
                    <div class="oc-stat-icon team">
                      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                      </svg>
                    </div>
                    <div>
                      <div class="oc-stat-label">Mitarbeiter</div>
                      <div class="oc-stat-value">{{ count($branchEmployees) }}</div>
                      <div class="oc-stat-sub">Zugeordnete Personen</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="oc-card" style="margin-bottom:18px;">
          <div class="oc-card-head">
            <h3 class="oc-card-title">Stammdaten & Kontakt</h3>
          </div>
          <div class="oc-card-body">
            <div class="oc-info-grid">
              <div class="oc-info-box">
                <div class="oc-info-label">Straße / Nr.</div>
                <div class="oc-info-value {{ $data->street ? '' : 'muted' }}">{{ $data->street ?: 'Nicht hinterlegt' }}
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">PLZ / Ort / Land</div>
                <div class="oc-info-value {{ ($data->postcode || $data->city || $data->country) ? '' : 'muted' }}">
                  {{ trim(($data->postcode ?: '') . ' ' . ($data->city ?: '')) ?: 'Nicht hinterlegt' }}
                  @if($data->country)
                    <br>{{ $data->country }}
                  @endif
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">E-Mail</div>
                <div class="oc-info-value {{ $data->email ? '' : 'muted' }}">
                  @if($data->email)
                    <a href="mailto:{{ $data->email }}" class="oc-link">{{ $data->email }}</a>
                  @else
                    Nicht hinterlegt
                  @endif
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Telefon</div>
                <div class="oc-info-value {{ $data->phone ? '' : 'muted' }}">{{ $data->phone ?: 'Nicht hinterlegt' }}
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">WhatsApp</div>
                <div class="oc-info-value {{ $data->whatsapp ? '' : 'muted' }}">
                  {{ $data->whatsapp ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Website</div>
                <div class="oc-info-value {{ $data->web ? '' : 'muted' }}">
                  @if($data->web)
                    <a href="{{ $data->web }}" target="_blank" class="oc-link">{{ $data->web }}</a>
                  @else
                    Nicht hinterlegt
                  @endif
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Geschäftsführer</div>
                <div class="oc-info-value {{ $data->gf ? '' : 'muted' }}">{{ $data->gf ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Mitarbeiterzahl</div>
                <div class="oc-info-value {{ $data->employee_count ? '' : 'muted' }}">
                  {{ $data->employee_count ?: 'Nicht hinterlegt' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="oc-card" style="margin-bottom:18px;">
          <div class="oc-card-head">
            <h3 class="oc-card-title">Bank- & Registerdaten</h3>
          </div>
          <div class="oc-card-body">
            <div class="oc-info-grid">
              <div class="oc-info-box">
                <div class="oc-info-label">Bank</div>
                <div class="oc-info-value {{ $data->bank ? '' : 'muted' }}">{{ $data->bank ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">IBAN</div>
                <div class="oc-info-value {{ $data->iban ? '' : 'muted' }}">{{ $data->iban ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">BIC</div>
                <div class="oc-info-value {{ $data->bic ? '' : 'muted' }}">{{ $data->bic ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Register</div>
                <div class="oc-info-value {{ $data->register ? '' : 'muted' }}">
                  {{ $data->register ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Steuer-Nr.</div>
                <div class="oc-info-value {{ $data->tax ? '' : 'muted' }}">{{ $data->tax ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">USt-IdNr.</div>
                <div class="oc-info-value {{ $data->vat ? '' : 'muted' }}">{{ $data->vat ?: 'Nicht hinterlegt' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="oc-card">
          <div class="oc-card-head">
            <h3 class="oc-card-title">Filialadressen</h3>
            <button type="button" class="oc-btn" onclick="openModal('createAddressModal')">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"></path>
              </svg>
              Adresse hinzufügen
            </button>
          </div>

          <div class="oc-card-body">
            @if(count($addresses))
              <div class="oc-address-list">
                @foreach($addresses as $address)
                  <div class="oc-address-card">
                    <div class="oc-address-head">
                      <div>
                        <h4 class="oc-address-title">{{ $address->name ?: ('Adresse #' . $address->id) }}</h4>
                        <div class="oc-address-sub">
                          {{ $address->full_address ?: 'Keine vollständige Adresse hinterlegt' }}
                        </div>
                      </div>

                      <div class="oc-actions">
                        <button type="button" class="oc-btn-ic primary js-edit-address" title="Bearbeiten"
                          data-id="{{ $address->id }}" data-name="{{ $address->name }}"
                          data-employee_id="{{ $address->employee_id }}" data-full_address="{{ $address->full_address }}"
                          data-street="{{ $address->street }}" data-postcode="{{ $address->postcode }}"
                          data-city="{{ $address->city }}" data-latitude="{{ $address->latitude }}"
                          data-longitude="{{ $address->longitude }}" data-elevation="{{ $address->elevation }}"
                          data-phone="{{ $address->phone }}" data-email="{{ $address->email }}"
                          data-status="{{ $address->status }}">
                          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                          </svg>
                        </button>

                        <button type="button" class="oc-btn-ic danger delete-address" title="Löschen"
                          data-id="{{ $address->id }}">
                          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                              d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <div class="oc-address-grid">
                      <div class="oc-info-box">
                        <div class="oc-info-label">Straße</div>
                        <div class="oc-info-value {{ $address->street ? '' : 'muted' }}">
                          {{ $address->street ?: 'Nicht hinterlegt' }}</div>
                      </div>

                      <div class="oc-info-box">
                        <div class="oc-info-label">Stadt / PLZ</div>
                        <div class="oc-info-value {{ ($address->city || $address->postcode) ? '' : 'muted' }}">
                          {{ trim(($address->postcode ?: '') . ' ' . ($address->city ?: '')) ?: 'Nicht hinterlegt' }}
                        </div>
                      </div>

                      <div class="oc-info-box">
                        <div class="oc-info-label">Status</div>
                        <div class="oc-info-value {{ $address->status ? '' : 'muted' }}">
                          {{ $address->status ?: 'Nicht hinterlegt' }}</div>
                      </div>

                      <div class="oc-info-box">
                        <div class="oc-info-label">Telefon</div>
                        <div class="oc-info-value {{ $address->phone ? '' : 'muted' }}">
                          {{ $address->phone ?: 'Nicht hinterlegt' }}</div>
                      </div>

                      <div class="oc-info-box">
                        <div class="oc-info-label">E-Mail</div>
                        <div class="oc-info-value {{ $address->email ? '' : 'muted' }}">
                          @if($address->email)
                            <a href="mailto:{{ $address->email }}" class="oc-link">{{ $address->email }}</a>
                          @else
                            Nicht hinterlegt
                          @endif
                        </div>
                      </div>

                      <div class="oc-info-box">
                        <div class="oc-info-label">ID</div>
                        <div class="oc-info-value">#{{ $address->id }}</div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="oc-empty">
                Es sind noch keine Filialadressen hinterlegt. Klicken Sie auf <strong>Adresse hinzufügen</strong>, um eine
                neue Adresse zu erstellen.
              </div>
            @endif
          </div>
        </div>
      </div>

      <div>
        <div class="oc-card" style="margin-bottom:18px;">
          <div class="oc-card-head">
            <h3 class="oc-card-title">Mitarbeiter der Niederlassung</h3>
          </div>

          <div class="oc-card-body">
            @if(count($branchEmployees))
              <div class="oc-staff-list">
                @foreach($branchEmployees as $emp)
                  @php
                    $img = $emp->image
                      ? asset('images/employee/' . $emp->image)
                      : asset('images/gender/male.png');
                  @endphp

                  <div class="oc-staff-card">
                    <img src="{{ $img }}" alt="{{ $emp->name }} {{ $emp->lastname }}" class="oc-avatar">

                    <div class="oc-staff-meta">
                      <h4 class="oc-staff-name">{{ $emp->name }} {{ $emp->lastname }}</h4>
                      <div class="oc-staff-sub">
                        {{ $emp->email ?: 'Keine E-Mail hinterlegt' }}
                        @if($emp->phone)
                          <br>Tel: {{ $emp->phone }}
                        @endif
                        @if($emp->status)
                          <br>Status: {{ $emp->status }}
                        @endif
                      </div>
                    </div>

                    @if(Route::has('employee.profile'))
                      <a href="{{ route('employee.profile', $emp->id) }}" class="oc-btn-ic primary"
                        title="Mitarbeiterprofil öffnen">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                          <circle cx="12" cy="7" r="4" />
                        </svg>
                      </a>
                    @endif
                  </div>
                @endforeach
              </div>
            @else
              <div class="oc-empty">
                Es sind aktuell keine Mitarbeiter dieser Niederlassung zugeordnet.
              </div>
            @endif
          </div>
        </div>

        <div class="oc-card">
          <div class="oc-card-head">
            <h3 class="oc-card-title">Schnellübersicht</h3>
          </div>
          <div class="oc-card-body">
            <div class="oc-info-grid" style="grid-template-columns:1fr;">
              <div class="oc-info-box">
                <div class="oc-info-label">Filiale</div>
                <div class="oc-info-value">{{ $data->branch }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Initial</div>
                <div class="oc-info-value {{ $data->initial ? '' : 'muted' }}">{{ $data->initial ?: 'Nicht hinterlegt' }}
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Slug</div>
                <div class="oc-info-value {{ $data->slug ? '' : 'muted' }}">{{ $data->slug ?: 'Nicht hinterlegt' }}</div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Logo URL</div>
                <div class="oc-info-value {{ $data->logo_url ? '' : 'muted' }}">
                  @if($data->logo_url)
                    <a href="{{ $data->logo_url }}" target="_blank" class="oc-link">{{ $data->logo_url }}</a>
                  @else
                    Nicht hinterlegt
                  @endif
                </div>
              </div>

              <div class="oc-info-box">
                <div class="oc-info-label">Gesamtausgaben</div>
                <div class="oc-info-value {{ $data->total_expense ? '' : 'muted' }}">
                  {{ $data->total_expense ?: 'Nicht hinterlegt' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
  </div>


  <div class="oc-modal-backdrop" id="editBranchModal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Filiale bearbeiten</h3>
        <button class="oc-btn-ic" type="button" onclick="closeModal('editBranchModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form method="POST" action="{{ route('branch.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id }}">

        <div class="oc-modal-b">
          <div class="oc-form-grid">
            <div class="oc-form-group">
              <label class="oc-label">Filialname *</label>
              <input type="text" class="oc-input-form" name="branch" value="{{ old('branch', $data->branch) }}" required>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Kürzel</label>
              <input type="text" class="oc-input-form" name="initial" value="{{ old('initial', $data->initial) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Slug</label>
              <input type="text" class="oc-input-form" name="slug" value="{{ old('slug', $data->slug) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Status</label>
              <select name="status" class="oc-select">
                <option value="published" @selected(old('status', $data->status ?? 'published') === 'published')>Aktiv
                </option>
                <option value="unpublished" @selected(old('status', $data->status ?? 'published') === 'unpublished')>Inaktiv
                </option>
              </select>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Geschäftsführer</label>
              <input type="text" class="oc-input-form" name="gf" value="{{ old('gf', $data->gf) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Mitarbeiterzahl</label>
              <input type="number" min="0" class="oc-input-form" name="employee_count"
                value="{{ old('employee_count', $data->employee_count) }}">
            </div>

            <div class="oc-form-group full">
              <label class="oc-label">Straße / Nr.</label>
              <input type="text" class="oc-input-form" name="street" value="{{ old('street', $data->street) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">PLZ</label>
              <input type="text" class="oc-input-form" name="postcode" value="{{ old('postcode', $data->postcode) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Ort</label>
              <input type="text" class="oc-input-form" name="city" value="{{ old('city', $data->city) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Land</label>
              <input type="text" class="oc-input-form" name="country" value="{{ old('country', $data->country) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">E-Mail</label>
              <input type="email" class="oc-input-form" name="email" value="{{ old('email', $data->email) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Telefon</label>
              <input type="text" class="oc-input-form" name="phone" value="{{ old('phone', $data->phone) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">WhatsApp</label>
              <input type="text" class="oc-input-form" name="whatsapp" value="{{ old('whatsapp', $data->whatsapp) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Website</label>
              <input type="text" class="oc-input-form" name="web" value="{{ old('web', $data->web) }}"
                placeholder="https://example.com">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Vorsitzender</label>
              <select name="chairman" class="oc-select">
                <option value="">Bitte wählen</option>
                @foreach($employee as $emp)
                  <option value="{{ $emp->id }}" @selected((string) old('chairman', $data->chairman) === (string) $emp->id)>
                    {{ $emp->name }} {{ $emp->lastname }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Kontaktperson</label>
              <select name="contact_person" class="oc-select">
                <option value="">Bitte wählen</option>
                @foreach($employee as $emp)
                  <option value="{{ $emp->id }}" @selected((string) old('contact_person', $data->contact_person) === (string) $emp->id)>
                    {{ $emp->name }} {{ $emp->lastname }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Primärfarbe</label>
              <div style="display:flex;gap:10px;align-items:center;">
                <input type="color" class="oc-input-form js-color-picker" data-target="edit_branch_color_text"
                  style="width:58px;padding:4px;" value="{{ old('color', $data->color ?: '#93c21c') }}">
                <input type="text" class="oc-input-form js-color-text" name="color" id="edit_branch_color_text"
                  value="{{ old('color', $data->color ?: '#93c21c') }}">
              </div>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Sekundärfarbe</label>
              <div style="display:flex;gap:10px;align-items:center;">
                <input type="color" class="oc-input-form js-color-picker" data-target="edit_branch_second_color_text"
                  style="width:58px;padding:4px;" value="{{ old('second_color', $data->second_color ?: '#1f2937') }}">
                <input type="text" class="oc-input-form js-color-text" name="second_color"
                  id="edit_branch_second_color_text" value="{{ old('second_color', $data->second_color ?: '#1f2937') }}">
              </div>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Bank</label>
              <input type="text" class="oc-input-form" name="bank" value="{{ old('bank', $data->bank) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">IBAN</label>
              <input type="text" class="oc-input-form" name="iban" value="{{ old('iban', $data->iban) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">BIC</label>
              <input type="text" class="oc-input-form" name="bic" value="{{ old('bic', $data->bic) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Gesamtausgaben</label>
              <input type="number" step="0.01" min="0" class="oc-input-form" name="total_expense"
                value="{{ old('total_expense', $data->total_expense) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Register</label>
              <input type="text" class="oc-input-form" name="register" value="{{ old('register', $data->register) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Steuer-Nr.</label>
              <input type="text" class="oc-input-form" name="tax" value="{{ old('tax', $data->tax) }}">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">USt-IdNr.</label>
              <input type="text" class="oc-input-form" name="vat" value="{{ old('vat', $data->vat) }}">
            </div>

            <div class="oc-form-group full">
              <label class="oc-label">Logo URL</label>
              <input type="text" class="oc-input-form" name="logo_url" value="{{ old('logo_url', $data->logo_url) }}">
            </div>

            <div class="oc-form-group full">
              <label class="oc-label">Bild / Logo Upload</label>
              <input type="file" class="oc-input-form" name="image">
            </div>
          </div>
        </div>

        <div class="oc-modal-f">
          <button type="button" class="oc-btn-soft" onclick="closeModal('editBranchModal')">Abbrechen</button>
          <button type="submit" class="oc-btn">Änderungen speichern</button>
        </div>
      </form>
    </div>
  </div>

  <div class="oc-modal-backdrop" id="editAddressModal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Adresse bearbeiten</h3>
        <button class="oc-btn-ic" type="button" onclick="closeModal('editAddressModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="edit-address-form">
        @csrf
        <input type="hidden" name="id" id="edit_address_id">
        <input type="hidden" name="branch_id" value="{{ $data->id }}">

        <div class="oc-modal-b">
          <div class="oc-form-grid">
            <div class="oc-form-group">
              <label class="oc-label">Zweigname</label>
              <input type="text" class="oc-input-form" name="name" id="edit_address_name">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Kontaktperson</label>
              <select class="oc-select" name="employee_id" id="edit_address_employee_id">
                <option value="">Bitte wählen</option>
                @foreach ($employee as $emp)
                  <option value="{{ $emp->id }}">
                    {{ $emp->name }} {{ $emp->lastname }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="oc-form-group full">
              <label class="oc-label">Adresse</label>
              <input type="text" class="oc-input-form" name="full_address" id="edit_address_full_address"
                placeholder="Adresse eingeben">
              <input type="hidden" name="street" id="edit_address_street">
              <input type="hidden" name="postcode" id="edit_address_postcode">
              <input type="hidden" name="city" id="edit_address_city">
              <input type="hidden" name="latitude" id="edit_address_latitude">
              <input type="hidden" name="longitude" id="edit_address_longitude">
              <input type="hidden" name="elevation" id="edit_address_elevation">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Telefon</label>
              <input type="text" class="oc-input-form" name="phone" id="edit_address_phone">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">E-Mail</label>
              <input type="email" class="oc-input-form" name="email" id="edit_address_email">
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Status</label>
              <input type="text" class="oc-input-form" name="status" id="edit_address_status">
            </div>
          </div>
        </div>

        <div class="oc-modal-f">
          <button type="button" class="oc-btn-soft" onclick="closeModal('editAddressModal')">Abbrechen</button>
          <button type="button" class="oc-btn update-address-btn">Adresse speichern</button>
        </div>
      </form>
    </div>
  </div>

  <div class="oc-modal-backdrop" id="createAddressModal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Neue Adresse anlegen</h3>
        <button class="oc-btn-ic" type="button" onclick="closeModal('createAddressModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="store-form">
        @csrf
        <div class="oc-modal-b">
          <div class="oc-form-layout">
            <div>
              <div class="oc-form-grid">
                <div class="oc-form-group">
                  <label class="oc-label">Zweigname</label>
                  <input type="hidden" name="branch_id" value="{{ $data->id }}">
                  <input type="text" class="oc-input-form" name="name" value="{{ old('name') }}">
                </div>

                <div class="oc-form-group">
                  <label class="oc-label">Kontaktperson</label>
                  <select class="oc-select" name="employee_id">
                    @foreach ($employee as $emp)
                      <option value="{{ $emp->id }}">
                        {{ $emp->name }} {{ $emp->lastname }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="oc-form-group full">
                  <label class="oc-label">Adresse</label>
                  <input type="text" class="oc-input-form" name="full_address" id="full_address"
                    value="{{ old('full_address') }}" placeholder="Adresse eingeben">
                  <input type="hidden" name="street" id="street-input" value="{{ old('street') }}">
                  <input type="hidden" name="postcode" id="postal_code-input" value="{{ old('postcode') }}">
                  <input type="hidden" name="city" id="locality-input" value="{{ old('city') }}">
                  <input type="hidden" name="latitude" id="latitude-input" value="{{ old('latitude') }}">
                  <input type="hidden" name="longitude" id="longitude-input" value="{{ old('longitude') }}">
                  <input type="hidden" name="elevation" id="elevation-input" value="{{ old('elevation') }}">
                </div>

                <div class="oc-form-group">
                  <label class="oc-label">Telefon</label>
                  <input type="text" class="oc-input-form" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="oc-form-group">
                  <label class="oc-label">E-Mail</label>
                  <input type="email" class="oc-input-form" name="email"
                    value="{{ session('customer_email') ?: old('email') }}">
                </div>
              </div>
            </div>

            <div>
              <div class="oc-map-box">
                <div id="gmp-map"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="oc-modal-f">
          <button type="button" class="oc-btn-soft" onclick="closeModal('createAddressModal')">Abbrechen</button>
          <button type="button" class="oc-btn save-task">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
              <path d="M17 21v-8H7v8" />
              <path d="M7 3v5h8" />
            </svg>
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{ asset('app-assets/js/scripts/pages/user-profile.js') }}"></script>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"
    defer></script>

  <script>
    function openModal(id) {
      const el = document.getElementById(id);
      if (el) {
        el.classList.add('open');

        if (id === 'createAddressModal') {
          setTimeout(() => {
            if (window.google && google.maps && typeof initMap === 'function') {
              initMap();
            }
          }, 250);
        }
      }
    }

    function closeModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.remove('open');
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
      setTimeout(() => { try { el.remove(); } catch (e) { } }, 4000);
    }

    document.addEventListener('click', function (e) {
      if (e.target.classList.contains('oc-modal-backdrop')) {
        e.target.classList.remove('open');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
      }
    });

    function fillInput(id, value) {
      const el = document.getElementById(id);
      if (el) el.value = value || '';
    }

    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.js-edit-address');
      if (!btn) return;

      fillInput('edit_address_id', btn.dataset.id);
      fillInput('edit_address_name', btn.dataset.name);
      fillInput('edit_address_employee_id', btn.dataset.employee_id);
      fillInput('edit_address_full_address', btn.dataset.full_address);
      fillInput('edit_address_street', btn.dataset.street);
      fillInput('edit_address_postcode', btn.dataset.postcode);
      fillInput('edit_address_city', btn.dataset.city);
      fillInput('edit_address_latitude', btn.dataset.latitude);
      fillInput('edit_address_longitude', btn.dataset.longitude);
      fillInput('edit_address_elevation', btn.dataset.elevation);
      fillInput('edit_address_phone', btn.dataset.phone);
      fillInput('edit_address_email', btn.dataset.email);
      fillInput('edit_address_status', btn.dataset.status);

      openModal('editAddressModal');
    });

    document.addEventListener('input', function (e) {
      const picker = e.target.closest('.js-color-picker');
      if (picker) {
        const target = document.getElementById(picker.dataset.target);
        if (target) target.value = picker.value;
      }

      const text = e.target.closest('.js-color-text');
      if (text && /^#[0-9A-Fa-f]{6}$/.test(text.value)) {
        const pickerForText = document.querySelector(`.js-color-picker[data-target="${text.id}"]`);
        if (pickerForText) pickerForText.value = text.value;
      }
    });
  </script>

  <script>
    let map;
    let marker;
    let autocompleteInstance = null;

    function initMap() {
      const mapEl = document.getElementById('gmp-map');
      if (!mapEl || !(window.google && google.maps)) return;

      const defaultLat = parseFloat(document.getElementById('latitude-input')?.value || 50.1109);
      const defaultLng = parseFloat(document.getElementById('longitude-input')?.value || 8.6821);

      map = new google.maps.Map(mapEl, {
        center: { lat: defaultLat, lng: defaultLng },
        zoom: 10,
      });

      marker = new google.maps.Marker({
        position: { lat: defaultLat, lng: defaultLng },
        map: map,
      });

      if (!autocompleteInstance) {
        initAutocomplete();
      }

      google.maps.event.trigger(map, 'resize');
      map.setCenter({ lat: defaultLat, lng: defaultLng });
    }

    function initAutocomplete() {
      const fullAddressInput = document.getElementById("full_address");
      if (!fullAddressInput || !(window.google && google.maps && google.maps.places)) return;

      const streetInput = document.getElementById("street-input");
      const latitudeInput = document.getElementById("latitude-input");
      const longitudeInput = document.getElementById("longitude-input");
      const elevationInput = document.getElementById("elevation-input");
      const postalCodeInput = document.getElementById("postal_code-input");
      const cityInput = document.getElementById("locality-input");

      const elevationService = new google.maps.ElevationService();

      autocompleteInstance = new google.maps.places.Autocomplete(fullAddressInput, {
        fields: ["address_components", "geometry"],
        types: ["address"],
      });

      autocompleteInstance.addListener("place_changed", () => {
        const place = autocompleteInstance.getPlace();

        if (!place.geometry) {
          Swal.fire({
            icon: 'warning',
            title: 'Adresse nicht gefunden',
            text: 'Für die ausgewählte Adresse konnten keine Details geladen werden.',
          });
          return;
        }

        const location = place.geometry.location;
        latitudeInput.value = location.lat();
        longitudeInput.value = location.lng();

        updateMap(location);
        fetchElevation(location, elevationInput);

        const components = parseAddressComponents(place.address_components);

        streetInput.value = `${components.route} ${components.street_number}`.trim();
        postalCodeInput.value = components.postal_code;
        cityInput.value = components.locality || components.administrative_area_level_1 || components.administrative_area_level_2;
        fullAddressInput.value = `${components.route} ${components.street_number}, ${cityInput.value}, ${components.postal_code}`;
      });

      function fetchElevation(location, elevationInput) {
        elevationService.getElevationForLocations(
          { locations: [location] },
          (results, status) => {
            if (status === google.maps.ElevationStatus.OK && results[0]) {
              elevationInput.value = results[0].elevation.toFixed(2);
            } else {
              elevationInput.value = "";
            }
          }
        );
      }

      function parseAddressComponents(components) {
        const address = {
          street_number: "",
          route: "",
          locality: "",
          postal_code: "",
          administrative_area_level_1: "",
          administrative_area_level_2: "",
        };

        (components || []).forEach((component) => {
          if (component.types.includes("street_number")) {
            address.street_number = component.long_name;
          }
          if (component.types.includes("route")) {
            address.route = component.long_name;
          }
          if (component.types.includes("locality")) {
            address.locality = component.long_name;
          }
          if (component.types.includes("administrative_area_level_1")) {
            address.administrative_area_level_1 = component.long_name;
          }
          if (component.types.includes("administrative_area_level_2")) {
            address.administrative_area_level_2 = component.long_name;
          }
          if (component.types.includes("postal_code")) {
            address.postal_code = component.long_name;
          }
        });

        return address;
      }

      function updateMap(location) {
        if (!marker) {
          marker = new google.maps.Marker({
            position: location,
            map: map,
            animation: google.maps.Animation.DROP,
          });
        } else {
          marker.setPosition(location);
        }

        map.panTo(location);
        map.setZoom(15);
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
      setTimeout(() => {
        if (window.google && google.maps) {
          initMap();
        }
      }, 600);
    });
  </script>

  <script>
    $(document).ready(function () {
      $('.save-task').click(function (e) {
        e.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let formData = new FormData($('#store-form')[0]);

        $.ajax({
          url: "{{ route('branch.address.store') }}",
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function () {
            $('.save-task')
              .prop('disabled', true)
              .html(`
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" class="spin">
                              <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            Speichert...
                          `);
          },
          success: function (response) {
            Swal.fire({
              icon: 'success',
              title: 'Gespeichert',
              text: response.message || 'Filialadresse erfolgreich hinzugefügt.',
              timer: 2200,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          },
          error: function (xhr) {
            if (xhr.status === 422) {
              const errors = xhr.responseJSON.errors || {};
              let errorMessage = '';

              for (let key in errors) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                errorMessage += `${errors[key][0]}<br>`;
              }

              Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: errorMessage,
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Beim Speichern ist ein Fehler aufgetreten. Bitte erneut versuchen.',
              });
            }
          },
          complete: function () {
            $('.save-task')
              .prop('disabled', false)
              .html(`
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                              <path d="M17 21v-8H7v8"/>
                              <path d="M7 3v5h8"/>
                            </svg>
                            Speichern
                          `);
          }
        });
      });
    });

  </script>

  <script>
    $(document).ready(function () {
      $('.update-address-btn').click(function (e) {
        e.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const formData = new FormData($('#edit-address-form')[0]);

        $.ajax({
          url: "{{ Route::has('branch.address.update') ? route('branch.address.update') : url('/branch_address_update') }}",
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function () {
            $('.update-address-btn')
              .prop('disabled', true)
              .html('Speichert...');
          },
          success: function (response) {
            Swal.fire({
              icon: 'success',
              title: 'Gespeichert',
              text: response.message || 'Filialadresse erfolgreich aktualisiert.',
              timer: 1800,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          },
          error: function (xhr) {
            if (xhr.status === 422) {
              const errors = xhr.responseJSON.errors || {};
              let errorMessage = '';

              for (let key in errors) {
                const input = $(`#edit-address-form [name="${key}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                errorMessage += `${errors[key][0]}<br>`;
              }

              Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: errorMessage,
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Die Adresse konnte nicht gespeichert werden. Bitte Route branch.address.update prüfen.',
              });
            }
          },
          complete: function () {
            $('.update-address-btn')
              .prop('disabled', false)
              .html('Adresse speichern');
          }
        });
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      $('.delete-address').click(function (e) {
        e.preventDefault();

        let addressId = $(this).data('id');

        Swal.fire({
          title: 'Sind Sie sicher?',
          text: "Diese Aktion kann nicht rückgängig gemacht werden.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#93c21c',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ja, löschen',
          cancelButtonText: 'Abbrechen'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `/branch_address_destroy/${addressId}`,
              method: 'GET',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              beforeSend: function () {
                Swal.fire({
                  title: 'Löschen...',
                  text: 'Bitte warten.',
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  didOpen: () => {
                    Swal.showLoading();
                  }
                });
              },
              success: function (response) {
                Swal.fire({
                  icon: 'success',
                  title: 'Gelöscht',
                  text: response.message || 'Filialadresse erfolgreich gelöscht.',
                  timer: 1800,
                  showConfirmButton: false
                }).then(() => {
                  window.location.reload();
                });
              },
              error: function () {
                Swal.fire({
                  icon: 'error',
                  title: 'Fehler',
                  text: 'Etwas ist schiefgelaufen. Bitte versuchen Sie es erneut.',
                });
              }
            });
          }
        });
      });
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
        label: 'Filialen',
        url: "{{ url('branch')}}",
        clickable: false

      },
      {
        label: 'Filialadressen und Mitarbeiter',
        url: "{{ url()->current() }}",
        clickable: false

      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush