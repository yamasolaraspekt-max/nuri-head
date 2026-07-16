# Welle B1 — ABGENOMMEN (2026-07-16, unabhängiger Evaluator)

**Prüfgegenstand:** Commit 1964753 — Ausgangsrechnungen, Gutschriften, Umsätze.
**Urteil:** Alle fünf Punkte grün, alle Werte selbst gemessen (echte Bestandsdaten, read-only).

- **Zahlen-Wahrheit:** Kacheln/Summen exakt gegen eigenständiges SQL auf invoices,
  inkl. Entfern-Gegenbeweis (Rechnung #20 rechnerisch entfernt → Differenz exakt).
- **Keine Verrechnung:** Gutschriften-KPI eigene Spalte, Umsatz unangetastet.
- **Performance:** 5/3/4 Queries je Fläche, kein N+1, Pagination 50 konfiguriert.
- **UI-Rubrik:** sa-ui-konform (Code-Beleg), Pills Farbe+Text, Storno gekennzeichnet, Leerzustände.
- **Routen-Schutz:** ohne Login 302, ohne Invoice-Rolle 403 (InvoiceMiddleware).

**Nicht-blockierende Nachmess-Posten (offen, Datenlage):**
1. Gutschriften-Fläche + Nicht-Verrechnung live nachmessen, sobald ≥1 echte Gutschrift im Bestand ist.
2. Pagination Seite 2 live aufrufen, sobald >50 Rechnungen im Zeitraum liegen.
3. UI-Pixel-Beleg (Kontrast am echten Rendern) bei nächster Browser-Abnahme mit erledigen.

Vorzeichen-/Verrechnungsregel der Gutschriften bleibt bewusst offene Fach-Entscheidung (Yama).
