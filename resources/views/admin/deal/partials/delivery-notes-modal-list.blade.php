@if($deliveryNotes->isEmpty())
    <div class="text-center text-muted py-4">
        Keine Lieferscheine für diesen Auftrag gefunden.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th>Lieferschein</th>
                    <th>Bestell-Nr.</th>
                    <th>Status</th>
                    <th>Fortschritt</th>
                    <th>Datum</th>
                    <th>Übergabe</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryNotes as $note)
                    <tr>
                        <td><strong>{{ $note->delivery_note ?? '#'.$note->id }}</strong></td>
                        <td>{{ $note->order_no ?? '-' }}</td>
                        <td>{{ $note->status ?? '-' }}</td>
                        <td>{{ $note->progress ?? 0 }}%</td>
                        <td>{{ optional($note->handover_date)->format('d.m.Y') ?? optional($note->order_date)->format('d.m.Y') ?? '-' }}</td>
                        <td>
                            {{ optional($note->handoverEmployee)->name }}
                            {{ optional($note->handoverEmployee)->lastname }}
                        </td>
                        <td>
                            <a href="{{ url('/admin/delivery-notes/'.$note->id.'/profile') }}" class="btn btn-sm btn-primary">
                                Öffnen
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif