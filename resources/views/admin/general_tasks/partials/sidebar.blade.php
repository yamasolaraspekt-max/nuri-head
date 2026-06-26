<div class="gt-sidebar-overlay" id="gtSidebarOverlay" data-gt-sidebar-close></div>

<aside class="gt-team-drawer" id="gtTeamDrawer" aria-hidden="true">
    <div class="gt-drawer-head">
        <div>
            <div class="gt-drawer-title">Team & Meine Aufgaben</div>
            <div class="gt-drawer-sub">Teamstatus und deine aktiven Aufgaben.</div>
        </div>
        <button class="gt-btn-ic" type="button" data-gt-sidebar-close>
            <i data-lucide="x"></i>
        </button>
    </div>

    <div class="gt-drawer-tabs">
        <button type="button" class="gt-drawer-tab active" data-gt-drawer-tab="team">Aktives Team</button>
        <button type="button" class="gt-drawer-tab" data-gt-drawer-tab="mine">Meine Aufgaben</button>
    </div>

    <div class="gt-drawer-body">
        <div class="gt-drawer-panel active" data-gt-drawer-panel="team">
            <div class="gt-panel">
                <div class="gt-panel-h">
                    <div class="gt-panel-title">Aktives Team</div>
                    <span class="gt-count">{{ $activeEmployeesCollection->count() }}</span>
                </div>

                <div class="gt-panel-b">
                    <div class="gt-team-list">
                        @forelse($activeEmployeesCollection as $employee)
                            @php
                                $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
                                $initials = mb_strtoupper(mb_substr($employee->name ?? 'M', 0, 1) . mb_substr($employee->lastname ?? '', 0, 1));
                            @endphp

                            <div class="gt-person">
                                @if(!empty($employee->image))
                                    <img class="gt-avatar" src="{{ asset('images/employee/' . $employee->image) }}" alt="{{ $fullName }}">
                                @else
                                    <div class="gt-avatar">{{ $initials }}</div>
                                @endif

                                <div>
                                    <div class="gt-person-name">{{ $fullName ?: ('Mitarbeiter #' . $employee->id) }}</div>
                                    <div class="gt-person-meta">{{ optional($employee->mainDepartmentPosition?->department)->department_name ?? 'Teammitglied' }}</div>
                                </div>

                                <span class="{{ ($employee->status_msg ?? '') === 'online' ? 'gt-online' : 'gt-offline' }}"></span>
                            </div>
                        @empty
                            <div class="gt-empty">Keine aktiven Mitarbeiter gefunden.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="gt-drawer-panel" data-gt-drawer-panel="mine">
            <div class="gt-panel">
                <div class="gt-panel-h">
                    <div class="gt-panel-title">Meine aktiven Aufgaben</div>
                    <span class="gt-count">{{ $myTasks->count() }}</span>
                </div>

                <div class="gt-panel-b">
                    <div class="gt-team-list">
                        @forelse($myTasks->take(20) as $task)
                            <div class="gt-person" style="align-items:flex-start">
                                <div class="gt-avatar">{{ Str::upper(Str::substr($task->title, 0, 1)) }}</div>
                                <div>
                                    <div class="gt-person-name">{{ $task->title }}</div>
                                    <div class="gt-person-meta">
                                        {{ $statusLabels[$task->status] ?? $task->status }}
                                        @if($task->due_at)
                                            · {{ \Carbon\Carbon::parse($task->due_at)->format('d.m.Y H:i') }}
                                        @endif
                                    </div>
                                    @if(!empty($task->description))
                                        <div class="gt-person-meta">{{ Str::limit($task->description, 86) }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="gt-empty">Du hast aktuell keine aktive Aufgabe.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
