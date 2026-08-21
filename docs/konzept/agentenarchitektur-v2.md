# KONZEPT — Agentenarchitektur V2: sechs Kernrollen, Dirigent als Router, temporäre Fachfähigkeiten

```yaml
zustand: "ENTWURF Fassung 2 (vier Korrekturen Yama eingearbeitet) — Yama 21.08.2026 spaet; Phase A: NUR Konzept, KEINE neuen Agenten, Abschlussmodus nicht erweitern; Einfuehrung gestuft B/C/D nach TESTBEREIT; unabhaengige Pruefung (Plan-Pruefer) steht aus"
ersetzt: "den ersten 'Orchestra'-Vorschlag in seinen Machtrollen-Teilen; bestaetigt ARBEITSREGELN 1.7 N1 (Integrations-Abnahme = frische Evaluator-Instanz, kein siebter Ordner) und N4 (Dirigent ohne Produktcode)"
bewertung_yama: "21.08. spaet: als Architekturkonzept 9/10 — uebernehmbar nach vier Korrekturen (eingearbeitet in Fassung 2: Z0-I1 NICHT eingeloest; Dirigent-Schreibrecht praezise; Lease-Autoritaet; Belegform SHA+Pfad+Anker). Fuer das laufende System ein ZIELBILD — technische Wirksamkeit erst durch die Negativproben bewiesen."
spannung_offen: "V2 beschreibt den Dirigenten als machtlosen Router (keine Kriterien, keine Produktentscheidungen, keine Voten, keine Konfliktloesung, keine Freigabe) — docs/regelwerk/VOLLMACHT-DIRIGENT.md (Yama 21.08. frueher) laesst ihn delegierte Yama-Posten entscheiden. Vorschlag Dirigent: Vollmacht bis zum Abschlussurteil unveraendert (Stabilitaet), ab Phase B Dirigent = Router nach V2, Entscheidungen zurueck zu Planner/Yama — Yama entscheidet, ob frueher. Die SCHREIBFRAGE ist entschieden (Yama 21.08.): siehe Abschnitt 3."
kern: "Nicht mehr Rollen schaffen, sondern die bestehenden Rollen durch temporaere Fachfaehigkeiten ergaenzen."
```

## Selbstkritik des ersten Konzepts (Yama)
| Schwäche | Warum problematisch | Verbesserung |
|---|---|---|
| Zu viele scheinbare Autoritätsrollen | mehr Übergaben, neue Ballkonflikte | sechs Kernrollen behalten |
| „Integrations-Evaluator" als zusätzliche Rolle | existiert als frische Evaluator-Instanz | Mandat verwenden, kein siebter Rollenordner |
| Dirigent als Autoritätsfunktion | könnte ungewollt entscheiden | Dirigent nur als restartbarer Router |
| ein Agent je Anforderung | Agentenexplosion, widersprüchliche Teilpläne | Agenten nach Risikodomäne bündeln |
| Spezialisten ausschließlich read-only | manche Fragen brauchen Experimente | getrennte, nicht mergebare Experimentierumgebung |
| Pfad-Leases als Hauptschutz | verschiedene Dateien ändern denselben Vertrag | zusätzlich Vertrags-, Schema-, Ressourcenabhängigkeiten |
| konfliktbasiertes WIP nur nach Pfaden | semantische Konflikte unsichtbar | Abhängigkeitsgraph + Contract Freeze |
| „Ereignisarchitektur" für Status | zweite Wahrheit neben `STATUS.md` | bestehende Git-Zustandskette härten |
| kein Schutz vor vorbelastetem Evaluator | Fachagent prüft sich später selbst | Beteiligungs-/Unabhängigkeitsmatrix |
| keine Obergrenze für Spezialisten | Aufwand wächst schneller als Nutzen | 0–2, höchstens 3 bei Hochrisiko |
| keine Ausfallregeln für Leases | abgestürzte Agenten blockieren dauerhaft | TTL, Heartbeat, Fencing Token |
| kein gestufter Einführungsplan | Umbau destabilisiert laufende Arbeit | Pilot nach Abschlussmodus |
| „ein Generator immer" zu starr | später unnötig langsam | zunächst einer; parallele Writer nur nach eingefrorenem Vertrag |
| kein messbarer Nutzennachweis | Agenten erzeugen nur Berichte | jeder Fachagent bedient ein Kriterium oder Risiko |

## 1 · Drei Ebenen statt vieler gleichberechtigter Agenten
```text
                         Yama — Entscheidung/Freigabe
                           │
                    Kontroll-Ebene: Dirigent — Routing · Priorität · Ressourcen · Alter
                           │
               Spezifikations-/Bau-Ebene: Planner → Plan-Prüfer → Generator → Evaluator
                           │
                 Integrations-/Release-Ebene: Integrator → frischer Evaluator → Release-Prüfer
```
Seitlich zuarbeitend, keine Autoritätsebene: Architekt/BIM · UX/Browser · Security · Datenmodell ·
3D/Rendering · Testinfra → Befunde an Planner oder Evaluator.

## 2 · Autoritätsrollen bleiben unverändert
**Planner** besitzt Ziel/Nicht-Ziele, Plan/Scope, Abhängigkeiten, Kriterien, Modellentscheidungen,
Rückweg; darf Fachagenten beauftragen, führt deren Ergebnisse zu **einem** konsistenten Plan zusammen.
**Plan-Prüfer** prüft Machbarkeit, Messbarkeit, erfüllbare Kriterien, vollständige Risikokanten,
richtige Agentenauswahl, eingefrorene Verträge — das Zusammenspiel, nicht nur Einzelberichte.
**Generator**: genau ein verantwortlicher Writer je Arbeitspaket; Hilfsagenten nur ohne überlappende
Produktdateien, ohne gemeinsamen veränderlichen Zustand, ohne Kriterienänderung, mit klar begrenztem
Teil; der Hauptgenerator verantwortet den Gesamtcommit. **Evaluator**: frische, unbeteiligte Instanz
prüft Plan, Diff, Code, Tests, Gegenprobe, Browser; liest den Generatorbericht erst nach eigener
Prüfung. **Integrator**: übernimmt genau einen freigegebenen Gegenstand, löst keinen fachlichen
Konflikt, schreibt als Einziger den Status, stoppt bei unklarer Herkunft. **Integrations-Abnahme**:
keine neue Rolle — frische Evaluator-Instanz nach der Integration (integrierter SHA, Zusammenspiel,
gemeinsame Tests, Rechte-/Datenflüsse, Integrationsregressionen). **Release-Prüfer**:
Reproduzierbarkeit, Artefakte, Migration, Rückweg, tatsächlich freigegebener Inhaltsstand.

## 3 · Der Dirigent ist keine Fach- oder Freigaberolle
**Darf:** Anforderungen klassifizieren · Risikoklassen bestimmen · Fachagenten auswählen · Worktrees
und Testressourcen zuweisen lassen · Abhängigkeiten/Blocker anzeigen · Übergaben anstoßen ·
WIP-Limits überwachen. **Darf nicht:** Kriterien erfinden · Produktentscheidungen treffen · Code
schreiben · Voten erteilen · Status fachlich verändern · Konflikte lösen · Veröffentlichung freigeben.
**Schreibrecht, präzise (Yama 21.08., löst den Widerspruch „ohne Bauwerkzeuge" ↔ „ohne Edit/Write"):**
*Der Dirigent darf Steuerungs- und Konzeptdokumente vorbereiten, aber keinen Produktcode, keine
Kriterien, keine Voten und keinen fachlichen Status schreiben.* Folge für das Haus: `dirigent` ist
eine **registrierte** Rolle (eigener Worktree `ticket-rolle-dirigent`, Zweig `rolle/dirigent`,
Tor- und Rückwegdefinition) mit **technisch begrenztem Schreibbereich** — `docs/konzept/`,
`docs/regelwerk/` (Steuerung), `docs/auftraege/` nur für Steuerungsblätter ohne Kriterien
(Gesamtaufträge, Abschlussmodus, Maßstäbe); **Auftragsblätter mit Kriterien schreibt der Planner**,
Voten der Evaluator, `docs/STATUS.md`/`docs/BEFUNDNOTIZEN.md` der Integrator. Der Zwischenzustand
„schreibender Zweig, aber unbekannte Rolle" ist nicht zulässig (A-37-Erweiterung).
**Vollständig restartbar:** Wissen nicht nur in der Sitzung; Rekonstruktion aus Statuswahrheit,
Auftragspaket, Git-SHAs, Ressourcen-Leases, letzten unabhängigen Voten — kein Single Point of Failure.

## 4 · Eine Arbeitszelle pro Auftrag
1 verantwortlicher Planner · 0–2 Fachagenten (Hochrisiko max. 3) · 1 Plan-Prüfer · 1
verantwortlicher Generator · 1 unabhängiger Evaluator · gemeinsamer Integrator-/Release-Dienst.
Ein Fachagent nur für eine klar getrennte Frage, z.B.:
```yaml
fachfrage: "Kann die Bodenplatte additiv eingefuehrt werden, ohne CeilingNode oder Bestandsdokumente umzudeuten?"
liefert: [Modellvarianten, Migrationsrisiken, betroffene Konsumenten, empfohlene Variante]
darf_nicht: [Produktcode aendern, Kriterien setzen, Status veraendern]
```

## 5 · Fachagenten nach Risiko, nicht je Kriterium
Gebäudemodell/Geschosse/Dach → Architekt/BIM · Bedienablauf/Tastatur/Fehlerführung → UX ·
`SceneDocument`/Schema/Commands → Datenmodell/Persistenz · Rechte/Identität/Mandanten → Security ·
Migrationen/Datenrückweg → Datenbank/Migration · Geometrie/Berechnungen → CAD/Geometrie ·
PBR/Licht/Schichtenrenderer → 3D/Rendering · sichtbare Wirkung → Browser/Accessibility ·
Paralleltests/DB-Kollisionen → Testinfrastruktur · Release/Deployment → Release/Operations.
**Standard:** gering = kein Zusatzagent · mittel = einer · hoch = zwei · kritisches Querschnittsthema =
max. drei; mehr nur mit ausdrücklicher Planner-Begründung.

## 6 · Experimente erlauben, aber strikt isolieren
Erlaubt, wenn: Wegwerf-Checkout/temporäres Verzeichnis · kein Produktbranch verändert · nicht direkt
gemergt/cherry-picked · ausdrücklich `EXPERIMENT` · Ergebnis und Grenzen dokumentiert · der
Generator baut die endgültige Lösung gegen den freigegebenen Plan selbst.

## 7 · Parallelität braucht mehr als getrennte Dateipfade
Gleichzeitig schreiben nur bei getrennten Mengen: Produktdateien · gemeinsam genutzte Typen/
Schnittstellen · Schema/Migrationen · generierte Bundles · Datenbanken · Ports · Browserkonten ·
temporäre Verzeichnisse · Integrationsziel · fachliche Entscheidungen. Renderer konsumiert
`SceneDocument` → derselbe Vertrag → vorher **Contract Freeze**:
```yaml
vertrag: {name: roof-layers-v1, sha256: ..., produzent: GP-0, konsumenten: [GP-2-command, GP-2-renderer]}
aenderung_nach_freeze: {folge: "beide Auftraege stoppen und zurueck zum Planner"}
```
**WIP-Stufen:** *Jetzt* (bis A-37 + Z0-I1 abgenommen): höchstens ein schreibender Produktauftrag,
Fachanalyse nur lesend/experimentell isoliert, keine parallelen DB-Läufe. *Erste Ausbaustufe:* ein
Hochrisiko-Writer, zusätzliche read-only Fachagenten, Evaluator und Browser auf eigener DB. *Später*
(nach Pilot + Zehnerauswertung): max. zwei schreibende Zellen bei getrennten Verträgen/Ressourcen,
weiterhin eine Integrationswarteschlange.

## 8 · Ressourcen-Leases technisch absichern
Lease = lokale technische Ressourcensperre (keine zweite Statuswahrheit): `lease_id, auftrag, rolle,
worktree, branch, basis_sha, vertrag_sha256, schreibbereiche[], testdatenbank, port, browserprofil,
fencing_token, heartbeat_bis`. Regeln: atomare Vergabe · begrenzte Laufzeit · Heartbeat · kontrollierte
Verlängerung · neuer Besitzer erhält höheren Fencing Token · alter Token darf weder committen noch
testen · Verlust = sofortiger Stopp · abgestürzte Agenten werden nie automatisch committet.
**Lease-Autorität (Korrektur 3, Yama 21.08. — festgelegt als ENTWURF für Z0-I2):** genau **ein**
lokaler Ort außerhalb aller Worktrees, `/Users/yamanuri/Documents/ticket-leases/<auftrag>/`, mit
**drei getrennten Dingen** (Korrektur Yama 21.08., zweite Runde — Zähler und Sperre dürfen nicht im
Lease-Verzeichnis liegen, sonst geht die Monotonie mit der Löschung des Sperrverzeichnisses verloren):
```text
ticket-leases/<auftrag>/
├── counter          # dauerhaft, wird NIE gelöscht — einzige Quelle des Fencing Tokens
├── counter.lock/    # mkdir-Sperre nur für das Erhöhen des Zählers (kurzlebig)
└── active/          # existiert genau dann, wenn ein Lease aktiv ist; mkdir = Vergabe (atomar)
    └── lease.yaml   # Inhaber, Rolle, Worktree, Zweig, Basis-SHA, fencing_token, heartbeat_bis
```
**Vergabe — robuste Reihenfolge (Crash-Kante Yama 21.08., dritte Runde; Z0-I2-Implementierungsregel,
vom Plan-Prüfer mitzuprüfen):** *Naiv* wäre `mkdir active/` → dann `lease.yaml` schreiben; stürzt der
Prozess dazwischen ab, existiert `active/` **ohne** `heartbeat_bis`, und keine Ablaufprüfung kann
entscheiden, wann eine Übernahme erlaubt ist. *Ebenso naiv* wäre, die Sperre nur für den Zähler zu
halten (Parallelrennen, Yama 21.08., vierte Runde): A erhöht auf 1 und löst, B erhöht auf 2 und löst,
A benennt `tmp.1` erfolgreich nach `active/` um — A besitzt `active/` mit Token 1 < Counter 2, die
Hooks lehnen A ab, B kann nicht umbenennen: ein gültig aussehendes, sofort veraltetes Lease.
**Deshalb serialisiert `counter.lock/` den gesamten kurzen Vergabevorgang (Endfassung):**
1. `counter.lock/` atomar anlegen (`mkdir`; scheitert → kurz warten, erneut; niemals wegräumen,
   solange frisch);
2. vorhandenes `active/` prüfen: `heartbeat_bis` gültig → Vergabe **ablehnen**; abgelaufen →
   kontrolliert entfernen (nur hier, unter der Sperre);
3. `counter` erhöhen und **dauerhaft** schreiben (`fsync`);
4. `active.tmp.<token>/lease.yaml` **vollständig** schreiben (inkl. `heartbeat_bis`);
5. Datei **und** temporäres Verzeichnis mit `fsync` sichern;
6. **atomar und ohne Überschreiben** nach `active/` umbenennen (`rename(2)`, scheitert bei Existenz
   → ablehnen, Token verfällt);
7. Elternverzeichnis mit `fsync` sichern;
8. **erst danach** `counter.lock/` lösen;
9. Abbruchreste (`active.tmp.*` ohne erfolgreiches Rename) **ausschließlich unter dieser Sperre**
   bereinigen; `active/` bleibt die einzige Wahrheit.
Damit können zwei Bewerber weder den Gewinner veralten lassen noch gleichzeitig übernehmen; ein
unter der Sperre abgestürzter Bewerber hinterlässt `counter.lock/` — sie gilt nach fester, kurzer
Frist (Sekunden, nicht Minuten) als verwaist und darf vom nächsten Bewerber entfernt werden, weil
unter ihr nie ein Lease entsteht, das nicht in `active/` sichtbar wäre.
Übernahme nach Ablauf: `active/` darf nur entfernt werden, wenn `heartbeat_bis` verstrichen ist; der
neue Bewerber erhält aus dem **dauerhaften** `counter` zwingend einen höheren Token — ein Token kann
nach Löschung von `active/` nie wiederverwendet werden. **Ablehnung veralteter Token durch drei Hooks**: (1) `scripts/commit-pruefen.sh` vor jedem Commit (Lease
vorhanden · Inhaber = `TICKET_ROLLE`+Worktree · Token = aktueller · Heartbeat nicht abgelaufen, sonst
Exit ≠ 0, kein Commit); (2) der Test-/DB-Wrapper aus Z0-I1 vor Migration/Seed/Truncate; (3) der
PreToolUse-Hook `veroeffentlichungs-tor.sh`-Muster für `git push` des Rollenzweigs. Keine Lease-
Kopie im Repo, kein Lease in `docs/STATUS.md` — die Lease ist Betriebszustand, nicht Wahrheit.
Ein abgelaufenes Lease hebt kein Mensch von Hand auf: neuer Bewerber wartet TTL ab, erhöht den
Token, der alte Inhaber scheitert beim nächsten Hook sichtbar.

## 9 · Unabhängigkeit als Beteiligungsmatrix
Je Agent und Auftrag: `plan_verfasst, kriterium_entworfen, code_geschrieben, tests_geschrieben,
integriert, release_geprueft`. Regeln: Planner ≠ Plan-Prüfer · Generator ≠ Evaluator · Integrator ≠
Integrations-Evaluator · Release-Prüfer weder Generator, Evaluator noch Integrator desselben Vorgangs ·
ein Fachagent, dessen Lösung in den Plan übernommen wurde, ist später nur Zeuge/Fachauskunft, nie
alleiniger Evaluator · Evaluator in frischer Instanz und getrenntem Checkout.

## 10 · Keine neue Statusplattform
`docs/STATUS.md` bleibt die eine Wahrheit · kein `state.json` · keine zweite Agentendatenbank ·
Zustandswechsel über ein validiertes Übergangswerkzeug · nur der Integrator schreibt · HTML aus
festgeschriebenem Snapshot. Das Übergangswerkzeug prüft: erlaubter Vorgängerzustand, verantwortliche
Rolle, Plan-Hash, Inhalts-SHA, Prüf-SHA, Votum, Ball, genau ein Zustandswechsel, keine Produktdatei
im Statuscommit.

## 11 · Verbesserter Übergabevertrag
`auftrag, rolle, basis_sha, plan_sha256, vertrag_sha256, risikoklasse, eingangsbedingungen[],
leases{worktree, datenbank}, scope{erlaubt[], verboten[]}, entscheidungen[], annahmen[],
abweichungen[], commit, tests{befehle[], ergebnisse[]}, gegenprobe, browser, offene_punkte[],
naechste_rolle, beteiligungsnachweis` — jede Zahl mit Befehl, SHA, Ausgabe; jeder Übernehmende prüft
Plan- und Vertrags-Hash vor Beginn.
**Belegform (Korrektur 4, Yama 21.08.):** dauerhafte Belege enthalten immer
`Commit-SHA + Dateipfad + stabiler Anker` (Anker = `auftrag:`-Kennung, Überschrift, Funktionsname,
Paragraf — kein nackter Zeilenzeiger). Beleg `e1298913` (Plan-Prüfer 280): nach A-42s Umzug zeigten
23 von 124 Verweisen auf `docs/STATUS.md` ins Leere oder auf anderes; `docs/STATUS.md:18787` reicht
nicht. Regelwerk-Kandidat für ARBEITSREGELN (B5 Beleg-Befehl ergänzen).

## 12 · Konflikt- und Stopplogik
Fachagenten widersprechen sich → Planner dokumentiert beide Wege und entscheidet · Entscheidung ändert
Produktumfang → ggf. Yama · unerfüllbares Kriterium → `SPEC_BLOCKED` zum Planner · Umgebung
verhindert Prüfung → `ENV_BLOCKED` · Lease verloren → Stopp · Basis-SHA bewegt → Plan erneut prüfen ·
Vertrags-Hash geändert → alle abhängigen Writer stoppen · fremder Diff im Worktree → kein Commit ·
Integrationskonflikt → Integrator stoppt, fachliche Rolle entscheidet · Browser nicht verfügbar →
sichtbare Abnahme bleibt offen · Fachagent ohne kriteriumsbezogenen Mehrwert → Bericht verwerfen,
Agent nicht erneut einsetzen.

## 13 · Optimale Zellen für den Golden Path
**GP-0**: Planner-Architect als Planner · BIM-Fachagent · Datenmodell-/Migrationsagent · optional
Command-/Undo-Fachagent · Plan-Prüfer · genau ein Generator · unabhängiger Evaluator — keine parallelen
Writer (`SceneDocument`, Schema, Migration, Commands = ein Vertrag). **GP-1**: Planner · UX ·
Browser/Accessibility · ein UI-Generator · Browser-Evaluator. **GP-2**: zunächst ein Generator;
Trennung Commands/Persistenz vs. Renderer erst nach eingefrorenem Schichtenmodell + GP-0-Abnahme
prüfen. **GP-3**: kein neuer Produktgenerator · Modell-/Persistenzevaluator · Browser-Evaluator ·
Integrations-Evaluator auf dem zusammengeführten SHA · Release-Prüfer.

## 14 · Kontrollierte Einführung (darf den Abschlussmodus nicht erweitern)
**Phase A — jetzt:** Konzept nur `ENTWURF`, keine neuen Agenten, A-42/A-37/Z0-I1 abschließen.
**Phase B — erster Pilot (GP-0-Planung):** ein Planner, zwei read-only Fachagenten, ein Plan-Prüfer,
kein Produktcode; messen: doppelte Befunde, echte Zusatzerkenntnisse, Widersprüche, Zeit bis zum
freigegebenen Plan, spätere SPEC-Fehler. **Phase C — technischer Pilot:** ein Generator mit Lease,
ein unabhängiger Evaluator, Statusübergang über den Integrator. **Phase D — Parallelisierung
bewerten** nach zehn Vorgängen: Erstfreigabequote, SPEC_BLOCKED beim Generator, Evaluator-
Nachbesserungen, Statusdrift, Ressourcenkollisionen, Integrationskonflikte, Zeitgewinn; zweiter Writer
nur ohne Sicherheits-/Eigentumsverletzung und ohne steigende Nachbesserungsrate.

## Harte Abnahme dieser Architektur (synthetische Gegenproben)
falsche Rolle im Worktree blockiert · zwei Writer erhalten nicht denselben Vertrag · zwei DB-Läufe
nicht dieselbe Datenbank · veralteter Fencing Token abgewiesen · abgelaufenes Lease stoppt den Writer ·
beteiligter Generator kann nicht Evaluator werden · geänderter Plan-Hash stoppt den Bau · geänderter
Vertrags-Hash stoppt alle Konsumenten · ungültiger Statusübergang abgewiesen · zweiter
Statusgeneratorlauf diff-frei · abgestürzter Agent hinterlässt keinen automatischen Commit · Dirigent
aus Repository und Leases vollständig neu startbar · jeder zusätzliche Fachagent hat einen eindeutigen,
kriteriumsbezogenen Beitrag.

## Endgültige Empfehlung (Yama): V2 übernehmen
sechs Kernrollen · Dirigent als machtloser, restartbarer Router · Integrations-Abnahme durch frische
Evaluator-Instanz · temporäre Fachagenten statt Dauerrollen · max. drei bei Hochrisiko · ein Writer je
Vertragsbereich · Contract Freeze zusätzlich zu Pfadtrennung · technisch abgesicherte Leases ·
Beteiligungsmatrix · bestehende Git-Statuskette · schrittweise Einführung mit messbarem Pilot.

## Bezug zum Bestand (Dirigent, 21.08.)
- Bereits eingelöst: N1 (Integrations-Abnahme = frisches Evaluator-Mandat), N4 (Dirigent ohne
  Produktcode; Schreibrecht nach Abschnitt 3), Rollen-Worktrees für sechs Rollen,
  `RECHTE_ALLE_FUER_ALLE`-Schalter, Stage-Scope-Abbruch.
- **Nicht** eingelöst (Korrektur 1, Yama 21.08.): **Z0-I1** ist ein Auftrag im `ENTWURF` — vier DBs,
  Guard, Parallel-/Kollisionsprobe sind weder gebaut noch unabhängig abgenommen. Ebenso nicht:
  Registrierung `dirigent` und `release-pruefer` im Rückweg (`scripts/rueckweg.py` führt fünf Bäume),
  `dirigent` im Tor des Integrationsstands, Schreibbarriere für `docs/BEFUNDNOTIZEN.md`,
  Claim-Sperre je Auftrag (W0-5 wurde zweimal gebaut: `28ca0834`, `ef7a8c89`).
- Noch nicht: Contract Freeze, Leases mit Fencing/TTL (Autorität in Abschnitt 8), Beteiligungsmatrix,
  Übergangswerkzeug mit Plan-/Vertrags-Hash, Experiment-Kennzeichnung, Übergabevertrag-Schema,
  Belegform → Kandidaten für G-1/G-2/G-3 aus `governance-automatisierung-zielbild.md`, nach Phase A.
- **Heutige Dirigent-Praxis weicht von V2 ab** (Planner-Vorarbeit in Personalunion, delegierte
  Entscheidungen per Vollmacht): bewusst beibehalten bis zum Abschlussurteil, danach nach V2 entzerrt.
