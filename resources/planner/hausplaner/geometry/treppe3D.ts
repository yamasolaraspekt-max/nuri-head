/**
 * Hausplaner — Treppen-3D-Körper (reine Geometrie), Grundlage fürs spätere 3D-Rendering.
 *
 * Aus Lauflinie/Laufbreite/Geschosshöhe entstehen die einzelnen Stufen-Quader einer geschlossenen
 * (Vollstufen-)Treppe im LOKALEN Koordinatensystem der Treppe:
 *   x = Laufrichtung (0 … Lauflänge), y = Breite (zentriert, ±Laufbreite/2), z = Höhe (0 … Geschosshöhe).
 * Stufe k (k=0…Auftritte−1): Quader x∈[k·Auftritt,(k+1)·Auftritt], z∈[0,(k+1)·Steigung] — gestapelt
 * ergeben sie das massive Treppenprofil. Die Stufenzahl kommt aus dem getesteten `berechneTreppe`.
 * Keine three.js-Abhängigkeit, keine Szene-Mutation — nur Zahlen (Mitte + Größe je Quader).
 */

import { berechneTreppe, type TreppenBereich } from './treppenBerechnung';

export interface Treppe3DEingabe {
  laufbreite: number;
  geschosshoehe: number;
  /** verfügbare Lauflänge (Grundriss), mm — bestimmt den Auftritt, sonst Schrittmaßregel. */
  verfuegbareLauflaenge?: number;
  gewuenschteSteigung?: number;
  bereich?: TreppenBereich;
}

export interface Stufenquader {
  /** Mittelpunkt [x,y,z], mm, im lokalen Treppen-KS. */
  mitte: [number, number, number];
  /** Kantenlängen [lx,ly,lz], mm. */
  groesse: [number, number, number];
}

export interface Treppe3DKoerper {
  stufen: Stufenquader[];
  anzahlSteigungen: number;
  anzahlAuftritte: number;
  steigungshoehe: number;
  auftritt: number;
  /** gesamte Lauflänge (Auftritte·Auftritt), mm. */
  lauflaenge: number;
  bestanden: boolean;
}

const r1 = (x: number): number => Math.round(x * 10) / 10;

export function treppe3DKoerper(e: Treppe3DEingabe): Treppe3DKoerper {
  const t = berechneTreppe({
    geschosshoehe: e.geschosshoehe,
    verfuegbareLauflaenge: e.verfuegbareLauflaenge,
    gewuenschteSteigung: e.gewuenschteSteigung,
    laufbreite: e.laufbreite,
    bereich: e.bereich,
  });

  const auftritt = t.auftritt;
  const steigung = t.steigungshoehe;
  const hw = e.laufbreite;

  const stufen: Stufenquader[] = [];
  for (let k = 0; k < t.anzahlAuftritte; k++) {
    const hoehe = (k + 1) * steigung;
    stufen.push({
      mitte: [r1((k + 0.5) * auftritt), 0, r1(hoehe / 2)],
      groesse: [r1(auftritt), r1(hw), r1(hoehe)],
    });
  }

  return {
    stufen,
    anzahlSteigungen: t.anzahlSteigungen,
    anzahlAuftritte: t.anzahlAuftritte,
    steigungshoehe: steigung,
    auftritt,
    lauflaenge: Math.round(t.anzahlAuftritte * auftritt),
    bestanden: t.bestanden,
  };
}
