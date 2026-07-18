/**
 * D-d — Projektion Dach → DachFlaecheProjektion[] (belastbarer Vertrag, ▲D4).
 * Grundlage: docs/hausplaner/dach-andock-spec.md §2 + §5 (D-d).
 *
 * EIGENER Vertrag neben RaumGeometrieProjektion: je Dachfläche ein Eintrag mit Fläche (m², auf cm²
 * gerundet), abgeleitetem Azimut (Nord = +y, ganzzahlig) und Neigung — Quelle für PV/Heizlast,
 * Nachfolger des stillgelegten RoofAreaEstimator. Die Zuordnung „Dachfläche → Decke eines Raums" ist
 * BEWUSST nicht hier (RaumGeometrieProjektion.decke bleibt null); kein stilles Befüllen.
 *
 * Ungültige (nicht-rechteckige) Dachkontur ⇒ DachGeometrieUngueltig aus dachFlaechen() propagiert
 * (nie stilles Falschdach). Der Vertrag ist durch das Fixture (dachProjektion.test.ts) eingefroren.
 */
import type { SceneDocument, DachFlaecheProjektion } from '../domain/scene.types';
import { dachFlaechen } from '../geometry/dachGeometrie';

export function projiziereDach(scene: SceneDocument): DachFlaecheProjektion[] {
  const geschossVon = new Map<string, number>();
  for (const level of scene.levels) {
    geschossVon.set(level.id, level.sortOrder);
  }

  const ergebnis: DachFlaecheProjektion[] = [];
  for (const roof of scene.roofs ?? []) {
    const geschoss = geschossVon.get(roof.levelId);
    if (geschoss === undefined) {
      continue; // Dach ohne gültiges Geschoss wird nicht projiziert (Integritäts-Sache, kein Rateswert).
    }

    for (const flaeche of dachFlaechen(roof)) {
      ergebnis.push({
        geschoss,
        roof_id: roof.id,
        dachtyp: roof.roofType,
        flaeche_m2: Math.round(flaeche.flaeche_m2 * 100) / 100, // belastbar auf cm² gerundet
        azimut_grad: flaeche.azimut_grad === null ? null : Math.round(flaeche.azimut_grad),
        neigung_grad: flaeche.neigung_grad,
        first_laenge_mm: flaeche.first_laenge_mm,
      });
    }
  }

  return ergebnis;
}
