# Navigation & Modulstruktur — Ist-Befund (Inventur)

**Stand:** 2026-07-03 · **Reine Read-only-Inventur — nichts geändert, kein Commit.** Kein Zielbaum, keine Umbenennung als Umsetzung, keine Routenverschiebung.
**Quellen:** `resources/views/admin/layouts/sidebar.blade.php` (2103 Z.) · `routes/web.php` (5428 Z.) · 378 Controller (`app/Http/Controllers/**`) · 389 Models (`app/Models/*`) · ~70 View-Ordner (`resources/views/admin/`) · vorhandene Navi-/IA-Doku.
**Methodik:** 3 parallele Read-only-Inventur-Agenten (Sidebar · Modul-Matrix · Doku/Begriffe), Befunde mit Datei:Zeile belegt.
**Zweck:** belastbarer Ausgangs-Ist-Befund für eine spätere Navi-Neuplanung (max. 4–5 Unterpunkte je Hauptbereich — **erst später**).

---

## 1. Kurzfazit
Das System ist **groß und überwiegend real** (378 Controller, 389 Models). Die Sidebar ist **technisch robust** (hartcodiertes `$sidebarSections`-Array, alle Routen lösen auf, `$safeRoute`-Fallback verhindert 500er), aber **informationsarchitektonisch die Schwachstelle**: **15 Sektionen · 37 Hauptpunkte · 93 Unterpunkte · max. Tiefe 2** — gewachsen statt geplant. Kernprobleme: **Rechnungen unter Vertrieb statt Finanzen**, **Finanzen unterentwickelt** (3 Kinder), **doppelter Rechnungs-Eintrag**, und viele Buchhaltungs-/Lohn-Bausteine sind **nur dokumentiert, nicht gebaut**. Es existiert bereits **umfangreiche Navi-/IA-Vorarbeit** (`ia-entscheidungen.md`, `navi-schwaechen-gesamt.md`, `navigation-terminologie-audit.md`, ratifiziertes `glossar.md`) — teils umgesetzt (Umbenennungen), teils offen (IA-2/IA-10).

## 2. Aktuelle Sidebar-Struktur
- Quelle: **hartcodiertes PHP-Array** `$sidebarSections` (`sidebar.blade.php:398–1340`, kein DB-Menü), gerendert `:2019–2103`, eingebunden via `app.blade.php:4451`.
- **15 Sektionen · 37 Hauptpunkte · 93 Unterpunkte · max. Tiefe 2** (Sektion → Punkt → Unterpunkt; keine 3. Ebene).
- Rechte: `is_admin`-Bypass (`:305`), sonst `user_rolls` (`:314`). **Nur Sektions-/Parent-Punkte tragen einen `permission`-Key** (Email, Inquiry, Customer, Partner, Problem, Employee, Organization, Product, Finance, Users); die 93 Unterpunkte sind größtenteils **ungeschützt**.
- Zusatz-Navigation außerhalb des Arrays: **Profil-Footer** (`app.blade.php:4475–4545`), **Mobile-Bottom-Nav** (`:5149–5221`).
- Zähler-Badges werden per JS aus `/api/sidebar-counts` gefüllt (`sidebar.blade.php:395`).

## 3. Sichtbare Navigationspunkte (Struktur)
Baum verdichtet (Zahl = Unterpunkte); jede Route wurde gegen `web.php` geprüft → **kein kaputter Link**:

```
Arbeitsbereich(2) · Berichte(3)
CRM[Kommunikation(6) · Anfragen(7) · Leads/Kunden(6) · Kontakte(8)]
Vertrieb[Angebote(4) · Aufträge(6)]
Projekte[Projektplanung · Meine Aufgaben · Allgemeine Aufgaben · Notizen(2) · Termine(2) · Wartung(2)]
Support[Tickets(3)]
Personal[Mitarbeiter(8) · HR-Daten(7) · Organisation(4)]
Artikel&Lager[Artikel(9) · Artikel-Daten(5) · Lager(7)]
Finanzen[Förderungen · Filial-Betriebskosten · Ratenzahlungen]   (nur 3)
Admin[Benutzer(4)] · Konfiguration(3) · Tools(2) · System(4) · Einstellungen(1) · Wissen(1)
```
> Vollständige 93-Zeilen-Detailtabelle (E1/E2/Label/Route/Perm/Datei:Zeile) wurde erstellt; kann auf Wunsch ergänzt werden.

## 4. Vorhandene Module (Domänen-Übersicht)
| Domäne | Real vorhanden & nutzbar | Teilweise / anders benannt | **Fehlt (0 Code-Treffer)** |
|---|---|---|---|
| CRM/Kontakte | Kunden/Leads, Kanban, Kontakte, Historie, Follow-ups | Ansprechpartner (in Kundenakte) | — |
| Vertrieb | Angebote, Aufträge (`deals`) | Nachfassen (kein Nav-Punkt) | — |
| Projekte | Planner, Aufgaben, Termine, Berichte, Phasen | „Projekt"=Planner (Alt-`Project*` dormant) | — |
| Service | Tickets (=**Problem**-Modul), Wartung, Fehlerkatalog | Reklamationen (via Feedback), Technikerzuordnung | **Notdienst, SLA/Eskalation** |
| Finanzen | Rechnungen (`invoices`), Betriebskosten, Ratenzahlungen, Förderungen | Zahlungen (nur Zahlarten), Storno (im Invoice-Flow) | **OP, Kanzlei-Übergabe, DATEV, SKR03/04, Kostenstellen** |
| Lager/Artikel | Artikel/Katalog, Bestände, Lieferscheine, Lieferanten, Wareneingang, Kaufanfragen | Einkauf (nur Kaufanfragen), Lagerorte (keine Entität) | **Inventur (Zählprozess), Mindestbestand** |
| Personal/Lohn | Mitarbeiter, Zeiten, Abwesenheit, Lohn&Vollkosten | Lohnvorbereitung (nur Lohnblatt) | **Lohnarten, DATEV-Lohn** |
| Admin/System | Benutzer, Rollen/Rechte, Abteilungen, Import/Export, Systemwarnung, DB-Bereinigung | Einstellungen (verteilt), Stammdaten (verteilt) | **Nummernkreise (kein Modul), zentraler Log-Viewer** |

## 5. Im Code vorhanden, aber nicht sichtbar verlinkt
- **`app/Http/Controllers/Old/` — 37 Controller, komplett tot** (kein Routen-Verweis), teils **Duplikate aktiver Controller** (Appointment/Customer/Offer/Project) + Backup-Artefakte (`… copy.php`, `oldMainAppointment.php`).
- **Projekt-Altdomäne** (`Project`, `ProjectTask`, `ProjectTimeline`, `ProjectAward`, …) — durch **Planner** abgelöst, Models dormant.
- **PV-/Solar-Rechenkerne** (`PVLongChecklist`, `SolarSystem`, `Heatpump*`, `EconomicCalculation`) — nur indirekt über Tools/PVGIS.
- **Bitrix/NIBE-Legacy** (`BitrixChatController`, `views/nibe/`) — Legacy (laut Memory zu ignorieren).
- **Layout-Altlasten** (`layouts/OLD CODE`, `app.blade copy*.php`, `test*.blade.php`, `product.zip` im View-Baum).

## 6. Sichtbar, aber ohne belastbare Funktion (bzw. verdächtig)
- **„Rechnungen (Canvas-Hinweis)"** (`sidebar:749`) — gleiche Route wie „Rechnungen", `?open_canvas=1` löst laut IA-Doku nur ein `alert()` → **Platzhalter im Menü**.
- **„Datenbankbereinigung"** (`:1330`) — funktional, aber **ungeschützt** (`$canDeleteGarbage` berechnet, aber nicht als Gate genutzt).
- **„Warteschleife"** → `url('wating_leads')` — funktioniert, aber Tippfehler-Route (konsistent in Route + Sidebar).
- ⚠ **Einschränkung:** „belastbare Funktion" ist statisch nur begrenzt prüfbar. `navi-schwaechen-gesamt.md` meldet ~126 offene Funde inkl. Platzhalter/Crashes — reale Fertigstellung je Nav-Punkt muss zur Laufzeit verifiziert werden.

## 7. Doppelte / verwirrende Begriffe (im Code verifiziert)
| Begriff A | Begriff B | dasselbe? | Empfehlung |
|---|---|---|---|
| **Rechnungen** (`invoices`, 11 Z.) | **`deal_invoices`** (0 Z., tot, kein Nav) | teilweise (2 Rechnungssysteme) | später klären (S1-10 härtet Legacy) |
| **Rechnungen** | **Rechnungen (Canvas-Hinweis)** | ja (gleiche Route) | zusammenführen (IA-10) |
| **Aufträge** (`deals`) | **Projekte** (Planner vs. `projects`) | nein; „Projekt" doppeldeutig | trennen/umbenennen |
| **Kunden** (`new_leads`, 52 Z.) | **`customers`** (0 Z., aber `Customer`-Model aktiv) | ja, aber `customers` tot | Referenzen bereinigen (nicht droppen) |
| **Kunden** | **Kontakte** (`AllContactController`, Sammelliste) | nein | schärfen |
| **Leads** | **Anfragen** (`inquiries`) | nein; „Lead" meint **3 Dinge** (Kunde/Website-Lead/`leads`-Mailtabelle) | trennen/umbenennen |
| **Artikel** (`products`) | **Produkte / Artikel-Gruppen** (`article_groups`) | teilweise | einheitlich benennen (Glossar) |
| **Buchhaltung** (nur Doku) | **Finanzen** (Menü) | teilweise | zusammenführen (Finanzen als Dach) |
| **Kostenstellen** (0 Code) | **Abteilungen** (`departments`) | nein (Kostenstelle noch Konzept) | später klären |
| **Inventar/Bestand** | **Inventur** (Zählprozess, fehlt) | teilweise | später klären |
> Ratifiziertes `glossar.md` existiert bereits als Konsolidierungsbasis.

## 8. Fachlich falsch/unklar platzierte Bereiche (großteils schon in `ia-entscheidungen.md`)
- 🔴 **Rechnungen unter „Vertrieb › Aufträge"** statt Finanzen (IA-2 — dokumentiert, nicht umgesetzt).
- 🔴 **Finanzen unterentwickelt** (nur Förderungen/Filial-Betriebskosten/Ratenzahlungen; Rechnungen/OP/Belege fehlen dort) (IA-1).
- 🟠 **Wartung** unter Projekte (statt Service), **Betriebsmittel/Maschinen** unter Lager, **Kalkulationssätze** unter Einstellungen (obwohl Lohn-/Kostenbezug).
- 🟠 **„Tickets"** unter Support = intern **„Problem"-Modul** (Begriff ≠ Implementierung).
- 🟠 CRM mischt **Anfragen + Leads + Kunden** in einer Sektion, Grenze unscharf (IA-4).

## 9. Berechtigungsrisiken
- **`is_admin`-Bypass** + Rechte nur auf **Parent-Ebene** → 93 Unterpunkte meist ungeschützt (keine feingranulare Sicht).
- 🔴 **`InvoiceMiddleware` registriert, aber nirgends angewandt** (`navi-schwaechen-gesamt.md`) → jeder eingeloggte Nutzer kann Rechnungen sehen/ändern.
- 🔴 **„Datenbankbereinigung" ungeschützt** (Gate berechnet, nicht genutzt).
- **`users.name = employees.id`** als FK-Missbrauch (IA-13) → Rechte-Checks mal `$userId`, mal `$employeeId`.
- Dokumentierte P0-Sicherheitsfunde (anonyme Website-Leads, IMAP-Klartext, Angebots-Kalkulation ohne Login).

## 10. Fehlende Informationen (nicht raten)
- **Welche IA-Entscheidungen sind noch autoritativ?** `ia-entscheidungen.md` (2026-06-28) teils umgesetzt (Renames), teils nicht (IA-2/IA-10) — Soll-Stand unklar.
- **Laufzeit-Funktionsfähigkeit** je Nav-Punkt statisch nicht belegbar.
- **`$safeRoute`-Fallback maskiert** potenzielle Broken-Links by design.
- **Berechtigungsmodell** unvollständig erkennbar (nur Parent-Perms).
- **Bau-Roadmap** der „nicht gebauten" Module (Rechnungskette = Sprint 1; Kostenstellen/DATEV/Lohnarten offen).

## 11. Offene Klärungsfragen für die spätere Navi-Planung
1. Ist `ia-entscheidungen.md` der verbindliche Soll-Stand — insb. IA-2 (Rechnungen → Finanzen) und IA-10 (Canvas-Doppel-Eintrag entfernen)?
2. Soll `deal_invoices` (tot) endgültig raus, und wie mit Altdaten (S1-10)?
3. Wird „Buchhaltung/Finanzen" das Dach — und wo landet die Rechnungskette (S1-08/09) im Menü?
4. Bleiben `customers`/`Customer`-Model-Referenzen (0 Z.) bestehen oder werden sie bereinigt?
5. Wie granular sollen Rechte künftig sein (Unterpunkt-Ebene statt nur Parent)?
6. Sollen „Notdienst/SLA/Inventur/Mindestbestand/Lohnarten/Nummernkreise" gebaut werden (dann Menüplätze reservieren) oder nicht?
7. Wie wird die **4–5-Unterpunkte-Regel** umgesetzt, wo heute bis **9** Unterpunkte existieren (Artikel 9, Kontakte 8, Mitarbeiter 8, Anfragen 7, HR-Daten 7, Lager 7)?

## 12. Empfehlung für den nächsten Schritt
**Ja — ein eigenes „Navi-Konzept"-Ticket ist sinnvoll**, als **separater Planungsschritt** (nicht jetzt umgesetzt). Vor dem Zielbaum klären: (a) IA-Entscheidungen ratifizieren/aktualisieren (v. a. IA-1/2/10), (b) Begriffe final über das bestehende `glossar.md` fixieren, (c) entscheiden, welche „nicht gebauten" Module Menüplätze bekommen. Parallel unkritisch: **`Old/`-Toter-Code + Layout-Artefakte** bereinigen (berührt Navigation nicht).

---

## Ergebnis (klare Aussagen)
- **Wirklich vorhanden & nutzbar:** CRM/Leads/Kanban, Kontakte, Angebote, Aufträge, Projekte (Planner), Aufgaben/Termine, Tickets (=Problem), Wartung, Mitarbeiter/HR/Abwesenheit, Lohn&Vollkosten, Artikel/Lager/Lieferanten, **Rechnungen (`invoices`)**, Betriebskosten, Ratenzahlungen, Förderungen, Benutzer/Rechte, Import/Export.
- **Nur geplant / halbfertig:** Buchhaltung/DATEV/Kanzlei/SKR03-04/Kostenstellen (**nur Doku**), OP/Zahlungen/Storno (**Sprint 1 in Arbeit — nur S1-01 umgesetzt**), Lohnarten/Lohnvorbereitung/DATEV-Lohn, Inventur/Mindestbestand, Notdienst/SLA, Reklamationen (nur via Feedback), zentraler Log-Viewer, Nummernkreis-Modul.
- **Kritische Navigationspunkte:** doppelter Rechnungs-Eintrag; Rechnungen falsch unter Vertrieb; Finanzen unterentwickelt; ungeschützte Datenbankbereinigung; nicht angewandte `InvoiceMiddleware`.
- **Fehlende Infos:** verbindlicher IA-Soll-Stand; Laufzeit-Funktionsfähigkeit je Punkt; vollständiges Berechtigungsmodell; Bau-Roadmap der fehlenden Module.
- **Navi-Konzept-Ticket erstellen?** **Ja** — als nächster, klar abgegrenzter Planungsschritt (kein Umbau jetzt).

---
**Verwandte Doku:** `docs/software-audit/ia-entscheidungen.md` · `docs/navi-schwaechen-gesamt.md` · `docs/navigation-konzept-audit.md` · `docs/navigation-terminologie-audit.md` · `docs/glossar.md` · `docs/uebernahme/index-sprint-1-rechnungsprozess.md`.
