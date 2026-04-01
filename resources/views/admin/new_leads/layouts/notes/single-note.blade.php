{{-- Main Note Card --}}
<div class="card note-card border-0 mb-0" data-id="{{ $note->id }}">
    <div class="card-body py-0 px-0 position-relative">

        {{-- Header --}}
        <div class="d-flex align-items-start mb-2">
            <img src="{{ asset('images/employee/' . $note->creator->image) }}"
                 class="rounded-circle mr-1 ml-1"
                 width="30" height="30">

            <div class="flex-grow-1 bg-white rounded p-1 position-relative">

                {{-- main note action icons (top right) --}}
                <div class="note-actions-main btn-group btn-group-sm position-absolute">
                    <button type="button"
                            class="btn btn-icon btn-icon rounded-circle btn-flat-primary waves-effect waves-light"
                            onclick="editNote({{ $note->id }})">
                        <i class="feather icon-edit"></i>
                    </button>

                    <button type="button"
                            class="btn btn-icon btn-icon rounded-circle btn-flat-danger waves-effect waves-light"
                            onclick="deleteNote({{ $note->id }})">
                        <i class="feather icon-trash-2"></i>
                    </button>

                    <button type="button"
                            class="btn btn-icon btn-icon rounded-circle btn-flat-secondary waves-effect waves-light"
                            onclick="openDeletedNotesModal({{ $note->id }})">
                        <i class="feather icon-archive"></i>
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-1 pr-5">
                    <strong class="text-primary">{{ $note->creator->name }}</strong>
                    <div class="title_clock d-flex">
                        <small class="text-muted mr-2">
                            {{ $note->created_at->format('d.m.y // H:i') }}
                        </small>
                    </div>
                </div>

                <div class="small text-body note-description note-content">
                    {!! $note->description !!}
                </div>
            </div>
        </div>

        {{-- Replies INSIDE the main card --}}
        <div class="reply-wrapper">
            @foreach ($note->replies as $reply)
                <div class="d-flex align-items-start mb-2 ml-5 reply-item" data-id="{{ $reply->id }}">
                    <img src="{{ asset('images/employee/' . $reply->creator->image) }}"
                         class="rounded-circle mr-2"
                         width="32" height="32">

                    <div class="flex-grow-1 bg-white rounded p-1 position-relative">

                        {{-- reply action icons (top right) --}}
                        @if (auth()->user()->name == $reply->creator->id)
                            <div class="reply-actions btn-group btn-group-sm position-absolute">
                                <button class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 waves-effect waves-light"
                                        onclick="editReply({{ $reply->id }})">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-icon btn-icon rounded-circle btn-flat-danger waves-effect waves-light"
                                        onclick="deleteReply({{ $reply->id }})">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pr-5">
                            <strong class="text-primary">
                                {{ $reply->creator->name }} {{ $reply->creator->lastname }}
                            </strong>
                            <small class="text-muted">
                                {{ $reply->created_at->format('d.m.y // H:i') }}
                            </small>
                        </div>

                        <div class="small rounded p-2 mt-1 text-dark reply-text note-content">
                            {{ $reply->description }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Inline Reply Input --}}
        <div class="ml-5 mt-0">
            <div class="input-group input-group">
                <input type="text"
                       class="form-control"
                       placeholder="Antwort schreiben..."
                       onkeydown="if(event.key === 'Enter') postReply({{ $note->id }}, this)">
                <div class="input-group-append">
                    <button type="button"
                            class="btn btn-icon btn-icon btn-primary waves-effect waves-light"
                            onclick="postReply({{ $note->id }}, this.closest('.input-group').querySelector('input'))">
                        <i class="fa fa-reply"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
