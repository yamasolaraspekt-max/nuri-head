# AUF-38-MW-N2 — Was am Messwerkzeug noch offen ist

> ## ⚠ NACHTRAG 29.07., 00:45 CEST — DIESES BLATT WAR BEIM SCHREIBEN SCHON HALB ERLEDIGT
>
> Ich habe den Stand um **00:31** gemessen, acht Minuten geschrieben und **nicht neu gemessen,
> bevor ich das Blatt hingelegt habe.** In diesen acht Minuten hat der Generator `c895061d`
> (00:37) geliefert — **`AUF38-MW-1` und `AUF38-MW-2` sind gebaut, verriegelt und berichtet.**
>
> **Mein Fehler, und es ist ein bekannter:** *„Wer merkt, dass der HEAD sich unter ihm bewegt hat,
> hört auf zu messen und meldet es."* Ich habe nicht gemerkt, dass er sich bewegt, weil ich nach
> dem ersten Blick nicht mehr hingesehen habe. **Ein Auftrag, der auf einer acht Minuten alten
> Messung steht, ist derselbe Fehler wie eine Zahl im Auftrag statt eines Befehls — nur zeitlich
> statt inhaltlich.**
>
> Und der Satz, mit dem ich zu Yama gegangen wäre, wäre falsch gewesen: *„der Generator hat nichts
> zu tun"* stimmte um 00:31 und stimmte um 00:37 nicht mehr.
>
> **Das Blatt gilt ab hier in der Fassung `MW-N2`.** Der ursprüngliche Kopf steht unten als
> Historie — nicht gelöscht, weil das Blatt bereits committet und übergeben war.

---

```yaml
auftrag:
  id: AUF-38-MW-N2
  status: ruht
   # PB-B2, 01.08.2026 - Planner. Stand bis heute: `aktiv`. 17 Blaetter trugen das,
   # die Struktur-Zusage S-01 erwartet GENAU EINES. `ruht` heisst hier ehrlich:
   # der Zustand ist NICHT nachgemessen. Wer das Blatt zieht, misst zuerst.
  spur: A
  heimat: ticket
  ziel: >
    Die drei kleineren Werkzeug-Befunde des Evaluators sind entschieden und, soweit
    beauftragt, gebaut. Die Reichweite des Werkzeugs steht geschrieben statt gemessen.
  nicht_ziel: >
    MW-1 und MW-2 sind mit c895061d erledigt und liegen beim Evaluator — hier NICHT nochmal.
    Die Definition von 'statisch' wird nicht geaendert. Kein Produktionscode. Kein public/*.

vorgeschichte:
  erledigt_durch: c895061d
  erledigt: [AUF38-MW-1, AUF38-MW-2]
  ballbesitz_dafuer: evaluator
  planner_bestaetigt: >
    Der Generator hat ohne meine Klassifizierung gebaut und das offengelegt. SEINE EINSCHAETZUNG
    WAR RICHTIG, und ich bestaetige sie foermlich: IMPLEMENTIERUNGSMANGEL, nicht
    Spezifikationsmangel. Anders als bei AUF38-S4-1/NZ2-1 stand die Aufgabe vollstaendig im
    Auftrag ("die Definition aus der Quittung als ausfuehrbares Skript"); die Umsetzung traf sie
    an zwei Stellen nicht. Der Auftrag traegt keine Mitschuld.
    Seine Begruendung fuers Nicht-Warten traegt ebenfalls: das Skript ist der population_command
    jeder weiteren Scheibe — solange es falsch misst, ist jede Zahl darunter falsch. Der Rueckweg
    war ein Revert ueber zwei Dateien ohne Daten und ohne Schema. UNTER SOLCHEN BEDINGUNGEN IST
    VORLAUFEN RICHTIG, und das Offenlegen macht es pruefbar statt heimlich.
  abgrenzungs_zusage: >
    ENTSCHIEDEN: JA, sie ist ein Kriterium und nicht nur ein Riegel. "Jeder Block aller Dateien
    ist sauber abgegrenzt — beginnt mit style={{, endet auf }}, Klammerbilanz 0" ist die
    gestaltunabhaengige Form nach R2 und faengt die naechste Desynchronisation, ohne dass jemand
    sie vorher zaehlt. Sie wandert damit in die Abnahme von c895061d, nicht in diesen Auftrag.

scope:
  population_command: "node scripts/statische-inline-stile.mjs"
  population_at_writing: >
    316 Stellen / 198 offen (korrigiert durch c895061d, vorher faelschlich 197).
    Messung, KEINE Bedingung.
  pfade:
    - scripts/statische-inline-stile.mjs
    - resources/planner/hausplaner/__tests__/rohwertZusage.test.ts
  ausschluesse:
    - stelle: "AUF38-MW-5 — T.a.b gilt als dynamisch (Z114 nimmt nur eine Ebene)"
      grund: >
        P3, null Fundstellen gemessen. Eine Aenderung an der Token-Tiefe waere eine Aenderung
        DER DEFINITION, und die gehoert nicht in eine Werkzeug-Nachbesserung. Wird ein Posten,
        wenn eine Fundstelle entsteht — der Zaehllauf meldet sie dann selbst.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Ein Vorlagen-Ausdruck mit fremdem Bezeichner gilt als dynamisch."
    typ: behavioural
    kritikalitaet: P2
    befund: AUF38-MW-4
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        istStatisch("style={{ width: `${breite}px` }}") === false.
        Heute true — der Ausdruck wird aufgeloest, bleibt aber INNERHALB der Backticks und wird
        eine Zeile spaeter mit der Zeichenkette entwertet.
    beleg: testausgabe
    warum_zuerst: >
      MW-1 und MW-2 zaehlten zu WENIG. MW-4 zaehlt zu VIEL — und nur diese Richtung erzeugt
      falsche Arbeit: ein dynamischer Stil gilt als statisch und wuerde zur Umstellung in eine
      Klasse beauftragt. Der Generator hat unabhaengig dieselbe Reihenfolge vorgeschlagen.
      Heute null Fundstellen mit Wirkung; das ist ein Glueck, keine Absicherung.

  - id: K-02
    aussage: "'?' und '...' werden erst nach dem Entwerten der Zeichenketten geprueft."
    typ: behavioural
    kritikalitaet: P2
    befund: AUF38-MW-3
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        istStatisch("style={{ content: '?' }}") === true und
        istStatisch("style={{ fontFamily: 'Foo ... Bar' }}") === true.
    beleg: testausgabe

  - id: K-03
    aussage: "Die Reichweite des Werkzeugs steht im Werkzeug, nicht in einem Votum."
    typ: presence
    kritikalitaet: P3
    pruefung:
      befehl: "head -30 scripts/statische-inline-stile.mjs"
      erwartet: >
        Ein Satz im Kopf: Grundgesamtheit sind .tsx; von 114 .ts-Dateien der Insel traegt genau
        eine ein style={{ — app/stil/tokenVariablen.ts, die Token-Datei selbst.
    beleg: rohausgabe
    grenze: >
      KEINE Zusage daraus bauen. Eine Zusage ueber die .ts-Dateien waere eine Zusage ueber eine
      Menge, die niemand pflegt, und sie wuerde beim ersten neuen Token-Modul rot ohne Fehler.
      Ein Satz im Kopf ist hier die richtige Form — er beantwortet die Frage, die sonst jeder
      Pruefer neu stellt.

  - id: K-04
    aussage: "Der neue Sollwert wird gemessen und berichtet."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs"
      erwartet: >
        Vollstaendiger Lauf im Bericht. Der Sollwert fuer Scheibe 5 (ConfigWizard) kommt aus
        DIESEM Lauf. Aendert sich durch MW-4 eine Zahl, ist das der eigentliche Befund.
    beleg: rohausgabe

  - id: K-05
    aussage: "Die Eichung der drei abgenommenen Scheiben traegt weiterhin."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs"
      erwartet: "StartView 0 offen · HausplanerStudio 0 offen · FachFlaeche 0 offen"
    beleg: rohausgabe
    begruendung: >
      Ein Massstab, der sich aendert, muss beweisen, dass er keine erteilte Freigabe rueckwirkend
      entwertet. Faellt eine der drei von 0 weg, ist das ein HALT an den Planner — keine
      Nachbesserung, keine Anpassung der Zahl.

  - id: K-06
    aussage: "Kein Produktionscode, kein Buendel beruehrt, Gates ohne Regression."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD -- public resources/planner/hausplaner/app && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "erster Befehl leer; Gates 0/0/0/0, Insel >= 1321 (Stand nach c895061d)"
    beleg: testzaehler-vorher-nachher

selbstnachweis:
  gegenprobe: >
    Je Kriterium die Korrektur zurueckdrehen und zeigen, dass die zugehoerige Zusage rot wird.
    Nach R8 ist eine Korrektur ohne Gegenprobe nicht belegt.
```

---

## Zwei Dinge, die aus `c895061d` als Lehre bleiben

**1. Die Längentreue war die richtige Entscheidung, und sie stand nicht im Auftrag.**
`ohneKommentare` ersetzt jedes Zeichen durch ein Leerzeichen und behält die Zeilenumbrüche, damit
Zeilennummern gültig bleiben. Ein Werkzeug, das Fundstellen meldet, darf die Fundstelle nicht
verschieben — sonst misst es richtig und zeigt falsch. **Das gehört ab jetzt in jeden Auftrag, der
Quelltext maskiert oder normalisiert:** *die Maske ist längentreu, oder die Zeilennummer ist kein
Beleg mehr.*

**2. Der zweite Fehler wäre der spiegelverkehrte erste gewesen.**
Ein `//` innerhalb von `'url(//cdn…)'` ist kein Kommentar. Wer Kommentare überspringt, ohne
Zeichenketten zu kennen, baut denselben Fehler in der anderen Richtung — und der Generator hat das
selbst verriegelt, bevor jemand danach gefragt hat.

**3. Seine eigene Offenlegung ist der Punkt, den ich am höchsten bewerte.**
Er hat gemeldet, dass seine erste Fassung der Längentreu-Zusage die Leerzeichen **von Hand gezählt**
hatte und prompt falsch war (9 statt 7) — *„derselbe improvisierte Maßstab, nur zwei Ebenen tiefer"*.
Das ist genau die Fehlerklasse, gegen die dieses ganze Werkzeug gebaut wurde, gefunden am eigenen
Werk. **Wer den eigenen Maßstab mit demselben Misstrauen behandelt wie fremde Arbeit, braucht die
Regel nicht mehr, gegen die er verstoßen hätte.**

---

## Reihenfolge (Stand 00:45)

1. **B-01** (⚡) — `.ai-workflow/` versionieren **und 223 ungesicherte Commits sichern**.
   Das ist der einzige wirklich offene Posten mit Risiko. Blatt:
   `generator-auftrag-b01-ai-workflow-sichern.md`.
2. **Dieses Blatt** (`AUF-38-MW-N2`) — klein, drei Befunde, davon einer mit gefährlicher Richtung.
3. **AUF-38 Scheibe 5** (ConfigWizard) mit dem Sollwert aus dem korrigierten Lauf.

Nichts davon fasst `public/*` an. Die aufgeschobene **headful-K7** des Evaluators an Scheibe 4 kann
parallel laufen — das Bündel steht still auf `a2a83e72`.

---

<details>
<summary><strong>Historie — der ursprüngliche Kopf vom 00:40, überholt durch <code>c895061d</code></strong></summary>

Der erste Kopf trug `id: AUF-38-MW-N1` mit acht Kriterien, darunter:

- `K-01`/`K-02` — Kommentar-Überspringung beim Einstufen + Live-Fundstelle
  `WerkzeugGruppenMenue.tsx:82` ⇒ **gebaut in `c895061d`**, gemessen 8 statt 7
- `K-03` — jeder Block sauber abgegrenzt, über alle Dateien ⇒ **gebaut in `c895061d`**,
  Gegenprobe 3 rot
- `K-06` — Eichung der drei abgenommenen Scheiben bleibt 0 ⇒ **gegengerechnet, trägt**
- `K-07`/`K-08` — kein Produktivcode, Gates ⇒ **belegt**, Insel 1315 → 1321

Sie liegen jetzt beim **Evaluator** als Abnahme von `c895061d`, nicht mehr beim Generator.
Der Kopf ist nicht gelöscht, weil das Blatt zu diesem Zeitpunkt bereits committet und übergeben
war — **ein übergebenes Blatt wird ergänzt, nicht ersetzt** (R6).

</details>
