<div class="oc-card" style="padding:16px">
  <form id="rentForm" class="oc-form-section">@csrf<input type="hidden" name="id" id="rent_id">
    <div class="oc-form-section-title"><span id="rentFormTitle">Miete / Objekt hinzufügen</span><button class="oc-btn-soft" id="resetRentBtn" type="button"><i data-lucide="plus"></i> Neu</button></div>
    <div class="oc-form-grid thirds">
      <div><label class="oc-label">Objektname *</label><input class="oc-field" name="object_name" id="rent_object_name" required></div>
      <div><label class="oc-label">Typ</label><input class="oc-field" name="object_type" id="rent_object_type" placeholder="Büro, Lager, Wohnung..."></div>
      <div><label class="oc-label">Status</label><select class="oc-field" name="status" id="rent_status">@foreach($rentStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
      <div><label class="oc-label">Kaltmiete</label><input type="number" step="0.01" class="oc-field calc-rent" name="rent_cost" id="rent_cost"></div>
      <div><label class="oc-label">Nebenkosten</label><input type="number" step="0.01" class="oc-field calc-rent" name="extra_cost" id="rent_extra_cost"></div>
      <div><label class="oc-label">Total</label><input type="number" step="0.01" class="oc-field" name="total" id="rent_total"></div>
      <div><label class="oc-label">Stadt</label><input class="oc-field" name="city" id="rent_city"></div>
      <div><label class="oc-label">Straße</label><input class="oc-field" name="street" id="rent_street"></div>
      <div><label class="oc-label">Hausnr.</label><input class="oc-field" name="house_no" id="rent_house_no"></div>
      <div><label class="oc-label">PLZ</label><input class="oc-field" name="postcode" id="rent_postcode"></div>
      <div><label class="oc-label">Vermieter</label><input class="oc-field" name="landlord_name" id="landlord_name"></div>
      <div><label class="oc-label">Kontakt</label><input class="oc-field" name="landlord_contact" id="landlord_contact"></div>
      <div><label class="oc-label">Vertrag Start</label><input type="date" class="oc-field" name="contract_start" id="contract_start"></div>
      <div><label class="oc-label">Vertrag Ende</label><input type="date" class="oc-field" name="contract_end" id="contract_end"></div>
      <div><label class="oc-label">Zyklus</label><input class="oc-field" name="payment_cycle" id="payment_cycle" placeholder="Monatlich, jährlich..."></div>
      <div><label class="oc-label">Fälligkeitstag</label><input type="number" min="1" max="31" class="oc-field" name="due_day" id="due_day"></div>
      <div><label class="oc-label">Nächste Fälligkeit</label><input type="date" class="oc-field" name="next_due_date" id="rent_next_due_date"></div>
      <div class="oc-span-3"><label class="oc-label">Notiz</label><textarea class="oc-field" name="notes" id="rent_notes" rows="2"></textarea></div>
    </div>
    <div class="mt-1"><button class="oc-btn" type="submit"><i data-lucide="save"></i> Miete speichern</button></div>
  </form>

  <div class="oc-toolbar"><div class="oc-filter-block search"><label class="oc-filter-label">Miete suchen</label><input class="oc-input search" id="rentSearch"></div></div>
  <div id="rentList" class="oc-mini-list"></div>
  <div class="oc-pagination"><div id="rentPaginationInfo" class="text-muted"></div><div id="rentPaginationButtons" class="d-flex" style="gap:6px"></div></div>
</div>
