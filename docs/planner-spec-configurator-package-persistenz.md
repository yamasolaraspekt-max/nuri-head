# Planner-Spec — ConfiguratorPackage-Persistenz (ticket, Heimat-App)

**Rolle:** Planner. Kein Produktionscode hier — Spezifikation für Generator + Evaluator.
**Heimat-App:** ticket (LIVE, höchste Sorgfaltsstufe). Bauordnung gilt: kein Endpunkt ohne Auth-Gate,
keine ID aus dem Request ohne Ownership, eine Wahrheit je abgeleitetem Wert.

## Ziel & Entscheidung
Autarke Konfigurationen aus dem ConfigWizard (Fenster/Tür/Treppe, künftig alle Fachplaner) werden
**serverseitig gespeichert** statt nur als JSON heruntergeladen. Eine gespeicherte Konfiguration ist
ein `ConfiguratorPackage` (Frontend-Typ `geometry/configuratorPackage.ts`, `schemaVersion` = 1) mit
Freigabestatus (`draft`…`approved`…`integrated`) und **Revisionierung** analog `hausplaner_documents`.

Entscheidung: **Neue Tabelle `configurator_packages`** (nicht `hausplaner_catalog_items` — das ist der
Referenz-Katalog, nicht nutzererstellte, statusbehaftete Konfigurationen). Anker = **Kunde**
(`customer_id`, nullable) + optional `alternative_id` (Objekt), damit ein Paket autark ohne Objekt
existieren kann und später verlustfrei zugeordnet wird (Yamas Autark-Leitplanke §3/§18).

## Nahtstellen
- **Migration** `create_configurator_packages_table`:
  `id`, `uuid`(char36, unique — die Frontend-Paket-id), `customer_id`(nullable, FK weich),
  `alternative_id`(nullable), `type`(string 40 — window|door|stair|…), `schema_version`(default 1),
  `revision`(default 1), `status`(string 20 — draft|…|integrated|outdated),
  `parameters`(json), `technical_data`(json), `geometry`(json nullable),
  `checks`(json), `warnings`(json), `checksum`(char64 nullable), `created_by`/`updated_by`, `timestamps`.
  Index auf (`customer_id`,`type`,`status`).
- **Model** `App\Models\ConfiguratorPackage` (`$casts` json-Felder; `$fillable` eng).
- **Controller** `HausplanerController` erweitern (KEIN neuer Controller):
  - `paketSpeichern(SpeichereConfiguratorPackageRequest $request): JsonResponse` — `store`/`update`
    per `uuid` (Upsert mit `revision`+1 bei Update; **base_revision-Prüfung → 409** wie beim Dokument).
  - `paketListe(Request $request): JsonResponse` — nur Pakete, die dem eingeloggten Nutzer/Kunden
    zustehen (Ownership-Gate).
- **FormRequest** `SpeichereConfiguratorPackageRequest`: validiert `uuid`,`type`(in-Liste),
  `status`(in STATUS_UEBERGAENGE-Domäne), `parameters`/`geometry` als array; `authorize()` prüft
  `permission:hausplaner.*` **und** Ownership (customer gehört zum Nutzerkreis).
- **Routen** (`routes/web.php`, `auth`+`permission:hausplaner.*`, CSRF):
  `POST /admin/hausplaner/pakete` → paketSpeichern; `GET /admin/hausplaner/pakete` → paketListe.
  Bewusst NICHT: löschen (kein Hard-Delete; `outdated` statt Löschen — Freigabe-Schutz).
- **Frontend** `ConfigWizard.tsx`: „Übernehmen" im Autark-Fall POSTet das Paket (fetch, CSRF-Header)
  statt Blob-Download; bei Erfolg Toast „gespeichert (Rev. N)". Der Modell-Schreibpfad (gewählte Wand)
  bleibt unverändert. Save-URL wird wie `data-speichern-url` aus der Blade injiziert (kein Hardcode).

## Kantenliste
- Doppel-Speichern desselben `uuid` → Update mit `revision+1`, nicht zweiter Datensatz (Unique-uuid).
- Veraltete base_revision (paralleler Editor) → **409**, kein stiller Überschreib (wie Dokument).
- Paket ohne Kunde (reine Vorlage) muss speicherbar bleiben (`customer_id` nullable).
- Ownership: fremdes `customer_id` im Request → 403, nie lesen/schreiben.
- `status`-Übergang muss `STATUS_UEBERGAENGE` respektieren (kein Sprung approved→draft ohne outdated).
- JSON-Größe: `parameters`/`geometry` klein halten; kein SceneDocument hier ablegen.
- CSRF fehlt/abgelaufen → 419 sauber im Frontend melden, kein Datenverlust (Entwurf bleibt im UI).

## Abnahmekriterien (für den Evaluator)
1. Migration additiv; `php artisan migrate` legt Tabelle an, `migrate:rollback` entfernt sie sauber.
2. POST speichert Paket; erneuter POST (gleiche uuid) erhöht `revision` auf 2, kein Duplikat.
3. Veraltete base_revision → HTTP 409 (eigener Testfall).
4. Ownership: Nutzer A kann Paket von Kunde B weder lesen noch überschreiben (403) — eigener Test.
5. Ohne `permission:hausplaner.*` → 403 an beiden Routen.
6. Frontend: „Übernehmen" (autark) erzeugt genau EINEN POST, 0 weitere Schreib-Requests; bei Erfolg
   Statuszeile „gespeichert (Rev. N)"; bei 409/419 sichtbare Meldung, Entwurf bleibt erhalten.
7. Keine zweite Wahrheit: der Paket-Status/Revision lebt nur in `configurator_packages`.

## Heimat-App & Ballbesitz
Alles in ticket. Nach Umsetzung durch den Generator: unabhängige Evaluator-Prüfung gegen die
Abnahmekriterien (eigene Tests, echte HTTP-Roundtrips, 409/403-Gegenbeweis). Ballbesitz: Yama.
