# Bereich 2 — Folge-Posten (offen, nicht im aktuellen Scope)

**Stand:** 2026-07-12 · Sammelstelle für belegte, aber bewusst zurückgestellte Befunde aus Bereich 2.

---

## N1 — Legacy-Preiseinheits-Lücke im `OfferDetailsController` / `offer_product_lists`

- **Endpoint / Datei:** `app/Http/Controllers/Customer/Offer/OfferDetailsController.php` (Speicherung `offer_product_lists`), Felder `price_unit_value` / `cost_price_unit_value`; Validierung nur `min:0.0001`. Route über den **Legacy-Positions-Pfad** (`offer.details.*`, `routes/web.php:3543`), **nicht** über `processOffer`/`save-document`.
- **Risiko:** gleiche Klasse wie P1-a-B1 — der Preiseinheits-Divisor ist frei aus dem Payload setzbar (z. B. 1000 oder 0,0001) und wird serverseitig **nicht** gegen den Katalog kontrolliert. Über diesen Pfad bliebe eine Preis-/Summen-Manipulation möglich.
- **Warum außerhalb P1-a:** **präexistent** (nicht durch P1-a verursacht) und in einem **anderen Endpoint/Model** (`offer_product_lists` = die relationale Legacy-Zweit-Positionswahrheit, nicht `offer_details.sections`). P1-a hat bewusst nur den führenden Speicherpfad `processOffer` gehärtet (gesperrter Scope, Yama 2026-07-12).
- **Empfohlener späterer Umgang:** im Zuge der geplanten **`offer_product_lists`-Stilllegung** (Ziel-Wahrheit „nur `offer_details.sections`", Bereich-2-Bewertung Punkt 2) miterledigen — entweder Pfad stilllegen (Variante B, Archiv) **oder**, falls er vorher aktiv bleibt, denselben `CatalogPriceGuard`-Schutz (inkl. Divisor-Neutralisierung) analog anwenden. Als eigenes „Legacy-Pricing"-Paket führen, mit eigener Evaluator-Runde.
- **Herkunft:** P1-a-Evaluator, zweite Runde (2026-07-12), Befund N1.
