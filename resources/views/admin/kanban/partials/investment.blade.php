@php
    $customers        = $customers        ?? [];
    $productColumns   = $productColumns   ?? collect();
    $overallMin       = $overallMin       ?? 0;
    $overallMax       = $overallMax       ?? 0;
    $overallCount     = $overallCount     ?? 0;
    $overallAvgMin    = $overallAvgMin    ?? 0;
    $overallAvgMax    = $overallAvgMax    ?? 0;
    $overallAvgTotal  = $overallAvgTotal  ?? 0;
    $overallRequest   = $overallRequest   ?? 0;

    // Ø Einzelanfragwert = Gesamt Ø / 2
    $overallSingleRequestAvg = $overallAvgTotal / 2;
@endphp

<style>
  /* ===== SUMMARY WRAPPER / CARDS ===== */

  .inv-summary-shell {
    border-radius: 18px;
    padding: 10px 12px;
    margin-bottom: 10px;
  }

  .inv-summary-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(203, 213, 225, 0.7);
  }

  .inv-summary-icon {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .inv-summary-icon svg {
    width: 18px;
    height: 18px;
  }

  .inv-summary-icon-count {
    background: rgba(52, 211, 153, 0.1);
    color: #059669;
  }

  .inv-summary-icon-min {
    background: rgba(248, 113, 113, 0.1);
    color: #dc2626;
  }

  .inv-summary-icon-max {
    background: rgba(52, 211, 153, 0.1);
    color: #16a34a;
  }

  .inv-summary-icon-request {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
  }

  .inv-summary-icon-avg-min {
    background: rgba(248, 113, 113, 0.1);
    color: #b91c1c;
  }

  .inv-summary-icon-avg-max {
    background: rgba(52, 211, 153, 0.1);
    color: #15803d;
  }

  .inv-summary-icon-avg-total {
    background: rgba(129, 140, 248, 0.12);
    color: #4f46e5;
  }

  .inv-summary-icon-avg-single {
    background: rgba(234, 179, 8, 0.12);
    color: #b45309;
  }

  .inv-summary-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }

  .inv-summary-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280;
  }

  .inv-summary-value {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    white-space: nowrap;
  }

  /* ===== PRODUKT-BADGES / LISTE ===== */

  .inv-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #f9fafb, #eef2f7);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(148, 163, 184, 0.35);
    gap: 8px;
    font-size: 11px;
    white-space: nowrap;
  }

  .inv-badge-part {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .inv-badge-part i.feather {
    width: 14px;
    height: 14px;
  }

  .inv-badge-max i.feather {
    color: #16a34a;
  }

  .inv-badge-min i.feather {
    color: #dc2626;
  }

  .inv-badge-avg i.feather {
    color: #b45309;
  }

  .inv-badge-divider {
    width: 1px;
    height: 18px;
    background: rgba(148, 163, 184, 0.5);
  }

  .inv-products-cell {
    padding-top: 6px;
    padding-bottom: 6px;
  }

  .inv-product-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 6px;
    padding: 5px 6px;
    border-radius: 10px;
    background: rgba(248, 250, 252, 0.8);
    border: 1px dashed rgba(148, 163, 184, 0.4);
  }

  .inv-product-row:last-child {
    margin-bottom: 0;
  }

  .inv-product-label {
    font-size: 12px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .inv-product-label::before {
    content: "";
    width: 16px;
    height: 16px;
    border-radius: 999px;
    background: radial-gradient(circle at 30% 30%, #93c21c, #93c21c);
    opacity: 0.9;
  }

  .inv-product-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    padding: 0 6px;
    border-radius: 999px;
    background: #e5f0c2;
    font-size: 11px;
    font-weight: 700;
    color: #3f6212;
  }

  /* Gesamt-Block rechts */

  .inv-total-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    padding: 6px 8px;
    border-radius: 12px;
    background: linear-gradient(135deg, #74b2d4, #4c82c2);
    color: #fff;
  }

  .inv-total-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .inv-total-row span {
    opacity: .9;
  }

  .inv-total-row strong {
    font-size: 12px;
  }

  /* Table sorting indicators */

  #investmentTable thead th[data-sort] {
    cursor: pointer;
    white-space: nowrap;
  }

  #investmentTable thead th[data-sort]::after {
    content: "";
    display: inline-block;
    margin-left: 4px;
    border-width: 4px 4px 0 4px;
    border-style: solid;
    border-color: transparent;
    opacity: 0.45;
  }

  #investmentTable thead th[data-sort].sorted-asc::after {
    border-width: 0 4px 4px 4px;
    border-color: transparent transparent #4b5563 transparent;
  }

  #investmentTable thead th[data-sort].sorted-desc::after {
    border-width: 4px 4px 0 4px;
    border-color: #4b5563 transparent transparent transparent;
  }

  @media (max-width: 768px) {
    .inv-total-wrap {
      align-items: stretch;
    }
  }
</style>

<div id="investmentInner" data-investment-total="{{ count($customers) }}">

  {{-- Top summary: Gesamtanfragen, Min, Max, Anfragewert gesamt --}}
  <div class="inv-summary-shell">
    <div class="row g-2">
      <div class="col-6 col-md-3">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="4" rx="1"></rect>
              <rect x="3" y="10" width="18" height="4" rx="1"></rect>
              <rect x="3" y="16" width="18" height="4" rx="1"></rect>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamtanfragen</span>
            <span class="inv-summary-value">{{ $overallCount }}</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-min">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3v14"></path>
              <path d="M6 13l6 6 6-6"></path>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamtwert Min</span>
            <span class="inv-summary-value">
              {{ number_format($overallMin, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-max">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 21V7"></path>
              <path d="M6 11l6-6 6 6"></path>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamtwert Max</span>
            <span class="inv-summary-value">
              {{ number_format($overallMax, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-request">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3v18"></path>
              <path d="M4 6h16"></path>
              <path d="M6 6l-3 5h6l-3-5z"></path>
              <path d="M18 6l-3 5h6l-3-5z"></path>
              <path d="M4 19h16"></path>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Anfragewert gesamt</span>
            <span class="inv-summary-value">
              {{ number_format($overallRequest, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Zweite Summary-Reihe: Ø Min, Ø Max, Gesamt Ø, Ø Einzelanfragwert --}}
  <div class="inv-summary-shell">
    <div class="row g-2">
      <div class="col-md-3 col-6">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-avg-min">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="3.2"></circle>
              <path d="M12 12v6"></path>
              <path d="M8.5 15.5L12 19l3.5-3.5"></path>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamtwert Ø Min</span>
            <span class="inv-summary-value">
              {{ number_format($overallAvgMin, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-avg-max">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="16" r="3.2"></circle>
              <path d="M12 12V6"></path>
              <path d="M8.5 8.5L12 5l3.5 3.5"></path>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamtwert Ø Max</span>
            <span class="inv-summary-value">
              {{ number_format($overallAvgMax, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-avg-total">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5.5 18.5A8.5 8.5 0 0 1 12 4a8.5 8.5 0 0 1 6.5 14.5"></path>
              <path d="M12 12l4-2"></path>
              <circle cx="12" cy="12" r="1.1"></circle>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Gesamt Ø</span>
            <span class="inv-summary-value">
              {{ number_format($overallAvgTotal, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="inv-summary-card">
          <div class="inv-summary-icon inv-summary-icon-avg-single">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5.5 18.5A8.5 8.5 0 0 1 12 4"></path>
              <path d="M12 12l3-1.5"></path>
              <circle cx="12" cy="12" r="1.1"></circle>
            </svg>
          </div>
          <div class="inv-summary-body">
            <span class="inv-summary-label">Ø&nbsp;Einzelanfragwert</span>
            <span class="inv-summary-value">
              {{ number_format($overallSingleRequestAvg, 2, ',', '.') }} €
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabelle: Kunden × Produkte --}}
  <div class="table-responsive">
    <table id="investmentTable" class="table table-sm table-striped table-bordered align-middle" data-investment-table="1">
      <thead>
        <tr>
          <th data-sort="text">Kunde</th>
          <th data-sort="text">Produkte & mögliche Investition</th>
          <th class="text-right" data-sort="number">Gesamtkundenwert</th>
        </tr>
      </thead>

      <tbody>
        @forelse($customers as $customer)
          @php
            $totalMin     = data_get($customer, 'total_min', 0);
            $totalMax     = data_get($customer, 'total_max', 0);
            $totalCount   = data_get($customer, 'total_count', 0);
            $avgMin       = data_get($customer, 'avg_min', 0);
            $avgMax       = data_get($customer, 'avg_max', 0);
            $avgTotal     = data_get($customer, 'avg_total', 0);
            $requestValue = ($totalMin + $totalMax) / 2;
          @endphp

          <tr>
            {{-- Kunde --}}
            <td class="align-middle">
              <strong>{{ data_get($customer, 'name') }}</strong>
            </td>

            {{-- Produkte: Min / Max / Durchschnitt (Min+Max)/2 mit Balance-Icon --}}
            <td class="align-middle inv-products-cell">
              @php $hasAnyProduct = false; @endphp

              @foreach($productColumns as $prodCol)
                @php
                  $p       = data_get($customer, 'products.' . $prodCol->id);
                  $min     = data_get($p, 'min_value', 0);
                  $max     = data_get($p, 'max_value', 0);
                  $count   = data_get($p, 'count', 0);
                  $avgProd = ($min + $max) / 2;
                @endphp

                @if($p)
                  @php $hasAnyProduct = true; @endphp

                  <div class="inv-product-row">
                    <div class="inv-product-label">
                      {{ $prodCol->name }}
                      @if($count)
                        <span class="inv-product-count">×{{ $count }}</span>
                      @endif
                    </div>

                    <div class="inv-badge">
                      <div class="inv-badge-part inv-badge-min">
                        <i class="feather icon-arrow-down-right"></i>
                        <span>{{ number_format($min, 2, ',', '.') }} €</span>
                      </div>

                      <div class="inv-badge-divider"></div>

                      <div class="inv-badge-part inv-badge-max">
                        <i class="feather icon-arrow-up-right"></i>
                        <span>{{ number_format($max, 2, ',', '.') }} €</span>
                      </div>

                      <div class="inv-badge-divider"></div>

                      <div class="inv-badge-part inv-badge-avg">
                        <span class="mr-25" style="display:inline-flex;align-items:center;">
                          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18"></path>
                            <path d="M4 6h16"></path>
                            <path d="M6 6l-3 5h6l-3-5z"></path>
                            <path d="M18 6l-3 5h6l-3-5z"></path>
                            <path d="M4 19h16"></path>
                          </svg>
                        </span>
                        <span>{{ number_format($avgProd, 2, ',', '.') }} €</span>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach

              @unless($hasAnyProduct)
                <span>Keine Produktdaten</span>
              @endunless
            </td>

            {{-- Gesamtwerte pro Kunde – kompletter Block --}}
            <td class="align-middle text-right" data-sort-val="{{ $requestValue }}">
              <div class="inv-total-wrap">
                <div class="inv-total-row">
                  <span>Gesamtanfrage</span>
                  <strong>{{ $totalCount }}</strong>
                </div>
                <div class="inv-total-row">
                  <span>Summe Min</span>
                  <strong>{{ number_format($totalMin, 2, ',', '.') }} €</strong>
                </div>
                <div class="inv-total-row">
                  <span>Summe Max</span>
                  <strong>{{ number_format($totalMax, 2, ',', '.') }} €</strong>
                </div>
                <div class="inv-total-row">
                  <span>Ø Min</span>
                  <strong>{{ number_format($avgMin, 2, ',', '.') }} €</strong>
                </div>
                <div class="inv-total-row">
                  <span>Ø Max</span>
                  <strong>{{ number_format($avgMax, 2, ',', '.') }} €</strong>
                </div>
                <div class="inv-total-row">
                  <span>Ø Gesamt</span>
                  <strong>{{ number_format($avgTotal, 2, ',', '.') }} €</strong>
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center">
              Keine Daten für die aktuelle Auswahl.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
