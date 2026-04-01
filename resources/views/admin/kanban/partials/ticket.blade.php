<div id="ticketInner" data-ticket-total="{{ $total }}">
  <div class="table-responsive p-2">
    <table class="table table-striped table-bordered table-sm align-middle mb-0">
      <thead>
        <tr>
          <th>Ticket</th>
          <th>Kunde</th>
          <th>Ort</th>
          <th>Produkt</th>
          <th>Verantwortlicher</th>
          <th>Mitarbeiter (Problem)</th>
          <th>Aufgaben</th>
          <th>Stand</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $t)
          <tr>
            <td>
              <strong>{{ $t->ticket_no }}</strong><br>
              <small class="text-muted">{{ $t->status ?? 'open' }}</small>
            </td>
            <td>{{ $t->customer_lastname }} {{ $t->customer_name }}</td>
            <td>{{ $t->postcode }} {{ $t->city }}</td>
            <td>{{ $t->product_initial }}</td>

            <td>
              @if($t->responsible_id)
                <div class="d-flex align-items-center">
                  @if($t->responsible_image)
                    <img src="{{ asset('images/employee/'.$t->responsible_image) }}" alt="" class="rounded-circle mr-1" width="26" height="26">
                  @endif
                  <span>{{ $t->responsible_lastname }} {{ $t->responsible_name }}</span>
                </div>
              @else
                <span class="text-muted">–</span>
              @endif
            </td>

            <td>
              @if($t->team_count > 0)
                <span class="badge badge-light-primary">{{ $t->team_count }} MA</span><br>
                <small class="text-muted">{{ $t->team_names }}</small>
              @else
                <span class="text-muted">keine</span>
              @endif
            </td>

            <td>
              @if($t->total_tasks > 0)
                <div><strong>{{ $t->done_tasks }}/{{ $t->total_tasks }}</strong> erledigt</div>
                <small class="text-muted">
                  offen: {{ $t->open_tasks }},
                  in Arbeit: {{ $t->progress_tasks }},
                  Team-Slots: {{ $t->team_slots }}
                </small>
              @else
                <span class="text-muted">keine Aufgaben</span>
              @endif
            </td>

            <td>
              <small class="text-muted">
                erstellt: {{ optional($t->created_at)->format('d.m.Y') ?? '-' }}<br>
                aktualisiert: {{ optional($t->updated_at)->format('d.m.Y') ?? '-' }}
              </small>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-3">
              Keine Tickets gefunden.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-1 px-2">
    {{ $tickets->appends(request()->query())->links() }}
  </div>
</div>
