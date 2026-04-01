{{-- resources/views/admin/new_leads/layouts/invoice.blade.php --}}
@php
  $productCount = isset($product_invoices) ? $product_invoices->count() : 0;
  $generalCount = isset($general_invoices) ? $general_invoices->count() : 0;

  $statusMap = [
    'draft'     => 'Entwurf',
    'sent'      => 'Gesendet',
    'paid'      => 'Bezahlt',
    'overdue'   => 'Überfällig',
    'cancelled' => 'Storniert',
  ];

  $tabUid = 'nlInvTabs_' . ($customer->id ?? 'x') . '_' . (int)$alternative_id . '_' . (int)$product_id;
  $prodId = $tabUid . '_prod';
  $genId  = $tabUid . '_gen';
@endphp

<div style="padding:18px;">
  <style>
    /* Tabs funktionieren ohne JS (wichtig für AJAX innerHTML) */
    .nl-inv-tabs input{display:none}
    .nl-inv-tabbar{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
    .nl-inv-tabbtn{
      border:1px solid #e2e8f0;background:#fff;border-radius:12px;
      padding:10px 12px;font-weight:900;color:#0f172a;
      display:inline-flex;gap:10px;align-items:center;cursor:pointer;
      user-select:none;
    }
    .nl-inv-tabbtn .nl-count{
      font-weight:900;
      padding:2px 8px;border-radius:999px;
      background:#f1f5f9;border:1px solid #e2e8f0;color:#334155;
      font-size:12px;
    }
    /* Aktiv-Farbe: #74b2d4 */
    #{{ $prodId }}:checked ~ .nl-inv-tabbar label[for="{{ $prodId }}"],
    #{{ $genId }}:checked  ~ .nl-inv-tabbar label[for="{{ $genId }}"]{
      background:#74b2d4; border-color:#74b2d4; color:#fff;
    }
    #{{ $prodId }}:checked ~ .nl-inv-tabbar label[for="{{ $prodId }}"] .nl-count,
    #{{ $genId }}:checked  ~ .nl-inv-tabbar label[for="{{ $genId }}"] .nl-count{
      background:rgba(255,255,255,.22); border-color:rgba(255,255,255,.35); color:#fff;
    }
    .nl-inv-panels .nl-panel{display:none}
    #{{ $prodId }}:checked ~ .nl-inv-panels .nl-panel-prod{display:block}
    #{{ $genId }}:checked  ~ .nl-inv-panels .nl-panel-gen{display:block}
  </style>

  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div style="min-width:0;">
      <div style="font-size:18px;font-weight:900;color:#0f172a;">Rechnungen</div>
      <div style="margin-top:4px;color:#64748b;font-weight:700;font-size:13px;">
        Kunde:
        <span style="color:#0f172a;">{{ $customer->firma ?? ($customer->lastname ?? '') }}</span>
        &nbsp;•&nbsp; Alternative:
        <span style="color:#0f172a;">#{{ (int)$alternative_id }}</span>
        &nbsp;•&nbsp; Produkt:
        <span style="color:#0f172a;">{{ (int)$product_id > 0 ? '#'.(int)$product_id : 'Alle' }}</span>
      </div>
    </div>

    <div style="display:flex;gap:8px;align-items:center;">
      <a href="{{ route('admin.invoices.index') }}?customer_id={{ $customer->id }}&alternative_id={{ (int)$alternative_id }}&product_id={{ (int)$product_id }}"
         style="text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:12px;padding:10px 12px;font-weight:900;color:#0f172a;display:inline-flex;gap:8px;align-items:center;">
        <i class="fa-solid fa-arrow-up-right-from-square" style="color:#64748b;"></i>
        Vollansicht
      </a>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="nl-inv-tabs" id="{{ $tabUid }}">
    <input type="radio" name="{{ $tabUid }}_tab" id="{{ $prodId }}" checked>
    <input type="radio" name="{{ $tabUid }}_tab" id="{{ $genId }}">

    <div class="nl-inv-tabbar">
      <label class="nl-inv-tabbtn" for="{{ $prodId }}">
        <i class="fa-solid fa-file-invoice" style="opacity:.85;"></i>
        Produkt-Rechnungen <span class="nl-count">{{ $productCount }}</span>
      </label>

      <label class="nl-inv-tabbtn" for="{{ $genId }}">
        <i class="fa-solid fa-layer-group" style="opacity:.85;"></i>
        Allgemeine Rechnungen <span class="nl-count">{{ $generalCount }}</span>
      </label>
    </div>

    <div class="nl-inv-panels" style="margin-top:14px;">
      {{-- Panel: Produkt-Rechnungen --}}
      <div class="nl-panel nl-panel-prod">
        <div style="background:#fff;border:1px solid rgba(226,232,240,.9);border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(2,6,23,.06);">
          <div style="padding:12px 14px;border-bottom:1px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div style="font-weight:900;color:#0f172a;">Treffer: {{ $productCount }}</div>
            <div style="color:#64748b;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:.06em;">Letzte zuerst</div>
          </div>

          @if(!$productCount)
            <div style="padding:18px;color:#64748b;font-weight:700;">
              Keine Produkt-Rechnungen gefunden.
            </div>
          @else
            <div style="overflow:auto;">
              <table style="width:100%;border-collapse:collapse;min-width:820px;">
                <thead>
                  <tr>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Datum</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Nr.</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Typ</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:right;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Gesamt</th>
                    <th style="text-align:right;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Aktion</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($product_invoices as $inv)
                    <tr style="border-bottom:1px solid #eef2f7;">
                      <td style="padding:12px 14px;font-weight:800;color:#0f172a;">
                        {{ \Illuminate\Support\Carbon::parse($inv->issue_date)->format('d.m.Y') }}
                      </td>
                      <td style="padding:12px 14px;font-weight:900;color:#0f172a;">
                        {{ $inv->invoice_no ?? '—' }}
                      </td>
                      <td style="padding:12px 14px;font-weight:800;color:#0f172a;">
                        {{ $inv->type ?? '—' }}
                      </td>
                      <td style="padding:12px 14px;font-weight:900;color:#334155;">
                        @php($s = strtolower($inv->status ?? 'draft'))
                        {{ $statusMap[$s] ?? $s }}
                      </td>
                      <td style="padding:12px 14px;text-align:right;font-weight:900;color:#0f172a;">
                        {{ number_format((float)($inv->total_amount ?? 0), 2, ',', '.') }} €
                      </td>
                      <td style="padding:12px 14px;text-align:right;">
                        <a href="{{ route('admin.invoices.index') }}?open={{ $inv->id }}"
                           style="text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:10px;padding:8px 10px;font-weight:900;color:#0f172a;display:inline-flex;gap:8px;align-items:center;">
                          <i class="fa-solid fa-folder-open" style="color:#64748b;"></i>
                          Öffnen
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Panel: Allgemeine Rechnungen --}}
      <div class="nl-panel nl-panel-gen">
        <div style="background:#fff;border:1px solid rgba(226,232,240,.9);border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(2,6,23,.06);">
          <div style="padding:12px 14px;border-bottom:1px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div style="font-weight:900;color:#0f172a;">Treffer: {{ $generalCount }}</div>
            <div style="color:#64748b;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:.06em;">Letzte zuerst</div>
          </div>

          @if(!$generalCount)
            <div style="padding:18px;color:#64748b;font-weight:700;">
              Keine allgemeinen Rechnungen gefunden (ohne dieses Produkt).
            </div>
          @else
            <div style="overflow:auto;">
              <table style="width:100%;border-collapse:collapse;min-width:820px;">
                <thead>
                  <tr>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Datum</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Nr.</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Typ</th>
                    <th style="text-align:left;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:right;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Gesamt</th>
                    <th style="text-align:right;padding:12px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.06em;font-weight:900;border-bottom:1px solid #e2e8f0;">Aktion</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($general_invoices as $inv)
                    <tr style="border-bottom:1px solid #eef2f7;">
                      <td style="padding:12px 14px;font-weight:800;color:#0f172a;">
                        {{ \Illuminate\Support\Carbon::parse($inv->issue_date)->format('d.m.Y') }}
                      </td>
                      <td style="padding:12px 14px;font-weight:900;color:#0f172a;">
                        {{ $inv->invoice_no ?? '—' }}
                      </td>
                      <td style="padding:12px 14px;font-weight:800;color:#0f172a;">
                        {{ $inv->type ?? '—' }}
                      </td>
                      <td style="padding:12px 14px;font-weight:900;color:#334155;">
                        @php($s = strtolower($inv->status ?? 'draft'))
                        {{ $statusMap[$s] ?? $s }}
                      </td>
                      <td style="padding:12px 14px;text-align:right;font-weight:900;color:#0f172a;">
                        {{ number_format((float)($inv->total_amount ?? 0), 2, ',', '.') }} €
                      </td>
                      <td style="padding:12px 14px;text-align:right;">
                        <a href="{{ route('admin.invoices.index') }}?open={{ $inv->id }}"
                           style="text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:10px;padding:8px 10px;font-weight:900;color:#0f172a;display:inline-flex;gap:8px;align-items:center;">
                          <i class="fa-solid fa-folder-open" style="color:#64748b;"></i>
                          Öffnen
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
