# Stränge & Zuständigkeiten (wberechnung → ticket)

## ⛔ DAUERDIREKTIVE: LOKAL-FIRST-ENTWICKLUNG (ab 2026-07-05, bis Yama „Tag X" ausruft)
**Gilt für ALLE Stränge und Sessions.**
- **Umgebung:** Yamas Rechner ist die **einzige** Arbeitsumgebung (lokale DB mit Seeder-Beispieldaten, lokale `.env`/Flags). **„main" / „produktiv fahren" = Yamas lokaler Hauptstand.** Migrationen/Seeder/Flags laufen **lokal, ohne Randzeit/Wartungsfenster/Deploy-Vorsicht** (lokales `mysqldump` vor destruktiven Läufen bleibt guter Ton, ist aber billig).
- **Produktions-Server = ABSOLUT TABU** für jede Instanz aus jedem Anlass bis Tag X: kein SSH, kein Remote-DB-Zugriff, kein Deploy, keine Produktions-`.env`.
- **Entfällt:** Deploy-Termine, Randzeit-/Wartungsfenster, Produktions-Backup-Zeremonie; **Echt-Nutzer-Beobachtungsfenster** (z. B. 14-Tage-Deny-Zähler) → wandern als Manifest-Posten in die Zeit **nach Tag X** (weiche Denies bleiben lokal aktiv). Nur wegen Deploy-Risiko getrennte Stufen dürfen zusammengelegt werden; **fachliche** Stufen (Befund→Bau) bleiben.
- **Unverändert (Qualität ≠ Produktionsschutz):** Pflicht-Stopps, Befund-vor-Bau, Weichen, additive Commits, Tests-grün-vor-Commit, Paritäts-Prinzip, ehrliche Datenlage, Rate-nicht-Regel, Strang-Scopes/Sperr-Dateien, Ein-Schreiber-pro-Datei, Migrationen nie umbenennen. Lokale Fehler sind billig, **nicht egal**.
- **RELEASE-MANIFEST-PFLICHT:** `docs/deploy/RELEASE-MANIFEST.md` ist die fortlaufende Tag-X-Liste. **Jeder** Commit mit prod-pflichtigem Teil (Migration · Seeder-Lauf · `.env`/Flag · Frontend-Fix mit Nutzerwirkung · Härtungs-Schalter · Config) trägt **im selben Commit** seine Manifest-Zeile nach (Posten · Commit · Reihenfolge/Abhängigkeit · Rollback · Nachläufe). **Posten ohne Zeile = Governance-Verstoß (gleichrangig Tabu-Bruch).**

## ⛔ DAUERDIREKTIVE: DATEN- UND KETTEN-SCHUTZ (ab 2026-07-05, strang-übergreifend)
**Gilt für ALLE playground→ticket-Schritte (Migration · Seeder · Import · Cut-over · FiBu). Volltext auch in `CLAUDE.md`.**
1. **Ticket-Daten unantastbar:** kein Transplantat ändert/löscht Bestands-Zeilen. Nur additiv: neue Tabellen · neue Spalten (nullable/Default) · neue Zeilen. **Jeder UPDATE/DELETE auf Bestand = eigener, explizit beauftragter Posten, nie Beifang.**
2. **Belegkette gesetzt — FiBu dockt an, baut nicht um:** Angebot(Sets→Artikel)→Auftrag→Rechnung(`invoices`, führend)·Bestelllisten. FiBu hängt **nur an die festgeschriebene Rechnung** (Buchungssatz); Angebot/Set/Artikel/Auftrag/Bestellliste werden **nicht verändert/dupliziert/ersetzt**.
3. **Konflikt:** playground-Code passt sich dem ticket-Schema an (Adapter bevorzugt, sonst additive Spalte) — nie umgekehrt. Prüfen: Teilrechnungen · Positions-Erlöskonten-Split · Leistungszeitraum-Herkunft.

Verbindliche Strang-Trennung, damit **kein Rechenkern doppelt gebaut** wird und Sperr-Dateien respektiert
werden. **Begriffsregel: Stufen IMMER mit Strang-Präfix benennen** — `Katalog-ii`, `Heizkörper-iii`, `M3`, `B1`
— **nie nacktes „Stufe (ii)"**. Stand: 2026-07-05.

**ORT-Regel (neu):** Jeder Strang trägt eine **Ort-Zeile** (Repo/Pfad · Branch · letzter bekannter Commit),
damit „gebaut vs. nicht gebaut / wo committet" nie wieder verwechselt wird — Lehre aus zwei Fehlannahmen:
`WberechnungImportSeeder` (als „gebaut" angenommen, fehlte) und **G-3b** (Feature auf keinem Branch dieses Repos, belegt durch `964a378`).

## 🚦 Parallelbetrieb-Karte (aktive Schreiber — Stand 2026-07-05)

**Regeln:**
- **Ein Schreiber pro Datei.** `sidebar.blade.php` gehört **aktuell Strang A** (M4-b Nav-Eintrag).
- **Migrations-Timestamps koordinieren sich über diese Datei** (nächster freier Slot ablesen, hier eintragen).
- **Einmal committete Migrationen werden NIE umbenannt** (Timestamp bleibt, auch wenn ein anderer Strang später einen früheren wählt).

### ⛔ ARBEITSBAUM-TRENNUNG + COMMIT-HYGIENE (ab 2026-07-05, verbindlich — Lehre aus dem Absorptions-Vorfall)
- **Ein Arbeitsbaum pro Instanz/Strang:** `git worktree add ../ticket-strang-<X> -b strang/<X>`. **Kein gemeinsamer Index mehr** — Absorption fremder ungestagter Änderungen wird technisch unmöglich (nicht nur verboten).
- **`git add -A` und `git commit -a` sind VERBOTEN.** Immer **explizite Pfade** stagen.
- **Ein Commit enthält ausschließlich Dateien des eigenen Strangs.**
- **Vor jedem Commit:** `git status` muss **leer sein bis auf die eigenen, im Stopp-2 berichteten Dateien** — sonst **STOPP + melden** (nicht mitcommitten).
- **Geteilte Datei `RELEASE-MANIFEST.md`** = einziger erwartbarer Berührungspunkt der Stränge: Manifest-Zeilen **nur im eigenen Commit** ergänzen, **nie fremde Zeilen anfassen**; Merge-Konflikte dort sind normal und werden **additiv** aufgelöst.

> **Provenienz-Vermerk (einmalig, KEIN History-Rewrite):** Beim `deal_invoices`-Rückbau (Accounting) hat der geteilte Arbeitsbaum meine ungestagten Änderungen in Fremd-Commits gezogen: **`f51dd50` (fox-ess)** enthält die Rückbau-**Datei-Löschungen**, **`d8d3870` (b2a-3)** die Rückbau-**Manifest-Zeile** — beide **fachlich zugehörig zu `b0735e3`** (Accounting-Rückbau-Commit). **Kein rebase/amend** auf dem gepushten Stand; hier nur dokumentiert.

| Strang | Ort (Repo · Branch) | SCHREIB-Scope (exakt) | Status | letzter Commit |
|---|---|---|---|---|
| **A — Heizkörper M4-b** | ticket · `private/app-code-backup` | **NUR** `resources/views/admin/layouts/sidebar.blade.php` + `docs/heizkoerper-bauplan.md` | aktiv | `89e175f` |
| **B — Spec-Standard B2–4** | ticket · `private/app-code-backup` | `app/Services/Spec/*` · `app/Console/Commands/Spec*` · `database/migrations/2026_07_05_15000[7+]*` · `tests/{Feature,Unit}/Spec*` | aktiv (B1+B2 committet, B3–4 offen) | `9501376` |
| **C — B2a-Bau (1/2/3)** | ticket · `private/app-code-backup` | `app/Services/Heizlast/*` · `app/Services/Anforderungsprofil/*` · `app/Models/Anforderungsprofil*` · `database/migrations/2026_07_05_170001–170007` · `database/seeders/{ReferenzKatalog,Klima}*` · `tests/{Feature,Unit}/{Heizlast,Anforderungsprofil}*` · `docs/befund-b2a-*` | **aktiv** | B2a-3 Heizlast-Adapter |
| **D — NAV 2b/3b** | ticket · `private/app-code-backup` | `sidebar.blade.php` + NAV-01-Doku — **GEPARKT bis A `sidebar.blade.php` committet hat** (Konflikt mit A) | ⏸ geparkt | `df1dc8c` |
| **E — Accounting/FiBu** | ticket · Worktree **`strang/accounting`** (Bau); Doku auf shared | **Invoice-/FiBu-Zone:** `docs/accounting/*` · `docs/umsatzdefinition.md` · (Bau später) `app/**/{Invoice,Accounting}*`, `app/Models/{Invoice,Accounting}*`, FiBu-Migrationen. **DealController nur Invoice-Teile.** Read-only Befund läuft. | **aktiv** (Schritt 0/1 + Phase 0) | `b0735e3` |

> Belegte Migrations-Timestamps (nie umbenennen): HK `2026_07_04_140001–140009`; Katalog/Spec
> `2026_07_04_150001–150004` (main), `2026_07_05_150005–150009` (testing → M5), S-3/SEC-DM `160000`.
> **Reserviert (Bau läuft):** Spec-M-C `2026_07_05_150010` (**Strang B**) · **B2a-Referenz+Klima `2026_07_05_170001–170004`** (**Strang C** — materials/konstruktionen/baualtersklassen/klima_plz). Kein Überlappungsbereich.

## Strang HEIZKÖRPER — parallele Instanz (NICHT anfassen)
- **Ort:** `ticket` = `/Users/yamanuri/Documents/ticket` · Branch `private/app-code-backup` · zuletzt `89e175f`.
- Roadmap **M0–M5** (Strang A): Fundament → Ventiltechnik → IDS-Mapper → **Rechenkern-Port (M3)** → **UI/Nav (M4)** → Prod-Migration (M5).
- Enthält: Rechenkern-Port (`app/Services/Heizkoerper/*`), **Mapper-Kette** (`SupplierArticleMapper`/`IdsMapper`/OMD-Datanorm-Stubs).
- Commits: `5f2bcd9`(i)·`80598a9`(ii-a)·`09eea5e`(ii-b)·`22e335d`(ii-c)·`1503854`(iii-a)·`f13f277`(iii-b)·`6bf75b0`/`947bed6`(iv)·`dd10a0e`(iv grün)·`af8c465`/`89e175f`(M4-a v). **M4-a committet** (Stufe v komplett: Stückliste + Übernahme-Endpunkt), **26 HK-Tests grün** (Abschluss-Bilanz Teil A). *[Korrektur 2026-07-05: früher „M4 uncommittet" — jetzt committet, live belegt.]* Prod-Migration (M5) offen: `product_radiator_specs` nur auf `ticket_testing`, nicht main.

## Strang KATALOG-CUT-OVER — DIESE Instanz
- **Ort:** `ticket` · `private/app-code-backup` · Katalog-(i) `217473f`; Fox-ESS/LONGi `46b1986`; Cut-over-Analyse `8287add`; Energiekonzept-Archiv `1085c43`; **Abschluss-Bilanz** `cutover-wb-abschlussbilanz.md`.
- `katalog-reconciliation-plan.md` Stufen **(i)–(iv)**: Schema additiv → Import → Adapter → Rechenkern.
- **Katalog-(i)** = `217473f` (4 Spec-Migrationen `150001–150004`, auf main migriert 2026-07-04).
- **Katalog-(ii) = Import-Seeder `WberechnungImportSeeder`** — **✅ GEBAUT + ABGENOMMEN** (`481b9cb`, 19/19, 8 Tests grün, nur `ticket_testing`). Scope: Netto-Neuwert (19 WP + AIKO + LONGi LR7), `imported_from='wberechnung'`, skip-Dedup. **M5-Posten B = regulärer Deploy-Lauf** im M5-Fenster (Smoke-Zählungen 19/5/24 = Kern-Smoke). *[Korrektur 2026-07-05: früher „nicht gebaut" — jetzt belegt gebaut.]*
- Cut-over-Analyse (Stufe 0+1) + **Abschluss-Bilanz** (Gewissheits-Audit: **301/301 klassifiziert, Teil D leer**, 🟡 11 A / 204 B): `docs/cutover-wb-module.md`, `cutover-wb-inventur.md`, `cutover-wb-abschlussbilanz.md`.
- **Fortschreibungs-Regel (verbindlich):** Bei jedem B-Slot-Abschluss wandert die Bilanz-Zeile **B→A** — im selben Commit wie der Slot.

## Strang WBERECHNUNG-APP / A-3d-Grundriss — /Herd/wberechnung (NICHT anfassen, read-only)
- **Ort:** `wberechnung` = `/Users/yamanuri/Herd/wberechnung` · Branch `main` · zuletzt `b4a9eda` (**WP-Daten-Fix**: Buderus EN-14511-Nenn, dichte LKs → Spalten-Fallback, `kurve_semantik`; **306 Tests grün**) · davor `eb11426`; A-3d: `3fef0dc`(OCR)·`fa837ef`(plan_uploads-Security).
- A-3d Raster/OCR, `MassstabVorschlagService`, `PlanBildVermessen` (Grundriss). Roadmap **B5**. Quell-App **read-only** — der WP-Daten-Fix ist **erledigt** (`b4a9eda`), das Schreibfenster ist **wieder zu** (strikt read-only, auch `waermepumpen*`).

## Strang G (CRM-Konversion & Belege) — eigene Instanz (NICHT anfassen)
- **Ort: UNBEKANNT / NICHT `private/app-code-backup`** — belegt durch `964a378` (kein Konversions-Feature in irgendeinem Branch dieses Repos). **Yama klärt den Arbeitsbereich der G-Instanz und trägt ihn nach.**
- **G-1…G-9**: CRM-Anfrage-Konversion, Rechnungs-/Beleg-PDF, **Schiene A** (PDF), **GAEB**, **Schiene B** (XRechnung). RBAC `crm.*`, Route-Prefix `/app`.
- **Tabu für andere Stränge:** Accounting, `finanz_safety`, Migrationen, Lohn/HR.
- Merke: `plan_uploads`/A-3d gehört **nicht** hierher (das ist der wberechnung-Grundriss-Strang, Modul K).

## Strang NAV (Navigation) — parallele Instanz (NICHT anfassen)
- **Ort:** `ticket` · `private/app-code-backup` · zuletzt `df1dc8c` (Energie-Konsolidierung: Sidebar + EconomicCalculation, **entsperrt NAV 2b/3b, M4-b, IA-2, B3**); davor `fa29f89`/`ca649b5`/`5680a71`.
- ✅ `sidebar.blade.php` + `EconomicCalculationController.php` **committet** (`df1dc8c`) — uncommittet-**Sperre aufgelöst**. `sidebar.blade.php`-Schreibrecht jetzt über die Parallelbetrieb-Karte (aktuell Strang A / M4-b).

## Strang OMD (Supplier-Connector) — DIESE Instanz
- **Ort:** `ticket` · `private/app-code-backup` · OMD Phase 1 `35a2904`. Mapper-Kette (`1503854`/`f13f277`) gehört zum Heizkörper-/Import-Strang.
- Phasen-gated; beim Katalog-Import nicht berühren (Tabu-Zone unten).

## Strang S1 (Rechnungsschiene-Härtung, Kanzlei-Übergabe) — eigene Instanz (NICHT anfassen)
> ⚠️ **„S1" = ausschließlich die Rechnungsschiene** (Invoice). **NICHT** verwechseln mit **SEC-DM** (Deal-Measurement-Security, unten) — die Commits `67a78a0`/`ab31863`/`15c0d55`/`63a7369` gehören zu **SEC-DM**, nicht zu S1.
- **Ort:** `ticket` (führend) · Branch/Commits **noch nicht belegt** (Planungsstand 2026-07-02) — *bei Umsetzung Ort-Zeile nachtragen (git-Beleg wie bei den anderen Strängen).*
- Basis: **Invoice-Modul** (`app/Models/Invoice*`, `Http/Controllers/Invoice/*`, Routes `/invoices` + `/invoices/canvas`). Planungsdok `docs/uebernahme/sprint-1-tickets-rechnungsschiene.md`.
- **S1-01…S1-11**: Nummernkreis → Löschsperre → Editiersperre-ab-sent → Storno/Gutschrift → Teilzahlung (`invoice_payments`) → payment_status → … → Legacy-Cleanup (S1-10) → Regressionssuite (S1-11). **A1 = Option 1** (Kanzlei führt FiBu; **keine** Buchhaltung im ticket).
- **Abgrenzung:** S1 = bestehende **Rechnungs-Schiene** (`/invoices`) härten. Strang **G** = CRM-Konversion + **Beleg-PDF/GAEB/XRechnung** (`/app`, `crm.*`). „Belege"-Überschneidung mit Yama klären (evtl. G ⊃ S1 oder getrennt).
- **Tabu für Cut-over/Katalog/Heizkörper:** Invoice-/Accounting-Dateien, `finanz_safety`, Rechnungs-Migrationen.

## Strang SEC-DM (Deal-Measurement-Security) — eigene Instanz (NICHT anfassen)
- **Ort:** `ticket` · `private/app-code-backup` · zuletzt `63a7369` (Preis-nullable); `67a78a0`(a)·`ab31863`(b-1)·`15c0d55`(b-2).
- **Scope:** `DealMeasurementPolicy`, Item-/Material-Schreibpfade, `DealMeasurement*Controller`, `DealMaterialListController`, `deal_measurement_*`. Doku: `docs/sicherheits-backlog.md`.
- **Umbenannt 2026-07-05** von „S-1/S-2" → **SEC-DM** (behebt Namens-Kollision mit dem **S1-Rechnungsschiene**-Strang). Offen: Umschalt auf hartes Deny (M5-Flags), Image-Präzisions-Weiche, `@index`-Write-on-read (SEC-DM-2).

## Sperr-Dateien — AUFGELÖST (Stand 2026-07-05, `df1dc8c`)
- ✅ `resources/views/admin/layouts/sidebar.blade.php` + `app/Http/Controllers/EconomicCalculationController.php` sind **committet** (`df1dc8c`) — keine uncommittet-Sperre mehr. `sidebar.blade.php` wird jetzt über die **Parallelbetrieb-Karte** koordiniert (ein Schreiber pro Datei; aktuell Strang A / M4-b).

## Tabu-Zonen (nie anfassen)
- `/api/planner` + Sanctum-Setup
- **OMD-Namespace** `app/Services/Suppliers/Omd/*` (beim Katalog-Import nicht berühren)
