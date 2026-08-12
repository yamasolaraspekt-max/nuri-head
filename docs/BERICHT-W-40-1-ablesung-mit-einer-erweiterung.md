# Baubericht W-40/1 — die Blätter lesen jetzt ab, statt vorzugeben

```yaml
auftrag: "W-40/1"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-40-1-ablesung-mit-einer-erweiterung.md
art: "NACHBESSERUNG an meiner eigenen Arbeit — W-40s sieben Blaetter sind von mir,
      und beide Fehler, die dieser Auftrag behebt, sind meine."
basis_sha: 2e7504ec
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Ein Befund gegen mich selbst steht in diesem Bericht, und er betrifft nicht das Blatt, sondern
> meine eigene Fehlerliste:** *mein Befund `ea418041` nennt **vier** betroffene Blätter — es sind
> **fünf**.* **Abschnitt „Meine Liste war unvollständig" unten.**

## Was geändert wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-40-gueltigkeitsstatus/
  1-ZWECK.md · 2-FUNKTION.md · 3-FORMELN.md · 4-BEDIENUNG.md
  5-CODE/LIESMICH.md · 6-PRUEFUNG.md · 7-GRENZEN.md          alle SIEBEN
REGISTER.md   Zeile 127:  ENTWORFEN -> BESCHRIEBEN
```

**Kein Produktivcode** *(W-40/1-8)*: `git status --porcelain resources/ app/` → **0**.

## W-40/1-1 · Dreizehn Stellen, keine gelöscht

**Jede überholte Stelle steht weiter lesbar da, gekennzeichnet mit Datum und Yamas Zuordnung.** *Die
Begründung ist die des Auftrags und sie hat sich heute den ganzen Tag bewährt:* **ein nachträglich
umgeschriebenes Blatt ist kein Beleg mehr.**

*Die Form, in allen fünf Blättern dieselbe:*

```text
BERICHTIGT (W-40/1, 12.08.)   Hier stand: „<der alte Wortlaut>"
                              <was heute gilt, mit Fundstelle>
                              <und was am alten Satz richtig bleibt>
```

> **Der dritte Teil ist der, den ich mir angewöhnt habe.** *Fast jede der dreizehn Stellen war zur
> Hälfte richtig — `3-FORMELN:33` rechnete `4 + 3 = 7` und die Rechnung stimmt; falsch war die
> Folgerung.* **Wer nur „falsch" schreibt, löscht die richtige Hälfte mit.**

## Der TRÄGER — die zweite Hälfte meines Fehlers, an drei Stellen

| Blatt | Stand vorher | Stand jetzt |
|---|---|---|
| `2-FUNKTION` | `fortschritt` und `gueltigkeit` **nebeneinander** | zwei Achsen an **ZWEI TRÄGERN** |
| `5-CODE` | `interface IrgendeinTraeger { fortschritt; gueltigkeit }` | `Schritt → SchrittStatus` · `Paket → ConfiguratorStatus` |
| `6-PRUEFUNG` B-1 | „ein EIGENES Feld **neben dem Fortschritt**" | „an SEINEM EIGENEN TRÄGER" + zweite Rot-Probe |

**Selbst nachgemessen, jede Zeile geöffnet:**

```text
resources/planner/hausplaner/geometry/configuratorPackage.ts
  :25-26   export type ConfiguratorStatus = 'draft' | … | 'outdated'   SIEBEN
  :72      status: ConfiguratorStatus;      in export interface ConfiguratorPackage
  :103     export const STATUS_UEBERGAENGE: Readonly<Record<…>>
  :107     checked:    ['draft', 'approved', 'generated']
  :120     export function kannIntegrieren(paket: Pick<…,'status'>): boolean
  :125     export function markiereVeraltet<T>(paket, jetzt, durch): T

resources/planner/hausplaner/app/studioDaten.ts
  :163     export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
```

> **`:72` ist die Zeile, die den ganzen Auftrag trägt** — *das Statusfeld sitzt im
> `ConfiguratorPackage`, nicht im Schritt.* **Der Plan-Prüfer hat sie in seiner DoR genannt; ich habe
> sie nicht übernommen, sondern die Datei geöffnet und das umschließende `export interface` mit
> gelesen.** *Eine Zeilennummer ohne ihren Block sagt nicht, wozu das Feld gehört.*

## W-40/1-1b · K-3 und K-4 sind UMGESTELLT, nicht gestrichen

**Der Auftrag nennt das den gefährlichsten Teil, und das trifft.** *Ich hatte beide Kriterien zuerst
durchgestrichen und als „erledigt" markiert — das ist nicht, was dort steht.*

```text
verlangt   von 'Frage stellen' auf 'Yamas Antwort tragen, mit Fundstelle'
mein 1. Griff   Durchstreichung + „ERLEDIGT"
berichtigt      K-3 und K-4 stehen wieder als aktive Kriterien, mit umgedrehtem
                Inhalt und je einem neuen Fehlerfall
```

> **Der Unterschied ist nicht kosmetisch.** *Ein gestrichenes Kriterium prüft nichts.* **Ein
> umgestelltes prüft, dass Yamas Antwort im Blatt steht — und sein Fehlerfall lautet jetzt: „die
> Rechnung `4 + 3 = 7` als offene Frage stehen lassen".** *Damit fängt K-3 genau den Zustand, aus dem
> dieser Auftrag entstanden ist.*

**Das ist Pflichtprüfung „Kriterien wörtlich lesen und NIE still ersetzen" gegen mich selbst
angewandt** — *ich hatte sinngemäß gelesen und dabei das Verb verloren.*

## W-40/1-5 · Vier Merkmale, und meines war das falsche

**Der Auftrag nennt: *worauf gewartet wird · Ebene · ORT · Aufhebung*.** *Meine erste Tabelle führte
`worauf · Ebene · **Adressat** · Aufhebung` — der Adressat steht in Yamas Antwort, aber nicht in den
vier geforderten.*

| Merkmal | `DECISION_BLOCKED` | `blocked` |
|---|---|---|
| worauf gewartet wird | **Mensch** | **Bedingung** |
| Ebene | Prozess | Produkt |
| **Ort** | `docs/STATUS.md` | das Gebäudemodell |
| Aufhebung | nur durch Yama, nie maschinell | automatisch |

*Der Adressat steht jetzt als ausdrücklich fünftes daneben.* **Vier gefordert, vier geliefert, das
fünfte gekennzeichnet — nicht vier geliefert und eines davon ausgetauscht.**

## W-40/1-4 · Yamas zwei Auflagen, wörtlich, an zwei Stellen

```text
7-GRENZEN   „blocked traegt seinen Grund mit, denn ein blocked ohne blockiert_durch
             ist eine Absage ohne Erklaerung."
            „blocked wird NIE von Hand gesetzt oder geloest, wer das will meint
             DECISION_BLOCKED."
4-BEDIENUNG Aufruf-Tabelle, Zeile blocked: GAR NICHT, mit derselben Auflage
```

**Und der mitzuführende Satz, in Yamas Wortlaut: „dieser hier löst sich ohne mich"** — *mit dem
Zusatz, dass er an `blocked` gehört und nicht an `DECISION_BLOCKED`.*

## W-40/1-3 · Die Null, die ich zweimal messen musste

```text
1. Versuch   grep -rn "blocked" resources/planner/hausplaner --include=*.ts --include=*.tsx
             -> (eval):1: no matches found: --include=*.ts        und dann:  0

2. Versuch   grep -rn "blocked" resources/planner/hausplaner --include='*.ts' --include='*.tsx'
             -> 0
```

> **Beide Male stand `0` da, und nur einmal bedeutete es dasselbe.** *Im ersten Lauf hat die Shell
> das Muster gefressen, der Befehl ist nie richtig gelaufen — und die Ausgabe sah aus wie ein
> Messergebnis.* **Eine Null aus einem kaputten Befehl ist von einer Null aus dem Code nicht zu
> unterscheiden, wenn man nur die Zahl anschaut.** *Ich melde das, weil ich `blocked = 0 Treffer` an
> vier Stellen der Blätter geschrieben habe; die Zahl steht auf dem zweiten Lauf.*

## Ein gemessener Nebenbefund: `markiereVeraltet` führt WER, nicht WARUM

**In `7-GRENZEN` stand die Frage „führt `markiereVeraltet` den Grund mit?" — ich habe sie
aufgemacht, statt sie als ungemessen stehen zu lassen:**

```ts
configuratorPackage.ts:125-128
markiereVeraltet(paket, jetzt, durch)
  -> { ...paket, status: 'outdated', updatedAt: jetzt, updatedBy: durch }
```

**`durch` landet in `updatedBy`.** *Das ist der **Urheber**, nicht die **Ursache**.*

> **Damit ist „outdated ohne Grund" halb geschlossen und halb offen** — *wer es gesetzt hat, steht
> fest; **was** die Bestätigung ungültig gemacht hat, nicht.* **Das ist dieselbe Auflage, die Yama
> für `blocked` als `blockiert_durch` gemacht hat, nur auf der Invalidierungsseite noch nicht
> erfüllt.** *Gemessen und benannt; behoben wird es nicht hier — das ist W-41.*

## Meine Liste war unvollständig: FÜNF Blätter, nicht vier

**`ea418041` nennt dreizehn Stellen in vier Blättern.** *`5-CODE/LIESMICH.md` fehlt darin, und es
trug die Prämisse am dicksten:*

```text
| 1–5 | noch keine | W-40 ist eine Vorgabe; für SchrittStatus existieren die drei Stufen nicht |
```

**Warum es durchgerutscht ist:** *ich habe nach den zwei **Fachfragen** gesucht — nach
`review-required` und nach `DECISION_BLOCKED`.* **`5-CODE` nennt beide nicht. Es trägt die Prämisse,
nicht die Fragen.**

> **Das ist H-9 an meinem eigenen Befund:** *ein Muster, das eine Schreibweise voraussetzt, misst die
> Schreibweise und nicht die Sache.* **Der Plan-Prüfer hat meine Liste Zeile für Zeile nachgemessen
> und jede Stelle bestätigt — geprüft wurde, ob die genannten Stellen stimmen, nicht ob die Liste
> vollständig ist.** *Genau die Frage, die er in seinem eigenen Nachtrag als seinen Anteil benennt.
> Sie ist zweimal hintereinander nicht gestellt worden, von ihm und von mir.*

**Der Vermerk steht im Blatt selbst, oben in `5-CODE/LIESMICH.md`** — *nicht nur hier im Bericht, wo
ihn beim Weiterbauen niemand liest.*

## Vier offene Fragen, alle vier zu

*Die Tabelle „Was dieses Blatt ausdrücklich NICHT entscheidet" in `7-GRENZEN` trug vier Zeilen. Sie
stehen alle noch da, jetzt mit einer dritten Spalte:*

| Frage | Antwort | von wem |
|---|---|---|
| Trägt `ENTWORFEN` noch? | **nein** — Ablesung mit einer Erweiterung | Yama |
| Gehört `review-required` zur Achse? | **ja**, gebaut als `checked` | Yama |
| `blocked` gegen `DECISION_BLOCKED`? | Mensch gegen Bedingung | Yama |
| Gilt die gebaute Übergangstabelle auch hier? | **ja**, `:103-111` | *gemessen* |

> **Zwei der vier hätte ich beim Bau selbst beantworten können — sie standen im Code.** *Ich habe die
> QUELLE gefragt, wo der BESTAND geantwortet hätte.* **Der Satz steht jetzt im Kopf von `7-GRENZEN`:
> „die Quelle gibt es nicht her" ist erst dann eine Grenze, wenn auch der Bestand nichts hergibt.**

## W-40/1-7 · Die Registerzeile, als letzter Schritt

```text
vorher   | W-40 | Gültigkeitsstatus confirmed·outdated·blocked | ENTWORFEN | W-38 | keine ⓝ …
nachher  | W-40 | Gültigkeitsstatus checked·approved·outdated + blocked | BESCHRIEBEN | W-38 |
           geometry/configuratorPackage.ts ⓝ … blocked ist die einzige Erweiterung — 0 Treffer …
```

**Drei Dinge ändern sich, und jedes ist gemessen:** *der Reifegrad, die **Stufennamen** (die des
Codes, nicht die des Zielbilds), und die Codespalte von „keine" auf die Datei.* **Die Erweiterung
`blocked` ist in der Zeile ausdrücklich benannt** — *das verlangt W-40/1-7 wörtlich.*

**Der Abschlusszähler, mit dem Befehl des Registers gemessen:**

```text
grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN'    HEAD 17  ->  jetzt 18
grep -cE '^\| W-[0-9]+ .*ENTWORFEN'      HEAD  3  ->  jetzt  2
```

> **Die Zeile war der LETZTE Schritt und nicht der erste.** *Ich hatte in `5028375e` geschrieben, ich
> ändere den Reifegrad nicht, bevor Yamas Antwort da ist — sie ist da, und sie sagt, dass zuerst das
> Blatt zu ändern ist.* **Wer die Zeile vorher umstellt, behauptet eine Ablesung, die es noch nicht
> gibt.**

## must_preserve, in drei Richtungen einzeln

| Richtung | Befehl | Ergebnis |
|---|---|---|
| geändert | `git status --porcelain resources/ app/` | **0** |
| hinzugefügt | `git ls-files --others --exclude-standard` unter `resources/` | **0** |
| entfernt | `git diff --diff-filter=D --name-only` | **0** |

**Kein Test gefahren, weil kein Code berührt ist** — *`configuratorPackage.ts` und `studioDaten.ts`
sind gelesen, nicht geändert.* **Eine Ablesung ändert ihre Quelle nicht.**

## Rückweg

*Sieben geänderte Blätter und eine Registerzeile, alle in einem Commit, keine Neuanlage außer diesem
Bericht.* **`git revert` genügt** — *und weil keine Stelle gelöscht, sondern jede als überholt
gekennzeichnet wurde, wäre auch der Zustand vor der Berichtigung aus dem Blatt selbst wieder
lesbar.*
