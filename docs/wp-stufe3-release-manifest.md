# RELEASE-MANIFEST — WP Stufe 3a: Auslegungsketten-Orchestrator (Verknüpfung)

**Stand:** 2026-07-14 · **lokal, kein Push, keine Migration, kein Kern-Eingriff, kein Commit** (Commit-Freigabe = Yama nach unabhängiger Prüfung).
**Startblock:** „Bau frei für WP Stufe 3" (Planner in delegierter Yama-Rolle). **Grundlage:** `docs/wp-stufe2-workflow-auslegungskette.md` (abgenommen). **Git-Ausgangsstand:** `6a45985` (G0b).
**Abschlussformel:** *Umgesetzt, bereit zur unabhängigen Prüfung.* Keine Selbst-Abnahme, keine Auto-Fortsetzung.

---

## 1. Umgesetzter Scope (Stufe 3a)
Ticket-eigener Orchestrator `WpAuslegungsketteService`, der die brachliegende Krone **`BivalenzService` je Kandidat (N×)** verdrahtet und ein Ranking (E-WP4) als **Read-Model** liefert. **Reuse, keine Parallelberechnung; Kern-Services byte-unverändert.**
- Kette: `WaermepumpenMatchService::kandidaten` → `CatalogDeviceRepository::heatPumps()` (WpKennlinie je `id`) → `BivalenzService::berechne` je Kandidat → Gate A + Ranking → gelabeltes DTO.
- **Gate A** (Muss): Vorlauf ≤ `max_vorlauf_c`, Heizlastdeckung@Bivalenzpunkt ≥ `min_deckung_ne_pct`; ungeeignete Kandidaten mit Grund ausgewiesen (nicht still entfernt).
- **Ranking B:** Gewichte in **einer** Config-Quelle (`config/wp_auslegung.php`). **Label „informativ, nicht verbindlich"** ist Teil des Ergebnisobjekts (`verbindlich=false`).
- **NIBE** wird als Feld gekennzeichnet, **nicht** geboostet (Test belegt: bessere JAZ einer anderen Marke rankt höher).
- **Operanden-Gate (G1–G5):** fehlender Pflicht-Operand → definierter Fehlerzustand (`gates_offen`), nie geraten.

## 2. Nicht umgesetzt / bewusst verschoben (E-WP5 + Pflicht-Stopp-Regeln)
- **E-WP4 Invest (30 %) + Verfügbarkeit (20 %) — Preis-Frage (E-WP5, punktueller Stopp an Yama, KEIN Gesamtstopp):** In ticket **keine belegbare Quelle** (belegt: `product_heat_pump_specs` ohne Preisfeld; `products` nur `price_unit`-String; `distributor_prices` via OMD/IDS inert; kein Verfügbarkeitsfeld). → Gewichte werden **nicht angewandt** (renormalisiert über JAZ), jeder Kandidat trägt `invest_quelle_fehlt`/`verfuegbarkeit_quelle_fehlt`, kein erfundener Wert. **Yama-Entscheidung offen:** Preis-/Verfügbarkeitsquelle (OMD/IDS-Anbindung? `products`-Preis? manuell?).
- **Persistenz am Anforderungsprofil (#4):** Stufe 3a = **Read-Model** (on-the-fly, schreibt nichts) → **keine Migration, keine neue Tabelle, keine neuen Spalten** (Pflicht-Stopp-Regel „Persistenz braucht Schema → Stopp" damit gar nicht ausgelöst). Ein persistierter Auslegungs-Snapshot bräuchte Schema/Registry-Erweiterung → **Stufe 3b** mit eigenem Pflicht-Stopp/Vorschlag.
- **Controller-Umstellung (#5):** **verschoben auf Stufe 3b** — die 3 Controller unverändert (keine Regression). Orchestrator ist gebaut/getestet/verfügbar; Umstellung + Deprecation der Fragmentpfade isoliert, um Regression sauber prüfbar zu halten (E-WP5 architektur → dokumentierte Default-Annahme).

## 3. E-WP5-Default-Annahmen (Annahme → Auswirkung → Änderungsweg)
| # | Annahme | Auswirkung | Änderungsweg |
|---|---|---|---|
| A1 | Orchestrator **input-agnostisch** (Eingabe-DTO; Quelle Request ODER Anforderungsprofil) | Verankerung am Profil ist Stufe 3b, kein Schreibpfad in 3a | Stufe-3b-Startblock |
| A2 | Config-Ort `config/wp_auslegung.php` | eine Ranking-Quelle | Config-Edit (kein Code) |
| A3 | Gerätetyp/Heizsystem als Operand (nicht hartkodiert) | fehlt → `geraetetyp_fehlt`-Gate | Feld-Herkunft in 3b belegen |

## 4. Geänderte/neue Dateien
- **Neu:** `config/wp_auslegung.php`, `app/Services/Auslegung/WpAuslegungsketteService.php`, `app/Services/Auslegung/WpAuslegungsEingabe.php`, `tests/Unit/Auslegung/WpAuslegungsketteServiceTest.php`, `docs/wp-stufe3-release-manifest.md`.
- **Geändert:** **keine** — bezogen auf den **WP-Produktivscope** (Kern-Services + Controller unberührt). Die getrackte `CLAUDE.md`-Modifikation im Arbeitsbaum ist **dokumentierter Vorbestand** (Rang-2-Drift, nicht in dieser Arbeit erzeugt, nie committet) — Befund: `docs/claude-md-befund.md`.

## 5. Kern-Services unverändert (Mirror-Analogie)
`git status app/Services/Heizlast/ app/Services/Klima/ app/Repositories/` → **leer**. `BivalenzService`/`WaermepumpenMatchService`/`CatalogDeviceRepository`/`JazService`/… nur aufgerufen, nicht verändert.

## 6. Datenbank / Migration
**Keine.** Read-Model, keine Persistenz, keine neue Tabelle/Spalte.

## 7. Tests — Befehle & Rohresultate
```
php artisan test tests/Unit/Auslegung/WpAuslegungsketteServiceTest.php
→ 9 passed (25 assertions)
```
Abdeckung (Startblock #7): Ranking-Reihenfolge + **Gate-A-Ausschluss** (id3 Vorlauf>Grenze, mit Grund) · **Label**-Präsenz im Ergebnisobjekt · **NIBE gekennzeichnet, nicht geboostet** (Gegenprobe: bessere Marke rankt höher) · **Invest/Verfügbarkeit `quelle_fehlt`** · **Config-Gate-A-Kipptest** (min_deckung 90→93 kippt Eignung) · **Gewicht-Kipptest** (JAZ-Gewicht 0 → Reihenfolge kippt nachvollziehbar → Config load-bearing) · **G1–G5-Gates** (fehlender Operand → definierter Fehler statt Ergebnis) · keine-Kandidaten (kein leeres „fertig") · **Determinismus**.
Wächter (volle Suite): `php artisan test` → **601 passed, 1 failed, 0 incomplete**. Einziger Rotfall: `InvoiceDeletionGuardTest` (Reverb `localhost:6001`) — **E4-anerkannter Vorbestand**, einziger Rotfall (E4-Ausnahme hält).

## 8. Referenz-/Determinismus-Hinweis (ehrlich)
Die Orchestrator-Logik ist mit **gestubten Kern-Services** deterministisch getestet (Gate A / Gewichte / Label als eigene Handrechnung). Ein Ende-zu-Ende-Referenzfall mit **realem** `BivalenzService` braucht geseedete `klima_plz` + Katalog-Kennlinien und ist als **Stufe-3b-Integrationsnachweis** vorgemerkt (dort auch die BivalenzService-interne Bivalenzpunkt-Handrechnung). `BivalenzService` selbst ist der (isolierte) Kern und wird hier nur verdrahtet.

## 9. Bestätigungen
- ✅ Kern-Services **unverändert** (Mirror-Analogie; git leer).
- ✅ **Keine Migration**, keine neue Tabelle/Spalte, **keine Preislogik-Änderung**.
- ✅ **Keine Controller-Regression** (unverändert; Umstellung = 3b).
- ✅ **Kein UI/Alpine**, kein wberechnung-Eingriff, kein G0c-/Gebäudeplaner-Vorgriff.
- ✅ **Label-Pflicht** im Ergebnisobjekt; **Invest/Verfügbarkeit nicht erfunden**.
- ✅ Kein `git add -A`, kein Push, HEAD `6a45985` unverändert.
- ⚠️ **Punktueller Preis-Stopp (E-WP5)** an Yama: Invest/Verfügbarkeit-Quelle (s. §2). Zweiter Rotfall = keiner (E4-Ausnahme gilt fort).

## 10. Prüfbefehle für den Evaluator
1. `git status --short app/Services/Heizlast app/Services/Klima app/Repositories` → leer (Kern unverändert).
2. `php artisan test tests/Unit/Auslegung/WpAuslegungsketteServiceTest.php` → 9 passed.
3. Config-Kipp selbst: `min_deckung_ne_pct` bzw. `gewichte.jaz` ändern → Reihenfolge/Eignung kippt nachvollziehbar.
4. NIBE-Nicht-Boost selbst prüfen (bessere Marke → höher).
5. Bypass: ein Ranking-Kriterium ohne Quelle erzeugt keinen erfundenen Wert (`quelle_fehlt`).
6. Suite grün außer E4-Reverb.

## 11. Offene Punkte / nächster Schritt / Ballbesitz
- **Punktueller Stopp (Yama):** Preis-/Verfügbarkeitsquelle für Invest/Verfügbarkeit (E-WP4).
- **Nächste Schritte:** **Stufe 3b** (Controller-Umstellung der Auslegungskette + E8-Preisanbindung + `offer_details`-Übergabe + E2E-Integrationsreferenz; ggf. persistierter **Auslegungs-Snapshot** mit Pflicht-Stopp bei Schemabedarf) als eigener Slice. **Begriffsabgrenzung (Yama 2026-07-14):** Der Stufe-3b-„Persistenz-Snapshot" meint den **WP-Auslegungs-Snapshot** — NICHT die **Geometrie-Profil-Persistenz**, die als **G0c-2** (objektgebundene, versionierte `gebaeude_geometrie`) bereits eigenständig umgesetzt ist (`docs/g0c2-release-manifest.md`). Stufe 3b darf nicht erneut „Profil-Persistenz" der Geometrie heißen. Parallel **G0c-1/2** (Geometrie) abgeschlossen/read-only-Plan.
- **Ballbesitz:** Evaluator (Prüfung) → danach **Yama** (Commit-Freigabe Stufe 3a + Preis-Entscheidung).

---

*Umsetzung der beauftragten Welle WP Stufe 3a abgeschlossen. RELEASE-MANIFEST und Prüfbefehle liegen vor. Umgesetzt, bereit zur unabhängigen Prüfung.*
