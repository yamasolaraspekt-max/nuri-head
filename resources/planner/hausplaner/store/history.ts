/**
 * Hausplaner — Undo/Redo-Historie über Immer-Patches (P0). Reine Klasse (testbar ohne UI).
 * Kanten (Spec §4): Speichern leert die Historie NICHT; ein neues Command nach Undo
 * verwirft den Redo-Stapel; Undo nach Save ⇒ isDirty (über gespeicherten Index).
 */
import type { Patch } from 'immer';

export interface HistorienEintrag {
  patches: Patch[];
  inversePatches: Patch[];
  beschreibung: string;
}

export class Historie {
  private rueckgaengig: HistorienEintrag[] = [];
  private wiederholen: HistorienEintrag[] = [];
  /** Anzahl ausgeführter Commands seit Dokumentstart — Position im Verlauf. */
  private zaehler = 0;
  /** Verlaufs-Position beim letzten erfolgreichen Speichern. */
  private gespeichertBei = 0;

  push(eintrag: HistorienEintrag): void {
    this.rueckgaengig.push(eintrag);
    this.wiederholen = []; // neues Command nach Undo ⇒ Redo verworfen
    this.zaehler += 1;
  }

  undo(): HistorienEintrag | null {
    const eintrag = this.rueckgaengig.pop();
    if (!eintrag) {
      return null;
    }
    this.wiederholen.push(eintrag);
    this.zaehler -= 1;

    return eintrag;
  }

  redo(): HistorienEintrag | null {
    const eintrag = this.wiederholen.pop();
    if (!eintrag) {
      return null;
    }
    this.rueckgaengig.push(eintrag);
    this.zaehler += 1;

    return eintrag;
  }

  markiereGespeichert(): void {
    this.gespeichertBei = this.zaehler; // Historie bleibt VOLL erhalten (Kante „Undo über Save-Grenze")
  }

  istDirty(): boolean {
    return this.zaehler !== this.gespeichertBei;
  }

  kannUndo(): boolean {
    return this.rueckgaengig.length > 0;
  }

  kannRedo(): boolean {
    return this.wiederholen.length > 0;
  }
}
