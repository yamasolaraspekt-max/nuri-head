# AUF-87-N2 — Die fünf Strukturprüfungen

*Planner, 30.07.2026, 08:05 CEST. Zweite Stufe des Validators.
**Auslöser: der Generator hat gemeldet, dass sie fehlen, und ich habe eine halbe Stunde später
in meinem eigenen Register gefunden, was Prüfung 1 gefangen hätte.***

> **AUF-87 prüft AUSFÜHRBARKEIT. Diese Stufe prüft STRUKTUR.**
> *„Beides zusammen ergibt erst das Gate"* — sein Satz, nicht meiner.

```yaml
auftrag:
  id: AUF-87-N2
  status: ruht
   # PB-B2, 01.08.2026 - Planner. Stand bis heute: `aktiv`. 17 Blaetter trugen das,
   # die Struktur-Zusage S-01 erwartet GENAU EINES. `ruht` heisst hier ehrlich:
   # der Zustand ist NICHT nachgemessen. Wer das Blatt zieht, misst zuerst.
  spur: B
  heimat: ticket
  ziel: >
    `scripts/auftrag-pruefen.mjs` prueft zusaetzlich die fuenf Strukturbedingungen aus
    AUFTRAGSSCHEMA §7 und meldet sie in derselben Form wie die Ausfuehrbarkeit.
  nicht_ziel: >
    KEINE Aenderung an den bestehenden fuenf Meldungsstufen.
    KEIN Meta-System — genau diese fuenf Pruefungen, keine sechste.
    KEINE Reparatur bestehender Blaetter. Findet er etwas: melden, nicht beheben.

geerbte_zusagen:
  befehl: "ls scripts/__tests__/"
  ergebnis: "auftragPruefen.test.mjs — deine eigene Zusagedatei aus AUF-87. Sie waechst, sie wird nicht ersetzt."

scope:
  pfade:
    - scripts/auftrag-pruefen.mjs
    - scripts/__tests__/auftragPruefen.test.mjs
  ausschluesse:
    - stelle: "alle bestehenden Auftragsblaetter"
      grund: "Der Validator wird an ihnen GEMESSEN, nicht an ihnen repariert."
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Genau EIN Auftrag traegt status: aktiv."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "Zwei Blaetter mit status aktiv ⇒ FEHLSCHLAG mit beiden Dateinamen."
    beleg: testausgabe
    gegenprobe: "die Pruefung entfernen ⇒ MUSS rot werden"
    begruendung: >
      **Der Beleg ist frisch und er ist meiner:** am 30.07. um 07:55 trug meine eigene
      Auftragstafel **sieben** Steuerungsmarken statt einer — sechs davon zeigten auf abgenommene
      Posten. Wer die Tafel nach §1 abholt, haette zuerst einen erledigten Auftrag gezogen.
      **Offen seit Tagen, gefunden durch Hinsehen, nicht durch ein Werkzeug.**

  - id: K-02
    aussage: "Jedes Kriterium hat typ UND pruefung.befehl — oder manual MIT Begruendung."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "Kriterium ohne befehl und ohne Begruendung ⇒ FEHLSCHLAG mit K-id."
    beleg: testausgabe
    gegenprobe: "ein Kriterium mit leerem befehl ⇒ MUSS rot werden"

  - id: K-03
    aussage: "Jedes P0/P1-Kriterium vom Typ absence hat einen presence- oder behavioural-Partner."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "absence-Kriterium P1 ohne Partner ⇒ FEHLSCHLAG mit K-id."
    beleg: testausgabe
    gegenprobe: "den Partner entfernen ⇒ MUSS rot werden"
    begruendung: >
      **Das ist die Pruefung mit dem hoechsten Wert.** Bei AUF-83-T2 waren K-01 bis K-04
      `absence`-Kriterien mit einem `grep` als Pruefverfahren. Der Evaluator hat die entfernte
      Navigation zurueckgeholt — **kein Test wurde rot.** Sechs Zusagen mussten nachtraeglich
      angelegt werden, die der Auftrag nicht verlangt hatte.
      *Ohne Partner hat man nicht aufgeraeumt, sondern entfernt.*

  - id: K-04
    aussage: "Jedes coverage-Kriterium hat ein population_command, und der Befehl ist ausfuehrbar."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "coverage ohne population_command ⇒ FEHLSCHLAG."
    beleg: testausgabe

  - id: K-05
    aussage: "Jeder Eintrag unter ausschluesse hat grund UND entschieden_von."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "Ausschluss ohne entschieden_von ⇒ FEHLSCHLAG."
    beleg: testausgabe

  - id: K-06
    aussage: "Der Validator liest ALLE yaml-Bloecke eines Blattes, nicht nur den ersten."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "bash scripts/auftrag-pruefen.sh docs/auftraege/generator-auftrag-auf83-t5-schienen-klappbar.md"
      erwartet: >
        Kein Hinweis mehr, dass nur der erste Block geprueft wurde. Die `measurements:` aus dem
        zweiten Block erscheinen im Bericht.
    beleg: rohausgabe
    begruendung: >
      **Befund aus deinem eigenen ersten Lauf, 30.07. 08:03:**
      *„HINWEIS: 2 yaml-Bloecke, geprueft wurde der ERSTE (der Kopf)."*
      **Damit ist meine ganze R19-Umstellung fuer den Validator unsichtbar** — die Messbloecke,
      die ich um 07:52 in T5 und AUF-88-P1 geschrieben habe, stehen in einem zweiten Block.
      *Ein Werkzeug, das die Haelfte nicht sieht, meldet Vollstaendigkeit, die es nicht hat.*

  - id: K-07
    aussage: "Ein erwarteter Nulltreffer ist kein Fehlschlag."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "bash scripts/auftrag-pruefen.sh docs/auftraege/generator-auftrag-auf83-t5-schienen-klappbar.md"
      erwartet: >
        `scope.population_command` von T5 wird nicht mehr als FEHLSCHLAG gemeldet — oder die
        Meldung nennt den Grund: eine `&&`-Kette, deren letztes Glied `grep` mit 0 Treffern ist.
    beleg: rohausgabe
    begruendung: >
      **Zweiter Befund aus deinem ersten Lauf, und er ist meiner.** Der T5-Befehl verkettet drei
      `grep` mit `&&`. Der letzte sucht `collapsed|klappZu|schieneZu` und findet **0** — genau das
      ist das gewuenschte Ergebnis, es beweist die Luecke. **`grep` liefert dafuer exit 1 und
      reisst die Kette.**
      *Der Befehl ist richtig, sein Exitcode ist es nicht.* **Ein Validator, der das nicht
      unterscheidet, macht aus jeder bewiesenen Luecke einen Fehlschlag** — und wird abgeschaltet.
      Wie du es loest, entscheidest du: `|| true` verlangen, `;` statt `&&`, oder eine Zeile
      `erwarteter_nulltreffer: true` im Kopf. **Melde, was du waehlst, und warum.**

  - id: K-08
    aussage: "Gates ohne Regression."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run test:hausplaner"
      erwartet: "genau die zwei Pfade aus scope; Gates ohne Regression"
    beleg: dateiliste + testzaehler

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit Votumszeile am Zeilenanfang."
  rueckwaerts_probe: >
    **Aus Schema §7, Punkt 4, und sie ist der eigentliche Test:** das Blatt von AUF-38 Scheibe 3
    im alten Zustand durch den Validator schicken (`git show <commit>:<pfad>`).
    **Findet er die Luecke nicht, die uns das NACHBESSERN eingebracht hat, taugt er nichts** —
    und das merken wir, bevor er im Weg steht.
```

---

## Warum diese Stufe jetzt kommt und nicht später

**Weil sie sich in der halben Stunde zwischen ihrer Meldung und diesem Blatt selbst belegt hat.**

Der Generator meldete um 07:26: *die fünf Strukturprüfungen fehlen, Prüfung 1 hätte gefangen, dass
zwei Blätter gleichzeitig `status: aktiv` trugen.*

Um 07:55 habe ich in meiner eigenen Auftragstafel **sieben Steuerungsmarken statt einer** gefunden,
sechs davon auf abgenommene Posten zeigend, **offen seit Tagen.**

> *Er hat die Lücke im Werkzeug gemeldet. Ich habe sie im Register gefunden. Beides ist derselbe
> Befund, und keiner von uns brauchte den anderen dafür — aber ein Skript hätte beides in einer
> Sekunde gesagt.*

## Und die zwei Befunde aus deinem ersten Lauf

**Du hast den Validator gebaut und sofort damit gemessen. Beide Funde treffen mich, nicht dich:**

- **Zwei YAML-Blöcke, geprüft wird der erste** — meine ganze R19-Umstellung ist für ihn unsichtbar.
- **Ein erwarteter Nulltreffer reißt die `&&`-Kette** — mein T5-Befehl ist richtig und wird als
  Fehlschlag gemeldet.

*Beides steht als K-06 und K-07 in diesem Blatt. **Das Werkzeug hat am ersten Tag zwei Fehler des
Planners gefunden — bevor es überhaupt abgenommen war.***
