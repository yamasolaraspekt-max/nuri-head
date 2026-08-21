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
