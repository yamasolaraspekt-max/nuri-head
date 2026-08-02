# IDS-Normquellen — Herkunft und Stand

> **Angelegt:** 01.08.2026 · **Korrigiert:** 01.08.2026, zweite Fassung · **Rolle:** Planner
> **Herkunft:** ITEK, <https://itek.de/wissen/verzeichnis-branchenstandards/ids-connect>
> **Diese Dateien sind Norm, nicht unser Code.** Sie werden nicht bearbeitet. Wer eine Aussage
> über ein IDS-Feld macht, belegt sie hier — mit Datei und Seite bzw. Zeile.

---

## 0 · Zwei zurückgenommene Behauptungen der ersten Fassung

Die erste Fassung dieses Papiers enthielt zwei Falschaussagen. Beide standen hier als Befund,
beide waren aus Teilinformation erschlossen statt gemessen. Sie sind hiermit **zurückgenommen**.

### RÜCKNAHME 1 — „Das PDF ist unvollständig, 8 von 39 Seiten"

**Falsch. Das PDF ist vollständig: 39 Seiten.**

Der Irrtum entstand aus drei Indizien, die alle keine waren:

| Indiz | Warum es nichts belegt |
|---|---|
| `file` meldet „8 page(s)" | `file` zählt bei diesem PDF (Version 1.3, Objektströme) falsch. **`pdfinfo` meldet `Pages: 39`.** |
| Ein Lesevorgang lieferte 8 Seiten | Ich hatte selbst `pages: "1-8"` angefordert. Die 8 war meine Eingabe, nicht das Ergebnis. |
| Fußzeile „Seite 8/39" auf Seite 8 | Normale Paginierung. Sie belegt die Gesamtseitenzahl, nicht einen Abbruch. |

Die Datei unter `public/uploads/68526a7c0a33e_…`, die neu geladene und die von ITEK sind
**dieselbe vollständige Datei**: `md5 5ad6f4cbcfc055be143ddb9c97cdedff`.

**Prüfbefehl, der die Frage entscheidet:**
```bash
pdfinfo docs/quellen/ids/2.5/IDS-Schnittstelle-2.5.pdf | grep Pages   # -> 39
# NICHT: file <datei>   -- diese Ausgabe ist bei diesem PDF unbrauchbar
```

### RÜCKNAHME 2 — „`NetPrice` ist der Preis je `PriceBasis` Einheiten, richtig wären 26,10 €"

**Falsch. `NetPrice` ist die Positionssumme.** Die Norm rechnet es auf Seite 39 selbst vor.

Ich hatte aus der XSD-Anmerkung zu `PriceBasis` („Preis bezieht sich auf ,n' Einheiten")
geschlossen, sie beziehe sich auf `NetPrice`. Sie bezieht sich auf `OfferPrice`. Der Anhang, der
das klärt, stand im selben PDF, das ich für unvollständig erklärt hatte — Fehler 1 hat Fehler 2
verursacht.

**`docs/product-data/03-data-quality-report.md` hatte recht** und bleibt unverändert gültig.

---

## 1 · Was hier liegt

```
ids/
├── LIESMICH.md              (dieses Papier)
├── 2.0/                     Altfassung, nur fuer Rueckwaertsfragen
│   ├── warenkorb-empfangen-2.0.xsd        27.118 B
│   ├── warenkorb-senden-2.0.xsd           26.787 B
│   └── beispiele/{empfangen,senden}.xml
├── 2.5/                     Beschreibung + Schemata
│   ├── IDS-Schnittstelle-2.5.pdf         429.545 B   39 Seiten, VOLLSTAENDIG
│   ├── warenkorb-empfangen-2.5.xsd        27.760 B   74 Elemente
│   ├── warenkorb-senden-2.5.xsd           27.367 B   73 Elemente
│   ├── heatinglabel-empfangen-2.5.xsd      7.996 B
│   ├── heatinglabel-senden-2.5.xsd         5.705 B
│   └── beispiele/{warenkorb-empfangen-minimal,warenkorb-senden-minimal}.xml
├── 2.5.1/                   die neuere Fassung
│   ├── warenkorb-empfangen-2.5.1.xsd      31.288 B   84 Elemente
│   └── beispiele/{warenkorb-empfangen,warenkorb-senden}.xml
└── shop-referenz/           POST-Formulare eines Shops
    └── webshop-{WKS,WKE}-*.html
```

**Die Dateiname-Warnung ist entfallen** — die PDF-Datei heißt jetzt schlicht
`IDS-Schnittstelle-2.5.pdf`.

---

## 2 · Die Preisregel — belegt, Seite 24 und 39

### Die Feldbedeutungen (Seite 24)

| Datum | XML | Muss/Kann | Format | Norm-Text |
|---|---|---|---|---|
| Angebotspreis | `OrderItem/OfferPrice` | K | DEZIMAL **10,4** | Brutto-, Listenpreis |
| **Nettopreis** | `OrderItem/NetPrice` | K | DEZIMAL **10,4** | **Einkaufspreis des Kunden. Beinhaltet Rabatte bzw. Rohstoffanteile und bezieht sich immer auf die Anfragemenge und Mengeneinheit.** |
| Preisbasis | `OrderItem/PriceBasis` | K | DEZIMAL 10,2 | Preis bezieht sich auf „n" Einheiten der Anfrage-/Angebots-Mengeneinheit |
| Menge | `OrderItem/Qty` | **M** | DEZIMAL 13,2 | Anfrage-/Angebotsmenge |
| Mengeneinheit | `OrderItem/QU` | **M** | STRING 4 | Codeliste Mengeneinheiten |
| MwSt | `OrderItem/VAT` | K | DEZIMAL 5,2 | Angabe in % |
| Zuschlag | `OrderItem/Zuschlag` | K | DEZIMAL 10,4 | **Prozentualer** Zuschlag der Position. **Rabatte werden als negative Zuschläge übertragen** |

### Die Rechnung der Norm (Seite 39, Beispiel Rohstoffangaben)

Sachverhalt: 50 m Ring Kabel · Preis 10.000 €/1.000 m · Kupfergewicht 96 kg/100 m ·
kalkuliert mit Kupferpreis 150 €/100 kg · aktueller Kupferpreis 300 €/100 kg · Rabatt 10 %.

```
Kupferzuschlag (KZ) = AM × (GAW / BW) × (AN − BN)
                    = 50 m × (96 kg / 100 m) × ((300 €/100 kg) − (150 €/100 kg))
                    = 72 €

Nettopreis (NP)     = (AM × (AP / PB) − R + KZ)
                    = 50 m × ((10.000 €/1.000 m) − 1.000 €/1.000 m) + 72 €
                    = 50 m × (9.000 €/1.000 m) + 72 €
                    = 522 €
```

**Daraus folgt verbindlich:**

1. **`NetPrice` ist die Positionssumme**, nicht der Stückpreis. Für 50 m Kabel: 522 €.
2. **`PriceBasis` gehört zu `OfferPrice`**, nicht zu `NetPrice`. `AP / PB` ergibt den
   Listenpreis je Einheit.
3. **`NetPrice` enthält den Rabatt bereits** — nicht noch einmal abziehen.
4. **`NetPrice` enthält den Rohstoffzuschlag bereits** — nicht noch einmal aufschlagen.
   Im Beispiel sind 72 € von 522 € reiner Kupferzuschlag, also **14 %**.
5. Der effektive Stückpreis ist `NetPrice / Qty` = 522/50 = **10,44 €/m**. Das ist der Wert,
   den `20-implementation-roadmap.md` als Abnahmekriterium nennt — er ist korrekt.

**Vier Nachkommastellen sind Pflicht.** `OfferPrice`, `NetPrice` und `Zuschlag` sind DEZIMAL
10,4; im XSD als `fractionDigits value="4" fixed="true"`. Unsere Preisspalten sind
`decimal(10,2)`.

### Die DEL-Notierung (Seite 26)

| Datum | XML | Norm-Text |
|---|---|---|
| Basis DEL-Notierung | `Rohstoffanteil/Basisnotierung` | DEZIMAL 10,4 |
| Aktuelle DEL-Notierung | `Rohstoffanteil/NotierungAktuell` | Beinhaltet die DEL-Notierung, **mit der der Nettopreis berechnet wurde**; muss nicht der aktuellen entsprechen, da ggf. **für Kontingente fixiert** |

Für einen Elektrobetrieb ist das kein Randthema: ohne diese beiden Werte lässt sich nicht
nachvollziehen, auf welcher Kupfernotierung ein Preis steht — und ein Kontingentpreis lässt sich
nicht von einem Tagespreis unterscheiden.

---

## 3 · Positionsebene vollständig (Seite 23–26)

| Datum | XML unter `Order/OrderItem/` | M/K | Format |
|---|---|---|---|
| Positionskennzeichen | `ItemChara` | K | `normal` · `alternate` (Alternativposition) · `provis` (Bedarfsposition) |
| Positionsnummer Handwerker | `RefItems/Customer` | K | STRING 35 — **darf im Großhandelssystem nicht verändert oder gelöscht werden** |
| Unterposition Handwerker | `RefItems/CustomerSubNo` | K | STRING 35 — dito |
| Positionsnummer Großhändler | `RefItems/Supplier` | K | STRING 35 — **darf in der Handwerkssoftware nicht verändert oder gelöscht werden** |
| Unterposition Großhändler | `RefItems/SupplierSubNo` | K | STRING 35 — dito |
| GTIN (EAN) | `EAN` | K | DEZIMAL 13,0 |
| HerstellerID | `ManufacturerID` | K | STRING 40 |
| HerstellerID-Typ | `ManufacturerIDType` | K | STRING 40 — z. B. DUNS, GLN |
| **Artikelnummer des Lieferanten** | `ArtNo` | **M** | STRING 15 — **„Großhändlernummer des Artikels"** |
| Menge | `Qty` | **M** | DEZIMAL 13,2 |
| Mengeneinheit | `QU` | **M** | STRING 4 |
| Kurztext | `Kurztext` | K | STRING 100 |
| Langtext | `Langtext` | K | STRING |
| Technische Klärung | `TechnClarification` | K | `Yes` / `No` |
| Wichtiger Hinweis | `Hinweis` | K | STRING — **muss dem Nutzer angezeigt werden** |
| Fehlercode | `Fehlercode` | K | INTEGER, Codeliste |
| Fehlertext | `Fehlertext` | K | STRING 256, vom Großhändlersystem |
| Rohstoffanteil | `Rohstoffanteil` | K | **mehrfach** |
| Diverser Artikel | `Divers` | K | BOOLEAN |

**`ArtNo` ist laut Norm die Großhändlernummer und ist Pflichtfeld.** Das bestätigt den Befund
aus `docs/product-data/10-target-domain-model.md` §4a: `IdsController` schreibt sie nach
`products.article_no` — dort gehört nach der verbindlichen Regel die **Hersteller**nummer hin.
Die Großhändlernummer gehört nach `products.sku`. Die Herstellerkennung steht in
`ManufacturerID` mit `ManufacturerIDType`, beides optional.

**Nur vier Felder der Position sind Pflicht:** `ArtNo`, `Qty`, `QU` — und auf Kopfebene
`ModeOfShipment`. Alles andere ist Kann. Wer mehr erzwingt, lehnt gültige Warenkörbe ab.

---

## 4 · Kopfebene (Seite 17–22)

**Muss:** `Warenkorb` · `WarenkorbInfo` · `WarenkorbInfo/Date` (DATE) ·
`WarenkorbInfo/Time` (TIME) · `WarenkorbInfo/Version` (STRING 10, Konstante „2.5") ·
`Order` · `Order/OrderInfo/ModeOfShipment` (**nur** „Lieferung" oder „Abholung", STRING 30).

`WarenkorbInfo/RueckgabeKZ`: nur „Warenkorbrückgabe" oder „Warenkorbrückgabe mit Bestellung".
**Hinweis der Norm: für die Sendung an das Shop-System nicht relevant und kann nicht übertragen
werden** — also nur im Rücklauf.

`OrderInfo` (alle Kann, jeweils STRING 15): `InquiryNo` Anfragenummer aus der Handwerkssoftware ·
`OfferNo` Angebotsnummer aus dem Großhandelssystem — **„es ist zu beachten, dass das referenzierte
Angebot noch gültig ist"** · `PartNo` Bestellnummer aus der Handwerkssoftware ·
`OrderConfNo` Auftragsbestätigungsnummer aus dem Großhandelssystem.

Liefertermin: `DeliveryWeek` (INTEGER, max. 53) **und** `DeliveryYear` **oder** `DeliveryDate` —
**nie beides**. Wird eine Lieferwoche übertragen, muss auch das Lieferjahr mit.

Weiter auf Kopfebene: `Cur` (STRING 3, ISO 4217) · `ZusatzText` (STRING 100, Hinweis z. B. für den
Fahrer) · `Kommission` (STRING 80) · `SupplierInfo` · `CustomerInfo` · `DeliveryPlaceInfo`.
Die Lieferadresse soll **nur** angegeben werden, wenn sie von der Kunden- bzw. Lieferantenadresse
abweicht.

---

## 5 · Aufrufparameter (Seite 14–15)

| Parameter | HTTP | M/K | Format |
|---|---|---|---|
| Aktionscode | `action` | **Muss** | Codeliste Aktionen |
| Kundennummer | `kndnr` | Kann | STRING 50 |
| Benutzername | `name_kunde` | Kann | STRING 50 |
| Passwort | `pw_kunde` | Kann | STRING 50 |
| Warenkorb | `warenkorb` | Kann | STRING — **nur mit `WKS`** |
| HOOK-URL | `hookurl` | Kann | STRING 256 |
| GH-Nummer | `ghnummer` | Kann | STRING 35 — **nur mit `ADL`** |
| Version | `version` | Kann | STRING 5 |
| Target | `target` | Kann | STRING 50 |
| Heatinglabel | `heatinglabel` | Kann | STRING — **nur mit `HLS`** |
| Suchbegriff | `searchterm` | Kann | STRING — **nur mit `AS`** |

Erlaubte Versionswerte: `1.3` · `2.0` · `2.1` · `2.2` · `2.3` · `2.5`.

`pw_kunde` ist der genormte Parametername — und der, unter dem in
`resources/views/admin/product/ids/ids.blade.php` Testzugänge im Klartext stehen.

**Skalare Datentypen (Seite 16):** DEZIMAL ist eine Fließkommazahl nach IEEE 488,
**Dezimaltrennzeichen ist der Punkt**, ein Tausendertrennzeichen ist **nicht erlaubt** — bei
INTEGER ebenso. DATE ist ISO 8601 `YYYY-MM-DD`, TIME ist `HH:MM:SS` **ohne Zeitzone**.

---

## 6 · Codelisten (Seite 34–38)

**Aktionscodes (8.1.1):** `WKE` Warenkorb empfangen · `WKS` Warenkorb senden ·
`ADL` Artikeldeeplink · `HLS` Heatinglabel senden.
*(Die Tabelle in 8.1.1 führt nur diese vier; `LI`, `SV` und `AS` sind in §5.8 beschrieben und in
der Codeliste des Anhangs nicht nachgetragen — ein Fehler der Norm, kein Fehler bei uns.)*

**Mengeneinheiten (8.1.2), vollständig:**
`CMQ` Kubikzentimeter · `CMK` Quadratzentimeter · `CMT` Zentimeter · `DZN` Dutzend ·
`GRM` Gramm · `HLT` Hektoliter · `KGM` Kilogramm · `KTM` Kilometer · `LTR` Liter ·
`MMT` Millimeter · `MTK` Quadratmeter · `MTQ` Kubikmeter · `MTR` Meter · `PCE` Stück ·
`PR` Paar · `SET` Satz · `TNE` Tonne.

**Fehlercodes (8.1.3):** `1` = Allgemeiner Fehler. Weitere Codes dürfen frei genutzt werden —
ein Parser darf also **nicht** auf eine geschlossene Liste prüfen.

**Rohstoffe (8.1.4):** `AL` Aluminium · `PB` Blei · `CR` Chrom · `AU` Gold · `CD` Kadmium ·
`CU` Kupfer · `MG` Magnesium · `NI` Nickel · `PL` Platin · `AG` Silber · `W` Wolfram ·
`ZN` Zink · `SN` Zinn.

**Status (8.1.10):** `NotUsed` Position nicht verwendet · `NotFound` Position nicht im Datenpool
vorhanden · `Used` Position in der Verbundanlage verwendet · `New` Position wurde über die
Web-Oberfläche hinzugefügt.

Heatinglabel-Codelisten (Anlagenfunktion, Anlagentyp, Lastprofil, Einbausituation, Produkttyp,
PDFtyp, Solarverwendung) stehen auf Seite 35–38. Sie sind bei uns nicht in Gebrauch.

---

## 7 · Was 2.5.1 gegenüber 2.5 ändert

Belegt aus `2.5.1/beispiele/warenkorb-empfangen.xml`: Die festen Auftragsreferenzen
(`InquiryNo`, `OfferNo`, `PartNo`, `OrderConfNo`) sind durch eine **wiederholbare** Struktur
ersetzt:

```xml
<Referenz>
  <ReferenzNumber>AB55151&lt;21&gt;</ReferenzNumber>
  <ReferenzDate>2009-11-11</ReferenzDate>
  <ReferenzType>220</ReferenzType>
</Referenz>
```

Ein gegen 2.5 gebauter Parser findet in einem 2.5.1-Warenkorb **keine** Auftragsnummer — sie ist
nicht weg, sie ist umgezogen. Die Codeliste zu `ReferenzType` (im Beispiel `220`, `231`) ist bei
uns **nicht** belegt; die 2.5-Beschreibung kennt sie nicht.

---

## 8 · Offene Beschaffungsposten

| # | Was fehlt | Wo |
|---|---|---|
| 1 | `warenkorb_senden_2_5_1.xsd` | ITEK — das 2.5.1-Sendebeispiel verweist darauf |
| 2 | Beschreibung zur Fassung 2.5.1 inkl. Codeliste `ReferenzType` | ITEK |
| 3 | Open-Masterdata-Spezifikation | nicht öffentlich; für Verbandsmitglieder kostenlos über ITEK |

---

## 9 · Die sieben Funktionen im Ablauf (§5.1–5.7, Seiten 8–13)

Nachgetragen 01.08.2026 nach vollständiger Lesung.

### 9.1 · Der gemeinsame Rahmen aller Funktionen

Jede Funktion läuft nach demselben Muster: Shop im Browserfenster aus der Handwerkssoftware
starten · Aktionen im Shop sind **Blackbox** · Rückübertragung an die HOOK-URL · Fenster schließen.

**Vier Festlegungen, die überall gelten:**

- **Der Parameter `hookurl` muss in jedem Fall mitgesendet werden** — ohne ihn ist keine
  Rückübertragung möglich. Die Norm wiederholt das bei jeder Funktion.
- **„Nach Abschluss der Verarbeitung *muss* das Browserfenster geschlossen werden."**
- **Fehler beim Aufruf werden im Browserfenster angezeigt.** Das Fehlerhandling obliegt dem Shop
  und ist nicht Teil der Norm.
- **Ermöglicht der Shop ein Weiterarbeiten (z. B. manuelle Anmeldung), müssen die Aufrufparameter
  aus der Handwerkssoftware erhalten bleiben**, damit der Prozess abgeschlossen werden kann.

**Eine Warnung, die die Norm bei jeder einzelnen Funktion wiederholt:**

> Beim Zugriff auf die Shop-Systeme ist zu beachten, dass es zu Problemen kommen kann, wenn in
> einem Shop-System **mehrere Funktionen parallel** gestartet werden.

**Folge für uns · BEWERTUNG.** Punchout-Aufrufe je Verbindung müssen serialisiert werden. Zwei
gleichzeitig offene Fenster gegen denselben Shop sind laut Norm ein bekannter Problemfall — das
gehört in die Spezifikation von Paket 4, nicht in die Fehlersuche danach.

### 9.2 · Die Rückgabe ist ein OCI-artiges Formular — BELEGT

§5.1c, wörtlich:

> Die Übernahme der Daten erfolgt als Übertragung eines **Formulars** an die HOOK-URL
> **analog der OCI Schnittstelle**.

Dieselbe Formulierung steht in §5.4f für die Artikelsuche.

**Das ist normseitig relevant für die Entscheidung E4a** (`docs/product-data/12-e4a-*.md`):
IDS und OCI sind nicht zwei konkurrierende Welten, sondern verwandte Mechanismen — IDS überträgt
sein XML in einem Formularfeld an dieselbe Art Rücksprung-Adresse, die OCI benutzt. Ein
Punchout-Client, der Formularfelder verarbeitet, ist damit kein Fremdkörper.

### 9.3 · Welches Schema wann gilt

| Funktion | Aktion | Schema der Rückgabe |
|---|---|---|
| Warenkorbübernahme Shop → Handwerk | `WKE` | `Warenkorb_empfangen.xsd` |
| Warenkorbübergabe Handwerk → Shop | `WKS` | `Warenkorb_senden.xsd` |
| **Artikelsuche** | `AS` | **`Warenkorb_empfangen_2-5.xsd`** — ein **anderes** Schema als bei `WKE` |
| Heatinglabel senden | `HLS` | `heatinglabel_senden.xsd`, Rückgabe `heatinglabel_empfangen.xsd` |

Dass die Artikelsuche ein eigenes Schema hat, ist leicht zu übersehen. Wer das
`WKE`-Schema gegen ein Suchergebnis validiert, bekommt Fehler, die keine sind.

### 9.4 · Pflichten bei Warenkorbübergabe (§5.2)

Bei der Verarbeitung im Shop müssen gegeben sein:

- **Es dürfen keine Positionen „verschluckt" werden** — entfällt eine bei der Verarbeitung, muss
  eine Fehlermeldung erscheinen.
- **Übertragene Positionsnummern müssen erhalten bleiben** — auch beim Bearbeiten im Shop.
- **Ein Bezug zu Belegen (z. B. Angeboten) soll möglich sein und bei der Verarbeitung erhalten
  bleiben.**

### 9.5 · Artikeldeeplink (§5.3) und Artikelsuche (§5.4)

**Deeplink `ADL`:** Anfrage über die **GH-Nummer** (Parameter `ghnummer`, STRING 35). Antwort ist
eine Browserseite mit Artikeldaten — keine Datenstruktur.

**Artikelsuche `AS`:** Der Suchbegriff geht im Parameter `searchterm`.

> In Parameter „searchterm" können **mehrere Begriffe, getrennt durch Leerzeichen**, angegeben
> werden.
> Die Anwendung des Suchstrings erfolgt durch die **Suchengine des empfangenden Systems**
> entsprechend der dort vorhandenen Logik. **Die konkrete Suchlogik wird durch den Aufruf nicht
> beeinflusst.**

**BEWERTUNG.** Wir können also weder Sortierung noch Trefferzahl noch Suchverhalten steuern. Jede
Oberfläche, die dem Anwender Suchoptionen anbietet, verspricht etwas, das die Schnittstelle nicht
einlöst.

Die Suche geht wahlweise gegen den Großhandelsshop **oder gegen den Open Datapool** — dieselbe
Aktion, anderes Ziel.

### 9.6 · Logininformationen (§5.6) — vollständig spezifiziert

Anfrage: nur `action=LI`. Antwort:

```xml
<Logininformationen>
    <Kundennummer_erforderlich>false</Kundennummer_erforderlich>
    <Benutzername_erforderlich>true</Benutzername_erforderlich>
    <Passwort_erforderlich>true</Passwort_erforderlich>
</Logininformationen>
```

Erlaubte Werte: `true` / `false`.

**Das ist die billigste sinnvolle Funktion, die wir bauen können** — drei Boolesche Werte, kein
Warenkorb, kein Schema, keine Preise. Sie beantwortet für jede Verbindung, welche Zugangsdaten
überhaupt nötig sind, statt sie zu erraten.

### 9.7 · Schnittstellenversion (§5.7)

Anfrage: nur `action=SV`. Antwort:

```xml
<Schnittstellenversionen>
    <Version>1.3</Version>
    <Version>2.0</Version>
    <Version>2.1</Version>
    <Version>2.2</Version>
    <Version>2.3</Version>
    <Version>2.5</Version>
</Schnittstellenversionen>
```

Damit lässt sich vor jedem Aufruf feststellen, was ein Shop tatsächlich kann — statt eine Version
anzunehmen. Die Präambel nennt genau das als vorgesehenen Weg: *„Die tatsächlich nutzbaren
Funktionen können über eine in der Schnittstelle definierte Abfrage festgestellt werden."*

**Zusammen sind `LI` und `SV` der Einstieg in jede neue Lieferantenverbindung** — sie kosten wenig
und ersetzen Raten durch Fragen.
