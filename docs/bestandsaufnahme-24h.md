# Bestandsaufnahme — letzte 24 Stunden

**Stand:** 2026-06-29, ~23:25 · Branch `private/app-code-backup` · Quelle: Git-Historie (`git log --since="24 hours ago"`).
Zweck: ehrlicher Überblick, **was alle parallel laufenden Agenten** in den letzten 24 h gemacht haben.

> **78 Commits** in 24 h, konfliktfrei (jede Spur dateiweise committet, saubere Fast-Forward-Pushes).
> Aufteilung: **56× fix**, **14× feat (Seeder/Demo)**, **8× docs (Inventuren)**.

---

## 1. Was wurde gemacht — nach Kategorie

| Kategorie | Anzahl | Inhalt |
|---|--:|---|
| **🔒 Sicherheit (P0)** | 13 | Komplettes Sicherheits-Fundament: anonyme Lecks geschlossen, IMAP-Passwörter verschlüsselt, Rechte-Eskalation (make_admin) dicht, IDOR (Aufgaben/Anfragen/Zeitpläne), Invoice-/Produkt-Gates, Reports-Übersicht. P0-01…P0-13. |
| **💥 Crashes (P1)** | 28 | ~28 reparierte 500er/tote Pfade quer durch alle Module (s. u. nach Spur). |
| **🗄️ Datenverlust (P2)** | 4 | Inquiry-Status erhalten, Lagerausgabe-Storno bucht zurück, Urlaubs-Resttage idempotent, Urlaubsjahr-Status. |
| **🛡️ XSS/CSRF (P3/P4/P5)** | 7 | HTML-Sanitizer (mews/purifier) in 5 Views, Lösch-Routen auf DELETE+CSRF (HR/Produkt), externe-Firma-Anlage, is_active-Toggle, employeeId()-Helper. |
| **🌱 Seeder / Demo-Daten** | 14 | Komplette Demo-Firma + Daten (s. Ist-Stand). |
| **🎨 UI / Navi** | 3 | „Partner"→„Kontakte" (+ Untermenü zuklappbar), „brand"→„Hersteller", zwei „System"→„Konfiguration"+„Tools & Wissen". |
| **📋 Docs / Inventuren** | 8 | Read-only Analysen (s. u.). |

---

## 2. Nach Arbeits-Spuren (parallel, konfliktfrei)

Es liefen mehrere Spuren parallel (laut Parallel-Protokoll ~4 Ausführer-Spuren + Planer). Grob nach Thema:

**Spur A — Sicherheit + Demo-Daten + CRM-Frontend + UI (diese Instanz):**
- Sicherheit P0-01…13 (Fundament).
- Alle Demo-Seeder: 16 Abteilungen + 50 Mitarbeiter (Gehalt/Vertrag/Urlaub/Krank/Hierarchie), Produkt-/Dienstleistungskatalog, 44 Artikel, Hersteller/Lieferanten + Sortiment, operative Daten (Kunden/Anfragen/Projekte, alle Gewerke), Lead-Aktivität (Berichte/Termine/Notizen), Zuständigkeitsmatrix, **CRM-Pipeline (Angebot→Auftrag→Rechnung)**, **Master-Sets**, **Assets/Inventar**.
- CRM-Crashes: email_configuration, new_lead_create, inquiry_type, lead_product_lists, Kanban-Leads.
- Partner-Profile (Stammdaten/Sortiment), Navi-Umbenennungen.
- Inventuren: Cockpit, CRM-Daten, Funktionstest-Crash-Backlog.

**Spur B — Personal / HR:**
- P1-22/23/23b (Urlaubsanspruch-Namespace, Qualifikation-Löschen + Ownership/IDOR), Mitarbeiter-CV-Crash.
- P2-31/32 (Urlaub-Resttage, Urlaubsjahr-Status), P4-41 (is_active), P5-44 (employeeId), P4-CSRF (HR-Lösch-Links).

**Spur C — Lager / Finanzen:**
- P1-20/24 (Übergaben tot: Asset-Modell, restore), P2-30 (Lagerausgabe-Storno bucht zurück).
- P1-Finanzen: AssetInstallment/Ratenzahlung (Null-Guards, Formular-Action), P4-CSRF (Produkt-Lösch).

**Spur D — Vertrieb / CRM-Crashes:**
- P1-17/19/21/25 (Offer-Comments-Routen, Inquiry-Kanban-Status, Rabattgruppen-Redirect, Sprache-Löschen 405).
- P1-Vertrieb (OfferController@show), P1-CRM (brand departments, all-contacts, Leadliste-Filter, global-restore, Lead-Detaildaten), Dashboard-Crash, P3-XSS.

**Planer:** Docs/Vision (Zielbild objekt-zentriertes CRM), Controlling-Bestandsaufnahme, Dashboard-Analyse, Navi-Schwächen, Parallel-Protokoll.

---

## 3. Daten-Ist-Stand (Demo „Solar Aspekt Nord GmbH")

| Bereich | Anzahl | | Bereich | Anzahl |
|---|--:|---|---|--:|
| Abteilungen | 16 | | Angebote | 29 |
| Mitarbeiter | 51 | | **Aufträge** | 14 |
| Produkte/Gewerke | 13 | | **Rechnungen** | 10 |
| Artikel | 44 | | Aufgaben | 45 |
| Partner (Hersteller etc.) | 45 | | Tickets | 15 |
| Lieferanten | 9 | | Master-Sets | 13 |
| Kunden/Leads | 52 | | Assets/Inventar | 18 |
| Anfragen | 40 | | Termine | 80 |
| Projekte | 31 | | **Umsatz (Rechnungen)** | **204.194 €** |

→ Die gesamte CRM-Kette (Anfrage → Lead → Angebot → Auftrag → Rechnung) ist durchgängig befüllt; Umsatz/Auftragsvolumen je Abteilung rechenbar.

---

## 4. Offene Befunde / Read-only-Inventuren (zum Nachschlagen)
- [funktionstest-crashes.md](funktionstest-crashes.md) — 44 Crashes kategorisiert (viele inzwischen gefixt; Rest = Sub-Endpunkte/Designfragen).
- [cockpit-inventur.md](cockpit-inventur.md) — Machbarkeit Profit-Center-Cockpit (Fundament da, Faktenbasis Umsatz/Kosten/Stunden teils noch dünn).
- [crm-daten-inventur.md](crm-daten-inventur.md) — wo Seeder das CRM testbar machen (großteils umgesetzt).
- [controlling-bestandsaufnahme.md](controlling-bestandsaufnahme.md) — Kostenrechnung/Kostenstelle (fehlt, eigenes Vorhaben).
- [zielbild-objekt-zentriertes-crm.md](zielbild-objekt-zentriertes-crm.md) — Langfrist-Vision + zwingende Reihenfolge.

---

## 5. Fazit
In 24 h ist **das Sicherheits-Fundament komplett**, **~28 Alltags-Crashes** quer durch alle Module repariert, **Datenverlust-Lecks** geschlossen und das **CRM von leer auf durchgängig befüllt** gebracht (inkl. Umsatz-Kette, Sets, Inventar) — alles **konfliktfrei parallel**. Reproduzierbar über `php artisan db:seed`.

**Noch offen (bewusst):** defekte Anlage-Workflows (Auftrag/Angebot — Seeder umgeht sie nur), die größeren Vorhaben aus den Inventuren (Kostenrechnung/Cockpit, objekt-zentriertes Datenmodell), sowie Funktionen ohne Backend (Akademie, Heizlast/WP/Fenster-Tools). Diese sind **Produkt-/Architektur-Entscheidungen** — sie gehören zur Roadmap, nicht zur Crash-Reparatur.
