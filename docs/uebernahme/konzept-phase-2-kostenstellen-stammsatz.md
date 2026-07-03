# Konzept Phase 2 (verfeinert) — Kostenstelle als stabiler Stammsatz „Abteilung je Filiale"

**Stand:** 2026-07-02 · **Read-only Konzept — KEIN Code, KEINE Migration, keine bestehende Datei geändert, nichts aus playground importiert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, **keine** Design-Vorlage — spätere Views strikt im ticket-Design (Vuexy/Bootstrap-Blade, ticket-Sidebar, Cards/Tabellen/Modals/Badges, Select2/Toastr).
**Verhältnis zu bestehenden Docs:** ergänzt/verfeinert `docs/uebernahme/arbeitspaket-phase-2-kostenstellen.md` (dessen Kostenstellen-Modell wird durch das **Stammsatz-Modell** hier ersetzt); baut auf `arbeitspaket-phase-0-1-buchhaltung.md` und `buchhaltung-datev-integrationsplan.md` auf. Planner-/Kanban-Dateien im Repo werden **nicht** angefasst.

## Neue fachliche Leitentscheidung
**Die Kostenstelle ist NICHT die lebende Abteilung.** Sie ist ein **stabiler buchhalterischer Stammsatz** für „Abteilung je Filiale". Abteilungen sind operative Organisation (erstellbar, umbenennbar, verschiebbar, auflösbar); Kostenstellen müssen historische Rechnungen/Buchungen **stabil und rückwirkungsfrei** halten. Zusätzlich: Kontenrahmen wählbar (SKR03/04) über `chart_of_accounts` + `account_mappings`; **niemals harte Kontonummern im Code** — immer `mapping_key`.

---

## 1. Ergebnis der Read-only-Datenanalyse → **ticket ist eindeutig Fall A**

| # | Frage | Ergebnis |
|---|---|---|
| Q1 | departments gesamt (aktiv) | **16** |
| Q2 | `branch_id` NULL | **0** |
| Q3 | `branch_id` gesetzt | **16 (100 %)** |
| Q4 | gleicher `department_name` über mehrere `branch_id` | **keine** |
| Q5 | gleicher `department_name` mehrfach mit `branch_id` NULL | **keine** |
| Q6 | deals mit `department_id` / davon Abteilung ohne branch | **14 / 0** |
| Q7 | projects mit `department_id` / davon Abteilung ohne branch | **31 / 0** |
| Q8 | invoices gesamt / mit `deal_id` / eindeutig via deal ableitbar | **11 / 11 / 10** |
| Q9 | unklar (dept/branch fehlt) / ohne deal | **0 / 0** |
| — | branches gesamt (Kontext) | **1** |

**Der einzige nicht ableitbare Fall (Q8: 11 vs. 10):** Rechnung `TST-OPEN-2337` (id 21) mit **verwaistem `deal_id = 36`** (Deal-Zeile existiert nicht) — Testdatensatz, kein Strukturproblem. → manuelle Zuordnung.

### Verdikt: **Fall A** — Abteilungen sind bereits filialspezifisch (branch_id 100 % gesetzt), keine filialübergreifenden Namensdopplungen, alle deal-/project-Abteilungen haben eine Filiale. **Automatische Ableitung ist sehr gut möglich.**

**Zwei wichtige Konsequenzen:**
1. **Aktuell nur 1 Filiale** → „Abteilung je Filiale" fällt heute praktisch mit „Abteilung" zusammen. Das Modell muss aber **mehrere Filialen** tragen (Zukunft), also `branch_id` als vollwertige Achse behalten.
2. Weil Ableitung heute zuverlässig ist, kann Backfill **fast vollständig automatisch** vorschlagen — nur der eine verwaiste Testfall bleibt manuell.

---

## 2. Tabellen-/Feldvorschlag `cost_centers` (Stammsatz)

| Feld | Typ | Bemerkung |
|---|---|---|
| id | id | |
| code | string(20) | DATEV-KOST1-Code (z. B. `1010`); **unveränderlich** nach Erstvergabe; von Yama vergebene Systematik |
| name | string | buchhalterischer Name (z. B. „Montage Berlin"); **eigenständig**, folgt NICHT automatisch der Abteilungs-Umbenennung |
| branch_id | unsignedBigInteger, nullable | FK → branches (nullOnDelete); **für `active` Pflicht** (App-Regel) |
| department_id | unsignedBigInteger, **nullable** | FK → departments (nullOnDelete); nullable, damit historische Kostenstellen bestehen bleiben, auch wenn die Abteilung geändert/archiviert/gelöscht wird |
| parent_id | unsignedBigInteger, nullable | FK → cost_centers self (optionale Hierarchie) |
| valid_from | date | Beginn der Gültigkeit |
| valid_until | date, nullable | Ende der Gültigkeit (NULL = offen) |
| status | string(12) | `active` / `inactive` / `archived` |
| closed_at | timestamp, nullable | Zeitpunkt der Stilllegung/Archivierung |
| replacement_cost_center_id | unsignedBigInteger, nullable | FK → cost_centers self; Nachfolger bei Zusammenlegung |
| created_by | unsignedBigInteger, nullable | FK → employees (nullOnDelete) |
| updated_by | unsignedBigInteger, nullable | FK → employees (nullOnDelete) |
| timestamps | | |

**Keine echte Löschung.** softDeletes ist **nicht** vorgesehen (fachlich nicht begründet — Stilllegung erfolgt über `status`/`closed_at`, nicht über Löschen). Ein `delete()` wird per Model-Guard (aus Phase 0, `DeletionGuard`) unterbunden.

**Eindeutigkeit / Constraints:**
- **`unique(code)`** (hart, DB).
- **Höchstens EIN `status='active'` je `(branch_id, department_id)`** — App-seitig erzwungen (kein DB-Partial-Unique in MySQL 5.7/8 ohne Tricks; daher Service-Prüfung + optional generierte Spalte). Historische/inaktive/archivierte Zeilen dürfen `(branch_id, department_id)` mehrfach tragen (Versionierung).
- `branch_id` bei `status='active'` **NOT NULL** (App-Regel, nicht DB, da Altzeilen).

---

## 3. Status-/Historienregeln (dynamische Abteilungen)

| Ereignis (operative Abteilung) | Wirkung auf Kostenstelle |
|---|---|
| **Abteilung neu angelegt** | keine automatische KST; KST entsteht per Generator/Backfill für `(branch, department)` (Vorschlag, manuell bestätigt) |
| **Abteilung umbenannt** | KST-`name` bleibt **unverändert** (rückwirkungsfrei). Optional Hinweis „Abteilungsname weicht ab"; Umbenennen der KST ist eine **bewusste** eigene Aktion, keine Automatik |
| **Abteilung verschoben** (andere Filiale) | bestehende KST wird `archived`/`inactive` (mit `closed_at`), **neue** KST für die neue `(branch, department)` angelegt; alte Rechnungen behalten ihre alte `cost_center_id` |
| **Abteilung aufgelöst** | KST → `status='inactive'` (bzw. `archived`), `closed_at` gesetzt; **bleibt historisch erhalten**; `department_id` darf bestehen bleiben (Referenz) oder bei DB-Löschung via nullOnDelete auf NULL fallen |
| **Abteilung geht in andere auf (Merge)** | Ursprungs-KST → `archived`, `replacement_cost_center_id` = Ziel-KST; neue Rechnungen laufen auf die Ziel-KST |

**Grundprinzipien:**
- **Alte `invoices` behalten ihre `cost_center_id` immer** — keine rückwirkende Neuzuordnung, kein Rückwirken von Umbenennungen.
- **Neue `invoices` dürfen nur `active` + gültige KST** verwenden (`valid_from <= heute <= valid_until` oder `valid_until` NULL).
- Kostenstellen werden **nie gelöscht**, nur `inactive`/`archived`.

---

## 4. Ableitungsregeln `cost_center_id` (Auftrag→Rechnung→Kostenstelle)

**Schritt 1 – Abteilung bestimmen (erste nicht-leere Quelle gewinnt, streng priorisiert):**
1. **Rechnungs-Kopfwert** `invoices.department_id` (manuelle Wahl) — **gewinnt immer**.
2. `invoices.deal_id → deals.department_id`.
3. `invoices.project_id → projects.department_id`.
4. `invoices.offer_detail_id → offer/lead_product_list.department_id` (falls die Angebots-/Positionsquelle eine Abteilung trägt).
5. **Ersteller** `invoices.created_by → employee_departments` — **nur schwacher Vorschlag**, und **nur** wenn genau **eine** Abteilung; nie automatisch scharf.

**Schritt 2 – Filiale bestimmen (Fall A):** `branch_id = departments.branch_id` (deterministisch, da 100 % gesetzt). *(Fall-B-Zweig — Filiale aus Ersteller/Auftrag oder manuell — bleibt im Modell erhalten, ist aber laut Analyse aktuell nicht nötig.)*

**Schritt 3 – Kostenstelle auflösen:** aktive, gültige `cost_centers` per `(branch_id, department_id)`. Genau ein Treffer → als **Vorschlag** setzen (im UI überschreibbar). 0 Treffer → Fehlerfall (§8). >1 aktiver Treffer → darf nicht vorkommen (App-Unique), sonst Datenfehler-Meldung.

**Härteregeln:**
- Ableitung erzeugt nur einen **überschreibbaren Vorschlag**, nie eine stille scharfe Zuordnung.
- **Bei Mehrdeutigkeit rät das System nicht** — `cost_center_id` bleibt leer, manuelle Auswahl wird erzwungen.
- Quelle 5 (Ersteller) füllt nie automatisch bei `sent`, sondern markiert nur einen Vorschlag zur Bestätigung.

---

## 5. Pflichtfeldregeln

- **Drafts** (`status='draft'`) dürfen `department_id`/`cost_center_id` **offen** lassen.
- **Ab `status='sent'`**: `department_id` **und** `cost_center_id` müssen gesetzt sein (App-Validierung im FormRequest/Service). **Kein DB-`NOT NULL`** (Altbestand).
- Bei unklarer/mehrdeutiger Ableitung: **manuelle Auswahl erzwungen** (Speichern als `sent` blockiert mit Toastr).
- Nur `active` + gültige KST zuweisbar; `inactive`/`archived` nicht für neue Rechnungen.
- **Altbestand bleibt grandfathered** — bestehende Rechnungen ohne KST bleiben gültig, keine rückwirkende Pflicht.

---

## 6. Backfill-Strategie

**Separates Artisan-Command, kein Auto-Run, idempotent, dry-run zuerst.**
1. **KST erzeugen:** aus real vorkommenden `(branch, department)`-Kombinationen (aus `departments` + genutzt in `deals`/`projects`) je eine `cost_centers`-Zeile vorschlagen: `code` (generierte Systematik, Yama bestätigt), `name` = „<Abteilung> <Filiale>", `branch_id`, `department_id`, `valid_from` = heute/Startdatum, `status='active'`.
2. **Rechnungen zuordnen — nur sichere Fälle automatisch vorschlagen:** eindeutig via deal/project/department + branch (laut Analyse **10 von 11**). Alles andere **nicht** setzen.
3. **Unsichere Fälle als Report:** z. B. verwaister `deal_id` (`TST-OPEN-2337`) → Liste „manuelle Zuordnung nötig".
4. **Dry-Run-Report zuerst:** Zuordnungsquote, KST-Vorschlagsliste, unsichere Fälle; **verändert nichts**. Erst nach Freigabe realer Lauf.
5. **Idempotenz:** zweiter Lauf erzeugt keine Dubletten (`unique(code)` + `(branch,department)`-Aktiv-Regel), setzt keine bereits gesetzten `cost_center_id` um.
6. **Grandfathering:** nicht zuordenbare Altrechnungen bleiben NULL + Badge „Kostenstelle offen".

---

## 7. UI-Auswirkungen (nur ticket-Design)

- **Kostenstellen-CRUD**: neuer Sidebar-Punkt (Controlling/Stammdaten), ticket-Tabelle mit Spalten Code/Name/Filiale/Abteilung/Status/Gültigkeit; Modal-Formular; **kein Löschen-Button**, stattdessen „Inaktiv setzen"/„Archivieren" + „Nachfolger wählen" (`replacement_cost_center_id`).
- **Generator „Filiale × Abteilung"**: Assistent, der fehlende KST für vorhandene Kombinationen vorschlägt (mit `code`-Vorschlag), Mehrfachanlage nach Bestätigung.
- **Aktive/inaktive/archivierte KST sichtbar**: Status-Badges (grün/grau/rot), Filter; archivierte mit `closed_at` + Nachfolger-Verweis.
- **Rechnungsformular** (`InvoiceController`/`InvoiceCanvasController`-Views): read-only angezeigter **abgeleiteter** KST-Vorschlag + **Select2-Override** (nur aktive/gültige, gefiltert nach Filiale); Abteilungsfeld; Validierungs-Toastr bei `sent`.
- **Warnbadge „Kostenstelle offen"** auf Rechnungen ohne Zuordnung (Altbestand/manuelle Nachpflege).
- **Keine** Konten-/Buchhaltungs-UI (chart/accounts/mappings bleiben leer in Phase 2).

---

## 8. SKR03/SKR04-Vorbereitung (Struktur, keine Werte)

- **`chart_of_accounts`**: `id · code (SKR03|SKR04) · name · is_active bool default false · timestamps`. Beide Rahmen als Zeilen möglich; **aktiv erst nach StB (B1)**.
- **`accounts`**: `id · chart_of_account_id(FK) · account_number · name · type · normal_balance(soll|haben) · is_active · timestamps`. **In Phase 2: 0 Zeilen** (Befüllung Phase 3).
- **`account_mappings`**: `id · chart_of_account_id(FK) · mapping_key (z. B. sales_revenue_19) · account_id(FK, nullable) · valid_from · valid_until · timestamps`. `unique(chart_of_account_id, mapping_key, valid_from)`. **In Phase 2: 0 Zeilen.**
- **`accounting_settings.active_chart_of_account_id`** (nullable FK → chart_of_accounts): **bleibt NULL bis B1.**
- **`AccountResolutionService` (Skelett):** einziger Weg zu einem Konto — nimmt `mapping_key` + `active_chart_of_account_id`, sucht das gültige Mapping. **Ohne Mapping liefert er kontrolliert `unmapped`/deny und wirft NIE eine hartkodierte Kontonummer.** In Phase 2 existieren 0 Mappings → jeder Aufruf endet in `unmapped` (gewollt). Damit ist der `mapping_key`-Zwang erzwungen, **bevor** überhaupt Konten existieren.
- **Keine echten Kontenwerte, keine Steuerschlüssel, kein Seeder mit Konten** in Phase 2.

---

## 9. Fehlerfälle

| Fall | Verhalten |
|---|---|
| Verwaister `deal_id` (z. B. TST-OPEN-2337) | keine Ableitung → `cost_center_id` leer, Report + manuelle Wahl bei `sent` |
| Keine Abteilung ableitbar | leer; `sent` blockiert mit Toastr „Abteilung/Kostenstelle erforderlich" |
| Ersteller in mehreren Abteilungen | mehrdeutig → kein Rateweg, leer + Pflichthinweis |
| `(branch, department)` ohne aktive KST | Fehler „Keine aktive Kostenstelle" + Angebot, sie anzulegen |
| Deal-Abteilung ≠ Kopf-Abteilung | Kopfwert gewinnt; Badge „Abweichung Auftrag/Rechnung" |
| KST `inactive`/`archived` gewählt | abgelehnt; nur `active`+gültig zuweisbar |
| Abteilung umbenannt nach Buchung | KST/`name` unverändert; historische Rechnung unberührt (rückwirkungsfrei) |
| Versuch, KST zu löschen | durch `DeletionGuard` unterbunden; nur Inaktiv/Archiv |
| Zweite aktive KST für gleiche `(branch, department)` | App-Unique verweigert Anlage |

---

## 10. Tests / Prüfpunkte

- **Ableitung (Unit):** Präzedenz Kopf > deal > project > offer > creator; creator nur bei genau einer Abteilung; Fall A deterministisch; Mehrdeutigkeit → leer.
- **Historie:** Umbenennung Abteilung ändert KST-`name` nicht; alte Rechnung behält `cost_center_id`; Merge setzt `replacement_cost_center_id`; Auflösung setzt `status`+`closed_at`, KST bleibt.
- **Eindeutigkeit:** `unique(code)`; höchstens eine aktive KST je `(branch, department)` (App); inaktive Dubletten erlaubt.
- **Pflichtfeld:** `draft` ohne KST speicherbar; `sent` blockiert ohne dept+KST; nur aktive/gültige zuweisbar.
- **Backfill:** Dry-Run verändert nichts; 10/11 automatisch vorgeschlagen; verwaister Fall im Report; idempotent (2. Lauf keine Dubletten/keine Umzuordnung); Live-Row-Count `invoices` unverändert.
- **Löschschutz:** `delete()` auf KST verweigert.
- **SKR-Vorbereitung:** `AccountResolutionService` ohne Mapping → `unmapped` (nie Konto); `accounts`/`account_mappings` leer; `active_chart_of_account_id` NULL.
- **Gate:** alle scharfen Finanz-Gates weiterhin ROT.
- **Regression:** vollständiger Rechnungs-Flow grün.

---

## 11. Was weiterhin vom Steuerberater blockiert bleibt

- **B1** — welcher Kontenrahmen aktiv (SKR03/04): `active_chart_of_account_id` bleibt NULL.
- **B2** — Kontenplan/Sachkonten: `accounts` leer.
- **B3** — Steuerschlüssel/BU (19/7/0 % PV/§13b/ig/steuerfrei): keine `tax_codes` in Phase 2.
- **B4** — Debitoren-/Kreditoren-Nummernsystematik + Sachkontenlänge: Phase 3.
- **`account_mappings`-Inhalte** (welcher `mapping_key` → welches Konto): erst nach B1+B2.

**Nicht** StB-blockiert (jetzt baubar): das **Kostenstellen-Stammsatz-Modell** + Zuordnung + Historienregeln (Yama-Entscheidung). Nur die **KOST1-Code-Systematik** weich mit Steuerberater/DATEV auf Kompatibilität abgleichen (kein Blocker).

---

## 12. Definition of Done

1. `cost_centers` (Stammsatz mit Historie/Status/Replacement) + leere `chart_of_accounts`/`accounts`/`account_mappings` + `accounting_settings.active_chart_of_account_id` (NULL) — als reversible Struktur (Umsetzung später).
2. KST für alle real vorkommenden Filiale×Abteilung-Kombinationen per Backfill-Command vorgeschlagen; Dry-Run-Report geprüft; 10/11 Rechnungen automatisch, 1 im Report.
3. Neue `sent`-Rechnungen erzwingen Abteilung + aktive/gültige KST; Drafts frei; Altbestand grandfathered.
4. Historienregeln greifen: Umbenennung rückwirkungsfrei, Auflösung/Merge korrekt (Status/closed_at/replacement), keine Löschung möglich.
5. Ableitung deterministisch + überschreibbar; Fehlerfälle (§9) sauber; bei Mehrdeutigkeit leer statt raten.
6. `AccountResolutionService` erzwingt `mapping_key`-Weg, liefert ohne Mapping `unmapped`; keine Kontenwerte/Steuerwerte; alle Finanz-Gates ROT; Live-Daten unverändert; Rechnungs-Flow grün.

---

## 13. Klare Empfehlung

**ticket ist Fall A.** Baue die automatische Ableitung `deal/project → department → branch → Kostenstelle` als Standard — sie deckt laut Daten ~91 % (10/11) sofort ab, Rest ist Testdaten. Modelliere `cost_centers` als **stabilen Stammsatz mit Historie** (nicht als Spiegel der lebenden Abteilung), damit Umbenennungen/Auflösungen/Merges historische Rechnungen nie verfälschen. Halte `department_id` nullable und `branch_id` als vollwertige Achse (heute 1 Filiale, Modell zukunftssicher für mehrere). Die Filial-Wahl im Workflow ist aktuell **nicht** nötig (Fall A), der Fall-B-Zweig bleibt als Reserve im Modell. SKR03/04 wird strukturell vorbereitet, bleibt aber wert- und mapping-leer bis zur Steuerberater-Freigabe (B1/B2).
