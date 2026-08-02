# 13 · AUF-P1-S2-d3 — Bildname geht im Lieferantenimport verloren

```yaml
auftrag:
  id: AUF-P1-S2-d3
  strang: produktdaten
  status: bereit
  spur: A
  heimat: ticket
  ziel: "SupplierConnectorService.php:1351 uebergibt 'name' statt 'title' an ProductImage,
         und ein Test belegt das per Mass Assignment."
  nicht_ziel: "Kein Backfill des Altbestands. Keine Vereinheitlichung des fehlenden Fallbacks.
               Keine Aenderung an products.product_image."

scope:
  population_command: "grep -c \"'title'\" app/Services/Suppliers/SupplierConnectorService.php"
  pfade:
    - app/Services/Suppliers/SupplierConnectorService.php
    - tests/Feature/Product/ProductImageFillableTest.php
  ausschluesse: []

kriterien:
  - id: K-01
    aussage: "Im ProductImage-Block steht kein 'title' mehr."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n \"'title'\" app/Services/Suppliers/SupplierConnectorService.php"
      erwartet: "kein Treffer im ProductImage-Block (Zeile ~1345-1355)"
    beleg: rohausgabe

  - id: K-02
    aussage: "Ein ProductImage laesst sich per Mass Assignment mit 'name' anlegen und zurueklesen."
    typ: presence
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "php artisan test --filter=ProductImageFillable"
      erwartet: "gruen; name kommt zurueck"
    beleg: testausgabe
    ausgefuehrt_von: generator      # php laeuft nur auf Yamas Rechner

  - id: K-03
    aussage: "Der Test kann rot werden."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "im Test probeweise 'title' statt 'name' uebergeben"
      erwartet: "derselbe Test faellt; Rohausgabe BEIDER Laeufe vorlegen"
    beleg: rohausgabe-beider-laeufe
    ausgefuehrt_von: generator

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/produktdaten/13-produktbild-name.md"
  gegenprobe: "siehe K-03 — ein Test, der nicht rot werden kann, prueft nichts."
```

---

> **Rolle:** Planner · **Stand:** 01.08.2026, Nacht · **Heimat-App:** `ticket` · **Spur:** A
> **Ersetzt** den Auftrag `AUF-P1-S2-d2` aus `planner-auftraege-paket1-schritt2-nachbesserung.md`.
> Grund: dort war der Defekt noch uncommittet und als Nachbesserung am Arbeitsbaum geschnitten.
> **Er steht inzwischen in `HEAD`** und braucht deshalb einen eigenen Vorgang.
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 1 · Ziel und Entscheidung

`app/Services/Suppliers/SupplierConnectorService.php:1351` übergibt `'title' => $product->product`
an `ProductImage::firstOrCreate`. Die Spalte `title` existiert an `product_images` nicht, und seit
der `$fillable`-Änderung an `ProductImage` steht `title` auch nicht mehr in der Zuweisungsliste.

**Entscheidung: `'title'` wird zu `'name'`.**

```php
ProductImage::firstOrCreate(
    ['product_id' => $product->id, 'image' => $filename],
    ['name' => $product->product]          // war: 'title'
);
```

### Warum das ein eigener Vorgang ist, kein Schönheitsfehler

Vor der `$fillable`-Änderung warf dieser Aufruf eine `QueryException`. Der umgebende
`catch (\Throwable)` in `:1354` hat sie weggeloggt — es entstand **kein** Bildsatz, aber es gab
eine Spur im Log.

Seit der Änderung wird `title` **stumm verworfen**: der Bildsatz entsteht, `name` bleibt `NULL`,
und es gibt weder Exception noch Logzeile. **Ein lauter Fehler wurde gegen einen leisen
getauscht.** Das ist die Verschlechterung, nicht die Behebung.

Die `$fillable`-Änderung selbst war richtig und bleibt: `product_images` hat die Spalte `name`,
eine Spalte `title` existiert in der einzigen Migration zur Tabelle nicht
(`2023_08_09_084555_create_product_images_table.php`).

## 2 · Spur

**A.** Datenpfad eines Importkanals mit Schreibwirkung auf den Bestand. Der Fahrplan führte
Schritt 2 als Spur B; nach Schutzregel 3 („Spurwechsel nur nach oben") ist dieser Teilvorgang
seit dem Fund Spur A.

## 3 · Nahtstellen

**Angefasst:**

1. `app/Services/Suppliers/SupplierConnectorService.php:1351` — `'title'` → `'name'`
2. Neuer Feature-Test, Ort: `tests/Feature/Product/ProductImageFillableTest.php`

**Bewusst NICHT angefasst:**

| Ort | Warum nicht |
|---|---|
| die fünf bereits korrekten Aufrufer (`ProductImageController:135`, `ProductImageCsvImportController:144,154`, `ProductImportController:220`, `ProductCsvImporter:372`) | übergeben schon `name` |
| `ProductImageController:181-190` (`new ProductImage` + Eigenschaftszuweisung) | umgeht `$fillable`, war nie betroffen |
| der fehlende Fallback | `ProductCsvImporter:372` hat `$product->product ?: $product->article_no`, hier gibt es keinen. Vereinheitlichung ist Umfangserweiterung ⇒ eigener Posten |
| `$product->update(['product_image' => …])` in `ProductImageController:133` | zweite Bildwahrheit an `products` — Posten für Paket 5 |

## 4 · Kantenliste

1. **`firstOrCreate` aktualisiert nicht.** Existiert `(product_id, image)` schon mit `name = NULL`,
   bleibt `NULL`. Bewusst so — der Aufrufer meint „beim Anlegen setzen". Muss dastehen, damit es
   niemand später für einen Fehler hält.
2. **Altbestand mit `name = NULL`.** Zeilen, die zwischen der `$fillable`-Änderung und dieser
   Korrektur entstanden, tragen `NULL`. Ein Backfill ist ein **eigener** Vorgang, kein Beifang.
3. **Leerer Produktname** ⇒ `name = ''` statt `NULL`. Verhalten benennen, nicht heimlich ändern.
4. **Verwaiste Bilddatei.** `File::put` (`:1343`) läuft **vor** `firstOrCreate` (`:1345`). Wirft
   die Datenbank, bleibt die Datei liegen. Bestandsverhalten — benannt, nicht beauftragt.
5. **Keine Factory für `ProductImage` oder `Product`** · BELEGT: `database/factories/` enthält nur
   `User`, `Anforderungsprofil`, `AnforderungsprofilWert`. Der Test muss die Zeilen direkt anlegen
   und dabei alle `NOT NULL`-Spalten von `products` bedienen. **Daran scheitert dieser Auftrag am
   ehesten**, nicht an der Einzeiler-Korrektur.
6. **`RefreshDatabase` gegen `ticket_testing`** (`phpunit.xml:27-28`, MySQL). Der Test darf die
   Live-Datenbank nicht berühren.

## 5 · Rückweg und Entdeckung

**Rückweg:** Eine Zeichenkette in einer Zeile plus eine neue Testdatei. Keine Migration, kein
Schema, kein Datenverlust beim Zurückdrehen. **Vor Beginn: gepusht** — vor dem Deploy ist der
Remote die einzige Kopie außerhalb der Maschine.

**Entdeckung — ein benanntes Signal:**

```sql
SELECT COUNT(*) FROM product_images
WHERE name IS NULL AND image LIKE 'supplier-%';
```

Nach dem nächsten Lieferantenimport mit Bildern muss die Zahl für **neu entstandene** Zeilen 0
sein. Der Präfix `supplier-` wird in `:1341` gesetzt und identifiziert genau diesen Pfad.

## 6 · Abnahmekriterien

1. `grep -n "'title'" app/Services/Suppliers/SupplierConnectorService.php` liefert im
   `ProductImage`-Block **null Treffer**.
2. Ein Feature-Test legt einen `ProductImage` per **Mass Assignment** mit `name` an und liest den
   Wert zurück. Das ist Kriterium (d) aus `20-implementation-roadmap.md:101` — bisher gar nicht
   adressiert, denn **keine der 128 Testdateien nennt `ProductImage`**.
3. **Gegen-Beweis, im Kriterium verankert:** Derselbe Test muss **fallen**, wenn man probeweise
   `'title'` statt `'name'` übergibt. Der Generator führt die Rot-Probe durch und legt die
   Rohausgabe **beider** Läufe vor. Ein Test, der nicht rot werden kann, prüft nichts.
4. Suite gegen die Baseline aus Schritt 1 nicht schlechter.

## 7 · Heimat-App

`ticket`. Keine andere App berührt.

## 8 · Ledger

```
PLANNER 2026-08-01 · AUF-P1-S2-d3 · Spur A (hochgestuft aus B) · Heimat ticket
  Ersetzt AUF-P1-S2-d2: der Defekt steht seit 247dc6c1-Umfeld in HEAD, nicht mehr im Arbeitsbaum.
  Entscheidung: SupplierConnectorService.php:1351 'title' -> 'name', plus Feature-Test mit Rot-Probe.
  Grund: die $fillable-Aenderung tauschte an dieser Stelle eine Exception gegen stillen Verlust.
  Ballbesitz: Generator - NICHT die Instanz, die diesen Auftrag geschrieben hat.
```
