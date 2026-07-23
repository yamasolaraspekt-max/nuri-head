/**
 * Batch 0 — Fähigkeiten-Navigation (ersetzt die FACHPLANER-Attrappe). Rendert die EINE
 * Fähigkeiten-Registry datengetrieben: Gruppen → Fähigkeiten mit Zustand. Keine Fachlogik hier —
 * nur SICHTBAR machen (jede 🔴-Leiche erscheint). Interaktive, aktive Werkzeuge sind klickbar
 * (setzen activeToolId über den Callback); Engines/„schläft" zeigen ihren Zustand (Panel = Batch 1–3).
 * A11y: Zustand als Farbe UND Text (kein Nur-Farbe-Signal).
 *
 * Token-Scope (docs/architektur/react-hausplaner-token-scope.md): AUSSCHLIESSLICH `T.*` aus studioDaten —
 * kein Hex/rgba in dieser Datei (Hex lebt nur in studioDaten.ts).
 */
import React from 'react';
import { T } from './studioDaten';
import { FAEHIGKEIT_GRUPPEN, faehigkeitenNach, type Faehigkeit } from './tools/faehigkeiten';

function ZustandBadge({ zustand }: { zustand: Faehigkeit['zustand'] }): React.ReactElement {
  const aktiv = zustand === 'aktiv';
  return (
    <span
      title={aktiv ? 'aktiv – bedienbar' : 'schläft – Panel folgt (Batch 1–3)'}
      style={{
        display: 'inline-flex', alignItems: 'center', gap: 4, fontSize: 10, flex: '0 0 auto',
        color: aktiv ? T.brandInk : T.muted,
        background: aktiv ? T.okSoft : T.hair2,
        border: `1px solid ${aktiv ? T.brand : T.hair}`,
        borderRadius: 6, padding: '0 6px',
      }}
    >
      <span aria-hidden style={{ width: 6, height: 6, borderRadius: '50%', background: aktiv ? T.brand : T.faint }} />
      {aktiv ? 'aktiv' : 'schläft'}
    </span>
  );
}

export function FaehigkeitenNavi(
  { onAktivieren, activeToolId }: { onAktivieren: (toolId: string) => void; activeToolId?: string },
): React.ReactElement {
  return (
    <div>
      <div style={{ padding: '10px 12px 2px', fontSize: 11, fontWeight: 700, color: T.ink }}>Fähigkeiten</div>
      {FAEHIGKEIT_GRUPPEN.map((g) => {
        const items = faehigkeitenNach(g.id);
        if (items.length === 0) return null;
        return (
          <div key={g.id}>
            <div style={{ padding: '8px 12px 3px', fontSize: 10.5, fontWeight: 600, color: T.muted, textTransform: 'uppercase', letterSpacing: 0.3 }}>
              {g.label}
            </div>
            {items.map((f) => {
              // Nur modus-schaltende Werkzeuge sind aus der Navi klickbar; Aktionen (Löschen/Duplizieren)
              // und Engines behalten ihre eigenen Handler (Op-Leiste bzw. Batch 1–3) — hier nur sichtbar.
              const klickbar = f.art === 'werkzeug' && f.zustand === 'aktiv' && !!f.toolId;
              const aktiv = klickbar && f.toolId === activeToolId;
              return (
                <button
                  key={f.id}
                  type="button"
                  title={`${f.label} — ${f.funktion}${f.eingang ? ` · ${f.eingang} → ${f.ausgang ?? ''}` : ''}`}
                  aria-disabled={!klickbar}
                  onClick={klickbar ? () => onAktivieren(f.toolId as string) : undefined}
                  style={{
                    display: 'flex', alignItems: 'center', gap: 8, width: '100%', textAlign: 'left',
                    border: 'none', font: 'inherit', padding: '6px 12px',
                    background: aktiv ? T.okSoft : 'transparent',
                    cursor: klickbar ? 'pointer' : 'default',
                    color: klickbar ? T.ink : T.muted,
                  }}
                >
                  <span style={{ flex: 1, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{f.label}</span>
                  <ZustandBadge zustand={f.zustand} />
                </button>
              );
            })}
          </div>
        );
      })}
      <div style={{ padding: '10px 12px', fontSize: 11, color: T.faint, borderTop: `1px solid ${T.hair}`, marginTop: 8 }}>
        Jede Fähigkeit sichtbar · „schläft" = Bedien-Panel folgt (Batch 1–3).
      </div>
    </div>
  );
}
