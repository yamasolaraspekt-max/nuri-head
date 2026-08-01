# 10 · Ziel-Domänenmodell — Artikelidentität

> **Rolle:** Planner · **Status: ENTWURF ZUR ENTSCHEIDUNG** — noch nicht freigegeben, kein Code
> **Stand:** 2026-08-01 · **Heimat-App:** `ticket` · **Spur:** A
> **Gehört zu:** Paket 1, Schritt 3 (`20-implementation-roadmap.md`)
> **Legende:** **BELEGT** · **BEWERTUNG** · **ANNAHME** · **OFFEN**

---

## 0 · Zwei Funde, die den Entwurf geprägt haben

Ich habe die Schreibpfade selbst nachgelesen, statt dem Prüfbericht zu glauben. Zwei Dinge kamen dabei heraus, die die Spezifikation deutlich vereinfachen. Ein dritter Fund kam später hinzu und ist als Korrektur in Abschnitt 4a festgehalten.

**Fund 1 — `products` hat null Indizes. BELEGT.**
`grep -rn "unique(\|index(" database/migrations/*products*` liefert **keinen einzigen Treffer**. Kein Unique, kein regulärer Index, auf keiner der sechs Produkt-Migrationen. Es gibt an dieser Tabelle also nicht nur keinen erzwungenen Schlüssel — es gibt auch keine Indexunterstützung für die Suchen, die die sechs Schreibpfade bei jedem Import ausführen.

**Fund 2 — ein Pfad ist bereits eine Leiter. BELEGT.**
`app/Services/Suppliers/SupplierProductImportService.php:209-238`, Methode `resolveProduct()`:

```php
if (!empty($productData['ean'])) {                        // Stufe 1
    $product = Product::where('ean', $productData['ean'])->first();
    if ($product) return $product;
}
if (!empty($productData['article_no']) && $productData['article_no'] !== 'Not filled') {
    $product = Product::where('article_no', $productData['article_no'])->first();   // Stufe 2
    if ($product) return $product;
}
if (!empty($priceData['article_no'])) {                   // Stufe 3
    $price = DistributorPrice::where('distributor_id', $distributor->id)
        ->where('article_no', $priceData['article_no'])->with('product')->first();
    if ($price && $price->product) return $price->product;
}
return null;
```

**BEWERTUNG.** Das ist bereits das richtige Muster: geordnete Stufen, erste Übereinstimmung gewinnt, sauberes `null` bei Misserfolg. Es fehlen nur drei Dinge — Normalisierung, die Trennung von Hersteller- und Lieferantennummer, und die Verbindlichkeit für die anderen vier Pfade.

**Das ändert den Charakter des Vorhabens.** Wir führen kein fremdes Konzept ein, sondern erheben ein vorhandenes, halbfertiges zur allgemeinen Regel. Das senkt das Risiko von Schritt 4 erheblich.

---

## 1 · Die sechs Pfade — verifiziert

> **Korrektur zu Fassung 1:** Es sind sechs, nicht fünf. Pfad 6 kam durch die `sku`-Prüfung hinzu (Abschnitt 4a).

| # | Pfad | Schlüssel | Fundstelle | Normalisiert? |
|---|---|---|---|---|
| 1 | Lieferantenimport (Leiter, s.o.) | EAN → `article_no` → `(distributor_id, article_no)` | `SupplierProductImportService.php:209-238` | **nein** |
| 2 | IDS-Altpfad | `article_no` via `firstOrCreate` | `IdsController.php:225` | **nein** |
| 3 | Spec-Import (Auslegung) | `brand_id` + `model` | `SpecImportService.php:340-353` | **nein** |
| 4 | CSV-Produktimport | `article_no`, sonst **Produktname** | `ProductImportController.php:130-136` | **nein** |
| 5 | Lieferanten-Kreuztabelle | `(hersteller, herst_artikelnr, supplier_channel)` — **mit Unique** | `2026_07_04_140007_create_supplier_article_map_table.php` | **nein** |
| **6** | **Seeder Wärmepumpen** | **`sku`** — und setzt zugleich `article_no = sku` | `database/seeders/HeatpumpSeeder.php:86-89` | **nein** |

**Pfad 4 ist der gefährlichste. BELEGT:**
```php
if (! $product && $productName !== '') {
    $product = Product::where('product', $productName)->first();
}
```
Zwei Artikel mit identischem Namen und verschiedenen Nummern werden zu einem verschmolzen. Das ist keine Zuordnung, das ist Datenverlust.

**Pfad 5 macht es richtig** — der Unique `supplier_article_map_neutral_key` über `(hersteller, herst_artikelnr, supplier_channel)` ist der einzige erzwungene Identitätsschlüssel im ganzen System.

---

## 2 · Teil 1 — Die Identitätsleiter

**Grundsatz:** Geordnete Stufen. Die erste Übereinstimmung gewinnt. Kein Treffer heißt **neuer Artikel**, nicht „irgendwie zuordnen".

| Stufe | Schlüssel | Eindeutigkeit | Vorschlag: darf … |
|---|---|---|---|
| **1** | **GTIN** (normalisiert, Prüfziffer gültig) | weltweit | automatisch zuordnen |
| **2** | **Hersteller + Herstellerartikelnummer** | je Hersteller | automatisch zuordnen |
| **3** | **Lieferant + Lieferantenartikelnummer** | je Lieferant | automatisch zuordnen |
| **4** | **Branchennummer + Land + Branche** (OMD `branch[]`) | je Branche und Land | automatisch zuordnen |
| **5** | **normalisierter Textvergleich** auf Artikelnummer | keine | **nur vorschlagen** ← *hier liegt deine Entscheidung* |
| — | Produktname | keine | **nie** — Pfad 4 wird ersatzlos gestrichen |

### Der Abbruch nach oben — wichtiger als die Stufen selbst

**Ein Treffer auf einer Stufe wird verworfen, wenn eine höhere Stufe widerspricht.**

Beispiel: Stufe 3 findet über `(Lieferant, 4711)` den Artikel A. Der eingehende Datensatz trägt aber GTIN `4012345678901`, Artikel A trägt GTIN `4012345000009`. Zwei verschiedene GTIN heißt: zwei verschiedene Artikel. Der Treffer wird **verworfen** und als Konflikt gemeldet — nicht überschrieben.

Ohne diese Regel ist eine Leiter gefährlicher als gar keine, weil sie falsche Zuordnungen mit dem Anschein von Verlässlichkeit erzeugt.

### Was bei „kein Treffer" passiert

Neuer Artikel mit `verifikations_status = 'importiert_ungeprueft'` und `imported_from = '<kanal>:<verbindung>'`. **Beide Felder existieren bereits** — `2026_07_05_150006` und `2026_07_05_150007`. Die Herkunftsschicht muss nicht gebaut werden, sie muss nur benutzt werden.

---

## 3 · Teil 2 — Die Normalisierungsregel

Verbindlich für **alle** Pfade, beim Schreiben **und** beim Suchen.

| Feld | Regel | Beispiel |
|---|---|---|
| **GTIN** | `TRIM`, alle Nicht-Ziffern entfernen, auf **14 Stellen linksseitig mit Nullen auffüllen**, Prüfziffer nach GS1 validieren | `4012345678901` → `04012345678901` |
| **Artikelnummer** (Hersteller, Lieferant, intern) | `TRIM`, Mehrfach-Leerzeichen zu einem, Großbuchstaben, **führende Nullen bleiben erhalten** | `" ab-4711 "` → `AB-4711` |
| **Herstellername** | `TRIM`, Mehrfach-Leerzeichen zu einem, Vergleich case-insensitiv | `" viessmann "` → `Viessmann` |
| **Modell** | `TRIM`, Mehrfach-Leerzeichen zu einem, Groß-/Kleinschreibung erhalten, Vergleich case-insensitiv | |

### Warum führende Nullen bleiben — und das kein Detail ist

Bei GTIN sind führende Nullen **bedeutungslos** (EAN-8, EAN-13 und GTIN-14 sind dasselbe Nummernsystem in verschiedener Schreibweise) — deshalb auf 14 Stellen auffüllen und danach vergleichen.

Bei Artikelnummern sind führende Nullen **bedeutungstragend**: `0815` und `815` können zwei verschiedene Artikel sein. Wer hier normalisiert, verschmilzt Artikel.

Zwei entgegengesetzte Regeln für zwei Felder, die beide „Nummer" heißen. Das ist die häufigste Fehlerquelle bei genau dieser Aufgabe.

### Der Kollations-Fallstrick — BELEGT

`config/database.php`: `utf8mb4_unicode_ci`. Diese Kollation ist

- **case-insensitiv** → `where('name', 'viessmann')` findet `Viessmann`
- **PAD SPACE** → *nachlaufende* Leerzeichen werden beim Vergleich ignoriert: `'4711  '` findet `'4711'`
- aber **nicht** leading-space-tolerant → `' 4711'` findet `'4711'` **nicht**

**BEWERTUNG.** Die Datenbank erledigt heute zufällig einen Teil der Normalisierung — und genau deshalb fällt das Fehlen der übrigen nicht auf. Sich darauf zu verlassen ist brüchig: die Kollation kann sich bei einer Migration ändern, und in PHP verglichen (etwa beim Dedup im Speicher) gilt sie gar nicht. **Normalisierung gehört in den Code, nicht in die Kollation.**

---

## 4 · Teil 3 — Fallen im Bestand

| Falle | Fundstelle | Behandlung im Zielmodell |
|---|---|---|
| **Sentinel-Wert `'Not filled'`** wird als Artikelnummer geführt und nur an *einer* Stelle abgefangen | `SupplierProductImportService.php:219` | Zentrale Sentinel-Liste (`'Not filled'`, `'-'`, `'n/a'`, `'0'`, `''`) ⇒ gilt als **leer**, nie als Schlüssel |
| **`brands.type`** ist mal `'brand'` (Default), mal `'manufacturer'` (Import) | `create_brands_table`; `SupplierProductImportService.php:200-206` | Hersteller und Marke sind **nicht dasselbe**. Stufe 2 der Leiter braucht den Hersteller — die Trennung ist zu entscheiden |
| **`sku` trägt drei verschiedene Bedeutungen** — siehe Abschnitt 4a | `2025_08_26_075553_*` u. a. | Bedeutung je Tabelle verbindlich festschreiben: an `products` = **Lieferanten-Artikelnummer** (Leiterstufe 3) |
| **`supplier_article_map.hersteller` ist ein String**, kein Fremdschlüssel auf `brands` | `2026_07_04_140007_*` | Bewusste Entkopplung („neutraler Schlüssel"). Beibehalten — aber die Normalisierungsregel gilt auch dort |

---

## 4a · `sku` — Korrektur eines eigenen Befunds

**Ich hatte in der ersten Fassung geschrieben, `sku` werde „von keinem Pfad benutzt". Das war falsch** und beruhte darauf, dass ich nur die fünf *Identitäts-Schreibpfade* geprüft hatte. Eine vollständige Suche über `app/`, `resources/`, `database/` ergibt rund 40 Fundstellen. Der wahre Befund ist unangenehmer als „unbenutzt":

### `sku` bedeutet an drei Orten drei verschiedene Dinge — BELEGT

| Ort | Bedeutung | Beleg |
|---|---|---|
| `products.sku` | **Zweitname der Artikelnummer**. `HeatpumpSeeder.php:86` benutzt `updateOrCreate(['sku' => …])` und setzt im selben Aufruf `article_no = sku` — identischer Wert in beiden Spalten. | `database/seeders/HeatpumpSeeder.php:86-89` |
| `offer_product_lists.sku` | **Rollenkennung einer Sammelposition**, keine Artikelnummer. Werte wie `SET-18-MAT`, `SET-18-WORK`, `TOOLS`. | `2025_08_27_122931_*:25` (Kommentar `// "SKU SET-18-MAT"`); `resources/views/admin/offer/offer/configuration/wp/index.blade.php:1703-1740` |
| Import-Zwischenzeile im Connector | **Lieferantenartikelnummer** | `app/Services/Suppliers/SupplierConnectorService.php:675` — `'sku' => $distributorArticleNo` |

### Weitere belegte Befunde

- **Ein sechster Identitätspfad, den ich übersehen hatte:** `HeatpumpSeeder.php:86` schlüsselt auf `sku`. Damit sind es nicht fünf, sondern **sechs** konkurrierende Identitätsbegriffe.
- **`sku` ist als Zuordnungsschlüssel konfigurierbar:** `SupplierConnectionController.php:832` lässt `match_by ∈ {ean, sku, article_no, supplier_article_number}` zu. Ein Administrator kann also einen Lieferantenimport auf `sku` matchen lassen — auf ein Feld mit drei Bedeutungen.
- **Niemand war sich der Spalte sicher:** `PlannerPlanController` prüft an vier Stellen mit `safeColumn('products','sku')` (= `Schema::hasColumn`), ob die Spalte überhaupt existiert, bevor sie benutzt wird (`:9214, :9619`). Defensive Programmierung gegen die eigene Datenbank ist ein Symptom, kein Zufall.
- **Suchpfade vermischen die Begriffe:** `ProductImageCsvImportController.php:300` sucht eine übergebene *Artikelnummer* mit `orWhere('sku', $articleNo)`. `ProductDifferenceController.php:37`, `DistributorController.php:147`, `InventoryRequestOutController.php:131` suchen ebenso über beide Spalten.
- **Geschrieben wird `products.sku` im Anwendungscode an genau einer Stelle:** `ProductController.php:1889`, beim Duplizieren eines Produkts (`… . '-COPY'`). Es gibt **kein Formular und keinen Import**, der `products.sku` vergibt — nur die beiden Seeder.

### Der entscheidende Fund: es gibt eine dokumentierte Absicht — BELEGT

`app/Services/Suppliers/SupplierProductImportService.php:251-255`:

```php
// products.article_no = Hersteller-Artikelnummer
'article_no' => $manufacturerArticleNo,

// sku may use supplier/distributor article number
'sku' => $productData['sku'] ?? $distributorArticleNo,
```

**Damit sind die beiden Spalten genau die zwei Schlüssel, die die Identitätsleiter auf Stufe 2 und 3 braucht** — die Absicht war von Anfang an richtig. Sie steht nur in einem Codekommentar statt in einer Regel, wird nirgends erzwungen, und wird an drei Stellen widerlegt:

- `HeatpumpSeeder.php:86-89` setzt `sku` **und** `article_no` auf denselben Wert
- `IdsController.php:225` schreibt die *Großhändler*-Nummer nach `article_no` — nicht die Herstellernummer
- die Suchpfade behandeln beide Spalten als austauschbar (`orWhere('sku', $articleNo)`)

### Empfehlung — revidiert gegenüber der ersten Fassung

Nicht `sku` zur „Zweitnummer" abstufen, sondern **die vorhandene Absicht zur verbindlichen Regel erheben**:

| Spalte | Verbindliche Bedeutung | Leiterstufe |
|---|---|---|
| `products.article_no` | **Hersteller-Artikelnummer** | Stufe 2 |
| `products.sku` | **Lieferanten-Artikelnummer** | Stufe 3 |
| `products.ean` → `gtin_normalized` | GTIN | Stufe 1 |

**Das spart eine Spalte und einen Migrationsschritt.** In der ersten Fassung hatte ich `manufacturer_article_no` und `supplier_article_no` als neue Felder vorgeschlagen — beide sind überflüssig, die Spalten existieren bereits unter anderem Namen.

Vier Aufräumposten bleiben, alle außerhalb von Paket 1:

1. **`IdsController.php:225` korrigieren** — Großhändlernummer gehört nach `sku`, nicht nach `article_no`. (Entfällt ohnehin mit der Stilllegung in Schritt 5.)
2. **`HeatpumpSeeder.php:86` korrigieren** — nicht auf `sku` schlüsseln, nicht beide Spalten gleichsetzen.
3. **`offer_product_lists.sku` umbenennen** (z. B. `position_role`) — dort ist es eine Positionsrolle, keine Artikelnummer. Der gemeinsame Name führt jeden in die Irre, der beide Tabellen sieht.
4. **`match_by = 'sku'`** in der Connector-Konfiguration bleibt zulässig — aber erst, wenn die Bedeutung festgeschrieben ist. Bis dahin entfernen.

Die vier `safeColumn`-Prüfungen im `PlannerPlanController` können entfallen, sobald die Bedeutung feststeht.

**BEWERTUNG.** Dieser Befund stützt die Grundthese der Spezifikation stärker als alles andere: Es fehlt nicht ein Feld, es fehlt eine *verbindliche Bedeutung je Feld*. Solange ein Spaltenname drei Dinge heißen kann, hilft auch der beste Constraint nicht.

---

## 5 · Teil 4 — Pflichtfeldkatalog (Vorschlag)

Drei Stufen, weil nicht jeder Artikel alles braucht. Ein Kabelbinder braucht keine Leistungskennlinie.

### Stufe I — Identität (ohne dies existiert der Artikel nicht)
`product` (Bezeichnung) · **mindestens ein Identifikator aus Leiterstufe 1–3** · `imported_from`

*Verstoß ⇒ **Ablehnung**. Ein Artikel ohne jeden Identifikator ist keiner.*

### Stufe II — handelsfähig (ohne dies nicht bestellbar oder kalkulierbar)
Stufe I · `brand_id` · `article_group` · `measure_unit` · mindestens ein Preis in `distributor_prices` mit Lieferantenbezug

*Verstoß ⇒ Artikel wird angelegt, aber als **nicht handelsfähig** markiert und in der Angebotssuche ausgeblendet.*

### Stufe III — auslegungsfähig (ohne dies nicht berechenbar)
Stufe II · vollständiger Spec-Satz je Gerätetyp · `verifikations_status = 'datenblatt_verifiziert'`

*Verstoß ⇒ Artikel ist verkäuflich, aber für Auslegungstools gesperrt.*

**BEWERTUNG.** Diese Stufung ist der einzige Weg, die im Auftrag geforderte Kennzahl „Anteil vollständiger Artikel" überhaupt bestimmbar zu machen (`03-data-quality-report.md` §2). Sie ist außerdem anschlussfähig an die bereits vorhandene Weiche **M-C** (`auslegungsstatus`) aus `docs/spec-import/00-spec-standard.md:183`.

---

## 6 · Teil 5 — Führende Quelle je Feldgruppe

| Feldgruppe | Führend | Begründung |
|---|---|---|
| Technische Merkmale | **Hersteller-Datenblatt** (Spec-Import) | einzige geprüfte Quelle; Downgrade-Schutz gilt bereits |
| Artikelbezeichnung, Kurztext | **Open Masterdata / Lieferant** | aktueller als jede manuelle Pflege |
| Einkaufspreis, Kondition | **jeweiliger Großhändler** | kundenindividuell, nur er kennt ihn |
| Tatsächlicher Einstandspreis | **Bestellung / Eingangsrechnung** | was wirklich bezahlt wurde |
| Verkaufspreis | **interne Kalkulation** | Geschäftsentscheidung |
| Verfügbarkeit | **zeitpunktbezogene Abfrage** | nie als zeitloser Zustand speichern |
| Medien | **Hersteller vor Großhändler** | näher an der Quelle |
| Lebenszyklus | **Hersteller vor Großhändler** | der Hersteller kündigt ab |

**Die Regel dahinter:** Eine automatische Quelle überschreibt niemals einen von Hand geprüften Wert, ohne dass es ausdrücklich erlaubt und protokolliert wird. Das ist der bestehende Downgrade-Schutz (`SpecImportService`, `--allow-downgrade`), ausgedehnt auf alle Kanäle.

---

## 7 · Teil 6 — Was das Schema braucht (additiv)

**Alles `nullable`, keine Bestandsspalte wird angefasst, keine Datenmigration.**

```php
// products — Identität
// KEINE neuen Nummernspalten nötig: article_no = Hersteller-Nr, sku = Lieferanten-Nr (s. 4a)
$t->string('gtin_normalized', 14)->nullable()->after('ean');   // 14-stellig, geprüft
$t->string('identity_rung', 8)->nullable();                    // welche Stufe hat zugeordnet
$t->string('completeness_level', 4)->nullable();               // I / II / III

// Indizes — bisher gibt es KEINEN
$t->index('article_no');
$t->index('gtin_normalized');
$t->index(['brand_id', 'model']);
$t->unique('gtin_normalized');            // erst nach belegter Dublettenfreiheit
```

> **Der Unique auf `gtin_normalized` wird in Schritt 4 NICHT gesetzt**, sondern erst, wenn Schritt 1 belegt hat, dass keine widersprüchlichen GTIN existieren. Bis dahin prüft der Service, nicht die Datenbank. Ein Unique auf verschmutzten Daten lässt die Migration scheitern — und zwar zur Unzeit.

**Bereits vorhanden, muss nicht gebaut werden:** `imported_from`, `verifikations_status`, `verifikations_datum`, `datenblatt_referenz`, `created_by`, `updated_by`.

---

## 8 · Teil 7 — Umstellung der sechs Pfade

| Pfad | Heute | Künftig |
|---|---|---|
| 1 · `SupplierProductImportService:209` | eigene 3-stufige Leiter | ruft `ProductIdentityService::resolve()` — die vorhandene Leiter wird dorthin gehoben |
| 2 · `IdsController:225` | `firstOrCreate(['article_no'])` | entfällt mit der Stilllegung (Paket 1, Schritt 5) |
| 3 · `SpecImportService:340` | `brand_id` + `model` | ruft den Service, Stufe 2; `brand_id`+`model` bleibt gültiger Sonderfall des Auslegungskanals |
| 4 · `ProductImportController:130` | `article_no`, sonst **Produktname** | ruft den Service; **Namensvergleich ersatzlos gestrichen** |
| 5 · `supplier_article_map` | eigener Unique | bleibt unverändert — der neutrale Schlüssel ist richtig; nur die Normalisierung greift auch dort |
| **6 · `HeatpumpSeeder:86`** | **`sku`**, setzt zugleich `article_no = sku` | ruft den Service, Stufe 2 (`brand_id`+`model`); die `sku`-Schlüsselung entfällt |

**Verriegelung im Test:** Ein Test prüft, dass kein `Product::create` / `firstOrCreate` / `new Product` außerhalb von `ProductIdentityService` existiert. Kommt ein sechster Pfad dazu, wird der Test rot. Das ist das Abnahmekriterium aus Schritt 4 — und der Grund, warum es mechanisch prüfbar ist.

---

## 9 · Teil 8 — Kantenliste

Die Fälle, an denen es erfahrungsgemäß bricht. Gehören in die Tests von Schritt 4.

1. Führende Nullen in der Artikelnummer (`0815` vs. `815`) — **dürfen nicht** verschmelzen
2. GTIN in 8, 12, 13 und 14 Stellen — **müssen** verschmelzen
3. GTIN mit falscher Prüfziffer — gilt als **nicht vorhanden**, nicht als Schlüssel
4. Gleiche GTIN, verschiedene Hersteller — **Konflikt**, keine Zuordnung
5. Derselbe Artikel bei zwei Lieferanten — ein `products`, zwei `distributor_prices`
6. Artikel ohne Hersteller — heute springt `IdsMapper.php:35-39` aus; künftig Stufe 3 oder neuer Artikel
7. Sentinel `'Not filled'` als Artikelnummer
8. Leerzeichen vorn (Kollation fängt sie **nicht**), Leerzeichen hinten (fängt sie)
9. Zwei Artikel mit identischem Namen — **nie** zusammenführen
10. Import derselben Datei zweimal — muss **einen** Artikel ergeben
11. Umlaute und ß im Herstellernamen
12. `brands.type` = `'brand'` vs. `'manufacturer'`

---

## 10 · Was du entscheiden musst

Vier Festlegungen. Zu jeder mein Vorschlag mit Begründung — die Entscheidung ist deine.

### E1 · Darf Stufe 5 automatisch zuordnen?
*Vorschlag: **nein**, nur vorschlagen.*
Begründung: Ein Textvergleich auf Artikelnummern ohne Lieferantenbezug trifft bei zwei Großhändlern zwangsläufig falsch, weil beide eigene Nummernkreise führen. Der Preis für „nein" ist eine Vorschlagsliste, die jemand durchsieht. Der Preis für „ja" ist ein falsch zugeordneter Preis, der niemandem auffällt.

### E2 · Hersteller und Marke trennen?
*Vorschlag: **ja**, aber erst in Paket 2.*
Begründung: `brands` führt beides in einer Tabelle mit einem `type`-Feld, das zwei Wertwelten hat. Stufe 2 der Leiter braucht den *Hersteller*. Für Paket 1 reicht: `type = 'manufacturer'` gilt als Hersteller, alles andere als Marke. Die saubere Trennung ist ein eigener Posten.

### E3 · Pflichtfeldkatalog — sind die drei Stufen richtig geschnitten?
*Vorschlag: wie in Teil 4.*
Zu prüfen ist vor allem Stufe II: Reicht **ein** Lieferantenpreis, um „handelsfähig" zu sein, oder muss auch eine Mengeneinheit gesetzt sein? Das ist eine fachliche Frage aus deinem Alltag, keine technische.

### E4 · Was passiert mit den 88 Bestandspreiszeilen?
*Vorschlag: **stehen lassen**, beim ersten echten Import neu bewerten.*
Begründung: Deine eigene Quelle bewertet sie als Demo-Daten. Sie zu löschen wäre ein destruktiver Schritt ohne Not; sie zu migrieren wäre Arbeit an Daten, die niemand braucht. Markieren als `imported_from = 'demo'` genügt.

### E5 · Bestätigen wir die im Code dokumentierte Bedeutung?
*Vorschlag: **ja**.*
`article_no` = Hersteller-Artikelnummer, `sku` = Lieferanten-Artikelnummer — genau so, wie es der Kommentar in `SupplierProductImportService.php:251-255` seit jeher vorsieht. Das ist die billigste aller Varianten: keine neue Spalte, keine Datenmigration, nur eine Regel und ihre Durchsetzung.

---

## 11 · Offen

- **GEKLÄRT (war offen):** `sku` ist nicht unbenutzt, sondern dreideutig — siehe Abschnitt 4a. Der ursprüngliche Befund in Fassung 1 war falsch und ist dort korrigiert. Zu entscheiden bleibt nur noch, ob `offer_product_lists.sku` umbenannt wird (Empfehlung: ja, eigener Posten).
- **OFFEN:** Ob die GTIN-Prüfziffernvalidierung sofort greifen soll oder zunächst nur warnt. Hängt am Ergebnis von Schritt 1, Abschnitt 4.
- **ANNAHME:** Ich nehme an, dass `products` künftig weiterhin sowohl Handels- als auch Auslegungsartikel führt (eine Zeile, zwei Datenquellen), wie in `docs/spec-import/00-spec-standard.md` §6 festgelegt. Die Leiter ist darauf ausgelegt. Soll das getrennt werden, ändert sich Stufe 2.
