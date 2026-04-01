{{-- resources/views/admin/settings/costing_sets/partials/_sets_table.blade.php --}}
@php
  // Helper for safe number display (DE)
  $nf = fn($n) => number_format((float)$n, 2, ',', '.');
@endphp

<style>
  /* Scoped to this partial only */
  .cs-list{ display:flex; flex-direction:column; gap:10px; }
  .cs-card{
    border:1px solid rgba(15,23,42,.10);
    border-radius:16px;
    background:#fff;
    box-shadow:0 6px 18px rgba(15,23,42,.06);
    overflow:hidden;
  }
  .cs-card-head{
    padding:10px 12px;
    display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
    background:linear-gradient(180deg, rgba(116,178,212,.12), rgba(255,255,255,1));
    border-bottom:1px solid rgba(15,23,42,.08);
  }
  .cs-title{ font-weight:1100; color:var(--ink); line-height:1.15; }
  .cs-sub{
    margin-top:4px;
    color:var(--muted);
    font-weight:900;
    font-size:12px;
  }
  .cs-badges{ display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
  .cs-badge{
    border-radius:999px;
    padding:5px 10px;
    font-weight:1100;
    font-size:11px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(15,23,42,.04);
    color:var(--ink);
    display:inline-flex; align-items:center; gap:6px;
    white-space:nowrap;
  }
  .cs-badge.def{ background:rgba(147,194,28,.18); border-color:rgba(147,194,28,.25); color:#365314; }
  .cs-badge.off{ background:rgba(100,116,139,.12); border-color:rgba(100,116,139,.20); color:#334155; }

  .cs-card-body{ padding:10px 12px; }
  .cs-kv{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:8px;
  }
  .cs-kv .kv{
    border:1px solid rgba(15,23,42,.08);
    border-radius:12px;
    padding:8px 10px;
    background:rgba(248,250,252,.8);
  }
  .cs-kv .k{ color:var(--muted); font-weight:1000; font-size:11px; }
  .cs-kv .v{ color:var(--ink); font-weight:1100; font-size:12px; margin-top:2px; }

  .cs-actions{
    padding:10px 12px;
    border-top:1px solid rgba(15,23,42,.08);
    display:flex; gap:8px; flex-wrap:wrap;
    justify-content:space-between;
    background:rgba(248,250,252,.65);
  }
  .cs-actions .left, .cs-actions .right{ display:flex; gap:8px; flex-wrap:wrap; }
  .cs-btn{
    border-radius:12px;
    padding:8px 10px;
    font-weight:1100;
    border:1px solid rgba(15,23,42,.10);
    background:#fff;
    cursor:pointer;
    user-select:none;
  }
  .cs-btn.primary{ background:rgba(147,194,28,.20); border-color:rgba(147,194,28,.30); }
  .cs-btn.blue{ background:rgba(116,178,212,.16); }
  .cs-btn.danger{ background:rgba(239,68,68,.14); border-color:rgba(239,68,68,.25); }
  .cs-btn:active{ transform:translateY(1px); }

  .cs-mini{
    border-radius:12px;
    width:34px; height:34px;
    display:inline-flex; align-items:center; justify-content:center;
    border:1px solid rgba(15,23,42,.10);
    background:rgba(116,178,212,.14);
    font-weight:1100;
    cursor:pointer;
  }
</style>

<div class="cs-list">
  @forelse($data as $cs)
    @php
      // IMPORTANT: JSON must be valid for JSON.parse(). Use @json + single quotes in attribute.
      $row = [
        'id' => (int)$cs->id,
        'name' => (string)$cs->name,
        'aw_minutes' => (float)$cs->aw_minutes,

        'material_overhead_percent' => (float)$cs->material_overhead_percent,
        'labor_overhead_percent'    => (float)$cs->labor_overhead_percent,

        // store both, UI will read site_* first; controller may still use project_*
        'site_overhead_percent'     => (float)($cs->site_overhead_percent ?? $cs->project_overhead_percent ?? 0),
        'site_overhead_fixed'       => (float)($cs->site_overhead_fixed   ?? $cs->project_overhead_fixed   ?? 0),

        'project_overhead_percent'  => (float)($cs->project_overhead_percent ?? 0),
        'project_overhead_fixed'    => (float)($cs->project_overhead_fixed   ?? 0),

        'risk_percent'   => (float)$cs->risk_percent,
        'profit_percent' => (float)$cs->profit_percent,

        'small_parts_percent' => (float)($cs->small_parts_percent ?? 0),
        'freight_fixed'       => (float)($cs->freight_fixed ?? 0),
        'handling_fixed'      => (float)($cs->handling_fixed ?? 0),
        'disposal_fixed'      => (float)($cs->disposal_fixed ?? 0),

        'commission_mode'  => (string)$cs->commission_mode,
        'commission_value' => (float)$cs->commission_value,

        'rounding_mode' => (string)($cs->rounding_mode ?? 'none'),

        'default_payroll_overhead_percent' => (float)($cs->default_payroll_overhead_percent ?? 0),
        'default_company_overhead_percent' => (float)($cs->default_company_overhead_percent ?? 0),
        'default_sell_markup_percent'      => (float)($cs->default_sell_markup_percent ?? 0),

        'is_active'  => (int)$cs->is_active,
        'is_default' => (int)$cs->is_default,
      ];
    @endphp

    <div class="cs-card">
      <div class="cs-card-head">
        <div style="min-width:0;">
          <div class="cs-title">{{ $cs->name }}</div>
          <div class="cs-sub">
            AW {{ (float)$cs->aw_minutes }} • Rundung {{ $cs->rounding_mode ?? 'none' }}
          </div>
        </div>

        <div class="cs-badges">
          @if($cs->is_default)
            <span class="cs-badge def">Default</span>
          @endif
          @if($cs->is_active)
            <span class="cs-badge">Aktiv</span>
          @else
            <span class="cs-badge off">Inaktiv</span>
          @endif
        </div>
      </div>

      <div class="cs-card-body">
        <div class="cs-kv">
          <div class="kv">
            <div class="k">GK</div>
            <div class="v">
              Mat {{ $nf($cs->material_overhead_percent) }}% • Lohn {{ $nf($cs->labor_overhead_percent) }}%
            </div>
          </div>
          <div class="kv">
            <div class="k">Risiko / Gewinn</div>
            <div class="v">
              {{ $nf($cs->risk_percent) }}% • {{ $nf($cs->profit_percent) }}%
            </div>
          </div>

          <div class="kv">
            <div class="k">Baustelle</div>
            <div class="v">
              {{ $nf($cs->site_overhead_percent ?? $cs->project_overhead_percent) }}% + {{ $nf($cs->site_overhead_fixed ?? $cs->project_overhead_fixed) }} €
            </div>
          </div>
          <div class="kv">
            <div class="k">Defaults (Matrix)</div>
            <div class="v">
              NK {{ $nf($cs->default_payroll_overhead_percent) }}% •
              GK {{ $nf($cs->default_company_overhead_percent) }}% •
              Markup {{ $nf($cs->default_sell_markup_percent) }}%
            </div>
          </div>
        </div>
      </div>

      <div class="cs-actions">
        <div class="left">
          <button class="cs-btn blue"
            data-cs-action="select"
            data-id="{{ $cs->id }}">Auswählen</button>

          <button class="cs-mini"
            title="Details"
            type="button"
            data-cs-info='@json($row)'>i</button>
        </div>

        <div class="right">
          <button class="cs-btn"
            data-cs-action="edit"
            data-id="{{ $cs->id }}"
            data-json='@json($row)'>Bearbeiten</button>

          <button class="cs-btn primary"
            data-cs-action="make-default"
            data-id="{{ $cs->id }}">Default</button>

          <button class="cs-btn danger"
            data-cs-action="delete"
            data-id="{{ $cs->id }}">Löschen</button>
        </div>
      </div>
    </div>
  @empty
    <div style="color:var(--muted); font-weight:900; padding:12px;">
      Keine Costing Sets gefunden.
    </div>
  @endforelse

  <div class="mt-2">
    {!! $data->links() !!}
  </div>
</div>