# Übersetzungs-Template: Wechselrichter-Datenblatt → kanonisches JSON

> Gerätetyp `wechselrichter`, `spec_version:"1.0"`. **Einheiten-Vorsicht: W vs. VA** (Scheinleistung).
> Nie raten, fehlend = `null`. Format = `spec:import`.

## JSON-Skelett (● Pflicht · ○ optional · ⬦ nur Hybrid)
```json
{
  "spec_version": "1.0", "geraetetyp": "wechselrichter",
  "identitaet": { "hersteller": "●", "modell": "●", "serie": "○", "kategorie": "wechselrichter" },
  "fachdaten": {
    "max_input_voltage": "● V", "min_mpp_voltage": "● V", "max_mpp_voltage": "● V", "dc_startup_voltage": "● V",
    "num_mpp_trackers": "●", "max_input_current_per_mpp": "● A", "max_short_circuit_current_per_mpp": "○ A",
    "ac_nominal_power": "● W", "max_ac_power": "● VA", "max_dc_power": "● W",
    "max_dc_ac_ratio": "○", "max_array_power_wp": "○ Wp", "num_phases": "● (1/3)",
    "integrated_grid_protection": "● bool", "vde4105_compliant": "● bool",
    "active_power_limit": "○ (keine/60%/70%)", "controllable_14a": "○ bool", "control_interface": "○",
    "operating_temp_min_c": "○ °C", "operating_temp_max_c": "○ °C", "temp_derating_from_c": "○ °C",
    "is_hybrid": "● bool", "eps_capable": "○ bool",
    "battery_min_voltage": "⬦ V", "battery_max_voltage": "⬦ V", "battery_max_charge_power_w": "⬦ W", "battery_max_current_a": "⬦ A",
    "wirkungsgrad_euro_pct": "○ %"
  },
  "herkunft": { "datenquelle": "hersteller_datenblatt", "verifikations_status": "importiert_ungeprueft",
    "verifikations_datum": "YYYY-MM-DD", "datenblatt_referenz": "●", "quelle_url": "○" }
}
```

## Extraktionsregeln
1. **W vs. VA strikt trennen:** `ac_nominal_power` [W] ist Wirkleistung, `max_ac_power` [VA] Scheinleistung.
   Deklariert das Blatt die Nennleistung als VA (z. B. Fox H3 PRO), Zahlenwert übernehmen + in Notiz/Auffälligkeit.
3. **MPP-Fenster** = `min_mpp_voltage`/`max_mpp_voltage`; **`dc_startup_voltage`** ist die Einschaltschwelle (≠ MPP-min).
4. `battery_max_charge_power_w` in **W** (Einheiten-Falle: die Batterie führt kW, der WR W — nicht verwechseln).
5. Netzregeln: `integrated_grid_protection` (NA-Schutz), `vde4105_compliant`, §14a-Block nur wenn ausgewiesen; sonst `null`.
6. `is_hybrid`: nur `true`, wenn Batterie-Eingang vorhanden. Hybrid-Felder (⬦) sonst alle `null`.
7. Mehrere Leistungsklassen (5.0/6.0/8.0 …) = **je ein JSON-Objekt**.

## Ausgabe-Konvention
JSON-Array · Fundstellen-Tabelle · `offene_felder` · `auffaelligkeiten` (v. a. W/VA-Deklaration, §14a-Lücken).
