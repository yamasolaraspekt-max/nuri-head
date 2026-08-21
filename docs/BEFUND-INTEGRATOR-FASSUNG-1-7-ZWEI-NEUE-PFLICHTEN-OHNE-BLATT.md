# Befund — Fassung 1.7 gibt dem Integrator zwei neue Pflichten, sein Rollenpaket kennt keine davon

```yaml
rolle: integrator
gemessen: "21.08.2026, 09:5x CEST, am Stand nach dem Rueckweg (18343ad3)"
gegenstand: "docs/ARBEITSREGELN.md Fassung 1.7 (0f554dd9, 20.08. 16:04) gegen
             docs/rollenkette/rollen/6-integrator/ — die Regelquelle gegen mein Blatt."
kein_eingriff: "Nur dieses Blatt. Mein Rollenpaket fasse ich nicht an: laut ARBEITSREGELN
                Z.1297-1298 schreibt es der Planner und der Plan-Pruefer nimmt es ab."
```

## Der Befund in einem Satz

> **Fassung 1.7 nennt den Integrator an zwei neuen Stellen. Von den drei Rollen, deren Pflichten
> sie ändert, sind zwei Rollenpakete nachgezogen — meines nicht.**

## 1 · Was 1.7 dem Integrator neu aufträgt, wörtlich

```text
ARBEITSREGELN.md:860   Zustandsleiter, neue Zeile
  | VEROEFFENTLICHT | INTEGRATION_GEPRUEFT oder RELEASE_BLOCKED |
    frische, am Vorgang unbeteiligte Evaluator-Instanz (Integrations-Abnahme, Fassung 1.7);
    GESCHRIEBEN WIE JEDER ZUSTAND UEBER DEN INTEGRATOR |

ARBEITSREGELN.md:581   §12.1, neue Befundklasse
  | INTEGRATION | INTEGRATOR | RELEASE_BLOCKED |
    Fassung 1.7: falsche Konfliktloesung oder falsche Basis beim Zusammenfuehren —
    kann nur auf der Integrationslinie behoben werden. |
```

**Das erste ist ein neuer Zustand, den ich schreiben muss. Das zweite ist eine Befundklasse, die
bei mir landet und `RELEASE_BLOCKED` auslöst.** Beides ist neu; keines stand vor 1.7 in der Kette.

## 2 · Was mein Rollenpaket davon weiß

```text
$ grep -r <begriff> docs/rollenkette/rollen/6-integrator/     (5 Dateien)
  INTEGRATION_GEPRUEFT      0
  Integrations-Abnahme      0
  Fassung 1.7               0
  INTEGRATION.*Integrator   0
```

**Vier Nullen.** Weder `2-WANN-BIN-ICH-DRAN.md` (das den Ablaufplan trägt) noch
`4-WAS-ICH-ABLIEFERE.md` (die neun Erzeugnisse) noch `5-WAS-ICH-NICHT-DARF.md` nennt eines der
beiden.

## 3 · Der Vergleich, der es zu einem Befund macht statt zu einer Klage

**Derselbe Commit hat zwei andere Rollenpakete sehr wohl nachgezogen:**

| Paket | `INTEGRATION_GEPRUEFT` | `Integrations-Abnahme` | `Fassung 1.7` | von 1.7 betroffen? |
|---|---|---|---|---|
| `1-planner` | 0 | 0 | 0 | nein |
| `2-plan-pruefer` | 0 | 0 | 0 | nein |
| `3-generator` | 0 | 0 | 0 | nein |
| `4-evaluator` | 0 | **2** | **1** | **ja — nachgezogen** |
| `5-release-pruefer` | **1** | 0 | **1** | **ja — nachgezogen** |
| **`6-integrator`** | **0** | **0** | **0** | **ja — NICHT nachgezogen** |

Der Zusatz beim Evaluator ist konkret und gut — zwei Zeilen in seiner „Wann bin ich dran"-Tabelle,
darunter ausdrücklich: *„Integrations-Abnahme, aber ich war Generator/Evaluator/Release-Prüfer/
**Integrator** des Vorgangs → **nein, nicht frisch, zwingend**"*. **Der Integrator kommt in 1.7
also sogar in fremden Blättern vor — nur nicht im eigenen.**

## 4 · Was NICHT stimmt an einer naheliegenden Zuspitzung

**Es hängt derzeit nichts.** Gemessen, bevor ich es dramatisiere:

```text
Auftraege im Zustand VEROEFFENTLICHT           0     -> keine Integrations-Abnahme faellig
Befunde mit 'klasse: INTEGRATION' im Bestand   0     -> die neue Klasse ist noch nie benutzt worden
```

**Die Lücke ist latent, nicht blockierend.** Sie wird erst scharf, wenn der erste Vorgang
`VEROEFFENTLICHT` erreicht — dann soll ich einen Zustand schreiben, den mein eigenes Blatt nicht
kennt, und ein Befund kann bei mir landen, für den mein Blatt kein Verfahren nennt.

**Und der zweite Grund, warum ich es jetzt melde statt später:** `4-WAS-ICH-ABLIEFERE.md` führt
**neun** Erzeugnisse. Ob die Integrations-Abnahme ein zehntes verlangt — ein Nachweis, dass der
Integrationsstand geprüft wurde —, steht nirgends. **Eine fehlende Liste liest sich wie eine leere**,
und das ist die Formulierung meines eigenen Erzeugnisses 8.

## 5 · Warum ich es nicht selbst nachtrage

`ARBEITSREGELN.md:1297-1298`, wörtlich:

> *„**Sein Rollenpaket** liegt in `docs/rollenkette/rollen/6-integrator/` und wird **unabhängig vom
> Plan-Prüfer** abgenommen, **nicht vom Planner, der es geschrieben hat**."*

**Der Planner schreibt es, der Plan-Prüfer nimmt es ab. Der Integrator tut weder das eine noch das
andere** — er ist der Gegenstand des Blattes, nicht sein Autor. Ein Integrator, der sein eigenes
Pflichtenheft ergänzt, schreibt sich seine Aufgaben selbst; das ist genau die Trennung, wegen der
das Paket unabhängig abgenommen wird.

## 6 · Ball

| an wen | was |
|---|---|
| **Planner** | die zwei Stellen aus Abschnitt 1 gehören in `6-integrator/2-WANN-BIN-ICH-DRAN.md` und, falls die Abnahme ein Erzeugnis verlangt, in `4-WAS-ICH-ABLIEFERE.md`. Er hat die anderen beiden Pakete am 20.08. bereits nachgezogen |
| **Plan-Prüfer** | die Abnahme des ergänzten Pakets, nach derselben Regel Z.1297 |
| **Yama** | zur Kenntnis: nichts hängt heute daran (0 Vorgänge auf `VEROEFFENTLICHT`, 0 Befunde der neuen Klasse). Es ist eine Lücke mit Vorlaufzeit, kein Stillstand |
