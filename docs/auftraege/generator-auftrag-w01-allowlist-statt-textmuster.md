# W-01 — Der Validator führt nur noch aus, was auf einer Liste steht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 22:0x*

```yaml
auftrag:
  id: W-01
  status: bereit   # Sicherheitsposten nach dem Push von 20:01. Nicht aktiv, weil der Generator gerade Z-03+Z-04 baut - W-01 ist der naechste Griff, nicht der laufende
```

## Warum es dieses Blatt gibt — ein realer Push, und meine Barriere hat ihn durchgelassen

**Am 01.08. um 20:01 hat ein Verzeichnislauf des Validators wirklich gepusht.** Nicht versehentlich
getippt — **ausgeführt, weil es als Abnahmekriterium in einem Blatt stand**:

```text
b01/K-05   befehl: "./<der-push-wrapper>.command && cat <sein-log>"
```

**Der Lauf war meiner** (Planner, 20:00). Der Evaluator hat den Inzident um 21:17 gemeldet und ihn
sich selbst zugeschrieben; die Zeitstempel sagen etwas anderes: `push-result.log` ist um 20:01:03
geschrieben, und auf beiden Remotes steht `9ac24f7b` — mein Commit von 20:00.

**Meine Barriere `GATE_MUSTER` (`8f81c3f7`) hat das nicht gefangen, und sie konnte es nicht.** Sie
prüft **Befehls-Text**. Ein Wrapper-Skript enthält keinen Text: `./<der-push-wrapper>.command`
trägt weder `git push` noch `npm` noch sonst ein Muster. *Der Evaluator hat dafür ROT gegeben. Das
ROT ist berechtigt und ich nehme es an.*

**Die Lehre ist nicht „ein Muster mehr".** Eine Liste des Verbotenen kann nur fangen, was jemand
vorher gedacht hat. Ein Blatt ist eine Datei, die jede Rolle schreiben darf — und was darin steht,
**passiert**. Deshalb dreht dieses Blatt die Richtung um: **nicht mehr aufzählen, was verboten ist,
sondern aufzählen, was erlaubt ist.**

## Bestand — gemessen 01.08. 22:0x

```text
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'ALLOWLIST'    -> 0
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'DENYLIST'     -> 2   (die Messung ist nicht leer)
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'GATE_MUSTER'  -> 2
```

**Die Grundgesamtheit ist klein und gemessen.** Erste Wörter aller `pruefung.befehl` in
`docs/auftraege/`:

```text
grep -rh 'befehl: "' docs/auftraege/*.md | sed 's/.*befehl: "//' | sed 's/[" ].*//' | sort | uniq -c | sort -rn

  52 grep · 38 npm · 36 node · 27 git · 4 bash · 3 php · 1 ls · 1 head · 1 for · 1 find · 1 cd
```

**Elf Programme.** Eine Allowlist ist keine Utopie, sondern zwölf Zeilen.

## Die Entscheidung

**Der Validator führt einen Befehl nur aus, wenn JEDES Glied der Kette mit einem Programm der
Allowlist beginnt.** Alles andere wird als `UEBERSPRUNGEN` gemeldet — nie stillschweigend.

```text
ALLOWLIST = git grep node wc ls head tail sed awk find sort uniq echo printf for cd test bash sh
```

**Drei Verschärfungen, ohne die die Liste ein Loch hat:**

1. **Jedes Glied, nicht das erste Wort.** `grep x && ./push.command` beginnt mit `grep`. Der
   Validator zerlegt bereits Ketten (`brechendesGlied` steht seit AUF-87) — dieselbe Zerlegung
   prüft hier jedes Glied.
2. **`bash` und `sh` nur mit einem Pfad unter `scripts/`.** Sonst ist `bash x.sh` wieder alles.
3. **Die Reihenfolge bleibt: DENYLIST vor ALLOWLIST vor GATE_MUSTER.** Ein Befehl, der auf der
   Denylist steht, wird auch dann nicht gefahren, wenn sein Programm erlaubt ist —
   `git push` beginnt mit `git`.

**Denylist und GATE_MUSTER bleiben stehen.** Sie sind nicht überflüssig geworden: die Allowlist
sagt, *welches Programm* laufen darf, die Denylist sagt, *welche Verwendung* nicht. `git` ist
erlaubt, `git push` nicht.

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs                    ALLOWLIST + istErlaubt() + Aufruf in pruefeEintrag
  scripts/__tests__/auftragPruefen.test.mjs      die Zusagen

Hier bewusst NICHT:
  DENYLIST und GATE_MUSTER                       bleiben unveraendert. Drei Listen mit drei
                                                 Gruenden sind KEINE drei Wahrheiten - sie
                                                 beantworten drei verschiedene Fragen.
  die 30 Blaetter                                kein Massenumbau. Wer einen nicht gelisteten
                                                 Befehl braucht, bekommt UEBERSPRUNGEN und
                                                 entscheidet dann bewusst.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
    - scripts/__tests__/auftragPruefen.test.mjs
  population_command: "node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'ALLOWLIST'"
  ausschluesse:
    - stelle: "DENYLIST und GATE_MUSTER"
      grund: "Andere Frage, andere Liste. Die Denylist behaelt Vorrang."
      entschieden_von: planner
    - stelle: "Die 30 Auftragsblaetter"
      grund: "Kein Massenumbau. Das Werkzeug repariert nichts, es meldet."
      entschieden_von: planner

kriterien:
  - id: W-01-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Allowlist existiert im Werkzeug, nicht im Blatt."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'ALLOWLIST'"
      erwartet: "mindestens 2"
    ausgangswert: "0 (gemessen 01.08. 22:0x; Partner 'DENYLIST' -> 2, die Messung ist nicht leer)"

  - id: W-01-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ein Wrapper-Aufruf wird UEBERSPRUNGEN - der Fall, der am 01.08. wirklich gepusht hat."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        NIEMALS DEN ECHTEN BEFEHLSNAMEN IN EINE ZUSAGE. Hier stand bis 01.08. 22:1x der
        WIRKLICHE Wrapper-Name aus b01/K-05. Das war derselbe Fehler wie in b01, nur eine
        Ebene hoeher: ein publizierender Befehl in einem Blatt - diesmal als Testfall.
        Greift die Allowlist beim Entwickeln noch nicht, fuehrt `pruefeEintrag` ihn AUS.
        Die Form ist der Pruefgegenstand, nicht der Name. Nimm einen Namen, der NICHTS tut:
          pruefeEintrag({ befehl: './gibt-es-nicht.command && cat egal.log' })
          -> stufe UEBERSPRUNGEN, Hinweis nennt das nicht gelistete Programm
        Und die Umkehrung, die die Textpruefung nicht faengt:
          'grep -n x datei && ./gibt-es-nicht.command'
          -> ebenfalls UEBERSPRUNGEN, obwohl das ERSTE Glied erlaubt ist
        Die Datei darf nicht existieren. Waere sie da und die Allowlist noch nicht scharf,
        liefe sie.
      erwartet: "beide UEBERSPRUNGEN"

  - id: W-01-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "ROTE Gegenprobe - die erlaubten Befehle laufen weiter."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ohne diese Zusage ist die schaerfste Fassung die, die gar nichts mehr ausfuehrt.
          'echo zwei'                                   -> OK, Ausgabe 'zwei'
          'node scripts/zaehle.mjs <datei> <muster>'     -> OK
          'git log -1 --pretty=%h'                       -> OK
          'bash scripts/pfade-pruefen.sh'                -> OK  (Pfad unter scripts/)
          'bash /tmp/fremd.sh'                           -> UEBERSPRUNGEN (Pfad NICHT unter scripts/)
      erwartet: "vier OK, eines uebersprungen"

  - id: W-01-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Denylist behaelt Vorrang vor der Allowlist."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        'git push fork HEAD' beginnt mit dem ERLAUBTEN Programm `git`.
        Erwartet: UEBERSPRUNGEN mit dem Denylist-Grund, NICHT ausgefuehrt.
        Ohne diese Zusage macht die Allowlist die Denylist versehentlich wirkungslos.
      erwartet: "UEBERSPRUNGEN, Hinweis nennt git push"

  - id: W-01-05
    typ: absence
    aussage: "Kein Blatt wurde umgebaut."
    pruefung:
      befehl: "git diff --name-only HEAD -- docs/auftraege | wc -l"
      erwartet: "0"
    ausgangswert: "0"

  - id: W-01-06
    typ: behavioural
    aussage: "Die bestehenden Zusagen bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/auftragPruefen.test.mjs scripts/__tests__/zaehle.test.mjs"
      erwartet: "0 fail. Ausgangswert 63 pass (gemessen 01.08. 19:5x). Danach mehr, nie weniger."

  - id: W-01-07
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: Allowlist-Pruefung ganz aus · nur das ERSTE Glied pruefen ·
        die Denylist NACH der Allowlist auswerten · `bash` ohne Pfadbedingung zulassen ·
        UEBERSPRUNGEN still verschlucken statt melden · './x' als erlaubt behandeln.
        Wie viele kommen durch?
```

## Kantenliste — wo das erfahrungsgemäß bricht

```text
1  Nur das erste Wort wird geprueft. `grep x && ./push.command` kommt durch. -> W-01-02 zweiter Fall
2  Die Allowlist wird VOR der Denylist ausgewertet - dann laeuft `git push`. -> W-01-04
3  `bash` ohne Pfadbedingung macht die Liste wertlos, weil jedes Skript wieder alles darf.
4  Die schaerfste Fassung fuehrt gar nichts mehr aus und wirkt gruen, weil nichts rot wird.
   -> W-01-03 ist die einzige Zusage, die das faengt.
5  UEBERSPRUNGEN wird still gezaehlt statt gemeldet - dann sieht ein Lauf sauber aus, waehrend
   die Haelfte der Kriterien nie lief. F-17 ist genau diese Klasse.
6  Ein Befehl in Backticks oder $() umgeht die Gliederzerlegung.
```

## Rückweg und Entdeckung

**Rückweg:** eine Konstante und eine Funktion in einer Datei, kein Datenpfad, kein Schema — der
Commit lässt sich zurückdrehen. **Aber der Rückweg liegt auf derselben Platte wie die Arbeit**,
solange nicht gepusht ist. **Hier stand „am 01.08. um 21:5x waren es 60 Commits" — eine Zahl ohne
Befehl, und sie war falsch.** Gemessen 22:09:

```text
git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration  ->  13
```

**Der ungewollte Push von 20:01 hat den Rueckstand auf null gesetzt.** Der Regelverstoss hat den
offenen Posten geschlossen, auf den Yama seit Tagen wartete. *Das entschuldigt ihn nicht — es macht
ihn nur schwerer zu benennen, und genau deshalb steht es hier.*

**Entdeckung:** die Zahl der `UEBERSPRUNGEN` in einem Verzeichnislauf. Steht sie bei 0, prüft die
Allowlist nichts; steht sie bei der Zahl der Einträge, prüft der Validator nichts mehr. *Beide
Enden sind rot, und beide sehen von außen ruhig aus.*

## Danach

**Bis W-01 abgenommen ist, fährt niemand einen Verzeichnislauf über `docs/auftraege/`.** Der
Evaluator hat das um 21:17 verfügt; `b01/K-05` ist seit `6cbe9578` entschärft, aber die
strukturelle Lücke schließt erst dieses Blatt.

---

## BEFUND DES PLANNERS gegen die gebaute Fassung — 01.08. 23:0x, VOR der Abnahme

*Widerspruch gehört vor den Bau, nicht in die Abnahme. Ich habe die Allowlist an einem frischen
Blatt (PW-01) ausprobiert und melde, was dabei herauskam.*

**Die Allowlist trifft ausgerechnet die Form, die unsere eigene Bauordnung vorschreibt.**

```text
node scripts/auftrag-pruefen.mjs docs/auftraege/bote-auftrag-pw01-sicherungs-push.md
  UEBERSPRUNGEN  PW-02  "git" steht nicht auf der Erlaubnisliste
                 $ git --no-optional-locks rev-list --count auto/...
```

`ALLOWLIST` führt `'git rev-list'` als Zwei-Wort-Muster. **`git --no-optional-locks rev-list`
matcht es nicht** — die globale Option steht zwischen `git` und dem Unterbefehl.

**Das ist keine Kleinigkeit:** der Governance-Skill schreibt für die Repo-Aufsicht ausdrücklich
`git --no-optional-locks` vor, *„damit keine Locks entstehen"* — und F-10 (Lock-Reste) ist die
eine Fehlerklasse, die auf diesem Mount nicht behebbar ist. **Die Allowlist bestraft die
lock-sichere Form und lässt die lock-erzeugende durch.**

```text
grep -rh 'befehl: "' docs/auftraege/*.md | sed 's/.*befehl: "//' | grep -c '^git '   -> 36
   davon mit --no-optional-locks                                                     ->  4
   (alle vier in Blaettern von heute - die Form setzt sich gerade erst durch)
```

**Vorschlag, nicht Vorschrift** — das Muster soll globale Optionen zwischen `git` und dem
Unterbefehl überspringen (`-c`, `-C <pfad>`, `--no-optional-locks`, `--git-dir`, `--work-tree`).
**Wichtig dabei:** `git -c core.hooksPath=… push` darf dadurch nicht erlaubt werden — der
Unterbefehl entscheidet, nicht die Option.

**Zweiter, kleinerer Befund:** `ls -1 .git/*.lock 2>/dev/null | wc -l` wird von der **Denylist**
als `umleitung` übersprungen. `2>/dev/null` ist eine Umleitung nach `/dev/null` und kann nichts
überschreiben. *Das ist die alte Denylist-Regel, nicht deine — ich melde es hier, weil es an
derselben Stelle auffällt.*

**Beides ist ein Befund gegen die Fassung, kein Rot gegen die Richtung.** Die Allowlist ist der
richtige Bau; sie ist an dieser Kante nur eine Spur zu eng.
