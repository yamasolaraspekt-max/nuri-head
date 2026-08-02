# 12 · E4a entschieden — Weg D (Angebotsvorlagen-Punchout)

> **Rolle:** Planner · **Stand:** 01.08.2026, Nacht · **Heimat-App:** `ticket` · **Spur:** A
> **Bezug:** `20-implementation-roadmap.md` §6 Frage 4 · `11-identitaetsspezifikation-*.md` §3
> **Grundlage:** vollständige Lesung von `OfferTemplateSupplierController.php` (765 Zeilen)
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 0 · Die Frage war falsch gestellt

Der Fahrplan bietet zwei Antworten an: **stilllegen** oder **auf den zentralen Dienst umhängen**.
Beide gehen an der Sache vorbei, weil beide unterstellen, Weg D sei ein zweiter IDS-Parser.

**Er ist keiner.** Weg D parst kein IDS-XML — kein `Warenkorb`, kein `OrderItem`, keine
XSD-Validierung, kein Aufruf gegen die Schemata. Er ist ein **generischer Punchout-Client**, und
sein Hauptformat ist **SAP OCI**.

---

## 1 · Was Weg D wirklich tut — BELEGT

### 1.1 · Drei Eingangsformate, keines davon IDS

`extractItems()` (`:405-461`) probiert der Reihe nach:

| Reihenfolge | Format | Erkennung |
|---|---|---|
| 1 | JSON-Liste | Schlüssel `items`, `products`, `positions` oder `basket`, wenn Liste |
| 2 | **SAP OCI** | Felder nach dem Muster `NEW_ITEM-<FELD>[<n>]`, per Regex `/^NEW_ITEM-([A-Z0-9_]+)\[(\d+)\]$/i` |
| 3 | flaches Formular | parallele Arrays `name[]`, `qty[]`, `unit[]`, `price[]`, `ek[]`, … |

`normalizeOciItem()` (`:463-491`) bildet die OCI-Feldnamen ab: `description`/`text`/`shorttext`,
`quantity`, `unit`/`unitofmeasure`, `price`/`priceamount`, `manufactmat`/`matnr`,
`vendormat`/`suppliermat`, `longtext`.

**Das sind OCI-Feldnamen, keine IDS-Elemente.** IDS heißt `Qty`, `QU`, `NetPrice`, `ArtNo`.

### 1.2 · Die Architektur hat OCI längst vorgesehen — BELEGT

`app/Services/Suppliers/Mappers/MapperRegistry.php:25`:

```php
'ids', 'oci' => new IdsMapper(),
```

mit dem Klassenkommentar:

> `'ids'` UND `'oci'` -> IdsMapper: der OCI-Punchout (**Sonepar**) liefert IDS-normalisierte
> Zeilen, der Kanal ist fachlich `'ids'`. Ohne diese Doppelauflösung bliebe die Map auf der
> **produktiven Strecke** leer.

**Weg D ist damit nicht der überzählige vierte Weg, sondern der Zugang zum produktiven
Lieferanten.** Ihn stillzulegen hieße, die Sonepar-Anbindung stillzulegen.

### 1.3 · Als IDS-Client ist er nicht normkonform — BELEGT

`buildForwardParams()` (`:319-340`) setzt Standardwerte, kommentiert als „Safe IDS/OCI defaults":

```php
'action'  => 'search',
'version' => '1.0',
'target'  => 'top',
'searchterm' => $query,  'query' => $query,  'q' => $query,
'rueckurl' => $returnUrl, 'return_url' => …, 'hookurl' => …,
'hook_url' => …, 'callback_url' => …, 'HOOK_URL' => $returnUrl,
```

Gegen die Norm (`docs/quellen/ids/2.5/IDS-Schnittstelle-2.5.pdf` S. 14 und S. 34) geprüft:

| Wert | Norm | Urteil |
|---|---|---|
| `action = 'search'` | gültig sind `WKS`, `WKE`, `ADL`, `AS`, `HLS`, `LI`, `SV` | **kein gültiger Aktionscode** |
| `version = '1.0'` | erlaubt sind `1.3`, `2.0`, `2.1`, `2.2`, `2.3`, `2.5` | **kein erlaubter Wert** |
| `target = 'top'` | Norm nennt `TOP` | Schreibweise abweichend |
| sechs Rücksprung-Parameter gleichzeitig | Norm kennt genau einen: `hookurl` | Streuverfahren |

**BEWERTUNG.** Das ist kein IDS-Client, sondern ein Streu-Ansatz: schick jedem Shop alles, was er
verstehen könnte. Für einen unbekannten Shop ist das pragmatisch. Gefährlich ist nur der
Kommentar „Safe IDS/OCI defaults" — er lädt jeden ein, das für eine IDS-Implementierung zu
halten und darauf aufzubauen.

### 1.4 · Er schreibt nichts — BELEGT

`grep -n "Product::\|new Product\|DistributorPrice\|::create(\|updateOrCreate\|firstOrCreate"`
über die ganze Datei liefert **null Treffer**.

Die normalisierten Zeilen tragen ausdrücklich `'product_id' => null` (`:531`) und gehen per
`returnPage()` an die Oberfläche. Der Kommentar bei `importReviewedToTemplate` (`:207-210`) sagt:
*„Template mode normally imports through postMessage/localStorage, not by writing into an
OfferDetail."*

**Folge:** Ein Punchout über Weg D füllt **weder** `supplier_article_map` **noch**
`distributor_prices`. Die Zeilen landen in einer Angebotsvorlage und sind danach weg. Die
Doppelauflösung in der `MapperRegistry` läuft für diesen Weg ins Leere, weil er den Mapper nie
aufruft.

### 1.5 · Was er besser macht als der IDS-Altpfad

`normalizeItem()` (`:534-536`) trennt sauber:

```php
'article_no'              => $raw['article_no'] ?? $raw['manufacturer_article_no'] ?? '',
'manufacturer_article_no' => $raw['manufacturer_article_no'] ?? $raw['article_no'] ?? '',
'distributor_article_no'  => $raw['distributor_article_no'] ?? $raw['supplier_article_no'] ?? '',
```

gespeist aus OCI `manufactmat`/`matnr` (Hersteller) und `vendormat`/`suppliermat` (Lieferant).
**Das ist genau die Trennung aus E5** — und damit richtiger als `IdsController`, der die
Großhändlernummer nach `products.article_no` schreibt.

---

## 2 · Drei Geldfehler in Weg D — BELEGT

### 2.1 · Eine Marge von 20 % entsteht aus dem Nichts

`normalizeItem():516`

```php
$margin = $ek > 0 ? round((($price - $ek) / $ek) * 100, 2) : 20;
```

Liefert der Shop keinen Einkaufspreis, entsteht eine **Marge von 20 %** ohne jede Quelle. Sie
wandert als `margin` und `marginPercent` in die Angebotsposition und sieht dort aus wie ein
gerechneter Wert.

**Das ist ein Geldwert ohne Herkunft.** Nach der Bauordnung (§1.1-5, „eine Wahrheit je
Sachverhalt") ist eine erfundene Kennzahl schlimmer als eine fehlende: die fehlende fällt auf.

### 2.2 · Jeder Preis gilt als „je 1 Einheit"

`normalizeItem():557-559`

```php
'price_unit_value' => 1,
'price_unit_label' => $unit,
'price_unit_text'  => '1 ' . $unit,
```

Die Preisbezugsmenge ist **hart auf 1 verdrahtet**. Das ist dieselbe Blindheit, die bei IDS
`PriceBasis` heißt — nur hier ohne Feld, das man auslesen könnte. Liefert ein Shop einen Preis je
100 oder je 1.000 Einheiten, ist die Position um diesen Faktor falsch, und niemand sieht es.

**Verbindung zur Norm:** IDS S. 24 führt `PriceBasis` als eigenes Feld, und S. 39 rechnet vor,
dass der Listenpreis erst durch `AP / PB` zum Einheitspreis wird. Ein Punchout, der die
Bezugsmenge nicht mitführt, kann keinen belastbaren Positionswert erzeugen.

### 2.3 · Fehlt der EK, wird der VK zum EK

`normalizeItem():509`

```php
$ek = $this->money($raw['ek'] ?? $raw['purchase_price'] ?? $raw['cost'] ?? $price);
```

Ohne EK-Feld ist `$ek = $price`. Dann ist die Marge rechnerisch 0 — still, ohne Hinweis. Eine
Position mit 0 % Marge ist von einer korrekt kalkulierten nicht zu unterscheiden.

---

## 3 · Die Entscheidung

> **E4a: Weg D wird weder stillgelegt noch auf den IDS-Dienst umgehängt.**
> **Er wird als eigenständiger Kanal `oci` anerkannt, an die vorhandene Kanalarchitektur
> angeschlossen und um seine drei Geldfehler bereinigt.**

### Warum nicht stilllegen
Er ist laut `MapperRegistry` die **produktive** Strecke (Sonepar). OCI ist ein eigener Standard,
den IDS nicht ersetzt; viele Shops sprechen nur OCI. Stilllegen wäre Funktionsverlust ohne Ersatz.

### Warum nicht auf den zentralen IDS-Dienst umhängen
Der zentrale Dienst wird IDS-XML gegen XSD verarbeiten. Weg D empfängt OCI-Formularfelder. Das
sind verschiedene Sprachen; „umhängen" hieße, den OCI-Teil wegzuwerfen — also doch stilllegen,
nur mit mehr Arbeit.

### Was stattdessen geschieht — drei Posten

| # | Posten | Spur | Warum |
|---|---|---|---|
| **D-1** | Die normalisierten Zeilen laufen durch `MapperRegistry->resolve('oci')` und füllen `supplier_article_map` | A | Die Doppelauflösung existiert bereits und läuft heute ins Leere. Ohne sie ist jeder Punchout flüchtig. |
| **D-2** | Die drei Geldfehler beheben: keine erfundene Marge, Preisbezugsmenge mitführen, fehlenden EK als fehlend kennzeichnen statt als gleich | A | Geldwerte ohne Herkunft |
| **D-3** | Die IDS-Fehlkonfiguration entfernen oder als „nicht IDS" kennzeichnen (`action=search`, `version=1.0`) und den Kommentar „Safe IDS/OCI defaults" richtigstellen | B | Reine Kennzeichnung, kein Datenpfad — aber sie verhindert, dass jemand darauf aufbaut |

**Reihenfolge:** D-3 zuerst (billig, verhindert Folgefehler), dann D-2 (Geld), dann D-1
(braucht den Identitätsdienst aus Paket 1 Schritt 4 und gehört deshalb hinter ihn).

### Was ausdrücklich nicht dazugehört

- Kein Umbau von `reviewReturn` und `importReviewedToTemplate`. Beide sind heute **funktionsgleich**
  (`:188-231` — beide geben nur `returnPage()` zurück) und der Name des zweiten verspricht einen
  Import, den er nicht leistet. Das ist ein eigener Posten, kein Beifang.
- Keine Änderung an `MapperRegistry`. Die Doppelauflösung `'ids','oci'` ist richtig und bleibt.
- Keine XSD-Validierung für diesen Weg. OCI hat keine.

---

## 4 · Folge für den Fahrplan

`20-implementation-roadmap.md` Schritt 5 führt Weg D unter „Doppelwahrheit stilllegen". **Das ist
zu streichen.** Schritt 5 bleibt bei Weg A (`gconline`); Weg D wandert als Posten D-1 bis D-3 in
Paket 2 bzw. hinter Schritt 4.

Damit hat Paket 1 Schritt 5 einen kleineren Umfang als geplant — und einen ehrlicheren.

## 5 · Ledger

```
PLANNER 2026-08-01 · E4a ENTSCHIEDEN · Spur A · Heimat ticket
  Weg D ist ein OCI-Punchout, kein IDS-Parser (extractItems:405-461 erkennt NEW_ITEM-*[n]).
  Er ist laut MapperRegistry:25 die produktive Strecke (Sonepar) - Stilllegen = Funktionsverlust.
  Entscheidung: bleibt, wird als Kanal 'oci' angeschlossen, drei Geldfehler werden behoben.
  Neue Posten D-1 (Mapper anschliessen), D-2 (Marge/Preisbasis/EK), D-3 (IDS-Fehlkonfiguration).
  Folge: Weg D faellt aus Paket 1 Schritt 5 heraus.
  Ballbesitz: Yama (Kenntnisnahme), dann Generator fuer D-3.
```
