{{-- resources/views/admin/todo/personal/_task_card.blade.php --}}

@push('style')
  <style>
    /* ============================
       ACTION BUTTONS (BOARD CARD)
       ============================ */

    /* IMPORTANT: your base CSS has .pt-card-actions { gap:.25rem; }
       keep but make it stronger */
    .pt-card-actions {
      display: flex;
      align-items: center;
      gap: .45rem !important;
      flex-wrap: wrap;
    }

    /* Your base CSS sets background #f9fafb and small padding.
       Here we force visible size + contrast. */
    .pt-icon-btn {
      width: 34px;
      height: 34px;
      padding: 0 !important;
      border: 1px solid #e5e7eb;
      background: #ffffff !important;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #0f172a;
      transition: background .12s ease, transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }

    .pt-icon-btn:hover {
      background: #cfe09b !important;
      border-color: #93c21c !important;
      transform: translateY(-1px);
      box-shadow: 0 10px 22px rgba(15, 23, 42, .12);
    }

    /* Force SVG visibility (some themes set svg to width:0 or currentColor mismatch) */
    .pt-icon-btn svg {
      width: 18px !important;
      height: 18px !important;
      display: block !important;
      stroke: currentColor !important;
      fill: none !important;
      stroke-width: 2.2 !important;
      stroke-linecap: round !important;
      stroke-linejoin: round !important;
    }

    /* If any icon uses fill (play triangle), allow fill */
    .pt-icon-btn svg.is-fill {
      fill: currentColor !important;
      stroke: none !important;
    }

    /* Selected state (single click) */
    .pt-card.is-selected {
      outline: 2px solid #93c21c;
      box-shadow: 0 0 0 6px rgba(147, 194, 28, .18);
    }

    /* Optional: make footer not cramped */
    .pt-card-footer {
      gap: .5rem;
    }
  </style>
@endpush

@php
  use Carbon\Carbon;

  $ageHours = Carbon::now()->diffInHours($task->created_at);
  $isOlder48 = $ageHours >= 48;

  $cardColor = $task->color ?: '#0f172a';

  $myPivot = $employeeId
    ? $task->employees->firstWhere('id', $employeeId)
    : null;

  $myStatus = $myPivot && $myPivot->pivot ? $myPivot->pivot->status : null;
  $myAccepted = $myStatus === 'accepted';

  $rejectedEmployees = $task->employees->filter(function ($e) {
    return $e->pivot && $e->pivot->status === 'rejected';
  });

  $hasControllers = method_exists($task, 'controllers') && $task->controllers && $task->controllers->count();
  $isPublic = (string) $task->public === '1';
  $isDeleted = method_exists($task, 'trashed') && $task->trashed();

  $profileUrl = route('personal-tasks.profile', $task->id);
  $editUrl = route('personal.task.edit', $task->id);
@endphp

<div class="pt-card {{ $myAccepted ? '' : 'is-pending' }} {{ $isDeleted ? 'pt-card-deleted' : '' }}"
  data-task-id="{{ $task->id }}" data-profile-url="{{ $profileUrl }}" draggable="{{ $isDeleted ? 'false' : 'true' }}"
  style="--pt-card-color: {{ $cardColor }};">

  {{-- HEADER --}}
  <div class="pt-card-header">
    <div class="pt-card-title-wrap">
      <div class="pt-card-title">
        {{ $task->task_title ?: 'Ohne Titel' }}
      </div>

      <div class="pt-card-meta">
        <span class="pt-pill" title="Priorität">
          <i data-feather="flag" class="pt-icon-xs"></i>
          {{ $task->priority ?: 'Normal' }}
        </span>

        @if($task->due_date)
          <span class="pt-pill" title="Fälligkeit">
            <i data-feather="calendar" class="pt-icon-xs"></i>
            {{ $task->due_date->format('d.m.') }}
            @if($task->due_time) {{ $task->due_time }} @endif
          </span>
        @endif

        @if($isOlder48)
          <span class="pt-pill pt-pill-warn" title="Älter als 48 Stunden">
            <i data-feather="clock" class="pt-icon-xs"></i>
            > 48 Std.
          </span>
        @endif

        @if(!empty($task->is_report))
          <span class="pt-pill" title="Report">
            <i data-feather="clipboard" class="pt-icon-xs"></i>
            Report
          </span>
        @endif

        @if($isPublic)
          <span class="pt-pill" title="Öffentlich">
            <i data-feather="unlock" class="pt-icon-xs"></i>
            Öffentlich
          </span>
        @else
          <span class="pt-pill" title="Privat">
            <i data-feather="lock" class="pt-icon-xs"></i>
            Privat
          </span>
        @endif
      </div>
    </div>

    <div class="pt-card-side">
      @if($myAccepted)
        <span class="pt-badge-accept" title="Du hast den Job akzeptiert">
          <i data-feather="check-circle" class="pt-icon-xs"></i>
          Job akzeptiert
        </span>
      @elseif($myPivot && $myStatus === 'rejected')
        <span class="pt-badge-pending" title="Du hast den Job abgelehnt">
          <i data-feather="x-circle" class="pt-icon-xs"></i>
          Job von dir abgelehnt
        </span>
      @elseif($myPivot)
        <span class="pt-badge-pending" title="Noch nicht akzeptiert">
          <i data-feather="alert-triangle" class="pt-icon-xs"></i>
          Noch nicht akzeptiert
        </span>
      @endif

      <input type="color" class="js-task-color pt-card-color-picker" value="{{ $cardColor }}"
        data-task-id="{{ $task->id }}" title="Farbe ändern">
    </div>
  </div>

  {{-- CUSTOMER --}}
  @if($task->customer)
    <div class="pt-card-row">
      <div class="pt-card-label">
        <i data-feather="user" class="pt-icon-xs"></i> Kunde
      </div>
      <div class="pt-card-value">
        {{ $task->customer->customer_no }}
        – {{ $task->customer->lastname }} {{ $task->customer->name }}
        @if($task->customer->city) · {{ $task->customer->city }} @endif
      </div>
    </div>
  @endif

  {{-- DESCRIPTION --}}
  @if($task->description)
    <div class="pt-card-desc">
      {{ \Illuminate\Support\Str::limit($task->description, 120) }}
    </div>
  @endif

  {{-- REJECTION REASONS --}}
  @if($rejectedEmployees->count())
    <div class="pt-card-desc" style="margin-top:.25rem;color:#b91c1c;">
      @foreach($rejectedEmployees as $re)
        <div>
          <strong>{{ $re->name }} {{ $re->lastname }}:</strong>
          {{ $re->pivot->reason ?? $re->pivot->change_reason ?? 'kein Grund angegeben' }}
        </div>
      @endforeach
    </div>
  @endif

  {{-- CONTROLLERS --}}
  @if($hasControllers)
    <div class="pt-card-row">
      <div class="pt-card-label">
        <i data-feather="shield" class="pt-icon-xs"></i> Controller
      </div>
      <div class="pt-card-people">
        @foreach($task->controllers as $ctrl)
          <div class="pt-avatar" title="{{ $ctrl->name }} {{ $ctrl->lastname }}">
            @if($ctrl->image)
              <img src="{{ asset('images/employee/' . $ctrl->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;">
            @else
              {{ mb_substr($ctrl->name, 0, 1) }}{{ mb_substr($ctrl->lastname, 0, 1) }}
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- EMPLOYEES --}}
  @if($task->employees->count())
    <div class="pt-card-row">
      <div class="pt-card-label">
        <i data-feather="users" class="pt-icon-xs"></i> Mitarbeiter
      </div>
      <div class="pt-card-people">
        @foreach($task->employees as $emp)
          <div class="pt-avatar" title="{{ $emp->name }} {{ $emp->lastname }}">
            @if($emp->image)
              <img src="{{ asset('images/employee/' . $emp->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;">
            @else
              {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- FOOTER --}}
  <div class="pt-card-footer">
    <div class="pt-card-footer-meta">
      @if($task->assignedBy)
        <span class="pt-pill pt-pill-light" title="Erstellt von">
          <i data-feather="edit-3" class="pt-icon-xs"></i>
          {{ $task->assignedBy->name }} {{ $task->assignedBy->lastname }}
        </span>
      @endif
      <span class="pt-pill pt-pill-light" title="Erstellt am">
        <i data-feather="clock" class="pt-icon-xs"></i>
        {{ $task->created_at->format('d.m.Y H:i') }}
      </span>
    </div>

    <div class="pt-card-actions">
      @if($isDeleted)
        {{-- Restore --}}
        <button type="button" class="pt-icon-btn js-restore-task" title="Wiederherstellen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <polyline points="1 4 1 10 7 10"></polyline>
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
          </svg>
        </button>

        {{-- Profile --}}
        <a href="{{ $profileUrl }}" class="pt-icon-btn js-open-profile-link" title="Profil öffnen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"></path>
          </svg>
        </a>
      @else
        {{-- Start --}}
        <button type="button" class="pt-icon-btn js-status-btn" data-status="on_progress" title="Starten / Fortsetzen">
          <svg viewBox="0 0 24 24" class="is-fill" aria-hidden="true">
            <path d="M8 5v14l11-7z"></path>
          </svg>
        </button>

        {{-- Pause --}}
        <button type="button" class="pt-icon-btn js-status-btn" data-status="pause" title="Pausieren">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 4h4v16H6z"></path>
            <path d="M14 4h4v16h-4z"></path>
          </svg>
        </button>

        {{-- Complete --}}
        <button type="button" class="pt-icon-btn js-status-btn" data-status="completed" title="Abschließen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M20 6 9 17l-5-5"></path>
          </svg>
        </button>

        {{-- Accept --}}
        <button type="button" class="pt-icon-btn js-accept-btn" title="Job akzeptieren">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M14 9V5a3 3 0 0 0-3-3l-1 9"></path>
            <path d="M7 22H3V11h4"></path>
            <path d="M7 11h10l4 4-4 7H7z"></path>
          </svg>
        </button>

        {{-- Reject --}}
        <button type="button" class="pt-icon-btn js-open-reject-modal" title="Job ablehnen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M15 9l-6 6M9 9l6 6"></path>
          </svg>
        </button>

        {{-- Edit --}}
        <button type="button" class="pt-icon-btn js-edit-task-btn" data-edit-url="{{ $editUrl }}" title="Bearbeiten">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
          </svg>
        </button>

        {{-- Delete --}}
        <button type="button" class="pt-icon-btn js-delete-task" title="Löschen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 6h18"></path>
            <path d="M8 6V4h8v2"></path>
            <path d="M6 6l1 16h10l1-16"></path>
          </svg>
        </button>

        {{-- Profile --}}
        <a href="{{ $profileUrl }}" class="pt-icon-btn js-open-profile-link" title="Profil öffnen">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"></path>
          </svg>
        </a>
      @endif
    </div>
  </div>
</div>

@push('scripts')
  <script>
    // Board card interaction:
    // - single click: select card
    // - double click: open profile
    // - clicking action buttons should NOT trigger select/open
    (function () {
      const SELECTED_CLASS = 'is-selected';

      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.pt-icon-btn, .pt-card-color-picker');
        if (btn) return; // buttons shouldn't toggle selection

        const card = e.target.closest('.pt-card');
        if (!card) return;

        // clear previous selection
        document.querySelectorAll('.pt-card.' + SELECTED_CLASS).forEach(el => {
          if (el !== card) el.classList.remove(SELECTED_CLASS);
        });

        card.classList.toggle(SELECTED_CLASS);
      });

      document.addEventListener('dblclick', function (e) {
        const btn = e.target.closest('.pt-icon-btn, .pt-card-color-picker');
        if (btn) return;

        const card = e.target.closest('.pt-card');
        if (!card) return;

        const url = card.getAttribute('data-profile-url');
        if (url) window.location.href = url;
      });
    })();
  </script>
@endpush