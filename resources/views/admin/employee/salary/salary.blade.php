@extends('admin.layouts.app')
@section('title') Lohn/Vollkosten @stop

@section('style')
<style>
  .salary-toolbar { gap: 12px; }
  .money { font-variant-numeric: tabular-nums; }
  .card-cost { font-size: 2rem; font-weight: 700; line-height: 1.1; }

  /* view toggle buttons */
  .view-toggle .btn.active { background: #0d6efd; color:#fff; border-color:#0d6efd; }

  /* cards */
  .salary-card .card-header { background: #74b2d4 !important; }
  .salary-card .card-header .text-white { color: rgba(255,255,255,.92) !important; }
  .salary-card .list-mini .list-group-item {
    padding: .5rem .75rem;
    font-size: .95rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
  }

  /* list view */
  .salary-table th, .salary-table td { vertical-align: middle; }
  .avatar-sm { width: 36px; height: 36px; object-fit: cover; border-radius: 999px; }

  /* subtle badges */
  .badge-soft { background: #74b2d4; color:white; margin-left:2px;}
  .muted { color: #6c757d; }

  /* small form spacing inside cards */
  .salary-card .form-label { font-size: .85rem; margin-bottom: .25rem; }

  /* keep controls compact */
  .toolbar-control { min-width: 160px; }
</style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row"></div>

    <div class="content-body">
      <div class="container-fluid py-3">

        {{-- =========================================================
             TOOLBAR
        ========================================================== --}}
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 salary-toolbar">
          <div class="d-flex flex-column">
            <h4 class="m-0 fw-semibold">Lohn & Vollkosten</h4>
            <div class="small muted">
              Zeitraum: <span class="fw-semibold">{{ str_pad($period['month'],2,'0',STR_PAD_LEFT) }}.{{ $period['year'] }}</span>
              @isset($stats)
                <span class="ms-2">| Neu: <span class="fw-semibold">{{ $stats['created'] ?? 0 }}</span></span>
                <span class="ms-2">| Aktualisiert: <span class="fw-semibold">{{ $stats['updated'] ?? 0 }}</span></span>
              @endisset
            </div>
          </div>

          <div class="d-flex flex-column flex-xl-row gap-2 align-items-xl-center">

            {{-- Filter --}}
            <form action="{{ route('salary.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
              <input
                type="text"
                name="search"
                placeholder="Mitarbeiter suchen…"
                value="{{ request('search') }}"
                class="form-control toolbar-control"
              >

              <select name="month" class="form-select toolbar-control">
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}" {{ (int)$period['month']===$m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                  </option>
                @endfor
              </select>

              <select name="year" class="form-select" style="width:120px;">
                @for($y=date('Y')-2;$y<=date('Y')+1;$y++)
                  <option value="{{ $y }}" {{ (int)$period['year']===$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>

              <button type="submit" class="btn btn-primary">Filtern</button>
            </form>

            {{-- View Toggle --}}
            <div class="btn-group view-toggle" role="group" aria-label="View toggle">
              <button type="button" class="btn btn-outline-secondary active" id="btnViewCards">
                <i class="fa fa-th-large me-1"></i> Cards
              </button>
              <button type="button" class="btn btn-outline-secondary" id="btnViewList">
                <i class="fa fa-list me-1"></i> List
              </button>
            </div>

          </div>
        </div>

        {{-- =========================================================
             VIEWS
        ========================================================== --}}

        {{-- ---------------------------
             CARDS VIEW
        ---------------------------- --}}
        <div id="viewCards">
          <div class="row g-3">
            @foreach($employees as $emp)
              @php
                $s = $emp->salaries->first();

                $hourly   = (float) data_get($s,'base_hourly',0);
                $weekly   = (float) data_get($s,'base_weekly',0);
                $monthly  = (float) data_get($s,'base_monthly',0);
                $yearly   = (float) data_get($s,'base_yearly',0);

                $hpw      = (int)   data_get($s,'working_hours_per_week', $emp->working_hour ?? 40);
                $wdpw     = (int)   data_get($s,'working_days_per_week', 5);
                $ct       = (string)data_get($s,'contract_type','hourly');

                $taxed    = (bool)  data_get($s,'is_taxed',true);
                $tIncome  = (float) data_get($s,'income_tax_rate_pct',21.000);
                $tSocEmp  = (float) data_get($s,'social_rate_employee_pct',19.700);
                $tSocEr   = (float) data_get($s,'social_rate_employer_pct',20.450);

                $gross    = (float) data_get($s,'gross_monthly', $monthly);
                $net      = (float) data_get($s,'net_monthly',   $monthly);
                $erTotal  = (float) data_get($s,'employer_total_monthly', $monthly);

                // extended fields (may be null if not migrated yet)
                $overheadPct = (float) data_get($s,'overhead_rate_pct', 0.0);
                $ekProd      = data_get($s,'ek_productive_hourly'); // may be null
                $prodHoursM  = data_get($s,'productive_hours_period'); // may be null
                $fullyLoaded = data_get($s,'fully_loaded_total_monthly'); // may be null

                $avatar   = $emp->image ? asset('images/employee/'.$emp->image) : asset('images/default-avatar.png');
                $sheetId  = data_get($s,'id');
              @endphp

              <div class="col-12 col-md-6 col-xl-4">
                <div class="card salary-card"
                  data-sheet-id="{{ $sheetId ?? '' }}"
                  data-emp-id="{{ $emp->id }}"
                  data-period-year="{{ $period['year'] }}"
                  data-period-month="{{ $period['month'] }}"

                  data-ct="{{ $ct }}"
                  data-hpw="{{ $hpw }}"
                  data-wdpw="{{ $wdpw }}"

                  data-taxed="{{ $taxed ? 1 : 0 }}"
                  data-income="{{ $tIncome }}"
                  data-socemp="{{ $tSocEmp }}"
                  data-socer="{{ $tSocEr }}"

                  data-hourly="{{ $hourly }}"
                  data-weekly="{{ $weekly }}"
                  data-monthly="{{ $monthly }}"
                  data-yearly="{{ $yearly }}"

                  data-gross="{{ $gross }}"
                  data-net="{{ $net }}"
                  data-ertotal="{{ $erTotal }}"

                  data-overhead="{{ $overheadPct }}"
                  data-ekprod="{{ is_null($ekProd) ? '' : $ekProd }}"
                  data-prodhoursm="{{ is_null($prodHoursM) ? '' : $prodHoursM }}"
                  data-fullyloaded="{{ is_null($fullyLoaded) ? '' : $fullyLoaded }}"
                >
                  <div class="card-header text-center" style="justify-content:center">
                    <div class="d-flex flex-column align-items-center gap-2 py-2">
                      <img src="{{ $avatar }}" alt="" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                      <div class="fw-semibold text-white">{{ $emp->name }} {{ $emp->lastname }}</div>
                      <div class="text-white small">
                        {{ str_pad($period['month'],2,'0',STR_PAD_LEFT).'.'.$period['year'] }}
                      </div>

                      <div class="mt-2">
                        <div class="small text-white">Stundenlohn</div>
                        <div class="fw-semibold money" data-text="hourly" style="font-size: 25px; color:white;">
                          € {{ number_format($hourly,2,',','.') }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card-body">
                    <div class="text-center mb-3">
                      <div class="small muted">Monatlicher Lohn (Basis)</div>
                      <div class="card-cost money" data-text="monthly">€ {{ number_format($monthly,2,',','.') }}</div>

                      <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                        <span class="badge badge-soft">
                          AG: <span class="money" data-text="ertotal">€ {{ number_format($erTotal,2,',','.') }}</span>
                        </span>
                        <span class="badge badge-soft">
                          GK: <span class="money" data-text="overhead">{{ number_format($overheadPct,1,',','.') }}%</span>
                        </span>
                        <span class="badge badge-soft">
                          EK €/h: <span class="money" data-text="ekprod">
                            {{ is_null($ekProd) ? '—' : ('€ '.number_format((float)$ekProd,2,',','.')) }}
                          </span>
                        </span>
                      </div>
                    </div>

                    <ul class="list-group list-group-flush list-mini mb-3">
                      <li class="list-group-item">
                        <span>Vertragstyp</span>
                        <strong class="text-capitalize" data-text="ct">{{ $ct }}</strong>
                      </li>
                      <li class="list-group-item">
                        <span>Std/Woche</span>
                        <strong data-text="hpw">{{ $hpw }}</strong>
                      </li>
                      <li class="list-group-item">
                        <span>Brutto / Monat</span>
                        <strong class="money" data-text="gross">€ {{ number_format($gross,2,',','.') }}</strong>
                      </li>
                      <li class="list-group-item">
                        <span>Netto / Monat</span>
                        <strong class="money" data-text="net">€ {{ number_format($net,2,',','.') }}</strong>
                      </li>
                      <li class="list-group-item">
                        <span>Vollkosten / Monat</span>
                        <strong class="money" data-text="fullyloaded">
                          {{ is_null($fullyLoaded) ? ('€ '.number_format($erTotal,2,',','.')) : ('€ '.number_format((float)$fullyLoaded,2,',','.')) }}
                        </strong>
                      </li>
                      <li class="list-group-item">
                        <span>Prod. Std (Monat)</span>
                        <strong class="money" data-text="prodhoursm">
                          {{ is_null($prodHoursM) ? '—' : number_format((float)$prodHoursM,2,',','.') }}
                        </strong>
                      </li>
                    </ul>

                    <div class="d-flex gap-2">
                      <button class="btn btn-outline-primary w-100" data-action="toggle-edit">Bearbeiten</button>
                      <button class="btn btn-primary w-100 text-white" data-action="open-details">Details</button>
                      <a class="btn btn-outline-secondary w-100" href="{{ url('next_employee/'.$emp->id) }}" title="Profil öffnen">
                        Profil
                      </a>
                    </div>

                    {{-- EDIT PANEL --}}
                    <div class="mt-3 d-none" data-panel="edit">
                      <div class="row g-2">

                        <div class="col-6">
                          <label class="form-label">Vertragstyp</label>
                          <select class="form-control" data-input="ct">
                            <option value="monthly" {{ $ct==='monthly'?'selected':'' }}>Monatsgehalt</option>
                            <option value="hourly"  {{ $ct==='hourly'?'selected':'' }}>Stundenlohn</option>
                            <option value="weekly"  {{ $ct==='weekly'?'selected':'' }}>Wochenlohn</option>
                            <option value="yearly"  {{ $ct==='yearly'?'selected':'' }}>Jahreslohn</option>
                          </select>
                        </div>

                        <div class="col-6">
                          <label class="form-label">Std/Woche</label>
                          <input type="number" class="form-control" min="1" step="1" data-input="hpw" value="{{ $hpw }}">
                        </div>

                        <div class="col-6">
                          <label class="form-label">Arbeitstage/Woche</label>
                          <input type="number" class="form-control" min="1" max="7" step="1" data-input="wdpw" value="{{ $wdpw }}">
                        </div>

                        <div class="col-6">
                          <label class="form-label">Gemeinkosten (%)</label>
                          <input type="number" class="form-control" step="0.001" data-input="overhead" value="{{ number_format($overheadPct,3,'.','') }}">
                        </div>

                        <div class="col-6">
                          <label class="form-label">Stundenlohn (€)</label>
                          <input type="number" class="form-control" step="0.01" data-input="hourly" value="{{ number_format($hourly,2,'.','') }}">
                        </div>

                        <div class="col-6">
                          <label class="form-label">Monatslohn (€)</label>
                          <input type="number" class="form-control" step="0.01" data-input="monthly" value="{{ number_format($monthly,2,'.','') }}">
                        </div>

                        <div class="col-12">
                          <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="taxed-{{ $emp->id }}" data-input="taxed" {{ $taxed?'checked':'' }}>
                            <label class="form-check-label" for="taxed-{{ $emp->id }}">Steuern/Abgaben berechnen</label>
                          </div>
                        </div>

                        <div class="col-12" data-wrap="tax" {{ $taxed ? '' : 'style=display:none;' }}>
                          <div class="row g-2">
                            <div class="col-md-4">
                              <label class="form-label">Steuer (%)</label>
                              <input type="number" step="0.001" class="form-control" data-input="income" value="{{ number_format($tIncome,3,'.','') }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Sozial (AN %)</label>
                              <input type="number" step="0.001" class="form-control" data-input="socemp" value="{{ number_format($tSocEmp,3,'.','') }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Sozial (AG %)</label>
                              <input type="number" step="0.001" class="form-control" data-input="socer" value="{{ number_format($tSocEr,3,'.','') }}">
                            </div>
                          </div>
                        </div>

                        {{-- LIVE SUMMARY --}}
                        <div class="col-12">
                          <div class="p-3 rounded-3 border bg-white">
                            <div class="d-flex flex-column gap-2">
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Brutto</span>
                                <span class="fw-bold money" data-live="gross">€ {{ number_format($gross,2,',','.') }}</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Abzüge AN</span>
                                <span class="fw-bold money" data-live="empded">€ 0,00</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Netto</span>
                                <span class="fw-bold money" data-live="net">€ {{ number_format($net,2,',','.') }}</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">AG-Beitrag</span>
                                <span class="fw-bold money" data-live="ercontrib">€ 0,00</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">AG-Gesamt</span>
                                <span class="fw-bold money" data-live="ertotal">€ {{ number_format($erTotal,2,',','.') }}</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Vollkosten (mit GK)</span>
                                <span class="fw-bold money" data-live="fullyloaded">—</span>
                              </div>
                              <div class="d-flex justify-content-between">
                                <span class="fw-semibold">EK €/h (prod)</span>
                                <span class="fw-bold money" data-live="ekprod">—</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                          <button class="btn btn-success w-100" data-action="save">Speichern</button>
                        </div>
                      </div>
                    </div>
                    {{-- /EDIT PANEL --}}
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-4">
            {{ $employees->links() }}
          </div>
        </div>

        {{-- ---------------------------
             LIST VIEW
        ---------------------------- --}}
        <div id="viewList" class="d-none">
          <div class="card">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0 salary-table">
                  <thead class="table-light">
                    <tr>
                      <th>Mitarbeiter</th>
                      <th class="text-end">Std/Woche</th>
                      <th class="text-end">Vertrag</th>
                      <th class="text-end">Std-Lohn</th>
                      <th class="text-end">Basis/Monat</th>
                      <th class="text-end">AG/Monat</th>
                      <th class="text-end">GK</th>
                      <th class="text-end">Vollkosten</th>
                      <th class="text-end">Prod.Std</th>
                      <th class="text-end">EK €/h</th>
                      <th class="text-end"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($employees as $emp)
                      @php
                        $s = $emp->salaries->first();
                        $avatar   = $emp->image ? asset('images/employee/'.$emp->image) : asset('images/default-avatar.png');

                        $hourly   = (float) data_get($s,'base_hourly',0);
                        $monthly  = (float) data_get($s,'base_monthly',0);
                        $ct       = (string)data_get($s,'contract_type','hourly');
                        $hpw      = (int) data_get($s,'working_hours_per_week', $emp->working_hour ?? 40);

                        $erTotal  = (float) data_get($s,'employer_total_monthly', $monthly);

                        $overheadPct = (float) data_get($s,'overhead_rate_pct', 0.0);
                        $fullyLoaded = data_get($s,'fully_loaded_total_monthly');
                        $prodHoursM  = data_get($s,'productive_hours_period');
                        $ekProd      = data_get($s,'ek_productive_hourly');
                      @endphp
                      <tr>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <img src="{{ $avatar }}" class="avatar-sm" alt="">
                            <div>
                              <div class="fw-semibold">{{ $emp->name }} {{ $emp->lastname }}</div>
                              <div class="small muted">{{ str_pad($period['month'],2,'0',STR_PAD_LEFT).'.'.$period['year'] }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="text-end">{{ $hpw }}</td>
                        <td class="text-end text-capitalize">{{ $ct }}</td>
                        <td class="text-end money">€ {{ number_format($hourly,2,',','.') }}</td>
                        <td class="text-end money">€ {{ number_format($monthly,2,',','.') }}</td>
                        <td class="text-end money">€ {{ number_format($erTotal,2,',','.') }}</td>
                        <td class="text-end">{{ number_format($overheadPct,1,',','.') }}%</td>
                        <td class="text-end money">
                          {{ is_null($fullyLoaded) ? '—' : ('€ '.number_format((float)$fullyLoaded,2,',','.')) }}
                        </td>
                        <td class="text-end">
                          {{ is_null($prodHoursM) ? '—' : number_format((float)$prodHoursM,2,',','.') }}
                        </td>
                        <td class="text-end money">
                          {{ is_null($ekProd) ? '—' : ('€ '.number_format((float)$ekProd,2,',','.')) }}
                        </td>
                        <td class="text-end">
                          <a class="btn btn-sm btn-outline-secondary" href="{{ url('next_employee/'.$emp->id) }}">Profil</a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="mt-4">
            {{ $employees->links() }}
          </div>
        </div>

        {{-- =========================================================
             DETAILS MODAL
        ========================================================== --}}
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 id="dTitle" class="modal-title">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="p-3 rounded-3 border">
                      <div class="text-uppercase small muted mb-2">Arbeitszeiten</div>
                      <div>Std/Woche: <strong id="dHPW"></strong></div>
                      <div>Std/Tag: <strong id="dHPD"></strong></div>
                      <div>Geplanter Monat (Std): <strong id="dPlannedM"></strong></div>
                      <div>Prod. Std (Monat): <strong id="dProdM"></strong></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-3 rounded-3 border">
                      <div class="text-uppercase small muted mb-2">Kosten</div>
                      <div>Stundenlohn: <strong id="dBH"></strong></div>
                      <div>Monatslohn (Basis): <strong id="dBM"></strong></div>
                      <div>Brutto/Monat: <strong id="dGross"></strong></div>
                      <div>Netto/Monat: <strong id="dNet"></strong></div>
                      <div>AG-Gesamt/Monat: <strong id="dER"></strong></div>
                      <div>Vollkosten/Monat: <strong id="dFL"></strong></div>
                      <div>EK €/h (prod): <strong id="dEK"></strong></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Schließen</button>
              </div>
            </div>
          </div>
        </div>

      </div> {{-- container --}}
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  // ============================
  // View Toggle persistence
  // ============================
  (function(){
    const KEY = 'salary_view_mode';
    const btnCards = document.getElementById('btnViewCards');
    const btnList  = document.getElementById('btnViewList');
    const viewCards = document.getElementById('viewCards');
    const viewList  = document.getElementById('viewList');

    function setMode(mode){
      const cards = mode === 'cards';
      viewCards.classList.toggle('d-none', !cards);
      viewList.classList.toggle('d-none', cards);

      btnCards.classList.toggle('active', cards);
      btnList.classList.toggle('active', !cards);

      try { localStorage.setItem(KEY, mode); } catch(e){}
    }

    btnCards?.addEventListener('click', ()=> setMode('cards'));
    btnList?.addEventListener('click',  ()=> setMode('list'));

    let initial = 'cards';
    try { initial = localStorage.getItem(KEY) || 'cards'; } catch(e){}
    setMode(initial);
  })();
</script>

<script>
  // ============================================================
  // Salary Cards Logic (derived + tax + overhead + EK)
  // ============================================================
  const UPSERT_URL = "{{ route('salary_sheets.upsert') }}";
  const TAX_URL_TPL = "{{ route('employees.tax_defaults', ['employee' => '__ID__']) }}";

  const nf = new Intl.NumberFormat('de-DE', { style:'currency', currency:'EUR', minimumFractionDigits:2 });
  const WEEKS_PER_YEAR = 52.1429;
  const MONTHS_PER_YEAR = 12;
  const AVG_WEEKS_PER_MONTH = WEEKS_PER_YEAR / MONTHS_PER_YEAR;

  const DEFAULT_INCOME = 21.000;
  const DEFAULT_SOCEMP = 19.700;
  const DEFAULT_SOCER  = 20.450;

  function fmtMoney(v){ return nf.format(+v||0); }
  function round2(v){ return Math.round((+v + Number.EPSILON) * 100)/100; }
  function round3(v){ return Math.round((+v + Number.EPSILON) * 1000)/1000; }

  function getState(card){
    return {
      sheet_id: card.dataset.sheetId || null,
      emp_id: +card.dataset.empId,
      period_year: +card.dataset.periodYear,
      period_month: +card.dataset.periodMonth,

      ct: card.dataset.ct || 'hourly',
      hpw: +card.dataset.hpw || 40,
      wdpw: +card.dataset.wdpw || 5,

      taxed: (+card.dataset.taxed)===1,
      income: parseFloat(card.dataset.income ?? DEFAULT_INCOME),
      socemp: parseFloat(card.dataset.socemp ?? DEFAULT_SOCEMP),
      socer:  parseFloat(card.dataset.socer  ?? DEFAULT_SOCER),

      hourly:  parseFloat(card.dataset.hourly ?? '0'),
      weekly:  parseFloat(card.dataset.weekly ?? '0'),
      monthly: parseFloat(card.dataset.monthly ?? '0'),
      yearly:  parseFloat(card.dataset.yearly ?? '0'),

      gross: parseFloat(card.dataset.gross ?? card.dataset.monthly ?? '0'),
      net:   parseFloat(card.dataset.net   ?? card.dataset.monthly ?? '0'),
      ertotal: parseFloat(card.dataset.ertotal ?? card.dataset.monthly ?? '0'),

      overhead: parseFloat(card.dataset.overhead ?? '0'),
      fullyloaded: parseFloat(card.dataset.fullyloaded ?? '0') || null,
      prodhoursm: parseFloat(card.dataset.prodhoursm ?? '0') || null,
      ekprod: parseFloat(card.dataset.ekprod ?? '0') || null,
    };
  }

  function bindInputs(card){
    return {
      ct:      card.querySelector('[data-input="ct"]'),
      hpw:     card.querySelector('[data-input="hpw"]'),
      wdpw:    card.querySelector('[data-input="wdpw"]'),
      overhead:card.querySelector('[data-input="overhead"]'),

      hourly:  card.querySelector('[data-input="hourly"]'),
      monthly: card.querySelector('[data-input="monthly"]'),

      taxed:   card.querySelector('[data-input="taxed"]'),
      income:  card.querySelector('[data-input="income"]'),
      socemp:  card.querySelector('[data-input="socemp"]'),
      socer:   card.querySelector('[data-input="socer"]'),
    };
  }

  function showEdit(card, show){
    const panel = card.querySelector('[data-panel="edit"]');
    if (!panel) return;
    panel.classList.toggle('d-none', !show);
    const btn = card.querySelector('[data-action="toggle-edit"]');
    if (btn) btn.textContent = show ? 'Abbrechen' : 'Bearbeiten';
  }

  function deriveAll(s){
    const hpw = Math.max(1, parseInt(s.hpw||40,10));
    let h=s.hourly, w=s.weekly, m=s.monthly, y=s.yearly;

    if (s.ct==='hourly' && h>0){ w=h*hpw; m=w*AVG_WEEKS_PER_MONTH; y=m*MONTHS_PER_YEAR; }
    else if (s.ct==='weekly' && w>0){ h= hpw ? (w/hpw) : 0; m=w*AVG_WEEKS_PER_MONTH; y=m*MONTHS_PER_YEAR; }
    else if (s.ct==='monthly' && m>0){ w=m/AVG_WEEKS_PER_MONTH; h= hpw ? (w/hpw) : 0; y=m*MONTHS_PER_YEAR; }
    else if (s.ct==='yearly'  && y>0){ m=y/MONTHS_PER_YEAR; w=m/AVG_WEEKS_PER_MONTH; h= hpw ? (w/hpw) : 0; }

    s.hourly  = round2(h);
    s.weekly  = round2(w);
    s.monthly = round2(m);
    s.yearly  = round2(y);
    return s;
  }

  function applyTax(s){
    const base = s.monthly;
    let empDed=0, net=base, erC=0, erT=base;

    if(s.taxed && base>0){
      empDed = base * ((+s.income + +s.socemp)/100.0);
      net = base - empDed;
      erC = base * (+s.socer/100.0);
      erT = base + erC;
    }

    s.gross = round2(base);
    s.empded = round2(empDed);
    s.net = round2(net);
    s.ercontrib = round2(erC);
    s.ertotal = round2(erT);
    return s;
  }

  // Simple monthly KPI approximation (client-side):
  // planned hours: hpw * AVG_WEEKS_PER_MONTH
  // productive hours: effective * (1 - unproductive%)  -> here we use 20% fixed if server didn't provide
  function applyOverheadAndEk(s){
    const overheadRate = Math.max(0, (+s.overhead||0)) / 100.0;

    // if server already computed fullyloaded / prodhoursm / ekprod, keep it unless missing
    const planned = round3((+s.hpw||0) * AVG_WEEKS_PER_MONTH);
    const hoursPerDay = (+s.wdpw||5) ? ((+s.hpw||0) / (+s.wdpw||5)) : 0;

    // client fallback: no absences => effective=planned; unproductive 20%
    const effective = planned;
    const prod = round3(effective * 0.80);

    const baseEmployer = (+s.ertotal||0);
    const overheadAmount = baseEmployer * overheadRate;
    const fully = baseEmployer + overheadAmount;

    s.prodhoursm = s.prodhoursm ?? prod;
    s.fullyloaded = s.fullyloaded ?? round2(fully);
    s.ekprod = s.ekprod ?? (s.prodhoursm > 0 ? (s.fullyloaded / s.prodhoursm) : null);

    s._plannedMonth = planned;
    s._hoursPerDay = round3(hoursPerDay);
    return s;
  }

  function renderCard(card, s){
    const safeSet = (sel, txt) => { const el = card.querySelector(sel); if (el) el.textContent = txt; };

    safeSet('[data-text="hourly"]', '€ ' + fmtMoney(s.hourly));
    safeSet('[data-text="monthly"]', '€ ' + fmtMoney(s.monthly));
    safeSet('[data-text="ct"]', s.ct);
    safeSet('[data-text="hpw"]', s.hpw);
    safeSet('[data-text="gross"]', '€ ' + fmtMoney(s.gross));
    safeSet('[data-text="net"]', '€ ' + fmtMoney(s.net));
    safeSet('[data-text="ertotal"]', '€ ' + fmtMoney(s.ertotal));
    safeSet('[data-text="overhead"]', (+(s.overhead||0)).toFixed(1).replace('.', ',') + '%');

    safeSet('[data-text="fullyloaded"]', s.fullyloaded==null ? '—' : ('€ ' + fmtMoney(s.fullyloaded)));
    safeSet('[data-text="prodhoursm"]', s.prodhoursm==null ? '—' : (Number(s.prodhoursm).toFixed(2).replace('.', ',')));
    safeSet('[data-text="ekprod"]', s.ekprod==null ? '—' : ('€ ' + fmtMoney(s.ekprod)));

    // live summary
    const live = {
      gross: card.querySelector('[data-live="gross"]'),
      empded: card.querySelector('[data-live="empded"]'),
      net: card.querySelector('[data-live="net"]'),
      ercontrib: card.querySelector('[data-live="ercontrib"]'),
      ertotal: card.querySelector('[data-live="ertotal"]'),
      fullyloaded: card.querySelector('[data-live="fullyloaded"]'),
      ekprod: card.querySelector('[data-live="ekprod"]'),
    };
    if (live.gross) live.gross.textContent = '€ ' + fmtMoney(s.gross);
    if (live.empded) live.empded.textContent = '€ ' + fmtMoney(s.empded || 0);
    if (live.net) live.net.textContent = '€ ' + fmtMoney(s.net);
    if (live.ercontrib) live.ercontrib.textContent = '€ ' + fmtMoney(s.ercontrib || 0);
    if (live.ertotal) live.ertotal.textContent = '€ ' + fmtMoney(s.ertotal);
    if (live.fullyloaded) live.fullyloaded.textContent = s.fullyloaded==null ? '—' : ('€ ' + fmtMoney(s.fullyloaded));
    if (live.ekprod) live.ekprod.textContent = s.ekprod==null ? '—' : ('€ ' + fmtMoney(s.ekprod));

    // persist back into dataset for save/details
    card.dataset.ct = s.ct;
    card.dataset.hpw = s.hpw;
    card.dataset.wdpw = s.wdpw;
    card.dataset.taxed = s.taxed ? 1 : 0;
    card.dataset.income = s.income;
    card.dataset.socemp = s.socemp;
    card.dataset.socer  = s.socer;

    card.dataset.hourly  = s.hourly;
    card.dataset.weekly  = s.weekly;
    card.dataset.monthly = s.monthly;
    card.dataset.yearly  = s.yearly;

    card.dataset.gross   = s.gross;
    card.dataset.net     = s.net;
    card.dataset.ertotal = s.ertotal;

    card.dataset.overhead = s.overhead ?? 0;
    card.dataset.fullyloaded = s.fullyloaded ?? '';
    card.dataset.prodhoursm = s.prodhoursm ?? '';
    card.dataset.ekprod = s.ekprod ?? '';
  }

  function rebuildFromInputs(card){
    const s = getState(card);
    const inp = bindInputs(card);

    s.ct = inp.ct?.value || s.ct;
    s.hpw = parseInt(inp.hpw?.value || s.hpw || '40', 10);
    s.wdpw = parseInt(inp.wdpw?.value || s.wdpw || '5', 10);
    s.overhead = parseFloat(inp.overhead?.value || s.overhead || '0');

    const hUser = parseFloat(inp.hourly?.value || '0');
    const mUser = parseFloat(inp.monthly?.value || '0');

    // authoritative input based on contract_type
    if (s.ct === 'hourly') s.hourly = hUser;
    else if (s.ct === 'monthly') s.monthly = mUser;
    else if (s.ct === 'weekly') s.weekly = (mUser>0 ? (mUser/AVG_WEEKS_PER_MONTH) : 0);
    else if (s.ct === 'yearly') s.yearly = mUser * MONTHS_PER_YEAR;

    s.taxed = !!inp.taxed?.checked;
    s.income = parseFloat(inp.income?.value || DEFAULT_INCOME);
    s.socemp = parseFloat(inp.socemp?.value || DEFAULT_SOCEMP);
    s.socer  = parseFloat(inp.socer?.value  || DEFAULT_SOCER);

    deriveAll(s);
    applyTax(s);

    // if user changes overhead locally, recompute EK locally (server will recompute authoritatively on save)
    s.fullyloaded = null;
    s.prodhoursm = null;
    s.ekprod = null;
    applyOverheadAndEk(s);

    renderCard(card, s);
  }

  async function loadTaxDefaults(card){
    const empId = card.dataset.empId;
    if (!empId) return;

    const url = TAX_URL_TPL.replace('__ID__', empId);
    const res = await fetch(url, { headers: { 'Accept':'application/json' }});
    if (!res.ok) throw new Error('HTTP '+res.status);
    const json = await res.json();

    const inp = bindInputs(card);
    if (inp.income) inp.income.value = (json.income_tax_rate_pct ?? DEFAULT_INCOME).toFixed(3);
    if (inp.socemp) inp.socemp.value = (json.social_rate_employee_pct ?? DEFAULT_SOCEMP).toFixed(3);
    if (inp.socer)  inp.socer.value  = (json.social_rate_employer_pct ?? DEFAULT_SOCER).toFixed(3);

    if (inp.taxed && !inp.taxed.checked) {
      inp.taxed.checked = true;
      const wrap = card.querySelector('[data-wrap="tax"]');
      if (wrap) wrap.style.display = '';
    }

    rebuildFromInputs(card);
  }

  function showToast(msg, isError=false){
    const el = document.createElement('div');
    el.className = 'toast align-items-center border-0 position-fixed bottom-0 end-0 m-3 ' + (isError ? 'text-bg-danger' : 'text-bg-success');
    el.role = 'alert';
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    document.body.appendChild(el);
    const t = new bootstrap.Toast(el, { delay: 2200 });
    t.show();
    el.addEventListener('hidden.bs.toast', ()=> el.remove());
  }

  async function saveCard(card){
    const s = getState(card);
    deriveAll(s); applyTax(s);

    const overhead = parseFloat(card.dataset.overhead || '0') || 0;

    const payload = {
      sheet_id: card.dataset.sheetId || null,
      emp_id: s.emp_id,
      period_year: s.period_year,
      period_month: s.period_month,

      contract_type: s.ct,
      working_hours_per_week: s.hpw,
      working_days_per_week: s.wdpw,

      base_hourly:  s.hourly.toFixed(2),
      base_weekly:  s.weekly.toFixed(2),
      base_monthly: s.monthly.toFixed(2),
      base_yearly:  s.yearly.toFixed(2),

      is_taxed: s.taxed ? 1 : 0,
      tax_source: 'employee_profile',
      income_tax_rate_pct: s.income,
      social_rate_employee_pct: s.socemp,
      social_rate_employer_pct: s.socer,

      gross_monthly: s.gross,
      employee_deductions_monthly: s.empded || 0,
      net_monthly: s.net,
      employer_contrib_monthly: s.ercontrib || 0,
      employer_total_monthly: s.ertotal,

      // extended (if your controller accepts them)
      overhead_rate_pct: overhead,

      currency: 'EUR'
    };

    const res = await fetch(UPSERT_URL, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();

    if (json?.sheet_id) card.dataset.sheetId = json.sheet_id;

    // If server returns computed fields, refresh dataset
    if (json?.data){
      const d = json.data;
      card.dataset.ct = d.contract_type ?? card.dataset.ct;
      card.dataset.hpw = d.working_hours_per_week ?? card.dataset.hpw;
      card.dataset.wdpw = d.working_days_per_week ?? card.dataset.wdpw;

      card.dataset.hourly  = d.base_hourly ?? card.dataset.hourly;
      card.dataset.weekly  = d.base_weekly ?? card.dataset.weekly;
      card.dataset.monthly = d.base_monthly ?? card.dataset.monthly;
      card.dataset.yearly  = d.base_yearly ?? card.dataset.yearly;

      card.dataset.gross   = d.gross_monthly ?? card.dataset.gross;
      card.dataset.net     = d.net_monthly ?? card.dataset.net;
      card.dataset.ertotal = d.employer_total_monthly ?? card.dataset.ertotal;

      if (d.overhead_rate_pct != null) card.dataset.overhead = d.overhead_rate_pct;

      if (d.fully_loaded_total_monthly != null) card.dataset.fullyloaded = d.fully_loaded_total_monthly;
      if (d.productive_hours_period != null) card.dataset.prodhoursm = d.productive_hours_period;
      if (d.ek_productive_hourly != null) card.dataset.ekprod = d.ek_productive_hourly;
    }

    // re-render
    let st = getState(card);
    deriveAll(st); applyTax(st); applyOverheadAndEk(st); renderCard(card, st);
    showEdit(card, false);
    showToast('Gespeichert');
  }

  function openDetails(card){
    const s = getState(card);
    deriveAll(s); applyTax(s); applyOverheadAndEk(s);

    const hpDay = s.wdpw ? (s.hpw / s.wdpw) : s.hpw;
    const plannedMonth = s._plannedMonth ?? round3(s.hpw * AVG_WEEKS_PER_MONTH);

    document.getElementById('dTitle').textContent = 'Details – ' + (card.querySelector('.fw-semibold')?.textContent.trim() || 'Mitarbeiter');
    document.getElementById('dHPW').textContent   = s.hpw;
    document.getElementById('dHPD').textContent   = round3(hpDay).toFixed(2).replace('.', ',');
    document.getElementById('dPlannedM').textContent = plannedMonth.toFixed(2).replace('.', ',');
    document.getElementById('dProdM').textContent = (s.prodhoursm==null ? '—' : Number(s.prodhoursm).toFixed(2).replace('.', ','));

    document.getElementById('dBH').textContent    = fmtMoney(s.hourly);
    document.getElementById('dBM').textContent    = fmtMoney(s.monthly);
    document.getElementById('dGross').textContent = fmtMoney(s.gross);
    document.getElementById('dNet').textContent   = fmtMoney(s.net);
    document.getElementById('dER').textContent    = fmtMoney(s.ertotal);
    document.getElementById('dFL').textContent    = (s.fullyloaded==null ? '—' : fmtMoney(s.fullyloaded));
    document.getElementById('dEK').textContent    = (s.ekprod==null ? '—' : fmtMoney(s.ekprod));

    new bootstrap.Modal(document.getElementById('detailsModal')).show();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.salary-card').forEach(card => {
      // initial render
      const s = getState(card);
      deriveAll(s); applyTax(s); applyOverheadAndEk(s); renderCard(card, s);

      const inputs = bindInputs(card);

      // toggle edit
      card.querySelector('[data-action="toggle-edit"]')?.addEventListener('click', async () => {
        const panel = card.querySelector('[data-panel="edit"]');
        const becomingVisible = panel?.classList.contains('d-none');
        showEdit(card, becomingVisible);

        // load tax defaults when edit opens (if taxed on)
        if (becomingVisible) {
          const taxed = card.querySelector('[data-input="taxed"]');
          if (taxed && taxed.checked) {
            try { await loadTaxDefaults(card); } catch(e) { /* ignore */ }
          }
          rebuildFromInputs(card);
        }
      });

      // show/hide tax
      inputs.taxed?.addEventListener('change', async () => {
        const wrap = card.querySelector('[data-wrap="tax"]');
        if (wrap) wrap.style.display = inputs.taxed.checked ? '' : 'none';

        if (inputs.taxed.checked) {
          try { await loadTaxDefaults(card); } catch(e) {}
        }
        rebuildFromInputs(card);
      });

      // recompute on changes
      [inputs.ct, inputs.hpw, inputs.wdpw, inputs.overhead, inputs.hourly, inputs.monthly, inputs.income, inputs.socemp, inputs.socer]
        .forEach(inp => inp && inp.addEventListener('input', () => rebuildFromInputs(card)));

      // details
      card.querySelector('[data-action="open-details"]')?.addEventListener('click', () => openDetails(card));

      // save
      card.querySelector('[data-action="save"]')?.addEventListener('click', async () => {
        try { await saveCard(card); }
        catch(e){ console.error(e); showToast('Speichern fehlgeschlagen', true); }
      });
    });
  });
</script>
@endsection
