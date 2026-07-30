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
  { onAktivieren, activeToolId, onEngine }: {
    onAktivieren: (toolId: string) => void;
    activeToolId?: string;
    /** AUF-33: öffnet die Fläche einer verfügbaren Engine. Fehlt der Rückruf, bleibt sie stumm. */
    onEngine?: (engineId: string) => void;
  },
): React.ReactElement {
  return (
    <div>
      {/* AUF-27: KEINE eigene Überschrift mehr — der Reiter „Fachplaner" beschriftet diese Fläche
          bereits. Eine zweite Überschrift darunter wäre eine doppelte Aussage, und das Wort
          „Fähigkeiten" ist Jargon: es beschreibt nicht, was der Nutzer sieht. */}
      {FAEHIGKEIT_GRUPPEN.map((g) => {
        const items = faehigkeitenNach(g.id);
        if (items.length === 0) return null;
        return (
          <div key={g.id}>
            <div className="hp-fn-rubrik">
              {g.label}
            </div>
            {items.map((f) => {
              // Nur modus-schaltende Werkzeuge sind aus der Navi klickbar; Aktionen (Löschen/Duplizieren)
              // und Engines behalten ihre eigenen Handler (Op-Leiste bzw. Batch 1–3) — hier nur sichtbar.
              // AUF-33/L2: Auch eine ENGINE ist klickbar, sobald sie `verfuegbar` ist — dann
              // öffnet sie ihre Fläche. Vorher war jede Engine tot, obwohl die Rechenfunktion da
              // war; es fehlte nur die Fläche davor.
              const istEngine = f.art === 'engine' && f.zustand === 'verfuegbar' && Boolean(onEngine);
              const klickbar = (f.art === 'werkzeug' && f.zustand === 'verfuegbar' && !!f.toolId) || istEngine;
              const aktiv = klickbar && f.toolId === activeToolId;
              return (
                <button
                  key={f.id}
                  type="button"
                  title={`${f.label} — ${f.funktion}${f.eingang ? ` · ${f.eingang} → ${f.ausgang ?? ''}` : ''}`}
                  aria-disabled={!klickbar}
                  onClick={klickbar ? () => (istEngine ? onEngine?.(f.id) : onAktivieren(f.toolId as string)) : undefined}
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
                  <span className="hp-fn-label">{f.label}</span>
                  <ZustandBadge zustand={f.zustand} />
                </button>
              );
            })}
          </div>
        );
      })}
      <div className="hp-fn-fuss">
        Jeder Eintrag sichtbar · „schläft" = Bedien-Panel folgt (Batch 1–3).
      </div>
    </div>
  );
}
