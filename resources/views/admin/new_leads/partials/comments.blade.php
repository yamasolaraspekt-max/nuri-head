<div class="comment-sidebar p-3">
    @foreach($comments as $comment)
        @include('admin.new_leads.partials.single_comment', ['comment' => $comment])
    @endforeach
</div>

