# Architektur-Entscheidungen — die fünf Weichenstellungen für tickets Kernprozess

**Zweck:** Die Workflow-Analyse (`docs/workflow-analyse.md`) hat fünf Grundsatzfragen offengelegt, die festlegen, wie tickets Kernprozess *grundsätzlich* funktionieren soll. Das sind **keine Bugs und keine schnellen Fixes** — es sind unternehmerische Entscheidungen, die das Fundament für alles Weitere bilden (Cockpit, Controlling, Buchhaltung, und auch die kleineren Workflow-Fixes hängen daran).

**Wie dieses Dokument zu nutzen ist:** Geh die fünf Fragen in Ruhe durch. Zu jeder gibt es den Hintergrund (warum es die Frage überhaupt gibt), die Optionen, eine Empfehlung und den Hinweis, ob du das allein entscheiden kannst oder ob der Steuerberater mitreden muss. Notiere zu jeder eine Antwort — die wird dann zur Vorgabe für die spätere Umsetzung.

**Wichtig:** Erst wenn diese fünf Weichen gestellt sind, sollten die größeren Umbauten (v.a. die einheitliche Status-Führung) gebaut werden. Sonst baut man etwas, das eine dieser Entscheidungen später wieder umwirft.

---

## Frage 1 — Was ist die verbindliche Quelle für „wo steht dieser Vorgang"?

### Hintergrund
Heute wird der Fortschritt eines Vorgangs an *vielen* Stellen gleichzeitig geführt. Allein die Lead-Liste hat rund elf Status-/Stufen-Felder, auf die zwölf verschiedene Programmteile schreiben. Der Auftrag hat fünf parallele Status-Felder. Es gibt keine einzige Stelle, die verbindlich sagt: „Dieser Vorgang steht *hier* im Prozess." Belegt ist das bereits an einem echten Lead, bei dem zwei Statusfelder *unterschiedliche* Werte trugen (`status='deal'`, `stage='accepted'`).

### Warum das wichtig ist
Solange es keine eine Wahrheit gibt, kann der angezeigte Stand je nachdem kippen, welcher Programmteil zuletzt geschrieben hat. Jede Auswertung, jedes Kanban, jedes Cockpit baut auf einer wackeligen Grundlage. Das ist die *strukturelle Wurzel* mehrerer anderer Schwächen.

### Optionen
- **(A) Ein führendes Statusfeld bestimmen.** Eines der vorhandenen Felder (z.B. `stage` in der Lead-Liste) wird zur verbindlichen Wahrheit erklärt; alle anderen werden davon abgeleitet oder abgeschafft. Alle Schreibpfade gehen künftig über *eine* zentrale Stelle.
- **(B) So lassen, aber synchron halten.** Die mehreren Felder bleiben, werden aber bei jeder Änderung konsistent zusammen geführt (wie es der heutige Storno-Fix für `status`+`stage` schon tut). Weniger Umbau, aber die Komplexität bleibt.
- **(C) Nichts tun.** Risiko bleibt bestehen.

### Empfehlung
**(A)** als Zielbild — ein führendes Feld, eine zentrale Schreibstelle. Das ist die sauberste Lösung und die Voraussetzung für ein verlässliches Cockpit. Aber: Das ist ein *größerer* Umbau, der die zwölf Schreibpfade berührt. Er sollte bewusst geplant und einzeln umgesetzt werden, nicht spontan. Kurzfristig ist (B) — konsistent zusammenführen — die pragmatische Brücke (und teilweise schon im Gange).

### Wer entscheidet
Du (technisch-konzeptionell). Kein Steuerberater nötig.

### ✅ ENTSCHIEDEN (ratifiziert von Yama, gemeinsam erarbeitet): Variante A + saubere Dreiteilung
**Ein Vorgang hat DREI getrennte Dimensionen** (statt der heutigen ~11 vermischten Felder). Das ist die Auflösung von Schwäche 1:

**Dimension 1 — PHASE (wo im Prozess):** eine einzige lineare Achse, EINE verbindliche Quelle. Ein Vorgang ist zu jeder Zeit an genau EINER Phase. Die finale, verbindliche Phasen-Liste (6 Phasen) — Begriffe bewusst nach dem gewählt, was das Team im Alltag sagt (Gewöhnung vor Theorie):
1. **Lead** (Vorgang frisch reingekommen; Team-Begriff, ersetzt „Anfrage")
2. **Angebot** (Beratung + Angebotserstellung zusammen; benannt nach dem Ergebnis der Phase. Zustand „Angebot draußen" beim Warten auf die Kundenentscheidung. NICHT „Verkauf" genannt, weil hier noch nicht verkauft, sondern beraten/angeboten wird — der Verkauf ist erst die Zusage = Übergang zu Auftrag.)
3. **Auftrag** (entsteht bei Zusage; die Zusage/Absage ist der ÜBERGANG, keine eigene Phase — Absage → Zustand „verloren")
4. **Montage** (die Ausführung/Leistungserbringung; Team-Begriff. Hinweis: fachlich wäre „Ausführung" breiter über alle Gewerke, aber „Montage" ist der eingespielte Begriff des Teams — Gewöhnung vor Theorie, bewusst so gewählt.)
5. **Abnahme** (EIGENE Phase — rechtlich/finanziell scharf: Gewährleistungsbeginn, Schlussrechnung fällig, oft nicht glatt: Mängel/Nachbesserung/zweiter Termin. Der Zustand „fertig, aber noch nicht abgenommen" muss sichtbar bleiben.)
6. **Abschluss** (darunter als AUFGABEN, nicht als eigene Phasen: Schlussrechnung, Nachkalkulation/Auswertung des Einzelprojekts, Dokumentation, Archivierung)

**Dimension 2 — ZUSTAND (wie es an der Phase läuft):** aktiv · pausiert · zurückgestellt · Wiedervorlage (mit Datum) · gewonnen · verloren (mit Grund) · archiviert. Quer zur Phase. „Archiviert" ist ein ZUSTAND, keine Phase — der Kunde/Vorgang wird archiviert, wenn abgeschlossen (Kunde archiviert, wenn ALLE seine Vorgänge abgeschlossen sind).

**Dimension 3 — HISTORIE (was passiert ist):** Notizen, Berichte je Phase, Kommunikation, Wiedervorlage-Termine, Dokumente. Keine Status, sondern Dokumentation.

**Wichtige Abgrenzungen (bewusst entschieden):**
- Qualifizierung ist KEINE eigene Phase (mit Beratung verschmolzen — passiert im Alltag zusammen).
- Entscheidung/Zusage ist KEINE Phase (Übergang Beratung→Auftrag bzw. → verloren).
- Archiv ist KEINE Phase (Zustand nach Abschluss).
- Nachkalkulation/Auswertung des EINZELprojekts = Aufgabe in der Abschluss-Phase. Die Auswertung ÜBER ALLE Projekte (Muster, Profitabilität je Gewerk/Abteilung) = Controlling/Cockpit, NICHT die Phasen-Achse.
- Leitprinzip: lieber wenige, klar unterscheidbare Phasen als viele verwaschene. Eine Phase verdient nur, wer ein echter Wartepunkt mit eigener Arbeit/Konsequenz ist.

**Umsetzung (Variante A):** EINE führende Quelle je Dimension, eine zentrale Schreibstelle. Das ist ein größerer Umbau (berührt die ~12 Schreibpfade) → bewusst geplant, einzeln, in Etappe 4 des Fahrplans (NACH den übrigen Weichen). Kurzfristige Brücke bleibt (B: konsistent zusammenführen, wie beim Storno-Fix), bis der volle Umbau dran ist.

**Noch NICHT gebaut.** Dies ist die Weichen-Entscheidung — das Zielbild der Statusführung. Der Bau ist Fahrplan-Etappe 4.1.

---

## Frage 2 — Soll „Angebot angenommen" Pflicht sein, bevor ein Auftrag entsteht?

### Hintergrund
Heute kann ein Lead in die Stufe „Auftrag" geschoben werden, *ohne* dass ein Angebot angenommen wurde. Das System verhindert das nicht — es *protokolliert nur*, dass der Schritt übersprungen wurde (ein Feld `moved_without_offer_acceptance`). Die Kausalität „erst Angebot annehmen, dann Auftrag" ist also freiwillig, nicht erzwungen.

### Warum das wichtig ist
Das ist eine echte Frage danach, wie *streng* dein Prozess sein soll. Manche Betriebe wollen, dass ein Auftrag *nur* aus einem angenommenen Angebot entstehen kann (saubere Kette, keine Aufträge „aus dem Nichts"). Andere brauchen die Flexibilität, auch mal ohne formelles Angebot direkt einen Auftrag anzulegen (Stammkunde, Kleinauftrag, Sonderfall).

### Optionen
- **(A) Pflicht.** Kein Auftrag ohne angenommenes Angebot. Das System blockiert den Schritt.
- **(B) Flexibel mit Warnung.** Wie heute — erlaubt, aber protokolliert/markiert, sodass man die Ausnahmen sieht.
- **(C) Frei.** Keine Einschränkung.

### Empfehlung
Das hängt von *deinem* Geschäft ab — ich rate hier nicht, sondern stelle die Frage scharf: **Kommt es bei euch regelmäßig vor, dass ein Auftrag ohne formelles Angebot entsteht (Stammkunde, Kleinstauftrag)?** Wenn ja → (B), die heutige flexible Variante ist richtig, evtl. mit besserer Sichtbarkeit der Ausnahmen. Wenn nein, und ihr wollt saubere Ketten erzwingen → (A). Für die spätere Buchhaltung/Controlling ist (A) oder (B mit guter Markierung) klar besser als (C).

### Wer entscheidet
Du (Geschäftsregel — hängt an eurem Vertriebsprozess).

---

## Frage 3 — Welches Rechnungssystem gilt: `deal_invoices` oder `invoices`?

### Hintergrund
Es gibt zwei parallele Rechnungs-Tabellen: `deal_invoices` (an den Auftrag gebunden) und die generische `invoices` (nur an Kunde/Objekt gebunden, ohne Auftragsbezug). Aktuell liegen die echten Umsätze (rund 204.000 € in den Demo-Daten) in `invoices`, während `deal_invoices` leer ist. Beide existieren nebeneinander.

### Warum das wichtig ist
Das ist die **kritischste** der fünf Fragen für deine Finanzauswertungen. Solange es zwei „Wahrheiten" über den Umsatz gibt, kann derselbe Umsatz doppelt gezählt werden oder durch die Lücke fallen. Jedes Cockpit, jedes Controlling, jede Buchhaltung braucht *eine* eindeutige Umsatzquelle. Ohne diese Entscheidung ist „Umsatz je Abteilung" nicht verlässlich berechenbar.

### Optionen
- **(A) `invoices` (generisch) ist führend.** Die auftragsgebundene `deal_invoices` wird aufgegeben oder in `invoices` integriert. Vorteil: dort liegen schon die echten Daten. Nachteil: `invoices` hat heute keinen sauberen Auftrags-/Abteilungsbezug — der müsste ergänzt werden.
- **(B) `deal_invoices` (auftragsgebunden) ist führend.** Sauberer Auftrags→Rechnungs-Bezug, gut fürs Controlling. Nachteil: die echten Daten liegen heute woanders; Migration nötig.
- **(C) Beide behalten, klar abgegrenzt.** Z.B. `deal_invoices` für auftragsbezogene, `invoices` für freie Rechnungen — mit klarer Regel, dass Umsatz nie doppelt entsteht.

### Empfehlung
Das ist **die eine Frage, die du NICHT allein entscheiden solltest** — sie gehört mit dem Steuerberater geklärt, weil sie direkt mit der Buchhaltung und der Umsatzdefinition zusammenhängt. Sie überschneidet sich mit den Steuerberater-Fragen (dort Frage 8: „welche Umsatzquelle gilt buchhalterisch als führend?"). Technisch tendiere ich zu *einer* führenden Quelle mit sauberem Auftrags- und Abteilungsbezug (Richtung B oder ein um Auftragsbezug ergänztes A), aber die buchhalterische Hoheit hat der Steuerberater.

### Wer entscheidet
Du **gemeinsam mit dem Steuerberater**. Kritisch.

---

## Frage 4 — Was passiert beim Auftrags-Storno mit Umsatz und Lead-Stufe?

### Hintergrund
**Diese Frage ist heute bereits zu großen Teilen beantwortet und umgesetzt** (Schwäche 5+6 aus der Workflow-Analyse):
- Lead-Stufe wird beim Storno auf den Vorzustand zurückgesetzt (status+stage gemeinsam).
- Offene Rechnungen werden als `storniert` markiert.
- Bezahlte Rechnungen werden **nicht** gelöscht, sondern mit `storniert_bezahlt_pruefen` markiert plus Warnung — weil ein bezahlter Umsatz buchhalterisch nicht verschwinden darf.

### Was noch offen ist
Eine reine Bestätigungs-/Folgefrage: **Wie soll mit den `storniert_bezahlt_pruefen`-Fällen weiter umgegangen werden?** Wenn ein Auftrag mit bereits bezahlter Rechnung storniert wird — was ist der reale Geschäftsprozess? Rückzahlung an den Kunden? Gutschrift? Umbuchung auf einen anderen Auftrag? Das ist heute bewusst als „menschliche Prüfung nötig" markiert; die *Regel*, was dann passiert, ist eine buchhalterische.

### Empfehlung
Die technische Rückabwicklung ist sauber gelöst. Die offene Frage — wie bezahlte stornierte Rechnungen buchhalterisch behandelt werden — gehört zum Steuerberater-Gespräch. Bis dahin ist die Markierung+Warnung genau richtig: nichts Falsches automatisch tun, sondern einen Menschen entscheiden lassen.

### Wer entscheidet
Technik: erledigt. Buchhalterische Folgeregel: **mit dem Steuerberater**.

---

## Frage 5 — Ist „Projekt" ein eigener Vorgang oder eine Phase des Auftrags?

### Hintergrund
Heute sind `deals` (Auftrag) und `projects` (Projekt) **zwei getrennte Datensätze** ohne direkte Verbindung — das Projekt hat keinen Verweis auf den Auftrag (`deal_id` fehlt). Sie hängen nur lose über das Kunde-Produkt-Adresse-Tripel zusammen. Der Auftrag trägt zwar ein eigenes Feld `project_status`, aber der eigentliche Projekt-Datensatz wird separat über den Planer geführt.

### Warum das wichtig ist
Das bestimmt, wie dein System „Auftrag" und „Projekt" denkt. Sind das zwei Sichten auf *dieselbe Sache* (ein Auftrag, der in die Umsetzung geht = ein Projekt)? Oder sind es wirklich *zwei verschiedene Dinge* (ein Auftrag kann mehrere Projekte umfassen, oder ein Projekt mehrere Aufträge)? Davon hängt ab, ob man sie verketten (ein Auftrag erzeugt sein Projekt) oder bewusst getrennt lassen sollte.

### Optionen
- **(A) Projekt = Phase des Auftrags.** Ein Auftrag, der in die Umsetzung geht, *ist* das Projekt. Dann sollte das Projekt fest am Auftrag hängen (`deal_id`), und ein Auftrag erzeugt automatisch sein Projekt.
- **(B) Projekt = eigener Vorgang.** Projekte sind unabhängige Einheiten, die mehrere Aufträge bündeln können (oder umgekehrt). Dann bleibt die Trennung, aber die Verbindung sollte sauber definiert werden (welcher Auftrag gehört zu welchem Projekt).
- **(C) So lassen.** Lose über das Tripel verbunden — mit dem Risiko, dass Auftrag und Projekt auseinanderdriften.

### Empfehlung
Das hängt davon ab, wie ihr real arbeitet — ich stelle die Frage scharf: **Ist bei euch ein Auftrag praktisch immer auch genau ein Projekt (1:1)? Oder fasst ihr mehrere Aufträge zu einem Projekt zusammen (z.B. eine Komplettsanierung mit mehreren Gewerken = ein Projekt, mehrere Aufträge)?** Bei einem Vollsanierer mit mehreren Gewerken am selben Objekt klingt eher (B) realistisch — ein Objekt/Projekt, mehrere Gewerke-Aufträge. Das passt auch zum objekt-zentrierten Zielbild aus `zielbild-objekt-zentriertes-crm.md`. Aber bestätige es anhand eurer Praxis. (C) — so lassen — würde ich nicht empfehlen, weil das Auseinanderdriften ein echtes Risiko ist.

### Wer entscheidet
Du (Geschäftsregel — hängt daran, wie ihr Aufträge und Projekte real strukturiert). Verbindet sich eng mit dem objekt-zentrierten Zielbild.

### ✅ ENTSCHIEDEN (ratifiziert von Yama, 2026-06-30): Variante A
**Das Objekt ist die Klammer, nicht das Projekt.** „Projekt" wird *kein* eigenständiger Vorgang über den Aufträgen.
- **Die Klammer über den Gewerken ist das Objekt** (Kunde → Objekt → mehrere Gewerke-Aufträge). Diese Klammer existiert bereits als saubere FK-Kette und wird genutzt.
- Die `projects`-Tabelle wird damit zur **Bauphasen-/Ausführungs-Sicht eines Auftrags** (Projektleiter, Montagestart, Fortschritt) — *nicht* zu einer eigenen Ebene über den Aufträgen.
- **Merksatz (verbindlich):** *Das Objekt klammert, der Auftrag führt aus, „Projekt" ist nur die Bauphase des Auftrags — keine eigene Ebene darüber.*
- **Wirkung:** Löst die Doppeldeutigkeit von „Projekt" auf (vorher: Gewerk-am-Objekt *vs.* `projects`-Tabelle). Künftig meint „Projekt" eindeutig die Bauphase eines Auftrags.
- **Noch NICHT gebaut.** Dies ist die Weichen-Entscheidung. Die technische Umsetzung (z.B. `projects` sauber an `deals` koppeln) kommt erst in der Bau-Phase, nach den übrigen Weichen — und berührt sich mit Weiche 1 (Status) und Weiche 3 (Rechnung), die zuerst stehen müssen.

---

## Zusammenfassung — wer entscheidet was

| Frage | Thema | Entscheider | Dringlichkeit |
|---|---|---|---|
| 1 | Verbindliche Statusquelle | Du (konzeptionell) | hoch — Fundament |
| 2 | Angebot-Annahme Pflicht? | Du (Vertriebsprozess) | mittel |
| 3 | Welches Rechnungssystem | **Du + Steuerberater** | **kritisch** |
| 4 | Storno-Behandlung | Technik erledigt; Folgeregel + Steuerberater | gering (Rest) |
| 5 | Projekt eigen oder Auftragsphase | Du (Arbeitsweise) | mittel |

## Die zwei, die zum Steuerberater gehören
Frage 3 (Rechnungssystem) und die Folgeregel zu Frage 4 (bezahlte stornierte Rechnungen) überschneiden sich mit dem Steuerberater-Fragenkatalog. Nimm sie ins selbe Gespräch mit.

## Die drei, die DU durchdenken kannst
Frage 1 (Statusquelle), Frage 2 (Angebot-Pflicht), Frage 5 (Projekt). Diese hängen an deiner Arbeitsweise — hier hilft es, ehrlich zu beschreiben, wie euer Prozess *real* läuft (nicht wie er idealerweise sein sollte), dann ergibt sich die Antwort meist von selbst.

---

## Wichtiger Grundsatz

Diese fünf Entscheidungen sind das Fundament. Erst wenn sie getroffen sind, sollten die größeren Umbauten gebaut werden — insbesondere die einheitliche Statusführung (Frage 1) und die Zusammenführung der Rechnungssysteme (Frage 3). Vorher einzelne Teile zu bauen, riskiert, dass eine spätere Grundsatzentscheidung die Arbeit wieder umwirft.

Die heutige Storno-Reparatur (Frage 4) war bewusst eine *abgegrenzte* Lücke, die unabhängig von den großen Fragen gefixt werden konnte. Die anderen vier sind echte Weichenstellungen — sie verdienen Nachdenken, nicht schnelle Umsetzung.

---

*Grundlage: `docs/workflow-analyse.md`. Dieses Dokument stellt die Entscheidungen — es trifft sie nicht. Die verbindlichen Antworten kommen von dir (und beim Rechnungssystem vom Steuerberater).*

---

# WEICHE 6 — Aufgaben-/Ausführungs-Architektur (Planner / Kanban / Phasen)

**Status: ANALYSIERT (zwei Fachmeinungen), NOCH NICHT entschieden.** Grundlage: `struktur-systeme-verhaeltnis-befund.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `architektur-bewertung-zweitmeinung.md`. Diese Weiche gehört zur Planner-Detailinventur (Fahrplan Etappe 5.1) und hängt an Weiche 1.

## Befundlage (belegt)
Das CRM hat drei/vier Systeme, die dieselben `phase_activities` instanziieren:
- **Lead-Kanban** (`lead_stages` × `lead_stage_sub_stages` auf `lead_product_lists`) = Pipeline-Position (die 6 Phasen + Unterphasen). Zweistufig, sauber, dynamisch pflegbar.
- **`kanban_lead_tasks`** = Büro-Aufgaben je Gewerk (Planung/Kuratierung), Instanz von Template A.
- **`planner_items`** = Feld-/Montage-Ausführung, **einzige Nuriva-Anbindung**, Aggregator (phase_activity + Termine + Tickets + personal_task + master_set), stage-gefiltert.
- **`customer_phase_lists`** = ältere Fortschrittsliste, **fast dormant** (4 Dateien app/, 0 Views, 1 Schreibstelle).

## Die zwei Fachmeinungen — Konsens
1. **Planner wird die Ausführungs-Wahrheit** — zwingend, weil Nuriva NUR `planner_items` liest (jede Alternative müsste Nuriva umbauen). Planner ist einziges stage-gefiltertes, aggregierendes, rückschreibendes System.
2. **`customer_phase_lists` wird abgelöst** — dormant, billig, risikoarm.
3. **„Eine Wahrheit" heißt pro Lebenszyklus-Ebene, nicht eine Tabelle:** Feld-Ausführung = `planner_items`; Büro-Planung = `kanban_lead_tasks` (bleibt eigene, legitime Ebene); `customer_phase_lists` = weg.

## Der eigentliche Fehler (Zweitmeinung-Korrektur, wichtig)
**Der Status-Rückfluss ist eine Audit-Sackgasse:** Wenn der Monteur eine Aufgabe abschließt (Foto, erledigt), landet der Status in `customer_histories` (Protokoll) — **NICHT zurück auf der Büro-Kanban-Karte oder der Phasen-Instanz, wo geplant wurde.** → Das Büro sieht die Feld-Erledigung nicht dort, wo es geplant hat. Das ist die gefährlichste Alltags-Falle (schärfer als zunächst angenommen — es ist die RÜCK-Richtung, nicht die Hin-Richtung).

## Der manuelle Büro→Planner-Schritt ist ein FEATURE, kein Bug
Nicht jede Büro-Aufgabe („Kunde anrufen", „Unterlagen prüfen") gehört aufs Monteur-Tablet. Das Büro kuratiert bewusst, was der Monteur sieht. → NICHT automatisch alles weiterleiten (würde Nuriva mit Rauschen fluten); stattdessen den Kuratier-Schritt (`storeProjectWorkItem`) 1-Klick-reibungsarm machen.

## Empfohlene Bereinigungs-Reihenfolge (zu entscheiden, NICHT umgesetzt)
1. **ZUERST: Weiche 1 (Statusquelle) entscheiden** — ohne Status-Vertrag ist jede Reconciliation Raten. Blockiert alles Weitere.
2. **Rück-Richtung schließen:** Feld-Status (Planner) → Büro-Kanban-Karte + Phasen-Instanz (nicht nur Audit). Höchster Alltags-ROI, geringes Risiko.
3. **`customer_phase_lists` ablösen** (dormant).
4. **Hin-Richtung entschlacken:** Kuratier-Schritt zum 1-Klick-„an Monteur geben" (Kuratierung behalten, Reibung raus).
5. **ZULETZT: Felder (B) an `phase_activities` (A) koppeln** — Erweiterung, kein Bruch, niedrige Dringlichkeit.

## Nebenbefund
**Stage-Tabellen-Wildwuchs:** mindestens 5 Stage-Tabellen (`stages`, `customer_stages`, `phase_stages`, `offer_kanban_stages`, `lead_stages`) — dasselbe „Danebenbau statt Ablösen"-Muster. Aufräum-Kandidat, später.

## Zu entscheidende Design-Fragen (für Yama, frischer Kopf)
1. Bestätigung: Planner = Feld-Ausführungs-Wahrheit, `kanban_lead_tasks` = Büro-Wahrheit, `customer_phase_lists` weg?
2. Rück-Richtung: Soll Feld-Status automatisch auf Büro-Karte + Phasen-Instanz zurücklaufen? (Empfehlung: ja, höchste Prio nach Weiche 1.)
3. Auto-Plan bei Montage-Eintritt, oder manuell wie heute?
4. Felder an Schritte koppeln — ja, aber wann (niedrige Dringlichkeit)?

---

## WEICHE 6 — Nachtrag: Progressbar-Fix hart geprüft (Ergebnis der Detailkette)

**Stand: ANALYSE ABGESCHLOSSEN, Bau offen (hängt an Weiche 1 + frischer Bau-Session).**

### Der belegte Alltagsfehler
Der Fortschrittsbalken im Kundenprofil (`customerKanbanProgressBar`) rechnet aus `kanban_lead_tasks` (Büro-Karten, status='done'). Der Monteur-Abschluss (`source_type='phase_activity'`) schreibt aber NICHT in `kanban_lead_tasks` — nur in `planner_items` + `customer_reports` + `customer_histories`. → **Der Balken bewegt sich nicht, wenn der Monteur draußen fertig wird.** Hart belegt.

### Warum der naheliegende Fix NICHT sicher ist (hart geprüft, wörtliche Code-Belege)
Vorschlag war: beim Abschluss die zugehörige kanban-Karte auf 'done' setzen. Prüfung ergab: **keine eindeutige Zuordnung** planner_item(phase_activity) ↔ kanban_lead_tasks-Karte:
- Kein direkter FK (weder `planner_item_id` auf kanban noch umgekehrt).
- Kein Unique-Constraint auf `(lead_product_list_id, phase_activity_id)`.
- `storeFromTemplate` dedupliziert nicht (`create()` ohne Check) → **mehrere Karten je Aufgabe möglich**.
- Kein bestehender Query, der die Karte auflöst.
→ Der Fix würde raten, welche Karte „die richtige" ist. **Nicht sicher baubar.**

### Die zwei möglichen Wege (Bau-Entscheidung, hängt an Weiche 1)
1. **Eindeutigkeit schaffen:** direkter Link (z.B. `planner_items.meta.kanban_lead_task_id`) ODER Unique-Index + `firstOrCreate` in `storeFromTemplate`. Dann wird der einfache Fix sicher.
2. **Progressbar-Quelle ändern:** Balken aus erledigten `planner_items`/`phase_activities` rechnen statt aus `kanban_lead_tasks`. Dann bewegt ihn jeder Abschluss automatisch. (Hängt an Weiche 1: welche Tabelle ist die Fortschritts-Wahrheit.)

### Positiver Prozess-Hinweis
Die Detailkette hat einen unsicheren Fix VERHINDERT, bevor er gebaut wurde. Die harte Prüfung (wörtliche Code-Belege, Gelesen/Nicht-gelesen-Liste, Selbstkritik) hat gezeigt: der erste Vorschlag stand auf ungeprüfter Annahme. Lehre: Bau-Vorschläge zu Aufgaben-/Ausführungs-Code IMMER auf Zuordnungs-Eindeutigkeit hart prüfen, bevor gebaut wird.

---

## WEICHE 6 — Nachtrag 2: Zuordnung final geklärt, Weg 2 bestätigt

**Ergebnis der vollständigen Prüfkette (dreifach belegt, DB leer):**
Es gibt KEINE nutzbare Zuordnung planner_item(phase_activity) ↔ kanban_lead_tasks-Karte:
- Kein FK, kein meta-Link (planner-Sync setzt gar kein meta; kanban.meta enthält nur Freitext-Strings ohne ID), kein Verknüpfungs-Query (2 unabhängige Greps leer), keine Daten zum empirischen Gegenbeweis (lokale DB = 0 Zeilen).
- Die einzige echte planner↔kanban-Kopplung ist der `kanban_task`-Identitäts-Link (source_id = kanban.id) — greift NICHT für Montage-Abschlüsse (die kommen als `phase_activity`).

**→ ENTSCHEIDUNG FÜR DEN BAU (wenn Progressbar-Fix drankommt): WEG 2.**
Den Profil-Progressbar aus erledigten `planner_items` (status='done') rechnen statt aus `kanban_lead_tasks`. Braucht kein Cross-Table-Matching; jeder Monteur-Abschluss bewegt den Balken automatisch, weil planner_items bereits die Feld-Abschluss-Wahrheit ist (status/done_at/done_by_employee_id).
Der einfache Fix (kanban-Karte auf done) wäre NUR nach einem Umbau möglich (echten Link schaffen: planner_items.meta.kanban_lead_task_id beim Sync ODER Unique+firstOrCreate in storeFromTemplate) — das ist ein Umbau, kein reiner Fix. Verworfen.

**Die eine offene DESIGN-Frage (Yama entscheidet, hängt an Weiche 1):**
Soll der Profil-Progressbar die FELD-Ausführung (planner_items) oder die BÜRO-Planung (kanban_lead_tasks) als Fortschritts-Wahrheit zeigen? Empfehlung: Feld-Ausführung (planner_items), weil das der reale Baufortschritt ist, den der Monteur meldet. Aber bewusst zu entscheiden.

**Status:** Analyse KOMPLETT abgeschlossen. Nächster Schritt ist BAU (Weg 2) — gehört in eine frische Bau-Session, nach Bestätigung der Design-Frage. Kein weiterer Analyse-Bedarf.

### Vollständige Befund-Kette zu Weiche 6 (Reihenfolge)
1. `struktur-systeme-verhaeltnis-befund.md` — drei Struktur-Systeme, task_phases = Heimat des Prinzips
2. `kanban-ebenen-montage-planner-nuriva-befund.md` — zwei Kanban-Ebenen + Montage→Planner→Nuriva bestätigt
3. `architektur-bewertung-zweitmeinung.md` — Berater-Bewertung geprüft, Rückfluss = Audit-Sackgasse
4. `monteur-rueckfluss-vier-ziele-befund.md` — 4 Ziele: Historie ✅, Tagesbericht halb, Progressbar gebrochen, erledigt halb
5. `planner-kanban-zuordnung-hart-geprueft.md` — keine eindeutige Zuordnung (Schema)
6. `planner-kanban-meta-daten-geprueft.md` — dreifach bestätigt: kein Link → Weg 2

---

## WEICHE 6 — Design-Frage ENTSCHIEDEN (Yama, ratifiziert)

**Der Profil-Progressbar zeigt die FELD-AUSFÜHRUNG (erledigte planner_items), nicht die Büro-Planung.**
Begründung (Yamas eigene Worte): der Fortschritt des Projekts soll vom Monteur-Abschluss beeinflusst werden — "das ist doch Sinn und Zweck". Der Balken soll den realen Baufortschritt zeigen (was draußen erledigt wurde), nicht was das Büro geplant hat.
→ Damit ist Weg 2 vollständig baureif: Progressbar aus planner_items (status='done') je Gewerk/Plan rechnen. Bau gehört in eine (frische) Bau-Session mit vollem Pflicht-Stopp.

---

## WEICHE 6 — Progressbar: Bau-Entscheidung (Weg A, ehrlicher Anzahl-Balken)

**Befund `progressbar-zeitgewichtung-geprueft.md` ergab:** Zeitgewichtung heute NICHT ehrlich baubar (phase_activities.duration ist time-Feld '00:00:0X', Sync-Cast (int)→0→Default 60 → alle Schritte wögen 60 = verkleidete Anzahl-Zählung). Soll-Ist NICHT baubar (planned_end_at beim Sync = null → kein Soll). Beide brauchen zuerst Daten-/Planungs-Disziplin.

**ENTSCHEIDUNG: Heute Weg A — ehrlicher ANZAHL-Balken.**
- Balken = erledigte / gesamte Aufgaben als Prozent (nach Anzahl, NICHT zeitgewichtet — ehrlich, keine Scheingenauigkeit).
- Zähler: "X von Y" + "Z offen".
- Quelle: planner_items, nur Montage-Plan, nur source_type='phase_activity', cancelled ausgeschlossen.
- Status-Kübel: erledigt=done, offen=nicht-done-nicht-cancelled (inkl. scheduled/in_progress/paused), gesamt=alle außer cancelled.
- Bewegt sich beim Monteur-Abschluss (Kernziel erfüllt). Fundament, auf das später Zeitgewichtung aufsetzt (nur Rechnung tauschen).

**AUFGESCHOBEN (nächste Schritte, brauchen Daten-Fix zuerst):**
1. Zeitgewichtung — braucht: phase_activities.duration als echte Integer-Minuten + Sync-Konvertierung time→Minuten reparieren.
2. Soll-Ist-Verzug (Kalender-Verzug am ehesten machbar) — braucht: planned_end_at konsequent gefüllt (auto aus Start+Dauer ableiten?).
3. Aufwand-Verzug/Puffer — braucht zusätzlich Ist-Timer-Nutzung (started_at/stopped_at durch Nuriva — NICHT VERIFIZIERT).

**6 Design-Entscheidungen offen (für später):** welche Ebene trägt Zeit; was bei fehlender Dauer; Dauer-Format fixen; planned_end_at auto ableiten?; Ist-Timer verpflichtend?; Status-Kübel bestätigt (paused=offen, cancelled raus).

---

## WEICHE 6 — Progressbar Weg A: GEBAUT & VERIFIZIERT (Commit f52ab10)

**Status: 🟢 FERTIG, Seed-verifiziert. Erster Produktiv-Bau nach der Analysekette.**

Geändert (additiv, 2 Dateien):
- KanbanLeadTaskController::context() liefert zusätzlich field_progress (tasks-Liste unverändert daneben). Neue montageFieldProgress(): planner_plans stage='montage' → planner_items source_type='phase_activity', nach Anzahl gezählt. Guards gegen kein-Plan (→0%) und fehlende Tabellen.
- Neue normalizePlannerItemStatus() spiegelt done-/cancel-Aliasse → schließt das Restrisiko roh geschriebener Status.
- customer_profile.blade.php: Balken/Prozent/"X von Y"/"Y Aufgaben" + neues "Z offen" aus field_progress; Titel "Montage-Fortschritt"; Listen-Header bleiben kanban (konsistent mit ihren Listen). Option α.

Verifikation (Seed, echter Endpoint context/53, DB war leer):
- 2 done/3 offen/1 storniert → 40%, storniert korrekt aus gesamt (5 statt 6) ✅
- 1 Item → done → Balken bewegt sich 40%→60% ✅
- Gewerk ohne Montage-Plan → 0%, HTTP 200, kein Crash ✅
- tasks-Liste weiter vorhanden (additiv) ✅
- php -l grün, Blade kompiliert, Seed restlos entfernt.

Der Alias-Test (storniert korrekt ausgeschlossen) hat konkret einen falsch rechnenden Balken verhindert — die einfache Query status<>'cancelled' hätte storniert als offen mitgezählt.

Unberührt (bestätigt): summaries(), api.php, Nuriva, Sync, planner_items-Schreibpfade.

Akzeptierter Mismatch (bewusst): Balken zeigt Feld-Fortschritt, Aufgabenliste im Drawer bleibt Büro (kanban).
NICHT an Produktivdaten belegt (DB leer) — nur Seed. Zeitgewichtung + Soll-Ist bleiben aufgeschoben (brauchen Daten-Disziplin).

---

## KUNDENPROFIL-STRUKTUR — Bestandsaufnahme (Ist/Soll-Abgleich)

**Status: kartiert (Blade-belegt), JS-Laufzeit NICHT verifiziert. Kein Neu-Design — Grundlage für spätere Design-Entscheidung.** Volltext: `kundenprofil-struktur-bestandsaufnahme.md`.
Quelle der Nav: layouts/profile.blade.php (12.352 Z.), via @include aus customer_profile.blade.php:32.

**Stärken (schon Soll-konform):**
- Hierarchie Kunde→Objekt→Gewerk verschachtelt sichtbar (Objekt-Galerie mit Karte/Street-View → Gewerke je Objekt). ✅
- "Projekt" korrekt keine eigene Nav-Ebene (passt zu Weiche 5). ✅
- Objekt-Zentrierung stark verankert (alternative 279×). ✅
- Bereichs-Nav ist DATEN-GETRIEBEN (Array label/count_key/count) → Neu-Sortierung wäre markup-arm. ✅ (wichtig)

**Hauptwiderspruch:**
- Die 6 Phasen (Weiche 1) erscheinen im Profil-Blade NICHT (lead_stage = 0 Vorkommen) — Phasen-Nav lebt nur im externen Kanban.
- Bereichs-Nav ist FLACH: mischt Phasen-Labels (Angebote/Auftrag/Montage) mit Funktionen (Aufgaben/Termin/Produkt/Tickets) ohne die 6er-Ordnung. Lead/Abnahme/Abschluss fehlen als Nav-Punkt; "Rechnungen" steht als Phase-Peer, ist aber Aufgabe der Abschluss-Phase.
- Phase→Aufgabe→Arbeitsschritt verteilt über 4 Stellen (Nav Aufgaben + Nav Arbeitsprozess + phaseSidebar + customerKanbanTaskDrawer) — spiegelt die Aufgaben-System-Zersplitterung.

**NICHT VERIFIZIERT (vor Umbau zu klären):**
- Das JS hinter phaseSidebar (data-service-id) — zeigt es zur Laufzeit doch Phasen (welches System)? Nur Blade belegt, nicht Laufzeit.

**Konsequenz:** Profil ist NICHT grundlegend falsch — Hierarchie-Basis stimmt. EIN Struktur-Problem: Phasen-Achse fehlt + flache gemischte Nav. Umbau wäre markup-arm (daten-getrieben), aber die Design-Entscheidung (wie Phase-Achse vs Funktion-Inhalt trennen) steht aus und hängt an Weiche 1 + 6.

---

## WEICHE 6 — Restfrage entschieden: Montageplan-Erzeugung

**Der Montageplan wird NICHT automatisch erstellt, sondern bewusst GEPLANT.** (Yama entschieden)

Kein Auto-Trigger bei Eintritt in die Montage-Phase. Stattdessen ein menschlicher Planungsschritt: Vorlage oder Set wählen, Materialliste mit Feinaufmaß abgleichen, Personal einteilen. Passt zum Kuratier-Prinzip (Büro plant bewusst, statt Automatismus).

Kontext (Yamas Prozessbeschreibung): Nach Auftragsbestätigung stehen Produkte/Dienstleistungen fest → Materialliste wird aus der Auftragsbestätigung erstellt → mit Feinaufmaß verglichen → bestellt. Jedes Projekt hat teils vorgefertigte Aufgaben (über Einstellungen neu konfigurierbar). Gewerke wie PV haben eigenen Montageplan, beziehbar aus verschiedenen Vorlagen oder aus Sets.

---

## WEICHE 1 + WEICHE 6 — final entschieden (fachlich begründet, korrigierbar)

Diese Entscheidungen sind auf Basis von Yamas Prozessbeschreibungen und der Befundlage getroffen. Wo eine nicht zur Praxis passt, wird sie korrigiert.

### WEICHE 1 — Phasen-Wahrheit: lead_stages
**Der verbindliche Phasen-Status eines Gewerks lebt in `lead_stages` / `lead_stage_sub_stages`.**
Begründung: Das ist das jüngste (2026), sauberste, zweistufige System (6 Hauptphasen × Unterphasen), an die 6 entschiedenen Phasen anschlussfähig, und Yama hat es selbst als sein Phasen-System bestätigt (Kanban mit Gewerken als Karten). Die alten verstreuten Statusfelder und customer_phase_lists werden NICHT mehr als Phasen-Wahrheit genutzt (customer_phase_lists wird ohnehin abgelöst).
→ Alle anderen Systeme (Profil-Anzeige, Rückfluss, Progressbar) richten sich nach lead_stages als der einen Antwort auf "in welcher Phase steht das Gewerk".

### WEICHE 6 — Rückfluss: ja, MIT Prüfschritt des Projektleiters
**Der Feld-Status des Monteurs (erledigt/Foto) läuft zurück ins Büro — aber als MELDUNG, die der Projektleiter PRÜFT, nicht als automatisch endgültige Wahrheit.**
Fluss: Monteur hakt ab → läuft ins Büro als "vom Monteur als erledigt gemeldet" → Projektleiter prüft → er bestätigt (oder schickt zurück). Der Monteur-Abschluss ist eine Meldung, die der Projektleiter abnimmt — kein automatisches Durchreichen.
Begründung: Sonst sieht das Büro an der geplanten Stelle nie, was draußen erledigt wurde. Höchster Alltagsnutzen. Der Prüfschritt ist bewusst gewollt (Kontroll-/Abnahme-Funktion des Projektleiters) — passt zum Kuratier-Prinzip.
→ Bau-Konsequenz: die Büro-Karte braucht einen Zwischenstatus wie "vom Monteur gemeldet" (≠ "vom PL bestätigt"). Nicht nur done/offen, sondern eine Melde→Prüf→Bestätigt-Kette.
ABER (technische Voraussetzung, hart geprüft): Es gibt heute KEINE eindeutige Verbindung planner_item(phase_activity) ↔ kanban_lead_tasks-Karte. Der Bau braucht ZUERST einen echten Link (z.B. planner_items.meta.kanban_lead_task_id beim Planen setzen, ODER Unique+firstOrCreate). → Entscheidung "ja" steht; Bau ist ein eigener späterer Schritt nach Schaffung des Links.

### WEICHE 6 — Felder an Arbeitsschritte koppeln: später
**Die Qualifizierungs-/Formular-Felder (product_formulas) werden an die Arbeitsschritte (phase_activities) gekoppelt — aber mit niedriger Priorität, nach den wichtigeren Schritten.**
Begründung: Sinnvolle Erweiterung, aber kein Alltags-Schmerz. Reihenfolge: erst Rückfluss + customer_phase_lists ablösen, dann diese Kopplung.

### Montageplan-Erzeugung: geplant, nicht automatisch (bereits entschieden, s.o.)

---

## DAMIT SIND WEICHE 1 UND WEICHE 6 GESCHLOSSEN.
Offene Bau-Schritte (kein Entscheidungsbedarf mehr, nur noch Umsetzung, je mit Pflicht-Stopp):
1. Rückfluss-Link schaffen + Feld-Status → Büro-Karte (nach Link).
2. customer_phase_lists ablösen (dormant).
3. Progressbar später auf lead_stages/planner ausrichten (Basis steht, gebaut).
4. Felder an Arbeitsschritte koppeln (niedrige Prio).
Alle Bau-Schritte hängen jetzt auf ENTSCHIEDENEM Boden — keine Weichen mehr offen im Kernprozess-Ausführungsteil.

---

## VIDEO-CALL via Jitsi (Feature-Flag)

Kunden- und interne Calls über `video_calls` + `JitsiService`; Systemnachricht `type='video_call'` im bestehenden Chat (MessageSent/Reverb); signierte, zeitlich begrenzte **Gast-Links** nur für Kunden-Calls (intern kein Externen-Zugang); Domain/Secrets per `.env`; JWT pures PHP HS256, nur bei `JITSI_JWT_ENABLED`. Berührt Weiche 1/5/6 nicht. Ausgeliefert deaktiviert (`JITSI_ENABLED=false`).

Verifiziert am 2026-07-04 (migrate, route:list, 11 Feature-Tests grün).
