@extends('admin.layouts.app')
@section('title') Team verwalten: {{ $team->name }} @endsection

@section('style')
{{-- Select2 Styles --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css">

<style>
  .stack { min-height: 120px; border: 1px dashed #ced4da; border-radius: .25rem; padding: .5rem; background: #fbfbfc; }
  .person { background: #fff; border: 1px solid #e9ecef; border-radius: .25rem; padding: .5rem .5rem; margin-bottom: .5rem; display:flex; align-items:center; justify-content:space-between; }
  .person small { color:#6c757d; display:block; }
  .person-info { display:flex; align-items:center; gap:.5rem; }
  .avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #dee2e6;
  }
  .stack-title { font-weight:600; font-size:.95rem; margin-bottom:.4rem; }
  .select2-container { width: 220px !important; max-width: 100%; }
  @media (max-width: 576px){ .select2-container { width: 100% !important; } }
</style>

@endsection

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <!-- Kopf + Breadcrumbs -->
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">
              Team verwalten: {{ $team->name }}
              <small class="text-muted">({{ $team->department->department_name ?? '—' }})</small>
            </h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('teams.index') }}">Teams</a></li>
                <li class="breadcrumb-item active">Verwalten</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-3 col-12 text-right">
        <a href="{{ route('teams.index') }}" class="btn btn-light"><i class="feather icon-corner-up-left"></i> Zurück</a>
      </div>
    </div>

    <!-- Inhalt -->
    <div class="content-body">
      {{-- SweetAlert: Server-Feedback --}}
      @if(session('success'))
        <script>window._flashSuccess = @json(session('success'));</script>
      @endif
      @if($errors->any())
        <script>window._flashErrors = @json($errors->all());</script>
      @endif

      <div class="row">
        <!-- Links: Team-Metadaten + Pool -->
        <div class="col-md-4">
          <div class="card mb-2">
            <form method="POST" action="{{ route('teams.update', $team) }}" id="metaForm">
              @csrf @method('PUT')
              <div class="card-content">
                <div class="card-body">
                  <div class="form-group">
                    <label>Teamname</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$team->name) }}" required>
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2" data-placeholder="Status wählen">
                      <option value="Published" {{ $team->status==='Published'?'selected':'' }}>Aktiv</option>
                      <option value="Unpublished" {{ $team->status==='Unpublished'?'selected':'' }}>Inaktiv</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Beschreibung</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description',$team->description) }}</textarea>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button class="btn btn-primary"><i class="feather icon-save"></i> Speichern</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Mitarbeitende (auf Abteilung beschränkt) -->
          <div class="card">
            <div class="card-content">
              <div class="card-body">
                <h6 class="mb-1">Mitarbeitende ({{ $team->department->department_name ?? '' }})</h6>
                <input type="text" id="searchPool" class="form-control form-control-sm mb-2" placeholder="Nach Namen suchen…">
                <div id="pool" class="stack">
                  @php $usedIds = $team->membersAll->pluck('employee_id')->toArray(); @endphp
                    @foreach($deptEmployees as $e)
                      @continue(in_array($e->id, $usedIds))
                      <div class="person" data-employee-id="{{ $e->id }}">
                        <div class="person-info">
                          <img src="{{ $e->image ? asset('images/employee/'.$e->image) : asset('images/employee/default.png') }}" alt="Foto" class="avatar">
                          <div>
                            <strong>{{ $e->lastname }} {{ $e->name }}</strong>
                          </div>
                        </div>
                        <div>
                          <select class="custom-select custom-select-sm position select2" data-placeholder="Position wählen">
                            <option value=""></option>
                            @foreach($positions as $p)
                              <option value="{{ $p->id }}">{{ $p->position }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    @endforeach

                </div>
                <small class="text-muted d-block mt-1">Per Drag & Drop in <strong>Leitung</strong>, <strong>Mitglieder</strong> oder <strong>Reserve</strong> ziehen.</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Rechts: Leitung / Mitglieder / Reserve -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-content">
              <div class="card-body">
                <div class="row">
                  <!-- Leitung -->
                  <div class="col-md-12 mb-2">
                    <div class="stack-title">Leitung (max. 1)</div>
                    <div id="leader" class="stack">
                      @if($team->leader && $team->leader->employee)
                        <div class="person" data-employee-id="{{ $team->leader->employee_id }}">
                          <div class="person-info">
                            <img src="{{ $team->leader->employee->image ? asset('images/employee/'.$team->leader->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="avatar">
                            <div>
                              <strong>{{ $team->leader->employee->lastname }} {{ $team->leader->employee->name }}</strong>
                              <small class="text-info">Leitung</small>
                            </div>
                          </div>
                          <div>
                            <select class="custom-select custom-select-sm position select2" data-placeholder="Position wählen">
                              <option value=""></option>
                              @foreach($positions as $p)
                                <option value="{{ $p->id }}" {{ $team->leader->position_id==$p->id?'selected':'' }}>{{ $p->position }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      @endif

                    </div>
                  </div>

                  <!-- Mitglieder -->
                  <div class="col-md-6 mb-2">
                    <div class="stack-title">Mitglieder</div>
                    <div id="members" class="stack">
                     @foreach($team->members as $m)
                      <div class="person" data-employee-id="{{ $m->employee_id }}">
                        <div class="person-info">
                          <img src="{{ $m->employee->image ? asset('images/employee/'.$m->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="avatar">
                          <div>
                            <strong>{{ $m->employee->lastname }} {{ $m->employee->name }}</strong>
                            <small class="text-secondary">Mitglied</small>
                          </div>
                        </div>
                        <div>
                          <select class="custom-select custom-select-sm position select2" data-placeholder="Position wählen">
                            <option value=""></option>
                            @foreach($positions as $p)
                              <option value="{{ $p->id }}" {{ $m->position_id==$p->id?'selected':'' }}>{{ $p->position }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    @endforeach

                    </div>
                  </div>

                  <!-- Reserve -->
                  <div class="col-md-6 mb-2">
                    <div class="stack-title">Reserve</div>
                    <div id="reserves" class="stack">
                      @foreach($team->reserves as $r)
                        <div class="person" data-employee-id="{{ $r->employee_id }}">
                          <div class="person-info">
                            <img src="{{ $r->employee->image ? asset('images/employee/'.$r->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="avatar">
                            <div>
                              <strong>{{ $r->employee->lastname }} {{ $r->employee->name }}</strong>
                              <small class="text-secondary">Reserve</small>
                            </div>
                          </div>
                          <div>
                            <select class="custom-select custom-select-sm position select2" data-placeholder="Position wählen">
                              <option value=""></option>
                              @foreach($positions as $p)
                                <option value="{{ $p->id }}" {{ $r->position_id==$p->id?'selected':'' }}>{{ $p->position }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      @endforeach

                    </div>
                  </div>
                </div>

                <div class="text-right">
                  <button id="saveMembers" class="btn btn-primary">
                    <i class="feather icon-save"></i> Teammitglieder speichern
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Schnellaktion: Reserve befördern -->
          <div class="card mt-1">
            <div class="card-content">
              <div class="card-body">
                <form method="POST" action="{{ route('teams.promote.reserve',$team) }}" id="promoteForm" class="form-inline">
                  @csrf
                  <label class="mr-1">Reserve befördern:</label>
                  <select name="reserve_employee_id" class="form-control select2 mr-1" data-placeholder="Reserve wählen">
                    <option value=""></option>
                    @foreach($team->reserves as $r)
                      <option value="{{ $r->employee_id }}">{{ $r->employee->lastname }} {{ $r->employee->name }}</option>
                    @endforeach
                  </select>
                  <label class="mr-1">Mitglied ersetzen (optional):</label>
                  <select name="replace_member_employee_id" class="form-control select2 mr-1" data-placeholder="Mitglied wählen">
                    <option value=""></option>
                    @foreach($team->members as $m)
                      <option value="{{ $m->employee_id }}">{{ $m->employee->lastname }} {{ $m->employee->name }}</option>
                    @endforeach
                  </select>
                  <button class="btn btn-outline-secondary">Übernehmen</button>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div> <!-- /row -->
    </div> <!-- /content-body -->
  </div> <!-- /content-wrapper -->
</div>
<!-- END: Content-->
@endsection

@section('script')
{{-- SweetAlert2 + Select2 + SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function() {
  // ---- SweetAlert: Server-Flash anzeigen ----
  if (window._flashSuccess) {
    Swal.fire({ icon: 'success', title: 'Erfolgreich', text: window._flashSuccess, confirmButtonText: 'OK' });
  }
  if (window._flashErrors && window._flashErrors.length) {
    Swal.fire({
      icon: 'error',
      title: 'Fehler',
      html: '<ul style="text-align:left;margin:0;padding-left:18px;">' +
            window._flashErrors.map(e => `<li>${e}</li>`).join('') +
            '</ul>',
      confirmButtonText: 'OK'
    });
  }

  // ---- Select2 Initialisierung ----
  function initSelect2(scope) {
    var $scope = scope ? $(scope) : $(document);
    $scope.find('select.select2').each(function(){
      var $el = $(this);
      if ($el.hasClass('select2-hidden-accessible')) return;
      $el.select2({
        theme: 'bootstrap4',
        width: 'resolve',
        placeholder: $el.data('placeholder') || '',
        allowClear: true,
        dropdownParent: $('body') // bei Modals ggf. anpassen
      });
    });
  }
  initSelect2();

  // ---- Suche im Pool ----
  document.getElementById('searchPool').addEventListener('input', function(){
    var q = this.value.toLowerCase();
    document.querySelectorAll('#pool .person').forEach(function(el){
      el.style.display = el.innerText.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
  });

  // ---- Sortierbare, verbundene Listen ----
  ['pool','leader','members','reserves'].forEach(function(id){
    new Sortable(document.getElementById(id), {
      group: 'people',
      animation: 150,
      sort: true,
      onAdd: function(evt) {
        // Max. 1 in Leitung
        if (evt.to.id === 'leader' && evt.to.children.length > 1) {
          evt.from.appendChild(evt.item);
          Swal.fire({ icon: 'warning', title: 'Hinweis', text: 'Die Leitung darf nur eine Person enthalten.' });
          return;
        }
        initSelect2(evt.item);
      },
      onUpdate: function(evt){ initSelect2(evt.item); }
    });
  });

  // ---- Payload aus DOM sammeln ----
  function collectStack(containerId) {
    var arr = [];
    document.querySelectorAll('#'+containerId+' .person').forEach(function(el, idx){
      var $select = $(el).find('select.position');
      var posVal = $select.length ? $select.val() : null;
      arr.push({
        employee_id: el.getAttribute('data-employee-id'),
        position_id: posVal || null,
        sort_order: idx
      });
    });
    return arr;
  }

  function collectPayload() {
    var leader = collectStack('leader');
    if (leader.length > 1) leader = [leader[0]];
    return { leader: leader, members: collectStack('members'), reserves: collectStack('reserves') };
  }

  // ---- Teammitglieder speichern (AJAX + SweetAlert) ----
  document.getElementById('saveMembers').addEventListener('click', function(){
    var payload = collectPayload();
    fetch("{{ route('teams.members.sync',$team) }}", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify(payload)
    })
    .then(r => { if (!r.ok) throw new Error('Speichern fehlgeschlagen'); return r.text(); })
    .then(() => {
      Swal.fire({ icon:'success', title:'Gespeichert', text:'Teammitglieder wurden aktualisiert.' })
        .then(() => location.reload());
    })
    .catch(err => {
      Swal.fire({ icon:'error', title:'Fehler', text: err.message || 'Fehler beim Speichern.' });
    });
  });

  // ---- Reserve befördern (AJAX + SweetAlert) ----
  document.getElementById('promoteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = this;
    var fd = new FormData(form);

    // Optional: Validierung
    if (!fd.get('reserve_employee_id')) {
      Swal.fire({ icon:'warning', title:'Hinweis', text:'Bitte eine Reserve-Person auswählen.' });
      return;
    }

    fetch(form.action, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: fd
    })
    .then(r => { if (!r.ok) throw new Error('Aktion fehlgeschlagen'); return r.text(); })
    .then(() => {
      Swal.fire({ icon:'success', title:'Befördert', text:'Die Reserve wurde in das Team übernommen.' })
        .then(() => location.reload());
    })
    .catch(err => {
      Swal.fire({ icon:'error', title:'Fehler', text: err.message || 'Aktion fehlgeschlagen.' });
    });
  });
})();
</script>
@endsection
