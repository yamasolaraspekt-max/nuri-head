// @ts-nocheck
/* eslint-disable */
// Integrierter Gemini-Prototyp "Dachdecker Pro 3D" — vollstaendiges 3D-Dachplaner-Tool
// (zum Ausprobieren/Vergleichen). Prototyp mit eigenem State/Mock-Daten; three ist
// installiert. Spaetere Haertung/Backend-Anbindung als Folgeschritt.
import React, { useState, useEffect, useRef, useMemo } from 'react';
// Produktauswahl: echtes Ziegelmodell bzw. echte Flachdach-/Metall-Eindeckung (statt Pauschalwert).
import { EindeckungMaterialPanel } from '../../components/energie/EindeckungMaterialPanel';
import { CoveringMaterialPanel } from '../../components/energie/CoveringMaterialPanel';
import { MontagesystemPanel } from '../../components/energie/MontagesystemPanel';
import { MontageStueckliste } from '../../components/energie/MontageStueckliste';
import { roofConfigStore, useRoofSlice, useObstaclesSlice } from '../../stores/roofConfigStore';
// ObstacleData liegt jetzt zentral in stores/roofTypes (EINE Quelle, keine Typ-Dublette).
import type { ObstacleData } from '../../stores/roofTypes';

// Dachfamilien je Eindeckungs-Auswahl (für den Covering-Picker).
const COVER_FAMILIES: Record<string, string[]> = {
  bitumen: ['bitumen'],
  kunststoff: ['kunststoffbahn'],
  trapezblech: ['metall_profil', 'metall_stehfalz'],
  schiefer: ['schiefer'],
};
// Reparatur 1: zentrale, getestete Klemm-/Umrechnungslogik (gleiche Mindestwerte
// in Geometrie UND Stück-/Holzliste) — verhindert NaN/Infinity bei ungültigen Maßen.
import { dachstuhlMasseM, dachstuhlHinweise, effektivCm, sichererCos, DACH_FLOOR_CM } from '../../utils/dachWerte';
// Reparatur 2: testbare Statuslogik gegen stillen Verlust der PV-Belegung bei Geometrieänderung.
import { geometrieMachtPruefpflichtig, BELEGUNG_WARNUNG } from '../../utils/belegungStatus';
// Reparatur 3: testbare Statuslogik, um Aufbauten nach Geometrieänderung nachzuziehen / als prüfpflichtig zu markieren.
import { aufbautenOhneFlaeche, istAufbauPruefpflichtig, AUFBAUTEN_WARNUNG } from '../../utils/aufbautenStatus';
// Reparatur 5: testbare Werkstattplan-Logik (Snapshot/Restore der Ebenen + Transparenz).
import { werkstattplanAnsicht, istWerkstattplanEintritt, istWerkstattplanAustritt } from '../../utils/werkstattplan';
// Reparatur 6: reine Polygon-Flächenberechnung (echte geneigte Dachfläche statt Rechteck-Rahmen).
import { polygonFlaecheM2 } from '../../utils/polygonFlaeche';
// Reparatur 7: echte Holzlängen (Sparren/Konter/Latten) aus der gezeichneten Geometrie — eine Mengenwahrheit.
import { holzMengenAusListe } from '../../utils/holzMengen';
// Reparatur 8: weitere echte Holzbauteile (Pfetten/Grat-/Kehlsparren) aus der gezeichneten Geometrie in die Liste.
import { holzBauteileAusListe } from '../../utils/holzBauteile';
// Eingabeaufforderung 28: Schiftsparren-Klassifikation (geclippte Sparren an Kehle/Grat) + „davon"-Breakdown.
import { klassifiziereSchifter, schifterMengenAusListe } from '../../utils/schifterListe';
// Reparatur 9: Sparren-Öffnungs-Verschneidung -> Auswechslungen/Wechselhölzer (geometrisch, ehrlich).
import { analysiereAuswechslung } from '../../utils/auswechslung';
// Reparatur 10: Sparren an Öffnungen real in Teilstücke trennen (nur sichere Fälle).
import { sparrenTeilstuecke } from '../../utils/sparrenTrennung';
// Dachform-Vorlagenbibliothek (reines Modul): Vorlagen-Daten + Suche/Filter + reines Apply-Mapping.
// Anwenden läuft AUSSCHLIESSLICH über setBuild/setCover -> der vorhandene useEffect feuert Rep-2/3-Warnungen.
import { DACHFORM_VORLAGEN, sucheVorlagen, filterVorlagen, applyVorlage, validateVorlage, vorschauSvg, istVorschauVersprechen, vorschauZeigtAufbau, vorschauZeigtPv, anzeigeStatus, aufbautenWerdenGesetzt, aufbautenNichtGesetzt, schneefangWirdGesetzt, gaubeSchematischGesetzt, AUFBAU_AUTO_HINWEIS, VORSCHAU_AUFBAU_HINWEIS, VORSCHAU_PV_HINWEIS, SCHNEEFANG_HINWEIS, GAUBE_SCHEMATISCH_HINWEIS } from '../../utils/dachformVorlagen';
// Eingabeaufforderung 12: linienförmige Dachbauteile (Schneefang) — flächenabhängig, mit PV-Sperrzone.
import { platziereSchneefang, flaecheInfoAusPolygon, type DachLinienBauteil } from '../../utils/linienBauteile';
// Eingabeaufforderung 13: zusammengesetzte Grundrisse (L/T/U) als echtes Polygon (keine Doppelzählung).
import { grundrissPolygon, formAusShape } from '../../utils/grundriss';
// Eingabeaufforderung 15: Dachöffnungen / Prüffelder (schematisch) für Gauben/Dachfenster/Kamin.
import { oeffnungRechteck, oeffnungVTiefeM } from '../../utils/dachOeffnung';
// Korrektur Schweben: lotrechte Orientierung für stehende Aufbauten (Gaube/Kamin) statt Flächen-Kippung.
import { stehendeAufbauBasis, istStehenderAufbau } from '../../utils/aufbauOrientierung';
// Eingabeaufforderung 17: korrekte, numerisch geprüfte Gauben-/Kamin-Geometrie + Anschluss ans Hauptdach.
import { pultGaubeGeometrie, giebelGaubeGeometrie, kaminGeometrie, type SurfaceFrame as GaubeSurfaceFrame, type Dreieck as GaubeDreieck, type Linie as GaubeLinie } from '../../utils/gaubeGeometrie';
// Eingabeaufforderung 18/19: maßhaltige Dachöffnungen + echte Dachhaut-Löcher (sichere Rechteckflächen).
import { sichereLoecher } from '../../utils/dachAusschnitt';
// Eingabeaufforderung 26: echte Kehl-/Gratlinien geneigter L-/T-Verschneidung (SSOT, sichtbarer Overlay).
import { verschneidungslinien } from '../../utils/dachVerschneidung';
// Eingabeaufforderung 27: geneigte U-Form (SSOT der Flächen/Kehlsparren/Gültigkeit) — Engine zeichnet exakt das Geprüfte.
import { uFormFlaechen, uFormKehlsparren, uFormKonstanten, uBauGueltig } from '../../utils/dachUForm';
import type { DachformVorlage, VorlagenStatus, RoofCategory as VorlagenRoofCategory } from '../../utils/dachformVorlagen';
import { Badge } from '../../components/ui/Badge';
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { 
  Home, Box, Hammer, Trash2, Maximize2, Wind, Disc, 
  MousePointer2, ArrowUpRight, Layers, Check, RotateCcw, 
  ClipboardList, Package, Move, Eye, EyeOff, ListChecks, Ruler, 
  PanelLeftClose, PanelRightClose, ChevronsRight, Menu, Plus, Edit2,
  ChevronDown, ChevronUp, ZoomIn, ZoomOut, Maximize, 
  Tent, Building, Square, Settings, Lock, Unlock, Trees, X, Download, BarChart, Play, Sun, LayoutTemplate, Zap, Minus, Triangle
} from 'lucide-react';

// ==========================================
// 1. TYPEN & KONFIGURATION
// ==========================================
type ViewMode = 'construct' | 'obstacles' | 'modules' | 'bom' | 'work';
type RoofCategory = 'pitched' | 'flat';
type RoofShape = 'sattel' | 'pult' | 'walm' | 'rect' | 'l-shape' | 't-shape' | 'u-shape';
type RoofCovering = 'ziegel' | 'schiefer' | 'trapezblech' | 'bitumen' | 'kunststoff' | 'gruendach' | 'kies';
type ObstacleType = 'chimney' | 'window' | 'vent' | 'sat' | 'lichtkuppel' | 'schleppgaube' | 'trapezgaube' | 'flachgaube' | 'giebelgaube' | 'spitzgaube';
type InteractionTool = 'select' | 'move' | 'delete' | 'rotate' | 'abbund';
type EdgeTopologyType = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM';
type TopologyCornerType = 'innen' | 'aussen';
type TopologyJoinType = 'grat' | 'kehle' | 'ortgang' | 'neutral';

// ObstacleData wird jetzt aus stores/roofTypes importiert (siehe oben) — die frühere
// lokale Definition ist entfallen, um die Typ-Dublette aufzulösen (Konzept A1/R2).
// (ObstacleType bleibt lokal, da der Planer ihn für eigene Casts/Defaults nutzt.)

interface BuildingParams {
  category: RoofCategory; shape: RoofShape;
  length: number; width: number; height: number;
  pitch: number; attika: number; 
  overhang: number; overhangGable: number; 
  lengthB: number; widthB: number;
  layerSpread: number; 
  rafterSpacing: number; rafterWidth: number; rafterHeight: number; 
  battenDist: number; 
}

interface LayerConfig {
    id: string; name: string; visible: boolean; deleted: boolean; isSystem: boolean; category: 'dach';
}

interface AdditionalRoof {
    id: string; name: string; type: 'pult' | 'flat' | 'sattel'; 
    length: number; width: number; height: number; pitch: number; offsetX: number; offsetZ: number;
}

interface SurfaceConfig {
  enabled: boolean; orientation: 'portrait' | 'landscape';
  gap: number; margin: number; customName?: string; locked?: boolean;   
}

interface ModuleData {
  id: string; surfaceId: string; x: number; y: number; row: number; col: number;
  selected?: boolean; rotation?: number; isPortrait?: boolean; 
}

interface TopologyPoint {
  x: number;
  y: number;
}

interface EdgeTopologyConfig {
  id: string;
  type: EdgeTopologyType;
  pitch: number;
  label: string;
}

interface TopologyCornerInfo {
  index: number;
  point: TopologyPoint;
  angleDeg: number;
  cornerType: TopologyCornerType;
  joinType: TopologyJoinType;
}

interface TopologyAnalysis {
  points: TopologyPoint[];
  edgeConfigs: EdgeTopologyConfig[];
  corners: TopologyCornerInfo[];
  innenEcken: number;
  aussenEcken: number;
  grate: number;
  kehlen: number;
  ortgaenge: number;
}

// --- TOPOLOGIE FUNKTIONEN WIEDERHERGESTELLT ---
function buildTopologyPolygon(build: BuildingParams): TopologyPoint[] {
  const L = Math.max(0.1, build.length);
  const W = Math.max(0.1, build.width);
  const LB = Math.max(0.1, build.lengthB);
  const WB = Math.max(0.1, build.widthB);

  // Eingabeaufforderung 13: Flachdach (rect/L/T/U) IMMER aus derselben Grundriss-Quelle wie buildFlat
  // ableiten -> Kanten-/Topologie-Overlay deckt sich exakt mit der 3D-Geometrie (auch Flach-T/Flach-U).
  if (build.category === 'flat') {
    return grundrissPolygon(formAusShape(build.shape), L, W, LB, WB).map(pt => ({ x: pt.x - L / 2, y: pt.y - W / 2 }));
  }
  if (build.shape === 'l-shape') {
    return [
      { x: -L / 2, y: -W / 2 }, { x: L / 2, y: -W / 2 }, { x: L / 2, y: 0 },
      { x: L / 2 - WB, y: 0 }, { x: L / 2 - WB, y: W / 2 + LB }, { x: -L / 2, y: W / 2 + LB },
    ];
  }
  if (build.shape === 't-shape') {
    return [
      { x: -L / 2, y: -W / 2 }, { x: L / 2, y: -W / 2 }, { x: L / 2, y: W / 2 },
      { x: WB / 2, y: W / 2 }, { x: WB / 2, y: W / 2 + LB }, { x: -WB / 2, y: W / 2 + LB },
      { x: -WB / 2, y: W / 2 }, { x: -L / 2, y: W / 2 },
    ];
  }
  return [ { x: -L / 2, y: -W / 2 }, { x: L / 2, y: -W / 2 }, { x: L / 2, y: W / 2 }, { x: -L / 2, y: W / 2 } ];
}

function getDefaultEdgeTopologyConfigs(build: BuildingParams, pointCount: number): EdgeTopologyConfig[] {
  const make = (type: EdgeTopologyType, index: number): EdgeTopologyConfig => ({
    id: `edge_${index}`, type, pitch: type === 'GIEBEL' ? 0 : build.pitch, label: `Kante ${index + 1}`,
  });
  if (build.category === 'flat') return Array.from({ length: pointCount }, (_, i) => make('TRAUFE', i));
  if (build.shape === 'pult') return Array.from({ length: pointCount }, (_, i) => make(['TRAUFE', 'GIEBEL', 'PULT_WAND', 'GIEBEL'][i] || 'GIEBEL', i));
  if (build.shape === 'walm') return Array.from({ length: pointCount }, (_, i) => make('TRAUFE', i));
  if (build.shape === 'sattel') return Array.from({ length: pointCount }, (_, i) => make(['TRAUFE', 'GIEBEL', 'TRAUFE', 'GIEBEL'][i] || 'GIEBEL', i));
  return Array.from({ length: pointCount }, (_, i) => make('TRAUFE', i));
}

function analyzeTopology(points: TopologyPoint[], edgeConfigs: EdgeTopologyConfig[]): TopologyAnalysis {
  const signedArea = points.reduce((acc, p, i) => { const n = points[(i + 1) % points.length]; return acc + p.x * n.y - n.x * p.y; }, 0) / 2;
  const isCCW = signedArea > 0;
  const corners: TopologyCornerInfo[] = points.map((point, index) => {
    const prev = points[(index - 1 + points.length) % points.length];
    const next = points[(index + 1) % points.length];
    const v1x = prev.x - point.x, v1y = prev.y - point.y;
    const v2x = next.x - point.x, v2y = next.y - point.y;
    const l1 = Math.hypot(v1x, v1y) || 1, l2 = Math.hypot(v2x, v2y) || 1;
    const n1x = v1x / l1, n1y = v1y / l1, n2x = v2x / l2, n2y = v2y / l2;
    const dot = Math.max(-1, Math.min(1, n1x * n2x + n1y * n2y));
    const baseAngle = Math.acos(dot) * 180 / Math.PI;
    const cross = n1x * n2y - n1y * n2x;
    const isInnerReflex = isCCW ? cross > 0 : cross < 0;
    const angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle;
    const cornerType: TopologyCornerType = angleDeg > 180 ? 'innen' : 'aussen';

    const prevEdge = edgeConfigs[(index - 1 + edgeConfigs.length) % edgeConfigs.length];
    const nextEdge = edgeConfigs[index % edgeConfigs.length];
    const prevIsTraufe = prevEdge?.type === 'TRAUFE' || prevEdge?.type === 'WALM' || prevEdge?.type === 'TEILWALM';
    const nextIsTraufe = nextEdge?.type === 'TRAUFE' || nextEdge?.type === 'WALM' || nextEdge?.type === 'TEILWALM';

    let joinType: TopologyJoinType = 'neutral';
    if (prevIsTraufe && nextIsTraufe) joinType = cornerType === 'innen' ? 'kehle' : 'grat';
    else if ((prevIsTraufe && nextEdge?.type === 'GIEBEL') || (nextIsTraufe && prevEdge?.type === 'GIEBEL')) joinType = 'ortgang';

    return { index, point, angleDeg, cornerType, joinType };
  });

  return {
    points, edgeConfigs, corners,
    innenEcken: corners.filter(c => c.cornerType === 'innen').length,
    aussenEcken: corners.filter(c => c.cornerType === 'aussen').length,
    grate: corners.filter(c => c.joinType === 'grat').length,
    kehlen: corners.filter(c => c.joinType === 'kehle').length,
    ortgaenge: corners.filter(c => c.joinType === 'ortgang').length,
  };
}

// --- DATENBANKEN ---
const MATERIAL_PROPS = {
  ziegel: { name: 'Dachziegel (Ton/Beton)', weightPerM2: 45, piecesPerM2: 12 },
  schiefer: { name: 'Naturschiefer', weightPerM2: 30, piecesPerM2: 35 },
  trapezblech: { name: 'Trapezblech', weightPerM2: 8, piecesPerM2: 1 },
  bitumen: { name: 'Bitumenschweißbahn', weightPerM2: 5, piecesPerM2: 1 },
};

const MODULE_TYPES = [
  { id: 't440', manufacturer: 'Trina Solar', name: 'Vertex S+ 440W', watts: 440, width: 1.134, height: 1.762, depth: 0.030, weight: 21.0, price: 110 },
  { id: 'j450', manufacturer: 'Ja Solar', name: 'JAM54S30 450W', watts: 450, width: 1.134, height: 1.722, depth: 0.030, weight: 20.5, price: 115 },
  { id: 'l500', manufacturer: 'LONGi', name: 'Hi-MO 6 Explorer 500W', watts: 500, width: 1.134, height: 1.950, depth: 0.035, weight: 23.5, price: 155 },
];

const VELUX_WINDOW_LIBRARY = [
  { id: 'CK02', label: 'VELUX CK02', width: 0.55, height: 0.78 },
  { id: 'CK04', label: 'VELUX CK04', width: 0.55, height: 0.98 },
  { id: 'MK06', label: 'VELUX MK06', width: 0.78, height: 1.18 },
  { id: 'SK06', label: 'VELUX SK06', width: 1.14, height: 1.18 },
];

const TIME_VARS = {
  SCAFFOLD_M2: 8, RAFTER_M: 10, MEMBRANE_M2: 3, BATTEN_M: 2, 
  TILE_M2: 15, INSULATION_M2: 12, CLEANUP: 90, HOOK_STD: 6, 
  HOOK_GRIND: 5, RAIL_M: 4, MOD_MOUNT: 12
};

const FASTENER_MAPPING: any = {
  ziegel: { name: 'K2 SingleHook 3S', type: 'hook', timePerUnit: 11 },
  schiefer: { name: 'K2 Schieferhaken', type: 'hook', timePerUnit: 6 },
  trapezblech: { name: 'K2 MultiRail 25', type: 'short_rail', timePerUnit: 3 },
  bitumen: { name: 'K2 D-Dome 6', type: 'flat_system', timePerUnit: 8 },
};

// ==========================================
// 2. HELPERS & TEXTURES
// ==========================================
// Phase-0-Fix: rekursiv. Vorher wurde nur die oberste Ebene disposed und nie
// Materialien/Texturen -> bei jedem Aufbauten-Update/Drag und beim Unmount leckten
// verschachtelte BufferGeometries/Materialien (fuehrt sonst zu "Too many active
// WebGL contexts" beim Hin-/Her-Navigieren).
function disposeMaterial(mat: any) {
    if (!mat) return;
    for (const k in mat) { const v = (mat as any)[k]; if (v && v.isTexture && v.dispose) v.dispose(); }
    if (mat.dispose) mat.dispose();
}
function disposeHierarchy(node: THREE.Object3D) {
    if (!node) return;
    node.traverse((o: any) => {
        if (o.geometry && o.geometry.dispose) o.geometry.dispose();
        const m = o.material;
        if (Array.isArray(m)) m.forEach(disposeMaterial); else if (m) disposeMaterial(m);
    });
    if ((node as any).dispose) { try { (node as any).dispose(); } catch(e) {} }
}

function clearGroup(group: THREE.Group) {
    if (!group) return;
    while (group.children.length > 0) {
        const child = group.children[0];
        group.remove(child);
        disposeHierarchy(child);
    }
}

const TextureGen = {
    create: (color: string, type: string) => {
        const s = 512; const c = document.createElement('canvas'); c.width=s; c.height=s; 
        const ctx = c.getContext('2d');
        if(!ctx) return new THREE.Texture();

        if (type === 'wood') {
            ctx.fillStyle = '#dcb28b'; ctx.fillRect(0,0,s,s);
            for(let i=0; i<60; i++) {
                ctx.fillStyle = `rgba(160,120,80,${Math.random()*0.2})`;
                ctx.fillRect(Math.random()*s, 0, Math.random()*30+5, s);
            }
        } else if (type === 'house_facade') {
            ctx.fillStyle = '#f8fafc'; ctx.fillRect(0,0,s,s);
            ctx.fillStyle = 'rgba(0,0,0,0.02)';
            for(let y=0; y<s; y+=64) ctx.fillRect(0, y, s, 2);
            const baseY = s * 0.90;
            ctx.fillStyle = '#e5e7eb';
            ctx.fillRect(0, baseY, s, s - baseY);
            ctx.strokeStyle = 'rgba(255,255,255,0.55)';
            ctx.lineWidth = 3;
            ctx.strokeRect(6, 6, s-12, s-12);
        } else if (type === 'iso') {
            ctx.fillStyle = '#facc15'; ctx.fillRect(0,0,s,s);
            ctx.fillStyle = 'rgba(255,255,255,0.2)';
            for(let i=0;i<1000;i++) ctx.fillRect(Math.random()*s, Math.random()*s, 2,2);
        } else if (type === 'membrane') {
            ctx.fillStyle = '#1e293b'; ctx.fillRect(0,0,s,s);
            ctx.strokeStyle = '#334155'; ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(s,s); ctx.moveTo(s,0); ctx.lineTo(0,s); ctx.stroke();
        } else if (type === 'metal') {
             ctx.fillStyle = '#cbd5e1'; ctx.fillRect(0,0,s,s);
             for(let i=0; i<500; i++) {
                 ctx.fillStyle = `rgba(0,0,0,${Math.random()*0.1})`;
                 ctx.fillRect(Math.random()*s, Math.random()*s, 5, 5);
             }
        } else if (type === 'solar') {
            ctx.fillStyle='#050505'; ctx.fillRect(0,0,s,s);
            ctx.strokeStyle='rgba(255,255,255,0.15)'; ctx.lineWidth=1; ctx.strokeRect(2,2,s-4,s-4);
            ctx.lineWidth=0.5; 
            for(let i=0;i<=s;i+=s/10){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,s);ctx.stroke();}
        } else if (type === 'roof_window' || type === 'dormer_window') {
            ctx.fillStyle = type === 'roof_window' ? '#94a3b8' : '#ffffff'; 
            ctx.fillRect(0, 0, s, s);
            ctx.fillStyle = '#38bdf8'; 
            ctx.fillRect(20, 20, s-40, s-40);
            ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
            ctx.beginPath(); ctx.moveTo(20, 20); ctx.lineTo(s-20, 20); ctx.lineTo(20, s-20); ctx.fill();
        } else {
            ctx.fillStyle = color; ctx.fillRect(0,0,s,s);
        }

        if(type === 'tile') {
            const w=128, h=64;
            for(let y=0; y<s; y+=h){
                for(let x=0; x<s; x+=w){
                    const off = (y/h)%2===0?0:w/2;
                    ctx.fillStyle='rgba(0,0,0,0.3)'; ctx.fillRect((x+off)%s,y+h-4,w,4);
                    ctx.fillStyle='rgba(255,255,255,0.1)'; ctx.fillRect((x+off)%s,y,2,h);
                }
            }
        }
        
        const t = new THREE.CanvasTexture(c); 
        t.wrapS = t.wrapT = THREE.RepeatWrapping;
        return t;
    }
};

// ==========================================
// 3. ROOF ENGINE CLASS
// ==========================================
class RoofEngine {
    scene: THREE.Scene; camera: THREE.PerspectiveCamera; renderer: THREE.WebGLRenderer; controls: OrbitControls;
    raycaster = new THREE.Raycaster(); 
    
    root = new THREE.Group(); 
    gStructure = new THREE.Group(); 
    gRafters = new THREE.Group();
    gMembrane = new THREE.Group();
    gCounterBattens = new THREE.Group();
    gInsulation = new THREE.Group();
    gBattens = new THREE.Group();
    gVisualTiles = new THREE.Group(); 
    gRoof = new THREE.Group();
    gObstacles = new THREE.Group();
    gLinien = new THREE.Group(); // Eingabeaufforderung 12: linienförmige Dachbauteile (Schneefang)
    gOeffnungen = new THREE.Group(); // Eingabeaufforderung 15: Dachöffnungen / Ausschnitte (Prüffeld)
    gGaubenstuhl = new THREE.Group(); // Eingabeaufforderung 15: Gaubendachstuhl (schematisch)
    gGaubenHaut = new THREE.Group(); // Eingabeaufforderung 16: Gaubendach (eigene Ebene, trennbar vom Körper)
    gWechsel = new THREE.Group(); // Eingabeaufforderung 16: Wechselhölzer/Auswechslung (eigene Ebene)
    gAnschluss = new THREE.Group(); // Eingabeaufforderung 17: Gaubenanschlüsse / Dachanschlusslinien (eigene Ebene)
    gVerschneidung = new THREE.Group(); // Eingabeaufforderung 26: echte Kehl-/Gratlinien der L/T-Dachverschneidung
    // Eingabeaufforderung 19: echte Dachhaut-Löcher (nur sichere Rechteckflächen). Pro Fläche eine
    // Rebuild-Closure (baut die Ziegel-Plane mit/ohne Löchern) + die aktuell gezeichnete Plane.
    dachhautRebuild: Map<string, (loecher: { uMin: number; uMax: number; vMin: number; vMax: number }[]) => THREE.Mesh> = new Map();
    dachhautMeshes: Map<string, THREE.Mesh> = new Map();
    echteLochIds: Set<string> = new Set(); // Öffnungen, die als echtes Dachhaut-Loch (nicht Prüffeld) dargestellt sind
    gSolar = new THREE.Group();
    gMountingRails = new THREE.Group();
    gMountingHooks = new THREE.Group();
    gMountingClamps = new THREE.Group();
    gHighlight = new THREE.Group();
    selectionHighlight: any = null; // Reparatur 4: Overlay-Mesh der aktiven Dachflächen-Auswahl

    mats: Record<string, THREE.Material> = {};
    surfaces: Map<string, { mesh: THREE.Mesh, origin: THREE.Vector3, vRight: THREE.Vector3, vDown: THREE.Vector3, vNormal: THREE.Vector3, width: number, height: number, type: string, polygon?: THREE.Vector2[], numRafters?: number }> = new Map();
    currentObstacles: ObstacleData[] = [];
    modules: Map<string, { mesh: THREE.Mesh, data: ModuleData }> = new Map();
    holzliste: any[] = [];
    
    onObstacleMove?: (id: string, surfaceId: string, x: number, y: number) => void;
    onObstacleRotate?: (id: string) => void;
    onObstacleDelete?: (id: string) => void;
    onSurfaceSelect?: (surfaceId: string) => void;
    onAbbundSelect?: (data: any) => void;
    onModuleUpdate?: (modules: ModuleData[]) => void;
    onSurfacesUpdated?: (surfaces: {id: string, name: string, width?: number, height?: number, type?: string, area?: number}[]) => void;

    draggedObj: THREE.Object3D | null = null;
    dragPlane = new THREE.Plane();
    activeTool: InteractionTool = 'select';
    obsHelper: THREE.BoxHelper;
    targetZoom: number = 1;
    manualZoom: number = 1;

    currentCover: RoofCovering = 'ziegel';
    private animationFrameId: number = 0;
    private boundOnPointerDown: (e: PointerEvent) => void;
    private boundOnPointerMove: (e: PointerEvent) => void;
    private boundOnPointerUp: (e: PointerEvent) => void;

    currentActiveSurfaceId: string | null = null;
    lockedSurfaces: Set<string> = new Set();

    constructor(canvas: HTMLCanvasElement) {
        this.scene = new THREE.Scene(); 
        this.scene.background = new THREE.Color(0xdce7f0);
        let initialAspect = canvas.clientWidth / canvas.clientHeight || 1;

        this.camera = new THREE.PerspectiveCamera(45, initialAspect, 0.1, 1000);
        this.camera.position.set(20, 25, 30);

        this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: "high-performance" });
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        
        this.controls = new OrbitControls(this.camera, canvas);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.maxPolarAngle = Math.PI / 2 - 0.05;

        const amb = new THREE.AmbientLight(0xffffff, 0.7); 
        const sun = new THREE.DirectionalLight(0xffffff, 1.2); 
        sun.position.set(30, 60, 20); 
        sun.castShadow = true;
        this.scene.add(amb, sun);

        this.scene.add(this.root); 
        this.root.add(
            this.gStructure, this.gRafters, this.gMembrane, 
            this.gCounterBattens, this.gInsulation, this.gBattens, 
            this.gVisualTiles, this.gRoof, this.gObstacles, this.gGaubenHaut, this.gAnschluss, this.gVerschneidung, this.gLinien, this.gOeffnungen, this.gGaubenstuhl, this.gWechsel,
            this.gSolar, this.gMountingRails, this.gMountingHooks, this.gMountingClamps,
            this.gHighlight
        );
        this.scene.add(new THREE.GridHelper(60, 60, 0xbdc3c7, 0xffffff));

        this.initMaterials();
        this.obsHelper = new THREE.BoxHelper(new THREE.Mesh(), 0xffaa00);
        this.obsHelper.visible = false;
        this.gHighlight.add(this.obsHelper);
        
        this.boundOnPointerDown = this.onPointerDown.bind(this);
        this.boundOnPointerMove = this.onPointerMove.bind(this);
        this.boundOnPointerUp = this.onPointerUp.bind(this);

        canvas.addEventListener('pointerdown', this.boundOnPointerDown);
        canvas.addEventListener('pointermove', this.boundOnPointerMove);
        canvas.addEventListener('pointerup', this.boundOnPointerUp);
        
        this.animate();
    }

    destroy() {
        cancelAnimationFrame(this.animationFrameId);
        if (this.renderer && this.renderer.domElement) {
            const canvas = this.renderer.domElement;
            canvas.removeEventListener('pointerdown', this.boundOnPointerDown);
            canvas.removeEventListener('pointermove', this.boundOnPointerMove);
            canvas.removeEventListener('pointerup', this.boundOnPointerUp);
        }
        disposeHierarchy(this.scene);
        this.renderer.dispose();
    }

    getNormalizedPointer(e: PointerEvent) {
        const rect = this.renderer.domElement.getBoundingClientRect();
        return {
            x: ((e.clientX - rect.left) / rect.width) * 2 - 1,
            y: -((e.clientY - rect.top) / rect.height) * 2 + 1
        };
    }

    onPointerDown(e: PointerEvent) {
        const p = this.getNormalizedPointer(e);
        this.raycaster.setFromCamera(new THREE.Vector2(p.x, p.y), this.camera);

        if (this.activeTool === 'abbund') {
            const allHolz = [...this.gRafters.children, ...this.gCounterBattens.children, ...this.gBattens.children];
            const hits = this.raycaster.intersectObjects(allHolz, true);
            if (hits.length > 0 && this.onAbbundSelect) {
                let obj = hits[0].object;
                if(obj.userData?.isHolz) this.onAbbundSelect(obj.userData);
            }
            return;
        }

        if (this.activeTool === 'select') {
            // Reparatur 4: Markieren-Werkzeug. Klick wählt eine Dachfläche aus
            // (Highlight + rechte Sidebar). Klick auf ein Modul wählt dessen Dachfläche
            // (kein Leerklick). Kamera-Drehen/Zoomen (OrbitControls) bleibt unberührt;
            // bei Klick ins Leere bleibt die bisherige Auswahl bestehen.
            let chosenSurf: string | null = null;
            const modHits = this.raycaster.intersectObjects(this.gSolar.children, true);
            if (modHits.length > 0) {
                const modId = modHits[0].object.userData?.id || modHits[0].object.userData?.moduleId;
                const m = modId ? this.modules.get(modId) : null;
                chosenSurf = m?.data?.surfaceId || null;
            }
            if (!chosenSurf) {
                const roofHits = this.raycaster.intersectObjects(this.gRoof.children, false);
                if (roofHits.length > 0) chosenSurf = roofHits[0].object.userData?.surfaceId || null;
            }
            if (chosenSurf && this.onSurfaceSelect) this.onSurfaceSelect(chosenSurf);
            return;
        }

        if (this.activeTool === 'delete' || this.activeTool === 'rotate') {
            const modHits = this.raycaster.intersectObjects(this.gSolar.children, true);
            if (modHits.length > 0 && this.activeTool === 'delete') {
                const mesh = modHits[0].object as THREE.Mesh;
                if (mesh.userData.id) {
                    this.removeModule(mesh.userData.id);
                }
                return;
            }

            const obsHits = this.raycaster.intersectObjects(this.gObstacles.children, true);
            if (obsHits.length > 0) {
                let obj = obsHits[0].object;
                while(obj.parent && obj.parent !== this.gObstacles) obj = obj.parent;
                if(obj.userData?.id) {
                    if(this.activeTool === 'delete' && this.onObstacleDelete) this.onObstacleDelete(obj.userData.id);
                    else if (this.activeTool === 'rotate' && this.onObstacleRotate) this.onObstacleRotate(obj.userData.id);
                }
            }
            return;
        }

        if (this.activeTool === 'move') {
            const intersects = this.raycaster.intersectObjects(this.gObstacles.children, true);
            if (intersects.length > 0) {
                let obj = intersects[0].object;
                while(obj.parent && obj.parent !== this.gObstacles) obj = obj.parent;
                if(obj.userData?.id) {
                    this.draggedObj = obj;
                    this.controls.enabled = false;
                    const obsData = this.currentObstacles.find(o => o.id === obj.userData.id);
                    if (obsData) {
                        const surf = this.surfaces.get(obsData.surfaceId);
                        if (surf) this.dragPlane.setFromNormalAndCoplanarPoint(surf.vNormal, surf.origin);
                    }
                }
            }
        }
    }

    onPointerMove(e: PointerEvent) {
        if(!this.draggedObj) return;
        const p = this.getNormalizedPointer(e);
        this.raycaster.setFromCamera(new THREE.Vector2(p.x, p.y), this.camera);
        
        const pt = new THREE.Vector3();
        if (this.raycaster.ray.intersectPlane(this.dragPlane, pt)) {
            const roofIntersects = this.raycaster.intersectObjects(this.gRoof.children, false);
            let targetSurfId = null;
            if(roofIntersects.length > 0) targetSurfId = roofIntersects[0].object.userData.surfaceId;
            
            const obsData = this.currentObstacles.find(o => o.id === this.draggedObj!.userData.id);
            const surfIdToUse = targetSurfId || obsData?.surfaceId;
            if (!surfIdToUse) return;

            const surf = this.surfaces.get(surfIdToUse);
            if(!surf) return;

            this.dragPlane.setFromNormalAndCoplanarPoint(surf.vNormal, surf.origin);
            if (!this.raycaster.ray.intersectPlane(this.dragPlane, pt)) return;

            const localPt = pt.clone().sub(surf.origin);
            let u = localPt.dot(surf.vRight) / surf.width;
            let v = localPt.dot(surf.vDown) / surf.height;
            u = Math.max(0.05, Math.min(0.95, u));
            v = Math.max(0.05, Math.min(0.95, v));

            if(this.onObstacleMove) this.onObstacleMove(this.draggedObj.userData.id, surfIdToUse, u, v);
        }
    }

    onPointerUp(e: PointerEvent) {
        this.draggedObj = null;
        this.controls.enabled = true;
    }

    getRoofMat() {
        // Eindeckung -> Optik. Flachdach-Abdichtungen bekommen bewusst KEINE
        // Ziegeltextur: Bitumen-/Kunststoffbahn = dunkle Dichtungsbahn (membrane),
        // Gruendach = generisches Gruen, Kies = Schuettung (gravel). Generische
        // Optik, keine Hersteller-/Produktdaten.
        switch (this.currentCover) {
            case 'ziegel':      return this.mats.tileRed;
            case 'schiefer':    return this.mats.tileDark;
            case 'trapezblech': return this.mats.metal;
            case 'bitumen':
            case 'kunststoff':  return this.mats.membrane;
            case 'gruendach':   return this.mats.greenRoof || this.mats.membrane;
            case 'kies':        return this.mats.gravel;
            default:            return this.mats.gravel;
        }
    }

    /** Tint die geteilte Ziegel-Optik (mats.tileRed) auf einen gewählten Farb-Hex,
     *  oder null = zurück auf die Basisfarbe. Repliziert die Init-Formel (color+map gleich). */
    applyTileColor(hex) {
        const c = hex || '#a14332';
        const m = this.mats.tileRed;
        if (!m) return;
        if (m.map) m.map.dispose();
        m.map = TextureGen.create(c, 'tile');
        m.color.set(c);
        m.needsUpdate = true;
    }

    /** Tint die Metall-/Schiefer-Optik auf einen gewählten Farb-Hex (oder Basis).
     *  Trapezblech -> mats.metal, Schiefer -> mats.tileDark; andere Cover unberührt. */
    applyCoveringColor(hex) {
        let m, base, type;
        if (this.currentCover === 'trapezblech') { m = this.mats.metal; base = '#aaaaaa'; type = 'metal'; }
        else if (this.currentCover === 'schiefer') { m = this.mats.tileDark; base = '#334155'; type = 'tile'; }
        else return;
        if (!m) return;
        const c = hex || base;
        if (m.map) m.map.dispose();
        m.map = TextureGen.create(c, type);
        m.color.set(c);
        m.needsUpdate = true;
    }

    zoomIn() { this.manualZoom *= 1.2; this.updateZoom(); }
    zoomOut() { this.manualZoom /= 1.2; this.updateZoom(); }
    resetZoom() {
        this.manualZoom = 1;
        this.updateZoom();
    }

    updateZoom() {
        let currentAspect = this.camera.aspect || 1;
        let aspectZoom = currentAspect > 1.0 ? Math.pow(currentAspect, 0.85) : currentAspect;
        this.targetZoom = aspectZoom * this.manualZoom;
        if (isNaN(this.targetZoom)) this.targetZoom = 1;
    }

    initMaterials() {
        this.mats.wall = new THREE.MeshStandardMaterial({ color: '#e2e8f0', map: TextureGen.create('#e2e8f0', 'noise') });
        this.mats.tileRed = new THREE.MeshStandardMaterial({ color: '#a14332', map: TextureGen.create('#a14332', 'tile'), roughness: 0.7 });
        this.mats.tileDark = new THREE.MeshStandardMaterial({ color: '#334155', map: TextureGen.create('#334155', 'tile'), roughness: 0.7 });
        this.mats.gravel = new THREE.MeshStandardMaterial({ color: '#888888', roughness: 1 });
        this.mats.greenRoof = new THREE.MeshStandardMaterial({ color: '#4d7c3a', roughness: 1 });
        this.mats.chimney = new THREE.MeshStandardMaterial({ color: '#884444', map: TextureGen.create('#884444', 'noise') });
        this.mats.metal = new THREE.MeshStandardMaterial({ color: '#aaaaaa', map: TextureGen.create('#aaaaaa', 'metal'), metalness: 0.7, roughness: 0.3 });
        this.mats.glass = new THREE.MeshStandardMaterial({ color: '#aaddff', transparent: true, opacity: 0.6 });
        this.mats.wood = new THREE.MeshStandardMaterial({ color: '#dcb28b', map: TextureGen.create('#dcb28b', 'wood'), roughness: 0.8 });
        this.mats.woodCut = new THREE.MeshStandardMaterial({ color: '#e6b88a', roughness: 0.9 });
        this.mats.insulation = new THREE.MeshStandardMaterial({ color: '#facc15', map: TextureGen.create('#facc15', 'iso'), roughness: 1 });
        this.mats.membrane = new THREE.MeshStandardMaterial({ color: '#1e293b', map: TextureGen.create('#1e293b', 'membrane'), roughness: 0.6, side: THREE.DoubleSide });
        this.mats.batten = new THREE.MeshStandardMaterial({ color: '#8d6e63', roughness: 0.9 });
        this.mats.invisible = new THREE.MeshBasicMaterial({ transparent: true, opacity: 0, depthWrite: false });
        // Eingabeaufforderung 15: wiederverwendbare Materialien (kein Leak) für Öffnungs-Prüffeld +
        // Gaubendachstuhl. Deckungsneutral (kein Produkt/Eindeckung), nur schematische Konstruktion.
        this.mats.oeffnung = new THREE.MeshBasicMaterial({ color: '#3b82f6', transparent: true, opacity: 0.18, side: THREE.DoubleSide, depthWrite: false });
        this.mats.oeffnungWarn = new THREE.MeshBasicMaterial({ color: '#f59e0b', transparent: true, opacity: 0.24, side: THREE.DoubleSide, depthWrite: false });
        this.mats.oeffnungRand = new THREE.LineBasicMaterial({ color: '#1e3a8a' });
        // Eingabeaufforderung 18: echter (sicherer) Rechteck-Ausschnitt = dunkle, eingelassene
        // Öffnungsfläche („optisch als Loch") unter der Dachhaut + Laibung. Deckungsneutral.
        this.mats.ausschnitt = new THREE.MeshStandardMaterial({ color: '#14181f', roughness: 1, side: THREE.DoubleSide });
        this.mats.ausschnittLaibung = new THREE.MeshStandardMaterial({ color: '#2b3340', roughness: 1, side: THREE.DoubleSide });
        this.mats.ausschnittRand = new THREE.LineBasicMaterial({ color: '#0f172a' });
        this.mats.gaubenStuhl = new THREE.MeshStandardMaterial({ color: '#c79a6b', roughness: 0.85 });
        // Eingabeaufforderung 17: dedizierte, DOPPELSEITIGE Materialien für die explizit konstruierte
        // Gaubengeometrie (kein Mutieren der geteilten wall/Roof-Materialien) + Anschlusslinien-Material.
        this.mats.gaubeKoerper = new THREE.MeshStandardMaterial({ color: '#e2e8f0', roughness: 0.9, side: THREE.DoubleSide });
        this.mats.gaubeDach = new THREE.MeshStandardMaterial({ color: '#8d3b2f', roughness: 0.85, side: THREE.DoubleSide });
        this.mats.anschlussLinie = new THREE.LineBasicMaterial({ color: '#0ea5e9' });
        // Eingabeaufforderung 24: ECHTE Kehl-Schnittlinie (berechnete Flächenverschneidung) — eigene
        // Farbe zur ehrlichen Unterscheidung von schematischen Anschlusslinien.
        this.mats.echteKehle = new THREE.LineBasicMaterial({ color: '#16a34a' });
        // Eingabeaufforderung 26: ECHTER GRAT (Hip) der L-Verschneidung — eigene Farbe (rot) zur
        // Unterscheidung von der Kehle (grün).
        this.mats.echterGrat = new THREE.LineBasicMaterial({ color: '#dc2626' });

        this.mats.solar = new THREE.MeshStandardMaterial({ color: '#ffffff', map: TextureGen.create('#000000', 'solar'), roughness: 0.2, metalness: 0.8 });
        this.mats.aluminium = new THREE.MeshStandardMaterial({ color: '#f8fafc', metalness: 0.6, roughness: 0.2 });
        this.mats.steel = new THREE.MeshStandardMaterial({ color: '#94a3b8', metalness: 0.5, roughness: 0.4 });
        this.mats.clampBlack = new THREE.MeshStandardMaterial({ color: '#171717', metalness: 0.5, roughness: 0.5 });

        this.mats.highlight = new THREE.MeshBasicMaterial({ color: '#3b82f6', transparent: true, opacity: 0.4, side: THREE.DoubleSide, depthWrite: false });
        this.mats.frameColor = new THREE.MeshStandardMaterial({ color: '#94a3b8', roughness: 0.8 });
        this.mats.roofWindow = new THREE.MeshStandardMaterial({ color: '#ffffff', map: TextureGen.create('#ffffff', 'roof_window'), roughness: 0.1, metalness: 0.5 });
        this.mats.dormerWindow = new THREE.MeshStandardMaterial({ color: '#ffffff', map: TextureGen.create('#ffffff', 'dormer_window'), roughness: 0.1, metalness: 0.5 });
    }

    drawBeamBetweenPoints(p1: THREE.Vector3, p2: THREE.Vector3, w: number, h: number, mat: THREE.Material, group: THREE.Group, name: string = 'Holz', type: string = 'sparren') {
        const len = Math.max(0.01, p1.distanceTo(p2));
        const center = p1.clone().lerp(p2, 0.5);
        const geo = new THREE.BoxGeometry(w, h, len);
        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.copy(center);
        mesh.lookAt(p2);
        mesh.castShadow = true;

        const vol = w * h * len;
        mesh.userData = { name, type, breite: w, hoehe: h, laenge: len, isHolz: true, volEinzel: vol };
        this.holzliste.push({...mesh.userData, volGesamt: vol, anzahl: 1});

        group.add(mesh);
    }

    createBeam(w: number, h: number, l: number, pos: THREE.Vector3, rot: THREE.Euler, mat: THREE.Material = this.mats.wood, name: string = 'Holz', type: string = 'pfette') {
        l = Math.max(0.01, l); w = Math.max(0.01, w); h = Math.max(0.01, h);
        const geo = new THREE.BoxGeometry(w, h, l);
        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.copy(pos);
        mesh.rotation.copy(rot);
        mesh.castShadow = true;

        const vol = w * h * l;
        mesh.userData = { name, type, breite: w, hoehe: h, laenge: l, isHolz: true, volEinzel: vol };
        this.holzliste.push({...mesh.userData, volGesamt: vol, anzahl: 1});

        return mesh;
    }

    getLineIntersections(val: number, poly: THREE.Vector2[], isUAxis: boolean): number[] {
        let hits = [];
        for (let i = 0; i < poly.length; i++) {
            let p1 = poly[i], p2 = poly[(i + 1) % poly.length];
            let c1 = isUAxis ? p1.x : p1.y, c2 = isUAxis ? p2.x : p2.y;
            let o1 = isUAxis ? p1.y : p1.x, o2 = isUAxis ? p2.y : p2.x;
            
            if (c1 === c2) continue; 
            if ((c1 <= val && val <= c2) || (c2 <= val && val <= c1)) {
                let t = (val - c1) / (c2 - c1);
                hits.push(o1 + t * (o2 - o1));
            }
        }
        hits.sort((a,b) => a - b);
        let uniqueHits = [];
        for (let i = 0; i < hits.length; i++) {
            if (i === 0 || Math.abs(hits[i] - hits[i-1]) > 0.001) uniqueHits.push(hits[i]);
        }
        return uniqueHits; 
    }

    cleanPolygon(poly: THREE.Vector2[]): THREE.Vector2[] {
        if(poly.length < 3) return poly;
        let clean = [];
        for(let pt of poly) {
            if(clean.length === 0 || clean[clean.length-1].distanceTo(pt) > 0.005) {
                clean.push(pt);
            }
        }
        if (clean.length > 2 && clean[0].distanceTo(clean[clean.length-1]) < 0.005) {
            clean.pop();
        }
        return clean;
    }

    isPointInsidePolygon(point: THREE.Vector2, polygon: THREE.Vector2[]) {
        let inside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const xi = polygon[i].x, yi = polygon[i].y;
            const xj = polygon[j].x, yj = polygon[j].y;
            const intersect = ((yi > point.y) !== (yj > point.y)) &&
                (point.x < (xj - xi) * (point.y - yi) / ((yj - yi) || 0.000001) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    addSurface(id: string, mesh: THREE.Mesh, origin: THREE.Vector3, vRight: THREE.Vector3, vDown: THREE.Vector3, vNormal: THREE.Vector3, width: number, height: number, type: string, polygon?: THREE.Vector2[]) {
        mesh.userData = { surfaceId: id };
        this.surfaces.set(id, {
            mesh,
            origin: origin.clone(),
            vRight: vRight.clone().normalize(),
            vDown: vDown.clone().normalize(),
            vNormal: vNormal.clone().normalize(),
            width,
            height,
            type,
            polygon,
        });
    }

    // Reparatur 4: dezentes Auswahl-Highlight für die aktive Dachfläche. Nutzt das
    // vorhandene Highlight-Material (mats.highlight) + die gHighlight-Gruppe; klont
    // den Flächen-Umriss (surf.mesh-Geometrie) leicht über der Fläche. Verändert die
    // Dachgeometrie/Materialien NICHT (eigenes Overlay-Mesh, geteilte Geometrie).
    highlightSurface(surfId: string | null) {
        if (this.selectionHighlight) {
            this.gHighlight.remove(this.selectionHighlight);
            this.selectionHighlight = null;
        }
        if (!surfId) return;
        const surf = this.surfaces.get(surfId);
        if (!surf || !surf.mesh || !surf.mesh.geometry) return;
        const hl = new THREE.Mesh(surf.mesh.geometry, this.mats.highlight);
        hl.position.copy(surf.mesh.position).addScaledVector(surf.vNormal, 0.03);
        hl.quaternion.copy(surf.mesh.quaternion);
        hl.renderOrder = 999;
        this.selectionHighlight = hl;
        this.gHighlight.add(hl);
    }

    surfacePoint(surf: any, x: number, y: number, normalOffset = 0) {
        return surf.origin.clone()
            .addScaledVector(surf.vRight, x * surf.width)
            .addScaledVector(surf.vDown, y * surf.height)
            .addScaledVector(surf.vNormal, normalOffset);
    }

    surfaceQuaternion(surf: any) {
        // Lokales X = Dachflächenbreite, lokales Y = Aufbauhöhe senkrecht zur
        // Dachfläche. Lokales Z zeigt gegen die gespeicherte V-Achse, damit die
        // Matrix rechtshändig bleibt; die Bauteile liegen trotzdem exakt im
        // selben Flächenkoordinatensystem wie Dachhaut, Raycast und Belegung.
        const xAxis = surf.vRight.clone().normalize();
        const yAxis = surf.vNormal.clone().normalize();
        const zAxis = surf.vDown.clone().normalize().negate();
        return new THREE.Quaternion().setFromRotationMatrix(new THREE.Matrix4().makeBasis(xAxis, yAxis, zAxis));
    }

    // Korrektur „Schweben": LOTRECHTE Orientierung für aufrecht stehende Aufbauten (Gaube/Kamin).
    // Lokales Y = Welt-Hoch (der Aufbau steht senkrecht, kippt NICHT mit der Dachneigung weg),
    // lokales Z (Front/Fenster) = horizontale Falllinie zur Traufe, lokales X = Y × Z (parallel Traufe).
    // Math in reiner, getesteter Util (aufbauOrientierung); hier nur Übertragung in eine THREE-Quaternion.
    verticalSurfaceQuaternion(surf: any) {
        const b = stehendeAufbauBasis({ x: surf.vDown.x, y: surf.vDown.y, z: surf.vDown.z });
        const xAxis = new THREE.Vector3(b.xAxis.x, b.xAxis.y, b.xAxis.z);
        const yAxis = new THREE.Vector3(b.yAxis.x, b.yAxis.y, b.yAxis.z);
        const zAxis = new THREE.Vector3(b.zAxis.x, b.zAxis.y, b.zAxis.z);
        return new THREE.Quaternion().setFromRotationMatrix(new THREE.Matrix4().makeBasis(xAxis, yAxis, zAxis));
    }

    // Eingabeaufforderung 17: reines Frame-Abbild von `surf` für die getestete Geometrie-Util.
    frameAusSurf(surf: any): GaubeSurfaceFrame {
        return {
            origin: { x: surf.origin.x, y: surf.origin.y, z: surf.origin.z },
            vRight: { x: surf.vRight.x, y: surf.vRight.y, z: surf.vRight.z },
            vDown: { x: surf.vDown.x, y: surf.vDown.y, z: surf.vDown.z },
            vNormal: { x: surf.vNormal.x, y: surf.vNormal.y, z: surf.vNormal.z },
            width: surf.width, height: surf.height,
        };
    }

    // Dreieckssuppe (lokale Aufbau-Koordinaten lx/ly/lz) -> BufferGeometry (mesh wird per grp.quaternion gedreht).
    trisToGeometry(tris: GaubeDreieck[]): THREE.BufferGeometry {
        const pos: number[] = [];
        for (const t of tris) for (const p of t) pos.push(p.lx, p.ly, p.lz);
        const g = new THREE.BufferGeometry();
        g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        g.computeVertexNormals();
        return g;
    }

    // Anschlusslinien (lokale Koordinaten) als THREE.Line-Objekte (für die Ebene gAnschluss).
    linienZuObjekten(linien: GaubeLinie[], mat: THREE.Material): THREE.Line[] {
        return linien.map((l) => {
            const geo = new THREE.BufferGeometry().setFromPoints(l.map((p) => new THREE.Vector3(p.lx, p.ly, p.lz)));
            return new THREE.Line(geo, mat);
        });
    }

    // Ridge-/Firsthöhe der Fläche an einer u-Position (oberer Flächenrand v=1) — für „kein Vertex über First".
    flaecheFirstHoehe(surf: any, xRel: number): number {
        return this.surfacePoint(surf, xRel, 1.0, 0).y;
    }

    moduleRectCorners(cxMeters: number, cyMeters: number, mW: number, mH: number) {
        return [
            new THREE.Vector2(cxMeters - mW/2, cyMeters - mH/2),
            new THREE.Vector2(cxMeters + mW/2, cyMeters - mH/2),
            new THREE.Vector2(cxMeters + mW/2, cyMeters + mH/2),
            new THREE.Vector2(cxMeters - mW/2, cyMeters + mH/2),
        ];
    }

    moduleFitsSurface(surf: any, cxMeters: number, cyMeters: number, mW: number, mH: number, margin: number) {
        if (cxMeters - mW/2 < margin || cxMeters + mW/2 > surf.width - margin) return false;
        if (cyMeters - mH/2 < margin || cyMeters + mH/2 > surf.height - margin) return false;

        const polygon = surf.polygon && surf.polygon.length > 2 ? surf.polygon : null;
        if (!polygon) return true;

        return this.moduleRectCorners(cxMeters, cyMeters, mW, mH)
            .every(corner => this.isPointInsidePolygon(corner, polygon));
    }

    moduleOverlapsObstacle(surf: any, surfId: string, obstacles: ObstacleData[], cxMeters: number, cyMeters: number, mW: number, mH: number) {
        const buffer = 0.1;
        return obstacles.some(o => {
            if (o.surfaceId !== surfId) return false;
            const ox = o.x * surf.width;
            const oy = o.y * surf.height;
            let obsW = o.width;
            let obsH = o.depth || o.height;

            const rot = ((o.rotation || 0) % (Math.PI * 2) + Math.PI * 2) % (Math.PI * 2);
            const isRotated90 = Math.abs(rot - Math.PI/2) < 0.1 || Math.abs(rot - 3*Math.PI/2) < 0.1;
            if (isRotated90) {
                obsW = o.depth || o.height;
                obsH = o.width;
            }

            return (cxMeters - mW/2 < ox + obsW/2 + buffer) &&
                (cxMeters + mW/2 > ox - obsW/2 - buffer) &&
                (cyMeters - mH/2 < oy + obsH/2 + buffer) &&
                (cyMeters + mH/2 > oy - obsH/2 - buffer);
        });
    }

    buildRoofFace(
        id: string, p: BuildingParams, origin: THREE.Vector3, uDir: THREE.Vector3, vDir: THREE.Vector3, normal: THREE.Vector3,
        uMax: number, vMax: number, rawPolygon: THREE.Vector2[], roofMat: THREE.Material
    ) {
        const spread = p.layerSpread || 0;
        // Phase-0-Hotfix: Maße gegen 0/leer/NaN klemmen. Ohne Klemmung ergab
        // rafterDist/battenDist = 0 (Feld leer/0) einen Rasterschritt 0 und damit
        // eine Endlosschleife (Tab-Freeze); NaN-Breiten/-Höhen korrumpierten die
        // Geometrie. (v||0) faengt NaN ab, Math.max sichert ein Mindestmaß.
        // Reparatur 1: dieselbe geklemmte Quelle wie die Stück-/Holzliste (eine
        // Wahrheit) — dachstuhlMasseM() entspricht exakt Math.max(floor,(v||0)/100).
        const masseM = dachstuhlMasseM(p);
        const dim = {
            rafterW: masseM.rafterW, rafterH: masseM.rafterH, rafterDist: masseM.rafterDist,
            counterW: 0.03, counterH: 0.05,
            battenW: 0.03, battenH: 0.04, battenDist: masseM.battenDist,
            insulationH: 0.14, tileH: 0.05
        };
        
        const kerve = dim.rafterH * 0.25;
        const hRafter = -dim.rafterH/2 + kerve; 
        const hMembrane = hRafter + dim.rafterH/2 + spread*0.1;
        const hCounter = hMembrane + dim.counterH/2 + spread*0.2;
        const hInsulation = hMembrane + dim.insulationH/2 + spread*0.5;
        const hBatten = hMembrane + dim.counterH + dim.battenH/2 + spread*0.2;
        const hTile = hBatten + dim.battenH/2 + dim.tileH/2 + spread*0.5;

        const crossNormal = new THREE.Vector3().crossVectors(uDir, vDir).normalize();
        const mBase = new THREE.Matrix4().makeBasis(uDir, vDir, crossNormal);
        const quat = new THREE.Quaternion().setFromRotationMatrix(mBase);

        const polygon = this.cleanPolygon(rawPolygon);
        if(polygon.length < 3) return;

        const shape = new THREE.Shape();
        shape.moveTo(polygon[0].x, polygon[0].y);
        for(let i=1; i<polygon.length; i++) shape.lineTo(polygon[i].x, polygon[i].y);
        shape.lineTo(polygon[0].x, polygon[0].y);

        const createPlane = (h: number, thickness: number, mat: THREE.Material, group: THREE.Group) => {
            try {
                const geo = new THREE.ExtrudeGeometry(shape, { depth: thickness, bevelEnabled: false });
                const mesh = new THREE.Mesh(geo, mat);
                mesh.position.copy(origin).addScaledVector(crossNormal, h - thickness);
                mesh.quaternion.copy(quat);
                mesh.castShadow = true;
                group.add(mesh);
                return mesh;
            } catch(e) { return null; }
        };

        createPlane(hMembrane, 0.005, this.mats.membrane, this.gMembrane);
        createPlane(hInsulation, dim.insulationH, this.mats.insulation, this.gInsulation);
        // E19: sichtbare Dachhaut (Ziegel) über eine Rebuild-Closure — kann später (in updateObstacles)
        // mit echten Löchern (THREE.Shape.holes) für sichere Rechteckflächen neu gebaut werden. Membran/
        // Dämmung oben bleiben IMMER voll (ohne Loch). Loch betrifft NUR die Dachhaut.
        const baueDachhaut = (loecher: { uMin: number; uMax: number; vMin: number; vMax: number; poly?: { x: number; y: number }[] }[]) => {
            const s = new THREE.Shape();
            s.moveTo(polygon[0].x, polygon[0].y);
            for (let i = 1; i < polygon.length; i++) s.lineTo(polygon[i].x, polygon[i].y);
            s.lineTo(polygon[0].x, polygon[0].y);
            for (const L of (loecher || [])) {
                const hp = new THREE.Path();
                if (L.poly && L.poly.length >= 3) { // E25: realer Gauben-Fußabdruck als Polygon-Loch
                    hp.moveTo(L.poly[0].x, L.poly[0].y);
                    for (let i = 1; i < L.poly.length; i++) hp.lineTo(L.poly[i].x, L.poly[i].y);
                    hp.lineTo(L.poly[0].x, L.poly[0].y);
                } else { // Rechteck-Loch (Dachfenster/Kamin/Lüfter/Lichtkuppel)
                    hp.moveTo(L.uMin, L.vMin); hp.lineTo(L.uMax, L.vMin); hp.lineTo(L.uMax, L.vMax); hp.lineTo(L.uMin, L.vMax); hp.lineTo(L.uMin, L.vMin);
                }
                s.holes.push(hp);
            }
            const geo = new THREE.ExtrudeGeometry(s, { depth: dim.tileH, bevelEnabled: false });
            const m = new THREE.Mesh(geo, roofMat);
            m.position.copy(origin).addScaledVector(crossNormal, hTile - dim.tileH);
            m.quaternion.copy(quat); m.castShadow = true;
            return m;
        };
        this.dachhautRebuild.set(id, baueDachhaut);
        const dachhaut0 = baueDachhaut([]);
        this.gVisualTiles.add(dachhaut0);
        this.dachhautMeshes.set(id, dachhaut0);

        const neigung = Math.acos(Math.max(-1, Math.min(1, normal.y)));

        const placeBeam = (isVertical: boolean, posOrthogonal: number, start: number, end: number, w: number, h: number, mat: THREE.Material, group: THREE.Group, hOffset: number, name: string, schifterArt?: string) => {
            const length = end - start;
            if(length < 0.02) return;
            const geo = new THREE.BoxGeometry(isVertical ? w : length, isVertical ? length : w, h);
            const mesh = new THREE.Mesh(geo, [mat, mat, mat, mat, this.mats.woodCut, this.mats.woodCut]);
            const uCenter = isVertical ? posOrthogonal : start + length/2;
            const vCenter = isVertical ? start + length/2 : posOrthogonal;
            mesh.position.copy(origin).addScaledVector(uDir, uCenter).addScaledVector(vDir, vCenter).addScaledVector(crossNormal, hOffset);
            mesh.quaternion.copy(quat);
            mesh.castShadow = true;

            const bType = isVertical ? 'sparren' : 'latte';
            const vol = w * h * length;
            mesh.userData = {
                name, type: bType, breite: w, hoehe: h, laenge: length, isHolz: true, volEinzel: vol,
                neigung: isVertical ? neigung : 0,
                kervenAbstand: isVertical ? Math.max(0, start + p.overhang) : 0,
                kervenTiefe: isVertical ? kerve : 0,
                // EA28: Schiftsparren-Klassifikation (nur Gemeinsparren; type bleibt 'sparren' → keine
                // Doppelzählung in holzMengen, nur „davon"-Breakdown). undefined/voll => normaler Sparren.
                schifter: schifterArt && schifterArt !== 'voll' ? schifterArt : null,
                istSchifter: !!(schifterArt && schifterArt !== 'voll'),
            };
            this.holzliste.push({...mesh.userData, volGesamt: vol, anzahl: 1});
            group.add(mesh);
        };

        // Sparren
        const numRafters = Math.min(2000, Math.max(1, Math.floor(uMax / dim.rafterDist)));
        let sparrenAnzahl = 0; // Reparatur 7: echte Sparrenzahl dieser Fläche zählen

        // Reparatur 10: SICHERE Öffnungen dieser Fläche bestimmen (für Sparren-Trennung +
        // Wechselhölzer). Sicher = Reparatur-9-Analyse liefert wechselAnzahl>0 (angrenzende
        // tragende Sparren eindeutig, nicht randnah, nicht außerhalb). Öffnungsmaße: Breite=u,
        // Höhe=v — NICHT die Aufbau-Tiefe (depth).
        const flaecheOeffnungen = (this.currentObstacles || [])
            .filter((o:any) => o && o.surfaceId === id && Number.isFinite(o.x) && Number.isFinite(o.y) && Number.isFinite(o.width) && Number.isFinite(o.height))
            .map((o:any) => {
                const a = analysiereAuswechslung({ breiteM: uMax, hoeheM: vMax }, { xRel: o.x, yRel: o.y, breiteM: o.width, hoeheM: o.height }, dim.rafterDist, { rafterWidthM: dim.rafterW });
                const uC = o.x * uMax, vC = o.y * vMax, hb = (o.width || 0) / 2, hh = (o.height || 0) / 2, rand = 0.05;
                return { sicher: a.wechselAnzahl > 0, uMin: uC - hb - rand, uMax: uC + hb + rand, vMin: vC - hh - rand, vMax: vC + hh + rand };
            })
            .filter((op:any) => op.sicher);

        const sparrenU: number[] = [];
        for(let i=0; i<=numRafters; i++) {
            const u = dim.rafterW/2 + i * ((uMax - dim.rafterW)/Math.max(1, numRafters));
            sparrenU.push(u);
            const hits = this.getLineIntersections(u, polygon, true);
            for (let k = 0; k < hits.length; k += 2) {
                if (hits[k+1] !== undefined && hits[k+1] - hits[k] > 0.05) {
                    // Reparatur 10: trennt eine SICHERE Öffnung diesen Sparrenabschnitt eindeutig?
                    const treffer = flaecheOeffnungen.filter((op:any) => u >= op.uMin && u <= op.uMax);
                    const teile = treffer.length === 1 ? sparrenTeilstuecke(hits[k], hits[k+1], treffer[0].vMin, treffer[0].vMax, 0.1) : [];
                    if (teile.length === 2) {
                        // Sicherer Fall: voller Sparren RAUS, unteres + oberes Teilstück REIN (keine Doppelzählung).
                        placeBeam(true, u, teile[0].vStart, teile[0].vEnd, dim.rafterW, dim.rafterH, this.mats.wood, this.gRafters, hRafter, `Sparrenstück unten ${id}`);
                        placeBeam(true, u, teile[1].vStart, teile[1].vEnd, dim.rafterW, dim.rafterH, this.mats.wood, this.gRafters, hRafter, `Sparrenstück oben ${id}`);
                        placeBeam(true, u, teile[0].vStart, teile[0].vEnd, dim.counterW, dim.counterH, this.mats.batten, this.gCounterBattens, hCounter, 'Konterlatte');
                        placeBeam(true, u, teile[1].vStart, teile[1].vEnd, dim.counterW, dim.counterH, this.mats.batten, this.gCounterBattens, hCounter, 'Konterlatte');
                    } else {
                        // Unsicher / kein Schnitt: voller Sparren bleibt (Prüffall via Reparatur 9).
                        // EA28: An Kehle (L/T/U-Notch) / Grat (Walm) ist dieser Sparren polygon-geclippt
                        // (vStart>0 bzw. vEnd<vMax) → fachlich ein Schiftsparren. Klassifizieren + benennen
                        // (Querschnitt unverändert; type bleibt 'sparren', daher keine Doppelzählung).
                        const sa = klassifiziereSchifter(hits[k], hits[k+1], vMax);
                        const sName = sa === 'voll' ? `Sparren ${id}`
                            : `Schiftsparren ${sa === 'kehle' ? 'Kehle' : sa === 'grat' ? 'Grat' : 'Misch'} ${id}`;
                        placeBeam(true, u, hits[k], hits[k+1], dim.rafterW, dim.rafterH, this.mats.wood, this.gRafters, hRafter, sName, sa);
                        placeBeam(true, u, hits[k], hits[k+1], dim.counterW, dim.counterH, this.mats.batten, this.gCounterBattens, hCounter, 'Konterlatte');
                    }
                    sparrenAnzahl++;
                }
            }
        }

        // Reparatur 10: Wechselhölzer (oben + unten) als ECHTE 3D-Bauteile für sichere Öffnungen.
        // Anfang/Ende = flankierende tragende Sparren (eindeutig); Länge = echte 3D-Strecke.
        flaecheOeffnungen.forEach((op:any) => {
            const links = sparrenU.filter((x) => x < op.uMin);
            const rechts = sparrenU.filter((x) => x > op.uMax);
            if (!links.length || !rechts.length) return; // keine eindeutigen tragenden Sparren -> kein Wechsel
            const uL = Math.max(...links), uR = Math.min(...rechts);
            const punkt = (uu:number, vv:number) => origin.clone().addScaledVector(uDir, uu).addScaledVector(vDir, vv).addScaledVector(crossNormal, hRafter);
            // E16: Wechselhölzer in EIGENE Ebene gWechsel (trennbar vom übrigen Dachstuhl), Holzliste bleibt erhalten.
            this.drawBeamBetweenPoints(punkt(uL, op.vMin), punkt(uR, op.vMin), dim.rafterW, dim.rafterH, this.mats.wood, this.gWechsel, `Wechsel unten ${id}`, 'wechsel');
            this.drawBeamBetweenPoints(punkt(uL, op.vMax), punkt(uR, op.vMax), dim.rafterW, dim.rafterH, this.mats.wood, this.gWechsel, `Wechsel oben ${id}`, 'wechsel');
        });

        // Traglattung
        const numBattens = Math.min(2000, Math.max(1, Math.floor(vMax / dim.battenDist)));
        for(let j=0; j<=numBattens; j++) {
            const v = j * dim.battenDist;
            const hits = this.getLineIntersections(v, polygon, false); 
            for (let k = 0; k < hits.length; k += 2) {
                if (hits[k+1] !== undefined && hits[k+1] - hits[k] > 0.05) {
                    placeBeam(false, v, hits[k], hits[k+1], dim.battenH, dim.battenW, this.mats.batten, this.gBattens, hBatten, 'Traglatte');
                }
            }
        }

        const rayGeo = new THREE.ShapeGeometry(shape);
        const rayTarget = new THREE.Mesh(rayGeo, this.mats.invisible);
        rayTarget.position.copy(origin).addScaledVector(crossNormal, hTile + 0.05);
        rayTarget.quaternion.copy(quat);
        this.gRoof.add(rayTarget);
        const roofSurfaceOrigin = origin.clone().addScaledVector(crossNormal, hTile + 0.05);
        this.addSurface(id, rayTarget, roofSurfaceOrigin, uDir, vDir, crossNormal, uMax, vMax, 'poly', polygon);
        // Reparatur 7: echte Sparrenzahl an der Fläche hinterlegen (war vorher nie gesetzt).
        const surfRef = this.surfaces.get(id);
        if (surfRef) surfRef.numRafters = sparrenAnzahl;
    }

    buildMainWallAndGables(p: BuildingParams, cx: number, cz: number, rotY: number, length: number, width: number, buildG1: boolean = true, buildG2: boolean = true) {
        const rad = p.pitch * Math.PI / 180;
        const w2 = width / 2;
        const rise = w2 * Math.tan(rad);
        
        const gGroup = new THREE.Group();
        const wall = new THREE.Mesh(new THREE.BoxGeometry(length, p.height, width), this.mats.wall);
        wall.position.y = p.height/2;
        gGroup.add(wall);

        const shape = new THREE.Shape();
        shape.moveTo(-w2, 0); shape.lineTo(w2, 0); shape.lineTo(0, rise);
        const geo = new THREE.ExtrudeGeometry(shape, { depth: 0.2, bevelEnabled: false });
        
        if (buildG1) {
            const g1 = new THREE.Mesh(geo, this.mats.wall);
            g1.rotation.y = Math.PI/2; 
            g1.position.set(-length/2, p.height, 0); 
            gGroup.add(g1);
        }
        
        if (buildG2) {
            const g2 = new THREE.Mesh(geo, this.mats.wall);
            g2.rotation.y = Math.PI/2; 
            g2.position.set(length/2 - 0.2, p.height, 0); 
            gGroup.add(g2);
        }

        gGroup.position.set(cx, 0, cz);
        gGroup.rotation.y = rotY;
        gGroup.updateMatrixWorld(true);
        [...gGroup.children].forEach(c => { c.applyMatrix4(gGroup.matrix); this.gStructure.add(c); });
    }

    buildCompoundPitchedFaces(p: BuildingParams, mat: THREE.Material, type: 'L' | 'T') {
        const rad = p.pitch * Math.PI / 180;
        const L = Math.max(0.1, p.length), W = Math.max(0.1, p.width);
        const oh = p.overhang, ohG = p.overhangGable;
        const L_b = Math.max(0.1, p.lengthB), W_b = Math.max(0.1, p.widthB);
        
        const slopeLen = (W/2 + oh) / Math.cos(rad);
        const slopeLenExt = (W_b/2 + oh) / Math.cos(rad);
        
        const kerve = (p.rafterHeight/100) * 0.25;
        const hPivot = p.height + 0.14 + (p.rafterHeight/100/2 - kerve) * Math.cos(rad);
        const yEaveEdge = hPivot - (oh * Math.tan(rad));

        const uMaxMain = L + 2*ohG;
        const vMaxMain = slopeLen;

        const pf_b = 0.14, pf_h = 0.14;
        const yPf = p.height + pf_h/2;

        this.gRafters.add(this.createBeam(L + 2*ohG, pf_h, pf_b, new THREE.Vector3(0, yPf, -W/2 + pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Nord', 'pfette'));

        const oN = new THREE.Vector3(L/2 + ohG, yEaveEdge, -W/2 - oh);
        const uN = new THREE.Vector3(-1, 0, 0);
        const vN = new THREE.Vector3(0, Math.sin(rad), Math.cos(rad));
        const nN = new THREE.Vector3(0, Math.cos(rad), -Math.sin(rad));
        const polyN = [new THREE.Vector2(0,0), new THREE.Vector2(uMaxMain,0), new THREE.Vector2(uMaxMain,vMaxMain), new THREE.Vector2(0,vMaxMain)];
        this.buildRoofFace('main_N', p, oN, uN, vN, nN, uMaxMain, vMaxMain, polyN, mat);

        const oS = new THREE.Vector3(-L/2 - ohG, yEaveEdge, W/2 + oh);
        const uS = new THREE.Vector3(1, 0, 0);
        const vS = new THREE.Vector3(0, Math.sin(rad), -Math.cos(rad));
        const nS = new THREE.Vector3(0, Math.cos(rad), Math.sin(rad));
        
        let cx = type === 'T' ? 0 : L/2 - W_b/2;
        let polyS = [];
        
        const cx_local = cx - (-L/2 - ohG); 
        const u_L = cx_local - (W_b/2 + oh);
        const u_R = cx_local + (W_b/2 + oh);
        const v_peak = (W_b/2 + oh) / Math.cos(rad);

        const mainLeft = -L/2 - ohG;
        const mainRight = L/2 + ohG;
        const leftEnd = cx - W_b/2;
        const rightStart = cx + W_b/2;
        
        if (leftEnd > mainLeft) {
            const lenL = leftEnd - mainLeft;
            this.gRafters.add(this.createBeam(lenL, pf_h, pf_b, new THREE.Vector3(mainLeft + lenL/2, yPf, W/2 - pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Süd Links', 'pfette'));
        }
        if (rightStart < mainRight) {
            const lenR = mainRight - rightStart;
            this.gRafters.add(this.createBeam(lenR, pf_h, pf_b, new THREE.Vector3(rightStart + lenR/2, yPf, W/2 - pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Süd Rechts', 'pfette'));
        }

        const extLen = L_b + ohG;
        const extZCenter = W/2 + extLen/2;
        this.gRafters.add(this.createBeam(pf_b, pf_h, extLen, new THREE.Vector3(cx - W_b/2 + pf_b/2, yPf, extZCenter), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Anbau West', 'pfette'));
        this.gRafters.add(this.createBeam(pf_b, pf_h, extLen, new THREE.Vector3(cx + W_b/2 - pf_b/2, yPf, extZCenter), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Anbau Ost', 'pfette'));

        polyS.push(new THREE.Vector2(0, vMaxMain));
        if (u_L > 0) {
            polyS.push(new THREE.Vector2(0, 0));
            polyS.push(new THREE.Vector2(u_L, 0));
        } else {
            const slopeV_left = v_peak / (cx_local - u_L);
            const v_int_left = v_peak - slopeV_left * cx_local;
            polyS.push(new THREE.Vector2(0, Math.max(0, v_int_left)));
        }
        
        polyS.push(new THREE.Vector2(cx_local, v_peak));
        
        if (u_R < uMaxMain) {
            polyS.push(new THREE.Vector2(u_R, 0));
            polyS.push(new THREE.Vector2(uMaxMain, 0));
        } else {
            const slopeV_right = v_peak / (cx_local - u_R); 
            const v_int_right = v_peak + slopeV_right * (uMaxMain - cx_local);
            polyS.push(new THREE.Vector2(uMaxMain, Math.max(0, v_int_right)));
        }
        polyS.push(new THREE.Vector2(uMaxMain, vMaxMain));

        const hRafterCenter = (-p.rafterHeight/100/2 + kerve) * Math.cos(rad);
        const pEndPeak = new THREE.Vector3(cx, yEaveEdge + v_peak * Math.sin(rad) + hRafterCenter, W/2 - W_b/2);
        
        if (u_L > 0) {
            const pStartL = new THREE.Vector3(cx - W_b/2 - oh, yEaveEdge + hRafterCenter, W/2 + oh);
            this.drawBeamBetweenPoints(pStartL, pEndPeak, p.rafterWidth/100*1.5, p.rafterHeight/100*2, this.mats.wood, this.gRafters, 'Kehlsparren Links', 'kehlsparren');
        }
        
        const pStartR = new THREE.Vector3(cx + W_b/2 + oh, yEaveEdge + hRafterCenter, W/2 + oh);
        if (type === 'T' && u_R < uMaxMain) {
            this.drawBeamBetweenPoints(pStartR, pEndPeak, p.rafterWidth/100*1.5, p.rafterHeight/100*2, this.mats.wood, this.gRafters, 'Kehlsparren Rechts', 'kehlsparren');
        } else {
            this.drawBeamBetweenPoints(pStartR, pEndPeak, p.rafterWidth/100*1.5, p.rafterHeight/100*2, this.mats.wood, this.gRafters, 'Gratsparren Rechts', 'gratsparren');
        }
        
        const yRidge = hPivot + (slopeLen - oh/Math.cos(rad)) * Math.sin(rad);
        this.gRafters.add(this.createBeam(L + 2*ohG, 0.14, 0.14, new THREE.Vector3(0, yRidge - 0.07, 0), new THREE.Euler(0,0,0), this.mats.wood, 'Firstpfette Main', 'pfette'));
        
        const ridgeCapMain = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, L + 2*ohG, 8, 1, false, 0, Math.PI), mat);
        ridgeCapMain.rotation.z = Math.PI / 2;
        ridgeCapMain.position.set(0, yRidge + 0.16 + (p.layerSpread || 0), 0);
        this.gVisualTiles.add(ridgeCapMain);
        
        const yRidgeExt = hPivot + (slopeLenExt - oh/Math.cos(rad)) * Math.sin(rad);
        const ridgeExtLen = L_b + ohG + W_b/2;
        const ridgeExtZStart = W/2 - W_b/2;
        const ridgeExtCenterZ = ridgeExtZStart + ridgeExtLen / 2;
        this.gRafters.add(this.createBeam(0.14, 0.14, ridgeExtLen, new THREE.Vector3(cx, yRidgeExt - 0.07, ridgeExtCenterZ), new THREE.Euler(0,0,0), this.mats.wood, 'Firstpfette Anbau', 'pfette'));

        const ridgeCapExt = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, ridgeExtLen, 8, 1, false, 0, Math.PI), mat);
        ridgeCapExt.rotation.z = Math.PI / 2;
        ridgeCapExt.rotation.y = Math.PI / 2;
        ridgeCapExt.position.set(cx, yRidgeExt + 0.16 + (p.layerSpread || 0), ridgeExtCenterZ);
        this.gVisualTiles.add(ridgeCapExt);
        
        this.buildRoofFace('main_S', p, oS, uS, vS, nS, uMaxMain, vMaxMain, polyS, mat);

        const totalU = L_b + ohG + W_b/2; 
        const uValleyBot = W_b/2 + oh;

        const oExtW = new THREE.Vector3(cx - W_b/2 - oh, yEaveEdge, W/2 - W_b/2);
        const uExtW = new THREE.Vector3(0, 0, 1);
        const vExtW = new THREE.Vector3(Math.cos(rad), Math.sin(rad), 0);
        const nExtW = new THREE.Vector3(-Math.sin(rad), Math.cos(rad), 0);
        const polyExtW = [new THREE.Vector2(0, v_peak), new THREE.Vector2(uValleyBot, 0), new THREE.Vector2(totalU, 0), new THREE.Vector2(totalU, slopeLenExt)];
        this.buildRoofFace('ext_W', p, oExtW, uExtW, vExtW, nExtW, totalU, slopeLenExt, polyExtW, mat);

        if (type === 'T') {
            const oExtE = new THREE.Vector3(cx + W_b/2 + oh, yEaveEdge, (W/2 - W_b/2) + totalU);
            const uExtE = new THREE.Vector3(0, 0, -1);
            const vExtE = new THREE.Vector3(-Math.cos(rad), Math.sin(rad), 0);
            const nExtE = new THREE.Vector3(Math.sin(rad), Math.cos(rad), 0);
            const polyExtE = [
                new THREE.Vector2(0, 0), 
                new THREE.Vector2(totalU - uValleyBot, 0), 
                new THREE.Vector2(totalU, v_peak), 
                new THREE.Vector2(0, slopeLenExt)
            ];
            this.buildRoofFace('ext_E', p, oExtE, uExtE, vExtE, nExtE, totalU, slopeLenExt, polyExtE, mat);
        } else if (type === 'L') {
            const oExtE = new THREE.Vector3(cx + W_b/2 + oh, yEaveEdge, (W/2 - W_b/2) + totalU);
            const uExtE = new THREE.Vector3(0, 0, -1);
            const vExtE = new THREE.Vector3(-Math.cos(rad), Math.sin(rad), 0);
            const nExtE = new THREE.Vector3(Math.sin(rad), Math.cos(rad), 0);
            const polyExtE = [
                new THREE.Vector2(0, 0), 
                new THREE.Vector2(totalU - uValleyBot, 0), 
                new THREE.Vector2(totalU, v_peak), 
                new THREE.Vector2(0, slopeLenExt)
            ];
            this.buildRoofFace('ext_E', p, oExtE, uExtE, vExtE, nExtE, totalU, slopeLenExt, polyExtE, mat);
        }

        // E26: sichtbare ECHTE Kehl-/Gratlinien (Dachhaut-/Deckungskehle, Welt-Koordinaten) als Overlay
        // in der EIGENEN, schaltbaren Ebene gVerschneidung (wird mit der Geometrie in updateBuilding
        // gebaut+geleert, von updateObstacles unberührt). SSOT = dachVerschneidung.ts; Flächen/Holzliste
        // unverändert. pruefpflichtig -> nichts zeichnen (ehrlich statt falscher Linie).
        const verschn = verschneidungslinien({
            form: type === 'T' ? 't' : 'l',
            length: L, width: W, lengthB: L_b, widthB: W_b,
            overhang: oh, overhangGable: ohG, pitchGrad: p.pitch, height: p.height, rafterHeight: p.rafterHeight,
        });
        for (const lin of verschn) {
            if (lin.pruefpflichtig) continue;
            const geo = new THREE.BufferGeometry().setFromPoints([
                new THREE.Vector3(lin.startDeck.x, lin.startDeck.y, lin.startDeck.z),
                new THREE.Vector3(lin.endeDeck.x, lin.endeDeck.y, lin.endeDeck.z),
            ]);
            this.gVerschneidung.add(new THREE.Line(geo, lin.art === 'kehle' ? this.mats.echteKehle : this.mats.echterGrat));
        }
    }

    buildSattel(p: BuildingParams, mat: THREE.Material) {
        this.buildMainWallAndGables(p, 0, 0, 0, Math.max(0.1, p.length), Math.max(0.1, p.width), true, true);
        const L = Math.max(0.1, p.length), W = Math.max(0.1, p.width);
        const oh = p.overhang, ohG = p.overhangGable;
        const rad = p.pitch * Math.PI / 180;
        const slopeLen = (W/2 + oh) / Math.cos(rad);
        const uMaxMain = L + 2*ohG;
        const kerve = (p.rafterHeight/100) * 0.25;
        const hPivot = p.height + 0.14 + (p.rafterHeight/100/2 - kerve) * Math.cos(rad);
        const yEaveEdge = hPivot - (oh * Math.tan(rad));
        
        const pf_b = 0.14, pf_h = 0.14;
        const yPf = p.height + pf_h/2;
        this.gRafters.add(this.createBeam(L + 2*ohG, pf_h, pf_b, new THREE.Vector3(0, yPf, -W/2 + pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Nord', 'pfette'));
        this.gRafters.add(this.createBeam(L + 2*ohG, pf_h, pf_b, new THREE.Vector3(0, yPf, W/2 - pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Süd', 'pfette'));
        
        const yRidge = hPivot + (slopeLen - oh/Math.cos(rad)) * Math.sin(rad);
        this.gRafters.add(this.createBeam(L + 2*ohG, 0.14, 0.14, new THREE.Vector3(0, yRidge - 0.07, 0), new THREE.Euler(0,0,0), this.mats.wood, 'Firstpfette', 'pfette'));
        
        const ridgeCap = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, L + 2*ohG, 8, 1, false, 0, Math.PI), mat);
        ridgeCap.rotation.z = Math.PI / 2;
        ridgeCap.position.set(0, yRidge + 0.16 + (p.layerSpread || 0), 0);
        this.gVisualTiles.add(ridgeCap);

        const polyN = [new THREE.Vector2(0,0), new THREE.Vector2(uMaxMain,0), new THREE.Vector2(uMaxMain,slopeLen), new THREE.Vector2(0,slopeLen)];
        const oN = new THREE.Vector3(L/2 + ohG, yEaveEdge, -W/2 - oh);
        const uN = new THREE.Vector3(-1, 0, 0); const vN = new THREE.Vector3(0, Math.sin(rad), Math.cos(rad)); const nN = new THREE.Vector3(0, Math.cos(rad), -Math.sin(rad));
        this.buildRoofFace('main_N', p, oN, uN, vN, nN, uMaxMain, slopeLen, polyN, mat);

        const polyS = [new THREE.Vector2(0,0), new THREE.Vector2(uMaxMain,0), new THREE.Vector2(uMaxMain,slopeLen), new THREE.Vector2(0,slopeLen)];
        const oS = new THREE.Vector3(-L/2 - ohG, yEaveEdge, W/2 + oh);
        const uS = new THREE.Vector3(1, 0, 0); const vS = new THREE.Vector3(0, Math.sin(rad), -Math.cos(rad)); const nS = new THREE.Vector3(0, Math.cos(rad), Math.sin(rad));
        this.buildRoofFace('main_S', p, oS, uS, vS, nS, uMaxMain, slopeLen, polyS, mat);
    }

    buildCompoundPitched(p: BuildingParams, mat: THREE.Material, type: 'L' | 'T') {
        const L = Math.max(0.1, p.length); const W = Math.max(0.1, p.width);
        this.buildMainWallAndGables(p, 0, 0, 0, L, W, true, true);
        let cx = type === 'T' ? 0 : L/2 - p.widthB/2;
        let cz = W/2 + p.lengthB/2;
        this.buildMainWallAndGables({...p, height: p.height}, cx, cz, Math.PI/2, Math.max(0.1, p.lengthB), Math.max(0.1, p.widthB), true, false);
        this.buildCompoundPitchedFaces(p, mat, type);
    }

    // Eingabeaufforderung 27: geneigte U-Form — Hauptdach-Satteldach + ZWEI spiegelsymmetrische Anbau-
    // Satteldächer (Flügel, außen bündig am Giebel, innen je eine Kehle). SSOT = dachUForm.ts (die Engine
    // ZEICHNET exakt die geprüften Flächen/Kehlsparren). Nur bei uBauGueltig aufgerufen (sonst buildFlat).
    buildCompoundPitchedU(p: BuildingParams, mat: THREE.Material) {
        const ue = {
            length: p.length, width: p.width, widthB: p.widthB, lengthB: p.lengthB,
            overhang: p.overhang, overhangGable: p.overhangGable, pitchGrad: p.pitch, height: p.height, rafterHeight: p.rafterHeight,
        };
        const k = uFormKonstanten(ue);
        const L = k.L, W = k.W, W_b = k.W_b, L_b = k.L_b, ohG = k.ohG, spread = p.layerSpread || 0;
        const v3 = (q: any) => new THREE.Vector3(q.x, q.y, q.z);

        // Wände: Hauptbaukörper + zwei Flügel (wie L/T, zweifach gespiegelt).
        this.buildMainWallAndGables(p, 0, 0, 0, L, W, true, true);
        for (const cx of [-k.cxWing, k.cxWing]) {
            this.buildMainWallAndGables({ ...p }, cx, W / 2 + L_b / 2, Math.PI / 2, Math.max(0.1, L_b), Math.max(0.1, W_b), true, false);
        }

        // 6 Dachflächen (main_N, main_S-Doppelnotch, je Flügel innen+außen) aus der reinen SSOT.
        for (const f of uFormFlaechen(ue)) {
            const poly = f.poly.map((pt) => new THREE.Vector2(pt.x, pt.y));
            this.buildRoofFace(f.id, p, v3(f.origin), v3(f.uDir), v3(f.vDir), v3(f.normal), f.uMax, f.vMax, poly, mat);
        }

        // Zwei Innen-Kehlsparren (geom. ermittelt; in die Holzliste).
        for (const ks of uFormKehlsparren(ue)) {
            this.drawBeamBetweenPoints(v3(ks.start), v3(ks.ende), p.rafterWidth / 100 * 1.5, p.rafterHeight / 100 * 2, this.mats.wood, this.gRafters, ks.name, 'kehlsparren');
        }

        // Pfetten + Firste/Caps (Haupt + je Flügel).
        const pf = 0.14, yPf = p.height + pf / 2;
        this.gRafters.add(this.createBeam(L + 2 * ohG, pf, pf, new THREE.Vector3(0, yPf, -W / 2 + pf / 2), new THREE.Euler(0, 0, 0), this.mats.wood, 'Fußpfette Nord', 'pfette'));
        if (L - 2 * W_b > 0.05) this.gRafters.add(this.createBeam(L - 2 * W_b, pf, pf, new THREE.Vector3(0, yPf, W / 2 - pf / 2), new THREE.Euler(0, 0, 0), this.mats.wood, 'Fußpfette Süd (Hof)', 'pfette'));
        this.gRafters.add(this.createBeam(L + 2 * ohG, 0.14, 0.14, new THREE.Vector3(0, k.yRidge - 0.07, 0), new THREE.Euler(0, 0, 0), this.mats.wood, 'Firstpfette Main', 'pfette'));
        const ridgeMain = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, L + 2 * ohG, 8, 1, false, 0, Math.PI), mat);
        ridgeMain.rotation.z = Math.PI / 2; ridgeMain.position.set(0, k.yRidge + 0.16 + spread, 0); this.gVisualTiles.add(ridgeMain);

        const ridgeExtLen = L_b + ohG + W_b / 2;
        const ridgeExtCenterZ = (W / 2 - W_b / 2) + ridgeExtLen / 2;
        for (const cx of [-k.cxWing, k.cxWing]) {
            this.gRafters.add(this.createBeam(0.14, 0.14, ridgeExtLen, new THREE.Vector3(cx, k.yRidgeExt - 0.07, ridgeExtCenterZ), new THREE.Euler(0, 0, 0), this.mats.wood, 'Firstpfette Anbau', 'pfette'));
            const cap = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, ridgeExtLen, 8, 1, false, 0, Math.PI), mat);
            cap.rotation.z = Math.PI / 2; cap.rotation.y = Math.PI / 2; cap.position.set(cx, k.yRidgeExt + 0.16 + spread, ridgeExtCenterZ); this.gVisualTiles.add(cap);
            // Anbau-Fußpfetten (entlang z) je Flügel
            const extLen = L_b + ohG;
            this.gRafters.add(this.createBeam(pf, pf, extLen, new THREE.Vector3(cx - W_b / 2 + pf / 2, yPf, W / 2 + extLen / 2), new THREE.Euler(0, 0, 0), this.mats.wood, 'Fußpfette Anbau', 'pfette'));
            this.gRafters.add(this.createBeam(pf, pf, extLen, new THREE.Vector3(cx + W_b / 2 - pf / 2, yPf, W / 2 + extLen / 2), new THREE.Euler(0, 0, 0), this.mats.wood, 'Fußpfette Anbau', 'pfette'));
        }

        // E26-Overlay: zwei echte Kehllinien (hofseitig) in der schaltbaren Ebene gVerschneidung.
        for (const lin of verschneidungslinien({ form: 'u', length: L, width: W, lengthB: L_b, widthB: W_b, overhang: p.overhang, overhangGable: ohG, pitchGrad: p.pitch, height: p.height, rafterHeight: p.rafterHeight })) {
            if (lin.pruefpflichtig) continue;
            const geo = new THREE.BufferGeometry().setFromPoints([v3(lin.startDeck), v3(lin.endeDeck)]);
            this.gVerschneidung.add(new THREE.Line(geo, this.mats.echteKehle));
        }
    }

    buildWalm(p: BuildingParams, mat: THREE.Material) {
        const rad = p.pitch * Math.PI / 180;
        const oh = p.overhang;
        const yEave = p.height; 
        const L = Math.max(0.1, p.length); const W = Math.max(0.1, p.width);
        const ridgeLen = Math.max(0, L - W);

        const w = new THREE.Mesh(new THREE.BoxGeometry(L, p.height, W), this.mats.wall);
        w.position.y = p.height/2; 
        this.gStructure.add(w);

        const pf_b = 0.14, pf_h = 0.14;
        const yPf = p.height + pf_h/2;
        this.gRafters.add(this.createBeam(L, pf_h, pf_b, new THREE.Vector3(0, yPf, W/2 - pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Süd', 'pfette'));
        this.gRafters.add(this.createBeam(L, pf_h, pf_b, new THREE.Vector3(0, yPf, -W/2 + pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Nord', 'pfette'));
        this.gRafters.add(this.createBeam(pf_b, pf_h, W, new THREE.Vector3(-L/2 + pf_b/2, yPf, 0), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette West', 'pfette'));
        this.gRafters.add(this.createBeam(pf_b, pf_h, W, new THREE.Vector3(L/2 - pf_b/2, yPf, 0), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Ost', 'pfette'));

        const kerve = (p.rafterHeight/100) * 0.25;
        const yEaveTip = yEave + 0.14 + (p.rafterHeight/100/2 - kerve) * Math.cos(rad) - (oh * Math.tan(rad));
        const slopeLen = (W/2 + oh) / Math.cos(rad);
        const yRidge = yEaveTip + slopeLen * Math.sin(rad);

        const wBotS = L + 2*oh;
        const wBotW = W + 2*oh;

        const buildWalmFace = (id: string, origin: THREE.Vector3, uDir: THREE.Vector3, vDir: THREE.Vector3, normal: THREE.Vector3, wBot: number, wTop: number) => {
            const poly = [
                new THREE.Vector2(0,0), new THREE.Vector2(wBot,0), 
                new THREE.Vector2(wBot/2 + wTop/2, slopeLen), new THREE.Vector2(wBot/2 - wTop/2, slopeLen)
            ];
            this.buildRoofFace(id, p, origin, uDir, vDir, normal, wBot, slopeLen, poly, mat);
        };

        const oS = new THREE.Vector3(-wBotS/2, yEaveTip, W/2 + oh);
        buildWalmFace('south', oS, new THREE.Vector3(1,0,0), new THREE.Vector3(0, Math.sin(rad), -Math.cos(rad)), new THREE.Vector3(0, Math.cos(rad), Math.sin(rad)), wBotS, ridgeLen);
        
        const oN = new THREE.Vector3(wBotS/2, yEaveTip, -W/2 - oh);
        buildWalmFace('north', oN, new THREE.Vector3(-1,0,0), new THREE.Vector3(0, Math.sin(rad), Math.cos(rad)), new THREE.Vector3(0, Math.cos(rad), -Math.sin(rad)), wBotS, ridgeLen);
        
        const oW = new THREE.Vector3(-L/2 - oh, yEaveTip, -wBotW/2);
        buildWalmFace('west', oW, new THREE.Vector3(0,0,1), new THREE.Vector3(Math.cos(rad), Math.sin(rad), 0), new THREE.Vector3(-Math.sin(rad), Math.cos(rad), 0), wBotW, 0);
        
        const oE = new THREE.Vector3(L/2 + oh, yEaveTip, wBotW/2);
        buildWalmFace('east', oE, new THREE.Vector3(0,0,-1), new THREE.Vector3(-Math.cos(rad), Math.sin(rad), 0), new THREE.Vector3(Math.sin(rad), Math.cos(rad), 0), wBotW, 0);

        this.gRafters.add(this.createBeam(ridgeLen, 0.14, 0.14, new THREE.Vector3(0, yRidge - 0.07, 0), new THREE.Euler(0,0,0), this.mats.wood, 'Firstpfette'));

        if (ridgeLen > 0) {
            const ridgeCap = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, ridgeLen, 8, 1, false, 0, Math.PI), mat);
            ridgeCap.rotation.z = Math.PI / 2;
            ridgeCap.position.set(0, yRidge + 0.16 + (p.layerSpread || 0), 0);
            this.gVisualTiles.add(ridgeCap);
        }

        const hRafter = (-p.rafterHeight/100/2 + kerve) * Math.cos(rad);
        const drawGrat = (pEave: THREE.Vector3, pRidge: THREE.Vector3, n: string) => {
            this.drawBeamBetweenPoints(pEave, pRidge, p.rafterWidth/100*1.5, p.rafterHeight/100*2, this.mats.wood, this.gRafters, n, 'gratsparren');
            
            const len = pEave.distanceTo(pRidge) + 0.2;
            const center = pEave.clone().lerp(pRidge, 0.5);
            const cap = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, len, 8), mat);
            const dir = new THREE.Vector3().subVectors(pRidge, pEave).normalize();
            cap.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), dir);
            cap.position.copy(center).add(new THREE.Vector3(0, 0.16 + (p.layerSpread || 0), 0));
            this.gVisualTiles.add(cap);
        };
        drawGrat(new THREE.Vector3(-L/2 - oh, yEaveTip + hRafter, W/2 + oh), new THREE.Vector3(-ridgeLen/2, yRidge + hRafter, 0), 'Gratsparren VL');
        drawGrat(new THREE.Vector3(L/2 + oh, yEaveTip + hRafter, W/2 + oh), new THREE.Vector3(ridgeLen/2, yRidge + hRafter, 0), 'Gratsparren VR');
        drawGrat(new THREE.Vector3(-L/2 - oh, yEaveTip + hRafter, -W/2 - oh), new THREE.Vector3(-ridgeLen/2, yRidge + hRafter, 0), 'Gratsparren HL');
        drawGrat(new THREE.Vector3(L/2 + oh, yEaveTip + hRafter, -W/2 - oh), new THREE.Vector3(ridgeLen/2, yRidge + hRafter, 0), 'Gratsparren HR');
    }

    buildPult(p: BuildingParams, mat: THREE.Material) {
        const rad = p.pitch * Math.PI / 180;
        const rise = p.width * Math.tan(rad);
        const yLow = p.height;
        const yHigh = p.height + rise;
        const L = Math.max(0.1, p.length); const W = Math.max(0.1, p.width);
        
        const backWall = new THREE.Mesh(new THREE.BoxGeometry(L, yHigh, 0.2), this.mats.wall);
        backWall.position.set(0, yHigh/2, -W/2);
        this.gStructure.add(backWall);

        const frontWall = new THREE.Mesh(new THREE.BoxGeometry(L, yLow, 0.2), this.mats.wall);
        frontWall.position.set(0, yLow/2, W/2);
        this.gStructure.add(frontWall);

        const shape = new THREE.Shape();
        shape.moveTo(W/2, 0); shape.lineTo(W/2, yLow); shape.lineTo(-W/2, yHigh); shape.lineTo(-W/2, 0); 
        const sideGeo = new THREE.ExtrudeGeometry(shape, { depth: 0.2, bevelEnabled: false });
        const leftWall = new THREE.Mesh(sideGeo, this.mats.wall);
        leftWall.rotation.y = -Math.PI/2; leftWall.position.set(-L/2, 0, 0);
        this.gStructure.add(leftWall);
        const rightWall = new THREE.Mesh(sideGeo, this.mats.wall);
        rightWall.rotation.y = -Math.PI/2; rightWall.position.set(L/2 + 0.2, 0, 0); 
        this.gStructure.add(rightWall);

        const pf_b = 0.14, pf_h = 0.14;
        this.gRafters.add(this.createBeam(L + 2*p.overhangGable, pf_h, pf_b, new THREE.Vector3(0, yLow + pf_h/2, W/2 - pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette Traufe', 'pfette'));
        this.gRafters.add(this.createBeam(L + 2*p.overhangGable, pf_h, pf_b, new THREE.Vector3(0, yHigh + pf_h/2, -W/2 + pf_b/2), new THREE.Euler(0,0,0), this.mats.wood, 'Fußpfette First', 'pfette'));

        const pultCap = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, L + 2*p.overhangGable, 8, 1, false, 0, Math.PI), mat);
        pultCap.rotation.z = Math.PI / 2;
        pultCap.position.set(0, yHigh + 0.16 + (p.layerSpread || 0), -W/2 - p.overhang);
        this.gVisualTiles.add(pultCap);

        const uMax = L + 2*p.overhangGable;
        const vMax = (W + p.overhang*2) / Math.cos(rad);
        const kerve = (p.rafterHeight/100) * 0.25;
        const hPivot = p.height + 0.14 + (p.rafterHeight/100/2 - kerve) * Math.cos(rad);
        const yEaveEdge = hPivot - (p.overhang * Math.tan(rad));

        const origin = new THREE.Vector3(-L/2 - p.overhangGable, yEaveEdge, W/2 + p.overhang);
        const uDir = new THREE.Vector3(1, 0, 0);
        const vDir = new THREE.Vector3(0, Math.sin(rad), -Math.cos(rad));
        const normal = new THREE.Vector3(0, Math.cos(rad), Math.sin(rad));
        
        const poly = [new THREE.Vector2(0,0), new THREE.Vector2(uMax,0), new THREE.Vector2(uMax,vMax), new THREE.Vector2(0,vMax)];
        this.buildRoofFace('main', p, origin, uDir, vDir, normal, uMax, vMax, poly, mat);
    }

    buildFlat(p: BuildingParams, mat: THREE.Material) {
        const shape = new THREE.Shape();
        const L=Math.max(0.1, p.length), B=Math.max(0.1, p.width);
        // Eingabeaufforderung 13: EIN echtes Grundrisspolygon (rect/L/T/U) aus der gemeinsamen
        // Logik -> keine doppelte Grundfläche, keine Rechteck-Ersatzform für L/T/U. Fläche via
        // Polygon (Shoelace) statt L×B. Aufbauten/Schneefang nutzen weiter die echte 'main'-Fläche.
        const pts = grundrissPolygon(formAusShape(p.shape), L, B, p.lengthB, p.widthB); // 0-basiert [{x,y}]
        pts.forEach((pt, i) => (i === 0 ? shape.moveTo(pt.x - L/2, pt.y - B/2) : shape.lineTo(pt.x - L/2, pt.y - B/2)));
        shape.lineTo(pts[0].x - L/2, pts[0].y - B/2);
        const localPoly = pts.map(pt => new THREE.Vector2(pt.x, pt.y));
        const gGeo = new THREE.ExtrudeGeometry(shape, { depth: p.height, bevelEnabled:false });
        const walls = new THREE.Mesh(gGeo, this.mats.wall);
        walls.rotation.x = Math.PI/2; walls.position.y = p.height;
        this.gStructure.add(walls);

        // E19: Flachdach-Dachhaut über Rebuild-Closure (Loch nur bei sicherer Rechteckfläche).
        // Shape-Koordinaten sind zentriert (pt.x-L/2, pt.y-B/2); Öffnung (u,v in [0,L]×[0,B]) -> Offset.
        const baueDachhautFlat = (loecher: { uMin: number; uMax: number; vMin: number; vMax: number }[]) => {
            const s = new THREE.Shape();
            pts.forEach((pt, i) => (i === 0 ? s.moveTo(pt.x - L/2, pt.y - B/2) : s.lineTo(pt.x - L/2, pt.y - B/2)));
            s.lineTo(pts[0].x - L/2, pts[0].y - B/2);
            for (const Lc of (loecher || [])) {
                const hp = new THREE.Path();
                if (Lc.poly && Lc.poly.length >= 3) { // E25: Polygon-Loch (Flachdach hat i.d.R. keine Gauben)
                    hp.moveTo(Lc.poly[0].x - L/2, Lc.poly[0].y - B/2);
                    for (let i = 1; i < Lc.poly.length; i++) hp.lineTo(Lc.poly[i].x - L/2, Lc.poly[i].y - B/2);
                    hp.lineTo(Lc.poly[0].x - L/2, Lc.poly[0].y - B/2);
                } else {
                    const a = Lc.uMin - L/2, b = Lc.uMax - L/2, c = Lc.vMin - B/2, d = Lc.vMax - B/2;
                    hp.moveTo(a, c); hp.lineTo(b, c); hp.lineTo(b, d); hp.lineTo(a, d); hp.lineTo(a, c);
                }
                s.holes.push(hp);
            }
            const m = new THREE.Mesh(new THREE.ShapeGeometry(s), mat);
            m.rotation.x = Math.PI/2; m.position.y = p.height + 0.05;
            return m;
        };
        this.dachhautRebuild.set('main', baueDachhautFlat);
        const plane = baueDachhautFlat([]);
        this.gVisualTiles.add(plane);
        this.dachhautMeshes.set('main', plane);

        const rayTarget = new THREE.Mesh(new THREE.ShapeGeometry(shape), this.mats.invisible);
        rayTarget.rotation.x = Math.PI/2; rayTarget.position.y = p.height + 0.06;
        this.gRoof.add(rayTarget);
        this.addSurface('main', rayTarget, new THREE.Vector3(-L/2, p.height + 0.06, -B/2), new THREE.Vector3(1,0,0), new THREE.Vector3(0,0,1), new THREE.Vector3(0,1,0), L, B, 'poly', localPoly);
    }

    // --- OBSTACLES & UPDATES ---
    updateBuilding(p: BuildingParams, covering: RoofCovering, addRoofs: AdditionalRoof[] = [], obstaclesForSplit?: ObstacleData[]) {
        this.currentCover = covering;
        // Reparatur 10: aktuelle Aufbauten für die Sparren-Trennung bereitstellen (VOR dem Bau
        // der Dachflächen). Nicht übergeben -> bisheriger Stand bleibt erhalten (additiv).
        if (obstaclesForSplit !== undefined) this.currentObstacles = obstaclesForSplit;

        this.clearModules();
        [this.gStructure, this.gRafters, this.gMembrane, this.gCounterBattens, this.gInsulation, this.gBattens, this.gVisualTiles, this.gRoof, this.gWechsel, this.gVerschneidung].forEach(g => clearGroup(g));
        this.surfaces.clear();
        this.dachhautRebuild.clear(); this.dachhautMeshes.clear(); this.echteLochIds.clear(); // E19: Dachhaut-Loch-Tracking neu
        this.holzliste = [];
        
        clearGroup(this.gHighlight);
        this.gHighlight.add(this.obsHelper);

        const roofMat = this.getRoofMat();

        if(p.category === 'flat') this.buildFlat(p, roofMat);
        else if(p.shape === 'sattel') this.buildSattel(p, roofMat);
        else if(p.shape === 'pult') this.buildPult(p, roofMat);
        else if(p.shape === 'walm') this.buildWalm(p, roofMat);
        else if((p.shape === 'l-shape' || p.shape === 't-shape') && p.category === 'pitched') this.buildCompoundPitched(p, roofMat, p.shape === 'l-shape' ? 'L' : 'T');
        // E27: geneigte U-Form bauen — NUR wenn die Geometrie gültig ist (uBauGueltig). Sonst greift der
        // unveränderte buildFlat-Fallback unten (Phase-0-Sicherheitsnetz: nie leeres Dach/Endlosschleife).
        else if(p.shape === 'u-shape' && p.category === 'pitched' && uBauGueltig({ length: p.length, width: p.width, widthB: p.widthB, lengthB: p.lengthB, overhang: p.overhang, overhangGable: p.overhangGable, pitchGrad: p.pitch, height: p.height, rafterHeight: p.rafterHeight })) this.buildCompoundPitchedU(p, roofMat);
        // E13 (defensiv): jede unerwartete Shape-/Kategorie-Kombination (z. B. ungültiges pitched u-shape)
        // fällt auf das Flachdach-Polygon zurück -> nie ein still leeres Dach. Reguläre Pfade unberührt.
        else this.buildFlat(p, roofMat);

        if (this.onSurfacesUpdated) {
            // Reparatur 4: vorhandene Flächenmaße (width/height/type) mitliefern, damit die
            // rechte Sidebar echte Flächenwerte statt Gebäude-Gesamtwerte zeigen kann.
            const sfs = Array.from(this.surfaces.entries()).map(([id, s]) => {
                const names: Record<string, string> = { 'main_S': 'Hauptdach Süd', 'main_N': 'Hauptdach Nord', 'main_W': 'Walm West', 'main_E': 'Walm Ost', 'south': 'Hauptdach Süd', 'north': 'Hauptdach Nord', 'west': 'Walm West', 'east': 'Walm Ost', 'ext_S': 'Anbau Süd', 'ext_W': 'Anbau West', 'ext_E': 'Anbau Ost', 'ext_R_in': 'Anbau rechts innen', 'ext_R_out': 'Anbau rechts außen', 'ext_L_in': 'Anbau links innen', 'ext_L_out': 'Anbau links außen', 'main': 'Flachdach' };
                return { id, name: names[id] || id, width: s.width, height: s.height, type: s.type, area: polygonFlaecheM2(s.polygon) };
            });
            this.onSurfacesUpdated(sfs);
        }
    }

    // Eingabeaufforderung 19: schneidet die sichtbare Dachhaut (Ziegel-Plane) sicherer Rechteckflächen
    // mit echten Löchern (THREE.Shape.holes). Leichtgewichtig — läuft bei jeder Aufbau-Änderung, OHNE
    // den vollen Geometrie-Rebuild (Tragwerk/Belegung bleiben unberührt). Membran/Dämmung bleiben voll.
    aktualisiereDachhautLoecher(list: ObstacleData[]) {
        this.echteLochIds = new Set();
        this.dachhautRebuild.forEach((rebuild, id) => {
            const surf = this.surfaces.get(id);
            if (!surf) return;
            const poly = (surf.polygon || []).map((p: any) => ({ x: p.x, y: p.y }));
            // E25: aRad (Dachneigung) + pitch + lyApexMax (wie beim Zeichnen) für den realen Gauben-Fußabdruck.
            const aRad = Math.max(0, Math.asin(Math.max(-1, Math.min(1, surf.vDown?.y ?? 0))));
            const oeffn = (list || []).filter((o) => o.surfaceId === id).map((o) => ({
                id: o.id, art: o.type, surfaceId: id, xRel: o.x, yRel: o.y, breiteM: o.width, hoeheM: o.height, tiefeM: o.depth,
                pitch: o.pitch, lyApexMax: this.flaecheFirstHoehe(surf, o.x) - this.surfacePoint(surf, o.x, o.y, 0.02).y - 1e-3,
            }));
            // E23: Gauben-Löcher nur auf GENEIGTER Fläche (Sattel/Pult); flach (vNormal.y≈1) ausschließen.
            // Die Rechteck-Bedingung prüft berechneAusschnitt zusätzlich -> Walm/Trapez/L-T-U bleiben Prüffeld.
            const geneigt = (surf.vNormal?.y ?? 1) < 0.999;
            const satz = sichereLoecher(id, poly, surf.width, surf.height, oeffn, geneigt, aRad);
            const alt = this.dachhautMeshes.get(id);
            if (alt) { this.gVisualTiles.remove(alt); (alt.geometry as any)?.dispose?.(); }
            const neu = rebuild(satz.loecher.map((l) => ({ ...l.rect, poly: l.poly }))); // E25: poly (Pentagon) wenn vorhanden
            this.gVisualTiles.add(neu);
            this.dachhautMeshes.set(id, neu);
            satz.echteIds.forEach((eid) => this.echteLochIds.add(eid));
        });
    }

    updateObstacles(list: ObstacleData[]) {
        this.currentObstacles = list;
        clearGroup(this.gObstacles);
        clearGroup(this.gGaubenHaut); // E16: Gaubendach-Ebene synchron mit den Aufbauten neu aufbauen
        clearGroup(this.gAnschluss); // E17: Anschlusslinien-Ebene synchron neu aufbauen
        this.aktualisiereDachhautLoecher(list); // E19: echte Dachhaut-Löcher VOR der Öffnungsdarstellung bestimmen
        // Eingabeaufforderung 15: Öffnungs-/Prüffeld-Ebene + Gaubendachstuhl-Ebene synchron neu aufbauen.
        this.updateOeffnungenUndStuhl(list);

        list.forEach(obs => {
            const surf = this.surfaces.get(obs.surfaceId);
            if(!surf) return;
            const grp = new THREE.Group(); grp.userData = { id: obs.id };
            let mesh;
            // E16: Gaubendach-Teile werden separat gesammelt und in die eigene Ebene gGaubenHaut gelegt
            // (gleiche Welt-Transformation), damit „Gaubendach ausblenden" Körper/Wangen/Stuhl stehen lässt.
            const dachTeile: any[] = [];
            // E17: Anschlusslinien (lokale Koordinaten) je Aufbau -> eigene Ebene gAnschluss.
            let anschlussLinien: GaubeLinie[] = [];
            // E24: echte (berechnete) Kehl-Schnittlinien je Aufbau -> eigene Ebene gAnschluss, eigene Farbe.
            let echteKehleLinien: GaubeLinie[] = [];
            const aRad = Math.max(0, Math.asin(Math.max(-1, Math.min(1, surf.vDown.y)))); // Dachneigung der Fläche

            if(obs.type === 'chimney') {
                // Lotrecht stehend: Sockeltiefe an die Neigung gekoppelt (kaminGeometrie), damit der
                // Sockel auch up-slope unter die geneigte Fläche reicht -> kein Spalt am Durchgang.
                const k = kaminGeometrie({ type: 'chimney', x: obs.x, y: obs.y, width: obs.width, height: obs.height, depth: obs.depth }, aRad);
                mesh = new THREE.Mesh(new THREE.BoxGeometry(obs.width, obs.height + k.sockel, obs.depth), this.mats.chimney);
                mesh.position.y = (obs.height - k.sockel)/2;
                // Anschluss-/Manschettenlinie: Durchdringungs-Parallelogramm auf der Hauptdachebene.
                const tanA = Math.tan(aRad), hw = obs.width/2, hd = obs.depth/2;
                const fc = [
                    { lx: -hw, ly: +tanA*hd, lz: -hd }, { lx: +hw, ly: +tanA*hd, lz: -hd },
                    { lx: +hw, ly: -tanA*hd, lz: +hd }, { lx: -hw, ly: -tanA*hd, lz: +hd },
                ];
                anschlussLinien = [[fc[0], fc[1]], [fc[1], fc[2]], [fc[2], fc[3]], [fc[3], fc[0]]] as GaubeLinie[];
            } else if(obs.type === 'window') {
                // Dachfenster liegt IN der Dachfläche (surfaceQuaternion). Umlaufender Anschlussrahmen
                // (deckungsneutral) + separate Anschlusslinie in der Ebene gAnschluss.
                mesh = new THREE.Group();
                const windowMesh = new THREE.Mesh(new THREE.BoxGeometry(obs.width, 0.06, obs.height), this.mats.roofWindow);
                windowMesh.position.y = 0.03;
                const flashing = new THREE.Mesh(new THREE.BoxGeometry(obs.width+0.12, 0.025, obs.height+0.12), this.mats.frameColor);
                flashing.position.y = 0.012;
                mesh.add(windowMesh, flashing);
                const hw = obs.width/2 + 0.06, hh = obs.height/2 + 0.06, yo = 0.02;
                const c = [{ lx:-hw, ly:yo, lz:-hh }, { lx:hw, ly:yo, lz:-hh }, { lx:hw, ly:yo, lz:hh }, { lx:-hw, ly:yo, lz:hh }];
                anschlussLinien = [[c[0],c[1]],[c[1],c[2]],[c[2],c[3]],[c[3],c[0]]] as GaubeLinie[];
            } else if(obs.type === 'sat') {
                mesh = new THREE.Group();
                const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.05, 0.8), this.mats.metal);
                pole.rotation.x = Math.PI/2; pole.position.y = 0.4;
                const dish = new THREE.Mesh(new THREE.SphereGeometry(0.4, 32, 16, 0, Math.PI*2, 0, 0.5), this.mats.metal);
                dish.position.set(0, 0.8, 0.2); dish.rotation.x = -Math.PI/4;
                mesh.add(pole, dish);
            } else if (obs.type === 'schleppgaube' || obs.type === 'flachgaube' || obs.type === 'trapezgaube') {
                // E17: PULTGAUBE aus geprüfter Geometrie — Anschlusskante hinten EXAKT auf der Hauptdach-
                // ebene, KEINE Rückwand, kein Vertex über First. Front lotrecht, Pultneigung abgeleitet.
                mesh = new THREE.Group();
                const g = pultGaubeGeometrie({ type: obs.type, x: obs.x, y: obs.y, width: obs.width, height: obs.height, depth: obs.depth, pitch: obs.pitch }, aRad);
                const body = new THREE.Mesh(this.trisToGeometry(g.koerperTris), this.mats.gaubeKoerper);
                const winMesh = new THREE.Mesh(new THREE.PlaneGeometry(g.fenster.breite, g.fenster.hoehe), this.mats.dormerWindow);
                winMesh.position.set(g.fenster.center.lx, g.fenster.center.ly, g.fenster.center.lz);
                mesh.add(body, winMesh);
                dachTeile.push(new THREE.Mesh(this.trisToGeometry(g.dachTris), this.mats.gaubeDach));
                anschlussLinien = [g.anschluss.hinten, g.anschluss.links, g.anschluss.rechts, g.anschluss.front];
            } else if (obs.type === 'giebelgaube' || obs.type === 'spitzgaube') {
                // E17/E24: GIEBEL-/SPITZGAUBE: Frontgiebel lotrecht, Apex unter dem Hauptfirst geklemmt.
                // E24: bei sicherer Geometrie ZWEI ebene Dachflächen + Wangen + ECHTE Kehl-Schnittlinien
                // (Dach∩Hauptdach); sonst Rückfall auf schematische Anschlusslinien.
                mesh = new THREE.Group();
                const refY = this.surfacePoint(surf, obs.x, obs.y, 0.02).y;
                const lyApexMax = this.flaecheFirstHoehe(surf, obs.x) - refY - 1e-3;
                const g = giebelGaubeGeometrie({ type: obs.type, x: obs.x, y: obs.y, width: obs.width, height: obs.height, depth: obs.depth, pitch: obs.pitch }, aRad, lyApexMax);
                const body = new THREE.Mesh(this.trisToGeometry(g.koerperTris), this.mats.gaubeKoerper);
                const winMesh = new THREE.Mesh(new THREE.PlaneGeometry(g.fenster.breite, g.fenster.hoehe), this.mats.dormerWindow);
                winMesh.position.set(g.fenster.center.lx, g.fenster.center.ly, g.fenster.center.lz);
                mesh.add(body, winMesh);
                dachTeile.push(new THREE.Mesh(this.trisToGeometry(g.dachTris), this.mats.gaubeDach));
                if (g.echteKehle && g.echteKehleLinks && g.echteKehleRechts) {
                    // ECHTE Kehlen (eigene Farbe) + Wandanschlusslinien (fBL->Kehlfront) + Fußlinie.
                    echteKehleLinien = [g.echteKehleLinks, g.echteKehleRechts];
                    anschlussLinien = [
                        [g.verts.fBL, g.verts.kehleFrontL], [g.verts.fBR, g.verts.kehleFrontR], g.anschluss.front,
                    ] as GaubeLinie[];
                } else {
                    // Rückfall: schematische Anschluss-/Kehllinien (keine echte Verschneidung).
                    anschlussLinien = [g.anschluss.kehleLinks, g.anschluss.kehleRechts, g.anschluss.front];
                }
            } else if (obs.type === 'lichtkuppel') {
                // Flachdach-Lichtkuppel: Sockel (Aufkantung) + glasige Kuppel. depth = Aufbauhöhe.
                mesh = new THREE.Group();
                const sockel = new THREE.Mesh(new THREE.BoxGeometry(obs.width, 0.12, obs.height), this.mats.frameColor);
                sockel.position.y = 0.06;
                const r = Math.max(0.2, obs.width / 2);
                const kuppel = new THREE.Mesh(new THREE.SphereGeometry(r, 20, 12, 0, Math.PI*2, 0, Math.PI/2), this.mats.roofWindow);
                kuppel.scale.set(1, Math.max(0.15, obs.depth || 0.3) / r, (obs.height || obs.width) / obs.width);
                kuppel.position.y = 0.12;
                mesh.add(sockel, kuppel);
            } else {
                mesh = new THREE.Mesh(new THREE.CylinderGeometry(0.1, 0.1, 0.4), this.mats.metal);
                mesh.rotation.x = Math.PI/2;
            }

            grp.add(mesh);
            const pos = this.surfacePoint(surf, obs.x, obs.y, 0.02);
            grp.position.copy(pos);
            // Stehende Aufbauten (Gaube/Kamin) stehen LOTRECHT (sonst schweben sie bei steilem Dach);
            // liegende (Dachfenster/Lüfter/Lichtkuppel/Sat) bleiben in der Dachebene.
            const quat = istStehenderAufbau(obs.type) ? this.verticalSurfaceQuaternion(surf) : this.surfaceQuaternion(surf);
            grp.quaternion.copy(quat);
            if(obs.rotation) grp.rotateY(obs.rotation);
            this.gObstacles.add(grp);

            // E16: Gaubendach in EIGENE Ebene (gGaubenHaut) mit identischer Welt-Transformation.
            // So bleibt beim Ausblenden des Gaubendachs der Gaubenkörper/-stuhl/die Öffnung sichtbar.
            if (dachTeile.length) {
                const dachGrp = new THREE.Group(); dachGrp.userData = { id: obs.id, gaubenhaut: true };
                dachGrp.position.copy(pos);
                dachGrp.quaternion.copy(quat);
                if(obs.rotation) dachGrp.rotateY(obs.rotation);
                dachTeile.forEach(t => dachGrp.add(t));
                this.gGaubenHaut.add(dachGrp);
            }

            // E17: Anschlusslinien (Rück-/Seiten-/Front-/Kehl-/Manschettenlinien) in EIGENER Ebene
            // gAnschluss mit identischer Welt-Transformation -> liegen exakt auf der Dachfläche.
            if (anschlussLinien.length || echteKehleLinien.length) {
                const ansGrp = new THREE.Group(); ansGrp.userData = { id: obs.id, anschluss: true };
                ansGrp.position.copy(pos);
                ansGrp.quaternion.copy(quat);
                if(obs.rotation) ansGrp.rotateY(obs.rotation);
                this.linienZuObjekten(anschlussLinien, this.mats.anschlussLinie).forEach(l => ansGrp.add(l));
                // E24: echte berechnete Kehl-Schnittlinien in eigener Farbe (ehrliche Unterscheidung).
                this.linienZuObjekten(echteKehleLinien, this.mats.echteKehle).forEach(l => ansGrp.add(l));
                this.gAnschluss.add(ansGrp);
            }
        });
    }

    // Eingabeaufforderung 15: Dachöffnungen/Prüffelder (eigene Ebene) + schematischer Gaubendachstuhl
    // (eigene Ebene). Beide nutzen das EINHEITLICHE Flächen-Koordinatensystem (surfacePoint/-Quaternion),
    // liegen also exakt auf der Dachfläche -> kein Schweben. KEINE Dacheindeckung/Material/Statik.
    updateOeffnungenUndStuhl(list: ObstacleData[]) {
        clearGroup(this.gOeffnungen);
        clearGroup(this.gGaubenstuhl);
        const MIT_OEFFNUNG = ['chimney', 'window', 'lichtkuppel', 'schleppgaube', 'trapezgaube', 'flachgaube', 'giebelgaube', 'spitzgaube'];
        const GAUBEN = ['schleppgaube', 'trapezgaube', 'flachgaube', 'giebelgaube', 'spitzgaube'];
        (list || []).forEach(o => {
            if (!MIT_OEFFNUNG.includes(o.type)) return;
            const surf = this.surfaces.get(o.surfaceId);
            if (!surf) return; // keine Phantom-Fläche -> nichts rendern
            const poly = (surf.polygon || []).map((p: any) => ({ x: p.x, y: p.y }));
            const fl = flaecheInfoAusPolygon(o.surfaceId, surf.width, surf.height, poly);
            const rect = oeffnungRechteck({ art: o.type, surfaceId: o.surfaceId, xRel: o.x, yRel: o.y, breiteM: o.width, hoeheM: o.height, tiefeM: o.depth }, fl);
            const c = [
                this.surfacePoint(surf, rect.uMinRel, rect.vMinRel, 0.05),
                this.surfacePoint(surf, rect.uMaxRel, rect.vMinRel, 0.05),
                this.surfacePoint(surf, rect.uMaxRel, rect.vMaxRel, 0.05),
                this.surfacePoint(surf, rect.uMinRel, rect.vMaxRel, 0.05),
            ];
            if (this.echteLochIds.has(o.id)) {
                // E19: ECHTES Dachhaut-Loch ist bereits in die Ziegel-Plane geschnitten (Membran scheint
                // durch). Hier nur die LAIBUNG (Seitenwände bis zur Membran) + Rand zeichnen — KEIN Prüffeld.
                const tief = 0.12;
                const cInner = [
                    this.surfacePoint(surf, rect.uMinRel, rect.vMinRel, -tief),
                    this.surfacePoint(surf, rect.uMaxRel, rect.vMinRel, -tief),
                    this.surfacePoint(surf, rect.uMaxRel, rect.vMaxRel, -tief),
                    this.surfacePoint(surf, rect.uMinRel, rect.vMaxRel, -tief),
                ];
                for (let i = 0; i < 4; i++) {
                    const j = (i + 1) % 4;
                    const wall = new THREE.BufferGeometry();
                    wall.setAttribute('position', new THREE.Float32BufferAttribute([
                        c[i].x, c[i].y, c[i].z, c[j].x, c[j].y, c[j].z, cInner[j].x, cInner[j].y, cInner[j].z,
                        c[i].x, c[i].y, c[i].z, cInner[j].x, cInner[j].y, cInner[j].z, cInner[i].x, cInner[i].y, cInner[i].z,
                    ], 3));
                    wall.computeVertexNormals();
                    this.gOeffnungen.add(new THREE.Mesh(wall, this.mats.ausschnittLaibung));
                }
                const bg = new THREE.BufferGeometry().setFromPoints([c[0], c[1], c[2], c[3], c[0]]);
                this.gOeffnungen.add(new THREE.Line(bg, this.mats.ausschnittRand));
            } else {
                // STUFE A: transparentes Prüffeld auf der Dachfläche (blau=innerhalb, amber=prüfpflichtig).
                // Gilt für Gauben, Walm/Trapez/L-T-U, überlappende Mehrlochfälle und außerhalb liegende.
                const g = new THREE.BufferGeometry();
                g.setAttribute('position', new THREE.Float32BufferAttribute([
                    c[0].x, c[0].y, c[0].z, c[1].x, c[1].y, c[1].z, c[2].x, c[2].y, c[2].z,
                    c[0].x, c[0].y, c[0].z, c[2].x, c[2].y, c[2].z, c[3].x, c[3].y, c[3].z,
                ], 3));
                g.computeVertexNormals();
                this.gOeffnungen.add(new THREE.Mesh(g, rect.innerhalb ? this.mats.oeffnung : this.mats.oeffnungWarn));
                const bg = new THREE.BufferGeometry().setFromPoints([c[0], c[1], c[2], c[3], c[0]]);
                this.gOeffnungen.add(new THREE.Line(bg, this.mats.oeffnungRand));
            }

            // (2) Gaubendachstuhl (nur Gauben): kleine Gaubensparren (Front -> Auflager über der Fläche)
            // + Auflager-/Firstlinie. Schematisch, eigene Ebene -> beim Ausblenden der Gaubenhaut sichtbar.
            if (GAUBEN.includes(o.type)) {
                // Gaubensparren: Fuß = Stirnwand-Oberkante VORN (Front auf Dach + lotrechte Fronthöhe),
                // Kopf = Anschluss HINTEN AUF dem Hauptdach (kein Höhenzuschlag) -> Sparren steigt nach
                // hinten an, beide Enden unter dem First (behebt die frühere Überhöhung über den First).
                const stuhl = new THREE.Group();
                const uM = o.x * surf.width, vM = o.y * surf.height;
                const halbU = Math.max(0.1, o.width / 2), halbV = Math.max(0.1, o.depth / 2);
                const aR = Math.max(0, Math.asin(Math.max(-1, Math.min(1, surf.vDown.y))));
                const dTiefe = 2 * halbV;
                const hFront = Math.max(0.2, Math.min(Math.max(0.3, o.height), dTiefe * Math.tan(aR) - 0.05));
                const zAxis = new THREE.Vector3(0, 0, 1);
                const hoch = new THREE.Vector3(0, hFront, 0);
                const nSp = 3;
                for (let i = 0; i <= nSp; i++) {
                    const uR = (uM - halbU + 2 * halbU * (i / nSp)) / surf.width;
                    const fuss = this.surfacePoint(surf, uR, (vM - halbV) / surf.height, 0.04).add(hoch);
                    const kopf = this.surfacePoint(surf, uR, (vM + halbV) / surf.height, 0.04);
                    const len = Math.max(0.1, fuss.distanceTo(kopf));
                    const beam = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.08, len), this.mats.gaubenStuhl);
                    beam.position.copy(fuss.clone().lerp(kopf, 0.5));
                    beam.quaternion.setFromUnitVectors(zAxis, kopf.clone().sub(fuss).normalize());
                    stuhl.add(beam);
                }
                // Gaubenpfette/Auflager am hinteren Anschluss (auf dem Hauptdach, unter First).
                const fl0 = this.surfacePoint(surf, (uM - halbU) / surf.width, (vM + halbV) / surf.height, 0.04);
                const fl1 = this.surfacePoint(surf, (uM + halbU) / surf.width, (vM + halbV) / surf.height, 0.04);
                const firstLen = Math.max(0.1, fl0.distanceTo(fl1));
                const firstBeam = new THREE.Mesh(new THREE.BoxGeometry(0.07, 0.08, firstLen), this.mats.gaubenStuhl);
                firstBeam.position.copy(fl0.clone().lerp(fl1, 0.5));
                firstBeam.quaternion.setFromUnitVectors(zAxis, fl1.clone().sub(fl0).normalize());
                stuhl.add(firstBeam);
                this.gGaubenstuhl.add(stuhl);
            }
        });
    }

    // Eingabeaufforderung 12: linienförmige Dachbauteile (Schneefang) — dünne Stange parallel zur
    // Traufe + Halter, leicht oberhalb der Dachfläche. KEINE Materialoptik/Eindeckung.
    updateLinienBauteile(list: DachLinienBauteil[]) {
        clearGroup(this.gLinien);
        (list || []).forEach((b: any) => {
            const surf = this.surfaces.get(b.surfaceId);
            if (!surf) return; // keine Phantom-Fläche -> nichts rendern
            const grp = new THREE.Group(); grp.userData = { id: b.id, linie: true };
            const q = this.surfaceQuaternion(surf);
            const centerU = (b.uStartRel + b.uEndRel) / 2;
            const laengeM = Math.max(0.2, (b.uEndRel - b.uStartRel) * surf.width);
            // Hauptstange (parallel Traufe)
            const bar = new THREE.Mesh(new THREE.BoxGeometry(laengeM, 0.05, 0.04), this.mats.metal);
            bar.position.copy(this.surfacePoint(surf, centerU, b.yRel, 0.12));
            bar.quaternion.copy(q);
            grp.add(bar);
            // Halter/Stützen in Abständen (~0,9 m)
            const n = Math.max(2, Math.min(14, Math.round(laengeM / 0.9)));
            for (let i = 0; i <= n; i++) {
                const uR = b.uStartRel + (b.uEndRel - b.uStartRel) * (i / n);
                const halter = new THREE.Mesh(new THREE.BoxGeometry(0.03, 0.12, 0.04), this.mats.metal);
                halter.position.copy(this.surfacePoint(surf, uR, b.yRel, 0.06));
                halter.quaternion.copy(q);
                grp.add(halter);
            }
            this.gLinien.add(grp);
        });
    }

    // Eingabeaufforderung 13: IDs von Aufbauten, deren Mittelpunkt außerhalb des ECHTEN Dachflächen-
    // Polygons liegt (z. B. im L/T/U-Innenwinkel oder U-Innenhof) -> prüfpflichtig, nicht still im Loch.
    aufbautenAusserhalbPolygon(list: ObstacleData[]): string[] {
        const out: string[] = [];
        (list || []).forEach(o => {
            const surf = this.surfaces.get(o.surfaceId);
            if (!surf || !surf.polygon || surf.polygon.length < 3) return;
            const pt = new THREE.Vector2(o.x * surf.width, o.y * surf.height);
            if (!this.isPointInsidePolygon(pt, surf.polygon)) out.push(o.id);
        });
        return out;
    }

    clearModules() {
        clearGroup(this.gSolar);
        clearGroup(this.gMountingRails);
        clearGroup(this.gMountingHooks);
        clearGroup(this.gMountingClamps);
        this.modules.clear();
        if(this.onModuleUpdate) this.onModuleUpdate([]);
    }

    autoLayout(surfaceConfigs: Record<string, SurfaceConfig>, obstacles: ObstacleData[], modType: any) {
        this.clearModules();
        this.surfaces.forEach((surf, surfId) => {
            const config = surfaceConfigs[surfId] || surfaceConfigs['default'];
            if(!config || !config.enabled) return;

            const { width, height } = surf;
            const isPortrait = config.orientation === 'portrait';
            const mW = isPortrait ? modType.width : modType.height;
            const mH = isPortrait ? modType.height : modType.width;
            const gap = (config.gap !== undefined ? config.gap : 2) / 100; 
            const margin = (config.margin !== undefined ? config.margin : 40) / 100;

            const usableW = Math.max(0, width - margin*2);
            const usableH = Math.max(0, height - margin*2);
            const cols = Math.floor((usableW + gap) / (mW + gap));
            const rows = Math.floor((usableH + gap) / (mH + gap));
            if (cols <= 0 || rows <= 0) return;
            
            const gridW = cols*mW + (cols-1)*gap;
            const gridH = rows*mH + (rows-1)*gap;
            const startX = margin + (usableW - gridW)/2;
            const startY = margin + (usableH - gridH)/2;

            for(let r=0; r<rows; r++) {
                for(let c=0; c<cols; c++) {
                    const cxMeters = startX + c*(mW+gap) + mW/2;
                    const cyMeters = startY + r*(mH+gap) + mH/2;
                    const cx = cxMeters / width;
                    const cy = cyMeters / height;

                    let valid = this.moduleFitsSurface(surf, cxMeters, cyMeters, mW, mH, margin);
                    if(valid && this.moduleOverlapsObstacle(surf, surfId, obstacles, cxMeters, cyMeters, mW, mH)) valid = false;

                    if(valid) {
                        const mId = crypto.randomUUID();
                        const mData: ModuleData = { id: mId, surfaceId: surfId, x: cx, y: cy, row: r, col: c, isPortrait };
                        this.addModuleMesh(mData, modType);
                    }
                }
            }
        });
        if(this.onModuleUpdate) this.onModuleUpdate(Array.from(this.modules.values()).map(m=>m.data));
    }

    addModuleMesh(d: ModuleData, modType: any) {
        const surf = this.surfaces.get(d.surfaceId);
        if(!surf) return;
        
        const mWidth = d.isPortrait ? modType.width : modType.height;
        const mHeight = d.isPortrait ? modType.height : modType.width;

        // Flachdach (waagerechte Flaeche, Normale ~ senkrecht nach oben): durch-
        // dringungsfreie Aufstaenderung mit Ballast/Auflast statt Stahl-Dachhaken.
        // Der Schraegdach-Pfad (Haken/Schiene/Klemmen) bleibt unten unveraendert.
        if (surf.vNormal && surf.vNormal.y > 0.99) {
            this.addFlatModuleMesh(d, modType, surf, mWidth, mHeight);
            return;
        }

        const hookHeight = 0.06;
        const railHeight = 0.04;
        const roofClearance = 0.006;
        const layerGap = 0.006;
        const hookCenterOffset = roofClearance + hookHeight / 2;
        const railCenterOffset = roofClearance + hookHeight + layerGap + railHeight / 2;
        const moduleCenterOffset = roofClearance + hookHeight + layerGap + railHeight + layerGap + modType.depth / 2;
        const clampCenterOffset = moduleCenterOffset + modType.depth / 2 + 0.012;

        const m = new THREE.Mesh(new THREE.BoxGeometry(mWidth, modType.depth, mHeight), this.mats.solar);
        m.position.copy(this.surfacePoint(surf, d.x, d.y, moduleCenterOffset));
        m.quaternion.copy(this.surfaceQuaternion(surf));
        if(d.rotation) m.rotateY(d.rotation); 
        m.userData = { id: d.id, moduleId: d.id };
        this.gSolar.add(m);
        this.modules.set(d.id, { mesh: m, data: d });

        [-0.25, 0.25].forEach(yPos => {
            const rMesh = new THREE.Mesh(new THREE.BoxGeometry(mWidth, railHeight, 0.04), this.mats.aluminium);
            rMesh.position.copy(this.surfacePoint(surf, d.x, d.y + ((yPos * mHeight) / surf.height), railCenterOffset));
            rMesh.quaternion.copy(m.quaternion);
            rMesh.userData = { moduleId: d.id };
            this.gMountingRails.add(rMesh);

            [-0.4, 0.4].forEach(xPos => {
                const hMesh = new THREE.Mesh(new THREE.BoxGeometry(0.03, hookHeight, 0.06), this.mats.steel);
                hMesh.position.copy(this.surfacePoint(
                    surf,
                    d.x + ((xPos * mWidth) / surf.width),
                    d.y + ((yPos * mHeight) / surf.height),
                    hookCenterOffset
                )); 
                hMesh.quaternion.copy(m.quaternion);
                hMesh.userData = { moduleId: d.id };
                this.gMountingHooks.add(hMesh);
            });
        });

        [-0.25, 0.25].forEach(yPos => {
            [-0.5, 0.5].forEach(xPos => {
                const cMesh = new THREE.Mesh(new THREE.BoxGeometry(0.04, 0.02, 0.03), this.mats.clampBlack);
                cMesh.position.copy(this.surfacePoint(surf, d.x + ((xPos * mWidth) / surf.width), d.y + ((yPos * mHeight) / surf.height), clampCenterOffset)); 
                cMesh.quaternion.copy(m.quaternion);
                cMesh.userData = { moduleId: d.id };
                this.gMountingClamps.add(cMesh);
            });
        });
    }

    addFlatModuleMesh(d: ModuleData, modType: any, surf: any, mWidth: number, mHeight: number) {
        // Generische Flachdach-Aufstaenderung: ~10° Aufstellwinkel (Richtwert),
        // durchdringungsfreie Ballast-/Auflast-Basis (Bautenschutzmatte + Wanne)
        // statt Stahl-Dachhaken. KEINE Last-/Ballast-/Produktdaten — nur Geometrie.
        const q = this.surfaceQuaternion(surf);
        const tilt = 10 * Math.PI / 180;
        const baseH = 0.04;          // Ballast-/Auflast-Basis inkl. Bautenschutzmatte
        const frontClear = 0.03;     // Vorderkante-Aufstaenderung ueber der Abdichtung
        const railH = 0.04;
        const rise = mHeight * Math.sin(tilt);   // Hoehengewinn Hinterkante durch Neigung
        const moduleCenterOffset = baseH + frontClear + railH + (rise / 2) + (modType.depth / 2);

        // 1) Modul, ~10° aufgestellt
        const m = new THREE.Mesh(new THREE.BoxGeometry(mWidth, modType.depth, mHeight), this.mats.solar);
        m.position.copy(this.surfacePoint(surf, d.x, d.y, moduleCenterOffset));
        m.quaternion.copy(q);
        m.rotateX(tilt);
        if(d.rotation) m.rotateY(d.rotation);
        m.userData = { id: d.id, moduleId: d.id };
        this.gSolar.add(m);
        this.modules.set(d.id, { mesh: m, data: d });

        const local = (lx: number, ly: number, lz: number) =>
            m.position.clone().add(new THREE.Vector3(lx, ly, lz).applyQuaternion(m.quaternion));

        // 2) Durchdringungsfreie Ballast-/Auflast-Basis (ersetzt bewusst die Dachhaken)
        const base = new THREE.Mesh(new THREE.BoxGeometry(mWidth * 1.05, baseH, mHeight * 1.05), this.mats.gravel);
        base.position.copy(this.surfacePoint(surf, d.x, d.y, baseH / 2));
        base.quaternion.copy(q);
        if(d.rotation) base.rotateY(d.rotation);
        base.userData = { moduleId: d.id };
        this.gMountingHooks.add(base);

        // 3) Tragschienen unter dem geneigten Modul (folgen der Neigung)
        [-0.25, 0.25].forEach(yPos => {
            const rMesh = new THREE.Mesh(new THREE.BoxGeometry(mWidth, railH, 0.04), this.mats.aluminium);
            rMesh.position.copy(local(0, -(modType.depth/2 + railH/2 + 0.004), yPos * mHeight));
            rMesh.quaternion.copy(m.quaternion);
            rMesh.userData = { moduleId: d.id };
            this.gMountingRails.add(rMesh);
        });

        // 4) Aufstaenderung als Dreiecks-/Trapezgestell: hinten hoch, vorne flach,
        //    auf der Ballast-Basis stehend (keine Verschraubung in die Abdichtung)
        [-0.45, 0.45].forEach(xPos => {
            const postH = baseH + frontClear + railH + rise;
            const post = new THREE.Mesh(new THREE.BoxGeometry(0.03, postH, 0.03), this.mats.aluminium);
            post.position.copy(this.surfacePoint(surf, d.x + ((xPos * mWidth) / surf.width), d.y + ((0.42 * mHeight) / surf.height), postH / 2));
            post.quaternion.copy(q);
            if(d.rotation) post.rotateY(d.rotation);
            post.userData = { moduleId: d.id };
            this.gMountingRails.add(post);

            const frontH = baseH + frontClear;
            const front = new THREE.Mesh(new THREE.BoxGeometry(0.03, frontH, 0.03), this.mats.aluminium);
            front.position.copy(this.surfacePoint(surf, d.x + ((xPos * mWidth) / surf.width), d.y - ((0.42 * mHeight) / surf.height), frontH / 2));
            front.quaternion.copy(q);
            if(d.rotation) front.rotateY(d.rotation);
            front.userData = { moduleId: d.id };
            this.gMountingRails.add(front);
        });

        // 5) Modulklemmen fixieren das Modul auf der Schiene (generisch)
        [-0.25, 0.25].forEach(yPos => {
            [-0.5, 0.5].forEach(xPos => {
                const cMesh = new THREE.Mesh(new THREE.BoxGeometry(0.04, 0.02, 0.03), this.mats.clampBlack);
                cMesh.position.copy(local(xPos * mWidth, (modType.depth/2 + 0.01), yPos * mHeight));
                cMesh.quaternion.copy(m.quaternion);
                cMesh.userData = { moduleId: d.id };
                this.gMountingClamps.add(cMesh);
            });
        });
    }

    removeModule(id: string) {
        const m = this.modules.get(id);
        if(m) {
            this.gSolar.remove(m.mesh); disposeHierarchy(m.mesh);
            this.modules.delete(id); 

            [this.gMountingRails, this.gMountingHooks, this.gMountingClamps].forEach(group => {
                const toRemove = group.children.filter(c => c.userData.moduleId === id);
                toRemove.forEach(c => {
                    group.remove(c);
                    disposeHierarchy(c);
                });
            });

            if(this.onModuleUpdate) this.onModuleUpdate(Array.from(this.modules.values()).map(mod=>mod.data)); 
        }
    }

    syncLayers(layers: LayerConfig[], globalOpacity: number) {
        const groupMap: Record<string, THREE.Group> = {
            'walls': this.gStructure, 'rafters': this.gRafters, 'membrane': this.gMembrane,
            'counterBattens': this.gCounterBattens, 'insulation': this.gInsulation,
            'battens': this.gBattens, 'tiles': this.gVisualTiles, 'obstacles': this.gObstacles,
            'linien': this.gLinien, 'oeffnungen': this.gOeffnungen, 'gaubenstuhl': this.gGaubenstuhl,
            'gaubenhaut': this.gGaubenHaut, 'wechsel': this.gWechsel, 'anschluss': this.gAnschluss,
            'verschneidung': this.gVerschneidung
        };
        layers.forEach(l => { const grp = groupMap[l.id]; if(grp) grp.visible = l.visible && !l.deleted; });

        [this.mats.tileRed, this.mats.tileDark, this.mats.gravel, this.mats.metal].forEach((m: any) => {
            if(m) { m.transparent = globalOpacity < 1; m.opacity = globalOpacity; m.needsUpdate = true; }
        });
    }

    animate = () => {
        this.animationFrameId = requestAnimationFrame(this.animate);
        this.controls.update();

        if (Math.abs(this.camera.zoom - this.targetZoom) > 0.001) {
            this.camera.zoom += (this.targetZoom - this.camera.zoom) * 0.1;
            this.camera.updateProjectionMatrix();
        }
        this.renderer.render(this.scene, this.camera);
    }
    
    resize(w:number, h:number) { 
        if (w <= 0 || h <= 0) return;
        this.camera.aspect = w/h; 
        this.updateZoom();
        this.camera.updateProjectionMatrix(); 
        this.renderer.setSize(w,h); 
    }
}

export default function DachplanerProPage() {
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const engineRef = useRef<RoofEngine | null>(null);
    const divRef = useRef<HTMLDivElement>(null);
    const abbundCanvasRef = useRef<HTMLCanvasElement>(null);

    // PHASE 0 (Store-Brücke): Geometrie (build), Eindeckungsart (cover) und gewähltes
    // DB-Produkt (selectedTile/selectedCovering) sind jetzt der roofConfigStore.roof-Slice
    // (einzige Wahrheit). useRoofSlice re-rendert NUR bei roof-Änderung (referenz-stabil).
    const { build, additionalRoofs, cover, selectedTile, selectedCovering } = useRoofSlice();
    const setBuild = roofConfigStore.setBuild;
    const setCover = roofConfigStore.setCover;
    const setSelectedTile = roofConfigStore.setSelectedTile;
    const setSelectedCovering = roofConfigStore.setSelectedCovering;
    const setAdditionalRoofs = roofConfigStore.setAdditionalRoofs;

    const [view, setView] = useState<ViewMode>('construct');
    const [tool, setTool] = useState<InteractionTool>('select');
    const [isLeftSidebarOpen, setIsLeftSidebarOpen] = useState(true);
    const [isRightSidebarOpen, setIsRightSidebarOpen] = useState(false);
    const [historyTab, setHistoryTab] = useState<'dach' | 'aufbauten'>('dach');

    const [abbundData, setAbbundData] = useState<any>(null);
    const [isHolzlisteOpen, setIsHolzlisteOpen] = useState(false);

    // Dachform-Vorlagenbibliothek (additive UI, lokaler State) — minimal gehalten.
    const [vorlagenOffen, setVorlagenOffen] = useState(true);
    const [vorlagenQuery, setVorlagenQuery] = useState('');
    const [vorlagenStatusFilter, setVorlagenStatusFilter] = useState<'' | VorlagenStatus>('');
    const [vorlagenKategorieFilter, setVorlagenKategorieFilter] = useState<'' | VorlagenRoofCategory>('');
    // Additive Zusatzfilter (Dachform / Merkmal) für die erweiterte Bibliothek.
    const [vorlagenFormFilter, setVorlagenFormFilter] = useState<string>('');
    const [vorlagenMerkmalFilter, setVorlagenMerkmalFilter] = useState<string>('');
    const [vorlagenDetailId, setVorlagenDetailId] = useState<string>('');

    const _buildDefaultUnused = ({  // PHASE 0: echte Quelle ist roofConfigStore.roof.build (useRoofSlice) 
        category: 'pitched', shape: 'sattel', 
        length: 10, width: 8, height: 5, 
        pitch: 35, attika: 0.3, 
        overhang: 0.5, overhangGable: 0.3,
        lengthB: 4, widthB: 4,
        layerSpread: 0,
        rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18,  
        battenDist: 34      
    });

    // additionalRoofs -> roofConfigStore.roof (Phase 0 Write-Through)
    const [globalOpacity, setGlobalOpacity] = useState(1);
    const [layers, setLayers] = useState<LayerConfig[]>([
        { id: 'walls', name: 'Mauerwerk & Ringanker', visible: true, deleted: false, isSystem: true, category: 'dach' },
        { id: 'rafters', name: 'Dachstuhl (Sparren/Pfetten)', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'membrane', name: 'Unterspannbahn / Schalung', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'counterBattens', name: 'Konterlattung', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'insulation', name: 'Aufsparrendämmung', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'battens', name: 'Traglattung (Eindeckung)', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'tiles', name: 'Dacheindeckung', visible: true, deleted: false, isSystem: true, category: 'dach' },
        { id: 'obstacles', name: 'Dachaufbauten', visible: true, deleted: false, isSystem: true, category: 'dach' },
        { id: 'linien', name: 'Schneefang / Linienbauteile', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'oeffnungen', name: 'Dachöffnungen / Ausschnitte', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'gaubenstuhl', name: 'Gaubendachstuhl (schematisch)', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'gaubenhaut', name: 'Gaubendach (Eindeckung)', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'wechsel', name: 'Wechselhölzer / Auswechslung', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'anschluss', name: 'Gaubenanschlüsse / Dachanschlüsse', visible: true, deleted: false, isSystem: false, category: 'dach' },
        { id: 'verschneidung', name: 'Kehlen / Grate (Dachverschneidung)', visible: true, deleted: false, isSystem: false, category: 'dach' }
    ]);
    
    // cover -> roofConfigStore.roof (Phase 0); Reset von selectedTile/Covering noch via Effekt unten
    // Gewähltes echtes Ziegelmodell bzw. Flachdach-/Metall-Eindeckung (Produktdatenbank) — überschreibt Pauschalwerte.
    // selectedTile/selectedCovering -> roofConfigStore.roof (Phase 0)
    // Reset bei Eindeckungswechsel ist jetzt in roofConfigStore.setCover gefaltet
    // (eine Emission statt Effekt-Nachlauf) — kein transienter Stale-Frame mehr.
    const [activeSurface, setActiveSurface] = useState<string>('');
    const [availableSurfaces, setAvailableSurfaces] = useState<{id: string, name: string, width?: number, height?: number, type?: string, area?: number}[]>([]);

    const [surfaceConfigs, setSurfaceConfigs] = useState<Record<string, SurfaceConfig>>({
        'default': { enabled: true, orientation: 'portrait', gap: 2, margin: 40 }
    });

    // obstacles ist jetzt ein roofConfigStore-Slice (Write-Through, SSOT analog roof).
    // useObstaclesSlice re-rendert NUR bei obstacles-Änderung (referenz-stabil); setObstacles
    // ist der store-gebundene Setter (Wert- ODER Updater-Form) — alle bisherigen Aufrufformen
    // (Funktions-Updater in Engine-Callbacks, Wert-Form bei Add/Delete) bleiben unverändert.
    const obstacles = useObstaclesSlice();
    const setObstacles = roofConfigStore.setObstacles;
    // Eingabeaufforderung 12: linienförmige Dachbauteile (Schneefang) + PV-Sperrzonen-Warnung.
    const [linienBauteile, setLinienBauteile] = useState<any[]>([]);
    const [linienPvWarnung, setLinienPvWarnung] = useState(false);
    // Reparatur 3: IDs der Aufbauten, die nach einer Geometrieänderung keiner gültigen
    // Dachfläche mehr zugeordnet sind (prüfpflichtig — nicht still gelöscht/verschoben).
    const [aufbautenPruefIds, setAufbautenPruefIds] = useState<string[]>([]);
    const [modules, setModules] = useState<ModuleData[]>([]);
    // Reparatur 2: true, wenn eine vorhandene Belegung durch eine Geometrieänderung
    // entfernt wurde und neu geprüft/belegt werden muss (verhindert stillen Verlust).
    const [belegungPruefpflichtig, setBelegungPruefpflichtig] = useState(false);
    const [selectedModuleIndex, setSelectedModuleIndex] = useState(2); 
    const [holzliste, setHolzliste] = useState<any[]>([]);
    const [edgeTopologyConfigs, setEdgeTopologyConfigs] = useState<EdgeTopologyConfig[]>(getDefaultEdgeTopologyConfigs(build, buildTopologyPolygon(build).length));

    // Dachform-Vorlagen: live gefiltert (Suche UND Status/Kategorie). Reines Modul, kein Engine-Zugriff.
    const vorlagenGefiltert = useMemo(() => {
        let list = sucheVorlagen(DACHFORM_VORLAGEN, vorlagenQuery);
        list = filterVorlagen(list, {
            status: vorlagenStatusFilter || undefined,
            category: vorlagenKategorieFilter || undefined,
        });
        // Additiv: Dachform-Filter (Grundform) — rein lesend, ändert die Daten nicht.
        if (vorlagenFormFilter) {
            list = list.filter((v) => {
                const sk = v.geometrie.shapeKey;
                if (vorlagenFormFilter === 'sattel') return sk === 'sattel';
                if (vorlagenFormFilter === 'pult') return sk === 'pult';
                if (vorlagenFormFilter === 'walm') return sk === 'walm' || sk === 'krueppelwalm';
                if (vorlagenFormFilter === 'flach') return v.geometrie.category === 'flat';
                if (vorlagenFormFilter === 'sonder') return !['sattel', 'pult', 'walm', 'rect'].includes(sk as string);
                return true;
            });
        }
        // Additiv: Merkmal-Filter (PV / Gauben / Aufbauten / Gebäudetyp / L-T-U) über Schlagworte.
        if (vorlagenMerkmalFilter) {
            list = list.filter((v) => {
                const tags = v.schlagworte.join(' ').toLowerCase();
                if (vorlagenMerkmalFilter === 'pv') return tags.includes('pv') || tags.includes('photovoltaik');
                if (vorlagenMerkmalFilter === 'gaube') return tags.includes('gaube');
                if (vorlagenMerkmalFilter === 'aufbau') return tags.includes('aufbau') || tags.includes('kamin') || tags.includes('dachfenster') || tags.includes('sperrfläche') || tags.includes('lüfter') || tags.includes('sat') || tags.includes('schneefang');
                if (vorlagenMerkmalFilter === 'gebaeude') return tags.includes('gebäudetyp') || tags.includes('efh') || tags.includes('mfh') || tags.includes('bungalow') || tags.includes('halle') || tags.includes('garage') || tags.includes('carport') || tags.includes('scheune') || tags.includes('werkstatt') || tags.includes('büro');
                if (vorlagenMerkmalFilter === 'ltu') return tags.includes('l-form') || tags.includes('t-form') || tags.includes('u-form') || tags.includes('mehrkörper') || tags.includes('mehrkoerper');
                return true;
            });
        }
        return list;
    }, [vorlagenQuery, vorlagenStatusFilter, vorlagenKategorieFilter, vorlagenFormFilter, vorlagenMerkmalFilter]);

    // Anwenden setzt AUSSCHLIESSLICH die Dachform/Geometrie (setBuild) -> der vorhandene useEffect
    // (deps [build, cover, additionalRoofs]) markiert vorhandene Belegung/Aufbauten als prüfpflichtig
    // (Reparatur 2/3). KEIN Direktaufruf der Engine.
    // KORREKTUR (deckungsneutral): KEIN setCover! Die Dacheindeckung/Materialauswahl bleibt
    // unverändert; das Deckmaterial wird ausschließlich über die separate Produktauswahl gewählt.
    const handleVorlageAnwenden = (v: DachformVorlage) => {
        const r = applyVorlage(v, build);
        if (!r.ok || !r.build) return; // geplant/nicht baubar -> blockiert (Button ist ohnehin disabled)
        setBuild(r.build); // nur Geometrie — Eindeckung bleibt erhalten
        // Standard-Aufbauten automatisch als Obstacles ergänzen (Kamin/Dachfenster/Lüfter/Sat/Gaube).
        // WICHTIG: bestehende Aufbauten NICHT löschen -> nur anhängen. KEINE Eindeckung/Material.
        // surfaceId ist bereits die echte Engine-Fläche der Zielform; nach dem Geometrie-Rebuild
        // greift Reparatur 3 (Flächen-/Prüfpflicht) und Reparatur 9/10 (Auswechslung/Sparren-Trennung).
        if (r.aufbauten && r.aufbauten.length > 0) {
            setObstacles(prev => {
                // Idempotenz: gleiche Vorlage zweimal anwenden erzeugt KEINE exakten Duplikate.
                // Bestehende Aufbauten werden NIE gelöscht/ersetzt — nur fehlende ergänzt.
                const vorhanden = (a: { surfaceId: string; art: string; xRel: number; yRel: number }) =>
                    prev.some(o => o.surfaceId === a.surfaceId && o.type === a.art
                        && Math.abs(o.x - a.xRel) < 0.001 && Math.abs(o.y - a.yRel) < 0.001);
                const neueObstacles: ObstacleData[] = r.aufbauten!
                    .filter(a => !vorhanden(a))
                    .map(a => ({
                        id: crypto.randomUUID(),
                        surfaceId: a.surfaceId,
                        type: a.art as ObstacleType,
                        x: a.xRel, y: a.yRel,
                        width: a.breiteM, height: a.hoeheM, depth: a.tiefeM,
                        pitch: a.pitchGrad, rotation: 0,
                    }));
                return neueObstacles.length ? [...prev, ...neueObstacles] : prev;
            });
        }
        // Eingabeaufforderung 12: Schneefang als linienförmiges Dachbauteil ergänzen (nicht löschen).
        if (r.linienBauteile && r.linienBauteile.length > 0) {
            setLinienBauteile(prev => {
                const vorhanden = (b: DachLinienBauteil) =>
                    prev.some(l => l.surfaceId === b.surfaceId && l.art === b.art && Math.abs(l.yRel - b.yRel) < 0.001);
                const neu = r.linienBauteile!.filter(b => !vorhanden(b)).map(b => ({ id: crypto.randomUUID(), ...b }));
                return neu.length ? [...prev, ...neu] : prev;
            });
            // PV-Sperrzone ist vorbereitet: vorhandene Belegung gegen den Schneefang prüfen
            // (KEINE automatische Modulverschiebung — Belegung bleibt erhalten, nur Warnhinweis).
            if (modules.length > 0 && r.linienBauteile.some(b => b.pvSperrbereich)) setLinienPvWarnung(true);
        }
    };

    useEffect(() => {
        if(!canvasRef.current) return;
        const engine = new RoofEngine(canvasRef.current);
        engineRef.current = engine;
        
        const resize = () => { if(divRef.current) engine.resize(divRef.current.clientWidth, divRef.current.clientHeight); };
        window.addEventListener('resize', resize); 
        resize();
        
        engine.onObstacleMove = (id, surfId, x, y) => { setObstacles(prev => prev.map(o => o.id===id ? {...o, surfaceId: surfId, x, y} : o)); };
        engine.onObstacleRotate = (id) => { setObstacles(prev => prev.map(o => o.id === id ? { ...o, rotation: (o.rotation || 0) + Math.PI / 2 } : o)); };
        engine.onObstacleDelete = (id) => { setObstacles(prev => prev.filter(o => o.id !== id)); };
        engine.onSurfaceSelect = (surfId) => { setActiveSurface(surfId); };
        engine.onAbbundSelect = (data) => { setAbbundData(data); };
        engine.onModuleUpdate = (mods) => { setModules([...mods]); };

        engine.onSurfacesUpdated = (surfaces) => {
            setAvailableSurfaces(surfaces);
            setActiveSurface(prev => surfaces.some(s => s.id === prev) ? prev : (surfaces[0]?.id || ''));
        };

        return () => { window.removeEventListener('resize', resize); engine.destroy(); };
    }, []);

    useEffect(() => {
        const mainElement = divRef.current;
        if (!mainElement) return;
        let frameId: number;
        const resizeObserver = new ResizeObserver((entries) => {
            if (frameId) cancelAnimationFrame(frameId);
            frameId = requestAnimationFrame(() => {
                if (engineRef.current && entries.length > 0) {
                    const { width, height } = entries[0].contentRect;
                    if (width > 0 && height > 0) engineRef.current.resize(width, height);
                }
            });
        });
        resizeObserver.observe(mainElement);
        return () => { resizeObserver.disconnect(); if (frameId) cancelAnimationFrame(frameId); };
    }, []);

    // Reparatur 5: Werkstattplan-Modus (tool='abbund') sichert beim Eintritt den
    // vorherigen Ebenen-/Transparenzzustand EINMALIG und stellt ihn beim Verlassen
    // zuverlässig wieder her — kein dauerhaftes Ausblenden von Dachhaut/Lattung mehr.
    // prevToolRef erkennt Ein-/Austritt; der Snapshot wird NICHT bei jedem Render überschrieben.
    const prevToolRef = useRef<InteractionTool>(tool);
    const werkstattSnapshotRef = useRef<{ layers: LayerConfig[]; globalOpacity: number } | null>(null);
    useEffect(() => {
        const prevTool = prevToolRef.current;
        if(engineRef.current) {
            engineRef.current.activeTool = tool;
            if(istWerkstattplanEintritt(prevTool, tool)) {
                // vorherigen Zustand einmalig sichern, dann nur Tragwerk zeigen
                werkstattSnapshotRef.current = { layers: layers.map(l => ({...l})), globalOpacity };
                setLayers(werkstattplanAnsicht(layers));
                setGlobalOpacity(0.2);
            } else if(istWerkstattplanAustritt(prevTool, tool)) {
                // echten vorherigen Zustand wiederherstellen (kein fester Standard)
                const snap = werkstattSnapshotRef.current;
                if(snap) {
                    setLayers(snap.layers.map(l => ({...l})));
                    setGlobalOpacity(snap.globalOpacity);
                    werkstattSnapshotRef.current = null;
                }
            }
        }
        prevToolRef.current = tool;
    }, [tool]);

    useEffect(() => {
        if(engineRef.current) {
            // Reparatur 2: updateBuilding leert die Belegung. War eine Belegung
            // vorhanden, wird sie als prüfpflichtig markiert (sichtbare Warnung)
            // statt still zu verschwinden — der Nutzer muss bewusst neu belegen.
            if(geometrieMachtPruefpflichtig(modules.length)) setBelegungPruefpflichtig(true);
            // Reparatur 10: aktuelle Aufbauten an updateBuilding übergeben -> betroffene Sparren
            // werden beim Geometrie-Rebuild real getrennt. (Effekt-Deps unverändert [build,cover,
            // additionalRoofs]; Aufbau-Bewegung löst KEINE Rebuild/Belegungsleerung aus.)
            engineRef.current.updateBuilding(build, cover, additionalRoofs, obstacles);
            // Reparatur 3: Aufbauten direkt nach updateBuilding nachziehen, damit sie
            // nicht an alten Welt-Koordinaten schweben (relative x,y werden neu auf die
            // aktuelle Flächengeometrie abgebildet). Aufbauten ohne gültige Fläche
            // (z.B. nach Dachformwechsel) werden als prüfpflichtig markiert, NICHT gelöscht.
            engineRef.current.updateObstacles(obstacles);
            engineRef.current.updateLinienBauteile(linienBauteile); // Schneefang nach Rebuild nachziehen
            engineRef.current.syncLayers(layers, globalOpacity);
            setHolzliste([...engineRef.current.holzliste]);
            // Reparatur 3 (fehlende Fläche) + E13 (außerhalb des L/T/U-Polygons) zusammen prüfpflichtig.
            const fehlt = aufbautenOhneFlaeche(obstacles, Array.from(engineRef.current.surfaces.keys())).pruefpflichtigIds;
            const ausserhalb = engineRef.current.aufbautenAusserhalbPolygon(obstacles);
            setAufbautenPruefIds([...new Set([...fehlt, ...ausserhalb])]);
        }
    }, [build, cover, additionalRoofs]);

    // 3D-Tint: gewählte Ziegelfarbe (hex aus den Modellfarben) aufs geteilte Ziegel-Material legen.
    // Läuft nach dem Rebuild-Effekt -> getRoofMat() liefert dasselbe mats.tileRed, das hier getintet wird.
    useEffect(() => {
        if (!engineRef.current) return;
        const hex = (cover === 'ziegel' && selectedTile?.color && Array.isArray(selectedTile.farben))
            ? (selectedTile.farben.find((f: any) => f.name === selectedTile.color)?.hex ?? null)
            : null;
        engineRef.current.applyTileColor(hex);
    }, [cover, selectedTile]);

    // 3D-Tint Metall/Schiefer: gewählten Tonwert (hex aus covering_colors) aufs Material legen.
    useEffect(() => {
        if (!engineRef.current) return;
        const hex = ((cover === 'trapezblech' || cover === 'schiefer') && selectedCovering?.color && Array.isArray(selectedCovering.farben))
            ? (selectedCovering.farben.find((f: any) => f.name === selectedCovering.color)?.hex ?? null)
            : null;
        engineRef.current.applyCoveringColor(hex);
    }, [cover, selectedCovering]);

    useEffect(() => {
        const polygon = buildTopologyPolygon(build);
        setEdgeTopologyConfigs(prev => {
            const defaults = getDefaultEdgeTopologyConfigs(build, polygon.length);
            if (prev.length !== polygon.length) return defaults;
            return prev.map((cfg, index) => ({
                ...cfg,
                label: defaults[index]?.label || cfg.label,
                pitch: cfg.type === 'GIEBEL' ? 0 : cfg.pitch,
            }));
        });
    }, [build.shape, build.category, build.length, build.width, build.lengthB, build.widthB, build.pitch]);

    useEffect(() => {
        if(engineRef.current) engineRef.current.syncLayers(layers, globalOpacity);
    }, [layers, globalOpacity]);

    useEffect(() => {
        if(engineRef.current) {
            engineRef.current.updateObstacles(obstacles);
            // Reparatur 3: Prüfpflicht-Liste konsistent halten — z.B. wenn ein Aufbau
            // bewusst entfernt oder per Drag auf eine gültige Fläche verschoben wurde.
            // E13: zusätzlich Aufbauten außerhalb des L/T/U-Polygons (Innenwinkel/Innenhof) markieren.
            const fehlt = aufbautenOhneFlaeche(obstacles, Array.from(engineRef.current.surfaces.keys())).pruefpflichtigIds;
            const ausserhalb = engineRef.current.aufbautenAusserhalbPolygon(obstacles);
            setAufbautenPruefIds([...new Set([...fehlt, ...ausserhalb])]);
        }
    }, [obstacles]);

    // Eingabeaufforderung 12: Schneefang-/Linienbauteile in 3D nachziehen (ohne Geometrie-Rebuild).
    useEffect(() => {
        if(engineRef.current) engineRef.current.updateLinienBauteile(linienBauteile);
    }, [linienBauteile]);

    // Reparatur 4: aktive Dachfläche im 3D dezent hervorheben. Läuft auch nach einem
    // Geometrie-Rebuild (updateBuilding leert gHighlight) erneut und wendet das
    // Highlight auf die — ggf. von onSurfacesUpdated zurückgesetzte — aktive Fläche an.
    useEffect(() => {
        if(engineRef.current) engineRef.current.highlightSurface(activeSurface || null);
    }, [activeSurface, build, cover, additionalRoofs]);

    useEffect(() => {
        if (!abbundData || !abbundCanvasRef.current) return;
        const canvas = abbundCanvasRef.current;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        const d = abbundData;
        const prop = d.laenge / Math.max(0.1, d.hoehe);
        let rh = 60; let rw = rh * prop;
        if(rw > canvas.width-100) { const s = (canvas.width-100)/rw; rw*=s; rh*=s; }
        
        const x = (canvas.width - rw)/2; const y = (canvas.height - rh)/2;
        
        ctx.fillStyle = '#cc9966'; ctx.strokeStyle = '#8b5a2b'; ctx.lineWidth = 2;
        ctx.fillRect(x,y,rw,rh); ctx.strokeRect(x,y,rw,rh);

        if(d.kervenAbstand && d.kervenTiefe) {
            const kX = x + (d.kervenAbstand/d.laenge)*rw;
            const kW = (0.14/d.laenge)*rw;
            const kH = (d.kervenTiefe/d.hoehe)*rh;
            ctx.fillStyle = '#e2e8f0'; 
            ctx.beginPath(); ctx.moveTo(kX, y+rh); ctx.lineTo(kX, y+rh-kH); ctx.lineTo(kX+kW, y+rh); ctx.fill(); ctx.stroke();
        }
    }, [abbundData]);

    // Reparatur 2: bewusste Neubelegung -> Belegung gilt wieder als geprüft/gültig.
    const handleLayout = () => { if(engineRef.current) { engineRef.current.autoLayout(surfaceConfigs, obstacles, MODULE_TYPES[selectedModuleIndex]); setBelegungPruefpflichtig(false); } };
    // Reparatur 2: bewusstes Entfernen -> eindeutiger Zustand „keine Belegung", keine Warnung.
    const handleClear = () => { if(engineRef.current) { engineRef.current.clearModules(); setBelegungPruefpflichtig(false); } };

    const updateSurfaceConfig = (key: keyof SurfaceConfig, val: any) => {
        const targetSurface = activeSurface || 'default';
        const baseConfig = surfaceConfigs[targetSurface] || surfaceConfigs['default'];
        const newConfigs = { ...surfaceConfigs, [targetSurface]: { ...baseConfig, [key]: val } };
        setSurfaceConfigs(newConfigs);
        if(engineRef.current && modules.length > 0) { engineRef.current.autoLayout(newConfigs, obstacles, MODULE_TYPES[selectedModuleIndex]); setBelegungPruefpflichtig(false); }
    };

    const addObs = (type: ObstacleType) => {
        const surf = activeSurface || availableSurfaces[0]?.id || 'main_S';
        const dim = type==='chimney'?{w:0.6,h:0.6,d:0.6,p:0}:
                    type==='window'?{w:0.78,h:1.18,d:0.1,p:0}:
                    type==='lichtkuppel'?{w:1.0,h:1.0,d:0.3,p:0}:
                    type==='sat'?{w:0.8,h:0.8,d:0.8,p:0}:
                    type==='schleppgaube'?{w:2.5,h:1.5,d:2.5,p:15}:
                    type==='trapezgaube'?{w:3.0,h:1.5,d:2.5,p:15}:
                    type==='flachgaube'?{w:2.5,h:1.5,d:2.5,p:3}:
                    type==='giebelgaube'?{w:2.5,h:1.5,d:2.5,p:35}:
                    type==='spitzgaube'?{w:2.0,h:0.0,d:2.0,p:45}:
                    {w:0.2,h:0.2,d:0.2,p:0};
        setObstacles([...obstacles, { id: crypto.randomUUID(), surfaceId: surf, type, x:0.5, y:0.5, width:dim.w, height:dim.h, depth:dim.d, pitch:dim.p, rotation: 0 }]);
    };

    // Eingabeaufforderung 12: Schneefang manuell als linienförmiges Bauteil auf der aktiven Fläche
    // setzen — flächenabhängig aus der ECHTEN Engine-Fläche (Polygon), keine Phantom-Fläche.
    const addSchneefang = () => {
        const surfId = activeSurface || availableSurfaces[0]?.id;
        const surf = surfId && engineRef.current ? engineRef.current.surfaces.get(surfId) : null;
        if (!surf) return;
        const poly = (surf.polygon || []).map((p: any) => ({ x: p.x, y: p.y }));
        const erg = platziereSchneefang(surfId, flaecheInfoAusPolygon(surfId, surf.width, surf.height, poly));
        if (!erg.bauteil) return; // zu schmal/unsicher -> nicht falsch setzen
        setLinienBauteile(prev => [...prev, { id: crypto.randomUUID(), ...erg.bauteil }]);
        if (modules.length > 0 && erg.bauteil.pvSperrbereich) setLinienPvWarnung(true);
    };

    const updateObsDim = (id: string, key: keyof ObstacleData, val: number) => {
        if(isNaN(val)) return;
        // Verhaltensgleich zum bisherigen prev.map(...): patcht genau den getroffenen
        // Aufbau, alle anderen behalten ihre Referenz (Write-Through in den Store).
        roofConfigStore.updateObstacle(id, { [key]: val });
    };

    const handleToggleLayerVisibility = (id: string) => { setLayers(layers.map(l => l.id === id ? { ...l, visible: !l.visible } : l)); };
    const handleDeleteLayer = (id: string) => { setLayers(layers.map(l => l.id === id ? { ...l, deleted: true, visible: false } : l)); };
    const handleRenameLayer = (id: string, newName: string) => { setLayers(layers.map(l => l.id === id ? { ...l, name: newName } : l)); };

    const exportDachSVG = () => {
        const B = build.width * 50; const T = build.length * 50;
        const uT = build.overhang * 50; const uO = build.overhangGable * 50;
        const viewW = B + 2*uO + 100; const viewH = T + 2*uT + 100;
        const cX = viewW/2; const cY = viewH/2;
        let svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${viewW} ${viewH}" style="background-color:white;">`;
        svg += `<style> .kontur { fill: none; stroke: black; stroke-width: 3; } .first { stroke: red; stroke-width: 3; stroke-dasharray: 10,5; } .grat { stroke: blue; stroke-width: 2; } text { font-family: sans-serif; font-size: 14px; fill: #333; } </style>`;
        const x0 = cX - B/2 - uO; const y0 = cY - T/2 - uT;
        const w = B + 2*uO; const h = T + 2*uT;
        svg += `<rect x="${x0}" y="${y0}" width="${w}" height="${h}" class="kontur" />`;
        if(build.shape === 'sattel') {
            svg += `<line x1="${cX}" y1="${y0}" x2="${cX}" y2="${y0+h}" class="first" />`;
            svg += `<text x="${cX+10}" y="${cY}">Firstlinie</text>`;
        }
        svg += `</svg>`;
        const blob = new Blob([svg], {type: "image/svg+xml;charset=utf-8"});
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a"); link.href = url; link.download = `dachausmittlung_${build.shape}.svg`;
        document.body.appendChild(link); link.click(); document.body.removeChild(link);
    };

    const pvStats = useMemo(() => {
        const count = modules.length;
        const mod = MODULE_TYPES[selectedModuleIndex];
        if(!mod) return { count: 0, kwp: 0, weight: 0, price: 0 };
        return {
            count,
            kwp: (count * mod.watts) / 1000,
            weight: count * mod.weight,
            price: count * mod.price
        };
    }, [modules, selectedModuleIndex]);

    const activeSurfaceConfig = surfaceConfigs[activeSurface] || surfaceConfigs['default'];

    // Reparatur 9: je gültigem Aufbau die Sparren-Öffnungs-Verschneidung (Auswechslung)
    // rein geometrisch bestimmen. Nutzt Öffnungsbreite (u) + Öffnungshöhe (v) — NICHT die
    // Aufbau-Tiefe (depth). Nur Aufbauten auf existierender, geschrägter Dachfläche.
    const auswechslungInfos = useMemo(() => {
        const infos: Record<string, any> = {};
        const m = dachstuhlMasseM(build);
        const eng = engineRef.current;
        if (!eng || build.category !== 'pitched') return infos;
        obstacles.forEach(o => {
            const surf = eng.surfaces.get(o.surfaceId);
            if (!surf || !Number.isFinite(surf.width) || !Number.isFinite(surf.height)) return;
            infos[o.id] = analysiereAuswechslung(
                { breiteM: surf.width, hoeheM: surf.height },
                { xRel: o.x, yRel: o.y, breiteM: o.width, hoeheM: o.height },
                m.rafterDist, { rafterWidthM: m.rafterW },
            );
        });
        return infos;
    }, [obstacles, build, cover, availableSurfaces]);

    const { bom, workPlan, totals } = useMemo(() => {
        let totalRoofArea = 0; let totalRafterLength = 0; let totalBattenLength = 0;
        // Reparatur 1: dieselbe geklemmte Quelle wie die Geometrie-Engine — kein
        // Teilen durch 0/NaN mehr (rafterDist/battenDist >= 5 cm garantiert).
        const bMasse = dachstuhlMasseM(build);
        if (engineRef.current) {
            engineRef.current.surfaces.forEach((s) => {
                if (s.type === 'poly' || s.type === 'rect') {
                    const sw = Number.isFinite(s.width) ? s.width : 0;
                    const sh = Number.isFinite(s.height) ? s.height : 0;
                    // Reparatur 6: echte (geneigte) Polygonfläche statt Rechteck-Rahmen.
                    // width*height nur als begründeter Ersatz, falls kein gültiges Polygon
                    // vorliegt. Für rechteckige Flächen (Sattel) identisch; für Walm/L/T kleiner.
                    const polyArea = polygonFlaecheM2(s.polygon as any);
                    totalRoofArea += polyArea > 0 ? polyArea : (sw * sh);
                    const rCount = s.numRafters || Math.floor(sw / bMasse.rafterDist) + 1;
                    totalRafterLength += rCount * sh;
                    const bCount = Math.floor(sh / bMasse.battenDist) + 1;
                    totalBattenLength += bCount * sw;
                }
            });
        }
        // Reparatur 7: echte Stab-Längen aus der bereits gezeichneten Geometrie nutzen
        // (eine Mengenwahrheit). Die obige Schleife liefert nur den Rahmen-Ersatz, falls
        // die Engine (noch) keine Holzliste hat. Die Engine clippt Sparren/Latten an Walm/
        // L/T -> echte Längen sind dort kürzer als der Rechteck-Rahmen.
        let konterLength = totalRafterLength;
        if (engineRef.current && Array.isArray(engineRef.current.holzliste) && engineRef.current.holzliste.length > 0) {
            const hm = holzMengenAusListe(engineRef.current.holzliste);
            if (hm.sparrenLaenge > 0) totalRafterLength = hm.sparrenLaenge;
            if (hm.lattenLaenge > 0) totalBattenLength = hm.lattenLaenge;
            konterLength = hm.konterLaenge > 0 ? hm.konterLaenge : totalRafterLength;
        }
        // ---- FLACHDACH: KEINE Schraegdach-Unterkonstruktion (Sparren/Konter-/Trag-
        //      lattung/Unterspannbahn/Ziegel) erfinden. Generische Flachdach-Posten
        //      aus der Geometrie; keine Hersteller-/Last-/Produktdaten. -----------
        if (build.category === 'flat') {
            if (totalRoofArea === 0) totalRoofArea = Math.max(0.1, build.length) * Math.max(0.1, build.width);
            const umfang = 2 * (Math.max(0.1, build.length) + Math.max(0.1, build.width)); // Randlaenge fuer Attika/Randabschluss
            const sealNames: Record<string, string> = {
                bitumen: 'Bitumen-Schweißbahn (2-lagig)',
                kunststoff: 'Kunststoff-Dichtungsbahn (FPO/PVC)',
                gruendach: 'Gründach-Aufbau (extensiv)',
                kies: 'Kiesschüttung (Auflast)',
            };
            const sealName = sealNames[cover] || 'Dachabdichtung (Bahn)';
            const fItems = [];
            let fp = 1;
            fItems.push({ pos: fp++, n: 'Dampfsperre', q: Math.ceil(totalRoofArea * 1.1), u: 'm²', tBase: TIME_VARS.MEMBRANE_M2, tTotal: totalRoofArea * TIME_VARS.MEMBRANE_M2, isFlat: true });
            fItems.push({ pos: fp++, n: 'Wärmedämmung (Gefälledämmung)', q: Math.ceil(totalRoofArea * 1.05), u: 'm²', tBase: TIME_VARS.INSULATION_M2, tTotal: totalRoofArea * TIME_VARS.INSULATION_M2, isFlat: true });
            fItems.push({ pos: fp++, n: sealName, q: Math.ceil(totalRoofArea * 1.1), u: 'm²', tBase: TIME_VARS.MEMBRANE_M2, tTotal: totalRoofArea * TIME_VARS.MEMBRANE_M2, isFlat: true });
            fItems.push({ pos: fp++, n: 'Attika-/Randabschluss', q: umfang.toFixed(1), u: 'lfm', tBase: 0, tTotal: umfang * TIME_VARS.BATTEN_M, isFlat: true });
            fItems.push({ pos: fp++, n: 'Dachgully + Notüberlauf', q: Math.max(1, Math.ceil(totalRoofArea / 200)), u: 'Stk', tBase: 0, tTotal: 0, isFlat: true });
            const fSteps = [
                { idx: 1, t: 'Gerüstbau', m: totalRoofArea * TIME_VARS.SCAFFOLD_M2 + 60, d: 'Gerüstaufbau & Sicherung', isFlat: true },
                { idx: 2, t: 'Dampfsperre & Dämmung', m: totalRoofArea * (TIME_VARS.MEMBRANE_M2 + TIME_VARS.INSULATION_M2), d: 'Dampfsperre und Gefälledämmung verlegen', isFlat: true },
                { idx: 3, t: 'Abdichtung', m: totalRoofArea * TIME_VARS.MEMBRANE_M2 * 1.5, d: 'Dachabdichtung verlegen/verschweißen, Anschlüsse herstellen', isFlat: true },
                { idx: 4, t: 'Randabschluss & Entwässerung', m: umfang * TIME_VARS.BATTEN_M + 120, d: 'Attika-Abdeckung, Dachgully & Notüberlauf', isFlat: true },
            ];
            const fTime = fSteps.reduce((acc, step) => acc + step.m, 0);
            // Gewicht nur aus generischer, vorhandener Materialeigenschaft (Bitumen);
            // fuer Gruendach/Kies KEINE Lastdaten erfinden -> 0 (unbekannt).
            // Gewählte echte Eindeckung (Bahn) überschreibt das Pauschalgewicht; sonst generisch.
            const fMatWeight = (selectedCovering && selectedCovering.flaechengewicht_kg_m2)
                ? selectedCovering.flaechengewicht_kg_m2
                : ((MATERIAL_PROPS[cover] && MATERIAL_PROPS[cover].weightPerM2) || 0);
            return { bom: fItems.filter(it => it.isFlat), workPlan: fSteps, totals: { min: fTime, area: totalRoofArea, weight: (totalRoofArea * fMatWeight) / 1000 } };
        }

        if (totalRoofArea === 0) {
            const horizontalRun = build.width/2 + build.overhang;
            // Reparatur 1: Cosinus gegen 0 absichern (Neigung ~90° -> sonst Infinity).
            const slopeLen = horizontalRun / sichererCos(build.pitch);
            totalRoofArea = 2 * (build.length + 2*build.overhangGable) * slopeLen;
        }

        const items = []; let pos = 1;
        const rafterVol = totalRafterLength * bMasse.rafterW * bMasse.rafterH;
        items.push({ pos: pos++, n: `Bauholz (KVH)`, q: rafterVol.toFixed(2), u: 'm³', tBase: TIME_VARS.RAFTER_M, tTotal: totalRafterLength * TIME_VARS.RAFTER_M, isFlat: false });
        items.push({ pos: pos++, n: 'Unterspannbahn', q: Math.ceil(totalRoofArea * 1.1), u: 'm²', tBase: TIME_VARS.MEMBRANE_M2, tTotal: totalRoofArea * TIME_VARS.MEMBRANE_M2, isFlat: false });
        items.push({ pos: pos++, n: 'Konterlattung (30x50mm)', q: konterLength.toFixed(1), u: 'lfm', tBase: 1, tTotal: konterLength * 1, isFlat: false });
        items.push({ pos: pos++, n: 'Traglattung (30x40mm)', q: totalBattenLength.toFixed(1), u: 'lfm', tBase: TIME_VARS.BATTEN_M, tTotal: totalBattenLength * TIME_VARS.BATTEN_M, isFlat: false });
        // Reparatur 8: weitere ECHTE Holzbauteile aus der gezeichneten Geometrie (Pfetten bei
        // Sattel/Walm, Grat-/Kehlsparren bei Walm/Anbau). Nur ausgeben, wenn tatsächlich
        // vorhanden (>0) — keine erfundenen Mengen. Geometrisch ermittelte Aufmaßwerte,
        // KEINE statische Bemessung (Querschnitt/Last separat fachkundig prüfen).
        const hBauteile = holzBauteileAusListe(engineRef.current?.holzliste);
        if (hBauteile.pfettenLaenge > 0) items.push({ pos: pos++, n: 'Pfetten (First/Fuß) · geom. ermittelt', q: hBauteile.pfettenLaenge.toFixed(1), u: 'lfm', tBase: TIME_VARS.RAFTER_M, tTotal: hBauteile.pfettenLaenge * TIME_VARS.RAFTER_M, isFlat: false });
        if (hBauteile.gratsparrenLaenge > 0) items.push({ pos: pos++, n: 'Gratsparren (3D) · geom. ermittelt', q: hBauteile.gratsparrenLaenge.toFixed(1), u: 'lfm', tBase: TIME_VARS.RAFTER_M, tTotal: hBauteile.gratsparrenLaenge * TIME_VARS.RAFTER_M, isFlat: false });
        if (hBauteile.kehlsparrenLaenge > 0) items.push({ pos: pos++, n: 'Kehlsparren (3D) · geom. ermittelt', q: hBauteile.kehlsparrenLaenge.toFixed(1), u: 'lfm', tBase: TIME_VARS.RAFTER_M, tTotal: hBauteile.kehlsparrenLaenge * TIME_VARS.RAFTER_M, isFlat: false });
        // Eingabeaufforderung 28: „davon Schiftsparren" — verkürzte Gemeinsparren an Kehle/Grat, geometrisch
        // aus der gezeichneten Holzliste klassifiziert. MENGENNEUTRALER Breakdown (Schifter sind bereits in
        // Bauholz/Sparren-lfm enthalten) → tTotal=0, KEINE Doppelzählung. Querschnitt = Gemeinsparren.
        const schifter = schifterMengenAusListe(engineRef.current?.holzliste);
        if (schifter.anzahl > 0) {
            const teil = [
                schifter.kehleAnzahl > 0 ? `${schifter.kehleAnzahl}× Kehle` : '',
                schifter.gratAnzahl > 0 ? `${schifter.gratAnzahl}× Grat` : '',
            ].filter(Boolean).join(' / ');
            const spanne = `${schifter.laengeMin.toFixed(1)}–${schifter.laengeMax.toFixed(1)} m · Σ ${schifter.laengeGesamt.toFixed(1)} lfm`;
            items.push({ pos: pos++, n: `davon Schiftsparren (${teil}) · ${spanne} · geom. ermittelt`, q: String(schifter.anzahl), u: 'Stk', tBase: 0, tTotal: 0, isFlat: false });
        }
        // Reparatur 9: Wechselhölzer (Auswechslung) — NUR die geometrisch sicher ableitbaren
        // (Öffnung schneidet Sparren, angrenzende tragende Sparren eindeutig, nicht randnah).
        // Aufmaßwert, statisch zu prüfen; prüfpflichtige Fälle werden NICHT als Menge gezählt.
        let wechselLaengeGesamt = 0;
        // Reparatur 10: Menge aus den ECHTEN 3D-Wechselhölzern (holzliste type 'wechsel') statt
        // der Rep-9-Schätzung — eine Mengenwahrheit, keine Doppelzählung mit den Teilstücken.
        (engineRef.current?.holzliste || []).forEach((h:any) => { if (h && h.type === 'wechsel' && Number.isFinite(h.laenge) && h.laenge > 0) wechselLaengeGesamt += h.laenge; });
        if (wechselLaengeGesamt > 0) items.push({ pos: pos++, n: 'Wechselhölzer (Auswechslung) · geom. ermittelt · statisch prüfen', q: wechselLaengeGesamt.toFixed(1), u: 'lfm', tBase: TIME_VARS.RAFTER_M, tTotal: wechselLaengeGesamt * TIME_VARS.RAFTER_M, isFlat: false });
        const baseMat = MATERIAL_PROPS[cover] || MATERIAL_PROPS.ziegel;
        // Echtes Produkt aus der Produktdatenbank überschreibt die Pauschalwerte.
        // Ohne Auswahl -> Pauschale (Richtwert). Ziegel: Bedarf+Flächengewicht aus Modell;
        // Trapezblech: Flächengewicht aus gewählter Metall-Eindeckung.
        let mat = baseMat;
        if (cover === 'ziegel' && selectedTile && (selectedTile.bedarf_stk_m2 || selectedTile.flaechengewicht_kg_m2 || selectedTile.gewicht_kg)) {
            mat = {
                name: `${selectedTile.hersteller ?? ''} ${selectedTile.modell ?? ''}`.trim() || baseMat.name,
                piecesPerM2: selectedTile.bedarf_stk_m2 ?? baseMat.piecesPerM2,
                weightPerM2: selectedTile.flaechengewicht_kg_m2
                    ?? (selectedTile.gewicht_kg != null && selectedTile.bedarf_stk_m2 != null
                        ? selectedTile.gewicht_kg * selectedTile.bedarf_stk_m2
                        : baseMat.weightPerM2),
            };
        } else if (cover === 'trapezblech' && selectedCovering && selectedCovering.flaechengewicht_kg_m2) {
            mat = {
                name: `${selectedCovering.hersteller ?? ''} ${selectedCovering.produkt ?? ''}`.trim() || baseMat.name,
                piecesPerM2: baseMat.piecesPerM2,
                weightPerM2: selectedCovering.flaechengewicht_kg_m2,
            };
        }
        // E19: Dachöffnungs-Flächenbilanz pro Fläche (echte Löcher mindern die Netto-EINDECKUNG;
        // Prüffelder NICHT). Brutto bleibt für Unterspannbahn/Lattung maßgeblich (oben), nur die
        // sichtbare Eindeckung (Ziegel) nutzt die Nettofläche. Reparatur-6-Polygonflächenlogik unberührt.
        let echteLochGesamt = 0, prueffeldGesamt = 0, nEcht = 0, nPruef = 0;
        try {
            (engineRef.current?.surfaces || new Map()).forEach((s: any, id: string) => {
                const aRad = Math.max(0, Math.asin(Math.max(-1, Math.min(1, s.vDown?.y ?? 0)))); // E25
                const oeffn = (obstacles || []).filter((o: any) => o.surfaceId === id).map((o: any) => ({
                    id: o.id, art: o.type, surfaceId: id, xRel: o.x, yRel: o.y, breiteM: o.width, hoeheM: o.height, tiefeM: o.depth,
                    pitch: o.pitch, lyApexMax: engineRef.current!.flaecheFirstHoehe(s, o.x) - engineRef.current!.surfacePoint(s, o.x, o.y, 0.02).y - 1e-3,
                }));
                if (!oeffn.length) return;
                const poly = (s.polygon || []).map((p: any) => ({ x: p.x, y: p.y }));
                const geneigt = (s.vNormal?.y ?? 1) < 0.999; // E23: Gauben-Loch nur auf geneigter Fläche
                const satz = sichereLoecher(id, poly, s.width, s.height, oeffn, geneigt, aRad); // E25: aRad -> Polygon-Netto
                echteLochGesamt += satz.oeffnungEchtM2; prueffeldGesamt += satz.oeffnungPrueffeldM2;
                nEcht += satz.echteIds.length; nPruef += satz.prueffeldIds.length;
            });
        } catch { echteLochGesamt = 0; }
        const nettoEindeckung = Math.max(0, totalRoofArea - echteLochGesamt);
        const tileCount = Math.ceil(nettoEindeckung * mat.piecesPerM2 * 1.05);
        items.push({ pos: pos++, n: `${mat.name}`, q: tileCount, u: 'Stk', tBase: 0, tTotal: nettoEindeckung * TIME_VARS.TILE_M2, isFlat: false });
        items.push({ pos: pos++, n: 'Bruttodachfläche', q: totalRoofArea.toFixed(1), u: 'm²', tBase: 0, tTotal: 0, isFlat: false });
        if (nEcht) items.push({ pos: pos++, n: `Dachöffnungen · echtes Dachhaut-Loch (${nEcht})`, q: echteLochGesamt.toFixed(2), u: 'm²', tBase: 0, tTotal: 0, isFlat: false });
        if (nPruef) items.push({ pos: pos++, n: `Dachöffnungen · Prüffeld ohne Abzug (${nPruef})`, q: prueffeldGesamt.toFixed(2), u: 'm²', tBase: 0, tTotal: 0, isFlat: false });
        if (nEcht) items.push({ pos: pos++, n: 'Nettodachfläche (Eindeckung n. echten Löchern)', q: nettoEindeckung.toFixed(1), u: 'm²', tBase: 0, tTotal: 0, isFlat: false });
        const steps = [
            { idx: 1, t: "Gerüstbau", m: totalRoofArea * TIME_VARS.SCAFFOLD_M2 + 60, d: "Gerüstaufbau & Sicherheit", isFlat: false },
            { idx: 2, t: "Abbinden & Richten", m: totalRafterLength * TIME_VARS.RAFTER_M, d: "Holz abbinden, Sparren setzen", isFlat: false },
            { idx: 3, t: "Unterdach", m: totalRoofArea * TIME_VARS.MEMBRANE_M2, d: "Schalung & Folie", isFlat: false },
            { idx: 4, t: "Lattung & Eindeckung", m: (totalBattenLength * TIME_VARS.BATTEN_M) + (totalRoofArea * TIME_VARS.TILE_M2), d: "Traglattung und Ziegel legen", isFlat: false }
        ];
        const totalTimePlan = steps.reduce((acc, step) => acc + step.m, 0);
        return { bom: items.filter(it => !it.isFlat), workPlan: steps, totals: { min: totalTimePlan, area: totalRoofArea, weight: (totalRoofArea * mat.weightPerM2) / 1000 } };
    }, [cover, build, obstacles, auswechslungInfos, selectedTile, selectedCovering]);

    const topologyAnalysis = useMemo(() => analyzeTopology(buildTopologyPolygon(build), edgeTopologyConfigs), [build, edgeTopologyConfigs]);

    const updateEdgeTopologyType = (index: number, type: EdgeTopologyType) => {
        setEdgeTopologyConfigs(prev => prev.map((edge, i) => i === index ? { ...edge, type, pitch: type === 'GIEBEL' ? 0 : edge.pitch || build.pitch } : edge));
    };

    const updateEdgeTopologyPitch = (index: number, pitch: number) => {
        setEdgeTopologyConfigs(prev => prev.map((edge, i) => i === index ? { ...edge, pitch } : edge));
    };

    const aggrHolzliste = useMemo(() => {
        let map = new Map();
        holzliste.forEach(h => {
            const key = `${h.type}_${h.breite}_${h.hoehe}_${h.laenge.toFixed(2)}`;
            if(map.has(key)) {
                let ex = map.get(key);
                ex.anzahl++; ex.volGesamt += h.volEinzel;
            } else {
                map.set(key, {...h});
            }
        });
        return Array.from(map.values());
    }, [holzliste]);

    return (
        <div className="flex h-screen bg-canvas text-ink font-sans overflow-hidden">
            <aside className={`bg-white border-r border-line flex flex-col shadow-2xl transition-all duration-300 ease-in-out overflow-hidden absolute inset-y-0 left-0 z-40 md:static md:z-10 ${isLeftSidebarOpen ? 'w-[480px] max-w-[90vw] translate-x-0' : 'w-[480px] max-w-[90vw] -translate-x-full pointer-events-none md:w-16 md:translate-x-0 md:pointer-events-auto'}`}>
                {isLeftSidebarOpen ? (
                <div className="w-full max-w-[480px] h-full flex flex-col">
                    <div className="h-16 flex items-center px-6 border-b border-line bg-canvas shrink-0 justify-between">
                        <div className="flex items-center">
                            <Trees className="text-brand-700 w-6 h-6 mr-3"/>
                            <div className="font-bold text-lg text-ink">DACHDECKER <span className="text-brand-700 font-mono">PRO</span></div>
                        </div>
                        <button onClick={() => setIsLeftSidebarOpen(false)} className="text-muted hover:text-ink transition-colors bg-canvas p-2.5 rounded-md hover:bg-line">
                            <PanelLeftClose className="w-4 h-4"/>
                        </button>
                    </div>

                    <div className="flex border-b border-line text-[10px] font-bold uppercase overflow-x-auto shrink-0 bg-white">
                        <NavBtn id="construct" icon={Home} label="Haus" active={view} set={setView} />
                        <NavBtn id="obstacles" icon={Box} label="Aufbauten" active={view} set={setView} />
                        <NavBtn id="modules" icon={LayoutTemplate} label="Belegung" active={view} set={setView} />
                        <NavBtn id="bom" icon={Package} label="Holzliste" active={view} set={setView} />
                        <NavBtn id="work" icon={Hammer} label="Kalkulation" active={view} set={setView} />
                    </div>

                    <div className="flex-1 overflow-y-auto p-6 space-y-8">
                        
                        {/* VIEW: KONSTRUKTION */}
                        {view === 'construct' && (
                            <div className="space-y-6">
                                {/* ADDITIVE SEKTION: Dachform-Vorlagenbibliothek (oberhalb der manuellen Dachform-Buttons).
                                    Anwenden NUR über setBuild/setCover -> Reparatur-2/3-Warnungen feuern automatisch.
                                    Bestehende Schräg/Flach-Umschaltung + ShapeBtn/Range/InputNumber bleiben unverändert. */}
                                <div className="bg-white border border-line rounded-xl overflow-hidden">
                                    <button
                                        onClick={() => setVorlagenOffen(o => !o)}
                                        className="w-full flex items-center justify-between gap-2 px-4 py-3 text-left bg-canvas hover:bg-line/40 transition-colors"
                                    >
                                        <span className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-ink">
                                            <LayoutTemplate className="w-4 h-4 text-brand-700"/> Dachform-Vorlagen
                                            <span className="text-[10px] font-mono text-muted bg-white border border-line rounded px-1.5 py-0.5">{vorlagenGefiltert.length}</span>
                                        </span>
                                        {vorlagenOffen ? <ChevronUp className="w-4 h-4 text-muted"/> : <ChevronDown className="w-4 h-4 text-muted"/>}
                                    </button>

                                    {vorlagenOffen && (
                                        <div className="p-4 space-y-3 border-t border-line">
                                            {/* (1) Suche */}
                                            <input
                                                type="text"
                                                value={vorlagenQuery}
                                                onChange={e => setVorlagenQuery(e.target.value)}
                                                placeholder="Vorlage suchen (z. B. Walm, Trapezblech, Flachdach) …"
                                                className="w-full bg-white border border-line rounded-lg p-2.5 text-sm text-ink outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-500/40"
                                            />

                                            {/* (2) Filter-Chips: Status + Kategorie */}
                                            <div className="space-y-2">
                                                <div className="flex flex-wrap gap-1.5">
                                                    <VorlagenChip active={vorlagenStatusFilter===''} onClick={()=>setVorlagenStatusFilter('')} label="Alle"/>
                                                    <VorlagenChip active={vorlagenStatusFilter==='verfuegbar'} onClick={()=>setVorlagenStatusFilter('verfuegbar')} label="Verfügbar"/>
                                                    <VorlagenChip active={vorlagenStatusFilter==='geplant'} onClick={()=>setVorlagenStatusFilter('geplant')} label="Geplant"/>
                                                </div>
                                                <div className="flex flex-wrap gap-1.5">
                                                    <VorlagenChip active={vorlagenKategorieFilter===''} onClick={()=>setVorlagenKategorieFilter('')} label="Alle Dächer"/>
                                                    <VorlagenChip active={vorlagenKategorieFilter==='pitched'} onClick={()=>setVorlagenKategorieFilter('pitched')} label="Schräg"/>
                                                    <VorlagenChip active={vorlagenKategorieFilter==='flat'} onClick={()=>setVorlagenKategorieFilter('flat')} label="Flach"/>
                                                </div>
                                                {/* Additiv: Dachform-Filter (Grundform) */}
                                                <div className="flex flex-wrap gap-1.5">
                                                    <VorlagenChip active={vorlagenFormFilter===''} onClick={()=>setVorlagenFormFilter('')} label="Alle Formen"/>
                                                    <VorlagenChip active={vorlagenFormFilter==='sattel'} onClick={()=>setVorlagenFormFilter('sattel')} label="Sattel"/>
                                                    <VorlagenChip active={vorlagenFormFilter==='pult'} onClick={()=>setVorlagenFormFilter('pult')} label="Pult"/>
                                                    <VorlagenChip active={vorlagenFormFilter==='walm'} onClick={()=>setVorlagenFormFilter('walm')} label="Walm"/>
                                                    <VorlagenChip active={vorlagenFormFilter==='flach'} onClick={()=>setVorlagenFormFilter('flach')} label="Flach"/>
                                                    <VorlagenChip active={vorlagenFormFilter==='sonder'} onClick={()=>setVorlagenFormFilter('sonder')} label="Sonderform"/>
                                                </div>
                                                {/* Additiv: Merkmal-Filter (Gebäudeform / PV / Aufbauten) */}
                                                <div className="flex flex-wrap gap-1.5">
                                                    <VorlagenChip active={vorlagenMerkmalFilter===''} onClick={()=>setVorlagenMerkmalFilter('')} label="Alle Merkmale"/>
                                                    <VorlagenChip active={vorlagenMerkmalFilter==='pv'} onClick={()=>setVorlagenMerkmalFilter('pv')} label="PV-optimiert"/>
                                                    <VorlagenChip active={vorlagenMerkmalFilter==='gaube'} onClick={()=>setVorlagenMerkmalFilter('gaube')} label="Gauben"/>
                                                    <VorlagenChip active={vorlagenMerkmalFilter==='aufbau'} onClick={()=>setVorlagenMerkmalFilter('aufbau')} label="Aufbauten"/>
                                                    <VorlagenChip active={vorlagenMerkmalFilter==='gebaeude'} onClick={()=>setVorlagenMerkmalFilter('gebaeude')} label="Gebäudetyp"/>
                                                    <VorlagenChip active={vorlagenMerkmalFilter==='ltu'} onClick={()=>setVorlagenMerkmalFilter('ltu')} label="L/T/U & Mehrkörper"/>
                                                </div>
                                            </div>

                                            {/* (2b) Status-Legende (3 Stufen) + Zähler */}
                                            <div className="flex items-center justify-between gap-2 text-[10px] text-muted px-0.5">
                                                <span className="min-w-0 leading-tight"><span className="font-bold text-mint-700">Verfügbar</span> = Form wird gebaut · <span className="font-bold text-amber-700">Teilweise</span> = Basisdach baubar, Aufbau = Vorschau · <span className="font-bold text-slate-600">Geplant</span> = noch nicht baubar</span>
                                                <span className="shrink-0 font-mono">{vorlagenGefiltert.filter(v=>v.anwendbar).length}✓ / {vorlagenGefiltert.length}</span>
                                            </div>

                                            {/* (3) scrollbare Karten-Liste */}
                                            <div className="space-y-2.5 max-h-[420px] overflow-y-auto custom-scrollbar pr-1">
                                                {vorlagenGefiltert.length === 0 && (
                                                    <div className="text-xs text-muted italic p-3 bg-canvas rounded-lg border border-dashed border-line flex flex-col items-start gap-2">
                                                        <span>Keine Vorlage passt zu Suche/Filter.</span>
                                                        <button
                                                            onClick={()=>{ setVorlagenQuery(''); setVorlagenStatusFilter(''); setVorlagenKategorieFilter(''); setVorlagenFormFilter(''); setVorlagenMerkmalFilter(''); }}
                                                            className="not-italic px-2 py-1 rounded-md bg-white border border-line text-ink text-[10px] font-bold hover:border-brand-500"
                                                        >Filter zurücksetzen</button>
                                                    </div>
                                                )}
                                                {vorlagenGefiltert.map(v => {
                                                    const g = v.geometrie;
                                                    const warnungen = validateVorlage(v).warnungen;
                                                    const offen = vorlagenDetailId === v.id;
                                                    const aStatus = anzeigeStatus(v); // 'verfuegbar' | 'teilweise' | 'geplant'
                                                    return (
                                                        <div key={v.id} className={`rounded-xl border shadow-sm transition-colors ${v.anwendbar ? 'bg-white border-line' : 'bg-canvas border-dashed border-line'} ${offen ? 'border-brand-500 ring-1 ring-brand-500/40' : ''}`}>
                                                            {/* Kompakter, klickbarer Kopf: kleines Thumbnail + Titel + Status (wenig Scrollen bei vielen Vorlagen) */}
                                                            <button onClick={()=>setVorlagenDetailId(offen ? '' : v.id)} className="w-full flex items-center gap-2.5 p-2.5 text-left">
                                                                <div
                                                                    className={`shrink-0 h-12 w-16 overflow-hidden rounded-md border flex items-center justify-center ${v.anwendbar ? 'border-line bg-white' : 'border-dashed border-line bg-canvas opacity-80'}`}
                                                                    aria-hidden="true"
                                                                    dangerouslySetInnerHTML={{ __html: vorschauSvg(v) }}
                                                                />
                                                                <div className="min-w-0 flex-1">
                                                                    <div className="text-xs font-bold text-ink truncate">{v.name}</div>
                                                                    <div className="text-[10px] text-muted truncate">{v.kurzbeschreibung}</div>
                                                                </div>
                                                                <div className="flex flex-col items-end gap-1 shrink-0">
                                                                    {/* Ehrlicher 3-Status: Verfügbar / Teilweise (Basisdach anwendbar, Aufbau = Vorschau) / Geplant */}
                                                                    <Badge tone={aStatus==='geplant' ? 'slate' : aStatus==='teilweise' ? 'amber' : 'green'}>
                                                                        {aStatus==='geplant' ? 'Geplant' : aStatus==='teilweise' ? 'Teilweise' : 'Verfügbar'}
                                                                    </Badge>
                                                                    {aStatus==='teilweise' && <span className="text-[9px] text-amber-700 leading-tight text-right max-w-[80px]">Basisdach + Aufbau-Vorschau</span>}
                                                                    {aStatus==='verfuegbar' && vorschauZeigtPv(v) && <span className="text-[9px] text-muted leading-tight">PV = Vorschau</span>}
                                                                </div>
                                                                <span className="text-muted text-xs shrink-0">{offen ? '▾' : '▸'}</span>
                                                            </button>

                                                            {offen && (
                                                            <div className="px-3.5 pb-3.5">
                                                                {/* Kennwerte */}
                                                                <div className="grid grid-cols-2 gap-1 text-[10px] font-mono text-muted">
                                                                    <span>Form: <span className="text-ink">{g.shapeKey}</span></span>
                                                                    <span>Neigung: <span className="text-ink">{g.defaultPitch}°</span></span>
                                                                    <span>L×B: <span className="text-ink">{g.defaultLength}×{g.defaultWidth} m</span></span>
                                                                    <span>Traufhöhe: <span className="text-ink">{g.defaultHeight} m</span></span>
                                                                    <span className="col-span-2">Dachdeckung: <span className="text-ink">separat auswählen</span> · RDN {v.dachdecker.rdnGrad}° / Lattmaß produktabhängig (Richtwert)</span>
                                                                </div>

                                                                {/* Validierungs-Warnungen (RDN-Unterschreitung / L>W-Pflicht …) inline vor dem Anwenden */}
                                                                {warnungen.length > 0 && (
                                                                    <div className="mt-2 space-y-1">
                                                                        {warnungen.map((w, i) => (
                                                                            <div key={i} className={`text-[10px] rounded-md px-2 py-1 border ${w.schwere==='fehler' ? 'text-rose-700 bg-rose-500/10 border-rose-500/30' : w.schwere==='warnung' ? 'text-amber-700 bg-amber-500/10 border-amber-500/30' : 'text-slate-600 bg-slate-50 border-slate-200'}`}>
                                                                                ⚠ {w.text}
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                )}

                                                                {/* Fachdetails: Zimmerer / Dachdecker / PV */}
                                                                <div className="mt-2 space-y-2 text-[10px] text-muted bg-canvas rounded-lg p-2.5 border border-line">
                                                                    <div><span className="font-bold text-ink uppercase tracking-wider">Zimmerer:</span> {v.zimmerer.dachstuhltyp}. Sparren {v.zimmerer.querschnittSparrenCm[0]}/{v.zimmerer.querschnittSparrenCm[1]} cm @ {v.zimmerer.sparrenabstandCm} cm, {v.zimmerer.materialFestigkeit}. {v.zimmerer.spannweiteHinweis}</div>
                                                                    <div><span className="font-bold text-ink uppercase tracking-wider">Dachdecker:</span> {v.dachdecker.deckungsHinweis} Mindestneigung {v.dachdecker.mindestneigungGrad}° (materialabhängiger Richtwert). {v.dachdecker.firstausbildung}. {v.dachdecker.lueftungHinweis}</div>
                                                                    <div><span className="font-bold text-ink uppercase tracking-wider">PV:</span> belegbar: {v.pv.belegbareSeiten.join(', ') || '—'}{v.pv.ausgeschlosseneSeiten.length ? ` · ausgeschlossen: ${v.pv.ausgeschlosseneSeiten.join(', ')}` : ''}. Sperrzonen First {v.pv.sperrzoneFirstM} m / Traufe {v.pv.sperrzoneTraufeM} m / Ortgang {v.pv.sperrzoneOrtgangM} m. {v.pv.statikPlausibilitaetHinweis}</div>
                                                                </div>

                                                                {/* Deckungsneutral: Vorlage setzt nur die Dachform, niemals die Eindeckung/Material */}
                                                                {v.anwendbar && (
                                                                    <div className="mt-2 text-[10px] text-brand-700 bg-brand-500/10 border border-brand-500/30 rounded-md px-2 py-1">Diese Vorlage setzt nur die Dachform. Die Dacheindeckung/Material bleibt unverändert — bitte separat über die Produktauswahl wählen.</div>
                                                                )}

                                                                {/* Aufbau-Auto: setzbare Aufbauten werden beim Anwenden real angelegt (positiv) */}
                                                                {v.anwendbar && aufbautenWerdenGesetzt(v) && (
                                                                    <div className="mt-2 text-[10px] text-mint-700 bg-mint-500/10 border border-mint-500/30 rounded-md px-2 py-1">{AUFBAU_AUTO_HINWEIS}</div>
                                                                )}
                                                                {/* Ehrlichkeit: Gaube wird nur schematisch als Aufbau gesetzt (keine echte Gaubendach-Geometrie) */}
                                                                {v.anwendbar && gaubeSchematischGesetzt(v) && (
                                                                    <div className="mt-2 text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/30 rounded-md px-2 py-1">{GAUBE_SCHEMATISCH_HINWEIS}</div>
                                                                )}
                                                                {/* Schneefang als linienförmiges Dachbauteil (PV-Sperrzone vorbereitet) */}
                                                                {v.anwendbar && schneefangWirdGesetzt(v) && (
                                                                    <div className="mt-2 text-[10px] text-mint-700 bg-mint-500/10 border border-mint-500/30 rounded-md px-2 py-1">Schneefang wird als linienförmiges Dachbauteil parallel zur Traufe gesetzt; PV-Abstand vorbereitet. Auslegung/Befestigung/Statik fachlich prüfen.</div>
                                                                )}
                                                                {/* Vorschau-Rest: noch nicht setzbare Merkmale bleiben manuell */}
                                                                {v.anwendbar && aufbautenNichtGesetzt(v).length > 0 && (
                                                                    <div className="mt-2 text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/30 rounded-md px-2 py-1">{aufbautenNichtGesetzt(v).join(' / ')}: {VORSCHAU_AUFBAU_HINWEIS}</div>
                                                                )}
                                                                {v.anwendbar && vorschauZeigtPv(v) && (
                                                                    <div className="mt-2 text-[10px] text-muted bg-canvas border border-line rounded-md px-2 py-1">{VORSCHAU_PV_HINWEIS}</div>
                                                                )}

                                                                {/* Eingabeaufforderung 13: ehrlicher L/T/U-Grundriss-Hinweis */}
                                                                {['l-shape','t-shape','u-grundriss'].includes(v.geometrie.shapeKey) && v.anwendbar && (
                                                                    <div className="mt-2 text-[10px] text-mint-700 bg-mint-500/10 border border-mint-500/30 rounded-md px-2 py-1">{v.geometrie.shapeKey==='u-grundriss'?'U':v.geometrie.shapeKey==='t-shape'?'T':'L'}-Grundriss mit Flachdach: echtes Grundrisspolygon (keine Doppelzählung). Geneigte Dachverschneidungen (Sattel/Walm/Pult) mit Kehlen/Graten folgen separat.</div>
                                                                )}
                                                                {['l-shape','t-shape','u-grundriss'].includes(v.geometrie.shapeKey) && !v.anwendbar && (
                                                                    <div className="mt-2 text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/30 rounded-md px-2 py-1">Grundriss ist vorbereitet; die Flachdach-Variante dieser Form ist verfügbar. Geneigte Dachkörper, Kehlen, Grate und Holzliste sind noch geplant.</div>
                                                                )}

                                                                {/* Pflichthinweis Statik (dezent) */}
                                                                <div className="mt-2 text-[9px] text-muted leading-snug italic">{v.hinweisStatik}</div>

                                                                {/* Geplant-Grund (sichtbar, nicht anwendbar) */}
                                                                {!v.anwendbar && v.geplantGrund && (
                                                                    <div className="mt-2 text-[10px] text-slate-600 bg-slate-50 border border-slate-200 rounded-md px-2 py-1">In dieser Engine noch nicht baubar: {v.geplantGrund}</div>
                                                                )}

                                                                {/* Anwenden — NUR bei verfügbar aktiv */}
                                                                <button
                                                                    onClick={()=>v.anwendbar && handleVorlageAnwenden(v)}
                                                                    disabled={!v.anwendbar}
                                                                    title={v.anwendbar ? (aufbautenWerdenGesetzt(v) ? 'Setzt Dachform + Aufbauten (bestehende Aufbauten bleiben erhalten); Eindeckung separat' : 'Dachform auf das Modell anwenden') : (v.geplantGrund || 'In dieser Engine noch nicht baubar')}
                                                                    className={`mt-2.5 w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-bold transition-all ${v.anwendbar ? 'bg-brand-500 text-ink hover:bg-brand-600' : 'bg-line/60 text-muted cursor-not-allowed'}`}
                                                                >
                                                                    {v.anwendbar ? <Check className="w-3.5 h-3.5"/> : <Lock className="w-3.5 h-3.5"/>}
                                                                    {v.anwendbar ? (aufbautenWerdenGesetzt(v) ? 'Dach + Aufbauten anwenden' : 'Vorlage anwenden') : 'Nicht anwendbar (geplant)'}
                                                                </button>
                                                            </div>
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <div className="grid grid-cols-2 gap-2 bg-canvas p-1.5 rounded-lg border border-line">
                                    <ModeBtn active={build.category==='pitched'} onClick={()=>{ setBuild({...build, category:'pitched', shape:'sattel', pitch:35}); setCover(c => (c==='bitumen'||c==='kunststoff'||c==='gruendach'||c==='kies') ? 'ziegel' : c); }} icon={ArrowUpRight} label="Schrägdach"/>
                                    <ModeBtn active={build.category==='flat'} onClick={()=>{ setBuild({...build, category:'flat', shape:'rect', pitch:0}); setCover(c => (c==='ziegel'||c==='schiefer'||c==='trapezblech') ? 'bitumen' : c); }} icon={Layers} label="Flachdach"/>
                                </div>
                                <div className="space-y-3">
                                    <Label text="Dachform"/>
                                    <div className="grid grid-cols-3 gap-2">
                                        {build.category === 'pitched' ? (
                                            <>
                                                <ShapeBtn active={build.shape==='sattel'} onClick={()=>setBuild({...build, shape:'sattel'})} label="Sattel"/>
                                                <ShapeBtn active={build.shape==='pult'} onClick={()=>setBuild({...build, shape:'pult'})} label="Pult"/>
                                                <ShapeBtn active={build.shape==='walm'} onClick={()=>setBuild({...build, shape:'walm'})} label="Walm"/>
                                                {/* Phase-0-Fix: gueltige Shapes 'l-shape'/'t-shape' + category 'pitched' setzen
                                                    (vorher 'sattel-l' etc. -> kein Match in updateBuilding -> leeres Dach). */}
                                                <ShapeBtn active={build.shape==='l-shape'} onClick={()=>setBuild({...build, category:'pitched', shape:'l-shape'})} label="L-Form (Schrägdach)"/>
                                                <ShapeBtn active={build.shape==='t-shape'} onClick={()=>setBuild({...build, category:'pitched', shape:'t-shape'})} label="T-Form (Schrägdach)"/>
                                                {/* E27: geneigte U-Form (zwei Flügel-Satteldächer); fällt bei ungültiger Geometrie auf Flachdach zurück. */}
                                                <ShapeBtn active={build.shape==='u-shape' && build.category==='pitched'} onClick={()=>setBuild({...build, category:'pitched', shape:'u-shape'})} label="U-Form (Schrägdach)"/>
                                            </>
                                        ) : (
                                            <>
                                                <ShapeBtn active={build.shape==='rect'} onClick={()=>setBuild({...build, shape:'rect'})} label="Rechteck"/>
                                                <ShapeBtn active={build.shape==='l-shape'} onClick={()=>setBuild({...build, category:'flat', shape:'l-shape'})} label="L-Form"/>
                                            </>
                                        )}
                                    </div>
                                </div>
                                <div className="space-y-5 border-t border-line pt-5">
                                    <Range label="Länge (Traufe)" val={build.length} set={(v:any)=>setBuild({...build, length: Math.max(0.1, v)})} min={5} max={25} />
                                    <Range label="Breite (Giebel)" val={build.width} set={(v:any)=>setBuild({...build, width: Math.max(0.1, v)})} min={4} max={15} />
                                    <Range label="Traufhöhe" val={build.height} set={(v:any)=>setBuild({...build, height: Math.max(0.1, v)})} min={3} max={10} />
                                    {build.category === 'pitched' && (
                                        <div className="grid grid-cols-1 gap-5 pt-2">
                                            <Range label="Dachneigung" val={build.pitch} set={(v:any)=>setBuild({...build, pitch:v})} min={10} max={60} unit="°" />
                                            <div className="grid grid-cols-2 gap-4">
                                                <Range label="Überstand Traufe" val={build.overhang} set={(v:any)=>setBuild({...build, overhang:v})} min={0} max={1.5} />
                                                <Range label="Überstand Ortgang" val={build.overhangGable} set={(v:any)=>setBuild({...build, overhangGable:v})} min={0} max={1.5} />
                                            </div>
                                            {(build.shape === 'sattel-l' || build.shape === 'sattel-t' || build.shape === 'walm-l' || build.shape === 'walm-t' || build.shape === 'l-shape' || build.shape === 't-shape') && (
                                                <div className="grid grid-cols-2 gap-4 pt-4 border-t border-line">
                                                    <Range label="Anbau Länge (Z)" val={build.lengthB} set={(v:any)=>setBuild({...build, lengthB: Math.max(0.1, v)})} min={2} max={20} />
                                                    <Range label="Anbau Breite (X)" val={build.widthB} set={(v:any)=>setBuild({...build, widthB: Math.max(0.1, v)})} min={2} max={build.width} />
                                                </div>
                                            )}
                                        </div>
                                    )}
                                </div>

                                <div className="p-5 bg-white border border-line rounded-xl space-y-4">
                                    <div className="flex items-center gap-2 mb-3 text-muted font-bold text-xs uppercase border-b border-line pb-3">
                                        <Ruler className="w-4 h-4 text-brand-700"/> Dachstuhl-Details
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <InputNumber label="Sparren Breite (cm)" val={build.rafterWidth} set={(v:any)=>setBuild({...build, rafterWidth: v})} min={DACH_FLOOR_CM.rafterWidth}/>
                                        <InputNumber label="Sparren Höhe (cm)" val={build.rafterHeight} set={(v:any)=>setBuild({...build, rafterHeight: v})} min={DACH_FLOOR_CM.rafterHeight}/>
                                        <InputNumber label="Sparren Abstand (cm)" val={build.rafterSpacing} set={(v:any)=>setBuild({...build, rafterSpacing: v})} min={DACH_FLOOR_CM.rafterSpacing}/>
                                        <InputNumber label="Latten Abstand (cm)" val={build.battenDist} set={(v:any)=>setBuild({...build, battenDist: v})} min={DACH_FLOOR_CM.battenDist}/>
                                    </div>
                                    {/* Reparatur 1: sichtbarer Hinweis, wenn ein Maß auf den Mindestwert geklemmt wurde. */}
                                    {dachstuhlHinweise(build).length > 0 && (
                                        <div className="text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/30 rounded-lg p-2 space-y-0.5">
                                            {dachstuhlHinweise(build).map((h, i) => <div key={i}>⚠ {h}</div>)}
                                        </div>
                                    )}
                                </div>

                                <div className="pt-5 border-t border-line">
                                    <Label text="Eindeckung"/>
                                    <select className="w-full bg-white border border-line rounded-lg p-2.5 text-sm text-ink mt-1 focus:border-brand-600 focus:ring-2 focus:ring-brand-500/40" value={cover} onChange={e=>setCover(e.target.value as any)}>
                                        {build.category === 'flat' ? (
                                            <>
                                                <option value="bitumen">Bitumen-Schweißbahn</option>
                                                <option value="kunststoff">Kunststoffbahn (FPO/PVC)</option>
                                                <option value="gruendach">Gründach (extensiv)</option>
                                                <option value="kies">Kiesschüttung</option>
                                            </>
                                        ) : (
                                            <>
                                                <option value="ziegel">Dachpfannen (Ton/Beton)</option>
                                                <option value="schiefer">Naturschiefer</option>
                                                <option value="trapezblech">Trapezblech / Stehfalz</option>
                                            </>
                                        )}
                                    </select>
                                    {cover === 'ziegel' && (
                                        <EindeckungMaterialPanel active={cover === 'ziegel'} selected={selectedTile} onSelect={setSelectedTile} roofPitch={build.pitch} />
                                    )}
                                    {(cover === 'bitumen' || cover === 'kunststoff' || cover === 'trapezblech' || cover === 'schiefer') && (
                                        <CoveringMaterialPanel active families={COVER_FAMILIES[cover] || []} selected={selectedCovering} onSelect={setSelectedCovering} roofPitch={build.pitch} />
                                    )}
                                    {/* PV-Montagesystem aus der Eindeckung geroutet (Befestiger/Schiene/Klemme + Warnungen). */}
                                    <MontagesystemPanel cover={cover} coveringFamily={selectedCovering?.family ?? null} tileTypeId={cover === 'ziegel' ? (selectedTile?.tile_type_id ?? null) : null} />
                                </div>
                            </div>
                        )}

                        {/* VIEW: OBJEKTE */}
                        {view === 'obstacles' && (
                            <div className="space-y-6">
                                {/* Reparatur 3: Warnung, wenn Aufbauten nach Geometrieänderung keiner gültigen Fläche mehr zugeordnet sind. */}
                                {aufbautenPruefIds.length > 0 && (
                                    <div className="flex items-start gap-2 text-[11px] text-amber-700 bg-amber-500/10 border border-amber-500/40 rounded-lg p-3">
                                        <span className="text-base leading-none">⚠</span>
                                        <span>{AUFBAUTEN_WARNUNG} ({aufbautenPruefIds.length} betroffen — unten markiert; bitte neu zuordnen oder entfernen.)</span>
                                    </div>
                                )}
                                <div className="p-3.5 bg-sky-500/10 border border-sky-500/30 rounded-lg text-xs text-sky-700 flex gap-3">
                                    <MousePointer2 className="w-5 h-5 shrink-0 text-sky-700 mt-0.5"/>
                                    <div><strong>Dachaufbauten platzieren:</strong> Ziehe Gauben oder Fenster direkt auf dem 3D-Modell an die richtige Position.</div>
                                </div>
                                <div className="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-lg text-xs text-amber-800 flex gap-3">
                                    <ClipboardList className="w-5 h-5 shrink-0 text-amber-700 mt-0.5"/>
                                    <div>
                                        <strong>Dachhaut-Löcher:</strong> Dachfenster, Kamin, Lüfter und Lichtkuppel werden auf allen
                                        <em>sicheren konvexen Dachflächen</em> (Rechteck-Sattel/Pult/Flach, trapezförmige Walmflächen und konvexe
                                        Mehr-Eck-Flächen) als <strong>echtes Dachhaut-Loch</strong> mit Nettoflächen-Abzug in der Stückliste dargestellt —
                                        nur wenn die Öffnung mit Randabstand vollständig innerhalb der Fläche liegt (nicht über schräge/diagonale
                                        Kanten). <strong>L-/T-/U-Flachdächer</strong> werden in konvexe Teilbereiche zerlegt: ein echtes Loch entsteht nur, wenn
                                        die Öffnung vollständig in <em>einem</em> Teilbereich liegt (nicht über Innenkanten, nicht im Innenhof, nicht im
                                        einspringenden Winkel) — sonst Prüffeld. <strong>Gauben</strong> (Schlepp-/Pult- und Giebel-/Satteldachgaube) erzeugen auf
                                        sicheren <em>rechteckigen Sattel-/Pultflächen</em> ein echtes Hauptdach-Loch unter dem Gaubenfußabdruck; ihre
                                        Anschluss-/Kehllinien sind <em>schematisch</em> (keine berechnete Flächenverschneidung). Auf Walm/Trapez/L-T-U und für
                                        Sondergauben bleibt die Gaube Prüffeld; <strong>geneigte L-/T-/U-Dächer</strong> bleiben geplant; echte Gauben-Kehlen folgen
                                        separat. Statik, Brandschutz und Dachdecker-Anschluss fachlich prüfen.
                                    </div>
                                </div>

                                <div className="flex gap-1.5 overflow-x-auto pb-2 border-b border-line custom-scrollbar">
                                    {availableSurfaces.map(s => (
                                        <button 
                                            key={s.id}
                                            onClick={() => setActiveSurface(s.id)}
                                            className={`px-4 py-1.5 text-xs font-bold uppercase rounded-t-lg border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 ${activeSurface === s.id ? 'border-brand-500 text-ink bg-white' : 'border-transparent text-muted hover:text-ink hover:bg-canvas'}`}
                                        >
                                            {s.name}
                                        </button>
                                    ))}
                                </div>

                                <div>
                                    <Label text="Standard Durchdringungen"/>
                                    <div className="grid grid-cols-4 gap-2.5 mt-2">
                                        <ObsBtn onClick={()=>addObs('chimney')} icon={Box} label="Kamin" color="text-brand-700"/>
                                        <ObsBtn onClick={()=>addObs('window')} icon={Maximize2} label="DF-Fenster" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('vent')} icon={Wind} label="Lüfter" color="text-muted"/>
                                        <GaubeBtn onClick={()=>addObs('sat')} icon={Disc} label="Sat-Anlage" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('lichtkuppel')} icon={Sun} label="Lichtkuppel" color="text-brand-700"/>
                                    </div>
                                </div>

                                <div className="pt-2 border-t border-line">
                                    <Label text="Gauben & Konstruktionen"/>
                                    <div className="grid grid-cols-5 gap-2.5 mt-2">
                                        <GaubeBtn onClick={()=>addObs('schleppgaube')} icon={Tent} label="Schlepp" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('trapezgaube')} icon={Building} label="Trapez" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('flachgaube')} icon={Square} label="Flach" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('giebelgaube')} icon={Home} label="Giebel" color="text-brand-700"/>
                                        <GaubeBtn onClick={()=>addObs('spitzgaube')} icon={Triangle} label="Spitz" color="text-brand-700"/>
                                    </div>
                                </div>

                                {/* Eingabeaufforderung 12: linienförmige Dachbauteile (Schneefang) */}
                                <div className="pt-6 border-t border-line">
                                    <Label text="Linienförmige Dachbauteile"/>
                                    <button onClick={addSchneefang} className="mt-2 w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-bold bg-brand-500 text-ink hover:bg-brand-600 transition-all">
                                        <Minus className="w-3.5 h-3.5"/> Schneefanglinie (aktive Fläche)
                                    </button>
                                    <div className="mt-1.5 text-[10px] text-muted leading-snug">{SCHNEEFANG_HINWEIS}</div>
                                    {linienPvWarnung && (
                                        <div className="mt-2 text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/40 rounded-md px-2 py-1">PV-Sperrzone des Schneefangs vorbereitet — vorhandene PV-Belegung gegen die Sperrlinie prüfen (es werden KEINE Module automatisch verschoben).</div>
                                    )}
                                    {linienBauteile.length === 0 && <div className="mt-2 text-xs text-muted italic p-3 bg-canvas rounded-lg border border-dashed border-line">Noch kein Linienbauteil gesetzt.</div>}
                                    {linienBauteile.map(l => (
                                        <div key={l.id} className="mt-2 bg-white p-3 rounded-xl border border-line shadow-sm">
                                            <div className="flex items-center justify-between">
                                                <div className="text-xs font-bold text-ink capitalize">{l.art} <span className="text-[10px] font-normal text-muted">(linienförmig)</span></div>
                                                <button onClick={()=>setLinienBauteile(linienBauteile.filter(x=>x.id!==l.id))} className="text-rose-700 hover:text-white hover:bg-rose-500 p-1.5 rounded transition-colors"><Trash2 className="w-4 h-4"/></button>
                                            </div>
                                            <div className="mt-1 grid grid-cols-2 gap-1 text-[10px] font-mono text-muted">
                                                <span>Fläche: <span className="text-ink">{l.surfaceId}</span></span>
                                                <span>Länge: <span className="text-ink">{l.laengeM} m</span></span>
                                                <span>Abstand Traufe: <span className="text-ink">{l.abstandTraufeM} m</span></span>
                                                <span>PV-Sperrzone: <span className="text-ink">−{l.sperrAbstandUntenM} / +{l.sperrAbstandObenM} m</span></span>
                                            </div>
                                            <div className="mt-1 text-[10px] text-amber-700">geometrisch gesetzt · fachlich/statisch prüfen · PV-Abstand vorbereitet · keine statische Bemessung · Produkt später auswählen</div>
                                        </div>
                                    ))}
                                </div>

                                <div className="space-y-3 pt-6 border-t border-line">
                                    <Label text="Platzierte Aufbauten & Maße"/>
                                    {obstacles.length === 0 && <div className="text-xs text-muted italic p-3 bg-canvas rounded-lg border border-dashed border-line">Noch keine Aufbauten platziert.</div>}
                                    {obstacles.map((o,i) => (
                                        <div key={o.id} className="bg-white p-4 rounded-xl border border-line hover:border-brand-500 transition-colors shadow-sm">
                                            <div className="flex justify-between items-center mb-4 border-b border-line pb-3">
                                                <div className="flex items-center gap-2.5">
                                                    <span className="text-[10px] bg-canvas px-2 py-0.5 rounded text-muted font-mono">#{i+1}</span>
                                                    <span className="text-xs font-bold uppercase text-ink">{o.type}</span>
                                                    <span className="text-[10px] text-muted bg-canvas px-1.5 py-0.5 rounded">({o.surfaceId})</span>
                                                    {/* Reparatur 3: Aufbau ohne gültige Fläche nach Geometrieänderung */}
                                                    {istAufbauPruefpflichtig(o.id, aufbautenPruefIds) && <span className="text-[10px] text-amber-700 bg-amber-500/10 border border-amber-500/40 px-1.5 py-0.5 rounded font-bold">⚠ prüfpflichtig</span>}
                                                </div>
                                                <button onClick={()=>setObstacles(obstacles.filter(x=>x.id!==o.id))} className="text-rose-700 hover:text-white hover:bg-rose-500 p-1.5 rounded transition-colors"><Trash2 className="w-4 h-4"/></button>
                                            </div>
                                            {/* Reparatur 9: Sparren-Öffnungs-Verschneidung -> Auswechslung erforderlich/prüfpflichtig (statisch prüfen). */}
                                            {auswechslungInfos[o.id]?.wechselErforderlich && (
                                                <div className={`mb-3 text-[10px] rounded-lg p-2 border ${auswechslungInfos[o.id].pruefpflichtig ? 'text-amber-700 bg-amber-500/10 border-amber-500/40' : 'text-ink bg-canvas border-line'}`}>
                                                    <span className="font-bold">{auswechslungInfos[o.id].pruefpflichtig ? '⚠ Auswechslung prüfpflichtig' : '⚠ Auswechslung erforderlich'}</span>{' · '}{auswechslungInfos[o.id].hinweise.join(' ')}
                                                </div>
                                            )}
                                            <div className={`grid gap-3 ${o.type.includes('gaube') ? 'grid-cols-4' : 'grid-cols-3'}`}>
                                                <InputNumber label="Breite (m)" val={o.width} set={(v:any)=>updateObsDim(o.id, 'width', v)} step="0.1"/>
                                                <InputNumber label="Höhe (m)" val={o.height} set={(v:any)=>updateObsDim(o.id, 'height', v)} step="0.1"/>
                                                <InputNumber label="Tiefe (m)" val={o.depth} set={(v:any)=>updateObsDim(o.id, 'depth', v)} step="0.1"/>
                                                {o.type.includes('gaube') && <InputNumber label="Neigung (°)" val={o.pitch || 15} set={(v:any)=>updateObsDim(o.id, 'pitch', v)} step="1" max={60}/>} 
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* VIEW: BELEGUNG */}
                        {view === 'modules' && (
                            <div className="space-y-6">
                                {/* Reparatur 2: Warnung, wenn eine Geometrieänderung die Belegung entfernt hat. */}
                                {belegungPruefpflichtig && (
                                    <div className="flex items-start gap-2 text-[11px] text-amber-700 bg-amber-500/10 border border-amber-500/40 rounded-lg p-3">
                                        <span className="text-base leading-none">⚠</span>
                                        <span>{BELEGUNG_WARNUNG} <button onClick={handleLayout} className="font-bold underline hover:text-amber-800">Jetzt neu belegen</button></span>
                                    </div>
                                )}
                                <div>
                                    <h3><Zap className="w-4 h-4 text-brand-700 inline mr-2"/> Modul-Datenbank</h3>
                                    <select className="mb-2 w-full bg-white border border-line rounded-lg p-2 text-ink focus:border-brand-600 focus:ring-2 focus:ring-brand-500/40" value={selectedModuleIndex} onChange={(e) => setSelectedModuleIndex(parseInt(e.target.value))}>
                                        {MODULE_TYPES.map((mod, idx) => (
                                            <option key={idx} value={idx}>{mod.manufacturer} {mod.name} ({mod.watts}W)</option>
                                        ))}
                                    </select>

                                    <div className="bg-white p-4 rounded-xl border border-line text-xs text-ink space-y-2 mb-4 shadow-sm">
                                        <div className="flex justify-between border-b border-line pb-2"><span>Leistung (STC):</span><span className="font-bold text-ink">{MODULE_TYPES[selectedModuleIndex].watts} Wp</span></div>
                                        <div className="flex justify-between border-b border-line pb-2"><span>Abmessungen:</span><span className="font-bold text-ink font-mono">{MODULE_TYPES[selectedModuleIndex].width.toFixed(3)} x {MODULE_TYPES[selectedModuleIndex].height.toFixed(3)} m</span></div>
                                        <div className="flex justify-between border-b border-line pb-2"><span>Gewicht:</span><span className="font-bold text-ink font-mono">{MODULE_TYPES[selectedModuleIndex].weight.toFixed(1)} kg</span></div>
                                        <div className="flex justify-between pt-1"><span>Einkaufspreis:</span><span className="font-bold text-mint-700 font-mono">{MODULE_TYPES[selectedModuleIndex].price.toFixed(2)} €</span></div>
                                    </div>

                                    <div className="flex gap-2 mb-4 mt-2">
                                        <button className={`flex-1 p-2 rounded border ${activeSurfaceConfig?.orientation === 'portrait' ? 'bg-canvas text-ink border-brand-500' : 'border-line text-muted'}`} onClick={()=>updateSurfaceConfig('orientation', 'portrait')}>Hochkant</button>
                                        <button className={`flex-1 p-2 rounded border ${activeSurfaceConfig?.orientation === 'landscape' ? 'bg-canvas text-ink border-brand-500' : 'border-line text-muted'}`} onClick={()=>updateSurfaceConfig('orientation', 'landscape')}>Querformat</button>
                                    </div>
                                </div>
                                
                                <div>
                                    <p className="text-xs text-muted mb-4">Die Auto-Belegung berechnet das Raster abzüglich der Sperrzonen.</p>
                                    <div className="grid grid-cols-1 gap-3">
                                        <button className="p-3 rounded bg-brand-500 text-ink hover:bg-brand-600" onClick={handleLayout}><Play className="w-4 h-4 inline mr-2" /> Auto-Belegung Starten</button>
                                        <button className="p-3 rounded border border-line text-rose-700 hover:bg-rose-500 hover:text-white" onClick={handleClear}><Trash2 className="w-4 h-4 inline mr-2" /> PV & UK Löschen</button>
                                    </div>
                                </div>

                                {pvStats.count > 0 && (
                                    <div className="bg-white p-5 rounded-xl border border-sky-500/30 shadow-lg mt-4">
                                        <h3 className="flex items-center gap-2 text-ink font-bold mb-4 text-sm uppercase tracking-widest"><Sun className="w-4 h-4 text-brand-700"/> Anlagen-Ergebnis</h3>
                                        <div className="space-y-4">
                                            <div>
                                                <div className="text-[10px] text-muted uppercase font-bold tracking-wider mb-1">Anzahl Module</div>
                                                <div className="text-3xl font-black text-ink">{pvStats.count} <span className="text-sm font-normal text-muted">Stück</span></div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="bg-canvas p-3 rounded-lg border border-line">
                                                    <div className="text-[10px] text-muted uppercase font-bold tracking-wider mb-1">Gesamtleistung</div>
                                                    <div className="text-lg font-black text-ink">{pvStats.kwp.toFixed(2)} <span className="text-xs font-normal">kWp</span></div>
                                                </div>
                                                <div className="bg-canvas p-3 rounded-lg border border-line">
                                                    <div className="text-[10px] text-muted uppercase font-bold tracking-wider mb-1">Gewicht</div>
                                                    <div className="text-lg font-black text-ink">{(pvStats.weight/1000).toFixed(2)} <span className="text-xs font-normal">t</span></div>
                                                </div>
                                            </div>
                                            <div className="pt-4 border-t border-line">
                                                <div className="flex justify-between items-center">
                                                    <span className="text-[10px] text-muted uppercase font-bold tracking-wider">Modul-Warenwert</span>
                                                    <span className="text-lg font-black text-mint-700 font-mono">{pvStats.price.toLocaleString('de-DE')} €</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* VIEW: BOM */}
                        {view === 'bom' && (
                            <div className="space-y-5">
                                <div className="bg-white p-5 rounded-xl border border-line overflow-x-auto">
                                    <div className="flex items-center gap-2 mb-5 text-xs font-bold text-ink uppercase tracking-wider border-b border-line pb-3">
                                        <Package className="w-4 h-4 text-brand-700"/> Material- & Holzliste
                                    </div>

                                    <div className="mb-4 grid grid-cols-2 gap-4">
                                        <div className="bg-canvas p-3 rounded-lg border border-line">
                                            <div className="text-[10px] text-muted uppercase font-bold">Dachfläche Gesamt</div>
                                            <div className="text-xl font-black text-ink">{totals.area.toFixed(1)} m²</div>
                                        </div>
                                    </div>

                                    <table className="w-full text-left text-xs">
                                        <thead><tr className="text-muted border-b border-line"><th className="py-2.5">Pos</th><th className="py-2.5">Artikel / Beschreibung</th><th className="py-2.5 text-right">Menge</th></tr></thead>
                                        <tbody className="divide-y divide-line">
                                            {bom.map((item, i) => (
                                                <tr key={i}><td className="py-3 text-muted font-mono text-[10px] w-8">{item.pos}</td><td className="py-3 font-medium text-ink">{item.n}</td><td className="py-3 text-right font-bold text-ink">{item.q} <span className="text-[9px] text-muted">{item.u}</span></td></tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                                {/* PV-Montagesystem aus der Eindeckung als Stücklisten-Abschnitt (ohne Mengen). */}
                                <MontageStueckliste cover={cover} coveringFamily={selectedCovering?.family ?? null} tileTypeId={cover === 'ziegel' ? (selectedTile?.tile_type_id ?? null) : null} />
                                <button className="p-3 rounded bg-brand-500 text-ink hover:bg-brand-600 w-full flex items-center justify-center font-bold gap-2" onClick={() => setIsHolzlisteOpen(true)}><Trees className="w-4 h-4"/> Detaillierte Bauteil-Liste</button>
                                <button className="p-3 rounded border border-line text-ink hover:bg-canvas w-full flex items-center justify-center gap-2" onClick={exportDachSVG}><Download className="w-4 h-4"/> 2D-Dachausmittlung (SVG)</button>
                            </div>
                        )}

                        {/* VIEW: WORK */}
                        {view === 'work' && (
                            <div className="space-y-5">
                                <div className="bg-white p-5 rounded-xl border border-line flex items-center justify-between">
                                    <div><div className="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">Kalkulierte Arbeitszeit</div><div className="text-2xl font-black text-ink">{(totals.min/60).toFixed(1)} <span className="text-sm text-muted font-normal">h</span></div></div>
                                    <div className="text-right"><div className="text-[10px] text-muted uppercase font-bold tracking-widest mb-1">Dach Gewicht</div><div className="text-2xl font-black text-ink">{totals.weight.toFixed(1)} <span className="text-sm text-muted font-normal">t</span></div></div>
                                </div>
                                <div className="space-y-4">
                                    {workPlan.map((step, i) => (
                                        <div key={i} className="bg-white p-5 rounded-xl border border-line">
                                            <div className="flex justify-between items-start">
                                                <div className="flex items-start gap-3">
                                                    <span className="bg-brand-500 text-ink w-7 h-7 flex items-center justify-center rounded-lg text-xs font-black mt-0.5">{step.idx}</span>
                                                    <div><div className="font-bold text-sm text-ink">{step.t}</div><div className="text-[11px] text-muted mt-1">{step.d}</div></div>
                                                </div>
                                                <div className="text-xs bg-canvas px-2.5 py-1 rounded-md text-ink font-mono border border-line">{Math.ceil(step.m)} min</div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
                ) : (
                <div className="hidden md:flex flex-col items-center h-full w-16 py-3">
                    <button
                        onClick={() => setIsLeftSidebarOpen(true)}
                        title="DACHDECKER PRO — Seitenleiste ausklappen"
                        className="h-12 w-12 flex items-center justify-center rounded-lg hover:bg-canvas focus:outline-none focus:ring-2 focus:ring-brand-500/40"
                    >
                        <Trees className="text-brand-700 w-6 h-6"/>
                    </button>
                    <div className="w-8 h-px bg-line my-2"/>
                    <div className="flex flex-col items-center gap-1.5 flex-1">
                        {[
                            { id: 'construct', icon: Home, label: 'Haus' },
                            { id: 'obstacles', icon: Box, label: 'Aufbauten' },
                            { id: 'modules', icon: LayoutTemplate, label: 'Belegung' },
                            { id: 'bom', icon: Package, label: 'Holzliste' },
                            { id: 'work', icon: Hammer, label: 'Kalkulation' },
                        ].map(({ id, icon: Icon, label }) => (
                            <button
                                key={id}
                                title={label}
                                onClick={() => { setView(id as ViewMode); setIsLeftSidebarOpen(true); }}
                                className={`w-10 h-10 flex items-center justify-center rounded-md transition-all focus:outline-none focus:ring-2 focus:ring-brand-500/40 border-l-[3px] ${view === id ? 'border-brand-500 text-ink bg-canvas' : 'border-transparent text-muted hover:text-ink hover:bg-canvas'}`}
                            >
                                <Icon className="w-5 h-5"/>
                            </button>
                        ))}
                    </div>
                    <button
                        onClick={() => setIsLeftSidebarOpen(true)}
                        title="Seitenleiste ausklappen"
                        className="h-10 w-10 flex items-center justify-center rounded-md text-muted hover:text-ink hover:bg-canvas focus:outline-none focus:ring-2 focus:ring-brand-500/40"
                    >
                        <ChevronsRight className="w-5 h-5"/>
                    </button>
                </div>
                )}
            </aside>

            <main className="flex-1 bg-canvas relative overflow-hidden" ref={divRef}>
                <div className="absolute top-6 left-1/2 transform -translate-x-1/2 z-30 bg-white/90 p-1.5 rounded-xl border border-line shadow-2xl flex items-center gap-1.5">
                    <ToolBtn active={tool==='select'} onClick={()=>setTool('select')} icon={MousePointer2} label="Markieren" />
                    <div className="w-px h-6 bg-line mx-1"></div>
                    <ToolBtn active={tool==='abbund'} onClick={()=>setTool('abbund')} icon={Hammer} label="Werkstattplan" />
                    <div className="w-px h-6 bg-line mx-1"></div>
                    <ToolBtn active={tool==='move'} onClick={()=>setTool('move')} icon={Move} label="Schieben" />
                    <ToolBtn active={tool==='rotate'} onClick={()=>setTool('rotate')} icon={RotateCcw} label="Drehen" />
                    <ToolBtn active={tool==='delete'} onClick={()=>setTool('delete')} icon={Trash2} label="Löschen" className="text-rose-500 hover:text-rose-700" />
                </div>

                <button onClick={() => { const next = !isLeftSidebarOpen; setIsLeftSidebarOpen(next); if (next && typeof window !== 'undefined' && window.matchMedia('(max-width:767px)').matches) setIsRightSidebarOpen(false); }} className={`absolute top-6 left-6 z-20 bg-white/90 p-2.5 rounded-xl border border-line hover:bg-canvas text-ink hover:text-ink md:hidden`}>
                    {isLeftSidebarOpen ? <PanelLeftClose className="w-5 h-5"/> : <Menu className="w-5 h-5"/>}
                </button>

                <button onClick={() => { const next = !isRightSidebarOpen; setIsRightSidebarOpen(next); if (next && typeof window !== 'undefined' && window.matchMedia('(max-width:767px)').matches) setIsLeftSidebarOpen(false); }} className="absolute top-6 right-6 z-20 bg-white/90 p-2.5 rounded-xl border border-line hover:bg-canvas flex items-center gap-2 text-ink hover:text-ink">
                    {!isRightSidebarOpen && <span className="text-[10px] font-bold uppercase text-muted">Eigenschaften & Ebenen</span>}
                    {isRightSidebarOpen ? <PanelRightClose className="w-5 h-5"/> : <Layers className="w-5 h-5"/>}
                </button>

                <div className="absolute bottom-6 right-6 z-20 flex flex-col gap-2.5">
                    <button onClick={() => engineRef.current?.zoomIn()} className="bg-white/90 p-3 rounded-xl border border-line hover:bg-canvas text-ink"><ZoomIn className="w-5 h-5"/></button>
                    <button onClick={() => engineRef.current?.resetZoom()} className="bg-white/90 p-3 rounded-xl border border-line hover:bg-canvas text-ink"><Maximize className="w-5 h-5"/></button>
                    <button onClick={() => engineRef.current?.zoomOut()} className="bg-white/90 p-3 rounded-xl border border-line hover:bg-canvas text-ink"><ZoomOut className="w-5 h-5"/></button>
                </div>

                <canvas ref={canvasRef} className="absolute inset-0 w-full h-full cursor-move block"/>
            </main>

            <aside className={`bg-white border-l border-line flex flex-col shadow-2xl transition-all duration-300 overflow-hidden absolute inset-y-0 right-0 z-40 md:static md:z-10 ${isRightSidebarOpen ? 'w-[320px] max-w-[85vw] translate-x-0' : 'w-[320px] max-w-[85vw] translate-x-full pointer-events-none md:w-0 md:translate-x-0 md:pointer-events-auto'}`}>
                <div className="w-[320px] h-full flex flex-col">
                    <div className="h-16 flex items-center px-6 border-b border-line bg-canvas justify-between">
                        <div>
                            <div className="font-bold text-lg text-ink leading-tight">Eigenschaften</div>
                            <div className="text-[11px] text-muted">{activeSurface ? (availableSurfaces.find(s => s.id === activeSurface)?.name || 'Auswahl') : 'Projekt-Übersicht'}</div>
                        </div>
                        <button onClick={() => setIsRightSidebarOpen(false)} className="text-muted hover:text-ink"><PanelRightClose className="w-4 h-4"/></button>
                    </div>

                    <div className="flex-1 overflow-y-auto p-6 space-y-6">
                        {/* Reparatur 2: Prüfpflicht-Warnung — sichtbar in Auswahl UND Übersicht; verdeckt nichts. */}
                        {belegungPruefpflichtig && (
                            <div className="flex items-start gap-2 text-[11px] text-amber-700 bg-amber-500/10 border border-amber-500/40 rounded-lg p-3">
                                <span className="text-base leading-none">⚠</span>
                                <span>{BELEGUNG_WARNUNG} Die folgenden PV-Kennzahlen sind dadurch nicht mehr gesichert.</span>
                            </div>
                        )}
                        {activeSurface ? (
                            <div className="space-y-4">
                                <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700 border-b border-line pb-2">
                                    <Square className="w-4 h-4"/> {availableSurfaces.find(s => s.id === activeSurface)?.name || 'Ausgewählte Dachfläche'}
                                </div>
                                <div className="grid grid-cols-2 gap-2.5">
                                    {/* Reparatur 4: echte Flächenmaße statt Gebäude-Gesamtwerte; sonst ehrlich "nicht verfügbar". */}
                                    {(() => {
                                        const sel = availableSurfaces.find(s => s.id === activeSurface);
                                        const hasDim = !!sel && Number.isFinite(sel.width) && Number.isFinite(sel.height);
                                        // Reparatur 6: echte (geneigte) Polygonfläche der Dachfläche, sonst "nicht verfügbar".
                                        const hasArea = !!sel && Number.isFinite(sel.area) && (sel!.area as number) > 0;
                                        return (<>
                                            <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Flächenmaß (B×H)</div><div className="font-mono text-ink text-sm">{hasDim ? `${sel!.width!.toFixed(1)} × ${sel!.height!.toFixed(1)} m` : 'nicht verfügbar'}</div></div>
                                            <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Fläche (geneigt)</div><div className="font-mono text-ink text-sm">{hasArea ? `${(sel!.area as number).toFixed(1)} m²` : 'nicht verfügbar'}</div></div>
                                            <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Flächen-ID</div><div className="font-mono text-ink text-sm">{activeSurface || '–'}</div></div>
                                        </>);
                                    })()}
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Traufhöhe</div><div className="font-mono text-ink text-sm">{build.height.toFixed(1)} m</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Neigung</div><div className="font-mono text-ink text-sm">{build.pitch.toFixed(0)}°</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Ausrichtung Module</div><div className="font-mono text-ink text-sm">{activeSurfaceConfig?.orientation === 'portrait' ? 'Hochkant' : 'Querformat'}</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Überstand Traufe</div><div className="font-mono text-ink text-sm">{build.overhang.toFixed(2)} m</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Überstand Ortgang</div><div className="font-mono text-ink text-sm">{build.overhangGable.toFixed(2)} m</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Sparren (B×H)</div><div className="font-mono text-ink text-sm">{effektivCm(build.rafterWidth, DACH_FLOOR_CM.rafterWidth)}×{effektivCm(build.rafterHeight, DACH_FLOOR_CM.rafterHeight)} cm</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Sparrenabstand</div><div className="font-mono text-ink text-sm">{effektivCm(build.rafterSpacing, DACH_FLOOR_CM.rafterSpacing)} cm</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Lattenabstand</div><div className="font-mono text-ink text-sm">{effektivCm(build.battenDist, DACH_FLOOR_CM.battenDist)} cm</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Module auf Fläche</div><div className="font-mono text-ink text-sm">{modules.filter(m => m.surfaceId === activeSurface).length} Stk · {((modules.filter(m => m.surfaceId === activeSurface).length * (MODULE_TYPES[selectedModuleIndex]?.watts || 0)) / 1000).toFixed(2)} kWp</div></div>
                                </div>

                                {(topologyAnalysis.grate > 0 || topologyAnalysis.kehlen > 0 || topologyAnalysis.ortgaenge > 0) && (
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line space-y-1">
                                        <div className="text-[10px] uppercase text-muted tracking-wider">Topologie (Anschlüsse)</div>
                                        <div className="text-xs text-ink">Grate: <span className="font-mono">{topologyAnalysis.grate}</span> · Kehlen: <span className="font-mono">{topologyAnalysis.kehlen}</span> · Ortgänge: <span className="font-mono">{topologyAnalysis.ortgaenge}</span></div>
                                    </div>
                                )}

                                {(() => {
                                    const hinweise: string[] = [];
                                    if (build.category === 'pitched' && build.pitch < 10) hinweise.push('Dachneigung sehr gering — Mindestneigung der Eindeckung prüfen.');
                                    if (build.category === 'pitched' && build.overhang === 0) hinweise.push('Kein Traufüberstand — Schlagregenschutz prüfen.');
                                    if (modules.filter(m => m.surfaceId === activeSurface).length === 0) hinweise.push('Noch keine Module auf dieser Fläche belegt.');
                                    return hinweise.length > 0 ? (
                                        <div className="bg-amber-500/10 border border-amber-500/30 rounded-lg p-3 space-y-2">
                                            <div className="text-[10px] uppercase text-amber-700 tracking-wider font-bold">Hinweise</div>
                                            <ul className="text-xs text-amber-700 list-disc pl-4 space-y-1">
                                                {hinweise.map((h, i) => <li key={i}>{h}</li>)}
                                            </ul>
                                            <button onClick={() => { setView('modules'); setIsLeftSidebarOpen(true); }} className="text-[11px] font-bold uppercase text-brand-700 hover:underline">→ Zur Belegung</button>
                                        </div>
                                    ) : null;
                                })()}

                                {abbundData && (
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line space-y-1">
                                        <div className="text-[10px] uppercase text-muted tracking-wider">Abbund (zuletzt gewählt)</div>
                                        <div className="text-xs text-ink font-mono">{(abbundData.breite*100).toFixed(1)}×{(abbundData.hoehe*100).toFixed(1)} cm · {abbundData.laenge.toFixed(2)} m{abbundData.neigung ? ` · ${(abbundData.neigung*180/Math.PI).toFixed(1)}°` : ''}</div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className="space-y-4">
                                <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700 border-b border-line pb-2">
                                    <ListChecks className="w-4 h-4"/> Projekt-Übersicht
                                </div>
                                <div className="grid grid-cols-2 gap-2.5">
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Projektstatus</div><div className="font-mono text-ink text-sm">Entwurf</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Dachform</div><div className="font-mono text-ink text-sm">{SHAPE_LABELS[build.shape] || build.shape}</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line col-span-2"><div className="text-[10px] uppercase text-muted tracking-wider">Gebäudemaße (L×B×H)</div><div className="font-mono text-ink text-sm">{build.length.toFixed(1)} × {build.width.toFixed(1)} × {build.height.toFixed(1)} m</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Dachneigung</div><div className="font-mono text-ink text-sm">{build.pitch.toFixed(0)}°</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Eindeckung</div><div className="font-mono text-ink text-sm">{COVER_LABELS[cover] || cover}</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Module</div><div className="font-mono text-ink text-sm">{modules.length} Stk</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Geschätzte Leistung</div><div className="font-mono text-ink text-sm">{pvStats.kwp.toFixed(2)} kWp</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Aufbauten</div><div className="font-mono text-ink text-sm">{obstacles.length} Stk{aufbautenPruefIds.length > 0 && <span className="text-amber-700 font-bold"> · {aufbautenPruefIds.length} ⚠</span>}</div></div>
                                    <div className="bg-canvas p-2.5 rounded-lg border border-line"><div className="text-[10px] uppercase text-muted tracking-wider">Dachflächen</div><div className="font-mono text-ink text-sm">{availableSurfaces.length}</div></div>
                                </div>

                                {(() => {
                                    const hinweise: string[] = [];
                                    if (modules.length === 0) hinweise.push('Noch keine Module belegt — nächster Schritt: Belegung.');
                                    if (build.category === 'pitched' && build.pitch < 10) hinweise.push('Dachneigung sehr gering — Mindestneigung der Eindeckung prüfen.');
                                    if (obstacles.length === 0) hinweise.push('Keine Dachaufbauten erfasst (optional).');
                                    return hinweise.length > 0 ? (
                                        <div className="bg-amber-500/10 border border-amber-500/30 rounded-lg p-3 space-y-2">
                                            <div className="text-[10px] uppercase text-amber-700 tracking-wider font-bold">Offene Hinweise</div>
                                            <ul className="text-xs text-amber-700 list-disc pl-4 space-y-1">
                                                {hinweise.map((h, i) => <li key={i}>{h}</li>)}
                                            </ul>
                                        </div>
                                    ) : null;
                                })()}

                                {modules.length === 0 && (
                                    <button onClick={() => { setView('modules'); setIsLeftSidebarOpen(true); }} className="w-full py-2.5 rounded-lg bg-brand-500 text-ink text-xs font-bold uppercase tracking-wide hover:bg-brand-600 transition-colors">
                                        Belegung öffnen
                                    </button>
                                )}
                            </div>
                        )}

                        <div className="pt-5 border-t border-line space-y-6">
                            <Label text="Ebenen & Sichtbarkeit"/>
                            <div className="space-y-2">
                                {layers.filter(l => !l.deleted).map((layer, index) => (
                                    <div key={layer.id} className="flex justify-between items-center bg-canvas p-2.5 rounded-lg border border-line">
                                        <span className="text-xs font-bold text-ink">{layer.name}</span>
                                        <button onClick={() => handleToggleLayerVisibility(layer.id)} className={`p-1.5 rounded transition-colors ${layer.visible ? 'text-brand-700' : 'text-muted'}`}>{layer.visible ? <Eye className="w-4 h-4"/> : <EyeOff className="w-4 h-4"/>}</button>
                                    </div>
                                ))}
                            </div>
                            <div className="pt-5 border-t border-line space-y-4">
                                <Range label="Transparenz Eindeckung" val={globalOpacity} set={setGlobalOpacity} min={0} max={1} unit=""/>
                                <Range label="Explosionsansicht" val={build.layerSpread} set={(v:any)=>setBuild({...build, layerSpread:v})} min={0} max={2} unit="m" />
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            
            {/* MODALS */}
            {isHolzlisteOpen && (
                <div className="modal-overlay" style={{ display: 'flex', position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.8)', zIndex: 50, alignItems: 'center', justifyContent: 'center' }}>
                    <div className="modal-box" style={{ width: '90%', maxWidth: '800px', backgroundColor: '#ffffff', borderRadius: '1rem', border: '1px solid #e5e7eb', maxHeight: '90vh', display: 'flex', flexDirection: 'column' }}>
                        <div className="modal-header" style={{ padding: '1rem 1.5rem', borderBottom: '1px solid #e5e7eb', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h2 className="text-lg font-bold text-ink flex items-center gap-2"><Trees className="text-brand-700"/> Holzliste (Abbund)</h2>
                            <button onClick={() => setIsHolzlisteOpen(false)} className="text-muted hover:text-ink"><X className="w-5 h-5"/></button>
                        </div>
                        <div className="modal-body custom-scrollbar" style={{ padding: '1.5rem', overflowY: 'auto' }}>
                            <table className="w-full text-left text-xs text-ink">
                                <thead>
                                    <tr className="border-b border-line text-muted">
                                        <th className="pb-2">Bauteil</th><th className="pb-2">Anz</th><th className="pb-2">B x H (cm)</th><th className="pb-2">Länge (m)</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-line">
                                    {aggrHolzliste.map((h, i) => (
                                        <tr key={i} className="hover:bg-canvas">
                                            <td className="py-2">{h.name} <span className="block text-[9px] text-muted uppercase">{h.type}</span></td>
                                            <td className="py-2 font-bold">{h.anzahl}</td>
                                            <td className="py-2 font-mono">{(h.breite*100).toFixed(0)}x{(h.hoehe*100).toFixed(0)}</td>
                                            <td className="py-2 font-mono text-ink font-bold">{h.laenge.toFixed(3)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {abbundData && (
                <div className="modal-overlay" style={{ display: 'flex', position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.8)', zIndex: 50, alignItems: 'center', justifyContent: 'center' }}>
                    <div className="modal-box flex flex-col" style={{ width: '95%', maxWidth: '1400px', height: '90vh', backgroundColor: '#ffffff', borderRadius: '1rem', border: '1px solid #e5e7eb' }}>
                        <div className="modal-header" style={{ padding: '1rem 1.5rem', borderBottom: '1px solid #e5e7eb', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h2 className="text-lg font-bold text-ink flex items-center gap-2"><Hammer className="text-ink"/> Werkstattplan: <span className="text-sky-700">{abbundData.name}</span></h2>
                            <button onClick={() => setAbbundData(null)} className="text-muted hover:text-ink"><X className="w-5 h-5"/></button>
                        </div>
                        <div className="flex flex-1 overflow-hidden bg-canvas rounded-b-2xl">
                            <div className="flex-1 relative bg-canvas flex items-center justify-center border-r border-line">
                                <canvas ref={abbundCanvasRef} width={1000} height={500} className="max-w-full max-h-full"></canvas>
                            </div>
                            <div className="w-80 p-6 bg-white space-y-4 overflow-y-auto">
                                <h3 className="text-xs font-bold text-muted uppercase tracking-wider border-b border-line pb-2">Bauteil-Daten</h3>
                                <div className="space-y-3 text-sm">
                                    <div><span className="text-muted block text-[10px] uppercase">Querschnitt</span><strong className="text-ink font-mono">{(abbundData.breite*100).toFixed(1)} x {(abbundData.hoehe*100).toFixed(1)} cm</strong></div>
                                    <div><span className="text-muted block text-[10px] uppercase">Gesamtlänge</span><strong className="text-ink text-lg font-mono font-bold">{abbundData.laenge.toFixed(3)} m</strong></div>
                                    <div className="pt-3 border-t border-line"><span className="text-muted block text-[10px] uppercase">Kappwinkel (Lotriss)</span><strong className="text-ink font-mono">{abbundData.neigung ? (abbundData.neigung*180/Math.PI).toFixed(1) + '°' : '-'}</strong></div>
                                    <div><span className="text-muted block text-[10px] uppercase">Waageriss</span><strong className="text-ink font-mono">{abbundData.neigung ? (90 - Math.abs(abbundData.neigung*180/Math.PI)).toFixed(1) + '°' : '-'}</strong></div>
                                    <div className="pt-3 border-t border-line"><span className="text-muted block text-[10px] uppercase">Kerventiefe (Einschnitt)</span><strong className="text-ink font-mono">{abbundData.kervenTiefe ? (abbundData.kervenTiefe*100).toFixed(1) + ' cm' : '-'}</strong></div>
                                    <div><span className="text-muted block text-[10px] uppercase">Kervenabstand (Traufe)</span><strong className="text-ink font-mono">{abbundData.kervenAbstand ? abbundData.kervenAbstand.toFixed(3) + ' m' : '-'}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {(isLeftSidebarOpen || isRightSidebarOpen) && (
                <div className="fixed inset-0 bg-black/30 z-30 md:hidden" onClick={() => { setIsLeftSidebarOpen(false); setIsRightSidebarOpen(false); }}/>
            )}
        </div>
    );
}

// --- UI HELPERS ---
const InputNumber = ({label, val, set, step="1", max, min}:any) => {
    // Reparatur 1: lokaler Tipp-Puffer. Leere oder nicht berechenbare Eingaben (NaN)
    // werden NICHT in den State geschrieben -> der letzte gültige Wert bleibt erhalten,
    // die Berechnung kann nicht mehr durch NaN kaputtgehen. Freies Tippen (auch "1.")
    // bleibt möglich; der Puffer wird nur bei echter Wertänderung von außen neu gesetzt.
    const [buf, setBuf] = useState(String(val ?? ''));
    useEffect(() => { if (parseFloat(buf) !== val) setBuf(String(val ?? '')); }, [val]);
    const aendern = (raw:string) => {
        setBuf(raw);
        const n = parseFloat(raw);
        if (raw.trim() === '' || isNaN(n)) return; // ungültig -> letzten gültigen Wert behalten
        set(n);
    };
    const verlassen = () => { const n = parseFloat(buf); if (buf.trim() === '' || isNaN(n)) setBuf(String(val ?? '')); };
    return (
        <div>
            <div className="text-[9px] text-muted mb-1 uppercase font-bold tracking-wider">{label}</div>
            <input type="number" step={step} max={max} min={min} value={buf} onChange={e=>aendern(e.target.value)} onBlur={verlassen} className="w-full bg-white border border-line rounded-lg p-2 text-xs font-mono text-ink outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-500/40" />
        </div>
    );
};

const Label = ({text}: {text: string}) => <div className="text-[10px] font-bold text-muted uppercase mb-2 tracking-widest">{text}</div>;

const NavBtn = ({id, icon:Icon, label, active, set}:any) => (
    <button onClick={()=>set(id)} className={`flex-1 py-3 flex flex-col items-center gap-1.5 border-b-[3px] transition-all duration-200 ${active===id ? 'border-brand-500 text-ink bg-white' : 'border-transparent text-muted hover:text-ink hover:bg-canvas'}`}>
        <Icon className={`w-4 h-4`}/> <span className="text-[9px] tracking-wide">{label}</span>
    </button>
);

const ModeBtn = ({active, onClick, label, icon:Icon}:any) => (
    <button onClick={onClick} className={`flex-1 py-2.5 text-xs font-bold rounded-md flex items-center justify-center gap-2 transition-all ${active?'bg-white text-ink shadow-sm border border-line':'bg-transparent text-muted hover:bg-canvas border border-transparent'}`}>
        {Icon && <Icon className={`w-3.5 h-3.5 ${active ? 'text-brand-700' : ''}`}/>} {label}
    </button>
);

const ToolBtn = ({active, onClick, label, icon:Icon, className}:any) => (
    <button onClick={onClick} className={`px-4 py-2.5 text-xs font-bold rounded-lg flex items-center justify-center gap-2 transition-all ${active?'bg-brand-500 text-ink':(className || 'text-muted hover:bg-canvas hover:text-ink')}`} title={label}>
        <Icon className="w-4 h-4"/> <span className="hidden sm:inline tracking-wide">{label}</span>
    </button>
);

const ShapeBtn = ({active, onClick, label}:any) => (
    <button onClick={onClick} className={`p-2.5 rounded-lg text-xs font-bold border transition-all shadow-sm ${active ? 'bg-canvas border-brand-500 text-brand-700' : 'bg-white border-line text-muted hover:bg-canvas hover:text-ink'}`}>
        {label}
    </button>
);

// Filter-Chip der Dachform-Vorlagenbibliothek (additiv, eigenes kleines Bedienelement).
const VorlagenChip = ({active, onClick, label}:any) => (
    <button onClick={onClick} className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border transition-all ${active ? 'bg-brand-500 text-ink border-brand-500' : 'bg-white text-muted border-line hover:bg-canvas hover:text-ink'}`}>
        {label}
    </button>
);

const ObsBtn = ({onClick, icon:Icon, label, color}:any) => (
    <button onClick={onClick} className="p-3 bg-white hover:bg-canvas rounded-lg flex flex-col items-center gap-2 border border-line hover:border-brand-500 shadow-sm">
        <Icon className={`w-5 h-5 ${color}`}/> <span className="text-[10px] font-bold uppercase tracking-wider text-muted text-center leading-tight">{label}</span>
    </button>
);

const GaubeBtn = ({onClick, icon:Icon, label, color}:any) => (
    <button onClick={onClick} className="p-2.5 bg-white hover:bg-canvas rounded-lg flex flex-col items-center gap-1.5 border border-line hover:border-brand-500 shadow-sm">
        <Icon className={`w-4 h-4 ${color}`}/> <span className="text-[9px] font-bold uppercase tracking-wider text-muted text-center leading-tight">{label}</span>
    </button>
);

const Range = ({label, val, set, min, max, unit='m'}:any) => (
    <div>
        <div className="flex justify-between text-[10px] font-bold uppercase tracking-wider text-muted mb-2"><span>{label}</span><span className="text-ink font-mono bg-canvas px-1.5 rounded border border-line shadow-inner">{val.toFixed(2)}{unit}</span></div>
        <input type="range" min={min} max={max} step={unit==='m'?0.1:1} value={val} onChange={e=>set(parseFloat(e.target.value))} className="w-full h-1.5 bg-line rounded-lg appearance-none cursor-pointer accent-brand-500"/>
    </div>
);

// --- ANZEIGE-LABELS (nur Read-Out fuer die Kontext-Seitenleiste; kein State, kein Shadowing) ---
const SHAPE_LABELS: Record<string, string> = {
    sattel: 'Satteldach', pult: 'Pultdach', walm: 'Walmdach',
    rect: 'Flachdach', 'l-shape': 'L-Form', 't-shape': 'T-Form',
    'sattel-l': 'Satteldach L-Form', 'sattel-t': 'Satteldach T-Form',
    'walm-l': 'Walmdach L-Form', 'walm-t': 'Walmdach T-Form',
};
const COVER_LABELS: Record<string, string> = {
    ziegel: 'Ziegel', schiefer: 'Schiefer', trapezblech: 'Trapezblech',
    bitumen: 'Bitumen', kunststoff: 'Kunststoff', gruendach: 'Gründach', kies: 'Kies',
};
