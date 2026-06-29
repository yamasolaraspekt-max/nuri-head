# Bericht — letzte Arbeitsschritte (CRM/Daten/UI-Spur)

**Stand:** 2026-06-29, ~23:50 · Branch `private/app-code-backup`.
Inhalt: was diese Spur (Sicherheit / Demo-Daten / CRM-Frontend / UI / Inventuren) zuletzt gemacht hat.

---

## 1. Navigation aufgeräumt (zuletzt)
- **„Partner" → „Kontakte"** im Hauptmenü + Untermenü manuell **zuklappbar** (hing vorher offen, da `open-by-route` per `!important`).
- **„brand" → „Hersteller"** als Anzeige (technischer Wert bleibt `brand` für die Filter).
- **Die zwei „System"** beseitigt → **vier eigene Haupt-Sektionen** mit Sub-Navi:
  **Konfiguration** (Arbeitsschritte/Projekt-Struktur/Filialen) · **Tools** (Werkzeuge/PV-Planer) · **System** (Systemwarnung/Feedback/KI-Wissen/DB-Bereinigung) · **Einstellungen** (Kalkulationssätze) · darunter **Wissen**.

## 2. CRM von „leer" auf „durchgängig befüllt" (Seeder)
| Bereich | Befüllt |
|---|--:|
| Angebote → Aufträge → Rechnungen (durchgängige Kette) | 29 → 14 → 10 |
| Umsatz (testbar je Abteilung) | 204.194 € |
| Aufgaben / Tickets | 45 / 15 |
| Master-Sets: Sets / Artikel-Komponenten / Set-Aufgaben | 13 / 44 / 49 |
| Assets/Inventar: Maschinen·Fahrzeuge·Werkzeuge·Mietobjekte / Fuhrpark | 18 / 6 |

Zuvor in dieser Spur: 16 Abteilungen + 50 Mitarbeiter (Gehalt/Vertrag/Urlaub/Krank/Hierarchie), Produkt-/Dienstleistungskatalog, 44 Artikel, Hersteller/Lieferanten + Sortiment + alle Partnertypen, 52 Kunden/Leads + Lead-Aktivität (Berichte/Termine/Notizen), Zuständigkeitsmatrix.

## 3. CRM-Crashes repariert (alle 500 → 200)
`/email_configuration` · `/new_lead_create` (Neukunden-Maske) · `/inquiry_type` · Kanban-Leads · `lead_product_lists` (Teilfix, ehrlich dokumentiert: nackter Sub-Endpunkt bleibt 500).

## 4. Read-only-Inventuren erstellt
- [bestandsaufnahme-24h.md](bestandsaufnahme-24h.md) — 78 Commits aller 4 Spuren kategorisiert.
- [cockpit-inventur.md](cockpit-inventur.md) — Machbarkeit Profit-Center-Cockpit.
- [crm-daten-inventur.md](crm-daten-inventur.md) — wo Seeder das CRM testbar machen.

## Offen / ehrlich
- **Defekte Anlage-Workflows** (Auftrag P1-16 / Angebot P1-18): der Seeder **umgeht** sie nur (Testdaten); für echten Datenfluss noch zu reparieren.
- **Nicht verlinkt, weil nicht vorhanden:** Akademie, Heizlast-/WP-/Fensterplaner-Tools.

Alles **idempotent** (`php artisan db:seed`), **konfliktfrei** (dateiweise committet, fremde Spuren unangetastet).
