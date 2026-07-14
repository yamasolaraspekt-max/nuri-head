# Startblock — AP-1 / Paket 5b: Heizlast-Belastbarkeits-Gate

**Stand:** 2026-07-14 · **read-only Analyse für den Startblock — KEIN Bau, kein Commit, kein Push, keine Migration, kein Seeder, keine Datenänderung.**
**Kapitel:** 6 (Heizlast & Gebäudebewertung), speist 7 (WP) + 12 (Angebotsübergabe).
**Grundlage:** `docs/gesamtfahrplan-gebaeude-energie-angebot.md` §8/§9, `docs/bereich2-wp-auslegungswizard-gap-analyse.md` §5, CLAUDE.md (Operanden-Gate, „eine Wahrheit", Startblock-Pflicht).

---

## 1. Ziel

Verhindern, dass eine **geschätzte oder unsichere Heizlast** als **verbindliche** Grundlage für WP-Geräteranking, technische Auslegung oder Angebotsfreigabe wirkt — ohne die Angebotserstellung unnötig zu blockieren und ohne eine zweite Heizlast-Wahrheit zu erzeugen.

## 2. Startpunkt / Ausgangsbefund (verifiziert)

Der Ausgangsbefund der Gap-Analyse ist **code-belegt bestätigt**:
- `OfferReadinessService::gradTechnischeAuslegung()` prüft nur **Existenz** eines Heizlast-Werts (`->exists()`), **ohne** `datenlage` zu bewerten → `geschaetzt` zählt voll `1.0` wie `berechnet`/`gemessen`.
- `WaermepumpenMatchService::kandidaten(float $benoetigtKw, …)` dimensioniert/rankt aus reinem kW-Wert — **kein** Belastbarkeits-Parameter, **kein** Guard.
- Das `datenlage`-Feld existiert bereits — die Information ist da, wird aber am Gate **nicht ausgewertet**.

---

## 3. Pflichtprüfung — die 10 Fragen (read-only beantwortet, mit Belegen)

### 1) Wo wird Heizlast aktuell gespeichert?
**Führend: `anforderungsprofil_werte`** (EAV), verankert am Objekt (`LeadAlternativeAdd`) **oder** Gewerk (`LeadProductList`), **versioniert** (genau eine aktive Version, `Anforderungsprofil::aktiv()`).
- Schlüssel: `phi_hl_kw`, `standardheizlast_kw`, `auslegungsheizlast_kw`, `spezifische_heizlast_w_m2` (`SchluesselRegistry.php:22–25`).
- Je Wert: `wert_num`, `datenlage`, `quelle`, `erfassungsweg` (`create_anforderungsprofil_werte_table.php:23–27`).
- **Transient daneben:** `HeizlastController::wpBerechnen` legt ein `HeizlastProjekt`+`heizlast_raeume`+`heizlast_bauteile` an, rechnet, und **löscht das Projekt sofort wieder** (`HeizlastController.php:171` `$projekt->delete()`) → reines Rechner-Tool, **keine** persistente zweite Wahrheit.

### 2) Gibt es bereits datenlage / confidence / quelle / berechnungsweg?
**Teilweise — genug für einen ersten Guard, aber nicht für alle vier Belastbarkeitsstufen:**
- **`datenlage`** — Enum `SchluesselRegistry::DATENLAGE = ['gemessen','berechnet','geschaetzt']` (`SchluesselRegistry.php:46`).
- **`quelle`** — Freitext (z. B. `"HeizlastRechner"`, `"klima_plz 01067"`, `"Kundenangabe"`).
- **`erfassungsweg`** — `manuell | import | berechnet | default`.
- **`confidence`/`belastbarkeit`** — **existiert nicht** als eigenes Feld.
- **`berechnungsweg`** (raumweise-DIN vs. Verbrauch vs. Typologie/Bauteil vs. Überschlag) — **existiert nicht** als expliziter, strukturierter Marker (steckt allenfalls im Freitext-`quelle`).
- **„fachlich freigegeben"** — **existiert nicht** (Freigabe-Workflow = Kapitel 13, nicht gebaut).
- Hinweis: `AuslegungVorschlagService` nutzt zusätzlich ein **synthetisches** `datenlage='fehlt'` als **Anzeige**-Marker für fehlende Werte (kein gespeicherter Wert).

### 3) Wo liest `OfferReadinessService` die `technische_auslegung`?
`gradTechnischeAuslegung()` (`OfferReadinessService.php:270`): `Anforderungsprofil::aktiv()…whereHas('werte', schluessel IN AUSLEGUNG_KEYS)->exists()` → **`1.0` bei bloßer Existenz, `datenlage` ignoriert.** Kriterium `technische_auslegung` ist **Gruppe `pflicht`** (weich, Gewicht 1) — **kein Blocker** (`:164`). `AUSLEGUNG_KEYS = ['phi_hl_kw','standardheizlast_kw','auslegungsheizlast_kw']` (`:39`).

### 4) Wo liest `AuslegungVorschlagService` die Heizlast?
`werteLesen()` liest `phi_hl_kw`/`standardheizlast_kw` **inkl. `datenlage`** (`AuslegungVorschlagService.php:90–118`); `positionGeraet()` gibt die `datenlage` durch (Default `'berechnet'`, `:136`). Der Service ist **read-only**, alle Positionen `preis_status='katalog_anker_fehlt'` (`:220`) — er **trägt `datenlage` bereits mit, gated aber nicht darauf**.

### 5) Wo nutzt `WaermepumpenMatchService` den kW-Wert?
`kandidaten(float $benoetigtKw, string $wpTyp, string $heizsystem, float $vorlaufC, int $limit=6)` (`WaermepumpenMatchService.php:23`) — reine Funktion auf kW; `bewerten()` (`:81`) rankt Geräte. **Kein `datenlage`-Parameter, kein Guard.** **Einziger Aufrufer:** `HeizlastController::wpBerechnen` (`:180`) mit `auslegungsheizlast_kw` aus der transienten Rechnung.

### 6) Gibt es einen Schreibpfad, der daraus verbindliche Empfehlungen macht?
**Nein — heute existiert KEIN persistierender „verbindliche WP-Empfehlung"-Schreibpfad.**
- `HeizlastController::wpBerechnen` gibt `[$ergebnis, $wp]` als **JSON** ans Rechner-Tool zurück, Projekt vorher gelöscht → nichts geschrieben.
- `AuslegungVorschlagService` = read-only Vorschau. `WpAngebotsWorkflowService`/4a-Cockpit = read-only.
→ **Der Guard ist präventiv:** Er verhindert (a) dass `technische_auslegung` auf `geschaetzt` voll erfüllt wirkt (→ Reife/„technisch bereit"), und (b) dass das WP-Ranking als *verbindlich* präsentiert wird — **bevor** je ein Schreibpfad daraus abgeleitet wird.

### 7) Welche Datenlagen gelten als belastbar?
Ziel-Regelmodell (gewünscht) → heute vorhandene Felder:
- **belastbar:** aktuelle raumweise DIN-Heizlast passend zum Gebäudezustand **oder** fachlich freigegeben.
  → heute am ehesten `datenlage='berechnet'` **mit** `quelle='HeizlastRechner'` (raumweise). „Freigegeben" hat **keinen** Marker (Kap. 13).

### 8) Welche Datenlagen sind nur vorläufig?
- **eingeschränkt:** Verbrauchsauswertung (gute Qualität) / Bauteil-/Typologie-Berechnung (mittlere Qualität).
  → heute `datenlage='gemessen'` (Verbrauch) bzw. `berechnet` mit typologischer `quelle` — **nicht sauber von belastbar trennbar** ohne `berechnungsweg`-Marker.
- **vorläufig:** Schätzung / nur Fläche+Baujahr / unvollständig.
  → `datenlage='geschaetzt'`.
- **unzureichend:** kein Heizlastwert oder nicht plausibel.
  → kein Wert (`exists()=false`) bzw. `wert_num` außerhalb Plausibilitätsband.

### 9) Was darf bei vorläufiger Heizlast weiterlaufen?
- Read-only **Vorschau** (`AuslegungVorschlagService`, 4a-Cockpit, Rechner-Tool) — **ja, aber klar markiert** („vorläufig / nicht verbindlich").
- Angebots**erstellung** generell — **ja** (nicht unnötig blockieren), **aber** ohne verbindliche WP-Systemempfehlung.

### 10) Was muss blockiert / als Warnung markiert werden?
- **Exaktes Geräte-Ranking** als *verbindlich* → **nur** bei belastbar/freigegeben; sonst als **unverbindliche Vorschau** markieren.
- **Angebotsreife** darf **nicht 100 % / nicht „technisch freigegeben"** werden, wenn Heizlast nur vorläufig → `gradTechnischeAuslegung` **teilerfüllt** statt `1.0`.
- **Verbindliche WP-Systemempfehlung** → gesperrt bei vorläufig/unzureichend.

---

## 4. Regelmodell (Mapping auf vorhandene Felder + ehrliche Lücken)

| Stufe | fachlich | heute ableitbar aus | Reife-Beitrag `technische_auslegung` | Ranking |
|---|---|---|---|---|
| **belastbar** | aktuelle DIN-Heizlast passend / fachlich freigegeben | `datenlage='berechnet'` + `quelle~HeizlastRechner`; („freigegeben" fehlt als Marker) | **1.0** (voll) | **verbindlich erlaubt** |
| **eingeschränkt** | Verbrauch gut / Typologie-Bauteil mittel | `datenlage='gemessen'` (Verbrauch) / `berechnet` typologisch | **~0.6 teilerfüllt** + Warnung | nur **Vorschau (unverbindlich)** |
| **vorläufig** | Schätzung / nur Fläche+Baujahr | `datenlage='geschaetzt'` | **~0.3 teilerfüllt** + Warnung | **keine** verbindliche Empfehlung |
| **unzureichend** | kein Wert / nicht plausibel | kein Wert / außerhalb Band | **0.0** (wie bisher) | gesperrt |

**Ehrliche Lücken (Migrations-relevant — hier die STOPP-Prüfung):**
1. **Trennung belastbar ↔ eingeschränkt** (raumweise-DIN vs. Verbrauch/Typologie) ist mit `datenlage` allein **nicht** sauber; ein strukturierter **`berechnungsweg`/`methode`**-Marker fehlt. → **Slice-1-Entscheidung:** zunächst **heuristisch** über `datenlage` + `quelle`/`erfassungsweg` klassifizieren, **ohne Migration**. Falls die Heuristik fachlich nicht trägt und ein echter `methode`-Marker nötig ist → **STOPP + melden** (Migration wäre eigener Posten).
2. **„fachlich freigegeben"** hat heute **keinen** Marker (Kap. 13). → Slice 1 behandelt „belastbar" allein über `datenlage='berechnet'`+Rechner-Quelle; ein Freigabe-Marker ist **Abhängigkeit zu Kapitel 13**, nicht Teil von 5b.

→ **Migrations-Bewertung für Slice 1: KEINE Migration nötig** (vorhandene Felder reichen für einen ehrlichen ersten Guard). Sollte sich beim Bau zeigen, dass eine saubere Klassifizierung ohne neues Feld nicht möglich ist → **STOPP nach Befund**, kein stiller Migrationsbau.

---

## 5. Analyseumfang / konkret zu lesende Dateien (bereits gelesen)

- `app/Services/Anforderungsprofil/SchluesselRegistry.php` (DATENLAGE, Keys)
- `app/Models/AnforderungsprofilWert.php` + `database/migrations/2026_07_05_170006_*` (Schema datenlage/quelle/erfassungsweg)
- `app/Services/Offer/OfferReadinessService.php` (`gradTechnischeAuslegung`, `AUSLEGUNG_KEYS`, Kriterienkatalog)
- `app/Services/Offer/AuslegungVorschlagService.php` (`werteLesen`/`positionGeraet`, datenlage-Durchreichung)
- `app/Services/Heizlast/WaermepumpenMatchService.php` (`kandidaten`/`bewerten`)
- `app/Http/Controllers/Energie/HeizlastController.php` (`wpBerechnen`, transient + einziger Match-Aufrufer)
- `app/Services/Anforderungsprofil/AnforderungsprofilHeizlastAdapter.php` (Schreibquelle der Heizlast-Werte — für Klassifizierungs-Heuristik)

## 6. Vorgehensweise Schritt für Schritt (Bau-Slice, NACH Freigabe)

1. **Belastbarkeits-Klassifizierer (read-only, klein):** eine reine Funktion/kleiner Service `HeizlastBelastbarkeit` (o. ä.), die aus `datenlage`+`quelle`+`erfassungsweg`(+Plausibilitätsband) eine der 4 Stufen liefert. **Keine** Persistenz, **keine** zweite Wahrheit — leitet nur aus den vorhandenen Feldern ab.
2. **`OfferReadinessService::gradTechnischeAuslegung`** von `exists()→1.0` auf **abgestuften Grad** (1.0/0.6/0.3/0.0 gem. §4) umstellen — liest zusätzlich die aktive-Version-Werte inkl. `datenlage`. Bleibt Gruppe `pflicht` (weich); dadurch kann Reife bei vorläufig nicht „technisch freigegeben"/100 % erreichen.
3. **Ranking-Markierung:** an der Stelle, die das WP-Ranking ausgibt (heute `HeizlastController::wpBerechnen` + read-only Vorschau/Cockpit), das Ergebnis mit `verbindlich: bool` + Stufe kennzeichnen; bei nicht-belastbar als **Vorschau (unverbindlich)** ausweisen. **Kein** neuer Schreibpfad.
4. **Kleine Anzeige (optional, nur wenn klein/sicher):** Belastbarkeits-Badge/Ampel im Reife-Panel/4a-Cockpit (Ticket-CI, kein Alpine) — read-only.

## 7. Scope

- Kleiner Guard/Klassifizierer + Erweiterung **bestehender** Services (`OfferReadinessService`, Vorschau/Ranking-Ausgabe).
- Optional kleines read-only Badge in Reife/Cockpit.

## 8. Nicht-Ziele

Keine Preislogik · keine Kataloglogik · kein Angebots-Schreibpfad · keine UI-Großänderung · **keine zweite Heizlast-Wahrheit** · keine Migration (bei Bedarf STOPP) · kein Freigabe-Workflow (Kap. 13) · kein `berechnungsweg`-Feldbau (nur heuristisch nutzen) · keine Änderung am Rechenkern.

## 9. Testpfad (vorgeschlagen)

1. **belastbar** (`datenlage='berechnet'`, Rechner-Quelle) → `technische_auslegung` = 1.0 erfüllt; Ranking `verbindlich=true`.
2. **eingeschränkt** (`gemessen`/Verbrauch) → teilerfüllt (~0.6) + Warnung; Ranking `verbindlich=false` (Vorschau).
3. **vorläufig** (`geschaetzt`) → teilerfüllt (~0.3) + Warnung; **keine** verbindliche Empfehlung; Reife < „technisch freigegeben".
4. **keine Heizlast** → 0.0, blockiert wie bisher.
5. **Read-only Vorschau** (`AuslegungVorschlagService`) bleibt bei eingeschränkt/vorläufig lauffähig, aber **markiert**.
6. **`WaermepumpenMatchService`/Aufrufer** erzeugt bei unzureichender Datenlage **kein verbindliches** Ranking.
7. **Kein-Write-Test:** Guard/Änderung schreibt nichts (Tabellen-Counts vorher==nachher), keine zweite Wahrheit.

## 10. Browser-Prüfpfad

- Reife-Panel (`offers.angebotsreife`) + 4a-Cockpit: bei geschätzter Heizlast **kein** 100 %/„technisch freigegeben", Belastbarkeits-Hinweis sichtbar; bei belastbarer Heizlast voll erfüllt. WP-Vorschau zeigt „unverbindlich" bei vorläufig.

## 11. Risiken

- **Fehlklassifizierung** (belastbar↔eingeschränkt ohne `methode`-Marker) → konservativ runden (im Zweifel niedrigere Stufe) + Warnung; harte Trennung erst mit späterem Marker.
- **Reife-Regression** (bestehende „reife" WP-Vorgänge fallen unter 100 %) → gewollt, aber sichtbar kommunizieren; kein Blocker-Upgrade (bleibt weich `pflicht`).
- **Scope-Kriechen** Richtung Freigabe-Workflow/Migration → strikt draußen; STOPP-Regeln.

## 12. Abhängigkeiten

- Nutzt vorhandene Felder `datenlage`/`quelle`/`erfassungsweg` (kein Neubau).
- „belastbar via Freigabe" hängt an **Kapitel 13** (Freigabe-Marker) — bewusst später.
- Saubere belastbar↔eingeschränkt-Trennung hängt an optionalem `berechnungsweg`-Marker — bewusst später (Slice 1 heuristisch).

## 13. Rückfallpfad

- Änderungen sind **additiv** an bestehenden Services (kein Löschen, kein Schema). Rückfall = Revert der 1–3 berührten Dateien (path-scoped), da kein Migration/Datenstand berührt wird.
- Vor Bau: betroffene Dateien notieren; `docs/rueckfall-archiv-regeln.md` gilt (nichts überschreiben/löschen).

## 14. Stop-Kriterium

- Startblock endet **hier** (read-only). **Bau erst nach Yama-Freigabe.**
- Beim Bau: **STOPP + melden**, falls (a) eine Migration nötig scheint, (b) die datenlage-Heuristik fachlich nicht trägt und ein `methode`/`berechnungsweg`-Feld gebraucht wird, (c) der Scope über den Guard + abgestufte Reife + Ranking-Markierung hinausginge.

## 15. Yama-Abnahme erforderlich?

**Ja** — vor Bau (Slice-Freigabe) und vor jedem Commit/Push (Standard). Evaluator strikt read-only.

---

*Ende Startblock. Read-only, keine Code-/Schema-/Datenänderung. Nächster Schritt: Yama-Freigabe für den Bau-Slice — dann Umsetzung gem. §6, sonst STOPP.*
