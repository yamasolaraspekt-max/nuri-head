# Übersetzungs-Template: Wärmepumpe-Datenblatt → kanonisches JSON

> **Workflow:** Hersteller-Datenblatt (PDF/Text) in eine Claude-Code-Session kopieren + dieses Template
> anhängen → schema-konformes JSON zurück. `spec_version:"1.0"`, Gerätetyp `waermepumpe`. **Nie raten,
> fehlender Wert = `null`.** Format = Import-Format (`spec:import`, siehe 00-spec-standard.md).

## JSON-Skelett (● Pflicht · ○ optional/teilweise)
```json
{
  "spec_version": "1.0",
  "geraetetyp": "waermepumpe",
  "identitaet": {
    "hersteller": "●", "modell": "●", "serie": "○", "kategorie": "waermepumpe"
  },
  "fachdaten": {
    "heizleistung_am7_w35_kw": "● kW", "heizleistung_a2_w35_kw": "● kW", "heizleistung_a7_w35_kw": "● kW",
    "cop_am7_w35": "●", "cop_a2_w35": "●", "cop_a7_w35": "●",
    "heizleistung_am7_w55_kw": "○ kW", "cop_am7_w55": "○",
    "scop_35": "○", "scop_55": "○",
    "modulation_min_kw": "○ kW", "modulation_max_kw": "○ kW",
    "max_vorlauf_c": "○ °C", "aussen_heizen_min_c": "● °C",
    "bauart": "○ (monoblock/split)", "kaeltemittel": "○", "phasen": "○ (1/3)", "schall_volllast_db": "○ dB"
  },
  "semantik": { "kurve_semantik": "● en14511_nenn|volllast_max", "leistungskurve": "○ null oder [[t,kW,cop],…]" },
  "herkunft": {
    "datenquelle": "hersteller_datenblatt", "imported_from": null,
    "verifikations_status": "importiert_ungeprueft",
    "verifikations_datum": "YYYY-MM-DD", "datenblatt_referenz": "● Dok-Nr./Version/Datum", "quelle_url": "○"
  }
}
```

## Extraktionsregeln (die Fachlogik)
1. **EN-14511-Nennleistung-Zeile** verwenden — **NICHT** `Prated`/Ecodesign/SCOP-Datenblatt-Nennwert.
2. **kW und COP eines Punkts zwingend aus DERSELBEN Betriebsart-Zeile** (Nenn **oder** Volllast — nie mischen;
   das war die Buderus-Falle). `kurve_semantik` dokumentiert die gewählte Betriebsart.
3. Betriebspunkte: A-7/A2/A7 je bei **W35**; zusätzlich A-7 bei **W55** (für die Vorlauf-Korrektur).
   Fehlt ein Punkt im Blatt → `null` (**nie interpolieren, nie erfinden**).
4. `kurve_semantik='en14511_nenn'`, wenn die Werte aus den Betriebspunkt-**Spalten** stammen; `volllast_max`
   nur, wenn eine echte Max-Wärmeleistungs-Tabelle vorliegt. Dichte `leistungskurve` nur bei belegtem Kennfeld.
5. **SCOP** immer mit Klimazone (mittel) + Vorlauf (35/55) zuordnen; unklare Klimazone → `null` + Auffälligkeit.
6. **Mehrere Modelle/Varianten auf einem Blatt** = **je ein JSON-Objekt** (Array). 400-V-Variante = eigenes Objekt.
7. `aussen_heizen_min_c` = Einsatzgrenze Heizbetrieb (nicht Lagertemperatur).

## Ausgabe-Konvention
1. Das/die JSON-Objekt(e).
2. **Fundstellen-Tabelle:** `Feld | Wert | Seite/Tabelle im Datenblatt`.
3. Block **`offene_felder`**: Pflichtfelder, die das Blatt nicht hergibt (+ warum).
4. Block **`auffaelligkeiten`**: Widersprüche, Einheiten-Zweifel, Betriebsart-Unklarheiten.

## Ausgefülltes Beispiel (Buderus WLW-7 MB AR, verifiziert)
```json
{
  "spec_version": "1.0", "geraetetyp": "waermepumpe",
  "identitaet": { "hersteller": "Buderus", "modell": "WLW-7 MB AR", "serie": "Logatherm WLW MB AR", "kategorie": "waermepumpe" },
  "fachdaten": {
    "heizleistung_am7_w35_kw": 6.71, "heizleistung_a2_w35_kw": 2.87, "heizleistung_a7_w35_kw": 2.84,
    "cop_am7_w35": 2.36, "cop_a2_w35": 4.06, "cop_a7_w35": 4.85,
    "heizleistung_am7_w55_kw": null, "cop_am7_w55": null, "scop_35": 4.58, "scop_55": 3.52,
    "modulation_min_kw": 1.3, "modulation_max_kw": 7.1, "max_vorlauf_c": 75, "aussen_heizen_min_c": -22,
    "bauart": "monoblock", "kaeltemittel": "R290 (Propan)", "phasen": null, "schall_volllast_db": 57.7
  },
  "semantik": { "kurve_semantik": "en14511_nenn", "leistungskurve": null },
  "herkunft": { "datenquelle": "hersteller_datenblatt", "imported_from": "wberechnung",
    "verifikations_status": "datenblatt_verifiziert", "verifikations_datum": "2026-07-05",
    "datenblatt_referenz": "Buderus 6721874368 (2024/04)", "quelle_url": null }
}
```
`auffaelligkeiten`: A-7/W55 nicht im Nenn-Blatt → `null` (Vorlauf-Korrektur nutzt Derating-Fallback);
`phasen` nicht ausgewiesen (Spannung „1×230").
