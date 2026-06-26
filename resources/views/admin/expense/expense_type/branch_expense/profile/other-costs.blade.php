<div class="oc-card" style="padding:16px">
  <form id="otherForm" class="oc-form-section">@csrf<input type="hidden" name="id" id="other_id">
    <div class="oc-form-section-title"><span id="otherFormTitle">Sonstige Kosten hinzufügen</span><button class="oc-btn-soft" id="resetOtherBtn" type="button"><i data-lucide="plus"></i> Neu</button></div>
    <div class="oc-form-grid thirds">
      <div><label class="oc-label">Titel *</label><input class="oc-field" name="title" id="other_title" required></div>
      <div><label class="oc-label">Kategorie</label><select class="oc-field" name="category" id="other_category">@foreach($otherCategories as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
      <div><label class="oc-label">Betrag *</label><input type="number" step="0.01" class="oc-field" name="amount" id="other_amount" required></div>
      <div><label class="oc-label">Lieferant</label><input class="oc-field" name="vendor" id="other_vendor"></div>
      <div><label class="oc-label">Rechnung Nr.</label><input class="oc-field" name="invoice_no" id="other_invoice_no"></div>
      <div><label class="oc-label">Status</label><select class="oc-field" name="status" id="other_status">@foreach($otherCostStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
      <div><label class="oc-label">Zahlungsdatum</label><input type="date" class="oc-field" name="payment_date" id="other_payment_date"></div>
      <div><label class="oc-label">Fällig am</label><input type="date" class="oc-field" name="due_date" id="other_due_date"></div>
      <div><label class="oc-label">Zahlungszyklus</label><input class="oc-field" name="payment_cycle" id="other_payment_cycle" placeholder="Monatlich, jährlich..."></div>
      <div class="oc-span-3"><label class="oc-label">Notiz</label><textarea class="oc-field" name="notes" id="other_notes" rows="2"></textarea></div>
    </div>
    <div class="mt-1"><button class="oc-btn" type="submit"><i data-lucide="save"></i> Kosten speichern</button></div>
  </form>

  <div class="oc-toolbar"><div class="oc-filter-block search"><label class="oc-filter-label">Kosten suchen</label><input class="oc-input search" id="otherSearch"></div></div>
  <div id="otherList" class="oc-mini-list"></div>
  <div class="oc-pagination"><div id="otherPaginationInfo" class="text-muted"></div><div id="otherPaginationButtons" class="d-flex" style="gap:6px"></div></div>
</div>
