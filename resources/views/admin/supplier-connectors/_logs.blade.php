<div class="map-card">
    <div class="map-card-header">
        <div>
            <h2 class="map-card-title">Letzte Import Logs</h2>
            <div class="map-card-desc">
                Hier siehst du, ob eine Rückgabe vom Lieferanten-Shop verarbeitet wurde.
            </div>
        </div>
    </div>

    @if($connection->logs->count())
        <div class="map-table-wrap">
            <table class="map-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Quelle</th>
                        <th>Gesamt</th>
                        <th>Erfolgreich</th>
                        <th>Fehler</th>
                        <th>Nachricht</th>
                        <th>Datum</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($connection->logs as $log)
                        <tr>
                            <td>
                                @if($log->status === 'success')
                                    <span class="sc-badge sc-badge-green">success</span>
                                @elseif($log->status === 'failed')
                                    <span class="sc-badge sc-badge-red">failed</span>
                                @else
                                    <span class="sc-badge sc-badge-gray">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td>{{ $log->source_type }}</td>
                            <td>{{ $log->total_items }}</td>
                            <td>{{ $log->success_items }}</td>
                            <td>{{ $log->failed_items }}</td>
                            <td>{{ $log->message }}</td>
                            <td>{{ optional($log->created_at)->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="sc-empty">
            <strong>Noch keine Import Logs vorhanden.</strong>
            Sobald ein Lieferanten-Shop Produkte zurückgibt, wird hier der Importverlauf angezeigt.
        </div>
    @endif
</div>