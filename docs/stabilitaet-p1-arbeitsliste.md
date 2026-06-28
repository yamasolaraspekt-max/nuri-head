# P1-Arbeitsliste (dedupliziert) — Stabilisierung ticket

**Stand:** 2026-06-28 · Zusammenführung der P1 aus `stabilitaet-fixliste.md` (Schwere) und `stabilitaet-routing-workflow.md` (Routing/Workflow). Gleicher Bug = ein Eintrag. **Nur lokal fixen, jeder Fix einzeln verifiziert + committet.**

Quelle: S = Stabilitäts-Liste · R = Routing/Workflow-Liste.

## PAKET 0 — Sicherheit (anonym erreichbar, zuerst)
| # | Stelle | Was | Datei:Zeile | Aufw |
|--:|---|---|---|:--:|
| 0.1 | Termin-Reports | `customer/appointments/{id}/reports` außerhalb auth-Gruppe → Kundendaten ohne Login | routes/web.php:4846 | S |
| 0.2 | Startseite | `/` und `/home` ohne auth → 500 (`auth()->user()->name` auf null) statt Login-Redirect | routes/web.php:605-606 | S |
| 0.3 | Feiertage | `public-holidays`-Gruppe ohne auth → anonym schreibbar | (Routengruppe) | S |
| 0.4 | Arbeitsorte | `work.place.*` (middleware `web` ohne auth) + store ohne Validierung | routes/web.php:1859 | S |
| 0.5 | **Weitere** ungeschützte Gruppen | per `route:list` systematisch gesucht | — | — |

## PAKET 1 — Wurzelfehler `users.name = employees.id` / fehlendes `employee_id` (1 Konzept, ~9 Stellen)
**Kern:** `user_rolls.user_id` (FK→users.id) wird mit `auth()->user()->name` (=employee_id) verglichen; `Auth::user()->employee_id` existiert nicht. → **Lösungsweg ZUERST melden (Pflicht-Stopp).**
| Stelle | Datei:Zeile | Quelle |
|---|---|---|
| Tagesberichte `isAdmin()` | DailyReportController:1651 | S+R |
| Artikel-Favoriten `authorizeOwner()` | ProductFavoriteListController:275 | S+R |
| Datenbankbereinigung | GarbageController:29 | R |
| Feedback | FeedbackController:29 | R |
| Eingeschränkte Benutzer | limit_user.blade.php:6 | R |
| Filialen (CRUD-Buttons) | branch.blade.php:599,803,858 | R |
| Mein Profil ($employee fehlt) | UserController:83 | R |
| Wissensdatenbank-Kategorien | knowledge/category/index.blade.php:155 | R |
| Persönliche Notizen `user_id` | PersonalNoteController:37 | R |

## PAKET 2 — Datenverlust in genutzten Kernbereichen
| # | Stelle | Was | Datei:Zeile | Aufw |
|--:|---|---|---|:--:|
| 2.1 | Anfrage bearbeiten | `status='Unpublished'` bedingungslos → verifizierte Anfrage verliert Status | InquiryController:1360,1072,2519 | S |
| 2.2 | Lagerausgaben | destroy/update ohne Bestand-Rückbuchung → Inventarschwund | InventoryRequestOutController:256,305 | S |
| 2.3 | Urlaub Genehmigung | `approve()` reduziert `remaining_day` nicht | LeaveController:230 | S |

## PAKET 3 — Crashes / tote Pfade in genutzten Kernbereichen
| # | Stelle | Was | Datei:Zeile | Aufw |
|--:|---|---|---|:--:|
| 3.1 | Anfragen Kanban | `InquiryController@updateStatus` fehlt → Drag 500 | routes/web.php:4021 | S |
| 3.2 | Anfragen | `InquiryController@reverify` fehlt → 500 | routes/web.php:4085 | M |
| 3.3 | E-Mail-Konten | falscher Namespace `EmailConfigurationController` | configuration.blade.php:26,67,283 | S |
| 3.4 | Externe Firmen | Formular postet `external.update` statt `external.store` | external.blade.php:63 | S |
| 3.5 | Alle Kontakte | `restore()` auf Brand/Distributor ohne SoftDeletes | AllContactController:994,1001 | S |
| 3.6 | Übergaben | import `Assets` statt `Asset` | HandoverController:8 | S |
| 3.7 | Persönliche Notizen | `initNoteSelect2` ReferenceError + toter `add_category` | note.blade.php:1079,1506 | S |
| 3.8 | Mitarbeiter-Dashboard | `getProjects()/getOffers()` fehlen | EmployeeDashboardController:421-423 | M |
| 3.9 | Leadliste | Objekt-Restore Route `restore-deleted` fehlt | NewLeadsController / customer_view.blade.php:5878 | S |
| 3.10 | Meine Aufgaben | `leadStages` nicht an View übergeben → leeres Dropdown | PersonalTaskController:298 | S |
| 3.11 | Wartungs-Checklisten | alle Buttons ohne JS-Handler (tot) | maintenance_checklists/index.blade.php | L |
| 3.12 | Wartungsverträge | Edit postet immer `store` → Duplikat statt Update | maintenance/wizard.blade.php | M |
| 3.13 | Auftrag anlegen | `DealController` fehlen store/dealStore/info/price/jump (+junk/unjunk/destroy/restore) | DealController | L |

## Bereits erledigt (nicht erneut)
- **BegFunding::create → 500** (R-P1 #6): durch das neue Förderungen-Modul ersetzt, `beg-fundings`-Route deaktiviert. ✅

## Nicht in diesem Auftrag
Performance (P3), Faktura/AB-Schritt/Rechnungsketten (später via playground), tote MAIN-Header (P4), Deployment.
