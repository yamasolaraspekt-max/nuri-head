# Archiv-Manifest — Cleanup H2: useTemplate alternative_id validieren/absichern

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** Paket 2b `644eff5` (useTemplate-Gate) · H1 `ee6332a` · H2-Vorbefund (alternative_id ohne exists/Zugehörigkeit).

## Warum archiviert
Eine **committete Bestandsdatei** wird geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfad → Archivkopie
- `app/Http/Controllers/Customer/Offer/OfferTemplatePickerController.php` → `OfferTemplatePickerController.php.original`

## Was geändert ist (additiv, defensiv)
`OfferTemplatePickerController`:
- **`useTemplate`** — Validierung `alternative_id` um `exists:lead_alternative_adds,id` ergänzt (fängt nicht
  existente IDs); zusätzlich **Zugehörigkeits-Guard**: wenn `alternative_id !== null` und nicht zum Kunden
  gehört (`lead_id != customer_id`) → **422 `OFFER_OBJECT_INVALID`** (keine Anlage). Verhindert stille
  Fehlbindung, Fremdkunden-Bindung und die Gate-Umgehung über kaputte IDs.
- **`check()`** — nur `exists:lead_alternative_adds,id` ergänzt (read-only Vorschau; **keine** neue Logik,
  **keine** Zugehörigkeitsprüfung).

## Nicht geändert / Nicht-Ziele
`OfferReadinessGate`/Reife-Logik · Preis/`component_id`/Katalog · Transaktions-/Anlagelogik · andere
Controller/Services/Views/Routen · Migration. Kein Refactor, kein Push, kein `git add -A`.
`null` bleibt erlaubt (WP→`OFFER_OBJECT_REQUIRED`, Non-WP→durch).

## Tests
`tests/Feature/Offer/UseTemplateGateTest.php` (additiv): null (Non-WP) durch · gültiges eigenes Objekt →
Vorlage genutzt · nicht existente `alternative_id` → 422, keine Anlage · fremdes Objekt → 422
`OFFER_OBJECT_INVALID`, keine Anlage.

## Rückweg (Notfall)
**Variante A:** `git revert` des H2-Commits. Alternativ die Datei aus `.original` zurückspielen.
Additiv-defensive Validierung/Guard, kein DB-/Schemaeingriff → verlustfrei.
