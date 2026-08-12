/**
 * AUF-33 / L2 — die Fläche einer **Rechen-Engine**. Muster für die übrigen zwölf.
 *
 * **Der Ablauf ist bewusst schlicht:** Eingabefelder → Knopf → Ergebnisblock + Prüfliste. Genau
 * das, was `geometry/treppenBerechnung.ts` vorzeichnet: *Eingabe rein, Zahlen und Prüfungen raus.*
 *
 * **Hier wird nicht gerechnet.** Kein Grenzwert, keine Formel, keine Rundung, kein DIN-Maß. Die
 * Fläche ruft `panel.berechne(...)` und zeigt, was zurückkommt — Zahl für Zahl, Prüfung für
 * Prüfung. Jede Zahl, die hier entstünde statt in der Engine, wäre ein Defekt, den L3 zwölfmal
 * mitkopieren würde.
 *
 * **`bestanden: false` ist ein gültiger Zustand, kein Fehlerbildschirm.** Die Zahlen bleiben
 * sichtbar, die verletzte Prüfung wird benannt. Ein Architekt will sehen, *wie knapp* er daneben
 * liegt — nicht, dass „ein Fehler aufgetreten" ist.
 *
 * **Schweregrade nie nur über Farbe** (UI-Bauordnung / WCAG 1.4.1): jede Prüfzeile trägt Zeichen
 * **und** Wort — „✕ Fehler", „⚠ Warnung", „ℹ Hinweis".
 *
 * **Hülle wiederverwendet:** Kopf, Zweck, Zurück-Weg und Escape kommen aus `FlaechenHuelle`
 * (`FachFlaeche.tsx`), nicht aus einem zweiten Rahmen.
 */
import React, { useState } from 'react';
import { GESPERRT_ZEIGER, GESPERRT_GRUND, GESPERRT_BESCHRIFTUNG } from './dashboard/gesperrtStil';
import { T } from './studioDaten';
import { FlaechenHuelle } from './FachFlaeche';
import {
  type EnginePanel, startwerte, fehlendePflichtfelder,
} from './dashboard/enginePanels';

/** Zeichen UND Wort je Schweregrad — nie nur Farbe. */
const SCHWERE_ANZEIGE: Readonly<Record<string, { zeichen: string; wort: string; token: 'errInk' | 'warnInk' | 'muted' }>> = {
  fehler: { zeichen: '✕', wort: 'Fehler', token: 'errInk' },
  warnung: { zeichen: '⚠', wort: 'Warnung', token: 'warnInk' },
  info: { zeichen: 'ℹ', wort: 'Hinweis', token: 'muted' },
};

interface Props {
  panel: EnginePanel;
  gruppe: string;
  zustand: 'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';
  zurueck: string;
  onZurueck: () => void;
}

export function EngineFlaeche({ panel, gruppe, zustand, zurueck, onZurueck }: Props): React.ReactElement {
  const [werte, setWerte] = useState<Record<string, string>>(() => startwerte(panel));
  /** Das Ergebnis der letzten Berechnung. `null` = noch nicht gerechnet — kein erfundener Startwert. */
  const [ergebnis, setErgebnis] = useState<ReturnType<EnginePanel['berechne']> | null>(null);

  const fehlt = fehlendePflichtfelder(panel, werte);
  const rechnen = (): void => { if (fehlt.length === 0) setErgebnis(panel.berechne(werte)); };

  return (
    <FlaechenHuelle titel={panel.titel} gruppe={gruppe} zustand={zustand} zweck={panel.zweck} zurueck={zurueck} onZurueck={onZurueck}>
      <>
        {/* Die Rechengrundlage steht sichtbar: der Nutzer soll wissen, wonach gerechnet wird. */}
        <div className="hp-ef-grundlage">
          Grundlage: {panel.grundlage}
        </div>

        <div className="hp-ef-raster">
          {/* --- Eingabe ------------------------------------------------------------------- */}
          <section className="hp-ef-spalte">
            <h3 className="hp-ef-rubrik">
              Eingaben ({panel.felder.length})
            </h3>
            <div className="hp-ef-felder">
              {panel.felder.map((f) => (
                <label key={f.schluessel} className="hp-ef-feld">
                  <span className="hp-ef-feldkopf">
                    <span className="hp-ef-label">{f.label}</span>
                    {f.einheit && <span className="hp-ef-einheit">{f.einheit}</span>}
                    {f.pflicht && <span className="hp-ef-pflicht">Pflicht</span>}
                  </span>
                  {f.auswahl ? (
                    <select
                      value={werte[f.schluessel] ?? ''}
                      onChange={(e) => setWerte({ ...werte, [f.schluessel]: e.target.value })}
                      className="hp-ef-eingabe"
                    >
                      {f.auswahl.map((o) => <option key={o.wert} value={o.wert}>{o.label}</option>)}
                    </select>
                  ) : (
                    <input
                      type="number" inputMode="numeric"
                      value={werte[f.schluessel] ?? ''}
                      onChange={(e) => setWerte({ ...werte, [f.schluessel]: e.target.value })}
                      className="hp-ef-eingabe"
                    />
                  )}
                  {f.hinweis && (
                    <span className="hp-ef-feldhinweis">{f.hinweis}</span>
                  )}
                </label>
              ))}
            </div>

            <button
              type="button" onClick={rechnen} disabled={fehlt.length > 0}
              title={fehlt.length > 0 ? `Es fehlt: ${fehlt.map((f) => f.label).join(', ')}` : 'Auslegung berechnen'}
              style={{
                marginTop: 16, width: '100%', padding: '10px 14px', borderRadius: 11, cursor: fehlt.length > 0 ? GESPERRT_ZEIGER : 'pointer',
                border: 'none', background: fehlt.length > 0 ? GESPERRT_GRUND : T.accent, color: fehlt.length > 0 ? GESPERRT_BESCHRIFTUNG : T.surface,
                fontWeight: 700, fontSize: 14,
              }}
            >
              Berechnen
            </button>
            {/* Operanden-Gate: fehlt eine Pflichtangabe, wird nicht gerechnet — und es steht da, welche. */}
            {fehlt.length > 0 && (
              <div className="hp-ef-fehlt">
                Es fehlt: {fehlt.map((f) => f.label).join(', ')}. Ohne diese Angabe wird nicht gerechnet.
              </div>
            )}
          </section>

          {/* --- Ergebnis ------------------------------------------------------------------ */}
          <section className="hp-ef-spalte">
            <h3 className="hp-ef-rubrik">
              Ergebnis ({panel.ergebnisFelder.length})
            </h3>

            {ergebnis === null ? (
              <div className="hp-ef-leer">
                Noch nicht gerechnet. „Berechnen" ruft die Auslegung auf; die Zahlen kommen aus der
                Engine, nicht aus dieser Fläche.
              </div>
            ) : (
              <>
                {/* Gesamturteil — Wort UND Zeichen, nicht nur Farbe. */}
                {/* AUF-52 Scheibe C: **Die Plakette nur, wenn die Engine ein Bestehens-Merkmal
                    liefert.** `berechneUw` und `pvSchnellBelegung` rechnen Werte aus — sie bestehen
                    nichts. Eine Plakette „nicht bestanden" waere dort eine **erfundene Bewertung**;
                    die Huelle zeigt, was da ist, und wo nichts ist, steht nichts. */}
                {/* A-14: `keinGesamturteil` unterdrueckt die Plakette fuer EINE Engine (N-003).
                    Nicht `bestanden` entfernen — das Feld traegt Information; nur die Plakette,
                    die daraus einen NACHWEIS macht, faellt ersatzlos weg. */}
                {!panel.keinGesamturteil && typeof ergebnis.bestanden === 'boolean' && (
                  <div style={{
                    display: 'flex', alignItems: 'center', gap: 8, flexWrap: 'wrap', marginBottom: 12,
                    padding: '9px 12px', borderRadius: 10,
                    background: ergebnis.bestanden ? T.okSoft : T.errSoft,
                    color: ergebnis.bestanden ? T.okInk : T.errInk, fontWeight: 700, fontSize: 13,
                  }}>
                    <span>{ergebnis.bestanden ? '✓' : '✕'}</span>
                    <span>{ergebnis.bestanden ? 'Alle Prüfungen bestanden' : 'Eine Prüfung ist nicht bestanden'}</span>
                  </div>
                )}

                <div className="hp-ef-werte">
                  {panel.ergebnisFelder.map((f) => (
                    <div key={f.schluessel} className="hp-ef-wertzeile">
                      <span className="hp-ef-wertlabel">{f.label}</span>
                      <span className="hp-ef-wert">
                        {String((ergebnis as unknown as Record<string, unknown>)[f.schluessel] ?? '—')}
                      </span>
                      {f.einheit && <span className="hp-ef-werteinheit">{f.einheit}</span>}
                    </div>
                  ))}
                </div>

                {/* AUF-52 Scheibe A: **Prüfliste nur, wenn die Engine eine liefert.**
                    `SparrenBerechnung` gibt Ausnutzungsgrade und `bestanden` zurueck, aber KEINE
                    Prueflisten-Eintraege. Hier eine zu bilden, waere eine Rechnung im Panel — und
                    genau die verbietet AUF-33 §3a. Also zeigt die Huelle, was da ist, und wo nichts
                    ist, steht nichts. */}
                {ergebnis.pruefungen !== undefined && (<>
                <h3 className="hp-ef-rubrik-eng">
                  Prüfungen ({ergebnis.pruefungen.length})
                </h3>
                <div className="hp-ef-pruefungen">
                  {ergebnis.pruefungen.map((p) => {
                    // Der Schweregrad sagt, wie schwer eine VERLETZUNG wöge — nicht, ob sie
                    // vorliegt. Eine bestandene Prüfung als „✕ Fehler" zu zeigen, war der Fehler
                    // meiner ersten Fassung; die Sichtprobe hat ihn gefunden. Bestanden ⇒ ✓ erfüllt.
                    const a = p.bestanden
                      ? { zeichen: '✓', wort: 'erfüllt', token: 'okInk' as const }
                      : (SCHWERE_ANZEIGE[p.schwere] ?? SCHWERE_ANZEIGE.info);
                    return (
                      <div key={p.id} className="hp-ef-pruefzeile">
                        <span style={{ flex: '0 0 auto', fontWeight: 700, color: T[a.token] }}>
                          {a.zeichen} {a.wort}
                        </span>
                        <span style={{ flex: '1 1 160px', minWidth: 0, color: p.bestanden ? T.muted : T.ink, overflowWrap: 'anywhere' }}>
                          {p.meldung}
                        </span>
                      </div>
                    );
                  })}
                </div>
                </>)}
              </>
            )}
          </section>
        </div>
      </>
    </FlaechenHuelle>
  );
}
