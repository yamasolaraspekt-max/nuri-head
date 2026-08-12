# A-15 — Messbericht: Fachaussage oder Hinweis

**Rolle:** Generator · **Stand:** 12.08.2026, **IN ARBEIT** — Achse 1 und die Plakettenlage sind
gemessen, Achse 2/3 und die Klassifikation folgen. **Kein Code angefasst.**

## A-15-1 · Die Menge — Pfad, Muster, Summe, und was NICHT dazugehört

```text
PFAD     resources/planner/hausplaner/geometry/  +  resources/planner/hausplaner/app/dashboard/
MUSTER   \bbestanden\b        (Wortgrenze, wie im Auftrag)
DATEIEN  *.ts, *.tsx
SUMME    13
```

**Die dreizehn:** `enginePanels.ts` · `abwassergefaelle.ts` · `configuratorPackage.ts` ·
`fbhAuslegung.ts` · `heizkreisVerteiler.ts` · `kuecheArbeitsdreieck.ts` · `sparrenBerechnung.ts` ·
`treppe2D.ts` · `treppe3D.ts` · `treppenBerechnung.ts` · `treppenTypen.ts` · `wandaufbau.ts` ·
`werkzeugRegistry.ts`

**Die Zahl des Auftrags stimmt.** *Ich hatte vorab „acht" gemeldet — das waren die Engine-**Panels**,
eine andere Menge. Gut, dass ich es als zu klären gemeldet habe und nicht als Widerspruch.*

### Was NICHT in der Menge ist — und warum

| ausgeschlossen | trägt das Wort? | Grund |
|---|---|---|
| `app/EngineFlaeche.tsx` | ja | **zeigt** die Plakette, rechnet nichts — die Hülle ist keine Rechnung |
| `app/rahmen/Buehne.tsx`, `EigenschaftenPanel.tsx` | ja | Darstellung |
| `__tests__/**` (14 Dateien) | ja | Zusagen über die Rechnungen, nicht die Rechnungen |
| `renderers/`, `app/tools/` | nein | **0 Treffer** — sie kommen gar nicht in Frage |

*Die Ausschlüsse sind gemessen, nicht gesetzt: `renderers/` und `app/tools/` tragen das Wort nicht.*

## A-15-2 · Achse 1 — Normnennung, je Datei mit Zeile

**ACHT nennen eine Norm:**

| Datei | Zeile | Norm |
|---|---|---|
| `sparrenBerechnung.ts` | **2**, **7** | Eurocode; DIN EN 1991-1-3 |
| `treppenBerechnung.ts` | **5**, **58** | DIN 18065 (auch als Grenzwert im Code) |
| `treppe2D.ts` | **6** | DIN 18065 (verweisend auf `berechneTreppe`) |
| `wandaufbau.ts` | **4**, **19** | DIN EN ISO 6946 |
| `abwassergefaelle.ts` | **4** | DIN 1986-100 (vereinfacht) |
| `kuecheArbeitsdreieck.ts` | **4** | DIN 18022 / Küchenergonomie |
| `enginePanels.ts` | **124**, **125** | DIN 18065 (im `zweck` und in `grundlage`) |
| `werkzeugRegistry.ts` | **13** | DIN 18065 (im Kommentar eines Feldes) |

**FÜNF nennen keine:** `configuratorPackage.ts` · `fbhAuslegung.ts` · `heizkreisVerteiler.ts` ·
`treppe3D.ts` · `treppenTypen.ts`

**Auch die Fünf stimmen mit dem Auftrag überein.**

## A-15-3 · Die zwei Unschärfen — beide BESTÄTIGT

1. **`kuecheArbeitsdreieck` nennt DIN 18022** — belegt, Zeile 4. Und der Kopf nennt sie selbst
   ergonomisch: *„Reine Ergonomie-Prüfung nach DIN 18022 / gängiger Küchenergonomie."*
   **Eine Norm zu nennen heißt hier nicht, ein Sicherheitsurteil zu fällen.**
2. **`fbhAuslegung` und `heizkreisVerteiler` nennen keine Norm** — belegt, 0 Treffer in beiden.
   **Und beide legen trotzdem eine Anlage aus.**

**Damit trägt der Zwei-Achsen-Vorschlag des Planners** — *„nennt eine Norm" reicht in beide
Richtungen nicht.*

## A-15-5 · Plakette je Engine — heute, gemessen

**Acht Engine-Panels** (`enginePanels.ts`), blockgenau gelesen:

| engineId | Rechnung | Plakette heute |
|---|---|---|
| `engine-treppe` | `berechneTreppe` | **ja** — im Browser gesehen: „Alle Prüfungen bestanden" (y=413) |
| `engine-sparren` | `berechneSparren` | **NEIN** — seit A-14 unterdrückt (`keinGesamturteil`) |
| `engine-fbh` | `fbhAuslegung` | ja, wenn `bestanden` boolean |
| `engine-heizkoerper` | mehrzeiliger Block | **ja** — im Browser: „Eine Prüfung ist nicht bestanden" (y=380) |
| `engine-fensterprodukt` | `berechneUw` | **nein** — liefert kein `bestanden` (Kommentar in `EngineFlaeche.tsx`) |
| `engine-abwasser` | `pruefeAbwasser` | ja, wenn `bestanden` boolean |
| `engine-kueche` | `bewerteArbeitsdreieck` | ja, wenn `bestanden` boolean |
| `engine-pv` | `pvSchnellBelegung` | **nein** — liefert kein `bestanden` |

> **`enginePanels.ts` und `werkzeugRegistry.ts` sind keine Engines** und könnten gar keine Plakette
> zeigen — der Auftrag sagt es, die Messung bestätigt es.

**Ein eigener Messfehler, vor dem Melden gefunden:** mein erstes Muster ordnete
`engine-heizkoerper` die Funktion `berechneUw` zu und übersprang `engine-fensterprodukt`. Ursache:
`engine-heizkoerper` hat einen **mehrzeiligen** `berechne:`-Block, über den das Muster hinweglief.
*Blockgenau nachgemessen — acht Panels, jede Zuordnung einzeln.*

## A-15-14 · Yamas drei Regeln — wörtlich, damit die nächste Klassifikation sie anwenden kann

```text
REGEL 1  IM ZWEIFEL DIE HOEHERE KLASSE.
         "eine zu strenge kostet eine Rueckfrage, eine zu milde den Schaden."
REGEL 2  JEDE ZEILE MIT BEGRUENDUNG UND FUNDSTELLE.
         kein "vermutlich Bauschaden", sondern "Bauschaden, weil <Datei:Zeile> sagt <X>"
REGEL 3  EIN VORSCHLAG JE ENGINE, nicht eine offene Frage je Engine.
         "dann ist es fuer dich ein Blick auf eine Liste, keine Sitzung."
```

**Dazu Yamas Grund, warum er Achse 2 nicht selbst vertritt** — er gehört in denselben Bericht:

> *„Dort wird einer Fehlfunktion eine **Schadensklasse** zugeordnet. Das ist eine Fach- und
> Haftungsentscheidung … **Eine Vollmacht, Aufgaben zu erledigen, ist keine Vollmacht, Fachwissen
> zu ersetzen, das ich nicht habe.**"*

## Die elf Engines — und woher die Zahl kommt

**13 (die Menge) − `enginePanels.ts` − `werkzeugRegistry.ts` = 11.** *Die beiden sind keine
Rechnungen: die eine beschreibt Panels, die andere führt ein Feld. Sie könnten gar keine Plakette
zeigen.* **Damit ist auch die „elf" des Auftrags gedeckt.**

## A-15-9 · Achse 3 — die drei A-Fälle NACHGEPRÜFT, nicht neu gesucht

| Engine | Zustand | Fundstelle | Wortlaut |
|---|---|---|---|
| `sparrenBerechnung.ts` | **A** | **10-12** | *„WICHTIG: VORBEMESSUNG … Ersetzt KEINE prüffähige Statik"* |
| `fbhAuslegung.ts` | **A** | **6-7** | *„GRENZE: hydraulischer Abgleich und normative Auslegung bleiben Fach-Engine"* |
| `heizkreisVerteiler.ts` | **A** | **6** | *„GRENZE: hydraulischer Abgleich/Rohrnetz bleibt Fach-Engine"* |

**Alle drei bestätigt.** *Sie benennen ihre Grenze selbst — und urteilen trotzdem. Genau das ist
Zustand A.*

## A-15-10 · B gegen C — wo es NICHT unterscheidbar ist, steht das

| Engine | Zustand | Fundstelle | Begründung |
|---|---|---|---|
| `abwassergefaelle.ts` | **A** | **4** | sagt selbst *„nach DIN 1986-100 **(vereinfacht)**"* — die Einschränkung steht im Kopf |
| `kuecheArbeitsdreieck.ts` | **nicht unterscheidbar (B/C)** | 6 | Z.6 *„nur Punkte → Wege → Bewertung"* beschreibt den Umfang, **nicht die Vollständigkeit gegenüber DIN 18022** |
| `wandaufbau.ts` | **nicht unterscheidbar (B/C)** | 6 | *„nur die Übergangswiderstände unterscheiden sich"* ist eine Bauteil-Aussage, **keine Aussage über die Vollständigkeit nach DIN EN ISO 6946** |
| `configuratorPackage.ts` | **keine Rechnung** | 100-101 | `bestanden` steht dort im **Freigabe-Status**, nicht in einem Fachurteil — **keine Engine im Sinne dieses Auftrags** |

> **Zustand C ist der gefährlichste, weil er sich wie B liest.** *Deshalb steht bei zweien
> „nicht unterscheidbar" statt einer Vermutung — der Unterschied ist am Code nicht messbar und
> braucht Fachprüfung.*

## A-15-11 · Die vier Treppen-Dateien — ZULIEFERUNG, und sie fehlt noch

`treppenBerechnung.ts` · `treppe2D.ts` · `treppe3D.ts` · `treppenTypen.ts` werden **nicht hier
gemessen**, sondern aus `W-09/1-5` übernommen.

**W-09/1 steht heute auf `BEREIT` und ist nicht gebaut** — die Zulieferung existiert also noch
nicht. **Das ist keine Lücke dieses Berichts, sondern eine Reihenfolge:** *A-15 kann erst
abschließen, wenn W-09/1 seine vier Zeilen geliefert hat, oder die Auflage wird geändert.*
**Ich messe sie NICHT ersatzweise — zwei Aufträge, die dieselbe Datei messen, erzeugen zwei Zahlen
und eine Diskussion.**

## A-15-4 / -12 / -13 · Achse 2 — VORGESCHLAGEN, NICHT ENTSCHIEDEN

> **Jede Zeile unten ist ein Fachurteil, kein Messwert.** *Achse 1 ist Beleg und Fundstelle, nicht
> Entscheider.* **Ich entscheide nichts — ich lege je Engine genau einen Vorschlag vor, wie Regel 3
> es verlangt. Keine Zeile lautet „zu klären".**

| Engine | Achse 2 · **vorgeschlagen** | Klasse | Begründung **mit Fundstelle** |
|---|---|---|---|
| `sparrenBerechnung.ts` | **PERSONENSCHADEN** · *vorgeschlagen, nicht entschieden* | FACHAUSSAGE | Standsicherheit. `10-12`: *„Ersetzt KEINE prüffähige Statik … Wind, Mehrfeld, Knicken bleiben dem Tragwerksplaner"* — **A-14 hat es bereits umgesetzt** |
| `abwassergefaelle.ts` | **BAUSCHADEN** · *vorgeschlagen, nicht entschieden* | FACHAUSSAGE | Rückstau. `4`: DIN 1986-100 **(vereinfacht)** — zu geringes Gefälle führt zu stehendem Abwasser |
| `wandaufbau.ts` | **BAUSCHADEN** · *vorgeschlagen, nicht entschieden* | FACHAUSSAGE | Feuchte. `4`: DIN EN ISO 6946 — ein falscher U-Wert verschiebt den Taupunkt in die Konstruktion |
| `fbhAuslegung.ts` | **FEHLAUSLEGUNG** · *vorgeschlagen, nicht entschieden* | FACHAUSSAGE | `6-7`: *„GRENZE: hydraulischer Abgleich und normative Auslegung bleiben Fach-Engine"* — Anlage zu klein/groß |
| `heizkreisVerteiler.ts` | **FEHLAUSLEGUNG** · *vorgeschlagen, nicht entschieden* | FACHAUSSAGE | `6`: dieselbe GRENZE-Zeile — Durchfluss und Verteilergröße |
| `kuecheArbeitsdreieck.ts` | **KOMFORT** · *vorgeschlagen, nicht entschieden* | **HINWEIS** | `4`: *„Reine **Ergonomie**-Prüfung nach **DIN 18022**"* — Norm und Ergonomie stehen in **derselben** Zeile; DIN 18022 ist eine **Komfortnorm**. *Die einzige Zeile, die auf HINWEIS fällt.* |
| `configuratorPackage.ts` | **keine** | **keine Engine** | `100-101`: `bestanden` steht im **Freigabe-Status**, nicht in einem Fachurteil — sie rechnet nichts |

### Wo ich aus ZWEIFEL die höhere Klasse gesetzt habe (Regel 1)

| Engine | erwogen | gesetzt | warum die höhere |
|---|---|---|---|
| `wandaufbau.ts` | FEHLAUSLEGUNG ↔ **BAUSCHADEN** | **BAUSCHADEN** · *vorgeschlagen, nicht entschieden* | Ein zu hoher U-Wert ist zunächst nur Energie — **aber derselbe Rechenweg trägt den Taupunkt.** Ob die Engine das abdeckt, ist Zustand *nicht unterscheidbar* (A-15-10). *Solange das offen ist, kostet die strengere Klasse eine Rückfrage, die mildere den Schimmel.* |
| `abwassergefaelle.ts` | FEHLAUSLEGUNG ↔ **BAUSCHADEN** | **BAUSCHADEN** · *vorgeschlagen, nicht entschieden* | „vereinfacht" (Z.4) sagt, dass Fälle fehlen. **Rückstau ist ein Wasserschaden, keine Fehldimensionierung.** |
| `heizkreisVerteiler.ts` | FEHLAUSLEGUNG ↔ BAUSCHADEN | **FEHLAUSLEGUNG** · *vorgeschlagen, nicht entschieden* | *Hier ist die höhere Klasse NICHT gesetzt* — die Engine rechnet Durchfluss, und ein falscher Durchfluss macht die Anlage schlecht, nicht das Gebäude nass. **Das ist eine Entscheidung, kein Zweifel;** wäre sie zweifelhaft, stünde BAUSCHADEN. |

### Was fehlen würde, um eine Klasse zu senken (Regel 3, zweiter Teil)

**`wandaufbau.ts`:** die Auskunft, **ob die Engine eine Tauwasserprüfung enthält oder nur den
U-Wert**. *Enthält sie nur den U-Wert und sagt das, wäre FEHLAUSLEGUNG vertretbar.* Am Code ist es
nicht entscheidbar — siehe A-15-10.

## A-15-6 · Die fünf ohne Norm — ausdrücklich

`configuratorPackage.ts` · `fbhAuslegung.ts` · `heizkreisVerteiler.ts` · `treppe3D.ts` ·
`treppenTypen.ts` nennen **keine Norm**.

> **Ihre Klasse hängt damit an Achse 2 allein** — und Achse 2 ist ein Fachurteil.
> **Kein „keine Norm, also Hinweis":** `fbhAuslegung` und `heizkreisVerteiler` stehen oben als
> **FACHAUSSAGE**, obwohl sie keine Norm nennen. *Sie legen Anlagen aus.*
> **Diese fünf werden Yama ausdrücklich vorgelegt.**

## A-15-5 · Die Auswirkung je Zeile — wer müsste nach A-14s Muster schweigen

| Engine | Klasse | Plakette heute | müsste schweigen? |
|---|---|---|---|
| `sparrenBerechnung` | FACHAUSSAGE | **schon weg** (A-14) | erledigt |
| `abwassergefaelle` | FACHAUSSAGE | ja, wenn `bestanden` | **ja** |
| `wandaufbau` | FACHAUSSAGE | **keine — es gibt gar kein Panel dafür** | nein, **aber aus einem anderen Grund**: nicht weil es schweigt, sondern weil es nie zu Wort kommt |
| `fbhAuslegung` | FACHAUSSAGE | ja | **ja** |
| `heizkreisVerteiler` | FACHAUSSAGE | kein eigenes Panel | entfällt |
| `kuecheArbeitsdreieck` | HINWEIS | ja | **nein** — Hinweise dürfen urteilen |

**Nach diesem Vorschlag müssten ZWEI Engines zusätzlich schweigen** (`abwassergefaelle`,
`fbhAuslegung`) — *vorbehaltlich Yamas Bestätigung von Achse 2.*

## Die Sperre — A-15 kann nicht abschließen, und der Grund ist keine Nachlässigkeit

**`A-15-13` verlangt: „Jede der elf Engines bekommt genau einen Vorschlag."**
**`A-15-11` verlangt zugleich: die vier Treppen-Dateien werden NICHT hier gemessen**, sondern aus
`W-09/1-5` übernommen, mit Commit-Verweis.

```text
W-09/1   zustand: BEREIT     ballbesitz: generator     basis_sha 65f3ece4
         -> NICHT gebaut. Die Zulieferung existiert nicht.
```

**Damit sind die beiden Kriterien heute nicht gleichzeitig erfüllbar.** Diese Tabelle steht bei
**sieben von elf**, und die fehlenden vier darf ich nicht selbst messen — *„zwei Aufträge, die
dieselbe Datei messen, erzeugen zwei Zahlen und eine Diskussion."*

### Was A-15 löst — zwei Wege, beide nicht meine Entscheidung

1. **W-09/1 läuft zuerst** und liefert seine vier Zeilen. *Dann trägt A-15-11, und A-15 schließt ab.*
2. **Die Auflage wird geändert:** A-15 misst die vier selbst, W-09/1 übernimmt sie später von hier.
   *Dann kehrt sich die Richtung um, und A-15-11 muss neu formuliert werden.*

**Der erste Weg ist der billigere, weil W-09/1 ohnehin geschnitten und BEREIT ist.**
*Aber die Reihenfolge zweier Aufträge gehört dem Planner, nicht mir.*

### Was NICHT der Grund ist

**Nicht Zeitmangel, nicht Umfang.** Die sieben messbaren Zeilen stehen vollständig, mit Fundstelle,
Begründung und der höheren Klasse im Zweifel. **Es fehlt eine Eingabe, die ein anderer Auftrag
liefern soll — und der ist nicht gelaufen.**

## A-15-7 · must_preserve

**Kein Code angefasst** — Nachweis im Abschlussbericht.

## Zwei Messfehler von mir, vor dem Melden gefunden

**1 · `berechneUw` gehört NICHT zu `wandaufbau`.** Ich hatte die Auswirkungszeile über
`engine-fensterprodukt` geführt. Gemessen: `berechneUw` steht in
`geometry/fensterProdukt.ts:94`, und **kein Panel benutzt `wandaufbau` überhaupt**.
*`wandaufbau` zeigt heute keine Plakette — nicht weil es schweigt, sondern weil es nie zu Wort kommt.*

**2 · Mein Zählmuster für `bestanden` war blind für einzeilige Schnittstellen.** Ich hatte
`^\s+bestanden` benutzt; in `wandaufbau.ts:28` steht es **inline**:
`export interface UPruefung { id: string; …; bestanden: boolean; }`. **Meine erste Messung meldete
dort 0 und war falsch.**

**Neu gezählt, alle elf, ohne Zeilenanfangs-Bedingung:**

```text
abwassergefaelle 5 · configuratorPackage 1 · fbhAuslegung 6 · heizkreisVerteiler 3
kuecheArbeitsdreieck 5 · sparrenBerechnung 2 · treppe2D 2 · treppe3D 2
treppenBerechnung 4 · treppenTypen 2 · wandaufbau 2
```

**Das ändert die Klassifikation nicht** — die Einordnung hängt an Achse 2 und 3, nicht an der Zahl.
*Aber eine Zahl, die ich gemeldet hätte, wäre falsch gewesen, und das gehört hierher.*

> **Die Klasse ist dieselbe wie bei den Zeilennummern:** *ein Muster, das eine Schreibweise
> voraussetzt, misst die Schreibweise und nicht die Sache.*
