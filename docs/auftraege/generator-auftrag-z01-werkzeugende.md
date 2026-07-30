# Z-01 — Ein Werkzeug endet an genau einer Stelle, und das Verlassen des Canvas hält die Vorschau an

**Spur A** *(Zeichenlogik und Zustandsführung, kein Markup)* · **Heimat: ticket** ·
**Basis: HEAD beim Ziehen**
*Geschnitten 30.07.2026, 23:20.* **Herkunft:** Yamas Prompt „Intelligentes Zeichnen",
Abschnitte 7 und 8 — Quelle `docs/quellen/prompt-zeichnen-2026-07-30.md`,
Messung `docs/planner/programm-zeichnen-bestandsaufnahme.md` (B-3, B-4, B-5, B-6).

Erste von elf Scheiben. Yamas Kernaussage: *„Der lange Strich ist kein Darstellungsproblem,
sondern ein fehlerhafter Tool-Lifecycle- beziehungsweise Preview-State."*

## Der Befund / die Lage

```text
resources/planner/hausplaner:

1) Aufraeumen beim Werkzeugwechsel steht VIERMAL abgeschrieben:
   app/HausplanerApp.tsx:494-496          setWandStart(null); setTreppeStart(null); setWerkzeug(..)
   app/HausplanerApp.tsx:829-831          dito
   app/rahmen/GruppenzeileUndSchiene.tsx:334   dito
   app/rahmen/GruppenzeileUndSchiene.tsx:354   dito
   app/HausplanerApp.tsx:778               (Escape-Weg, raeumt korrekt) -> zusammen 5 Vorkommen

2) Und EINMAL vergessen:
   app/HausplanerApp.tsx:371   usePlannerUiStore.getState().setActiveTool('auswahl')
   -> Rueckfall-Effekt bei ausgefallenem Werkzeug. wandStart/treppeStart bleiben stehen.

3) Der Setter raeumt nicht auf:
   app/HausplanerApp.tsx:183   const setWerkzeug = useCallback((w) => ...setActiveTool(w), [])

4) An der Buehne haengt kein Verlassen-Ereignis:
   app/rahmen/Buehne.tsx:88-107  onClick, onMouseMove, onWheel, onDragMove, onDragEnd
   grep -rn "onMouseLeave|onPointerLeave" app/rahmen/  -> 0 Treffer

5) setPointerCapture kommt im GESAMTEN Baum nicht vor -> dieser Verdacht ist erledigt.

6) Vorschau ist bereits vom Dokument getrennt:
   app/rahmen/Buehne.tsx:363-378  zwei <Group listening={false}> ausserhalb des Dokuments.
```

## Schritt 0 — vor jeder Zeile Code: den Fehler sehen

Der Planner erwartet, dass der Strich beim Verlassen **einfriert** (weil `onMouseMove` an der
Bühne hängt, nicht am Fenster) und nicht dem Zeiger folgt. **Das ist unbewiesen.**

Bau, starte, ziehe eine Wand halb, fahre zur Werkzeugleiste — und **schreibe auf, was du
siehst**: friert der Strich am Rand ein, oder folgt er dem Zeiger über den Canvas hinaus?
Der Befund gehört ins Protokoll, auch wenn er meiner Erwartung widerspricht. **Widerspricht er
ihr, meldest du zurück statt zu bauen** — dann liegt eine Ursache vor, die hier nicht steht.

## Umfang

| Naht | Anker |
|---|---|
| Anfang | `HausplanerApp.tsx`: die Zuweisung an den Namen `setWerkzeug` |
| Ende | dieselbe Datei: die Zuweisung an `setzeWerkzeugZurueck` (der bestehende Escape-Weg) |
| Zweitens | `Buehne.tsx`: das öffnende `<Stage` bis zu seinem eigenen `>` — die Ereignisliste |
| Drittens | neue Datei `app/tools/werkzeugEnde.ts` — die reine Entscheidung, testbar ohne React |

**Nicht enthalten:** kein `InteractiveTool`-Interface mit zwölf Methoden (Yamas Abschnitt 8
vollständig) — das wäre ein Posten, der auf nichts zeigt, solange nur zwei Werkzeuge einen
Zwischenzustand haben. Gebaut wird genau so viel Lebenslauf, wie heute zwei Werkzeuge
brauchen: **beenden** und **pausieren**. Kein `fangKern`-Anschluss (das ist Z-02). Keine
Statusleisten-Formulierung über den einen Text hinaus, der in K-05 steht.

## Die Festlegung

Yamas eigene Standardempfehlung, übernommen:

- **Werkzeugwechsel bricht ab.** Unbestätigte Teilaktion weg, Vorschau weg.
- **Canvas verlassen pausiert nur.** Startpunkt bleibt, Vorschau wird **ausgeblendet**
  (nicht eingefroren stehen gelassen), Statusleiste sagt es.
- **Zurück in den Canvas:** Vorschau lebt wieder auf, ohne Klick.

## Kriterien

```yaml
  - id: K-01
    aussage: "Es gibt genau einen Ort, der ein Werkzeug beendet; kein Aufrufer räumt selbst auf."
    befehl: "cd resources/planner/hausplaner && grep -rn 'setWandStart(null)' --include=*.ts --include=*.tsx app/ | grep -v werkzeugEnde | wc -l"
    erwartet: "0"
    gegenbeweis: >
      Grep zählt Gestalt, nicht Sache (VORLAGE-Regel 6). Zusätzlich: den Rückfall-Effekt
      auslösen (Werkzeug wand aktiv, halb gezogen, in die 3D-Ansicht wechseln) und prüfen,
      dass wandStart danach null ist. Fängt der Test das nicht, ist K-01 wertlos.

  - id: K-02
    aussage: "Der Rückfall auf auswahl in HausplanerApp.tsx läuft über denselben Ort wie die Leiste."
    befehl: "cd resources/planner/hausplaner && grep -c \"setActiveTool('auswahl')\" app/HausplanerApp.tsx"
    erwartet: "0"
    gegenbeweis: >
      Eine Umbenennung würde den Zähler auch auf 0 bringen, ohne dass sich etwas ändert.
      Deshalb: den Aufruf im Rückfall-Effekt bewusst wieder auf den alten direkten Weg
      setzen und zeigen, dass der neue Test dabei ROT wird.

  - id: K-03
    aussage: "Die Bühne behandelt das Verlassen des Zeigers."
    befehl: "cd resources/planner/hausplaner && grep -c 'onMouseLeave' app/rahmen/Buehne.tsx"
    erwartet: "1"
    gegenbeweis: >
      Handler anhängen und leer lassen erfüllt den Zähler. Der Beweis ist K-04, nicht dieser.

  - id: K-04
    aussage: "Zeiger draußen heißt: keine Vorschaugeometrie wird gezeichnet, Startpunkt bleibt."
    befehl: "cd resources/planner/hausplaner && node --test __tests__/werkzeugEnde.test.ts 2>&1 | tail -3"
    erwartet: "pass"
    gegenbeweis: >
      Mutation, vor den Tests gefahren (VORLAGE-Regel 5): in werkzeugEnde.ts die Bedingung
      für 'zeigerDrinnen' auf true festnageln. Mindestens ein Test muss fallen. Fällt keiner,
      prüft die Zusage die Gestalt und nicht die Funktion.

  - id: K-05
    aussage: "Der Zustand ist für den Nutzer benannt, nicht nur intern."
    befehl: "cd resources/planner/hausplaner && grep -rn 'Zeichnung pausiert' app/ | wc -l"
    erwartet: "1"
    gegenbeweis: >
      Der Text muss beim pausierten Zustand ERSCHEINEN, nicht nur im Quelltext stehen.
      Im Browsertest ablesen und im Protokoll notieren, in welchem Element er auftaucht.

  - id: K-06
    aussage: "Escape verhält sich unverändert — der bestehende Weg wird nicht doppelt gebaut."
    befehl: "cd resources/planner/hausplaner && grep -c 'useEscapeEbene' app/HausplanerApp.tsx"
    erwartet: "5"
    gegenbeweis: >
      Selbst nachgezählt (VORLAGE-Regel 4) — und dabei korrigiert: der Zähler steht heute auf
      5, nicht auf 4. Es sind vier Ebenen (Z. 314, 315, 770, 782) PLUS die Import-Zeile 58,
      die der Zähler mitzählt. Mein erster Wert war die Zahl der Ebenen, nicht die des Befehls.
      Wird eine fünfte Ebene angelegt, ist der Escape-Weg zweimal da — genau die verwaiste
      zweite Wahrheit, gegen die dieser Auftrag antritt.

  - id: L-01
    aussage: "Browsertest gefahren, an http://ticket.test, nicht an 127.0.0.1:8000."
    befehl: "cd resources/planner/hausplaner && ls ../../../docs/browsertest-z01-*.md | wc -l"
    erwartet: "1"
    gegenbeweis: >
      Protokoll ohne Beobachtung ist eine Behauptung. Es muss die drei Pflicht-Viewports
      (1440 / 1024 / 375) nennen, Schritt 0 beantworten und Yamas E2E-Fall wörtlich abfahren:
      Wandzeichnung starten -> Maus aus Canvas zur Toolbar -> Fensterwerkzeug anklicken ->
      kein Reststrich sichtbar -> Fensterwerkzeug aktiv -> alter Preview-State leer.
```

## Vorbehalt / Reihenfolge

Dieser Auftrag macht den Fang **nicht** besser — die 150-mm-Festverdrahtung und der
ungenutzte `fangKern` bleiben, wie sie sind (B-1, B-2). Das ist Z-02 und hängt an dieser
Scheibe, weil ein Fangzustand erst dann sauber gelöscht werden kann, wenn es einen Ort gibt,
der löscht.

Er baut auch **nicht** die zwölfmethodige Lifecycle-Schnittstelle aus Yamas Abschnitt 8.
Wenn Z-05 (Polygonwerkzeug) ein drittes Werkzeug mit Zwischenzustand bringt, wird aus
`werkzeugEnde.ts` das Interface — dann getragen von drei Nutzern statt von keinem.

**Bindend:** Der Generator meldet „umgesetzt", nie „grün". Die Abnahme fährt der Evaluator.
