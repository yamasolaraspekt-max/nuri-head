<div class="xcard p-2" style="background:rgba(255,255,255,.70)">
    <div style="display:grid; grid-template-columns: 340px 1fr; gap:14px;">
        <!-- LEFT: SIDEBAR -->
        <div class="xcard p-2" style="position:sticky; top:10px; align-self:start;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                <div style="font-weight:900;">Alle Positionen</div>
                <div style="color:#64748b; font-weight:700; font-size:12px;">
                    {{ $positions->count() }}
                </div>
            </div>

            <div class="mt-1">
                <input id="qual-sidebar-q" class="xfield" placeholder="Suchen..." value="{{ e($q ?? '') }}">
            </div>

            <div class="mt-1" style="color:#64748b; font-weight:600; font-size:12px;">
                Ziehen & Ablegen → setzt Qualifikation + Preis
            </div>

            <div id="sidebar-positions"
                 class="droplist"
                 style="min-height:420px; max-height:62vh; overflow:auto; background:rgba(207,224,155,.20);">
                @forelse($positions as $p)
                    <div class="drag-item" data-pos-id="{{ $p->id }}" style="cursor:grab;">
                        <div style="min-width:0;">
                            <div style="font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $p->position }}
                            </div>
                            <div style="font-size:12px; color:#64748b; font-weight:700;">
                                @if($p->qualification)
                                    {{ $p->qualification }} • {{ $p->price !== null ? number_format($p->price,2,',','.') . ' €' : '-' }}
                                @else
                                    Keine Qualifikation
                                @endif
                                • {{ $p->status === 'Published' ? 'Aktiv' : 'Inaktiv' }}
                            </div>
                        </div>
                        <div style="font-weight:900; color:#74b2d4;">⇢</div>
                    </div>
                @empty
                    <div style="color:#64748b; font-weight:700;">Keine Positionen gefunden.</div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: QUALIFICATION CARDS -->
        <div>
            <div class="d-flex align-items-center justify-content-between mb-2" style="display:flex; gap:10px;">
                <div style="color:var(--muted); font-weight:700;">
                    Qualifikationen
                </div>
                <button id="btn-new-qual" class="xbtn xbtn-primary" type="button">+ Qualifikation</button>
            </div>

            <div id="qual-cards" class="board">
                @foreach($quals as $q)
                    <div class="xcard p-1" data-qcard="{{ $q->id }}">
                        <div style="padding:10px 12px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <div>
                                <div style="font-weight:900; font-size:16px;">{{ $q->name }}</div>
                                <div style="color:#64748b; font-weight:800;">
                                    Standard: {{ number_format($q->default_price,2,',','.') }} €
                                    • {{ $q->positions->count() }} Positionen
                                </div>
                            </div>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <button type="button" class="btn btn-sm" data-qhandle
                                        style="border-radius:10px; background:rgba(15,23,42,.06);">↕</button>

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

                        <div class="droplist"
                             data-qual-drop
                             data-qual-id="{{ $q->id }}">
                            @forelse($q->positions as $p)
                                <div class="drag-item" data-pos-id="{{ $p->id }}">
                                    <div style="min-width:0;">
                                        <div style="font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $p->position }}
                                        </div>
                                        <div style="font-size:12px; color:#64748b; font-weight:800;">
                                            {{ $p->price !== null ? number_format($p->price,2,',','.') . ' €' : '-' }}
                                            • {{ $p->status === 'Published' ? 'Aktiv' : 'Inaktiv' }}
                                        </div>
                                    </div>
                                    <div style="font-weight:900; color:#93c21c;">✔</div>
                                </div>
                            @empty
                                <div style="color:#64748b; font-weight:700;">Positionen hierher ziehen…</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
