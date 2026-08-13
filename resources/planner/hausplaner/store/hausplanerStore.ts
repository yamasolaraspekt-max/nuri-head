/**
 * Hausplaner — zentraler Store (P0, zustand + Immer). EINE Wahrheit für die Szene;
 * 2D- und (später) 3D-Renderer LESEN den Store, Änderungen laufen NUR über
 * executeCommand (Immer produceWithPatches ⇒ Undo/Redo über inverse Patches).
 *
 * Entscheide aus dem Schema-Review:
 * - setActiveLevel LEERT die Selektion (b — nie Geister-Selektion auf unsichtbaren Nodes).
 * - CommandAbgelehnt lässt die Szene unverändert und legt die Meldung in letzteAblehnung.
 * Speichern: PUT (web-Route, CSRF) mit base_revision; 409 ⇒ konflikt-Status, kein stiller Verlust.
 */
import { create } from 'zustand';
import { enablePatches, produceWithPatches, applyPatches, type Patch } from 'immer';
import type { SceneDocument } from '../domain/scene.types';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';
import { applyCommand } from '../commands/applyCommand';
import { Historie } from './history';

enablePatches();

export type HausplanerModus = '2d' | 'split' | '3d';
export type SpeicherStatus = 'gespeichert' | 'ungespeichert' | 'speichert' | 'konflikt' | 'fehler';

export interface HausplanerState {
  scene: SceneDocument | null;
  speichernUrl: string | null;
  csrfToken: string | null;

  modus: HausplanerModus;
  activeLevelId: string | null;
  selectedNodeIds: string[];
  /**
   * AUF-35a: das **Primärobjekt** der Auswahl — dessen Eigenschaften zeigt das Panel, und nur an
   * ihm hängen die Griffe. Additiv ergänzt; `selectedNodeIds` bleibt die Liste, hier steht nur,
   * welches davon führt. Kein zweiter Auswahlzustand.
   */
  primaerId: string | null;
  /** AUF-35a: das Objekt unter dem Zeiger (Hover-Vorschau). Reine Anzeige, nie im Dokument. */
  ueberfahrenId: string | null;

  speicherStatus: SpeicherStatus;
  konfliktRevision: number | null;
  letzteAblehnung: string | null;

  init: (scene: SceneDocument, speichernUrl: string, csrfToken: string) => void;
  setModus: (modus: HausplanerModus) => void;
  setActiveLevel: (levelId: string) => void;
  /** Setzt die Auswahl. Ohne `primaerId` rückt das **zuletzt** gewählte nach (Kante 3). */
  selectNodes: (ids: string[], primaerId?: string | null) => void;
  setUeberfahren: (id: string | null) => void;

  executeCommand: (command: HausplanerCommand) => boolean;
  /**
   * A-31 — **eine zusammengehörige Änderung ist EIN Undo-Schritt.** Führt die ganze Liste in
   * **einem** `produceWithPatches` aus und schreibt **einen** Historien-Eintrag.
   *
   * **Alles oder nichts:** lehnt ein Befehl mitten in der Liste ab, wird der ganze Draft verworfen
   * — die Szene bleibt unverändert, `letzteAblehnung` trägt die Meldung, Rückgabe `false`. *Das
   * kostet nichts extra: `produceWithPatches` verwirft den Draft ohnehin, es gibt keinen
   * Zwischenzustand, den man aufräumen müsste.* **Und die Alternative wäre ein Zustand, den keine
   * Zusage beschreiben kann** — „N−1 von N Wänden gespiegelt" ist keine Aussage.
   *
   * Eine **leere** Liste tut nichts und meldet `true`: kein Historien-Eintrag, keine Szene-Änderung.
   * *Sonst hinterließe eine Operation ohne Auswahl einen Undo-Schritt, der nichts zurücknimmt.*
   */
  executeCommands: (commands: HausplanerCommand[]) => boolean;
  undo: () => void;
  redo: () => void;
  kannUndo: () => boolean;
  kannRedo: () => boolean;
  istDirty: () => boolean;

  save: () => Promise<void>;
}

const historie = new Historie();

function wendePatchesAn(scene: SceneDocument, patches: Patch[]): SceneDocument {
  return applyPatches(scene, patches) as SceneDocument;
}

/**
 * Beschriftung des Historien-Eintrags. Ein einzelner Befehl behält seinen Typ als Text — genau wie
 * vor A-31, damit die Umstellung an der Beschriftung nichts ändert. Mehrere gleichartige Befehle
 * werden gezählt, gemischte aufgezählt.
 */
function beschreibe(commands: HausplanerCommand[]): string {
  if (commands.length === 1) {
    return commands[0].type;
  }
  const arten = [...new Set(commands.map((c) => c.type))];

  return arten.length === 1 ? `${commands.length}× ${arten[0]}` : arten.join(' + ');
}

export const useHausplanerStore = create<HausplanerState>((set, get) => ({
  scene: null,
  speichernUrl: null,
  csrfToken: null,

  modus: '2d',
  activeLevelId: null,
  selectedNodeIds: [],
  primaerId: null,
  ueberfahrenId: null,

  speicherStatus: 'gespeichert',
  konfliktRevision: null,
  letzteAblehnung: null,

  init: (scene, speichernUrl, csrfToken) => {
    historie.markiereGespeichert();
    set({
      scene,
      speichernUrl,
      csrfToken,
      activeLevelId: scene.levels[0]?.id ?? null,
      selectedNodeIds: [],
      primaerId: null,
      ueberfahrenId: null,
      speicherStatus: 'gespeichert',
      konfliktRevision: null,
      letzteAblehnung: null,
    });
  },

  setModus: (modus) => set({ modus }),

  // Entscheid (b): Geschosswechsel leert die Selektion — deterministisch, nie versteckte Treffer.
  // Entscheid (b) gilt weiter, jetzt inklusive Primärobjekt: eine Auswahl, die das Geschoss
  // verlässt, hinterlässt auch kein führendes Objekt (Kante 2).
  setActiveLevel: (levelId) => set({ activeLevelId: levelId, selectedNodeIds: [], primaerId: null }),

  selectNodes: (ids, primaerId) => set({
    selectedNodeIds: ids,
    // Ohne ausdrückliche Angabe führt das zuletzt gewählte Objekt — es bildet ab, woran zuletzt
    // gearbeitet wurde. Leere Auswahl ⇒ kein Primärobjekt.
    primaerId: primaerId !== undefined ? primaerId : (ids.length > 0 ? ids[ids.length - 1] : null),
  }),

  setUeberfahren: (id) => set({ ueberfahrenId: id }),

  // Ein Befehl ist der Sonderfall einer Liste mit einem Eintrag — NICHT eine zweite Fassung
  // derselben Logik. Zwei Ausführungswege wären zwei Wahrheiten, und die eine bekäme irgendwann
  // eine Korrektur, die der anderen fehlt.
  executeCommand: (command) => get().executeCommands([command]),

  executeCommands: (commands) => {
    const { scene } = get();
    if (!scene) {
      return false;
    }
    if (commands.length === 0) {
      return true; // nichts zu tun — und deshalb auch kein Undo-Schritt
    }
    try {
      const jetzt = new Date().toISOString();
      const [neueScene, patches, inversePatches] = produceWithPatches(scene, (draft) => {
        // ALLE Befehle in EINEM Draft: wirft einer, verwirft Immer den ganzen Entwurf.
        for (const command of commands) {
          applyCommand(draft, command, jetzt);
        }
      });
      historie.push({ patches, inversePatches, beschreibung: beschreibe(commands) });
      set({
        scene: neueScene as SceneDocument,
        speicherStatus: 'ungespeichert',
        letzteAblehnung: null,
      });

      return true;
    } catch (fehler) {
      if (fehler instanceof CommandAbgelehnt) {
        set({ letzteAblehnung: fehler.message }); // Szene unverändert — definierter Fehlerzustand
        return false;
      }
      throw fehler;
    }
  },

  undo: () => {
    const { scene } = get();
    const eintrag = historie.undo();
    if (!scene || !eintrag) {
      return;
    }
    set({
      scene: wendePatchesAn(scene, eintrag.inversePatches),
      speicherStatus: historie.istDirty() ? 'ungespeichert' : 'gespeichert',
    });
  },

  redo: () => {
    const { scene } = get();
    const eintrag = historie.redo();
    if (!scene || !eintrag) {
      return;
    }
    set({
      scene: wendePatchesAn(scene, eintrag.patches),
      speicherStatus: historie.istDirty() ? 'ungespeichert' : 'gespeichert',
    });
  },

  kannUndo: () => historie.kannUndo(),
  kannRedo: () => historie.kannRedo(),
  istDirty: () => historie.istDirty(),

  save: async () => {
    const { scene, speichernUrl, csrfToken } = get();
    if (!scene || !speichernUrl) {
      return;
    }
    set({ speicherStatus: 'speichert' });

    try {
      const antwort = await fetch(speichernUrl, {
        method: 'PUT',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
        },
        body: JSON.stringify({
          base_revision: scene.revision,
          schema_version: scene.schemaVersion,
          scene,
        }),
      });

      if (antwort.status === 409) {
        const daten = (await antwort.json()) as { aktuelle_revision?: number };
        set({ speicherStatus: 'konflikt', konfliktRevision: daten.aktuelle_revision ?? null });
        return;
      }
      if (!antwort.ok) {
        set({ speicherStatus: 'fehler' });
        return;
      }

      const daten = (await antwort.json()) as { revision: number };
      historie.markiereGespeichert(); // Historie bleibt erhalten (Kante „Undo über Save-Grenze")
      const aktualisiert = { ...get().scene!, revision: daten.revision };
      set({ scene: aktualisiert, speicherStatus: historie.istDirty() ? 'ungespeichert' : 'gespeichert', konfliktRevision: null });
    } catch {
      set({ speicherStatus: 'fehler' });
    }
  },
}));
