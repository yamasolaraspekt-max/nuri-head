@extends('admin.layouts.app')

@section('title') Mitarbeiter Details @stop

@section('style')
<style>
    .contacts strong { color: #111827; }
    .contacts a { color: #374151; text-decoration: none; }
    .contacts a:hover { text-decoration: underline; }
    .employee_card .photo img { border-radius: 999px; object-fit: cover; }
    .employee_card .contacts p { font-size: 12px; color: #6b7280; }
    .emp-color-input { border: none; background: transparent; padding: 0; width: 24px; height: 24px; cursor: pointer; }
    .emp-color-wrapper { display: inline-flex; align-items: center; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        {{-- Breadcrumbs & Title --}}
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">MITARBEITERLISTE</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a>Liste</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">

                                @php
                                    $statusTab = request('status_tab', $statusTab ?? 'active');
                                @endphp

                                {{-- Header --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                                    <div class="mb-1 mb-md-0">
                                        <h4 class="mb-0">Mitarbeiterliste</h4>
                                        <small class="text-muted">
                                            @if($statusTab === 'active')
                                                Aktive Mitarbeiter ({{ $activeCount ?? 0 }})
                                            @else
                                                Deaktivierte / ehemalige Mitarbeiter ({{ $inactiveCount ?? 0 }})
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        <a type="button" class="btn btn-outline-primary" href="{{ url('emp_create') }}">
                                            <i class="feather icon-user-plus mr-25"></i> Mitarbeiter erstellen
                                        </a>
                                    </div>
                                </div>

                                {{-- Navigation Tabs & Generate Button --}}
                                <ul class="nav nav-pills mb-2 align-items-center">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $statusTab === 'active' ? 'active' : '' }}"
                                           href="{{ route('emp.info', array_merge(request()->except('page', 'status_tab'), ['status_tab' => 'active'])) }}">
                                            <i class="feather icon-user-check mr-25"></i> Aktiv
                                            <span class="badge badge-light-primary ml-50">{{ $activeCount ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $statusTab === 'inactive' ? 'active' : '' }}"
                                           href="{{ route('emp.info', array_merge(request()->except('page', 'status_tab'), ['status_tab' => 'inactive'])) }}">
                                            <i class="feather icon-user-x mr-25"></i> Deaktiviert
                                            <span class="badge badge-light-secondary ml-50">{{ $inactiveCount ?? 0 }}</span>
                                        </a>
                                    </li>

                                    {{-- GENERATE BUTTON (Next to tabs) --}}
                                    <li class="nav-item ml-2">
                                        <button type="button" class="btn btn-flat-danger" data-toggle="modal" data-target="#generateAllModal">
                                            <i class="feather icon-refresh-cw mr-25"></i> Passcodes generieren
                                        </button>
                                    </li>
                                </ul>

                                {{-- Filter / Sort --}}
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <form action="{{ route('emp.info') }}" method="GET" class="form-inline flex-wrap">
                                            <input type="hidden" name="status_tab" value="{{ $statusTab }}">
                                            
                                            <div class="input-group mr-1 mb-1" style="min-width: 220px;">
                                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Suche Mitarbeiter">
                                            </div>

                                            <div class="input-group mr-1 mb-1">
                                                <select name="sort" class="form-control" onchange="this.form.submit()">
                                                    <option value="lastname" {{ request('sort', 'lastname') == 'lastname' ? 'selected' : '' }}>Nachname</option>
                                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Vorname</option>
                                                    <option value="department" {{ request('sort') == 'department' ? 'selected' : '' }}>Abteilung</option>
                                                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                                                </select>
                                            </div>

                                            <div class="input-group mr-1 mb-1">
                                                <select name="direction" class="form-control" onchange="this.form.submit()">
                                                    <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>Aufsteigend</option>
                                                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Absteigend</option>
                                                </select>
                                            </div>

                                            <button class="btn btn-primary mb-1" type="submit">
                                                <i class="feather icon-filter mr-25"></i> Filter
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Main Table --}}
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 60px;"># / Farbe</th>
                                                <th scope="col">Mitarbeiter</th>
                                                <th scope="col">Abteilung</th>
                                                <th scope="col">Gebiet</th>
                                                <th scope="col">Betrieb</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" class="text-right">Aktionen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $item)
                                                <tr>
                                                    {{-- Index + Color --}}
                                                    <td style="border-left: 6px solid {{ $item->color ?? '#8fc73e' }};">
                                                        <div class="emp-color-wrapper">
                                                            <span>{{ $loop->iteration }}</span>
                                                            <form action="{{ url('/employee_color/'.$item->id) }}" method="POST" class="ml-1">
                                                                @csrf @method('PATCH')
                                                                <input type="color" name="color" value="{{ $item->color ?? '#8fc73e' }}" class="emp-color-input" title="Farbe ändern" onchange="this.form.submit()">
                                                            </form>
                                                        </div>
                                                    </td>

                                                    {{-- Mitarbeiter Info --}}
                                                    <td>
                                                        <div class="employee_card d-flex">
                                                            <div class="photo mr-1">
                                                                <a type="button" class="btn btn-icon btn-icon" data-toggle="modal" data-target="#image{{ $item->id }}">
                                                                    <img src="{{ asset('images/employee/'.$item->image) }}" alt="avatar" height="32" width="32">
                                                                </a>
                                                            </div>
                                                            <div class="contacts">
                                                                <a href="{{ url('employee_profile/'.$item->id) }}">
                                                                    {{ $item->lastname }}, <strong>{{ $item->name }}</strong>
                                                                    <p class="m-0"><i class="feather icon-phone"></i> {{ $item->phone }}</p>
                                                                    <p class="m-0"><i class="feather icon-mail"></i> {{ $item->email }}</p>
                                                                    @php
                                                                        $main_address = DB::table('employee_addresses')->where('emp_id', $item->id)->where('main', 'active')->select('street', 'postal', 'city')->first();
                                                                    @endphp
                                                                    @if ($main_address)
                                                                        <p class="m-0"><i class="feather icon-map-pin"></i> {{ $main_address->street }} {{ $main_address->postal }} {{ $main_address->city }}</p>
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>

                                                        {{-- Image Modal --}}
                                                        <div class="modal fade text-left" id="image{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <h3><a href="{{ url('next_employee/'.$item->id) }}">{{ $item->name }} {{ $item->lastname }}</a></h3>
                                                                        <p><code>{{ $item->branch }}</code></p>
                                                                        <img src="{{ asset('images/employee/'.$item->image) }}" alt="avatar" height="200" width="200">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Abteilung --}}
                                                    <td>
                                                        @php
                                                            $employeePositions = DB::table('department_positions')
                                                                ->join('departments', 'departments.id', '=', 'department_positions.department_id')
                                                                ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                                                                ->where('department_positions.employee_id', $item->id)
                                                                ->select('departments.department_name', 'positions.position', 'department_positions.main')
                                                                ->get();
                                                        @endphp
                                                        @forelse($employeePositions as $pos)
                                                            <div class="mb-1">
                                                                <span class="badge badge-info">{{ $pos->department_name }}</span>
                                                                <span class="badge badge-secondary">{{ $pos->position }}</span>
                                                                <span class="badge badge-primary">{{ $pos->main == 'active' ? 'Haupt' : 'Neben' }}</span>
                                                            </div>
                                                        @empty
                                                            <span class="text-muted">-</span>
                                                        @endforelse
                                                    </td>

                                                    {{-- Gebiet --}}
                                                    <td>
                                                        @if($postcodeLists->where('employee_id', $item->id)->count())
                                                            @foreach($postcodeLists->where('employee_id', $item->id) as $pList)
                                                                <div class="mb-1">
                                                                    <span class="badge badge-secondary">{{ $pList->postcode_from }} - {{ $pList->postcode_to }}</span>
                                                                    @if($pList->country) <span class="badge badge-primary">{{ $pList->country }}</span> @endif
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- Betrieb --}}
                                                    <td>{{ $item->branch }}</td>

                                                    {{-- Status --}}
                                                    <td>
                                                        @if($item->status === 'Active')
                                                            <span class="badge badge-success">Aktiv</span>
                                                        @else
                                                            <span class="badge badge-secondary">Deaktiviert</span>
                                                        @endif
                                                    </td>

                                                    {{-- Actions (Consolidated) --}}
                                                    <td class="text-right">
                                                        <div class="btn-group">
                                                            {{-- Passcode Reset --}}
                                                            <button type="button" class="btn btn-icon btn-flat-warning rounded-circle" data-toggle="modal" data-target="#reset-passcode{{ $item->id }}" title="Passcode Reset">
                                                                <i class="feather icon-lock"></i>
                                                            </button>

                                                            {{-- Edit --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Employee')->where('is_update', 'on')->exists())
                                                                <a href="{{ url('next_employee/'.$item->id) }}" class="btn btn-icon btn-flat-primary rounded-circle" title="Bearbeiten">
                                                                    <i class="feather icon-edit"></i>
                                                                </a>
                                                            @endif

                                                            {{-- Toggle Status --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Employee')->where('is_update', 'on')->exists())
                                                                @if($item->status != 'Active')
                                                                    <a href="{{ url('/employee_active/'.$item->id) }}" class="btn btn-icon btn-flat-success rounded-circle" title="Aktivieren"><i class="feather icon-check-square"></i></a>
                                                                @else
                                                                    <a href="{{ url('/employee_deactive/'.$item->id) }}" class="btn btn-icon btn-flat-danger rounded-circle" title="Deaktivieren"><i class="feather icon-power"></i></a>
                                                                @endif
                                                            @endif

                                                            {{-- Delete --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Employee')->where('is_delete', 'on')->exists())
                                                                <button type="button" class="btn btn-icon btn-flat-danger rounded-circle" data-toggle="modal" data-target="#delete-pro{{ $item->id }}" title="Löschen"><i class="feather icon-trash"></i></button>
                                                            @endif
                                                        </div>

                                                        {{-- Individual Passcode Modal --}}
                                                        <div class="modal fade text-left" id="reset-passcode{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Passcode neu setzen: {{ $item->name }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <form action="{{ url('/employee_passcode/'.$item->id) }}" method="POST">
                                                                        @csrf @method('PATCH')
                                                                        <div class="modal-body text-left">
                                                                            <div class="alert alert-warning mb-2">
                                                                                <i class="feather icon-alert-triangle mr-1"></i> Der alte Passcode wird überschrieben.
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Neuer 4-stelliger Passcode</label>
                                                                                <input type="text" class="form-control" name="passcode" placeholder="z.B. 1234" required pattern="\d{4}" maxlength="4" autocomplete="off">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-warning">Speichern</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Delete Modal --}}
                                                        <div class="modal fade text-left" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Löschen bestätigen</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <div class="modal-body text-left">
                                                                        <p>Möchten Sie <strong>{{ $item->name }} {{ $item->lastname }}</strong> wirklich löschen?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Nein</button>
                                                                        <a href="{{ url('/emp_destroy/'.$item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted p-3">Keine Mitarbeiter gefunden.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                {{ $data->links() }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal for Bulk Generation --}}
<div class="modal fade text-left" id="generateAllModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Warnung: Alle Passcodes überschreiben</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Sind Sie sicher? Dies wird <strong>für alle aktiven Mitarbeiter</strong> einen neuen zufälligen Passcode generieren.</p>
                <p class="text-danger font-weight-bold">Die alten Passcodes werden unwiderruflich gelöscht!</p>
                <p class="text-muted"><small>Nach dem Generieren wird Ihnen eine Liste aller neuen Codes angezeigt.</small></p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('emp.generate_all') }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-danger">Ja, alles neu generieren</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SUCCESS MODAL: Shows generated codes (Auto-opens if session has data) --}}
@if(session('generated_codes'))
<div class="modal fade text-left" id="showGeneratedCodesModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h5 class="modal-title white">Passcodes Erfolgreich Generiert</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-2">
                    <i class="feather icon-alert-triangle mr-1"></i> <strong>Achtung:</strong> Bitte drucken oder speichern Sie diese Liste jetzt. Die Codes sind nach dem Schließen dieses Fensters nicht mehr einsehbar!
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Mitarbeiter</th>
                                <th>Betrieb</th>
                                <th class="text-center">Neuer Passcode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('generated_codes') as $code)
                            <tr>
                                <td>{{ $code['name'] }}</td>
                                <td>{{ $code['branch'] }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light-primary font-medium-2" style="letter-spacing: 2px;">{{ $code['code'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                    <i class="feather icon-printer"></i> Drucken
                </button>
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    Ich habe die Codes gespeichert (Schließen)
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('script')
<script>
    $(document).ready(function () {
        @if(Session::has('update_msg')) toastr.success("{{ session('updated_msg') }}"); @endif
        @if(Session::has('save_msg')) toastr.success("{{ session('save_msg') }}"); @endif
        @if(Session::has('delete_msg')) toastr.error("{{ session('delete_msg') }}"); @endif

        // Auto-open success modal if present
        @if(session('generated_codes'))
            $('#showGeneratedCodesModal').modal('show');
        @endif
    });
</script>
@endsection