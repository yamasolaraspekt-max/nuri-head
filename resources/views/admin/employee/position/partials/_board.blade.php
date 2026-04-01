<div id="qual-cards" class="board">
@foreach($quals as $q)
    <div class="xcard p-1" data-qcard="{{ $q->id }}">
        <div class="d-flex align-items-center justify-content-between" style="padding:10px 12px;">
            <div>
                <div style="font-weight:900; font-size:16px;">{{ $q->name }}</div>
                <div style="color:#64748b; font-weight:700;">
                    Standard: {{ number_format($q->default_price,2,',','.') }} €
                    • Positionen: {{ $q->positions->count() }}
                </div>
            </div>
            <div class="d-flex" style="gap:8px; align-items:center;">
                <button type="button" class="btn btn-sm" data-qhandle style="border-radius:10px; background:rgba(15,23,42,.06);">↕</button>

                <button type="button" class="btn btn-sm"
                    data-qedit="{{ $q->id }}"
                    data-name="{{ e($q->name) }}"
                    data-price="{{ $q->default_price }}"
                    style="border-radius:10px; background:rgba(116,178,212,.18);">
                    Edit
                </button>

                <button type="button" class="btn btn-sm"
                    data-qdelete="{{ $q->id }}"
                    style="border-radius:10px; background:rgba(239,68,68,.16);">
                    Del
                </button>
            </div>
        </div>

        <div class="droplist" data-droplist data-qual-id="{{ $q->id }}">
            @forelse($q->positions as $p)
                <div class="drag-item" data-pos-id="{{ $p->id }}">
                    <div style="min-width:0;">
                        <div style="font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $p->position }}
                        </div>
                        <div style="color:#64748b; font-weight:700; font-size:12px;">
                            {{ $p->price !== null ? number_format($p->price,2,',','.') . ' €' : '-' }}
                            • {{ $p->status==='Published' ? 'Aktiv' : 'Inaktiv' }}
                        </div>
                    </div>
                    <div style="font-weight:900; color:#93c21c;">⇄</div>
                </div>
            @empty
                <div style="color:#64748b; font-weight:700;">Positionen hierher ziehen…</div>
            @endforelse
        </div>
    </div>
@endforeach
</div>
