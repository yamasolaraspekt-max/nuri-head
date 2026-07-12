# Rückfall- & Archiv-Regeln (Original-Erhalt bei Code-Änderungen)

**Stand:** 2026-07-11 · **Geltung:** DAUERHAFT und für JEDE Instanz/jeden Agenten in diesem Repository. Ergänzt die `bauordnung.md` (§4 Bau-Prozess) und `agents/04`-Verbote. Bei Konflikt gilt BETRIEBSORDNUNG/CLAUDE.md.

> **Kurzregel:** Optimieren ja. Überschreiben ohne Rückfallpfad nein. Löschen nein. Original nachvollziehbar erhalten.

Code darf **später** geändert/optimiert/verknüpft/ersetzt werden — **nur** wenn ein Kapitel/Paket dafür freigegeben ist. Das **Original muss nachvollziehbar erhalten** bleiben (Notfall-Rückgriff).

## 1. Kein endgültiges Löschen
- **Kein eigenständiges Löschen** von Code, Views, Services, Controllern, Imports, Tabellenlogik oder alten Dateien.
- **Kein `git rm` ohne ausdrückliche Yama-Freigabe.**
- **Keine destruktive Migration** (DROP/DELETE/UPDATE auf Bestand) ohne gesonderte Freigabe (deckt sich mit DAUERDIREKTIVE: UPDATE/DELETE = eigener beauftragter Posten).

## 2. Vor jeder größeren Änderung Original sichern
Wenn eine Datei stark umgebaut oder alte Logik ersetzt wird — **vorher**:
1. **Nutzung prüfen** (Aufrufer/Referenzen/Routen belegen).
2. **Beschreiben, was ersetzt wird.**
3. **Rückfallpfad festlegen.**

## 3. Original aufbewahren
- **Variante A — Git reicht** (kleine, klare Änderung): sauberer path-scoped Commit, vorher/nachher nachvollziehbar, Tests grün.
- **Variante B — Archivkopie nötig** (großer Umbau, alte Logik, Wizards, Importe, Rechenkerne, Views): Original **zusätzlich** in den Archivordner sichern.

**Archivordner:** `_archiv/`
**Struktur:** `_archiv/YYYY-MM-DD/<kapitel-oder-thema>/<originalpfad>`
Beispiel: `_archiv/2026-07-11/wp-wizard/resources/views/admin/offer/offer/configuration/wp/index.blade.php`

## 4. Archiv mit Manifest
Jede Archivierung braucht `_archiv/YYYY-MM-DD/<thema>/MANIFEST.md` mit:
- Warum archiviert?
- Originalpfad
- Neuer/aktiver Pfad oder Ersatzlogik
- Welche Nutzung wurde geprüft?
- Welche Tests wurden ausgeführt?
- Wie kommt man im Notfall zurück?
- Wer hat freigegeben?

## 5. Nicht alles blind archivieren
Kein Kopieren bei kleinen Änderungen. Archiv **nur** bei: großem Umbau · alte Logik wird ersetzt · Datei wird aus aktivem Pfad entfernt · Wizard/Rechner/Import wird abgelöst · Risiko, dass wir später nachsehen müssen.

## 6. Wenn unsicher
Nicht löschen · Original behalten · in **`docs/ablage-kandidaten.md`** markieren · Yama fragen.

## 7. Umsetzungspaket muss Rückfallpfad nennen
Jedes Paket benennt: **Was wird geändert? · Was bleibt erhalten? · Wo liegt das Original? · Wie testen wir? · Wie gehen wir zurück, wenn es nicht passt?**

---
*Hinweis: `_archiv/` und `docs/ablage-kandidaten.md` werden erst bei der ersten realen Archivierung/Kandidaten-Markierung angelegt — diese Regel ist read-only bis ein freigegebenes Paket sie auslöst.*
