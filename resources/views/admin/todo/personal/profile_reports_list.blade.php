@php
    $items = $grouped[$parentId] ?? collect();
@endphp

@foreach($items as $comment)
    <div class="tp-comment">
        <div class="tp-comment-header">
            <div class="tp-avatar-ring">
                @if($comment->employee && $comment->employee->image)
                    <img src="{{ asset('images/employee/'.$comment->employee->image) }}"
                         style="width:100%;height:100%;object-fit:cover;">
                @else
                    @if($comment->employee)
                        {{ mb_substr($comment->employee->name,0,1) }}{{ mb_substr($comment->employee->lastname,0,1) }}
                    @else
                        ?
                    @endif
                @endif
            </div>
            <div>
                <div class="tp-comment-name">
                    {{ $comment->employee->name ?? 'Unbekannt' }}
                    {{ $comment->employee->lastname ?? '' }}
                </div>
                <div class="tp-comment-time">
                    {{ $comment->created_at->format('d.m.Y H:i') }}
                </div>
            </div>
        </div>
        <div class="tp-comment-body">
            {{ $comment->comment }}
        </div>
        <div class="tp-comment-actions">
            <span class="js-reply-toggle" style="text-decoration:underline;">Antworten</span>
        </div>
        <form class="tp-reply-form" data-comment-id="{{ $comment->id }}" style="display:none;margin-top:.35rem;">
            @csrf
            <div class="form-group mb-25">
                <textarea name="comment"
                          class="form-control"
                          rows="2"
                          placeholder="Antwort schreiben..."></textarea>
            </div>
            <button type="button"
                    class="btn btn-xs btn-outline-primary js-reply-send">
                Senden
            </button>
        </form>

        {{-- Nested replies --}}
        <div class="tp-comment-replies">
            @include('admin.todo.personal.profile_reports_list', [
                'grouped'  => $grouped,
                'parentId' => $comment->id,
            ])
        </div>
    </div>
@endforeach
