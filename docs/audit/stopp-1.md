# ⛔ STOPP 1 — Audit-Ergebnis (Yama entscheidet)

> Synthese aus `01-fehler`, `02-architektur`, `03-swot`. Live-System (~3000 Kunden). Rein lesend erhoben. **Drei Lieferungen:** (A) P0-Liste mit Mini-Fix-Aufträgen · (B) Architektur-Urteil je Bereich · (C) Reihenfolge-Empfehlung. Der entkoppelte CODE-AUDIT-01 (läuft) ergänzt später `code-audit.md` + `bauordnung.md`.

## (A) P0-Liste — sofort (Sicherheit / Datenverlust)

### P0-1 · Anonyme Schreibrouten (kein Login nötig) — **schwerste**
**Fund:** Route-Gruppen `['middleware'=>'web']` ohne `auth`; Controller ohne ctor-auth. Anonym auslösbar: HR-Doks löschen/hochladen (`web.php:1738`), HR-Stammdaten schreiben (`web.php:1772 ff.`), Kunden-Dokumente/Bilder (`ImageController`), Belegkette-Phasen (`CustomerStageController`, web.php:1200), Belegkette-Angebot (`OfferDetailsController`, web.php:3540).
**Mini-Fix:** `auth`-Middleware auf jede betroffene Gruppe; wo Rolle nötig, zusätzlich `CheckUserPermission`. **Verifikation:** anonymer `curl -X POST` je Route → 302/401 statt 200; eingeloggter berechtigter Nutzer weiterhin 200; Suite ≥ Vorgänger. Additiv (nur Middleware, kein Dateneingriff).

### P0-2 · Account-Takeover `UserController::updatePassword`
**Fund:** `User/UserController.php:544`, Route `users/{user}/password` (web.php:2221, nur web+auth) setzt Passwort **beliebigen** Users — kein `is_admin`/`isSelf`. **Mini-Fix:** `abort_unless(auth()->id()===$user->id || auth()->user()->is_admin, 403)` bzw. `CheckUserPermission:Users,update` (wie der korrekte Zwilling `adminUsersPassword`). **Verifikation:** Nutzer A ändert Passwort von B → 403; Self/Admin → ok.

### P0-3 · HR/Lohn/Medizin ungegatet (eingeloggt, IDOR)
**Fund:** `EmployeeController:687/790` profile_update (`salary_per_hour/iban/tax_*`), `:187` updatePasscode, `:678` destroy; `LeaveController:265` approve (fremder Urlaubssaldo); `EmployeeSickController:208-391` (Krankmeldungen). **Mini-Fix:** Rollen-Gate (HR/Admin) + Ownership; Lohn-/Bank-/Medizinfelder nur mit expliziter Personal-Berechtigung. **Verifikation:** Nicht-HR-Nutzer → 403 je Action.

### P0-4 · Belegkette-Löschung ungegatet
**Fund:** `OfferController:1314` destroy (Hard-delete 7-Tabellen-Angebotsbaum), `NewLeadsController:13941` deleteObject (Kaskade Objekt+Gewerke), `:3291` destroyWithReason. **Mini-Fix:** `authorize`-Gate analog `DealController::authorizeDealDelete` (existiert als Vorbild). **Verifikation:** Unberechtigter → 403; berechtigter → atomar (Transaktion).

### P0-5 · Massenlöschungen
**Fund:** `InquiryController:2231` bulkDelete (`whereIn('id',$ids)->delete()` freier IDs), ProductPosition/MaintenanceContract-bulkDelete. **Mini-Fix:** Rollen-Gate + Ownership-Filter auf `$ids`. **Verifikation:** fremde IDs → gefiltert/403.

### P0-6 (DATEN — eigener Yama-Auftrag, NICHT additiv) · FK-Waisen + Test-Rechnung
**Fund:** DI-1: 19 `lead_alternative_adds` mit `lead_id` ohne `new_leads` (Kunde unerreichbar). DI-2: `invoices` Test-Zeile `TST-OPEN-2337` (deal_id verwaist, +1.000 € Umsatz-Skew). **⚠️ Diese Fixes ändern Bestandsdaten → laut DAUERDIREKTIVE ein eigener, explizit von Yama zu beauftragender Posten (kein Beifang).** Vorschlag: (a) Test-Rechnung nach Bestätigung entfernen/markieren; (b) Waisen-lead_id auf korrekte new_leads mappen ODER Objekte als verwaist markieren — braucht Yamas Entscheidung zur Herkunft.

## (B) Architektur-Urteil je Bereich (GRÜN=Design kann drauf · GELB=parallel sanieren · ROT=erst sanieren)
| Bereich | Urteil | Begründung (Beleg) |
|---|---|---|
| **Personal** | 🔴 ROT | Anonyme HR-Schreibrouten (P0-1) + Lohn/Medizin-IDOR (P0-3) — erst absichern |
| **Kunde/Lead** | 🔴 ROT | NewLeadsController 14.054 Z. (Gott-Klasse) + anonyme/ungegatete Kaskadenlöschung (P0-4) + FK-Waisen (DI-1) |
| **Angebot/Auftrag** | 🟡 GELB | Angebot anonym editier-/hardlöschbar (P0-1/4) → sichern; Deal gegated ✅; config-Blade 25k Z. entzerren |
| **Montage/Planner** | 🟡 GELB | PlannerPlan 11k Z.; Rückfluss-Link fehlt (Weiche 6, bekannt); Material-IDOR |
| **Lager/Artikel** | 🟡 GELB | Distributor/Preis-IDOR (P1); Status-Zoo |
| **Kundendienst** | 🟡 GELB | tote Routen `ticketTasks.*` (RT-1); sonst tragfähig |
| **Verwaltung/System** | 🟡 GELB | UserController-Takeover (P0-2); RBAC-in-Blade |
| **Energie/Auslegung** | 🟢 GRÜN | neue Services sauber; nur `radiators`-Altlast nicht andocken |
| **Finanzen/Accounting** | 🟢 GRÜN | invoices gegated + in-house FiBu neu/getestet (TABU-Zone) |
| **Formulare (Querschnitt)** | 🟢 GRÜN | FS-02/04/05/07 gebaut, `new Function` raus |

## (C) Reihenfolge-Empfehlung
1. **P0-Sicherheit zuerst, einzeln** (Betriebsordnung: je Fix Stopp→Fix→Verifikation→Commit): P0-1 (anonyme Routen) → P0-2 (Takeover) → P0-3 (HR/Lohn/Medizin) → P0-4 (Belegkette-Löschung) → P0-5 (Massenlöschung). **Vor allem anderen** — Live-System, teils ohne Login.
2. **DATEN-Posten P0-6** als separater, von dir beauftragter Schritt (nicht additiv).
3. **ROT-Bereiche** (Personal, Kunde/Lead) sanieren, bevor Design drauf kommt.
4. **GELB** parallel zur Design-Phase (Strangler: Neues sauber, Alt-Code bei Berührung).
5. **GRÜN** trägt Design/Erweiterung sofort.

## Offen / NICHT-VERIFIZIERT
- JS-Konsole Top-10 (1A-5) — braucht Browser, manuell.
- Live-Impact-Ranking der 57 toten Routen (welche sind aus Blades verlinkt).
- P0-6-Herkunft der FK-Waisen (Import-Artefakt vs. Re-Mapping).
- Cluster-Zeilennummern SEC-1b/1c teils via Subagent (stichprobenartig gegengeprüft).
