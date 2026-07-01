# Vertrauens-Landkarte — was ist gesichert, was ist Vermutung

**Zweck:** Ehrliche Sortierung der bisherigen Arbeit nach Sicherheitsgrad. Antwort auf die berechtigte Frage "kann ich dem trauen?". Keine Beruhigung — eine klare Unterscheidung, worauf man bauen kann und was noch Prüfung braucht.

**Vier Sicherheitsstufen:**
- 🟢 **BEWIESEN** — byte-genau oder real verifiziert, mathematisch/empirisch belegt. Darauf kann man bauen.
- 🔵 **ENTSCHIEDEN** — deine Weichen-Entscheidungen. Verbindlich, weil von dir gesetzt.
- 🟡 **KARTIERUNG** — strukturierte Zusammenfassung dessen, was ein Agent gesehen hat. Orientierung, NICHT hart verifiziert. Vor dem Bauen je Stelle nachprüfen.
- 🟠 **HART GEPRÜFT** — Analyse mit wörtlichen Belegen + Selbstkritik (die "gründlichen" Befunde). Stark, aber grep-gestützt wo 11k-Zeilen-Dateien im Spiel sind.

---

## 🟢 BEWIESEN (byte-genau / real verifiziert — darauf bauen)

| Was | Beleg |
|---|---|
| CSS-Auslagerung Kundenprofil (Scheibe 1) | md5-Vergleich gegen git-Original, zeichengleich (cbd92d8) |
| serialsOverlay-Partial (2a) | Byte-Vergleich identisch |
| halfDoneModal + doneHistoryModal (2b/2c) | md5 identisch, DOM-IDs verifiziert (d51ec8f) |
| Profil-Reduktion 23.145 → 19.470 Z. | wc -l, durchgehend Profil-200-Check |
| Wetter-Tool Customer-Falle-Fix | GET /get_weather/105 → 200, NewLeads gefunden |
| Bestellantrag customers→new_leads (4 Stellen) | Dropdown 0→52, Ticket-Liste 0→15, real geprüft (9475c0a) |
| Storno-Lücken (Schwäche 5+6) | real verifiziert, idempotent (d839d92, 7b550c6) |

→ Diese Arbeit ist gesichert. Der Zweifel muss sie nicht erfassen.

---

## 🔵 ENTSCHIEDEN (deine Weichen — verbindlich)

| Weiche | Entscheidung |
|---|---|
| Glossar | Kunde=new_leads, Objekt=lead_alternative_adds, Gewerk=lead_product_lists, Angebot=offers, Auftrag=deals. Kein physischer Rename. |
| Weiche 5 | Projekt = Bauphase des Auftrags (Objekt klammert). |
| Weiche 1 | 6 Phasen: Lead→Angebot→Auftrag→Montage→Abnahme→Abschluss. Drei Dimensionen: Phase/Zustand/Historie. |
| Prinzip | Phase → Aufgabe → Arbeitsschritt; je universell + gewerkespezifisch. |
| Weiche 6 (Teil) | Planner = Feld-Ausführungs-Wahrheit; kanban_lead_tasks = Büro-Wahrheit; customer_phase_lists ablösen. |
| Progressbar | Zeigt Feld-Ausführung (planner_items), Weg 2. |

→ Das sind deine Entscheidungen. Sie stehen, bis DU sie änderst.

---

## 🟠 HART GEPRÜFT (wörtliche Belege + Selbstkritik — stark, aber Restlücken benannt)

| Befund | Kernaussage | Restlücke (ehrlich) |
|---|---|---|
| struktur-systeme-verhaeltnis | task_phases = Heimat des Prinzips, an 6 Phasen anschlussfähig | — |
| kanban-ebenen-montage-planner-nuriva | 2 Kanban-Ebenen + Montage→Planner→Nuriva bestätigt | UI-Render-Details nicht Zeile-für-Zeile |
| architektur-bewertung-zweitmeinung | Rückfluss = Audit-Sackgasse; Kuratier-Schritt ist Feature | — |
| planner-kanban-zuordnung (2 Befunde) | KEINE nutzbare Zuordnung planner↔kanban → Weg 2 | grep-gestützt bei 11k-Controller; DB leer (keine Empirie) |
| progressbar-bau-befund | Balken rechnet client-seitig aus kanban_lead_tasks; Fix additiv safe | taskBuckets-Helper nicht ganz gelesen; Verifikation braucht Seed-Daten |

→ Diese sind so gründlich, wie Analyse sein kann. Wo "grep-gestützt/DB leer" steht, ist die Aussage stark aber nicht absolut — bei echtem Bau nochmal an der konkreten Stelle prüfen.

---

## 🟡 KARTIERUNG (Orientierung — NICHT hart verifiziert, vor Bau nachprüfen)

| Dokument | Was es ist | Verifikations-Status |
|---|---|---|
| crm-inventur 00-08 (8 Zonen) | Gesamt-Funktionslandkarte | Breite kartiert, Zahlen/Details NICHT einzeln geprüft |
| kundenprofil-architektur-bestandsaufnahme | Profil-Struktur, ~105 AJAX, %-Aufteilung | **Schnittplan-Teil praktisch bewiesen** (wir haben danach geschnitten, stimmte). Breite Zahlen (105 AJAX, 78% JS) = Schätzung, ungeprüft. |
| customer-model-falle-befund | Welche Stellen ins Leere laufen | Die 2 gefixten Stellen bewiesen; Rest kartiert, nicht einzeln geprüft |
| kalender/hierarchie/erfassung/begriffs-Befunde | frühe Landkarten | Orientierung, nie hart gegengeprüft |
| Gesamtkonzept / Fahrplan / Abarbeitungs-Reihenfolge | deine Steuerungsdokumente | bauen AUF den Kartierungen auf — so verlässlich wie diese |

→ **Das ist die Ebene, an der dein Zweifel berechtigt ansetzt.** Diese Dokumente sind gute Orientierung, aber keine harte Wahrheit. Wo eine davon zur Grundlage eines Baus wird, gilt: erst die konkrete Stelle hart prüfen (Pflicht-Stopp), dann bauen. Genau das haben wir bisher IMMER getan — deshalb konnte die Progressbar-Falle nicht durchrutschen.

---

## Fazit für den Zweifel

- **Was gebaut/entschieden ist (🟢🔵): sicher.** Der Zweifel muss es nicht erfassen.
- **Was hart geprüft ist (🟠): stark**, mit ehrlich benannten Restlücken.
- **Was reine Kartierung ist (🟡): Orientierung, nicht Wahrheit** — hier ist Nachprüfen vor jedem Bau richtig und war immer Teil des Vorgehens.
- **Der eingebaute Schutz:** Vor JEDEM Bau kam ein Pflicht-Stopp mit Verifikation der konkreten Stelle. Deshalb steht die Bau-Arbeit auf 🟢, nicht auf 🟡. Die Kartierung musste nie "stimmen" — sie zeigt nur, WO man genauer hinschaut.

*Ehrliche Sortierung. Byte-Beweise und deine Entscheidungen sind fest. Kartierungen sind Landkarten, keine Fotos. Beim nächsten Mal: die 🟡-Bereiche, bei denen das Bauchgefühl am größten ist, gezielt hart nachprüfen — mit frischem Kopf.*

---

## Nachtrag (nach dem Schreiben ergänzt): Kundenprofil-Kartierung ist inzwischen HART verifiziert

Die oben unter 🟡 als „Schätzung, ungeprüft" eingestufte **Kundenprofil-Architektur-Kartierung** wurde danach hart gegengeprüft → **`kundenprofil-kartierung-hart-verifiziert.md`**. Ergebnis: **GRÜNDLICH** — die überprüfbaren Kernzahlen sind exakt (Original 23.145 Z.; Sektionen 12/3616/4969; %-Split 16/6/78; 25 `route()` im JS; `{{ $item }}`=0; `NewLeadsController@view`→`NewLeads`; 4/4 Block-Ranges; Doppel-ID; 5/5 Partials sauber). Kleine Korrekturen: aktuelle Größe **19.409** (nicht 19.470 — Scheibe 2.3 kam danach); „~105 AJAX" = real **103** Call-Sites; „JS Blade-frei" nicht 100 % (3 `{{ $emp }}` im JS). → Diese Kartierung rückt damit von 🟡 nach **hart-verifiziert**; die anderen 🟡-Dokumente bleiben Orientierung.
