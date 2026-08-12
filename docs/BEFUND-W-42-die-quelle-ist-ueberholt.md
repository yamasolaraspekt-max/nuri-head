# Befund zu W-42 — die Quelle sagt „schreibt NICHTS", der Code schreibt dreimal

```yaml
auftrag: "W-42"
rolle: "generator"
art: "BEFUND vor dem Ziehen, read-only — nichts geaendert, nichts gezogen"
gemessen_am: "12.08.2026"
stand: fb399e32
warum_nicht_gezogen: "docs/STATUS.md traegt 24 Zeilen Fremdarbeit; ich sammle sie nicht ein."
```

> **Die Abweichung des Planners von Yamas Freigabe trägt — und sie trägt stärker, als sein eigenes
> Blatt sagt.** *Ich habe sie gegengelesen, wie beim Ziehen zugesagt, und dabei einen Befund gegen
> die QUELLE gefunden.*

## Was die Quelle sagt

**`docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md:184-185`, am Bau-Stand gelesen:**

> *„`ConfigWizard` 271 Z, Fenster/Tür/Treppe, 5 Schritte — **schreibt NICHTS ins BuildingDocument,
> lädt JSON herunter**. Schreibpfad ist als ‚nächste Scheibe' benannt."*

## Was der Code tut

```text
resources/planner/hausplaner/app/ConfigWizard.tsx   271 Zeilen (die Zahl stimmt)

:184  const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
:205  const ok = store.executeCommand({ type: 'ADD_NODE', node: treppe   as SceneNode });
:226  const ok = store.executeCommand({ type: 'ADD_NODE', node: knoten   as SceneNode });

grep -c 'executeCommand'   ->  3
```

**Der Wizard schreibt ins Gebäudemodell — dreimal, für Heizkörper, Treppe und Fenster/Tür.** *Und
die Meldungen sagen die Wahrheit:* `„Heizkörper „…" ins Modell gesetzt — im Plan verschiebbar."`

**Der JSON-Download in `:244-247` ist der RÜCKFALL**, *nicht der Regelfall* — jeder der drei
Schreibpfade steht hinter einer Bedingung wie `if (art === 'heizkoerper' && scene)`. **Ohne
geladene Szene gibt es kein Ziel, und dann wird heruntergeladen.**

## Was daraus folgt

| | |
|---|---|
| **Die Abweichung des Planners ist richtig** | *ein `ENTWORFEN`-Blatt hätte vorgegeben, was gebaut ist — und die nächste Rolle hätte einen **zweiten** Schreibpfad angelegt* |
| **Die Quelle ist an dieser Stelle überholt** | *sie sagt „schreibt NICHTS"; gemessen sind es drei Schreibpfade* |
| **Das betrifft nicht nur W-42** | *dieselbe Quelle trägt W-40 und W-41 — bei W-40 war ihre Abwesenheitsaussage bereits falsch (`outdated` existiert), hier ist es die zweite* |

> **Zweimal dieselbe Klasse in derselben Quelle: eine Abwesenheit behauptet, die es nicht gibt.**
> *Der Verfasser hat vier Punkte ausdrücklich als „nicht gemessen" gekennzeichnet — **diese beiden
> gehörten nicht dazu**, sie standen als Ergebnis da.* **Das ist keine Nachlässigkeit, sondern der
> Grund, warum Pflichtprüfung 8 „am Bau-Stand messen statt übernehmen" verlangt.**

## Ein Nebenbefund zum Blatt selbst

```text
W-42s grundlage nennt   state/paketSpeichern.ts
tatsaechlich liegt sie  app/state/paketSpeichern.ts     (64 Zeilen)
```

**Der `app/`-Teil fehlt im Pfad.** *Kein Mangel der Sache — meine erste Messung lief ins Leere, bis
ich gesucht statt geglaubt habe.* **Genannt, damit es beim Bauen niemanden aufhält.**

## Was ich NICHT getan habe

```text
NICHT gezogen     docs/STATUS.md traegt 24 Zeilen Fremdarbeit; sie einzusammeln
                  verbietet die stehende Auflage.
NICHT geaendert   weder W-42s Blatt noch die Quelle. Ob der ueberholte Satz in
                  BERICHT-PROZESSEBENE-DREI-FRAGEN.md berichtigt wird, gehoert dem
                  Planner — die Datei ist seine Vorlage an Yama.
NICHT entschieden ob Yamas Freigabe damit erfuellt oder abgewichen ist. Der Planner
                  hat die Abweichung selbst benannt und begruendet; ich bestaetige
                  nur ihre TATSACHENGRUNDLAGE.
```

> **Mein Beitrag ist eine Messung, keine Entscheidung:** *der Code schreibt, die Quelle sagt das
> Gegenteil, und die Abweichung des Planners steht damit auf festem Grund.*
