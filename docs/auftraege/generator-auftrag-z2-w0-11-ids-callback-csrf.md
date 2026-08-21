# Z2-W0-11 · `POST ids/callback`: CSRF-Ausnahme mit Auto-Produktanlage schließen, tote Ausnahmen aufräumen

```yaml
zustand: ENTWURF
welle: 0 / Phase 4 — Stopp-Regel-Kandidat (Integrität Produktstamm)
basis_sha: ae7cee9d
herkunft: Messung A-10 (docs/backlog/inventur-2026-08-21-z2-folge.md)
spur: A — CSRF, Schreibpfad, Produktstamm
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Abgrenzung (Restpunkt §265, Planner 21.08.): zwei Teile, verschieden abhängig
| Teil | Inhalt | hängt am Operanden? | läuft |
|---|---|---|---|
| **A — unabhängig, JETZT** | (A1) `uid` kommt nicht mehr frei aus der Query — Zuschreibung aus Session/auth; (A2) die **fünf toten Ausnahmen** in `VerifyCsrfToken::$except` entfallen (treffen keine Route, schützen niemanden) | nein | dieser Auftrag, sofort baubar |
| **B — operandenabhängig** | CSRF-Schutz des echten IDS-Rückwegs (externer Shop postet zurück): signierter/nonce-gebundener State statt Ausnahme | **ja — Y-12: wie liefert der IDS-Shop zurück?** (Frage an externen Partner, im Haus nicht messbar) | eigener Folgeauftrag Z2-W0-11b nach Y-12; bis dahin bleibt die Ausnahme für `ids/callback` (mit A1 ist die Fremdzuschreibung bereits weg, der Rest-Schaden ist ein unerwünschter Import unter eigener `uid`) |

## Ziel (Teil A)
`uid` wird nie aus der Query übernommen (Session/auth bindet den Importeur); die fünf toten
CSRF-Ausnahmen sind entfernt; die verbleibenden Ausnahmen treffen reale Routen (route:list-Abgleich).
**Teil B** (State/Nonce für den IDS-Rückweg) folgt nach Y-12 als Z2-W0-11b.

## Ist-Beleg
`VerifyCsrfToken.php:14-29` Ausnahme `ids/callback`; Route hinter `Authenticate`;
`IdsController@callback:33-78` legt `ImportedIdsItem` an, `?auto=1` → `autoPromoteItem():89ff` →
`Distributor::firstOrCreate('GC Online')` + Produktanlage über `ProductIdentityService`; `uid` aus Query
(`:56`); keine Signatur, kein Nonce. Tote Ausnahmen: `api/reminder/*/status`, `api/due-personal-notes`
(reale Routen ohne `api/`-Präfix, `routes/web.php:4727-4728`), `ids/search/callback`, `/ids/receive`,
`/ids/callback` (Dublette). **Operand: wie liefert der IDS-Shop zurück?** (Form-POST ohne Session?
Dann ist (a) der Weg: State-Token beim Absprung erzeugen, beim Rückweg prüfen.)

## Scope · Dateien
`VerifyCsrfToken.php` (tote Einträge raus; `ids/callback` nur, wenn (a) gebaut), `IdsController`
(State/Nonce prüfen; `uid` aus Session/State statt Query), Absprungstelle des IDS-Flows (State erzeugen)
— **erst messen**, wo der Absprung liegt; Test: Rückweg ohne/mit falschem State → 4xx, keine Items;
mit gültigem State → wie bisher. **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung am IDS-XML-Parsing; `ai/chats/{chat}/message` und
`supplier-connectors/.../return` sind **eigene** Posten (niedriger / by design).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Auto-Submit-POST ohne gültigen State → 4xx, 0 neue `imported_ids_items` | Schutz | *n.U.* | Testname + DB-Probe |
| B: Regulärer Rückweg mit State → wie bisher (Items angelegt) | Funktion | *n.U.* | Testname |
| C: `uid` wird nicht mehr aus der Query übernommen (grep + Test) | Zuschreibung | *n.U.* | grep |
| D: `VerifyCsrfToken::$except` enthält die fünf toten Einträge nicht mehr; die verbleibenden treffen reale Routen (route:list-Abgleich) | Aufräumen | *n.U.* | Rohausgabe |

**P1-Kriterium A ist vor dem Bau wirksam rot** (Ausnahme aktiv, `uid` aus Query).

## Rückweg
Ein Commit, zurückdrehbar; keine Daten.
