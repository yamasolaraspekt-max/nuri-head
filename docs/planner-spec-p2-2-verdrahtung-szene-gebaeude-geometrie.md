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
