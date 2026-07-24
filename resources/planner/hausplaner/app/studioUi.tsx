/** Kleine, gemeinsame UI-Bausteine für das Studio (v9). Nur Darstellung. */
import React from 'react';
import { T } from './studioDaten';

/**
 * Rendert ein 24er-viewBox-Icon aus rohem SVG-Innen-Markup (interne, statische Daten). `titel` gibt dem
 * Icon einen nativen SVG-`<title>`-Tooltip UND ein `aria-label` (A11y, §9 Tooltip-Pflicht). Da das Markup
 * über dangerouslySetInnerHTML kommt, wird der `<title>` dem HTML vorangestellt (mit Escape der Sonderzeichen).
 */
export function Ikon({ inhalt, size = 22, stroke = 1.6, titel }: { inhalt: string; size?: number; stroke?: number; titel?: string }): React.ReactElement {
  const esc = (s: string): string => s.replace(/[<>&]/g, (c) => (c === '<' ? '&lt;' : c === '>' ? '&gt;' : '&amp;'));
  const html = (titel ? `<title>${esc(titel)}</title>` : '') + inhalt;
  return (
    <svg
      width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor"
      strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round"
      role={titel ? 'img' : undefined} aria-label={titel}
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
}

/**
 * Zustands-Pille (aktiv/schläft) — GETEILTER Baustein (FaehigkeitenNavi + künftige Topbar), eine Wahrheit.
 * A11y: Zustand als Farbe UND Text. „aktiv" = STATUS-Grün `T.ok` (kein Marken-Moment `T.brand`) — ein
 * Zustand ist kein Marken-Signal; das behebt das „falsche Grün" und hebt den Kontrast. 0 roher Hex (nur T.*).
 */
export type StudioZustand = 'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';

/** Dashboard v1 §1 — ehrliche Zustände: „verfügbar" = Status-Grün T.ok; sonst kein „schläft ohne Grund".
 *  Jeder Zustand trägt Farbe UND Text UND Punkt (A11y, nie nur Farbe). 0 roher Hex (nur T.*). */
const ZUSTAND: Record<StudioZustand, { kurz: string; lang: string; fg: string; bg: string; rand: string; punkt: string }> = {
  verfuegbar: { kurz: 'verfügbar', lang: 'verfügbar – bedienbar', fg: T.okInk, bg: T.okSoft, rand: T.ok, punkt: T.ok },
  voraussetzung: { kurz: 'Vorauss. fehlt', lang: 'Voraussetzung fehlt (z. B. Räume/Auswahl)', fg: T.warnInk, bg: T.warnSoft, rand: T.warn, punkt: T.warn },
  nur_ergebnis: { kurz: 'nur Ergebnis', lang: 'nur Ergebnis – kein Zeichen-Modus', fg: T.ink, bg: T.hair2, rand: T.hair, punkt: T.muted },
  in_entwicklung: { kurz: 'in Entwicklung', lang: 'in Entwicklung – Panel folgt', fg: T.muted, bg: T.hair2, rand: T.hair, punkt: T.faint },
};

export function ZustandBadge({ zustand }: { zustand: StudioZustand }): React.ReactElement {
  const z = ZUSTAND[zustand];
  return (
    <span
      title={z.lang}
      style={{
        display: 'inline-flex', alignItems: 'center', gap: 4, fontSize: 10, flex: '0 0 auto',
        color: z.fg, background: z.bg, border: `1px solid ${z.rand}`, borderRadius: 6, padding: '0 6px',
      }}
    >
      <span aria-hidden style={{ width: 6, height: 6, borderRadius: '50%', background: z.punkt }} />
      {z.kurz}
    </span>
  );
}
