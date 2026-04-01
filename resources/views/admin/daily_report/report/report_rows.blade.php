@php
  /** Kanonischer Code -> Deutsches Label */
  $TYPE_MAP = [
    'Task'        => 'Aufgabe',
    'Appointment' => 'Termin',
    'Project'     => 'Projekt',
    'Offer'       => 'Angebot',
    'Problem'     => 'Ticket',
    'Pause'       => 'Pause',
    'Manual'      => 'Manuell',
    'Missing'     => 'Fehlend',    
    'Other'       => 'Sonstiges',
  ];
  $totalWorked = 0;
@endphp

@foreach ($entries as $entry)
@php
    $code       = $entry['type'] ?? 'Other';
    $label      = $TYPE_MAP[$code] ?? $code;
    $isMissing  = !empty($entry['is_missing']);
    $entryHours = (float) ($entry['hours'] ?? 0);
    if (!$isMissing) { $totalWorked += $entryHours; }

    $id         = $entry['id'] ?? null;
    $fromSaved  = !empty($entry['from_saved']);

    // NEW
    $billing    = $entry['billing_type'] ?? null;
    $category   = $entry['activity_category'] ?? null;
    $isTravel   = !empty($entry['is_travel']);
@endphp


  <tr
    class="daily_report_tr {{ $isMissing ? 'missing-row bg-light-danger' : '' }}"
    data-type="{{ $code }}"
    data-missing="{{ $isMissing ? '1' : '0' }}"
    data-id="{{ $id ?? '' }}"
  >
    {{-- Zeitspanne --}}
    <td>
      <div class="d-flex align-items-center">
        <input type="time"
               class="form-control  mr-1 start_time_input"
               name="start_time"
               value="{{ $entry['time_start'] }}">
        <span class="time_saperator mx-1">-</span>
        <input type="time"
               class="form-control  ml-1 end_time_input"
               name="end_time"
               value="{{ $entry['time_end'] }}">
      </div>
    </td>

    {{-- Stunden --}}
    <td>
      <input type="number"
             class="form-control  hours_spent_input"
             name="hours_spent"
             step="0.25" min="0"
             value="{{ number_format($entryHours, 2, '.', '') }}">
    </td>

    {{-- Arbeitsort / Adresse (editierbar für alle) --}}
    <td>
      <input type="text"
             class="form-control autocomplete-address"
             name="address"
             value="{{ $entry['address'] ?? '' }}"
             placeholder="Ort">
     
    </td>

    {{-- Typ --}}
    <td data-type="{{ $code }}">
      <select name="type" class="form-control select2" data-placeholder="Typ wählen">
        @foreach ($TYPE_MAP as $val => $text)
          <option value="{{ $val }}" {{ $code === $val ? 'selected' : '' }}>
            {{ $text }}
          </option>
        @endforeach
      </select>
    </td>

    {{-- Abrechnung / Kategorie / Reise --}}
  <td>
    <div class="mb-25">
      <select name="billing_type" class="form-control ">
        <option value="">Abrechnung…</option>
        <option value="billable"     {{ $billing === 'billable' ? 'selected' : '' }}>Abrechenbar</option>
        <option value="non_billable" {{ $billing === 'non_billable' ? 'selected' : '' }}>Nicht abrechenbar</option>
        <option value="internal"     {{ $billing === 'internal' ? 'selected' : '' }}>Intern</option>
      </select>
    </div>

    <div class="d-flex align-items-center">
      <input type="text"
            name="activity_category"
            class="form-control "
            placeholder="Kategorie"
            value="{{ $category ?? '' }}">

      <div class="custom-control custom-checkbox ml-50">
        <input type="checkbox"
              class="custom-control-input is_travel_input"
              id="travel_{{ $loop->index }}"
              name="is_travel"
              value="1"
              {{ $isTravel ? 'checked' : '' }}>
        <label class="custom-control-label"
              for="travel_{{ $loop->index }}"
              title="Reisezeit">
          <i class="feather icon-navigation"></i>
        </label>
      </div>
    </div>
  </td>



    {{-- Kunde (multi) --}}
    <td>
      @php
        $selectedIds = collect($entry['customers'] ?? [])
          ->pluck('id')
          ->map(fn($v)=>(string)$v)
          ->all();
      @endphp

      <select name="customer_ids[]"
              class="form-control select2 customer-multi"
              data-placeholder="Kunden wählen"
              multiple>
        @foreach ($customers as $customer)
          <option value="{{ $customer->id }}"
            {{ in_array((string)$customer->id, $selectedIds, true) ? 'selected' : '' }}>
            {{ $customer->name }} {{ $customer->lastname }}
          </option>
        @endforeach
      </select>

      {{-- per-customer share inputs --}}
      <div class="customer-shares mt-1">
        @if (!empty($entry['customers']))
          @foreach ($entry['customers'] as $c)
            <div class="form-row align-items-center mb-1 customer-share" data-id="{{ $c['id'] }}">
              <div class="col-12 col-md-4">
                <small class="text-muted">{{ $c['name'] }} {{ $c['lastname'] }}</small>
              </div>

              {{-- Stunden pro Kunde --}}
              <div class="col-12 col-md-3">
                <input type="number" step="0.25" min="0"
                      name="share_hours[{{ $c['id'] }}]"
                      class="form-control "
                      placeholder="Std."
                      value="{{ $c['share_hours'] !== null ? number_format($c['share_hours'],2,'.','') : '' }}">
              </div>

              {{-- Notiz + Icons --}}
              <div class="col-12 col-md-5 d-flex align-items-center">
                <input type="text"
                      name="customer_note[{{ $c['id'] }}]"
                      class="form-control  mr-1"
                      placeholder="Notiz"
                      value="{{ $c['note'] ?? '' }}">

                {{-- Kommentar-Icon (nutzt daily_report_notes) --}}
                <button type="button"
                        class="btn btn-icon btn-outline-secondary btn-notes mr-25"
                        title="Notizen"
                        data-date="{{ $start_date ?? ($entry['date'] ?? '') }}"
                        data-entry="{{ $id ?? ($entry['id'] ?? '__null') }}">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"
                          stroke="currentColor" stroke-width="1.6"/>
                    <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.6"/>
                  </svg>
                </button>

                {{-- Datei-Icon (nutzt daily_report_attachments) --}}
                <button type="button"
                        class="btn btn-icon btn-outline-secondary btn-attach"
                        title="Anhänge"
                        data-date="{{ $start_date ?? ($entry['date'] ?? '') }}"
                        data-entry="{{ $id ?? ($entry['id'] ?? '') }}">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M21 15V7a5 5 0 0 0-10 0v10a3 3 0 0 0 6 0V8"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>
            </div>
          @endforeach
        @endif
      </div>

    </td>

    {{-- Beschreibung (editierbar für alle) --}}
    <td>
      <input type="text"
             name="description"
             class="form-control description_input"
             placeholder="Beschreibung"
             value="{{ $entry['description'] ?? '' }}">
    </td>

    {{-- Aktionen (nur EIN <td>!) --}}
    <td class="text-center action-with-counter">
      {{-- Speichern / Übernehmen: immer ein Button, nie nur Icon --}}
      <button type="button"
              class="btn btn-icon btn-success saveRow"
              data-id="{{ $id ?? '' }}"
              title="{{ $fromSaved ? 'Änderungen speichern' : 'Eintrag übernehmen' }}">
        <i class="feather {{ $fromSaved ? 'icon-save' : 'icon-check' }}"></i>
      </button>

      {{-- Löschen nur für echte gespeicherte Einträge --}}
      @if (!empty($id))
        <button type="button"
                class="btn btn-icon btn-danger deleteRow"
                data-id="{{ $id }}"
                title="Löschen">
          <i class="feather icon-trash"></i>
        </button>
      @endif

      {{-- Notizen --}}
      <span class="position-relative d-inline-block mr-1">
        <button type="button"
                class="btn btn-icon btn-outline-secondary btn-notes"
                title="Notes"
                data-date="{{ $start_date ?? ($entry['date'] ?? '') }}"
                data-entry="{{ $id ?? '__null' }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.8"/>
          </svg>
        </button>
        <span class="count-badge note-count hidden" data-entry="{{ $id ?? '' }}">0</span>
      </span>

      {{-- Anhänge --}}
      <span class="position-relative d-inline-block">
        <button type="button"
                class="btn btn-icon btn-outline-secondary btn-attach"
                title="Anhänge"
                data-date="{{ $start_date ?? ($entry['date'] ?? '') }}"
                data-entry="{{ $id ?? '' }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 15V7a5 5 0 0 0-10 0v10a3 3 0 0 0 6 0V8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </button>
        <span class="count-badge attach-count hidden" data-entry="{{ $id ?? '' }}">0</span>
      </span>
    </td>
  </tr>
@endforeach

{{-- Summen --}}
<tr class="total_footer font-weight-bold">
  <td colspan="1" class="text-uppercase">GESAMTZEIT</td>
  <td id="worked_total">{{ number_format($totalWorked, 2, ',', '.') }} Std.</td>
  <td colspan="5"></td>
  <td id="missing_hours"></td>
</tr>
