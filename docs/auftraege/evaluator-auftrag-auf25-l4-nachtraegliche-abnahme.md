# AUF-25 / L4 — nachträgliche Abnahme: gebaut am 25.07., nie geprüft

**An den EVALUATOR** · *Geschnitten 30.07. 22:47*

## Warum es diesen Auftrag gibt

**Der Bau ist fünf Tage alt und hat nie ein Votum bekommen.**

```text
Generator-Commit   17c8be22   25.07. 17:00   "L4 (AUF-25): 19 Fachplaner-Flaechen
                                              statt 'Konfigurator folgt'-Toast"
Basis              6bb38035
Umfang             6 Dateien, 1052 insertions / 208 deletions
Votum im Ledger    KEINES
Tafelzeile         gab es bis heute 22:13 nicht
```

*Ich habe den Posten heute Abend zuerst als „ungebaut, fünf Tage unsichtbar" gemeldet — **das war
falsch**, gemessen ist er gebaut. Was fehlt, ist nicht der Bau, sondern die Buchhaltung.
**Ein fertiger Bau ohne Abnahme zählt in keiner Bilanz, und niemand hat je geprüft, ob er hält,
was das Blatt verlangt.***

## Der Prüfstand

```text
git diff 6bb38035..17c8be22
```

**Das Auftragsblatt liegt:** `docs/auftraege/generator-auftrag-l4-fachplaner-flaechen.md`
(25.07., Spur A, *„Vorbedingung: keine"*).

## Was heute im Baum steht

```text
app/FachFlaeche.tsx                    223 Z.
app/dashboard/fachFlaechen.ts          525 Z.
__tests__/fachFlaechen.test.ts          95 Z.,  22 assert
```

## Fragen, die dieser Prüfstand beantworten muss

```yaml
  - id: E-01
    frage: "Wie viele Flaechen sind es wirklich?"
    lage: >
      Der Auftrag nennt **20**, der Commit-Text **19**, mein `grep -c "id:"` liefert **21**.
      **Drei Zahlen, keine geprueft.** *Nicht die Zahl im Blatt uebernehmen — selbst zaehlen
      und sagen, wonach gezaehlt wurde.*
    hinweis: >
      Mein grep zaehlt womoeglich Felder statt Flaechen. Genau der Fall, den ich heute
      dreimal selbst hatte: der Befehl misst die Gestalt, nicht die Sache.

  - id: E-02
    frage: "Ist der Vertroestungs-Toast wirklich fort — oder nur an dieser Stelle?"
    nachweis: >
      `grep -rn "Konfigurator folgt" resources/planner/hausplaner/` ueber ALLE Dateien.
      Eine Zusage prueft es bereits (`assert.doesNotMatch(studio, /Konfigurator folgt/)`),
      **aber sie liest nur `HausplanerStudio.tsx`.**
    gegenbeweis: >
      Setze den Toast in eine ANDERE Datei der Insel und lass die Zusage laufen.
      Bleibt sie gruen, prueft sie einen Ausschnitt und nennt ihn das Ganze.

  - id: E-03
    frage: "Haelt Kante 4 des Auftrags?"
    lage: >
      Der Auftrag verlangt: *„die Flaeche darf nicht so aussehen, als koennte sie rechnen"* —
      **keine Schaltflaeche „Berechnen", jedes Feld `disabled`, der Grund als TEXT in der
      Flaeche und nicht nur im Tooltip.**
    gegenbeweis: >
      Entferne den Grund-Text und lass die Suite laufen. Bleibt sie gruen, ist Kante 4
      unverriegelt — *und dann steht sie im Blatt und in keinem Test.*

  - id: E-04
    frage: "Die Mutationsprobe — was fangen die 22 assert ueberhaupt?"
    nachweis: >
      Mindestens 8 Mutationen VOR jedem Urteil: eine Flaeche entfernen · zwei Modulnamen
      vertauschen · ein Feld von `disabled` auf aktiv setzen · den Leerzustand loeschen.
      **Wie viele kommen durch?**
    hinweis: >
      *Ueber die acht AUF-48-Scheiben kamen 38 von 52 durch. Rechne mit einem aehnlichen Bild,
      aber miss es. Die Zahl gehoert in das Votum, auch wenn sie 0 ist.*

  - id: E-05
    frage: "Ist die Flaeche seit dem 25.07. beschaedigt worden?"
    lage: >
      `FachFlaeche.tsx` wurde am **27.07. im Rahmen von AUF-38 Scheibe 3** angefasst
      (Inline-Stile). **Der Prüfstand ist `6bb38035..17c8be22` — aber das Urteil betrifft
      den Stand von HEUTE.**
    nachweis: >
      Beides messen und getrennt melden: hielt der Bau am 25.07., und haelt er heute noch?
```

## Auflagen

- **Kein Produktivcode.** Ergibt die Prüfung Rot, geht es als Nachbesserung an den Generator.
- **Artefakt statt Behauptung** — Rohausgabe je Frage.
- **L-01 mit Anker**, falls im Browser gemessen wird: HTTP 200 · mindestens ein `canvas` ·
  Titel enthält „Hausplaner". *Auch melden, wenn alles gut aussah.*
- **Kein Merge, kein Push.**

*Dieser Auftrag ist ausdrücklich kein Vorwurf an den Generator: er hat gebaut, gemeldet und die
Kante eingehalten. **Die Lücke ist meine** — ich habe den Posten nie auf die Tafel gesetzt und
nie zur Abnahme gegeben.*
