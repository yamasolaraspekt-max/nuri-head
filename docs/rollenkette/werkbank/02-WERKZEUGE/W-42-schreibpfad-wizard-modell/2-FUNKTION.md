# W-42 · Schreibpfad Wizard → Gebäudemodell — FUNKTION

## Die ZWEI WEGE — beide gebaut, und die Bedingung entscheidet

```text
Weg A  IM GEBAEUDE     Bedingung: eine Szene ist geladen
                       :174  if (art === 'heizkoerper' && scene) {
                       :172  const scene = store.scene;
                       -> executeCommand ADD_NODE (:184, :205, :226)

Weg B  STANDALONE      Bedingung: kein Gebaeude vorhanden
                       :244-247  new Blob([JSON.stringify(paket, …)])
                                 a.href = url; a.download = dateiname; a.click();
                       -> ConfiguratorPackage als JSON herunterladen
```

**Die Bedingung ist `&& scene`** — *dreimal, je Bauteilart.* **Ohne geladene Szene gibt es kein
Ziel, und dann fällt der Wizard auf den Download zurück.**

> **Wer nur den Download sieht, hält ihn für den Regelfall.** *Er ist der **Rückfall**.* **Genau
> diese Verwechslung steckt in beiden überholten Quellen — siehe `7-GRENZEN.md`.**

## Der Weg ins Gebäude — was dabei entsteht

```ts
:178-183   const radiator: ObjectNode = {
             id, type: 'object', objectType: 'radiator', catalogItemId: 'radiator-default',
             levelId: level.id, visible: true, locked: false, tags: [],
             createdAt: jetzt, updatedAt: jetzt,
             transform: { position: { x: 2000, y: 500, z: 0 }, … },
             parameters: { 'objekt.typ': …, 'objekt.label': …, 'objekt.laenge': … },
           };
:184       const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
```

**Der Knoten wird vollständig gebaut und dann als EIN Kommando übergeben.** *Nicht als
Direktzugriff auf das Dokument — über `executeCommand`, also über den Weg, der Rückgängig
ermöglicht.*

**Das Geschoss kommt aus dem Store, nicht aus dem Wizard** (`:175`):

```ts
const levelId = store.activeLevelId ?? scene.levels[0]?.id ?? null;
```

> **Zwei Rückfallstufen in einer Zeile** — *aktives Geschoss, sonst das erste, sonst `null`.* **Das
> ist die Stelle, an der ein Bauteil landen würde, wenn kein Geschoss aktiv ist.**

## Eingabe und Ausgabe

| | Was | Woher / Wohin |
|---|---|---|
| **Eingabe** | die Auswahl des Konfigurators (`wahl`) | W-35 |
| | `store.scene`, `store.activeLevelId` | `hausplanerStore` |
| **Ausgabe A** | ein `SceneNode` im Dokument | `executeCommand({ type: 'ADD_NODE' })` |
| **Ausgabe B** | eine JSON-Datei beim Anwender | Blob + Download |
| **Rückmeldung** | `onÜbernehmen(nachricht)` | eine Zeichenkette, zwei Fälle je nach `ok` |

## Was `ok` bedeutet — und was daran offen ist

```ts
:184-185   const ok = store.executeCommand({ … });
           onÜbernehmen(ok ? `… ins Modell gesetzt — im Plan verschiebbar.` : `…`);
```

**Die Meldung unterscheidet zwei Fälle.** *Was bei `ok === false` mit einem halb gebauten Zustand
geschieht, ist **nicht gemessen** — siehe `7-GRENZEN.md`.*

## Schichtzuordnung

| Schicht | W-42 | Beleg |
|---|---|---|
| 1 Domäne | **schreibt** | `ADD_NODE` erzeugt einen `SceneNode` im Dokument |
| 2 Geometrie | **nein** | keine Rechnung außer festen Startwerten |
| 3 Anwendung | **ja** | `app/ConfigWizard.tsx`, der Übernehmen-Zweig |
| 4/5 Oberfläche | **mittelbar** | die Meldung über `onÜbernehmen` |

## Scope — die Grenze zu W-35 ist die wichtigste

```text
W-42 IST      der SCHREIBPFAD: der Uebernehmen-Zweig, die drei ADD_NODE-Aufrufe,
              die Bedingung && scene, der Rueckfall auf den Download, und die
              Meldung die beide Faelle unterscheidet.

W-42 IST NICHT
              W-35 — der KONFIGURATOR: die fuenf Schritte, die Auswahl, die
              Oberflaeche des Dialogs. Beide leben in DERSELBEN Datei
              (app/ConfigWizard.tsx, 271 Z.), und das macht die Grenze noetig:
              W-35 ist alles bis zur Auswahl, W-42 ist was danach damit geschieht.

              app/state/paketSpeichern.ts — das Speichern eines ConfiguratorPackage
              an eine URL. Anderer Weg, anderes Ziel, eigenes Werkzeug oder keines.
```

> **Die Grenze verläuft INNERHALB einer Datei, nicht zwischen zweien.** *Das ist ungewöhnlich und
> muss deshalb hier stehen — wer `ConfigWizard.tsx` öffnet, sieht beide Werkzeuge nebeneinander.*
