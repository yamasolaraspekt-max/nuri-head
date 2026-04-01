<div class="sortable-item" data-activity-id="{{ $activity->id }}">
    <div class="folder-toggle"
        data-toggle="collapse"
        data-target="#activity{{ $activity->id }}">
        <span class="folder-icon" data-target="#activity{{ $activity->id }}">+</span>
        <span class="folder-label sub-data" data-activity-id="{{ $activity->id }}">
            {{ $loop->index + 1 }} . {{ $activity->title }}
        </span>

        <div class="button-container">

            @php 
                $hasPhaseSet = \App\Models\MasterSet::where('task_phase_id', $phase->id)->exists();
                $activitySetCount = \App\Models\MasterSet::where('phase_activity_id', $activity->id)->count();
            @endphp
            
            {{-- FIXED BUTTON: Uses js-open-master-set class, handles disabled state, removed onclick --}}
            <button type="button"
                class="btn btn-icon btn-flat-info js-open-master-set {{ $hasPhaseSet ? 'disabled' : '' }}"
                title="{{ $hasPhaseSet ? 'Phase hat bereits ein Set' : 'Set verknüpfen' }}"
                data-type="activity"
                data-target-id="{{ $activity->id }}"
                data-product-id="{{ $phase->product_id }}"
                @if($hasPhaseSet) disabled @endif>
                <i class="feather icon-package"></i>
                @if($activitySetCount > 0)
                    <span class="badge badge-pill badge-danger badge-up">{{ $activitySetCount }}</span>
                @endif
            </button>


            
            <button class="btn btn-icon btn-flat-warning waves-effect waves-light"
                onclick="showActivityModal(this)"
                data-section-id="{{ $phase->section_id }}"
                data-section-name="{{ $phase->section_name }}"
                data-product-id="{{ $phase->product_id }}"
                data-phase-id="{{ $phase->id }}"
                data-stage="{{ $phase->stage }}"
                data-parent-id="{{ $activity->id }}">
                <i class="feather icon-plus"></i>
            </button>

            <button class="btn btn-icon btn-flat-warning waves-effect waves-light"
                title="Aktivität bearbeiten"
                onclick="editActivity({{ $activity->id }})">
                <i class="feather icon-edit"></i>
            </button>

            <button class="btn btn-icon btn-flat-danger waves-effect waves-light btn-delete-activity"
                title="Aktivität löschen"
                data-id="{{ $activity->id }}">
                <i class="feather icon-trash"></i>
            </button>

            <button class="btn btn-icon btn-flat-warning waves-effect waves-light"
                title="Aktivität kopieren"
                data-id="{{ $activity->id }}"
                data-phase-id="{{ $phase->id }}">
                <i class="feather icon-copy"></i>
            </button>
        </div>
    </div>

    <!-- Child Activities -->
    <div class="collapse subfolder" id="activity{{ $activity->id }}">
        @foreach($phase->activities->where('parent_id', $activity->id) as $child)
            <div>
                <span class="folder-label sub-data" data-activity-id="{{ $child->id }}">
                    - {{ $child->title }}
                </span>

                <button class="btn btn-icon btn-flat-warning waves-effect waves-light"
                    onclick="editActivity({{ $child->id }})">
                    <i class="feather icon-edit"></i>
                </button>

                <button class="btn btn-icon btn-flat-danger waves-effect waves-light btn-delete-activity"
                    data-id="{{ $child->id }}">
                    <i class="feather icon-trash"></i>
                </button>

                <button class="btn btn-icon btn-flat-warning waves-effect waves-light"
                    data-id="{{ $child->id }}"
                    data-phase-id="{{ $phase->id }}">
                    <i class="feather icon-copy"></i>
                </button>
            </div>
        @endforeach
    </div>
</div>
