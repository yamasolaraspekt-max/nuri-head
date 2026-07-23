/**
 * Hausplaner P1b — 3D-Szenenaufbau (rohes three.js nach RoofEngine-Muster, ▲P1-K1).
 *
 * Der Renderer ist der ZWEITE Renderer DERSELBEN Daten: er hält KEINE Kopie der Szene
 * (nur eine Referenz auf das unveränderliche Store-Dokument fürs Neuzeichnen — keine
 * zweite Wahrheit) und erzeugt keinerlei Commands. Meshes werden bei jedem Update aus
 * dem Dokument NEU abgeleitet: Wände über segmentiereWand → platziereWandQuader
 * (ein Mesh je Quader, Kante 7), Böden aus den erkannten Räumen (ZoneNode room).
 *
 * Kanten (Spec §3): leere Szene ⇒ Grid + Default-Kamera statt NaN-fitToScene (5);
 * Level ohne Räume ⇒ Wände ohne Boden, kein Fehler (4); überlappende Wände werden
 * NICHT versetzt — sichtbares Flackern ist der ehrliche Zustand (3); dispose() gibt
 * WebGL-Ressourcen vollständig frei (6). Klemm-Segmente (Kante 2) rendern sichtbar
 * in Warnfarbe — nie stillschweigend „passend gerechnet".
 *
 * Farben (UX-Rahmen): neutral — Wände Grau, Böden hell; Auswahl = Marken-Grün #93c21c;
 * Klemm-Markierung Amber. Keine Statusfarben-Zweckentfremdung.
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';
import type { ObjectNode, OpeningNode, SceneDocument, WallNode, RoofNode } from '../../domain/scene.types';
import type { RendererAdapter } from './adapter';
import { weltZuThree } from './adapter';
import { segmentiereWand } from './segmentierung';
import { platziereWandQuader, bodenPunkteThree, platziereTreppenStufe } from './platzierung';
import { treppe3DKoerper } from '../../geometry/treppe3D';
import { parametereZuTreppe } from '../../geometry/treppeObjekt';
import { erkenneRaeume } from '../../geometry/roomDetection';
import { dachMeshWelt, dachflaechen, type DachFlaeche } from './dachMesh';
import { flaecheZuFrame, aufbauKoerper, type AufbauFrame } from './dachAufbautenMesh';
import { DachGeometrieUngueltig } from '../../geometry/dachGeometrie';

/**
 * Render-Welle 1: Sonnenrichtung als reine Funktion (unit-testbar ohne WebGL).
 *
 * Liefert die NORMIERTE Richtung Szene→Sonne im three-Raum (y-up; Welt-Nord = +y ⇒ three −z, ▲K2).
 * `northAngleGrad`: Drehung des wahren Nordens gegenüber Welt-+y in Grad, im Uhrzeigersinn —
 * heutige SceneDocuments TRAGEN DIESES FELD NICHT (Ist-Beleg scene.types.ts), der Parameter ist
 * die Andock-Naht für eine spätere Welle. `geo.latitude` verfeinert nur die Sonnenhöhe
 * (Äquinoktium-Mittag 90°−|Breite|, geklemmt auf 10–75°). Fallback ohne Werte: Süd, 35° Höhe
 * (deutscher Jahresmittel-Kompromiss, Spec §3.2). Ungültige Eingaben (NaN/∞) fallen auf den
 * Fallback zurück — nie NaN in der Lichtposition (Kante Spec §5).
 */
export function sonnenRichtung(
  northAngleGrad?: number | null,
  geo?: { latitude: number } | null,
): THREE.Vector3 {
  const nord = Number.isFinite(northAngleGrad as number) ? (northAngleGrad as number) : 0;
  const hoeheGrad = geo && Number.isFinite(geo.latitude)
    ? Math.min(75, Math.max(10, 90 - Math.abs(geo.latitude)))
    : 35;

  // Kompass-Azimut der Sonne: 180° = Süd; northAngle verschiebt den Kompass gegenüber Welt-+y.
  const azimutRad = ((180 + nord) * Math.PI) / 180;
  const hoeheRad = (hoeheGrad * Math.PI) / 180;

  // Welt (x=Ost, y=Nord, z=Höhe) → three (x, y=Höhe, z=−Welt-y), Achsabbildung wie weltZuThree.
  const weltX = Math.sin(azimutRad) * Math.cos(hoeheRad);
  const weltY = Math.cos(azimutRad) * Math.cos(hoeheRad);
  const weltZ = Math.sin(hoeheRad);
  return new THREE.Vector3(weltX, weltZ, -weltY).normalize();
}

const FARBE_WAND = 0xd9dee5;      // heller Putz — Form über Schatten + Kanten
const FARBE_BODEN = 0xe3d8c4;     // warmer, klar erkennbarer Bodenton (hebt sich von Wänden/Hintergrund ab)
const FARBE_DECKE = 0xeef0f2;     // helle, kühle Decke — nur von unten/innen sichtbar (Rückseiten-Culling)
const FARBE_DACH = 0xc0895f;      // Terrakotta/Braun (neutral, keine Statusfarbe)
const FARBE_AUSWAHL = 0xa3e635;   // Marken-/Akzent-Grün (einzige Akzentfarbe)
const FARBE_GEKLEMMT = 0xe8b93c;  // Amber — Kante 2 sichtbar markiert

export class HausplanerDreiDSzene implements RendererAdapter {
  private readonly container: HTMLElement;
  private readonly renderer: THREE.WebGLRenderer;
  private readonly szene: THREE.Scene;
  private readonly kamera: THREE.PerspectiveCamera;
  private readonly steuerung: OrbitControls;
  private readonly inhalt: THREE.Group;          // alle abgeleiteten Meshes (je Update neu)
  private readonly beobachter: ResizeObserver;

  /** Referenz aufs unveränderliche Store-Dokument (KEINE Kopie — eine Wahrheit bleibt der Store). */
  private dokument: SceneDocument | null = null;
  private aktivesLevelId: string | null = null;
  private ausgewaehlt = new Set<string>();
  private rafId = 0;
  private readonly aufKlickAuswahl?: (nodeId: string | null) => void;
  /** Render-Welle 1: PMREM-Ziel der RoomEnvironment — im dispose() freigeben (kein GPU-Leak, Kante Spec §5). */
  private readonly umgebung: THREE.WebGLRenderTarget;

  constructor(container: HTMLElement, aufKlickAuswahl?: (nodeId: string | null) => void) {
    this.container = container;
    this.aufKlickAuswahl = aufKlickAuswahl;

    this.renderer = new THREE.WebGLRenderer({ antialias: true });
    this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    this.renderer.setSize(container.clientWidth || 1, container.clientHeight || 1);
    this.renderer.shadowMap.enabled = true;                   // Pro-CAD: echte Schlagschatten
    this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    this.renderer.toneMapping = THREE.ACESFilmicToneMapping;  // filmisches Tone-Mapping (Kontrast/Farbe)
    this.renderer.toneMappingExposure = 1.05;
    container.appendChild(this.renderer.domElement);

    this.szene = new THREE.Scene();
    this.szene.background = new THREE.Color(0xeef1f5);        // helles CI-Studio
    this.szene.fog = new THREE.Fog(0xeef1f5, 70, 170);       // dezente Tiefenstaffelung

    // Render-Welle 1 (A3): prozedurale Umgebung via PMREM + RoomEnvironment — Reflexionen +
    // indirektes Licht, ohne HDRI-Asset. background bleibt bewusst die helle CI-Farbe
    // (Studio-Charakter, Sichtprobe-Entscheidung); die Intensität ist gedrosselt, damit die
    // bestehende Licht-Balance (A4: ACES + Exposure 1.05) nicht absäuft.
    const pmrem = new THREE.PMREMGenerator(this.renderer);
    this.umgebung = pmrem.fromScene(new RoomEnvironment(), 0.04);
    pmrem.dispose();
    this.szene.environment = this.umgebung.texture;
    this.szene.environmentIntensity = 0.55;

    this.kamera = new THREE.PerspectiveCamera(
      50,
      (container.clientWidth || 1) / (container.clientHeight || 1),
      0.01,
      500,
    );
    this.setzeStandardKamera();

    this.steuerung = new OrbitControls(this.kamera, this.renderer.domElement);
    this.steuerung.enableDamping = true;

    // Pro-CAD-Beleuchtung: kühles Himmels-/Bodenlicht + schattenwerfendes Hauptlicht + Fülllicht.
    this.szene.add(new THREE.HemisphereLight(0xffffff, 0xc9ced5, 0.9));
    // Render-Welle 1 (A3): Sonne aus der Szene abgeleitet statt fix. Heutige Dokumente tragen
    // weder northAngle noch geolocation (Ist-Beleg scene.types.ts) ⇒ Fallback Süd/35° greift;
    // die Parameter-Naht steht für die spätere Welle. Distanz 31 ≈ bisheriger Betrag |(16,24,12)|,
    // damit das bestehende Schatten-Frustum (±35, far 90) die Szene weiter umschließt (Spec §3.3).
    const hauptlicht = new THREE.DirectionalLight(0xffffff, 2.0);
    hauptlicht.position.copy(sonnenRichtung().multiplyScalar(31));
    hauptlicht.castShadow = true;
    hauptlicht.shadow.mapSize.set(2048, 2048);
    hauptlicht.shadow.camera.near = 1;
    hauptlicht.shadow.camera.far = 90;
    hauptlicht.shadow.camera.left = -35;
    hauptlicht.shadow.camera.right = 35;
    hauptlicht.shadow.camera.top = 35;
    hauptlicht.shadow.camera.bottom = -35;
    hauptlicht.shadow.bias = -0.0004;
    this.szene.add(hauptlicht);
    const fuelllicht = new THREE.DirectionalLight(0xffffff, 0.8);
    fuelllicht.position.set(-14, 10, -12);
    this.szene.add(fuelllicht);

    // Dunkler Boden (nimmt Schatten auf) + feines technisches Raster.
    const bodenFlaeche = new THREE.Mesh(
      new THREE.PlaneGeometry(240, 240),
      new THREE.MeshStandardMaterial({ color: 0xe6e9ee, roughness: 1, metalness: 0 }),
    );
    bodenFlaeche.rotation.x = -Math.PI / 2;
    bodenFlaeche.position.y = -0.002;
    bodenFlaeche.receiveShadow = true;
    this.szene.add(bodenFlaeche);
    const raster = new THREE.GridHelper(80, 80, 0xcfd6de, 0xe2e6ea);
    (raster.material as THREE.Material).transparent = true;
    (raster.material as THREE.Material).opacity = 0.5;
    this.szene.add(raster);

    this.inhalt = new THREE.Group();
    this.szene.add(this.inhalt);

    this.renderer.domElement.addEventListener('pointerdown', this.klick);

    this.beobachter = new ResizeObserver(() => this.groesseAnpassen());
    this.beobachter.observe(container);

    const schleife = () => {
      this.rafId = requestAnimationFrame(schleife);
      this.steuerung.update();
      this.renderer.render(this.szene, this.kamera);
    };
    schleife();
  }

  // ---------------------------------------------------------------- Adapter-Naht

  /** Dokument/Selektion übernehmen und Meshes NEU ableiten (der Store bleibt die Wahrheit). */
  aktualisiere(dokument: SceneDocument, aktivesLevelId: string, ausgewaehlteIds: string[]): void {
    this.dokument = dokument;
    this.aktivesLevelId = aktivesLevelId;
    this.ausgewaehlt = new Set(ausgewaehlteIds);
    this.baueInhalt();
  }

  focusNode(nodeId: string): void {
    const ziel = this.inhalt.children.find((m) => m.userData.nodeId === nodeId);
    if (ziel) {
      const box = new THREE.Box3().setFromObject(ziel);
      this.steuerung.target.copy(box.getCenter(new THREE.Vector3()));
    }
  }

  fitToScene(): void {
    const box = new THREE.Box3().setFromObject(this.inhalt);
    if (box.isEmpty()) {
      this.setzeStandardKamera(); // Kante 5: leere Szene ⇒ Default statt NaN
      return;
    }
    const zentrum = box.getCenter(new THREE.Vector3());
    const groesse = box.getSize(new THREE.Vector3());
    const radius = Math.max(groesse.x, groesse.y, groesse.z, 1);
    this.kamera.position.set(zentrum.x + radius * 1.2, zentrum.y + radius, zentrum.z + radius * 1.2);
    this.steuerung.target.copy(zentrum);
    this.kamera.lookAt(zentrum);
  }

  setActiveLevel(levelId: string): void {
    this.aktivesLevelId = levelId;
    this.baueInhalt();
  }

  /** Kante 6: WebGL-Ressourcen vollständig freigeben (Speicherleck beim Verlassen). */
  dispose(): void {
    cancelAnimationFrame(this.rafId);
    this.beobachter.disconnect();
    this.renderer.domElement.removeEventListener('pointerdown', this.klick);
    this.leereInhalt();
    this.szene.environment = null;
    this.umgebung.dispose(); // Render-Welle 1: PMREM-Ziel freigeben (Kante Spec §5, Re-Mount ohne GPU-Leak)
    this.steuerung.dispose();
    this.renderer.dispose();
    this.renderer.domElement.remove();
    this.dokument = null;
  }

  // ---------------------------------------------------------------- Aufbau

  private setzeStandardKamera(): void {
    this.kamera.position.set(10, 8, 10);
    this.kamera.lookAt(0, 0, 0);
  }

  private groesseAnpassen(): void {
    const b = this.container.clientWidth || 1;
    const h = this.container.clientHeight || 1;
    this.kamera.aspect = b / h;
    this.kamera.updateProjectionMatrix();
    this.renderer.setSize(b, h);
  }

  private leereInhalt(): void {
    for (const kind of [...this.inhalt.children]) {
      this.inhalt.remove(kind);
      kind.traverse((obj) => {
        if ((obj as THREE.Mesh).geometry) {
          const mesh = obj as THREE.Mesh;
          mesh.geometry.dispose();
          const mat = mesh.material;
          (Array.isArray(mat) ? mat : [mat]).forEach((m) => m.dispose());
        }
      });
    }
  }

  private baueInhalt(): void {
    this.leereInhalt();
    const dokument = this.dokument;
    if (!dokument || !this.aktivesLevelId) {
      return; // Kante 5: nur Grid + Licht bleiben — ehrlicher Leerzustand
    }
    const level = dokument.levels.find((l) => l.id === this.aktivesLevelId);
    if (!level) {
      return;
    }

    const knoten = dokument.nodes.filter((n) => n.levelId === level.id && n.visible !== false);
    const waende = knoten.filter((n): n is WallNode => n.type === 'wall');
    const oeffnungen = knoten.filter(
      (n): n is OpeningNode => n.type === 'window' || n.type === 'door' || n.type === 'opening',
    );
    // Böden abgeleitet aus derselben Raumerkennung wie im 2D (Räume = Projektion der Wände,
    // keine zweite Wahrheit). Level ohne geschlossenen Ring ⇒ keine Räume ⇒ keine Böden (Kante 4).
    const raeume = erkenneRaeume(waende, level.defaultWallHeight).map((r, i) => ({ id: `raum-${level.id}-${i}`, polygon: r.polygon }));

    // Wände: ein Mesh je Quader (Kante 7); Klemm-Segmente in Warnfarbe (Kante 2).
    for (const wand of waende) {
      const eigene = oeffnungen.filter((o) => o.hostWallId === wand.id);
      const segmentiert = segmentiereWand(wand, eigene);
      for (const quader of segmentiert.quader) {
        const p = platziereWandQuader(wand, quader, level.elevation);
        const farbe = this.ausgewaehlt.has(wand.id)
          ? FARBE_AUSWAHL
          : p.geklemmt ? FARBE_GEKLEMMT : FARBE_WAND;
        const mesh = new THREE.Mesh(
          new THREE.BoxGeometry(p.masse.x, p.masse.y, p.masse.z),
          new THREE.MeshStandardMaterial({ color: farbe, roughness: 0.72, metalness: 0.02 }),
        );
        mesh.position.set(p.zentrum.x, p.zentrum.y, p.zentrum.z);
        mesh.rotation.y = p.rotationY;
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        mesh.userData.nodeId = wand.id;
        const kanten = new THREE.LineSegments(
          new THREE.EdgesGeometry(mesh.geometry),
          new THREE.LineBasicMaterial({ color: 0x64748b, transparent: true, opacity: 0.32 }),
        );
        mesh.add(kanten);
        this.inhalt.add(mesh);
      }
    }

    // Treppen (objectType 'stair'): je Stufe ein Quader (wie Waende), Placement ueber platziereTreppenStufe.
    const treppen = knoten.filter((n): n is ObjectNode => n.type === 'object' && n.objectType === 'stair');
    for (const treppe of treppen) {
      const tp = parametereZuTreppe(treppe.parameters);
      if (!tp) continue;
      const koerper = treppe3DKoerper({
        laufbreite: tp.laufbreite,
        geschosshoehe: tp.geschosshoehe,
        verfuegbareLauflaenge: Math.hypot(tp.endX - tp.startX, tp.endY - tp.startY) || undefined,
        gewuenschteSteigung: tp.gewuenschteSteigung,
        bereich: tp.bereich,
      });
      const farbe = this.ausgewaehlt.has(treppe.id) ? FARBE_AUSWAHL : FARBE_WAND;
      for (const stufe of koerper.stufen) {
        const pl = platziereTreppenStufe({ x: tp.startX, y: tp.startY }, { x: tp.endX, y: tp.endY }, stufe, level.elevation);
        const mesh = new THREE.Mesh(
          new THREE.BoxGeometry(pl.masse.x, pl.masse.y, pl.masse.z),
          new THREE.MeshStandardMaterial({ color: farbe, roughness: 0.72, metalness: 0.02 }),
        );
        mesh.position.set(pl.zentrum.x, pl.zentrum.y, pl.zentrum.z);
        mesh.rotation.y = pl.rotationY;
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        mesh.userData.nodeId = treppe.id;
        this.inhalt.add(mesh);
      }
    }

    // Böden aus erkannten Räumen (Level ohne Räume ⇒ einfach keine Böden, Kante 4).
    for (const raum of raeume) {
      if (raum.polygon.length < 3) {
        continue;
      }
      const boden = bodenPunkteThree(raum.polygon, level.elevation);
      const form = new THREE.Shape();
      boden.punkte.forEach((p, i) => (i === 0 ? form.moveTo(p.x, -p.z) : form.lineTo(p.x, -p.z)));
      const mesh = new THREE.Mesh(
        new THREE.ShapeGeometry(form),
        new THREE.MeshStandardMaterial({
          color: this.ausgewaehlt.has(raum.id) ? FARBE_AUSWAHL : FARBE_BODEN,
          side: THREE.DoubleSide,
        }),
      );
      mesh.rotation.x = -Math.PI / 2;              // Shape-y ⇒ −z (Herleitung: platzierung.ts)
      mesh.position.y = boden.y;
      mesh.receiveShadow = true;
      mesh.userData.nodeId = raum.id;
      this.inhalt.add(mesh);
    }

    // Decken (obere Raumabschlüsse): dieselbe Raumpolygon-Fläche wie der Boden, auf Wandhöhe
    // (level.elevation + defaultWallHeight). RÜCKSEITEN-CULLING (side: BackSide) ⇒ nur von UNTEN/
    // innen sichtbar; von oben durchsichtig, damit die Draufsicht nicht verdeckt wird. Kein
    // userData.nodeId (dekorativ, nicht selektierbar — der Boden trägt die Raum-Selektion).
    const deckenHoehe = level.elevation + level.defaultWallHeight;
    // Feature A: existiert eine MODELLIERTE Geschossdecke für dieses Level, ersetzt sie die dekorative
    // Raum-Decke (eine Wahrheit je Level) — sonst bleibt der dekorative Raumabschluss.
    const hatModellDecke = (dokument.ceilings ?? []).some((c) => c.levelId === level.id && c.visible !== false);
    for (const raum of hatModellDecke ? [] : raeume) {
      if (raum.polygon.length < 3) {
        continue;
      }
      const decke = bodenPunkteThree(raum.polygon, deckenHoehe);
      const form = new THREE.Shape();
      decke.punkte.forEach((p, i) => (i === 0 ? form.moveTo(p.x, -p.z) : form.lineTo(p.x, -p.z)));
      const mesh = new THREE.Mesh(
        new THREE.ShapeGeometry(form),
        new THREE.MeshStandardMaterial({ color: FARBE_DECKE, side: THREE.BackSide }),
      );
      mesh.rotation.x = -Math.PI / 2;
      mesh.position.y = decke.y;
      mesh.receiveShadow = true;
      this.inhalt.add(mesh);
    }

    // Modellierte Geschossdecke (Feature A): Slab-mit-Löchern (Treppendurchbrüche) auf der Wand-Oberkante,
    // selektierbar (userData.nodeId). Polygon minus oeffnungen; eine Geometrie-Quelle je Decke.
    for (const decke of (dokument.ceilings ?? []).filter((c) => c.levelId === level.id && c.visible !== false)) {
      if (decke.polygon.length < 3) {
        continue;
      }
      const oberkante = level.elevation + level.defaultWallHeight;
      const aussen = bodenPunkteThree(decke.polygon, oberkante);
      const form = new THREE.Shape();
      aussen.punkte.forEach((p, i) => (i === 0 ? form.moveTo(p.x, -p.z) : form.lineTo(p.x, -p.z)));
      for (const oeff of decke.oeffnungen ?? []) {
        if (oeff.polygon.length < 3) {
          continue;
        }
        const loch = bodenPunkteThree(oeff.polygon, oberkante);
        const pfad = new THREE.Path();
        loch.punkte.forEach((p, i) => (i === 0 ? pfad.moveTo(p.x, -p.z) : pfad.lineTo(p.x, -p.z)));
        pfad.closePath();
        form.holes.push(pfad);
      }
      const mesh = new THREE.Mesh(
        new THREE.ShapeGeometry(form),
        new THREE.MeshStandardMaterial({ color: this.ausgewaehlt.has(decke.id) ? FARBE_AUSWAHL : FARBE_DECKE, side: THREE.DoubleSide }),
      );
      mesh.rotation.x = -Math.PI / 2;
      mesh.position.y = aussen.y;
      mesh.receiveShadow = true;
      mesh.userData.nodeId = decke.id;
      this.inhalt.add(mesh);
    }

    // Dächer (D-c / W-3a): OHNE Aufbauten unverändert aus dem Mesh-Bauplan (dachMeshWelt). MIT Aufbauten
    // auf rechteckigem Sattel/Pult/Flach je Dachfläche als Shape-mit-Loch + Aufbaukörper über die reine
    // Engine (gaubeGeometrie). Walm o. Ä. (dachflaechen liefert []) rendert wie bisher; seine Aufbauten
    // erscheinen als Prüf-Marker (ehrlich, kein stiller Wegfall). Ungültige Kontur ⇒ Dach übersprungen
    // (Kante 1, kein Crash). Dach nur im aktiven Geschoss (▲D1).
    const daecher = (dokument.roofs ?? []).filter((r) => r.levelId === level.id && r.visible !== false);
    for (const dach of daecher) {
      const aufbauten = dach.aufbauten ?? [];
      const farbeDach = this.ausgewaehlt.has(dach.id) ? FARBE_AUSWAHL : FARBE_DACH;

      let flaechen: DachFlaeche[] = [];
      let firstHoeheMm = 0;
      try {
        firstHoeheMm = dachMeshWelt(dach).firstHoeheMm;
        if (aufbauten.length > 0) {
          flaechen = dachflaechen(dach);
        }
      } catch (fehler) {
        if (fehler instanceof DachGeometrieUngueltig) {
          continue;
        }
        throw fehler;
      }

      if (aufbauten.length === 0 || flaechen.length === 0) {
        // Bestandsrender: ganze Dachfläche aus dem Mesh-Bauplan (unverändert).
        this.rendereDachMesh(dach, farbeDach);
        // Aufbauten auf nicht-rechteckigem Dach (Walm): ehrlicher Prüf-Marker statt stillem Wegfall.
        for (const _ of aufbauten) {
          this.aufbauMarker(this.dachMittelpunktThree(dach, firstHoeheMm), dach.id);
        }
        continue;
      }

      // Rechteckige Flächen (Sattel/Pult/Flach): Aufbauten liegen (W-3a-MVP) auf der Primärfläche #0
      // (das Modell trägt noch keine Flächen-Zuordnung). Loch + Körper masshaltig aus der Engine.
      const yRidgeThree = firstHoeheMm / 1000;
      for (let fi = 0; fi < flaechen.length; fi++) {
        const af = flaecheZuFrame(flaechen[fi], yRidgeThree);
        const eigene = fi === 0 ? aufbauten : [];
        const koerper = eigene.map((a) => aufbauKoerper(af, a));
        const loecher = koerper
          .filter((k) => !k.pruefpflichtig && k.holePolyUV.length >= 3)
          .map((k) => k.holePolyUV);
        this.rendereDachflaeche(af, farbeDach, loecher, dach.id);
        for (const k of koerper) {
          if (k.pruefpflichtig || k.tris.length === 0) {
            this.aufbauMarker(k.refWorld, dach.id);
            continue;
          }
          this.rendereAufbauKoerper(k.tris, dach.id);
        }
      }
    }
  }

  // ---------------------------------------------------------------- Dach-/Aufbau-Render (W-3a)

  /** Bestehendes Dach-Rendering: ganze Fläche aus dem reinen Mesh-Bauplan (unverändert extrahiert). */
  private rendereDachMesh(dach: RoofNode, farbe: number): void {
    let bauplan;
    try {
      bauplan = dachMeshWelt(dach);
    } catch (fehler) {
      if (fehler instanceof DachGeometrieUngueltig) {
        return;
      }
      throw fehler;
    }
    const positionen: number[] = [];
    for (const dreieck of bauplan.dreiecke) {
      for (const p of dreieck) {
        const t = weltZuThree(p);
        positionen.push(t.x, t.y, t.z);
      }
    }
    const geometrie = new THREE.BufferGeometry();
    geometrie.setAttribute('position', new THREE.Float32BufferAttribute(positionen, 3));
    geometrie.computeVertexNormals();
    const mesh = new THREE.Mesh(
      geometrie,
      new THREE.MeshStandardMaterial({ color: farbe, roughness: 0.82, metalness: 0.0, side: THREE.DoubleSide }),
    );
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.userData.nodeId = dach.id;
    this.inhalt.add(mesh);
  }

  /** W-3a: eine rechteckige Dachfläche als Shape-mit-Löchern (u,v-Meter), in den three-Raum abgebildet. */
  private rendereDachflaeche(af: AufbauFrame, farbe: number, loecherUV: Array<Array<{ x: number; y: number }>>, nodeId: string): void {
    const { width: W, height: H } = af.frame;
    const form = new THREE.Shape();
    form.moveTo(0, 0);
    form.lineTo(W, 0);
    form.lineTo(W, H);
    form.lineTo(0, H);
    form.closePath();
    for (const loch of loecherUV) {
      if (loch.length < 3) continue;
      const pfad = new THREE.Path();
      loch.forEach((p, i) => (i === 0 ? pfad.moveTo(p.x, p.y) : pfad.lineTo(p.x, p.y)));
      pfad.closePath();
      form.holes.push(pfad);
    }
    const geometrie = new THREE.ShapeGeometry(form);
    // (u,v,0) → Welt: origin + u·vRight + v·vDown (vNormal als 3. Basisachse; makeBasis nutzt Spalten).
    const m = new THREE.Matrix4().makeBasis(
      new THREE.Vector3(af.frame.vRight.x, af.frame.vRight.y, af.frame.vRight.z),
      new THREE.Vector3(af.frame.vDown.x, af.frame.vDown.y, af.frame.vDown.z),
      new THREE.Vector3(af.frame.vNormal.x, af.frame.vNormal.y, af.frame.vNormal.z),
    );
    m.setPosition(af.frame.origin.x, af.frame.origin.y, af.frame.origin.z);
    geometrie.applyMatrix4(m);
    geometrie.computeVertexNormals();
    const mesh = new THREE.Mesh(
      geometrie,
      new THREE.MeshStandardMaterial({ color: farbe, roughness: 0.82, metalness: 0.0, side: THREE.DoubleSide }),
    );
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.userData.nodeId = nodeId;
    this.inhalt.add(mesh);
  }

  /** W-3a: Aufbaukörper (Gauben-/Kamin-/Aufsatz-Dreiecke in three-Metern) als ein Mesh. */
  private rendereAufbauKoerper(tris: Array<[{ x: number; y: number; z: number }, { x: number; y: number; z: number }, { x: number; y: number; z: number }]>, nodeId: string): void {
    const positionen: number[] = [];
    for (const t of tris) {
      for (const p of t) {
        positionen.push(p.x, p.y, p.z);
      }
    }
    const geometrie = new THREE.BufferGeometry();
    geometrie.setAttribute('position', new THREE.Float32BufferAttribute(positionen, 3));
    geometrie.computeVertexNormals();
    const mesh = new THREE.Mesh(
      geometrie,
      new THREE.MeshStandardMaterial({ color: FARBE_DACH, roughness: 0.8, metalness: 0.0, side: THREE.DoubleSide }),
    );
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.userData.nodeId = nodeId;
    this.inhalt.add(mesh);
  }

  /** W-3a: Prüf-Marker (Amber-Drahtwürfel) für einen nicht sicher platzierbaren Aufbau — kein Crash. */
  private aufbauMarker(pos: { x: number; y: number; z: number }, nodeId: string): void {
    const kanten = new THREE.LineSegments(
      new THREE.EdgesGeometry(new THREE.BoxGeometry(0.6, 0.6, 0.6)),
      new THREE.LineBasicMaterial({ color: FARBE_GEKLEMMT }),
    );
    kanten.position.set(pos.x, pos.y, pos.z);
    kanten.userData.nodeId = nodeId;
    this.inhalt.add(kanten);
  }

  /** Näherungs-Ankerpunkt (three) für Walm-Prüf-Marker: Polygon-Schwerpunkt auf Firsthöhe. */
  private dachMittelpunktThree(dach: RoofNode, firstHoeheMm: number): { x: number; y: number; z: number } {
    const n = dach.polygon.length || 1;
    const cx = dach.polygon.reduce((s, p) => s + p.x, 0) / n;
    const cy = dach.polygon.reduce((s, p) => s + p.y, 0) / n;
    return weltZuThree({ x: cx, y: cy, z: firstHoeheMm });
  }

  // ---------------------------------------------------------------- Auswahl (nur selectNodes-Naht)

  private klick = (ereignis: PointerEvent): void => {
    if (!this.aufKlickAuswahl) {
      return;
    }
    const rechteck = this.renderer.domElement.getBoundingClientRect();
    const zeiger = new THREE.Vector2(
      ((ereignis.clientX - rechteck.left) / rechteck.width) * 2 - 1,
      -((ereignis.clientY - rechteck.top) / rechteck.height) * 2 + 1,
    );
    const strahl = new THREE.Raycaster();
    strahl.setFromCamera(zeiger, this.kamera);
    const treffer = strahl.intersectObjects(this.inhalt.children, false);
    const nodeId = (treffer[0]?.object.userData.nodeId as string | undefined) ?? null;
    this.aufKlickAuswahl(nodeId); // erzeugt im Store NUR selectNodes — nie Geometrie-Commands
  };
}
