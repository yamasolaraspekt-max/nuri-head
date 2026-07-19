# Reuse-Matrix (Vorlage)

Vor Produktivänderungen ausfüllen. Ohne ausgefüllte Matrix keine Neuentwicklung.

| Planner-Anforderung | Ticket-Kandidat | Pfad | Reuse-Klasse | Vorgehen | Risiken | Tests |
| ------------------- | --------------- | ---- | ------------ | -------- | ------- | ----- |
| Projektkopf | `x-page-head`-Komponente | (Pfad) | R1 | direkt verwenden | gering | bestehende View-Tests |
| Bearbeitungssperre | `bearbeitungs-sperre` Partial | `resources/views/admin/layouts/partials/bearbeitungs-sperre.blade.php` | R1 | `@include` | gering | Sperr-Ping/Leave |
| Rechte am Objekt | `User::hasPermission` + `permission:Item,action` | `app/Models/User.php` | R1 | Middleware/Blade-Gate | Org-/Rollenbindung | Policy-/Feature-Tests |
| Dokument-Upload | `PlanUploadController` + `PlanUpload` | `app/Http/Controllers/Energie/PlanUploadController.php` | R2/R4 | prüfen | Objektbezug, Storage | Uploadtests |
| Versionierung/Snapshot | Hausplaner Snapshot/Revision | `app/Domain/Hausplaner/...` | R1/R2 | wiederverwenden | Revision-Konflikt (409) | Snapshot-/409-Tests |
| 3D-Viewport | kein Kandidat | – | R5 | neu entwickeln | GPU/Performance | Renderer-Tests |

Spalten: Reuse-Klasse ∈ {R1, R2, R3, R4, R5}. Jede R5-Zeile braucht eine technische Begründung.
