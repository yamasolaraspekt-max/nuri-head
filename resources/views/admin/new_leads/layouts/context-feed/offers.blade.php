@php
    $ctx = $ctx ?? [];
    $offerFolders = $offerFolders ?? collect();
@endphp

@if($offerFolders->count())
    @foreach($offerFolders as $folder)
        @php
            $offer = $folder->offer ?? $folder->latestOffer ?? null;
            $comments = $folder->comments ?? collect();
            $creator = $folder->creator ?? $folder->employee ?? null;
            $product = $folder->product ?? null;
        @endphp

        <div class="ma-feed-card" data-offer-folder-id="{{ $folder->id }}">
            <button type="button" class="ma-feed-head" data-feed-collapse>
                <span class="ma-note-type-icon bg-orange">
                    <i data-feather="folder"></i>
                </span>

                <span class="flex-grow-1">
                    <span class="ma-feed-title">
                        {{ $folder->name ?? $folder->title ?? 'Offer Folder #' . $folder->id }}
                    </span>

                    <span class="ma-feed-meta d-block">
                        {{ optional($folder->created_at)->format('d.m.Y H:i') }}
                        @if($product)
                            · {{ $product->article_group }}
                        @endif
                    </span>

                    @if($offer)
                        <span class="ma-feed-preview d-block">
                            Angebot: {{ $offer->offer_no ?? $offer->offer_number ?? '#' . $offer->id }}
                        </span>
                    @endif
                </span>

                <i data-feather="chevron-down"></i>
            </button>

            <div class="ma-feed-body">
                <div class="d-flex align-items-center mb-2">
                    @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                        'employee' => $creator,
                        'size' => 28,
                    ])

                    <div class="ml-2">
                        <div class="ma-feed-title">
                            Erstellt von:
                            {{ $creator ? trim($creator->name . ' ' . $creator->lastname) : '-' }}
                        </div>

                        <div class="ma-feed-meta">
                            {{ optional($folder->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded p-2 mb-2">
                    <div class="ma-feed-mini-row">
                        <span><i data-feather="folder"></i> Folder ID</span>
                        <small>#{{ $folder->id }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="tag"></i> Status</span>
                        <small>{{ $folder->status ?? '-' }}</small>
                    </div>

                    @if($offer)
                        <div class="ma-feed-mini-row">
                            <span><i data-feather="file-text"></i> Angebot</span>
                            <small>{{ $offer->offer_no ?? $offer->offer_number ?? '#' . $offer->id }}</small>
                        </div>
                    @endif
                </div>

                @if($folder->description ?? false)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-1">Beschreibung</div>
                        <div class="ma-feed-content">
                            {!! $folder->description !!}
                        </div>
                    </div>
                @endif

                <div class="ma-feed-title mb-2">Kommentare / Notizen</div>

                @forelse($comments as $comment)
                    <div class="ma-feed-comment">
                        <div class="d-flex justify-content-between">
                            <strong class="ma-feed-author">
                                {{ optional($comment->employee ?? $comment->author ?? $comment->creator ?? null)->name }}
                                {{ optional($comment->employee ?? $comment->author ?? $comment->creator ?? null)->lastname }}
                            </strong>

                            <small class="ma-feed-meta">
                                {{ optional($comment->created_at)->format('d.m.Y H:i') }}
                            </small>
                        </div>

                        <div class="ma-feed-content mt-1">
                            {!! $comment->comment ?? $comment->description ?? $comment->note ?? '' !!}
                        </div>
                    </div>
                @empty
                    <div class="ma-feed-empty mb-2">
                        Keine Angebotskommentare vorhanden.
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
@else
    @include('admin.new_leads.layouts.context-feed.empty', [
        'title' => 'Keine Angebote',
        'message' => 'Keine Offer Folder oder Angebote für diesen Kundenbereich gefunden.',
        'icon' => 'folder',
    ])
@endif