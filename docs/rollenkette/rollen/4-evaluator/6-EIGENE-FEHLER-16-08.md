# EIGENE FEHLER — gesucht, gezählt, behoben (16.08.)

**Anlass:** Yamas Auftrag, im Bezug auf die eigene Rolle erst alle Fehler zu suchen, ihre Zahl zu
nennen und sie dann nacheinander zu beheben.

**Ergebnis in einem Satz:** Fünf Fehler gesucht — **vier waren echt, einer war es nicht**, und beim
Beheben kam **ein sechster** dazu, der keiner Behebung durch mich zugänglich ist. Kein einziger
Fehler lag in den Voten: **alle 71 Abnahmen sind an beiden Statusorten angekommen.** Was falsch
war, war das Werkzeug, mit dem ich jeden Takt messe — und damit möglicherweise jede Aussage
„bei mir liegt nichts an".

---

## Was NICHT falsch war — zuerst, weil es die Zahl relativiert

| Geprüft | Befund |
|---|---|
| 71 Voten gegen `docs/STATUS.md` | 70 passen oder sind weitergewandert, **0 hängen hinter meinem Votum zurück** |
| Wortlaut-Regel bei Zustandswechseln ([`ARBEITSREGELN.md:1479`](../../../ARBEITSREGELN.md#L1479)) | **0 Verstöße.** Die Regel gilt ab `05347393` (16.08. 16:10); A-33 (14:57) liegt davor |
| Tafelzeile gegen Zustandsfeld, 87 Kennungen | **0 echte Drift** |
| Zaun im Fließtext (`t.find('```')`-Falle) | Mein Abbruch prüft bündig — von den 11 Fließtext-Zäunen trifft mich keiner |

Die einzige gemeldete Lücke — „W-07 hat kein Zustandsfeld" — war **mein** Fehler: die Kennung
heißt `W-07N`, ihr Datensatz steht auf Z.4264. Das ist E2.

---

## E1 · `vendor` und `.env` fehlten in meinem Baum — BEHOBEN

**Wirkung:** §15 („`getDatabaseName()` muss `ticket_testing` sagen") war in meinem eigenen Baum
nicht belegbar, ebensowenig eine Browserabnahme. Beides fordert der Takt vor jedem Schreiben.

**Behebung:** `vendor` per `cp -al` aus dem Integrations-Checkout (`composer.lock` beidseits
`01677e50`, also derselbe Stand), `.env` und `.env.testing` kopiert. Beide sind in `.gitignore`
(Z.7/8/13) — kein Beifang, `git status` bleibt still.

**Beleg, und er kann rot werden:**

```
php artisan tinker --env=testing --execute='… getDatabaseName() …'   →  ticket_testing
php artisan tinker              --execute='… getDatabaseName() …'   →  ticket        ← Gegenprobe
phpunit.xml:28                                                       →  force="true"
```

Ohne die Gegenprobe wäre „sagt `ticket_testing`" keine Messung, sondern eine Zusage.

## E2 · Mein Kennungsmuster war zu eng — BEHOBEN

`[AWZ]-[0-9]+(/[0-9]+)?` verfehlt jede Kennung mit Suffixbuchstaben oder ohne Bindestrichziffer.
**Acht Datensätze waren für mich unsichtbar:** `B5`, `B5N`, `B6`, `B7`, `P-02`, `W-01N`, `W-07N`,
`W-21L`. Läge dort ein Ball, hätte ich ihn nicht gesehen.

**Behebung:** kein Kennungsmuster mehr. Es zählt, was ein Datensatz **ist** (E4), nicht wie er heißt.

## E3 · Tafelzustand aus der falschen Stelle gelesen — BEHOBEN

Ich las den ersten `` `WORT` `` der Zeile statt **Spalte 2**.

- `A-16` heißt *„`TIME_VARS` im Produktivbaum"* — ich las `TIME_VARS` als Zustand.
- `A-06` trägt `**ERLEDIGT**` **ohne** Backticks — ich las gar nichts.

Beides erschien als Tafel/Datensatz-Divergenz, die es nicht gibt. **Behebung:** Spalte 2 nach
`split("|")`, Sternchen und Backticks abgeräumt.

## E4 · Befundblock als Auftragsdatensatz gelesen — BEHOBEN, nach einem Fehlversuch

Rollen legen unter `auftrag:` auch **Befundblöcke** ab. `P-04` hat ausschließlich solche,
`A-40` hat einen Datensatz und sechzehn Befunde. Wer beide gleich liest, hält `zustand: BEFUND`
für einen Auftragszustand.

**Die erste Fassung meiner Behebung war selbst falsch** und wurde vor dem Commit verworfen: sie
erkannte den Datensatz an `blatt`/`art`/`spur`/`prioritaet`. Nachgemessen fielen damit **zwanzig
echte Datensätze** durch — A-01…A-05, A-07, A-18, A-19, W-02/1, W-04/1, W-05/1, W-08/1, W-11/1,
W-13/1, W-20, W-21/1, W-22/1, W-23, W-27, B5N tragen keines dieser Felder.

**Die tragfähige Regel steht in den Daten, nicht in der Erwartung:** ein **Befund** trägt `rolle`
UND `zeit`; ein **Datensatz** trägt `zustand` und ist kein Befund. Genau ein Block trägt beides
samt Zustand — `P-03` mit `zustand: BEFUND`, also richtigerweise ein Befund.

**Gegenprobe auf einem zweiten, unabhängigen Weg:** 87 Datensätze — und die Statustafel vor dem
ersten Zaun hat exakt 87 Zeilen. Zwei Wege, die nichts voneinander wissen.

## E5 · Kein Fehler — WIDERLEGT durch Messung

Ich vermutete, bei mehreren Blöcken einer Kennung müsse der **spätere** gewinnen („bei Abweichung
gewinnt der neuere Schreibvorgang"). Gemessen: **in 12 von 12 Fällen steht der Datensatz vor den
Befunden, in 0 Fällen dahinter.** Die „Behebung" hätte A-40 von `ENTWURF` auf `BEFUND`
verfälscht — ich hätte mir einen Fehler eingebaut, der vorher nicht da war.

Der Grundsatz gilt weiter für Tafel gegen Datensatz. Er gilt **nicht** für die Reihenfolge zweier
Blöcke. Das ist derselbe Griff wie H-9: ein Satz, zwei Geltungsbereiche.

## E6 · NEU beim Beheben gefunden — §6 Testdatenbank-Trennung. KEINE Reparatur durch mich

[`5-WAS-ICH-NICHT-DARF.md:8`](5-WAS-ICH-NICHT-DARF.md) verbietet mir dieselbe Testdatenbank wie
der Generator, [`ARBEITSREGELN.md:288`](../../../ARBEITSREGELN.md#L288) sagt es im Wortlaut:
*„Insbesondere dürfen Generator und Evaluator nicht gleichzeitig dieselbe `ticket_testing`-
Datenbank verwenden."*

**Gemessen, alle sechs Bäume:**

```
ticket · ticket-rolle-generator · ticket-rolle-planner ·
ticket-rolle-plan-pruefer · ticket-release-pruefung · ticket-rolle-evaluator
    → phpunit.xml:28  DB_DATABASE="ticket_testing"  force="true"
```

**Die Trennung ist strukturell nicht umgesetzt, und zwar nirgends.** `phpunit.xml` ist versioniert
und erzwingt den Namen mit `force="true"` — eine eigene `.env.testing` je Baum kommt dagegen nicht
an. Ich habe die Kollision durch das Kopieren der `.env.testing` nicht verursacht; sie bestand
vorher und betrifft jede Rolle.

**Ich behebe das nicht.** Es wäre eine Änderung an gemeinsamem, versioniertem Bau-Code — das ist
Generator-Arbeit auf einen Planner-Auftrag hin. **Ball beim Planner.** Bis dahin: die Insel-Suite
(`npm run test:hausplaner`) braucht keine Datenbank und bleibt kollisionsfrei; vor jedem PHP-Lauf
prüfe ich, ob der Generator gerade misst, und melde eine Kollision als `ENV_BLOCKED` (§6:290).

---

## Fremdbefund — nicht meiner, deshalb gemeldet und nicht angefasst

`scripts/a26-ball-drift.sh:53` trägt **dasselbe zu enge Kennungsmuster**, das bei mir E2 war:

```
(\| \*\*[AW]-[0-9]+(/[0-9]+[a-z]?)?\*\*|auftrag: "?[AW]-[0-9]+(/[0-9]+[a-z]?)?)
```

**Am Werkzeug selbst gemessen**, nicht aus dem Quelltext geschlossen:

```
printf 'auftrag: "W-07N"' | grep -oE '<Muster von Z.53>'   →   auftrag: "W-07      ← abgeschnitten
```

Aus der Tafelzeile `| **W-07N** …` kommt gar nichts, weil `**` nicht auf `W-07` folgt. Damit fallen
dieselben acht Kennungen aus der Ball-Drift-Prüfung **am Tor** — und Z.142 (`UNGEPRUEFT`) schlägt
nicht an, weil sie nie in die Liste kommen. Der Lauf meldet `exit=0` und schweigt.

**Wirkung heute ehrlich beziffert:** alle acht sind aktuell deckungsgleich, **0 Drift**. Sechs sind
abgeschlossen; offen sind `P-02` (`VORLAGE`, Ball plan-pruefer) und `W-21L` (`DECISION_BLOCKED`).
Die Lücke wirkt heute nicht — sie bliebe still, wenn sie morgen wirkte. **Ball beim Planner.**

A-26 ist `BETRIEBSBESTAETIGT`. Ich fasse abgenommenen fremden Code nicht an, auch nicht „nur
schnell" ([`5-WAS-ICH-NICHT-DARF.md:9`](5-WAS-ICH-NICHT-DARF.md)).

---

## Das Werkzeug — damit derselbe Fehler nicht zum vierten Mal frisch getippt wird

`scripts/evaluator-rueckstand.py` ersetzt den Einzeiler, der bislang jeden Takt neu entstand.
Es beantwortet **eine andere Frage** als `a26-ball-drift.sh` (Rückstand einer Rolle über mehrere
Zweige, nicht Drift am Tor) — keine zweite Wahrheit.

`--fangprobe` fährt jeden der behobenen Fehler **alt gegen neu**. Jede Probe wäre mit der alten
Lesart rot; sonst belegte sie nichts (Pflichtprüfung 4):

```
E2  W-07N              alt=VERFEHLT              neu=gefunden
E3  A-16               alt=TIME_VARS             neu=BETRIEBSBESTAETIGT
E3  A-06               alt=?                     neu=ERLEDIGT
E4  P-04               alt las zustand=BEFUND    neu=kein Datensatz
E4b 20 Datensätze ohne blatt/art/spur            20 gefunden, 0 verloren
GP  Datensätze 87 gegen Tafelzeilen 87           gleich
ROT Drift  A-16 künstlich auf IN_ARBEIT          gemeldet=['A-16']
ROT Ball   A-16 künstlich auf evaluator          gemeldet=['A-16']
---- Fangproben rot: 0
```

Die beiden ROT-Proben stehen dort, weil „keine Drift" und „kein Ball" **nur dann eine Aussage
sind, wenn das Werkzeug anschlagen kann**. Sie mutieren im Speicher; `docs/STATUS.md` bleibt
byte-gleich (`md5 6cc04ca7…` vor und nach dem Lauf, gegen `HEAD` geprüft).

Zweiter Beleg, dass der Balldetektor trägt — er findet fremde Bälle, die wirklich liegen:

```
--rolle integrator    → ['A-37']
--rolle plan-pruefer  → ['A-38', 'A-39', 'A-40', 'A-42', 'P-02']
--rolle evaluator     → keiner          ← und das ist jetzt eine gemessene Aussage
```

---

## Zählung

| | |
|---|---|
| gesuchte Fehler | 5 |
| davon echt und behoben | **4** (E1–E4) |
| davon durch Messung widerlegt | **1** (E5) — die Behebung hätte Schaden angerichtet |
| beim Beheben neu gefunden | **1** (E6) — nicht in meiner Rolle behebbar, Ball beim Planner |
| Fehlversuche innerhalb der Behebung, vor dem Commit verworfen | **1** (E4, erste Fassung) |
| Fremdbefunde gemeldet, nicht angefasst | **1** (`a26-ball-drift.sh:53`) |
| Fehler in den Voten selbst | **0** von 71 |

---

# NACHTRAG 21:5x — E7 und E8, gefunden beim nächsten Takt

## E7 · Ich habe „0 Drift" gemeldet und nur den Zustand gemessen — BEHOBEN

**Das ist die Fehlerklasse aus Punkt 6 des Takts, an mir selbst:** meine Ausgabe hieß `Drift`,
verglich aber ausschließlich das Feld `zustand`. Der **Ballbesitz** — genau das, wonach ich in
jedem Takt suche — wurde nie verglichen.

**Wirkung, gemessen:** `A-37` trägt in der Tafelzeile 88 **`Plan-Prüfer`** und im Datensatz
**`integrator`**. Meine letzten Taktmeldungen sagten trotzdem „0 Drift". Der Plan-Prüfer hatte
die Abweichung um 20:20 selbst gemeldet (`d4fad8bb`, *„A-37s Ballbesitz steht an zwei Orten
verschieden"*); ich bestätige sie hier unabhängig — und mein Werkzeug hätte sie finden müssen.

**Behebung:** Zustand-Drift und Ball-Drift werden getrennt gezählt und getrennt benannt. Die
Normalisierung ist **wortgleich** aus `a26-ball-drift.sh:121` übernommen (Kleinschreibung,
Umlaute, `–`/`—` = kein Ball) — zwei Prüfungen desselben Feldes mit zwei Schreibweisenregeln
wären eine zweite Wahrheit.

**Beleg in beide Richtungen:**

```
ROT Ball-Drift   A-38  Tafel-Ball künstlich auf generator (Datensatz 'plan-pruefer')  → gemeldet
GEG Schreibweise A-37  Backticks statt Sternchen, dieselbe Rolle                      → schweigt
```

Die Gegenprobe steht dort, weil eine Barriere, die bei jeder Schreibweise warnt, nach A-03 in
drei Tagen weggeklickt ist.

**Eigener Konstruktionsfehler in dieser Behebung, offengelegt:** die Rot-Probe zielte zuerst auf
`A-16`. Dessen Datensatz trägt `ballbesitz: —`, und die Regel verlangt zu Recht auf **beiden**
Seiten einen Ball — die Probe meldete deshalb nur die echte A-37-Drift und **sah aus, als
schlüge sie an**. Ziel auf eine Kennung mit Ball an beiden Orten umgestellt (A-38).

## E8 · Elf W-Aufträge waren dauerhaft driftungeprüft — BEHOBEN

Der Altbestand führt denselben Vorgang unter zwei Schreibweisen: Tafel `W-01`, Datensatz
`W-01/1`. Ohne Paarung fallen **elf** Kennungen aus jedem Vergleich — und es sind dieselben
W-Aufträge, die mein Kennungsmuster schon zweimal übersehen hat.

**Behebung, bewusst eng:** gepaart wird nur, wenn die Tafelkennung selbst keinen Datensatz hat
**und genau ein** Kandidat `<kennung>/…` existiert. **Vergleichbar: 76 → 85.**

**Was NICHT gepaart wird, und warum das richtig ist:** `W-05` und `W-21` haben je zwei Kandidaten
(`/1` und `/2`). Die Regel rät nicht. **Mein erstes Prüfkriterium war hier falsch** — ich erwartete
„alle 87 vergleichbar" und meldete die korrekte Zurückhaltung des Werkzeugs als rot. Kriterium
berichtigt: die Paarung muss *wirken* und jede ungepaarte Kennung einen *nachweisbaren* Grund
haben. Die vier Datensätze hinter W-05 und W-21 habe ich stattdessen **von Hand** geprüft — alle
`BETRIEBSBESTAETIGT`, Ball `—`, Tafel identisch, **keine Drift**.

Gegenprobe zur Paarung: einer Kennung künstlich einen zweiten Kandidaten geben → sie bleibt
ungepaart statt geraten.

## Was KEIN Befund war — `a26-ball-drift.sh` ist entlastet

Zwischenzeitlich sah es so aus, als übersähe das Tor-Werkzeug die A-37-Ball-Drift: es lief mit
`exit=0` und schwieg. **Ursache gemessen, nicht vermutet:** `a26-ball-drift.sh:51-54` bildet die
zu prüfenden Kennungen aus dem **Diff**. Bei sauberem Arbeitsbaum ist der leer, `KENNUNGEN` ist
leer, und Zeile 55 steigt mit `exit 0` aus. Es ist eine **Barriere für den anstehenden Commit**,
kein Bestandsprüfer — genau so gebaut und genau so richtig. A-37s Drift ist committet, liegt also
nicht im Diff.

**Der Befund gegen a26 wäre falsch gewesen und wird nicht erhoben.** Der andere von 20:5x (zu
enges Kennungsmuster auf Z.53) bleibt davon unberührt bestehen.

**Was daraus systemisch folgt und nicht mir gehört:** die Ball-Drift im **Bestand** hat keinen
Prüfer. a26 sieht nur den Commit, der gerade entsteht. Mein Werkzeug sieht sie jetzt, aber es
läuft nur, wenn ich es fahre. **Ball beim Planner.**

## E9 · Ein Kriterium grün gemeldet, ohne die vorgeschriebene Probe zu fahren — Ergebnis richtig, Weg falsch

**Anlass:** nach meiner A-41-Abnahme (`ce91b05f`, 12/12) hat der Generator vier A-41-Befunde
abgearbeitet und dabei `scripts/status-erzeugen.sh` um **+41/−3 Zeilen** geändert. Die Frage, die
ich mir stellen musste: **hätte ich die finden müssen?**

Im Diff stand ein Satz, der wie ein Urteil gegen mich aussah: *„bis dahin druckte der Modus die
Tafel MIT dem jüngsten Eintrag als Gewinner und die Warnung darunter."* Genau das verbietet
**A-41-7** — *beide in der Meldung, Rückgabe `2`, **Tafel unverändert***.

**Meine Messtischzeile von damals, im Wortlaut:**

> `Code :313-321 geöffnet: widerspruch.append(...) mit Kommentar „Regel 4: melden, nicht
> aufloesen"; :472 „DER WIDERSPRUCH KOMMT VOR DER TAFEL…"` → **grün**

**Das ist „es steht da", nicht „es wirkt"** — die Fehlerklasse aus Punkt 6 meines eigenen Takts.
Und es wiegt schwerer als sonst: der Auftrag schrieb den Weg zur Probe **ausdrücklich hin**
(`GIT_AUTHOR_DATE`/`GIT_COMMITTER_DATE` gleichsetzen, zwei Zustands-Commits derselben Kennung mit
verschiedenem Zustand) — und begründete das damit, dass A-41-7 *„das einzige der zwölf Kriterien
ist, das in keiner einzigen Meldung vorkommt"*. Der Auftrag hat mir die Probe in die Hand gelegt,
und ich habe sie nicht gefahren.

**Jetzt gefahren, in einem Probe-Repo, gegen den Bau, den ich abgenommen habe (`f19557c8`):**

```text
$ TICKET_ROLLE=generator bash status-erzeugen(f19557c8) --tafel        RUECKGABE: 2

  # KEINE Tafel erzeugt — WIDERSPRUCH bei gleicher Zeit (1)
      X-01       ABGENOMMEN           evaluator        a5aeee0b  16.08 12:00
      X-01       CODE_FERTIG          generator        1c7da6e2  16.08 12:00
    RUECKGABE 2 — NICHT erzeugt, Widerspruch
    GEMELDET, NICHT aufgeloest (Regel 4). Die Entscheidung gehoert dem Integrator.
```

**Alle drei Forderungen erfüllt** — beide Zeilen, Rückgabe 2, keine Tafel. **Mein Ergebnis war
richtig.** Der Satz im Diff beschreibt eine Änderung, die der Generator **beim Bau von A-41**
vorgenommen hat, nicht danach.

**Rot-Probe, damit die Aussage etwas wert ist** — bei VERSCHIEDENEN Zeiten muss die Tafel sehr
wohl entstehen, sonst hinge `RUECKGABE 2` gar nicht am Widerspruch:

```text
Zeiten 13:00 / 12:00 → RUECKGABE 1
  # Statuswahrheit — ERZEUGT aus dem Commit-Log, 1 Kennungen
      X-01       ABGENOMMEN           evaluator        64d4078a  16.08 13:00
```

**Regression geprüft:** die heutige Fassung (816 Zeilen, gegen 703 bei der Abnahme) liefert gegen
dieselbe Probe eine **byte-gleiche** Ausgabe. Die vier Befunde haben A-41-7 nicht verändert.

### Der Beinahe-Fehler, der wichtiger ist als der Befund

**Hätte ich den Generatorbericht zuerst gelesen statt zuerst gemessen, hätte ich einen falschen
Eigenbefund gegen mich selbst erhoben** — „A-41-7 war rot, ich habe es grün gemeldet". Die
Reihenfolge des Takts (*erst Auftrag, Diff, Code und eigene Gegenproben — den Bericht ERST DANACH*)
hat das verhindert. Sie schützt nicht nur vor fremden Behauptungen, sondern auch vor der eigenen
Bereitschaft, sich schuldig zu sprechen.

**Was bleibt:** ein Verfahrensfehler ohne Folgen im Ergebnis. Ein grünes Kriterium, dessen Beleg
die geforderte Probe nicht enthielt. Die Probe ist jetzt gefahren und liegt hier als Rohausgabe.

## E10 · Der E9-Fall war kein Einzelfall — zwei weitere Kriterien ohne die geforderte Probe

Nach E9 lag die Frage nahe, ob „es steht da statt es wirkt" bei mir öfter vorkommt. **Systematisch
geprüft an allen zwölf A-41-Messtischzeilen: zehn tragen eine gefahrene Messung** (Rot-Beleg über
Stände, Läufe mit Rohausgabe, Zählungen am Bestand). **Zwei nicht:**

| | meine Zeile von damals | Kriterium verlangt |
|---|---|---|
| **A-41-8** | „`--no-merges` an drei Stellen (`:293`, `:297`, `:450`)" + Gegenprobe am Bestand: **0** Merge-Commits | *ein Zustands-Betreff, der nur über einen Merge in den Log kommt, erzeugt **keine** zweite Zeile* |
| **A-41-9** | „je einzeln **im Code**: K1 4 · K2 10 · K3 3 · K4 5 · K5 3 · K6 6 · K7 1 Treffer" | ***„Behandelt" heißt nicht „im Code adressiert"** … wenn der Beleg die **Probe** ist* |

**Bei A-41-9 steht die Forderung wortwörtlich im Kriterium** — und ich habe genau das geliefert,
was es ausschließt. Bei A-41-8 wog es doppelt: meine „Gegenprobe" zählte **0** Merge-Commits im
Bestand, die Filterung konnte dort also gar nicht wirken. **Eine Probe, die nicht rot werden kann,
belegt nichts.**

### A-41-8 nachgeholt — mit Rot-Richtung

Probe-Repo: ein Zustands-Commit auf einem Nebenzweig (`CODE_FERTIG`, 10:00), der **Merge** trägt
selbst einen Zustands-Betreff (`ABGENOMMEN`, 11:00) — der jüngere. Wirkt der Filter nicht, gewinnt
der Merge.

```text
mit --no-merges (abgenommener Bau f19557c8):
    Y-01   CODE_FERTIG   generator   73673b6b  16.08 10:00      ABGENOMMEN: 0 Treffer

ohne --no-merges (Argument entfernt, Anker 3× gezählt, 1 Kommentar bleibt):
    Y-01   ABGENOMMEN    integrator  0460c4e0  16.08 11:00      der Merge verdrängt den echten
```

### A-41-9 nachgeholt — alle sieben Kanten als Probe

| Kante | verlangt | Beleg aus gefahrener Probe |
|---|---|---|
| **K1** | gleiche Zeit → beide melden, Rückgabe `2`, keine Tafel | `RUECKGABE 2`, beide X-01-Zeilen, „KEINE Tafel erzeugt" (E9) |
| **K2** | Kennung ohne Blatt → Zeile erzeugt **und** gemeldet | `K2 · ZUSTAND OHNE AUFTRAG — Zeile erzeugt UND gemeldet: 2`, Zeilen stehen in der Tafel |
| **K3** | jüngster über sechs Stände, verdrängte einzeln protokolliert | bereits bei der Abnahme gefahren: `--bootstrap` **86** Kennungen, **13** verdrängte Stände einzeln |
| **K4** | Prosa nicht übernehmen, aber je Zweig protokollieren | `K4 · PROSA je Zweig: generator 13 Zeilen · Datensatz 2 · Prosa 7`; Seed trägt **nur** `V-01` |
| **K5** | Revert zählt nicht, der zurückgedrehte bleibt gültig | Revert-Commit `d1a57c6` **nicht** in der Tafel; `V-01 CODE_FERTIG` steht mit dem SHA des **Originals** `5f1182e3` |
| **K6** | fremder Zweig → Zeile erzeugt **und** gemeldet | `K6 · ZUSTAND IM FREMDEN ZWEIG: W-99 Rollenmarke 'evaluator' — liegt auf rolle/generator`, Zeile in der Tafel |
| **K7** | Merge nicht zählen | siehe A-41-8 oben, mit Rot-Richtung |

**Sieben von sieben, jede mit Rohausgabe.** Das Ergebnis meiner Abnahme ändert sich nicht — der Bau
erfüllt die Kriterien. **Was sich ändert, ist der Beleg:** er ist jetzt der, den das Kriterium
verlangt hat.

### Drei eigene Konstruktionsfehler in diesen Proben, offengelegt

1. **`sed 's/--no-merges//g'`** ersetzte das Argument durch einen **leeren String** — `git log`
   bekam ein leeres Argument und lieferte gar nichts (`RUECKGABE 3, Eingang leer`). Das sah nach
   einem Befund aus und war meine Mutation. Behoben: Argument **entfernen** statt leeren.
2. **`git revert -q`** — die Option gibt es nicht, der Revert entstand nie, und ich hätte K5 an
   einem Repo ohne Revert „geprüft".
3. **Kennungen `K5-01`/`K6-01` erfunden**, die das Kennungsmuster nicht treffen (`[A-Z]+-?[0-9]+`
   liest `K5`, dann steht `-01` im Weg). Beide Commits wurden als *„NICHT IM WORTLAUT"* gemeldet.
   **Das ist zugleich ein Positivbefund über den Bau:** er schluckt eine unbrauchbare Kennung
   nicht still, sondern meldet sie.

## Zählung nach dem Nachtrag

| | |
|---|---|
| eigene Fehler insgesamt behoben | **6** (E1–E4, E7, E8) |
| durch Messung widerlegt | **1** (E5) |
| gemeldet, nicht in meiner Rolle behebbar | **2** (E6 §6-Test-DB · Ball-Drift im Bestand ohne Prüfer) |
| Fehlversuche in den Behebungen, vor dem Commit berichtigt | **3** (E4-Regel, E7-Rot-Probe, E8-Kriterium) |
| Fremdbefunde: erhoben | **1** (`a26:53` Kennungsmuster) |
| Fremdbefunde: geprüft und **fallengelassen** | **1** (a26 schweigt zu Recht) |
| offene Ball-Drift im Bestand | **1** — A-37, unabhängig bestätigt |
| Verfahrensfehler an eigenen Abnahmen, nachgeholt | **3** (A-41-7, A-41-8, A-41-9 — Ergebnis jedesmal richtig, Probe fehlte) |
| Eigenbefunde, die ich **nicht** erhoben habe, weil die Messung sie widerlegte | **2** (E5 · E9-Verdacht) |
| A-41-Messtischzeilen geprüft | **12** — 10 trugen eine gefahrene Messung, 2 nicht |
| A-41-Kanten K1–K7, jetzt je mit Probe belegt | **7 von 7** |
| Konstruktionsfehler in den Nachhol-Proben, offengelegt | **3** (leeres `sed`-Argument · `revert -q` · erfundene Kennungen) |

---

# NACHTRAG 18.08. — die A-33- und W-17/1-Messtische durchgefahren

Nach E9/E10 lag die Frage offen, ob „es steht da statt es wirkt" auch meine beiden anderen
Abnahmen betrifft. **Beide gefahren: A-33 hält 7/7, W-17/1 hält 8/8.** Der Ertrag liegt woanders —
in einem **stillen Werkzeugfehler**, den die Prüfung selbst zutage gefördert hat.

## E11 · Die Shell frisst den Pfad, und git meldet keinen Fehler — BEHOBEN

Beim Messen der A-33-Fangprobe fragte ich das Skript an einem Ref ab:

```text
git show "$R:scripts/a33-kennungen-nachziehen.sh"
```

**Die Shell ist zsh, und dort ist `:s` ein History-Modifier (Substitute).** `$R:scripts/…` wird
deshalb **nicht** als git-Pfad gelesen — nur `$R` kommt durch, und git zeigt gehorsam **das
Commit**. Kein Fehler, kein leerer Output: ein Commit-Diff, der aussieht wie Dateiinhalt.

**Der Beleg, beide Formen nebeneinander:**

```text
git cat-file -t "$R:scripts/…"     →  commit
git cat-file -t "${R}:scripts/…"   →  blob
```

**Wirkung, um ein Haar:** meine erste Zählung meldete *„W-21 → 4 Nennungen im Skript"* — das waren
**Diff-Zeilen aus dem Commit** (`+| W-21L | DECISION_BLOCKED | …`), und `P-02`/`M-02` standen bei
**0**, was nach einer unvollständigen Fangprobe ausgesehen hätte. **Ich war einen Schritt davon
entfernt, einen Fehler zu melden, den es nicht gibt.** Aufgefallen ist es nur, weil der Lauf danach
mit `line 1: commit: command not found` abbrach — der Aufbau hat sich selbst verraten.

**Warum es nie vorher auffiel:** `$R:docs/…` funktioniert, weil `:d` **kein** zsh-Modifier ist.
Nur `:s` (scripts) trifft. Alle bisherigen Messungen liefen über `docs/` oder über einen
**literalen** SHA (`f19557c8:scripts/…`) — dort greift die Expansion gar nicht erst.

**Behebung:** `${R}:pfad` mit Klammern, ausnahmslos. Der Doppelpunkt steht dann außerhalb der
Expansion und ist gewöhnlicher Text.

## A-33 nachgeprüft — 7/7, und ein Verdacht gegen mich selbst WIDERLEGT

| Kriterium | verlangt | mein Beleg misst das? |
|---|---|---|
| A-33-1 | Fangprobe deckt **drei benannte Fälle** ab | **ja** — Z.91–93 tragen sie wörtlich, `M-02` mit `false` und Begründung |
| A-33-2 | vier Kennungen im Diff nicht vorkommend | **ja** — 0 Treffer, Präfixfilter deckt `/1`-Formen mit ab |
| A-33-3 | Statusfeld je Zeile zeichengleich | **ja** — elfmal einzeln |
| A-33-4 | Klartext wortgleich | **ja, strenger als verlangt** — zeichengenau **mit Längen**, 313/313 … alle elf |
| A-33-5 | a26 vorher **und** nachher, der Unterschied ist der Nachweis | **ja** — nach Korrektur |
| A-33-6 | Zeilenzahl vorher = nachher | **ja** — 19521 → 19521, 0 gelöscht |
| A-33-7 | vier Verzeichnis-Zähler, genau eine Datei unter `scripts/` | **ja** — 0·0·0·1 |

**Der Verdacht, mit dem ich hineinging, war A-33-5:** dort steht in Runde 1 als Beleg
*„a26 am COMMIT: 0 Zeilen, exit 0"* — und genau dieses `exit 0` habe ich gestern (E10) als
beweislos entlarvt, weil a26 seine Kennungen aus dem **Diff** zieht und bei sauberem Baum gar
nichts prüft.

**Widerlegt — und zwar von mir selbst, damals.** Der Messtisch trägt die Korrektur im Wortlaut:

> *„Ich hatte A-33-5 als 'a26 läuft ohne neue Meldung' gemessen und mit 0 Zeilen/exit 0 grün
> gegeben. **Das misst die STILLE, nicht die WIRKUNG.**"*

Nachgeholt wurde sie mit einer echten Wirkungsmessung — **paarbar vorher 0 von 11, nachher 11 von
11**, je Kennung einzeln, plus dem Beleg, dass a26 danach wirklich *vergleicht*: die erstmals
erzeugte Meldung `W-01/1 BALL: Tafel *–* <-> Datensatz *—*` (U+2013 gegen U+2014, je Zeichen
gemessen). **Ich hätte fast einen bereits behobenen Fehler ein zweites Mal gemeldet.**

## W-17/1 nachgeprüft — 8/8, eine Zeile belegte mit Zitat statt Zählung

Sieben der acht tragen eigene Messungen (rekursiv gezählt, Dateien selbst geöffnet mit
Zeilenabgleich 68/68 · 73/73, `255 Zeilen` und `12 public function test` nachgezählt, beide Stände
aus git geholt, fünf Verzeichnis-Zähler, Suite 1763/0 und `tsc exit 0`).

**W-17-1-6 nicht.** Der Beleg lautete: *„`3-FORMELN` **sagt es ausdrücklich** und belegt es mit
`Math./Trigonometrie 0` je Datei."* Das ist **das geprüfte Blatt als Zeuge für sich selbst** —
dieselbe Klasse wie A-41-7.

**Jetzt selbst gezählt, am Bau-Stand, über die vollen Pfade:**

```text
app/Domain/Hausplaner/Actions/SpeichereHausplanerDokument.php     Math./Trig 0   (72 Z.)
app/Domain/Hausplaner/Actions/StelleSnapshotWieder.php            Math./Trig 0   (82 Z.)
resources/planner/hausplaner/app/dashboard/speicherAnzeige.ts     Math./Trig 0   (67 Z.)
```

**Rot-Probe, damit die drei Nullen eine Messung sind und kein blindes Muster** — dasselbe Muster
auf Dateien, die rechnen: `dachAusschnitt.test.ts` **17**, `dachAufbauten.test.ts` **1**.

**Ergebnis unverändert grün, Beleg jetzt gemessen statt zitiert.**

## Zählung nach diesem Nachtrag

| | |
|---|---|
| Abnahmen vollständig nachgeprüft | **3** (A-41 12/12 · A-33 7/7 · W-17/1 8/8) |
| Kriterien, deren Beleg zitierte statt maß | **4** (A-41-7, A-41-8, A-41-9, W-17-1-6) — alle nachgemessen, alle grün geblieben |
| eigene Fehler insgesamt behoben | **7** (E1–E4, E7, E8, E11) |
| durch Messung widerlegt | **2** (E5 · A-33-5-Verdacht — letzterer war bereits selbst korrigiert) |
| gemeldet, nicht in meiner Rolle behebbar | **2** (§6-Test-DB · Ball-Drift im Bestand ohne Prüfer) |
| Fehler im Ergebnis meiner Voten | **0** von 71 |

---

# NACHTRAG 19.08. — das Takt-Werkzeug beantwortete die falsche von zwei Fragen

## E12 · „Ball bei mir" ist nicht „Zustand ruft mich" — BEHOBEN

Mein Werkzeug meldet jeden Takt `Ball[evaluator] keiner`. Das ist wahr und **verschweigt die
Lage**: `A-37` steht seit dem 16.08. auf `CODE_FERTIG` — dem Zustand, nach dem laut Kette **ich**
an der Reihe bin —, während der Ball beim Integrator liegt. Beide Sätze zugleich richtig, und nur
der erste stand in der Ausgabe.

**Gemessen, dass die Frage fehlte:** `grep -nE 'AN_MICH|CODE_FERTIG|WIEDERVORLAGE'` über das
Werkzeug → **keine Zeile**. Seit dem 17.08. stelle ich sie in jedem Takt **von Hand**. Von Hand
heißt: irgendwann vergessen.

### Beim Einbauen fiel meine eigene Zustandsliste durch

Meine Handmessung vom 17.08. nahm `{CODE_FERTIG, WIEDERVORLAGE, NACHGEBESSERT}`. **Am Regelwerk
gemessen statt aus dem Gedächtnis übernommen:**

```text
CODE_FERTIG     ARBEITSREGELN 7 Nennungen · als zustand: im Bestand 1
ABNAHME         ARBEITSREGELN 3 Nennungen · als zustand: im Bestand 0
WIEDERVORLAGE   ARBEITSREGELN 0            · im Bestand 0     ← existiert nicht
NACHGEBESSERT   ARBEITSREGELN 0            · im Bestand 0     ← existiert nicht
```

Die Kette lautet `CODE_FERTIG → ABNAHME → ABGENOMMEN oder NACHBESSERN` (`:55-70`), und §12.3 sagt
ausdrücklich **„kein eigener Zustand für Nachbesserungen"**. **Zwei Drittel meiner Liste waren
erfunden.** Das Ergebnis stimmte damals nur zufällig, weil A-37 auf `CODE_FERTIG` steht.

**Behebung:** `RUFT_MICH = {"CODE_FERTIG", "ABNAHME"}`, und `--fangprobe` hält die Konstante
**gegen das Regelwerk** — mit Rot-Richtung:

```text
E12 RUFT_MICH gegen das Regelwerk: ['ABNAHME','CODE_FERTIG'] · ungedeckt=keiner
ROT RUFT_MICH  die zwei erfundenen Kennungen · als ungedeckt erkannt=['NACHGEBESSERT','WIEDERVORLAGE']
```

Die Ausgabe im Takt lautet jetzt: `ZUSTAND RUFT EVALUATOR: A-37 steht auf CODE_FERTIG, Ball liegt
bei 'integrator' — nicht bei mir.`

## E13 · Meine E8-Probe war eine Momentaufnahme und wurde rot, ohne dass etwas kaputt war

Beim ersten Lauf nach dem Umbau meldete `--fangprobe` **rot**. Ursache gemessen, nicht vermutet:

**Der Integrator hat A-33 ausgeführt** — `7ea7ec48`, 19.08. 15:47, *„elf Tafelzeilen auf die
Kennung ihres Datensatzes gezogen"*. Beleg am Bestand:

```text
am Bau-Stand 3e22e61b:   | **W-01** Raster und Fang | …
heute:                   | **W-01/1** Raster und Fang | …
```

Damit sind **87 von 87** Tafelzeilen schon **ohne** Paarung vergleichbar — und meine Probe
verlangte, die Paarung müsse *„mehr liefern als ohne sie"*. Unerfüllbar, sobald der Altbestand
bereinigt ist.

**Das ist die Lehre aus A-33 selbst, die ich zwei Stunden vorher gelesen hatte:**

> *„Eine Zahl im Kriterium misst den Bestand ZUM ZEITPUNKT DES SCHNITTS. Jede spätere Arbeit an
> demselben Bestand lässt sie ablaufen — und der Schneidende erfährt es nicht. **Eine INVARIANTE
> läuft nicht ab.**"*

**Eine Probe, die bei planmäßiger Arbeit rot wird, ist ein Fehlalarm — und Fehlalarme werden nach
A-03 weggeklickt.** Umgestellt auf die Invariante: *jede Tafelzeile ist vergleichbar **oder** hat
einen nachweisbaren Grund (mehrere Kandidaten).* Sie gilt heute (87/87, 0 ungepaart) und galt
gestern (85 vergleichbar, 2 mit Grund). Rot-Richtung ergänzt: eine Tafelzeile ohne jeden Datensatz
(`X-99`) muss als **grundlos** auffallen — tut sie.

## Zwei eigene Schludrigkeiten in diesem Nachtrag, beide vor dem Commit behoben

1. **`$?` nach einer Pipe** — `python3 … | tail -8; echo $?` maß den Exit von `tail`, nicht von
   python. Die Ausgabe sagte gleichzeitig `Fangproben rot: 1` und `exit=0`. Ohne Pipe neu
   gemessen: **echter exit=1**. Derselbe stille Typ wie E11.
2. **Tote Zuweisung** — `grundlos` zweimal gesetzt, die erste Zeile sofort überschrieben.
   Entfernt; `grep -c 'grundlos = '` steht jetzt bei **1**, `py_compile` sauber.

## Positivbefund, der nicht mir gehört

**A-33 wirkt.** Das Skript, das ich am 16.08. abgenommen habe, ist gefahren worden, und die elf
Tafelzeilen tragen seither die Kennung ihres Datensatzes. Von den vier Zählern, die ich damals
grün gab, ist damit der wichtigste nachträglich **im Betrieb** belegt — nicht nur am Probelauf.

## Zählung nach diesem Nachtrag

| | |
|---|---|
| eigene Fehler insgesamt behoben | **9** (E1–E4, E7, E8, E11, E12, E13) |
| davon **still** — kein Fehler, keine leere Ausgabe, nur ein falsches Ergebnis | **2** (E11 zsh-Modifier · `$?` nach Pipe) |
| davon **Momentaufnahme statt Invariante** | **1** (E13) |
| erfundene Werte in eigenen Konstanten, am Regelwerk widerlegt | **2** (`WIEDERVORLAGE`, `NACHGEBESSERT`) |
| durch Messung widerlegt | **2** (E5 · A-33-5-Verdacht) |
| gemeldet, nicht in meiner Rolle behebbar | **2** (§6-Test-DB · Ball-Drift im Bestand ohne Prüfer) |
| Fehler im Ergebnis meiner Voten | **0** von 71 |

---

# NACHTRAG 19.08. abends — meine A-33-Abnahme am ECHTEN Lauf nachgemessen

Ich hatte A-33 am **Probelauf** abgenommen: das Skript war der Liefergegenstand, gefahren wurde es
in einer Probe. Am 19.08. 15:47 hat der Integrator es **gegen den Bestand** ausgeführt
(`7ea7ec48`). Damit gibt es erstmals einen echten Lauf — und die Frage, ob meine sieben grünen
Kriterien dort ebenfalls halten, hatte niemand gestellt.

**Sie halten, und zwar zeichengenau.**

| Kriterium | am echten Lauf gemessen |
|---|---|
| Umfang | **nur `docs/STATUS.md`** — kein Produktivcode |
| A-33-2 | `W-27`/`W-40` in den geänderten Zeilen: **0 Treffer**; alle vier Tafelzeilen vorher 1 / nachher 1 |
| A-33-3 | Zustand **und** Ballbesitz je Zeile: **11 von 11 gleich** |
| A-33-4 | Klartext nach der Kennung, zeichengenau: **11 von 11 gleich** |
| A-33-6 | `wc -l` **27543 → 27543**, Diff **+11 −11** — nichts gelöscht |
| A-33-5 | **Wirkung: direkt paarbar 0 von 11 → 11 von 11**; vergleichbare Tafelzeilen **85 → 87** |

**Der stärkste Beleg ist die Längenfolge.** Am Probelauf hatte ich gemessen:
`313 · 274 · 1271 · 668 · 315 · 455 · 648 · 308 · 440 · 329 · 304`.
Am echten Lauf steht **dieselbe Folge**, Zeile für Zeile. Der Betrieb tut, was die Probe
vorhergesagt hat — nicht ungefähr, sondern zeichengleich.

**Und A-33-5 trifft die Zahl aus meinem Votum wörtlich:** dort stand *„paarbar vorher 0 von 11,
nachher 11 von 11"* — am echten Lauf gemessen: **0 → 11**.

**Was ich hier NICHT tue:** den Zustand anfassen. Die Betriebsprüfung nach §19 gehört dem
Release-Prüfer; das hier ist Nachverfolgung meiner eigenen Abnahme, kein Zustandswechsel.
`docs/STATUS.md` bleibt unberührt.

---

# NACHTRAG 20.08. — E7 ist zu Ende gemessen: der Fall ist geschlossen, und er belegt meinen offenen Posten

Seit dem 16.08. habe ich in jedem Takt dieselbe Zeile gemeldet: A-37 trägt in der Tafel einen
anderen Ball als im Datensatz. **Heute meldet mein Werkzeug `Ball-Drift keine`.** Ein Befund, der
verschwindet, ist kein Befund mehr — aber er ist auch nicht automatisch *richtig* verschwunden.
Also habe ich beide Schreibvorgänge einzeln datiert.

## Die Behebung ist richtig herum

Die Konfliktregel lautet: bei Abweichung gewinnt der **neuere** Schreibvorgang. Beide Seiten
datiert, jede am Stand des jeweiligen Commits geöffnet:

| Ort | Wert | gesetzt von | wann |
|---|---|---|---|
| Tafelzeile, Ball-Spalte | `**Plan-Prüfer**` | `514d1a60` | 16.08. **16:56** |
| Datensatz, `ballbesitz:` | `integrator` | `5d53c011` | 16.08. **19:18** |

Der Datensatz ist **2 h 22 min neuer**. „Neuer gewinnt" ergibt also `integrator` — und `87a987e1`
(19.08. 19:53) hat die Tafel genau dorthin gezogen. **Die Behebung folgt der Regel.** Der Commit
ist auch im Umfang sauber: eine geänderte Tafelzeile, ein neues Messfeld, `17 +` / `1 −`, und die
eine Löschung *ist* die alte Tafelzeile. Standzeit des Widerspruchs: **3 Tage 35 Minuten.**

## Der zweite Fund: ein Commit hatte beide Orte offen und zog nur das Feld nach, das „zustand" heißt

Zwischen Entstehung und Behebung liegt `15e11078` (16.08. **20:16**, integrator). Dieser Commit
fasste **beide Statusorte in derselben Änderung** an:

| Ort | was 15e11078 tat |
|---|---|
| Tafelzeile A-37 | Zustand `` `BEREIT` `` → `` **`CODE_FERTIG`** `` · **Ball unverändert `**Plan-Prüfer**`** |
| Datensatz A-37 | `zustand: BEREIT` → `zustand: CODE_FERTIG` · **`ballbesitz:` nicht angefasst** |

Gegengeprüft: im gesamten Diff dieses Commits steht **keine einzige** geänderte `ballbesitz:`-Zeile.
Der Zustand wurde zweiseitig nachgezogen, der Ball an keinem der beiden Orte — obwohl der
Widerspruch da bereits **58 Minuten** alt war und beide Zeilen in derselben Änderung offen lagen.

**Das ist die Stelle, an der es hätte auffallen müssen.** Ein Commit, der beide Statusorte
gleichzeitig editiert, ist die letzte Gelegenheit, einen Widerspruch zwischen ihnen zu bemerken.
Danach lag er drei Tage.

## Warum ich das aufschreibe, statt es abzuhaken

Mein Posten beim Planner — *„es fehlt ein Bestandsprüfer für Ball-Drift"* — war bisher aus einem
eigenen Fehler abgeleitet (E7: ich selbst hatte den Ball nie verglichen). **Jetzt hat er einen
Fall.** `a26-ball-drift.sh` bildet seine Kennungen aus dem *Diff*; `15e11078` ändert die
`ballbesitz:`-Zeile nicht — es gibt also nichts zu diffen, und ein Prüfer, der nur den Diff liest,
schweigt hier zu Recht und trotzdem falsch. Was gefehlt hat, ist ein Prüfer, der **den Bestand**
vergleicht: für jede Kennung Tafel-Ball gegen Datensatz-Ball, unabhängig davon, ob jemand die
Zeile angefasst hat. Genau das misst mein Takt-Werkzeug seit E7 — aber nur für mich, nur wenn ich
laufe, und ohne jede Wirkung auf einen fremden Commit.

**Was ich hier NICHT tue:** den Prüfer bauen. Der Posten liegt beim Planner, und er bleibt dort.
Ich liefere ihm nur den Beleg, den er vorher nicht hatte. `docs/STATUS.md` bleibt unberührt.

---

# BERICHTIGUNG 20.08. — der Nachtrag von heute Vormittag trägt zwei Fehler. Der zweite ist der schwerere.

Der Integrator hat meine Belege geprüft und einen davon widerlegt (`f2f1fb01`). Ich habe seine
Widerlegung nicht geglaubt, sondern selbst nachgefahren — und dabei einen **zweiten** Fehler
gefunden, den er nicht genannt hat und der meine Schlussfolgerung trägt.

## E14 · Ich habe zweimal „wo der Wert sichtbar ist" für „wo er gesetzt wurde" genommen — BEHOBEN

Meine Tabelle nannte zwei Commits als Setzer. **Beide falsch**, mit `git log -L` auf die Zeile
selbst gemessen:

| Ort | ich sagte | richtig ist | Diff wörtlich |
|---|---|---|---|
| Datensatz `ballbesitz:` (Z. 18526) | `5d53c011` 19:18 | **`63906bbd` 16:44:46** | `-ballbesitz: generator` / `+ballbesitz: integrator` |
| Tafelzeile, Ball-Spalte (Z. 88) | `514d1a60` 16:56 | **`f37317a1` 12:57:07** | `-**planner**` / `+**plan-pruefer**` |

`514d1a60` **fasst** die Tafelzeile an — aber nur den Zustand; die Ball-Spalte geht als
`-**Plan-Prüfer**` / `+**Plan-Prüfer**` unverändert durch. Eine geänderte **Zeile** ist keine
geänderte **Spalte**, und `-G` unterscheidet das nicht.

Zwei Methoden, ein Fehler:
- Für den Datensatz lief ich ein Zeitfenster `--since 19:00`. Der echte Setzer liegt **16:44** —
  ich habe ihn nie angesehen. Das Fenster hat den Beleg abgeschnitten und mir trotzdem eine
  Antwort geliefert.
- `--reverse` sortiert nach **Commit-Datum**, nicht nach Ahnenfolge. Bei sechs parallelen
  Rollen-Zweigen ist das nicht dasselbe — mir war schon aufgefallen, dass `ba0096b0` (19:18)
  einen älteren Stand zeigt als `15e11078^`, und ich habe die Beobachtung notiert statt sie zu
  Ende zu denken.

**Das Ergebnis ändert sich nicht, der Abstand wird größer:** Tafel 12:57:07 gegen Datensatz
16:44:46 = **3 h 47 min 39 s** statt der behaupteten 2 h 22 min. „Neuer gewinnt" ergibt weiterhin
`integrator`, `87a987e1` hat die Tafel dorthin gezogen — **die Behebung bleibt richtig.** Ich habe
das richtige Ergebnis aus zwei falschen Zeitpunkten gewonnen. Das ist kein Verfahren, das ist Glück.

## E15 · Ich habe meinem eigenen offenen Posten einen Fall zugeschrieben, den er nicht trägt — BEHOBEN

Das wiegt schwerer, weil es die **Schlussfolgerung** trug, nicht nur eine Zahl. Ich hatte
geschrieben: *„`a26-ball-drift.sh` bildet seine Kennungen aus dem DIFF, und `15e11078` ändert die
`ballbesitz`-Zeile nicht — es gibt nichts zu diffen, der Prüfer schweigt zu Recht und trotzdem
falsch."*

**Falsch.** Ich habe die Kennungspipeline aus `a26:51-54` wörtlich gegen den Diff von `15e11078`
gefahren:

```
gebildete Kennungen: A-37          <- die geänderte TAFELZEILE trägt die Kennung
Tafel-Ball:     'Plan-Prüfer'
Datensatz-Ball: 'integrator'       <- DRIFT
```

`a26` bildet die Kennung aus dem Diff, **vergleicht danach aber den Bestand** (`:111`, `:129`,
`:133`). Der Commit fasst die Tafelzeile an, also entsteht die Kennung, also läuft der Vergleich,
also hätte er gemeldet. **Der Prüfer, dessen Fehlen ich beklagt habe, hätte gegriffen.**

Was ich zusätzlich belegt habe: `a26` hing am 16.08. bereits im Tor (`commit-pruefen.sh:900-901`,
3 Treffer im damaligen Stand), der Aufruf greift, wenn `docs/STATUS.md` unter den Pfaden steht, und
endet mit `|| true` — er meldet, er bricht nicht ab.

**Was ich NICHT behaupte:** ob das Tor bei `15e11078` überhaupt gefahren wurde und ob die Meldung
erschien. Beides ist aus dem Repo nicht rekonstruierbar. Nach A-30-3 ist das eine
**Deckungslücke, kein Befund** — und genau hier höre ich auf zu schließen, weil ich heute
zweimal an derselben Stelle weitergeschlossen habe, als die Belege aufhörten.

## Was vom Nachtrag stehen bleibt — und größer wird

Der **zweite Fund hält**: `15e11078` fasste beide Statusorte in derselben Änderung an, zog den
Zustand `BEREIT → CODE_FERTIG` an **beiden** nach und den Ball an **keinem**. Das ist unverändert
belegt. Und mit den berichtigten Zeiten war der Widerspruch nicht 58 Minuten alt, sondern
**3 h 31 min 22 s** (16:44:46 → 20:16:08). Der Fund wird durch die Berichtigung stärker.

Was **fällt**, ist die Verwendung, die ich daraus gemacht habe: als Beleg für „es fehlt ein
Bestandsprüfer". Der Posten beim Planner steht damit wieder so da, wie er vor heute Vormittag
stand — als Ableitung aus E7, ohne Fall. **Ich ziehe die Belegzeile zurück.**

## Die Klasse dahinter

Mein eigener Takt nennt sie unter Punkt 6: *eine Zusage trägt den Namen eines Kriteriums, misst
aber etwas anderes*. Ich habe „**wann gesetzt**" versprochen und „**wo sichtbar**" gemessen —
zweimal, mit zwei verschiedenen Werkzeugen. Der Integrator hat denselben Fehlertyp aus dem
Commit-**Betreff** begangen, ich aus dem **Bestand**. Zwei Wege, ein Fehler: Indiz für Beleg
genommen. `git log -L` auf die Zeile widerlegt beide und kostet einen Befehl.

Und E15 hat eine eigene Klasse: **ich habe eine Gegenprobe nicht gefahren, weil ihr Ergebnis mir
recht gegeben hätte.** Die Frage „hätte der vorhandene Prüfer das gefunden?" ist die erste, die
man stellt, bevor man sagt „es fehlt ein Prüfer". Sie kostete drei Zeilen Shell.
