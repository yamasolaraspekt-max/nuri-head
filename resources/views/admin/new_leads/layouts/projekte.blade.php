{{-- kommunikation.blade.php --}}
<div class="kp-wrap">
    <style>
        .kp-wrap {
            --kp-green: #93c21c;
            --kp-green-soft: #cfe09b;
            --kp-blue: #74b2d4;
            --kp-blue-soft: #c0d8ea;
            --kp-orange: #f8ac00;
            --kp-pink: #e50656;
            --kp-text: #374151;
            --kp-muted: #6b7280;
            --kp-border: #c0d8ea;

            background: #ffffff;
            color: var(--kp-text);
            padding: 14px;
            max-width: 100%;
            overflow: hidden;
        }

        .kp-wrap *,
        .kp-wrap *::before,
        .kp-wrap *::after {
            box-sizing: border-box;
            box-shadow: none !important;
        }

        .kp-hero {
            position: relative;
            min-height: 520px;
            border: 1px solid var(--kp-border);
            border-radius: 28px;
            background:
                radial-gradient(circle at top left, rgba(147, 194, 28, .22), transparent 34%),
                radial-gradient(circle at bottom right, rgba(116, 178, 212, .28), transparent 36%),
                #ffffff;
            overflow: hidden;
            padding: 34px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr);
            gap: 28px;
            align-items: center;
        }

        .kp-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: rgba(248, 172, 0, .14);
            color: #9a6500;
            border: 1px solid rgba(248, 172, 0, .35);
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }

        .kp-title {
            margin: 0;
            color: var(--kp-blue);
            font-size: clamp(28px, 4vw, 48px);
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .kp-text {
            margin: 14px 0 0;
            max-width: 680px;
            color: var(--kp-text);
            font-size: 15px;
            line-height: 1.75;
            font-weight: 650;
        }

        .kp-context {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 24px;
        }

        .kp-context-card {
            border: 1px solid var(--kp-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, .78);
            padding: 14px;
            min-width: 0;
        }

        .kp-context-label {
            color: var(--kp-muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }

        .kp-context-value {
            color: var(--kp-text);
            font-size: 14px;
            font-weight: 900;
            overflow-wrap: anywhere;
        }

        .kp-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .kp-btn {
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--kp-blue);
            color: #ffffff;
            font-weight: 900;
            text-decoration: none;
            cursor: default;
        }

        .kp-btn-soft {
            background: #ffffff;
            border: 1px solid var(--kp-border);
            color: var(--kp-text);
        }

        .kp-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 390px;
        }

        .kp-visual-card {
            width: min(100%, 520px);
            border: 1px solid var(--kp-border);
            border-radius: 30px;
            background: rgba(255, 255, 255, .72);
            padding: 22px;
        }

        .kp-progress {
            margin-top: 18px;
            display: grid;
            gap: 10px;
        }

        .kp-progress-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .kp-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--kp-green);
            flex: 0 0 auto;
        }

        .kp-progress-line {
            height: 10px;
            border-radius: 999px;
            background: var(--kp-blue-soft);
            overflow: hidden;
            flex: 1;
        }

        .kp-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: var(--kp-green);
        }

        .kp-progress-text {
            width: 92px;
            font-size: 12px;
            color: var(--kp-muted);
            font-weight: 900;
            text-align: right;
        }

        .kp-floating {
            position: absolute;
            right: 24px;
            bottom: 24px;
            border-radius: 22px;
            background: var(--kp-green);
            color: #ffffff;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 1100px) {
            .kp-hero {
                grid-template-columns: 1fr;
            }

            .kp-visual {
                min-height: auto;
            }
        }

        @media (max-width: 720px) {
            .kp-wrap {
                padding: 8px;
            }

            .kp-hero {
                padding: 18px;
                border-radius: 22px;
            }

            .kp-context {
                grid-template-columns: 1fr;
            }

            .kp-actions {
                flex-direction: column;
            }

            .kp-btn {
                width: 100%;
            }

            .kp-floating {
                position: static;
                margin-top: 14px;
                justify-content: center;
            }
        }
    </style>

    <section class="kp-hero">
        <div>
            <div class="kp-badge">
                <i data-feather="tool"></i>
                Montage / Projekt
            </div>

            <h3 class="kp-title">
                Projektbereich kommt bald
            </h3>

            <p class="kp-text">
                Dieser Bereich wird vorbereitet. Hier werden später Montageplanung, Projektfortschritt,
                Kommunikation, Aufgaben, Freigaben und wichtige Projektdaten übersichtlich zusammengeführt.
            </p>

            <div class="kp-context">
                <div class="kp-context-card">
                    <div class="kp-context-label">Kunde</div>
                    <div class="kp-context-value">
                        {{ trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) ?: ($customer->firma ?? '–') }}
                    </div>
                </div>

                <div class="kp-context-card">
                    <div class="kp-context-label">Objekt</div>
                    <div class="kp-context-value">
                        {{ $alternative->object_name ?? $alternative->full_address ?? '–' }}
                    </div>
                </div>

                <div class="kp-context-card">
                    <div class="kp-context-label">Produkt-ID</div>
                    <div class="kp-context-value">
                        #{{ $productData->product_id ?? '–' }}
                    </div>
                </div>
            </div>

            <div class="kp-actions">
                <span class="kp-btn">
                    <i data-feather="clock"></i>
                    Bald verfügbar
                </span>

                <span class="kp-btn kp-btn-soft">
                    <i data-feather="layers"></i>
                    Projektmodul in Vorbereitung
                </span>
            </div>
        </div>

        <div class="kp-visual">
            <div class="kp-visual-card">
                <svg viewBox="0 0 720 520" width="100%" height="100%" role="img"
                    aria-label="Montage und Projekt Illustration">
                    <defs>
                        <linearGradient id="kpRoof" x1="0" x2="1">
                            <stop offset="0" stop-color="#74b2d4" />
                            <stop offset="1" stop-color="#93c21c" />
                        </linearGradient>

                        <linearGradient id="kpPanel" x1="0" x2="1">
                            <stop offset="0" stop-color="#c0d8ea" />
                            <stop offset="1" stop-color="#ffffff" />
                        </linearGradient>

                        <filter id="kpShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#1f2937" flood-opacity=".12" />
                        </filter>
                    </defs>

                    <rect x="42" y="365" width="640" height="42" rx="21" fill="#c0d8ea" opacity=".7" />

                    <g filter="url(#kpShadow)">
                        <path d="M130 260 L360 112 L590 260 Z" fill="url(#kpRoof)" />
                        <rect x="178" y="260" width="364" height="150" rx="18" fill="#ffffff" stroke="#c0d8ea"
                            stroke-width="8" />
                        <rect x="292" y="302" width="72" height="108" rx="8" fill="#cfe09b" />
                        <rect x="394" y="302" width="86" height="60" rx="10" fill="#c0d8ea" />
                        <rect x="216" y="302" width="52" height="46" rx="10" fill="#c0d8ea" />
                    </g>

                    <g transform="translate(238 156)">
                        <rect x="0" y="0" width="92" height="54" rx="10" fill="url(#kpPanel)" stroke="#ffffff"
                            stroke-width="5" />
                        <line x1="30" y1="4" x2="30" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="60" y1="4" x2="60" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="5" y1="27" x2="87" y2="27" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                    </g>

                    <g transform="translate(348 156)">
                        <rect x="0" y="0" width="92" height="54" rx="10" fill="url(#kpPanel)" stroke="#ffffff"
                            stroke-width="5" />
                        <line x1="30" y1="4" x2="30" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="60" y1="4" x2="60" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="5" y1="27" x2="87" y2="27" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                    </g>

                    <g transform="translate(458 156)">
                        <rect x="0" y="0" width="92" height="54" rx="10" fill="url(#kpPanel)" stroke="#ffffff"
                            stroke-width="5" />
                        <line x1="30" y1="4" x2="30" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="60" y1="4" x2="60" y2="50" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                        <line x1="5" y1="27" x2="87" y2="27" stroke="#74b2d4" stroke-width="3" opacity=".7" />
                    </g>

                    <g transform="translate(94 294)">
                        <circle cx="48" cy="44" r="28" fill="#f8ac00" />
                        <rect x="32" y="72" width="32" height="72" rx="14" fill="#374151" />
                        <path d="M20 110 L76 110" stroke="#374151" stroke-width="15" stroke-linecap="round" />
                        <path d="M44 144 L24 194" stroke="#374151" stroke-width="15" stroke-linecap="round" />
                        <path d="M54 144 L82 194" stroke="#374151" stroke-width="15" stroke-linecap="round" />
                        <path d="M86 78 L136 42" stroke="#93c21c" stroke-width="12" stroke-linecap="round" />
                        <circle cx="144" cy="36" r="13" fill="#93c21c" />
                    </g>

                    <g transform="translate(520 286)">
                        <rect x="0" y="0" width="118" height="92" rx="18" fill="#ffffff" stroke="#74b2d4"
                            stroke-width="6" />
                        <path d="M26 32 H92" stroke="#93c21c" stroke-width="10" stroke-linecap="round" />
                        <path d="M26 58 H74" stroke="#f8ac00" stroke-width="10" stroke-linecap="round" />
                        <circle cx="94" cy="60" r="12" fill="#e50656" />
                    </g>

                    <path d="M116 102 C192 36, 280 74, 328 54 C430 12, 516 54, 584 104" fill="none" stroke="#c0d8ea"
                        stroke-width="12" stroke-linecap="round" opacity=".65" />

                    <circle cx="118" cy="102" r="10" fill="#93c21c" />
                    <circle cx="584" cy="104" r="10" fill="#f8ac00" />
                </svg>

                <div class="kp-progress">
                    <div class="kp-progress-row">
                        <span class="kp-dot"></span>
                        <div class="kp-progress-line">
                            <div class="kp-progress-fill" style="width: 76%;"></div>
                        </div>
                        <div class="kp-progress-text">Planung</div>
                    </div>

                    <div class="kp-progress-row">
                        <span class="kp-dot" style="background: var(--kp-blue);"></span>
                        <div class="kp-progress-line">
                            <div class="kp-progress-fill" style="width: 48%; background: var(--kp-blue);"></div>
                        </div>
                        <div class="kp-progress-text">Montage</div>
                    </div>

                    <div class="kp-progress-row">
                        <span class="kp-dot" style="background: var(--kp-orange);"></span>
                        <div class="kp-progress-line">
                            <div class="kp-progress-fill" style="width: 24%; background: var(--kp-orange);"></div>
                        </div>
                        <div class="kp-progress-text">Freigabe</div>
                    </div>
                </div>
            </div>

            <div class="kp-floating">
                <i data-feather="hard-hat"></i>
                Projektmodul
            </div>
        </div>
    </section>
</div>

<script>
    if (window.feather) {
        window.feather.replace();
    }
</script>