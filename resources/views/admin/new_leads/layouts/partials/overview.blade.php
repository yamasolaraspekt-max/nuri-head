<style>
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        background-color: var(--slate-50);
    }
    .summary-card {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .summary-card h3 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--slate-500);
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--slate-100);
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .data-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    .data-label { color: var(--slate-600); font-weight: 500; }
    .data-value { color: var(--slate-900); font-weight: 600; text-align: right; }
    .highlight-value { color: var(--blue-600); }
</style>

<div class="step-body p-0">
    <div class="overview-grid">
        
        <div class="summary-card">
            <h3><i class="feather icon-home"></i> Gebäude & Basis</h3>
            <div class="data-row">
                <span class="data-label">Objekt / Bj.</span>
                <span class="data-value">{{ $alternative->object_type ?? '—' }} / {{ $alternative->building_year ?? '—' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Wohnfläche</span>
                <span class="data-value">{{ $alternative->heated_area ?? '0' }} m²</span>
            </div>
            <div class="data-row">
                <span class="data-label">Zustand</span>
                <span class="data-value">{{ $alternative->building_condition ?? '—' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Eigentümer</span>
                <span class="data-value">{{ $alternative->owner_count ?? '—' }}</span>
            </div>
        </div>

        <div class="summary-card">
            <h3><i class="feather icon-layers"></i> Dachflächen ({{ count($roofs) }})</h3>
            @foreach($roofs as $roof)
            <div style="margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--slate-100);">
                <div class="data-row">
                    <span class="data-label">{{ $roof->designation ?? 'Dach' }}</span>
                    <span class="data-value">{{ $roof->kwp_size ?? '0' }} kWp</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Neigung/Ausr.</span>
                    <span class="data-value">{{ $roof->roof_pitch }}° / {{ $roof->roof_orientation }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="summary-card">
            <h3><i class="feather icon-thermometer"></i> Heizsystem</h3>
            <div class="data-row">
                <span class="data-label">Technik</span>
                <span class="data-value highlight-value">{{ $alternative->heating_system_type ?? '—' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Heizlast</span>
                <span class="data-value">{{ $alternative->heating_load_calculation ?? '0' }} kW</span>
            </div>
            <div class="data-row">
                <span class="data-label">Warmwasser</span>
                <span class="data-value">{{ $alternative->hot_water_generation ?? '—' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Bäder</span>
                <span class="data-value">{{ $alternative->bathroom_count ?? '0' }}</span>
            </div>
        </div>

        <div class="summary-card">
            <h3><i class="feather icon-zap"></i> E-Mobilität</h3>
            <div class="data-row">
                <span class="data-label">E-Auto</span>
                <span class="data-value">{{ $alternative->electric_car ?? 'Nein' }} ({{ $alternative->electric_car_count ?? 0 }})</span>
            </div>
            <div class="data-row">
                <span class="data-label">Wallboxen</span>
                <span class="data-value">{{ $alternative->wallbox_count ?? 0 }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Starkstrom</span>
                <span class="data-value">{{ $alternative->heavy_current_cable ?? '—' }}</span>
            </div>
        </div>

        <div class="summary-card" style="border-left: 4px solid var(--blue-600);">
            <h3><i class="feather icon-activity"></i> Energieverbrauch</h3>
            <div class="data-row">
                <span class="data-label">Haushalt</span>
                <span class="data-value">{{ $alternative->power_household ?? 0 }} kWh</span>
            </div>
            <div class="data-row">
                <span class="data-label">Wärmepumpe</span>
                <span class="data-value">{{ $alternative->power_heatpump ?? 0 }} kWh</span>
            </div>
            <div class="data-row" style="margin-top: 10px; border-top: 2px solid var(--slate-100); padding-top: 5px;">
                <span class="data-label" style="font-weight: 700;">GESAMT</span>
                <span class="data-value highlight-value" style="font-size: 1.1rem;">{{ $alternative->power_total ?? 0 }} kWh/a</span>
            </div>
        </div>
        
        <div class="summary-card col-span-2">
            <h3><i class="feather icon-message-square"></i> Letzte Notiz</h3>
            <p style="font-size: 0.9rem; color: var(--slate-700); line-height: 1.5;">
                {{ $alternative->object_remark ?: 'Keine Bemerkungen hinterlegt.' }}
            </p>
        </div>

    </div>
    
    <div class="step-footer justify-end">
        <button type="button" onclick="window.goToStep(2)" class="btn-wizard btn-primary-blue">
            Daten bearbeiten <i class="feather icon-arrow-right"></i>
        </button>
    </div>
</div>