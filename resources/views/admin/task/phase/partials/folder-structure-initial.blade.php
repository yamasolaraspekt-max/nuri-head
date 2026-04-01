@php
  $phasesGrouped = ($phases ?? collect())->groupBy('stage_id');
@endphp


@foreach(($groupedStages[$currentVersion] ?? collect()) as $stageModel)
    @php
        $stageName = $stageModel->stage;
         $phaseList = $phasesGrouped[$stageModel->id] ?? collect();

    @endphp

    <div class="card border mb-1">
        <div class="card-header d-flex justify-content-between align-items-center mb-1"
             style="cursor: pointer;"
             data-toggle="collapse"
             data-target="#collapseStage{{ \Illuminate\Support\Str::slug($stageName) }}"
             aria-expanded="true">
            <h6 class="mb-0">
                {{ ucfirst($stageName) }}
                <span class="badge badge-primary">{{ $phaseList->count() }}</span>
            </h6>
            <i class="feather icon-chevron-down"></i>
        </div>

        <div id="collapseStage{{ \Illuminate\Support\Str::slug($stageName) }}" class="collapse show">
            <div class="card-body p-1">
                <div class="sortable-phases"
                    data-stage="{{ $stageName }}"
                    data-stage-id="{{ $stageModel->id }}"
                    data-version="{{ $stageModel->version }}">
                    @forelse($phaseList as $phase)
                        @include('admin.task.phase.partials.single-phase', ['phase' => $phase])
                    @empty
                        <div class="text-muted p-2">Keine Phase vorhanden</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endforeach
