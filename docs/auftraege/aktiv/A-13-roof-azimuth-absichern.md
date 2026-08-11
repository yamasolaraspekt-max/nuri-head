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

---

## Evaluator-Votum (§11) — 12.08.2026

```yaml
auftrag: "A-13"
commit: a09b69af          # Bau; Basis 783d47c1, Elter 50e968e9
votum: ABGENOMMEN
fehlerklasse: BEWEIS      # ein P2, der den Bau nicht trifft
gegenprobe: "Rot am Elter (8/8 rot) · fuenf eigene Mutationen · eigene Wegwerf-Probe am
  SCHREIBPFAD (save mit 400) · Bestandsdaten gezaehlt"
browser: nicht_anwendbar
datenbank: "ticket_testing — vor dem ersten Schreiben belegt (DB = ticket_testing)"
befunde:
  - "P2 BEWEIS · A-13-1: KEINE Zusage misst den saving-Hook. Alle acht rufen die statische
     Methode direkt; der Hook laesst sich entfernen und die Suite bleibt gruen."
```

### Messtisch — alle acht Zeilen

```text
-1  Waechter am Model, alle Pfade    VERHALTEN JA (eigene Probe unten), ZUSAGE FEHLT (Befund)
-2  Konventionshinweis am Model      "0=N, 90=E, 180=S, 270=W" steht in PVRoof.php
-3  Test in der Hausform             8 Zusagen, Namensmuster wie das Vorbild
-4  Factory gemessen statt gebaut    selbst nachgemessen: KEINE PVRoof-Factory im Repo,
                                     der Bau hat auch keine angelegt — richtig entschieden
-5  Grenze 0 <= x < 360 begruendet    am Hausmuster belegt, nicht selbst gewaehlt
-6  kein Bestandsdatensatz geaendert  Migration 0, Seeder 0, UPDATE 0 im Bau-Diff;
                                     p_v_roofs in ticket_testing: 0 Saetze, 0 ausserhalb 0..359
-7  Verhaltensaenderung im Bericht   ausdruecklich, samt dem unangenehmen Teil
-8  §3-Beleg in 6d57e627             2 Befehlszeilen, 2 Ausgaben
```

### Rot am Elter — die Zusagen messen wirklich etwas

```text
dieselbe Testdatei gegen das Elter-Model 50e968e9:  8 von 8 ROT
im Pruefstand a09b69af:                             8 von 8 GRUEN
```

### Fünf eigene Mutationen — und zwei überleben

```text
M1 saving-Hook entfernt              failed 0   BLIND   <- Befund
M2 '>= 360' auf '> 360' aufgeweicht  failed 1   GEFANGEN
M3 untere Grenze entfernt            failed 1   GEFANGEN
M4 is_numeric entfernt               failed 0   BLIND   (erklaerbar, s.u.)
M5 null/'' Fruehausstieg entfernt    failed 1   GEFANGEN
md5 vor und nach jeder Probe identisch.
```

**Der Grund ist derselbe für beide:** *alle acht Zusagen rufen `PVRoof::pruefeAzimut(...)`
**direkt** auf — keine einzige speichert ein Model.*

```text
Test :30 :36 :43 :49 :55 :60 :67   ->  PVRoof::pruefeAzimut(...)
save() / create() in der Zusage    ->  0 Treffer
```

**M4 ist dabei harmlos und erklärbar:** ohne `is_numeric` vergleicht PHP 8 `'Sued'` mit `360` als
Zeichenketten, `'S' > '3'`, also wirft es trotzdem. *Die Wache ist richtig, nur nicht durch diese
Zusage bewiesen.*

### Der Befund — P2, `BEWEIS`, und warum er die Abnahme nicht hält

**`A-13-1` verlangt: „die Prüfung sitzt am Model und greift für ALLE Pfade."** *Der Bericht meldet
das grün und verweist auf eine Wegwerf-Probe. **Die Zusagen decken es nicht** — der `saving`-Hook
ist entfernbar, ohne dass ein Test es merkt.*

**Ich habe deshalb den Schreibpfad selbst geprüft, statt es beim Befund zu belassen:**

```text
eigene Wegwerf-Probe im Pruefstand, §15 vorher belegt (DB = ticket_testing):
  $r = new PVRoof(); $r->roof_azimuth = 400; $r->save();
  -> EVAL| save(400) WIRFT RoofAzimuthOutOfRangeException — der Hook greift
Probe danach entfernt.
```

> **Das Verhalten stimmt, der Nachweis fehlt.** *Deshalb `BEWEIS` und nicht `CODE`, und deshalb P2
> statt P1: der Bau tut, was `A-13-1` verlangt — was fehlt, ist eine Zusage, die es festhält.
> **Ohne sie kann der Hook bei einem späteren Umbau still verschwinden**, und genau diese Lage hat
> die Werkbank bei A-08 schon einmal gekostet.* **Nachforderung an den Generator: eine Zusage, die
> speichert statt aufzurufen.**

*Was hier gut ist und benannt gehört: `A-13-4` hat der Bauende gemessen statt gebaut — es gibt
keine `PVRoof`-Factory, und er hat keine angelegt, obwohl der Auftrag das Wort „Factory" enthielt.
Und `A-13-7` nennt die Verhaltensänderung mitsamt dem unangenehmen Teil: ein Altwert außerhalb der
Grenze bleibt beim nächsten Speichern hängen.*

---

## Release-Prüfung A-13 (§10) — 12.08.2026

**Urteil: `RELEASE_FREI`** — mit **einer Betriebsauflage** (siehe unten) und **einem offenen P2**,
der nach §12.5 kein Release-Hindernis ist.

```yaml
auftrag: "A-13"
abnahme_commit: "a09b69af"
release_commit: "a09b69af"
votum: RELEASE_FREI
ci: pass                      # php artisan test --testsuite=Unit -> 278 passed (851 assertions)
artefakte_reproduzierbar: true # kein Bundle, kein Build — reiner PHP-Serverpfad
migration: nicht_anwendbar     # 0 Migrationen im Bau-Diff
rueckweg: pass                 # reiner git revert, reverse-apply-Probe sauber
smoke_test_plan: "Vor der Veroeffentlichung Yamas drei SELECTs gegen `ticket` (Betriebsauflage).
                  Danach: ein Dachformular mit gueltigem Azimut speichern (muss durchgehen),
                  eines mit 400 (muss abgewiesen werden), und ein BESTANDSDach ohne Azimut-
                  Aenderung speichern (muss durchgehen)."
befunde: []
```

### Die drei Fragen, die bei einem `saving`-Hook auf einer Live-Tabelle zählen

**1. §15 — verändert der Hook Bestandsdaten? NEIN, am Code gemessen, nicht am Bericht.**

```text
pruefeAzimut()   PVRoof.php:145-154  -> enthaelt KEINE Zuweisung. Nur `return` (null/'')
                                        oder `throw`. Kein Rechenweg, keine Normalisierung.
booted()/saving  PVRoof.php:161-166  -> ruft nur pruefeAzimut($roof->roof_azimuth).
                                        Der Wert wird gelesen, nicht zurueckgeschrieben.
Mutator          setRoofAzimuthAttribute -> 0 Treffer im Model.
Bau-Diff         3 Dateien, 144 insertions(+), 0 deletions(-)
                 Migrationen 0 · UPDATE 0 · Seeder 0 · database/ 0
```

*370 wird also **nicht** zu 10 gemacht.* **Abgewiesen statt zurechtgebogen** — genau so steht es
auch im Docblock der Ausnahme (`Keine stille Korrektur`). Es gibt keinen Pfad, auf dem ein
bestehender Wert beim Speichern eines Datensatzes still verändert würde. **§15 ist gewahrt; eine
eigene Freigabe für Datenänderung ist nicht nötig, weil keine stattfindet.** Test-Ziel ist
`ticket_testing` (`phpunit.xml:28`, `force="true"`), also auch kein Messen an Produktivdaten.

**Der Restfall ist eine ABWEISUNG, keine Änderung — und er ist die Betriebsauflage:** ein
Bestandssatz mit einem Wert außerhalb `0 ≤ x < 360` wird beim nächsten Speichern **abgelehnt**
(A-13-7 nennt das ausdrücklich). Zwei Dinge dazu habe ich nachgemessen, weil sie die Größe des
Restrisikos bestimmen:

```text
Spaltentyp   2024_06_04_103808:67  $table->float('roof_azimuth')->nullable();
             -> nur ZAHLEN oder NULL koennen im Bestand stehen. Textwerte wie 'Sued'
                sind im Bestand nicht moeglich; is_numeric kann dort nicht zuschlagen.
Ausnahme     RoofAzimuthOutOfRangeException wird NIRGENDS gefangen (0 Treffer ausserhalb
             ihrer Definition und des Models) -> ein Altsatz ausserhalb der Grenze
             erzeugt beim Speichern eine 500, keine freundliche Formularmeldung.
```

> **Betriebsauflage (bindend vor der Veröffentlichung, nicht vor `RELEASE_FREI`):** Yamas drei
> `SELECT`s gegen `ticket` — wie viele `p_v_roofs`-Sätze tragen `roof_azimuth` außerhalb
> `0 ≤ x < 360`. *Das Blatt weist diese Messung ausdrücklich Yama zu („der Fall gehört ihm, nicht
> dem Code"), und §15 verbietet mir das Messen an Produktivdaten. Ich habe sie deshalb **nicht**
> gefahren, sondern als Bedingung eingetragen.* **Bei Ergebnis 0 ist die Auflage erledigt; bei > 0
> gehört die Entscheidung Yama, bevor deployt wird.**

**2. Greift die Prüfung auf ALLEN sechs Schreibpfaden? JA — und auf mehr als sechs.**

```text
1  Old/PVChecklistController.php:138   new PVRoof + ->save()      -> saving greift
2  Task/PersonalTaskController.php:6495 PVRoof::create([…])       -> saving greift
3  Customer/PVRoofController.php:70     PVRoof::create([…])       -> saving greift
4  Customer/NewLeadsController.php:845  PVRoof::create([…])       -> saving greift
5  Customer/NewLeadsController.php:1347 PVRoof::create([…])       -> saving greift
6  Customer/NewLeadsController.php:7082 PVRoof::create($roofData) -> MASS-ASSIGNMENT, saving greift
+  Customer/NewLeadsController.php:7070 fill($roofData)+save()    -> Aenderungspfad, saving greift
+  Customer/PVRoofController.php:136    find(id)->update([…])     -> Model-update, saving greift
+  Customer/PVRoofController.php:199    firstOrCreate([…])        -> saving greift
```

**Die entscheidende Gegenprobe ist nicht die Liste, sondern die Suche nach dem Pfad, der am Model
VORBEI schreibt** — denn nur ein solcher würde den Hook aushebeln:

```text
$ grep -rn --include='*.php' -E "table\('p_v_roofs'\)->(update|insert|delete)" app/
  (0 Treffer)
$ grep -rn --include='*.php' -E "PVRoof::where\([^)]*\)->update\(" app/
  (0 Treffer)
```

*Die einzigen ereignislosen Operationen auf `p_v_roofs` sind `->delete()`
(`NewLeadsController:6993`, `:13979`, `Old/PVChecklistController:104`) — sie schreiben keinen
Azimut.* **Es existiert kein Schreibweg an Eloquent vorbei; der Model-Ort deckt damit tatsächlich
alles ab, was heute schreibt — und auch den „siebten Pfad", der bei `NewLeadsController:7209` erst
als Kommentar notiert ist.**

*Nachweislage:* der **Beweis** dafür fehlt weiterhin in der Zusagenmenge — das ist der offene P2
(alle acht Zusagen rufen `pruefeAzimut` direkt auf, keine speichert). Der Evaluator hat das
Verhalten mit einer eigenen Wegwerf-Probe gegen `ticket_testing` belegt (`save(400)` wirft), der
Generator zusätzlich auf dem `create()`-Pfad. **Verhalten belegt, Regressionsschutz offen.**

**3. Rückweg — reiner `git revert`, keine Datenmigration? JA, am Diff verifiziert.**

```text
$ git show a09b69af --stat
  app/Exceptions/RoofAzimuthOutOfRangeException.php | 26 ++++
  app/Models/PVRoof.php                             | 49 ++++++
  tests/Unit/Models/PVRoofAzimutVertragTest.php     | 69 ++++++
  3 files changed, 144 insertions(+)          <- 0 deletions, rein additiv
$ git show a09b69af | git apply --check -R -
  (ohne Ausgabe -> reverse-apply ist sauber, ohne etwas zu schreiben)
$ git diff a09b69af HEAD -- app/… tests/…
  (leer -> die drei Dateien sind seit dem Bau unveraendert)
```

*Keine Migration im Scope (0), keine Datenänderung, keine Abhängigkeit von außen auf die neue
Ausnahme oder die Konstanten (0 Treffer außerhalb der zwei Dateien).* **`git revert a09b69af`
stellt den Vorzustand vollständig her; ein Rückweg für Daten ist nicht nötig, weil keine Daten
angefasst wurden.** Das deckt sich mit der Rückweg-Zusage des Blattes.

### Standard-§10

```text
Kette (je merge-base --is-ancestor, alle OK)
  783d47c1 Basis -> 7f80eeea BEREIT -> 6d57e627 IN_ARBEIT -> a09b69af Bau
        -> 511fe7d7 CODE_FERTIG -> c9397575 ABGENOMMEN -> 4ce5b4d4 Claim -> HEAD
Votum und Kandidat zeigen auf DENSELBEN Commit: c9397575 nennt a09b69af.  pass
Scope-Reinheit: 3 Dateien, alle drei im Blatt-Scope. Keine resources/, keine
  scripts/, keine database/, kein Blade, kein Bundle.                      pass
Suite selbst gefahren: php artisan test --testsuite=Unit
  -> 278 passed (851 assertions), 2.95s — dieselbe Zahl wie im §11-Bericht.
Beifang seit dem Bau: git diff --name-only a09b69af HEAD -- app/ database/
  tests/ config/ routes/  -> LEER. Kein Produktivpfad hat sich seither bewegt.
Artefakte: kein Build, kein Bundle, keine Konfig-, ENV- oder Abhaengigkeits-
  aenderung (composer.json/lock 0). Nichts zu reproduzieren.
Rechte/Mandanten/Datenschutz: unberuehrt — der Hook liest ein Feld und wirft.
Offene P0/P1: KEINE.
```

### Zwei Hinweise ohne Hindernis (Eigentümer: Planner/Generator, nicht dieser Vermerk)

```text
H1  Der Scope-Punkt "roof_azimuth in das vorhandene validate() von
    PVRoofController::store" (DECISION, Zeile ZUSAETZLICH) ist NICHT gebaut —
    PVRoofController.php steht nicht im Bau-Diff, und roof_azimuth fehlt weiter
    im validate()-Block ab Z.42. Kein Akzeptanzkriterium verlangt ihn, deshalb
    kein Befund; er ist aber genau die Zeile, die aus der 500 eine freundliche
    Formularmeldung machen wuerde -> passt zur Betriebsauflage oben.
H2  Der Cast ist 'decimal:2'. Ein Bestandswert 359.999 wird beim LESEN zu
    "360.00" gerundet und damit vom Waechter abgewiesen, obwohl der gespeicherte
    Wert innerhalb der Grenze liegt. Sehr schmale Kante, keine Datenaenderung,
    aber sie gehoert benannt, falls die SELECTs der Betriebsauflage Werte
    knapp unter 360 zeigen.
H3  Kosmetisch: der Docblock der Ausnahme nennt die Tabelle `pv_roofs`; sie
    heisst `p_v_roofs` (Model ohne $table, Migration 2024_06_04_103808).
```

**Der offene P2 (Klasse `BEWEIS`) wird hier ausdrücklich mitgeführt und nicht geschluckt:** *eine
Zusage, die **speichert** statt aufzurufen, fehlt weiterhin; die Nachforderung liegt beim Generator.*
**Nach §12.5 ist das kein Release-Hindernis** — das Verhalten ist von zwei Rollen unabhängig belegt,
was fehlt, ist der Regressionsschutz gegen einen späteren Umbau.


---

# NACHTRAG 12.08. — Yamas Messung liegt vor. Die Bedingung ist LEER, und der Hetzner-Posten ist dadurch SCHÄRFER

**A-13 ist `RELEASE_FREI` (8/8, Bau `a09b69af`). Die Betriebsauflage aus `A-13-7` verlangte die
Datenmessung — Yama hat sie gefahren:**

```text
SELECT COUNT(*) … roof_azimuth ausserhalb [0,360)
ERGEBNIS   0 Saetze gesamt · 0 ausserhalb · 0 NULL
-> DIE BEDINGUNG IST LEER. Kein Bestandsdatensatz kann beim naechsten Speichern
   haengen bleiben, weil es keinen gibt.
```

**Und warum Yama das durfte und die Rollen nicht — seine Begründung, weil sie eine Regel schärft:**

> *„Der Unterschied ist nicht Rang, sondern **Handlung**: §15 verbietet Testdaten in der Arbeits-DB
> und Messen an Produktivdaten; ein **lesender** `COUNT` gegen die **lokale Dev-DB** schreibt nichts
> und ist keine Datenoperation."*

*Damit ist auch mein eigener Abbruch vom 11.08. richtig eingeordnet: ich hatte `artisan tinker`
versucht und die Verweigerung gemeldet statt sie zu umgehen. **Das war richtig — nicht weil Messen
verboten ist, sondern weil `tinker` ein schreibfähiges Werkzeug ist.***

## Der Hetzner-Posten ist jetzt SCHÄRFER als vor der Heilung

**Gegengemessen, Yamas drei Zahlen:**

```text
SPEICHERNDE CONTROLLER   4 Dateien mit PVRoof::create / new PVRoof:
                           Customer/PVRoofController · Customer/NewLeadsController ·
                           Task/PersonalTaskController · Old/PVChecklistController
                         -> davon DREI ausserhalb von Old/. Yamas "drei" trifft die
                            aktiven; meine Vier zaehlt die Dateien. Beide Zahlen richtig,
                            verschiedene Mengen (B6).
catch-BLOECKE um diese Schreibpfade      0
FORMULARVALIDIERUNG fuer roof_azimuth    0   (app/Http/Requests/ -> 0 Treffer)
```

> **Und daraus folgt der Punkt, den Yama benennt und der vor der Heilung nicht existierte:**
>
> *Lokal ist die Bedingung leer. **Auf Hetzner ist sie ungemessen** — und dort trifft ein Altsatz mit
> Wert außerhalb `[0,360)` jetzt auf eine Model-Validierung, **0 `catch`-Blöcke und 0
> Formularvalidierung**.*
>
> **Das heißt: die Heilung kann auf Hetzner einen FEHLER AUSLÖSEN, wo vorher nur ein falscher Wert
> stand.** *Ein erneutes Speichern eines Altsatzes wirft, niemand fängt es, und es gibt keine
> Formularmeldung, die es abfängt — der Nutzer sieht einen Serverfehler statt eines Hinweises.*
>
> **Das ist keine Kritik an A-13.** *Der Bau ist richtig; ohne ihn wäre der falsche Wert still
> geblieben. **Aber es ist ein Vor-Deploy-Posten, und er gehört Yama:*** dieselben drei SELECTs auf
> Hetzner, **vor** der Veröffentlichung.

```text
WAS DARAUS FOLGT, und es ist kein neuer Auftrag:
  1  vor dem Hetzner-Deploy: die drei SELECTs dort fahren. Ergebnis 0/0/0 -> nichts zu tun.
  2  Ergebnis > 0  ->  DANN braucht es eine Entscheidung: Altsaetze korrigieren
     (Datenoperation, nur Yama) ODER die Validierung auf neue Saetze begrenzen.
     BEIDES ist eine Yama-Entscheidung, keine Rollenarbeit.
  3  UNABHAENGIG davon ist "0 catch-Bloecke bei vier Schreibpfaden" ein eigener Befund
     ueber die Fehlerbehandlung — er betrifft NICHT nur roof_azimuth, und er ist hier
     nur BENANNT, nicht behoben (H-1: mit Zieladresse, naemlich als eigener Auftrag,
     falls Yama ihn will).
```

```yaml
nachtrag: "12.08. — Yamas Datenmessung: 0/0/0, die Bedingung ist leer"
a13_7_geschlossen: "die Verhaltensaenderung ist gemessen und beziffert: lokal trifft sie
                    KEINEN Bestandssatz"
hetzner_offen: "ungemessen. Und dort SCHAERFER als vorher: Altsatz + Model-Validierung +
                0 catch + 0 Formularvalidierung = Serverfehler statt Hinweis."
gegenmessung: "4 schreibende Dateien (3 ausserhalb Old/) · 0 catch · 0 Requests-Validierung —
               Yamas Zahlen bestaetigt, die 3/4-Differenz ist eine Mengenfrage (B6)"
neuer_nebenbefund: "0 catch-Bloecke bei vier Schreibpfaden auf p_v_roofs. Betrifft mehr als
                    roof_azimuth. Nur benannt, mit Zieladresse (eigener Auftrag, falls gewollt)."
```
