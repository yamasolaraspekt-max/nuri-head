# Z2-W0-1 · Gebäudeakte `/objekte/*` hinter `permission:Customer,read` — das Menü sagt es schon, die Route nicht

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, LIVE) — VORRANG vor allem Welle-1-Rest
basis_sha: 7a82ecfb            # Messstand der Security-Gegenprobe 21.08.
herkunft: Befund S-5 (docs/backlog/inventur-2026-08-21-z2.md), BESTÄTIGT durch security-reviewer (opus) 21.08.
spur: A — Autorisierung, LIVE-Daten (~3000 Kunden); voller Zyklus Plan-Prüfer → Generator → Evaluator
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
`objekte.index`, `objekte.akte/{alternative}`, `objekte.auslegung` sind nur mit `Customer,read`
erreichbar — exakt das, was die Sidebar (`'permission' => 'Customer'`) bereits behauptet.

## Ist-Beleg (Gegenprobe, `route:list` autoritativ)
`routes/web.php:817-819` im Block `['web','auth']` ab `:793`; `route:list`: alle drei Routen
MW `['web', Authenticate]`, **kein** `permission`. `ObjektakteController.php` (200 Z., angelegt
`96904b5e` 16.07. — **nach** der Sicherheitsrunde 10.07.): kein `authorize`/`hasPermission`/
`__construct`. `index()` (`:53-58`) paginiert ALLE `LeadAlternativeAdd` mit Kundenname/Firma/
Kundennummer; `scopeGebaeudeSuche` (`LeadAlternativeAdd.php:426-444`) LIKE-Filter über Kundenname
ohne Bindung → **durchsuchbarer Vollexport des Kundenstamms für jeden Login.** Entschärfung:
`auslegung()` ist schreibfrei (0 save/update/create/delete) — Offenlegung, keine Integrität.
Widerlegungsversuche der Gegenprobe alle negativ: kein Gate::before (0 Treffer), keine Policy für
`LeadAlternativeAdd` (AuthServiceProvider 5 Policies, keine passende), kein globaler Scope,
Kernel-Middleware ohne auth/permission, kein Route-Cache. CSRF greift, schützt aber nur vor
Fremdseiten, nicht vor dem eingeloggten Innentäter.
**Reproduktion:** Nutzer ohne Customer-Recht → `GET /objekte?q=a` → 200 mit Kundenliste.

## Scope · Dateien
- `routes/web.php:817-819`: `->middleware('permission:Customer,read')` je Route (Muster
  `routes/web.php:4988`), ODER als Gruppe um die drei Routen.
- `tests/Feature/Security/ObjektakteGateTest.php` (neu), Muster
  `tests/Feature/Security/CustomerPermissionGateTest.php`: Nutzer ohne `Customer` → 403 auf allen
  drei Routen; Nutzer mit `Customer,read` → 200. **Nur gegen `ticket_testing`.**
**Nicht-Ziele:** kein neues Permission-Item (Customer existiert); keine Änderung an
`ObjektakteController`-Logik oder -Ausgabe; keine Sidebar-Änderung; keine Migration.

## Kanten
Bestandsnutzer mit Customer-Recht dürfen nichts verlieren (Test: 200). Die Schreibweise des Items
ist `Customer` (Großschreibung, 16× im Bestand; Nebenbefund „hausplaner" kleingeschrieben → W0-4).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `php artisan route:list --name=objekte` zeigt `permission:Customer,read` auf allen 3 Routen (Rohausgabe) | Gate | *n.U.* | route:list |
| B: Feature-Test „ohne Customer → 403" auf index/akte/auslegung grün | Test | *n.U.* | Testname + Zähler |
| C: Feature-Test „mit Customer,read → 200" grün (kein Verlust) | Schutz | *n.U.* | Testname |
| D: `git diff --numstat` zeigt außer routes/web.php + Testdatei keine Datei | Grenze | *n.U.* | Rohausgabe |

**P1-Kriterium B ist vor dem Bau wirksam rot** (route:list: kein permission; reproduziert 200).

## Rückweg
Ein Commit, Route-Middleware + Test, zurückdrehbar; kein Schema, keine Daten. Entdeckung einer
Fehlsperre: Customer-berechtigte Nutzer bekämen 403 — Kriterium C fängt es.
