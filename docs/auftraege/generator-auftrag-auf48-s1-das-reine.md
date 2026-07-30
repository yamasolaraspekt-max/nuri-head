# AUF-48 SCHEIBE 1 — Das Reine aus `HausplanerApp.tsx` herauslösen

```yaml
auftrag:
  id: AUF-48-S1
  titel: "Reine Hilfsfunktionen in ein eigenes Modul, drei tote Konstanten fallen"
  status: aktiv
  spur: B
  heimat: ticket
  rolle: generator
  angelegt: "2026-07-30 18:45 CEST"
  grundlage: "docs/planner/zuschnitt-auf48-hausplanerapp-zerlegen.md §7 — Anker ueber NAMEN"
  ziel: >
    Die sieben reinen Funktionen ohne React-Zustand wandern in ein eigenes Modul; die drei
    nachweislich toten Stilkonstanten fallen. HausplanerApp.tsx wird kuerzer, ohne dass sich
    ein einziges Verhalten aendert.
  nicht_ziel: >
    KEINE Verhaltensaenderung. Keine Umbenennung. Keine der 78 Inline-Stellen (Scheibe 7).
    Kein Anfassen der Layout-Rechnung. Scheibe 2, 3 und 4 bleiben unberuehrt.
```

## Warum diese Scheibe zuerst

**Sie ist die einzige, die nichts wissen muss.** Die sieben Funktionen hängen an keinem `useState`,
keinem `useEffect` und keinem Kontext — sie nehmen Werte und geben Werte zurück. **Damit ist der
Gegenbeweis billig:** wenn sich ein Verhalten ändert, war die Verschiebung falsch, und das sieht
man sofort an 28 bestehenden Zusagen.

## Bestand, gemessen

```yaml
measurement:
  observed_at_commit: 8e7c57b9
  observed_at: "2026-07-30 18:43 CEST"
  freshness_rule: "Weicht HEAD ab, alle Befehle vor Baubeginn neu fahren. Zeilenzahl aendert
                   sich staendig — sie ist ein Umfangsmass, KEINE Schnittkante (PB-007)."
  werte:
    - id: M-01
      command: "git show <commit>:resources/planner/hausplaner/app/HausplanerApp.tsx | wc -l"
      observed_value: 2511
      purpose: "Umfang der Datei — am 30.07. 09:05 waren es 2308, sie waechst taeglich"
    - id: M-02
      command: "git show <commit>:...HausplanerApp.tsx | grep -c 'navGrp'"
      observed_value: 1
      purpose: "nur die Definition — kein Gebrauch. Gilt ebenso fuer navHub (1) und navSub (1)"
    - id: M-03
      command: "git show <commit>:...HausplanerApp.tsx | grep -c 'navItem'"
      observed_value: 2
      purpose: "Definition PLUS eine Verwendung — navItem BLEIBT, es ist nicht tot"
```

**`navGrp`, `navHub`, `navSub` haben je genau ein Vorkommen: ihre eigene Definition.**
*Der Prüfer hat das unabhängig mit einem anderen Werkzeug bestätigt — zwei Messungen, dasselbe
Ergebnis.* **`navItem` sieht ähnlich aus und ist es nicht.**

## Umfang

```yaml
scope:
  schreiben:
    - resources/planner/hausplaner/app/reineHelfer.tsx         # NEU
      # KORRIGIERT 30.07., 19:07 — im Blatt stand `.ts`. Das war MEIN Fehler:
      # svgWrap, werkzeugIcon und opIcon geben React-Elemente zurueck und enthalten
      # JSX — eine `.ts`-Datei uebersetzt das nicht. Der Generator hat `.tsx`
      # angelegt und damit richtig gehandelt. KEINE Scope-Abweichung.
    - resources/planner/hausplaner/app/HausplanerApp.tsx       # nur Entnahme + Import
    - resources/planner/hausplaner/__tests__/reineHelfer.test.ts  # NEU
  anker_der_scheibe:
    - "function svgWrap"
    - "function werkzeugIcon"
    - "function opIcon"
    - "const uuid"
    - "function istWand"
    - "function istOeffnung"
    - "function lotAufWand"
    - "const navGrp   — ENTFAELLT"
    - "const navHub   — ENTFAELLT"
    - "const navSub   — ENTFAELLT"
  ausschluss:
    - pfad: "alles ab `export function HausplanerApp`"
      grund: "Scheiben 2 bis 4 — abgeleitete Werte, Tasten, JSX"
      entschieden_von: "Planner, 30.07., 18:45 (Zuschnitt §7)"
    - pfad: "const navItem"
      grund: "hat eine Verwendung, ist nicht tot"
      entschieden_von: "Planner, 30.07., 18:45"
```

**`KontextOptionenLeiste` bleibt, wo sie ist** — sie ist eine Komponente, keine reine Funktion.

## Geerbte Zusagen — die Liste, nicht die Zahl (R9-Barriere)

**28 Testdateien lesen `HausplanerApp` ein.** Der vollständige Befehl steht in K-03; die Liste ist
zu lang für dieses Blatt, **aber sie wird gefahren, nicht geschätzt**:

```text
git grep -l 'HausplanerApp' HEAD -- 'resources/planner/hausplaner/__tests__/*' \
                                    'resources/planner/hausplaner/__domtests__/*'
```

*Darunter `stilschicht.test.ts` — sie prüft Stilkonstanten und ist der wahrscheinlichste Kandidat,
der beim Entfernen der drei toten Konstanten anschlägt. **Schlägt sie an, ist das kein Fehler des
Auftrags, sondern der Beweis, dass die Zusage greift** — dann melden, nicht die Zusage anpassen.*

## Abnahmekriterien

```yaml
kriterien:
  - id: K-01
    aussage: "Die sieben reinen Funktionen stehen in reineHelfer.ts und nirgends sonst."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c 'function svgWrap\\|function werkzeugIcon\\|function opIcon\\|const uuid\\|function istWand\\|function istOeffnung\\|function lotAufWand' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "0 — keine der sieben Definitionen bleibt zurueck"
    gegenbeweis: >
      Eine Definition zurueckkopieren — der Befehl muss dann 1 melden. **Und `tsc` muss den
      doppelten Namen erkennen**; tut er es nicht, ist der Import falsch verdrahtet.

  - id: K-02
    aussage: "Die drei toten Konstanten sind fort, navItem ist geblieben."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "grep -c 'navGrp\\|navHub\\|navSub' resources/planner/hausplaner/app/HausplanerApp.tsx && grep -c 'navItem' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "0 und danach 2"
    gegenbeweis: "navItem mitentfernen — der zweite Befehl meldet 0, und die Oberflaeche verliert eine Zeile."

  - id: K-03
    aussage: "Keine der geerbten Zusagen faellt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Testzahl vorher und nachher gemeldet; keine Datei rot, die vorher gruen war"
    gegenbeweis: >
      Eine der sieben Funktionen beim Verschieben absichtlich veraendern (z. B. `lotAufWand`
      Vorzeichen drehen) — mindestens eine der 28 Dateien muss rot werden. **Wird keine rot,
      ist die Flaeche unverriegelt, und das ist ein meldepflichtiger Nebenbefund.**

  - id: K-04
    aussage: "Die Datei ist kuerzer geworden, und zwar nur durch Entnahme."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "git diff --numstat <basis> HEAD -- resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "Loeschungen deutlich groesser als Einfuegungen; Einfuegungen sind nur die import-Zeile"
    gegenbeweis: "Kommt Logik hinzu, ist es keine Zerlegung mehr, sondern ein Umbau."
```

## Betrieb

**Fassung B:** committen auf `auto/hausplaner-integration`, **Basis-SHA und Generator-SHA melden**,
niemals nach `main` mergen, niemals pushen, nur eigene Pfade stagen.

**Wenn beim Verschieben auffaellt, dass eine der sieben Funktionen doch Zustand liest:** nicht
mitnehmen, **melden.** Dann gehoert sie in Scheibe 2 und der Zuschnitt ist an der Stelle falsch.
