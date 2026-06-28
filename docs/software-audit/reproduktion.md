# Dynamische Verifikation der schwersten Sicherheitsfunde

**Stand:** 2026-06-28 · **Methode:** anonymes `curl` (ohne Session-Cookie) gegen die laufende App, **nur lesende** GET-Endpoints. Destruktive Routen (`/route-cache`, `/fix-notes`, Delete) wurden **nicht** getriggert. Eine echte isolierte Kopie (`ticket_audit`) war nicht baubar: DB-User `ticket_user` ist auf `ticket.*` beschränkt, kein Root; SQLite kann das MySQL-spezifische Schema nicht hosten.

## Methoden-Beweis (Kontrollen)
| Route | erwartet | Antwort | ok? |
|---|---|---|---|
| `GET /home` | geschützt | **302 → /login** | ✅ Methode erkennt Schutz |
| `GET /login` | offen | 200 (Login-Seite) | ✅ Methode erkennt offen |

> Lesart: **302 → /login** oder **401/403** = geschützt. **200 mit echtem Inhalt** = offen. **404/500 ohne Login-Redirect** = Auth-Middleware feuerte **nicht** (Route ist erreichbar, Controller lief/scheiterte erst danach).

## Ergebnis je Fund

| Fund | Trigger (anonym) | Tatsächliche Antwort | Verdikt |
|---|---|---|---|
| **#23** all-contacts/export | `GET /all-contacts/export` | **200 + CSV** | 🔴 **BESTÄTIGT** — anonymer Export aller Kontakte |
| **#23** all-contacts | `GET /all-contacts` | 500, kein Login-Redirect | 🔴 **BESTÄTIGT** — kein Auth-Gate (AllContactController ohne `auth`) |
| **#29** purchase_request | `GET /purchase_request` | 500, kein Login-Redirect | 🔴 **BESTÄTIGT** — kein Auth-Gate (Controller ohne `auth`) |
| **#30** goods-receipts | `GET /admin/goods-receipts` | 500, kein Login-Redirect | 🔴 **BESTÄTIGT** — kein Auth-Gate (Controller ohne `auth`) |
| **#31** beg-fundings | `GET /beg-fundings` | 500, kein Login-Redirect | 🔴 **BESTÄTIGT** — kein Auth-Gate (Controller ohne `auth`) |
| **#20** lead/email/api | `GET /lead/email/api/1` | 404, **kein** Login-Redirect | 🔴 **BESTÄTIGT erreichbar** — WebsiteController ohne `auth`; 404 = E-Mail-ID 1 fehlt, nicht Auth |
| **#26** leaves/notes | `GET /leaves/1/notes` | 404, **kein** Login-Redirect | 🔴 **BESTÄTIGT erreichbar** — LeaveController ohne `auth`; 404 = Leave-ID 1 fehlt |
| **#28** product CRUD | `GET /product`, `/product_details/1` | **302 → /login** | 🟢 **FALSE POSITIVE** — `ProductController:37` hat `$this->middleware('auth')`. Auth greift trotz `is_Admin`-Key-Bug. (Rest-Thema: kein **Rollen**-Check — siehe unten.) |
| **#32** customer_phase | `GET /customer_phase_manage` | **302 → /login** | 🟢 **FALSE POSITIVE** — `CustomerPhaseListController:22` hat `$this->middleware('auth')`. `middlware`-Typo ist folgenlos. |
| **#24** OfferComment | — | `OfferCommentController::class` 0× in `web.php`, 0 in `route:list` | 🟢 **FALSE POSITIVE** — Controller ist **gar nicht geroutet** (toter Code), kein offener Endpoint. |
| **#33** /route-cache | nicht getriggert (destruktiv) | Route-Def: `Route::get('/route-cache', fn)` standalone, keine Auth-Gruppe | ⚠️ **Zugriff offen (statisch)**, Effekt bewusst nicht ausgelöst |
| **#34** /fix-notes | nicht getriggert (destruktiv) | Route-Def: `Route::get('/fix-notes', fn)` standalone, keine Auth-Gruppe | ⚠️ **Zugriff offen (statisch)**, Effekt bewusst nicht ausgelöst |

## Nachgelagerte Stichproben (weitere Tier-A-Kandidaten)
| Fund | Trigger | Antwort | Verdikt |
|---|---|---|---|
| **#122** `GET /make_admin/{id}` | nicht getriggert (mutiert) | `UserController` hat `$this->middleware('auth')` | 🟢 **FALSE POSITIVE** für „anonym" — bleibt nur CSRF-Thema (eingeloggtes Opfer) |
| **#117** `GET /api/lead-name-suggestions` | `curl ?term=a` (anonym) | **401 `{"ok":false,"message":"Unauthenticated"}`** | 🟢 **FALSE POSITIVE** — Endpoint ist geschützt |

## Bilanz (13 Sicherheits-/Routing-Funde geprüft)
- ✅ **6 anonym bestätigt** (real, ohne Login erreichbar): #20, #23, #26, #29, #30, #31 — davon **#23/export = 200 + Datenabfluss**.
- 🟢 **5 FALSE POSITIVES**: #24 (unrouted), #28 (Konstruktor-auth), #32 (Konstruktor-auth), #117 (401), #122 (Konstruktor-auth).
- 🛑 **2 Zugriff-offen, Effekt bewusst nicht getriggert**: #33, #34.

## ⚠️ META-ERKENNTNIS (das eigentliche Ergebnis dieser Phase)
**~38 % der geprüften Sicherheits-/Routing-Funde waren False Positives.** Ursache: Die Audit-Agenten haben Routen statisch geprüft, aber **Controller-Konstruktoren (`$this->middleware('auth')`) und Laufzeitverhalten nicht** berücksichtigt.

**Konsequenz für die Fix-Phase:**
1. „131 bestätigt" heißt **statisch bestätigt** — die belastbare Zahl anonym-ausnutzbarer Funde ist aktuell **6** (dynamisch), nicht 45.
2. **Jeder** verbleibende Tier-A-Fund (18 Stück, ⚠️) muss **vor** einem Fix einzeln dynamisch/Konstruktor-geprüft werden — Quote legt nahe, dass ~1/3 davon ebenfalls FPs sind.
3. Tier B (CSRF/XSS/Rollen) und Tier C (Crash-Bugs) sind davon **weniger betroffen** (Crash-Bugs lassen sich i.d.R. eindeutig am Code zeigen), sollten aber stichprobenartig reproduziert werden.

---

## Gleis 2 — Nachverifikation der restlichen 10 Tier-A-Funde
Gleiche Methode (anonymer lesender Probe / Konstruktor- & Routen-Check, nichts Destruktives).

| Fund | Prüfung | Ergebnis |
|---|---|---|
| **#116** `/api/secure/master-sets` | anonym GET | **401** „API username and password are required" → 🟢 **FALSE POSITIVE** (Controller-Auth vorhanden) |
| **#115** `/register` | anonym GET | **200** (Registrierungsformular öffentlich) → 🔴 **real** — anonyme Selbstregistrierung; Fix = Registrierung deaktivieren (Produktentscheidung) |
| **#104** email-account toggle/test | Routen-/Konstruktor-Check | außerhalb der `auth`-Gruppe + `LeadEmailAccountsController` ohne `auth` → 🔴 **real offen** (POST, nicht getriggert) |
| **#108** brand/distributor/external destroy via GET | Konstruktor-Check | Brand-/Distributor-/ExternalPersonalController **haben** `auth` → kein anonymer → 🟠 **reklassifiziert Tier B** (CSRF, Opfer nötig) |
| **#123** `/dispatch-chat-jobs`, `/chat-jobs` | Routen-/Konstruktor-Check | standalone, `MessageController` ohne `auth` → 🔴 **real offen** (dispatcht Jobs; nicht getriggert) |
| **#124** `/run-backfill-phase-sections` | Routen-Check | standalone Closure ohne Middleware → 🔴 **real offen** (Artisan; nicht getriggert) |
| **#103 / #106 / #118 / #119** | route:list-Check | Routen-**Duplikate/Kollisionen** (kein Auth-Loch) → ⚪ **reklassifiziert Qualität** (#106 und #118 sind derselbe Fund) |

**Bilanz Gleis 2:** von 10 → **1 FALSE POSITIVE** (#116), **1 → Tier B** (#108), **4 → Qualität** (#103/106/118/119), **4 real-offen** (#104, #115, #123, #124).

## Endgültige anonyme Angriffsfläche (Stand 2026-06-28)
- ✅ **geschlossen (Gleis 1):** #20, #23, #26, #29, #30, #31 — nachgewiesen 302→/login (siehe `gleis1-nachweis.md`).
- 🟧 **noch offen, neu bestätigt (Gleis 2):** #104, #115, #123, #124.
- 🛑 **offen, bewusst nicht getriggert (destruktiv):** #33 `/route-cache`, #34 `/fix-notes`.
- 🟢 **FALSE POSITIVES gesamt:** #24, #28, #32, #116, #117, #122 (6).

## Wichtiger systemischer Vorbehalt
Die Audit-Agenten haben **Controller-Konstruktoren nicht auf `$this->middleware('auth')` geprüft**. Genau das hat 2 der „kritischen" Routing-Funde gekippt (#28, #32). **Schlussfolgerung:** Jeder verbleibende „fehlende Auth-Middleware"-Fund (Regel `routing`) muss vor einem Fix gegen den Controller-Konstruktor gegengeprüft werden. Bestätigt **ohne** Konstruktor-Auth sind: AllContactController, PurchaseRequestController, GoodsReceiptController, BegFundingsController, WebsiteController, LeaveController.

## Schritt 3 — Middleware-Abhängigkeitsanalyse
- `#32` (`middlware`-Typo @1444): betrifft **8** customer_phase-Routen — **aber** Controller-Konstruktor erzwingt Auth → real folgenlos.
- `#28` (`is_Admin`-Key @2275): Gruppe hat nur `web` (kein Auth-Gate per Route) — **aber** ProductController-Konstruktor erzwingt Auth. Rest-Thema: **kein Admin-Rollen-Check** → jeder eingeloggte User darf Produkt-CRUD (inkl. `GET /product_destroy`). Downgrade kritisch → mittel (eingeloggtes Opfer nötig).
- `web`-Gruppe (Kernel.php:35–43) enthält **kein** `auth` → keine globale Auth. Die beiden Typo-Fixes schließen **nur ihre eigenen 2 Gruppen**; die übrigen offenen Gruppen (#20, #23, #26, #29, #30, #31, #33, #34) sind **unabhängig** und je einzeln zu schließen.

## Schritt 4 — „tote" Old/-Controller
40 Controller in `app/Http/Controllers/Old/` → **0** aktive Routen (`route:list` ohne `Old\`, `routes/` ohne Old-Namespace). **Keine Angriffsfläche → bleiben Cleanup**, nicht Sicherheit.
