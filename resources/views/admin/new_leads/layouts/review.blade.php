<div class="cr-wrap" data-customer-review-root data-customer-id="{{ $customerId }}"
    data-alternative-id="{{ $alternativeId }}" data-product-id="{{ $productId }}"data-index-url="{{ route('customer-reviews.index') }}"
     data-store-url="{{ route('customer-reviews.store') }}"
     data-base-url="{{ url('/customer-reviews') }}">

    <style>
        .cr-wrap {
            --cr-orange: #f8ac00;
            --cr-pink: #e50656;
            --cr-blue: #74b2d4;
            --cr-blue-soft: #c0d8ea;
            --cr-green: #93c21c;
            --cr-green-soft: #cfe09b;
            --cr-text: #1f2937;
            --cr-muted: #6b7280;
            --cr-border: #e5e7eb;
            --cr-soft: #f8fafc;
            background: #fff;
            color: var(--cr-text);
            padding: 16px;
        }

        .cr-wrap *,
        .cr-wrap *::before,
        .cr-wrap *::after {
            box-sizing: border-box;
        }

        .cr-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: start;
            margin-bottom: 18px;
        }

        .cr-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cr-title {
            font-size: 22px;
            font-weight: 900;
            color: var(--cr-blue);
            margin: 0;
        }

        .cr-subtitle {
            margin-top: 6px;
            color: var(--cr-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .cr-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(100px, 1fr));
            gap: 10px;
        }

        .cr-stat {
            border: 1px solid var(--cr-border);
            border-radius: 16px;
            padding: 12px;
            background: #fff;
            text-align: center;
        }

        .cr-stat-value {
            font-size: 20px;
            font-weight: 900;
            color: var(--cr-orange);
        }

        .cr-stat-label {
            font-size: 11px;
            color: var(--cr-muted);
            margin-top: 4px;
        }

        .cr-toolbar {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 180px 180px auto;
            gap: 10px;
            align-items: center;
            margin-bottom: 14px;
        }

        .cr-card {
            background: #fff;
            border: 1px solid var(--cr-border);
            border-radius: 20px;
            padding: 16px;
        }

        .cr-form-card {
            display: none;
            margin-bottom: 16px;
            border-color: rgba(248,172,0,.35);
            background: linear-gradient(180deg, rgba(248,172,0,.06), #fff 45%);
        }

        .cr-form-card.is-open {
            display: block;
        }

        .cr-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cr-card-title {
            font-size: 15px;
            font-weight: 900;
            color: var(--cr-text);
        }

        .cr-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .cr-form-group {
            margin-bottom: 12px;
        }

        .cr-form-group.full {
            grid-column: 1 / -1;
        }

        .cr-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--cr-muted);
            margin-bottom: 6px;
        }

        .cr-input,
        .cr-select,
        .cr-textarea {
            width: 100%;
            border: 1px solid var(--cr-border);
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 13px;
            outline: none;
            background: #fff;
            color: var(--cr-text);
        }

        .cr-input:focus,
        .cr-select:focus,
        .cr-textarea:focus {
            border-color: var(--cr-blue);
            box-shadow: 0 0 0 3px rgba(116,178,212,.16);
        }

        .cr-textarea {
            min-height: 82px;
            resize: vertical;
        }

        .cr-stars {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .cr-star-btn {
            border: 0;
            background: #f3f4f6;
            color: #9ca3af;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            font-size: 20px;
            cursor: pointer;
            transition: .15s ease;
        }

        .cr-star-btn.active {
            background: rgba(248,172,0,.18);
            color: var(--cr-orange);
        }

        .cr-check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--cr-text);
        }

        .cr-actions {
            display: flex;
            gap: 8px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .cr-btn {
            border: 0;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: .15s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .cr-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none !important;
        }

        .cr-btn-primary {
            background: var(--cr-orange);
            color: #fff;
        }

        .cr-btn-secondary {
            background: #f3f4f6;
            color: var(--cr-text);
        }

        .cr-btn-blue {
            background: rgba(116,178,212,.18);
            color: #256683;
        }

        .cr-btn-danger {
            background: rgba(229,6,86,.12);
            color: var(--cr-pink);
        }

        .cr-btn:hover {
            transform: translateY(-1px);
        }

        .cr-list-card {
            min-height: 260px;
        }

        .cr-list-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cr-list-count {
            font-size: 12px;
            color: var(--cr-muted);
            font-weight: 800;
        }

        .cr-list {
            display: grid;
            gap: 12px;
        }

        .cr-review {
            border: 1px solid var(--cr-border);
            border-radius: 20px;
            padding: 14px;
            background: #fff;
            transition: .15s ease;
        }

        .cr-review.is-editing {
            border-color: rgba(116,178,212,.55);
            box-shadow: 0 8px 24px rgba(15,23,42,.06);
        }

        .cr-review.is-hidden {
            display: none;
        }

        .cr-review-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .cr-review-stars {
            color: var(--cr-orange);
            font-size: 18px;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .cr-review-meta {
            font-size: 12px;
            color: var(--cr-muted);
            margin-top: 3px;
        }

        .cr-badges {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .cr-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 900;
            background: #f3f4f6;
            color: var(--cr-text);
        }

        .cr-badge-critical {
            background: rgba(229,6,86,.12);
            color: var(--cr-pink);
        }

        .cr-review-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--cr-border);
        }

        .cr-review-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--cr-muted);
            margin-bottom: 4px;
        }

        .cr-review-text {
            font-size: 13px;
            line-height: 1.55;
            color: var(--cr-text);
            white-space: pre-wrap;
        }

        .cr-edit-form {
            display: none;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--cr-border);
        }

        .cr-review.is-editing .cr-edit-form {
            display: block;
        }

        .cr-empty {
            border: 1px dashed var(--cr-border);
            border-radius: 20px;
            padding: 28px;
            text-align: center;
            color: var(--cr-muted);
            background: #fff;
            font-weight: 800;
        }

        .cr-empty.is-hidden {
            display: none;
        }

        .cr-toast-wrap {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 99999;
            display: grid;
            gap: 10px;
            width: min(360px, calc(100vw - 32px));
            pointer-events: none;
        }

        .cr-toast {
            pointer-events: auto;
            border-radius: 18px;
            background: #fff;
            border: 1px solid var(--cr-border);
            box-shadow: 0 16px 40px rgba(15,23,42,.16);
            padding: 13px 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            animation: crToastIn .18s ease-out;
        }

        .cr-toast.success {
            border-color: rgba(147,194,28,.35);
        }

        .cr-toast.error {
            border-color: rgba(229,6,86,.35);
        }

        .cr-toast-icon {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            flex: 0 0 auto;
        }

        .cr-toast.success .cr-toast-icon {
            background: rgba(147,194,28,.15);
            color: #4d7c0f;
        }

        .cr-toast.error .cr-toast-icon {
            background: rgba(229,6,86,.12);
            color: var(--cr-pink);
        }

        .cr-toast-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--cr-text);
            margin-bottom: 2px;
        }

        .cr-toast-msg {
            font-size: 12px;
            color: var(--cr-muted);
            line-height: 1.45;
        }

        @keyframes crToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1100px) {
            .cr-toolbar {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 980px) {
            .cr-header {
                grid-template-columns: 1fr;
            }

            .cr-stats {
                grid-template-columns: repeat(3, 1fr);
            }

            .cr-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .cr-wrap {
                padding: 10px;
            }

            .cr-stats,
            .cr-toolbar {
                grid-template-columns: 1fr;
            }

            .cr-review-top,
            .cr-list-head {
                flex-direction: column;
                align-items: stretch;
            }

            .cr-badges {
                justify-content: flex-start;
            }

            .cr-actions {
                flex-direction: column;
            }

            .cr-btn {
                width: 100%;
            }
        }
    </style>

    <div class="cr-toast-wrap" id="customerReviewToastWrap"></div>

    <div class="cr-header">
        <div>
            <div class="cr-title-row">
                <h3 class="cr-title">Bewertungen</h3>

                <button type="button" class="cr-btn cr-btn-primary" data-cr-toggle-create>
                    <i data-feather="plus-circle"></i>
                    Neue Bewertung
                </button>
            </div>

            <div class="cr-subtitle">
                <strong>Kunde:</strong> {{ $customer->display_name }}

                @if($alternative)
                    · <strong>Objekt:</strong> {{ $alternative->object_name ?: $alternative->full_address ?: '#' . $alternative->id }}
                @endif

                @if($product)
                    · <strong>Produkt:</strong> {{ $product->article_group }}
                @endif
            </div>
        </div>

        <div class="cr-stats">
            <div class="cr-stat">
                <div class="cr-stat-value" data-cr-count>{{ $stats['count'] }}</div>
                <div class="cr-stat-label">Bewertungen</div>
            </div>

            <div class="cr-stat">
                <div class="cr-stat-value">{{ $stats['avg_stars'] ?: '-' }}</div>
                <div class="cr-stat-label">Ø Sterne</div>
            </div>

            <div class="cr-stat">
                <div class="cr-stat-value">{{ $stats['critical_count'] }}</div>
                <div class="cr-stat-label">Kritisch</div>
            </div>
        </div>
    </div>

    <div class="cr-card cr-form-card" data-cr-create-card>
        <div class="cr-card-head">
            <div class="cr-card-title">Neue Bewertung erfassen</div>

            <button type="button" class="cr-btn cr-btn-secondary" data-cr-close-create>
                <i data-feather="x"></i>
                Schließen
            </button>
        </div>

        <form id="customerReviewForm">
            @csrf

            <input type="hidden" name="customer_id" value="{{ $customerId }}">
            <input type="hidden" name="alternative_id" value="{{ $alternativeId }}">
            <input type="hidden" name="product_id" value="{{ $productId }}">
            <input type="hidden" name="stars" value="5">

            <div class="cr-form-grid">
                <div class="cr-form-group">
                    <label class="cr-label">Sterne</label>
                    <div class="cr-stars" data-cr-stars>
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    class="cr-star-btn active"
                                    data-star="{{ $i }}">
                                ★
                            </button>
                        @endfor
                    </div>
                </div>

                <div class="cr-form-group">
                    <label class="cr-label">Verhalten des Kunden</label>
                    <select name="behavior" class="cr-select">
                        <option value="">Bitte wählen</option>
                        <option value="very_good">Sehr gut</option>
                        <option value="friendly">Freundlich</option>
                        <option value="normal">Normal</option>
                        <option value="price_sensitive">Preissensibel</option>
                        <option value="unreliable">Unzuverlässig</option>
                        <option value="difficult">Schwierig</option>
                        <option value="aggressive">Aggressiv</option>
                    </select>
                </div>

                <div class="cr-form-group full">
                    <label class="cr-label">Worauf sollte man bei diesem Kunden achten?</label>
                    <textarea name="caution_note"
                              class="cr-textarea"
                              placeholder="z.B. Preis vorher klar bestätigen, alle Absprachen schriftlich festhalten..."></textarea>
                </div>

                <div class="cr-form-group full">
                    <label class="cr-label">Internet Feedback / Online Eindruck</label>
                    <textarea name="internet_feedback"
                              class="cr-textarea"
                              placeholder="z.B. Google-Bewertungen, öffentliche Hinweise, externe Erfahrungen..."></textarea>
                </div>

                <div class="cr-form-group full">
                    <label class="cr-label">Interne Notiz</label>
                    <textarea name="internal_note"
                              class="cr-textarea"
                              placeholder="Interne Bewertung oder Erfahrung mit dem Kunden..."></textarea>
                </div>

                <div class="cr-form-group">
                    <label class="cr-label">Quelle</label>
                    <input type="text"
                           name="source"
                           class="cr-input"
                           placeholder="z.B. Telefonat, Google, Monteur, Außendienst">
                </div>

                <div class="cr-form-group">
                    <label class="cr-label">Status</label>
                    <label class="cr-check">
                        <input type="checkbox" name="is_critical" value="1">
                        Als kritisch markieren
                    </label>
                </div>
            </div>

            <div class="cr-actions">
                <button type="submit" class="cr-btn cr-btn-primary">
                    <i data-feather="save"></i>
                    Speichern
                </button>

                <button type="reset" class="cr-btn cr-btn-secondary">
                    <i data-feather="rotate-ccw"></i>
                    Zurücksetzen
                </button>
            </div>
        </form>
    </div>

    <div class="cr-card cr-list-card">
        <div class="cr-list-head">
            <div>
                <div class="cr-card-title">Bisherige Bewertungen</div>
                <div class="cr-list-count">
                    <span data-cr-visible-count>{{ $reviews->count() }}</span> von {{ $reviews->count() }} sichtbar
                </div>
            </div>
        </div>

        <div class="cr-toolbar">
            <input type="search"
                   class="cr-input"
                   data-cr-search
                   placeholder="Suchen nach Verhalten, Notiz, Quelle, Mitarbeiter...">

            <select class="cr-select" data-cr-star-filter>
                <option value="">Alle Sterne</option>
                <option value="5">5 Sterne</option>
                <option value="4">4 Sterne</option>
                <option value="3">3 Sterne</option>
                <option value="2">2 Sterne</option>
                <option value="1">1 Stern</option>
            </select>

            <select class="cr-select" data-cr-critical-filter>
                <option value="">Alle Status</option>
                <option value="1">Nur kritisch</option>
                <option value="0">Nicht kritisch</option>
            </select>

            <button type="button" class="cr-btn cr-btn-secondary" data-cr-clear-filters>
                <i data-feather="x-circle"></i>
                Filter löschen
            </button>
        </div>

        <div id="customerReviewList" class="cr-list">
            @forelse($reviews as $review)
                @php
    $searchText = strtolower(collect([
        $review->employee_name,
        $review->behavior_label,
        $review->caution_note,
        $review->internet_feedback,
        $review->internal_note,
        $review->source,
    ])->filter()->implode(' '));
                @endphp

                <div class="cr-review"
                     data-review-id="{{ $review->id }}"
                     data-stars="{{ $review->stars }}"
                     data-critical="{{ $review->is_critical ? 1 : 0 }}"
                     data-search="{{ e($searchText) }}">

                    <div class="cr-review-top">
                        <div>
                            <div class="cr-review-stars">
                                {{ str_repeat('★', $review->stars) }}{{ str_repeat('☆', 5 - $review->stars) }}
                            </div>

                            <div class="cr-review-meta">
                                Von {{ $review->employee_name }}
                                · {{ $review->created_at?->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <div class="cr-badges">
                            @if($review->behavior)
                                <span class="cr-badge">
                                    {{ $review->behavior_label }}
                                </span>
                            @endif

                            @if($review->is_critical)
                                <span class="cr-badge cr-badge-critical">
                                    Kritisch
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($review->caution_note)
                        <div class="cr-review-section">
                            <div class="cr-review-label">Achtung / Vorsicht</div>
                            <div class="cr-review-text">{{ $review->caution_note }}</div>
                        </div>
                    @endif

                    @if($review->internet_feedback)
                        <div class="cr-review-section">
                            <div class="cr-review-label">Internet Feedback</div>
                            <div class="cr-review-text">{{ $review->internet_feedback }}</div>
                        </div>
                    @endif

                    @if($review->internal_note)
                        <div class="cr-review-section">
                            <div class="cr-review-label">Interne Notiz</div>
                            <div class="cr-review-text">{{ $review->internal_note }}</div>
                        </div>
                    @endif

                    @if($review->source)
                        <div class="cr-review-section">
                            <div class="cr-review-label">Quelle</div>
                            <div class="cr-review-text">{{ $review->source }}</div>
                        </div>
                    @endif

                    <div class="cr-actions">
                        <button type="button" class="cr-btn cr-btn-blue" data-cr-edit>
                            <i data-feather="edit-2"></i>
                            Bearbeiten
                        </button>

                        <button type="button" class="cr-btn cr-btn-danger" data-cr-delete>
                            <i data-feather="trash-2"></i>
                            Löschen
                        </button>
                    </div>

                    <form class="cr-edit-form" data-cr-edit-form>
                        @csrf

                        <input type="hidden" name="stars" value="{{ $review->stars }}">

                        <div class="cr-form-grid">
                            <div class="cr-form-group">
                                <label class="cr-label">Sterne</label>
                                <div class="cr-stars" data-cr-stars>
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                class="cr-star-btn {{ $i <= $review->stars ? 'active' : '' }}"
                                                data-star="{{ $i }}">
                                            ★
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div class="cr-form-group">
                                <label class="cr-label">Verhalten</label>
                                <select name="behavior" class="cr-select">
                                    <option value="">Bitte wählen</option>
                                    <option value="very_good" @selected($review->behavior === 'very_good')>Sehr gut</option>
                                    <option value="friendly" @selected($review->behavior === 'friendly')>Freundlich</option>
                                    <option value="normal" @selected($review->behavior === 'normal')>Normal</option>
                                    <option value="price_sensitive" @selected($review->behavior === 'price_sensitive')>Preissensibel</option>
                                    <option value="unreliable" @selected($review->behavior === 'unreliable')>Unzuverlässig</option>
                                    <option value="difficult" @selected($review->behavior === 'difficult')>Schwierig</option>
                                    <option value="aggressive" @selected($review->behavior === 'aggressive')>Aggressiv</option>
                                </select>
                            </div>

                            <div class="cr-form-group full">
                                <label class="cr-label">Achtung / Vorsicht</label>
                                <textarea name="caution_note" class="cr-textarea">{{ $review->caution_note }}</textarea>
                            </div>

                            <div class="cr-form-group full">
                                <label class="cr-label">Internet Feedback</label>
                                <textarea name="internet_feedback" class="cr-textarea">{{ $review->internet_feedback }}</textarea>
                            </div>

                            <div class="cr-form-group full">
                                <label class="cr-label">Interne Notiz</label>
                                <textarea name="internal_note" class="cr-textarea">{{ $review->internal_note }}</textarea>
                            </div>

                            <div class="cr-form-group">
                                <label class="cr-label">Quelle</label>
                                <input type="text"
                                       name="source"
                                       class="cr-input"
                                       value="{{ $review->source }}">
                            </div>

                            <div class="cr-form-group">
                                <label class="cr-label">Status</label>
                                <label class="cr-check">
                                    <input type="checkbox"
                                           name="is_critical"
                                           value="1"
                                           @checked($review->is_critical)>
                                    Als kritisch markieren
                                </label>
                            </div>
                        </div>

                        <div class="cr-actions">
                            <button type="submit" class="cr-btn cr-btn-primary">
                                <i data-feather="save"></i>
                                Aktualisieren
                            </button>

                            <button type="button" class="cr-btn cr-btn-secondary" data-cr-cancel-edit>
                                <i data-feather="x"></i>
                                Abbrechen
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="cr-empty" data-cr-empty>
                    Noch keine Bewertung vorhanden.
                </div>
            @endforelse
        </div>

        <div class="cr-empty is-hidden" data-cr-no-results>
            Keine Bewertung passt zu deiner Suche.
        </div>
    </div>
</div> 
     <script>
(function () {
    "use strict";

    /**
     * Customer reviews partial is loaded dynamically by customer_profile.
     * Important: this script must not depend on top-level let/const names or on `this`
     * because the parent loader executes partial scripts manually.
     */
    const MODULE_KEY = "CustomerReviewAjax";
    const SELECTOR_ROOT = "[data-customer-review-root]";

    const api = window[MODULE_KEY] || {};
    window[MODULE_KEY] = api;

    function root() {
        return document.querySelector(SELECTOR_ROOT);
    }

    function normalizeId(value) {
        if (value === undefined || value === null || value === "" || value === "null") {
            return "";
        }

        return String(value);
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute("content") : "";
    }

    function routes() {
        const el = root();

        return {
            index: el ? (el.dataset.indexUrl || "") : "",
            store: el ? (el.dataset.storeUrl || "") : "",
            base: el ? (el.dataset.baseUrl || "") : "",
        };
    }

    function current() {
        const el = root();

        return {
            customerId: el ? normalizeId(el.dataset.customerId) : "",
            alternativeId: el ? normalizeId(el.dataset.alternativeId) : "",
            productId: el ? normalizeId(el.dataset.productId) : "",
        };
    }

    function escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function refreshIcons() {
        if (window.feather && typeof window.feather.replace === "function") {
            try {
                window.feather.replace();
            } catch (error) {
                console.warn("Feather refresh failed", error);
            }
        }
    }

    function ensureToastWrap() {
        let wrap = document.getElementById("customerReviewToastWrap");

        if (wrap) {
            return wrap;
        }

        wrap = document.createElement("div");
        wrap.id = "customerReviewToastWrap";
        wrap.className = "cr-toast-wrap";
        document.body.appendChild(wrap);

        return wrap;
    }

    function toast(type, title, message) {
        const wrap = ensureToastWrap();
        const item = document.createElement("div");

        item.className = "cr-toast " + (type || "success");
        item.innerHTML = `
            <div class="cr-toast-icon">${type === "error" ? "!" : "✓"}</div>
            <div>
                <div class="cr-toast-title">${escapeHtml(title || "")}</div>
                <div class="cr-toast-msg">${escapeHtml(message || "")}</div>
            </div>
        `;

        wrap.appendChild(item);

        window.setTimeout(function () {
            item.style.opacity = "0";
            item.style.transform = "translateY(-8px)";
            item.style.transition = ".18s ease";

            window.setTimeout(function () {
                item.remove();
            }, 220);
        }, 3600);
    }

    function injectUtilityStyles() {
        if (document.getElementById("customerReviewAjaxUtilityStyles")) {
            return;
        }

        const style = document.createElement("style");
        style.id = "customerReviewAjaxUtilityStyles";
        style.textContent = `
            .cr-loading-spinner {
                width:20px;
                height:20px;
                border:3px solid #e5e7eb;
                border-top-color:#f8ac00;
                border-radius:999px;
                display:inline-block;
                animation:crSpin .8s linear infinite;
            }

            @keyframes crSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .cr-swal-popup { border-radius: 22px !important; padding: 20px !important; }

            .cr-swal-confirm,
            .cr-swal-cancel {
                border: 0 !important;
                border-radius: 14px !important;
                padding: 10px 16px !important;
                font-size: 13px !important;
                font-weight: 900 !important;
                cursor: pointer !important;
                margin: 0 4px !important;
            }

            .cr-swal-confirm { background: #e50656 !important; color: #fff !important; }
            .cr-swal-cancel { background: #f3f4f6 !important; color: #1f2937 !important; }
        `;

        document.head.appendChild(style);
    }

    async function confirmDelete() {
        if (window.Swal) {
            const result = await Swal.fire({
                title: "Bewertung löschen?",
                text: "Diese Bewertung wird gelöscht.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ja, löschen",
                cancelButtonText: "Abbrechen",
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    popup: "cr-swal-popup",
                    confirmButton: "cr-swal-confirm",
                    cancelButton: "cr-swal-cancel",
                },
            });

            return result.isConfirmed;
        }

        return window.confirm("Diese Bewertung wirklich löschen?");
    }

    async function request(url, options) {
        if (!url) {
            throw new Error("URL fehlt.");
        }

        const finalOptions = Object.assign({
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
            },
        }, options || {});

        const token = csrfToken();

        if (token) {
            finalOptions.headers["X-CSRF-TOKEN"] = token;
        }

        if (finalOptions.body instanceof FormData && token && !finalOptions.body.has("_token")) {
            finalOptions.body.append("_token", token);
        }

        const response = await fetch(url, finalOptions);
        const contentType = response.headers.get("content-type") || "";
        const payload = contentType.includes("application/json")
            ? await response.json()
            : await response.text();

        if (!response.ok) {
            const message = payload && typeof payload === "object"
                ? (payload.message || Object.values(payload.errors || {}).flat().join("\n"))
                : payload;

            throw new Error(message || "Serverfehler.");
        }

        return payload;
    }

    function parseError(error) {
        if (!error) {
            return "Unbekannter Fehler.";
        }

        return error.message || String(error);
    }

    function buildIndexUrl() {
        const r = routes();
        const c = current();

        if (!r.index) {
            return "";
        }

        const url = new URL(r.index, window.location.origin);

        if (c.customerId) {
            url.searchParams.set("customer_id", c.customerId);
        }

        if (c.alternativeId) {
            url.searchParams.set("alternative_id", c.alternativeId);
        }

        if (c.productId) {
            url.searchParams.set("product_id", c.productId);
        }

        return url.toString();
    }

    async function reload() {
        const el = root();
        const url = buildIndexUrl();

        if (!el || !url) {
            return;
        }

        el.style.opacity = ".55";

        try {
            const response = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "text/html, application/json",
                },
            });

            const contentType = response.headers.get("content-type") || "";
            let html = "";

            if (contentType.includes("application/json")) {
                const json = await response.json();
                html = json.html || json.view || "";
            } else {
                html = await response.text();
            }

            if (!response.ok) {
                throw new Error(html || "Reload fehlgeschlagen.");
            }

            if (html) {
                const holder = document.createElement("div");
                holder.innerHTML = html;
                const newRoot = holder.querySelector(SELECTOR_ROOT);

                if (newRoot) {
                    el.replaceWith(newRoot);
                } else {
                    el.innerHTML = html;
                }

                bind();
            }
        } finally {
            const activeRoot = root();

            if (activeRoot) {
                activeRoot.style.opacity = "1";
            }
        }
    }

    function setStars(scope, value) {
        value = parseInt(value || 1, 10);

        if (!Number.isFinite(value)) {
            value = 1;
        }

        value = Math.max(1, Math.min(5, value));

        const input = scope.querySelector('input[name="stars"], [name="stars"]');
        const buttons = scope.querySelectorAll(".cr-star-btn");

        if (input) {
            input.value = value;
        }

        buttons.forEach(function (btn) {
            const star = parseInt(btn.dataset.star || "0", 10);
            btn.classList.toggle("active", star <= value);
        });
    }

    function bindStars(el) {
        el.querySelectorAll("[data-cr-stars]").forEach(function (group) {
            if (group.dataset.crBound === "1") {
                return;
            }

            group.dataset.crBound = "1";

            const form = group.closest("form") || el;

            group.addEventListener("click", function (event) {
                const button = event.target.closest(".cr-star-btn");

                if (!button) {
                    return;
                }

                event.preventDefault();
                setStars(form, button.dataset.star);
            });
        });
    }

    function bindCreateToggle(el) {
        const card = el.querySelector("[data-cr-create-card]");
        const openBtn = el.querySelector("[data-cr-toggle-create]");
        const closeBtn = el.querySelector("[data-cr-close-create]");

        if (openBtn && openBtn.dataset.crBound !== "1") {
            openBtn.dataset.crBound = "1";
            openBtn.addEventListener("click", function () {
                if (card) {
                    card.classList.add("is-open");
                }

                refreshIcons();
            });
        }

        if (closeBtn && closeBtn.dataset.crBound !== "1") {
            closeBtn.dataset.crBound = "1";
            closeBtn.addEventListener("click", function () {
                if (card) {
                    card.classList.remove("is-open");
                }
            });
        }
    }

    function bindCreateForm(el) {
        const form = el.querySelector("#customerReviewForm");

        if (!form || form.dataset.crBound === "1") {
            return;
        }

        form.dataset.crBound = "1";

        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            const submit = form.querySelector('[type="submit"]');
            const originalText = submit ? submit.innerHTML : "";

            if (submit) {
                submit.disabled = true;
                submit.innerHTML = '<span class="cr-loading-spinner"></span> Speichern...';
            }

            try {
                const data = await request(routes().store, {
                    method: "POST",
                    body: new FormData(form),
                });

                toast("success", "Gespeichert", data.message || "Bewertung wurde gespeichert.");
                await reload();
            } catch (error) {
                toast("error", "Fehler", parseError(error));
            } finally {
                if (submit) {
                    submit.disabled = false;
                    submit.innerHTML = originalText;
                }
            }
        });
    }

    function bindSearchAndFilters(el) {
        const search = el.querySelector("[data-cr-search]");
        const starFilter = el.querySelector("[data-cr-star-filter]");
        const criticalFilter = el.querySelector("[data-cr-critical-filter]");
        const clearBtn = el.querySelector("[data-cr-clear-filters]");
        const visibleCount = el.querySelector("[data-cr-visible-count]");
        const noResults = el.querySelector("[data-cr-no-results]");

        function apply() {
            const term = (search && search.value ? search.value : "").trim().toLowerCase();
            const star = starFilter ? starFilter.value : "";
            const critical = criticalFilter ? criticalFilter.value : "";
            let count = 0;

            el.querySelectorAll(".cr-review").forEach(function (card) {
                const haystack = (card.textContent || "").toLowerCase();
                const matchesTerm = !term || haystack.includes(term);
                const matchesStar = !star || String(card.dataset.stars || "") === String(star);
                const matchesCritical = !critical || String(card.dataset.critical || "0") === String(critical);
                const visible = matchesTerm && matchesStar && matchesCritical;

                card.classList.toggle("is-hidden", !visible);

                if (visible) {
                    count += 1;
                }
            });

            if (visibleCount) {
                visibleCount.textContent = String(count);
            }

            if (noResults) {
                noResults.classList.toggle("is-hidden", count !== 0);
            }
        }

        [search, starFilter, criticalFilter].forEach(function (input) {
            if (!input || input.dataset.crBound === "1") {
                return;
            }

            input.dataset.crBound = "1";
            input.addEventListener("input", apply);
            input.addEventListener("change", apply);
        });

        if (clearBtn && clearBtn.dataset.crBound !== "1") {
            clearBtn.dataset.crBound = "1";
            clearBtn.addEventListener("click", function () {
                if (search) {
                    search.value = "";
                }

                if (starFilter) {
                    starFilter.value = "";
                }

                if (criticalFilter) {
                    criticalFilter.value = "";
                }

                apply();
            });
        }

        apply();
    }

    function bindEditButtons(el) {
        el.querySelectorAll("[data-cr-edit]").forEach(function (button) {
            if (button.dataset.crBound === "1") {
                return;
            }

            button.dataset.crBound = "1";

            button.addEventListener("click", function () {
                const card = button.closest(".cr-review");

                if (!card) {
                    return;
                }

                el.querySelectorAll(".cr-review.is-editing").forEach(function (item) {
                    if (item !== card) {
                        item.classList.remove("is-editing");
                    }
                });

                card.classList.add("is-editing");
                refreshIcons();
            });
        });

        el.querySelectorAll("[data-cr-cancel-edit]").forEach(function (button) {
            if (button.dataset.crBound === "1") {
                return;
            }

            button.dataset.crBound = "1";

            button.addEventListener("click", function () {
                const card = button.closest(".cr-review");

                if (card) {
                    card.classList.remove("is-editing");
                }
            });
        });
    }

    function bindUpdateForms(el) {
        el.querySelectorAll("[data-cr-edit-form]").forEach(function (form) {
            if (form.dataset.crBound === "1") {
                return;
            }

            form.dataset.crBound = "1";

            form.addEventListener("submit", async function (event) {
                event.preventDefault();

                const reviewId = form.dataset.reviewId || "";
                const submit = form.querySelector('[type="submit"]');
                const originalText = submit ? submit.innerHTML : "";

                if (!reviewId) {
                    toast("error", "Fehler", "Bewertung-ID fehlt.");
                    return;
                }

                if (submit) {
                    submit.disabled = true;
                    submit.innerHTML = "Speichern...";
                }

                try {
                    const formData = new FormData(form);
                    formData.append("_method", "PUT");

                    const data = await request(routes().base + "/" + reviewId, {
                        method: "POST",
                        body: formData,
                    });

                    toast("success", "Aktualisiert", data.message || "Bewertung wurde aktualisiert.");
                    await reload();
                } catch (error) {
                    toast("error", "Fehler", parseError(error));
                } finally {
                    if (submit) {
                        submit.disabled = false;
                        submit.innerHTML = originalText;
                    }
                }
            });
        });
    }

    function bindDeleteButtons(el) {
        el.querySelectorAll("[data-cr-delete]").forEach(function (button) {
            if (button.dataset.crBound === "1") {
                return;
            }

            button.dataset.crBound = "1";

            button.addEventListener("click", async function () {
                const reviewId = button.dataset.reviewId || "";

                if (!reviewId) {
                    toast("error", "Fehler", "Bewertung-ID fehlt.");
                    return;
                }

                const confirmed = await confirmDelete();

                if (!confirmed) {
                    return;
                }

                const originalText = button.innerHTML;

                button.disabled = true;
                button.innerHTML = "Löschen...";

                try {
                    const formData = new FormData();
                    formData.append("_method", "DELETE");

                    const data = await request(routes().base + "/" + reviewId, {
                        method: "POST",
                        body: formData,
                    });

                    toast("success", "Gelöscht", data.message || "Bewertung wurde gelöscht.");
                    await reload();
                } catch (error) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                    toast("error", "Fehler", parseError(error));
                }
            });
        });
    }

    function bind() {
        injectUtilityStyles();

        const el = root();

        if (!el) {
            return;
        }

        bindStars(el);
        bindCreateToggle(el);
        bindCreateForm(el);
        bindSearchAndFilters(el);
        bindEditButtons(el);
        bindUpdateForms(el);
        bindDeleteButtons(el);
        refreshIcons();
    }

    Object.assign(api, {
        root,
        routes,
        current,
        normalizeId,
        escapeHtml,
        refreshIcons,
        ensureToastWrap,
        toast,
        injectUtilityStyles,
        confirmDelete,
        request,
        parseError,
        reload,
        bind,
        afterLoad: bind,
    });

    window.deleteCustomerReview = async function (reviewId) {
        const confirmed = await confirmDelete();

        if (!confirmed) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append("_method", "DELETE");

            const data = await request(routes().base + "/" + reviewId, {
                method: "POST",
                body: formData,
            });

            toast("success", "Gelöscht", data.message || "Bewertung wurde gelöscht.");
            await reload();
        } catch (error) {
            toast("error", "Fehler", parseError(error));
        }
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", bind, { once: true });
    } else {
        bind();
    }
})();
</script> 