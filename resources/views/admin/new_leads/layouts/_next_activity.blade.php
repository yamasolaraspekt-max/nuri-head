@php
    $nextRealActivity = null;

    if ($phaseId) {
        $activities = \App\Models\PhaseActivities::where('phase_id', $phaseId)
            ->orderBy('sort_order')
            ->get();

        foreach ($activities as $act) {
            $isDone = \App\Models\CustomerHistory::where([
                ['customer_id', $customer_id],
                ['alternative_id', $alternative_id],
                ['product_id', $productId],
                ['phase_id', $phaseId],
                ['activity_id', $act->id],
                ['is_done', 1],
            ])->exists();

            if (!$isDone) {
                $nextRealActivity = $act;
                break;
            }
        }
    }
@endphp

<div id="nextStepContainer_{{ $phaseId }}_{{ $activityId }}_{{ $productId }}">
    @if($nextRealActivity)
        <p class="m-0 font-weight-bold text-dark">{{ $nextRealActivity->title }}</p>
        <p class="m-0 text-muted">{{ $nextRealActivity->description }}</p>
    @else
        <p class="text-muted">Alle Schritte erledigt 🎉</p>
    @endif
</div>
