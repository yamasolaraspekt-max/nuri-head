# AUF-83-T3-N1 — Zeile 1 verschlanken, damit der Zeichenbereich wirklich gewinnt

*Planner, 30.07.2026, 08:55 CEST. Nachbesserung zu `AUF-83-T3`.
**Auslöser: das Votum des Evaluators — K-08 ROT, `−20 px` statt Gewinn.***

> **K-08 fällt NICHT.** Ich habe an T3 drei Kriterien anders geschnitten, weil sie sich als falsch
> geschnitten erwiesen. **Dieses hier ist nicht falsch geschnitten — es ist verfehlt.**
>
> *Yamas Auftrag vom 29.07. sagt in einem Satz, worum es geht: „der Zeichenbereich soll mehr Platz
> bekommen." **K-08 ist nicht ein Kriterium unter neun. Es ist das Ziel.** Ein Ziel fallen zu
> lassen, weil es rot gemessen wurde, wäre die teuerste Abkürzung, die dieses Projekt kennt.*

```yaml
auftrag:
  id: AUF-83-T3-N1
  status: aktiv
  spur: B
  heimat: ticket
  ziel: >
    Zeile 1 gibt so viel Hoehe zurueck, dass die Buehne gegenueber `d78c2466` waechst statt zu
    schrumpfen. Kein Inhalt geht verloren — was nicht in die Zeile passt, wandert in ein
    Ueberlauf-Menue.
  nicht_ziel: >
    KEINE Aenderung an Zeile 2 (Arbeitsbereiche) oder Zeile 3 (Werkzeugzeile, AUF-70).
    KEIN Wegfall von Objektname, Uebernehmen-Knopf oder Staleness-Pille — sie sind die Zusage
    aus T2 und tragen Fachlogik.
    KEINE zweite Statusquelle. `objektkopf.ts` rechnet nicht und darf es auch weiter nicht.
    KEIN Eingriff in Scheibe 7 ausser den Zeilen von Zeile 1.

geerbte_zusagen:
  befehl: "grep -rl 'objektkopf\\|hp-ok-' resources/planner/hausplaner/__tests__/ resources/planner/hausplaner/__domtests__/ tests/Feature/Hausplaner/"
  auflage: >
    Die LISTE steht in der Quittung, jede Datei angesehen. **`UebernahmeKnopfTest.php` und
    `objektkopf.test.ts` sind seit T3 verschaerft** — sie duerfen nicht geschwaecht werden, um
    Platz zu schaffen.

measurements:
  - id: M-01
    command: "Objektseite, 1440x900, Expertenmodus: document.querySelectorAll('[data-schiene]')[0].parentElement.getBoundingClientRect()"
    observed_value: "vorher (d78c2466) height 594 · nachher (T3) height 574 · Differenz -20"
    observed_at_commit: "d78c2466 gegen Arbeitsbaum-T3"
    observed_at: "2026-07-30T08:47:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "der Befund selbst — gemessen vom Evaluator, zweimal vorher, dreimal nachher, identisch"
  - id: M-02
    command: "grep -c 'overflow\\|Overflow\\|ueberlauf' resources/planner/hausplaner/app/HausplanerApp.tsx"
    observed_value: 16
    observed_at_commit: "a49b0f9c"
    observed_at: "2026-07-30T08:53:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "gap proof — alle 16 sind CSS-Eigenschaft `overflow`, KEIN Ueberlauf-Menue. Yamas Punkt 2 nennt eines"

kriterien:
  - id: N-01
    aussage: "Die Buehne ist auf der Objektseite HOEHER als vor T3."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Objektseite, 1440x900, Expertenmodus. Vorher-Stand ueber `git archive d78c2466`
        herstellen (R22), dieselbe Messung wie M-01, je zweimal.
      erwartet: >
        `height` groesser als **594**. **Kein Sollwert fuer den Zuwachs** — er wird gemessen und
        berichtet. **Aber kleiner-gleich 594 ist rot**, und dann ist der Auftrag nicht erfuellt.
    beleg: zwei Rohausgaben vorher, zwei nachher
    ausgefuehrt_von: evaluator

  - id: N-02
    aussage: "Zeile 1 traegt hoechstens drei Dinge nebeneinander."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        Links **Geschoss**, Mitte **Objektname** (gekuerzt mit `title`, nicht umgebrochen),
        rechts **Speichern**. **Alles andere liegt im Ueberlauf-Menue** — Uebernehmen-Knopf,
        Staleness-Pille, Speicherstatus-Pille.
    beleg: Bildschirmfoto + DOM-Auszug
    begruendung: >
      Gemessen traegt Zeile 1 heute **sechs** Dinge: Geschoss-Waehler · Objektname mit Adresse ·
      Uebernehmen-Knopf · Staleness-Pille · Gespeichert-Pille · Speichern-Knopf.
      **Sechs Dinge in einer Reihe sind der Grund fuer die 20 px** — und fuer den Nebenbefund des
      Evaluators, dass die Zeile bei 1024 px aus dem Fenster laeuft.

  - id: N-03
    aussage: "Das Ueberlauf-Menue verliert nichts und erfindet nichts."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner:dom -- --filter=objektkopf"
      erwartet: >
        Uebernehmen-Knopf, Staleness-Pille und Speicherstatus sind **im geoeffneten Menue**
        erreichbar, mit demselben `action`, demselben Status und demselben Verbot einer zweiten
        Statusquelle. **Der Knopf bleibt ein `form`-Submit**, kein neuer Pfad.
    beleg: testausgabe + Bildschirmfoto des geoeffneten Menues
    gegenprobe: >
      Den Uebernehmen-Knopf aus dem Menue entfernen ⇒ MUSS rot werden.
      *Eine Zusage, die nur das Vorhandensein der Zeile prueft, faengt einen verlorenen Knopf nicht.*

  - id: N-04
    aussage: "Bei 1024 px laeuft nichts aus dem Fenster."
    typ: absence
    kritikalitaet: P2
    pruefung:
      typ: visuell
      schritte: "1024 px, Objektseite, Expertenmodus"
      erwartet: "Kein abgeschnittenes Element, kein waagerechter Bildlauf, Speichern sichtbar."
    beleg: Bildschirmfoto
    begruendung: >
      **Nebenbefund des Evaluators, mit Bild belegt:** bei 1024 px wird die Staleness-Pille
      abgeschnitten und der Speichern-Knopf ist ohne Scrollen nicht sichtbar.
      *Er hat es ausdruecklich NICHT als K-01-Fehlschlag gefuehrt, weil K-01 nur fuer 1440 px
      geprueft war. Genau deshalb bekommt es hier ein eigenes Kriterium.*

  - id: N-05
    aussage: "Gates ohne Regression, Scheibe 7 unveraendert."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "Gates 0/0/0/0; Scheibe 7 bei 78 offen — oder Abweichung Zeile fuer Zeile begruendet"
    beleg: testzaehler + rohausgabe

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung mit Votumszeile am Zeilenanfang."
  vorher_wert_pflicht: >
    **Vor dem Bau festhalten:** die Buehnenhoehe im aktuellen Arbeitsbaum (erwartet 574) und die
    Zahl der Elemente in Zeile 1 (erwartet 6). *Ohne diese zwei Zahlen ist N-01 nach dem Bau nicht
    mehr belegbar.*
  commit_gehoert_dazu: >
    **`git commit -- <pfade>` ist Teil von `umgesetzt`** (kern.md, seit 30.07. 08:34).
    Die Meldung traegt den Hash.
```

---

## Warum K-08 nicht fällt, obwohl ich drei andere Kriterien geschnitten habe

**K-01, K-02 und K-04 habe ich anders geschnitten, weil sie sich als falsch geschnitten erwiesen** —
die Arbeit war schon getan, oder das Kriterium verlangte etwas, das seine eigene Grenze verletzte.

**K-08 ist nicht falsch geschnitten. Es ist verfehlt.** Der Unterschied ist der ganze Auftrag:

> Yama, 29.07.: *„der Zeichenbereich soll mehr Platz bekommen."*

**Der Evaluator hat den Beweis geliefert, dass genau das nicht passiert ist** — zweimal vorher,
dreimal nachher, identische Werte, Vorher-Stand per `git archive d78c2466` hergestellt und
danach per `md5` nachweislich restauriert.

*Ein Ziel fallen zu lassen, weil es rot gemessen wurde, wäre die teuerste Abkürzung, die dieses
Projekt kennt — und sie wäre nach genau dem Muster gebaut, das wir heute den ganzen Tag abstellen.*

## Und der Grund steht in Yamas eigenem Auftragstext

**Sein Punkt 2 nennt für die Kopfleiste: *„rechts Speicherstatus/Speichern/Undo/Redo/Overflow"*.**

**Gemessen: es gibt kein Überlauf-Menü.** Alle 16 Treffer auf `overflow` in `HausplanerApp.tsx`
sind die CSS-Eigenschaft.

**Das fehlende Stück ist also nicht neu zu erfinden — es stand von Anfang an im Auftrag und ist
übersehen worden. Von mir.**
