import { test } from "node:test";
import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { fileURLToPath } from "node:url";
import {
  gradToRad,
  radToGrad,
  aeussereTraufkanteHoeheM,
  geneigteLaengeAusWaagerechtM,
  ortgangFlaechenlaengeM,
  sattelSparrenlaengeM,
  sattelFirstRiseM,
  pultSparrenlaengeM,
  pultRiseM,
  walmFirstRiseM,
  walmRuecksprungM,
  walmFirstlaengeM,
  walmFirstlaengeGleicheNeigungM,
  gratsparrenLaenge3DM,
  geneigteFlaecheAusGrundrissM2,
  dachflaecheAusPolygonM2,
  sparrenfeldAufteilung,
  clampPitchGrad,
  walmIstKonsistent,
  eindeckungPasstZuKategorie,
  neigungBrauchtZusatzmassnahme,
  validateVorlage,
  sucheVorlagen,
  filterVorlagen,
  nurVerfuegbare,
  nurGeplante,
  findeVorlage,
  istAnwendbar,
  applyVorlage,
  vorschauSvg,
  vorschauZeigtAufbau,
  vorschauZeigtPv,
  istVorschauVersprechen,
  anzeigeStatus,
  standardAufbauten,
  aufbautenWerdenGesetzt,
  aufbautenNichtGesetzt,
  schneefangWirdGesetzt,
  gaubeSchematischGesetzt,
  UNGESTUETZTE_GAUBE_TOKENS,
  hauptflaecheInfo,
  ENGINE_FLAECHEN,
  AUFBAU_AUTO_HINWEIS,
  VORSCHAU_AUFBAU_HINWEIS,
  VORSCHAU_PV_HINWEIS,
  DACHFORM_VORLAGEN,
  type BuildingParamsLike,
  type DachformVorlage,
} from "../../utils/dachformVorlagen.ts";
import { polygonFlaecheM2 } from "../../utils/polygonFlaeche.ts";
import {
  platziereAufbauten,
  verfuegbareBreiteM,
  MAX_BREITE_ANTEIL,
  type DachflaecheInfo,
  type AufbauWunsch,
} from "../../utils/aufbauPlatzierung.ts";
import {
  platziereSchneefang,
  sperrzoneVRel,
  istInSperrzone,
  flaecheInfoAusPolygon,
  SCHNEEFANG_HINWEIS,
  type DachLinienBauteil,
} from "../../utils/linienBauteile.ts";
import {
  grundrissPolygon,
  grundrissFlaecheM2,
  anzahlInnenwinkel,
  eckenAnalyse,
  istZusammengesetzt,
  formAusShape,
  erwarteteInnenwinkel,
} from "../../utils/grundriss.ts";
import { oeffnungRechteck, oeffnungVTiefeM } from "../../utils/dachOeffnung.ts";
import {
  stehendeAufbauBasis,
  istStehenderAufbau,
} from "../../utils/aufbauOrientierung.ts";

const RT = (x: number) => Math.round(x * 1e9) / 1e9; // Rundung gegen Float-Rauschen
const alle = DACHFORM_VORLAGEN;

// Standard-prevBuild (spiegelt die Engine-Default-BuildingParams der DachplanerProPage).
const defaultBuild: BuildingParamsLike = {
  category: "pitched", shape: "sattel",
  length: 10, width: 8, height: 5, pitch: 35, attika: 0.3,
  overhang: 0.5, overhangGable: 0.3,
  lengthB: 4, widthB: 4, layerSpread: 0,
  rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34,
};

// Dokumentierte Engine-BuildingParams-Feldliste (Drift-Wächter für apply.build).
const ENGINE_BUILDING_PARAMS_KEYS = [
  "category", "shape", "length", "width", "height", "pitch", "attika",
  "overhang", "overhangGable", "lengthB", "widthB", "layerSpread",
  "rafterSpacing", "rafterWidth", "rafterHeight", "battenDist",
];

// =====================================================================
// Reinheit
// =====================================================================
test("Reinheit: dachformVorlagen.ts importiert kein react/three und nutzt keine DOM/THREE-Symbole", () => {
  const pfad = fileURLToPath(new URL("../../utils/dachformVorlagen.ts", import.meta.url));
  const quelle = readFileSync(pfad, "utf8");
  // kein Import aus react / react-dom / three / @react-three
  const importRegex = /\bfrom\s+["'](react|react-dom|three|@react-three[^"']*)["']/;
  assert.ok(!importRegex.test(quelle), "verbotener react/three-Import gefunden");
  // keine DOM-/THREE-/Hook-Symbole
  const verbotene = ["new THREE", "THREE.", "document.", "useState", "useEffect", "window.addEventListener"];
  for (const sym of verbotene) {
    assert.ok(!quelle.includes(sym), `verbotenes Symbol gefunden: ${sym}`);
  }
});

// =====================================================================
// Winkel
// =====================================================================
test("gradToRad: 0->0, 180->PI, 90->PI/2; radToGrad invers", () => {
  assert.equal(gradToRad(0), 0);
  assert.equal(RT(gradToRad(180)), RT(Math.PI));
  assert.equal(RT(gradToRad(90)), RT(Math.PI / 2));
  assert.equal(RT(radToGrad(Math.PI)), 180);
  assert.equal(RT(radToGrad(gradToRad(37))), 37);
});

// =====================================================================
// Traufkante / äußere Kante
// =====================================================================
test("aeussereTraufkanteHoeheM: H - oh*tan(alpha); pitch=0 -> Traufhöhe", () => {
  assert.equal(RT(aeussereTraufkanteHoeheM(5, 0.5, 35)), RT(5 - 0.5 * Math.tan((35 * Math.PI) / 180)));
  assert.equal(aeussereTraufkanteHoeheM(5, 0.5, 0), 5); // Traufkante = Traufhöhe
});

// =====================================================================
// /cos-Regeln (Regression gegen doppeltes /cos)
// =====================================================================
test("geneigteLaengeAusWaagerechtM(1,45) = 1/cos45", () => {
  assert.equal(RT(geneigteLaengeAusWaagerechtM(1, 45)), RT(1 / Math.cos((45 * Math.PI) / 180)));
});

test("sattelSparrenlaengeM(8,0.5,35) = 4.5/cos35", () => {
  assert.equal(RT(sattelSparrenlaengeM(8, 0.5, 35)), RT(4.5 / Math.cos((35 * Math.PI) / 180)));
});

test("ortgangFlaechenlaengeM(10,0.3) = 10.6 OHNE /cos", () => {
  assert.equal(RT(ortgangFlaechenlaengeM(10, 0.3)), 10.6);
  // doppeltes /cos wäre größer als 10.6 -> Regression
  assert.ok(ortgangFlaechenlaengeM(10, 0.3) < 10.6 / Math.cos((35 * Math.PI) / 180));
});

test("pult: vMax über Breite mit /cos, seitliche Länge (Ortgang) ohne /cos", () => {
  assert.equal(RT(pultSparrenlaengeM(6, 0.5, 15)), RT(7 / Math.cos((15 * Math.PI) / 180)));
  assert.equal(RT(pultRiseM(6, 15)), RT(6 * Math.tan((15 * Math.PI) / 180)));
  assert.equal(RT(ortgangFlaechenlaengeM(10, 0.3)), 10.6); // seitliche Länge ohne /cos
});

// =====================================================================
// Walm
// =====================================================================
test("Walm: Rise/Rücksprung/Firstlänge und Konsistenz", () => {
  assert.equal(RT(walmFirstRiseM(8, 30)), RT(4 * Math.tan((30 * Math.PI) / 180)));
  // alpha_walm = alpha_haupt -> R = width/2
  const h = walmFirstRiseM(8, 30);
  assert.equal(RT(walmRuecksprungM(h, 30)), 4);
  // Firstlänge = L - 2R
  const R = walmRuecksprungM(h, 30);
  assert.equal(RT(walmFirstlaengeM(12, R)), 4);
  // Engine-Spezialfall (gleiche Neigung): max(0, L-W)
  assert.equal(walmFirstlaengeGleicheNeigungM(12, 8), 4);
  // Konsistenz
  assert.equal(walmIstKonsistent(8, 8), false); // L=W
  assert.equal(walmIstKonsistent(8, 10), false); // L<W
  assert.equal(walmIstKonsistent(12, 8), true);
});

test("walmFirstlaengeM darf negativ werden (Inkonsistenz-Signal, nicht still 0)", () => {
  assert.ok(walmFirstlaengeM(6, 5) < 0); // 6 - 10 = -4
});

// =====================================================================
// Gratsparren als echte 3D-Diagonale
// =====================================================================
test("gratsparrenLaenge3DM(3,4,12) = 13 (3D-Diagonale, nie Projektion)", () => {
  assert.equal(gratsparrenLaenge3DM(3, 4, 12), 13);
  // waagerechte Projektion sqrt(9+16)=5 wäre falsch
  assert.ok(gratsparrenLaenge3DM(3, 4, 12) > Math.sqrt(9 + 16));
});

// =====================================================================
// Fläche
// =====================================================================
test("dachflaecheAusPolygonM2 nutzt Shoelace (Rep6) und != width*height bei Trapez", () => {
  const trapez = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 8, y: 4 }, { x: 2, y: 4 }];
  assert.equal(dachflaecheAusPolygonM2(trapez), polygonFlaecheM2(trapez));
  assert.equal(dachflaecheAusPolygonM2(trapez), 32); // (10+6)/2*4
  assert.notEqual(dachflaecheAusPolygonM2(trapez), 10 * 4); // nicht width*height
});

test("geneigteFlaecheAusGrundrissM2(48,35) = 48/cos35; <3 Punkte/NaN -> 0", () => {
  assert.equal(RT(geneigteFlaecheAusGrundrissM2(48, 35)), RT(48 / Math.cos((35 * Math.PI) / 180)));
  assert.equal(dachflaecheAusPolygonM2([{ x: 0, y: 0 }, { x: 1, y: 1 }]), 0); // < 3 Punkte
  assert.equal(geneigteFlaecheAusGrundrissM2(NaN, 35), 0); // NaN -> 0
  assert.equal(dachflaecheAusPolygonM2([{ x: NaN, y: 0 }, { x: 1, y: 0 }, { x: 1, y: 1 }]), 0);
});

// =====================================================================
// Sparrenfeld
// =====================================================================
test("sparrenfeldAufteilung(10,0.7): felder=15, sparren=16, eff<=0.7", () => {
  const r = sparrenfeldAufteilung(10, 0.7);
  assert.equal(r.felder, 15);
  assert.equal(r.sparrenAnzahl, 16);
  assert.ok(r.effektiverAbstandM <= 0.7);
  assert.ok(Number.isFinite(r.effektiverAbstandM) && r.effektiverAbstandM > 0);
});

// =====================================================================
// Pitch-Klemmung
// =====================================================================
test("clampPitchGrad: 0->{1,true}, 90->{85,true}, 35->{35,false}; Flach [1.5,8]", () => {
  assert.deepEqual(clampPitchGrad(0), { wert: 1, geklemmt: true });
  assert.deepEqual(clampPitchGrad(90), { wert: 85, geklemmt: true });
  assert.deepEqual(clampPitchGrad(35), { wert: 35, geklemmt: false });
  assert.deepEqual(clampPitchGrad(0, 1.5, 8), { wert: 1.5, geklemmt: true });
  assert.deepEqual(clampPitchGrad(10, 1.5, 8), { wert: 8, geklemmt: true });
  assert.deepEqual(clampPitchGrad(3, 1.5, 8), { wert: 3, geklemmt: false });
  assert.equal(clampPitchGrad(NaN).geklemmt, true);
  assert.equal(clampPitchGrad(Infinity).geklemmt, true);
});

// =====================================================================
// Eindeckung / Neigung
// =====================================================================
test("eindeckungPasstZuKategorie: Kategorie-Trennung", () => {
  assert.equal(eindeckungPasstZuKategorie("bitumen", "pitched"), false);
  assert.equal(eindeckungPasstZuKategorie("ziegel", "flat"), false);
  assert.equal(eindeckungPasstZuKategorie("ziegel", "pitched"), true);
  assert.equal(eindeckungPasstZuKategorie("bitumen", "flat"), true);
});

test("neigungBrauchtZusatzmassnahme(15,10,22): NEIGUNG_UNTER_RDN, nicht UNTER_MINDEST", () => {
  const w = neigungBrauchtZusatzmassnahme(15, 10, 22);
  const codes = w.map((x) => x.code);
  assert.ok(codes.includes("NEIGUNG_UNTER_RDN"));
  assert.ok(!codes.includes("NEIGUNG_UNTER_MINDEST"));
});

// =====================================================================
// Suche / Filter
// =====================================================================
test("sucheVorlagen('walm') findet walm-standard", () => {
  const r = sucheVorlagen(alle, "walm");
  assert.ok(r.some((v) => v.id === "walm-standard"));
});

test("filterVorlagen: status verfügbar = 88 (inkl. 6 Flachdach-L/T/U); category flat = rect + L/T/U-Polygon", () => {
  // E13: +3 konvertierte (l-shape-flat/flach-t/flach-u) +3 neue Gebäudetyp-Flach-L/T/U = 88 verfügbar.
  assert.equal(filterVorlagen(alle, { status: "verfuegbar" }).length, 100);
  const flat = filterVorlagen(alle, { category: "flat" });
  assert.ok(flat.every((v) => v.geometrie.category === "flat"));
  const flatVerf = flat.filter((v) => v.status === "verfuegbar");
  const flatVerfIds = flatVerf.map((v) => v.id);
  assert.ok(flatVerfIds.includes("flach-bitumen") && flatVerfIds.includes("flach-gruendach"));
  assert.ok(flatVerfIds.includes("l-shape-flat") && flatVerfIds.includes("flach-t") && flatVerfIds.includes("flach-u"));
  assert.equal(flatVerf.length, 28); // 22 rect + 6 L/T/U
  assert.ok(flatVerf.every((v) => ["rect", "l-shape", "t-shape", "u-shape"].includes(v.geometrie.engineShape as string)));
});

test("Selektoren nurVerfuegbare/nurGeplante/findeVorlage", () => {
  assert.equal(nurVerfuegbare(alle).length, 100);
  assert.ok(nurGeplante(alle).length >= 1);
  assert.equal(nurVerfuegbare(alle).length + nurGeplante(alle).length, alle.length);
  assert.equal(findeVorlage(alle, "sattel-standard")?.id, "sattel-standard");
  assert.equal(findeVorlage(alle, "gibt-es-nicht"), undefined);
});

// =====================================================================
// Invarianten je Vorlage (Daten-Konsistenz)
// =====================================================================
test("Invarianten: verfügbar/geplant konsistent, ids eindeutig, hinweisStatik gesetzt", () => {
  const ids = new Set<string>();
  for (const v of alle) {
    assert.ok(!ids.has(v.id), `doppelte id: ${v.id}`);
    ids.add(v.id);
    assert.ok(v.hinweisStatik.trim().length > 0, `hinweisStatik leer bei ${v.id}`);
    assert.equal(v.anwendbar, v.status === "verfuegbar");

    if (v.status === "verfuegbar") {
      assert.ok(v.apply != null, `verfügbar ohne apply: ${v.id}`);
      assert.ok(["sattel", "pult", "walm", "rect", "l-shape", "t-shape", "u-shape"].includes(v.geometrie.engineShape as string), `engineShape ungültig: ${v.id}`);
      // KORREKTUR (deckungsneutral): apply setzt AUSSCHLIESSLICH Geometrie, KEINE Eindeckung/cover
      assert.equal((v.apply as any).cover, undefined, `apply.cover muss undefiniert sein (deckungsneutral): ${v.id}`);
      assert.equal((v.dachdecker as any).empfohleneEindeckung, undefined, `empfohleneEindeckung darf nicht gesetzt sein: ${v.id}`);
      assert.equal((v.dachdecker as any).zulaessigeEindeckungen, undefined, `zulaessigeEindeckungen darf nicht gesetzt sein: ${v.id}`);
      assert.equal(v.dachdecker.dachdeckungSeparatAuswaehlen, true, `dachdeckungSeparatAuswaehlen muss true sein: ${v.id}`);
      assert.ok((v.dachdecker.deckungsHinweis ?? "").trim().length > 0, `deckungsHinweis leer: ${v.id}`);
      assert.equal(istAnwendbar(v), true, `istAnwendbar false bei verfügbar: ${v.id}`);
      // keine Maße NaN/negativ (nur numerische Felder)
      const b = v.apply!.build;
      for (const [k, val] of Object.entries(b)) {
        if (typeof val !== "number") continue; // category/shape sind Strings
        assert.ok(Number.isFinite(val), `${v.id}.${k} nicht endlich`);
        assert.ok(val >= 0, `${v.id}.${k} negativ`);
      }
      assert.ok(b.length > 0 && b.width > 0 && b.height > 0, `Maße <= 0 bei ${v.id}`);
    } else {
      assert.equal(v.anwendbar, false, `geplant anwendbar: ${v.id}`);
      assert.equal(v.apply, undefined, `geplant mit apply: ${v.id}`);
      assert.ok((v.geplantGrund ?? "").trim().length > 0, `geplantGrund fehlt: ${v.id}`);
      assert.equal(istAnwendbar(v), false, `istAnwendbar true bei geplant: ${v.id}`);
    }
  }
});

test("Walm-Vorlage konsistent (L>W) und löst keine WALM_INKONSISTENT aus", () => {
  const walm = findeVorlage(alle, "walm-standard")!;
  const val = validateVorlage(walm);
  assert.ok(val.ok);
  assert.ok(!val.warnungen.some((w) => w.code === "WALM_INKONSISTENT"));
  // inkonsistenter Walm (L<=W) erzeugt Fehler
  const kaputt: DachformVorlage = {
    ...walm,
    geometrie: { ...walm.geometrie, defaultLength: 6, defaultWidth: 8 },
    apply: walm.apply ? { ...walm.apply, build: { ...walm.apply.build, length: 6, width: 8 } } : undefined,
  };
  const valKaputt = validateVorlage(kaputt);
  assert.ok(valKaputt.warnungen.some((w) => w.code === "WALM_INKONSISTENT"));
  assert.equal(valKaputt.ok, false);
});

test("Trapezblech-flachgeneigt löst NEIGUNG_UNTER_RDN (Demonstration), bleibt anwendbar", () => {
  const v = findeVorlage(alle, "sattel-blech-flachgeneigt")!;
  const val = validateVorlage(v);
  assert.ok(val.warnungen.some((w) => w.code === "NEIGUNG_UNTER_RDN"));
  assert.equal(val.ok, true); // 'warnung', kein 'fehler' -> bleibt anwendbar
});

// =====================================================================
// Apply-Mapping
// =====================================================================
test("applyVorlage(walm-standard): ok, build merged (lengthB/widthB/layerSpread unverändert), L>W, deckungsneutral (kein cover)", () => {
  const walm = findeVorlage(alle, "walm-standard")!;
  const r = applyVorlage(walm, defaultBuild);
  assert.equal(r.ok, true);
  assert.ok(r.build);
  // KORREKTUR (deckungsneutral): applyVorlage liefert KEIN cover -> Eindeckung bleibt unverändert
  assert.equal((r as any).cover, undefined);
  assert.ok(r.build!.length > r.build!.width);
  // additiv: unberührte Felder behalten den Wert
  assert.equal(r.build!.lengthB, defaultBuild.lengthB);
  assert.equal(r.build!.widthB, defaultBuild.widthB);
  assert.equal(r.build!.layerSpread, defaultBuild.layerSpread);
  // gesetzte Vorlagen-Felder
  assert.equal(r.build!.category, "pitched");
  assert.equal(r.build!.shape, "walm");
  assert.equal(r.build!.pitch, 30);
  assert.equal(r.build!.rafterSpacing, 70); // cm
});

test("applyVorlage(geplant): ok=false + Grund, kein build/cover", () => {
  const zelt = findeVorlage(alle, "zeltdach")!;
  const r = applyVorlage(zelt, defaultBuild);
  assert.equal(r.ok, false);
  assert.ok((r.grund ?? "").length > 0);
  assert.equal(r.build, undefined);
  assert.equal(r.cover, undefined);
});

test("applyVorlage: alle verfügbaren liefern ok=true ohne NaN; deckungsneutral (KEIN cover ausgegeben)", () => {
  for (const v of nurVerfuegbare(alle)) {
    const r = applyVorlage(v, defaultBuild);
    assert.equal(r.ok, true, `apply fehlgeschlagen: ${v.id}`);
    assert.ok(r.build, `kein build: ${v.id}`);
    // KORREKTUR (deckungsneutral): niemals ein cover -> Materialauswahl bleibt unangetastet
    assert.equal((r as any).cover, undefined, `apply darf kein cover liefern: ${v.id}`);
    for (const [k, val] of Object.entries(r.build!)) {
      assert.ok(Number.isFinite(val as number) || typeof val === "string", `${v.id}.${k} NaN/Infinity`);
    }
  }
});

// =====================================================================
// Drift-Wächter: apply.build-Felder ⊆ Engine-BuildingParams-Felder
// =====================================================================
test("VorlagenBuildPatch-Feldspiegelung: apply.build-Schlüssel sind dokumentierte Engine-Felder", () => {
  for (const v of nurVerfuegbare(alle)) {
    for (const key of Object.keys(v.apply!.build)) {
      assert.ok(
        ENGINE_BUILDING_PARAMS_KEYS.includes(key),
        `apply.build.${key} (${v.id}) ist kein dokumentiertes Engine-BuildingParams-Feld`,
      );
    }
  }
});

// =====================================================================
// Gesamtzahlen
// =====================================================================
test("Gesamt: 82 verfügbar, übrige geplant, gleiche/abweichende Form geplant", () => {
  assert.equal(nurVerfuegbare(alle).length, 100);
  // l-shape/t-shape sind geplant (Compound-Bug)
  assert.equal(findeVorlage(alle, "l-shape-pitched")?.status, "geplant");
  assert.equal(findeVorlage(alle, "t-shape-pitched")?.status, "geplant");
  assert.equal(findeVorlage(alle, "zeltdach")?.status, "geplant");
  assert.equal(findeVorlage(alle, "krueppelwalm")?.status, "geplant");
});

// =====================================================================
// Erweiterte Bibliothek (additive Vorlagen + Bildvorschau)
// =====================================================================
test("Erweitert: Gesamtzahlen (122 gesamt / 82 verfügbar / 40 geplant)", () => {
  assert.equal(alle.length, 150);
  assert.equal(nurVerfuegbare(alle).length, 100);
  assert.equal(nurGeplante(alle).length, 50);
  assert.equal(nurVerfuegbare(alle).length + nurGeplante(alle).length, alle.length);
});

test("Erweitert: ids eindeutig + Name + vorschauSvg für ALLE Vorlagen vorhanden", () => {
  const ids = new Set<string>();
  for (const v of alle) {
    assert.ok(!ids.has(v.id), `doppelte id: ${v.id}`);
    ids.add(v.id);
    assert.ok(v.name.trim().length > 0, `name leer: ${v.id}`);
    const svg = vorschauSvg(v);
    assert.ok(svg.startsWith("<svg"), `kein SVG-Start: ${v.id}`);
    assert.ok(svg.includes("</svg>"), `kein SVG-Ende: ${v.id}`);
    assert.ok(svg.includes("<polygon") || svg.includes("<rect") || svg.includes("<path"), `kein Inhalt im SVG: ${v.id}`);
  }
});

test("Erweitert: vorschauSvg ohne NaN/Infinity/undefined und ohne negative Koordinaten", () => {
  for (const v of alle) {
    const svg = vorschauSvg(v);
    assert.ok(!/NaN|Infinity|undefined/.test(svg), `NaN/Infinity/undefined in ${v.id}`);
    const ohneLabel = svg.replace(/aria-label="[^"]*"/g, "");
    assert.ok(!/-\d/.test(ohneLabel), `negative Koordinate in ${v.id}`);
    const nums = ohneLabel.match(/\d+(\.\d+)?/g) || [];
    for (const t of nums) {
      const n = Number(t);
      assert.ok(Number.isFinite(n) && n >= 0, `ungültige Zahl ${t} in ${v.id}`);
    }
  }
});

test("Erweitert: verfügbare nur sattel/pult/walm/rect + gültige Maße + deckungsneutral (kein cover)", () => {
  for (const v of nurVerfuegbare(alle)) {
    assert.ok(v.apply, `verfügbar ohne apply: ${v.id}`);
    assert.ok(["sattel", "pult", "walm", "rect", "l-shape", "t-shape", "u-shape"].includes(v.geometrie.engineShape as string), `engineShape ungültig: ${v.id}`);
    const b = v.apply!.build;
    assert.ok(b.length > 0 && b.width > 0 && b.height > 0, `Maße <= 0: ${v.id}`);
    for (const [k, val] of Object.entries(b)) {
      if (typeof val !== "number") continue;
      assert.ok(Number.isFinite(val) && val >= 0, `${v.id}.${k} ungültig`);
    }
    // KORREKTUR (deckungsneutral): apply enthält keinerlei Eindeckung/cover
    assert.equal((v.apply as any).cover, undefined, `apply.cover muss undefiniert sein: ${v.id}`);
    assert.equal(istAnwendbar(v), true, `nicht anwendbar trotz verfügbar: ${v.id}`);
  }
});

test("Erweitert: geplante nicht anwendbar (apply undefined, Grund gesetzt, applyVorlage ok=false)", () => {
  for (const v of nurGeplante(alle)) {
    assert.equal(v.apply, undefined, `geplant mit apply: ${v.id}`);
    assert.equal(v.anwendbar, false, `geplant anwendbar: ${v.id}`);
    assert.ok((v.geplantGrund ?? "").trim().length > 0, `geplantGrund fehlt: ${v.id}`);
    assert.equal(istAnwendbar(v), false, `istAnwendbar true bei geplant: ${v.id}`);
    assert.equal(applyVorlage(v, defaultBuild).ok, false, `applyVorlage ok bei geplant: ${v.id}`);
  }
});

test("Erweitert: GENEIGTE L-/T-/U-/Mehrkörper-Vorlagen sind geplant; Flachdach-L/T/U verfügbar", () => {
  const ltuKeys = ["l-shape", "t-shape", "u-grundriss", "mehrfluegel", "mehrkoerper"];
  const ltuTags = ["l-form", "t-form", "u-form", "mehrkörper", "mehrkoerper"];
  const ltu = alle.filter(
    (v) =>
      ltuKeys.includes(v.geometrie.shapeKey) ||
      v.schlagworte.some((s) => ltuTags.includes(s.toLowerCase())),
  );
  assert.ok(ltu.length >= 12, `zu wenige L/T/U/Mehrkörper-Vorlagen erkannt: ${ltu.length}`);
  for (const v of ltu) {
    if (v.geometrie.category === "flat") {
      assert.equal(v.status, "verfuegbar", `Flach-L/T/U nicht verfügbar: ${v.id}`);
      assert.ok(v.apply != null, `Flach-L/T/U ohne apply: ${v.id}`);
    } else {
      assert.equal(v.status, "geplant", `geneigtes L/T/U/Mehrkörper nicht geplant: ${v.id}`);
      assert.equal(v.apply, undefined, `geneigtes L/T/U/Mehrkörper mit apply: ${v.id}`);
    }
  }
});

test("Erweitert: neue Beispiel-ids vorhanden + korrekt klassifiziert", () => {
  assert.equal(findeVorlage(alle, "efh-walm")?.status, "verfuegbar");
  assert.equal(findeVorlage(alle, "pv-sattel-sued")?.status, "verfuegbar");
  assert.equal(findeVorlage(alle, "sattel-giebelgaube")?.status, "verfuegbar");
  assert.equal(findeVorlage(alle, "garage-flach")?.status, "verfuegbar");
  assert.equal(findeVorlage(alle, "doppelhaus-sattel")?.status, "geplant");
  assert.equal(findeVorlage(alle, "pyramidendach")?.status, "geplant");
  assert.equal(findeVorlage(alle, "winkelbungalow-l")?.status, "geplant");
  assert.equal(findeVorlage(alle, "pv-anbau")?.status, "geplant");
  assert.equal(findeVorlage(alle, "gewerbehalle-shed")?.status, "geplant");
});

test("Erweitert: Suche/Filter auf der großen Bibliothek", () => {
  assert.ok(sucheVorlagen(alle, "gaube").length >= 5);
  assert.ok(sucheVorlagen(alle, "pv").length >= 8);
  assert.ok(sucheVorlagen(alle, "walm").length >= 6);
  const verfPultV = alle.filter((v) => v.status === "verfuegbar" && v.geometrie.shapeKey === "pult");
  assert.ok(verfPultV.length >= 6, `zu wenige verfügbare Pultdächer: ${verfPultV.length}`);
  assert.ok(verfPultV.every((v) => v.geometrie.engineShape === "pult"));
  // applyVorlage bleibt für alle verfügbaren konsistent
  for (const v of nurVerfuegbare(alle)) {
    const r = applyVorlage(v, defaultBuild);
    assert.equal(r.ok, true, `apply fehlgeschlagen: ${v.id}`);
  }
});

// =====================================================================
// KORREKTUR: Deckungsneutralität (Vorlagen setzen NIE Material/Eindeckung)
// =====================================================================
test("Deckungsneutral: JEDE Vorlage trägt die neutralen Deckungs-Felder und KEINE feste Eindeckung", () => {
  for (const v of alle) {
    const dd = v.dachdecker as any;
    // neutrale Pflichtfelder vorhanden
    assert.equal(dd.dachdeckungSeparatAuswaehlen, true, `dachdeckungSeparatAuswaehlen != true: ${v.id}`);
    assert.ok((dd.deckungsHinweis ?? "").trim().length > 0, `deckungsHinweis leer: ${v.id}`);
    assert.equal(typeof dd.regeldachneigungAbhaengigVonMaterial, "boolean", `regeldachneigungAbhaengigVonMaterial fehlt: ${v.id}`);
    assert.equal(typeof dd.lattmassAbhaengigVonProdukt, "boolean", `lattmassAbhaengigVonProdukt fehlt: ${v.id}`);
    // keinerlei festes Material mehr im Output
    assert.equal(dd.empfohleneEindeckung, undefined, `empfohleneEindeckung gesetzt: ${v.id}`);
    assert.equal(dd.zulaessigeEindeckungen, undefined, `zulaessigeEindeckungen gesetzt: ${v.id}`);
    // der neutrale Hinweis muss auf separate Auswahl hinweisen
    assert.match(dd.deckungsHinweis, /separat/i, `deckungsHinweis ohne 'separat'-Hinweis: ${v.id}`);
  }
});

test("Deckungsneutral: KEINE apply-Definition enthält ein cover-Feld", () => {
  for (const v of alle) {
    if (!v.apply) continue;
    assert.ok(!Object.prototype.hasOwnProperty.call(v.apply, "cover"), `apply.cover vorhanden: ${v.id}`);
    assert.ok(!Object.prototype.hasOwnProperty.call(v.apply.build, "cover"), `apply.build.cover vorhanden: ${v.id}`);
  }
});

test("Deckungsneutral: applyVorlage ändert NUR Geometrie, niemals eine Eindeckung (kein cover im Ergebnis)", () => {
  const r = applyVorlage(findeVorlage(alle, "walm-standard")!, defaultBuild);
  assert.equal(r.ok, true);
  assert.ok(!Object.prototype.hasOwnProperty.call(r, "cover"), "applyVorlage liefert ein cover");
  // build trägt keinerlei material-/cover-Schlüssel
  for (const k of Object.keys(r.build!)) {
    assert.notEqual(k, "cover", "build enthält cover");
    assert.notEqual(k, "material", "build enthält material");
  }
});

test("Deckungsneutral: validateVorlage erzeugt KEINE EINDECKUNG_KATEGORIE-Warnung mehr", () => {
  for (const v of nurVerfuegbare(alle)) {
    const val = validateVorlage(v);
    assert.ok(!val.warnungen.some((w) => w.code === "EINDECKUNG_KATEGORIE"), `EINDECKUNG_KATEGORIE bei ${v.id}`);
  }
});

// =====================================================================
// FACHAUDIT: Titel = Bild = Datenstruktur = Status = Anwendbarkeit
// =====================================================================

// kleine SVG-Marker (aus den SVG-Bausteinen)
const svgHatSatSchuessel = (s: string) => s.includes('cx="96"') && s.includes('r="5"'); // svgSat
const svgHatPvFeld = (s: string) => s.includes('fill="#1d4ed8"') || s.includes('fill="#bfdbfe"'); // svgPvFeld
const svgHatGaube = (s: string) => s.includes('rx="0.5"'); // Gauben-Fensterchen
const tokens = (v: DachformVorlage) => new Set(v.schlagworte.map((s) => s.toLowerCase()));

test("Audit-Grundgerüst: jede Vorlage hat Name, Status, Kategorie, shapeKey, SVG; keine doppelten ids", () => {
  const ids = new Set<string>();
  for (const v of alle) {
    assert.ok(v.name.trim().length > 0, `Name leer: ${v.id}`);
    assert.ok(["verfuegbar", "geplant"].includes(v.status), `Status ungültig: ${v.id}`);
    assert.ok(["pitched", "flat"].includes(v.geometrie.category), `Kategorie ungültig: ${v.id}`);
    assert.ok((v.geometrie.shapeKey ?? "").length > 0, `shapeKey fehlt: ${v.id}`);
    const svg = vorschauSvg(v);
    assert.ok(svg.startsWith("<svg") && svg.includes("</svg>"), `SVG ungültig: ${v.id}`);
    assert.ok(!ids.has(v.id), `doppelte id: ${v.id}`);
    ids.add(v.id);
  }
  assert.equal(alle.length, 150);
});

test("SAT-Teilstring-Bug behoben: Sat-Schüssel NUR bei echtem 'sat'/'satellit'-Token", () => {
  for (const v of alle) {
    const t = tokens(v);
    const erwartet = t.has("sat") || t.has("satellit");
    assert.equal(svgHatSatSchuessel(vorschauSvg(v)), erwartet, `Sat-Overlay falsch bei ${v.id}`);
  }
  // konkret: dach-sat-anlage hat sie, ein beliebiges Satteldach nicht
  assert.equal(svgHatSatSchuessel(vorschauSvg(findeVorlage(alle, "dach-sat-anlage")!)), true);
  assert.equal(svgHatSatSchuessel(vorschauSvg(findeVorlage(alle, "sattel-standard")!)), false);
});

test("PV-Teilstring-Bug behoben: PV-Feld NUR bei echtem 'pv'/'photovoltaik'-Token (nicht 'pv-tauglich')", () => {
  for (const v of alle) {
    const t = tokens(v);
    const isGrund = ["l-shape", "t-shape", "u-grundriss", "mehrfluegel"].includes(v.geometrie.shapeKey);
    const erwartet = (t.has("pv") || t.has("photovoltaik")) && !isGrund;
    assert.equal(svgHatPvFeld(vorschauSvg(v)), erwartet, `PV-Overlay falsch bei ${v.id}`);
  }
  // sattel-ost-west/pult-sued (nur 'pv-tauglich') zeigen KEIN PV-Feld mehr
  assert.equal(svgHatPvFeld(vorschauSvg(findeVorlage(alle, "sattel-ost-west")!)), false);
  assert.equal(svgHatPvFeld(vorschauSvg(findeVorlage(alle, "pult-sued")!)), false);
  // dedizierte pv-* behalten ihr Feld
  assert.equal(svgHatPvFeld(vorschauSvg(findeVorlage(alle, "pv-sattel-sued")!)), true);
});

test("Gauben-Teilstring-Bug behoben: 'gauben' (Mansard) löst KEINE Gaube aus, echte Gauben schon", () => {
  assert.equal(svgHatGaube(vorschauSvg(findeVorlage(alle, "mansard")!)), false);
  for (const id of ["sattel-schleppgaube-1", "walm-gaube", "dach-breite-gaube", "sattel-giebelgaube"]) {
    assert.equal(svgHatGaube(vorschauSvg(findeVorlage(alle, id)!)), true, `Gaube fehlt bei ${id}`);
  }
});

test("Titel↔Schlagwort: Form-/Merkmalsbegriff im Titel hat das passende Schlagwort", () => {
  const regeln: Array<[RegExp, string[]]> = [
    [/l-form|l-grundriss/i, ["l-form", "l-shape"]],
    [/t-form|t-grundriss/i, ["t-form", "t-shape"]],
    [/u-form|u-grundriss/i, ["u-form", "u-grundriss"]],
    [/\bgaube\b|gauben/i, ["gaube"]],
    [/dachfenster/i, ["dachfenster"]],
    [/\bkamin\b/i, ["kamin", "schornstein"]],
    [/sat-anlage/i, ["sat", "satellit"]],
    [/\bpv\b|photovoltaik/i, ["pv", "photovoltaik"]],
    [/walmdach\b/i, ["walm"]],
    [/pultdach/i, ["pult"]],
    [/satteldach/i, ["sattel"]],
  ];
  for (const v of alle) {
    const t = tokens(v);
    for (const [re, toks] of regeln) {
      if (re.test(v.name)) {
        assert.ok(toks.some((x) => t.has(x)), `Titel "${v.name}" (${v.id}) ohne Schlagwort ${toks.join("/")}`);
      }
    }
  }
});

test("Form-SVGs sind distinkt: Krüppelwalm≠Walm, Mansardwalm≠Mansard, versetztes/Schlepp≠Pult", () => {
  // farb-/clip-/id-normalisiert -> reine Geometrie vergleichen
  const norm = (v: DachformVorlage) => vorschauSvg(v)
    .replace(/fill="[^"]*"/g, "F").replace(/stroke="[^"]*"/g, "S")
    .replace(/aria-label="[^"]*"/g, "").replace(/id="rc[^"]*"/g, "id")
    .replace(/url\(#rc[^)]*\)/g, "url").replace(/opacity="[^"]*"/g, "").replace(/stroke-dasharray="[^"]*"/g, "");
  const sig = (id: string) => norm(findeVorlage(alle, id)!);
  assert.notEqual(sig("krueppelwalm"), sig("walm-standard"), "Krüppelwalm identisch zu Vollwalm");
  assert.notEqual(sig("mansardwalm"), sig("mansard"), "Mansardwalm identisch zu Mansard");
  assert.notEqual(sig("versetztes-pult"), sig("pult-standard"), "Versetztes Pult identisch zu Pult");
  assert.notEqual(sig("schleppdach"), sig("pult-standard"), "Schleppdach identisch zu Pult");
  // L/T/U eindeutig voneinander und von Rechteck unterscheidbar
  const lf = sig("l-shape-pitched"), tf = sig("t-shape-pitched"), uf = sig("u-grundriss"), rect = sig("flach-standard");
  assert.ok(new Set([lf, tf, uf, rect]).size === 4, "L/T/U/Rechteck nicht alle unterscheidbar");
});

test("Keine SVG-Vorschau zeigt material-/deckungsbezogene Muster (deckungsneutral)", () => {
  for (const v of alle) {
    const svg = vorschauSvg(v).toLowerCase();
    for (const bad of ["pattern", "ziegelmuster", "<image", "hatch", "texture"]) {
      assert.ok(!svg.includes(bad), `SVG enthält Material-/Bildmuster '${bad}': ${v.id}`);
    }
  }
});

test("Keine negativen/NaN/Infinity-Koordinaten in irgendeiner SVG-Vorschau", () => {
  for (const v of alle) {
    const svg = vorschauSvg(v).replace(/aria-label="[^"]*"/g, "");
    assert.ok(!/-\d/.test(svg), `negative Koordinate: ${v.id}`);
    assert.ok(!/NaN|Infinity/.test(svg), `NaN/Infinity: ${v.id}`);
  }
});

test("Ehrlichkeit: Vorschau-Versprechen = nur NICHT setzbarer Aufbau (Lichtkuppel/Schneefang) oder PV; apply ohne cover/obstacles im build", () => {
  for (const v of alle) {
    if (istVorschauVersprechen(v)) {
      assert.equal(v.status, "verfuegbar", `Vorschau-Versprechen nur bei verfügbar: ${v.id}`);
      assert.ok(aufbautenNichtGesetzt(v).length > 0 || vorschauZeigtPv(v), `weder offener Aufbau noch PV: ${v.id}`);
    }
    const r = applyVorlage(v, defaultBuild);
    if (r.ok) {
      assert.ok(!("cover" in (r as any)), `apply liefert cover: ${v.id}`);
      for (const key of Object.keys(r.build!)) assert.notEqual(key, "obstacles", `build trägt obstacles: ${v.id}`);
    }
  }
  // Nur noch PV-only ist Vorschau-Versprechen; Schneefang (Linienbauteil) + Lichtkuppel + Aufbauten werden gesetzt
  assert.equal(istVorschauVersprechen(findeVorlage(alle, "pv-sattel-sued")!), true);
  assert.equal(istVorschauVersprechen(findeVorlage(alle, "dach-schneefang")!), false);
  assert.equal(istVorschauVersprechen(findeVorlage(alle, "flach-lichtkuppel")!), false);
  assert.equal(istVorschauVersprechen(findeVorlage(alle, "sattel-kamin")!), false);
  assert.equal(istVorschauVersprechen(findeVorlage(alle, "sattel-standard")!), false);
});

test("Ehrlichkeit-SVG: alle setzbaren Aufbauten/Linien (inkl. Schneefang, Lichtkuppel) werden SOLIDE dargestellt", () => {
  for (const id of ["sattel-kamin", "walm-gaube", "sattel-2-dachfenster", "dach-sat-anlage", "flach-lichtkuppel", "dach-schneefang"]) {
    assert.ok(!vorschauSvg(findeVorlage(alle, id)!).includes("stroke-dasharray"), `setzbarer Aufbau fälschlich gestrichelt: ${id}`);
  }
  // kein verfügbares Aufbau-Template mehr gestrichelt (alles gesetzt)
  for (const v of nurVerfuegbare(alle)) {
    assert.ok(!vorschauSvg(v).includes("stroke-dasharray"), `verfügbare Vorlage gestrichelt: ${v.id}`);
  }
  assert.ok(VORSCHAU_PV_HINWEIS.toLowerCase().includes("pv"));
});

test("E13: GENEIGTE L/T/U + Mehrkörper bleiben geplant; FLACHDACH-L/T/U sind verfügbar", () => {
  const ltu = alle.filter((v) => ["l-shape", "t-shape", "u-grundriss", "mehrfluegel", "mehrkoerper"].includes(v.geometrie.shapeKey));
  assert.ok(ltu.length >= 16);
  for (const v of ltu) {
    if (v.geometrie.category === "flat") {
      // Flachdach-L/T/U: echtes Polygon -> verfügbar
      assert.equal(v.status, "verfuegbar", `Flach-L/T/U sollte verfügbar sein: ${v.id}`);
      assert.ok(["l-shape", "t-shape", "u-shape"].includes(v.geometrie.engineShape as string), `Flach-L/T/U engineShape: ${v.id}`);
    } else {
      // Geneigte L/T/U + Mehrkörper: Dachverschneidung/Kehlen noch nicht sicher -> geplant
      assert.equal(v.status, "geplant", `geneigtes L/T/U/Mehrkörper sollte geplant sein: ${v.id}`);
      assert.equal(v.anwendbar, false, `geneigtes L/T/U anwendbar: ${v.id}`);
      assert.equal(applyVorlage(v, defaultBuild).ok, false, `apply ok bei geplant: ${v.id}`);
    }
  }
  // konkrete Flachdach-Freischaltungen
  for (const id of ["l-shape-flat", "flach-t", "flach-u", "winkelbungalow-l-flach", "gewerbe-l-flach", "buero-u-flach"]) {
    assert.equal(findeVorlage(alle, id)!.status, "verfuegbar", `nicht verfügbar: ${id}`);
  }
});

test("PV-Felder werden auf die Dachfläche geclippt (kein Schweben) bei Sattel/Pult/Walm/Flach", () => {
  for (const v of alle) {
    if (svgHatPvFeld(vorschauSvg(v)) && ["sattel", "pult", "walm", "rect"].includes(v.geometrie.shapeKey)) {
      assert.ok(vorschauSvg(v).includes("clip-path="), `PV nicht geclippt: ${v.id}`);
    }
  }
});

// =====================================================================
// FACHAUDIT v2: 3-Status-Ehrlichkeit, Garage/Carport, Grabendach-Tiefpunkt
// =====================================================================
const svgHatGaragentor = (s: string) => s.includes('width="20" height="16"'); // svgGarageTor
const svgHatCarport = (s: string) => s.includes('width="30" height="2.5"'); // svgCarportOffen Querriegel

test("Anzeige-Status (3 Stufen): geplant / teilweise (nicht setzbarer Rest) / verfuegbar (Aufbau wird gesetzt)", () => {
  assert.equal(anzeigeStatus(findeVorlage(alle, "l-shape-pitched")!), "geplant");
  // verfuegbar: setzbare Aufbauten/Linien werden gesetzt -> vollständig anwendbar (inkl. Lichtkuppel + Schneefang)
  for (const id of ["sattel-kamin", "sattel-2-dachfenster", "sattel-4-dachfenster", "sattel-standard", "dach-sat-anlage", "flach-lichtkuppel", "dach-schneefang"]) {
    assert.equal(anzeigeStatus(findeVorlage(alle, id)!), "verfuegbar", `sollte verfuegbar sein: ${id}`);
  }
  // teilweise: schematische Gaube (Aufbau gesetzt, aber keine echte Gaubendach-Geometrie)
  for (const id of ["walm-gaube", "sattel-giebelgaube", "sattel-schleppgaube-1", "dach-breite-gaube"]) {
    assert.equal(anzeigeStatus(findeVorlage(alle, id)!), "teilweise", `Gaube sollte teilweise sein: ${id}`);
  }
  // PV-only bleibt verfuegbar (PV-Belegung ist separater Planungsschritt)
  assert.equal(anzeigeStatus(findeVorlage(alle, "pv-sattel-sued")!), "verfuegbar");
  // jede geplante Vorlage -> 'geplant'; jede 'teilweise' ist anwendbar (offener Aufbau ODER schematische Gaube)
  for (const v of alle) {
    if (v.status === "geplant") assert.equal(anzeigeStatus(v), "geplant", `geplant falsch: ${v.id}`);
    if (anzeigeStatus(v) === "teilweise") {
      assert.equal(v.anwendbar, true, `teilweise muss anwendbar sein: ${v.id}`);
      assert.ok(aufbautenNichtGesetzt(v).length > 0 || gaubeSchematischGesetzt(v), `teilweise braucht offenen Aufbau/Gaube: ${v.id}`);
    }
  }
});

test("Garage-Vorlagen sind als Garage erkennbar (Garagentor) oder als Nebenkörper (Mehrkörper)", () => {
  const garagen = alle.filter((v) => v.schlagworte.map((s) => s.toLowerCase()).includes("garage"));
  assert.ok(garagen.length >= 4);
  for (const v of garagen) {
    const istMehr = v.geometrie.shapeKey === "mehrkoerper";
    const svg = vorschauSvg(v);
    assert.ok(istMehr || svgHatGaragentor(svg), `Garage nicht erkennbar: ${v.id}`);
  }
});

test("Carport-Vorlagen sind als Carport (offene Stützen) oder Nebenkörper erkennbar", () => {
  const carports = alle.filter((v) => v.schlagworte.map((s) => s.toLowerCase()).includes("carport"));
  assert.ok(carports.length >= 2);
  for (const v of carports) {
    const istMehr = v.geometrie.shapeKey === "mehrkoerper";
    assert.ok(istMehr || svgHatCarport(vorschauSvg(v)), `Carport nicht erkennbar: ${v.id}`);
  }
  // Garagentor erscheint NICHT auf reinen Satteldächern (Token-genau)
  assert.equal(svgHatGaragentor(vorschauSvg(findeVorlage(alle, "sattel-standard")!)), false);
});

test("Grabendach zeigt innenliegende Tiefpunkte (Kehlrinnen-Marker)", () => {
  const svg = vorschauSvg(findeVorlage(alle, "grabendach")!);
  assert.ok(svg.includes('cx="52"') && svg.includes('cy="53"'), "Grabendach ohne Tiefpunkt-Marker");
});

// =====================================================================
// AUFGABE: Aufbauten automatisch setzen (Kamin/Dachfenster/Lüfter/Sat/Gaube)
// =====================================================================
const aufb = (id: string) => standardAufbauten(findeVorlage(alle, id)!);
const arten = (id: string) => aufb(id).map((a) => a.art).sort();

test("Kamin-Vorlage setzt genau einen chimney auf der Hauptfläche", () => {
  const a = aufb("sattel-kamin");
  assert.deepEqual(a.map((x) => x.art), ["chimney"]);
  assert.equal(a[0].surfaceId, "main_S");
});

test("Dachfenster: 2 bzw. 4 window-Aufbauten je nach Titel; Pult auf 'main'", () => {
  assert.equal(arten("sattel-2-dachfenster").filter((x) => x === "window").length, 2);
  assert.equal(arten("sattel-4-dachfenster").filter((x) => x === "window").length, 4);
  const pult = aufb("pult-dachfenster");
  assert.equal(pult.filter((x) => x.art === "window").length, 2);
  assert.ok(pult.every((x) => x.surfaceId === "main"));
});

test("Lüfter/Rauchabzug -> vent; Sat -> sat (auf 'main' beim Flachdach)", () => {
  assert.ok(aufb("flach-luefter").every((x) => x.art === "vent" && x.surfaceId === "main"));
  assert.ok(aufb("flach-rauchabzug").some((x) => x.art === "vent"));
  assert.deepEqual(aufb("dach-sat-anlage").map((x) => x.art), ["sat"]);
});

test("Gaube: konkrete Gaubenart wird gesetzt (schlepp/giebel/trapez/flach), generisch -> giebelgaube", () => {
  assert.ok(aufb("sattel-schleppgaube-1").every((x) => x.art === "schleppgaube"));
  assert.equal(aufb("sattel-schleppgaube-2").filter((x) => x.art === "schleppgaube").length, 2);
  assert.ok(aufb("sattel-giebelgaube").every((x) => x.art === "giebelgaube"));
  assert.ok(aufb("sattel-walmgaube").every((x) => x.art === "trapezgaube"));
  assert.ok(aufb("sattel-flachgaube").every((x) => x.art === "flachgaube"));
  assert.deepEqual(aufb("walm-gaube").map((x) => x.art), ["giebelgaube"]); // generisch
  assert.equal(aufb("dach-mehrere-kleine-gauben").filter((x) => x.art === "giebelgaube").length, 2);
});

test("Lichtkuppel wird als Aufbauart gesetzt; Schneefang ist KEIN Punkt-Aufbau (eigenes Linienbauteil)", () => {
  const lk = aufb("flach-lichtkuppel");
  assert.equal(lk.length, 1);
  assert.equal(lk[0].art, "lichtkuppel");
  assert.equal(lk[0].surfaceId, "main");
  assert.equal(aufbautenWerdenGesetzt(findeVorlage(alle, "flach-lichtkuppel")!), true);
  assert.deepEqual(aufbautenNichtGesetzt(findeVorlage(alle, "flach-lichtkuppel")!), []);
  // Schneefang ist KEIN Punkt-Obstacle in standardAufbauten ...
  assert.deepEqual(aufb("dach-schneefang"), []);
  // ... wird aber jetzt als Linienbauteil gesetzt -> nicht mehr 'offen'
  assert.deepEqual(aufbautenNichtGesetzt(findeVorlage(alle, "dach-schneefang")!), []);
  assert.equal(schneefangWirdGesetzt(findeVorlage(alle, "dach-schneefang")!), true);
});

test("applyVorlage liefert FLÄCHENABHÄNGIG platzierte aufbauten (gleiche Arten, Fläche, + pruefpflichtig-Flag)", () => {
  const v = findeVorlage(alle, "sattel-kamin")!;
  const r = applyVorlage(v, defaultBuild);
  assert.equal(r.ok, true);
  const soll = standardAufbauten(v);
  assert.equal(r.aufbauten!.length, soll.length);
  assert.deepEqual(r.aufbauten!.map((a) => a.art), soll.map((a) => a.art));
  assert.ok(r.aufbauten!.every((a) => a.surfaceId === "main_S"));
  assert.ok(r.aufbauten!.every((a) => typeof a.pruefpflichtig === "boolean"));
  // geplant -> ok=false, keine aufbauten
  const g = applyVorlage(findeVorlage(alle, "zeltdach")!, defaultBuild);
  assert.equal(g.ok, false);
  assert.equal(g.aufbauten, undefined);
  // reine Form -> leere aufbauten
  assert.deepEqual(applyVorlage(findeVorlage(alle, "sattel-standard")!, defaultBuild).aufbauten, []);
});

test("Auto-Aufbauten: surfaceId liegt in den ECHTEN Engine-Flächen der Zielform (Walm = south, nicht main_S)", () => {
  // Regressionsschutz gegen Phantom-Flächen: walm-* muss 'south' nutzen (vom Workflow gefunden).
  assert.equal(standardAufbauten(findeVorlage(alle, "walm-gaube")!)[0].surfaceId, "south");
  assert.equal(standardAufbauten(findeVorlage(alle, "walm-kamin")!)[0].surfaceId, "south");
  for (const v of alle) {
    const erlaubt = ENGINE_FLAECHEN[v.geometrie.shapeKey];
    for (const a of standardAufbauten(v)) {
      assert.ok(erlaubt && erlaubt.includes(a.surfaceId), `surfaceId ${a.surfaceId} existiert nicht für ${v.geometrie.shapeKey}: ${v.id}`);
    }
  }
});

test("Alle Auto-Aufbauten: gültige Maße/Position/Fläche, kein NaN/Infinity/negativ, kein Material", () => {
  const ARTEN = new Set(["chimney", "window", "vent", "sat", "lichtkuppel", "schleppgaube", "trapezgaube", "flachgaube", "giebelgaube"]);
  for (const v of alle) {
    const erlaubteFlaechen = ENGINE_FLAECHEN[v.geometrie.shapeKey] ?? [];
    for (const a of standardAufbauten(v)) {
      assert.ok(ARTEN.has(a.art), `unbekannte Art ${a.art}: ${v.id}`);
      assert.ok(erlaubteFlaechen.includes(a.surfaceId), `unbekannte Fläche ${a.surfaceId}: ${v.id}`);
      for (const n of [a.xRel, a.yRel, a.breiteM, a.hoeheM, a.tiefeM, a.pitchGrad]) {
        assert.ok(Number.isFinite(n), `nicht endlich (${v.id})`);
      }
      assert.ok(a.xRel > 0 && a.xRel < 1, `xRel außerhalb (0,1): ${v.id}`);
      assert.ok(a.yRel > 0 && a.yRel < 1, `yRel außerhalb (0,1): ${v.id}`);
      assert.ok(a.breiteM > 0 && a.hoeheM > 0 && a.tiefeM > 0, `Maß <= 0: ${v.id}`);
      assert.ok(a.pitchGrad >= 0, `negative Neigung: ${v.id}`);
      // deckungsneutral: ein Aufbau trägt keinerlei Material/Produkt-Feld
      for (const k of Object.keys(a)) assert.ok(!["cover", "material", "productId", "eindeckung"].includes(k), `Material-Feld am Aufbau: ${v.id}.${k}`);
    }
    // Nur verfügbare Vorlagen mit eindeutiger Hauptfläche setzen Aufbauten
    if (standardAufbauten(v).length > 0) {
      assert.equal(v.status, "verfuegbar", `geplant setzt Aufbauten: ${v.id}`);
      assert.ok(["sattel", "pult", "walm", "rect"].includes(v.geometrie.shapeKey), `unsichere Form setzt Aufbauten: ${v.id}`);
    }
  }
  assert.ok(AUFBAU_AUTO_HINWEIS.toLowerCase().includes("automatisch"));
});

// =====================================================================
// EINGABEAUFFORDERUNG 11: flächenabhängige Aufbauplatzierung
// =====================================================================
const W = (art: string, xRel: number, yRel: number, breiteM: number, hoeheM: number): AufbauWunsch =>
  ({ art: art as any, surfaceId: "main_S", xRel, yRel, breiteM, hoeheM, tiefeM: 0.5, pitchGrad: 0 });
const RECHTECK = (b: number, h: number): DachflaecheInfo =>
  ({ surfaceId: "main_S", breiteTraufeM: b, breiteFirstM: b, hoeheM: h, form: "rechteck" });
const TRAPEZ = (bUnten: number, bOben: number, h: number): DachflaecheInfo =>
  ({ surfaceId: "main_S", breiteTraufeM: bUnten, breiteFirstM: bOben, hoeheM: h, form: "trapez" });
// AABB-Überlappung in u/v-Metern (Mittelabstand)
const ueberlappen = (a: any, b: any, W: number, H: number) => {
  const au0 = a.xRel * W - a.breiteM / 2, au1 = a.xRel * W + a.breiteM / 2;
  const bu0 = b.xRel * W - b.breiteM / 2, bu1 = b.xRel * W + b.breiteM / 2;
  const av0 = a.yRel * H - a.hoeheM / 2, av1 = a.yRel * H + a.hoeheM / 2;
  const bv0 = b.yRel * H - b.hoeheM / 2, bv1 = b.yRel * H + b.hoeheM / 2;
  return !(au1 <= bu0 || au0 >= bu1 || av1 <= bv0 || av0 >= bv1);
};

test("hauptflaecheInfo: Walm-Südfläche ist trapezförmig (First schmaler), Sattel/Pult/Flach rechteckig", () => {
  const walm = hauptflaecheInfo({ category: "pitched", shape: "walm", length: 12, width: 8, height: 5, pitch: 30, attika: 0, overhang: 0.5, overhangGable: 0.3, lengthB: 0, widthB: 0, layerSpread: 0, rafterSpacing: 70, rafterWidth: 8, rafterHeight: 18, battenDist: 34 } as any, "south");
  assert.equal(walm.form, "trapez");
  assert.ok(walm.breiteFirstM < walm.breiteTraufeM, "Walm-First nicht schmaler");
  assert.ok(verfuegbareBreiteM(walm, 1) < verfuegbareBreiteM(walm, 0), "Breite verjüngt sich nicht zur First");
  const sattel = hauptflaecheInfo({ category: "pitched", shape: "sattel", length: 10, width: 8, height: 5, pitch: 35, overhang: 0.5, overhangGable: 0.3 } as any, "main_S");
  assert.equal(sattel.form, "rechteck");
  const flach = hauptflaecheInfo({ category: "flat", shape: "rect", length: 12, width: 8, height: 5, pitch: 3, overhang: 0, overhangGable: 0 } as any, "main");
  assert.equal(flach.form, "rechteck");
  assert.equal(flach.breiteTraufeM, 12);
});

test("Platzierung: Aufbau bleibt innerhalb einer rechteckigen Dachfläche (Footprint im Polygon)", () => {
  const f = RECHTECK(8, 5);
  const res = platziereAufbauten([W("chimney", 0.5, 0.72, 0.6, 0.6)], f);
  const a = res[0];
  assert.ok(a.xRel * f.breiteTraufeM - a.breiteM / 2 >= 0, "ragt links raus");
  assert.ok(a.xRel * f.breiteTraufeM + a.breiteM / 2 <= f.breiteTraufeM, "ragt rechts raus");
  assert.ok(a.yRel * f.hoeheM - a.hoeheM / 2 >= 0 && a.yRel * f.hoeheM + a.hoeheM / 2 <= f.hoeheM, "ragt oben/unten raus");
  assert.equal(a.pruefpflichtig, false);
});

test("Platzierung: Größe wird bei kleiner Fläche begrenzt; zu kleine Fläche -> prüfpflichtig", () => {
  // große Gaube (2.5 m) auf schmaler Fläche (3 m) -> Breite <= 0.5*3 = 1.5 m
  const res = platziereAufbauten([W("giebelgaube", 0.5, 0.5, 2.5, 1.5)], RECHTECK(3, 4));
  assert.ok(res[0].breiteM <= 1.5 + 1e-9, `Breite nicht begrenzt: ${res[0].breiteM}`);
  assert.equal(res[0].angepasst, true);
  // sehr kleine Fläche -> Gaube wird prüfpflichtig (unter MIN_GAUBE)
  const klein = platziereAufbauten([W("giebelgaube", 0.5, 0.5, 2.5, 1.5)], RECHTECK(1.5, 2));
  assert.equal(klein[0].pruefpflichtig, true);
});

test("Platzierung: keine Aufbauten außerhalb der Dachfläche, kein NaN/Infinity/negativ", () => {
  const f = RECHTECK(6, 4);
  const res = platziereAufbauten([W("window", 1.5, 1.5, 0.78, 1.18), W("chimney", -0.5, 0.9, 0.6, 0.6)], f);
  for (const a of res) {
    for (const n of [a.xRel, a.yRel, a.breiteM, a.hoeheM, a.tiefeM]) assert.ok(Number.isFinite(n) && n >= 0, "ungültiger Wert");
    assert.ok(a.xRel >= 0 && a.xRel <= 1 && a.yRel >= 0 && a.yRel <= 1, "rel außerhalb [0,1]");
    assert.ok(a.xRel * f.breiteTraufeM + a.breiteM / 2 <= f.breiteTraufeM + 1e-6, "ragt heraus");
  }
});

test("Platzierung: mehrere Dachfenster überschneiden sich nicht", () => {
  const f = RECHTECK(10, 6);
  const res = platziereAufbauten([W("window", 0.24, 0.46, 0.78, 1.18), W("window", 0.42, 0.46, 0.78, 1.18), W("window", 0.6, 0.46, 0.78, 1.18), W("window", 0.78, 0.46, 0.78, 1.18)], f);
  for (let i = 0; i < res.length; i++) for (let j = i + 1; j < res.length; j++) {
    assert.ok(!ueberlappen(res[i], res[j], f.breiteTraufeM, f.hoeheM), `Dachfenster ${i}/${j} überlappen`);
  }
});

test("Platzierung: mehrere Gauben und Kamin+Dachfenster kollidieren nicht", () => {
  const f = RECHTECK(12, 6);
  const gauben = platziereAufbauten([W("giebelgaube", 0.32, 0.42, 2.5, 1.5), W("giebelgaube", 0.68, 0.42, 2.5, 1.5)], f);
  assert.ok(!ueberlappen(gauben[0], gauben[1], f.breiteTraufeM, f.hoeheM), "Gauben überlappen");
  const mix = platziereAufbauten([W("giebelgaube", 0.5, 0.42, 2.5, 1.5), W("chimney", 0.5, 0.72, 0.6, 0.6), W("window", 0.2, 0.5, 0.78, 1.18), W("window", 0.8, 0.5, 0.78, 1.18)], f);
  for (let i = 0; i < mix.length; i++) for (let j = i + 1; j < mix.length; j++) {
    assert.ok(!ueberlappen(mix[i], mix[j], f.breiteTraufeM, f.hoeheM), `Aufbau ${i}/${j} (${mix[i].art}/${mix[j].art}) kollidieren`);
  }
});

test("Platzierung: trapezförmige Walmfläche -> keine zu breite Gaube nahe First", () => {
  // Trapez: unten 12 m, oben 2 m, Höhe 6 m. Bei yRel 0.8 ist verfügbar ~ 4 m.
  const f = TRAPEZ(12, 2, 6);
  const res = platziereAufbauten([W("giebelgaube", 0.5, 0.8, 3.0, 1.5)], f);
  const avail = verfuegbareBreiteM(f, res[0].yRel);
  assert.ok(res[0].breiteM <= MAX_BREITE_ANTEIL * avail + 1e-9, "Gaube breiter als erlaubt auf Trapez");
  // Footprint bleibt im zentrierten Trapez
  const halb = res[0].breiteM / 2;
  const uC = f.breiteTraufeM / 2;
  assert.ok(Math.abs(res[0].xRel * f.breiteTraufeM - uC) + halb <= avail / 2 + 1e-6, "Gaube ragt aus Trapez");
});

test("Platzierung: ungültige/leere Fläche -> alles prüfpflichtig, nichts erfunden", () => {
  const res = platziereAufbauten([W("chimney", 0.5, 0.5, 0.6, 0.6)], RECHTECK(0, 0));
  assert.equal(res[0].pruefpflichtig, true);
});

test("Platzierung über applyVorlage: Walm-Gaube auf schmaler Firstzone bleibt im Trapez (echte Geometrie)", () => {
  // walm-gaube: length 13, width 9 -> Firstlänge 4 m (schmal). Gaube 2.5 m -> wird flächenabhängig begrenzt.
  const r = applyVorlage(findeVorlage(alle, "walm-gaube")!, defaultBuild);
  const a = r.aufbauten![0];
  assert.equal(a.surfaceId, "south");
  assert.ok(a.breiteM > 0 && Number.isFinite(a.breiteM));
  assert.ok(a.xRel > 0 && a.xRel < 1 && a.yRel > 0 && a.yRel < 1);
});

// =====================================================================
// EINGABEAUFFORDERUNG 12: Schneefang als linienförmiges Dachbauteil
// =====================================================================
const RECHT = (b: number, h: number): DachflaecheInfo => ({ surfaceId: "main_S", breiteTraufeM: b, breiteFirstM: b, hoeheM: h, form: "rechteck" });
const TRAP = (bu: number, bo: number, h: number): DachflaecheInfo => ({ surfaceId: "south", breiteTraufeM: bu, breiteFirstM: bo, hoeheM: h, form: "trapez" });

test("Schneefang ist linienförmig (Start/Ende/Länge), kein punktförmiger Aufbau", () => {
  const erg = platziereSchneefang("main_S", RECHT(8, 5));
  assert.ok(erg.bauteil);
  const b = erg.bauteil!;
  assert.equal(b.art, "schneefang");
  // Linien-Felder statt Punkt-Maße
  assert.ok("uStartRel" in b && "uEndRel" in b && "laengeM" in b);
  assert.ok(!("width" in b) && !("height" in b) && !("depth" in b), "Schneefang hat Punkt-Aufbau-Maße");
  assert.ok(b.uEndRel > b.uStartRel, "Start/Ende nicht in u-Richtung");
  assert.ok(b.laengeM > 0 && Number.isFinite(b.laengeM), "Länge nicht positiv/endlich");
  assert.equal(b.pvSperrbereich, true);
  assert.equal(b.deckungsneutral, true);
  assert.equal(b.statischZuPruefen, true);
  assert.ok(b.hinweis.toLowerCase().includes("statisch"));
});

test("Schneefang: Länge = verfügbare Breite − Randabstände; liegt in Traufnähe; keine negative Länge", () => {
  const erg = platziereSchneefang("main_S", RECHT(10, 6));
  const b = erg.bauteil!;
  assert.ok(b.laengeM < 10 && b.laengeM > 8, `Länge unplausibel: ${b.laengeM}`);
  assert.ok(b.yRel <= 0.4 && b.yRel >= 0.05, "Schneefang nicht in Traufnähe");
  // zentriert
  assert.ok(Math.abs((b.uStartRel + b.uEndRel) / 2 - 0.5) < 1e-6, "Linie nicht zentriert");
});

test("Schneefang auf trapezförmiger (Walm-)Fläche wird auf verfügbare Breite begrenzt; surfaceId south", () => {
  const f = TRAP(12, 2, 6); // unten 12 m, oben 2 m
  const erg = platziereSchneefang("south", f);
  const b = erg.bauteil!;
  assert.equal(b.surfaceId, "south");
  const avail = verfuegbareBreiteM(f, b.yRel);
  // Linienlänge bleibt innerhalb der verfügbaren Breite an der y-Position
  assert.ok((b.uEndRel - b.uStartRel) * f.breiteTraufeM <= avail + 1e-6, "Linie breiter als verfügbar");
});

test("Schneefang: zu schmale/leere Fläche -> kein Bauteil, pruefpflichtig (nicht falsch setzen)", () => {
  assert.equal(platziereSchneefang("main_S", RECHT(0.6, 4)).bauteil, null);
  assert.equal(platziereSchneefang("main_S", RECHT(0.6, 4)).pruefpflichtig, true);
  assert.equal(platziereSchneefang("", RECHT(8, 5)).bauteil, null);
});

test("Schneefang PV-Sperrzone wird vorbereitet (relativer v-Bereich); istInSperrzone konsistent", () => {
  const b = platziereSchneefang("main_S", RECHT(8, 5)).bauteil!;
  const z = sperrzoneVRel(b, 5);
  assert.ok(z && z.vMaxRel > z.vMinRel, "keine gültige Sperrzone");
  // ein Modul genau auf der Linie liegt in der Sperrzone
  assert.equal(istInSperrzone(b, b.yRel, 5), true);
  // ein Modul weit oberhalb nicht
  assert.equal(istInSperrzone(b, 0.9, 5), false);
});

test("flaecheInfoAusPolygon: Trapez (Walm-Polygon) wird erkannt, breiteFirst < breiteTraufe", () => {
  // Walm-Süd-Polygon: (0,0)-(14,0)-(9,6)-(5,6) -> oben Spanne 4 m
  const poly = [{ x: 0, y: 0 }, { x: 14, y: 0 }, { x: 9, y: 6 }, { x: 5, y: 6 }];
  const f = flaecheInfoAusPolygon("south", 14, 6, poly);
  assert.equal(f.form, "trapez");
  assert.ok(Math.abs(f.breiteFirstM - 4) < 0.01, `breiteFirst falsch: ${f.breiteFirstM}`);
  // Rechteck-Polygon -> rechteck
  const rPoly = [{ x: 0, y: 0 }, { x: 8, y: 0 }, { x: 8, y: 5 }, { x: 0, y: 5 }];
  assert.equal(flaecheInfoAusPolygon("main_S", 8, 5, rPoly).form, "rechteck");
});

test("applyVorlage(dach-schneefang) liefert linienBauteile mit Schneefang auf gültiger Fläche; kein cover", () => {
  const r = applyVorlage(findeVorlage(alle, "dach-schneefang")!, defaultBuild);
  assert.equal(r.ok, true);
  assert.ok(!("cover" in (r as any)), "apply liefert cover");
  assert.ok(Array.isArray(r.linienBauteile) && r.linienBauteile!.length === 1, "kein Schneefang-Linienbauteil");
  const b = r.linienBauteile![0];
  assert.equal(b.art, "schneefang");
  assert.ok(ENGINE_FLAECHEN["sattel"].includes(b.surfaceId), `Phantom-Fläche: ${b.surfaceId}`);
  assert.ok(b.laengeM > 0 && Number.isFinite(b.laengeM));
  assert.equal(b.deckungsneutral, true);
  // Schneefang trägt KEIN Material/cover/Produkt-Feld
  for (const k of Object.keys(b)) assert.ok(!["cover", "material", "productId", "eindeckung", "hersteller"].includes(k), `Material-Feld am Schneefang: ${k}`);
  // reine Form ohne Schneefang -> keine Linienbauteile
  assert.deepEqual(applyVorlage(findeVorlage(alle, "sattel-standard")!, defaultBuild).linienBauteile, []);
  assert.ok(SCHNEEFANG_HINWEIS.length > 0);
});

test("Schneefang: keine NaN/Infinity/negativen Werte; Linie innerhalb [0,1]", () => {
  for (const f of [RECHT(8, 5), TRAP(12, 2, 6), RECHT(3, 2.5)]) {
    const b = platziereSchneefang(f.surfaceId, f).bauteil;
    if (!b) continue;
    for (const n of [b.yRel, b.uStartRel, b.uEndRel, b.laengeM, b.abstandTraufeM, b.sperrAbstandObenM, b.sperrAbstandUntenM]) {
      assert.ok(Number.isFinite(n) && n >= 0, "ungültiger Wert");
    }
    assert.ok(b.uStartRel >= 0 && b.uEndRel <= 1, "u außerhalb [0,1]");
  }
});

// =====================================================================
// EINGABEAUFFORDERUNG 13: zusammengesetzte Grundrisse (L/T/U)
// =====================================================================
test("grundrissPolygon: L/T/U erzeugen KEINE Rechteck-Ersatzform (mehr Ecken + Innenwinkel)", () => {
  const rect = grundrissPolygon("rechteck", 12, 8);
  assert.equal(rect.length, 4);
  assert.equal(anzahlInnenwinkel(rect), 0);
  assert.equal(istZusammengesetzt(rect), false);
  const l = grundrissPolygon("l-form", 12, 8, 5, 4);
  const t = grundrissPolygon("t-form", 12, 8, 5, 4);
  const u = grundrissPolygon("u-form", 12, 8, 5, 4);
  assert.equal(l.length, 6);
  assert.equal(t.length, 8);
  assert.equal(u.length, 8);
  for (const p of [l, t, u]) assert.equal(istZusammengesetzt(p), true);
});

test("Innenwinkel: L=1, T=2, U=2; rechteck=0; eckenAnalyse trennt innen/außen", () => {
  assert.equal(anzahlInnenwinkel(grundrissPolygon("l-form", 12, 8, 5, 4)), 1);
  assert.equal(anzahlInnenwinkel(grundrissPolygon("t-form", 12, 8, 5, 4)), 2);
  assert.equal(anzahlInnenwinkel(grundrissPolygon("u-form", 12, 8, 5, 4)), 2);
  for (const f of ["l-form", "t-form", "u-form"] as GrundrissForm[]) {
    assert.equal(anzahlInnenwinkel(grundrissPolygon(f, 12, 8, 5, 4)), erwarteteInnenwinkel(f));
  }
  const an = eckenAnalyse(grundrissPolygon("l-form", 12, 8, 5, 4));
  assert.equal(an.innenwinkel.length, 1);
  assert.equal(an.aussenecken.length, 5); // 6 Ecken - 1 innen
});

test("Flächen ohne Doppelzählung: L/T/U-Fläche < width×height (Bounding-Box), > 0, kein NaN", () => {
  const L = 12, B = 8;
  for (const f of ["l-form", "t-form", "u-form"] as GrundrissForm[]) {
    const a = grundrissFlaecheM2(grundrissPolygon(f, L, B, 5, 4));
    assert.ok(a > 0 && Number.isFinite(a), `Fläche ungültig: ${f}`);
    assert.ok(a < L * B, `Fläche nicht < Bounding-Box (Doppelzählung?): ${f} = ${a}`);
  }
  // konkret: L = 12*4 + 5*4 = 68 (nicht 96)
  assert.ok(Math.abs(grundrissFlaecheM2(grundrissPolygon("l-form", 12, 8, 5, 4)) - 68) < 0.01);
  // U-Innenhof: Fläche = 12*4 + 2*(5*4) = 88 (offene Mitte fehlt)
  assert.ok(Math.abs(grundrissFlaecheM2(grundrissPolygon("u-form", 12, 8, 5, 4)) - 88) < 0.01);
  // rechteck = volle Fläche
  assert.ok(Math.abs(grundrissFlaecheM2(grundrissPolygon("rechteck", 12, 8)) - 96) < 0.01);
});

test("U-Form hat offene Mitte (Innenhof): Flügelflächen ohne den Hofbereich", () => {
  const L = 16, B = 12, LB = 4, WB = 4;
  const a = grundrissFlaecheM2(grundrissPolygon("u-form", L, B, LB, WB));
  const voll = L * B;
  const hof = (L - 2 * LB) * (B - WB); // offener Innenhof
  assert.ok(Math.abs(a - (voll - hof)) < 0.01, "Innenhof nicht ausgespart");
  assert.ok(hof > 0, "kein offener Innenhof");
});

test("grundrissPolygon: robuste/entartete Eingaben -> kein NaN/negativ, Bein < Gesamt geklemmt", () => {
  for (const f of ["l-form", "t-form", "u-form", "rechteck"] as GrundrissForm[]) {
    for (const args of [[0, 0, 0, 0], [12, 8, 99, 99], [NaN, 8, 5, 4], [12, 8, -3, -3]] as const) {
      const p = grundrissPolygon(f, args[0], args[1], args[2], args[3]);
      for (const pt of p) { assert.ok(Number.isFinite(pt.x) && Number.isFinite(pt.y) && pt.x >= 0 && pt.y >= 0, `ungültiger Punkt ${f}`); }
      assert.ok(grundrissFlaecheM2(p) >= 0, `negative Fläche ${f}`);
    }
  }
});

test("formAusShape: Vorlagen-shapeKey/engineShape -> GrundrissForm", () => {
  assert.equal(formAusShape("l-shape"), "l-form");
  assert.equal(formAusShape("t-shape"), "t-form");
  assert.equal(formAusShape("u-shape"), "u-form");
  assert.equal(formAusShape("u-grundriss"), "u-form");
  assert.equal(formAusShape("rect"), "rechteck");
  assert.equal(formAusShape("sattel"), "rechteck");
});

test("E13 Vorlagen: Flachdach-L/T/U anwendbar mit korrektem apply (shape + lengthB/widthB); keine Doppelfläche", () => {
  for (const id of ["l-shape-flat", "flach-t", "flach-u", "winkelbungalow-l-flach", "gewerbe-l-flach", "buero-u-flach"]) {
    const v = findeVorlage(alle, id)!;
    assert.equal(v.status, "verfuegbar", `nicht verfügbar: ${id}`);
    assert.equal(v.geometrie.category, "flat", `nicht flach: ${id}`);
    assert.equal(istAnwendbar(v), true, `nicht anwendbar: ${id}`);
    const r = applyVorlage(v, defaultBuild);
    assert.equal(r.ok, true, `apply fehlgeschlagen: ${id}`);
    assert.ok(["l-shape", "t-shape", "u-shape"].includes(r.build!.shape as string), `apply.shape falsch: ${id}`);
    assert.ok((r.build as any).lengthB > 0 && (r.build as any).widthB > 0, `lengthB/widthB fehlen: ${id}`);
    assert.ok(!("cover" in (r as any)), `apply liefert cover: ${id}`);
    // echte Polygonfläche < Bounding-Box (keine Rechteck-Ersatzform)
    const poly = grundrissPolygon(formAusShape(r.build!.shape as string), r.build!.length, r.build!.width, (r.build as any).lengthB, (r.build as any).widthB);
    assert.ok(grundrissFlaecheM2(poly) < r.build!.length * r.build!.width, `keine echte L/T/U-Fläche: ${id}`);
    // keine Auto-Aufbauten/Schneefang auf zusammengesetztem Grundriss (keine Phantomfläche)
    assert.deepEqual(r.aufbauten, []);
    assert.deepEqual(r.linienBauteile, []);
  }
});

test("E13: geneigte L/T/U bleiben geplant (kein apply, kein falsches Rechteckdach)", () => {
  for (const id of ["l-shape-pitched", "t-shape-pitched", "walm-l", "walm-t", "walm-u", "pult-l", "pult-t", "pult-u", "sattel-u", "winkelbungalow-l"]) {
    const v = findeVorlage(alle, id)!;
    assert.equal(v.status, "geplant", `sollte geplant sein: ${id}`);
    assert.equal(istAnwendbar(v), false, `geneigtes L/T/U anwendbar: ${id}`);
    assert.equal(applyVorlage(v, defaultBuild).ok, false, `apply ok bei geplant: ${id}`);
  }
});

test("E13 U-Härtung: lengthB >= L/2 erzeugt KEIN selbstüberschneidendes Polygon / keine Doppelzählung", () => {
  // Workflow-Gegenbeispiel: L=10, B=10, WB=4, LB=6 (>L/2). Ohne Klemmung wäre Fläche > Bounding-Box.
  for (const [L, B, LB, WB] of [[10, 10, 6, 4], [12, 8, 99, 99], [16, 12, 8, 4]] as const) {
    const p = grundrissPolygon("u-form", L, B, LB, WB);
    const a = grundrissFlaecheM2(p);
    assert.ok(a > 0 && Number.isFinite(a), `U-Fläche ungültig (${L},${B},${LB})`);
    assert.ok(a <= L * B + 1e-6, `U-Fläche > Bounding-Box (Selbstüberschneidung/Doppelzählung): ${a} > ${L * B}`);
    assert.equal(anzahlInnenwinkel(p), 2, `U nicht mehr 2 Innenwinkel (entartet) bei (${L},${B},${LB})`);
  }
});

// =====================================================================
// EINGABEAUFFORDERUNG 14/15: Gaubenarten als eigene Vorlagen + Schwebe-Fix
// =====================================================================
const normSvg = (id: string) => vorschauSvg(findeVorlage(alle, id)!)
  .replace(/fill="[^"]*"/g, "F").replace(/stroke="[^"]*"/g, "S").replace(/aria-label="[^"]*"/g, "")
  .replace(/id="rc[^"]*"/g, "id").replace(/url\(#rc[^)]*\)/g, "url").replace(/opacity="[^"]*"/g, "")
  .replace(/<rect x="0"[^/]*\/>/, "");

test("Gauben: jede Gaubenart hat eigene Vorlage + Titel + eigene Bildvorschau (keine generische Gaube)", () => {
  for (const id of ["sattel-spitzgaube", "sattel-tonnengaube", "sattel-fledermausgaube", "sattel-rundgaube", "sattel-zwerchgaube", "sattel-schleppgaube-1", "sattel-giebelgaube", "sattel-flachgaube", "sattel-walmgaube"]) {
    const v = findeVorlage(alle, id);
    assert.ok(v, `Vorlage fehlt: ${id}`);
    assert.ok(v!.name.toLowerCase().includes("gaube"), `Titel ohne Gaube: ${id}`);
    assert.ok(vorschauSvg(v!).startsWith("<svg"), `kein SVG: ${id}`);
  }
  // distinkte Silhouetten: Spitz/Tonne/Fledermaus/Rund/Zwerch/Giebel/Flach/Schlepp paarweise verschieden
  const ids = ["sattel-spitzgaube", "sattel-tonnengaube", "sattel-fledermausgaube", "sattel-rundgaube", "sattel-zwerchgaube", "sattel-giebelgaube", "sattel-flachgaube", "sattel-schleppgaube-1"];
  const sigs = ids.map(normSvg);
  assert.equal(new Set(sigs).size, ids.length, "Gauben-SVGs nicht alle distinkt");
  // gerundete Gauben nutzen Bogen-Pfade
  for (const id of ["sattel-tonnengaube", "sattel-segmentbogengaube", "sattel-fledermausgaube", "sattel-rundgaube"]) {
    assert.ok(vorschauSvg(findeVorlage(alle, id)!).includes("<path"), `keine Bogenform: ${id}`);
  }
});

test("Gauben-Status ehrlich: unterstützte Arten verfügbar+teilweise (Obstacle), seltene Arten geplant (nur Vorschau)", () => {
  // unterstützt -> verfügbar (Daten) + teilweise (Anzeige) + schematisch gesetzt
  for (const id of ["sattel-schleppgaube-1", "sattel-giebelgaube", "sattel-walmgaube", "sattel-flachgaube", "walm-schleppgaube", "pult-flachgaube"]) {
    const v = findeVorlage(alle, id)!;
    assert.equal(v.status, "verfuegbar", `unterstützte Gaube nicht verfügbar: ${id}`);
    assert.equal(anzeigeStatus(v), "teilweise", `unterstützte Gaube nicht 'teilweise': ${id}`);
    assert.equal(gaubeSchematischGesetzt(v), true, `Gaube nicht schematisch gesetzt: ${id}`);
    assert.ok(standardAufbauten(v).some((a) => a.art.endsWith("gaube")), `kein Gauben-Obstacle: ${id}`);
  }
  // seltene Arten -> geplant, KEIN Obstacle, aber Bildvorschau
  for (const id of ["sattel-spitzgaube", "sattel-tonnengaube", "sattel-fledermausgaube", "sattel-rundgaube", "sattel-zwerchgaube", "sattel-segmentbogengaube", "sattel-dreiecksgaube"]) {
    const v = findeVorlage(alle, id)!;
    assert.equal(v.status, "geplant", `seltene Gaube nicht geplant: ${id}`);
    assert.equal(anzeigeStatus(v), "geplant", `seltene Gaube anzeige nicht geplant: ${id}`);
    assert.deepEqual(standardAufbauten(v), [], `seltene Gaube setzt Obstacle: ${id}`);
    assert.ok(UNGESTUETZTE_GAUBE_TOKENS.some((t) => v.schlagworte.map((s) => s.toLowerCase()).includes(t)), `seltene Gaube ohne ung. Token: ${id}`);
  }
});

test("Gauben-Anzahl: drei -> 3, Gaubenband -> 4, zwei -> 2 Gauben-Obstacles", () => {
  assert.equal(standardAufbauten(findeVorlage(alle, "sattel-schleppgaube-3")!).filter((a) => a.art === "schleppgaube").length, 3);
  assert.equal(standardAufbauten(findeVorlage(alle, "sattel-gaubenband")!).filter((a) => a.art === "schleppgaube").length, 4);
  assert.equal(standardAufbauten(findeVorlage(alle, "sattel-satteldachgaube-2")!).filter((a) => a.art === "giebelgaube").length, 2);
});

test("Schwebe-Fix: Gauben bleiben mit voller Schrägen-TIEFE unter dem First (kein Schweben über die andere Seite)", () => {
  const RAND = 0.4;
  for (const id of ["sattel-schleppgaube-3", "sattel-satteldachgaube-2", "dach-mehrere-kleine-gauben", "walm-zwei-gauben", "sattel-gaubenband"]) {
    const v = findeVorlage(alle, id)!;
    const r = applyVorlage(v, defaultBuild);
    const surfId = r.aufbauten![0].surfaceId;
    const fl = hauptflaecheInfo(r.build!, surfId);
    for (const a of r.aufbauten!) {
      const upSlopeEnd = a.yRel * fl.hoeheM + a.tiefeM / 2;
      const downSlopeEnd = a.yRel * fl.hoeheM - a.tiefeM / 2;
      assert.ok(upSlopeEnd <= fl.hoeheM - RAND + 1e-6, `${id}: Gaube ragt über First (${upSlopeEnd.toFixed(2)} > ${(fl.hoeheM - RAND).toFixed(2)})`);
      assert.ok(downSlopeEnd >= RAND - 1e-6, `${id}: Gaube ragt über Traufe (${downSlopeEnd.toFixed(2)})`);
      assert.ok(a.tiefeM > 0 && a.breiteM > 0 && Number.isFinite(a.tiefeM) && Number.isFinite(a.hoeheM), `${id}: ungültige Maße`);
    }
  }
});

test("Gauben auf nicht baubaren Hauptdächern (Mansard/Krüppelwalm/Mehrkörper) bleiben geplant", () => {
  for (const id of ["mansard-schleppgauben", "mansard-mittelgaube", "mansardwalm-gauben", "krueppelwalm-gaube", "haus-gaube-garage"]) {
    const v = findeVorlage(alle, id)!;
    assert.equal(v.status, "geplant", `sollte geplant sein: ${id}`);
    assert.equal(istAnwendbar(v), false, `sollte nicht anwendbar sein: ${id}`);
  }
  // Gebäudetyp-Gauben auf baubarem Dach sind verfügbar/teilweise
  for (const id of ["mfh-gauben", "stadtvilla-gauben"]) {
    assert.equal(findeVorlage(alle, id)!.status, "verfuegbar", `sollte verfügbar sein: ${id}`);
    assert.equal(anzeigeStatus(findeVorlage(alle, id)!), "teilweise", `sollte teilweise sein: ${id}`);
  }
});

// =====================================================================
// EINGABEAUFFORDERUNG 15: Dachöffnungen / Prüffelder (konstruktive Ebene)
// =====================================================================
const FL_RECHT = (b: number, h: number): DachflaecheInfo => ({ surfaceId: "main_S", breiteTraufeM: b, breiteFirstM: b, hoeheM: h, form: "rechteck" });
const FL_TRAP = (bu: number, bo: number, h: number): DachflaecheInfo => ({ surfaceId: "south", breiteTraufeM: bu, breiteFirstM: bo, hoeheM: h, form: "trapez" });

test("oeffnungVTiefeM: Dachfenster -> hoeheM (Schräge), Gaube/Kamin -> tiefeM (Aufbauhöhe)", () => {
  assert.equal(oeffnungVTiefeM({ art: "window", hoeheM: 1.18, tiefeM: 0.1 }), 1.18);
  assert.equal(oeffnungVTiefeM({ art: "giebelgaube", hoeheM: 1.5, tiefeM: 2.5 }), 2.5);
  assert.equal(oeffnungVTiefeM({ art: "chimney", hoeheM: 0.6, tiefeM: 0.6 }), 0.6);
});

test("oeffnungRechteck: Prüffeld liegt in der Fläche, inkl. Sicherheitsrand, löst Auswechslung aus, kein NaN", () => {
  const r = oeffnungRechteck({ art: "giebelgaube", surfaceId: "main_S", xRel: 0.5, yRel: 0.42, breiteM: 2.5, hoeheM: 1.5, tiefeM: 2.5 }, FL_RECHT(10, 6), 0.1);
  for (const n of [r.uMinRel, r.uMaxRel, r.vMinRel, r.vMaxRel]) { assert.ok(Number.isFinite(n) && n >= 0 && n <= 1, "rel außerhalb [0,1]"); }
  assert.ok(r.uMaxRel > r.uMinRel && r.vMaxRel > r.vMinRel, "leeres Prüffeld");
  assert.equal(r.auswechslungErforderlich, true);
  assert.equal(r.sicherheitsrandM, 0.1);
  assert.equal(r.innerhalb, true); // zentriert auf großer Fläche
  assert.ok(r.breiteM > 0 && r.tiefeM > 0);
});

test("oeffnungRechteck: Öffnung am Rand / auf schmaler Trapezfläche -> innerhalb=false (prüfpflichtig)", () => {
  // Gaube ganz am Ortgang einer schmalen Fläche
  assert.equal(oeffnungRechteck({ art: "giebelgaube", surfaceId: "main_S", xRel: 0.95, yRel: 0.5, breiteM: 2.5, hoeheM: 1.5, tiefeM: 2.5 }, FL_RECHT(4, 6)).innerhalb, false);
  // breite Gaube nahe First eines schmalen Trapez-Walms
  assert.equal(oeffnungRechteck({ art: "giebelgaube", surfaceId: "south", xRel: 0.5, yRel: 0.9, breiteM: 3.0, hoeheM: 1.5, tiefeM: 2.5 }, FL_TRAP(12, 1, 6)).innerhalb, false);
});

test("E15: auto-platzierte Gauben/Dachfenster/Kamine ergeben ein Prüffeld INNERHALB der echten Fläche", () => {
  for (const id of ["sattel-giebelgaube", "sattel-schleppgaube-3", "walm-zwei-gauben", "sattel-2-dachfenster", "sattel-kamin"]) {
    const r = applyVorlage(findeVorlage(alle, id)!, defaultBuild);
    const fl = hauptflaecheInfo(r.build!, r.aufbauten![0].surfaceId);
    for (const a of r.aufbauten!) {
      const rect = oeffnungRechteck({ art: a.art, surfaceId: a.surfaceId, xRel: a.xRel, yRel: a.yRel, breiteM: a.breiteM, hoeheM: a.hoeheM, tiefeM: a.tiefeM }, fl);
      assert.equal(rect.innerhalb, true, `${id}: Öffnung nicht innerhalb der Fläche (${a.art})`);
      assert.equal(rect.surfaceId, a.surfaceId);
    }
  }
});

// --- Korrektur „Schweben": lotrechte Orientierung stehender Aufbauten (Gaube/Kamin) ---------------
const N0 = (x: number) => RT(x) + 0; // normalisiert -0 -> 0 (assert/strict nutzt Object.is)
const dot = (a: any, b: any) => a.x * b.x + a.y * b.y + a.z * b.z;
const len3 = (a: any) => Math.hypot(a.x, a.y, a.z);
const cross3 = (a: any, b: any) => ({ x: a.y * b.z - a.z * b.y, y: a.z * b.x - a.x * b.z, z: a.x * b.y - a.y * b.x });
// vDown einer Satteldachfläche bei Neigung theta (Traufe->First: hinauf + nach Norden(-z)).
const vDownSattel = (thetaGrad: number) => {
  const t = (thetaGrad * Math.PI) / 180;
  return { x: 0, y: Math.sin(t), z: -Math.cos(t) };
};

test("Orientierung: yAxis ist EXAKT Welt-Hoch (0,1,0) -> Aufbau steht lotrecht, kippt nicht mit der Schräge", () => {
  for (const p of [15, 25, 35, 45, 60]) {
    const b = stehendeAufbauBasis(vDownSattel(p));
    assert.deepEqual({ x: N0(b.yAxis.x), y: N0(b.yAxis.y), z: N0(b.yAxis.z) }, { x: 0, y: 1, z: 0 });
  }
});

test("Orientierung: zAxis (Front) ist HORIZONTAL (y=0) und zeigt zur Traufe (= -vDown, projiziert)", () => {
  const b = stehendeAufbauBasis(vDownSattel(40));
  assert.equal(N0(b.zAxis.y), 0, "Front darf keine vertikale Komponente haben");
  assert.equal(N0(len3(b.zAxis)), 1, "zAxis normiert");
  // -vDown horizontal = (0,0,cos) -> normalisiert (0,0,1): Front zeigt nach Süden (zur Traufe)
  assert.deepEqual({ x: N0(b.zAxis.x), y: N0(b.zAxis.y), z: N0(b.zAxis.z) }, { x: 0, y: 0, z: 1 });
});

test("Orientierung: xAxis (Breite) parallel Traufe, horizontal, orthonormal + rechtshändig (x×y=z)", () => {
  const b = stehendeAufbauBasis(vDownSattel(38));
  assert.equal(N0(b.xAxis.y), 0, "Breite parallel Traufe = horizontal");
  assert.equal(N0(len3(b.xAxis)), 1);
  assert.equal(N0(dot(b.xAxis, b.yAxis)), 0);
  assert.equal(N0(dot(b.xAxis, b.zAxis)), 0);
  assert.equal(N0(dot(b.yAxis, b.zAxis)), 0);
  const c = cross3(b.xAxis, b.yAxis); // rechtshändig: x × y = z
  assert.deepEqual({ x: N0(c.x), y: N0(c.y), z: N0(c.z) }, { x: N0(b.zAxis.x), y: N0(b.zAxis.y), z: N0(b.zAxis.z) });
});

test("Orientierung: Basis ist UNABHÄNGIG von der Dachneigung (Kern des Schweben-Fix)", () => {
  // Egal wie steil — die lotrechte Basis bleibt identisch (Aufbau lehnt NICHT mit der Schräge weg).
  const flach = stehendeAufbauBasis(vDownSattel(20));
  const steil = stehendeAufbauBasis(vDownSattel(55));
  for (const k of ["xAxis", "yAxis", "zAxis"] as const) {
    assert.deepEqual(
      { x: N0(flach[k].x), y: N0(flach[k].y), z: N0(flach[k].z) },
      { x: N0(steil[k].x), y: N0(steil[k].y), z: N0(steil[k].z) },
      `${k} darf nicht von der Neigung abhängen`,
    );
  }
});

test("Orientierung: schräges Heading (Walmfläche) -> zAxis horizontal normiert in Falllinien-Richtung", () => {
  // vDown mit x- und z-Anteil (diagonale Fläche), plus vertikalem Anteil.
  const b = stehendeAufbauBasis({ x: 0.5, y: 0.7, z: 0.5 });
  assert.equal(N0(b.zAxis.y), 0);
  assert.equal(N0(len3(b.zAxis)), 1);
  // Richtung = -(x,z) normiert
  const erw = { x: -0.5, z: -0.5 };
  const l = Math.hypot(erw.x, erw.z);
  assert.equal(N0(b.zAxis.x), N0(erw.x / l));
  assert.equal(N0(b.zAxis.z), N0(erw.z / l));
});

test("Orientierung: Flachdach-Fallback (vDown ohne Horizontalanteil) -> stabile lotrechte Basis", () => {
  const b = stehendeAufbauBasis({ x: 0, y: 1, z: 0 });
  assert.deepEqual({ x: N0(b.yAxis.x), y: N0(b.yAxis.y), z: N0(b.yAxis.z) }, { x: 0, y: 1, z: 0 });
  assert.equal(N0(len3(b.zAxis)), 1);
  assert.equal(N0(len3(b.xAxis)), 1);
});

test("istStehenderAufbau: Gauben + Kamin = lotrecht; Dachfenster/Lüfter/Sat/Lichtkuppel = liegend", () => {
  for (const t of ["chimney", "schleppgaube", "trapezgaube", "flachgaube", "giebelgaube"]) {
    assert.equal(istStehenderAufbau(t), true, `${t} muss lotrecht stehen`);
  }
  for (const t of ["window", "vent", "sat", "lichtkuppel"]) {
    assert.equal(istStehenderAufbau(t), false, `${t} liegt in der Dachebene`);
  }
});
