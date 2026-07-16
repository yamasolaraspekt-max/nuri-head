# Entscheidungsvorlage — Pipeline-Fläche (Abwicklung): Welcher Status ist die Wahrheit?

**Datum:** 2026-07-16 · **Rolle:** Planner (kein Code) · **Anlass:** Welle B2, Fläche „Pipeline" —
der Deal trägt vier Status-Felder, das Operanden-Gate verbietet Raten.

## Befund: Wer schreibt und liest welches Feld (Code-Bestandsaufnahme)

| Feld | Wer schreibt | Wofür steht es | Wer liest |
|---|---|---|---|
| `project_status` (deals) | DealController (`planned`), DealMeasurementController (`measurement`), PersonalTaskController (Validierung `new/start/on_going/on_review/completed/pause/cancel`) | **Abwicklungs-/Durchführungsstand des Projekts** — belegte Werte: new · planned · start · measurement · on_going · on_review · completed · pause · cancel | deal/profile, invoices/index, todo/task- und Termin-Details |
| `deal_status` (offer_folders) | OfferFolderController, OfferTemplatePicker, NewLeads (`DEAL_STATUS_OPEN`) — Label im Code: **„Auftragsstatus"** | **Kaufmännischer Status des Auftrags-Ordners** (offen → …) | folder-show, deal/profile, invoices/index, kanban (alt) |
| `measurement_status` (deals) | DealMeasurementController (`open`/`completed`) | Teil-Status **nur fürs Feinaufmaß** | materialbedarf, auftraege-Layout |
| LeadStage/`companyStage` (eigene Tabellen) | Termin-/Lead-Strecke (`LeadStage::where('key', …)`) | **Vertriebs-Funnel** (Lead-Stufen mit Sub-Stages) | Termin-/Lead-Flächen |
| `status` (deals) | u. a. MainAppointmentController (`'start'`) | Alt-Doppelspur zu project_status, uneinheitlich | verstreut |

## Empfehlung (▲B2-K1)

**Die Pipeline-Fläche „Abwicklung" zeigt `deals.project_status` — nichts anderes.**

Begründung: Es ist das einzige Feld, das den *Durchführungsstand* trägt, es hat ein belegtes,
validiertes Werte-Set, und seine Schreiber sind genau die Abwicklungs-Workflows (Deal anlegen,
Feinaufmaß, Aufgaben). Die anderen Felder sind andere Fragen: `deal_status` = kaufmännisch
(gehört zur Auftrags-/AB-Strecke), LeadStage = Vertriebs-Funnel (eigene Fläche „Vertriebs-
Pipeline", falls je gewünscht), `measurement_status` = Teilschritt.

**Spaltenvorschlag der Pipeline (Reihenfolge = Prozess):**
Neu → Geplant → Gestartet → Aufmaß → In Arbeit → In Prüfung → Pausiert → Abgeschlossen
(cancel als Filter „Abgebrochen", nicht als Spalte — beendete Vorgänge verstopfen das Board nicht).

**V1 = LESEND:** Board-Ansicht (Karten je Deal: Kunde, Objekt, Betrag falls verknüpft, Alter im
Status), Klick öffnet das Deal-Profil. KEIN Drag&Drop in V1 — Statuswechsel bleibt in den
Workflows, die ihn heute schreiben (eine Wahrheit, kein zweiter Schreibpfad). Drag&Drop wäre V2
mit eigenem Konzept (wer darf welchen Übergang, Ereignis→Folge).

## Bewusste Grenzen / Rest-Risiken

1. `deals.status` ist eine Alt-Doppelspur (wird z. B. vom Termin-Controller auf 'start' gesetzt).
   Die Pipeline IGNORIERT sie bewusst; Konsolidierung = eigener späterer Vorgang (notiert).
2. Werte-Vollständigkeit: ob der Live-Bestand weitere project_status-Werte enthält, prüft der
   Generator vor dem Bau per SQL-Zählung (SELECT project_status, COUNT(*) GROUP BY) — unbekannte
   Werte bekommen eine eigene „Sonstige"-Spalte statt still zu verschwinden.

## Nächster Schritt

Yamas Freigabe dieser Vorlage ⇒ Generator baut die Lese-Fläche (Route + Controller + Board-View
nach sa-ui, Sidebar-Flip „Pipeline"), Evaluator nimmt sie mit SQL-Gegenprobe ab.
