# Abarbeitungs-Reihenfolge — ticket CRM, Schritt für Schritt

**Was das ist:** Die konkrete Reihenfolge, in der wir die Kapitel des Gesamtkonzepts anfassen — mit Begründung je Schritt. Verdichtet Konzept (das Buch, `gesamtkonzept-ticket-crm.md`) + Fahrplan (die grobe Route) zu einer durcharbeitbaren Liste.

**Ordnungslogik (3 Kriterien, in dieser Priorität):**
1. **Blockiert es anderes?** Entscheidungen, von denen viel abhängt → zuerst (sonst baut man auf Sand).
2. **Schmerz oder Sicherheit?** Echte Bugs + risikoarme Aufräumarbeit → dürfen jederzeit dazwischen.
3. **Braucht es erst Verstehen?** Grob kartierte Bereiche → erst Detail-Inventur, dann anfassen.

**Zwei Spuren laufen parallel** (stören sich nicht — die eine ist *bauen*, die andere *entscheiden*):
- **Bau-Spur:** konkrete, sichere Handarbeit (Zerlegung, Bugfixes).
- **Denk-Spur:** Weichen entscheiden, Bereiche vertiefen.

---

## Die Reihenfolge — als Etappen

### ETAPPE 1 — Fundament fertig machen (Denk-Spur) · HÖCHSTE PRIORITÄT
*Diese Dinge blockieren den ganzen Kernprozess-Umbau. Solange sie offen sind, ist Teil III–V nicht bau-reif.*

**1.1 — Weiche 1: Verbindliche Statusquelle** (Kap. 2/4)
Die wichtigste offene Entscheidung. Die Auflösung (Phase/Zustand/Historie) steht im Sollkonzept — sie muss ratifiziert werden. Löst Schwäche 1 (die ~11 Status-Felder) auf, die sich als „mehrere Wahrheiten"-Muster durchs ganze System zieht. *Gemeinsam durchsprechen, ich lege die Optionen vor, du entscheidest.*

**1.2 — Weiche 2: Angebot-Annahme Pflicht vor Auftrag?** (Kap. 2)
Reine Geschäftsregel — hängt an eurem Vertriebsalltag. Schnell zu entscheiden.

**1.3 — Steuerberater-Paket bündeln & Termin** (Kap. 2/7/14)
Weiche 3 (Rechnungssystem) + Weiche 4 (Storno-Folge) + die 10 Controlling-Fragen. *Ein* gebündeltes Gespräch. Läuft extern, blockiert intern nichts, außer der Rechnungs-Konsolidierung.

→ **Nach Etappe 1 steht das Fundament.** Erst dann ist der Kernprozess-Umbau (Etappe 4) erlaubt.

---

### ETAPPE 2 — Bau-Spur: Kundenprofil zerlegen (läuft parallel zu Etappe 1)
*Sicher, unabhängig von den Weichen, im Fluss. Kann jederzeit voranlaufen, während Etappe 1 gedacht wird.*

**2.1 — Scheibe 2a: serialsOverlay** → Partial. ✓ **ERLEDIGT** (Commit `a97be10`, byte-genau, alle 8 Element-IDs im gerenderten DOM verifiziert).
**2.2 — Scheibe 2b/2c:** doneHistoryModal, halfDoneModal (weitere 0-Blade-Modals, Muster wiederholen).
**2.3 — weitere Content-Blöcke** nach Rangliste im Schnittplan (sicher → riskant).
**2.4 — JS-Bootstrap-Konstanten, dann JS-Module** (der riskante Teil, zuletzt, jede Scheibe einzeln).
**2.5 — zweite Datei `customer_view.blade`** (eigener Schnittplan danach).

---

### ETAPPE 3 — Aufräumen & echte Bugs (Bau-Spur, jederzeit dazwischen)
*Unabhängig, risikoarm oder klar umrissen. Gut als Füller zwischen größeren Schritten.*

**3.1 — echter Bug: 404-Referenzen `object-context-menu-final.*`** (Kontextmenü lädt gar nicht). Klein, echter Alltagsdefekt.
**3.2 — Skript-Dubletten** (Sortable, chart.js je 2× geladen). Kosmetisch, trivial.
**3.3 — Doppel-DOM-ID `maHoverPreviewOverlay`** (vor Zerlegung von Block Q).
**3.4 — Sicherheitspunkt: öffentliche IDS-Callback-Routen ohne Auth** (Kap. 9) — *prüfen, ob real exponiert; wenn ja, priorisieren.*
**3.5 — restliche Customer-Fallen-Stellen** im Zuge der `customers`-Bereinigung (Glossar Kap. 1).

---

### ETAPPE 4 — Kernprozess-Umbau (NUR nach Etappe 1!)
*Gesperrt, bis die Weichen stehen. Dann in dieser Reihenfolge:*

**4.1 — Statusführung vereinheitlichen** (nach Weiche 1) — Phase/Zustand/Historie sauber trennen.
**4.2 — Erfassungs-Workflow: Mehrfachheit nutzbar machen** (Kunde→Objekt→Gewerke; UI existiert schon, nur Nutzerführung). Nach Weiche 5 (steht) + Statusarbeit.
**4.3 — Rechnungssystem konsolidieren** (nach Weiche 3, Steuerberater).

---

### ETAPPE 5 — Grob kartierte Bereiche vertiefen (Denk-Spur, vor Bau)
*Diese Kapitel sind nur grob kartiert. Erst Detail-Inventur, dann bau-reif. Reihenfolge nach Wichtigkeit/Blockier-Wirkung:*

**5.1 — Planner / Projektmanagement** (Kap. 11) — größter Brocken (~11k Z.), **3 parallele Phasen-Systeme + projects/planner_plans**. Wichtigster Vertiefungs-Kandidat, weil es dasselbe „mehrere Wahrheiten"-Muster ist wie beim Status — und weil Projekt/Bauphase (Weiche 5) schon entschieden ist, gibt es hier Anschluss.
**5.2 — Master-Set / Angebots-Konfiguration** (Kap. 6) — ~6.700 + 25k Z.; hängt eng am Angebots-Workflow.
**5.3 — Produktkatalog** (Kap. 8) + **5.4 — Lager/Beschaffung/Großhandel** (Kap. 9) — die Warenwelt; Katalog vor Beschaffung (Beschaffung baut auf Katalog auf).
**5.5 — HR-Monolith** (Kap. 10) — viel Code, DB leer; niedrigere Dringlichkeit.

---

### ETAPPE 6 — Fehlende Funktionen & Altlasten (später)
**6.1 — Serverseitiges Angebots-/Auftrags-PDF** (Kap. 7) — echte Lücke; Priorität hängt davon ab, wie sehr es im Alltag fehlt (deine Einschätzung).
**6.2 — Legacy-Aufräumung** (Kap. 16) — ~58.500 Z. toter Ballast entfernen. Großer Aufräum-Strang, niedrige Dringlichkeit, aber hoher Ordnungs-Gewinn. Erst wenn sicher ist, dass wirklich 0 Live-Routen dranhängen.
**6.3 — Cross-Gewerk-Intelligenz & Cockpit** (Kap. 14, Zielbild) — die „intelligente Schicht", ganz zuletzt, baut auf allem darunter auf.

---

## Was JETZT dran ist (die nächsten 3 konkreten Schritte)

1. **Bau-Spur:** Scheibe **2b/2c** (doneHistoryModal, halfDoneModal) schneiden — Muster wie 2a wiederholen. *(2a/serialsOverlay ist bereits erledigt, `a97be10`.)*
2. **Denk-Spur:** Weiche 1 (Statusquelle) gemeinsam durchsprechen — die wichtigste offene Entscheidung.
3. **Denk-Spur:** Weiche 2 (Angebot-Pflicht) — schnell, direkt danach.

Danach: Etappe 2 weiter (Profil) + Etappe 5.1 (Planner-Detail-Inventur) als nächster großer Verstehens-Schritt.

---

## Prinzipien, die die Reihenfolge schützen
- **Nie Etappe 4 vor Etappe 1.** Kein Kernprozess-Bau, bevor die Weichen stehen.
- **Nie einen grob kartierten Bereich (Etappe 5) bauen, bevor seine Detail-Inventur da ist.**
- **Bau-Spur und Denk-Spur laufen parallel** — man muss nicht auf das eine warten, um das andere zu tun.
- **Jeder Bau-Schritt:** kleiner Auftrag → Pflicht-Stopp/Befund → gemeinsame Freigabe → Bau → Verifikation.
- **Neue Funde** → Backlog-Eintrag, nicht sofort verfolgen.
- **Reihenfolge schlägt Neugier:** der interessanteste Fund ist nicht automatisch der nächste Schritt.

---

*Konkrete Route. Ergänzt Konzept (`gesamtkonzept-ticket-crm.md`) + 8-Zonen-Inventur (`crm-inventur-00-index.md`). Fortschreiben: erledigte Etappen abhaken.*
