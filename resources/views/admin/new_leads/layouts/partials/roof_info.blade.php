<form class="partial-form" data-section="roof_info" data-id="{{ $alternative->id }}">
    @csrf

    <style>
        .fw-roof-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .fw-roof-toolbar-title {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .fw-roof-toolbar-title strong {
            font-size: 14px;
            font-weight: 900;
            color: #334155;
        }

        .fw-roof-toolbar-title span {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .fw-roof-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .fw-roof-info-box {
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .fw-empty-state {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 18px;
            padding: 34px 18px;
            text-align: center;
            color: #64748b;
        }

        .fw-empty-state i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 999px;
            background: #eff6ff;
            color: #74b2d4;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .fw-empty-title {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 900;
            color: #334155;
        }

        .fw-empty-subtitle {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .fw-roof-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .fw-roof-toolbar .fw-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="fw-shell">
        <div class="fw-body">

            <div class="fw-roof-info-box">
                <i class="feather icon-info"></i>
                <span>Dachflächen erfassen: Ausrichtung, Neigung, Dachtyp, Belegung und relevante Hinweise pro
                    Dachfläche.</span>
            </div>

            <section class="fw-section">
                <div class="fw-roof-toolbar">
                    <div class="fw-roof-toolbar-title">
                        <strong>Dachflächen</strong>
                        <span>{{ count($roofs ?? []) }} Dachfläche(n) angelegt</span>
                    </div>

                    <button type="button" class="fw-btn fw-btn-outline"
                        onclick="addNewRoofEditProfile(); document.getElementById('no-roof-message')?.remove();">
                        <i class="feather icon-plus"></i>
                        Neue Dachfläche hinzufügen
                    </button>
                </div>

                <div id="roof-wrapper" class="fw-roof-list">
                    @foreach (($roofs ?? []) as $index => $roof)
                        @include('admin.new_leads.layouts.partials.roof-fields', [
                            'index' => $index,
                            'roof' => $roof,
                            'alternative' => $alternative,
                        ])
                      @endforeach
                @if(count($roofs ?? []) === 0)
                    <div id="no-roof-message" class="fw-empty-state">
                            <i class="feather icon-home"></i>
                            <p class="fw-empty-title">Bisher wurden keine Dachflächen angelegt.</p>
                            <p class="fw-empty-subtitle">Klicken Sie oben, um das erste Dach hinzuzufügen.</p>
                        </div>
                @endif
                </div>
            </section>
        </div>

        <div class="fw-footer">
            <button type="button" onclick="window.goToStep(2)" class="fw-btn fw-btn-secondary">
                <i class="feather icon-arrow-left"></i>
                Zurück
            </button>

            <button type="submit" class="fw-btn fw-btn-primary" onclick="setTimeout(goNext, 500)">
                Speichern & Weiter
                <i class="feather icon-arrow-right"></i>
            </button>
        </div>
    </div>
</form>
 