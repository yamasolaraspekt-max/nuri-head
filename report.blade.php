@extends('admin.layouts.app')
@section('title')
Tagesbericht
@endsection

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/calendar.css')}}">

  <style>
    .missing-row { background-color: #ffe6e6 !important; }
  </style>

  <style>
.notes-drawer-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.2);opacity:0;pointer-events:none;transition:opacity .2s;}
.notes-drawer{position:fixed;top:0;right:-460px;height:100vh;width:420px;max-width:92vw;background:#fff;box-shadow:-16px 0 40px rgba(15,23,42,.15);transition:right .25s;z-index:1050;display:flex;flex-direction:column;}
.notes-drawer.open{right:0;}
.notes-drawer-backdrop.open{opacity:1;pointer-events:auto;}
.notes-header{padding:12px 16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;}
.notes-list{padding:12px 12px 80px;overflow:auto;flex:1;}
.note-item{display:flex;gap:10px;margin-bottom:12px;}
.note-avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;background:#eee;}
.note-bubble{background:#f3f4f6;border-radius:12px;padding:8px 10px;max-width:280px;}
.note-meta{font-size:11px;color:#6b7280;margin-top:2px;}
.notes-inputbar{position:absolute;bottom:0;left:0;right:0;border-top:1px solid #e5e7eb;background:#fff;padding:8px;}
</style>

<style>
.attach-drawer-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.2);opacity:0;pointer-events:none;transition:opacity .2s;}
.attach-drawer{position:fixed;top:0;right:-460px;height:100vh;width:420px;max-width:92vw;background:#fff;box-shadow:-16px 0 40px rgba(15,23,42,.15);transition:right .25s;z-index:1050;display:flex;flex-direction:column;}
.attach-drawer.open{right:0;}
.attach-drawer-backdrop.open{opacity:1;pointer-events:auto;}
.attach-header{padding:12px 16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;}
.attach-list{padding:12px 12px 100px;overflow:auto;flex:1;}
.attach-item{display:flex;align-items:center;justify-content:space-between;border:1px solid #e5e7eb;border-radius:10px;padding:8px 10px;margin-bottom:8px;}
.attach-thumb{width:56px;height:56px;border-radius:6px;border:1px solid #e5e7eb;object-fit:cover;background:#f8fafc;margin-right:10px;}
.attach-footer{position:absolute;bottom:0;left:0;right:0;border-top:1px solid #e5e7eb;background:#fff;padding:8px;}
.action-with-counter { padding-top: 8px; }
.count-badge{
  position:absolute; top:-6px; right:-6px;
  min-width:18px; height:18px; line-height:18px;
  background:#111827; color:#fff; border-radius:9999px;
  font-size:11px; text-align:center; padding:0 5px;
}
.count-badge.hidden { display:none; }

</style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">TAGESBERICHT</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bericht</li>
                <li class="breadcrumb-item active">{{ $employee_name }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrum-right">
          <div class="dropdown">
            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="feather icon-settings"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="{{ route('work.place.index') }}">Arbeitsplatz</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="row match-height justify-content-start align-items-stretch text-center mb-2" id="daily_report_row">
        <!-- Wochen-Navigation + Titel -->
        <div class="col-12 d-flex justify-content-between align-items-center mb-2">
          <button class="btn btn-sm btn-outline-secondary" id="btnPrevWeek">
            <i class="fa fa-chevron-left"></i> Vorherige Woche
          </button>
          <span class="week_title font-weight-bold"></span>
          <button class="btn btn-sm btn-outline-secondary" id="btnNextWeek">
            Nächste Woche <i class="fa fa-chevron-right"></i>
          </button>
        </div>

        <!-- Versteckte Felder -->
        <input type="hidden" id="selected_date" value="{{ $start_date }}">
        <input type="hidden" id="employee_id" value="{{ $employee_id }}">

        <!-- Tages-Karten -->
        <div class="daily_report_row d-flex flex-row flex-wrap col-md-7 p-0"></div>

        <!-- Gesamt-Karte -->
        <div class="cards text-center daily_card total_card col-md-2 ml-1 d-flex flex-column justify-content-between mb-2">
          <div class="daily_header">
            <div class="title">
              <div class="daily_date"><span class="daily_report">GESAMT</span></div>
              <div class="status_date"><i class="fa fa-circle primary" id="status"></i></div>
            </div>
          </div>
          <div class="card-content">
            <div class="card-body">
              <p class="mb-0 start-time">0 Std.</p>
              <p class="mb-0 fail_time">0 Std. fehlt</p>
              <p class="mb-0 end_time">0 Std.</p>
            </div>
          </div>
        </div>
      </div>

      <select id="filterType" class="form-control w-25 mb-2">
        <option value="">Alle Typen</option>
        <option value="Aufgabe">Aufgabe</option>
        <option value="Termin">Termin</option>
        <option value="Projekt">Projekt</option>
        <option value="Angebot">Angebot</option>
        <option value="Ticket">Ticket</option>
        <option value="Pause">Pause</option>
        <option value="Fehlend">Fehlend</option>
        <option value="Manuell">Manuell</option>
      </select>
      <input type="hidden"
        id="expected_hours_per_day"
        value="{{ number_format($expectedHoursForDay ?? 0, 2, '.', '') }}">


      <div class="row mt-2" id="daily_report_table">
        <div class="table-responsive mt-1">
          <table class="table daily_report_table">
          <thead class="table-header">
            <tr>
              <th>ZEIT</th>
              <th>STD.</th>
              <th>ARBEITSORT</th>
              <th>TYP</th>
              <th>ABR./KAT.</th> <!-- NEW -->
              <th>KUNDE</th>
              <th>BESCHREIBUNG</th>
              <th>AKTIONEN</th>
            </tr>
          </thead>

            @php $totalWorked = 0; @endphp
            <tbody id="daily_report_tbody">
              @include('admin.daily_report.report.report_rows', ['entries' => $entries, 'customers' => $customers])
            </tbody>
          </table>
        </div>
      </div>

      <div class="text-right mt-3">
        <button class="btn btn-primary" id="completeDailyReport">
          <i class="feather icon-file-text"></i> Tagesbericht erstellen (PDF)
        </button>

        <button class="btn btn-outline-secondary ml-2" id="viewReportHistory">
          <i class="feather icon-clock"></i> Bericht-Historie
        </button>
      </div>

      <div class="modal fade" id="reportHistoryModal" tabindex="-1" role="dialog" aria-labelledby="reportHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="reportHistoryLabel">Bericht-Historie</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Schließen"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <ul id="reportHistoryList" class="list-unstyled"><li>Lade Daten…</li></ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row" id="year_table"></div>
    </div>
  </div>
</div>


<div class="notes-drawer-backdrop" id="notesBackdrop"></div>
<div class="notes-drawer" id="notesDrawer" aria-hidden="true">
    <div class="notes-header">
        <div>
        <div class="font-weight-bold">Notes</div>
        <small class="text-muted" id="notesContext">—</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="notesClose">&times;</button>
    </div>

    <div class="notes-list" id="notesList">
        <div class="text-muted small">Loading…</div>
    </div>

    <div class="notes-inputbar">
        <form id="notesForm" class="d-flex align-items-center">
        @csrf
        <input type="hidden" name="date" id="notesDate">
        <input type="hidden" name="entry_id" id="notesEntry">
        <input type="text" name="message" id="notesMessage" class="form-control mr-2" placeholder="Write a note…" maxlength="2000" required>
        <button class="btn btn-primary" type="submit">Send</button>
        </form>
    </div>
</div>


<div class="attach-drawer-backdrop" id="attachBackdrop"></div>
<div class="attach-drawer" id="attachDrawer" aria-hidden="true">
  <div class="attach-header">
    <div>
      <div class="font-weight-bold">Anhänge</div>
      <small class="text-muted" id="attachContext">—</small>
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" id="attachClose">&times;</button>
  </div>

  <div class="attach-list" id="attachList">
    <div class="text-muted small">Keine Dateien.</div>
  </div>

  <div class="attach-footer">
    <form id="attachForm" class="d-flex align-items-center">
      @csrf
      <input type="hidden" name="date" id="attachDate">
      <input type="hidden" name="entry_id" id="attachEntry">
      <input type="file" id="attachFiles" name="files[]" multiple class="form-control-file mr-2" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,image/*">
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    <div id="attachPreview" class="mt-2" style="display:none"></div>
  </div>
</div>
@endsection
 @section('script')
<!-- Core libs -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
<script src="{{ asset('app-assets/js/scripts/tooltip/tooltip.js') }}"></script>

<script>
/** Canonical code -> German label */
const TYPE_I18N = {
  Task: 'Aufgabe',
  Appointment: 'Termin',
  Project: 'Projekt',
  Offer: 'Angebot',
  Problem: 'Ticket',
  Pause: 'Pause',
  Manual: 'Manuell',
  Missing: 'Fehlend'
};
/** German label -> Canonical code */
const TYPE_REV = Object.fromEntries(Object.entries(TYPE_I18N).map(([k,v])=>[v,k]));
function typeToLabel(code){ return TYPE_I18N[code] || code; }
function labelToCode(label){ return TYPE_REV[label] || label; }
</script>

<script>
/* ---------- Small helpers ---------- */
const $D = (sel, ctx=document) => ctx.querySelector(sel);
const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

function getEmployeeId() {
  return ($('#employee_id').val() || "{{ $employee_id }}").toString().trim();
}
function getSelectedDate() {
  const fromHidden = $('#selected_date').val();
  const fromQS = new URLSearchParams(location.search).get('date');
  return fromQS || fromHidden || "{{ $start_date }}";
}
function setSelectedDate(iso) {
  $('#selected_date').val(iso);
  const url = new URL(location.href);
  url.searchParams.set('date', iso);
  history.replaceState(null, '', url.toString());
}
function snap5(hhmm) {
  if (!/^\d{2}:\d{2}$/.test(hhmm)) return hhmm;
  let [h,m] = hhmm.split(':').map(Number);
  m = Math.round(m/5)*5;
  if (m===60) { h=(h+1)%24; m=0; }
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}
function diffHours(start,end) {
  if (!start || !end) return 0;
  const [sh,sm] = start.split(':').map(Number);
  const [eh,em] = end.split(':').map(Number);
  let a = sh*60+sm, b = eh*60+em, d = b-a;
  if (d<0) d += 1440; // crosses midnight
  return +(d/60).toFixed(2);
}
function rowsOverlap(aStart, aEnd, bStart, bEnd) {
  const aS = timeToMin(aStart);
  const aE = timeToMin(aEnd);
  const bS = timeToMin(bStart);
  const bE = timeToMin(bEnd);

  // classic “open interval” overlap on the same day
  // [aS,aE) and [bS,bE) overlap iff:
  return aS < bE && bS < aE;
}

function timeToMin(t){ 
  const [h,m]=t.split(':').map(Number); 
  return h*60+m; 
}

function setLoading(btn, on=true){
  if (!btn) return;
  const $btn = $(btn);
  if (on) {
    $btn.prop('disabled', true).attr('data-loading','1');
    if (!$btn.data('orig')) $btn.data('orig', $btn.html());
    $btn.html('<span class="spinner-border spinner-border-sm mr-1"></span>Please wait');
  } else {
    $btn.prop('disabled', false).removeAttr('data-loading');
    if ($btn.data('orig')) $btn.html($btn.data('orig'));
  }
}

/* ---------- Weekly ribbon + totals ---------- */
function renderWeeklyReport(employeeId) {
  const selected = moment(getSelectedDate(), 'YYYY-MM-DD');
  const monday = selected.clone().startOf('isoWeek').format('YYYY-MM-DD');
  setSelectedDate(selected.format('YYYY-MM-DD'));

  $.get(`/weekly_report/${employeeId}?date=${monday}`, function(res){
    if (!res || !Array.isArray(res.days)) return;

    const $container = $('.daily_report_row'); $container.empty();
    $('.week_title').text(`Week ${moment(monday).isoWeek()}`);
    const wk = ['Mo','Di','Mi','Do','Fr','Sa','So'];

    res.days.forEach(day=>{
      const fullDate = day.full_date;
      const wd = moment(fullDate).isoWeekday(); // 1..7
      if (wd>5) return; // hide weekend
      const isSel = fullDate === getSelectedDate();
      const badge = day.has_report ? 'success' : 'danger';
      const label = `${wk[wd-1]}, ${moment(fullDate).format('DD.MM.')}`;
      const w = Number(day.worked||0), e = Number(day.expected||0), f = Number(day.fail||0);

      $container.append(`
        <div class="cards text-center daily_card ml-1 ${isSel?'active':''}" data-date="${fullDate}">
          <div class="daily_header">
            <div class="title">
              <div class="daily_date"><span class="daily_report">${label}</span></div>
              <div class="status_date"><i class="fa fa-circle text-${badge}" title="${badge==='success'?'Report exists':'No report'}"></i></div>
            </div>
          </div>
          <div class="card-content">
            <div class="card-body">
              <p class="mb-0 start-time">${w.toFixed(2)} Std.</p>
              <p class="mb-0 fail_time">${f.toFixed(2)} Std. fehlt</p>
              <p class="mb-0 end_time">${e.toFixed(2)} Std.</p>
            </div>
          </div>
        </div>
      `);
    });

    const totalWorked = res.days.reduce((s,d)=> s + Number(d.worked||0), 0);
    const totalExpected = res.days.reduce((s,d)=> s + Number(d.expected||0), 0);
    const totalFail = Math.max(0, totalExpected - totalWorked);
    $('.total_card .start-time').text(`${totalWorked.toFixed(2)} Std.`);
    $('.total_card .fail_time').text(`${totalFail.toFixed(2)} Std. fehlt`);
    $('.total_card .end_time').text(`${totalExpected.toFixed(2)} Std.`);
  });
}

/* ---------- Day loader ---------- */
function loadDay(employeeId, iso) {
  setSelectedDate(iso);
  $('.daily_card').removeClass('active');
  $(`.daily_card[data-date="${iso}"]`).addClass('active');

  $.get(`/daily_report_reload/${employeeId}/${iso}`, function(res){
    if (!res?.success) { alert('No data.'); return; }
    $('#daily_report_table tbody').html(res.html);
    $('#worked_total').text(`${Number(res.totalWorked).toFixed(2)} Std.`);
    $('#missing_hours').text(`${Number(res.missingHours).toFixed(2)} Std. fehlt`);
    initSelects(); initAutocomplete();
    refreshAllCounters();        // <-- add
  });
}


/* ---------- Multi-customer shares ---------- */
function renderShares($row) {
  const ids = ($row.find('select.customer-multi').val() || []).map(String);
  const $box = $row.find('.customer-shares');
  const existing = new Set(
    $box.find('.customer-share').map(function () {
      return String($(this).data('id'));
    }).get()
  );

  // remove rows for unselected ids
  $box.find('.customer-share').each(function () {
    if (!ids.includes(String($(this).data('id')))) $(this).remove();
  });

  const entryId = $row.data('id') || '';
  const dateVal = $('#selected_date').val() || "{{ $start_date ?? '' }}";

  // add rows for new ids
  ids.forEach(id => {
    if (existing.has(id)) return;

    const name = $row.find(`select.customer-multi option[value="${id}"]`).text().trim();

    $box.append(`
      <div class="form-row align-items-center mb-1 customer-share" data-id="${id}">
        <div class="col-12 col-md-4">
          <small class="text-muted">${name}</small>
        </div>

        <div class="col-12 col-md-3">
          <input type="number" step="0.25" min="0"
                 name="share_hours[${id}]"
                 class="form-control "
                 placeholder="Std.">
        </div>

        <div class="col-12 col-md-5 d-flex align-items-center">
          <input type="text"
                 name="customer_note[${id}]"
                 class="form-control  mr-1"
                 placeholder="Notiz">

          <button type="button"
                  class="btn btn-icon btn-outline-secondary btn-notes mr-25"
                  title="Notizen"
                  data-date="${dateVal}"
                  data-entry="${entryId || '__null'}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"
                    stroke="currentColor" stroke-width="1.6"/>
              <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.6"/>
            </svg>
          </button>

          <button type="button"
                  class="btn btn-icon btn-outline-secondary btn-attach"
                  title="Anhänge"
                  data-date="${dateVal}"
                  data-entry="${entryId}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M21 15V7a5 5 0 0 0-10 0v10a3 3 0 0 0 6 0V8"
                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
      </div>
    `);
  });
}

/* ---------- UI init ---------- */
function initSelects(){
  $('.select2').select2({ width:'100%' });
  // multi-customer change hook
  $(document).off('change.customerMulti').on('change.customerMulti','select.customer-multi',function(){
    renderShares($(this).closest('tr'));
  });
  // render initial shares for preselected rows
  $('#daily_report_tbody tr').each(function(){
    if ($(this).find('select.customer-multi').length) renderShares($(this));
  });
}

function initAutocomplete(){
  $('.autocomplete-address').each(function(){
    if (this._gmAuto) return;
    this._gmAuto = new google.maps.places.Autocomplete(this,{
      types:['geocode'],
      componentRestrictions:{ country:"de" }
    });
  });
}

/* ---------- Validation + overlap check ---------- */
function validateRow($row){
  const sRaw = $row.find('[name="start_time"]').val()
            || $row.find('.start_time_input').val()
            || '';
  const eRaw = $row.find('[name="end_time"]').val()
            || $row.find('.end_time_input').val()
            || '';

  const s = snap5(sRaw);
  const e = snap5(eRaw);

  if (!/^\d{2}:\d{2}$/.test(s) || !/^\d{2}:\d{2}$/.test(e)) {
    return { ok:false, msg:'Time must be HH:MM' };
  }

  const rawHours =
    $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val()
    || diffHours(s,e);

  const h = Number(rawHours || 0);
  if (h <= 0) {
    return { ok:false, msg:'Hours must be > 0' };
  }

  const currentId = $row.data('id') ? String($row.data('id')) : null;
  let conflictInfo = null;

  $('#daily_report_tbody tr.daily_report_tr').each(function(){
    const $r = $(this);

    // same DOM row – ignore
    if (this === $row[0]) return;

    // ignore artificial “Missing” gaps
    if ($r.hasClass('missing-row')) return;

    // only check rows that are stored in DB (have real id)
    const otherIdRaw = $r.data('id');
    if (!otherIdRaw) return;

    const otherId = String(otherIdRaw);

    // IMPORTANT: ignore rows that represent the same DB entry
    if (currentId && otherId === currentId) return;

    const rs = $r.find('[name="start_time"],.start_time_input').val();
    const re = $r.find('[name="end_time"],.end_time_input').val();
    if (!rs || !re) return;

    if (rowsOverlap(s, e, rs, re)) {
      conflictInfo = {
        id: otherId,
        start: rs,
        end: re,
        type: $r.data('type') || ''
      };
      return false; // break .each()
    }
  });

  if (conflictInfo) {
    console.warn(
      'DailyReport overlap: trying',
      s, e, 'conflicts with entry',
      conflictInfo.id,
      '(', conflictInfo.start, '–', conflictInfo.end, ')'
    );
    return {
      ok:false,
      msg:`Time overlaps with another entry (${conflictInfo.start}–${conflictInfo.end})`
    };
  }

  // write back snapped values and hours
  $row.find('[name="start_time"],.start_time_input').val(s);
  $row.find('[name="end_time"],.end_time_input').val(e);

  if (
    !$row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val()
  ) {
    $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input')
        .val(diffHours(s,e).toFixed(2));
  }

  return { ok:true };
}

/* ---------- Reload ---------- */
function reloadReport(){
  const emp = getEmployeeId();
  const date = getSelectedDate();
    $.get(`/daily_report_reload/${emp}/${date}`, function(res){
      if (!res?.success) { Swal.fire('Error','Reload failed.','error'); return; }
      $('#daily_report_tbody').html(res.html);
      $('#worked_total').text(Number(res.totalWorked).toFixed(2).replace('.',',')+' Std.');
      $('#missing_hours').text(Number(res.missingHours).toFixed(2).replace('.',',')+' Std.');

      if (res.expectedHours !== undefined) {
          $('#expected_hours_per_day').val(Number(res.expectedHours).toFixed(2));
      }

      initSelects(); 
      initAutocomplete();
      refreshAllCounters();
  });

}

 
/* ---------- Save (supports multi-customer) ---------- */
function saveRow($row, btn, endpoint) {
  const v = validateRow($row);
  if (!v.ok) {
    Swal.fire('Hinweis', v.msg, 'warning');
    return;
  }

  // collect multi-customer data
  const ids = $row.find('select.customer-multi').val() || [];
  const shareHours   = {};
  const sharePercent = {};
  const customerNote = {};

  ids.forEach(id => {
    const h = $row.find(`[name="share_hours[${id}]"]`).val();
    const p = $row.find(`[name="share_percent[${id}]"]`).val(); // may not be rendered
    const n = $row.find(`[name="customer_note[${id}]"]`).val();

    if (h !== undefined && h !== '') shareHours[id] = h;
    if (p !== undefined && p !== '') sharePercent[id] = p;
    if (n !== undefined && n !== '') customerNote[id] = n;
  });

  // start/end time (aligned with validator: start_time / end_time)
  const start = $row.find('[name="start_time"], .start_time_input').val();
  const end   = $row.find('[name="end_time"], .end_time_input').val();

  // hours: either from field or calculated from times
  const rawHours =
    $row.find('[name="hours_spent"], [name="total_time"], .hours_spent_input').val()
    || diffHours(start, end);

  const hours = Number(rawHours || 0).toFixed(2);

  const payloadBase = {
    _token: $('meta[name="csrf-token"]').attr('content'),
    employee_id: getEmployeeId(),
    date: getSelectedDate(),

    // IMPORTANT: these match your PHP validator
    start_time: start,
    end_time:   end,

    work_place_id: $row.find('[name="work_place_id"]').val() || null,
    type: $row.find('[name="type"]').val() || 'Manual',
    description: $row.find('[name="description"], .description_input').val() || '',
    address: $row.find('[name="address"]').val() || null,

    billing_type: $row.find('[name="billing_type"]').val() || null,
    activity_category: $row.find('[name="activity_category"]').val() || null,
    is_travel: $row.find('.is_travel_input').is(':checked') ? 1 : 0,

    customer_ids: ids,
    share_hours: shareHours,
    share_percent: sharePercent,
    customer_note: customerNote,

    id: $row.data('id') || undefined
  };

  // for “missing” entries your route probably expects hours_spent,
  // for normal save it expects hours (as in your controller)
  const payload = (endpoint === 'add_missing')
    ? { ...payloadBase, hours_spent: hours }
    : { ...payloadBase, hours:       hours };

  if (btn) setLoading(btn, true);

  $.post(
    endpoint === 'add_missing'
      ? "{{ route('daily.report.add_missing') }}"
      : "{{ route('daily.report.save') }}",
    payload
  )
  .done(res => {
    if (!res?.success && endpoint === 'save') {
      Swal.fire('Error', 'Speichern fehlgeschlagen.', 'error');
      return;
    }
    Swal.fire('Gespeichert', 'Eintrag wurde gespeichert.', 'success');
    reloadReport();
  })
  .fail(xhr => {
    const msg = xhr.responseJSON?.message || 'Speichern fehlgeschlagen.';
    Swal.fire('Error', msg, 'error');
  })
  .always(() => {
    if (btn) setLoading(btn, false);
  });
}

/* ---------- Totals ---------- */
const recalcDebounced = debounce(recalculateTotals, 150);
function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms);} }
function recalculateTotals(){
  let total=0;
  $('#daily_report_tbody tr.daily_report_tr').each(function(){
    if ($(this).hasClass('missing-row')) return;
    const v = parseFloat($(this).find('[name="hours_spent"],[name="total_time"]').val()) || 0;
    total += v;
  });
  $('#worked_total').text(total.toFixed(2).replace('.',',')+' Std.');
  const expected = Number($('#expected_hours_per_day').val()||10);
  const missing = Math.max(0, expected - total);
  $('#missing_hours').text(missing.toFixed(2).replace('.',',')+' Std.');
}

/* ---------- Events ---------- */
$(document).ready(function(){
  setSelectedDate(getSelectedDate());
  renderWeeklyReport(getEmployeeId());
  initSelects(); initAutocomplete();
    refreshAllCounters(); 
  $(document).on('click', '.daily_card[data-date]', function(){
    loadDay(getEmployeeId(), $(this).data('date'));
  });

  function shiftWeek(days){
    const newDate = moment(getSelectedDate()).add(days,'days').startOf('isoWeek').format('YYYY-MM-DD');
    setSelectedDate(newDate);
    renderWeeklyReport(getEmployeeId());
    loadDay(getEmployeeId(), newDate);
  }
  $('#btnPrevWeek').on('click', ()=> shiftWeek(-7));
  $('#btnNextWeek').on('click', ()=> shiftWeek(7));
 
  $(document).on('click', '.daily_report_add button', function(){
    saveRow($(this).closest('tr'), this, 'add_missing');
  });

  // normal save button inside every data row
  $(document).on('click', '.saveRow', function(){
    const $row = $(this).closest('tr');

    // missing rows or rows without id → create
    const endpoint =
      $row.hasClass('missing-row') || !$row.data('id')
        ? 'add_missing'
        : 'save';

    saveRow($row, this, endpoint);
  });

  $(document).on('click', '.btn-save-entry', function(){
    saveRow($(this).closest('tr'), this, 'save');
  });

  $(document).on('click', '.deleteRow', function(){
    const id = $(this).data('id');
    const token = $('meta[name="csrf-token"]').attr('content');
    Swal.fire({
      title:'Are you sure?', text:'This entry will be deleted.', icon:'warning',
      showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel'
    }).then(r=>{
      if (!r.isConfirmed) return;
      setLoading(this, true);
      $.ajax({ url:`/daily_report_time/${id}`, type:'DELETE', data:{ _token:token } })
        .done(res=>{ Swal.fire('Deleted', res.message || 'Entry deleted.', 'success'); reloadReport(); })
        .fail(()=> Swal.fire('Error','Delete failed.','error'))
        .always(()=> setLoading(this, false));
    });
  });

  $(document).on('change', '[name="start_time"],[name="end_time"],.start_time_input,.end_time_input', function(){
    const $row = $(this).closest('tr');
    const s = snap5($row.find('[name="start_time"],.start_time_input').val());
    const e = snap5($row.find('[name="end_time"],.end_time_input').val());
    if (s) $row.find('[name="start_time"],.start_time_input').val(s);
    if (e) $row.find('[name="end_time"],.end_time_input').val(e);
    const h = diffHours(s,e);
    if (h>0) $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val(h.toFixed(2));
    recalcDebounced();
  });
  $(document).on('input', '[name="hours_spent"],[name="total_time"],.hours_spent_input', recalcDebounced);

  // Filter by German label present in the table
  $('#filterType').on('change', function () {
    const want = $(this).val();
    $('#daily_report_tbody tr.daily_report_tr').each(function () {
      const rawCode = $(this).data('type') || $(this).find('td:nth-child(4)').data('type') || $(this).find('td:nth-child(4)').text().trim();
      const label = typeToLabel(rawCode);
      $(this).toggle(!want || label === want);
    });
  });

  // Complete + PDF
  $(document).on('click', '#completeDailyReport', function(){
    const btn = this;
    Swal.fire({
      title:'Complete?', text:'All data will be saved and a PDF will be generated.', icon:'warning',
      showCancelButton:true, confirmButtonText:'Yes, proceed', cancelButtonText:'Cancel'
    }).then(r=>{
      if (!r.isConfirmed) return;
      setLoading(btn, true);
      $.post("{{ route('daily.report.complete') }}", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        employee_id: getEmployeeId(),
        date: getSelectedDate()
      })
      .done(res => { window.open(res.pdf_url, '_blank'); Swal.fire('Done','Daily report saved and PDF created.','success'); })
      .fail(()=> Swal.fire('Error','Save or PDF generation failed.','error'))
      .always(()=> setLoading(btn, false));
    });
  });

  // History modal
  $(document).on('click', '#viewReportHistory', function(){
    $('#reportHistoryList').html('<li>Loading…</li>');
    $('#reportHistoryModal').modal('show');
    $.get(`/daily-report/history?employee_id=${getEmployeeId()}&date=${getSelectedDate()}`, function(res){
      if (!res?.history?.length) { $('#reportHistoryList').html('<li>No report found.</li>'); return; }
      const li = res.history.map(item=>`
        <li class="mb-2">
          <i class="feather icon-calendar"></i>
          Created at: <strong>${item.created_at}</strong><br>
          <a href="${item.url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
            <i class="feather icon-download"></i> Open report
          </a>
        </li>`).join('');
      $('#reportHistoryList').html(li);
    }).fail(()=> $('#reportHistoryList').html('<li class="text-danger">Load failed.</li>'));
  });

  // Ctrl/Cmd+S quick save on the focused row
  $(document).on('keydown', function(e){
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase()==='s') {
      e.preventDefault();
      const $focusedRow = $(document.activeElement).closest('tr');
      if ($focusedRow.length) saveRow($focusedRow, null, 'add_missing');
    }
  });
});
</script>
<script>
"use strict";

/* === Notes === */
const DEFAULT_AVATAR = "{{ asset('images/gender/male.png') }}";
const EMPLOYEE_IMG_BASE = "{{ asset('images/employee') }}";
let NOTES_CTX = { date:null, entry:"__null" };

const esc = s => (s||"").replace(/[&<>"']/g,m=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[m]));
const avatarUrl = v => (!v ? DEFAULT_AVATAR : (/^https?:\/\//i.test(v)||v.startsWith("/") ? v : (EMPLOYEE_IMG_BASE+"/"+v)));

function setNoteCountOnRow(entryId, n){
  if(!entryId) return;
  const $row = $(`#daily_report_tbody tr.daily_report_tr[data-id="${entryId}"]`);
  if(!$row.length) return;
  const $b = $row.find(`.note-count[data-entry="${entryId}"]`);
  if(!$b.length) return;
  n = Number(n)||0;
  $b.text(n).toggleClass('hidden', n===0);
}
function renderNotes(list){
  if(!Array.isArray(list)||!list.length){
    $("#notesList").html('<div class="text-muted small">No notes yet.</div>');
    return;
  }
  const html = list.map(n=>`
    <div class="note-item d-flex">
      <img class="note-avatar" src="${avatarUrl(n.avatar)}" alt="">
      <div>
        <div class="note-bubble">
          <div class="small font-weight-bold">${esc(n.author||"User")}</div>
          <div>${esc(n.message||"")}</div>
        </div>
        <div class="note-meta">${esc(n.created||"")}</div>
      </div>
    </div>
  `).join("");
  $("#notesList").html(html);
  const el = document.getElementById("notesList"); if(el) el.scrollTop = el.scrollHeight;
}

function openNotes(date, entryId){
  const eid = entryId ? String(entryId) : "__null";
  NOTES_CTX = { date, entry:eid };
  $("#notesDate").val(date);
  $("#notesEntry").val(eid);
  $("#notesContext").text(`Date: ${moment(date,'YYYY-MM-DD').format('DD.MM.YYYY')} • Row: ${eid==="__null"?"—":eid}`);
  $("#notesMessage").val("");
  $("#notesList").html('<div class="text-muted small">Loading…</div>');

  $.get(`{{ route('daily.notes.index') }}`, { date, entry_id:eid })
    .done(res => {
      const items = Array.isArray(res?.data)?res.data:[];
      renderNotes(items);
      if(eid!=="__null") setNoteCountOnRow(eid, items.length);
    })
    .fail(()=> $("#notesList").html('<div class="text-danger small">Load failed.</div>'));

  $("#notesBackdrop").addClass("open");
  $("#notesDrawer").addClass("open").attr("aria-hidden","false");
}
function closeNotes(){ $("#notesBackdrop").removeClass("open"); $("#notesDrawer").removeClass("open").attr("aria-hidden","true"); }

$(document).on("click",".btn-notes",function(){
  const entryId = $(this).closest("tr").data("id") || $(this).data("entry") || "__null";
  const date = $(this).data("date") || $("#selected_date").val() || "{{ $start_date ?? '' }}";
  openNotes(date, entryId);
});
$("#notesClose, #notesBackdrop").on("click", closeNotes);

$("#notesForm").on("submit", function(e){
  e.preventDefault();
  const btn = $(this).find('button[type="submit"]')[0];
  $(btn).prop("disabled",true).html('<span class="spinner-border spinner-border-sm mr-1"></span>Saving…');

  $.post(`{{ route('daily.notes.store') }}`, $(this).serialize())
    .done(()=> $.get(`{{ route('daily.notes.index') }}`, { date:NOTES_CTX.date, entry_id:NOTES_CTX.entry })
      .done(res=>{
        const items = Array.isArray(res?.data)?res.data:[];
        renderNotes(items);
        if(NOTES_CTX.entry!=="__null") setNoteCountOnRow(NOTES_CTX.entry, items.length);
        $("#notesMessage").val("").focus();
      }))
    .fail(xhr=>{
      const msg = xhr.responseJSON?.message || "Save failed.";
      if(window.Swal) Swal.fire("Error", msg, "error"); else alert(msg);
    })
    .always(()=> $(btn).prop("disabled",false).text("Send"));
});
</script>
<script>
function setNoteCountOnRow(entryId, n){
  if(!entryId) return;
  const $b = $(`.note-count[data-entry="${entryId}"]`);
  if(!$b.length) return;
  n = Number(n)||0;
  $b.text(n);
  $b.toggleClass('hidden', n===0);
}
function setAttachCountOnRow(entryId, n){
  if(!entryId) return;
  const $row = $(`#daily_report_tbody tr.daily_report_tr[data-id="${entryId}"]`);
  if(!$row.length) return;
  const $b = $row.find(`.attach-count[data-entry="${entryId}"]`);
  if(!$b.length) return;
  n = Number(n)||0;
  $b.text(n).toggleClass('hidden', n===0);
}
/* fetch both counters for one row */
function refreshRowCounters(entryId, date){
  if(!entryId) return;
  const d = date || $('#selected_date').val() || "{{ $start_date ?? '' }}";
  $.get(`{{ route('daily.notes.index') }}`, { date:d, entry_id:String(entryId) })
    .done(res => setNoteCountOnRow(entryId, Array.isArray(res?.data)? res.data.length : 0));
  $.get(`{{ route('daily.attach.index') }}`, { date:d, entry_id:String(entryId) })
    .done(res => setAttachCountOnRow(entryId, Array.isArray(res?.attachments)? res.attachments.length : 0));
}
function refreshAllCounters(){
  const d = $('#selected_date').val() || "{{ $start_date ?? '' }}";
  $('#daily_report_tbody tr.daily_report_tr').each(function(){
    const id = $(this).data('id');
    if(id) refreshRowCounters(String(id), d);
  });
}
</script>


<script>
"use strict";

/* === Attachments === */
let ATTACH_CTX = { date:null, entry:"", note_id:null };

const escA = s => (s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const humanSize = b => { const u=['B','KB','MB','GB','TB']; let i=0,n=+b||0; while(n>=1024&&i<u.length-1){n/=1024;i++;} return (n<10&&i?n.toFixed(1):Math.round(n))+' '+u[i]; };
const fileIcon = ext => { ext=(ext||'').toLowerCase(); if(['jpg','jpeg','png','gif','webp'].includes(ext))return'🖼️'; if(ext==='pdf')return'📄'; if(['doc','docx'].includes(ext))return'📝'; if(['xls','xlsx','csv'].includes(ext))return'📊'; return'📎'; };
const isImg = m => /^image\//i.test(m||'');

function setAttachCountOnRow(entryId,n){
  if(!entryId) return;
  const $b = $('.attach-count[data-entry="'+entryId+'"]');
  if(!$b.length) return;
  n = Number(n)||0; $b.text(n).toggleClass('hidden', n===0);
}

function renderAttachList(list){
  if(!Array.isArray(list)||!list.length){
    $('#attachList').html('<div class="text-muted small">Keine Dateien.</div>');
    return;
  }
  const html = list.map(a=>{
    const url=a.url, name=escA(a.name||''), ext=escA(a.ext||''), mime=a.mime||'', size=humanSize(a.size);
    const thumb = (a.is_image||isImg(mime))
      ? `<a href="${url}" target="_blank"><img class="attach-thumb" src="${url}" alt=""></a>`
      : `<div class="attach-thumb d-flex align-items-center justify-content-center">${fileIcon(ext)}</div>`;
    return `
      <div class="attach-item" data-id="${a.id}">
        <div class="d-flex align-items-center" style="gap:10px;">
          ${thumb}
          <div>
            <div class="font-weight-500">${name}</div>
            <small class="text-muted">${ext || (mime.split('/').pop()||'')} • ${size}</small>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <a class="btn btn-sm btn-outline-primary mr-1" href="${url}" target="_blank" rel="noopener">Öffnen</a>
          <button type="button" class="btn btn-sm btn-outline-danger btn-attach-del" data-id="${a.id}">Löschen</button>
        </div>
      </div>`;
  }).join('');
  $('#attachList').html(html);
}

function openAttach(date, entryId){
  ATTACH_CTX = { date, entry: entryId||"", note_id:null };
  $('#attachDate').val(date);
  $('#attachEntry').val(entryId||'');
  $('#attachContext').text(`Datum: ${moment(date,'YYYY-MM-DD').format('DD.MM.YYYY')} • Zeile: ${entryId||'—'}`);
  $('#attachList').html('<div class="text-muted small">Laden…</div>');

  $.get(`{{ route('daily.attach.index') }}`, { date, entry_id: entryId||'' })
    .done(res=>{
      const items = res?.attachments || [];
      ATTACH_CTX.note_id = res?.note_id || null;
      renderAttachList(items);
      if(ATTACH_CTX.entry) setAttachCountOnRow(ATTACH_CTX.entry, items.length);
    })
    .fail(()=> $('#attachList').html('<div class="text-danger small">Laden fehlgeschlagen.</div>'));

  $('#attachBackdrop').addClass('open');
  $('#attachDrawer').addClass('open').attr('aria-hidden','false');
}
function closeAttach(){ $('#attachBackdrop').removeClass('open'); $('#attachDrawer').removeClass('open').attr('aria-hidden','true'); }

$(document).on('click','.btn-attach',function(){
  const entryId = $(this).closest('tr').data('id') || $(this).data('entry') || '';
  const date = $(this).data('date') || $('#selected_date').val() || "{{ $start_date ?? '' }}";
  openAttach(date, entryId);
});
$('#attachClose, #attachBackdrop').on('click', closeAttach);

$('#attachFiles').on('change', function(){
  const files = Array.from(this.files||[]);
  const $wrap = $('#attachPreview').empty(); if(!files.length) return $wrap.hide();
  files.forEach(f=>{
    const ext=(f.name.split('.').pop()||'').toLowerCase(), img=/^image\//i.test(f.type);
    const thumb = img ? `<img src="${URL.createObjectURL(f)}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #e5e7eb;">`
                      : `<span>${fileIcon(ext)}</span>`;
    $wrap.append(`<div class="border rounded px-2 py-1 d-flex align-items-center mb-1" style="gap:8px;">${thumb}<small class="text-truncate" style="max-width:260px;">${escA(f.name)}</small></div>`);
  });
  $wrap.show();
});

$('#attachForm').on('submit', function(e){
  e.preventDefault();
  const btn = $(this).find('button[type="submit"]')[0];
  const input = document.getElementById('attachFiles');
  const files = Array.from(input.files||[]);
  if(!files.length) return;

  const fd = new FormData();
  fd.append('date', $('#attachDate').val());
  fd.append('entry_id', $('#attachEntry').val());
  files.forEach(f=>fd.append('files[]', f));

  $(btn).prop('disabled',true).text('Hochladen…');
  fetch(`{{ route('daily.attach.store') }}`, {
    method:'POST',
    headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    body: fd
  })
  .then(r=>r.ok?r.json():r.json().then(Promise.reject))
  .then(()=> $.get(`{{ route('daily.attach.index') }}`, { date:ATTACH_CTX.date, entry_id:ATTACH_CTX.entry||'' }))
  .then(res=>{
    const items = res?.attachments || [];
    renderAttachList(items);
    if(ATTACH_CTX.entry) setAttachCountOnRow(ATTACH_CTX.entry, items.length);
    $('#attachFiles').val(''); $('#attachPreview').hide().empty();
  })
  .catch(err=>{
    const msg = err?.message || 'Upload fehlgeschlagen.';
    if(window.Swal) Swal.fire('Fehler', msg, 'error'); else alert(msg);
  })
  .finally(()=> $(btn).prop('disabled',false).text('Upload'));
});

$(document).on('click','.btn-attach-del', function(){
  const id = $(this).data('id'); const btn = this;
  $(btn).prop('disabled',true).text('…');
  fetch(`{{ url('/daily-attachments') }}/${id}`, {
    method:'DELETE',
    headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  })
  .then(r=>r.ok?r.json():Promise.reject())
  .then(()=> $.get(`{{ route('daily.attach.index') }}`, { date:ATTACH_CTX.date, entry_id:ATTACH_CTX.entry||'' }))
  .then(res=>{
    const items = res?.attachments || [];
    renderAttachList(items);
    if(ATTACH_CTX.entry) setAttachCountOnRow(ATTACH_CTX.entry, items.length);
  })
  .catch(()=> { if(window.Swal) Swal.fire('Fehler','Löschen fehlgeschlagen.','error'); })
  .finally(()=> $(btn).prop('disabled',false).text('Löschen'));
});
</script>


@endsection
