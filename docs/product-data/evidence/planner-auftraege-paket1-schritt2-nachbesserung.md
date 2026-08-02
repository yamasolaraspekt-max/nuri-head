# Planner-Aufträge · Paket 1, Schritt 2 — Nachbesserung

> **Rolle:** Planner · **Stand:** 2026-08-01 · **Heimat-App:** `ticket`
> **Anlass:** Evaluator-Urteil rot zu Schritt 2 (`evaluator-befund-paket1-schritt2.md`)
> **Rollenwechsel angesagt:** Ich war Evaluator dieses Vorgangs. Diese Aufträge sind ein
> getrennter Durchgang. Die Umsetzung macht ein Generator, die Abnahme ein Evaluator —
> **beides nicht ich.**
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## Auftrag AUF-P1-S2-c · `fusion_forms` Doppelschlüssel auflösen

### Ziel & Entscheidung

`config/services.php` enthält den Schlüssel `'fusion_forms'` zweimal (Zeile 34 und 38). In PHP
gewinnt der zweite; der erste ist unerreichbarer Code. **Entscheidung: Der zweite Block bleibt
(`FUSION_WEBHOOK_TOKEN`), der erste wird ersatzlos gestrichen — nicht umbenannt.**

Zielzustand:
```php
    'fusion_forms' => [
        'token' => env('FUSION_WEBHOOK_TOKEN'),
    ],
```

**Warum der zweite und nicht der erste:** Er beschreibt den heute wirksamen Pfad. Behielte man den
ersten, wechselte die gelesene Umgebungsvariable — eine Verhaltensänderung, die dieser Auftrag
ausdrücklich nicht will.

**Warum streichen und nicht umbenennen:** Der Fahrplan schlug „zweiten Schlüssel umbenennen" vor
(`20:96`). Ich weiche ab. Ein umbenannter Schlüssel (`fusion_forms_legacy` o. ä.) wird von keiner
einzigen Stelle gelesen — das ist per Definition eine verwaiste zweite Wahrheit, und deren
Vermeidung steht im Wächter. Streichen ist die einzige Variante, die nichts zurücklässt.

**Warum das gefahrlos ist · BELEGT.** Beide `.env`-Variablen sind gesetzt (je 24 Zeichen) und tragen
**denselben Wert** (Vergleich ohne Anzeige der Werte durchgeführt). Der Doppelschlüssel hat heute
keine Wirkung; die Bereinigung ist verhaltensneutral. Damit ist Yamas Blockade („welches Token
gilt?") gegenstandslos.

### Spur

**A** — die Zeile steuert die Autorisierung von sieben Endpunkten; Autorisierung ist nach der
Governance immer Spur A, auch wenn die Messung Verhaltensneutralität belegt.

### Nahtstellen

**Angefasst wird genau eine Datei:** `config/services.php`, Zeilen 34–36 entfernen.

**Bewusst NICHT angefasst:**

| Ort | Warum nicht |
|---|---|
| `.env` | Nicht im Repo, pro Umgebung gepflegt. Das Entfernen der dann unbenutzten `FUSION_FORMS_TOKEN` ist ein eigener Posten — und einer, den nur Yama auf jeder Umgebung nachziehen kann. |
| die 7 Lesestellen | Sie lesen alle denselben Schlüssel `services.fusion_forms.token` und bleiben unverändert korrekt. |
| `app/Http/Kernel.php:74` (`'fusion.token'`) | Alias unverändert. |
| die zwei verschiedenen Header-Namen | `X-Fusion-Token` (5 Stellen) vs. `X-Fusion-Form-Token` (2 Stellen) gegen **denselben** Config-Wert. Eigener Befund, eigener Posten — nicht in diesem Auftrag. |

**Erweiterungspunkt, nicht jetzt bauen:** Falls je zwei getrennte Geheimnisse nötig werden (ein
Webhook-Token, ein Formular-Token), ist der Andockpunkt ein zweiter, *anders benannter*
Config-Schlüssel plus Umstellung der jeweiligen Lesestellen. Kein Umbau des heutigen nötig.

### Kantenliste

1. **Config-Cache.** Liegt `bootstrap/cache/config.php`, wirkt die Änderung erst nach
   `php artisan config:clear` bzw. `config:cache`. Ohne diesen Schritt sieht der Generator eine
   grüne Prüfung, die nichts über den laufenden Betrieb aussagt.
2. **Config wird versehentlich leer.** Tippfehler im `env()`-Namen ⇒ `null`. Folge ist **kein**
   Auth-Bypass — die zwei öffentlich erreichbaren Eingänge (`api.php:86` hinter
   `VerifyFusionToken`, `web.php:614` `/receive-fusion-form`) haben beide einen `!$token`-Vorabtest
   und antworten dann mit 401. Die Folge ist ein **Totalausfall der Lead-Annahme**: die Website
   liefert nichts mehr ein. Das ist die eigentliche Gefahr dieses Auftrags.
3. **Trailing Whitespace in `.env`.** `env()` liefert den Wert mitsamt Leerzeichen; der Vergleich
   ist strikt (`!==`). Nicht durch diesen Auftrag verursacht, aber beim Prüfen zu beachten.
4. **`FusionWebhookController::webhook()` (Zeile 86) ist nicht geroutet · BELEGT** — toter Code.
   Nicht in diesem Auftrag entfernen; gehört zu Schritt 5.

### Rückweg & Entdeckung

**Rückweg:** Reine Entfernung dreier Zeilen, keine Migration, kein Datenbezug ⇒ Commit
zurückdrehbar. **Vor Beginn muss der aktuelle Stand gepusht sein** — vor dem Deploy ist der Remote
die einzige Kopie außerhalb der Maschine.

**Entdeckung:** Zwei benannte Signale, 48 Stunden lang:
- Neue Zeilen in `fusion_form_submissions` / `wp_fusion_form_entries` kommen weiter an. Bleiben sie
  aus, greift Kante 2.
- 401-Zähler auf `POST /receive-fusion-form` und `POST /api/fusion/webhook`. Steigt er, ebenfalls
  Kante 2.

### Abnahmekriterien

1. `grep -c "^    'fusion_forms' =>" config/services.php` liefert **1**, nicht 2.
2. `php artisan config:clear && php artisan tinker --execute="echo config('services.fusion_forms.token');"`
   gibt denselben Wert aus wie `FUSION_WEBHOOK_TOKEN` in `.env`. *(Kriterium c aus `20:101`,
   mechanisch erfüllt.)*
3. `tests/Feature/FusionWebhookTest.php` bleibt grün.
   **Vorbehalt, den der Evaluator kennen muss:** Dieser Test übergibt in Zeile 48 selbst
   `config('services.fusion_forms.token')` als Header. Er wandert also mit der Config mit und kann
   nur eine **leere** Config aufdecken, nicht eine *falsch gewählte*. Als alleiniger Beleg genügt er
   nicht — Kriterium 2 ist der tragende.
4. Suite gegen die Baseline aus Schritt 1 nicht schlechter.

### Heimat-App

`ticket`. Keine andere App berührt.

---

## Auftrag AUF-P1-S2-d2 · `ProductImage`-Nachbesserung

### Ziel & Entscheidung

Die bereits umgesetzte Änderung an `app/Models/ProductImage.php` (`$fillable` von
`['product_id','image','title']` auf `['product_id','name','image']`) **bleibt bestehen und wird
bestätigt** — `product_images` hat die Spalte `name`, eine Spalte `title` existiert nicht
(`2023_08_09_084555_create_product_images_table.php`, einzige Migration zur Tabelle).

**Entscheidung: Der von ihr erzeugte Folgefehler wird im selben Vorgang geschlossen.**
`app/Services/Suppliers/SupplierConnectorService.php:1351` übergibt `'title' => $product->product`
an `ProductImage::firstOrCreate` und schreibt damit seit der Änderung ins Leere. Zielzustand:

```php
ProductImage::firstOrCreate(
    ['product_id' => $product->id, 'image' => $filename],
    ['name' => $product->product]          // war: 'title'
);
```

**Warum das zwingend dazugehört:** Vor der `$fillable`-Änderung warf dieser Aufruf eine
`QueryException` (Spalte `title` existiert nicht), die der umgebende `catch (\Throwable)` in Zeile
1354 still wegloggte — es entstand **kein** Bildsatz. Seit der Änderung wird `title` stumm verworfen:
der Bildsatz entsteht, `name` bleibt `NULL`, und es gibt weder Exception noch Logzeile. Ein lauter
Fehler ist gegen einen leisen getauscht. Ohne diesen Auftrag ist der Defekt nicht behoben, sondern
verschoben.

### Spur

**A** — Datenpfad eines Importkanals, Verhaltensänderung an Bestandsschreibung. Der Fahrplan führte
Schritt 2 als Spur B; nach Schutzregel 3 („Spurwechsel nur nach oben") ist dieser Teilvorgang ab
sofort A.

### Nahtstellen

**Angefasst:**
1. `app/Services/Suppliers/SupplierConnectorService.php:1351` — `'title'` → `'name'`.
2. Ein neuer Feature-Test (Ort: `tests/Feature/Product/ProductImageFillableTest.php`).

**Bewusst NICHT angefasst:**

| Ort | Warum nicht |
|---|---|
| die fünf bereits korrekten Pfade | `ProductImageController:135`, `ProductImageCsvImportController:144,154`, `ProductImportController:220`, `ProductCsvImporter:372` übergeben schon `name` und funktionieren seit der `$fillable`-Änderung. |
| `ProductImageController:181-190` (`new ProductImage` + `$data->name = …`) | Eigenschaftszuweisung umgeht `$fillable`, war nie betroffen. |
| der fehlende Fallback | `ProductCsvImporter:372` hat `$product->product ?: $product->article_no`; `SupplierConnectorService` hat keinen. Vereinheitlichung ist Umfangserweiterung ⇒ eigener Posten. |
| `$product->update(['product_image' => …])` in `ProductImageController:133` | Zweite Bildwahrheit an `products`, unabhängig von diesem Vorgang. Posten für Paket 5. |

**Erweiterungspunkt, nicht jetzt bauen:** Wenn Paket 5 (`product_media` mit MD-Codes und Hash)
kommt, ersetzt es `product_images` als führende Medientabelle. Dieser Auftrag baut nichts, was das
erschwert — er korrigiert eine Feldzuweisung.

### Kantenliste

Gehört in die Tests bzw. in die Prüfung:

1. **`firstOrCreate` aktualisiert nicht.** Existiert `(product_id, image)` bereits mit
   `name = NULL`, bleibt `NULL` — der zweite Import repariert nichts. Bewusst so: der Aufrufer meint
   „beim Anlegen setzen". Muss im Auftrag stehen, damit es niemand später für einen Fehler hält.
2. **Altbestand mit `name = NULL`.** Zeilen, die zwischen der `$fillable`-Änderung und dieser
   Nachbesserung entstanden sind, tragen `NULL`. Ein Backfill ist ein **eigener** Vorgang, nicht
   Beifang hier.
3. **Leerer Produktname.** `$product->product` kann `''` sein ⇒ `name = ''` statt `NULL`. Verhalten
   benennen, nicht heimlich ändern.
4. **Verwaiste Bilddatei.** `File::put` (Zeile 1343) läuft **vor** `firstOrCreate` (1345). Wirft die
   DB, bleibt die Datei liegen. Bestandsverhalten, durch diesen Auftrag weder verursacht noch
   behoben — nur benannt.
5. **Keine Factory für `ProductImage` oder `Product` · BELEGT** (`database/factories/` enthält nur
   `User`, `Anforderungsprofil`, `AnforderungsprofilWert`). Der Test muss die Zeilen direkt anlegen
   und dabei alle `NOT NULL`-Spalten von `products` bedienen. Das ist der wahrscheinlichste Grund,
   an dem dieser Auftrag scheitert.
6. **`RefreshDatabase` gegen `ticket_testing`** (`phpunit.xml:27-28`, MySQL, nicht SQLite). Der Test
   darf die Live-DB nicht berühren.

### Rückweg & Entdeckung

**Rückweg:** Eine Zeichenkette in einer Zeile plus eine neue Testdatei. Keine Migration, kein
Schema. Commit zurückdrehbar ohne Datenverlust. **Vor Beginn: gepusht.**

**Entdeckung — ein benanntes Signal, kein „wird schon auffallen":**
```sql
SELECT COUNT(*) FROM product_images
WHERE name IS NULL AND image LIKE 'supplier-%';
```
Nach dem nächsten Lieferantenimport mit Bildern muss die Zahl für **neu entstandene** Zeilen 0 sein.
Steigt sie, greift die Korrektur nicht. (Der Präfix `supplier-` wird in Zeile 1341 gesetzt und
identifiziert genau diesen Pfad.)

### Abnahmekriterien

1. `grep -n "'title'" app/Services/Suppliers/SupplierConnectorService.php` liefert im
   `ProductImage`-Block **null Treffer**. *(Zeile 1351 ist der einzige Treffer dort; andere Treffer
   in der Datei sind nicht Teil dieses Auftrags und bleiben.)*
2. Ein Feature-Test legt einen `ProductImage` per **Mass Assignment** mit `name` an und liest den
   Wert zurück. *(Das ist Kriterium d aus `20:101`, bisher gar nicht adressiert — kein einziger der
   128 Testdateien nennt `ProductImage`.)*
3. **Gegen-Beweis, im Kriterium verankert:** Derselbe Test muss **fallen**, wenn man im Test
   probeweise `'title'` statt `'name'` übergibt. Fällt er nicht, prüft er nicht, was er behauptet.
   Der Generator führt diese Rot-Probe durch und legt die Rohausgabe beider Läufe vor.
4. Suite gegen die Baseline aus Schritt 1 nicht schlechter.

### Heimat-App

`ticket`.

---

## Was danach noch offen bleibt

Diese zwei Aufträge schließen Schritt 2 **nicht** allein. Es fehlt weiterhin, und zwar nur bei Yama:

| # | Offen | Warum nur Yama |
|---|---|---|
| 1 | Regressions-Baseline (`php artisan test`) | kein `php` in der Analyseumgebung — belegt |
| 2 | `php artisan route:list` für Kriterium a | dito |
| 3 | Testupload für Kriterium b | dito |
| 4 | `SHOW COLUMNS FROM product_images;` | löst meine ANNAHME auf, dass es keine Spalte `title` gibt |

**Erst wenn diese vier vorliegen und ein unabhängiger Evaluator die beiden Aufträge oben abgenommen
hat, ist Schritt 2 grün.** Schritt 3 ist danach an der Reihe — und braucht seinerseits
`ergebnis-2026-08.txt` aus Schritt 1.

## Ledger-Zeilen (zum Einfügen in `docs/handoff-status.md`)

```
PLANNER 2026-08-01 · AUF-P1-S2-c · Spur A · Heimat ticket
  Entscheidung: config/services.php Z.34-36 ersatzlos streichen, FUSION_WEBHOOK_TOKEN bleibt.
  Belegt verhaltensneutral: beide .env-Werte identisch. Blockade aus der Uebergabe aufgeloest.
  Ballbesitz: Generator.

PLANNER 2026-08-01 · AUF-P1-S2-d2 · Spur A (hochgestuft aus B) · Heimat ticket
  Entscheidung: SupplierConnectorService.php:1351 'title' -> 'name', plus Feature-Test mit Rot-Probe.
  Grund: die $fillable-Aenderung tauschte dort eine Exception gegen stillen Datenverlust.
  Ballbesitz: Generator.

EVALUATOR 2026-08-01 · Paket 1 Schritt 2 · Urteil ROT
  a gruen mit Vorbehalt, b gruen mit Vorbehalt, c offen (entblockt), d rot.
  Beleg: evaluator-befund-paket1-schritt2.md. Baseline nicht erhebbar (kein php/mysql).
  Ballbesitz: Yama (4 Messbefehle), danach Generator.

OFFEN AN YAMA · maszgebliche DB = ticket auf 3307 (entschieden 2026-08-01, 20 §6 Frage 3).
OFFEN AN YAMA · E4 getrennt in E4a (Weg D Punchout) und E4b (88 Preiszeilen).
```
