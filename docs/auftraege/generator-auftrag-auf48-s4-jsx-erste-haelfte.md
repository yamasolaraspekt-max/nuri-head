# AUF-48 SCHEIBE 4a — Die Kopfleiste aus dem JSX herauslösen

```yaml
auftrag:
  id: AUF-48-S4a
  titel: "Der obere Rahmen des JSX wird eine eigene Komponente"
  status: aktiv
  spur: B
  heimat: ticket
  rolle: generator
  angelegt: "2026-07-30 19:53 CEST"
  grundlage: "Zuschnitt §7 — Anker ueber NAMEN. Scheibe 4 ist zu gross und wird geteilt."
  ziel: >
    Der obere Teil des JSX — Werkzeugzeile, Objektkopf, Ueberlauf, Arbeitsbereich-Waehler,
    Bedien-Werkzeugleiste — verlaesst die Hauptfunktion als eigene Komponente mit benannten
    Eigenschaften. Die Hauptfunktion ruft sie auf.
  nicht_ziel: >
    KEINE Verhaltensaenderung, keine Umbenennung sichtbarer Beschriftungen, KEINE Inline-Stelle
    anfassen (das ist AUF-38 Scheibe 7 und kommt DANACH). Der untere Teil des JSX — Buehne,
    Schienen, Palette, Dialoge — bleibt unberuehrt.
```

## Warum Scheibe 4 geteilt wird

**Gemessen ist der JSX-Block der Hauptfunktion 1250 Zeilen** — die Haelfte der Datei. **Als ein
Auftrag waere das kein Schnitt, sondern ein Umzug.** Er wird deshalb an der Naht geteilt, die im
Code schon steht: der obere Rahmen endet, wo die Buehne beginnt.

> **Und die Reihenfolge zu AUF-38 ist zwingend:** **120 der 133 offenen Inline-Stellen liegen in
> genau diesem JSX-Block.** Wer sie vorher abraeumte, muesste sie danach ein zweites Mal anfassen.
> **Erst zerlegen, dann aufraeumen.**

## Bestand, gemessen

```yaml
measurement:
  observed_at_commit: 59e91b50
  observed_at: "2026-07-30 19:52 CEST"
  freshness_rule: "Weicht HEAD ab, neu messen. Zeilennummern sind KEINE Kanten (PB-007)."
  werte:
    - id: M-01
      command: "git show <commit>:...HausplanerApp.tsx | wc -l"
      observed_value: 2375
      purpose: "Verlauf 2511 -> 2447 -> 2375 ueber zwei Scheiben"
    - id: M-02
      command: "sed -n '1126,$p' | wc -l   (ab dem return der Hauptfunktion)"
      observed_value: 1250
      purpose: "der JSX-Block — 53 Prozent der Datei"
    - id: M-03
      command: "sed -n '1126,$p' | grep -c 'style={{'"
      observed_value: 120
      purpose: "Inline-Stellen im JSX; im ganzen Rest der Datei sind es 13"
    - id: M-04
      command: "git show <commit>:...HausplanerApp.tsx | grep -nE '^(export )?function [A-Z]'"
      observed_value: "KontextOptionenLeiste bei 184, HausplanerApp bei 249"
      purpose: "die Hauptfunktion beginnt bei 249, ihr return bei 1126 — NICHT das return bei 232"
```

**Anker der Scheibe** — Kommentare, die im Code stehen und die Naht markieren:

```text
Beginn:  der Kommentar „Werkzeugleiste — neutral, Marke nur fuer Primaeraktion"
Ende:    die Bedien-Werkzeugleiste, letzter Block vor der Buehne
Drin:    Werkzeugzeile · Objektkopf (AUF-83-T3/K-01) · Ueberlauf (T3-N1) ·
         Arbeitsbereich-Waehler (AUF-34) · Einstieg zur Befehlspalette (T3/K-05b) ·
         Bedien-Werkzeugleiste (AUF-68)
```

## Umfang

```yaml
scope:
  schreiben:
    - resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx       # NEU
    - resources/planner/hausplaner/app/HausplanerApp.tsx              # Entnahme + Aufruf
    - resources/planner/hausplaner/__tests__/kopfrahmen.test.ts       # NEU
  ausschluss:
    - pfad: "jede style={{-Stelle"
      grund: "AUF-38 Scheibe 7, kommt NACH der Zerlegung"
      entschieden_von: "Planner, 30.07., 19:53 — und Yamas Fokus vom 19:20"
    - pfad: "alles ab der Buehne abwaerts"
      grund: "Scheibe 4b"
      entschieden_von: "Planner, 30.07., 19:53"
```

**Die Eigenschaften der neuen Komponente werden benannt uebergeben, nicht als Sammelobjekt.**
*Ein `props`-Klumpen verschoebe die Unuebersichtlichkeit nur eine Datei weiter.*

## Abnahmekriterien

```yaml
kriterien:
  - id: K-01
    aussage: "Der Kopfrahmen steht in Kopfrahmen.tsx und die Hauptfunktion ruft ihn."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c '<Kopfrahmen' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "1"
    gegenbeweis: "Den Aufruf entfernen — tsc meldet den unbenutzten Import, und die Oberflaeche verliert den Kopf."

  - id: K-02
    aussage: "Keine Inline-Stelle wurde angefasst."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c 'style={{' resources/planner/hausplaner/app/HausplanerApp.tsx resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx"
      erwartet: "die SUMME beider Dateien ist 133 — genau wie vorher, nur anders verteilt"
    gegenbeweis: >
      **Dieses Kriterium misst die Sache, nicht die Gestalt** — es zaehlt die Summe, nicht die
      Verschiebung. Raeumst du eine Stelle auf, sinkt die Summe, und das ist ein Befund:
      Aufraeumen ist AUF-38, nicht dieser Auftrag.

  - id: K-03
    aussage: "Die sichtbare Oberflaeche ist unveraendert."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Testzahl vorher 1476, nachher mindestens gleich; keine Datei rot, die vorher gruen war"
    gegenbeweis: >
      Eine Eigenschaft beim Aufruf weglassen — mindestens eine Zusage muss rot werden.
      **Wird keine rot, ist der Kopfrahmen unverriegelt** — das war in S1 und S2 beide Male so.
      Dann schreibst du die Zusagen und pruefst jede mit einer eigenen Mutation gegen.

  - id: K-04
    aussage: "Die Eigenschaften sind einzeln benannt, kein Sammelobjekt."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "grep -cE 'props: |\\.\\.\\.props' resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx"
      erwartet: "0"
    gegenbeweis: "Ein Sammelobjekt einbauen — der Befehl meldet 1."

  - id: K-05
    aussage: "Die Datei ist nur durch Entnahme kuerzer geworden."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "git diff --numstat <basis> HEAD -- resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "Loeschungen deutlich groesser als Einfuegungen"
    gegenbeweis: "Kommt Logik hinzu, ist es Umbau statt Zerlegung."
```

## Betrieb

**Fassung B:** committen auf `auto/hausplaner-integration`, **Basis-SHA und Generator-SHA melden**,
niemals nach `main` mergen, niemals pushen, nur eigene Pfade stagen.

**Meldet ein Pruefbefehl etwas Unerwartetes: pruefe zuerst, ob er die Sache misst oder die Gestalt.**
*In S2 hat mein K-02 sechs geloeschte Huellen gemeldet, wo es einen Formwechsel gab. Du hast es
erkannt und widerlegt — mach das wieder.*
