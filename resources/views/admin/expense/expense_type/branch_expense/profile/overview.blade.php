<div class="oc-card" style="padding:16px">
  <div class="oc-form-section">
    <div class="oc-form-section-title">Fälligkeiten & Warnungen</div>
    <div class="oc-mini-list" id="alertList">
      @forelse($alerts as $alert)
        <div class="oc-alert-item {{ $alert['status'] === 'overdue' ? 'overdue' : '' }}">
          <strong>{{ $alert['title'] }}</strong>
          <div class="small text-muted">{{ $alert['date'] }} · {{ number_format($alert['amount'], 2, ',', '.') }} €</div>
        </div>
      @empty
        <div class="text-muted">Keine Fälligkeiten in den nächsten 30 Tagen.</div>
      @endforelse
    </div>
  </div>

  <div class="oc-form-section">
    <div class="oc-form-section-title">Branch Zusammenfassung</div>
    <div class="row">
      <div class="col-md-6">
        <strong>{{ $expense->branch?->branch }}</strong><br>
        <span class="text-muted">{{ $expense->branch?->street }} {{ $expense->branch?->postcode }} {{ $expense->branch?->city }}</span><br>
        <span class="text-muted">Jahr: {{ $expense->year }}</span>
      </div>
      <div class="col-md-6">
        <div>Total: <strong>{{ number_format($kpis['total'] ?? 0, 2, ',', '.') }} €</strong></div>
        <div>Personal: {{ number_format($kpis['employee_total'] ?? 0, 2, ',', '.') }} €</div>
        <div>Raten: {{ number_format($kpis['installment_total'] ?? 0, 2, ',', '.') }} €</div>
      </div>
    </div>
  </div>
</div>
