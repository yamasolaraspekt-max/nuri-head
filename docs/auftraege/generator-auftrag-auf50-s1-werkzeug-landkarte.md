# AUF-50 STUFE 1 — Die Werkzeug-Landkarte

```yaml
auftrag:
  id: AUF-50-S1
  titel: "Je Werkzeugvertrag eine Marke: deckt | fehlt | ohne-modell | stillgelegt"
  status: aktiv
  spur: B
  heimat: ticket
  rolle: generator
  angelegt: "2026-07-30 10:12 CEST"
  grundlage: "docs/planner/stufenplan-auf50-werkzeuge-funktionstuechtig.md §3 Stufe 1"
  ziel: >
    Eine Zahl herstellen, die es heute nicht gibt: wie viele Modellbefehle wirklich fehlen,
    damit die Werkzeugleiste funktioniert. Alles Weitere an AUF-50 haengt daran.
  nicht_ziel: >
    KEIN Produktivcode am Planer. Kein neuer Befehl in applyCommand. Keine UI-Aenderung.
    Diese Stufe ZAEHLT, sie baut nicht.
```

## Warum diese Stufe zuerst

**Ohne sie ist jeder Aufwandssatz geraten.** Der Stufenplan vermutet, dass der groesste Teil der
`create`-Vertraege durch **einen** vorhandenen Befehl mit unterschiedlichem `type` gedeckt ist —
*vermutlich*, und genau das ist die offene Frage. **Die Marke `fehlt` ist der eigentliche Bauvorrat.**

## Bestand, gemessen

```yaml
measurement:
  observed_at_commit: ba69f432
  observed_at: "2026-07-30 10:10 CEST"
  freshness_rule: "Weicht HEAD ab, alle fuenf Befehle vor Baubeginn neu fahren."
  werte:
    - id: M-01
      command: "git show <commit>:resources/planner/hausplaner/app/tools/werkzeugVertrag.ts | grep -c 'umkehrbar:'"
      observed_value: 110
      purpose: "Grundgesamtheit der Vertraege"
      # KORRIGIERT 30.07., 18:38 — hier stand 111. Mein Befehl zaehlte `umkehrbar:`
      # und hat die TYPDEKLARATION der Schnittstelle mitgezaehlt. Mit Anfuehrungszeichen
      # gezaehlt (`werkzeugId: '`) sind es 110 — und 110 eindeutige ids.
      # Der Stufenplan vom 26.07. hatte recht, meine Nachmessung war falsch.
    - id: M-02
      command: "git show <commit>:resources/planner/hausplaner/app/tools/werkzeugVertrag.ts | grep -c 'umkehrbar: false'"
      observed_value: 33
      purpose: "Erwartungswert fuer die Marke `ohne-modell` — Ansicht, Auswahl, Messen"
    - id: M-03
      command: "git show <commit>:resources/planner/hausplaner/commands/applyCommand.ts | grep -c \"case '\""
      observed_value: 19
      purpose: "vorhandene Modellbefehle — der Vorrat, gegen den `deckt` geprueft wird"
    - id: M-04
      command: "git show <commit>:resources/planner/hausplaner/app/tools/werkzeugVertrag.ts | wc -l"
      observed_value: 1419
      purpose: "Umfang der Datei, die gelesen werden muss"
    - id: M-05
      command: "git show <commit>:resources/planner/hausplaner/commands/applyCommand.ts | wc -l"
      observed_value: 424
      purpose: "Umfang der Befehlsstelle"
```

**Der Stufenplan nannte 110 Vertraege. Gemessen sind es 111** — die Datei ist seit dem 26.07.
gewachsen. *Deshalb steht die Zahl hier mit Befehl und Commit und nicht als Erinnerung.*

## Umfang

```yaml
scope:
  lesen:
    - resources/planner/hausplaner/app/tools/werkzeugVertrag.ts
    - resources/planner/hausplaner/commands/applyCommand.ts
    - resources/planner/hausplaner/app/tools/toolRegistry.ts
  schreiben:
    - resources/planner/hausplaner/app/tools/werkzeugLandkarte.ts   # NEU, reine Daten
    - resources/planner/hausplaner/__tests__/werkzeugLandkarte.test.ts   # NEU
  ausschluss:
    - pfad: "resources/planner/hausplaner/commands/applyCommand.ts"
      grund: "wird gelesen, nicht geaendert — ein neuer Befehl waere Stufe 3"
      entschieden_von: "Planner, 30.07., 10:12"
```

**`werkzeugLandkarte.ts` ist eine Datenliste, kein Verhalten:** je Vertrag `{ id, marke, begruendung }`,
wobei `begruendung` bei `deckt` den **Namen des deckenden Befehls** nennt und bei `fehlt` den Satz,
was der Befehl leisten muesste.

## Abnahmekriterien

```yaml
kriterien:
  - id: K-01
    aussage: "Jeder der 110 Vertraege traegt genau eine Marke."   # 111 war mein Zaehlfehler
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=werkzeugLandkarte"
      erwartet: "gruen; der Test zaehlt Vertraege und Landkarteneintraege und vergleicht sie"
    gegenbeweis: >
      Einen Vertrag aus der Landkarte entfernen — der Test muss rot werden und die fehlende
      id nennen, nicht nur die Zahl.

  - id: K-02
    aussage: "Die vier Marken sind die einzigen erlaubten Werte."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "npm run tsc:hausplaner"
      erwartet: "exit 0 — `marke` ist ein Vereinigungstyp aus genau vier Literalen"
    gegenbeweis: "Eine fuenfte Marke eintragen — tsc muss fehlschlagen."

  - id: K-03
    aussage: "Jede `deckt`-Marke nennt einen Befehl, den es in applyCommand WIRKLICH gibt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=werkzeugLandkarte"
      erwartet: "gruen; der Test liest die case-Zweige aus applyCommand.ts und prueft jede Nennung"
    gegenbeweis: >
      Einen erfundenen Befehlsnamen eintragen — der Test muss rot werden. **Das ist das Kriterium,
      das die ganze Stufe traegt: eine Landkarte, die auf Befehle zeigt, die es nicht gibt,
      ist schlimmer als keine.**

  - id: K-04
    aussage: "Die Zahl der `fehlt`-Marken steht als Ergebnis im Commit-Text."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "git log -1 --pretty=%B | grep -c 'fehlt:'"
      erwartet: "1 — die Zahl ist das Produkt dieser Stufe, nicht die Datei"
    gegenbeweis: "Ohne die Zeile ist der Auftrag nicht abgeschlossen."

  - id: K-05
    aussage: "Kein Verhalten am Planer hat sich geaendert."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Testzahl vorher und nachher gemeldet; keine bestehende Zusage faellt"
    gegenbeweis: >
      `git diff --stat` gegen den Basis-Commit darf ausserhalb der zwei neuen Dateien
      **null** Zeilen zeigen.
```

## Betrieb

**Fassung B gilt:** committen auf `auto/hausplaner-integration`, Basis-SHA und Generator-SHA melden,
**niemals nach `main` mergen, niemals pushen.** Nur die eigenen Pfade stagen.

**Wenn beim Lesen auffaellt, dass ein Vertrag gar nicht in den Bauplaner gehoert:** Marke
`stillgelegt` und **melden**, nicht entfernen. `toolCatalogStillgelegt.ts` fuehrt so etwas bereits.
