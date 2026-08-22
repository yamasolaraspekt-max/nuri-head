# Paket 2 (Dach) — der Zuschnitt, und warum die empfohlene Reihenfolge sich dreht

```yaml
art: "ZUSCHNITT — Messung und Aufteilung vor dem Schneiden der Blaetter. Kein Bau,
      keine Fachentscheidung."
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 19, Posten 7 — 'dann Dach-2'"
mess_sha: 49141f90
quelle: "anschluss-entscheidung-2026-08-22.md — Paket 2, 7 Module, 1106 Zeilen"
ergebnis: "3 Module gehoeren an EIN Werkzeug (W-29) — dort liegt bereits Z1-W2-6.
           Die Reihenfolge 'dachOeffnung zuerst' traegt nicht."
```

## Die sieben Module, gemessen am Stand `49141f90`

| Modul | Z. | eigene Tests | echte Importeure | Register-Träger |
|---|---|---|---|---|
| `geometry/dachAusschnitt.ts` | 531 | 1 | 0 | **W-29** (7) · W-07 (3) · W-30 (2) |
| `geometry/dachTopologie.ts` | 183 | 1 | 0 | W-27 (1) — *schwach* |
| `geometry/schifterListe.ts` | 152 | 1 | 0 | **W-25** (5) · W-43 (3) · W-27 (3) |
| `geometry/dachOeffnung.ts` | 96 | **0** | 1 (`dachAusschnitt`) | **W-29** (7) · W-07 (2) |
| `geometry/sparrenTrennung.ts` | 67 | 1 | 0 | **W-21** (3) · W-29 (2) |
| `projection/dachProjektion.ts` | 43 | 1 | 0 | W-07 (1) — *schwach* |
| `geometry/dachVorlage.ts` | 34 | 1 | 0 | **W-30** (4) · W-07 (3) |

**Abhängigkeiten innerhalb des Pakets — es gibt genau eine:**

```
dachAusschnitt  ->  dachOeffnung        (die einzige interne Kante)
die uebrigen sechs sind untereinander unabhaengig

Vorleistungen AUSSERHALB des Pakets, alle ERREICHBAR:
  dachOeffnung    -> aufbauPlatzierung
  dachAusschnitt  -> polygonFlaeche · linienBauteile · gaubeGeometrie
```

> **Die Vorleistungen sind angeschlossen — nur die sieben Dach-Module nicht.** *Es fehlt kein
> Fundament; es fehlt der letzte Meter.*

## ⚠ Befund 1 — die empfohlene Reihenfolge trägt nicht

**Die Anschluss-Entscheidung sagt:** *„in kleinen Schritten — `dachVorlage` und `dachOeffnung`
(130 Z.) zuerst, `dachAusschnitt` zuletzt."*

**`dachOeffnung`s einziger Verbraucher ist `dachAusschnitt`.** *Wer `dachOeffnung` zuerst
anschließt, schließt ein Modul an, dessen Abnehmer noch nicht da ist.* **Und `dachAusschnitt` ist
selbst unerreichbar** — es steht in derselben 27er-Liste. *Ein Import von einem unerreichbaren
Modul ist kein Ladeweg.*

| | |
|---|---|
| **`dachVorlage` zuerst** | **hält** — 34 Zeilen, keine Abhängigkeit, keine Abnehmerfrage |
| **`dachOeffnung` zuerst** | **hält nicht** — entweder mit `dachAusschnitt` zusammen, oder es braucht einen **neuen** Verbraucher, den dieses Blatt dann benennen muss |

**Das ist keine Korrektur der Entscheidung, sondern eine Messung, die sie braucht.** *Die
Entscheidung wollte klein anfangen — richtig; nur ist `dachOeffnung` nicht das kleine Stück.*

## ⚠ Befund 2 — drei Module zeigen auf **ein** Werkzeug, und dort liegt schon ein Blatt

**W-29 „Dachdurchdringungen" ist Träger von:**

```
geometry/auswechslung.ts    -> Z1-W2-6   BEREITS GESCHNITTEN (49141f90)
geometry/dachOeffnung.ts    -> W-29 in ALLEN SIEBEN Blattteilen
geometry/dachAusschnitt.ts  -> W-29 in ALLEN SIEBEN Blattteilen
geometry/sparrenTrennung.ts -> W-29 mit 2 Stellen (Traeger ist aber W-21)
```

**Die Regel lautet „ein Werkzeug = ein Blatt" (gen 19 Posten 4).** *Drei Module an W-29 lassen sich
nicht als drei Blätter schneiden, ohne sie zu brechen.* **Und fachlich gehören sie zusammen:** alle
drei rechnen an derselben Sache — einem Loch im Dach und dem, was danach trägt.

**Zur Entscheidung, nicht von mir zu treffen:**

| Weg | was er bedeutet |
|---|---|
| **A — `Z1-W2-6` erweitern** | ein Blatt für W-29, das `auswechslung` **und** `dachOeffnung` trägt. *Größer, aber die Regel bleibt heil.* |
| **B — `Z1-W2-6` bleibt, `dachOeffnung` bekommt W-07** | zweitstärkste Zuordnung (2 Stellen). *Kleiner, aber die Zuordnung wird nach Bedarf statt nach Messung gewählt — genau das, was ich bei `W-16`/`grundriss.ts` abgelehnt habe.* |
| **C — ein W-29-Sammelblatt** | `auswechslung` + `dachOeffnung` + `dachAusschnitt` in einem Schnitt. *Fachlich am saubersten, aber 822 Zeilen — kein kleiner Schritt mehr.* |

**Meine Empfehlung: A.** *`dachAusschnitt` bleibt draußen (531 Z., das größte Modul des Pakets), aber
`dachOeffnung` und `auswechslung` gehören in denselben Schnitt — der nächste Befund zeigt, warum.*

## ⚠ Befund 3 — das Werkzeugblatt W-29 hat den Grund bereits ausgearbeitet

**Ich zitiere statt nachzubauen** (P-02 Punkt 4), `W-29-dachdurchdringungen/1-ZWECK.md`:

```text
auswechslung.ts   analysiereAuswechslung(...)  ->  wechselErforderlich   GERECHNET
dachOeffnung.ts:91  auswechslungErforderlich: true                       FEST VERDRAHTET

Produktivaufrufer von analysiereAuswechslung   NULL
Aufrufer von oeffnungRechteck                  dachAusschnitt.ts:303
```

> **Den tragenden Punkt habe ich am Code nachgeprüft, nicht übernommen:** `dachOeffnung.ts:91` trägt
> `auswechslungErforderlich: true` — **die Zeilenangabe stimmt heute noch.**
> **Die übrigen Zahlen des W-29-Blattes sind gealtert**, und das ist zu vermerken, bevor jemand sie
> zitiert:
>
> ```
>                         W-29-Blatt      heute (49141f90)
> auswechslung.ts            174 Z.          195 Z.
>   analysiereAuswechslung     :87            :108
> dachAusschnitt.ts          510 Z.          531 Z.
>   Aufruf oeffnungRechteck    :303           :324
> ```
>
> *„Eine Zeilennummer ist kein Beleg, sondern ein Verfallsdatum."* **Der Befund selbst ist davon
> unberührt** — er hängt an `auswechslungErforderlich: true`, und das steht unverändert da.

> *„Das behauptende Modul ist das, das läuft. `dachAusschnitt` ruft `oeffnungRechteck` — und bekommt
> `auswechslungErforderlich: true`, immer, unabhängig von der Lage der Öffnung. **Die Rechnung, die
> es besser weiß, wird von keinem Produktivpfad angefasst.** … Eine Warnung, die immer kommt, ist
> die, die weggeklickt wird (A-03). Und sie kommt hier nicht aus Vorsicht, sondern aus einer nicht
> gezogenen Leitung."*

**Eine Präzisierung, die meine Messung beisteuert und die den Befund NICHT abschwächt:**
*„das Modul, das läuft"* meint **innerhalb der Geometrie**. **Für den Anwender läuft heute keines
von beiden** — `dachAusschnitt` steht selbst in der 27er-Liste. *Das W-29-Blatt sagt es an anderer
Stelle selbst: „OBERFLÄCHE FEHLT — kein Verbraucher außerhalb der Geometrie."*
**Beide Wege sind unerreichbar — der behauptende und der rechnende.**

> **Und das W-29-Blatt zieht die Grenze, die auch für uns gilt:** *„Ob sie angeschlossen wird, ist
> eine **Fachentscheidung** und kein Ablesevorgang — sie ändert eine Auskunft an den Anwender über
> **Statik**."* **Nach CLAUDE.md ist das ein Rückfrage-Fall.** *Der Anschluss selbst ist es nicht —
> aber `auswechslungErforderlich: true` durch die Rechnung zu **ersetzen**, ist es.*

## Was ich empfehle

| | |
|---|---|
| **zuerst schneiden** | `dachVorlage` → **W-30**. 34 Zeilen, keine Abhängigkeit — der wirklich kleine Schritt. **Aber:** das W-30-Blatt sagt *„hier fehlt nicht der Anschluss, sondern die RECHNUNG"* — **vor dem Schnitt zu prüfen**, sonst hängt man ein Modul an ein Werkzeug, dem etwas anderes fehlt. |
| **dann** | `Z1-W2-6` um `dachOeffnung` erweitern (Weg A) — **Entscheidung des Dirigenten**, weil es ein bereits geschnittenes Blatt vergrößert. |
| **eigener Schnitt, später** | `dachAusschnitt` (531 Z.) · `schifterListe` (hängt an der Holzlisten-Frage) |
| **Zuordnung offen** | `dachTopologie` (W-27, **1** Stelle) und `dachProjektion` (W-07, **1** Stelle) — *dieselbe schwache Lage wie `grundriss.ts` in `Z1-W2-3`; die Zuordnung wäre eine Entscheidung, keine Messung.* |
| **nicht tun** | `dachOeffnung` allein anschließen. *Es hätte keinen Abnehmer.* |

## Zwei Dinge, die jedes Paket-2-Blatt tragen muss

1. **Fach-Linse ist Pflicht vor der Freigabe** (Anschluss-Entscheidung, Paket 2): *„Dachgeometrie ist
   die Ecke mit den meisten Fach-Linsen. Ein falscher Schifterschnitt ist teurer als ein fehlender."*
2. **`dachOeffnung` hat keine eigene Testdatei** — geprüft wird es mittelbar in
   `__tests__/dachformVorlagen.test.ts`. **Dieselbe Lage wie `grundriss.ts` in `Z1-W2-3`**, und sie
   braucht dort dasselbe eigene Kriterium: *die fremde Suite muss grün bleiben und namentlich im
   Bericht stehen.*
