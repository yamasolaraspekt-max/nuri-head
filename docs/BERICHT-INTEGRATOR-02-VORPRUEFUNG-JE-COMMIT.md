# BERICHT INTEGRATOR 02 — Vorprüfung je Commit, und was der Rückstand wirklich ist

```yaml
rolle: integrator
betriebsart: NUR_LESEND
gemessen: "16.08.2026, 16:52 CEST, Rueckstand HEAD..fork/auto/hausplaner-integration"
gegenstand: "Erzeugnis 2 (Herkunftszuordnung JE COMMIT) und die Vorarbeit zu 4 und 5 —
             alles rein lesend, kein Wort noetig."
warum_jetzt: "Yama: 'warum machst du nichts du bist sehr sehr faul.' Berechtigt.
              Ich habe auf SCHREIBEND gewartet und dabei uebersehen, dass die
              VORPRUEFUNG des Laufs keine Schreibrechte braucht. Sie ist jetzt gefahren."
```

## Der Befund in einem Satz

> **Der Rückstand ist kein Stapel von Lieferungen, der auf Abnahme wartet. Er ist das
> Arbeitsprotokoll von fünf Rollen — und er enthält NULL Zeilen Produktivcode.**

## Was gemessen wurde, mit Befehl

```text
$ git rev-list --count HEAD..fork/auto/hausplaner-integration            151
$ git rev-list --count --no-merges …                                      87   ← die Vorgaenge
$ git rev-list --count --merges …                                         64

Produktivpfade, je Verzeichnis ueber den GESAMTEN Rueckstand:
$ for p in resources app database routes config tests public bootstrap; do
    git rev-list --count HEAD..fork/auto/hausplaner-integration -- $p; done
  resources/ 0 · app/ 0 · database/ 0 · routes/ 0 · config/ 0 · tests/ 0 · public/ 0 · bootstrap/ 0

$ git log --no-merges --name-only --format='' … | cut -d/ -f1 | sort | uniq -c
  104 docs      20 scripts
```

**Acht Verzeichnisse, acht Nullen.** Keine Migration, keine Route, keine Testdatei, kein
Insel-Code. Der Lauf kann die Anwendung nicht beschädigen — **das Risiko liegt vollständig in der
Statuswahrheit selbst**, nicht im Programm.

## Was die 87 Vorgänge anfassen

| Klasse | Anzahl |
|---|---|
| **nur `docs/STATUS.md`** | **60** |
| `docs/STATUS.md` + weitere Dateien | 4 |
| ohne `docs/STATUS.md` | 23 |
| davon berühren `scripts/` | 16 |

**60 von 87 sind ein und dieselbe Datei.** Rund sieben von zehn Vorgängen des „Integrationslaufs"
sind Schreibvorgänge auf der Statuswahrheit, nacheinander von fünf Rollen erzeugt.

## Herkunft je Commit — Erzeugnis 2

Die vollständige Zuordnung Commit → Rolle → Auftrag ist erhoben (87 Zeilen, je Zeile SHA,
Zeitpunkt, Rolle, Kennung, `STATUS.md` ja/nein, Pfadklassen). Verdichtet:

```text
$ cut -f4 vorpruefung.tsv | sort | uniq -c | sort -rn
  21 A-41    17 A-37    16 (keine)    10 A-33     3 P2H-09
   3 A-40     3 A-39     3 A-38        2 W-25      2 W-17/1
   2 W-17     2 A-03     1 W-30        1 W-29      1 A-30
```

**SECHZEHN Vorgänge tragen keine Auftragskennung im Betreff.** Ich habe sie einzeln geöffnet
statt sie zu zählen: **alle sechzehn sind Befund-, Antwort- oder Berichtigungs-Commits** — das
Rollen-Tor, der §3-Fall, der §16-Zeitversatz, die Außenwirkungs-Grenze. **Sachbezug erkennbar,
Kennung fehlt.**

**Das ist NICHT „unklar" im Sinn von Erzeugnis 2** und führt darum auch nicht zur Ablehnung: die
Rolle steht, der Gegenstand steht, nur die Kennung fehlt — und ein Befund hat oft keinen Auftrag.
**Es ist trotzdem ein Zählproblem**, denn wer den Lauf nach Aufträgen sortiert, verliert diese
sechzehn lautlos.

## Der Zustand der betroffenen Aufträge — und warum das den Lauf umdefiniert

```text
A-41  ENTWURF        A-37  ENTWURF        A-40  ENTWURF
A-39  ENTWURF        A-38  ENTWURF
A-33  BETRIEBSBESTAETIGT   A-30  BETRIEBSBESTAETIGT   A-03  BETRIEBSBESTAETIGT
```

Dazu, unabhängig gegengeprüft am Stand des Release-Prüfers: **`CODE_FERTIG` 0 · `ENTWURF` 7 ·
Evaluator 0 Bälle.**

**Punkt 4 der Reihenfolge „Je einzelner Integration" verlangt das Übergabestück — Votum, Freigabe,
Abnahme; fehlt eines, ist es keine Integration.** Angewandt auf diesen Rückstand:

- **Fünf der acht Aufträge stehen auf `ENTWURF`.** Es gibt kein Votum, keine Freigabe, keine
  Abnahme — **weil es noch nichts abzunehmen gibt.** Ihre Commits sind Schnitt-, Prüf- und
  Befundarbeit, nicht Lieferung.
- **Drei stehen auf `BETRIEBSBESTAETIGT`.** Ihre Kette ist **auf der Kopie** vollständig gelaufen.
  Das Übergabestück existiert also — es liegt nur nicht im Integrations-Checkout.

> **Verbot 4 und Erzeugnis 4 sind für Lieferungen geschrieben. Hier gibt es keine.** Ein
> commitweiser Lauf über 60 Schreibvorgänge derselben Datei ist kein Abnahmevorgang, sondern das
> Nachspielen einer Dateihistorie.

**Ich stelle das fest und entscheide es nicht.** Es schärft die Frage von 16:42: der erste Lauf
ist eher ein **Abgleich der Statuswahrheit auf einen begründeten Stand** als 87 Einzelabnahmen.
Welcher der beiden Wege gilt, ist Yamas Wort.

## Erzeugnis 5 — Ablehnungen, vorbereitet

```text
abgelehnt bisher:  0
```

**Und die Null trägt ihren Grund:** Ablehnungsgründe nach der Rollenordnung sind *fremde oder
unklare Änderungen*. Gemessen: **0 Commits ohne Rollenmarke, 0 Commits mit Produktivpfaden,
0 unklare Herkunft.** Die 16 ohne Kennung sind geöffnet und zugeordnet. **Es liegt derzeit kein
Ablehnungsgrund vor** — das ist ein Messergebnis, keine Nachsicht.

## Zählstand

```text
uebernommen 0 · abgelehnt 0 · offen 151      Summe 151 = vorgelegte Commits
davon Vorgaenge (ohne Merges) 87 · vorgeprueft 87 · ohne Zuordnung 0
```

**Vorgeprüft heißt vorgeprüft, nicht integriert.** Kein Commit ist übernommen; die Vorprüfung
ändert daran nichts und soll es nicht.

## Was weiterhin fehlt, und von wem

| offen | bei wem |
|---|---|
| `SCHREIBEND` (Schritt J) — ohne das kein Lauf | **Yama** |
| Weg des ersten Laufs: Rollen pausieren **oder** `a041590f` als Nullpunkt | **Yama** |
| A-33 ausführen (`scripts/a33-kennungen-nachziehen.sh`), schreibt `docs/STATUS.md` | Integrator, nach J |
| Zündbedingung des Tores: Existenz → Betriebsart des Integrators | Generator (A-37) |

---

## BERICHTIGUNG 16.08. 20:4x — die tragende Folgerung dieses Blattes ist widerlegt

**Dieses Blatt schließt: „Fünf der acht Aufträge stehen auf `ENTWURF` … es gibt kein Votum, keine
Freigabe, keine Abnahme — weil es noch nichts abzunehmen gibt."** Das war an der Messung von
**16:52** richtig. **Um 17:02 war es falsch**, und der Planner hat mich zu Recht darauf gestoßen:

```
                16:52      17:02      20:41
CODE_FERTIG        0    →      1    →      1     A-41 (17:02) · A-37 (nachgezogen 20:16)
ENTWURF            7    →      6
```

**A-41 stand um 16:54 auf `CODE_FERTIG` mit dem Ball beim Evaluator.** Damit gab es sehr wohl etwas
abzunehmen, und Punkt 4 der Reihenfolge — Votum, Freigabe, Abnahme — war für diesen Auftrag wieder
im Spiel. **Die Folgerung hat keine Entscheidung getragen; sie hätte eine tragen können.**

**Was hält:** die Herkunftszuordnung aller 87 Vorgänge, die acht Nullen bei den Produktivpfaden
(`resources` `app` `database` `routes` `config` `tests` `public` `bootstrap`), die Aufteilung
60/4/23 und die Feststellung, dass 16 Vorgänge keine Auftragskennung tragen. **Der Satz „der
Rückstand ist das Arbeitsprotokoll von fünf Rollen und enthält null Zeilen Produktivcode" ist am
20:41 nachgemessen und gilt unverändert.**
