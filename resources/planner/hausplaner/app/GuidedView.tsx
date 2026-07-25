/** Geführte Planung — WizardBase (v9). Stepper + Fokus-Schrittkarte + Aufgabe-Panel + Navigation. */
import React from 'react';
import { istAusloeser } from './dashboard/dialogFokus';
import { T, STATUS_LABEL, type SchrittStatus, type Fahrschritt } from './studioDaten';
import type { KonfigArt } from './ConfigWizard';
import { Ikon } from './studioUi';

interface Props {
  schritt: number;
  setSchritt: (i: number) => void;
  onExperte: () => void;
  onKonfigurator: (art: KonfigArt) => void;
  modell: { geschosse: number; fenster: number; tuer: number; treppe: number };
  /** AUF-39/L5: die elf Schritte, abgeleitet aus dem Modell (`dashboard/fahrschritte.ts`). */
  schritte: readonly Fahrschritt[];
}

const badgeFarbe: Record<SchrittStatus, { bg: string; fg: string }> = {
  ok: { bg: T.okSoft, fg: T.okInk }, prog: { bg: T.infoSoft, fg: T.infoInk },
  warn: { bg: T.warnSoft, fg: T.warnInk }, open: { bg: T.hair2, fg: T.muted },
};
const checkFarbe: Record<SchrittStatus, { bg: string; fg: string; sym: string }> = {
  ok: { bg: T.okSoft, fg: T.okInk, sym: '✓' }, warn: { bg: T.warnSoft, fg: T.warnInk, sym: '!' },
  open: { bg: T.hair2, fg: T.faint, sym: '' }, prog: { bg: T.infoSoft, fg: T.infoInk, sym: '' },
};

export function GuidedView({ schritt, setSchritt, onExperte, onKonfigurator, modell, schritte }: Props): React.ReactElement {
  // AUF-39/L5: Die Schritte kommen als Daten herein — abgeleitet aus dem Modell, nicht mehr aus
  // hartkodierten Demo-Werten. Die Ansicht selbst ist unverändert; sie hat nur eine andere Quelle.
  const STEPS = schritte;
  const s = STEPS[schritt];
  const n = STEPS.length;

  return (
    <div style={{ maxWidth: 1180, margin: '0 auto', padding: '14px 30px 30px', minHeight: '100%', display: 'flex', flexDirection: 'column' }}>
      {/* AUF-46: Die zweite Spalte war mit `1fr 320px` fest. Bei 390 px passte `1fr + 320 + Lücke`
          nicht mehr; das Aufgaben-`aside` legte sich über den Inhalt und fing die Zeigerereignisse
          ab — eine sichtbare, aber tote Schaltfläche. `auto-fit` stapelt die Spalten stattdessen
          untereinander, sobald der Platz fehlt. */}
      {/* Stepper */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 6, overflow: 'auto', padding: '12px 4px 20px' }}>
        {STEPS.map((st, i) => {
          const aktiv = i === schritt;
          const rn = st.status === 'ok' ? T.ok : (st.status === 'warn' && !aktiv ? T.warn : (aktiv ? T.accent : T.surface));
          const rnInk = (st.status === 'ok' || aktiv || (st.status === 'warn' && !aktiv)) ? T.surface : T.muted;
          const sym = st.status === 'ok' ? '✓' : String(i + 1);
          return (
            <React.Fragment key={st.titel}>
              <div
                role="button" tabIndex={0} onClick={() => setSchritt(i)}
                onKeyDown={(e) => { if (istAusloeser(e)) setSchritt(i); }}
                style={{ display: 'flex', alignItems: 'center', gap: 10, flex: '0 0 auto', cursor: 'pointer', opacity: aktiv ? 1 : 0.6 }}
              >
                <span style={{ width: 30, height: 30, borderRadius: '50%', background: rn, color: rnInk, display: 'grid', placeItems: 'center', fontWeight: 700, fontSize: 13, boxShadow: '0 1px 2px rgba(28,40,48,.05)' }}>{sym}</span>
                <span style={{ fontSize: 13, fontWeight: 600, color: aktiv ? T.ink : T.muted, whiteSpace: 'nowrap' }}>{st.titel}</span>
              </div>
              {i < n - 1 && <span style={{ width: 26, height: 2, background: T.hair, borderRadius: 2, margin: '0 2px', flex: '0 0 auto' }} />}
            </React.Fragment>
          );
        })}
      </div>

      {/* Board: Fokus + Seitenpanel */}
      <div style={{ flex: 1, display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 20, minHeight: 0 }}>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16, minWidth: 0 }}>
          <div style={{ background: T.surface, borderRadius: 22, padding: '26px 28px', boxShadow: '0 1px 2px rgba(28,40,48,.05)' }}>
            <div style={{ fontSize: 12.5, fontWeight: 700, letterSpacing: '.1em', textTransform: 'uppercase', color: T.accent }}>Schritt {schritt + 1} von {n}</div>
            <div style={{ fontSize: 24, fontWeight: 800, letterSpacing: '-.01em', margin: '6px 0 4px' }}>{s.titel}</div>
            <div style={{ color: T.muted, fontSize: 14.5 }}>{s.hinweis}</div>
            <div>
              <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7, fontSize: 12.5, fontWeight: 600, borderRadius: 999, padding: '6px 13px', marginTop: 14, background: badgeFarbe[s.status].bg, color: badgeFarbe[s.status].fg }}>{STATUS_LABEL[s.status]}</span>
              <span style={{ marginLeft: 8, fontSize: 12, color: T.muted, fontVariantNumeric: 'tabular-nums' }}>Im Modell: {modell.geschosse} Geschoss{modell.geschosse === 1 ? '' : 'e'} · {modell.fenster} Fenster · {modell.tuer} Tür{modell.tuer === 1 ? '' : 'en'} · {modell.treppe} Treppe{modell.treppe === 1 ? '' : 'n'}</span>
            </div>
            <div style={{ marginTop: 18, display: 'flex', flexDirection: 'column', gap: 2 }}>
              {s.checks.map((c) => {
                const cf = checkFarbe[c.status];
                return (
                  <div key={c.text} style={{ display: 'flex', alignItems: 'flex-start', gap: 12, padding: '9px 0', fontSize: 14 }}>
                    <span style={{ width: 22, height: 22, borderRadius: '50%', flex: '0 0 auto', display: 'grid', placeItems: 'center', marginTop: 1, background: cf.bg, color: cf.fg, fontSize: 12, fontWeight: 700 }}>{cf.sym}</span>
                    <span style={{ color: c.status === 'open' ? T.faint : T.ink }}>{c.text}</span>
                  </div>
                );
              })}
            </div>
            {s.titel.includes('Türen') && (
              <div style={{ display: 'flex', gap: 8, marginTop: 16, flexWrap: 'wrap' }}>
                {([['fenster', 'Fenster'], ['tuer', 'Tür'], ['treppe', 'Treppe']] as const).map(([a, l]) => (
                  <button key={a} type="button" onClick={() => onKonfigurator(a)}
                    style={{ border: `1px solid ${T.hair}`, background: T.surface, color: T.ink, fontWeight: 600, fontSize: 13, padding: '9px 16px', borderRadius: 11, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 7 }}>
                    <Ikon inhalt='<rect x="4" y="4" width="16" height="16" rx="1"/><path d="M12 4v16M4 12h16"/>' size={15} />{l} konfigurieren
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Fokus-Canvas (Muster-Grundriss) */}
          <div style={{ flex: 1, minHeight: 280, background: T.surface, borderRadius: 22, boxShadow: '0 1px 2px rgba(28,40,48,.05)', position: 'relative', overflow: 'hidden' }}>
            <span style={{ position: 'absolute', top: 14, left: 16, fontSize: 12, color: T.muted, background: 'rgba(255,255,255,.7)', padding: '3px 10px', borderRadius: 8 }}>{schritt <= 2 ? 'Erdgeschoss · Grundriss · 1:50' : s.titel}</span>
            <svg style={{ position: 'absolute', inset: 0, width: '100%', height: '100%' }} viewBox="0 0 640 360" preserveAspectRatio="xMidYMid meet">
              <g>
                <polygon points="150,70 490,70 490,80 160,80" fill={T.canvasWallFill} />
                <polygon points="150,70 160,80 160,270 150,280" fill={T.canvasWallFill} />
                <polygon points="490,70 490,280 480,270 480,80" fill={T.canvasWallFill} />
                <polygon points="150,280 490,280 480,270 160,270" fill={T.canvasWallFill} />
                <polygon points="330,80 340,80 340,270 330,270" fill={T.canvasWallFill} />
              </g>
              <g stroke="#9aa4af" strokeWidth={3}><rect x="220" y="70" width="64" height="10" fill={T.surface} /><line x1="220" y1="75" x2="284" y2="75" /></g>
              <text x="240" y="185" textAnchor="middle" fontFamily="Inter" fontSize="13" fill="#7c8590">Wohnen</text>
              <text x="410" y="185" textAnchor="middle" fontFamily="Inter" fontSize="12" fill="#aab2bb">Küche</text>
            </svg>
            <div style={{ position: 'absolute', left: 16, right: 16, bottom: 14, background: 'rgba(20,30,34,.92)', color: '#eef3f2', borderRadius: 12, padding: '11px 15px', fontSize: 13, display: 'flex', gap: 9, alignItems: 'center' }}>
              <span style={{ color: '#7fd8d3' }}><Ikon inhalt='<path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/>' size={16} /></span>
              <span>{s.hinweis}</span>
            </div>
          </div>
        </div>

        {/* Seitenpanel: Aufgabe + Empfehlung + Erweiterte Bearbeitung */}
        <aside style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          <div style={{ background: T.surface, borderRadius: 22, padding: 20, boxShadow: '0 1px 2px rgba(28,40,48,.05)' }}>
            <h4 style={{ margin: '0 0 12px', fontSize: 12.5, fontWeight: 700, letterSpacing: '.09em', textTransform: 'uppercase', color: T.faint }}>Aufgabe</h4>
            {s.aufgaben.map((a) => (
              <div key={a.titel} style={{ display: 'flex', flexDirection: 'column', gap: 5, padding: '12px 0', borderBottom: `1px solid ${T.hair2}` }}>
                <div style={{ fontSize: 13.5, fontWeight: 600, display: 'flex', alignItems: 'center', gap: 8, color: a.warn ? T.warnInk : T.ink }}>
                  {a.warn && <Ikon inhalt='<path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>' size={16} />}
                  {a.titel}
                </div>
                {a.detail && <div style={{ fontSize: 12.5, color: T.muted }}>{a.detail}</div>}
              </div>
            ))}
          </div>
          {s.empfehlung && (
            <div style={{ background: T.accentSoft, borderRadius: 16, padding: 18 }}>
              <div style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', color: T.accentInk }}>Nächste empfohlene Aktion</div>
              <div style={{ fontSize: 15, fontWeight: 700, margin: '6px 0 14px', color: '#0a4f4d' }}>{s.empfehlung.titel}</div>
              <button type="button" onClick={() => (s.empfehlung?.cfg ? onKonfigurator('fenster') : undefined)} style={{ width: '100%', border: 0, background: T.accent, color: T.surface, fontWeight: 700, fontSize: 14, padding: 11, borderRadius: 11, cursor: 'pointer' }}>{s.empfehlung.aktion}</button>
            </div>
          )}
          <button type="button" onClick={onExperte} style={{ width: '100%', border: '1px dashed #d3dbdb', background: 'transparent', color: T.muted, fontWeight: 600, fontSize: 13, padding: 12, borderRadius: 12, cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 8 }}>
            <Ikon inhalt='<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>' size={16} /> Erweiterte Bearbeitung
          </button>
        </aside>
      </div>

      {/* Navigation */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '18px 2px 2px' }}>
        <button type="button" onClick={() => setSchritt(Math.max(0, schritt - 1))} style={{ border: `1px solid ${T.hair}`, background: T.surface, color: T.ink, fontWeight: 600, fontSize: 14, padding: '11px 20px', borderRadius: 12, cursor: 'pointer', boxShadow: '0 1px 2px rgba(28,40,48,.05)' }}>Zurück</button>
        <span style={{ flex: 1 }} />
        <span style={{ fontSize: 13, color: T.muted }}>Schritt {schritt + 1} von {n}</span>
        <button type="button" onClick={() => setSchritt(Math.min(n - 1, schritt + 1))} style={{ border: 0, background: T.brand, color: T.surface, fontWeight: 600, fontSize: 14, padding: '11px 26px', borderRadius: 12, cursor: 'pointer' }}>Weiter</button>
      </div>
    </div>
  );
}
