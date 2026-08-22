# BERICHTSREGELN — Fortschritts- und Lageberichte (gilt ab 22.08.2026)

```yaml
erteilt_von: "Yama, 22.08.2026 00:2x — Wortlaut uebernommen; gilt fuer jeden Fortschritts-/Lagebericht (Text, HTML, Tafel), egal wer ihn erzeugt"
grund: "Ein Bericht mischte historische Rueckblicke, alte Zwischenstaende und aktuellen Zustand in einer Oberflaeche (Kopf 2f5e8b92 bei Integrationsstand 3b2e5334; '6/6 ACKs' ohne Generation; A-42 offen dargestellt, obwohl ABGENOMMEN; ungleiche Grundmengen nebeneinander; 'BESCHRIEBEN = nichts zu bauen'; 412 Commits als Fortschritt; Z2 mit Hausplaner vermischt; Pull-Betrieb als durchgesetzt)"
```

## 1 · Genau ein Mess-SHA, atomar
Jeder Bericht misst **einen** Stand: `Mess-SHA` (Integrations-HEAD zum Messzeitpunkt) + `Datenzeitpunkt`.
Im Kopfbereich stehen **ausschließlich** Tatsachen dieses Mess-SHA. Historische Aussagen werden nicht
gelöscht, sondern in einen **klar markierten Rückblick** verschoben. Ein Bericht, dessen Kopf einen
anderen SHA nennt als seine Zahlen, ist falsch.

## 2 · ACKs nur je Generation — Tabelle statt Summe
Nie „n/n ACKs". Immer eine Tabelle je Rolle:
`Rolle · Auftrag · aktuelle Generation · ACK-Generation · Digest gültig · Sitzung · Lease · Arbeitszustand`.
Ein ACK gilt nur für die Generation, die es nennt.

## 3 · Pull-Betrieb = SOFT-AKTIV
Bis A-37 (technische Commit-Barriere: Rolle, Sitzung, Worktree, Branch, Auftrag, Generation, Digest,
Lease; auch nacktes `git commit` und Merge) umgesetzt **und negativ abgenommen** ist, heißt der Pull-Betrieb
**`SOFT-AKTIV — organisatorisch wirksam, technisch noch umgehbar`**. Nie „durchgesetzt".

## 4 · Drei Reifegrade, strikt getrennt
1. `CODE VORHANDEN/BESCHRIEBEN` — beweist Codebestand, **nicht** Produktreife. Vorhandener Code kann trotzdem
   nicht in der Oberfläche erreichbar, nicht aktivierbar, nicht über Commands wirksam, nicht im `SceneDocument`
   gespeichert, nach Speichern/Laden nicht stabil, in 2D/3D nicht sichtbar, im Browser nicht abgenommen sein.
2. `PRODUKTWEG ANGESCHLOSSEN` — der Weg `UI → Werkzeug → Command → SceneDocument → Undo/Redo → Speichern/Laden
   → 2D/3D` ist belegt.
3. `BROWSERABGENOMMEN` — der Weg ist im Browser unabhängig abgenommen.
Ein Werkzeug gilt erst mit Stufe 3 als produktreif.

## 5 · Kennzahlen
Commitanzahl ist **Aktivitätsinformation**, nie Fortschrittswert (leere, fremde, korrigierende Commits
erhöhen sie). Primäre Kennzahlen: testbare Werkzeuge · vollständig angeschlossene Werkzeuge ·
browserabgenommene Golden-Path-Schritte · offene Blocker · ältester Auftrag · aktuelle Abnahmekette.
Zahlen verschiedener Grundmengen (Registerzeilen, Auftragszeilen, Generator-Aufträge, Registry-Werkzeuge)
stehen nie so nebeneinander, dass sie vergleichbar wirken — jede Zahl nennt ihre Grundmenge.

## 6 · Zwei getrennte Ansichten
`Hausplaner / Golden Path` und `Plattform, Rechte, Steuerungsarchitektur` (Z2, A-37, Z0-I*) werden nie in
einer Fortschrittsansicht gemischt.

## 7 · Sitzungsidentität bei headless Betrieb
Eine alte PID ist **kein** Lebensnachweis (jeder `--resume`-Lauf ist ein neuer Prozess). Stabile Identität =
**Sitzungs-ID**; je Lauf: Prozess-ID + Startkennung; dazu aktuelle Generation + Digest, **atomarer
Heartbeat** und Schreibrecht **ausschließlich unter gültiger Lease**. Transkript-mtime ist nur
Aktivitätshinweis.

## 8 · Abschluss jedes Berichts
`Mess-SHA · Datenzeitpunkt · aktuelle Wahrheit · offene Abweichungen · genau EINE nächste Handlung`.
