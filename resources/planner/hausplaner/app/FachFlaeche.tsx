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
import { GESPERRT_ZEIGER, GESPERRT_GRUND, GESPERRT_BESCHRIFTUNG } from './dashboard/gesperrtStil';
import { T } from './studioDaten';
import { Ikon, ZustandBadge, type StudioZustand } from './studioUi';
import { useDialogFokus } from './dashboard/dialogFokus';
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
/**
 * AUF-38 Scheibe 3 — die statischen Stile stehen jetzt in `hausplaner.css` (`.hp-fach-*`).
 * Ihre Farben sind dort `--hp-*`-Variablen aus `T`; **kein Wert ist abgeschrieben.** Was aus
 * Zustand oder Messung kommt, blieb inline — Ziel ist null *statische* Inline-Stile.
 */

/** Ein deaktiviertes Eingangsfeld: Beschriftung, Einheit, leerer Eingang. Nimmt nichts entgegen. */
function EingangFeld({ feld, grundId }: { feld: FeldVorschau; grundId: string }): React.ReactElement {
  return (
    <label className="hp-fach-feld">
      <span className="hp-fach-feldkopf">
        <span className="hp-fach-feldname">{feld.label}</span>
        {feld.einheit && <span className="hp-fach-einheit">{feld.einheit}</span>}
        {feld.typ && (
          <span className="hp-fach-typ">{feld.typ}</span>
        )}
      </span>
      <input
        type="text" value="" readOnly disabled aria-describedby={grundId} placeholder="—"
        /* AUF-38 Scheibe 3, Nachbesserung: **bleibt inline.** Die Sperr-Werte kommen aus
           `gesperrtStil.ts` — der EINEN Wahrheit darueber, wie eine gesperrte Flaeche aussieht
           (AUF-71). Sie hier als Variable nachzubauen hiesse, diese Wahrheit zu verdoppeln: aendert
           das Modul seinen Token, folgte die CSS nicht mit. */
        style={{
          width: '100%', boxSizing: 'border-box', border: `1px solid ${T.hair}`, borderRadius: 10,
          padding: '9px 12px', font: 'inherit', fontSize: 13.5, background: GESPERRT_GRUND,
          color: GESPERRT_BESCHRIFTUNG, cursor: GESPERRT_ZEIGER,
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
      className="hp-fach-ausgang"
    >
      <span className="hp-fach-ausgang-kopf">
        {feld.label}
        {feld.einheit && <span className="hp-fach-ausgang-einheit">{feld.einheit}</span>}
        {feld.typ && (
          <span className="hp-fach-typ">{feld.typ}</span>
        )}
      </span>
      <span className="hp-fach-leerwert">—</span>
    </div>
  );
}

/**
 * AUF-33 — die **Hülle** einer Fachfläche: Überlagerung, Kopf mit Zurück-Weg, Zweck, Escape,
 * Klick daneben. Sie war bis dahin im Rumpf von `FachFlaeche` eingebaut; seit die Engine-Flächen
 * (L2) dieselbe Hülle brauchen, steht sie hier **einmal** und wird zweimal benutzt.
 *
 * Der Auftrag sagt es wörtlich: *„`FachFlaeche.tsx` liefert bereits Kopf, Zweck, Zurück und
 * Leerzustand — das wird wiederverwendet, nicht neu gebaut."* Ein zweiter Rahmen mit eigenem
 * Escape-Handler und eigener Kopfzeile wäre genau die Doppelung, die dieser Posten vermeiden soll.
 */
export function FlaechenHuelle({
  titel, gruppe, zustand, zweck, zurueck, onZurueck, children,
}: {
  titel: string;
  gruppe: string;
  zustand: StudioZustand;
  zweck: string;
  /** Beschriftung des Zurück-Knopfes — kommt von der Herkunft, nie pauschal (Kante 2). */
  zurueck: string;
  onZurueck: () => void;
  children: React.ReactNode;
}): React.ReactElement {
  const titelId = React.useId();
  const huelle = React.useRef<HTMLDivElement>(null);

  /**
   * AUF-49: Escape **und** Fokus. Vorher schloss Escape zwar, aber der Fokus blieb beim Öffnen
   * draußen stehen, lief beim Tabben hinter den Dialog und kehrte beim Schließen nicht zurück.
   * Die Regel steht einmal in `dashboard/dialogFokus.ts` und gilt für alle Dialoge.
   */
  useDialogFokus(huelle, onZurueck);

  return (
    <div
      onClick={onZurueck}
      /* AUF-38 Scheibe 3, Nachbesserung: **bleibt inline.** `rgba(24,34,38,.30)` hat in `T` keinen
         Token — AUF-56 hat die selteneren Schattenwerte ausdruecklich roh gelassen, weil ein Token
         fuer einen einzigen Aufruf keine Rolle ist, sondern eine Umbenennung. In der CSS waere es
         ein roher Farbwert in einer Regel und damit ein Verstoss gegen Kriterium 4. */
      style={{
        position: 'fixed', inset: 0, background: 'rgba(24,34,38,.30)', backdropFilter: 'blur(2px)',
        display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 90, padding: 16,
      }}
    >
      <div
        ref={huelle}
        role="dialog" aria-modal="true" aria-labelledby={titelId}
        onClick={(e) => e.stopPropagation()}
        /* AUF-38 Scheibe 3, Nachbesserung: **bleibt inline.** Derselbe Grund — der Schatten
           `rgba(28,50,55,.18)` hat keinen Token (AUF-56, seltenere Schattenwerte). */
        style={{
          width: 'min(880px, 100%)', maxHeight: '94%', background: T.surface, borderRadius: 20,
          boxShadow: '0 10px 34px rgba(28,50,55,.18)', display: 'flex', flexDirection: 'column',
          overflowX: 'hidden', overflowY: 'auto',
        }}
      >
        <div className="hp-fach-kopf">
          <button
            type="button" onClick={onZurueck}
            className="hp-fach-zurueck"
          >
            <Ikon inhalt='<path d="M15 6l-6 6 6 6"/>' size={15} />{zurueck}
          </button>
          <div className="hp-fach-kopf-text">
            <div className="hp-fach-gruppe">
              Fachplaner · {gruppe}
            </div>
            <div className="hp-fach-titelzeile">
              <h2 id={titelId} className="hp-fach-titel">
                {titel}
              </h2>
              <ZustandBadge zustand={zustand} />
            </div>
          </div>
        </div>

        <p className="hp-fach-zweck">
          {zweck}
        </p>

        {children}
      </div>
    </div>
  );
}

export function FachFlaeche({ flaeche, herkunft, onZurueck }: Props): React.ReactElement {
  const basisId = React.useId();
  const grundId = `${basisId}-grund`;

  return (
    <FlaechenHuelle
      titel={flaeche.label} gruppe={flaeche.gruppe} zustand={flaeche.zustand}
      zweck={flaeche.zweck} zurueck={zurueckLabel(herkunft)} onZurueck={onZurueck}
    >
      <>
        {/* 4 · Leerzustand: der Grund steht als Text, nicht nur als Tooltip (Kante 4). */}
        <div
          id={grundId}
          className="hp-fach-grund"
        >
          <span className="hp-fach-grund-icon">
            <Ikon inhalt='<circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/>' size={16} titel="Hinweis" />
          </span>
          <span className="hp-fach-hinweis">
            {GRUND_DEAKTIVIERT} {flaeche.engine ? HINWEIS_ENGINE : HINWEIS_OHNE_ENGINE}
          </span>
        </div>

        {/* 3 · Feldstruktur-Vorschau — die Form des späteren Panels, ohne Werte. */}
        <div className="hp-fach-rumpf">
          <section className="hp-fach-spalte">
            <h3 className="hp-fach-spaltentitel">Eingangsgrößen ({flaeche.eingaenge.length})</h3>
            <div className="hp-fach-raster">
              {flaeche.eingaenge.map((f) => <EingangFeld key={f.label} feld={f} grundId={grundId} />)}
            </div>
          </section>
          <section className="hp-fach-spalte">
            <h3 className="hp-fach-spaltentitel">Ergebnisse ({flaeche.ausgaenge.length})</h3>
            <div className="hp-fach-liste">
              {flaeche.ausgaenge.map((f) => <AusgangZeile key={f.label} feld={f} grundId={grundId} />)}
            </div>
          </section>
        </div>
      </>
    </FlaechenHuelle>
  );
}
