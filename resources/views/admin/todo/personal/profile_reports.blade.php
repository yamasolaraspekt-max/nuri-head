@php
    // helper to nest comments
    $grouped = $comments->groupBy('parent_id');
@endphp

<div style="margin-bottom:1rem;">
    <form id="tp-report-form">
        @csrf
        <div class="form-group mb-50">
            <label for="tp-report-textarea" style="font-size:.8rem;font-weight:600;">Neuer Bericht / Kommentar</label>
            <textarea id="tp-report-textarea"
                      name="comment"
                      class="form-control"
                      rows="3"
                      placeholder="Schreibe hier deinen Bericht oder Kommentar..."></textarea>
        </div>
        <button type="submit" class="btn btn-sm btn-primary">
            Speichern
        </button>
    </form>
</div>

<div>
    @if($comments->count())
        @include('admin.todo.personal.profile_reports_list', [
            'grouped' => $grouped,
            'parentId'=> null,
        ])
    @else
        <div style="font-size:.8rem;color:#9ca3af;">Noch keine Berichte vorhanden.</div>
    @endif
</div>
