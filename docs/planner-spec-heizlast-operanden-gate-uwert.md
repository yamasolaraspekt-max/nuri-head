# PLANNER-SPEC — Heizlast Operanden-Gate für fehlende U-Werte (ticket)

**Rolle:** Planner (kein Code) · **Datum:** 2026-07-18 · **Heimat-App: ticket.**
**Auslöser:** Evaluator-Spec-Review P2-2 (S2) — die Geometrie→Heizlast-Naht liefert für Bauteile ohne
belegten U-Wert still 0 Transmission, OHNE Warnung, und die Belastbarkeit bleibt fälschlich „belastbar".
Betrifft NICHT nur die Szene-Übernahme, sondern **auch die bestehende Grundriss-Linie** (gleicher Pfad).

---

## 0 · Ist-Beleg (grep, Code = Wahrheit)

- `GeometrieAbleitungService::opakeUQuelle` gibt ohne `u_wert`/`konstruktion_id` **nur** `['u_strategie'=>'C']`
  zurück — **kein** `u_wert`, **kein** `u_wert_datenlage`.
- `HeizlastRechner` (≈Z.85): `$u = (float)($b['u_wert'] ?? 0) + $deltaUwb;` → fehlender U-Wert ⇒ **0**, still.
- `AnforderungsprofilHeizlastAdapter::hinweise()` zählt Bauteile nur mit
  `u_wert_datenlage === 'importiert_ungeprueft'` **oder** `'geschaetzt'` → **`'fehlt'`/unbelegt wird NICHT gezählt**.
- `OfferReadinessService` (Z.325): `$eingabenUnsicher = $werte->has('ergebnis_hinweis')` → an
  `HeizlastBelastbarkeit::beurteile(...)`. `beurteile()` stuft `BELASTBAR` bei `$eingabenUnsicher`
  auf `EINGESCHRAENKT` herab (Z.76). **Der Herabstufungs-Mechanismus existiert bereits** — er wird nur
  für fehlende U-Werte nicht ausgelöst.

**Kern:** Es fehlt EIN Signal — „U-Wert unbelegt" — an genau zwei Stellen. Alles Weitere ist schon da.

---

## 1 · Ziel & Entscheidung (nicht verhandelbar für den Generator)

**Entscheidung: markieren + herabstufen, NICHT erfinden, NICHT hart verweigern.**

| # | Festlegung |
|---|---|
| **H1** | Ein opakes Bauteil, das auf `u_strategie='C'` OHNE belegten `u_wert` zurückfällt, trägt zusätzlich `u_wert_datenlage='fehlt'`. Rein additives Feld; die **HeizlastRechner-Formel bleibt byte-genau** (er liest weiter nur `u_wert`, 0 für fehlend). Kein erfundener U-Wert (kein stiller Ersatzwert — DAUERDIREKTIVE/Operanden-Gate). |
| **H2** | `AnforderungsprofilHeizlastAdapter::hinweise()` zählt zusätzlich Bauteile mit `u_wert_datenlage==='fehlt'` und schreibt einen Hinweis: „N U-Wert(e) fehlen (Standard C, unbelegt) — Transmission unvollständig". Der Hinweis landet wie gehabt in `ergebnis_hinweis`. |
| **H3** | Folge automatisch: `ergebnis_hinweis` gesetzt ⇒ `OfferReadinessService` setzt `eingabenUnsicher=true` ⇒ `HeizlastBelastbarkeit` stuft `BELASTBAR → EINGESCHRAENKT` (unverbindlich, reifegrad 0.6) mit sichtbarem Hinweis. **Keine Änderung** an `HeizlastBelastbarkeit` oder `OfferReadinessService` nötig. |
| **H4** | Die Heizlast-Zahl wird weiterhin ausgegeben (Pipeline läuft end-to-end), aber ehrlich als *nicht belastbar / unvollständig* gekennzeichnet — kein stilles „belastbar" mit 0-Transmission. |

**Verworfen:** (a) Norm-Standard-U je Baualtersklasse erfinden — braucht eine belegte Normbasis + Freigabe,
eigener späterer Posten; jetzt würde es Zahlen fingieren. (b) Harte Verweigerung (Exception wie
Pflicht-Werte) — würde die bestehende Grundriss-Linie brechen, die heute (unvollständige) Zahlen liefert.

---

## 2 · Nahtstellen (wo — und wo bewusst NICHT)

- **`GeometrieAbleitungService::opakeUQuelle`** (nur der `return ['u_strategie'=>'C']`-Zweig): `+ 'u_wert_datenlage'=>'fehlt'`.
  Der belegte Pfad (A: direkter U / Konstruktion) bleibt unverändert.
- **`AnforderungsprofilHeizlastAdapter::hinweise()`**: ein dritter Zähler `$fehlt` für `'fehlt'` + ein Hinweis-Satz.
- **NICHT angefasst:** `HeizlastRechner` (Formel byte-genau), `HeizlastBelastbarkeit`, `OfferReadinessService`,
  `fensterUQuelle` (Fenster ziehen U aus `fenster_typ` — eigener Prüf-/Folgeposten, s. Kante 4), keine Migration,
  keine DB-Struktur.

---

## 3 · Kantenliste

1. **Teils belegt, teils fehlend:** nur die unbelegten Bauteile zählen als `'fehlt'`; belegte bleiben unberührt. Ein einziges `'fehlt'` genügt für die Herabstufung.
2. **Bestehende Grundriss-Projekte (Regression, gewollt):** aktive Profile, deren Geometrie via Grundriss ohne U-Werte entstand, wechseln beim nächsten Lauf von „belastbar" auf „eingeschränkt" + Hinweis. Das ist die **Korrektur eines stillen Fehlers**, nicht ein Bruch — muss aber bewusst als Verhaltensänderung benannt und von Yama freigegeben werden.
3. **Voll belegte Projekte:** kein `'fehlt'`, kein neuer Hinweis, Belastbarkeit bleibt „belastbar" (kein Fehlalarm).
4. **Fenster/Türen:** `fensterUQuelle` liefert `fenster_typ` statt `u_wert`. Ob der Rechner daraus einen U-Wert auflöst oder ebenfalls 0 rechnet, ist getrennt zu prüfen; falls 0 → analoge `'fehlt'`-Markierung in einem Folge-Slice (nicht in diesem, um den Schnitt klein zu halten).
5. **decke/boden:** bleiben `null` (kein erfundener Aufbau) — sie erzeugen gar keine Bauteile, also keine `'fehlt'`-Zählung; ihre Unvollständigkeit ist über die separate decke/boden-Regel abzubilden.

---

## 4 · Abnahmekriterien (Evaluator, Gegen-Beweis)

1. **Markierung (H1):** `ausGeometrie` für eine Wand ohne U-Wert/Konstruktion → Bauteil trägt `u_strategie='C'` UND `u_wert_datenlage='fehlt'`. Belegte Wand (direkter U oder Konstruktion) → **kein** `'fehlt'`. HeizlastRechner-Ergebnis für dieselbe Eingabe **byte-genau wie vorher** (Formel unverändert).
2. **Zählung (H2):** Adapter-`hinweise()` gibt bei k fehlenden U-Werten den Hinweis „k U-Wert(e) fehlen …"; bei 0 fehlenden keinen solchen Hinweis.
3. **Herabstufung (H3, End-to-End):** Ein Objekt mit reiner Geometrie-Übernahme (alle U-Werte fehlend) → Belastbarkeit **EINGESCHRAENKT** (nicht `belastbar`), `verbindlich=false`, Hinweis nennt die fehlenden U-Werte. Ein voll belegtes Objekt → **BELASTBAR** unverändert. Gegen-Beweis: es darf **kein** Fall existieren, in dem fehlende U-Werte zu `belastbar` führen.
4. **Wächter:** volle Heizlast-Testsuite grün (selbst ausgeführt, `php artisan test --filter=Heizlast` + Anforderungsprofil/Offer-Tests); keine stille Änderung an belegten Bestandsprojekten.

---

## 5 · Arbeitspakete & Governance

- **U-a (rein, verifizierbar):** `opakeUQuelle` + `'fehlt'` + Unit-Test (ausGeometrie emittiert `'fehlt'`; Rechner-Ergebnis unverändert). Standalone-Harness-fähig.
- **U-b (Adapter-Zähler + Hinweis):** `hinweise()` erweitern + Feature-Test (Herabstufung End-to-End).
- **Stopp vor U-b:** Yama-Go wegen der **gewollten Verhaltensänderung an Bestandsprojekten** (Kante 2) — „belastbar" → „eingeschränkt" ist sichtbar für Nutzer.
- Rollentrennung: diese Spec = Planner; Bau = Generator (grep-first, additiv, `php -l`); Abnahme = Evaluator (eigener Testlauf + Gegen-Beweis); Freigabe der Verhaltensänderung = Yama. Heimat = ticket.

---

*Dieser Posten schließt die S2-Lücke des P2-2-Spec-Reviews an der Wurzel (Geometrie→Heizlast-Naht), damit
die Szene-Übernahme (P2-2a) UND die Grundriss-Linie eine ehrliche Belastbarkeit statt stiller 0-Transmission
liefern.*
