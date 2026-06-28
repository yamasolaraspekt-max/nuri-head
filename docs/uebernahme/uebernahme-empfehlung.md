# Übernahme-Empfehlung: `playground` (Laravel) → `ticket`

**Stand:** 2026-06-28 · **Nur Vorschlag — keine Umsetzung.** In beiden Projekten ist nichts geändert/exportiert/kopiert. Scope: **nur Laravel + Blade** (React/TS ausgeschlossen). **Du wählst aus; danach folgt ein separater, modulweiser Export-/Anpassungsauftrag.**

---

## A) Risiko & Kollision — gilt für JEDE Übernahme (einmal verstehen)
`playground` ist ein **Prototyp** mit eigenen Annahmen. Vor jeder Modulübernahme nach `ticket` zu beachten:

1. **Auth-Modell unterschiedlich.** playground = saubere **RBAC** (`roles`/`permissions`, Middleware `permission:crm.manage`). ticket = `is_admin`-Bypass + `user_rolls` + `users.name = employees.id`. → Rechte-Checks jedes Moduls auf tickets `hasPermission()`/`user_rolls` **umverdrahten** (pro Modul mittlerer Aufwand).
2. **Eigene Tabellen (deutschsprachig).** playground bringt eigene Schemas mit (`customers`, `angebot_offers`, `hr_payroll_*`, `accounting_*`, `betriebsmittel*`). ticket hat eigene (`new_leads`, `employees`, `products`…). → Für **neue Domänen** (Buchhaltung, Disposition, Betriebsmittel) einfach **neue Tabellen parallel** anlegen (kollisionsarm). Für **überlappende** (Lager, Artikel, Kunden) drohen Doppelstrukturen → **mappen/zusammenführen** statt parallel.
3. **Routing/UI-Konvention.** playground = modulare `routes/modules/*.php` + API/Token. ticket = ein großes `web.php` + Blade/Session. **Gut:** playgrounds `views/modules/*`-Blade ist bereits server-rendered (CSRF/Session) — passt zu ticket. API-only-Controller müssen auf Blade-Rückgabe umgestellt werden.
4. **Framework/Stack kompatibel.** Beide Laravel 11 + MySQL. PHP 8.2 (pg) vs 8.4 (ticket) = abwärtskompatibel, unkritisch. **Reverb** (nur bei Kommunikation nötig) = zusätzliche Infrastruktur (eigener Prozess/Port).
5. **Prototyp-Reife.** playground hat **unterbrochene End-to-End-Ketten** (z. B. Angebot→Auftrag-Endpoint fehlt) und **offene GoBD-Punkte**. → Nur **solide Datenmodelle + Logik** übernehmen, kaputte Ketten **nicht** blind; in ticket testen/härten.
6. **Multi-Mandant.** playground ist mandantenfähig vorbereitet (`accounting_client_id`). ticket ist Einzelmandant → Mandanten-Scope beim Übernehmen **fixieren/vereinfachen**.

---

## B) Priorisierte Liste (sortiert nach Nutzen ÷ Aufwand — oben: viel Nutzen, leicht übernehmbar)

| # | Modul aus playground | Füllt welche ticket-Lücke | Nutzen | Aufwand | Risiko/Kollision | Empfehlung |
|---|---|---|---|---|---|---|
| 1 | **Dynamische Formular-Engine** (`formulare`, `formularbaukasten`, `formular-antworten`, `formular-berechnung`) | ticket-Checklisten-Formulare sind **buggy** (Tier-C); keine echte Engine | **hoch** | **mittel** | neue Tabellen (`dynamic_forms…`) — kollisionsarm; Smartrouting-Service mitnehmen | **übernehmen (anpassen)** |
| 2 | **Betriebsmittel / Fuhrpark** (`betriebsmittel`, `betriebsmittelarten`) | fehlt in ticket | mittel-hoch | **klein-mittel** | komplett neue Domäne → **kollisionsfrei**; nur Auth umverdrahten | **übernehmen** |
| 3 | **Disposition / Plantafel** (`dispositionen`, `personalressourcen`, `personalzuordnungen`, `kapazitaet`) | fehlt ganz (ticket hat nur Termine) | **hoch** (Montage-Geschäft) | mittel | neue Tabellen; Bezug auf Mitarbeiter/Projekte mappen | **anpassen-dann-übernehmen** |
| 4 | **Förderungen** (`foerderungen`, `foerder_parameter`) | ersetzt das **kaputte** BEG-Förderungen (Tier-C) | mittel | **klein** | kleines, klar abgegrenztes Schema | **übernehmen (anpassen)** |
| 5 | **Verträge** (`vertraege`) | fehlt in ticket | klein-mittel | **klein** | eigenständig, kollisionsarm | **übernehmen** |
| 6 | **HR + Lohnvorbereitung** (`mitarbeiter`, `arbeitsvertraege`, `hr-prozesse`, `lohnarten`, `lohnvorbereitung`, `zeiterfassung`, `ueberstunden`) | ticket-HR **dünn/kaputt** (Urlaubsanspruch 404) | **hoch** | **groß** | eigene `hr_*`-Tabellen; muss an tickets `employees` gekoppelt werden; Lohn = komplex | **anpassen-dann-übernehmen** |
| 7 | **Energie/PV-Rechner — Laravel-Teil** (`wp-auslegung`, `wr-auslegung`, `lastmanagement`, `lastprofil`, `konfigurator`, `produktkatalog`) | ticket hat nur `pvgis`-Route | **hoch** (PV-Kerngeschäft) | mittel-groß | viele Produkt-Tabellen; **3D-Planer-UI ist React → bleibt draußen** | **anpassen-dann-übernehmen** |
| 8 | **Lager-Buchungskette** (`wareneingaenge`, `materialentnahmen`, `lagerorte`, `bestaende`, `inventur`, `bestellungen`) | ticket-Lager teils/buggy (Übergaben Tier-C) | mittel-hoch | mittel | **überlappt** mit ticket-Lager → mappen/zusammenführen, nicht parallel | **anpassen (selektiv)** |
| 9 | **Reklamationen / Serviceaufträge** (`reklamationen`, dedizierter Service) | ticket: Wartung im falschen Bereich, keine Reklamation | mittel | mittel | überlappt mit ticket-Tickets/Wartung | **anpassen (selektiv)** |
| 10 | **Controlling / KPI / OKR** (`controlling-kpi`, `ziele`, `abteilungs-guv`) | fehlt in ticket | mittel-niedrig | mittel | neue Tabellen; braucht saubere Datenquellen | **übernehmen (wenn gewünscht)** |
| 11 | **Angebotsampel + Versionen** (Teil von `angebote`) | ticket-Angebote ok, aber ohne Pflichtdaten-Gate/Versionen | mittel | mittel | greift in vorhandenen ticket-Angebots-Flow ein → vorsichtig | **anpassen (Teil-Übernahme)** |
| 12 | **Buchhaltung / FiBu / DATEV / GoBD** (`buchhaltung` + ~30 Submodule) | **größte ticket-Lücke** (keine echte FiBu) | **sehr hoch (strategisch)** | **sehr groß** | 60+ Tabellen, Multi-Mandant, **Prototyp/GoBD noch nicht bestanden**, regulatorisch heikel | **anpassen-dann-übernehmen — als eigenes Teilprojekt** |
| 13 | **Reverb-Kommunikation** (`kommunikation`, `benachrichtigungen`) | ticket-Chat/E-Mail ist der schwächste Bereich | mittel | mittel-groß | neue Infrastruktur (Reverb-Server); ersetzt 2 ticket-Systeme | **anpassen (nur mit Infra-Entscheidung)** |

> **RBAC (`rollen`/`permissions`)** steht **nicht** in der Liste, weil es **kein Modul**, sondern ein **Querschnitt-Fundament** ist. playgrounds Rechte-Modell ist sauberer als tickets `is_admin`-Bypass — aber das ist ein **eigenes Sicherheits-Refactor** (berührt jede Route), kein „Modul übernehmen". Empfehlung: später als **Vorlage** für ein ticket-Auth-Refactor nutzen.

---

## C) Schnell-Empfehlung (wenn du sofort starten willst)
- **Leichte Quick-Wins zuerst (gut Nutzen, klein Aufwand):** #1 Formular-Engine, #2 Betriebsmittel, #4 Förderungen (ersetzt einen kaputten ticket-Bug), #5 Verträge.
- **Großer strategischer Hebel, aber eigenes Projekt:** #12 Buchhaltung/DATEV — **nicht** nebenbei, sondern als gehärtetes Teilprojekt (Prototyp-Risiko!).
- **Hoher Geschäftsnutzen, mittlerer Aufwand:** #3 Disposition, #7 PV-Rechner, #6 HR/Lohn.

## D) Nicht lohnend / ausgeschlossen
- **CRM-Kern, Anfragen, Angebote-Basis, Tickets, Termine/Kalender** — **ticket gewinnt** (live & ausgereift). Nicht übernehmen.
- **3D-Dachplaner** (React/Three.js), **TS-Connector-Framework**, **React-SPA** — **ausgeschlossen** (kein Laravel/Blade). Nur die zugehörigen Laravel-**Daten/Rechner** sind relevant (siehe #7).
- **Veranstaltungen, OCR/LLM-Erkennung** — Rand/unreif → später, geringe Priorität.

---
**Nächster Schritt (nach deiner Auswahl):** pro gewähltem Modul ein separater Auftrag: Migrationen + Models + Controller + Blade-Views aus `playground` sichten → Datenmodell auf ticket mappen → Rechte-Checks auf tickets Auth umstellen → in ticket testen/härten. **Erst nach deiner Freigabe.**
