@foreach ($notes as $note)
    <div class="card bg-light border-left border-danger mb-2" data-id="{{ $note->id }}">
        <div class="card-body d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="small text-muted mb-1">
                    <i class="feather icon-user"></i> {{ $note->creator->name ?? 'Unbekannt' }}
                    <i class="feather icon-clock ml-2"></i> {{ \Carbon\Carbon::parse($note->created_at)->format('d.m.y H:i') }}
                </div>
                <div class="note-description small text-dark">{!! $note->description !!}</div>
            </div>
            <div class="ml-3 text-right">
                <button class="btn btn-sm btn-success mb-2" onclick="restoreDeletedNote({{ $note->id }})" title="Wiederherstellen">
                    <i class="feather icon-rotate-ccw"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteNote({{ $note->id }})" title="Endgültig löschen">
                    <i class="feather icon-trash-2"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach
