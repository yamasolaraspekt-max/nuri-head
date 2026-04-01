<div class="phase-item"
     data-phase-id="{{ $phase->id }}"
     data-stage-id="{{ $phase->stage_id }}"
     data-version="{{ $phase->version }}">
    <div class="folder-toggle"
        data-toggle="collapse"
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
            
            @php $setCount = \App\Models\MasterSet::where('task_phase_id', $phase->id)->count(); @endphp
            
           <button type="button"
            class="btn btn-icon btn-flat-info waves-effect waves-light js-open-master-set"
            data-type="phase"
            data-target-id="{{ $phase->id }}"
            data-product-id="{{ $section->product_id }}"
            title="Master Set">
            <i class="feather icon-link"></i>
            </button>


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

            <button class="btn btn-icon btn-flat-warning btn-copy-phase" title="Aktivität kopieren"
                data-phase-id="{{ $phase->id }}">
                <i class="feather icon-copy"></i>
            </button>
        </div>
    </div>

    <div class="collapse subfolder sortable-activities"
    id="phase{{ $phase->id }}"
    data-phase-id="{{ $phase->id }}">
        @foreach($phase->activities->whereNull('parent_id')->sortBy('sort_order') as $activity)
            @include('admin.task.phase.partials.phase-activity', ['activity' => $activity, 'phase' => $phase])
        @endforeach
    </div>
</div>