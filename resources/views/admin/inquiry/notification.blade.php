<ul class="timeline">
    @forelse($notifications as $note)
        <li class="timeline-item mb-3">
            <span class="timeline-point timeline-point-success"></span>
            <div class="timeline-event">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-1">{{ $note->data['title'] }}</h6>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($note->data['performed_at'])->diffForHumans() }}</small>
                </div>
                <p class="mb-0">{{ $note->data['message'] }}</p>
                <small class="text-muted">
                    👤 Von: <strong>{{ $note->data['from'] }}</strong><br>
                    🏷️ Typ: {{ $note->data['contact_type'] ?? '—' }}
                </small>
                <br>
                <a href="{{ route('inquiry.show.profile', $note->data['lead_id']) }}" class="btn btn-sm btn-outline-primary mt-2">➡️ Zur Anfrage</a>
            </div>
        </li>
    @empty
        <li class="timeline-item">
            <span class="timeline-point timeline-point-warning"></span>
            <div class="timeline-event">
                <p class="mb-0">Keine Benachrichtigungen für diese Anfrage gefunden.</p>
            </div>
        </li>
    @endforelse
</ul>
