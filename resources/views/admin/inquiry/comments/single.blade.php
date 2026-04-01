<div class="comment-card border rounded p-3 mb-3 shadow-sm">
    <div class="d-flex justify-content-between">
        <div>
            <strong>{{ $comment->employee->name }} {{ $comment->employee->lastname }}</strong>
            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-success like-btn" data-id="{{ $comment->id }}">👍 {{ $comment->likes }}</button>
            <button class="btn btn-sm btn-outline-danger dislike-btn" data-id="{{ $comment->id }}">👎 {{ $comment->dislikes }}</button>
            <button class="btn btn-sm btn-outline-primary reply-btn" data-id="{{ $comment->id }}">Reply</button>
            <button class="btn btn-sm btn-outline-warning edit-btn" data-id="{{ $comment->id }}">Edit</button>
            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $comment->id }}">Delete</button>
        </div>
    </div>
    <p class="mt-2">{{ $comment->comment }}</p>

    {{-- Replies --}}
    @foreach($comment->replies as $reply)
        <div class="ms-4 mt-3 border-start ps-3">
            @include('admin.inquiry.comments.single', ['comment' => $reply])
        </div>
    @endforeach
</div>
