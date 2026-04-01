<div class="ap-report-wrapper">

    @php
        // Group reports by appointment_id (null group is allowed)
        $groupedReports = $reports->groupBy('appointment_id');
    @endphp

    @forelse($groupedReports as $appointmentId => $appointmentReports)
        @php
            /** @var \App\Models\MainAppointment|null $appointment */
            $appointment = optional($appointmentReports->first())->appointment;
        @endphp

        <div class="ap-appointment-group mb-2"
             data-appointment-id="{{ $appointmentId }}"
             data-customer-id="{{ $appointment->customer_id ?? $customerId }}"
             data-alternative-id="{{ $alternativeId }}"
             data-product-id="{{ $productId }}">

            {{-- APPOINTMENT CARD (HEADER) -------------------------------- --}}
            <div class="ap-appointment-header">
                <div class="ap-appointment-main">
                    <div class="ap-appointment-title">
                        <i class="feather icon-calendar mr-50"></i>
                        {{ $appointment->name ?? 'Termin ohne Titel' }}
                    </div>

                    <div class="ap-appointment-sub">
                        @if($appointment)
                            {{ optional($appointment->start_date)->format('d.m.Y') }}

                            @if($appointment->start_time)
                                · {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }}
                                @if($appointment->end_time)
                                    – {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}
                                @endif
                            @endif

                            @if($appointment->appointment_type)
                                · <span class="ap-appointment-type">
                                    {{ $appointment->appointment_type }}
                                  </span>
                            @endif
                        @else
                            Kein Termin verknüpft
                        @endif
                    </div>

                    @if($appointment && $appointment->employees?->count())
                        <div class="ap-appointment-employees">
                            @foreach($appointment->employees as $employee)
                                <span class="ap-appointment-employee">
                                    <img src="{{ $employee->image
                                                ? asset('images/employee/'.$employee->image)
                                                : asset('images/employee/noimage.png') }}"
                                         alt=""
                                         class="ap-appointment-employee-avatar">
                                    {{ $employee->lastname }} {{ $employee->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="ap-appointment-actions">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm ap-open-report-form">
                        <i class="feather icon-file-text"></i> Report
                    </button>
                </div>
            </div>

            {{-- INLINE CREATE FORM FOR THIS APPOINTMENT ------------------ --}}
            <div class="ap-report-create-wrapper" style="display:none;">
                <form class="ap-report-create-form"
                      data-appointment-id="{{ $appointmentId }}"
                      data-customer-id="{{ $appointment->customer_id ?? $customerId }}"
                      data-alternative-id="{{ $alternativeId }}"
                      data-product-id="{{ $productId }}">

                    <div class="ap-report-card mb-1">
                        <div class="ap-report-top">
                            <div>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       name="title"
                                       placeholder="Titel des Reports*">
                            </div>
                            <div class="ap-report-stage">
                                <select name="stage" class="form-control form-control-sm">
                                    <option value="">Phase wählen…</option>
                                    <option value="lead">Lead</option>
                                    <option value="offer">Verkauf</option>
                                    <option value="deal">Auftrag</option>
                                    <option value="project">Montage</option>
                                    <option value="completed">Abschluss</option>
                                </select>
                            </div>
                        </div>

                        <div class="ap-report-body">
                            <textarea name="content"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Report-Text*"></textarea>
                        </div>

                        <div class="ap-report-footer">
                            <div class="ap-report-author">
                                <span>Neuen Report zu diesem Termin erstellen</span>
                            </div>
                            <div>
                                <button type="submit"
                                        class="btn btn-primary btn-sm">
                                    <i class="feather icon-save"></i> Speichern
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- REPORT-LIST UNTER DIESEM TERMIN -------------------------- --}}
            <div class="ap-report-list mt-1">
                @foreach($appointmentReports as $report)
                    @include('admin.kanban.partials._report_card', ['report' => $report])
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-muted small p-2">
            Noch keine Reports vorhanden.
        </div>
    @endforelse
</div>
