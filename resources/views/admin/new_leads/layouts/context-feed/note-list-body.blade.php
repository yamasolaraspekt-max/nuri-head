@if($notes->count())
    @foreach($notes as $note)
        @include('admin.new_leads.layouts.notes.single-note', ['note' => $note])
    @endforeach
@else
    @include('admin.new_leads.layouts.context-feed.empty', [
        'title' => 'Keine Notizen',
        'message' => 'Keine Notizen für diesen Kundenbereich vorhanden.',
        'icon' => 'message-square',
    ])
@endif