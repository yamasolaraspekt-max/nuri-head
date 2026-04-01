@php
    $user  = $comment->user;
    $name  = $user ? trim(($user->lastname ?? '').' '.($user->name ?? '')) : 'Unbekannt';
    $avatar = $user && $user->image
        ? asset('images/employee/'.$user->image)
        : asset('images/employee/noimage.png');
@endphp

<div class="cr-comment-row" data-comment-id="{{ $comment->id }}">
    <img src="{{ $avatar }}" alt="{{ $name }}" class="cr-comment-avatar">
    <div class="cr-comment-bubble">
        <div class="cr-comment-meta">
            <span class="cr-comment-author">{{ $name }}</span>
            <span class="cr-comment-time">
                {{ optional($comment->created_at)->format('d.m.Y H:i') }}
            </span>
        </div>
        <div class="cr-comment-text">
            {{ $comment->comment }}
        </div>

        @if($comment->replies && $comment->replies->count())
            <div class="cr-comment-replies">
                @foreach($comment->replies as $reply)
                    @php
                        $rUser = $reply->user;
                        $rName = $rUser ? trim(($rUser->lastname ?? '').' '.($rUser->name ?? '')) : 'Unbekannt';
                        $rAvatar = $rUser && $rUser->image
                            ? asset('images/employee/'.$rUser->image)
                            : asset('images/employee/noimage.png');
                    @endphp
                    <div class="cr-comment-row cr-comment-row--reply" data-comment-id="{{ $reply->id }}">
                        <img src="{{ $rAvatar }}" alt="{{ $rName }}" class="cr-comment-avatar">
                        <div class="cr-comment-bubble">
                            <div class="cr-comment-meta">
                                <span class="cr-comment-author">{{ $rName }}</span>
                                <span class="cr-comment-time">
                                    {{ optional($reply->created_at)->format('d.m.Y H:i') }}
                                </span>
                            </div>
                            <div class="cr-comment-text">
                                {{ $reply->comment }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
