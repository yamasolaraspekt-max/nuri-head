{{-- resources/views/admin/settings/costing_sets/partials/_roles_matrix.blade.php --}}
@php
  // show defaults badge line (optional)
  $defNk = (float)($set->default_payroll_overhead_percent ?? 0);
  $defGk = (float)($set->default_company_overhead_percent ?? 0);
  $defMk = (float)($set->default_sell_markup_percent ?? 0);
@endphp

<style>
  /* Scoped helpers for matrix */
  .rm-headline{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:8px 10px;
    border:1px solid rgba(15,23,42,.08);
    border-radius:12px;
    background:rgba(248,250,252,.8);
    margin-bottom:10px;
    flex-wrap:wrap;
  }
  .rm-pill{
    border-radius:999px;
    padding:6px 10px;
    font-weight:1100;
    font-size:12px;
    border:1px solid rgba(15,23,42,.10);
    background:rgba(15,23,42,.04);
    color:var(--ink);
    display:inline-flex; gap:8px; align-items:center;
  }
  .rm-note{ color:var(--muted); font-weight:900; font-size:12px; }

  .rm-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
  }
  .rm-table thead th{
    position:sticky;
    top:0;
    z-index:2;
    background:rgba(192,216,234,.35);
    border-bottom:1px solid rgba(15,23,42,.10);
    font-weight:1100;
    white-space:nowrap;
  }
  .rm-table th, .rm-table td{
    padding:10px 10px;
    border-bottom:1px solid rgba(15,23,42,.06);
    vertical-align:middle;
    white-space:nowrap;
  }
  .rm-table tbody tr:hover td{ background:rgba(248,250,252,.9); }
  .rm-table td:first-child{
    white-space:normal;
    min-width:220px;
    font-weight:1100;
    color:var(--ink);
  }

  .rm-inp{
    width:130px;
    border-radius:10px;
    border:1px solid rgba(15,23,42,.12);
    padding:8px 10px;
    font-weight:1100;
    background:#fff;
  }
  .rm-inp[readonly]{ background:rgba(15,23,42,.04); }

  @media(max-width: 700px){
    .rm-inp{ width:120px; }
    .rm-table td:first-child{ min-width:180px; }
  }
</style>

<div class="rm-headline">
  <div class="rm-pill">
    Defaults:
    <span>NK {{ number_format($defNk,2,',','.') }}%</span> •
    <span>GK {{ number_format($defGk,2,',','.') }}%</span> •
    <span>Markup {{ number_format($defMk,2,',','.') }}%</span>
  </div>
  <div class="rm-note">
    Hinweis: Vollkost & VK werden beim Tippen automatisch berechnet.
  </div>
</div>

<div class="table-responsive" style="overflow:auto;">
  <table class="rm-table table mb-0 table-hover">
    <thead>
      <tr>
        <th>Qualifikation</th>
        <th style="width:160px;">Lohn €/h</th>
        <th style="width:160px;">Lohn-NK %</th>
        <th style="width:160px;">GK %</th>
        <th style="width:180px;">Vollkost €/h</th>
        <th style="width:180px;">VK €/h</th>
      </tr>
    </thead>
    <tbody>
      @forelse($roles as $r)
        <tr class="rm-row" data-role-row="{{ $r->id }}">
          <td>{{ $r->qualification?->name ?? 'Qualifikation' }}</td>

          <td>
            <input class="rm-inp"
              data-field="wage_cost_per_hour"
              value="{{ number_format((float)$r->wage_cost_per_hour,2,'.','') }}">
          </td>

          <td>
            <input class="rm-inp"
              data-field="payroll_overhead_percent"
              value="{{ number_format((float)$r->payroll_overhead_percent,2,'.','') }}">
          </td>

          <td>
            <input class="rm-inp"
              data-field="company_overhead_percent"
              value="{{ number_format((float)$r->company_overhead_percent,2,'.','') }}">
          </td>

          <td>
            <input class="rm-inp"
              data-field="full_cost_rate_per_hour"
              value="{{ number_format((float)$r->full_cost_rate_per_hour,2,'.','') }}">
          </td>

          <td>
            <input class="rm-inp"
              data-field="sell_rate_per_hour"
              value="{{ number_format((float)$r->sell_rate_per_hour,2,'.','') }}">
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="color:var(--muted); padding:16px; font-weight:900;">
            Keine Rollen gefunden. Klicke „Sync Rollen“.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>