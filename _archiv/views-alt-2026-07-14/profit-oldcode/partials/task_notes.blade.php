@if ($comments->count())
    @foreach ($comments as $comment)
        <div class="mb-2 border-bottom pb-1">
            <strong>{{ $comment->author->name ?? 'Unbekannt' }}</strong>
            <small class="text-muted ml-1">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
            <p class="mb-1">{{ $comment->comment }}</p>

            {{-- Replies --}}
            @foreach ($comment->replies as $reply)
                <div class="ml-3 border-left pl-2">
                    <strong>{{ $reply->author->name ?? 'Unbekannt' }}</strong>
                    <small class="text-muted ml-1">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                    <p class="mb-1">{{ $reply->comment }}</p>
                </div>
            @endforeach
        </div>
    @endforeach
@else
    <p class="text-muted small">Keine Kommentare vorhanden.</p>
@endif
