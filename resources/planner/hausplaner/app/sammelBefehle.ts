/**
 * A-31 — **die Befehlslisten der Sammel-Operationen, rein und prüfbar.**
 *
 * ---
 *
 * **Warum es diese Datei gibt.** Fünf Operationen der Hauptansicht riefen `executeCommand` in einer
 * **Schleife** — und ein `executeCommand` ist **ein** Undo-Schritt. Ein Undo nach dem Spiegeln
 * drehte **eine** Wand zurück und ließ den Grundriss halb gespiegelt stehen: kein „teilweise
 * rückgängig", sondern ein Zustand, den es zeichnerisch nicht geben kann. Seit A-31 bauen die
 * Operationen **eine Liste** und übergeben sie an `executeCommands` — ein `produceWithPatches`, ein
 * Historien-Eintrag, alles oder nichts.
 *
 * **Warum die Listen HIER stehen und nicht in der Komponente.** *Eine Zusage, die nur den Text von
 * `HausplanerApp.tsx` liest, prüft die Schreibweise und nicht die Sache.* Hier sind es reine
 * Funktionen: eine Zusage kann sie aufrufen, das Ergebnis gegen den Store fahren und **rot werden**.
 * Dasselbe Muster wie `einpassen`, `wendeAuswahlAn` oder `dupliziereGeschoss`.
 *
 * **Keine dieser Funktionen fasst den Store an.** Sie bekommen Knoten und geben Befehle zurück.
 */
import type { HausplanerCommand } from '../domain/commands.types';
import type { Level, OpeningNode, RoofNode, SceneNode, WallNode } from '../domain/scene.types';
import { istOeffnung } from './reineHelfer';
import { versetzteWand, spiegelteWand, bbox as punkteBbox, achsenMitte, type Achse } from '../geometry/editierGeometrie';
import { wandLaenge } from '../geometry/wallGeometry';

/**
 * **Löschen: die Kaskade ist schon im Befehl, und deshalb darf sie nicht zweimal kommen.**
 *
 * `REMOVE_NODE` auf eine Wand entfernt **ihre Öffnungen mit** — ausdrücklich, damit EIN Undo beides
 * zurückbringt (`applyCommand.ts:168`). Sind Wand **und** ihr Fenster gemeinsam ausgewählt, wäre der
 * zweite Befehl ein `REMOVE_NODE` auf einen Knoten, den der erste bereits mitgenommen hat — und
 * `nodeOderFehler` lehnt ihn mit `node_unbekannt` ab.
 *
 * **Vor A-31 fiel das nicht auf:** jeder Aufruf fing seine Ablehnung selbst, die Schleife lief
 * weiter, unterm Strich war beides weg. **Unter „alles oder nichts" würde derselbe Fall die GANZE
 * Löschung verwerfen** — der Benutzer drückt Entf und es passiert nichts. *Deshalb fällt die
 * Öffnung hier heraus, bevor die Liste entsteht: dasselbe Ergebnis wie vorher, in einem Schritt.*
 */
export function befehleLoeschen(ids: readonly string[], nodes: readonly SceneNode[]): HausplanerCommand[] {
  const zuLoeschen = new Set(ids);
  const befehle: HausplanerCommand[] = [];
  for (const id of ids) {
    const n = nodes.find((x) => x.id === id);
    // Unbekannte id: unverändert durchreichen. Sie war auch vorher eine Ablehnung, und sie
    // stillschweigend zu schlucken hieße, einen Fehler zu verstecken statt ihn zu melden.
    if (n && istOeffnung(n) && zuLoeschen.has(n.hostWallId)) {
      continue; // die Wand nimmt sie ohnehin mit
    }
    befehle.push({ type: 'REMOVE_NODE', nodeId: id });
  }

  return befehle;
}

/** Ergebnis von {@link befehleDuplizieren} — die Liste und die IDs, die danach ausgewählt werden. */
export interface DuplikatBefehle {
  befehle: HausplanerCommand[];
  neueIds: string[];
}

/**
 * **Duplizieren.** Wände werden um 500/500 versetzt, Öffnungen entlang ihrer Wirtswand weitergerückt
 * und dabei an deren Ende geklemmt — beides unverändert gegenüber der Fassung vor A-31.
 *
 * *Die neuen IDs kommen von außen (`naechsteId`), damit eine Zusage sie vorhersagen kann; die
 * Komponente reicht `uuid` durch.*
 */
export function befehleDuplizieren(
  ids: readonly string[],
  nodes: readonly SceneNode[],
  waende: readonly WallNode[],
  naechsteId: () => string,
  jetzt: string,
): DuplikatBefehle {
  const befehle: HausplanerCommand[] = [];
  const neueIds: string[] = [];
  for (const id of ids) {
    const n = nodes.find((x) => x.id === id);
    if (!n) continue;
    const neueId = naechsteId();
    if (n.type === 'wall') {
      const g = versetzteWand(n.start, n.end, 500, 500);
      befehle.push({ type: 'ADD_NODE', node: { ...n, id: neueId, start: g.start, end: g.end, createdAt: jetzt, updatedAt: jetzt } });
      neueIds.push(neueId);
    } else if (istOeffnung(n)) {
      const wand = waende.find((w) => w.id === (n as OpeningNode).hostWallId);
      if (!wand) continue;
      const o = n as OpeningNode;
      const laenge = wandLaenge(wand.start, wand.end);
      const off = Math.round(Math.max(0, Math.min(o.offsetFromWallStart + o.width + 100, laenge - o.width)));
      befehle.push({ type: 'ADD_NODE', node: { ...o, id: neueId, offsetFromWallStart: off, createdAt: jetzt, updatedAt: jetzt } });
      neueIds.push(neueId);
    }
  }

  return { befehle, neueIds };
}

/**
 * **Spiegeln des ganzen Grundrisses.** Ein Knopfdruck, **eine** Handlung — und seit A-31 auch **ein**
 * Undo-Schritt. Leere Wandliste ergibt eine leere Liste; die Komponente ruft dann nichts auf.
 */
export function befehleSpiegeln(waende: readonly WallNode[], achse: Achse): HausplanerCommand[] {
  const b = punkteBbox(waende.flatMap((w) => [w.start, w.end]));
  if (!b) {
    return [];
  }
  const pos = achsenMitte(b, achse);

  return waende.map((w) => {
    const g = spiegelteWand(w.start, w.end, achse, pos);

    return { type: 'MOVE_NODE', nodeId: w.id, position: { start: g.start, end: g.end } } as HausplanerCommand;
  });
}

/**
 * **Geschoss duplizieren.** Vorher `N+2` Undo-Schritte für **ein** Geschoss: das Geschoss, jede Wand
 * einzeln, das Dach. *Die Reihenfolge bleibt zwingend — `ADD_LEVEL` zuerst, weil die Knoten auf das
 * neue Geschoss zeigen.*
 */
export function befehleGeschossDuplizieren(
  dup: { level: Level; nodes: SceneNode[]; roof: RoofNode | null },
): HausplanerCommand[] {
  const befehle: HausplanerCommand[] = [{ type: 'ADD_LEVEL', level: dup.level }];
  for (const n of dup.nodes) {
    befehle.push({ type: 'ADD_NODE', node: n });
  }
  if (dup.roof) {
    befehle.push({ type: 'ADD_ROOF', roof: dup.roof });
  }

  return befehle;
}
