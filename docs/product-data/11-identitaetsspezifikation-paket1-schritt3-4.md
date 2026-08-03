# 11 · Identitätsspezifikation — Paket 1, Schritt 3 und 4

> **Rolle:** Planner · **Stand:** 2026-08-01 · **Heimat-App:** `ticket` · **Spur:** A
> **Ersetzt:** `10-target-domain-model.md` §1 (Pfadliste) und §8 (Umstellungsplan) — die übrigen
> Abschnitte von `10` bleiben gültig und werden hier verfeinert, nicht verworfen.
> **Status:** Entscheidungen sind **getroffen**, nicht gestellt. Yama kann jeder widersprechen,
> solange der Generator nicht angefangen hat. Ohne Widerspruch gilt, was hier steht.
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 0 · Warum es diese Fassung gibt

`10-target-domain-model.md` §1 führt „**die sechs Pfade — verifiziert**" auf. Ich habe den Zensus
selbst gefahren statt ihn zu übernehmen. Ergebnis:

```
grep -rn "Product::firstOrCreate\|Product::updateOrCreate\|Product::create\|new Product(" \
     app/ database/ --include=*.php | grep -v "ProductImage\|ProductPrice\|ProductMedia"
```

**Neun Stellen schreiben `Product`, nicht sechs.** Und einer der sechs dokumentierten Pfade
(`SpecImportService`) schreibt überhaupt kein `Product` — er liest nur.

Das ist kein Schönheitsfehler. Das Abnahmekriterium von Schritt 4 lautet: *„Alle sechs Schreibpfade
rufen nachweislich `ProductIdentityService` auf — im Test verriegelt."* Gegen sechs gebaut, hätte
der Verriegelungstest **drei Pfade offengelassen** und wäre trotzdem grün gewesen. Ein grünes
Kriterium, das die Hälfte des Problems nicht sieht, ist schlimmer als keins.

---

## 1 · Der belegte Zensus — neun Schreibstellen, acht Identitätsbegriffe

| # | Ort | Schlüssel zur Auflösung | Schreibart | In `10`? |
|---|---|---|---|---|
| 1 | `app/Services/Suppliers/SupplierProductImportService.php:34`<br>(Auflösung `:209-238`) | EAN → `article_no` → `(distributor_id, dp.article_no)` | `Product::create` | ✅ Pfad 1 |
| 2 | `app/Services/Suppliers/SupplierConnectorService.php:699`<br>(Auflösung `:643-661`) | EAN → `article_no` → **`dp.article_no` ohne `distributor_id`** | `Product::create` | ❌ **fehlt** |
| 3 | `app/Http/Controllers/Product/IDS/gconline/IdsController.php:89`<br>(`autoPromoteItem`) | `article_no` (trägt die **GC-Online-Nummer**) | `firstOrCreate` | ❌ **fehlt** |
| 4 | `app/Http/Controllers/Product/IDS/gconline/IdsController.php:226` | `article_no` (trägt die **Großhändlernummer**) | `firstOrCreate` | ✅ Pfad 2 |
| 5 | `app/Http/Controllers/Product/ProductImportController.php:141`<br>(Auflösung `:131-136`) | `article_no` → **Produktname** | `new Product` | ✅ Pfad 4 |
| 6 | `app/Services/ProductCsvImporter.php:358`<br>(Auflösung `:320-327`) | `article_no` → **`(model, product)`**, wobei `model` die **Herstellernummer** trägt | `Product::create` | ❌ **fehlt** |
| 7 | `app/Http/Controllers/Product/ProductController.php:1040`<br>(Auflösung `:1023-1027`) | **`(product, model, brand_id)`** — reiner Textschlüssel | `new Product` | ❌ **fehlt** |
| 8 | `app/Http/Controllers/Product/ProductController.php:1317` | `id` (Primärschlüssel) | `updateOrCreate` | ❌ fehlt, s. u. |
| 9 | `database/seeders/HeatpumpSeeder.php:86` | `sku`, setzt zugleich `article_no = sku` | `updateOrCreate` | ✅ Pfad 6 |

**Nicht in der Liste, obwohl `10` es führt:** `app/Services/Spec/SpecImportService.php`.
`grep -n "Product::\|new Product\|->save()"` liefert **null Treffer**. Die einzige Produktberührung
ist `:352` — `DB::table('products')->where('brand_id',…)->where('model',…)->first()`, ein **reiner
Lesezugriff**. Der Spec-Import schreibt in `product_heat_pump_specs`, `product_pv_module_specs`,
`inverters`, `batteries` (`SpecImportService.php:20`), nicht in `products`. **Pfad 3 aus `10` ist
kein Schreibpfad.** Er bleibt trotzdem relevant, weil er `(brand_id, model)` als Identitätsbegriff
zum *Finden* benutzt — also muss er die Normalisierungsregel mittragen.

**Ebenfalls nicht in der Liste:** `supplier_article_map` (Pfad 5 aus `10`). Das ist eine andere
Tabelle mit eigenem, korrektem Unique. Sie bleibt unangetastet.

**Zu Stelle 8 · BEWERTUNG.** `Product::updateOrCreate(['id' => $request->product_id], …)` schlüsselt
auf den Primärschlüssel, nicht auf Artikelidentität. Das ist ein *Aktualisierungs*pfad, kein
Auflösungspfad. Er kann aber anlegen, wenn `product_id` leer ankommt. Er gehört deshalb in die
Verriegelung, aber **nicht** in die Leiter.

---

## 2 · Drei Befunde, die `10` nicht hat — und die die Spezifikation verschärfen

### 2.1 · `SupplierConnectorService:657` sucht ohne Lieferantenbezug · BELEGT

```php
$existingPrice = DistributorPrice::where('article_no', $distributorArticleNo)
    ->with('product')->first();
```

Der Schwesterpfad `SupplierProductImportService:230` filtert korrekt zusätzlich auf
`distributor_id`. Hier fehlt der Filter. **Folge:** Führen zwei Großhändler dieselbe
Artikelnummer — bei fünf- bis sechsstelligen Hausnummernkreisen die Regel, nicht die Ausnahme —,
liefert `first()` den Treffer des *falschen* Lieferanten, und der eingehende Preis landet am
falschen Produkt. Das ist kein Dublettenproblem, das ist ein **Preis am falschen Artikel**.

Dass es heute nicht auffällt, liegt daran, dass der Beschaffungsstrang null Datenzeilen hat
(`03` §6). Beim ersten echten Import fällt es sofort an — und zwar still.

### 2.2 · Der manuelle Anlageweg hat einen eigenen, reinen Textschlüssel · BELEGT

`ProductController:1023-1027` prüft vor dem Anlegen auf `(product, model, brand_id)`. Das ist der
Weg, den **Menschen täglich über die Oberfläche benutzen**. Er ist damit der wichtigste der neun —
und der einzige, den `10` gar nicht kennt.

**BEWERTUNG.** Ein Dreifach-Textschlüssel über die Bezeichnung ist als *Warnung* brauchbar („gibt es
vielleicht schon") und als *Identität* untauglich. Er gehört auf Leiterstufe 5, nicht davor.

### 2.3 · `ProductCsvImporter` schreibt die Herstellernummer nach `model` · BELEGT

```php
// :326-327
if (!$existing && $manufacturerNo && $productName) {
    $existing = Product::where('model', $manufacturerNo)…
```

Die Variable heißt `$manufacturerNo`, das Zielfeld ist `model`. Das **widerspricht E5 direkt**:
dort soll die Herstellernummer nach `article_no`. Zwei Importwege legen dieselbe Zahl in zwei
verschiedene Spalten. `10` §4a hat `sku` als dreideutig entlarvt — `model` ist der vierte Fall
derselben Krankheit, nur eine Ebene tiefer.

---

## 3 · Die Entscheidungen — getroffen

| # | Frage | **Entscheidung** | Begründung in einem Satz |
|---|---|---|---|
| **E1** | Darf Stufe 5 (Textvergleich) automatisch zuordnen? | **Nein — nur Vorschlag.** | Zwei Großhändler führen eigene Nummernkreise; ein Texttreffer ohne Lieferantenbezug trifft zwangsläufig irgendwann falsch, und ein falsch zugeordneter Preis fällt niemandem auf. |
| **E2** | Hersteller und Marke trennen? | **Nicht in Paket 1.** Für die Leiter gilt: `brands.type === 'manufacturer'` ist Hersteller, alles andere Marke. | Der Lieferantenimport legt Marken bereits mit `type = 'manufacturer'` an (`SupplierProductImportService:200-206`, BELEGT) — die Unterscheidung trägt für Stufe 2, die saubere Trennung ist eigener Aufwand ohne Nutzen für Paket 1. |
| **E3** | Pflichtfeldkatalog — Schnitt der drei Stufen | **Wie `10` §5, mit einer Verschärfung: Stufe II verlangt `measure_unit` zwingend**, nicht nur einen Preis. | Ohne Mengeneinheit ist keine Position kalkulierbar — das ist exakt der Mechanismus hinter dem 50-m-Kabel-Fehler (Faktor 50). Ein Artikel ohne Einheit ist nicht handelsfähig, egal wie viele Preise er hat. |
| **E4a** | Weg D (`OfferTemplateSupplierController`) stilllegen oder umhängen? | **Noch nicht entschieden — bewusst.** | Ich habe die 765 Zeilen nicht gelesen. Eine Entscheidung über Stilllegung ohne gelesenen Codepfad wäre genau der Fehler, den diese Sitzung vermeiden soll. Eigener Planner-Vorgang, terminiert vor Schritt 5. **Blockiert Paket 1 nicht** — Schritt 3 und 4 hängen nicht daran. |
| **E4b** | Die 88 Bestandspreiszeilen | **Stehen lassen, `imported_from = 'demo'` setzen.** | Löschen ist destruktiv ohne Not, Migrieren ist Arbeit an Daten, die niemand braucht; die Markierung genügt, um sie später zu erkennen. |
| **E5** | `article_no` = Hersteller-Nr, `sku` = Lieferanten-Nr? | **Ja, verbindlich.** | Der Codekommentar `SupplierProductImportService:251-255` legt es seit jeher so fest, zwei von neun Pfaden halten sich schon daran, und es spart eine Spalte plus eine Migration. |
| **F3** | Maßgebliche Datenbank | **Live-`ticket` auf Port 3307.** | Die Prüfabfragen sind ausschließlich `SELECT`; Entscheidungen auf Testdaten zu treffen hieße, die Migrationsstrategie an Zahlen zu hängen, die den Bestand nicht abbilden. |

**Neu hinzugekommen durch §2.3:**

| # | Frage | **Entscheidung** |
|---|---|---|
| **E6** | Was gilt `products.model`? | **`model` ist die Typ-/Modellbezeichnung des Herstellers, keine Nummer.** Der Gebrauch in `ProductCsvImporter:327` ist ein Fehler und wird korrigiert — die Herstellernummer gehört nach `article_no`. Der Spec-Import darf `(brand_id, model)` weiter als Sonderfall benutzen, weil er dort echte Modellbezeichnungen führt. |

---

## 4 · Die Identitätsleiter — verbindlich

**Grundsatz.** Geordnete Stufen, erste Übereinstimmung gewinnt, **kein Treffer heißt neuer Artikel**.

| Stufe | Schlüssel | Ergebnis |
|---|---|---|
| 1 | `gtin_normalized` (14-stellig, Prüfziffer gültig) | automatisch |
| 2 | `(brand_id [type=manufacturer], article_no)` normalisiert | automatisch |
| 3 | `(distributor_id, sku)` normalisiert | automatisch |
| 4 | `(branchennummer, land, branche)` — OMD `branch[]` | automatisch, **erst ab Paket 6 aktiv** |
| 5 | normalisierter Textvergleich: `article_no` allein **oder** `(product, model, brand_id)` | **nur Vorschlag** |
| — | Produktname allein | **nie** |

**Stufe 4 ist heute leer.** Die Datenfelder existieren nicht. Sie steht trotzdem in der Leiter, damit
Paket 6 sie einhängt, ohne die Reihenfolge zu ändern. Der Service behandelt sie als „kein Treffer".

### Der Abbruch nach oben — die wichtigste Regel

**Ein Treffer wird verworfen, wenn eine höhere Stufe widerspricht.**

Konkret: Stufe 3 findet über `(Lieferant 7, sku 4711)` den Artikel A. Der eingehende Satz trägt
GTIN `04012345678901`, Artikel A trägt `04012345000009`. Zwei verschiedene GTIN = zwei verschiedene
Artikel. **Treffer verworfen, Ergebnis `KONFLIKT`, kein Schreibzugriff.**

Ohne diese Regel ist eine Leiter gefährlicher als gar keine: sie erzeugt falsche Zuordnungen mit
dem Anschein von Verlässlichkeit.

Der Widerspruch wird nur geprüft, wenn **beide Seiten** den höheren Schlüssel führen. Ein leerer
Wert widerspricht nicht.

---

## 5 · Normalisierung — verbindlich, beim Schreiben **und** beim Suchen

| Feld | Regel |
|---|---|
| **GTIN** | `TRIM` · alle Nicht-Ziffern entfernen · links auf **14 Stellen** mit Nullen füllen · Prüfziffer nach GS1 validieren · ungültig ⇒ gilt als **nicht vorhanden** |
| **Artikelnummer** (`article_no`, `sku`) | `TRIM` · Mehrfach-Leerzeichen zu einem · **Großbuchstaben** · **führende Nullen bleiben** |
| **Herstellername** | `TRIM` · Mehrfach-Leerzeichen zu einem · Vergleich case-insensitiv |
| **Modell** | `TRIM` · Mehrfach-Leerzeichen zu einem · Schreibweise erhalten · Vergleich case-insensitiv |

**Zwei entgegengesetzte Regeln für zwei Felder, die beide „Nummer" heißen:** Bei GTIN sind führende
Nullen bedeutungslos (EAN-8/13 und GTIN-14 sind dasselbe Nummernsystem) — deshalb auffüllen. Bei
Artikelnummern sind sie bedeutungstragend — `0815` und `815` können zwei Artikel sein. Wer hier
normalisiert, verschmilzt Artikel. Das ist die häufigste Fehlerquelle bei genau dieser Aufgabe.

### Sentinel-Liste — zentral, verbindlich

`'Not filled'` · `'-'` · `'n/a'` · `'N/A'` · `'0'` · `''` · `null`

Ein Wert aus dieser Liste gilt als **leer** und wird **nie** als Schlüssel benutzt. Heute wird
`'Not filled'` an genau einer Stelle abgefangen (`SupplierProductImportService:219`) und an acht
nicht — `prepareProductCreateData:246` schreibt es sogar aktiv als `article_no` in die Datenbank.

### Kollation — nicht darauf verlassen · BELEGT

`config/database.php` nutzt `utf8mb4_unicode_ci`: case-insensitiv und PAD SPACE, also toleriert die
Datenbank *nachlaufende* Leerzeichen, **führende aber nicht**. Sie erledigt heute zufällig einen
Teil der Normalisierung — und genau deshalb fällt das Fehlen der übrigen nicht auf. In PHP
verglichen gilt sie gar nicht. **Normalisierung gehört in den Code.**

---

## 6 · `ProductIdentityService` — die Schnittstelle

Zwei Wertobjekte und ein Dienst. Bewusst ohne Eloquent-Magie, damit der Verriegelungstest greifen
kann.

```php
namespace App\Services\Product\Identity;

/** Was ein Kanal über einen Artikel behauptet — bereits normalisiert oder roh. */
final class ProductIdentity
{
    public function __construct(
        public readonly ?string $gtin                  = null,
        public readonly ?string $manufacturerArticleNo = null,   // -> products.article_no
        public readonly ?int    $brandId               = null,   // brands.type = manufacturer
        public readonly ?string $supplierArticleNo     = null,   // -> products.sku
        public readonly ?int    $distributorId         = null,
        public readonly ?string $model                 = null,
        public readonly ?string $name                  = null,
        public readonly string  $channel               = 'unknown', // z.B. 'ids:gconline'
    ) {}
}

final class IdentityMatch
{
    public const AUTOMATISCH = 'automatisch';  // Stufe 1-4, zugeordnet
    public const VORSCHLAG   = 'vorschlag';    // Stufe 5, NICHT zugeordnet
    public const KONFLIKT    = 'konflikt';     // Abbruch nach oben griff
    public const NEU         = 'neu';          // kein Treffer -> neuer Artikel

    public function __construct(
        public readonly string   $ergebnis,
        public readonly ?Product $product,
        public readonly ?int     $stufe,
        public readonly string   $begruendung,   // fuer Protokoll und Vorschlagsliste
    ) {}
}

final class ProductIdentityService
{
    public function normalize(ProductIdentity $in): ProductIdentity;

    /** Führt die Leiter aus. Schreibt NICHTS. */
    public function resolve(ProductIdentity $in): IdentityMatch;

    /** Die einzige Stelle im System, die Product anlegen darf. */
    public function createFrom(ProductIdentity $in, array $weitereFelder = []): Product;
}
```

**Warum `resolve()` nichts schreibt:** Sonst kann man es im Test nicht gegen Bestandsdaten laufen
lassen, und der Dry-Run aus Schritt 4 wäre nicht möglich. Trennung von Finden und Schreiben ist die
Voraussetzung dafür, die Leiter gegen die echte Datenbank zu messen, bevor man sie scharf schaltet.

**Abschaltbarkeit:** `config('produkt.identitaet.aktiv')`, Default `false`. Bei `false` verhält sich
jeder umgestellte Pfad **exakt wie heute**. Das ist der Rückweg ohne Datenmigration.

---

## 7 · Schema — additiv, `nullable`, keine Bestandsspalte angefasst

**Ausgangslage · BELEGT:** Über **alle** Migrationen zu `products` gibt es **keinen einzigen**
`unique(` oder `index(`. `article_no`, `ean`, `model` stammen aus
`2023_06_22_085602_create_products_table.php:17,18,25`, `sku` aus
`2025_08_26_075553_*:10` — alle `->string()->nullable()` ohne Index.

```php
// products
$t->string('gtin_normalized', 14)->nullable()->after('ean');
$t->string('identity_rung', 8)->nullable();        // welche Stufe hat zugeordnet
$t->string('completeness_level', 4)->nullable();   // I / II / III

// Indizes - bisher existiert KEINER
$t->index('article_no',      'products_article_no_idx');
$t->index('sku',             'products_sku_idx');
$t->index('gtin_normalized', 'products_gtin_idx');
$t->index(['brand_id','model'], 'products_brand_model_idx');
```

**Kein `unique` in Schritt 4.** Der Unique auf `gtin_normalized` wird erst gesetzt, wenn
`ergebnis-2026-08.txt` belegt hat, dass keine widersprüchlichen GTIN existieren. Ein Unique auf
verschmutzten Daten lässt die Migration scheitern — und zwar zur Unzeit, mitten in der Migration,
mit halb angewandtem Schema. Bis dahin prüft der Service, nicht die Datenbank.

**Bereits vorhanden, nicht neu bauen:** `imported_from` (`2026_07_05_150006`),
`verifikations_status`, `verifikations_datum`, `datenblatt_referenz` (`2026_07_05_150007`),
`created_by`/`updated_by` (`2026_05_05_093310`).

**Neue Tabelle für Stufe 5:**

```php
Schema::create('product_identity_suggestions', function (Blueprint $t) {
    $t->id();
    $t->foreignId('product_id')->constrained();     // der vermutete Treffer
    $t->json('incoming');                           // die ProductIdentity, die kam
    $t->string('channel', 64);
    $t->string('reason', 255);
    $t->string('status', 16)->default('offen');     // offen | bestaetigt | verworfen
    $t->foreignId('decided_by')->nullable()->constrained('users');
    $t->timestamps();
    $t->index(['status','channel']);
});
```

Ohne diese Tabelle ist E1 („nur vorschlagen") nicht umsetzbar — ein Vorschlag, den niemand sehen
kann, ist eine Verwerfung mit Extraschritten.

---

## 8 · Umstellungsplan — alle neun Stellen

| # | Ort | Heute | Künftig |
|---|---|---|---|
| 1 | `SupplierProductImportService:34/209` | eigene 3-Stufen-Leiter | ruft `resolve()`; die vorhandene Leiter **wandert in den Service** — sie ist das Vorbild, nicht der Gegner |
| 2 | `SupplierConnectorService:699/643` | eigene Leiter **ohne `distributor_id`** | ruft `resolve()`; der fehlende Lieferantenfilter ist damit behoben (§2.1) |
| 3 | `IdsController:89` | `firstOrCreate(article_no)` mit GC-Nummer | entfällt mit Schritt 5; **bis dahin** `resolve()` mit `supplierArticleNo`, nicht `manufacturerArticleNo` |
| 4 | `IdsController:226` | `firstOrCreate(article_no)` mit Großhändlernummer | wie 3 |
| 5 | `ProductImportController:141/131` | `article_no` → **Produktname** | ruft `resolve()`; **Namensvergleich ersatzlos gestrichen** |
| 6 | `ProductCsvImporter:358/320` | `article_no` → `(model, product)` | ruft `resolve()`; Herstellernummer wandert von `model` nach `manufacturerArticleNo` (E6) |
| 7 | `ProductController:1040/1023` | `(product, model, brand_id)` | ruft `resolve()`; Treffer auf Stufe 5 wird dem Anwender **als Rückfrage angezeigt**, nicht stillschweigend übernommen |
| 8 | `ProductController:1317` | `updateOrCreate(['id'])` | bleibt fachlich, wird aber verriegelt: `id` muss gesetzt sein, sonst Ausnahme statt stillem Anlegen |
| 9 | `HeatpumpSeeder:86` | `sku`, setzt `article_no = sku` | ruft `resolve()` auf Stufe 2 (`brand_id`+`model`); die `sku`-Schlüsselung entfällt |
| — | `SpecImportService:352` | liest `(brand_id, model)` | **bleibt lesend**, übernimmt aber die Normalisierungsregel |
| — | `supplier_article_map` | eigener Unique | unverändert — der neutrale Schlüssel ist richtig |

### Die Verriegelung

Ein Test, der über den Quelltext läuft, nicht über das Verhalten:

```php
/** @test */
public function kein_pfad_legt_produkte_am_identitaetsdienst_vorbei_an(): void
{
    $treffer = $this->grepProjekt(
        '(Product::(firstOrCreate|updateOrCreate|create)|new\s+Product\s*\()',
        verzeichnisse: ['app', 'database/seeders'],
        ausnahmen: ['app/Services/Product/Identity/ProductIdentityService.php'],
    );

    $this->assertSame([], $treffer,
        "Diese Stellen legen Produkte an, ohne durch ProductIdentityService zu gehen:\n"
        . implode("\n", $treffer));
}
```

**Kommt eine zehnte Stelle dazu, wird der Test rot.** Das ist der eigentliche Wert von Schritt 4 —
nicht die Migration, sondern die Tatsache, dass das Problem danach nicht mehr unbemerkt wachsen kann.

---

## 9 · Kantenliste — jede Zeile ist ein Testfall

| # | Fall | Erwartung |
|---|---|---|
| 1 | `0815` vs. `815` als `article_no` | **zwei** Artikel, keine Verschmelzung |
| 2 | GTIN in 8, 12, 13, 14 Stellen, gleiche Nummer | **ein** Artikel |
| 3 | GTIN mit falscher Prüfziffer | gilt als nicht vorhanden, Leiter fällt auf Stufe 2 |
| 4 | Gleiche GTIN, verschiedene `brand_id` | `KONFLIKT`, keine Zuordnung, kein Schreibzugriff |
| 5 | Derselbe Artikel bei zwei Lieferanten | ein `products`, zwei `distributor_prices` |
| 6 | Zwei Lieferanten, **gleiche** `sku`, verschiedene Artikel | zwei Artikel — der Fall aus §2.1 |
| 7 | Artikel ohne Hersteller | Stufe 2 übersprungen, nicht abgebrochen (`IdsMapper:35-39` springt heute aus) |
| 8 | `'Not filled'` als `article_no` | gilt als leer, wird **nicht** geschrieben |
| 9 | `' 4711'` (führendes Leerzeichen) vs. `'4711'` | ein Artikel — die Kollation fängt das **nicht** |
| 10 | `'4711  '` (nachlaufend) vs. `'4711'` | ein Artikel — die Kollation fängt das zufällig |
| 11 | Zwei Artikel, identischer Name, verschiedene Nummern | **nie** zusammenführen |
| 12 | Dieselbe Datei zweimal importiert | **ein** Artikel |
| 13 | Umlaute und ß im Herstellernamen | ein Artikel |
| 14 | `brands.type = 'brand'` statt `'manufacturer'` | Stufe 2 greift nicht, Leiter fällt auf Stufe 3 |
| 15 | `config('produkt.identitaet.aktiv') = false` | **jeder** Pfad verhält sich exakt wie vor der Änderung |
| 16 | Stufe-5-Treffer | Zeile in `product_identity_suggestions`, **kein** Schreibzugriff auf `products` |

Fall 15 ist der wichtigste. Er ist der Rückweg, und ein Rückweg, der nicht getestet ist, ist keiner.

---

## 10 · Abnahmekriterien — mechanisch prüfbar

1. **Verriegelung greift.** Der Test aus §8 ist grün. **Gegen-Beweis:** Der Generator fügt
   probeweise irgendwo ein `Product::create([])` ein; der Test muss **rot** werden. Rohausgabe
   beider Läufe liegt vor. *Ein Verriegelungstest, der nicht rot werden kann, verriegelt nichts.*
2. **Doppelimport erzeugt einen Artikel.** Dieselbe Datei zweimal durch Pfad 1 ⇒
   `SELECT COUNT(*) FROM products WHERE article_no = '<X>'` liefert **1**.
3. **Alle 16 Kantenfälle** aus §9 haben je einen benannten Test, alle grün.
4. **Der Schalter trägt.** Mit `produkt.identitaet.aktiv = false` ist die Testsuite grün **und**
   ein Import erzeugt byte-gleiche Zeilen wie vor der Änderung (Vergleich über
   `SELECT * … ORDER BY id` beider Läufe).
5. **Keine Regression.** Suite gegen die Baseline aus Schritt 1 nicht schlechter, inklusive der
   Referenzfall-Tests.
6. **Migration ist umkehrbar.** `migrate` gefolgt von `migrate:rollback` lässt `products`
   spaltengleich zum Ausgangszustand zurück (`SHOW COLUMNS` vorher/nachher, Rohausgabe).

---

## 11 · Rückweg und Entdeckung

**Rückweg — dreifach, absteigend:**

1. `config('produkt.identitaet.aktiv') = false` — sofort, ohne Deploy, ohne Datenmigration.
2. Commit zurückdrehen — die Migration ist additiv und `nullable`, kein Bestandsfeld wird angefasst.
3. `migrate:rollback` — entfernt die drei Spalten und die vier Indizes.

**Voraussetzung vor Beginn:** aktueller Stand **gepusht**. Vor dem Deploy ist der Remote die einzige
Kopie außerhalb der Maschine. (Stand 2026-08-01: erledigt, `ahead` steht bei 4.)

**Entdeckung — vier benannte Signale, keins davon „wird schon auffallen":**

```sql
-- 1) Greift die Leiter?  Muss fallen, nicht steigen.
SELECT COUNT(*) FROM products WHERE identity_rung IS NULL;

-- 2) Stauen sich Vorschläge?  Wenn ja, ist Stufe 5 zu weit gefasst.
SELECT COUNT(*) FROM product_identity_suggestions WHERE status = 'offen';

-- 3) Entstehen weiter Zwillinge?  Muss 0 bleiben.
SELECT article_no, COUNT(*) c FROM products
 WHERE article_no IS NOT NULL AND article_no <> 'Not filled'
 GROUP BY article_no HAVING c > 1;

-- 4) Schreibt noch jemand den Sentinel?  Muss 0 bleiben.
SELECT COUNT(*) FROM products WHERE article_no = 'Not filled';
```

Signal 1 und 3 laufen täglich für 14 Tage. Steigt 3, ist die Leiter nicht scharf.

---

## 12 · Was Schritt 4 ausdrücklich **nicht** enthält

Damit der Generator den Umfang nicht ausdehnt und der Evaluator nicht danach sucht:

- **Kein `unique`-Constraint.** Erst nach belegter Dublettenfreiheit, eigener Vorgang.
- **Keine Bereinigung von Bestandsdaten.** Kein Zusammenführen, kein Löschen, kein Backfill von
  `gtin_normalized`. Der Service prüft nur; das Aufräumen ist Paket 2.
- **Keine Trennung Hersteller/Marke.** Siehe E2.
- **Keine Stilllegung von Weg A oder D.** Das ist Schritt 5.
- **Keine Änderung an `offer_product_lists.sku`.** Umbenennung nach `position_role` ist Paket 2,
  Posten 0.
- **Keine Preislogik.** Der `NetPrice`-Fehler (Positionssumme statt Stückpreis, Faktor 50) ist
  Paket 3 und wird hier **nicht** mit angefasst, obwohl er teurer ist. Reihenfolge nach
  Abhängigkeit, nicht nach Schmerz.

---

## 13 · Ledger

```
PLANNER 2026-08-01 · Paket 1 Schritt 3 · Spur A · Heimat ticket · ABGESCHLOSSEN
  Ergebnis: docs/product-data/11-identitaetsspezifikation-paket1-schritt3-4.md
  Korrigiert 10 §1: neun Schreibstellen statt sechs, belegt per Zensus.
  SpecImportService ist KEIN Schreibpfad (nur :352 lesend) - Pfad 3 aus 10 faellt weg.
  Neu erfasst: SupplierConnectorService:699, IdsController:89, ProductCsvImporter:358,
               ProductController:1040 und :1317.
  Entscheidungen getroffen: E1 nein · E2 nicht in Paket 1 · E3 mit measure_unit ·
    E4b demo-markieren · E5 ja · E6 model ist keine Nummer · F3 Live-DB 3307.
  OFFEN: E4a (Weg D) - bewusst nicht entschieden, 765 Zeilen ungelesen. Blockiert Paket 1 nicht.
  Ballbesitz: Yama (Freigabe der Entscheidungen), dann Generator fuer Schritt 4.

PLANNER 2026-08-01 · Paket 1 Schritt 4 · Spur A · Heimat ticket · SPEZIFIZIERT
  Umfang: ProductIdentityService + additive Migration + 9 Pfade + Verriegelungstest.
  Rueckweg dreifach, Schalter produkt.identitaet.aktiv Default false.
  16 Kantenfaelle, 6 Abnahmekriterien, 4 Entdeckungssignale.
  Ballbesitz: Generator - NICHT dieselbe Instanz, die diese Spezifikation geschrieben hat.
```

---

## 14 · Nachtrag 2026-08-03 — die Messwerte sind da, Schritt 3 ist geschlossen

> **Rolle:** Planner (andere Instanz als der Verfasser oben) · **Freigabe:** Yama, 03.08.
> („weiter mit planner schritt 3"), kein Widerspruch gegen E1–E6/F3 — die Entscheidungen gelten.

**Aus `ergebnis-2026-08.txt` (Commit `307b486e`) und Nachmessung 03.08.:**

| Messwert | Zahl | Befehl/Quelle |
|---|---|---|
| Dubletten 3a (normalisierte `article_no`) | **0** | `ergebnis-2026-08.txt` §3 |
| Dubletten 3b/3e (EAN) | **leer — alle 94 Artikel ohne EAN** | ebd. §2/§3 |
| Dubletten 3c (`brand_id`+`model`) | **4** (Marke 79: standard/komfort/premium/eco je 2×) | ebd. §3 |
| Sentinel `'Not filled'`/`'-'`/`'n/a'`/`'0'` in `article_no` | **0** | tinker-Count 03.08. |
| Artikel mit gefülltem `sku` | **44 / 94** | tinker-Count 03.08. |
| **`brands.type = 'manufacturer'`** | **0 von 50** | tinker-Count 03.08. |

**Konsequenzen — drei, keine ändert die Entscheidungen:**

1. **Stufe 2 greift im heutigen Bestand nie** (0 Hersteller-Marken). Kante 14 ist damit der
   **Regelfall**, nicht der Sonderfall: die Leiter fällt bestandsseitig auf Stufe 3/5/NEU durch.
   Das ist kein Blocker (genau dafür ist das Durchfallen gebaut), aber Entdeckungssignal 1
   (`identity_rung IS NULL` bzw. niedrige Stufen) wird anfangs hoch stehen. **Das Typisieren der
   50 Bestandsmarken ist Bestandsdaten-Arbeit → Paket 2, eigener beauftragter Posten, kein Beifang
   von Schritt 4.**
2. **Der `unique` auf `gtin_normalized` bleibt eigener Vorgang.** Zwar 0 GTIN-Konflikte gemessen —
   aber bei 0 vorhandenen GTINs ist das eine leere Aussage, kein Freibrief.
3. **Signal-Baselines stehen:** Zwilling-Query (Signal 3) = 0 · Sentinel (Signal 4) = 0. Jede
   Abweichung nach oben ist ab jetzt ein Befund.

**Auftragsblatt für Schritt 4:** `docs/auftraege/produktdaten/17-identitaet-schritt4.md`
(Status `entwurf`, braucht Gegenlesen nach B8, dann Generator).
