# Navigations- & Modulvergleich: System A (ticket) ↔ System B (Playground-CRM)

**Stand:** 2026-07-03 · **Reine Read-only-Inventur & Vergleich — nichts geändert, kein Commit, kein Zielbaum.**
**System A:** `/Users/yamanuri/Documents/ticket` (Laravel, monolithisch). **System B:** `/Users/yamanuri/Documents/Playground/backend-laravel` (Laravel, modular; React-SPA abgerissen).
**Quellen:** 4 Read-only-Agenten (System-A-Sidebar+Module [Vorbericht], System-A-Topbar/Quick/Dashboard, Playground-Nav, Playground-Module) · Befunde mit Datei:Zeile belegt · Routen gegen `route:list` geprüft.
**Grundlage System A:** `docs/navigation-ist-befund-inventur.md`. **Leitlinie (nur später):** max. 4–5 Unterpunkte je Hauptbereich.

---

## 1. Kurzfazit
**Playground ist navigatorisch sauberer strukturiert, System A hat mehr (teils versteckte) Zugriffswege.** Playground: **eine** führende Blade-Sidebar (13 Sektionen · 122 Punkte · Tiefe 2 · **alle 122 Routen existieren**), gespiegelt auf Desktop/Icon-Rail/Mobile aus **einer** Quelle, klare Fachsektionen — insbesondere **Buchhaltung als eigene Top-Sektion (28 Submodule)**. System A: hartcodierte Sidebar (15/37/**93**) **plus** eine große **zweite, weitgehend ungeschützte Navigationsebene** aus Topbar-Quick-Menüs, Quick-Sider-Panel (~22 Kacheln), Mobile-FAB und einem **benutzerkonfigurierbaren Dashboard-Shortcut-System** — dazu eine **globale Suche (⌘K)**, die Playground fehlt.
**Wichtige Einordnung:** Playgrounds Buchhaltung **wirkt fertiger, als sie ist** — sie ist **Prototyp und produktiv hart gesperrt** (`FINANZ_EXPORT_GESPERRT=true`, Default-Deny). System A hat davon **fast nichts** (nur Rechnungen `invoices` + Sprint-1-Härtung S1-01/02). **Keines** der Systeme ist buchhalterisch produktionsreif.

## 2. Was am vorherigen Bericht fehlte
Der Vorbericht (`navigation-ist-befund-inventur.md`) erfasste System-A-**Sidebar + Module**, aber **nicht**: (a) System-A **Topbar/Quick-Menü/Quick-Sider/Dashboard-Shortcuts/globale Suche** (die „zweite Navigation"), und (b) **System B (Playground)** komplett. Beides ist hier ergänzt.

## 3. Fundorte der Navigation — System A
| Fundort | Datei | Rolle |
|---|---|---|
| Sidebar (führend) | `resources/views/admin/layouts/sidebar.blade.php` (hartcodiertes `$sidebarSections`) | führende Navigation |
| Topbar (Suche, Aktivitäten, Glocke, Report-Hub, „Neu anlegen", „Quick Menu") | `admin/layouts/app.blade.php:4556–4816` | Teil-/Quick-Navigation |
| Quick-Sider „Schnellzugriff" (~22 Kacheln, 2 Grids) | `app.blade.php:4962–5138` | Quick-Menü |
| Mobile-Bottom-Nav + FAB | `app.blade.php:5149–5221` | mobile Navigation |
| Profil-/User-Footer-Dropdown | `app.blade.php:4891–4955` | Kontextnavigation |
| Dashboard-Shortcut-System (benutzerkonfigurierbar) | `Dashboard/UserDashboardShortcutController.php` + `dashboard/employee/mobile.blade.php` | Schnellzugriffe |
| Globale Suche (⌘K) | `app.blade.php:4584–4603/7485` → `contacts.global.search` | globales Suchmenü |

## 4. Fundorte der Navigation — Playground
| Fundort | Datei | Rolle |
|---|---|---|
| Sidebar (führend, einzige) | `resources/views/layouts/app.blade.php:30–179` (`$nav`-Array) | führende Navigation (Desktop-Full + Icon-Rail + Mobile-Overlay aus **einer** Quelle) |
| Rechtes Kontext-Panel | `app.blade.php:310–314` (konditional `@section('context')`) | seitenspezifische Kontextnavigation |
| Icon-Rail (eingeklappt) | `app.blade.php:233–243` (erster Link je Sektion) | Schnellzugriff (keine eigenen Ziele) |
| React-Nav (MainSidebar/Topbar/funktionsmenue.ts) | nur `.claude/worktrees/**` + `app.jsx` | **tot/verwaist** (SPA abgerissen) |
> **Kein** Topbar-Quick-Menü · **keine** globale Suche/Command-Palette · **kein** User-/Logout-Menü im Layout.

## 5. Vollständige Navigation — Playground (13 Sektionen / 122 Punkte, Tiefe 2, alle Routen existieren)
1. **Übersicht:** Dashboard · Benachrichtigungen
2. **Vertrieb & CRM:** Anfragen · Kontakte · Kunden · Kontaktarten · Kontaktvorlagen · Lieferanten
3. **Angebote & Aufträge:** Angebote · Angebots-Arbeitsraum · Angebotsvorlagen · Angebots-Sets · Aufträge · Auftrags-Arbeitsraum · Auftragsbestätigungen · **Rechnungen**
4. **Projekte & Baustelle:** Projekte · Projektprofile · Projekt-Lohnkosten · Bautagesberichte · Entwicklungsberichte · Feinaufmaß · Aufgaben · Aufgabenmaterial · Aufgabennachweise
5. **Lager & Artikel:** Artikel · Artikelgruppen · Artikelimport · Technische Spezifikationen · Leistungen · Lagerorte · Bestellungen · Wareneingänge · Materialentnahmen · Inventur · Inventur-Sessions
6. **Energie & Auslegung:** Anlagenkonfigurator · WR-Auslegung · WP-Auslegung · Wärmepumpen · Produktkatalog · Lastprofile · Wallbox-Lastmanagement · 3D-Dachplaner · Förderungen
7. **Buchhaltung (28):** Übersicht · Journal · Belege · Bank · Konten · Kasse · Offene Posten · Partner(Deb./Kred.) · Dimensionen · Buchungsvorschläge · BWA · Bilanz · SuSa · Projektrechnung · Kostenstellenrechnung · Rechnungseingang&-ausgang · UStVA · Anlagenbuchhaltung · Mahnwesen · Monatsabschluss · Perioden · Nummernkreise · Fristen · GoBD · Prüfzentrum · Steuerberater · *(konditional)* Revision·Gate-Protokoll · Revision·GoBD-Protokoll
8. **Personal & HR (16):** Mitarbeiter · HR-Prozesse · Arbeitsverträge · Lohnvorbereitung · Lohnarten · Zeiterfassung · Überstunden · Personalnachweise · Personalressourcen · Personalzuordnungen · Teams · Gewerke · Abteilungen · Abteilungsübersicht · Abteilungs-GuV · Niederlassungen
9. **Kundendienst & Einsatz:** Tickets · Reklamationen · Plantafel · Termine · Montagevorbereitung · Betriebsmittel(+Dashboard/Reports/Prüfplanung/Prüfreport) · Betriebsmittelarten
10. **Controlling:** Controlling-KPI · Ziele · Innenaufträge · Kapazität&Produktivität · Verträge
11. **Formulare & Kommunikation:** Formulare · Formularbaukasten · Formular-Antworten · Kommunikation · Veranstaltungen
12. **Stammdaten (Energie/Produkt, `/pilot/`):** Produkte · Hersteller · Ziegeltypen · Eindeckung · Dämmstoffe · Montagesysteme · Objekte
13. **System:** Einstellungen · Design-System · Rollen&Rechte · Systemmodule · Module · Uploads · Erkennung

## 6. Kurzreferenz Navigation — System A
15 Sektionen · 37 Hauptpunkte · **93 Unterpunkte** · Tiefe 2 (Details: `navigation-ist-befund-inventur.md`). Sektionen: Arbeitsbereich · Berichte · CRM · Vertrieb · Projekte · Support · Personal · Artikel&Lager · Finanzen(nur 3) · Admin · Konfiguration · Tools · System · Einstellungen · Wissen. **Rechnungen** hängen unter **Vertrieb › Aufträge**; **Finanzen** hat nur Förderungen/Filial-Betriebskosten/Ratenzahlungen.

## 7. Topbar- & Quick-Menü-Inventur — System A
- **Globale Suche (⌘K):** Topbar-Input → `/global-search` (`contacts.global.search`, `auth`); nur Entitäts-Treffer, **keine** Command-Palette.
- **„Neu anlegen" (+):** Anfrage · Kunde · Hersteller · Lieferant · Produkt · Personal (6 `$createLinks`, **ungated**).
- **„Quick Menu"-Dropdown:** Alle Apps · Dashboard · Nachrichten · Kalender · Aufgaben · KI Chat (ungated).
- **Quick-Sider „Schnellzugriff" (~22 Kacheln):** Dashboard · Suche · **Zeiterfassung (Platzhalter `href=#`)** · Nachrichten · Kalender · Feinaufmaß · Kapazität · Karte · KI Chat · Breaking News · Benachr. · Logout · Ticket×3 · Aufgaben · Team-Aufgaben · Termine · Prozess(Kanban) · Kontakte · Abteilung(dynamisch `quick.departments`). **Alle ungated.**
- **Report-Hub** (Topbar) → `admin.report.index` (kein Gate im Markup).

## 8. Topbar- & Quick-Menü-Inventur — Playground
**Keines.** Topbar enthält nur Menü-Toggle, Sidebar-Einklapp-Button, Seitentitel, statischen Text. **Kein** Quick-Menü, **keine** globale Suche, **kein** User-/Logout-Menü im Layout. Einzige dynamische Elemente: rechtes Kontext-Panel (konditional) + 2 rollenabhängige Buchhaltung-Revisionslinks.

## 9. Dashboard-/Schnellzugriffs-Inventur — System A
**Benutzerkonfigurierbares Shortcut-System** (`UserDashboardShortcutController`): Default-Katalog mit Gates über `user_rolls`, **aber**: **„Angebote" (`admin/offers`) und „Aufträge" (`deal.all.list`) sind `permission_key=null` → ungated**. Nutzer können **eigene Shortcuts mit beliebiger URL** anlegen (optionaler `permission_key`; ohne Key **kein** Gate). Zusätzlich AJAX-Daten-Widgets (keine Navlinks).

## 10. Dashboard-/Schnellzugriffs-Inventur — Playground
**Keine** hartcodierten Dashboard-Shortcut-Kacheln und **kein** benutzerkonfigurierbares Shortcut-System gefunden. Dashboard = `/app` (eigene Sektion „Übersicht").

## 11. Modul-Inventur — Playground (verdichtet)
Fast alle Fachmodule haben Route+Controller+View+Model (Prototyp-Charakter → durchweg „fachlich teilweise nutzbar"). Kernblöcke:
- **CRM/Vertrieb:** Kunden(`customers`) · Anfragen(`inquiries`) · Angebote(`offers`/`offer_versions`) · Aufträge(`orders`) · Auftragsbestätigungen · Verträge · Angebotsampel(`OfferTrafficLightService`).
- **Projekte:** Projekte(+~25 Sub-Models) · Phasen/Profile · Feinaufmaß · Bautagesberichte · Montagevorbereitung · Projekt-Lohnkosten · Disposition/Plantafel · Ressourcen/Kapazität.
- **Service:** Tickets · Reklamationen · Betriebsmittel/Fuhrpark(8 Ctrl, Prüf-/Report-Suite) · Wartung(via Prüfplan); **Serviceaufträge dormant** (Model, keine Route/Nav); **kein SLA-Model**.
- **Finanzen/Buchhaltung (28 Submodule, ~50 Models, ~65 Services):** In/Out-Rechnungen · OP · Zahlungen · Journal · SKR03/04(`chart_of_accounts`) · Kostenstellen/-träger · Steuerschlüssel · Mahnwesen · Buchungsvorschläge · **DATEV-EXTF** · XRechnung · GoBD/Festschreibung · UStVA/BWA/Bilanz/SuSa · AfA/Anlagen · Bank/Kasse · Prüfzentrum/Gate-Protokoll. **Alle produktiv HART GESPERRT (Prototyp, nicht abgenommen).**
- **Lager:** Artikel · Artikelgruppen · Stückliste · Lieferanten · Bestellungen · Wareneingang · Materialentnahme · Lagerorte · Bestände · Inventur · DATANORM/Connector-Import.
- **Personal/Lohn:** Mitarbeiter · Arbeitsverträge · HR-Prozesse · Zeiterfassung/Überstunden · Lohnarten(`hr_wage_types`) · Lohnvorbereitung(`hr_payroll_*`) · Freigabe-Workflow · DATEV-Lohn(Skelett, unklar).
- **Playground-eigen (System A hat nicht):** Betriebsmittel/Fuhrpark · Formular-Engine(`dynamic_forms`) · Energie/PV-/WP-/3D-Dachplaner-Suite · Controlling/KPI/OKR/Ziele · Reverb-Kommunikation · Veranstaltungen · Beleg-OCR-Erkennung · Abteilungs-GuV · Innenaufträge/Kostenträger · XRechnung.

## 12. Direkte Gegenüberstellung nach Fachbereichen
| Fachbereich | System A | Playground | A-Platz | B-Platz | Verhältnis | Bewertung / Relevanz |
|---|---|---|---|---|---|---|
| CRM/Kontakte | Kunden(`new_leads`), Kontakte, Kanban | Kunden(`customers`), Kontakte, Kontaktarten/-vorlagen | CRM | Vertrieb&CRM | ähnlich | beide ok; B bündelt Kunde+Kontakt in einer Sektion |
| Leads/Anfragen | Anfragen + Leads/Kunden getrennt | Anfragen + Kunden | CRM | Vertrieb&CRM | ähnlich; „Lead"-Mehrdeutigkeit nur A | A hat 3-fach-„Lead"-Problem |
| Angebote | Angebote/Assistent/Vorlagen | Angebote/Arbeitsraum/Vorlagen/Sets | Vertrieb | Angebote&Aufträge | ähnlich | B mit „Arbeitsraum"-Konzept |
| Aufträge | `deals` (Aufträge) | `orders` (Aufträge) | Vertrieb | Angebote&Aufträge | gleich (andere Tabelle) | — |
| **Rechnungen** | `invoices` unter **Vertrieb** (+Canvas-Doppel) | Rechnungen unter Angebote&Aufträge **+ volle Buchhaltung** | Vertrieb | Vertrieb **+ Buchhaltung** | **anders platziert** | **B plausibler** (hat Buchhaltungs-Heimat) |
| Buchhaltung/DATEV | **fehlt** (nur Doku/Sprint 1) | **eigene Sektion, 28 Submodule** (hart gesperrt) | – | Buchhaltung | **fehlt in A** | B als Vorbild-Struktur (nicht als fertige Funktion) |
| Kostenstellen/Controlling | **fehlt** | Kostenstellenrechnung + Controlling-Sektion | – | Buchhaltung/Controlling | **fehlt in A** | B relevant |
| Projekte | Planner, Aufgaben, Berichte | Projekte, Phasen, Feinaufmaß, Bautagesberichte, Disposition | Projekte | Projekte&Baustelle | ähnlich; B tiefer (Baustelle) | B baustellennäher |
| Service/Tickets | Tickets(=Problem), Wartungsverträge, Fehlerkatalog | Tickets, Reklamationen, Betriebsmittel, Plantafel | Support/Projekte | Kundendienst&Einsatz | teils anders platziert | B bündelt Service+Disposition+Betriebsmittel |
| Lager/Artikel | Artikel(9), Lager(7), Lieferanten | Artikel, Lager, Inventur, Bestellungen | Artikel&Lager | Lager&Artikel | ähnlich | vergleichbar |
| Energie/PV | nur „PV-Planer (PVGIS)" (1 Kachel) | **große Energie&Auslegung-Sektion** (9) | Tools | Energie&Auslegung | **A minimal, B umfangreich** | B nur teils übernehmbar (viel PV-spezifisch) |
| Personal | Mitarbeiter, HR-Daten, Organisation | Mitarbeiter, HR, Lohnarten/-vorbereitung, Abteilungen | Personal | Personal&HR | ähnlich; B granularer bei Lohn | — |
| Lohn/Gehalt | „Lohn & Vollkosten" (1, `salary`) | Lohnvorbereitung + Lohnarten (`hr_payroll_*`) | Personal | Personal&HR | anders benannt/granular | B differenzierter |
| Admin/System | Benutzer, Rollen, Konfig verteilt | System-Sektion: Rollen, Systemmodule, Einstellungen | Admin/Konfig/System | System | ähnlich; B kompakter | B klarer gebündelt |
| Betriebskosten/Raten/Förderung | Filial-Betriebskosten, Ratenzahlungen, Förderungen (in „Finanzen") | Förderungen (in Energie); Betriebskosten n/a | Finanzen | verteilt | teils nur A | A-spezifisch |

## 13. Nur im aktuellen System (A) vorhanden
| Punkt | Bereich | Bewertung | Begründung |
|---|---|---|---|
| Globale Suche (⌘K) | Topbar | **behalten** | echter Mehrwert, in B nicht vorhanden |
| Dashboard-Shortcut-System (konfigurierbar) | Dashboard | **prüfen** | nützlich, aber Berechtigungslücke (Aufgabe 21) |
| Filial-Betriebskosten (`BranchExpense`) | Finanzen | behalten | in B nicht gefunden |
| Ratenzahlungen (`InstallmentPayment`) | Finanzen | behalten | in B nicht gefunden |
| Wartungsverträge & -Checklisten (dediziert) | Projekte/Service | behalten | B hat Wartung nur via Prüfplan |
| Fehlerkatalog (`Error`) | Support | prüfen | B: kein dediziertes Pendant |
| Datenbankbereinigung | System | **prüfen** (ungeschützt!) | sensibel, ungated |
| Report-Hub / Tagesberichte / Überfällige Berichte | Berichte | behalten | eigenständiges Reporting |
| Kanban „Feinaufmaß"/Lead-Kanban | Vertrieb/CRM | behalten | B hat Feinaufmaß, aber kein Lead-Kanban gleicher Art |
| Kalkulationssätze (`CostingSet`) | Einstellungen | später neu einordnen | Kostenbezug |

## 14. Nur im Playground (B) vorhanden
| Punkt | Bereich | Bewertung | Begründung |
|---|---|---|---|
| Volle **Buchhaltung** (28 Submodule) | Finanzen | **erst nach Prüfung** (Struktur ja, Funktion gesperrt) | strukturelles Vorbild; funktional Prototyp/gesperrt |
| **DATEV-EXTF / XRechnung / GoBD / UStVA / BWA / Bilanz / AfA** | Finanzen | später relevant | konzeptionell wertvoll, **nicht** produktiv |
| **Kostenstellen/-träger / Innenaufträge / Abteilungs-GuV** | Controlling/Finanzen | erst nach Prüfung | passt zu unserem Kostenstellen-Konzept |
| **Betriebsmittel/Fuhrpark** (Prüf-/Report-Suite) | Service | eventuell übernehmen | fehlt in A, klar abgegrenzt |
| **Formular-Engine** (`dynamic_forms`) | Formulare | eventuell übernehmen | dynamische Checklisten |
| **Energie/PV/WP/3D-Dachplaner** | Energie | nur Inspiration/teilweise | sehr PV-spezifisch, teils React-3D |
| **Controlling/KPI/OKR/Ziele** | Controlling | später relevant | fehlt in A |
| **Disposition/Plantafel** | Service/Projekte | eventuell übernehmen | echte Einsatzplanung |
| **Veranstaltungen / Beleg-OCR-Erkennung / Reverb-Chat** | div. | Inspiration/unklar | Rand/teils Legacy |
| **Angebots-/Auftrags-„Arbeitsraum"** | Vertrieb | erst nach Prüfung | UX-Muster |
| **Systemmodule (Modul-An/Aus)** | System | erst nach Prüfung | Feature-Flags |

## 15. Gleiche Funktion, unterschiedliche Begriffe
| A | B | fachlich | Risiko | Empfehlung |
|---|---|---|---|---|
| Rechnungen `invoices` (+ tote `deal_invoices`) | Rechnungen `accounting_outgoing/incoming_invoices` | gleich (B trennt Ein/Aus) | mittel | Begriffe im Glossar fixieren; A-Legacy raus (S1-10) |
| Aufträge = `deals` | Aufträge = `orders` | gleich | niedrig | Tabellennamen egal, Label „Aufträge" beibehalten |
| Kunden = `new_leads` (`customers` tot) | Kunden = `customers` (lebt) | gleich | mittel (A-Verwirrung) | A: `customers`/`Customer` bereinigen |
| „Lohn & Vollkosten" (`salary`) | Lohnvorbereitung + Lohnarten (`hr_payroll_*`) | ähnlich, B granularer | niedrig | Begriffe trennen (Vorbereitung ≠ Vollkosten) |
| Abteilungen (`departments`) | Abteilungen + **Abteilungs-GuV** | gleich + Zusatz | niedrig | GuV klar als Controlling kennzeichnen |
| „PV-Planer" | Energie&Auslegung (WR/WP/Konfigurator/Dachplaner) | A minimal, B breit | niedrig | später klären, ob PV-Ausbau gewünscht |
| Kostenstellen (nur Doku) | Kostenstellenrechnung (real) | gleich Konzept | niedrig | B als Referenz für unser Kostenstellen-Konzept |

## 16. Gleiche Funktion, unterschiedliche Platzierung
| Funktion | A-Platz | B-Platz | plausibler | Warum |
|---|---|---|---|---|
| **Rechnungen** | Vertrieb › Aufträge | Angebote&Aufträge **+ Buchhaltung** | **B** | Rechnung braucht Buchhaltungs-Heimat; Vertriebs-Kontext bleibt zusätzlich |
| **Kostenstellen** | (fehlt/Admin-Konzept) | Buchhaltung/Controlling | **B** | Kostenstelle ist Finanz-/Controlling-Thema |
| **DATEV/Kanzlei** | (fehlt) | Buchhaltung | **B** | gehört zu Buchhaltung, nicht System |
| Betriebsmittel/Maschinen | Lager | Kundendienst&Einsatz | **B** (diskutabel) | näher an Einsatz/Disposition |
| Lohn | Personal | Personal&HR (Lohnvorbereitung) | gleich | ok; Lohn-Kanzlei-Übergabe später ggf. Finanzen |
| Wartung | Projekte | Kundendienst&Einsatz | **B** | Wartung ist Service |
| Kontakte/Kunden | CRM (getrennt) | Vertrieb&CRM (gebündelt) | B leicht besser | eine Sektion statt zwei |

## 17. Doppelte / verwirrende Begriffe (Kurz)
- **A intern:** „Rechnungen" + „Rechnungen (Canvas-Hinweis)" (identische Route, Platzhalter); `invoices` vs. `deal_invoices`; `new_leads` vs. `customers`; „Lead" = 3 Dinge; „Tickets" = Problem-Modul. (Details `navigation-ist-befund-inventur.md` §7.)
- **B intern:** „Buchhaltung/Rechnungseingang-ausgang" vs. eigenständige UStVA/Anlagen/Mahnwesen (Routen teils `/buchhaltung/x` teils `/buchhaltung-x` — inkonsistentes Präfix); „Stammdaten" unter `/pilot/` (Alt-Prefix).
- **A↔B:** Aufträge(`deals`)↔(`orders`); Kunden(`new_leads`)↔(`customers`) — gleiche Sache, andere Tabellen → beim späteren Angleich Glossar nutzen.

## 18. Sichtbare Punkte ohne belastbare Funktion
| System | Punkt | Fundort | Problem |
|---|---|---|---|
| A | **„Rechnungen (Canvas-Hinweis)"** | Sidebar:749 | gleiche Route, `?open_canvas=1` = nur `alert()` |
| A | **„Zeiterfassung"** (Quick-Sider) | app.blade.php Quick-Sider | `href="#"` → Platzhalter |
| A | **„Home"** (Mobile-Bottom) | :5150 | Button ohne Ziel |
| A | „Datenbankbereinigung" | Sidebar:1330 | funktional, aber **ungeschützt** |
| B | Buchhaltung-Module (BWA/Bilanz/DATEV/UStVA …) | Buchhaltung-Sektion | **sichtbar, aber produktiv hart gesperrt** → wirkt fertiger als es ist (403/Gate) |
| B | Serviceaufträge | (Model, keine Route) | im Code, nicht in Nav (dormant) |
| A/B | (generell) | — | Laufzeit-Fertigstellung je Punkt statisch nicht voll verifizierbar |

## 19. Wichtige Module im Code ohne sichtbare Navigation
| System | Modul | Status | in Navi? |
|---|---|---|---|
| A | `app/Http/Controllers/Old/` (37 Ctrl, tot) | Legacy/Duplikate | nein (raus) |
| A | Projekt-Altdomäne (`Project*`, durch Planner ersetzt) | dormant | nein |
| A | PV-/Solar-Rechenkerne (`SolarSystem`, `Heatpump*`) | halb, nur via Tools | teils |
| B | Serviceaufträge (`Serviceauftrag`) | Skelett | nein → gehört ggf. rein |
| B | Historie/Audit (`HistoryEntry`, Querschnitt) | aktiv als Service | nein (ok) |
| B | KI-Layer (`Services/Ki`) | Querschnitt | nein (ok) |

## 20. Altschienen & Legacy
| System | Bereich | Status | späteres Ticket |
|---|---|---|---|
| A | `deal_invoices` (0 Z., tot) | Alt-Rechnungsschiene | **S1-10 Legacy/Cleanup** |
| A | `Old/`-Controller (37) + `OLD CODE`-Layouts + `app.blade copy*.php` + `product.zip` | toter Code/Artefakte | Cleanup (navi-neutral) |
| A | doppelter Rechnungs-Menüeintrag | Platzhalter | **Navi-Konzept** / IA-10 |
| A | `wating_leads` (Tippfehler-Route) | funktional, unsauber | Navi-Konzept |
| A | Bitrix/NIBE (`views/nibe`, `Bitrix*`) | Legacy | ignorieren (Memory) |
| B | React-SPA-Reste (`app.jsx`, `.claude/worktrees` Nav) | tot | Playground-intern (nicht unser Cleanup) |
| B | `/pilot/`-Prefix (Stammdaten) | Alt-Prefix | Playground-intern |

## 21. Berechtigungsrisiken (kritisch)
| System | Punkt | Berechtigung | Risiko |
|---|---|---|---|
| A | **Topbar-Quick-Menüs + Quick-Sider + Mobile-FAB** | **komplett ungated** (kein `@can`) | jeder eingeloggte Nutzer sieht/nutzt alle Schnellzugriffe |
| A | **Dashboard-Shortcut „Angebote"/„Aufträge"** | `permission_key = null` | ungated; Vertriebsdaten ohne Rechteprüfung |
| A | **Nutzer-eigene Shortcuts (beliebige URL)** | optionaler Key, ohne Key **kein** Gate | Nutzer kann sich Schnellzugriff auf jede URL bauen |
| A | `InvoiceMiddleware` registriert, **nicht angewandt** | keine | jeder kann Rechnungen sehen/ändern (Vorbefund) |
| A | Sidebar-Rechte nur **Parent-Ebene** | grob | 93 Unterpunkte meist ungeschützt |
| A | Datenbankbereinigung | ungated | destruktiv, ohne Gate |
| B | Module bleiben in Sidebar sichtbar, Recht **auf Route-Ebene** (`permission:*`/`role:*`/`acc.role:*`) | mittel-gut | sicherer erzwungen (403), aber „tote" Menüpunkte für Unberechtigte |
| B | Buchhaltung `acc.role:*` + Safety-Gate (Default-Deny) | streng | vorbildlich für sensible Finanzpfade |
> **Kernunterschied:** A gatet in der **Sidebar** (umgehbar über ungated Quick-Menüs/Dashboard), B gatet auf **Routen-Ebene** (nicht umgehbar). Für sensible Bereiche ist **B's Modell sicherer**.

## 22. Strukturqualität — A vs. B
| Kriterium | System A | Playground |
|---|---|---|
| Eine Navi-Quelle | nein (Sidebar + Quick + Dashboard + Mobile getrennt) | **ja** (eine `$nav`-Quelle, 3 Viewports) |
| Klare Hauptbereiche | mittel (15 Sektionen, teils dünn/überlappend) | **besser** (13 klare Fachsektionen) |
| Sensible Bereiche getrennt/geschützt | schwach (ungated Quick-Layer) | **besser** (Route-Gates, Finanz-Safety-Gate) |
| Doppelbegriffe/Platzhalter | mehrere (Canvas-Doppel, Zeiterfassung, Home) | wenige (Präfix-Inkonsistenz) |
| Submenü-Länge (Ziel 4–5) | verletzt (Artikel 9, Kontakte 8, Mitarbeiter 8) | **stärker verletzt** (Buchhaltung 28, Personal 16, Lager 11) |
| Tote Links | 0 (alle Routen auflösbar via `$safeRoute`) | 0 (alle 122 auflösbar) |
| Extra-Zugänge (Quick/Global-Search/Dashboard) | **viel** (Mehrwert + Risiko) | **keine** (klar, aber weniger komfortabel) |
**Urteil:** **Playground wirkt fachlich klarer und konsistenter** (eine Quelle, klare Sektionen, Route-Gates, Buchhaltung als eigenes Haus). **System A ist funktional reicher an Schnellzugriffen** (globale Suche, konfigurierbare Dashboard-Shortcuts), aber **strukturell zerfasert und bei Rechten riskant**. **Beide** verletzen die 4–5-Regel deutlich (Playground stärker) — beide brauchen Untergliederung via Tabs/Bereichsseiten.

## 23. Mögliche Übernahmen aus Playground (für spätere Navi-Planung)
| Playground-Punkt | Warum interessant | in A vorhanden? | Risiko | Bewertung |
|---|---|---|---|---|
| **Buchhaltung als eigene Top-Sektion** (Struktur, nicht Funktion) | löst A-Problem „Rechnungen unter Vertrieb / Finanzen dünn" | nein | niedrig (nur Navi-Struktur) | **erst nach Prüfung** |
| **Kostenstellen/Controlling-Sektion** | passt zu unserem Kostenstellen-Konzept | nein | niedrig | später relevant |
| **Eine Navi-Quelle → 3 Viewports** | beseitigt A's zerfaserte Navi | nein | mittel (Refactor) | erst nach Prüfung |
| **Route-Level-Gates statt Sidebar-Gates** | schließt A's ungated-Quick-Menü-Lücke | nein | mittel | **relevant** (Sicherheit) |
| **Angebots-/Auftrags-„Arbeitsraum"** | klares UX-Muster | teils (Canvas) | niedrig | erst nach Prüfung |
| **Betriebsmittel/Fuhrpark** | fehlt in A, klar abgegrenzt | nein | niedrig | eventuell übernehmen |
| **Disposition/Plantafel** | echte Einsatzplanung | nein | mittel | später relevant |
| **Formular-Engine** | dynamische Checklisten | teils (Checklisten-Formulare) | mittel | erst nach Prüfung |
| Energie/PV-Suite | breit, aber PV-spezifisch/3D-React | teils (PVGIS) | hoch | nur Inspiration |

## 24. Kritische Punkte (markiert)
- 🔴 **A: doppelte Rechnungsnavigation** (Sidebar + Canvas-Hinweis).
- 🔴 **A: `deal_invoices` Alt-Schiene** → S1-10.
- 🔴 **A: ungated Quick-Menüs/Quick-Sider/Mobile-FAB + Dashboard-Shortcuts „Angebote/Aufträge"** (Berechtigungsrisiko, versteckte zweite Navigation).
- 🔴 **A: `InvoiceMiddleware` nicht angewandt** → Rechnungen ohne Rechteschutz.
- 🟠 **B: DATEV/Buchhaltung wirkt fertig, ist aber hart gesperrt/Prototyp** — bei Übernahme nicht als „fertig" behandeln.
- 🟠 **A: Rechnungen fachlich falsch platziert** (Vertrieb statt Finanzen) — durch S1-01/02 aufgewertet, braucht Finanz-Heimat.
- 🟠 **Beide: Submenüs zu lang** (Buchhaltung 28 / Personal 16 / Artikel 9) — 4–5-Regel verletzt.
- 🟠 **A: Zeiterfassung/Home = Platzhalter** in Quick/Mobile.
- 🟠 **Begriffe:** `deals`/`orders`, `new_leads`/`customers`, „Lead"-Mehrdeutigkeit.

## 25. Offene Klärungsfragen für die spätere Navi-Planung
1. Soll die künftige Navi **eine Quelle → mehrere Viewports** werden (Playground-Muster) statt getrennter Sidebar/Quick/Dashboard?
2. Werden Rechte künftig **auf Routen-Ebene** erzwungen (Playground) statt nur Sidebar-Filter — und werden die **ungated Quick-Menüs/Dashboard-Shortcuts** abgesichert?
3. Bekommt **Buchhaltung/Finanzen** eine eigene Top-Sektion (Playground-Struktur), und wandern Rechnungen dorthin (Vertriebs-Kontext bleibt)?
4. Übernehmen wir Playground-**Kostenstellen/Controlling/Betriebsmittel/Disposition/Formular-Engine** — welche, in welcher Reihenfolge?
5. Wie setzen wir die **4–5-Unterpunkte-Regel** um, wo A bis 9 und B bis 28 Unterpunkte hat (Tabs/Bereichsseiten)?
6. Bleibt die **globale Suche (⌘K)** (nur A) erhalten/ausgebaut?
7. Wie werden **`customers`/`Customer`-Referenzen** (A, tot) und **`deal_invoices`** (S1-10) bereinigt?
8. Ist der Playground-Stand **eingefroren** (Vergleichsquelle) oder aktiv weiterentwickelt (dann Zeitpunkt fixieren)?
9. Welche Playground-Module sind **produktiv gemeint** vs. **Prototyp** (v. a. Buchhaltung/DATEV nicht als fertig einplanen)?

## 26. Empfehlung für den nächsten Schritt
**Ein eigenes „Navi-Konzept"-Ticket erstellen** — als separater Planungsschritt (kein Umbau jetzt). Playground ist die **bessere Struktur-Referenz** (eine Quelle, klare Fachsektionen, Buchhaltung als Haus, Route-Gates); System A liefert **Schnellzugriff-Ideen** (globale Suche, Dashboard-Shortcuts), die aber **abgesichert** werden müssen. Vor dem Zielbaum klären: IA-Entscheidungen (Rechnungen→Finanzen), Begriffe via `glossar.md`, Rechte-Modell (Route-Level), und welche Playground-Module produktiv vs. Prototyp sind. Parallel unkritisch: A-Legacy (`Old/`, `deal_invoices`, Canvas-Doppel) bereinigen.

---

## Ergebnis (klare Aussagen)
- **Vollständiger/konsistenter:** **Playground** (eine Nav-Quelle, 122 Punkte, 0 tote Links, 3 Viewports gespiegelt) — obwohl System A absolut mehr Code hat.
- **Fachlich klarer strukturiert:** **Playground** (klare Sektionen, Buchhaltung/Controlling als eigene Häuser, Route-Gates).
- **Funktional reicher an Zugängen:** **System A** (globale ⌘K-Suche + konfigurierbare Dashboard-Shortcuts + Quick-Menüs) — aber zerfasert und bei Rechten riskant.
- **Wertvolle Playground-Punkte:** Buchhaltung-**Struktur**, Kostenstellen/Controlling, Route-Level-Rechte, Betriebsmittel, Disposition/Plantafel, Formular-Engine, „Arbeitsraum"-UX.
- **Dringend in A bereinigen:** doppelte Rechnungsnavigation, `deal_invoices`, ungated Quick-Menüs/Dashboard-Shortcuts, `InvoiceMiddleware`, Rechnungen→Finanzen.
- **Doppelbegriffe zwingend klären:** `deals`/`orders`, `new_leads`/`customers`, „Lead"(3-fach), Rechnungen/`deal_invoices`.
- **Kritische Quick-Menü-Punkte:** alle A-Quick-Menüs + Dashboard-Shortcuts „Angebote/Aufträge" (ungated).
- **Fehlende/versteckte Module:** A: Buchhaltung/Kostenstellen/DATEV fehlen; `Old/` tot. B: Serviceaufträge dormant.
- **Fehlende Infos vor Planung:** Playground eingefroren?, welche B-Module produktiv vs. Prototyp, verbindlicher IA-Soll-Stand, Rechte-Zielmodell.
- **Navi-Konzept-Ticket erstellen?** **Ja.**

---

## Anhang — Herkunft & Reifegrad der Playground-Navigation (Zusatzbefund, read-only)

**1. Quelle:** hartcodiertes PHP-Array `$nav` in `Playground/backend-laravel/resources/views/layouts/app.blade.php:30–179`. **Nicht** datengetrieben (nicht aus DB). Das „Systemmodule"-Feature (`SystemmoduleController`, Tabelle `modules`) ist ein Modul-An/Aus-Schalter und **speist die Sidebar nicht**.

**2. Aktiv gerendert (nicht rekonstruiert):** dasselbe `$nav`-Array speist aus **einer** Quelle Desktop-Full-Sidebar (`:206–221`), Icon-Rail (`:233–243`) und Mobile-Overlay (`:281–296`). **Alle 122 Routen existieren** (`route:list`-Abgleich, 0 tote Links).

**3. Echte aktive Menüpunkte:** alle 122 sind gerenderte, klickbare Punkte mit Route + Controller + View. Keine „nur-Doku"-Platzhalter in der Nav.

**4. Prototyp / gesperrt / geplant:**
- Gesamtsystem = **Prototyp** (>85 % DB-echt, nicht produktionsreif).
- **Buchhaltung = aktiv gerendert, aber produktiv HART GESPERRT** (`config/finanz_safety.php`: `FINANZ_EXPORT_GESPERRT=true`, `master_mode=absolute`, Default-Deny; Middleware `finanz.gate:<key>` → 403). **DATEV wirkt fertiger, als es freigegeben ist.**
- Rechte auf **Routen-Ebene** (`permission:*`/`role:*`/`acc.role:*`) → Unberechtigte sehen den Punkt, bekommen bei Klick 403.
- Konditional eingeblendet: nur die 2 Buchhaltung-**Revisionslinks** (Gate-/GoBD-Protokoll, nur Finanz-Leserollen).
- Dormant (nicht im Menü): z. B. **Serviceaufträge** (Model/Migration, keine Route/Nav).

**5./6. Zusätzliche Topbar / Quick-Menü / Dashboard / Schnellzugriffe:** **Keine.** Topbar (`:249–270`) = nur Menü-/Einklapp-Button + Seitentitel + statischer Text. **Keine** globale Suche/Command-Palette, **kein** User-/Logout-Menü im Layout, **kein** Dashboard-Shortcut-System. Einzige Nicht-Sidebar-Elemente: konditionales rechtes **Kontext-Panel** (`@section('context')`), **Icon-Rail** (erster Link je Sektion), 2 rollenabhängige Revisionslinks. → **Gegensatz zu System A** (das Quick-Menüs, Quick-Sider, Dashboard-Shortcuts und ⌘K-Suche hat).

**7. Besonders markiert:**
| Bereich | Wo im Playground | Status |
|---|---|---|
| **Rechnungen** | (a) *Angebote & Aufträge* → `/app/rechnungen`; (b) *Buchhaltung* → „Rechnungseingang & -ausgang" | beide Prototyp; (b) zusätzlich **gesperrt** |
| **Buchhaltung** | eigene Sektion, **28 Punkte** | aktiv gerendert · **HART GESPERRT + Prototyp**; DATEV wirkt fertiger als es ist |
| **Finanzen** | keine eigene Sektion — in *Buchhaltung* (+ *Controlling*) | Buchhaltung gesperrt; Controlling Prototyp |
| **Lohn / Gehalt** | *Personal & HR*: Lohnvorbereitung, Lohnarten (`hr_payroll_*`) | Prototyp; DATEV-Lohn nur Skelett, nicht eigenständig im Menü |
| **Lager / Inventur** | *Lager & Artikel*: Inventur + Inventur-Sessions | Prototyp; echter Zählprozess (anders als System A) |
| **Admin / System** | *System*: Rollen & Rechte, Systemmodule, Module, Einstellungen, Uploads, Erkennung, Design-System | Prototyp; RBAC real |
| **Energie / Auslegung** | eigene Sektion, **9 Punkte** (inkl. **3D-Dachplaner**) | Prototyp; 3D-Dachplaner = einzige lebende React-Insel, Rest Blade |

**Kernaussage:** Die Playground-Nav ist **echt aktiv gerendert** (eine `$nav`-Quelle, alle Routen existieren), aber **funktional Prototyp**, und die **Buchhaltung ist produktiv gesperrt** — nicht als „fertig" behandeln. Kein Topbar-/Quick-Menü, keine Dashboard-Schnellzugriffe, keine globale Suche.

---
**Verwandte Doku:** `docs/navigation-ist-befund-inventur.md` · `docs/software-audit/ia-entscheidungen.md` · `docs/navi-schwaechen-gesamt.md` · `docs/glossar.md` · `docs/uebernahme/index-sprint-1-rechnungsprozess.md`.
