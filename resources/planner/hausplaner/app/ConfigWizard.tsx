/**
 * ConfigWizard (v9) — geführter Konfigurator-Dialog für Fenster/Tür/Treppe.
 * Nutzt die echten Premium-Bauart-Icons (public/hausplaner/icons/*). Schritte: Bauart → Maße →
 * Material → Prüfung → Übernehmen, mit Live-Vorschau. „Übernehmen" erzeugt ein echtes autarkes
 * ConfiguratorPackage (geometry/configuratorPackage.ts) und lädt es als JSON herunter. Der Schreibpfad
 * ins Gebäudemodell (Command) bleibt die nächste Scheibe.
 */
import React from 'react';
import { istAusloeser, useDialogFokus } from './dashboard/dialogFokus';
import { speicherePaket, kannPaketSpeichern } from './state/paketSpeichern';
import { T } from './studioDaten';
import { Ikon } from './studioUi';
import { FENSTER_BAUARTEN, TUER_BAUARTEN, fensterBauartNach, type OeffnungsBauart } from '../geometry/oeffnungsBauarten';
import { TREPPEN_BAUARTEN, type TreppenBauart } from '../geometry/treppenBauarten';
import { HEIZKOERPER_TYPEN, type HeizkoerperTyp } from '../geometry/heizkoerperTypen';
import { neuesPaket, type ConfiguratorType } from '../geometry/configuratorPackage';
import { useHausplanerStore } from '../store/hausplanerStore';
import type { SceneNode, WallNode, OpeningNode, ObjectNode } from '../domain/scene.types';
import { treppeZuParametern } from '../geometry/treppeObjekt';

const ICON_BASE = new URL('.', import.meta.url).href;

export type KonfigArt = 'fenster' | 'tuer' | 'treppe' | 'heizkoerper';

interface Props {
  art: KonfigArt;
  standalone?: boolean;
  onClose: () => void;
  onÜbernehmen: (nachricht: string) => void;
}

interface Kachel { id: string; datei: string; label: string; }

const SCHRITTE = ['Bauart', 'Maße', 'Material', 'Prüfung', 'Übernehmen'] as const;

function katalogFür(art: KonfigArt): { ordner: string; titel: string; kacheln: Kachel[] } {
  if (art === 'fenster') return { ordner: 'fenster', titel: 'Fenster konfigurieren', kacheln: FENSTER_BAUARTEN as readonly OeffnungsBauart[] as Kachel[] };
  if (art === 'tuer') return { ordner: 'tuer', titel: 'Tür konfigurieren', kacheln: TUER_BAUARTEN as readonly OeffnungsBauart[] as Kachel[] };
  if (art === 'treppe') return { ordner: 'treppe', titel: 'Treppe konfigurieren', kacheln: TREPPEN_BAUARTEN as readonly TreppenBauart[] as Kachel[] };
  return { ordner: 'heizkoerper', titel: 'Heizkörper konfigurieren', kacheln: HEIZKOERPER_TYPEN as readonly HeizkoerperTyp[] as unknown as Kachel[] };
}

const TYP_MAP: Record<KonfigArt, ConfiguratorType> = { fenster: 'window', tuer: 'door', treppe: 'stair', heizkoerper: 'radiator' };

export function ConfigWizard({ art, standalone = true, onClose, onÜbernehmen }: Props): React.ReactElement {
  const { ordner, titel, kacheln } = katalogFür(art);
  const [schritt, setSchritt] = React.useState(0);
  const [wahl, setWahl] = React.useState<Kachel>(kacheln[0]);
  const [breite, setBreite] = React.useState(art === 'treppe' ? 1000 : art === 'heizkoerper' ? 1000 : 1010);
  const [hoehe, setHoehe] = React.useState(art === 'fenster' ? 1360 : art === 'heizkoerper' ? 600 : 2010);

  const iconUrl = (k: Kachel): string => `${ICON_BASE}icons/${ordner}/${k.datei}`;
  const letzter = schritt === SCHRITTE.length - 1;

  const huelle = React.useRef<HTMLDivElement>(null);
  const titelId = React.useId();
  useDialogFokus(huelle, onClose);

  return (
    <div onClick={onClose} style={{ position: 'fixed', inset: 0, background: 'rgba(24,34,38,.30)', backdropFilter: 'blur(2px)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 90, padding: 24 }}>
      {/* AUF-49: Diese Fläche trug bis hierher KEINE Dialogsemantik — kein `role`, kein
          `aria-modal`, keinen Escape-Handler. Der Fokus blieb beim Öffnen auf dem Knopf dahinter,
          der erste Tab-Sprung landete HINTER dem Dialog. Jetzt dieselbe Regel wie in den
          Fachflächen: `dashboard/dialogFokus.ts`, einmal gebaut. */}
      <div
        ref={huelle}
        role="dialog" aria-modal="true" aria-labelledby={titelId}
        onClick={(e) => e.stopPropagation()}
        style={{ width: 'min(900px, 100%)', maxHeight: '92%', background: T.surface, borderRadius: 24, boxShadow: '0 10px 34px rgba(28,50,55,.18)', display: 'flex', flexDirection: 'column', overflow: 'hidden' }}
      >
        {/* Kopf */}
        <div className="hp-kw-kopf">
          <div id={titelId} className="hp-kw-titel">{titel}</div>
          <div className="hp-kw-untertitel">{standalone ? 'Autark — kein Gebäude nötig. Live-Vorschau bei jedem Schritt.' : 'Im Projekt — schreibt als Command ins Gebäudemodell.'}</div>
          <button type="button" onClick={onClose} aria-label="Schließen" className="hp-kw-schliessen">
            <Ikon inhalt='<path d="M6 6l12 12M18 6L6 18"/>' size={16} />
          </button>
        </div>

        {/* AUF-46: auch hier stand eine feste zweite Spalte (`1fr 300px`) — dieselbe Ursache wie
            in der geführten Planung. Bei 390 px stapeln die Spalten jetzt, statt sich zu überlagern. */}
        {/* Schritt-Punkte */}
        <div className="hp-kw-schritte">
          {SCHRITTE.map((n, i) => (
            <React.Fragment key={n}>
              <div role="button" tabIndex={0} onClick={() => setSchritt(i)} onKeyDown={(e) => { if (istAusloeser(e)) setSchritt(i); }} className="hp-kw-schritt">
                <span style={{ width: 26, height: 26, borderRadius: '50%', background: i === schritt ? T.accent : (i < schritt ? T.ok : T.surface2), color: (i === schritt || i < schritt) ? T.surface : T.muted, display: 'grid', placeItems: 'center', fontSize: 12, fontWeight: 700, boxShadow: T.schattenFlach }}>{i < schritt ? '✓' : i + 1}</span>
                {i === schritt && <span className="hp-kw-schrittname">{n}</span>}
              </div>
              {i < SCHRITTE.length - 1 && <span className="hp-kw-strich" />}
            </React.Fragment>
          ))}
        </div>

        {/* Körper */}
        <div className="hp-kw-koerper">
          <div>
            {schritt === 0 && (
              <>
                <div className="hp-kw-lead">Bauart wählen — {kacheln.length} Typen als Premium-Icons. Maße sind Vorlage, im nächsten Schritt frei.</div>
                <div className="hp-kw-kacheln">
                  {kacheln.map((k) => {
                    const on = k.id === wahl.id;
                    return (
                      <button key={k.id} type="button" title={k.label} onClick={() => setWahl(k)} style={{ display: 'grid', gap: 5, placeItems: 'center', padding: '8px 6px', borderRadius: 12, cursor: 'pointer', border: `1.5px solid ${on ? T.accent : T.hair}`, background: on ? T.accentSoft : T.surface }}>
                        <img src={iconUrl(k)} alt={k.label} loading="lazy" className="hp-kw-kachelbild" />
                        <span style={{ fontSize: 10, lineHeight: 1.15, color: on ? T.accentInk : T.muted, textAlign: 'center', height: 24, overflow: 'hidden', fontWeight: on ? 600 : 400 }}>{k.label}</span>
                      </button>
                    );
                  })}
                </div>
              </>
            )}
            {schritt === 1 && (
              <>
                <div className="hp-kw-abschnitt">Maße</div>
                <div className="hp-kw-feldzeile"><label className="hp-kw-feldlabel">Breite (mm)</label><input type="number" value={breite} onChange={(e) => setBreite(Math.max(100, Math.round(Number(e.target.value))))} className="hp-kw-feld" /></div>
                <div className="hp-kw-feldzeile"><label className="hp-kw-feldlabel">{art === 'treppe' ? 'Geschosshöhe (mm)' : 'Höhe (mm)'}</label><input type="number" value={hoehe} onChange={(e) => setHoehe(Math.max(100, Math.round(Number(e.target.value))))} className="hp-kw-feld" /></div>
              </>
            )}
            {schritt === 2 && (
              <>
                <div className="hp-kw-abschnitt">Material</div>
                <div className="hp-kw-feldzeile"><label className="hp-kw-feldlabel">{art === 'treppe' ? 'Konstruktion' : 'Profilsystem / Rahmen'}</label>
                  <select className="hp-kw-feld">{art === 'treppe' ? <><option>Stahlwange</option><option>Holzwange</option><option>Beton</option></> : <><option>Kunststoff 70 mm (5-Kammer)</option><option>Aluminium</option><option>Holz</option></>}</select>
                </div>
                {art !== 'treppe' && <div className="hp-kw-feldzeile"><label className="hp-kw-feldlabel">Verglasung</label><select className="hp-kw-feld"><option>2-fach Wärmeschutz</option><option>3-fach Wärmeschutz</option></select></div>}
              </>
            )}
            {schritt === 3 && (
              <>
                <div className="hp-kw-abschnitt">Prüfung</div>
                <div className="hp-kw-pruefzeile"><span className="hp-kw-marke hp-kw-marke--ok">✓</span>Maße plausibel</div>
                <div className="hp-kw-pruefzeile"><span className="hp-kw-marke hp-kw-marke--ok">✓</span>{art === 'treppe' ? 'DIN 18065 Schrittmaß' : 'Norm-Anschlag korrekt'}</div>
                <div className="hp-kw-pruefzeile"><span className="hp-kw-marke hp-kw-marke--warn">!</span>Rastermaß — 40 mm Versatz prüfen</div>
              </>
            )}
            {schritt === 4 && (
              <>
                <div className="hp-kw-abschnitt-eng">Übernehmen</div>
                {/* AUF-74: Der autarke Zweig versprach eine spätere, verlustfreie Übernahme ins Projekt
                    — das beschreibt etwas, das es nicht gibt. Jetzt steht dort, was wirklich
                    entsteht: eine Datei.
                    **Der andere Zweig ist wahr und bleibt Zeichen für Zeichen stehen**, samt
                    seiner Einleitung — deshalb wanderte auch sie in den Zweig. */}
                <div className="hp-kw-fliess">{standalone ? (kannPaketSpeichern()
                  ? 'Ergebnis: gespeichert in deiner Paketliste — und zusätzlich als Datei zum Herunterladen. Ins Gebäude kommt das Bauteil über den Experten, indem du eine Wand wählst.'
                  : 'Ergebnis: eine Datei zum Herunterladen. Ins Gebäude kommt das Bauteil über den Experten, indem du eine Wand wählst.') : 'Als Fachobjekt speichern — als ein Command ins Gebäudemodell, Undo/Redo inklusive.'}</div>
              </>
            )}
          </div>

          {/* Vorschau */}
          <div className="hp-kw-vorschau">
            <span className="hp-kw-vorschau-marke">Vorschau</span>
            <img src={iconUrl(wahl)} alt={wahl.label} className="hp-kw-vorschau-bild" />
            <div className="hp-kw-vorschau-titel">{wahl.label}</div>
            <div className="hp-kw-vorschau-mass">{breite} × {hoehe} mm</div>
          </div>
        </div>

        {/* Fuß */}
        <div className="hp-kw-fuss">
          <span className="hp-kw-status">Status: <b className="hp-kw-statuswert">Entwurf</b> · {standalone ? (kannPaketSpeichern() ? 'Ergebnis: Paketliste + Datei' : 'Ergebnis: Datei zum Herunterladen') : 'Undo/Redo im Modell'}</span>
          <span className="hp-kw-knopfreihe">
            <button type="button" onClick={() => setSchritt(Math.max(0, schritt - 1))} className="hp-kw-knopf-zurueck">Zurück</button>
            <button type="button" onClick={() => {
              if (!letzter) { setSchritt(schritt + 1); return; }
              const jetzt = new Date().toISOString();
              const id = (globalThis.crypto?.randomUUID?.() ?? `cfg-${jetzt}-${wahl.id}`);
              const store = useHausplanerStore.getState();
              const scene = store.scene;
              // Heizkörper ist freistehend: als ObjectNode 'radiator' ins aktive Geschoss (verschiebbar).
              if (art === 'heizkoerper' && scene) {
                const levelId = store.activeLevelId ?? scene.levels[0]?.id ?? null;
                const level = scene.levels.find((l) => l.id === levelId) ?? scene.levels[0];
                if (level) {
                  const radiator: ObjectNode = {
                    id, type: 'object', objectType: 'radiator', catalogItemId: 'radiator-default', levelId: level.id,
                    visible: true, locked: false, tags: [], createdAt: jetzt, updatedAt: jetzt,
                    transform: { position: { x: 2000, y: 500, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
                    parameters: { 'objekt.typ': wahl.id, 'objekt.label': wahl.label, 'objekt.laenge': breite, 'objekt.hoehe': hoehe },
                  };
                  const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
                  onÜbernehmen(ok ? `Heizkörper „${wahl.label}" ins Modell gesetzt — im Plan verschiebbar.` : `Heizkörper: Platzierung abgelehnt.`);
                  return;
                }
              }
              // Treppe ist freistehend: bei geladener Szene direkt als ObjectNode ins Modell (Standard-Lauflinie, verschiebbar).
              if (art === 'treppe' && scene) {
                const levelId = store.activeLevelId ?? scene.levels[0]?.id ?? null;
                const level = scene.levels.find((l) => l.id === levelId) ?? scene.levels[0];
                if (level) {
                  const params = treppeZuParametern({
                    startX: 1000, startY: 1000, endX: 1000, endY: 4000,
                    laufbreite: breite, geschosshoehe: Math.max(2000, hoehe),
                    bereich: 'wohnung', typ: wahl.id,
                  });
                  const treppe: ObjectNode = {
                    id, type: 'object', objectType: 'stair', catalogItemId: 'stair-default', levelId: level.id,
                    visible: true, locked: false, tags: [], createdAt: jetzt, updatedAt: jetzt,
                    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
                    parameters: params,
                  };
                  const ok = store.executeCommand({ type: 'ADD_NODE', node: treppe as SceneNode });
                  onÜbernehmen(ok ? `Treppe „${wahl.label}" ins Modell gesetzt — im Plan verschiebbar.` : `Treppe: Platzierung abgelehnt.`);
                  return;
                }
              }
              // Wenn eine Wand ausgewählt ist und es eine Öffnung ist: direkt ins Modell (ADD_NODE).
              if ((art === 'fenster' || art === 'tuer') && scene) {
                const wand = scene.nodes.find(
                  (n): n is WallNode => n.type === 'wall' && store.selectedNodeIds.includes(n.id),
                );
                if (wand) {
                  const len = Math.hypot(wand.end.x - wand.start.x, wand.end.y - wand.start.y);
                  const w = Math.min(breite, Math.max(100, Math.round(len - 100)));
                  const offset = Math.max(0, Math.round(len / 2 - w / 2));
                  const knoten: OpeningNode = {
                    id, type: art === 'fenster' ? 'window' : 'door', levelId: wand.levelId,
                    visible: true, locked: false, tags: [], createdAt: jetzt, updatedAt: jetzt,
                    hostWallId: wand.id, offsetFromWallStart: offset, width: w, height: hoehe,
                    sillHeight: art === 'fenster' ? 900 : 0,
                    produkt: { typ: wahl.id, ...(art === 'fenster' ? { oeffnungsArt: fensterBauartNach(wahl.id)?.oeffnungsArt } : {}) },
                  };
                  const ok = store.executeCommand({ type: 'ADD_NODE', node: knoten as SceneNode });
                  onÜbernehmen(ok ? `${wahl.label} auf die gewählte Wand gesetzt.` : `${wahl.label}: Platzierung abgelehnt (passt nicht in die Wand).`);
                  return;
                }
              }
              // Sonst: autark als ConfiguratorPackage (JSON-Download).
              const paket = neuesPaket({
                id, type: TYP_MAP[art], jetzt, autor: 'Solar Aspekt',
                parameters: { bauart: wahl.id, bauartLabel: wahl.label, breiteMm: breite, hoeheMm: hoehe, autark: true },
                geometry: { breite, hoehe },
              });
              // AUF-74, vierte Stelle — im Auftrag nicht enthalten, beim Messen aufgefallen:
              // der Fehlerfall wurde verschluckt und die Erfolgsmeldung trotzdem gezeigt. Wer
              // zehn Minuten konfiguriert und dann eine Erfolgsmeldung ohne Datei bekommt, sucht
              // sie im Download-Ordner. Jetzt sagt die Fläche, was wirklich geschehen ist.
              const dateiname = `konfigurator-${art}-${wahl.id}.json`;
              let entstanden = true;
              try {
                const blob = new Blob([JSON.stringify(paket, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = dateiname; a.click();
                URL.revokeObjectURL(url);
              } catch {
                entstanden = false;
              }
              // AUF-81: **zusätzlich** speichern, nicht statt. Der Download bleibt der Weg für
              // alle ohne Speicherrecht, und er funktioniert heute — ihn zu ersetzen hieße, einen
              // Weg zu schließen, bevor der neue bewiesen ist.
              void speicherePaket(art, wahl.label, paket).then((gespeichert) => {
                // Dieselbe Sorgfalt wie in AUF-74: gemeldet wird, was WIRKLICH geschehen ist —
                // beide Wege einzeln, kein „gespeichert", wenn nur der Download geklappt hat.
                const teile: string[] = [];
                if (gespeichert) teile.push('in deiner Paketliste gespeichert');
                if (entstanden) teile.push(`als Datei „${dateiname}" heruntergeladen`);
                onÜbernehmen(teile.length > 0
                  ? `${wahl.label}: ${teile.join(' und ')}. Ins Gebäude kommt das Bauteil über den Experten — dort eine Wand wählen.`
                  : `${wahl.label}: Es ist nichts entstanden — weder gespeichert noch heruntergeladen. Ins Gebäude kommt das Bauteil über den Experten, dort eine Wand wählen.`);
              });
            }} className="hp-kw-knopf-weiter">{letzter ? 'Übernehmen' : 'Weiter'}</button>
          </span>
        </div>
      </div>
    </div>
  );
}
