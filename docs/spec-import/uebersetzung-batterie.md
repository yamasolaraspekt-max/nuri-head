# Übersetzungs-Template: Batteriespeicher-Datenblatt → kanonisches JSON

> Gerätetyp `batterie`, `spec_version:"1.0"`. Nie raten, fehlend = `null`. Format = `spec:import`.
>
> ⚠️ **Ehrlicher Hinweis:** Die heutigen Auslegungs-Kerne lesen von der Batterie **nur die
> WR-Kompatibilitätsfelder** (Spannung, Ladeleistung, Strom). Kapazität/Chemie/Zyklen sind zwar wertvolle
> Handels-/Zukunftsdaten (Batterie-Auslegung = B3/B4-Konzept, siehe konzept-auslegung-universal.md §4),
> werden aber **heute nicht ausgelegt** — daher als optional erfasst, nie erfunden.

## JSON-Skelett (● Pflicht f. WR-Kompat · ○ Handels-/Zukunftsdaten)
```json
{
  "spec_version": "1.0", "geraetetyp": "batterie",
  "identitaet": { "hersteller": "●", "modell": "●", "serie": "○", "kategorie": "batterie" },
  "fachdaten": {
    "min_voltage": "● V", "max_voltage": "● V", "max_charge_power_kw": "● kW", "max_current_a": "● A",
    "nominal_voltage": "○ V", "battery_type": "○ (LFP/NMC)", "kapazitaet_kwh": "○ kWh",
    "kapazitaet_nutzbar_kwh": "○ kWh", "entladeleistung_kw": "○ kW", "dod_pct": "○ %",
    "zyklen": "○", "operating_temp_min_c": "○ °C", "operating_temp_max_c": "○ °C"
  },
  "herkunft": { "datenquelle": "hersteller_datenblatt", "verifikations_status": "importiert_ungeprueft",
    "verifikations_datum": "YYYY-MM-DD", "datenblatt_referenz": "●", "quelle_url": "○" }
}
```

## Extraktionsregeln
1. **Pflicht für Auslegung heute:** `min_voltage`/`max_voltage` (Spannungsfenster für WR-Kopplung),
   `max_charge_power_kw` [**kW**, nicht W], `max_current_a`.
2. `kapazitaet_nutzbar_kwh` vs. `kapazitaet_kwh` (brutto) unterscheiden — Datenblatt gibt oft beide; DOD dazu.
3. `battery_type` als Chemie-Kürzel (LFP/NMC). `zyklen` = garantierte Vollzyklen bei angegebener DOD.
4. Modulare Systeme: eine Zeile je **Grundmodul**; Skalierung/„bis N Module" in Notiz, nicht als eigenes Objekt.
5. Kapazität/Zyklen sind (noch) nicht auslegungswirksam → als ○ erfassen, aber **nie schätzen**, wenn nicht im Blatt.

## Ausgabe-Konvention
JSON-Array · Fundstellen-Tabelle · `offene_felder` · `auffaelligkeiten`
(z. B. „nur brutto-kWh angegeben, nutzbar fehlt" · „kW/W-Verwechslungsgefahr Ladeleistung").
