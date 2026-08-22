# GESAMTAUFTRAG v2 — Fortschrittswahrheit herstellen, Rollenkollisionen technisch schließen, eindeutig testbereiten Stand erzeugen

```yaml
erteilt_von: "Yama, 21.08.2026 abends (Wortlaut 1:1 unten); ersetzt und erweitert GESAMTAUFTRAG-TESTSTAND-2026-08-21.md (v1 bleibt als Phasen-1-4-Vorlage gueltig, wo v2 nichts anderes sagt)"
weitergegeben_durch: "Dirigent (Vollmacht docs/regelwerk/VOLLMACHT-DIRIGENT.md)"
gilt_fuer: alle Rollen
phase_0_snapshot: "2026-08-21 20:58:40 - HEAD 976f7d6b (generator Z2-W0-12 gebaut) - fork 0/0 - gemeinsamer Checkout nur drei Planner-Blattaenderungen (W0-4/6/11 Restpunkte) uncommittet, KEINE fremde Produktivarbeit - 16 Worktrees (inkl. 3 Scratchpad-Worktrees fremder Sitzung 303cefb6, ticket-rolle-* auf 80b3cbf9/af6f1403) - keine laufenden Testprozesse - Testdatenbanken ohne Zugang nicht messbar (Phase 3)"
nachtrag_yama_21_08_spaet: "WIP-GRENZE und REIHENFOLGE — siehe Abschnitt 'Nachtrag Yama (spaet)' unten; gilt vor allem Uebrigen"
```

## Nachtrag Yama (21.08., spät) — WIP-Grenze und bessere Reihenfolge, vom Dirigenten vollständig übernommen

**Urteil Yama:** Konzept richtig, aber zu viel angefangen, zu wenig vollständig geschlossen. Hebel:
weniger parallele Bauten, mehr Abnahme, technische Isolation. **Umstellung von „viel gleichzeitig
bauen" auf „Blocker schließen, abnehmen, dann Neues beginnen".**

**WIP-Grenze (gleichzeitig höchstens):** ein Sicherheitsbau · eine unabhängige Abnahme · ein
Planner-Zuschnitt ohne Code. **Acht Verbesserungen:** (1) gemeinsamen Checkout sofort wirklich
sperren — dort lagen erneut uncommittierte W0-11-Produktänderungen; (2) Z0-I1 vor allen parallelen
DB-Prüfungen abschließen (Kollision zweimal eingetreten — höchster technischer Blocker); (3) WIP
begrenzen; (4) A-37 sofort schließen (20/21, kleine js-yaml-Nachbesserung; erst das funktionierende
Rollen-Tor macht Parallelarbeit belastbar); (5) Statuswechsel atomar — Baucommit, Zustand, Ball,
Bau-SHA in einem kontrollierten Übergang; (6) Abnahme vor weiteren Bauten — Z1-W1-3 ist abgenommen,
die übrigen vier schließen; (7) Hausplaner und allgemeine Sicherheit getrennt berichten; (8) Berichte
stark verkürzen — eine Tabelle `Auftrag | gebaut | unabhängig geprüft | Browser | Blocker | nächster
Besitzer`, historische Fehler bleiben in der Chronik.

**Reihenfolge (Yama):** 1 W0-11-Arbeit aus dem gemeinsamen Checkout sichern → 2 Y-13 technisch
ausführen und Z0-I1 abnehmen → 3 A-37 nachbessern und vollständig abnehmen → 4 Z1-W1-1/2/4/5
abschließen → 5 bereits gebaute Z2-Aufträge unabhängig abnehmen → 6 erst danach W0-2/4/5/6/11
weiterbauen → 7 vollständige Browserprüfung → 8 Test-SHA und TESTBEREIT-Urteil → 9 Dachschichten
und fotorealistische Darstellung.

**Anweisung Dirigent daraus:** Generator baut NICHTS Neues, bis Z0-I1 abgenommen ist (Ausnahme: die
Sicherung der W0-11-Arbeit in den eigenen Worktree, pfadgenau, ohne Sammelcommit); der EINE
Sicherheitsbau ist Z0-I1; die EINE Abnahme läuft beim Evaluator seriell (Z1-W1-1/2/4/5, dann
gebaute Z2); der EINE Planner-Zuschnitt ist A-37 (Blattberichtigung ohne Code). Integrator zieht
Zustände atomar nach und trennt im Bericht Hausplaner von Sicherheit.

## Entscheidungen des Planners/Dirigenten zu den im Auftrag genannten Punkten (21.08., Vollmacht)

- **A-37-21:** `js-yaml` wird **direkte, versionierte Abhängigkeit** (nicht transitiv über Puppeteer/Cosmiconfig), Lockfile aktualisiert. **A-37-5:** Überschrift und Tabelle nennen denselben Exitcode. **Klarstellung:** die inneren YAML-Prüfer unterscheiden 2/3/4, `commit-pruefen.sh` endet nach außen mit 1 — beides steht im Blatt. Kette: Planner (Blatt berichtigen) → Plan-Prüfer → Generator im eigenen Worktree → Evaluator prüft **alle** A-37-Kriterien erneut.
- **W0-8 (secure.image):** Kriterium wird **fachlich berichtigt** — `permission:Customer,read` als vorgezogene Rechteprüfung **plus** `findOrFail` (404 bei unbekannter ID) erfüllt die Sicherheitswirkung; Route-Model-Binding mit Umstellung der Routennamen und aller sieben Erzeuger ist **Nicht-Ziel** (Scope-Explosion ohne Sicherheitsgewinn). Keine Mischform; der Generator deutet nichts um — der Plan-Prüfer prüft das berichtigte Kriterium.
- **W0-11:** Teil A (uid-Bindung + fünf tote Ausnahmen) sofort, Teil B (IDS-Rückgabeformat, extern) als W0-11b nach **Y-12** — bereits im Blatt getrennt.
- **Y-10/Y-11:** wie erteilt (8 h; reversible Stilllegung) — bereits eingearbeitet (W0-12, W0-10).
- **Rollenbarriere (Phase 2):** Stage-Scope-Abbruch ist angewiesen; A-38 (Merges durch das Rollen-Tor) wird vom Plan-Prüfer priorisiert. **Der Dirigent selbst verlässt den gemeinsamen Checkout** für weitere Planner-Schreibarbeit (eigener Worktree/Rollenbranch) — Ausnahme: Commits bereits offener eigener Dateien mit explizitem Pfad.
- **Testdatenbanken (Phase 3):** Auftrag **Z0-I1** geschnitten (je Rolle eigene DB + Namensprüfung vor dem ersten Schreibzugriff + Positiv-/Kollisionsprobe). **Y-13 ENTSCHIEDEN (Yama, 21.08. abends):** `ticket_user` erhält vollständige Rechte (CREATE, DROP, ALTER, Migrationen, Datenänderungen) auf `ticket_testing\_%`; die vier Testdatenbanken werden selbstständig angelegt/zurückgesetzt/verwaltet; **parallele DB-Läufe erst nach erfolgreichem Guard- und Verbindungstest**; Produktionsdatenbanken unberührt. Das GRANT führt Yama (Root) aus; der Generator misst dessen Wirksamkeit als Erstes (ENV_BLOCKED bei Fehlschlag).
- **Fortschrittsbericht (Phase 7):** Auftrag **Z0-F1** — HTML nur aus einem Snapshot erzeugen, 13-zeilige Aktivierungsmatrix, historische Abschnitte markiert.

---

## Wortlaut (Yama, 21.08.2026)

### Ausgangslage
Der Fortschrittsbericht „Fortschritt Hausplaner — Werkzeugkasten" ist eine wertvolle Chronik, aber
keine belastbare aktuelle Statuswahrheit. Er enthält mehrere Zeitstände nebeneinander. Deshalb gilt:
1. Keine Zahl, kein Zustand und kein Ball wird aus dem HTML-Bericht übernommen.
2. Vor jeder Aussage wird der aktuelle Repository-Stand frisch gemessen.
3. Maßgeblich sind der konkrete SHA, die aktuellen Auftragsblätter, Evaluatorvoten, Git-Historie und
   tatsächlich ausgeführte Prüfungen.
4. „BESCHRIEBEN" bedeutet nur: vorhandener Code wurde dokumentiert. Es beweist nicht, dass eine
   Funktion sichtbar, bedienbar, modellwirksam, speicherbar oder im Browser abgenommen ist.
5. Commitzahlen sind Aktivität, aber kein Fertigstellungsnachweis.

### Ziel
Am Ende muss genau eines der beiden Urteile vorliegen: **TESTBEREIT** — mit eindeutigem Test-SHA
und vollständigen Belegen · **NICHT TESTBEREIT** — mit konkreten offenen Kriterien, Ballbesitz und
nächster Handlung. Keine Zwischenform wie „fast testbereit".

### PHASE 0 — Pflicht-Stopp und unveränderlicher Ausgangsstand
Vor jeder weiteren schreibenden Arbeit: `git status --short --branch` · aktueller HEAD und Fernstand ·
`git worktree list --porcelain` · Voraus-/Rückstand aller Rollenbranches · aktuelle Besitzer aller
uncommittierten Dateien · laufende Prozesse und belegte Testressourcen.
Der Integrationscheckout enthält aktuell uncommittierte Kontostatus-Arbeit. Diese Änderungen gehören
nicht automatisch dem Integrator. **Pflicht:** Alle Schreiber im gemeinsamen Checkout stoppen ·
Eigentümer und Auftrag der uncommittierten Dateien feststellen · Nichts stagen, committen, zurücksetzen,
staschen oder löschen, bevor der Eigentümer bestätigt ist · Die Arbeit anschließend kontrolliert und
pfadgenau in den zuständigen Rollen-Worktree überführen · Vor dem Bereinigen des gemeinsamen Checkouts
muss ein bytegleicher, überprüfter Sicherungsstand im zuständigen Worktree oder Commit existieren ·
Kein `git add -A`, kein Sammelcommit und kein stilles Einsammeln · Gemeinsamer Checkout danach
ausschließlich für den Integrator. Alte Worktrees nur inventarisieren. Keine Löschung oder
Bereinigung ohne gesonderte Freigabe.

### PHASE 1 — Statuswahrheit korrigieren
Die aktuelle Statuszeile von A-37 ist überholt. Vorhandener Evaluatorbefund: VOTUM A-37 · Ergebnis:
NACHBESSERN · 20 von 21 Kriterien erfüllt · offen: A-37-21 · Ball laut Votum beim Planner, nicht beim
Evaluator. Der Integrator muss diesen Befund in die Statuswahrheit übernehmen, ohne ihn neu zu bewerten.
Der Planner entscheidet anschließend A-37-21: `js-yaml` wird direkt verwendet und darf nicht nur
transitiv über Puppeteer/Cosmiconfig verfügbar sein. Empfehlung: `js-yaml` ausdrücklich als direkte
versionierte Abhängigkeit aufnehmen und Lockfile aktualisieren. Zusätzlich die zwei SPEC-Widersprüche
berichtigen: 1. A-37-5: Überschrift und Tabelle müssen denselben Exitcode nennen. 2. Klarstellen, dass
die inneren YAML-Prüfer 2/3/4 unterscheiden, während `commit-pruefen.sh` nach außen mit 1 endet.
Danach: Planner → Plan-Prüfer → Generator im eigenen Worktree → unabhängiger Evaluator. Der Evaluator
prüft nach der Nachbesserung alle A-37-Kriterien erneut, nicht nur A-37-21.

### PHASE 2 — Rollenbarriere wirklich aktivieren
Dass Worktrees existieren, reicht nicht. Nachzuweisen ist ihre tatsächliche Benutzung. **Pflicht:**
Jede schreibende Rolle arbeitet ausschließlich in ihrem zugeordneten Worktree und Rollenbranch ·
`TICKET_ROLLE` und `scripts/rollen-tor.sh` müssen vor jedem Commit wirksam sein · `docs/STATUS.md`
darf außerhalb des Integrationscheckouts nicht geschrieben werden · Der Integrator darf ausschließlich
bereits committierte, klar zugeordnete Rollenarbeit übernehmen · Ein fremd bestückter Index muss vor
dem Commit erkannt werden · Staging und Commit dürfen nicht als zwei ungeschützte Schritte im
gemeinsamen Checkout stattfinden · A-38 ist anschließend zu prüfen und zu bauen, damit auch Merges
nicht am Rollen-Tor vorbeilaufen. **Positive und negative Prüfungen:** richtige Rolle, richtiger
Worktree, richtiger Branch → erlaubt · richtige Rolle, falscher Worktree → gesperrt · falscher Branch
→ gesperrt · fremde `STATUS.md`-Änderung → gesperrt · Merge ohne Rollenherkunft → vom A-38-Mechanismus
erkannt · Integrator im Integrationscheckout → erlaubt.

### PHASE 3 — Testressourcen isolieren
Die Evaluator-Abnahme hat belegt, dass parallele Läufe dieselbe Datenbank `ticket_testing`
zurücksetzen. Dadurch verschwand während einer Browserabnahme das Testkonto. Das ist ein
Infrastrukturfehler, kein Produktfehler. Vor weiteren parallelen Abnahmen muss technisch gelten:
Jeder gleichzeitig arbeitende Prüfer erhält eine eigene Datenbank (z.B. `ticket_testing_evaluator`,
`ticket_testing_generator`, `ticket_testing_security`, `ticket_testing_browser`) · Alternativ müssen
alle datenbankverändernden Läufe technisch serialisiert werden · Eine bloße Absprache reicht nicht ·
Jeder Testlauf muss vor dem ersten Schreibzugriff den tatsächlichen Datenbanknamen prüfen und bei
falscher Datenbank abbrechen · Kein Test gegen Produktion oder eine nicht eindeutig als Testdatenbank
erkannte Datenbank · Browserkonto und Testobjekt gehören ausschließlich zur jeweiligen Browserbühne ·
Ein paralleler Lauf darf Konto, IDs, Migrationen oder Tabellen eines anderen Prüfstands nicht
verändern. Diese Barriere muss mit einer Positiv- und einer Kollisionsprobe bewiesen werden.

### PHASE 4 — Z1 vollständig unabhängig abnehmen
Z1-W1-1 bis Z1-W1-5 bleiben getrennte Abnahmegegenstände. Für jeden Auftrag einzeln: echten
Bauumfang suchen, nicht nur `bau_sha` glauben · vollständigen Diff lesen · jedes Kriterium erneut
messen · TypeScript-Prüfung · vollständige Hausplaner-Suite · relevante Mutations-/Rotprobe ·
Scopeprüfung · getrenntes Votum ABGENOMMEN oder NACHBESSERN. Für Z1-W1-2 zusätzlich: Browserprüfung
der Walmdach-Fehlermeldung · Widerspruch an `dachformVorlagen.ts:478` auflösen · ohne verfügbaren
Browser keine Abnahme behaupten. Kein Sammelvotum für alle fünf.

### PHASE 5 — Z2-Zustände und Sicherheitswelle bereinigen
Die Statuswahrheit muss den realen Bauzustand abbilden. Bereits gebaute Aufträge dürfen nicht
dauerhaft auf ENTWURF stehen. Aktuell einzeln prüfen: W0-1 gebaut · W0-3 gebaut · W0-7 gebaut · W0-8
gebaut, aber Auftrag enthält einen Konflikt zwischen Route-Model-Binding und unveränderten
Routenerzeugern · W0-9 derzeit offenbar in Arbeit; uncommittierte Dateien nicht als gebaut zählen ·
übrige Aufträge nach aktuellem Stand neu messen. Für W0-8 darf der nicht umgesetzte Binding-Teil nicht
still als erfüllt gelten. Planner und Plan-Prüfer müssen entweder das Kriterium fachlich berichtigen,
weil `findOrFail` plus vorgezogene Rechteprüfung die Sicherheitswirkung bereits erfüllt, oder die
Routennamen und alle Erzeuger kontrolliert gemeinsam umstellen. Keine Mischform und keine nachträgliche
Umdeutung durch den Generator. **Verbindliche Entscheidungen:** Y-10: 8 h, konfigurierbar,
rückstellbar · Y-11: `api/secure/master-sets*` reversibel stilllegen, nicht löschen; Tests für
deaktivierten und bewusst aktivierten Zustand · W0-11: operandenunabhängige CSRF-Härtung von dem extern
nicht messbaren IDS-Rückgabeformat trennen · W0-9: deaktivierte Nutzer dürfen durch keinen Loginweg
automatisch reaktiviert werden · Rechte-Schalter `RECHTE_ALLE_FUER_ALLE` bleibt standardmäßig `false` ·
Der Schalter darf Eigentums-, Identitäts-, Objektbindungs- oder Kontostatusprüfungen niemals umgehen.
Jeder Z2-Auftrag durchläuft einzeln: Planner → Plan-Prüfer → Generator → Evaluator → Integrator.

### PHASE 6 — Statuskopplung und alte Aufträge
A-39, A-40 und A-42 dürfen nicht unbegrenzt als ENTWURF beim Plan-Prüfer liegen bleiben. Für jeden
Auftrag: aktuellen Basis-SHA prüfen · historische Zahlen neu messen · DoR erteilen oder mit genauem
Restpunkt zurückgeben · Ball und Alter aktualisieren. A-41 beziehungsweise `status-erzeugen.sh` darf
nicht nur vorhanden sein. Zu prüfen ist: Wird die Statuswahrheit tatsächlich aus Zustandscommits
erzeugt? Entsteht ein Auftragsdatensatz im selben Handgriff wie das Auftragsblatt? Kann weiterhin ein
gebauter Auftrag als ENTWURF erscheinen? Werden Ball, Zustand, Bau-SHA und Begründung atomar
nachgezogen? Werden unlesbare YAML-Blöcke verlustfrei gemeldet? Werden Befundnotizen vor dem ersten
vollständigen Erzeugungslauf gemäß A-42 erhalten? Wenn 16 von 16 Datensätzen nachträglich manuell
angelegt werden mussten, ist die Kopplung noch nicht betriebswirksam.

### PHASE 7 — Fortschrittsbericht neu erzeugen
Der HTML-Bericht wird nicht manuell fortgeschrieben, sondern aus einem einzigen festgeschriebenen
Snapshot neu erzeugt. Jede Ausgabe nennt: Zeitpunkt · Branch · SHA · Arbeitsbaumzustand · betrachtete
Zweige · Messverfahren. Historische Abschnitte müssen ausdrücklich als historische Momentaufnahme
markiert werden. **Mindestens folgende Kennzahlen getrennt führen:** 1. Register geklärt · 2. Code
vorhanden · 3. im Werkzeugkatalog registriert · 4. in der UI sichtbar · 5. aktivierbar · 6. Command
wird ausgelöst · 7. `SceneDocument` wird verändert · 8. Undo/Redo funktioniert · 9. gespeichert und neu
geladen · 10. 2D wirksam · 11. 3D wirksam · 12. Bundle aktuell · 13. Browserabnahme erfolgt. Erst diese
Aktivierungsmatrix beantwortet, warum 43 geklärte Registerzeilen noch nicht wie ein vollständiger
Werkzeugkasten wirken. **Nicht mehr als Fortschrittsbeweis verwenden:** reine Commitzahl ·
„BESCHRIEBEN" allein · vorhandene Datei ohne produktiven Aufrufer · grüner Test ohne
Rot-/Mutationsprobe · Ballbesitz ohne passenden Zustand.

### PHASE 8 — Dachschichten erst nach TESTBEREIT
Ticket-Hausplaner bleibt Modell- und Renderbasis · Playground liefert ausschließlich Ideen für
Layer-Gruppen und Explosion · Kein Kopieren der monolithischen Playground-Seite · `RoofNode.schichten`
additiv und optional · granulare Commands für Hinzufügen, Ändern und Entfernen · gespeichertes
Ansichtsprofil getrennt von physischer Konstruktion · erste Stufe: Schichtensichtbarkeit nur in 3D ·
`roof.visible` als Ganzes bleibt in 2D und 3D konsistent · `dachformVorlagen.ts` und der
deckungsneutrale Wächter bleiben unangetastet · keine vorzeitige Verdrahtung von `holzBauteile`,
`holzMengen` oder manueller Sparrenrechnung · Bau erst nach dem Gesamturteil TESTBEREIT.

### GESAMTABNAHME
TESTBEREIT darf erst gemeldet werden, wenn: A-37 abschließend abgenommen und wirksam ist · alle fünf
Z1-Aufträge einzeln ABGENOMMEN sind · W0-7 ABGENOMMEN ist · W0-1, W0-2 und W0-3 ABGENOMMEN sind ·
beide Rechte-Schalterstellungen getestet wurden · ohne Recht bei Schalter=false zuverlässig 403
entsteht · Eigentums- und Identitätsverletzungen unabhängig vom Schalter verhindert werden ·
deaktivierte Nutzer sich über keinen Loginweg anmelden können · alle Tests ausschließlich gegen
isolierte Testdatenbanken liefen · die Browserprüfung mit Testbenutzer und Testobjekt durchgeführt
wurde · der Arbeitsbaum sauber ist · ein eindeutiger Test-SHA genannt wird · bekannte Restpunkte
getrennt ausgewiesen sind.

### Ausgabeformat
1. Aktueller unveränderlicher Snapshot · 2. Korrigierte Zustands- und Balltabelle · 3. Ergebnis je
Phase · 4. Ausgeführte Prüfungen mit echter Ausgabe und Zählern · 5. Nicht ausgeführte Prüfungen ·
6. Offene Restpunkte mit Ballbesitz · 7. Test-SHA · 8. Eindeutiges Urteil TESTBEREIT oder NICHT TESTBEREIT.

### Grenzen
keine Produktionsdaten · keine fremden uncommittierten Änderungen übernehmen · kein Merge nach main ·
kein Push, Release oder Deployment ohne gesonderte Freigabe von Yama · keine Browserabnahme behaupten,
wenn sie nicht durchgeführt wurde · Generator nimmt den eigenen Bau niemals ab · historische Befunde
nicht löschen, sondern als überholt kennzeichnen.
