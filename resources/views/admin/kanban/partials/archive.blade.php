

<div id="archiveInner">
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
        @forelse ($archive as $lead)
         <tr id="row-{{ $lead->lead_product_id }}"
            data-customer-id="{{ $lead->customer_id }}"
            data-alt-id="{{ $lead->alternative_id }}"
            data-product-id="{{ $lead->product_id }}">
            <td>{{ $lead->customer_name }} {{ $lead->customer_lastname }}</td>
            <td>{{ $lead->street }}, {{ $lead->postcode }} {{ $lead->city }}</td>
            <td>{{ $lead->initial }}</td>
            <td>
              {{ $lead->employee_name }} {{ $lead->employee_lastname }}
            </td>
            <td>{{ \Carbon\Carbon::parse($lead->updated_at)->format('d.m.Y') }}</td>
            <td class="d-flex align-items-center">
              <select class="form-control d-inline-block w-auto restore-select mr-2">
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
                data-source="archive"
                data-id="{{ $lead->lead_product_id }}"
                title="Wiederherstellen">
                Wiederherstellen
              </button>
            </td>
        </tr>

        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">Keine Archiv-Leads gefunden.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-3 pb-3">
      {{ $archive->withQueryString()->onEachSide(1)->fragment('archive')->links() }}
  </div>
</div>