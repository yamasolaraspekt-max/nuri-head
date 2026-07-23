import { test } from "node:test";
import assert from "node:assert/strict";
import { holzBauteileAusListe, OFFENE_HOLZBAUTEILE } from "../geometry/holzBauteile";
import { holzMengenAusListe } from "../geometry/holzMengen";

// Repräsentative Walm-Holzliste: Sparren + Pfetten + Gratsparren (echte 3D-Längen)
const walmListe = () => [
  { type: "sparren", name: "Sparren main_S", laenge: 5 },
  { type: "sparren", name: "Konterlatte", laenge: 5 },
  { type: "latte", name: "Traglatte", laenge: 10 },
  { type: "pfette", name: "Firstpfette", laenge: 6 },
  { type: "pfette", name: "Fußpfette Nord", laenge: 12 },
  { type: "pfette", name: "Fußpfette Süd", laenge: 12 },
  { type: "gratsparren", name: "Gratsparren VL", laenge: 7 },
  { type: "gratsparren", name: "Gratsparren VR", laenge: 7 },
  { type: "kehlsparren", name: "Kehlsparren Links", laenge: 4 },
];

test("Pfetten/Grat-/Kehlsparren werden getrennt + mit echter Länge summiert", () => {
  const b = holzBauteileAusListe(walmListe());
  assert.equal(b.pfettenLaenge, 30); // 6+12+12
  assert.equal(b.pfettenAnzahl, 3);
  assert.equal(b.gratsparrenLaenge, 14); // 7+7
  assert.equal(b.gratsparrenAnzahl, 2);
  assert.equal(b.kehlsparrenLaenge, 4);
  assert.equal(b.kehlsparrenAnzahl, 1);
});

test("Reparatur 7 bleibt unberührt: Sparren/Konter/Latten unverändert aus derselben Liste", () => {
  // Die neuen Bauteile dürfen die Rep-7-Aggregation NICHT verfälschen.
  const m = holzMengenAusListe(walmListe());
  assert.equal(m.sparrenLaenge, 5); // nur echter Sparren, NICHT Pfette/Grat/Kehl
  assert.equal(m.konterLaenge, 5);
  assert.equal(m.lattenLaenge, 10);
  assert.equal(m.sparrenAnzahl, 1);
});

test("Satteldach (Pfetten, keine Grate/Kehlen) -> Grat/Kehl bleiben 0", () => {
  const sattel = [
    { type: "pfette", name: "Firstpfette", laenge: 12 },
    { type: "pfette", name: "Fußpfette Nord", laenge: 12 },
    { type: "sparren", name: "Sparren main_S", laenge: 5 },
  ];
  const b = holzBauteileAusListe(sattel);
  assert.equal(b.pfettenLaenge, 24);
  assert.equal(b.gratsparrenLaenge, 0);
  assert.equal(b.kehlsparrenLaenge, 0);
  assert.equal(b.gratsparrenAnzahl, 0);
});

test("Flachdach / leere Liste -> alles 0 (keine erfundenen Bauteile)", () => {
  const leer = holzBauteileAusListe([]);
  assert.deepEqual(leer, { pfettenLaenge: 0, pfettenAnzahl: 0, gratsparrenLaenge: 0, gratsparrenAnzahl: 0, kehlsparrenLaenge: 0, kehlsparrenAnzahl: 0 });
  assert.deepEqual(holzBauteileAusListe(undefined), leer);
  assert.deepEqual(holzBauteileAusListe(null), leer);
});

test("ungültige/negative Längen -> ignoriert (kein NaN/Infinity/negativ)", () => {
  const b = holzBauteileAusListe([
    { type: "pfette", name: "x", laenge: NaN },
    { type: "pfette", name: "y", laenge: -5 },
    { type: "gratsparren", name: "z", laenge: Infinity },
    { type: "pfette", name: "ok", laenge: 6 },
  ]);
  assert.equal(b.pfettenLaenge, 6);
  assert.equal(b.pfettenAnzahl, 1); // nur das gültige Stück
  assert.equal(b.gratsparrenLaenge, 0);
  assert.ok(Number.isFinite(b.pfettenLaenge) && b.pfettenLaenge >= 0);
});

test("offene Holzbauteile sind dokumentiert (Wechsel/Schwelle/Mittelpfette/Schifter)", () => {
  assert.ok(OFFENE_HOLZBAUTEILE.length >= 3);
  assert.ok(OFFENE_HOLZBAUTEILE.some((s) => /Wechsel|Auswechslung/i.test(s)));
  assert.ok(OFFENE_HOLZBAUTEILE.some((s) => /Mittelpfette/i.test(s)));
});
