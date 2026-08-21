# Z2-W0-5 · Nuriva-API `api/planner/*`: Zuständigkeitsbindung an vier Stellen, ein Baustein

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, LIVE, Mobile-Token-Fläche)
basis_sha: cb500067            # Messstand Z2c
herkunft: Befunde A-1..A-4 (docs/backlog/inventur-2026-08-21-z2-folge.md), Erstfunde — Nuriva war in der IDOR-Inventur TABU
spur: A — Autorisierung + PII (Standortdaten) + Integrität fremder Kundenakten/Pläne
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung) — VOR dem Bau: Gegenprobe A-1..A-4 wie bei S-1/2/5
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
Kein `api/planner`-Endpunkt liefert oder schreibt Daten eines Mitarbeiters, Kunden, Items oder
Plans, für den der authentifizierte Mitarbeiter nicht zuständig ist. **Ein** wiederverwendbarer
Baustein, vier Anschlussstellen.

## Ist-Beleg (Z2c, Controller-Rümpfe gelesen)
A-1 `PlannerEmployeeApiController@employeeWork/@employeeDayReport` (`routes/api.php:281-287`):
`{employee}` ungeprüft, Antwort mit GPS (`latest_location`). A-2 `PlannerMobileCustomerImageController@upload/@index`
(`:318-322`): `customer_id` nur `exists`. A-3 `PlannerMasterSetController@link/unlink/addToPlan`
(`:340-353`): Binding ohne Scope, `addToPlan` legt Items in fremden Plänen an. A-4
`PlannerItemMaterialController@index/store` (`:356-362`): kein Ownership, `requested_by_employee_id`
vom Client. **Vorhandener Baustein:** `completeItemWithReport()` prüft vorbildlich
`whereExists(planner_item_employees…)` (`PlannerEmployeeApiController.php:1726-1731`);
Vorgesetztenkette `resolveReviewer` existiert im selben Controller.

## Scope · Dateien
- Neuer Baustein (Trait/Service im Planner-Namensraum, Name frei): `darfMitarbeiterSehen(employeeId)`
  = self ODER Vorgesetztenkette (resolveReviewer-Muster) ODER Admin; `istZustaendigFuerItem(item)` /
  `…Plan(plan)` / `…Kunde(customerId)` = `planner_item_employees`-Zuordnung des `authEmployeeId()`.
- Vier Anschlussstellen (A-1..A-4), je `abort(403)`; A-4 zusätzlich `requested_by_employee_id`
  serverseitig aus `authEmployeeId()`.
- Tests `tests/Feature/Planner/PlannerApiZustaendigkeitTest.php` (neu, Muster
  `PlannerApiContractTest.php`, dort existiert `test_my_work_without_employee_is_403`):
  je Endpunkt „fremd → 403", „eigen → 200", A-1 „Vorgesetzter → 200". **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung der Antwortformate (Nuriva-Vertrag bleibt); keine Token-Abilities
(Z2-W0-6); keine Web-Routen (`/planner/*` = Z2-W0-3/Y-6); keine Migration.

## Kanten — und ein benannter Operand
**„Wer darf fremde Mitarbeiter sehen?"** Der Docblock sagt Manager/Admin; die Vorgesetztenkette
ist im Code vorhanden. Dieses Blatt **übernimmt diese im Code angelegte Regel** (self / Kette /
Admin) als Arbeitsannahme — **ausdrücklich als Annahme gekennzeichnet**, im Baubericht wiederholt;
will Yama eine andere Regel (z.B. Team statt Kette), ist das Y-Posten und ändert nur den Baustein.
Nuriva-App darf nicht brechen: die App lädt „eigene" Daten — alle Eigen-Pfade bleiben 200.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: A-1 fremder Mitarbeiter → 403; eigener → 200; Vorgesetzter → 200 | Baustein+A-1 | *n.U.* | Testnamen |
| B: A-2 fremde `customer_id` → 403 (index + upload); zugeordnete → 200 | A-2 | *n.U.* | Testnamen |
| C: A-3 fremder Plan/Item → 403 (link/unlink/addToPlan); eigener → 200 | A-3 | *n.U.* | Testnamen |
| D: A-4 fremdes Item → 403; `requested_by_employee_id` im Datensatz = `authEmployeeId()` trotz abweichendem Client-Wert | A-4 | *n.U.* | Testname + DB-Probe |
| E: Genau EIN Baustein, vier Aufrufer (grep) — keine vier Kopien | Reuse | *n.U.* | grep-Rohausgabe |
| F: `PlannerApiContractTest.php` bleibt grün (Vertrag) | Schutz | *n.U.* | Zähler |

**P1-Kriterien A–D sind vor dem Bau wirksam rot** (Reproduktionen im Befund).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten. Entdeckung einer Fehlsperre: Nuriva-Nutzer
bekämen 403 auf eigene Daten — Kriterium F + die Eigen-Pfade fangen es; bei Zweifel Rückweg per Commit.

## Nachtrag 21.08. — Security-Gegenprobe (opus): A-1..A-4 BESTÄTIGT, Blatt präzisiert

**Gegenprobe:** `route:list --path=api/planner` — alle 20 Routen nur `api` + `Authenticate:sanctum`;
`grep -c "permission:" routes/api.php` → 0; keine Policy, kein `Gate::before`, kein globaler Scope,
kein `resolveRouteBinding`; `PlannerApiContractTest.php` berührt A-1..A-4 nicht. **Radius:** jeder
Account mit Passwort bekommt einen Token mit allen vier Abilities (`PlannerApiAuthController:50-80`,
kein Rollenfilter; 52 User, 49 Nicht-Admins); `sanctum.stateful` enthält `ticket.test` → **jede
eingeloggte CRM-Browser-Session** erreicht `/api/planner/*` per Cookie ohne Token. **Teil-Entwarnung
A-2:** Upload-Typ (`mimes`), Größe (25 MB), Pfad (`basename` + Whitelist, `storage/app`) sind sauber —
nur Ownership fehlt. **Verschärfung A-4:** `:130-131` bevorzugt den Client-Wert aktiv vor
`authEmployeeId()`, kein `exists:employees,id`.

**Baustein für A-1, im Haus vorhanden:** `directSupervisor()` (`:1978-1987`) + `resolveReviewer()`
(`:1994-2018`, zyklensicher, Tiefe 15) — A-1 braucht die **Umkehrung** (vom Ziel aufsteigen, Treffer
auf `authEmployeeId()`), ein Schleifenaufruf, kein neuer Mechanismus. Zweite Achse `isSuperAdmin()`.

**Zwei Baustufen — Stufe 1 ist OHNE offenen Operanden baubar:**
- **Stufe 1 (dieser Auftrag):** A-1: `$employee === authEmployeeId() || isSuperAdmin()`, sonst 403 —
  schließt den GPS-Leak sofort, die 3 Admins arbeiten weiter. A-2/A-3/A-4 wie oben (Zuständigkeit
  über `planner_item_employees`, 404 statt Leak; A-4 `requested_by_employee_id` aus der Validierung
  entfernen, hart `authEmployeeId()`).
- **Stufe 2 (nach Y-9):** Vorgesetztenkette für A-1 — und ob Disponenten/Planer ohne
  Vorgesetztenverhältnis fremde Tage sehen dürfen, ist **Y-9** (Rechte-Fachentscheidung).
- **Matrix-Ergänzung:** G: Stufe-1-Regel für A-1 getestet (fremd → 403, Admin → 200); H: der
  **Cookie-Pfad** (stateful Browser-Session) ist an denselben Prüfungen gebunden — Test über
  `actingAs` ohne Token auf einen A-Endpunkt → 403 für fremd.

**Nebenbefunde aus der Gegenprobe (außerhalb dieses Auftrags, abgelegt in z2-folge):** `secure.image`
ohne Ownership (Bestand); `sanctum.expiration` NULL; `users.is_active` nirgends geprüft.
