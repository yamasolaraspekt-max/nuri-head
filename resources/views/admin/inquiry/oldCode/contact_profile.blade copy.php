@extends('admin.layouts.app')

@section('title', 'ANFRAGE AUFNAHME')

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<meta name="csrf-token" content="{{ csrf_token() }}"> 
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
  .kanban-board { display:flex; gap:20px; }
  .kanban-column { flex:1; background:#f1f1f1; border-radius:8px; padding:10px; min-height:300px; }
  .kanban-header { font-weight:700; text-align:center; background:#8fc73e; color:#fff; padding:8px; border-radius:4px; margin-bottom:10px; }
  .kanban-card { background:#fff; border:1px solid #ccc; padding:12px; border-radius:6px; cursor:grab; }
  .kanban-card img { width:30px; height:30px; object-fit:cover; border-radius:50%; }

  .circle { width:35px; height:35px; background:#7DC242; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:1.2rem; }
  .line { width:9px; height:4px; background:#7DC242; margin-left:-3px; margin-right:-2px; position:relative; top:2px; }
  .profile   { width:35px; height:35px; border-radius:50%; object-fit:cover; border:3px solid #7DC242; } /* inside (green) */
  .profile-s { width:35px; height:35px; border-radius:50%; object-fit:cover; border:3px solid #f4a459; } /* field (orange) */
  .profile-r { width:35px; height:35px; border-radius:50%; object-fit:cover; border:3px solid #ea5455; }
  .text { font-size:10px; font-weight:500; color:#555; text-align:center; margin-top:10px; }

  @keyframes flash { 0%{background:#c3f3c3;} 50%{background:#a8e6a8;} 100%{background:#c3f3c3;} }
  .animated.flash { animation: flash 2s ease-in-out 1; }

  .timeline { list-style:none; padding-left:0; position:relative; }
  .timeline:before { content:''; position:absolute; top:0; bottom:0; left:18px; width:2px; background:#dee2e6; }
  .timeline-item { position:relative; margin-left:40px; }
  .timeline-point { width:12px; height:12px; background:#6c757d; border-radius:50%; position:absolute; left:-24px; top:3px; }
  .timeline-point-success { background:#28a745; }
  .timeline-point-warning { background:#ffc107; }
  .timeline-event { background:#f9f9f9; padding:10px 15px; border-radius:6px; border:1px solid #ddd; }

  .comment { background:#f8f9fa; border:1px solid #ddd; padding:10px; margin-bottom:10px; border-radius:5px; }
  .comment button { margin-right:5px; }
  .comment .d-flex { margin-bottom:10px; }
  .comment p { font-size:14px; line-height:1.5; }
    /* Bereits verifizierte Anfrage – sichtbar, aber gesperrt */
 
.kanban-card-verified {
  border-color: #34d399;
  box-shadow: 0 0 0 1px rgba(52,211,153,0.4);
}

</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">ANFRAGE</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                  <a href="{{ url('/inquiry_view') }}">Anfrageliste</a>
                </li>
                <li class="breadcrumb-item active">
                  <a>
                    {{ $data->firma ?? '' }} - {{ $data->name ?? '' }} {{ $data->lastname ?? '' }}
                  </a>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body container-fluid">
      {{-- Header Banner --}}
      <div class="card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div>
              <h5 class="mb-1">
                <p class="mb-0">
                  <strong>{{ $data->firma ?? '' }} - {{ $data->name ?? '' }} {{ $data->lastname ?? '' }}</strong>
                </p>
                <p class="mb-0">
                  <small>{{ $data->pre_type ?? '' }}</small>
                </p>
              </h5>
              <p class="mb-0 text-muted">
                <i class="bi bi-geo-alt-fill"></i>
                {{ $data->street ?? '' }}, {{ $data->postcode ?? '' }} {{ $data->city ?? '' }}
              </p>
              <div>
                @for($i = 0; $i < 5; $i++)
                  <i class="bi bi-star{{ $i < 4 ? '-fill text-warning' : '' }}"></i>
                @endfor
              </div>
            </div>

            <div class="ml-3">
              <h5 class="mb-1">
                <p class="mb-0"><strong>Note</strong></p>
                <p class="mb-0"><small>{{ $data->note ?? '' }}</small></p>
              </h5>
            </div>
          </div>
          <div class="btn-toolbar">
            @if($data->status!="Published")
            <a class="btn btn-sm btn-success me-2" href="{{ url('inquiry_edit/'.$data->id) }}">
              <i class="feather icon-edit"></i>
            </a>
            @endif
          </div>
        </div>
      </div>

      <div class="row gx-4">
        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h6 class="text-uppercase text-secondary">Grundlegende Informationen</h6>
              <dl class="row">
                <dt class="col-5 mb-1">E-Mail</dt>
                <dd class="col-7">{{ $data->email ?? ''}}</dd>

                <dt class="col-5 mb-1">Telefon</dt>
                <dd class="col-7">{{ $data->phone ?? '' }}</dd>

                <dt class="col-5 mb-1">Erstellt</dt>
                <dd class="col-7">
                  {{ \Carbon\Carbon::parse($data->created_at)->locale('de')->isoFormat('D. MMMM YYYY, HH:mm') }}
                </dd>

                <dt class="col-5 mb-1">Verfasser</dt>
                <dd class="col-7">
                  <div class="d-flex align-items-center mb-2">
                    <img src="{{ asset('images/employee/'.$data->emp_image) }}"
                         alt="Mitarbeiter"
                         width="30"
                         height="30"
                         class="rounded-circle">
                    <span class="ms-2">{{ $data->emp_name }} {{ $data->emp_lastname}}</span>
                  </div>
                </dd>

                <dt class="col-5 mb-1">Status</dt>
                <dd class="col-7">
                  <div class="badge badge-primary" id="status">
                    @if($data->status=="Unpublished")
                      Neue
                    @elseif($data->status=="progress")
                      In Bearbeitung
                    @elseif($data->status=="junk")
                      Junk
                    @elseif($data->status=="Published" || $data->status=="verified")
                      Verifiziert
                    @else
                      {{ $data->status }}
                    @endif
                  </div>
                </dd>

                <dt class="col-5 mb-1">Priorität</dt>
                <dd class="col-7">
                  @php
                    $priorities = [
                      'normal'    => 'Normal',
                      'high'      => 'Dringend',
                      'very_high' => 'Sehr Dringend',
                      'low'       => 'Niedrig'
                    ];
                  @endphp
                  {{ $priorities[$data->periority] ?? 'Unbekannt' }}
                </dd>
              </dl>

              <hr>

              <h6 class="text-uppercase text-secondary">Weitere Informationen</h6>
              <dl class="row">
                <dt class="col-5 mb-1">Nächster Schritt</dt>
                <dd class="col-7">{{ $data->next_step ?? '' }}</dd>

                <dt class="col-5 mb-1">Fälligkeitsdatum</dt>
                <dd class="col-7">
                  {{ $data->due_date
                       ? \Carbon\Carbon::parse($data->due_date)->locale('de')->isoFormat('D. MMMM YYYY')
                       : '' }}
                </dd>

                @if($data->note)
                  <dt class="col-5 mb-1">Notiz</dt>
                  <dd class="col-7">{{ $data->note ?? '' }}</dd>
                @endif
              </dl>
            </div>
          </div>
        </div>

        {{-- Main --}}
        <div class="col-lg-9 mb-4">
          <div class="card-content">
            <div class="card-body">
              {{-- Tabs --}}
              <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active"
                     id="home-tab-fill"
                     data-toggle="tab"
                     href="#home-fill"
                     role="tab"
                     aria-controls="home-fill"
                     aria-selected="true">KANBAN</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link"
                     id="profile-tab-fill"
                     data-toggle="tab"
                     href="#profile-fill"
                     role="tab"
                     aria-controls="profile-fill"
                     aria-selected="false">PRODUKT</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link"
                     id="verify-tab-fill"
                     data-toggle="tab"
                     href="#verify-fill"
                     role="tab"
                     aria-controls="verify-fill"
                     aria-selected="false">VERIFIZIERUNG</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link"
                     id="comment-tab-fill"
                     data-toggle="tab"
                     href="#comment-fill"
                     role="tab"
                     aria-controls="comment-fill"
                     aria-selected="false">KOMMENTAR</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link"
                     id="messages-tab-fill"
                     data-toggle="tab"
                     href="#messages-fill"
                     role="tab"
                     aria-controls="messages-fill"
                     aria-selected="false">AKTIVITÄTEN</a>
                </li>
              </ul>

              <div class="tab-content pt-1">
                {{-- KANBAN --}}
                <div class="tab-pane active"
                     id="home-fill"
                     role="tabpanel"
                     aria-labelledby="home-tab-fill">
                    <div class="kanban-board">
                        @foreach(['Unpublished' => 'Neu', 'progress' => 'In Bearbeitung', 'verified' => 'Verifizieren', 'junk' => 'Junk'] as $status => $label)
                            @php
                                // Normalfall: status = Spalte
                                // Sonderfall: Published => in "verified" anzeigen
                                $belongsHere = $data->status === $status
                                    || ($status === 'verified' && $data->status === 'Published');

                                $alreadyVerified = in_array($data->status, ['Published', 'verified']);
                            @endphp

                            <div class="kanban-column" id="kanban-{{ $status }}" data-status="{{ $status }}">
                                <div class="kanban-header">{{ $label }}</div>

                                @if($belongsHere)
                                    <div class="kanban-card draggable {{ $alreadyVerified ? 'kanban-card-verified' : '' }}"
                                        data-id="{{ $data->id }}"
                                        data-already-verified="{{ $alreadyVerified ? '1' : '0' }}">
                                        
                                        @if($alreadyVerified)
                                            <span class="badge badge-success mb-1">Bereits verifiziert</span>
                                        @endif

                                        <strong>{{ $data->firma ?? ''}}</strong><br>
                                        <small>{{ $data->street ?? '' }}, {{ $data->postcode ?? '' }} {{ $data->city ?? '' }}</small>

                                        <hr class="my-2">

                                        <div class="d-flex align-items-center mb-2">
                                            <img src="{{ asset('images/employee/'.$data->emp_image) }}"
                                                alt="Mitarbeiter"
                                                width="30"
                                                height="30"
                                                class="rounded-circle">
                                            <span class="ms-2">{{ $data->emp_name }} {{ $data->emp_lastname }}</span>
                                        </div>

                                        @php
                                            $product_list = collect($productList);
                                            $grouped = $product_list->where('inquiry_id', $data->id)
                                                ->unique(fn($p) => $p->product_id.'-'.$p->inquiry_id);

                                            $male   = asset('images/gender/male.png');
                                            $female = asset('images/gender/female.png');
                                            $empImg = function ($img, $gender) use ($male, $female) {
                                                $path = $img ? public_path('images/employee/'.$img) : null;
                                                if ($img && $path && file_exists($path)) {
                                                    return asset('images/employee/'.$img);
                                                }
                                                return strtolower($gender ?? '') === 'female' ? $female : $male;
                                            };

                                            if (!function_exists('translate_service')) {
                                                function translate_service(?string $key): string {
                                                    $m = [
                                                        'complete'    => 'Komplettlösung',
                                                        'montage'     => 'Montage',
                                                        'product'     => 'Produkt',
                                                        'plan'        => 'Planung',
                                                        'maintenance' => 'Wartung',
                                                        'repair'      => 'Reparatur',
                                                        'emergency'   => 'Notdienst',
                                                        'others'      => 'Sonstiges',
                                                    ];
                                                    $k = strtolower($key ?? '');
                                                    return $m[$k] ?? ($key ?? '');
                                                }
                                            }
                                        @endphp

                                        @if($grouped->where('status', 'open')->count() > 0)
                                            <div style="display:flex; align-items:center; justify-content:flex-start; flex-wrap:nowrap;">
                                                @foreach ($grouped->where('status', 'open') as $p)
                                                    @php
                                                        $service    = translate_service($p->phase_section ?? '');
                                                        $department = $p->department_name ?? '';

                                                        $hasInside  = !empty($p->employee_id);
                                                        $insideImg  = $empImg($p->eimage ?? null, $p->egender ?? null);
                                                        $insideName = trim(($p->ename ?? '') . ' ' . ($p->elastname ?? ''));

                                                        $hasField   = !empty($p->field_employee);
                                                        $fieldImg   = $empImg($p->fimage ?? null, $p->fgender ?? null);
                                                        $fieldName  = trim(($p->fname ?? '') . ' ' . ($p->flastname ?? ''));
                                                    @endphp

                                                    <div class="d-flex flex-column align-items-center mr-1">
                                                        <div class="d-flex align-items-center">
                                                            <div class="circle"
                                                                data-toggle="tooltip"
                                                                title="{{ $p->article_group }}">
                                                                {{ $p->initial }}
                                                            </div>

                                                            <div class="line"></div>
                                                            <div class="image select-employee"
                                                                data-type="employee"
                                                                data-id="{{ $p->id }}"
                                                                data-toggle="tooltip"
                                                                title="{{ $hasInside ? $insideName : 'Innendienst wählen' }}">
                                                                <img src="{{ $insideImg }}"
                                                                    alt="Innendienst"
                                                                    class="profile">
                                                            </div>

                                                            <div class="line"></div>
                                                            <div class="image select-employee"
                                                                data-type="field_employee"
                                                                data-id="{{ $p->id }}"
                                                                data-toggle="tooltip"
                                                                title="{{ $hasField ? $fieldName : 'Außendienst wählen' }}">
                                                                <img src="{{ $fieldImg }}"
                                                                    alt="Außendienst"
                                                                    class="profile-s">
                                                            </div>
                                                        </div>

                                                        <div class="text">{{ $service }}</div>
                                                        <div class="text mt-0">{{ $department }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- PRODUKT --}}
                <div class="tab-pane"
                     id="profile-fill"
                     role="tabpanel"
                     aria-labelledby="profile-tab-fill">

                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Produktliste</h5>
                    @if($data->status != "Published")
                      <button type="button"
                              class="btn btn-sm btn-success"
                              id="addNewProductRow">+ Produkt hinzufügen</button>
                    @endif
                  </div>

                  @php
                    if (!function_exists('translate_service')) {
                      function translate_service(?string $key): string {
                        $map = [
                          'complete'    => 'Komplettlösung',
                          'montage'     => 'Montage',
                          'product'     => 'Produkt',
                          'plan'        => 'Planung',
                          'maintenance' => 'Wartung',
                          'repair'      => 'Reparatur',
                          'emergency'   => 'Notdienst',
                          'others'      => 'Sonstiges'
                        ];
                        $k = strtolower($key ?? '');
                        return $map[$k] ?? ($key ?? '');
                      }
                    }

                    $male   = asset('images/gender/male.png');
                    $female = asset('images/gender/female.png');
                    $empImg = function ($img, $gender) use ($male, $female) {
                      $path = $img ? public_path('images/employee/'.$img) : null;
                      if ($img && $path && file_exists($path)) {
                        return asset('images/employee/'.$img);
                      }
                      return strtolower($gender ?? '') === 'female' ? $female : $male;
                    };
                  @endphp

                  <form id="tabProductForm">
                    @csrf
                    <input type="hidden" name="inquiry_id" value="{{ $data->id }}">

                    <table class="table table-bordered">
                      <thead class="thead-light">
                        <tr>
                          <th>Produkt</th>
                          <th>Service</th>
                          <th>Abteilung</th>
                          <th>Innendienst</th>
                          <th>Außendienst</th>
                          <th>Termin</th>
                          <th>Aktion</th>
                        </tr>
                      </thead>

                      <tbody id="productRowsTab">
                        @forelse ($productList as $row)
                          <tr
                            data-row-id="{{ $row->id }}"
                            data-product-id="{{ $row->product_id }}"
                            data-service-id="{{ $row->service_id }}"
                            data-department-id="{{ $row->department_id }}"
                          >
                            <td>{{ $products->firstWhere('id', $row->product_id)?->article_group }}</td>
                            <td>{{ translate_service($row->phase_section) }}</td>
                            <td>{{ $departments->firstWhere('id', $row->department_id)?->department_name }}</td>

                            <td>
                              <div class="d-flex align-items-center gap-2 select-employee"
                                  data-type="employee"
                                  data-id="{{ $row->id }}"
                                  data-toggle="tooltip"
                                  title="{{ !empty($row->employee_id) ? 'Innendienst ändern' : 'Innendienst zuweisen' }}">
                                <img src="{{ $empImg($row->eimage ?? null, $row->egender ?? null) }}"
                                     class="rounded-circle"
                                     width="32"
                                     height="32"
                                     alt="Innendienst">
                                <span>{{ $row->ename ?? '—' }} {{ $row->elastname ?? '' }}</span>
                              </div>
                            </td>

                            <td>
                              <div class="d-flex align-items-center gap-2 select-employee"
                                  data-type="field_employee"
                                  data-id="{{ $row->id }}"
                                  data-toggle="tooltip"
                                  title="{{ !empty($row->field_employee) ? 'Außendienst ändern' : 'Außendienst zuweisen' }}">
                                <img src="{{ $empImg($row->fimage ?? null, $row->fgender ?? null) }}"
                                     class="rounded-circle"
                                     width="32"
                                     height="32"
                                     alt="Außendienst">
                                <span>{{ $row->fname ?? '—' }} {{ $row->flastname ?? '' }}</span>
                              </div>
                            </td>

                            <td>
                              @if(!empty($row->appointment_date))
                                {{ \Carbon\Carbon::parse($row->appointment_date)->format('d.m.Y H:i') }}
                              @else
                                <span class="text-muted">—</span>
                              @endif
                            </td>

                            <td class="text-nowrap">
                              <button type="button"
                                      class="btn btn-sm btn-danger delete-tab-product"
                                      data-id="{{ $row->id }}"
                                      data-product="{{ $row->product_id }}">
                                <i class="feather icon-trash"></i>
                              </button>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="7" class="text-center text-muted">
                              Keine Produkte vorhanden.
                            </td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>

                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                  </form>
                </div>

                {{-- VERIFIZIERUNG --}}
                <div class="tab-pane"
                     id="verify-fill"
                     role="tabpanel"
                     aria-labelledby="verify-tab-fill">

                  @php
                    $fullAddress = trim(($data->street ?? '').' '.($data->postcode ?? '').' '.($data->city ?? ''));
                    $lead = DB::table('new_leads')
                        ->whereNull('deleted_at')
                        ->whereRaw('LOWER(name) = ?', [strtolower($data->name ?? '')])
                        ->whereRaw('LOWER(lastname) = ?', [strtolower($data->lastname ?? '')])
                        ->whereRaw('LOWER(full_address) = ?', [strtolower($fullAddress)])
                        ->first();

                    $targetType  = null;
                    $targetLabel = null;
                    $targetUrl   = null;

                    if ($lead) {
                        $targetType  = 'Lead';
                        $targetLabel = 'Lead #'.$lead->id;
                        $targetUrl   = url('new_lead_profile/'.$lead->id);
                    } else {
                        $dName = $data->firma ?: trim(($data->name ?? '').' '.($data->lastname ?? ''));
                        $dist  = DB::table('distributors')
                            ->whereRaw('LOWER(name) = ?', [strtolower($dName)])
                            ->first();
                        $brand = DB::table('brands')
                            ->whereRaw('LOWER(name) = ?', [strtolower($dName)])
                            ->first();

                        if ($dist) {
                            $targetType  = 'Lieferant';
                            $targetLabel = $dist->name;
                            $targetUrl   = route('distributor');
                        } elseif ($brand) {
                            $targetType  = $brand->type === 'brand' ? 'Hersteller' : $brand->type;
                            $targetLabel = $brand->name;
                            $targetUrl   = route('brand.info');
                        }
                    }

                    $reverifyOptions = [
                      'Lead','Lieferant','Hersteller','Geschäftspartner',
                      'Architekt','Nachunternehmer','Bank','Versicherung',
                      'Bewerber','others'
                    ];

                    $canReverify = in_array($data->status, ['Published','verified']);
                  @endphp

                  <div class="card mt-2">
                    <div class="card-body">
                      <h5 class="mb-2">
                        Aktuelle Verifizierung
                      </h5>

                      <div class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge badge-info">
                          {{ $data->status }}
                        </span>
                      </div>

                      <div class="mb-2">
                        <strong>Aktueller Typ:</strong>
                        @if($data->pre_type)
                          <span class="badge badge-primary">
                            {{ $data->pre_type }}
                          </span>
                        @else
                          <span class="text-muted">Noch kein Typ gesetzt</span>
                        @endif
                      </div>

                      <div class="mb-2">
                        <strong>Ziel-Datensatz:</strong><br>
                        @if($targetType)
                          <span class="badge badge-success">{{ $targetType }}</span>
                          <span class="ml-1">{{ $targetLabel }}</span>
                          @if($targetUrl)
                            <a href="{{ $targetUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary ml-2">
                              Öffnen
                            </a>
                          @endif
                        @else
                          <span class="text-muted">
                            Keine verknüpfte Entität gefunden (Lead/Lieferant/Hersteller).
                          </span>
                        @endif
                      </div>

                      <hr>

                      @if($canReverify)
                        <h6 class="mb-2">Neu verifizieren</h6>
                        <p class="text-muted small mb-2">
                          Du kannst diese Anfrage auf einen anderen Typ neu verifizieren.
                          Der aktuell gesetzte Typ
                          @if($data->pre_type)
                            <strong>{{ $data->pre_type }}</strong>
                          @endif
                          kann nicht erneut gewählt werden.
                          Bestehende Zuordnungen (Lead, Lieferant, Hersteller, …) werden dabei ersetzt.
                        </p>

                        <form id="reverifyForm" class="form-inline">
                          <div class="form-group mr-1 mb-1">
                            <select name="type"
                                    id="verifyType"
                                    class="form-control">
                              <option value="">Neuen Typ wählen</option>
                              @foreach($reverifyOptions as $opt)
                                @if($opt === $data->pre_type)
                                  <option value="{{ $opt }}" >
                                    {{ $opt }} (aktuell)
                                  </option>
                                @else
                                  <option value="{{ $opt }}">{{ $opt }}</option>
                                @endif
                              @endforeach
                            </select>
                          </div>

                          <button type="submit"
                                  class="btn btn-warning mb-1">
                            Neu verifizieren
                          </button>
                        </form>
                      @else
                        <p class="text-muted mb-0">
                          Diese Anfrage ist noch nicht endgültig verifiziert.
                          Nutze zunächst das Kanban, um sie zu verifizieren.
                        </p>
                      @endif
                    </div>
                  </div>
                </div>

                {{-- AKTIVITÄTEN --}}
                <div class="tab-pane p-2"
                     id="messages-fill"
                     role="tabpanel"
                     aria-labelledby="messages-tab-fill">
                  <h4 class="mb-3">
                    <i class="feather icon-bell"></i>
                    Benachrichtigungen zur Anfrage
                  </h4>
                  <div id="notification-content">
                    <div class="text-muted">Benachrichtigungen werden geladen...</div>
                  </div>
                </div>

                {{-- KOMMENTAR --}}
                <div class="tab-pane p-2"
                     id="comment-fill"
                     role="tabpanel"
                     aria-labelledby="comment-tab-fill">
                  <h4 class="mb-3">
                    <i class="feather icon-mail"></i>
                    KOMMENTAR
                  </h4>

                  <form id="commentForm">
                    <div id="quill-editor"></div>
                    <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
                  </form>

                  <div id="comments-section"></div>
                </div>

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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- KANBAN STATUS + ERSTVERIFIZIERUNG --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  ['Unpublished', 'progress', 'verified', 'junk'].forEach(status => {
    const col = document.getElementById('kanban-' + status);
    if (!col) return;

    new Sortable(col, {
      group: 'kanban-group',
      animation: 150,
      onAdd: function (evt) {
        const id        = evt.item.dataset.id;
        const newStatus = evt.to.dataset.status;
        const cardName  = evt.item.dataset.name || 'Anfrage'; 

        if (!id || !newStatus) return;

        // --- STATUS: VERIFIED (Logic for Verification) ---
        if (newStatus === 'verified') {
          const card = evt.item;
          const alreadyVerified = card.dataset.alreadyVerified === '1';

          // FALL 1: Bereits verifizierte Anfrage -> nur Stufe/Zustand setzen
          if (alreadyVerified) {
            updateStatus(id, 'verified');
            Swal.fire({
              icon: 'info',
              title: 'Bereits verifiziert',
              text: 'Der Typ (Lead/Lieferant/…) bleibt gleich – es wurde nur die Kanban-Stufe aktualisiert.',
              timer: 2000,
              showConfirmButton: false
            });
            return;
          }

          // FALL 2: Erstmalige Verifizierung -> Typ auswählen lassen
          const options = [
              "Lead", "Kunde", "Lieferant", "Hersteller", "Geschäftspartner",
              "Architekt", "Nachunternehmer", "Bank", "Versicherung", "Bewerber", "others"
          ];

          let optionsHtml = '';
          options.forEach(opt => {
              optionsHtml += `<option value="${opt}">${opt}</option>`;
          });

          Swal.fire({
            title: 'Anfrage verifizieren',
            html: `
              <div style="text-align:left;font-size:16px;">
                  <p>Bitte wählen Sie den Ziel-Typ für <strong>${cardName}</strong>:</p>
                  <select id="verifyKanbanOption" class="swal2-select" style="margin-top:0;">
                      ${optionsHtml}
                  </select>
              </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Verifizieren',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
               const val = document.getElementById('verifyKanbanOption').value;
               if(!val) Swal.showValidationMessage('Bitte Typ wählen');
               return val;
            }
          }).then(result => {
            if (result.isConfirmed) {
              const selectedType = result.value;

              $.ajax({
                url: `/inquiry/${id}/verify`,
                type: 'POST',
                data: {
                  _token: $('meta[name="csrf-token"]').attr('content'),
                  type: selectedType, // Controller expects 'type' or 'option'
                  option: selectedType 
                },
                success: function(response) {
                  Swal.fire({
                      icon: 'success',
                      title: 'Verifiziert',
                      text: 'Erfolgreich verifiziert.',
                      timer: 1500,
                      showConfirmButton: false
                  }).then(() => {
                      // ✅ 1. Check for explicit redirect URL from Controller
                      if (response.redirect_url) {
                          window.location.href = response.redirect_url;
                      } 
                      // ✅ 2. Fallback: If Controller sent lead_id but no URL, build it manually
                      else if (response.lead_id) {
                          window.location.href = `{{ route('new.lead.view') }}?highlight_id=${response.lead_id}`;
                      }
                      // ✅ 3. Fallback: Just reload if it wasn't a lead
                      else {
                          location.reload();
                      }
                  });
                },
                error: function(xhr) {
                  // If failed, reload to reset card position
                  if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorData = xhr.responseJSON.errors;
                    let msgs = '';
                    if (typeof errorData === 'object') {
                        msgs = Object.values(errorData).flat().join('<br>');
                    } else {
                        msgs = errorData || xhr.responseJSON.message; 
                    }
                    
                    Swal.fire({ 
                        title: 'Validierungsfehler', 
                        html: msgs, 
                        icon: 'warning' 
                    }).then(() => location.reload());
                  } else {
                    Swal.fire('Fehler', 'Etwas ist schief gelaufen', 'error')
                        .then(() => location.reload());
                  }
                }
              });
            } else {
              // Cancelled -> Reload to reset card position
              location.reload();
            }
          });

        } else {
          // --- ALL OTHER STATUSES ---
          updateStatus(id, newStatus);
        }

      }
    });
  });
});

function updateStatus(id, status) {
  fetch(`/inquiry/${id}/status`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ status })
  })
  .then(res => res.json())
  .then(res => {
    if (!res.success) {
      Swal.fire('Fehler', "Status konnte nicht gespeichert werden", 'error');
      return;
    }
    const statusMap = {
      'Unpublished': 'Neue',
      'progress':    'In Bearbeitung',
      'verified':    'Verifiziert',
      'junk':        'Junk'
    };
    const badge = document.getElementById('status');
    if (badge) badge.textContent = statusMap[status] ?? status;
  });
}
</script>

{{-- PRODUKT TAB (NEUE ZEILEN, SELECT2, MITARBEITER-ZUWEISUNG) --}}
<script>
const SERVICES      = @json($serviceList);
const PRODUCTS      = @json($products);
const DEPARTMENTS   = @json($departments);
const EMP_IMG_DIR   = "{{ asset('images/employee/') }}";
const CSRF          = '{{ csrf_token() }}';
const URL_EMPLOYEES = '{{ route("inquiry.department.employees") }}';
const URL_SAVE      = '{{ route("inquiry.products.save") }}';
const URL_DELETE    = '{{ route("inquiry.products.delete") }}';

let tabRowIndex = 0;

/* ===== helpers ===== */
const t = (s) => {
  const m = {
    complete:"Komplettlösung",
    montage:"Montage",
    product:"Produkt",
    plan:"Planung",
    maintenance:"Wartung",
    repair:"Reparatur",
    emergency:"Notdienst",
    others:"Sonstiges"
  };
  return m[(s||'').toLowerCase()] || s || '';
};

const debounce = (fn, ms=150)=>{ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; };

function formatEmployee(opt) {
  if (!opt.id) return opt.text;
  const $el = $(opt.element);
  const img = $el.data('img') ? `${EMP_IMG_DIR}/${$el.data('img')}` : '';
  const pos = $el.data('positions') || '';
  return `<div style="display:flex;align-items:center;">
            ${img
              ? `<img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">`
              : `<div class="me-2 rounded-circle" style="width:36px;height:36px;background:#e5e7eb;"></div>`}
            <div><strong>${opt.text}</strong><br><small>${pos}</small></div>
          </div>`;
}
const formatEmployeeSelection = (opt) => opt.text;

function ensureOption($sel, value, label) {
  const v = String(value);
  if (!$sel.find(`option[value="${v}"]`).length) {
    $sel.append(`<option value="${v}">${label}</option>`);
  }
}

/* ===== row template ===== */
function buildRow(index) {
  const productOpts = PRODUCTS.map(p => `<option value="${p.id}">${p.article_group}</option>`).join('');
  const deptOpts    = DEPARTMENTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('');
  return `
    <tr data-index="${index}">
      <td>
        <select name="product_id[]" class="form-select tab-product" data-index="${index}">
          <option value="">Produkt wählen</option>${productOpts}
        </select>
      </td>
      <td>
        <select name="service_id[]" class="form-select tab-service" data-index="${index}">
          <option value="">Service wählen</option>
        </select>
      </td>
      <td>
        <select name="department_id[]" class="form-select tab-department" data-index="${index}">
          <option value="">Abteilung wählen</option>${deptOpts}
        </select>
      </td>
      <td>
        <select name="employee_id[]" class="form-select tab-employee" data-index="${index}">
          <option value="">Innendienst wählen</option>
        </select>
      </td>
      <td>
        <select name="field_employee[]" class="form-select tab-field" data-index="${index}">
          <option value="">Außendienst wählen</option>
        </select>
      </td>
      <td>
        <input type="datetime-local" name="appointment_date[]" class="form-control" data-index="${index}">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger removeRow">
          <i class="feather icon-trash"></i>
        </button>
      </td>
    </tr>
  `;
}

/* ===== service + employee helpers (using new backend shape) ===== */
function fillServices($serviceSelect, productId) {
  const filtered = SERVICES.filter(s => String(s.product_id) === String(productId));
  $serviceSelect.empty().append('<option value="">Service wählen</option>');
  filtered.forEach(s => $serviceSelect.append(`<option value="${s.id}">${t(s.phase_section)}</option>`));
  $serviceSelect.trigger('change.select2');
}

function fetchEmployeesAuto({ productId, serviceId = null, departmentId = null }) {
  return $.post(URL_EMPLOYEES, {
    _token: CSRF,
    product_id: productId,
    service_id: serviceId || null,
    department_id: departmentId || null,
    stage: 'inquiry'
  });
}

function fillEmployeeSelect($select, data, emptyLabel) {
  $select.empty().append(`<option value="">${emptyLabel}</option>`);
  (data || []).forEach(emp => {
    $select.append(
      `<option value="${emp.id}" data-img="${emp.image || ''}" data-positions="${(emp.positions || []).join(', ')}">
        ${emp.name} ${emp.lastname}
       </option>`
    );
  });
  $select.trigger('change.select2');
}

/* ===== init a new dynamic row ===== */
function initRow(index) {
  const $product    = $(`.tab-product[data-index="${index}"]`);
  const $service    = $(`.tab-service[data-index="${index}"]`);
  const $department = $(`.tab-department[data-index="${index}"]`);
  const $employee   = $(`.tab-employee[data-index="${index}"]`);
  const $field      = $(`.tab-field[data-index="${index}"]`);

  [$product, $service, $department, $employee, $field].forEach($s =>
    $s.select2({ width:'100%' })
  );
  [$employee, $field].forEach($s =>
    $s.select2({
      templateResult: formatEmployee,
      templateSelection: formatEmployeeSelection,
      escapeMarkup: m => m,
      width:'100%'
    })
  );

  // Product change → load services + auto-suggest dept/service + employees
  $product.on('change', async function () {
    const pid = $(this).val();

    if (!pid) {
      $service.empty().append('<option value="">Service wählen</option>').trigger('change.select2');
      $department.val('').trigger('change.select2');
      fillEmployeeSelect($employee, [], 'Innendienst wählen');
      fillEmployeeSelect($field, [], 'Außendienst wählen');
      return;
    }

    fillServices($service, pid);

    try {
      const res = await fetchEmployeesAuto({ productId: pid });

      const did = res?.department_id ? String(res.department_id) : '';
      const sid = res?.service_id    ? String(res.service_id)    : '';

      if (did && !$department.val()) {
        const deptLabel = DEPARTMENTS.find(d => String(d.id) === did)?.department_name || `Abt. ${did}`;
        ensureOption($department, did, deptLabel);
        $department.val(did).trigger('change.select2');
      }

      if (sid) {
        const svcMeta  = SERVICES.find(s => String(s.id) === sid);
        const svcLabel = t(svcMeta?.phase_section || '');
        ensureOption($service, sid, svcLabel || `Service ${sid}`);
        $service.val(sid).trigger('change.select2');
      }

      const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
      const external = Array.isArray(res?.external_employees) && res.external_employees.length
        ? res.external_employees
        : internal;

      fillEmployeeSelect($employee, internal, 'Innendienst wählen');
      fillEmployeeSelect($field, external, 'Außendienst wählen');

    } catch (e) {
      refreshAssignees(index);
    }
  });

  // Dept / service change → refresh employees (always via API)
  const refreshAssignees = (idx) => {
    const pid = $(`.tab-product[data-index="${idx}"]`).val();
    const sid = $(`.tab-service[data-index="${idx}"]`).val();
    const did = $(`.tab-department[data-index="${idx}"]`).val();
    const $emp  = $(`.tab-employee[data-index="${idx}"]`);
    const $femp = $(`.tab-field[data-index="${idx}"]`);

    if (!pid) {
      fillEmployeeSelect($emp, [], 'Innendienst wählen');
      fillEmployeeSelect($femp, [], 'Außendienst wählen');
      return;
    }

    fetchEmployeesAuto({ productId: pid, serviceId: sid, departmentId: did }).done(res => {
      // reflect any corrected dept/service from backend
      if (res?.department_id && String(res.department_id) !== String(did || '')) {
        const newDid   = String(res.department_id);
        const deptLabel= DEPARTMENTS.find(d => String(d.id) === newDid)?.department_name || `Abt. ${newDid}`;
        ensureOption($department, newDid, deptLabel);
        $department.val(newDid).trigger('change.select2');
      }
      if (res?.service_id && String(res.service_id) !== String(sid || '')) {
        const newSid = String(res.service_id);
        const svcMeta  = SERVICES.find(s => String(s.id) === newSid);
        const svcLabel = t(svcMeta?.phase_section || '');
        ensureOption($service, newSid, svcLabel || `Service ${newSid}`);
        $service.val(newSid).trigger('change.select2');
      }

      const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
      const external = Array.isArray(res?.external_employees) && res.external_employees.length
        ? res.external_employees
        : internal;

      fillEmployeeSelect($emp,  internal, 'Innendienst wählen');
      fillEmployeeSelect($femp, external, 'Außendienst wählen');
    }).fail(() => {
      fillEmployeeSelect($emp, [], 'Innendienst wählen');
      fillEmployeeSelect($femp, [], 'Außendienst wählen');
    });
  };

  $service.on('change',  debounce(() => refreshAssignees(index)));
  $department.on('change', debounce(() => refreshAssignees(index)));
}

/* ===== add new product row ===== */
$('#addNewProductRow').on('click', function () {
  tabRowIndex++;
  $('#productRowsTab').append(buildRow(tabRowIndex));
  initRow(tabRowIndex);
});

/* ===== remove dynamic row ===== */
$(document).on('click', '.removeRow', function () {
  $(this).closest('tr').remove();
});

/* ===== delete persisted product row ===== */
$(document).on('click', '.delete-tab-product', function () {
  const rowId     = $(this).data('id');
  const productId = $(this).data('product');
  const $btn      = $(this);

  Swal.fire({
    title: 'Bist du sicher?',
    text: 'Das Produkt wird dauerhaft gelöscht.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ja, löschen!',
    cancelButtonText: 'Abbrechen'
  }).then((res) => {
    if (!res.isConfirmed) return;
    $.ajax({
      url: URL_DELETE,
      method: 'DELETE',
      data: { _token: CSRF, id: rowId, product_id: productId },
      success: () => {
        $btn.closest('tr').remove();
        Swal.fire('Gelöscht', 'Produkt wurde gelöscht.', 'success');
      },
      error: () => Swal.fire('Fehler', 'Konnte nicht löschen.', 'error')
    });
  });
});

/* ===== inline assignment (unchanged logic, uses /getAllEmployees) ===== */
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]').content;

  document.querySelectorAll('.select-employee').forEach(el => {
    el.addEventListener('click', async function () {
      const type = this.dataset.type;           // 'employee' | 'field_employee'
      const id   = this.dataset.id;             // inquiry_product_lists.id

      try {
        const res = await fetch('/getAllEmployees');
        const employees = await res.json();

        if (!employees.length) {
          return Swal.fire('Keine Mitarbeiter', 'Keine Mitarbeiter gefunden.', 'info');
        }

        let html = `<select id="employeeSelect" class="form-control" style="width:100%">`;
        employees.forEach(emp => {
          const imgSrc = emp.image
            ? `/images/employee/${emp.image}`
            : (emp.gender === 'male'
                ? '/images/gender/male.png'
                : '/images/gender/female.png');
          html += `<option value="${emp.emp_id}" data-img="${imgSrc}">
                     ${emp.name} ${emp.lastname}
                   </option>`;
        });
        html += `</select>`;

        const swal = await Swal.fire({
          title: type === 'employee'
            ? 'Innendienst auswählen'
            : 'Außendienst auswählen',
          html: html,
          confirmButtonText: 'Aktualisieren',
          cancelButtonText: 'Abbrechen',
          showCancelButton: true,
          focusConfirm: false,
          didOpen: () => {
            $('#employeeSelect').select2({
              templateResult: formatOption,
              templateSelection: formatOption,
              dropdownParent: $('.swal2-container'),
              width: '100%'
            });

            function formatOption(opt) {
              if (!opt.id) return opt.text;
              const img = $(opt.element).data('img');
              return $(
                `<span><img src="${img}" style="width:26px;height:26px;border-radius:50%;margin-right:8px;"> ${opt.text}</span>`
              );
            }
          },
          preConfirm: async () => {
            const empId = $('#employeeSelect').val();
            if (!empId) {
              Swal.showValidationMessage('Bitte einen Mitarbeiter auswählen.');
              return false;
            }
            try {
              const resp = await fetch(`/inquiry-products/${id}/update-employee`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ type, employee_id: empId })
              });
              const text = await resp.text();
              let result;
              try { result = JSON.parse(text); } catch { throw new Error('Ungültige Serverantwort.'); }
              if (result.status !== 'success') throw new Error(result.message || 'Fehler');
              return result;
            } catch (e) {
              Swal.showValidationMessage(`Fehler: ${e.message}`);
              return false;
            }
          }
        });

        if (swal.isConfirmed) {
          Swal.fire('Aktualisiert!', 'Der Mitarbeiter wurde erfolgreich geändert.', 'success')
              .then(() => location.reload());
        }

      } catch (err) {
        console.error(err);
        Swal.fire('Fehler', 'Die Mitarbeiterliste konnte nicht geladen werden.', 'error');
      }
    });
  });
});
</script>


{{-- SAVE NEW PRODUCT ROWS --}}
<script>
$('#tabProductForm').on('submit', function (e) {
  e.preventDefault();

  const payload = {
    _token: CSRF,
    inquiry_id: $('input[name="inquiry_id"]').val(),
    product_id: [],
    service_id: [],
    department_id: [],
    employee_id: [],
    field_employee: [],
    appointment_date: []
  };

  let ok = true;

  $('#productRowsTab tr[data-index]').each(function () {
    const index = $(this).data('index');
    const pid = $(`.tab-product[data-index="${index}"]`).val();
    const sid = $(`.tab-service[data-index="${index}"]`).val();
    const did = $(`.tab-department[data-index="${index}"]`).val();
    const eid = $(`.tab-employee[data-index="${index}"]`).val();
    const fid = $(`.tab-field[data-index="${index}"]`).val();
    const apd = $(`input[name="appointment_date[]"][data-index="${index}"]`).val();

    if (!pid || !sid || !did || !eid) {
      $(this).addClass('table-danger');
      ok = false;
      return;
    }
    $(this).removeClass('table-danger');

    payload.product_id.push(pid);
    payload.service_id.push(sid);
    payload.department_id.push(did);
    payload.employee_id.push(eid);
    payload.field_employee.push(fid || null);
    payload.appointment_date.push(apd || null);
  });

  if (!ok) {
    Swal.fire({
      icon:'warning',
      title:'Fehler',
      text:'Bitte füllen Sie alle Pflichtfelder aus.'
    });
    return;
  }

  $.post(URL_SAVE, payload)
    .done(res => {
      Swal.fire({
        icon:'success',
        title:'Gespeichert',
        text:res.message || 'Erfolgreich gespeichert.'
      }).then(() => location.reload());
    })
    .fail(xhr => {
      const list = xhr.responseJSON?.errors
        ? Object.values(xhr.responseJSON.errors).flat().join('\n')
        : (xhr.responseJSON?.message || 'Speichern fehlgeschlagen.');
      Swal.fire({
        icon:'error',
        title:'Fehler',
        text:list
      });
    });
});
</script>

{{-- VERIFIZIERUNGS-TAB: REVERIFY --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('reverifyForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const select = document.getElementById('verifyType');
    const type   = select ? select.value : '';

    if (!type) {
      Swal.fire('Achtung', 'Bitte neuen Verifizierungstyp wählen.', 'warning');
      return;
    }

    Swal.fire({
      title: 'Neu verifizieren?',
      text: 'Bestehende Verknüpfungen (Lead/Lieferant/Hersteller …) werden durch den neuen Typ ersetzt.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, aktualisieren',
      cancelButtonText: 'Abbrechen'
    }).then(result => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: '{{ url("inquiry/".$data->id."/reverify") }}',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        data: { type: type },
        success: function(res) {
            if (!res.success) {
                Swal.fire('Fehler', res.message || 'Neu-Verifizierung fehlgeschlagen.', 'error');
                return;
            }
            Swal.fire({
                icon: 'success',
                title: 'Aktualisiert',
                text: 'Die Anfrage wurde neu verifiziert.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // ✅ REDIRECT LOGIC FOR REVERIFY
                if (res.redirect_url) {
                    window.location.href = res.redirect_url;
                } else if (res.target && res.target.url) {
                    // Fallback to target URL if redirect_url not set
                    window.location.href = res.target.url;
                } else {
                    window.location.reload();
                }
            });
        },
        error: function(xhr) {
            let msg = 'Neu-Verifizierung fehlgeschlagen.';
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire('Fehler', msg, 'error');
        }
      });
    });
  });
});
</script>

{{-- AKTIVITÄTEN-TAB --}}
<script>
function loadInquiryNotifications(inquiryId) {
  $.ajax({
    url: `/inquiry/get/notification/${inquiryId}`,
    type: 'GET',
    success: function (html) {
      $('#notification-content').html(html);
    },
    error: function () {
      $('#notification-content').html('<div class="text-danger">Fehler beim Laden der Benachrichtigungen.</div>');
    }
  });
}
$('a[data-toggle="tab"][href="#messages-fill"]').on('shown.bs.tab', function () {
  loadInquiryNotifications("{{ $data->id }}");
});
</script>

{{-- KOMMENTAR-TAB --}}
<script>
var quill = new Quill('#quill-editor', {
  theme: 'snow',
  placeholder: 'Schreiben Sie hier Ihren Kommentar...',
  modules: {
    toolbar: [
      ['bold','italic','underline','strike'],
      [{ 'list':'ordered'},{ 'list':'bullet'}],
      [{ 'align':[] }],
      ['link'],
      ['image']
    ]
  }
});

const inquiry_id = @json($data->id);

function fetchComments() {
  $.ajax({
    url: '/inquiry/' + inquiry_id + '/comments',
    method: 'GET',
    success: function(data) {
      $('#comments-section').empty();
      data.forEach(function(comment) {
        let html = `
          <div class="card mb-3 comment" data-id="${comment.id}">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <strong>${comment.employee ? comment.employee.name : 'Unknown'}</strong>
                <small>${new Date(comment.created_at).toLocaleString()}</small>
              </div>
              <p class="card-text">${comment.comment}</p>

              ${comment.employee_id === @json(auth()->id()) ? `
                <div class="btn-group mb-2">
                  <button class="btn btn-sm btn-warning edit-button" data-id="${comment.id}">
                    <i class="feather icon-edit"></i> Bearbeiten
                  </button>
                  <button class="btn btn-sm btn-danger delete-button" data-id="${comment.id}">
                    <i class="feather icon-trash"></i> Löschen
                  </button>
                </div>` : ''}

              <div class="btn-group mb-2">
                <button class="btn btn-sm btn-success mr-2 like-button" data-id="${comment.id}">
                  <i class="feather icon-thumbs-up"></i> Gefällt mir (${comment.likes})
                </button>
                <button class="btn btn-sm btn-danger dislike-button" data-id="${comment.id}">
                  <i class="feather icon-thumbs-down"></i> Gefällt mir nicht (${comment.dislikes})
                </button>
              </div>

              <div class="mt-2">
                <button class="btn btn-sm btn-info reply-button" data-id="${comment.id}">
                  <i class="feather icon-reply"></i> Antwort
                </button>
              </div>

              <div class="replies-container" style="display:none; padding-left:20px;">
                ${Array.isArray(comment.replies) && comment.replies.length > 0 ? comment.replies.map(reply => `
                  <div class="card mt-2 comment reply" data-id="${reply.id}">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <strong>${reply.employee ? reply.employee.name : 'Unknown'}</strong>
                        <small>${new Date(reply.created_at).toLocaleString()}</small>
                      </div>
                      <p class="card-text">${reply.comment}</p>
                    </div>
                  </div>`).join('') : 'No replies yet.'}
              </div>

              ${Array.isArray(comment.replies) && comment.replies.length > 0 ? `
                <button class="btn btn-sm btn-secondary show-replies-button" data-id="${comment.id}">
                  <i class="feather icon-eye"></i> Show Replies
                </button>` : ''}
            </div>
          </div>`;
        $('#comments-section').append(html);
      });
    }
  });
}

$(document).on('click', '.show-replies-button', function() {
  const repliesContainer = $(this).closest('.comment').find('.replies-container');
  repliesContainer.toggle();
  $(this).html(repliesContainer.is(':visible')
    ? '<i class="feather icon-eye-off"></i> Hide Replies'
    : '<i class="feather icon-eye"></i> Show Replies');
});

$(document).on('click', '.reply-button', function() {
  const comment_id = $(this).data('id');
  const replyText = prompt('Write your reply:');
  if (replyText) {
    $.ajax({
      url: '/comments/' + comment_id + '/reply',
      method: 'POST',
      data: { comment: replyText },
      success: function() {
        fetchComments();
        Swal.fire('Success', 'Your reply has been posted!', 'success');
      },
      error: function() {
        Swal.fire('Error', 'There was an issue posting your reply.', 'error');
      }
    });
  }
});

$('#commentForm').submit(function(e) {
  e.preventDefault();
  const commentText = quill.root.innerHTML;
  $.ajax({
    url: '/inquiry/' + inquiry_id + '/comments',
    method: 'POST',
    data: { comment: commentText },
    success: function() {
      fetchComments();
      quill.root.innerHTML = '';
      Swal.fire('Success', 'Your comment has been posted!', 'success');
    },
    error: function() {
      Swal.fire('Error', 'There was an issue posting your comment.', 'error');
    }
  });
});

$(document).on('click', '.edit-button', function() {
  const comment_id = $(this).data('id');
  const current = $(this).closest('.comment').find('p').text();
  const newComment = prompt('Edit your comment:', current);
  if (newComment) {
    $.ajax({
      url: '/comments/' + comment_id + '/edit',
      method: 'PUT',
      data: { comment: newComment },
      success: function() {
        fetchComments();
        Swal.fire('Success', 'Your comment has been updated!', 'success');
      },
      error: function() {
        Swal.fire('Error', 'There was an issue updating your comment.', 'error');
      }
    });
  }
});

$(document).on('click', '.delete-button', function() {
  const comment_id = $(this).data('id');
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to undo this action!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '/comments/' + comment_id + '/delete',
        method: 'DELETE',
        success: function() {
          fetchComments();
          Swal.fire('Deleted!', 'Your comment has been deleted.', 'success');
        },
        error: function() {
          Swal.fire('Error', 'There was an issue deleting your comment.', 'error');
        }
      });
    }
  });
});

$(document).on('click', '.like-button', function() {
  const comment_id = $(this).data('id');
  $.ajax({
    url: '/comments/' + comment_id + '/like',
    method: 'POST',
    success: function(data) {
      $(`[data-id=${comment_id}] .like-button`)
        .html(`<i class="feather icon-thumbs-up"></i> Gefällt mir (${data.likes})`);
      Swal.fire('Success', 'You liked this comment!', 'success');
    },
    error: function() {
      Swal.fire('Error', 'There was an issue liking the comment.', 'error');
    }
  });
});

$(document).on('click', '.dislike-button', function() {
  const comment_id = $(this).data('id');
  $.ajax({
    url: '/comments/' + comment_id + '/dislike',
    method: 'POST',
    success: function(data) {
      $(`[data-id=${comment_id}] .dislike-button`)
        .html(`<i class="feather icon-thumbs-down"></i> Gefällt mir nicht (${data.dislikes})`);
      Swal.fire('Success', 'You disliked this comment!', 'success');
    },
    error: function() {
      Swal.fire('Error', 'There was an issue disliking the comment.', 'error');
    }
  });
});

fetchComments();
</script>
@endsection