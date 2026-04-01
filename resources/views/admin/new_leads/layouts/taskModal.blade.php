{{-- =========================
  CUSTOM (NON-BOOTSTRAP) TASK MODAL (CROSS-BROWSER)
  - centered
  - scrollable body (works in all browsers)
  - palette: #74b2d4, #c0d8ea, #93c21c, #cfe09b
  - keeps your existing IDs/names
  - no bootstrap classes
========================= --}}

<style>
  :root{
    --pri:#74b2d4; --pri-soft:#c0d8ea; --grn:#93c21c; --grn-soft:#cfe09b;
    --txt:#0f172a; --muted:rgba(15,23,42,.62);
    --bd:rgba(15,23,42,.12); --bd2:rgba(15,23,42,.08);
    --shadow:0 26px 80px rgba(0,0,0,.30); --shadow2:0 14px 30px rgba(15,23,42,.14);
    --r:18px;
  }

  /* Overlay (use fixed + flex centering; no fancy selectors needed) */
  #taskModal.tm-overlay{
    position:fixed;
    top:0; left:0; right:0; bottom:0;
    z-index:9999;
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px 14px;
    background:rgba(11,18,32,.55);
  }
  #taskModal.tm-overlay.is-open{ display:flex; }

  /* Panel */
  #taskModal .tm-panel{
    width: min(1100px, 96vw);
    max-height: 92vh;               /* overall modal height */
    display:flex;
    flex-direction:column;
    border-radius:22px;
    overflow:hidden;
    background:#fff;
    box-shadow:var(--shadow);
    border:1px solid rgba(255,255,255,.25);
  }

  /* Header */
  #taskModal .tm-header{
    flex:0 0 auto;
    padding:14px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    background:linear-gradient(135deg, rgba(116,178,212,.92), rgba(192,216,234,.95));
    border-bottom:1px solid rgba(0,0,0,.08);
  }
  #taskModal .tm-title h3{
    margin:0;
    font-size:16px;
    font-weight:950;
    color:var(--txt);
    letter-spacing:.2px;
  }
  #taskModal .tm-title p{
    margin:2px 0 0 0;
    font-size:12.5px;
    font-weight:800;
    color:rgba(15,23,42,.72);
  }
  #taskModal .tm-close{
    width:40px; height:40px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,.10);
    background:rgba(255,255,255,.68);
    cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    font-weight:900;
    line-height:1;
  }
  #taskModal .tm-close:hover{ background:rgba(255,255,255,.88); }

  /* Body (scroll area) */
  #taskModal .tm-body{
    flex:1 1 auto;
    overflow:auto;                   /* scroll inside */
    -webkit-overflow-scrolling:touch;/* iOS smooth */
    padding:14px 16px;
    background:
      radial-gradient(1200px 520px at 10% 0%, rgba(116,178,212,.18), transparent 60%),
      radial-gradient(900px 480px at 90% 10%, rgba(147,194,28,.14), transparent 58%),
      linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.92));
  }

  /* Footer (sticky by layout) */
  #taskModal .tm-footer{
    flex:0 0 auto;
    padding:12px 16px;
    border-top:1px solid rgba(0,0,0,.08);
    background:rgba(255,255,255,.92);
    display:flex;
    justify-content:flex-end;
    gap:10px;
  }

  /* Grid */
  #taskModal .tm-grid{
    display:grid;
    grid-template-columns: 1.35fr .65fr;
    gap:14px;
  }
  @media (max-width: 900px){
    #taskModal .tm-grid{ grid-template-columns: 1fr; }
  }

  #taskModal .tm-card{
    background:rgba(255,255,255,.92);
    border:1px solid var(--bd2);
    border-radius:var(--r);
    box-shadow:var(--shadow2);
    padding:14px;
  }

  /* Rows/cols */
  #taskModal .tm-row{ display:grid; grid-template-columns: repeat(12, 1fr); gap:12px; }
  #taskModal .tm-col-12{ grid-column:span 12; }
  #taskModal .tm-col-8{ grid-column:span 8; }
  #taskModal .tm-col-4{ grid-column:span 4; }
  @media (max-width: 900px){
    #taskModal .tm-col-8, #taskModal .tm-col-4{ grid-column:span 12; }
  }

  /* Labels & Inputs */
  #taskModal .tm-label{
    display:block;
    margin:0 0 7px 0;
    font-size:12.5px;
    font-weight:950;
    color:var(--txt);
  }
  #taskModal .tm-input,
  #taskModal .tm-textarea,
  #taskModal .tm-select{
    width:100%;
    border:1px solid var(--bd);
    border-radius:14px;
    padding:10px 12px;
    background:#fff;
    color:var(--txt);
    outline:none;
  }
  #taskModal .tm-textarea{ min-height: 90px; resize: vertical; }
  #taskModal .tm-input:focus,
  #taskModal .tm-textarea:focus,
  #taskModal .tm-select:focus{
    border-color: rgba(116,178,212,.65);
    box-shadow: 0 0 0 4px rgba(116,178,212,.18);
  }

  /* Color + switch */
  #taskModal .tm-colorbar{
    display:flex; align-items:center; justify-content:flex-end; gap:10px;
  }
  #taskModal .tm-colorbtn{
    width:46px; height:42px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,.10);
    background:rgba(255,255,255,.88);
    cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    position:relative;
  }
  #taskModal .tm-swatch{
    width:18px; height:18px;
    border-radius:6px;
    background:var(--swatch, var(--grn));
    border:1px solid rgba(0,0,0,.12);
  }
  #taskModal .tm-menu{
    position:absolute;
    top:calc(100% + 10px);
    right:0;
    width:220px;
    background:#fff;
    border:1px solid rgba(0,0,0,.12);
    border-radius:16px;
    box-shadow:0 18px 42px rgba(0,0,0,.18);
    padding:8px;
    display:none;
    z-index:50;
  }
  #taskModal .tm-colorbtn.is-open .tm-menu{ display:block; }
  #taskModal .tm-menu-item{
    display:flex; align-items:center; gap:10px;
    padding:10px 10px;
    border-radius:12px;
    cursor:pointer;
    font-weight:900;
    color:var(--txt);
  }
  #taskModal .tm-menu-item:hover{ background: rgba(116,178,212,.12); }
  #taskModal .tm-dot{
    width:14px; height:14px;
    border-radius:6px;
    background:var(--c);
    border:1px solid rgba(0,0,0,.16);
  }

  #taskModal .tm-switch{
    display:flex; align-items:center; gap:10px;
    padding:10px 12px;
    border:1px solid rgba(0,0,0,.10);
    border-radius:14px;
    background:rgba(255,255,255,.86);
    user-select:none;
  }
  #taskModal .tm-switch span{
    font-size:12.5px;
    font-weight:950;
    color:var(--txt);
  }
  #taskModal .tm-switch input{ display:none; }
  #taskModal .tm-toggle{
    width:46px; height:26px;
    border-radius:999px;
    background:rgba(15,23,42,.18);
    position:relative;
    cursor:pointer;
    flex-shrink:0;
  }
  #taskModal .tm-toggle:after{
    content:"";
    position:absolute;
    top:3px; left:3px;
    width:20px; height:20px;
    border-radius:999px;
    background:#fff;
    box-shadow:0 8px 16px rgba(0,0,0,.16);
    transition: transform .15s ease;
  }
  #taskModal .tm-switch input:checked + .tm-toggle{
    background: rgba(147,194,28,.75);
  }
  #taskModal .tm-switch input:checked + .tm-toggle:after{
    transform: translateX(20px);
  }

  /* Steps section (no bootstrap collapse) */
  #taskModal .tm-section{ margin-top:14px; }
  #taskModal .tm-section-header{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:12px 12px;
    border-radius:16px;
    border:1px solid rgba(0,0,0,.10);
    background: linear-gradient(135deg, rgba(116,178,212,.12), rgba(192,216,234,.22));
    cursor:pointer;
  }
  #taskModal .tm-section-header strong{
    display:flex; align-items:center; gap:10px;
    font-weight:950;
    color:var(--txt);
    font-size:13px;
  }
  #taskModal .tm-chevron{
    width:34px; height:34px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,.10);
    background: rgba(255,255,255,.78);
    display:flex; align-items:center; justify-content:center;
    transition: transform .15s ease;
  }
  #taskModal .tm-section.is-open .tm-chevron{ transform: rotate(180deg); }
  #taskModal .tm-section-body{
    display:none;
    margin-top:10px;
    border:1px solid rgba(0,0,0,.10);
    border-radius:16px;
    background: rgba(255,255,255,.92);
    padding:12px;
  }
  #taskModal .tm-section.is-open .tm-section-body{ display:block; }

  /* steps table (responsive horizontal scroll) */
  #taskModal .tm-table-wrap{
    width:100%;
    overflow:auto;
    -webkit-overflow-scrolling:touch;
    border-radius:14px;
  }
  #taskModal .tm-table{
    width:100%;
    min-width: 860px; /* makes it scrollable on small screens */
    border-collapse: separate;
    border-spacing:0;
    border:1px solid rgba(0,0,0,.10);
    border-radius:14px;
    overflow:hidden;
    background:#fff;
  }
  #taskModal .tm-table thead th{
    padding:10px;
    background: rgba(192,216,234,.35);
    border-bottom:1px solid rgba(0,0,0,.10);
    text-align:left;
    font-size:12px;
    font-weight:950;
    color:var(--txt);
    vertical-align: middle;
  }
  #taskModal .tm-table td{
    padding:10px;
    border-bottom:1px solid rgba(0,0,0,.08);
    vertical-align: top;
  }
  #taskModal .tm-table tbody tr:last-child td{ border-bottom:0; }

  /* Buttons */
  #taskModal .tm-btn{
    border:0;
    border-radius:14px;
    padding:10px 14px;
    font-weight:950;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:10px;
  }
  #taskModal .tm-btn--ghost{
    background: rgba(15,23,42,.06);
    color: var(--txt);
    border: 1px solid rgba(15,23,42,.10);
  }
  #taskModal .tm-btn--primary{
    color:#fff;
    background: linear-gradient(135deg, var(--pri), #5aa4ca);
    box-shadow: 0 14px 26px rgba(116,178,212,.28);
  }
  #taskModal .tm-btn--danger{
    color:#fff;
    background: linear-gradient(135deg, #ef4444, #f97316);
    box-shadow: 0 14px 26px rgba(239,68,68,.22);
  }

  #taskModal .tm-iconbtn{
    width:38px; height:38px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,.10);
    background: rgba(116,178,212,.10);
    cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center;
  }

  /* ✅ IMPORTANT FIX: make the form a flex column so footer stays visible */
#taskModal .tm-panel{
  display: flex;
  flex-direction: column;
  max-height: 92vh;
}

#taskModal form#task_form{
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  min-height: 0;            /* critical for overflow to work in all browsers */
}

/* ✅ only body scrolls */
#taskModal .tm-body{
  flex: 1 1 auto;
  min-height: 0;            /* critical */
  overflow: auto;
  -webkit-overflow-scrolling: touch;
}

/* ✅ footer always visible */
#taskModal .tm-footer{
  flex: 0 0 auto;
}

/* Optional: if you ever change to position:sticky later */
#taskModal .tm-body{
  padding-bottom: 12px;
}

</style>

<div class="tm-overlay" id="taskModal" aria-hidden="true" role="dialog" aria-labelledby="taskModalLabel">
  <div class="tm-panel" role="document">
    <div class="tm-header">
      <div class="tm-title">
        <h3 id="taskModalLabel">Aufgaben erstellen</h3>
        <p>Bitte füllen Sie die wichtigsten Daten aus und speichern Sie anschließend.</p>
      </div>

      <button type="button" class="tm-close close_task_window" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    </div>

    <form id="task_form">
      @csrf

      {{-- hidden context --}}
      <input type="hidden" name="customer_id" id="select_customer_id">
      <input type="hidden" name="alternative_id" id="select_alternative_id">
      <input type="hidden" name="product_id" id="select_product_id">
      <input type="hidden" name="is_customer" value="1">

      <div class="tm-body">
        <div class="tm-grid">
          {{-- Left: main --}}
          <div class="tm-card">
            <div class="tm-row">
              <div class="tm-col-8">
                <label class="tm-label" for="task_title">Aufgabentitel</label>
                <input type="text" id="task_title" class="tm-input" name="task_title" placeholder="z.B. Vor-Ort Termin vorbereiten">
              </div>

              <div class="tm-col-4">
                <label class="tm-label">Farbe & Sichtbarkeit</label>

                <div class="tm-colorbar">
                  <input type="hidden" name="color" id="color" value="#93c21c">

                  <div class="tm-colorbtn" id="tmColorBtn" style="--swatch:#93c21c;">
                    <span class="tm-swatch" id="tmSwatch"></span>

                    <div class="tm-menu" id="tmColorMenu">
                      @php
                        $colors = [
                          ['#93c21c','Grün'],
                          ['#74b2d4','Blau'],
                          ['#ef4444','Rot'],
                          ['#f59e0b','Gelb'],
                          ['#8b5cf6','Lila'],
                          ['#111827','Schwarz'],
                        ];
                      @endphp

                      @foreach($colors as [$hex,$label])
                        <div class="tm-menu-item tm-menu-item" data-color="{{ $hex }}">
                          <span class="tm-dot" style="--c:{{ $hex }}"></span>
                          <span>{{ $label }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>

                  <label class="tm-switch" title="Öffentlich / Privat">
                    <span>Öffentlich</span>
                    <input type="checkbox" id="customSwitch10" name="public" checked>
                    <span class="tm-toggle" aria-hidden="true"></span>
                  </label>
                </div>
              </div>

              <div class="tm-col-12">
                <label class="tm-label" for="description">Beschreibung</label>
                <textarea name="description" id="description" class="tm-textarea" placeholder="Kurze Notizen / Kontext…"></textarea>
              </div>
            </div>
          </div>

          {{-- Right: timing & people --}}
          <div class="tm-card">
            <div class="tm-row">
              <div class="tm-col-12">
                <label class="tm-label" for="due_date">Fälligkeitsdatum</label>
                <input type="date" id="due_date" class="tm-input" name="due_date">

                <input type="hidden" name="same_id" value="same">
                <input type="hidden" id="start_date" name="start_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
              </div>

              <div class="tm-col-12">
                <label class="tm-label" for="due_time">Fälligkeitsuhrzeit</label>
                <input type="time" id="due_time" class="tm-input" name="due_time">
              </div>

              <div class="tm-col-12">
                <label class="tm-label" for="total_day">Gesamt Tage</label>
                <input type="number" id="total_day" class="tm-input" name="total_day" placeholder="z.B. 2">
              </div>

              <div class="tm-col-12">
                <label class="tm-label" for="total_time">Gesamtstunden</label>
                <input type="number" id="total_time" class="tm-input" name="total_time" placeholder="z.B. 6">
              </div>

              <div class="tm-col-12" id="task_employee_section">
                <label class="tm-label" for="employee">Zugewiesen an</label>
                <select name="employee[]" id="employee" class="tm-select" multiple>
                  @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}">
                      {{ $emp->name }} {{ $emp->lastname }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="tm-col-12" id="task_controller_section">
                <label class="tm-label" for="controller">Kontroller</label>
                <select name="controller[]" id="controller" class="tm-select" multiple>
                  @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}">
                      {{ $emp->name }} {{ $emp->lastname }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        {{-- Steps --}}
        <div class="tm-section" id="tmSteps">
          <div class="tm-section-header" data-toggle="tm-collapse">
            <strong><i class="feather icon-list"></i> Aufgabenschritte</strong>
            <div class="tm-chevron"><i class="feather icon-chevron-down"></i></div>
          </div>

          <div class="tm-section-body">
            <div class="tm-table-wrap">
              <table class="tm-table" id="key_task">
                <thead>
                  <tr>
                    <th style="width:44px;">#</th>
                    <th>Aufgabenschritte</th>
                    <th style="width:140px;">
                      Dauer<br>
                      <span style="font-size:11.5px;font-weight:900;color:rgba(15,23,42,.68);">
                        <code id="key_total_time">23 Stunden</code>
                      </span>
                    </th>
                    <th style="width:260px;">Zugewiesen</th>
                    <th>Beschreibung</th>
                    <th style="width:120px;">Aktion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>1</strong></td>
                    <td><input type="text" name="key[0][task]" class="tm-input" placeholder="Schritt…"></td>
                    <td><input type="number" name="key[0][duration]" class="tm-input" placeholder="Std"></td>
                    <td>
                      <select name="key[0][employee_id][]" class="tm-select" multiple>
                        @foreach ($employees as $employee)
                          <option value="{{ $employee->id }}" data-image="{{ asset('images/employee/'.$employee->image) }}">
                            {{ $employee->name }} {{ $employee->lastname }}
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td><textarea name="key[0][key_description]" class="tm-textarea" style="min-height:44px" placeholder="Beschreibung…"></textarea></td>
                    <td style="white-space:nowrap;">
                      <button type="button" class="tm-iconbtn add-task-steps" title="Hinzufügen"><i class="fa fa-plus"></i></button>
                      <button type="button" class="tm-iconbtn remove-task-steps" title="Entfernen"><i class="fa fa-minus"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div style="height:14px;"></div>
      </div>

      <div class="tm-footer">
        <button type="button" class="tm-btn tm-btn--ghost close_task_window">
          <i class="feather icon-x"></i> abbrechen
        </button>
        <button type="button" class="tm-btn tm-btn--primary save-task-close">
          <i class="feather icon-save"></i> speichern
        </button>
      </div>
    </form>
  </div>
</div>
