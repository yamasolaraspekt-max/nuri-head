<div class="oc-card" style="padding:16px">
  <form id="insuranceForm" class="oc-form-section">@csrf<input type="hidden" name="id" id="insurance_id">
    <div class="oc-form-section-title"><span id="insuranceFormTitle">Versicherung hinzufügen</span><button class="oc-btn-soft" id="resetInsuranceBtn" type="button"><i data-lucide="plus"></i> Neu</button></div>
    <div class="oc-form-grid thirds">
      <div><label class="oc-label">Versicherung für *</label><input class="oc-field" name="insurance_for" id="insurance_for" required></div>
      <div><label class="oc-label">Anbieter</label><input class="oc-field" name="provider" id="provider"></div>
      <div><label class="oc-label">Police Nr.</label><input class="oc-field" name="policy_number" id="policy_number"></div>
      <div><label class="oc-label">Deckungssumme</label><input type="number" step="0.01" class="oc-field" name="coverage_amount" id="coverage_amount"></div>
      <div><label class="oc-label">Monatlich</label><input type="number" step="0.01" class="oc-field" name="monthly_payable" id="monthly_payable"></div>
      <div><label class="oc-label">Status</label><select class="oc-field" name="status" id="insurance_status">@foreach($insuranceStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
      <div><label class="oc-label">Start</label><input type="date" class="oc-field" name="start_date" id="insurance_start_date"></div>
      <div><label class="oc-label">Ende</label><input type="date" class="oc-field" name="end_date" id="insurance_end_date"></div>
      <div><label class="oc-label">Zyklus</label><input class="oc-field" name="payment_cycle" id="insurance_payment_cycle"></div>
      <div><label class="oc-label">Fälligkeitstag</label><input type="number" min="1" max="31" class="oc-field" name="due_day" id="insurance_due_day"></div>
      <div><label class="oc-label">Nächste Fälligkeit</label><input type="date" class="oc-field" name="next_due_date" id="insurance_next_due_date"></div>
      <div><label class="oc-label">Letzte Zahlung</label><input type="date" class="oc-field" name="payment_date" id="insurance_payment_date"></div>
      <div class="oc-span-3"><label class="oc-label">Notiz</label><textarea class="oc-field" name="notes" id="insurance_notes" rows="2"></textarea></div>
    </div>
    <div class="mt-1"><button class="oc-btn" type="submit"><i data-lucide="save"></i> Versicherung speichern</button></div>
  </form>

  <div class="oc-toolbar"><div class="oc-filter-block search"><label class="oc-filter-label">Versicherung suchen</label><input class="oc-input search" id="insuranceSearch"></div></div>
  <div id="insuranceList" class="oc-mini-list"></div>
  <div class="oc-pagination"><div id="insurancePaginationInfo" class="text-muted"></div><div id="insurancePaginationButtons" class="d-flex" style="gap:6px"></div></div>
</div>
