<div class="oc-card" style="padding:16px">
  <div class="oc-form-section">
    <div class="oc-form-section-title">Mitarbeiterkosten</div>
    <div class="oc-mini-list" id="employeeList">
      @forelse(($linked['employees'] ?? []) as $employee)
        <div class="oc-mini-item">
          <strong>{{ $employee->name }} {{ $employee->lastname }}</strong>
          <div class="small text-muted">{{ $employee->department_name }} · {{ $employee->position }}</div>
          <div>{{ number_format($employee->salary, 2, ',', '.') }} €</div>
        </div>
      @empty
        <div class="text-muted">Keine Mitarbeiterkosten gefunden.</div>
      @endforelse
    </div>
  </div>

  <div class="oc-form-section">
    <div class="oc-form-section-title">Ratenzahlungen</div>
    <div class="oc-mini-list" id="installmentList">
      @forelse(($linked['installments'] ?? []) as $installment)
        <div class="oc-mini-item">
          <strong>{{ $installment->installment_id ?? '#'.$installment->id }}</strong>
          <div class="small text-muted">{{ $installment->type ?? '-' }} · {{ $installment->due_date ?? '-' }}</div>
          <div>{{ number_format($installment->total ?? 0, 2, ',', '.') }} €</div>
        </div>
      @empty
        <div class="text-muted">Keine Raten gefunden.</div>
      @endforelse
    </div>
  </div>

  <div class="oc-form-section">
    <div class="oc-form-section-title">Maschinen & Assets</div>
    <div class="oc-mini-list" id="assetMachineList">
      @forelse(($linked['assets'] ?? []) as $asset)
        <div class="oc-mini-item"><strong>Asset: {{ $asset->item ?? '#'.$asset->id }}</strong><div>{{ number_format($asset->amount ?? 0, 2, ',', '.') }} €</div></div>
      @empty
      @endforelse
      @forelse(($linked['machines'] ?? []) as $machine)
        <div class="oc-mini-item"><strong>Maschine: {{ $machine->name }} {{ $machine->model }}</strong><div>{{ number_format($machine->amount ?? 0, 2, ',', '.') }} €</div></div>
      @empty
      @endforelse
      @if(empty(($linked['assets'] ?? [])) && empty(($linked['machines'] ?? [])))
        <div class="text-muted">Keine Assets/Maschinen gefunden.</div>
      @endif
    </div>
  </div>
</div>
