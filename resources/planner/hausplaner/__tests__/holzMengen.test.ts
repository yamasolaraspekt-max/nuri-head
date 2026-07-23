import { test } from "node:test";
import assert from "node:assert/strict";
import { holzMengenAusListe } from "../geometry/holzMengen";
import { schifterMengenAusListe } from "../geometry/schifterListe";

// Repräsentative Holzliste eines rechteckigen Satteldachs (2 Sparren à 5 m,
// 2 Konterlatten à 5 m, 3 Traglatten à 10 m).
const rechteckListe = () => [
  { type: "sparren", name: "Sparren main_S", laenge: 5 },
  { type: "sparren", name: "Sparren main_S", laenge: 5 },
  { type: "sparren", name: "Konterlatte", laenge: 5 },
  { type: "sparren", name: "Konterlatte", laenge: 5 },
  { type: "latte", name: "Traglatte", laenge: 10 },
  { type: "latte", name: "Traglatte", laenge: 10 },
  { type: "latte", name: "Traglatte", laenge: 10 },
];

test("rechteckige Fläche: Sparren/Konter/Latten korrekt getrennt summiert", () => {
  const m = holzMengenAusListe(rechteckListe());
  assert.equal(m.sparrenLaenge, 10); // 2×5
  assert.equal(m.konterLaenge, 10); // 2×5 (Konterlatte NICHT als Sparren gezählt)
  assert.equal(m.lattenLaenge, 30); // 3×10
  assert.equal(m.sparrenAnzahl, 2);
});

test("geclippte (Walm-/Dreiecks-)Stäbe reduzieren die Längen gegenüber Rahmen", () => {
  // Walm: Sparren werden zur Spitze hin kürzer (z.B. 5, 3, 1 statt 3×5=15)
  const walm = [
    { type: "sparren", name: "Sparren main_W", laenge: 5 },
    { type: "sparren", name: "Sparren main_W", laenge: 3 },
    { type: "sparren", name: "Sparren main_W", laenge: 1 },
    { type: "latte", name: "Traglatte", laenge: 8 },
    { type: "latte", name: "Traglatte", laenge: 4 },
  ];
  const m = holzMengenAusListe(walm);
  assert.equal(m.sparrenLaenge, 9); // 5+3+1, NICHT 3×5=15 (Rahmen wäre höher)
  assert.ok(m.sparrenLaenge < 3 * 5, "echte Längen < Rahmen-Schätzung");
  assert.equal(m.lattenLaenge, 12);
  assert.equal(m.sparrenAnzahl, 3);
});

test("leere Holzliste -> 0 (kein Fehler)", () => {
  const m = holzMengenAusListe([]);
  assert.deepEqual(m, { sparrenLaenge: 0, konterLaenge: 0, lattenLaenge: 0, sparrenAnzahl: 0 });
  assert.deepEqual(holzMengenAusListe(undefined), { sparrenLaenge: 0, konterLaenge: 0, lattenLaenge: 0, sparrenAnzahl: 0 });
  assert.deepEqual(holzMengenAusListe(null), { sparrenLaenge: 0, konterLaenge: 0, lattenLaenge: 0, sparrenAnzahl: 0 });
});

test("ungültige/negative Längen -> 0, niemals NaN/Infinity/negativ", () => {
  const m = holzMengenAusListe([
    { type: "sparren", name: "Sparren x", laenge: NaN },
    { type: "sparren", name: "Sparren x", laenge: -4 },
    { type: "sparren", name: "Sparren x", laenge: Infinity },
    { type: "latte", name: "Traglatte", laenge: 10 },
  ]);
  assert.ok(Number.isFinite(m.sparrenLaenge) && m.sparrenLaenge >= 0);
  assert.equal(m.sparrenLaenge, 0);
  assert.equal(m.sparrenAnzahl, 0); // ungültige Längen zählen nicht
  assert.equal(m.lattenLaenge, 10);
});

// EA28-Regressionsschloss: Schiftsparren (umbenannte, an Kehle/Grat geclippte Gemeinsparren) MÜSSEN
// weiterhin als Sparren mitzählen — sonst fallen sie aus Bauholz-m³ und Lohn heraus (Mengen-Unter-Count,
// vom Geometrie-Prüfer aufgedeckt). Außerdem: Schifter sind eine TEILMENGE der Sparren-lfm (Anti-Doppelzählung).
test("EA28: Schiftsparren zählen als Sparren; Σ Schifter-lfm ⊆ Σ Sparren-lfm (keine Mengen-Lücke)", () => {
  const geclippt = [
    { type: "sparren", name: "Sparren main_S", laenge: 5.5, istSchifter: false, schifter: null },
    { type: "sparren", name: "Sparrenstück oben main_S", laenge: 2.0 }, // Öffnung -> voller Sparren geteilt
    { type: "sparren", name: "Schiftsparren Kehle main_S", laenge: 4.0, istSchifter: true, schifter: "kehle" },
    { type: "sparren", name: "Schiftsparren Grat main_W", laenge: 3.0, istSchifter: true, schifter: "grat" },
    { type: "sparren", name: "Konterlatte", laenge: 9.0 },
    { type: "latte", name: "Traglatte", laenge: 9.0 },
  ];
  const m = holzMengenAusListe(geclippt);
  // 5.5 + 2.0 + 4.0 + 3.0 = 14.5 — Schiftsparren NICHT verloren.
  assert.equal(m.sparrenLaenge, 14.5);
  assert.equal(m.sparrenAnzahl, 4);
  assert.equal(m.konterLaenge, 9.0); // Konterlatte trotz "…sparren"-naher Logik getrennt
  // Schifter sind echte Teilmenge der Sparren-Gesamtlänge (kein Double-Count).
  const s = schifterMengenAusListe(geclippt);
  assert.equal(s.anzahl, 2);
  assert.equal(s.laengeGesamt, 7.0);
  assert.ok(s.laengeGesamt < m.sparrenLaenge, `Schifter ${s.laengeGesamt} ⊂ Sparren ${m.sparrenLaenge}`);
});

test("Konterlatte wird nicht als Sparren gezählt (type 'sparren', aber name 'Konterlatte')", () => {
  const m = holzMengenAusListe([
    { type: "sparren", name: "Sparren a", laenge: 6 },
    { type: "sparren", name: "Konterlatte", laenge: 6 },
  ]);
  assert.equal(m.sparrenLaenge, 6);
  assert.equal(m.konterLaenge, 6);
  assert.equal(m.sparrenAnzahl, 1);
});
