# Transplant-Vorbereitungs-Landkarte: wberechnung → ticket (Phase 1.4)

> **Reine Planung / Landkarte — kein Code, kein Bau, nichts an wberechnung geändert (nur gelesen).**
> Zweck: den späteren Umzug der wberechnung-Auslegungs-Suite nach ticket **belegt** vorbereiten
> (Funktionsumfang, Navi-Platz, Design/CI, Tabellen-Kollisionen, Reihenfolge), damit Phase 1.4
> ohne Überraschungen läuft. **Gebaut wird erst am Cut-over** (vgl. `stopp-1-eintrittsgate-phase-1.md`,
> Teil I.2: Migrations-Kurve flacht ab). Stand: 2026-07-04.
> Gate-Vorbedingung erfüllt: MySQL-Tauglichkeit bewiesen (271/271, ebd. Teil B.6).

---

## Kurzfassung (für die eilige Entscheidung)

- **Was zieht um:** keine reine „Heizlast", sondern eine komplette **PV+Wärmepumpe-Auslegungs-Suite**
  (Energiekonzept, Heizlast DIN 12831, WP-/WR-Auslegung, PVGIS/Solar, Sanierung, EN442/EN1264-Checks,
  Plan-Import, Grundriss-Editor, Komponenten-Kataloge) — 14 Feature-Gruppen, 271 Tests.
- **Wohin in der Navi:** ticket hat den Platz **schon** — Bereich **8 „Energie & Auslegung"** existiert,
  seine Punkte sind heute **Prototyp** (*„Rechenkerne dormant"*). **wberechnung ist genau diese Kerne.**
  → Einpassen, nicht neu erfinden.
- **Design:** Logik transplantiert weitgehend 1:1; **alle 31 Blade-Views werden neu gebaut**
  (Tailwind/Alpine → Vuexy/Bootstrap), weil ticket-Design verbindlich ist.
- **Härteste Punkte:** `users`/Auth-Merge, doppelte PV-Kataloge (`inverters`/`batteries` existieren in
  beiden mit unterschiedlichem Schema), und wberechnungs eigene Artikel-/Lager-Welt vs tickets `products`.

---

## Achse ① — Funktionsumfang (was genau umzieht)

Belegt aus `routes/web.php` (57 Routen), 14 Controllern, 48 Services, 43 Migrationen.

### Kern-Rechenkerne (der eigentliche Wert — hier steckt die Ingenieurleistung)
| Werkzeug | Route(n) | Norm/Inhalt |
|---|---|---|
| **Heizlast-Tool** | `heizlast.*` | WP-Auslegung Verbrauchs- + Heizlastmethode, Förderung, speichern |
| **Heizlast-Projekt** | `heizlast-projekt.*` | Raumweise DIN EN 12831-1 / DIN TS 12831-1, Nachweis-PDF, CRUD |
| **WP-Auslegung** | `energiekonzepte.auslegung` | Bivalenz, Strom-Split, JAZ, Saison, Vorschlag |
| **WR-Auslegung** | `wr-auslegung.*` | Wechselrichter-Sizing (StringBuilder, InverterSizing/-Suggestion-Services) |
| **PV-Ertrag** | `pvgis`, `solar` | PVGIS-Ertrag + Google Solar API (Dachsegmente) |
| **Sanierungsrechner** | `sanierung.*` | Vorher/Nachher, Φ_HL, BAFA-Hülle |
| **Heizkörper-Check** | `heizkoerper-check.*` | EN 442, m²→Bedarf je Systemtemperatur |
| **Fußboden-Check** | `fussboden-check.*` | EN 1264, FBH-Deckung bei niedriger Vorlauftemperatur |

### Datenhaltung / Entität
| Baustein | Route(n) | Inhalt |
|---|---|---|
| **Energiekonzept** | `energiekonzepte.*` (resource) + `.angebot` | zentrale Projekt-Entität, Dashboard-KPIs (Gewinn/CO₂/Autarkie), Beratungs-/Angebots-PDF |
| **Komponenten-Kataloge** | `katalog.*` | PV-Module, Wechselrichter, Batterien (+ Specs), generisches Admin-CRUD |
| **Wärmepumpen** | `waermepumpen.*` | WP-Katalog (index/show) + Leistungskurve |

### Werkzeuge / Beiwerk (schwerer, Umzug evtl. gestaffelt/später)
| Werkzeug | Route(n) | Warum heikel |
|---|---|---|
| **Plan-Import** | `plan.*` | DWG/DXF/PDF/Bild-Upload + **Queue-Klassifikation** (Storage + Jobs) |
| **Grundriss-Editor** | `grundriss.*` | 2D-**SVG-Editor**, Maßstabs-Kalibrierung — komplexeste interaktive View |
| **Materialliste** | `materialliste.index` | WP-Montage-Sets → überlappt mit tickets Material/Artikel |

> **Priorisierungs-Hinweis:** Die **Kern-Rechenkerne** (obere Tabelle) sind der Grund für den Umzug und
> transplantieren am saubersten (Service-Logik, wenig DB-Kopplung). **Plan-Import + Grundriss-Editor**
> sind Kandidaten für eine **spätere** Welle (schwere Views + Queue/Storage).

---

## Achse ② — Navi-Platzierung in ticket

**ticket hat den Bereich bereits:** NAV-01 Bereich **8 „Energie & Auslegung"** (*Alt: Planung & Auslegung ·
Technik*, „Alleinstellung", Punkte als **Prototyp** markiert, weil *„ticket: Rechenkerne dormant"*).
Der Playground-Plan sah dort schon „Energie-Tools (WP/WR-Auslegung/Konfigurator/Dachplaner/Lastmanagement)"
vor. **wberechnung füllt diese dormanten Prototypen mit realen Engines.**

**Empfohlenes Mapping (wberechnung → Bereich-8-Unterlinks):**
| Bereich-8 Unterlink (heute) | wird gefüllt durch |
|---|---|
| Auslegungen WP | Heizlast-Tool + Heizlast-Projekt + WP-Auslegung |
| Auslegungen WR | WR-Auslegung |
| Konfigurator | Energiekonzept + PVGIS/Solar |
| *(neu als Unterlinks/Tabs)* | Sanierung · Heizkörper-Check · Fußboden-Check |
| *(neu, evtl. später)* | Grundriss/Plan-Import |
| Förderungen (schon produktiv) | ggf. Anbindung an `heizlast.foerderung` |
| Komponenten-Kataloge | **Entscheidung:** Bereich-8-Tab **oder** Bereich 7 „Artikel" (siehe Achse ④) |

**Namens-Hinweis:** Das Modul ist breiter als „Heizlast". Vorschlag Modul-/Bereichsname = **„Energie & Auslegung"**
(= vorhandener Bereich-8-Name). Route-Namespaces bleiben sprechend (`heizlast.`, `wr-auslegung.`,
`energiekonzepte.`), unter ein `guest`/`auth`-freies **ticket-Web-Guard** gehängt (nicht wberechnungs eigenes Auth).

---

## Achse ③ — Design / CI

| | wberechnung | ticket |
|---|---|---|
| CSS | **Tailwind v4** (utility-first) | **Vuexy** (Bootstrap-basiert) |
| JS | **Alpine.js** | Blade + jQuery/Bootstrap-Welt |
| Build | Vite | (Vuexy-Assets) |
| Views | **31 Blade-Dateien** | Blade im Vuexy-Layout |

**Regel (verbindlich, gesetzt):** ticket-Design ist Pflicht; **fremde Views werden NICHT 1:1 übernommen**,
sondern im ticket-Design **neu gebaut**. wberechnungs Oberfläche ist **Vorlage für Funktion, nicht für Aussehen.**

**Konsequenz für den Umzug:**
- **Logik** (Controller, Services, Models, Berechnungen) transplantiert weitgehend **unverändert**.
- **Alle 31 Views** werden in **Vuexy/Bootstrap-Blade** neu aufgebaut; Tailwind-Klassen + Alpine-Interaktionen
  → Bootstrap-Markup + ticket-übliches JS. Das ist der **größte Handarbeits-Posten** des Umzugs.
- **Reihenfolge:** einfache Formular-/Ergebnis-Views zuerst; die **interaktiven** (Grundriss-SVG-Editor,
  Plan-Import-Dropzone) zuletzt — oder gekapselt als isolierte Widgets.

---

## Achse ④ — Tabellen-/Migrations-Kollisionen (43 wberechnung-Migrationen)

Literaler Abgleich der wberechnung-Tabellennamen gegen tickets Schema (410 Tabellen):

| wberechnung-Tabelle(n) | ticket-Status | Kollisionstyp | Empfehlung |
|---|---|---|---|
| `users` | existiert (eigenes `App\Models\User`) | **HART** (Tabelle + Model + Auth) | wberechnungs Auth/User **verwerfen**, tickets User/Guard nutzen; Fremdschlüssel auf `users` umbiegen |
| `migrations`, `cache`, `jobs` | existiert (Framework) | Infra | wberechnung-Versionen **weglassen** |
| `inverters` (54 Sp.) | existiert (**45 Sp.**) | **Schema-divergent, gleiche Domäne** | Yama-Entscheid: **ein** Katalog reconcilen (Spalten-Mapping) **oder** wberechnungs umbenennen |
| `batteries` (28 Sp.) | existiert (**32 Sp.**) | Schema-divergent | dito |
| `artikels`, `lieferants`, `bestellungs`, `lagerbestands`, `wareneingangs`, `bestellpositions`, `lieferant_artikels` | ticket hat `products`/`article_groups`/Lieferanten/Inventar | **Semantisch dupliziert** | Yama-Entscheid: Heizlast nutzt **tickets** Artikel-/Lager-Welt **oder** eigener Parallel-Katalog |
| `pv_modules`(+`_specs`), `inverter_specs`, `battery_specs`, `batterie_wr_kompatibilitaet`, `inverter_battery` | kein direktes Pendant (aber an inverters/batteries gekoppelt) | Folgt der Katalog-Entscheidung | mit inverters/batteries gemeinsam entscheiden |
| `energiekonzepts`, `waermepumpes`, `auslegungs`, `wp_auslegungs`, `heizlast_projekte`, `heizlast_raeume`, `heizlast_bauteile`, `heizkoerper`, `konstruktionen`, `fenster_specs`, `sanierungs_varianten`, `raum_geometrien`, `baualtersklassen`, `materials`, `plan_uploads` | **kein ticket-Pendant** | **Sauber** | direkt transplantieren (Präfix optional zur Klarheit) |

**Kernrisiken:** (1) der `users`/Auth-Merge, (2) die **doppelten PV-Kataloge** (beide Apps haben
`inverters`/`batteries`, unterschiedlich geschnitten — ticket ist ein Solar-CRM und pflegt bereits
Wechselrichter/Batterien), (3) die **doppelte Artikel-/Lager-Domäne**. Diese drei sind
**Architektur-Entscheidungen, keine Mechanik** — sie gehören vor den Umzug geklärt.

---

## Achse ⑤ — Empfohlene Umzugs-Reihenfolge (Phase 1.4)

1. **Entscheidungen einholen** (unten „Offene Yama-Entscheidungen") — v. a. Katalog-Reuse + Artikel-Reuse,
   sonst baut man Duplikate ein.
2. **Datenschicht:** saubere Domain-Migrationen nach ticket (ohne `users`/`cache`/`jobs`); FKs auf tickets
   `users` umbiegen; Kollisions-Tabellen (`inverters`/`batteries`/`artikels`) gemäß Entscheid reconcilen
   oder präfixen. **Abnahme:** `migrate:fresh` gegen `ticket_testing` grün.
3. **Logik:** Controller/Services/Models portieren; `App\`-Namespace-Konflikte auflösen
   (v. a. `App\Models\User`, evtl. `Artikel`/`Lieferant` vs tickets Product-Modelle).
4. **Routen:** `heizlast.` / `wr-auslegung.` / `energiekonzepte.` unter Bereich 8 einhängen, ticket-Web-Guard.
5. **Views:** 31 Blades in Vuexy neu bauen (interaktive zuletzt).
6. **Tests:** die 271 mitportieren, gegen **`ticket_testing`** grün fahren — **das ist die Phase-1.4-Abnahme**
   (die ticket-Isolation ist bereits verdrahtet). Bis dahin bleibt der wberechnung-MySQL-Re-Check
   (`scripts/wberechnung-mysql-check.sh`) die laufende Referenz.
7. **Cut-over:** wberechnung einfrieren, sobald die 271 in ticket grün sind und Bereich 8 produktiv ist.

---

## Yama-Entscheidungen (2026-07-04 — getroffen)

1. **Modul-/Bereichsname:** ✅ **„Energie & Auslegung"** (= Bereich-8-Name). *(Weiche 3)*
2. **PV-Katalog:** ✅ **KEIN Doppel-Katalog.** Tickets `inverters`/`batteries` (+ `products`/`article_groups`)
   sind die **einzige** Katalog-Wahrheit — **inklusive der technischen Daten, die die Rechenkerne brauchen**.
   wberechnungs `inverters`/`batteries` werden **nicht transplantiert**, sondern in tickets Katalog **überführt**. *(Weiche 1)*
3. **Artikel/Lager:** ✅ Heizlast/Materialliste an **tickets** `products`/`article_groups`/Lieferanten anbinden;
   wberechnungs `artikels`/`lieferants` **nicht** mitziehen, sondern **mappen/überführen**. *(Weiche 2)*
4. **Tabellen-Präfix:** ✅ **kein Präfix** für die belegt kollisionsfreien Domain-Tabellen (`heizlast_*`,
   `energiekonzepte`, …) — die Kollisions-Karte (Achse ④) trägt das. *(Weiche 4)*
5. **Plan-Import + Grundriss-Editor:** ✅ **spätere Welle** (schwerste Views + Queue/Storage). *(Weiche 5)*

> **Folge (Pflicht-Vorbedingung):** Die **Katalog-Reconciliation** (tickets Katalog additiv erweitern +
> wb-Daten überführen + **Adapter-Schicht**, über die die Rechenkerne aus tickets Katalog lesen) ist jetzt
> **Vorbedingung** der Rechenkern-Transplantation. Detailplan: **`docs/katalog-reconciliation-plan.md`**.

---

## Was draußen bleibt / Risiken

- **wberechnungs Auth/Login** (`Auth\LoginController`, eigenes `users`) — bleibt draußen; ticket-Auth gilt.
- **Framework-Delta:** wberechnung PHPUnit ^12.5 / Laravel-11-Skeleton mit Tailwind4-Vite; ticket eigene
  PHPUnit-/Vuexy-Welt. Beim Test-Port auf tickets Test-Setup achten (nicht wberechnungs `phpunit.xml`).
- **Bewegtes Ziel:** wberechnung wächst weiter (262→266→271). Diese Landkarte ist ein **Schnappschuss**;
  vor dem Bau gegen den dann aktuellen Stand gegenprüfen (Route-/Migrations-Liste neu ziehen).
- **Kein stiller Doppel-Katalog:** das größte fachliche Risiko ist, `inverters`/`batteries`/`artikels`
  doppelt zu führen — deshalb stehen die Reuse-Fragen **vor** dem Bau.
