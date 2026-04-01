<div id="tm-root"
     data-employee-id="{{ $employee->id }}"
     data-hourly-rate="{{ $employee->salary_per_hour ?? 0 }}"
     data-default-monthly-hours="{{ $employee->working_hour ?? 0 }}"
     data-working-type="{{ $employee->working_type ?? '' }}">

    <div class="row">
        {{-- LEFT: calendar + controls --}}
        <div class="col-xl-8 col-lg-7 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h4 class="card-title mb-0">Monat auswählen</h4>
                        <small class="text-muted">
                            Wähle den Monat und trage deine täglichen Arbeitszeiten ein.
                        </small>
                    </div>
                    <div class="d-flex align-items-center mt-1 mt-md-0">
                        <input type="month" id="tmMonth" class="form-control mr-1"
                               value="{{ now()->format('Y-m') }}">
                        <button id="btnReloadMonth" class="btn btn-outline-secondary btn-sm ml-1">
                            Neu laden
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Employee contract info chips --}}
                    <div class="tm-chips mb-2">
                        <span class="badge badge-light-primary mr-50">
                            Arbeitsmodell: {{ $employee->working_type ?? '–' }}
                        </span>
                        <span class="badge badge-light-info mr-50">
                            Vertragliche Std./Monat: {{ $employee->working_hour ?? '–' }}
                        </span>
                        <span class="badge badge-light-success">
                            Lohn pro Stunde: {{ number_format($employee->salary_per_hour ?? 0, 2, ',', '.') }} €
                        </span>
                    </div>

                    {{-- Target hours input --}}
                    <div class="form-group">
                        <label for="tmTargetHours">Zielstunden für diesen Monat</label>
                        <div class="input-group">
                            <input type="number" step="0.25" min="0"
                                   id="tmTargetHours"
                                   class="form-control"
                                   value="{{ $employee->working_hour ?? 0 }}">
                            <div class="input-group-append">
                                <span class="input-group-text">Stunden</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Beispiel: 100 Stunden für einen Vertrag mit 100h im Monat.
                        </small>
                    </div>

                    {{-- Calendar grid --}}
                    <div id="tmCalendarWrapper">
                        <div class="tm-calendar-header">
                            <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div>
                            <div>Fr</div><div>Sa</div><div>So</div>
                        </div>
                        <div id="tmCalendarDays" class="tm-calendar-days">
                            {{-- filled by JS --}}
                        </div>
                    </div>

                    <small class="text-muted d-block mt-1">
                        Klicke auf einen Tag, um Start-, Endzeit und Pause zu setzen.
                    </small>
                </div>

                <div class="card-footer d-flex justify-content-between flex-wrap">
                    <div class="mb-1 mb-md-0">
                        <span class="badge badge-pill badge-light-secondary mr-50" id="tmPlanStatusBadge">
                            Status: <span id="tmPlanStatusText">Entwurf</span>
                        </span>
                    </div>
                    <div>
                        <button id="btnSaveDraft" class="btn btn-outline-primary mr-50">
                            Entwurf speichern
                        </button>
                        <button id="btnSubmitPlan" class="btn btn-primary">
                            Zur Genehmigung senden
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: summary + approval --}}
        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Übersicht & Prognose</h4>
                </div>
                <div class="card-body">
                    <div class="tm-summary-grid mb-2">
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Geplante Stunden</div>
                            <div class="tm-summary-value" id="tmTotalHours">0.00 h</div>
                        </div>
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Zielstunden</div>
                            <div class="tm-summary-value" id="tmTargetHoursText">0.00 h</div>
                        </div>
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Differenz</div>
                            <div class="tm-summary-value" id="tmDiffHours">0.00 h</div>
                        </div>
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Ø notwendige Std / Resttag</div>
                            <div class="tm-summary-value" id="tmAvgRemaining">0.00 h</div>
                        </div>
                    </div>

                    <div class="tm-progress-wrapper mb-2">
                        <label class="tm-progress-label">
                            Fortschritt zum Ziel
                            <span id="tmProgressPercentText" class="float-right">0 %</span>
                        </label>
                        <div class="tm-progress-bar">
                            <div id="tmProgressFill"></div>
                        </div>
                    </div>

                    <div class="tm-summary-grid mb-2">
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Stundenlohn</div>
                            <div class="tm-summary-value" id="tmHourlyRate">
                                {{ number_format($employee->salary_per_hour ?? 0, 2, ',', '.') }} €
                            </div>
                        </div>
                        <div class="tm-summary-item">
                            <div class="tm-summary-label">Vorauss. Monatslohn</div>
                            <div class="tm-summary-value" id="tmEstimatedPay">0.00 €</div>
                        </div>
                    </div>

                    {{-- Supervisor / Admin approval section --}} 
                        <hr>
                        <h6 class="mb-1">Genehmigung</h6>
                        <div class="form-group">
                            <label for="tmApproveStatus">Status ändern</label>
                            <select id="tmApproveStatus" class="form-control form-control-sm">
                                <option value="">– auswählen –</option>
                                <option value="approved">Genehmigen</option>
                                <option value="rejected">Ablehnen</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tmApproveComment">Kommentar</label>
                            <textarea id="tmApproveComment" rows="2"
                                      class="form-control form-control-sm"></textarea>
                        </div>
                        <button id="btnApprovePlan" class="btn btn-sm btn-success">
                            Status speichern
                        </button> 
                </div>
            </div>
        </div>
    </div>
</div>
