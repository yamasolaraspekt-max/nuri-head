# G0c-1 — Geometrie-Migrationsplan (read-only, keine Migration, kein App-Code)

**Stand:** 2026-07-14 · **read-only Analyse/Plan** (Startblock G0c v1.0 §2). **Keine Migrationsdatei, keine Model-/Controller-Änderung, kein Seeder.** · **Git-Ausgangsstand:** `7a2b829`.
**Ziel:** die **führende** Geometrie zieht vom HeizlastProjekt-Stand-alone-Tool an das **versionierte, objektgebundene** Zuhause (`anforderungsprofile.gebaeude_geometrie`) — AP-4-Befund. Danach: **eine** Geometriewahrheit, am Objekt verankert, versioniert, nicht destruktiv.
**Abschluss G0c-1 = PFLICHT-STOPP:** Plan an Yama; G0c-2 (Ausführung) erst nach ausdrücklicher Freigabe inkl. der markierten Entscheidungspunkte.

---

## 1. Quellen-Inventar (belegt)

| Speicher | Rolle heute | Bindung | Destruktiv? |
|---|---|---|---|
| `raum_geometrien` (`polygon`, `wand_segmente`, `hoehe_mm`, `decke`, `boden`, `geschoss`; 1:1 an `heizlast_raum`) | **Quell-Geometrie** (gezeichneter Grundriss) | `heizlast_raum_id` → `heizlast_raeume` → `heizlast_projekte` (**Tool**, nicht Objekt) | **ja** — `RaumGeometrie::updateOrCreate` überschreibt in-place, **keine Version** |
| `heizlast_bauteile` (typ/grenzflaeche/azimut/flaeche_m2/u_*) | **abgeleitete** Bauteile (aus Geometrie) | an `heizlast_raum` | **ja** — `GeometrieAbleitungService::schreibeInProjekt` Z. 96 `bauteile()->delete()` + Neuanlage |

**Schreiber (aus G0b-Schreibpfad-Inventar, verifiziert):**
- `GrundrissController::speichern` → `RaumGeometrie::updateOrCreate` (Geometrie) + `schreibeInProjekt` (Bauteile).
- `GeometrieAbleitungService::schreibeInProjekt` → destruktive Bauteil-Neuanlage.
- `AnforderungsprofilHeizlastAdapter` / `AnforderungsprofilService` / `Anforderungsprofil` → lesen/schreiben `gebaeude_geometrie` (versioniert).

**Zeilenzahlen (Dev-DB `ticket`, read-only):** `heizlast_projekte` 0 · `heizlast_raeume` 0 · `heizlast_bauteile` 0 · `raum_geometrien` 0 · `anforderungsprofile` 0 · davon mit `gebaeude_geometrie` 0. → **Lokal keine Bestandsgeometrie** (Realbestand nur Hetzner, off-limits → Deploy-Tag, §7). Backfill lokal trivial (0 Zeilen).

## 2. Ziel-Modell (existierende Struktur dokumentiert, nicht neu erfunden)

- **`anforderungsprofile.gebaeude_geometrie`** (JSON, nullable, **existiert bereits**): Rechen-Input des `HeizlastRechner` = `raeume[]` + je Raum `bauteile[]` (mit `u_wert`, `u_wert_datenlage`, `quelle`). Gelesen von `AnforderungsprofilHeizlastAdapter`.
- **Versionierung:** über die `anforderungsprofile`-Zeile selbst (`version`, `status` entwurf|aktiv|abgeloest, `abgeloest_durch_id`) — append-only via `AnforderungsprofilService::neueVersion/aktivieren`. Genau eine `aktiv` je Verankerung.
- **Objektbindung:** polymorph `verankerbar` = `LeadAlternativeAdd` (Objekt, kanonisch) oder `LeadProductList` (Gewerk). → **Geometrie ist damit objekt-verankert + versioniert**, ohne neue Tabelle.

**→ Der Ziel-Hook existiert bereits.** Der Umzug ist primär (a) **Schreiber-Umleitung** (Geometrie als neue Profil-Version statt destruktivem `raum_geometrien`-Überschreiben), (b) **Deprecation** des HeizlastProjekt-Pfades (read-only), (c) ein **Format-Transform** (Quell-Shape → Ziel-Shape, §3).

### Markierte Entscheidungspunkte für Yama (nicht selbst entschieden)
- **D1 — Schemabedarf:** Reicht der bestehende `gebaeude_geometrie`-JSON-Hook (dann **keine Migration**, nur Verhalten + Transform), oder soll eine **additive** Spalte (z. B. `raum_geometrien.anforderungsprofil_id` als Verweis / `geometry_hash`) ergänzt werden? *(Falls additive Migration gewünscht → G0c-2 mit Pflicht-Stopp + additivem Vorschlag.)*
- **D2 — Quell-Shape ↔ Ziel-Shape:** `raum_geometrien` (polygon+wand_segmente) ist die *editierbare* Geometrie; `gebaeude_geometrie` (raeume[]+bauteile[]) ist der *Rechen-Input*. Bleibt `raum_geometrien` als editierbarer Layer (read-only nach Umzug) erhalten, während `gebaeude_geometrie` die führende versionierte Wahrheit wird? Oder wandert auch die editierbare Roh-Geometrie ins Profil-JSON?
- **D3 — `heizlast_bauteile`:** bleibt als abgeleiteter Cache (re-derivierbar via `ausGeometrie`) oder wird ebenfalls stillgelegt?
- **D4 — Mehrraum/Geschoss:** heute `GrundrissController` = 1 Projekt : 1 Raum; `gebaeude_geometrie.raeume[]` erlaubt mehrere. Umzug = Modell-Erweiterung? (Scope-Frage für G0c-2.)
- **D5 — Verankerung:** Geometrie ans **Objekt** (`LeadAlternativeAdd`) oder ans **Gewerk** (`LeadProductList`)? (AP-3 Option E: Objekt kanonisch.)

## 3. Daten-Mapping (Feld → Feld, Einheiten bleiben mm-Integer)

| Quelle (`raum_geometrien` / abgeleitet) | Ziel (`gebaeude_geometrie.raeume[]`) | Hinweis |
|---|---|---|
| `polygon` (mm) | `raeume[i].polygon` bzw. daraus `grundflaeche` (via `ausGeometrie`, Topologie-Gate G0b) | mm-Integer bleibt |
| `wand_segmente` (mm, `oeffnungen[]`) | `raeume[i].bauteile[]` (Wand + separate Fenster/Tür), Öffnungsabzug via `RaumHuelleService` | Transform = `GeometrieAbleitungService::ausGeometrie` (Mirror, read-only) |
| `hoehe_mm` | `raeume[i].hoehe_m` (=/1000) | — |
| `decke`/`boden` | `raeume[i].bauteile[]` (typ decke/boden) | — |
| `heizlast_bauteile.u_wert/u_strategie/quelle` | `bauteile[].u_wert` + `u_wert_datenlage` + `quelle` | Datenlage/Quelle mitführen |
| — (neu) | `anforderungsprofile.version/status` | Version statt Überschreiben |

**NULL-Verhalten:** fehlende `decke/boden` → kein Bauteil (wie heute). Keine Personendaten in Geometrie (Pseudonymisierung irrelevant).

## 4. Umstellungsplan Leser/Schreiber

- **Leser:** `AnforderungsprofilHeizlastAdapter` liest **bereits** `gebaeude_geometrie` → nach Umzug die führende Quelle; kein Umbau nötig. `HeizlastController`/Auslegung lesen künftig aus dem Profil statt aus dem transienten Tool-Projekt.
- **Schreiber (Kern des Umzugs):** `GrundrissController::speichern` schreibt die Geometrie als **neue Profil-Version** (`AnforderungsprofilService::neueVersion`) am verankerten Objekt — **statt** `RaumGeometrie::updateOrCreate` (destruktiv). Der alte `raum_geometrien`-Pfad wird **read-only/deprecated** markiert (nicht entfernt — Rückfall-/Archiv-Regel).
- **UX im Heizlast-Tool:** „Speichern" erzeugt künftig eine versionierte Objekt-Geometrie (mit Versionshinweis) statt eines Tool-lokalen Projekts; frühere Versionen bleiben öffenbar/vergleichbar. Genauer UX-Text = G0c-2.
- **Topologie-Gate (G0b) bleibt Pflichtdurchgang** am Ingestion-Punkt — unverändert.

## 5. Äquivalenz-Beweisplan (G0c-2)

- **Dual-Read:** für jede migrierte Zeile das abgeleitete Ergebnis (Flächen/Bauteile) aus alt (`raum_geometrien`→`ausGeometrie`) vs. neu (`gebaeude_geometrie`) — **per-Zeile-Hash-Vergleich** identisch.
- **Zeilenzahlen** alt vs. neu identisch.
- **Golden P1–P5** vor/nach exakt (25/15/24/42 m² · H_V 10,625) — Mirror unverändert.
- Lokal: 0 Zeilen → Äquivalenz trivial grün; der echte Beweis läuft am Deploy-Tag (§7).

## 6. Rollback-Plan (G0c-2)

- **Backup:** DB-Dump der lokalen Arbeits-DB vor Ausführung (Pfad+Hash+Größe ins Manifest).
- **Migration-down:** falls eine additive Migration (D1) entsteht → `migrate` + `migrate:rollback` + `migrate` auf frischer Test-DB belegt; additiv (kein Drop/Rename bestehender Spalten).
- **Abbruchkriterien:** ein Äquivalenz-Hash weicht ab · Mapping-Fall ohne Plan-Deckung · Rollback schlägt fehl · destruktiver Pfad nicht ohne UX-Bruch umleitbar → **Pflicht-Stopp**, keine Altdaten-„Reparatur".

## 7. Deploy-Tag-Abschnitt (Hetzner-Runbook, Reihenfolge)

Der Umzug läuft am von Yama ausgelösten Deploy-Tag gegen den echten Bestand — als Checklistenpunkt **neben** dem G0b-Gate-Scan:
1. **Backup** (DB-Dump, verifiziert).
2. **Migration** (falls additive Spalte, D1) — additiv, Rollback belegt. *(D1 aufgelöst = Verhalten-only, keine Migration; Punkt bleibt nur als Rückfall-Slot.)*
3. **Backfill** `raum_geometrien`(+Bauteile) → `gebaeude_geometrie` (versioniert, nicht destruktiv).
4. **Äquivalenz-Check** (Zeilenzahl + per-Zeile-Hash + P1–P5) — bei Abweichung **Stopp**.
5. **Umschaltung** der Schreiber auf das versionierte Zuhause; alter Pfad read-only.
*(Hetzner bleibt bis zum Deploy-Tag off-limits; hier nur Runbook-Doku.)*

### 7a. Objekt-Zuordnungsstrategie für den Hetzner-Altbestand (Pflicht, kein Raten)

G0c-2 verlangt für jede Geometrie ein **Objekt** (`LeadAlternativeAdd`). Der Hetzner-Altbestand
(`heizlast_projekte`/`raum_geometrien`) ist jedoch **objekt-los entstanden** (kein Objekt-FK, §9-Befund).
Ein automatisches Backfill darf die Objektbindung deshalb **nicht raten** (Operanden-Gate: fehlender
Operand → definierter Fehlerzustand, nie erfundener Wert). Verbindliches Vorgehen am Deploy-Tag:

1. **Kandidaten-Report (read-only):** je Alt-`heizlast_projekt` die erreichbaren Zuordnungs-Signale
   sammeln (erfassender Nutzer, Zeitfenster, ggf. Kunden-/Adress-Bezug aus dem Erfassungskontext) und
   als **manuelle Review-Liste** ausgeben — **kein** automatischer Schreibvorgang.
2. **Eindeutig zuordenbar** (genau ein Objekt belegbar) → Vorschlag in der Review-Liste, Backfill erst
   **nach menschlicher Bestätigung** (Vorschlag + Bestätigung, Automatisierungs-Klasse b).
3. **Nicht eindeutig / kein Signal** → **kein** Backfill; die Zeile bleibt als objekt-lose Alt-Geometrie
   im deprecated `raum_geometrien`-Pfad liegen (nicht gelöscht, nicht geraten) und wird in der
   Review-Liste als „manuell zuzuordnen" geführt.
4. **Kein Sammel-Default** („alle unklaren an Objekt X") — jede Zuordnung ist ein eigener, belegter Posten.

Ergebnis: der Altbestand wird **belegt und mensch-bestätigt** an Objekte gehängt oder bleibt sichtbar
unzugeordnet — nie stillschweigend an ein falsches Objekt gebunden. Die Review-Liste ist der Nachweis.

---

## 8. Nicht-Scope G0c-1 & nächster Schritt
**Nicht-Scope:** keine Migrationsdatei, keine Model-/Controller-Änderung, keine Seeder, kein G1a-Vorgriff, keine Schema-„Verbesserung" am Bestand.
**Nächster Schritt:** **PFLICHT-STOPP** — Yama entscheidet D1–D5 (v. a. **D1 Schemabedarf**: Verhalten-only ohne Migration vs. additive Spalte). Erst nach Freigabe des Plans + der Entscheidungspunkte startet **G0c-2** (Ausführung) als eigener Zyklus.

---

## 9. G0c-2-Vorprüfung (Self-Clearing) — **SOFORT-STOPP** (belegt 2026-07-14)

D1 ist geklärt (Verhalten-only; `gebaeude_geometrie` existiert). **Aber die Schreiber-Umleitung ist Verhalten-only NICHT ausführbar** — Sofort-Stopp-Auslöser:

**Befund (firsthand):** Der Geometrie-Erfassungspfad ist **objekt-los**:
- `heizlast_projekte` hat **keinen** Objekt-FK (kein `alternative_id`/`object_id`/`customer_id`).
- `GrundrissController` (der einzige Ingestion-Schreiber, G0b) hat **keinen** `alternative_id`/`Anforderungsprofil`-Bezug; `speichern()` nimmt **kein** Objekt entgegen.

Das Ziel `anforderungsprofile.gebaeude_geometrie` ist jedoch **objekt-verankert** (`verankerbar = LeadAlternativeAdd`). Ohne Objekt-Kontext kann der Schreiber **nicht** wissen, an welches Objekt/Profil er eine neue Geometrie-Version schreibt. Jede Anbindung trifft die **Sofort-Stopp-Liste** des bedingten Bau frei:
- Objekt-Auswahl im Grundriss-Editor → **UX-Bruch, den Nutzer bemerken würden**.
- „Speichern" erzeugt Objekt-Profil-Version statt Tool-Projekt → **Wechsel der führenden Wahrheit**, den der Nutzer bemerkt.

**→ PFLICHT-STOPP. Yama-Entscheidung nötig (Architektur/UX + führende Wahrheit, nicht delegiert-default):** *Wie erreicht der Objekt-Kontext (`lead_alternative_add`) die Geometrie-Erfassung?*
- **Option A:** Objekt-Auswahl im Grundriss-Editor (UX-Änderung im Tool).
- **Option B (empfohlen):** Geometrie-Erfassung **aus dem Objekt-Kontext** starten — Objektprofil bzw. **AP-3a Konfigurationsarbeitsraum je Objekt** (existiert read-only) übergibt das Objekt; der Editor wird objekt-gebunden aufgerufen. Verbindet G0c mit der AP-3-Klammer (Option E).
- **Option C:** separater objekt-gebundener Erfassungsflow.

Erst nach dieser Entscheidung ist G0c-2 (Verhalten-only) baubar. Alt-Pfad bleibt bis dahin unverändert (nichts gelöscht/umgeleitet).

---

## 10. AUFLÖSUNG des Sofort-Stopps (Yama-Entscheidung 2026-07-14, Rang 4)

**Entscheidung:** **Option B + Auffang-„A-lite".** Der Objekt-Kontext erreicht die Geometrie-Erfassung
aus dem Objekt-/AP-3a-Kontext (Editor wird objekt-gebunden mit `?alternative=<id>` aufgerufen); fehlt
das Objekt beim Speichern, wird **serverseitig mit 422 (`objekt_fehlt`) abgelehnt** statt geraten.
Objektlose Persistenz ist verboten. Kein Option C.

**Begriffsfestlegung (verbindlich, gegen Doppelbenennung):**
- **G0c-2 = die objektgebundene, versionierte Geometrie-Persistenz** in
  `anforderungsprofile.gebaeude_geometrie`. Die „Profil-Persistenz" der Geometrie **ist damit Teil von
  G0c-2** und in dieser Welle umgesetzt (`GrundrissController::speichern` → `AnforderungsprofilService`).
- Eine **spätere Stufe 3b** (WP-Auslegungskette) darf **NICHT erneut als „Profil-Persistenz"** beschrieben
  werden; sie ist **Folgeintegration** (Controller-Umstellung der Auslegungskette, E8-Preisanbindung,
  `offer_details`-Übergabe, E2E-Referenz) — ein anderer Gegenstand als die Geometrie-Persistenz.

**Scope-Zuschnitt (Yama):** *Backend jetzt, UI als Sub-Slice.* Der server-seitige 422-Guard genügt für
G0c-2; der sichtbare Objekt-Picker/„Grundriss erfassen"-Action aus AP-3a ist ein **browser-verifizierter
Sub-Slice** (verschoben, markiert). Kein Editor-Redesign.

**D1–D5 Auflösung (Kurzform; wörtlich im G0c-2-RELEASE-MANIFEST):** D1 = Verhalten-only, **keine
Migration** (`gebaeude_geometrie` existiert). D2 = `raum_geometrien` bleibt editierbarer/transienter
Vorschau-Layer, `gebaeude_geometrie` wird führend. D3 = `heizlast_bauteile` bleibt re-derivierbarer
Cache (nicht in G0c-2 stillgelegt). D4 = 1 Raum je Save (Mehrraum bleibt Modell-Erweiterung späterer
Slice). D5 = Verankerung am **Objekt** (`LeadAlternativeAdd`, AP-3 Option E).

**Umsetzungsstand G0c-2:** umgesetzt (Backend), Tests grün, volle Suite nur E4-Reverb rot. Details +
Prüfbefehle: `docs/g0c2-release-manifest.md`. Alt-Tabellen bleiben (deprecated, nicht gelöscht).

*Ende G0c-1/2-Plan. Read-only-Plan; die Ausführung G0c-2 ist im G0c2-RELEASE-MANIFEST belegt.*
