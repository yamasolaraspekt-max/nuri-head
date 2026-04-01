@if($controllers->count())
    <div class="flex flex-wrap gap-1 mt-1">
        @foreach($controllers as $ctrl)
            <div class="badge badge-primary d-flex align-items-center gap-2">
                {{ $ctrl->employee->name ?? '' }} {{ $ctrl->employee->lastname ?? '' }}
                <button class="ml-2 text-white hover:text-red-500" style="background:transparent;border:none;"
                        onclick="deleteController({{ $ctrl->id }}, {{ $ctrl->project_id }}, {{ $ctrl->phase_id }})">
                    <i class="feather icon-trash-2"></i>
                </button>
            </div>
        @endforeach
    </div>
@else
    <small class="text-gray-500">Kein Kontrolleur zugewiesen</small>
@endif
