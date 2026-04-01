@php
    $employeeId = optional(auth()->user())->employee_id;
@endphp

<span id="stamp-my-count" data-count="{{ $myLists->count() }}" hidden></span>
<span id="stamp-other-count" data-count="{{ $otherLists->count() }}" hidden></span>

{{-- My lists --}}
@foreach($myLists as $list)
    <div class="stamp-folder"
         data-list-id="{{ $list->id }}"
         data-mine="1"
         data-name="{{ $list->name }}"
         data-fullname="{{ $list->name }} ({{ $list->items_count }} Stempel)"
         data-description="{{ $list->description }}"
         data-color="{{ $list->color }}"
         data-shared="{{ $list->is_shared ? 1 : 0 }}">
        <svg class="stamp-folder-icon" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v1H3V4z"
                  fill="{{ $list->color ?: '#ef4444' }}" opacity=".85"/>
            <rect x="2" y="6" width="20" height="12" rx="2" ry="2"
                  fill="#020617" stroke="#fca5a5" stroke-width="0.6"/>
        </svg>
        <div>
            <div class="stamp-folder-title">{{ $list->name }}</div>
            <div class="stamp-folder-meta">
                {{ $list->items_count }} Stempel · zuletzt {{ $list->updated_at->diffForHumans() }}
            </div>
            <div class="stamp-folder-owner">
                Eigene Liste
                @if($list->is_shared)
                    · <i class="feather icon-users"></i> geteilt
                @endif
            </div>
        </div>
        <div class="stamp-folder-actions">
            <button type="button" class="btn btn-outline-light stamp-folder-edit" title="Bearbeiten">
                <i class="feather icon-edit-2"></i>
            </button>
            <button type="button" class="btn btn-outline-light stamp-folder-delete" title="Löschen">
                <i class="feather icon-trash-2"></i>
            </button>
        </div>
    </div>
@endforeach

{{-- Shared lists --}}
@foreach($otherLists as $list)
    <div class="stamp-folder"
         data-list-id="{{ $list->id }}"
         data-mine="0"
         data-name="{{ $list->name }}"
         data-fullname="{{ $list->name }} ({{ $list->items_count }} Stempel)"
         data-description="{{ $list->description }}"
         data-color="{{ $list->color }}"
         data-shared="{{ $list->is_shared ? 1 : 0 }}">
        <svg class="stamp-folder-icon" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v1H3V4z"
                  fill="{{ $list->color ?: '#fb7185' }}" opacity=".85"/>
            <rect x="2" y="6" width="20" height="12" rx="2" ry="2"
                  fill="#020617" stroke="#fecaca" stroke-width="0.6"/>
        </svg>
        <div>
            <div class="stamp-folder-title">{{ $list->name }}</div>
            <div class="stamp-folder-meta">
                {{ $list->items_count }} Stempel · zuletzt {{ $list->updated_at->diffForHumans() }}
            </div>
            <div class="stamp-folder-owner">
                von {{ optional($list->owner)->name ?? 'Mitarbeiter' }}
            </div>
        </div>
        <div class="stamp-folder-actions">
            {{-- keine Edit/Delete für fremde Ordner --}}
        </div>
    </div>
@endforeach

@if(!$myLists->count() && !$otherLists->count())
    <div class="text-center text-muted" style="font-size:.8rem; padding:.7rem 0;">
        Noch keine Stempel-Ordner angelegt.
    </div>
@endif
