 
<style>
    /* Custom style for employee dropdown image */
    .emp-avatar { width: 20px; height: 20px; object-fit: cover; border-radius: 50%; margin-right: 8px; }
    .fc-event { cursor: pointer; }
    /* Fix Select2 width in Modal */
    .select2-container { width: 100% !important; }
</style>

<style>
  .xmodal{position:fixed; inset:0; display:none; z-index:9999;}
  .xmodal.is-open{display:block;}
  .xmodal__backdrop{position:absolute; inset:0; background:rgba(0,0,0,.55);}
  .xmodal__panel{
    position:relative;
    width:min(920px, calc(100% - 32px));
    margin:48px auto;
    background:#fff;
    border-radius:16px;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
    overflow:hidden;
  }
  .xmodal__header{
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 18px;
    background:linear-gradient(90deg, #8fc73e, #94b664);
    color:#fff;
  }
  .xmodal__title{display:flex; gap:10px; align-items:center; font-weight:700;}
  .xmodal__close{
    border:0; background:transparent; color:#fff; font-size:20px; line-height:1;
    width:36px; height:36px; border-radius:10px; cursor:pointer;
  }
  .xmodal__close:hover{background:rgba(255,255,255,.16);}
  .xmodal__body{padding:18px;}
  .xmodal__footer{
    display:flex; justify-content:flex-end; gap:10px;
    padding:14px 18px; background:#f7f7fb; border-top:1px solid #eee;
  }

  .xlbl{font-weight:700; color:#222; display:block; margin-bottom:6px;}
  .xreq{color:#e11d48;}
  .xinput{
    width:100%;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:10px 12px;
    outline:none;
  }
  .xinput:focus{border-color:#93c5fd; box-shadow:0 0 0 3px rgba(59,130,246,.15);}
  .xbtn{
    border:1px solid transparent; border-radius:12px;
    padding:10px 14px; cursor:pointer; font-weight:700;
    display:inline-flex; align-items:center; gap:8px;
  }
  .xbtn--primary{background:#0d6efd; color:#fff;}
  .xbtn--primary:hover{filter:brightness(.95);}
  .xbtn--ghost{background:#fff; border-color:#e5e7eb;}
  .xbtn--ghost:hover{background:#f3f4f6;}

  /* Select2 inside custom modal */
  .select2-container{width:100% !important;}
</style>


<div class="container-fluid p-3 bg-white h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="calendar" class="me-2"></i> Termine & Planung
            </h4>
            <small class="text-muted">
                Kunde: {{ $cid }} | Produkt: {{ $product_name }}
            </small>
        </div>
        
        <div class="d-flex gap-2">
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-list-tab" data-toggle="pill" href="#pills-list" role="tab">
                        <i data-feather="list"></i> Liste
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-calendar-tab" data-toggle="pill" href="#pills-calendar" role="tab">
                        <i data-feather="calendar"></i> Kalender
                    </a>
                </li>
            </ul>
            <button class="btn btn-primary" onclick="CalendarAppointments.open()">
                <i data-feather="plus"></i> Neu
            </button>
        </div>
    </div>

    <hr class="mb-3">

    <div class="tab-content" id="pills-tabContent">
        
        <div class="tab-pane fade show active" id="pills-list" role="tabpanel">
            @if($appointments->isEmpty())
                <div class="alert alert-warning p-2">
                    <i data-feather="alert-triangle"></i> Keine Termine für dieses Produkt gefunden.
                </div>
            @else
                <div class="list-group">
                    @foreach($appointments as $app)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start flex-column flex-md-row gap-2 mb-2 border rounded">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i data-feather="bookmark" class="me-2 text-primary"></i>
                                    <strong style="font-size: 1.1em;">{{ $app->name }}</strong>
                                    @if($app->appointment_type)
                                        <span class="badge badge-light-primary ms-2" style="background-color: {{ $app->color ?? '#0d6efd' }}; color: #fff;">
                                            {{ $app->appointment_type }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-muted small mb-1">
                                    <i data-feather="clock" class="me-1"></i>
                                    {{ \Carbon\Carbon::parse($app->start_date)->format('d.m.Y') }} 
                                    {{ $app->start_time ? \Carbon\Carbon::parse($app->start_time)->format('H:i') : '' }} 
                                    –
                                    {{ $app->end_date ? \Carbon\Carbon::parse($app->end_date)->format('d.m.Y') : '' }} 
                                    {{ $app->end_time ? \Carbon\Carbon::parse($app->end_time)->format('H:i') : '' }}
                                </div>

                                @if($app->note)
                                    <div class="text-muted small mt-2 p-2 bg-light rounded">
                                        <i data-feather="file-text" class="me-1"></i> {!! nl2br(e($app->note)) !!}
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <a href="{{ url('customer/appointments/' . $app->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                    <i data-feather="external-link" class="me-1"></i> Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="pills-calendar" role="tabpanel">
            <div id="fullCalendar" style="min-height: 600px;"></div>
        </div>
    </div>
</div>

<!-- CUSTOM MODAL (no Bootstrap) -->
<div id="createAppModal" class="xmodal" aria-hidden="true">
  <div class="xmodal__backdrop" data-xmodal-close></div>

  <div class="xmodal__panel" role="dialog" aria-modal="true" aria-labelledby="xmodalTitle">
    <div class="xmodal__header">
      <div class="xmodal__title" id="xmodalTitle">
        <i data-feather="calendar"></i>
        <span>Neuen Termin erstellen</span>
      </div>

      <button type="button" class="xmodal__close" aria-label="Close" data-xmodal-close>✕</button>
    </div>

    <form action="{{ route('main_appointments.customer-modal') }}" method="POST" id="calApp_form">
      @csrf

      <input type="hidden" name="customer_id" value="{{ $cid }}">
      <input type="hidden" name="alternative_id" value="{{ $aid }}">
      <input type="hidden" name="public" value="1">
      <input type="hidden" name="type" value="appointment">
      <input type="hidden" name="contact_mode" value="new">

      @php
        $pName = $product_name ?? 'Allgemein';
        $jsonArray = [ $pName => [ (int)($cid ?? 0), (int)($pid ?? 0), (string)($aid ?? 0) ] ];
      @endphp
      <input type="hidden" name="products" value="{{ json_encode($jsonArray) }}">

      <div class="xmodal__body">
        <div class="row" style="gap:12px;">
          <div class="col-12">
            <label class="xlbl">Titel / Betreff <span class="xreq">*</span></label>
            <input type="text" name="name" class="xinput" required placeholder="z.B. Vor-Ort Begehung">
          </div>

          <div class="col-12">
            <label class="xlbl">Mitarbeiter <span class="xreq">*</span></label>

            <select name="employee_id[]" id="calApp_employee_select" class="xinput" multiple required style="width:100%">
                @foreach(($calenderEmployees ?? []) as $e)
                <option value="{{ $e->emp_id }}">
                    {{ $e->name }} {{ $e->lastname }}
                </option>
                @endforeach
            </select>
            </div>


          <div class="col-12"><hr></div>

          <div class="col-md-3 col-12">
            <label class="xlbl small">Startdatum <span class="xreq">*</span></label>
            <input type="date" name="start_date" id="calApp_start_date" class="xinput" required>
          </div>
          <div class="col-md-3 col-12">
            <label class="xlbl small">Startzeit</label>
            <input type="time" name="start_time" class="xinput" value="09:00">
          </div>
          <div class="col-md-3 col-12">
            <label class="xlbl small">Enddatum</label>
            <input type="date" name="end_date" id="calApp_end_date" class="xinput">
          </div>
          <div class="col-md-3 col-12">
            <label class="xlbl small">Endzeit</label>
            <input type="time" name="end_time" class="xinput" value="10:00">
          </div>

          <div class="col-12"><hr></div>

          <div class="col-md-6 col-12">
            <label class="xlbl small">Priorität</label>
            <select name="priority" class="xinput">
              <option value="normal" selected>🔵 Normal</option>
              <option value="high">🔴 Hoch</option>
              <option value="low">🟢 Niedrig</option>
            </select>
          </div>

          <div class="col-md-6 col-12">
            <label class="xlbl small">Durchführung</label>
            <select name="execution_type" class="xinput">
              <option value="vor_ort" selected>📍 Vor Ort</option>
              <option value="online">💻 Online</option>
              <option value="telefon">📞 Telefon</option>
            </select>
          </div>

          <div class="col-12">
            <label class="xlbl small">Notiz</label>
            <textarea name="note" class="xinput" rows="3" placeholder="Zusätzliche Informationen..."></textarea>
          </div>
        </div>
      </div>

      <div class="xmodal__footer">
        <button type="button" class="xbtn xbtn--ghost" data-xmodal-close>Abbrechen</button>
        <button type="submit" class="btn btn-primary">
          <i data-feather="save"></i> Termin Speichern
        </button>
      </div>
    </form>
  </div>
</div>
