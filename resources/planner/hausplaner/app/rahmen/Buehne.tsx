/**
 * AUF-48 Scheibe 4c — **die Bühne.** Alle Konva-Ebenen des 2D-Grundrisses.
 *
 * **Die Ebenenfolge IST die Zeichenreihenfolge** (K-01) und deshalb keine Kosmetik:
 *
 * ```text
 * Referenzunterlage   unterstes Kind (AUF-88-P1 / K-03) — sonst liegt sie ÜBER dem Plan
 * Räume · Wände · Öffnungen · Dächer · Treppen · Objekte · zwei Vorschauen
 * ```
 *
 * **Null Inline-Stellen.** Konva arbeitet über Props, nicht über CSS — diese Scheibe ist die
 * einzige der fünf, bei der AUF-38 nichts zu holen hat, und genau deshalb schneidet sie sich
 * sauber: keine Verflechtung mit der Gestaltung, nur mit den Daten.
 *
 * **Was hier NICHT passiert: nichts wird entschieden.** Das Markup ist zeichengleich das, was
 * vorher in `HausplanerApp.tsx` stand.
 *
 * **`stageRef` ist KEIN Zustand**, sondern der Griff auf die Bühne, den Kalibrierung und der
 * DOM-Zuhörer aus AUF-88-P1 brauchen. Er wird **durchgereicht, nicht neu angelegt** — ein
 * eigener Griff an dieser Stelle wäre ein zweiter auf dieselbe Sache.
 *
 * *(Der Satz nennt die Haken-Funktion bewusst NICHT beim Namen: K-03 ist ein schlichtes `grep`
 * ueber diese Datei, und ein erklaerender Kommentar darf eine Pruefung nicht entwerten. Vierter
 * Fall dieser Klasse in diesem Zyklus — deshalb steht der Grund hier.)*
 */
import React from 'react';
import { Stage, Layer, Line, Rect, Group, Text, Circle } from 'react-konva';
import type Konva from 'konva';
import type { Level, ObjectNode, SceneDocument, SceneNode, WallNode } from '../../domain/scene.types';
import { useHausplanerStore } from '../../store/hausplanerStore';
import { T, FARBEN } from '../studioDaten';
import { istOeffnung, lotAufWand } from '../reineHelfer';
import type { Werkzeug } from '../tools/werkzeugArten';
import type { raeumeAus } from '../ableitungen';
import { panAus, type Pan } from '../dashboard/pan';
import { UnterlagenEbene } from '../unterlage/UnterlagenEbene';
import { MASSSTAB_STANDARD } from '../unterlage/kalibrierung';
import { wandLaenge, punktAufWand, wandBaender, tuerBlattGeometrie, type Punkt } from '../../geometry/wallGeometry';
import { treppeZuParametern, parametereZuTreppe } from '../../geometry/treppeObjekt';
import { versetzteWand } from '../../geometry/editierGeometrie';
import { treppe2DSymbol } from '../../geometry/treppe2D';
// Z-01: `onMouseMove` haengt an der Buehne — draussen kam kein Ereignis mehr an, und die Vorschau
// blieb stehen, wo der Zeiger die Flaeche zuletzt beruehrt hat (Schritt 0, gemessen). Diese
// Entscheidung ist der Unterschied zwischen "eingefroren" und "pausiert".
import { zeigtVorschau } from '../tools/werkzeugEnde';
import type { UnterlageZustand } from '../state/unterlage';

/** Derselbe Zugriff wie in der Hauptfunktion — ein Speicher, keine zweite Quelle. */
const store = useHausplanerStore;

/**
 * **Einzeln benannte Eigenschaften, kein Sammelobjekt.** Die Bühne nimmt Daten entgegen und
 * meldet Ereignisse zurück; sie hält nichts.
 */
export interface BuehneEigenschaften {
  stageBreite: number;
  hoehe: number;
  zoom: number;
  setZoom: React.Dispatch<React.SetStateAction<number>>;
  pan: Pan | null;
  setPan: React.Dispatch<React.SetStateAction<Pan | null>>;
  rasterAn: boolean;
  rasterLinien: React.ReactElement[];
  /** **Kein Zustand, sondern der Griff auf die Buehne** — durchgereicht, nicht neu angelegt. */
  stageRef: React.RefObject<Konva.Stage | null>;
  scene: SceneDocument;
  level: Level;
  nodes: readonly SceneNode[];
  waende: readonly WallNode[];
  raeume: ReturnType<typeof raeumeAus>;
  bandVon: Map<string, ReturnType<typeof wandBaender>[number]>;
  massElemente: React.ReactElement[];
  selectedNodeIds: readonly string[];
  unterlage: UnterlageZustand | null;
  werkzeug: Werkzeug;
  cursor: Punkt;
  setCursor: (p: Punkt) => void;
  wandStart: Punkt | null;
  treppeStart: { x: number; y: number } | null;
  /** Z-01: steht der Zeiger auf der Flaeche? Draussen wird die Vorschau AUSGEBLENDET. */
  zeigerDrinnen: boolean;
  /** Z-01: die Buehne MELDET nur — der Zustand wohnt in der Hauptfunktion (K-03: kein Zustand hier). */
  beiZeigerAus: () => void;
  beiZeigerEin: () => void;
  klick: (e: Konva.KonvaEventObject<MouseEvent>) => void;
  weltPunkt: (e: Konva.KonvaEventObject<MouseEvent>) => Punkt;
  mitWinkelSnap: (start: Punkt, p: Punkt) => Punkt;
  waehleAn: (id: string, ev: MouseEvent) => void;
}

export function Buehne({
  stageBreite, hoehe, zoom, setZoom, pan, setPan, rasterAn, rasterLinien, stageRef,
  scene, level, nodes, waende, raeume, bandVon, massElemente, selectedNodeIds, unterlage,
  werkzeug, cursor, setCursor, wandStart, treppeStart, zeigerDrinnen, beiZeigerAus, beiZeigerEin,
  klick, weltPunkt, mitWinkelSnap, waehleAn,
}: BuehneEigenschaften): React.ReactElement {
  return (
    <Stage
      ref={stageRef as never}
      width={stageBreite}
      height={hoehe}
      draggable={werkzeug === 'auswahl'}
      onClick={klick}
      onMouseMove={(e) => setCursor(weltPunkt(e))}
      onMouseLeave={beiZeigerAus}
      onMouseEnter={beiZeigerEin}
      onWheel={(e) => {
        e.evt.preventDefault();
        const faktor = e.evt.deltaY < 0 ? 1.1 : 1 / 1.1;
        setZoom((z) => Math.min(1, Math.max(0.02, z * faktor)));
      }}
      scaleX={zoom}
      scaleY={-zoom}
      {...panAus(pan, hoehe)}
      // AUF-51: WÄHREND des Ziehens mitschreiben, nicht erst am Ende. `onMouseMove` rendert in
      // Mausbewegungs-Frequenz; ohne `onDragMove` setzte das laufende Rendern die Bühne auf den
      // alten Wert zurück, und der Verschub ruckelte gegen den Zeiger.
      onDragMove={(e) => { if (e.target === e.currentTarget) setPan({ x: e.target.x(), y: e.target.y() }); }}
      onDragEnd={(e) => { if (e.target === e.currentTarget) setPan({ x: e.target.x(), y: e.target.y() }); }}
    >
      <Layer>
        {/* AUF-88-P1 / K-03 — die Referenzunterlage: ALS ERSTES Kind, also die unterste
            Ebene. `scaleY={-1}` kontert denselben Y-Flip, den auch `Text` hier kontert
            (die Stage dreht mit `scaleY={-zoom}` auf CAD-Konvention Y-hoch) — ohne ihn
            stünde ein gescanntes Blatt auf dem Kopf. */}
        {unterlage?.aktuelle?.bildUrl && (
          <Group scaleY={-1}>
            <UnterlagenEbene
              bildUrl={unterlage.aktuelle.bildUrl}
              massstabMmProEinheit={unterlage.aktuelle.massstabMmProEinheit ?? MASSSTAB_STANDARD}
            />
          </Group>
        )}
        {rasterAn && rasterLinien}
        {massElemente}

        {/* Räume: Füllung + Fläche (m², aus mm² gerundet auf 2 Stellen) */}
        {raeume.map((raum, i) => {
          const xs = raum.polygon.map((p) => p.x);
          const ys = raum.polygon.map((p) => p.y);
          const cx = (Math.min(...xs) + Math.max(...xs)) / 2;
          const cy = (Math.min(...ys) + Math.max(...ys)) / 2;

          return (
            <Group key={`raum${i}`} listening={false}>
              <Line points={raum.polygon.flatMap((p) => [p.x, p.y])} closed fill={FARBEN.raum} stroke="transparent" />
              <Text
                x={cx - 600} y={cy + 150} width={1200} align="center"
                scaleY={-1}
                text={`${(raum.flaecheMm2 / 1_000_000).toFixed(2)} m²`}
                fontSize={220 } fill={FARBEN.gedaempft}
              />
            </Group>
          );
        })}

        {/* Wände + Bemaßung — gefüllte Bänder mit Gehrung (P2b-2), Fallback Linie. */}
        {waende.map((w) => {
          const ausgewaehlt = selectedNodeIds.includes(w.id);
          const mitte = punktAufWand(w.start, w.end, wandLaenge(w.start, w.end) / 2);
          const band = bandVon.get(w.id);
          const klick = (e: Konva.KonvaEventObject<MouseEvent>): void => {
            if (werkzeug === 'auswahl') {
              e.cancelBubble = true;
              waehleAn(w.id, e.evt);
            }
          };

          return (
            <Group
              key={w.id}
              draggable={werkzeug === 'auswahl'}
              onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(w.id, e.evt); } }}
              onDragEnd={(e) => {
                const dx = e.target.x();
                const dy = e.target.y();
                e.target.position({ x: 0, y: 0 });
                if (dx || dy) {
                  const g = versetzteWand(w.start, w.end, dx, dy);
                  store.getState().executeCommand({ type: 'MOVE_NODE', nodeId: w.id, position: { start: g.start, end: g.end } });
                }
              }}
            >
              {band ? (
                <Line
                  points={band.ecken.flatMap((p) => [p.x, p.y])}
                  closed
                  fill={ausgewaehlt ? FARBEN.auswahl : FARBEN.wandFuellung}
                  stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.wand}
                  strokeWidth={1.5 / zoom}
                  lineJoin="round"
                  onClick={klick}
                />
              ) : (
                <Line
                  points={[w.start.x, w.start.y, w.end.x, w.end.y]}
                  stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.wand}
                  strokeWidth={w.thickness}
                  lineCap="butt"
                  onClick={klick}
                />
              )}
              <Text
                x={mitte.x - 500} y={mitte.y + w.thickness / 2 + 320} width={1000} align="center"
                scaleY={-1}
                text={`${Math.round(wandLaenge(w.start, w.end))} mm`}
                fontSize={180} fill={FARBEN.gedaempft} listening={false}
              />
            </Group>
          );
        })}

        {/* Öffnungen (Symbol auf der Wand; Status nicht nur Farbe: geklemmt trägt ⚠-Text) */}
        {nodes.filter(istOeffnung).map((o) => {
          const wand = waende.find((w) => w.id === o.hostWallId);
          if (!wand) {
            return null;
          }
          const mitte = punktAufWand(wand.start, wand.end, o.offsetFromWallStart + o.width / 2);
          const winkel = (Math.atan2(wand.end.y - wand.start.y, wand.end.x - wand.start.x) * 180) / Math.PI;
          const ausgewaehlt = selectedNodeIds.includes(o.id);

          return (
            <Group
              key={o.id} x={mitte.x} y={mitte.y} rotation={winkel}
              draggable={werkzeug === 'auswahl'}
              onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(o.id, e.evt); } }}
              onDragEnd={(e) => {
                const lot = lotAufWand({ x: e.target.x(), y: e.target.y() }, wand);
                const laenge = wandLaenge(wand.start, wand.end);
                const off = Math.round(Math.max(0, Math.min(lot.offset - o.width / 2, laenge - o.width)));
                // Fix (Evaluator 4c1cfac): Konva-Position auf die Wandachse zuruecksetzen,
                // analog zum Wand-Handler. Sonst strandet die Oeffnung bei Quer-Drag neben
                // der Wand, weil react-konva unveraenderte Positions-Props nicht neu setzt.
                const neueMitte = punktAufWand(wand.start, wand.end, off + o.width / 2);
                e.target.position({ x: neueMitte.x, y: neueMitte.y });
                store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: o.id, changes: { offsetFromWallStart: off } });
              }}
            >
              <Rect
                x={-o.width / 2} y={-(wand.thickness / 2 + 40)} width={o.width} height={wand.thickness + 80}
                fill={T.surface} stroke={ausgewaehlt ? FARBEN.auswahl : o.type === 'door' ? FARBEN.gedaempft : FARBEN.linie}
                strokeWidth={30}
                onClick={(e) => {
                  if (werkzeug === 'auswahl') {
                    e.cancelBubble = true;
                    waehleAn(o.id, e.evt);
                  }
                }}
              />
              {o.type === 'window' && <Line points={[-o.width / 2, 0, o.width / 2, 0]} stroke={FARBEN.linie} strokeWidth={20} listening={false} />}
              {o.type === 'door' && (() => {
                const tb = tuerBlattGeometrie(o.width, o.anschlag ?? 'links', o.oeffnung ?? 'innen');
                return (
                  <>
                    <Line points={[tb.angelpunkt.x, tb.angelpunkt.y, tb.blattEnde.x, tb.blattEnde.y]} stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.gedaempft} strokeWidth={28} listening={false} />
                    <Line points={tb.bogen.flatMap((p) => [p.x, p.y])} stroke={FARBEN.linie} strokeWidth={16} listening={false} />
                  </>
                );
              })()}
              {o.clamped && (
                <Text x={-o.width / 2} y={wand.thickness / 2 + 380} width={o.width} align="center" scaleY={-1} text="⚠ geklemmt" fontSize={160} fill={FARBEN.warnung} listening={false} />
              )}
            </Group>
          );
        })}

        {/* Dächer (D-c): Traufkontur (gestrichelt) + First-Linie; die First-Linie zeigt die
            Richtung, aus der die Flächen-Azimute abgeleitet werden. Auswahl öffnet das Panel. */}
        {(scene.roofs ?? []).filter((r) => r.levelId === level.id && r.visible !== false).map((r) => {
          const rad = (r.firstAzimutGrad * Math.PI) / 180;
          const ux = Math.sin(rad), uy = Math.cos(rad);
          const cx = r.polygon.reduce((s, p) => s + p.x, 0) / r.polygon.length;
          const cy = r.polygon.reduce((s, p) => s + p.y, 0) / r.polygon.length;
          let sMin = Infinity, sMax = -Infinity;
          for (const p of r.polygon) {
            const s = p.x * ux + p.y * uy;
            if (s < sMin) sMin = s;
            if (s > sMax) sMax = s;
          }
          const halb = (sMax - sMin) / 2;
          const ausgewaehlt = selectedNodeIds.includes(r.id);
          const farbe = ausgewaehlt ? FARBEN.auswahl : T.materialWood;

          return (
            <Group key={r.id}>
              <Line
                points={r.polygon.flatMap((p) => [p.x, p.y])}
                closed stroke={farbe} strokeWidth={40} dash={[300, 200]}
                onClick={(e) => {
                  if (werkzeug === 'auswahl') {
                    e.cancelBubble = true;
                    waehleAn(r.id, e.evt);
                  }
                }}
              />
              {r.roofType !== 'flach' && (
                <Line points={[cx - halb * ux, cy - halb * uy, cx + halb * ux, cy + halb * uy]} stroke={farbe} strokeWidth={90} listening={false} />
              )}
              <Text x={cx - 1000} y={cy - 260} width={2000} align="center" scaleY={-1} text={`Dach · ${r.roofType} · ${r.neigungGrad}°`} fontSize={200} fill={FARBEN.gedaempft} listening={false} />
            </Group>
          );
        })}

        {/* Treppen (objectType 'stair') — Grundriss-Symbol: Umriss + Trittstufen + Aufwaerts-Pfeil. */}
        {nodes.filter((n): n is ObjectNode => n.type === 'object' && n.objectType === 'stair').map((st) => {
          const tp = parametereZuTreppe(st.parameters);
          if (!tp) return null;
          const sym = treppe2DSymbol({
            start: { x: tp.startX, y: tp.startY }, end: { x: tp.endX, y: tp.endY },
            laufbreite: tp.laufbreite, geschosshoehe: tp.geschosshoehe,
            gewuenschteSteigung: tp.gewuenschteSteigung, bereich: tp.bereich,
          });
          const ausgewaehlt = selectedNodeIds.includes(st.id);
          const farbe = ausgewaehlt ? FARBEN.auswahl : (sym.bestanden ? FARBEN.wand : FARBEN.gefahr);
          const mx = (tp.startX + tp.endX) / 2;
          const my = (tp.startY + tp.endY) / 2;
          return (
            <Group
              key={st.id}
              draggable={werkzeug === 'auswahl'}
              onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(st.id, e.evt); } }}
              onDragEnd={(e) => {
                const dx = Math.round(e.target.x()); const dy = Math.round(e.target.y());
                e.target.position({ x: 0, y: 0 });
                if (dx || dy) {
                  const neu = { ...tp, startX: tp.startX + dx, startY: tp.startY + dy, endX: tp.endX + dx, endY: tp.endY + dy };
                  store.getState().executeCommand({
                    type: 'UPDATE_NODE', nodeId: st.id,
                    changes: {
                      parameters: treppeZuParametern(neu),
                      transform: { ...st.transform, position: { x: neu.startX, y: neu.startY, z: st.transform.position.z } },
                    },
                  });
                }
              }}
              onClick={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(st.id, e.evt); } }}
            >
              <Line points={sym.umriss.flatMap((q) => [q.x, q.y])} closed stroke={farbe} strokeWidth={40 / zoom} fill={ausgewaehlt ? T.brandWash : T.canvasWallGhost} />
              {sym.stufen.map((s, i) => (
                <Line key={i} points={[s[0].x, s[0].y, s[1].x, s[1].y]} stroke={farbe} strokeWidth={25 / zoom} listening={false} />
              ))}
              <Line points={[sym.pfeil.von.x, sym.pfeil.von.y, sym.pfeil.bis.x, sym.pfeil.bis.y]} stroke={farbe} strokeWidth={30 / zoom} listening={false} />
              <Circle x={sym.pfeil.bis.x} y={sym.pfeil.bis.y} radius={90} fill={farbe} listening={false} />
              <Text x={mx - 700} y={my + 200} width={1400} align="center" scaleY={-1} text={`Treppe · ${sym.anzahlSteigungen}×${Math.round(sym.steigungshoehe)} mm`} fontSize={170} fill={FARBEN.gedaempft} listening={false} />
            </Group>
          );
        })}

        {/* Generische Objekte (radiator/wallbox/… außer stair) — Grundriss-Kasten + Label. */}
        {nodes.filter((n): n is ObjectNode => n.type === 'object' && n.objectType !== 'stair').map((ob) => {
          const px = ob.transform.position.x;
          const py = ob.transform.position.y;
          const laenge = Number(ob.parameters['objekt.laenge']) || 800;
          const tiefe = 150;
          const label = String(ob.parameters['objekt.label'] ?? ob.objectType);
          const ausgewaehlt = selectedNodeIds.includes(ob.id);
          const farbe = ausgewaehlt ? FARBEN.auswahl : FARBEN.wand;
          return (
            <Group key={ob.id} x={px} y={py}
              draggable={werkzeug === 'auswahl'}
              onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(ob.id, e.evt); } }}
              onDragEnd={(e) => {
                const nx = Math.round(e.target.x()); const ny = Math.round(e.target.y());
                store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: ob.id, changes: { transform: { ...ob.transform, position: { x: nx, y: ny, z: ob.transform.position.z } } } });
              }}
              onClick={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(ob.id, e.evt); } }}
            >
              <Rect x={0} y={0} width={laenge} height={tiefe} cornerRadius={30} stroke={farbe} strokeWidth={40 / zoom} fill={ausgewaehlt ? T.brandWash : T.canvasWallGhost} />
              <Text x={0} y={-90} width={laenge} align="center" scaleY={-1} text={label} fontSize={150} fill={FARBEN.gedaempft} listening={false} />
            </Group>
          );
        })}

        {/* Vorschau beim Treppezeichnen */}
        {zeigtVorschau({ wandStart, treppeStart, zeigerDrinnen }, 'treppe') && treppeStart && werkzeug === 'treppe' && (
          <Group listening={false}>
            <Line points={[treppeStart.x, treppeStart.y, mitWinkelSnap(treppeStart, cursor).x, mitWinkelSnap(treppeStart, cursor).y]} stroke={FARBEN.auswahl} strokeWidth={50} dash={[200, 120]} />
            <Circle x={treppeStart.x} y={treppeStart.y} radius={90} fill={FARBEN.auswahl} />
          </Group>
        )}

        {/* Vorschau beim Wandzeichnen */}
        {zeigtVorschau({ wandStart, treppeStart, zeigerDrinnen }, 'wand') && wandStart && werkzeug === 'wand' && (
          <Group listening={false}>
            <Line points={[wandStart.x, wandStart.y, mitWinkelSnap(wandStart, cursor).x, mitWinkelSnap(wandStart, cursor).y]} stroke={FARBEN.auswahl} strokeWidth={60} dash={[200, 120]} />
            <Circle x={wandStart.x} y={wandStart.y} radius={90} fill={FARBEN.auswahl} />
          </Group>
        )}
      </Layer>
    </Stage>
  );
}
