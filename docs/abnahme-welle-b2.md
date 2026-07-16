# Welle B2 — Abnahme-Protokoll (unabhängiger Evaluator)

## Pipeline-Board /deals/pipeline — ABGENOMMEN (2026-07-16)
**Commit-Stand:** 3a812c9 (Board) + ce25394 (Blocker-Fix B2-1). **Votum: FREI.**
- Eine Wahrheit: nur deals.project_status. 🟢
- Spalten-Zahlen == eigenes SQL (GROUP BY): 14/14 deckungsgleich, cancel als Zähler. 🟢
- Operanden-Gate: 13 unbekannte Altwerte sichtbar in „Sonstige", nichts verloren. 🟢
- Query-Zahl: 4 (1 + 3 Eager-Load), kein N+1. 🟢
- Routen-Schutz: ohne Login 302 → /login. 🟢
- Blocker B2-1 (Spaltenname name→article_group) nachweislich geschlossen, Produkt-Label „Wärmepumpe".

**Offener, bewusst getrennter Folge-Posten (Yama-Entscheid):** Mapping/Bereinigung der
deutschen Alt-project_status-Werte (Montage/offen/in Bearbeitung), damit die Stufen-Spalten
real füllen statt alles in „Sonstige". DAUERDIREKTIVE: kein UPDATE als Beifang.

## Sets, Gesamtfirma-Cockpit, Je-Abteilung-Cockpit — umgesetzt, Abnahme ausstehend

## B2-Batch (Materialentnahmen, Cockpit-Trilogie, Geschäftsführung) — ABGENOMMEN (2026-07-16)
**Votum: FREI** (unabhängiger Evaluator, alle Werte selbst gemessen).
- Materialentnahmen (9175360): Zähler == SQL, Filter, Operanden-Gate, Query-Zahl 2 (kein N+1), 302. 🟢
- Gesamtfirma/Je-Abteilung/Geschäftsführung: reine Präsentation über die eine overview-Wahrheit,
  Werte 1:1, Zeitraum/Wähler greifen, Zustände real erreichbar. 🟢
- Reverb-Fix (c40a7aa): InvoiceDeletionGuardTest 9/9. 🟢
- P0 B2-SIDEBAR-1 (\$safeRoute-Backslash über 5 Commits) gefixt (2cd80bf): voller Wächter 704/704,
  0 failed, Arbeitsliste-Gruppe wieder grün, Admin-Seiten rendern die Sidebar fehlerfrei (HTTP 200). 🟢
- B2-UI-2 (WCAG-Kontrast) in allen 7 Views auf #6b7280, 0× #9ca3af. 🟢

**Lern-Merker (verankert):** nach Heredoc-/Skript-Edits an Blade/PHP IMMER Gegen-Grep auf `\$`
+ echten Rendered-Output prüfen — isolierte curl-302-/Controller-Abnahmen treffen den Sidebar-Render nie.

**Offen (nicht blockierend):** B2-UI-1 (Inline-Hex→Token, Controlling-weiter Posten),
B2-UI-3 (Pixel-Beleg 3 Viewports — via Screenshot-Zugriff nachholbar), Alt-Statuswert-Mapping Pipeline.
