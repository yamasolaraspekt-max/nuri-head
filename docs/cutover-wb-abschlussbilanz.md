# Cut-over wberechnung → ticket — Abschluss-Bilanz („Gewissheits-Audit")

**Stand:** 2026-07-05 · **read-only** · **Frage (wörtlich):** *„Sind alle notwendigen Codes und
Informationen von wberechnung ins ticket übernommen, sodass man nicht mehr in wberechnung schauen muss?"*

**Antwort in einem Satz:** *Nein — bewusst noch nicht.* Übernommen ist **das Heizkörper-Modul** (Rechenkern +
Katalog + UI, live-testgrün); der **gesamte übrige Rechenkern-Bestand** (Heizlast, WP-/PV-Auslegung, PVGIS,
Wirtschaftlichkeit, Klima, Energiekonzept) ist **entschieden, aber noch nicht portiert** — dafür bleibt wb
Referenz (Teil B). Diese Bilanz belegt jede Position live und weist nach (Teil D), dass **nichts unerfasst**
durchgefallen ist.

**Methodik:** Jede A-Zeile live gegengeprüft (Datei existiert · Test gelaufen · Commit-Hash · Marker gezählt).
Dokumentenstände zählen nicht als Beweis. Grundlagen: Datei-Manifest (301, unten Teil D), `cutover-wb-module.md`
(15 Module), `cutover-wb-inventur.md` (38 Tabellen), `wp-datenblatt-verifikation.md`, `STRAENGE.md`, Roadmap.

---

## Teil A — ÜBERNOMMEN (live verifiziert)

| Was | wb-Quelle | ticket-Ort | Beweis (live 2026-07-05) |
|---|---|---|---|
| **HK-Rechenkern** (EN-442 `qReal`, Hydraulik) | `Services/Heizlast/HeizkoerperService`, `HydraulikService` | `app/Services/Heizkoerper/RadiatorPerformanceService.php`, `HydraulicService.php` | **`6bf75b0`** — byte-genauer Port `wb@d81faa8`, **7 Paritäts-Tests**; `RadiatorPerformanceServiceTest` grün |
| **Kompatibilität** (D3-Regeln 1–6) | `HeizkoerperService::minVorlauf` u.a. | `app/Services/Heizkoerper/CompatibilityService.php` | **`947bed6`** — `CompatibilityServiceTest` **10 grün** (Regel 1–6 + Datenlage) |
| **Katalog-Adapter** (q_norm aus ticket-Katalog) | — (neu) | `RadiatorCatalogAdapter.php` | `RadiatorCatalogAdapterTest` grün (q_norm aus Länge×Anzahl) |
| **HK-Katalog** (30 Kermi EN-442) | `Models/Heizkoerper`, `heizkoerper` (30) | `database/seeders/RadiatorSpecSeeder.php` + `product_radiator_specs` | **`09eea5e`** — 30 Zeilen + `imported_from`-Marker (Seeder-Code; Zeilen flüchtig unter `RefreshDatabase`) |
| **HK-Schema** (Domäne) | `heizkoerper`-Migration | 9 Migrationen `2026_07_04_140001–140009` | **`5f2bcd9`** — auf **`ticket_testing`** (⚠️ nicht main → M5) |
| **HK-UI + Übernahme** (Konfigurator, Ampel, Stückliste) | `HeizkoerperCheckController` + Views | `app/Http/Controllers/Heizkoerper/HeizkoerperController.php` + `views/admin/heizkoerper/konfigurator.blade.php` | **`89e175f`** („Stufe v komplett", **committet**) — Endpoint- + Konfigurator-View-Test **grün** (Alpine/CSRF/663/404/422) |

**HK-Gesamtnachweis:** `php artisan test tests/{Unit,Feature}/Heizkoerper` → **26 grün / 83 Assertions**.

**Nicht-wb, aber Cut-over-relevant (ticket-eigener Katalog, live auf main):**
| Fox-ESS/LONGi-Geräte | — (ticket-Eigenrecherche) | `products` 26 · `inverters` 13 · `batteries` 2 · `product_pv_module_specs` 10 | **`46b1986`** — live gezählt auf main; 2 brands |
| WR/Bat/PV/WP-**Spec-Schema** (additiv) | wb-Spec-Struktur | 4 Spec-Migrationen `150001–150004` | **`217473f`** — auf main migriert |
| **wb-Katalog-Import** (19 WP `en14511_nenn` + AIKO 2 + LONGi LR7 3) — *B→A, Stufe 2* | wb `waermepumpen`/`artikel` (`b4a9eda`) | `products` + `product_heat_pump_specs` + `product_pv_module_specs` (**testing**) | `WberechnungImportSeeder` — **8 Tests grün** (Zeilen-Soll 19/5, Buderus-A-7-COP 2,36, NIBE-Varianten, Dedup-Skip, Idempotenz, Rückbau); Marker `imported_from='wberechnung'`; **main = M5-Deploy-Paket** |
| **B2a-1 Referenz-Kataloge** Heizlast + Klima — *B→A* | wb `b4a9eda` | `materials` (23) · `konstruktionen` (5) · `baualtersklassen` (25) · `klima_plz` (8168), **lokal** | `ReferenzKatalogSeeder` — **6 Tests grün**; `verifikations_status` je Zeile (din_belegt/tabula_richtwert, W-B2a-4); material_id-Remap wb→ticket; Migr. `170001–170004` |
| **B2a-2 Anforderungsprofil-Framework** (Bedarfs-Rahmen) — *neu-A (kein wb-Port)* | — (Zielbild Phase 1, bewusst KEIN Nachbau von wb-`heizlast_projekte`) | `anforderungsprofile` + `anforderungsprofil_werte` (`170005/06`, testing) | versioniert append-only · polymorph verankert (Whitelist Objekt `LeadAlternativeAdd`/Gewerk `LeadProductList`) · Datenlage je Wert + Schlüssel-Registry-Vertrag · **9 Tests grün** |
| **B2a-3 Heizlast-Kern** (Rechner+RaumHülle+Normwerte) + Adapter — *B→A* | wb `b4a9eda` **byte-genau (Diff=0)** | `app/Services/Heizlast/*` + `AnforderungsprofilHeizlastAdapter` + `170007` (Geometrie-Spalte), lokal | **8 Tests + Byte-Beweis** (H_T=17,26 · Φ_HL 892/948 W · auslegung 0,95 kW · spez 37,9); deckt **5/28 Anker** (HeizlastRechnerTest); UWert/Auslegung/WW = separate Adapter |
| **B2b-C Klima/WW-Kern** (KlimaPlz+Höhe+WW+KlimaBin+Konstanten+DTO) + Adapter — *B→A* | wb `b4a9eda` **byte-genau (Diff=0, 6 Klassen)** | `app/Services/Heizlast/*` + `AnforderungsprofilKlimaAdapter`, lokal (CSV; Tabelle=Tag-X) | **7 Tests · 3 harte Anker über das Profil**: `norm_aussentemp_c` **−8,5** °C (Dresden 01067) · θe-Höhe **−13,5** °C (611 m, Δ500) · `phi_ww_kw` **0,80** kW (4 P.×0,20). **Zähler 8/28** (5 B2a-3 + 3 C). **schließt B2a-3-Gate**; CSV==Tabelle (C1.1). Rest der 10: KlimaBin (zone/θe −14/bins) + KlimaPlz-Koordinaten → **B** (Bivalenz nutzt KlimaBin, byte-portiert bereitgestellt) |
| **B2b-A UWert-Kern** (UWertService + 4 Models + KonstruktionTyp-Enum) + Adapter — *B→A* | wb `b4a9eda` **byte-genau (Diff=0, 6 Dateien)** | `UWertService` + `app/Models/{Baualtersklasse,Material,Konstruktion,FensterSpec}` + `app/Enums/KonstruktionTyp` + `AnforderungsprofilUWertAdapter` (nutzt B2a-1-Tabellen; **kein** fenster_specs nötig — `fenster()` = Konstante; **FensterSpec = tote Referenz ohne Tabelle, Tag-X-frei** = keine Migration ausstehend) | **8 Tests · 6 harte Anker**: ausSchichten Ziegel **1,45**/gedämmt **0,28** · fenster **0,8**/einfachglas **5,0** · ausBaualter(2020,fenster) **1,10** · Adapter baujahr→u_wert (`tabula_richtwert`) + Guard-Test. **Zähler 14/28** |
| **Anker-Arithmetik (28)** | — | — | **5 (B2a-3: H_T 17,26·H_V 10,63·Φ_HL 892/948·Wand-netto 24) + 3 (C: nat_c −8,5·θe-Höhe −13,5·phi_ww 0,80) + 6 (A: Ziegel 1,45·gedämmt 0,28·fenster 0,8·einfachglas 5,0·ausBaualter 1,10·Adapter) = 14**. **14 offen → B2b-B:** 7 Auslegung (Φ_HL 7,8–12,4·JAZ 2,9·w_el 6069·Sperrzeit-f 1,09 +3) · 7 Klima-Rest (KlimaBin zone/θe −14/bins/saison/profil + KlimaPlz-Koordinaten) |

> **Diskrepanz-Befund (Doku ↔ Realität):** `product_heat_pump_specs` existiert auf main, ist aber **0 Zeilen**
> (WP-Import = Teil B). `product_radiator_specs`/`radiator_connection_factors` **fehlen auf main** (nur testing) →
> HK-Schema ist Teil A, aber **produktiv erst mit M5**. „✅ (testing)"-Notizen sind daher als *Schema+Seeder+grüner
> Test* zu lesen, nicht als persistente Produktivzeilen.

---

## Teil B — ENTSCHIEDEN, NOCH NICHT DA (dafür bleibt wb Referenz)

Live belegt: **kein** dieser Rechenkerne ist in ticket (`find app -iname …` → FEHLT für Heizlast, Waermepumpe,
WpKennlinie, Bivalenz, Pvgis, Sanierungs, Fussboden, Energiekonzept, PvProjekt, Klima).

| Was (wb-Modul) | Roadmap-Slot | Gate/Blocker | wb bleibt Referenz für |
|---|---|---|---|
| **Heizlast-Kern** DIN EN 12831 — *Rechner B→A (B2a-3)* | **✓ Rechner portiert** | UWert/Auslegung/WW = eigene Adapter (offen) | `HeizlastRechner`+`RaumHuelleService`+`HeizlastNormwerte` **portiert Diff=0**; `UWertService`/`AuslegungService`/`WarmwasserService` warten (23/28 Anker) |
| **Klimadaten** — *KlimaPlz/Bin/Höhe B→A (B2b-C)* | **✓ portiert (byte-genau)** | `OpenMeteo` bleibt (online) | `KlimaPlzService`/`KlimaBinService`/`HoehenkorrekturService` portiert; `OpenMeteoKlimaService` = bewusst NICHT (online, W-B2a-3 offline) |
| **WP-Auslegung** Kennlinie/Bivalenz/JAZ (F+G) | **B2a** | nach B1 (Katalog) | `WpKennlinieService` (jetzt datenblatt-sauber, `b4a9eda`), `BivalenzService`, `JazService` |
| **PV-/WR-Sizing** (I) | **B2a** | nach B1 | `PvProjektService`, `InverterSizingService`, `StringBuilderService`, `Contracts/*` |
| **PVGIS-Ertrag** (J) | **B2/B3** | löst ticket-`PVToolsController`-Stub ab | `PvgisController`, `GeocodingService` |
| **Wirtschaftlichkeit** (H/L) | **B3/B4** | ✅ EconomicCalc-Sperre **aufgelöst** (`df1dc8c`); Rechenkern-Port noch offen | `SanierungsWirtschaftlichkeitService`, `SanierungController` |
| **Förderung** BAFA/KfW (O) | **B3/B4** | integriert in H | `FoerderungService` |
| **Fußbodenheizung** EN 1264 (M) | **B3-Unterpunkt** | erste Streichposition | `FussbodenheizungService` |
| **Energiekonzept-Bundler** (Q) | **B4** | neue Struktur ≠ ticket-`EconomicCalculation` | `EnergiekonzeptController`, `Models/Energiekonzept` (+ 6 Faker-Datensätze, archiviert `1085c43`) |
| **batterie_wr_kompatibilitaet** (26) — *Rest von W-C4* | **PV/WR-Sizing-Slot** (nicht Heizlast) | n:m `artikel↔artikel` → `products↔products` | `batterie_wr_kompatibilitaet` |
*(Die 3 Heizlast-Referenz-Kataloge `materials`/`konstruktionen`/`baualtersklassen` + `klima_plz` sind mit **B2a-1 nach Teil A gewandert** — siehe dort.)*
| **Grundriss/Plan-Import** A-3d (K) | **B5/W6** | eigene Welle; Queue/Storage | `GrundrissController`, `PlanUploadController`, 4 Jobs, `MassstabVorschlagService` |
| **HK produktiv** (M5) | **M5** | Prod-Migration-Fenster (~3000 Kunden) | HK-Schema von testing → main |

**Diese 12 Zeilen SIND die ehrliche Antwort auf „wofür muss ich noch in wb schauen".** *(WP-Katalog-Import ist mit Stufe 2 nach Teil A gewandert — testing; main = M5-Deploy-Paket.)*

---

## Teil C — BEWUSST VERZICHTET

| Was | Entscheids-Referenz |
|---|---|
| Fox-ESS/LONGi-**WR/Bat-Duplikate** aus wb | Netto-Neuwert (W-C3): ticket ist datenblatt-verifiziert, gewinnt |
| **Heizkörper-Katalog-Duplikat** (wb `heizkoerper` 30) | `RadiatorSpecSeeder` hat identische Kermi-Zeilen (Parallelstrang) — entfällt |
| **Fenster-Dummies** (`fenster_specs` 3) | Prototyp; echte Fenster-Referenz erst mit B2a |
| **wb-User** (`users` 1) | ticket-Auth gilt (Modul: `User`, `Auth/LoginController`, `LoginRequest`) |
| **wb-Procurement** (Modul N) | **A4** entschieden: ticket-Supplier-Stack (OMD-Strang). Betrifft `Services/Procurement/*` (9), Models `Lieferant`/`Bestellung`/`Lagerbestand`/… (8), Migrationen (7) |
| **wb-Framework/Infra** | Laravel-Standard: `config/` (10), `Providers`, `Console`, Wurzel-Configs, Framework-Migrationen (users/cache/jobs) — ticket hat eigene |

---

## Teil D — UNERFASST (das Gewissheits-Kriterium · ZIEL: LEER)

Das Datei-Manifest (301 Quelldateien, ohne vendor/node_modules) — **jede Gruppe** einer Kategorie zugeordnet.
A = übernommen · B = entschieden-offen · C = verzichtet · **Infra** = Framework/Config/Docs (Unterfall C).

| Verzeichnis-Gruppe | Dateien | A | B | C | Infra | Anker |
|---|---:|---:|---:|---:|---:|---|
| `Services/Heizlast` | 26 | 2 | 24 | – | – | A: HeizkoerperService+HydraulikService (Port-Quelle) |
| `Services/Energie` | 8 | – | 8 | – | – | PV/WR-Sizing (B2a) |
| `Services/Procurement` | 9 | – | – | 9 | – | A4-Verzicht |
| `Services/Import` + `Services/` (Root) | 6 | – | 6 | – | – | A-3d/B5 + PV-Sizing |
| `Models` | 30 | 1 | 21 | 8 | – | A: Heizkoerper · C: 8 Procurement/Auth |
| `Http/Controllers` | 14 | 1 | 12 | 1 | – | A: HeizkoerperCheck · C: Auth |
| `Jobs` · `Policies` · `Enums` · `Support` | 8 | – | 8 | – | – | A-3d/B5 + Kern-Support |
| `Http/Requests` · `Console` · `Providers` | 6 | – | 3 | – | 3 | B: Energiekonzept/Backfill · Infra: Auth/Provider/CreateUser |
| `config/` | 10 | – | – | – | 10 | Laravel-Standard |
| `database/migrations` | 44 | 3 | 31 | 7 | 3 | A: HK-Schema · C: Procurement · Infra: users/cache/jobs |
| `database/seeders` | 10 | 1 | 8 | – | 1 | A: Heizkoerper · Infra: DatabaseSeeder |
| `database/factories` | 6 | – | 5 | – | 1 | Infra: UserFactory |
| `database/data` | 4 | – | 4 | – | – | waermepumpen.csv (A-nah, verifiziert) + klima/material |
| `resources/views` | 31 | – | 26 | – | 5 | B: Fach-Views (B3) · Infra: welcome/dashboard/layouts/login |
| `resources/js` + `css` | 5 | – | 2 | – | 3 | B: grundriss snap/freihand (B5) |
| `routes` | 2 | – | 1 | – | 1 | B: web.php · Infra: console.php |
| `tests` | 56 | 3 | 50 | – | 3 | A: HK-Tests · Infra: Example×2/TestCase |
| Wurzel (Docs/Config/Dotfiles) | 23 | – | – | – | 23 | README/CLAUDE/composer/… |
| **Summe** | **301** | **11** | **204** | **25** | **61** | **A+B+C+Infra = 301** |

**Teil D = LEER.** Keine der 301 Dateien bleibt ohne Kategorie → nichts ist in Stufe 0/1 durchgefallen.
(11 A + 204 B + 86 C/Infra = 301. Die 204 B-Dateien sind der Umfang, der „wb noch Referenz" begründet.)

---

## Teil E — AMPEL-FAZIT

🟡 **16 von 215 fachlich wertvollen Positionen übernommen** (Heizkörper-Modul `6bf75b0`/`947bed6`/`09eea5e`/`89e175f`
**+ wb-Katalog-Import** 19 WP + AIKO/LONGi **+ B2a-1 Referenz-Kataloge** `materials`/`konstruktionen`/`baualtersklassen`/`klima_plz` **+ B2a-3 Heizlast-Kern** + **B2b-C Klima/WW-Kern** + **B2b-A UWert-Kern** (byte-genau), lokal) · **199 entschieden-offen** (B1→B5/M5) ·

**Basis-Klärung (202→215):** „202" war die Zahl der *offenen* B-Posten, nicht die Basis — die Gesamt-Basis ist **215** (A+B-Klasse des Gewissheits-Audits: 301/301 klassifiziert − 86 C/Infra). Die **14 übernommenen A-Posten** (215 − 201) sind je in Teil A per Commit-Hash + Manifest-Zeile identifiziert (Heizkörper-Modul `6bf75b0`/`947bed6`/`09eea5e`/`89e175f` · wb-Katalog-Import `481b9cb` · Spec-Schema `217473f` · B2a-1 Referenz-Kataloge `9b48521` · B2a-3 Heizlast-Kern); B2a-2 zählt als *neu-A* (Framework, kein wb-Port) nicht in die Übernahme-Quote.
**Teil D leer** (301/301 klassifiziert) → **es ist nichts unbemerkt liegengeblieben, aber wb ist noch NICHT
abschaltbar.** Für Heizkörper + Geräte-Katalog gilt ticket; für alles andere (Heizlast, WP-/PV-Auslegung, PVGIS,
Wirtschaftlichkeit, Klima, Energiekonzept, Grundriss) bleibt **wb die Wahrheit**, bis der jeweilige B-Slot portiert ist.

**Einfrier-Vermerk (W-C2):** Mit Stufe 2 ist der **Geräte-Katalog wb komplett eingefroren** — WP war es schon,
jetzt auch die WR/Bat/Modul-Zeilen (Fox-ESS/LONGi bereits in ticket, AIKO/LONGi-LR7 importiert). Geräte-Änderungen
laufen ab jetzt in ticket; die wb-Geräte-Daten sind nur noch Herkunftsnachweis. (K/Grundriss bleibt bis B5 wb-benutzbar.)

**Fortschreibungs-Regel:** Bei jedem B-Slot-Abschluss wandert die Zeile **B → A** (Beweis-Zeile ergänzen, selber Commit).
**Ist Teil B leer, ist wb abschaltbar** — frühestens nach **M5** (HK produktiv) **und B5** (Grundriss).
Nächster Schritt Richtung A: **B2a-Kern-Ports** (Heizlast + Klima), sobald freigegeben. WP-Import-**main-Lauf = M5-Deploy-Paket** (mit HK-Migration; ein Fenster, ein Backup, ein Rollback-Plan — Roadmap §6).
