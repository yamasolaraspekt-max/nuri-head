# MEILENSTEINPLAN V3 — Planungsmodell und Meilensteine (ENTWURF, Dirigent, 22.08.2026 13:50)

```yaml
status: "ENTWURF — Planungsmodell (Begriffe, Pflichtfelder, Erreicht-Regeln) + Meilensteine M0..M3 für GESAMTKONZEPT V3. Termine sind ZIELE, keine Zusagen (Bewertung 22.08. — Tempo schwankt um Faktor 9); jedes Ziel hat eine messbare Erreicht-Bedingung."
gehoert_zu: docs/konzept/gesamtkonzept-v3-bedienweg-zuerst.md
anlass: "Yama 22.08. 13:4x: 'wir brauchen ein Konzept, dann Meilensteine (was wollen wir wann erzielen); Meilensteine bestehen aus Aufträgen; Aufträge haben Aufgaben; Aufgaben bestehen aus Kriterien; ein Auftrag hat ein Ziel — diese Schritte sauber formulieren'"
mess_sha: Integration fafcc882
```

## 1 · Planungsmodell — fünf Ebenen, jede mit Pflichtfeldern

```
KONZEPT  ──▶  MEILENSTEIN  ──▶  AUFTRAG (Blatt)  ──▶  AUFGABE  ──▶  KRITERIUM
 warum          was bis wann       ein Ziel, eine Lieferung   ein Schritt,        eine prüfbare
 (Ursache,      (messbar,          (Spur, Bedienweg,          eine Rolle,         Aussage mit
 Mechanismus)   Erreicht-wenn)     Abnahme)                   ein Ergebnis        Messbefehl, Rot/Grün
```

| Ebene | Pflichtfelder | Erreicht, wenn | Wer schreibt | Wo liegt es |
|---|---|---|---|---|
| **Konzept** | Ziel-Satz · Ursachen (gemessen) · Mechanismus je Ursache · Besitzer · Messgrößen · Stopp-Auslöser · Rückweg · Kanten | alle Meilensteine erreicht **und** die Messgrößen stehen zwei Lageberichte lang ohne Stopp | Dirigent (Entwurf), Yama (Freigabe) | `docs/konzept/` → Regeln ins Regelwerk |
| **Meilenstein** | Kennung `M<n>` · Ziel (ein Satz) · Termin (Ziel) · Erreicht-wenn (Messgrößen mit Zahl) · Aufträge (Liste mit Kennung) · Besitzer · Risiko/Abhängigkeit | **alle** Aufträge ABGENOMMEN (bei Produkt: BROWSER) **und** Erreicht-Bedingung gemessen | Dirigent | dieses Blatt; Zustand je Auftrag in `docs/STATUS.md` |
| **Auftrag** (Blatt) | Kennung · **Ziel (ein Satz, prüfbar)** · Spur A/W (Halbsatz) · **N4 Bedienweg** · Aufgaben (nummeriert, je Rolle) · **N3 Nachvollzugs-Matrix** (je Kriterium eine Zeile) · Rückweg & Entdeckung · Heimat-App · DoR-Beleg · Abnahme-Beleg | DoR ERTEILT → CODE_FERTIG → **ABGENOMMEN** (Reifegrad genannt) → Zustandscommit aus dem Ereignis | Planner (Blatt), Plan-Prüfer (DoR), Generator (Bau), Evaluator (Votum), Integrator (Zustand) | `docs/auftraege/aktiv/<Kennung>-*.md` + Block in STATUS.md |
| **Aufgabe** | Nummer `<Kennung>-A<n>` · Rolle · Eingabe (SHA/Pfad) · Ergebnis (Pfad/SHA) · **welche Kriterien sie erfüllt** (Liste) · Verboten | ihre Kriterien sind grün **und** das Ergebnis liegt als SHA vor | Planner (im Blatt) | Abschnitt „Aufgaben" des Blatts |
| **Kriterium** | Nummer `<Kennung>-<n>` · eine prüfbare Aussage · **Messbefehl (zitierbar, mit Ort: Probe-Root/Test-DB/Browser)** · Rot-Lage (Basis-SHA) · Grün-Erwartung · Belegform (SHA + Pfad + Anker, Rohausgabe) · Reifegrad-Anteil (CODE / PRODUKTWEG / BROWSER) | Evaluator misst Grün am Endstand, selbst ausgelöst; Rot am Basis-SHA belegt | Planner | Matrixzeile im Blatt |

**Vier Regeln, die das Modell zusammenhalten:**
1. **Jede Ebene zeigt auf die nächste und zurück:** ein Kriterium gehört zu genau einer Aufgabe, eine Aufgabe zu genau
   einem Auftrag, ein Auftrag zu genau einem Meilenstein (ein zweiter Meilenstein darf ihn *abhängig* nennen, nicht
   *besitzen*). Verwaiste Kriterien (ohne Aufgabe) oder Aufgaben ohne Kriterium sind ein DoR-Mangel.
2. **Ein Ziel ist erreicht, wenn alle seine Kriterien grün sind — nicht, wenn der Bau fertig ist.** CODE_FERTIG ist
   Zwischenstand; Meilenstein-Zählung nimmt nur ABGENOMMEN (Produkt: BROWSER).
3. **Kein Termin ohne Erreicht-Bedingung, keine Erreicht-Bedingung ohne Messbefehl.** Termine sind Ziele; verfehlte
   Termine werden im Lagebericht genannt, nicht verschoben ohne Grund.
4. **Was kein Kriterium hat, wird nicht gebaut.** (Bestehende Regel, hier als Klammer: Beleg statt Behauptung.)

## 2 · Meilensteine für V3

### M0 — „Das Haus kann sicher bauen" (heute, 22.08.) — Ziel: die Werkstatt ist dicht, die Statuswahrheit erreichbar
- **Erreicht wenn:** nackter `git commit` scheitert in 7/7 Bäumen (**✔ 12:59, Evaluator 16613211**) · A-37 ABGENOMMEN (**✔**) ·
  A-43 Posten 1+2 ABGENOMMEN (**✔ 13:3x**) · Z-Zustandscommits möglich (**läuft: Integrator gen 10, elf Posten**) ·
  Anschluss-Vorlage liegt (**läuft: Planner gen 15**).
- **Aufträge:** A-37 ✔ · A-43 (Posten 3 Nachlieferung läuft: Generator gen 12 → Evaluator) · INT-zustand-z-welle · KONZEPT-planner-anschluss.
- **Besitzer:** Dirigent. **Risiko:** Tor-Wörter befristet (README 6f) — Rücknahme nach Hinweg aller Rollen auf den A-43-Stand (heute).

### M1 — „Die fünf Z1 sind abgenommen, die Anschlussentscheidung ist getroffen" — Ziel bis **24.08.**
- **Erreicht wenn:** Z1-W1-1..5 je Votum ABGENOMMEN (W1-1/W1-2 mit Browser) · Yama hat je Anschlusspaket entschieden
  (anschließen/parken/verwerfen) · Werkzeug-Register (V3-2) als Tabelle mit Seed committet · Y-13 GRANT vorhanden
  (`SHOW GRANTS` zeigt `ticket_testing_%`) · Z0-I1 ABGENOMMEN (Test-DB-Isolation: vier DBs, `TEST_ROLLE` Pflicht,
  Kollisionsprobe) · Tor-Wörter zurückgenommen (aktion = Rollenverb).
- **Aufträge (Ziel je Auftrag, Aufgaben, Kriterien — Kurzform; Blätter tragen die Matrix):**
  - **Z1-W1-5 Nachprüfung** (Evaluator gen 11) — Ziel: der eine Mangel aus a4144ff4 ist am Integrationsstand behoben. Aufgaben: A1 Evaluator misst rot/grün; Kriterien: Blatt Z1-W1-5 unverändert + Gegenprobe (Tests, tsc, Produktcode-Diff).
  - **Z1-W1-1 / Z1-W1-2 Browserabnahme** (Evaluator, nach Y-13) — Ziel: Kriterium C bzw. E im Browser belegt (Badge-Vorbehalt sichtbar; Walmdach-Ablehnung mit sichtbarer Meldung). Aufgaben: A1 Test-DB bereit (Z0-I1), A2 Browserlauf (Puppeteer, headful), A3 Votum. Kriterien: die offenen aus d40adbf5 / 27143f96.
  - **Z0-I1 Test-DB-Isolation** (Planner PARKED_DRAFT → DoR → Generator → Evaluator) — Ziel: Tests laufen nur gegen eindeutig benannte Test-DBs, parallel je Rolle. Aufgaben: A1 GRANT (Yama), A2 Blatt aus PARKED_DRAFT schneiden, A3 Bau, A4 Kollisionsprobe. Kriterien: vier DBs, `TEST_ROLLE` Pflicht, `SELECT DATABASE()` je Lauf, Kollisionsprobe rot→grün, Produktiv-DB unberührt.
  - **Werkzeug-Register (V3-2)** (Planner, Konzept→Regel) — Ziel: jede Registerzeile hat Modul + Kennung + Reifegrad. Aufgaben: A1 aus Anschluss-Vorlage, A2 Lücken als „parken/verwerfen" markieren. Kriterien: 37/37 Zeilen mit Kennung oder Ausnahme; 0 Module ohne Zeile.
  - **Anschlussentscheidung** (Yama) — Ziel: je Paket ein Wort. Aufgaben: A1 Vorlage lesen, A2 entscheiden. Kriterium: Entscheidung als Datei `auftraege/ANSCHLUSS-entscheidung-yama.md` mit Datum.
  - **Tor-Wörter zurücknehmen** (Dirigent) — Ziel: `aktion` = Rollenverb in 7/7 Rollenquellen; Kriterium: Commit je Rolle durch das Tor mit dem Verb (Evaluator-Stichprobe).
- **Besitzer:** Dirigent; Yama für GRANT + Anschluss. **Risiko:** Y-13 (nur Yama); Evaluator-Kapazität.

### M2 — „Erste Anschlusswelle bedienbar" — Ziel bis **29.08.**
- **Erreicht wenn:** ≥ 3 Anschlusspakete `ABGENOMMEN (BROWSER)` (Spur W) · Bedienweg-Quote der Woche > 50 % ·
  Module ohne Ladeweg = nur noch die von Yama geparkten · STATUS.md: 0 Handänderungen ohne Erzeuger-Marke seit M1 ·
  BEREIT-Vorrat ≤ 6 · ältestes CODE_FERTIG < 24 h · Abnahmerückstand (12) auf 0 (je Votum oder datierte Entscheidung).
- **Aufträge:** Anschluss-Blätter je Paket (Spur W; N4 Pflicht; Kriterien: Werkzeug in `toolRegistry`, Auslöser im
  Browser, Ergebnis sichtbar, Rot-Probe ohne Werkzeug, Browserabnahme) · Abnahmerückstand Posten 2–12 (Evaluator,
  Risikoreihenfolge; W0-7, W0-3, W0-1, W0-8, W0-10, W0-11, W0-12, W1-1, W1-2, A-38) · Z2-W0-5b (`linked`-Wache) ·
  Regelbau „Erzeuger-Marke STATUS.md" (Spur A, klein) · Berichtsregel V3-8 (sechs Messgrößen).
- **Besitzer:** Planner (Blätter), Evaluator (Abnahmen), Integrator (Zustände), Dirigent (Deckel/Reihenfolge). **Risiko:** Abnahmekapazität → zweite Evaluator-Sitzung nur auf Yamas Entscheidung.

### M3 — „Der Apparat trägt sich selbst" — Ziel bis **05.09.**
- **Erreicht wenn:** Dispatcher Z0-I4 ABGENOMMEN (12 Pflichtproben), alle Rollenwecker entfernt · Z0-I2/Z0-I3
  (Claim/Identität an Lease/Fencing) ABGENOMMEN · Werkstatt/Produkt ≤ 1:2 über die Woche · Berichtigungsquote ≤ 15 %,
  Zweit-Ebene 0 · die sechs Messgrößen stehen zwei Lageberichte lang ohne Stopp → Konzept V3 gilt (Regeln bei Yama).
- **Aufträge:** Z0-I2, Z0-I3, Z0-I4 (Spur A) · zweite Anschlusswelle (Spur W) · Golden-Path GP-0..GP-3 Entscheidung (Yama) · ARBEITSREGELN-Fassung mit V3-1..V3-8 (Yama).
- **Besitzer:** Dirigent; Yama (Regeln). **Risiko:** Regelbau-Maximum vier; Dispatcher-Risiken R1–R6 aus der Z0-I4-Vorgabe.

## 3 · Wie ein Auftrag ab jetzt aussieht (Vorlage, kurz)

```
Kennung · Titel
ZIEL (ein Satz, prüfbar): …
MEILENSTEIN: M1   SPUR: W (Bedienweg benannt; kein Rechte/Geld/DB/Auth; ≤ 8 Kriterien; Revert möglich)
N4 BEDIENWEG: toolRegistry-Kennung · Menü/Route · Auslöser · Zielreifegrad BROWSERABGENOMMEN
AUFGABEN:
  A1 (Planner)   Blatt + Matrix            → erfüllt K1..K8 strukturell
  A2 (Generator) Bau in EINER Lieferung     → K1..K6 grün am Endstand
  A3 (Evaluator) Rot/Grün + Browserlauf     → K7 (Browser), K8 (Rot-Probe ohne Werkzeug)
  A4 (Integrator) Zustand aus dem Ereignis  → Zustandscommit mit Reifegrad
KRITERIEN (N3-Matrix): K<n> · Aussage · Messbefehl (Ort!) · Rot @Basis · Grün-Erwartung · Belegform · Reifegrad
RÜCKWEG: Revert <SHA>; ENTDECKUNG: Werkzeug fehlt in Leiste / Test rot
HEIMAT: Hausplaner-Insel (resources/js/…)
```

## 4 · Was dieses Blatt nicht tut
Keine Regel (Yama) · keine Termine als Zusage · keine Produktentscheidung (Anschluss = Yama) · kein Bau.
