{{-- resources/views/admin/employee/position/partials/_hierarchy_matrix.blade.php --}}
@php
    $fmt = fn($n) => number_format((float) $n, 2, ',', '.');
@endphp

<style>
    .qh-wrap {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 14px;
        align-items: start;
    }

    @media (max-width: 1100px) {
        .qh-wrap {
            grid-template-columns: 1fr;
        }
    }

    .qh-help {
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(116,178,212,.14), rgba(255,255,255,1));
        padding: 14px;
        position: sticky;
        top: 12px;
    }

    .qh-help-title {
        font-weight: 1100;
        color: var(--ink);
        font-size: 16px;
        margin-bottom: 8px;
    }

    .qh-help-text {
        color: var(--muted);
        font-weight: 850;
        font-size: 13px;
        line-height: 1.55;
    }

    .qh-legend {
        display: grid;
        gap: 8px;
        margin-top: 14px;
    }

    .qh-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 950;
        color: var(--ink);
    }

    .qh-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        display: inline-block;
    }

    .qh-dot.green { background: #93c21c; }
    .qh-dot.red { background: #ef4444; }
    .qh-dot.blue { background: #74b2d4; }

    .qh-main {
        display: grid;
        gap: 12px;
    }

    .qh-toolbar {
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 18px;
        background: #fff;
        padding: 12px;
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 10px;
        align-items: center;
    }

    @media (max-width: 850px) {
        .qh-toolbar {
            grid-template-columns: 1fr;
        }
    }

    .qh-search {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(15,23,42,.12);
        padding: 11px 13px;
        font-weight: 900;
    }

    .qh-btn {
        border-radius: 12px;
        border: 1px solid rgba(15,23,42,.10);
        background: rgba(116,178,212,.14);
        padding: 10px 13px;
        font-weight: 1100;
        cursor: pointer;
        white-space: nowrap;
    }

    .qh-btn:hover {
        background: rgba(116,178,212,.22);
    }

    .qh-card {
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15,23,42,.06);
        overflow: hidden;
    }

    .qh-card-head {
        padding: 14px 16px;
        background: linear-gradient(180deg, rgba(147,194,28,.14), rgba(255,255,255,1));
        border-bottom: 1px solid rgba(15,23,42,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
    }

    .qh-title {
        font-weight: 1100;
        color: var(--ink);
        font-size: 17px;
    }

    .qh-sub {
        color: var(--muted);
        font-weight: 900;
        font-size: 12px;
        margin-top: 3px;
    }

    .qh-summary {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .qh-pill {
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 1100;
        border: 1px solid rgba(15,23,42,.08);
        background: rgba(15,23,42,.04);
        color: var(--ink);
        white-space: nowrap;
    }

    .qh-pill.green {
        background: rgba(147,194,28,.18);
        border-color: rgba(147,194,28,.26);
        color: #365314;
    }

    .qh-pill.blue {
        background: rgba(116,178,212,.16);
        border-color: rgba(116,178,212,.25);
        color: #0f172a;
    }

    .qh-card-body {
        padding: 14px;
        display: none;
    }

    .qh-card.is-open .qh-card-body {
        display: block;
    }

    .qh-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    @media (max-width: 850px) {
        .qh-grid {
            grid-template-columns: 1fr;
        }
    }

    .qh-work {
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 16px;
        background: rgba(248,250,252,.88);
        padding: 12px;
        transition: .12s ease;
    }

    .qh-work.is-allowed {
        background: rgba(147,194,28,.12);
        border-color: rgba(147,194,28,.28);
    }

    .qh-work.is-denied {
        background: rgba(239,68,68,.06);
        border-color: rgba(239,68,68,.14);
    }

    .qh-work.is-self {
        background: rgba(116,178,212,.14);
        border-color: rgba(116,178,212,.28);
    }

    .qh-work-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .qh-work-name {
        font-weight: 1100;
        color: var(--ink);
    }

    .qh-work-small {
        font-size: 11px;
        color: var(--muted);
        font-weight: 900;
        margin-top: 2px;
    }

    .qh-switch {
        position: relative;
        width: 48px;
        height: 26px;
        flex: 0 0 auto;
    }

    .qh-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .qh-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        border-radius: 999px;
        transition: .15s ease;
    }

    .qh-slider:before {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .15s ease;
        box-shadow: 0 2px 6px rgba(15,23,42,.25);
    }

    .qh-switch input:checked + .qh-slider {
        background: #93c21c;
    }

    .qh-switch input:checked + .qh-slider:before {
        transform: translateX(22px);
    }

    .qh-switch input:disabled + .qh-slider {
        background: #74b2d4;
        cursor: not-allowed;
    }

    .qh-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .qh-field label {
        display: block;
        font-size: 10px;
        font-weight: 1100;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .qh-field input {
        width: 100%;
        border-radius: 11px;
        border: 1px solid rgba(15,23,42,.12);
        padding: 8px 9px;
        font-weight: 1000;
        background: #fff;
    }

    .qh-field input[readonly] {
        background: rgba(15,23,42,.04);
    }

    .qh-empty {
        padding: 18px;
        border-radius: 16px;
        background: rgba(248,250,252,.9);
        color: var(--muted);
        font-weight: 900;
        text-align: center;
    }

    .qh-hidden {
        display: none !important;
    }
</style>

<div class="qh-wrap">
    <aside class="qh-help">
        <div class="qh-help-title">So funktioniert die Job-Hierarchie</div>

        <div class="qh-help-text">
            Jede Karte ist eine Mitarbeiter-Qualifikation. In der Karte wählst du aus,
            welche Arbeiten diese Qualifikation ausführen darf.
            <br><br>
            Beispiel: Bei <strong>Meister</strong> aktivierst du <strong>Geselle</strong>,
            <strong>Helfer</strong> und <strong>Azubi</strong>.
        </div>

        <div class="qh-legend">
            <div class="qh-legend-item">
                <span class="qh-dot green"></span>
                Erlaubt
            </div>
            <div class="qh-legend-item">
                <span class="qh-dot red"></span>
                Nicht erlaubt
            </div>
            <div class="qh-legend-item">
                <span class="qh-dot blue"></span>
                Eigene Qualifikation
            </div>
        </div>

        <div class="qh-help-text" style="margin-top:14px;">
            <strong>Zeitfaktor:</strong><br>
            1.00 = normale Zeit<br>
            0.90 = schneller<br>
            1.20 = braucht 20% mehr Zeit
            <br><br>
            <strong>Kostenfaktor:</strong><br>
            Normalerweise 1.00 lassen.
        </div>
    </aside>

    <main class="qh-main">
        <div class="qh-toolbar">
            <input type="text" id="qh-search" class="qh-search" placeholder="Qualifikation suchen, z.B. Meister, Geselle, Azubi...">

            <button type="button" class="qh-btn" id="qh-open-all">
                Alle öffnen
            </button>

            <button type="button" class="qh-btn" id="qh-close-all">
                Alle schließen
            </button>
        </div>

        @forelse($quals as $performer)
            @php
                $allowedCount = 0;

                foreach ($quals as $requiredCountItem) {
                    $countKey = $performer->id . '_' . $requiredCountItem->id;
                    $countRule = $rules->get($countKey);

                    if (
                        $performer->id === $requiredCountItem->id ||
                        (bool) ($countRule?->allowed ?? false)
                    ) {
                        $allowedCount++;
                    }
                }
            @endphp

            <section class="qh-card {{ $loop->first ? 'is-open' : '' }}"
                     data-qh-card
                     data-search-text="{{ strtolower($performer->name) }}">

                <div class="qh-card-head" data-qh-toggle>
                    <div>
                        <div class="qh-title">
                            {{ $performer->name }}
                        </div>
                        <div class="qh-sub">
                            Mitarbeiter-Qualifikation · {{ $fmt($performer->default_price) }} €/h
                        </div>
                    </div>

                    <div class="qh-summary">
                        <span class="qh-pill green">
                            {{ $allowedCount }} erlaubt
                        </span>
                        <span class="qh-pill blue">
                            Öffnen / schließen
                        </span>
                    </div>
                </div>

                <div class="qh-card-body">
                    <div class="qh-grid">
                        @foreach($quals as $required)
                            @php
                                $key = $performer->id . '_' . $required->id;
                                $rule = $rules->get($key);

                                $isSelf = $performer->id === $required->id;

                                $allowed = $isSelf
                                    ? true
                                    : (bool) ($rule?->allowed ?? false);

                                $efficiency = $isSelf
                                    ? 1
                                    : (float) ($rule?->efficiency_factor ?? 1);

                                $costFactor = $isSelf
                                    ? 1
                                    : (float) ($rule?->cost_factor ?? 1);
                            @endphp

                            <div class="qh-work {{ $isSelf ? 'is-self' : ($allowed ? 'is-allowed' : 'is-denied') }}"
                                 data-hierarchy-cell
                                 data-performer-id="{{ $performer->id }}"
                                 data-required-id="{{ $required->id }}">

                                <div class="qh-work-top">
                                    <div>
                                        <div class="qh-work-name">
                                            {{ $required->name }}-Arbeit
                                        </div>

                                        <div class="qh-work-small">
                                            @if($isSelf)
                                                Eigene Qualifikation immer erlaubt
                                            @else
                                                Diese Arbeit {{ $allowed ? 'ist erlaubt' : 'ist nicht erlaubt' }}
                                            @endif
                                        </div>
                                    </div>

                                    <label class="qh-switch">
                                        <input type="checkbox"
                                               data-field="allowed"
                                               {{ $allowed ? 'checked' : '' }}
                                               {{ $isSelf ? 'disabled' : '' }}>
                                        <span class="qh-slider"></span>
                                    </label>
                                </div>

                                <div class="qh-fields">
                                    <div class="qh-field">
                                        <label>Zeitfaktor</label>
                                        <input data-field="efficiency_factor"
                                               value="{{ number_format($efficiency, 2, '.', '') }}"
                                               {{ $isSelf ? 'readonly' : '' }}>
                                    </div>

                                    <div class="qh-field">
                                        <label>Kostenfaktor</label>
                                        <input data-field="cost_factor"
                                               value="{{ number_format($costFactor, 2, '.', '') }}"
                                               {{ $isSelf ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @empty
            <div class="qh-empty">
                Keine Qualifikationen gefunden.
            </div>
        @endforelse
    </main>
</div>