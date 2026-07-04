# UX-/Layout-/Frontend-Audit — ticket-CRM

> **Reine Analyse (nur Lesen), kein Bau, keine Datei geändert außer diesem Doc.** Maßstab: nicht „hübsch", sondern *Wie schnell findet ein Projektleiter morgens das Wichtige? Wie viele Klicks bis zur häufigsten Aktion? Wie viel Schulung braucht ein Neuer?* Nutzer = Büro-MA (~40) eines gewerkeübergreifenden Handwerksbetriebs, **keine IT-Profis**. Baut auf der 8-Zonen-Inventur ([crm-inventur-00-index.md](crm-inventur-00-index.md)) + [kundenprofil-struktur-bestandsaufnahme.md](kundenprofil-struktur-bestandsaufnahme.md) auf — **bewertet** sie UX-seitig, inventarisiert nicht neu, und **stichprobt gegen Live** (Inventur-Stand kann durch letzte Commits abweichen). Belege: Datei:Zeile, Zählungen, Klick-Pfade, `route:list`. Stand 2026-07-03. Evidenz über 4 parallele Explore-Agenten + eigene Messungen + Live-Stichproben erhoben.

---
## TEIL 1 — TECHNISCHE FRONTEND-BESTANDSAUFNAHME (gemessen)

### 1.1 Stack + Struktur
- **Blade + jQuery 3.7.1 + Bootstrap + select2 + Quill 2.0.2 + FullCalendar 6.1.15 + Vue 3.2.36** (Vue kaum genutzt). Theme = Vuexy (`public/app-assets`).
- **⚠️ Bootstrap-Versions-Chaos:** `package.json` deklariert `^5.3.7`, ausgeliefert wird `/public/app-assets/css/bootstrap.css` = **v4.3.1 (2019)**, zusätzlich ein **v5.1.3-JS-Artefakt** im Baum. Drei BS-Stände nebeneinander. *(Runtime-aktive Kombination NICHT VERIFIZIERT, aber der Mismatch ist belegt — package.json vs. served CSS.)* `tailwind 4.1.17` steht in package.json, ist aber **nicht** im Prod-CSS.
- **Top-Blades (Zeilen):** `offer/config` 25.064 · **`customer_profile` 19.727** · `new_leads/customer_profile` 19.338 · `master_sets/index` 15.270 · `offer/folder-show` 14.480 · **`dashboard/mobile` 13.431** · `todo/personal/calendar` 12.384 · `new_leads/layouts/profile` 12.352 · `layouts/app` 11.145 · **`kanban/kanban` 5.096**.

### 1.2 Konsistenz — kein Design-System (gemessen über 7 Kern-Views)
| Metrik | Wert | Beleg |
|---|---|---|
| **Einzigartige Hex-Farben** | **253** | grep `#[0-9a-f]{3,6}`, sort -u (Top: #93c21c grün 102×, #74b2d4 blau 90×) |
| **Button-Klassen-Muster** | **152** | distinct `btn-*`-Kombinationen |
| **Modal-Frameworks** | **5** | Bootstrap `.modal` (41×) + eigene `.cmodal`/`.cpwf-modal`/`.ccp-modal` + Drawer (42×) |
| **@includes / 7 Views** | **29** (4,1/View) | „Komponenten-Bibliothek" existiert, wird kaum genutzt |

**Drei „Speichern"-Buttons, drei Stile** (gleiches Label, andere Semantik): `customer_profile:2459` **`btn-success`** (grün) · `customer_profile:2654` **`btn-primary`** (blau) · `new_leads/customer_profile:845` **`btn-primary`**. → Nutzer sieht widersprüchliche Affordanz. **Design-System: NEIN** (belegt).

### 1.3 Monolith-Blades + Performance
- **91,8 % Inline-Code** im Schnitt. `customer_profile` 19.727 Z. = **15.595 Z. Inline-JS + 2.715 Z. Inline-CSS (92,8 %)**; `new_leads/customer_profile` 17.550 Z. Inline-JS. → **Jede Änderung an einer View berührt 19k-Zeilen-Dateien** = extremes Wartungsrisiko.
- **Kundenprofil ~2,2 MB** Seitengewicht (Inventur-bekannt) + 15,6k Z. Inline-JS render-relevant.
- **N+1 bekannt:** `context()` per Karte (Ebenen-Befund) — für Board-Badges ungeeignet, `summaries`-Batch existiert (siehe Verzahnung Stufe C).
- **N+1 GEMESSEN im `summaries`-Batch (2026-07-04, Stufe-C-Verifikation):** 1 HTTP-Call, aber intern **≈4 Queries/Karte** — **8 Queries bei 1 Karte → 221 bei 53** (`KanbanLeadTaskController::summaries`/`summaryPayload`, Task-/Template-Laden je Karte). Wächst **linear mit dem Betrieb** → bei 200+ Karten spürbare Board-Ladezeit. **VOR dem echten Wachstum fixen** (Batch-Laden von Tasks/Templates über `lead_product_list_id IN (…)` statt je Karte), nicht erst bei Beschwerden. Stufe C selbst fügte **0 Queries** hinzu (Filter auf geladener Collection). → Quick-Win-Kandidat, s. Welle 1.
- Profil-Struktur: von ~31.700 Z. beider Profil-Blades sind nur **~2.800 Z. echte Bereiche**, ~29.000 Z. CSS/JS (Beleg: kundenprofil-struktur §0).

### 1.4 Responsive / Mobile
- **Viewport-Meta vorhanden** (`width=device-width, user-scalable=0`). **128 @media-Queries** über die 7 Views.
- **Aber 180 feste px-Breiten** in den Top-3 (customer_profile 78 · kanban 63 · new_leads/customer_profile 39) → überschreiben Media-Queries. **Responsive ist geflickt, nicht nativ (≈4/10).** Baustellen-Tablet: eingeschränkt nutzbar (NICHT im Browser gemessen — Beleg = px-Breiten-Zählung).

---
## TEIL 2 — UX DER 5 KERN-FLÄCHEN (Layout / Inhalt / Funktion)

### (A) Leads-Kanban (`kanban/kanban.blade.php` 5.096 Z. + `public/js/kanban.js`)
- **Layout:** Filterleiste (:3268-3340) + 4-6 Spalten-Header + ~20 Karten/Spalte; Zoom-Controls (:3541-3572).
- **Inhalt — Karte zeigt 9 Felder** (kanban.js:2030-2200): Kundenname · Produkt-Initial · Datum · Adresse · Sub-Stage-Chip · nächster-Schritt-Preview (*eingeklappt, default unsichtbar*) · 2 MA-Avatare · Team-Pill · Aktions-Buttons. **🔴 Kern-Lücke: kein Auftragswert, kein Überfällig-/Stillstand-Indikator, keine Dringlichkeit** → alle Karten wirken gleich; der Vertrieb sieht am Board NICHT, welche Karte Aufmerksamkeit braucht.
- **Funktion:** Verschieben 1 Klick · Menü+Aktion 2 Klicks · Notiz 1 Klick. **12 tote Elemente** (4 disabled Filter-Buttons :3305/3310/3315/3322 + 8 CSS-Verstecke alter Termin-UI :575-583).

### (B) Kundenprofil (`customer_profile.blade.php` 19.727 Z. + `layouts/profile.blade.php`)
- **Layout — DREI überlappende Nav-Mechanismen** (kundenprofil-struktur §1): (1) Top-Nav Info/Historie/Aktivität (:5033) · (2) Bereichs-Nav **12 Punkte** datengetrieben (:5920-6115) · (3) Feed-Switcher **7 Typen** (:6373). **Überlappende Labels** (Angebot/Auftrag/Aufgaben/Tickets/Termine in #2 UND #3). 10+ Sektionen.
- **Inhalt — 🔴 die 6-Phasen-Achse fehlt komplett** (0× `lead_stage` im Profil-Blade, kundenprofil-struktur §3): Phase + nächster Schritt **>5 Sekunden** (2-3 Klicks: Sidebar→Produkt→Detail). Kein Umsatz in der Übersicht. **Stärke:** Objekt-Zentrierung stark (`alternative` 279×), Kunde→Objekt→Gewerk verschachtelt sichtbar (Galerie mit Google-Map/Street-View je Objekt).
- **Funktion:** 4-5 unerreichbare Modals (purchaseModal ohne Trigger, priceHistory-Drawer unklar).

### (C) Objekt-Profil (`new_leads/object.blade.php` 2.145 Z., teilt `layouts/profile`)
- Gleiches 3-Panel-Layout, Adresse-first + Produkt/Service/Abteilung-Tabs + Google-Map. Schlanker als das Kundenprofil. Dead: 2 evtl. leere Tabs.

### (D) Dashboard /home (`dashboard/employee/mobile.blade.php` 13.431 Z.)
- **8 Widgets** (CSS-Grid `grid-personal`): Uhr · HR/Monatsübersicht · Absence-Request · **Mein Arbeitstag (Focus-Today)** · **Zu prüfen (NEU, fa41c61)** · **Meine Follow-ups (NEU, 20a493d)** · Arbeitsstunden-Chart · Meine Notizen.
- **🔴 Fehlt für „was ist heute wichtig":** überfällige Angebote · Umsatz/Pipeline-at-risk · Team-Kapazität · nächste 3 Termine **above-the-fold** · SLA-/Alters-Indikator. Focus-Today deckt Aufgaben/Termine/Tickets ab, aber nicht Wert/Überfälligkeit. Act-on-due = 2-4 Klicks.

### (E) Aufgaben/Termine (`todo/personal/calendar.blade.php` 12.384 Z.)
- FullCalendar Monat/Woche/Tag, **phasen-farbcodiert** (blau=lead, orange=offer …). **Fehlt: Überfällig-Rot für Vergangenheit, Konflikt-Erkennung (Überlappungen), Wiederholungs-Marker.** 2-3 Klicks/Aktion.

**→ 22 tote UI-Elemente über alle Flächen** (Kanban 12 · Profil 4 · Objekt 2 · Dashboard 3 · Kalender 1) = kognitive Last („was ist aktiv, was Platzhalter?").

---
## TEIL 2b — GESAMT-NAVIGATION (`admin/layouts/sidebar.blade.php`, 2.103 Z., Array :398-1340, eingebunden `app.blade.php:4451`)

### (i) Umfang — datengetriebenes `$sidebarSections`-Array
**14 Sektionen, 116+ Punkte:** Arbeitsbereich(2) · Berichte(3) · **CRM(21)** · Vertrieb(10) · Projekte(9) · Support(3) · **Personal(29)** · **Artikel & Lager(21)** · Finanzen(3) · Admin(4) · Konfiguration(3) · Tools(2) · System(4) · Einstellungen(1) · Wissen(1). 11 Sektionen permission-gated (`user_rolls`, `is_admin`-Bypass).

### (ii) Nav-Hygiene
| Befund | Anzahl | Beleg |
|---|---|---|
| Tote Routen (404) | **0** | alle 116 live (route:list) |
| **Legacy-Link** | **1** | 🔴 `chats.view` → **`MessageController@index`** (alter Bitrix-Chat, Inventur-Claim **live bestätigt**; Agent-„0 legacy" hiermit korrigiert) |
| Duplikat | **1** | „Rechnungen" + „Rechnungen (Canvas-Hinweis)" → beide `invoices.index` (:742-758) |
| Tippfehler | 1 | `/wating_leads` (funktioniert via Route-Name) |
| **Orphan Arbeitsfläche** | 1 | 🔴 **B1-Pflege `phase_management/{product}/{section_id}`** ist parametrisierte Kontext-Route — **nicht direkt navigierbar**; nur die Phasen-*Vorlage* `task_phase.index` steht in der Nav. Die B1-Prüflisten-Pflege erreicht man nur über ein Gewerk. |

### (iii) Architektur-Urteil
- **Modul-zentrisch, historisch gewachsen — NICHT arbeits-/rollen-zentrisch.** 14 Sektionen × bis 29 Punkte = eine **Ablage nach Datenbanktabellen**, nicht nach Tagesarbeit. Für ~40 MA sind **Personal(29) + Artikel&Lager(21) + CRM(21)** überdimensioniert; ein Vertriebler scrollt durch HR/Lager/Admin, um zu „Leads" zu kommen.
- **Tiefe:** 2-3 Ebenen bis zur Arbeitsfläche (Sektion→Untermenü→Punkt).
- **Benennung** teils fachfremd (Arbeitsprozess vs. Aufgaben vs. Arbeitstag — der neue MA unterscheidet das nicht).
- **Benchmark:** Monday/Asana/Linear = **flache, arbeits-zentrierte** Nav („Meine Arbeit / Boards / Kunden") mit **Cmd+K-Sprung**; ticket = modul-zentriertes Mega-Menü ohne Suche.

### (iv) Nav-Zielbild (Vorschlag, verzahnt mit den 3 Profil-Navs)
Konsolidierung auf **6-7 arbeits-zentrierte Gruppen** (Aufwand **M** je Gruppe, das Array ist datengetrieben = markup-arm umbaubar):
1. **Mein Bereich** (Dashboard, meine Aufgaben, Follow-ups, Zu-prüfen, meine Termine)
2. **Kunden & Objekte** (Leads-Kanban, Kundenprofile, Objekte, Kontakte)
3. **Vertrieb** (Angebote, Aufträge, Rechnungen — Duplikat auflösen)
4. **Projekte & Montage** (Planer, Aufgaben-Phasen inkl. **B1-Pflege sichtbar machen**, Wartung)
5. **Stammdaten** (Artikel, Lager, Sets/Kalkulation)
6. **Verwaltung** (Personal/HR, Finanzen, Admin, System) — für die meisten Rollen eingeklappt/gegated
7. **Suche/Cmd+K** (neu) + Chat-Link auf den **modernen** Chat umhängen.

---
## TEIL 3 — BENCHMARK-VERGLEICH (aus Wissen, kein Web-Zugriff)

| Fläche | Best-in-Class-Muster | ticket heute | Übertragbar? | Aufwand |
|---|---|---|---|---|
| **Board-Karte** | Monday/Pipedrive: **Alters-Indikator, Wert, Avatar, Farb-Status, Stillstand** | 9 Felder, **kein Wert/Alter/Dringlichkeit** | **JA** (Daten da: created_at, offers.price, updated_at) | **S** |
| **Datensatz-Seite** | Salesforce/HubSpot: **Header mit Kern-Feldern + Aktivitäts-Timeline + Tabs** statt Endlos-Scroll | 3 Navs, Phase versteckt, 19k-Monolith | **JA** (aber L) | **L** |
| **Command/Suche** | Linear: **Cmd+K** global | keine globale Suche/Sprung | **JA** | **M** |
| **Dashboard** | Asana/Monday: **„Meine Arbeit"-zentriert** | 8 Widgets, „Mein Bereich" **schon angelegt** (fa41c61/20a493d) | **JA — Muster steht bereits** | **S-M** |
| **Formulare** | Odoo: **Inline-Edit** statt Modal-Kaskaden | 5 Modal-Frameworks, Drawer-Kaskaden | teilweise | **L** |

**Ehrlicher Abstand:**
- **ticket ist STRUKTURELL BESSER**, wo Generalisten nichts haben: **Gewerke-Klammer** (Kunde→Objekt→Gewerk verschachtelt), **Montage-Fortschritt** (planner_items), **Qualifikations-Prüfung** (B3), **objekt-zentrierte Karte/Street-View**. Das ist echte Branchen-Tiefe.
- **Kosmetischer Abstand:** Farb-/Button-/Modal-Wildwuchs (253/152/5) — reparierbar ohne Struktur.
- **Fundamentaler Abstand:** Monolith-Blades (92 % inline), Nav-Tiefe, fehlende Board-Signale, fehlende Phasen-Achse im Profil, keine globale Suche.

---
## TEIL 4 — TOP-15-SCHWÄCHEN + 3-WELLEN-ROADMAP

### Top-15 (Beleg · Schmerz · Benchmark · Aufwand · Risiko)
| # | Schwäche | Beleg | Schmerz (wer) | Benchmark | Aufw. | Risiko |
|---|---|---|---|---|---|---|
| 1 | Karte ohne Wert/Alter/Überfällig | kanban.js:2030-2200 (9 Felder, keine Signale) | Vertrieb/PL sieht Prioritäten nicht | Monday/Pipedrive | **S** | niedrig |
| 2 | Dashboard ohne „heute": Umsatz/überfällig/Termine | mobile.blade 8 Widgets | PL/Buchhaltung | Asana | **S-M** | niedrig |
| 3 | 253 Farben / 152 Buttons / 3 Speichern-Stile | §1.2 | alle (Verwirrung) | Design-Token | **S** | niedrig |
| 4 | 22 tote UI-Elemente | Teil 2 | alle (kogn. Last) | — | **S** | niedrig |
| 5 | 6-Phasen-Achse fehlt im Profil | 0× lead_stage | alle | Salesforce Timeline | **M** | mittel |
| 6 | Phase+Schritt >5 Sek (3 Navs) | kundenprofil-struktur §3-4 | alle täglich | HubSpot Header | **L** | mittel |
| 7 | Nav modul-zentrisch, 14×116, 2-3 tief | sidebar §(iii) | Neue MA/alle | Monday flach | **M** | mittel |
| 8 | Legacy-Chat-Link | chats.view→MessageController | wer chattet | — | **S** | niedrig |
| 9 | B1-Pflege nicht navigierbar | phase_management contextual | PL (Prozess-Pflege) | — | **S** | niedrig |
| 10 | Kalender ohne Überfällig-Rot/Konflikte | calendar.blade | Dispo | Outlook/Google | **S-M** | niedrig |
| 11 | Monolith-Blades 92 % inline | §1.3 | Entwickler/Tempo | Komponenten | **L** | hoch |
| 12 | Bootstrap-Versions-Chaos (4.3.1/5.1.3/5.3.7) | §1.1 | Rendering-Wanzen | eine Version | **M** | mittel |
| 13 | Responsive geflickt (180 px-Breiten) | §1.4 | Tablet-Nutzer | native Grids | **M** | mittel |
| 14 | Keine globale Suche/Cmd+K | Nav | alle (Sprung) | Linear | **M** | niedrig |
| 15 | Modal-/Drawer-Kaskaden (5 Frameworks) | §1.2 | alle | Odoo Inline | **L** | mittel |

### Roadmap in 3 Wellen
**Welle 1 — Quick Wins (S, ohne Verhaltensrisiko):**
- **Karten-Signale** (Alter aus created_at, Wert aus offers.price, Überfällig-Rot, Stillstand aus updated_at) — #1. ⚠️ **gehört in Kanban Stufe C** (siehe Verzahnung), nicht separat.
- **Konsistenz-Partial** (EIN `x-save-button`, Farb-Token-Datei, ein Modal-Muster) — #3.
- **Tote UI entfernen** (22 Elemente) + **Nav-Duplikat** + **Chat-Link umhängen** + **B1-Pflege in Nav** — #4/#8/#9.
- **Dashboard-Widgets** „Überfällige Angebote" + „nächste 3 Termine" + „Umsatz-at-risk" nach dem **Mein-Bereich-Muster (fa41c61/20a493d)** — #2.
- **`summaries`-Batch entzerren** (Perf): Tasks/Templates **einmal** über `lead_product_list_id IN (…)` laden statt ≈4 Queries/Karte (gemessen 221 bei 53 Karten, §1.3). Isolierter Schritt, kein Verhaltensrisiko; **vor** dem Karten-Wachstum (200+) fixen. Klein-Schuld gleich mit: reported-Pill-Farbe in `kanban.js` steckt als **Inline-Style** (`#fff3cd`) statt CSS-Klasse (Stufe C) → in die Konsistenz-Token-Datei ziehen.

**Welle 2 — Eine Fläche richtig (M): DAS BOARD.** Begründung: **Kanban Stufe B/C fasst das Board ohnehin an** → das Layout-Urteil (Karten-Signale, Informationsdichte, Badges) **MUSS in Stufe C einfließen**, sonst wird zweimal gebaut. Danach die **Nav-Konsolidierung** (Zielbild §iv).

**Welle 3 — Strukturell (L):**
- **Profil-Redesign** (Fahrplan Ebene 2): Header mit Kern-Feldern + Phasen-Achse + Aktivitäts-Timeline + Tabs statt 3 Navs/Endlos-Scroll. **Nav-Vorgabe kommt aus §2b(iv)** — verzahnen, nicht doppeln. JS-Laufzeit-Klärung (was `main-content`/`phaseSidebar` real zeigen) ist Voraussetzung (NICHT VERIFIZIERT).
- **Blade→Komponenten** + **eine Bootstrap-Version** + **native Responsive**.

### Verzahnung mit laufenden Strängen (explizit)
- **Kanban Stufe B (Rendering aus FK):** rein technisch (String→lead_stage_id). **Stufe C (Badges/Karten-Anreicherung)** ist der Ort für **Karten-Signale #1** (Wert/Alter/Überfällig) + die Board-Informationsdichte → **Welle-1-Empfehlung #1 gehört in Stufe C**, nicht separat.
- **Profil-Redesign (Fahrplan Ebene 2):** bekommt aus diesem Audit seine **Nav-Vorgabe** (§2b iv: die 3 Profil-Navs → eine Achse + Inhalt) + das Timeline/Header-Muster (Teil 3).
- **„Mein Bereich"-Widgets (fa41c61/20a493d):** **taugt als Design-Referenz — JA.** Das self-contained `<article class="widget">`-Muster (Header/Icon/Pill-Zähler/@once-JS) ist die sauberste Komponente im Baum und die Blaupause für weitere Dashboard-Widgets (überfällig/Umsatz/Termine).

---
## Gelesen / NICHT gelesen (ehrlich)
**Gelesen/gemessen:** crm-inventur-00-index + kundenprofil-struktur-bestandsaufnahme (Pflicht-Kontext, voll); Top-12-Blades nach Zeilenzahl; via 4 Explore-Agenten: 253 Hex/152 Buttons/5 Modals/29 @includes/91,8 % inline (7 Views), Bootstrap/jQuery/Vue-Versionen, 128 @media/180 px-Breiten; 5 Flächen (Karten-Felder, Widget-Liste, Klick-Pfade, 22 tote Elemente); Gesamt-Nav (sidebar.blade 116 Punkte, route:list-Liveness). **Live-Stichproben:** chats.view→MessageController (Legacy bestätigt), phase_management-Kontext-Route, sidebar 2.103 Z.
**NICHT verifiziert:** echte **Browser-Render-Zeiten/Netzwerk-Wasserfall** (Seitengewicht 2,2 MB ist Inventur-Wert, nicht neu gemessen); **Tablet-Nutzbarkeit** nur über px-Breiten-Zählung, nicht im Gerät; was die **JS-befüllten Profil-Panels/`phaseSidebar` zur Laufzeit** zeigen (die „6 Phasen fehlen"-Aussage gilt belegt fürs Blade, nicht die AJAX-Laufzeit); ob die 152 Button-/253 Farb-Werte teils aus Vendor-CSS stammen (Agent zählte über die Views, Vendor-Anteil NICHT abgegrenzt); die Objekt-Profil-Tiefe nur kursorisch.

## Selbstkritik
- **Agenten können irren:** Agent „0 Legacy" war falsch (chats.view IST MessageController) — nur die Live-Stichprobe hat's gefangen. Andere Agenten-Zahlen (253/152) sind **nicht** einzeln nachgestichprobt → als Größenordnung, nicht als exakte Wahrheit lesen.
- **„Fehlt für heute" ist teils Wertung:** dass Umsatz/Team-Kapazität aufs Dashboard *gehören*, ist Benchmark-Analogie (Asana/Monday), kein belegter Nutzer-Wunsch — Yama priorisiert.
- **Kein Neubau-Vorschlag im Detail:** der Betrieb läuft live; die Roadmap bewertet Verbesserbares mit Aufwands-/Risiko-Klasse, entwirft aber keine fertigen Screens. Welle 3 (Profil/Blade-Zerlegung) ist bewusst als „verzahnt mit bestehendem Fahrplan" markiert, nicht als neuer Strang.
- **Board-Signale doppelt genannt** (Welle 1 #1 + Verzahnung Stufe C) — bewusst: es ist ein Quick Win, der aber in den laufenden Board-Strang gehört, nicht daneben.

---
*Reine Analyse — nichts am Code/Schema geändert. Grundlage: [crm-inventur-00-index.md](crm-inventur-00-index.md), [kundenprofil-struktur-bestandsaufnahme.md](kundenprofil-struktur-bestandsaufnahme.md); 4 Explore-Agenten (Nav/Konsistenz/5-Flächen/status-Schreiber) + Live-Messungen + Stichproben. Belege: Datei:Zeile inline, Zählungen, route:list 2026-07-03.*
