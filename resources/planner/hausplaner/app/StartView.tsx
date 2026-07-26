/** Start-Launcher „Was möchtest du planen?" (v9). Projekt-Karten + Fachplaner-Hubs + Zuletzt. */
import React from 'react';
import { istAusloeser } from './dashboard/dialogFokus';
import { T, FACH, PROJ, type FachHub, type ZuletztEintrag } from './studioDaten';
import { ZustandBadge } from './studioUi';
import { Ikon } from './studioUi';

interface Props {
  /** Öffnet die geführte Planung ggf. bei einem Schritt (0-basiert). */
  onGuided: (schritt?: number) => void;
  /** Öffnet einen Konfigurator (autark); Name des Moduls. */
  onKonfigurator: (name: string, fenster?: boolean) => void;
  /**
   * AUF-40 Teil A — **die zuletzt bearbeiteten Projekte des Nutzers.** Leer heißt leer: dann steht
   * dort „Noch kein Projekt geöffnet.", **keine Beispielzeile**, die wie ein Projekt aussieht.
   * Der Grundzustand ist bewusst die leere Liste — beim ersten Start ist sie der Normalfall.
   * Gefüllt wird sie in **Teil B** (Route + Controller, bei Yama).
   */
  projekte?: readonly ZuletztEintrag[];
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

/**
 * AUF-40 Teil A — eine Karte **ohne** Ziel wird nicht klickbar gemacht, damit sie beschäftigt
 * aussieht. Sie trägt `in Entwicklung` (vorhandenes `ZustandBadge`, Muster AUF-25) und **den
 * Grund**, warum sie noch nirgendwohin führt. Ohne `onClick` ist sie keine Schaltfläche: keine
 * Rolle, kein Tastaturfokus, kein Zeiger — sonst wäre sie eine Schaltfläche, die nichts tut.
 */
function Karte({ ico, titel, desc, onClick, grund }: { ico: string; titel: string; desc: string; onClick?: () => void; grund?: string }): React.ReactElement {
  const [hover, setHover] = React.useState(false);
  if (!onClick) {
    return (
      <div style={{ ...cardBase, cursor: 'default' }} title={grund}>
        <span style={icoBox}><Ikon inhalt={ico} size={26} /></span>
        <div style={{ display: 'flex', alignItems: 'center', gap: 8, flexWrap: 'wrap' }}>
          <div style={{ fontSize: 15.5, fontWeight: 700 }}>{titel}</div>
          <ZustandBadge zustand="in_entwicklung" />
        </div>
        <div style={{ fontSize: 13, color: T.muted, marginTop: 4, lineHeight: 1.45 }}>{desc}</div>
        {grund && <div style={{ fontSize: 12, color: T.faint, marginTop: 6, lineHeight: 1.4 }}>{grund}</div>}
      </div>
    );
  }
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

export function StartView({ onGuided, onKonfigurator, projekte = [] }: Props): React.ReactElement {
  return (
    <div style={{ minHeight: '100%', background: 'radial-gradient(1100px 420px at 82% -8%, #e9f4f2 0%, transparent 60%), radial-gradient(900px 400px at 5% 0%, #eef3e6 0%, transparent 55%)' }}>
      <div style={wrap}>
        <div style={kicker}>Neues Vorhaben</div>
        <div style={h1}>Was möchtest du planen?</div>
        <p style={lead}>Ein ganzes Gebäude — oder nur ein einzelnes Bauteil. Jeder Konfigurator führt dich Schritt für Schritt und läuft auch <b>autark</b>, ganz ohne Gebäude.</p>

        {/* AUF-40 Teil A — **Zuletzt bearbeitet: der Bestand, nicht drei erfundene Namen.**
            Hier standen „EFH Mustermann", „Fenster-Angebot Hahn" und „Sanierung Musterstr. 5" —
            bei jedem Nutzer, auch beim allerersten Start. Die Liste kommt jetzt herein; solange
            sie leer ist, sagt die Fläche das, statt Beispiele zu zeigen, die wie Projekte aussehen.
            **Die echte Liste braucht eine Route und ist Teil B** (bei Yama). */}
        {projekte.length === 0 ? (
          <div style={{ marginTop: 24, background: T.surface, borderRadius: 14, padding: '14px 16px', boxShadow: '0 1px 2px rgba(28,40,48,.05)', maxWidth: 520 }}>
            <div style={{ fontSize: 13.5, fontWeight: 700 }}>Noch kein Projekt geöffnet.</div>
            <div style={{ fontSize: 12.5, color: T.muted, marginTop: 4 }}>
              Ein Vorhaben beginnt unten mit <b>Hausplaner</b> — oder mit einem der Fachplaner, die auch ohne Gebäude laufen.
            </div>
          </div>
        ) : (
        <div style={{ display: 'flex', gap: 12, marginTop: 24, flexWrap: 'wrap' }}>
          {projekte.map((z) => (
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
        )}

        {/* Projekt */}
        <div style={{ marginTop: 40 }}>
          <div style={themeHead}><span style={{ fontSize: 16, fontWeight: 700 }}>Projekt</span><span style={{ fontSize: 13, color: T.faint }}>Das komplette Vorhaben, alle Gewerke</span></div>
          <div style={grid3}>
            {/* AUF-40 Teil A — **drei Karten, drei Ziele.** Vorher riefen alle drei `onGuided(1)`
                auf: drei Versprechen, ein Ziel. „Weiterarbeiten" öffnete kein Bestandsprojekt,
                sondern begann bei Schritt 1. Wo ein Ziel fehlt, sagt die Karte das jetzt — statt
                auf den geführten Ablauf umgeleitet zu werden, damit sie beschäftigt aussieht. */}
            <Karte ico={PROJ[0].icon} titel="Sanierungsplan" desc="Bestand aufnehmen, Maßnahmen planen, Schritt für Schritt sanieren."
              grund="Der Sanierungsablauf ist ein eigener Weg — er unterscheidet sich noch nicht vom Neubau-Ablauf." />
            <Karte ico={PROJ[1].icon} titel="Hausplaner" desc="Neubau / Gesamtgebäude über alle Geschosse und Gewerke." onClick={() => onGuided(1)} />
            <Karte ico={PROJ[2].icon} titel="Weiterarbeiten" desc="Bestandsprojekt öffnen und fortsetzen."
              grund="Braucht die Liste der eigenen Projekte — die kommt aus dem Bestand und ist noch nicht angebunden." />
          </div>
        </div>

        <div style={{ marginTop: 34, display: 'inline-flex', alignItems: 'center', gap: 8, background: T.accentSoft, color: T.accentInk, borderRadius: 999, padding: '6px 14px', fontSize: 12.5, fontWeight: 600 }}>
          <Ikon inhalt='<path d="M20 6L9 17l-5-5"/>' size={16} />Fachplaner — jeder läuft autark, ohne Gebäude. Fenster, Türen, Treppen und Heizkörper setzt der Experte ins Gebäude; sonst entsteht eine Datei zum Herunterladen
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
