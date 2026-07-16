# P3-d1 — Entscheidung: Katalog-Kuratierung vs. manuelle Matching-Auswahl (READ-ONLY)

> **Status:** read-only Analyse + Empfehlung. **Kein Bau, kein Commit, kein Push, keine Migration, keine Katalogänderung, kein Schreiben in `offer_details.sections`, keine neue Mapping-Tabelle.** Alle Zahlen an lokaler DB (`ticket`) + Code belegt — keine Annahme.
> **Bezug:** P3-d0 (`docs/bereich2-p3d0-wp-katalog-inventur.md`) · P3-d0a (`c1cab4e`) · Datum 2026-07-13.

---

## 1. Ist-Belege (die Entscheidungsgrundlage)
| Fakt | Wert |
|---|---|
| WP-Produkte gesamt (`article_group=2`) | **22** |
| davon mit **Preis** (VK oder EK > 0) | **3** (die Set-Produkte 9/10/11 — Demo) |
| davon mit **Kennlinie/Specs** (`product_heat_pump_specs`) | **19** (product_id 101–119, aus `wberechnung` importiert) |
| davon mit **BEIDEM** (Preis **und** Specs) | **0** |
| WP-`master_sets` (ag=2) | 1 · Komponenten: 3 |
| Herkunft der Specs | real: `products.imported_from='wberechnung'`, `WberechnungWpKurvenSeeder` (Kennlinien) |
| Preis der 19 Spec-Produkte | **0 von 19 bepreist** |

**Kernaussage:** Es gibt **kein einziges** WP-Produkt, das gleichzeitig Preis **und** Kennlinie trägt. Die technische Wahrheit (Specs, real) und die Preis-Wahrheit (Demo-Set) liegen auf **disjunkten** `product_id` — sogar mit verschiedenen Herstellern (NIBE ↔ Stiebel/Viessmann/Vaillant).

**Vorhandene Katalog-Pflege (Reuse, kein Neubau nötig):** `ProductController` (`product.edit`/`product.update`, Z. 2296-2297), `Product/ProductWPController`, `ProductImportController` (`products.import.store`), `DistributorPriceController` (Distributor-Preise), `Planner/PlannerMasterSetController` (Master-Set-Editor).

---

## 2. Optionen im Vergleich

### Bewertungsmatrix
| Kriterium | A — Kuratierung zuerst | B — manuelle Auswahl zuerst | C — Hybrid (Minimal-Kuratierung → dann manuelle Auswahl) |
|---|---|---|---|
| **Fachliche Richtigkeit** | hoch (eine Produkt-Wahrheit) | **niedrig auf Ist-Daten** (Zuordnung über disjunkte Populationen) | hoch, sobald kuratierte Teilmenge existiert |
| **Risiko falscher Preise** | gering | **hoch** (Falsch-Anker: NIBE-Gerät ↔ Viessmann-Preis) | gering (Auswahl nur unter kuratierten Produkten) |
| **Aufwand** | mittel (Daten: Preise an reale Spec-Produkte / WP-Set kuratieren) — nutzt bestehende Pflege-UI | gering (nur UI), aber Ergebnis unbrauchbar | mittel: kleine Kuratierung + beschränkte Auswahl-UI |
| **Datenmodell-Auswirkung** | **keine neue Tabelle** — Preis additiv an bestehende `products`/`master_set_components`; Join bleibt `product_id` | keine Tabelle nötig, aber Anker landet auf falschem Produkt | **keine neue Tabelle**; Join `product_id` auf kuratierter Teilmenge |
| **UX-Auswirkung** | Nutzer sieht später **eindeutige**, bepreiste Vorschläge | Nutzer trifft folgenreiche Fehlzuordnung ohne Basis | Nutzer wählt **nur** unter validen (Preis+Specs) Produkten; klarer, ehrlicher |
| **Testbarkeit im Browser** | Reife/Preis erst nach Kuratierung sichtbar | sofort klickbar, aber prüft nichts Sinnvolles | schrittweise: erst kuratierte Produkte sichtbar, dann Auswahl prüfbar |
| **Rückfallpfad** | additive Preis-Daten (DAUERDIREKTIVE) → belegt rücknehmbar als eigener Posten | UI additiv, aber falsche Anker müssten bereinigt werden | additive Daten + additive UI → verlustfrei |
| **Sichtbarer Nutzen zuerst** | erst nach Datenarbeit | sofort (aber wertlos/riskant) | **schnell** mit sauberer Basis (kleine Kuratierung genügt) |
| **Langfristig sauberste Architektur** | **Ja** (eine Wahrheit) | Nein (zementiert Falsch-Anker) | **Ja** (Kuratierung ist der Zielzustand, Auswahl nur Übergang) |

### Kurzfazit je Option
- **A** ist fachlich richtig, aber „ganz zuerst voll kuratieren" verzögert sichtbaren Nutzen.
- **B** ist auf der **belegten** Ist-Datenlage (0 Überschneidung) **fachlich falsch**: der Nutzer würde ein technisches Gerät an eine fremde Preis-Komponente hängen → systematischer Falsch-Anker. **Nicht empfohlen.**
- **C** verbindet beides: eine **kleine, saubere** WP-Produktmenge (Preis **und** Specs je `product_id`) schaffen — damit Auswahl/Matching überhaupt *bedeutungsvoll* wird — und die manuelle Auswahl **strikt auf kuratierte Produkte** beschränken.

---

## 3. Empfehlung

### Welche Option zuerst?
**Option C (Hybrid), mit Kuratierung als erstem Schritt.** Konkret die Reihenfolge:
1. **P3-d2 — Minimal-Kuratierung (beauftragter, additiver Katalog-Datenposten):** eine kleine Menge **realer** WP-Geräte so herstellen, dass Preis **und** Kennlinie auf **demselben `product_id`** liegen — bevorzugt, indem den bereits real importierten Spec-Produkten (`imported_from='wberechnung'`) Preise ergänzt werden (über bestehende `DistributorPriceController`/`ProductController`), bzw. ein kuratiertes WP-`master_set` aus diesen Produkten gebildet wird. **Additiv** (DAUERDIREKTIVE), Demo-Set unangetastet als separater Alt-Posten. Eigener Startblock + Evaluator.
2. **P3-d3 — manuelle Matching-Auswahl (read→write-Slice), beschränkt auf kuratierte Produkte:** UI zeigt nur Produkte mit Preis **und** Specs; Nutzer bestätigt die Zuordnung (Vorschlag+Bestätigung); `component_id` wird **nur** für solche Produkte gesetzt.
3. **Auto-`component_id`: weiterhin NEIN**, bis ein Produkt nachweislich kuratiert ist (Preis+Specs+konsistent, article_group=2) — dann erst nach der P3-d0-4-Bedingungs-Regel.

### Warum?
Weil **belegt 0 von 22** WP-Produkten Preis+Specs tragen: jede manuelle Zuordnung auf heutiger Datenlage erzeugt einen Falsch-Anker zwischen nicht zusammengehörenden Produkten. Eine **kleine** Kuratierung (nicht das ganze Sortiment) genügt, um sofort **sauberen** Nutzen zu liefern, nutzt **bestehende** Pflege-UI (kein Neubau), braucht **keine neue Tabelle** (Join bleibt `product_id`) und ist der langfristige Zielzustand.

### Welcher nächste Bau-Slice?
**P3-d2 — Minimal-Kuratierung.** Zuerst read-only Startblock: welche realen Spec-Produkte eignen sich, woher kommen Preise (Distributor/Herstellerliste), Umfang (z. B. 3–6 Geräte), additive Schreibweise, Prüfweg. **Erst danach** P3-d3 (Auswahl-UI). *(P3-d2 ist ein Katalog-Datenposten → eigene Yama-Freigabe, DAUERDIREKTIVE-konform, additiv, mit Beleg/Trail.)*

### Was bleibt verboten?
- **Auto-`component_id`** (bis kuratiert + 4-Bedingungs-Regel erfüllt).
- **Schätzpreise / Hersteller- oder Leistungs-Toleranz als Ersatzregel.**
- **Cross-Population-Mapping** (Gerät eines `product_id` an Preis eines anderen `product_id`).
- **Neue Mapping-Tabelle** (Zuordnung läuft über `product_id` auf Bestandstabellen).
- Änderung an **`CatalogPriceGuard`** / Preislogik · Schreiben in **`offer_details.sections`** ohne separaten, freigegebenen Slice.
- **Destruktive** Katalogänderung (UPDATE/DELETE an Bestand nur als eigener beauftragter Posten).

### Welche Daten dürfen NIEMALS automatisch gemappt werden?
1. Produkte, deren **Preis und Specs auf verschiedenen `product_id`** liegen (die heutige Ist-Lage).
2. **Demo-/Seed-Produkte** (die 3 bepreisten Set-Produkte 9/10/11 ohne Kennlinie).
3. Zuordnungen allein über **Hersteller** oder **Leistungs-Toleranz** ohne Artikel-/Modell-Identität.
4. Gemappte `product_id`, deren Produkt **nicht als WP klassifiziert** ist (`article_group ≠ 2`) — der Konfliktfall.
5. Alles, was **nicht** die vier Bedingungen (deterministischer Schlüssel → genau eine Komponente, führendes Set, bepreist, widerspruchsfrei belegt) erfüllt.

---

## 4. Nicht-Ziele (dieses Dokument)
Kein Bau · keine Katalog-/Datenänderung · keine Migration · keine neue Mapping-Tabelle · kein Schreiben in `offer_details.sections`/`component_id` · kein Commit · kein Push. Reine Analyse; nur dieses Dokument neu (nicht committet).
