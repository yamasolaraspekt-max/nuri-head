# VOTUM Z2-W0-5 — API-Planner-Zuständigkeit (A-1..A-4 IDOR)

**evaluator · 22.08.2026 · Auftrag `ABNAHME-evaluator-Z2-W0-5` gen 9 · Lease-Token 1**
**Bau A = `28ca0834` (abgenommen) · Integrationsstand `8cfd91b4` · Bau B `ef7a8c89` NICHT Gegenstand**

## Ergebnis: ABGENOMMEN — 8 von 8 Kriterien erfüllt

Ein Befund ohne Kriterienwirkung ist am Ende benannt. **Kein ENV_BLOCKED**: die Testdatenbank war
erreichbar und der Lauf nebenwirkungsfrei (Begründung unten).

## Kriterien

| # | Verlangt | Beleg (selbst gemessen) |
|---|---|---|
| **A** | A-1 fremder Mitarbeiter → 403; eigener → 200; Admin → 200 | `employees/{fremd}/work` und `/day-report` → **403**; eigener → **200**; Admin → **200** |
| **B** | A-2 fremde `customer_id` → 403 (index + upload); zugeordnete → 200 | `customer-images` mit fremder Kunden-Id → **403**, upload → **403**; zugeordnet → **200** |
| **C** | A-3 fremder Plan/Item → 403 (link/unlink/addToPlan); eigener → 200 | link/unlink/addToPlan auf fremd → je **403**; eigener Plan/Item → **200** |
| **D** | A-4 fremdes Item → 403; Melder-Id = `authEmployeeId()` trotz abweichendem Client-Wert | fremdes Item → **403**; Melder kommt aus der Sitzung, Client-Wert wird ignoriert |
| **E** | Genau EIN Baustein, vier Aufrufer — keine vier Kopien | `app/Support/Planner/PlannerZustaendigkeit.php` **einmal**; eingebunden in genau **vier** Controllern |
| **F** | `PlannerApiContractTest.php` bleibt grün | selbst gefahren: **14 passed, 29 assertions** |
| **G** | Stufe-1-Regel für A-1 mit Schalter **false** getestet | `setUp` setzt den Rechte-Schalter auf `false`; Einzeltests schalten auf `true` — **beide** Stellungen gemessen |
| **H** | Cookie-Pfad (stateful Session ohne Token) an dieselben Prüfungen gebunden | Test „h cookie weg ohne token ist ebenso gebunden" → **403** für fremd |

## Rot/Grün — der Bau ist die Ursache, nicht der Zufall

Statisch an den Wachen gezählt (`verlangeMitarbeiterSicht` / `verlangeZustaendigkeitFuer…`):

```
                                        vor dem Bau (28ca0834^)   danach
PlannerEmployeeApiController                     0                  2
PlannerItemMaterialController                    0                  2
PlannerMasterSetController                       0                  3
PlannerMobileCustomerImageController             0                  2
```

**Kriterium D zusätzlich am Code:** vorher stand die Melder-Id als `nullable|integer` in der
Validierung und wurde aus dem Request übernommen; jetzt ist das Feld aus der Validierung **entfernt**
und die Id wird hart aus der Sitzung gesetzt.

## Gegen-Beweis: die Tests messen, was ihr Name sagt

Ein Test, der wie ein Kriterium heißt, muss nicht dasselbe messen. Deshalb **Mutationsproben im
Wegwerf-Klon** — je eine Wache entfernt, gemessen, welche Tests rot werden:

| Mutation | rot geworden | übrige |
|---|---|---|
| Mitarbeiter-Wache entfernt | `a1 fremder mitarbeiter ist verboten`, `h cookie weg …` | 12 grün |
| Kunden-Wache entfernt | `a2 fremde kundenakte ist verboten` | 13 grün |
| Item-/Plan-Wachen im MasterSet entfernt | `a3 fremder plan und punkt sind verboten`, `a3 offener schalter …` | 12 grün |
| Item-Wache im Material entfernt | `a4 fremdes item ist verboten` | 13 grün |
| Melder wieder aus dem Client | `a4 melder kommt aus der sitzung …`, `a4 offener schalter erlaubt kein faelschen` | 12 grün |

**Jede Mutation trifft punktgenau die Tests ihres Kriteriums und keine anderen.** Damit hängen die
grünen Zusagen an den Wachen und nicht an der Testkulisse. Der Klon wurde nach jeder Mutation
zurückgesetzt (`git status` leer); am Bestand wurde **nichts** verändert.

## Grundmenge: alle 16 Routen der vier Ressourcen, keine ungeprüft

```
PlannerEmployeeApiController     myWork eigen-pfad · employeeWork WACHE · myDayReport eigen-pfad
                                 employeeDayReport WACHE · completeItemWithReport eigen-pfad
PlannerItemMaterialController    index WACHE · store WACHE
PlannerMasterSetController       index — · search — · show — · linked — · link WACHE · unlink WACHE · addToPlan WACHE
PlannerMobileCustomerImageController  upload WACHE · index WACHE
```

`index`, `search` und `show` im MasterSetController sind **Katalogzugriffe** ohne Kunden- oder
Item-Bezug — dort gibt es kein „fremd". Die Eigen-Pfade brauchen keine Fremdprüfung; das Blatt
verlangt sie ausdrücklich als 200 („Nuriva darf nicht brechen").

## Befund ohne Kriterienwirkung: `linked` ist ungeschützt

`GET /api/planner/items/{plannerItem}/master-sets` trägt **null Wachen**. Selbst ausgelöst im
Wegwerf-Klon, bei geschlossenem Rechte-Schalter:

```
EV-PROBE linked fremdes Item -> HTTP 200
body={"ok":true,"items":[{"id":22,"name":"W05-Set 6a8986a5a5673","description":null}]}
```

**Kein Mangel an diesem Auftrag:** die Matrix nennt für C ausdrücklich `link`, `unlink` und
`addToPlan`; `linked` steht in keinem Kriterium, und ich schreibe keines nach.
**Aber es ist dieselbe Klasse wie A-3**, sitzt in einem der vier Controller und wirkt beim heutigen
Schalterstand. Preisgegeben wird die Zuordnung „dieses fremde Item trägt diese Master-Sets" — der
Inhalt ist Katalogdatum, die **Zuordnung** ist es nicht. Gehört als Posten in die Z2-Folge.

## Warum kein ENV_BLOCKED

Der Auftrag lässt ENV_BLOCKED zu, wenn die Test-Isolation fehlt. Gemessen:

- Keine andere Rolle hatte DB-Arbeit (alle sechs Rollenquellen gelesen), keine Testprozesse aktiv.
- Der Test nutzt **`DatabaseTransactions`**, nicht `RefreshDatabase` — er setzt `ticket_testing`
  **nicht** neu auf. Genau das war die Ursache des §6-Vorfalls bei A-42; hier tritt er nicht ein.
- Vorzustand belegt und nach dem Lauf unverändert: 451 Tabellen, `users` 0 Zeilen.

**Eigener Aufbaufehler, offengelegt:** mein erster DB-Zugriff scheiterte mit „Access denied", und
ich hätte das beinahe als fehlenden `GRANT` (Y-13) gemeldet. Ursache war **mein** Parsing —
`cut -d= -f2` schnitt das Anführungszeichen im Passwort ab. Mit `-f2-` trägt der Zugang über TCP
**und** Socket. Kein Umgebungsmangel. Beinahe hätte ich eine eigene Ungenauigkeit als fremde
Blockade gemeldet.

## Abweichung, vor der Messung gemeldet

Der Auftrag nennt das Blatt unter `docs/auftraege/aktiv/Z2-W0-5*.md`. **Diesen Pfad gibt es nicht.**
Gemessen wurde gegen `docs/auftraege/generator-auftrag-z2-w0-5-api-planner-zustaendigkeit.md` am
Integrationsstand, in dem `28ca0834` enthalten ist (`merge-base --is-ancestor` exit 0).

## Was ich NICHT verwendet habe

Mein eigener Vergleich der beiden Bauten (`evaluator-w0-5-vergleich.yaml`, Empfehlung A) ist
Vorarbeit und kein Votum — der Auftrag sagt es, und ich halte mich daran. Bau B (`ef7a8c89`) habe
ich nicht geprüft und nicht bewertet.

## Ball

**Dirigent** — Z2-W0-5 abgenommen. `linked` gehört in die Z2-Folge, nicht in dieses Votum.
