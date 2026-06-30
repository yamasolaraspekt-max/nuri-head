# Kalender / Termine — Bestandsaufnahme

**Read-only Analyse, nichts geändert.** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Ziel: voller Überblick über Funktionsumfang und Verknüpfungen des Termin-/Kalenderbereichs.

> Kurzfazit: Das ist **kein** Standard-Kalender, sondern ein **operatives Termin-Drehkreuz** des CRM.
> Eine einzige zentrale Tabelle (`main_appointments`) hängt an fast allem (Kunde, Lead, Auftrag, Aufmaß,
> Ticket, Aufgabe, Filiale) und wird aus **sieben** verschiedenen Vorgängen heraus erzeugt. Der Controller
> dahinter ist mit ~3.300 Zeilen und 40+ Aktionen der größte Knotenpunkt im ganzen Modul.

---

## 1. Funktionsumfang — was der Bereich kann

### Termin-Arten
Die Art steckt in mehreren Feldern von `main_appointments`:
- **`appointment_type`** — fachliche Art. In den Demo-Daten: **Beratung, Montagebesprechung, Übergabe, Aufmaß**. Das Feld ist frei (varchar), weitere Arten sind möglich.
- **`contact_mode`** — Kontaktweg: **Telefon, Vor-Ort, Video** (mit `link` für Video-Termine).
- **`type`** — technische Hauptkategorie (`appointment`); daneben `execution_type`, `pre_type`, `source`, `contact_type`, `is_contact` zur weiteren Klassifizierung.
- Termine können auch **Kontakt-/Berichts-Charakter** haben (`is_contact`, `is_report`) — d. h. der „Termin" dient teils als Aktivitäts-/Kontakteintrag.

### Ansichten
- **Kalenderansicht** (FullCalendar-artig): `CustomerMainAppointmentController@getEvents` liefert die Events; `index()`/`data()` rendern den Kalender. Damit kommen die Standard-Zeitraster (Tag/Woche/Monat) aus der Kalender-JS-Bibliothek.
- **Listen-/Tabellenansicht**: `MainAppointmentController@index` → paginierte Liste (`appointment_view` / `appointment_table`), mit **Tab-Filtern** über `data_type`: *general, created (von mir erstellt), participant (ich nehme teil), cancel, expired, confirm, deleted*.
- **Kontaktliste**: `contactListIndex()` / `contactList()` — termin-bezogene Kontakte.
- **Mobiler Kalender**: `MobileCalendarController` und `Api/MobileCalendarController` (zwei Varianten) + `mobileStore()` für die App/mobile Anlage.
- **Dashboard-Widget**: `DashboardCalendarWidgetController` zeigt Termine auf dem Dashboard.

### Aktionen (Auswahl aus 40+ Methoden)
- **Anlegen**: `store`, `mobileStore` (mobil), `duplicate` (Termin duplizieren) — plus die automatische Anlage (s. Teil 2).
- **Bearbeiten/Verschieben**: `update`, `editCalendar` (Verschieben direkt aus dem Kalender, z. B. Drag&Drop), `updateAjax`, `editCalendar` mit Datums-/Zeitänderung; `change_date`/`change_reason` protokollieren Verschiebungen.
- **Serie/Wiederholung**: Feld `repeat` (+ `date_type`, `from_day/to_day/from_month/to_month`); `no_repeat()` löst einen Termin aus einer Serie heraus.
- **Mitarbeiter zuweisen / einladen**: `add_employee` / `addEmployee` / `removeEmployee` (n:m über `main_appointment_employees`). Jeder zugewiesene Mitarbeiter hat **einen eigenen Status** (`main_appointment_employees.status` + `reason`) — d. h. **Einladung mit Zu-/Absage** je Teilnehmer (`accept_request`).
- **Status / Absagen**: `status` / `updateStatus` (z. B. *confirm, cancel, junk, deleted*); `destroy`/`calendar_destroy` (Soft-Delete, `deleted_at`) + `restore`.
- **Erinnerungen**: Felder `reminder_date`, `reminder_time`, `is_notified`; `no_reminder()` schaltet sie ab. Versand über einen **Minuten-Cron** `appointments:dispatch-reminders` (`app/Console/Commands/DispatchMainAppointmentReminders.php`, in `Kernel.php:27` als `everyMinute()`). Protokoll in `main_appointment_reminder_logs` (inkl. `reminder_count`, damit nicht doppelt erinnert wird). Anstehende Erinnerungen: `MainAppointmentReminderController@upcoming` + `markSeen`.
- **Benachrichtigungen**: `getAppointmentNotifications()`, `CustomerMainAppointmentController@notifyDueToday()`.
- **Berichte zum Termin** (Feed-Charakter): `toggleReport`/`saveReport`/`loadReport`/`deleteReport`/`reports`, plus **Reaktionen & Kommentare** (`reactReport`, `commentReport`) → Tabelle `appointment_reports` mit `likes`, `dislikes`, `comments`, `next_step`, `due_date`.
- **Karte/Route**: `getMap()` + `latitude`/`longitude`/`full_address` — Termine sind verortet.
- **Farb-/Prioritätslogik**: `color` (freie Farbe je Termin), `priority`, `status` steuern die Darstellung.

### Verfügbarkeit/Auslastung
Es gibt **Arbeitszeitpläne** je Mitarbeiter (`employee_time_schedules` / `EmployeeTimeSchedule`) und die Teilnehmer-Zuordnung. Eine **explizite Frei/Belegt-Konfliktprüfung** beim Terminieren (wie die Überlappungsprüfung beim Urlaub) ist im Termin-Pfad **nicht eindeutig erkennbar** — Auslastung ergibt sich eher aus der Anzeige der zugewiesenen Termine als aus einer harten Verfügbarkeitslogik. *(Beobachtung, nicht abschließend verifiziert.)*

---

## 2. Verknüpfungen — woran ein Termin hängt

Alles läuft über **`main_appointments`** (zentral) + **`main_appointment_employees`** (Teilnehmer). Die Fremdschlüssel zeigen die Reichweite:

| Verknüpft mit | Feld in `main_appointments` |
|---|---|
| **Kunde / Lead** | `customer_id` (→ new_leads) |
| **Gewerk / Lead-Produkt** | `lead_product_list_id`, dazu `lead_stage_id`, `lead_stage_sub_stage_id` (Kanban-Stufe) |
| **Auftrag-Aufmaß** | `deal_measurement_id` (Feinaufmaß zum Deal) |
| **Ticket / Reklamation** | `problem_id`, `problem_task_id` |
| **Aufgabe** | `task_id` |
| **Planer** | `planner_item_id` (Einsatz-/Projektplaner) |
| **Filiale / Standort** | `branch_id`, `branch_address_id` |
| **Kontakt** | `contact_id`, `contact_type` |
| **Produkte/Anfrage** | `products` (json), `product_inquiry` (json) |
| **Ersteller / Teilnehmer** | `created_by`, n:m über `main_appointment_employees` |
| **Berichts-Verantwortliche** | `report_responsible` (json) |

### Automatisch erzeugte vs. manuelle Termine
`MainAppointment::create` wird aus **sieben** Quellen heraus aufgerufen — Termine entstehen also als Nebenprodukt anderer Vorgänge:
- **Lead-Kanban** (`Customer/Kanban/LeadOverviewController`)
- **Anfrage** (`Inquiry/InquiryController`)
- **Auftrag/Deal** (`Customer/Deal/DealController`)
- **Aufmaß** (`Customer/Deal/DealMeasurementController`) → typischer „Aufmaß"-Termin
- **Ticket/Problem** (`Ticket/ProblemController`, `Ticket/TicketAppointmentController`)
- **Mitarbeiter** (`Employee/EmployeeController`)
- **Mobil** (`MobileCalendarController`, `Api/MobileCalendarController`)

Plus die **manuelle** Anlage über `MainAppointmentController@store` / `duplicate` und die Kalender-Maske.

### Zusammenhang mit Tagesberichten / Zeiterfassung / Anwesenheit
- **Tagesberichte**: Termine tragen einen eigenen **Bericht** (`is_report`, `report`, `report_date`, `report_by`) und sind über `appointment_reports` mit dem Berichtswesen (inkl. Reaktionen/Kommentaren) verzahnt. `next_step`/`due_date` führen den Vorgang fort.
- **Zeiterfassung/Anwesenheit**: `total_time` am Termin; die eigentliche Ist-Zeit-/Anwesenheitserfassung liegt in eigenen Tabellen (`attendances`, `daily_report_time_customers`) und ist **auftrags-/kundenbezogen** — die Brücke „Termin ⇄ erfasste Stunden" ist vorhanden (gemeinsame Kunden-/Produktbezüge), aber nicht als harte 1:1-Verknüpfung am Termin selbst.

---

## 3. Wer sieht was — Sichtbarkeit / Rollen

Die Standard-Liste (`MainAppointmentController@index`) zeigt einem Nutzer **seine eigenen + die ihn betreffenden** Termine:
```
WHERE created_by = (eigene Mitarbeiter-ID)
   OR main_appointment_employees.employee_id = (eigene Mitarbeiter-ID)
```
Also: **selbst erstellte** Termine und solche, bei denen man **Teilnehmer** ist. Über die `data_type`-Tabs lässt sich das umschalten (created / participant / general / cancel / expired / confirm / deleted).

- Es gibt ein **`public`**-Feld am Termin (öffentlich/privat), das in der Listen-Hauptabfrage aber **nicht** als Filter genutzt wird — der Sichtbarkeitsumfang ergibt sich primär aus *Ersteller/Teilnehmer*.
- Filter nach **Abteilung** (`department`) tauchen an mehreren Stellen im Controller auf (z. B. Kontaktlisten/Untermenüs), aber die Haupt-Terminliste ist **personen-** (Ersteller/Teilnehmer), nicht abteilungs- oder teamweit.
- **Datenschutz-Hinweis (Beobachtung):** Es gibt **keine** durchgängige rollen-/rechtebasierte Sichtbarkeitsschicht (kein `hasPermission`-Gate auf der Terminliste). Wer Termine sieht, hängt allein an Ersteller/Teilnehmer-Zuordnung. Ob das gewünscht ist (z. B. dürfen Vorgesetzte/Abteilung mehr sehen?), ist eine **fachliche/Datenschutz-Entscheidung**, keine technische.

---

## 4. Besonderheiten & Komplexität

- **Ein Mega-Knotenpunkt:** `MainAppointmentController` (~3.300 Zeilen, 40+ öffentliche Methoden) bündelt Anlage, Bearbeitung, Kalender-Verschiebung, Teilnehmer, Status, Erinnerungen, Berichte, Reaktionen, Karte, Mobile und Lead-Stage-Kontext in **einer** Klasse. Das ist die mit Abstand dichteste Stelle des Moduls.
- **Sieben Anlage-Pfade + manuell:** Denselben „Termin anlegen" gibt es aus Lead, Anfrage, Auftrag, Aufmaß, Ticket, Mitarbeiter, Mobil — plus die Maske. Mehrere Wege zum selben Ziel.
- **Termin = mehr als Termin:** Durch `is_contact`/`is_report` ist ein Eintrag teils Termin, teils Kontaktprotokoll, teils Aktivitäts-/Bericht-Feed (mit Likes/Kommentaren). Das vermischt Kalender und CRM-Aktivitätsstrom.
- **Serien + Verschiebe-Protokoll:** Wiederholungen (`repeat`, Tages-/Monats-Spannen) und revisionssichere Verschiebungen (`change_date`/`change_reason`/`changed_by`) gehen über einen Standard-Kalender hinaus.
- **Eigenes Erinnerungs-Subsystem:** Minuten-Cron + Log-Tabelle mit Zähler, getrennt von den übrigen Reminder-Systemen (s. u.).
- **Mehrere parallele Reminder-Welten:** `main_appointment_reminder_logs` (Termine), `reminders`/`reminder_events` (generisch), `lead_reminders` (Lead-Kanban), `PersonalNoteReminderController` (Notizen). Vier Quellen für „erinnere mich".

---

## 5. Auffälligkeiten (nur benannt — NICHT angefasst)

- **Totes Alt-System:** Die alte Tabelle `appointments` + `appointment_employees` + `Old/AppointmentController`, `Old/oldMainAppointment`, `Old/AppointmentCommentController`, `Old/AppointmentAttachmentController` haben **0 Routen** — komplett abgelöst durch `main_appointments`, aber noch im Code. Verwaister Ballast.
- **Doppelter Mobile-Controller:** `MobileCalendarController` (Root) **und** `Api/MobileCalendarController` — zwei Varianten nebeneinander, vermutlich Migration App→API. Unklar, welcher führend ist.
- **Fragile Sichtbarkeits-Abfrage:** In `index()` (Zeilen 63-68) mischt die Hauptabfrage `orWhere(created_by)`/`orWhere(participant)` mit `whereDate(start/end <= now)` **ohne saubere Klammerung** — die Datumsbedingung hängt logisch an der OR-Verzweigung und filtert dadurch möglicherweise anders als gedacht (z. B. blendet sie künftige Termine in der Standardliste aus). Wirkt unbeabsichtigt. *(Beobachtung — nicht verifiziert/nicht gefixt.)*
- **`public`-Feld ungenutzt:** Es existiert eine öffentlich/privat-Markierung, die in der Hauptliste nicht ausgewertet wird — entweder Rest einer geplanten Sichtbarkeitslogik oder Altlast.
- **Frei-Text-Arten:** `appointment_type`/`status`/`execution_type` sind freie Strings ohne feste Liste — Inkonsistenz-Risiko (z. B. „Aufmaß" vs. „Aufmass").
- **Status-Werte uneinheitlich:** Es tauchen `cancel/confirm/deleted/junk` (Liste) neben anderen Status-Begriffen auf; kein zentrales Status-Enum.

---

*Ende der read-only Bestandsaufnahme. Kein Code, kein Schema, keine Daten verändert.*
