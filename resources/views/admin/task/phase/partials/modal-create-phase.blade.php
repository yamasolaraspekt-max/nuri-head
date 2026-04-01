{{-- Modal: NEUE PHASE --}}
<div class="modal fade" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <form method="post"
                  action="{{ action('App\Http\Controllers\TaskPhaseController@store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Name</label>
                        <div class="col-md-8">
                            <input type="text" name="phase_name" class="form-control" value="{{ old('phase_name') }}">
                            <input type="hidden" name="product_id" id="product_id" value="{{ request()->product }}">
                            <input type="hidden" name="section_id" value="{{ request()->section_id }}">
                            <input type="hidden" name="section_name" value="{{ $section->phase_section }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Version</label>
                        <div class="col-md-8">
                            <select name="version" id="modal_version" class="form-control select2">
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
                            <select name="stage_id" id="modal_stage_id" class="form-control select2">
                                <option value="">-- Bitte wählen --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">speichern</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                </div>
            </form>
        </div>
    </div>
</div>
