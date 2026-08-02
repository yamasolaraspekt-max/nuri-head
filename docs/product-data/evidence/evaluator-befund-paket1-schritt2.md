# Evaluator-Befund · Paket 1, Schritt 2 — „Belegte Nulldefekte beheben"

> **Rolle:** Evaluator · **Stand:** 2026-08-01 · **Heimat-App:** `ticket` · **Spur laut Planner:** B
> **Generator:** Yama (andere Sitzung) — Rollentrennung nach `pruefrahmen.md` §3 gewahrt
> **Grundlage:** `20-implementation-roadmap.md` §3 Schritt 2, Abnahmekriterien Zeile 101
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 0 · Urteil in einem Satz

**Schritt 2 ist ROT** — nicht weil die drei Änderungen schlecht wären, sondern weil (d) über die
Spezifikation hinausgeht und dabei eine unbehandelte Folge an einer zweiten Stelle erzeugt, (c) gar
nicht umgesetzt ist, und für **keine** der vier Behebungen das geforderte mechanische
Abnahmekriterium erfüllt wurde.

| Defekt | Umgesetzt | Urteil | Grund |
|---|---|---|---|
| a · tote Route | ja | **grün, mit Vorbehalt** | Sachbeweis stark; `php artisan route:list` nicht ausführbar |
| b · View-Name | ja | **grün, mit Vorbehalt** | Sachbeweis vollständig; Testupload nicht ausführbar |
| c · `fusion_forms` doppelt | **nein** | **offen** | war blockiert — **die Blockade ist mit diesem Befund aufgelöst** |
| d · `ProductImage::$fillable` | ja | **ROT** | Umfangsüberschreitung + unbehandelte Folge + kein Test |

---

## 1 · Umgebungsgrenze — was dieses Urteil nicht leisten kann · BELEGT

```
php       NICHT VORHANDEN
mysql     NICHT VORHANDEN
composer  NICHT VORHANDEN
node      v22.22.3
npm       10.9.8
```

Daraus folgt hart:

- **Regressions-Baseline nicht erhebbar.** `pruefrahmen.md` §3 nennt sie „stehende Pflicht". Ohne
  PHPUnit gibt es weder Vorher- noch Nachher-Menge. Kein Urteil dieses Befunds darf als „Testsuite
  grün" gelesen werden.
- `php artisan route:list` (Kriterium a) und der Testupload (Kriterium b) sind nicht ausführbar.
- `config('services.fusion_forms.token')` (Kriterium c) ist nicht auflösbar — ich habe stattdessen
  die Quelle gemessen, siehe §5.

Der Testbestand: **128 Testdateien**, davon 14 Sicherheits-Referenzfälle unter
`tests/Feature/Security/`. `phpunit.xml:28` fährt gegen `ticket_testing`, nicht gegen `ticket`.

---

## 2 · Repo-Aufsicht — der Baum hat sich während der Messung bewegt · BELEGT

```
HEAD bei Sitzungsstart : 78253dc7
HEAD am Ende der Messung: 04371048     (9 neue Commits)
Ungepusht: 43 (deine Übergabe) -> 45 (Start) -> 54 (Ende)
```

Governance: *„Wer merkt, dass der HEAD sich unter ihm bewegt hat, hört auf zu messen und meldet es."*
Hiermit gemeldet. **Meine Messwerte bleiben trotzdem gültig**, weil die 9 Commits ausschließlich
`docs/` berühren (`git diff --stat 78253dc7..HEAD` = 9 Dateien, alle unter `docs/`) und die Diffs
meiner drei Messobjekte unverändert sind:

```
routes/web.php                               0  1
app/Http/Controllers/DatanormController.php  1  1
app/Models/ProductImage.php                  1  1
```

Zusätzlich sind zwei fremde Dateien neu im Baum: `scripts/auftrag-pruefen.mjs`,
`scripts/__tests__/auftragPruefen.test.mjs`. **Nicht mit `git add -A` einsammeln.**

**54 ungepushte Commits = weiterhin kein Backup außerhalb der Maschine.**

---

## 3 · Defekt a — tote Route · GRÜN mit Vorbehalt

**Umsetzung (Rohdiff):**
```diff
-    Route::post('/ids/search/inline-forward', [IdsSearchController::class, 'forwardToShopInline'])->name('ids.search.forward.inline');
```

**Nachgemessen — der Defekt ist echt:**

Die Roadmap nennt `routes/web.php:520`, sagt aber nicht, wo der Controller liegt. Gefunden unter
`app/Http/Controllers/Product/IDS/gconline/IdsSearchController.php` (Import: `routes/web.php:309`).
Seine vollständige Methodenliste:

```
12: __construct   19: form   24: requestPriceForMaterial
140: forwardToShop   173: back   178: forms   183: inlineBack
```

Kein `forwardToShopInline`. Suche über `app/ routes/ resources/ tests/`: **null Treffer.**

**Gegen-Beweis 1 — war die Methode je da?**
```
git log --oneline -S"forwardToShopInline" -- app/ routes/
18948032 Back up application code: add app/ + routes/ to git (LOCAL private branch)
```
Genau ein Commit, der Initial-Import. **Die Methode hat nie existiert.** Die Route war ab Tag eins
tot — jeder Aufruf hätte einen `ReflectionException` geworfen.

**Gegen-Beweis 2 — bricht die Entfernung etwas?**
```
grep -rn "ids.search.forward.inline"  app/ resources/ routes/ tests/ config/  -> null Treffer
grep -rn "inline-forward"             app/ resources/ routes/ tests/ public/  -> null Treffer
```
Weder Routenname noch URI werden irgendwo referenziert. Nichts hängt daran.

**OFFEN:** Kriterium a fordert wörtlich `php artisan route:list`. Der Sachbeweis ist stärker als das
Kriterium (die Methode existierte nie), aber er ist nicht der geforderte Beleg. **Yama muss den
Befehl einmal fahren.**

---

## 4 · Defekt b — View-Name · GRÜN mit Vorbehalt

**Umsetzung:**
```diff
-        return view('datanorm.upload', compact('parsedData'));
+        return view('admin.datanorm.upload', compact('parsedData'));
```

**Nachgemessen:**
```
find resources/views -ipath "*datanorm*" -type f
resources/views/admin/datanorm/upload.blade.php        <- einzige Datei
```
`resources/views/datanorm/` existiert nicht. Der Defekt ist echt: jeder Upload warf eine
`InvalidArgumentException: View [datanorm.upload] not found`.

**Gegen-Beweis 1 — ist der neue Name geraten oder belegt?**
Belegt. Der *zweite* `view()`-Aufruf desselben Controllers benutzte schon immer den richtigen Namen:
```
DatanormController.php:11   return view('admin.datanorm.upload');       <- showForm, unverändert
DatanormController.php:47   return view('admin.datanorm.upload', ...);  <- parseFile, korrigiert
```
Die Korrektur stellt Konsistenz innerhalb einer Datei her. Beide Routen (`datanorm.form`,
`datanorm.parse`, `routes/web.php:4741-4742`) zeigen jetzt auf dieselbe Ansicht — genau so ist die
View gebaut.

**Gegen-Beweis 2 — verträgt die View die übergebene Variable?**
Ja, und zwar ausdrücklich:
```blade
upload.blade.php:117   @if(isset($parsedData))
upload.blade.php:119   <pre …>{{ json_encode($parsedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
```
Das `isset()` ist der Grund, warum dieselbe View beide Routen bedienen kann. Sie hängt an
`@extends('admin.layouts.app')` und definiert `@section('content')` — das Layout passt.

**Randnotiz, nicht blockierend:** Die View trägt `@section('title') Verfügbarkeit` und einen
Breadcrumb „Mitarbeiter" — beides ist aus einer anderen Ansicht kopiert und gehört nicht zu
DATANORM. Kosmetik, eigener Posten, Spur B.

**OFFEN:** Kriterium b fordert einen Testupload, der die View rendert statt zu werfen. Nicht
ausführbar. **Yama muss einmal hochladen.**

---

## 5 · Defekt c — `fusion_forms` doppelt · NICHT UMGESETZT, aber entblockt

Du hattest das zurückgestellt mit: *„Welches Token gilt — `FUSION_FORMS_TOKEN` oder
`FUSION_WEBHOOK_TOKEN`? Bis dahin nicht anfassen."* **Diese Frage ist beantwortet.**

**Ist-Zustand · BELEGT** (`config/services.php:34-40`):
```php
    'fusion_forms' => [
        'token' => env('FUSION_FORMS_TOKEN'),
    ],

    'fusion_forms' => [
        'token' => env('FUSION_WEBHOOK_TOKEN'),
    ],
```
Prüfung auf weitere Doppelschlüssel in der Datei: nur dieser eine.

**Die entscheidende Messung — ohne die Werte zu zeigen:**
```
FUSION_FORMS_TOKEN   = GESETZT (Länge 24)
FUSION_WEBHOOK_TOKEN = GESETZT (Länge 24)
Vergleich der beiden Werte: IDENTISCH
```

**BEWERTUNG.** Heute gilt in PHP der zweite Array-Schlüssel, also `FUSION_WEBHOOK_TOKEN`. Weil beide
`.env`-Variablen aber **denselben Wert** tragen, hat der Doppelschlüssel **heute keine Wirkung**.
Die Bereinigung ist damit risikofrei: welche der beiden Zeilen du behältst, ändert am laufenden
Betrieb nichts. Kein Webhook bricht.

**Empfehlung an den Planner (nicht meine Entscheidung):** die zweite Zeile behalten
(`FUSION_WEBHOOK_TOKEN`), weil sie den heute wirksamen Pfad beschreibt, und die erste ersatzlos
streichen — nicht umbenennen. Eine Umbenennung erzeugt einen zweiten Konfigurationsschlüssel, den
niemand liest; das ist eine neue verwaiste Wahrheit.

**Was daran hängt — 7 Lesestellen, alle auf denselben Schlüssel:**
```
app/Http/Middleware/VerifyFusionToken.php:19
app/Http/Controllers/FusionWebhookController.php:18, 90, 107
app/Http/Controllers/Wordpress/FusionFormController.php:39
app/Http/Controllers/Wordpress/FusionFormSubmissionController.php:58, 246
tests/Feature/FusionWebhookTest.php:48        <- es gibt einen Test
```
`tests/Feature/FusionWebhookTest.php` ist der Regressionsschutz für diese Änderung. **Er existiert
bereits** — Kriterium c ist damit nach der Umsetzung mechanisch prüfbar, sobald du die Suite fährst.

---

## 6 · Defekt d — `ProductImage::$fillable` · ROT

**Umsetzung:**
```diff
-    protected $fillable = ['product_id', 'image', 'title'];
+    protected $fillable = ['product_id', 'name', 'image'];
```

**Nachgemessen — das Schema · BELEGT.** Genau eine Migration berührt die Tabelle
(`database/migrations/2023_08_09_084555_create_product_images_table.php`):
```php
$table->id();
$table->unsignedBigInteger('product_id');
$table->string('name')->nullable();
$table->string('image');
$table->timestamps();
```
Keine spätere Migration, kein `rename`, kein `addColumn`. Spalte `title` gibt es nicht, Spalte `name`
schon. **Die Richtung der Änderung ist korrekt.**

### 6.1 · Was die Änderung wirklich repariert — größer als die Roadmap sagt

Fünf Mass-Assignment-Pfade übergeben `name` und verloren es bisher **still**, weil `name` nicht
fillable war:

| Fundstelle | Aufruf |
|---|---|
| `ProductImageController.php:135` | `ProductImage::create(['product_id','name','image'])` |
| `ProductImageCsvImportController.php:144` | `ProductImage::create(['product_id','name','image'])` |
| `ProductImageCsvImportController.php:154` | `ProductImage::create(['product_id','name','image'])` |
| `ProductImportController.php:220` | `firstOrCreate([…], ['name' => $product->product])` |
| `ProductCsvImporter.php:372` | `updateOrCreate([…], ['name' => …])` |

`ProductImageController.php:181-190` (`new ProductImage` + `$data->name = …`) war nie betroffen —
Eigenschaftszuweisung umgeht `$fillable`. Deshalb hat es nie jemandem auffallen können: **ein**
Pfad funktionierte, fünf verloren stumm.

### 6.2 · Warum es trotzdem rot ist — drei Gründe

**Grund 1 — Umfangsüberschreitung.** Die Spezifikation sagt (`20:97`): *„Feld ergänzen"*. Umgesetzt
wurde ergänzen **und entfernen** (`title` ist raus). `pruefrahmen.md` §7: die Änderung sitzt dort,
wo der Planner sie vorgesehen hat, *und nicht darüber hinaus*. Die Entfernung mag richtig sein — sie
ist aber nicht beauftragt und hätte an den Planner zurückgehen müssen.

**Grund 2 — die Entfernung hat eine unbehandelte Folge · BELEGT.**
`app/Services/Suppliers/SupplierConnectorService.php:1345-1352`:
```php
ProductImage::firstOrCreate(
    ['product_id' => $product->id, 'image' => $filename],
    ['title' => $product->product]          // <- 'title', nicht 'name'
);
```
umschlossen von `try { … } catch (\Throwable $exception) { Log::warning(…); }` (Zeile 1354).

| | vorher (`title` fillable) | nachher (`title` nicht fillable) |
|---|---|---|
| Eloquent | nimmt `title` ins INSERT | verwirft `title` still |
| DB | Spalte fehlt → `QueryException` | INSERT geht durch |
| `catch` | fängt, loggt Warnung | greift nicht |
| Ergebnis | **kein Bildsatz**, aber eine Logzeile | **Bildsatz mit `name = NULL`**, keine Logzeile |

Das ist eine **Verhaltensänderung an einem Importpfad**, kein Nulldefekt. Sie ist unterm Strich
besser (das Bild wird jetzt gespeichert), aber sie tauscht einen lauten Fehler gegen einen leisen:
die Absicht des Aufrufers — *Bildname = Produktname* — wird ab jetzt kommentarlos verworfen, ohne
Exception und ohne Logeintrag. Der Aufräumposten `'title'` → `'name'` an Zeile 1351 gehört
zwingend dazu, sonst ist der Defekt nur verschoben.

> **ANNAHME, die nur du auflösen kannst:** Ich schließe aus dem Migrationsstand, dass
> `product_images` keine Spalte `title` hat. Falls jemand die Spalte je von Hand angelegt hat,
> stimmt die Tabelle oben nicht. Eine Zeile genügt:
> `SHOW COLUMNS FROM product_images;`

**Grund 3 — Spurverstoß.** Spur B verlangt *„kein Datenpfad, keine PHP-Logik"*. `$fillable` ist
definitionsgemäß ein Datenpfad, und Grund 2 zeigt, dass die Änderung reales Laufzeitverhalten
verschiebt. Nach Schutzregel 3 gilt: **hoch auf Spur A, zurück an den Planner.** Nach oben wechselt
man, nach unten nicht.

### 6.3 · Abnahmekriterium nicht erfüllt

Kriterium d: *„Ein Test setzt das Feld und liest es zurück."*
```
grep -rln "ProductImage" tests/   ->  (kein Treffer)
```
Von 128 Testdateien nennt keine einzige `ProductImage`. Das Kriterium ist nicht knapp verfehlt,
sondern gar nicht adressiert — und es ist das einzige der vier Kriterien, das **ohne** laufende
Datenbank ohnehin nicht abnehmbar wäre.

---

## 7 · Wächter-Durchlauf

| Invariante | Ergebnis |
|---|---|
| Regressions-Baseline vorher/nachher | **nicht erhebbar** (kein PHP) — siehe §1 |
| Testsuite selbst ausgeführt | **nein**, nicht möglich |
| Referenzfälle `tests/Feature/Security/` (14 Dateien) | von den drei Änderungen nicht berührt (keine Route mit Auth-Gate entfernt, kein Controller mit Ownership-Prüfung angefasst) |
| Keine Regression an Bestandsdaten | a, b: n.z. (keine Datenberührung). d: siehe §6.2 — **eine Verhaltensänderung, unbehandelt** |
| Schreib-Heimat `ticket` eingehalten | ja, alle drei Dateien liegen in `ticket` |
| Keine verwaiste zweite Wahrheit | a: beseitigt eine. b: beseitigt eine. d: **erzeugt eine** (`'title'` an `SupplierConnectorService:1351` ist jetzt eine Zuweisung ins Leere) |
| Bauordnung `ticket` (Sicherheit/IDOR) | unberührt |

---

## 8 · Zusatzbefunde — nicht beauftragt, für den Planner

**Z1 — derselbe Fehler wie Defekt c, nur in den Routen · BELEGT.**
`routes/web.php:692-693`:
```php
Route::post('/fusion/webhook/ajax', [FusionFormSubmissionController::class, 'webhookAjax'])->name('fusion.webhook.ajax');
Route::post('/fusion/webhook/ajax', [FusionWebhookController::class, 'handleAjax'])->name('fusion.webhook.ajax');
```
Gleiche Methode, gleiche URI, gleicher Name, zwei verschiedene Controller. Die zweite gewinnt →
`FusionFormSubmissionController::webhookAjax` ist toter Code, den niemand erreicht. Dieselbe
Fehlerklasse wie `config/services.php:34,38` — und wieder im Fusion-Webhook-Pfad.

**Z2 — 77 doppelte Routennamen in `routes/web.php`**, einer in `routes/api.php` (`api.mobile.`).
Gemessen mit `grep -oE -- "->name\('[^']*'\)" routes/web.php | sort | uniq -d | wc -l`. Ein Teil
davon sind Gruppenpräfixe (`admin.`, `admin.kanban.tasks.`) und harmlos; der Rest ist ungeprüft.
**Das ist ANNAHME-Material, kein Befund** — es sagt nur, wo sich hinzusehen lohnt. Kandidat für einen
eigenen Posten in Paket 1, Schritt 5 („Doppelwahrheit stilllegen"), weil es exakt dieselbe Sorte
Doppelwahrheit ist.

**Z3 — Fundstellenkorrektur für die Roadmap.** `20:94` nennt für Defekt a nur `routes/web.php:520`.
Der zugehörige Controller liegt unter `app/Http/Controllers/Product/IDS/gconline/`, nicht unter
`app/Http/Controllers/`. Wer nach `app/Http/Controllers/IdsSearchController.php` sucht, findet
nichts und hält die Route für unauffindbar.

---

## 9 · Ballbesitz — was jetzt zu tun ist, in dieser Reihenfolge

**Bei dir (Yama), noch vor allem anderen:**

1. **Push.** 54 Commits, kein Backup außerhalb der Maschine. `20:157` verlangt das ausdrücklich
   *vor* Schritt 4.
2. Drei Befehle, die nur dein Rechner kann — sie schließen die Vorbehalte aus §3, §4, §6:
   ```
   php artisan route:list | grep ids.search           # Kriterium a
   SHOW COLUMNS FROM product_images;                  # löst die ANNAHME in §6.2 auf
   php artisan test                                   # Regressions-Baseline
   ```
   Die Baseline bitte **vor** weiteren Änderungen festhalten — „26/122" ist erst dann ein Urteil,
   wenn bekannt ist, dass 26/122 auch vorher galt.

**Zurück an den Generator (Spur A, weil §6.2 einen Datenpfad berührt):**

3. `SupplierConnectorService.php:1351`: `'title'` → `'name'`.
4. Ein Test, der einen `ProductImage` mit `name` per Mass Assignment anlegt und zurückliest
   (Kriterium d).

**Zurück an den Planner:**

5. Defekt c freigeben — die Blockade ist nach §5 aufgelöst, beide Token sind identisch, die
   Bereinigung ist verhaltensneutral.
6. Entscheiden, ob Z1 (`routes/web.php:692-693`) als fünfter Nulldefekt in Schritt 2 nachgezogen
   oder nach Schritt 5 verschoben wird.

**Erst wenn 3 bis 5 durch sind und die Suite gegen die Baseline steht**, ist Schritt 2 grün und
Schritt 3 an der Reihe.
