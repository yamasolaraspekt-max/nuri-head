@php
    $steps = collect($task->steps ?? []);
@endphp

@if($steps->count())
    <div class="gt-step-preview">
        @foreach($steps->take(4) as $step)
            @php
                $stepAssignees = collect($step->assignees ?? []);
                $checkedBy = $step->checkedBy ?? null;
                $checkedName = $checkedBy ? trim(($checkedBy->name ?? '') . ' ' . ($checkedBy->lastname ?? '')) : '';
                $plannedMinutes = (int) ($step->planned_minutes ?? $step->soll_minutes ?? 0);
                $actualMinutes = (int) ($step->actual_minutes ?? $step->ist_minutes ?? 0);
            @endphp
            <div class="gt-step-mini {{ !empty($step->is_done) ? 'done' : '' }}" data-step-card-id="{{ $step->id }}">
                <button
                    type="button"
                    class="gt-step-mini-check-btn {{ !empty($step->is_done) ? 'done' : '' }}"
                    data-step-toggle-modal
                    data-task-id="{{ $task->id }}"
                    data-step-id="{{ $step->id }}"
                    data-step-title="{{ e($step->title) }}"
                    data-next-done="{{ !empty($step->is_done) ? 0 : 1 }}"
                    title="{{ !empty($step->is_done) ? 'Als offen markieren' : 'Als erledigt markieren' }}"
                >
                    <i data-lucide="{{ !empty($step->is_done) ? 'check' : 'circle' }}" style="width:15px;height:15px"></i>
                </button>

                <div class="gt-step-mini-main">
                    <div class="gt-step-mini-title">{{ $step->title }}</div>
                    <div class="gt-step-mini-meta">
                        @if($plannedMinutes > 0)
                            Geplante Zeit {{ number_format($plannedMinutes / 60, 2, ',', '.') }} h
                        @endif
                        @if($plannedMinutes > 0 && $actualMinutes > 0) · @endif
                        @if($actualMinutes > 0)
                            Tatsächliche Zeit {{ number_format($actualMinutes / 60, 2, ',', '.') }} h
                        @endif
                        @if(!empty($step->is_done))
                            · erledigt
                            @if($checkedName !== '') von {{ $checkedName }} @endif
                            @if($step->checked_at) am {{ \Carbon\Carbon::parse($step->checked_at)->format('d.m.Y H:i') }} @endif
                        @endif
                    </div>

                    <div class="gt-step-mini-users">
                        @foreach($stepAssignees->take(3) as $employee)
                            @include('admin.general_tasks.partials.employee-avatar')
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        @if($steps->count() > 4)
            <div class="gt-person-meta">+ {{ $steps->count() - 4 }} weitere Schritte</div>
        @endif
    </div>
@endif
