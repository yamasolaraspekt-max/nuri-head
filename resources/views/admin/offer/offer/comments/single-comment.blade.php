<div class="media mb-3" id="comment-{{ $comment->id }}">
    <img src="{{ asset('images/employee/' . ($comment->employee->image ?? 'images/gender/male.png')) }}"
         class="rounded-circle mr-2" width="40" height="40" alt="Avatar">

    <div class="media-body">
        <div class="d-flex justify-content-between align-items-center">
            <strong>{{ $comment->employee->name ?? 'Unbekannt' }}</strong>
            <small>{{ $comment->created_at->diffForHumans() }}</small>
        </div>

        <p class="mb-1" id="comment-text-{{ $comment->id }}">{{ $comment->comment }}</p>

        <div class="text-muted mb-1">
            <a href="#" onclick="editComment({{ $comment->id }}, '{{ e($comment->comment) }}')">Bearbeiten</a> |
            <a href="#" onclick="deleteComment({{ $comment->id }})">Löschen</a>
        </div>

        <!-- 🧵 Reply form -->
        <form class="replyForm" data-parent-id="{{ $comment->id }}">
            <div class="input-group mb-2">
                <input type="text" name="comment" class="form-control form-control-sm replyInput" placeholder="Antwort hinzufügen..." required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Antworten</button>
                </div>
            </div>
        </form>


        <!-- 🧵 Replies go here -->
        <div class="reply-children ml-4 mt-2" data-parent="{{ $comment->id }}">
            @if ($comment->children && $comment->children->count())
                <div class="reply-children ml-4 mt-2" data-parent="{{ $comment->id }}">
                    @foreach ($comment->children as $child)
                        @include('admin.offer.offer.comments.single-comment', ['comment' => $child])
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
