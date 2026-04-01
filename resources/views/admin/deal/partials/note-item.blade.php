<div class="note-message {{ $note->created_by == $employeeId ? 'own' : '' }}" id="note_{{ $note->id }}" style="margin-left:12px !important;">
    <div class="d-flex align-items-start">
        @php
            $emp = DB::table('employees')->where('id', $note->created_by)->first();
            $img = $emp && $emp->image ? asset('images/employee/' . $emp->image) : asset('default-avatar.png');
        @endphp

        <img src="{{ $img }}" class="rounded-circle mr-2" width="30" height="30">

        <div style="flex:1">
            <p class="mb-1">{{ $note->description }}</p>
            <small class="text-muted d-block">
                {{ $emp->name ?? 'Unbekannt' }} • {{ \Carbon\Carbon::parse($note->created_at)->format('d.m.Y H:i') }}
            </small>

            <div class="mt-1 d-flex gap-2 align-items-center">
                @if($note->created_by == $employeeId)
                    <i class="fa fa-pencil text-primary cursor-pointer mr-2" onclick="editNote({{ $note->id }})" title="Bearbeiten"></i>
                    <i class="fa fa-trash text-danger cursor-pointer mr-2" onclick="deleteNote({{ $note->id }})" title="Löschen"></i>
                @endif
                <i class="fa fa-reply text-info cursor-pointer" onclick="toggleReplyInput({{ $note->id }})" title="Antworten"></i>
            </div>

            {{-- Reply Input --}}
            <div class="reply-box mt-2" id="reply_box_{{ $note->id }}" style="display:none;">
                <textarea class="form-control form-control-sm mb-1" rows="2" placeholder="Antwort eingeben..." id="reply_input_{{ $note->id }}"></textarea>
                <button class="btn btn-sm btn-success" onclick="sendReply({{ $note->id }})">
                    <i class="fa fa-paper-plane"></i> Senden
                </button>
            </div>

            {{-- Replies --}}
            @if($groupedNotes->has($note->id))
                <div class="ml-4 border-left pl-3 mt-3">
                    @foreach($groupedNotes[$note->id] as $reply)
                        @include('admin.deal.partials.note-item', [
                            'note' => $reply,
                            'groupedNotes' => $groupedNotes,
                            'employeeId' => $employeeId
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
