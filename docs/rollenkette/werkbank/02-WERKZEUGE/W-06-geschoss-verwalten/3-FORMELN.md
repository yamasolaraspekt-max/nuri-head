# W-06 · Geschoss verwalten — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.** *Eine Formel, die an zwei Orten steht, wird an einem Ort korrigiert und
> am anderen vergessen.*

## Benutzte Formeln: KEINE — und das ist gemessen, nicht angenommen

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| — | — | — |

**Die gesamte Mathematik der drei Module, alle Fundstellen:**

```text
geometry/geschossVorlage.ts          keine
app/dashboard/geschossStapel.ts      Math.abs   (:57, Betrag fuer die Hoehenlage)
app/dashboard/GeschossFlaeche.tsx    keine
```

**Kein `Math.hypot`, kein `Math.sqrt`, kein `Math.cos`, keine Matrix.** *Was W-06 rechnet, sind drei
Additionen (`elevation + defaultWallHeight + floorThickness`, `sortOrder + 1`, `position − 1 +
richtung`) und ein Betrag für die Vorzeichen-Ausgabe.* **Dafür kennt die Sammlung keine Nummer, und
sie braucht auch keine.**

> **Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke.** *Die Sammlung ist ein
> Geometrie-Verzeichnis; eine Addition gehört nicht hinein, nur weil sie eine Zahl liefert.*

## Die Registerspalte nennt F-032 — und das ist eine FORMEL, keine Sperre

**Der Punkt, der viermal Messzeit spart.** *Gemessen: **vier** Werkzeuge tragen `F-032` in ihrer
Registerspalte, und alle vier stehen auf `LEER`:*

```text
02-WERKZEUGE/REGISTER.md:38   W-12  Ansicht und Kamera
                        :48   W-16  Grundriss unterlegen
                        :54   W-06  Geschoss verwalten
                        :67   W-14  Kopieren/Spiegeln/Drehen
```

> **Vier Werkzeuge, eine Referenz, alle LEER — das SIEHT aus wie ein gemeinsamer Blocker.** *Es ist
> keiner.* **`F-032` ist die Formel „Transformation eines Punktes"** (`FORMELSAMMLUNG.md`, Abschnitt
> `### F-032 · Transformation eines Punktes`) — *eine homogene 4×4-Matrix, keine Sperre, keine Ampel,
> nichts, worauf man wartet.* **Die Registerspalte ist die Formelreferenz und nicht ein Hindernis.**

**Wer sie für eine Sperre hält, lässt vier Werkzeuge unbeschrieben** — *und zwar aus demselben Grund,
aus dem ein Muster die Schreibweise misst statt die Sache: ein Wort gelesen, statt nachgesehen.*

**Und W-06 selbst benutzt F-032 nicht.** *Sie steht als Referenz im Register; im Code der drei Module
kommt keine Transformation vor.* **Wo sie gebraucht würde — Ansicht, Kamera, Spiegeln — ist ein
anderes Werkzeug.**

> *Die Fundstelle steht hier als **Anker** und nicht als Zeilennummer (A-34): eine Einfügung in die
> Sammlung verschiebt jede Zahl dahinter lautlos, und der Verweis zeigt dann auf eine andere Formel.*

## Normative Größen

**Keine.** *W-06 rechnet keine Last, keine Norm, keinen Beiwert. Die Höhenlage ist eine Summe aus
Werten, die der Anwender gesetzt hat.*
