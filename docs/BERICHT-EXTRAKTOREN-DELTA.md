# Bericht — die zwei Extraktoren, und ein gebrochener Service, dessen fehlende Hälfte im Archiv liegt

```yaml
art: "MESSBERICHT. Kein Bau, keine Kopie — Yamas zwei Auflagen ausdruecklich eingehalten."
gemessen_am: "12.08."
basis_sha: 139a095c
auftrag: "Yamas Punkt 7: 'die zwei Extraktoren anschliessen — NICHT kopieren, ohne vorher
          zu messen. Erst messen, dann das Delta.' Plus 12.08.: 'Ergebnis ist ein Bericht,
          kein Bau, und Prototyp-Zahlen werden als Ist gekennzeichnet, nicht als Soll.'"
auflage_1_erfuellt: "kein Bau. Keine Datei in app/ oder resources/ angefasst."
auflage_2_erfuellt: "die zwei Extraktoren tragen KEINE Fachkonstanten — sie leiten ab.
                     Es gibt also keine Prototyp-Zahl zu kennzeichnen. Die eine Zahl, die
                     vorkommt (min(89.0) als Winkelgrenze), ist eine SCHUTZSCHRANKE, keine
                     Fachangabe — und als solche unten benannt."
```

## Das Delta — und hier gilt Yamas Neunzehntel-Satz NICHT

**Gemessen: alle acht Dateien in `docs/_playground-archiv/backend-laravel/app/Services/Energie/`
gegen `app/Services/Energie/`.**

```text
DATEI                             ARCHIV   BESTAND   LAGE
EpsBoxService.php                  101 Z       —     NUR IM ARCHIV
KabelService.php                   153 Z       —     NUR IM ARCHIV
PerformanceService.php             140 Z       —     NUR IM ARCHIV
PvBelegungExtractor.php             95 Z       —     NUR IM ARCHIV
RoofTemplateFeatureExtractor.php   120 Z       —     NUR IM ARCHIV
SchutzkomponentenService.php       108 Z       —     NUR IM ARCHIV
StringBuilderService.php           161 Z       —     NUR IM ARCHIV
InverterSizingService.php          553 Z    658 Z    DIVERGENT
                                  ------
nur im Archiv                       878 Z  (sieben Dateien)

NUR IM BESTAND:  KostenService 48 Z · PvProjektService 133 Z · PvgisErtragService 137 Z
```

> **Yamas Satz „der Bestand ist an neun von zehn Stellen weiter als das Archiv" gilt hier nicht.**
> *Bei den Energie-Services ist das **Archiv reicher**: sieben Dateien mit 878 Zeilen fehlen im
> Bestand.* **Das ist der Grund, warum diese Messung gelohnt hat — und der Grund, warum sie NICHT
> mit einer Kopie endet:** *„fehlt im Bestand" heißt **nicht** „wird gebraucht". Sieben Services für
> eine Architektur, die es hier möglicherweise nicht gibt, sind kein Gewinn, sondern sieben
> Anschlussfragen.*

**Bei der einen divergenten Datei ist der Bestand weiter:** `InverterSizingService` — **658 gegen
553 Zeilen.** *Dort gilt Yamas Satz. **Nicht kopieren.***

## Die zwei Extraktoren — sie tragen die Prinzipien dieses Hauses, vor deren Regelfassung

### `RoofTemplateFeatureExtractor.php` — 120 Zeilen

**Dateikopf, wörtlich:**

> *„Leitet die promoteten Feature-Spalten einer Dach-Vorlage aus dem rohen Planer-State
> (`config_json`) ab — **die EINZIGE Wahrheit. Kein Frontend-Input fließt in die Merkmale; so kann es
> keine Drift zwischen Rohzustand und den Filter-/Matching-Spalten geben.**"*

```text
extract(array $config): array   ->  ELF Feature-Spalten
  category · roof_shape · pitch_deg · length_m · width_m · aspect_ratio ·
  roof_area_m2 · body_count · obstacle_types · covering_kind
Private Helfer: roofArea() · str() · num()   — defensiv gegen unvollstaendigen config_json
```

### `PvBelegungExtractor.php` — 95 Zeilen

**Dateikopf, wörtlich, und der zweite Satz ist A-10:**

> *„SSOT-Prinzip: kein vorberechneter Frontend-kWp-Wert wird übernommen, sondern aus der
> tatsächlichen Modul-Platzierung (`config.modules`) × Modulleistung (`config.module.watts`)
> abgeleitet.*
> ***Ist `module.watts` unbekannt, bleibt kWp null (keine erfundene Leistung).***"

> **„Keine erfundene Leistung" ist A-10, geschrieben bevor A-10 ein Auftrag war.** *Zusammen mit
> `dachformVorlagen` (`status: 'geplant'`), `gaubeGeometrie` (`pruefeAufbau`), `segmentierung`
> (CSG-frei) und `dachVerschneidung` (Regressionsschloss) ist das der **sechste** unabhängige
> Fundort desselben Gedankens.* **Die Baurichtlinie aus `SCHICHTEN.md` hat damit einen Beleg
> außerhalb der Insel.**

## Der Fund, der über den Auftrag hinausgeht: `roofArea()` IST F-023

```php
// RoofTemplateFeatureExtractor.php:82-97
$footprint = $length * $width;
if ($category === 'flat' || $pitch === null || $pitch <= 0) return round($footprint, 2);
$cos = cos(deg2rad(min(89.0, $pitch)));        // <- Winkelgrenze
if ($cos <= 0.0001) return round($footprint, 2);  // <- zweite Sicherung
return round($footprint / $cos, 2);
```

```text
FORMELSAMMLUNG F-023   A_Dach = A_Grundriss / cos(alpha)
   Grenzfall dort:     "alpha -> 90 Grad -> cos -> 0 -> Flaeche -> unendlich.
                        Ueber 85 Grad absagen"
W-08s Blatt:           "F-023 NICHT implementiert"  (gemessen am BESTAND, richtig)
DIESER FUND:           F-023 IST implementiert — im ARCHIV, MIT Grenzfall, und der
                       Code sichert ZWEIFACH: min(89.0) begrenzt den Winkel, und
                       cos <= 0.0001 faengt den Rest.
```

> **W-08s Befund bleibt richtig und wird präziser:** *dort steht „F-023 nicht implementiert **und
> nicht nötig**: F-011 auf geneigte Koordinaten liefert die wahre Fläche direkt".* **Beides gilt, und
> der Unterschied ist die EINGABE:**
>
> ```text
> F-011 (Bestand)   braucht die geneigten u/v-Koordinaten des echten Polygons  -> genauer
> F-023 (Archiv)    braucht nur category/length/width/pitch                    -> Rechteck-Naeherung
> ```
>
> **Der Extraktor braucht F-023, weil er nur Rechteckmaße hat** — er liest einen Prototyp-State.
> *Damit sind es nicht „zwei Wege zum selben Ziel", sondern **zwei Wege für zwei Eingabelagen**. Das
> ist eine Verfeinerung von W-08s Satz, kein Widerspruch.*
>
> **Und die Schutzschranke `min(89.0)` ist eine ZAHL — sie ist als solche gekennzeichnet:** *keine
> Fachangabe, sondern ein Divisionsschutz. **Nach H-7 ist sie weder Ist noch Soll, sondern eine
> Grenze** — dieselbe Art Zahl wie `battenDist: 0.05` in `dachWerte.ts`.*

## DER DRINGENDSTE FUND — und er stand nicht im Auftrag

**`app/Services/Energie/PvProjektService.php` ist gebrochen:**

```text
:8   use App\Services\StringBuilderService;
:19          private StringBuilderService $builder,     <- KONSTRUKTOR-Abhaengigkeit
GEMESSEN:  ls app/Services/StringBuilderService.php  ->  existiert NICHT
           die Klasse liegt NUR im Archiv, und dort mit anderem Namespace:
           docs/_playground-archiv/.../Services/Energie/StringBuilderService.php (161 Z)
```

**Es ist keine ungenutzte `use`-Zeile — es ist ein Konstruktorparameter.** *Wer `PvProjektService`
instanziiert, bekommt einen Fatal Error.*

**Und der Bruch ist BEKANNT und BENANNT** — gefunden in einer fremden Datei:

```text
app/Services/Offer/KonfigurationsprojektService.php:21
   " *    KEIN Aufruf von PvProjektService (in ticket gebrochen)."
```

```text
AUFRUFER von PvProjektService   0 (ausser dieser Nennung, die ihn ausdruecklich meidet)
ZUSAGEN darauf                  0
```

> **Damit ist der Bruch tot, nicht akut** — niemand ruft ihn, keine Zusage hängt daran. **Aber er ist
> die schärfste Anschlussfrage des ganzen Archivs:** *ein Service im Bestand verlangt eine Klasse,
> die nur im Archiv existiert.*
>
> **Und genau hier greift Yamas Regel, wörtlich:** *„Nichts kopieren, ohne vorher zu messen."*
> **Kopieren wäre falsch, und zwar aus drei gemessenen Gründen:**
>
> ```text
> 1  der NAMESPACE passt nicht: Bestand erwartet App\Services\StringBuilderService,
>    das Archiv liefert App\Services\Energie\StringBuilderService
> 2  ob die Archiv-Fassung (161 Z) zur Bestands-Schnittstelle passt, ist UNGEMESSEN —
>    PvProjektService ruft Methoden auf, die niemand geprueft hat
> 3  PvProjektService ist laut Memory-Lage ausdruecklich NICHT verdrahtet (AP-3a-Grenze,
>    "blinden Service verdrahten ist Fehl-Bau"). Ihn lauffaehig zu machen, ohne dass ein
>    Auftrag ihn braucht, waere derselbe Fehler in neuer Gestalt.
> ```

## Was ich NICHT gemessen habe

```text
- Die INHALTE der sechs anderen Archiv-Services (EpsBox, Kabel, Performance,
  Schutzkomponenten, StringBuilder, plus die divergente InverterSizing).
  Grund: der Auftrag nennt ZWEI Extraktoren. Sechs weitere zu messen waere eine
  Scope-Erweiterung ohne Auftrag — sie stehen unten als Zieladresse (H-1).
- WORIN InverterSizingService divergiert. Nur die Zeilenzahl ist gemessen
  (553 gegen 658). Ein Diff ueber 105 Zeilen Unterschied ist eine eigene Messung.
- Ob die elf Feature-Spalten des Extraktors zu einer Tabelle im Bestand passen.
  Das braucht das Schema, und das ist eine DB-Frage — Yamas Bereich.
```

## Ergebnis

```text
1  Die zwei Extraktoren sind GEMESSEN, nicht kopiert. Beide sind fachlich stark:
   SSOT-Prinzip, Ableitung aus dem Rohzustand, und "keine erfundene Leistung" —
   das ist A-10, vor A-10.
2  Sie existieren im Bestand NICHT. Sieben Archiv-Services fehlen dort (878 Z).
   Bei der einen gemeinsamen Datei ist der BESTAND weiter (658 gegen 553).
3  roofArea() ist F-023 mit doppeltem Grenzfall — im Archiv gebaut, im Bestand nicht.
   W-08s Befund bleibt richtig; der Unterschied liegt in der EINGABE, nicht im Ziel.
4  PvProjektService ist GEBROCHEN: Konstruktor verlangt eine Klasse, die nur im
   Archiv liegt. Bekannt, benannt, tot — und ausdruecklich NICHT durch Kopieren
   zu heilen (Namespace, ungemessene Schnittstelle, AP-3a-Grenze).
5  KEIN Bau, KEINE Kopie, keine Datei in app/ oder resources/ angefasst.
```

```yaml
auftrag_erfuellt: "die zwei Extraktoren gemessen, Delta benannt, nicht kopiert"
delta: "7 Services nur im Archiv (878 Z) · 1 divergent (Bestand +105 Z) · 3 nur im Bestand"
staerkster_fund: "PvProjektService ist gebrochen — Konstruktor verlangt eine Klasse, die
                  nur im Archiv existiert. Bekannt (KonfigurationsprojektService:21), tot
                  (0 Aufrufer, 0 Zusagen), NICHT durch Kopieren zu heilen."
formel_fund: "roofArea() = F-023 mit doppeltem Grenzfall (min(89) + Epsilon). Im Bestand
              nicht gebaut. W-08s 'nicht noetig' wird praeziser: zwei Eingabelagen,
              nicht zwei Wege zum selben Ziel."
sechster_fundort: "'keine erfundene Leistung' im PvBelegungExtractor — A-10-Gedanke,
                   erstmals AUSSERHALB der Insel belegt"
zieladresse_offen: "sechs Archiv-Services ungemessen (EpsBox, Kabel, Performance,
                    Schutzkomponenten, StringBuilder) + WORIN InverterSizing divergiert.
                    Beides eigene Messungen, kein Anhang an diesen Bericht."
offen_an_yama: "brauchen wir die sieben Archiv-Services ueberhaupt? 'Fehlt im Bestand'
                heisst nicht 'wird gebraucht'. Ohne diese Antwort ist jede Anbindung
                ein Bau ohne Erstnutzer."
```
