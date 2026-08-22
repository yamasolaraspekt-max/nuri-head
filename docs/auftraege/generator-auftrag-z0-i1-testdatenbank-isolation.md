# Z0-I1 · Testdatenbanken isolieren: eine Datenbank je Prüfstand, Namensprüfung vor dem ersten Schreibzugriff

```yaml
zustand: ENTWURF
welle: 0 / Infrastruktur — Gesamtauftrag v2 Phase 3; VOR weiteren parallelen Abnahmen
basis_sha: 976f7d6b
herkunft: "Evaluator-Abnahme belegt — parallele Laeufe setzen dieselbe ticket_testing zurueck, Testkonto verschwand waehrend einer Browserabnahme (Gesamtauftrag v2, Phase 3)"
spur: "A — Testinfrastruktur, Datenbank (nur Testdatenbanken), Schutzgrenze 'Tests nur gegen eindeutig benannte Testdatenbanken'"
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
wip_prioritaet: "Yama 21.08. spaet — Z0-I1 ist der hoechste technische Blocker; der EINE zulaessige Sicherheitsbau, bis er abgenommen ist"
operand: "Y-13 ENTSCHIEDEN 21.08. (Yama) — ticket_user erhaelt vollstaendige Rechte (CREATE, DROP, ALTER, Migrationen, Datenaenderungen) auf ticket_testing_%; die vier Testdatenbanken duerfen selbststaendig angelegt, zurueckgesetzt und verwaltet werden; Produktionsdatenbanken unberuehrt; REIHENFOLGE — parallele DB-Laeufe erst NACH erfolgreichem Guard- und Verbindungstest (Kriterium F)"
grant_beleg: "Der Generator misst als ERSTES, ob das GRANT wirksam ist (SHOW GRANTS FOR CURRENT_USER(); CREATE DATABASE ticket_testing_generator) — ist es nicht wirksam, meldet er ENV_BLOCKED mit Rohausgabe; das GRANT selbst fuehrt nur Yama (Root) aus, keine Rolle haelt Root"
```

## Ziel
1. **Teil A (nach Y-13):** vier getrennte Testdatenbanken `ticket_testing_evaluator`,
   `ticket_testing_generator`, `ticket_testing_security`, `ticket_testing_browser`; die Rolle wählt
   ihre DB über **eine** Stelle (`TICKET_ROLLE` → `DB_DATABASE` in `phpunit.xml`/`.env.testing`-Ableitung
   oder ein Wrapper-Skript) — keine Handwahl je Lauf.
2. **Teil B (sofort):** jeder Testlauf prüft **vor dem ersten Schreibzugriff** den tatsächlichen
   Datenbanknamen (`SELECT DATABASE()`) gegen das Muster `^ticket_testing(_[a-z]+)?$` und **bricht ab**,
   wenn er nicht passt — keine Migration, kein Seed, kein Truncate gegen eine andere DB. Kein Test gegen
   `ticket` (Produktion) oder unbekannte Namen.
3. Browserkonto und Testobjekt gehören zur Browserbühne (`ticket_testing_browser`); ein paralleler
   Lauf darf sie nicht verändern.

## Ist-Beleg
Evaluator-Bericht (Gesamtauftrag v2 Phase 3): gemeinsamer `ticket_testing`-Bestand parallel
zurückgesetzt, Testkonto verschwand. `phpunit.xml`/Test-Bootstrap: **zu messen**, wo `DB_DATABASE`
für Tests gesetzt wird (RefreshDatabase/DatabaseMigrations-Nutzung zählen) — Generator misst zuerst.

## Scope · Dateien
`phpunit.xml` (oder `.env.testing`), `tests/TestCase.php` bzw. ein Bootstrap-Guard (Namensprüfung),
ggf. `scripts/` Wrapper (`test-als-rolle.sh`), Doku in `docs/regelwerk/` (eine Zeile: welche Rolle
welche DB). **Nicht-Ziele:** keine Änderung an Produktiv-DB-Config; keine Migrationen am Schema;
keine Änderung der Tests selbst (nur Bootstrap/Guard).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Guard: Lauf gegen DB `ticket` (simuliert per Env) bricht VOR dem ersten Schreibzugriff ab, Rohausgabe der Meldung | Teil B | *n.U.* | Rohausgabe |
| B: Guard: Lauf gegen `ticket_testing_evaluator` läuft durch | Teil B | *n.U.* | Rohausgabe |
| C: Positivprobe: zwei Rollen laufen gleichzeitig gegen zwei DBs, beide grün, Konten/IDs der jeweils anderen unverändert (DB-Probe vorher/nachher) | Teil A | *n.U.* | Rohausgaben |
| D: Kollisionsprobe: zwei Läufe gegen DIESELBE DB → der Guard/die Serialisierung verhindert die Kollision ODER sie wird sichtbar gemeldet (kein stilles Überschreiben) | Teil A | *n.U.* | Rohausgaben |
| E: Rollen→DB-Zuordnung steht an einer Stelle (Datei:Zeile), keine Handwahl | Teil A | *n.U.* | Zitat |
| F (Y-13-Reihenfolge): Guard-Test (A/B) UND Verbindungstest je der vier DBs (`SELECT DATABASE()` = erwarteter Name, CREATE/DROP-Probe auf einer Wegwerf-Tabelle) sind GRÜN, BEVOR eine Rolle parallel läuft — Freigabe der Parallelität steht im Baubericht mit Rohausgaben | Freigabe | *n.U.* | Rohausgaben |
| G: Produktions-DB `ticket` unberührt — `SHOW GRANTS` zeigt die neuen Rechte nur auf `ticket_testing\_%` (Rohausgabe) | Schutz | *n.U.* | Rohausgabe |

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute kein Guard).

---

## ERWEITERUNG 22.08.2026 — abnehmbare Kriterien, Wiederverwendung, Operandengrenze

**Herkunft:** Yamas Vorgaben über `rollen/planner.yaml` gen 6; Ist-Lage-Messung des Plan-Prüfers
(`ereignisse/VORRAT-Z0-I1/plan-pruefer-ist-lage-kriterium-A.yaml`), **von mir einzeln nachgefahren**.
*Die Matrix oben bleibt als Beleg stehen (A-20-4); sie nennt Kriterien, aber keine Messbefehle —
die stehen hier.*

### ⚠ ZUERST: der halbe Bau existiert bereits, und das Blatt nennt ihn nicht

**`tests/TestDatenbank.php` (66 Z., Anlass `PB-056`, 03.08.) beantwortet die Kernfrage dieses
Blattes schon — gebaut, benutzt und mit einer eigenen Zusagendatei abgesichert:**

```
tests/TestDatenbank.php        ROLLEN-Liste + BASIS + zwei Riegel
tests/CreatesApplication.php:23  benutzt sie VOR dem Bootstrap: putenv('DB_DATABASE='.$db)
tests/Unit/TestDatenbankTest.php  eigene Zusagen, u. a. auf den Rückfall (:21-23)
```
**Anlass laut Datei (`:10-13`) ist derselbe Vorfall wie in der Herkunft dieses Blattes:** *„drei
Rollen fuhren gleichzeitig `RefreshDatabase` gegen dieselbe `ticket_testing`… Deadlocks beim
`drop table` und falsche rote Ergebnisse."*
**Bauweise, nachgefahren:** *zwei* Riegel — (1) die Rolle muss auf der Liste stehen, (2) der
**Ergebnisname** muss auf das harte Präfix passen. Die Begründung für den zweiten steht bei `:22-24`:
*„wäre die Liste eines Tages offener, fängt er trotzdem `../produktion`… Ein Riegel, der nur die
Eingabe prüft, schützt so lange, bis jemand die Eingabe erweitert."*

**Folge für dieses Blatt: `Z0-I1` ist kein Neubau, sondern eine Erweiterung an drei Punkten.**
*Was hier steht, muss an `TestDatenbank` andocken — ein zweiter Namensentscheider wäre genau die
zweite Wahrheit, die CLAUDE.md verbietet.*

### ⚠ Der Musterkonflikt — und warum der Bestand ihn schon richtig löst

**Ziel 2 verlangt das Muster `^ticket_testing(_[a-z]+)?$`. Allein gebaut wäre es zu weit:**

```
Muster allein, gegen fünf Proben (python3, re.match):
  ticket                  passt nicht   richtig abgewiesen
  ticket_testing          PASST         richtig
  ticket_testing_browser  PASST         gewollt
  ticket_testing_kopie    PASST         ⚠ zu weit — siehe A-03-1
  zz_ticket_testing       passt nicht   richtig abgewiesen
```
**`scripts/browser-buehne.sh:68-70` (A-03-1) begründet, warum das nicht genügt:** *„EIN Name, kein
Muster — ein Muster wie `*_test*` ließe `ticket_test_kopie` durch, und eine Kopie der Arbeitsdaten
trägt dieselben Kundendaten wie das Original."* **Und `scripts/__tests__/buehnenWaechter.test.mjs:95`
führt `ticket_testing_kopie` als laufende Negativprobe.**

**⚠ BERICHTIGUNG AN MIR SELBST, vor dem Commit gefunden:** mein erster Entwurf schloss daraus
*„keine Musterprüfung, sondern eine Liste"*. **Das ist falsch, und der Bestand zeigt warum.**
`TestDatenbank` hat **beides** — und die Reihenfolge trägt: Riegel 1 (Liste) fängt `kopie` ab,
**bevor** das Muster überhaupt greift. Nachgefahren:
```
TEST_ROLLE='kopie'     -> Riegel 1 wirft: 'kopie' ist keine bekannte Rolle
TEST_ROLLE='evaluator' -> ticket_testing_evaluator
```
**Richtig ist also: Liste UND Muster, in dieser Reihenfolge** — genau wie gebaut. *Ich hatte einen
Konflikt konstruiert, wo der Bestand die Antwort bereits trägt; hätte ich `TestDatenbank` vor dem
Schreiben geöffnet, wäre die Entscheidung nie nötig gewesen.* **Das Muster ist nicht der Fehler —
das Muster OHNE Liste wäre einer.**

### ⚠ Die echte Lücke, dreifach: Rückfall, Rollennamen, fehlender Verbindungsnachweis

| # | Lücke | Beleg |
|---|---|---|
| 1 | **Der Rückfall ist eingebaut UND als Zusage getestet** | `TestDatenbank.php:44-46` gibt bei leerem `TEST_ROLLE` die Sammelbühne zurück; `:37-39` nennt es *„ein Angebot, keine neue Pflicht"*; `TestDatenbankTest.php:21-23` prüft genau das |
| 2 | **Die Rollenliste passt nicht zum Auftrag** | Bestand `ROLLEN = ['generator','evaluator','planner','pruefer']` — der Auftrag verlangt `evaluator, generator, security, browser`. **`browser` und `security` werden heute abgewiesen** (nachgefahren: *„Riegel 1 wirft"*) |
| 3 | **Niemand prüft, ob die Setzung angekommen ist** | `CreatesApplication.php:23-25` setzt `DB_DATABASE`, fragt die Verbindung aber nie; `SELECT DATABASE` → **0 Dateien** im ganzen Bestand |

**Punkt 1 ist der heikelste, weil die Zusage bewusst gebrochen werden muss.** Der Auftrag verlangt
*„`TEST_ROLLE` verpflichtend, kein Rückfall auf gemeinsames `ticket_testing`"*. **Das kehrt eine
dokumentierte Entscheidung um** — und das ist etwas anderes, als eine Zusage stillschweigend
anzupassen, damit ein Kriterium grün wird. *Deshalb steht es hier als eigener Punkt und nicht als
Nebensatz in einem Kriterium (siehe `Z0-I1-4`).*

### ⚠ Zweiter Befund: Teil A macht die beiden vorhandenen Wächter falsch-positiv

**Gemessen:** beide Wächter vergleichen mit **exakter Gleichheit** gegen einen einzigen Namen —
`scripts/buehnen-waechter.sh:29` + `:123` (`[ "$db" != "$ERWARTETE_DB" ]`) und
`scripts/browser-buehne.sh:31` + `:71`. **Sobald die Browserbühne auf `ticket_testing_browser`
läuft, meldet `buehnen-waechter.sh` sie als FALSCH und gibt `exit 3` (`ENV_BLOCKED`).**
*Ein Schutz, der nach dem Umbau am richtigen Zustand anschlägt, wird weggeklickt — A-03 hat das
selbst als Fehlerklasse benannt.* **Beide Skripte gehören deshalb zum Liefergegenstand**, nicht als
Nebenwirkung, sondern als Kriterium (`Z0-I1-6`).

### Wiederverwendung vor Neuentwicklung (CLAUDE.md) — was übernommen wird

| Vorhanden | was daraus übernommen wird |
|---|---|
| **`tests/TestDatenbank.php` (66 Z., PB-056)** | **die Sache selbst.** Liste + Präfix-Riegel + Wurf bei Unbekanntem. `Z0-I1` **erweitert diese Klasse**, baut keinen zweiten Namensentscheider |
| **`tests/CreatesApplication.php:21-25`** | der **Zeitpunkt**: der Name steht **vor** dem Bootstrap fest — *„danach steht die Verbindung und ein Wechsel käme zu spät"* (`:17-18`) |
| **`tests/Unit/TestDatenbankTest.php`** | die vorhandenen Zusagen bleiben **bis auf die eine** (Rückfall) unverändert gültig |
| `scripts/buehnen-waechter.sh` (149 Z., A-04) | die **Haltung**: *„wer weder `DB_DATABASE` noch ein auflösbares `APP_ENV` trägt, ist UNBEKANNT und damit UNSICHER"* (`:20`), `exit 3` = `ENV_BLOCKED`, *„ohne Auskunft gibt es keine Entwarnung"* (`:40`) |
| `scripts/browser-buehne.sh` (83 Z., A-03) | der **Nachweisort**: der Beweis wird **am Kindprozess** geführt, nicht an der Konfiguration (`:13`, `:41-44`) |
| `phpunit.xml:25-28` | bleibt unverändert wirksam — wird **geprüft**, nicht ersetzt |

**Der tragende Grund für den Nachweisort steht im Bestand belegt** (`browser-buehne.sh:11-13`):
`DB_DATABASE=… php artisan serve` → *„Kind sieht: `ticket` — FALSCH, und leise"*, weil Laravel
nicht durchgereichte Variablen aktiv auf `false` setzt (`ServeCommand.php:179`, Durchreich-Liste
13 Einträge, **null** davon `DB_*`). **Deshalb genügt `phpunit.xml` nicht als Beleg:** es ist eine
**Setzung**, keine **Prüfung**. *Zwei Dateien desselben Repos beantworten diese Frage heute
gegensätzlich — `browser-buehne.sh` sagt, eine Setzung genüge nicht; `phpunit.xml` verlässt sich
darauf.* **`Z0-I1-1` schließt genau diese Lücke: `SELECT DATABASE()` fragt die Verbindung selbst.**

*Präzisierung, damit daraus kein falscher Vorwurf wird:* `<env force="true">` **wirkt** bei
phpunit — der Unterschied zu `artisan serve` ist real. Was fehlt, ist die Prüfung, **dass** sie
gewirkt hat, und der Schutz für Wege **an `phpunit.xml` vorbei** (artisan-Befehle, Seeder).

### Kriterien — je mit Messbefehl und heutigem rotem Ergebnis

**Alle Rot-Belege am 22.08. im Worktree `ticket-rolle-planner` gefahren.** *Kein Datenbanklauf —
das Verbot bis Z0-I1 gilt auch für die Messung, die es aufhebt; alle Belege sind Textmessungen.*

- **Z0-I1-1** · **Der Guard fragt die VERBINDUNG, vor dem ersten Schreibzugriff.**
  `SELECT DATABASE()` läuft vor Migration, Seed und Truncate; das Ergebnis wird gegen die Liste
  geprüft; bei Abweichung **Abbruch** mit Rohausgabe des **gefundenen** Namens.
  **Rot:** `grep -rl 'SELECT DATABASE' --include=*.php --include=*.xml --include=*.sh .` → **0 Dateien**.
  Gegenprobe, dass der Griff trägt: `grep -rl 'RefreshDatabase' tests/` → **70 Dateien**.
  **Art des Rot: Produkt.**
- **Z0-I1-2** · **Negativprobe Produktion.** Lauf gegen `ticket` (per Env simuliert) bricht ab,
  **bevor** geschrieben wird. Rohausgabe und `echo $?` in den Bericht.
  **Rot:** `tests/TestCase.php` hat **10 Zeilen** und **0** Treffer auf eine Datenbank-Namensprüfung.
  **Art des Rot: Schutz** — deshalb genügt kein Grep, die Probe muss **ausgelöst** werden.
- **Z0-I1-3** · **Die vier Auftragsrollen sind zulässig, `ticket_testing_kopie` bleibt abgewiesen.**
  `TestDatenbank::ROLLEN` trägt die Rollen dieses Auftrags; **Liste und Präfix-Riegel bleiben beide
  erhalten**, in dieser Reihenfolge.
  **Rot, nachgefahren:** `TEST_ROLLE=browser` → *„Riegel 1 wirft: 'browser' ist keine bekannte
  Rolle"*, `TEST_ROLLE=security` → dito. **Die beiden Rollen, für die dieses Blatt gebaut wird,
  werden heute abgewiesen.** Gegenprobe: `TEST_ROLLE=evaluator` → `ticket_testing_evaluator`.
  **Positivprobe:** alle Namen aus `ROLLEN` kommen durch. **Negativprobe:** `kopie` wird abgewiesen —
  *sie ist heute grün und muss grün bleiben.*
  **⚠ ZU KLÄREN, BEVOR GEBAUT WIRD (kein Bauentscheid des Generators):** der Auftrag nennt
  `evaluator, generator, security, browser`; der Bestand führt `generator, evaluator, planner,
  pruefer`. **Zwei Namen kommen hinzu, zwei stehen zur Disposition.** *Wer `planner` und `pruefer`
  entfernt, nimmt zwei Rollen ihre eigene Datenbank — das ist eine Entscheidung über Daten und
  gehört Yama, nicht dem Bau.* **Vorschlag zur Bestätigung: die Liste wird auf sechs erweitert**
  (`generator, evaluator, planner, pruefer, security, browser`) — additiv, niemand verliert etwas,
  und `Z0-I1-7`/`-8` brauchen ohnehin nur zwei davon.
  **Absage-Regel:** Wer `Z0-I1-3` mit einer **reinen** Musterprüfung erfüllt meldet, hat es **nicht**
  erfüllt; der Listen-Riegel muss vor dem Muster stehen bleiben.
- **Z0-I1-4** · **`TEST_ROLLE` ist verpflichtend — der Rückfall entfällt, und zwar ausdrücklich.**
  Fehlt `TEST_ROLLE`, bricht der Lauf ab; es gibt **keinen** stillen Rückfall auf `ticket_testing`.
  **Rot — und es ist ein Vergleichs-Rot, kein Produkt-Rot:** `TEST_ROLLE` **existiert bereits**
  (`grep -rl` → **2 Dateien**: `CreatesApplication.php`, `TestDatenbank.php`). Was fehlt, ist die
  Pflicht: `TestDatenbank.php:44-46` liefert bei leerem Wert die Sammelbühne, `:37-39` begründet das
  als *„ein Angebot, keine neue Pflicht"*.
  **⚠ EINE GETESTETE ZUSAGE MUSS DAFÜR FALLEN:** `tests/Unit/TestDatenbankTest.php:21-23` prüft
  `name(null)`, `name('')` und `name('   ')` auf `ticket_testing`. **Diese Zusage wird durch die
  umgekehrte ersetzt** (leerer Wert → Ausnahme). *Das ist der eine Fall, in dem eine bestehende
  Zusage geändert werden **darf** — weil der Auftrag es ausdrücklich verlangt und die Änderung hier
  benannt ist. **Sie muss im Bau-Bericht als solche gemeldet werden**, nicht im Diff verschwinden.*
  **Begründung aus dem Bestand:** *„was weder … noch … trägt, ist UNBEKANNT und damit UNSICHER"*
  (`buehnen-waechter.sh:20`). **Der Rückfall auf die Sammelbühne ist genau der Vorfall**, gegen den
  sowohl `PB-056` als auch dieses Blatt geschrieben wurden.
- **Z0-I1-5** · **Die Zuordnung Rolle → Datenbank steht an EINER Stelle**, mit `datei:zeile` belegt;
  keine Handwahl je Lauf. **Diese Stelle ist `TestDatenbank::ROLLEN`/`::BASIS` — sie existiert und
  wird nicht verdoppelt.**
  **Rot:** die Skripte tragen den Namen **unabhängig davon** ein zweites und drittes Mal
  (`buehnen-waechter.sh:29`, `browser-buehne.sh:31`), ausdrücklich als *„BEWUSSTE Duplikation"*
  (`:26-28`) mit einer Zusage, die die Drift zwischen **zwei** Orten abfängt.
  *Diese Begründung trug für zwei Skripte und **einen** Namen. Mit vier Rollennamen und einer
  PHP-Klasse als Quelle trägt sie nicht mehr — der Wert steht dann an drei Orten in zwei Sprachen.*
  **Verlangt:** die Skripte beziehen die zulässigen Namen aus derselben Quelle **oder** eine Zusage
  belegt die Übereinstimmung aller drei Orte. *Welcher der beiden Wege — Bauentscheid des Generators,
  beide erfüllen das Kriterium.*
- **Z0-I1-6** · **Die vorhandenen Wächter ziehen mit — keine Abschwächung, keine Falsch-Positiven.**
  Nach dem Bau meldet `buehnen-waechter.sh` eine Bühne auf `ticket_testing_browser` als **OK**
  (`exit 0`) und eine auf `ticket` weiterhin als **FALSCH** (`exit 3`).
  Die Zusagen in `scripts/__tests__/buehnenWaechter.test.mjs` bleiben grün — **einschließlich der
  Negativprobe `ticket_testing_kopie` (`:95`) und `zz_ticket_testing` (`:94`)**.
  **Rot:** heute vergleichen beide exakt gegen `ticket_testing` (`:123` bzw. `:71`) und würden jeden
  Teil-A-Namen als FALSCH melden.
  **Absage-Regel:** Wer eine bestehende Zusage **ändert oder entfernt**, um dieses Kriterium grün zu
  bekommen, hat es **nicht** erfüllt. *Die Zusage ist der Schutz, nicht der Testaufwand.*
- **Z0-I1-7** · *(Teil A — hängt an `Y-13`)* **Parallele Positivprobe:** zwei Rollen laufen
  gleichzeitig gegen zwei Datenbanken, beide grün, Konten und IDs der jeweils anderen **unverändert**
  (Probe vorher/nachher, beide Rohausgaben).
- **Z0-I1-8** · *(Teil A — hängt an `Y-13`)* **Kollisionsprobe:** zwei Läufe gegen **dieselbe**
  Datenbank — die Kollision wird verhindert **oder sichtbar gemeldet**, nie still überschrieben.
  *Das ist der Vorfall aus der Herkunft dieses Blattes, als Probe formuliert.*

### ⚠ Operandengrenze `Y-13` — was ohne Yamas Entscheidung baubar ist

**`Y-13` (MySQL-Rechte für die vier Datenbanken; Root hält Yama) ist offen seit `9d76f698`,
21.08. 21:01 — und nirgends als entschieden vermerkt.** *Ich entscheide ihn nicht; eine
Datenbankentscheidung wird nicht still automatisiert (CLAUDE.md).*

| | Kriterien | Zustand |
|---|---|---|
| **Ohne `Y-13` baubar** | `Z0-I1-1` bis `Z0-I1-6` | **jetzt** — der Guard, die Liste, `TEST_ROLLE`, die eine Stelle und das Mitziehen der Wächter brauchen keine neue Datenbank |
| **Hängt an `Y-13`** | `Z0-I1-7`, `Z0-I1-8` | **erst danach** — beide setzen zwei existierende Datenbanken voraus |

**Wichtig für den Zuschnitt:** `Z0-I1-3` und `Z0-I1-5` nennen die vier Rollennamen bereits, **ohne
dass die Datenbanken existieren müssen** — die Liste ist die Zusage, welche Namen zulässig sind,
nicht die Behauptung, dass sie angelegt sind. **Der Guard darf einen Namen aus der Liste, dessen
Datenbank fehlt, nicht als „in Ordnung" behandeln:** eine fehlende Datenbank ist ein Fehler des
Aufbaus und muss laut scheitern. *Auch das ist „im Zweifel laut, nie still".*

## Rückweg
Config/Guard per Commit zurückdrehbar; zusätzliche Test-DBs schaden nicht (Löschung später nur mit Freigabe).

---

# UMSCHNITT AUF STUFE 1 — 22.08., ohne `root` baubar

```yaml
anlass: "Entscheidung des Dirigenten in Yamas Namen, 22.08. 15:12:40
         (STEUERUNG-dirigent/dirigent-entscheidung-y13-y12-erledigt.yaml).
         Planner gen 19 Posten 8. Das Verbot 'PARKED_DRAFT anfassen' ist dafuer aufgehoben."
mess_sha: ec239609
stufe_1: "EINE gemeinsame Test-DB ueber den SOCKET, ohne root, ohne neue Datenbank."
stufe_2: "Vier DBs ticket_testing_<rolle> — Folgeposten, braucht root. NICHT bauen."
```

## Warum Stufe 1 überhaupt geht — die gemessene Lage

```
ticket_user@localhost (SOCKET)     ALL PRIVILEGES auf ticket_g1b1_testing (446 Tabellen)
ticket_testing                     existiert (451 Tabellen)
.env.testing                       zeigt auf ticket_testing ueber 127.0.0.1:3307
ticket_user@127.0.0.1              hat dort NUR ticket.*        <- DAS ist die ENV_BLOCKED-Ursache
```

> ## ⚠ DIESE PRÄMISSE IST STRITTIG — nicht als gemessen führen
>
> **Der Satz „die Sperre lag am Weg" stand hier als Messung. Er ist es derzeit nicht.**
>
> Eine **zweite, unabhängige Messung** (lesende Sitzung in Yamas Auftrag, 15:48:01) kommt über
> **dieselbe TCP-Verbindung** zu einem anderen Ergebnis:
>
> ```
> SHOW GRANTS (TCP, 127.0.0.1:3307)   -> USAGE ON *.*  +  ALL ON `ticket`.*
>                                        ticket_testing wird NICHT genannt
> SELECT COUNT(*) FROM ticket_testing.migrations   -> 616      <- der Zugriff GELINGT
> ```
>
> **Die Rechteliste deckt sich zeichengleich mit meiner** — der Widerspruch liegt nicht in den
> Grants, sondern zwischen **Rechteliste** und **tatsächlichem Zugriff**. *Meine Aussage ist als
> Rechteliste richtig und als Zugriffsaussage nicht haltbar.*
>
> **Nach der Hausregel gilt damit KEINE der beiden Messungen**, bis der Unterschied aufgelöst ist.
> **Aufzulösen durch eine dritte Messung** mit Leserecht auf `mysql.user`: welche `ticket_user`-
> Konten existieren und welche Grants jedes trägt. *Ich kann sie nicht fahren — der Zugriff auf die
> Zugangsdaten ist mir verwehrt, und ich habe ihn nicht umgangen.*
>
> **Was das für dieses Blatt bedeutet — und was nicht:**
> **Die Kriterien sind unberührt.** `Z0-I1-1` (Guard fragt die Verbindung), `-9` (Serialisierung),
> `-10` (`SELECT DATABASE()` im Beleg) und `-11` (Seed) stehen unabhängig davon, *warum* die Sperre
> entstand. **Betroffen ist die Begründung, nicht die Sache.**
> **Offen bleibt aber:** ob Stufe 1 die Sperre wirklich auflöst. *Der Bau kann richtig sein — der
> Beleg dafür wäre es nicht.*
>
> **Nebenbefund derselben Messung, der die Auflagen-Frage entspannen könnte:** `.env.testing` trägt
> `DB_DATABASE=ticket_testing` — genau den Namen aus Yamas Auflage 1 —, und über den Socket hat
> `ticket_user@localhost` auch auf `ticket_testing` volle Rechte. **Arbeitet Stufe 1 gegen
> `ticket_testing`, wird meine Auflagen-Klarstellung bei `Z0-I1-11` gegenstandslos.** *Das ist eine
> Frage an Yama, nicht meine Entscheidung — aber sie gehört hierher, bevor jemand den Namen
> festschreibt.*

~~**Die Sperre lag nie an fehlenden Rechten, sondern am Weg:** über den **Socket** hat der Nutzer
alles, über **TCP** nichts.~~ *(Fassung vom 15:36, siehe Kasten oben — als Beleg stehengelassen,
nicht gelöscht.)*

## Was von den acht Kriterien gilt, und was Folgeposten wird

| Kriterium | Stufe 1 | Änderung |
|---|---|---|
| **Z0-I1-1** Guard fragt die Verbindung | **gilt** | unverändert — jetzt erst recht, der Weg ist die Ursache |
| **Z0-I1-2** Negativprobe `ticket` | **gilt** | unverändert — Produktiv-DB nie im Testlauf |
| **Z0-I1-3** Rollennamen zulässig | **umgeschnitten** | Stufe 1 kennt **eine** DB: `ticket_g1b1_testing`. Das Muster prüft **diesen einen Namen exakt**, `ticket_testing_kopie` und jeder andere bleiben abgewiesen |
| **Z0-I1-4** `TEST_ROLLE` verpflichtend | **gilt** | unverändert — sie benennt jetzt den **Lease-Halter**, nicht die DB |
| **Z0-I1-5** Zuordnung an EINER Stelle | **gilt** | unverändert |
| **Z0-I1-6** vorhandene Wächter ziehen mit | **gilt** | unverändert |
| **Z0-I1-7** parallele Positivprobe | **Folgeposten Stufe 2** | braucht vier DBs, braucht `root` |
| **Z0-I1-8** Kollisionsprobe | **umgeschnitten** | Stufe 1 hat **eine** DB — Kollision wird nicht durch Trennung verhindert, sondern durch **Serialisierung** (neues Kriterium 9) |

## Die neuen Kriterien der Stufe 1

- **Z0-I1-9** · **DIE LÄUFE SIND SERIALISIERT — ÜBER EINE DB-LEASE.**

  **Verlangt:** Vor dem ersten Schreibzugriff zieht der Lauf eine Lease unter
  `leases/TESTDB-ticket_g1b1_testing/` nach **V2 §8** (`counter`, `counter.lock/`, `active/lease.yaml`
  mit `fencing_token`, `heartbeat_bis`, `owner`). Eine belegte, gültige Lease → **der zweite Lauf
  wartet oder bricht ab**, er läuft nicht mit.

  **Messbefehl:** zwei Läufe gleichzeitig starten; der zweite meldet die fremde Lease mit
  `fencing_token` und Halter, Rückgabewert ≠ 0. Danach: erster Lauf gibt frei, zweiter läuft.

  **Heutiges (rotes) Ergebnis:** `leases/TESTDB-ticket_g1b1_testing/` existiert nicht; zwei Läufe
  setzen dieselbe DB gegenseitig zurück — **das ist der Anlassfall dieses Blattes** (Testkonto
  verschwand während einer laufenden Browserabnahme).

  **Absage-Regel:** Eine Datei als Sperre („`.lock` anlegen") erfüllt (9) **nicht** — ohne
  `fencing_token` und `heartbeat_bis` ist ein abgestürzter Lauf eine Dauersperre. *Die Lease-Mechanik
  existiert bereits; sie wird benutzt, nicht nachgebaut.*

- **Z0-I1-10** · **`SELECT DATABASE()` STEHT IN JEDEM BELEG.**

  **Verlangt:** Jeder Testlauf gibt den tatsächlich verbundenen Datenbanknamen aus, und der Bericht
  zitiert ihn. **Nicht die Konfiguration, sondern die Verbindung.**

  **Messbefehl:** `SELECT DATABASE()` je Lauf → `ticket_g1b1_testing`; im Bericht wörtlich.

  **Heutiges (rotes) Ergebnis:** kein Lauf gibt ihn aus. *Was in `.env.testing` steht, ist eine
  Absicht; was `SELECT DATABASE()` sagt, ist die Tatsache — und die beiden gehen heute auseinander.*

- **Z0-I1-11** · **DER SEED STELLT SEINE VORBEDINGUNG SELBST HER.**

  **Verlangt:** Ein Skript legt die Prüfvoraussetzungen **idempotent** in `ticket_g1b1_testing` an —
  Prüfnutzer (`a24`) und Prüfobjekt, sonst nichts.

  **Die drei Auflagen sind Yamas, wörtlich zitiert** (`docs/VORLAGE-AN-YAMA-2026-08-12.md:1596-1613`,
  Entscheidung 13.08.: *„Entschieden: C. Das Prüfskript stellt seine Vorbedingung selbst her —
  idempotent, nur wenn es läuft, nur gegen `ticket_testing`."*):

  ```text
  1  FAIL CLOSED, nicht fail silent.
     Das Skript prueft den Datenbanknamen BEVOR es irgendetwas schreibt.
     Stimmt er nicht exakt -> Abbruch mit Wortlaut, Rueckgabewert ungleich 0.
     Kein Default, keine Annahme, kein "vermutlich Test".

  2  IDEMPOTENT heisst nachgemessen, nicht behauptet.
     Zweimal laufen lassen, danach zaehlen: die Menge muss identisch sein.
     Das ist ein Kriterium im Blatt, kein Satz in der Botschaft.

  3  DAS SKRIPT SAET NUR, WAS DER PRUEFLAUF BRAUCHT.
     Pruefnutzer und Pruefobjekt. Nichts darueber hinaus.
  ```

  **Messbefehl:**
  ```
  Lauf 1 -> Nutzer a24 existiert · Lauf 2 -> Menge IDENTISCH (Auflage 2, gezaehlt)
  Lauf gegen 'ticket' (per Env simuliert) -> Abbruch, Rueckgabe != 0, Wortlaut (Auflage 1)
  Rot-Probe: Browserabnahme OHNE Seed -> faellt mit klarer Meldung, nicht stumm
  Diff: database/ waechst NICHT um einen dauerhaften Seeder (Auflage 3 / Weg A ausgeschlossen)
  ```

  **Heutiges (rotes) Ergebnis:** `a24` wird **2× verwendet und 0× angelegt**; `database/` → **0**.
  *Die Prüfbühne setzt eine Vorbedingung voraus, die niemand herstellt.*

  **Absage-Regel:** Ein dauerhafter Seeder erfüllt (11) **nicht** — das ist Weg A, und Yama hat ihn
  ausdrücklich verworfen: *„ein dauerhafter Seed ist eine zweite Wahrheit … und die Drift ist still."*

  > **Ein Wort des Beschlusses ist nachzuziehen, und ich lege es offen:** Yamas Auflage 1 sagt
  > *„nur gegen `ticket_testing`"*. **Stufe 1 arbeitet gegen `ticket_g1b1_testing`.** Prüft das
  > Skript wörtlich auf `ticket_testing`, **bricht es bei der Stufe-1-Datenbank ab** — die Auflage
  > würde den Lauf verhindern, den sie schützen soll.
  > **Aufgelöst wie bei P-02 Punkt 3: der Zweck trägt, der Name ist jünger.** Gemeint war „die
  > Test-DB, niemals Produktion". **Der exakte Name ist `ticket_g1b1_testing`** (Entscheidung
  > 22.08. 15:12:40). *Die Auflage wird damit nicht aufgeweicht — sie bleibt „exakt oder Abbruch",
  > nur mit dem heute gültigen Namen.*

## Folgeposten (NICHT bauen)

1. **Stufe 2 — vier Datenbanken** `ticket_testing_<rolle>`. Braucht `root`; das GRANT führt nur Yama:
   ```sql
   GRANT ALL PRIVILEGES ON `ticket_testing_%`.* TO 'ticket_user'@'localhost';
   GRANT ALL PRIVILEGES ON `ticket_testing_%`.* TO 'ticket_user'@'127.0.0.1';
   FLUSH PRIVILEGES;
   ```
   **Beide Hosts** — der Fehler dieses Blattes war, dass `@127.0.0.1` fehlte.
   Mit Stufe 2 werden **Z0-I1-7** (parallele Positivprobe) und die ursprüngliche Fassung von
   **Z0-I1-8** wieder wirksam; **Z0-I1-9** (Serialisierung) wird dann überflüssig, **nicht falsch**.

2. **`.env.testing` und `phpunit.xml`** auf `DB_SOCKET` + `DB_DATABASE=ticket_g1b1_testing`.
   *Das ist Teil des Baus, hier nur benannt, damit es nicht als Nebenwirkung geschieht.*

## Was der Umschnitt NICHT ändert

- **Die Musterfrage aus meiner Vorarbeit bleibt:** ein Muster allein lässt `ticket_testing_kopie`
  durch. **Liste UND Muster**, in dieser Reihenfolge — `tests/TestDatenbank.php` baut es bereits so.
- **Y-13 als Operand bleibt im Kopf stehen** — er gilt für Stufe 2. *Nicht löschen: er erklärt,
  warum es zwei Stufen gibt.*

## Nachvollzugs-Matrix Stufe 1 (ARBEITSREGELN §5 / N3) — **maßgeblich**

**Zwei Systematiken standen nebeneinander: `A–G` in der Matrix oben, `Z0-I1-1..11` bei den
Kriterien.** N3 verlangt je Kriterium eine Zeile — mit zwei Zählungen ist nicht entscheidbar,
welche gemeint ist. **Maßgeblich ist ab hier `Z0-I1-n`;** die Buchstaben-Matrix oben bleibt als
Beleg der Vorarbeit stehen und ist **ÜBERHOLT**.

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z0-I1-1 Guard fragt die Verbindung | AP-1 Guard | n.U. | n.U. |
| Z0-I1-2 Negativprobe `ticket` | AP-1 (Negativlauf) | n.U. | n.U. |
| Z0-I1-3 nur `ticket_g1b1_testing` zulässig | AP-1 (Liste + Muster) | n.U. | n.U. |
| Z0-I1-4 `TEST_ROLLE` verpflichtend | AP-2 Rollenpflicht | n.U. | n.U. |
| Z0-I1-5 Zuordnung an EINER Stelle | AP-2 (eine Wahrheit) | n.U. | n.U. |
| Z0-I1-6 vorhandene Wächter ziehen mit | AP-2 (Regressionsprobe) | n.U. | n.U. |
| Z0-I1-9 Läufe serialisiert über DB-Lease | AP-3 Lease | n.U. | n.U. |
| Z0-I1-10 `SELECT DATABASE()` im Beleg | AP-3 (Belegpflicht) | n.U. | n.U. |
| Z0-I1-11 Seed stellt Vorbedingung her | AP-4 Seed, drei Auflagen | n.U. | n.U. |

**Nicht in dieser Matrix, und das ist Absicht:** `Z0-I1-7` und `Z0-I1-8` gehören zu **Stufe 2**
(Folgeposten, braucht `root`). *Ein Kriterium ohne Matrixzeile macht ein Blatt nicht BEREIT-fähig —
ein Kriterium, das ausdrücklich einer anderen Stufe zugewiesen ist, gehört nicht in diese Matrix.*

**Arbeitspakete:** AP-1 Guard und Namensprüfung · AP-2 Rolle und eine Wahrheit ·
AP-3 Serialisierung und Beleg · AP-4 Seed.

## N4 — Bedienweg

**Bedienweg: keiner.** Dies ist die **Prüfbühne**, kein Werkzeug — sie wird nicht bedient, sie trägt.
**Zielreifegrad:** entfällt. *Ihre Zusage ist, dass eine Browserabnahme nicht mehr abbricht, weil ein
zweiter Lauf die Datenbank zurückgesetzt hat.*
