/**
 * L4 (Fahrplan §3) — die Fläche eines Fachplaner-Untermoduls.
 *
 * Ersetzt den bisherigen Toast „Konfigurator folgt." durch eine echte, tiefe Fläche mit vier
 * Teilen: Kopf (Modul, Gruppe, Zurück-Weg) · Zweck · Feldstruktur-Vorschau (deaktivierte Ein- und
 * Ausgangsfelder mit sichtbarem Grund) · Leerzustand mit `ZustandBadge`.
 *
 * Dünnes JSX über Daten: der ganze Inhalt kommt aus `dashboard/fachFlaechen.ts`, hier steht keine
 * Modulliste und kein Fachtext. Wiederverwendet werden `T` (Tokens), `Ikon` und `ZustandBadge` aus
 * `studioUi` — kein zweites Designsystem, 0 roher Farbwert.
 *
 * Kante 4 (Auftrag): die Fläche darf nicht so aussehen, als könnte sie rechnen. Es gibt deshalb
 * KEINE Schaltfläche „Berechnen"; jedes Feld ist `disabled` und der Grund steht als Text in der
 * Fläche, nicht nur im Tooltip.
 *
 * Kante 5 (Auftrag): umbrechen statt abschneiden. Die Feldstruktur liegt in einem
 * `repeat(auto-fit, minmax(…))`-Raster, der Kopf in einem `flexWrap`-Streifen; horizontal wird
 * nichts gekappt (`overflowX: 'hidden'`, `overflowWrap: 'anywhere'`). Genau der Fehler, der im
 * Expertenmodus bei 1375 px den vierten Reiter verschluckt, wird hier nicht wiederholt.
 */
import React from 'react';
import { T } from './studioDaten';
import { Ikon, ZustandBadge } from './studioUi';
import {
  GRUND_DEAKTIVIERT,
  HINWEIS_ENGINE,
  HINWEIS_OHNE_ENGINE,
  zurueckLabel,
  type FachFlaeche as FachFlaecheDaten,
  type FeldVorschau,
  type FlaechenHerkunft,
} from './dashboard/fachFlaechen';

interface Props {
  flaeche: FachFlaecheDaten;
  /** Woher der Nutzer kam — bestimmt die Beschriftung des Zurück-Wegs (Kante 2). */
  herkunft: FlaechenHerkunft;
  /** Schließt die Fläche und stellt genau den vorherigen Kontext wieder her. */
  onZurueck: () => void;
}

/** Raster, das umbricht statt abzuschneiden — eine Spalte ab ca. 375 px, sonst so viele wie passen. */
const raster: React.CSSProperties = {
  display: 'grid',
  gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
  gap: 12,
};

const spaltenTitel: React.CSSProperties = {
  fontSize: 11.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase',
  color: T.faint, margin: '0 0 10px',
};

/** Ein deaktiviertes Eingangsfeld: Beschriftung, Einheit, leerer Eingang. Nimmt nichts entgegen. */
function EingangFeld({ feld, grundId }: { feld: FeldVorschau; grundId: string }): React.ReactElement {
  return (
    <label style={{ display: 'block', minWidth: 0 }}>
      <span style={{ display: 'flex', alignItems: 'baseline', gap: 6, flexWrap: 'wrap', fontSize: 12.5, color: T.muted, marginBottom: 5, overflowWrap: 'anywhere' }}>
        <span style={{ color: T.ink }}>{feld.label}</span>
        {feld.einheit && <span style={{ color: T.faint }}>{feld.einheit}</span>}
        {feld.typ && (
          <span style={{ fontSize: 10.5, color: T.accentInk, background: T.accentSoft, borderRadius: 6, padding: '0 6px' }}>{feld.typ}</span>
        )}
      </span>
      <input
        type="text" value="" readOnly disabled aria-describedby={grundId} placeholder="—"
        style={{
          width: '100%', boxSizing: 'border-box', border: `1px solid ${T.hair}`, borderRadius: 10,
          padding: '9px 12px', font: 'inherit', fontSize: 13.5, background: T.surface2,
          color: T.faint, cursor: 'not-allowed',
        }}
      />
    </label>
  );
}

/** Eine deaktivierte Ergebniszeile: Beschriftung, Einheit, „—" statt eines erfundenen Wertes. */
function AusgangZeile({ feld, grundId }: { feld: FeldVorschau; grundId: string }): React.ReactElement {
  return (
    <div
      aria-describedby={grundId}
      style={{
        display: 'flex', alignItems: 'baseline', justifyContent: 'space-between', gap: 10,
        flexWrap: 'wrap', minWidth: 0, background: T.surface2, border: `1px solid ${T.hair}`,
        borderRadius: 10, padding: '9px 12px',
      }}
    >
      <span style={{ display: 'flex', alignItems: 'baseline', gap: 6, flexWrap: 'wrap', fontSize: 13, color: T.ink, overflowWrap: 'anywhere' }}>
        {feld.label}
        {feld.einheit && <span style={{ fontSize: 12, color: T.faint }}>{feld.einheit}</span>}
        {feld.typ && (
          <span style={{ fontSize: 10.5, color: T.accentInk, background: T.accentSoft, borderRadius: 6, padding: '0 6px' }}>{feld.typ}</span>
        )}
      </span>
      <span style={{ fontSize: 13.5, color: T.faint, fontVariantNumeric: 'tabular-nums' }}>—</span>
    </div>
  );
}

export function FachFlaeche({ flaeche, herkunft, onZurueck }: Props): React.ReactElement {
  const basisId = React.useId();
  const titelId = `${basisId}-titel`;
  const grundId = `${basisId}-grund`;

  // Escape schließt — derselbe Rückweg wie die Schaltfläche, nicht die Startseite (Kante 2).
  React.useEffect(() => {
    const beiTaste = (e: KeyboardEvent): void => { if (e.key === 'Escape') onZurueck(); };
    window.addEventListener('keydown', beiTaste);
    return () => window.removeEventListener('keydown', beiTaste);
  }, [onZurueck]);

  return (
    <div
      onClick={onZurueck}
      style={{
        position: 'fixed', inset: 0, background: 'rgba(24,34,38,.30)', backdropFilter: 'blur(2px)',
        display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 90, padding: 16,
      }}
    >
      <div
        role="dialog" aria-modal="true" aria-labelledby={titelId}
        onClick={(e) => e.stopPropagation()}
        style={{
          width: 'min(880px, 100%)', maxHeight: '94%', background: T.surface, borderRadius: 20,
          boxShadow: '0 10px 34px rgba(28,50,55,.18)', display: 'flex', flexDirection: 'column',
          overflowX: 'hidden', overflowY: 'auto',
        }}
      >
        {/* 1 · Kopf — Modul, Gruppe, Zurück-Weg. Bricht um, statt zu kappen (Kante 5). */}
        <div style={{ display: 'flex', alignItems: 'flex-start', gap: 12, flexWrap: 'wrap', padding: '20px 24px 12px' }}>
          <button
            type="button" onClick={onZurueck}
            style={{
              border: `1px solid ${T.hair}`, background: T.surface, color: T.ink, fontWeight: 600,
              fontSize: 13, padding: '7px 13px', borderRadius: 10, cursor: 'pointer',
              display: 'flex', alignItems: 'center', gap: 7, flex: '0 0 auto',
            }}
          >
            <Ikon inhalt='<path d="M15 6l-6 6 6 6"/>' size={15} />{zurueckLabel(herkunft)}
          </button>
          <div style={{ flex: '1 1 240px', minWidth: 0 }}>
            <div style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.09em', textTransform: 'uppercase', color: T.accent }}>
              Fachplaner · {flaeche.gruppe}
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap', marginTop: 4 }}>
              <h2 id={titelId} style={{ fontSize: 21, fontWeight: 800, letterSpacing: '-.01em', margin: 0, overflowWrap: 'anywhere' }}>
                {flaeche.label}
              </h2>
              <ZustandBadge zustand={flaeche.zustand} />
            </div>
          </div>
        </div>

        {/* 2 · Zweck — ein Satz, was hier entsteht. */}
        <p style={{ margin: 0, padding: '0 24px', fontSize: 14.5, color: T.muted, lineHeight: 1.5, overflowWrap: 'anywhere' }}>
          {flaeche.zweck}
        </p>

        {/* 4 · Leerzustand: der Grund steht als Text, nicht nur als Tooltip (Kante 4). */}
        <div
          id={grundId}
          style={{
            margin: '14px 24px 0', display: 'flex', alignItems: 'flex-start', gap: 10, flexWrap: 'wrap',
            background: T.hair2, border: `1px solid ${T.hair}`, borderRadius: 12, padding: '11px 14px',
            fontSize: 12.5, color: T.muted, lineHeight: 1.45,
          }}
        >
          <span style={{ color: T.faint, flex: '0 0 auto', marginTop: 1 }}>
            <Ikon inhalt='<circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/>' size={16} titel="Hinweis" />
          </span>
          <span style={{ flex: '1 1 220px', minWidth: 0 }}>
            {GRUND_DEAKTIVIERT} {flaeche.engine ? HINWEIS_ENGINE : HINWEIS_OHNE_ENGINE}
          </span>
        </div>

        {/* 3 · Feldstruktur-Vorschau — die Form des späteren Panels, ohne Werte. */}
        <div style={{ padding: '18px 24px 24px', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 22 }}>
          <section style={{ minWidth: 0 }}>
            <h3 style={spaltenTitel}>Eingangsgrößen ({flaeche.eingaenge.length})</h3>
            <div style={raster}>
              {flaeche.eingaenge.map((f) => <EingangFeld key={f.label} feld={f} grundId={grundId} />)}
            </div>
          </section>
          <section style={{ minWidth: 0 }}>
            <h3 style={spaltenTitel}>Ergebnisse ({flaeche.ausgaenge.length})</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {flaeche.ausgaenge.map((f) => <AusgangZeile key={f.label} feld={f} grundId={grundId} />)}
            </div>
          </section>
        </div>
      </div>
    </div>
  );
}
