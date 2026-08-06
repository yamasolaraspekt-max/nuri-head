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
import React, { useEffect, useRef, useState } from 'react';
import { useHausplanerStore } from '../store/hausplanerStore';
import { HausplanerDreiDSzene } from '../renderers/three-d/szene';
import { T } from './studioDaten';

/** Der Stil der Nicht-darstellbar-Meldung — OBEN, nicht unten.
 *  *Am Browser gemessen:* unten am Leinwandrand lag die Meldung in allen drei Breiten
 *  ausserhalb des Fensters (1440: top 892 bei 900 Fensterhoehe; 1024: 474-812 bei 768;
 *  375: 694-813 bei 812). Vorhanden und unlesbar ist so gut wie nicht vorhanden.
 *  Der Rest: — als Konstante, damit kein statischer Inline-Block
 *  entsteht (Stilschicht-Zusage Scheibe 8c). Farben ausschliesslich ueber Tokens. */
const MELDUNG_STIL: React.CSSProperties = {
  position: 'absolute', left: 12, top: 12, right: 12, maxWidth: '52ch',
  // *Am Browser gemessen:* mit `right: 140` (Abstand zum Fit-Knopf) blieb bei 1024 px im
  // schmalen 3D-Bereich ein Kasten von 25 px Breite — ein Wort je Zeile, 337 px hoch, ueber
  // der Leinwand. Der Knopf sitzt UNTEN rechts; oben braucht die Meldung ihm nicht auszuweichen. padding: '8px 12px',
            fontSize: 12, lineHeight: 1.4, borderRadius: 8,
            border: `1px solid ${T.warn}`, background: T.warnSoft, color: T.warnInk,
};

export function DreiDBereich({ sichtbar }: { sichtbar: boolean }): React.ReactElement {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const szeneRef = useRef<HausplanerDreiDSzene | null>(null);

  // Leere-Szene-Hinweis (Kante 5): reine Ableitung aus dem Store, kein eigener State.
  const scene = useHausplanerStore((s) => s.scene);
  const activeLevelId = useHausplanerStore((s) => s.activeLevelId);
  const istLeer =
    !scene || !scene.nodes.some((n) => n.levelId === activeLevelId && n.type === 'wall');

  /**
   * **A-01-4 — die leere Stelle bekommt einen Grund.**
   *
   * Ein Bestandsdokument kann ein Dach tragen, das der Renderer nicht zeichnen kann: die Geometrie
   * verweigert nicht-rechteckige Konturen (`dachGeometrie.ts`, „kein stilles Falschdach"). Seit
   * A-01 entsteht ein solches Dach nicht mehr — *aber gespeicherte gibt es, und sie tragen
   * `freigabe: 'bestaetigt'` ueber einer leeren Ansicht.*
   *
   * Abgelesen **in** `uebernehmen`, direkt nach `aktualisiere` — nicht in einem eigenen Effekt.
   *
   * **Am Browser gemessen, nicht vermutet:** ein eigener `useEffect` ueber `[scene, activeLevelId]`
   * stand VOR dem Effekt, der die Szene ueberhaupt anlegt. Beim ersten Durchlauf war `szeneRef`
   * noch `null`, danach lief er nie wieder — das Bestandsdokument mit L-Dach zeigte weiterhin eine
   * leere Stelle, *waehrend die Insel-Zusagen gruen waren*. Genau der Fall, den ein gruener
   * Inseltest nicht faengt.
   */
  const [nichtDarstellbar, setNichtDarstellbar] = useState<ReadonlyArray<{ nodeId: string; grund: string }>>([]);

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
        // Nach der Zeichnung, aus derselben Quelle: was konnte nicht gezeichnet werden?
        setNichtDarstellbar(szene.nichtDarstellbar());
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
      {nichtDarstellbar.length > 0 && (
        <div
          role="status"
          style={MELDUNG_STIL}
        >
          {nichtDarstellbar.length === 1
            ? `Ein Dach wird hier nicht gezeigt: ${nichtDarstellbar[0].grund}`
            : `${nichtDarstellbar.length} Dächer werden hier nicht gezeigt: ${nichtDarstellbar[0].grund}`}
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
