/**
 * Hausplaner P1c — React-Hülle um die three.js-Szene (RoofEngine-Muster: Insel-Store per
 * subscribe, imperative Szene — KEIN React Three Fiber, ▲P1-K1).
 *
 * Ein Store, zwei Renderer: diese Hülle LIEST den Store (subscribe) und reicht Dokument/
 * Level/Selektion an HausplanerDreiDSzene durch; zurück fließt AUSSCHLIESSLICH
 * selectNodes (Klick-Auswahl) — nie ein Geometrie-Command. Es existiert kein zweiter
 * Szenen-State (Abnahmekriterium 1).
 *
 * Kante 6 (Kamera + Ressourcen): Die Instanz lebt über Moduswechsel hinweg — der
 * Elternteil blendet den Bereich nur aus (display:none), statt zu unmounten ⇒ die
 * Kamera bleibt erhalten. dispose() läuft erst beim echten Unmount (Seite verlassen).
 */
import React, { useEffect, useRef } from 'react';
import { useHausplanerStore } from '../store/hausplanerStore';
import { HausplanerDreiDSzene } from '../renderers/three-d/szene';
import { T } from './studioDaten';

export function DreiDBereich({ sichtbar }: { sichtbar: boolean }): React.ReactElement {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const szeneRef = useRef<HausplanerDreiDSzene | null>(null);

  // Leere-Szene-Hinweis (Kante 5): reine Ableitung aus dem Store, kein eigener State.
  const scene = useHausplanerStore((s) => s.scene);
  const activeLevelId = useHausplanerStore((s) => s.activeLevelId);
  const istLeer =
    !scene || !scene.nodes.some((n) => n.levelId === activeLevelId && n.type === 'wall');

  useEffect(() => {
    const container = containerRef.current;
    if (!container) {
      return undefined;
    }

    const szene = new HausplanerDreiDSzene(container, (nodeId) => {
      useHausplanerStore.getState().selectNodes(nodeId ? [nodeId] : []);
    });
    szeneRef.current = szene;

    const uebernehmen = (): void => {
      const s = useHausplanerStore.getState();
      if (s.scene && s.activeLevelId) {
        szene.aktualisiere(s.scene, s.activeLevelId, s.selectedNodeIds);
      }
    };
    uebernehmen();
    szene.fitToScene();

    // Store-Abo: JEDE Szenen-/Level-/Selektionsänderung erreicht 3D aus demselben Command.
    const abbestellen = useHausplanerStore.subscribe(uebernehmen);

    return () => {
      abbestellen();
      szene.dispose(); // Kante 6: WebGL-Ressourcen nur beim echten Verlassen freigeben
      szeneRef.current = null;
    };
  }, []);

  return (
    <div style={{ position: 'relative', flex: 1, minWidth: 0, display: sichtbar ? 'block' : 'none' }}>
      <div ref={containerRef} className="hp-3d-flaeche" />
      {istLeer && (
        <div
          className="hp-3d-mitte"
        >
          <span
            style={{
              background: '#ffffffcc', border: '1px solid #e5e7eb', borderRadius: 8,
              padding: '10px 16px', fontSize: 13, color: '#6b7280',
            }}
          >
            Leere Szene — im 2D-Modus Wände zeichnen, 3D folgt automatisch.
          </span>
        </div>
      )}
      <button
        type="button"
        onClick={() => szeneRef.current?.fitToScene()}
        style={{
          position: 'absolute', right: 12, bottom: 12, padding: '6px 12px', fontSize: 12,
          fontWeight: 600, borderRadius: 8, border: '1px solid #d1d5db', background: T.surface,
          color: T.canvasWall, cursor: 'pointer',
        }}
      >
        Ansicht einpassen
      </button>
    </div>
  );
}
