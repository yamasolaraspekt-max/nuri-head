# EVALUATION — WP Stufe 3b / P1b-1: WpCostingService (verhaltensgleiche Kostenextraktion)

**Rolle:** unabhängiger, read-only Evaluator (Generator ≠ Evaluator).
**Datum:** 2026-07-14 · **Prüf-HEAD:** `f76b7dd` (bestätigt) · **Umgebung:** PHP 8.4 / Laravel 11 / Test-DB `ticket_testing`.
**Manifest:** `docs/configuration/wp-p1b1-costing-release-manifest.md`.

## Urteil: **GRÜN**

Verhaltensgleiche Extraktion der WP-Kostenlogik in `WpCostingService`/`WpKostenErgebnis` ist sauber umgesetzt und gegen den P1a-Golden-Master vollständig paritätisch. Kein Golden-Wert verschoben, keine zusätzliche Fachlogik gezogen, A1 strukturell erhalten. Eigene Nachrechnung, Gesamtsuite und beide selbst gebauten Gegenbeweise bestätigen das Manifest. Einzige Auflage ist operativ (siehe unten) und deckt sich mit dem, was das Manifest ohnehin fordert.

---

## 1. Git / Scope
- `git rev-parse HEAD` = `f76b7dd…` — erwartungsgemäß.
- Tracked-Änderungen (`git status --short`, ohne untracked):
  `M CLAUDE.md` · `M EnergieAuslegungController.php` · `M EnergiekonzeptController.php`.
  `CLAUDE.md` ist reine **Vorbestands-Drift** (nicht Teil von P1b-1, kein Code).
- `git status --short database routes config resources` → **LEER**. Keine Migration/Route/View/Config berührt.
- `git diff f76b7dd -- tests/Feature/Energie/` → **LEER**. Golden-Master unverändert (committet in f76b7dd, alle 3 Dateien tracked).
- `git diff` der beiden Controller: **ausschließlich der Kostenpfad** — `use KostenService`→`use WpCostingService`, Konstruktor-Property `$kosten`→`$costing`, Entfernen der lokalen `$stromkostenJahr`- und `$kosten[...]`-Berechnung, Ersatz durch `$this->costing->berechne(...)`, `stromkosten_jahr`-Ausgabe auf `round($kostenErg->stromkostenJahr)`. Keine fremde Zeile, keine fremde Datei im Diff.

**Scope-Beobachtung (Auflage, operativ):** Der Working-Tree trägt einen großen Stapel **unrelated untracked Docs** (`docs/ap2-*`, `docs/ap3-*`, `docs/bereich2-*`, `docs/arbeitskompass-ticket.md`, `docs/wp-stufe*`, u.a.), die NICHT zu P1b-1 gehören. Sie berühren keinen Code/keine Migration/Route/Config/View, sind also für die fachliche Bewertung irrelevant — aber ein naives `git add -A` würde sie mit einsammeln. **Der spätere Commit MUSS pfad-scoped erfolgen** (nur Service + DTO + Test + die 2 Controller + Manifest). Das Manifest fordert genau das bereits (✅ „Kein `git add -A`").

## 2. Ausschließlich Kostenlogik extrahiert? — JA
`WpCostingService::berechne(float $investition, float $stromKwh, float $strompreis): WpKostenErgebnis` kapselt genau:
- `investitionNetto = KostenService::berechne([], null, null, $investition)['summe_netto']`
- `stromkostenJahr = $stromKwh * $strompreis` (roh)

`WpKostenErgebnis` ist ein readonly-DTO mit exakt diesen zwei Feldern. **Nicht** enthalten: Förderung, Dokument, Ranking, Bivalenz, JAZ-/Stromverbrauch-Neuberechnung, Warmwasser, Produktwahl, Geräteparameter. `KostenService` wird per Reuse/Default-Instanz unverändert eingebunden. Signatur nimmt nur typisierte Skalare (kein Request/Controller/View/Session). Bestätigt konform zu Manifest §2.

## 3. Controller dünner + keine zweite Kostenformel — JA
`grep -n "kosten->berechne|stromKwh \*|summe_netto|stromkostenJahr" …Controller` → **kein Treffer** einer duplizierten WP-Kostenformel mehr; beide Controller rufen nur noch `$this->costing->berechne(...)`.
`grep -rn "WpCostingService|WpFundingAssessmentService|WpDocumentService" app/` → **nur `WpCostingService`** existiert. Kein Förder-/Dokument-Service vorgezogen (A2 aufgelöst, ohne P1b-2/3 vorwegzunehmen).
Rest-Referenz „KostenService" nur noch als **beschreibender Kommentar** (`EnergieAuslegungController:181`) — korrekt, da KostenService der gekapselte Kern bleibt. Kein toter Import, kein toter Code.

## 4. P1a-Golden-Master unverändert — JA
- `php artisan test tests/Feature/Energie/` → **21 passed (126 assertions)**.
- Golden-Dateien nicht als `M` in `git status`; `git diff f76b7dd -- tests/Feature/Energie/` LEER.

## 5. Rundung identisch — JA
Service liefert `stromkostenJahr` **roh**; die `round()`-Ausgabe sitzt weiterhin im Controller-View-Model (`'stromkosten_jahr' => round($kostenErg->stromkostenJahr)`).
Golden-Werte eigenständig verifiziert in `WpAuslegungCharakterisierungTest`:
- Normalfall stromkosten **1945.0** (`round(6483*0.30)`), investition_netto **25000.0**.
- WW ohne WP stromkosten **1655.0**.
- Förderdeckel: investition **50000.0** → zuschuss **15000.0** (50 % von 30000).
Alle unverändert.

## 6. A1 erhalten — JA
`WpCostingService` hat **keinen Geräteparameter** → Kosten bleiben geräteunabhängig (heutiges, fachlich fragwürdiges Ist). `test_geraetewahl_aendert_nur_anzeige_nicht_die_zahlen` (Golden) und `WpCostingServiceTest::test_geraete_unabhaengig_und_deterministisch` bestätigen das erhaltene Ist. P1b-1 hat A1 **weder korrigiert noch versteckt beeinflusst**; A1-Korrektur ist korrekt als P1c ausgelagert.

## 7. Eigene Gegenbeweise (mit Restore-Nachweis)
md5-Baseline vor Mutation:
- Service `d5c26ce5a531c3490b3a3de9fbf417ca`
- EnergieAuslegungController `d9f520ae792ffcad5f09e097b436fb0f`
- EnergiekonzeptController `00198973b7dc61a9c70b2bb7bd261b02`

| # | Mutation | Erwartung | Ergebnis (mein Lauf) |
|---|---|---|---|
| G-A | Service `stromkostenJahr … * 1.1` | Service- + Golden-Test rot | **9 failed** (Unit+Feature zusammen), inkl. `WpAuslegungCharakterisierungTest`, Service `standardausgabe`/`warmwasser`/… |
| G-B | Controller-Ausgabe `round`→`floor` | Rundungs-/Normalfall-Test rot | **1 failed** (`WpAuslegungCharakterisierungTest` normalfall stromkosten) |

**Restore-Nachweis:** Nach beiden Restores md5 aller drei Dateien **identisch zur Baseline** (Service `d5c26ce5…`, Auslegung `d9f520ae…`, Konzept `00198973…`). `git status` zeigt nach Restore wieder nur `M CLAUDE.md` + die 2 planmäßigen Controller — keine Mutation im finalen Diff. Beide Gegenbeweise beweisen, dass die Tests echte Regressionen der extrahierten Formel und der Ausgabe-Rundung fangen.

## 8. Gesamtsuite
`php artisan test` → **635 passed, 1 failed (2253 assertions)**.
Einziger Rotfall: `Tests\Feature\Invoice\InvoiceDeletionGuardTest` — `BroadcastException`, Pusher/Reverb `localhost:6001` nicht erreichbar (bekannter **Reverb-E4**, infrastrukturell, nicht P1b-1-verursacht, nicht neu).

## 9. Manifest-Wahrheit (Abgleich mit eigenem Lauf)
| Manifest-Aussage | Mein Ergebnis | Übereinstimmung |
|---|---|---|
| 6 Service-Tests grün | 6 passed (14 assertions) | ✅ |
| 21 Golden grün | 21 passed (126) | ✅ |
| Volle Suite 635/1, nur Reverb-E4 | 635/1, Reverb-E4 | ✅ |
| Keine Migration/Route/View/Config | bestätigt (LEER) | ✅ |
| Golden-Diff vs f76b7dd LEER | bestätigt | ✅ |
| G3 `round`→`floor` → 1 rot | G-B: 1 failed | ✅ |
| G1 `×1.1` → 8 rot | G-A: **9 failed** (Unit+Feature-Scope zusammen) | ⚠️ Richtung identisch, Zähler-Differenz durch anderen Test-Scope (Manifest zählte offenbar nur eine Teilmenge). Kein Defekt. |

## 10. Mängel nach Schwere
- **Blocker:** keine.
- **Hoch:** keine.
- **Mittel:** keine.
- **Niedrig / operativ (Auflage):** Working-Tree enthält viele **unrelated untracked Docs** → Commit MUSS pfad-scoped erfolgen (nur die 5 P1b-1-Dateien + Manifest), niemals `git add -A`. Deckt sich mit Manifest §8.
- **Kosmetisch:** Gegenbeweis-Zähler G1 im Manifest (8) vs. eigener Lauf (9) — nur Scope-Zählweise, kein inhaltlicher Widerspruch.

## 11. Ballbesitz
Evaluator-Prüfung abgeschlossen → **Yama** (separate, pfad-scoped Commit-Freigabe). **Kein** Commit, **kein** Push, **keine** Fortsetzung mit P1b-2/3/4 oder P1c ohne eigenen Startblock.

---

Unabhängige Prüfung P1b-1 abgeschlossen. Urteil: GRÜN. Keine Produktivcodeänderung (außer restaurierten Gegenbeweisen), keine Migration, kein Commit, kein Push.
