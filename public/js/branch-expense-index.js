(function () {
  const cfg = window.branchExpenseIndexConfig || {};
  let state = { page: 1, items: [], pagination: {} };
  let deleteAction = null;
  let timer = null;

  function csrf() { return $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val(); }
  function tpl(t, id) { return String(t).replace('__ID__', id); }
  function esc(v) { return String(v ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c])); }
  function money(v) { return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(Number(v || 0)); }
  function icons() { if (window.lucide) lucide.createIcons(); }
  function toast(msg, bad = false) {
    const html = $(`<div class="oc-toast"><div class="oc-stat-icon ${bad ? 'bad' : 'good'}" style="width:34px;height:34px"><i data-lucide="${bad ? 'x-circle' : 'check-circle-2'}"></i></div><div><strong>${bad ? 'Fehler' : 'Erfolg'}</strong><div class="small text-muted">${esc(msg)}</div></div><button class="oc-btn-ic" style="margin-left:auto" type="button">×</button></div>`);
    html.find('button').on('click', () => html.remove());
    $('#toastWrap').append(html); icons();
    setTimeout(() => html.fadeOut(200, () => html.remove()), 4200);
  }
  function ok(m) { toast(m, false); }
  function bad(m) { toast(m, true); }
  function pill(status, label) {
    let c = 'gray';
    if (['active', 'closed'].includes(status)) c = 'green';
    if (['draft', 'open'].includes(status)) c = 'orange';
    if (['overdue', 'expired'].includes(status)) c = 'red';
    if (status === 'archived') c = 'purple';
    return `<span class="oc-pill ${c}">${esc(label || status || '-')}</span>`;
  }
  function filters() {
    return {
      page: state.page,
      per_page: $('#perPage').val(),
      search: $('#expenseSearch').val(),
      branch_id: $('#filterBranch').val(),
      year: $('#filterYear').val(),
      status: $('#filterStatus').val()
    };
  }
  function loadAll() { loadAnalytics(); loadExpenses(); }
  function loadAnalytics() {
    $.get(cfg.analyticsUrl, filters()).done(r => {
      Object.keys(r || {}).forEach(k => {
        const moneyKeys = ['total_cost', 'rent_total', 'insurance_total', 'other_total', 'employee_total', 'installment_total'];
        $(`[data-stat="${k}"]`).text(moneyKeys.includes(k) ? money(r[k]) : r[k]);
      });
    });
  }
  function loadExpenses() {
    $('#expenseLoader').addClass('show');
    $.get(cfg.dataUrl, filters())
      .done(r => { state.items = r.items || []; state.pagination = r.pagination || {}; renderExpenses(); renderPagination(); })
      .fail(x => bad(x.responseJSON?.message || 'Daten konnten nicht geladen werden.'))
      .always(() => $('#expenseLoader').removeClass('show'));
  }
  function row(x) {
    return `<div class="oc-item"><div class="oc-item-row expenses">
      <div><div class="oc-cell-title">ID</div><span class="oc-id-badge">#${x.id}</span></div>
      <div class="oc-main"><div class="oc-avatar">${esc((x.branch_initial || x.branch || '?').slice(0, 2).toUpperCase())}</div><div><div class="oc-ttl">${esc(x.branch || '-')}</div><div class="oc-subt">${esc(x.branch_street || '')} ${esc(x.branch_postcode || '')} ${esc(x.branch_city || '')}</div></div></div>
      <div><div class="oc-cell-title">Jahr</div><strong>${esc(x.year)}</strong><div class="small text-muted">${esc(x.period_start || '-')} - ${esc(x.period_end || '-')}</div></div>
      <div><div class="oc-cell-title">Kosten</div><strong>${money(x.total)}</strong><div class="small text-muted">Miete: ${money(x.rent_total)} · Vers.: ${money(x.insurance_total)}</div></div>
      <div><div class="oc-cell-title">Details</div><strong>${x.rents_count || 0}</strong> Miete<br><span class="small text-muted">${x.insurances_count || 0} Versicherung · ${x.other_costs_count || 0} Kosten</span></div>
      <div><div class="oc-cell-title">Status</div>${pill(x.status, x.status_label)}</div>
      <div class="oc-actions"><a class="oc-btn-ic success" href="${esc(x.profile_url)}" title="Profil öffnen"><i data-lucide="layout-dashboard"></i></a><button class="oc-btn-ic primary edit-expense" data-id="${x.id}" title="Bearbeiten"><i data-lucide="pencil"></i></button><button class="oc-btn-ic danger delete-expense" data-id="${x.id}" data-name="${esc(x.branch)} ${esc(x.year)}" title="Löschen"><i data-lucide="trash-2"></i></button></div>
    </div></div>`;
  }
  function renderExpenses() {
    $('#expenseList').html(state.items.map(row).join('') || '<div class="oc-empty">Keine Branch Expenses gefunden. Wählen Sie zuerst eine Branch und erstellen Sie ein Jahr.</div>');
    icons();
  }
  function renderPagination() {
    const p = state.pagination || {};
    $('#paginationInfo').text(p.total ? `${p.from || 0}-${p.to || 0} von ${p.total}` : 'Keine Ergebnisse');
    let html = '';
    for (let i = 1; i <= Number(p.last_page || 1); i++) html += `<button class="oc-btn-soft ${i === Number(p.current_page) ? 'active' : ''}" data-page="${i}" type="button">${i}</button>`;
    $('#paginationButtons').html(html);
  }
  function openExpenseModal(item = null) {
    $('#expenseForm')[0].reset(); $('#expense_id').val(''); $('#expenseModalTitle').text('Branch Expense hinzufügen');
    if (item) {
      $('#expenseModalTitle').text('Branch Expense bearbeiten'); $('#expense_id').val(item.id);
      ['branch_id', 'year', 'period_start', 'period_end', 'notes'].forEach(k => $('#' + k).val(item[k])); $('#expense_status').val(item.status);
    } else {
      if ($('#filterBranch').val()) $('#branch_id').val($('#filterBranch').val());
      if ($('#filterYear').val()) $('#year').val($('#filterYear').val());
    }
    $('#expenseModal').modal('show'); icons();
  }
  function saveExpense(e) {
    e.preventDefault();
    const id = $('#expense_id').val();
    const url = id ? cfg.updateUrl : cfg.storeUrl;
    $.post(url, $('#expenseForm').serialize()).done(r => { ok(r.message || 'Gespeichert'); $('#expenseModal').modal('hide'); loadAll(); }).fail(x => bad(x.responseJSON?.message || 'Speichern fehlgeschlagen.'));
  }
  function deleteConfirm(text, cb) { deleteAction = cb; $('#deleteText').text(text); $('#confirmDeleteModal').modal('show'); }

  $(document).on('click', '#openCreateExpenseBtn', () => openExpenseModal());
  $(document).on('click', '#refreshBtn', loadAll);
  $(document).on('click', '.edit-expense', function () { $.get(tpl(cfg.showUrlTemplate, $(this).data('id'))).done(r => openExpenseModal(r.item)); });
  $(document).on('click', '.delete-expense', function () { const id = $(this).data('id'); deleteConfirm(`Datensatz ${$(this).data('name')} löschen?`, () => $.get(tpl(cfg.destroyUrlTemplate, id)).done(r => { ok(r.message || 'Gelöscht'); loadAll(); })); });
  $('#expenseForm').on('submit', saveExpense);
  $('#paginationButtons').on('click', 'button', function () { state.page = $(this).data('page'); loadExpenses(); });
  $('#expenseSearch,#filterBranch,#filterYear,#filterStatus,#perPage').on('input change', function () { clearTimeout(timer); timer = setTimeout(() => { state.page = 1; loadAll(); }, 250); });
  $('#confirmDeleteBtn').on('click', function () { if (deleteAction) deleteAction(); $('#confirmDeleteModal').modal('hide'); deleteAction = null; });

  loadAll(); icons();
})();
