# Archiv-Manifest — Paket 2b: useTemplate (Pfad 4) über OfferReadinessGate absichern

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** Paket 2a `fff4ba5` (+ Nachtrag `1b75b9d`) · schließt den in 2a bewusst ausgeklammerten Pfad 4.

## Warum archiviert
Ein **Bestandscontroller** wird berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfad → Archivkopie
- `app/Http/Controllers/Customer/Offer/OfferTemplatePickerController.php` → `OfferTemplatePickerController.php.original`

## Was geändert ist — nur `useTemplate`, ein Block VOR `DB::transaction`
1. **Objektbindung (Option B, Yama-Entscheidung 2026-07-13):** WP (`product_id=2`) **ohne** `alternative_id`
   → HTTP **422** `OFFER_OBJECT_REQUIRED`, Meldung „Bitte zuerst ein Objekt auswählen, bevor eine
   WP-Vorlage als Angebot verwendet wird." Begründung: WP-Angebot ist objektgebunden; kundenweite
   LPL-Auflösung wäre fachlich unsauber (könnte das falsche Objekt prüfen).
2. **Zentrales Reife-Gate:** `OfferReadinessGate::pruefe(customer, alternative, product)` — bei WP + offenen
   Blockern → **422** `OFFER_NOT_READY` + `blocker[]`. Kein neuer Gate-Code (Wiederverwendung aus 2a).

Beide Prüfungen stehen **vor** der Transaktion → bei Sperre entsteht **kein** Offer/OfferFolder/OfferDetail
und **kein** `usage_count`-Inkrement.

## Verhalten
- WP + `alternative_id=null` → 422 `OFFER_OBJECT_REQUIRED` (Option B).
- WP + Objekt + Blocker → 422 `OFFER_NOT_READY`.
- WP + Objekt + reif → Vorlage wird verwendet (Offer+Folder+Detail, usage_count +1).
- WP + Objekt, aber kein LPL auflösbar → durchlassen (Legacy, wie 2a).
- Non-WP → unverändert durchlassen.
- `check()` bleibt unberührt (read-only Vorschau, kein Erstellweg).

## Bewusst NICHT geändert / Nicht-Ziele
Keine Migration · keine Persistenz/zweite Statuswahrheit · keine Preislogik · kein Override · keine PV ·
keine Auslegung→sections · kein Umbau der Template-Logik (nur Early-Return) · kein Push.

## Rückweg (Notfall)
1. Controller aus diesem Archiv zurückspielen (bzw. `git revert`).
2. Test-Datei löschen. Rein additiv, kein DB-/Schemaeingriff → verlustfrei.
