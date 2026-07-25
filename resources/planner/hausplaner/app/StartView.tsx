/** Start-Launcher „Was möchtest du planen?" (v9). Projekt-Karten + Fachplaner-Hubs + Zuletzt. */
import React from 'react';
import { istAusloeser } from './dashboard/dialogFokus';
import { T, FACH, PROJ, ZULETZT, type FachHub } from './studioDaten';
import { Ikon } from './studioUi';

interface Props {
  /** Öffnet die geführte Planung ggf. bei einem Schritt (0-basiert). */
  onGuided: (schritt?: number) => void;
  /** Öffnet einen Konfigurator (autark); Name des Moduls. */
  onKonfigurator: (name: string, fenster?: boolean) => void;
}

const wrap: React.CSSProperties = { maxWidth: 1080, margin: '0 auto', padding: '20px 16px 70px' };
const kicker: React.CSSProperties = { fontSize: 12.5, fontWeight: 700, letterSpacing: '.14em', textTransform: 'uppercase', color: T.accent };
const h1: React.CSSProperties = { fontSize: 34, fontWeight: 800, letterSpacing: '-.02em', margin: '10px 0 8px' };
const lead: React.CSSProperties = { fontSize: 16, color: T.muted, maxWidth: 640, marginBottom: 14 };
const themeHead: React.CSSProperties = { display: 'flex', alignItems: 'baseline', gap: 12, margin: '0 4px 16px' };
// AUF-46: `repeat(3, 1fr)` erzwang drei Spalten auch bei 390 px — der Inhalt konnte nicht unter
// seine Mindestbreite, und die Karten ragten über den Rand. `auto-fit` legt so viele Spalten an,
// wie wirklich passen: drei bei 1440, eine bei 390.
const grid3: React.CSSProperties = { display: 'grid', gap: 16, gridTemplateColumns: 'repeat(auto-fit, minmax(230px, 1fr))' };
const cardBase: React.CSSProperties = { background: T.surface, borderRadius: 16, padding: 22, cursor: 'pointer', boxShadow: '0 1px 2px rgba(28,40,48,.05)', border: '1px solid transparent' };
const icoBox: React.CSSProperties = { width: 52, height: 52, borderRadius: 13, background: T.accentSoft, color: T.accentInk, display: 'grid', placeItems: 'center', marginBottom: 16 };

function Karte({ ico, titel, desc, onClick }: { ico: string; titel: string; desc: string; onClick: () => void }): React.ReactElement {
  const [hover, setHover] = React.useState(false);
  return (
    <div
      role="button" tabIndex={0} onClick={onClick}
      onKeyDown={(e) => { if (istAusloeser(e)) onClick(); }}
      onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}
      style={{ ...cardBase, transform: hover ? 'translateY(-3px)' : 'none', boxShadow: hover ? '0 10px 34px rgba(28,50,55,.10)' : cardBase.boxShadow, borderColor: hover ? '#dcebe9' : 'transparent', transition: 'transform .14s, box-shadow .14s, border-color .14s' }}
    >
      <span style={icoBox}><Ikon inhalt={ico} size={26} /></span>
      <div style={{ fontSize: 15.5, fontWeight: 700 }}>{titel}</div>
      <div style={{ fontSize: 13, color: T.muted, marginTop: 4, lineHeight: 1.45 }}>{desc}</div>
    </div>
  );
}

function HubKarte({ f, onKonfigurator }: { f: FachHub; onKonfigurator: Props['onKonfigurator'] }): React.ReactElement {
  const [hover, setHover] = React.useState(false);
  return (
    <div
      onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}
      style={{ ...cardBase, transform: hover ? 'translateY(-3px)' : 'none', boxShadow: hover ? '0 10px 34px rgba(28,50,55,.10)' : cardBase.boxShadow, borderColor: hover ? '#dcebe9' : 'transparent', transition: 'transform .14s, box-shadow .14s, border-color .14s' }}
    >
      {f.hub && <span style={{ display: 'inline-block', fontSize: 11, fontWeight: 700, letterSpacing: '.05em', textTransform: 'uppercase', color: T.accent, marginBottom: 2 }}>Hub</span>}
      <span style={{ ...icoBox, width: 40, height: 40, borderRadius: 11, marginBottom: 12 }}><Ikon inhalt={f.icon} size={20} /></span>
      <div style={{ fontSize: 14, fontWeight: 700 }}>{f.name}</div>
      <div style={{ fontSize: 13, color: T.muted, marginTop: 4, lineHeight: 1.45 }}>{f.desc}</div>
      {f.sub && (
        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8, marginTop: 16 }}>
          {f.sub.map((s) => (
            <span
              key={s[0]} role="button" tabIndex={0}
              onClick={() => onKonfigurator(s[0], s[1])}
              onKeyDown={(e) => { if (istAusloeser(e)) onKonfigurator(s[0], s[1]); }}
              style={{ fontSize: 12.5, fontWeight: 600, color: T.accentInk, background: T.accentSoft, borderRadius: 999, padding: '6px 13px', cursor: 'pointer' }}
            >{s[0]}</span>
          ))}
        </div>
      )}
    </div>
  );
}

export function StartView({ onGuided, onKonfigurator }: Props): React.ReactElement {
  return (
    <div style={{ minHeight: '100%', background: 'radial-gradient(1100px 420px at 82% -8%, #e9f4f2 0%, transparent 60%), radial-gradient(900px 400px at 5% 0%, #eef3e6 0%, transparent 55%)' }}>
      <div style={wrap}>
        <div style={kicker}>Neues Vorhaben</div>
        <div style={h1}>Was möchtest du planen?</div>
        <p style={lead}>Ein ganzes Gebäude — oder nur ein einzelnes Bauteil. Jeder Konfigurator führt dich Schritt für Schritt und läuft auch <b>autark</b>, ganz ohne Gebäude.</p>

        {/* Zuletzt bearbeitet */}
        <div style={{ display: 'flex', gap: 12, marginTop: 24, flexWrap: 'wrap' }}>
          {ZULETZT.map((z) => (
            <div
              key={z.name} role="button" tabIndex={0}
              onClick={() => (z.win ? onKonfigurator('Fenster', true) : onGuided(z.goto))}
              onKeyDown={(e) => { if (istAusloeser(e)) (z.win ? onKonfigurator('Fenster', true) : onGuided(z.goto)); }}
              style={{ display: 'flex', alignItems: 'center', gap: 12, background: T.surface, borderRadius: 14, padding: '12px 16px', boxShadow: '0 1px 2px rgba(28,40,48,.05)', border: '1px solid transparent', cursor: 'pointer', minWidth: 230 }}
            >
              <span style={{ width: 38, height: 38, borderRadius: 11, background: T.accentSoft, color: T.accentInk, display: 'grid', placeItems: 'center', flex: '0 0 auto' }}><Ikon inhalt={z.icon} size={20} /></span>
              <div><div style={{ fontSize: 13.5, fontWeight: 700 }}>{z.name}</div><div style={{ fontSize: 11.5, color: T.muted }}>{z.meta}</div></div>
            </div>
          ))}
        </div>

        {/* Projekt */}
        <div style={{ marginTop: 40 }}>
          <div style={themeHead}><span style={{ fontSize: 16, fontWeight: 700 }}>Projekt</span><span style={{ fontSize: 13, color: T.faint }}>Das komplette Vorhaben, alle Gewerke</span></div>
          <div style={grid3}>
            <Karte ico={PROJ[0].icon} titel="Sanierungsplan" desc="Bestand aufnehmen, Maßnahmen planen, Schritt für Schritt sanieren." onClick={() => onGuided(1)} />
            <Karte ico={PROJ[1].icon} titel="Hausplaner" desc="Neubau / Gesamtgebäude über alle Geschosse und Gewerke." onClick={() => onGuided(1)} />
            <Karte ico={PROJ[2].icon} titel="Weiterarbeiten" desc="Bestandsprojekt öffnen und fortsetzen." onClick={() => onGuided(1)} />
          </div>
        </div>

        <div style={{ marginTop: 34, display: 'inline-flex', alignItems: 'center', gap: 8, background: T.accentSoft, color: T.accentInk, borderRadius: 999, padding: '6px 14px', fontSize: 12.5, fontWeight: 600 }}>
          <Ikon inhalt='<path d="M20 6L9 17l-5-5"/>' size={16} />Fachplaner — jeder läuft autark, ohne Gebäude, und ist später verlustfrei ins Projekt übernehmbar
        </div>

        {/* Fachplaner */}
        <div style={{ marginTop: 40 }}>
          <div style={themeHead}><span style={{ fontSize: 16, fontWeight: 700 }}>Fachplaner</span><span style={{ fontSize: 13, color: T.faint }}>Direkt loslegen — ein Raum, ein Bauteil oder eine Anlage genügt</span></div>
          <div style={grid3}>
            {FACH.map((f) => <HubKarte key={f.name} f={f} onKonfigurator={onKonfigurator} />)}
          </div>
        </div>
      </div>
    </div>
  );
}
