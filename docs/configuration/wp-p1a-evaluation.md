# EVALUATOR-BERICHT — WP Stufe 3b / P1a (Charakterisierung WP-Auslegung)

**Rolle:** unabhängiger read-only EVALUATOR (Generator ≠ Evaluator) · **Stand:** 2026-07-14
**Prüfgegenstand:** Golden-Master-Charakterisierung des HEUTIGEN WP-Auslegungsverhaltens vor P1b-Dienstextraktion.
**HEAD (Ist):** `f0055d643903e983a5e5a0bf55f03137a21f5375` == erwartet `f0055d6` ✔
**Umgebung:** eigener Lauf, MySQL `ticket_testing`, RefreshDatabase.

---

## VOTUM: FREIGABE-EMPFEHLUNG (mit einer dokumentarischen Auflage) — Yama ist finaler Freigeber

P1a ist eine saubere, wertgenaue Charakterisierung des Ist-Verhaltens. Kein Produktivcode geändert, keine
Migration, kein vorgezogener P1b-Scope. Alle eigenständig nachgerechneten Sollwerte stimmen mit den gepinnten
Golden-Werten überein. Einzige Auflage: eine kosmetische Doku-Inkonsistenz (Erfassungs-Commit im Test-Docblock),
kein Korrektheits- oder Scope-Mangel → **kein Veto-Grund**.

---

## 1. Git / Scope-Nachweis (eigener Lauf)

- `git rev-parse HEAD` → `f0055d6…` ✔ (== erwartet).
- `git status --short app/ database/ routes/ config/ resources/` → **LEER** ✔ → kein Produktivcode-, kein Migrations-, kein Route-/View-/Config-Diff.
- `git status --short tests/Feature/Energie/` → `?? tests/Feature/Energie/` → P1a-Tests **untracked, sauber getrennt** ✔.
- `git diff --stat CLAUDE.md` → `12 ++++++++++++` — Vorbestands-Drift der Governance-Datei, **nicht Teil von P1a** (keine der P1a-Dateien betroffen; die Änderung ist reines Doku-Vorbestand). Nicht dem Generator zuzurechnen.
- Übriger `git status` = ausschließlich untracked Doku (`docs/…`) + `tests/Feature/Energie/` — kein Produktivpfad angefasst.
- `grep -rl "WpCostingService\|WpFundingAssessmentService\|WpDocumentService" app/` → **leer** (Exit 1) ✔ → keine vorgezogene Dienstextraktion.

**Urteil:** kein Produktivcode-Diff, keine Migration, P1a klar isoliert. ✔

## 2. Baseline-Verifikation (committed Zustand ohne P1a)

- Vorbereiteter Wegwerf-Worktree existiert: `…/scratchpad/baseline-f0055d6`, `git worktree list` bestätigt `f0055d6 (detached HEAD)`, HEAD dort `f0055d6…` ✔.
- `ls tests/Feature/Energie` im Worktree → **No such file or directory** ✔ → committed Baseline enthält P1a NICHT.
- `php artisan test` im Worktree → **608 passed, 1 failed (2113 assertions), 32,09 s** ✔ (== Manifest).
- Einziger Rotfall Baseline: `Tests\Feature\Invoice\InvoiceDeletionGuardTest` → `BroadcastException / Pusher error: cURL error 7: … localhost port 6001` = bekannter **Reverb-E4** (kein WP-Bezug, kein Produktivcode-Diff) ✔.

**Urteil:** saubere committed Baseline unabhängig reproduziert; einziger Rotfall ist der umgebungsbedingte Reverb-E4. ✔

## 3. Messen die Tests das IST-Verhalten (Golden Master)?

Ja. Belege aus dem gelesenen Testcode:
- **Echte Controller-Methoden:** `WpAuslegungCharakterisierungTest` ruft `EnergieAuslegungController::wpBerechnen` / `wpDokument` direkt auf und liest das View-Model `getData()['ergebnis']`; `EnergiekonzeptWpCharakterisierungTest` ruft `EnergiekonzeptController::berechnen` (→ `baueKonzept`/`baueWp`) und liest `getData()['konzept']['wp']`; `HeizlastCharakterisierungTest` ruft `HeizlastController::berechnen` und liest `ergebnis`/`wp`. Die Blade wird bewusst NICHT gerendert.
- **Keine erfundenen Sollwerte, kein Ranking als Ist:** Es werden ausschließlich die vom Controller erzeugten Werte gegen zuvor erfasste Zahlen gepinnt. `WpAuslegungsketteService`-Rankingwerte tauchen in den Tests NICHT als Erwartung auf.
- **Rundung explizit geprüft:** `jaz` auf 2 NK (`assertSame(2.9, …)`), `q_heiz_kwh`/`q_ww_kwh`/`strom_kwh`/`stromkosten_jahr` ganzzahlig (`assertSame(6483.0, …)` etc.). Deckt sich mit `wpErgebnis` (`round($jaz,2)`, `round($qHeizKwh)`, `round($stromKwh)`, `round($stromkostenJahr)`).
- **Keine externen Netzabhängigkeiten:** nur DB-Seeds (`brands`/`products`/`product_heat_pump_specs`) + reine Rechenpfade; kein HTTP/Broadcast.

**Urteil:** echter Golden Master gegen `f0055d6`. ✔

## 4. Eigene Nachrechnungen (unabhängig, aus dem Code, NICHT aus dem Manifest)

Quellen selbst gelesen: `HeizlastKonstanten::B_VH_DEFAULT = 2000` (Zeile 13), `WW_KWH_PA['normal'] = 700` (Zeile 74),
`JAZ['luft_wasser']['hk'] = 2.9` (Zeile 92), `WarmwasserService::qWwKwh = personen × wwKwhPa` (Zeile 27-30),
`JazService::stromverbrauch = (qHeiz + (ww_mit_wp ? qWw : 0)) / jaz` (Zeile 27-33),
`FoerderungService::deckel(1) = 30000` (Zeile 42-53), `wpErgebnis` (Zeile 360-461).

| Prüfwert | eigene Rechnung | Test-Erwartung | Deckung |
|---|---|---|---|
| q_heiz @ heizlast 4.0 | 4.0 × 2000 = **8000** | 8000.0 | ✔ |
| q_heiz @ heizlast 15.0 | 15.0 × 2000 = **30000** | 30000.0 | ✔ |
| q_ww @ 1 Person | 1 × 700 = **700** | 700.0 | ✔ |
| q_ww @ 6 Personen | 6 × 700 = **4200** | 4200.0 | ✔ |
| strom @ Normalfall | (16000+2800)/2.9 = 6482,76 → **6483** | 6483.0 | ✔ |
| strom @ ww_mit_wp=false | 16000/2.9 = 5517,24 → **5517** | 5517.0 | ✔ |
| strom @ heizlast 4.0 | 10800/2.9 = 3724,14 → **3724** | 3724.0 | ✔ |
| strom @ 6 Personen | 20200/2.9 = 6965,52 → **6966** | 6966.0 | ✔ |
| Förder-Deckel(1) | **30000** | 30000 | ✔ |
| Förderung invest 50000 (selbstbewohnt, gas/25 J) | foerderfaehig=min(50000,30000)=**30000**; Satz Grund 30+Klima 20=50 %; zuschuss=round(30000×50/100)=**15000** | foerderfaehig 30000, zuschuss 15000 | ✔ |
| Förderung Normalfall invest 25000 | zuschuss=round(25000×50/100)=**12500**; eff=12500/25000=**50,0 %**; netto **12500** | 12500 / 50.0 / 12500 | ✔ |
| Förderung ohne Klima (heizungsart „keine") | Satz nur 30 %; zuschuss=round(25000×30/100)=**7500**; netto **17500** | 7500 / 30.0 / 17500 | ✔ |
| Förderung Effizienz+Einkommen | einkommensbonus→we_unter_40k=min(1,1)=1; Satz min(70, 35+20+30)=**70 %**; zuschuss=round(25000×70/100)=**17500**; netto **7500** | 17500 / 70.0 / 7500 | ✔ |

**Geräte-Unabhängigkeit (A1) im Code verifiziert:** In `wpErgebnis` (Zeile 360-461) fließt `$hp` NUR in den
Anzeigeblock `wp` (hersteller/modell/scop/heizleistung …). `jaz`, `q_heiz_kwh`, `strom_kwh`, `stromkosten_jahr`,
`investition_netto`, `foerderung` werden ausschließlich aus Formular-Operanden + Konstanten berechnet — `$hp` geht
in KEINE dieser Zahlen ein. Damit misst `test_geraetewahl_aendert_nur_anzeige_nicht_die_zahlen` ein **echtes,
fachlich fragwürdiges Ist** (SCOP/Leistung des Geräts wirken nicht auf JAZ/Strom), keinen Testfehler. Das Manifest
kennzeichnet dies unter §9 **A1** korrekt als dokumentiertes Altproblem und verschiebt die Behebung ausdrücklich
auf P1c/Orchestrator — **ohne es in P1a/P1b zu „verbessern"**. Korrekt gehandhabt. ✔

**Gegenprobe fokussiert:** `php artisan test tests/Feature/Energie/` → **21 passed (126 assertions)** ✔.

**Urteil:** Alle 4+ selbst nachgerechneten Sollwerte deckungsgleich mit den Golden-Werten. Kein erfundener Wert. ✔

## 5. Nicht-Scope-Treue

- Keine Service-Extraktion: `WpCostingService`/`WpFundingAssessmentService`/`WpDocumentService` existieren nicht (grep leer).
- Keine Orchestrator-/Ranking-Verdrahtung in den Controllern (kein Produktivcode-Diff; `git status app/` leer).
- Keine neue View/Route/Migration/`auslegung_ergebnis` (git status resources/routes/database leer).
- Testzugriff erfolgt über öffentliche Controller-Methoden + View-Model — **keine Testbarkeits-Änderung am Produktivcode nötig** (belegt: `app/` leer).

**Urteil:** nichts aus P1b/P1c vorgezogen. ✔

## 6. Charakterisierungs-Qualität / Matrixabdeckung

Die Tests prüfen mehr als „Array existiert": konkrete numerische Sollwerte, Rundungsstellen, plus Fehlerfälle
(`ValidationException` bei fehlendem Pflichtfeld in WP + Heizlast; `wp_index` ohne Gerät → `ergebnis`/`konzept.wp`
null mit Fehlermeldung „nicht gefunden"). Gedeckte Kernkette: Bedarf (q_heiz), Rundung, WW (Personen + ww_mit_wp),
Verbrauchs-Plausi (additiv, Kernrechnung unverändert), Kosten/Invest, Förderung (Grund/Klima/Effizienz/Einkommen),
Förder-Deckel, Produktwahl-Fehler, Geräte-Unabhängigkeit, Dokument = Rechenwahrheit, Energiekonzept-Parität, nur-WP-Gesamt,
Heizlast-Ergebnis + WP-Matchblock + Transienz. **Kernkette vollständig gepinnt.**

**Abdeckungslücken (nur Hinweise, KEINE Blocker — Kernkette ist gedeckt):**
- WW-Komfort ≠ „normal": Badewanne/„hoch" (1000 kWh/Person) und `speicher_liter` mit Badewanne (90 statt 50) nicht gepinnt.
- JAZ nur `luft_wasser`/`hk` (2.9) gepinnt; `fussbodenheizung` (fbh 4.0, vorlauf 35), `beides` sowie `sole_*`/`wasser_wasser` ungetestet.
- Förderung Mehrfamilien-Fall (`anzahl_we` > 1, Vermietet-/Selbstnutzer-Split, Deckel-Staffel > 1 WE) nicht gepinnt.
- Heizlast-Numerik nur ein Gebäudefall (4.35 kW); Detail-Numerik laut Manifest zusätzlich durch committete `HeizlastRechner`-Referenztests gedeckt (plausibel, nicht Teil dieser 21).

Diese Fälle stammen aus der Matrix-Peripherie (§5-Varianten), nicht aus der tragenden Extraktions-Kette für P1b.
Für P1b ist die byte-/wertgleiche Absicherung der Kosten-/Förder-/Dokument-Pfade das Ziel — und genau die ist gepinnt.

## 7. Manifest-Wahrheitsprüfung

| Manifest-Aussage | eigener Befund | Deckung |
|---|---|---|
| Baseline 608 passed / 1 failed | 608 / 1 (Reverb E4) | ✔ |
| Fokus `tests/Feature/Energie/` = 21 passed | 21 passed (126 assertions) | ✔ |
| Gesamt MIT P1a = 629 passed / 1 failed | 629 passed / 1 failed (2239 assertions) | ✔ |
| Delta Baseline→Gesamt = +21 (genau P1a) | 629 − 608 = 21 | ✔ |
| einziger Rotfall = Reverb E4, unverändert | in Baseline UND Gesamt derselbe `InvoiceDeletionGuardTest` / `localhost:6001` | ✔ |
| „kein Produktivcode geändert" | `git status app/ database/ routes/ config/ resources/` leer | ✔ (belegt) |

**Urteil:** alle Manifest-Zahlen mit eigenem Lauf reproduziert; „kein Produktivcode geändert" belegt. ✔

## 8. Mängel nach Schwere

- **Blocker:** keine.
- **Auflage (kosmetisch/Doku):** Der Docblock in `WpAuslegungCharakterisierungTest` (Zeile 24) nennt als Erfassungs-Commit
  `a00bb0a`, während Manifest/HEAD `f0055d6` sind. Die Tests laufen auf `f0055d6` grün → **kein Korrektheitsmangel**;
  nur eine Herkunfts-Referenz sollte vor Commit auf `f0055d6` vereinheitlicht werden. (READ-ONLY: nicht korrigiert, nur gemeldet.)
- **Hinweis:** Matrix-Peripherie-Lücken aus §6 (WW-Komfort-Varianten, weitere JAZ-Klassen, Mehr-WE-Förderung) — optional in
  P1a nachziehbar oder bewusst P1b/P1c überlassen; kein Blocker, da Kernkette gepinnt.
- **Hinweis (nicht P1a-verursacht):** `CLAUDE.md` lokale Vorbestands-Drift (+12 Zeilen) und Reverb-E4-Rotfall bestehen unabhängig fort.

## 9. Ballbesitz

Unabhängige Evaluation abgeschlossen → **Yama** (finaler Freigeber vor Commit). **Kein Commit, kein Push, keine
Fortsetzung mit P1b durch den Evaluator.** Empfehlung: vor dem Commit lediglich die Docblock-Herkunftsangabe
`a00bb0a`→`f0055d6` angleichen (Generator-Aufgabe, keine Logikänderung).

---

**Unabhängige Prüfung P1a abgeschlossen. Urteil: FREIGABE-EMPFEHLUNG mit einer kosmetischen Doku-Auflage (Erfassungs-Commit im Test-Docblock). Keine Produktivcodeänderung, keine Migration, kein Commit, kein Push.**

---

## Nachtrag: Post-Eval-Docblock-Fix bestätigt (Re-Prüfung, 2026-07-14)

Der Generator hat die unter §8 verlangte kosmetische Auflage umgesetzt. Re-Prüfung read-only durchgeführt
(Yama-Regel: Änderung nach Evaluatorprüfung → erneute Evaluatorprüfung).

- **Delta = ausschließlich die Docblock-Kommentarzeile:** `WpAuslegungCharakterisierungTest.php` Zeile 24 nennt jetzt
  „gegen den unveränderten Stand **f0055d6** erfasst"; `grep a00bb0a tests/Feature/Energie/` → **kein Treffer mehr** (Exit 1).
  Reiner Kommentar, keine Test-/Rechenlogik berührt.
- **Kein weiterer Diff:** `git status --short app/ database/ routes/ config/ resources/` → **LEER**; einziger tracked Diff bleibt
  `CLAUDE.md` (+12, Vorbestands-Drift, nicht P1a). Die 3 Testdateien weiterhin untracked (`?? tests/Feature/Energie/`),
  Umfang unverändert plausibel (WpAuslegung 266 / EnergiekonzeptWp 121 / Heizlast 93 Zeilen).
- **Testläufe unverändert grün:** `php artisan test tests/Feature/Energie/` → **21 passed (126 assertions)**;
  volle Suite → **629 passed, 1 failed (2239 assertions)**, einziger Rotfall weiterhin Reverb-E4
  (`InvoiceDeletionGuardTest`, `localhost:6001`), nicht P1a-verursacht.

**Urteil bleibt: FREIGABE-EMPFEHLUNG** — die einzige Auflage ist damit erledigt; es steht keine offene Auflage mehr aus.
**Ballbesitz: Yama** (finale Commit-Freigabe). Kein Commit, kein Push durch den Evaluator.
