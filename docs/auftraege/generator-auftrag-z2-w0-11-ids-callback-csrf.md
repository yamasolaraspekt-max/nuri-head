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
| **A — unabhängig, JETZT (ehrlich begrenzt, Planner 21.08. nach Yamas Maßstab Nr. 8)** | (A1) `uid` aus der Query wird **nicht mehr gelesen** — der Import läuft unter dem authentifizierten Nutzer; **KEIN Persistenzversprechen**: `ImportedIdsItem` speichert keine Urheberschaft (`user_id` wird nicht persistiert — die „bestehende Fremdzuschreibung" war deshalb nicht wirksam, der Befund A-10 ist in diesem Punkt berichtigt); die Schutzwirkung von A1 ist die **Session-/Request-Bindung**, nicht eine gespeicherte Zuschreibung; (A2) die **fünf toten Ausnahmen** in `VerifyCsrfToken::$except` entfallen | nein | dieser Auftrag; **Kriterium A/C dürfen NICHT grün werden, weil ein nicht speicherbares Feld an `create()` übergeben wird** — sie messen Ablehnung/Ignorieren, nicht Speicherung |
| **C — Urheberspalte (separater Modell-/Fachauftrag W0-11c, ENTWURF)** | additive Spalte/Beziehung „Importeur" an `ImportedIdsItem` + Migration + Rückweg + Fremdzuschreibungsprobe — **kein stiller Zusatz zu W0-11** (Yama 21.08.) | Modellentscheidung | eigenes Blatt nach Abschlussurteil (Spur Sicherheit, Welle 2) |
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
| A (Teil B, nach Y-12): Auto-Submit-POST ohne gültigen State → 4xx, 0 neue `imported_ids_items` | Schutz | *n.U.* | Testname + DB-Probe |
| B (Teil B, nach Y-12): Regulärer Rückweg mit State → wie bisher (Items angelegt) | Funktion | *n.U.* | Testname |
| C (Teil A): `uid` wird aus der Query **nicht mehr gelesen** — `grep -n "input('uid')\|query('uid')\|->uid" IdsController.php` → 0 Lesestellen; Test: Request mit fremder `uid` → der Lauf verwendet `auth()->id()` (Rohausgabe) — **ausdrücklich KEIN Test auf gespeicherte Zuschreibung** (Modell speichert sie nicht; das wäre ein Scheintest) | Session-/Request-Bindung | *n.U.* | grep + Testname |
| D (Teil A): `VerifyCsrfToken::$except` enthält die fünf toten Einträge nicht mehr; die verbleibenden treffen reale Routen (route:list-Abgleich) | Aufräumen | *n.U.* | Rohausgabe |
| E (Teil A): Baubericht benennt ehrlich die Grenze — „Urheberschaft wird nicht persistiert, W0-11c offen" | Ehrlichkeit | *n.U.* | Zitat |

**P1-Kriterium A ist vor dem Bau wirksam rot** (Ausnahme aktiv, `uid` aus Query).

## Rückweg
Ein Commit, zurückdrehbar; keine Daten.
