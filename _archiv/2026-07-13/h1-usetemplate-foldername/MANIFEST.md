# Archiv-Manifest — Cleanup H1: useTemplate optionales folder_name null-sicher

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** Paket 2b `644eff5` (useTemplate-Gate) · H1-Vorbefund aus dem P3-d0a-Evaluator.

## Warum archiviert
Eine **committete Bestandsdatei** wird geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfad → Archivkopie
- `app/Http/Controllers/Customer/Offer/OfferTemplatePickerController.php` → `OfferTemplatePickerController.php.original`

## Was geändert ist (genau eine Zeile, defensiv)
`OfferTemplatePickerController::useTemplate`, Zeile ~377:
```
- 'name' => $validated['folder_name'] ?: $template->name . ' - ' . now()->format('d.m.Y H:i'),
+ 'name' => ($validated['folder_name'] ?? null) ?: $template->name . ' - ' . now()->format('d.m.Y H:i'),
```
Behebt „Undefined array key", wenn das **optional (`nullable`) validierte** `folder_name` im Request **ganz fehlt**.
Verhalten unverändert: gesetzt → Wert genutzt; leer/`null`/fehlend → Fallback = Template-Name.

## Nicht geändert / Nicht-Ziele
`OfferReadinessGate`/Reife-Logik · Preis/`component_id`/Katalog · Validierung · Transaktionslogik · andere
Controller/Services/Views/Routen · Migration. Kein Refactor, kein Push, kein `git add -A`.

## Tests
`tests/Feature/Offer/UseTemplateGateTest.php` (additiv): ohne `folder_name` → kein 500, Ordner mit
Template-Name-Fallback; mit `folder_name` → exakt übernommener Name.

## Rückweg (Notfall)
**Variante A:** `git revert` des H1-Commits. Alternativ die Zeile aus `.original` zurücksetzen (ein Edit).
Rein defensive Einzeiler-Änderung, kein DB-/Schemaeingriff → verlustfrei.
