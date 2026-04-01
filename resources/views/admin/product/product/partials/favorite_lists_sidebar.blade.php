@php
    $employeeId = optional(auth()->user())->name;
@endphp

{{-- My lists --}}
@foreach($myLists as $list)
    <div class="fav-folder"
         data-list-id="{{ $list->id }}"
         data-mine="1"
         data-name="{{ $list->name }}"
         data-fullname="{{ $list->name }} ({{ $list->items_count }} Produkte)"
         data-description="{{ $list->description }}"
         data-color="{{ $list->color }}"
         data-shared="{{ $list->is_shared ? 1 : 0 }}">
        <svg class="fav-folder-icon" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v1H3V4z"
                  fill="{{ $list->color ?: '#93c21c' }}" opacity=".85"/>
            <rect x="2" y="6" width="20" height="12" rx="2" ry="2"
                  fill="#0f172a" stroke="#64748b" stroke-width="0.6"/>
        </svg>
        <div>
            <div class="fav-folder-title">{{ $list->name }}</div>
            <div class="fav-folder-meta">
                {{ $list->items_count }} Produkte · zuletzt {{ $list->updated_at->diffForHumans() }}
            </div>
            <div class="fav-folder-owner">
                Eigene Liste
                @if($list->is_shared)
                    · <i class="feather icon-users"></i> geteilt
                @endif
            </div>
        </div>
        <div class="fav-folder-actions">
            <button type="button" class="btn btn-outline-light fav-folder-edit" title="Bearbeiten">
                <i class="feather icon-edit-2"></i>
            </button>
            <button type="button" class="btn btn-outline-light fav-folder-delete" title="Löschen">
                <i class="feather icon-trash-2"></i>
            </button>
        </div>
    </div>
@endforeach

{{-- Other shared lists --}}
@foreach($otherLists as $list)
    <div class="fav-folder"
         data-list-id="{{ $list->id }}"
         data-mine="0"
         data-name="{{ $list->name }}"
         data-fullname="{{ $list->name }} ({{ $list->items_count }} Produkte)"
         data-description="{{ $list->description }}"
         data-color="{{ $list->color }}"
         data-shared="{{ $list->is_shared ? 1 : 0 }}">
        <svg class="fav-folder-icon" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v1H3V4z"
                  fill="{{ $list->color ?: '#38bdf8' }}" opacity=".85"/>
            <rect x="2" y="6" width="20" height="12" rx="2" ry="2"
                  fill="#020617" stroke="#64748b" stroke-width="0.6"/>
        </svg>
        <div>
            <div class="fav-folder-title">{{ $list->name }}</div>
            <div class="fav-folder-meta">
                {{ $list->items_count }} Produkte · zuletzt {{ $list->updated_at->diffForHumans() }}
            </div>
            <div class="fav-folder-owner">
                von {{ optional($list->owner)->name ?? 'Mitarbeiter' }}
            </div>
        </div>
        <div class="fav-folder-actions">
            {{-- keine Buttons für fremde Ordner --}}
        </div>
    </div>
@endforeach

@if(!$myLists->count() && !$otherLists->count())
    <div class="text-center text-muted" style="font-size:.8rem; padding:.7rem 0;">
        Noch keine Favoritenlisten angelegt.
    </div>
@endif
