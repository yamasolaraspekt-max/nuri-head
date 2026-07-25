# SPEZIFIKATION — „Hausplaner-Transplantation playground → ticket" (Planner)

**Datum:** 2026-07-16 · **Rolle:** Planner · **Heimat-App: ticket (LIVE, höchste Sorgfaltsstufe).**
**Grundhaltung:** streng ADDITIV (DAUERDIREKTIVE), kein Umbau bestehender Ketten, kein UPDATE als
Beifang. Der Hausplaner landet zunächst **„in Abnahme"** (sichtbarer Marker), bis die fünf
P1-Browser-Sichtproben grün sind — die Code-Schicht ist evaluator-grün, die gerenderte Optik nicht.

## 1 · Kern-Entscheidungen (nicht verhandelbar für den Generator)

| # | Festlegung |
|---|---|
| **▲T1 Anker = Objekt** | In ticket verankert der Hausplaner am **Objekt** (`lead_alternative_adds`, `alternative_id`) — NICHT an `project_id` wie im playground. Grund: das Objekt ist die kanonische Gebäudeakte (LeadAlternativeAdd, ~90 Felder), an der schon Grundriss/Anforderungsprofil hängen. `hausplaner_documents.alternative_id` **unique** = genau ein Plan je Objekt. Der playground-Code (project_id) wird beim Portieren an dieser einen Stelle umgestellt. |
| **▲T2 Additiv & isoliert** | Drei neue Tabellen `hausplaner_documents/_snapshots/_catalog_items`, FK auf `lead_alternative_adds` nur wenn die Tabelle existiert (defensiv). KEIN Eingriff in bestehende Tabellen. Neue Migration mit frischem Zeitstempel (nie umbenennen). |
| **▲T3 Domäne 1:1, nur Anker anders** | Models/Actions (`HausplanerDocument/Snapshot/CatalogItem`, `SpeichereHausplanerDokument` mit lockForUpdate→409, `ErstelleLeeresSzenenDokument`, `StelleSnapshotWieder`) werden 1:1 portiert; einzige Änderung: `project_id`→`alternative_id`. Namespace `App\Domain\Hausplaner\*` (wie playground). Die geprüfte P0-Logik (Revision/Checksum/Undo) bleibt unangetastet. |
| **▲T4 Insel-Bundle** | Das fertige Editor-Bundle (`hausplaner.js`, three+konva, im Cloud-Sandbox gebaut) wird nach `ticket/public/hausplaner/` kopiert — dieselbe Insel wie playground, kein React in der ticket-SPA. Blade-Hülle lädt es nur, wenn die Datei existiert (ehrlicher Zustand sonst). |
| **▲T5 BearbeitungsSperre** | Der Hausplaner nutzt die **vorhandene** ticket-`BearbeitungsSperreService` (Bereich `hausplaner`, id = alternative_id) — je Objekt ein Bearbeiter (Yamas Regel). Zusätzlich die harte Revision→409 aus P0. Zwei Schutzschichten, additiv. |
| **▲T6 Rechte** | `permission:hausplaner.view / hausplaner.manage` wie playground; Super-Rollen passieren. Routen web (Session+CSRF, JSON), kein api-Prefix. |

## 2 · Abgrenzung zur bestehenden Dach-Insel

Die frühere Dach-Insel (`public/planer/`, Route `hausplaner.dachplaner`, Prototyp-Banner) bleibt
**unangetastet** — sie ist ein separater Prototyp. Der neue Hausplaner ist eine **neue** Route
(`hausplaner.objekt.seite` o. ä.) am Objekt. Der spätere Dach-Andock (eigene Spec) führt beide zusammen.

## 3 · Kantenliste

1. Objekt ohne Plan ⇒ Erstaufruf legt leeres SceneDocument an (ErstelleLeeresSzenenDokument), rendert.
2. `lead_alternative_adds` fehlt/leer ⇒ Migration setzt FK nur bei vorhandener Tabelle (defensiv), 404 bei unbekanntem Objekt.
3. Zweiter Bearbeiter am selben Objekt ⇒ BearbeitungsSperre-Banner (weich) + Revision-409 (hart).
4. Bundle fehlt ⇒ Blade zeigt Skeleton/„in Abnahme"-Hinweis statt 404-Konsole.
5. Alt-Grundriss am Objekt (raum_geometrien/gebaeude_geometrie) ⇒ bleibt Legacy-lesbar, wird NICHT überschrieben (Hausplaner ist eigene Zeichen-Wahrheit; Projektion später).

## 4 · Arbeitspakete (je einzeln prüfbar, mit Stopp)

- **T-a Foundation:** additive Migration (Anker alternative_id) + 3 Models + 3 Actions (portiert, Anker umgestellt) — **Stopp: Schema-Review an Yama** (eine Seite: Tabellen + Anker + FK-Regel).
- **T-b Landung:** Route + Controller + Blade-Hülle am Objekt, „in Abnahme"-Marker, Bundle-Kopie; read-only lauffähig (Szene lädt).
- **T-c Editor scharf:** Bundle-Verdrahtung (Speichern/Snapshots/409), BearbeitungsSperre-Include.
- **T-d Abnahme:** Evaluator-Durchgang (Contract-Tests nach ticket portiert, Anker-Variante) + die 5 P1-Browser-Sichtproben am ticket-Objekt.

## 5 · Abnahmekriterien

1. Migration additiv, `migrate` grün, keine Bestandstabelle berührt (Diff = 3 neue Tabellen).
2. `hausplaner_documents.alternative_id` unique; zweites Dokument je Objekt unmöglich.
3. Speichern mit alter base_revision ⇒ 409, Revision+Checksum unverändert (Contract-Test, Anker-Variante).
4. Zweiter Bearbeiter ⇒ Sperre-Banner sichtbar.
5. Ohne `hausplaner.view` ⇒ 403 auf allen Routen; Gast ⇒ Redirect.
6. Wächter: volle ticket-Suite grün (704+).
7. Repo-Aufsicht: git 4 Apps sauber.

## 6 · Übergabeformat

umgesetzt/offen/Ballbesitz; Generator meldet „umgesetzt", nie „grün". Hartes Gate für „produktiv
freigegeben" (Marker entfernen): grüne P1-Browser-Sichtproben + grüner Evaluator-Durchgang T-d.
