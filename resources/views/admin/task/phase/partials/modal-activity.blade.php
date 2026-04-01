{{-- Activity Modal --}}
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="activityForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <div class="title d-flex align-items-center">
                            Aufgabenschritt
                            <div class="avatar mr-1 ml-2">
                                <img src="{{ asset('images/employee/'.$user->image) }}"
                                     alt="avtar img holder"
                                     height="32"
                                     width="32">
                            </div>
                        </div>
                    </h5>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Title / hidden IDs --}}
                        <div class="col-md-12">
                            <label for="Title">Aufgabentitel</label>
                            <input type="hidden" value="" name="product_id" id="product_id">
                            <input type="hidden" value="" name="parent_id" id="parent_id">
                            <input type="hidden" id="phase_id" name="phase_id">
                            <input type="hidden" value="" name="section_id" id="section_id">
                            <input type="hidden" value="" name="section_name" id="section_name">
                            <input type="hidden" id="activity_id" name="activity_id">
                            <input type="text" class="form-control" name="title">
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Aufgabenbeschreibung</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                            </div>
                        </div>

                        {{-- Department --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Abteilung/Gewerk</label>
                                <select class="form-control select2-tags" name="department_id[]" multiple>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Position --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Qualifikation</label>
                                <select class="form-control select2-tags" name="position_id[]" multiple>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->position }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Article / product --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Produkt</label>
                                <select class="form-control select2-tags" name="article_id[]" multiple>
                                    @foreach ($articles as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->article_no }} - {{ $product->product }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Duration --}}
                        <div class="col-md-4">
                            <label>Aufgabendauer</label>
                            <input type="time" name="duration" class="form-control" placeholder="Dauer">
                        </div>

                        {{-- Photo checkbox --}}
                        <div class="col-md-3">
                            <fieldset class="mt-2">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" value="needed" name="photo">
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span>Foto?</span>
                                </div>
                            </fieldset>
                        </div>

                        {{-- Link --}}
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Link Url</label>
                                <input type="text" class="form-control" name="link" placeholder="Youtube, Website, Drive...">
                            </div>
                        </div>

                        {{-- Answered by --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Verantwortungsbereich</label>
                                <select class="form-control" name="answered_by">
                                    <option value="1">Kunden</option>
                                    <option value="2" selected>Mitarbeiter</option>
                                </select>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Hinweis</label>
                                <textarea name="note" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">speichern</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
