{{-- F3: "Meine Follow-ups" — offene Follow-up-personal_tasks, die beim eingeloggten
     Verantwortlichen liegen. Eigenes Widget neben "Zu prüfen" (partials/reviews) im
     Container "Mein Bereich" (#view-personal) = die 2 Sektionen aus Entscheidung 4.
     Aktionen laufen über den streng gescopten FollowUpController (nur eigenes Follow-up). --}}
@php $today = now()->toDateString(); @endphp
<article class="widget col-span-4 row-span-6" data-widget-id="myFollowups" data-widget-key="myFollowups"
    data-widget-title="Meine Follow-ups" data-widget-tags="follow-up wiedervorlage nachfass aufgabe">
    <div class="widget-header">
        <div class="widget-title-wrap">
            <span class="widget-icon danger">
                <i data-lucide="list-checks"></i>
            </span>
            <span>
                <span class="widget-title">Meine Follow-ups</span>
                <span class="widget-subtitle">Deine offenen Nachverfolgungen – nach Fälligkeit, überfällige zuerst</span>
            </span>
        </div>
        <div class="widget-tools">
            <span class="pill danger">Offen ({{ $myFollowups->count() }})</span>
        </div>
    </div>

    <div class="widget-content">
        <div id="myFollowupsList" class="pl-review-list" data-action-base="{{ url('/followups') }}">
            @forelse ($myFollowups as $fu)
                @php
                    $kunde = trim(($fu->customer_name ?? '') . ' ' . ($fu->customer_lastname ?? ''));
                    $kunde = $kunde !== '' ? $kunde : ($fu->customer_company ?? ('Kunde #' . $fu->customer_id));
                    $gewerk = $fu->product_name ?? '–';
                    $artLabel = ($fu->follow_up_art === 'wiederaufnahme') ? 'Wiederaufnahme' : 'Nachfass';
                    $overdue = $fu->due_date && $fu->due_date < $today;
                @endphp
                <div class="pl-review-item" data-followup-id="{{ $fu->id }}"
                    style="border:1px solid var(--border,#e5e7eb);border-radius:10px;padding:10px 12px;margin-bottom:8px;">
                    <div class="pl-review-title" style="font-weight:600;margin-bottom:4px;">{{ $fu->task_title }}</div>
                    <div class="pl-review-meta" style="font-size:12px;color:#64748b;display:flex;flex-wrap:wrap;gap:4px 12px;margin-bottom:8px;">
                        <span><strong>Kunde:</strong> {{ $kunde }}</span>
                        <span><strong>Gewerk:</strong> {{ $gewerk }}</span>
                        <span class="fu-due" @if($overdue) style="color:#dc2626;font-weight:600;" @endif>
                            <strong>Fällig:</strong> {{ $fu->due_date ? \Illuminate\Support\Carbon::parse($fu->due_date)->format('d.m.Y') : '—' }}@if($overdue) (überfällig)@endif
                        </span>
                        <span class="pill">{{ $artLabel }}</span>
                    </div>
                    <div class="pl-review-actions" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                        <button type="button" class="fu-complete pill danger" data-id="{{ $fu->id }}"
                            style="cursor:pointer;border:none;">Erledigt</button>
                        <button type="button" class="fu-snooze pill" data-id="{{ $fu->id }}"
                            style="cursor:pointer;border:none;" title="3 Tage später">Verschieben (+3 Tage)</button>
                        <a class="pill" href="{{ route('new.lead.profile', $fu->customer_id) }}"
                            style="text-decoration:none;">Zum Kunden</a>
                    </div>
                </div>
            @empty
                <div class="pl-review-empty" style="padding:16px;color:#64748b;text-align:center;">Keine offenen Follow-ups. 👍</div>
            @endforelse
        </div>
    </div>

    <div class="resize-handle"></div>
</article>

@once
    <script>
        (function () {
            const listEl = document.getElementById('myFollowupsList');
            const base = (listEl && listEl.dataset.actionBase) || '/followups';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            async function post(id, action, btn) {
                if (!id) return;
                if (btn) btn.disabled = true;
                try {
                    const res = await fetch(`${base}/${id}/${action}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return await res.json();
                } catch (e) {
                    if (btn) btn.disabled = false;
                    alert('Aktion fehlgeschlagen: ' + e.message);
                    return null;
                }
            }

            document.addEventListener('click', async function (e) {
                const c = e.target.closest('.fu-complete');
                if (c) {
                    const data = await post(c.dataset.id, 'complete', c);
                    if (data && data.success) {
                        const item = document.querySelector(`.pl-review-item[data-followup-id="${c.dataset.id}"]`);
                        if (item) item.remove();
                    }
                    return;
                }
                const s = e.target.closest('.fu-snooze');
                if (s) {
                    const data = await post(s.dataset.id, 'snooze', s);
                    if (data && data.success) {
                        const item = document.querySelector(`.pl-review-item[data-followup-id="${s.dataset.id}"]`);
                        const due = item && item.querySelector('.fu-due');
                        if (due && data.due_date) {
                            const d = data.due_date.split('-');
                            due.style.color = '';
                            due.style.fontWeight = '';
                            due.innerHTML = '<strong>Fällig:</strong> ' + (d.length === 3 ? `${d[2]}.${d[1]}.${d[0]}` : data.due_date);
                        }
                        if (s) s.disabled = false;
                    }
                    return;
                }
            });
        })();
    </script>
@endonce
