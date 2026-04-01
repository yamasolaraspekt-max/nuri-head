


<div id="junkInner">
  <div class="table-responsive p-3">
    <table class="table table-striped table-bordered align-middle">
      <thead>
        <tr>
          <th>Kunde</th>
          <th>Adresse</th>
          <th>Produkt</th>
          <th>Mitarbeiter</th>
          <th>Datum</th>
          <th>Aktion</th>
        </tr>
      </thead>
      <tbody>
            @forelse ($junk as $lead)
              <tr id="row-{{ $lead->lead_product_id }}"
                  data-customer-id="{{ $lead->customer_id }}"
                  data-alternative-id="{{ $lead->alternative_id }}"
                  data-product-id="{{ $lead->product_id }}">
                <td>{{ $lead->customer_name }} {{ $lead->customer_lastname }}</td>
                <td>{{ $lead->street }}, {{ $lead->postcode }} {{ $lead->city }}</td>
                <td>{{ $lead->initial }}</td>
                <td>
                  {{ $lead->employee_name }} {{ $lead->employee_lastname }}
                </td>
                <td>{{ \Carbon\Carbon::parse($lead->updated_at)->format('d.m.Y') }}</td>
                <td class="d-flex align-items-center flex-wrap" style="gap:.5rem">
                  <select class="form-control d-inline-block w-auto restore-select">
                    <option value="" disabled selected>Wiederherstellen nach…</option>
                    <option value="lead">Lead (Qualifizierung &amp; Angebot)</option>
                    <option value="offer">Angebot (Verkauf)</option>
                    <option value="deal">Auftrag</option>
                    <option value="project">Montage</option>
                    <option value="completed">Abschluss</option>
                    <option value="ticket">Ticket</option>
                  </select>
                  <button
                    class="btn btn-success btn-sm btn-restore"
                    data-source="junk"
                    data-id="{{ $lead->lead_product_id }}"
                    title="Wiederherstellen">
                    Wiederherstellen
                  </button>
                  <button
                    class="btn btn-outline-danger btn-sm btn-purge d-none"
                    data-id="{{ $lead->lead_product_id }}"
                    title="Endgültig löschen">
                    Endgültig löschen
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">Keine Junk-Leads gefunden.</td>
              </tr>
            @endforelse
        </tbody>

    </table>
  </div>

  <div class="px-3 pb-3">
    {{ $junk->withQueryString()->onEachSide(1)->fragment('junk')->links() }}
  </div>
</div>