# INVENTUR Z1, zweite Stufe — Konsistenz-Linse, Lauf vom 21.08.2026

> Fach: [Backlog](REGISTER.md) · Verfahren: [INVENTUR-VERFAHREN.md](../regelwerk/INVENTUR-VERFAHREN.md)
> **Lauf:** 1 Finder (konsistenz-finder, sonnet), Zone `resources/planner/hausplaner/`
> (geometry/domain/projection/app/rahmen). Schließt die Linsen-Übergabe aus dem ersten Lauf.
> Vorbestand gelesen: inventur-2026-08-20-z1.md, fahrplan-2026-08-20.md (K-1..R-2 ausgeschlossen).

## Ü-1 · Konsistenz (Übergabe aus Lauf 1, BESTÄTIGT) · zwei Prüfpfade, zwei stumme Gegenurteile

**beleg:** `geometry/configuratorPackage.ts:120-122` `kannIntegrieren` → nur `'approved'`;
`geometry/integrationAbgleich.ts:45-46` `statusKonflikt` lässt `'approved' || 'integrated'` durch;
`pruefeOeffnungsIntegration` (`:74-117`) nutzt NUR `statusKonflikt`, `pruefePaketIntegration`
(`:125-135`) zusätzlich `kannIntegrieren`. **Empirisch** (node, Paket `status:'integrated'`):
`pruefeOeffnungsIntegration → integrierbar: true, konflikte: []` ·
`pruefePaketIntegration → integrierbar: false, konflikte: []`. Reichweite: **0 Produktiv-Aufrufer**
von `integrationAbgleich.ts`; `kannIntegrieren` produktiv ungenutzt.
**erklaerung:** Dateikopf verspricht „Konflikte MIT Handlungsoptionen (nie stillschweigend)" —
für `'integrated'` urteilen beide Pfade gegensätzlich UND stumm (0 Konflikteinträge). Heute
folgenlos, der erste Anschluss erbt den Widerspruch unbemerkt.
**erledigt_wenn:** eine Antwort auf „ist `integrated` integrierbar" — `statusKonflikt` bekommt
einen erklärenden Fall für `'integrated'` und `pruefeOeffnungsIntegration` zieht `kannIntegrieren`
heran; ODER Yama entscheidet fachlich, dass Re-Integration ein gültiger dritter Fall ist.
**⚠ Y-Kandidat:** ist „erneut integrieren" (Re-Import nach Änderung) fachlich gewollt? **aufwand:** S

## K-5 · Konsistenz · `polygonFlaecheM2`: Meter-Vertrag von einem Verbraucher widersprochen

**beleg:** `geometry/polygonFlaeche.ts:11-13` Kopf: Meter-Eingabe/m²; `dachGeometrie.ts:89` hält
(mm→m vor dem Aufruf); `renderers/three-d/deckenMesh.ts:14` Kommentar behauptet das Gegenteil
(„rechnet KEINE Einheit um (Input mm ⇒ mm²)"), `:19/:21` ruft ohne Umrechnung, `:23` teilt durch
`MM2_PRO_M2`. Reichweite: `deckenNettoFlaecheM2` 0 Produktiv-Aufrufer (dormant, wie R-2).
Vorbestand: vom Plan-Prüfer in §128 gemessen („drei Aussagen über dieselbe Einheit"), aber nie
ins Backlog überführt — **hiermit überführt.**
**erklaerung:** Der Rumpf prüft keine Einheit; der Kopf ist eine Bitte, keine Garantie. Wird
`deckenNettoFlaecheM2` verdrahtet, entscheidet Zufall zwischen richtig und Faktor 10⁶ — dieselbe
Falle wie W-08/1 und R-1, an dritter Stelle.
**erledigt_wenn:** `deckenMesh.ts` konvertiert vor dem Aufruf nach Meter (Muster
`dachGeometrie.ts:89`), `MM2_PRO_M2` + widersprechender Kommentar entfallen; Test an bekanntem
mm-Rechteck. ODER Kopf von `polygonFlaeche.ts` auf „einheitenlos, Aufrufer verantwortlich"
heruntergestuft und alle Aufrufer geprüft. **aufwand:** S

## K-6 · Konsistenz · snake_case vs. kebab-case für denselben Fachbegriff

**beleg:** Zählung in domain/geometry/projection: kebab `175` Werte/16 Dateien vs. snake `33`/5 —
Bindestrich ist klare Mehrheit. Kollision: `domain/scene.types.ts:208` `'underfloor_heating'` vs.
`geometry/configuratorPackage.ts:17` `'underfloor-heating'`; `:180-181` `'heat_pump_indoor/outdoor'`
vs. `'heat-pump'`. `scene.types.ts` führt beide Schreibweisen zugleich (`'dreh-kipp'` + `'underfloor_heating'`).
**erklaerung:** `configuratorPackage.ts:1-11` verspricht verlustfreie Übernahme ins Gesamtprojekt
(= SceneNodes); ein naiver String-Vergleich Fußbodenheizung/Wärmepumpe schlägt dann lautlos fehl.
Heute ungekoppelt (latent). **erledigt_wenn:** Konvention benannt (Bindestrich) und
`scene.types.ts` folgt ODER getestete Übersetzungstabelle bei Bau der Übernahme. **aufwand:** S

## Negatives Ergebnis, ausdrücklich (korrigiert eine Prämisse)
`insulationThickness` ist NICHT doppelt deklariert im Sinn der Prämisse aus §147-Anmerkung 2:
`scene.types.ts:110` (WallNode.construction) und `:238` (RouteNode) tragen **beide `// mm`**,
unverändert seit `00bfed2b`; zwei fachlich getrennte Node-Typen, durch TypeScript strukturell
getrennt. Kein Befund — die Prämisse war überholt oder falsch gelesen.
