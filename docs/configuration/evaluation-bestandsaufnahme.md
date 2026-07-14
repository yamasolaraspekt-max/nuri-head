# Evaluation — Unabhängige Prüfung der systemweiten Bestandsaufnahme „Bedarfsgeführte Konfiguration & Auslegung"

**Rolle:** unabhängiger read-only EVALUATOR (Generator ≠ Evaluator) · **Stand:** 2026-07-14 · **Git:** `a00bb0a`
**Prüfgegenstand:** sechs Dokumente unter `docs/configuration/` (ADR-0001, Gesamtarchitektur, Anforderungs-/Lückenmatrix, Modul-/Abhängigkeitsmatrix, Umsetzungsfahrplan, Vollständigkeitsbericht).
**Methode:** eigenes Nachmessen (grep/awk/`route:list`/Quelltext) statt Nachlesen. Jeder Befund mit Beleg (Datei:Zeile / Befehlsausgabe).

> **Abschlussurteil vorab: 🔴 ROT** — ausgelöst durch **einen** materiell falschen Kernbefund: der als „kritischster Punkt" markierte Sicherheitsbefund **SEC-03 (`/ids/callback` öffentlich schreibend ohne Auth) ist WIDERLEGT**. Die Route läuft nachweislich hinter `App\Http\Middleware\Authenticate`. Alle übrigen Kernaussagen (Zählung, Matrix-Vollständigkeit, Geometrie-Grundrisiko, Stale, Doppel-Positionsstruktur) sind belastbar. Ein falscher Sicherheitsbefund ist per Prüfrubrik ein Rot-Auslöser; die Analyse ist ansonsten hochwertig und nach SEC-03-Korrektur auf **Grün** hebbar.

---

## 1. Git- & Scope-Prüfung

- **HEAD == `a00bb0a`** bestätigt: `a00bb0ad6c89c49da3b0fecd23df0897bf978c67` (`git rev-parse HEAD`).
- `git log --oneline -6`: `a00bb0a fix(wp): reject stale profile version saves` … `8554e71 feat(offer): read-only object configuration overview (AP-3a)`. Die sechs Prüf-Docs sind **nicht committet** (kein Commit im Analyse-Slice) — korrekt.
- **Kein Produktivcode / keine Migration / keine Route / keine View im Diff.** `git diff --name-only` = **nur `CLAUDE.md`** (tracked, +12 Zeilen). Inhalt = reine Governance-Direktiven (Startpflicht, Fachagenten, Arbeitskompass, Optimierungs-Reihenfolge, Kapitel-Startblock) — **kein** Produktivcode, **keine** der sechs Docs. Herkunft: vorbestehende Governance-Pflege, außerhalb des Analyse-Slice. **Hinweis (nicht blockierend):** diese tracked Änderung sollte als eigener Governance-Posten committet/gesondert behandelt werden, damit der Doku-Slice sauber bleibt.
- **Die sechs Prüf-Docs sind neue untracked Dateien:** `git status --short docs/configuration/` → `?? docs/configuration/`.
- **Geparkte P1a-Arbeit eindeutig GETRENNT** vom Doku-Diff: `tests/Feature/Energie/WpAuslegungCharakterisierungTest.php`, `EnergiekonzeptWpCharakterisierungTest.php` (untracked, eigener Ordner). Ebenso getrennt: die P1a/P0-Startblock-Docs (`docs/wp-stufe3b-*`, `docs/ap*`) — alle untracked, nicht in `docs/configuration/`.

**Alle uncommitteten Dateien (Herkunft vermutet):**

| Datei(en) | Status | Herkunft |
|---|---|---|
| `CLAUDE.md` | ` M` (tracked) | Governance-Pflege (außerhalb Slice) |
| `docs/configuration/**` (6 Docs) | `??` | **Prüfgegenstand** (Generator, dieser Slice) |
| `docs/configuration/evaluation-bestandsaufnahme.md` | `??` | **diese Datei** (Evaluator) |
| `tests/Feature/Energie/*Charakterisierung*.php` | `??` | geparkte WP-3b-P1a-Arbeit |
| `docs/wp-stufe1..3b-*`, `docs/ap2/ap3/ap4-*`, `docs/bereich2-*`, `docs/gesamtfahrplan-*`, `docs/playground-uebernahme-*`, `docs/wberechnung-uebernahme-*`, `docs/auslegungsworkflow-*`, `docs/arbeitskompass-ticket.md` | `??` | vorausgegangene Bestandsaufnahmen / Fahrpläne |

**Scope-Urteil:** sauber. Keine der Änderungen berührt Produktivcode/Migration/Route/View. Der einzige tracked-Diff (`CLAUDE.md`) ist Governance, keine Analyse-Ausgabe.

---

## 2. Gegenmatrix Masterentscheid ↔ Anforderungsmatrix

Der Masterentscheid ist im Repo nicht als eigene Datei greifbar (die sechs Docs referenzieren „Masterentscheidung §1–§20" als Grundlage); die Prüfung erfolgt gegen die im Vollständigkeitsbericht §13 selbst dokumentierte Paragraf→ID-Abbildung und die Modul-Aufzählung. Jeder Muss-Block hat ≥1 Matrix-ID:

| Quellabschnitt (§) | Muss-Anforderung | Matrix-ID(s) | vollständig übernommen? | Abweichung |
|---|---|---|---|---|
| §2 Architektur | Bedarf führt, Service-Schichtung | ARCH-01/04/07/08 | ja | — |
| §3 Produktvorgabe = Filter | Filter statt Eignungsnachweis, Warnungen | ARCH-02/03 | ja | — |
| §4 Modulaktivierung | dynamisch + Abhängigkeit | ARCH-05/06 | ja | — |
| §5 Datenwahrheit | Kunde/Objekt/Profil/Angebot | DATA-01..04, BLDG-01 | ja | — |
| §6 Datenübernahme/-prüfung | Übernahme + Prüf-Vorstufe | DATA-05..08 | ja | — |
| §7 Herkunft/Belastbarkeit | Herkunfts-/Vertrauensstufen | DATA-09/10 | ja | — |
| §8 Gebäudemodell/LiDAR | kanonisch, 2D primär, Eingangswege | BLDG-01..09, LIDAR-01..03 | ja | — |
| §9 Module | HL/HYD/WP/PV/BAT/WB/GRID/ROOF/WIN/DOOR/FAC | je Gruppe erfasst | ja | — |
| §10 Varianten/Auswahl/Freigabe | vier Zustände | ARCH-09/10, WP-04, OFFER-01/03 | ja | — |
| §11 Filter zeigt bessere Lösung | Filter-Hinweis | ARCH-02, UX-06 | ja | — |
| §12 Preis/Verfügbarkeit | getrennt, Geräte- ≠ Gesamtpreis | DATA-12/13/14 | ja | — |
| §13 UX | Kopf/Navigation/Arbeit/Ergebnis/Light+Dark | UX-01..06 | ja | — |
| §14 Servicegrenzen/Versionierung | dünne Controller + Stale | ARCH-07/08, DATA-15, SEC-01..04 | ja | — |
| §20 Nicht-Ziele | BIM/Tragwerk/KI/Bestell-/Preis-Erfindung/Parallel-DB | ADR-0001 „Nicht-Ziele" | ja (siehe §10 unten) | in ADR, nicht als eigene Matrix-Zeilen (bewusst — Nicht-Ziele sind Ausschlüsse, keine Anforderungen) |

**Matrix-IDs ohne Quellbezug:** keine gefunden. TEST-01..04 (Tests) und OFFER-01..04 (Angebotsübergabe) sind vom Masterentscheid §9/§14 gedeckt.
**Abnahmeblocker aus fehlender Quellabdeckung:** **keiner.** Die Matrix deckt jeden Muss-Block ab.

---

## 3. Zahlen- & Konsistenzprüfung (eigenes Nachzählen)

**Datenzeilen (awk über Zeilen mit ID in Spalte 1):** genau **80** Zeilen.

**Statuszählung (Spalte „Status", `awk -F'|' … | sort | uniq -c`):**

| Statuskategorie | Doc (Vollst.-Bericht) | Eigene Zählung | Δ |
|---|---|---|---|
| `besteht_belastbar` | 13 (16 %) | **13** | 0 |
| `besteht_teilweise` | 31 (39 %) | **31** | 0 |
| `fehlt` | 31 (39 %) | **31** | 0 |
| `widersprüchlich` | 4 (5 %) | **4** | 0 |
| `umgesetzt_ungeprüft` | 1 (1 %) | **1** | 0 |
| `blockiert` | 0 | **0** | 0 |
| `nicht_anwendbar` | 0 | **0** | 0 |
| `umgesetzt_geprüft` | **nicht in der Tabelle** | **0** | — |
| **Summe** | **80** | **80** | 0 |

- **Summe = 80 bestätigt.** 13+31+31+4+1 = 80.
- **Prozente korrekt:** 13/80=16,25 %→16; 31/80=38,75 %→39; 31/80→39; 4/80=5; 1/80=1,25 %→1. **Summe der gerundeten Anteile = 16+39+39+5+1 = 100 %** (die Doc-Tabelle nennt korrekt 100 %; die vom Auftrag genannten „99 %" treffen nicht zu — es sind 100 %). **Keine absolute Zahl fehlt** in der Tabelle (alle Kategorien mit Anzahl ausgewiesen).
- **Statuskatalog-Lücke (redaktionell):** die Vollständigkeitsbericht-Tabelle listet 7 der 8 Kategorien; **`umgesetzt_geprüft` (= 0) fehlt** in der Tabelle, obwohl `blockiert = 0` und `nicht_anwendbar = 0` aufgeführt sind. → **Korrektur: `umgesetzt_geprüft | 0 | —` ergänzen** (Vollständigkeit des 8-Kategorien-Katalogs).
- **ID-Eindeutigkeit:** `uniq -d` über alle 80 IDs = **leer** → jede ID genau einmal, keine Dubletten, keine verwaisten/fehlenden Nummern innerhalb der Gruppen.

**Gruppenzahl 18 vs. 19 (Nutzer-Markierung — bestätigt als Inkonsistenz):**
Distinkte ID-Präfixe = **19**: `ARCH BAT BLDG DATA DOOR FAC GRID HL HYD LIDAR OFFER PV ROOF SEC TEST UX WB WIN WP`. **WIN und DOOR sind zwei getrennte Präfixe.** Der Vollständigkeitsbericht und die Matrix-Zusammenfassung sprechen von „**18 Gruppen**" und führen „WIN/DOOR 3" als **einen** kombinierten Abschnitt (eine Tabellenüberschrift „WIN / DOOR"). Beide Lesarten sind intern konsistent, **aber die Bezeichnung ist mehrdeutig**.
→ **Korrektur (redaktionell): präzisieren zu „18 Tabellenabschnitte / 19 ID-Präfixe (WIN + DOOR getrennt)"**, damit die Zahl reproduzierbar ist.

**Matrix ↔ Modul-Matrix:** stichprobenartig gegengeprüft — jede Modulregel der Abhängigkeitsmatrix hat ein Matrix-Pendant (WP→Heizlast: HL-03/WP-01; PV→Dachgeometrie: PV-01/ROOF-01; Speicher→Last/Erzeugung: BAT-01; Geometrie-Kaskaden: DATA-15/BLDG-04). Keine verwaiste Modulregel.

---

## 4. Stichprobenprüfung der Codebelege (klassifiziert)

| Beleg (Doc-Behauptung) | Verifikation | Klasse |
|---|---|---|
| `EnergieAuslegungController::wpBerechnen`, `wp_index` required (`:199`) | `wpBerechnen` validiert `'wp_index' => ['required','integer','min:0']` (Datei Z.199) — produktzentrierter Einstieg | **bestätigt** |
| `KonfigurationsprojektService.php:83` `verdrahtet=$istWp` | Z.83 `'verdrahtet' => $istWp, // nur WP hat eine echte Auslegungs-Naht`; Non-WP-Zweig „Fachmodul noch nicht verdrahtet" | **bestätigt** |
| führendes Objekt `lead_alternative_adds`, Kette `new_leads→lead_alternative_adds→lead_product_lists→offers` | Kette in `LeadAlternativeAdd`, KonfigurationsprojektService liest `lead_product_lists` | **bestätigt** |
| Anforderungsprofil append-only, `gebaeude_geometrie` versioniert | `GrundrissController::schreibeGeometrieVersion`; `Anforderungsprofil` append-only (aus vorausgegangenen Wellen, Commits `f2d9a33`/`865e230`) | **bestätigt** |
| Geometrie-Tabellen `raum_geometrien`, `sanierungs_varianten`, `p_v_roof_plans` existieren | Migrationen `2026_07_08…raum_geometrien`, `2026_07_07…sanierungs_varianten`, `2024_08_26…p_v_roof_plans` | **bestätigt** |
| `RoofAreaEstimator` | `app/Services/RoofAreaEstimator.php` existiert (Service, **kein** persistenter Store) | **bestätigt (Einordnung s. §5)** |
| Doppel-Positionsstruktur `offer_product_lists` (2025-08) vs `offer_details` (2026-03) | beide Migrationen vorhanden | **bestätigt** |
| `product_heat_pump_specs.product_id` nullable, Auto-Anker blockiert | Migration Z.19 `unsignedBigInteger('product_id')->nullable()` (Kommentar nennt „FK", Deklaration ohne `->foreign()`-Constraint) | **teilweise bestätigt** (nullable ja; „ohne FK" plausibel, aber der Doc-Wortlaut „kein FK" sollte am tatsächlichen Constraint-Fehlen belegt werden, nicht am Kommentar) |
| fehlende Module Wallbox/Netz/Messkonzept/Speicher-Sizing | nur Stammdaten-Modelle (`ElectricVehicle`, `CustomerMeterCabinet`), keine Auslegungs-Services | **bestätigt** |

Kein Beleg **falsch interpretiert** außer der spec-FK-Nuance (teilweise) und dem Sicherheitsbefund (§7, **falsch** — separat).

---

## 5. Geometriequellen — „fünf Parallelwahrheiten"

| Nr | Quelle | Tabelle/Service | schreibt | liest | versioniert | produktiv genutzt | Einordnung |
|---|---|---|---|---|---|---|---|
| 1 | `anforderungsprofile.gebaeude_geometrie` | Profil-Spalte | ja (`GrundrissController::schreibeGeometrieVersion`) | Heizlast/Ableitung | **ja** (append-only) | ja | **Führend (behalten)** |
| 2 | `raum_geometrien` | Tabelle + `RaumGeometrie`-Model | ja (`GrundrissController`, `GeometrieAbleitungService`) | Heizlast | nein | teilweise (Heizlast-Pfad) | **schreibbarer Geometrie-Store** → migrieren/andocken |
| 3 | `p_v_roof_plans.roof_structures` | Tabelle + `PVRoofPlan` + `PVRoofPlanController` | ja | PV | nein | ja (PV-Insel) | **schreibbarer Geometrie-Store** → andocken |
| 4 | `sanierungs_varianten.massnahmen` | Tabelle (JSON `massnahmen`) | ja | Sanierung | nein | teilweise | **Maßnahmen-JSON, keine Geometrie** i.e.S. |
| 5 | `RoofAreaEstimator` | Service | **nein** (keine Persistenz) | OSM/Web-Mercator-Schätzung | n/a | KI-Chat | **Schätzer/Berechnung, kein Store** |

**Urteil:** Das **Grundrisiko ist bestätigt** — es existieren **≥2 unabhängig schreibbare fachliche Geometrie-Stores fürs selbe Objekt** (Nr. 1 `gebaeude_geometrie`, Nr. 3 `p_v_roof_plans`, dazu Nr. 2 `raum_geometrien`). Die Konsolidierungsforderung (DATA-11/BLDG-09/ROOF-02) ist berechtigt.
**Redaktioneller Vorbehalt:** die Formel „**fünf Parallelwahrheiten**" überzeichnet. Sauber sind es **drei schreibbare Geometrie-Stores** + **eine Maßnahmen-JSON (keine Geometrie)** + **ein nicht-persistenter Schätzer**. Die Docs annotieren dies zwar korrekt („legacy", „Insel", „Schätzer"), führen die fünf aber als gleichrangige „Wahrheiten". → **Empfehlung: umformulieren zu „3 schreibbare Geometrie-Stores + 2 abgeleitete/Schätz-Quellen".** Kein falscher Architektur-Befund, nur Präzisierung.

---

## 6. Doppel-Positionsstruktur `offer_product_lists` vs. `offer_details.sections`

- Beide Strukturen existieren (Migrationen 2025-08 flach vs. 2026-03 JSON). Konfigurator-Services referenzieren ausschließlich `offer_details.sections` (führend/Snapshot-Schiene); `offer_product_lists` ist die ältere flache Zeilenstruktur.
- Der Doc-Befund benennt korrekt: **Führung ist zu entscheiden, Alt-Struktur belegt stilllegen** (eigener Slice 7.3), **nicht in dieser Analyse gebaut**.
- **Urteil: teilweise redundant / Planner-Entscheidung offen** — kein aktiver Beleg, dass **dieselbe** Position in beiden Strukturen unabhängig schreibend gepflegt wird; die Doc-Einordnung „Doppelstruktur-Kandidat, Führung entscheiden" ist **angemessen** (nicht als bestätigte aktive Parallelwahrheit überzeichnet). Belastbar.

---

## 7. Sicherheitsbefund `/ids/callback` — 🔴 WIDERLEGT (kritischste Korrektur)

**Doc-Behauptung (in 5 Dokumenten):** „`/ids/callback` **öffentlich schreibend ohne Auth**, schreibt Produkte/Preise" (Gesamtarchitektur §11; Matrix SEC-03 = `widersprüchlich`; Vollständigkeitsbericht §5; Fahrplan 9.1 „hoch, vorziehen"; ADR-Kontext).

**Eigene Prüfung (read-only, keine Angriffsausführung):**

1. **Route:** `routes/web.php:497` `Route::post('/ids/callback', [IdsController::class, 'callback'])->name('ids.callback');` — steht **außerhalb** der `auth`-Gruppe. (Dies war offenbar die Grundlage der Doc-Annahme.)
2. **Controller-Konstruktor** (`IdsController.php:22-27`):
   ```php
   $this->middleware('auth');                                  // KEIN ->except(['callback'])
   $this->middleware('permission:Product,update')->only(['promoteToProduct']);
   ```
   `$this->middleware('auth')` **ohne `->except`** gilt für **alle** Methoden, inklusive `callback`.
3. **Wirkt das Konstruktor-Middleware in Laravel 11?** Ja, in **diesem** Projekt:
   - `composer.json`: `laravel/framework ^11.44.7` (installiert 11.48.0).
   - `App\Http\Controllers\Controller` **extends `Illuminate\Routing\Controller`** → besitzt `middleware()`/`getMiddleware()`.
   - `Illuminate\Routing\Route::controllerMiddleware()` (Z.1123-1126): `if (method_exists($controllerClass,'getMiddleware')) return $this->controllerDispatcher()->getMiddleware(...)` → Konstruktor-Middleware wird **weiterhin eingesammelt**.
4. **Autoritativer Gegenbeweis — Laravels eigene Auflösung:**
   ```
   $ php artisan route:list -v --path=ids/callback
     POST  ids/callback  ids.callback › …IdsController@callback
       ⇂ web
       ⇂ App\Http\Middleware\Authenticate
   ```
   Die Route läuft **mit `App\Http\Middleware\Authenticate`**. `Authenticate::redirectTo` → `route('login')` (Web) bzw. `null`/401 (JSON). Ein **unauthentifizierter** externer POST erreicht `autoPromoteItem()` (die `Product`/`Distributor`/`DistributorPrice`-Writes) **nicht**.

**Befund:** Die Kernaussage „öffentlich schreibend **ohne Auth**" ist **FALSCH**. Der Schreibpfad ist durch `auth` gated. Die Rubrik-Bedingung „nur bestätigen, wenn kein alternativer Schutz existiert" ist **nicht** erfüllt — hier existiert sogar der **stärkste** Schutz (volle Session-Auth), nicht nur eine Signatur.

**Zusätzlicher realer (gegenläufiger) Befund — als Hinweis, nicht als Bestätigung des Doc-Befunds:** Der Code-Kommentar „SERVER CALLBACK → must remain PUBLIC" und die Route-Platzierung außerhalb der `auth`-Gruppe zeigen die **Absicht**, den Endpunkt öffentlich zu halten (externer GC-Online-Server als Aufrufer). Das Konstruktor-`auth` (ohne `->except(['callback'])`) **konterkariert** diese Absicht: der externe Server kann sich nicht anmelden → der Callback ist für seinen vorgesehenen Zweck **vermutlich funktional gebrochen** (302/401 statt Import). Das ist ein **Funktions-/Konsistenzbefund**, kein „öffentliche Schreib-Lücke". Er gehört in die Matrix, aber **umgekehrt** klassifiziert.

**Korrektur-Auftrag an den Generator (Pflicht vor Grün):**
- SEC-03 von „`widersprüchlich` / öffentlich schreibend ohne Auth" ändern in: „**`/ids/callback` ist per Konstruktor-`auth` gated (route:list belegt `Authenticate`); Widerspruch zur `must remain PUBLIC`-Absicht → mögliche funktionale Störung des externen Callbacks; kein signatur-/secret-basierter Webhook-Schutz** (falls der Endpunkt bewusst geöffnet werden soll, fehlt HMAC/Secret/IP-Allowlist)." Status z. B. `besteht_teilweise` (auth vorhanden) mit Funktions-/Design-Risiko.
- Fahrplan 9.1 nicht mehr als „Public-Write-Sicherheitslücke sofort vorziehen" führen, sondern als „Callback-Auth-Widerspruch klären: entweder `->except(['callback'])` **plus** Signatur-/Secret-Absicherung, oder bewusst auth-pflichtig lassen".
- Gesamtarchitektur §11, Vollständigkeitsbericht §5, Risiko-Zeile entsprechend entschärfen.

**Schweregrad des Doc-Fehlers:** hoch, weil dieser Befund als „kritischster Punkt" und höchste Fahrplan-Priorität ausgewiesen ist — eine falsche P0-Sicherheitszuweisung würde Arbeitszeit fehlleiten und ein nicht existentes Leck vermelden.

---

## 8. Stale-Kaskaden

**Doc-Behauptung:** „kein Ergebnis-Stale-Trigger; `app/Observers`/`app/Listeners` leer" (DATA-15 = `fehlt`).

**Eigene Prüfung:**
- `app/Observers/` **existiert nicht** (kein Verzeichnis) → effektiv leer.
- `app/Listeners/` **ist nicht leer**: `LogUserLogin.php`, `LogUserLogout.php`, `StoreLeadActivity.php` — **keiner** invalidiert Auslegungsergebnisse.
- Einzige Stale-Mechanik: **Versionsketten-Stale** am Profil (`StaleProfilVersionException`, Commit `a00bb0a`). Keine kaskadierende Ergebnis-Invalidierung (Heizlast→WP, Dach→PV, Verbrauch→PV/Speicher, Fenster→Heizlast/Fassade, Nordwinkel→PV, Hausanschluss→Wallbox/Messkonzept, Produktauswahl→Angebot).

**Urteil:** Kernaussage **bestätigt** (kein Ergebnis-Stale-Trigger; die Read-only-Verbraucher lesen frisch, künftige `auslegung_ergebnis`-Persistenz braucht echten Trigger).
**Redaktionelle Korrektur:** „`app/Listeners` **leer**" ist ungenau — es liegen 3 sachfremde Listener vor. → präzisieren zu „**kein** Observer/Listener zur **Ergebnis-Invalidierung**; die vorhandenen Listener betreffen nur Login/Lead-Aktivität".

---

## 9. Fahrplanprüfung (`umsetzungsfahrplan.md`)

| Kriterium | Befund |
|---|---|
| Kleine, einzeln prüfbare Slices statt Großwelle | erfüllt — Phasen 0–10 in Einzel-Slices mit IDs |
| Startblock-Vertrag je Slice (IDs/Scope/Nicht-Scope/Datenwahrheit/Dateien/Migration/Rechte/Tests/Browser/Gegenbeweise/Manifest/Pflicht-Stopp) | als **Vertrag benannt** (Kopf) und je Slice IDs zugeordnet; die vollen Startblöcke folgen slice-weise (read-only Plan) |
| Migration von UI/Neuberechnung getrennt | erfüllt — Migrationen (1.1/1.2/0.3) als eigene Pflicht-Stopp-Slices, UI in Phase 8, Neuberechnung/Stale in 1.3 |
| Sicherheits-Slice vorhanden | vorhanden (9.1) — **aber auf falschem Befund**; nach §7-Korrektur als „Callback-Auth-Widerspruch" neu zu fassen (nicht streichen) |
| Geometrie-Konsolidierungs-Slice | vorhanden (Phase 2 + 3.1/3.2) |
| Stale-Mechanik-Slice | vorhanden (1.3, DATA-15/BLDG-04) |
| Auslegung→Angebot-Brücke | vorhanden (7.1 OFFER-02, 7.2) |
| geparkte WP-P1a-Arbeit berücksichtigt | vorhanden (Phase 0.1/0.2, IDs ARCH-07/TEST-01) |
| Pflicht-Stopps bei Migration/Sicherheit | erfüllt (0.3, 1.1, 1.2, 9.1) |

**Fahrplan-Urteil:** strukturell **belastbar**, keine unprüfbare Großwelle, keine Vermischung Migration+UI+Neuberechnung. **Einziger Mangel:** der Sicherheits-Slice 9.1 fußt auf dem widerlegten SEC-03 und ist umzuformulieren. Kein Abnahmeblocker aus fehlendem Slice.

---

## 10. Nicht-Ziele (§20)

Alle sechs Ausschlüsse sind in ADR-0001 „Nicht-Ziele" abgebildet und durchgängig respektiert: keine BIM-Autorensoftware, keine Tragwerks-/Genehmigungsplanung, keine ungeprüfte KI-/LiDAR-Planung (LiDAR nur Vorschlagsquelle mit Bestätigung, LIDAR-01..03), keine automatische Bestell-/Förderzusage, **keine erfundenen Preis-/Verfügbarkeitsdaten** (Operanden-Gate: `preis_status='katalog_anker_fehlt'`, kein Auto-Anker mangels FK), keine parallelen Kunden-/Objekt-/Gebäude-DBs (alles auf `lead_*`-Kern), kein Konfigurator außerhalb ticket. **Konsistent — keine Abweichung.**

---

## 11. Abweichungen & Blocker (Sammlung)

**Abnahmeblocker (Rot-Auslöser):**
- **B1 — SEC-03 Sicherheitsbefund falsch** (§7): `/ids/callback` ist auth-gated (route:list belegt `Authenticate`); „öffentlich schreibend ohne Auth" ist widerlegt. Betrifft 5 Dokumente + Fahrplan-Top-Priorität. **Korrektur zwingend.**

**Redaktionelle Korrekturen (keine Architektur-/Zahlenfehler im Kern):**
- **R1 — Gruppenzahl:** „18 Gruppen" → „18 Tabellenabschnitte / **19 ID-Präfixe** (WIN + DOOR getrennt)".
- **R2 — Statuskatalog:** `umgesetzt_geprüft (= 0)` in der Vollständigkeitsbericht-Tabelle **ergänzen** (8. Kategorie fehlt).
- **R3 — Geometrie-Formel:** „fünf Parallelwahrheiten" → „**3 schreibbare Geometrie-Stores + 2 abgeleitete/Schätz-Quellen**".
- **R4 — Stale-Wortlaut:** „`app/Listeners` leer" → „kein Observer/Listener zur **Ergebnis-Invalidierung**" (Listeners enthält 3 sachfremde Einträge).
- **R5 — Spec-FK:** „`product_id` nullable **ohne FK**" am tatsächlich fehlenden `->foreign()`-Constraint belegen (Migrationskommentar sagt „FK", Deklaration hat keinen) — Wortlaut schärfen.

**Bestätigt korrekt (keine Änderung nötig):** 80er-Summe, alle Statuszahlen (13/31/31/4/1), Prozente (100 %), ID-Eindeutigkeit, Matrix-Vollständigkeit gg. Masterentscheid, produktzentrierter WP-Einstieg, Objekt-/Angebotskette, Geometrie-Grundrisiko (≥2 schreibbare Stores), Doppel-Positionsstruktur, kein Ergebnis-Stale, fehlende Module WB/GRID/BAT/WIN/DOOR/ROOF/LiDAR, Nicht-Ziele, Fahrplan-Struktur, keine Produktivcode-/Migration-/Umsetzungsbehauptung.

---

## 12. Abschlussurteil

**🔴 ROT** — ausschließlich wegen **B1** (falscher Sicherheitsbefund SEC-03). Per Prüfrubrik ist ein „unbestätigter/falscher Sicherheitsbefund" ein Rot-Auslöser; hier ist der als kritischste P0 markierte Befund nachweislich widerlegt (`route:list -v` zeigt `Authenticate`). Alle übrigen Kernaussagen, Zahlen und die Matrix-Vollständigkeit sind belastbar; die Analyse ist ansonsten methodisch sauber, read-only, ohne Produktivcode und ohne Umsetzungsbehauptung.

**Hebung auf Grün:** nach Korrektur von B1 (SEC-03 umklassifizieren zu „auth-gated / Callback-Absichts-Widerspruch, kein Public-Write") und Übernahme der redaktionellen Korrekturen R1–R5 ist das Dokumentenpaket abnahmefähig (**Grün mit redaktionellen Korrekturen** wäre erreicht, sobald B1 als Zahlen-/Formulierungssache und nicht als Architektur-/Sicherheitsfehler behandelt wird — da B1 aber ein *falscher Sicherheitsbefund* ist, lautet das Urteil jetzt Rot).

**Ballbesitz:** **Generator** — B1 (Pflicht) und R1–R5 (redaktionell) in den sechs Docs einarbeiten, danach Rück-Übergabe an den Evaluator zur Nachkontrolle des korrigierten SEC-03. Kein Bau, keine Migration, kein Commit bis dahin.

---

> Unabhängige Prüfung der systemweiten Bestandsaufnahme abgeschlossen. Urteil: Rot. Keine Produktivcodeänderung, keine Migration, kein Commit und kein Push.

---

## 13. Nachkontrolle nach Generator-Korrektur (Runde 2)

**Stand:** 2026-07-14 · **read-only Nachmessen** (eigenes grep/awk/`route:list`, kein Vertrauen auf Zusagen) · **Git:** `a00bb0a` (unverändert; weiterhin kein Commit, kein Produktivcode im Diff — nur die sechs Docs + diese Datei untracked, `CLAUDE.md` als getrennter Governance-Diff).

Der Generator meldet Einarbeitung von B1 + R1–R5. Ergebnis meiner Nachkontrolle:

### B1 — SEC-03 umklassifiziert (Pflicht) — ✅ VOLLSTÄNDIG
- **Kein Public-Write-Leck mehr behauptet:** `grep -rniE "öffentlich schreibend|ohne auth|public.?write"` über die sechs Docs findet **keine** aktive Lücken-Behauptung mehr (Treffer nur in dieser Evaluations-Akte als Zitat des Alt-Befunds — zulässig/gewollt).
- **Matrix SEC-03 (Z.159):** Status `widersprüchlich` → **`besteht_teilweise`**; Ist-Stand nennt jetzt korrekt `IdsController.php:24` `$this->middleware('auth')` ohne `->except`, `route:list -v` → `Authenticate`, „**Kein** Public-Write-Leck", Nebenbefund = Callback-Auth-Widerspruch (Funktion/Design).
- **Gesamtarchitektur §11 (Z.64), Vollständigkeitsbericht §5 (Z.43) + §11-Reihenfolge (Z.74), Fahrplan 9.1 (Z.93):** alle konsistent auf „auth-gated, kein P0, Priorität niedrig-mittel" umgestellt; 9.1 aus der Sofort-Reihenfolge entfernt.
- **Statusverschiebung nachgemessen:** `awk … | uniq -c` über die Matrix ergibt jetzt **`besteht_belastbar` 13 · `besteht_teilweise` 32 · `fehlt` 31 · `widersprüchlich` 3 · `umgesetzt_ungeprüft` 1** = **80**. `widersprüchlich` sind exakt DATA-11/BLDG-09/ROOF-02 (SEC-03 nicht mehr dabei). Übereinstimmend mit dem Vollständigkeitsbericht.

### R1 — Gruppenzahl — ✅
Matrix-Summenzeile (Z.188) und Vollständigkeitsbericht §1 (Z.10) + §13 (Z.84) sagen jetzt „**18 Tabellenabschnitte / 19 ID-Präfixe** (WIN + DOOR getrennt)"; Aufschlüsselung „WIN 2 + DOOR 1". Eigene Nachrechnung der Gruppengrößen = 80.

### R2 — Statuskatalog & Zahlen — ✅ (nachgerechnet)
Vollständigkeitsbericht §1-Tabelle listet jetzt **alle 8 Kategorien** inkl. `umgesetzt_geprüft = 0` und `nicht_anwendbar = 0`. Kontrolle im Doc: „13+32+31+3+1+0+0+0 = 80". **Eigene Nachrechnung:**
- Summe: 13+32+31+3+1 = **80** ✓
- Prozente: 13/80=16,25→16 · 32/80=**40,0**→40 · 31/80=38,75→39 · 3/80=3,75→4 · 1/80=1,25→1 → **16+40+39+4+1 = 100 %** ✓ (sauberer als die Vorrunde). Narrative Prozente in §12 (Z.80) konsistent auf 16/40/39/4/1 aktualisiert.

### R3 — Geometrie-Formel — ✅
„fünf Parallelwahrheiten" ist in **allen** betroffenen Stellen ersetzt durch „**3 schreibbare Stores + 2 abgeleitete/Schätz-Quellen**": Matrix DATA-11 (Z.39), Modul-Matrix §3 (Z.43), Vollständigkeitsbericht §5 (Z.42) + §9 (Z.62), Gesamtarchitektur §11 (Z.64). Zuordnung korrekt (schreibbar = `gebaeude_geometrie`/`raum_geometrien`/`p_v_roof_plans`; abgeleitet = `sanierungs_varianten.massnahmen`/`RoofAreaEstimator`).

### R4 — Stale-Wortlaut — ✅
DATA-15 (Z.43) sagt jetzt „**kein** Observer/Listener zur **Ergebnis-Invalidierung** (`app/Observers` existiert nicht; die 3 vorhandenen `app/Listeners` betreffen nur Login/Lead-Aktivität)". Deckt sich mit meiner Messung (Observers-Dir fehlt; Listeners = LogUserLogin/LogUserLogout/StoreLeadActivity).

### R5 — Spec-FK — ✅
Modul-Matrix §3 (Z.45) und Fahrplan 6.2 (Z.68) formulieren jetzt „nullable, Deklaration **ohne** `->foreign()`-Constraint trotz „FK"-Kommentar" — deckungsgleich mit Migration `2026_07_04_150004…:19`.

### Konsistenz-Gegenprobe (neue Inkonsistenzen?)
- **Keine** Alt-Zahl mehr im Umlauf: `grep` nach „teilweise 31 / widersprüchlich 4 / 39 % teilweise / 5 %" findet nur die **erklärenden Übergangsnotizen** („4→3", „31→32") — korrekt als Änderungs-Historie, nicht als aktive Zahl.
- Matrix ↔ Vollständigkeitsbericht: Statuszahlen (13/32/31/3/1) identisch. ✓
- SEC-03 erscheint jetzt konsistent in der „teilweise"-Aufzählung (Vollständigkeitsbericht §3 „SEC-01/02/03/04"). ✓
- Modul-Matrix: 0 verbliebene `widersprüchlich`-Tokens (nutzt eigene Status-Skala) — kein Konflikt. ✓

### Aktualisiertes Urteil

**🟢 GRÜN (mit umgesetzten redaktionellen Korrekturen).** Der einzige Rot-Auslöser (B1, falscher Sicherheitsbefund SEC-03) ist behoben und durch einen belegten, korrekt eingeordneten Befund (auth-gated + Callback-Auth-Widerspruch, kein P0) ersetzt. R1–R5 sind vollständig, korrekt und widerspruchsfrei eingearbeitet; alle Zahlen (80; 13/32/31/3/1; 100 %) sind unabhängig nachgerechnet und zwischen Matrix und Vollständigkeitsbericht konsistent. Keine neue Inkonsistenz, kein Produktivcode, keine Migration, keine Umsetzungsbehauptung.

**Ballbesitz:** **Yama** — das Read-only-Dokumentenpaket (sechs Docs + diese Evaluationsakte) ist abnahmefähig. Nächstes Gate ist die Yama-Freigabe zum Commit der Docs; erst danach beginnt slice-weise Umsetzung (Startblöcke gemäß Fahrplan, jeder mit Pflicht-Stopp + Evaluator). Kein Commit, kein Push, kein Bau ohne Yama-Freigabe.

> Nachkontrolle nach Generator-Korrektur abgeschlossen. Urteil: Grün (redaktionelle Korrekturen B1 + R1–R5 umgesetzt und unabhängig nachgemessen). Keine Produktivcodeänderung, keine Migration, kein Commit und kein Push.
