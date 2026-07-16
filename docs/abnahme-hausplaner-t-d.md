# Abnahme T-d — Hausplaner in ticket · 5 Browser-Sichtproben

**Rolle:** Yama (Sicht-Abnahme am Bildschirm) · **Datum angelegt:** 2026-07-16
**Vorbedingung:** Evaluator hat den Code-Layer (T-a/T-b/T-c, Commits 76a7dc6 / 7fb76dc /
6c4f6bd) unabhaengig gruen gegeben UND `php artisan migrate` ist gelaufen (3 additive
Tabellen existieren). Erst dann starten diese Sichtproben.

**Regel:** Der Marker „In Abnahme — Browser-Sichtproben ausstehend" in der Insel-View
(`resources/views/admin/hausplaner/objekt.blade.php`, Zeile mit `.hp-abnahme`) wird
**erst entfernt, wenn ALLE fuenf Proben gruen sind** — und die Marker-Entfernung ist eine
eigene, additive Aenderung (kein Beifang). Eine rote Probe blockiert die Marker-Entfernung.

Als Testobjekt eine echte Gebaeudeakte oeffnen (admin/objekte → ein Objekt) — im Text
unten `OBJ` = dessen Objekt-ID (= lead_alternative_adds.id = der Anker).

---

## SP-1 · Einstieg & Landung (Button → Seite)

**Ziel:** Der Hausplaner ist von der Gebaeudeakte aus erreichbar, und die Landung zeigt
das richtige Objekt am richtigen Anker.

**Schritte:** Gebaeudeakte des Testobjekts oeffnen → im Kopf (Aktionsleiste, neben
„Kundenakte oeffnen") den Button **„Hausplaner oeffnen"** anklicken.

**Gruen (muss so sein):**
- Der Button ist als is_admin sichtbar.
- Klick fuehrt auf `/admin/hausplaner/objekt/OBJ`, HTTP 200, kein 403/404/500.
- Oben steht der Objektname bzw. „Objekt #OBJ" und — falls vorhanden — die Adresse.
- Der amber Marker „In Abnahme — Browser-Sichtproben ausstehend" ist sichtbar (er soll
  in dieser Phase noch da sein).
- Im Metablock stehen Dokument-ID, Schema v1, Revision, und **Objekt-Anker
  (alternative_id) = OBJ** (NICHT eine project_id).

**Rot (blockiert):** Button fehlt fuer is_admin; 403/404/500; falsche/leere Objekt-ID im
Metablock; Anker zeigt eine andere Zahl als OBJ.

---

## SP-2 · Editor mountet & Szene ist eingebettet (kein Lade-Fetch)

**Ziel:** Das transplantierte Insel-Bundle laeuft in ticket und liest die Szene aus dem
eingebetteten JSON — nicht ueber einen zusaetzlichen Lade-Request.

**Schritte:** Auf der Hausplaner-Seite die Entwicklertools oeffnen (Konsole + Netzwerk),
Seite neu laden.

**Gruen (muss so sein):**
- Der 2D/3D-Editor mountet sichtbar in `#hausplaner-root` (die Skeleton-Kachel wird
  ersetzt bzw. der Editor erscheint) — es bleibt NICHT bei der reinen Skeleton-Ansicht.
- **Konsole: keine roten Fehler** (insb. kein „Failed to load module", kein three/konva-
  Fehler, kein CSP-Block des Bundles).
- **Netzwerk: KEIN Lade-Fetch der Szene** — die Szene kommt aus
  `<script type="application/json" id="hausplaner-scene">`. `hausplaner.js` und ggf.
  `hausplaner.css` laden mit 200.

**Rot (blockiert):** Editor mountet nicht (nur Skeleton); rote Konsolenfehler; das Bundle
holt die Szene per XHR/fetch nach (widerspricht der ▲K3-Regel „kein Lade-Fetch").

---

## SP-3 · Speichern, Revision & 409-Konflikt (die Mehrbenutzer-Wahrheit)

**Ziel:** Ein Speichern erhoeht die Revision sauber; ein veralteter Zweit-Save wird mit
409 abgewiesen — kein stiller Overwrite.

**Schritte:**
1. Eine kleine Aenderung im Editor machen und speichern.
2. Dieselbe Objekt-Seite in einem **zweiten Tab** oeffnen (laedt die nun hoehere Revision).
3. Im **ersten** Tab (der noch die alte Revision haelt) erneut speichern.

**Gruen (muss so sein):**
- Schritt 1: Speichern 200, Revision zaehlt +1, eine Checksum kommt zurueck.
- Schritt 3: der veraltete Save wird mit **HTTP 409** abgewiesen; die Antwort nennt die
  aktuelle Revision; die UI meldet „zwischenzeitlich veraendert" (kein stilles
  Ueberschreiben der neueren Version).

**Rot (blockiert):** Revision springt nicht/dopplet; der veraltete Save geht mit 200 durch
und ueberschreibt die neuere Version (Datenverlust); 500 statt 409.

---

## SP-4 · Snapshot erstellen, listen, wiederherstellen (append-only)

**Ziel:** Versionsstaende lassen sich sichern und zurueckholen, ohne Historie zu
zerstoeren.

**Schritte:** Snapshot erstellen (optional mit Label) → Snapshot-Liste oeffnen → eine
weitere Aenderung speichern → den zuvor erstellten Snapshot wiederherstellen.

**Gruen (muss so sein):**
- Snapshot erscheint in der Liste mit Revision und (falls gesetzt) Label.
- Wiederherstellen laedt den gesicherten Stand zurueck.
- **Append-only:** vor dem Wiederherstellen wird der aktuelle Stand als Snapshot
  (Grund „vor_wiederherstellung") gesichert — die Historie waechst, nichts wird geloescht.
  Die Revision bewegt sich vorwaerts (kein Zuruecksetzen des Revisionszaehlers).
- Ein Snapshot, der zu einem **anderen** Dokument gehoert, laesst sich hier nicht
  wiederherstellen (404) — nur zum Gegencheck, falls die ID zur Hand ist.

**Rot (blockiert):** Snapshot fehlt in der Liste; Wiederherstellen laedt den falschen
Stand; die Historie wird ueberschrieben statt ergaenzt; fremder Snapshot laesst sich
einspielen.

---

## SP-5 · BearbeitungsSperre-Banner bei Zweitsitzung (weiche Sperre)

**Ziel:** Bearbeiten zwei Personen dasselbe Objekt, warnt ein sichtbares Banner — die
weiche Sperre greift zusaetzlich zur harten 409-Wahrheit aus SP-3.

**Schritte:** Objekt als **User A** oeffnen und offen lassen. Dieselbe Objekt-Seite als
**User B** (zweiter Browser oder Inkognito, anderer Login) oeffnen.

**Gruen (muss so sein):**
- Netzwerk: die Seite pingt `POST /admin/sperre/ping` (system.sperre.ping) im ~30-s-Takt,
  Antwort 200.
- Bei der Zweitsitzung erscheint oben das **amber Banner** „Dieses Dokument wird gerade
  von … bearbeitet" (Name des Erst-Bearbeiters, wenn verfuegbar).
- Kein `route()`-Fehler / kein 500 durch den eingebundenen Sperre-Partial.
- Schliesst eine Sitzung, verschwindet das Banner in der anderen wieder (spaetestens nach
  Verfall ~2 min ohne Heartbeat).

**Rot (blockiert):** kein Ping; kein Banner trotz echter Zweitsitzung; die Seite wirft
wegen des Partials eine Exception (500).

---

## Ergebnis-Notiz (von Yama auszufuellen)

| Probe | Gruen/Rot | Beleg / Beobachtung |
|---|---|---|
| SP-1 Einstieg & Landung |  |  |
| SP-2 Editor mountet / Szene eingebettet |  |  |
| SP-3 Speichern / Revision / 409 |  |  |
| SP-4 Snapshot / Wiederherstellen |  |  |
| SP-5 Sperre-Banner |  |  |

**Alle fuenf gruen →** Marker-Entfernung freigeben (eigener additiver Commit). **Eine rot →**
zurueck an den Generator mit dem konkreten Fehlerbild; Marker bleibt stehen.
