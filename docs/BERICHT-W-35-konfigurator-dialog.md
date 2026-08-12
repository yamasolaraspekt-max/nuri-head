# Baubericht W-35 — Konfigurator-Dialog, abgelesen

```yaml
auftrag: "W-35"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-35-konfigurator-dialog.md
art: "STUFE 6 · ABLESUNG — der Code existiert, 271 Zeilen, nichts zu widerlegen"
basis_sha: 0474f53b
befund_vor_dem_ziehen: "99aa4a03"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Zwei Funde beim Ablesen, die nicht im Auftrag standen, und beide betreffen die Ehrlichkeit der
> Fläche:** *Schritt 2 („Material") nimmt nichts auf, und Schritt 3 („Prüfung") prüft nichts.*
> **Beide gemessen, beide in `7-GRENZEN.md`, keiner behoben — das ist eine Ablesung.**

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-35-konfigurator-dialog/
  1-ZWECK.md · 2-FUNKTION.md · 3-FORMELN.md · 4-BEDIENUNG.md
  5-CODE/LIESMICH.md · 6-PRUEFUNG.md · 7-GRENZEN.md
REGISTER.md   Zeile 122:  LEER -> BESCHRIEBEN, und ZWEI Angaben berichtigt
```

**Kein Produktivcode.** *`ConfigWizard.tsx` ist gelesen, nicht geändert — auch der überholte
Dateikopf nicht.*

## W-35-1 · Die Registerzeile trug ZWEI Fehler, das Kriterium nennt einen

```text
vorher   | W-35 | Konfigurator-Dialog Fenster·Tür·Treppe | LEER | W-04, W-09 |
           ungeprüft — app/ConfigWizard.tsx (271 Z) · schreibt NICHT ins Gebäudemodell |

nachher  | W-35 | Konfigurator-Dialog Fenster·Tür·Treppe·Heizkörper | BESCHRIEBEN | W-04, W-09 |
           app/ConfigWizard.tsx (271 Z) ⓝ — VIER Arten (:23), fünf Schritte (:34),
           TYP_MAP (:43) … schreibt sehr wohl ins Gebäudemodell: ADD_NODE in :184, :205, :226
           — der Schreibpfad selbst ist W-42 · sechs Wächter, davon konfiguratorEhrlich (11 Tests) |
```

**Fehler 1, im Kriterium genannt:** *drei Arten statt vier.* **`ConfigWizard.tsx:23` geöffnet:**
`export type KonfigArt = 'fenster' | 'tuer' | 'treppe' | 'heizkoerper'`.

**Fehler 2, NICHT im Kriterium — und er stand in derselben Zeile:** *„schreibt NICHT ins
Gebäudemodell".* **Selbst gemessen, nicht von W-42 übernommen:**

```text
grep -n ADD_NODE resources/planner/hausplaner/app/ConfigWizard.tsx
  :184   store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode })
  :205   store.executeCommand({ type: 'ADD_NODE', node: treppe   as SceneNode })
  :210   // … direkt ins Modell (ADD_NODE).                       ← Kommentar, kein Aufruf
  :226   store.executeCommand({ type: 'ADD_NODE', node: knoten   as SceneNode })
```

> **Wer nur die Arten korrigiert, ist nach der alten Fassung GRÜN — und die Zeile behauptet weiter
> das Gegenteil dessen, was zwei Aufträge vorher gemessen und abgenommen wurde.** *Das ist
> Pflichtprüfung 4: ein grünes Kriterium ist keins.*

**DAS KRITERIUM IST INZWISCHEN ERWEITERT.** *Ich hatte den Fund beim Ziehen gemeldet (`99aa4a03`)
und die zweite Korrektur ausdrücklich als möglicherweise außerhalb des Scopes gekennzeichnet, mit
der Entscheidung beim Evaluator.* **Der Planner hat W-35-1 daraufhin in `671b1d9d` umgeschrieben —
beide Fehler sind jetzt genannt, mit dem Satz: „Die zweite Korrektur ist damit IM SCOPE und keine
Erweiterung des Bauenden."** *Damit ist die Frage, die ich offen gelassen habe, beantwortet, bevor
sie den Evaluator erreicht hat.*

**Und der Planner hat den schwereren Teil dazugemessen, den ich nicht gesehen hatte:**

```text
Zeile 122 (W-35)   „schreibt NICHT ins Gebäudemodell"
Zeile 129 (W-42)   „der Pfad IST gebaut", mit denselben drei Fundstellen
```

> **Zwei Zeilen IM SELBEN REGISTER widersprechen sich über DIESELBE Datei.** *Ich hatte den
> Widerspruch als „Registerzeile gegen Code" beschrieben; er ist zugleich „Register gegen Register".*
> **Bei W-42s Abnahme wurde nur die 129 gezogen — dieselbe Klasse wie bei W-40, wo der Reifegrad im
> Register stehen blieb, während das Blatt schon berichtigt war.**

**Zur ADD_NODE-Zahl, weil zwei Zählungen kursieren und beide stimmen:** *`grep -n ADD_NODE` findet
**vier** Treffer, davon **drei** `executeCommand`-Aufrufe und **einen Kommentar** (`:210`).* **Wer
`ADD_NODE` zählt, findet vier; wer Schreibvorgänge zählt, drei.** *Beide Zahlen stehen im Blatt, je
mit dem, was sie messen.*

**Und die Ursache ist keine Schlamperei, sondern eine Quelle:**

```text
ConfigWizard.tsx:2     „geführter Konfigurator-Dialog für Fenster/Tür/Treppe."       DREI
ConfigWizard.tsx:5-6   „Der Schreibpfad ins Gebäudemodell (Command) bleibt die nächste Scheibe."
```

**Der Dateikopf trägt BEIDE Fehler, und er ist der ältere.** *Die Registerzeile stammt aus meiner
Erhebung — ich habe den Kopf gelesen und ihn abgeschrieben, während der Typ vier Zeilen weiter unten
widersprochen hätte.* **H-6 in seiner unangenehmen Form: nicht ein Wort für einen Beleg gehalten,
sondern einen ganzen Satz.**

*Der Kopf bleibt unangetastet* — **W-42 hat `:6` bereits als überholt benannt und stehen gelassen,
mit dem Satz „eine Ablesung ändert ihre Quelle nicht". Für `:2` gilt dasselbe.**

## W-35-2 · Fünf Schritte, am Code gezählt

```ts
:34   const SCHRITTE = ['Bauart', 'Maße', 'Material', 'Prüfung', 'Übernehmen'] as const;
:53   const letzter = schritt === SCHRITTE.length - 1;
:90   {i < SCHRITTE.length - 1 && <span className="hp-kw-strich" />}
```

**Keine feste Zahl im Code — die Länge wird zweimal aus der Liste gelesen.**

**Und die Schrittpunkte sind SPRUNGMARKEN, keine Anzeige** (`:86`): *`onClick={() => setSchritt(i)}`
plus `onKeyDown`, **ohne jede Prüfung**, vorwärts wie rückwärts.* **Der Dialog führt, aber er hält
nicht auf** — *von Schritt 0 direkt auf 4 ist erlaubt.*

## W-35-3 (TRAGEND) · `TYP_MAP`, und die Abbildung daneben, die keinen Schutz hat

```ts
:43   const TYP_MAP: Record<KonfigArt, ConfiguratorType> =
        { fenster: 'window', tuer: 'door', treppe: 'stair', heizkoerper: 'radiator' };
:233  const paket = neuesPaket({ id, type: TYP_MAP[art], … });      ← die einzige Benutzung
```

**Das ist der Weg vom Klick zum Paket — und damit zu W-40s Freigabegraden.** *`neuesPaket` liefert
ein `ConfiguratorPackage`, dessen `status` genau die Gültigkeitsachse trägt, die ich heute
berichtigt habe.*

**Beim Öffnen der Nachbarzeilen ist ein zweiter Punkt aufgefallen, der nicht im Auftrag stand:**

```ts
:36-41   function katalogFür(art) {
           if (art === 'fenster')  return …
           if (art === 'tuer')     return …
           if (art === 'treppe')   return …
           return { ordner: 'heizkoerper', … };        ← kein if, sondern der Rückfall
         }
```

> **Zwei Abbildungen derselben vier Arten: `TYP_MAP` ist typgesichert, `katalogFür` nicht.** *Eine
> fünfte Art würde von `TYP_MAP` gestoppt und von `katalogFür` stillschweigend mit dem
> **Heizkörper**-Katalog bedient.* **Am Code ablesbar — dort steht kein `if`.**

## W-35-4 · Die Vorbelegungen, VOLLSTÄNDIG

| Art | Breite | Höhe |
|---|---|---|
| Fenster | 1010 *(Rückfall)* | **1360** |
| Tür | 1010 *(Rückfall)* | 2010 *(Rückfall)* |
| Treppe | **1000** | 2010 *(Rückfall)* |
| Heizkörper | **1000** | **600** |

**Das Auftragsblatt nennt „Treppe 1000, Heizkörper 1000/600, Fenster 1360" — richtig, aber die
Rückfälle `1010` und `2010` fehlen darin, und genau sie gelten für die TÜR**, *die dort nicht
vorkommt.* **Kein Widerspruch, eine Lücke — deshalb steht die Tabelle vollständig im Blatt.**

## W-35-5 · Die Grenze zu W-42, gespiegelt statt neu gezogen

**W-42s Blatt sagt: „W-35 ist alles bis zur Auswahl; W-42 ist, was danach damit geschieht."** *In
`2-FUNKTION.md` steht dieselbe Grenze von der anderen Seite — und keine zweite Beschreibung des
Schreibpfads.* **Die drei `ADD_NODE`-Stellen sind genannt und nicht beschrieben.**

## W-35-6 · Sechs Wächter, je mit der Zusage

**Alle sechs lesen die Datei** — *gemessen:* `grep -rln "ConfigWizard" __tests__/` **→ genau diese
sechs.**

| Wächter | Zeilen · Tests | Die Zusage |
|---|---|---|
| `konfiguratorEhrlich` | 136 · **11** | **wörtlich, `:2`: *„AUF-74 — der Konfigurator sagt, was wirklich passiert."*** |
| `configWizardWrite` | 85 · 3 | jede Art landet als der richtige Knotentyp — einer je Bauteilart |
| `paketSpeichern` | 126 · 12 | Download und Speichern sind **zwei** Wege, jeder wird einzeln gemeldet |
| `breiten` | 76 · 5 | der Dialog **stapelt** auf schmalen Geräten, statt sich zu überlagern (AUF-46) |
| `dialogFokus` | 113 · 11 | der Dialog **ist** ein Dialog: `role`, `aria-modal`, `useDialogFokus` (AUF-49) |
| `stilschicht` | 815 · 58, **davon 1** | `:460` — in `ConfigWizard` bleibt keine offene statische Stelle |

> **Die letzte Zeile trägt die Zahl zweimal, und das ist Absicht.** *„58 Tests" als Zusage für dieses
> Werkzeug wäre falsch — genau die Falle, in die ich bei W-39 gelaufen bin, als ich `stilschicht`
> nach seiner Überschrift eingeordnet habe.* **Gemessen: `:458` liest `ConfigWizard.tsx`, ein Test
> (`:460`) benutzt es.**

**Und `konfiguratorEhrlich`s Dateikopf erklärt, warum seine Prüfungen eng sind:** *„Ein breiter
`grep` auf ‚speichern' findet den Zweig, der die **Wahrheit** sagt … und meldet ihn als Fehler.
Geprüft wird deshalb jede Stelle einzeln, nicht die Datei im Ganzen."* **Das ist H-8, von einem Test
aus formuliert.**

## Zwei Funde beim Ablesen, die nicht im Auftrag standen

### 1 · Schritt 2 heißt „Material" und nimmt nichts auf

```text
:124-127   zwei <select> — WEDER value NOCH onChange
:47-50     vier Zustaende: schritt · wahl · breite · hoehe   — Material ist keiner davon

und alle VIER Ausgaenge geoeffnet:
:234  paket.parameters    bauart · bauartLabel · breiteMm · hoeheMm · autark      kein Material
:182  radiator.parameters objekt.typ · objekt.label · objekt.laenge · objekt.hoehe kein Material
:203  treppe.parameters   aus treppeZuParametern(…)                                kein Material
:224  knoten.produkt      typ · oeffnungsArt                                       kein Material
```

**Der Anwender wählt „3-fach Wärmeschutz", und dieses Wort taucht danach an keiner Stelle wieder
auf.** *Und kein Wächter deckt es ab:*
`grep -rn "Verglasung\|Profilsystem\|Rastermaß\|Stahlwange" __tests__/` **→ 0 Treffer.**

### 2 · Schritt 3 heißt „Prüfung" und prüft nichts

```text
:133  ✓  Maße plausibel
:134  ✓  DIN 18065 Schrittmaß  (Treppe)  /  Norm-Anschlag korrekt
:135  !  Rastermaß — 40 mm Versatz prüfen
```

**Keine der drei liest `breite` oder `hoehe`; die Zeichen sind fest verdrahtet.** *Bedingt ist allein
der TEXT in `:134`.*

> **Die Unterscheidung ist mir beim Gegenlesen aufgefallen, und sie zählt:** *„hängt an keiner
> Bedingung" wäre falsch gewesen — `:134` hängt an `art === 'treppe'`.* **Die Aussage lautet nicht
> „nichts ist bedingt", sondern „nichts ist gemessen".**

**Das Schwerste daran ist `:134`:** *eine Norm zu nennen ist eine fachliche Zusage.* **„DIN 18065
Schrittmaß" steht als Haken da, ohne dass etwas dagegen geprüft würde.** *Ob und wie das gerechnet
werden soll, gehört Yama — es steht in `7-GRENZEN.md` als Frage, nicht als Vorgabe.*

> **Warum beides in `7-GRENZEN` steht und nicht in einem neuen Auftrag:** *W-35 ist eine Ablesung.
> Ich stelle fest, was ist; ich baue es nicht um und schneide mir dafür auch kein Blatt.* **Aber ein
> Blatt, das fünf Schritte aufzählt, ohne zu sagen, dass zwei davon ins Leere greifen, beschreibt
> eine Bedienung, die es so nicht gibt.**

## Eine Zahl aus dem Auftragsblatt, die inzwischen driftet

**Das Blatt nennt `konfiguratorEhrlich` „den DRITTEN Ehrlichkeitswächter dieser Stufe".** *Gezählt
statt übernommen:*

```text
ls __tests__/ | grep -i ehrlich
  fussleistenEhrlich · gefuehrteEhrlich · konfiguratorEhrlich
  snapshotFlaecheEhrlich · startEhrlich                          FUENF
```

**Beim Schnitt war „der dritte" richtig; inzwischen stehen zwei weitere daneben.** *Pflichtprüfung 8
— feste Zahlen in Blättern driften.* **Im Blatt steht die Fünf mit dem Befehl, nicht die Drei.**

## W-35-1b · Zwei Schreibweisen, die der Code nicht trägt

**Beim Ziehen gemeldet, vom Planner in `671b1d9d` als eigenes Kriterium aufgenommen.** *Beide Zahlen
selbst nachgemessen, nicht aus seinem Commit übernommen:*

```text
grep -c "katalogFuer\|onUebernehmen"  ConfigWizard.tsx   ->  0
grep -c "katalogFür"                  ConfigWizard.tsx   ->  2
grep -c "onÜbernehmen"                ConfigWizard.tsx   ->  6
```

**Inhalt und Zeilennummern des Auftragsblattes treffen, nur die Schreibweise nicht.** *H-9 in der
harmlosen Form — und genau die Umlaut-Falle, vor der meine eigene Auflage warnt.* **In allen sieben
Blättern steht die Schreibweise des Codes; die falsche kommt nur dort vor, wo sie als Fund zitiert
wird.**

> **Der Planner hat beim Berichtigen selbst einen Fehler gemacht und ihn stehen lassen** — *ein
> dateiweiter Replace zog die Umlaute auch durch sein eigenes Kriterium, das danach behauptete, die
> Fassung MIT Umlaut habe 0 Treffer.* **Das ist B6: kein dateiweites Muster, blockweise mit
> Gegenprobe.** *Ich führe es hier mit, weil es dieselbe Falle ist, in die ich bei A-22 gelaufen
> bin — dort hat mein Gegenprobe-Muster die umbenannten Schlüssel an ihrer Endung erkannt, und 14
> trugen sie schon vorher.* **Ein Prüfmuster, das den Beleg mitverändert, vernichtet ihn.**

## Eine eigene Zählung, die ich korrigiert habe

**In `2-FUNKTION` stand zuerst „elf Einfuhren".** *Ich hatte die Zeilenspanne `:9-19` gezählt statt
die Zeilen; `import React` in `:8` fiel heraus.* **Gemessen: `grep -c '^import '` → 12.**

*Klein, und trotzdem gemeldet — eine Zahl, die ich abschätze statt zu zählen, ist genau die Sorte,
die später als Beleg zitiert wird.*

## W-35-8 · Sieben Blätter, Gegenprobe grün

```text
1-ZWECK 7fd24162 · 2-FUNKTION a5d67871 · 3-FORMELN 56364028 · 4-BEDIENUNG 549a4d47
5-CODE/LIESMICH aaa5c2d6 · 6-PRUEFUNG e68a3d13 · 7-GRENZEN 250379b5
tail -n +2 <blatt> | md5, gegen ALLE uebrigen Werkzeugblaetter geprueft: 0 Kollisionen

4-BEDIENUNG traegt f9626c46 NICHT mehr — der Hash stammte von vor der W-35-1b-Ergaenzung
(die drei Umlaut-Zaehlungen). Neu gemessen statt den alten stehen zu lassen: ein Hash,
der zu seiner Datei nicht passt, ist schlimmer als keiner.
```

**Abschlusszähler:** `grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN'` — **HEAD 18 → jetzt 19.**

## Was nicht gefahren wurde, und es steht hier statt zu fehlen

| | |
|---|---|
| **Mutationsproben** | **keine gesetzt.** *Die Fangtabelle in `6-PRUEFUNG` sagt bei jeder Zeile „nicht gefahren" — eine Fangprobe, die ich nicht setze, ist keine Messung, sondern eine Erwartung* |
| **Insel-Suite** | **nicht gefahren** — *kein Produktivcode berührt* |
| **Browserabnahme** | **offen** — *drei Punkte für die nächste Sichtprobe stehen in `4-BEDIENUNG.md`* |

**Einzige Ausnahme in der Fangtabelle:** *die Zeile „fünfte `KonfigArt` ohne `katalogFür`-Zweig"
trägt ohne Mutation, weil dort **kein `if`** steht — das ist am Code ablesbar.*

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert (`resources/`, `app/`) | **0** |
| hinzugefügt | **0** |
| entfernt | **0** |
| Rückweg | reine Neuanlage plus **eine** Registerzeile; `git revert` genügt |
