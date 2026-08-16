# Schritt I an den Evaluator — Yama hat Weg 1 gewählt

> **Release-Prüfer, 16.08. ~19:5x, auf Yamas Entscheidung: „dann bitte weg 1 machen".**
> Weg 1 war: *Schritt I jetzt, dann J.* Du bist damit die Rolle, an der die gesamte
> Worktree-Umstellung hängt — und der Weg ist frei geräumt.

## Die Lage in vier Zeilen

```
Tor in allen Baeumen        6 von 6      -> die Sperre ist SCHARF
docs/STATUS.md schreiben    nur integrator, und der ist nicht SCHREIBEND
A-37 im Log                 CODE_FERTIG (fb59f6cc, 19:38, Generator, 18 Kriterien)
Schritt J                   wartet auf Schritt I — auf dich
```

**Die Statuswahrheit ist eingefroren.** Fünf Rollen dürfen nicht mehr schreiben, die sechste noch
nicht. Das löst sich, sobald V6 vollständig belegt ist — und der offene Teil ist deiner.

## Was du hast — gemessen, damit du es nicht selbst prüfen musst

```
dein Baum        81f97845   von mir um 19:5x per Fast-Forward nachgezogen
                            (0 voraus · 0 uncommittet · HEAD unbewegt · Rueckfall d7603a51)
Tor im Baum      1          du kannst die Sperre selbst fahren
A-37-Blatt       1          liegt bei dir
Ballbesitz       0 andere   du hast nichts, was daneben liegt
```

## Was Schritt I verlangt

Wörtlich aus `2-WANN-BIN-ICH-DRAN.md`: *„Evaluator prüft **positive und negative** Sperrfälle
unabhängig."* Und aus `1-AUFTRAG.md`, V6: *„Eigener Rollen- und Checkoutschutz aktiv — positive
**und** negative Sperrfälle bestanden."*

**Beide Richtungen, und das „und" trägt das Gewicht.** Eine Sperre, die nur positiv geprüft ist,
kann alles sperren — auch das Erlaubte. Eine, die nur negativ geprüft ist, kann alles durchlassen.

**Was ich dir dazu ausdrücklich NICHT liefere: das Ergebnis.** Ich habe die Sperre heute mehrfach
gefahren und weiß, was herauskommt — aber Schritt I verlangt eine *unabhängige* Prüfung, und wenn
ich dir meine Zahlen vorlege, prüfst du meine Messung statt der Barriere. **Miss selbst.**

Was ich nenne, ist nur der *Umfang*, damit nichts vergessen wird:

- **positiv** — die Sperre greift, wo sie soll: jede der fünf Rollen, die `docs/STATUS.md` außerhalb
  des Integrations-Checkouts ändern will.
- **negativ** — sie greift nicht, wo sie nicht soll: der Integrator selbst; und jede Rolle bei
  *anderen* Dateien. Eine Barriere, die Blätter und Werkzeuge mitsperrt, wäre falsch.
- **die Kanten** — A-37 nennt sechs (K1–K6), der Generator hat sie in `97f1dd00` einzeln belegt.
  Sein Beleg ist der Prüfgegenstand, nicht die Prüfung.

## Ein Umstand, den du kennen musst: du kannst `docs/STATUS.md` nicht schreiben

Deine Abnahme wird den Zustand wechseln wollen — das geht heute **nicht über die Datei**. Der Weg,
der trotz Sperre funktioniert, ist der **Wortlaut-Commit**, den A-41 eingeführt hat:

```
evaluator: zustand: A-37 · <ZUSTAND> · <rolle> · abnahme 97f1dd00
```

Gemessen und nicht vermutet: der Generator-Commit `fb59f6cc` hat genau so den Zustand gesetzt und
`docs/STATUS.md` **0 mal** angefasst. **Das ist keine Umgehung der Sperre**, sondern das Verfahren,
für das sie gebaut wurde.

**Zwei Fallen darin, beide von mir heute selbst getreten:**

1. **Ein Zustands-Commit trägt genau eine Kennung.** Ich hatte zwei Aufträge in einen gepackt; der
   zweite stand nur im Fließtext und war für das Werkzeug unsichtbar.
2. **Das Ballfeld verlangt eine Rolle** (`[a-z-]+`), keinen Gedankenstrich. Mein `· — ·` wurde mit
   *„NICHT IM WORTLAUT, deshalb nicht gezählt"* abgewiesen.

Nach deinem Commit lohnt ein `bash scripts/status-erzeugen.sh --tafel` — dort steht sofort, ob dein
Zustand gezählt wurde oder unter „nicht im Wortlaut" landet.

## Eine Berichtigung, die dich betrifft, weil sie in der Lehre steckt

Der Generator schreibt in `fb59f6cc`: *„Um 20:0x holte der Plan-Prüfer als letzter den
Integrationszweig — Hälfte 2."* **Das trifft nicht.** Gemessen am Reflog: den Plan-Prüfer-Baum habe
**ich** um 19:36 per Fast-Forward nachgezogen (`dab4086b` → `c698a10e`); seine eigenen Commits
beginnen erst 19:42.

Der Unterschied ist keine Rechthaberei, er ist die Lehre: **eine fremde Rolle hat die Zündbedingung
erfüllt, ohne es zu wissen.** Liest man es als „er hat selbst nachgezogen", verschwindet genau die
Warnung — dass eine selbst-konditionierte Barriere von jemandem ausgelöst werden kann, der ihre
Bedingung gar nicht im Blick hatte. Ich habe P-07 behoben und dabei ungesehen einen zweiten Schalter
umgelegt.

## Was danach kommt

Nach deinem Votum ist V6 vollständig, und Schritt J liegt bei Yama — nicht bei mir. **Ich habe
Schritt J heute zweimal nicht erteilt und erteile ihn auch jetzt nicht**, wo meine eigene Handlung
den Zeitdruck erzeugt hat.

---

## ⚠ NACHTRAG 19:5x — WARTE. Die Fertigmeldung ist überholt.

**Fang noch nicht an.** Zwischen der Zustellung dieser Anweisung und jetzt hat sich der
Prüfgegenstand bewegt. Zeitachse, aus dem Log gemessen:

```
16:56:59   A-37 BEREIT                     (planner)
19:38:16   A-37 CODE_FERTIG                (generator) — "ALLE ACHTZEHN KRITERIEN"
19:43:47   A-37-19 kommt INS BLATT         (planner, 4a10abca)
19:49:23   A-37-19 GEBAUT                  (generator, 1c36544e, commit-pruefen.sh)
   -> danach KEINE neue Fertigmeldung
```

```
Kriterien im Blatt heute      19   (A-37-1 … A-37-19)
Kriterien in der Fertigmeldung 18   ("ALLE ACHTZEHN")
```

**Die gültige `CODE_FERTIG`-Meldung deckt den heutigen Bau nicht ab.** Sie ist elf Minuten älter als
das neunzehnte Kriterium und fünf Minuten älter als dessen Eintrag ins Blatt. Der Bau dazu existiert
(33 Zeilen in `commit-pruefen.sh`), gemeldet ist er nicht.

**Warum das dich trifft:** du würdest gegen eine Meldung prüfen, die einen anderen Umfang hat als
der Gegenstand. Egal wie sorgfältig — das Ergebnis wäre in dem Moment überholt, in dem jemand die
Differenz bemerkt. Genau das ist dem Plan-Prüfer um 19:47 passiert; er hat seine Bestätigung um
19:53 selbst zurückgenommen: *„sie war schon beim Schreiben überholt."*

**Was fehlt, ist ein Commit des Generators, kein Bau:** eine neue Fertigmeldung im Wortlaut, die
neunzehn Kriterien nennt und `1c36544e` als Bau-SHA führt. Danach steht dein Ziel still, und Schritt
I ist in einem Zug fahrbar.

**Was ich dazu nicht tue:** den Generator anweisen. Er hat gebaut und wird melden; das ist seine
Reihenfolge, nicht meine. Ich melde nur, dass zwischen Bau und Meldung eine Lücke steht — und dir,
dass du nicht hineinläufst.

**Zur Lage insgesamt:** die Statuswahrheit bleibt bis dahin eingefroren. Das kostet weiterhin nichts
Messbares — es liegt kein `ABGENOMMEN` beim Release-Prüfer, gemessen und nicht gehofft.
