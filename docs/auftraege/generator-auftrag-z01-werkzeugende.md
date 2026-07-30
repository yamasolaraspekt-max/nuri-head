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

*Kopf nach `AUFTRAGSSCHEMA §7` — maschinell lesbar durch `node scripts/auftrag-pruefen.mjs`.
**Warum die Form sich ändert:** der Validator aus AUF-87 fand in ALLEN fünf bereitliegenden
Blättern „KEIN PRUEFBEFEHL im Kopf". Das Werkzeug ist gut, die Blätter fütterten es nur nicht.*

```yaml
scope:
  datei: resources/planner/hausplaner/app/rahmen/Buehne.tsx
  population_command: "grep -rc 'setWandStart(null)' resources/planner/hausplaner/app/HausplanerApp.tsx"
  ausschluesse:
    - stelle: "InteractiveTool mit zwoelf Methoden (Prompt-Abschnitt 8 vollstaendig)"
      grund: "Nur zwei Werkzeuge haben heute einen Zwischenzustand. Zwoelf Methoden fuer zwei Nutzer waeren ein Posten, der auf nichts zeigt."
      entschieden_von: planner
    - stelle: "Anschluss von geometry/fangKern.ts"
      grund: "Eigene Scheibe Z-02; ein Fangzustand kann erst geloescht werden, wenn es einen Ort gibt, der loescht."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Es gibt genau einen Ort, der ein Werkzeug beendet; kein Aufrufer raeumt selbst auf."
    pruefung:
      befehl: "grep -rn 'setWandStart(null)' --include=*.ts --include=*.tsx resources/planner/hausplaner/app/ | grep -v werkzeugEnde | wc -l"
      erwartet: "0"
    ausgangswert: "5 (HausplanerApp 494, 778, 829 - GruppenzeileUndSchiene 334, 354)"
    gegenbeweis: >
      Grep zaehlt Gestalt, nicht Sache. Zusaetzlich den Rueckfall ausloesen - Werkzeug wand
      aktiv, halb gezogen, in die 3D-Ansicht wechseln - und pruefen, dass wandStart danach
      null ist. Faengt der Test das nicht, ist K-01 wertlos.

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Der Rueckfall auf auswahl laeuft ueber denselben Ort wie die Leiste."
    pruefung:
      befehl: "grep -o \"setActiveTool('auswahl')\" resources/planner/hausplaner/app/HausplanerApp.tsx | wc -l"
      erwartet: "0"
    ausgangswert: "1 (Zeile 371, der Rueckfall-Effekt)"
    gegenbeweis: >
      Eine Umbenennung braechte den Zaehler auch auf 0. Deshalb: den Aufruf bewusst wieder auf
      den alten direkten Weg setzen und zeigen, dass der neue Test dabei ROT wird.

  - id: K-03
    typ: presence
    aussage: "Die Buehne behandelt das Verlassen des Zeigers."
    pruefung:
      befehl: "grep -o 'onMouseLeave' resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l"
      erwartet: "1"
    ausgangswert: "0"
    gegenbeweis: >
      Einen leeren Handler anzuhaengen erfuellt den Zaehler. Der Beweis ist K-04, nicht dieser.

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Zeiger draussen heisst: keine Vorschaugeometrie, Startpunkt bleibt."
    pruefung:
      befehl: "cd resources/planner/hausplaner && node --test __tests__/werkzeugEnde.test.ts | tail -4"
      erwartet: "pass, 0 fail"
    hinweis: >
      Der Validator meldet hier heute "exit 0, aber KEINE Ausgabe" - richtig, denn die
      Testdatei entsteht erst in diesem Auftrag. Nach dem Bau muss die Meldung verschwinden.
      Bleibt sie, laeuft der Test nicht, und K-04 ist eine Hoffnung.
    gegenbeweis: >
      Mutation VOR den Tests: in werkzeugEnde.ts die Bedingung fuer zeigerDrinnen auf true
      festnageln. Mindestens ein Test muss fallen. Faellt keiner, prueft die Zusage die Gestalt
      und nicht die Funktion.

  - id: K-05
    typ: presence
    aussage: "Der pausierte Zustand ist fuer den Nutzer benannt, nicht nur intern."
    pruefung:
      befehl: "grep -rn 'Zeichnung pausiert' resources/planner/hausplaner/app/ | wc -l"
      erwartet: "1"
    ausgangswert: "0"
    gegenbeweis: >
      Der Text muss ERSCHEINEN, nicht nur im Quelltext stehen. Im Browsertest ablesen und
      notieren, in welchem Element er auftaucht.

  - id: K-06
    typ: presence
    aussage: "Der bestehende Escape-Weg wird benutzt, nicht ein zweiter gebaut."
    pruefung:
      befehl: "grep -o 'useEscapeEbene' resources/planner/hausplaner/app/HausplanerApp.tsx | wc -l"
      erwartet: "5"
    ausgangswert: "5 - VIER Ebenen (Z. 314, 315, 770, 782) PLUS die Import-Zeile 58, die der Zaehler mitzaehlt. Selbst nachgemessen und dabei meinen ersten Wert 4 korrigiert."
    gegenbeweis: >
      Wird eine fuenfte Ebene angelegt, steigt der Zaehler auf 6 - dann ist der Escape-Weg
      zweimal da, genau die verwaiste zweite Wahrheit, gegen die dieser Auftrag antritt.

  - id: L-01
    typ: presence
    aussage: "Browsertest gefahren, an http://ticket.test, nicht an 127.0.0.1:8000."
    pruefung:
      befehl: "find docs -maxdepth 1 -name 'browsertest-z01-*.md' | wc -l"
      erwartet: "1"
    gegenbeweis: >
      Ein Protokoll ohne Beobachtung ist eine Behauptung. Es nennt die drei Pflicht-Viewports
      1440 / 1024 / 375, beantwortet Schritt 0 und faehrt Yamas E2E-Fall woertlich ab:
      Wandzeichnung starten, Maus aus dem Canvas zur Toolbar, Fensterwerkzeug anklicken,
      kein Reststrich sichtbar, Fensterwerkzeug aktiv, alter Preview-State leer.
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
