{{-- Task Phase Modal Edit --}}
<div class="modal fade" id="editPhaseModal" tabindex="-1" role="dialog" aria-labelledby="editPhaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning white">
                <h5 class="modal-title" id="editPhaseLabel">PHASE BEARBEITEN</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form id="editPhaseForm"
                  method="post"
                  action=""
                  data-action-template="{{ url('/task-phases') }}/:id/update">
                @csrf

                <input type="hidden" name="phase_id" id="edit_phase_id">

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Name</label>
                        <div class="col-md-8">
                            <input type="text" name="edit_phase_name" id="edit_phase_name"
                                   class="form-control" placeholder="Phasenname">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Version</label>
                        <div class="col-md-8">
                            <select name="edit_version" id="modal_edit_version" class="form-control select2">
                                <option value="">-- Bitte wählen --</option>
                                @foreach ($groupedStages as $version => $stagesInVersion)
                                    <option value="{{ $version }}">Version: {{ $version }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Phase</label>
                        <div class="col-md-8">
                            <select name="edit_stage_id" id="modal_edit_stage_id" class="form-control select2">
                                <option value="">-- Bitte wählen --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Änderung speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>
