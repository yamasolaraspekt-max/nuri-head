@php
    $phaseGroup = $groupedPhases[$stageKey] ?? collect();
    $firstItem = $phaseGroup->first();
    $stageLabel = strtoupper($stageKey);
    $isCurrentStage = $currentStageKey === $stageKey;
    $total = $phaseGroup->filter(fn($r) => $r->activity)->count();
    $doneCount = $phaseGroup->filter(fn($r) => $r->is_done == 1)->count();

    $allEmployees = collect($groupedPhases)
        ->flatten(1)
        ->pluck('done_by')
        ->filter()
        ->unique()
        ->map(fn($id) => \App\Models\Employee::find($id))
        ->filter();

    $activities = collect();
    $nextActivity = null;

    $phaseId = optional(optional($firstItem)->phase)->id;
    $activityId = optional(optional($firstItem)->activity)->id;
    $productId = optional($firstItem)->product_id ?? 'x';

    if ($phaseId) {
        $activities = DB::table('phase_activities')
            ->where('phase_id', $phaseId)
            ->orderBy('sort_order')
            ->get();

        $currentIndex = $activities->search(fn($a) => $a->id == $activityId);
        $nextActivity = $activities->get($currentIndex + 1);
    }
@endphp

@if($phaseGroup->filter(fn($item) => $item->phase !== null || $item->activity !== null)->isNotEmpty())
    <div class="card mt-0 mb-1">
        {{-- Collapsible Content --}}
        <div id="stage-{{ $stageKey }}" class="collapse">
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">Kurz</th>
                            <th>Beschreibung</th>
                            <th style="width: 80px;">Erledigt!</th>
                            <th style="width: 100px;">Datum</th>
                            <th style="width: 100px;">Erledigt von</th>
                            <th style="width: 200px;">Zuständig</th>
                            <th style="width: 100px;">Dokument</th>
                            <th>Notiz</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($phaseGroup->groupBy(fn($item) => optional($item->phase)->id) as $groupPhaseId => $phaseActs)
                            @php $phase = optional($phaseActs->first())->phase; @endphp

                            @if ($phase)
                                @php
                                    $allActivities = $phase->activities ?? collect();
                                    $total = $allActivities->count();
                                    $doneCount = $allActivities->filter(fn($act) =>
                                        \App\Models\CustomerHistory::where([
                                            ['activity_id', $act->id],
                                            ['customer_id', $customer_id],
                                            ['alternative_id', $alternative_id],
                                            ['is_done', 1],
                                        ])->exists()
                                    )->count();
                                @endphp

                                <tr class="bg-light">
                                    <td colspan="8">
                                        <strong>{{ $phase->phase_name }}</strong>
                                        <span class="badge badge-dark ml-2">{{ $doneCount }} / {{ $total }} erledigt</span>
                                    </td>
                                </tr>

                                @foreach ($phaseActs->filter(fn($a) => optional($a->activity)->parent_id === null) as $act)
                                    @include('admin.new_leads.layouts._activity_row', [
                                        'act' => $act,
                                        'allActivities' => $phaseGroup,
                                        'level' => 0,
                                        'customer' => (object)['id' => $customer_id],
                                        'alternative' => (object)['id' => $alternative_id],
                                        'currentActivityId' => $currentActivityId,
                                    ])
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
