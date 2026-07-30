# AUF-48 SCHEIBE 3 — Tasten und die vier Zustandsfunktionen

```yaml
auftrag:
  id: AUF-48-S3
  titel: "Der Tastenblock wird ein eigenes Modul; die vier Zustandsfunktionen bekommen Zusagen"
  status: aktiv
  spur: B
  heimat: ticket
  rolle: generator
  angelegt: "2026-07-30 19:47 CEST"
  grundlage: >
    Zuschnitt §7 (Anker ueber NAMEN) UND die Meldung des Generators im Commit 59e91b50:
    „vier lesen/schreiben Zustand, gehoeren in Scheibe 3".
  ziel: >
    Die Tastenzuordnung verlaesst die Komponente als reine Abbildung Ereignis -> Absicht.
    Die vier Zustandsfunktionen bleiben stehen, bekommen aber die Zusagen, die ihnen fehlen.
  nicht_ziel: >
    KEINE Verhaltensaenderung. Keine Tastenbelegung aendern, keine hinzufuegen, keine entfernen.
    Kein useEffect entfernen oder zusammenlegen. Kein localStorage-Zugriff verschieben.
    Scheibe 4 (das JSX) bleibt unberuehrt.
```

## Warum diese Scheibe anders geschnitten ist als S1 und S2

**Der Generator hat die Antwort im Commit 59e91b50 selbst geliefert:** vier Funktionen lesen oder
schreiben Zustand und durften deshalb nicht mit — `waehleBereich` (UI-Store + `localStorage`),
`klappeSchiene` (React-State + `localStorage`), `oeffnePalette` und `schliessePalette`
(React-State + `ref`). **Sie bleiben, wo sie sind. Was ihnen fehlt, sind Zusagen.**

**Auslagern lässt sich hier nur eines: die Zuordnung Taste -> Absicht.** Sie ist rein — aus einem
Tastenereignis wird ein Name, sonst nichts. **Wer die Absicht ausfuehrt, bleibt in der Komponente.**

## Bestand, gemessen

```yaml
measurement:
  observed_at_commit: 59e91b50
  observed_at: "2026-07-30 19:46 CEST"
  freshness_rule: "Weicht HEAD ab, neu messen. Zeilenzahlen sind Umfangsmasse, KEINE Kanten."
  werte:
    - id: M-01
      command: "git show <commit>:...HausplanerApp.tsx | wc -l"
      observed_value: 2375
      purpose: "Umfang nach S2 — Verlauf 2511 -> 2447 -> 2375"
    - id: M-02
      command: "git show <commit>:...HausplanerApp.tsx | grep -c 'useEffect('"
      observed_value: 7
      purpose: "sieben Effekte; NUR der mit `function taste` ist in dieser Scheibe"
    - id: M-03
      command: "git show <commit>:...HausplanerApp.tsx | grep -c 'localStorage'"
      observed_value: 4
      purpose: "vier Zugriffe — sie bleiben ALLE stehen, kein einziger wird verschoben"
```

**Anker:** `function taste` bis einschliesslich der `Delete`/`Backspace`-Zeile.
**Nicht in dieser Scheibe:** die Tastenbehandlung im JSX (Pfeiltasten, `Enter`, `Escape` in der
Palettenliste) — die gehoert zu Scheibe 4.

## Umfang

```yaml
scope:
  schreiben:
    - resources/planner/hausplaner/app/tastenAbsicht.ts             # NEU — reine Abbildung
    - resources/planner/hausplaner/app/HausplanerApp.tsx            # Entnahme + Import
    - resources/planner/hausplaner/__tests__/tastenAbsicht.test.ts  # NEU
    - resources/planner/hausplaner/__tests__/zustandsfunktionen.test.ts  # NEU — die vier
  ausschluss:
    - pfad: "waehleBereich · klappeSchiene · oeffnePalette · schliessePalette"
      grund: "lesen/schreiben Zustand — sie BLEIBEN stehen und bekommen nur Zusagen"
      entschieden_von: "Generator gemeldet in 59e91b50, Planner bestaetigt 19:47"
    - pfad: "die sechs Einzeiler-Ableitungen aus S2"
      grund: "der Generator hat begruendet, dass eine Huelle ohne Aussage entstuende — angenommen"
      entschieden_von: "Planner, 30.07., 19:47"
```

## Abnahmekriterien

```yaml
kriterien:
  - id: K-01
    aussage: "Die Tastenzuordnung ist eine reine Funktion und kennt die Komponente nicht."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -cE 'usePlannerUiStore|useState|useRef|localStorage|document\\.' resources/planner/hausplaner/app/tastenAbsicht.ts"
      erwartet: "0 — kein Zustand, kein DOM, kein Speicher"
    gegenbeweis: "Einen Store-Zugriff einbauen — der Befehl meldet dann 1, und tsc zieht die Abhaengigkeit nach."

  - id: K-02
    aussage: "Keine Tastenbelegung hat sich geaendert."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=tastenAbsicht"
      erwartet: "gruen; je Taste eine Zusage, INKLUSIVE Delete und Backspace"
    gegenbeweis: >
      Eine Taste auf eine andere Absicht legen — mindestens eine Zusage muss rot werden.
      **Zaehle vorher die Tasten im alten Block und danach im Modul: die Zahl muss gleich sein.**

  - id: K-03
    aussage: "Die vier Zustandsfunktionen sind verriegelt — erstmals."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=zustandsfunktionen"
      erwartet: "gruen; je Funktion mindestens eine Zusage, die den EFFEKT prueft"
    gegenbeweis: >
      **Der Kern dieser Scheibe.** In S1 und S2 war die Flaeche jedes Mal unverriegelt. Mutiere
      die vier VOR dem Schreiben der Tests: `waehleBereich` auf eine feste id, `klappeSchiene`
      mit gedrehtem `offen`, `oeffnePalette`/`schliessePalette` vertauscht. **Halte fest, welche
      Zusage rot wird.** Wird keine rot, ist das der Befund — dann schreibst du sie.
      *Diese vier fassen `localStorage` an; eine Zusage, die nur den Rueckgabewert prueft,
      reicht nicht — sie muss den gespeicherten Zustand pruefen.*

  - id: K-04
    aussage: "Keine der geerbten Zusagen faellt, und die Testzahl steigt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Testzahl vorher 1476, nachher hoeher; keine Datei rot, die vorher gruen war"
    gegenbeweis: "Sinkt die Zahl, ist eine Zusage verschwunden — das ist ein Befund, kein Aufraeumen."

  - id: K-05
    aussage: "Die vier localStorage-Zugriffe stehen unveraendert in HausplanerApp.tsx."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c 'localStorage' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "4 — unveraendert"
    gegenbeweis: "Einen verschieben — der Befehl meldet 3, und das waere ein Umbau statt einer Zerlegung."
```

## Betrieb

**Fassung B:** committen auf `auto/hausplaner-integration`, **Basis-SHA und Generator-SHA melden**,
niemals nach `main` mergen, niemals pushen, nur eigene Pfade stagen.

> **Zu den Pruefbefehlen dieses Blattes:** in S2 hat mein K-02-Befehl **6 statt 0** gemeldet, weil
> mehrzeilige Huellen einzeilig wurden — **ein Formwechsel, keine Loeschung.** Der Generator hat
> das erkannt und mit einer besseren Messung belegt. **Meldet ein Befehl hier etwas Unerwartetes:
> pruefe zuerst, ob der Befehl die Sache misst oder nur die Gestalt** — und wenn er die Gestalt
> misst, sag es und miss anders. *Das ist kein Ungehorsam, das ist die Arbeit.*
