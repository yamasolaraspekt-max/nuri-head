@extends('admin.layouts.app')
@section('title', 'Branch Expense Management')

@php
  $years = range(now()->year + 2, now()->year - 6);
@endphp

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/branch-expense-profile.css') }}">
@endsection

@section('content')
<div class="app-content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="oc-wrap">
      <div class="oc-titlebar">
        <div>
          <div class="oc-title">BRANCH & HAUSKOSTEN</div>
          <div class="oc-sub">Wählen Sie zuerst Branch und Jahr. Details wie Miete, Versicherung und andere Kosten öffnen danach im separaten Profil.</div>
          <div class="oc-breadcrumb">
            <a href="{{ url('/employee_dashboard') }}">Home</a><span>›</span><span class="current">Branch Expense</span>
          </div>
        </div>
        <div class="d-flex flex-wrap" style="gap:8px">
          <button type="button" class="oc-btn-soft" id="refreshBtn"><i data-lucide="refresh-cw"></i> Aktualisieren</button>
          <button type="button" class="oc-btn" id="openCreateExpenseBtn"><i data-lucide="plus"></i> Branch Expense hinzufügen</button>
        </div>
      </div>

      <div class="oc-analytics" id="expenseAnalytics">
        <div class="oc-stat"><div class="oc-stat-icon total"><i data-lucide="building-2"></i></div><div><div class="oc-stat-label">Datensätze</div><div class="oc-stat-value" data-stat="total_records">0</div><div class="oc-stat-sub">Branch/Jahr</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon good"><i data-lucide="euro"></i></div><div><div class="oc-stat-label">Gesamt</div><div class="oc-stat-value" data-stat="total_cost">0 €</div><div class="oc-stat-sub">Alle Kosten</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon warn"><i data-lucide="home"></i></div><div><div class="oc-stat-label">Miete</div><div class="oc-stat-value" data-stat="rent_total">0 €</div><div class="oc-stat-sub">Objekte/Haus</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon total"><i data-lucide="shield-check"></i></div><div><div class="oc-stat-label">Versicherung</div><div class="oc-stat-value" data-stat="insurance_total">0 €</div><div class="oc-stat-sub">Monatlich</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon gray"><i data-lucide="users"></i></div><div><div class="oc-stat-label">Personal</div><div class="oc-stat-value" data-stat="employee_total">0 €</div><div class="oc-stat-sub">Gehälter</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon gray"><i data-lucide="receipt"></i></div><div><div class="oc-stat-label">Raten</div><div class="oc-stat-value" data-stat="installment_total">0 €</div><div class="oc-stat-sub">Assets/Maschinen</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon warn"><i data-lucide="calendar-clock"></i></div><div><div class="oc-stat-label">Fällig bald</div><div class="oc-stat-value" data-stat="due_soon">0</div><div class="oc-stat-sub">Nächste 30 Tage</div></div></div>
        <div class="oc-stat"><div class="oc-stat-icon bad"><i data-lucide="triangle-alert"></i></div><div><div class="oc-stat-label">Überfällig</div><div class="oc-stat-value" data-stat="overdue">0</div><div class="oc-stat-sub">Offene Termine</div></div></div>
      </div>

      <div class="oc-toolbar">
        <div class="oc-toolbar-left">
          <div class="oc-filter-block search"><label class="oc-filter-label">Suche</label><input type="search" class="oc-input search" id="expenseSearch" placeholder="Branch, Jahr, Status, Notizen..."></div>
          <div class="oc-filter-block"><label class="oc-filter-label">Branch zuerst wählen</label><select class="oc-input" id="filterBranch"><option value="">Alle Filialen</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string)$selectedBranchId === (string)$branch->id)>{{ $branch->branch }}</option>@endforeach</select></div>
          <div class="oc-filter-block"><label class="oc-filter-label">Jahr</label><select class="oc-input" id="filterYear"><option value="">Alle Jahre</option>@foreach($years as $year)<option value="{{ $year }}" @selected((string)$selectedYear === (string)$year)>{{ $year }}</option>@endforeach</select></div>
          <div class="oc-filter-block"><label class="oc-filter-label">Status</label><select class="oc-input" id="filterStatus"><option value="">Alle</option>@foreach($expenseStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
        </div>
        <div class="oc-toolbar-right">
          <div class="oc-filter-block" style="min-width:100px"><label class="oc-filter-label">Pro Seite</label><select class="oc-input" id="perPage"><option>12</option><option>24</option><option>48</option><option>100</option></select></div>
        </div>
      </div>

      <div class="oc-card">
        <div class="oc-loader" id="expenseLoader"><div class="spinner-border text-primary" role="status"></div></div>
        <div class="oc-list-head expenses"><div>ID</div><div>Branch</div><div>Jahr</div><div>Kosten</div><div>Details</div><div>Status</div><div class="text-right">Aktion</div></div>
        <div class="oc-list" id="expenseList"></div>
      </div>
      <div class="oc-pagination"><div id="paginationInfo" class="text-muted"></div><div id="paginationButtons" class="d-flex" style="gap:6px"></div></div>
    </div>
  </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document"><div class="modal-content">
    <form id="expenseForm">@csrf<input type="hidden" name="id" id="expense_id">
      <div class="modal-header"><h5 class="modal-title" id="expenseModalTitle">Branch Expense hinzufügen</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
      <div class="modal-body">
        <div class="oc-form-grid">
          <div><label class="oc-label">Branch *</label><select class="oc-field" name="branch_id" id="branch_id" required><option value="">Bitte wählen</option>@foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->branch }}</option>@endforeach</select></div>
          <div><label class="oc-label">Jahr *</label><input type="number" min="2000" max="{{ now()->year + 5 }}" class="oc-field" name="year" id="year" value="{{ now()->year }}" required></div>
          <div><label class="oc-label">Periode Start</label><input type="date" class="oc-field" name="period_start" id="period_start"></div>
          <div><label class="oc-label">Periode Ende</label><input type="date" class="oc-field" name="period_end" id="period_end"></div>
          <div><label class="oc-label">Status</label><select class="oc-field" name="status" id="expense_status">@foreach($expenseStatuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
          <div><label class="oc-label">Notiz</label><input class="oc-field" name="notes" id="notes"></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="oc-btn-soft" data-dismiss="modal">Schließen</button><button type="submit" class="oc-btn"><i data-lucide="save"></i> Speichern</button></div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Datensatz löschen</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body"><p id="deleteText">Möchten Sie diesen Datensatz wirklich löschen?</p></div><div class="modal-footer"><button class="oc-btn-soft" data-dismiss="modal" type="button">Abbrechen</button><button class="oc-btn" id="confirmDeleteBtn" type="button" style="background:#ef4444">Ja, löschen</button></div></div></div></div>
<div class="oc-toast-wrap" id="toastWrap"></div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
window.branchExpenseIndexConfig = {
  dataUrl: @json(route('branch.expense.data')),
  analyticsUrl: @json(route('branch.expense.analytics')),
  storeUrl: @json(route('branch.expense.store')),
  updateUrl: @json(route('branch.expense.update')),
  showUrlTemplate: @json(url('/branch_expense/__ID__/show')),
  destroyUrlTemplate: @json(url('/branch_expense_delete/__ID__')),
};
</script>
<script src="{{ asset('js/branch-expense-index.js') }}"></script>
@endsection
