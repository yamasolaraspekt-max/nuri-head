/** Kleine, gemeinsame UI-Bausteine für das Studio (v9). Nur Darstellung. */
import React from 'react';

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
