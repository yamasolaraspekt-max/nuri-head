# 20 · Fahrplan — Pakete zu je fünf Schritten

> **Rolle:** Planner · **Stand:** 2026-08-01 · **Heimat-App:** `ticket`
> **Grundlage:** Phase-A-Inventur (`01`–`04`), IDS-2.5.1-Schema, Open-Masterdata-YAML, Governance-Zyklus
> **Legende:** **BELEGT** · **BEWERTUNG** · **ANNAHME** · **OFFEN**

---

## 1 · Wie ich schneide — die sechs Regeln

Bevor die Liste kommt, die Regeln, nach denen sie entstanden ist. Ohne sie ist jede Reihenfolge beliebig.

**Regel 1 — Ein Schritt hat genau ein mechanisch prüfbares Abnahmekriterium.**
Nicht „Parser funktioniert", sondern „liest `Beispielwarenkorb_empfangen.xml`, validiert grün, liefert 10,44 €/m bei Menge 50". Ein Kriterium, das man diskutieren kann, ist keins. Das ist die Lehre aus AUF-38: ein unscharfes *Wort* fällt weniger auf als eine unscharfe Zahl, deshalb muss es messbar formuliert sein.

**Regel 2 — Jeder Schritt hat einen Rückweg ohne Datenmigration.**
Feature-Flag, oder Commit zurückdrehbar, oder additive Migration. Ein Schritt, dessen Rücknahme Daten kostet, wird geteilt, bis er es nicht mehr tut.

**Regel 3 — Höchstens ein Schritt mit hohem Risiko je Paket.**
Sonst kippen zwei gleichzeitig und man weiß nicht, welcher es war.

**Regel 4 — Kein Schritt hängt an einer Freigabe, die noch nicht da ist.**
Alles, was auf den DS-Zugang oder SwaggerHub wartet, gehört nicht ins laufende Paket. Sonst blockiert ein Schritt vier andere. Zugänge laufen als **Stufe 0 parallel**, nicht als Schritt.

**Regel 5 — Reihenfolge nach Abhängigkeit, nicht nach Wichtigkeit.**
E-Rechnung ist dringender als der Artikelschlüssel. Der Artikelschlüssel kommt trotzdem zuerst, weil vier spätere Schritte auf ihm stehen und E-Rechnung auf keinem.

**Regel 6 — Ein Paket endet in einem Zustand, in dem man aufhören könnte.**
Nach fünf Schritten ist das System besser und in sich stimmig, auch wenn nie ein sechster käme. Kein Paket hinterlässt eine halbe Struktur.

**Zusatzregel aus der Governance:** Planner ≠ Generator ≠ Evaluator. Jeder Schritt bekommt vorab seine **Spur** (A = voller Zyklus, B = Kurzspur). Der Generator stuft nicht selbst ein. Spurwechsel nur nach oben.

---

## 2 · Der Fahrplan im Ganzen — acht Pakete

| Paket | Titel | Ergebnis nach Abschluss | Risiko |
|---|---|---|---|
| **1** | **Fundament und Wahrheit** | Der Bestand ist gemessen, die Artikelidentität ist entschieden und im Schema erzwungen, tote Doppelwege sind weg | mittel |
| 2 | Produktkern | Produkt · Variante · Lieferantenartikel getrennt, `supplier_article_map` gefüllt, Herkunft an jedem Wert | hoch |
| 3 | Preis und Zeit | Preishistorie, Staffeln, Gültigkeiten, Preisbasis, Konditionen | mittel |
| 4 | IDS produktiv | Ein Hook mit Token, Shop-Profile, Warenkorb rein und raus, `RefItems` erhalten | hoch |
| 5 | Medien und Dokumente | `product_media` mit MD-Codes und Hash, Abruf auf Bedarf | gering |
| 6 | Open Masterdata | OAuth2-Verbindungen, Delta-Abruf, Konfliktbehandlung | mittel |
| 7 | Belege und E-Rechnung | ZUGFeRD-Eingang und -Ausgang, ODX-Kette | hoch |
| 8 | Intelligenz | Sets mit Preisautomatik, Kompatibilität, Ersatzartikel, Vergleich | mittel |

**Parallel dazu, ohne Bauaufwand — Stufe 0 (deine Strecke):**
DS-Space freischalten · SwaggerHub-Domain freigeben oder YAML laden · ODX-Zugang beim Verband anfordern · Testzugang bei einem zweiten Großhändler.
Diese vier blockieren nur Paket 6 und 7. Paket 1 bis 5 laufen ohne sie.

**BEWERTUNG zur Reihenfolge — mein Widerspruch zur Masterprompt-Roadmap.** Dort steht Stufe 1 Datenbereinigung vor Stufe 2 Produktkern. Ich drehe das um. Solange sechs Schreibpfade sechs verschiedene Identitätsbegriffe benutzen (`03-data-quality-report.md` §3.1), erzeugt der nächste Import dieselben Zwillinge, die man gerade weggeräumt hat. **Identität definieren und erzwingen, dann bereinigen** — sonst ist die Bereinigung verlorene Arbeit.

---

## 3 · Paket 1 — Fundament und Wahrheit

**Ziel des Pakets:** Nach diesen fünf Schritten wissen wir, was wirklich im Bestand ist, das System hat genau **einen** Begriff von Artikelidentität, dieser Begriff ist von der Datenbank erzwungen, und es gibt keine zwei konkurrierenden IDS-Wege mehr.

**Warum diese fünf und nicht andere:** Schritt 1 kostet nichts und macht alle folgenden Schätzungen belastbar. Schritt 2 räumt belegte Defekte weg, die niemanden etwas kosten, und probt den Zyklus an ungefährlichem Material. Schritt 3 ist die Entscheidung, an der vier spätere Pakete hängen. Schritt 4 ist der einzige Hochrisiko-Schritt des Pakets. Schritt 5 beseitigt die Doppelwahrheit, bevor darauf weitergebaut wird.

---

### Schritt 1 · Ist-Bestand messen

| | |
|---|---|
| **Ziel** | Die 47 Prüfabfragen aus `evidence/query-results/datenqualitaet-pruefabfragen.sql` einmal laufen lassen und das Ergebnis ablegen. |
| **Spur** | **B** — rein lesend, kein Datenpfad, keine Logik |
| **Rolle** | Yama führt aus · Planner wertet aus |
| **Abhängigkeit** | keine |
| **Abnahmekriterium** | `docs/product-data/evidence/query-results/ergebnis-2026-08.txt` existiert und enthält Ergebnisse zu allen zehn Abschnitten. Die in `03` als Sekundärbeleg geführten Zahlen sind entweder bestätigt oder korrigiert. |
| **Rückweg** | entfällt — nur `SELECT` |
| **Entdeckung** | Weicht eine Zahl um mehr als 20 % von der Sekundärquelle ab, ist die Inventur älter als gedacht und `03` wird nachgeführt. |
| **Aufwand** | ~15 Minuten |

**Warum zuerst:** Drei Entscheidungen in Schritt 3 hängen an der Dublettenzahl. Ohne diese Zahl entscheidet man ins Blaue. Und wenn sich herausstellt, dass `products` nur wenige hundert Zeilen hat, ändert das die Migrationsstrategie in Schritt 4 grundlegend.

---

### Schritt 2 · Belegte Nulldefekte beheben

| | |
|---|---|
| **Ziel** | Vier Fehler beseitigen, die belegt sind, nichts kosten und keinen Datenpfad berühren. |
| **Spur** | **B** — je Fehler ein benanntes Abnahmekriterium |
| **Rolle** | Generator setzt um, hakt selbst ab · eine Ledger-Zeile |
| **Abhängigkeit** | keine |

**Umfang, abschließend aufgezählt — kein anderer Fehler:**

| # | Defekt | Fundstelle | Behebung |
|---|---|---|---|
| a | Route zeigt auf nicht existierende Methode `forwardToShopInline` | `routes/web.php:520` | Route entfernen |
| b | `view('datanorm.upload')` — View existiert nur als `admin.datanorm.upload` ⇒ jeder Upload wirft eine Exception | `app/Http/Controllers/DatanormController.php:47` | View-Namen korrigieren |
| c | `'fusion_forms'` zweimal als Array-Schlüssel; der zweite gewinnt, `FUSION_FORMS_TOKEN` ist unerreichbar | `config/services.php:34,38` | zweiten Schlüssel umbenennen |
| d | `$fillable` in `ProductImage` verwirft das Metadatenfeld still | `app/Models/ProductImage.php:11` | Feld ergänzen |

| | |
|---|---|
| **Abnahmekriterium** | (a) `php artisan route:list` wirft keinen Fehler mehr für `ids.search.forward.inline`. (b) Ein Testupload rendert die View statt einer Exception. (c) `config('services.fusion_forms.token')` liefert nachweislich den erwarteten Wert. (d) Ein Test setzt das Feld und liest es zurück. |
| **Rückweg** | Commit zurückdrehbar, keine Datenmigration |
| **Entdeckung** | Testsuite bleibt grün — sonst war es kein Nulldefekt |
| **Aufwand** | klein |

**Warum hier:** Nicht wegen der Fehler, sondern wegen des Zyklus. Bevor Schritt 4 an die Struktur geht, soll die Rollentrennung einmal an Material geprobt sein, bei dem ein Fehler nichts kostet.

---

### Schritt 3 · Identität entscheiden und spezifizieren

| | |
|---|---|
| **Ziel** | Eine schriftliche, verbindliche Festlegung: Was identifiziert einen Artikel, welche Treffer dürfen automatisch zuordnen, welche nur vorschlagen. **Kein Code.** |
| **Spur** | **A** — die Entscheidung berührt Bestandsdaten und alle vier Kanäle |
| **Rolle** | Planner entwirft · **Yama entscheidet** · fachlicher Prüfer nimmt ab |
| **Abhängigkeit** | Schritt 1 (Dublettenzahl je Schlüssel) |

**Inhalt der Spezifikation:**

1. **Die Identitätsleiter** — welche Stufe automatisch zuordnen darf:
   GTIN → Hersteller + Herstellernummer → Lieferant + Lieferantennummer → Branchennummer + Land → normalisierter Text.
   *Vorschlag: Stufen 1–4 automatisch, Stufe 5 nur als Vorschlag zur Bestätigung.*
2. **Normalisierungsregel** — `TRIM`, Groß-/Kleinschreibung, führende Nullen, Sonderzeichen. Verbindlich für alle sechs Schreibpfade.
3. **Pflichtfeldkatalog** — was heißt „vollständiger Artikel"? Ohne diese Festlegung ist die Kennzahl aus dem Auftrag nicht bestimmbar (`03` §2).
4. **Führende Quelle je Feldgruppe** — technische Herstellerdaten, Einkaufspreis, Verkaufspreis, Verfügbarkeit.

| | |
|---|---|
| **Abnahmekriterium** | `docs/product-data/10-target-domain-model.md` existiert, enthält die Leiter als Entscheidungstabelle, den Pflichtfeldkatalog und die Normalisierungsregel — und ist von dir freigegeben. Jede der sechs heutigen Schreibpfad-Fundstellen ist darin namentlich zugeordnet. |
| **Rückweg** | Dokument, kein Code — jederzeit änderbar |
| **Entdeckung** | entfällt |
| **Aufwand** | mittel, überwiegend Entscheidung |

**Das ist der eigentliche Engpass des Pakets.** Alle vier folgenden Pakete stehen auf dieser einen Festlegung.

---

### Schritt 4 · Identität im Schema verankern

| | |
|---|---|
| **Ziel** | Die entschiedene Identität wird von der Datenbank erzwungen statt von sechs Importpfaden unterschiedlich interpretiert. |
| **Spur** | **A** — Schema, Bestandsdaten, abgeleitete Werte. **Der einzige Hochrisiko-Schritt dieses Pakets.** |
| **Rolle** | Planner spezifiziert · Generator setzt um · **unabhängiger Evaluator** misst nach |
| **Abhängigkeit** | Schritt 1 und 3 |

**Umfang:**

- Additive Migration an `products`: `manufacturer_article_no`, `supplier_article_no`, `gtin_normalized`, `identity_source` — alle `nullable`, keine Bestandsspalte wird angefasst.
- Ein **Unique-Index je entschiedenem Schlüssel**, zunächst als *Prüfung im Dry-Run*, nicht als DB-Constraint.
- Eine zentrale `ProductIdentityService`-Klasse, durch die **alle sechs** Schreibpfade laufen. Die sechs Pfade rufen sie auf, statt selbst zu entscheiden.

| | |
|---|---|
| **Abnahmekriterium** | Alle sechs in `03` §3.1 belegten Schreibpfade rufen nachweislich `ProductIdentityService` auf — im Test verriegelt, sodass ein sechster Pfad ohne Aufruf den Test rot macht. Ein Import derselben Datei zweimal erzeugt nachweislich **einen** Artikel, nicht zwei. Die Testsuite bleibt grün, inklusive der Referenzfall-Tests. |
| **Rückweg** | Migration additiv und `nullable` ⇒ Commit zurückdrehbar ohne Datenverlust. Der Service ist per Config abschaltbar; bei `false` verhalten sich die Pfade wie heute. **Vor dem Beginn: aktueller Stand gepusht** — vor dem Deploy ist der Remote die einzige Kopie außerhalb der Maschine. |
| **Entdeckung** | Zähler „Artikel ohne Identität nach Stufe 1–4" und „Vorschläge auf Stufe 5 offen". Steigt der erste, greift die Leiter nicht. |
| **Aufwand** | groß |

**Kantenliste für die Spezifikation:** führende Nullen in Artikelnummern · GTIN mit 8/12/13/14 Stellen · derselbe Artikel bei zwei Lieferanten · Artikel ohne Hersteller (heute springt `IdsMapper.php:35-39` dann aus) · Groß-/Kleinschreibung · gleiche EAN bei verschiedenen Herstellern (echter Konflikt, darf **nicht** automatisch zusammenführen).

---

### Schritt 5 · Doppelwahrheit stilllegen

| | |
|---|---|
| **Ziel** | Ein IDS-Weg statt vier, toter Bestand markiert oder entfernt. |
| **Spur** | **A** — Routen und Endpunkte entfallen |
| **Rolle** | Planner listet · Generator setzt um · Evaluator prüft, dass nichts Benutztes wegfällt |
| **Abhängigkeit** | keine fachliche; sinnvoll nach Schritt 2 |

**Umfang:**

- **Weg A stilllegen** (`gconline`): `IdsController`, `IdsSearchController`, `ImportedIdsItemController`, `IdsItemsImported`, `resources/views/ids/*`, `resources/js/ids-listener.js`, 12 Routen, Broadcast-Kanal `ids`, Sidebar-Eintrag, vier tote CSRF-Ausnahmen.
- **Weg D stilllegen** (`OfferTemplateSupplierController`, 765 Zeilen mit eigenem Parser) — oder auf den zentralen Service umhängen. Entscheidung nötig.
- **Tabelle `imported_ids_items` bleibt stehen** (0 Zeilen). Ein DROP ist ein eigener, ausdrücklich beauftragter Posten, kein Beifang.
- **Toter Bestand markieren**, nicht löschen: `customer_products`, `product_favorites`, `stamp_articles`, `distributor_product`, `group_sets`, `radiator_connection_factors`.
- **Klartext-Zugangsdaten entfernen:** `IDS_*` aus `.env` und die hart eincodierten Testzugänge in `resources/views/admin/product/ids/ids.blade.php:70-78`.

| | |
|---|---|
| **Abnahmekriterium** | `grep -rn "imported_ids_items\|IdsController" app/ routes/ resources/` liefert null Treffer. Die Testsuite bleibt grün. `php artisan route:list` zeigt genau **einen** IDS-Rücklauf-Endpunkt. Kein Klartext-Passwort mehr im Repository (`grep -rn "pw_kunde.*value=" resources/` = leer). |
| **Rückweg** | Reine Entfernung, Commit zurückdrehbar. Keine Datenmigration, weil alle betroffenen Tabellen 0 Zeilen haben (Schritt 1 bestätigt das). |
| **Entdeckung** | 404-Zähler auf den entfernten Routen für 14 Tage. Ruft sie jemand auf, war der Weg doch benutzt. |
| **Aufwand** | mittel |

> **Hinweis zur Ausführung:** `rm` ist über die Dateibrücke gesperrt. Die Entfernung braucht entweder ein `git rm`-Skript, das du ausführst, oder du legst den Branch selbst an. Das ist eine Werkzeugfrage, keine fachliche.

---

## 4 · Wann ist Paket 1 „erledigt"

Alle fünf Abnahmekriterien sind vom **Evaluator** belegt abgehakt — mit Rohausgabe, nicht mit Prosa. Zusätzlich der Wächter:

- Testsuite selbst ausgeführt und grün, inklusive Referenzfall-Tests
- keine Regression an Bestandsdaten
- Rückweg vorhanden und erprobt
- Schreib-Heimat eingehalten (`ticket`)
- keine verwaisten zweiten Wahrheiten

**Fällt ein Schritt rot:** Die Nachbesserung hat Vorrang vor dem nächsten Schritt. Kein Weiterrücken auf Zuruf.

**Erst danach** wird Paket 2 geschnitten — und zwar mit den Zahlen aus Schritt 1 und der Entscheidung aus Schritt 3 in der Hand, nicht vorher. Ein Paket 2, das ich heute schon detaillierte, wäre eine Vermutung.

---

## 5 · Was ich in Paket 2 erwarte (Skizze, nicht beauftragt)

Damit die Richtung erkennbar ist — die Ausformulierung folgt nach Abschluss von Paket 1:

0. Aufräumposten aus der `sku`-Klärung: `offer_product_lists.sku` → `position_role`, `HeatpumpSeeder`-Schlüsselung, `match_by='sku'`, vier `safeColumn`-Prüfungen (`10-target-domain-model.md` §4a)
1. IDS-Parser + XSD-Validator, isoliert, gegen die echten ITEK-Beispieldateien
2. `supplier_article_map` durch den Identitätsservice füllen
3. Produkt · Variante · Lieferantenartikel trennen
4. Herkunft je Feld (`imported_from`, `import_batch_id`, Quelle) auf den Handelskanal ausdehnen
5. Ein Hook-Endpunkt mit Einmal-Token, Ablaufzeit und Ownership-Prüfung

---

## 6 · Entscheidungsbedarf vor dem Start

| # | Frage | Blockiert |
|---|---|---|
| 1 | Darf Stufe 5 der Identitätsleiter bei exakter Textübereinstimmung automatisch zuordnen — oder bleibt sie reiner Vorschlag? | Schritt 3, 4 |
| 2 | Pflichtfeldkatalog: Was ist ein „vollständiger Artikel"? | Schritt 3 |
| 3 | Gilt die Live-DB `ticket` als maßgeblich oder `ticket_testing`? | Schritt 1, 4 |
| 4 | Weg D (Angebotsvorlagen-Punchout): stilllegen oder auf den zentralen Service umhängen? | Schritt 5 |
| 5 | Zweiter Lieferantenstack in der App *wberechnung* — konsolidieren oder koexistieren? | Paket 2 |

Frage 1 bis 4 brauche ich vor dem Start von Paket 1. Frage 5 kann bis Paket 2 warten.
