/**
 * AUF-83-T3-N1 — der **Überlauf** für Zeile 1.
 *
 * **Warum es diese Datei gibt:** Der Evaluator hat K-08 mit Gegen-Beweis widerlegt — die Bühne
 * verlor 20 px, statt zu gewinnen, weil Zeile 1 sechs Dinge nebeneinander trug (Geschoss ·
 * Objektname · Übernehmen-Knopf · Staleness-Pille · Gespeichert-Pille · Speichern-Knopf). Yamas
 * eigener Auftragstext vom 29.07. nennt für die Kopfleiste ausdrücklich ein „Overflow" — das fehlte.
 *
 * **Was hier NICHT passiert: nichts fällt weg.** Übernehmen-Knopf, Staleness-Pille und
 * Speicherstatus bleiben exakt dieselben Elemente mit demselben `action`, demselben Status und
 * demselben Verbot einer zweiten Statusquelle — nur hinter einem Knopf statt in der Zeile.
 *
 * **Dasselbe Muster wie `GeschossFlaeche`:** Anker-`span`, Umschalt-Knopf mit `aria-expanded` /
 * `aria-haspopup="dialog"`, Klick-daneben und Escape schließen. Keine zweite Menü-Logik — es ist
 * dieselbe, die Yama am Geschoss-Wähler schon zweimal geprüft hat. Ein Fokus-Falle (`dialogFokus.ts`)
 * wäre hier zu viel: das ist ein Anker-Menü mit zwei Zielen, kein seitenfüllender Dialog wie die
 * Befehlspalette oder `ConfigWizard`.
 *
 * **Rechts verankert (`hp-ok-menue`, `right: 0`), nicht links wie beim Geschoss-Wähler** — der
 * Knopf steht nahe am rechten Rand der Zeile; ein linksbündiges Menü liefe bei 1024 px aus dem
 * Fenster (N-04).
 */
import React, { useEffect, useRef } from 'react';
import { GESPERRT_BESCHRIFTUNG, GESPERRT_GRUND, GESPERRT_ZEIGER } from './gesperrtStil';
import { kopfzeile, pillenText, type Objektkopf } from '../state/objektkopf';

/** Dieselbe Form wie das lokale `statusPill` in `HausplanerApp` — hier nur gelesen, nicht erfunden. */
export interface Speicherstatus {
  text: string;
  farbe: string;
  grund: string;
}

interface Props {
  objektkopf: Objektkopf;
  speicherstatus: Speicherstatus;
  csrfToken: string;
  offen: boolean;
  setOffen: (offen: boolean) => void;
}

export function ObjektkopfUeberlauf({ objektkopf, speicherstatus, csrfToken, offen, setOffen }: Props): React.ReactElement {
  const huelle = useRef<HTMLSpanElement>(null);

  // Klick daneben und Esc schließen — wortgleiches Verhalten zu `GeschossFlaeche`, kein zweites Muster.
  useEffect(() => {
    if (!offen) return undefined;
    const beiKlick = (e: MouseEvent): void => {
      if (huelle.current && !huelle.current.contains(e.target as Node)) setOffen(false);
    };
    const beiTaste = (e: KeyboardEvent): void => { if (e.key === 'Escape') setOffen(false); };
    document.addEventListener('mousedown', beiKlick);
    document.addEventListener('keydown', beiTaste);
    return () => { document.removeEventListener('mousedown', beiKlick); document.removeEventListener('keydown', beiTaste); };
  }, [offen, setOffen]);

  return (
    <span ref={huelle} className="hp-ok-anker">
      <button
        type="button" aria-expanded={offen} aria-haspopup="dialog"
        onClick={() => setOffen(!offen)}
        className="hp-ok-ueberlauf-knopf"
        title={`Übernahme und Speicherstatus — ${kopfzeile(objektkopf)}`}
      >
        <span aria-hidden className="hp-ok-punkt" style={{ background: speicherstatus.farbe }} />
        {speicherstatus.text}
        <span aria-hidden>▾</span>
      </button>

      {offen && (
        <div role="dialog" aria-label="Übernahme und Speicherstatus" className="hp-ok-menue">
          {/* Derselbe Speicherstatus wie vorher inline — hier nur umgezogen, nicht neu berechnet. */}
          <span style={{ fontSize: 12, fontWeight: 600, padding: '4px 12px', borderRadius: 999, color: speicherstatus.farbe, background: speicherstatus.grund }}>
            {speicherstatus.text}
          </span>

          {/* Die Staleness-Pille — Wahrheit bleibt `objektkopf.status`, keine zweite Quelle. */}
          <span className={`hp-ok-pille hp-ok-pille--${objektkopf.status}`}>{pillenText(objektkopf)}</span>

          {/* Der Übernehmen-Knopf bleibt ein echtes Formular mit CSRF — derselbe POST auf dieselbe
              Route wie bisher. Ein `fetch` wäre ein zweiter Weg zur selben Wirkung. */}
          <form method="POST" action={objektkopf.uebernehmenUrl} className="hp-ok-form">
            <input type="hidden" name="_token" value={csrfToken} />
            <button
              type="submit"
              className="hp-ok-knopf"
              disabled={objektkopf.szeneLeer}
              title={objektkopf.szeneLeer
                ? 'Keine Szene vorhanden — erst zeichnen und speichern.'
                : 'Übernimmt die Szenen-Geometrie als neue Version in die Auslegung (gebaeude_geometrie).'}
              style={objektkopf.szeneLeer
                ? { cursor: GESPERRT_ZEIGER, background: GESPERRT_GRUND, color: GESPERRT_BESCHRIFTUNG }
                : undefined}
            >
              In Auslegung übernehmen
            </button>
          </form>
        </div>
      )}
    </span>
  );
}
