<div class="deal-notes-wrap">
    @if($groupedNotes->has(null) && $groupedNotes[null]->count())
        @foreach($groupedNotes[null] as $note)
            @include('admin.deal.partials.note-item', [
                'note' => $note,
                'groupedNotes' => $groupedNotes,
                'employeeId' => $employeeId,
                'level' => 0
            ])
        @endforeach
    @else
        <div class="deal-notes-empty">
            <i class="fa fa-sticky-note-o mr-1"></i> Keine Notizen vorhanden.
        </div>
    @endif
</div>

<style>
.deal-notes-wrap{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.deal-notes-empty{
    padding:18px 14px;
    border:1px dashed #d1d5db;
    border-radius:14px;
    background:#fff;
    color:#6b7280;
    text-align:center;
    font-size:13px;
}

.deal-note-thread{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.deal-note-children{
    margin-top:10px;
    margin-left:18px;
    padding-left:14px;
    border-left:2px solid #e5e7eb;
    display:flex;
    flex-direction:column;
    gap:10px;
}
</style>