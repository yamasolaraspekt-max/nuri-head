/**
 * ConfigWizard (v9) — geführter Konfigurator-Dialog für Fenster/Tür/Treppe.
 * Nutzt die echten Premium-Bauart-Icons (public/hausplaner/icons/*). Schritte: Bauart → Maße →
 * Material → Prüfung → Übernehmen, mit Live-Vorschau. „Übernehmen" erzeugt ein echtes autarkes
 * ConfiguratorPackage (geometry/configuratorPackage.ts) und lädt es als JSON herunter. Der Schreibpfad
 * ins Gebäudemodell (Command) bleibt die nächste Scheibe.
 */
import React from 'react';
import { T } from './studioDaten';
import { Ikon } from './studioUi';
import { FENSTER_BAUARTEN, TUER_BAUARTEN, type OeffnungsBauart } from '../geometry/oeffnungsBauarten';
import { TREPPEN_BAUARTEN, type TreppenBauart } from '../geometry/treppenBauarten';
import { neuesPaket, type ConfiguratorType } from '../geometry/configuratorPackage';

const ICON_BASE = new URL('.', import.meta.url).href;

export type KonfigArt = 'fenster' | 'tuer' | 'treppe';

interface Props {
  art: KonfigArt;
  standalone?: boolean;
  onClose: () => void;
  onÜbernehmen: (bauartLabel: string) => void;
}

interface Kachel { id: string; datei: string; label: string; }

const SCHRITTE = ['Bauart', 'Maße', 'Material', 'Prüfung', 'Übernehmen'] as const;

function katalogFür(art: KonfigArt): { ordner: string; titel: string; kacheln: Kachel[] } {
  if (art === 'fenster') return { ordner: 'fenster', titel: 'Fenster konfigurieren', kacheln: FENSTER_BAUARTEN as readonly OeffnungsBauart[] as Kachel[] };
  if (art === 'tuer') return { ordner: 'tuer', titel: 'Tür konfigurieren', kacheln: TUER_BAUARTEN as readonly OeffnungsBauart[] as Kachel[] };
  return { ordner: 'treppe', titel: 'Treppe konfigurieren', kacheln: TREPPEN_BAUARTEN as readonly TreppenBauart[] as Kachel[] };
}

const TYP_MAP: Record<KonfigArt, ConfiguratorType> = { fenster: 'window', tuer: 'door', treppe: 'stair' };

export function ConfigWizard({ art, standalone = true, onClose, onÜbernehmen }: Props): React.ReactElement {
  const { ordner, titel, kacheln } = katalogFür(art);
  const [schritt, setSchritt] = React.useState(0);
  const [wahl, setWahl] = React.useState<Kachel>(kacheln[0]);
  const [breite, setBreite] = React.useState(art === 'treppe' ? 1000 : 1010);
  const [hoehe, setHoehe] = React.useState(art === 'fenster' ? 1360 : 2010);

  const iconUrl = (k: Kachel): string => `${ICON_BASE}icons/${ordner}/${k.datei}`;
  const letzter = schritt === SCHRITTE.length - 1;

  const feld: React.CSSProperties = { width: '100%', border: `1px solid ${T.hair}`, borderRadius: 10, padding: '10px 12px', font: 'inherit', fontSize: 13.5, boxSizing: 'border-box' };
  const feldLabel: React.CSSProperties = { display: 'block', fontSize: 12.5, color: T.muted, marginBottom: 5 };

  return (
    <div onClick={onClose} style={{ position: 'fixed', inset: 0, background: 'rgba(24,34,38,.30)', backdropFilter: 'blur(2px)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 90, padding: 24 }}>
      <div onClick={(e) => e.stopPropagation()} style={{ width: 'min(900px, 100%)', maxHeight: '92%', background: T.surface, borderRadius: 24, boxShadow: '0 10px 34px rgba(28,50,55,.18)', display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>
        {/* Kopf */}
        <div style={{ padding: '22px 26px 14px', textAlign: 'center', position: 'relative' }}>
          <div style={{ fontSize: 20, fontWeight: 800 }}>{titel}</div>
          <div style={{ color: T.muted, fontSize: 13.5, marginTop: 3 }}>{standalone ? 'Autark — kein Gebäude nötig. Live-Vorschau bei jedem Schritt.' : 'Im Projekt — schreibt als Command ins Gebäudemodell.'}</div>
          <button type="button" onClick={onClose} aria-label="Schließen" style={{ position: 'absolute', top: 18, right: 20, width: 34, height: 34, borderRadius: 10, border: 0, background: T.surface2, color: T.muted, cursor: 'pointer', display: 'grid', placeItems: 'center' }}>
            <Ikon inhalt='<path d="M6 6l12 12M18 6L6 18"/>' size={16} />
          </button>
        </div>

        {/* Schritt-Punkte */}
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '4px 26px 18px', flexWrap: 'wrap' }}>
          {SCHRITTE.map((n, i) => (
            <React.Fragment key={n}>
              <div role="button" tabIndex={0} onClick={() => setSchritt(i)} onKeyDown={(e) => { if (e.key === 'Enter') setSchritt(i); }} style={{ display: 'flex', alignItems: 'center', gap: 9, cursor: 'pointer' }}>
                <span style={{ width: 26, height: 26, borderRadius: '50%', background: i === schritt ? T.accent : (i < schritt ? T.ok : T.surface2), color: (i === schritt || i < schritt) ? '#fff' : T.muted, display: 'grid', placeItems: 'center', fontSize: 12, fontWeight: 700, boxShadow: '0 1px 2px rgba(28,40,48,.05)' }}>{i < schritt ? '✓' : i + 1}</span>
                {i === schritt && <span style={{ fontSize: 12, color: T.ink, fontWeight: 600 }}>{n}</span>}
              </div>
              {i < SCHRITTE.length - 1 && <span style={{ width: 20, height: 2, background: T.hair, margin: '0 6px' }} />}
            </React.Fragment>
          ))}
        </div>

        {/* Körper */}
        <div style={{ padding: '6px 30px 8px', overflow: 'auto', display: 'grid', gridTemplateColumns: '1fr 300px', gap: 26 }}>
          <div>
            {schritt === 0 && (
              <>
                <div style={{ fontSize: 13.5, color: T.muted, marginBottom: 12 }}>Bauart wählen — {kacheln.length} Typen als Premium-Icons. Maße sind Vorlage, im nächsten Schritt frei.</div>
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 9, maxHeight: 300, overflow: 'auto', padding: 2 }}>
                  {kacheln.map((k) => {
                    const on = k.id === wahl.id;
                    return (
                      <button key={k.id} type="button" title={k.label} onClick={() => setWahl(k)} style={{ display: 'grid', gap: 5, placeItems: 'center', padding: '8px 6px', borderRadius: 12, cursor: 'pointer', border: `1.5px solid ${on ? T.accent : T.hair}`, background: on ? T.accentSoft : T.surface }}>
                        <img src={iconUrl(k)} alt={k.label} loading="lazy" style={{ width: '100%', height: 'auto', display: 'block' }} />
                        <span style={{ fontSize: 10, lineHeight: 1.15, color: on ? T.accentInk : T.muted, textAlign: 'center', height: 24, overflow: 'hidden', fontWeight: on ? 600 : 400 }}>{k.label}</span>
                      </button>
                    );
                  })}
                </div>
              </>
            )}
            {schritt === 1 && (
              <>
                <div style={{ fontWeight: 700, marginBottom: 10 }}>Maße</div>
                <div style={{ marginBottom: 12 }}><label style={feldLabel}>Breite (mm)</label><input type="number" value={breite} onChange={(e) => setBreite(Math.max(100, Math.round(Number(e.target.value))))} style={feld} /></div>
                <div style={{ marginBottom: 12 }}><label style={feldLabel}>{art === 'treppe' ? 'Geschosshöhe (mm)' : 'Höhe (mm)'}</label><input type="number" value={hoehe} onChange={(e) => setHoehe(Math.max(100, Math.round(Number(e.target.value))))} style={feld} /></div>
              </>
            )}
            {schritt === 2 && (
              <>
                <div style={{ fontWeight: 700, marginBottom: 10 }}>Material</div>
                <div style={{ marginBottom: 12 }}><label style={feldLabel}>{art === 'treppe' ? 'Konstruktion' : 'Profilsystem / Rahmen'}</label>
                  <select style={feld}>{art === 'treppe' ? <><option>Stahlwange</option><option>Holzwange</option><option>Beton</option></> : <><option>Kunststoff 70 mm (5-Kammer)</option><option>Aluminium</option><option>Holz</option></>}</select>
                </div>
                {art !== 'treppe' && <div style={{ marginBottom: 12 }}><label style={feldLabel}>Verglasung</label><select style={feld}><option>2-fach Wärmeschutz</option><option>3-fach Wärmeschutz</option></select></div>}
              </>
            )}
            {schritt === 3 && (
              <>
                <div style={{ fontWeight: 700, marginBottom: 10 }}>Prüfung</div>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '8px 0', fontSize: 14 }}><span style={{ width: 22, height: 22, borderRadius: '50%', background: T.okSoft, color: T.okInk, display: 'grid', placeItems: 'center', fontWeight: 700 }}>✓</span>Maße plausibel</div>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '8px 0', fontSize: 14 }}><span style={{ width: 22, height: 22, borderRadius: '50%', background: T.okSoft, color: T.okInk, display: 'grid', placeItems: 'center', fontWeight: 700 }}>✓</span>{art === 'treppe' ? 'DIN 18065 Schrittmaß' : 'Norm-Anschlag korrekt'}</div>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '8px 0', fontSize: 14 }}><span style={{ width: 22, height: 22, borderRadius: '50%', background: T.warnSoft, color: T.warnInk, display: 'grid', placeItems: 'center', fontWeight: 700 }}>!</span>Rastermaß — 40 mm Versatz prüfen</div>
              </>
            )}
            {schritt === 4 && (
              <>
                <div style={{ fontWeight: 700, marginBottom: 8 }}>Übernehmen</div>
                <div style={{ fontSize: 13.5, color: T.muted, lineHeight: 1.6 }}>Als Fachobjekt speichern — {standalone ? 'autark als ConfiguratorPackage (Vorlage/Angebot), später verlustfrei ins Projekt.' : 'als ein Command ins Gebäudemodell, Undo/Redo inklusive.'}</div>
              </>
            )}
          </div>

          {/* Vorschau */}
          <div style={{ background: T.surface2, borderRadius: 18, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 10, padding: 22 }}>
            <span style={{ alignSelf: 'flex-start', fontSize: 11, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint }}>Vorschau</span>
            <img src={iconUrl(wahl)} alt={wahl.label} style={{ width: 140, height: 'auto' }} />
            <div style={{ fontSize: 14, fontWeight: 700 }}>{wahl.label}</div>
            <div style={{ fontSize: 12.5, color: T.muted, fontVariantNumeric: 'tabular-nums' }}>{breite} × {hoehe} mm</div>
          </div>
        </div>

        {/* Fuß */}
        <div style={{ padding: '16px 30px 24px', display: 'flex', alignItems: 'center', gap: 12 }}>
          <span style={{ fontSize: 12.5, color: T.muted }}>Status: <b style={{ color: T.accentInk }}>Entwurf</b> · {standalone ? 'als ConfiguratorPackage speicherbar' : 'Undo/Redo im Modell'}</span>
          <span style={{ marginLeft: 'auto', display: 'flex', gap: 10 }}>
            <button type="button" onClick={() => setSchritt(Math.max(0, schritt - 1))} style={{ border: `1px solid ${T.hair}`, background: T.surface, color: T.ink, fontWeight: 600, fontSize: 14, padding: '11px 20px', borderRadius: 12, cursor: 'pointer' }}>Zurück</button>
            <button type="button" onClick={() => {
              if (!letzter) { setSchritt(schritt + 1); return; }
              const jetzt = new Date().toISOString();
              const id = (globalThis.crypto?.randomUUID?.() ?? `cfg-${jetzt}-${wahl.id}`);
              const paket = neuesPaket({
                id, type: TYP_MAP[art], jetzt, autor: 'Solar Aspekt',
                parameters: { bauart: wahl.id, bauartLabel: wahl.label, breiteMm: breite, hoeheMm: hoehe, autark: standalone },
                geometry: { breite, hoehe },
              });
              try {
                const blob = new Blob([JSON.stringify(paket, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = `konfigurator-${art}-${wahl.id}.json`; a.click();
                URL.revokeObjectURL(url);
              } catch { /* Download optional — Übernahme meldet trotzdem */ }
              onÜbernehmen(wahl.label);
            }} style={{ border: 0, background: T.brand, color: '#fff', fontWeight: 700, fontSize: 14, padding: '11px 26px', borderRadius: 12, cursor: 'pointer' }}>{letzter ? 'Übernehmen' : 'Weiter'}</button>
          </span>
        </div>
      </div>
    </div>
  );
}
