# Funktionstest — Crash-Backlog (Smoke-Test als Admin)

**Stand:** Juni 2026 · Methode: alle 602 GET-Navigationsseiten als Admin (Hoffmann) per HTTP abgerufen.
**Ergebnis:** 471 OK (2xx) · 45 Redirect (3xx) · **44 Crashes (5xx)** · 401er = token-geschützte Mobile-/Planner-APIs (kein Fehler).

Dies ist die **P1-Liste (Crashes / tote Pfade)** — Grundlage für „Schritt für Schritt optimieren".

---

## A) Fehlende Controller-Methoden (Route gebunden, Methode fehlt)
| Seite | Ursache |
|---|---|
| `/lead/overview` | `Customer\Kanban\LeadOverviewController::index` existiert nicht |
| `/assets/list` | `Inventory\AssetController::list` existiert nicht |
| `/employee_details` | `Employee\EmployeeController::view` existiert nicht |
| `/refresh_salary` | `Employee\Profile\SalaryController::salary` existiert nicht |
| `/positions/list` | `Employee\Position\PositionController::loadPosition` existiert nicht |
| `/public-holidays/sample` | `Employee\Profile\PublicHolidayController::downloadSample` existiert nicht |

## B) Falscher Namespace in der View (`action()` zeigt auf nicht existierenden Controller)
| Seite | Ursache |
|---|---|
| `/email_configuration` | View ruft `App\Http\Controllers\EmailConfigurationController@index` — korrekt: `Email\EmailConfigurationController` |
| `/inquiry_type` | View ruft `App\Http\Controllers\InquiryTypeController@store` — Namespace/Methode prüfen |

## C) View-/Blade-Fehler
| Seite | Ursache |
|---|---|
| `/new_lead_create` | **Blade-Syntaxfehler** in `customer.blade.php:15` (`unexpected identifier "services"`) — blockiert Neukunden-Maske |
| `/daily_report` | `Undefined variable $employee_name` in `report.blade.php:2877` |
| `/task_phase_create` | View `admin.task.phase.phase_create` nicht gefunden |

## D) SQL / falsche Spalte
| Seite | Ursache |
|---|---|
| `/lead_product_lists` | `Unknown column 'product_positions.position_id'` — Query referenziert nicht existierende Spalte |

## E) Fehlender Routen-Parameter
| Seite | Ursache |
|---|---|
| `/visual/plan` | `Missing required parameter for [Route: planner.projects.profile.data]` |

## F) Weitere 5xx — teils AJAX-Endpunkte ohne Pflicht-Parameter (im Normalbetrieb evtl. ok)
Diese werden im UI per JavaScript mit Parametern aufgerufen; der nackte Direktaufruf crasht. Einzeln prüfen, ob echter Bug oder nur fehlende Query-Parameter:

`/contact_list` · `/customer_phase_manage` · `/appointments/fetch` · `/calendar/datasets` ·
`/dashboard/load-tab` · `/dashboard/tab-counts` · `/chat/customers/search` · `/get-employees` ·
`/email_refresh` · `/employee-sick` · `/handover_multiple` · `/activities_create` · `/activity/nextStep` ·
`/admin/fusion-forms/import/ajax` · `/admin/lead-email-accounts/create` · `/admin/lead-email-accounts/realtime-data` ·
`/admin/master-set-carts` · `/admin/master-set-carts/create` · `/admin/settings/costing-sets` · `/admin/teams/create` ·
`/lead/appointments/index` · `/lead/archive` · `/lead/junk` · `/lead/references` · `/lead/kanban/value-analytics` ·
`/lead_new_sort` · `/lead_qualified_sort` · `/lead_incomplete_sort` · `/lead_not_qualified_sort` · `/lead_junk_sort` ·
`/new-leads/neighbor-products`

> **Achtung Daten-Integrität:** Einige dieser GET-Endpunkte (`/lead/junk`, `/lead/archive`, `*_sort`) ändern beim bloßen Aufruf den Datenbestand (GET mit Seiteneffekt). Während des Tests wurden so operative Demo-Daten verändert/geleert. Das deckt sich mit dem bekannten Befund **P4-35 (zustandsändernde GET-Routen ohne CSRF)** und sollte auf POST/DELETE umgestellt werden.

---

## Empfohlene Reihenfolge (P1)
1. **C/new_lead_create** zuerst (Blade-Syntaxfehler blockiert Neukunden-Anlage).
2. **A** fehlende Methoden (Kernseiten: lead/overview, assets/list, employee_details).
3. **B** Namespace-Fixes (email_configuration, inquiry_type).
4. **D/E** SQL-Spalte + Routen-Parameter.
5. **F** einzeln einordnen (echter Bug vs. AJAX-ohne-Parameter) + GET-Seiteneffekte (P4-35) entschärfen.
