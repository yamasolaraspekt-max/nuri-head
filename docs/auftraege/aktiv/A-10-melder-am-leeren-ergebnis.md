# A-10 — Der Melder greift am Wurf, nicht am leeren Ergebnis

```yaml
auftrag: A-10
titel: "Ein Dach, das KEINE Flaeche liefert, wird gemeldet - auch ohne Ausnahme"
basis_sha: d58b220e
prioritaet: P2
art: "Folgeauftrag nach §12.5 - A-01 bleibt RELEASE_FREI, hier wird nichts rueckwirkend geaendert"
anlass: "b29bb79d (A-05-Abnahme), Gegenprobe E4b des Evaluators"
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
claim: "planner 10.08. 18:2x — Claim VOR der Nacharbeit gesetzt. Das Blatt lag 45 h unberuehrt
        (mtime 08.08. 14:52), ballbesitz stand auf planner, KEIN Claim lag darauf; die zweite
        Planner-Instanz hat A-09 geclaimt, A-10 nicht. Zwei Restpunkte der 1. DoR-Runde
        (9c63da13) eingearbeitet plus die dort ausdruecklich als 'kein Blocker' genannte
        Empfehlung."
```

## Der Befund

**A-01-4 hat den stillen Ausfall beseitigt — aber nur auf einem der beiden Wege dorthin.**

```text
WURF-PFAD    dachFlaechen wirft DachGeometrieUngueltig
             -> szene.ts faengt, nichtDarstellbar() meldet, die 3D zeigt den Hinweis   GEBAUT

LEER-PFAD    l-shape ohne `anbau` wirft NICHT - es liefert { dreiecke: [] }, 0 Flaechen
             -> kein Wurf, kein Fang, Melder []                                        LUECKE
```

> **Ein Dach, das nichts zeigt und nichts sagt** — genau der Zustand, gegen den A-01-4 gebaut
> wurde. *Dass die Lücke auf dem anderen Pfad liegt, macht sie nicht kleiner; sie macht den Melder
> unvollständig.*

**Rot-Beleg, dreifach unabhängig gemessen:**

```text
Generator (9e97d274)       dachMeshWelt -> {dreiecke:[]}, dachflaechen -> 0 Flaechen
Generator (e0fae829)       A-05-3: laedt schema-gueltig, bleibt still leer
Evaluator (b29bb79d, E4b)  eigene Wegwerf-Zusage 12/12: Melder []
                           - und der Melder greift am Wurf-Pfad, die Luecke ist spezifisch
```

## Wiederverwendungsprüfung (§5, Fassung 1.2.2)

```text
renderers/three-d/szene.ts     nichtGezeichnet[] + nichtDarstellbar() - der Melder EXISTIERT,
                               beide Faenger (Z.498 / Z.544) sind gebaut
app/DreiDBereich.tsx           die Hinweis-Anzeige (role="status") existiert, mit T-Tokens
__tests__/dachAusKontur.test.ts  A-01s Zusagen - must_preserve
geometry/dachGeometrie.ts:87   die Absage auf dem Wurf-Pfad - unangetastet
```

**Es wird nichts Neues gebaut.** *Der Melder bekommt eine zweite Eingangsbedingung — leeres
Ergebnis statt nur Ausnahme. Anzeige, Tokens und Zusagen stehen bereits.*

## Akzeptanzkriterien

**A-10-1 (P1):** Liefert die Dachberechnung **null Flächen**, ohne zu werfen, meldet
`nichtDarstellbar()` das Dach mit einem lesbaren Grund. *Rot heute: Melder `[]`.*

**A-10-2 (`must_preserve`-KONTROLLE — von der Rot-Pflicht nach §5 AUSGENOMMEN):** Ein Dach, das
**Flächen liefert**, wird **nicht** gemeldet. *Ohne dieses Kriterium wäre „melde immer" grün.*

> **Warum ausgenommen statt gestrichen — Restpunkt 1 der 1. DoR-Runde (`9c63da13`).** Der
> Plan-Prüfer hat richtig gemessen: dieser Fall ist an der Basis **grün**, und zwar trivial —
> heute wird *überhaupt nichts* gemeldet, also auch kein Flächen-Dach. Als P1 geführt hätte das
> Blatt gegen „kein Kriterium ist bereits erfüllt" verstoßen.
>
> Es bleibt stehen, weil es der **Gegenhalter zu A-10-1** ist: ohne es wäre „melde immer" eine
> vollständig grüne Lösung. Gleiche Bauart wie `A-01-2`, `A-02-1` und `A-08-2` — das ist inzwischen
> das vierte Kriterienpaar dieser Form, und das Muster ist damit belegt, nicht geraten.

**A-10-3 (`must_preserve`):** **Alle A-01-Zusagen bleiben grün**, insbesondere der Wurf-Pfad und
die Fußleisten-Absage. *§7: keine Abschwächung bestehender Tests.*

**A-10-4 (Sichtkette, P2):** Die Browserabnahme zeigt den Hinweis über einem geladenen
`l-shape`-Dokument. **Hier gehört die Sicht-Ebene hin** — *in A-05 war sie zwecklos, weil dort
nichts gebaut wurde; hier belegt sie, dass der Nutzer die Meldung wirklich sieht.*

**A-10-5 (P1, Mutationsprobe — Empfehlung der 1. DoR-Runde, aufgenommen):** Mindestens **drei**
Mutationen fallen: **die neue Leer-Bedingung entfernt** · ihr Ergebnis ignoriert (geprüft, aber
nicht gemeldet) · die Bedingung so verengt, dass sie nur bei `dreiecke.length === 0` greift und
`dachflaechen === 0` übersieht.

> **Warum aufgenommen, obwohl ausdrücklich „kein Blocker".** Nach dem Vorbild `A-08-6`: eine
> Bedingung ohne Mutationszusage ist **stumm entfernbar** — jemand nimmt sie im nächsten Umbau
> heraus, die Suite bleibt grün, und der Befund kommt in Monaten zurück. Genau diese Lage hatte
> A-08 (dort war `&&` → `||` die eine Mutation, die alle Schutzbedingungen gleichzeitig entwertete
> und jeden Einzeltest grün ließ). Die Zusage kostet wenig und macht die Bedingung dauerhaft
> sichtbar.

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten     KEINE
Produktivcode    renderers/three-d/szene.ts (+ ggf. app/DreiDBereich.tsx)
Bundle           JA - Insel, `npm run build:hausplaner` gehoert ins Tor
Testdaten-Ziel   KEINES (Fixture a01-bestandsdokument-l-dach.json liegt bereits)
Prozessbindung   Browserabnahme nach §8 - Anker-Regel gilt, Buehne nur ueber browser-buehne.sh
Werkzeuge        npm run test:hausplaner - vorhanden UND in Gebrauch
```

**Konfliktprüfung (§5) — Restpunkt 2 der 1. DoR-Runde (`9c63da13`), hier nachgetragen:**

*Selbst an den Blättern nachgemessen, nicht aus der DoR-Notiz übernommen — zwei Angaben dort waren
ungenau:*

```text
A-04  IN_ARBEIT     scripts/buehnen-waechter.sh + __tests__/buehnenWaechter.test.mjs
                    NICHT commit-pruefen.sh — noch weiter entfernt als angenommen
A-07  ENTWURF       scripts/commit-pruefen.sh + commitPruefen.test.mjs
A-09  ENTWURF       scripts/commit-pruefen.sh + commitPruefen.test.mjs
A-10  DIESES        renderers/three-d/szene.ts (+ ggf. app/DreiDBereich.tsx)
-> disjunkt zu allen drei. A-10 darf PARALLEL laufen, kein aktives Blatt teilt eine Datei.

ABER — vom Plan-Pruefer nicht genannt und hier ergaenzt:
A-01  RELEASE_FREI  beruehrt DIESELBE Datei: "szene.ts — die zwei Faenger melden statt zu
                    schlucken" (A-01 Z.56, Faenger an szene.ts:498 und :544).
                    A-10 baut auf genau diesem Bau auf. Kein PARALLEL-Konflikt (A-01 ist
                    abgeschlossen), aber der Grund, warum A-10-3 kein Formalismus ist:
                    dieselbe Datei, derselbe Mechanismus, eine Stelle weiter.
```

> **Warum die Zeile trotz Disjunktheit ins Blatt gehört.** Der Nachweis, dass *kein* Konflikt
> besteht, ist genauso Arbeit wie der Nachweis eines Konflikts — und er fehlte schon zweimal:
> bei A-08 hat der Plan-Prüfer die Kollision mit A-07 selbst ergänzt (*„fehlte in BEIDEN
> Dokumenten"*), und daraus wurde die festgelegte Baureihenfolge. Ohne die Zeile muss die nächste
> Rolle sie erneut herleiten.
>
> *Nebenbei beim Messen aufgefallen, gehört nicht in dieses Blatt: **weder A-07 noch A-10 haben
> einen eigenen `## Scope`-Abschnitt.** Bei A-10 steht der Produktivcode im §5-Block, bei A-07
> ist er nur über die Konfliktprüfung von A-08 belegt. Kein Mangel dieses Auftrags, aber eine
> Formlücke, die das Messen von Konflikten jedes Mal teurer macht als nötig.*

**Erstnutzer** (§5 1.2.2 — der Melder ist vorhanden, die Bedingung ist neu): **der Evaluator bei
der nächsten Browserabnahme eines Bestandsdokuments.**

## Nicht-Ziele

- **Keine L-, T-, U-Dächer bauen.** *A-01s Nicht-Ziel bleibt (Entscheidung `bd1383c8`, gestützt auf
  die Achtpunkt-Lückenliste). A-10 macht die Lücke **sichtbar**, nicht kleiner.*
- **Keine Änderung an `dachGeometrie.ts:87`** — der Wurf-Pfad ist gebaut und abgenommen.

## Rückweg

Eine Bedingung in `szene.ts`, `git revert` genügt. **Kein Zustand außerhalb des Repos betroffen.**
