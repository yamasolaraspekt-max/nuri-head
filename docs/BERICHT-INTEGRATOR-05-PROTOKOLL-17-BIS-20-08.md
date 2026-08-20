# BERICHT INTEGRATOR 05 — Erzeugnis 4 für 17.–20.08., und die Stelle, an der ich es nicht führen kann

```yaml
rolle: integrator
gemessen: "20.08.2026, 13:5x CEST · Fenster d10a2f7c..HEAD (seit Bericht 03, 16.08. 20:43)"
gegenstand: "Erzeugnis 4 (Integrationsprotokoll), Erzeugnis 2 (Herkunft je Commit),
             Erzeugnis 5 (Ablehnungen) und Erzeugnis 7 (Repo-Zustand) fuer vier Tage."
kein_eingriff: "Ein neues Blatt. Kein Zustandsfeld, kein docs/STATUS.md, kein Bau."
```

## Der Befund in einem Satz

> **Bericht 02 schloss mit acht Nullen auf den Produktivpfaden. Diese vier Tage haben drei
> Commits mit Produktivcode — und alle drei sind an mir vorbei auf den Zweig gekommen.**

## 1 · Das Fenster in Zahlen

```text
$ git rev-list --count d10a2f7c..HEAD                 389   Commits gesamt
$ … --merges                                          163
$ … --no-merges                                       226   Vorgaenge

Vorgaenge nach Rollenmarke:
  122 plan-pruefer · 44 release-pruefer · 22 generator · 21 planner · 9 evaluator · 8 integrator
  ohne Rollenmarke: 0
```

**Erzeugnis 2 ist damit erfuellt und meldet null „unklar":** jeder der 226 Vorgänge trägt eine
Rollenmarke. **Das ist kein Verdienst der Vorprüfung, sondern der Commit-Konvention** — die Marke
steht im Betreff, ob der Commit durch mich läuft oder nicht.

## 2 · Wie die Commits auf den Zweig gekommen sind — Erzeugnis 4

**Zwei Wege, und nur einer geht durch mich.** Gemessen am Reflog dieses Checkouts, 17.–20.08.:

```text
Reflog-Eintraege im Fenster        108
  davon  merge                      79      <- der Rueckweg, meiner
  davon  commit:                    27      <- direkt in den Integrations-Checkout geschrieben

Direktschreiber nach Rolle:
  12 generator · 8 integrator · 5 plan-pruefer · 2 planner
```

**Neunzehn Commits fremder Rollen sind ohne Merge auf `auto/hausplaner-integration` entstanden.**
Für sie kann ich kein Protokoll führen: es gibt keinen Ursprungscommit, kein „Ziel-HEAD vorher /
nachher" im Sinne einer Übernahme und keinen Zeitpunkt, an dem eine Vorprüfung möglich gewesen
wäre. **Sie sind nicht integriert worden — sie waren schon da.**

*Meine acht eigenen `commit:`-Einträge sind meine Berichte und STATUS-Schreibvorgänge; die gehören
dorthin.*

**Der Rückweg selbst, 99 Merges mit meiner Marke.** Die übrigen 64 Merges im Fenster sind
**Gegenrichtung**, nicht Rückweg — sie führen in die Rollenzweige hinein:

```text
17 release-pruefer (eigene Merges)          14 fork/auto/... -> rolle/release-pruefer
10 rolle/plan-pruefer -> rolle/release-pruefer     9 auto/... -> rolle/release-pruefer
 7 rolle/planner -> …   4 rolle/generator -> …   2 rolle/evaluator -> …
 1 auto/... -> rolle/planner
```

**63 von 64 laufen über den Release-Prüfer.** Das ist dieselbe Lage wie in Bericht 01 (*„77 der 150
tragen die Marke release-pruefer"*) — unverändert, vier Tage später.

## 3 · Der Bruch mit Bericht 02: Produktivcode

**Bericht 02 maß acht Verzeichnisse, acht Nullen, und schloss daraus, der Lauf könne die Anwendung
nicht beschädigen. Für dieses Fenster gilt das nicht mehr:**

```text
resources/ 2 · app/ 1 · database/ 0 · routes/ 0 · config/ 0 · tests/ 0 · public/ 0 · bootstrap/ 0
```

| Commit | Zeit | Pfad | Umfang | Kennung | Test dabei | Zugang |
|---|---|---|---|---|---|---|
| `62736115` | 20.08. 13:12 | `app/dashboard/enginePanels.ts` | +23 −3 | **keine** | ja (+61 −2) | **direkt** |
| `d0d62e49` | 19.08. 19:49 | `app/tools/werkzeugLandkarte.ts` | +8 −1 | A-35 | ja (+4 −2) | **direkt** |
| `90b89ae2` | 20.08. 13:21 | `app/Services/Geometrie/SzeneProjektionService.php` | +16 −3 | **keine** | **nein** | **direkt** |

*(`resources/planner/hausplaner/` gekürzt.)*

**Alle drei tragen `commit:` im Reflog — keiner ist über einen Rollenzweig gekommen.** Damit gilt
für den ersten Produktivcode seit vier Tagen: **er ist an der einzigen Stelle vorbeigelaufen, die
ihn hätte vorprüfen sollen.**

**Ich bewerte den Bau NICHT.** Ob `62736115` die Schneelastzone richtig behebt, ist Sache des
Evaluators; die Betreffe lesen sich sorgfältig, und zwei der drei bringen ihren Test mit. **Was ich
feststelle, ist ausschließlich die Zugangsart** — und dass zwei von drei keine Auftragskennung
tragen, was nach Bericht 02 (16 von 87) kein neues Muster ist, hier aber erstmals Produktivcode
betrifft.

## 4 · Erzeugnis 5 — Ablehnungen

```text
abgelehnt in diesem Fenster:  0
```

**Und die Null trägt einen anderen Grund als in Bericht 02.** Dort war sie ein Messergebnis: kein
Ablehnungsgrund lag vor. **Hier ist sie strukturell** — man kann nur ablehnen, was einem vorgelegt
wird. Neunzehn fremde Commits sind nie vorgelegt worden. **Eine Null, die aus fehlender Vorlage
stammt, ist keine Freigabe**, und ich schreibe sie deshalb nicht als eine auf.

## 5 · Erzeugnis 7 — Repository-Zustand, 20.08. 13:5x

```text
HEAD 9ae47791 · Baum: 21 uncommittierte fremde Eintraege (.claude/agents/, in Arbeit)
fork 0/0 · backup-private 0/0 · Rueckfluss JA
alle fuenf Rollenzweige 0 voraus
Gegenrichtung: release-pruefer 8 · evaluator 12 · plan-pruefer 8 · generator 8 · planner 8 zurueck
```

**Die Gegenrichtung war am 20.08. um 12:10 auf null und ist um 13:50 wieder bei acht.** Bericht 04
sagt, sie habe keinen Takt; das ist die zweite Messung derselben Aussage, 100 Minuten später.

## 6 · Ball

| an wen | was |
|---|---|
| **Yama** | die Regelfrage aus dem Index-Befund bekommt hier ihre Kostenseite: **drei Produktivcode-Commits ohne Vorprüfung**. Solange direkt in den Integrations-Checkout committet wird, ist Erzeugnis 4 für diese Commits nicht führbar — das ist keine Nachlässigkeit, sondern eine Folge der Arbeitsweise |
| **Evaluator** | `62736115`, `d0d62e49`, `90b89ae2` sind Produktivcode; die Abnahme ist seine, nicht meine. `90b89ae2` bringt keinen Test mit — Feststellung, kein Votum |
| **Planner** | zwei der drei tragen keine Auftragskennung. Bericht 02 zählte 16 von 87 ohne Kennung; neu ist, dass es jetzt Code betrifft |
