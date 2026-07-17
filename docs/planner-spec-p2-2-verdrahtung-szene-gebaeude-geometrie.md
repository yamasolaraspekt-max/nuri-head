# PLANNER-SPEC — P2-2: Verdrahtung Szene-Projektion → gebaeude_geometrie (ticket)

**Rolle:** Planner (kein Code) · **Datum:** 2026-07-17 · **Heimat-App:** ticket (LIVE) · **Bau-Gate:** ERST
nach P2-1b-Evaluator-grün UND Yama-Go — dies ist der erste Schritt der Projektions-Linie, der SCHREIBT.

## 0 · Ist-Beleg (grep, Code = Wahrheit)
- Der belastbare Schreibpfad existiert bereits: `GrundrissController::schreibeGeometrieVersion(LeadAlternativeAdd
  $objekt, array $geometrie)` (G0c-2) — sucht das AKTIVE `Anforderungsprofil` am Objekt (verankerbar
  LeadAlternativeAdd), erzeugt via `AnforderungsprofilService::neueVersion($aktiv, …)` bzw. `anlegen(...)`
  eine NEUE Version, setzt `->gebaeude_geometrie = $geometrie`, speichert, `->aktivieren(...)`. Append-only,
  eine aktive Version je Verankerung; kein destruktives Überschreiben, keine `raum_geometrien`-Persistenz.
- `anforderungsprofile.gebaeude_geometrie` (JSON, cast array) = HeizlastRechner-Input; der
  `AnforderungsprofilHeizlastAdapter` liest `gebaeude_geometrie.raeume`.
- Format-Kette bewiesen (P2-1a-Round-Trip): SzeneProjektionService liefert raeume im RaumGeometrie-INPUT
  (polygon, wand_segmente); `GeometrieAbleitungService::ausGeometrie` macht daraus den bauteile-Output,
  den gebaeude_geometrie erwartet.

## 1 · Ziel & Entscheidung
Ein Aktion, die die Hausplaner-Szene eines Objekts in eine NEUE, aktive `gebaeude_geometrie`-Version
projiziert — damit fließt der im Hausplaner gezeichnete Grundriss in die belastbare Heizlast-/PV-Kette.
Kein neuer Schreibmechanismus: **wiederverwendet den bestehenden `schreibeGeometrieVersion`-Pfad**
(AnforderungsprofilService), nur die Quelle ist die Szene statt des Ein-Raum-Grundrisses.

## 2 · Pipeline (rein additiv, ein neuer Baustein)
Neue Action `App\Domain\Hausplaner\Actions\UebernehmeSzeneInAuslegung` (oder Service-Methode):
1. Szene des Objekts laden (`HausplanerDocument.scene_json` an alternative_id).
2. `SzeneProjektionService::projiziere($scene)` → raeume[] (RaumGeometrie-Input, Polygone schon
   TopologieGate-geprüft; wirft GeometrieUngueltigException bei ungültiger Geometrie).
3. Je Raum: `GeometrieAbleitungService::ausGeometrie(RaumGeometrie)` → bauteile-Format.
4. `gebaeude_geometrie = ['raeume' => [ …ausGeometrie je Raum… ]]`.
5. Schreiben via den BESTEHENDEN Versionspfad (schreibeGeometrieVersion-Muster): aktives Profil am
   Objekt → `neueVersion`/`anlegen` → `->gebaeude_geometrie=` → `save` → `aktivieren`. Append-only.

## 3 · Auslöser (die eigentliche „Verdrahtung" — Yama-Entscheidung)
**Empfehlung: EXPLIZITE Nutzer-Aktion** „Geometrie in Auslegung übernehmen" (Button in der Insel /
Objektakte), NICHT automatisch bei jedem Szenen-Speichern. Grund: automatische Projektion bei jedem
Save erzeugt Versions-Wildwuchs und ändert das Speicher-Verhalten still; eine explizite Übernahme ist
nachvollziehbar (eine neue Profil-Version je Klick) und additiv. (Alternative Auto-Übernahme bewusst
NICHT empfohlen.)

## 4 · Idempotenz / Herkunft
Vor dem Schreiben Quell-Hash der Szene bestimmen (analog `SourceGeometryRef::sourceHash`/`CanonicalHash`);
ist die aktive Version bereits aus derselben Szene abgeleitet (gleicher Hash), KEINE neue Version anlegen
(kein Leerlauf-Versionsmüll). Der Hash wandert als Herkunft in die neue Version (Feld/Meta, additiv).

## 5 · Kantenliste
- Szene ohne geschlossene Räume → 0 raeume → KEINE Übernahme (Nutzerhinweis „kein Raum erkannt"),
  keine leere Version.
- Ungültige Geometrie → GeometrieUngueltigException aus dem Gate → Übernahme abgebrochen, nichts
  geschrieben.
- Bestehende, NICHT aus der Szene stammende gebaeude_geometrie (z. B. via GrundrissController gepflegt):
  Übernahme legt eine neue Version an (löst die alte ab, append-only) — die alte bleibt als Historie.
  Konflikt-Sichtbarkeit für den Nutzer (Warnung „ersetzt manuell gepflegte Geometrie") empfohlen.
- decke/boden aus der Szene sind heute null → die abgeleiteten Räume tragen keine Decke/Boden-Bauteile
  (ehrlich; kein erfundener bauteil_typ).

## 6 · Abnahmekriterien (Evaluator, Gegen-Beweis)
- Übernahme eines Zwei-Raum-Szenarios → neue aktive Anforderungsprofil-Version am Objekt mit
  `gebaeude_geometrie.raeume` = 2 Räume; alte Version status=abgeloest (append-only, Historie erhalten).
- `AnforderungsprofilHeizlastAdapter` liest die neue Geometrie und der HeizlastRechner rechnet daraus
  (End-to-End: Szene → Heizlast sichtbar).
- Zweite Übernahme derselben (unveränderten) Szene → KEINE neue Version (Idempotenz über Quell-Hash).
- Ungültige/leere Szene → nichts geschrieben, keine Version.
- Nur additive Dateien + der eine dokumentierte Auslöser; `GrundrissController`/`schreibeGeometrieVersion`
  unverändert wiederverwendet (kein zweiter Schreibpfad). Volle Suite grün.

## 7 · Governance / Stopp
Erster SCHREIBENDER Schritt der Linie → **Yama-Go Pflicht**, plus ein von Yama benannter Referenzfall
(ein echtes Objekt, an dem die Übernahme einmal kontrolliert geprüft wird), bevor die Aktion produktiv
sichtbar wird. Keine Migration nötig (gebaeude_geometrie-Spalte + Versionierung existieren). Rollentrennung:
diese Spec = Planner; Bau = Generator (grep-first: schreibeGeometrieVersion/AnforderungsprofilService real
prüfen); Abnahme = Evaluator; Referenzfall-Freigabe = Yama.

## 8 · Nachbesserung nach Evaluator-Spec-Review (2026-07-17)

Der Evaluator hat die Spec (vor dem Bau) gegen den echten Code gemessen: S1/S4/S6 tragen. Zwei echte
Löcher (S2 rot, S5 gelb) + S3-Präzisierung werden hier verbindlich geschlossen. **Diese Festlegungen
haben Vorrang vor §4/§6 oben, wo sie abweichen.**

**S2 — U-Werte / Operanden-Gate (der Blocker).** Die projizierte Geometrie liefert Flächen, aber keine
belegten U-Werte: `GeometrieAbleitungService::opakeUQuelle` gibt ohne `u_wert`/`konstruktion_id` nur
`u_strategie='C'` zurück, `HeizlastRechner` rechnet `u_wert ?? 0` → Transmission der Hülle = 0. **Dasselbe
Loch hat die bestehende Grundriss-Linie** (gleicher `ausGeometrie`-Pfad) — es ist die Geometrie→Heizlast-
Naht, nicht ein Fehler der Szene-Übernahme.
- **Entscheidung:** Die Übernahme ist eine **Geometrie-Übernahme**. Bauteile tragen `u_strategie='C'` OHNE
  erfundenen `u_wert` (kein stiller Ersatzwert). Die Herkunft markiert `_herkunft.u_werte='unbelegt'` +
  Klartext-Hinweis, dass U-Werte/Konstruktionen ein **nachgelagerter Pflichtschritt** vor belastbarer
  Heizlast sind.
- **§6-Kriterium entschärft:** NICHT „End-to-End Heizlast sichtbar/belastbar", sondern „Pipeline läuft
  ohne Formatbruch (Szene→Projektion→`gebaeude_geometrie`→Adapter/HeizlastRechner) UND fehlende U-Werte
  sind sichtbar unbelegt (`u_strategie='C'` + `_herkunft.u_werte='unbelegt'`), kein stiller Belegt-Wert."
- **Eigener Posten (NICHT in dieser Action, Heizlast-Heimat):** die `u_strategie='C'`-Auflösung bzw. ein
  Operanden-Gate im HeizlastRechner/Adapter, das „C ohne u_wert" als Lücke ausweist statt still 0 zu
  rechnen. Betrifft auch die Grundriss-Linie → gehört als eigener Vorgang in die Heizlast-Heimat.

**S3 — Herkunft/Hash-Key festgenagelt.** Herkunft steht unter dem reservierten Key
`gebaeude_geometrie._herkunft = { quelle:'hausplaner_szene', source_hash:<CanonicalHash der Szene>,
u_werte:'unbelegt', hinweis:<klartext> }`. Idempotenz vergleicht `_herkunft.source_hash` (nicht die
abgeleitete Geometrie, nicht ein Top-Level-Feld im `raeume`-Namensraum). Keine Migration (JSON-Spalte).

**S5 — Kanten ergänzt.**
- **Rechte-Gate:** die schreibende Übernahme braucht ein `permission`-Gate — gehört an den **P2-2b-Auslöser**
  (analog Hausplaner), NICHT in die unverdrahtete Action P2-2a.
- **Keine Szene** (Objekt ohne `HausplanerDocument`): eigener Status `keine_szene`, nichts geschrieben.
- **Profil-Auswahl:** genau ein aktives Profil je Verankerung ist durch `aktivieren()`-Scope garantiert →
  `aktiv()->first()` ist eindeutig; kein aktives → `anlegen()`.
- **Staleness:** weicht `_herkunft.source_hash` vom aktuellen Szenen-Hash ab, zeigt die UI/P2-2b „Szene
  geändert seit letzter Übernahme" (der Hash ist der Enabler; Anzeige = P2-2b).

**Bau-Status:** P2-2a (Action + Referenzfall-Test) ist mit dieser Nachbesserung umgesetzt (additiv,
UNVERDRAHTET, kein Produktiv-Caller). Abnahme durch unabhängigen Evaluator; danach P2-2b (Auslöser +
Rechte-Gate + Staleness-Anzeige) nach Yama-Go + Referenzfall.
