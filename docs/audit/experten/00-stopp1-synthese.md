# ⛔ STOPP 1 — Experten-Inventur (CRM-AUTOMATISIERUNG-MASTER Stufe 1)

> 10 Fachbereichs-Experten haben SEINEN Bereich rein lesend inventarisiert (je Datei `docs/audit/experten/NN-*.md`, firsthand Code/SQL + Explore-Unteragenten, bauen auf code-/intelligenz-/hebel-Audit auf). **Yama sichtet + ergänzt versteckte Funktionen + bestätigt — erst dann Stufe 2 (Prozess-Graph + Konfigurations-Achse).**

## Der eine rote Faden: ZWEI WELTEN
ticket ist **zwei Systeme in einem**: eine **junge Rechen-/Service-Zone** (Auslegung, Accounting, Formular-Engine) mit **Assistenz-Reife ~4** — und ein **umsatztragender Alt-Kern** (Vertrieb→Auftrag→Rechnung), der **prozess-stumm ~2** ist (speichert sauber, stößt aber Folgen nicht an). **Der billigste Automatisierungsgewinn ist überall „VERDRAHTEN statt Neubau"**: fünf fertige Intelligenz-Schichten liegen brach (SmartroutingService, PlausibilityService, AnforderungsprofilHeizlastAdapter, 2 FollowUpCreator-Slots, gebaute Einsatzplanung) — je 0/wenige Aufrufer.

## Reife je Bereich (Kurz-Befund)
| # | Bereich | Reife | Top-Stärke | Top-Schwäche |
|---|---|:--:|---|---|
| 1 | **Vertrieb/CRM** | MITTEL (~3) | Sauberes Gewerk-Modell + **verdrahteter** Zuständigkeits-Router `getLeadEmployee()`, validierter Intake + Duplikat-Abwehr | Phasenwechsel stößt **0** Vertriebs-Folge an (kein „Angebot erstellen"-Task) → liegengebliebene Angebote; `qualified()` missbraucht `status` als Fehlertext-Halter |
| 2 | **Angebot/Auslegung** | Rechen ~4 / Verdrahtung ~1 | Stärkste Fach-Intelligenz (DIN-Heizlast, PV-String, WP-Matching, BEG, Energiekonzept); `AnforderungsprofilHeizlastAdapter` = bestes Operanden-Gate | **Auslegungs-Welt isoliert** (0 Aufrufer aus Angebot/Wirtschaftlichkeit) → alles von Hand abgetippt; Angebot vertraut Client-Einzelpreisen; PLZ-Miss → stilles Norm-Klima |
| 3 | **Auftrag** | ~3 (reife Inseln) | `destroy`-Storno vorbildlich (atomar, bezahlte Rechnungen markiert); **Einsatzplanung gebaut** (Material+Qualifikation+Konflikt+Scoring, Reife 4) | Angebot→Auftrag stößt fast nichts an (Einsatzplanung nur manuell); **junk()-Storno-Bug** (Rechnungen bleiben offen); Auftragsbestätigung/Nachträge fehlen |
| 4 | **Beschaffung** | ~2 | Katalog/Preis bestückt (94 Produkte, 88 Preise, 9 Großhändler); **Materialbedarf abgeleitet** (Angebot+Aufmaß, Plan/Ist, Bestands-Match) | Prozess-Schicht komplett leer (0 Rows); **kein Order-out** (EDI), nur Bestelldruck; Wareneingang bucht **keinen** Bestand; DATANORM nur Parse-Preview |
| 5 | **Disposition** | ~2 (Insel 4) | Reiches Rang-/Kapazitäts-/Territorium-Modell befüllt (Qualifikationen, Prozente, `department_id` 52/52) | „Rechnet rückwärts": **keine Verfügbarkeits-/Doppelbuchungsprüfung** (Blind-INSERT), **kein Vorwärts-Qualifikations-Matching**, keine Routenplanung, keine Verantwortungsmatrix |
| 6 | **Montage/Doku** | ~2–3 | Planner = Feld-Ausführungs-Wahrheit (Weiche 6), Progressbar aus planner_items, Report-Mirror + Historie | Rückfluss nur teilweise; **FollowUpCreator NICHT am Feld-Abschluss**; **Aufmaß→Heizlast NICHT durchgereicht**; Checklisten ohne Smartrouting (0 Aufrufer) |
| 7 | **Abnahme** | **0–1 (niedrigster)** | Protokoll-Muster (`MaintenanceProtocol`) + Vorlagen-Kategorie im Haus (Wiederverwendung möglich) | **Leere Kanban-Spalte, kein Abnahmeprotokoll/Mängelliste/Unterschrift**; `handovers` = Lager-Asset-Transfer (Namensfalle, NICHT Kunden-Übergabe); Gewährleistungsbeginn nirgends abgeleitet |
| 8 | **Finanz/FiBu** | Beleg reif / Geld dünn | Rechnung + race-safe Nummernkreis + GoBD-Schutz + Positions-Durchreichung + server-autoritative Summen | **FiBu gebaut, aber 0 Belege/0 Buchungen** (Festschreib→Buchung-Trigger fehlt); „bezahlt" = blind ohne Zahlungseingang; kein Mahnwesen; Konsistenz-Löcher (Status `open` nicht im Enum, `paid_at`<`issue_date`) |
| 9 | **Auswertung/Controlling** | ~1,5–2 | Umsatz-/Aktivitäts-Cockpit vorhanden | **Keine echte Nachkalkulation** (nur Vorkalkulation, nie persistiert); **kein Cross-Projekt-Controlling** (keine Kosten-/Gewinnseite); Wartungs-Wiedervorlage **nicht automatisch** |
| 10 | **Querschnitt** | zwei Welten | FK-Kanban-Hook, Accounting, Auslegungs-Kerne (schützen) | 2 echte Live-Doppel-Wahrheiten (offer_kanban_stages↔lead_stages; deals 5 Statusfelder); Prozess-stumm (kein Model-Observer); 5 Intelligenz-Schichten brach; Rechte-Grants dünn (Enforcement läuft) |

## Wiederkehrende Muster (bereichsübergreifend)
1. **Gebaut-aber-nicht-verdrahtet** ist DAS Leitmotiv (Smartrouting, Plausibility, Anforderungsprofil-Brücke, FollowUpCreator-Slots, Einsatzplanung, MaintenanceProtocol) → Quick-Wins = einhängen.
2. **Phasenübergänge sind die fehlenden Auslöser** (Lead→Angebot, Angebot→Auftrag, Abnahme→Rechnung, Wartungs-Fälligkeit) — der FK-Kanban-Hook ist der ideale, schon existierende Ereignis-Punkt.
3. **Durchreichungs-Lücken an den Rändern** (Aufmaß→Heizlast, Objekt-Adresse, GR→Bestand) — die Geld-Kette ist bereits sauber durchgereicht.
4. **Status-Zoo** untergräbt jede Auswertung (Weiche 1 noch nicht gebaut).
5. **Abnahme + Nachkalkulation + Zahlungs-/Mahnwesen** sind die echten weißen Flecken (Grad 0–2), teils mit vorhandenen Bausteinen billig baubar.

## ⛔ Deine Aufgabe an STOPP 1 (bevor Stufe 2)
1. **Sichten** — stimmen die 10 Kurz-Befunde mit deiner Betriebsrealität?
2. **Versteckte Funktionen ergänzen** — was nutzt ihr, das der Code-Scan nicht als „genutzt" erkennt (z. B. manuelle Workarounds, externe Tools)?
3. **Bestätigen** — dann starte ich Stufe 2 (echter Prozess-Graph inkl. Eventualitäten + Konfigurations-Achse Gewerke×Gebäude×Förderung + Informationsbedarf-Matrix), die als Stopp-2 die **realen Projekt-Typen + Häufigkeiten** von dir erfragt.

*(Vollausgabe je Bereich in den 10 Dateien; `06-montage-doku.md` finalisiert gerade.)*
