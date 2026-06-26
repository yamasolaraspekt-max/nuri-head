@php
    $ctx = $ctx ?? [];
    $dealNotes = $dealNotes ?? collect();
@endphp

@if($deals->count())
    @foreach($deals as $deal)
        @php
            $author = $deal->author ?? null;
            $product = $deal->product ?? null;
            $folder = $deal->folder ?? null;
            $notes = $dealNotes->get($deal->id, collect());
            $latestMeasurement = $deal->latestMeasurement ?? null;
            $latestDeliveryNote = $deal->latestDeliveryNote ?? null;
        @endphp

        <div class="ma-feed-card" data-deal-id="{{ $deal->id }}">
            <button type="button" class="ma-feed-head" data-feed-collapse>
                <span class="ma-note-type-icon bg-blue">
                    <i data-feather="package"></i>
                </span>

                <span class="flex-grow-1">
                    <span class="ma-feed-title">
                        {{ $deal->order_number ?: 'Auftrag #' . $deal->id }}
                    </span>

                    <span class="ma-feed-meta d-block">
                        Status: {{ $deal->deal_status ?: $deal->status ?: '-' }}
                        @if($product)
                            · {{ $product->article_group }}
                        @endif
                    </span>

                    @if($deal->info)
                        <span class="ma-feed-preview d-block">
                            {{ \Illuminate\Support\Str::limit(strip_tags($deal->info), 90) }}
                        </span>
                    @endif
                </span>

                <i data-feather="chevron-down"></i>
            </button>

            <div class="ma-feed-body">
                <div class="d-flex align-items-center mb-2">
                    @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                        'employee' => $author,
                        'size' => 28,
                    ])

                    <div class="ml-2">
                        <div class="ma-feed-title">
                            Auftrag erstellt von:
                            {{ $author ? trim($author->name . ' ' . $author->lastname) : '-' }}
                        </div>
                        <div class="ma-feed-meta">
                            {{ optional($deal->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded p-2 mb-2">
                    <div class="ma-feed-mini-row">
                        <span><i data-feather="hash"></i> Angebotsnummer</span>
                        <small>{{ $deal->offer_number ?: '-' }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="folder"></i> Offer Folder</span>
                        <small>{{ $folder->name ?? $folder->title ?? $deal->offer_folder_id ?? '-' }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="euro"></i> Preis</span>
                        <small>{{ $deal->price ? number_format((float) $deal->price, 2, ',', '.') . ' €' : '-' }}</small>
                    </div>

                    <div class="ma-feed-mini-row">
                        <span><i data-feather="calendar"></i> Signaturdatum</span>
                        <small>{{ optional($deal->sign_date)->format('d.m.Y') ?: '-' }}</small>
                    </div>
                </div>

                @if($deal->info)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-1">Auftragsinfo</div>
                        <div class="ma-feed-content">
                            {!! $deal->info !!}
                        </div>
                    </div>
                @endif

                @if($latestMeasurement || $latestDeliveryNote)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-2">Aktueller Stand</div>

                        @if($latestMeasurement)
                            <div class="ma-feed-mini-row">
                                <span><i data-feather="clipboard"></i> Letztes Aufmaß</span>
                                <small>{{ optional($latestMeasurement->created_at)->format('d.m.Y H:i') }}</small>
                            </div>
                        @endif

                        @if($latestDeliveryNote)
                            <div class="ma-feed-mini-row">
                                <span><i data-feather="truck"></i> Letzter Lieferschein</span>
                                <small>{{ optional($latestDeliveryNote->created_at)->format('d.m.Y H:i') }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="ma-feed-title mb-2">Auftragsnotizen</div>

                @forelse($notes as $note)
                    <div class="ma-feed-comment">
                        <div class="d-flex justify-content-between">
                            <strong class="ma-feed-author">
                                Mitarbeiter #{{ $note->created_by }}
                            </strong>

                            <small class="ma-feed-meta">
                                {{ optional($note->created_at)->format('d.m.Y H:i') }}
                            </small>
                        </div>

                        <div class="ma-feed-content mt-1">
                            {!! $note->description !!}
                        </div>

                        @if($note->children && $note->children->count())
                            <div class="ma-feed-replies">
                                @foreach($note->children as $child)
                                    <div class="ma-feed-comment is-reply">
                                        <div class="d-flex justify-content-between">
                                            <strong class="ma-feed-author">
                                                Mitarbeiter #{{ $child->created_by }}
                                            </strong>

                                            <small class="ma-feed-meta">
                                                {{ optional($child->created_at)->format('d.m.Y H:i') }}
                                            </small>
                                        </div>

                                        <div class="ma-feed-content mt-1">
                                            {!! $child->description !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="ma-feed-empty mb-2">
                        Keine Auftragsnotizen vorhanden.
                    </div>
                @endforelse

                <form class="ma-context-form mt-2" data-context-post="{{ route('customer.context-feed.deal.note', $deal->id) }}">
                    @csrf

                    <textarea name="description" class="form-control form-control-sm mb-2" rows="2" placeholder="Neue Auftragsnotiz schreiben..."></textarea>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i data-feather="plus"></i>
                        Notiz speichern
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@else
    @include('admin.new_leads.layouts.context-feed.empty', [
        'title' => 'Keine Aufträge',
        'message' => 'Keine Aufträge für diesen Kundenbereich gefunden.',
        'icon' => 'package',
    ])
@endif