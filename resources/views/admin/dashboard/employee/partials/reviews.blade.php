{{-- PL-Prüfliste: Montage-Karten (status='reported'), die beim eingeloggten Prüfer liegen.
     Additiv, reine Office-Web-Ansicht. Bestätigen/Zurück-auf-offen nutzen den BESTEHENDEN
     updateStatus-Endpunkt (PATCH /admin/kanban/tasks/{task}/status) — kein neuer Schreibpfad. --}}
<article class="widget col-span-4 row-span-6" data-widget-id="reviews" data-widget-key="reviews"
    data-widget-title="Zu prüfen" data-widget-tags="prüfung montage kanban qualifikation aufgabe">
    <div class="widget-header">
        <div class="widget-title-wrap">
            <span class="widget-icon danger">
                <i data-lucide="shield-check"></i>
            </span>
            <span>
                <span class="widget-title">Zu prüfen</span>
                <span class="widget-subtitle">Vom Monteur gemeldete Montage-Aufgaben, die deine Bestätigung brauchen</span>
            </span>
        </div>
        <div class="widget-tools">
            <span class="pill danger">Zu prüfen ({{ $reviews->count() }})</span>
        </div>
    </div>

    <div class="widget-content">
        <div id="plReviewList" class="pl-review-list" data-status-base="{{ url('/admin/kanban/tasks') }}">
            @forelse ($reviews as $review)
                @php
                    $kunde = trim((optional($review->customer)->name ?? '') . ' ' . (optional($review->customer)->lastname ?? ''));
                    $kunde = $kunde !== '' ? $kunde : (optional($review->customer)->company ?? ('Kunde #' . $review->customer_id));
                    $gewerk = optional($review->product)->article_group ?? optional($review->product)->product ?? '–';
                    $taetigkeit = optional($review->phaseActivity)->title ?? $review->title ?? 'Aufgabe';
                    $melder = $reviewMelder[$review->id] ?? null;
                @endphp
                <div class="pl-review-item" data-review-id="{{ $review->id }}"
                    style="border:1px solid var(--border,#e5e7eb);border-radius:10px;padding:10px 12px;margin-bottom:8px;">
                    <div class="pl-review-title" style="font-weight:600;margin-bottom:4px;">{{ $taetigkeit }}</div>
                    <div class="pl-review-meta" style="font-size:12px;color:#64748b;display:flex;flex-wrap:wrap;gap:4px 12px;margin-bottom:8px;">
                        <span><strong>Kunde:</strong> {{ $kunde }}</span>
                        <span><strong>Gewerk:</strong> {{ $gewerk }}</span>
                        <span><strong>Gemeldet von:</strong> {{ $melder ?: '—' }}@if($review->updated_at) am {{ $review->updated_at->format('d.m.Y H:i') }}@endif</span>
                        @if(is_null($review->reviewer_employee_id))
                            <span class="pill">ohne Prüfer</span>
                        @endif
                    </div>
                    <div class="pl-review-actions" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                        <button type="button" class="pl-review-confirm pill danger" data-id="{{ $review->id }}"
                            style="cursor:pointer;border:none;">Bestätigen</button>
                        <button type="button" class="pl-review-reopen pill" data-id="{{ $review->id }}"
                            style="cursor:pointer;border:none;" title="Karte zurück auf 'offen' — der Monteur erfährt davon nichts">Zurück auf offen</button>
                        <a class="pill" href="{{ route('new.lead.profile', $review->customer_id) }}"
                            style="text-decoration:none;">Im Kundenprofil öffnen</a>
                    </div>
                </div>
            @empty
                <div class="pl-review-empty" style="padding:16px;color:#64748b;text-align:center;">Keine offenen Prüfungen. 👍</div>
            @endforelse
        </div>
    </div>

    <div class="resize-handle"></div>
</article>

@once
    <script>
        (function () {
            const listEl = document.getElementById('plReviewList');
            const base = (listEl && listEl.dataset.statusBase) || '/admin/kanban/tasks';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            async function setStatus(id, status, btn) {
                if (!id) return;
                if (btn) btn.disabled = true;
                try {
                    const res = await fetch(`${base}/${id}/status`, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ status })
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const item = document.querySelector(`.pl-review-item[data-review-id="${id}"]`);
                    if (item) item.remove();
                } catch (e) {
                    if (btn) btn.disabled = false;
                    alert('Aktion fehlgeschlagen: ' + e.message);
                }
            }

            document.addEventListener('click', function (e) {
                const c = e.target.closest('.pl-review-confirm');
                if (c) { setStatus(c.dataset.id, 'done', c); return; }
                const r = e.target.closest('.pl-review-reopen');
                if (r) { setStatus(r.dataset.id, 'open', r); return; }
            });
        })();
    </script>
@endonce
