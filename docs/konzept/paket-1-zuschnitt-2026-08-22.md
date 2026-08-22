# Paket 1 (Massenermittlung) — der Zuschnitt, und warum es nicht fünf gleichartige Blätter werden

```yaml
art: "ZUSCHNITT — Messung und Aufteilung vor dem Schneiden der Blaetter. Kein Bau, keine Entscheidung
      ueber Operanden."
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 19, Posten 4/7 — 'Paket 1 (je Werkzeug)'"
mess_sha: 6d8e819f
quelle: "docs/konzept/anschluss-entscheidung-2026-08-22.md — Paket 1, 5 Module, 705 Zeilen"
ergebnis: "2 Module baubar · 3 Module mit fehlendem Operanden (Frage an Yama, nicht Blatt)"
```

## Warum dieses Blatt vor den Auftragsblättern steht

Der Auftrag lautet *„Paket 1 (je Werkzeug)"* — fünf Module, also der Erwartung nach fünf Blätter.
**Die Messung ergibt etwas anderes, und es ist besser, das vor dem Schneiden zu sagen als danach:**
drei der fünf können heute nicht gebaut werden, **und der Bestand hat das bereits einmal gemessen
und begründet zurückgegeben.**

> *Fünf Blätter zu schneiden, von denen drei an einer bekannten Wand enden, wäre Arbeit, die
> aussieht wie Fortschritt.*

## Die fünf Module, gemessen am Stand `6d8e819f`

| Modul | Z. | eigene Tests | echte Importe im Produktivpfad | Engine-Kennung | Register-Träger |
|---|---|---|---|---|---|
| `geometry/wandFlaeche.ts` | 253 | 1 | **0** | — | **W-02** (5 Stellen) |
| `geometry/auswechslung.ts` | 195 | 1 | **0** | — | **W-29** (alle 7 Blattteile) |
| `geometry/holzBauteile.ts` | 99 | 1 | **0** | `engine-holzbauteile` | **W-25** (6 Stellen) |
| `geometry/holzMengen.ts` | 81 | 1 | **0** | `engine-holzmengen` | **W-20** (alle 7 Blattteile) |
| `geometry/wandaufbau.ts` | 96 | 1 | **0** | `engine-uwert` | W-02 / W-10 / W-22 |

**Alle fünf haben eine eigene Suite. Keines hat einen Laufzeit-Import im Produktivpfad.**

> **Eine Zwischenmessung, die ich fast falsch übernommen hätte:** ein Suchlauf über die *Exportnamen*
> meldet für `holzMengen`, `holzBauteile` und `wandaufbau` je ein bis zwei „Aufrufer". **Es sind
> keine.** `app/tools/faehigkeiten.ts` nennt die Namen als **Zeichenketten in einer Deklaration**
> (`engineModul`, `engineExport`) und hat **0** Importe aus `geometry/`; `geometry/schifterListe.ts`
> nennt `holzBauteile` in **zwei Kommentarzeilen**.
> **Gemessen über `from '…<modul>'` sind es bei allen fünf 0.** *„Der Ort ist nicht die Wirkung" —
> dieselbe Regel, an der ich schon einmal vorbeigelesen habe.*

## Der Befund, der den Zuschnitt bestimmt: drei von fünf sind schon einmal zurückgegeben worden

**`app/dashboard/enginePanels.ts` ist der gebahnte Weg** — 8 der 13 deklarierten Engines haben dort
eine Fläche. **Fünf wurden mit Messung zurückgegeben**, und die Auftragstafel nennt die Ursache:

> *„**8 von 13 Engines haben eine Fläche, 5 mit Messung zurückgegeben** — **vier davon an derselben
> Ursache: die Hülle übergibt Felder, diese Engines brauchen Listen** (Holzliste ×2, Heizkreisliste,
> Schichtliste); die fünfte eine Ergebnisform ohne Zahlen."*
> — `docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`, AUF-52

**Drei dieser vier sind Paket-1-Module.** Der Vertrag der Hülle ist gemessen:

```
EngineFeld     (enginePanels.ts:34)  schluessel · label · einheit · pflicht
                                     vorgabe?: number · auswahl?: {wert,label}[]
                                     -> je Feld EIN Skalar
berechne       (enginePanels.ts:70)  (werte: Record<string, string>) => EngineErgebnis
                                     -> flache Zeichenketten

verlangte Eingaenge (faehigkeiten.ts)
  engine-uwert         Schicht[]
  engine-holzmengen    HolzStueck[]
  engine-holzbauteile  Holzliste
```

*Die Hülle kann keine Liste entgegennehmen. Das ist kein Versäumnis des Anschlusses, sondern eine
Eigenschaft ihres Vertrags — und der Generator hat sie damals richtig benannt statt sie zu umgehen.*

## Zwei Klassen, und die Trennlinie ist der Operand

### Klasse A — baubar (2 Module)

**Beide brauchen keine Liste, die es nicht gibt, und keinen Fachwert, den niemand kennt.**
*Die Begründung ist bei den beiden aber nicht dieselbe, und die erste Fassung dieses Blattes hat das
verschliffen — berichtigt:*

**`wandFlaeche` (`wandMengen`) — der Eingang ist unmittelbar Modell.**

```
wandMengen(wand: WallNode, oeffnungen: readonly OpeningNode[], bezug: Bezugsmass)
WallNode · OpeningNode  ->  domain/scene.types.ts, beide im SceneDocument gefuehrt
Bezugsmass 'roh'|'fertig' -> PFLICHT, eine NUTZERWAHL (wandFlaeche.ts:38, :47)
```

**`auswechslung` — die Maße sind ableitbar, ein Wert ist es nicht.**

```
analysiereAuswechslung(flaeche: FlaecheMasse, oeffnung: Oeffnung, rafterDistM: number, opts?)
FlaecheMasse · Oeffnung  ->  EIGENE Modultypen, aus RoofNode/RoofAufbau ableitbar
rafterDistM              ->  STEHT NICHT IM SceneDocument (0 Treffer in domain/)
```

> **Der Sparrenabstand ist trotzdem kein fehlender Operand — er wird bereits erfragt:**
> `enginePanels.ts:184` führt `sparrenabstandM` als **Pflichtfeld mit Vorgabe `0.8` m** für die
> Sparren-Engine; `geometry/dachformVorlagen.ts` trägt Vorlagenwerte (8× `70`, 1× `62.5` cm).
> **Ein bekannter, üblicher Wert mit vorhandener Vorgabe ist etwas anderes als ein Fachwert ohne
> Quelle (λ) oder eine fehlende Struktur (Holzliste).** *Die Trennlinie zu Klasse B hält — aber sie
> verläuft an der Verfügbarkeit des Operanden, nicht am Satz „liegt im Modell".*

→ **Zwei Anschlussblätter, `Z1-W2-5` (W-02) und `Z1-W2-6` (W-29)**, nach dem Muster von Paket 3.
**`Z1-W2-6` muss den Sparrenabstand ausdrücklich als erfragten Wert führen**, nicht als Modellwert.

### Klasse B — Operand fehlt (3 Module). **Das ist eine Frage, kein Blatt.**

**1 · `wandaufbau` / U-Wert — zwei offene Operanden, und der Bestand nennt beide selbst.**

> ## ⚠ BERICHTIGUNG 22.08. abends — „kein Materialkatalog" war **falsch gemessen**
>
> ~~`lambda` im Bestand: NUR in `geometry/wandaufbau.ts` — KEIN Materialkatalog.~~
>
> **Das galt für die Insel und wurde als Aussage über den Bestand geschrieben.** Nachgemessen:
>
> ```
> lambda in der Insel (resources/planner/hausplaner)     3 Dateien   (nicht 1)
> lambda im CRM-Bestand (app/ database/ config/ …)      10 Dateien
>
> DER KATALOG EXISTIERT:
>   app/Models/Material.php
>   database/migrations/2026_07_05_170001_create_materials_table.php
>   database/seeders/ReferenzKatalogSeeder.php · database/data/b2a_referenz.php
>
> UND DER RECHENWEG AUCH:
>   app/Services/Heizlast/UWertService.php  "Strategie B – Schichtaufbau"
>     @param array{material?: string, lambda?: float, dicke_mm: float|int} $schichten
>     $lambda = $m->lambda_w_mk;
> ```
>
> **`UWertService` nimmt `dicke_mm` — dasselbe Feld, das `scene.types.ts:133` als `dickeMm`
> führt.** *Der Übergang ist fast passgenau; was fehlt, ist die Verbindung, nicht die Rechnung.*
>
> **Damit ist meine Frage an Yama falsch gestellt gewesen.** Sie lautete *„Materialkatalog anlegen
> — und woraus?"*. **Richtig gestellt lautet sie: ANBINDEN ODER NICHT.**
> *Den Befund haben der Plan-Prüfer (16:30:47) und die lesende Sitzung (16:44:21) unabhängig
> gemeldet; ich zitiere sie, statt sie nachzubauen — die Reichweitenmessung oben ist meine eigene.*
>
> **Was die Frage NICHT einfacher macht:** die Anbindung Insel → CRM-Service ist eine
> **Architekturfrage** (CLAUDE.md: React/TypeScript bleibt auf die Insel begrenzt), und ob
> **Heizlast**-Referenzwerte für eine **Wandaufbau**-Anzeige fachlich taugen, entscheidet Yama.
> *Die zweite Frage — die Schichtreihenfolge innen→außen — bleibt unberührt offen.*

```
domain/scene.types.ts:133   schichten?: Array<{ materialId?: string; dickeMm: number }>
geometry/wandaufbau.ts:9    interface Schicht { … dicke … lambda … }
```

> `scene.types.ts:126` sagt es wörtlich: *„**Nicht** `geometry/wandaufbau.Schicht`: das ist ein
> Rechentyp mit `dicke` und `lambda`, dies ist ein Modellfeld mit `dickeMm`. **Sie zusammenzuziehen
> ist eine eigene Entscheidung.**"*
> Und `:130`: *„Die Reihenfolge trägt (noch) keine Bedeutung … **Sie ist als Frage zurückgegeben;
> bis sie beantwortet ist, darf sich niemand auf die Reihenfolge verlassen.**"*

**Beide Operanden sind Fachentscheidungen:** λ je Material ist ein Bauphysikwert, die Reihenfolge
innen→außen bestimmt `rsi`/`rse`. **Nach CLAUDE.md ist das ein Rückfrage-Fall, keine stille
Automatisierung.** *Ein U-Wert aus geratenen λ-Werten sähe aus wie eine Angabe — genau das, wovor
der Modellkommentar warnt.*

**2 · `holzMengen` und `holzBauteile` — die Liste existiert im Modell nicht.**

```
'HolzStueck' / 'HolzStück' in domain/   0
'Holzliste'                in domain/   0    (in geometry/: 5)
```

**Es fehlt nicht die Anzeige, sondern der Erzeuger.** *Wer erzeugt die Holzliste aus dem
Dachstuhl — und ist das Paket 2 (Dach) oder ein eigener Schnitt?* **Das ist eine Reihenfolgefrage,
keine Bauaufgabe.**

## Empfehlung

| | |
|---|---|
| **jetzt schneiden** | `Z1-W2-5` `wandFlaeche` → W-02 · `Z1-W2-6` `auswechslung` → W-29 |
| **als Frage vorlegen** | U-Wert-Operanden (λ-Katalog, Reihenfolge) — `planner-frage-paket-1-operanden` |
| **an Paket 2 hängen** | `holzMengen`, `holzBauteile` — der Erzeuger der Holzliste gehört zum Dach |
| **nicht tun** | drei Blätter schneiden, deren Messbefehle heute an einer bekannten Wand enden |

> **Was diese Empfehlung nicht ist:** eine Entscheidung über die Operanden. **Die liegt bei Yama**,
> und die Frage geht ihm ungekürzt zu. *Der Auftrag „Paket 1, je Werkzeug" bleibt gültig — er wird
> hier nicht verkleinert, sondern in der Reihenfolge sortiert, die die Messung hergibt.*

## Was noch offen ist und hier nur benannt wird

- **`berechneUWert` steht in KEINEM Werkzeugblatt namentlich** (0 Treffer über alle
  `02-WERKZEUGE/`-Blätter). Der Wortstamm `wandaufbau` trifft W-02/W-10/W-22. *Dieselbe Lücke wie
  bei `grundriss.ts` in `Z1-W2-3`: das Register kennt Module über den Namen, nicht über die
  Funktion.*
- **`UWERT_VORBEHALT`** (`wandaufbau.ts:57`) muss dort erscheinen, wo die Zahl erscheint — die
  Anschluss-Entscheidung führt das als Punkt 5. **Gilt, sobald der U-Wert überhaupt baubar ist.**
