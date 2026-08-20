# KONZEPT — Nachtrag ARBEITSREGELN 1.7: die vier Orchestra-Übernahmen

> **Zustand: ENTWURF, Fassung 3** (Fassung 1 → `REVISE` R1–R10; Fassung 2 → `REVISE` mit zwei
> Rest-Punkten R11a/R11b aus der verengten Zweitrunde, beide hier behoben; Fassung 1 liegt
> unverändert in Git unter `b3a9c6a4`). Dieses Blatt hat
> **keine Prozessautorität**; es gilt erst nach Yamas Freigabe und Einarbeitung in
> `docs/ARBEITSREGELN.md` — als Nachtrag der einen Quelle, nie als Parallelwerk.

**Verfasser-Rolle:** Planner · **Fassung 2:** 20.08.2026, nach Plan-Prüfer-Urteil (Stand `3f9ead3a`)
**Herkunft:** Abgleich des extern vorgeschlagenen „Orchestra"-Modells gegen die gemessene
Rollenkette (sechs von neun Rollen existieren: `1-planner` … `6-integrator`).

---

## 1. Ziel & Entscheidung

**Vier Übernahmen, nicht mehr.** Kein eigenes CLAUDE.md, kein `state.json`, keine eigenen
Gate-Dateien; die vier Neuerungen werden als §-Nachträge eingearbeitet.

**Zielfassung: 1.7** — die nächstfreie Nummer. §19 trägt bereits „Fassung 1.5" (B5, 12.08.) und
„Fassung 1.6" (B6, 12.08.) bei Dateikopf 1.4.2; die Uneinheitlichkeit ist dort dokumentiert und
wird hier **nicht** begradigt (das Regelwerk gehört nicht dem Planner). *(R1)*

### N1 — Integrations-Abnahme

**Entscheidung:** Nach **jedem Integrations-Commit** (Rückweg fremder, freigegebener Arbeit in den
gemeinsamen Zweig) prüft eine Instanz, die an diesem Vorgang **weder gebaut noch integriert noch
das Release-Votum getragen** hat, den integrierten Stand als Ganzes: Zusammenspiel der Pakete,
Testsuite auf dem Integrationsstand, Regressionen, gemeinsame Datenflüsse, Rechtegrenzen.

**Kettenfolge — im Bestand verankert, nicht umgeordnet** *(R2)*: Der Bestand legt fest, dass das
Release-Votum **vor** der Integration steht (§10: letzter Status vor dem Merge ist `RELEASE_FREI`;
`6-integrator/2-WANN-BIN-ICH-DRAN.md`: Auslöser ist ein Commit **mit** Freigabe). Die
Integrations-Abnahme sitzt deshalb **nach** dem Integrator als neuer, letzter Prüfschritt:

```text
4-evaluator (ABGENOMMEN) → 5-release-pruefer (RELEASE_FREI) → 6-integrator (Merge;
  Kind-Commit dokumentiert RELEASE_FREI → VEROEFFENTLICHT — §16-Exklusivregel UNVERÄNDERT)
  → 4-evaluator, FRISCHE INSTANZ (Integrations-Abnahme) → INTEGRATION_GEPRUEFT
  → 5-release-pruefer: BETRIEBSBESTAETIGT
```

**Zustand und seine Lage** *(R5, R11b)*: neuer Zustand `INTEGRATION_GEPRUEFT`, Eigentümerin ist die
prüfende Evaluator-Instanz; **belegt eine `IN_ARBEIT`-Stelle: NEIN** *(berichtigt bei der
Einarbeitung, 20.08.: Fassung 3 sagte hier „ja" — die §3-Angabe misst aber die `IN_ARBEIT`-Schranke,
und eine Integrations-Abnahme ist kein laufender Bau; ein JA hätte jeden neuen Bau grundlos
blockiert. Von keiner der drei Prüfrunden bemerkt, gefunden beim Lesen von §3 vor dem Schnitt)*. Er sitzt **zwischen `VEROEFFENTLICHT`
und `BETRIEBSBESTAETIGT`**: der Merge-Folgecommit dokumentiert weiterhin als einzigen Übergang
`RELEASE_FREI → VEROEFFENTLICHT` (§16 :830-834 bleibt wörtlich unangetastet). **Ausgewiesene
Änderung an §16 :844:** der Übergang des Release-Prüfers lautet künftig
`INTEGRATION_GEPRUEFT → BETRIEBSBESTAETIGT` statt `VEROEFFENTLICHT → BETRIEBSBESTAETIGT` — die
Integrations-Abnahme wird seine benannte Vorbedingung. Das ist eine bewusste Erweiterung einer
bestehenden Regel und wird im Nachtrag als solche geführt, nicht versteckt. Einzuarbeiten in die
§3-Zustandskette **und** die §16-Übergangstabelle; geschrieben wird der Zustand wie jeder andere
über den Integrator als alleinigen STATUS.md-Schreiber.

**FAIL-Routing nach §12-Klassen, nicht fest verdrahtet** *(R3)*: Ein Integrations-FAIL wird wie
jede rote Abnahme klassifiziert — Spezifikationsfehler → Planner; Umgebungsfehler → Umgebungsrolle;
Baufehler → Generator; **Integrationsfehler (falsche Konfliktlösung, falscher Basis-SHA) →
Integrator**, als eigene, bislang unbenannte Klasse der §12.1-Tabelle.

**Zuschnitt:** kein siebter Rollenordner — die Prüfung ist ein **Mandat der Rolle `4-evaluator`**.
„Kein siebter Ordner" heißt **nicht** „kein Blatt anfassen": die Rollenpakete
`4-evaluator/1-AUFTRAG.md` + `2-WANN-BIN-ICH-DRAN.md` (neues Mandat + neuer Auslöser),
`5-release-pruefer/2-WANN-BIN-ICH-DRAN.md` und `6-integrator/2-WANN-BIN-ICH-DRAN.md` werden
nachgezogen. *(Nahtstellen unten)*

**Begründung — berichtigt** *(R4)*: Fassung 1 berief sich auf
`BEFUND-ZWEI-RELEASE-PRUEFER-UND-DER-FEHLENDE-RUECKFLUSS.md`; das war **falsch adressiert** — jener
Befund beschreibt einen Git-Rückfluss der Release-Linie, keine fehlende Prüfung, und sein
Verwirrungsmuster (zwei Instanzen derselben Rolle im selben Vorgang) spricht eher **gegen** als für
den N1-Zuschnitt. Die Berufung ist gestrichen. Tatsächliche Begründung: **§15 zählt den
Pflichtprozess „Planner–Plan-Prüfer–Generator–Evaluator–Release-Prüfer" auf — der Integrator und
eine Prüfung nach ihm kommen darin nicht vor.** Nach dem Merge prüft heute niemand den entstandenen
Stand; genau diese Lücke schließt N1. **Schutz gegen das Phantom-Ball-Muster aus jenem Befund:**
die Integrations-Abnahme führt **keinen** Ball in eigener Sache — Ballführung läuft ausschließlich
über den Integrator in STATUS.md; ihr Bericht nennt eine Instanz-Kennung und die Erklärung, an
welchem Paket des Vorgangs sie **nicht** beteiligt war.

### N2 — Veröffentlichungs-Hook (Gate technisch, Vertretungsregel unangetastet)

**Entscheidung:** Ein `PreToolUse`-Hook blockiert **Veröffentlichungswege**: Push nach `main`,
Push auf `upstream`, Tags, jede `--force`-Variante. **Transport-Pushes bleiben frei** — der Bestand
definiert „Push = Transport, nicht veröffentlicht", und die Vertretungsregel (§4) trägt dem
Release-Prüfer Sicherungs-Pushes auf `fork`/`backup-private` **ohne Einzelrückfrage** auf; ein Hook,
der die blockierte, würde eine Yama-Weisung abschwächen. *(R6)*

**Markensemantik — drei Wege, deckungsgleich mit §4** *(R6, R11a)*: Der Hook prüft eine lokale
Markendatei (`.claude/freigabe-veroeffentlichung`, ungetrackt), die nach dem Push entfernt wird.
Sie entsteht auf genau zwei Wegen:
1. **Yamas Freigabe im Gespräch je Vorgang** — für alles, was keine stehende Vertretung deckt.
2. **§4-Vertretung des Release-Prüfers, unverändert:** für einen Stand mit `RELEASE_FREI` setzt
   der Release-Prüfer die Marke **selbst** und trägt den `RELEASE_FREI`-SHA hinein — das ist
   Dokumentation seiner bestehenden Vollmacht (§4 :205-209: Push von Arbeitszweigen, Merge nach
   `main`, Tags für `RELEASE_FREI`-Stände „ohne Einzelrückfrage"), **keine neue Rückfrage und
   keine Einschränkung von §4**. Der Hook fügt hier nur eines hinzu: den maschinellen Abgleich,
   dass der gepushte Stand der SHA in der Marke ist.
3. Transport-Pushes (`fork`/`backup-private`) brauchen **keine** Marke — siehe oben.

**Gegenprinzip benannt** *(R6)*: Der Bestand dokumentiert am eigenen `post-commit`-Wächter das
Prinzip „ein blockierender Hook wird nach dem dritten Mal umgangen" (AUF-75). Für Commits ist
Melden richtig; für **Veröffentlichung** fällt die Abwägung anders aus: der Schaden eines falschen
Pushes nach `main` ist irreversibel öffentlich, die Blockade eines legitimen kostet Minuten. Jede
Fehlblockade wird als Befund erfasst.

**Reichweite — ehrlich benannt** *(R7)*: Der Hook greift bei Werkzeug-Aufrufen der Agenten-Sitzungen,
**nicht** bei einem von Hand getippten Terminal-Push. Er ist ein Gate für die Instanzen, kein
Systemschutz.

**Verteilung:** committete Vorlage `.claude/settings.hook-vorlage.json` + Einricht-Schritt je
Arbeitsbaum (die lokale `.claude/settings.json` ist git-ignoriert, Beleg `.gitignore:28` und
`79aeb1e6` — dessen Botschaft geteilte Hook-Config ausdrücklich vorsieht). Gemessen 20.08.2026
(flüchtig, Zeitstempel Pflicht): **13 Arbeitsbäume** neben dem Hauptcheckout → 13 Einricht-Schritte. *(R10)*

### N3 — Nachvollzugs-Matrix als §5-Pflichtzeile

**Entscheidung:** Die DoR-Liste in §5 erhält eine Pflichtzeile: **jedes Abnahmekriterium steht in
einer Matrixzeile** `Kriterium → Arbeitspaket → (nach Umsetzung) Commit-SHA → Testbeleg`; ein
Kriterium ohne Zeile macht den Auftrag nicht `BEREIT`-fähig. Der Plan-Prüfer prüft die Matrix
**vor** dem Bau auf Vollständigkeit; der Evaluator prüft **gegen** sie. *(R9)*
Angebunden wird an §5 — **nicht** über den Begriff „Spur": die ARBEITSREGELN kennen dieses Wort
nicht (0 Treffer), es gehört dem Skill `governance-zyklus` und wird in der Regelquelle weder
eingeführt noch vorausgesetzt. *(R9)*

### N4 — Dirigent als benannte Rolle

**Entscheidung:** Agent `.claude/agents/dirigent.md`: orchestriert (Intake, Agentenauswahl nach dem
Roster, Übergaben), führt **keine** Edit/Write-Werkzeuge, gibt keine eigenen Ergebnisse frei;
Freigaben bleiben ausnahmslos bei Yama. Der Fassung-1-Satz zur Agententiefen-Begrenzung ist
**gestrichen** — er war eine unangkündigte fünfte Regel; ob eine Tiefenbegrenzung ins Regelwerk
gehört, ist eine eigene, hier nicht gestellte Frage. *(R9)*

**Unverändert NICHT übernommen:** `state.json` (STATUS.md ist der eine Statusträger, P-01/`8fc5edb8`) ·
Starterpaket-Installation (zweites Regelwerk) · Orchestra-Vollprozess für Kleinvorgänge.

## 2. Einordnung des Vorgangs

Voller Prüfzyklus: Planner → Plan-Prüfer → Yama → Einarbeitung → unabhängige Abnahme. Ein
Regelnachtrag berührt Autorisierung und Prozessrecht; eine Abkürzung gibt es hier nicht.

## 3. Nahtstellen *(R2, R5 — vollständig nach Plan-Prüfer-Frage 4)*

| Wo (Stelle am Stand `3f9ead3a`) | Was |
|---|---|
| ARBEITSREGELN §3 :58-69 · :95-98 | Zustand `INTEGRATION_GEPRUEFT` in die Kette, mit §3-Platz-Angabe |
| ARBEITSREGELN §4 :193 | Rollentrennungs-Satz Release-Prüfer/Integrator um die neue Station ergänzen |
| ARBEITSREGELN §4 :205-212 | Vertretungsregel: N2-Markensemantik ausdrücklich verschränken |
| ARBEITSREGELN §5 :244-273 | N3-Matrix als neue Pflichtzeile der BEREIT-Liste |
| ARBEITSREGELN §10 :388 · :399-412 | Kettenfolge: `RELEASE_FREI` vor Merge bleibt; N1 danach |
| ARBEITSREGELN §12.1 :558-566 | neue FAIL-Klasse „Integrationsfehler → Integrator" |
| ARBEITSREGELN §15 :708-709 | Pflichtprozess-Aufzählung um Integrator + Integrations-Abnahme ergänzen |
| ARBEITSREGELN §16 :830-834 | Exklusivregel Merge-Folgecommit: bleibt wörtlich unangetastet (Beleg im Nachtrag) |
| ARBEITSREGELN §16 :836-847 · :844 | Übergangstabelle um `INTEGRATION_GEPRUEFT`; **ausgewiesene Änderung** :844 (neuer Startpunkt des Release-Prüfer-Übergangs) |
| ARBEITSREGELN §19 | Eintrag „Fassung 1.7" mit Herkunft dieses Blattes |
| Rollenpakete `4-evaluator/` · `5-release-pruefer/` · `6-integrator/` | Mandat, Auslöser, WANN-Blätter |
| `.claude/agents/dirigent.md` | neu (nach Freigabe) |
| `.claude/settings.hook-vorlage.json` | neu, committet |
| `docs/STATUS.md` | **unberührt von Hand** — Zustandszeilen entstehen über den Integrator |

## 4. Kantenliste

1. Ignorierte `settings.json`: ohne Vorlage + 13 Einricht-Schritte ein Gate mit offenen Türen.
2. Hook-Muster: `--force`-Varianten und Deploy-Skripte gehören in die Vorlage, nicht in Prosa.
3. Frische-Nachweis der Integrations-Abnahme: ohne Beteiligungs-Erklärung + Instanz-Kennung ungültig;
   Ballführung nie in eigener Sache (Phantom-Ball-Muster).
4. Matrix als Förmlichkeit: rückwirkend gefüllt beweist sie nichts — Prüfung **vor** dem Bau.
5. Rollenwort-Inflation: „Dirigent" wird einmal definiert; „Intendant" wird nicht eingeführt.
6. Worktree-Drift: bis die Bäume den 1.7-Stand ziehen, gilt 1.4.2 — Stichtag in den Nachtrag.

## 5. Rückweg & Entdeckung

**Rückweg:** alles additiv; Fassungen versioniert (§19), Rücknahme = ein Commit, keine Migration.
Hook trägt keinen Zustand. Stand vor Wirksamwerden gepusht auf `fork`.
**Entdeckung:** Fehlt die Integrations-Abnahme künftig, zeigt STATUS.md Vorgänge, die von
`RELEASE_FREI`/Merge **nicht** nach `INTEGRATION_GEPRUEFT` weiterrücken — eine sichtbare, stehende
Zustandslücke (deshalb braucht N1 den Zustand; ohne ihn wäre das Fehlen unsichtbar). Fehlgriffe des
Hooks melden sich selbst als Blockade und werden als Befund erfasst.

## 6. Abnahmekriterien *(R8 — in Barriere-B5-Form: Zählergebnis MIT Trefferzeilen)*

1. `grep -n "Integrations-Abnahme" docs/ARBEITSREGELN.md` liefert ≥ 2 Treffer, davon einer in §4/§15
   (Mandat) und einer in der Kettenfolge nach dem Integrator — **Trefferzeilen im Beleg zitiert**;
   zusätzlich `grep -n "INTEGRATION_GEPRUEFT" docs/ARBEITSREGELN.md` ≥ 2 (§3-Kette + §16-Tabelle),
   Trefferzeilen zitiert.
2. Hook-Probe, präzisiert: **frisch gestartete Sitzung** in einem Baum mit eingerichteter Vorlage;
   Push-Ziel ist ein **lokales Wegwerf-Bare-Repo** (`file://`-Remote, benannt im Beleg). Ohne Marke:
   Blockade-Rohausgabe. Mit Marke: Durchlass-Rohausgabe. Drittens: ein Push auf ein als `fork`
   benanntes Transport-Remote läuft **ohne** Marke durch (Vertretungsregel unangetastet). Alle drei
   Rohausgaben im Beleg; die Reichweiten-Grenze (nur Agenten-Sitzungen) steht im Nachtragstext.
3. Ein Muster-Auftrag nach 1.7 enthält die Matrix; ein durchgespielter Fall mit einem matrixlosen
   Kriterium wird vom Plan-Prüfer als nicht-`BEREIT` zurückgewiesen — Beleg: Blattpfad + Commit-SHA
   des Musterfalls.
4. `grep -n "Fassung 1.7" docs/ARBEITSREGELN.md` ≥ 1 Treffer **in §19** (Trefferzeile zitiert;
   Abgrenzung: die Alt-Treffer „Fassung 1.5"/„1.6" gehören zu B5/B6 und zählen nicht);
   `.claude/agents/dirigent.md` existiert und `grep -c "Edit\|Write" <tools-Zeile>` = 0.
5. **Kein Commit DIESES Vorgangs ändert `docs/STATUS.md`:** für jeden Einarbeitungs-Commit ist
   `git show <sha> --stat -- docs/STATUS.md` leer — die Commits im Beleg benannt. Fremde Commits
   im selben Zeitraum zählen **nicht**: der Zweig ist geteilt, und parallele Integrator-Arbeit an
   STATUS.md ist dessen legitimes Mandat.
   *(SPEC-Berichtigung nach Evaluator-Rot vom 20.08.: die Erstfassung verlangte einen leeren
   `git log`-Bereich — auf einem geteilten Zweig misst das fremde, legitime Arbeit mit. Der
   Rot-Befund traf die Messvorschrift, nicht den Bau; Klasse SPEC → Planner nach §12.1.)*
   **Wer den Vorgangsumfang festlegt:** nicht der Einarbeitende durch Aufzählen — der Evaluator
   leitet die Vorgangs-Commits **selbst** aus der Branch-Historie ab (`git log` im Zeitraum,
   gefiltert auf Commits, deren Botschaft die Einarbeitung nennt oder deren Diff die
   Nahtstellen-Dateien berührt) und gleicht sie gegen die benannte Liste ab; eine Abweichung ist
   ein Befund. *(Nachtrag auf den zweiten Evaluator-Hinweis vom 20.08. — sonst wäre das Kriterium
   durch Nicht-Nennen umgehbar.)*

## 7. Heimat-App

**ticket**, Branch `auto/hausplaner-integration`. Dieses Blatt ist der Entwurf, nicht der Vollzug.

---

## Anhang: Abarbeitung der zehn Muss-Änderungen aus der Planprüfung (Fassung 1 → 2)

| R# | Behoben durch |
|---|---|
| R1 | Zielfassung 1.7; Kriterium 4 umformuliert mit §19-Eingrenzung und Alt-Treffer-Abgrenzung |
| R2 | Kettenfolge neu: N1 **nach** `RELEASE_FREI`→Merge, in Zuständen ausgeschrieben |
| R3 | FAIL-Routing über §12-Klassen inkl. neuer Klasse Integrationsfehler→Integrator |
| R4 | Berufung auf Zwei-Release-Prüfer-Befund gestrichen; neue Begründung §15-Lücke; Phantom-Ball-Schutz |
| R5 | Zustand `INTEGRATION_GEPRUEFT` mit §3-Platz-Angabe; §3+§16 als Nahtstellen |
| R6 | Hook blockt nur Veröffentlichungswege; Transport frei; Markensemantik definiert; AUF-75-Gegenprinzip adressiert |
| R7 | Probeaufbau: frische Sitzung, Wegwerf-Bare-Repo, Reichweiten-Grenze benannt |
| R8 | Kriterien 1/4/5 in B5-Form (Trefferzeilen, §-Eingrenzung, SHA-Bereich) |
| R9 | N3 an §5 angebunden, „Spur" aus der Regelquelle herausgehalten; Agententiefen-Satz gestrichen |
| R10 | 13 Arbeitsbäume, gemessen 20.08.2026 mit Zeitstempel-Pflicht als flüchtige Messung |

### Zweitrunde (Fassung 2 → 3)

| R# | Befund der Zweitrunde | Behoben durch |
|---|---|---|
| R11a | Marken-Pflicht für Push/Merge nach `main` drehte die §4-Vertretung des Release-Prüfers für `RELEASE_FREI`-Stände in eine Einzelfreigabe zurück | Markensemantik-Weg 2: der Release-Prüfer setzt die Marke selbst kraft bestehender Vollmacht, mit `RELEASE_FREI`-SHA; §4 unverändert |
| R11b | Lage von `INTEGRATION_GEPRUEFT` relativ zu `VEROEFFENTLICHT`/`BETRIEBSBESTAETIGT` undefiniert; Kollision mit §16-Exklusivregel bzw. -Eigentümerschaft | Lage festgelegt: zwischen `VEROEFFENTLICHT` und `BETRIEBSBESTAETIGT`; §16 :830-834 unangetastet; Änderung an §16 :844 **ausgewiesen** statt verschwiegen |

**Ball:** Schluss-Nachprüfung nur R11a/R11b, danach Vorlage an Yama.
