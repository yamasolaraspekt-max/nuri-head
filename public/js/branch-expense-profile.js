(function () {
  const cfg = window.branchExpenseProfileConfig || {};
  let deleteAction = null;
  let timer = null;
  let pages = { rent: 1, insurance: 1, other: 1 };

  function tpl(t, id) { return String(t).replace('__ID__', id); }
  function esc(v) { return String(v ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c])); }
  function money(v) { return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(Number(v || 0)); }
  function icons() { if (window.lucide) lucide.createIcons(); }
  function toast(msg, bad = false) {
    const html = $(`<div class="oc-toast"><div class="oc-stat-icon ${bad ? 'bad' : 'good'}" style="width:34px;height:34px"><i data-lucide="${bad ? 'x-circle' : 'check-circle-2'}"></i></div><div><strong>${bad ? 'Fehler' : 'Erfolg'}</strong><div class="small text-muted">${esc(msg)}</div></div><button class="oc-btn-ic" style="margin-left:auto" type="button">×</button></div>`);
    html.find('button').on('click', () => html.remove()); $('#toastWrap').append(html); icons(); setTimeout(() => html.fadeOut(200, () => html.remove()), 4200);
  }
  function ok(m) { toast(m, false); }
  function bad(m) { toast(m, true); }
  function pill(status, label) {
    let c = 'gray'; if (['active', 'paid', 'closed'].includes(status)) c = 'green'; if (['draft', 'due', 'open', 'paused'].includes(status)) c = 'orange'; if (['overdue', 'expired', 'cancelled', 'terminated'].includes(status)) c = 'red'; if (status === 'archived') c = 'purple';
    return `<span class="oc-pill ${c} small">${esc(label || status || '-')}</span>`;
  }
  function subPagination(info, buttons, p, cb) {
    $(info).text(p.total ? `${p.from || 0}-${p.to || 0} von ${p.total}` : 'Keine Ergebnisse');
    let h = ''; for (let i = 1; i <= Number(p.last_page || 1); i++) h += `<button type="button" class="oc-btn-soft ${i === Number(p.current_page) ? 'active' : ''}" data-p="${i}">${i}</button>`;
    $(buttons).html(h).find('button').on('click', function () { cb($(this).data('p')); });
  }
  function actions(type, id) { return `<div class="oc-actions justify-content-start mt-1"><button class="oc-btn-ic primary edit-${type}" data-id="${id}"><i data-lucide="pencil"></i></button><button class="oc-btn-ic danger delete-${type}" data-id="${id}"><i data-lucide="trash-2"></i></button></div>`; }
  function refreshProfile() {
    $.get(cfg.profileDataUrl).done(r => {
      const moneyKeys = ['total', 'rent_total', 'insurance_total', 'other_total', 'employee_total', 'installment_total'];
      Object.keys(r.kpis || {}).forEach(k => $(`[data-kpi="${k}"]`).text(moneyKeys.includes(k) ? money(r.kpis[k]) : r.kpis[k]));
      if ($('#alertList').length) renderAlerts(r.alerts || []);
      if ($('#employeeList').length) renderLinked(r.linked || {});
    });
  }
  function renderAlerts(items) {
    $('#alertList').html(items.map(a => `<div class="oc-alert-item ${a.status === 'overdue' ? 'overdue' : ''}"><strong>${esc(a.title)}</strong><div class="small text-muted">${esc(a.date)} · ${money(a.amount)}</div></div>`).join('') || '<div class="text-muted">Keine Fälligkeiten in den nächsten 30 Tagen.</div>');
  }
  function renderLinked(linked) {
    $('#employeeList').html((linked.employees || []).map(x => `<div class="oc-mini-item"><strong>${esc(x.name)} ${esc(x.lastname)}</strong><div class="small text-muted">${esc(x.department_name)} · ${esc(x.position)}</div><div>${money(x.salary)}</div></div>`).join('') || '<div class="text-muted">Keine Mitarbeiterkosten gefunden.</div>');
    $('#installmentList').html((linked.installments || []).map(x => `<div class="oc-mini-item"><strong>${esc(x.installment_id || '#' + x.id)}</strong><div class="small text-muted">${esc(x.type || '-')} · ${esc(x.due_date || '-')}</div><div>${money(x.total)}</div></div>`).join('') || '<div class="text-muted">Keine Raten gefunden.</div>');
    const assets = (linked.assets || []).map(x => `<div class="oc-mini-item"><strong>Asset: ${esc(x.item || '#' + x.id)}</strong><div>${money(x.amount)}</div></div>`).join('');
    const machines = (linked.machines || []).map(x => `<div class="oc-mini-item"><strong>Maschine: ${esc(x.name)} ${esc(x.model || '')}</strong><div>${money(x.amount)}</div></div>`).join('');
    $('#assetMachineList').html(assets + machines || '<div class="text-muted">Keine Assets/Maschinen gefunden.</div>');
  }

  function loadRents() {
    if (!$('#rentList').length) return;
    $.get(cfg.rentDataUrl, { page: pages.rent, search: $('#rentSearch').val() }).done(r => {
      $('#rentList').html((r.items || []).map(x => `<div class="oc-mini-item"><strong>${esc(x.object_name)}</strong> ${pill(x.status, x.status_label)}<div class="small text-muted">${esc(x.street || '')} ${esc(x.house_no || '')} ${esc(x.postcode || '')} ${esc(x.city || '')}</div><div>Miete: ${money(x.rent_cost)} · NK: ${money(x.extra_cost)} · Total: <strong>${money(x.total)}</strong></div><div class="small text-muted">Fällig: ${esc(x.next_due_date || '-')}</div>${actions('rent', x.id)}</div>`).join('') || '<div class="text-muted">Keine Mieten gefunden.</div>');
      subPagination('#rentPaginationInfo', '#rentPaginationButtons', r.pagination || {}, p => { pages.rent = p; loadRents(); }); icons();
    });
  }
  function resetRent() { if (!$('#rentForm').length) return; $('#rentForm')[0].reset(); $('#rent_id').val(''); $('#rentFormTitle').text('Miete / Objekt hinzufügen'); }
  function saveRent(e) { e.preventDefault(); const id = $('#rent_id').val(); $.post(id ? tpl(cfg.rentUpdateUrlTemplate, id) : cfg.rentStoreUrl, $('#rentForm').serialize()).done(r => { ok(r.message || 'Gespeichert'); resetRent(); loadRents(); refreshProfile(); }).fail(x => bad(x.responseJSON?.message || 'Speichern fehlgeschlagen.')); }
  function editRent(id) { $.get(tpl(cfg.rentShowUrlTemplate, id)).done(r => { const x = r.item || {}; resetRent(); $('#rent_id').val(x.id); $('#rentFormTitle').text('Miete bearbeiten'); $('#rent_object_name').val(x.object_name); $('#rent_object_type').val(x.object_type); $('#rent_cost').val(x.rent_cost); $('#rent_extra_cost').val(x.extra_cost); $('#rent_total').val(x.total); $('#rent_city').val(x.city); $('#rent_street').val(x.street); $('#rent_house_no').val(x.house_no); $('#rent_postcode').val(x.postcode); $('#landlord_name').val(x.landlord_name); $('#landlord_contact').val(x.landlord_contact); $('#contract_start').val(x.contract_start); $('#contract_end').val(x.contract_end); $('#payment_cycle').val(x.payment_cycle); $('#due_day').val(x.due_day); $('#rent_next_due_date').val(x.next_due_date); $('#rent_status').val(x.status); $('#rent_notes').val(x.notes); }); }

  function loadInsurances() {
    if (!$('#insuranceList').length) return;
    $.get(cfg.insuranceDataUrl, { page: pages.insurance, search: $('#insuranceSearch').val() }).done(r => {
      $('#insuranceList').html((r.items || []).map(x => `<div class="oc-mini-item"><strong>${esc(x.insurance_for)}</strong> ${pill(x.status, x.status_label)}<div class="small text-muted">${esc(x.provider || '-')} · Police: ${esc(x.policy_number || '-')}</div><div>Monatlich: <strong>${money(x.monthly_payable)}</strong> · Deckung: ${money(x.coverage_amount)}</div><div class="small text-muted">Fällig: ${esc(x.next_due_date || '-')} · Ende: ${esc(x.end_date || '-')}</div>${actions('insurance', x.id)}</div>`).join('') || '<div class="text-muted">Keine Versicherungen gefunden.</div>');
      subPagination('#insurancePaginationInfo', '#insurancePaginationButtons', r.pagination || {}, p => { pages.insurance = p; loadInsurances(); }); icons();
    });
  }
  function resetInsurance() { if (!$('#insuranceForm').length) return; $('#insuranceForm')[0].reset(); $('#insurance_id').val(''); $('#insuranceFormTitle').text('Versicherung hinzufügen'); }
  function saveInsurance(e) { e.preventDefault(); const id = $('#insurance_id').val(); $.post(id ? tpl(cfg.insuranceUpdateUrlTemplate, id) : cfg.insuranceStoreUrl, $('#insuranceForm').serialize()).done(r => { ok(r.message || 'Gespeichert'); resetInsurance(); loadInsurances(); refreshProfile(); }).fail(x => bad(x.responseJSON?.message || 'Speichern fehlgeschlagen.')); }
  function editInsurance(id) { $.get(tpl(cfg.insuranceShowUrlTemplate, id)).done(r => { const x = r.item || {}; resetInsurance(); $('#insurance_id').val(x.id); $('#insuranceFormTitle').text('Versicherung bearbeiten'); $('#insurance_for').val(x.insurance_for); $('#provider').val(x.provider); $('#policy_number').val(x.policy_number); $('#coverage_amount').val(x.coverage_amount); $('#monthly_payable').val(x.monthly_payable); $('#insurance_status').val(x.status); $('#insurance_start_date').val(x.start_date); $('#insurance_end_date').val(x.end_date); $('#insurance_payment_cycle').val(x.payment_cycle); $('#insurance_due_day').val(x.due_day); $('#insurance_next_due_date').val(x.next_due_date); $('#insurance_payment_date').val(x.payment_date); $('#insurance_notes').val(x.notes); }); }

  function loadOthers() {
    if (!$('#otherList').length) return;
    $.get(cfg.otherDataUrl, { page: pages.other, search: $('#otherSearch').val() }).done(r => {
      $('#otherList').html((r.items || []).map(x => `<div class="oc-mini-item"><strong>${esc(x.title)}</strong> ${pill(x.status, x.status_label)}<div class="small text-muted">${esc(x.category_label || '-')} · ${esc(x.vendor || '-')} · ${esc(x.invoice_no || '-')}</div><div>Betrag: <strong>${money(x.amount)}</strong></div><div class="small text-muted">Fällig: ${esc(x.due_date || '-')} · Bezahlt: ${esc(x.payment_date || '-')}</div>${actions('other', x.id)}</div>`).join('') || '<div class="text-muted">Keine sonstigen Kosten gefunden.</div>');
      subPagination('#otherPaginationInfo', '#otherPaginationButtons', r.pagination || {}, p => { pages.other = p; loadOthers(); }); icons();
    });
  }
  function resetOther() { if (!$('#otherForm').length) return; $('#otherForm')[0].reset(); $('#other_id').val(''); $('#otherFormTitle').text('Sonstige Kosten hinzufügen'); }
  function saveOther(e) { e.preventDefault(); const id = $('#other_id').val(); $.post(id ? tpl(cfg.otherUpdateUrlTemplate, id) : cfg.otherStoreUrl, $('#otherForm').serialize()).done(r => { ok(r.message || 'Gespeichert'); resetOther(); loadOthers(); refreshProfile(); }).fail(x => bad(x.responseJSON?.message || 'Speichern fehlgeschlagen.')); }
  function editOther(id) { $.get(tpl(cfg.otherShowUrlTemplate, id)).done(r => { const x = r.item || {}; resetOther(); $('#other_id').val(x.id); $('#otherFormTitle').text('Kosten bearbeiten'); $('#other_title').val(x.title); $('#other_category').val(x.category); $('#other_amount').val(x.amount); $('#other_vendor').val(x.vendor); $('#other_invoice_no').val(x.invoice_no); $('#other_status').val(x.status); $('#other_payment_date').val(x.payment_date); $('#other_due_date').val(x.due_date); $('#other_payment_cycle').val(x.payment_cycle); $('#other_notes').val(x.notes); }); }

  function deleteConfirm(text, cb) { deleteAction = cb; $('#deleteText').text(text); $('#confirmDeleteModal').modal('show'); }

  $('#refreshProfileBtn').on('click', function () { refreshProfile(); loadRents(); loadInsurances(); loadOthers(); });
  $('.calc-rent').on('input', () => $('#rent_total').val((Number($('#rent_cost').val() || 0) + Number($('#rent_extra_cost').val() || 0)).toFixed(2)));
  $('#rentForm').on('submit', saveRent); $('#insuranceForm').on('submit', saveInsurance); $('#otherForm').on('submit', saveOther);
  $('#resetRentBtn').on('click', resetRent); $('#resetInsuranceBtn').on('click', resetInsurance); $('#resetOtherBtn').on('click', resetOther);
  $('#rentSearch').on('input', () => { clearTimeout(timer); timer = setTimeout(() => { pages.rent = 1; loadRents(); }, 250); });
  $('#insuranceSearch').on('input', () => { clearTimeout(timer); timer = setTimeout(() => { pages.insurance = 1; loadInsurances(); }, 250); });
  $('#otherSearch').on('input', () => { clearTimeout(timer); timer = setTimeout(() => { pages.other = 1; loadOthers(); }, 250); });
  $(document).on('click', '.edit-rent', function () { editRent($(this).data('id')); });
  $(document).on('click', '.edit-insurance', function () { editInsurance($(this).data('id')); });
  $(document).on('click', '.edit-other', function () { editOther($(this).data('id')); });
  $(document).on('click', '.delete-rent', function () { const id = $(this).data('id'); deleteConfirm('Miete löschen?', () => $.get(tpl(cfg.rentDestroyUrlTemplate, id)).done(r => { ok(r.message || 'Gelöscht'); loadRents(); refreshProfile(); })); });
  $(document).on('click', '.delete-insurance', function () { const id = $(this).data('id'); deleteConfirm('Versicherung löschen?', () => $.get(tpl(cfg.insuranceDestroyUrlTemplate, id)).done(r => { ok(r.message || 'Gelöscht'); loadInsurances(); refreshProfile(); })); });
  $(document).on('click', '.delete-other', function () { const id = $(this).data('id'); deleteConfirm('Kosten löschen?', () => $.get(tpl(cfg.otherDestroyUrlTemplate, id)).done(r => { ok(r.message || 'Gelöscht'); loadOthers(); refreshProfile(); })); });
  $('#confirmDeleteBtn').on('click', function () { if (deleteAction) deleteAction(); $('#confirmDeleteModal').modal('hide'); deleteAction = null; });

  loadRents(); loadInsurances(); loadOthers(); icons();
})();
