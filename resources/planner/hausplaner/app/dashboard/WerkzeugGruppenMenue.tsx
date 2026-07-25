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
import { T } from '../studioDaten';
import { resolveToolState } from '../tools/activation';
import type { AktivierungsKontext } from '../tools/toolTypes';
import { WERKZEUG_GRUPPEN, iconPfad } from './werkzeugGruppen';
import { werkzeugAnzeige, ANZEIGE_ZEICHEN, ANZEIGE_TEXT } from '../tools/werkzeugZustand';

interface Props {
  offen: string | null;
  setOffen: (id: string | null) => void;
  kontext: AktivierungsKontext;
  aktivId: string;
  angeheftet: ReadonlySet<string>;
  onAnheften: (toolId: string) => void;
}

export function WerkzeugGruppenMenue({ offen, setOffen, kontext, aktivId, angeheftet, onAnheften }: Props): React.ReactElement {
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
      {WERKZEUG_GRUPPEN.map((gruppe) => {
        const auf = offen === gruppe.id;
        return (
          <span key={gruppe.id} style={{ position: 'relative' }}>
            <button
              type="button" aria-expanded={auf} aria-haspopup="menu"
              title={`${gruppe.label} — ${gruppe.werkzeuge.length} Werkzeuge`}
              onClick={() => setOffen(auf ? null : gruppe.id)}
              style={{
                display: 'inline-flex', alignItems: 'center', gap: 4, padding: '5px 9px', fontSize: 12,
                borderRadius: 8, cursor: 'pointer', background: auf ? T.brandWash : 'transparent',
                border: `1px solid ${auf ? T.brandInk : 'transparent'}`,
                color: auf ? T.brandInk : T.ink, fontWeight: auf ? 700 : 600,
              }}
            >
              {gruppe.label}
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
                  maxHeight: '60vh', overflowY: 'auto', minWidth: 260, maxWidth: '90vw',
                }}
              >
                {gruppe.werkzeuge.map((tool) => {
                  const zustand = resolveToolState(tool, kontext);
                  const anzeige = werkzeugAnzeige(tool, {
                    aktivId, angeheftet, empfohlen: new Set<string>(), aktivierung: zustand,
                  });
                  const fest = angeheftet.has(tool.id);
                  return (
                    <div key={tool.id} role="menuitem" style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '5px 6px', borderRadius: 7 }}>
                      <img
                        src={iconPfad(tool)} alt="" aria-hidden width={18} height={18}
                        style={{ flex: '0 0 auto', opacity: zustand.enabled ? 1 : 0.45 }}
                        // Kante 3: fehlendes Icon ⇒ Platzhalter mit Grund, kein Absturz, kein Loch.
                        onError={(e) => {
                          const el = e.currentTarget;
                          el.style.visibility = 'hidden';
                          el.title = 'Icon-Datei fehlt';
                        }}
                      />
                      <span style={{ flex: 1, minWidth: 0, fontSize: 12.5, overflowWrap: 'anywhere', color: zustand.enabled ? T.ink : T.muted }}>
                        {tool.label}
                      </span>
                      {tool.shortcut && (
                        <span style={{ fontSize: 10.5, color: T.muted, border: `1px solid ${T.controlBorder}`, borderRadius: 4, padding: '1px 5px' }}>{tool.shortcut}</span>
                      )}
                      {/* Zustand als Zeichen UND Text — nie nur Farbe. Bei gesperrt steht der Grund dabei. */}
                      <span
                        title={zustand.enabled ? ANZEIGE_TEXT[anzeige] : `${ANZEIGE_TEXT[anzeige]}: ${zustand.reason ?? ''}`}
                        style={{ fontSize: 10.5, color: T.muted, flex: '0 0 auto' }}
                      >
                        {ANZEIGE_ZEICHEN[anzeige]} {anzeige === 'gesperrt' ? 'gesperrt' : 'in Entwicklung'}
                      </span>
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
