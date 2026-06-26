<table class="oc-table">
    <thead>
        <tr>
            <th style="width:50px;"></th>
            <th style="width:60px;">#</th>
            <th style="width:90px;">Foto</th>
            <th>Initial</th>
            <th>Artikel-Gruppe</th>
            <th class="text-right">Min-Wert</th>
            <th class="text-right">Max-Wert</th>
            <th style="width:150px;text-align:right;">Aktion</th>
        </tr>
    </thead>
    <tbody>
    @forelse($data as $group)
        <tr class="oc-row-card">
            <td>
                <button type="button" class="oc-btn-ic js-toggle-collapse" data-target="#collapse-group-{{ $group->id }}">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </button>
            </td>
            <td>{{ $group->id }}</td>
            <td>
                <div class="oc-avatar">
                    @if($group->image)
                        <img src="{{ asset('images/articles/'.$group->image) }}" alt="{{ $group->article_group }}">
                    @else
                        {{ mb_strtoupper(mb_substr($group->initial ?: $group->article_group, 0, 2)) }}
                    @endif
                </div>
            </td>
            <td>{{ $group->initial ?: '—' }}</td>
            <td>
                <div style="font-weight:800;">{{ $group->article_group }}</div>
                <div style="font-size:12px;color:#6b7280;">
                    {{ $group->subArticleGroups->count() }} Sub-Artikelgruppen
                </div>
            </td>
            <td class="text-right">
                {{ !is_null($group->min_value) ? number_format($group->min_value, 2, ',', '.') : '–' }}
            </td>
            <td class="text-right">
                {{ !is_null($group->max_value) ? number_format($group->max_value, 2, ',', '.') : '–' }}
            </td>
            <td style="text-align:right;">
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="oc-btn-ic primary" onclick="openModal('editGroupModal-{{ $group->id }}')">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>

                    <a href="{{ route('article_group.destroy', $group->id) }}"
                       class="oc-btn-ic danger js-ajax-delete"
                       data-method="DELETE"
                       data-confirm="Möchten Sie diese Artikel-Gruppe wirklich löschen?">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="8" style="padding:0;border:none;background:transparent;">
                <div class="oc-collapse" id="collapse-group-{{ $group->id }}">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div style="font-size:15px;font-weight:900;">Sub-Artikelgruppen</div>
                            <div style="font-size:12px;color:#6b7280;">
                                <strong>{{ $group->article_group }}</strong> • {{ $group->subArticleGroups->count() }} Einträge
                            </div>
                        </div>
                        <div style="min-width:240px;">
                            <input type="text" class="oc-input js-sub-search" data-target="#sub-table-{{ $group->id }}" placeholder="Innerhalb dieser Gruppe suchen …">
                        </div>
                    </div>

                    <div style="overflow:auto;">
                        <table class="oc-sub-table" id="sub-table-{{ $group->id }}">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th style="width:120px;">Initial</th>
                                    <th>Sub-Artikel</th>
                                    <th style="width:150px;">Wert</th>
                                    <th style="width:120px;">Status</th>
                                    <th style="width:120px;text-align:right;">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($group->subArticleGroups as $sub)
                                    <tr>
                                        <td>{{ $sub->id }}</td>
                                        <td>{{ $sub->initial ?: '—' }}</td>
                                        <td>{{ $sub->sub_article }}</td>
                                        <td>{{ $sub->value ?: '—' }}</td>
                                        <td>{{ $sub->status ?: '—' }}</td>
                                        <td style="text-align:right;">
                                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                                <button type="button" class="oc-btn-ic primary" onclick="openModal('editSubModal-{{ $sub->id }}')">
                                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>

                                                <a href="{{ route('sub_article_group.destroy', $sub->id) }}"
                                                   class="oc-btn-ic danger js-ajax-delete"
                                                   data-method="DELETE"
                                                   data-confirm="Möchten Sie diese Sub-Artikelgruppe wirklich löschen?">
                                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center;color:#6b7280;">Noch keine Sub-Artikelgruppen vorhanden.</td>
                                    </tr>
                                @endforelse

                                <tr class="js-inline-create-row">
                                    <td colspan="6">
                                        <form class="js-ajax-form" method="POST" action="{{ route('sub_article_group.store') }}">
                                            @csrf
                                            <input type="hidden" name="article_group_id" value="{{ $group->id }}">

                                            <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr 1fr auto;gap:10px;align-items:end;">
                                                <div>
                                                    <label class="oc-label">Initial</label>
                                                    <input type="text" class="oc-input" name="initial">
                                                </div>
                                                <div>
                                                    <label class="oc-label">Sub-Artikel</label>
                                                    <input type="text" class="oc-input" name="sub_article" required>
                                                </div>
                                                <div>
                                                    <label class="oc-label">Wert</label>
                                                    <input type="text" class="oc-input" name="value">
                                                </div>
                                                <div>
                                                    <label class="oc-label">Status</label>
                                                    <input type="text" class="oc-input" name="status">
                                                </div>
                                                <div>
                                                    <button type="submit" class="oc-btn">Hinzufügen</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8">
                <div class="oc-empty">Keine Artikel-Gruppen gefunden.</div>
            </td>
        </tr>
    @endforelse
    </tbody>
</table>