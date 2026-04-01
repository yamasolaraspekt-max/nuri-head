@if($groupedNotes->has(null))
    @foreach($groupedNotes[null] as $note)
        @include('admin.deal.partials.note-item', [
            'note' => $note,
            'groupedNotes' => $groupedNotes,
            'employeeId' => $employeeId
        ])
    @endforeach
@else
    <p class="text-muted px-2">Keine Notizen vorhanden.</p>
@endif
