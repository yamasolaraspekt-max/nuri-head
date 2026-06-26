{{-- resources/views/admin/task/phase/partials/modal-activity.blade.php --}}

<div class="phase-modal-backdrop" id="activityModal" aria-hidden="true">
    <div class="phase-modal xl" role="dialog" aria-modal="true" aria-labelledby="activityModalTitle">
        <form id="activityForm">
            <div class="phase-modal-head">
                <div>
                    <h5 class="phase-modal-title" id="activityModalTitle">Aufgabenschritt</h5>

                    <div class="phase-modal-subtitle">
                        <span class="phase-user-chip">
                            <img src="{{ asset('images/employee/' . ($user->image ?? 'users.png')) }}" alt="">
                            {{ trim(($user->name ?? '') . ' ' . ($user->lastname ?? '')) }}
                        </span>
                    </div>
                </div>

                <button type="button" class="phase-modal-close" data-close-phase-modal>
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="phase-modal-body">
                <input type="hidden" value="" name="product_id" id="product_id">
                <input type="hidden" value="" name="parent_id" id="parent_id">
                <input type="hidden" id="phase_id" name="phase_id">
                <input type="hidden" value="" name="section_id" id="section_id">
                <input type="hidden" value="" name="section_name" id="section_name">
                <input type="hidden" id="activity_id" name="activity_id">

                <div class="phase-form-grid">
                    <div class="phase-form-group full">
                        <label class="phase-label">Aufgabentitel</label>
                        <input type="text" class="phase-input" name="title" required>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Aufgabenbeschreibung</label>
                        <textarea name="description" rows="3" class="phase-textarea"></textarea>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Abteilung / Gewerk</label>
                        <select class="phase-select select2-tags" name="department_id[]" multiple>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Qualifikation</label>
                        <select class="phase-select select2-tags" name="position_id[]" multiple>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->position }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Produkt</label>
                        <select class="phase-select select2-tags" name="article_id[]" multiple>
                            @foreach ($articles as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->article_no }} - {{ $product->product }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="phase-form-group">
                        <label class="phase-label">Aufgabendauer</label>
                        <input type="time" name="duration" class="phase-input">
                    </div>

                    <div class="phase-form-group">
                        <label class="phase-label">Foto</label>
                        <label class="phase-check-card">
                            <input type="checkbox" value="needed" name="photo">
                            Foto benötigt
                        </label>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Link URL</label>
                        <input type="text" class="phase-input" name="link" placeholder="Youtube, Website, Drive...">
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Verantwortungsbereich</label>
                        <select class="phase-select" name="answered_by">
                            <option value="1">Kunden</option>
                            <option value="2" selected>Mitarbeiter</option>
                        </select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label">Hinweis</label>
                        <textarea name="note" rows="3" class="phase-textarea"></textarea>
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