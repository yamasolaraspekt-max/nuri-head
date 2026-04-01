@foreach ($comments as $comment)
    @include('admin.offer.offer.comments.single-comment', ['comment' => $comment])
@endforeach
