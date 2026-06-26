{{-- resources/views/admin/task/phase/partials/modal-edit-phase.blade.php --}}

<div class="phase-modal-backdrop" id="editPhaseModal" aria-hidden="true">
    <div class="phase-modal sm" role="dialog" aria-modal="true" aria-labelledby="editPhaseLabel">
        <form
            id="editPhaseForm"
            method="post"
            action=""
            data-action-template="{{ url('/task-phases') }}/:id/update"
        >
            @csrf

            <input type="hidden" name="phase_id" id="edit_phase_id">

            <div class="phase-modal-head">
                <div>
                    <h5 class="phase-modal-title" id="editPhaseLabel">Phase bearbeiten</h5>
                    <div class="phase-modal-subtitle">
                        Name, Version und Stage dieser Phase ändern.
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
                        <input
                            type="text"
                            name="edit_phase_name"
                            id="edit_phase_name"
                            class="phase-input"
                            placeholder="Phasenname"
                            required
                        >
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Version</label>
                        <select name="edit_version" id="modal_edit_version" class="phase-select select2">
                            <option value="">-- Bitte wählen --</option>
                            @foreach ($groupedStages as $version => $stagesInVersion)
                                <option value="{{ $version }}">Version: {{ $version }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Phase / Stage</label>
                        <select name="edit_stage_id" id="modal_edit_stage_id" class="phase-select select2">
                            <option value="">-- Bitte wählen --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="phase-modal-footer">
                <button type="button" class="phase-btn-soft" data-close-phase-modal>
                    Abbrechen
                </button>

                <button type="submit" class="phase-btn-warning">
                    <i class="feather icon-save"></i>
                    Änderung speichern
                </button>
            </div>
        </form>
    </div>
</div>