# IA-Entscheidungen (Informationsarchitektur) — später entscheiden, NICHT fixen

**Stand:** 2026-06-28 · Aus dem Navigations-Konzept-Audit herausgelöste **echte Design-/Struktur-Entscheidungen**. Das sind **keine Bugs** — sie erfordern eine bewusste Produkt-Entscheidung, keinen schnellen Code-Fix. **Reine Doku — kein Code geändert.**

## Struktur / Gruppierung
| # | Thema | Problem | Möglicher Weg | Schwere |
|---|---|---|---|---|
| IA-1 | **Finanzen unterentwickelt** | Nur 3 Unterseiten; Rechnungen, Wirtschaftlichkeits-Kalkulation, Wareneingänge liegen woanders bzw. gar nicht im Menü | Rechnungen/Kalkulation/Wareneingänge unter Finanzen bündeln (oder „Vertrieb & Finanzen") | 🔴 hoch |
| IA-2 | **Rechnungen unter „Vertrieb" versteckt** | `admin/invoices` hängt unter Vertrieb › Aufträge statt bei Finanzen | Nach Finanzen verschieben | 🔴 hoch |
| IA-3 | **PV-Tools fehlen im Menü** | `pvgis`, `climate/*`, `economic-calculations`, `costing-sets`, `backup-generators` existieren als Routen, aber **kein Menüeintrag** | Eigenen Bereich „PV-Tools" / „Technik & Planung" einführen | 🔴 hoch |
| IA-4 | **CRM ↔ Vertrieb unscharf** | Anfragen im CRM, Angebote/Aufträge im Vertrieb, Rechnungen wieder woanders — Pipeline zerrissen | Klare Pipeline Anfrage→Angebot→Auftrag→Rechnung (eigener Bereich „Verkaufsprozess") | 🟠 mittel |
| IA-5 | **Wartung im falschen Bereich** | „Wartung" unter Projekte, ist aber After-Sales/Service; „Support" hat nur Tickets | Wartung zu Support/„Service & Support" oder eigenem „After-Sales" | 🟠 mittel |
| IA-6 | **Partner-Menü zu breit & flach** | 8 Untereinträge; „Alle Kontakte"/„Alle Marken" machen Spezialkategorien redundant | Architekten/Banken/Versicherungen als Filter/Tabs statt eigene Punkte (8→4-5) | 🟡 niedrig |
| IA-7 | **Personal: zu viele Stammdaten-Punkte** | HR-Daten listet System-Konfig (Vertragstypen, Sprachen, Länder, Steuerklassen) als 7 Menüpunkte | Konfig-Stammdaten nach Admin › System verschieben | 🟡 niedrig |
| IA-8 | **Arbeitsbereich zu schmal** | Nur Dashboard + Lead-Kanban; persönliche Tools (Meine Aufgaben/Kalender/Notizen) verstreut | Persönliche Einstiegspunkte hier bündeln — oder Bereich auflösen | 🟡 niedrig |

## Navigations-Muster
| # | Thema | Problem | Möglicher Weg |
|---|---|---|---|
| IA-9 | **Leere MAIN-Header ohne URL (9×)** | Eltern-Menüpunkte (Kommunikation, Leads/Kunden, Partner, Aufträge, Notizen, Termine, Wartung, Artikel, Lager) führen nirgendwo hin — reine Gruppen-Labels | Pro Bereich eine Übersichts-/Landing-Seite — **oder** bewusst als reine Aufklapp-Header belassen (dann ist es kein Mangel) |
| IA-10 | **Doppelter Rechnungs-Eintrag** | „Rechnungen" und „Rechnungen (Canvas-Hinweis)" zeigen auf dieselbe Route (nur `?open_canvas=1`); der zweite löst nur einen `alert()` aus | Canvas-Hinweis aus dem Menü nehmen, Canvas per Deeplink/Button öffnen |
| IA-11 | **Zwei Chat-Systeme** | „Bitrix24-Chat" zeigt auf die **alte, nur-lesende** Legacy-Ansicht; der moderne interne Chat liegt unter anderem Routennamen | Entscheiden, welches Chat-System bleibt; Navi auf das aktive zeigen, Legacy entfernen |
| IA-12 | **Mobile-Suche ausgeblendet** | Sidebar-Suchbutton ist auf `max-width:767px` per `display:none` versteckt — keine Schnellnavigation auf Mobil-Web | Persistenter Suchbutton / FAB / Bottom-Sheet auf Mobile |

## Architektur-Hinweis (betrifft Navi-Rechte)
| # | Thema | Problem | Möglicher Weg |
|---|---|---|---|
| IA-13 | **`users.name = employees.id`** | Das `name`-Feld wird als Fremdschlüssel missbraucht; Berechtigungen prüfen mal `$userId`, mal `$employeeId` → Risiko falsch gewährter/verweigerter Menürechte | Eigene FK-Spalte `employee_id` in `users` (Migration); Rechte-Checks vereinheitlichen. **Kein Quick-Fix — Datenmodell-Entscheidung.** |

**Summe IA-Entscheidungen: 13.**

---
**Nicht in diesen drei Listen:** Reine UX-/Qualitäts-Punkte (gemischt DE/EN-Texte, fehlende Empty-States, keine Pagination, veraltetes Bootstrap-UI, generische Partner-Formulare ohne Fachfelder) bleiben im Gesamtbericht `navigation-konzept-audit.md` dokumentiert — weder Crash noch Security noch IA-Struktur.
