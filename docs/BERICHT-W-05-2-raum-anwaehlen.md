# Baubericht W-05/2 — Raum anwählen, und die Auswahl überlebt keinen Wandzug

```yaml
auftrag: "W-05/2"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-05-2-raum-anwaehlen.md
art: "BAU — die erkannten Raeume anwaehlbar machen. Kein Name, kein Schema, keine Migration."
basis_sha: c09dcb93
bau_sha: 83d6e108
gebaut_am: "13.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Die Browserabnahme hat einen Fehlbefund gegen den eigenen Bau verhindert** — *mein erster
> Messgriff zählte die sieben **Wandbänder** als Räume und ließ zwei Prüfungen fallen, obwohl der
> Bau richtig war.* **Die Farben haben es aufgeklärt.**

## Was gebaut wurde

```text
app/raumAuswahl.ts                   NEU — zwei reine Funktionen, der Kern
__tests__/raumAuswahl.test.ts        NEU — 10 Zusagen
app/rahmen/Buehne.tsx                listening frei, Klick, Hervorhebung
app/HausplanerApp.tsx                der fluechtige Zustand + useCallback-Import
public/hausplaner/hausplaner.js      das Buendel, im SELBEN Commit
scripts/w052-browserabnahme.mjs      NEU — der Ablauf-Beleg
```

## W-05-2-1 (TRAGEND) · Warum eine Signatur und kein `useEffect`

**Erkannte Räume haben keine Kennung** (`roomDetection.ts:35-40`) — *ihre Identität ist der Index in
der Liste* (`Buehne.tsx:147`, `key={\`raum${i}\`}`). **Eine Auswahl, die einen Wandzug überlebt,
zeigt danach auf einen anderen Raum**, und der Nutzer kann es nicht merken.

```ts
app/raumAuswahl.ts
raumSignatur(raeume)      -> `${anzahl}|${gerundeteFlaeche}:${eckenzahl},…`
gueltigeAuswahl(a, r)     -> null, sobald die Signatur abweicht
```

> **Ein `useEffect` hätte NACH dem Rendern aufgeräumt** — *für einen Bilddurchlauf stünde die
> Hervorhebung auf dem falschen Raum.* **Die Signatur wird bei jedem Rendern mitgeprüft: eine
> veraltete Auswahl gilt schon im selben Durchlauf als keine.** *Und sie schreibt nichts — A-24 hat
> gezeigt, was ein Effekt anrichtet, der ins Modell greift.*

**Die Rundung der Fläche ist nicht Kosmetik:** *ohne sie könnte ein Gleitkomma-Rest zweier
Ableitungen desselben Grundrisses die Auswahl bei jedem Rendern löschen* — **und die Auflage wäre in
ihr Gegenteil verkehrt.** *Dafür steht eine eigene Zusage im Wächter.*

## Der Wächter — 10 Zusagen, und eine davon ist die Gegenrichtung

```text
✔ eine Auswahl ueberlebt die AENDERUNG der Raumliste NICHT
✔ auch eine Aenderung OHNE Anzahlwechsel setzt zurueck      <- der gefaehrlichste Fall
✔ auch eine andere ECKENZAHL bei gleicher Flaeche setzt zurueck
✔ GEGENPROBE: dieselbe Liste ein zweites Mal abgeleitet BEHAELT die Auswahl
✔ ein Gleitkomma-Rest setzt NICHT zurueck
✔ keine Auswahl / Index ausserhalb / leere Liste -> null   (drei Zusagen)
✔ die Signatur unterscheidet Anzahl, Flaeche und Eckenzahl
✔ die REIHENFOLGE zaehlt — vertauschte Raeume sind nicht dieselbe Liste
```

> **Die vierte ist die wichtigste nach der ersten:** *ohne sie wäre eine Auswahl, die sich bei jedem
> Rendern selbst löscht, formal „sicher" und praktisch unbrauchbar.* **`raeumeAus` läuft in einem
> `useMemo` und liefert bei gleichen Wänden ein neues Feld mit gleichem Inhalt.**

## W-05-2-2 · Das Muster kommt aus dem Bestand — mit einer begründeten Abweichung

| Bestand | Übernommen |
|---|---|
| `Buehne.tsx:165` — `if (werkzeug === 'auswahl') { e.cancelBubble = true; … }` | ja, wörtlich |
| `:190` — `fill={ausgewaehlt ? FARBEN.auswahl : …}` | ja |
| `listening` an das Werkzeug binden | ja — vorher `listening={false}` |

**Die eine Abweichung, zeilenweise begründet:** *die Wände hängen an `selectedNodeIds` im Store.*
**Räume können das nicht — sie haben keine Kennung.** *Deshalb ein eigener, flüchtiger Zustand in
der Hauptfunktion; die Bühne bleibt zustandslos.* **„K-03: kein Zustand hier" steht wörtlich in
ihren eigenen Props.**

## W-05-2-3 (SCHUTZGRENZE) · Kein Schema, kein Name

```text
git status --porcelain resources/    ->  4 Eintraege
davon Schema/Zod/Migration           ->  0
```

**Der NAME ist nicht gebaut** — *er bräuchte eine Identität, die es nicht gibt, und damit eine
Entscheidung Yamas.* **Wer ihn jetzt baut, entscheidet sie still.**

## W-05-2-4 · Die Flächenanzeige ist unberührt

```text
git diff Buehne.tsx | grep -E "flaecheMm2|toFixed|m²"
  -  {/* Räume: Füllung + Fläche (m², aus mm² gerundet auf 2 Stellen) */}
  +  {/* Räume: Füllung + Fläche (m², aus mm² gerundet auf 2 Stellen)
```

**Nur der Kommentar wurde erweitert.** *Die Zeile, die die Zahl zeichnet, ist nicht im Diff.*

## W-05-2-5 · Die Quellen-Wächter der Bühne

**Am Bau-Stand erhoben** *(`grep -rln "Buehne.tsx" __tests__/`)* — **die NUR-QUELLE-Klasse findet
kein Import-Muster:**

```text
kontur.test.ts        11 Zusagen   gruen
buehne.test.ts        10 Zusagen   gruen
werkzeugEnde.test.ts  15 Zusagen   gruen
                      36 zusammen
```

**Insel-Suite: 1728 / 0** *(vorher 1718; meine zehn kommen dazu).* **`tsc:hausplaner` ohne Ausgabe.**

## W-05-2-6 · Die Fangprobe ist GEFAHREN

```text
md5-Anker app/raumAuswahl.ts   93d67763871b7663d499605e9843709d
Mutation                       die Signaturpruefung aus gueltigeAuswahl entfernt
Ergebnis                       DREI Zusagen rot, und zwar genau die tragenden:
                                 „ueberlebt die AENDERUNG NICHT"
                                 „auch OHNE Anzahlwechsel"
                                 „auch andere ECKENZAHL"
                               Die GEGENPROBE blieb gruen — sie misst etwas anderes.
Rueckschrift                   md5 gegen den Anker: identisch, 10 von 10 gruen
```

## W-05-2-7 · Browserabnahme, als Ablauf

**Bühne gegen `ticket_testing`**, am Kindprozess geprüft (`APP_ENV=testing`).

**Die Saat musste neu gelegt werden** — *die Testdatenbank war leer.* **Genau das ist der Grund,
den A-24-3 für die geänderte Nachweisform nannte: ein in der Datenbank abgelegter Beleg ist auf
dieser Insel strukturell keiner.** *Gesät sind ZWEI Räume aus sieben Wänden mit einer Mittelwand —
zwei sind nötig, weil „weg" sonst von „auf dem anderen" nicht zu unterscheiden wäre.*

```text
✔ Anmeldung
✔ Expertenmodus geoeffnet
✔ Zwei Raeume gezeichnet          ["rgba(127,174,28,0.06)", "rgba(127,174,28,0.06)"]
✔ Ein Raum ist hervorgehoben — genau einer   ["#7fae1c", "rgba(127,174,28,0.06)"]
✔ Nach der Aenderung: KEINE Hervorhebung     ["rgba(127,174,28,0.06)"]   Raumzahl 2 -> 1
```

**Gemessen wird am gerenderten Konva-Bild**, *nicht am Quelltext:* `FARBEN.raum` =
`rgba(127,174,28,0.06)`, `FARBEN.auswahl` = `#7fae1c`.

### Der Fehlbefund, den diese Abnahme beinahe erzeugt hätte

**Mein erster Messgriff nahm „geschlossene, gefüllte Linie mit ≥ 4 Ecken" — und das sind die
Wandbänder auch.**

```text
gemeldet   9 Treffer fuer 2 Raeume:
           ["rgba(127,174,28,0.06)", "rgba(127,174,28,0.06)", "#4b5563" x7]
Folge      zwei Pruefungen fielen, obwohl der Bau richtig war
```

> **Aufgeklärt haben es die WERTE, nicht die Zahl.** *`#4b5563` ist `FARBEN.wandFuellung`.* **Wer
> nur „3 von 5 belegt" gelesen hätte, hätte einen Mangel gemeldet, den es nicht gibt.** *Behoben:
> die Probe unterscheidet jetzt nach FARBE statt nach Form, und der Grund steht als Kommentar im
> Skript.*

### Der Bildschirmabzug ist UNBRAUCHBAR, und das steht hier statt zu fehlen

**Er zeigt eine PHP-Meldung („Broken pipe") und eine halb gerenderte Seite** — *aufgenommen, als der
Browser schon schloss.*

> **Er belegt nichts, und ich führe ihn nicht als Beleg.** *Die Beweiskraft dieser Abnahme liegt in
> den Farbmessungen am lebenden Szenengraph, und die stehen oben mit ihren Werten.* **Ein Bild, das
> man vorzeigt, ohne es angesehen zu haben, ist genau die Sorte Beleg, die diese Kette
> auseinandernimmt.**

## Das Bündel, im selben Commit

```text
git show 83d6e108:public/hausplaner/hausplaner.js | grep -c 'signatur'   ->  1
```

**Am COMMIT gezählt, nicht im Arbeitsbaum** — *die Form, an der A-24 gehangen hat.*

> **Und die Regel aus A-23 gilt weiter:** *dort blieb das Bündel byte-identisch, weil nur Kommentare
> geändert wurden.* **Hier ändert es sich, also gehört es hinein.**

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert | **3** — `Buehne.tsx`, `HausplanerApp.tsx`, das Bündel |
| hinzugefügt | **3** — `raumAuswahl.ts`, sein Wächter, das Abnahmeskript |
| entfernt | **0** |
| Schema / Zod / Migration | **0** |
| Rückweg | `git revert`; der Kern ist ein eigenes Modul, die Fläche ruft es nur |
