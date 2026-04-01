{{-- resources/views/admin/todo/personal/profile.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Aufgabe – ' . ($task->task_title ?? 'Ohne Titel'))

@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<style>
    .tp-shell {
        display: grid;
        grid-template-columns: minmax(280px, 340px) 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .tp-shell {
            grid-template-columns: 1fr;
        }
    }

    .tp-card {
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 16px 40px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
        padding: 1.1rem 1.25rem;
    }

    .tp-card h3 {
        margin: 0 0 .5rem;
        font-size: 1rem;
    }

    .tp-kv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .85rem;
    }
    .tp-kv-table tr td:first-child {
        width: 40%;
        font-weight: 600;
        color: #4b5563;
        padding: .25rem .25rem .25rem 0;
        vertical-align: top;
    }
    .tp-kv-table tr td:last-child {
        padding: .25rem 0 .25rem .25rem;
        color: #111827;
    }

    .tp-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .15rem .5rem;
        border-radius: 999px;
        background: #f3f4f6;
        font-size: .75rem;
        color: #374151;
        white-space: nowrap;
    }
    .tp-pill-strong {
        background: #d1fae5;
        color: #065f46;
        font-weight: 600;
    }
    .tp-avatar-ring {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 999px;
        overflow: hidden;
        background: #e5e7eb;
        font-size: .75rem;
        color: #111827;
    }
    .tp-avatar-ring + .tp-avatar-ring {
        margin-left: -10px;
        border: 2px solid #fff;
    }

    .tp-tabs {
        display: inline-flex;
        padding: .2rem;
        border-radius: 999px;
        background: #f3f4f6;
        margin-bottom: 1rem;
    }
    .tp-tabs button {
        border: none;
        background: transparent;
        border-radius: 999px;
        padding: .35rem .9rem;
        font-size: .8rem;
        cursor: pointer;
        color: #4b5563;
    }
    .tp-tabs button.is-active {
        background: #111827;
        color: #f9fafb;
    }

    .tp-keys-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }
    .tp-keys-table th,
    .tp-keys-table td {
        padding: .35rem .4rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .tp-keys-table th {
        font-weight: 600;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #6b7280;
    }
    .tp-key-done {
        text-decoration: line-through;
        color: #6b7280;
    }
    .tp-badge-status {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .1rem .45rem;
        border-radius: 999px;
        font-size: .7rem;
        background: #eef2ff;
        color: #3730a3;
    }

    .tp-history-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .tp-history-item {
        display: grid;
        grid-template-columns: 90px 1fr;
        gap: .6rem;
        font-size: .8rem;
        padding: .4rem .2rem;
        border-bottom: 1px dashed #e5e7eb;
    }
    .tp-history-item time {
        color: #6b7280;
        font-size: .75rem;
    }
    .tp-history-title {
        font-weight: 600;
        margin-bottom: .1rem;
    }
    .tp-history-meta {
        font-size: .75rem;
        color: #4b5563;
    }

    .tp-comment {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: .6rem .7rem;
        margin-bottom: .5rem;
        background: #f9fafb;
    }
    .tp-comment-header {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .25rem;
    }
    .tp-comment-name {
        font-size: .8rem;
        font-weight: 600;
        color: #111827;
    }
    .tp-comment-time {
        font-size: .7rem;
        color: #6b7280;
    }
    .tp-comment-body {
        font-size: .8rem;
        color: #111827;
        margin-top: .2rem;
    }
    .tp-comment-actions {
        margin-top: .25rem;
        font-size: .75rem;
        color: #4b5563;
        cursor: pointer;
    }
    .tp-comment-replies {
        margin-left: 1.5rem;
        margin-top: .3rem;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row mb-1">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="content-header-title mb-0">
                        Aufgabe: {{ $task->task_title ?? 'Ohne Titel' }}
                    </h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('personal-tasks.index', ['tab' => 'my']) }}">Aufgaben</a>
                        </li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('personal-tasks.index', ['tab' => 'my']) }}"
                       class="btn btn-outline-secondary btn-sm mr-50">
                        Zurück
                    </a>
                    <a href="{{ route('personal.task.edit', $task->id) }}"
                       class="btn btn-sm btn-primary">
                        <i data-feather="edit" class="mr-25" style="width:14px;height:14px;"></i>
                        Bearbeiten
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="tp-shell">

                {{-- LEFT COLUMN: META + PEOPLE + ATTACHMENTS --}}
                <div class="tp-left-column">

                    {{-- Meta --}}
                    <div class="tp-card mb-1">
                        <h3>Überblick</h3>
                        <table class="tp-kv-table">
                            <tr>
                                <td>ID</td>
                                <td>#{{ $task->id }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <span class="tp-pill tp-pill-strong">
                                        {{ $task->task_status ?? 'offen' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Priorität</td>
                                <td>
                                    <span class="tp-pill">
                                        <i data-feather="flag" style="width:12px;height:12px;"></i>
                                        {{ $task->priority ?? 'Normal' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Öffentlich / Privat</td>
                                <td>
                                    @if($task->public)
                                        <span class="tp-pill">
                                            <i data-feather="unlock" style="width:12px;height:12px;"></i>
                                            Öffentlich
                                        </span>
                                    @else
                                        <span class="tp-pill">
                                            <i data-feather="lock" style="width:12px;height:12px;"></i>
                                            Privat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Erstellt von</td>
                                <td>
                                    @if($task->assignedBy)
                                        <div style="display:flex;align-items:center;gap:.4rem;">
                                            <div class="tp-avatar-ring">
                                                @if($task->assignedBy->image)
                                                    <img src="{{ asset('images/employee/'.$task->assignedBy->image) }}"
                                                         style="width:100%;height:100%;object-fit:cover;">
                                                @else
                                                    {{ mb_substr($task->assignedBy->name,0,1) }}{{ mb_substr($task->assignedBy->lastname,0,1) }}
                                                @endif
                                            </div>
                                            <div style="font-size:.8rem;">
                                                {{ $task->assignedBy->name }} {{ $task->assignedBy->lastname }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Erstellt am</td>
                                <td>
                                    {{ $task->created_at?->format('d.m.Y H:i') ?? '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Fällig</td>
                                <td>
                                    @if($task->due_date)
                                        {{ $task->due_date->format('d.m.Y') }}
                                        @if($task->due_time)
                                            {{ $task->due_time }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Geplante Dauer</td>
                                <td>
                                    @if($task->total_time)
                                        {{ $task->total_time }} Std.
                                        @if($task->total_day)
                                            ({{ $task->total_day }} Tage)
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Customer --}}
                    <div class="tp-card mb-1">
                        <h3>Kunde / Auftrag</h3>
                        @if($task->customer)
                            <div style="font-size:.85rem;">
                                <div><strong>{{ $task->customer->customer_no }}</strong></div>
                                <div>
                                    {{ $task->customer->lastname }} {{ $task->customer->name }}
                                </div>
                                <div style="color:#6b7280;">
                                    {{ $task->customer->postcode }} {{ $task->customer->city }}
                                </div>
                            </div>
                        @else
                            <div style="font-size:.8rem;color:#9ca3af;">Kein Kunde verknüpft</div>
                        @endif
                    </div>

                    @php
                        // Current employee (Employee ID)
                        $currentEmployeeId = $employeeId ?? (int) (auth()->user()->name ?? 0);

                        // Creator as Employee ID (assigned_by is stored as employee.id as string)
                        $creatorId = (int) ($task->assigned_by ?? 0);

                        // Controller IDs as array
                        $controllerIds = $controllerEmployees
                            ? $controllerEmployees->pluck('id')->map(fn($id) => (int) $id)->all()
                            : [];

                        // Allowed to manage team if: creator OR one of the controllers
                        $canManageTeam = $currentEmployeeId > 0 && (
                            $currentEmployeeId === $creatorId ||
                            in_array($currentEmployeeId, $controllerIds, true)
                        );
                    @endphp

                    {{-- People --}}
                    <div class="tp-card mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-25">
                            <h3 class="mb-0">Team</h3>

                            @if($canManageTeam)
                                <button type="button"
                                        class="btn btn-xs btn-outline-secondary"
                                        data-toggle="modal"
                                        data-target="#tp-modal-team">
                                    <i class="feather icon-edit"></i>
                                </button>
                            @endif
                        </div>


                        {{-- Controllers (Verantwortliche, global) --}}
                        <div style="font-size:.8rem;margin-bottom:.35rem;">
                            Verantwortliche
                            <button type="button"
                                    class="btn btn-link btn-sm p-0 ml-25 align-baseline"
                                    data-toggle="modal"
                                    data-target="#tp-modal-controllers">
                                ändern
                            </button>
                        </div>
                        @if($controllerEmployees->count())
                            <div>
                                @foreach($controllerEmployees as $emp)
                                    <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                        @if($emp->image)
                                            <img src="{{ asset('images/employee/'.$emp->image) }}"
                                                style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ mb_substr($emp->name,0,1) }}{{ mb_substr($emp->lastname,0,1) }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-size:.8rem;color:#9ca3af;">Kein Kontroller definiert</div>
                        @endif

                        {{-- Global employees for the whole task --}}
                        <div style="font-size:.8rem;margin:.75rem 0 .35rem;">
                            Mitarbeiter (gesamte Aufgabe)
                        </div>
                        @if($task->employees->count())
                            <div>
                                @foreach($task->employees as $emp)
                                    <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                        @if($emp->image)
                                            <img src="{{ asset('images/employee/'.$emp->image) }}"
                                                style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ mb_substr($emp->name,0,1) }}{{ mb_substr($emp->lastname,0,1) }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-size:.8rem;color:#9ca3af;">Keine Mitarbeiter zugewiesen</div>
                        @endif

                        {{-- Employees per key --}}
                        <div style="font-size:.8rem;margin:.75rem 0 .35rem;">
                            Mitarbeiter nach Schritt
                        </div>
                        <div style="font-size:.78rem;">
                            @forelse($keys as $key)
                                @php
                                    $assignedIds = (array) ($key->employee_id ?? []);
                                    if (is_string($key->employee_id) && $key->employee_id !== '') {
                                        $assignedIds = json_decode($key->employee_id, true) ?: [];
                                    }
                                    $assignedEmps = $assignedIds
                                        ? \App\Models\Employee::whereIn('id', $assignedIds)->get()
                                        : collect();
                                @endphp

                                <div style="margin-bottom:.35rem;">
                                    <div style="font-weight:600;">
                                        #{{ $loop->iteration }} – {{ $key->task ?? 'Schritt ohne Titel' }}
                                    </div>
                                    @if($assignedEmps->count())
                                        <div style="margin-top:.15rem;">
                                            @foreach($assignedEmps as $emp)
                                                <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                                    @if($emp->image)
                                                        <img src="{{ asset('images/employee/'.$emp->image) }}"
                                                            style="width:100%;height:100%;object-fit:cover;">
                                                    @else
                                                        {{ mb_substr($emp->name,0,1) }}{{ mb_substr($emp->lastname,0,1) }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div style="font-size:.75rem;color:#9ca3af;margin-top:.1rem;">
                                            Keine Mitarbeiter für diesen Schritt zugewiesen
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div style="font-size:.8rem;color:#9ca3af;">Keine Aufgabenschritte definiert.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Modal: Controllers (Verantwortliche) --}}
                    <div class="modal fade" id="tp-modal-controllers" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Verantwortliche für diese Aufgabe</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="tp-form-controllers">
                                        @csrf
                                        <div class="form-group">
                                            <label for="tp-controllers-select">Mitarbeiter auswählen</label>
                                            <select id="tp-controllers-select"
                                                    name="controllers[]"
                                                    class="form-control"
                                                    multiple
                                                    style="width:100%;">
                                                @php
                                                    $currentControllers = $controllerEmployees->pluck('id')->all();
                                                @endphp
                                                @foreach($allEmployees as $emp)
                                                    <option value="{{ $emp->id }}"
                                                            data-image="{{ $emp->image ? asset('images/employee/'.$emp->image) : '' }}"
                                                            @if(in_array($emp->id, $currentControllers)) selected @endif>
                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Diese Mitarbeiter sind für die gesamte Aufgabe verantwortlich.
                                            </small>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary btn-sm"
                                            data-dismiss="modal">
                                        Abbrechen
                                    </button>
                                    <button type="button"
                                            id="tp-controllers-save"
                                            class="btn btn-primary btn-sm">
                                        Speichern
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal: Team (Mitarbeiter für Aufgabe / Schritte) --}}
                    <div class="modal fade" id="tp-modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Team-Mitglieder hinzufügen</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="tp-form-team">
                                        @csrf

                                        {{-- Scope: whole task vs specific keys --}}
                                        <div class="form-group">
                                            <label class="d-block mb-50">Zuweisungsbereich</label>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio"
                                                    class="custom-control-input"
                                                    id="tp-scope-task"
                                                    name="scope"
                                                    value="task"
                                                    checked>
                                                <label class="custom-control-label" for="tp-scope-task">
                                                    gesamte Aufgabe
                                                </label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio"
                                                    class="custom-control-input"
                                                    id="tp-scope-keys"
                                                    name="scope"
                                                    value="keys">
                                                <label class="custom-control-label" for="tp-scope-keys">
                                                    ausgewählte Schritte
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Employees --}}
                                        <div class="form-group">
                                            <label for="tp-employees-select">Mitarbeiter auswählen</label>
                                            <select id="tp-employees-select"
                                                    name="employee_ids[]"
                                                    class="form-control"
                                                    multiple
                                                    style="width:100%;">
                                                @foreach($allEmployees as $emp)
                                                    <option value="{{ $emp->id }}"
                                                            data-image="{{ $emp->image ? asset('images/employee/'.$emp->image) : '' }}">
                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Diese Mitarbeiter werden der Aufgabe oder den gewählten Schritten hinzugefügt.
                                            </small>
                                        </div>

                                        {{-- Keys (only shown when scope = keys) --}}
                                        <div class="form-group" id="tp-keys-wrapper" style="display:none;">
                                            <label for="tp-keys-select">Aufgabenschritte auswählen</label>
                                            <select id="tp-keys-select"
                                                    name="key_ids[]"
                                                    class="form-control"
                                                    multiple
                                                    style="width:100%;">
                                                @foreach($keys as $key)
                                                    <option value="{{ $key->id }}">
                                                        #{{ $loop->iteration }} – {{ $key->task ?? 'Schritt ohne Titel' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Die Mitarbeiter werden nur diesen Schritten zugewiesen.
                                            </small>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary btn-sm"
                                            data-dismiss="modal">
                                        Abbrechen
                                    </button>
                                    <button type="button"
                                            id="tp-team-save"
                                            class="btn btn-primary btn-sm">
                                        Speichern
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Attachments (only list – upload kannst du aus alter Logik übernehmen) --}}
                     @php
                        $attachmentData = $task->attachments->map(function ($file) {
                            return [
                                'id'         => $file->id,
                                'image_name' => $file->image_name,
                                'file_type'  => $file->file_type,
                                'url'        => asset('images/task/personal/document/'.$file->image),
                            ];
                        })->values();
                    @endphp

                    <div class="tp-card">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                            <h3 class="mb-0">Anhänge</h3>

                            <div style="display:flex;gap:.35rem;align-items:center;">
                                <input type="text"
                                    id="tp-attach-search"
                                    placeholder="Suche..."
                                    style="font-size:.8rem;padding:.2rem .45rem;border-radius:999px;border:1px solid #e5e7eb;min-width:140px;">
                                <button type="button"
                                        id="tp-attach-upload-btn"
                                        style="font-size:.8rem;border-radius:999px;border:none;padding:.3rem .8rem;background:#e5f3d0;color:#111827;cursor:pointer;">
                                    Datei wählen
                                </button>
                            </div>
                        </div>

                        {{-- hidden input for manual select --}}
                        <input type="file" id="tp-attach-file-input" multiple style="display:none;">

                        {{-- drag and drop area --}}
                        <div id="tp-attach-dropzone"
                            style="border:1px dashed #d1d5db;border-radius:.75rem;padding:.6rem .75rem;font-size:.8rem;text-align:center;margin-bottom:.6rem;background:#f9fafb;">
                            Dateien hierher ziehen oder klicken, um hochzuladen.
                        </div>

                        {{-- list --}}
                        @if($task->attachments->count())
                            <ul id="tp-attach-list"
                                style="list-style:none;margin:0;padding:0;font-size:.8rem;">
                                @foreach($task->attachments as $file)
                                    <li class="tp-attach-item"
                                        data-index="{{ $loop->index }}"
                                        data-id="{{ $file->id }}"
                                        data-name="{{ $file->image_name }}"
                                        style="display:flex;justify-content:space-between;align-items:center;padding:.25rem 0;border-bottom:1px solid #f3f4f6;cursor:pointer;">
                                        <div style="display:flex;align-items:center;gap:.25rem;">
                                            <i data-feather="file" style="width:14px;height:14px;"></i>
                                            <span>{{ $file->image_name }}</span>
                                            <span style="color:#9ca3af;">({{ $file->file_type }})</span>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:.25rem;">
                                            <button type="button"
                                                    class="tp-attach-open"
                                                    style="border:none;background:none;font-size:.75rem;color:#2563eb;cursor:pointer;">
                                                Öffnen
                                            </button>
                                            <button type="button"
                                                    class="tp-attach-delete"
                                                    style="border:none;background:none;font-size:.75rem;color:#b91c1c;cursor:pointer;">
                                                Löschen
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div id="tp-attach-empty"
                                style="font-size:.8rem;color:#9ca3af;">
                                Keine Dateien hochgeladen
                            </div>
                            <ul id="tp-attach-list"
                                style="list-style:none;margin:0;padding:0;font-size:.8rem;display:none;"></ul>
                        @endif
                    </div>

                    {{-- Viewer modal --}}
                    <div id="tp-attach-modal-backdrop"
                        style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:9999;align-items:center;justify-content:center;">
                        <div id="tp-attach-modal"
                            style="background:white;border-radius:1rem;max-width:900px;width:96%;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,.35);">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .9rem;border-bottom:1px solid #e5e7eb;">
                                <div id="tp-attach-modal-title"
                                    style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    Datei
                                </div>
                                <div style="display:flex;align-items:center;gap:.35rem;">
                                    <button type="button"
                                            id="tp-attach-prev"
                                            style="border:none;background:none;font-size:1rem;cursor:pointer;">
                                        ‹
                                    </button>
                                    <button type="button"
                                            id="tp-attach-next"
                                            style="border:none;background:none;font-size:1rem;cursor:pointer;">
                                        ›
                                    </button>
                                    <button type="button"
                                            id="tp-attach-modal-close"
                                            style="border:none;background:none;font-size:1.1rem;cursor:pointer;">
                                        ✕
                                    </button>
                                </div>
                            </div>
                            <div id="tp-attach-modal-body"
                                style="flex:1;overflow:auto;background:#080b10;display:flex;align-items:center;justify-content:center;padding:1rem;">
                                {{-- content injected via JS --}}
                            </div>
                            <div id="tp-attach-modal-meta"
                                style="padding:.5rem .9rem;font-size:.75rem;color:#6b7280;border-top:1px solid #111827;">
                            </div>
                        </div>
                    </div>

                    {{-- JS data --}}
                    <script>
                        window.tpAttachments = @json($attachmentData);
                        window.tpAttachmentsStoreUrl   = "{{ route('personal-tasks.attachments.store', $task->id) }}";
                        window.tpAttachmentDeleteRoute = "{{ route('personal-tasks.attachments.destroy', 0) }}"; // id will be replaced in JS
                    </script>


                </div>

                {{-- RIGHT COLUMN: DESCRIPTION + TABS --}}
                <div class="tp-right-column">
                    <div class="tp-card mb-1">
                        <h3>Beschreibung</h3>
                        <div style="font-size:.85rem;color:#111827;">
                            {{ $task->description ?: 'Keine Beschreibung hinterlegt.' }}
                        </div>
                    </div>

                    <div class="tp-card">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="tp-tabs" id="tp-tabs">
                                <button type="button" data-tab="steps" class="is-active">Schritte</button>
                                <button type="button" data-tab="history">Verlauf</button>
                                <button type="button" data-tab="reports">Berichte</button>
                                <button type="button" data-tab="notifications">Benachrichtigung</button>
                            </div>
                        </div>

                        {{-- TABS CONTENT --}}
                        <div id="tp-tab-steps" class="tp-tab-panel">
                            @include('admin.todo.personal.profile_steps', ['task' => $task, 'keys' => $keys])
                        </div>

                        <div id="tp-tab-history" class="tp-tab-panel" style="display:none;">
                            @include('admin.todo.personal.profile_history', ['history' => $history])
                        </div>

                        <div id="tp-tab-reports" class="tp-tab-panel" style="display:none;">
                            @include('admin.todo.personal.profile_reports', [
                                'task'     => $task,
                                'comments' => $comments,
                                'employeeId' => $employeeId,
                            ])
                        </div>

                        <div id="tp-tab-notifications" class="tp-tab-panel" style="display:none;">
                             <div class="tp-card" id="tp-notification-card" data-task-id="{{ $task->id }}">
                                <h3>Benachrichtigungen</h3>

                                <ul id="tp-notifications-list"
                                    class="activity-timeline timeline-left list-unstyled"
                                    style="margin:0;padding:0;font-size:.8rem;">
                                    <li>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold">Lade Benachrichtigungen...</p>
                                        </div>
                                    </li>
                                </ul>
                            </div> 
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script') 
<script>

     window.currentEmployeeId = {{ (int) ($employeeId ?? 0) }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Tabs
    (function() {
        const tabs = document.querySelectorAll('#tp-tabs button');
        const panels = {
            steps:   document.getElementById('tp-tab-steps'),
            history: document.getElementById('tp-tab-history'),
            reports: document.getElementById('tp-tab-reports'),
        };

        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                tabs.forEach(b => b.classList.toggle('is-active', b === btn));
                Object.keys(panels).forEach(key => {
                    panels[key].style.display = (key === tab) ? 'block' : 'none';
                });
            });
        });
    })();
 
    // Toggle key (complete / undo) with SweetAlert, done_status + work_progress
   // Toggle key (complete / undo) with SweetAlert, done_status + work_progress
        document.addEventListener('click', function (e) {
            const row = e.target.closest('.js-key-toggle-row');
            if (!row) return;

            const toggleBtn = e.target.closest('.js-key-toggle');
            if (!toggleBtn) return;

            const keyId  = row.dataset.keyId;
            const isDone = row.dataset.completed === '1';

            // -------------------------
            // 1) Check if current user is assigned to this key
            // -------------------------
            const meIdRaw = window.currentEmployeeId || 0;
            const meId    = parseInt(meIdRaw, 10);

            let assignedIds = [];
            if (row.dataset.employeeIds) {
                try {
                    assignedIds = JSON.parse(row.dataset.employeeIds);
                } catch (err) {
                    console.warn('Invalid employeeIds on key row', err, row.dataset.employeeIds);
                }
            }

            const isMember = meId > 0 && Array.isArray(assignedIds)
                ? assignedIds.map(Number).includes(meId)
                : false;

            if (!isMember) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nicht dein Schritt',
                    text: 'Du bist diesem Aufgabenschritt nicht zugeordnet und kannst ihn nicht bearbeiten.',
                });
                return;
            }

            // -------------------------
            // 2) Existing logic: undo or complete / partial
            // -------------------------

            // --- UNDO COMPLETED STEP ---
            if (isDone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Schritt zurücksetzen?',
                    text: 'Dieser Aufgabenschritt wird wieder als offen markiert.',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, zurücksetzen',
                    cancelButtonText: 'Abbrechen',
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch("{{ url('/personal-task-keys') }}/" + keyId + "/toggle", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ mode: 'undo' }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: 'Der Schritt konnte nicht zurückgesetzt werden.',
                            });
                            return;
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Zurückgesetzt',
                            timer: 1200,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        console.error('Key toggle undo error', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Beim Speichern ist ein Fehler aufgetreten.',
                        });
                    });
                });

                return;
            }

            // --- COMPLETE / PARTIAL STEP ---
            Swal.fire({
                title: 'Aufgabenschritt aktualisieren',
                html: `
                    <div style="text-align:left;margin-bottom:.5rem;">
                        <label for="swal-done-status" style="font-size:.8rem;">Status</label>
                        <select id="swal-done-status" class="swal2-select" style="width:100%;">
                            <option value="complete">Vollständig erledigt</option>
                            <option value="part">Teilweise erledigt</option>
                        </select>
                    </div>
                    <div id="swal-progress-wrap" style="text-align:left;margin-bottom:.5rem;display:none;">
                        <label for="swal-progress" style="font-size:.8rem;">Fortschritt in %</label>
                        <input id="swal-progress"
                            type="number"
                            class="swal2-input"
                            value="50"
                            min="1"
                            max="99"
                            style="width:100%;box-sizing:border-box;">
                    </div>
                    <div style="text-align:left;">
                        <label for="swal-submit_time" style="font-size:.8rem;">Istzeit (Stunden, z.B. 1.5)</label>
                        <input id="swal-submit_time"
                            type="number"
                            class="swal2-input"
                            value="1"
                            min="0"
                            step="0.25"
                            style="width:100%;box-sizing:border-box;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                focusConfirm: false,
                didOpen: () => {
                    const statusSel    = document.getElementById('swal-done-status');
                    const progressWrap = document.getElementById('swal-progress-wrap');

                    const updateVisibility = () => {
                        progressWrap.style.display =
                            statusSel.value === 'part' ? 'block' : 'none';
                    };

                    statusSel.addEventListener('change', updateVisibility);
                    updateVisibility();
                },
                preConfirm: () => {
                    const statusSel   = document.getElementById('swal-done-status');
                    const submitInput = document.getElementById('swal-submit_time');
                    const progInput   = document.getElementById('swal-progress');

                    const doneStatus  = statusSel.value;
                    const submitTime  = parseFloat(submitInput.value);

                    if (isNaN(submitTime) || submitTime < 0) {
                        Swal.showValidationMessage('Bitte eine gültige Istzeit angeben (>= 0).');
                        return false;
                    }

                    let workProgress = 100;
                    if (doneStatus === 'part') {
                        workProgress = parseInt(progInput.value, 10);
                        if (isNaN(workProgress) || workProgress <= 0 || workProgress >= 100) {
                            Swal.showValidationMessage('Bitte Fortschritt zwischen 1 und 99 % eingeben.');
                            return false;
                        }
                    }

                    return {
                        done_status: doneStatus,
                        work_progress: workProgress,
                        submit_time: submitTime,
                    };
                },
            }).then(result => {
                if (!result.isConfirmed || !result.value) return;

                const payload = {
                    mode: 'complete',
                    done_status:   result.value.done_status,
                    work_progress: result.value.work_progress,
                    submit_time:   result.value.submit_time,
                };

                fetch("{{ url('/personal-task-keys') }}/" + keyId + "/toggle", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                })
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Der Aufgabenschritt konnte nicht aktualisiert werden.',
                        });
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Gespeichert',
                        timer: 1200,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                })
                .catch(err => {
                    console.error('Key toggle save error', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: 'Beim Speichern ist ein Fehler aufgetreten.',
                    });
                });
            });
        });

    // Comments (reports)
    (function() {
        const form = document.getElementById('tp-report-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const textarea = form.querySelector('textarea[name="comment"]');
            const val = textarea.value.trim();
            if (!val) return;

            fetch("{{ route('personal-tasks.comments.store', $task->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ comment: val }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    alert('Kommentar konnte nicht gespeichert werden.');
                    return;
                }
                location.reload();
            })
            .catch(err => {
                console.error('comment save error', err);
            });
        });

        // reply
        document.addEventListener('click', function(e) {
            const replyBtn = e.target.closest('.js-reply-toggle');
            if (replyBtn) {
                const wrap = replyBtn.closest('.tp-comment');
                const replyForm = wrap.querySelector('.tp-reply-form');
                if (replyForm) {
                    replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
                }
            }

            const replySend = e.target.closest('.js-reply-send');
            if (!replySend) return;

            const formEl   = replySend.closest('form');
            const commentId= formEl.dataset.commentId;
            const textarea = formEl.querySelector('textarea[name="comment"]');
            const val      = textarea.value.trim();
            if (!val) return;

            fetch("{{ url('/personal-tasks/comments') }}/" + commentId + "/reply", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ comment: val }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    alert('Antwort konnte nicht gespeichert werden.');
                    return;
                }
                location.reload();
            })
            .catch(err => {
                console.error('reply save error', err);
            });
        });
    })();

    if (window.feather) {
        feather.replace();
    } 

    const controllersUrl   = "{{ route('personal-tasks.team.controllers', $task->id) }}";
    const employeesTaskUrl = "{{ route('personal-tasks.team.employees', $task->id) }}";
    const employeesKeysUrl = "{{ route('personal-tasks.team.employees-keys', $task->id) }}";

    // (existing code for tabs, key toggle, comments ... keep it)

    // -------------------------------
    // Team – scope toggle (task / keys)
    // -------------------------------
    (function() {
        const scopeRadios  = document.querySelectorAll('input[name="scope"]');
        const keysWrapper  = document.getElementById('tp-keys-wrapper');

        if (!scopeRadios.length || !keysWrapper) return;

        function refreshScope() {
            const scope = document.querySelector('input[name="scope"]:checked')?.value || 'task';
            keysWrapper.style.display = scope === 'keys' ? 'block' : 'none';
        }

        scopeRadios.forEach(r => r.addEventListener('change', refreshScope));
        refreshScope();
    })();

    $('#tp-controllers-select, #tp-employees-select, #tp-keys-select').select2({
        width: '100%'
    });


   // -------------------------------
    // Save controllers
    // -------------------------------
    (function() {
        const canManageTeam = @json($canManageTeam); // <--- HERE

        const btn = document.getElementById('tp-controllers-save');
        if (!btn) return;

        btn.addEventListener('click', () => {
            if (!canManageTeam) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Berechtigung',
                    text: 'Sie dürfen die Verantwortlichen für diese Aufgabe nicht ändern.',
                });
                return;
            }

            const select = document.getElementById('tp-controllers-select');
            if (!select) return;

            const values = Array.from(select.options)
                .filter(o => o.selected)
                .map(o => parseInt(o.value, 10))
                .filter(v => !isNaN(v));

            fetch(controllersUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ controllers: values }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    alert('Verantwortliche konnten nicht aktualisiert werden.');
                    return;
                }
                $('#tp-modal-controllers').modal('hide');
                location.reload();
            })
            .catch(err => {
                console.error('controllers save error', err);
                alert('Fehler beim Speichern der Verantwortlichen.');
            });
        });
    })();

    // -------------------------------
    // Save team (employees)
    // -------------------------------
    (function() {
        const canManageTeam = @json($canManageTeam); // <--- AND HERE

        const btn = document.getElementById('tp-team-save');
        if (!btn) return;

        btn.addEventListener('click', () => {
            if (!canManageTeam) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Berechtigung',
                    text: 'Sie dürfen das Team für diese Aufgabe nicht ändern.',
                });
                return;
            }

            const form          = document.getElementById('tp-form-team');
            const scopeInput    = form.querySelector('input[name="scope"]:checked');
            const employeesSel  = document.getElementById('tp-employees-select');
            const keysSel       = document.getElementById('tp-keys-select');

            const scope         = scopeInput ? scopeInput.value : 'task';

            const employeeIds = Array.from(employeesSel.options)
                .filter(o => o.selected)
                .map(o => parseInt(o.value, 10))
                .filter(v => !isNaN(v));

            if (!employeeIds.length) {
                alert('Bitte mindestens einen Mitarbeiter wählen.');
                return;
            }

            let url     = employeesTaskUrl;
            let payload = { employee_ids: employeeIds };

            if (scope === 'keys') {
                const keyIds = Array.from(keysSel.options)
                    .filter(o => o.selected)
                    .map(o => parseInt(o.value, 10))
                    .filter(v => !isNaN(v));

                if (!keyIds.length) {
                    alert('Bitte mindestens einen Aufgabenschritt wählen.');
                    return;
                }

                url = employeesKeysUrl;
                payload.key_ids = keyIds;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    alert('Team konnte nicht aktualisiert werden.');
                    return;
                }
                $('#tp-modal-team').modal('hide');
                location.reload();
            })
            .catch(err => {
                console.error('team save error', err);
                alert('Fehler beim Speichern des Teams.');
            });
        });
    })();
    if (window.feather) {
        feather.replace();
    }


    (function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const attachments      = window.tpAttachments || [];
        const storeUrl         = window.tpAttachmentsStoreUrl;
        const deleteRouteProto = window.tpAttachmentDeleteRoute; // ends with /0

        const fileInput  = document.getElementById('tp-attach-file-input');
        const uploadBtn  = document.getElementById('tp-attach-upload-btn');
        const dropzone   = document.getElementById('tp-attach-dropzone');
        const searchInput= document.getElementById('tp-attach-search');
        const listEl     = document.getElementById('tp-attach-list');
        const emptyEl    = document.getElementById('tp-attach-empty');

        const modalBackdrop = document.getElementById('tp-attach-modal-backdrop');
        const modalBody     = document.getElementById('tp-attach-modal-body');
        const modalMeta     = document.getElementById('tp-attach-modal-meta');
        const modalTitle    = document.getElementById('tp-attach-modal-title');
        const btnClose      = document.getElementById('tp-attach-modal-close');
        const btnPrev       = document.getElementById('tp-attach-prev');
        const btnNext       = document.getElementById('tp-attach-next');

        let currentIndex = null;

        function reloadPage() {
            window.location.reload();
        }

        // -----------------------
        // Upload helpers
        // -----------------------
        function uploadFiles(files) {
            if (!files || !files.length) return;

            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    alert('Upload fehlgeschlagen.');
                    return;
                }
                // simplest: reload to sync list + JS data
                reloadPage();
            })
            .catch(err => {
                console.error('upload error', err);
                alert('Fehler beim Hochladen.');
            });
        }

        // Click → file input
        if (uploadBtn && fileInput) {
            uploadBtn.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', e => {
                if (!e.target.files.length) return;
                uploadFiles(e.target.files);
            });
        }

        // Drag & drop
        if (dropzone) {
            ['dragenter','dragover'].forEach(ev => {
                dropzone.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#eef3ff';
                    dropzone.style.borderColor = '#93c5fd';
                });
            });

            ['dragleave','drop'].forEach(ev => {
                dropzone.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#f9fafb';
                    dropzone.style.borderColor = '#d1d5db';
                });
            });

            dropzone.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                uploadFiles(files);
            });

            // click on dropzone = open file dialog
            dropzone.addEventListener('click', () => {
                if (fileInput) fileInput.click();
            });
        }

        // -----------------------
        // Search
        // -----------------------
        if (searchInput && listEl) {
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.toLowerCase();
                const items = listEl.querySelectorAll('.tp-attach-item');

                items.forEach(li => {
                    const name = li.dataset.name ? li.dataset.name.toLowerCase() : '';
                    const text = li.textContent.toLowerCase();
                    const match = !q || name.includes(q) || text.includes(q);
                    li.style.display = match ? 'flex' : 'none';
                });
            });
        }

        // -----------------------
        // Viewer
        // -----------------------
        function openViewer(index) {
            if (!attachments.length) return;
            if (index < 0 || index >= attachments.length) return;

            currentIndex = index;
            const att = attachments[index];

            modalTitle.textContent = att.image_name || 'Datei';
            modalMeta.textContent  = att.file_type ? `Typ: ${att.file_type}` : '';

            const ext = (att.file_type || '').toLowerCase();
            let innerHtml = '';

            if (['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext)) {
                innerHtml = `<img src="${att.url}"
                                   style="max-width:100%;max-height:80vh;object-fit:contain;border-radius:.5rem;">`;
            } else if (ext === 'pdf') {
                innerHtml = `<iframe src="${att.url}"
                                      style="width:100%;height:80vh;border:none;border-radius:.5rem;background:white;"></iframe>`;
            } else {
                innerHtml = `
                    <div style="color:white;text-align:center;font-size:.9rem;">
                        <p>Dieser Dateityp kann hier nicht direkt angezeigt werden.</p>
                        <p><a href="${att.url}" target="_blank" style="color:#93c5fd;">Im neuen Tab öffnen</a></p>
                    </div>`;
            }

            modalBody.innerHTML = innerHtml;
            modalBackdrop.style.display = 'flex';
        }

        function closeViewer() {
            modalBackdrop.style.display = 'none';
            currentIndex = null;
        }

        if (btnClose) {
            btnClose.addEventListener('click', closeViewer);
        }
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', e => {
                if (e.target === modalBackdrop) {
                    closeViewer();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', e => {
                e.stopPropagation();
                if (currentIndex === null || !attachments.length) return;
                const nextIndex = (currentIndex - 1 + attachments.length) % attachments.length;
                openViewer(nextIndex);
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', e => {
                e.stopPropagation();
                if (currentIndex === null || !attachments.length) return;
                const nextIndex = (currentIndex + 1) % attachments.length;
                openViewer(nextIndex);
            });
        }

        if (listEl) {
            listEl.addEventListener('click', e => {
                const openBtn = e.target.closest('.tp-attach-open');
                const deleteBtn = e.target.closest('.tp-attach-delete');
                const item = e.target.closest('.tp-attach-item');

                if (!item) return;

                const index = parseInt(item.dataset.index, 10);

                if (deleteBtn) {
                    const id = item.dataset.id;
                    if (!id) return;

                    if (!confirm('Anhang wirklich löschen?')) return;

                    const deleteUrl = deleteRouteProto.replace(/0$/, id);

                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.success) {
                            alert('Löschen fehlgeschlagen.');
                            return;
                        }
                        reloadPage();
                    })
                    .catch(err => {
                        console.error('delete error', err);
                        alert('Fehler beim Löschen.');
                    });

                    return;
                }

                // click anywhere on item or "Öffnen"
                if (openBtn || e.target.closest('.tp-attach-item')) {
                    openViewer(index);
                }
            });
        }

        if (window.feather) {
            feather.replace();
        }
    })();
</script>

<script>
    // Tabs
    (function() {
        const tabs = document.querySelectorAll('#tp-tabs button');
        const panels = {
            steps:          document.getElementById('tp-tab-steps'),
            history:        document.getElementById('tp-tab-history'),
            reports:        document.getElementById('tp-tab-reports'),
            notifications:  document.getElementById('tp-tab-notifications'),
        };

        function showTab(tabKey) {
            // toggle button state
            tabs.forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.tab === tabKey);
            });

            // toggle panels
            Object.keys(panels).forEach(key => {
                if (!panels[key]) return;
                panels[key].style.display = (key === tabKey) ? 'block' : 'none';
            });
        }

        // click handler
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                showTab(tab);

                // when notifications tab is opened first time, trigger load
                if (tab === 'notifications') {
                    if (window.fetchTaskNotificationsOnce) {
                        window.fetchTaskNotificationsOnce();
                    }
                }
            });
        });

        // initial tab (fallback: first button or "steps")
        const activeBtn = document.querySelector('#tp-tabs button.is-active') || tabs[0];
        const initial   = activeBtn ? activeBtn.dataset.tab : 'steps';
        showTab(initial);
    })();
</script>

<script>
    (function () {
        const $card   = $('#tp-notification-card');
        const $list   = $('#tp-notifications-list');
        const baseUrl = "{{ url('/notifications/task') }}";

        if (!$card.length || !$list.length) {
            console.warn('Notification elements not found in DOM.');
            return;
        }

        const taskId = $card.data('task-id');
        if (!taskId) {
            console.error('Task ID fehlt für Benachrichtigungen.');
            return;
        }

        let loaded = false;

        function fetchTaskNotifications(id) {
            $.ajax({
                url : baseUrl + '/' + id,
                type: 'GET',
                success: function (response) {
                    console.log('Notifications received:', response);

                    $list.empty();

                    const items = (response && response.data) ? response.data : [];

                    if (items.length === 0) {
                        $list.append(`
                            <li>
                                <div class="timeline-info">
                                    <p class="font-weight-bold">Keine Benachrichtigungen</p>
                                </div>
                            </li>
                        `);
                        return;
                    }

                    items.forEach(function (notification) {
                        const title       = notification.title   || 'Benachrichtigung';
                        const message     = notification.message || 'Keine Details verfügbar.';
                        const performedAt = notification.performed_at
                            ? new Date(notification.performed_at).toLocaleString()
                            : '';

                        $list.append(`
                            <li style="margin-bottom:.6rem;">
                                <div class="timeline-icon bg-primary">
                                    <i class="feather icon-bell font-medium-2"></i>
                                </div>
                                <div class="timeline-info">
                                    <p class="font-weight-bold">${title}</p>
                                    <span>${message}</span>
                                </div>
                                <small>${performedAt}</small>
                            </li>
                        `);
                    });
                },
                error: function (xhr) {
                    console.error('Error fetching notifications:', xhr);

                    $list.empty().append(`
                        <li>
                            <div class="timeline-info">
                                <p class="font-weight-bold text-danger">
                                    Benachrichtigungen konnten nicht geladen werden.
                                </p>
                            </div>
                        </li>
                    `);

                    if (window.Swal) {
                        Swal.fire({
                            icon : 'error',
                            title: 'Fehler',
                            text : 'Benachrichtigungen konnten nicht geladen werden. Bitte erneut versuchen.',
                        });
                    }
                }
            });
        }

        // expose "load once" function for the tabs script
        window.fetchTaskNotificationsOnce = function () {
            if (loaded) return;
            loaded = true;
            fetchTaskNotifications(taskId);
        };

        // Optional: if notifications tab is initially active, load immediately
        const activeBtn = document.querySelector('#tp-tabs button.is-active');
        if (activeBtn && activeBtn.dataset.tab === 'notifications') {
            window.fetchTaskNotificationsOnce();
        }
    })();
</script>

@endsection
