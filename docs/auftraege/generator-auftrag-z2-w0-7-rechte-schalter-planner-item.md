# Z2-W0-7 · Rechte-Schalter „alle für alle" + Permission-Item `Planner` (Yamas Entscheidung 21.08.)

```yaml
zustand: ENTWURF
welle: 0 — läuft VOR W0-1 (damit die Tests der Tore von Anfang an beide Schalterstellungen kennen)
basis_sha: 114b98f6
herkunft: docs/regelwerk/ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md (Yama 21.08., zweimal bestätigt) · Y-6, Y-9
spur: A — Autorisierung, LIVE-Daten, Config
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
1. **Ein Schalter:** `config('rechte.alle_fuer_alle')` (env `RECHTE_ALLE_FUER_ALLE`, Default **false**
   im Code, **true** in Yamas lokaler `.env`) — ist er wahr, liefert `User::hasPermission()` immer
   `true`. Damit passen `permission:`-Middleware (`CheckUserPermission`), Blade-Sichtbarkeit und alle
   Aufrufer ohne weitere Änderung.
2. **Permission-Item `Planner`** (read/add/update/delete) existiert im Rechtesystem — angelegt über
   den vorhandenen Weg (Migration/Seeder-Muster der bestehenden Items, **additiv**), damit die 61
   `/planner/*`-Routen und die API ein Item referenzieren können (W0-5 Stufe 2 / Posten 0.7).

## Ist-Beleg
`hasPermission()` `app/Models/User.php:56-74` (`user_rolls`, `where('item_id', $item)`);
`isSuperAdmin()` `:51-54`; `CheckUserPermission` Middleware; Items im Bestand: Product, Customer,
Employee, Problem, Hausplaner, Finance, Users, Inquiry (`grep -rhoE "permission:[A-Za-z_]+"`); **kein**
`Planner`. Nebenbefund `permission:hausplaner` 1× klein — Collation-Messung in W0-4.

## Scope · Dateien
- `config/rechte.php` (neu, eine Taste) · `.env.example` (Eintrag mit Kommentar + Verweis auf das
  Entscheidungsblatt) · `app/Models/User.php::hasPermission()` (eine vorgeschaltete Zeile, Kommentar
  mit Verweis).
- Item `Planner`: Migration/Seeder nach Hausmuster (wie das jüngste Item angelegt wurde — **erst
  messen**, welches Muster gilt), additiv, idempotent.
- Tests `tests/Feature/Security/RechteSchalterTest.php`: Schalter **false** → Nutzer ohne Recht 403
  auf einer gegateten Route (z.B. `hausplaner.*`), mit Recht 200; Schalter **true** → derselbe Nutzer
  ohne Recht 200; `isSuperAdmin()` unverändert (kein Nicht-Admin wird Admin). Item `Planner` existiert
  nach Migration. **Nur `ticket_testing`.**
**Nicht-Ziele:** kein `is_admin=1` für Nutzer; keine `user_rolls`-Massendaten; keine Änderung an
Ownership-/Integritätsprüfungen (W0-2 Schreibpfad, W0-5 A-4 bleiben); keine Produktionsdaten.

## Kanten
- Die Tore aus W0-1/2/5 müssen **in beiden Schalterstellungen** getestet werden — sonst prüft kein
  Test mehr, ob ein Tor überhaupt schließt (Schalter true in der Testumgebung würde alles grün
  färben). Testumgebung: Default **false**; der true-Fall wird je Test explizit gesetzt.
- `hasPermission` wird evtl. mit `$item` in verschiedenen Schreibweisen gerufen — der Schalter
  greift davor, die Collation-Frage bleibt für den false-Fall bestehen (W0-4 misst).
- Cache: gibt es einen Rechte-Cache (Session/Redis)? **Messen**; wenn ja, invalidieren beim Schalterwechsel.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Schalter false → ohne Recht 403, mit Recht 200 (gegatete Bestandsroute) | Schalter | *n.U.* | Testnamen |
| B: Schalter true → ohne Recht 200; `isSuperAdmin()` für Nicht-Admin weiter false | Schalter | *n.U.* | Testnamen |
| C: Item `Planner` mit vier Aktionen existiert nach Migration (SQL-Probe `ticket_testing`) | Item | *n.U.* | Rohausgabe |
| D: `.env.example` trägt `RECHTE_ALLE_FUER_ALLE=false` + Kommentar; Yamas lokale `.env` steht auf true (Yama/Dirigent setzt sie, nicht der Generator — `.env` ist keine Repo-Datei) | Doku | *n.U.* | Zitat |
| E: `git diff --numstat`: nur config/rechte.php, .env.example, User.php, Migration/Seeder, Testdatei | Grenze | *n.U.* | Rohausgabe |

**P1-Kriterium B ist vor dem Bau wirksam rot** (kein Schalter existiert; Nutzer ohne Recht → 403).

## Rückweg
`RECHTE_ALLE_FUER_ALLE=false` → Tore wirken sofort. Code-Commit zurückdrehbar; Migration additiv
(Item bleibt, schadet nicht).
