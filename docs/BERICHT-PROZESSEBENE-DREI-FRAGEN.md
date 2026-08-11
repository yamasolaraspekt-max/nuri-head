# Bericht — die drei Fragen aus Teil 4. Die Prozessebene ist nicht leer, sie ist gebaut

```yaml
art: "Messbericht des Planners auf Yamas Teil-4-Fragen. KEINE Einordnung, KEINE Entscheidung."
gemessen_am: "12.08."
basis_sha: 717eb11c
auftrag: "Yamas Zielbild Teil 4 und Teil 6 Punkt 2 — Existenz, Umfang, Fundstelle. Messen, nicht bewerten."
grenze: "Wo die Prozessebene hingehoert, entscheidet Yama. Dieser Bericht liefert die Zahlen dafuer."
```

## Die Kernaussage, bevor die Einzelheiten kommen

**Der Auftrag vermutet eine leere Stelle:** *„euer Register kennt dafür **keine einzige Zeile**, und
im Code gibt es bereits `ConfigWizard.tsx` samt Test."*

**Die erste Hälfte stimmt — das Register kennt keine Zeile. Die zweite ist zu klein:** *es gibt nicht
„bereits einen Wizard", sondern **eine gebaute Prozessebene aus fünf Bausteinen mit elf Schritten,
einem Statusmodell, Prüfpunkten, Aufgaben und Empfehlungen.***

```text
app/dashboard/fahrschritte.ts   202 Z   die ELF Schritte, "abgeleitet aus dem Modell"
app/GuidedView.tsx              165 Z   Stepper + Fokus-Schrittkarte + Aufgabe-Panel + Navigation
app/ConfigWizard.tsx            271 Z   gefuehrter Dialog Fenster/Tuer/Treppe, 5 Schritte
app/studioDaten.ts              (Teil)  SchrittStatus · Pruefpunkt · Aufgabe · Empfehlung · Fahrschritt
app/HausplanerStudio.tsx        (Teil)  ruft GuidedView und ConfigWizard
                                        zusammen: > 640 Zeilen Prozessebene
```

---

## FRAGE 1 · Was kann `ConfigWizard.tsx` heute?

```text
Umfang        271 Zeilen, 2 Exporte
Gegenstand    laut Dateikopf: "gefuehrter Konfigurator-Dialog fuer FENSTER/TUER/TREPPE"
              -> NICHT fuer Geschosse, Rohbau oder Dach
Schritte      Bauart -> Masse -> Material -> Pruefung -> Uebernehmen, mit Live-Vorschau
Aufgerufen    app/GuidedView.tsx · app/HausplanerStudio.tsx ·
von            app/dashboard/ObjektkopfUeberlauf.tsx · app/dashboard/fachFlaechen.ts
```

**Und die Antwort auf „was schreibt er ins `BuildingDocument`" ist die wichtigste des Berichts —
sie steht wörtlich im Dateikopf:**

> *„‚Übernehmen' erzeugt ein echtes autarkes `ConfiguratorPackage` (`geometry/configuratorPackage.ts`)
> und **lädt es als JSON herunter**. **Der Schreibpfad ins Gebäudemodell (Command) bleibt die nächste
> Scheibe.**"*

```text
-> Er schreibt NICHTS ins BuildingDocument. Er erzeugt ein Paket und laedt es herunter.
-> Der Wizard ist heute ein ERZEUGER, kein Bearbeiter des Modells.
```

> **Das ist die genaue Stelle, an der Yamas Regel aus Teil 5 heute noch nicht erfüllbar ist:**
> *„Wizard und Expertenmodus arbeiten auf denselben Fachobjekten. Der Wizard ist kein zweites
> Modell."* **Solange „Übernehmen" eine JSON-Datei herunterlädt statt ein Kommando abzusetzen,
> arbeiten sie nicht auf denselben Objekten.** *Der Dateikopf sagt das selbst und nennt es „die
> nächste Scheibe" — es ist bekannt und benannt, nicht verschwiegen.*

---

## FRAGE 2 · Existiert ein Geschossmodell — und trägt es die drei Felder?

```text
GEFUNDEN in domain/scene.types.ts:
  :66   elevation: number;          // mm ueber ±0            <- JA, existiert
  :327  traufhoeheMm                // Default: level.elevation + defaultWallHeight
  :343  "Geschossdecke (Feature A, additiv). Slab auf der Wand-Oberkante des Levels
         (level.elevation + …)"                               <- Decke existiert
  :369  geschoss: number
  :397  geschoss: number

NICHT GEFUNDEN:
  baseElementId       0 Treffer
  ceilingElementId    0 Treffer
```

**Bewertung, so knapp wie möglich:** *`elevation` ist da, die Geschossdecke ist da, und sie ist
**additiv** gebaut (Feature A). **Was fehlt, ist die explizite Beziehung**, die das Zielbild
verlangt:*

```
UpperFloor.baseSlabId = LowerFloor.ceilingSlabId      -> existiert NICHT
```

> **Heute wird die Höhe **gerechnet** (`level.elevation + defaultWallHeight`), nicht **verwiesen**.**
> *Das funktioniert, solange niemand eine Decke verschiebt oder ein Geschoss dazwischenschiebt — und
> es ist genau die Stelle, an der Yamas Regel „ab dem zweiten Geschoss entsteht keine neue
> Bodenplatte" datenseitig noch nichts hat, worauf sie sich stützen könnte.* **Das Datenmodell muss
> erweitert werden; es muss nicht neu gebaut werden.**

---

## FRAGE 3 · Gibt es Status oder Revision je Bauabschnitt?

**Zwei verschiedene Dinge gefunden, und sie dürfen nicht verwechselt werden:**

```text
1  scene.types.ts:27   revision: number;
                       "Server-vergeben; Client sendet base_revision beim Speichern"
   -> das ist OPTIMISTIC LOCKING fuers ganze Dokument, KEIN Bauabschnitts-Status.
      Es verhindert, dass zwei Nutzer sich ueberschreiben. Nichts weiter.

2  studioDaten.ts:163  export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
   studioDaten.ts:164  export interface Pruefpunkt { status: SchrittStatus; text: string; }
   studioDaten.ts      export interface Aufgabe { warn?, titel, detail? }
   studioDaten.ts      export interface Empfehlung { titel, aktion, cfg? }
   studioDaten.ts      export interface Fahrschritt { … status: SchrittStatus … }
   studioDaten.ts:255  export const STATUS_LABEL: Record<SchrittStatus, string>
   -> DAS ist ein Statusmodell je Schritt, und es ist gebaut.
```

**Der Abgleich mit dem Zielbild, Zahl gegen Zahl:**

| | Zielbild 3.6 | gebaut |
|---|---|---|
| Stufen | **acht** | **vier** |
| | `not-started` | `open` |
| | `in-progress` | `prog` |
| | `incomplete` | `warn` (näherungsweise) |
| | `review-required` | — |
| | `valid` | `ok` |
| | `confirmed` | **fehlt** |
| | `outdated` | **fehlt** |
| | `blocked` | **fehlt** |

> **Die drei fehlenden Stufen sind genau die, die das Zielbild inhaltlich trägt.** *`confirmed`
> trennt „gerechnet" von „vom Nutzer bestätigt" — ohne sie kann L-9 (PV erst nach **bestätigter**
> Dachgeometrie) nicht geprüft werden. **`outdated` ist die Invalidierung**, also der Kern von
> „Änderungen propagieren, niemals stille Löschung". **`blocked` ist die Sperre.*** *Die vier
> vorhandenen Stufen beschreiben **Fortschritt**; die drei fehlenden beschreiben **Gültigkeit**. Das
> sind zwei Achsen, nicht eine längere Liste.*

**Und `Empfehlung { titel, aktion, cfg? }` ist bereits Yamas 3.5** — *„der Wizard schlägt den nächsten
Schritt vor"*. **Auch das ist gebaut, nicht zu erfinden.**

---

## Was ich NICHT gemessen habe und deshalb nicht behaupte

```text
- Die INHALTE der elf Schritte. fahrschritte.ts hat 202 Zeilen; ich habe die Datei
  NICHT durchgelesen und weiss nicht, ob die elf Schritte Yamas Reihenfolge aus 3.1
  entsprechen. Mein Extraktionsbefehl auf id/titel lieferte 0 Treffer — die Struktur
  ist anders aufgebaut, als ich vermutet hatte. Das ist eine offene Messung, kein Befund.
- Ob GuidedView den Fortschritt JE GESCHOSS zeigt (Zielbild 3.4) oder nur global.
- Ob es einen Abhaengigkeitsgraphen gibt. Ich habe nach status/revision gesucht,
  nicht nach Kanten zwischen Bauteilen.
- Den Test zu ConfigWizard, den Yama erwaehnt. Ich habe seine Existenz nicht geprueft.
```

> *Vier ungemessene Punkte, ausdrücklich benannt. **Nach fünf Messfehlern an zwei Tagen schreibe ich
> lieber vier Lücken hin als eine Vermutung.***

---

## Was dieser Bericht für Yamas Entscheidung bedeutet

**Ich ordne nicht ein — aber die Zahlen verschieben die Frage:**

```text
Die Frage war:  "bekommt die Prozessebene eine eigene Werkzeugreihe (P-01, P-02 …),
                 eine eigene Werkbank-Abteilung, oder gehoert sie in 00-ARCHITEKTUR?"

Was die Messung hinzufuegt:
  Es geht nicht um die Einordnung von etwas NEUEM, sondern um die Einordnung von
  ueber 640 Zeilen BESTEHENDEM Code, der bereits elf Schritte, vier Statusstufen,
  Pruefpunkte, Aufgaben und Empfehlungen traegt — und der in KEINER Registerzeile steht.

  Damit ist es dieselbe Lage wie bei W-06 bis W-23, nur eine Ebene hoeher:
  gebauter Code ohne Werkbankzeile. Und dieselbe Richtung wie in WERKBANK-ANSCHLUSS.md:
  "der Anschluss ist Code -> Werkbank eintragen, nicht umgekehrt."
```

**Die drei konkreten Lücken, die eine Entscheidung braucht — nicht die Ebene als Ganzes:**

```text
1  der SCHREIBPFAD des Wizards ins Gebaeudemodell ("bleibt die naechste Scheibe")
2  die drei fehlenden Statusstufen confirmed · outdated · blocked
3  baseElementId / ceilingElementId im Geschossmodell
```

```yaml
frage_1: "ConfigWizard 271 Z, Fenster/Tuer/Treppe, 5 Schritte — schreibt NICHTS ins
          BuildingDocument, laedt JSON herunter. Schreibpfad ist als 'naechste Scheibe' benannt."
frage_2: "Geschossmodell existiert (elevation :66, Geschossdecke additiv :343).
          baseElementId und ceilingElementId FEHLEN — Hoehe wird gerechnet, nicht verwiesen."
frage_3: "ZWEI Dinge: revision :27 ist Optimistic Locking, kein Status. SchrittStatus :163
          ist ein echtes Statusmodell mit VIER Stufen; das Zielbild verlangt ACHT.
          Die drei fehlenden (confirmed/outdated/blocked) sind die Gueltigkeitsachse."
nicht_gemessen: "Inhalte der elf Schritte · Fortschritt je Geschoss · Abhaengigkeitsgraph ·
                 der ConfigWizard-Test"
entscheidung: "gehoert Yama. Dieser Bericht liefert Zahlen, keine Empfehlung zur Einordnung."
```
