/** Kleine, gemeinsame UI-Bausteine für das Studio (v9). Nur Darstellung. */
import React from 'react';
import { T } from './studioDaten';

/** Rendert ein 24er-viewBox-Icon aus rohem SVG-Innen-Markup (interne, statische Daten). */
export function Ikon({ inhalt, size = 22, stroke = 1.6 }: { inhalt: string; size?: number; stroke?: number }): React.ReactElement {
  return (
    <svg
      width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor"
      strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round"
      dangerouslySetInnerHTML={{ __html: inhalt }}
    />
  );
}

/**
 * Zustands-Pille (aktiv/schläft) — GETEILTER Baustein (FaehigkeitenNavi + künftige Topbar), eine Wahrheit.
 * A11y: Zustand als Farbe UND Text. „aktiv" = STATUS-Grün `T.ok` (kein Marken-Moment `T.brand`) — ein
 * Zustand ist kein Marken-Signal; das behebt das „falsche Grün" und hebt den Kontrast. 0 roher Hex (nur T.*).
 */
export function ZustandBadge({ zustand }: { zustand: 'aktiv' | 'schlaeft' }): React.ReactElement {
  const aktiv = zustand === 'aktiv';
  return (
    <span
      title={aktiv ? 'aktiv – bedienbar' : 'schläft – Panel folgt (Batch 1–3)'}
      style={{
        display: 'inline-flex', alignItems: 'center', gap: 4, fontSize: 10, flex: '0 0 auto',
        color: aktiv ? T.okInk : T.muted,
        background: aktiv ? T.okSoft : T.hair2,
        border: `1px solid ${aktiv ? T.ok : T.hair}`,
        borderRadius: 6, padding: '0 6px',
      }}
    >
      <span aria-hidden style={{ width: 6, height: 6, borderRadius: '50%', background: aktiv ? T.ok : T.faint }} />
      {aktiv ? 'aktiv' : 'schläft'}
    </span>
  );
}
