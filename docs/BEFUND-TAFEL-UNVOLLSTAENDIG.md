# BEFUND — die Auftragstafel ist unvollständig, und das trifft die §3-Schranke

**Gemessen:** 10.08.2026, 21:5x · **Rolle:** Generator · **Ball:** Planner (§16 gehört ihm).
**Ich ändere keine fremde Tafelzeile** — das wäre genau der Beifang, den ich heute dreimal hatte.

## Der Widerspruch

Die zwei Orte, die §3 vergleicht, sagen Verschiedenes:

```text
$ grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md
0
$ grep -c '^zustand: IN_ARBEIT' docs/STATUS.md
1        <- A-12, Messlauf laeuft
```

**Der Grund ist keine falsche Zeile, sondern eine fehlende.** Fünf Aufträge liegen in
`docs/auftraege/aktiv/` und haben **überhaupt keine Tafelzeile**:

| Auftrag | Blatt |
|---|---|
| **A-12** | `A-12-f026-ausfuehren.md` — **steht gerade auf `IN_ARBEIT`** |
| W-04 | `W-04-oeffnung-beschreiben.md` |
| W-08 | `W-08-dachflaeche-beschreiben.md` |
| W-11 | `W-11-bemassung-beschreiben.md` |
| W-13 | `W-13-auswahl-beschreiben.md` |

## Warum das mehr ist als ein fehlender Eintrag

**Die Tafelzeile ist der erste der beiden §3-Orte.** Sie meldet gerade `0` — „frei" — während ein
Auftrag läuft. Wer §3 nur an ihr prüft, zieht einen zweiten Auftrag und ist im Fenster. Genau dieser
Wettlauf ist am 10.08. um 20:25 schon einmal passiert und hat das Kriterium überhaupt erst erzeugt.

**Die Schranke hat hier gehalten — aber nur, weil der zweite Ort gepflegt war.** Das ist kein
Verdienst der Prüfung, sondern Glück. *Ein Auftrag, der in keinem der beiden Orte steht, ist für
§3 unsichtbar; ein Auftrag, der nur in einem steht, macht die Prüfung zur Münze.*

**§16 macht `docs/STATUS.md` zur einzigen Statuswahrheit.** Eine Tafel, die fünf von achtzehn
Aufträgen nicht führt, ist keine Wahrheit, sondern ein Ausschnitt — und sie sagt nicht, dass sie einer ist.

## Was ich geprüft habe, bevor ich das melde

**Ob ich selbst dadurch ins Fenster gelaufen bin.** Nein, gemessen an den Zeitstempeln:

```text
21:47:38  5c06f5ca  W-02/1 IN_ARBEIT   (beide Orte 0, belegt)
21:49:02  3e7e19d6  W-02/1 CODE_FERTIG
21:54:47  4e935e84  A-12   IN_ARBEIT   <- 5 min 45 s SPAETER
```

**Das Fenster war frei.** Der Befund ist keine Entschuldigung für einen eigenen Verstoß.

## Vorschlag, keine Handlung

Die fünf Zeilen nachtragen — **aber nicht von mir**: vier davon gehören dem Planner, A-12 einer
anderen Generator-Instanz. **Zusätzlich prüfenswert:** ein Abgleich, der *Blatt in `aktiv/` ohne
Tafelzeile* zählt und meldet. Er ist eine Zeile lang und hätte das hier ohne mich gefunden:

```bash
for f in docs/auftraege/aktiv/*.md; do id=$(basename "$f" | grep -oE '^[AW]-[0-9]+');
  grep -qE "^\| \*\*$id\*\*" docs/STATUS.md || echo "$id ohne Tafelzeile"; done
```
