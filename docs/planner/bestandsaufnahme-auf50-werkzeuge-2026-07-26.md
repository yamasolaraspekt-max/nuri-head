# AUF-50 — die Zählung vor dem Stufenplan

**Planner · 26.07.2026, 20:45 · alle Zahlen an diesem Tag gemessen, HEAD `72e3d31`**

**Warum diese Datei existiert:** AUF-50 ist seit 20:25 entsperrt (die Bedingung *erst Layout fertig*
ist mit AUF-39 · 43 · 44 · 45 · 47 buchstäblich erfüllt). Die Tafel sagt seit dem 25.07.: *„Planner
schreibt danach den Stufenplan."* **Einen Auftrag über 110 Werkzeuge zu schreiben, ohne vorher
gezählt zu haben, wäre genau die Sorte Auftrag, die der Generator heute dreimal zu Recht korrigiert
hat.** Also erst die Zählung.

---

## 1. Die vier Zahlen, um die es geht

| Was | Zahl | Quelle |
|---|--:|---|
| Werkzeuge im Paket (Registry) | **101** | `app/tools/werkzeugPaket.ts` |
| Funktionsverträge | **110** | `app/tools/werkzeugVertrag.ts` |
| **Werkzeug-Modi, die die Zeichenfläche kennt** | **7** | `app/HausplanerApp.tsx`, `type Werkzeug` |
| Ausführbare Command-Typen im Modell | **19** | `domain/commands.types.ts` |

**Die dritte Zahl ist die, die alles entscheidet.** Der Typ, den die Zeichenfläche versteht, lautet
vollständig:

```ts
type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke'
```

**Sieben.** Jedes andere Werkzeug **setzt zwar `activeToolId`** — der Klick geht also nicht ins
Leere —, **aber niemand hört zu.** Es gibt keinen Empfänger, der auf diesen Wert reagiert.

**Damit lautet der Befund: 7 von 101 sind angeschlossen. 94 haben heute keinen Empfänger.**

## 2. Warum „110 Verträge" nicht bedeutet, dass 110 Dinge tun

Der Vertrag führt zu jedem Werkzeug eine `commandId` — `WallCommand`, `RotateCommand`,
`LassoSelectCommand` und so fort, **110 verschiedene**. Das sieht nach 110 Befehlen aus. **Der
Vertrag sagt selbst, dass es keine sind:**

> *„Technischer Command-Name aus dem Vertrag, z. B. `WallCommand`. **Metadatum, kein Aufruf.**"*

Und die Gegenzahl bestätigt es: das Modell kennt **19** Command-Typen, und `app/` löst sie an
**34** Stellen aus. **110 Namen stehen 19 ausführbaren Befehlen gegenüber.** Wer die Verträge für
Funktion hält, verzählt sich um den Faktor fünf.

**Das ist kein Vorwurf an AUF-36**, der die Verträge gebaut hat: sie sind die *Beschreibung*, was
ein Werkzeug verspricht — genau dafür waren sie gedacht, und sie sind der Grund, warum die Zählung
hier überhaupt möglich ist.

## 3. Wie sich die 110 Verträge auf Familien verteilen

| Familie | Anzahl | Was das für den Bau heißt |
|---|--:|---|
| `create` | **40** | Etwas entsteht in der Szene ⇒ braucht **ADD_NODE** und einen Zeichenmodus |
| `modify` | **20** | Etwas Vorhandenes ändert sich ⇒ **UPDATE_NODE/MOVE_NODE**, braucht **Auswahl** |
| `workflow` | **15** | Führt durch einen Ablauf ⇒ kein Command, sondern Oberfläche |
| `assign-or-calculate` | **9** | Rechnet oder weist zu ⇒ hängt an den **Engines** (AUF-52) |
| `import` | **8** | **Phase 2 des Fahrplans** — steht nach §14 still |
| `view` | **7** | Ändert nur die Ansicht ⇒ **kein** Command, kein Undo |
| `measurement` | **5** | Misst ⇒ liest die Szene, schreibt nicht |
| `selection` | **4** | Wählt aus ⇒ Auswahlzustand, kein Command |
| `domain` | **2** | Fachlogik |

**Die Verteilung sagt, wo der Aufwand liegt:** `create` und `modify` sind zusammen **60 von 110**
und brauchen beide dasselbe Fundament — einen Empfänger, der auf `activeToolId` reagiert, und einen
Weg von der Zeichenfläche zu einem Command.

## 4. Was daraus für den Stufenplan folgt

**Nicht 110 Posten, sondern vier Stufen — und die erste ist die einzige, die wirklich neu ist.**

| Stufe | Inhalt | Warum in dieser Reihenfolge |
|---|---|---|
| **50.1** | **Ein Empfänger.** Die Zeichenfläche reagiert auf `activeToolId` **generisch** statt über sieben feste Namen: ein Werkzeug erklärt in seinem Vertrag, was es erzeugt, und der Empfänger führt das aus. | **Ohne diese Stufe ist jedes weitere Werkzeug ein neuer `case` in einem `switch`** — 101 `case` sind kein Bauplan, sondern ein Symptom |
| **50.2** | **`create`, in Scheiben nach Bauteil** (Wand · Öffnung · Dach · Decke · Treppe · Objekt · Route) | Sie teilen sich einen Command (`ADD_NODE`) und unterscheiden sich nur im Knotentyp |
| **50.3** | **`modify` + `selection`** (24) | Setzt Auswahl voraus, also **50.2** |
| **50.4** | **`view` + `measurement`** (12) | Schreibt nichts, braucht kein Command — **die billigste Stufe, und deshalb nicht die erste**: sie würde den Fortschritt schmeicheln, ohne das Fundament zu legen |

**Ausdrücklich nicht in AUF-50:** die **8 `import`-Werkzeuge** (Phase 2, steht nach §14 still) und
die **9 `assign-or-calculate`** (die hängen an AUF-52, den zwölf Engines, und sind dort schon
beauftragt). **AUF-50 ist damit ein Posten über ~78 Werkzeuge, nicht über 110** — und die
Unterscheidung gehört in den Auftrag, nicht in die Ausrede hinterher.

## 5. Was ich noch nicht gemessen habe

- **Wie viele der 101 heute im Zustand `gesperrt` stehen**, weil ihre Vorbedingung nicht erfüllt
  ist — das ist eine andere Frage als „hat einen Empfänger", und sie ändert die Reihenfolge
  innerhalb von 50.2 möglicherweise.
- **Ob der generische Empfänger aus 50.1 ohne Änderung an `store/` oder `domain/` möglich ist.**
  Das ist die eine Stelle, an der AUF-50 die K4-Schichten berühren könnte — **und wenn ja, ist das
  eine Rückgabe an Yama, keine Entscheidung des Planners.**

**Beides messe ich, bevor ich 50.1 als Auftrag schreibe.** Diese Datei ist die Zählung, nicht der
Auftrag.
