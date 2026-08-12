# W-42 — Schreibpfad Wizard → Gebäudemodell. Er ist gebaut, und zwei Quellen sagen das Gegenteil

```yaml
auftrag: "W-42"
werkzeug: "W-42 Schreibpfad Wizard → Gebäudemodell"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (ABLESUNG). ABWEICHUNG VON YAMAS FREIGABE, siehe
      Abschnitt 1: er hat W-42 als VORGABE mit Ziel ENTWORFEN freigegeben, der Code EXISTIERT aber."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08. für W-40, W-41 und W-42 als Vorgabe. Beim Messen des Operanden
         (Pflichtprüfung 5) hat sich gezeigt, dass W-42s Gegenstand gebaut ist — und dass zwei
         Quellen im Repo das Gegenteil behaupten."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/ConfigWizard.tsx (271 Z.) als Quelle ·
            geometry/configuratorPackage.ts · state/paketSpeichern.ts · store/hausplanerStore"
```

## 1 — Ich weiche von der Freigabe ab, und hier ist der Grund

**Yama hat freigegeben:** *„W-40, W-41, W-42 warten auf dich — das Angebot des Planners, sie als
Vorgabe mit Ziel `ENTWORFEN` zu schneiden."* **Für W-40 und W-41 stimmt das. Für W-42 nicht.**

```text
Die Legende des Registers, woertlich:
  ENTWORFEN   die Blaetter GEBEN VOR, was gebaut werden soll.
              Fuer Werkzeuge, deren Code NOCH NICHT EXISTIERT (Klasse C).
```

> **W-42s Code existiert.** *Ein `ENTWORFEN`-Blatt darüber wäre nach der eigenen Legende falsch —
> und es wäre schlimmer als ein fehlendes Blatt: es würde vorgeben, was schon da ist, und die nächste
> Rolle würde einen zweiten Schreibpfad bauen. **Deshalb Ziel `BESCHRIEBEN`.** Die Abweichung ist
> gemeldet und nicht still vollzogen; wenn Yama sie anders will, gilt seine Fassung.*

## 2 — Der Befund: drei Schreibstellen, vier Bauteilarten

**Gemessen und jede Stelle geöffnet** (`app/ConfigWizard.tsx`):

```text
:171   const store = useHausplanerStore.getState();

:178-184   radiator: ObjectNode, objectType 'radiator'
           store.executeCommand({ type: 'ADD_NODE', node: radiator })
           Meldung: „Heizkoerper ins Modell gesetzt — im Plan verschiebbar"

:199-205   treppe: ObjectNode, objectType 'stair'
           store.executeCommand({ type: 'ADD_NODE', node: treppe })
           Meldung: „Treppe ins Modell gesetzt"

:219-226   knoten: OpeningNode, type art === 'fenster' ? 'window' : 'door'
           mit hostWallId, offsetFromWallStart, width, height, sillHeight
           store.executeCommand({ type: 'ADD_NODE', node: knoten })
           Meldung: „… auf die gewaehlte Wand gesetzt."
```

**Vier Bauteilarten, nicht drei** — *die dritte Stelle deckt Fenster **und** Tür über einen
Ausdruck ab.*

## 3 — Zwei Quellen im Repo sagen das Gegenteil, und beide aus demselben Grund

```text
ConfigWizard.tsx:5-6, der EIGENE Dateikopf:
  „Uebernehmen erzeugt ein echtes autarkes ConfiguratorPackage und laedt es als JSON
   herunter. Der Schreibpfad ins Gebaeudemodell (Command) BLEIBT DIE NAECHSTE SCHEIBE."

docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md:
  „ConfigWizard 271 Z … schreibt NICHTS ins BuildingDocument, laedt JSON herunter.
   Schreibpfad ist als naechste Scheibe benannt."
```

> **Beide messen die falsche Schreibweise.** *`BuildingDocument` kommt in der Datei **0 Mal** vor —
> der Schreibpfad heißt `useHausplanerStore.getState().executeCommand({ type: 'ADD_NODE' })` und die
> Knoten sind `SceneNode`/`ObjectNode`/`OpeningNode`. **Wer auf `BuildingDocument` sucht, findet
> nichts und schließt daraus auf Abwesenheit.** Das ist H-9, und es ist derselbe Fehler, der heute
> dreimal an einem einzigen Feldnamen passiert ist. **Der Dateikopf ist überholt**, nicht falsch
> gemeint: die Scheibe ist inzwischen gebaut, der Kommentar wurde nicht nachgezogen.*

**Das Blatt muss beide Stellen benennen** — *nicht um sie zu korrigieren (der Dateikopf gehört zum
Code, der Bericht ist ein datiertes Protokoll), sondern damit die nächste Rolle nicht auf sie
baut. **Ein überholter Kommentar an der Quelle ist gefährlicher als eine Lücke:** er sieht wie eine
Aussage über den Bestand aus.*

## 4 — Was WIRKLICH offen ist

```text
ZWEI WEGE, beide gebaut:
  standalone   kein Gebaeude vorhanden -> ConfiguratorPackage als JSON herunterladen
               (:74 „Autark — kein Gebaeude noetig", :146 kannPaketSpeichern)
  im Gebaeude  executeCommand ADD_NODE (die drei Stellen aus Abschnitt 2)

NICHT GEMESSEN und darum als GRENZE zu benennen:
  — was passiert, wenn executeCommand fehlschlaegt (ok ist false): die Meldung
    unterscheidet zwei Faelle, aber ob etwas zurueckgerollt wird, ist ungeprueft
  — ob das ConfiguratorPackage und der ADD_NODE-Weg DASSELBE Bauteil ergeben
    (zwei Wege, ein Ergebnis?) — das ist die Frage nach der zweiten Wahrheit
  — der ConfigWizard-Test: die Quelle fuehrt ihn unter NICHT GEMESSEN
```

## 5 — Scope

```text
W-42 IST      der Schreibpfad in ConfigWizard.tsx: die drei executeCommand-Stellen,
              die vier Bauteilarten, der standalone-Zweig, und die Abgrenzung
              zwischen beiden Wegen.

W-42 IST NICHT
              der KONFIGURATOR selbst (Schritte, Bauarten, Vorschau) -> W-35.
              der Store und executeCommand -> hausplanerStore, eigenes Werkzeug.
              configuratorPackage.ts und paketSpeichern.ts — sie werden BENUTZT,
              mit Fundstelle genannt, aber nicht beschrieben.
              KEINE Aenderung am Code, auch nicht am ueberholten Dateikopf: ein Blatt
              beschreibt, es berichtigt keine Kommentare. Der Befund gehoert gemeldet.
```

## 6 — Abnahmekriterien

```text
W-42-1  (P1, TRAGEND) 1-ZWECK stellt fest, dass der Schreibpfad GEBAUT ist, mit den
        drei executeCommand-Stellen und den vier Bauteilarten. Fundstellen am Bau-Stand
        nennen, nicht aus diesem Blatt uebernehmen.
W-42-2  (P1) 7-GRENZEN nennt BEIDE ueberholten Quellen woertlich: den eigenen Dateikopf
        („bleibt die naechste Scheibe") und die Berichtsaussage („schreibt NICHTS ins
        BuildingDocument"), und die gemeinsame Ursache — BuildingDocument kommt 0 Mal
        vor, der Pfad heisst anders. Ohne diesen Abschnitt baut die naechste Rolle einen
        zweiten Schreibpfad.
W-42-3  (P1) Die ZWEI WEGE sind unterschieden: standalone mit JSON-Download und der
        Weg ins Gebaeude. Je Weg die Bedingung, unter der er greift, mit Fundstelle.
W-42-4  7-GRENZEN nennt die drei ungemessenen Punkte aus Abschnitt 4, darunter
        ausdruecklich die Frage, ob beide Wege dasselbe Bauteil ergeben. Das ist die
        Frage nach einer zweiten Wahrheit und sie wird GESTELLT, nicht beantwortet.
W-42-5  Die Scope-Grenze zu W-35 steht in 2-FUNKTION: der Konfigurator gehoert dort,
        der Schreibpfad hier.
W-42-6  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit mindestens einer geöffneten Stelle** (Pflichtprüfung 7) — *Abschnitt 3 ist der
Grund: ein Suchmuster auf den falschen Namen hat hier zwei Quellen in die Irre geführt.*

```yaml
warum_BESCHRIEBEN_trotz_der_Freigabe_ENTWORFEN: "Der Code existiert, und die Legende reserviert
        ENTWORFEN fuer Werkzeuge OHNE Code. Ein Vorgabe-Blatt haette vorgegeben, was schon gebaut
        ist, und die naechste Rolle haette einen zweiten Schreibpfad angelegt. Die Abweichung ist
        gemeldet, nicht still vollzogen — will Yama es anders, gilt seine Fassung."
was_dieser_auftrag_fuer_yama_hergibt: "Eine der drei Luecken aus Abschnitt 7 der Vorlage ist keine.
        Der Wizard schreibt Fenster, Tuer, Treppe und Heizkoerper ins Modell. Die Aussage im Bericht
        stammt aus einer Messung auf BuildingDocument, und dieses Wort kommt in der Datei nicht vor."
warum_P1: "Zwei Quellen im Repo behaupten das Gegenteil des Bestands, eine davon ist der Dateikopf
        selbst. Solange das so steht, ist jede Planung darauf falsch — und ein ueberholter Kommentar
        an der Quelle wirkt wie eine Messung."
W_42_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
