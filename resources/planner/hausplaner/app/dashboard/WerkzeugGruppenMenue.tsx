/**
 * I4 (AUF-21) — die 22 Kategorie-Gruppen in der oberen Werkzeugleiste.
 *
 * Nach I2 lagen 110 Fach-Werkzeuge als Daten im Katalog, und **keines** war erreichbar. Hier werden
 * sie sichtbar: je Kategorie ein Menü, darin Icon, Label, Kürzel und der **Zustand als Farbe UND
 * Text**. Die linke Leiste bleibt schlank — dort stehen nur die Pflichtwerkzeuge und was der Nutzer
 * anheftet (★). Eine Leiste mit 110 Einträgen wäre keine Leiste mehr.
 *
 * **Ehrlichkeit, nicht verhandelbar:** Kein Werkzeug hier hat heute einen Handler. Jeder Eintrag
 * sagt das mit `ANZEIGE_TEXT`; ein Werkzeug, das so aussieht, als könnte es etwas, ist ein Fehler.
 * Der Unterschied zu den 15 DTP-Werkzeugen, die I2 entfernt hat: **diese hier kommen wirklich.**
 *
 * **Modulebene, nicht im Rumpf von `HausplanerApp`** — Befund B1: Menüeinträge und Anheft-Sterne
 * sind fokussierbar, und `onMouseMove` rendert die App in Mausbewegungs-Frequenz. Eine im Rumpf
 * definierte Komponente verlöre bei jeder Bewegung Fokus und Tastaturbedienung.
 */
import React, { useEffect, useRef } from 'react';
import { GESPERRT_DECKKRAFT } from './gesperrtStil';
import { T } from '../studioDaten';
import { resolveToolState } from '../tools/activation';
import type { AktivierungsKontext } from '../tools/toolTypes';
import { WERKZEUG_GRUPPEN, iconPfad, type WerkzeugGruppe } from './werkzeugGruppen';
import { werkzeugAnzeige, ANZEIGE_ZEICHEN, ANZEIGE_TEXT } from '../tools/werkzeugZustand';

interface Props {
  offen: string | null;
  setOffen: (id: string | null) => void;
  kontext: AktivierungsKontext;
  aktivId: string;
  angeheftet: ReadonlySet<string>;
  onAnheften: (toolId: string) => void;
  /**
   * AUF-34: die anzuzeigenden Gruppen — seit den Arbeitsbereichen **nicht mehr alle**, sondern die
   * des gewählten Bereichs. Als Prop und nicht aus dem Store gelesen: die Komponente bleibt dumm
   * und ist ohne Store testbar. Ohne Angabe alle — dann verhält sie sich wie vor AUF-34.
   */
  gruppen?: readonly WerkzeugGruppe[];
}

export function WerkzeugGruppenMenue({ offen, setOffen, kontext, aktivId, angeheftet, onAnheften, gruppen = WERKZEUG_GRUPPEN }: Props): React.ReactElement {
  const huelle = useRef<HTMLSpanElement>(null);

  // Klick daneben und Esc schließen das Menü — sonst bleibt es beim Weiterarbeiten im Weg stehen.
  useEffect(() => {
    if (!offen) return undefined;
    const beiKlick = (e: MouseEvent): void => {
      if (huelle.current && !huelle.current.contains(e.target as Node)) setOffen(null);
    };
    const beiTaste = (e: KeyboardEvent): void => { if (e.key === 'Escape') setOffen(null); };
    document.addEventListener('mousedown', beiKlick);
    document.addEventListener('keydown', beiTaste);
    return () => { document.removeEventListener('mousedown', beiKlick); document.removeEventListener('keydown', beiTaste); };
  }, [offen, setOffen]);

  return (
    <span ref={huelle} style={{ display: 'inline-flex', alignItems: 'center', gap: 2, flexWrap: 'wrap', position: 'relative' }}>
      {gruppen.map((gruppe) => {
        const auf = offen === gruppe.id;
        return (
          <span key={gruppe.id} style={{ position: 'relative' }}>
            <button
              type="button" aria-expanded={auf} aria-haspopup="menu"
              // AUF-34: der Knopf trägt die KURZform, der Tooltip das volle Themen-Label. Elf volle
              // Labels („Prüfung, Zusammenarbeit & Revision") nebeneinander sprengen jede Breite —
              // und genau die mehrzeilige Leiste ist der Mangel, den dieser Posten behebt.
              title={`${gruppe.label} — ${gruppe.werkzeuge.length} Werkzeuge`}
              onClick={() => setOffen(auf ? null : gruppe.id)}
              style={{
                display: 'inline-flex', alignItems: 'center', gap: 4, padding: '5px 9px', fontSize: 12,
                borderRadius: 8, cursor: 'pointer', background: auf ? T.brandWash : 'transparent',
                border: `1px solid ${auf ? T.brandInk : 'transparent'}`,
                color: auf ? T.brandInk : T.ink, fontWeight: auf ? 700 : 600,
              }}
            >
              {gruppe.kurz}
              <span style={{ fontSize: 10, color: T.muted }}>▾</span>
            </button>

            {auf && (
              <div
                role="menu" aria-label={gruppe.label}
                style={{
                  position: 'absolute', top: '100%', left: 0, zIndex: 60, marginTop: 4,
                  background: T.surface, border: `1px solid ${T.hair}`, borderRadius: 10,
                  boxShadow: `0 10px 28px ${T.canvasWallGhost}`, padding: 6,
                  // Kante 1: 15 Einträge sprengen das Menü. Es scrollt und bricht um — es kappt nicht.
                  maxHeight: '60vh', overflowY: 'auto', maxWidth: '90vw',
                  // AUF-34 / Kriterium 12: 260 px waren zu wenig. In „Bearbeiten" brach die
                  // Beschriftung Buchstabe für Buchstabe um — „K-o-p-i-e-r-e-n" untereinander —,
                  // weil Kürzel-Kästchen, Zustandstext und Stern die Breite nahmen und der Text
                  // ausweichen musste. Ein senkrecht stehendes Wort ist unlesbar; das ist derselbe
                  // Fehler wie Kappung, nur andersherum. Deshalb: mehr Grundbreite UND der
                  // Zustandstext in eine zweite Zeile (unten), statt in derselben Zeile zu drängeln.
                  minWidth: 320,
                }}
              >
                {gruppe.werkzeuge.map((tool) => {
                  const zustand = resolveToolState(tool, kontext);
                  const anzeige = werkzeugAnzeige(tool, {
                    aktivId, angeheftet, empfohlen: new Set<string>(), aktivierung: zustand,
                  });
                  const fest = angeheftet.has(tool.id);
                  return (
                    <div key={tool.id} role="menuitem" aria-disabled={!zustand.enabled} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '5px 6px', borderRadius: 7 }}>
                      <img
                        src={iconPfad(tool)} alt="" aria-hidden width={18} height={18}
                        style={{ flex: '0 0 auto', opacity: zustand.enabled ? 1 : GESPERRT_DECKKRAFT }}
                        // Kante 3: fehlendes Icon ⇒ Platzhalter mit Grund, kein Absturz, kein Loch.
                        onError={(e) => {
                          const el = e.currentTarget;
                          el.style.visibility = 'hidden';
                          el.title = 'Icon-Datei fehlt';
                        }}
                      />
                      {/* AUF-34 / Kriterium 12: Beschriftung und Zustand stehen UNTEREINANDER, nicht
                          nebeneinander. Vorher teilten sich Label, Kürzel, Zustandstext und Stern
                          eine Zeile — dem Label blieben ~60 px, und `overflowWrap: 'anywhere'`
                          brach es dann Buchstabe für Buchstabe um. `break-word` statt `anywhere`
                          bricht nur, wenn ein ganzes Wort nicht passt; bei 320 px Menübreite
                          passiert das nicht mehr. */}
                      <span style={{ flex: 1, minWidth: 0, display: 'flex', flexDirection: 'column', gap: 1 }}>
                        <span style={{ fontSize: 12.5, overflowWrap: 'break-word', color: zustand.enabled ? T.ink : T.muted }}>
                          {tool.label}
                        </span>
                        {/* Zustand als Zeichen UND Text — nie nur Farbe. Bei gesperrt steht der Grund dabei. */}
                        <span
                          title={zustand.enabled ? ANZEIGE_TEXT[anzeige] : `${ANZEIGE_TEXT[anzeige]}: ${zustand.reason ?? ''}`}
                          style={{ fontSize: 10.5, color: T.muted }}
                        >
                          {ANZEIGE_ZEICHEN[anzeige]} {anzeige === 'gesperrt' ? 'gesperrt' : 'in Entwicklung'}
                        </span>
                      </span>
                      {tool.shortcut && (
                        <span style={{ flex: '0 0 auto', fontSize: 10.5, color: T.muted, border: `1px solid ${T.controlBorder}`, borderRadius: 4, padding: '1px 5px', whiteSpace: 'nowrap' }}>{tool.shortcut}</span>
                      )}
                      <button
                        type="button"
                        onClick={() => onAnheften(tool.id)}
                        title={fest ? `„${tool.label}" aus der linken Leiste lösen` : `„${tool.label}" an die linke Leiste anheften`}
                        aria-pressed={fest}
                        style={{
                          flex: '0 0 auto', width: 24, height: 24, borderRadius: 6, cursor: 'pointer',
                          border: `1px solid ${fest ? T.brandInk : T.controlBorder}`,
                          background: fest ? T.brandWash : T.surface, color: fest ? T.brandInk : T.muted, fontSize: 12,
                        }}
                      >
                        ★
                      </button>
                    </div>
                  );
                })}
              </div>
            )}
          </span>
        );
      })}
    </span>
  );
}
