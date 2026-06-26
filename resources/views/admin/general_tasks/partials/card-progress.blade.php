@php
    $steps = collect($task->steps ?? []);
    $doneSteps = $steps->filter(function ($step) { return !empty($step->is_done); })->count();
    $stepCount = $steps->count();

    $progress = (int) ($task->progress_percent ?? 0);
    if ($progress <= 0 && $stepCount > 0) {
        $progress = (int) round(($doneSteps / max($stepCount, 1)) * 100);
    }
    if (($task->status ?? '') === 'done') { $progress = 100; }
    $progress = max(0, min(100, $progress));
@endphp

<div class="gt-progress-box gt-card-progress-compact">
    <div class="gt-progress-head">
        <span>Fortschritt</span>
        <span>{{ $progress }}%</span>
    </div>
    <div class="gt-progress-track">
        <div class="gt-progress-fill" style="width: {{ $progress }}%"></div>
    </div>
</div>
