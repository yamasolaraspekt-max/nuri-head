# MASSSTAB — „Sehr gute Abschlussarbeit": zwölf Muss-Kriterien und die Beweise dazu

```yaml
erteilt_von: "Yama, 21.08.2026 spaet — Wortlaut uebernommen; gilt als Abnahme-Massstab fuer das Urteil TESTBEREIT"
trennung: "Frage 1 — ist der Abschlussprozess sehr gut und TESTBEREIT? Frage 2 — ist der Hausplaner als Produkt sehr gut? Der Golden Path ist fuer das Abschlussurteil KEIN Pflichtbau, fuer die spaetere Produktbewertung zentral."
neue_reihenfolge_yama: "1 A-42 unabhaengig schliessen -> 2 A-37 als technische Schreibbarriere vollstaendig aktivieren (21/21) -> 3 Z0-I1 bauen und abnehmen -> 4 erst danach die uebrigen parallelen Abnahmen"
stand_bei_uebernahme: "gute bis teilweise sehr gute Einzelarbeit, aber noch keine insgesamt sehr gute Abschlussleistung — keines der zwoelf Kriterien vollstaendig gruen"
```

## Harte Kriterien (alle Muss; ein roter Punkt wird durch keine Menge grüner Tests ausgeglichen)

| Nr. | Kriterium | Erforderlicher Beweis | Stand 21.08. |
|---:|---|---|---|
| 1 | Eindeutiger Prüfstand | Branch, sauberer Baum, unveränderlicher Test-SHA | teilweise |
| 2 | Rollen technisch getrennt | richtige Rolle, richtiger Worktree, fremde Schreibzugriffe technisch blockiert | offen |
| 3 | A-37 vollständig wirksam | 21/21 unabhängig geprüft, direkte Abhängigkeit plus Lockfile, sechs Worktrees getestet | offen |
| 4 | A-42 verlustfrei abgenommen | Block-, Zeilen-, Zaun- und Ballbilanz; idempotenter Zweitlauf | gebaut, Abnahme offen |
| 5 | Testdatenbanken isoliert | vier DBs, Rollenautomatik, Schutz vor falscher DB, Parallel- und Kollisionsprobe | offen |
| 6 | Z1 vollständig abgenommen | fünf getrennte Evaluatorvoten, keine Sammelabnahme | 2 von 5 (W1-3, W1-4) |
| 7 | W0-Sicherheitswelle abgenommen | Rechte, Eigentum, Identität, Loginstatus und beide Schalterstellungen | offen |
| 8 | Spezifikationen erfüllbar | kein Kriterium verlangt nicht vorhandene Daten oder unmögliche Wirkung | W0-11 offen |
| 9 | Gesamte Qualitätstore grün | echte Ausgaben auf demselben Test-SHA | Schlusslauf fehlt |
| 10 | Browserabnahme erfolgt | authentifizierter Benutzer, isolierte Browser-DB, Screenshots und Konsole | offen |
| 11 | Statuswahrheit stimmt | Bau, Zustand, Ball, Votum und SHA atomar und widerspruchsfrei | weiterhin Nachlauf |
| 12 | Eindeutiges Abschlussurteil | TESTBEREIT oder NICHT TESTBEREIT mit Test-SHA und offenen Restpunkten | fehlt |

## 1 · A-42 unabhängig abschließen (Evaluator)
Bau `26c46f31` ist stark (zwei beschädigende Versuche vor dem Commit erkannt und zurückgenommen) —
der Generatorbericht reicht nicht. Der Evaluator prüft unabhängig: `461 = 289 + 172` am damaligen
Ausgangsstand · alle 172 Blöcke byte-identisch · keine verlorene oder hinzugefügte Inhaltszeile ·
gerade Zaunbilanz in beiden Dateien · 104 echte Auftragsdatensätze unverändert · Ballbesitz je Rolle
vorher = beide Dateien nachher · zweiter Lauf verändert keine Datei · die zwei zurückgebliebenen
Überschriften sind bewusst bewertet · `scripts/yama-posten.py` und alle Wacheanweisungen durchsuchen
anschließend beide Dateien · `docs/STATUS.md` enthält danach Bau-SHA, Zustand und Evaluatorball.
Erst dann ist A-42 `ABGENOMMEN`, nicht nur gebaut.

## 2 · A-37 muss wirklich 21/21 erreichen (Fundament)
`js-yaml` direkt in `package.json` · auch als Root-Abhängigkeit im Lockfile · reproduzierbare
Installation aus dem Lockfile · Tor in allen sechs relevanten Checkouts vorhanden · richtige Rolle im
richtigen Baum kommt durch · falsche Rolle abgewiesen · falscher Branch abgewiesen · fehlende
Rollenkennung mit dem festgelegten Rückgabewert abgewiesen · Rollen-Worktrees dürfen `docs/STATUS.md`
nicht schreiben · ausschließlich der Integrator darf dort schreiben · Syntax-, Modul- und
Laufzeitfehler unterscheidbar gemeldet · Rollenmarken mit Klammerzusätzen richtig behandelt · jeder
negative Fall tatsächlich ausgelöst, nicht nur im Code gefunden · vollständige Wiederabnahme aller 21
Kriterien. **Ein weiterer Generatorcommit direkt auf dem Integrationszweig wäre nach Aktivierung
dieses Tors ein klares Ausschlusskriterium für „sehr gut".**

## 3 · Z0-I1 muss technisch funktionieren
`SHOW GRANTS` bestätigt die Rechte von `ticket_user` · Rechte nur auf `ticket_testing_%`, keine
Produktionsdatenbank · vier echte Datenbanken `ticket_testing_evaluator/_generator/_security/_browser` ·
`TICKET_ROLLE` wählt die Datenbank automatisch · keine Rolle trägt den Namen je Lauf manuell ein ·
vor dem ersten Schreibzugriff `SELECT DATABASE()` geprüft · `ticket` oder unbekannter Name → Abbruch
vor Migration, Seed oder Truncate · zwei Rollen laufen gleichzeitig gegen getrennte Datenbanken ohne
gegenseitigen Einfluss · absichtlicher Lauf gegen dieselbe Datenbank wird verhindert oder sichtbar
serialisiert · Browserkonto und Testobjekt überleben parallele Generator- und Evaluatorläufe · der
Evaluator wiederholt diese Tests unabhängig. Erst dann gelten DB- und Browserabnahmen parallel als
vertrauenswürdig.

## 4 · Gebaute Aufträge wirklich abschließen (je Auftrag)
`Auftrag → freigegebener Plan → Bau-SHA → vollständiger Diff → eigene Gegenprobe → unabhängiges Votum
→ Browser, falls sichtbar → Statusnachtrag`. Für Z1: fünf getrennte Voten · nicht nur Übernahme der
Generatorzahlen · TypeScript und vollständige Hausplaner-Suite je relevantem Prüfstand ·
Mutations-/Rotprobe · Scopeprüfung · Walmdach-Fehlermeldung tatsächlich im Browser · Widerspruch an
`dachformVorlagen.ts` auflösen · W1-3 nach seiner Abnahme auch auf den korrekten Zustand setzen.

## 5 · W0-11 braucht eine erfüllbare Wahrheit
Das Blatt versprach eine persistente Importeur-Bindung, obwohl `ImportedIdsItem` diese Information
nicht speichert. Der Planner wählt verbindlich: Urheberspalte additiv einführen · Urheberschaft als
eigenen Auftrag trennen · oder Teil A ehrlich auf die durchsetzbare Session-/Request-Schutzwirkung
begrenzen. Danach erneute DoR, ggf. neuer Bau, Migration und Rückweg bei Schemaänderung,
Fremdzuschreibungsprobe, unabhängige Abnahme. **Ein Kriterium darf nicht grün werden, weil der Test
nur prüft, ob ein nicht speicherbares Feld an `create()` übergeben wurde.**
*(Planner-Entscheid 21.08.: Teil A ehrlich begrenzt; Urheberspalte = separater Fachauftrag W0-11c —
siehe Blatt.)*

## 6 · Statuswahrheit hält Schritt
Nicht mehr regelmäßig: abgenommener Auftrag auf `CODE_FERTIG` · gebauter auf `BEREIT`/`ENTWURF` ·
Tafelzeile und Datensatz mit verschiedenen Bällen · Evaluatorvotum nur in einem Committext ·
manuelle Nachträge Tage später. Idealer Nachweis: Zustandscommit erzeugt Zustand, Ball, Bau-SHA und
Begründung atomar · Statusgenerator idempotent · zweiter Lauf = leerer Diff · unlesbares YAML stoppt
sichtbar · kein Befundblock geht verloren · Bericht und Status aus demselben Snapshot.

## 7 · Schlussprüfung auf genau einem Test-SHA
Auf einem sauberen, eindeutig benannten Commit mindestens: Schema-Prüfung · TypeScript · vollständige
Hausplaner-Suite · DOM-Tests · Produktions-Bundle · Bundle-Drift-Prüfung · relevante PHP-/Featuretests ·
Rechte- und Mandantentests · Persistenz-, Migrations- und Rückwegstests · Browserprüfung. Jeder Lauf
mit vollständigem Befehl, Rückgabewert, Anzahl bestanden/fehlgeschlagen, Branch und SHA, verwendeter
Testdatenbank, Datum und Rolle. **Ein grüner Lauf auf einem anderen Commit ist kein Beweis für den
Test-SHA.**

## 8 · Browserabnahme darf nicht ersetzt werden
Authentifizierter Browserlauf: Testbenutzer anmelden · Rolle und Rechte bestätigen · Testobjekt öffnen ·
Handlung durchführen · erwartete 2D-/3D- oder Fehlermeldung sehen · speichern und neu laden bei
Persistenz · Browserkonsole ohne neue Fehler · Netzwerkzugriffe auf richtige Endpunkte · feste
Screenshots · Testkonto nach parallelen Läufen weiterhin vorhanden. HTTP-Status, DOM-Test oder
Headless-Ersatz allein genügt nicht.

## Das Urteil, wenn alles grün ist
```text
TESTBEREIT
Test-SHA: <sha>
Arbeitsbaum: sauber
A-37: 21/21
Z1: 5/5 einzeln abgenommen
W0-Pflichtmenge: vollständig abgenommen
Z0-I1: Parallel- und Kollisionsprobe bestanden
Browser: durchgeführt
P0/P1 offen: 0
```
Bedingungen: alle zwölf Muss-Kriterien grün · kein P0/P1-Restpunkt offen · alle Nachweise auf
demselben Test-SHA · Generator und Evaluator vollständig getrennt · Status und Commits stimmen
überein · Fehler nicht nur repariert, sondern durch wirksame Gegenproben dauerhaft verhindert · genau
ein ehrliches Urteil.

## Für einen „sehr guten Hausplaner" zusätzlich (nach dem Abschluss: GP-0 … GP-3)
echte Bodenplatte getrennt von Zwischendecke · Höhenkette aus einer Quelle · Grundfläche →
Bodenplatte → EG → Treppe → Zwischendecke → OG → Dach · Änderungen zeigen Auswirkungen vor dem Commit ·
Zurück, Undo/Redo, Phase zurücksetzen getrennt · Dachschichten echte gespeicherte Projektdaten · CAD-,
Konstruktions-, Präsentationsmodus · Speichern/Schließen/Neuladen ohne Zustandsverlust · 2D und 3D
dieselbe Konstruktion · vollständiger Golden Path unabhängig im Browser wiederholt.
