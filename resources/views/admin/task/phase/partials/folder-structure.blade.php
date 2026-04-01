@php
    $phaseList = $phases ?? collect();
@endphp

@if($phaseList->isNotEmpty())
    <div class="card border mb-1">
        <div class="card-header">
            <h6>{{ ucfirst($stage) }} <span class="badge badge-primary">{{ $phaseList->count() }}</span></h6>
        </div>
        <div class="card-body p-1">
            <div class="sortable-phases" data-stage="{{ $stage }}">
                @if($phaseList->isNotEmpty())
                    <div class="sortable-phases" data-stage="{{ $stageName }}">
                        @foreach($phaseList as $phase)
                            <div class="phase-item" data-phase-id="{{ $phase->id }}">
                                <div class="folder-toggle"
                                    data-bs-toggle="collapse"
                                    data-target="#phase{{ $phase->id }}"
                                    data-phase-id="{{ $phase->id }}"
                                    style="background:white; border-bottom: 5px solid #f1f1f1;">

                                    <span class="folder-icon">+</span>

                                    <span class="folder-label heading sortable-phase">
                                        {{ $phase->phase_name }}
                                        @if($phase->status === 'Published')
                                            <span class="badge badge-success ml-1">Aktiv</span>
                                        @else
                                            <span class="badge badge-secondary ml-1">Inaktiv</span>
                                        @endif
                                    </span>

                                    <span class="total-sub-tasks">
                                        ({{ $phase->activities->whereNull('parent_id')->count() }})
                                    </span>

                                    <div class="button-container">
                                        <button type="button" class="btn btn-icon btn-flat-success" title="Aktiv"
                                            onclick="activePhase(this)"
                                            data-product-id="{{ $phase->product_id }}"
                                            data-section-id="{{ $phase->section_id }}"
                                            data-section-name="{{ $phase->section_name }}"
                                            data-phase-id="{{ $phase->id }}"
                                            data-stage="{{ $phase->stage }}"
                                            data-parent-id="">
                                            <i class="feather icon-check-square"></i>
                                        </button>

                                        <button type="button" class="btn btn-icon btn-flat-success" title="Neue Aktivität hinzufügen"
                                            onclick="showActivityModal(this)"
                                            data-product-id="{{ $phase->product_id }}"
                                            data-section-id="{{ $phase->section_id }}"
                                            data-section-name="{{ $phase->section_name }}"
                                            data-phase-id="{{ $phase->id }}"
                                            data-stage="{{ $phase->stage }}"
                                            data-parent-id="">
                                            <i class="feather icon-plus"></i>
                                        </button>

                                        <button class="btn btn-icon btn-flat-warning" title="Phase bearbeiten"
                                            onclick="editPhase(this)"
                                            data-phase-id="{{ $phase->id }}"
                                            data-phase-name="{{ $phase->phase_name }}"
                                            data-version="{{ $phase->version }}"
                                            data-stage-id="{{ $phase->stage_id }}"
                                            data-stage="{{ $phase->stage }}">
                                            <i class="feather icon-edit"></i>
                                        </button>

                                        <button class="btn btn-icon btn-flat-danger btn-delete-phase" title="Phase löschen"
                                            data-id="{{ $phase->id }}">
                                            <i class="feather icon-trash"></i>
                                        </button>

                                       <button class="btn btn-icon btn-flat-warning waves-effect waves-light btn-copy-activity"
                                            title="Aktivität kopieren"
                                            data-id="{{ $activity->id }}"
                                            data-phase-id="{{ $phase->id }}">
                                            <i class="feather icon-copy"></i>
                                        </button>

                                    </div>
                                </div>

                                <!-- Activities -->
                                <div class="collapse subfolder sortable-activities"
                                    id="phase{{ $phase->id }}"
                                    data-phase-id="{{ $phase->id }}">
                                    @foreach($phase->activities->whereNull('parent_id')->sortBy('sort_order') as $activity)
                                        @include('admin.task.phase.partials.phase-activity', ['activity' => $activity, 'phase' => $phase])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted pl-1 mb-2">Keine Phasen unter {{ ucfirst($stageName) }}.</div>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="text-muted pl-1 mb-2">Keine Phasen unter {{ ucfirst($stage) }}.</div>
@endif
