# VOTUM Z2-W0-7 — Rechte-Schalter + Item `Planner` (S-1)

**evaluator · 22.08.2026 · Auftrag `ABNAHME-evaluator-Z2-W0-7` gen 13 · Lease-Token 1**
**Bau `5831c06a` · Integrationsstand `aeae9381`**

## Ergebnis: ABGENOMMEN — 5 von 5 Kriterien erfüllt

| # | Verlangt | Beleg (selbst gemessen) |
|---|---|---|
| **A** | Schalter `false` → ohne Recht 403, mit Recht 200 | beide Tests grün, und die Mutation trifft sie (unten) |
| **B** | Schalter `true` → ohne Recht 200; `isSuperAdmin()` für Nicht-Admin weiter false | beide Tests grün; der Schalter sitzt **vor** `isSuperAdmin()` und lässt es unberührt |
| **C** | Item `Planner` mit vier Aktionen existiert nach Migration (SQL-Probe) | SQL-Rohausgabe unten; die vier Aktionen sind **Spalten** |
| **D** | `.env.example` trägt `RECHTE_ALLE_FUER_ALLE=false` + Kommentar | `.env.example:63-66`, drei Kommentarzeilen + der Wert |
| **E** | `git diff --numstat`: nur die fünf genannten Dateigruppen | exakt sechs Dateien, nichts außerhalb |

## C — die SQL-Probe, und warum „vier Aktionen" bereits erfüllt ist

```
SELECT id, item, created_at FROM ticket_testing.user_roll_items WHERE item = 'Planner';
+----+---------+---------------------+
| id | item    | created_at          |
+----+---------+---------------------+
|  1 | Planner | 2026-08-22 13:20:26 |
+----+---------+---------------------+
```

Meine erste Probe lief ins Leere, weil ich die Tabellennamen **geraten** hatte
(`permission_items`/`permission_actions`). Nach dem Lesen der Migration: die Tabelle heißt
`user_roll_items`, das Feld `item`.

**Die vier Aktionen sind keine anzulegenden Zeilen, sondern Spalten** — `SHOW COLUMNS FROM
user_rolls` zeigt `is_read`, `is_update`, `is_delete`, `is_add`, und `hasPermission()` bildet sie
über ein `match` ab (`read/view/show/index → is_read` usw.). Sie existieren also strukturell,
sobald das Item da ist. Die Migration sagt das auch selbst: *„Es werden keine Rechte vergeben — nur
das Item angelegt, damit es referenzierbar ist."* **Kein Mangel, sondern so gebaut.**

## E — die Grenze, mit dem Messbefehl des Blatts

```
5  0  .env.example
10 0  app/Models/User.php
34 0  config/rechte.php
41 0  database/migrations/2026_08_21_210000_add_planner_permission_item.php
25 8  tests/Feature/Security/ObjektakteGateTest.php
90 0  tests/Feature/Security/RechteSchalterTest.php
```
`-- resources/ app/Http/` → **leer**.

## Gegen-Beweis: die Tests hängen am Schalter, nicht an der Kulisse

Zwei Mutationen im Wegwerf-Klon, in **beide** Richtungen:

| Mutation | rot geworden | übrige |
|---|---|---|
| Schalter liest konstant `false` | `schalter an ohne recht erlaubt`, `schalter an macht keinen admin` | 3 grün |
| Schalter liest konstant `true` | `schalter aus ohne recht verboten` | 4 grün |

Jede Richtung trifft genau die Tests ihrer Schalterstellung. `item planner existiert` bleibt in
beiden Fällen grün — richtig, es hängt an der Migration, nicht am Schalter. Klon nach jeder
Mutation zurückgesetzt (`git status` leer), am Bestand nichts verändert.

**Eine grobe erste Mutation habe ich verworfen:** das Auskommentieren des `if` zerbrach die Methode
syntaktisch und ließ *alle vier* Tests fallen — das hätte nichts belegt außer meiner eigenen
Ungenauigkeit. Erst die gezielte Ersetzung des Konfigurationsaufrufs ist ein Gegen-Beweis.

## Grundmenge: alle Planner-Routen, keine ohne Messung

`php artisan route:list --path=planner --json`, Stand `71a75985`:

```
Gesamt 81  ·  /planner/* (web) 61  ·  api/planner/* 20
web /planner/*   auth=61
api/planner/*    auth=19 · ohne=1   (POST api/planner/auth/token — der Login, muss offen sein)
mit permission:  0 von 81
```

**Die Zahl 61 des Befunds S-1 bestätigt sich zeichengenau.** Auch hier ein eigener Fehler zuerst:
meine erste Klassifikation meldete „81 ohne Middleware", weil ich nach kleingeschriebenem `auth`
suchte — die Middleware heißt `Authenticate:sanctum`.

## Zwei Befunde ohne Kriterienwirkung

1. **Keine einzige Planner-Route trägt `permission:`.** Das Item ist angelegt, aber noch nirgends
   referenziert — der Schalter schützt heute also keine Planner-Route, sondern wirkt an den
   gegateten Bestandsrouten. Das ist **so gewollt und im Bau ausdrücklich gesagt**; ich nenne es,
   damit niemand aus „Item angelegt" auf „Planner ist gegatet" schließt.
2. **Kein Rechte-Cache** — die Kante des Blatts verlangt das zu messen. `hasPermission()` fragt
   direkt die Datenbank (`->exists()`), kein `Cache::`, keine Session. Der eine Treffer meiner
   Suche war `'remember_token'` im `$hidden`-Array — eine Teilstring-Falle, kein Cache. **Damit
   entfällt die Invalidierung beim Schalterwechsel.**

## Was ich nicht gemessen habe

Der zweite Teil von **D** („Yamas lokale `.env` steht auf true") ist im Kriterium selbst dem
Dirigenten/Yama zugewiesen, nicht dem Generator. Ich habe `.env` **nicht gelesen** — der Zugriff
auf die Geheimnisdatei wurde von der Umgebung abgelehnt, und ich habe ihn nicht umgangen. Der
versionierte Teil des Kriteriums ist erfüllt; den lokalen Wert kann nur Yama bestätigen.

## Ball

**Dirigent** — Z2-W0-7 abgenommen.
