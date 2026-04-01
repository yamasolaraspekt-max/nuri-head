 @foreach ($comments as $comment)
        <div class="comment mb-3 p-2 border rounded" id="comment-{{ $comment->id }}">
            <div class="d-flex justify-content-between align-items-center">
                <strong>{{ $comment->user->fullname }}</strong>
                <small class="text-muted">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
            </div>

            {{-- Quoted Message (if this comment itself is a reply) --}}
            @if($comment->parent && $comment->parent->comment)
                <div class="mb-2">
                    <a href="javascript:void(0)" class="scroll-to-comment text-muted d-block small"
                       data-target="#comment-{{ $comment->parent->id }}">
                        <i class="feather icon-corner-up-left"></i>
                        <blockquote class="blockquote pl-2 border-left border-gray" style="border-left: 3px solid #ccc;">
                            {{ Str::limit($comment->parent->comment, 120) }}
                        </blockquote>
                    </a>
                </div>
            @endif

            <div class="comment-body mt-1">{{ $comment->comment }}</div>

            <div class="comment-actions mt-2">
                @if(auth()->user()->name == $comment->user_id)
                    <button class="btn btn-sm btn-link text-danger delete-comment" data-id="{{ $comment->id }}">
                        <i class="feather icon-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-link edit-comment" data-id="{{ $comment->id }}" data-body="{{ $comment->comment }}">
                        <i class="feather icon-edit"></i>
                    </button>
                @endif
                <button class="btn btn-sm btn-link reply-comment"
                        data-id="{{ $comment->id }}"
                        data-body="{{ $comment->comment }}">
                    <i class="feather icon-corner-down-right"></i> Antwort
                </button>
            </div>

            {{-- Replies --}}
            @foreach($comment->replies as $reply)
                <div class="reply ml-3 mt-3 p-2 rounded" id="comment-{{ $reply->id }}" style="    background-color: #f9f9f9 !important;">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $reply->user->fullname }}</strong>
                        <small class="text-muted">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                    </div>

                    {{-- Quoted parent comment at the top --}}
                    @if($reply->parent && $reply->parent->comment)
                        <a href="javascript:void(0)" class="scroll-to-comment text-muted d-block small"
                           data-target="#comment-{{ $reply->parent->id }}">
                            <i class="feather icon-corner-up-left"></i>
                            <blockquote class="blockquote pl-2 border-left border-gray mb-2" style="border-left: 3px solid #ccc;">
                                {{ Str::limit($reply->parent->comment, 120) }}
                            </blockquote>
                        </a>
                    @endif

                    <div class="reply-body">{{ $reply->comment }}</div>

                    @if(auth()->user()->name == $reply->user_id)
                        <div class="comment-actions mt-2">
                            <button class="btn btn-sm btn-link text-danger delete-comment" data-id="{{ $reply->id }}">
                                <i class="feather icon-trash"></i>
                            </button>
                            <button class="btn btn-sm btn-link edit-comment" data-id="{{ $reply->id }}" data-body="{{ $reply->comment }}">
                                <i class="feather icon-edit"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach