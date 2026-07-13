# P3-d0 — WP-Katalog-Inventur & Gerät→Komponente-Regel (READ-ONLY Befund)

> **Status:** read-only Untersuchung. **Kein Bau, kein Commit, kein Push, keine Katalog-/Preis-/CatalogPriceGuard-Änderung, kein Schreiben in `offer_details.sections`.** Alle Aussagen sind an der lokalen DB (`ticket`) + am Code belegt — **keine Annahmen ohne Beleg.**
> **Zweck:** Klären, ob ein WP-Auslegungskandidat später **eindeutig** auf einen Katalog-/Set-/Komponenten-Anker (`component_id`) gemappt werden darf.
> **Bezug:** P3-c-preview (setzt bewusst keinen `component_id`) · Ziel-Wahrheiten Anforderungsprofil / `offer_details.sections` / P1-a-Preis · Datum 2026-07-13.

---

## 1. Inventur (belegt)

### 1.1 WP-Preis-Set
- **`master_sets` ag=2:** genau **ein** Set — id=2 „Wärmepumpe-Set Standard".
- **`master_set_components` (Set 2):** 3 Komponenten, alle `type=product`, `is_stammartikel=0`:

| comp_id | product_id | article_no | qty | VK (unit_price) | EK (purchase_price) |
|---|---|---|---|---|---|
| 4 | 9 | ART-0005 | 6,00 | 12.237,12 | 9.712,00 |
| 5 | 10 | ART-0006 | 5,00 | 10.227,62 | 7.358,00 |
| 6 | 11 | ART-0007 | 2,00 | 11.542,08 | 8.744,00 |

- **`products` 9/10/11** (`article_group=2`, `status=active`): echte WP-Geräte-Produkte —
  9 = „Luft-Wasser-Wärmepumpe **Stiebel Eltron** Standard" (brand 66), 10 = „**Viessmann** Komfort" (brand 64), 11 = „**Vaillant** Premium" (brand 65).
- **Aber:** `heatpump_type=NULL`, `scop=NULL`, **0** Einträge in `product_heat_pump_specs` → **keine Kennlinie/COP**. Die `qty`-Werte (6/5/2) sind für ein Gerät unplausibel → **Demo-/Seed-Charakter**.

### 1.2 Geräte-Kandidaten (Auslegung)
- **`product_heat_pump_specs`:** 19 Zeilen, `product_id` **101–119** (Spalten: `leistungskurve`, `heizleistung_*`, `cop_*`, `scop_35/55`, `max_vorlauf_c`, `modulation_*`, `geraetetyp`, `serie`, `kaeltemittel`).
- **`products` 101–119** (`article_group=2`): z. B. „**NIBE** S2125-8/-12/-14 …" — volle technische Daten, **aber `retail_price`/`purchase_price` = NULL** (unbepreist) und **in keinem `master_set_component`**.
- **`WaermepumpenMatchService::kandidaten()`** → `CatalogDeviceRepository::heatPumps()`:
  `product_heat_pump_specs s` leftJoin `products p` (`p.id=s.product_id`) leftJoin `brands b` → Kandidat trägt `s.*` + `p.model as modell` + `b.name as hersteller`. **Kandidaten-Identität = `product_id` (101–119) + Modell + Hersteller + Leistungskurve.**

### 1.3 Der Bruch (belegt)
| Prüfung | Ergebnis |
|---|---|
| Spec-Produkte (101–119) in `master_set_components`? | **0** |
| Set-Produkte (9/10/11) in `product_heat_pump_specs`? | **0** |
| Gemeinsames `product_id` zwischen Set und Specs? | **keins** (9–11 ↔ 101–119 disjunkt) |
| Gemeinsamer Hersteller? | **nein** (Stiebel/Viessmann/Vaillant ↔ NIBE) |
| Gemeinsame Leistung? | **nicht vergleichbar** (Set-Produkte ohne kW-Spec) |

→ **Der Match-Kandidat und die bepreiste Set-Komponente sind zwei getrennte, unbelegt zusammenhängende Populationen.**

---

## 2. Fachliche Bewertung `product_id` (Auflage 1)
- Ist `master_set_components.product_id` das **WP-Gerät**? — **Ja, gemeint** (type=product, WP-Geräteprodukte 9/10/11), aber **Demo-Daten** (implausible qty, keine Specs).
- Ist `product_heat_pump_specs.product_id` **dieselbe** Artikel-/Produkt-Wahrheit? — **Nein.** Anderer `product_id`-Bereich (101–119), andere Marke (NIBE), unbepreist, keine Set-Zugehörigkeit.
- Namens-/Hersteller-/Modell-/Leistungsbeleg für Gleichheit? — **Fehlt vollständig** (Hersteller widersprechen sich, Set-Produkte ohne Leistung).
- **Fazit:** `product_id` verbindet die beiden Seiten **nicht** → `product_id` **darf NICHT** als Auto-Mapping-Regel gelten (keine fachliche Eindeutigkeit).

---

## 3. Join-Schlüssel-Analyse
| Schlüssel | Eignung heute | Begründung |
|---|---|---|
| `product_id` | **untauglich** | Set (9–11) ↔ Specs (101–119) disjunkt |
| Artikelnummer (`article_no`) | untauglich | Set hat ART-0005..7; Spec-Produkte nicht im Set/kein gemeinsamer Bezug |
| Hersteller+Modell | untauglich | Marken überschneiden sich nicht (Stiebel/Viessmann/Vaillant ↔ NIBE) |
| Leistung kW (Toleranz) | untauglich | Set-Produkte tragen keine Leistungs-Spec |
| `component_id`/`set_id` | Ziel-Anker, kein Match-Schlüssel | wäre erst nach eindeutigem Match setzbar |
| Kennlinie | nur Plausibilisierung | nur Spec-Seite vorhanden |

**Eindeutige Regel Gerät→Komponente existiert heute nicht** (Schnittmenge leer).

---

## 4. Entscheidungsmatrix (Auflage 2)
| Fall | Befund (heute) | Auto-`component_id`? | UI-Verhalten | nächster Schritt |
|---|---|---|---|---|
| **Eindeutiger Treffer** | tritt aktuell **nicht** auf (Populationen disjunkt) | **JA — nur** wenn alle 4 Bedingungen* erfüllt | „automatisch zugeordnet (Regel …)" + Preis | (erst nach Datenkuratierung) |
| **Mehrere Treffer** | — | **NEIN** | Auswahlliste, `zu_bestaetigen` | manuelle Wahl (P3-d0a) |
| **Kein Treffer** | **aktueller Ist-Zustand** (kein gemeinsamer Schlüssel) | **NEIN** | `katalog_anker_fehlt` (wie P3-c), Aufgabe „Komponente wählen/anlegen" | Datenkuratierung + P3-d0a |
| **Set vorhanden, Gerät nicht eindeutig** | Set 2 da, aber 3 Varianten ohne Geräte-Bezug | **NEIN** | Set-Varianten anzeigen, manuelle Wahl | P3-d0a |
| **Gerät eindeutig, Komponente nicht bepreist** | Spec-Produkte 101–119 unbepreist | **NEIN** (kein Preis) | „nicht bepreist", Aufgabe | Preis/Set-Anker ergänzen |
| **product_id passt technisch, aber Name/Modell/Leistung widersprechen** | (Konfliktfall) | **NEIN** (Datenkonflikt) | Warnung „Konflikt — prüfen" | Datenbereinigung, nie Auto |
| **Nur Leistungs-Toleranz passt, kein Artikel-/Modellbezug** | (Näherungsfall) | **NEIN** | „ähnliches Gerät" als Vorschlag, `zu_bestaetigen` | manuelle Wahl |

\* Bedingungen für ein späteres „JA": (a) **deterministischer** Schlüssel liefert **genau eine** Komponente, (b) in einem **eindeutig führenden** WP-Set, (c) Komponente **bepreist**, (d) Name/Modell/Leistung **widerspruchsfrei** belegt.

---

## 5. Lückenliste (was fehlt für Auto-Mapping)
1. **Eine Produkt-Wahrheit:** WP-Geräte müssen **gleichzeitig** bepreist (Set-Komponente) **und** mit Kennlinie (`product_heat_pump_specs`) auf **demselben `product_id`** geführt sein. Heute getrennt.
2. **Kuratierte statt Demo-Daten:** Set 2 trägt Demo-Produkte (implausible qty, keine Specs); Spec-Produkte sind unbepreist. Reale WP-Geräte fehlen als konsolidierte Quelle.
3. **Führendes Set + Zuordnungsregel:** Definition, welches `master_set` je WP-Konfiguration führt und wie ein Kandidat 1:1 auf eine Komponente zeigt.
4. **Belegte Identität:** gemeinsamer, geprüfter Schlüssel (bevorzugt `product_id`) mit Name/Modell/Leistungs-Konsistenz.

---

## 6. Soll-Regel (Konzept, nicht bauen)
Später zulässig **nur**, wenn Datenlücken geschlossen sind:
1. Kandidat liefert `product_id` (aus `product_heat_pump_specs`).
2. Suche `master_set_components.product_id == Kandidat.product_id` im **führenden** WP-Set.
3. Genau **eine** Komponente **und** bepreist **und** Name/Modell/Leistung konsistent → `component_id` setzen (Kennzeichen „automatisch zugeordnet").
4. Sonst → **kein** Anker (`katalog_anker_fehlt`), manuelle Auswahl (Operanden-Gate: nie stiller Default).

---

## 7. UI-Darstellungs-Fälle (Konzept)
- **eindeutig gemappt:** Anker + Preis, Badge „automatisch zugeordnet (Regel product_id)".
- **mehrere Kandidaten:** Auswahl-Liste, `zu_bestaetigen`, kein Auto-Anker.
- **kein Treffer:** `katalog_anker_fehlt` (wie P3-c), Aufgabe „Komponente wählen/anlegen".
- **manuell erforderlich:** expliziter Auswahl-Schritt, kein stiller Default; bei Konflikt Warnung.

---

## 8. Klare Empfehlung
- **`component_id` automatisch setzen: NEIN.** Beleg: Geräte-Kandidaten (`product_heat_pump_specs` 101–119, NIBE, unbepreist) und bepreiste WP-Set-Komponenten (`master_set_components` product_id 9/10/11, Stiebel/Viessmann/Vaillant, ohne Specs) sind **disjunkt** — kein gemeinsamer `product_id`, kein gemeinsamer Hersteller, keine gemeinsame Leistung. Es gibt **keine** eindeutige, belegte Regel.
- **Nach welcher Regel bei „ja":** entfällt heute (siehe Soll-Regel §6 — erst nach Datenkuratierung).
- **Welche Daten müssen ergänzt werden:** §5 — eine konsolidierte WP-Produkt-Wahrheit (Preis **und** Kennlinie auf demselben `product_id`), kuratierte reale Geräte, führendes Set + Zuordnungsregel.
- **Nächster Bau-Slice: JA — P3-d0a als read-only Matching-Vorschau.** Sie zeigt je Kandidat den Join-Status (eindeutig / mehrere / kein Treffer / Set-ohne-Gerät / Gerät-unbepreist / Konflikt) und macht die Datenlücke im UI sichtbar — **setzt aber KEINEN `component_id`** und keinen Preis. Damit ist die Datenkuratierung prüfbar, bevor je ein Auto-Anker erlaubt wird.

---

## 9. Nicht-Ziele / Regeln (eingehalten)
Kein Bau · kein Commit · kein Push · keine Migration · keine Änderung an Katalogdaten/Preisen/`CatalogPriceGuard` · kein Schreiben in `offer_details.sections` · keine Annahme ohne Beleg. Reine Lese-Untersuchung; nur dieses Dokument neu (nicht committet).
