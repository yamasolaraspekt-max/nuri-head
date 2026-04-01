@foreach($inquiry->comments as $comment)
    @include('admin.inquiry.comments.single', ['comment' => $comment])
@endforeach
