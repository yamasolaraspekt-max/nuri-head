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
        <div style={{ margin: '12px 24px 0', fontSize: 12, color: T.faint, overflowWrap: 'anywhere' }}>
          Grundlage: {panel.grundlage}
        </div>

        <div style={{ padding: '18px 24px 24px', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 22 }}>
          {/* --- Eingabe ------------------------------------------------------------------- */}
          <section style={{ minWidth: 0 }}>
            <h3 style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint, margin: '0 0 10px' }}>
              Eingaben ({panel.felder.length})
            </h3>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: 12 }}>
              {panel.felder.map((f) => (
                <label key={f.schluessel} style={{ display: 'block', minWidth: 0 }}>
                  <span style={{ display: 'flex', alignItems: 'baseline', gap: 6, flexWrap: 'wrap', fontSize: 12.5, marginBottom: 5, overflowWrap: 'anywhere' }}>
                    <span style={{ color: T.ink }}>{f.label}</span>
                    {f.einheit && <span style={{ color: T.faint }}>{f.einheit}</span>}
                    {f.pflicht && <span style={{ color: T.muted, fontSize: 11 }}>Pflicht</span>}
                  </span>
                  {f.auswahl ? (
                    <select
                      value={werte[f.schluessel] ?? ''}
                      onChange={(e) => setWerte({ ...werte, [f.schluessel]: e.target.value })}
                      style={{ width: '100%', boxSizing: 'border-box', fontSize: 13, padding: '7px 9px', borderRadius: 9, border: `1px solid ${T.controlBorder}`, background: T.surface, color: T.ink }}
                    >
                      {f.auswahl.map((o) => <option key={o.wert} value={o.wert}>{o.label}</option>)}
                    </select>
                  ) : (
                    <input
                      type="number" inputMode="numeric"
                      value={werte[f.schluessel] ?? ''}
                      onChange={(e) => setWerte({ ...werte, [f.schluessel]: e.target.value })}
                      style={{ width: '100%', boxSizing: 'border-box', fontSize: 13, padding: '7px 9px', borderRadius: 9, border: `1px solid ${T.controlBorder}`, background: T.surface, color: T.ink }}
                    />
                  )}
                  {f.hinweis && (
                    <span style={{ display: 'block', marginTop: 4, fontSize: 11.5, color: T.faint, lineHeight: 1.35, overflowWrap: 'anywhere' }}>{f.hinweis}</span>
                  )}
                </label>
              ))}
            </div>

            <button
              type="button" onClick={rechnen} disabled={fehlt.length > 0}
              title={fehlt.length > 0 ? `Es fehlt: ${fehlt.map((f) => f.label).join(', ')}` : 'Auslegung berechnen'}
              style={{
                marginTop: 16, width: '100%', padding: '10px 14px', borderRadius: 11, cursor: fehlt.length > 0 ? 'not-allowed' : 'pointer',
                border: 'none', background: fehlt.length > 0 ? T.hair2 : T.accent, color: fehlt.length > 0 ? T.muted : T.surface,
                fontWeight: 700, fontSize: 14,
              }}
            >
              Berechnen
            </button>
            {/* Operanden-Gate: fehlt eine Pflichtangabe, wird nicht gerechnet — und es steht da, welche. */}
            {fehlt.length > 0 && (
              <div style={{ marginTop: 8, fontSize: 12, color: T.muted, overflowWrap: 'anywhere' }}>
                Es fehlt: {fehlt.map((f) => f.label).join(', ')}. Ohne diese Angabe wird nicht gerechnet.
              </div>
            )}
          </section>

          {/* --- Ergebnis ------------------------------------------------------------------ */}
          <section style={{ minWidth: 0 }}>
            <h3 style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint, margin: '0 0 10px' }}>
              Ergebnis ({panel.ergebnisFelder.length})
            </h3>

            {ergebnis === null ? (
              <div style={{ fontSize: 12.5, color: T.muted, lineHeight: 1.45, background: T.hair2, border: `1px solid ${T.hair}`, borderRadius: 12, padding: '11px 14px' }}>
                Noch nicht gerechnet. „Berechnen" ruft die Auslegung auf; die Zahlen kommen aus der
                Engine, nicht aus dieser Fläche.
              </div>
            ) : (
              <>
                {/* Gesamturteil — Wort UND Zeichen, nicht nur Farbe. */}
                <div style={{
                  display: 'flex', alignItems: 'center', gap: 8, flexWrap: 'wrap', marginBottom: 12,
                  padding: '9px 12px', borderRadius: 10,
                  background: ergebnis.bestanden ? T.okSoft : T.errSoft,
                  color: ergebnis.bestanden ? T.okInk : T.errInk, fontWeight: 700, fontSize: 13,
                }}>
                  <span>{ergebnis.bestanden ? '✓' : '✕'}</span>
                  <span>{ergebnis.bestanden ? 'Alle Prüfungen bestanden' : 'Eine Prüfung ist nicht bestanden'}</span>
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', gap: 7, marginBottom: 14 }}>
                  {panel.ergebnisFelder.map((f) => (
                    <div key={f.schluessel} style={{ display: 'flex', alignItems: 'baseline', gap: 8, flexWrap: 'wrap', fontSize: 13 }}>
                      <span style={{ flex: '1 1 140px', minWidth: 0, color: T.muted, overflowWrap: 'anywhere' }}>{f.label}</span>
                      <span style={{ fontWeight: 700, color: T.ink, fontVariantNumeric: 'tabular-nums' }}>
                        {String((ergebnis as unknown as Record<string, unknown>)[f.schluessel] ?? '—')}
                      </span>
                      {f.einheit && <span style={{ color: T.faint, fontSize: 11.5 }}>{f.einheit}</span>}
                    </div>
                  ))}
                </div>

                {/* Prüfliste — auch bei „nicht bestanden" bleiben die Zahlen oben stehen. */}
                <h3 style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint, margin: '0 0 8px' }}>
                  Prüfungen ({ergebnis.pruefungen.length})
                </h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                  {ergebnis.pruefungen.map((p) => {
                    // Der Schweregrad sagt, wie schwer eine VERLETZUNG wöge — nicht, ob sie
                    // vorliegt. Eine bestandene Prüfung als „✕ Fehler" zu zeigen, war der Fehler
                    // meiner ersten Fassung; die Sichtprobe hat ihn gefunden. Bestanden ⇒ ✓ erfüllt.
                    const a = p.bestanden
                      ? { zeichen: '✓', wort: 'erfüllt', token: 'okInk' as const }
                      : (SCHWERE_ANZEIGE[p.schwere] ?? SCHWERE_ANZEIGE.info);
                    return (
                      <div key={p.id} style={{ display: 'flex', alignItems: 'flex-start', gap: 8, fontSize: 12.5, lineHeight: 1.4 }}>
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
              </>
            )}
          </section>
        </div>
      </>
    </FlaechenHuelle>
  );
}
