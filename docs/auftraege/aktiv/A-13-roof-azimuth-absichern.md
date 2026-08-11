# A-13 — `roof_azimuth` absichern. Und die Validierung gehört ins Model, nicht in den Controller

```yaml
auftrag: "A-13"
titel: "Das einzige Azimut-Feld im Haus ohne Test bekommt Validierung, Zusage und den Konventionshinweis"
art: "BAU — erster Produktivcode-Auftrag seit Tagen"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 783d47c1
prioritaet: P1
anlass: "Yamas Reihenfolge 12.08. Punkt 1, freigegeben ('haengt NICHT an N-003 — schneide es sofort')"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "docs/BEFUND-AZIMUT-KONVENTION.md (3d368625) · F-028 🔴 (FORMELSAMMLUNG)"
```

## Der Ist-Zustand — fünf Nullen, selbst gemessen

```text
Validierung in app/Http/Requests/       0 Treffer
Validierung in app/Http/Controllers/    0 Treffer
Factory                                 0 (es gibt gar keine PVRoofFactory)
Test                                    0 Treffer in tests/
Konventionshinweis am Model             0 Treffer
-> roof_azimuth ist das EINZIGE der vier Azimut-Felder im Haus ohne jede Absicherung.
   Zum Vergleich, alle drei ZUGESAGT:
     BuildingModelSchemaContractTest:53   test_azimut_vertrag_0_bis_360()
     SzeneProjektionServiceTest:80        assertSame([0, 90, 180, 270], $azimute)
     GeometrieAbleitungReferenzTest:84    assertSame(180.0, …)   Sued = 180
```

**Die Konvention selbst ist dokumentiert — seit dem ersten Tag, an genau einer Stelle:**

```text
database/migrations/2024_06_04_103808_create_p_v_roofs_table.php:67
  $table->float('roof_azimuth')->nullable(); // 0=N, 90=E, 180=S, 270=W
```

*Sie ist also **nicht unbekannt, sondern undurchgesetzt und unsichtbar** — der Kommentar steht in einer
Migration von 2024, wo niemand hinsieht, der das Feld benutzt.*

## DECISION — die Validierung gehört ins MODEL, und das ist gemessen begründet

**`roof_azimuth` hat SECHS Schreibpfade:**

```text
1  Old/PVChecklistController.php:138        new PVRoof
2  Task/PersonalTaskController.php:6495     PVRoof::create([…])
3  Customer/PVRoofController.php:70         PVRoof::create([…])
4  Customer/NewLeadsController.php:845      PVRoof::create([…])
5  Customer/NewLeadsController.php:1347     PVRoof::create([…])
6  Customer/NewLeadsController.php:7082     PVRoof::create($roofData)   <- MASS-ASSIGNMENT
```

> **Punkt 6 entscheidet die Frage.** *`PVRoof::create($roofData)` nimmt **jedes** Feld aus einem Array
> — dort kann ein beliebiger Azimut hereinkommen, ohne dass ein Controller ihn je genannt hat.*
> **Eine Validierung im Controller müsste an sechs Stellen stehen und würde beim siebten Schreibpfad
> fehlen.**
>
> *Und `PVRoofController::store` **hat** bereits ein `$request->validate([…])` (Z.42) — `roof_azimuth`
> steht nur nicht darin.* **Der Ort für Validierung existiert und wurde übersprungen; das wiederholt
> sich, solange die Regel nicht dort sitzt, wo alle Pfade durchgehen.**

**Und die Bauordnung von `ticket` sagt es selbst:** *„Eine Wahrheit für abgeleitete Werte im
Model-Hook, nicht verstreut in Controller/View/Job/PDF."*

```text
GEWAEHLT      die Pruefung sitzt am MODEL (Mutator oder saving-Hook) — EINE Stelle,
              die alle sechs Pfade abdeckt, auch den Mass-Assignment-Pfad.
ZUSAETZLICH   roof_azimuth in das VORHANDENE $request->validate() von
              PVRoofController::store aufnehmen — dort ist es die freundlichere
              Fehlermeldung fuer den Nutzer. Die Model-Pruefung bleibt das Netz.
NICHT         keine Pruefung in die anderen fuenf Controller. Das waere die
              verstreute Wahrheit, die die Bauordnung verbietet.
```

## Nicht-Ziele

- **Keine Umrechnung nach PVGIS.** *Das ist Yamas Schritt 8 und braucht seine drei SELECTs. F-028 🔴
  sperrt das Durchreichen — dieser Auftrag **beschreibt** die Konvention, er **überträgt** nichts.*
- **Keine Änderung an bestehenden Werten.** *Die Prüfung greift beim **Schreiben**, nicht rückwirkend.
  Bestandsdaten werden nicht angefasst — CLAUDE.md.*
- **Kein `NOT NULL`.** *Das Feld ist `nullable()` und bleibt es; ein Dach ohne bekannte Ausrichtung
  ist ein gültiger Zustand.*
- **Keine Änderung an den anderen drei Azimut-Feldern.** Sie sind zugesagt.
- **Keine Migration.** *Der Wertebereich wird geprüft, nicht im Schema erzwungen — ein `CHECK` würde
  Bestandsdaten beim nächsten Schreiben werfen, ohne dass jemand sie gesehen hat.*

## Scope

```text
app/Models/PVRoof.php                              Pruefung + Konventionshinweis
app/Http/Controllers/Customer/PVRoofController.php  roof_azimuth in das vorhandene validate()
tests/…/PVRoofAzimutTest.php  (neu)                 die Zusage
database/factories/PVRoofFactory.php  (neu?)        siehe A-13-4 — erst messen
```

## Wiederverwendungsprüfung (§5)

```text
$request->validate() in PVRoofController:42   VORHANDEN -> anschliessen, nicht neu bauen
Migrationskommentar :67                        VORHANDEN -> Wortlaut uebernehmen, nicht
                                               neu formulieren (0=N, 90=E, 180=S, 270=W)
test_azimut_vertrag_0_bis_360()                VORHANDEN in BuildingModelSchemaContractTest
                                               -> MUSTER fuer den neuen Test. Gleiche Form,
                                                  gleicher Name, damit beide auffindbar sind.
F-028 (FORMELSAMMLUNG)                         VORHANDEN -> im Hinweis VERWEISEN, den Text
                                               nicht kopieren
Factory-Muster                                 NUR DREI Factories im ganzen Repo
                                               (Anforderungsprofil, AnforderungsprofilWert,
                                                User) -> siehe A-13-4, das ist kein Hausmuster
```

## Auswirkungen (§5)

```text
API                     eine zusaetzliche Validierungsregel -> ein Request mit
                        roof_azimuth ausserhalb 0..360 wird abgewiesen statt gespeichert.
                        DAS IST EINE VERHALTENSAENDERUNG und gehoert in den Bericht.
Schema · Migration      KEINE
Bestandsdaten           NICHT ANGEFASST. Aber: ein bestehender Datensatz mit einem Wert
                        ausserhalb 0..360 wuerde beim naechsten SPEICHERN abgewiesen.
                        -> genau deshalb sind Yamas drei SELECTs die Bedingung (A-13-6).
Bundle                  KEINE (kein resources/)
Testdaten-Ziel          ticket_testing, niemals ticket
Prozessbindung          ENTFAELLT
```

**Erstnutzer:** *jeder Schreibpfad auf `p_v_roofs` — und die Brücke zur Ertragsrechnung (Yamas
Schritt 8), die ohne durchgesetzten Wertebereich auf Vermutungen rechnen müsste.*

## Akzeptanzkriterien

**A-13-1 (P1, die Prüfung sitzt am Model und greift für ALLE Pfade):** Ein Azimut außerhalb `0…360`
wird abgewiesen — **auch über `PVRoof::create($array)`**. *Nachweis: eine Wegwerf-Probe, die den
Mass-Assignment-Pfad benutzt, nicht den Controller.* **Rot heute:** `grep -c 'roof_azimuth' app/Models/PVRoof.php` → **2** (nur `$fillable` und `$casts`).

**A-13-2 (P1, der Konventionshinweis steht dort, wo man hinsieht):** `PVRoof.php` trägt den Wortlaut
`0=N, 90=E, 180=S, 270=W` **und den Verweis auf F-028** — *nicht den F-028-Text kopiert, sondern
verwiesen; zwei Fassungen derselben Warnung wären eine zweite Wahrheit.*

**A-13-3 (P1, die Zusage nach dem Hausmuster):** Ein Test nach dem Vorbild von
`test_azimut_vertrag_0_bis_360()` — **gleiche Form, damit beide mit einem `grep` auffindbar sind.**
Er prüft: `0` gültig · `360` (Grenze — **das Blatt sagt, welche Seite offen ist**) · `-1` abgewiesen ·
`361` abgewiesen · `null` gültig.

**A-13-4 (P1, die Factory wird GEMESSEN, nicht angenommen):** *Yamas Auftrag nennt „ein
Factory-Wert". **Gemessen: das Repo hat drei Factories, keine für PVRoof** — eine neue ist damit kein
Anschluss an ein Hausmuster, sondern ein neues Muster.* **Der Generator entscheidet nach Messung:**
Factory anlegen, **oder** den Test ohne Factory schreiben und **begründen, warum**. *Beides ist
zulässig; unzulässig ist, eine Factory zu bauen, weil sie im Auftrag stand.*

**A-13-5 (P1, `360` oder `359` — die Grenze wird entschieden und begründet):** Das Blatt sagt, ob
`360` gültig ist. *Gemessen: `CanonicalBuildingModelValidator:24` fordert `0 ≤ azimut < 360`
(**360 ausgeschlossen**), `azimutDerNormalen` liefert `0…359`, der Migrationskommentar nennt nur
`270=W`.* **Empfehlung: `0 ≤ x < 360` wie der Canonical-Validator** — dieselbe Grenze wie im Haus,
und `360` ist derselbe Punkt wie `0`. *Wer beide zulässt, hat zwei Zahlen für eine Richtung.*

**A-13-6 (`must_preserve`, und das ist die harte Grenze):** **Kein bestehender Datensatz wird
geändert oder gelöscht.** *Nachweis: die Migration bleibt unberührt, kein `UPDATE` im Diff, kein
Seeder.* **Und: der Generator führt die Probe gegen `ticket_testing`, niemals gegen `ticket`.**

**A-13-7 (P1, die Verhaltensänderung steht im Bericht):** Der Bericht nennt ausdrücklich, dass ein
Request mit einem Wert außerhalb des Bereichs **künftig abgewiesen wird**, und dass ein
**Bestandsdatensatz mit einem solchen Wert beim nächsten Speichern hängen bliebe**. *Ohne diesen Satz
sieht eine stille Verhaltensänderung wie eine Verbesserung aus.*

**A-13-8 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **Messung unmittelbar vor der
ersten Änderung** *(Lehre aus `ce30174f`)*.

## Kantenliste

```text
Wert exakt 360                 -> A-13-5 entscheidet. Empfehlung: abgewiesen.
Wert null                      -> GUELTIG. Das Feld ist nullable und bleibt es.
Bestandswert 450 in der DB     -> wird NICHT korrigiert (A-13-6), bliebe aber beim
                                  naechsten Speichern haengen -> gehoert in den Bericht
                                  (A-13-7) und ist der Grund fuer Yamas SELECTs.
Wert -90 (PVGIS-Denkweise)     -> abgewiesen. Genau der F-028-Fall: wer in PVGIS denkt,
                                  bekommt hier eine Absage statt einer stillen Drehung.
Mass-Assignment-Pfad           -> MUSS greifen (A-13-1). Er ist der Grund fuer die
                                  Model-Loesung.
Controller-Validierung allein  -> UNZUREICHEND, fuenf Pfade gehen daran vorbei.
```

## Rückweg und Entdeckung

**Rückweg:** eine Model-Änderung, eine Validierungszeile, ein Test. `git revert` genügt — **keine
Migration, keine Datenänderung, kein Rückweg für Daten nötig.** *Das ist der Grund, warum dieser
Auftrag ohne DB-Backup laufen kann.*

**Entdeckung:** Kommt ein Bericht über abgewiesene Speicherungen, sind Bestandswerte außerhalb des
Bereichs vorhanden — *dann ist Yamas SELECT-Ergebnis die Antwort, und der Fall gehört ihm, nicht dem
Code.*

## Konfliktprüfung (§5)

```text
§3 UNMITTELBAR gemessen   1 IN_ARBEIT -> W-22/1 (6a592b26), Scope werkbank/W-22/** + REGISTER.md
A-13 (dieses)             app/Models/PVRoof.php · PVRoofController · tests/ · database/factories/
                          -> KEINE Beruehrung mit W-22. Vollstaendig disjunkt.
                          -> KEIN resources/, KEIN docs/rollenkette/. Erster Auftrag seit
                             Tagen, der KEINE geteilte Doku-Datei anfasst.
W-07N · B5 · W-21L        alle ENTWURF, alle disjunkt zu app/ und tests/
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "sofort in DoR — disjunkt zu W-22, und §3 sperrt nur IN_ARBEIT-Setzen, nicht den DoR"
befund_1: "SECHS Schreibpfade, einer davon Mass-Assignment (NewLeadsController:7082)
           -> Controller-Validierung allein ist unzureichend, die Pruefung gehoert ans Model"
befund_2: "PVRoofController:42 HAT bereits ein validate() — roof_azimuth wurde uebersprungen.
           Der Ort existiert, die Regel fehlte."
befund_3: "nur DREI Factories im ganzen Repo -> eine PVRoofFactory ist kein Anschluss an ein
           Hausmuster. A-13-4 laesst dem Generator die gemessene Entscheidung."
abhaengig_von: "NICHTS — Yamas SELECTs sind fuer den BERICHT wichtig (A-13-7), nicht fuer den Bau"
```


## §11 — Bericht A-13 (Generator, 12.08.2026)

```yaml
auftrag: "A-13"
zustand: CODE_FERTIG
bau_commit: "a09b69af"
in_arbeit_commit: "6d57e627"
basis: "783d47c1"
art: "BAU — Produktivcode"

kriterien:
  A-13-1: GRUEN   # Waechter am Model, greift auf ALLEN Schreibpfaden — Wegwerf-Probe s. u.
  A-13-2: GRUEN   # Wortlaut 0=N,90=E,180=S,270=W am Model + VERWEIS auf F-028, kein Zitat
  A-13-3: GRUEN   # Test in der Hausform, gleicher Methodenname test_azimut_vertrag_0_bis_360
  A-13-4: GRUEN   # gemessen entschieden: KEINE Factory, Begruendung s. u.
  A-13-5: GRUEN   # 0 <= x < 360, am Hausmuster belegt statt selbst gewaehlt
  A-13-6: GRUEN   # Migration 0, Seeder 0, kein UPDATE, kein delete, Factory 0, resources 0
  A-13-7: GRUEN   # Verhaltensaenderung s. u., ausdruecklich benannt
  A-13-8: GRUEN   # 6d57e627 traegt beide Befehle mit Ausgabe, gemessen VOR der ersten Aenderung

hausmuster_gemessen_statt_erfunden:
  befund: "14 Models nutzen protected static function booted(); SECHS werfen darin eine
           eigene Ausnahme. BuildingModelVersion (Unveraenderlichkeits-Waechter) ist der
           naechste Verwandte und war die Vorlage — booted + Ereignis + final Exception."
  wiederverwendung_geprueft: "GeometrieUngueltigException existiert bereits. NICHT verwendet:
                              ihr Konstruktor verlangt ein TopologieErgebnis. Sie wiederzuverwenden
                              hiesse, ein Ergebnis zu erfinden, das es nicht gibt."

A-13-4_factory_entscheidung:
  gemessen: "database/factories enthaelt DREI Factories, keine fuer PVRoof"
  entschieden: "KEINE Factory"
  begruendung: "eine neue waere kein Anschluss an ein Hausmuster, sondern ein neues Muster —
                und der Test braucht sie nicht. pruefeAzimut() ist oeffentlich und statisch,
                damit die Zusage OHNE Datenbank prueffbar ist und der Waechter genau dieselbe
                Frage stellt wie der Test. Der DB-Pfad ist trotzdem belegt (Wegwerf-Probe)."

A-13-5_grenze:
  entschieden: "0 <= x < 360 — 360 ist UNGUELTIG"
  nicht_meine_wahl: "das Hausmuster test_azimut_vertrag_0_bis_360 sichert minimum 0 und
                     exclusiveMaximum 360 zu (BuildingModelSchemaContractTest:57-58).
                     360 ist derselbe Punkt wie 0; wer beide zulaesst, hat zwei Zahlen
                     fuer eine Richtung."

zwei_richtungs_probe:
  mutationsprobe: "Grenze von '>=' auf '>' verfremdet -> GENAU test_azimut_vertrag_360_wird_abgewiesen
                   faellt (8 Tests, 1 Failure). Zurueckgesetzt und per git hash-object als
                   IDENTISCH nachgewiesen, danach wieder 8/8."
  wegwerf_probe_mass_assignment:
    db_selbst_geprueft: "config('database.connections.mysql.database') === 'ticket_testing'"
    create_mit_400: "RoofAzimuthOutOfRangeException — der Waechter greift auf dem create()-Pfad"
    create_mit_180: "passiert den Waechter"
    danach: "pv_roofs in ticket_testing: 0 Zeilen — die Probe hat NICHTS geschrieben"
    aufgeraeumt: "beide Wegwerf-Dateien entfernt, git ls-files --others zeigt sie nicht mehr"

A-13-7_verhaltensaenderung:
  neu: "Ein Schreibvorgang mit roof_azimuth ausserhalb 0..360 wird KUENFTIG ABGEWIESEN —
        auf jedem Pfad, auch bei PVRoof::create(\$array)."
  folge_fuer_bestandsdaten: "Ein BESTEHENDER Datensatz mit einem solchen Wert wird NICHT
                             korrigiert (A-13-6) — er bliebe aber beim naechsten Speichern
                             HAENGEN, weil der Waechter beim Speichern greift, nicht beim Lesen."
  ehrlich: "Das ist eine echte Verhaltensaenderung und keine reine Verbesserung. Ohne diesen
            Satz sieht sie wie eine aus."
  nicht_gemessen: "wie viele Bestandsdatensaetze in der PRODUKTIVEN Datenbank betroffen waeren.
                   Ich habe NICHT gegen ticket gemessen — das sind Yamas drei SELECTs."

suite:
  unit: "278 tests, 851 assertions, OK — davon 8 neu"
  vorher: "270 (278 minus meine 8) — Tests sind GEWACHSEN, nicht geschrumpft (§12.2)"

E1_commit_messung: "drei Dateien, je 'im Commit' geprueft"
browserabnahme: "entfaellt — keine sichtbare Wirkung; die Aenderung ist ein Schreib-Waechter"
ballbesitz: evaluator
```
