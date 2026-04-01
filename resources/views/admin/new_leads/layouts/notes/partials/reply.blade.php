<div class="d-flex align-items-start mb-2 ml-5 reply-item position-relative" data-id="{{ $reply->id }}">
    <img src="{{ asset('images/employee/' . $reply->creator->image) }}" class="rounded-circle mr-2" width="32" height="32">

    <div class="flex-grow-1 bg-white rounded p-2 position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <strong class="text-primary">{{ $reply->creator->name }} {{ $reply->creator->lastname }}</strong>
            <small class="text-muted">{{ $reply->created_at->format('d.m.y // H:i') }}</small>
        </div>

        <div class="small rounded mt-2 text-dark reply-text">
            {{ $reply->description }}
        </div>

        {{-- Show edit/delete if user is owner --}}
        @if (auth()->user()->name == $reply->created_by)
            <div class="position-absolute d-flex gap-2" style="bottom: 0.25rem; right: 0.5rem;">
                <a href="#" class="text-muted me-2" onclick="editReply({{ $reply->id }})" title="Bearbeiten">
                    <i class="feather icon-edit"></i>
                </a>
                <a href="#" class="text-danger" onclick="deleteReply({{ $reply->id }})" title="Löschen">
                    <i class="feather icon-trash"></i>
                </a>
            </div>
        @endif
    </div>
</div>
