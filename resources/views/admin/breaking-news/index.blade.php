@extends('admin.layouts.app')

@section('title', 'Breaking News – Verwaltung')

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- Page layout --- */
        .breaking-news-page {  
            box-sizing: border-box;
        }
        .bn-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }
        .bn-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .bn-title-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #fff;
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.4);
        }
        .bn-create-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 999px;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: #fff;
            background: #cfe09b; 
            cursor: pointer;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .bn-create-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.45);
        }
        .bn-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }

        /* --- Table --- */
        .bn-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .bn-table thead {
            background: linear-gradient(90deg, #f9fafb, #eef2ff);
        }
        .bn-table th {
            padding: 9px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
            text-align: left;
        }
        .bn-table th.text-right { text-align: right; }
        .bn-table td {
            padding: 10px 16px;
            font-size: 0.78rem;
            color: #0f172a;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .bn-table td.text-right { text-align: right; }
        .bn-table tbody tr:last-child td { border-bottom: none; }
        .bn-table tbody tr:hover { background: #f9fafb; }

        /* --- Badges --- */
        .bn-badge-type {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .bn-type-info { background: #e0f2fe; color: #0369a1; }
        .bn-type-warning { background: #fef3c7; color: #92400e; }
        .bn-type-danger { background: #fee2e2; color: #b91c1c; }
        .bn-type-success { background: #dcfce7; color: #166534; }

        .bn-status-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .bn-status-dot {
            width: 6px; height: 6px; border-radius: 999px; margin-right: 6px;
        }
        .bn-status-active { background: #ecfdf5; color: #93c21c; }
        .bn-status-active .bn-status-dot { background: #93c21c; }
        .bn-status-inactive { background: #f1f5f9; color: #64748b; }
        .bn-status-inactive .bn-status-dot { background: #94a3b8; }

        /* --- Actions --- */
        .bn-actions {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            justify-content: flex-end;
        }
        .bn-action-btn {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.7rem; padding: 4px 8px;
            border-radius: 999px; border: 1px solid #e2e8f0;
            background: #ffffff; color: #334155; cursor: pointer;
            transition: all 0.12s ease;
        }
        .bn-action-btn:hover { background: #f8fafc; border-color: #cbd5f5; transform: translateY(-0.5px); }
        .bn-action-btn-danger { border-color: #fecaca; color: #e50656; }
        .bn-action-btn-danger:hover { background: #fef2f2; border-color: #e50656; }
        .bn-empty-cell { padding: 28px 16px; text-align: center; font-size: 0.8rem; color: #9ca3af; }

        /* --- Audio WAV pill --- */
        .bn-audio-pill {
            margin-top: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 999px;
            border: none;
            background: #0f172a;
            color: #e5e7eb;
            font-size: 0.7rem;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.35);
            transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
        }
        .bn-audio-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.45);
            background: #020617;
        }
        .bn-audio-pill.is-playing {
            background: #111827;
        }
        .bn-audio-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 30%, #22c55e, #16a34a);
        }
        .bn-audio-label {
            font-size: 0.7rem;
            opacity: 0.9;
        }
        .bn-audio-wave {
            display: inline-flex;
            align-items: flex-end;
            gap: 2px;
            height: 14px;
        }
        .bn-audio-wave span {
            width: 2px;
            border-radius: 999px;
            background: #e5e7eb;
            opacity: 0.65;
            transform-origin: bottom;
            transform: scaleY(0.3);
        }
        .bn-audio-pill.is-playing .bn-audio-wave span:nth-child(1) { animation: bn-wave 0.35s ease-in-out infinite alternate; }
        .bn-audio-pill.is-playing .bn-audio-wave span:nth-child(2) { animation: bn-wave 0.42s ease-in-out infinite alternate; }
        .bn-audio-pill.is-playing .bn-audio-wave span:nth-child(3) { animation: bn-wave 0.38s ease-in-out infinite alternate; }
        .bn-audio-pill.is-playing .bn-audio-wave span:nth-child(4) { animation: bn-wave 0.45s ease-in-out infinite alternate; }
        .bn-audio-pill.is-playing .bn-audio-wave span:nth-child(5) { animation: bn-wave 0.4s ease-in-out infinite alternate; }

        /* --- MODAL STYLES --- */
        #news-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 99999;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }
        #news-modal.flex {
            display: flex !important;
        }
        .bn-modal-card {
            background: #fff;
            width: 90%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(148, 163, 184, 0.4);
            padding: 24px;
            position: relative;
            margin: 20px;
        }
        .bn-modal-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .bn-modal-title {
            font-size: 1.1rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 10px;
        }
        .bn-modal-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%; background: #eef2ff; color: #93c21c;
        }
        .bn-modal-close {
            background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.2rem;
        }
        .bn-modal-close:hover { color: #475569; }

        /* --- Form Elements --- */
        .bn-form-group { margin-bottom: 16px; }
        .bn-field-label {
            display: block; font-size: 0.75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 6px;
        }
        .bn-input, .bn-select, .bn-textarea {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            color: #1e293b;
            outline: none;
            box-sizing: border-box;
        }
        .bn-input:focus, .bn-select:focus, .bn-textarea:focus {
            border-color: #93c21c;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .bn-error-msg {
            color: #e11d48; font-size: 0.75rem; margin-top: 4px; display: none;
        }
        .bn-error-msg.visible { display: block; }

        .bn-grid-row { display: flex; gap: 12px; }
        .bn-col { flex: 1; }
        .bn-checkbox-wrapper {
            display: flex; align-items: center; height: 100%; padding-top: 24px;
        }
        .bn-checkbox-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.85rem; font-weight: 500; color: #334155; cursor: pointer;
        }

        .bn-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            margin-top: 8px;
            display: flex; justify-content: flex-end; gap: 8px;
        }
        .bn-btn-cancel {
            padding: 8px 16px; border-radius: 8px; border: 1px solid #e2e8f0;
            background: #fff; color: #475569; font-weight: 500; cursor: pointer; font-size: 0.85rem;
        }
        .bn-btn-cancel:hover { background: #f8fafc; }
        .bn-btn-save {
            padding: 8px 16px; border-radius: 8px; border: none;
            background: #93c21c; color: #fff; font-weight: 500; cursor: pointer;
            font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;
        }
        .bn-btn-save:hover { background: #93c21c; }
        .bn-btn-save:disabled { opacity: 0.7; cursor: not-allowed; }

        .hidden { display: none !important; }

        @media (max-width: 600px) {
            .bn-grid-row { flex-direction: column; gap: 12px; }
            .bn-checkbox-wrapper { padding-top: 0; margin-bottom: 12px; }
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        .animate-spin { animation: spin 1s linear infinite; }

        @keyframes bn-wave {
            from { transform: scaleY(0.3); opacity: 0.6; }
            to   { transform: scaleY(1);   opacity: 1; }
        }
    </style>

    <style>
    /* --- Audio recorder in modal --- */
    .bn-rec-wrapper {
        margin-top: 8px;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .bn-rec-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #475569;
    }
    .bn-rec-header span {
        opacity: 0.9;
    }
    .bn-rec-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bn-rec-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        border: none;
        font-size: 0.75rem;
        cursor: pointer;
        background: #0f172a;
        color: #e5e7eb;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }
    .bn-rec-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.25);
        background: #020617;
    }
    .bn-rec-btn[disabled] {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }
    .bn-rec-btn-primary {
        background: #ef4444;
        color: #fef2f2;
    }
    .bn-rec-btn-primary:hover {
        background: #b91c1c;
    }
    .bn-rec-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
    }
    .bn-rec-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #ef4444;
        animation: bn-rec-pulse 1s ease-in-out infinite;
    }
    @keyframes bn-rec-pulse {
        0%   { transform: scale(1);   opacity: 0.6; }
        50%  { transform: scale(1.4); opacity: 1;   }
        100% { transform: scale(1);   opacity: 0.6; }
    }
    .bn-rec-timer {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
    }
    .bn-rec-preview {
        margin-top: 6px;
        font-size: 0.75rem;
        color: #475569;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .bn-rec-preview audio {
        width: 100%;
    }
</style>

@endsection

@section('content')
<div class="breaking-news-page">
    <div class="bn-header">
        <h1 class="bn-title">
            <span class="bn-title-icon">
                <i data-feather="zap"></i>
            </span>
            <span>Breaking News</span>
        </h1>
        <button id="btn-open-create" class="bn-create-btn">
            <i data-feather="plus"></i>
            <span>Neue Meldung</span>
        </button>
    </div>

    <div class="bn-card">
        <div style="overflow-x: auto;">
            <table class="bn-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Titel</th>
                        <th>Typ</th>
                        <th>Zeitraum</th>
                        <th>Status</th>
                        <th>Erstellt von</th>
                        <th class="text-right">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="news-table-body">
                    @forelse($breakingNews as $news)
                        @php
    $audioUrl = $news->audio_path
        ? asset('storage/' . $news->audio_path)
        : ($news->audio_url ?? null);
                        @endphp
                        <tr data-id="{{ $news->id }}"
                            data-json='@json($news->toArray() + ["audio_url" => $audioUrl])'>
                            <td>
                                @if($news->icon)
                                    <i data-feather="{{ $news->icon }}"></i>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:0.8rem;font-weight:600;color:#0f172a;">
                                    {{ $news->title }}
                                </div>
                                <div style="font-size:0.72rem;color:#6b7280;margin-top:2px;">
                                    {{ \Illuminate\Support\Str::limit($news->message, 80) }}
                                </div>

                                @if($audioUrl)
                                    <button type="button"
                                            class="bn-audio-pill"
                                            data-audio-url="{{ $audioUrl }}">
                                        <span class="bn-audio-icon">
                                            <i data-feather="volume-2"></i>
                                        </span>
                                        <span class="bn-audio-label">Audio abspielen</span>
                                        <div class="bn-audio-wave" aria-hidden="true">
                                            <span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </button>
                                @endif
                            </td>
                            <td>
                                @php
    $typeClass = match ($news->type) {
        'info' => 'bn-type-info',
        'warning' => 'bn-type-warning',
        'danger' => 'bn-type-danger',
        'success' => 'bn-type-success',
        default => 'bn-type-info',
    };
                                @endphp
                                <span class="bn-badge-type {{ $typeClass }}">
                                    {{ ucfirst($news->type) }}
                                </span>
                            </td>
                            <td style="font-size:0.72rem;color:#4b5563;">
                                @if($news->starts_at)
                                    <div>Von: {{ $news->starts_at->format('d.m.Y H:i') }}</div>
                                @endif
                                @if($news->ends_at)
                                    <div>Bis: {{ $news->ends_at->format('d.m.Y H:i') }}</div>
                                @else
                                    <span style="color:#9ca3af;">kein Ende</span>
                                @endif
                            </td>
                            <td>
                                @if($news->is_active)
                                    <span class="bn-status-pill bn-status-active">
                                        <span class="bn-status-dot"></span>Aktiv
                                    </span>
                                @else
                                    <span class="bn-status-pill bn-status-inactive">
                                        <span class="bn-status-dot"></span>Inaktiv
                                    </span>
                                @endif
                            </td>
                            <td style="font-size:0.72rem;color:#6b7280;">
                                {{ optional($news->creator)->name ?? 'System' }}
                            </td>
                            <td class="text-right">
                                <div class="bn-actions">
                                    <button class="bn-action-btn btn-edit" type="button">
                                        <i data-feather="edit-3" style="width:12px;height:12px;"></i>
                                        Edit
                                    </button>
                                    <button class="bn-action-btn btn-toggle" type="button">
                                        <i data-feather="power" style="width:12px;height:12px;"></i>
                                        @if($news->is_active) Deaktiv @else Aktiv @endif
                                    </button>
                                    <button class="bn-action-btn bn-action-btn-danger btn-delete" type="button">
                                        <i data-feather="trash-2" style="width:12px;height:12px;"></i>
                                        Löschen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="7" class="bn-empty-cell">
                                Noch keine Breaking News vorhanden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="news-modal">
    <div class="bn-modal-card">
        <div class="bn-modal-header">
            <h2 class="bn-modal-title" id="news-modal-title">
                <span class="bn-modal-icon">
                    <i data-feather="alert-circle"></i>
                </span>
                <span>Meldung</span>
            </h2>
            <button id="news-modal-close" class="bn-modal-close" type="button">
                <i data-feather="x"></i>
            </button>
        </div>

        <form id="news-form" enctype="multipart/form-data">
            <input type="hidden" id="news-id">

            <div class="bn-form-group">
                <label class="bn-field-label">Titel</label>
                <input type="text" id="news-title" class="bn-input">
                <p class="bn-error-msg" data-error="title"></p>
            </div>

            <div class="bn-form-group">
                <label class="bn-field-label">Nachricht</label>
                <textarea id="news-message" rows="3" class="bn-textarea"></textarea>
                <p class="bn-error-msg" data-error="message"></p>
            </div>

            <div class="bn-form-group">
                <div class="bn-grid-row">
                    <div class="bn-col">
                        <label class="bn-field-label">Typ</label>
                        <select id="news-type" class="bn-select">
                            <option value="info">Info</option>
                            <option value="warning">Warnung</option>
                            <option value="danger">Alarm</option>
                            <option value="success">Hinweis</option>
                        </select>
                        <p class="bn-error-msg" data-error="type"></p>
                    </div>
                    <div class="bn-col">
                        <label class="bn-field-label">Icon (Feather)</label>
                        <select id="news-icon" class="bn-select">
                            <option value="">Kein</option>
                            <option value="alert-triangle">alert-triangle</option>
                            <option value="cloud-rain">cloud-rain</option>
                            <option value="info">info</option>
                            <option value="bell">bell</option>
                            <option value="megaphone">megaphone</option>
                        </select>
                        <p class="bn-error-msg" data-error="icon"></p>
                    </div>
                    <div class="bn-col" style="flex: 0 0 auto;">
                        <div class="bn-checkbox-wrapper">
                            <label class="bn-checkbox-label">
                                <input type="checkbox" id="news-is-active" checked>
                                Aktiv
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bn-form-group">
                <div class="bn-grid-row">
                    <div class="bn-col">
                        <label class="bn-field-label">Start</label>
                        <input type="datetime-local" id="news-starts-at" class="bn-input">
                        <p class="bn-error-msg" data-error="starts_at"></p>
                    </div>
                    <div class="bn-col">
                        <label class="bn-field-label">Ende (Auto-Deaktivierung)</label>
                        <input type="datetime-local" id="news-ends-at" class="bn-input">
                        <p class="bn-error-msg" data-error="ends_at"></p>
                    </div>
                </div>
            </div>

                {{-- Audio upload + recorder --}}
            <div class="bn-form-group">
                <label class="bn-field-label">Audio (optional)</label>
                <input type="file" id="news-audio" class="bn-input" accept="audio/*">
                <p class="bn-error-msg" data-error="audio"></p>

                <div class="bn-rec-wrapper">
                    <div class="bn-rec-header">
                        <span>Mikrofon aufnehmen</span>
                        <span class="bn-rec-timer" id="bn-rec-timer">00:00</span>
                    </div>
                    <div class="bn-rec-controls">
                        <button type="button"
                                class="bn-rec-btn bn-rec-btn-primary"
                                id="bn-rec-start">
                            <i data-feather="mic"></i>
                            <span>Aufnahme starten</span>
                        </button>
                        <button type="button"
                                class="bn-rec-btn"
                                id="bn-rec-stop"
                                disabled>
                            <i data-feather="square"></i>
                            <span>Stopp</span>
                        </button>
                        <button type="button"
                                class="bn-rec-btn"
                                id="bn-rec-reset"
                                disabled>
                            <i data-feather="trash-2"></i>
                            <span>Löschen</span>
                        </button>

                        <div class="bn-rec-indicator" id="bn-rec-indicator" style="display:none;">
                            <span class="bn-rec-dot"></span>
                            <span>Aufnahme läuft…</span>
                        </div>
                    </div>

                    <div class="bn-rec-preview" id="bn-rec-preview" style="display:none;">
                        <span>Vorschau der Aufnahme:</span>
                        <audio id="bn-rec-audio-preview" controls></audio>
                    </div>
                </div>
            </div>


            <div class="bn-footer">
                <button type="button" id="news-cancel" class="bn-btn-cancel">
                    Abbrechen
                </button>
                <button type="submit" id="news-save" class="bn-btn-save">
                    <span id="news-save-label">Speichern</span>
                    <span id="news-save-spinner" class="hidden">
                        <i data-feather="loader" class="animate-spin" style="width: 14px; height: 14px;"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Shared audio player for table rows --}}
<audio id="bn-row-audio-player" style="display:none;"></audio>
@endsection

@section('script')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();

            const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const modal      = document.getElementById('news-modal');
            const form       = document.getElementById('news-form');
            const btnOpen    = document.getElementById('btn-open-create');
            const btnClose   = document.getElementById('news-modal-close');
            const btnCancel  = document.getElementById('news-cancel');
            const tableBody  = document.getElementById('news-table-body');
            const titleSpan  = document.querySelector('#news-modal-title span:last-child');
            const inputId    = document.getElementById('news-id');
            const inputTitle = document.getElementById('news-title');
            const textareaMsg= document.getElementById('news-message');
            const selectType = document.getElementById('news-type');
            const selectIcon = document.getElementById('news-icon');
            const chkActive  = document.getElementById('news-is-active');
            const inputStart = document.getElementById('news-starts-at');
            const inputEnd   = document.getElementById('news-ends-at');
            const inputAudio = document.getElementById('news-audio');
            // Recorder UI
            const recStartBtn       = document.getElementById('bn-rec-start');
            const recStopBtn        = document.getElementById('bn-rec-stop');
            const recResetBtn       = document.getElementById('bn-rec-reset');
            const recTimerEl        = document.getElementById('bn-rec-timer');
            const recIndicatorEl    = document.getElementById('bn-rec-indicator');
            const recPreviewWrapper = document.getElementById('bn-rec-preview');
            const recPreviewAudio   = document.getElementById('bn-rec-audio-preview');

            // Recorder state
            let mediaRecorder   = null;
            let recStream       = null;
            let recChunks       = [];
            let recTimer        = null;
            let recSeconds      = 0;
            let recordedBlob    = null;
            let recordedFile    = null;

            const saveBtn        = document.getElementById('news-save');
            const saveLabel      = document.getElementById('news-save-label');
            const saveSpinner    = document.getElementById('news-save-spinner');

            const storeUrl    = '{{ route('breaking-news.store') }}';
            const updateUrlTpl = '{{ route('breaking-news.update', ['breakingNews' => '__ID__']) }}';
            const toggleUrlTpl = '{{ route('breaking-news.toggle', ['breakingNews' => '__ID__']) }}';
            const deleteUrlTpl = '{{ route('breaking-news.destroy', ['breakingNews' => '__ID__']) }}';

            // Shared audio player for row WAV pills
            const rowAudioPlayer = document.getElementById('bn-row-audio-player');
            let currentAudioBtn = null;

            function clearAudioState() {
                if (currentAudioBtn) {
                    currentAudioBtn.classList.remove('is-playing');
                }
                currentAudioBtn = null;
                if (rowAudioPlayer) {
                    rowAudioPlayer.pause();
                    rowAudioPlayer.removeAttribute('src');
                }
            }

            if (rowAudioPlayer) {
                rowAudioPlayer.addEventListener('ended', clearAudioState);
                rowAudioPlayer.addEventListener('pause', function () {
                    if (currentAudioBtn && rowAudioPlayer.currentTime > 0 && !rowAudioPlayer.ended) {
                        currentAudioBtn.classList.remove('is-playing');
                    }
                });
                rowAudioPlayer.addEventListener('play', function () {
                    if (currentAudioBtn) {
                        currentAudioBtn.classList.add('is-playing');
                    }
                });
            }

            if (recStartBtn) {
                recStartBtn.addEventListener('click', () => {
                    startRecording();
                });
            }

            if (recStopBtn) {
                recStopBtn.addEventListener('click', () => {
                    stopRecording();
                });
            }

            if (recResetBtn) {
                recResetBtn.addEventListener('click', () => {
                    resetRecordingState();
                    // Also clear any selected file in input
                    if (inputAudio) {
                        inputAudio.value = '';
                        inputAudio.disabled = false;
                    }
                });
            }


            function handleAudioPillClick(btn) {
                if (!rowAudioPlayer || !btn) return;
                const src = btn.getAttribute('data-audio-url');
                if (!src) return;

                // If click same button -> toggle play/pause
                if (currentAudioBtn === btn) {
                    if (rowAudioPlayer.paused) {
                        rowAudioPlayer.play();
                    } else {
                        rowAudioPlayer.pause();
                    }
                    return;
                }

                // Switch to new button
                clearAudioState();
                currentAudioBtn = btn;
                rowAudioPlayer.src = src;
                rowAudioPlayer.play().catch(() => {
                    // play blocked by browser
                });
            }

            function clearErrors() {
                document.querySelectorAll('[data-error]').forEach(function (el) {
                    el.classList.remove('visible');
                    el.textContent = '';
                });
            }

            function setLoading(isLoading) {
                if (!saveBtn || !saveLabel || !saveSpinner) return;
                if (isLoading) {
                    saveSpinner.classList.remove('hidden');
                    saveLabel.textContent = 'Speichern...';
                    saveBtn.disabled = true;
                } else {
                    saveSpinner.classList.add('hidden');
                    saveLabel.textContent = 'Speichern';
                    saveBtn.disabled = false;
                }
            }
            function formatRecTime(sec) {
                    const m = Math.floor(sec / 60);
                    const s = Math.floor(sec % 60);
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }

                function resetRecordingState() {
                    // Stop timer
                    if (recTimer) {
                        clearInterval(recTimer);
                        recTimer = null;
                    }
                    recSeconds = 0;
                    if (recTimerEl) {
                        recTimerEl.textContent = '00:00';
                    }

                    // Stop recorder & stream
                    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                        try { mediaRecorder.stop(); } catch (e) {}
                    }
                    mediaRecorder = null;

                    if (recStream) {
                        recStream.getTracks().forEach(t => t.stop());
                        recStream = null;
                    }

                    recChunks = [];
                    recordedBlob = null;
                    recordedFile = null;

                    // Reset UI
                    if (recIndicatorEl) recIndicatorEl.style.display = 'none';
                    if (recPreviewWrapper) recPreviewWrapper.style.display = 'none';
                    if (recPreviewAudio) {
                        recPreviewAudio.pause();
                        recPreviewAudio.removeAttribute('src');
                    }

                    if (recStartBtn) {
                        recStartBtn.disabled = false;
                    }
                    if (recStopBtn) {
                        recStopBtn.disabled = true;
                    }
                    if (recResetBtn) {
                        recResetBtn.disabled = true;
                    }

                    // Re-enable regular file input
                    if (inputAudio) {
                        inputAudio.disabled = false;
                    }
                }

                function startRecording() {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Mikrofonaufnahme wird von diesem Browser nicht unterstützt.');
                        return;
                    }

                    navigator.mediaDevices.getUserMedia({ audio: true })
                        .then(stream => {
                            recStream = stream;
                            mediaRecorder = new MediaRecorder(stream);

                            recChunks = [];
                            recSeconds = 0;
                            if (recTimerEl) {
                                recTimerEl.textContent = '00:00';
                            }

                            // UI state
                            if (recStartBtn) recStartBtn.disabled = true;
                            if (recStopBtn)  recStopBtn.disabled  = false;
                            if (recResetBtn) recResetBtn.disabled = true;
                            if (inputAudio)  inputAudio.disabled  = true;
                            if (recIndicatorEl) recIndicatorEl.style.display = 'inline-flex';
                            if (recPreviewWrapper) recPreviewWrapper.style.display = 'none';

                            // Timer
                            recTimer = setInterval(() => {
                                recSeconds += 1;
                                if (recTimerEl) {
                                    recTimerEl.textContent = formatRecTime(recSeconds);
                                }
                            }, 1000);

                            mediaRecorder.ondataavailable = e => {
                                if (e.data && e.data.size > 0) {
                                    recChunks.push(e.data);
                                }
                            };

                            mediaRecorder.onstop = () => {
                                if (recChunks.length) {
                                    recordedBlob = new Blob(recChunks, { type: 'audio/webm' });
                                    recordedFile = new File([recordedBlob], 'breaking-news-recording.webm', {
                                        type: 'audio/webm'
                                    });

                                    // Preview
                                    if (recPreviewAudio) {
                                        const url = URL.createObjectURL(recordedBlob);
                                        recPreviewAudio.src = url;
                                        recPreviewAudio.load();
                                    }
                                    if (recPreviewWrapper) {
                                        recPreviewWrapper.style.display = 'flex';
                                    }

                                    // Enable reset
                                    if (recResetBtn) recResetBtn.disabled = false;
                                }

                                // Stop timer + indicator
                                if (recTimer) {
                                    clearInterval(recTimer);
                                    recTimer = null;
                                }
                                if (recIndicatorEl) recIndicatorEl.style.display = 'none';

                                // Allow "start" again (to override current recording)
                                if (recStartBtn) recStartBtn.disabled = false;

                                // Stop stream
                                if (recStream) {
                                    recStream.getTracks().forEach(t => t.stop());
                                    recStream = null;
                                }
                            };

                            mediaRecorder.start();
                        })
                        .catch(err => {
                            console.error('getUserMedia error', err);
                            alert('Zugriff auf das Mikrofon wurde verweigert oder ist nicht möglich.');
                        });
                }

                function stopRecording() {
                    if (!mediaRecorder || mediaRecorder.state !== 'recording') return;
                    mediaRecorder.stop();

                    // UI while stopping
                    if (recStopBtn)  recStopBtn.disabled = true;
                    if (recIndicatorEl) recIndicatorEl.style.display = 'none';

                    // Timer will be stopped in onstop
                }



            function openModal(mode, data) {
                clearErrors();
                if (form) form.reset();
                if (inputId) inputId.value = '';
                resetRecordingState();
                if (mode === 'edit' && data) {
                    if (titleSpan) titleSpan.textContent = 'Meldung bearbeiten';
                    if (inputId)    inputId.value = data.id || '';
                    if (inputTitle) inputTitle.value = data.title || '';
                    if (textareaMsg)textareaMsg.value = data.message || '';
                    if (selectType) selectType.value = data.type || 'info';
                    if (selectIcon) selectIcon.value = data.icon || '';
                    if (chkActive)  chkActive.checked = !!data.is_active;

                    if (inputStart && data.starts_at) {
                        inputStart.value = String(data.starts_at).replace(' ', 'T').substring(0, 16);
                    }
                    if (inputEnd && data.ends_at) {
                        inputEnd.value = String(data.ends_at).replace(' ', 'T').substring(0, 16);
                    }
                    // file input cannot be prefilled for security reasons
                    if (inputAudio) inputAudio.value = '';
                } else {
                    if (titleSpan) titleSpan.textContent = 'Neue Meldung';
                    if (chkActive) chkActive.checked = true;
                    if (inputAudio) inputAudio.value = '';
                }

                if (modal) {
                    modal.classList.add('flex');
                }
                feather.replace();
            }

            function closeModal() {
                 resetRecordingState();
                if (modal) {
                    modal.classList.remove('flex');
                }
            }

            function parseRowJson(row) {
                if (!row) return null;
                const json = row.getAttribute('data-json');
                if (!json) return null;
                try {
                    return JSON.parse(json);
                } catch (e) {
                    return null;
                }
            }

            function renderRow(news) {
                const typeClasses = {
                    info: 'bn-type-info',
                    warning: 'bn-type-warning',
                    danger: 'bn-type-danger',
                    success: 'bn-type-success',
                };
                const typeClass = typeClasses[news.type] || 'bn-type-info';

                const startsAt = news.starts_at ? (news.starts_at_formatted || news.starts_at) : null;
                const endsAt   = news.ends_at   ? (news.ends_at_formatted   || news.ends_at)   : null;

                const activeBadge = news.is_active
                    ? `<span class="bn-status-pill bn-status-active">
                            <span class="bn-status-dot"></span>Aktiv
                       </span>`
                    : `<span class="bn-status-pill bn-status-inactive">
                            <span class="bn-status-dot"></span>Inaktiv
                       </span>`;

                const creatorName = news.creator && news.creator.name ? news.creator.name : 'System';
                const fullMsg = news.message || '';
                const shortMsg = fullMsg.length > 80 ? fullMsg.substring(0, 77) + '…' : fullMsg;

                const audioUrl = news.audio_url || '';
                const audioHtml = audioUrl
                    ? `
                        <button type="button"
                                class="bn-audio-pill"
                                data-audio-url="${audioUrl}">
                            <span class="bn-audio-icon">
                                <i data-feather="volume-2"></i>
                            </span>
                            <span class="bn-audio-label">Audio abspielen</span>
                            <div class="bn-audio-wave" aria-hidden="true">
                                <span></span><span></span><span></span><span></span><span></span>
                            </div>
                        </button>
                      `
                    : '';

                // Escape single quotes in JSON for attribute
                const jsonAttr = JSON.stringify(news).replace(/'/g, '&#39;');

                return `
                    <tr data-id="${news.id}" data-json='${jsonAttr}'>
                        <td>
                            ${news.icon ? `<i data-feather="${news.icon}"></i>` : ''}
                        </td>
                        <td>
                            <div style="font-size:0.8rem;font-weight:600;color:#0f172a;">
                                ${news.title || ''}
                            </div>
                            <div style="font-size:0.72rem;color:#6b7280;margin-top:2px;">
                                ${shortMsg}
                            </div>
                            ${audioHtml}
                        </td>
                        <td>
                            <span class="bn-badge-type ${typeClass}">
                                ${news.type ? news.type.charAt(0).toUpperCase() + news.type.slice(1) : ''}
                            </span>
                        </td>
                        <td style="font-size:0.72rem;color:#4b5563;">
                            ${startsAt ? `<div>Von: ${startsAt}</div>` : ''}
                            ${endsAt ? `<div>Bis: ${endsAt}</div>` : '<span style="color:#9ca3af;">kein Ende</span>'}
                        </td>
                        <td>
                            ${activeBadge}
                        </td>
                        <td style="font-size:0.72rem;color:#6b7280;">
                            ${creatorName}
                        </td>
                        <td class="text-right">
                            <div class="bn-actions">
                                <button type="button" class="bn-action-btn btn-edit">
                                    <i data-feather="edit-3" style="width:12px;height:12px;"></i>
                                    Edit
                                </button>
                                <button type="button" class="bn-action-btn btn-toggle">
                                    <i data-feather="power" style="width:12px;height:12px;"></i>
                                    ${news.is_active ? 'Deaktiv' : 'Aktiv'}
                                </button>
                                <button type="button" class="bn-action-btn bn-action-btn-danger btn-delete">
                                    <i data-feather="trash-2" style="width:12px;height:12px;"></i>
                                    Löschen
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }

            async function ajax(url, options) {
                const defaultHeaders = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                };
                if (csrfToken) {
                    defaultHeaders['X-CSRF-TOKEN'] = csrfToken;
                }
                const finalOptions = Object.assign({}, options || {}, {
                    headers: Object.assign({}, defaultHeaders, (options && options.headers) || {}),
                });
                const resp = await fetch(url, finalOptions);
                const data = await resp.json().catch(() => null);
                if (!resp.ok) {
                    const err = new Error('HTTP error ' + resp.status);
                    err.response = resp;
                    err.data = data;
                    throw err;
                }
                return data;
            }

            // Open modal (create)
            if (btnOpen) {
                btnOpen.addEventListener('click', function () {
                    openModal('create');
                });
            }

            // Close modal via X or Cancel
            [btnClose, btnCancel].forEach(function (btn) {
                if (btn) {
                    btn.addEventListener('click', function () {
                        closeModal();
                    });
                }
            });

            // Close when clicking backdrop
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            // Submit form (create + update)
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    clearErrors();
                    setLoading(true);

                    const id = inputId ? inputId.value : '';
                    const isEdit = !!id;
                    const url = isEdit
                        ? updateUrlTpl.replace('__ID__', id)
                        : storeUrl;

                    const formData = new FormData();
                    formData.append('title',     inputTitle ? inputTitle.value : '');
                    formData.append('message',   textareaMsg ? textareaMsg.value : '');
                    formData.append('type',      selectType ? selectType.value : 'info');
                    formData.append('icon',      selectIcon ? selectIcon.value : '');
                    formData.append('starts_at', inputStart ? inputStart.value : '');
                    formData.append('ends_at',   inputEnd ? inputEnd.value : '');
                    formData.append('is_active', chkActive && chkActive.checked ? '1' : '0');

                    // Prefer recorded audio if available, otherwise uploaded file
                    if (recordedFile) {
                        formData.append('audio', recordedFile);
                    } else if (inputAudio && inputAudio.files && inputAudio.files[0]) {
                        formData.append('audio', inputAudio.files[0]);
                    }


                    if (isEdit) {
                        formData.append('_method', 'PUT');
                    }

                    ajax(url, {
                        method: 'POST',
                        body: formData,
                    })
                    .then(function (res) {
                        setLoading(false);
                        if (!res || !res.success) return;

                        const news = res.data;
                        news.starts_at_formatted = news.starts_at ? (news.starts_at_formatted || news.starts_at) : null;
                        news.ends_at_formatted   = news.ends_at   ? (news.ends_at_formatted   || news.ends_at)   : null;

                        // audio_url should be returned from backend; keep if present
                        const rowHtml = renderRow(news);

                        if (!tableBody) return;

                        if (isEdit) {
                            const existingRow = tableBody.querySelector('tr[data-id="' + id + '"]');
                            if (existingRow) {
                                existingRow.outerHTML = rowHtml;
                            }
                        } else {
                            const emptyRow = document.getElementById('empty-row');
                            if (emptyRow) emptyRow.remove();
                            tableBody.insertAdjacentHTML('afterbegin', rowHtml);
                        }

                        feather.replace();
                        closeModal();
                    })
                    .catch(function (err) {
                        setLoading(false);
                        if (err.data && err.data.errors) {
                            const errors = err.data.errors;
                            Object.keys(errors).forEach(function (field) {
                                const msg = errors[field][0];
                                const el = document.querySelector('[data-error="' + field + '"]');
                                if (el) {
                                    el.classList.add('visible');
                                    el.textContent = msg;
                                }
                            });
                        } else {
                            alert('Fehler beim Speichern der Meldung.');
                        }
                    });
                });
            }

            // Delegated table click handlers: audio, edit, toggle, delete
            if (tableBody) {
                tableBody.addEventListener('click', function (e) {
                    const audioBtn = e.target.closest('.bn-audio-pill');
                    if (audioBtn) {
                        handleAudioPillClick(audioBtn);
                        return;
                    }

                    const btn = e.target.closest('button');
                    if (!btn) return;

                    const row = btn.closest('tr');
                    if (!row) return;
                    const id = row.getAttribute('data-id');
                    const data = parseRowJson(row);

                    // Edit
                    if (btn.classList.contains('btn-edit')) {
                        openModal('edit', data);
                        return;
                    }

                    // Toggle active
                    if (btn.classList.contains('btn-toggle')) {
                        const url = toggleUrlTpl.replace('__ID__', id);
                        ajax(url, { method: 'POST' })
                            .then(function (res) {
                                if (!res || !res.success) return;
                                const updated = data || {};
                                updated.is_active = res.is_active;
                                row.setAttribute('data-json', JSON.stringify(updated).replace(/'/g, '&#39;'));
                                const newHtml = renderRow(updated);
                                row.outerHTML = newHtml;
                                feather.replace();
                            })
                            .catch(function () {
                                alert('Fehler beim Ändern des Status.');
                            });
                        return;
                    }

                    // Delete
                    if (btn.classList.contains('btn-delete')) {
                        if (!confirm('Diese Meldung wirklich löschen?')) return;

                        const url = deleteUrlTpl.replace('__ID__', id);
                        const formData = new FormData();
                        formData.append('_method', 'DELETE');

                        ajax(url, {
                            method: 'POST',
                            body: formData,
                        })
                        .then(function (res) {
                            if (!res || !res.success) return;
                            row.remove();
                            if (tableBody.querySelectorAll('tr').length === 0) {
                                tableBody.innerHTML = `
                                    <tr id="empty-row">
                                        <td colspan="7" class="bn-empty-cell">
                                            Noch keine Breaking News vorhanden.
                                        </td>
                                    </tr>
                                `;
                            }
                        })
                        .catch(function () {
                            alert('Fehler beim Löschen.');
                        });
                    }
                });
            }
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
                label: 'Breaking News',
                url: "{{ url()->current() }}",
                clickable: false
            }, 
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush