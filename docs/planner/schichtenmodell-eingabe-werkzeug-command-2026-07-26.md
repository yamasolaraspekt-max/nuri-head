# Eingabe → Werkzeug → Command → Eignung → Objekt. Einordnung des Schichtenmodells

**26.07.2026, Planner.** Yama: *"Tools statten die Anwendung mit ausfuehrbaren Faehigkeiten aus.
Eingabegeraete loesen diese Tools aus. Die Tools wenden ihre Funktionen auf geeignete Objekte an."*

Drei Messungen dazu. **Eine davon zwingt mich, eine Aussage von vor einer Stunde zurueckzunehmen.**

---

## Messung 1 — Die Unterscheidung modal/sofort steht bereits

Yamas `ToolExecutionMode` (`modal` gegen `immediate`) gibt es schon, und zwar woertlich:

```
app/tools/faehigkeiten.ts:22
  export type FaehigkeitArt = 'werkzeug' | 'aktion' | 'engine';
  /** 'werkzeug' = setzt activeToolId · 'aktion' = Sofortbefehl
      · 'engine' = reine Eingang→Ergebnis-Rechnung. */
```

**Zwei seiner Arten sind da, und wir haben eine dritte, die er nicht nennt:** `engine`. Eine reine
Rechnung ist weder ein Bedienmodus noch ein Sofortbefehl — sie hat keinen Zeiger, keine Vorschau,
kein Abbrechen. Das ist keine Abweichung vom Entwurf, sondern eine Ergaenzung, die aus unserem
Fach kommt: dreizehn dieser Rechen-Engines liegen in `geometry/`, elf davon warten als AUF-52.

## Messung 2 — Der Command-Bus steht, die Werkzeug-Schicht fehlt

Yamas Trennung *Tool ist der Bedienmodus, Command ist die Aenderung* ist bei uns **halb gebaut**:

- **Command-Seite: da.** `executeCommand(command: HausplanerCommand)`, 19 Typen, ueber
  `produceWithPatches`, Undo/Redo aus inversen Patches. Genau seine „atomare, validierte
  Aenderung".
- **Werkzeug-Seite: nicht da.** Es gibt kein `interface Werkzeug` mit
  `pointerAb/pointerZug/pointerAuf/taste/abbrechen`. Gemessen: **29 Stellen** mit
  `werkzeug === '…'` in `HausplanerApp.tsx`. Der Bedienmodus ist kein Gegenstand, sondern eine
  Zeichenkette, ueber 29 Bedingungen verstreut.

**Damit ist klarer, was AUF-50.1 wirklich ist.** Ich hatte „eine Zuordnungstabelle" geschrieben —
das war zu klein gedacht. Es ist **die fehlende Schicht**: aus 29 Bedingungen wird eine Handvoll
Werkzeug-Gegenstaende mit denselben fuenf Methoden. Groesser als gedacht, und trotzdem die
richtige erste Stufe, weil alles Weitere darauf steht.

## Messung 3 — Und hier nehme ich etwas zurueck

Ich habe heute geschrieben: *"Touch ist ein eigenes Vorhaben in der Groessenordnung von AUF-50
selbst."* **Das war in der Groesse richtig geschaetzt und in der Form falsch.**

Gemessen, woher die Zeigersignale kommen:

```
HausplanerApp.tsx:1517   onMouseMove={(e) => setCursor(weltPunkt(e))}
```

**Die Zeichenflaeche spricht an genau einer Stelle mit dem Eingabegeraet, und sie spricht nur
Maus.** Kein `onPointerDown`, kein `onTouchStart`. Das ist die Wurzel der Null, die ich gemessen
habe — **nicht 75 Werkzeuge ohne Touch, sondern eine Ereignisquelle, die nur eine Sprache kennt.**

Yamas `NormalizedPointerEvent` beseitigt genau das: `source: 'mouse' | 'touch' | 'pen'`, ein
Weltpunkt, ein Knopf, drei Zusatztasten. **Wird normalisiert, bekommt jedes Werkzeug Touch, ohne
dass ein Werkzeug etwas von Touch weiss.**

**Die Korrektur lautet deshalb nicht „Touch ist billig", sondern:**

> **Die Reihenfolge entscheidet ueber den Preis.** Nach 50.1 ist Touch **ein Adapter plus
> Toleranzen** — Trefferradius, Zieh-Schwelle, Langdruck, Zwei-Finger. Vor 50.1 muesste dieselbe
> Arbeit in **29 Bedingungen** eingewebt werden, und zwar in einer Datei mit 2.052 Zeilen.

Touch bleibt ein P1 und ein eigenes Vorhaben. Aber es kostet nach 50.1 einen Bruchteil dessen, was
es davor kostet — und das ist ein Planungsargument, kein Bauchgefuehl.

---

## Zwei Warnungen zum Entwurf

### Warnung 1: `commandId` als Zeichenkette holt die Blindheit zurueck

Sein `ToolDefinition` traegt `commandId?: string`, und der Aufruf lautet
`commandBus.execute("MoveObject", {...})`.

**Das ist genau die Bauart, die uns heute den groessten Befund eingebracht hat.** Vier Zeilen
`setWerkzeug(tool.id as Werkzeug)` haben die Typpruefung stillgelegt, und deshalb hat der Compiler
**seit dem ersten Tag** nicht gemeldet, dass 83 Werkzeuge ins Leere zeigen. Ein Command, der ueber
seinen Namen als Text gerufen wird, ist derselbe Fall: ein Tippfehler oder ein umbenannter Command
faellt erst zur Laufzeit auf, und dann nur, wenn jemand hinsieht.

**Vorschlag:** die Zuordnung fuehrt auf den **typisierten Command**, nicht auf seinen Namen. Unser
`HausplanerCommand` ist eine Vereinigung von 19 Typen — der Compiler kennt sie alle. *Ein Register,
das auf Namen zeigt, ist ein Register ohne Pruefer.*

Falls fuer die Befehlspalette oder eine KI-Anweisung doch ein Textschluessel gebraucht wird: gern —
aber **an genau einer Stelle uebersetzt**, mit einer Vollstaendigkeitspruefung, die rot wird, wenn
ein Command keinen Schluessel hat. Nicht als durchgehender Aufrufweg.

### Warnung 2: Die Namensgleichheit hat uns bisher nur zufaellig verschont

Sein Hinweis, `objectTypeId: "window"` und `toolId: "create-window"` nicht beide `window` zu
nennen, trifft bei uns **noch** nicht zu — aber aus dem falschen Grund:

```
Werkzeug-ids (deutsch):  wand · fenster · tuer · dach · decke · treppe · heizkoerper
Schema-Typen (englisch): wall · window · door · roof · ceiling · stair · radiator
```

**Die Trennung existiert, weil die eine Seite deutsch und die andere englisch ist — nicht, weil
jemand sie entschieden hat.** Das ist eine Sicherheit, die beim ersten eingedeutschten Schema-Wert
verschwindet. Und die DAUERDIREKTIVE verbietet das Umbenennen persistierter Werte ohnehin, also
bleibt es so — aber **als Regel aufgeschrieben, nicht als Zufall.**

---

## Was ich uebernehme, ohne Vorbehalt

**Der Grundsatz.** *"Das Icon gibt der Maus keine Faehigkeit. Das Icon aktiviert ein Tool. Das Tool
interpretiert Maus, Tastatur oder Touch und nutzt die Eignungen des ausgewaehlten Objekts, um einen
Command auszufuehren."*

Und die vier Saetze darunter, die ich fuer die beste Zusammenfassung des ganzen Tages halte:

> Das Objekt sagt: *Was darf mit mir gemacht werden?*
> Das Werkzeug sagt: *Welche Aktion soll ausgefuehrt werden?*
> Das Eingabegeraet sagt: *Wie hat der Nutzer sie ausgeloest?*
> Der Command sagt: *Wie wird die Aenderung sicher durchgefuehrt?*

**Die Begriffstabelle** uebernehme ich als verbindlich — mit der einen Ersetzung: `Capability` am
Objekt heisst bei uns **Eignung**, weil `Faehigkeit` in `faehigkeiten.ts` bereits etwas anderes
bezeichnet.

---

## Was das an der Reihenfolge aendert

Der Zuschnitt von 23:45 bleibt richtig, **50.1 bekommt aber einen zweiten Teil** — und der ist der
Grund, warum alles Weitere billiger wird:

| Stufe | Inhalt | Probe des Erprobers |
|---|---|---|
| **50.1a** | Werkzeug-Schicht: aus 29 Bedingungen ein `interface Werkzeug` mit fuenf Methoden; die vier Umdeutungen weg | *Er klickt ein Werkzeug, das nichts tut, und liest, dass es nicht angeschlossen ist.* |
| **50.1b** | **Eingabe-Normalisierung** an der einen Ereignisquelle | *Er bedient dasselbe Werkzeug mit der Maus und mit dem Finger — und merkt keinen Unterschied im Verhalten.* |
| **50.1c** | Eignungen je Objekttyp, Voraussetzung je Verb | *Er waehlt eine Wand und sieht genau die Werkzeuge aufgehen, die zu einer Wand passen.* |
| 50.2 | Objekt-Katalog: ein Verb + 16 Eintraege | *Er waehlt „Heizkoerper", klickt in den Raum, ein Heizkoerper steht da.* |
| 50.3 | Generisches Eigenschaftenpanel aus dem Parameterschema | *Er aendert Masse und Farbe an einem Objekt, fuer das niemand ein Panel gebaut hat.* |
| 50.4 | Die Zahl | *Er tippt 4000 und drueckt Enter.* |
| 50.5 | Griffe | *Er sieht Griffe am Fenster.* |
| 50.6 | Der sprechende Fang | *Er liest „Endpunkt", bevor er klickt.* |

**50.1b ist neu und steht bewusst vor allem anderen ausser der Werkzeug-Schicht.** Es ist die
Stelle, an der Touch von einem eigenen Vorhaben zu einer Eigenschaft des Fundaments wird.

**Und eine Grenze, die ich ziehe:** 50.1a beruehrt `HausplanerApp.tsx` tief — dieselbe Datei, die
AUF-48 zerlegen soll. **Beides gleichzeitig geht nicht.** Entweder AUF-48 zuerst und 50.1 baut auf
zerlegten Dateien, oder 50.1 zuerst und AUF-48 zerlegt danach weniger. Ich halte die erste
Reihenfolge fuer richtig, aber es ist eine Entscheidung mit Kosten in beide Richtungen und sie
gehoert zu Yamas offener Frage 3.
