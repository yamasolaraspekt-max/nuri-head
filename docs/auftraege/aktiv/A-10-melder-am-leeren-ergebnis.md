# A-10 — Der Melder greift am Wurf, nicht am leeren Ergebnis

```yaml
auftrag: A-10
titel: "Ein Dach, das KEINE Flaeche liefert, wird gemeldet - auch ohne Ausnahme"
basis_sha: d58b220e
prioritaet: P2
art: "Folgeauftrag nach §12.5 - A-01 bleibt RELEASE_FREI, hier wird nichts rueckwirkend geaendert"
anlass: "b29bb79d (A-05-Abnahme), Gegenprobe E4b des Evaluators"
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
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

**A-10-2 (P1, Gegenprobe):** Ein Dach, das **Flächen liefert**, wird **nicht** gemeldet.
*Ohne dieses Kriterium wäre „melde immer" grün.*

**A-10-3 (`must_preserve`):** **Alle A-01-Zusagen bleiben grün**, insbesondere der Wurf-Pfad und
die Fußleisten-Absage. *§7: keine Abschwächung bestehender Tests.*

**A-10-4 (Sichtkette, P2):** Die Browserabnahme zeigt den Hinweis über einem geladenen
`l-shape`-Dokument. **Hier gehört die Sicht-Ebene hin** — *in A-05 war sie zwecklos, weil dort
nichts gebaut wurde; hier belegt sie, dass der Nutzer die Meldung wirklich sieht.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten     KEINE
Produktivcode    renderers/three-d/szene.ts (+ ggf. app/DreiDBereich.tsx)
Bundle           JA - Insel, `npm run build:hausplaner` gehoert ins Tor
Testdaten-Ziel   KEINES (Fixture a01-bestandsdokument-l-dach.json liegt bereits)
Prozessbindung   Browserabnahme nach §8 - Anker-Regel gilt, Buehne nur ueber browser-buehne.sh
Werkzeuge        npm run test:hausplaner - vorhanden UND in Gebrauch
```

**Erstnutzer** (§5 1.2.2 — der Melder ist vorhanden, die Bedingung ist neu): **der Evaluator bei
der nächsten Browserabnahme eines Bestandsdokuments.**

## Nicht-Ziele

- **Keine L-, T-, U-Dächer bauen.** *A-01s Nicht-Ziel bleibt (Entscheidung `bd1383c8`, gestützt auf
  die Achtpunkt-Lückenliste). A-10 macht die Lücke **sichtbar**, nicht kleiner.*
- **Keine Änderung an `dachGeometrie.ts:87`** — der Wurf-Pfad ist gebaut und abgenommen.

## Rückweg

Eine Bedingung in `szene.ts`, `git revert` genügt. **Kein Zustand außerhalb des Repos betroffen.**
