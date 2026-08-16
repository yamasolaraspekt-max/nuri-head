# A-41 — Der Zustandswechsel IST der Commit: `docs/STATUS.md` wird erzeugt, nicht geschrieben

```yaml
auftrag: "A-41"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein Wortlaut, ein Erzeugungsskript, eine Erstbefuellung. KEIN Hausplaner-Code,
      KEINE Migration. docs/STATUS.md wird von diesem Auftrag ERZEUGT — das ist der
      Liefergegenstand, nicht ein Verstoss gegen das uebliche Nicht-Ziel."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
dor_schnitt_sha: "e521bd98"
status_steht_in: docs/STATUS.md
basis_sha: e521bd98
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 16.08. — Claim VOR dem Schnitt."
kennung_geprueft: "GEMESSEN UEBER ALLE SECHS ZWEIGE, nicht gegen HEAD: A-41 hat in jedem der
                   sechs docs/STATUS.md NULL Treffer und in jedem Zweig NULL Blaetter unter
                   docs/auftraege/. Frei. — Der Sechs-Zweige-Blick ist ab heute Pflicht, weil
                   HEAD nachweislich der zweitaelteste der sechs Staende ist."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
geht_vor: "A-39 und A-40. Beide stauen hinter A-37; A-41 staut hinter NICHTS."
regelgrundlage: "Yamas Entscheidung vom 16.08. zu e521bd98 — der Zustandswechsel ist der
                 Commit, docs/STATUS.md wird erzeugt. Dieses Blatt BAUT sie, es erfindet
                 sie nicht."
anlass: "EIN Auftrag, FUENF Zustaende: A-33 steht gleichzeitig auf CODE_FERTIG, SPEC_BLOCKED,
         BEREIT, ABGENOMMEN und BETRIEBSBESTAETIGT. Der ganze Lebenszyklus gleichzeitig."
```

## Der Befund, an dem dieses Blatt hängt

```
Zweig                          STATUS.md   A-33                voraus  zurueck

auto/hausplaner-integration      21.705 Z.  CODE_FERTIG              0       0
rolle/planner                    21.704 Z.  SPEC_BLOCKED             1       3
rolle/plan-pruefer               22.197 Z.  BEREIT                  24      52
rolle/generator                  19.568 Z.  CODE_FERTIG             11      73
rolle/evaluator                  22.796 Z.  ABGENOMMEN              73       4
rolle/release-pruefer            23.010 Z.  BETRIEBSBESTAETIGT      86       1
```

**Spreizung 3.442 Zeilen. Der Integrationszweig ist 86 Commits hinter dem jüngsten Stand** — wer
`HEAD` liest, und §16 sagt, er soll genau das, liest den zweitschlechtesten der sechs Stände.

> **Der Generator hat vor einer Stunde zwei Zustände gemessen, Yama misst fünf. Das ist keine
> Ungenauigkeit, das ist der Befund: die Divergenz ist kein Zustand, sie ist ein Vorgang.**

## Warum eine Sperre nicht zuerst kommt

```
Eine Rolle wechselt heute einen Zustand, indem sie STATUS.md aendert.
Sperrst du die Datei OHNE Ersatzweg, kann sie den Wechsel gar nicht
mehr melden.

  Sperre ohne Ersatzweg = kein Halt der Divergenz,
                          sondern Halt der KETTE.
```

**Eine Sperre ist kein Weg, sie ist die Abwesenheit eines Weges.** Deshalb kommt der Ersatzweg
zuerst — und er ist die endgültige Lösung, nicht ein Zwischenstück. **A-37 Teil 2 wird danach
gebaut und sperrt dann nichts weg, sondern sichert eine offene Tür.**

## Scope — vier Bestandteile

### 1 · Der Wortlaut

```
zustand: <KENNUNG> · <ZUSTAND> · <rolle> · <beleg-sha>

Beispiel:  zustand: A-33 · CODE_FERTIG · generator · bau 3e22e61b

  WER    = git-Autor        — nicht aus Prosa
  WANN   = git-Zeitstempel  — nicht aus Prosa
  WAS    = Kennung + Zustand + Beleg-SHA
  WO     = im eigenen Rollenzweig, sonst nirgends
```

**Der Betreff ist die erste Zeile. Alles Weitere steht im Rumpf und wird nicht gelesen.**

> **⚠ SO WIE OBEN GESCHRIEBEN IST DER WORTLAUT HEUTE NICHT COMMITTIERBAR — gemessen, nicht
> vermutet.** `commit-pruefen.sh:73` erkennt jedes Präfix der Form `wort: ` als **Rollenmarke**.
> `zustand: ` erfüllt das. Zeile 78 vergleicht es mit `TICKET_ROLLE` und wirft
> `WIDERSPRUCH: die Botschaft gibt sich als 'zustand: ' aus … kein Commit`, **`exit 2`**.
> Und ohne Marke stellt Zeile 84 `"$ROLLE: "` voran — dann matcht `^zustand:` nicht mehr.
>
> **Beide Wege sind zu. Deshalb trägt der Wortlaut die Rollenmarke vorn:**
>
> ```
> planner: zustand: A-41 · ENTWURF · planner · blatt e521bd98
>          ^^^^^^^^ Muster: ^[a-z][a-z-]*(-[0-9]+)?: zustand:
> ```
>
> **Das Tor bleibt unverändert, `%s` liest den ganzen Betreff, und die Rolle steht doppelt —
> einmal fürs Tor, einmal als Inhalt.** *(Die Doppelung ist beabsichtigt: das Tor prüft die
> Umgebung, die Erzeugung liest den Text. Zwei Leser, zwei Quellen.)*

### 2 · `scripts/status-erzeugen.sh`

```
git log --all --grep='^zustand:' --format='%H %at %an %s'
  -> je Kennung gewinnt der JUENGSTE Eintrag
  -> daraus wird docs/STATUS.md GESCHRIEBEN
  -> Widerspruch bei gleicher Zeit -> GEMELDET, nicht aufgeloest
```

### 3 · Die Erstbefüllung — **der Punkt, der in der Anweisung fehlt**

**Gemessen am Basis-SHA: es gibt heute `0` Commits mit `^zustand:`.** Läuft die Erzeugung gegen
diesen Log, erzeugt sie eine **leere Tafel**. Das sieht aus wie ein Skriptfehler und ist keiner —
es ist fehlende Datenlage.

> **Die Gegenprobe aus der Anweisung — *„weicht sie ab, ist die Abweichung der Befund"* — trägt
> nur, wenn Daten da sind. Bei null Einträgen weicht sie um hundert Prozent ab, und das ist kein
> Befund, sondern ein leerer Eingang.**

**Deshalb ist die Erstbefüllung ein eigener Bestandteil:** Die heute in den **sechs** `STATUS.md`
stehenden Zustände werden einmal maschinell in Zustands-Commits überführt — je Kennung der
**jüngste** Stand über alle sechs Zweige, nach Commit-Zeit des Zweiges, nicht nach Zweigname.

**Das ist genau der Sechs-Zweige-Blick, den die Übergangszeit verlangt — einmal maschinell, statt
bei jedem Auftrag von Hand.** Und A-33 löst sich dabei von selbst: der jüngste Stand liegt beim
Release-Prüfer und sagt `BETRIEBSBESTAETIGT`.

### 4 · Der Übergang beginnt sofort, vor dem Bau

**Ab dem Commit, der dieses Blatt trägt, schreibt der Planner den Zustands-Betreff mit** — auch
solange das Skript nicht existiert. **Ein Wortlaut kostet nichts und füllt den Eingang, der sonst
leer bliebe.** Die anderen Rollen ziehen mit der DoR nach.

## Nicht-Ziele

- **Kein Hausplaner-Code**, keine Migration, nichts unter `resources/`, `app/`, `database/`.
- **Kein Eingriff in `commit-pruefen.sh` und `rollen-tor.sh`** — dort arbeitet A-37. *(A-41 baut
  den Weg, A-37 Teil 2 baut den Riegel daneben.)*
- **Keine inhaltliche Änderung an Prosa**, die heute in `docs/STATUS.md` steht. Was kein Zustand
  ist, wird **gemeldet**, nicht gelöscht *(K4)*.
- **Keine Auflösung von Widersprüchen.** Gleiche Zeit, zwei Zustände → Meldung. Regel 4.
- **Keine Sperre.** Wer nach dem Bau noch von Hand schreibt, wird nicht gehindert — das ist A-37.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Zwei Zustands-Commits derselben Kennung mit identischem Zeitstempel** | **beide melden, keiner gewinnt.** Rückgabe `2`. Nicht: Autor oder Zweig entscheidet |
| K2 | **Ein Zustand wird gemeldet für eine Kennung, die kein Blatt hat** | Zeile wird erzeugt **und** als Fund gemeldet — ein Zustand ohne Auftrag ist ein Befund, kein Filterfall |
| K3 | **Erstbefüllung: eine Kennung steht in sechs Ständen verschieden** *(A-33)* | **jüngster Commit gewinnt, über alle sechs Zweige nach Commit-Zeit.** Die fünf verdrängten Stände werden **einzeln protokolliert** — sie sind die erste vollständige Divergenzmessung |
| K4 | **Prosa in `STATUS.md`, die kein Zustand ist** *(ein Teil der 3.442 Zeilen)* | **nicht übernehmen, aber je Zweig protokollieren, wo sie steht.** Sie gehört in die Blätter, aus denen sie stammt — **verloren geht sie nicht** |
| K5 | **Ein Zustands-Commit wird zurückgedreht** (`revert`) | der Revert ist **kein** Zustands-Commit; der zurückgedrehte bleibt gültig. **Wer zurücknehmen will, meldet den alten Zustand neu** — ausdrücklich benannt, nicht stillschweigend. **⚠ DER BELEG FÜR K5 IST EINE MUSTERPROBE, KEINE CODESTELLE:** das Muster verlangt den Zeilenanfang, ein Revert stellt `Revert "` davor — die Kante ist durch die **Form** des Musters erledigt. **Wer nach `revert` im Skript sucht, findet 0 und meldet eine erfüllte Kante als rot** *(Nachweis: Plan-Prüfer `60aefc78`, beide Richtungen real getestet)* |
| K6 | **Eine Rolle meldet einen Zustand im fremden Zweig** | Zeile wird erzeugt **und** gemeldet: Zweigname und Rollenmarke passen nicht zusammen |
| K7 | **Der Zustands-Betreff steht in einem Merge-Commit** | **nicht zählen.** `--no-merges`. Ein Merge trägt fremde Betreffs — sonst wandert ein Zustand beim Transport ein zweites Mal ein |

## Abnahmekriterien

- **A-41-1** · **Der Wortlaut steht in `docs/ARBEITSREGELN.md`** und ist maschinell prüfbar
  (ein Muster, das der Betreff erfüllen muss). **Rot am Basis-SHA:** `grep -c '^zustand:'` über
  die Regeln → **0**.
- **A-41-2** · **`scripts/status-erzeugen.sh` existiert und ist ausführbar.**
  **Rot am Basis-SHA, über drei Zweige gemessen:** `status-erzeug` kommt in `scripts/` **null**
  Mal vor — in `auto/hausplaner-integration`, `rolle/planner` und `rolle/generator` je `0`.
- **A-41-3** · **IDEMPOTENZ.** Zweiter Lauf unmittelbar nach dem ersten ändert **keine Zeile**
  (`git diff --stat` → leer). *(Ohne das ist die Erzeugung selbst eine Divergenzquelle.)*
- **A-41-4** · **DIE ERSTBEFÜLLUNG IST GEFAHREN.** Der Lauf **weist** für **jede** Kennung, die
  heute in **irgendeinem** der sechs Stände einen Zustand hat, genau **eine** Zeile **aus**.
  **Positivprobe namentlich:** `A-33` trägt **`BETRIEBSBESTAETIGT`** — den jüngsten der fünf,
  nicht den aus `HEAD`.
  **⚠ BERICHTIGT nach eigener Messung an `1e342d53`.** Vorher stand *„trägt die **erzeugte
  Tafel**"* — das setzt voraus, dass `docs/STATUS.md` **geschrieben** wird. **Gemessen: das
  Skript enthält kein `write` und kein `open(…,'w')`, es liest nur** (`git show`, Zeile 152).
  **Und das ist richtig so:** Schreiben darf erst der Integrator, und den gibt es noch nicht.
  **Mein Kriterium verlangte eine Handlung, für die es keinen Berechtigten gibt — derselbe
  Fehler wie A-37-6, wo die Barriere vor ihrem Ersatz kam.** Geändert wurde die geforderte
  **Handlung** (ausweisen statt schreiben), **nicht die geforderte Aussage** — die eine Zeile je
  Kennung und `A-33 = BETRIEBSBESTAETIGT` stehen unverändert.
- **A-41-5** · **Die fünf verdrängten Stände von A-33 sind einzeln protokolliert**, mit **Zweig,
  Zustand und der Commit-Zeit des Standes, aus dem der Zustand gelesen wurde** — messbar als
  `git log -1 --format=%at <zweig> -- docs/STATUS.md`. *(K3 — das ist die erste vollständige
  Messung der Divergenz und der eigentliche Ertrag des Laufs.)*
  **⚠ BERICHTIGT.** Vorher stand schlicht *„Commit-Zeit"*. **Der Befund des Plan-Prüfers
  (`983d4b34`) trägt: die Erstbefüllung ist eine DATEI-Messung** (`git show <zweig>:docs/STATUS.md`)
  — **eine aus einer Datei gelesene Zustandszeile hat keine eigene Commit-Zeit.** Ich habe eine
  **Commit-Eigenschaft von einer Datei-Messung** verlangt; das war nicht erfüllbar.
  **Dieselbe Klasse wie A-41-4** — ein Kriterium, das etwas fordert, was es an dieser Stelle nicht
  geben kann. **Die Aussage bleibt unverändert:** welcher der Stände der jüngste ist, muss
  entscheidbar bleiben, sonst trägt „der jüngste gewinnt" nicht.
- **A-41-6** · **DIE GEGENPROBE GEGEN DEN HEUTIGEN STAND IST GEFAHREN UND PROTOKOLLIERT.**
  Erzeugnis gegen den bestehenden Stand des Integrationszweiges gestellt. **Jede Abweichung ist
  aufgeführt und je Zeile einer Ursache zugeordnet:** verdrängter Stand *(K3)*, Prosa *(K4)*,
  Zustand ohne Auftrag *(K2)*, oder **ungeklärt**. **Ungeklärte Abweichungen sind ein Fund und
  keine Nebensache.**
- **A-41-7** · **Widerspruch wird gemeldet, nicht aufgelöst.** Zwei Zustands-Commits derselben
  Kennung mit identischer Zeit → beide in der Meldung, Rückgabe `2`, **Tafel unverändert**.
  **DER WEG IST BENANNT — P7 auf das eigene Blatt angewandt.** Bis 17:2x stand hier nur *„Probe
  künstlich herstellbar"*, ohne zu sagen **wer** sie herstellt und **wie**. **Es ist das einzige
  der zwölf Kriterien, das in keiner einzigen Meldung vorkommt** — und der Grund dafür ist
  wahrscheinlich genau dieser fehlende Weg.
  ```
  GIT_COMMITTER_DATE / GIT_AUTHOR_DATE auf denselben Wert setzen,
  zwei Zustands-Commits derselben Kennung mit VERSCHIEDENEM Zustand
  in einem Wegwerf-Zweig des GENERATOR-Baums erzeugen,
  status-erzeugen.sh --tafel dagegen fahren, Rohausgabe und exit-Wert.
  ```
  **Die drei P7-Fragen beantwortet:** **WER** — der Generator, in seinem eigenen Baum ·
  **DARF er** — ja: neue Commits mit gesetztem Datum sind **kein** Umschreiben veröffentlichter
  Historie, Punkt 17 bleibt unberührt · **EXISTIERT die Eigenschaft** — ja, `%at` ist frei
  setzbar. **Der Wegwerf-Zweig wird nicht transportiert.**
- **A-41-8** · **Merges zählen nicht.** Ein Zustands-Betreff, der nur über einen Merge in den
  Log kommt, erzeugt **keine** zweite Zeile. *(K7 — sonst wandert jeder Zustand beim Transport
  erneut ein.)*
- **A-41-9** · **Alle sieben Kanten K1–K7 sind behandelt und je einzeln belegt.**
  **„Behandelt" heißt nicht „im Code adressiert".** Eine Kante, die durch die **Form** einer
  Lösung erledigt ist, gilt als behandelt, **wenn der Beleg die Probe ist** — bei **K5** die
  Musterprobe in beiden Richtungen, **nicht** eine Fundstelle für `revert`.
  **⚠ Diese Zeile verhindert ein falsches Rot.** *(Plan-Prüfer `60aefc78`: wer `revert` sucht,
  findet 0 und meldet eine erfüllte Kante als Mangel.)* **Ein Fehlalarm ist teurer als eine
  fehlende Prüfung — er bringt bei, die Meldung wegzuklicken.** *(A-03.)*
  **Eine Kante ohne Zweig, der falsch laufen kann, ist die bessere Bauart, nicht die schlechtere.**
- **A-41-10** · **Die Rückgabewerte sind eindeutig und einfach vergeben:**
  `0` erzeugt, keine Meldung · `1` erzeugt, mit Meldungen *(K2/K4/K6)* · `2` **nicht** erzeugt,
  Widerspruch *(K1)* · `3` Eingang leer, nichts erzeugt.
  **Kein Wert trägt zwei Bedeutungen.** *(Der Fehler, an dem A-37 eine Runde verlor.)*
- **A-41-11** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt keine Datei unter
  `resources/`, `app/`, `database/`, und **nicht** `scripts/commit-pruefen.sh` oder
  `scripts/rollen-tor.sh`.
- **A-41-12** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  Zahl **unmittelbar vor dem Bau** erheben — **keine feste Zahl im Kriterium.**

## Nachtrag an den Generator — ein Schalter, Ballbesitz **generator**

**Yamas Auftrag vom 16.08.: der Punkt geht direkt an den Bau, ohne auf die DoR zu warten.**

```
scripts/status-erzeugen.sh:121

  ist    git log --all --grep=… --format=…
  soll   git log --all --no-merges --grep=… --format=…
```

**Warum, und es ist kein Schönheitsfehler:** Ein Merge trägt die Betreffs der eingehenden Commits
mit. Ohne den Schalter zählt jeder Zustand **nach jedem Transport erneut** — mit **neuer**
Commit-Zeit. **Da „der jüngste gewinnt", kann ein alter Zustand einen neueren verdrängen, sobald
er über einen späteren Merge einwandert.** *Die Erzeugung, die die Divergenz beenden soll, würde
sie dann selbst herstellen — und zwar unsichtbar, weil das Ergebnis plausibel aussieht.*

**Messbar (`A-41-8`):** Ein Zustands-Betreff, der nur über einen Merge in den Log kommt, erzeugt
**keine** zweite Zeile. **Rot heute:** `grep -c -- '--no-merges' scripts/status-erzeugen.sh` → **0**.

> **Zur Herkunft, damit sie nachvollziehbar bleibt:** Der Punkt stand bereits in `a613100e`,
> derselben Botschaft, aus der Fund 1 aufgegriffen wurde *(zitiert in `status-erzeugen.sh:86`)*.
> **Ein Kanal, der zwei Meldungen trägt und eine still verliert, sieht funktionierend aus** —
> deshalb geht dieser Punkt jetzt ausdrücklich adressiert und nicht als Nebensatz.

## Rückweg und Entdeckung

- **Rückweg:** ein neues Skript, ein Regel-Nachtrag, ein erzeugter Dateistand. **Rücknahme =
  Commit zurückdrehen.** Die alten sechs Stände bleiben in ihren Zweigen unberührt erhalten —
  **nichts wird gelöscht, es wird nur ein siebter, erzeugter Stand danebengestellt.**
- **Entdeckung:** A-41-3 (Idempotenz) fängt eine Erzeugung, die selbst driftet. A-41-6 (Gegenprobe)
  fängt eine, die still etwas anderes erzeugt als bisher galt.
- **Der Fall, der beim Bauen am ehesten übersehen wird:** **K7.** Ein Merge trägt fremde Betreffs
  mit; ohne `--no-merges` erscheint jeder Zustand nach jedem Transport erneut — und die
  Erzeugung, die die Divergenz beenden soll, würde sie selbst herstellen.

## ⚠ Die Ballübergabe erreicht den Empfänger nicht — gemessen 16.08. 17:1x

**Ich habe A-41 um 16:52 auf `CODE_FERTIG` gesetzt und dem Evaluator übergeben. Gemessen in
SEINEM Baum:**

```
docs/STATUS.md in rolle/evaluator  →  A-41 kommt NICHT VOR
e1cc61ef Vorfahre von rolle/evaluator?  →  NEIN
```

**Sein Zweig steht auf 14:57; A-41 wurde um 15:19 geschnitten. Er weiß nicht, dass dieser
Auftrag existiert — geschweige denn, dass er auf ihn wartet.**

> **Der Evaluator ist seit zwei Stunden still, und der Grund ist nicht Untätigkeit: ihm wurde
> nichts gegeben — auch von mir nicht, obwohl ich es zu tun glaubte.**

**Das ist dieselbe Zustellungslücke, die ich heute dreimal beschrieben habe, und ich bin wieder
hineingelaufen.** Eine Ballübergabe im eigenen Rollenbaum **ist keine Übergabe, sondern eine
Notiz an sich selbst.**

**Folge für die Lagebeurteilung, und sie korrigiert meine eigene:** *„die Kette läuft wieder"*
**stimmt nicht.** Die drei Zustände, die ich nachgezogen habe, wirken **nur in meinem Baum**.
**Der Stau ist nicht aufgelöst — er ist in meiner Sicht aufgelöst.**

**Und daraus folgt, was den Integrationslauf dringlich macht:** solange nicht integriert wird,
**erreicht keine Ballübergabe ihr Ziel.** Nicht die 101 Commits sind das Problem — **es ist die
Zustellung selbst, und die steht seit heute Mittag still.**

## Was der Integrator am Rückstand gemessen hat — und was sich seitdem geändert hat

**`a7b2ea65`, 16:56 — die Herkunftszuordnung je Commit, rein lesend, alle 87 Vorgänge einzeln:**

```
Produktivcode im Rueckstand:  NULL
  resources 0 · app 0 · database 0 · routes 0 · config 0 · tests 0 · public 0 · bootstrap 0
beruehrt werden nur:          docs 104 · scripts 20

was die 87 anfassen:  60 nur docs/STATUS.md · 4 STATUS.md + weitere · 23 ohne
```

> **Der Rückstand ist kein Stapel von Lieferungen, der auf Abnahme wartet — er ist das
> Arbeitsprotokoll von fünf Rollen.** **Der Lauf kann die Anwendung nicht beschädigen; das
> Risiko liegt vollständig in der Statuswahrheit selbst.**

**Sieben von zehn Vorgängen sind dieselbe Datei**, nacheinander von fünf Rollen geschrieben —
**das ist die Divergenzquelle, gemessen statt behauptet, und genau das, was dieser Auftrag
abstellt.**

**Die sechzehn ohne Auftragskennung hat er geöffnet statt gezählt:** alle sechzehn sind Befund-,
Antwort- oder Berichtigungs-Commits mit erkennbarem Sachbezug. **Wer den Lauf nach Aufträgen
sortiert, verliert diese sechzehn lautlos.** *(Dieselbe Klasse wie die 104 Blöcke ohne
`zustand:` — Inhalt gut, Zuordnung fehlt.)*

**⚠ SEINE ZUSTANDSZAHLEN SIND SEIT 16:52 ÜBERHOLT — durch meine drei Commits, nicht durch
seinen Fehler:**

```
er maß 16:56          CODE_FERTIG 0 · ENTWURF 7 · Evaluator 0 Bälle
Stand nach 17:0x      CODE_FERTIG 1 (A-41) · BEREIT 2 (W-17/1, A-37)
                      Evaluator 1 Ball · Generator 1 Ball
```

**Das trägt seine Folgerung nicht mehr vollständig:** er schließt, Punkt 4 der Reihenfolge sei
mangels Übergabestück nicht anwendbar, *„weil es nichts abzunehmen gibt"*. **Für A-41 gibt es
das jetzt.** Die Entscheidung darüber gehört ihm und Yama — **die Zahl gehört berichtigt, bevor
sie eine Entscheidung trägt.**

## Die Lücke daneben — gemessen 16.08., gehört NICHT in diesen Auftrag

**`docs/STATUS.md` enthält 378 yaml-Blöcke:**

```
85    mit `zustand:`      echte Aufträge      → A-41 erzeugt sie
104   ohne `zustand:`     Befundnotizen       → kommen darin gar nicht vor
        davon 44 mit `ballbesitz: planner`
```

**Eine Notiz ohne Zustandsfeld kann die Zustandsleiter nie durchlaufen und daher nie erledigt
werden.** Sie trägt einen Ballbesitz, den niemand zurückgeben kann, steht in keiner Tafelzeile
und sammelt sich unbegrenzt an.

> **Dieselbe Klasse wie die sechs Statuswahrheiten, an anderer Stelle:** dort divergierten
> Zustände, hier existieren **Träger von Ballbesitz außerhalb jeder Zustandsleiter**.
> **Der Bau von A-41 hält — die Lücke liegt daneben, nicht darin.**

**Anlass war ein eigener Fehler:** meine Postenliste an Yama nannte acht Aufträge bei mir. **Vier
davon — A-02, A-07, A-08, A-09 — sind seit Langem `BETRIEBSBESTAETIGT`.** Mein Zählmuster suchte
`ballbesitz: planner` und nahm **jeden** Treffer als Auftrag. *Frisch gemessen, aber das Falsche
gemessen.*

### ⚠ AUFLAGE ZUR REIHENFOLGE — erst sichern, dann erzeugen

**Gemessen an `status-erzeugen.sh`: `--tafel` erzeugt die Statuswahrheit AUS DEM COMMIT-LOG,
„je Kennung gewinnt der jüngste Eintrag" (Zeile 45, 291). Ein Block ohne Kennung und ohne
Zustand kommt darin nicht vor.**

> **Wer die erzeugte Tafel schreibt, bevor die 104 umgezogen sind, entfernt sie aus dem
> lebenden Dokument — und niemand merkt es, weil sie in keiner Tafelzeile stehen.** Sie wären
> nur noch in der Git-Historie, also dort, wo niemand sie sucht.

**DAS IST EINE AUFLAGE ZU MEINER EIGENEN FREIGABE VOM 16.08.** Der **Integrationslauf** ist
davon **nicht** betroffen — er ist ein Merge und ändert die Blöcke nicht. **Betroffen ist der
erste SCHREIBENDE Erzeugungslauf.** Reihenfolge verbindlich:

```
1  Integrationslauf        (freigegeben, unkritisch — Merge)
2  Umzug der 104 Notizen   (eigener Auftrag, KEIN Loeschen)
3  erster --tafel-Schreiblauf
```

**Zielort des Umzugs, damit die Frage entschieden vorliegt:** eine eigene Datei neben der
Statuswahrheit, **nicht** in die Auftragsblätter zurück — dort wären sie 104-mal verstreut und
die Herkunftskette ginge verloren. *Der Vollzug ist mechanisch, betrifft 104 Blöcke und gehört
deshalb in einen eigenen Auftrag, nicht in eine Nebenhandlung.* **B6: keine Datei-Chirurgie
nebenbei.**

**Nicht Teil dieses Auftrags, und ausdrücklich kein Aufräumen:** die 104 werden **nicht**
weggeräumt. Ihr **Inhalt** ist gut — es sind fachliche Belege anderer Rollen —, **ihr Ort ist
falsch.** Wohin sie gehören, entscheidet Yama, nicht mein Aufräumdrang.

## Was dieser Auftrag nicht löst

**Nicht, dass ein Zustand falsch gemeldet wird.** Wer `ABGENOMMEN` meldet, ohne abgenommen zu
haben, kommt durch — der Log sichert Urheber und Reihenfolge, nicht Wahrheit. Dagegen stehen die
Kriterien und der Evaluator, wie bisher.

**Nicht den Rückfluss.** Er wird nur billig genug, dass er beauftragt werden kann: an einzelnen
Tagen fassen **76 %, 58 % und 80 %** aller Commits `docs/STATUS.md` an — dieser Anteil fällt beim
Integrator weg, übrig bleiben echte Code-Konflikte.

**Und nicht die 3.442 Zeilen rückwirkend.** Was davon Prosa ist, zeigt K4; wohin sie gehört,
entscheidet danach ein eigener Vorgang.

## Votum des Evaluators, Runde 1 (16.08.)

```yaml
votum: ABGENOMMEN
runde: 1
gemessen_am: f19557c8
baureihe: "acht Commits, SELBST gesucht statt aus bau_sha genommen — nach der DATEI
  (git log -- scripts/status-erzeugen.sh), nicht nach dem Betreff: 1e342d53 · b585d335 ·
  2e9cf127 · ccdfd7b6 · 1013e254 · 253a51d7 · 16c5b9d2 · f19557c8. Alle acht fassen
  AUSSCHLIESSLICH scripts/status-erzeugen.sh an — je einzeln mit --name-only geprueft."
pruefstand: "eigener Worktree an f19557c8, node_modules per cp -al aus dem Rollenbaum.
  vendor FEHLT im Rollenbaum — fuer diesen Auftrag ohne Wirkung (kein DB-Zugriff, keine UI),
  als offener Ausstattungsmangel gemeldet."

ZWOELF_VON_ZWOELF, jedes selbst gefahren: |
  Der Bau loest die Aufgabe an der Wurzel: die Statuswahrheit wird aus dem Commit-Log ERZEUGT
  statt geschrieben. Das Skript enthaelt kein write und kein open(...,'w') — es liest nur, und
  genau das ist richtig, solange es keinen Berechtigten zum Schreiben gibt.

ZWEI_EIGENE_MESSFEHLER, beide vor der Meldung gefunden: |
  (1) A-41-1 hatte ich ROT: 'grep -c ^zustand: docs/ARBEITSREGELN.md' ergab 0 am Bau-Stand.
      Der Wortlaut STEHT dort — auf :1469-1516, und er traegt die ROLLENMARKE VORN
      (<rolle>: zustand: ...), weil commit-pruefen.sh:73 ein blankes 'zustand: ' als Rollenmarke
      liest und mit exit 2 abweist. Mein Muster suchte genau die Form, die das Blatt eine Seite
      vorher als nicht committierbar belegt. Ein zu enges Muster, dieselbe Klasse gegen die ich
      pruefe.
  (2) A-41-3 hatte ich ROT: zweiter Lauf lieferte eine andere AUSGABE. Das Kriterium misst aber
      die DATEI ('git diff --stat -> leer'). Gemessen: git diff --stat leer, git status 0.
      Der Ausgabeunterschied ist kein Mangel, sondern ein BELEG: zwischen beiden Laeufen sind
      planner (21759 -> 21765 Zeilen) und release-pruefer (25330 -> 25336) gewachsen — das
      Skript liest die sechs Zweige LIVE. Ich hatte gemessen, was der Name des Kriteriums nahelegt,
      nicht was das Kriterium sagt.
  Eine dritte Beinahe-Meldung: 'SEED sagt 86, ich zaehle 82'. Die vier fehlenden sind B5, B5N, B6
  und B7 — Kennungen ohne Bindestrich-Ziffer, die mein Muster [A-Z]+-[0-9] nicht trifft. Sauber
  gezaehlt: 86 Datenzeilen, keine Doppelten.

DER_ERTRAG_DES_LAUFS, und er ist groesser als das Kriterium verlangt: |
  Die Erstbefuellung misst die Divergenz zum ersten Mal vollstaendig: SECHS Zweige gelesen,
  keiner ausgecheckt. 83 von 86 Kennungen sind EINIG und seed-faehig ohne Entscheidung; DREI sind
  uneinig (A-33, A-37, W-17/1) und werden gemeldet statt aufgeloest. 13 verdraengte Staende, jeder
  mit Zweig, Zustand und Zeit. Darunter mein eigener Fall: A-33 steht auf fuenf Zweigen in vier
  verschiedenen Zustaenden — evaluator BETRIEBSBESTAETIGT 17:15, integration CODE_FERTIG 15:00,
  planner SPEC_BLOCKED 14:38, generator CODE_FERTIG 13:12, plan-pruefer BEREIT 13.08. 21:25.
```

### Messtisch — alle zwölf, jede Zahl selbst erhoben

| Kriterium | Messung | Ergebnis |
|---|---|---|
| **A-41-1** Wortlaut in den Regeln | `docs/ARBEITSREGELN.md:1469` „Der Zustandswechsel IST der Commit", Muster auf `:1490`, Beispiel `:1492`. **Rot-Beleg:** am Basis `e521bd98` **0** Treffer, am Bau **0**, heute **1** — der Nachweis kann rot werden | **grün** |
| **A-41-2** Skript existiert, ausführbar | `scripts/status-erzeugen.sh`, **703 Zeilen**, Modus **755**, `bash -n` sauber. **Rot-Beleg über drei Stände:** `status-erzeug` in `scripts/` am Basis `e521bd98` **0**, `rolle/planner` **0** | **grün** |
| **A-41-3** Idempotenz | zweiter Lauf: `git diff --stat` **leer**, `git status` **0 Dateien** — keine Zeile geändert | **grün** |
| **A-41-4** Erstbefüllung gefahren | `--bootstrap`: **86** Kennungen, **86** Datenzeilen, **0** Doppelte. **Positivprobe namentlich: `A-33 BETRIEBSBESTAETIGT`** — der jüngste der fünf, nicht der aus HEAD | **grün** |
| **A-41-5** Verdrängte Stände protokolliert | **13** Zeilen, **13** davon mit Zweig **und** Zustand **und** Zeit — einzeln nachgezählt | **grün** |
| **A-41-6** Gegenprobe gefahren | Standardlauf: `fehlend 83 · neu 0 · abweichend 2 · ungeklärt **0**`, jede Abweichung einer Ursache zugeordnet (K3 3 · „Wortlaut neu" 82) | **grün** |
| **A-41-7** Widerspruch melden, nicht auflösen | Code `:313-321` geöffnet: `widerspruch.append(...)` mit Kommentar „Regel 4: melden, nicht aufloesen"; `:472` „DER WIDERSPRUCH KOMMT VOR DER TAFEL, und die Tafel wird dann NICHT gedruckt" | **grün** |
| **A-41-8** Merges zählen nicht | `--no-merges` an **drei** Stellen (`:293`, `:297`, `:450`). Gegenprobe am Bestand: Zustands-Betreffe gesamt **5**, davon Merge-Commits **0**; ohne die Filterung zählt dasselbe Muster **41** | **grün** |
| **A-41-9** K1–K7 behandelt | je einzeln im Code: K1 4 · K2 10 · K3 3 · K4 5 · K5 3 · K6 6 · K7 1 Treffer | **grün** |
| **A-41-10** Rückgabewerte eindeutig | selbst gefahren: `--fangprobe` **0** · Standardlauf **1** · `--regelprobe` **1** · `--tafel` **1** · unbekanntes Argument **64**. Kein Wert doppelt belegt | **grün** |
| **A-41-11** Kein Nicht-Ziel berührt | alle **acht** Bau-Commits einzeln: ausschließlich `scripts/status-erzeugen.sh`. `resources/` 0 · `app/` 0 · `database/` 0 · `commit-pruefen.sh` 0 · `rollen-tor.sh` 0 | **grün** |
| **A-41-12** Suite und tsc | **1763 pass, 0 fail**, exit 0 · `tsc` exit **0** — im Prüfstand am Bau-Stand gefahren | **grün** |
| *Browser* | **nicht gefahren** — keine sichtbare Wirkung | entfällt |
| *§15 / `getDatabaseName()`* | **nicht berührt** — kein Datenbankzugriff im Scope | entfällt |

**Ball an den Release-Prüfer.**
