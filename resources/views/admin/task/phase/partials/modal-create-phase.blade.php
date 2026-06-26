{{-- resources/views/admin/task/phase/partials/modal-create-phase.blade.php --}}

<div class="phase-modal-backdrop" id="primary" aria-hidden="true">
    <div class="phase-modal sm" role="dialog" aria-modal="true" aria-labelledby="createPhaseTitle">
        <form method="post" action="{{ route('task.phase.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="phase-modal-head">
                <div>
                    <h5 class="phase-modal-title" id="createPhaseTitle">Neue Phase</h5>
                    <div class="phase-modal-subtitle">
                        Neue Phase für diese Sektion erstellen.
                    </div>
                </div>

                <button type="button" class="phase-modal-close" data-close-phase-modal>
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="phase-modal-body">
                <div class="phase-form-grid">
                    <div class="phase-form-group full">
                        <label class="phase-label">Name</label>
                        <input type="text" name="phase_name" class="phase-input" value="{{ old('phase_name') }}" required>

                        <input type="hidden" name="product_id" id="product_id" value="{{ request()->product }}">
                        <input type="hidden" name="section_id" value="{{ request()->section_id }}">
                        <input type="hidden" name="section_name" value="{{ $section->phase_section }}">
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Version</label>
                        <select name="version" id="modal_version" class="phase-select select2">
                            <option value="">-- Bitte wählen --</option>
                            @foreach ($groupedStages as $version => $stagesInVersion)
                                <option value="{{ $version }}">Version: {{ $version }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Phase / Stage</label>
                        <select name="stage_id" id="modal_stage_id" class="phase-select select2">
                            <option value="">-- Bitte wählen --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="phase-modal-footer">
                <button type="button" class="phase-btn-soft" data-close-phase-modal>
                    Abbrechen
                </button>

                <button type="submit" class="phase-btn">
                    <i class="feather icon-save"></i>
                    Speichern
                </button>
            </div>
        </form>
    </div>
</div>