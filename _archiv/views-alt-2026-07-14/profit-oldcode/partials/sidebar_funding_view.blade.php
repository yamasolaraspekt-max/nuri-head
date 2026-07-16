@if($lead && $alternative && $product)
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0">{{ $lead->name ?? '' }} {{ $lead->lastname ?? '' }}</h4>
      <small class="text-muted">{{ $lead->full_address ?? '-' }}</small><br>
      <small class="text-muted">Produkt: {{ $product->article_group ?? '-' }}</small>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary text-center p-2">
        <h6 class="mb-0">Basis</h6>
        <h4>{{ optional($funding)->base_percentage ?? '0' }}%</h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success text-center p-2">
        <h6 class="mb-0">Effizienz</h6>
        <h4>{{ optional($funding)->efficiency_percentage ?? '0' }}%</h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning text-center p-2">
        <h6 class="mb-0">Schnelligkeit</h6>
        <h4>{{ optional($funding)->speed_percentage ?? '0' }}%</h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-danger text-center p-2">
        <h6 class="mb-0">Einkommen</h6>
        <h4>{{ optional($funding)->income_percentage ?? '0' }}%</h4>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary pb-1 text-white">Gebäudedetails</div>
        <div class="card-body p-0">
          <table class="table table-sm table-striped mb-0" id="funding-details-table">
            @php $fields = [
              'house_type' => 'Haus-Typ',
              'is_owner' => 'Eigentümer',
              'is_living_inside' => 'Selbstnutzung', 
              'heating_age' => 'Heizungsalter', 
              'incmoe' => 'Haushaltseinkommen', 
              'living_space' => 'Wohnfläche',
              'number_we' => 'Wohneinheiten',
              'installation_year' => 'Installationsjahr',
              'status' => 'Status',
            ];
            @endphp

            @foreach($fields as $field => $label)
              <tr>
                <th>{{ $label }}</th>
                <td>
                  <span class="editable-field" data-field="{{ $field }}" data-lead-id="{{ $lead->id }}" data-alt-id="{{ $alternative->id }}" data-product-id="{{ $product->id }}">
                    {{ $field === 'is_owner' || $field === 'is_living_inside' || $field === 'uses_environmental_heat' ? (optional($funding)->$field ? 'Ja' : 'Nein') : (optional($funding)->$field ?? '-') }}
                  </span>
                  <i class="feather icon-edit text-primary ms-2"></i>
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-dark text-white">Förderanteile (Diagramm)</div>
        <div class="card-body">
          <canvas id="sidebarFundingChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div> 
 

@else
  <div class="p-3 text-danger">Keine Daten vorhanden</div>
@endif


 