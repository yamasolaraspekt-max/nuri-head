# Archiv-Manifest — Paket 2a: Angebotsreife-Gate im Erstellpfad (read-only)

**Datum:** 2026-07-12 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** `docs/bereich2-angebotsworkflow-konzept.md` (Kap. 4 Angebotsreife) · Paket 1 `1faeba7` · 1b-b `f0a8b01`

## Warum archiviert
Drei **Bestandscontroller** werden berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfade → Archivkopien
- `app/Http/Controllers/Customer/Offer/OfferWizardController.php` → `OfferWizardController.php.original`
- `app/Http/Controllers/Customer/Offer/OfferController.php`       → `OfferController.php.original`
- `app/Http/Controllers/Customer/Offer/OffersController.php`      → `OffersController.php.original`

## Was geändert/neu ist
**Neu (additiv):**
- `app/Services/Offer/OfferReadinessGate.php` — zentraler Guard/Resolver. READ-ONLY (liest nur
  `OfferReadinessService`), keine Persistenz, keine zweite Statuswahrheit. Regel: sperrt nur bei
  offenen **Blockern**, nur für **WP** (product_id=2), **kein Override**, **kein LPL ⇒ durchlassen**.
- `tests/Feature/Offer/OfferReadinessGateTest.php`.

**Bestandscontroller geändert — Guard-Aufruf VOR dem jeweiligen Create (5 WP-Erstellwege lückenlos):**
- `OfferWizardController::createOffer` — nach LPL-Auflösung, vor `DB::transaction`: Gate → 422 `OFFER_NOT_READY`.
- `OfferController::store` — nach Ermittlung customer/alternative/product, vor `DB::beginTransaction`: Gate → 422 via `errorResponse`.
- `OfferController::syncOfferLeadProducts` (Auto-Anlage via `data()`) — vor `Offer::create`: bei WP-Blocker
  `continue` (Zeile wird NICHT auto-materialisiert). *(Evaluator-Befund BLOCKER-1)*
- `OfferController::processOffer` (via `saveDocument()`, else-Zweig = neues Angebot) — vor `Offer::create`:
  `DB::rollBack()` + 422 via `errorResponse`. Update-/Restore-Zweige (bestehendes Angebot) bleiben ungegatet. *(Evaluator-Befund MAJOR-2)*
- `OffersController::store` — nach `validate`, vor `Offer::create`: Gate → 422 `OFFER_NOT_READY`.

Kein Import-Zwang: Gate wird per `app(OfferReadinessGate::class)` aufgelöst (minimaler Fußabdruck).

## Fehlerverhalten (Entscheidungen Paket 2a)
- WP + LPL gefunden + offene Blocker → **kein** `Offer::create`, HTTP **422** (JSON/AJAX),
  verständliche Meldung + `blocker`-Labels, **keine PII**.
- Non-WP → unverändert durchgelassen (Gate liefert `null`).
- WP, aber kein LPL auflösbar → unverändert durchgelassen (**Legacy-/ungegateter Fall**, siehe Abschlussbericht).

## Bewusst NICHT geändert / Nicht-Ziele
Keine Migration · keine neue Persistenz · keine Preislogik · kein Umbau der Angebotslogik (nur
Early-Return) · keine PV · keine Auslegung→sections · keine zweite Statuswahrheit · **kein Override**
(→ eigenes Paket 2b) · **Pfad 4 `OfferTemplatePickerController::useTemplate` NICHT gebaut** (nur
dokumentiert: `alternative_id` nullable ⇒ mehrdeutige LPL-Auflösung).

## Nachtrag (nach Evaluator, Yama-Scope-Entscheidung 2026-07-13)
Der Evaluator meldete zwei in der Startblock-Inventur übersehene, ungegatete WP-Erstellwege
(`syncOfferLeadProducts` :368, `processOffer` :2231). Yama-Entscheidung: **beide gaten** (2a erweitern),
mit demselben zentralen Guard. Damit sind alle 5 aktiven WP-Erstellwege abgedeckt. `useTemplate` (Pfad 4)
bleibt bewusst außen vor (Doku). Verhaltensänderung `syncOfferLeadProducts`: WP-Zeilen mit offenem Blocker
werden beim Listen-Laden **nicht mehr** automatisch zu Angeboten — gewünscht (entspricht dem Ziel).

## Rückweg (Notfall)
1. Die drei Controller aus diesem Archiv zurückspielen (bzw. `git revert`).
2. `OfferReadinessGate.php` + Test löschen. Rein additiv, kein DB-/Schemaeingriff → verlustfrei.
