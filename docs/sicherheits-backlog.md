# Sicherheits-Backlog (eskalierte Bestandslücken)

> Eigene, additiv gepflegte Liste. Einträge sind **Bestandsbefunde**, die bewusst **nicht** im
> auslösenden Feature-Ticket gefixt werden (Scope-Disziplin), sondern als eigener Strang.
> Priorität terminiert Yama.

## S-1 · `deal_measurement_items`-Schreibfläche ohne Ownership — **HOCH**

**Befund (M4-a v-c-2, 2026-07-05):** Die gesamte Deal-Measurement-Schreibfläche ist nur durch
`auth` geschützt — **kein Ownership-/Policy-Check**. Jeder authentifizierte Nutzer kann in **jedes
fremde Aufmaß** schreiben.

- `DealMeasurementMaterialController@saveMaterials` (`:15`): Guard = `auth` (Route in
  `Route::middleware(['auth'])`, `routes/web.php:4162`) + `status==='completed'`→423. Kein Ownership.
- `DealMeasurementController@256`/`@551` (echter `deal_measurement_items`-Schreiber): kein
  `__construct`/`middleware`/`authorize`/`Gate`/`Policy` — nur `auth()->user()` für Audit-Spalten.
- Route-Model-Binding (`{measurement}`) akzeptiert jede ID.

**Neu eingeführt (gleiche Bestandslage, bewusst NICHT abweichend):**
`HeizkoerperController@uebernehmen` (`heizkoerper.stueckliste.uebernehmen`) folgt exakt dem
Bestandsmuster (Flag+`auth` + `completed`→423), damit v-c-2 keine Sonderrolle spielt — **erbt
denselben Ownership-Gap**.

**Fix (eigener Strang):** modulweit eine Policy nach **`PlanUploadPolicy`-Muster** (bzw. bestehende
Ownership-/Zuständigkeits-Logik) an **allen drei** Stellen: `saveMaterials` +
`DealMeasurementController`-Schreibpfade + `heizkoerper.stueckliste.uebernehmen`. Prüfen: darf der
Nutzer den zugehörigen Deal bearbeiten (Zuweisung/Rolle/Filiale)?

**Priorität: HOCH** (Yama terminiert). Verwandt: die WP-/Heizlast-Auslegungstabellen
(`heizlast_projekte` u. a.) sind bei ihrer Produktiv-Anbindung auf dieselbe Ownership-Frage zu prüfen.
