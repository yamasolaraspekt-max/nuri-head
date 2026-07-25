# ⇒ GENERATOR-AUFTRAG AUF-53 — Das Import-Recht

**Vom:** Planner · **25.07.2026** · **Entscheidung Yama, 25.07.:** *„eigenes Recht"* (aus AUF-41).

**Vorher gelesen:** HEAD `1043435` · `git log -5` · Tafelzeile AUF-53 (§3a) ·
`app/Http/Middleware/CheckUserPermission.php` · `app/Models/User.php:56-74` (`hasPermission`) ·
`database/migrations/2023_06_14_131732_create_user_rolls_table.php` ·
`app/Http/Controllers/User/UserRollController.php:349` · `routes/web.php:4989-5009` ·
`app/tools/werkzeugVertrag.ts` (die acht Verträge mit `permission.import`).

**Alle Zahlen unten sind am 25.07. gemessen** — nach der Regel, die ich mir nach AUF-45 gegeben habe:
jeder Auftrag mit einer Zahl im Kriterium trägt die Messung mit, aus der sie stammt.

---

## 1. Die Falle, die den Auftrag umbaut

**Der naheliegende Weg wäre `permission:Hausplaner,import`. Er würde nichts schützen.**

`User::hasPermission()` (Z. 56-74) bildet die Aktion auf **genau vier feste Spalten** ab:

```php
$column = match ($action) {
    'read', 'view', 'show', 'index' => 'is_read',
    'add',  'create', 'store'       => 'is_add',
    'update','edit'                 => 'is_update',
    'delete','destroy'              => 'is_delete',
    default                         => 'is_read',      // ← hier landet 'import'
};
```

Eine unbekannte Aktion fällt in `default` und wird zu **`is_read`**. Eine Route mit
`permission:Hausplaner,import` sähe geschützt aus und wäre für **jeden Leseberechtigten offen**.
Das ist schlimmer als kein Recht, weil es Sicherheit vortäuscht.

## 2. Der Weg, den die Messung zeigt: `Hausplaner,add`

| gemessen | Ergebnis |
|---|---|
| Spalten in `user_rolls` | `is_read` · `is_update` · `is_delete` · **`is_add`** — vier, fest |
| Item `Hausplaner` in der Rechteverwaltung | **vorhanden**, `UserRollController:349` („Gebäudeplaner (Hausplaner)") |
| Routen, die heute `Hausplaner,add` nutzen | **0** |
| Werkzeuge mit `permission.import` im Vertrag | **8** (`ImportFileCommand`, `ImportImageCommand`, `RecognizeCommand`, `ApproveDetectionCommand`, `CalibrateCommand`, `CropCommand`, `SetNorthCommand`, `AiAssistantCommand`) |

**`Hausplaner,add` ist frei, vorhanden und unbenutzt.** Es ist ein **eigenes** Recht, getrennt von
`update` — genau das, was Yama entschieden hat — und es braucht:

- **keine Migration** (die Spalte existiert seit 2023),
- **keine Änderung an der Rechteverwaltung** (das Item existiert, die Spalte wird dort schon gepflegt),
- **keine Änderung an `hasPermission`** (`'add'` ist bereits abgebildet).

**Das ist die ganze Ersparnis:** Aus einem Posten mit Migration an einer Tabelle, die das gesamte CRM
trägt, wird eine Zuordnung. Der Tor-1-Charakter bleibt trotzdem — `routes/` wird angefasst.

## 3. Was gebaut wird

1. **Die Vorbedingung `permission.import` bildet auf `Hausplaner,add` ab.** In der Zuordnungstabelle
   aus AUF-36 (`app/tools/vorbedingungen.ts`), dort wo heute `permission.edit → Hausplaner,update`
   steht. **Eine Zeile.**
2. **`HausplanerApp` reicht das Recht durch.** Wo heute `permissions: [RECHT_BEARBEITEN]` steht
   (`HausplanerApp.tsx:370`), kommt das Import-Recht dazu — **nur wenn der angemeldete Nutzer es
   wirklich hat**. Woher die Insel das erfährt, ist §4.
3. **Import-Routen, falls und sobald es sie gibt:** `middleware('permission:Hausplaner,add')`.
   **Heute gibt es keine** — deshalb ist dieser Punkt vorbereitet, nicht ausgeführt (§5).

## 4. Die offene Stelle, die gemessen und benannt gehört

Die Insel kennt heute **ein** Recht: `RECHT_BEARBEITEN`. Woher es kommt und ob es aus dem
angemeldeten Nutzer stammt oder fest gesetzt ist, **ist vor dem Bauen zu messen**. Ergibt die
Messung, dass die Rechte **nicht** aus dem Nutzer kommen, dann ist das ein eigener Posten —
**zurückgeben, nicht nebenbei bauen.** Ein Recht, das die Insel sich selbst erteilt, schützt nichts.

*(Ich habe diese Stelle bewusst nicht selbst gemessen: sie liegt in der Blade/Mount-Naht, und der
Generator sieht beim Bauen mehr davon als ich beim Lesen. Aber sie **muss** im Bericht stehen.)*

## 5. Was **nicht** gebaut wird

- **Keine neue Aktion, keine neue Spalte, keine Migration.** Wer `import` als fünfte Aktion einführt,
  ändert `hasPermission` und eine Tabelle, an der das ganze CRM hängt — für einen Namen.
- **Keine Import-Funktion.** Dieser Posten vergibt ein Recht; er baut kein einziges der acht
  Werkzeuge. Die bleiben gesperrt — nur mit einem Grund, der jetzt erfüllbar **ist**.
- **Kein Werkzeug wird freigeschaltet, das das Recht nicht hat.** Die Vorbedingung bleibt eine
  Vorbedingung; sie geht bei einem berechtigten Nutzer von selbst auf grün, bei allen anderen nicht.
- **Keine Rechtevergabe.** Wer `Hausplaner,add` bekommt, entscheidet Yama in der Rechteverwaltung —
  nicht der Code.

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Keine Migration, keine Änderung an `hasPermission`:** `git diff` zeigt null Zeilen in
   `database/migrations/` und in `app/Models/User.php`.
4. **`import` erscheint nirgends als Aktion:** `grep -r "permission:Hausplaner,import"` = **0 Treffer**.
   *(Das ist das eigentliche Sicherheitskriterium dieses Postens.)*
5. **Die acht sind zugeordnet:** Test belegt, dass genau die acht Verträge mit `permission.import`
   auf `Hausplaner,add` abbilden — **acht, nicht sieben, nicht neun**.
6. **Ohne Recht bleibt gesperrt:** Test mit einem Kontext ohne `Hausplaner,add` ⇒ alle acht gesperrt,
   Grund unverändert „Keine Berechtigung zum Importieren."
7. **Mit Recht wird entsperrt — und nur diese acht:** Test mit dem Recht ⇒ die acht sind nicht mehr
   an `permission.import` gescheitert, **und die Zahl der insgesamt gesperrten Werkzeuge sinkt um
   genau die Menge, die allein daran hing**. Die Zahl wird gemessen, nicht angenommen.
8. **Herkunft der Rechte gemessen und im Bericht benannt** (§4) — auch wenn das Ergebnis „kommt heute
   nicht aus dem Nutzer" lautet.
9. **Mutations-Gegenbeweis:** die Zuordnung auf `Hausplaner,update` verfälschen ⇒ mindestens ein Test
   rot. Zahl nennen.
10. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
11. **Klassifikation: `sichtbar`** für Nutzer mit dem Recht — Sichtprobe mit und ohne, damit belegt
    ist, dass das Recht wirklich unterscheidet.

## 7. Vorbedingung zum Ziehen

**Yamas Freigabe zum Bauen.** Er hat das Ziel entschieden (eigenes Recht) — der Bau berührt `routes/`
und die Rechte-Naht und bleibt Tor 1. **Die Migration entfällt** (§2), damit ist der Eingriff
erheblich kleiner als bei der Anlage des Postens angenommen; die Freigabe bleibt trotzdem seine.
