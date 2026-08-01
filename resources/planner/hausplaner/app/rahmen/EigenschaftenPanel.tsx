/**
 * AUF-48 Scheibe 4d — **das rechte Eigenschaften-Panel.**
 *
 * **Die wichtigste der fünf Scheiben für AUF-38:** dieser Block trägt **67 der 133** getrackten
 * Inline-Stellen — mehr als die Hälfte. *Wer Scheibe 7 gegen „120 im JSX-Block" plant, plant in
 * Wahrheit gegen diese Datei.*
 *
 * Enthalten: die Reiter aus `PANEL_TABS` · der Inhaltsbereich · die Mehrfachauswahl-Übersicht
 * (AUF-35a) · Sicht und Sperre je Knoten (Dashboard v1 §5) · die L/T/U-Anbaumaße · die Prüfungen
 * mit Schwere als **Symbol UND Text** (A11y).
 *
 * **Was hier NICHT passiert: nichts wird entschieden.** Das Markup ist zeichengleich das, was
 * vorher in `HausplanerApp.tsx` stand; keine Inline-Stelle wurde angefasst.
 *
 * **Mitgezogen sind die Helfer, die nur dieses Panel benutzt hat** — gemessen, nicht vermutet:
 * `PANEL_ID`, `reiterId`, `ICON_BASE`, `knopf`, `panelLabel`, `panelInput`, die vier
 * `aktualisiere*`-Befehle und `setzeWandLaenge`. Sie hängen alle nur an den Auswahl-Werten, und
 * die kommen als Eigenschaften herein.
 */
import React from 'react';
import { useHausplanerStore } from '../../store/hausplanerStore';
import type { ObjectNode, OpeningNode, RoofNode, RoofAnbauMasse, SceneNode, WallNode } from '../../domain/scene.types';
import { istVerschneidungsForm } from '../../domain/roofShape';
import { T, FARBEN, OP_TOKEN } from '../studioDaten';
import { opKnopfBild } from '../dashboard/opKnopfZustand';
import { PANEL_TABS, type PanelTabId } from '../dashboard/panelTabs';
import { ReiterLeiste } from '../dashboard/ReiterLeiste';
import { SchienenSchalter } from '../dashboard/SchienenSchalter';
import { ZustandBadge } from '../studioUi';
import { BEFUNDE_LEER, BEFUNDE_UMFANG } from '../dashboard/befunde';
import type { Werkzeug } from '../tools/werkzeugArten';
import type { raeumeAus } from '../ableitungen';
import { berechneTreppe } from '../../geometry/treppenBerechnung';
import { treppeZuParametern, type TreppeParams } from '../../geometry/treppeObjekt';
import { PROFIL_KATALOG, VERGLASUNG_KATALOG, berechneUw, rcMachbar, preisFenster, profilNach, verglasungNach, type OeffnungsArt, type RcKlasse } from '../../geometry/fensterProdukt';
import { FENSTER_BAUARTEN, TUER_BAUARTEN, fensterBauartNach, tuerBauartNach } from '../../geometry/oeffnungsBauarten';
import { TREPPEN_BAUARTEN, treppenBauartNach } from '../../geometry/treppenBauarten';
import type { Level } from '../../domain/scene.types';
import type { SchienenZustand } from '../state/schienenSpeicher';
import type { mehrfachUebersicht } from '../tools/auswahlUebersicht';
import type { befundeAus } from '../dashboard/befunde';

/** Derselbe Zugriff wie in der Hauptfunktion — ein Speicher, keine zweite Quelle. */
const store = useHausplanerStore;

const ICON_BASE = new URL('.', import.meta.url).href;
const PANEL_ID = 'hp-eigenschaften-panel';
const reiterId = (id: PanelTabId): string => `hp-eigenschaften-tab-${id}`;

/**
 * **Einzeln benannte Eigenschaften, kein Sammelobjekt.** Das Panel nimmt Auswahl und Knoten
 * entgegen und meldet Befehle zurück; es hält nichts.
 */
export interface EigenschaftenPanelEigenschaften {
  aktiverTab: PanelTabId;
  setAktiverTab: (id: PanelTabId) => void;
  istSchmal: boolean;
  schienen: SchienenZustand;
  klappeSchiene: (seite: 'links' | 'rechts', offen: boolean) => void;
  level: Level;
  werkzeug: Werkzeug;
  raeume: ReturnType<typeof raeumeAus>;
  befunde: ReturnType<typeof befundeAus>;
  auswahlUebersicht: ReturnType<typeof mehrfachUebersicht>;
  selectedNode: SceneNode | null;
  selectedWall: WallNode | null;
  selectedOpening: OpeningNode | null;
  selectedRoof: RoofNode | null;
  selectedStair: ObjectNode | null;
  selectedStairParams: TreppeParams | null;
  selectedObjekt: ObjectNode | null;
  dupliziere: () => void;
  loescheAuswahl: () => void;
}

export function EigenschaftenPanel({
  aktiverTab, setAktiverTab, istSchmal, schienen, klappeSchiene, level, werkzeug, raeume, befunde,
  auswahlUebersicht, selectedNode, selectedWall, selectedOpening, selectedRoof, selectedStair,
  selectedStairParams, selectedObjekt, dupliziere, loescheAuswahl,
}: EigenschaftenPanelEigenschaften): React.ReactElement {
  const MAUERWERK = [
    { id: 'ziegel', label: 'Ziegel (Hochlochziegel)' },
    { id: 'kalksandstein', label: 'Kalksandstein (KS)' },
    { id: 'porenbeton', label: 'Porenbeton' },
    { id: 'leichtbeton', label: 'Leichtbeton (Bims)' },
    { id: 'stahlbeton', label: 'Stahlbeton' },
    { id: 'holzstaender', label: 'Holzständerwand' },
  ] as const;
  const WANDSTAERKEN = [115, 150, 175, 240, 300, 365] as const;
  const knopf = (aktiv: boolean, gesperrt = false): React.CSSProperties => {
    const b = opKnopfBild(aktiv, gesperrt);
    return {
      padding: '6px 12px', fontSize: 12.5, fontWeight: 600, borderRadius: 8,
      border: `1px solid ${b.rahmenToken ? OP_TOKEN[b.rahmenToken] : T.controlBorder}`,
      background: OP_TOKEN[b.grundToken],
      color: OP_TOKEN[b.iconToken],
      opacity: b.deckkraft,
      cursor: b.cursor,
    };
  };
  const panelLabel: React.CSSProperties = { display: 'block', color: FARBEN.gedaempft, marginBottom: 8 };
  const panelInput: React.CSSProperties = { width: '100%', marginTop: 3, padding: '5px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}`, fontSize: 12.5 };
  function aktualisiereDach(changes: Partial<RoofNode>): void {
    if (selectedRoof) {
      store.getState().executeCommand({ type: 'UPDATE_ROOF', roofId: selectedRoof.id, changes });
    }
  }
  function aktualisiereWand(changes: Partial<WallNode>): void {
    if (selectedWall) {
      store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedWall.id, changes });
    }
  }
  function setzeWandLaenge(neu: number): void {
    if (!selectedWall || !(neu > 0)) return;
    const dx = selectedWall.end.x - selectedWall.start.x;
    const dy = selectedWall.end.y - selectedWall.start.y;
    const len = Math.hypot(dx, dy);
    if (len === 0) return;
    const end = { x: Math.round(selectedWall.start.x + (dx / len) * neu), y: Math.round(selectedWall.start.y + (dy / len) * neu) };
    store.getState().executeCommand({ type: 'MOVE_NODE', nodeId: selectedWall.id, position: { start: selectedWall.start, end } });
  }
  function aktualisiereOeffnung(changes: Partial<OpeningNode>): void {
    if (selectedOpening) {
      store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedOpening.id, changes });
    }
  }
  function aktualisiereTreppe(aenderung: Partial<TreppeParams>): void {
    if (!selectedStair || !selectedStairParams) return;
    const neu = { ...selectedStairParams, ...aenderung };
    store.getState().executeCommand({
      type: 'UPDATE_NODE', nodeId: selectedStair.id,
      changes: { parameters: treppeZuParametern(neu) },
    });
  }

  return (
    <>
    {/* Rechtes Eigenschaften-Panel — AUF-83-T5 / K-01/K-02/K-05/K-06: klappbar, wie die linke
        Schiene. Der alte Kommentar „immer sichtbar" beschrieb ab diesem Auftrag das Gegenteil
        des Verhaltens und ist deshalb fort (K-06) — ein Kommentar, der die alte Wahrheit
        weiterträgt, wird geglaubt. */}
    {/* AUF-26/B3: `overflowWrap` + `boxSizing` — Text bricht um, statt im Wort abgeschnitten zu
        werden. Ein Hinweis, der bei „…brauch" endet, ist kein Hinweis. */}
    <div
      {...(istSchmal && schienen.rechts ? {} : { 'data-schiene': true })}
      className={istSchmal && schienen.rechts ? 'hp-schiene-overlay hp-schiene-overlay--rechts' : undefined}
      style={{
        width: schienen.rechts ? 268 : 32, flex: '0 0 auto', background: T.surface,
        borderLeft: `1px solid ${T.hair}`, padding: schienen.rechts ? 14 : 0,
        overflowY: schienen.rechts ? 'auto' : 'hidden', overflowWrap: 'anywhere',
        boxSizing: 'border-box', fontSize: 12.5, color: FARBEN.text,
      }}
    >
      <div className="hp-schiene-kopf">
        {schienen.rechts && <div style={{ fontWeight: 800, fontSize: 11.5, textTransform: 'uppercase', letterSpacing: '.04em', color: FARBEN.gedaempft, marginBottom: 8, flex: 1, minWidth: 0 }}>Eigenschaften</div>}
        <SchienenSchalter seite="rechts" offen={schienen.rechts} label="Eigenschaften" onClick={() => klappeSchiene('rechts', !schienen.rechts)} />
      </div>
      {schienen.rechts && (
      <>
      {/* Dashboard v2.2 (§20 / UI-5): Reiter aus PANEL_TABS (Daten, nicht Markup). Seit AUF-27
          rendert sie die gemeinsame `ReiterLeiste` — dieselbe Verdrahtung wie die Schiene,
          inklusive der AUF-19-Nacharbeiten (aria-controls, Pfeiltasten, Fokusnachführung).
          `allgemein` zeigt den unveränderten Panelinhalt. */}
      <ReiterLeiste
        reiter={PANEL_TABS}
        aktiv={aktiverTab}
        setAktiv={(id) => setAktiverTab(id as PanelTabId)}
        ariaLabel="Eigenschaften-Bereiche"
        panelId={PANEL_ID}
        reiterId={(id) => reiterId(id as PanelTabId)}
      />
      {/* B3 (Nacharbeit N3): DER Inhaltsbereich der Reiter. Bis hierher trug er keine Rolle —
          die Reiterleiste versprach ein Tabpanel, das im Baum nicht existierte. `aria-labelledby`
          zeigt auf den GERADE aktiven Reiter, `id` ist das Ziel aller vier `aria-controls`. */}
      <div role="tabpanel" id={PANEL_ID} aria-labelledby={reiterId(aktiverTab)}>
      {aktiverTab === 'pruefungen' ? (
        /* Dashboard v2.4 (§34 / UI-10): Prüfungscenter. Die Liste kommt aus `befundeAus`
           (rein, getestet) und hat heute 0 oder 1 Eintrag — der Store hält genau EINE
           Ablehnung. Was NICHT geführt wird, steht ehrlich unter der Liste, statt eine
           Historie vorzutäuschen. */
        <div style={{ color: FARBEN.gedaempft, lineHeight: 1.6 }}>
          {befunde.length === 0 ? (
            <div className="hp-ep-befund-leer">
              <span>{BEFUNDE_LEER}</span>
              <ZustandBadge zustand="verfuegbar" />
            </div>
          ) : (
            <ul className="hp-ep-befundliste">
              {befunde.map((b) => (
                <li key={b.id} className="hp-ep-befund">
                  {/* Schwere als Symbol UND Text, nicht nur als Farbe (A11y). */}
                  <span aria-hidden className="hp-ep-schwere-symbol">✋</span>
                  <span><strong className="hp-ep-schwere-text">Abgelehnt</strong> – {b.text}</span>
                </li>
              ))}
            </ul>
          )}
          <div className="hp-ep-umfang">{BEFUNDE_UMFANG}</div>
        </div>
      ) : aktiverTab !== 'allgemein' ? (
        <div style={{ color: FARBEN.gedaempft, lineHeight: 1.7 }}>
          <div className="hp-ep-hinweis">{PANEL_TABS.find((t) => t.id === aktiverTab)?.hinweis}</div>
          <ZustandBadge zustand={PANEL_TABS.find((t) => t.id === aktiverTab)?.zustand ?? 'in_entwicklung'} />
        </div>
      ) : (
        <>
      {/* AUF-35a / Kante 4: Bei MEHRFACHauswahl zeigt das Panel eine Übersicht mit Anzahl je Typ
          statt Einzelfelder zu raten. Die Zählung kommt aus `mehrfachUebersicht` (rein,
          getestet); das Markup bleibt dünn. Darunter laufen die Einzelfelder wie bisher weiter —
          sie zeigen das PRIMÄROBJEKT, also das zuletzt gewählte. */}
      {auswahlUebersicht.gesamt > 1 && (
        <div className="hp-ep-mehrfach">
          <div className="hp-ep-mehrfach-zahl">{auswahlUebersicht.gesamt} Objekte gewählt</div>
          <div className="hp-ep-typreihe">
            {auswahlUebersicht.typen.map((t) => (
              <span key={t.typ} className="hp-ep-typ">
                {t.bezeichnung}
              </span>
            ))}
          </div>
          {auswahlUebersicht.gesperrt > 0 && (
            <div style={{ fontSize: 11.5, color: FARBEN.gedaempft }}>🔒 {auswahlUebersicht.gesperrt} davon gesperrt — wählbar, aber nicht bearbeitbar.</div>
          )}
          <div style={{ fontSize: 11.5, color: FARBEN.gedaempft }}>
            Unten stehen die Eigenschaften des zuletzt gewählten Objekts.
          </div>
        </div>
      )}
      {/* Dashboard v1 §5: Sicht (Auge) + Sperre (Schloss) je selektiertem Node → vorhandene Commands
          SET_NODES_SICHTBAR/SET_NODES_GESPERRT. Zustand als Text UND Symbol (A11y). Entsperren fragt nach. */}
      {selectedNode && (
        <div className="hp-ep-knopfreihe-unten">
          <button type="button" style={knopf(false)} title={selectedNode.visible === false ? 'Einblenden' : 'Ausblenden'} aria-label="Sicht umschalten"
            onClick={() => store.getState().executeCommand({ type: 'SET_NODES_SICHTBAR', nodeIds: [selectedNode.id], sichtbar: selectedNode.visible === false })}>
            {selectedNode.visible === false ? '🙈 Ausgeblendet' : '👁 Sicht'}
          </button>
          <button type="button" style={knopf(selectedNode.locked === true)} title={selectedNode.locked ? 'Entsperren' : 'Sperren'} aria-label="Sperre umschalten"
            onClick={() => { if (selectedNode.locked && !window.confirm('Objekt ist gesperrt — entsperren?')) return; store.getState().executeCommand({ type: 'SET_NODES_GESPERRT', nodeIds: [selectedNode.id], gesperrt: !selectedNode.locked }); }}>
            {selectedNode.locked ? '🔒 Gesperrt' : '🔓 Sperren'}
          </button>
        </div>
      )}
      {selectedRoof ? (
        <>
          <div className="hp-ep-titel">Dach</div>
          <label style={panelLabel}>Dachform
            <select value={selectedRoof.roofType} onChange={(e) => aktualisiereDach({ roofType: e.target.value as RoofNode['roofType'] })} style={panelInput}>
              <option value="sattel">Satteldach</option>
              <option value="walm">Walmdach</option>
              <option value="pult">Pultdach</option>
              <option value="flach">Flachdach</option>
              <option value="l-shape">L-Dach</option>
              <option value="t-shape">T-Dach</option>
              <option value="u-shape">U-Dach</option>
            </select>
          </label>
          <label style={panelLabel}>Neigung (°)
            <input type="number" min={0} max={89} value={selectedRoof.neigungGrad} onChange={(e) => aktualisiereDach({ neigungGrad: Math.max(0, Math.min(89, Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>First-Richtung (° Azimut, Nord=0)
            <input type="number" value={selectedRoof.firstAzimutGrad} onChange={(e) => aktualisiereDach({ firstAzimutGrad: Number(e.target.value) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Überstand (mm)
            <input type="number" min={0} value={selectedRoof.ueberstandMm} onChange={(e) => aktualisiereDach({ ueberstandMm: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          {/* Verdrahtung #1: L/T/U-Anbaumaße (verdrahten den schon abgenommenen Verschneidungs-Render an die UI).
              Operanden-Gate: fehlende/≤0 Maße rendern NICHT — mit konkretem Grund markiert, kein erfundener Wert. */}
          {istVerschneidungsForm(selectedRoof.roofType) && (() => {
            const a = selectedRoof.anbau;
            const istU = selectedRoof.roofType === 'u-shape';
            const setzeAnbau = (feld: keyof RoofAnbauMasse, wert: number): void => {
              const basis: RoofAnbauMasse = { length: a?.length ?? 0, width: a?.width ?? 0, lengthB: a?.lengthB, widthB: a?.widthB };
              basis[feld] = Math.max(0, Math.round(wert));
              aktualisiereDach({ anbau: basis });
            };
            const fehlt = !a || !(a.length > 0) || !(a.width > 0) || (istU && (!(a.lengthB && a.lengthB > 0) || !(a.widthB && a.widthB > 0)));
            return (
              <>
                <div className="hp-ep-untertitel">Anbau / Verschneidung</div>
                <label style={panelLabel}>Außenmaß Länge (mm)
                  <input type="number" min={0} value={a?.length ?? ''} onChange={(e) => setzeAnbau('length', Number(e.target.value))} style={panelInput} />
                </label>
                <label style={panelLabel}>Außenmaß Breite (mm)
                  <input type="number" min={0} value={a?.width ?? ''} onChange={(e) => setzeAnbau('width', Number(e.target.value))} style={panelInput} />
                </label>
                {istU && (
                  <>
                    <label style={panelLabel}>Innenhof/Kerbe Länge (mm)
                      <input type="number" min={0} value={a?.lengthB ?? ''} onChange={(e) => setzeAnbau('lengthB', Number(e.target.value))} style={panelInput} />
                    </label>
                    <label style={panelLabel}>Innenhof/Kerbe Breite (mm)
                      <input type="number" min={0} value={a?.widthB ?? ''} onChange={(e) => setzeAnbau('widthB', Number(e.target.value))} style={panelInput} />
                    </label>
                  </>
                )}
                {fehlt && (
                  <div style={{ fontSize: 11, color: FARBEN.warnung, marginTop: 2 }}>
                    ⚠ {istU
                      ? 'U-Dach braucht alle vier Maße > 0 (Außen Länge/Breite + Innenhof/Kerbe Länge/Breite) — sonst rendert es nicht.'
                      : 'L/T-Dach braucht Außenmaß Länge und Breite > 0 — sonst rendert es nicht.'}
                  </div>
                )}
              </>
            );
          })()}
          <button type="button" style={{ ...knopf(false), width: '100%', marginTop: 4 }} onClick={() => { store.getState().executeCommand({ type: 'REMOVE_ROOF', roofId: selectedRoof.id }); store.getState().selectNodes([]); }}>Dach entfernen</button>
        </>
      ) : selectedWall ? (
        <>
          <div className="hp-ep-titel">Wand</div>
          <label style={panelLabel}>Mauerwerk
            <select value={selectedWall.construction?.materialId ?? ''} onChange={(e) => aktualisiereWand({ construction: { ...(selectedWall.construction ?? {}), materialId: e.target.value } })} style={panelInput}>
              <option value="">— wählen —</option>
              {MAUERWERK.map((m) => (<option key={m.id} value={m.id}>{m.label}</option>))}
            </select>
          </label>
          <label style={panelLabel}>Wandstärke (mm)
            <select value={WANDSTAERKEN.includes(selectedWall.thickness as typeof WANDSTAERKEN[number]) ? selectedWall.thickness : ''} onChange={(e) => aktualisiereWand({ thickness: Number(e.target.value) })} style={panelInput}>
              {!WANDSTAERKEN.includes(selectedWall.thickness as typeof WANDSTAERKEN[number]) && (<option value="">{selectedWall.thickness} mm (aktuell)</option>)}
              {WANDSTAERKEN.map((d) => (<option key={d} value={d}>{d} mm</option>))}
            </select>
          </label>
          <label style={panelLabel}>Höhe (mm)
            <input type="number" min={100} value={selectedWall.height} onChange={(e) => aktualisiereWand({ height: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Länge (mm)
            <input type="number" min={1} value={Math.round(Math.hypot(selectedWall.end.x - selectedWall.start.x, selectedWall.end.y - selectedWall.start.y))} onChange={(e) => setzeWandLaenge(Math.max(1, Math.round(Number(e.target.value))))} style={panelInput} />
          </label>
          <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Länge ändern verschiebt das Wandende entlang der Achse. Bewegen: Wand im Plan ziehen.</div>
          <div className="hp-ep-knopfreihe">
            <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={dupliziere}>Duplizieren</button>
            <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
          </div>
        </>
      ) : selectedOpening ? (
        <>
          <div className="hp-ep-titel">{selectedOpening.type === 'door' ? 'Tür' : selectedOpening.type === 'window' ? 'Fenster' : 'Öffnung'}</div>
          {(selectedOpening.type === 'window' || selectedOpening.type === 'door') && (() => {
            const istFenster = selectedOpening.type === 'window';
            const katalog = istFenster ? FENSTER_BAUARTEN : TUER_BAUARTEN;
            const aktuellTyp = selectedOpening.produkt?.typ;
            const waehleTyp = (t: (typeof katalog)[number]): void => {
              const prod = selectedOpening.produkt ?? {};
              const aend: NonNullable<OpeningNode['produkt']> = { ...prod, typ: t.id };
              if (t.oeffnungsArt) aend.oeffnungsArt = t.oeffnungsArt;
              aktualisiereOeffnung({ produkt: aend });
            };
            return (
              <div className="hp-ep-feldgruppe">
                <div className="hp-ep-rubrik">Bauart</div>
                <div className="hp-ep-bauartraster">
                  {katalog.map((t) => {
                    const aktivT = aktuellTyp === t.id;
                    return (
                      <button key={t.id} type="button" title={t.label} onClick={() => waehleTyp(t)}
                        style={{ display: 'grid', gap: 3, placeItems: 'center', padding: 5, borderRadius: 8, cursor: 'pointer',
                          border: `1.5px solid ${aktivT ? T.brandInk : T.controlBorder}`, background: aktivT ? T.brandWash : T.surface }}>
                        <img src={`${ICON_BASE}icons/${istFenster ? 'fenster' : 'tuer'}/${t.datei}`} alt={t.label} loading="lazy" className="hp-ep-bauartbild" />
                        <span style={{ fontSize: 8.5, lineHeight: 1.15, color: aktivT ? FARBEN.text : T.muted, textAlign: 'center', height: 20, overflow: 'hidden' }}>{t.label}</span>
                      </button>
                    );
                  })}
                </div>
                {aktuellTyp && (
                  <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 6 }}>Bauart: <strong style={{ color: FARBEN.text }}>{(istFenster ? fensterBauartNach(aktuellTyp) : tuerBauartNach(aktuellTyp))?.label ?? aktuellTyp}</strong></div>
                )}
              </div>
            );
          })()}
          {selectedOpening.type === 'door' && (
            <>
              <label style={panelLabel}>Anschlag (Angel)
                <select value={selectedOpening.anschlag ?? 'links'} onChange={(e) => aktualisiereOeffnung({ anschlag: e.target.value as 'links' | 'rechts' })} style={panelInput}>
                  <option value="links">links</option>
                  <option value="rechts">rechts</option>
                </select>
              </label>
              <label style={panelLabel}>Öffnungsrichtung
                <select value={selectedOpening.oeffnung ?? 'innen'} onChange={(e) => aktualisiereOeffnung({ oeffnung: e.target.value as 'innen' | 'aussen' })} style={panelInput}>
                  <option value="innen">nach innen</option>
                  <option value="aussen">nach außen</option>
                </select>
              </label>
            </>
          )}
          <label style={panelLabel}>Breite (mm)
            <input type="number" min={100} value={selectedOpening.width} onChange={(e) => aktualisiereOeffnung({ width: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Höhe (mm)
            <input type="number" min={100} value={selectedOpening.height} onChange={(e) => aktualisiereOeffnung({ height: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Brüstungshöhe (mm)
            <input type="number" min={0} value={selectedOpening.sillHeight ?? 0} onChange={(e) => aktualisiereOeffnung({ sillHeight: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Position ab Wandanfang (mm)
            <input type="number" min={0} value={selectedOpening.offsetFromWallStart} onChange={(e) => aktualisiereOeffnung({ offsetFromWallStart: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 10 }}>Maße direkt hier ändern — oder die Öffnung im Plan entlang der Wand ziehen.</div>
          {(selectedOpening.type === 'window' || selectedOpening.type === 'door') && (() => {
            const prod = selectedOpening.produkt ?? {};
            const prof = profilNach(prod.profilId ?? '') ?? PROFIL_KATALOG[0];
            const verg = verglasungNach(prod.verglasungId ?? '') ?? VERGLASUNG_KATALOG[0];
            const oa = (prod.oeffnungsArt ?? 'dreh-kipp') as OeffnungsArt;
            const rc = (prod.rc ?? 'ohne') as RcKlasse;
            const uw = berechneUw({ breiteMm: selectedOpening.width, hoeheMm: selectedOpening.height, uf: prof.uf, ug: verg.ug, ansichtsbreiteMm: prof.ansichtsbreiteMm });
            const rcOk = rcMachbar(rc, verg);
            const preis = preisFenster({ breiteMm: selectedOpening.width, hoeheMm: selectedOpening.height, profil: prof, verglasung: verg, oeffnungsArt: oa, rc });
            const setP = (aend: Partial<NonNullable<OpeningNode['produkt']>>) => aktualisiereOeffnung({ produkt: { ...prod, ...aend } });
            return (
              <div className="hp-ep-abschnitt">
                <div className="hp-ep-abschnitt-titel">Produkt (Fensterbau)</div>
                <label style={panelLabel}>Profilsystem
                  <select value={prof.id} onChange={(e) => setP({ profilId: e.target.value })} style={panelInput}>
                    {PROFIL_KATALOG.map((p) => (<option key={p.id} value={p.id}>{p.name}</option>))}
                  </select>
                </label>
                <label style={panelLabel}>Verglasung
                  <select value={verg.id} onChange={(e) => setP({ verglasungId: e.target.value })} style={panelInput}>
                    {VERGLASUNG_KATALOG.map((v) => (<option key={v.id} value={v.id}>{v.name}</option>))}
                  </select>
                </label>
                <label style={panelLabel}>Öffnungsart
                  <select value={oa} onChange={(e) => setP({ oeffnungsArt: e.target.value as OeffnungsArt })} style={panelInput}>
                    <option value="fest">fest</option>
                    <option value="dreh">Dreh</option>
                    <option value="kipp">Kipp</option>
                    <option value="dreh-kipp">Dreh-Kipp</option>
                  </select>
                </label>
                <label style={panelLabel}>Einbruchschutz (RC)
                  <select value={rc} onChange={(e) => setP({ rc: e.target.value as RcKlasse })} style={panelInput}>
                    <option value="ohne">ohne</option>
                    <option value="RC1N">RC1N</option>
                    <option value="RC2N">RC2N</option>
                    <option value="RC2">RC2</option>
                    <option value="RC3">RC3</option>
                  </select>
                </label>
                <div style={{ marginTop: 8, padding: 10, background: T.bg, border: `1px solid ${T.hair}`, borderRadius: 8, fontSize: 12, lineHeight: 1.7, color: FARBEN.text }}>
                  <div>U-Wert (U<sub>w</sub>): <strong>{uw.uw.toFixed(2)}</strong> W/(m²·K)</div>
                  <div>RC {rc === 'ohne' ? '' : rc}: <strong style={{ color: rc === 'ohne' ? FARBEN.gedaempft : rcOk ? FARBEN.erfolg : FARBEN.gefahr }}>{rc === 'ohne' ? 'kein Nachweis' : rcOk ? 'mit dieser Verglasung möglich' : 'Verglasung reicht nicht'}</strong></div>
                  <div>Preis (netto): <strong>{preis.gesamt.toLocaleString('de-DE')} €</strong></div>
                  <div style={{ fontSize: 10.5, color: FARBEN.gedaempft, marginTop: 4 }}>Rahmen {preis.rahmen} € · Glas {preis.glas} € · Beschlag {preis.beschlag} € · RC {preis.rcAufpreis} €</div>
                  <div style={{ fontSize: 10.5, color: FARBEN.gedaempft, marginTop: 4 }}>Katalogwerte sind Platzhalter bis zu den echten Schüco-Daten.</div>
                </div>
              </div>
            );
          })()}
          <div className="hp-ep-knopfreihe">
            <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={dupliziere}>Duplizieren</button>
            <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
          </div>
        </>
      ) : selectedStair && selectedStairParams ? (
        <>
          <div className="hp-ep-titel">Treppe</div>
          {(() => {
            const aktuellTyp = selectedStairParams.typ;
            return (
              <div className="hp-ep-feldgruppe">
                <div className="hp-ep-rubrik">Bauart</div>
                <div className="hp-ep-bauartraster hp-ep-bauartraster--treppe">
                  {TREPPEN_BAUARTEN.map((t) => {
                    const aktivT = aktuellTyp === t.id;
                    return (
                      <button key={t.id} type="button" title={t.label} onClick={() => aktualisiereTreppe({ typ: t.id })}
                        style={{ display: 'grid', gap: 3, placeItems: 'center', padding: 5, borderRadius: 8, cursor: 'pointer',
                          border: `1.5px solid ${aktivT ? T.brandInk : T.controlBorder}`, background: aktivT ? T.brandWash : T.surface }}>
                        <img src={`${ICON_BASE}icons/treppe/${t.datei}`} alt={t.label} loading="lazy" className="hp-ep-bauartbild" />
                        <span style={{ fontSize: 8.5, lineHeight: 1.15, color: aktivT ? FARBEN.text : T.muted, textAlign: 'center', height: 20, overflow: 'hidden' }}>{t.label}</span>
                      </button>
                    );
                  })}
                </div>
                {aktuellTyp && (
                  <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 6 }}>Bauart: <strong style={{ color: FARBEN.text }}>{treppenBauartNach(aktuellTyp)?.label ?? aktuellTyp}</strong></div>
                )}
              </div>
            );
          })()}
          {(() => {
            const erg = berechneTreppe({ geschosshoehe: selectedStairParams.geschosshoehe, laufbreite: selectedStairParams.laufbreite, gewuenschteSteigung: selectedStairParams.gewuenschteSteigung, bereich: selectedStairParams.bereich, verfuegbareLauflaenge: Math.hypot(selectedStairParams.endX - selectedStairParams.startX, selectedStairParams.endY - selectedStairParams.startY) || undefined });
            return (
              <div style={{ marginBottom: 10, padding: 10, background: erg.bestanden ? T.okSoft : T.errSoft, border: `1px solid ${erg.bestanden ? T.okBorder : T.errBorder}`, borderRadius: 8, fontSize: 11.5, lineHeight: 1.6, color: FARBEN.text }}>
                <div><strong>{erg.anzahlSteigungen}</strong> Steigungen · <strong>{erg.anzahlAuftritte}</strong> Auftritte</div>
                <div>Steigung {erg.steigungshoehe} mm · Auftritt {erg.auftritt} mm</div>
                <div>Schrittmaß {erg.schrittmass} mm · {erg.bestanden ? 'DIN 18065 erfüllt' : 'DIN 18065 verletzt'}</div>
              </div>
            );
          })()}
          <label style={panelLabel}>Nutzungsbereich
            <select value={selectedStairParams.bereich} onChange={(e) => aktualisiereTreppe({ bereich: e.target.value as TreppeParams['bereich'] })} style={panelInput}>
              <option value="wohnung">Wohnung</option>
              <option value="gebaeude">Gebäude</option>
              <option value="aussen">außen</option>
            </select>
          </label>
          <label style={panelLabel}>Laufbreite (mm)
            <input type="number" min={500} value={selectedStairParams.laufbreite} onChange={(e) => aktualisiereTreppe({ laufbreite: Math.max(500, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Geschosshöhe (mm)
            <input type="number" min={2000} value={selectedStairParams.geschosshoehe} onChange={(e) => aktualisiereTreppe({ geschosshoehe: Math.max(2000, Math.round(Number(e.target.value))) })} style={panelInput} />
          </label>
          <label style={panelLabel}>Ziel-Steigungshöhe (mm, optional)
            <input type="number" min={0} value={selectedStairParams.gewuenschteSteigung ?? ''} onChange={(e) => { const v = Math.round(Number(e.target.value)); aktualisiereTreppe({ gewuenschteSteigung: v > 0 ? v : undefined }); }} style={panelInput} />
          </label>
          <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Stufung wird automatisch nach DIN 18065 gerechnet. Bewegen: Treppe im Plan ziehen.</div>
          <div className="hp-ep-knopfreihe">
            <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
          </div>
        </>
      ) : selectedObjekt ? (
        <>
          <div className="hp-ep-titel">{String(selectedObjekt.parameters['objekt.label'] ?? 'Objekt')}</div>
          <label style={panelLabel}>Länge (mm)
            <input type="number" min={100} value={Number(selectedObjekt.parameters['objekt.laenge']) || 0} onChange={(e) => store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedObjekt.id, changes: { parameters: { ...selectedObjekt.parameters, 'objekt.laenge': Math.max(100, Math.round(Number(e.target.value))) } } })} style={panelInput} />
          </label>
          <label style={panelLabel}>Höhe (mm)
            <input type="number" min={100} value={Number(selectedObjekt.parameters['objekt.hoehe']) || 0} onChange={(e) => store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedObjekt.id, changes: { parameters: { ...selectedObjekt.parameters, 'objekt.hoehe': Math.max(100, Math.round(Number(e.target.value))) } } })} style={panelInput} />
          </label>
          <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Bewegen: im Plan ziehen.</div>
          <div className="hp-ep-knopfreihe">
            <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
          </div>
        </>
      ) : (
        <div style={{ color: FARBEN.gedaempft, lineHeight: 1.7 }}>
          {/* AUF-59: Hier standen zwei ausgeschriebene Knöpfe „↔ Links/Rechts" und „↕ Oben/Unten"
              (117x43, ohne `title`) — dieselbe Handlung wie die beiden Spiegel-Icons in der
              Bedienzeile, mit derselben Sperrbedingung. Gemessen war es dieselbe Funktion,
              nicht eine zweite: `spiegeleGrundriss('vertikal'/'horizontal')`, `waende.length === 0`.
              Der Text weicht dem vorhandenen Icon — die Funktion bleibt, sie steht eine Zeile
              höher und nimmt dort keinen Panel-Platz weg. */}
          <div className="hp-ep-lesehinweis">Objekt anklicken (Auswahl-Werkzeug) = markieren; dann ziehen zum Bewegen, oder Duplizieren/Löschen.</div>
          <div className="hp-ep-kennzahl">Werkzeug: <strong style={{ color: FARBEN.text }}>{werkzeug}</strong></div>
          <div className="hp-ep-kennzahl">Geschoss: <strong style={{ color: FARBEN.text }}>{level.name}</strong></div>
          <div className="hp-ep-kennzahl">Räume: {raeume.length} · {(raeume.reduce((acc, r) => acc + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</div>
          <div className="hp-ep-fusskasten">
            Ein Dach auswählen zeigt hier seine Parameter. Ablauf: Wand ziehen (W) → Dach (D) über den Umriss → 3D.
          </div>
        </div>
      )}
        </>
      )}
      </div>
      </>
      )}
    </div>
    </>
  );
}
