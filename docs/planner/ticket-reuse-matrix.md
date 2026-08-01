<!-- pfade-pruefen: historisch -->
<!-- Matrix mit abgekuerzten Pfaden; die genannten Pfade beschreiben einen vergangenen Zustand (PB-031, 01.08.2026) -->

# Ticket-Reuse-Matrix (Planner)

Vorlage + reale Startzeilen. Vor Produktivänderungen je Slice ausfüllen/aktualisieren.
Reuse-Klasse ∈ {R1 direkt, R2 erweitern, R3 extrahieren, R4 Adapter, R5 nicht (Begründung)}.

| Planner-Anforderung | Ticket-Kandidat | Pfad | Klasse | Vorgehen | Risiken | Tests |
| --- | --- | --- | --- | --- | --- | --- |
| Projektkopf/Toolbar-Rahmen | `x-page-head` | `resources/views/.../components` | R1 | direkt | gering | View-Tests |
| Navigation | Sidebar | `resources/views/admin/layouts/sidebar.blade.php` | R2 | Item ergänzen | Sichtbarkeit/Recht | – |
| Gleichzeitiges Editieren | Bearbeitungssperre-Partial | `.../partials/bearbeitungs-sperre.blade.php` | R1 | `@include` | gering | Ping/Leave |
| Rechte am Objekt | `hasPermission` + `permission:` | `app/Models/User.php` | R1 | Middleware/Gate | Org/Rolle | Policy/Feature |
| Rechte-Registry | `permissionModules()` | `app/Http/Controllers/User/UserRollController.php` | R2 | Item eintragen | – | – |
| Objektbindung | `LeadAlternativeAdd` | Model | R1 | Route-Model-Binding | Fremd-Org-Leak | 404-Test |
| Upload Bild/PDF | `PlanUploadController`/`PlanUpload` | `app/Http/Controllers/Energie/...` | R2/R4 | prüfen | Objektbezug/Storage | Upload-Tests |
| Persistenz/Version/Snapshot | Hausplaner-Domain | `app/Domain/Hausplaner/*` | R1/R2 | wiederverwenden | 409-Konflikt | Snapshot/409 |
| Designsystem/CI | `--sa-*` + Styleguide | `resources/views/admin/styleguide/index.blade.php` | R1 | Tokens/Klassen | – | visuell |
| Heizlast-Projektion | GeometrieAbleitung/Adapter | `app/Services/Heizlast/...` | R4 | Projektion | Statusmapping | Integrationsprüfung |
| 2D-Canvas / 3D-Viewport / Gizmo | kein Kandidat | – | R5 | neu (CAD) | GPU/Performance/Echtzeit | Renderer-Tests |
| Aufgaben/Kommentare/Aktivität | (zu verifizieren) | Phase 2 | R4? | Adapter prüfen | Fachbedeutung ≠ | neu |

R5-Begründung 2D/3D: Echtzeit-GPU-Rendering ist im Ticket nicht vorhanden und nicht abbildbar.

---

## Ergänzte reale Zeilen (Sitzung 2)

| Planner-Anforderung | Ticket-Kandidat | Pfad | Klasse | Vorgehen | Risiken |
| --- | --- | --- | --- | --- | --- |
| CI-Farben/Tokens | `sa-ui` Partial | `resources/views/admin/layouts/partials/sa-ui.blade.php` | R1 | in Planner-Blades einbinden, `var(--sa-*)` nutzen | – |
| Seitenkopf | `x-page-head` | `resources/views/components/page-head.blade.php` | R1 | direkt | – |
| App-Shell/Sidebar | `app.blade.php`/`sidebar` | `resources/views/admin/layouts/*` | R1/R2 | einbetten/Item | Sichtbarkeit |
| Modale/Tabellen | Task-/Produkt-Partials | `resources/views/admin/*/partials/*` | R1 | Muster übernehmen | – |
| CAD-Anmerkungen | Kommentar-System | `app/Models/*Comment.php` | R4 | Adapter (Anmerkung ≠ Ticket-Kommentar) | Fachbedeutung |
| Planner-Aufgaben/Issues | Task-System | `app/Http/Controllers/Task/*`, `app/Models/*Task*` | R4 | Adapter/Projektion | Statusmapping |
| Objekt-Aktivität | Activity/Audit | `CustomerActivity`, `AuditableLead` | R4 | in vorhandenes Aktivitätssystem schreiben | Objektbezug |
| Benachrichtigungen | `app/Notifications/*` | ebd. | R4 | vorhandene Notifications nutzen | – |
| Grundriss-Import | `PlanUpload` + `PlanKlassifizieren` | ebd. | R2/R4 | erweitern/anbinden | Storage/Job |
| Geometrie-Tests | `tests/Unit/{Geometrie,BuildingModel}` | ebd. | R1 | Muster + Nachbarschaft nutzen | – |
