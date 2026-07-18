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
import type { OpeningNode, SceneDocument, WallNode, ZoneNode } from '../../domain/scene.types';
import type { RendererAdapter } from './adapter';
import { weltZuThree } from './adapter';
import { segmentiereWand } from './segmentierung';
import { platziereWandQuader, bodenPunkteThree } from './platzierung';
import { dachMeshWelt } from './dachMesh';
import { DachGeometrieUngueltig } from '../../geometry/dachGeometrie';

const FARBE_WAND = 0x9ca3af;      // neutrales Grau
const FARBE_BODEN = 0xe5e7eb;     // hell
const FARBE_DACH = 0xb08968;      // gedämpftes Terrakotta/Braun (neutral, keine Statusfarbe)
const FARBE_AUSWAHL = 0x93c21c;   // Marken-Grün (einzige Akzentfarbe)
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

  constructor(container: HTMLElement, aufKlickAuswahl?: (nodeId: string | null) => void) {
    this.container = container;
    this.aufKlickAuswahl = aufKlickAuswahl;

    this.renderer = new THREE.WebGLRenderer({ antialias: true });
    this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    this.renderer.setSize(container.clientWidth || 1, container.clientHeight || 1);
    container.appendChild(this.renderer.domElement);

    this.szene = new THREE.Scene();
    this.szene.background = new THREE.Color(0xf3f4f6);

    this.kamera = new THREE.PerspectiveCamera(
      50,
      (container.clientWidth || 1) / (container.clientHeight || 1),
      0.01,
      500,
    );
    this.setzeStandardKamera();

    this.steuerung = new OrbitControls(this.kamera, this.renderer.domElement);
    this.steuerung.enableDamping = true;

    // Ein Hemisphären- + ein Richtungslicht (Spec), neutraler Boden-Grid.
    this.szene.add(new THREE.HemisphereLight(0xffffff, 0x9ca3af, 1.0));
    const richtungslicht = new THREE.DirectionalLight(0xffffff, 1.4);
    richtungslicht.position.set(8, 14, 6);
    this.szene.add(richtungslicht);
    this.szene.add(new THREE.GridHelper(40, 40, 0xd1d5db, 0xe5e7eb));

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
      if (kind instanceof THREE.Mesh) {
        kind.geometry.dispose();
        (Array.isArray(kind.material) ? kind.material : [kind.material]).forEach((m) => m.dispose());
      }
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
    const raeume = knoten.filter((n): n is ZoneNode => n.type === 'zone' && n.zoneType === 'room');

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
          new THREE.MeshStandardMaterial({ color: farbe }),
        );
        mesh.position.set(p.zentrum.x, p.zentrum.y, p.zentrum.z);
        mesh.rotation.y = p.rotationY;
        mesh.userData.nodeId = wand.id;
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
      mesh.userData.nodeId = raum.id;
      this.inhalt.add(mesh);
    }

    // Dächer (D-c): aus dem reinen Mesh-Bauplan (dachMeshWelt) ein Mesh je Dach, Eckpunkte via
    // weltZuThree. Ungültige (nicht-rechteckige) Kontur ⇒ dieses Dach wird übersprungen — nie ein
    // stilles Falschdach und kein Render-Crash (Kante 1). Dach nur im obersten/aktiven Geschoss (▲D1).
    const daecher = (dokument.roofs ?? []).filter((r) => r.levelId === level.id && r.visible !== false);
    for (const dach of daecher) {
      let bauplan;
      try {
        bauplan = dachMeshWelt(dach);
      } catch (fehler) {
        if (fehler instanceof DachGeometrieUngueltig) {
          continue;
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
      const dachMesh = new THREE.Mesh(
        geometrie,
        new THREE.MeshStandardMaterial({
          color: this.ausgewaehlt.has(dach.id) ? FARBE_AUSWAHL : FARBE_DACH,
          side: THREE.DoubleSide,
        }),
      );
      dachMesh.userData.nodeId = dach.id;
      this.inhalt.add(dachMesh);
    }
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
