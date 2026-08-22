/**
 * Z1-V1-1 Module 2–4 — **drei geprüfte Dach-Module erreichen den Benutzer.**
 *
 * ```text
 * geometry/dachVorlage.ts        34 Z.   Aufrufer im Produktivpfad  0   -> dachVorlage()
 * projection/dachProjektion.ts   43 Z.   Aufrufer im Produktivpfad  0   -> projiziereDach()
 * geometry/sparrenTrennung.ts    67 Z.   Aufrufer im Produktivpfad  0   -> sparrenTeilstuecke()
 * ```
 *
 * Alle drei hängen am **ausgewählten Dach** und erscheinen deshalb in einem Abschnitt, statt den
 * Panelbereich dreimal zu öffnen. *Keines rechnet hier etwas — gerechnet wird in den Modulen.*
 *
 * ---
 *
 * ## ⚠ `RoofShape` ist nicht `DachForm` — acht gegen vier
 *
 * `dachVorlage(form)` nimmt **`DachForm`** = `'sattel' | 'walm' | 'pult' | 'flach'`.
 * `RoofNode.roofType` ist **`RoofShape`** und hat **acht** Werte: dieselben vier **plus**
 * `'rect' | 'l-shape' | 't-shape' | 'u-shape'` (`domain/roofShape.ts:12`).
 *
 * **Für diese vier gibt es keine Vorlage**, und das wird gesagt statt still auf `'sattel'`
 * zurückzufallen — `dachVorlage()` selbst tut genau das („fällt auf Sattel zurück"), was für
 * einen Standardwert richtig ist, als **Anzeige** aber ein Satteldach behaupten würde, wo ein
 * L-Dach steht.
 *
 * ## ⚠ Sparrentrennung ohne Dachaufbau
 *
 * `sparrenTeilstuecke` teilt einen Sparren **an einer Öffnung**. Die Öffnung ist ein
 * `RoofAufbau` (`RoofNode.aufbauten`). **Gibt es keinen, gibt es nichts zu trennen** — dann
 * steht das hier, statt eine leere Liste als „nicht trennbar" zu zeigen. Die Umrechnung
 * Aufbau → v-Maß folgt der **vorhandenen** art-abhängigen Achsenregel `oeffnungVTiefeM`
 * (`geometry/dachOeffnung.ts:52`); `x`/`y` sind die **Mitte** (`auswechslung.ts:128-129`),
 * nicht die Ecke — deshalb ± die halbe Tiefe.
 */
import React from 'react';
import type { RoofNode, SceneDocument } from '../../domain/scene.types';
import type { DachForm } from '../../geometry/dachVorlage';
import { dachVorlage } from '../../geometry/dachVorlage';
import { projiziereDach } from '../../projection/dachProjektion';
import { sparrenTeilstuecke } from '../../geometry/sparrenTrennung';
import { oeffnungVTiefeM } from '../../geometry/dachOeffnung';

/** Die vier Formen, für die es eine Vorlage gibt — deckungsgleich mit `DachForm`. */
const MIT_VORLAGE: readonly DachForm[] = ['sattel', 'walm', 'pult', 'flach'];

function hatVorlage(form: string): form is DachForm {
  return (MIT_VORLAGE as readonly string[]).includes(form);
}

export interface DachKennzahlenEigenschaften {
  dach: RoofNode | null;
  scene: SceneDocument | null;
}

export function DachKennzahlen({ dach, scene }: DachKennzahlenEigenschaften): React.ReactElement | null {
  if (!dach) return null;

  // ── Modul 2 — Vorlage zur Form ──────────────────────────────────────────────────────────────
  const vorlage = hatVorlage(dach.roofType) ? dachVorlage(dach.roofType) : null;

  // ── Modul 3 — projizierte Flächen, auf DIESES Dach gefiltert ────────────────────────────────
  //
  // ⚠ **`projiziereDach` WIRFT** — `DachGeometrieUngueltig`, sobald die Traufkontur nicht
  // rechteckig ist (`dachGeometrie.ts`, „kein stilles Falschdach"). Das ist richtig so und wird
  // hier NICHT unterdrückt, sondern **angezeigt**. Ohne dieses `catch` reißt der Wurf beim
  // Rendern den gesamten Panelbaum ab: gemessen an der Fixture `u-dach` (Form `u-shape`, acht
  // Polygonpunkte) verschwand nach dem Anwählen des Dachs das ganze Studio — Konva-Stage weg,
  // Panel leer. *Ein geworfener Fehler in einer Anzeige ist kein Fehlerfall des Moduls, sondern
  // ein Fehlerfall der Anzeige.*
  let flaechen: ReturnType<typeof projiziereDach> = [];
  let projektionsFehler: string | null = null;
  if (scene) {
    try {
      flaechen = projiziereDach(scene).filter((f) => f.roof_id === dach.id);
    } catch (e) {
      projektionsFehler = e instanceof Error ? e.message : String(e);
    }
  }

  // ── Modul 4 — Sparrentrennung am ersten Aufbau ──────────────────────────────────────────────
  const ys = (dach.polygon ?? []).map((p) => p.y);
  const sparrenLaengeM = ys.length >= 3 ? (Math.max(...ys) - Math.min(...ys)) / 1000 : 0;
  const aufbau = (dach.aufbauten ?? [])[0] ?? null;
  const vTiefeM = aufbau
    ? oeffnungVTiefeM({ art: aufbau.typ, hoeheM: aufbau.hoeheMm / 1000, tiefeM: aufbau.tiefeMm / 1000 })
    : 0;
  const vMitteM = aufbau ? aufbau.y * sparrenLaengeM : 0;
  const teilstuecke = aufbau
    ? sparrenTeilstuecke(0, sparrenLaengeM, vMitteM - vTiefeM / 2, vMitteM + vTiefeM / 2)
    : [];

  return (
    <div className="hp-ep-abschnitt" data-pruefung="dachkennzahlen" data-form={dach.roofType}>
      <div className="hp-ep-abschnitt-titel">Kennzahlen zum Dach</div>

      {/* Modul 2 */}
      {vorlage ? (
        <div className="hp-ep-kennzahl" data-menge="vorlage">
          Vorlage {vorlage.label} · Regelneigung {vorlage.neigungGrad}°
          {dach.neigungGrad !== vorlage.neigungGrad && (
            <> · gesetzt {dach.neigungGrad}°</>
          )}
        </div>
      ) : (
        <div className="hp-ep-befund" data-meldung="keine-vorlage">
          Für die Form „{dach.roofType}" gibt es keine Vorlage — Regelneigung wird nicht ausgewiesen.
        </div>
      )}

      {/* Modul 3 */}
      {projektionsFehler ? (
        <div className="hp-ep-befund" data-meldung="projektion-ungueltig">
          Keine Fläche ausgewiesen — {projektionsFehler}
        </div>
      ) : flaechen.length > 0 ? (
        flaechen.map((f, i) => (
          <div className="hp-ep-kennzahl" data-menge="dachflaeche" key={`${f.roof_id}-${i}`}>
            Fläche {f.flaeche_m2.toFixed(2)} m² · Neigung {f.neigung_grad}°
            {f.azimut_grad === null ? ' · Azimut offen' : ` · Azimut ${f.azimut_grad}°`}
          </div>
        ))
      ) : (
        <div className="hp-ep-befund" data-meldung="keine-projektion">
          Keine projizierte Fläche — die Dachkontur ist nicht auswertbar.
        </div>
      )}

      {/* Modul 4 */}
      {!aufbau ? (
        <div className="hp-ep-lesehinweis" data-meldung="kein-aufbau">
          Kein Dachaufbau gesetzt — es gibt keinen Sparren zu trennen.
        </div>
      ) : teilstuecke.length === 0 ? (
        <div className="hp-ep-befund" data-meldung="nicht-trennbar">
          Sparren nicht sicher trennbar — die Öffnung reicht an Traufe oder First, oder ein
          Reststück wäre zu kurz. Der volle Sparren bleibt stehen.
        </div>
      ) : (
        teilstuecke.map((t) => (
          <div className="hp-ep-kennzahl" data-menge="teilstueck" key={t.lage}>
            Teilstück {t.lage} {t.laengeM.toFixed(2)} m
          </div>
        ))
      )}
      <div className="hp-ep-lesehinweis">
        Geometrische Ableitung — keine statische Bemessung.
      </div>
    </div>
  );
}
