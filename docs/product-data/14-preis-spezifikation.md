# 14 · Preis und Zeit — Spezifikation aus der IDS-Norm

> **Rolle:** Planner · **Stand:** 01.08.2026, Nacht · **Heimat-App:** `ticket` · **Spur:** A
> **Status: VORLAGE, NICHT BEAUFTRAGT.** Das ist Paket 3. Es wird erst geschnitten, wenn Paket 1
> abgenommen ist. Der Grund, es jetzt zu schreiben: die Normquellen liegen seit heute vollständig
> vor, und die Felderliste steht damit belegt fest statt vermutet.
> **Quellen:** `docs/quellen/ids/2.5/IDS-Schnittstelle-2.5.pdf` S. 24–26 und S. 39 ·
> `docs/quellen/ids/2.5/warenkorb-empfangen-2.5.xsd` · `docs/quellen/ids/LIESMICH.md`
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 1 · Die eine Regel, aus der alles folgt

Die Norm rechnet auf Seite 39 selbst vor, am Beispiel eines 50-m-Kabelrings:

```
Kupferzuschlag KZ = AM × (GAW / BW) × (AN − BN)
                  = 50 m × (96 kg / 100 m) × ((300 €/100 kg) − (150 €/100 kg))
                  = 72 €

Nettopreis     NP = (AM × (AP / PB) − R + KZ)
                  = 50 m × ((10.000 €/1.000 m) − 1.000 €/1.000 m) + 72 €
                  = 522 €
```

**`NetPrice` ist die Positionssumme.** Sie enthält Rabatt **und** Rohstoffzuschlag bereits.
`PriceBasis` gehört zu `OfferPrice`, nicht zu `NetPrice`.

Daraus folgen fünf Sätze, die jeder Preisimport befolgen muss:

1. `NetPrice` **nicht** mit `Qty` multiplizieren — sie ist schon die Summe.
2. Den Listenpreis je Einheit erhält man als `OfferPrice / PriceBasis`.
3. Den Einkaufspreis je Einheit erhält man als `NetPrice / Qty`.
4. Rabatt und Rohstoffzuschlag **nicht** noch einmal anwenden.
5. Ohne `PriceBasis` ist der Listenpreis nicht interpretierbar. Fehlt das Feld, gilt der Preis
   als **unbekannt**, nicht als „je 1".

**BEWERTUNG.** Punkt 5 ist der teuerste. Ein Preis je 1.000 Einheiten, der als Preis je Stück
gelesen wird, ist um Faktor 1.000 falsch — und sieht dabei völlig plausibel aus.

---

## 2 · Was die Norm verlangt — belegt

### 2.1 · Preisfelder der Position (S. 24)

| Norm | XML | M/K | Format |
|---|---|---|---|
| Angebotspreis | `OrderItem/OfferPrice` | K | DEZIMAL **10,4** |
| Nettopreis | `OrderItem/NetPrice` | K | DEZIMAL **10,4** |
| Preisbasis | `OrderItem/PriceBasis` | K | DEZIMAL 10,2 |
| Menge | `OrderItem/Qty` | **M** | DEZIMAL 13,2 |
| Mengeneinheit | `OrderItem/QU` | **M** | STRING 4, Codeliste |
| Mehrwertsteuer | `OrderItem/VAT` | K | DEZIMAL 5,2, in % |
| Zuschlag | `OrderItem/Zuschlag` | K | DEZIMAL 10,4, **prozentual**, Rabatte als **negative** Werte |
| Währung | `OrderInfo/Cur` | K | STRING 3, ISO 4217 |

### 2.2 · Rohstoffanteil (S. 25–26), **mehrfach je Position**

| Norm | XML unter `OrderItem/Rohstoffanteil/` | Format |
|---|---|---|
| Rohstoff | `Rohstoff` | STRING 3, Codeliste (13 Werte, `CU` = Kupfer) |
| Gewichtsanteilswert | `Gewichtsanteilswert` | DEZIMAL 10,4 |
| Gewichtsanteilseinheit | `Gewichtsanteilseinheit` | STRING 3, Mengeneinheiten-Codeliste |
| Basiswert | `Basiswert` | DEZIMAL 10,4 |
| Basiseinheit | `Basiseinheit` | STRING 3 |
| Basis-DEL-Notierung | `Basisnotierung` | DEZIMAL 10,4 |
| Aktuelle DEL-Notierung | `NotierungAktuell` | DEZIMAL 10,4 |

Norm zu `NotierungAktuell`: *„Beinhaltet die DEL-Notierung, mit der der Nettopreis berechnet
wurde; muss nicht der aktuellen DEL-Notierung entsprechen, da ggf. für Kontingente fixiert."*

**Ohne diese beiden Notierungen ist ein Kontingentpreis nicht von einem Tagespreis zu
unterscheiden.** Für einen Elektrobetrieb ist das der Unterschied zwischen einer belastbaren und
einer wertlosen Kalkulation — im Beispiel der Norm sind 72 von 522 € reiner Kupferzuschlag,
also 14 %.

---

## 3 · Was wir haben — belegt

### 3.1 · `distributor_prices` (`2023_10_16_141346`)

| Spalte | Typ | Kommentar in der Migration |
|---|---|---|
| `article_no` | string, nullable | |
| `discount_price` | decimal(10,**2**) | „Rabatt in €" |
| `discount_percent` | decimal(5,2) | „Rabatt in %" |
| `price` | decimal(10,**2**) | „UVP" |
| `purchase_price` | decimal(10,**2**) | „EK" |
| `price_date` | date | |
| `availability` | string | |
| `status` | string, Default `Published` | |

**Kein Unique, kein Index** außer den drei Fremdschlüsseln.

### 3.2 · Preise liegen an vier Orten — belegt

| # | Ort | Spalten | Genauigkeit |
|---|---|---|---|
| 1 | `products` | `retail_price`, `purchase_price`, `vat_percent` | decimal(12,2) |
| 2 | `distributor_prices` | `price`, `purchase_price`, `discount_price`, `discount_percent` | decimal(10,2) |
| 3 | `master_set_components` | `unit_price`, `purchase_price` | decimal(10,2) |
| 4 | `master_set_cart_items` | `unit_price`, `purchase_price`, `margin`, `skonto` | decimal(12,2) |

Orte 3 und 4 sind ausdrücklich **Spiegel** von Ort 2, verbunden über `distributor_price_id`
(`docs/bereich2-p3d2-ids-openmaster-preisfluss.md` §1). Das ist eine bewusste Entscheidung und
bleibt. Ort 1 ist die vierte Wahrheit ohne Lieferantenbezug.

### 3.3 · Die Lückenliste

| Norm verlangt | Bei uns | Folge |
|---|---|---|
| DEZIMAL **10,4** | decimal(10,**2**) | dritte und vierte Stelle fallen weg — bei Preisen je 100 oder 1.000 Einheiten ist dort der ganze Wert |
| `PriceBasis` | **fehlt** | Listenpreis nicht interpretierbar |
| `QU` je Preis | **fehlt** | Preis ohne Einheit; `products.measure_unit` ist die Artikeleinheit, nicht die Preiseinheit |
| `Cur` | **fehlt** | implizit EUR |
| `VAT` je Preiszeile | nur `products.vat_percent` | Steuersatz am Artikel, nicht am Lieferantenpreis |
| `Zuschlag` prozentual | **fehlt** | |
| `Rohstoffanteil` mehrfach | **fehlt vollständig** | Kupfernotierung nicht nachvollziehbar |
| `Basisnotierung` / `NotierungAktuell` | **fehlt** | Kontingent- und Tagespreis ununterscheidbar |
| Zeitachse | nur `price_date`, eine Zeile je `(distributor, product)` | `updateOrCreate` überschreibt; **keine Historie, keine Gültigkeit, keine Staffel** |

**Die Zeitachse ist der zweitgrößte Posten nach `PriceBasis`.** Heute ersetzt jeder Import den
Vorpreis. Damit ist weder eine Preisentwicklung sichtbar noch ein Angebot von gestern
nachrechenbar — und die Norm mahnt bei `OfferNo` ausdrücklich an, *„dass das referenzierte
Angebot noch gültig ist"*.

---

## 4 · Die Umrechnungsregel Warenkorb → Katalogpreis

Das ist die Stelle, an der ein Importeur am ehesten falsch liegt, weil sie zwei verschiedene
Dinge verbindet.

**`distributor_prices` ist eine Katalogtabelle je Artikel und Lieferant.**
**Ein IDS-Warenkorb ist eine Positionsliste mit Mengen.** Die Umrechnung ist deshalb nicht
„Feld nach Feld", sondern:

```
purchase_price  (EK je Einheit)     = NetPrice / Qty
price           (Listenpreis je Ei.) = OfferPrice / PriceBasis
price_basis                          = PriceBasis        (mitführen, nicht auflösen)
price_unit                           = QU
currency                             = OrderInfo/Cur
vat_percent                          = VAT
price_date                           = WarenkorbInfo/Date
```

**Zwei Fallen dabei:**

- `Qty` kann 0 sein oder fehlen, wenn der Shop die Position nicht führt (seit IDS 2.3 ist `Qty`
  für nicht gefundene Positionen Kann). **Division durch null.** Ohne `Qty > 0` entsteht **kein**
  Katalogpreis, und das ist kein Fehler, sondern der Normalfall für einen nicht geführten Artikel.
- `PriceBasis` **mitführen, nicht wegrechnen.** Wer `price` auf „je 1" normalisiert und
  `price_basis` verwirft, verliert die Information, mit der sich der Wert gegen die
  Lieferantenrechnung prüfen lässt.

---

## 5 · Vorschlag für das Schema — additiv

**Alles `nullable`, keine Bestandsspalte wird angefasst, keine Datenmigration.**

```php
// distributor_prices — Praezision und Bezug
$t->decimal('net_price_4', 10, 4)->nullable();      // EK je Einheit, 4 Nachkomma
$t->decimal('list_price_4', 10, 4)->nullable();     // Listenpreis je Einheit, 4 Nachkomma
$t->decimal('price_basis', 10, 2)->nullable();      // IDS PriceBasis
$t->string('price_unit', 4)->nullable();            // IDS QU
$t->string('currency', 3)->nullable();              // ISO 4217
$t->decimal('vat_percent', 5, 2)->nullable();
$t->decimal('surcharge_percent', 10, 4)->nullable();// IDS Zuschlag, negativ = Rabatt

// Zeitachse — die eigentliche Aenderung
$t->date('valid_from')->nullable();
$t->date('valid_to')->nullable();
$t->decimal('min_qty', 13, 2)->nullable();          // Staffel
$t->string('price_source', 16)->nullable();         // ids | oci | omd | datanorm | manuell | demo
$t->unsignedBigInteger('import_batch_id')->nullable();

$t->index(['distributor_id','product_id','valid_from']);
```

```php
// neue Tabelle: Rohstoffanteile, mehrfach je Preiszeile
Schema::create('distributor_price_raw_materials', function (Blueprint $t) {
    $t->id();
    $t->foreignId('distributor_price_id')->constrained()->cascadeOnDelete();
    $t->string('material', 3);                 // IDS Codeliste: CU, AL, AG, ...
    $t->decimal('weight_value', 10, 4)->nullable();
    $t->string('weight_unit', 3)->nullable();
    $t->decimal('base_value', 10, 4)->nullable();
    $t->string('base_unit', 3)->nullable();
    $t->decimal('quotation_base', 10, 4)->nullable();     // Basis-DEL-Notierung
    $t->decimal('quotation_current', 10, 4)->nullable();  // Notierung, mit der gerechnet wurde
    $t->timestamps();
    $t->index(['distributor_price_id','material']);
});
```

**Warum neue Spalten statt Änderung der vorhandenen:** `decimal(10,2)` auf `decimal(10,4)` zu
ändern ist eine `change()`-Migration auf einer Bestandstabelle. Die Bauordnung verbietet das
(§1.1-1, additiv). Die alten Spalten bleiben als Anzeigewert, die neuen tragen den genauen Wert;
wer rechnet, nimmt die neuen.

**Warum keine Preishistorie als eigene Tabelle:** `valid_from`/`valid_to` plus der Wegfall des
`updateOrCreate` genügen. Eine zweite Tabelle wäre eine zweite Wahrheit für denselben Sachverhalt.

---

## 6 · Kantenliste

1. `Qty = 0` oder fehlend ⇒ **kein** Katalogpreis, kein Fehler
2. `PriceBasis` fehlt ⇒ Preis gilt als **unbekannt**, nicht als „je 1"
3. `NetPrice` ohne `OfferPrice` ⇒ EK bekannt, Liste unbekannt — zulässig
4. `Zuschlag` negativ ⇒ Rabatt, nicht Fehler
5. Rohstoffanteil **mehrfach** je Position (Kupfer und Silber am selben Artikel)
6. `Basisnotierung ≠ NotierungAktuell` ⇒ Kontingentpreis, **nicht** korrigieren
7. Derselbe Artikel bei zwei Lieferanten ⇒ zwei Preiszeilen, kein Überschreiben
8. Zweiter Import am selben Tag ⇒ **eine** Zeile, nicht zwei
9. Import mit älterem `price_date` als der Bestand ⇒ **nicht** überschreiben
10. `Cur ≠ 'EUR'` ⇒ Preis speichern, aber nicht in eine EUR-Summe rechnen
11. Rundung: erst am Ende auf 2 Stellen, nie zwischendurch
12. `decimal(10,4)` bei Preis je 1.000 ⇒ 5 signifikante Vorkommastellen bleiben

---

## 7 · Was ausdrücklich nicht dazugehört

- **Keine Änderung an `CatalogPriceGuard`.** Der liest den Snapshot in `master_set_components`,
  und das bleibt (`docs/bereich2-p3d2b-*.md` §3). Quelle → Spiegel, nicht entweder/oder.
- **Keine Bereinigung der 88 Demo-Preiszeilen.** E4b: stehen lassen, `price_source = 'demo'`.
- **Keine Zusammenführung der vier Preisorte.** Das ist ein eigener, größerer Vorgang.
- **Keine Änderung an `products.retail_price`/`purchase_price`.**
- **Kein Umbau der Angebotskalkulation.**

---

## 8 · Warum das nicht heute gebaut wird

Paket 3 steht hinter Paket 1 und 2, und zwar aus einem konkreten Grund: die Preiszeile hängt an
`product_id`. Solange sechs bis neun Schreibpfade neun verschiedene Identitätsbegriffe benutzen
(`11-identitaetsspezifikation-*.md` §1), hängt jeder importierte Preis an einem möglicherweise
falschen Artikel. Ein genauer Preis am falschen Produkt ist schlechter als ein ungenauer am
richtigen.

**Diese Vorlage wird zum Auftrag, wenn Paket 1 abgenommen ist** — nicht früher.

## 9 · Ledger

```
PLANNER 2026-08-01 · Paket 3 VORLAGE (nicht beauftragt) · Spur A · Heimat ticket
  Preisregel aus IDS 2.5 S.39 belegt: NetPrice = Positionssumme inkl. Rabatt und Rohstoffzuschlag.
  Neun Lueckenposten gegen distributor_prices, groesste: PriceBasis fehlt, decimal(10,2) statt 10,4,
  keine Zeitachse, Rohstoffanteil/DEL-Notierung fehlen vollstaendig.
  Umrechnung Warenkorb->Katalog spezifiziert (NetPrice/Qty bzw. OfferPrice/PriceBasis).
  Schemavorschlag additiv, plus eine neue Tabelle distributor_price_raw_materials.
  Wird zum Auftrag, wenn Paket 1 abgenommen ist. Ballbesitz: liegt still.
```
