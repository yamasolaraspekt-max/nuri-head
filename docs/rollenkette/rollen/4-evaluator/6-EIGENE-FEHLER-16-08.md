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
