<div class="gt-modal-backdrop" id="gtTaskModal">
    <div class="gt-modal gt-task-create-modal">
        <div class="gt-modal-h">
            <div>
                <h3 class="gt-modal-ttl" id="gtTaskModalTitle">Neue Aufgabe</h3>
                <div class="gt-help">Single oder Bulk/Schritte erstellen. Zusatzoptionen sind rechts im Einstellungsbereich.</div>
            </div>
            <button class="gt-btn-ic" type="button" onclick="gtCloseModal('gtTaskModal')">×</button>
        </div>

        <form id="gtTaskForm" method="POST" action="{{ route('general-tasks.store') }}">
            @csrf
            <input type="hidden" name="_method" id="gtTaskMethod" value="POST">
            <input type="hidden" name="id" id="gtTaskId">

            <div class="gt-modal-b gt-task-modal-layout">
                <main class="gt-task-modal-main">
                    <div class="gt-form-group">
                        <label class="gt-label">Titel *</label>
                        <input class="gt-input gt-input-lg" name="title" id="gtTitle" required placeholder="Aufgabentitel eingeben">
                    </div>

                    <div class="gt-form-group">
                        <label class="gt-label">Beschreibung</label>
                        <textarea class="gt-textarea gt-textarea-lg" name="description" id="gtDescription" placeholder="Was muss erledigt werden?"></textarea>
                    </div>

                    <section class="gt-section-box gt-mode-section">
                        <div class="gt-section-head">
                            <div>
                                <div class="gt-section-title">Aufgabenart</div>
                                <div class="gt-help">Standard ist Single. Bulk nutzt mehrere Schritte mit eigenen verantwortlichen Mitarbeitern.</div>
                            </div>
                        </div>

                        <div class="gt-mode-grid gt-task-mode-hero">
                            <label class="gt-mode-card">
                                <input type="radio" name="task_mode" value="single" id="gtTaskModeSingle" checked data-task-mode-radio>
                                <span>
                                    <strong>Single Aufgabe</strong>
                                    <small>Eine Aufgabe mit gemeinsamer Mitarbeiterliste.</small>
                                </span>
                            </label>

                            <label class="gt-mode-card">
                                <input type="radio" name="task_mode" value="bulk" id="gtTaskModeBulk" data-task-mode-radio>
                                <span>
                                    <strong>Bulk / Schritte</strong>
                                    <small>Mehrere Schritte. Mitarbeiter werden pro Schritt gewählt.</small>
                                </span>
                            </label>
                        </div>
                    </section>

                    <section class="gt-section-box gt-single-only" id="gtSingleAssigneeSection">
                        <div class="gt-section-head">
                            <div>
                                <div class="gt-section-title">Mitarbeiter</div>
                                <div class="gt-help">Nur bei Single Aufgabe sichtbar. Bei Bulk wählst du Mitarbeiter in jedem Schritt.</div>
                            </div>
                        </div>

                        <div class="gt-assignee-grid">
                            @foreach($employeesCollection ?? [] as $employee)
                                @php
                                    $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
                                    $initials = mb_strtoupper(mb_substr($employee->name ?? 'M', 0, 1) . mb_substr($employee->lastname ?? '', 0, 1));
                                @endphp
                                <label class="gt-assignee-card" title="{{ $fullName ?: ('Mitarbeiter #' . $employee->id) }}">
                                    <input type="checkbox" name="assignee_ids[]" value="{{ $employee->id }}" data-assignee-checkbox>
                                    @if(!empty($employee->image))
                                        <img class="gt-assignee-avatar" src="{{ asset('images/employee/' . $employee->image) }}" alt="{{ $fullName }}">
                                    @else
                                        <span class="gt-assignee-avatar">{{ $initials }}</span>
                                    @endif
                                    <span class="gt-assignee-name">{{ $fullName ?: ('#' . $employee->id) }}</span>
                                    <span class="gt-assignee-check"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    @include('admin.general_tasks.partials.task-steps-form')
                </main>

                <aside class="gt-task-modal-sidebar">
                    <div class="gt-sidebar-card">
                        <div class="gt-sidebar-card-title">Einstellungen</div>

                        <div class="gt-form-group">
                            <label class="gt-label">Priorität</label>
                            <select class="gt-select" name="priority" id="gtPriority">
                                @foreach($priorityLabels ?? [] as $key => $label)
                                    <option value="{{ $key }}" @selected($key === 'normal')>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gt-form-group">
                            <label class="gt-label">Sichtbarkeit</label>
                            <select class="gt-select" name="visibility" id="gtVisibility">
                                <option value="all">Alle Mitarbeiter</option>
                                <option value="department">Bestimmte Abteilung</option>
                                <option value="specific">Bestimmte Personen</option>
                            </select>
                        </div>

                        <div class="gt-form-group">
                            <label class="gt-label">Abteilung</label>
                            <select class="gt-select" name="department_id" id="gtDepartment">
                                <option value="">Keine / Alle</option>
                                @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="gt-sidebar-card">
                        <button class="gt-sidebar-toggle" type="button" data-settings-toggle="time">
                            <span><i data-lucide="calendar-clock" style="width:15px;height:15px"></i> Zeit & Anzeige</span>
                            <i data-lucide="chevron-down" style="width:15px;height:15px"></i>
                        </button>
                        <div class="gt-sidebar-toggle-body" data-settings-body="time">
                            <div class="gt-form-group">
                                <label class="gt-label">Fällig am</label>
                                <input class="gt-input" type="datetime-local" name="due_at" id="gtDueAt">
                            </div>
                            <label class="gt-switch-line">
                                <input type="hidden" name="show_due_datetime" value="0">
                                <input type="checkbox" name="show_due_datetime" value="1" id="gtShowDueDateTime" checked>
                                <span>Datum und Uhrzeit auf Karte anzeigen</span>
                            </label>
                        </div>
                    </div>

                    <div class="gt-sidebar-card">
                        <button class="gt-sidebar-toggle" type="button" data-settings-toggle="recurrence">
                            <span><i data-lucide="repeat-2" style="width:15px;height:15px"></i> Wiederholung</span>
                            <i data-lucide="chevron-down" style="width:15px;height:15px"></i>
                        </button>
                        <div class="gt-sidebar-toggle-body" data-settings-body="recurrence" hidden>
                            <label class="gt-switch-line">
                                <input type="checkbox" name="is_recurring" value="1" id="gtIsRecurring">
                                <span>Diese Aufgabe wiederholen</span>
                            </label>
                            <div id="gtRecurrenceSettings" hidden>
                                <div class="gt-form-group">
                                    <label class="gt-label">Rhythmus</label>
                                    <select class="gt-select" name="recurrence_frequency" id="gtRecurrenceFrequency">
                                        @foreach($recurrenceLabels ?? [] as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="gt-form-group" id="gtRecurrenceWeekdayWrap">
                                    <label class="gt-label">Wochentag</label>
                                    <select class="gt-select" name="recurrence_weekday" id="gtRecurrenceWeekday">
                                        <option value="">Automatisch</option>
                                        @foreach($weekdayLabels ?? [] as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="gt-form-group">
                                    <label class="gt-label">Wiederholen bis</label>
                                    <input class="gt-input" type="datetime-local" name="recurrence_ends_at" id="gtRecurrenceEndsAt">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gt-sidebar-card">
                        <button class="gt-sidebar-toggle" type="button" data-settings-toggle="dependency">
                            <span><i data-lucide="git-branch" style="width:15px;height:15px"></i> Abhängigkeit</span>
                            <i data-lucide="chevron-down" style="width:15px;height:15px"></i>
                        </button>
                        <div class="gt-sidebar-toggle-body" data-settings-body="dependency" hidden>
                            <div class="gt-form-group">
                                <label class="gt-label">Vorgänger / Parent</label>
                                <select class="gt-select gt-select2" name="dependency_parent_ids[]" id="gtDependencyParentIds" multiple data-placeholder="Aufgabe suchen, die zuerst erledigt werden muss">
                                    @foreach($taskCollection ?? [] as $dependencyTask)
                                        <option value="{{ $dependencyTask->id }}">#{{ $dependencyTask->id }} — {{ $dependencyTask->title }}</option>
                                    @endforeach
                                </select>
                                <div class="gt-help">Richtung: Parent / Vorgänger → Child / diese Aufgabe.</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="gt-modal-f">
                <button type="button" class="gt-btn-soft" onclick="gtCloseModal('gtTaskModal')">Abbrechen</button>
                <button type="submit" class="gt-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>
