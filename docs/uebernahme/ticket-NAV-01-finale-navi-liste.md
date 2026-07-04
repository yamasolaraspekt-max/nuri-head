# ticket-CRM — Finale Navigations-Liste (NAV-01)

> **Reine Ableitung (kein Code, keine Nav/Routen/Blades geändert, kein Commit ohne OK).** Konsolidiert aus: System-A-Nav-Inventur (`navigation-ist-befund-inventur.md`, 15 Sektionen/37/93 + Agent-Item-Liste), Playground-Katalog (`uebernahme/inventar-playground.md`, 122 Punkte), Vergleich (`navigation-vergleich-bewertung-ticket-vs-playground.md`), Weichen (`architektur-entscheidungen.md`: Weiche 1 Phasen=lead_stages · Weiche 5 Projekt=Bauphase, keine eigene Ebene · Weiche 6 Planner=Feld-Wahrheit) + Live-Stichproben (route:list 2026-07-03). Übernimmt das gesetzte **Ziel-Gerüst B** und behebt die **8 Struktur-Fehler A**; Abweichungen sind als solche markiert. **Status:** produktiv · Prototyp · geplant · eingefroren. **Herkunft:** A-Punkt / playground / neu.

---
## ⚠️ REVISION v2 — 23 Ein-Wort-Bereiche, strikt 2 Ebenen (2026-07-04)

> **Diese Revision überschreibt das 11-Themenraum-Konzept in §1 unten.** §1 bleibt als **Trail** erhalten (nicht gelöscht), ist aber **ÜBERHOLT**.

**Grund (Yama, live 2026-07-04):** Das ticket-Template rendert **drei** Aufklapp-Ebenen (Sektion → Haupt-Item → Kind) — Sektion und Haupt-Item sind **zwei konkurrierende Aufklapp-Begrifflichkeiten** (z. B. „Kontakte ▸ Kontakte"), dazu „X & Y"-Sammel-Labels. Yamas Vorgabe: **keine zwei Begrifflichkeiten**, stattdessen **mehr Haupt-Navis mit je kurzer Unterliste**.

**Entscheidung (Yama, Auswahl-Dialog 2026-07-04):** **23 Ein-Wort-Bereiche** (Alternative „~18 kompakter" verworfen) · Pipeline-Bereich = **„Leads"** · Vorplanung = **„Planung"**.

**Prinzip — strikt 2 Ebenen:** Bereich = aufklappbare **Haupt-Navi** (ein Wort) · darunter **flache Unter-Links** (kein `children`/`submenu`). Ganze Bereiche gaten über **Sektions-`permission`**; Finance bleibt **Item-Gate**.

**Die 23 Bereiche (Zahl = Unter-Links):**
`Arbeitsbereich[7] · Anfragen[7] · Leads[6] · Kontakte[8] · Kommunikation[3] · Angebote[3] · Aufträge[5] · Montage[4] · Artikel[9] · Artikel-Daten[5] · Lager[7] · Planung[3] · Tickets[3] · Wartung[2] · Mitarbeiter[8] · HR-Daten[7] · Organisation[4] · Phasen[3] · Stammdaten[2] · Filialen[3] · E-Mail-Einrichtung[3] · System[4] · Benutzer[4]`

**Belege (verifiziert vor Commit, Original `b34e4b2^` → Final):**
- **Nav-Invariante:** 109 Routen-Ziele Original → 109 Final. Einziger Orphan = `offers/wizard` (**nur-URL**, ersetzt durch Angebots-Assistent `wizard-smart`). Neuzugang = `lead-stages.manage` (neue Lead-Phasen-Seite). 0 unerklärte Orphans, 0 Duplikate. (110 Nav-Punkte / 109 Routen; Differenz = „Kundenakte" via Variable.)
- **Rechte-Erhalt:** je `permission`-Key identische gegatete Blätter. Email 1→2 Bereiche (dieselben 6 Blätter: Kommunikation + E-Mail-Einrichtung), Finance 1→3 Item-Gates (dieselben 3: Förderungen/Betriebskosten/Ratenzahlungen), 8 weitere Keys je 1 Section-Gate unverändert. Keine Verengung/Weitung.

**Umgesetzt in Commits:** `b34e4b2` (Sidebar 2 Ebenen/23 Bereiche) · `903fe1e` (Lead-Phasen-Verwaltungsseite, neue CRUD-Seite auf bestehende JSON-API) · `9286dce` (Fix: Phasen-Löschung schreibt `lead_stage_id` konsistent mit `status`, Stufe-A-Kanon).

**Beibehalten aus §1:** Weichen (1/5/6), Buchhaltung-nur-URL (Entsch. 3), Kundendienst eigener Bereich (Entsch. 5), Junk/Papierkorb als letzte Punkte, Finance-Gate. **Abweichung:** Themenraum-Bündelung (11) → Ein-Wort-Auflösung (23); „Benachrichtigungen" = Topbar (kein Sidebar-Punkt). **Offen (Phase III):** Tab-Konsolidierung (Kontakte-Typ, Junk/Papierkorb).

---

## 1. DIE FINALE LISTE (Sidebar = nur Themenräume, max. 4–5 Unterpunkte)  ·  ⚠️ ÜBERHOLT durch Revision v2 oben (Trail erhalten)

### 1 · Arbeitsbereich  ·  *Alt: Start · Cockpit*
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Dashboard | `home` (EmployeeDashboardController) | — | produktiv | A-Arbeitsbereich |
| Lead-Kanban | `lead.kanban` | — | produktiv | A-Arbeitsbereich |
| Meine Tagesberichte | Tagesbericht-Route (A-Berichte, „meine Arbeit") | — | produktiv | A-Berichte (Split A7) |
| Benachrichtigungen | Notifications | — | produktiv | playground `benachrichtigungen` / A-Topbar |

### 2 · Anfragen & Leads  ·  *Alt: Akquise · Vertriebstrichter*  (EIN Trichter — A2)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Anfragen | `inquiry.customer`/`inquiry.published.list` **+ Tabs** Junk(`inquiry.junk.list`)/Papierkorb(`inquiry.deleted.list`) | Inquiry | produktiv | A-CRM›Anfragen(7)→Tabs (A5) |
| Leadliste | `new.lead.view` **+ Tabs** Warteschleife(`waiting.loop.leads`)/Junk(`lead.junks`)/Papierkorb(`deleted.leads`) | Customer | produktiv | A-CRM›Leads(6)→Tabs |
| Kundenakte | `new.lead.profile` (Kunde→Objekt→Gewerk) | Customer | produktiv | A-Kundenakte |

### 3 · Kontakte  ·  *Alt: Adressen · Partner*  (Typ als Tabs, nicht 7 Punkte — A3)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Alle Kontakte | `all.contacts` **+ Typ-Tabs** Marken/Architekten/Bank/Versicherung/Subunternehmer/**Lieferanten** | Partner | produktiv | A-CRM›Kontakte(8)→Tabs; playground `kontakte` |

### 4 · Kommunikation  ·  *Alt: Nachrichten · Posteingang*
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Posteingang | `lead.email.inbox` **+ Tab** Lead-Mails | Email | produktiv | A-CRM›Kommunikation(6) |
| Chat | **moderner** Chat (NICHT `chats.view`→MessageController = Legacy!) | Email | produktiv (Link umhängen) | A (🔴 Legacy-Link, s. §2) |

### 5 · Angebote & Aufträge  ·  *Alt: Vertrieb · Verkauf*  (kaufmännischer Fluss — playground-Schnitt)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Angebote | `admin.offers.index` **+Aktion** Assistent(`offers.wizard.smart`) **+Tabs** Vorlagen(`offer-templates.index`)/Sets(`admin.master_sets.index`)/Arbeitsraum(`offer.folder-show`) | — | produktiv | A-Vertrieb›Angebote(4) + playground `angebot-sets` |
| Auftragsbestätigungen | (ticket: keine serverseitige AB-PDF — crm-inventur #2) | — | **geplant** | playground `auftragsbestaetigungen` |
| Aufträge | `deal.all.list` **+Tabs** Feinaufmaß(`deal.measurements.kanban`)/Junk(`deal.junk.list`)/Papierkorb(`deal.delete.list`) | — | produktiv | A-Vertrieb›Aufträge(6) |
| Rechnungen | **EIN** Punkt → produktives Ziel; `invoices.index`/`/admin/invoices` heute **toter Menülink** (Phase-II-Commit-2: dokumentieren + reparieren); `/deal/invoices`-Alt-Schiene **nur-URL**; Canvas = **+Aktion**, nicht 2. Punkt | Finance | produktiv (Link kaputt→Fix) | A-Vertrieb›Aufträge (Entscheidung 6: Schienen-Frage entscheidet die Nav NICHT) |

### 6 · Montage  ·  *Alt: Ausführung · (Projekte)*  — NICHT „Projekte & Baustelle" (Weiche 5)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Einsatzplan / Projektplanung | `planner.index` | — | produktiv | A-Projekte›Projektplanung |
| Bautagesberichte | Montage-/Report-Route | — | teilweise | playground `bautagesberichte` |
| Zu prüfen (PL-Prüfliste) | reported-Montage-Karten (heute Dashboard-Widget fa41c61) | — | produktiv (Widget) / geplant (Liste) | neu (aus B3) |
| Kontroll-Berichte | `admin.report.index`/`admin.overdue-center.reports.index` | Administrator | produktiv | A-Berichte (Split A7 — ⚠️ Vertrieb-vs-Montage, s. §4) |

### 7 · Lager & Artikel  ·  *Alt: Warenwirtschaft · Katalog & Lager*  (playground 11→4; **NICHT „Artikel & Material"**)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Artikel | `product.info` **+Tabs** Gruppen(`article_group.index`)/Import(`ids.search.form`)/Spezial(`product.formula.index`/`radiator.config.view`) | Product | produktiv | A-Artikel&Lager›Artikel(9)→verdichtet |
| Leistungen | Leistungen/Gewerke-Route | Product | teilweise | playground `leistungen` |
| Bestellungen | `purchase.request` **+Tab** Wareneingänge(`request.out.details`/`delivery-notes.index`) | Product | produktiv (Kaufanfrage; echte „Bestellung"-Entität fehlt) | A-Lager + playground `bestellungen` |
| Lager & Inventur | `inventory.index` **+Tabs** Orte/Entnahmen/**Inventur**(Zählprozess) | Product | produktiv (Bestand) / **geplant** (Inventur) | A-Lager(7) + playground `inventur` |

### 8 · Energie & Auslegung  ·  *Alt: Planung & Auslegung · Technik*  (Alleinstellung; Punkte als **Prototyp** wo nicht produktiv)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Konfigurator | Konfigurator/`admin.pvgis.index` | — | **Prototyp** | playground `konfigurator` |
| Auslegungen WR/WP | WR-/WP-Auslegung (ticket: Rechenkerne dormant) | — | **Prototyp** | playground `wr-/wp-auslegung` |
| Dachplaner | (playground-3D = React, ausgeschlossen; nur Blade-Hülle) | — | **geplant** | playground `dachplaner-pro` |
| Förderungen | `foerderungen.index` | — | produktiv | A-Finanzen›Förderungen |

### 9 · Kundendienst  ·  *Alt: Service · Wartung & Service*  (Entscheidung 5 — playground-Gewinn; echtes Geschäft bei Wartungsverträgen)
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Tickets | `problem.view`/`problem.create` (intern „Problem"-Modul) | Problem | produktiv | A-Support›Tickets(3) |
| Reklamationen | Reklamations-/Feedback-Route | Problem | teilweise | playground `reklamationen` |
| Wartung | `admin.maintenance_checklists.index`/`admin.maintenance.contracts.index` | — | produktiv | A-Projekte›Wartung(2) |
> Betriebsmittel/Maschinen (`machine.inventory`) heute unter Lager — Zuordnung Kundendienst vs. Lager als Tab später (nicht blockierend).

> **🧊 Buchhaltung — NICHT in der Sidebar (Entscheidung 3).** Eingefroren (Weiche 3, Accounting-Instanz) = monatelang totes Gewicht; ein gesperrter Bereich soll nicht als produktiv wirken. **Alle Buchhaltungs-Ziele bleiben nur-URL erreichbar**; der Bereich **kehrt nach Weiche-3-Klärung zurück**. Bis dahin kein Sidebar-Punkt.

### 10 · Personal  ·  *Alt: Team · Mitarbeiter*
| Unterpunkt | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|
| Mitarbeiter | `emp.info` | Employee | produktiv | A-Personal›Mitarbeiter(8) |
| Qualifikationen | `position.index` (position_qualifications) | Employee | produktiv | A-Organisation |
| Urlaub & Abwesenheit | `leave.day.info`/`employee.sickness-holiday-analyser` | Employee | produktiv | A-HR-Daten(7) |
| Zeit | `time_management.slots`/`admin.attendance.analytics` | Employee | produktiv | A-HR-Daten |
| Lohn & Vollkosten | `salary.index` | **Super/`$canSalary`** | produktiv | A-Personal (härtestes Gate) |

### 11 · Verwaltung  ·  *Alt: Einstellungen · Administration*  (aus der Tagesarbeit raus — A4; **3 Bündel**, Entscheidung 8)
| Bündel | Unterpunkte | Route/Ziel | Recht | Status | Herkunft |
|---|---|---|---|---|---|
| **Phasen & Abläufe** | Phasen & Abläufe | `lead-stages.index` + `task_phase.index` + **`phase_management`** | — | produktiv | 🟢 **NEU — hatte KEINEN Nav-Einstieg** (schließt Orphan aus UX-Audit) |
| **Vorlagen & Stammdaten** | Vorlagen · Kontaktarten & -vorlagen | Vorlagen-Pflege · Kontaktart-Pflege | Partner | produktiv | A-Konfiguration; playground `kontaktarten/-vorlagen` |
| **System** | E-Mail-Einrichtung · Benutzer & Rechte · Firma & Filialen | `email.configuration`/Domain-Filter · `user-rolls.index`/`user.admin` · `branch.info` | Email/Users | produktiv | A-Konfiguration/Admin/System→Verwaltung (A4) |
> „Datenbankbereinigung" (heute ungeschützt) → unter System, **mit Gate** (`$canDeleteGarbage` als Recht anwenden, nicht nur berechnen).

### Topbar (global, nicht Sidebar)
**Suche (⌘K, existiert in A)** · **+Neu** (Anfrage · Lead · Angebot · Termin · Notiz) · **Benachrichtigungen**. Die A-Quick-Menüs/Quick-Sider/Dashboard-Shortcuts **fließen in „+Neu" + ⌘K**, NICHT als zweite Hauptnavigation (Vergleich §11).

---
## 2. SCHICKSALS-TABELLE (jeder A-Bereich · jeder playground-Bereich)

### System A (ticket) — 15 Sektionen → Ziel
| A-Sektion (Unterpunkte) | Schicksal |
|---|---|
| Arbeitsbereich(2) | **bleibt** → 1 (Dashboard, Lead-Kanban) |
| Berichte(3) | **gesplittet** (A7): Tagesberichte→1 · Kontroll-/Overdue-Berichte→6 (Recht Administrator) |
| CRM›Kommunikation(6) | **umgezogen** → 4 Kommunikation (Chat-Legacy-Link umhängen) |
| CRM›Anfragen(7) | **umgezogen** → 2; Junk/Papierkorb → **Tabs** (A5) |
| CRM›Leads/Kunden(6) | **umgezogen** → 2; Warteschleife/Junk/Papierkorb → **Tabs**; „CRM"-Etikett **entfällt** (A1) |
| CRM›Kontakte(8) | **verschmolzen** → 3 als **Typ-Tabs** (A3); Lieferanten als Tab |
| Vertrieb›Angebote(4) | → 5; `wizard`+`wizard-smart` **konsolidiert** → **eine +Aktion** (wizard-smart, s. §4); Vorlagen/Sets → Tabs |
| Vertrieb›Aufträge(6) | → 5; **Rechnungen** bleibt Punkt, **Canvas-Hinweis → +Aktion** (A6, IA-10); Junk/Papierkorb → Tabs |
| Projekte›Projektplanung | → 6 Einsatzplan |
| Projekte›Meine/Allgemeine Aufgaben | Meine→1/6, Allgemeine→6 |
| Projekte›Notizen(2) | **kontextbezogen** (Kundenakte/Karte), raus aus Sidebar |
| Projekte›Termine(2) | → 6 (bzw. 1 „meine Termine") |
| Projekte›Wartung(2) | **umgezogen** → 9 Kundendienst (Entscheidung 5) |
| Support›Tickets(3) | **umgezogen** → 9 Kundendienst (Entscheidung 5 — hat jetzt ein Zuhause) |
| Personal›Mitarbeiter(8)/HR-Daten(7)/Organisation(4) | **verdichtet** → 10 (5 Punkte); Rest (Verträge/Länder/Steuer/Feiertage/Sprachen) → Tabs bzw. 11 Verwaltung |
| Artikel&Lager›Artikel(9)/Artikel-Daten(5)/Lager(7) | **verdichtet** → 7 (4 Punkte, Rest Tabs) — playground 11→4 |
| Finanzen(3): Förderungen/Betriebskosten/Ratenzahlungen | Förderungen→8; Betriebskosten/Ratenzahlungen→**Buchhaltung nur-URL** bzw. 11 Verwaltung |
| Admin(4)/Konfiguration(3)/System(4)/Einstellungen(1)/Wissen(1) | **umgezogen** → 11 Verwaltung (Wissen→1 oder 11); „Datenbankbereinigung" **gaten** (heute ungeschützt) |
| **RAUS/Fix** | Rechnungen-Canvas-Hinweis (Platzhalter alert → +Aktion) · `wating_leads`-Tippfehler (fixen) · `Old/`-Controller (nie in Nav) |

### Playground (122) — als Konzept übernommen / verworfen
| playground-Bereich | Entscheidung |
|---|---|
| CRM/Kontakte/Kommunikation (kunden/kundenakte/kontakte/anfragen/kommunikation) | **übernommen** als Bereiche 2/3/4 |
| Angebote/Sets/Aufträge/Auftragsbestätigungen/Rechnungen/Leistungen | **übernommen** → 5 (Auftragsbestätigungen=geplant); Rechnungen **NICHT** führend unter Vertrieb (→ primär 9, produktiv-Invoice in 5) |
| Projekte/Feinaufmaß/Bautagesberichte/Disposition/Montagevorbereitung | **übernommen** → 6 Montage (Weiche 5: „Projekte"→Montage) |
| Artikel/Lager/Bestellungen/Wareneingänge/Materialentnahmen/**Inventur**/Lieferanten | **übernommen** → 7 (Inventur=Gewinn, geplant); Lieferanten → 3 (Tab), **nicht** Vertrieb |
| **Energie-Tools** (WP/WR-Auslegung/Konfigurator/Dachplaner/Lastmanagement) | **übernommen** → 8 (Prototyp; 3D-Dachplaner=React, ausgeschlossen) |
| **Buchhaltung(30 Submodule)** | **Konzept übernommen, aber NICHT in der Sidebar** (Entscheidung 3): nur-URL bis Weiche 3, dann eigener Bereich |
| **Kundendienst** (tickets/reklamationen) | **übernommen als Bereich 9** (Entscheidung 5) |
| Betriebsmittel/Fuhrpark | → 9 Kundendienst (Tab) bzw. 7 Lager (`machine.inventory` heute A-Lager) |
| HR/Lohnvorbereitung/Lohnarten/Zeiterfassung | **übernommen** → 10 (Lohn-Detail=Prototyp/geplant) |
| **Controlling** (KPI/OKR/GuV) | **verworfen für jetzt** (kein produktives ticket-Pendant; später eigener Bereich) — Vergleich §9 |
| Formulare (dynamische Engine/Smartrouting) | **verworfen als Nav-Bereich** (Querschnitt, kein Menüpunkt; Idee für Angebots-Pflichtfelder) |
| Plattform/Auth/Design-System/Uploads/Erkennung | → 11 Verwaltung (Design-System later); Erkennung/OCR = Prototyp |
| React-SPA / 3D-Planer / TS-Connectoren | **ausgeschlossen** (stack-fremd, inventar-playground §„NICHT im Inventar") |

---
## 3. QUICK-MENÜ / TOPBAR (was bleibt · wird Sidebar · fliegt)
- **Bleibt Topbar:** ⌘K-Suche (echte Schnellsuche) · Benachrichtigungen.
- **Wird „+Neu"-Aktion:** Angebot/Termin/Notiz/Anfrage/Lead aus den A-Quick-Menüs → **eine gated „+Neu"-Aktion** (heute teils ungated, Vergleich §5).
- **Fliegt:** Quick-Sider (~22 Kacheln) + nutzer-eigene URL-Shortcuts als **zweite versteckte Hauptnavigation** (ungated, Platzhalter „Zeiterfassung"/„Home") → ersetzt durch ⌘K + „+Neu". Dashboard-Shortcuts bleiben **im Dashboard**, nicht global.

---
## 4. ENTSCHEIDUNGEN (Yama, 2026-07-03 — alle 8 getroffen)
1. **Bereichsname = „Montage"** (Weiche 5); „Projekte" nur noch im **Einsatzplan-Kontext** erlaubt, nicht als Bereichsname.
2. **Kontroll-Berichte → Montage (6)** (kontrollieren Ausführung, nicht Verkauf).
3. **Buchhaltung RAUS aus der Sidebar** (eingefroren = totes Gewicht; Ziele bleiben **nur-URL**, Bereich kehrt nach Weiche-3 zurück). → **bleibt bei 11 Bereichen.**
4. **Zweiter Veto-Begriff: bleibt OFFEN** — nur Yama kann ihn nennen; die *Alt:*-Spalte je Bereich bleibt im Doc, damit er beim Lesen wählt.
5. **Kundendienst REIN als eigener Bereich (9)** — Tickets/Reklamationen/Wartung (echtes Geschäft bei Wartungsverträgen; die A-Ticket-Routen hätten sonst kein Zuhause).
6. **Rechnungen = EIN Punkt in 5** aufs produktive Ziel; toter `/admin/invoices`-Menülink dokumentieren + reparieren (**Phase-II-Commit-2**); `/deal/invoices` nur-URL; Buchhaltungs-Suite wartet mit Bereich Buchhaltung auf Weiche 3. **Die Nav entscheidet die Schienen-Frage NICHT.**
7. **`wizard-smart` als +Aktion; `wizard` RAUS aus der Nav, aber nur-URL** (nichts löschen — fachliche Ablösung = spätere eigene Prüfung, kein Nav-Thema).
8. **Verwaltung = 3 Bündel:** *Phasen & Abläufe* · *Vorlagen & Stammdaten* (Vorlagen + Kontaktarten) · *System* (E-Mail-Einrichtung + Benutzer & Rechte + Firma & Filialen).

→ **Finale 11 Bereiche:** Arbeitsbereich · Anfragen & Leads · Kontakte · Kommunikation · Angebote & Aufträge · Montage · Lager & Artikel · Energie & Auslegung · **Kundendienst** · Personal · Verwaltung.

---
## 5. Gelesen / NICHT gelesen · NICHT VERIFIZIERT · Selbstkritik
**Gelesen (voll):** `navigation-ist-befund-inventur.md` (System A: 15/37/93, IA-Fehler, Begriffs-Dopplungen), `navigation-vergleich-bewertung-ticket-vs-playground.md`, `uebernahme/inventar-playground.md` (122-Punkte-Modulkatalog), Weichen 1/5/6 (aus Memory/architektur-entscheidungen). **Agent-Nav-Audit** (116 Items + Routen, für die Ziel-Zuordnung). **Live-Stichproben (route:list 2026-07-03):** `offers.wizard` **und** `offers.wizard.smart` leben (beide → OfferWizardController); `customer_phase`-Routen **weg** (bestätigt, seit f6a8b4c); `admin/lead-stages` + `phase_management` **leben** (Verwaltung-Punkt „Phasen & Abläufe" real); `chats.view` → **`MessageController@index`** (Legacy bestätigt).
**NICHT VERIFIZIERT / offen:** die **vollständige 93-Zeilen-Detailtabelle** (E1/E2/Label/Route je Punkt) lag NICHT im ist-befund-Doc („kann auf Wunsch ergänzt werden") — ich habe auf **Sektions-/Untermenü-Ebene + Agent-Item-Liste** gemappt, **nicht jeden der 93 Labels verbatim**; einzelne selten benannte Punkte könnten in einem Sammel-Tab landen, ohne einzeln aufgeführt zu sein. Welche **moderne Chat-Route** den Legacy-Link ersetzt = nicht ermittelt. Welcher **wizard** fachlich abgelöst ist = nur Route-, kein Laufzeit-Check. **Bautagesberichte/Leistungen/Auftragsbestätigungen/Inventur**-Reife in ticket = Status-Schätzung (teilweise/geplant), nicht laufzeit-verifiziert.
**Selbstkritik (Ableitung vs. Geschmack):**
- **Ableitung (belegt):** die 11 Bereiche + Tab-Konsolidierungen folgen Ziel-Gerüst B + den 8 A-Fehlern; Rechnungen-Reconciliation folgt IA-2 + Weiche-3-Frozen.
- **Geschmack (markiert):** die *Alt:*-Begriffe, die Verwaltung-Bündelung, und **ob Kundendienst eigener Bereich wird** sind Vorschläge, keine Vorgaben — bewusst als Yama-Fragen ausgelagert.
- **Lücke:** der **Service/Kundendienst-Bereich fehlt im gesetzten Gerüst** — ich habe ihn NICHT stillschweigend erfunden, sondern als Konflikt/Frage 5 offengelegt; A-Tickets(3) hängen bis zur Entscheidung „in der Luft".

---
*Reine Analyse — nichts an Nav/Routen/Code geändert. Belege: A-Punkte via ist-befund + Agent-Nav-Audit (Datei:Zeile/Route), playground via inventar-playground, Weichen via architektur-entscheidungen, Live via route:list 2026-07-03.*
