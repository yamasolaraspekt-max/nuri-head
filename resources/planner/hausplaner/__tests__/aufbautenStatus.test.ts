import { test } from "node:test";
import assert from "node:assert/strict";
import {
  aufbautenOhneFlaeche,
  istAufbauPruefpflichtig,
  AUFBAUTEN_WARNUNG,
} from "../geometry/aufbautenStatus";

// Punkt 8 — Szenarien der Aufbauten-Synchronisation

test("Kamin/Gaube/Fenster auf weiter vorhandener Fläche -> wird nachgezogen, NICHT prüfpflichtig", () => {
  // Gebäudelänge/-breite/Neigung/Überstand ändern: Fläche 'main_S' bleibt bestehen
  const aufbauten = [
    { id: "k1", surfaceId: "main_S" },
    { id: "g1", surfaceId: "main_S" },
    { id: "f1", surfaceId: "main_N" },
  ];
  const r = aufbautenOhneFlaeche(aufbauten, ["main_S", "main_N"]);
  assert.equal(r.anzahl, 0);
  assert.deepEqual(r.pruefpflichtigIds, []);
});

test("Dachform ändern -> alte Flächen weg -> Aufbauten prüfpflichtig (nicht still falsch)", () => {
  // Satteldach (main_S/main_N) -> Walmdach (south/north/west/east)
  const aufbauten = [
    { id: "k1", surfaceId: "main_S" },
    { id: "g1", surfaceId: "main_N" },
  ];
  const r = aufbautenOhneFlaeche(aufbauten, ["south", "north", "west", "east"]);
  assert.equal(r.anzahl, 2);
  assert.deepEqual(r.pruefpflichtigIds.sort(), ["g1", "k1"]);
});

test("Teilweise: ein Aufbau auf alter Fläche, einer auf gültiger -> nur der alte prüfpflichtig", () => {
  const aufbauten = [
    { id: "alt", surfaceId: "weg" },
    { id: "ok", surfaceId: "south" },
  ];
  const r = aufbautenOhneFlaeche(aufbauten, ["south", "north"]);
  assert.deepEqual(r.pruefpflichtigIds, ["alt"]);
});

test("keine Aufbauten vorhanden -> keine unnötige Prüfpflicht/Warnung", () => {
  assert.equal(aufbautenOhneFlaeche([], ["main_S"]).anzahl, 0);
  assert.equal(aufbautenOhneFlaeche(undefined as unknown as [], ["main_S"]).anzahl, 0);
});

test("Aufbau bewusst entfernt -> keine falsche Prüfpflicht bleibt bestehen", () => {
  // nach dem Entfernen ist die Liste leer bzw. enthält den Aufbau nicht mehr
  const r = aufbautenOhneFlaeche([{ id: "rest", surfaceId: "south" }], ["south"]);
  assert.equal(r.anzahl, 0);
});

test("istAufbauPruefpflichtig erkennt einzelne Aufbauten korrekt", () => {
  assert.equal(istAufbauPruefpflichtig("k1", ["k1", "g1"]), true);
  assert.equal(istAufbauPruefpflichtig("ok", ["k1", "g1"]), false);
  assert.equal(istAufbauPruefpflichtig("k1", []), false);
});

test("Warntext gesetzt und verständlich", () => {
  assert.ok(AUFBAUTEN_WARNUNG.length > 0);
  assert.ok(/Aufbauten|geprüft/i.test(AUFBAUTEN_WARNUNG));
});
