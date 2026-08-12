# Die §3-Schranke zählt fünf Aufträge nicht — Entscheidung des Planners, 12.08.

```yaml
anlass: "Generator-Befund in c528161c, beim §3-Beleg fuer B5 selbst aufgefallen. Er hat den
         Befehl ausdruecklich NICHT geaendert: 'ihn einseitig umzuschreiben waere eine stille
         Aenderung an einer Schranke, die anderen gehoert. Entschieden wird es vom Planner.'"
art: "ENTSCHEIDUNG + Vorlagen-Berichtigung. Keine Regeländerung ohne §3-Platz."
ballbesitz: "planner (entschieden), Umsetzung in ARBEITSREGELN wartet auf den §3-Platz"
prioritaet: "P1 — die Schranke versagt in die GEFAEHRLICHE Richtung"
```

## Der Befund, von mir selbst nachgemessen

```text
kanonisch   grep -cE '^\| \*\*[AW]-[0-9]+.*IN_ARBEIT'      ->  0     <- FALSCH
berichtigt  grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT'   ->  1     <- richtig
Weckruf des Planners (praefixfrei)                          ->  1     <- richtig
Zustandsfelder  grep -cE '^zustand: *IN_ARBEIT'             ->  1     <- richtig

Praefixe, die die Tafel wirklich traegt:
  17 A   ·   14 W   ·   3 B   ·   1 P   ·   1 M
  -> FUENF Zeilen sind fuer den kanonischen Befehl unsichtbar.
```

**Der laufende Fall ist der Beweis:** *B5 stand sichtbar auf `IN_ARBEIT` in Zeile 35, und der erste
§3-Ort meldete **frei**. Der Generator hat das selbst ausgelöst und selbst gemeldet.*

> **Warum das ernster ist als die Statusdrift von heute früh:** *jene wies in die **harmlose**
> Richtung — die Tafel behauptete mehr Sperre, als es gab. **Diese Lücke weist in die gefährliche:**
> die Schranke sagt „frei", während gebaut wird. Sie ist genau der Schutz gegen zwei gleichzeitig
> bauende Instanzen im geteilten Baum, und für B-, M- und P-Aufträge existiert sie nicht.*

## Der zweite Fund, und er ist der eigentliche — die Schranke hat keine Heimat

```text
grep -rlE '\[AW\]' docs/            ->  VIER Dateien:
  docs/STATUS.md                          (Kriteriumstext in Auftragsbloecken)
  docs/BEFUND-TAFEL-UNVOLLSTAENDIG.md     (Befund, der den Befehl zitiert)
  docs/auftraege/aktiv/W-01-fang-beschreiben.md     status: ENTWURF
  docs/auftraege/aktiv/W-02-wand-beschreiben.md     status: ENTWURF

grep -nE '\[AW\]' docs/ARBEITSREGELN.md   ->  0 TREFFER
```

### PRÄZISIERT 12.08. durch den Plan-Prüfer (`50505407`) — und er hat recht

```text
'IN_ARBEIT' in docs/ARBEITSREGELN.md   ->  VIER Treffer.  Der ZUSTAND ist verankert.
das Pruefmuster  '[AW]' dort           ->  0 Treffer.     Die METHODE ist unverankert.
```

> **Der Unterschied zählt, und meine erste Fassung hat ihn verwischt:** *ich schrieb „die Schranke
> ist nirgends als Regel verankert". Richtig ist: **die Regel ist verankert, das Werkzeug nicht.**
> Seine Folgerung: *„eine unverankerte Regel müsste neu beschlossen werden, ein unverankertes
> Werkzeug nur aufgeschrieben."* **Das senkt die Hürde für die Umsetzung erheblich** — es braucht
> keinen Beschluss von Yama, nur einen Eintrag. Und er übernimmt das breitere Muster für seine
> eigenen §3-Belege und meldet den Wechsel, statt ihn still zu machen.*

**Seine Zahlen, unabhängig gemessen und deckungsgleich mit meinen:** `[AW]` findet **31**
Tafelzeilen, `[A-Z]+-?[0-9]+` findet **36** — fünf Aufträge fallen durch.

> **Der „kanonische" Befehl ist als PRÜFMETHODE nirgends verankert.** *Er ist eine **Gewohnheit**, die sich
> über Auftragsblätter verbreitet hat — kopiert von Blatt zu Blatt, nie geprüft. **Das ist B7 in
> Reinform, beide Teile:** Mehrfachvorkommen sah wie Bestätigung aus *(vier Fundorte, null
> normative Quelle)*, und der Ort — mitten in Abnahmekriterien — sah wie Autorität aus.*

## ENTSCHIEDEN

```text
(1) ES GILT AB SOFORT   grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT' docs/STATUS.md
    Begruendung: deckungsgleich mit dem ZWEITEN §3-Ort (Zustandsfelder). Zwei Orte, die
    dasselbe zaehlen sollen, muessen dasselbe Muster haben — sonst ist die Doppelpruefung
    keine Kontrolle, sondern zwei verschiedene Fragen.

(2) DER PLANNER-WECKRUF BLEIBT wie er ist. Sein Muster ist praefixfrei und hat B5 korrekt
    gezaehlt. Das war Glueck und kein Verdienst — aber es ist die robustere Form und wird
    nicht auf die schwaechere umgestellt.

(3) DIE ZWEI ENTWURFS-BLAETTER werden nachgezogen (W-01, W-02) — sie stehen auf ENTWURF,
    da wirkt keine Rueckwirkung. HIER in diesem Vorgang erledigt.

(4) STATUS.md und ARBEITSREGELN.md NICHT ANGEFASST: beide liegen im Scope von B5
    (IN_ARBEIT beim Generator). Die Verankerung als Regel wartet auf den §3-Platz.
    Ohne Verankerung bleibt der Befehl eine Gewohnheit — dann war diese Entscheidung
    nur eine Notiz, und H-1 sagt, was eine Notiz wert ist.

(5) NICHT ENTSCHIEDEN, weil es Yama gehoert: ob die Praefixe M und P ueberhaupt
    Auftragszeilen sein sollen. Sie sind je EINE Zeile; wenn sie etwas anderes sind als
    Auftraege, ist die richtige Antwort vielleicht, sie aus der Auftragstafel zu nehmen
    statt das Muster zu erweitern. Gemessen, nicht entschieden.
```

## Was der Generator richtig gemacht hat, und warum ich es nenne

*Er hat den Befehl **nicht** geändert, obwohl er ihn in der Hand hatte und der Fehler offensichtlich
war. Seine Begründung: **„ihn einseitig umzuschreiben wäre eine stille Änderung an einer Schranke,
die anderen gehört."*** *Er hat stattdessen beide Zahlen und das berichtigte Muster gemeldet.*

> **Das ist der Unterschied zwischen einem Fund und einem Eingriff** — und er hat ihn an genau der
> Stelle gezogen, an der ich heute zweimal darüber gestolpert bin: bei der W-43-Registerzeile
> (Beifang) und beim A-17-Datensatz (Doppelung). *Wer eine fremde Schranke repariert, ohne zu
> fragen, hat sie geändert, nicht geprüft.*

## Offen, und zwar bei mir

```text
- Verankerung in docs/ARBEITSREGELN.md (§3)      wartet auf den §3-Platz
- Eintrag in docs/STATUS.md                      wartet auf den §3-Platz (B5-Scope)
- Vorlagen-Berichtigung fuer kuenftige Blaetter  mit der Verankerung zusammen
```
