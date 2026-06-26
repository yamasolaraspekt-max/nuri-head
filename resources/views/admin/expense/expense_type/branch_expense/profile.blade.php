@extends('admin.layouts.app')
@section('title', 'Branch Expense Profil')

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
          <div class="oc-title">{{ strtoupper($expense->branch?->branch ?? 'BRANCH') }} · {{ $expense->year }}</div>
          <div class="oc-sub">Branch Expense Profil mit eigener Navigation und separatem Content-Bereich.</div>
          <div class="oc-breadcrumb">
            <a href="{{ url('/employee_dashboard') }}">Home</a><span>›</span>
            <a href="{{ route('branch.expense') }}">Branch Expense</a><span>›</span>
            <span class="current">Profil</span>
          </div>
        </div>
        <div class="d-flex flex-wrap" style="gap:8px">
          <a class="oc-btn-soft" href="{{ route('branch.expense') }}"><i data-lucide="arrow-left"></i> Zurück</a>
          <button type="button" class="oc-btn-soft" id="refreshProfileBtn"><i data-lucide="refresh-cw"></i> Aktualisieren</button>
        </div>
      </div>

      <div class="oc-profile-grid">
        <aside class="oc-side">
          <div class="oc-side-head">
            <div class="oc-main">
              <div class="oc-avatar">{{ strtoupper(mb_substr($expense->branch?->branch ?? 'B', 0, 2)) }}</div>
              <div>
                <div class="oc-side-title">{{ $expense->branch?->branch ?? '-' }}</div>
                <div class="oc-side-sub">Jahr {{ $expense->year }} · {{ $expense->status_label }}</div>
              </div>
            </div>
          </div>
          <nav class="oc-nav">
            @foreach($tabs as $key => $tab)
              <a class="oc-nav-link {{ $activeTab === $key ? 'active' : '' }}" href="{{ $tab['url'] }}">
                <i data-lucide="{{ $tab['icon'] }}"></i>
                <span>{{ $tab['label'] }}</span>
              </a>
            @endforeach
          </nav>
        </aside>

        <main class="oc-content">
          <div class="oc-analytics" id="profileKpis">
            <div class="oc-stat"><div class="oc-stat-icon total"><i data-lucide="euro"></i></div><div><div class="oc-stat-label">Total</div><div class="oc-stat-value" data-kpi="total">{{ number_format($kpis['total'] ?? 0, 2, ',', '.') }} €</div></div></div>
            <div class="oc-stat"><div class="oc-stat-icon warn"><i data-lucide="home"></i></div><div><div class="oc-stat-label">Miete</div><div class="oc-stat-value" data-kpi="rent_total">{{ number_format($kpis['rent_total'] ?? 0, 2, ',', '.') }} €</div></div></div>
            <div class="oc-stat"><div class="oc-stat-icon total"><i data-lucide="shield-check"></i></div><div><div class="oc-stat-label">Versicherung</div><div class="oc-stat-value" data-kpi="insurance_total">{{ number_format($kpis['insurance_total'] ?? 0, 2, ',', '.') }} €</div></div></div>
            <div class="oc-stat"><div class="oc-stat-icon bad"><i data-lucide="triangle-alert"></i></div><div><div class="oc-stat-label">Überfällig</div><div class="oc-stat-value" data-kpi="overdue">{{ $kpis['overdue'] ?? 0 }}</div></div></div>
          </div>

          @include('admin.expense.expense_type.branch_expense.profile.' . $activeTab)
        </main>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Datensatz löschen</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body"><p id="deleteText">Möchten Sie diesen Datensatz wirklich löschen?</p></div><div class="modal-footer"><button class="oc-btn-soft" data-dismiss="modal" type="button">Abbrechen</button><button class="oc-btn" id="confirmDeleteBtn" type="button" style="background:#ef4444">Ja, löschen</button></div></div></div></div>
<div class="oc-toast-wrap" id="toastWrap"></div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
window.branchExpenseProfileConfig = {
  expenseId: @json($expense->id),
  activeTab: @json($activeTab),
  profileDataUrl: @json(route('branch.expense.profile.data', $expense)),
  rentDataUrl: @json(route('branch.expense.rents.data', $expense)),
  rentStoreUrl: @json(route('branch.expense.rents.store', $expense)),
  rentShowUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/rents/__ID__/show')),
  rentUpdateUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/rents/__ID__/update')),
  rentDestroyUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/rents/__ID__/delete')),
  insuranceDataUrl: @json(route('branch.expense.insurances.data', $expense)),
  insuranceStoreUrl: @json(route('branch.expense.insurances.store', $expense)),
  insuranceShowUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/insurances/__ID__/show')),
  insuranceUpdateUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/insurances/__ID__/update')),
  insuranceDestroyUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/insurances/__ID__/delete')),
  otherDataUrl: @json(route('branch.expense.other.data', $expense)),
  otherStoreUrl: @json(route('branch.expense.other.store', $expense)),
  otherShowUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/other-costs/__ID__/show')),
  otherUpdateUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/other-costs/__ID__/update')),
  otherDestroyUrlTemplate: @json(url('/branch_expense/'.$expense->id.'/other-costs/__ID__/delete')),
};
</script>
<script src="{{ asset('js/branch-expense-profile.js') }}"></script>
@endsection
