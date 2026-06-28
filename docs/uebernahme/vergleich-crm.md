# Vergleich `ticket` ↔ `playground` (nur Laravel/Blade)

**Stand:** 2026-06-28 · Reine Lese-Analyse. Richtung bindend: Module wandern **playground → ticket**. `ticket` bleibt das Zentrum.
**ticket-Bewertung** stützt sich auf die vorhandenen Audits (Reifegrad-Heatmap, Crash-/Tier-B-/IA-Listen) — **nicht neu auditiert**. **playground-Bewertung** aus der Code-Kartierung (Laravel-Backend) + playgrounds eigener `BEFUND.md`.

Reifer = am Code beurteilt (Struktur, Vollständigkeit, Datenmodell-Tiefe). „Prototyp" heißt: breit gebaut, aber nicht produktiv gehärtet.

| Funktionsbereich | In `ticket`? | In `playground` (Laravel)? | Reifere Umsetzung | Einschätzung |
|---|---|---|---|---|
| **CRM: Leads/Kunden/Anfragen** | ✅ ja, breit & produktiv (Anfragen-Flow 🟢 ausgereift lt. Audit) | ✅ ja (kunden, kundenakte, anfragen, objects) | **ticket** (live, tief ausgebaut) | ticket gewinnt. playground bringt sauberes `customers→objects→projects`-Datenmodell, aber kein Übernahmegrund. |
| **Kommunikation/Chat/E-Mail** | ⚠️ teilweise — **schwächster ticket-Bereich**: 2 parallele IMAP-Systeme, 2 Chat-Systeme, Klartext-Passwörter (Audit) | ✅ ja, Reverb-WebSockets (kommunikation, benachrichtigungen) | **playground** (moderner, Realtime) | Kandidat: playgrounds Reverb-Chat als Ablösung des ticket-Wirrwarrs — aber Reverb ist neue Infrastruktur. |
| **Partner (Lieferanten/Marken/Banken…)** | ✅ ja, aber generische Maske ohne Fachfelder (Audit) | ✅ ja (lieferanten + lieferanten_artikel) | gemischt | ticket deckt es ab; playgrounds `lieferanten_artikel`-Verknüpfung ist sauberer. Kleiner Nutzen. |
| **Angebote** | ✅ ja (🟢 ausgereift lt. Audit) | ✅ ja + **Angebotsampel** (Pflichtdaten-Gate), Versionen, Sets/Vorlagen | **playground** (Ampel-Logik, Versionierung) | ticket-Basis ok; playgrounds Angebotsampel + Versionierung sind echte Mehrwerte. |
| **Aufträge / Auftragsbestätigung** | ✅ ja (🟢, 1 toter Doppel-Menüpunkt) | ✅ ja (auftraege, auftragsbestaetigungen, supplements, labor/material-lines) | **playground** (feinere Positionsstruktur) | Beide vorhanden; playground granularer. Mittlerer Nutzen. |
| **Rechnungen** | ✅ ja (unter Vertrieb) | ✅ ja (angebot_invoices) **+ an Buchhaltung gekoppelt** | **playground** (FiBu-Anbindung) | ticket hat Rechnungen, aber isoliert; playground bindet sie an die Buchhaltung. |
| **Buchhaltung (FiBu/DATEV/GoBD)** | ❌ **nein** — nur BEG-Förderungen + Ratenzahlungen (beide **buggy**, Tier-C) | ✅✅ **ja, riesig**: ~30 Module, doppelte Buchführung, DATEV-Export, UStVA/BWA/Bilanz/SuSa, Mahnwesen, OP, AfA | **playground** (mit Abstand) | **Größte echte Lücke in ticket.** Top-Kandidat — aber Prototyp (GoBD/DATEV-Test noch nicht bestanden). |
| **HR / Personal** | ⚠️ dünn — Urlaubsanspruch **kaputt** (404), Zeitpläne Authz auskommentiert (Audit) | ✅ ja (Profile, Verträge, Zeiten, Urlaub, Krank, Qualifikationen) | **playground** | playground deutlich vollständiger; echte Lücke in ticket. |
| **Lohn-Vorbereitung** | ❌ nein (nur Gehalt-Sichtrecht) | ✅ ja (Lohnarten, Lohnläufe, Lohnzeilen, Freigaben, Exporte) | **playground** | Echte Lücke; Top-Kandidat zusammen mit HR. |
| **Zeiterfassung / Überstunden / Bautagesberichte** | ⚠️ teilweise (Tagesberichte vorhanden) | ✅ ja (zeiterfassung, clock_events, ueberstunden, bautagesberichte) | **playground** | playground tiefer; ticket hat Ansätze. Mittel. |
| **Disposition / Plantafel / Ressourcen** | ❌ **nein** (nur Termine/Kalender, keine Einsatzplanung) | ✅ ja (dispositionen, disposition_tasks, personnel_assignments, kapazitaet) | **playground** | Echte Lücke — für ein Montage-Geschäft sehr wertvoll. Top-Kandidat. |
| **Termine / Kalender** | ✅ ja (🟢) | ✅ ja (termine) | **ticket** | ticket deckt es; kein Übernahmegrund. |
| **Projekte / Projekt-Akte** | ⚠️ teils (Notizen-Bereich teilweise) | ✅ ja, sehr tief (Akte, Profile/Phasen, Lohnkosten, Aufmaß, Tagesberichte) | **playground** | playgrounds Projekt-Akte ist umfangreicher (Phasen, Nachkalkulation). Mittel-hoch. |
| **Aufmaß / Montagevorbereitung** | ❌ nein | ✅ ja (feinaufmass, montagevorbereitung) | **playground** | Lücke; nischig aber nützlich. Mittel. |
| **Artikel / Stammdaten** | ✅ ja (🟢, 10 Punkte) | ✅ ja (artikel, gruppen, stückliste, technische-daten, import) | gemischt | Beide solide. playground hat saubereren Stücklisten-/Import-Pfad. Geringer Nutzen. |
| **Lager / Wareneingang / Materialentnahme / Inventur** | ⚠️ teilweise (Lager 🟡, Übergaben buggy lt. Audit) | ✅ ja, vollständig (bestellungen, wareneingaenge, materialentnahmen, bestaende, inventur) | **playground** | playgrounds Lagerbuchungs-Kette ist vollständiger; ticket-Lager hat Bugs. Mittel-hoch. |
| **Einkauf / Bestellungen** | ⚠️ teils (Bestellanfragen vorhanden) | ✅ ja (bestellungen/bestellpositionen ↔ lieferanten_artikel) | **playground** | playground runder. Mittel. |
| **Betriebsmittel / Fuhrpark** | ❌ nein (nur „Assets/Center"-Ansatz) | ✅ ja (Fahrzeuge/Maschinen, Wartung, Reservierung, Prüfpläne) | **playground** | Saubere Lücke, neue Domäne → kollisionsarm. Guter Kandidat. |
| **Kundendienst: Tickets** | ✅ ja (🟢 ausgereift) | ✅ ja (tickets, nachrichten, notizen) | **ticket** | ticket deckt es gut ab → playground-Tickets nicht lohnend. |
| **Reklamationen / Serviceaufträge** | ⚠️ teils (Wartung unter Projekte) | ✅ ja (reklamationen, serviceauftraege) | **playground** | playground hat dedizierte Reklamation/Service. Mittel. |
| **Wartung** | ⚠️ ja, aber im falschen Bereich (IA-Liste) | (teils via service) | **ticket** | ticket hat Wartungs-Checklisten; eher IA-Frage als Übernahme. |
| **Dynamische Formulare / Checklisten** | ⚠️ Checklisten-Formulare **buggy** ($formula, Tier-C) | ✅✅ ja, echte Engine (Baukasten, Antworten, Berechnung, Smartrouting) | **playground** | Echte Lücke; playgrounds Form-Engine ist ein Highlight. Top-Kandidat. |
| **Energie/PV-Tools (Daten/Rechner)** | ❌ fast nein (nur `pvgis`-Route, nicht im Menü — IA-Liste) | ✅ ja (Heizlast, PV/WP/WR-Auslegung, Lastmanagement, Produktkatalog) | **playground** | Lücke; für PV-Geschäft wertvoll. **3D-Planer-UI bleibt React = ausgeschlossen**, nur Laravel-Rechner/Daten. Hoch. |
| **Förderungen** | ⚠️ BEG-Förderungen **buggy** (Tier-C) | ✅ ja (foerderungen, foerder_parameter) | **playground** | playground kann das kaputte BEG-Modul ersetzen. Klein-mittel. |
| **Verträge** | ❌ nein (kein dediziertes Modul) | ✅ ja (vertraege) | **playground** | Kleine, saubere Lücke. Klein. |
| **Controlling / KPI / OKR / Abteilungs-GuV** | ❌ nein | ✅ ja (controlling-kpi, ziele, abteilungs-guv) | **playground** | Nice-to-have; Management-Ebene. Mittel-niedrig. |
| **Veranstaltungen** | ❌ nein | ✅ ja (veranstaltungen) | **playground** | Randmodul. Niedrig. |
| **Beleg-/Bild-Erkennung (OCR)** | ❌ nein | ⚠️ teils (erkennung, LLM-Proxy — z. T. Prototyp) | **playground** | Zukunftsthema, noch unreif. Niedrig/später. |
| **Rechte & Rollen (Auth-Fundament)** | ⚠️ `is_admin`-Bypass + `user_rolls`, `users.name=employees.id`-Hack (Security/IA-Befund) | ✅ saubere **RBAC** (roles/permissions/Pivot, Middleware-Gating) | **playground** | playgrounds Modell ist sicherer — aber **kein Modul**, sondern Querschnitt. Eigenes Refactor, nicht „übernehmen". |
| **Realtime (WebSockets)** | ❌ nein | ✅ Reverb | **playground** | Infrastruktur, kein Modul. Nur mit Kommunikation/Benachrichtigung sinnvoll. |
| **Dashboard / Arbeitsbereich** | ⚠️ ja, aber 13k-Zeilen-View + Fatals (Tier-C) | ✅ schlankere Blade-Module | gemischt | ticket-Dashboard ist eigenständig; eher reparieren als ersetzen. |

## Kernaussagen des Abgleichs
1. **ticket ist stark im klassischen CRM-Vertrieb** (Leads→Anfragen→Angebote→Aufträge→Tickets) — hier **gewinnt ticket**, kein Übernahmebedarf.
2. **playground füllt genau die ticket-Lücken**, die die Audits aufgezeigt haben:
   - **Buchhaltung/FiBu/DATEV** (ticket hat faktisch keine) — größter Hebel.
   - **HR + Lohnvorbereitung** (ticket dünn/kaputt).
   - **Disposition/Plantafel** (ticket fehlt ganz).
   - **Dynamische Formular-Engine** (ticket-Checklisten buggy).
   - **Lager-Buchungskette + Betriebsmittel/Fuhrpark** (ticket teils/fehlt).
   - **Energie-/PV-Rechner** (ticket nur pvgis) — ohne den React-3D-Planer.
3. **Aber:** playground ist **Prototyp** mit eigener Auth (RBAC) und eigenen, deutschsprachigen Tabellen. Jede Übernahme heißt: **Datenmodell + Rechte-Checks an ticket anpassen** (siehe `uebernahme-empfehlung.md`, Abschnitt Risiko/Kollision).
