# AUF-87 — Der Validator: ein Auftragsblatt, dessen Prüfbefehle ins Leere greifen, ist nicht abgebbar

*Planner, 30.07.2026, 06:40 CEST. **Ich schulde dieses Werkzeug seit dem 27.07.** — es steht seit
dem Tag im Schema als „der Validator" und hat nie ein Blatt bekommen. In der Zwischenzeit hat die
Fehlerklasse, gegen die es gebaut wird, **fünf Ausprägungen** erreicht.*

> **Das ist der Auftrag mit dem besten Verhältnis von Aufwand zu Ersparnis, den ich heute schreiben
> kann.** Er baut nichts am Produkt. Er macht **F-04 von einer Regel zu einer Barriere** — und
> hätte, gemessen an den Befunden dieser 24 Stunden, **drei von fünf Rückweisungen verhindert.**

```yaml
auftrag:
  id: AUF-87
  status: ruht
   # PB-B2, 01.08.2026 - Planner. Stand bis heute: `aktiv`. 17 Blaetter trugen das,
   # die Struktur-Zusage S-01 erwartet GENAU EINES. `ruht` heisst hier ehrlich:
   # der Zustand ist NICHT nachgemessen. Wer das Blatt zieht, misst zuerst.
  spur: B
  heimat: ticket
  ziel: >
    `scripts/auftrag-pruefen.sh <blatt.md>` liest den YAML-Kopf eines Auftragsblatts, faehrt jeden
    darin genannten Pruefbefehl und meldet, welcher fehlschlaegt oder nichts findet. Ein Blatt,
    dessen Befehle ins Leere greifen, faellt damit AUF, bevor jemand danach baut.
  nicht_ziel: >
    KEINE Bewertung des Inhalts — der Validator prueft AUSFUEHRBARKEIT, nicht Richtigkeit.
    KEINE Aenderung an bestehenden Blaettern.
    KEIN neuer YAML-Parser als Abhaengigkeit — `js-yaml` liegt bereits im Baum (pruefen, sonst
    melden statt nachinstallieren).
    KEINE Ausfuehrung schreibender Befehle (siehe sicherheit).

scope:
  population_command: >
    ls docs/auftraege/generator-auftrag-*.md docs/auftraege/evaluator-auftrag-*.md | wc -l &&
    grep -l 'pruefung:' docs/auftraege/*.md | wc -l
  # ENTFAELLT nach R19 (30.07.) — die folgenden Zeilen sind HERKUNFTSNACHWEIS, KEINE Bedingung.
  # Wer die Zahl braucht, faehrt population_command. Neue Blaetter tragen dieses Feld nicht mehr.
  population_at_writing_ALT: >
    Messung des Planners, 30.07. 06:40, KEINE Bedingung: `docs/auftraege/` haelt ueber 40 Blaetter,
    davon tragen die neueren einen YAML-Kopf nach `AUFTRAGSSCHEMA.md` (27.07.). Aeltere haben
    keinen — **der Validator muss das aushalten und es SAGEN, nicht daran scheitern.**
  pfade:
    - scripts/auftrag-pruefen.sh
    - scripts/__tests__/auftragPruefen.test.mjs      # oder der uebliche Ort fuer Skript-Zusagen
    - docs/auftraege/AUFTRAGSSCHEMA.md               # KORRIGIERT 30.07. 07:32 auf deinen Befund 1: NUR Abschnitt 7 ("Was als Naechstes zu bauen ist"). Den von mir genannten Abschnitt gibt es nicht — sechste Auspraegung von F-04, und du hast sie gemeldet statt sie zu umgehen.
  ausschluesse:
    - stelle: "alle bestehenden Auftragsblaetter"
      grund: >
        Der Validator wird an ihnen GEMESSEN, nicht an ihnen repariert. Findet er in einem alten
        Blatt einen toten Befehl: melden, nicht beheben. **Sonst waere der erste Lauf ein
        Massenumbau von 40 Dateien.**
      entschieden_von: planner

sicherheit:
  denylist: >
    Der Validator fuehrt einen Befehl NICHT aus, wenn er eines dieser Muster enthaelt:
    `git commit` · `git push` · `git add` · `git reset` · `git checkout` · `rm ` · `mv ` ·
    `> ` (Umleitung) · `npm run build` · `curl` · `chmod`.
    **Er meldet ihn als UEBERSPRUNGEN mit Grund** — er verschweigt ihn nicht.
  begruendung: >
    Ein Auftragsblatt ist eine Datei, die jede Rolle schreiben darf. Ein Werkzeug, das ihre Inhalte
    ungefiltert an die Shell gibt, ist ein Hebel, den niemand beabsichtigt hat. *Die Blaetter sind
    heute vertrauenswuerdig; das Werkzeug soll es auch dann noch sein, wenn eines es nicht ist.*

kriterien:
  - id: K-01
    aussage: "Der Validator findet jeden Pruefbefehl im Kopf — auch die in `scope`."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: >
        Aus einem Blatt mit N Befehlen findet er N. **Gezaehlt wird die MENGE, nicht ein Muster:**
        `scope.population_command` UND jedes `kriterien[].pruefung.befehl`.
    beleg: testausgabe
    gegenprobe: >
      Ein Kriterium mit `befehl` hinzufuegen ⇒ die gefundene Zahl MUSS steigen.
      Steigt sie nicht, sucht er nach Muster statt nach Menge — **das ist F-01, die Klasse mit
      vier Auspraegungen.**

  - id: K-02
    aussage: "Ein Befehl, der `exit != 0` liefert, wird als FEHLSCHLAG gemeldet."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "Blatt mit `grep -n 'GibtEsNicht' <datei>` ⇒ Ausgabe nennt K-id, Befehl und Exitcode"
    beleg: testausgabe
    begruendung: >
      **Das ist der Fall aus T1a Fassung 1:** mein K-05 nannte eine Testdatei mit `innerWidth`,
      `grep` lieferte `exit 1`, und das Blatt lag trotzdem. *Der Validator haette es in einer
      Sekunde gesagt.*

  - id: K-03
    aussage: "Ein Befehl mit `exit 0`, aber LEERER Ausgabe wird als VERDAECHTIG gemeldet."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "eigene Meldungsstufe, unterscheidbar von FEHLSCHLAG und von OK"
    beleg: testausgabe
    begruendung: >
      **Drei Stufen, nicht zwei.** `grep -c` liefert `0` bei `exit 1`; `sed -n '9999,9999p'`
      liefert nichts bei `exit 0`. Der zweite Fall ist der gefaehrlichere: **er sieht aus wie
      Erfolg.** *Genau so ist mir die Grundgesamtheit von T3 durchgerutscht — der Befehl lief,
      er beschrieb nur einen Stand von vor vier Tagen.*

  - id: K-04
    aussage: "Ein Blatt OHNE YAML-Kopf ist kein Fehlschlag, sondern eine eigene Meldung."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "`KEIN KOPF` mit Dateiname, Exitcode des Validators bleibt 0"
    beleg: testausgabe
    grenze: >
      Die aelteren Blaetter haben keinen Kopf. **Ein Werkzeug, das bei ihnen rot wird, wird
      abgeschaltet** — und dann faengt es auch die neuen nicht mehr.

  - id: K-05
    aussage: "Ein Kriterium mit `pruefung.typ: visuell` wird als NICHT MASCHINELL gemeldet."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "eigene Stufe, mit K-id und der Zeile `ausgefuehrt_von`"
    beleg: testausgabe
    begruendung: >
      **Der Validator darf nicht so tun, als haette er alles geprueft.** Ein Blatt, dessen
      Kriterien saemtlich `visuell` sind, bekommt von ihm KEIN gruenes Haekchen — es bekommt die
      Liste dessen, was ein Mensch ansehen muss. *Sonst ersetzt das Werkzeug die Pruefung, statt
      sie vorzubereiten — und das ist die naechste Fehlerklasse, nicht die Loesung der letzten.*

  - id: K-06
    aussage: "Die Denylist greift, und sie schweigt nicht."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "node --test scripts/__tests__/auftragPruefen.test.mjs"
      erwartet: "Blatt mit `git commit -m x` als Pruefbefehl ⇒ Stufe UEBERSPRUNGEN, mit Grund"
    beleg: testausgabe
    gegenprobe: >
      Die Denylist leeren ⇒ die Zusage MUSS rot werden.

  - id: K-07
    aussage: "Der erste Lauf ueber den echten Bestand ist gemacht und BERICHTET — nicht behoben."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "bash scripts/auftrag-pruefen.sh docs/auftraege/generator-auftrag-auf83-t3-kopfleiste-arbeitszeile.md docs/auftraege/generator-auftrag-auf83-t5-schienen-klappbar.md"
      erwartet: >
        Zwei Berichte. **Was er an meinen Blaettern findet, ist der Ertrag dieses Auftrags** —
        er wird gemeldet, nicht stillschweigend repariert.
    beleg: rohausgabe beider Berichte

  - id: K-08
    aussage: "Gates ohne Regression, nichts ausserhalb des Scopes."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "genau die Pfade aus scope; Gates 0/0/0"
    beleg: dateiliste + testzaehler

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2."
  mutationspflicht: >
    Jede Gegenprobe wird ZUERST auf Auffinden geprueft — und die Datei muss nach der Mutation
    noch laufen. *Eine Mutation, die das Skript zerlegt, liefert ein wertloses Rot.*
  keine_stille_kappung: >
    Findet der Validator mehr als er anzeigt, sagt er es. **Eine Liste, die bei zehn abschneidet,
    liest sich wie „zehn Probleme" und ist „mindestens zehn".**
```

---

## Warum dieser Auftrag Spur B ist

**Er fasst keinen Datenpfad an, keine PHP-Logik, keine Query, keinen Endpunkt, keinen abgeleiteten
Wert.** Er liest Dateien und startet Prozesse. *Die Denylist ist keine Sicherheitsgrenze im Sinne
der Spur-Definition, sondern eine Sorgfaltsmaßnahme — sie steht trotzdem im Kopf, weil sie eine
Zusage bekommt.*

## Was er gekostet hätte — und was er gespart hätte

**In den letzten 24 Stunden gab es fünf Rückweisungen. Drei davon hätte er gefangen:**

| Rückweisung | Was er gesagt hätte |
|---|---|
| **T1a Fassung 1** — K-05 nannte eine Testdatei mit `innerWidth`, `grep` lieferte `exit 1` | `K-05: FEHLSCHLAG (exit 1)` |
| **T3 erste Messung** — Grundgesamtheit vom 25.07., vier Tage alt | `population_command: OK, aber Ausgabe weicht von population_at_writing ab` |
| **T2** — mein Ausschluss behauptete, `objekt.blade` trage kein `hp-bar` | `ausschluss ohne Beleg: der Grund nennt eine Messung, die kein Befehl deckt` |

**Zwei hätte er nicht gefangen** — T3/K-01 gegen AUF-70 und die Kollision mit den `pfade`. *Das ist
kein Argument gegen ihn, sondern die Begründung für **R17**: der Validator prüft, ob ein Befehl
läuft; R17 verlangt, dass der Planner ihn überhaupt schreibt.*

## Reihenfolge

1. **T3** — im Bau.
2. **T5** — liegt, gesperrt bis T3 gebaut ist und der K-08-Wert steht.
3. **Dieses Blatt (AUF-87)** — der Validator. *Er berührt keine einzige Datei der Layout-Kette;
   fällt T3 oder T5 auf eine Rückfrage zurück, kann er dazwischenlaufen, ohne etwas zu blockieren.*

---

## NACHTRAG 30.07., 07:32 — drei Antworten auf deine Quittung

**Empfangsquittung zuerst** (die neue SLA aus dem Konzept): *empfangen 07:30 · verstanden ·
nichts blockiert · Antwort sofort, weil du auf zwei davon wartest.*

### Befund 1 — angenommen, das Blatt ist korrigiert

**Den Abschnitt gibt es nicht.** Selbst nachgesehen: `AUFTRAGSSCHEMA.md` hat neun Abschnitte,
keiner heißt *„Wie geprueft wird"*. **`pfade` nennt jetzt Abschnitt 7** — dort steht der Validator
als offener Posten, dort gehört sein Vollzug hin. *Sechste Ausprägung von F-04: ich habe ein
Artefakt benannt, das ich nicht geprüft hatte.*

### Befund 2 — dein Rat gilt, und aus zwei Listen werden drei Stufen

**Du hast recht, dass es zwei verschiedene Validatoren sind.** Inzwischen sind es drei Listen:
dein Blatt (Ausführbarkeit), `AUFTRAGSSCHEMA §7` (fünf Strukturprüfungen), und seit 07:30 das
Konzept `KONZEPT-EVIDENZBASIERTE-PLANUNG.md §8` (Evidenz, Konflikt, Reuse, Freshness).

> **Entscheid: drei Stufen desselben Werkzeugs, jede für sich abnehmbar (R5).**
>
> | | Umfang | Zustand |
> |---|---|---|
> | **AUF-87** | **Ausführbarkeit** — drei Meldungsstufen, Denylist | **im Bau, unverändert** |
> | **AUF-87-N2** | **Struktur** — die fünf Prüfungen aus Schema §7 | Blatt folgt, wenn das Gate grün ist |
> | **AUF-87-N3** | **Evidenz** — Freshness gegen HEAD, Ticket-Konflikt, Reuse, unbelegte Zahl | Blatt folgt danach |
>
> **Dein Umfang bleibt, wie er ist. Ich erweitere ihn nicht.**

*Zwei der fünf Strukturprüfungen sind besonders viel wert und begründen, warum N2 kommt und nicht
verfällt:* **Prüfung 1** (*genau ein Auftrag mit `status: aktiv`*) hätte die vier Fälle gefangen,
in denen ein `status:` stehengeblieben ist. **Prüfung 3** (*jedes P1-`absence` braucht einen
`presence`-Partner*) ist wörtlich die Regel, an der T2 gescheitert wäre, hätte der Evaluator nicht
von Hand die Navigation zurückgeholt.

### Befund 3 — nichts zu tun, aber eine Verschärfung an K-04

**Nach R19 ist die Herkunftszahl keine Bedingung** — du hast die gemessene genommen, das ist
richtig, mehr ist nicht zu tun.

**Aber dein Nebensatz ist ein Entwurfsbefund:** *„67 davon haben keinen Kopf … das ist jetzt das
häufigste Ergebnis, nicht der Randfall."*

> **K-04 wird verschärft: `KEIN KOPF` wird AGGREGIERT gemeldet, nicht je Datei.**
> Eine Zeile mit der Anzahl und, auf Wunsch, die Liste hinter einem Schalter.
> **Ein Werkzeug, das bei jedem Lauf 67 Warnungen ausgibt, wird abgeschaltet** — und dann fängt
> es auch die 13 nicht mehr, für die es gebaut ist. *Das ist dieselbe Klasse wie „keine stille
> Kappung", nur andersherum: nicht zu wenig melden, sondern zu viel.*
