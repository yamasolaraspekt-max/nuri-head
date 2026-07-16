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
