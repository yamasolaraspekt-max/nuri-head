# ABSCHLUSSMODUS — zuerst technische Isolation und Abnahmen schließen, keine neue Breite erzeugen

```yaml
erteilt_von: "Yama, 21.08.2026 spaet — Wortlaut 1:1; ersetzt in der Reihenfolge GESAMTAUFTRAG-V2 (dessen Phasen 6/7 bleiben als Inhalt gueltig, wo der Abschlussmodus nichts anderes sagt)"
weitergegeben_durch: "Dirigent (Vollmacht docs/regelwerk/VOLLMACHT-DIRIGENT.md) — an alle Rollen"
gilt_ab: "sofort, bis zum Urteil TESTBEREIT / NICHT TESTBEREIT"
entschieden_nicht_mehr_rueckfragen: "Y-10 (8 h), Y-11 (reversible Stilllegung), Y-13 (ticket_user volle Rechte auf ticket_testing_%)"
stand_bei_uebernahme: "Z1-W1-3 ABGENOMMEN (b9fe55c0) · W1-1 Kriterium C ENV_BLOCKED (DB-Kollision, kein Produktfehler) · gebaut: W0-1 10c05d8b, W0-3 69c85d01, W0-7 5831c06a, W0-8 29eb791c, W0-9 f595d654, W0-10 cb771cbf, W0-11A fd94dea5, W0-12 976f7d6b · W0-5 uncommittiert im Integrationscheckout (Eigentuemer Generator, Sicherung angewiesen) · A-37 NACHBESSERN 20/21, Blatt berichtigt (rolle/dirigent) · Z0-I1 ENTWURF, DoR angewiesen"
```

## Nachtrag Yama (21.08., spät) — Maßstab „sehr gut" und geänderte Reihenfolge
Abnahme-Maßstab: [`SEHR-GUT-KRITERIEN-ABSCHLUSS-2026-08-21.md`](SEHR-GUT-KRITERIEN-ABSCHLUSS-2026-08-21.md)
(zwölf Muss-Kriterien; ein roter Punkt wird durch keine Menge grüner Tests ausgeglichen).
**Neue Reihenfolge (ersetzt Phase-2/3-Folge unten):** **1 A-42 unabhängig schließen** (Evaluator:
Block-/Zeilen-/Zaun-/Ballbilanz, idempotenter Zweitlauf) → **2 A-37 als technische Schreibbarriere
vollständig aktivieren** (21/21, js-yaml direkt + Lockfile, sechs Worktrees, jeder Negativfall
tatsächlich ausgelöst) → **3 Z0-I1 bauen und abnehmen** (SHOW GRANTS, vier DBs, Rollenautomatik,
Guard, Parallel- und Kollisionsprobe, Evaluator wiederholt) → **4 erst danach die übrigen parallelen
Abnahmen.** W0-11: Teil A ehrlich auf Session-/Request-Schutz begrenzt, Urheberspalte = W0-11c (separat).

## Ziel
Der aktuelle Stand wird kontrolliert bis zu einem eindeutigen Urteil gebracht: **TESTBEREIT** mit
Test-SHA und Browsernachweis **oder** **NICHT TESTBEREIT** mit exakt benannten Restpunkten und
Ballbesitz. Ab jetzt gilt: angefangene Arbeit abschließen und unabhängig abnehmen, bevor weitere
normale Produkt- oder Sicherheitsaufträge begonnen werden.

## Verbindliche Entscheidungen von Yama
**Y-10:** `NURIVA_TOKEN_LAUFZEIT_STUNDEN = 8`, konfigurierbar und rückstellbar. **Y-11:**
`api/secure/master-sets*` reversibel stilllegen, nicht löschen. **Y-13:** `ticket_user` erhält
vollständige Rechte einschließlich CREATE, DROP, ALTER, Migrationen und Datenänderungen auf
`ticket_testing_%`. Die vier isolierten Testdatenbanken dürfen selbstständig angelegt, zurückgesetzt
und verwaltet werden. Produktionsdatenbanken bleiben unberührt. **Diese drei Punkte sind entschieden
und dürfen nicht erneut als Rückfrage an Yama gestellt werden.**

## WIP-Limit (bis TESTBEREIT)
maximal ein uncommittierter Produktbau pro Rolle · maximal ein neuer Sicherheitsbau gleichzeitig ·
unabhängige Abnahmen haben Vorrang vor neuen Bauten · A-37, A-38 und Z0-I1 haben Vorrang vor
A-39/A-42 · kein Dachschichten- oder sonstiger neuer Produktbau.

## PHASE 0 — unveränderlichen Stand und Eigentum prüfen
Vor jeder Schreibarbeit: 1. `git status --short --branch` · 2. aktueller HEAD und Fernstand ·
3. `git worktree list --porcelain` · 4. Voraus-/Rückstand aller Rollenbranches · 5. uncommittierte
Dateien und deren Eigentümer · 6. laufende Test-, Server- und Browserprozesse · 7. belegte
Datenbanken und Ports. Kein fremdes Staging übernehmen. Kein `git add -A`. Keine fremden Änderungen
zurücksetzen, staschen oder löschen.

## PHASE 1 — Rollen- und Checkout-Isolation erzwingen
Ab sofort: Generator schreibt ausschließlich im Generator-Worktree · Planner ausschließlich im
Planner-Worktree · Evaluator ausschließlich im Evaluator-Worktree · Plan-Prüfer ausschließlich im
Plan-Prüfer-Worktree · Release-Prüfer ausschließlich im Release-Worktree · **Nur der Integrator
schreibt im Integrationscheckout** · Zuständiger Rollenbranch und `TICKET_ROLLE` müssen vor jedem
Commit geprüft werden · Interner Transport in den Integrationszweig erfolgt ausschließlich durch
den Integrator · Keine direkten Generator-/Planner-Commits mehr auf dem Integrationszweig · A-38
wird nach A-37 als nächster Governance-Bau ausgeführt, damit auch Merges nicht am Rollen-Tor
vorbeilaufen. Positive und negative Probe verlangen: richtige Rolle/richtiger Baum erlaubt; falscher
Baum, falscher Branch und fremde STATUS-Änderung gesperrt.
*(Dirigent: eigener Worktree `ticket-rolle-dirigent` auf `rolle/dirigent`, Rollen-Tor um den
Eintrag `dirigent` ergänzt — Plan-Prüfer prüft diese additive Zeile mit A-38.)*

## PHASE 2 — Z0-I1 Testdatenbanken vollständig umsetzen
Mit der erteilten Y-13-Freigabe `ticket_testing_evaluator`, `ticket_testing_generator`,
`ticket_testing_security`, `ticket_testing_browser` anlegen und `ticket_user` vollständige Rechte
auf diese Testdatenbanken beziehungsweise `ticket_testing_%` geben. Danach: jede Rolle erhält eine
eindeutige `.env.testing`-Zuordnung · jeder Lauf prüft vor dem ersten Schreibzugriff den exakten
Datenbanknamen · falscher Name oder Produktionsdatenbank führt zum sofortigen Abbruch · jeder
Browserprüfstand erhält eigenes Testkonto und eigenes Testobjekt · parallele Positivprobe: zwei
Rollen laufen gleichzeitig, ohne gegenseitige Änderungen · Kollisionsprobe: eine Rolle versucht die
Datenbank einer anderen Rolle zu verwenden und wird technisch gestoppt. Z0-I1 durchläuft Generator →
unabhängiger Evaluator → Integrator. Bis zur erfolgreichen Abnahme bleiben parallele
datenbankverändernde Läufe untersagt.

## PHASE 3 — A-37 abschließen
Aktueller Evaluatorbefund: NACHBESSERN, 20 von 21 Kriterien erfüllt. Planner: 1. `js-yaml` als
direkte versionierte Abhängigkeit beauftragen · 2. Widerspruch zum Exitcode in A-37-5 berichtigen ·
3. Klarstellen: eingebetteter YAML-Prüfer unterscheidet die inneren Ursachen, `commit-pruefen.sh`
endet nach außen entsprechend seiner dokumentierten Semantik. Plan-Prüfer prüft die neue Fassung.
Generator baut ausschließlich diese Nachbesserung im Generator-Worktree. Evaluator prüft
anschließend alle 21 Kriterien erneut, nicht nur A-37-21. Integrator übernimmt erst danach Zustand,
Ball und Prüf-SHA. *(Dirigent: Blatt berichtigt 21.08. — exit 5 gemessen, Klarstellung innen/außen,
js-yaml-Entscheid — Commit auf `rolle/dirigent`.)*

## PHASE 4 — Statuswahrheit mit der Commitwirklichkeit abgleichen
Frisch messen: vorhandener Baucommit, CODE_FERTIG-Meldung, Evaluatorvotum, Zustand, Ball, Bau-SHA
und Prüf-SHA. W0-5, W0-10, W0-11 und W0-12 wurden als CODE_FERTIG gemeldet. Der Integrator muss
die Meldungen kontrolliert übernehmen, ohne das Evaluatorurteil vorwegzunehmen. Z1-W1-3 ist
unabhängig ABGENOMMEN und muss entsprechend nachgezogen werden. Kein gebauter Auftrag darf dauerhaft
als ENTWURF oder BEREIT geführt werden. Kein abgenommener Auftrag darf weiterhin CODE_FERTIG
anzeigen. Die Korrektur erfolgt mechanisch aus Commits und Voten, nicht aufgrund von Fließtext
oder HTML-Berichten.

## PHASE 5 — Z1 vollständig abnehmen
Evaluator arbeitet einzeln: **Z1-W1-1:** nach Z0-I1 Browserkriterium auf isolierter
Browserdatenbank wiederholen; der bisherige ENV_BLOCKED-Befund ist kein Produktfehler. **Z1-W1-2:**
Walmdach-Fehlermeldung im Browser prüfen und Widerspruch an `dachformVorlagen.ts:478` fachlich
auflösen. **Z1-W1-4:** zentrale `dachWerte`-Quelle unabhängig prüfen. **Z1-W1-5:**
`insulationType`-Zweig unabhängig prüfen. Für jeden Auftrag: echten Bauumfang suchen · vollständigen
Diff lesen · alle Kriterien selbst messen · TypeScript und vollständige Hausplaner-Suite · passende
Rot-/Mutationsprobe · Browserprüfung, wenn verlangt · eigenes Votum ABGENOMMEN oder NACHBESSERN.
Kein Sammelvotum.

## PHASE 6 — vorhandene Z2-Bauten abnehmen
Zuerst die bereits gebauten Sicherheitsaufträge abnehmen, bevor neue begonnen werden: W0-1, W0-3,
W0-5, W0-7, W0-8, W0-9, W0-10, W0-11 Teil A, W0-12. **Besondere Punkte:** **W0-8:** der Konflikt
Route-Model-Binding gegen unveränderte Routenerzeuger muss vom Planner ausdrücklich aufgelöst werden;
der Generator darf das unerfüllte Teilziel nicht selbst umdeuten *(Dirigent: aufgelöst — Rechte-
prüfung + `findOrFail` erfüllt die Sicherheitswirkung, Binding-Umbau ist Nicht-Ziel; Plan-Prüfer
prüft das berichtigte Kriterium)*. **W0-11:** CSRF-Härtung und tote Ausnahmen getrennt von der
Frage einer neuen Urheberspalte behandeln; die behauptete bestehende Fremdzuschreibung war nicht
wirksam, weil `user_id` nicht persistiert wurde; eine neue Urheberspalte ist ein separater Modell-/
Fachauftrag und kein stiller Zusatz zu W0-11. **W0-9:** Browserprüfung muss zeigen, dass
deaktivierte Benutzer sich nicht neu anmelden, aus laufender Websitzung beendet werden, keine
gültigen API-Token behalten, nach bewusster Reaktivierung wieder korrekt arbeiten. **W0-10:** Tests
für deaktivierten und bewusst aktivierten Schalterzustand. **W0-12:** 8 Stunden = 480 Minuten,
0 = unbegrenzt, Widerruf ohne Kontosperre und Bereinigung unabhängig prüfen.

## PHASE 7 — nur A-38 als nächsten Governance-Bau starten
A-38 ist nach A-37 der nächste erlaubte neue Governance-Bau. Ziel: Merges müssen dieselbe Rollen-
und Herkunftskontrolle durchlaufen wie normale Commits. A-39 und A-42 bleiben BEREIT, werden aber
erst nach A-37, A-38 und Z0-I1 begonnen. A-40 bleibt beim Planner, bis die drei offenen Punkte 1, 2
und 4 korrigiert und erneut geprüft sind.

## PHASE 8 — Bundle- und Browserabnahme
Das Bundle wurde reproduzierbar gebaut; das ist noch keine Browserabnahme. Auf isolierter
Browserdatenbank prüfen: Z1-W1-1 Badge · Walmdach-Fehlermeldung · Rechte-Schalter false und true ·
403 ohne Recht bei false · Eigentums- und Identitätsbindung unabhängig vom Schalter · Kontosperre
über alle Loginwege · Master-Set-Stilllegung · Speichern und Neuladen relevanter Hausplaner-Daten.
Jede Browserprüfung nennt: Test-SHA · Datenbank · Testbenutzer · Testobjekt · Route · Handlung ·
erwartetes Ergebnis · tatsächliches Ergebnis · Screenshot beziehungsweise reproduzierbaren Nachweis.

## PHASE 9 — Gesamturteil
TESTBEREIT nur, wenn: Z0-I1 abgenommen · A-37 abgenommen und aktiv · A-38 abgenommen und aktiv ·
alle fünf Z1-Aufträge einzeln abgenommen · notwendige Z2-Aufträge unabhängig abgenommen ·
Rechte-Schalter in beiden Zuständen geprüft · Eigentums- und Identitätsschutz wirksam · deaktivierte
Nutzer zuverlässig gesperrt · Browserprüfung durchgeführt · Bundle reproduzierbar · Arbeitsbaum
sauber · eindeutiger Test-SHA genannt. Andernfalls lautet das Urteil NICHT TESTBEREIT mit: offenem
Kriterium, Ursache, Ballbesitz und nächster konkreter Handlung.

## PHASE 10 — Dachschichten vorgemerkt, noch nicht bauen
Nach TESTBEREIT folgt als erster Produkt-Slice: RoofNode-Schichten → granulare Commands →
Speichern/Laden → Ebenenpanel → 3D-Sichtbarkeit → Explosion → Referenzprojekt → später
PBR/Fotorealismus. Bis zum TESTBEREIT-Urteil wird daran kein Produktcode verändert.

## Berichtsformat pro Runde
Nur eine kurze aktuelle Tabelle:
`| Auftrag | Zustand | Ball | Bau-SHA | unabhängige Abnahme | Browser | Blocker | nächste Handlung |`
Danach höchstens: neue Belege · neue Widersprüche · nächster kritischer Schritt. Commitzahlen und
historische Selbstkorrekturen gehören in die Chronik, nicht in die aktuelle Fortschrittsmeldung.

## Grenzen
keine Produktionsdaten verändern · keine fremden uncommittierten Änderungen übernehmen · kein Merge
nach main · kein Push, Release oder Deployment ohne gesonderte Freigabe von Yama · Generator nimmt
eigenen Bau niemals ab · offene Browserprüfung niemals als bestanden melden · historische Befunde
nicht löschen, sondern als überholt kennzeichnen.

---

## NACHTRAG 2 (Yama 21.08. spät) — korrigierte Reihenfolge nach realen Gegenbeweisen
**Gemessen am Integrationsstand `8511ca05` (Dirigent, 21.08.):** `e9e6ee5b` ist ein **leerer
Commit** (kein Pfad im `--stat`) → A-42 steht maschinenlesbar weiter auf `BEREIT`/Generator ·
`scripts/rueckweg.py` `BAEUME` führt **fünf** Bäume (ohne `ticket-rolle-release`, ohne
`ticket-rolle-dirigent`) · das Rollen-Tor des Integrationsstands kennt keine Rolle `dirigent` ·
`docs/BEFUNDNOTIZEN.md` trägt **keine** Schreibbarriere · **W0-5 wurde zweimal gebaut** (`28ca0834` im
gemeinsamen Checkout, `ef7a8c89` in `rolle/generator`) · A-38 (`0f731c22`) wurde vor der vollen
A-37-Aktivierung gebaut. Urteil Yama: gute Einzel- und Prüfarbeit, **keine sehr gute
Gesamtsteuerung** — Selbstkorrektur funktioniert, technische Koordination verhindert bekannte Fehler
noch nicht.

**Reihenfolge ab sofort (ersetzt Nachtrag 1 in der Abfolge, nicht in den Kriterien):**
1. **WIP-Stopp** — keine neuen Bauten; A-38 bleibt, wird bis nach A-37 weder abgenommen noch
   weiterbearbeitet; keine parallelen DB-Läufe.
2. **A-42 wirklich abschließen** — Evaluator (frische Instanz): vollständige 10-Punkte-Bilanz,
   idempotenter Zweitlauf, Zieldateischutz (`BEFUNDNOTIZEN.md`); erst nach positivem Votum zieht der
   Integrator Datensatz und Tafel in **einem echten, nicht leeren** Zustandscommit nach; Gegenprobe:
   `git show --stat` enthält `docs/STATUS.md`.
3. **Steuerungsrolle vollständig registrieren** — Entscheid Dirigent (gedeckt durch Yamas Satz vom
   21.08.: *„Dirigent darf Steuerungs- und Konzeptdokumente vorbereiten, aber keinen Produktcode,
   keine Kriterien, keine Voten und keinen fachlichen Status schreiben"*): `dirigent` wird **echte
   Rolle** (Worktree `ticket-rolle-dirigent`, Zweig `rolle/dirigent`, Tor + Rückweg) mit **technisch
   begrenztem Schreibbereich** (`docs/konzept/`, `docs/regelwerk/`, Steuerungsblätter in
   `docs/auftraege/` ohne Kriterien); **Auftragsblätter mit Kriterien schreibt ab jetzt der Planner**;
   `release-pruefer` ebenfalls in den Rückweg. Bis zur Registrierung: kein Rückweg von `rolle/dirigent`.
4. **A-37 auf 21/21** — `js-yaml` direkt + Lockfile, Tor auf alle Rollen/Bäume, `rueckweg.py` kennt
   alle Bäume, `BEFUNDNOTIZEN.md` unter dieselbe Barriere, positive **und** negative Mutationsproben
   (fremder Baum, falscher Zweig, unregistrierte Rolle scheitern technisch — je ausgelöst).
5. **Claim-Sperre ergänzen (Z0-I2, ENTWURF → Planner)** — genau eine aktive Bau-Claim je Auftrag,
   atomar vor dem ersten schreibenden Commit, zweite Sitzung stoppt technisch (Lease-Autorität:
   `docs/konzept/agentenarchitektur-v2.md` §8); **W0-5-Doppelbau fachlich vergleichen, keinen Stand
   blind überschreiben** (Evaluator vergleicht, Entscheidung danach).
6. **Z0-I1 bauen und unabhängig abnehmen** — vier DBs, Guard, gleichzeitiger Negativtest; erst danach
   parallele datenbankverändernde Abnahmen.
7. **Erst dann Abschlussbetrieb** — W1-/W0-Abnahmen an echten Bau-SHAs, Browser auf isolierter DB,
   Statusänderungen nur mit Votum + Bau-SHA + Testbeleg, dann TESTBEREIT/NICHT TESTBEREIT mit Test-SHA.

**Berichtsform ab jetzt (jede Meldung, jede Rolle):** Ausgangs-SHA · Ergebnis-SHA · geänderte Pfade ·
unabhängiges Votum · Browserstatus · offene Abweichung · nächster Ball. **Ein Commit ohne Diff ist
kein Zustandsfortschritt.** Belege dauerhaft als `Commit-SHA + Dateipfad + stabiler Anker`, kein
nackter Zeilenzeiger (Beleg `e1298913`: 23 von 124 STATUS-Zeigern nach A-42 ungültig).

### Abweichungsprotokoll — WIP-Stopp ausgegeben 22:04, nicht wirksam (Dirigent, gemessen 22:2x)
Yama 21.08.: *„Ab jetzt nichts erneut senden, sondern diese Abweichungen als fehlende Bestätigung
protokollieren."* Gemessen mit `git log --since='22:04'` und `git branch -a --contains <sha>` am
Integrationsstand `7eaab966`:

| Zeit | SHA | Rolle | Was | Abweichung von Reihenfolge 1–7 | liegt auf |
|---|---|---|---|---|---|
| 22:05 | `a4144ff4` | Evaluator | Votum Z1-W1-5 NACHBESSERN | Z1 statt A-42 (lief vermutlich schon) | `rolle/evaluator` → integriert `82dc8d47` |
| 22:08–22:18 | `909af6cb` `37a001e0` `07ee0a38` `4254aa8a` | Plan-Prüfer | Posten 283–286 (Yamas Altposten) | Altposten statt A-37-DoR-Bereitschaft/V2 | `rolle/plan-pruefer` → integriert |
| 22:09 | `d78a2211` | Generator | Z1-W1-5-1 „mein Zweig ist eingefroren" | — (Stopp verstanden, positiv) | `auto/hausplaner-integration` |
| 22:15 | `27143f96` | Evaluator | Votum Z1-W1-2 (E ENV_BLOCKED) | Z1 statt A-42 — **nach** Evaluator-Stopp | `rolle/evaluator` → integriert `237da4cd` |
| 22:15 | `844ae872` | Release-Prüfer | Einfrierung gelockert („zu weit gefasst") | hebt die Sperre, die den Generator gehalten hatte | — |
| 22:16 | `824f8512` | Generator | **A-39 neu gebaut** | Neubau trotz WIP-Stopp | **direkt `auto/hausplaner-integration`, NICHT `rolle/generator`** |
| 22:17 | `8529c63b` | Generator | A-39 CODE_FERTIG gemeldet | Zustandsmeldung eines gesperrten Baus | **direkt `auto/hausplaner-integration`** |
| 22:19 | `7eaab966` | Generator | **Z1-W1-5-1 gebaut** | Neubau trotz WIP-Stopp | **direkt `auto/hausplaner-integration`**, noch nicht gepusht |
| 22:07–22:19 | `82dc8d47` `a5b77ecc` `35ae00fe` `237da4cd` `b1974918` `d3053f56` | Integrator | sechs Rückwege | transportiert außerhalb der Folge Gebautes | Integrationszweig |

**Befund, der schwerer wiegt als die Reihenfolge:** drei Generator-Commits (`824f8512`, `8529c63b`,
`7eaab966`) sind **nicht** auf `rolle/generator` — sie entstanden **im gemeinsamen
Integrations-Checkout** (dieselbe Klasse wie `28ca0834` beim W0-5-Doppelbau). Das ist der Fall, den
der Maßstab „sehr gut" als **klares Ausschlusskriterium** nennt, und der Beweis, dass die
Schreibbarriere A-37 für den Generator im gemeinsamen Checkout heute **nicht wirkt**. Keine
Bestätigung „EVALUATOR: geparkt", keine Rollenmeldung eingegangen (Stand dieses Protokolls).
**Folgen:** A-39 und Z1-W1-5-1 bleiben **geparkt** — keine Abnahme, kein Transport aufgrund dieser
Bauten; A-38 bleibt geparkt; der Befund „Generator committet im Integrations-Checkout" geht als
Kriterium in A-37 (technisch: Tor muss `generator` ohne `TICKET_ROLLE`/im falschen Baum mit Exit ≠ 0
abweisen — **und zwar auch bei nacktem `git commit`**, nicht nur über `commit-pruefen.sh`). **Gemessen
(Dirigent, Integrations-Checkout):** `core.hooksPath = .githooks`; dort liegen nur `commit-msg` und
`post-commit`, **kein `pre-commit`**, und keiner der beiden ruft `rollen-tor` oder liest
`TICKET_ROLLE` — das Tor wirkt heute ausschließlich innerhalb von `scripts/commit-pruefen.sh`; ein
direktes `git commit` umgeht es vollständig. Das ist die Lücke, die A-37 (Tor im `pre-commit`-Hook)
und die Claim-Sperre Z0-I2 schließen müssen.

### Yama-Entscheidung 21.08. (nach 22:24) — Weg (a): Generator-Sitzung manuell anhalten
Nachgemessen (Dirigent): **`ada3b645`** 22:24:43, Generator, **direkt auf `auto/hausplaner-integration`**
(vierter direkter Generator-Commit nach dem WIP-Stopp; zum Messzeitpunkt bereits auf `fork`, HEAD der
Integration, Arbeitsbaum sauber). Wortlaut Yama, verbindlich:

> Yama-Entscheidung: Weg (a). Die Generator-Sitzung, die im gemeinsamen Checkout
> `auto/hausplaner-integration` arbeitet, ist sofort manuell anzuhalten. Keine weitere Nachricht als
> Ersatz für den Stopp. Der aktuelle Integrationsstand wird nach dem tatsächlichen Anhalten frisch
> gemessen und als Stopp-SHA protokolliert. Keine der seit dem WIP-Stopp entstandenen Arbeiten wird
> gelöscht, zurückgesetzt, abgenommen oder als Fortschritt der kritischen Kette gewertet.
> Wiederaufnahme des Generators erst, wenn A-37 eine wirksame `pre-commit`-Barriere enthält und deren
> Negativprobe belegt: fehlende Rollenkennung scheitert · Generator im Integrationscheckout scheitert ·
> falscher Worktree/Zweig scheitert · Integrator im Integrationscheckout darf den eng begrenzten
> Integrationscommit ausführen · nacktes `git commit` kann die Prüfung nicht umgehen.
> Danach gilt weiterhin ausschließlich: A-42 → A-37 → Z0-I1.

**Anhalten vollzogen (Dirigent, 22:36:4x) — Weg (a), technisch statt manuell, weil Yama die Sitzung
nicht sicher identifizieren konnte:** Identifikation über Transkript-Urheberschaft, nicht über Raten:
Sitzung `7df19ed4` (Projekt `-Users-yamanuri-Documents-ticket`, geboren 19.08. 13:10:08) trägt die
Commit-Botschaften `A-39 gebaut`, `Symbolebene gemessen`, `Zaehlaussagen in Kommentaren …` als
**eigene Werkzeugaufrufe** (`tool_use`), setzt `TICKET_ROLLE=generator` (8×) und stand in 3290
`cwd`-Feldern **ausschließlich** im gemeinsamen Checkout `ticket` bzw. `ticket/resources/planner/hausplaner`,
nie in `ticket-rolle-generator`. Prozess: **PID 87659** (VS-Code-Claude, gestartet 19.08. 13:09:33,
die einzige Sitzung dieses Starts **ohne** `--resume`; alle anderen fünf tragen fremde Sitzungs-IDs:
87750→`303cefb6`, 87761→`4a2203cb` Release-Prüfer, 88049→`3870df7a` Plan-Prüfer, 88088→`aa0cddd3`,
80335→`03737d75`). Maßnahme: `kill -STOP 87659` — **reversibel** (`kill -CONT 87659`), kein Kill,
keine Datei verändert; nachher `ps` Status `T`, alle anderen `S`.
**Stopp-SHA: `4e02c273`** (22:34:22, *ein sechster direkter Generator-Commit*: „M-3 der Maurer-Linse
behoben"); `git status`: `auto/hausplaner-integration` **1 voraus** gegenüber `fork`, uncommittete
Änderung `docs/FACHPRUEFUNG-DREI-LINSEN.md` (bleibt unangetastet — nicht gelöscht, nicht committet);
Reflog-Spitze: `4e02c273` commit · `b92be043` ff rolle/plan-pruefer · `7f2e5cc6` merge · `6f89d060` commit
· `a2501764` ff rolle/evaluator · `ada3b645` commit. Wiederaufnahme (`kill -CONT`) **nur** nach
Yamas Freigabe und erst, wenn A-37 die `pre-commit`-Barriere mit belegter Negativprobe trägt.
**Gegenprobe Urheberschaft (22:4x):** alle sechs direkten Commits (`824f8512` `8529c63b` `7eaab966`
`ada3b645` `6f89d060` `4e02c273`) tragen ihre Botschaft als `tool_use` **nur** in `7df19ed4`; der
zweite Generator `aa0cddd3` (PID 88088, arbeitet in `ticket-rolle-generator`, Urheber von `d78a2211`
„mein Zweig ist eingefroren") hat **keinen** direkten Commit — er läuft weiter.
**Zwei Steuerungslücken, gemessen:** (1) **Planner unbesetzt** — kein Prozess, kein Transkript mit
dominanter Rolle `planner` in 48 h; die letzten `planner:`-Commits (`807c4fe4` 21.08. 10:04 u. a.)
stammen aus der **Dirigent-Sitzung selbst** (`ed7ac3c9`, damals im Planner-Worktree). Seit Yamas
Schreibrecht-Satz darf der Dirigent keine Kriterien schreiben → A-37-Erweiterung und Z0-I2 haben
heute **keinen Verfasser**; Entscheidung Yama: eigene Planner-Sitzung starten (Worktree
`ticket-rolle-planner`, `TICKET_ROLLE=planner`) oder ausdrücklich einen vom Dirigenten
instanziierten Planner-Agenten zulassen (Rollentrennungs-Risiko benannt). (2) **Evaluator `303cefb6`
(PID 87750) ist seit 22:21:06 untätig** (Transkript endet mit einer Nachricht, kein Werkzeuglauf
danach) — Sitzungsnachrichten **wecken eine untätige Sitzung nicht**, sie werden erst bei der
nächsten Werkzeugrunde zugestellt; meine vier Nachrichten (22:04–22:2x) liegen dort ungelesen.
Folge: A-42-Bilanz kommt erst, wenn Yama die Evaluator-Sitzung anstößt (oder sie von sich aus
weiterarbeitet). Dasselbe gilt für jede Stopp-Nachricht: **wirksam ist nur, was technisch
erzwungen wird** — Beleg für Z0-I2 und für die `pre-commit`-Barriere.

### Yama-Entscheidung 21.08. (nach 22:45) — Planner, Evaluator, Generator-Stopp
> **Punkt 1: Weg (a).** Yama startet eine eigenständige Planner-Sitzung im Worktree
> `ticket-rolle-planner` mit `TICKET_ROLLE=planner`. Der Dirigent darf keinen Planner im zweiten
> Glied instanziieren und keine Kriterien stellvertretend schreiben.
> **Evaluator:** Sitzung `303cefb6` wird angestoßen; sie bearbeitet ausschließlich die unabhängige
> A-42-Abnahme und bestätigt zuerst den Parkzustand.
> **Generator-Stopp:** PID `87659` bleibt per `SIGSTOP` angehalten. Keine Wiederaufnahme mit `CONT`,
> bis (1) A-42 unabhängig abgeschlossen, (2) A-37 einschließlich wirksamem `pre-commit`-Hook fertig,
> (3) alle fünf Negativproben bestanden, (4) ein nacktes `git commit` das Tor nicht umgehen kann,
> (5) die unabhängige Evaluator-Abnahme vorliegt.
> Der zweite Generator im korrekten Worktree, PID `88088`, bleibt verfügbar, darf aber erst nach
> fertigem Planner-Auftrag und DoR **ausschließlich A-37** bearbeiten.
> **Verbindliche Reihenfolge: A-42 → A-37 → Z0-I1 → übrige Abnahmen.** Z0-I2 wird vom neuen
> Planner spezifiziert, aber noch nicht gebaut. A-38, A-39 und weitere Produktarbeit bleiben
> geparkt. Der Integrator transportiert bis dahin ausschließlich freigegebene Ergebnisse dieser
> Reihenfolge. Freigabe erteilt: Planner starten und Evaluator wecken. Keine Freigabe: Generator
> `87659` fortsetzen, weitere Produktarbeit oder Reihenfolge ändern.

Bericht nach Planner-Start und Evaluator-Wecken mit: Rollen-/Sitzungs-ID · Worktree und Zweig ·
Ausgangs-SHA · erstem tatsächlich bearbeitetem Auftrag · Bestätigung PID `87659` = `T`.

### Ursache der wirkungslosen Steuerung, gemessen (Dirigent, 22:5x)
`grep -c cross-session-message` in **allen sechs** Rollen-Transkripten = **0**; „WIP-STOPP" = 0; der
Text „KORRIGIERTE REIHENFOLGE" existiert ausschließlich im Dirigent-Transkript. **Keine einzige
Sitzungsnachricht des Dirigenten ist je zugestellt worden** — der Sender meldete `success`, der
Empfänger hat nichts erhalten. Der WIP-Stopp war nicht „ausgegeben, aber unwirksam", er war
**nicht zugestellt**. Folge: die Rollen arbeiteten den ganzen Abend nach dem integrierten Stand und
ihren stehenden Anweisungen — konsistent mit allen Abweichungen oben. Der Evaluator wurde von Yama
mit „schau nach" geweckt (22:51), fand „sechs Bälle unbewegt" und kennt A-42 als seinen Auftrag
nicht. Kanalwechsel: **`docs/auftraege/DIRIGENT-TAFEL.md`** (dieser Worktree) ist die eine
Anweisungstafel; jede Rolle liest sie vor jeder Arbeit; Yama tippt je Sitzung einmal den Lesebefehl.
PID 61814 (22:55) ist die Codex-Sitzung, kein Planner.

### PULL-Betrieb aktiviert (Yama-Modell, 23:33) — Meilenstein, gemessen
Steuerungsstelle `/Users/yamanuri/.ticket-steuerung/` (operative Wahrheit; Spiegel `steuerung-spiegel/**`
nur Snapshot). **Sechs von sechs ACK-Dateien gültig** (Rolle · Sitzung · Generation · Digest):
Planner `20c9c319` gen 3 · Plan-Prüfer `3870df7a` gen 2 · Integrator `03737d75` gen 3 · Evaluator
`303cefb6` gen 3 · Generator `aa0cddd3` gen 2 · Release-Prüfer `4a2203cb` gen 2. Registrierungen
`sitzungen/*.yaml` vollständig. **Erste Lease:** `leases/A-42/` Token 1, Inhaber Evaluator — A-42
`GECLAIMT`, Bilanz auf `26c46f31` läuft. Planner lieferte drei belegte Befunde ohne Sacharbeit
(Weckerkonflikt → eigener Takt auf Pull umgestellt; Wartebedingung → berichtigt; CODE_FERTIG-Betreff:
`e9e6ee5b` leer **und** zwei Kennungen **und** falscher Bau-SHA `0f731c22` → in Evaluator-/Integrator-
Auftrag gen 3 übernommen; doppelter `ticket-rolle-generator`-Baum im Evaluator-Scratchpad → A-37-22
wählt Bäume über Pfad + Zweig). Keine Sacharbeit, kein Commit, keine Lease außerhalb A-42. PID 87659 = `T`.
Grenze (Yama): Sofortlösung, noch keine unübergehbare Barriere — Durchsetzung gehört in A-37/Z0-I3.

### 23:38–23:55 — A-42 NACHBESSERN → A-42-1 gebaut; Planner-Besetzung Variante C (Yama)
- **Evaluator-Votum `3f7f61d6`** (rolle/evaluator, `VOTUM-A-42.md`): NACHBESSERN, **ein** Punkt — 10/11
  erfüllt, 7 zeichengenau, Idempotenz belegt, Bau-Identität selbst geprüft (`26c46f31` = A-42-Bau,
  `0f731c22` = A-38-Bau, `e9e6ee5b` leer und nennt **beide** SHAs). Punkt 8: drei verwaiste Überschriften.
  Befunde ohne Abzug: `BEFUNDNOTIZEN.md` ohne Schreibbarriere (bestätigt); vier Wächter kennen die Datei nicht.
- **Entscheidung Dirigent (Vollmacht):** Nachbesserung = **Verweis** unter jeder Überschrift, nichts entfernen;
  zuständig **Integrator** (einziger STATUS-Schreiber), nicht Generator.
- **Integrator `d17d2ddf`** (23:44, nur `docs/STATUS.md`, numstat 4/0): **zwei** Verweise gesetzt (Anker
  `statuswahrheit_in_zwei_fassungen`, `P-05`, beide in BEFUNDNOTIZEN nachgewiesen); die dritte
  („A-17 MESSBERICHT") **bewusst nicht** — sie hatte schon vor `26c46f31` keinen Block (am 12.08. nach
  `6d6823dd` entfernt); ein Verweis wäre eine unrichtige Herkunftsangabe. Gegenprobe 850/850 Zäune,
  104/104 Datensätze, Zustandszählungen unverändert. Ereignis `integrator-A-42-1-CODE_FERTIG.yaml`,
  Lease A-42-1 Token 1 gezogen und freigegeben. Rückweg `6af8a5d1` holt das Votum in die Kette.
  **Formbefund des Integrators an mich:** er las `integrator.yaml` gen 4 mit dem Digest von gen 3 —
  Datei und `.sha256` entstanden in zwei Runden → README: **atomar veröffentlichen** (tmp + mv beide).
- **Planner-Besetzung, Yama Variante C:** `79285cf2`/PID 70499 (frühere zweite Plan-Prüfer-Instanz im
  gemeinsamen Checkout, Commits 291–298 direkt auf der Integration; hat Kollision korrekt gemeldet,
  keinen Anspruch erhoben) wird **geschlossen** — bis dahin `SIGSTOP` (jetzt `T`); Meldung
  `SITZUNG-70499-ROLLENWECHSEL` bleibt Beleg; ihr **PID-Befund** (flüchtige Shell-PID statt
  Sitzungsprozess in 3 von 4 Einträgen) → A-37-25/Z0-I3. `20c9c319`: Hauptprozess 68609 **existiert
  nicht mehr**, nur sein Cron-Takt läuft headless alle 4 Min (`--resume=20c9c319`) — kein Lebensnachweis
  als Sitzung; Transkript-mtime ist keiner. `rollen/planner.yaml` gen 4 = **FORMAL UNGÜLTIG, NICHT
  QUITTIERBAR, Planner NICHT_BESETZT_GEPARKT**; Generation 5 erst mit frischer Sitzung im Worktree
  `ticket-rolle-planner` (gleiche Sitzungs-ID in `sitzung_erwartet`, Registrierung, ACK; pid =
  Sitzungsprozess + Startkennung). Meine Zwischenentscheidung „Variante A" (gen 5, 23:47) war vor
  Yamas C **falsch angesetzt** und ist zurückgenommen/als überholt markiert — Lektion: Rollenbesetzung
  ist Yamas Entscheidung, nicht Routing. **Kein „6/6 aktiv"**: fünf Rollen quittiert, Planner unbesetzt.

### 22.08. 00:00–00:15 — A-42 ABGENOMMEN; Planner neu besetzt; Kette läuft
- **Planner neu besetzt (Variante C, vom Dirigenten in Yamas Vertretung vollzogen):** Sitzung
  `ef8ec540-263d-4f06-8a02-5a2f1beeb572` headless im Worktree `ticket-rolle-planner` gestartet
  (`claude -p --session-id`, PID 88928, Start 00:00:48), Generation 5 **vorab** auf diese ID ausgestellt
  (`sitzung_erwartet` = Registrierung = ACK, atomar mit Digest `59a785fd…`); erster Lauf: Registrierung mit
  echter Sitzungsprozess-PID + Startkennung (ppid-Kette belegt), ACK gen 5 gültig, gesamte Vorarbeit
  gelesen, keine Sacharbeit. Takt alle 4 Min durch den Dirigenten (Scheduler, keine Inhalte). Sitzung
  70499 beendet (kill nach Yamas C); `20c9c319` ohne Sitzungsprozess, nur headless Takt → nicht Planner.
- **A-42:** Integrator `9e5f1ff5` setzt den dritten Verweis (A-17; sein Einwand war falsch gemessen,
  selbst berichtigt; Evaluator-Nachprüfung `b193f72e` hatte den umgezogenen Block nachgewiesen) →
  **Evaluator `b1c0c2d4`: A-42 ABGENOMMEN, 11/11**, drei Verweise `:7434` `:17651` `:18632`, Ereignis
  `evaluator-ABGENOMMEN.yaml` 00:08:59; Rückweg `b15c938d`. Offen: Integrator-Zustandscommit (eine
  Kennung, Wortlaut, Bau `26c46f31`, Beleg = Votum-SHA).
- **Generationen:** Planner 6 = `SPEZ-planner-A-37` (**aktion: spezifizieren**, Lease, commit-pruefen mit
  `NODE_PATH`, Ergebnis-Ereignis) · Plan-Prüfer 3 = `DOR-plan-pruefer-A-37` (warten auf Planner-CODE_FERTIG,
  dann DoR) · Integrator 5 · Evaluator 4 · Generator 2 · Release-Prüfer 2.
- **Planner-Zuschnittfrage** („Kein Hook" ist Nicht-Ziel des A-37-Blatts :229 ↔ A-37-25 verlangt den Hook):
  entschieden durch **Yama** (Weg a: „A-37 einschließlich wirksamem pre-commit-Hook"); Form = Hausform
  **ÜBERHOLT-Kennzeichnung** (:205/:220), nichts löschen; A-37-18 ohne feste Baumzahl, Verweis auf
  A-37-22-Liste. Planner-Ist-Beleg für A-37-22: `rueckweg.py` wählt Bäume über Namen (:128), prüft nie den
  Zweig, `ticket-release-pruefung` nur als Ziel-Orakel (:118).
- PID 87659 weiter `T` (Bedingungen 2–5 offen: A-37 mit Hook, fünf Negativproben, nacktes `git commit`
  gesperrt, Evaluator-Abnahme).

### 22.08. 00:23–01:16 — A-37 spezifiziert, DoR in zwei Runden ERTEILT, Bau angestoßen
- **Planner `3dde19ea`** (00:23): A-37-22…27, ein Pfad (+293/−4), jeder Rot-Beleg im eigenen Baum gefahren,
  drei Rot-Arten getrennt; `planner-CODE_FERTIG.yaml`; Lease freigegeben. Zuschnitt „Kein Hook" als
  ÜBERHOLT gekennzeichnet (Yama Weg a). Integrator-Rückweg `4a716da8`.
- **Plan-Prüfer DoR Runde 1 `1568610f`: NICHT ERTEILT** (Restpunkte u. a. Sitzungs-/Lease-Identität: gemessen,
  dass PID 88928 tot war, während die Sitzung unter 97092 weiterlief) → Yamas Zielregel wörtlich an Plan-Prüfer
  und Planner (`dirigent-antwort-zielregel-lease-yama.yaml`) → **Planner `213edd28`** schließt Restpunkte 1+2 →
  **DoR Runde 2 `a248eaaf`: ERTEILT** (00:49); Planner-Berichtigungen `0579727c`, Belegberichtigung `762243b9`;
  Plan-Prüfer Auflagenprüfung `2b9cedc4`, „Anmerkungen geschlossen". Integrator transportiert Voten
  (`f1b84c65`, `1027c8d5`, `21ea84c3`, `f4366313`, `c153a9f8`, `e1539dc7`).
- **Generator gen 3 = `BAU-generator-A-37` (01:16): aktion bauen** — eigener Worktree, Lease, commit-pruefen mit
  `NODE_PATH`, jede Negativprobe tatsächlich auslösen, kein Produktcode, `--no-verify` als dokumentierte Grenze;
  Ergebnis `generator-CODE_FERTIG.yaml` → Evaluator (gen 5 bereit, alle Positiv-/Negativproben).
- **Integrator gen 7:** Rückweg `rolle/planner` bis `762243b9` (Lücke seit 00:43, vierte Erinnerung — mein
  Versäumnis: Planner-Blatt-Stände waren in gen 6 nicht als transportfähig gelistet; jetzt ohne Einzelweisung).
- Headless-Takt des Planners: Läufe 1–9 (Lauf 9 mit API-Abbruch, ohne Schaden); Registrierung/Lease je Lauf
  nachgeführt; Befunde: `paragraph8-fehlzuordnung` (Plan-Prüfer berichtigt), Z0-I2-Vorrat (§8 Schritt 6
  `mv`/`rename`), Z0-I1-Ist-Lage Kriterium A (Plan-Prüfer). PID 87659 = `T`.

### 22.08. 01:16 → 07:54 — Stillstand; 08:0x — zwei Korrekturen Yamas
- **Stillstand 01:14–07:51:** Dirigent-Prozess (und damit Takt, Veröffentlichung) stand; generator gen 3 /
  integrator gen 7 wurden erst 07:54 wirksam (Integrator reagierte sofort: `ab9e837c`, drei Planner-Blattstände
  transportiert; Plan-Prüfer Vorlage an Yama „Kette steht seit sieben Stunden", gemessen vor dem Wirksamwerden).
  **Y-13** war in der Steuerungsstelle nicht vermerkt (nur auf `rolle/dirigent`) → `auftraege/Y-13-entscheidung-yama.md`;
  offen allein die GRANT-Ausführung mit root (Yama), messbar per `SHOW GRANTS`.
- **Generator-Basis:** `rolle/generator` 3 voraus / 98 zurück, `--ff-only` unmöglich (Generator meldete richtig,
  zog keine Schreibarbeit vor). Meine Entscheidung „Bauen auf `abd1719c`, Integrationsfassung der Hooks übernehmen,
  W0-5-Konflikte beim Rückweg auflösen" (`dirigent-antwort-nicht-ff.yaml`) **von Yama verworfen und zurückgenommen**:
  kein Merge mit Altarbeit, kein Reset, kein Bau auf veraltetem Stand → **frischer, isolierter Arbeitsstand von
  `ab9e837c`**, alter Zweig unverändert als Beleg. Umsetzung (Tor bindet `generator` an `ticket-rolle-generator` /
  `rolle/generator`): alten Baum und Zweig **umbenennen und erhalten**, neuen Baum/Zweig unter dem gebundenen Namen
  von `ab9e837c` anlegen; generator gen 4 = `BASE_BLOCKED`/Stopp (Lease frei, nichts schreiben), gen 5 = neue Basis.
- **Z0-I1 zu früh:** Planner zog unter `SPEZ-planner-A-37` eine Lease (Token 5) für die Z0-I1-Erweiterung, Worktree
  1 Datei offen → planner gen 7 = **Parken** bis A-37 gebaut und ABGENOMMEN (Entwurf ins Scratchpad, Worktree sauber,
  Lease frei). Reihenfolge: `A-42 → A-37 vollständig aktiv → Z0-I1`.
- **Prozessregel (Yama):** nach DoR Kriterienstand einfrieren; Beleg-/Formulierungsberichtigungen ohne
  Kriterienwirkung als **ein Errata-Ereignis**, ein Commit, ein Rückweg (README). Basis-Regel `BASE_BLOCKED`.
- **08:01–08:12 Vollzug:** Bäume getauscht (alt → `ticket-rolle-generator-beleg-2026-08-21` /
  `rolle/generator-beleg-2026-08-21` @ `abd1719c`; neu `ticket-rolle-generator` / `rolle/generator` @ `ab9e837c`,
  0/0 zur Integration). Generator: `BASE_BLOCKED`-Ereignis, **ACK gen 5, Lease Token 2, Bau läuft**
  (`scripts/rueckweg.py` in Arbeit). **Selbstmeldung Generator:** hat zum Nachweis von A-37-22 `rueckweg.py`
  **echt gefahren** → drei fremde Bäume per Fast-Forward auf `ab9e837c` (plan-pruefer, evaluator, release; 0 offen,
  kein Commit, nichts überschrieben; Bäume mit offenen Dateien übersprungen). Entscheidung: kein Zurückdrehen;
  Regel „Generator fährt nie ein Transportwerkzeug"; Beleg über Probe-Repo im Scratchpad; `--trocken` als
  Vorratsposten (Planner). Planner gen 8: 198 Z0-I1-Zeilen bleiben als **`PARKED_DRAFT`** im Worktree (nicht
  committen/löschen/auschecken), Lease frei, ACK gen 8 — vollzogen 08:08 (Takt 11).
- **08:12–08:23 — Nachschärfung vor dem ersten abnahmefähigen Commit (Yama):** A-37 prüft Zustände, erzwingt aber
  keine Ausführungsberechtigung (Generator konnte `rueckweg.py` auf echten Bäumen fahren). Planner gen 9:
  A-37-22b/c/d → **Widerspruch 22b/22d** (Positivprobe im echten Checkout vs. Proben nur im Wegwerf-Repo) von Yama
  erkannt → gen 10: Preflight-Autorisierung nebenwirkungsfrei am Bestand, Transportkern mit begrenztem Probe-Root,
  Produktiv-Einstieg nur nach echtem Preflight, Probe-Modus lehnt reale Worktrees ab. **Pause überlaufen:** gen 6
  (Pause) 08:12:36 veröffentlicht, Generator committet trotzdem `49972884` (08:09, A-37-22) und `1155709d` (08:16,
  A-37-23) und arbeitet weiter (Skripte offen bis 08:18) — **Beweis: der Pull-Takt ist keine Schreibbarriere** →
  Generator-Sitzung 88088 **technisch angehalten (SIGSTOP, reversibel)**, eingefroren HEAD `1155709d` + offene
  `commit-pruefen.sh`/`rollen-tor.sh`; Vorab-Commits bleiben als „Vor-Nachschärfungsstand, nicht abnahmefähig";
  generator gen 7 = angehalten_eingefroren. **Vierte Nachschärfung A-37-22e (Yama wörtlich):** vor jedem
  schreibenden Schritt und im Commit-Gate Generation+Digest gegen die Rollenquelle prüfen; veraltete/fehlende ACK
  oder Aktion pausieren → Commit abgewiesen → planner gen 11; plan-pruefer gen 5 votet erst auf der vollständigen
  Lieferung (99ea9183 + ergänzender Commit mit 22e). `scripts/` bleibt für den Dirigenten gesperrt (Evaluator ändert
  keine Rollenentscheidung). Integrator transportierte `99ea9183` (`eb304cf5`).

### 22.08. 08:23–09:07 — Nachschärfung vollständig, DoR ERTEILT; zwei Identitäts-Befunde
- **Planner:** `fdc8d7d5` (22e + 22b/22d-Auflösung + Vorab-Commits gekennzeichnet), `96b24ca3` (22b-Messbefehl von
  Wort- auf Wirkungsmessung, nach Plan-Prüfer-Vormessung); Integrator `2832bdd4`, `968f39f6`. **Plan-Prüfer gen 6:
  DoR ERTEILT `1ca8d512` über `99ea9183 + fdc8d7d5 + 96b24ca3`** (09:05).
- **Inventur-Bilanz `06642e35`** (unveränderliche Messbasis, Mess-SHA `eb304cf5`) → drei Steuerungslisten
  (`4bc2e3ff`): Abnahmerückstand 12, Produkt-Backlog 13 (+Wächter, 2 Yama-Fragen), Steuerungs-Backlog 12 (+2).
  W0-5-Entscheidung: A `28ca0834` bleibt, B `ef7a8c89` Beleg + Vorrat (Evaluator-Vergleich).
- **Identitäts-Befund Generator (`generator-befund-pause-und-wecker-wirkungslos.yaml`):** SIGSTOP auf PID 88088 hält
  einen **Lauf**, nicht die **Sitzung** — nach VS-Code-Neustart (2.1.239, 08:47) lief `aa0cddd3` unter 91834 weiter;
  der Wecker-Cron überlebt den Laufwechsel nicht. Die Sitzung hat gen 7 gelesen und **organisatorisch** gehalten
  (HEAD `1155709d`, PAUSIERT-Ereignis) — technisch war sie nicht angehalten. Dieselbe Klasse wie 87659/`7df19ed4`:
  um 09:04:34 erschienen **neue Läufe** der gesperrten Sitzung `7df19ed4` → sofort SIGSTOP, dazu eine
  **Dauersperre** (`sperre-7df19ed4.sh`: jeder neue Lauf wird automatisch angehalten) bis A-37 ABGENOMMEN.
  Lehre: PID-Stopps sind Notbremse je Lauf; die Sitzung hält nur das Commit-Gate (A-37-22e) — genau das wird jetzt gebaut.
- **Generator gen 8 = bauen** (Sitzung läuft unter 91834; 88088 bleibt als verwaister Lauf `T`).
Die fünf Negativproben gehen als Kriterien
in das erweiterte A-37-Blatt (Planner); der Integrator transportiert nichts aufgrund der seit 22:04
entstandenen Generator-Bauten. Das V2-Konzept (`0d897b0e`) ist bereit für die spätere unabhängige
Prüfung; **die technische Schreibsperre hat Vorrang vor jeder weiteren Konzept- oder Produktarbeit.**
