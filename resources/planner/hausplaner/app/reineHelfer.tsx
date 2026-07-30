/**
 * AUF-48 Scheibe 1 — **das Reine aus `HausplanerApp.tsx`.**
 *
 * **Was hier steht:** sieben Funktionen, die weder React-Zustand lesen noch schreiben. Sie nehmen
 * Werte und geben Werte zurück — kein `useState`, kein `useEffect`, kein Kontext, kein Store.
 * *Genau deshalb ist diese Scheibe die erste: sie muss nichts wissen, und ein Fehler beim
 * Verschieben fällt sofort an den 28 bestehenden Zusagen auf.*
 *
 * **Was hier NICHT passiert: nichts wird geändert.** Kein umbenannter Bezeichner, keine geglättete
 * Zeile, keine „bei der Gelegenheit"-Verbesserung. Die Rümpfe sind Zeichen für Zeichen dieselben.
 * *Eine Zerlegung, die nebenbei umbaut, ist keine Zerlegung — dann weiß hinterher niemand, ob ein
 * geändertes Verhalten vom Verschieben oder vom Umbauen kommt.*
 *
 * ---
 *
 * **⚠ Abweichung vom Auftragsblatt: `.tsx` statt `.ts`, und sie ist technisch erzwungen.**
 * Das Blatt nennt `app/reineHelfer.ts`. **Drei der sieben Funktionen enthalten JSX**
 * (`svgWrap`, `werkzeugIcon`, `opIcon`) — TypeScript liest JSX ausschließlich in `.tsx`, unabhängig
 * von der `jsx`-Einstellung. Die Alternative wäre gewesen, das JSX in `React.createElement`-Aufrufe
 * umzuschreiben; **das wäre genau die Verhaltens-/Gestaltänderung, die `nicht_ziel` verbietet**, und
 * es hätte die Icons unlesbar gemacht. `.tsx` hält außerdem die Grundgesamtheit des
 * Scheibe-7-Messwerkzeugs unverändert (es zählt `.tsx`).
 *
 * **Die drei toten Konstanten sind hier NICHT gelandet.** `navGrp`, `navHub`, `navSub` hatten je
 * genau ein Vorkommen — ihre eigene Definition (gemessen, und vom Prüfer unabhängig bestätigt).
 * Sie sind ersatzlos entfallen. **`navItem` ist geblieben**: es sieht gleich aus, hat aber eine
 * echte Verwendung.
 */
import React from 'react';
import type { OpeningNode, SceneNode, WallNode } from '../domain/scene.types';
import type { Punkt } from '../geometry/wallGeometry';

// SVG-Icons (frei/Feather-Stil, nachgezeichnet) — einheitlich 24er-Viewbox, stroke=currentColor.
export function svgWrap(children: React.ReactNode): React.ReactElement {
  return (
    <svg width={18} height={18} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={1.7} strokeLinecap="round" strokeLinejoin="round">{children}</svg>
  );
}
export function werkzeugIcon(w: string): React.ReactElement {
  switch (w) {
    case 'auswahl': return svgWrap(<path d="M5 3l6 16 2-7 7-2z" />);
    case 'wand': return svgWrap(<rect x="3" y="10" width="18" height="4" rx="1" />);
    case 'fenster': return svgWrap(<><rect x="4" y="4" width="16" height="16" rx="1" /><path d="M12 4v16M4 12h16" /></>);
    case 'tuer': return svgWrap(<><path d="M7 21V4h9v17" /><path d="M7 21a9 9 0 0 1 9-9" /></>);
    case 'dach': return svgWrap(<path d="M3 12L12 5l9 7" />);
    case 'decke': return svgWrap(<><rect x="3" y="9" width="18" height="5" rx="1" /><path d="M13 9v5" /></>);
    case 'treppe': return svgWrap(<path d="M3 21h4v-4h4v-4h4v-4h4" />);
    default: return svgWrap(<circle cx="12" cy="12" r="3" />);
  }
}
// Bedien-Icons (Ansicht/Bearbeiten/Messen&Export) — je mit Tooltip + Funktionsbeschreibung.
export function opIcon(name: string): React.ReactElement {
  switch (name) {
    case 'zoom-in': return svgWrap(<><circle cx="11" cy="11" r="7" /><path d="M21 21l-4.35-4.35M11 8v6M8 11h6" /></>);
    case 'zoom-out': return svgWrap(<><circle cx="11" cy="11" r="7" /><path d="M21 21l-4.35-4.35M8 11h6" /></>);
    case 'zoom-reset': return svgWrap(<><circle cx="12" cy="12" r="8" /><circle cx="12" cy="12" r="2" /></>);
    case 'einpassen': return svgWrap(<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" />);
    case 'grid': return svgWrap(<><rect x="4" y="4" width="16" height="16" rx="1" /><path d="M4 10h16M4 16h16M10 4v16M16 4v16" /></>);
    case 'fang': return svgWrap(<><path d="M12 3v6M12 15v6M3 12h6M15 12h6" /><circle cx="12" cy="12" r="2.5" /></>);
    case 'dup': return svgWrap(<><rect x="9" y="9" width="11" height="11" rx="2" /><path d="M5 15V5a2 2 0 0 1 2-2h8" /></>);
    case 'del': return svgWrap(<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" />);
    case 'mirror-h': return svgWrap(<path d="M12 3v18M8 7l-4 5 4 5M16 7l4 5-4 5" />);
    case 'mirror-v': return svgWrap(<path d="M3 12h18M7 8l5-4 5 4M7 16l5 4 5-4" />);
    case 'drehen': return svgWrap(<><path d="M21 12a9 9 0 1 1-3-6.7" /><path d="M21 3v5h-5" /></>);
    case 'messen': return svgWrap(<><path d="M3 15l12-12 6 6-12 12z" /><path d="M8 8l2 2M11 5l2 2M5 11l2 2" /></>);
    case 'bemassung': return svgWrap(<path d="M3 8h18M5 6v4M19 6v4M9 6v4M13 6v4M8 16h8" />);
    case 'export': return svgWrap(<path d="M12 3v12M8 7l4-4 4 4M5 15v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4" />);
    // AUF-70: Rückgängig/Wiederholen ziehen in diese Zeile und brauchen dort ein Icon.
    case 'undo': return svgWrap(<><path d="M3 12a9 9 0 1 1 3 6.7" /><path d="M3 3v5h5" /></>);
    case 'redo': return svgWrap(<><path d="M21 12a9 9 0 1 0-3 6.7" /><path d="M21 3v5h-5" /></>);
    case 'pdf': return svgWrap(<><path d="M7 3h7l4 4v14H7z" /><path d="M9 13h2a1.5 1.5 0 0 0 0-3H9v6M15 11h-2v5" /></>);
    default: return svgWrap(<circle cx="12" cy="12" r="3" />);
  }
}

export const uuid = (): string =>
  (globalThis.crypto?.randomUUID?.() ?? `id-${Math.random().toString(36).slice(2)}-${Date.now()}`);

export function istWand(n: SceneNode): n is WallNode {
  return n.type === 'wall';
}
export function istOeffnung(n: SceneNode): n is OpeningNode {
  return n.type === 'window' || n.type === 'door' || n.type === 'opening';
}

/** Abstand Punkt→Strecke + Offset entlang der Strecke (mm). */
export function lotAufWand(p: Punkt, w: WallNode): { abstand: number; offset: number } {
  const dx = w.end.x - w.start.x;
  const dy = w.end.y - w.start.y;
  const laengeQ = dx * dx + dy * dy;
  if (laengeQ === 0) {
    return { abstand: Number.POSITIVE_INFINITY, offset: 0 };
  }
  const t = Math.max(0, Math.min(1, ((p.x - w.start.x) * dx + (p.y - w.start.y) * dy) / laengeQ));
  const fx = w.start.x + t * dx;
  const fy = w.start.y + t * dy;

  return { abstand: Math.hypot(p.x - fx, p.y - fy), offset: t * Math.sqrt(laengeQ) };
}
