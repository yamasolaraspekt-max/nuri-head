# Navigation Vergleich — System A (ticket) vs. Playground

**Stand:** 2026-07-03 · **Read-only Vergleichs- & Bewertungsdokument — kein Code, keine Navigation/Routen geändert, keine bestehende Datei geändert, kein Commit.**
**Grundlage:** `docs/navigation-ist-befund-inventur.md` (System A) + `docs/navigation-vergleich-ticket-vs-playground.md` (voller A↔B-Vergleich inkl. Reifegrad-Anhang). Dieses Dokument verdichtet die Befunde zu einer **fachlichen Bewertung** für die spätere Navi-Planung.

---

## 2. Kurzfazit
- **System A (ticket)** ist **tiefer und gewachsen**: hartcodierte Sidebar mit 15 Sektionen / 37 Haupt- / 93 Unterpunkten (3 Ebenen) **plus** viele Zusatz-Zugänge — **Topbar-Quick-Menüs, Quick-Sider (~22 Kacheln), Dashboard-Shortcuts, mobile FAB und eine globale ⌘K-Suche**.
- **Playground** ist **flacher und fachlich breiter**: eine führende Blade-Sidebar mit 13 Sektionen / 122 Punkten (2 Ebenen), mit **eigenen Sektionen für Buchhaltung, Energie & Auslegung, Controlling und Kundendienst & Einsatz**.
- **Playground ist als Modulkatalog wertvoll**, aber **nicht 1:1 als Sidebar-Ziel geeignet** (122 direkte Punkte, Buchhaltung mit 28 Punkten, Prototyp/gesperrt).
- **System A hat reale Module**, aber die Navigation ist **informationsarchitektonisch überladen und teils falsch gewichtet** (Rechnungen unter Vertrieb, Finanzen unterentwickelt, doppelter Rechnungs-Eintrag, ungeschützte Quick-Menüs).

## 3. Direkter Strukturvergleich
| Kriterium | System A (ticket) | Playground |
|---|---|---|
| Sektionen | 15 | 13 |
| Haupt-/Unterpunkte | 37 Haupt · 93 Unter | 122 Punkte |
| Ebenen | **3** (Sektion → Haupt → Unter) | **2** (Sektion → Punkt) |
| Zusatz-Navigation | Topbar-Quick-Menüs · Quick-Sider · Dashboard-Shortcuts · Mobile-FAB · **⌘K-Suche** | **keine** (nur Sidebar + konditionales Kontext-Panel) |
| Buchhaltung | **fehlt** (Rechnungen unter Vertrieb; „Finanzen" = 3 Punkte) | **eigene Sektion, 28 Punkte** (Prototyp, hart gesperrt) |
| Energie / PV | 1 Punkt („PV-Planer / PVGIS") | **eigene Sektion, 9 Punkte** (inkl. 3D-Dachplaner) |
| Rechte | **Sidebar-Ebene** (Parent-Perms) + teils ungeschützte Quick-Menüs | **Routen-Ebene** (`permission:*`/`role:*`/`acc.role:*`) |

## 4. System A — Stärken
- **Zusatznavigation vorhanden:** globale ⌘K-Suche, Quick-Menüs, konfigurierbare Dashboard-Shortcuts → schnelle Wege für Alltagsaktionen.
- **Tiefere Gruppierung möglich** (3 Ebenen) — erlaubt Sammel-Untermenüs (z. B. Kontakte, HR-Daten).
- **Viele reale, produktiv genutzte Module** (CRM, Angebote, Aufträge, Projekte/Planner, Artikel/Lager, Personal, Rechnungen inkl. Sprint-1-Härtung).
- **Quick-Menüs/Suche** können später **gezielt für echte Schnellaktionen** wiederverwendet werden (nicht als zweite Hauptnavigation).

## 5. System A — Schwächen
- **Rechnungen hängen unter „Vertrieb › Aufträge"** statt in Finanzen.
- **Finanzen unterentwickelt** (nur Förderungen / Filial-Betriebskosten / Ratenzahlungen).
- **Doppelter Rechnungs-Eintrag** („Rechnungen" + „Rechnungen (Canvas-Hinweis)").
- **Viele Unterpunkte** (Artikel 9, Kontakte 8, Mitarbeiter 8) → 4–5-Regel verletzt.
- **Rechte teils nur auf Parent-Ebene** → 93 Unterpunkte meist ungeschützt.
- **Quick-Menüs / Quick-Sider / Dashboard-Shortcuts teils ungated** (u. a. „Angebote"/„Aufträge" `permission_key=null`; nutzer-eigene URL-Shortcuts ohne Gate).
- **Informationsarchitektur gewachsen statt geplant** (zerfaserte Zugänge, Platzhalter wie „Zeiterfassung"/„Home").

## 6. Playground — Stärken
- **Eigene Sektion Buchhaltung** (fachlich klarer als „Rechnungen unter Vertrieb").
- **Eigene Sektion Energie & Auslegung** (Konfigurator, WR/WP-Auslegung, Lastmanagement, Dachplaner).
- **Eigene Bereiche für Controlling und Kundendienst & Einsatz** (statt „Support").
- **Lager, Inventur, Wareneingang, Materialentnahmen** fachlich klarer und vollständiger sichtbar (inkl. echtem Inventur-Zählprozess).
- **Personal / HR deutlich umfangreicher** (Lohnvorbereitung, Lohnarten, Zeiterfassung, Abteilungen/GuV).
- **Als fachlicher Modulkatalog sehr hilfreich** — zeigt, welche Themenräume ein vollständiges Handwerks-ERP haben kann.

## 7. Playground — Schwächen
- **122 Punkte sind als direkte Navigation zu viel.**
- **Buchhaltung mit 28 Punkten** ist für eine Sidebar zu breit.
- **Keine Quick-Menü-/Such-Ebene** (weniger komfortabel für Schnellaktionen).
- **Rechnungen stehen dort ebenfalls unter „Angebote & Aufträge"** (zusätzlich zur Buchhaltung).
- **Prototyp-/gesperrte Buchhaltung** darf **nicht wie produktiv fertig** wirken (Default-Deny, DATEV nicht abgenommen).
- **Routen-Ebene als Rechtebasis** ist sicherer, muss aber genauer geprüft werden (gesperrte Module bleiben sichtbar → 403).

## 8. Fachliche Bewertung nach Domänen
| Domäne | System A | Playground | Kurzbewertung |
|---|---|---|---|
| Übersicht / Dashboard | „Arbeitsbereich" + reiches Dashboard-Shortcut-System | „Übersicht" (Dashboard + Benachrichtigungen) | A komfortabler, B schlanker; A-Shortcuts absichern |
| CRM / Kontakte | CRM: Leads/Kunden + Kontakte getrennt | Vertrieb & CRM: Kunden + Kontakte gebündelt | vergleichbar; B bündelt sauberer |
| Vertrieb | Angebote + Aufträge | Angebote & Aufträge (+ Arbeitsräume) | B mit „Arbeitsraum"-UX-Muster |
| Angebote & Aufträge | Übersicht/Assistent/Vorlagen; deals | + Auftragsbestätigungen, Sets, Arbeitsräume | B fachlich vollständiger |
| Projekte / Baustelle | Planner, Aufgaben, Berichte | Projekte, Phasen, Feinaufmaß, Bautagesberichte, Disposition | B baustellennäher |
| Kundendienst / Service | „Support" (Tickets=Problem) + Wartung unter Projekte | „Kundendienst & Einsatz" (Tickets, Reklamationen, Plantafel, Betriebsmittel) | **B klarer** (Service als Themenraum) |
| Lager / Artikel / Inventur | Artikel & Lager (Inventar, keine echte Inventur) | Lager & Artikel (Inventur + Sessions, Wareneingang, Materialentnahme) | **B vollständiger** |
| Energie / Auslegung | 1 Punkt (PVGIS) | eigene Sektion (9) | **B umfangreicher** (viel PV-spezifisch) |
| Finanzen / Buchhaltung | fehlt (Rechnungen unter Vertrieb) | eigene Sektion (28, gesperrt/Prototyp) | **B als Struktur-Vorbild**, nicht als fertige Funktion |
| Personal / HR / Lohn | Mitarbeiter/HR-Daten/Organisation; „Lohn & Vollkosten" | Personal & HR (Lohnvorbereitung, Lohnarten, GuV) | B granularer bei Lohn |
| Controlling | fehlt | eigene Sektion (KPI, Ziele, Innenaufträge, Verträge) | **B relevant** |
| Stammdaten | verteilt (Konfiguration/Einstellungen) | eigene Sektion (`/pilot/…`) | B klarer getrennt (Prefix `pilot` aber unschön) |
| System / Admin | Admin + System + Konfiguration + Einstellungen (4 Sektionen) | eine „System"-Sektion | **B kompakter** |

## 9. Punkte aus Playground, die als Inspiration wertvoll sind
- **Energie & Auslegung als eigener Bereich.**
- **Kundendienst & Einsatz** statt nur „Support".
- **Lager & Artikel inklusive Inventur, Wareneingang, Materialentnahmen.**
- **Controlling als eigener Bereich.**
- **Buchhaltung als eigener fachlicher Themenraum** — aber später **stark verdichtet** (nicht 28 Punkte).
- **Stammdaten sauber von System trennen.**
- (ergänzend) **Rechte auf Routen-Ebene** als Sicherheitsmuster; **eine Navi-Quelle → mehrere Viewports**.

## 10. Punkte, die NICHT 1:1 übernommen werden sollen
- **28 direkte Buchhaltungs-Menüpunkte** (viel zu breit für eine Sidebar).
- **Rechnungen als führender Punkt unter „Angebote & Aufträge"** (gehören primär nach Finanzen).
- **Lieferanten unter „Vertrieb & CRM"** (eher Einkauf/Stammdaten).
- **Abteilungs-GuV unter Personal** (eher Controlling/Finanzen).
- **Prototyp-Buchhaltung als normale produktive Navigation** (darf nicht fertig wirken).
- **Zu viele direkte Punkte ohne Themenräume** (flache 122-Punkte-Liste).

## 11. Konsequenzen für die spätere Navi-Planung
- **Sidebar zeigt künftig nur Themenräume.**
- **Pro Hauptbereich maximal 4–5 direkte Unterpunkte.**
- **Details** gehören in **Bereichsseiten, Tabs, Filter oder Aktionen** — nicht in die Sidebar.
- **Quick-Menüs nur für echte Schnellaktionen**, nicht als zweite versteckte Hauptnavigation.
- **Rechnungen gehören primär nach Finanzen.**
- **Vertrieb** darf Rechnungen nur **kontextbezogen** aus Auftrag/Angebot/Projekt öffnen.
- **Buchhaltung/DATEV/Kanzlei** darf **nicht fertiger wirken, als technisch umgesetzt** ist.
- **Sensible Bereiche** (Finanzen/Lohn/Admin/Export) brauchen **klare Rechteprüfung** (idealerweise Routen-Ebene).

## 12. Offene Fragen
- Welche Playground-Navi ist **aktiv gerendert** und welche nur aus Routen/Doku rekonstruiert? *(Befund: alle 122 aktiv gerendert aus `$nav`, alle Routen existieren — siehe Reifegrad-Anhang.)*
- Welche Playground-**Buchhaltungspunkte** sind wirklich nutzbar und welche nur Prototyp/gesperrt?
- Welche **Quick-Menü-Punkte in System A** sind kritisch oder doppelt (ungated, Platzhalter)?
- Welche **Navigationsebene** soll künftig führend sein: Sidebar, Bereichsseiten oder globale Suche?
- Welche **Begriffe gelten verbindlich** laut `glossar.md`?
- Welche **Module sollen sichtbar** sein, obwohl sie noch nicht fertig sind (und wie kennzeichnen)?

## 13. Empfehlung nächster Schritt
**Noch keine Navigation umbauen.** Erst ein eigenes **Navi-Konzept-Ticket** erstellen, das aus **beiden Beständen** (System A = reale Module + Zusatzzugänge; Playground = fachliche Themenraum-Struktur) eine **schlanke Zielnavigation** ableitet — mit max. 4–5 Unterpunkten je Bereich, Details in Bereichsseiten/Tabs, gesicherten Quick-Aktionen und Rechnungen unter Finanzen.

---
`Dieses Dokument ist ein Vergleichs- und Bewertungsdokument. Es ist kein Umsetzungsauftrag.`
