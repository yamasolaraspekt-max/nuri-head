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
import { ZustandBadge } from './studioUi';
import { FAEHIGKEIT_GRUPPEN, faehigkeitenNach, type Faehigkeit } from './tools/faehigkeiten';

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
              const klickbar = f.art === 'werkzeug' && f.zustand === 'verfuegbar' && !!f.toolId;
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
                  {/* AUF-26/B4: umbrechen statt kappen. „Horizont…", „Sparren-…" sind informationslos; zwei
                      Zeilen kosten weniger als ein unlesbarer Eintrag. Der `title` der Zeile bleibt. */}
                  <span style={{ flex: 1, minWidth: 0, overflowWrap: 'anywhere', whiteSpace: 'normal', lineHeight: 1.25 }}>{f.label}</span>
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
