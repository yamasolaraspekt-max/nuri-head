{{-- 
  Saved Kanban filters topbar
  Place this above <div class="kanban-zoom-card"> or inside the Kanban tab before the board.
--}}
<div class="kb-filter-mode-bar" id="kbFilterModeBar">
    <div class="kb-filter-mode-copy">
        <span class="kb-filter-mode-icon"><i class="feather icon-filter"></i></span>
        <div>
            <strong>Möchtest du meine Filter sehen oder alle Kunden?</strong>
            <small>Speichere Kunden-, Produkt-, Unternehmen- oder andere Filter als persönliche Ansicht.</small>
        </div>
    </div>

    <div class="kb-filter-mode-actions">
        <button type="button" class="kb-filter-mode-btn is-active" data-kb-filter-scope="all">
            <i class="feather icon-users"></i>
            Alle Kunden
        </button>

        <button type="button" class="kb-filter-mode-btn" data-kb-filter-scope="mine">
            <i class="feather icon-user-check"></i>
            Meine Filter
        </button>

        <select id="kbSavedFilterSelect" class="form-control kb-saved-filter-select">
            <option value="">Gespeicherten Filter wählen…</option>
            @foreach(($kanbanFilterSettings ?? collect()) as $filterSetting)
                <option value="{{ $filterSetting->id }}"
                        data-default="{{ $filterSetting->is_default ? 1 : 0 }}"
                        data-filters='@json($filterSetting->filters ?? [])'>
                    {{ $filterSetting->is_default ? '★ ' : '' }}{{ $filterSetting->name }}
                </option>
            @endforeach
        </select>

        <button type="button" class="kb-filter-mode-btn kb-filter-save-btn" id="kbSaveCurrentFilter">
            <i class="feather icon-save"></i>
            Filter speichern
        </button>
    </div>
</div>
