@forelse($deletedNotes as $note)
    <div class="card mb-2 border-left border-danger" data-id="{{ $note->id }}">
        <div class="card-body">
            @if($note->parent_id && $note->parent)
                <div class="mb-2 text-muted small">
                    <i class="feather icon-corner-up-left"></i>
                    Ursprünglich als Antwort zu:
                    <div class="bg-white border p-2 mt-1 rounded small">{!! Str::limit(strip_tags($note->parent->description), 150) !!}</div>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="text-primary">{{ $note->creator->name ?? 'Unbekannt' }}</strong>
                <small class="text-muted">{{ \Carbon\Carbon::parse($note->deleted_at)->format('d.m.y // H:i') }}</small>
            </div>

            <div class="text-dark small">{!! $note->description !!}</div>

            <div class="mt-2 text-right">
                <button class="btn btn-sm btn-success" onclick="restoreDeletedNote({{ $note->id }})">
                    <i class="feather icon-rotate-ccw"></i> Wiederherstellen
                </button>
                <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteNote({{ $note->id }})">
                    <i class="feather icon-trash-2"></i> Endgültig löschen
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="text-muted">Keine gelöschten Notizen gefunden.</div>
@endforelse
