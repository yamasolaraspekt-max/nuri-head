/**
 * Z1-W2-6 — **die Auswechslungs-Analyse erreicht den Benutzer.**
 *
 * ---
 *
 * **Zwei geprüfte Module, beide ohne Ladeweg.** `geometry/auswechslung.ts` (195 Zeilen, eigene
 * Testdatei) weiß, welche Sparren eine Dachöffnung schneidet — **0 Aufrufer**. Und
 * `oeffnungVTiefeM` (`geometry/dachOeffnung.ts:52`), die art-abhängige Achsenregel, hat
 * **0 Nutzer ausserhalb der eigenen Datei**, obwohl sie in `dachformVorlagen.test.ts:1299-1302`
 * geprüft ist. *Gebaut, geprüft, und erreicht niemanden.* Diese Datei ist der Weg.
 *
 * ---
 *
 * ## ⚠ Die Achsenregel wird BENUTZT, nicht nachgebaut (Kriterium b)
 *
 * `RoofAufbau` und `Oeffnung` benennen dieselben Wörter verschieden:
 *
 * | Modellfeld (`RoofAufbau`) | → | `Oeffnung` | Achse |
 * |---|---|---|---|
 * | `x` (bereits 0..1) | → | `xRel` | u — parallel Traufe |
 * | `y` (bereits 0..1) | → | `yRel` | v — geneigt Traufe→First |
 * | `breiteMm` /1000 | → | `breiteM` | u — parallel Traufe, dieselbe Achse |
 * | **`oeffnungVTiefeM(...)`** | → | `hoeheM` | v — **art-abhängig** |
 *
 * **Das letzte Feld ist der Fallstrick.** `hoeheMm` ist die *vertikale* Fronthöhe, `tiefeMm` die
 * Ausdehnung *entlang der Schräge* — und welches von beiden das v-Maß ist, hängt an der Art:
 * Dachfenster liegen in der Dachebene (`hoeheM`), Gauben und Kamine stehen heraus (`tiefeM`).
 * **Für neun von zehn `ObstacleType` wäre ein blindes `hoeheMm → hoeheM` falsch.**
 *
 * **Deshalb steht hier keine eigene `if (art === 'window')`-Zeile.** Kriterium (b) weist sie
 * ausdrücklich ab: *eine zweite Wahrheit über die Achsenzuordnung, an genau der Stelle, an der ein
 * Fehler eine plausible falsche Sparrenzahl erzeugt.* Wiederverwendung vor Neuentwicklung.
 *
 * ## Der Sparrenabstand ist ein erfragter Wert (Kriterium c)
 *
 * `rafterDistM` steht **nicht** im `SceneDocument` — 0 Treffer in `domain/`. Er wird deshalb
 * angezeigt und ist wählbar; die Vorgabe `0,8 m` stammt aus `enginePanels.ts:184`
 * (`schluessel 'sparrenabstandM', pflicht: true, vorgabe: 0.8`). **Ein stiller Vorgabewert ist
 * unzulässig** — *eine Sparrenzahl, die auf einem unsichtbaren Abstand beruht, ist eine Behauptung
 * mit Nachkommastelle.*
 *
 * ## `pruefpflichtig` ist ein Vorbehalt, keine Zahl (Kriterium d)
 *
 * Das Modul zieht seine Grenze selbst: Wechselhölzer nur, wenn die tragenden Sparren eindeutig
 * bestimmbar sind und die Öffnung nicht in First/Traufe/Ortgang liegt. **Sonst `pruefpflichtig`
 * und KEINE erfundenen Mengen.** Hier steht dann auch keine — *das Modul unterscheidet „keine
 * nötig" von „nicht bestimmbar", und eine Oberfläche, die beides als 0 zeigt, macht diese
 * Unterscheidung zunichte.* **Keine statische Bemessung** bleibt sichtbar: die Anzeige darf nicht
 * wie ein Nachweis aussehen.
 */
import React from 'react';
import type { RoofAufbau, RoofNode } from '../../domain/scene.types';
import { analysiereAuswechslung } from '../../geometry/auswechslung';
import { oeffnungVTiefeM } from '../../geometry/dachOeffnung';
import { T, FARBEN } from '../studioDaten';

/** Vorgabe aus `enginePanels.ts:184` — benannt, nicht erfunden. */
const SPARRENABSTAND_VORGABE_M = 0.8;

export interface AuswechslungAnzeigeEigenschaften {
  /** Das Dach, auf dem der Aufbau sitzt — liefert die Flächenmaße. */
  dach: RoofNode | null;
  /** Der gewählte Aufbau. `null` = nichts zu analysieren. */
  aufbau: RoofAufbau | null;
}

const meter = (n: number, stellen = 2): string => n.toFixed(stellen).replace('.', ',');

export function AuswechslungAnzeige({ dach, aufbau }: AuswechslungAnzeigeEigenschaften): React.ReactElement | null {
  // Kriterium c: der Abstand gehoert dem Benutzer und ist sichtbar.
  const [abstandM, setAbstandM] = React.useState<number>(SPARRENABSTAND_VORGABE_M);

  if (!dach || !aufbau) return null;

  // Flaechenmasse aus dem Dach ableiten: die Bounding-Box des Polygons in Metern.
  const xs = (dach.polygon ?? []).map((p) => p.x);
  const ys = (dach.polygon ?? []).map((p) => p.y);
  if (xs.length < 3) return null;
  const flaeche = {
    breiteM: (Math.max(...xs) - Math.min(...xs)) / 1000,
    hoeheM: (Math.max(...ys) - Math.min(...ys)) / 1000,
  };

  // ── Die Umrechnung RoofAufbau -> Oeffnung. Das v-Mass kommt aus der VORHANDENEN Regel. ──
  const oeffnung = {
    xRel: aufbau.x,
    yRel: aufbau.y,
    breiteM: aufbau.breiteMm / 1000,
    hoeheM: oeffnungVTiefeM({
      art: aufbau.typ,
      hoeheM: aufbau.hoeheMm / 1000,
      tiefeM: aufbau.tiefeMm / 1000,
    }),
  };

  const a = analysiereAuswechslung(flaeche, oeffnung, abstandM);

  return (
    <div className="hp-ep-abschnitt" data-pruefung="auswechslung" data-art={aufbau.typ}>
      <div className="hp-ep-abschnitt-titel">Auswechslung</div>

      <label style={{ display: 'block', fontSize: 12, color: FARBEN.gedaempft, marginBottom: 8 }}>
        Sparrenabstand (m)
        <input
          type="number" step={0.05} min={0.05} value={abstandM}
          onChange={(e) => setAbstandM(Math.max(0.05, Number(e.target.value) || SPARRENABSTAND_VORGABE_M))}
          data-feld="sparrenabstand"
          style={{
            width: '100%', marginTop: 4, padding: '6px 8px', borderRadius: 8,
            border: `1px solid ${T.controlBorder}`, background: T.surface, color: T.ink, fontSize: 13,
          }}
        />
      </label>

      <div className="hp-ep-kennzahl" data-wert="sparren">
        Betroffene Sparren: <strong style={{ color: FARBEN.text }}>{a.betroffeneSparren}</strong>
        {a.spanntMehrereFelder && ' · über mehrere Felder'}
      </div>
      <div className="hp-ep-kennzahl" data-wert="wechsel-erforderlich">
        Wechsel erforderlich: <strong style={{ color: FARBEN.text }}>{a.wechselErforderlich ? 'ja' : 'nein'}</strong>
      </div>

      {a.pruefpflichtig ? (
        <>
          {/* Kriterium d: HIER STEHT KEINE MENGE. Nicht "0 Stueck" — das Modul sagt
              "nicht bestimmbar", und das ist etwas anderes als "keine noetig". */}
          <div className="hp-ep-befund" data-pruefpflichtig="ja">
            <span aria-hidden className="hp-ep-schwere-symbol">!</span>
            <span>
              <strong className="hp-ep-schwere-text">Prüfpflichtig</strong>
              {' – keine Wechselholz-Menge ableitbar.'}
              {a.randzonen.length > 0 && ` Randzone: ${a.randzonen.join(' · ')}.`}
            </span>
          </div>
          {a.hinweise.map((h, i) => (
            <div key={i} className="hp-ep-kennzahl" data-hinweis="ja" style={{ color: FARBEN.gedaempft }}>{h}</div>
          ))}
        </>
      ) : (
        <div className="hp-ep-kennzahl" data-wert="wechsel">
          Wechselhölzer: <strong style={{ color: FARBEN.text }}>{a.wechselAnzahl}</strong>
          {a.wechselAnzahl > 0 && ` · je ${meter(a.wechselLaengeM)} m`}
        </div>
      )}

      {/* Die Selbstbeschraenkung des Moduls bleibt sichtbar — die Anzeige darf nicht wie ein
          Nachweis aussehen. */}
      <div className="hp-ep-kennzahl" style={{ color: FARBEN.gedaempft, marginTop: 6, fontSize: 11 }}>
        Geometrische Ableitung — keine statische Bemessung.
      </div>
    </div>
  );
}
