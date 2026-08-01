# Fahrplan Werkzeuge — Zahlen, Namen, Termine

**Angelegt 31.07.2026, 00:20 · Planner.** Anlass: Yamas Frage *„wieviel Werkzeuge hast du
erzeugt und wie viele sind offen, hast du deren Funktion formuliert, komm mit Fakten."*

**Jede Zahl hier ist mit dem danebenstehenden Befehl gemessen, keine ist geschätzt.**

---

## 1. Wie viele es insgesamt sind — und warum 110 und 101 beide stimmen

```text
grep -c "werkzeugId: '" app/tools/werkzeugVertrag.ts    ->  110  Vertraege
grep -cE "\{ id: '"     app/tools/werkzeugPaket.ts      ->  101  Katalog-Werkzeuge
comm: im Vertrag, nicht im Katalog                      ->    9
```

Die **9** sind die Kern-Werkzeuge, die vor dem 110er-Paket schon da waren:

```text
auswahl · wand · fenster · tuer · dach · treppe · decke · duplizieren · loeschen
```

**110 = 101 Katalog + 9 Kern.** *Die beiden Zahlen widersprechen sich nicht, sie zählen
verschiedene Mengen — genau der Fehler, der heute schon zweimal Zeit gekostet hat.*

## 2. Was bedienbar ist: 7

```text
resources/planner/hausplaner/app/tools/werkzeugArten.ts:
  export type Werkzeug = 'auswahl'|'wand'|'fenster'|'tuer'|'dach'|'treppe'|'decke'

grep -E "werkzeug === '" app/HausplanerApp.tsx  ->  5 Klick-Zweige
  wand · fenster|tuer · dach · decke · treppe
```

**Sieben Werkzeuge kann man im Canvas anklicken und benutzen.** `duplizieren` und `loeschen`
sind Aktionen am ausgewählten Objekt, keine Zeichenwerkzeuge.

## 3. Ist die Funktion beschrieben? Ja — für alle 110, zweifach

**Katalog** (`werkzeugPaket.ts`), je Eintrag:

```text
grep -c 'funktion:' ->  102      grep -c 'einsatz:' ->  102
grep -c 'shortcut:' ->   16
```
dazu `label` · `kategorie` (22 Kategorien) · `icon` · `ansichten` (2D/3D) · `prioritaet` ·
`anheftbar`.

**Vertrag** (`werkzeugVertrag.ts`), je Werkzeug: `commandId` · `familie` (9 Familien) ·
`eingaben` · `ergebnisse` · `vorbedingungen` (12 Arten) · `seiteneffekte` · `umkehrbar` ·
`protokollpflichtig` · `dienstMethode`.

*Was fehlt, ist nicht die Beschreibung. Es ist der Bau.*

## 4. Was noch fehlt — mit Namen, nicht als Zahl

`app/tools/werkzeugLandkarte.ts`, 110 Marken:

| Marke | Zahl | Bedeutung |
|---|--:|---|
| `deckt` | **41** | ein Befehl in `applyCommand.ts` leistet es — 8 davon sind bedienbar, **33 haben keinen Bedienweg** |
| `ohne-modell` | **42** | Ansicht, Auswahl, Messen — ändern das Gebäude nicht, brauchen nur UI |
| `fehlt` | **21** | **braucht einen neuen Modellbefehl — das ist der echte Bauvorrat** |
| `stillgelegt` | **6** | `linie · polylinie · rechteck · kreis · bogen · polygon` — gehören nicht in den Bauplaner |

**Die 21, namentlich:**

```text
Bearbeiten   ausrichten · drehen · gruppieren · skalieren · verteilen
CAD          teilen · trimmen · verbinden · verlaengern · versatz
Messen       bemassen
Import       erkennung-bestaetigen · nordrichtung-setzen
Architektur  unterzug
Heizung      pumpe
Elektro      leuchte · schalter · steckdose · verteiler
PV           pv-modul
Zusammenarb. kommentar
```

## 5. Der gemessene Takt — Grundlage jeder Terminangabe

Aus dem git-Log dieser Nacht, Produktivcode:

```text
20:32  AUF-48-S3     |
21:16  AUF-48-S4a    |  fuenf Umbau-Scheiben in 118 Minuten
21:36  AUF-48-S4b    |  = 23 Minuten je Scheibe
22:00  AUF-48-S4c    |
22:17  AUF-48-S4d    |
22:30  AUF-48-S4e    |

23:20  Z-01 Blatt geschnitten
00:10  Z-01 gebaut       = 50 Minuten, mit Browsertest und Mutationsprobe
```

**Umbau-Scheibe ≈ 23 Minuten. Neue Funktion mit Browsertest ≈ 50 Minuten.**
*Das sind Messwerte von heute Nacht, keine Schätzung — und sie gelten, solange alle drei
Rollen laufen.*

## 6. Termine

**Voraussetzung jeder Zeile: der Generator arbeitet, der Evaluator nimmt ab.** Steht eine
Rolle, verschiebt sich alles um die Standzeit — heute Abend waren das 73 Minuten.

| Wann | Was | Was du danach kannst | Aufwand |
|---|---|---|---|
| **31.07. Nacht** | Z-01 Abnahme | *fertig gebaut* — der lange Strich ist weg | — |
| **31.07.** | **Z-02 · Z-03** Fang anschließen, Fangtyp sichtbar | Wandenden treffen ohne Pixelgenauigkeit | ~2 h |
| **31.07.** | **Z-05** Polygonwerkzeug mit Konturprüfung | Ecken klicken statt Rechteck | ~1 h |
| **31.07.** | **Z-06** Decke aus gezeichneter Kontur | **saubere Zwischendecke bei L-, T-, U-Form** | ~1 h |
| **01.08.** | **Z-08** Dach aus Kontur | Dach über echtem Grundriss | ~1 h |
| **01.08.** | **Z-09** Gehrung, T- und Kreuzanschluss | saubere Ecken statt Stumpfstoß | ~2 h |
| **01.08.** | **Z-04 · Z-10 · Z-11** Fang-Rest, Maßeingabe, Touch | Länge tippen statt ziehen | ~3 h |
| **ab 02.08.** | die **21 fehlenden** in Paketen zu je 5 | Elektro, Heizung, PV, CAD-Bearbeitung | ~5 Pakete |
| **danach** | die **33 gedeckten ohne Bedienweg** | Befehl existiert, nur der Knopf fehlt | Kurzspur |

## 7. Was du HEUTE schon bauen kannst — ohne eine weitere Zeile Code

```text
+ Geschoss      GeschossFlaeche.tsx:160   neues Geschoss ueber dem obersten
⧉ Duplizieren   GeschossFlaeche.tsx:161   Waende, Oeffnungen UND Dach ein Stockwerk hoeher,
                                          Oeffnungen an die neuen Waende umgehaengt
Decke           applyCommand.ts ADD_CEILING  eine je Geschoss, Treppendurchbrueche
                                          automatisch geschnitten
```

**Die Einschränkung, damit du nicht hineinläufst:** die Deckenkontur ist heute die
**Bounding-Box aller Wände** (`gebaeudeUmriss()`, `HausplanerApp.tsx`). Bei rechteckigem
Grundriss ist das exakt richtig. Bei L-, T- oder U-Form legt sich die Decke auch über
Bereiche ohne Wände. **Das behebt Z-06 am 31.07.**
