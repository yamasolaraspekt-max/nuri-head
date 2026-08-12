# W-35 · Konfigurator-Dialog — GRENZEN

> **Dieses Blatt ist Pflicht.** *Bei einer Ablesung steht hier, was der Code NICHT kann — gemessen,
> nicht vermutet. Und die Lehre aus W-40/1 gilt weiter: „die Quelle sagt es nicht" ist erst dann eine
> Grenze, wenn auch der Bestand nichts hergibt.*

## Der Befund, der beim Ablesen aufgefallen ist: Schritt 2 nimmt nichts auf

```ts
:124-127
<label>{art === 'treppe' ? 'Konstruktion' : 'Profilsystem / Rahmen'}</label>
<select className="hp-kw-feld">
  {art === 'treppe' ? <><option>Stahlwange</option><option>Holzwange</option><option>Beton</option></>
                    : <><option>Kunststoff 70 mm (5-Kammer)</option><option>Aluminium</option><option>Holz</option></>}
</select>
{art !== 'treppe' && <… <select className="hp-kw-feld"><option>2-fach Wärmeschutz</option>
                                                       <option>3-fach Wärmeschutz</option></select> …>}
```

**Beide `<select>` tragen weder `value` noch `onChange`.** *Es gibt keinen Zustand für Material und
keinen für Verglasung — `:47-50` führt vier Zustände, und Material ist keiner davon.*

**Und die Wahl erreicht auch nichts Späteres. Alle vier Ausgänge geöffnet:**

```text
:234  paket.parameters   bauart · bauartLabel · breiteMm · hoeheMm · autark        kein Material
:182  radiator.parameters  objekt.typ · objekt.label · objekt.laenge · objekt.hoehe  kein Material
:203  treppe.parameters    aus treppeZuParametern(…) — Laufbreite, Geschosshoehe …   kein Material
:224  knoten.produkt       typ · oeffnungsArt                                        kein Material
```

> **Einer von fünf Schritten sammelt nichts ein.** *Der Anwender wählt „3-fach Wärmeschutz", und
> dieses Wort taucht danach an keiner Stelle wieder auf.* **Das ist keine Vermutung: vier Ausgänge,
> vier Parameterlisten, viermal kein Material.**

**Und kein Wächter deckt es ab** — *gemessen:*

```text
grep -rn "Verglasung\|Profilsystem\|Rastermaß\|Stahlwange" __tests__/    ->  0 Treffer
```

> **Warum das hierher gehört und nicht in einen Auftrag:** *W-35 ist eine ABLESUNG. Ich stelle fest,
> was ist — ich baue es nicht um und ich schneide mir dafür auch kein Blatt.* **Aber ein Blatt, das
> die fünf Schritte aufzählt, ohne zu sagen, dass einer davon ins Leere greift, beschreibt eine
> Bedienung, die es so nicht gibt.**
>
> **Die Klasse ist bekannt, und sie ist größer als gedacht** — *`ls __tests__/ | grep -i ehrlich`
> liefert **FÜNF**:* `fussleistenEhrlich`, `gefuehrteEhrlich`, `konfiguratorEhrlich`,
> `snapshotFlaecheEhrlich`, `startEhrlich`. **Alle fünf sind aus derselben Sorte Fund entstanden:
> eine Fläche behauptet etwas, das dahinter nicht stattfindet.**
>
> *Das Auftragsblatt nennt `konfiguratorEhrlich` „den dritten dieser Stufe" — das war beim Schnitt
> richtig und ist es nicht mehr; `startEhrlich` und `snapshotFlaecheEhrlich` stehen inzwischen
> daneben.* **Gezählt statt übernommen, weil eine feste Zahl in einem Blatt driftet
> (Pflichtprüfung 8).**
>
> **Hier ist der Fund neu und der Wächter fehlt — er wäre der sechste.**

## Der zweite Fund: Schritt 3 heißt „Prüfung" und prüft nichts

```ts
:133   <span …--ok">✓</span>Maße plausibel
:134   <span …--ok">✓</span>{art === 'treppe' ? 'DIN 18065 Schrittmaß' : 'Norm-Anschlag korrekt'}
:135   <span …--warn">!</span>Rastermaß — 40 mm Versatz prüfen
```

**Die drei Zeilen lesen `breite` und `hoehe` nicht.** *Die Zeichen `✓`, `✓`, `!` sind fest verdrahtet;
bedingt ist allein der TEXT in `:134` (`art === 'treppe'`).*

```text
Folge 1   „Masse plausibel" steht auch bei 50 000 x 100 mm.
Folge 2   „DIN 18065 Schrittmass" ist ein Haken ohne Rechnung — der Name einer Norm
          als Zusicherung, ohne dass etwas gegen sie geprueft wuerde.
Folge 3   die Warnung „Rastermass — 40 mm Versatz pruefen" steht IMMER da, auch bei
          glatten Massen. Eine Warnung, die immer leuchtet, wird ignoriert.
```

> **Folge 2 ist die schwerste.** *Eine Norm zu nennen ist eine fachliche Zusage.* **Ob sie eingelöst
> werden soll und mit welcher Rechnung, ist eine Fachentscheidung und gehört Yama — nicht diesem
> Blatt und nicht dem, der als Nächstes hier baut.**

## Der `standalone`-Zweig als eigene Bedienlage

```ts
:45   standalone = true          ← Vorbelegung
:74   {standalone ? 'Autark — kein Gebäude nötig. Live-Vorschau bei jedem Schritt.'
                  : 'Im Projekt — schreibt als Command ins Gebäudemodell.'}
:146  {standalone ? (kannPaketSpeichern() ? … : …) : 'Als Fachobjekt speichern — …'}
:164  Fußzeile, dieselbe Verzweigung
```

**`standalone` steuert DREI Texte — und keinen einzigen Ausgang.**

> **Das ist die Grenze, die man kennen muss:** *welcher der vier Ausgänge greift, entscheidet
> `scene` (und bei Fenster/Tür zusätzlich die ausgewählte Wand), **nicht** `standalone`.* **Ein
> Aufruf mit `standalone={false}` bei leerer Szene beschreibt sich als „schreibt als Command ins
> Gebäudemodell" und landet trotzdem im Download-Zweig.**
>
> **Ob diese Lage vorkommt, habe ich NICHT gemessen** — *dafür müsste man alle Aufrufer von
> `ConfigWizard` prüfen.* **Ich sage, was der Code zulässt, nicht was geschieht.**

## Was der Dialog nicht kann

```text
KEIN Zurueck nach dem Uebernehmen   der Knopf fuehrt aus dem Dialog heraus; was
                                    entstanden ist, bleibt. Im Modell-Zweig faengt
                                    Undo/Redo das auf (:148), im Datei-Zweig nichts.

KEINE Pruefung beim Springen        die Schrittpunkte (:86) springen ohne Bedingung.
                                    Von Schritt 0 direkt auf 4 ist erlaubt; dann gelten
                                    kacheln[0] und die Vorbelegungen.

KEINE Obergrenze fuer die Masse     :117-118 klemmen nur nach unten (100 mm). Gedeckelt
                                    wird erst in der Wand-Einpassung (:217) — und nur dort.

KEIN Hinweis auf die Anhebung       :196 Math.max(2000, hoehe) hebt die Geschosshoehe
                                    still an. Das Feld zeigt weiter den eingetippten Wert.

KEIN Schutz fuer eine fuenfte Art   katalogFür (:40) faellt auf den Heizkoerper-Katalog
                                    zurueck. TYP_MAP (:43) wuerde stoppen, katalogFür nicht.
```

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Soll Schritt 2 „Material" die Wahl aufnehmen — und wohin? | **Yama** *(fachlich: gehört Material ins Paket, in den Knoten, in beides?)* |
| Soll „DIN 18065 Schrittmaß" wirklich gerechnet werden? | **Yama** — *eine Norm ist eine Zusage* |
| Soll die Warnung `Rastermaß` an eine Bedingung? | **Yama** |
| Braucht `katalogFür` denselben Typschutz wie `TYP_MAP`? | **Planner** — *ein Bau, kein Blatt* |
| Kommt `standalone={false}` bei leerer Szene vor? | **ungemessen** — *wer es braucht, misst die Aufrufer* |

> **Fünf Punkte, alle benannt statt still angenommen — und keiner davon hier beantwortet.** *Drei
> gehören Yama, weil sie Fach- und Normfragen sind; einer dem Planner; einer ist schlicht nicht
> gemessen und heißt deshalb so.*

## Was später kommen könnte

```text
- ein Waechter fuer Schritt 2 und 3   -> die vierte Ehrlichkeitspruefung dieser Stufe
- der Typschutz fuer katalogFür       -> ein kleiner Bau, ein Record statt if/if/if/return
- die Aufrufer von ConfigWizard       -> wer ruft mit standalone={false}?
```
