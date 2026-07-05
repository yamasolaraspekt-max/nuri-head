# Übersetzungs-Template: PV-Modul-Datenblatt → kanonisches JSON

> Gerätetyp `pv_modul`, `spec_version:"1.0"`. **Alle elektrischen Werte bei STC** (25 °C, 1000 W/m²).
> Nie raten, fehlend = `null`. Format = `spec:import`.

## JSON-Skelett (● Pflicht · ○ optional)
```json
{
  "spec_version": "1.0", "geraetetyp": "pv_modul",
  "identitaet": { "hersteller": "●", "modell": "●", "serie": "○", "kategorie": "pv_modul" },
  "fachdaten": {
    "pmpp_wp": "● Wp", "voc_v": "● V", "vmpp_v": "● V", "isc_a": "● A", "impp_a": "● A",
    "tk_voc_pct_k": "● %/K (negativ)", "tk_isc_pct_k": "○ %/K (positiv, Fallback 0.05)",
    "tk_pmpp_pct_k": "○ %/K (negativ)", "tk_vmpp_pct_k": "○ %/K",
    "u_sys_max_v": "● V", "sicherung_max_a": "○ A"
  },
  "herkunft": { "datenquelle": "hersteller_datenblatt", "verifikations_status": "importiert_ungeprueft",
    "verifikations_datum": "YYYY-MM-DD", "datenblatt_referenz": "● Modell/Version/Datum", "quelle_url": "○" }
}
```

## Extraktionsregeln
1. **STC-Spalte** verwenden (nicht NOCT/NMOT). NOCT ist auslegungs-irrelevant (Kern liest es nicht) → weglassen.
2. **Vorzeichen der TK strikt übernehmen:** `tk_voc` negativ (V sinkt mit Wärme), `tk_isc` positiv, `tk_pmpp` negativ.
   Datenblatt gibt oft nur zwei von dreien → fehlende `null` (Kern leitet `tk_vmpp` aus β_Pmpp − α_Isc ab).
3. `u_sys_max_v` = **maximale Systemspannung** des Moduls (meist 1500 V) — sicherheitskritische Obergrenze.
4. `sicherung_max_a` = **max. Strangsicherung** (Datenblatt „Maximum series fuse rating").
5. `pmpp_wp` als Ganzzahl [Wp]. Bei mehreren Leistungsklassen auf einem Blatt = **je ein JSON-Objekt**
   (all-black/silber elektrisch identisch → 1 Objekt + Farbe in `identitaet`/Notiz).

## Ausgabe-Konvention
JSON-Array · Fundstellen-Tabelle (`Feld | Wert | Seite/Tabelle`) · `offene_felder` · `auffaelligkeiten`
(z. B. „nur tk_voc + tk_pmpp angegeben, tk_isc Fallback").
