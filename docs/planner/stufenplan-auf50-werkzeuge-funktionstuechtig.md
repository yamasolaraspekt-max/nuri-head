# AUF-50 — Stufenplan: die 110 Werkzeuge funktionstüchtig machen

*Planner, 30.07.2026, 06:42 CEST. **Der Zuschnitt liegt seit dem 26.07., das Blatt fehlte.**
Yamas Frage vom 25.07.: „sollen wir jetzt coden funktionstüchtig machen".*

> **Vorab, weil die Zahl „110 gegen 19" seit Tagen durch die Berichte geht und größer klingt, als
> sie ist:** ich habe sie heute zum ersten Mal aufgeschlüsselt. **33 der 110 Werkzeuge brauchen
> überhaupt keinen Modellbefehl.** Die echte Lücke ist kleiner und hat eine Form.

---

## 1. Was gemessen ist (30.07., 06:42 — Befehle in §7)

```text
110 Vertraege in werkzeugVertrag.ts (1419 Zeilen, AUF-36)
 19 Modellbefehle in applyCommand.ts (424 Zeilen, 19 case-Zweige)
 77 Vertraege mit  umkehrbar: true    ← aendern das Modell, brauchen einen Befehl
 33 Vertraege mit  umkehrbar: false   ← Ansicht, Auswahl, Messen: brauchen KEINEN
```

**Die neun Familien:**

| Familie | Anzahl | Ändert das Modell? |
|---|---|---|
| `create` | **40** | ja — der Hauptblock |
| `modify` | **20** | ja |
| `workflow` | 15 | teils |
| `assign-or-calculate` | 9 | teils |
| `import` | 8 | ja |
| `view` | 7 | **nein** |
| `measurement` | 5 | **nein** |
| `selection` | 4 | **nein** |
| `domain` | 2 | teils |

**Und die wichtigste Zeile aus dem Bestand — sie ist eine Entscheidung, kein Zufall:**

> `werkzeugVertrag.ts` sagt selbst: *„Hier entsteht **kein zweiter Ausführungsweg**. Es gibt kein
> `runTool`, keinen Dispatcher … Ein zweiter Weg daneben verlöre Undo und die Ablehnungsprüfung —
> das wäre der teuerste Fehler des ganzen Pakets."*

**Gemessen: es gibt keinen Dispatcher, und das ist richtig so.** AUF-50 baut deshalb **keinen**.
*Wer aus 110 Verträgen einen Dispatcher macht, hat 110 Wege am `applyCommand` vorbei.*

**Was noch steht und benutzt werden kann:** 4 240 Zeilen Werkzeugschicht — Aktivierungs-Engine
(`activation.ts`, rein, ohne DOM), 12 Vorbedingungen (`vorbedingungen.ts`), Darstellung
(`toolPresentation.ts`), Zustand als Daten (`werkzeugZustand.ts`), Fähigkeiten-Registry
(`faehigkeiten.ts`). **Der Unterbau ist da. Was fehlt, ist die Ausführung.**

---

## 2. Die Frage, die vor jeder Stufe steht

**Nicht: „wie führen wir 110 Befehle aus?"** Sondern, je Vertrag:

> **Ist das ein eigener Modellbefehl — oder ein vorhandener mit anderen Daten — oder gar keiner?**

Ein Beispiel aus der Messung: **40 Verträge tragen `familie: 'create'`.** Das Modell kennt
`ADD_NODE` mit einem `type`-Feld. **Ein Großteil der 40 sind vermutlich `ADD_NODE` mit
unterschiedlichem `type`, nicht 40 neue Befehle.** *Vermutlich — das ist Stufe 1, und sie ist eine
Zählung, kein Bau.*

---

## 3. Vier Stufen

### Stufe 1 — Die Landkarte (kein Produktivcode)

**Je Vertrag eine von vier Marken**, als Daten neben dem Vertrag, nicht als Prosa:

| Marke | Bedeutung | erwartet |
|---|---|---|
| `deckt` | ein vorhandener Modellbefehl leistet es (ggf. mit anderen Daten) | der größte Teil der 40 `create` |
| `fehlt` | braucht einen neuen Befehl in `applyCommand` | der eigentliche Bauvorrat |
| `ohne-modell` | Ansicht, Auswahl, Messen — berührt das Modell nie | **die 33 mit `umkehrbar: false`** |
| `stillgelegt` | gehört nicht in einen Bauplaner | `toolCatalogStillgelegt.ts` führt so etwas bereits |

**Das Ergebnis ist eine Zahl, die es heute nicht gibt: wie viele Befehle wirklich fehlen.**
*Alles danach hängt daran. Ohne sie ist jeder Aufwandssatz geraten — und geratene Zahlen sind
F-04, die Klasse mit fünf Ausprägungen.*

**Abnahme:** die Summe der vier Marken ist **110**, jede Marke hat eine Begründung aus dem Vertrag
(nicht aus dem Gefühl), und `ohne-modell` deckt sich mit `umkehrbar: false` — **oder die Abweichung
ist einzeln erklärt.**

### Stufe 2 — Die `ohne-modell`-Werkzeuge zum Leben bringen

**Sie sind die billigsten und die sichtbarsten.** Ansicht, Auswahl, Messen: kein neuer Befehl,
keine Migration, kein Undo-Risiko. **Ein Nutzer merkt sofort, ob ein Maßwerkzeug misst.**

*Hier liegt auch ein offener P1: `auswahlDarstellung.griffe` wird berechnet und nie gezeichnet.
Zweiter Fall dieser Art im Projekt — der erste war die Höhenlage, die AUF-43 sichtbar gemacht hat.*

**Vorbedingung:** AUF-48 Scheibe 4 (das JSX), weil das Zeichnen dort sitzt.

### Stufe 3 — `deckt`: vorhandene Befehle erreichbar machen

Werkzeuge, deren Modellbefehl **existiert** und die trotzdem nichts tun. **Kein neuer Befehl, nur
die Verbindung** — und die entsteht dort, wo die Werkzeugleiste steht, nicht in einem Dispatcher.

**Grenze, wörtlich aus dem Bestand:** jede Änderung am Modell läuft über `applyCommand` mit
inversen Patches und `CommandAbgelehnt` **vor** der Mutation. *Wer daran vorbei ausführt, verliert
Undo — und Undo ist bei einem Bauplaner keine Bequemlichkeit.*

### Stufe 4 — `fehlt`: die neuen Modellbefehle

**Erst jetzt, und in Paketen nach Familie.** Jeder neue Befehl braucht: inverse Patches, eine
Ablehnungsprüfung, eine Zusage, und **Bestandsdaten dürfen nicht brechen.**

**Das ist Spur A, jedes Paket einzeln.** *Und es ist der einzige Teil von AUF-50, dessen Umfang ich
heute nicht kenne — weil Stufe 1 ihn erst misst.*

---

## 4. Reihenfolge und Sperren

```text
Stufe 1   frei, sobald ein Blatt vorliegt — beruehrt KEINEN Produktivcode
Stufe 2   gesperrt bis AUF-48 Scheibe 4 (das JSX)
Stufe 3   gesperrt bis Stufe 1 abgenommen ist
Stufe 4   gesperrt bis Stufe 3 steht, danach Paket fuer Paket, Spur A
```

**Stufe 1 kann sofort laufen und kollidiert mit nichts** — sie schreibt eine Datei neben den
Vertrag und fasst weder `HausplanerApp.tsx` noch die Blades an. *Sie ist damit der einzige Teil
der Werkzeug-Baustelle, der neben der Layout-Kette herlaufen darf.*

---

## 5. Was dieser Plan ausdrücklich nicht tut

- **Keinen Dispatcher.** Steht dreimal oben, weil es die teuerste mögliche Abkürzung ist.
- **Keine Umbenennung persistierter Werte** (`type: wall|window|door|ceiling`, `objectType`,
  `zoneType`, `routeType`) — DAUERDIREKTIVE.
- **Keine Aufwandsschätzung.** Sie käme vor Stufe 1 und wäre geraten.
- **Kein „alle 110 werden funktionieren".** Manche werden `stillgelegt` — *und das ist ein
  Ergebnis, kein Scheitern. Ein Bauplaner braucht keine Bézier-Kurven.*

---

## 6. Was Yama davon hat, und wann

**Nach Stufe 1** weiß er zum ersten Mal, wie groß die Werkzeug-Baustelle wirklich ist — als Zahl,
nicht als „110 gegen 19".

**Nach Stufe 2** merkt er es am Bildschirm: Messen misst, Auswählen zeigt Griffe.

**Nach Stufe 3** funktionieren die Werkzeuge, deren Maschinerie längst da war. *Das ist
erfahrungsgemäß der Sprung, den ein Nutzer als „jetzt geht es" beschreibt.*

**Stufe 4 ist die eigentliche Bauzeit** — und ihr Umfang steht nach Stufe 1 fest, nicht vorher.

---

## 7. Messbefehle

```text
grep -rho "commandId: *'[^']*'" resources/planner/hausplaner/ | sort -u | wc -l     → 110
grep -c "case '" resources/planner/hausplaner/commands/applyCommand.ts              →  19
grep -c "umkehrbar: true"  resources/planner/hausplaner/app/tools/werkzeugVertrag.ts →  77
grep -c "umkehrbar: false" resources/planner/hausplaner/app/tools/werkzeugVertrag.ts →  33
grep -o "familie: *'[^']*'" …/werkzeugVertrag.ts | sort | uniq -c | sort -rn        → die neun Familien
wc -l resources/planner/hausplaner/app/tools/*.ts                                   → 4240
```

**Alle sechs sind am 30.07. um 06:42 gefahren worden, nicht übernommen.**
