<div class="oc-modal-backdrop" id="createGroupModal">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Neue Artikel-Gruppe</h3>
            <button type="button" class="oc-btn-ic" onclick="closeModal('createGroupModal')">×</button>
        </div>

        <form class="js-ajax-form" method="POST" action="{{ route('article_group.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    <div>
                        <label class="oc-label">Initial</label>
                        <input type="text" name="initial" class="oc-input">
                    </div>
                    <div>
                        <label class="oc-label">Artikel-Gruppe *</label>
                        <input type="text" name="article_group" class="oc-input" required>
                    </div>
                    <div>
                        <label class="oc-label">Min-Wert</label>
                        <input type="number" step="0.01" name="min_value" class="oc-input">
                    </div>
                    <div>
                        <label class="oc-label">Max-Wert</label>
                        <input type="number" step="0.01" name="max_value" class="oc-input">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="oc-label">Foto</label>
                        <input type="file" name="image" class="oc-input">
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('createGroupModal')">Abbrechen</button>
                <button type="submit" class="oc-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

@foreach($data as $group)
    <div class="oc-modal-backdrop" id="editGroupModal-{{ $group->id }}">
        <div class="oc-modal">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Artikel-Gruppe bearbeiten</h3>
                <button type="button" class="oc-btn-ic" onclick="closeModal('editGroupModal-{{ $group->id }}')">×</button>
            </div>

            <form class="js-ajax-form" method="POST" action="{{ route('article_group.update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $group->id }}">

                <div class="oc-modal-b">
                    <div class="oc-form-grid">
                        <div>
                            <label class="oc-label">Initial</label>
                            <input type="text" name="initial" class="oc-input" value="{{ $group->initial }}">
                        </div>
                        <div>
                            <label class="oc-label">Artikel-Gruppe *</label>
                            <input type="text" name="article_group" class="oc-input" value="{{ $group->article_group }}" required>
                        </div>
                        <div>
                            <label class="oc-label">Min-Wert</label>
                            <input type="number" step="0.01" name="min_value" class="oc-input" value="{{ $group->min_value }}">
                        </div>
                        <div>
                            <label class="oc-label">Max-Wert</label>
                            <input type="number" step="0.01" name="max_value" class="oc-input" value="{{ $group->max_value }}">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label class="oc-label">Foto</label>
                            <input type="file" name="image" class="oc-input">
                        </div>
                    </div>
                </div>

                <div class="oc-modal-f">
                    <button type="button" class="oc-btn-soft" onclick="closeModal('editGroupModal-{{ $group->id }}')">Abbrechen</button>
                    <button type="submit" class="oc-btn">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($group->subArticleGroups as $sub)
        <div class="oc-modal-backdrop" id="editSubModal-{{ $sub->id }}">
            <div class="oc-modal">
                <div class="oc-modal-h">
                    <h3 class="oc-modal-ttl">Sub-Artikelgruppe bearbeiten</h3>
                    <button type="button" class="oc-btn-ic" onclick="closeModal('editSubModal-{{ $sub->id }}')">×</button>
                </div>

                <form class="js-ajax-form" method="POST" action="{{ route('sub_article_group.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $sub->id }}">

                    <div class="oc-modal-b">
                        <div class="oc-form-grid">
                            <div>
                                <label class="oc-label">Initial</label>
                                <input type="text" name="initial" class="oc-input" value="{{ $sub->initial }}">
                            </div>
                            <div>
                                <label class="oc-label">Sub-Artikel *</label>
                                <input type="text" name="sub_article" class="oc-input" value="{{ $sub->sub_article }}" required>
                            </div>
                            <div>
                                <label class="oc-label">Wert</label>
                                <input type="text" name="value" class="oc-input" value="{{ $sub->value }}">
                            </div>
                            <div>
                                <label class="oc-label">Status</label>
                                <input type="text" name="status" class="oc-input" value="{{ $sub->status }}">
                            </div>
                        </div>
                    </div>

                    <div class="oc-modal-f">
                        <button type="button" class="oc-btn-soft" onclick="closeModal('editSubModal-{{ $sub->id }}')">Abbrechen</button>
                        <button type="submit" class="oc-btn">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endforeach