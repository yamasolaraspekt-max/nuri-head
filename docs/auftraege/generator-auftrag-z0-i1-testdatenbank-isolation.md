# Z0-I1 · Testdatenbanken isolieren: eine Datenbank je Prüfstand, Namensprüfung vor dem ersten Schreibzugriff

```yaml
zustand: ENTWURF
welle: 0 / Infrastruktur — Gesamtauftrag v2 Phase 3; VOR weiteren parallelen Abnahmen
basis_sha: 976f7d6b
herkunft: Evaluator-Abnahme belegt: parallele Laeufe setzen dieselbe ticket_testing zurueck, Testkonto verschwand waehrend einer Browserabnahme (Gesamtauftrag v2, Phase 3)
spur: A — Testinfrastruktur, Datenbank (nur Testdatenbanken), Schutzgrenze 'Tests nur gegen eindeutig benannte Testdatenbanken'
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
operand: Y-13 — Anlage der Datenbanken braucht MySQL-Rechte: ticket_user hat (vermutlich) kein CREATE DATABASE; Root haelt Yama. Entweder Yama legt ticket_testing_{evaluator,generator,security,browser} an, oder erteilt ticket_user CREATE auf 'ticket_testing\_%'. OHNE Y-13 ist nur die Namenspruefung (Teil B) baubar.
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

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute kein Guard).

## Rückweg
Config/Guard per Commit zurückdrehbar; zusätzliche Test-DBs schaden nicht (Löschung später nur mit Freigabe).
