# W-42 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 3 Anwendung | `resources/planner/hausplaner/app/ConfigWizard.tsx` **(271 Z.)** | **der Übernehmen-Zweig** — W-42s ganzer Bestand |
| 1 Domäne | `domain/scene.types` | `SceneNode`, `ObjectNode`, `OpeningNode`, `WallNode` — nur als Typ |
| Store | `store/hausplanerStore` | `scene`, `activeLevelId`, `executeCommand` |
| *(daneben)* | `app/state/paketSpeichern.ts` (64 Z.) | **anderer Weg**: ein Paket an eine URL — nicht W-42 |

> **W-42 teilt sich seine Datei mit W-35.** *Die Grenze verläuft innerhalb von `ConfigWizard.tsx`:
> alles bis zur Auswahl ist der Konfigurator, was danach damit geschieht ist der Schreibpfad.*

## Die drei Schreibstellen

```ts
:184   const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
:205   const ok = store.executeCommand({ type: 'ADD_NODE', node: treppe   as SceneNode });
:226   const ok = store.executeCommand({ type: 'ADD_NODE', node: knoten   as SceneNode });
```

**Drei Aufrufe, vier Bauteilarten** — *`:226` trägt Fenster und Tür.*

## Kernstelle

```ts
// :172-185 — die ganze Bauart in dreizehn Zeilen
const scene = store.scene;
if (art === 'heizkoerper' && scene) {
  const levelId = store.activeLevelId ?? scene.levels[0]?.id ?? null;
  …
  const radiator: ObjectNode = { id, type: 'object', objectType: 'radiator', …
    transform: { position: { x: 2000, y: 500, z: 0 }, … },
    parameters: { 'objekt.typ': wahl.id, 'objekt.label': wahl.label, … } };
  const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
  onÜbernehmen(ok ? `Heizkörper „${wahl.label}" ins Modell gesetzt — im Plan verschiebbar.` : …);
  return;
}
```

**Die Kernstelle ist `&& scene`, nicht `executeCommand`.** *Das Kommando ist gewöhnlich; die
Bedingung entscheidet, welcher der beiden Wege greift — und genau sie haben zwei Quellen
übersehen.*

## Warum zwei Quellen den Pfad nicht gefunden haben

```text
BuildingDocument  in ConfigWizard.tsx   0 Treffer
SceneDocument     in ConfigWizard.tsx   0 Treffer
```

> **Der Schreibpfad nennt den Dokumenttyp GAR NICHT.** *Er nennt den **Store** und das
> **Kommando**:* `store.executeCommand({ type: 'ADD_NODE' })`. **Wer nach dem Dokument sucht, findet
> nichts — und schließt daraus, es werde nicht geschrieben.**
>
> **Das ist H-9 in seiner teuersten Form:** *ein Muster, das eine Schreibweise voraussetzt, misst
> die Schreibweise und nicht die Sache. Hier hat es **zwei** Quellen in die Irre geführt und wäre
> um ein Haar in ein drittes Blatt gewandert.*

## Schnittstelle

```ts
// ConfigWizard.tsx:29 — W-42 gibt nach aussen genau EINE Zeichenkette zurueck
onÜbernehmen: (nachricht: string) => void;
```

**Mehr Schnittstelle hat der Schreibpfad nicht.** *Er meldet, was er getan hat — und der Aufrufer
zeigt es als Toast (`HausplanerStudio.tsx`, W-39).*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-35** `ConfigWizard` | die Auswahl, die übernommen wird | **dieselbe Datei** — Grenze in `2-FUNKTION` |
| `store/hausplanerStore` | `scene`, `activeLevelId`, `executeCommand` | **ja** |
| `domain/scene.types` | die Knotentypen | **ja**, reine Typ-Importe |
| **W-39** `HausplanerStudio` | *umgekehrt:* zeigt die Meldung als Toast | — |
