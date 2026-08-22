# Verfallsdatum-Kopfzeilen — die Grundmenge, gemessen und eingeordnet

```yaml
art: "ZUARBEIT — Messung und Einordnung fuer einen Posten, der NICHT mir gehoert.
      Kein Bau, keine Kopfzeile geschrieben, keine Stilllegung."
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 19, Posten 7 (Reihenfolge) —
          WERKZEUGWEG-entscheidung-2026-08-22.md Punkt 5"
besitzer_laut_entscheidung: "GENERATOR, ausdruecklich 'NACH Anschlusswelle 1', Meilenstein M2"
mess_sha: 34494fb7
ergebnis: "27 stimmt — ist aber die falsche Grundmenge. Der Posten betrifft 24."
```

## Die Rollengrenze steht vor der Messung

**Punkt 5 der WERKZEUGWEG-Entscheidung nennt als Besitzer den Generator**, mit dem Zusatz
*„NACH Anschlusswelle 1 — keine parallele Aufräumaktion (Index-Kollision 21.08.)"*.
**Posten 7 meines Auftrags nennt den Posten in meiner Reihenfolge.** *Beides passt zusammen, wenn
ich ihn vorbereite und nicht ausführe* — und genau das ist dieses Blatt.

> **Ich schreibe keine einzige Kopfzeile.** Was hier steht, ist die Grundmenge mit Einordnung, damit
> der Generator sie nicht 24-mal selbst erraten muss. *Welle 1 ist nicht abgeschlossen: die Blätter
> sind geschnitten, keines ist gebaut.*

## Die Zahl stimmt — heute nachgemessen

```
Dateien im Inselbaum (ohne __tests__/__domtests__)     160
erreichbar ab main.tsx ueber LAUFZEIT-Kanten           133
UNERREICHT                                              27
```

**Unverändert gegenüber der Anschluss-Entscheidung vom Vormittag** — es wurde seither nichts gebaut,
nur spezifiziert. *Die Messung ist reproduzierbar: BFS ab `main.tsx`, `import type` zählt nicht,
dynamische `import()` und `export * from` zählen mit.*

## ⚠ Aber 27 ist die falsche Grundmenge — drei davon haben zu Recht keinen Ladeweg

**Der Auftrag lautet „Verfallsdatum je Modul **ohne Ladeweg**" mit der Folge *„sonst Stilllegung"*.
Drei der 27 sind reine Typmodule.** Für sie gibt es zur Laufzeit **korrekterweise** keine Kante:

| Modul | Z. | Laufzeit-Exporte | Nutzer | davon `import type` |
|---|---|---|---|---|
| `domain/scene.types.ts` | 406 | 1 (`SCHEMA_VERSION`) | **34** | **34** |
| `app/tools/toolTypes.ts` | 116 | 0 | 17 | **17** |
| `app/tools/werkzeugArten.ts` | 21 | 0 | 5 | **5** |

> **`domain/scene.types.ts` ist das zentrale Datenmodell mit 34 Nutzern.** *Ihm eine Kopfzeile
> „zuletzt geprüft · erwarteter Anschluss · **sonst Stilllegung**" zu geben, wäre grob falsch — und
> genau das würde passieren, wenn der Posten die 27 ungefiltert übernimmt.*

**Gegenprobe gefahren, weil zwei Zahlen zunächst nicht aufgingen:**
`toolTypes` schien einen Nutzer ohne `import type` zu haben — es ist ein **mehrzeiliges**
`import type { … }` (`activation.ts:10-16`), mein Regex sah nur die Schlusszeile. **17 von 17.**

**Die Grundmenge des Postens ist damit 24.**

## Die 24, vollständig eingeordnet

| Gruppe | n | Module | erwarteter Anschluss |
|---|---|---|---|
| **hat bereits ein Blatt** | 6 | `integrationAbgleich` `aufbautenStatus` `grundriss` `wandFlaeche` `auswechslung` `werkzeugRegistry` | `Z1-W2-1` `-2` `-3` `-5` `-6` · `-4` (Probe, **beide Ausgänge offen**) |
| **Paket 2 — Dach** | 7 | `dachAusschnitt` `dachTopologie` `schifterListe` `dachOeffnung` `sparrenTrennung` `dachVorlage` `dachProjektion` | Anschlusswelle, nach Paket 1 |
| **Paket 1 Klasse B — Operand fehlt** | 3 | `wandaufbau` `holzMengen` `holzBauteile` | **offen bei Yama** (`planner-frage-paket-1-operanden`) |
| **Paket 4 — geparkt** | 4 | `treppenTypen` `treppeSvg` `raumProjektion` `heizkreisVerteiler` | *„parken, nicht verwerfen"* (Anschluss-Entscheidung) |
| **Werkzeug-Infrastruktur** | 4 | `auswahlDarstellung` `toolCatalogStillgelegt` `trefferSuche` `werkzeugLandkarte` | **hier ist der Posten wirklich fällig** |
| | **24** | | |

**Die Summe geht auf: 6 + 7 + 3 + 4 + 4 = 24.**

> **Zwanzig der vierundzwanzig haben bereits einen benannten erwarteten Anschluss.** *Für sie ist die
> Kopfzeile eine Eintragung, keine Entscheidung.* **Die eigentliche Arbeit des Postens sind die vier
> Infrastruktur-Module** — und für `toolCatalogStillgelegt` liegt bereits eine Messung vor
> (**VERWERFEN zurückgezogen**: ein echter Test importiert es, `length === 54`), für
> `werkzeugRegistry` läuft mit `Z1-W2-4` gerade die Probe.

## Nebenbefund, bei der Zuarbeit gefunden — benannt, nicht gelöst

**`SCHEMA_VERSION` ist eine Konstante ohne Verbraucher, und die Zahl steht daneben dreimal
hartkodiert:**

```
domain/scene.types.ts:18     export const SCHEMA_VERSION = 3 as const     Importe: 0
domain/validation.ts:284     schemaVersion: z.literal(3)      <- die PRUEFUNG
domain/validation.ts:341     schemaVersion: 3
fixtures/studioFixtures.ts   :81 und :106                     schemaVersion: 3
```

**Das ist eine zweite Wahrheit über die Schema-Version.** *Die Konstante existiert genau dafür, und
niemand benutzt sie; wer auf 4 hebt, muss vier Stellen finden statt einer.* **Und
`domain/validation.ts:6` trägt noch den Kommentar `schemaVersion literal 1`, während der Code `3`
prüft** — der Kommentar ist zweimal überholt worden.

> **Das ist kein Teil dieses Postens und keiner meiner Aufträge.** *Es gehört in den Backlog als
> eigener Schnitt — genannt, damit es nicht ein drittes Mal überholt wird.*

## Empfehlung an den Generator (wenn Welle 1 abgeschlossen ist)

1. **Grundmenge 24, nicht 27.** Die drei Typmodule ausdrücklich mit Begründung ausnehmen — *nicht
   stillschweigend weglassen, sonst fehlt beim nächsten Zählen wieder die Erklärung.*
2. **Für die 20 mit benanntem Anschluss** ist die Kopfzeile eine Eintragung aus der Tabelle oben.
3. **Für die vier Infrastruktur-Module** ist sie eine Entscheidung — und `Z1-W2-4` liefert für eines
   davon gerade den Messbefund. **Erst dessen Ergebnis abwarten.**
4. **Die Erreichbarkeitsmessung neu fahren**, nicht diese Zahlen übernehmen: sobald ein Blatt gebaut
   ist, sinkt die Menge. *Ein Beleg ohne Stand ist kein Beleg — und dieser hier trägt `34494fb7`.*
