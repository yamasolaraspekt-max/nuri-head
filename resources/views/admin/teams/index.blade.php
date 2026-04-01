@extends('admin.layouts.app')
@section('title') Teams @endsection

@section('style')
<style>
  /* Search field with icon */
  input[name="search"] {
    border-radius: 10px;
    padding-left: 38px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='gray' viewBox='0 0 24 24'%3E%3Cpath d='M21 20l-5.6-5.6a7 7 0 10-1.4 1.4L20 21zM5 10a5 5 0 1110 0A5 5 0 015 10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: 18px;
    background-position: 10px center;
  }

  /* Horizontal avatar rows */
  .avatar-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 4px 0;
  }

  .avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: transform 0.15s ease-in-out;
  }

  .avatar:hover {
    transform: scale(1.08);
  }

  .badge-info {
    background-color: #74b2d4 !important;
  }
</style>
@endsection


@section('content')
<!-- BEGIN: Content -->
<div class="app-content content">
  <div class="app-content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Teams</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">Teams</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-3 col-12 text-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#createTeamModal">
          <i class="fa fa-plus"></i> Team erstellen
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="content-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <!-- Search -->
      <form method="GET" action="{{ route('teams.index') }}" class="mb-2">
        <div class="row align-items-center">
          <div class="col-md-9 mb-1 mb-md-0">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                   placeholder="Teamname, Abteilung oder Leiter suchen...">
          </div>
          <div class="col-md-3 text-right">
            <button class="btn btn-outline-secondary btn-block" type="submit">
              <i class="feather icon-search"></i> Suchen
            </button>
          </div>
        </div>
      </form>

      <!-- Team Cards -->
      <div class="row" id="table-hover-animation">
        @forelse($teams as $team)
          <div class="col-md-4">
            <div class="card mb-2">
              <div class="card-content">
                <div class="card-body">

                  <!-- Header -->
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $team->name }}</h5>
                    <span class="badge badge-info">{{ $team->department->department_name ?? '—' }}</span>
                  </div>

                  <!-- Description -->
                  @if($team->description)
                    <p class="mt-2 mb-2 text-muted">{{ $team->description }}</p>
                  @endif

                  <!-- Leader -->
                  <div class="mb-2">
                    <strong>Teamleiter:</strong><br>
                    @if($team->leader && $team->leader->employee)
                      <div class="avatar-row mt-1">
                        <img
                          src="{{ $team->leader->employee->image ? asset('images/employee/'.$team->leader->employee->image) : asset('images/employee/default.png') }}"
                          class="avatar"
                          alt="Teamleiter"
                          title="{{ $team->leader->employee->lastname }} {{ $team->leader->employee->name }} — Leitung">
                      </div>
                    @else
                      <span class="text-muted">Nicht festgelegt</span>
                    @endif
                  </div>

                  <!-- Members -->
                  <div class="mb-2">
                    <strong>Mitglieder ({{ $team->members->count() }}):</strong>
                    <div class="avatar-row">
                      @foreach($team->members as $m)
                        <img
                          src="{{ $m->employee->image ? asset('images/employee/'.$m->employee->image) : asset('images/employee/default.png') }}"
                          class="avatar"
                          alt="Mitglied"
                          title="{{ $m->employee->lastname ?? '' }} {{ $m->employee->name ?? '' }}@if($m->position) — {{ $m->position->position }}@endif">
                      @endforeach
                    </div>
                  </div>

                  <!-- Reserves -->
                  <div class="mb-3">
                    <strong>Ersatzmitglieder ({{ $team->reserves->count() }}):</strong>
                    <div class="avatar-row">
                      @foreach($team->reserves as $r)
                        <img
                          src="{{ $r->employee->image ? asset('images/employee/'.$r->employee->image) : asset('images/employee/default.png') }}"
                          class="avatar"
                          alt="Reserve"
                          title="{{ $r->employee->lastname ?? '' }} {{ $r->employee->name ?? '' }}@if($r->position) — {{ $r->position->position }}@endif">
                      @endforeach
                    </div>
                  </div>

                  <!-- Buttons -->
                  <div class="d-flex">
                    <a href="{{ route('teams.edit', $team) }}" class="btn btn-outline-primary btn-sm mr-1">
                      <i class="feather icon-users"></i> Mitglieder
                    </a>
                    <form method="POST" action="{{ route('teams.destroy',$team) }}" onsubmit="return confirm('Dieses Team wirklich löschen?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm">
                        <i class="feather icon-trash-2"></i> Löschen
                      </button>
                    </form>
                  </div>

                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-info">Noch keine Teams vorhanden.</div>
          </div>
        @endforelse
      </div>

    </div>
  </div>
</div>
<!-- END: Content -->


<!-- Create Team Modal -->
<div class="modal fade text-left" id="createTeamModal" tabindex="-1" role="dialog" aria-labelledby="createTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
    <form method="POST" action="{{ route('teams.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h4 class="modal-title" id="createTeamModalLabel">Neues Team</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Teamname</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Abteilung</label>
          <select name="department_id" class="form-control" required>
            <option value="">— auswählen —</option>
            @foreach($departments as $d)
              <option value="{{ $d->id }}">{{ $d->department_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="Published" selected>Aktiv</option>
            <option value="Unpublished">Inaktiv</option>
          </select>
        </div>

        <div class="form-group">
          <label>Beschreibung (optional)</label>
          <textarea name="description" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Speichern</button>
      </div>
    </form>
  </div>
</div>
@endsection
