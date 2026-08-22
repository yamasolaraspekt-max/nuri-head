# VOTUM Z1-V1-1 — Sammelblatt „Anzeige am ausgewählten Objekt" (Spur V)

**ABGENOMMEN (BROWSER) — vier von vier gelieferten Modulen, V-1 bis V-6 je Modul belegt.**

| Feld | Wert |
|---|---|
| Blattstand | `3ab3bb88` · Kriterientext Spur V, DoR `f443f057` |
| Bau | `1c6b7601` · Ausgang `5617dc4c` |
| Mein Stand | `346cf1f9` |
| gelesen_bis | 2026-08-22T19:15:44+02:00 |
| Bühne | `browser-buehne.sh --port 8102`, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 20 |
| Bündel | das **ausgelieferte** — es trägt den Bau, deshalb habe ich am Nutzer-Stand gemessen |

## Die Kriterien je Modul

| Modul | V-1 Aufrufstelle | V-2 Wirkung | V-3 Rot | V-4 Fachlogik |
|---|---|---|---|---|
| `treppenTypen` | `TreppentypAnzeige.tsx:55` | **4 Eingabewerte → 4 Ergebnisse** | ✔ | leer |
| `dachVorlage` | `DachKennzahlen.tsx:59` | 2 Formen → 2 Ergebnisse | ✔ | leer |
| `dachProjektion` | `DachKennzahlen.tsx:74` | Meldungsfall + **Prüfmittel** | ✔ | leer |
| `sparrenTrennung` | `DachKennzahlen.tsx:89` | Meldungsfall + **Prüfmittel** | ✔ | leer |

**V-1 — über die Aufrufstelle MIT Klammer gemessen, nicht über den Namen.** Der Blatt-Halbsatz
warnt genau davor, und er trifft: der rohe Zähllauf gibt für `dachVorlage` **4** Treffer, davon
sind **3 Kommentarzeilen**. Echte Aufrufstellen je Modul: **genau 1**.
*`istSicherTrennbar` — der zweite Export von `sparrenTrennung` — hat 0 Aufrufer; V-1 verlangt den
Aufrufer für das **Modul**, und der steht mit `sparrenTeilstuecke` (`:89`).*

**V-4 — beide SHA genannt** (`git diff 5617dc4c..1c6b7601 -- <modulpfad>`), alle vier leer.
Gegenprobe, dass der Befehl anschlägt: dieselbe Spanne auf `EigenschaftenPanel.tsx` →
`1 file changed, 8 insertions(+)`.

**V-5 — Suite 1778/1778, DOM 36/36, `tsc` 0, Bündel mitcommittet.** Dass das Bündel den Bau
wirklich trägt, habe ich **nicht über Funktionsnamen** geprüft (die überleben die Minifizierung
nicht — meine erste Messung gab dafür vier Nullen), sondern über DOM-Marken und Anzeigetexte:
`data-pruefung="treppentyp"`, `data-pruefung="dachkennzahlen"`, `Steigungen`, `Auftritte`,
`Regelneigung`, `Traufkontur`, `Grundfläche` — je 1; Gegenprobe mit einem erfundenen String: 0.

**V-6 — 0 Pfade außerhalb der Insel**, das Bündel ausgenommen. Der ganze Bau: vier Pfade.

## V-2 im Einzelnen — was ich ausgelöst habe

**`treppenTypen` — die stärkste Wirkungsprobe der Lieferung.** Vier Rechentypen durchgeschaltet,
je Eingabewert ein anderes Ergebnis, wörtlich:

```
gerade     Grundfläche 4.20 × 1.00 m   16 Steigungen · 15 Auftritte
l-podest   Grundfläche 2.96 × 2.96 m   16 Steigungen · 15 Auftritte
u-podest   Grundfläche 2.00 × 2.96 m   16 Steigungen · 15 Auftritte
spindel    Grundfläche 1.60 × 1.60 m   16 Steigungen · 15 Auftritte
```

**Unabhängig nachgerechnet:** 15 Auftritte × 0,28 m = **4,20 m**, und Auftritte = Steigungen − 1.
Dass Steigungen/Auftritte über alle vier Bauformen **gleich** bleiben, ist richtig — sie hängen an
der Geschosshöhe, nicht am Rechentyp. Eine Anzeige mit fester Zahl wäre hier aufgefallen.

**`dachVorlage` — zwei Formen, zwei Ergebnisse, am selben Dach:**

```
u-shape  ->  „Für die Form „u-shape" gibt es keine Vorlage — Regelneigung wird nicht ausgewiesen."
sattel   ->  „Vorlage Satteldach · Regelneigung 35°"
```

**`dachProjektion` und `sparrenTrennung` — hier wäre es beinahe stehen geblieben.**
Am Bestand zeigen beide nur ihre Grenzmeldung:

```
„Keine Fläche ausgewiesen — Traufkontur ist nicht rechteckig — V1 unterstützt nur
 rechteckige Grundrisse (kein stilles Falschdach)."
„Kein Dachaufbau gesetzt — es gibt keinen Sparren zu trennen."
```

Das ist eine Wirkung — aber **eine, die von einer fest verdrahteten Meldung nicht zu unterscheiden
wäre.** Der Generator hat den Grund offengelegt (die einzige Dach-Fixture `u-dach` trägt
`U_UMRISS`; `decke-treppe` hat `roofs: []`), und ich habe es nachgemessen: er stimmt.

**Deshalb habe ich das Prüfmittel selbst gebaut** — im Wegwerf-Klon, nicht im Repo: in der
`u-dach`-Fixture `U_UMRISS` → `RECHTECK_UMRISS` und `roofType: 'sattel'`, danach ein Dachaufbau
(`aufbauten: [{ typ: 'window', 1200×1000×1200 }]`). Neu gebündelt, derselbe Bedienweg:

```
Fläche 60.43 m² · Neigung 35° · Azimut   0°
Fläche 60.43 m² · Neigung 35° · Azimut 180°
Teilstück unten 3.50 m · Teilstück oben 3.50 m
```

**Und die Fläche geht in der Nachrechnung auf:** Grundriss 10,0 × 8,0 m, Überstand 500 mm ringsum
→ 11,0 × 9,0 m; ein Satteldach teilt das in zwei Hälften zu 49,5 m²; bei 35° Neigung
49,5 ÷ cos 35° = **60,43 m²**. Beide Flächen gleich groß, Azimute 0° und 180° gegenüberliegend —
genau, was ein Satteldach liefern muss.
*Für die Teilstücke habe ich **keine** eigene Fachrechnung geführt; belegt ist, dass die Anzeige
vom Eingang abhängt (ohne Aufbau die Meldung, mit Aufbau zwei Teilstücke), nicht ihre Bemessung.*

**Damit ist V-2 für alle vier Module belegt** — für zwei davon allerdings nur, weil ich das
Prüfmittel gebaut habe. Das ist ein Befund, kein Mangel des Baus; siehe unten.

## V-3 — die Rot-Probe

Bündel aus dem **Vorstand `5617dc4c`** gebaut. Dort existieren `TreppentypAnzeige.tsx` und
`DachKennzahlen.tsx` **nicht**; im Bündel `treppentyp` **0**, `dachkennzahlen` **0**.
Derselbe Bedienweg an beiden Fixtures:

```
u-dach       Dach 1 gewaehlt   -> treppentyp 0 · dachkennzahlen 0
decke-treppe Treppe 1 gewaehlt -> treppentyp 0 · dachkennzahlen 0
```

**Der Ortsbeleg ist hier besonders sauber:** im Rot-Lauf steht das Eigenschaften-Panel vollständig
da („EIGENSCHAFTEN › … Dach › Dachform" bzw. „… Treppe › BAUART"), **und die Z1-W2-3-Anzeige
`grundrissform` erscheint weiterhin** (`marker: 1`). Ich habe also nicht auf einer kaputten Seite
gemessen — nur die vier neuen Module fehlen.

## Befund: ein Prüfmittel ist trivial, und es fehlt

Für `dachProjektion` und `sparrenTrennung` ist der Positivfall am Bestand nicht auslösbar — das
stimmt. **Nicht herstellbar ist er aber nicht:** `RECHTECK_UMRISS` liegt bereits in
`studioFixtures.ts` (Zeile 54, für die Decke), `uDach` zeigt in derselben Datei, wie ein Dach
angehängt wird, und `RoofNode.aufbauten` steht im Schema (`scene.types.ts:330`). Mein Prüfmittel
war **eine geänderte Zeile plus ein Aufbau-Eintrag**.

Das ist dieselbe Lage wie bei **Z1-W2-5-b** und **Z1-W2-6**, und dort ist sie bereits entschieden
(Weg A: Fixture als Prüfmittel). Ich schlage nichts vor und entscheide nichts — ich lege offen,
dass die Bausteine im selben File liegen und dass die Wirkung damit belegbar ist, wie mein Lauf
zeigt.

*Warum ich trotzdem ABGENOMMEN gebe:* V-2 verlangt „Wirkung im Browser ausgelöst; Eingabewert und
Ergebnis wörtlich genannt" — das ist für alle vier erfüllt, und für die beiden Grenzfälle habe ich
zusätzlich belegt, dass die Module **rechnen** und nicht nur melden. Ein NACHBESSERN wäre hier
Formalismus gegen einen Bau, dessen einzige Lücke ein Prüfmittel ist, das das Blatt nicht verlangt.

## Zum herausgenommenen fünften Modul

`geometry/dachTopologie.ts` ist **nicht** geliefert, mit Begründung: `analyzeTopology` braucht je
Polygonkante einen Typ (`TRAUFE | GIEBEL | PULT_WAND | WALM | TEILWALM`), und kein Ort im Bestand
leitet Kantentypen her. Das ist **kein Blocker, den jemand versteckt hat**, sondern eine
offengelegte Fachentscheidung — das Blatt verlangt ausdrücklich, keinen zu verstecken. Ich habe
den Punkt nicht als Mangel gewertet; die Klasse trägt vier statt fünf Module, und der Reifegrad
gilt je Modul.

## Meine eigenen Messausfälle in diesem Lauf

1. **V-4 auf einen Pfad gemessen, den es nicht gibt:** `dachProjektion.ts` liegt unter
   `projection/`, nicht `geometry/`. Ein `git diff` auf einen nicht existierenden Pfad ist
   **immer leer** — ich hätte „unberührt" gemeldet, ohne etwas gemessen zu haben. Erst die
   Existenzprüfung (`cat-file -e`) machte es sichtbar.
2. **V-1 mit dem falschen Funktionsnamen:** `sparrenTrennung(` gibt 0 — das Modul exportiert
   `sparrenTeilstuecke` und `istSicherTrennbar`. Erst die Exportliste, dann die Zählung.
3. **V-5 über Funktionsnamen im Bündel:** vier Nullen, weil die Minifizierung Namen ersetzt.
   `wandflaeche` überlebte nur als DOM-String — das war der Hinweis. Über Marken und Anzeigetexte
   wiederholt, mit Gegenprobe an einem erfundenen String.
4. `sed`-Bereichsraten über eine Fixture (`/const uDach/,/^}/`) gab „roofs: 0", während ich das
   Dach im Browser bediente. Über `grep -n 'roofs:'` wiederholt: Zeile 108 trägt `roofs: [dach]`.

*Alle vier sind dieselbe Klasse: ein Griff, der ins Leere geht, sieht aus wie ein Befund.*

**Ball:** Integrator (Transport dieses Votums).
