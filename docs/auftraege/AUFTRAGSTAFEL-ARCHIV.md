# ⇒ AUFTRAGSTAFEL — ARCHIV (abgeschlossene Posten)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Anlass:** Yama, 25.07.: *„mach das"* — die Tafel war auf
39 Zeilen gewachsen und der eine aktive Posten darin nicht mehr auffindbar.

**Was hier steht:** Posten mit Status `ERLEDIGT` oder `ENTFÄLLT`, **wortgleich** aus der Tafel
herausgenommen — keine Zeile gekürzt, keine Formulierung geglättet. Ein archivierter Posten ist kein
gelöschter Posten. Wer einen Beleg sucht, findet ihn hier.

**Was hier nicht steht:** die Belege selbst. Die Wahrheit bleibt `docs/handoff-status.md` —
das Archiv ist Register, nicht Beweis (Tafel §0).

**Zurück ist erlaubt.** Zeigt eine Nachmessung, dass ein abgenommener Posten doch offen ist, wandert die
Zeile zurück auf die Tafel — mit Begründung im Ledger. Archiv heißt abgeschlossen, nicht unantastbar.

---

## Abgeschlossen — Stand 25.07.2026

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-12** | **Dashboard v2 — Flächen des Werkzeug-Dashboards** (v2.1 Kontext-Options-Leiste, v2.2 Panel-Reiter, v2.3 Projektbrowser, v2.4 Prüfungscenter, v2.5 Befehlspalette). **Vorrang vor AUF-11** — Yama, 25.07.: „wir haben dashboard design fest gelegt sollst als erstes fertig gestellt werden v1 usw". Zwei Batches, Batch 1 wird berichtet und abgenommen, bevor Batch 2 beginnt. Ändert den Store **nicht** und liegt damit **außerhalb** des Sperrbereichs von AUF-1 | Generator | ``ERLEDIGT` — Batch 1 **freigegeben mit Auflage** (Evaluator-Votum 25.07.) · **Batch 2 `BERICHTET`** (`5092b10`) — wartet auf Evaluator, Spezifikation `evaluator-auftrag-dashboard-v2-batch2.md` | `generator-auftrag-dashboard-v2-flaechen.md` (Auftrag) + `docs/fahrplan-dashboard-versionen.md` (Fahrplan v1–v6) + `evaluator-auftrag-dashboard-v2-batch1.md` (Evaluator-Spezifikation, Planner 25.07.) |
| **AUF-1** | **A1-Abnahme wiederholen** (Gegenstand `c0ffe31`), unter den zwei neuen Auflagen E1 (erst messen, dann lesen) und E2 (voller Prüfrahmen, nicht nur N1–N7) | Evaluator, **frische Instanz** | `ERLEDIGT` — **Freigabe mit Auflage** (Votum 25.07., frische Instanz). Vier Auflagen binden **A2**: Kürzel `R`/`K` verriegeln · `zoneTools` memoisieren · Render-Pfad-Test · `herkunft` entscheiden | `evaluator-auftrag-wizard-welle-a1-werkzeug-praesentation.md` + Ledger-Block „ZWEI ERGÄNZUNGEN" (Z. 1103) |
| **AUF-4** | **Wizard-Welle A2** *(= dieser Posten; „A2" und „AUF-4" sind dasselbe)* — Leiste liest die Präsentationsschicht, inkl. P9-Memoisierung | Generator | `ERLEDIGT` — **FREIGABE MIT AUFLAGE** (`32b1862` · `728ae69`), 3/4 A1-Auflagen testverriegelt, Auflage 3 = Infra-Lücke → AUF-30. Gegenzeichnung AUF-29 läuft nebenher — umgesetzt 25.07. Das ist **L1** aus `fahrplan-frontend-layout-hausplaner.md` und der Engpass des gesamten Layouts. Die vier A1-Auflagen sind mit umzusetzen | `generator-auftrag-wizard-welle-a2-leiste-liest-praesentation.md` **inkl. §8** |
| **AUF-20** | **ID-Sprache entscheiden** — die gerenderte Registry ist deutsch (`wand`, `fenster`, `tuer`, `dach`, `decke`, `treppe`, `auswahl`, `loeschen`, `duplizieren`), Katalog und das neue 110er-Paket sind englisch (`wall`, `window`, `door`, …). Eine Wahrheit je Sachverhalt: entweder 9 Registry-IDs umbenennen (berührt Commands, Tests, Fixtures) oder 110 Paket-IDs. **Vor dieser Entscheidung wird kein Icon einsortiert** — sonst entstehen 110 Dateinamen, die man danach wieder anfassen muss | Planner | `ERLEDIGT` — **von Yama überstimmt 25.07.: alles deutsch.** Werkzeug-IDs bleiben deutsch, die 110 Paket-IDs werden eingedeutscht. Gespeicherte Datenwerte im Szenendokument bleiben unberührt (Datenschutz, keine Sprachfrage) | `docs/planner/inventur-werkzeug-icons-2026-07-25.md` §7 |
| **AUF-24** | **Die 9 Werkzeug-IDs auf Englisch umbenennen** — `auswahl→select · wand→wall · fenster→window · tuer→door · dach→roof · decke→ceiling · treppe→stair · loeschen→delete · duplizieren→duplicate`. Labels bleiben deutsch. 210 Treffer in ~30 Dateien (Registry, Aktivierung, Zonen, Commands, Fixtures, Tests). Berührt **kein** persistiertes Schema — die IDs stehen dort nicht (je 0 Treffer). **Vor I2 von AUF-21** | — | `ENTFÄLLT` — Yama 25.07.: alles deutsch, keine Umbenennung. Kette verkürzt sich auf drei Schritte | `docs/planner/entscheidung-id-sprache-werkzeuge.md` |
| **AUF-26** | **Panel- und Label-Kappung beheben** (Spur B) — bei 1375 px ist der vierte Reiter „Historie" unsichtbar, „↕ Oben/Unten" gekappt, Hinweistext bricht im Wort ab; Rail-Labels abgeschnitten ohne `title`. Reiterzeile bricht um oder scrollt, kappt nicht; kein `overflow:hidden` auf textführenden Flächen; gekappte Labels bekommen `title`. Pflicht-Viewports 1440/1024/375 | Generator (nativ) | `ERLEDIGT` — **FREIGABE** (`da50af4`): per iframe bei 1440/1371/371 px gemessen, alle vier Reiter sichtbar, keiner geklippt, Panel fest 268 px. **Der ~1375-px-Defekt ist AUF-34 zugeordnet** (dreizeilige Gruppenzeile treibt den waagerechten Überlauf) — meine frühere Zuordnung war falsch | `docs/planner/ux-befund-expertenmodus-2026-07-25.md` B3/B4 |
| **AUF-32** | **Kuratierung für I3 — nach Kategorie statt Werkzeug für Werkzeug** — `priority` taugt nicht (5 von 110 `primary`), `canPin` ist überall `true`. Vorschlag: die 110 über ihre **22 Paket-Kategorien** in Gruppen-Menüs zeigen, wie in `dashboard-tools-v1.html` entworfen (Ansicht · Bearbeiten · Transformieren · Anordnen · Messen · Bemaßen …), daneben die angehefteten. Aus 110 Einzelentscheidungen werden 22 | Planner → Yama | `ERLEDIGT` — **Yama 25.07.: „ja"**. Gruppierung nach den 22 Kategorien freigegeben, I3-Auftrag geschrieben | Planner-Befund 2 zu I2 |

---

## Notizen, die mit diesen Posten mitgewandert sind

**Zu AUF-12 (Vorrang):** Yama hat am 25.07. entschieden, dass das Dashboard-Design steht und
**zuerst fertiggestellt** wird — versionsweise, v1 ist gebaut, v2 ist dieser Auftrag. Damit rückt AUF-12
vor AUF-11: nicht weil die L1–L7-Inventur falsch wäre, sondern weil ihre **Reihenfolge** einer anderen
Entscheidung folgte. Die Zuordnung v1–v6 → L1–L7 steht in `docs/fahrplan-dashboard-versionen.md` §4;
kein Posten aus L1–L7 fällt weg, jeder bekommt eine Versionsnummer.

**Warum AUF-12 nicht hinter AUF-1 gesperrt ist:** AUF-1 sperrt AUF-4, weil A2 `toolPresentation.ts`
liest. Dashboard v2 fasst weder `toolPresentation.ts` noch den Store noch Zod an — gemessen und in §5
des Auftrags als Guardrail festgeschrieben. Die beiden Arbeitsflächen überschneiden sich nicht.

