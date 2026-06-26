@php
    $level = $level ?? 0;
    $isOwn = (string) $note->created_by === (string) $employeeId;

    $emp = DB::table('employees')
        ->where('id', $note->created_by)
        ->select('id', 'name', 'lastname', 'image', 'gender')
        ->first();

    $gender = $emp->gender ?? null;

    $defaultAvatar = $gender === 'Male'
        ? asset('images/gender/male.png')
        : asset('images/gender/female.png');

    $avatar = (!empty($emp?->image) && file_exists(public_path('images/employee/' . $emp->image)))
        ? asset('images/employee/' . $emp->image)
        : $defaultAvatar;

    $authorName = trim(($emp->name ?? 'Unbekannt') . ' ' . ($emp->lastname ?? ''));
@endphp

<div class="deal-note-thread" id="note_{{ $note->id }}">
    <div class="deal-note {{ $isOwn ? 'own' : '' }} level-{{ $level }}">
        <div class="deal-note-avatar-wrap">
            <img src="{{ $avatar }}" alt="{{ $authorName }}" class="deal-note-avatar">
        </div>

        <div class="deal-note-body">
            <div class="deal-note-card">
                <div class="deal-note-head">
                    <div class="deal-note-author">{{ $authorName ?: 'Unbekannt' }}</div>
                    <div class="deal-note-time">{{ \Carbon\Carbon::parse($note->created_at)->format('d.m.Y H:i') }}</div>
                </div>

                <div class="deal-note-text">
                    {!! nl2br(e($note->description)) !!}
                </div>

                <div class="deal-note-actions">
                    @if($isOwn)
                        <button type="button" class="deal-note-action text-primary" onclick="editNote({{ $note->id }})" title="Bearbeiten">
                            <i class="fa fa-pencil"></i>
                        </button>

                        <button type="button" class="deal-note-action text-danger" onclick="deleteNote({{ $note->id }})" title="Löschen">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif

                    <button type="button" class="deal-note-action text-info" onclick="toggleReplyInput({{ $note->id }})" title="Antworten">
                        <i class="fa fa-reply"></i>
                    </button>
                </div>
            </div>

            <div class="deal-reply-box" id="reply_box_{{ $note->id }}" style="display:none;">
                <textarea
                    class="form-control form-control-sm mb-2"
                    rows="2"
                    placeholder="Antwort eingeben..."
                    id="reply_input_{{ $note->id }}"
                ></textarea>

                <button class="btn btn-sm btn-success" type="button" onclick="sendReply({{ $note->id }})">
                    <i class="fa fa-paper-plane mr-1"></i> Senden
                </button>
            </div>
        </div>
    </div>

    @if($groupedNotes->has($note->id) && $groupedNotes[$note->id]->count())
        <div class="deal-note-children">
            @foreach($groupedNotes[$note->id] as $reply)
                @include('admin.deal.partials.note-item', [
                    'note' => $reply,
                    'groupedNotes' => $groupedNotes,
                    'employeeId' => $employeeId,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>

@if($level === 0)
<style>
.deal-note{
    display:flex;
    align-items:flex-start;
    gap:10px;
}

.deal-note.own{
    flex-direction:row-reverse;
}

.deal-note-avatar-wrap{
    flex:0 0 auto;
    padding-top:2px;
}

.deal-note-avatar{
    width:34px;
    height:34px;
    border-radius:999px;
    object-fit:cover;
    border:2px solid #e5e7eb;
    background:#fff;
}

.deal-note-body{
    min-width:0;
    flex:1;
    max-width:calc(100% - 44px);
}

.deal-note.own .deal-note-body{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
}

.deal-note-card{
    width:100%;
    max-width:100%;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:12px 14px;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
}

.deal-note.own .deal-note-card{
    background:#f4fef0;
    border-color:#d9f0d0;
}

.deal-note-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:8px;
    flex-wrap:wrap;
}

.deal-note-author{
    font-size:13px;
    font-weight:900;
    color:#111827;
}

.deal-note-time{
    font-size:11px;
    color:#6b7280;
    white-space:nowrap;
}

.deal-note-text{
    font-size:13px;
    line-height:1.6;
    color:#1f2937;
    word-break:break-word;
}

.deal-note-actions{
    display:flex;
    align-items:center;
    gap:10px;
    margin-top:10px;
}

.deal-note-action{
    border:none;
    background:transparent;
    padding:0;
    cursor:pointer;
    font-size:14px;
    line-height:1;
}

.deal-note-action:hover{
    opacity:.8;
}

.deal-reply-box{
    width:100%;
    margin-top:8px;
    padding:10px 12px;
    border:1px solid #e5e7eb;
    border-radius:14px;
    background:#fafafa;
}

.deal-note.level-1 .deal-note-card,
.deal-note.level-2 .deal-note-card,
.deal-note.level-3 .deal-note-card{
    border-radius:14px;
    padding:10px 12px;
}

.deal-note.level-1 .deal-note-avatar,
.deal-note.level-2 .deal-note-avatar,
.deal-note.level-3 .deal-note-avatar{
    width:30px;
    height:30px;
}
</style>
@endif