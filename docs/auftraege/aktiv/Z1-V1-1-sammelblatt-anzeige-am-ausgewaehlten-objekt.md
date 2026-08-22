# Z1-V1-1 — Sammelblatt Spur V: fünf geprüfte Module bekommen eine Anzeige am ausgewählten Objekt

**ZIEL:** Fünf Module, deren Eingang **der ausgewählte Knoten selbst liefert**, erscheinen im
`EigenschaftenPanel`. **Ein Zuschnitt, eine DoR, ein Bauauftrag, eine Abnahme, ein Transport.**

```yaml
auftrag: "Z1-V1-1"
spur: V
art: "SAMMELBLATT (Nachtrag 1.6). Fester Kriterientext V-1..V-6 — KEINE eigenen Kriterien.
      Fachlogik unberuehrt, neu ist nur der WEG."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
klasse: "Anzeige am ausgewaehlten Objekt — der Eingang liegt vollstaendig im SceneDocument bzw.
         an dem Knoten, den app/rahmen/EigenschaftenPanel.tsx bereits haelt."
zielkomponente: "app/rahmen/EigenschaftenPanel.tsx (576 Z.) — haelt selectedWall, selectedOpening,
                 selectedRoof, selectedStair, selectedStairParams (:66-70)"
module_anzahl: 5
mess_sha: 3daf4f1e
kennung_geprueft: "Z1-V1-1: docs/ 0 Treffer. git log --all --grep 2 Treffer — BEIDE sind
                   ZUWEISUNG, keine Vergabe: 7cf2a2ff (Dirigent, Nachtrag 1.6 in Kraft) und
                   f443f057 (Plan-Pruefer §421, 'Planner fuer das Sammelblatt Z1-V1-1'). Frei."
dor_beleg: "ERTEILT auf den KRITERIENTEXT — plan-pruefer §421, Beleg f443f057, MIT DREI
            HALBSAETZEN (V-1 Aufrufstelle mit Klammer · V-4 mit basis_sha..endstand_sha ·
            V-6 Buendel ausgenommen). Dieses Blatt ist gegen den ERGAENZTEN Text geschnitten.
            Je Blatt prueft der Plan-Pruefer nur Vollstaendigkeit der Belege."
basis_sha: 3daf4f1e
prioritaet: P0
ballbesitz: "plan-pruefer (Belegvollstaendigkeit)"
regelgrundlage: "ARBEITSREGELN-NACHTRAG-1-6-SPUR-V.md (in Kraft 22.08. 18:3x) ·
                 Planner gen 20 Posten 1"
zielreifegrad: "ABGENOMMEN (BROWSER) je Modul"
```

## Die Kriterien — fester Text, nicht je Blatt erfunden

**Maßgeblich ist der Text MIT den drei Halbsätzen aus der DoR des Plan-Prüfers (§421, `f443f057`).**
*Ohne sie messen V-1 und V-4 das Falsche, und V-5 widerspricht V-6.*

| Nr | Kriterium (mit Halbsatz) |
|---|---|
| **V-1** | Aufrufer im Produktivpfad **0 → ≥ 1**; Komponente/Aufrufstelle **mit Pfad** benannt. **Halbsatz: gezählt wird die AUFRUFSTELLE MIT KLAMMER** — nicht das Vorkommen des Namens, nicht der Import. |
| **V-2** | Wirkung im Browser **ausgelöst**; Eingabewert und Ergebnis **wörtlich** genannt |
| **V-3** | **Rot-Probe**: derselbe Bedienweg gegen den Stand **ohne** das Modul → Wirkung fehlt; **Ortsbeleg** |
| **V-4** | Fachlogik unberührt. **Halbsatz: `git diff <basis_sha>..<endstand_sha> -- <modulpfad>` → leer. OHNE BEIDE SHA IST DAS KRITERIUM NICHT ERFÜLLT.** |
| **V-5** | Insel-Suite grün, `tsc` **0**, **Bündel gebaut und mitcommittet** |
| **V-6** | Kein Produktcode außerhalb `resources/planner/hausplaner`, **AUSGENOMMEN das nach V-5 mitgelieferte Bündel `public/hausplaner/hausplaner.js`** |

> **Warum die Halbsätze zählen — je eine Messung des Plan-Prüfers:**
> **V-4:** `git diff` **ohne Referenz** vergleicht Arbeitsbaum gegen Index und ist **nach dem Commit
> immer leer** — auch wenn der Commit die Fachlogik umgeschrieben hat. *Dieselbe Klasse wie die
> Schrittmaß-Prüfung: eine Prüfung, die ihre eigene Voraussetzung misst.*
> **V-6:** `public/hausplaner/hausplaner.js` liegt **nicht** unter `resources/planner/hausplaner` —
> wer das Bündel mitcommittet, verletzt V-6; wer V-6 einhält, verletzt V-5.
> **V-1:** an `integrationAbgleich` gemessen ergaben drei Messwege **drei Zahlen** — Name **5**,
> Import **2**, Aufruf mit Klammer **1**. *Nur der Aufruf mit Klammer ist der Anschluss.*

> **Fällt V-4 bei einem Modul** (die Fachlogik müsste angefasst werden), **ist es kein Spur V** —
> dann fällt es aus diesem Blatt heraus und bekommt ein eigenes. *Das Sammelblatt verbirgt keinen
> Blocker.*

---

## Die fünf Module — je eine Belegzeile

| # | Modul | Z. | Aufrufer / Komponente (Pfad) | Bedienweg (N4) | Registerzeile | Klasse-Begründung |
|---|---|---|---|---|---|---|
| 1 | `geometry/treppenTypen.ts` | 153 | `app/rahmen/EigenschaftenPanel.tsx` — hält `selectedStairParams` (`:70`) | Treppe auswählen → Typ erscheint im Panel | **W-09** Treppe · `registry_id: treppe` | Eingang `TreppenTypEingabe`; **das Panel hält die Parameter bereits** und ruft `berechneTreppe` an `:494` |
| 2 | `geometry/dachVorlage.ts` | 34 | `app/rahmen/EigenschaftenPanel.tsx` — hält `selectedRoof` (`:68`) | Dach auswählen → Vorlagenwerte/Default-Neigung erscheinen | **W-07** Dach aus Kontur · `registry_id: dach`/`kontur` | Eingang `form: DachForm`; **`RoofNode.roofType` liegt im Modell** |
| 3 | `projection/dachProjektion.ts` | 43 | `app/rahmen/EigenschaftenPanel.tsx` (`selectedRoof`) | Dach auswählen → projizierte Fläche erscheint | **W-07** (Entscheidung Posten 18) | Eingang **`scene: SceneDocument`** — der vollständigste Eingang der Klasse, kein fehlender Operand |
| 4 | `geometry/sparrenTrennung.ts` | 67 | `app/rahmen/EigenschaftenPanel.tsx` (`selectedRoof`) | Dach auswählen → Trennbarkeit/Teilstücke erscheinen | **W-21** Sparren und Lattung | Eingang **nur Zahlen** (`vStart`, `vEnd`, `vMinOpen`, `vMaxOpen`) — keine Struktur, die im Modell fehlt |
| 5 | `geometry/dachTopologie.ts` | 183 | `app/rahmen/EigenschaftenPanel.tsx` (`selectedRoof`) | Dach auswählen → Kantentypen erscheinen | **W-27** Dachkantentypen (Entscheidung Posten 18) | Eingang `points`/`edgeConfigs` — **aus `RoofNode.polygon` ableitbar; der Bau misst den Verbraucher** |

**Alle fünf: eigene Testdatei vorhanden, Aufrufer im Produktivpfad heute `0`.**

```
Messbefehl je Modul (V-1 nach Halbsatz 3 — AUFRUFSTELLE MIT KLAMMER):
  grep -rnE '\b<funktion>\(' --include='*.ts' --include='*.tsx' . \
    | grep -v '__tests__' | grep -vE '/<modul>\.ts:'        ->  0

ROTES ERGEBNIS heute, Stand 3daf4f1e — je Modul die gemessene Funktion:
  treppenTypen     treppenTyp()          0
  dachVorlage      dachVorlage()         0
  dachProjektion   projiziereDach()      0
  sparrenTrennung  sparrenTeilstuecke()  0
  dachTopologie    analyzeTopology()     0
Tests: __tests__/<modul>.test.ts vorhanden (fuenfmal)
Gegenprobe BFS ab main.tsx: alle fuenf stehen in der Unerreicht-Liste (27).
```

> **Eine Zwischenmessung war falsch und ist verworfen:** ein erster Zähllauf gab 1–2 Aufrufe je
> Modul. Sein Ausschluss der Definitionsdatei (`^\./geometry/…`) traf nicht, er zählte die
> **modulinternen** Aufrufe mit. **Die breitere Messung gibt fünfmal 0 und deckt sich mit dem
> BFS-Befund.** *Beide Befehle stehen hier, weil eine Zahl ohne ihr Messmuster keine Zahl ist.*

## ⚠ Was NICHT in diesem Blatt steht — und warum

**Das Sammelblatt verbirgt keinen Blocker. Diese Module fallen ausdrücklich heraus:**

| Modul | Grund | wohin |
|---|---|---|
| `geometry/wandaufbau.ts` | **λ-Blocker** — der Operand ist der CRM-Katalog `materials.lambda_w_mk` (Posten 13) | **Paket 5**, Naht Szene→Heizlast |
| `geometry/dachOeffnung.ts` | **`RoofAufbau`/`surfaceId`-Blocker** + das feste `auswechslungErforderlich: true` (Posten 16) | **Dach-2** mit Zimmerer-Linse |
| `geometry/dachAusschnitt.ts` | 531 Z., hängt an `dachOeffnung` | **Dach-2** |
| `geometry/holzMengen.ts` · `holzBauteile.ts` | **Holzliste existiert im Modell nicht** (`domain/`: 0) — Erzeuger ist Dachgeometrie (Posten 15) | **Paket 2** |
| `geometry/schifterListe.ts` | hängt an derselben Holzliste | **Paket 2** |
| `integrationAbgleich` · `aufbautenStatus` · `grundriss` · `wandFlaeche` · `auswechslung` · `werkzeugRegistry` | **haben bereits ein DoR-erteiltes Blatt** (`Z1-W2-1…-6`) — sie sind baubar und werden nicht neu geschnitten | unverändert |
| `trefferSuche` · `auswahlDarstellung` · `werkzeugLandkarte` · `toolCatalogStillgelegt` | **andere Klasse** (Werkzeug-Infrastruktur, kein Anzeigegegenstand am Objekt) | eigenes Sammelblatt |
| `raumProjektion` · `heizkreisVerteiler` · `treppeSvg` | **Paket 4, geparkt** (Anschluss-Entscheidung: „parken, nicht verwerfen") | unverändert geparkt |

> **Warum fünf und nicht acht:** die Obergrenze ist 8, aber **die Klasse trägt nur fünf**. *Ein
> sechstes Modul hineinzunehmen, dessen Eingang nicht am ausgewählten Knoten hängt, würde die
> Klasse auflösen — und der Evaluator müsste je Modul einen anderen Bedienweg fahren.*

## N4 — Bedienweg der Klasse

| | |
|---|---|
| **Auslöser** | ein Objekt wird ausgewählt (Treppe bzw. Dach) |
| **Ort** | `app/rahmen/EigenschaftenPanel.tsx` — **die Komponente existiert und hält die Knoten bereits** |
| **kein** | Leisteneintrag, kein Menüpunkt, keine neue `toolRegistry`-Kennung |
| **Zielreifegrad** | `ABGENOMMEN (BROWSER)` **je Modul** — V-2/V-3 fährt der Evaluator fünfmal |

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Modul | V-1 | V-2 | V-3 | V-4 | V-5 | V-6 |
|---|---|---|---|---|---|---|
| `treppenTypen` | n.U. | n.U. | n.U. | n.U. | n.U. | n.U. |
| `dachVorlage` | n.U. | n.U. | n.U. | n.U. | n.U. | n.U. |
| `dachProjektion` | n.U. | n.U. | n.U. | n.U. | n.U. | n.U. |
| `sparrenTrennung` | n.U. | n.U. | n.U. | n.U. | n.U. | n.U. |
| `dachTopologie` | n.U. | n.U. | n.U. | n.U. | n.U. | n.U. |

**V-5 und V-6 gelten für die Lieferung als Ganzes** (eine Suite, ein `tsc`, ein Bündel, ein Diff) —
**V-1 bis V-4 je Modul einzeln.**

## Rückweg

**Revert des einen Bau-Commits.** Kein Zustand entsteht: die Fachlogik bleibt unverändert (V-4), die
Anzeige ist additiv, das Datenmodell wird nicht erweitert. *Fällt ein einzelnes Modul in der Abnahme
durch, fällt nur seine Zeile — der Rest der Lieferung bleibt, und das durchgefallene Modul bekommt
ein eigenes Blatt.*
