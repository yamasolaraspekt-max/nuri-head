# ROLLENKETTE 3D-HAUSPLANER

> Ein Wissensspeicher, **sechs** Sichten, **fünf** Übergabestücke.

---

## Der Grundgedanke

Sechs gleich gebaute Wissensordner **driften** — sie müssen gepflegt werden, um
übereinzustimmen. Das ist in diesem Projekt zweimal passiert: die Auftragsblätter
führten einen zweiten Status neben `STATUS.md`, der Git-Index trug eine zweite
Wahrheit neben dem Arbeitsbaum. Beide Male war die Lösung nicht „besser abgleichen",
sondern **eine Wahrheit statt zwei**.

Deshalb ist es hier umgedreht:

```
werkbank/       DER GEGENSTAND — gehört niemandem, alle lesen dasselbe
rollen/         SECHS SICHTEN — je 5 Blätter, kein eigenes Sachwissen
uebergaben/     FÜNF STAFFELSTÄBE — halten die Kette zusammen
```

---

## Aufbau

```
rollenkette/
│
├── werkbank/                    ← das Sachwissen. EINMAL.
│   ├── 00-ARCHITEKTUR/          Pflichtentscheidungen, Schichten
│   ├── 01-MATHEMATIK/           F-001 … F-nnn, jede genau einmal
│   ├── 02-WERKZEUGE/            W-01 … W-nn, je 7 Blätter
│   ├── 04-QUELLEN/              belegte Fundstellen aus dem Netz
│   └── 05-MATERIALQUELLEN/      was aus Yamas eigenem Bestand stammt
│
├── rollen/                      ← wer tut was. KEIN zweites Sachwissen.
│   ├── 1-planner/
│   ├── 2-plan-pruefer/
│   ├── 3-generator/
│   ├── 4-evaluator/
│   ├── 5-release-pruefer/
│   └── 6-integrator/         ← NEU, Entscheidung B-2 (14.08.)
│       je: 1-AUFTRAG · 2-WANN-BIN-ICH-DRAN · 3-WAS-ICH-LESE
│           · 4-WAS-ICH-ABLIEFERE · 5-WAS-ICH-NICHT-DARF
│
└── uebergaben/                  ← die Staffelstäbe
    ├── A-auftragsblatt.md
    ├── B-baubericht.md
    ├── C-abnahmevotum.md
    ├── D-freigabeschein.md
    └── E-integrationsprotokoll.md   ← NEU (Integrator)
```

---

## Die Kette

```
   Planner ──── A ────► Plan-Prüfer ──── A freigegeben ────► Generator
                                                                 │
                                                                 B
                                                                 ▼
   Yama ◄──── D ──── Release-Prüfer ◄──── C ──── Evaluator ◄─────┘
```

**Ein Übergabestück, zwei Rollen.** Was der Planner in `A` schreibt, ist exakt das
Blatt, das der Plan-Prüfer prüft und der Generator baut. Kein Abgleich nötig —
es ist dasselbe Dokument.

| Stück | Von | An | Enthält |
|---|---|---|---|
| **A** Auftragsblatt | Planner | Plan-Prüfer → Generator | Werkzeug, Basis-SHA, Kriterien mit Rot-Beleg, Grenzen |
| **B** Baubericht | Generator | Evaluator | Was gebaut wurde, Bau-SHA, Selbstmessung, offene Punkte |
| **C** Abnahmevotum | Evaluator | Release-Prüfer | Grün/Rot je Kriterium mit Beleg, Fehlerklasse, Prüf-SHA |
| **D** Freigabeschein | Release-Prüfer | Yama | Release-SHA, §10-Prüfung, Rückweg, Restrisiko |
| **E** Integrationsprotokoll | **Integrator** | Yama | Ursprungscommit, Ziel-HEAD vorher/nachher, berührte Pfade, Übernahmen **und** Ablehnungen |

---

## Die drei Eigenschaften — und wie sie erzwungen werden

| Anforderung | Mechanismus | Wo geprüft |
|---|---|---|
| **Kausalität** | Jedes Übergabestück nennt seinen Vorgänger **per SHA**. Keine Übergabe ohne Herkunft. | Kopf jedes Übergabestücks |
| **Plausibilität** | Jede Zahl trägt ihre Messung mit — Datei:Zeile oder Befehlsausgabe. Keine Zahl ohne Beleg. | Feld „gemessen an" |
| **Konsistenz** | Nicht durch Abgleich, sondern durch **Einmaligkeit**: jede Tatsache steht an genau einem Ort, alles andere verweist. | Werkbank-Regel |

> **Die Konsistenz-Regel im Klartext:** Eine Formel steht in `01-MATHEMATIK`.
> Ein Werkzeug verweist auf ihre Nummer. Ein Auftragsblatt verweist auf das
> Werkzeug. Ein Votum verweist auf das Auftragsblatt. **Niemand schreibt ab.**
> Wer abschreibt, erzeugt die zweite Wahrheit, die später driftet.

---

## Was hier NICHT steht

- **Der Prozess** — Zustände, Abnahmeregeln, Commit-Disziplin: `docs/ARBEITSREGELN.md`
- **Der Status** — wo ein Auftrag steht: `docs/STATUS.md`
- **Der Code** — im Repo. Hier stehen nur Verweise darauf.
