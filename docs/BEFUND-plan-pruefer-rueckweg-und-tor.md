# BEFUND plan-pruefer 16.08. — der Rückweg ist offen, und dieselbe Runde hat mich aus der Statuswahrheit ausgesperrt

> Dieser Befund steht **nicht** in `docs/STATUS.md`, weil das Rollen-Tor seit dieser Runde
> jedem außer dem Integrator das Schreiben dorthin verwehrt. Der Wortlaut der Sperre:
> `ROLLEN-TOR VERSTOSS 'plan-pruefer' aendert docs/STATUS.md ausserhalb des Integrations-Checkouts.`

++ b/docs/STATUS.md

```yaml
auftrag: "P-07"
titel: "DER RUECKWEG IST OFFEN — von 103 fehlenden Commits sind 5 geblieben; und mein Zweig zeigt jetzt auf denselben Commit wie der des Release-Pruefers"
rolle: plan-pruefer
zeit: "16.08. 19:38"
mess_stand: c698a10e19e9c7de406860a2290572a79794a066
baum: "sauber (0 Eintraege)"
was_passiert_ist: |
  Um 19:36:20 hat c698a10e meinen Zweig mit rolle/release-pruefer zusammengefuehrt.
  In meinen Baum sind 343 Commits eingegangen. Er traegt jetzt alle SECHS
  Werkzeugverzeichnisse, die ihm gefehlt haben (W-25, W-26, W-28, W-29, W-30, W-43),
  und die Blaetter von A-40 und A-42, die er vorher gar nicht hatte.
p07_und_p09_sind_aufgeloest: |
  Erreichbarkeit meiner Befunde, jetzt gemessen:
    rolle/planner          fehlen  5   (vorher 103)
    rolle/generator        fehlen  1
    rolle/evaluator        fehlen  1
    rolle/release-pruefer  fehlen  0
  Der juengste meiner Commits liegt beim Planner jetzt bei 19:22 statt bei 13:45.
  Damit ist die Ursache erledigt, die heute Abend drei Fehlbefunde erzeugt hat —
  meinen K4-Befund, meinen W-25-Befund und die Reifegrad-Berichtigung des Planners.
  Der Generator hat um 19:34 mit 2a95b64e die vier Reifegrade nachgezogen.
neuer_zustand_zwei_zweige_ein_commit: |
  refs/heads/rolle/plan-pruefer und refs/heads/rolle/release-pruefer zeigen beide auf
  c698a10e. Mein Worktree steht weiterhin auf rolle/plan-pruefer — geprueft, nicht
  angenommen —, ich schreibe also in meinen eigenen Zweig. Aber die beiden Zweige sind
  im Moment nicht unterscheidbar. Das ist kein Fehler, den ich beheben kann oder darf;
  ich melde ihn, weil die Einzelschreiber-Regel auf getrennten Zweigen aufbaut.
a42_dritte_klasse_bestaetigt: |
  1eabd4bc (planner, 19:34) meldet eine dritte Blockklasse in docs/STATUS.md.
  Nachgemessen an meinem jetzt vollstaendigen Baum:
    Bloecke gesamt        442   (er 444)
    mit zustand            90   (er  90)  — exakt
    auftrag ohne zustand  167   (er 165)
    WEDER noch            185   (er 189)
    Summe 90+167+185 = 442, geht auf
  Die Abweichungen sind Zeitversatz: zwischen seinem Commit und meiner Messung liegen
  zwei eigene Bloecke von mir. Die Sache stimmt vollstaendig — es gibt drei Klassen,
  die dritte ist die groesste, und A-42 kennt nur zwei.
mein_a42_befund_gilt_weiter: |
  A-18 ist auch am vollstaendigen Baum unsichtbar: 258 auftrag-Zeilen, 257 erfasst.
  Der Befund von 19:36 haengt also nicht an meinem alten Stand. A-42s Messvorschrift
  verliert weiterhin genau einen Block, und K4 faengt ihn nicht, weil er selbst
  nicht kaputt ist.
ballbesitz: yama
```

## Nachtrag 19:40 — die Sperre ist scharf geworden

**Gemessen, nicht vermutet:**

- `docs/STATUS.md` wird abgewiesen, Rückgabe 1.
- Ein anderer Pfad geht durch: eine Probedatei `docs/PROBE-TOR.md` wurde als `fb072f35` angenommen.
  **Die Sperre betrifft also genau eine Datei**, nicht meine Rolle insgesamt.
- Bedingung im Werkzeug (`scripts/rollen-tor.sh:324`):
  `INTEGRATOR_DA="$(git log --all --format=%s --grep='^integrator:')"` — sobald ein Commit mit
  der Rollenmarke `integrator:` existiert, ist A-37 Teil 2 scharf.

**Eigener Fehler in dieser Runde:** Die Probedatei `docs/PROBE-TOR.md` ist dadurch im Bestand
gelandet. Ich habe versucht, sie zurückzunehmen; das Werkzeug weist die Löschung nach F-14 ab
(`was nicht geschrieben wurde, wird auch nicht belegt`). Sie steht jetzt als Altlast im Baum und
gehört entfernt — durch den, der es darf. Ich melde sie, statt sie an der Regel vorbei zu löschen.

**Was das für meine Wache heißt:** Befundblöcke können ab sofort nicht mehr in
`docs/STATUS.md` geschrieben werden. Bis Yama etwas anderes anordnet, legt der Plan-Prüfer
seine Befunde in Dateien dieser Form ab. Der Ball für beides — die Probedatei und der Ort
künftiger Befunde — liegt bei **Yama**.

---

## 19:42 — die Statuswahrheit ist eingefroren, und der einzige Schreiber hat sie nie geschrieben

**Anlass:** A-37 wurde um 19:38 mit `fb59f6cc` als CODE_FERTIG gemeldet. Das ist ein
Ballwechsel in meiner Bahn, also die Meldepflichten geprüft.

**Bau-SHA existiert:** `97f1dd00`, 16.08. 19:14, *„A-37-17 belegt — alle sechs Kanten je
einzeln gefahren"*. ✓

**Aber er steht in keinem Feld.** Gemessen über alle fünf Zweige plus Integration:

| Zweig | A-37 letzter Zustand |
|---|---|
| HEAD · generator · release-pruefer · planner · integration | **BEREIT** |

**Der Grund ist kein Versäumnis des Generators.** Sein Commit ändert ausschließlich
`scripts/rollen-tor.sh` (16 Zeilen) — er konnte `docs/STATUS.md` nicht anfassen, weil
dieselbe Sperre auch ihn trifft. Der Zustandswechsel steht deshalb nur im Commit-Betreff.

**Der einzige berechtigte Schreiber schreibt nicht:**

- Commits mit Rollenmarke `integrator:`: **3** — 16:17, 16:56, 17:29
- davon mit `docs/STATUS.md` im Scope: **0**
- letzter Integrator-Commit: **17:29**, also seit über zwei Stunden still

**Letzte Änderung an `docs/STATUS.md`: mein eigener Commit `dab4086b` um 19:35** — eine
Minute bevor die Sperre scharf wurde. Seither steht die Statuswahrheit still, während
Generator (19:38) und Planner (19:39) weiterarbeiten.

**Was das heißt:** Die Zustandskette nach §3 kann nicht mehr fortgeschrieben werden. Jede
Rolle kann ihren Zustand nur noch im Commit-Betreff ansagen, und A-20 (Blatt + Tafelzeile +
Datensatz) ist ab sofort für jede neue Zustandsänderung verletzt. Der Rückstau ist heute
noch klein — **eine** Ansage —, aber er wächst mit jedem Ballwechsel.

**Ball bei Yama.** Entweder der Integrator übernimmt das Schreiben der Statuswahrheit
tatsächlich, oder die Sperre braucht eine Ausnahme für Zustandsfelder. Ich kann weder das
eine noch das andere entscheiden, und die Sperre selbst ist als A-37-Bau gewollt.

---

## 19:44 — Fortschreibung, und eine Einschränkung meiner eigenen Prognose

Ich hatte um 19:42 geschrieben, der Rückstau *„wächst mit jedem Ballwechsel"*. **Gemessen ist
er nach acht Minuten immer noch eins.** Die Prognose war zu weit, und der Grund ist lehrreich.

**Zwei Zustandsansagen im Betreff seit dem Einfrieren, je einzeln gegen den Datensatz geprüft:**

| Zeit | Commit | Kennung | Betreff | Datensatz | |
|---|---|---|---|---|---|
| 19:42 | `b55305e6` | W-17/1 | BETRIEBSBESTAETIGT | BETRIEBSBESTAETIGT | **deckt sich** |
| 19:38 | `fb59f6cc` | A-37 | CODE_FERTIG | BEREIT | **klafft** |

**Warum W-17/1 nicht klafft:** Der Zustand stand schon vorher dort — zuletzt mitgeschrieben in
`2bab146d` um 18:42, also vor der Sperre. Der Release-Prüfer *bestätigt* um 19:42 einen
Zustand, statt einen neuen zu setzen. Eine Bestätigung braucht keinen Schreibvorgang.

**Genauer gefasst:** Der Rückstau wächst nicht mit jedem Ballwechsel, sondern **nur mit jedem
echten Zustandswechsel**. Das ist deutlich seltener — heute Abend genau einer.

**Die Rollen haben sich bereits angepasst.** Der Release-Prüfer schreibt in
`docs/RELEASE-PRUEFUNG-A-41.md` (37 Zeilen), ich in diese Datei. Beide weichen auf eigene
Dateien aus, ohne dass es jemand angeordnet hat. Das funktioniert für Befunde und Berichte —
**für Zustandsfelder funktioniert es nicht**, weil deren Ort nach A-20 festgelegt ist.

**Der Ball bei Yama bleibt derselbe und wird dadurch nicht dringender, aber klarer:** es geht
nicht um den Betrieb der Kette, der läuft. Es geht um genau eine Sache — wer nach A-37 Teil 2
die Zustandsfelder schreibt.

---

## 19:47 — A-37 CODE_FERTIG: Meldepflichten geprüft, Ballwechsel bestätigt

**Der Ball geht an den Evaluator.** Ich bestätige ihn hier, weil ich ihn im Datensatz nicht
bestätigen kann — A-37 steht dort weiterhin auf `BEREIT`, siehe oben.

**Meldepflicht 1 — SHA existent:** `97f1dd00`, 16.08. 19:14, *„A-37-17 belegt — alle sechs
Kanten je einzeln gefahren, Rohausgabe"*. ✓

**Meldepflicht 2 — SHA in einem Feld:** ✗ **nicht erfüllbar.** Der Zustandswechsel steht nur
im Commit-Betreff von `fb59f6cc`. Das ist kein Versäumnis des Generators, sondern die Folge
der Sperre; sein Commit ändert ausschließlich `scripts/rollen-tor.sh`.

**Meldepflicht 3 — Scope-Diff selbst gemessen.** Basis `bc2125d9`, fünfzehn Bau-Commits,
Dateien einzeln ausgezählt:

| Datei | Commits |
|---|---|
| `scripts/rollen-tor.sh` | 12 |
| `scripts/commit-pruefen.sh` | 4 |
| `scripts/module-nachziehen.sh` | 1 |

**Nicht-Ziele: alle null.** `docs/STATUS.md` 0, `resources/` 0, `app/` 0, `database/` 0,
`routes/` 0. Das Nicht-Ziel *„KEINE Änderung an docs/STATUS.md"* ist über den gesamten Bau
eingehalten — bemerkenswert, weil genau dieser Bau die Sperre erzeugt hat, die es erzwingt.

**Ein Befund, klein:** Das `art`-Feld kündigt *„Erweiterung von `scripts/commit-pruefen.sh`
plus ein neues Prüfskript"* an — also zwei Dateien. Gebaut wurden **drei**. Die dritte,
`scripts/module-nachziehen.sh`, stammt aus `4577fd1d` *(„A-37-12 bis A-37-16 gebaut — der
Modulstand")* und ist damit durch Kriterien gedeckt, nur nicht im `art`-Feld angekündigt.
Alle drei liegen unter `scripts/`, keine berührt ein Nicht-Ziel.

**Urteil: der Bau hält seinen Rahmen.** Was der Abnahme fehlt, ist nicht der Beleg, sondern
der Ort, an dem er stehen müsste.

**Eigene Fangprobe:** Mein erster Lauf zählte vier Dateien, darunter `scripts/status-erzeugen.sh`.
Das Muster `grep -i 'a-37'` traf Commits, die A-37 nur erwähnen. Mit dem engeren Muster
`^generator: (zustand: )?A-37|A-37-[0-9]|A-37 Teil` bleiben drei. Die vierte war meine.

---

## 19:50 — A-42-5 geprüft: die Kanten tragen, und K4 trifft zahlengenau

**Zweigprobe zuerst:** Das Blatt steht in allen fünf Ständen einheitlich auf 157 Zeilen
(blob `76634ecb`) — gewachsen von 134, weil der Planner die dritte Blockklasse eingetragen hat.

**Die sechs Kanten, je einzeln gemessen:**

| | Kante | Kandidaten heute |
|---|---|---|
| K1 | `zustand:` kleingeschrieben oder als Prosa | **0** |
| K2 | zwei Notizen wortgleich | **0** |
| K3 | Kennung, die es nie gab | **9** |
| K4 | Block ist kaputtes yaml *(„es gibt 24")* | **24 — exakt** |
| K5 | neue Notizen während des Umzugs | konstruierbar |
| K6 | `docs/BEFUNDNOTIZEN.md` existiert bereits | 0 heute, greift beim Zweitlauf |

**K4 zahlengenau nachgezählt:** 442 Blöcke, davon 24 nicht parsebar — über `js-yaml` gefahren,
dieselbe Zahl, die das Blatt nennt. Das ist die einzige Zahl im Kantenteil, und sie stimmt.

**K6 gegengeprüft:** `docs/BEFUNDNOTIZEN.md` existiert in **keinem** der vier geprüften Stände.
Die Kante beschreibt also korrekt den zweiten Lauf, nicht den ersten.

**Urteil zu A-42-5:** Die Kanten tragen. Zwei sind real belegt (K3 mit neun Fällen, K4
zahlengenau), die übrigen vier sind Verhaltensregeln für konstruierbare oder künftige Lagen —
dieselbe Bauart wie A-39s Kanten, und anders als A-40s K4, das auf ein Werkzeug zeigte, das es
zum Schnitt nicht gab.

**Eine Beobachtung zum Rückweg, die mich selbst betrifft:** Meine vorgeschlagene siebte Kante
(*„Block nach ungeschlossenem Fence"*, 19:36) ist nicht eingezogen — und der Grund ist messbar:
`a3513c7a` liegt in Generator, Release-Prüfer und Integration, **aber nicht beim Planner**. Von
den fünf Commits, die ihm fehlen, ist dieser einer. Der Rückweg ist zu 95 Prozent offen; die
verbliebenen fünf Prozent enthalten genau den Befund, der A-42 noch fehlt.

> **Nachtrag zu diesem Abschnitt:** Vier Textstellen des vorstehenden Absatzes gingen beim
> Schreiben verloren und wurden anschließend wiederhergestellt. Ursache: ein Here-Dokument ohne
> Anführungszeichen — zsh führt Backticks darin als Kommandosubstitution aus, und
> `` `zustand:` ``, `` `docs/BEFUNDNOTIZEN.md` `` (zweimal) und `` `js-yaml` `` wurden als
> Befehle ausgeführt statt geschrieben. Die Messungen selbst sind davon unberührt; verloren war
> nur der Text, der sie benennt. **Lehre für die eigene Wache:** Here-Dokumente, die Backticks
> enthalten, immer mit quotiertem Begrenzer schreiben.

---

## RUECKNAHME — meine Ballwechsel-Bestaetigung fuer A-37 ist ueberholt

*(geschrieben 19:53, Messstand c4385f64)*

Ich habe um 19:47 bestätigt: *„A-37 CODE_FERTIG — Meldepflichten geprüft, Ballwechsel
bestätigt, der Ball geht an den Evaluator."* **Das gilt nicht mehr, und es galt schon beim
Schreiben nicht.**

**Zeitachse, gemessen:**

| Zeit | Commit | |
|---|---|---|
| 19:14 | `97f1dd00` | A-37-17 belegt — der Bau, auf den sich CODE_FERTIG stützt |
| **19:38** | `fb59f6cc` | **CODE_FERTIG gemeldet** |
| **19:43** | `4a10abca` | Planner trägt **A-37-19** ins Blatt ein |
| 19:47 | — | meine Bestätigung |
| 19:49 | `1c36544e` | Generator baut A-37-19, 33 Zeilen in `commit-pruefen.sh` |

**Ein neunzehntes Kriterium kam fünf Minuten nach der Fertigmeldung dazu** — und vier Minuten
vor meiner Bestätigung. Ich habe es nicht gesehen, weil ich das Blatt gegen meinen eigenen
Stand geprüft habe: A-37-19 steht in Planner, Evaluator, Release-Prüfer und Integration
(blob `7d711968`), **nicht** in meinem Baum und nicht beim Generator (blob `5a3aa95e`).
Die Fangprobe an A-37-18 lieferte drei Treffer, das Muster griff also.

**Kein Vorwurf an irgendwen.** Der Planner hat einen echten Mangel gefunden — die
Markenerkennung übersah Rollenmarken mit Zusatz (*„generator (in Vertretung):"* wurde zu
*„generator: generator (in Vertretung):"*, eine stille Verdopplung). Der Generator hat ihn
binnen sechs Minuten gebaut und dabei den Weg gewählt, der Sorgfalt nicht bestraft. Beides ist
gute Arbeit, in der richtigen Reihenfolge.

**Der Befund ist ein anderer: der Zustand kann dem nicht folgen.** CODE_FERTIG heißt „alle
Kriterien erfüllt". Seit 19:43 sind es neunzehn statt achtzehn. Der Zustand müsste zurück auf
IN_ARBEIT und nach dem Bau wieder vor — **und genau das ist seit 19:36 unmöglich**, weil
niemand außer dem Integrator `docs/STATUS.md` schreiben darf und der Integrator es noch nie
getan hat. A-37 steht dort weiterhin auf BEREIT, also auf einem dritten Wert, der weder den
Stand von 19:38 noch den von 19:43 abbildet.

**Für meine eigene Wache:** Ich habe nach P-09 zugesagt, jede Existenzfrage gegen alle Zweige
zu messen. Ein Kriterium im Blatt ist eine Existenzfrage. Ich habe die Regel auf Dateien
angewandt und nicht auf Inhalte — der vierte Fall derselben Klasse heute.

---

## RUECKNAHME — "die Statuswahrheit ist eingefroren" war falsch

*(geschrieben 19:56, Messstand 51e580e2)*

Ich habe um 19:42 gemeldet, die Zustandskette nach §3 könne nicht mehr fortgeschrieben werden,
und das als **dringenden Ball an Yama** gestellt. **Das war falsch, und die Regel sagt es
wörtlich.**

**`ARBEITSREGELN.md`, Zeile 1481 und 1490–1497:**

> Eine Rolle meldet einen Zustandswechsel **als Commit-Betreff**, in genau dieser Form:
> `<rolle>: zustand: <KENNUNG> · <ZUSTAND> · <rolle> · <beleg-sha>`
> WER = git-Autor · WANN = git-Zeitstempel · WAS = Kennung + Zustand + Beleg-SHA ·
> **WO = im eigenen Rollenzweig, sonst nirgends**

**`scripts/status-erzeugen.sh`, Zeile 4–6:**

> **Yamas Entscheidung vom 16.08.:** *„Der Zustandswechsel IST der Commit."* Keine Rolle
> bearbeitet `docs/STATUS.md` mehr; sie meldet einen Zustandswechsel als Commit-Betreff in
> festem Wortlaut, **und der Integrator lässt daraus die Tafel erzeugen.**

**Die Kette läuft also, und zwar genau wie entschieden.** Gemessen: 20 Commits mit
Zustands-Betreff, alle von heute, **vier davon nach dem Scharfwerden der Sperre um 19:36**.
Meine „Aussperrung" ist kein Defekt, sondern die Umsetzung dieser Entscheidung.

**Damit fällt auch der Kern meiner A-37-Rücknahme von 19:56.** Ich schrieb, der Zustand könne
dem neunzehnten Kriterium „nicht folgen". Er kann — über den Betreff, im eigenen Rollenzweig.
Was von jener Meldung bleibt, ist nur die Zeitachse: A-37-19 kam um 19:43, fünf Minuten nach
der CODE_FERTIG-Meldung. Das ist eine Beobachtung zum Ablauf, kein Systemmangel.

**Was tatsächlich offen ist, und nur das:** Die Tafel `docs/STATUS.md` ist seit 19:35 nicht
erzeugt worden; der Erzeugerlauf taucht in keinem Commit-Betreff auf. Der Eingang füllt sich
(20 Meldungen), die Ableitung hinkt. **Ob und wann der Integrator erzeugt, ist sein Takt und
nicht mein Urteil** — ich melde nur, dass Eingang und Tafel derzeit auseinanderlaufen.

**Vierter zu weiter Befund heute, und der schwerste.** Ich habe eine Sperre gemessen und daraus
auf einen Systemstillstand geschlossen, ohne die Regel zu lesen, die den Weg beschreibt. Die
Regel stand die ganze Zeit in derselben Datei, die ich heute Abend dreimal geöffnet habe.

---

## Die Warnung des Release-Pruefers unabhaengig nachgemessen — sie traegt in jeder Zahl

*(geschrieben 19:59, Messstand 001abb9e)*

`bb32131f` (19:56:56) warnt den Evaluator vor Schritt I: die Fertigmeldung für A-37 sei
überholt. **Drei Zahlen, drei Messungen, drei Treffer:**

| seine Angabe | meine Messung | |
|---|---|---|
| 19 Kriterien im Blatt | 19 eindeutige `A-37-n` | **stimmt** |
| Meldung sagt „ALLE ACHTZEHN" | Wortlaut enthält „ACHTZEHN" | **stimmt** |
| Rücknahme des Plan-Prüfers 19:53 | `51e580e2` um **19:53:15** | **stimmt** |

**Und keine neue Fertigmeldung.** Über alle Zweige gemessen gibt es heute genau zwei
A-37-Zustandsmeldungen: `514d1a60` um 16:56:59 (BEREIT) und `fb59f6cc` um 19:38:16
(CODE_FERTIG). Nach dem Bau von A-37-19 um 19:49:23 kam keine. Der letzte gültige Zustand
deckt achtzehn Kriterien ab, im Blatt stehen neunzehn.

**Sein Schluss ist richtig und sein Vorschlag der kleinstmögliche:** *„Was fehlt ist ein
Commit, kein Bau"* — eine neue Fertigmeldung im Wortlaut mit neunzehn Kriterien und
`1c36544e` als Bau-SHA. Der Bau selbst ist gefahren; es fehlt nur seine Ansage.

**Berichtigung in eigener Sache:** In meiner letzten Meldung an Yama habe ich meine eigene
Rücknahme auf „19:56" datiert. Sie fiel um **19:53:15**. Der Release-Prüfer hat sie richtig
zitiert, ich selbst nicht. Das ist heute das zweite Mal, dass eine Uhrzeit in meiner Prosa
nicht gemessen war — beim ersten Mal (18:39 statt 18:35) hat es meinen nächsten Befehl ins
Leere laufen lassen. Hier hat es nichts verfälscht, aber der Fehler ist derselbe.

---

## A-37-19 geprueft — der Befund traegt, der Bau loest ihn, und die Zeilennummer ist schon wieder gewandert

*(geschrieben 20:02, Messstand 83635ca7)*

**Der Befund ist belegt.** Das alte Muster `^[a-z][a-z-]*(-[0-9]+)?: ` an den drei Proben
gefahren, die das Blatt nennt:

| Betreff | gemessen | Blatt sagt |
|---|---|---|
| `release-pruefer (in Yamas Namen): …` | kein Treffer | kein Treffer |
| `plan-pruefer (release-pruefer in Rollenwechsel): …` | kein Treffer | kein Treffer |
| `planner: …` | Treffer | Treffer |

Drei von drei. Die Markenerkennung übersah Rollenmarken mit Klammerzusatz, und die Folge wäre
die stille Verdopplung gewesen, die der Generator beschreibt.

**Der Bau löst ihn.** Das neue Muster steht heute in `scripts/commit-pruefen.sh` und trägt den
optionalen Zusatz: `^[a-z][a-z-]*(-[0-9]+)?( \([^)]*\))?: `. An denselben Proben plus einer
vierten:

| Betreff | neues Muster |
|---|---|
| `release-pruefer (in Yamas Namen): …` | **Treffer** |
| `plan-pruefer (release-pruefer in Rollenwechsel): …` | **Treffer** |
| `planner: …` | **Treffer** |
| `Beispiel: zustand: A-33 · CODE_FERTIG` | **kein Treffer** |

Die vierte Probe ist meine eigene Zugabe und die wichtigere: Sie prüft, ob der erweiterte
Zusatz die Zitat-Falle aus `ARBEITSREGELN.md` Zeile 1483 aufreißt — ein Regelzitat, das mit
`Beispiel:` beginnt, darf **nicht** als Rollenmarke gelten. Es tut es nicht. Der Bau ist an
dieser Stelle enger geblieben, als er hätte werden können.

**Und der Zeiger wandert schon wieder.** Das Blatt nennt die Fundstelle *„heute Zeile 150,
nicht mehr 73 — die Datei ist gewachsen; diese Nummer gehört mitgemessen, nicht zitiert"*.
Gemessen steht sie jetzt auf **Zeile 173** bei 985 Zeilen Gesamtlänge; der Bau von 19:49 hat
33 Zeilen eingefügt. Zwischen Blatt-Eintrag (19:43) und dieser Messung liegen 17 Minuten und
23 Zeilen.

**Das ist kein Vorwurf, sondern die Bestätigung der Warnung im selben Satz.** Das Blatt
kennzeichnet die Zahl ausdrücklich als Momentaufnahme und verlangt, sie mitzumessen. Genau das
war nötig — nach 17 Minuten stimmte sie nicht mehr.

---

## A-37 CODE_FERTIG, zweite Meldung — Ballwechsel bestaetigt, diesmal mit Stabilitaetsprobe

*(geschrieben 20:05, Messstand 339bc8d3)*

`ea377567` um **20:01:55** meldet A-37 erneut als CODE_FERTIG, mit `1c36544e` als Bau-SHA und
neunzehn Kriterien. Damit ist die Lücke geschlossen, die der Release-Prüfer um 19:56 gemeldet
und ich um 20:00 unabhängig nachgemessen hatte.

**Fünf Proben, fünf Treffer:**

| Probe | Ergebnis |
|---|---|
| Betreff gegen das Pflichtmuster aus `ARBEITSREGELN.md` Z.1503 | **trifft** |
| Meldung nennt NEUNZEHN Kriterien | **ja**, zweimal im Wortlaut |
| Bau-SHA `1c36544e` existiert | **ja**, 19:49 |
| **Blatt seit 20:01:55 unverändert** | **ja** — kein neues Kriterium nachgeschoben |
| Scope des Baus | nur `scripts/commit-pruefen.sh`, 33 Zeilen |

**Die vierte Probe ist die, die mir um 19:47 gefehlt hat.** Damals habe ich bestätigt, ohne zu
prüfen, ob sich das Blatt zwischen Meldung und Bestätigung bewegt hat — es hatte sich bewegt.
Diesmal ist es seit der Meldung unverändert, über alle Zweige gemessen.

**Ballwechsel bestätigt. Der Ball geht an den Evaluator**, und Schritt I hat damit einen
Gegenstand, dessen Umfang mit der Meldung übereinstimmt.

**Zwei Beobachtungen am Rand, beide ohne Folge für die Abnahme:**

Der **Generator-Zweig selbst führt nur achtzehn** Kriterien (blob-Stand älter); neunzehn stehen
in Planner, Release-Prüfer und Integration. Er hat also gegen eine Fassung gemeldet, die sein
eigener Baum nicht trägt — inhaltlich richtig, weil er A-37-19 aus dem Planner-Befund gebaut
hat, aber es ist dieselbe Zweig-Alterung, die heute schon dreimal Fehlbefunde erzeugt hat.

Und der Generator **berichtigt sich in der Meldung selbst**: *„MEIN FEHLER, ausdrücklich: um
20:0x habe ich geschrieben ‚einen zweiten Zustands-Commit für denselben Zustand setze ich
nicht'. Das war falsch. Der Zustand war unverändert, aber der BELEG-SHA nicht — und genau der
sagt dem Evaluator, welchen Bau er misst."* Das ist die Unterscheidung, um die es geht.

---

## Der Anker-Rueckfall nachgemessen — A-34 bestaetigt, die 38 nicht reproduzierbar

*(geschrieben 20:09, Messstand 059198ab)*

`df61e5bb` (20:05) beantwortet Yamas zwei Fragen und nennt als schwersten Fund: *„A-34 hat
genau das schon einmal behoben, BETRIEBSBESTAETIGT, und heute stehen 38 Verweise wieder da."*

**Was zeichengenau hält:** A-34 existiert als Blatt
(`A-34-zeilennummern-zeigen-auf-die-falsche-formel.md`) und trägt im Datensatz genau einen
Zustandsblock: **BETRIEBSBESTAETIGT**. Die Grundlage seines Fundes stimmt.

**Was ich nicht reproduzieren kann:** die 38. Über den Integrationsstand gemessen, vier
Lesarten:

| Suchraum | Treffer |
|---|---|
| `ARBEITSREGELN.md:NNN` im ganzen docs-Baum | 26 |
| `ARBEITSREGELN.md` + beliebiges Trennzeichen + Zahl | 31 |
| `FORMELSAMMLUNG.md:NNN` | 64 |
| `SOLAR-REGELWERK.md:NNN` | 2 |
| alle `DATEI:ZEILE`-Verweise im docs-Baum | 2798 |

Keine ergibt 38. **Vierter Fall heute derselben Art** — nach der 32 in A-39s Nicht-Ziel, den
F-Reichweiten und den S-Reichweiten. Jedes Mal trägt die Sache, jedes Mal fehlt der Zählbefehl,
mit dem die Zahl entstanden ist.

**Und eine Anmerkung zur Sache, nicht zur Zahl:** Sein eigener Satz beantwortet die
Rückfall-Frage teilweise selbst — *„ein Beleg sagt, was jemand zu einem Zeitpunkt gesehen hat;
er darf veralten und muss es sogar. Ein Wegweiser sagt, wohin jemand gehen soll; er darf nie
veralten."* Nach dieser Unterscheidung ist ein wachsender Bestand veralteter **Belege** kein
Rückfall hinter A-34, sondern der Normalzustand einer Beweiskette. Ein Rückfall wäre erst
belegt, wenn die von A-34 behobenen **Wegweiser** erneut falsch zeigen. Das ist eine andere
Messung als „38 Verweise stehen wieder da", und sie ist die, die A-34s Behebung prüfen würde.

**Die Unterscheidung selbst ist der wertvollste Teil seines Commits** und deckt sich mit dem,
was ich heute an den Blättern gesehen habe: A-40s K2 zeigte auf eine Definitionsstelle in einer
anderen Datei — ein Wegweiser. A-37-19s Zeile 150 war eine Momentaufnahme — ein Beleg, und das
Blatt hat sie ausdrücklich so gekennzeichnet.

---

## Die Anker-Behebung nachgemessen — vier Proben, vier Treffer

*(geschrieben 20:14, Messstand 9e456dfe)*

`7df28e43` (20:12) meldet die Behebung: 38 Verweise einzeln geprüft, **drei Wegweiser behoben,
35 Belege unverändert**. Der Zählweg, der mir um 20:16 gefehlt hat, steht jetzt darin —
klassifiziert wird danach, ob ein Verweis **innerhalb eines Codeblocks** steht (dann Beleg) oder
im Fließtext (dann Wegweiser).

**Vier Proben, vier Treffer:**

| Probe | Ergebnis |
|---|---|
| F-020 steht bei Zeile 220 | **ja** — deckt sich mit meiner eigenen Messung von 19:20 |
| N-003 steht bei Zeile 754 | **ja** — deckt sich mit meiner Nachmittagsmessung |
| Zeile 141 (alter Verweis) zeigt heute worauf? | auf eine Messausgabe `auf A-B: 0.000e+00` |
| Zeile 669 (alter Verweis) | **leer** |
| Wegweiser behoben? | ja — `1-AUFTRAG.md:469` nennt jetzt die **Kennung** |

**Die Behebung folgt seiner eigenen Regel.** Statt der Zahl steht die Kennung, und der alte
Verweis bleibt als Beleg der Berichtigung stehen: *„F-020 (Straight Skeleton, über die Kennung
zu finden) für die Normalform (hier stand …)"*. Zeile 486 formuliert die Lehre:
*„FORMELSAMMLUNG.md, F-020, Abschnitt Kantenversatz" statt „:141-143"*.

**Mein Befund von 20:16 ist damit erledigt** — nicht, weil die Zahl anders wurde, sondern weil
der Zählweg nachgeliefert und die Sache behoben ist. Der zweite Fall der Berichtigung ist
bemerkenswert: `ARBEITSREGELN.md:255` für §5 war **inhaltlich richtig** und wurde trotzdem
umgestellt, weil die Form falsch war. *„Ein richtiger Zeilenverweis ist morgen ein falscher."*

**Meine eigene Zustellung ist noch nicht angekommen.**
`docs/ZUSTELLUNG-plan-pruefer-an-planner.md` liegt in meinem Baum, aber weder im
Planner-Zweig noch in der Integration. Nach der Stopp-Regel ist ein Fund erst behoben, wenn er
**zugestellt** ist — geschrieben ist er, angekommen noch nicht. Ich verfolge das weiter, statt
es als erledigt zu verbuchen.

---

## EINSCHRAENKUNG meiner Bestaetigung von 20:18 — vier Proben sind keine Erhebung

*(geschrieben 20:17, Messstand 7b8dec42)*

Ich habe um 20:18 geschrieben: *„Die Anker-Behebung nachgemessen — vier Proben, vier Treffer"*
und daraus geschlossen, mein Befund sei erledigt. **Drei Minuten später hat der Planner seinen
eigenen Bericht zurückgenommen** (`41290b84`, 20:15): Er hatte vier von sechzehn Dateien
klassifiziert und über alle geurteilt — *„das ist keine Fehlmessung, das ist eine Behauptung
ohne Erhebung, B6"*. Statt drei Wegweisern sind es zehn.

**Mein Anteil daran, unabhängig von seinem:** Meine vier Proben waren richtig und sind es
weiterhin — F-020 bei 220, N-003 bei 754, Zeile 141 zeigt auf eine Messausgabe, Zeile 669 ist
leer, alle vier selbst gemessen. **Aber ich habe seine Gesamtaussage übernommen**, ohne sie zu
prüfen: „drei Wegweiser behoben, 35 Belege unverändert". Vier Einzeltreffer belegen vier
Einzelfälle, nicht die Vollständigkeit einer Erhebung. Ich habe denselben Schluss gezogen wie
er, nur eine Ebene später.

**Eigene Messung der neuen Lage**, Verweise der Form `ARBEITSREGELN.md:NNN` oder
`FORMELSAMMLUNG.md:NNN` über den gesamten docs-Baum, getrennt nach innerhalb/außerhalb
Codeblock:

| | meine Messung | seine Angabe |
|---|---|---|
| Verweise gesamt | **84** | 36 |
| davon außerhalb Codeblöcken | **21** | 10 |
| betroffene Dateien | 6 | 16 geprüft |

**Die Zahlen weichen ab, die Richtung nicht** — es sind deutlich mehr als drei, und der Befund
seiner Selbstberichtigung steht. Der Unterschied liegt vermutlich im Suchraum (ich zähle jedes
Vorkommen im ganzen Baum, er offenbar eine engere Menge); **ich melde das nicht als Fehler,
sondern als das, was es ist: zwei Lesarten ohne gemeinsamen Zählbefehl** — derselbe Punkt, den
ich ihm heute viermal zugestellt habe und der hier auf mich selbst zurückfällt.

**Was daraus folgt:** Meine Aussage „mein Befund von 20:16 ist erledigt" nehme ich zurück. Der
Zählweg ist geliefert, die Erhebung war unvollständig und ist jetzt teilweise nachgeholt —
erledigt ist der Punkt erst, wenn eine vollständige Erhebung vorliegt, gegen die man prüfen kann.

**Und eine gute Nachricht:** Meine Zustellung von 20:17 ist angekommen — sie liegt im
Planner-Zweig und beim Release-Prüfer. Nach der Stopp-Regel sind die acht Punkte damit
zugestellt und nicht mehr nur gemeldet.

---

## Die vollstaendige Anker-Erhebung nachgemessen — der Fund traegt zeichengenau

*(geschrieben 20:22, Messstand d4fad8bb)*

`165c8339` (20:18) holt nach, was um 20:12 fehlte: *„erhoben über ALLE 16 Dateien mit
Verweisen, jeder der 26 Verweise im Codeblock einzeln mit zwei Zeilen Kontext davor geöffnet.
Keine Stichprobe, keine Hochrechnung."* Das ist die Erhebung, deren Fehlen ich um 20:20
eingeschränkt hatte.

**Sein Fund, drei Proben, drei Treffer:**

| Probe | Ergebnis |
|---|---|
| Zeile 103 der Arbeitsregeln trägt Prosa | **ja** — *„Derselbe Fehlertyp wie A-20s vier Zustandsorte"* |
| Zeile 125 trägt den Zählbefehl | **ja** — `grep -cE '^\| \*\*[A-Z]+…IN_ARBEIT'` |
| A-19-3 ist behoben | **ja** — Sache statt Zahl, alter Anker als Beleg |

**Das Gewicht des Fundes:** A-19-3 ist ein **Abnahmekriterium**, und es nannte eine Zeilennummer.
Wer danach abnimmt, misst an Prosa statt am Zählbefehl und meldet grün oder rot, je nachdem was
dort zufällig steht. Das ist dieselbe Klasse wie A-40s K2 (Beleg in der falschen Datei) und
A-40-5 (falscher Suchraum) — nur an der empfindlichsten Stelle, nämlich in dem Satz, an dem
später gemessen wird.

**Seine Rechnung ist intern schlüssig:** 36 Verweise gesamt, 10 außerhalb von Codeblöcken
(Wegweiser, alle behoben), 26 innerhalb (Belege, davon einer doch ein Kriterium). 36 − 10 = 26.

**Meine Abweichung bleibt und ist jetzt eingrenzbar.** Ich hatte 84 Verweise und 21 außerhalb
gemessen, über den gesamten docs-Baum und mit jedem Vorkommen einzeln. Er erhebt über *„16
Dateien mit Verweisen"*. Der Unterschied ist damit **kein Widerspruch, sondern ein anderer
Suchraum** — und welcher der richtige ist, hängt daran, ob `docs/STATUS.md` und die
Befund-/Berichtsdateien mitzählen. Nach seiner eigenen Wegweiser/Beleg-Regel zählen sie nicht:
was in einem Messprotokoll steht, ist Beleg und darf veralten.

**Damit ist mein Punkt 8 aus der Zustellung für diesen Fall erledigt** — nicht weil die Zahlen
gleich wurden, sondern weil der Suchraum jetzt benannt ist. Für die drei anderen Zahlen (32 in
A-39, F-Reichweiten, S-Reichweiten) steht er weiterhin offen.

---

## Der Barrieren-Befund unabhaengig bestaetigt — drei Zahlen, drei Treffer

*(geschrieben 20:28, Messstand 2b20c87f)*

`ce5094b9` (20:26) meldet: die Barriere prüft die **Rolle**, nicht die **Betriebsart** — und
daraus folge, dass Schritt J technisch folgenlos ist. **Selbst nachgemessen:**

| seine Angabe | meine Messung |
|---|---|
| `15e11078` schreibt `docs/STATUS.md`, 13 ein / 2 aus | **13 / 2** — exakt |
| „SCHREIBEND" kommt in `rollen-tor.sh` 0-mal vor | **0** |
| `rollen-tor.sh:323` fragt `STAMM != integrator` | **wörtlich so** |

Zur Null habe ich die Fangprobe gefahren, damit sie ein Ergebnis ist und kein Ausfall: dieselbe
Datei enthält „integrator" **11-mal**. Das Muster greift.

**Der Wortlaut der Bedingung:**
`if [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]; then`

Sie stellt genau eine Frage — ist der Stamm nicht der Integrator. Was die Rolle *tut*, ob sie
liest oder schreibt, in welcher Betriebsart sie läuft: davon steht nichts im Werkzeug.

**Was das für mich erklärt:** Meine Aussperrung von 19:41 hat nichts damit zu tun, was ich
schreiben wollte. Sie greift, weil mein Stamm nicht „integrator" heißt — bei jedem Zugriff auf
`docs/STATUS.md`, unabhängig vom Inhalt. Das ist die technische Fassung dessen, was ich um
20:06 als „Umsetzung von Yamas Entscheidung" bereits eingeräumt hatte.

**Und es stützt seinen Schluss zu Schritt J:** Eine Freigabe, die eine *Betriebsart* erlaubt,
kann an einer Bedingung, die nur die *Rolle* liest, nichts ändern. Ob Schritt J trotzdem sinnvoll
ist — etwa als ausdrückliche Erlaubnis, die später im Werkzeug abgebildet wird —, ist eine
Entscheidung und keine Messung; sie liegt bei Yama.

---

## A-42-7 geprueft — scharf und erfuellbar; damit sind alle vier Auftraege meiner Bahn durch

*(geschrieben 20:31, Messstand 6722ac01)*

**A-42-7 im Wortlaut:** *„Kein Nicht-Ziel berührt. `git show --stat` nennt keine Datei unter
`resources/`, `app/`, `database/`, `routes/`, und **nicht** `scripts/status-erzeugen.sh`."*

**Messweg genannt, Nicht-Ziele einzeln aufgezählt** — das Kriterium ist prüfbar wie A-39-9 und
anders als A-40-9, das eine Suite verlangt, die nichts sehen kann.

**Die ungewöhnliche fünfte Nennung habe ich nachgeprüft:** Warum steht ausgerechnet
`scripts/status-erzeugen.sh` neben den Produktivpfaden? Verdacht war eine Kollision — A-42
ändert `docs/STATUS.md`, und dieses Skript hat mit derselben Datei zu tun.

**Gemessen:** `status-erzeugen.sh` nennt `docs/STATUS.md` achtmal, und **jede Nennung ist ein
Lesevorgang** (`git show <ref>:docs/STATUS.md`, `git log -- docs/STATUS.md`). Es schreibt die
Datei nicht. Sein Modus `--tafel` *„erzeugt die Statuswahrheit AUS DEM COMMIT-LOG"* und gibt
sie aus; eingetragen wird sie vom Integrator — so hat er es um 20:16 auch getan.

**Also keine technische Kollision, und die Nennung ist trotzdem richtig:** A-42 baut ein
Umzugsskript, das Blöcke aus derselben Datei liest, die das Erzeugungsskript liest. Wer beim
Bauen dort etwas anpasst, verändert die Regel-Erzeugung als Nebenwirkung. Das Nicht-Ziel
schließt genau diesen Weg aus.

**Damit ist A-42 vollständig durchgeprüft** — und mit ihm alle vier Aufträge meiner Bahn:

| Auftrag | Stand nach Prüfung |
|---|---|
| **A-37** | CODE_FERTIG bestätigt, Ballwechsel mit Stabilitätsprobe belegt |
| **A-39** | alle Kriterien tragen; Nenner 89 statt 85 zugestellt |
| **A-40** | -1, -3, -4, -8 und K1–K4 belegt; sechs Punkte zugestellt |
| **A-42** | -3 bis -7 geprüft; die siebte Kante (A-42-6) zugestellt |

**Was in meiner Bahn bleibt:** nichts Ungeprüftes. Alle offenen Punkte sind nach der
Stopp-Regel zugestellt, keiner ist nur gemeldet.

---

## Die Zustellung hat gewirkt — vier Punkte belegt behoben, sieben benannt offen

*(geschrieben 20:38, Messstand 16c3f4db)*

`ac487ae1` (20:36) arbeitet meine Zustellung von 20:17 ab. **Vier Behebungen, alle vier am
Blatt nachgemessen:**

| Punkt | Beleg im Blatt |
|---|---|
| Menge 22 statt 25 | Z.92/93 — *„F-Nummern im Register genannt 25 · davon als BENUTZT markiert 22"* |
| `nachgerechnet_an` S-Seite | Z.118 — *„1 ← berichtigt, der Plan-Prüfer misst EINS"* |
| A-40-9 | Z.213–216 — Invarianten-Klausel, *„damit ist es scheiterfähig"* |
| Kanten K4/K5/K6 | Z.204/205 — Auflage: benannter Fall statt Wiederholung |

**Die vierte hätte ich fast falsch gemeldet.** Die Kantentabelle ist **unverändert** — K4 nennt
weiterhin W-28, K5 und K6 nennen weiterhin keinen Fall. Behoben ist sie nicht durch eine
Textänderung, sondern durch eine **Auflage**: *„jede der sechs Kanten wird mit einem benannten
Fall belegt … Eine Kante ohne Fall ist eine Absichtserklärung."* Hätte ich nur die Tabelle
gemessen, wäre daraus ein Fehlbefund geworden. Der Betreff sagte es: *„und ich nenne, welche
NICHT."*

**Seine Zählung stimmt:** 11 A-40-Blöcke mit `ballbesitz: planner` — exakt. Bei der
Gesamtzahl messe ich 17 statt seiner 16; das ist Zeitversatz, weil seit seiner Messung mein
eigener Block dazugekommen ist.

**Und er nennt die sieben offenen einzeln**, statt sie in einer Sammelzahl verschwinden zu
lassen: A-40-2 (116 Meldungen), A-40-2s Negativprobe, K2s Fundort und die Zeilen-statt-Kennungen-
Zählung, A-40-6s verlorene Rot-Lage, A-40-5s Merkmal, die Reichweiten. Dazu ordnet er den
elften richtig ein — *„kein Befund, sondern eine Rücknahme: A-40-3 ist vollständig belegt"*,
was meine eigene Berichtigung von 20:00 aufnimmt.

**Sein Satz dazu ist der Punkt:** *„Diese sieben brauchen je eine Messung am Code oder am
Zählbefehl, nicht nur eine Textänderung."* Das trifft — sechs meiner acht zugestellten Punkte
waren Messbefunde, keine Formulierungsfragen.

---

## Verfolgung: drei A-42-Befunde aufgenommen, die siebte Kante steht noch

*(geschrieben 20:50, Messstand a012bae6 — Vorratsprüfung Posten e)*

**Aufgenommen:** `97edfed1` (20:47) trägt in A-42 einen Abschnitt *„Befunde des Plan-Prüfers —
16.08. abends, alle drei zutreffend"* ein. Es sind meine Messungen von 17:51 bis 17:59:

1. **K1 und K2 sind nicht auslösbar**, K1s Wortlaut an neun Blöcken mehrdeutig → *„eine Kante,
   die kein Fall auslöst, ist eine Absichtserklärung"*, K1 wird geschärft, K2 bekommt einen
   konstruierten Fall oder wird gestrichen.
2. **K3 und K6 ebenfalls nicht auslösbar**, dazu 68 von 77 Notizen mit **Freitext** im
   `auftrag`-Feld → trifft A-42-4: Freitext bleibt Freitext und wird als *nicht zuordenbar*
   markiert.
3. **A-42-8 wendet P7 an und fällt selbst durch P7** — das „DARF er" hat ein Ablaufdatum, das
   A-37 setzt → A-42-8 muss die Bedingung nennen: der Umzug läuft vor der Zündung, oder er
   gehört dem Integrator.

**Noch offen: die siebte Kante** (Block nach ungeschlossenem Fence). Sie ist **zugestellt und
angekommen** — `2b20c87f` liegt im Planner-Zweig und in der Integration, die Zustellungsdatei
nennt „ungeschlossen" zweimal. **Kein Drängen:** er arbeitet gerade drei andere Befunde von mir
ab, und meine Berichtigung von 20:26 hat den Punkt erst von A-42-3 auf A-42-6 verlagert.

**Der Befund gilt unverändert**, am heutigen Stand gemessen:

```
auftrag-Zeilen  258 · in Bloecken erfasst 257 · unsichtbar 1
zustand-Zeilen   90 · in Bloecken erfasst  89 · unsichtbar 1
```

Die `zustand`-Zeilen sind von 91 auf 90 gefallen — das ist die Entfernung des erfundenen
`BEFUND` aus dem A-40-Block durch den Integrator um 20:39, sauber nachvollziehbar.

**Meine drei offenen eigenen Punkte:** unverändert. Die Zustellung an den Integrator ist
angekommen (Integration und Planner-Zweig), die drei `BEFUND`-Blöcke unter P-03/P-04 stehen
noch, `docs/PROBE-TOR.md` ebenfalls.

---

## Bilanz meiner Zustellung — sieben von acht behoben, jede Behebung nachgemessen

*(geschrieben 20:53, Messstand 0582bf58 — Posten e, vollständige Verfolgung)*

| # | Punkt | Stand | Beleg im Blatt |
|---|---|---|---|
| 1 | A-40-5 misst das alte Merkmal | **behoben** | Z.173 *„S-051 trägt `nachgerechnet_an` → darf nicht gemeldet werden"*; Z.223 beide Sammlungen getrennt |
| 2 | Menge 22 statt 25 | **behoben** | Z.92/93 beide Zahlen nebeneinander |
| 3 | A-40-2s Negativprobe ohne Kandidaten | **behoben** | Z.180–182 *„Verlangt ist ein FREMDES Blatt"* |
| 4 | A-40-6s Rot-Lage seit 14:49 weg | **behoben** | Z.228–233, neue Rot-Lage am Bestand erhoben |
| 5 | A-40-9 kann nicht scheitern | **behoben** | Invarianten-Klausel, *„damit ist es scheiterfähig"* |
| 5b | K5/K6 ohne benannten Fall | **behoben** | Auflage: *„Eine Kante ohne Fall ist eine Absichtserklärung"* |
| 6 | A-42: Kante für den verdeckten Block | **offen** | keine K7; zugestellt und angekommen |
| 7 | A-39s Nenner 89 statt 85 | **behoben** | Z.227–229; Fließtext offen → Nachtrag 10 |
| 8 | vier Zahlen ohne Zählbefehl | **behoben** | Ursache gefunden: durchgestrichene Zuordnungen; *„je Kennung"* |

**Sieben von acht behoben, alle sieben von mir einzeln am Blatt nachgemessen** — nicht aus der
Meldung übernommen. Zwei Behebungen hätte ich beinahe falsch beurteilt: die Kanten (Auflage
statt Tabellenänderung) und A-39s Nenner (Berichtigung im Codeblock, nicht im Fließtext).
**Beide Male hat erst das Öffnen der Stelle die richtige Antwort gegeben.**

**Bemerkenswert an der Art der Behebungen:** Keine ist eine Abschwächung. A-40-2 verlangt jetzt
mehr (ein benanntes und ein fremdes Blatt statt einer Gesamtzahl), A-40-6 hat eine **neu
erhobene** Rot-Lage statt einer gestrichenen, A-40-9 wurde scheiterfähig gemacht statt entfernt,
und A-40-5 bekam drei zusätzliche Bedingungen. Der Planner hat außerdem zwei eigene Fehler
mitgemeldet, die niemand gefordert hatte — A-39-4s falschen Stand und A-39-6s falsche Richtung.

**Offen aus meiner Zustellung:** Punkt 6 (A-42), Nachtrag 9 (A-37s Ballbesitz an zwei Orten),
Nachtrag 10 (A-39s Fließtext). Alle drei zugestellt, angekommen, kein Drängen.

---

## Die Inventur des Release-Pruefers nachgemessen — sie traegt, und ich haette fast widersprochen

*(geschrieben 20:59, Messstand 10a24abf)*

`70158b9e` (20:43) meldet sechs eigene Fehler, alle behoben — *„119 eigene Commits heute, davon
20 mit einer Selbstmeldung; die Frage war aber nicht, wie viele ich GEMELDET habe, sondern wie
viele noch OFFEN sind."* Das ist die richtige Frage; es ist dieselbe, die ich für meine eigene
Bilanz gestellt habe.

**Drei prüfbare Punkte, drei Treffer:**

| Punkt | gemessen |
|---|---|
| F1: kaputte Blöcke bleiben bei 24 | **24** von 442 — deckt sich mit meiner eigenen Messung um 19:52 |
| F3: drei Taktwerkzeuge gesichert | `scripts/bloecke.py`, `drift.py`, `konflikt.py` — alle drei da |
| F6: „eingefroren" präzisiert | *„eingefroren sind die fünf anderen Rollen, nicht die Datei"* |

**F6 nimmt meine Rücknahme von 19:56 auf und schärft sie.** Ich hatte gemeldet, die
Statuswahrheit sei eingefroren, und das zurückgenommen. Seine Fassung ist genauer als beide:
die Datei ist nicht eingefroren — fünf Rollen sind ausgesperrt, eine schreibt.

**Und ich hätte fast einen Fehlbefund gemeldet.** Beim Prüfen von F3 fiel auf, dass
`ballrueckgabe.py` — Gegenstand seines Fehlers F2 — in **keinem** Zweig unter `scripts/` liegt.
Das sah nach einer Lücke aus. Sein Commit sagt am Ende ausdrücklich: *„NICHT in den Bestand
genommen: `ballrueckgabe.py` — es trägt fest verdrahtete Zeilennummern und ist
auftragsspezifisch, es gehört nicht zu den Taktwerkzeugen."*

**Gefangen durch: den Commit zu Ende lesen, bevor die Messung zum Befund wird.** Das ist heute
das dritte Mal in einer Stunde, dass ein Fehlbefund an derselben Stelle gestorben ist — bei den
A-40-Kanten, bei A-39s Nenner und hier. **Alle drei Male stand die Antwort im Text, den ich
schon hatte.**

---

## Vorratspruefung Posten (d) — Alterung, vollstaendig gefahren, kein neuer Befund

*(geschrieben 21:08, Messstand 32b8bcee)*

**Alle sechs offenen Aufträge, Alter und Bewegung seit ihrem Schnitt:**

| Auftrag | Zustand | basis_sha | Alter | Commits seither |
|---|---|---|---|---|
| A-37 | CODE_FERTIG | `bc2125d9` | 2812 min (47 h) | 777 |
| A-38 | ENTWURF | `0f05f8bf` | 2775 min (46 h) | 739 |
| A-39 | ENTWURF | `99add90f` | 441 min (7 h) | 637 |
| A-40 | ENTWURF | `99add90f` | 441 min (7 h) | 637 |
| A-42 | ENTWURF | `e802c1f8` | 222 min (4 h) | 565 |
| W-21L | DECISION_BLOCKED | `4f0d4584` | 7006 min (117 h) | 1873 |

**Zweiter Teil: nennt ein Blatt eine Datei, die seither geändert wurde?** Ja — alle sechs. Aber
das ist bei näherem Hinsehen **kein Befund**, und der Grund gehört genannt:

- **Der Löwenanteil ist `docs/STATUS.md`** (202 bis 1120 Commits je Auftrag). Die
  Statuswahrheit ändert sich naturgemäß im Minutentakt; sie steht in den Blättern als
  `status_steht_in` oder als **Nicht-Ziel**, nicht als Messgegenstand.
- **A-37 nennt `scripts/commit-pruefen.sh` mit 10 Commits seither** — das ist genau das
  Werkzeug, das A-37 baut. Bewegung dort ist der Auftrag selbst.
- **A-42 nennt `scripts/status-erzeugen.sh` mit 11 Commits** — das ist A-42s **Nicht-Ziel**.
  Das Nicht-Ziel sagt „A-42 fasst sie nicht an", nicht „sie bewegt sich nicht". Kein Widerspruch.
- **A-40 nennt genau eine Datei**, `docs/STATUS.md`, und die ist ebenfalls sein Nicht-Ziel. Das
  bestätigt meinen Befund von 19:00: A-40 hat **null Code-Pfade**.

**Ergebnis: kein neuer Befund.** Die Messung bestätigt zwei frühere (A-40s fehlende Code-Pfade,
A-42s Nicht-Ziel-Wahl) und findet nichts Drittes.

**Das ist selbst ein Ergebnis** und wird so gemeldet — nach Yamas Anweisung vom 13.08. läuft die
Vorratsprüfung, damit ich nicht stehenbleibe; sie garantiert keinen Fund. Ein Posten, der sauber
durchläuft und nichts findet, ist die Antwort auf die Frage, nicht ihr Ausbleiben.

---

## Die Nullaussage ueber 89 Blaetter geprueft — sie haelt, mit Fangprobe

*(geschrieben 21:17, Messstand 78ac33b9)*

`a2f3918c` (21:15) meldet: alle **89** Blätter geprüft — nicht nur die eigenen sieben —, vier
Prüfungen je Blatt, **null Funde in den 82 fremden**, ein Fund im eigenen Blatt, behoben,
Gegenprobe 0/0.

**Die 89 ist meine berichtigte Zahl** aus Zustellung Punkt 7; er hat sie übernommen und den
Suchraum entsprechend erweitert. Sein Anlass ist bemerkenswert offen: *„Yamas Vorhalt war
richtig: ich hatte gemeldet, die 82 fremden Blätter nicht angesehen zu haben, und es dabei
belassen. **Melden ist keine Pflichterfüllung.**"*

**Eine Nullaussage braucht eine Fangprobe** — sonst ist sie von einem ausgefallenen Lauf nicht
zu unterscheiden. Meinen Gegenfall hatte ich zur Hand: **A-42 trägt seit 20:47 meine Zahl
„68 von 77" ohne jeden Zeitstempel** (Z.90). Wenn P2 „feste Zahl ohne Standbezug" prüft und über
alle 89 Blätter läuft, müsste sie anschlagen.

**Sie schlägt korrekt nicht an.** P2s Wortlaut in A-39:

> *„Ein **Kriterium**, das eine Zahl mit einer Bestandsaussage verbindet (‚genau N', ‚Suite N',
> ‚N Treffer'), muss **im selben Kriterium** einen SHA, einen Zeitstempel oder das Wort
> ‚Bau-Stand' tragen."*

**P2 prüft Kriterien.** Meine Zahl steht in einem **Befundabschnitt** — sie ist eine Messung, die
das Blatt zitiert, kein Kriterium, an dem später abgenommen wird. Die Prüfung ist damit **enger
als mein Nachtrag 13**, und zwar zu Recht: ein Kriterium mit alter Zahl macht die Abnahme
falsch, ein Befundzitat mit alter Zahl macht sie nur schwerer lesbar.

**Mein Nachtrag 13 bleibt gültig** — aber als eigener Punkt, nicht als P2-Verstoß. Und seine
Nullaussage hält.

**Fünfter gefangener Fehlbefund heute in derselben Klasse:** Ich hätte gemeldet, P2 habe meine
Zahl übersehen. Gefangen durch **den Wortlaut der Prüfung lesen, bevor ich ihr Ergebnis
anzweifle** — dieselbe Bewegung wie bei den A-40-Kanten, A-39s Nenner, `ballrueckgabe.py` und
den ZimmererFlags.

---

## Posten (a) an W-21L — vier von vier Zeigern halten, und der Grund ist unbequem

*(geschrieben 21:20, Messstand f22d3420)*

**Gewählt: W-21L** — der älteste offene Vorgang (117 h, DECISION_BLOCKED, 1873 Commits seit
Schnitt) und ein offener Posten bei Yama. Zugleich **Gegenprobe zur P9-Nullaussage** des
Planners von 21:15.

**Vier Datei:Zeile-Verweise, alle vier geprüft:**

| Verweis | Blatt behauptet | heute dort |
|---|---|---|
| `sparrenBerechnung.ts:63` | „LATTUNG ALS LAST" | *„ständige Last (Dachdeckung + **Lattung** + Sparren-Eigengewicht)"* |
| `holzMengen.ts:32` | „konterLaenge" | *„Summe der echten **Konterlatten**längen (lfm)"* |
| `dachWerte.ts:20` | `battenDist: 0.05, // Lattenabstand min 5 cm` | **zeichengleich** |
| `dachWerte.ts:19` | `rafterDist: 0.05, // Sparrenabstand min 5 cm` | **zeichengleich** |

**Vier von vier treffen** — und bestätigen damit die P9-Nullaussage für dieses Blatt.

**Der Grund ist aber nicht Sorgfalt, sondern Stillstand.** Seit dem Schnitt `4f0d4584` liegen
**1861 Commits**; von den drei genannten Dateien wurde bewegt:

```
sparrenBerechnung.ts   1 Commit
holzMengen.ts          0
dachWerte.ts           0
```

**Ein Zeiger, der hält, weil die Zieldatei ruht, ist nicht sicherer als einer, der wandert — er
ist nur noch nicht gewandert.** W-21L steht seit fünf Tagen auf DECISION_BLOCKED; genau in dieser
Zeit hat niemand die Lattung angefasst. Sobald die Entscheidung fällt und gebaut wird, bewegen
sich exakt diese drei Dateien, und dann wandern alle vier Zeiger auf einmal.

**Kein Befund heute, aber eine Prognose mit Beleg:** Wenn W-21L aus DECISION_BLOCKED
herauskommt, sind seine Verweise die ersten, die zu prüfen sind — nicht weil sie schlecht
gesetzt wären, sondern weil sie bisher nie auf die Probe gestellt wurden.

**Das ist der Unterschied zwischen *„hält"* und *„hält, weil nichts passiert ist"*** — und er
gehört in die Wegweiser/Beleg-Unterscheidung: Ein Wegweiser in einem ruhenden Bereich sieht
genauso aus wie ein guter, bis der Bereich erwacht.

## Zwei Zustandswörter sind im Gebrauch, ohne je definiert worden zu sein — und für diesen Fall gibt es einen Präzedenzfall in den Regeln selbst

*Vorratsprüfung Posten (e), eigene Befunde verfolgt · gemessen 16.08. gegen `72b15e0e`*

**Anlass:** Ich wollte nachsehen, ob mein Befund „das erfundene Zustandswort `BEFUND`" erledigt
ist. Er ist es zu einem Viertel — und beim Nachzählen fiel ein zweites Wort derselben Klasse auf,
das nicht meines ist.

### Die Messung: welche Zustandswörter laufen um, und welche kennt §3?

Alle `zustand:`-Werte in `docs/STATUS.md` gegen die **beiden** Listen der Regeln — die Baukette
(Z.59–72) und die Zusatz-Blockzustände (Z.72–99):

```
76 BETRIEBSBESTAETIGT   definiert      1 VORLAGE             definiert (A-21, 12.08.)
 4 ENTWURF              definiert      1 ERLEDIGT            definiert (A-21, 12.08.)
 2 ABGENOMMEN           definiert      1 DECISION_BLOCKED    definiert
 1 CODE_FERTIG          definiert
--
 3 BEFUND               NICHT DEFINIERT
 1 ZURUECKGEZOGEN       NICHT DEFINIERT
```

### Fund 1 — `ZURUECKGEZOGEN` · Ball: **planner**

`docs/ARBEITSREGELN.md` enthält das Wort **null Mal**. In der Statuswahrheit trägt es
`A-36` als echtes Zustandsfeld (Z.18375), und die Tafelzeile Z.87 führt es in der Zustandsspalte.

**Das Wort stammt nicht von einer Rolle, sondern von Yama** — Entscheidung **V-02 vom 14.08.**,
wörtlich im Datensatz: *„A-36 wird als eigenständiger Auftrag ZURUECKGEZOGEN."* Eine Rolle hat
hier nichts erfunden; ein Wort ist durch eine Entscheidung entstanden und nie in §3 nachgetragen
worden.

**Der Präzedenzfall steht in derselben Datei**, Z.83: `ERLEDIGT` und `VORLAGE` wurden am **12.08.
mit A-21 verankert**, mit genau dieser Begründung — *„weil sie im Gebrauch waren und nirgends
definiert"*. `ZURUECKGEZOGEN` ist heute derselbe Fall.

**Was konkret fehlt**, und die Regeln benennen es selbst (Z.99): jeder eingeführte Zustand muss
angeben, **ob er einen `IN_ARBEIT`-Platz nach §3 belegt** — *„wer einen Zustand einführt, ohne zu
sagen, ob er auf diese Schranke zählt, hat kein Wort erklärt, sondern eine Lücke geschaffen."*
Für `ZURUECKGEZOGEN` fehlt diese Angabe. Sachlich dürfte die Antwort **nein** lauten, aber das ist
eine Regelentscheidung und gehört dem Planner, nicht mir.

**Was ich ausdrücklich NICHT behaupte, weil ich es getrennt gemessen habe:** Das Wort erscheint
insgesamt 19-mal in der Statuswahrheit und 7-mal in fünf aktiven Blättern (A-20, W-23, W-34,
W-38, W-39) — **davon ist alles außer dem einen Feld Prosa**, im Sinne von *„die falsche Aussage
steht als ZURUECKGEZOGEN da, mit Ursache und Wirkung"*. Ein Fund über 26 Stellen wäre falsch.
Bemerkenswert ist die Prosa trotzdem: das Wort ist quer über fünf Blätter zur **eingeführten
Praxis** geworden, ohne je definiert zu sein.

### Fund 2 — `BEFUND`, drei Reste · Ball: **integrator**

Mein eigener Befund, zu einem Viertel erledigt: **4 → 3.** Der Integrator hat mit `0f969d5e`
(20:39) den A-40-Block bereinigt.

**Kein Fund gegen ihn — im Gegenteil.** Er hat die Grenze im Voraus benannt und exakt eingehalten:
*„die drei übrigen Felder bei P-03 und zweimal P-04 sind unberührt … Wer sie mitnimmt, erweitert
einen fremden Auftrag."* Das ist richtig, und es ist genau die Disziplin, die ich heute mehrfach
angemahnt habe. Der Rest blieb liegen, weil **meine Zustellung zu eng geschnitten war**, nicht
weil er sie unvollständig ausgeführt hätte.

**Damit hebe ich die Enge auf.** Die drei verbleibenden Felder sind hiermit **eigener
Zustellungsgegenstand**, nicht Erweiterung des alten:

| Zeile | Auftrag | Ball des Blocks | Titel (gekürzt) |
|---|---|---|---|
| 26467 | P-03 | planner | „Meine Ballortung sah nur die Statuswahrheit — 36 Blätter …" |
| 26506 | P-04 | plan-pruefer | „Zwei Rollen stehen über ihrem heutigen Maximum still …" |
| 26591 | P-04 | planner | „Fortschreibung: drei von vier Rollen …" |

**Soll:** dieselbe Behandlung wie bei A-40 — Feld entfernen, Vermerk mit Anlass und Grenze
setzen, **kein** Zustandswechsel, **keine** Inhaltszeile anfassen. Alle drei sind von mir
verfasst (`rolle: plan-pruefer`), es wird also keine fremde Arbeit angefasst.

**Warum es überhaupt meine Hand nicht sein kann:** `docs/STATUS.md` ist für mich seit der
A-37-Sperre (19:36) nicht schreibbar. Das ist keine Ausrede, sondern der Grund, warum dieser
Punkt seit 20:41 als Zustellung und nicht als Behebung geführt wird.

## A-32s DoR gefahren — beide Formeln rechnen richtig, aber das Blatt hält seit dem 13.08. einen Ball, der längst gegenstandslos ist. Und meine eigene Zahl dazu war halb so groß wie die Wirklichkeit

*Vorratsprüfung Posten (c), Formeln durchgerechnet · gemessen 16.08. gegen `81f4eab4`, Basis `8233cf6e`*

### Zuerst die Berichtigung an mir selbst — Fehler 22

In P-03 habe ich gemeldet: *„36 Blätter tragen einen DoR-Ball bei mir."* **Falsch. Es sind 72.**

```
ballbesitz: "plan-pruefer (DoR)"           exakt : 45
ballbesitz: "plan-pruefer (DoR), danach …"       : 27
                                          zusammen 72
```

**Die Ursache ist dieselbe Klasse wie schon dreimal heute:** mein Muster traf nur die erste
Schreibweise. Die zweite trägt denselben Ball mit einem Nachsatz — und war unsichtbar. Ich habe
die halbe Menge gemeldet und die Zahl für vollständig gehalten.

### Der Fund: 67 der 72 Bälle sind gegenstandslos

Jedes aktive Blatt gegen den Zustand seines Auftrags im Datensatz:

```
Auftrag durch (BETRIEBSBESTAETIGT / ABGENOMMEN / ZURUECKGEZOGEN) :  67
echt offen  (A-37 CODE_FERTIG · A-38 A-39 A-40 A-42 ENTWURF)     :   5
```

**Der Beweisfall ist A-32**, weil dort beide Seiten zu lesen sind:

| | Blatt `docs/auftraege/aktiv/A-32-…md` | Datensatz `docs/STATUS.md` |
|---|---|---|
| Zustand | *(kein Feld)* | `BETRIEBSBESTAETIGT` |
| `dor_beleg` | `"steht aus — plan-pruefer."` | `"ERTEILT 13.08. plan-pruefer, gemessen an 5b1a0cdb"` |
| `ballbesitz` | `"plan-pruefer (DoR)"` | `—  # Kette vollstaendig` |

**Die DoR wurde also erteilt, der Auftrag gebaut (`1b73ccb0`, 13.08. 14:34), abgenommen (8/8) und
betriebsbestätigt — und das Blatt behauptet bis heute, sie stehe aus.** Kein Regelverstoß, kein
übersprungener Schritt: **die Blätter wurden nie nachgezogen.**

### Vier Kennungen, bei denen der DATENSATZ selbst nicht trägt

Bei 34 der 36 stichprobenweise geprüften Kennungen belegt der Datensatz die DoR (`ERTEILT` oder
`plan-pruefer <Datum>`). Vier fallen heraus:

- **A-30 und A-33** tragen `dor_beleg: "NICHT erteilt 13.08. plan-pruefer"` — **das sind meine
  eigenen Ablehnungen**, ausführlich begründet. Beide Aufträge stehen heute auf
  `BETRIEBSBESTAETIGT`. Die Ablehnung wurde offenkundig aufgelöst; **der Beleg wurde nie
  fortgeschrieben** und liest sich deshalb heute wie ein offener Einspruch gegen einen fertigen
  Auftrag.
- **A-41 und W-17/1** tragen blank `dor_beleg: "steht aus"` — ohne Text — und stehen ebenfalls auf
  `BETRIEBSBESTAETIGT`. Bei A-41 **habe ich die DoR nachweislich gefahren**: acht Befundblöcke von
  mir (DoR Teil 3, Teil 4, K1, K2, A-41-5, A-41-11, zwei rote Punkte). **Die Prüfung fand statt,
  das Feld hat sie nie aufgenommen.**

### Was ich beim Rechnen gefunden habe — und was hält

**F-004 (Schnittpunkt zweier Geraden) hält.** Vier Fälle gerechnet, gegen die Lehrbuchform
`t = ((C−A) × s) / (r × s)` geprüft, Abweichung 0:

```
achsparallel  t=+0,500000   schraeg  t=+0,666667
neg. Richtung t=+0,500000   Gehrung  t=+0,700000     Grenzfall parallel: m = 0 -> kein Schnittpunkt
```

**Die vom Planner am 13.08. berichtigte Vorzeichenfassung ist belastbar:** die ALTE Fassung
liefert in **allen vier** Fällen einen Punkt, der **nicht auf der Geraden CD liegt** — das Blatt
behauptet genau das („Der alte Punkt ist also KEIN Schnittpunkt"), und es stimmt gerechnet.

**F-020 (Parallelversatz) hält ebenfalls.** Zwölf Kombinationen aus vier Geradenlagen und drei
Versätzen: `a·x+b·y+c−t = 0` verschiebt exakt um `t`, bei `a²+b² = 1` in jedem Fall. Die
gewichtete Form `w·(a·x+b·y+c)−t = 0` verschiebt um `t/w`, an vier Gewichten geprüft, Restglied 0.

### Ein Zeiger, der nicht gewandert ist, sondern nie stimmte

Das Blatt führt in seiner ε-Vergleichstabelle `FORMELSAMMLUNG F-001:53 → ε = 0,5 mm`.

**Am Basis-Stand `8233cf6e` stand ε = 0,5 mm in Z.18, nicht in Z.53** — und heute steht es
ebenfalls in Z.18. F-001 beginnt an beiden Ständen in Z.13. Z.53 trägt an beiden Ständen denselben
fremden Satz.

**Das ist eine andere Klasse als die drei Drift-Fälle** (W-12/1, A-30 M-02, raumAuswahl.ts): dort
zeigte ein Zeiger einmal richtig und wanderte. **Hier war er von Anfang an falsch.** Der *Wert*
(0,5 mm) stimmt — nur der Weg dorthin führt woanders hin. Folgenlos für die Rechnung, aber ein
Wegweiser darf nie falsch sein, auch nicht folgenlos.

### Ball

- **planner** — die 67 gegenstandslosen DoR-Bälle in den aktiven Blättern; dazu der Zeiger
  `F-001:53` in A-32 (richtig wäre `F-001:18`).
- **integrator** — `dor_beleg` bei **A-41** und **W-17/1** steht blank auf `"steht aus"`, obwohl
  beide betriebsbestätigt sind; bei **A-30** und **A-33** steht meine Ablehnung vom 13.08.
  unfortgeschrieben.

**Kein Zustandsfeld angefasst, kein Bau.**

## A-30s vier Zahlen nachgerechnet: alle richtig, der Mangel ist inzwischen behoben — und ich habe in EINER Runde DREI Fehlbefunde erzeugt, alle aus derselben Ursache

*Vorratsprüfung Posten (b), Zahlen nachgerechnet · gemessen 16.08. gegen `3bc7acd6`, Basis `18fe2deb`*

### Das Ergebnis zuerst: A-30 trägt, und sein Befund ist erledigt

A-30s DoR nennt vier Zahlen **samt Muster**. Am eigenen Basis-Stand nachgezählt:

| Zahl im Blatt | nachgerechnet | Urteil |
|---|---|---|
| 30-mal `A-` | 30 | **trifft** |
| 36-mal `W-` | 36 (weites Muster) | **trifft** |
| einmal `M-` | 1 — `M-02-Kopienzahl` | trifft, aber **Fehlalarm, bereits aktenkundig** (Z.2547) |
| einmal `P-` | 1 | **trifft** |
| 12 Tafelzeilen ohne Datensatz | 12 roh · **1** nach Auflösung | **trifft** — 12 = 1 echte + 11 Sammelzeilen |

**Die 12 und meine 1 widersprechen sich nicht — meine Zahl erklärt seine.** Die Tafelzeile `W-01`
gehört zum Datensatz `W-01/1`; wer das nicht auflöst, zählt jeden Werkzeugauftrag als Loch. Genau
diese Auflösung protokolliert die Statuswahrheit selbst als **„15 → 4 → 2"**.

**Und der eigentliche Befund von A-30 ist heute behoben.** Die zwei echten Lücken trugen ihren
Zustand nur an einem der zwei nach §16 vorgeschriebenen Orte:

```
             Tafelzeile   Datensatz          Tafelzeile   Datensatz
   A-06   Basis:  1           0      ->   heute:  1           1
   P-02   Basis:  1           0      ->   heute:  1           4
```

**Heute bleibt genau eine Tafelzeile ohne Datensatz: `M-02-Kopienzahl`** — die aktenkundige
Nicht-Auftragszeile aus einer fremden Tabelle. **Echte Lücken: null, in beide Richtungen.**

### Und jetzt der Teil, der mir gehört: drei Fehlbefunde in einer Runde

Ich habe in dieser einen Prüfung **dreimal** eine Abweichung gemessen, die keine war. **Jedes Mal
lag es an meinem Muster, nie am geprüften Text** — und jedes Mal war der Fehlbefund vor dem Melden
gefangen, weil ich die Abweichung geöffnet statt gezählt habe.

1. **`W-` 33 statt 36.** Mein Muster verlangte `W-<Ziffern>` oder `W-<Ziffern>/<Ziffer>`. Es
   verfehlte **`W-07N`, `W-01N`, `W-21L`** — Suffix-Buchstabe statt Ziffer. Ich hätte gemeldet,
   A-30 habe sich um drei verzählt. **Die DoR hat weit gezählt und lag richtig.**
2. **`M-` verschwunden (1 → 0).** Mein Muster verlangte `**M-02**`; die Zeile heißt
   `**M-02-Kopienzahl**`. Ich hätte gemeldet, eine Tafelzeile sei verlorengegangen.
3. **Vier Datensätze ohne Tafelzeile: `B5`, `B5N`, `B6`, `B7`.** Mein Muster verlangte einen
   **Bindestrich** in der Kennung. Alle vier haben eine Tafelzeile — Z.36, 42, 37, 41. Ich hätte
   vier §16-Verstöße gegen fertige Aufträge gemeldet.

### Die Musterwarnung, die daraus folgt — für jeden, der Kennungen zählt

Vier Formen brechen jeden naheliegenden Kennungs-Zähler. Sie stehen alle in derselben Datei:

```
Suffix-Buchstabe    W-01N  W-07N  W-21L        -> Muster mit \d+$ verfehlt sie
Ohne Bindestrich    B5  B5N  B6  B7            -> Muster mit [A-Z]+- verfehlt sie
Sammelzeile         Tafel W-01 -> Datensatz W-01/1  -> zaehlt als Loch, ist keins
Fremde Tabelle      M-02-Kopienzahl  Generator     -> sieht aus wie eine Auftragszeile
```

**Das ist nicht nur mein Problem.** Der Release-Prüfer hat heute denselben Fehler an anderer
Stelle gemeldet und behoben (`a296eb48`): sein Zählweg lief über `auftrag:` und übersah jeden
Block ohne Kennung — *„der Fehler ist nicht die Zahl, sondern der Zählweg."* Und ich selbst habe
in der Runde davor 36 statt 72 DoR-Bälle gemeldet, weil mein Muster die Schreibweise
`plan-pruefer (DoR), danach …` nicht kannte.

**Dreimal dieselbe Klasse an einem Tag, bei zwei verschiedenen Rollen.** Der gemeinsame Kern: ein
Zähler unterstellt eine Namensform, die Datei kennt vier. **Wer eine Kennungszahl meldet, muss die
Form nennen, nach der er gezählt hat — sonst ist die Zahl nicht nachprüfbar, sondern nur
wiederholbar.**

**Ball: planner** — als Ergänzung zu der Summenprobe-Regel, die er heute schon verankert hat
(*„wer den Reifegrad zählt, nennt die Summe und die Zeilenzahl dazu"*). Diese hier ist ihre
Schwester für Kennungen. **Kein Zustandsfeld angefasst, kein Bau.**

## A-42, vierter DoR-Fund: der Basis-Stand enthält die Datei nicht, an der das Blatt gemessen zu haben angibt

*Vorratsprüfung Posten (a) an einem Blatt in meiner Bahn · gemessen 16.08. gegen `9fd2a2af`*

### Der Fund

A-42 schreibt in seinem tragenden Absatz:

> **Gemessen an `status-erzeugen.sh` (Zeile 45, 291):** `--tafel` erzeugt die Statuswahrheit **aus
> dem Commit-Log** …

Das Blatt führt `basis_sha: e802c1f8` und `dor_schnitt_sha: "e802c1f8"`.

```
git cat-file -e e802c1f8:scripts/status-erzeugen.sh   ->  NEIN, nicht in diesem Baum
git cat-file -e b2d373fb:scripts/status-erzeugen.sh   ->  NEIN  (b2d373fb = der Blatt-Commit selbst)
```

**Die Datei existiert in keinem der beiden Stände, die das Blatt nennt.**

**Warum, und es ist NICHT „die Datei ist zu neu":** `status-erzeugen.sh` entstand mit `1e342d53`
am **16.08. 15:15** — **zwei Stunden VOR** dem Basis-Stand (17:24). Sie ist trotzdem kein
Vorfahre:

```
git merge-base --is-ancestor 1e342d53 e802c1f8   ->  NEIN
```

**Das ist die Rückweg-Klasse**, die ich am Nachmittag als P-07/P-09 gemeldet habe: Der
Planner-Zweig hatte den Generator-Bau zum Schnittzeitpunkt noch nicht erhalten. Die Messung selbst
ist offenkundig richtig — die Datei liegt heute in allen vier Rollenbäumen mit **816 Zeilen** —
aber **am erklärten Stand ist sie nicht nachvollziehbar.** Wer die DoR nachprüft, bekommt „No such
file", nicht eine abweichende Zahl.

**Soll:** `basis_sha` auf einen Stand setzen, der den Bau enthält, oder den Messstand der beiden
Zeiger getrennt ausweisen.

### Was ich geprüft habe und was HÄLT

**Die tragende Behauptung stimmt — im Code nachgelesen, nicht am Wortlaut.** `--tafel` speist sich
aus dem Commit-Log über

```
Z.214   MUSTER = "--grep=^\(\w\+[a-z-]*: \)\?zustand:"
Z.189   r"zustand:\s+"   …   Z.191   r"(?P<zustand>[A-Z_]+)\s+·\s+"
```

**Ein Block ohne Kennung und ohne Zustand kann gar keinen solchen Betreff erzeugen** und damit
nicht in der erzeugten Tafel vorkommen. A-42s Prämisse trägt.

**Zeiger Z.45 trägt den zitierten Wortlaut verbatim** (`Je Kennung gewinnt der juengste Eintrag.`),
die erste Aussage steht eine Zeile darüber in Z.44. Kein Mangel. **Z.291 zeigt heute auf eine
Fangproben-Zeile** (`("vorwort-a-41.md", "a-41", False, …)`) — ob das Wanderung ist, **lässt sich
nicht feststellen**, weil der erklärte Stand die Datei nicht führt. Ich melde es als
untestbar, nicht als Fehler.

### Und ein Fund, den ich NICHT melde, weil das Blatt ihn vorweggenommen hat

Ich habe die Blocktabelle nachgezählt und weiche ab:

```
                     Blatt 22:0x      meine Messung 9fd2a2af
Bloecke gesamt           446                444
  mit zustand:            89                 90
  ohne, mit auftrag:     168                168      <- trifft exakt
  WEDER noch             189                186
```

**Das ist kein Fund.** Das Blatt schreibt die Zahlen ausdrücklich *„nicht als Zusage"* hin und
sagt: *„Der Zählbefehl steht in A-42-1 — keine feste Zahl in einem Kriterium (P6: eine Rot-Lage
mit Uhr ist keine)."* **Genau deshalb bewegt sich die Zahl, ohne dass ein Kriterium bricht.** Meine
Abweichung bestätigt die These des Blatts, statt sie zu widerlegen.

### Eine Falle für jeden, der diese Blöcke zählt — auch für mich

Mein erster Zähler ergab **442** Blöcke, mein zweiter **444**. Der Unterschied: `docs/STATUS.md`
trägt **444 `​```yaml`-Zäune, aber nur 442 schließende** — zwei Blöcke sind nicht zugemacht. Ein
strenger Parser verliert sie stillschweigend; erst die tolerante Form (*ein neuer `​```yaml`
schließt den vorigen*) findet alle. **Das ist dieselbe Klasse wie die vier Namensformen von
gestern Abend: nicht die Zahl ist falsch, sondern die unausgesprochene Annahme über die Form.**

**Ball: planner** (der `basis_sha`-Punkt). **Kein Zustandsfeld angefasst, kein Bau, keine
DoR-Entscheidung — der Fund ist zugestellt, das Urteil steht aus.**

## Alterung der vier ENTWURF-Aufträge: A-38 hält an jedem Punkt — und seine Lage hat sich verhältnismäßig verbessert, absolut verdreifacht

*Vorratsprüfung Posten (d) · gemessen 16.08. gegen `3f93a5cc`*

**Null Aufträge stehen auf BEREIT**, deshalb an den vier ENTWURF-Aufträgen gefahren, deren DoR bei
mir liegt.

### Alterung, gemessen

| Auftrag | Basis | Alter | Commits seither | genannte Dateien, seither geändert |
|---|---|---|---|---|
| A-38 | `0f05f8bf` | **2824 min** (47 h) | **785** | `STATUS.md` 307 · `commit-pruefen.sh` **10** · eigenes Blatt 11 |
| A-39 | `99add90f` | 491 min | 683 | `STATUS.md` 235 · `commit-pruefen.sh` 9 |
| A-40 | `99add90f` | 491 min | 683 | `STATUS.md` 235 |
| A-42 | `e802c1f8` | 272 min | 611 | `STATUS.md` 202 · `status-erzeugen.sh` 12 |

**A-38 ist der Prüffall:** ältestes Blatt, und es misst an einer Datei, die sich seither zehnmal
geändert hat.

### Ergebnis: alle drei prüfbaren Aussagen halten

**Der Zeiger hat zehn Änderungen überstanden.** Das Blatt sagt *„`merge` in
`scripts/commit-pruefen.sh` (-i) → 4 Treffer, keine Prüfung"*:

```
0f05f8bf (Basis)  ->  4       heute  ->  4
```

Und die vier Treffer betreffen heute wie damals **unaufgelöste Merge-Einträge im Index**
(Z.969, 975, 976, 978) — **keine Prüfung von Merge-Commits.** Die Aussage steht wörtlich.

**Die tragende Strukturaussage hält exakt.** Z.67 des Blatts sagt, die markenlosen Commits seien
*„= ALLE"* Merges:

```
Commits gesamt          705
ohne Rollenmarke        180
  davon Merges          180
  davon NICHT-Merges      0        Summenprobe 180 + 0 = 180   GEHT AUF
```

**Kein Kriterium trägt eine feste Zahl.** A-38-1 bis A-38-9 sind sämtlich verhaltensbeschreibend
(Hook existiert, Negativfall, Positivfall, `core.hooksPath`, zweiter Worktree, sechs Kanten,
Nicht-Ziele, Suite). **Die gewachsenen Zahlen brechen daher kein Kriterium** — dieselbe
P6-Disziplin, die A-42 anwendet.

### Die neue Zahl, und sie macht den Auftrag dringlicher

```
                         Blatt (15.08.)      heute (16.08.)
Merges gesamt                    70                313
davon ohne Rollenmarke           58                180
Anteil                          83 %               57 %
```

**Die Quote ist gefallen, die absolute Zahl hat sich verdreifacht.** Der Grund für die bessere
Quote ist sichtbar: der Integrator markiert seine Rückwege inzwischen
(`integrator: Rueckweg — rolle/… `). **Der Grund für die schlechtere absolute Zahl ist derselbe
Betrieb, der die Quote verbessert:** es wird schlicht viel mehr gemergt.

**Für A-38 heißt das:** das Blatt beschreibt die Lage weiterhin richtig, und der Bau wird
dringlicher, nicht entbehrlicher. *Eine Quote, die sich bessert, während die Menge wächst, ist
kein Entwarnungssignal.*

### Und mein vierter Musterfehler in zwei Runden

Meine erste Zählung fand **drei markenlose Nicht-Merge-Commits** und damit einen Widerspruch zu
Z.67. Alle drei waren Fehlbefunde meines Musters:

```
release-pruefer (in Yamas Namen): …
plan-pruefer (release-pruefer in Rollenwechsel): …
```

**Die Rollenmarke kommt auch in der Form `rolle (Zusatz):` vor.** Mein Muster verlangte `rolle:`
direkt. Mit `^<rolle>( \([^)]*\))?:` sind es **null** Ausnahmen, und A-38s Aussage steht.

**Das ist derselbe Kern wie die vier Namensformen und die zwei offenen `​```yaml`-Zäune: nicht die
Zahl ist falsch, sondern meine unausgesprochene Annahme über die Form.** Viermal in zwei Runden,
jedes Mal vor dem Melden gefangen — aber jedes Mal auch nur deshalb, weil ich die Abweichung
geöffnet statt gezählt habe.

**Ball: planner** — die frische Merge-Zahl (180 von 313) als Fortschreibung für A-38.
**Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## Eigene Befunde verfolgt: neun zugestellt, neun angekommen, keiner bewegt — und der Grund ist eine Hand, die seit 77 Minuten ruht

*Vorratsprüfung Posten (e) · gemessen 16.08. 22:0x gegen `d2c14029`*

### Meine neun Befunde, am Halter nachgemessen

| # | Befund | Halter | zugestellt | Alter | Stand heute |
|---|---|---|---|---|---|
| 1 | 39 Bälle abgeben | integrator | `651e61e4` | 27 min | **39**, unverändert |
| 2 | `zustand: BEFUND` ×3 | integrator | `8f293730` | 19 min | **3**, unverändert |
| 3 | `ZURUECKGEZOGEN` undefiniert | planner | `8f293730` | 19 min | **0** Treffer in ARBEITSREGELN |
| 4 | 72 DoR-Bälle in Blättern | planner | `94c98ad0` | 13 min | **72** (45 + 27), unverändert |
| 5 | `dor_beleg` A-41 · W-17/1 | integrator | `94c98ad0` | 13 min | beide `"steht aus"` |
| 6 | `dor_beleg` A-30 · A-33 | integrator | `94c98ad0` | 13 min | beide `"NICHT erteilt"` |
| 7 | Zeiger `F-001:53` in A-32 | planner | `94c98ad0` | 13 min | unverändert (Z.127) |
| 8 | Kennungs-Musterregel | planner | `4a9b449c` | 9 min | nicht verankert |
| 9 | A-42 `basis_sha` | planner | `49900324` | 5 min | `e802c1f8`, Datei fehlt weiter |

**Alle neun sind im Integrationszweig angekommen** — `merge-base --is-ancestor` für jeden der sechs
Zustellcommits: enthalten. **Der Zustellweg trägt.** Und **keiner ist älter als 27 Minuten** —
dass nichts bewegt ist, ist bei diesem Alter kein Vorwurf, sondern normal.

### Was ich stattdessen gemessen habe, und das ist der eigentliche Befund

```
Baelle in docs/STATUS.md
  planner  81 · plan-pruefer 39 · generator 10 · release-pruefer 5 · integrator 2 · evaluator 0
  SUMME                                                                              137

Schreibberechtigt seit 19:36                          1 Rolle  (rollen-tor.sh:344)
Letzter Schreibvorgang an docs/STATUS.md          vor 81 min  (0f969d5e, 20:39)
Letzter Sachcommit des Integrators                vor 77 min  (d10a2f7c, 20:43)

Letzter Sachcommit der uebrigen fuenf Rollen:
  plan-pruefer 2 min · planner 7 min · release-pruefer 7 min · evaluator 13 min · generator 21 min
```

**Fünf Rollen haben in den letzten 21 Minuten gearbeitet. Die eine Hand, die 137 Bälle bewegen
darf, seit 77 Minuten nicht.** In derselben Stunde habe ich allein 17 Sachcommits geschrieben,
zehn davon Zustellungen mit Ballwechsel — **jede einzelne landet auf diesem Stapel.**

### Die Sperre habe ich an mir selbst gemessen, nicht angenommen

Der Torcode trägt einen datierten Beleg, der das Gegenteil nahelegt: *„Die Sperre wirkte VERKEHRT
HERUM … `planner` und `plan-pruefer` haben `docs/STATUS.md` weiter geschrieben — sie umgehen
nichts, das Tor liegt in ihren Bäumen gar nicht."* **Ich habe seit 19:36 behauptet, die Datei sei
für mich gesperrt, ohne es je zu prüfen.** Nachgeholt, in meinem eigenen Arbeitsbaum:

```
scripts/commit-pruefen.sh   vorhanden, 1016 Z.   ruft das Tor in Z.126-134
scripts/rollen-tor.sh       vorhanden,  540 Z.   Sperre in Z.344
STAMM = "plan-pruefer"  !=  "integrator"          -> die Sperre greift fuer mich
```

**Meine Behauptung war richtig — aber sie war es aus Glück, nicht aus Messung.** Der zitierte
Beleg beschreibt einen Stand von 16:17; das Tor ist seither in meinen Baum gewandert. *Ein Beleg
darf veralten. Eine Behauptung über den heutigen Zustand darf es nicht.*

### Und eine Vermutung, die ich beim Messen fallen lassen musste

Ich hielt die Meldung des Planners *„DEADLOCK AUFGELÖST"* (`b5dea668`, vor 7 min) zunächst für
folgenlos, weil sie **keine einzige Torfdatei** anfasst — `rollen-tor.sh:344` steht über alle vier
Stände hinweg zeichengleich.

**Das war falsch gedacht, und der Blick in die Änderung zeigt warum.** Die 24 neuen Zeilen stehen
in `.../rollen/6-integrator/2-WANN-BIN-ICH-DRAN.md` und geben ausdrücklich frei:

> `FREIGEGEBEN  Ballrueckgaben und Zustandswechsel einzeln eintragen — das ist Buchfuehrung ueber
> bereits gefallene Entscheidungen, kein Erzeugen und keine Entscheidung.`

**Der Deadlock war eine Freigabe-Frage, keine Torfrage.** Der Planner hatte den `--tafel`-Schreiblauf
ausgenommen und dabei die einzelne Ballrückgabe mit eingesperrt; er hat genau das gelöst. **Die
Barriere bleibt zu Recht stehen** — sie schützt vor Erzeugen, nicht vor Buchführung.

**Damit ist der Weg frei, und es fehlt nur die Ausführung.**

**Ball: integrator.** Die 137 Bälle sind jetzt buchhalterisch rückgebbar; meine neun Punkte 1, 2, 5
und 6 sind darunter. **Kein Zustandsfeld angefasst, kein Bau.**

## A-40: meine eigene Korrektur wiederholt den Fehler, den sie korrigiert — 64 ist wieder ein Zeilenzähler

*Vorratsprüfung Posten (a) an A-40 · gemessen 16.08. gegen `d2c14029`, Basis `99add90f`*

### Der Fund, und er ist meiner

A-40 führt meine drei DoR-Befunde als *„alle nachgemessen und alle zutreffend"* und behebt sie mit
drei Auflagen. Der erste Befund lautet dort wörtlich:

```
Der Befehl zaehlt ZEILEN, nicht Kennungen:   87 Zeilen  ->  64 Kennungen
```

Und **Auflage (a)** zieht daraus die richtige Folgerung:

> *gezählt wird **je Kennung**, nicht je Zeile — eine Kennung mit drei Definitionsstellen ist EIN
> Eintrag ohne Ampel, nicht drei*

**Die Auflage ist richtig. Die Zahl daneben ist es nicht.** Gemessen im Verzeichnis
`docs/rollenkette/werkbank/01-MATHEMATIK/` über beide Sammlungen:

```
                                        Basis 99add90f    heute d2c14029
Ueberschriftenzeilen  ^### [FNS]-ddd          64                64
verschiedene Kennungen darin                  62                62
```

**64 ist die Zahl der Definitionszeilen. Die Zahl der Kennungen ist 62.** Die Differenz ist
vollständig erklärt und sie ist ausgerechnet das Beispiel, das die Auflage selbst anführt:

```
N-003 hat DREI Definitionsstellen:
   Z.754  N-003 · Sparren-Vorbemessung (Biegung + Durchbiegung) · FACH-GATE
   Z.784  N-003 · Geltungsbereich — von Yama festgelegt 12.08., DAUERGELB
   Z.814  N-003 · AUFLAGE an die Ausgabe — keine stille Zahl

64 − 3 + 1 = 62
```

**Ich habe einen Zeilenzähler (87) durch einen anderen Zeilenzähler (64) ersetzt und ihn
„Kennungen" genannt** — genau der Fehler, den derselbe Befund anprangert, im selben Codeblock, drei
Zeilen über der Auflage, die ihn verbietet. **Das ist Fehler 23**, und er wiegt schwerer als die
vier Musterfehler dieser Nacht: die waren Fehlbefunde vor dem Melden, dieser steht seit dem
Nachmittag in einem fremden Blatt und stützt dort eine Auflage.

### Was daran NICHT zu beanstanden ist

**Die Zahl ist stabil, nicht gewandert** — 64/62 an beiden Ständen. Kein Alterungsproblem.

**Die 87 kann ich nicht widerlegen**, weil das Blatt das Muster nicht mitliefert, mit dem sie
erhoben wurde. Der weite Zählweg (jede Zeile, die irgendwo eine Kennung nennt) ergibt am
Basis-Stand **165**, nicht 87. **Ich melde das als nicht nachprüfbar, nicht als falsch** — und es
ist ein Beleg mehr für die Musterregel, die ich vorhin zugestellt habe: *wer eine Kennungszahl
nennt, nennt die Form dazu.*

**Auflage (c) ist bereits erfüllbar und sinnvoll**, getrennt gemessen:

```
FORMELSAMMLUNG.md    32 Definitionszeilen   30 Kennungen
SOLAR-REGELWERK.md   32 Definitionszeilen   32 Kennungen
```

**Nur die F-/N-Seite trägt überhaupt Mehrfachstellen.** Eine Summe über beide hätte das verdeckt —
Auflage (c) greift genau richtig.

### Soll

**In A-40 die Zeile berichtigen:** `87 Zeilen -> 64 Kennungen` wird zu **`87 Zeilen -> 64
Definitionsstellen -> 62 Kennungen`**. Die Auflage (a) bleibt unverändert richtig; sie bekommt nur
die Zahl, die zu ihr passt.

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## A-39 gegen seine eigenen acht Prüfungen gehalten — es besteht sie. Und mein Fehler 23 steht in ZWEI Blättern, nicht in einem

*Vorratsprüfung Posten (c), auf die Selbstanwendung gewendet · gemessen 16.08. gegen `eddf8ddf`, Basis `99add90f`*

### A-39 an sich selbst geprüft — der Test, an dem A-42-8 gescheitert ist

A-39 definiert acht Innenprüfungen P1–P8. Ich habe sie gegen A-39 selbst gehalten:

| | Prüfung | Ergebnis an A-39 |
|---|---|---|
| **P1** | Kante ohne Kriterium | **besteht** — A-39-8 nennt *„alle sechs Kanten K1–K6"*, die von P1 ausdrücklich erlaubte Sammelform |
| **P3** | geforderte Datei ohne Erzeuger | **besteht** — A-39-1 verlangt `scripts/blatt-pruefen.sh`, der Kopf `art:` nennt es als Liefergegenstand |
| **P6** | Rot-Lage mit Uhr | **besteht** — alle sechs Positivproben hängen an festen SHAs, kein wanderndes Fenster |
| **P7** | Kriterium ohne gangbaren Weg | **besteht** — dritte Frage geprüft, siehe unten |

**P7s dritte Frage — „existiert die verlangte Eigenschaft auf dem Messweg?" — ist die harte.** A-39
stützt sechs Kriterien auf vier historische Stände. Alle vier geprüft:

```
0ee521f7  16.08. 13:38  Vorfahre der Integration
8559b555  16.08. 14:18  Vorfahre der Integration
5db5f8a9  16.08. 13:36  Vorfahre der Integration
5bbc55bf  16.08. 13:23  Vorfahre der Integration
```

**Und zwei davon habe ich inhaltlich geöffnet**, statt die Existenz für den Beleg zu halten:

- **A-39-3** verlangt, dass P2 in A-33-1 *„genau EINS"* findet. Am Stand `8559b555`, Z.179:
  `unter dem Muster A-/W-  genau EINS -> A-06`. **Der Fall liegt dort.**
- **A-39-5** verlangt, dass P4 in A-33-7 *„`scripts/` null Mal"* gegen `art:` findet. Am Elternstand
  `fe6b436a`, Z.232: *„Kein Code. Gegenprobe: der Bau-Commit fasst NUR `docs/STATUS.md` an"* — und
  der Kopf Z.14: *„Liefergegenstand ist `scripts/a33-kennungen-nachziehen.sh`"*. **Der Widerspruch
  liegt dort, wortgenau.**

**A-39s Positivproben sind echt.** Das ist der Punkt, den das Blatt selbst macht: *„Ein Prüfer, den
man nie hat sprechen sehen, ist von einem kaputten nicht zu unterscheiden."*

### Fehler 23 ist größer, als ich ihn gemeldet habe

Ich habe vorhin berichtet, A-40 gebe **64** als Kennungszahl aus, während es Definitionszeilen sind.
**Dieselbe 64 steht auch in A-39**, im Schlussabschnitt:

> *„der Plan-Pruefer misst 36, 37 oder 40, je nach Zaehlweise, und **ich selbst heute 64 ueber beide
> Sammlungen**."*

**Meine Berichtigung muss also zwei Blätter erreichen, nicht eines.** In A-39 ist der Schaden
kleiner — der Absatz heißt *„Zwei Zahlen, die ich nenne ohne sie zu behaupten"* und trägt bereits
den Vermerk *„die Größenordnungs-Aussage trägt, die Zahl nicht"*. **Das ist die richtige Form.** In
A-40 stützt dieselbe Zahl dagegen eine Auflage.

### Die Ampel-Zahlen beider Blätter verifizieren exakt

Mit dem Muster, das A-39 selbst nennt (`^### [FNS]-`), je Sammlung getrennt:

```
FORMELSAMMLUNG.md    32 Definitionszeilen   9 mit Ampel   23 OHNE      30 Kennungen
SOLAR-REGELWERK.md   32 Definitionszeilen   0 mit Ampel   32 OHNE      32 Kennungen
```

**A-39 sagt „32 Kennungen, 23 ohne Ampel"** — beides trifft die FORMELSAMMLUNG auf den Punkt (die
32 als Zeilenzahl, siehe oben). **A-40 sagt „S-Nummern definiert 32, alle 32 ohne Ampel"** — trifft
das SOLAR-REGELWERK exakt. **Zwei unabhängig geschriebene Zahlen, beide reproduzierbar.**

### Eine Zahl für den Bauenden, die noch nirgends steht

A-40s **Auflage (b)** nimmt Einträge mit `nachgerechnet_an` **oder** `gegengeprueft_an` von der
Meldung aus. Wie groß ist diese Ausnahme heute?

```
Feld nachgerechnet_an   1
Feld gegengeprueft_an   0
```

**Die Ausnahme schützt genau einen Eintrag.** Das bestätigt meinen früheren Befund, den A-40 als
*„Er meldet auch den EINZIGEN nachgerechneten Eintrag als ampellos"* aufgenommen hat — **„der
einzige" war und ist wörtlich richtig.** Für den Bauenden ist das relevant: die Ausnahme ist heute
fast leer, sie wird aber mit jedem nachgerechneten Eintrag wachsen.

### Und eine Zahl, die ich nicht widerlegen kann

A-39 nennt *„4 Treffer"* auf `nachgerechnet`. Ich messe je nach Muster **5** (Wortvorkommen je
Datei) oder **1** (das Feld `nachgerechnet_an`). **Keines ergibt 4.** Da das Blatt das Muster nicht
mitliefert, melde ich es **als nicht nachprüfbar, nicht als falsch** — der dritte Fall heute Nacht,
und der dritte Beleg für die zugestellte Musterregel.

**Ball: planner** — Fehler 23 auch in A-39s Schlussabschnitt, dazu die Ausnahme-Größe für A-40s
Auflage (b). **Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## A-39 · DoR-Ergebnis: NICHT ERTEILT — genau EIN §5-Punkt fehlt, und er ist eine Zeile

*§5-Durchgang vollständig gefahren · gemessen 16.08. gegen `6b844aed`, Basis `99add90f`*

### Der fehlende Punkt

§5 verlangt für ein **neu zu bauendes** Werkzeug ausdrücklich einen **benannten Erstnutzer**:

> *„Schreibt der Auftrag ein **neu zu bauendes** Werkzeug vor, tritt an die Stelle ein **benannter
> Erstnutzer**: welche Rolle es ab wann in welchem Ablauf benutzt."* — belegt an A-04-6.

A-39 baut `scripts/blatt-pruefen.sh`. Gemessen, was das Blatt dazu sagt:

| §5 verlangt | A-39 sagt | |
|---|---|---|
| **in welchem Ablauf** | *„Es läuft im DoR-SCHRITT, nicht im Tor"* (Z.6), eigener Abschnitt Z.43 | **genannt** |
| **welche Rolle** | nirgends als Nutzer; `plan-pruefer` steht nur in `dor_beleg`, `ballbesitz` und als Verweis | **herleitbar, nicht genannt** |
| **ab wann** | — | **fehlt** |

**Das ist der einzige Punkt, an dem ich A-39 hängen sehe** — und §5 ist dort eindeutig: *„Fehlt ein
Punkt, bleibt der Auftrag `ENTWURF`."* Es ist eine Zeile im Kopf, kein Umbau.

### Was ich geprüft habe und was trägt

**Alle acht eigenen Prüfungen, gegen A-39 selbst gehalten — es besteht sie:**

```
P1  besteht   A-39-8 nennt "alle sechs Kanten K1-K6" — die von P1 erlaubte Sammelform
P2  besteht   kein Kriterium bindet eine Zahl an einen Bestand ohne Standbezug;
              A-39-10 verbietet es sogar ausdruecklich ("nicht gegen eine feste Zahl pruefen")
P3  besteht   A-39-1 verlangt das Skript, art: nennt es als Liefergegenstand
P4  besteht   A-39-9 schliesst resources/, app/, docs/STATUS.md und commit-pruefen.sh aus —
              NICHT scripts/blatt-pruefen.sh, das der Kopf liefert. Praezise vermieden.
P5  besteht   A-39 vergibt selbst nur EINEN Code (Rueckgabe 0 in A-39-7); alle vier
              exit-Nennungen sind Belegfaelle ueber A-37s doppelten exit 3
P6  besteht   alle sieben Belegstaende sind feste SHAs, kein wanderndes Fenster
P7  besteht   siehe Staende unten
P8  besteht   K4 behandelt den Nicht-Ziel-Pfad ausdruecklich
```

**Alle sieben zitierten Belegstände existieren und sind vom heutigen Stand erreichbar:**

```
0ee521f7  8559b555  5db5f8a9  5bbc55bf  7ef8f046  a613100e  e802c1f8
```

**Zwei davon inhaltlich geöffnet** (im vorigen Befund belegt): der Fall liegt jeweils wortgenau dort.

**A-39-10 ist am Bauort ausführbar** — `gebaut_in: ticket-rolle-generator`, und dieser Baum trägt
`node_modules` **und** `tsc`. *In meinem Baum fehlt beides; das ist ohne Belang, weil dort nicht
gebaut wird.* **Gemessen, nicht angenommen.**

**Der Positivfall A-39-7 ist konstruierbar** — von 40 geprüften Blättern nennen 4 ihre Kanten
sammelweise.

### A-39s eigene P8-Selbstmessung nachgeprüft: die Aussage hält, die Zahl ist gewachsen

Das Blatt begründet seinen Suchraum nicht mit dem Pfad, sondern misst gegen P8:

> *„**32 Dateien außerhalb** tragen ebenfalls `auftrag: "X-NN"` … **Keine einzige trägt
> `## Abnahmekriterien`.**"*

```
Dateien ausserhalb docs/auftraege/ mit auftrag:-Feld   heute  35   (Blatt: 32, gemessen 18:3x)
davon mit '## Abnahmekriterien'                        heute   0   (Blatt: keine einzige)
```

**Die tragende Aussage hält exakt: null.** Die Zahl ist in gut vier Stunden von 32 auf 35
gewachsen — **und drei davon gehen auf mich**, `docs/BEFUND-plan-pruefer-rueckweg-und-tor.md` ist
selbst eine der 35. **Kein Mangel:** solange keine Außendatei Abnahmekriterien trägt, fallen beide
Mengen zusammen und das Suchraum-Argument steht. *Aber es steht auf einer Zahl, die sich bewegt —
und die Bewegung kommt vom laufenden Betrieb, nicht von einem Fehler.*

### Soll

**Eine Zeile im Kopf**, etwa: `erstnutzer: "plan-pruefer, ab dem ersten DoR-Durchgang nach dem Bau
— vor der Erteilung, an jedem Blatt in docs/auftraege/aktiv/."` Damit ist der §5-Punkt belegt und
A-39 aus meiner Sicht `BEREIT`-fähig.

**Ball: planner.** **Kein Zustandsfeld angefasst** — der Eintrag von `dor_beleg` in
`docs/STATUS.md` bleibt beim Integrator, ich darf dort nicht schreiben. **Kein Bau.**

## A-40 · DoR-Fund: das Kriterium A-40-2 vergibt eine Nummer, die A-39 bereits belegt — und acht Stellen nennen vier verschiedene Zahlen

*§5-Durchgang an A-40 · gemessen 16.08. gegen `b7dd6579`, Basis `99add90f`*

### Der tragende Fund: A-40-2 ist nicht bloß veraltet, es zeigt auf eine andere Prüfung

```
- **A-40-2** · **Die siebte Innenprüfung läuft und findet einen echten Fall.**
```

**A-39 liefert acht Prüfungen, P1 bis P8.** Die „siebte" ist damit **vergeben** — sie heißt dort
`P7 KRITERIUM OHNE GANGBAREN WEG`. **A-40s neue Prüfung ist die neunte, nicht die siebte.**

**Das ist kein Schönheitsfehler:** wer A-40-2 baut und „die siebte Innenprüfung" sucht, findet eine
bestehende Prüfung mit völlig anderem Inhalt. Das Kriterium ist nicht unklar — **es ist auf eine
falsche, existierende Sache gerichtet.**

**Und es fällt durch A-39s eigenes P2**, *„feste Zahl ohne Standbezug"*: `die siebte` bindet eine
Zahl an den Bestand von A-39s Prüfungen und trägt **keinen SHA, keinen Zeitstempel und nicht das
Wort Bau-Stand**. Genau der Fall, den P2 melden soll — gefunden mit dem Werkzeug, das A-39 baut.

### Acht Stellen, vier Zahlen

| Stelle | sagt |
|---|---|
| A-39 Blattinhalt (P-Definitionen gezählt) | **acht** |
| A-39 Titelzeile | **acht** |
| **A-39 DATEINAME** `A-39-die-fuenf-innenpruefungen-des-blattes.md` | **fünf** |
| A-40 Kopf `art:` (Z.7) | **fünf** |
| A-40 Abschnitt 3 (Z.72) „neben die sechs aus A-39" | **sechs** |
| **A-40-2 Kriterium (Z.172)** „die siebte" | **sieben** |
| `docs/STATUS.md` Tafelzeile Z.90 „Fünf Innenprüfungen" | **fünf** |
| `docs/STATUS.md` Datensatz Z.22090 | **fünf** |

**A-40 widerspricht sich sogar selbst:** der Kopf sagt fünf, Abschnitt 3 sagt sechs.

### Der Mechanismus, gemessen statt vermutet

```
a613100e   16.08. 15:19    5 Pruefungen   "# A-39 — Fünf Prüfungen …"
e802c1f8   16.08. 17:24    7 Pruefungen   "# A-39 — Sieben Prüfungen …"
b7dd6579   16.08. 22:12    8 Pruefungen   "# A-39 — Acht Prüfungen …"

Dateiname gesetzt 2624062b, 16.08. 14:13 — seither UNVERAENDERT.
```

**A-39 ist während seiner DoR-Runden gewachsen, und der Titel wurde jedes Mal sauber nachgezogen.**
Was nicht mitwuchs: der Dateiname und jede Fremdreferenz. **Jede ist bei der Zahl eingefroren, die
sie beim Schreiben sah.** Das ist kein Nachlässigkeitsfehler, sondern die Bauform: *eine Zahl im
Namen altert, sobald die Sache wächst.*

### Der Präzedenzfall steht in A-38, und er unterscheidet die beiden Fälle

A-38 hat heute denselben Fall an sich selbst behandelt und die Regel dazu formuliert:

> *„Präzedenzfall A-33: dort hieß ein Blatt „zehn Tafelzeilen", gemessen waren es elf — es wurde
> **stillgelegt und durch ein Blatt mit richtigem Namen ersetzt**. **Hier genügt die Berichtigung,
> weil der Dateiname keine Zahl trägt.**"*

**A-39s Dateiname trägt eine Zahl.** Nach dieser Unterscheidung fällt A-39 in die A-33-Klasse, nicht
in die A-38-Klasse. **Ob umbenannt oder anders geheilt wird, ist eine Planner-Entscheidung** — ich
messe nur, dass der Unterschied hier greift.

### Soll

1. **A-40-2**: `die siebte` → **`die neunte`**, oder besser ohne Zahl: *„die in Abschnitt 3
   beschriebene Innenprüfung"*. **Eine Zahl, die auf einen fremden Bestand zeigt, gehört nicht in
   ein Kriterium** — das ist P2 wörtlich.
2. **A-40 Kopf und Abschnitt 3** auf denselben Stand bringen (heute fünf gegen sechs).
3. **A-39 Dateiname und die zwei Stellen in `docs/STATUS.md`** — A-33-Klasse, Planner entscheidet.

**Ball: planner** für 1 und 2, **integrator** für die zwei Stellen in `docs/STATUS.md`.
**Kein Zustandsfeld angefasst, kein Bau. A-40s DoR ist damit nicht abgeschlossen** — dieser Fund
steht vor den übrigen §5-Punkten.

## A-40 · §5-Punkt „jeder Prüfbefehl ist auf Syntax und Aussagekraft geprüft": gefahren — die Syntax hält, die Aussagekraft nicht. Und ich berichtige mich

*§5-Durchgang an A-40, zweiter Teil · gemessen 16.08. gegen `b8a06344`*

### Zuerst Fehler 24, an mir selbst

Ich habe vor zwei Runden gemeldet, A-40s Zahl **87** sei *„nicht nachprüfbar, weil das Blatt das
Muster nicht mitliefert"*. **Das war falsch.** Das Blatt liefert den Befehl mit — in Z.204-205,
sechsunddreißig Zeilen unter der Stelle, die ich zitiert habe. Gefahren:

```
grep -nE '^#+ *\**`?[FNS]-[0-9]{3}|^\| *\**`?[FNS]-[0-9]{3}|^- *\**`?[FNS]-[0-9]{3}' \
     docs/rollenkette/werkbank/01-MATHEMATIK/*.md | grep -vE '🟢|🟡|🔴'

  -> 87 Trefferzeilen
```

**Die 87 ist exakt reproduzierbar.** Ich habe „nicht nachprüfbar" gesagt, ohne den Befehl zu
suchen, der im selben Blatt steht — **derselbe Fehlgriff, den ich heute Abend viermal an fremden
Zahlen gefunden habe, nur andersherum: nicht ein zu enges Muster, sondern gar keines gesucht.**

### Und damit wird Fehler 23 schärfer, nicht hinfällig

```
A-40 sagt:        87 Zeilen  ->  64 Kennungen

gemessen:
  Befehl Z.204 (breit)          87 Zeilen  ->  54 Kennungen
  Muster ^### [FNS]- (eng)      64 Zeilen  ->  62 Kennungen
```

**Das Blatt paart die Zeilenzahl des BREITEN Befehls mit der Zeilenzahl des ENGEN Musters und nennt
Letztere „Kennungen".** Zwei verschiedene Zählwege in einer Zeile, und die zweite Zahl trägt das
falsche Wort. **64 ist weder die Kennungszahl des einen noch des anderen.**

### Der Aussagekraft-Befund: der Befehl zählt die S-Seite doppelt

```
Kennungen aus Ueberschriften   54
Kennungen aus Tabellenzeilen   32
in BEIDEN                      32       <- jede Tabellenzeile ist eine Dublette
NUR in der Tabelle              0
```

**Alle 32 Tabellenzeilen sind Wiederholungen ihrer eigenen Überschrift.** Es ist die
Übersichtstabelle des Solar-Regelwerks (`| **S-001** Tagesnummer und Jahreswinkel | — | S-002 · …`),
also ein **Verzeichnis**, keine Definitionsstelle.

**Damit verletzt der Befehl beide Auflagen, die A-40 sich selbst gegeben hat:**

- **Auflage (a)** — *„gezählt wird je Kennung, nicht je Zeile"*: der Befehl zählt Zeilen, und die
  S-Seite doppelt.
- **Auflage (c)** — *„die beiden Sammlungen werden getrennt ausgewiesen"*: der Befehl gibt **eine**
  Liste über `*.md` aus.

Getrennt gemessen sieht es so aus — **beide Summenproben gehen auf**:

```
FORMELSAMMLUNG     32 Treffer -> 23 nach Ampelfilter -> 22 Kennungen
SOLAR-REGELWERK    64 Treffer -> 64 nach Ampelfilter -> 32 Kennungen
                                 --                    --
                                 87                    54
```

**Der Ampelfilter greift auf der S-Seite null Mal** — dort trägt keine Definitionsstelle eine
Ampel. Das ist kein Fehler des Filters, sondern genau der Befund, den A-40 belegen will.

### Ein Fehlbefund, den ich vor dem Melden gefangen habe

Ich hatte gemessen, dass der Bestand **❌** enthält, das der Filter `🟢|🟡|🔴` nicht kennt — ein
Eintrag mit ❌ würde fälschlich als „ohne Ampel" gezählt. **Nachgeprüft: ❌ steht genau einmal, in
Prosa** (*„… mit ❌. Ich hatte das am 13.08. als Lücke …"*), **und trifft null Befundzeilen.**
Kein Fund. **Der fünfte gefangene Fehlbefund heute Nacht, und wieder war die Auflösung im Text.**

### Soll

**Der Befehl in Z.204-205 muss vor dem Bau umgeschrieben werden** — er ist heute der einzige
Messweg, den A-40 nennt, und er misst nicht, was A-40s Auflagen verlangen:

```
je Sammlung getrennt · nur Definitionsstellen (Ueberschriften), keine Verzeichniszeilen
· je Kennung zaehlen, nicht je Zeile · Eintraege mit nachgerechnet_an ausnehmen (heute: 1)
```

**Zum Vergleich die Zahl, die eine solche Zählung heute liefern würde:**
`FORMELSAMMLUNG 22 · SOLAR-REGELWERK 32` ohne Ampel, bei 30 bzw. 32 Kennungen insgesamt.

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau. A-40s DoR bleibt offen** — dies ist der
zweite Fund, nach der Nummernvergabe in A-40-2.

## §5s Punkt „jedes P1-Kriterium ist vor dem Bau wirksam rot" ist an den vier jüngsten Aufträgen NICHT PRÜFBAR — sie markieren keine Stufen

*§5-Durchgang an A-40, dritter Teil · gemessen 16.08. gegen `b8a06344`*

### Der Fund

```
              Kriterien   Stufenmarkierungen
   A-38            9              1
   A-39           13              0     <-- keine
   A-40            9              0     <-- keine
   A-41           24              0     <-- keine
   A-42           11              0     <-- keine
                  --             --
                  57              0     (ohne A-38)

   zum Vergleich:  A-33  14 Kriterien / 12 Markierungen · A-30 8 · A-32 9 · A-34 6
   von 89 aktiven Blaettern tragen 70 eine Stufenmarkierung.
```

**§5 verlangt: *„jedes P1-Kriterium ist vor dem Bau wirksam rot"*.** Bei einem Blatt ohne
P-Stufen ist dieser Punkt **nicht prüfbar** — weder von mir noch später von
`scripts/blatt-pruefen.sh`, das A-39 baut. **Es fehlt nicht der Nachweis, es fehlt die Frage.**

**A-41 ist der Beleg dafür, dass es durchgeht:** 24 Kriterien, null Markierungen, heute
`BETRIEBSBESTAETIGT`.

**Und A-39 trifft es doppelt:** es baut das Werkzeug, das Blätter gegen sich selbst hält — und
trägt selbst die Markierung nicht, die §5 für seine eigene Abnahme braucht.

### Der Fall, an dem es sichtbar wurde: A-40-6

A-40-6 lautet *„`nachgerechnet_an` trägt die Abweichung, nicht nur das Ergebnis"*. **Der Planner
hat selbst gemerkt, dass er die Rot-Lage beseitigt hatte** — durch seinen eigenen Eintrag an
`S-008` um 14:49 — und eine neue gesetzt. Ich habe beide nachgemessen:

```
Eintraege mit Feld nachgerechnet_an        1
davon mit einer abweichungs-Zeile          1
Kennungen insgesamt                       62
Kennungen OHNE nachgerechnet_an           61

-> Faelle, auf die A-40-6s Aussage anwendbar ist :  1
-> davon rot                                     :  0
```

**Die ersetzte Rot-Lage misst etwas anderes als das Kriterium behauptet:** sie zählt Kennungen
**ohne** das Feld (61 von 62), während das Kriterium prüft, ob ein **vorhandenes** Feld die
Abweichung trägt. Für diese Aussage ist der Bestand heute vollständig grün.

**Das ist kein Mangel des Kriteriums** — es nennt seine Nachweisform selbst: *„(Mutationsprobe.)"*,
und A-39s eigenes **P6 lässt „ein KONSTRUIERTER Fall" ausdrücklich zu.** **Der Mangel ist, dass
niemand prüfen kann, ob A-40-6 überhaupt P1 ist** und die Rot-Pflicht damit greift.

### Und eine ausgefallene Messung, die ich fast gemeldet hätte

Mein erster Lauf ergab **null** Stufenmarkierungen für A-33 — womit ich beinahe geschrieben hätte,
*kein* Blatt markiere Stufen. **A-33 trägt heute zwölf.** Gefangen, weil ich das Muster gegen einen
bekannten Treffer gehalten habe: `A-33-1 (P1, TRAGEND)` am Stand `8559b555`, wo dasselbe Muster
sechs Treffer liefert. **Eine ausgefallene Messung ist kein Ergebnis** — hier hätte sie den Befund
um den Faktor 70 verzerrt, und zwar in die bequeme Richtung.

### Soll

**Die vier Blätter markieren ihre tragenden Kriterien**, wie 70 andere es tun. Das ist keine
Formsache: **ohne die Markierung ist ein §5-Punkt unprüfbar, und ein unprüfbarer Punkt gilt nach
§5 als fehlend** — *„Fehlt ein Punkt, bleibt der Auftrag `ENTWURF`."*

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau. A-40s DoR bleibt offen** — dritter Fund
nach der Nummernvergabe in A-40-2 und dem Prüfbefehl in Z.204.

## A-42 · §5-Punkt „Prüfbefehl auf Syntax und Aussagekraft": gefahren — er hält, wo es zählt, und die Abweichung hebt sich nachweislich auf

*§5-Durchgang an A-42 · gemessen 16.08. gegen `171f9e60`*

### Der Befehl, wörtlich gefahren

A-42-1 liefert seinen Zählbefehl vollständig mit. Gefahren, und gegen zwei Alternativen gehalten:

```
A-42-1 wie geschrieben                446 Bloecke   168 ohne zustand
am Zeilenanfang verankert             442 Bloecke   168 ohne zustand
Zaunzahl als Schiedsrichter           444
```

**Keine der beiden Fassungen trifft 444 — und zwar aus zwei verschiedenen Gründen:**

- **+2 unverankert:** `​```yaml` kommt **452-mal** vor, aber nur **444-mal am Zeilenanfang**. Acht
  Vorkommen stehen **in Prosa** — und mehrere davon in Notizen, die *genau dieses Parser-Problem
  beschreiben* (`„dort steht ein ​```yaml IM TEXT des Feldes dor_beleg, als ZITAT der Fundstelle"`).
  **Der Befehl stolpert über die Dokumentation seines eigenen Fehlers.**
- **−2 verankert:** zwei yaml-Blöcke sind **nie geschlossen** (Z.3215 und Z.7876); ein auf
  `^```$` verankertes Muster verliert sie stillschweigend.

### Und jetzt der Teil, der entscheidet: es hebt sich auf

**Die tragende Zahl ist in allen drei Fassungen identisch: 168.** Das ist die Menge, die A-42
bewegt — *„Blöcke ohne `zustand:`, mit `auftrag:`"*.

**Und keine der sechs Störstellen liegt in einem Block, der umzieht:**

```
Z.58      keine Blockzugehoerigkeit (Tafelzeile)          -> zieht nicht um
Z.13196   Block 13189-13378, hat zustand                  -> bleibt
Z.13234   Block 13189-13378, hat zustand                  -> bleibt
Z.13307   Block 13189-13378, hat zustand                  -> bleibt
Z.3215    unverschlossen, A-08, hat zustand               -> bleibt
Z.7876    unverschlossen, kein auftrag (dritte Klasse)    -> bleibt
```

**Damit trägt A-42-2, die Summengleichung.** Der Zählfehler von +2 erscheint **auf beiden Seiten
der Gleichung** — vorher wie nachher —, weil die störenden Blöcke in `docs/STATUS.md` verbleiben.
**Und A-42-1 verlangt ausdrücklich denselben Befehl für beide Messungen** (*„Vorher und nachher
gezählt, mit demselben Befehl"*). **Das ist keine glückliche Fügung, sondern genau das, wogegen
diese Auflage schützt.**

**Kein Blocker an diesem Punkt.**

### Eine Klarstellung, die ich trotzdem empfehle

Die Zahl **446** wird im Bericht als „Blöcke" erscheinen, und es sind **444**. Wer sie später gegen
eine Zaunzählung hält — so wie ich gerade —, findet eine Abweichung und muss sie erst auflösen.

**Soll:** im Bericht die Zaunzahl danebenstellen, etwa
`446 Regex-Treffer / 444 Zaeune / 168 Umzugsbloecke`. **Das ist dieselbe Disziplin wie die
Summenprobe-Regel des Planners** (*„wer den Reifegrad zählt, nennt die Summe und die Zeilenzahl
dazu"*) — hier für Blöcke.

### Und ein eigener Fehler auf dem Weg dorthin

Mein erster Lauf brach ab: **`(eval):21: parse error`**. Ursache war meine eigene `echo`-Zeile —
ich hatte `​```yaml` in **doppelten** Anführungszeichen stehen, und zsh las die Backticks als
Kommandosubstitution. **Dieselbe Klasse wie mein Here-Dokument-Fehler von heute Nachmittag.**
Sauber wiederholt mit einfachen Anführungszeichen; **kein Messwert stammt aus dem abgebrochenen
Lauf.**

**Ball: planner** (die Klarstellung). **Kein Zustandsfeld angefasst, kein Bau.**

## A-42 · DoR-Fund, der schwerste: der Umzug nimmt 120 von 137 Bällen mit, und kein Kriterium erwähnt das

*§5-Durchgang an A-42, dritter Teil · gemessen 16.08. gegen `88f7bc2b`*

### Die Messung

A-42 verschiebt jeden Block **mit** `auftrag:` und **ohne** `zustand:` nach `docs/BEFUNDNOTIZEN.md`.
Genau diese Blöcke tragen aber den Großteil aller offenen Bälle:

```
ROLLE                bleibt   zieht um   gesamt
plan-pruefer              8         31       39
planner                   3         78       81
generator                 4          6       10
release-pruefer           0          5        5
integrator                2          0        2
evaluator                 0          0        0
SUMME                    17        120      137
```

**120 von 137 Bällen wandern aus der Statuswahrheit heraus.**

### Warum das keine Kleinigkeit ist

**Jede Rolle ortet ihre Bälle über `docs/STATUS.md`.** Meine eigene Wacheanweisung nennt den Befehl
wörtlich: `grep -n '^ballbesitz: plan-pruefer' docs/STATUS.md`. Gemessen:

```
heute            39
nach dem Umzug    8
```

**Der Befehl bleibt richtig und liefert trotzdem eine falsche Lage.** Dasselbe gilt für
`scripts/yama-posten.py` des Release-Prüfers, das über dieselbe Datei läuft — und für den Planner
härter als für alle anderen: **78 seiner 81 Bälle ziehen um.**

**Nichts geht verloren** — A-42s Nicht-Ziele sind an dieser Stelle einwandfrei (*„KEIN Löschen. Kein
Block verschwindet; jeder steht danach vollständig in der Zieldatei."*). **Die Bälle sind nicht weg,
sie sind unauffindbar.** Das ist genau der Unterschied, den A-30 für Zustände gemacht hat: *die
Statuswahrheit sagt dort nicht das Falsche, sie sagt gar nichts.*

### Der §5-Hebel: es gibt keinen dritten Zustand

§5 verlangt wörtlich:

> *„jede Anforderung ist entweder ein **Kriterium** oder ein ausdrückliches **Nicht-Ziel**; einen
> dritten Zustand gibt es nicht"*

Gemessen: **`ballbesitz` kommt in A-42 genau einmal vor — im eigenen Kopf** (Z.15, das Feld des
Blattes selbst). **Kein Kriterium, kein Nicht-Ziel, keine Kante** nennt die Folge für die
Ballortung. Sie steht damit im dritten Zustand, den §5 ausschließt.

### Und die Reihenfolge verschärft es

A-42 nennt seine Stellung in der Kette:

```
staut_hinter: "NICHTS. Muss VOR dem ersten schreibenden --tafel-Lauf fertig sein."
Reihenfolge:  1 Integrationslauf, 2 DIESER Auftrag, 3 erster Schreiblauf.
```

**Die 137 Ballrückgaben kommen in dieser Reihenfolge nicht vor.** Sie brauchen einen Schreibvorgang
in `docs/STATUS.md`, den seit `b5dea668` allein der Integrator ausführen darf — und der hat seit
**106 Minuten** nicht gearbeitet, die Statuswahrheit ist seit **110 Minuten** unberührt.
**Läuft A-42 vorher, zieht der Stapel mit um.**

### Soll — drei gangbare Wege, ich empfehle den zweiten

1. **Die Ballrückgaben laufen VOR A-42.** Dann bleibt nur, was wirklich offen ist. *Setzt voraus,
   dass der Integrator vorher tätig wird — heute nicht absehbar.*
2. **A-42 bekommt eine Ball-Summengleichung**, in seiner eigenen Bauform und neben A-42-2:
   *„Bälle je Rolle vorher = Bälle je Rolle in `STATUS.md` nachher + Bälle in `BEFUNDNOTIZEN.md`"* —
   **plus die Angabe im Bericht, wie die Ballortung nach dem Umzug lautet.** *Das ist dieselbe
   Disziplin, mit der A-42-2 schon den Blockverlust ausschließt, nur für die zweite Größe, die in
   denselben Blöcken steckt.*
3. **Ein ausdrückliches Nicht-Ziel** („die Ballortung wird nicht angepasst") **plus Zustellung an
   alle fünf betroffenen Rollen.** *Ehrlich, aber es verschiebt die Arbeit, statt sie zu benennen.*

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung** — A-42s DoR
bleibt offen, dies ist der zweite Fund nach dem `basis_sha`.

## A-38 · Der Datensatz trägt eine überholte DoR-Freigabe — und die Berichtigung steht in einem Block, den A-42 wegträgt

*§5-Durchgang an A-38 · gemessen 16.08. gegen `88f7bc2b`*

### Zwei Werte für dasselbe Feld

```
Datensatz  docs/STATUS.md      dor_beleg: "BEREIT — 2. Runde 15.08., siehe dor_votum_runde_2"
Blatt      A-38-...md  Z.18    dor_beleg: "NICHT ERTEILT — 3. Runde, siehe docs/STATUS.md.
                                            Restpunkte 16.08. behoben."
```

**Die neuere Information steht im Blatt, die ältere in der Statuswahrheit.** Und der Datensatz
widerspricht sich dabei selbst: er führt `dor_beleg: "BEREIT"` neben `zustand: ENTWURF`. **Wer die
Statuswahrheit liest — und §16 sagt, das ist die maßgebliche Quelle —, sieht eine erteilte
Freigabe, die zurückgenommen wurde.**

### Wo die Rücknahme wirklich steht, und warum das der eigentliche Fund ist

Die dritte DoR-Runde existiert. Sie steht **nicht** im A-38-Datensatz, sondern in einem eigenen
Block, Z.21475–21536:

```yaml
auftrag: "dor_runde_3_votum_a37_a38"
titel: "DoR Runde 3 fuer A-37 und A-38 — NICHT ERTEILT, fuenf Restpunkte, alle klein und alle belegt"
rolle: plan-pruefer
zeit: "2026-08-16 13:00"
```

**Gemessen: dieser Block trägt ein `auftrag:`-Feld und KEIN `zustand:`.** Damit gehört er zu den
**168 Blöcken, die A-42 nach `docs/BEFUNDNOTIZEN.md` verschiebt.**

**Die Folge, wenn A-42 vor der Berichtigung läuft:**

```
docs/STATUS.md sagt dann zu A-38:   dor_beleg: "BEREIT — 2. Runde 15.08."
die Ruecknahme liegt in:            docs/BEFUNDNOTIZEN.md, unter der Freitext-Kennung
                                    "dor_runde_3_votum_a37_a38"
```

**Die Statuswahrheit trüge dann eine Freigabe, deren Rücknahme sie nicht mehr kennt.** Das ist
nicht dieselbe Klasse wie mein Ball-Befund von vorhin — dort werden Posten unauffindbar, hier wird
**eine zurückgenommene Freigabe zur letzten Aussage der maßgeblichen Quelle.**

**Und die Kennung verschärft es:** `dor_runde_3_votum_a37_a38` ist Freitext, keine Auftragskennung.
Wer in `BEFUNDNOTIZEN.md` nach `A-38` sucht, findet den Block über das `titel:`-Feld — aber keine
Zuordnung führt von A-38 dorthin. *Genau der Fall, den A-42-4 benennt: „eine Herkunftszeile mit
Freitext-Kennung ist keine Zuordnung."*

### Ein Fehlbefund, den ich vor dem Melden gefangen habe

Ich hatte zunächst **drei** Werte gezählt: der dritte war `dor_beleg: "steht aus"` in Z.331 des
Blattes. **Nachgesehen: das steht in einem `text`-Block** und ist ein **Zitat** — der
Blattschreiber dokumentiert dort, was er beim Schnitt in die Statuswahrheit eingetragen hat, und
schreibt ausdrücklich dazu: *„Die Abweichung von A-20 wird gemeldet, nicht stillschweigend
gemacht."* **Kein Fund — es sind zwei Werte, nicht drei.** Der sechste gefangene Fehlbefund heute
Nacht.

### Soll

1. **Integrator:** `dor_beleg` im A-38-Datensatz auf den Stand der 3. Runde bringen. **Das ist
   Buchführung über eine bereits gefallene Entscheidung** — genau die Klasse, die `b5dea668`
   ausdrücklich freigegeben hat, und **kein** Zustandswechsel.
2. **Planner:** A-42 muss Blöcke berücksichtigen, die **Entscheidungen zu einem Auftrag** tragen,
   aber selbst keinen `zustand:` führen. Der Ball-Befund von vorhin und dieser hier sind derselbe
   Mechanismus an zwei verschiedenen Feldern — *ein Umzug nach Formmerkmal bewegt auch Inhalte, die
   nach Bedeutung bleiben müssten.*

**Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## Nachtrag zum A-42-Fund: der Umzug trägt Entscheidungen weg — aber es sind FÜNFZEHN Blöcke, nicht hundertachtundsechzig

*Eigene Warnung nachgemessen und begrenzt · 16.08. gegen `b43d26a7`*

### Warum ich meinen eigenen Befund nachgemessen habe

Ich habe vorhin gemeldet, A-42 trage Entscheidungen fort, und dabei den A-38-Fall als Beleg
genannt. **Das war richtig, aber ungezählt.** Eine Warnung ohne Umfang ist für den Planner nicht
arbeitsfähig — er weiß nicht, ob er fünfzehn Blöcke prüfen muss oder hundertachtundsechzig.

### Die Zählung, mit zwei Formen, weil eine nicht reicht

Ein Umzugsblock trägt eine Entscheidung entweder in einem **Feld** oder in seinem **Titel**. Mein
A-38-Fall trug sie im Titel — ein reiner Feldzähler hätte ihn verfehlt:

```
Umzugsbloecke gesamt                        168
davon entscheidungstragend (Vereinigung)     15    =  8 %
   nur ueber ein Feld                         8
   nur ueber den Titel                        6
   ueber beides                               1

betroffene Auftragskennungen:  A-33 · A-37 · A-38
```

**Muster am bekannten Treffer verifiziert:** der Block `dor_runde_3_votum_a37_a38` wird von beiden
Formen erfasst — Titel *„DoR Runde 3 fuer A-37 und A-38 — NICHT ERTEILT"* und Feld
`ballwechsel_quittiert:`. **Ein Zähler, der ihn nicht findet, taugt nicht.**

### Die sechs Titel-Fälle im Wortlaut

```
A-33        BEREIT          "A-33 steht BEREIT mit einer tragenden Zielzahl 1/2 …"
A-33        CODE_FERTIG     "A-33 steht im Generator-Baum auf CODE_FERTIG und im Fernstand …"
A-37        BEREIT          "A-37 ist BEREIT seit 12:39 und wuchs um 12:48 um drei Kriterien …"
A-37        BEREIT          "A-37 ist seit 12:39 BEREIT und hat seither vier Kriterien …"
A-37        BEREIT          "A-37 steht auf BEREIT mit einem ZURUECKGENOMMENEN Votum als Beleg …"
A-37/A-38   BEREIT          "Eine zweite Instanz meiner Rolle hat A-37/A-38 BEREIT gesetzt …"
A-37/A-38   NICHT ERTEILT   "DoR Runde 3 fuer A-37 und A-38 — NICHT ERTEILT, fuenf Restpunkte …"
```

### Was das für die Schwere bedeutet — und ich stufe herunter

**Von den drei betroffenen Aufträgen sind zwei durch:** `A-33` ist `BETRIEBSBESTAETIGT`, `A-37`
steht auf `CODE_FERTIG`. Ihre Entscheidungen sind Historie; ein Umzug in die Befundnotizen nimmt
ihnen nichts, was heute noch gebraucht wird.

**Live ist genau einer: A-38.** Und dort greift der Fund von vorhin unverändert — der Datensatz
sagt `BEREIT — 2. Runde`, die Rücknahme steht im Umzugsblock.

**Damit ist meine Warnung von vorhin richtig, aber sie betrifft 8 % der Umzugsmenge und eine
einzige lebende Entscheidung.** *Das ist kein Grund, A-42 umzubauen — es ist ein Grund, fünfzehn
Blöcke einzeln anzusehen.*

### Soll, jetzt mit Umfang

**Planner:** vor dem Bau die **fünfzehn** entscheidungstragenden Umzugsblöcke durchsehen. Für jeden
nur zwei Fragen: *Steht die Entscheidung auch im Datensatz ihres Auftrags?* Wenn ja, darf der Block
umziehen. Wenn nein — **heute nachweislich bei A-38** —, gehört sie vorher dorthin.

**Das ist ein Arbeitsgang von Minuten, kein Umbau.** Und er schließt genau die Lücke, die A-42-2s
Summengleichung nicht sieht: *sie zählt Blöcke, nicht Bedeutungen.*

**Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## Wache-Punkt 2 systematisch gefahren: 32 DoR-Abweichungen Blatt gegen Datensatz — und ZWEI davon zeigen in die gefährliche Richtung

*Alle 89 aktiven Blätter gegen ihren Block · gemessen 16.08. gegen `252e0ecd`*

### Das Ergebnis

```
vergleichbare Paare (beide Seiten fuehren dor_beleg)     41
   gleich klassifiziert                                    9
   ABWEICHEND                                             32
ein Feld fehlt auf einer Seite                            45
```

**Die Richtung entscheidet, nicht die Zahl:**

```
Datensatz WEITER als Blatt   30
   STEHT-AUS  gegen ERTEILT              28   harmlos: das Blatt wurde nie nachgezogen
   STEHT-AUS  gegen NICHT-ERTEILT         2   A-30, A-33 — beide BETRIEBSBESTAETIGT
BLATT STRENGER als Datensatz  2           <-- die gefaehrliche Richtung
   NICHT-ERTEILT gegen BEREIT             2   A-37, A-38
```

**Die 28 bestätigen unabhängig, was ich als „67 gegenstandslose DoR-Bälle" gemeldet habe** — auf
einem anderen Weg gemessen, dieselbe Lücke: die Blätter wurden nach der Erteilung nicht
fortgeschrieben.

### Die zwei kritischen Fälle sind ein einziger Vorgang

**A-37 ist neu, A-38 hatte ich schon.** Beide tragen dieselbe Form:

```
             Blatt                                    Datensatz
A-37   dor_beleg: "NICHT ERTEILT — 3. Runde"    dor_beleg: "BEREIT — 2. Runde 15.08."
                                                zustand:   CODE_FERTIG
                                                ballbesitz: integrator
A-38   dor_beleg: "NICHT ERTEILT — 3. Runde"    dor_beleg: "BEREIT — 2. Runde 15.08."
                                                zustand:   ENTWURF
```

**Beide zeigen auf denselben Beleg** — den Block `dor_runde_3_votum_a37_a38`, der A-37 **und** A-38
zusammen behandelt und der (gemessen) keinen `zustand:` trägt, also zu A-42s Umzugsmenge gehört.

**A-37 wiegt schwerer als A-38**, weil es weiter ist: es steht auf `CODE_FERTIG`, der Ball liegt
beim Integrator, die Abnahmekette läuft. **Die Statuswahrheit sagt dort „BEREIT — 2. Runde" über
einen Auftrag, dessen dritte DoR-Runde nicht erteilt wurde.** *Dass die fünf Restpunkte laut Blatt
am 16.08. behoben sind, steht ebenfalls nur im Blatt.*

### Und mein erster Lauf war unbrauchbar

Mein erster Klassifikator meldete **78 von 86 abweichend**. Das war kein Befund, sondern ein
kaputtes Werkzeug — zwei Fehler auf einmal:

- `plan-pruefer 12.08.` und `8c2272cd — …` sind **erteilte** DoR-Belege mit Beleg; mein
  Klassifikator warf sie in „SONST" und zählte sie als Abweichung.
- Blätter **ohne** `dor_beleg` im Kopf wurden nicht übersprungen, sondern mitgezählt.

**Ich habe die 78 nicht gemeldet, sondern das Werkzeug repariert** — dieselbe Entscheidung, die der
Planner heute an seiner Reifegrad-Zählung getroffen hat (*„die Summenprobe hat es gefangen"*).
**Eine Abweichungsquote von 91 % ist ein Werkzeugbefund, kein Bestandsbefund.**

### Soll

**Integrator:** `dor_beleg` bei **A-37 und A-38** auf den Stand der 3. Runde bringen. **Ein
Handgriff, zwei Felder, eine Quelle** — der Beleg liegt in `dor_runde_3_votum_a37_a38`. Buchführung
über eine gefallene Entscheidung, kein Zustandswechsel.

**Planner:** die 28 nicht nachgezogenen Blätter — bereits als DoR-Ball-Befund zugestellt, hier auf
zweitem Weg bestätigt.

**Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## Wache-Punkt 2 vollständig: eine Kennungs-Dublette, ein doppelter Schlüssel — und die Zustandskette hält über alle 87 Tafelzeilen

*Alle drei offenen Teilprüfungen gefahren · gemessen 16.08. gegen `aa49949e`*

### 1 · Kennungs-Dubletten: eine, und es ist meine

```
P-04   ->  2 Bloecke mit zustand-Feld
```

**Unverändert der einzige Fall.** Beide Blöcke tragen mein erfundenes `zustand: BEFUND` und stehen
seit 20:41 beim Integrator. **Kein neuer Fund, aber die Bestätigung, dass es der einzige geblieben
ist** — 443 andere Blöcke sind sauber.

### 2 · Doppelter Schlüssel: einer, und er ist neu

```
Block A-09, Z.3259-3290
   release_vermerk:  Z.3267   "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme af8f2054 …"
   release_vermerk:  Z.3288   "release-pruefer 10.08.: RELEASE_FREI an af8f2054 …"
```

**Beide auf Blockebene, beide im selben Block** — geprüft über die Zaungrenzen, nicht über Nähe.

**Die Folge benenne ich aus der Sprachregel, nicht aus einer Vorführung:** YAML verbietet doppelte
Schlüssel; gängige Leser nehmen den **letzten** und verwerfen den ersten ohne Meldung. **Ich konnte
das nicht am Objekt zeigen — PyYAML ist in diesem Baum nicht vorhanden.** *Eine ausgefallene
Vorführung ist kein Beleg, deshalb steht hier die Regel und nicht ein behaupteter Lauf.*

**Der Bestand kennt die Lösung bereits.** Dieselbe Datei führt an vier anderen Stellen
`release_vermerk_1`, `release_vermerk_2` und `release_vermerk_stamm` — die Konvention existiert,
A-09 folgt ihr nur nicht.

**Geringe Dringlichkeit, klare Sache:** A-09 steht auf `BETRIEBSBESTAETIGT`, der verdeckte Vermerk
ist Historie. **Aber A-22 will die Statuswahrheit maschinell lesbar machen** — und ein Block, aus
dem ein Leser stillschweigend eine Zeile verliert, ist genau das nicht.

### 3 · Zustand Tafelzeile gegen Datensatz: null Abweichungen

```
Tafelzeilen mit lesbarem Zustand    87
Datensaetze mit Zustand             89
ABWEICHEND                           0
```

**Das ist das sauberste Ergebnis des Abends, und es gehört genannt.** A-30 hat im August gemeldet,
dass Zustände an zwei Orten auseinanderlaufen; heute laufen sie an keiner einzigen Stelle
auseinander. **Zusammen mit dem A-06/P-02-Befund von vorhin — beide Lücken geschlossen — ist die
§16-Doppelführung des Zustands heute intakt.**

*Die Differenz 87 zu 89 ist keine Lücke: zwei Datensätze führen einen Zustand ohne eigene
Tafelzeile, weil sie über eine Sammelzeile laufen — dieselbe Auflösung, die A-30s „15 → 4 → 2"
beschreibt.*

### Soll

**Integrator:** in A-09 den ersten `release_vermerk` (Z.3267) in `release_vermerk_stamm` umbenennen
— **die Konvention steht vier Zeilen weiter unten im selben Dokument.** Kein Inhalt ändert sich,
kein Zustand wird berührt.

**Kein Zustandsfeld angefasst, kein Bau.**

## Die Gegenmessung des Release-Prüfers nachgerechnet: seine Zerlegung trifft auf den Block genau — und seine Barrierenlücke ebenfalls

*Fremde Zahlen frisch gemessen · 16.08. gegen `265cc00e`*

### Seine Zerlegung 168 = 124 + 41 + 3, unabhängig nachgerechnet

Er hatte 124 gemessen, ich 168, und er hat die Differenz aufgelöst statt sie stehen zu lassen:
*„Er zählt BLÖCKE, ich zähle BÄLLE, und beides ist für die jeweilige Frage richtig."*

**Nachgerechnet über dieselben 168 Umzugsblöcke, ohne seine Zahlen anzusehen:**

```
ballbesitz auf lebender Rolle    124
ballbesitz auf Gedankenstrich     41
kein ballbesitz-Feld               3
ballbesitz auf etwas anderem       0
                                 ---
Summenprobe                      168
```

**Alle drei Zahlen treffen zeichengenau.** Seine Auflösung trägt, und sie ist die richtige: *wer die
41 Gedankenstrich-Blöcke mitzählt, misst geschlossene Sachen mit — für meine Frage richtig, für
seine falsch.*

**Das ist der zweite Fall heute, in dem zwei Rollen verschiedene Zahlen zur selben Sache messen und
beide recht haben** — nach den drei Reifegrad-Zahlen des Planners. **Beide Male war die Auflösung
nicht „wer hat recht", sondern „welche Frage wird gestellt".**

### Seine zweite Feststellung: die Zieldatei ist von keiner Barriere gedeckt

Ich habe sie am Tor selbst nachgemessen, nicht aus seinem Bericht übernommen:

```
scripts/commit-pruefen.sh:132
    case "$_p" in docs/STATUS.md) TOR_STATUS_PFAD=1 ;; esac

BEFUNDNOTIZEN in commit-pruefen.sh :  0 Treffer
BEFUNDNOTIZEN in rollen-tor.sh     :  0 Treffer
```

**Die Pfadliste des Tors ist ein einziges Literal.** Nur `docs/STATUS.md` zündet die Sperre; jeder
andere Pfad läuft ungebremst durch. **Seine Feststellung stimmt.**

### Was meine Messung hinzufügt: die Größe der Verschiebung

**Nach dem Umzug wechseln 124 lebende Bälle die Schutzklasse.**

```
heute      124 lebende Baelle in docs/STATUS.md        -> Sperre greift, nur der Integrator schreibt
danach     124 lebende Baelle in docs/BEFUNDNOTIZEN.md -> keine Barriere, jede Rolle schreibt
verbleibend 17 Baelle in docs/STATUS.md                -> weiter geschuetzt
```

**Derselbe Inhalt, dieselbe Bedeutung, andere Schutzklasse — allein durch den Ortswechsel.** Und
A-42 sieht das Thema durchaus: Z.102-105 behandeln ausdrücklich, dass der Generator nach Zündung
der STATUS-Sperre nicht mehr in `docs/STATUS.md` schreiben darf. **Über den Schutz der Zieldatei
sagt das Blatt nichts.**

**Ich stufe das nicht ein.** Ob die Zieldatei überhaupt geschützt gehören soll, ist eine
Regelentscheidung — die Sperre entstand aus A-37 und hat einen bestimmten Zweck, den ein
Notizspeicher womöglich nicht teilt. **Gemessen ist nur, dass die Frage heute nirgends beantwortet
ist:** weder im Tor noch im Blatt.

### Sein Urteil über meine Arbeit, und warum ich es hier stehen lasse

Er schreibt zu meiner Herunterstufung von vorhin: *„sauber gearbeitet. Er hat seinen eigenen Befund
gezählt statt ihn stehen zu lassen."* **Das notiere ich nicht als Lob, sondern als Beleg:** die
Herunterstufung war nachprüfbar, und sie ist nachgeprüft worden. *Ein Befund, den niemand
gegenmisst, ist von einer Behauptung nicht zu unterscheiden — auch der eigene.*

**Ball: planner** — der Schutz der Zieldatei gehört als Kriterium oder als ausdrückliches
Nicht-Ziel ins Blatt, dritter Zustand ausgeschlossen (§5). **Kein Zustandsfeld angefasst, kein
Bau.**

## Yama-Posten „driftender Zeiger raumAuswahl.ts" frisch gemessen — unverändert, stabil, und mein Fehler 21 ist damit geschlossen

*Offener Posten bei Yama · gemessen 16.08. gegen `fcd007a1`*

### Der Posten selbst: unverändert und nicht gewachsen

`app/raumAuswahl.ts` schreibt in Z.7-8:

> *„Ihre heutige Identität ist der **Index in der Liste** (`Buehne.tsx:147`, `key={\`raum${i}\`}`)"*

```
Buehne.tsx:147   heute:  {massElemente}
Buehne.tsx:162   heute:  <Group key={`raum${i}`} listening={werkzeug === 'auswahl'}>
```

**Die Drift beträgt fünfzehn Zeilen und sie ist STABIL.** Der Befund im Datensatz notierte
seinerzeit ebenfalls *„Heute steht es auf :162"* — heute steht es immer noch dort. **Der Posten ist
nicht schlimmer geworden**, und der Grund ist messbar: `Buehne.tsx` wurde insgesamt nur **viermal**
geändert, zuletzt am 13.08. um 00:56.

*Für Yamas Vorlage ist das die brauchbare Auskunft: ein offener Posten, der nicht driftet, während
er offen ist.*

### Und jetzt der Teil, der mir gehört: Fehler 21 ist geschlossen

In meiner Fehlerliste steht als Nummer 21: **„Nachtrag 12 unvollständig erhoben — nur 1 von 3
Verweisen geprüft."** Genau dieselbe Datei. **Nachgeholt, alle drei:**

```
ableitungen.ts:61              -> export function raeumeAus(waende, level)          TRIFFT
geometry/roomDetection.ts:35-40 -> export interface ErkannterRaum {
                                     polygon; kanten; flaecheMm2; volumenMm3 }      TRIFFT
Buehne.tsx:147                 -> {massElemente}   statt key={`raum${i}`}           DRIFTET
```

**Zwei von drei halten zeichengenau** — und der zweite trifft nicht nur die Zeilenspanne, sondern
**alle vier Feldnamen**, die der Kommentar aufzählt.

**Das Ergebnis ist unbequem für meine ursprüngliche Meldung:** ich hatte einen driftenden Zeiger
gemeldet und dabei den Eindruck erweckt, die Datei sei unzuverlässig. **Gemessen ist das Gegenteil:
zwei Drittel ihrer Zeiger sind heute noch exakt.** *Ein einzelner Fund ohne die Grundgesamtheit
sagt nichts über die Qualität des Ganzen — dieselbe Lehre wie bei den 15 von 168 Umzugsblöcken vor
zwei Runden.*

### Warum die drei sich unterschiedlich verhalten

**Die beiden haltenden Zeiger zeigen in Dateien, die an diesen Stellen nicht gewachsen sind. Der
driftende zeigt in die einzige der drei, die sich seither bewegt hat.** Das ist kein Zufall und
keine Nachlässigkeit des Schreibers — **es ist die Bauform**: eine Zeilennummer ist so haltbar wie
die Datei, in die sie zeigt.

**Bleibt bei Yama**, unverändert. Die Behebung wäre eine Änderung an einem Kommentar in fremdem
Produktivcode — *„ich melde sie und fasse sie nicht an"*, wie es im Datensatz steht, und dabei
bleibe ich.

**Kein Zustandsfeld angefasst, kein Bau.**

## Die Behebung meines A-42-Ballfunds nachgemessen: die Gleichung hält, der mitgelieferte Ortungsbefehl nicht

*Vorratsprüfung (e) an einer FREMDEN Behebung · gemessen 16.08. gegen `0df68243`*

Der Planner hat meinen Fund mit `11232084` behoben und meine Empfehlung wörtlich übernommen: die
Ball-Summengleichung als **A-42-11**, dazu **A-42-12** mit dem neuen Ortungsbefehl je Rolle.
**Ich habe beide gefahren, statt die Behebung zu glauben.**

### A-42-11 hält — und zwar aus demselben Grund wie A-42-2

Die Gleichung lautet: *vorher je Rolle über `docs/STATUS.md`, nachher über **beide** Dateien, die
Summen müssen übereinstimmen.*

**Sie trägt, auch mit dem losen Muster, das das Kriterium mitliefert.** Der Grund ist ein
Erhaltungssatz: jede Zeile liegt nach dem Umzug in genau einer der beiden Dateien, die Summe über
beide bleibt unverändert. **Dieselbe Mechanik, mit der A-42-2s Zählfehler von +2 sich aufhebt.**

### A-42-12 hält NICHT — dem Befehl fehlt der Zeilenanfang

Das Kriterium gibt jeder Rolle diesen Befehl mit:

```
grep -c 'ballbesitz: <rolle>' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

**Ohne `^` zählt er jede Zeile mit, in der die Zeichenfolge irgendwo vorkommt.** Gemessen am
heutigen Bestand:

```
                  ohne Anker    mit Anker
plan-pruefer          49           39
planner               90           81
generator             15           10
release-pruefer        7            5
evaluator              1            0
integrator             2            2
```

**Fünf von sechs Rollen bekämen eine zu große Zahl** — bei mir zehn Phantom-Bälle, beim Planner
neun. **Und die Phantome sind ausgerechnet Fließtext, der die Ballortung zitiert**, zum Beispiel:

```
Z.17743   "'grep ^ballbesitz: plan-pruefer direkt gelesen' — grep liest Zeilen, nicht Zaeune"
Z.19808   "(1) sie liest 'grep ^ballbesitz: plan-pruefer' — ein yaml-FELD."
```

**Der Befehl zählt die Dokumentation seiner selbst.** Dazu kommt eine zweite Unschärfe: drei
Zeilen, die `planner` **und** `plan-pruefer` erwähnen, werden für **beide** Rollen gezählt.

**Und ein zweiter, kleinerer Mangel:** `grep -c` über **zwei** Dateien gibt **zwei Zeilen** aus, keine
Summe:

```
docs/BEFUNDNOTIZEN.md:31
docs/STATUS.md:8
```

*Wer eine Zahl will, muss sie selbst bilden — und wer das übersieht, vergleicht eine Teilzahl mit
einer Gesamtzahl.*

### Die Ironie, die den Fund festmacht

**Meine eigene Wacheanweisung führt den Anker seit jeher:**

```
Wache:     grep -n '^ballbesitz: plan-pruefer' docs/STATUS.md      MIT Anker
A-42-12:   grep -c  'ballbesitz: <rolle>'      zwei Dateien        OHNE Anker
```

**Das Kriterium, das die Ballortung retten soll, liefert eine schlechtere Ortung als die, die es
ersetzt.**

### Soll — ein Zeichen und ein Bindestrich, beide Formen getestet

```
cat docs/STATUS.md docs/BEFUNDNOTIZEN.md 2>/dev/null | grep -c '^ballbesitz: <rolle>'
grep -h '^ballbesitz: <rolle>' docs/STATUS.md docs/BEFUNDNOTIZEN.md 2>/dev/null | grep -c ''
```

**Beide liefern heute 39 für mich und 81 für den Planner — die verankerten Zahlen.** Und beide
überstehen es, dass `docs/BEFUNDNOTIZEN.md` **vor** dem Lauf noch gar nicht existiert: der Fehler
geht auf stderr, die Zahl bleibt richtig. *Das ist keine Kleinigkeit, denn A-42-11 verlangt die
Vorher-Messung genau in diesem Zustand.*

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau.**

## Yama-Posten „Regelkollision §3/E1/Beifang" gemessen: beide Regeln stehen, das Tor erzwingt die eine — offen ist nur noch die Regelfrage selbst

*Offener Posten bei Yama · gemessen 16.08. gegen `9a67f208`*

### Die Kollision, wie sie protokolliert ist

Der Datensatz hält sie in Z.1839 fest:

> *„die REGELKOLLISION im Regelwerk selbst — **„zweiter Commit unmittelbar" gegen „nie fremde
> unverfolgte Arbeit einsammeln"** — steht unverändert, weil sie eine **Regeländerung** braucht und
> keine Arbeitsweise."*

### Beide Seiten stehen heute, mit Fundstelle

```
Seite A   ARBEITSREGELN Z.409
          "unmittelbar nach dem Merge wird dessen SHA in einem Statusuebergang auf dem
           Zielbranch festgehalten; erst dieser Uebergang darf VEROEFFENTLICHT setzen"

Seite B   ARBEITSREGELN Z.692
          "Nur ausdruecklich geprüfte Pfade werden gestaged; niemals git add -A."
```

### Und die eine Seite ist maschinell erzwungen

Am Tor gemessen, nicht gefolgert:

```
ohne Pfadangabe            exit 2, Aufruf-Hinweis          (Exit OHNE Pipe gelesen)
Stagen                     git add -- "$p", je Pfad einzeln, Z.945
git add -A / git add .     0 Treffer im ganzen Tor
```

**Regel B ist damit nicht nur geschrieben, sondern gebaut.** Ein Commit ohne benannte Pfade kommt
gar nicht zustande, und ein pauschales Einsammeln ist im Tor nirgends möglich.

**Folge für die Kollision:** der „zweite Commit unmittelbar" nach Seite A schreibt **einen** Pfad —
`docs/STATUS.md`. Pfadgenau ist das immer möglich, auch wenn im selben Baum fremde unverfolgte
Arbeit liegt. **Die Kollision kann heute von keiner Arbeitsweise mehr ausgelöst werden.**

**Was offen bleibt, ist genau das, was der Datensatz selbst sagt: die Regelfrage.** Ob die zwei
Sätze im Wortlaut nebeneinander stehen bleiben oder einer präzisiert wird, ist eine
Regeländerung — **§1 behält sie Yama vor, und ich entscheide sie nicht.**

**Für die Vorlage ist die brauchbare Auskunft:** *der Posten ist ungefährlich geworden, ohne
entschieden zu sein.* Er kostet heute nichts und blockiert nichts; er ist eine offene Textstelle,
keine offene Gefahr.

### Zwei eigene Fehler auf dem Weg dorthin

**Erstens, fast gemeldet:** mein erster Durchgang fand `Beifang` nur **einmal** in den
Arbeitsregeln — in einem Nebensatz über die Baumtrennung. Ich stand kurz davor zu schreiben, die
Regel sei aus der Prozessquelle verschwunden. **Sie steht in Z.692, nur unter anderen Worten**
(*„niemals `git add -A`"* statt *„Beifang"*). **Der siebte gefangene Fehlbefund heute Nacht — und
wieder war es mein Muster, nicht der Bestand.**

**Zweitens, tatsächlich passiert:** ich habe den Exit-Code des Tores **hinter einer Pipe** gelesen
und `exit=0` notiert. **Das ist der Exit von `head`.** Ohne Pipe gemessen: **exit 2.** *Genau die
Falle, vor der meine eigene Wacheanweisung im selben Absatz warnt, in dem sie das Messen verlangt.*
**Die 0 stand nicht länger als eine Ausgabe im Terminal, aber sie stand da.**

**Ball: yama** — unverändert, als Regelfrage. **Kein Zustandsfeld angefasst, kein Bau.**

## Die Anker-Behebung nachgemessen: jetzt ist das Muster zu STRENG — der Generator verliert die Hälfte seiner Bälle

*Dritte Runde am selben Befehl · gemessen 16.08. gegen `c2596c40`*

Der Planner hat meinen Fund mit `6da4e914` behoben und **beide** Enden verankert:
`^ballbesitz: <rolle>$`. **Der Schluss-Anker ist neu, und er kostet.**

### Gemessen am heutigen Bestand

```
ROLLE                nur ^     sein ^$      lose
plan-pruefer            39          38        49
planner                 81          80        90
generator               10           5        15
release-pruefer          5           5         7
integrator               2           2         2
evaluator                0           0         1
```

**Drei Rollen verlieren Bälle, der Generator die Hälfte.** Die verlorenen Zeilen sind
Ballzeilen **mit angehängtem Kommentar**, zum Beispiel Z.18694:

```
ballbesitz: plan-pruefer  # 16.08. vom Planner zurueckgegeben: die Restpunkte
                          #   der 1. DoR-Runde sind behoben (8f2aed6f, d2ca3611, …)
```

**Das ist eine gültige Ballzeile mit einer Begründung dahinter** — genau die Form, die ich heute
Abend schon einmal gemessen habe, als sie die Differenz zwischen 38 und 39 erklärte.

### Der reine Vorn-Anker wäre auch falsch

**Ich schlage nicht vor, den Schluss-Anker einfach zu streichen.** Fangprobe an vier Zeilen:

```
Eingabe                                  ^…$   ^…   ^…([[:space:]]|$)
ballbesitz: plan-pruefer                  ja    ja        ja
ballbesitz: plan-pruefer-2                nein  JA        nein
ballbesitz: plan-pruefer  # Kommentar     NEIN  ja        ja
ballbesitz: planner                       nein  nein      nein
                                          ---   ---       ---
Treffer                                    1     3         2      <- 2 ist richtig
```

**Der reine Vorn-Anker fängt `plan-pruefer-2` mit** — eine Instanz-Rolle. Heute trägt kein
`ballbesitz`-Feld eine solche Form (gemessen: nur `generator`, `integrator`, `offen`,
`plan-pruefer`, `planner`, `release-pruefer`, `yama`), **aber die Fangproben des Tores führen
`plan-pruefer-2` ausdrücklich als Fall.**

### Soll — die Form, die beide Fallen umgeht, an beiden Proben getestet

```
grep -cE '^ballbesitz: <rolle>([[:space:]]|$)' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

**Über den echten Bestand liefert sie für alle sechs Rollen genau die Soll-Zahl**
(39 · 81 · 10 · 5 · 2 · 0), **und an der Fangprobe trifft sie 2 statt 1 oder 3.**

### Die Lehre, und sie ist inzwischen belegt

**Das ist die dritte Runde an demselben einen Befehl**, und jede hat etwas gefunden:

```
Runde 1   ohne Anker        49 statt 39   zaehlt Prosa mit, die den Befehl zitiert
Runde 2   ^…$               38 statt 39   verliert Ballzeilen mit Kommentar
Runde 3   ^…([[:space:]]|$)     39        trifft, an Bestand UND Fangprobe geprueft
```

**Kein Schritt war unsorgfältig — jeder war eine plausible Verschärfung des vorigen.** Was jedes
Mal fehlte, war dasselbe: **der Befehl wurde überlegt statt gefahren.** *Ein Zählbefehl ist wie
eine Formel: ein richtig gedachtes Muster kann falsch zählen, und man sieht es erst am Fall.*

**Deshalb liegt hier eine Fangprobe bei und nicht nur eine Begründung.**

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau.**

## Konvergenz mit dem Release-Prüfer — und sein Muster ist besser als meins

*Zwei Rollen, derselbe Fund, unabhängig · gemessen 16.08. gegen `74c997c7`*

### Die Konvergenz: sechs Zahlenpaare, sechsmal deckungsgleich

Der Release-Prüfer hat mit `65c30073` denselben Anker-Fund gemeldet wie ich mit `7137054c`,
**unabhängig und im selben Zeitfenster**. Seine Zahlen gegen meine:

```
              er        ich
planner       80/81     80/81
plan-pruefer  38/39     38/39
generator      5/10      5/10
evaluator      0/0       0/0
release-pruefer 5/5      5/5
integrator     2/2       2/2
```

**Sechs von sechs identisch.** Und er ist weiter gegangen als ich: **er hat alle sieben verfehlten
Zeilen einzeln geöffnet** — fünf mit YAML-Kommentar, zwei mit Klammerzusatz — und festgestellt,
dass kein Grenzfall darunter ist.

**Seine Einordnung übernehme ich, weil sie schärfer ist als meine:** *„Eine zu große Zahl lässt
einen suchen und nichts finden. Eine ZU KLEINE sagt ihm, er sei fertig."* **Der Generator sähe 5
statt 10 offener Bälle und hätte keinen Anlass nachzusehen.** Das ist die gefährlichere Richtung,
und ich hatte sie nur als „verliert Bälle" benannt, nicht als *Schweigen, das als Erledigung
gelesen wird.*

### Und jetzt gegen mich selbst: sein Muster fängt mehr als meins

```
mein Vorschlag    ^ballbesitz: <rolle>([[:space:]]|$)
sein Vorschlag    ^ballbesitz: "?<rolle>"?([ #(]|$)
```

**Am echten Bestand liefern beide für alle sechs Rollen dieselben Zahlen.** An der Fangprobe nicht:

```
Eingabe                                  meins   seines
ballbesitz: plan-pruefer                   ja      ja
ballbesitz: "plan-pruefer (DoR)"          NEIN     ja
ballbesitz: plan-pruefer  # Kommentar      ja      ja
ballbesitz: plan-pruefer(x)               NEIN     ja
ballbesitz: plan-pruefer-2                nein    nein
                                          ---     ---
                                            2       4
```

**Mein Muster verliert die gequotete Form** — und das ist genau die Form, die **jedes Blatt**
benutzt (`ballbesitz: "plan-pruefer (DoR)"`). Heute trägt `docs/STATUS.md` 38 gequotete
`ballbesitz`-Werte; keiner davon ist ein Rollenname, deshalb fällt es an dieser Datei nicht auf.
**Es ist eine Lücke, die heute nichts kostet und morgen alles.**

**Eine einzige Kante spricht für meins**, und sie ist theoretisch: bei einem **Tabulator** hinter
dem Rollennamen trifft meins, seins nicht. **Gemessen: null solche Zeilen im Bestand.**

### Die Form, die beide Stärken vereint — an vier Proben getestet

```
grep -cE '^ballbesitz: "?<rolle>"?([[:space:]#(]|$)' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

```
nackt · gequotet · mit Kommentar · mit Tabulator   ->  4 Treffer, alle richtig
plan-pruefer-2 · planner                           ->  0 Treffer, beide richtig abgewiesen
am echten Bestand: plan-pruefer 39 · generator 10  ->  Soll getroffen
```

### Was ich daraus mitnehme

**Ich hatte eine Fangprobe beigelegt und trotzdem eine Form übersehen** — die gequotete. Meine
Probe prüfte die Fälle, an die ich beim Bauen des Musters gedacht hatte. **Seine prüfte die Fälle,
die im Bestand vorkommen.** *Eine Fangprobe ist nur so gut wie die Formenliste, aus der sie
gemacht ist — und die gehört aus der Datei genommen, nicht aus dem Kopf.*

**Ball: planner** — die vereinte Form für A-42-12. **Kein Zustandsfeld angefasst, kein Bau.**

## Yama-Posten W-21L: die zweite Bedingung ist eingetreten — der erste Schritt liegt nicht mehr bei Yama

*Offener Posten bei Yama · gemessen 16.08. gegen `74c997c7`*

### Der Posten trägt seine eigene Ausstiegsbedingung, und sie ist erfüllt

```
Datensatz  ballbesitz: —  # bis Yama die Fachdaten liefert ODER W-23 sie erzeugt
Blatt      ballbesitz: "YAMA — der Auftrag kann ohne Fachdaten nicht in DoR gehen"
           blockiert_durch: "OPERANDEN-GATE — keine Deckungsart/Lattweiten-Daten
                             im Repo, gemessen"
Zustand    DECISION_BLOCKED
```

**W-23 steht heute auf `BETRIEBSBESTAETIGT`, abgenommen 8/8** (`53060551`). Als W-21L geschnitten
wurde, stand W-23 laut dessen eigenem Blatt noch **auf LEER** — *„Ich schlage vor: dieser Auftrag
wartet auf W-23."*

### Die Daten existieren, gemessen und nicht vermutet

W-23s Blatt führt die geforderten Größen für **sieben Braas-Modelle**, im Wortlaut aus der Quelle:

```
Modell               Lattmass     Verschiebespiel   Regeldachneigung   Datenstatus
Achat 12V            330-360 mm         30               16 Grad       verifiziert
Granat 11V           338-380 mm         42               25 Grad       verifiziert
Harzer Pfanne 7      372-405 mm          —               22 Grad       verifiziert
Rubin 13V            330-360 mm         30                —            teilweise verifiziert
Rubin 9V             370-400 mm         30               16 Grad       verifiziert
Topas 11V            320-380 mm         60               25 Grad       verifiziert
Topas 13V            320-360 mm         40               25 Grad       verifiziert
```

**Das ist genau die Tabelle, die W-21L verlangt** — Deckungsart, Lattweite von…bis, Mindestneigung,
Quelle —, nur für sieben Modelle eines Herstellers statt für „die Lattung".

### Was W-23 dazu selbst sagt, und es ist die entscheidende Stelle

> ```
> W-21L wartet laut Fahrplan auf "W-23s Ziegeltabelle" — OPERANDEN-GATE.
> NACH diesem Auftrag ist die Lage:
>   ENTSPERRT fuer SIEBEN Braas-Modelle mit verifiziertem Lattmass-Bereich
>   WEITER GESPERRT fuer alles andere — und das ist RICHTIG, nicht bedauerlich
> ```
> **„Damit ist W-21L nicht ‚entsperrt', sondern ZUSCHNEIDBAR … Der Unterschied gehört in W-21Ls
> Nachschnitt und ist eine PLANNER-ENTSCHEIDUNG, die nach diesem Auftrag fällt."**

### Was ich daraus melde — und was ausdrücklich nicht

**Gemeldet:** W-21Ls `blockiert_durch` ist sachlich überholt. *„Keine Deckungsart/Lattweiten-Daten
im Repo, gemessen"* stimmt heute nicht mehr: es gibt sie, für sieben Modelle, in einem
abgenommenen Auftrag. **Und die Ausstiegsbedingung, die der Datensatz selbst nennt — „oder W-23 sie
erzeugt" —, ist eingetreten.**

**Der nächste Schritt liegt damit beim Planner, nicht bei Yama**: der Nachschnitt auf die sieben
belegten Modelle. W-23 benennt ihn wörtlich als Planner-Entscheidung.

**NICHT gemeldet, weil es nicht stimmt:** dass die Sperre gefallen sei. **Sie steht weiter für alles
außerhalb der sieben** — und W-23 nennt das ausdrücklich *richtig, nicht bedauerlich*. **Eine
Lattung ohne belegten Bereich wäre die erfundene Zahl, gegen die das Gate steht.**

**Für Yamas Vorlage:** *einer seiner acht Posten ist kleiner geworden. Er braucht Yama nicht mehr
für den ersten Schritt — nur noch für alles jenseits der sieben Modelle.*

**Ball: planner** (der Nachschnitt). **Kein Zustandsfeld angefasst, kein Bau, keine
DoR-Entscheidung.**

## A-42-12 ist ausgemessen: DREI unabhängige Wege, sechs Zahlen, keine Abweichung

*Vierte Runde, Abschluss · gemessen 16.08. gegen `520d47b0`*

### Die vereinte Form hält an allen zehn Fällen

Der Release-Prüfer hat die Vereinigung an zehn Fällen gefahren und **zwei ergänzt, die ich nicht
geprüft hatte**. Ich habe alle zehn selbst nachgefahren:

```
                                              SOLL   getroffen
ballbesitz: plan-pruefer                       ja       ja
ballbesitz: "plan-pruefer (DoR)"               ja       ja
ballbesitz: plan-pruefer  # Kommentar          ja       ja
ballbesitz: plan-pruefer<TAB># Tabulator       ja       ja
ballbesitz: plan-pruefer(direkt geklammert)    ja       ja
ballbesitz: plan-pruefer-2                    nein     nein
ballbesitz: planner                           nein     nein
ballbesitz_vorher: plan-pruefer               nein     nein     <- seine Ergaenzung
  ballbesitz: plan-pruefer  (eingerueckt)     nein     nein     <- seine Ergaenzung
x ballbesitz: plan-pruefer                    nein     nein     <- meine Ergaenzung
                                              ----     ----
                                                 5        5
```

**Fünf Treffer, fünf korrekte Abweisungen. Kein Fehltreffer in beide Richtungen.**

### Und die dritte, von der Zeilenform unabhängige Probe

`scripts/yama-posten.py` liest **YAML-Zäune** statt Zeilenmuster. Seine eigene Summenzeile gegen
meinen grep:

```
                  Werkzeug   grep
plan-pruefer          39      39
planner               81      81
generator             10      10
release-pruefer        5       5
integrator             2       2
evaluator              0       0
```

**Sechs von sechs, über zwei grundverschiedene Lesarten.** Damit ist die Zahl nicht mehr von der
Musterwahl abhängig — und genau das war die Krankheit der vier Runden.

### Mein achter gefangener Fehlbefund, und er ist der lehrreichste

**Mein erster Durchlauf des Werkzeugs ergab 39 · 78 · 6 · 5 · 1 · 0** — drei Abweichungen, und
damit ein scheinbarer Widerspruch zur Aussage des Release-Prüfers, das Werkzeug liefere „genau die
rechte Spalte".

**Ich habe die falsche Zahl gezählt.** Statt der Summe, die das Werkzeug selbst nennt, habe ich
**seine Listenzeilen** gezählt — und die zeigen nur Blöcke **mit** Kennung. Das Werkzeug sagt es in
seiner eigenen Kopfzeile:

```
Posten mit ballbesitz: planner   —   81   (Stand HEAD)
  davon mit Kennung 78 · ohne Kennung 3   <- die ohne uebersieht jede Zaehlung ueber auftrag:
```

**Die Auflösung stand in Zeile drei der Ausgabe, die ich schon vor mir hatte.** *Zum achten Mal
heute Nacht — und diesmal hätte ich einen Kollegen widerlegt, der recht hatte.*

**Die Lehre schließt an die der vier Runden an:** *ein Werkzeug fahren genügt nicht, man muss auch
die richtige Zahl aus seiner Ausgabe nehmen.* **Es hat mir seine Summe hingeschrieben, und ich habe
seine Zeilen gezählt.**

### Stand

**A-42-12 braucht keine fünfte Runde.** Die Form ist dreifach belegt, die Zahlen sind es auch.
**Ball: planner** — es fehlt nur noch das Einsetzen. **Kein Zustandsfeld angefasst, kein Bau.**

## Meine eigene Wache-Liste gemessen: von acht Yama-Posten liegen mindestens VIER nicht mehr bei Yama

*Alle acht Posten der Wacheanweisung geprüft · gemessen 16.08. gegen `8781795b`*

### Der Anstoß kam aus der Statuswahrheit selbst

Beim Messen des Seed-Wegs bin ich auf einen Block gestoßen, der genau das schon festgestellt hat:

> *„Die Liste in der Wache kann von acht auf sechs. Zwei Posten haben Yamas Aufmerksamkeit seit
> Tagen ohne Gegenstand gebunden … **Ich habe sie in jeder Runde weitergereicht, ohne sie ein
> einziges Mal zu messen.** Das ist derselbe Fehler, den ich heute Nacht an fremden Blättern
> gemeldet habe: ein Posten wird kopiert statt geprüft."*

**Das trifft mich unverändert.** Ich rezitiere dieselben acht Posten seit vielen Runden. **Also
gemessen, jeden einzeln.**

### Das Ergebnis

| # | Posten | gemessen | Lage |
|---|---|---|---|
| 1 | **Fach-Gate N-003** | FORMELSAMMLUNG Z.784 | **entschieden** — *„von Yama festgelegt 12.08., DAUERGELB"*, mit ERLAUBT/NICHT-ERLAUBT-Liste. Das Gelb ist der beschlossene Dauerzustand, keine offene Frage. |
| 2 | **A-13-Veröffentlichungsbedingung** | STATUS Z.7038-7047 | **entschieden** — die Bedingung wurde gemessen und war **leer** (`p_v_roofs` 0 Zeilen). A-13 ist veröffentlicht und `BETRIEBSBESTAETIGT`, `ballbesitz: —`. |
| 3 | Tragwerk an die Zeichenfläche | — | **nicht gemessen**, bleibt für die nächste Runde |
| 4 | **W-21L** | heute gemessen | **verkleinert** — erster Schritt beim Planner, Yama nur noch jenseits der sieben Modelle |
| 5 | **versatz-Quittung** | STATUS Z.3042 / Z.16843 | **OFFEN, und der Abschlussvermerk ist zu stark** — siehe unten |
| 6 | **Seed-Weg der Prüfbühne** | STATUS Z.3037 | **entschieden** — *„ERLEDIGT 13.08.: Yama hat WEG C entschieden, mit drei Auflagen (fail closed, nur `ticket_testing`, idempotent)."* **Aus steht der BAU**, und der gehört Planner und Generator. |
| 7 | **Zeiger raumAuswahl.ts** | heute gemessen | **offen, stabil** — 147 → 162, unverändert seit der Aufnahme |
| 8 | **Regelkollision §3/E1/Beifang** | heute gemessen | **praktisch geschlossen, formal offen** — beide Regeln stehen, das Tor erzwingt eine |

### Der eine Punkt, an dem ich einer Schließung widerspreche

Ein Abschlussvermerk (Z.16856) sagt: *„GESCHLOSSEN 14.08. … **beide Posten inzwischen entschieden**
(versatz 13.08., Weg C 13.08.)."*

**Für Weg C stimmt das — für versatz nicht.** Der Posten heißt **`versatz-Quittung`**, und
gemessen:

```
Z.3042   VERTRETUNGSENTSCHEID versatz (Release-Pruefer in Yamas Namen, 13.08.) — ERZEUGEND
Z.16843  "...sagt selbst: 'Diese Entscheidung ist eine Vertretung, kein Fachurteil.
          Yama kann sie mit einem Satz umdrehen.'  Eine Quittung liegt nicht vor."
```

**Die Entscheidung liegt vor. Die Quittung nicht.** Der Vermerk setzt beides gleich —
*„entschieden"* ist nicht *„quittiert"*, und der Posten trägt das zweite Wort im Namen. **Ich lasse
ihn offen.**

### Was das für die Vorlage heißt

**Mindestens vier der acht liegen nicht mehr bei Yama** (1, 2, 6, und bei 4 der erste Schritt).
**Einer ist stabil offen** (7), **einer nur noch formal** (8), **einer wirklich offen** (5) — und
einer ist noch ungemessen (3).

**Und die D-Liste in der Statuswahrheit führt Posten 6 weiter unter „BLEIBT BEI YAMA"** (Z.2898),
obwohl vier Zeilen später der Erledigungsvermerk steht. **Das ist dieselbe Klasse wie die
28 nicht nachgezogenen Blätter: die Entscheidung fiel, der Eintrag blieb.**

**Ball: yama** — die Liste gehört ihm, ich lege die Messung daneben und streiche nichts.
**Kein Zustandsfeld angefasst, kein Bau.**

## Yama-Posten „Tragwerk an die Zeichenfläche": die Operanden sind gemessen, die Entscheidung bleibt Handwerk

*Der achte und letzte Posten meiner Wache-Liste · gemessen 16.08. gegen `6cab51a0`*

### Die Frage, im Wortlaut und in ihrem Zusammenhang

Sie stammt aus Punkt 4 einer Planner-Vorlage und lautet dort:

> ```
> W-21 Sparren/Lattung    EIN Aufrufer, und der ist ein ENGINE-PANEL, keine Zeichenflaeche.
>                         Das ist eine ANZEIGE, kein Werkzeug — und nach A-14 traegt sie
>                         jetzt den N-003-Vorbehalt.
>                         -> ZULETZT, und die Frage lautet nicht "Werkzeug bauen", sondern
>                            "gehoert Tragwerk ueberhaupt an die Zeichenflaeche?"
> ```

Und die Statuswahrheit ordnet ihn ein (Z.2903): **„Handwerkspraxis. Vom Bildschirm nicht zu
klären."**

### Was ich messen kann — und es bestätigt die Einordnung

```
Aufrufer von berechneSparren im Produktivcode        1
  davon Engine-Panel      app/dashboard/enginePanels.ts:227     1
  davon Zeichenflaeche    app/rahmen/ · renderers/              0
Nennung OHNE Aufruf       app/tools/faehigkeiten.ts:83          1
Exporte der Sparrenrechnung                                     4
```

**„Ein Aufrufer, und der ist ein Engine-Panel" trifft heute zeichengenau.** Die Zeichenfläche ruft
die Sparrenrechnung **null Mal** — gemessen über `app/rahmen/` und `renderers/`, nicht gefolgert.

**Und der N-003-Vorbehalt steht**, im Panel (Z.223-225, `{ schluessel: 'vorbehalt', label:
'Vorbehalt' }`) und durch eine Zusage gesichert (`sparrenVorbehalt.test.ts`, A-14-2:
*„vorbehalt überlebt `berechneSparren(...) as unknown as EngineErgebnis`"*).

### Mein neunter gefangener Fehlbefund, und es ist die P7-Falle

Meine erste Messung fand **zwei** Produktivdateien mit `berechneSparren` und damit einen
Widerspruch zum „EIN Aufrufer". **Nachgesehen: `faehigkeiten.ts` importiert nicht und ruft nicht
auf** — 0 Importe, 0 Aufrufformen. Sie **nennt** die Funktion in einem Beschreibungstext eines
Katalogeintrags (`{ id: 'engine-sparren', … funktion: '…' }`), und mein `grep` traf diesen Text.

**Das ist wörtlich die P7-Lehre aus meinem eigenen Gedächtnis:** *Ort ≠ Wirkung — Verbraucher über
den Funktionsnamen messen, ein Dateikopf ist kein Beleg.* **Hier war es sogar ein Fließtext in
einer Datenzeile.**

### Was ich NICHT tue

**Ich beantworte die Frage nicht.** Ob Tragwerk an die Zeichenfläche gehört, ist eine Aussage
darüber, **wie ein Zimmerer arbeitet** — ob er die Sparrenlage am Plan setzt oder als Ergebnis
abliest. **Kein Zähler im Repository kann das entscheiden**, und die Statuswahrheit sagt das
selbst: *vom Bildschirm nicht zu klären.*

**Was ich liefere, ist der Boden:** die Frage ist heute keine Bau-Frage. Es gibt nichts zu bauen,
was nicht da wäre — es gibt eine Anzeige, die rechnet, und die Frage ist, ob daraus ein Werkzeug
auf der Fläche werden soll. **Yama kann sie mit einem Satz beantworten, und der Satz ist
Handwerk.**

### Damit ist meine Wache-Liste einmal vollständig durchgemessen

```
1 N-003                    entschieden 12.08. (DAUERGELB)              nicht mehr offen
2 A-13-Bedingung           gemessen leer, veroeffentlicht              nicht mehr offen
3 Tragwerk/Zeichenflaeche   Operanden gemessen, Entscheidung Handwerk   OFFEN, bei Yama
4 W-21L                    erster Schritt beim Planner                  verkleinert
5 versatz-Quittung         Entscheid liegt vor, Quittung nicht          OFFEN, bei Yama
6 Seed-Weg                 Weg C entschieden 13.08., Bau steht aus      nicht mehr bei Yama
7 raumAuswahl.ts           147 -> 162, stabil                           OFFEN, bei Yama
8 Regelkollision           beide Regeln stehen, Tor erzwingt eine       formal offen
```

**Bei Yama liegen sachlich noch drei** (3, 5, 7) **plus eine Regelfrage** (8). **Vier sind
erledigt oder gewandert.**

**Ball: yama.** Ich streiche nichts, die Liste gehört ihm. **Kein Zustandsfeld angefasst, kein
Bau.**

## A-40 · DoR-Ergebnis: NICHT ERTEILT — vier Punkte, und einer davon ist derselbe wie bei A-39

*§5-Durchgang abgeschlossen · gemessen 16.08. gegen `eb023990`*

### Die vier Punkte

**1 · A-40-2 vergibt eine Nummer, die A-39 belegt.** *„Die siebte Innenprüfung"* — A-39 liefert
acht; die siebte heißt dort `P7 KRITERIUM OHNE GANGBAREN WEG`. **A-40s Prüfung ist die neunte.**
Und die Zahl fällt durch A-39s eigenes P2: sie bindet einen fremden Bestand ohne Standbezug.
*(Belegt in `b8a06344`.)*

**2 · Der einzige genannte Messweg misst nicht, was A-40s Auflagen verlangen.** Der Befehl in
Z.204-205 zählt Zeilen statt Kennungen und die S-Seite doppelt (alle 32 Tabellenzeilen sind
Dubletten ihrer Überschriften), und er gibt **eine** Liste über beide Sammlungen aus. **Damit
verletzt er Auflage (a) und Auflage (c) desselben Blattes.** *(Belegt in `761b7e96`.)*

**3 · Keine Stufenmarkierung.** A-40 trägt neun Kriterien und **null** P-Markierungen — wie A-39,
A-41 und A-42, während 70 der 89 aktiven Blätter sie führen. **§5s Punkt „jedes P1-Kriterium ist
vor dem Bau wirksam rot" ist damit nicht prüfbar**, weder von mir noch von `blatt-pruefen.sh`.
*(Belegt in `171f9e60`.)*

**4 · Kein benannter Erstnutzer — NEU, und es ist derselbe Punkt, an dem A-39 hängt.** Gemessen:
`erstnutzer`, „ab wann", „wer benutzt" — **null Treffer im Blatt.** A-40 führt **zwei Pflichtfelder
und drei Zustände** am Facheintrag ein. §5 verlangt für neu Gebautes: *„welche Rolle es ab wann in
welchem Ablauf benutzt."* **Wer `nachgerechnet_an` künftig füllt, steht nirgends.**

### Dazu ein Kettenpunkt, der keine Beanstandung ist

```
A-40  staut_hinter: "A-37, dann A-39"
A-37  CODE_FERTIG
A-39  ENTWURF · dor_beleg "steht aus" · DoR von mir NICHT ERTEILT (ein Punkt)
```

**A-40 kann nicht vor A-39 auf BEREIT.** Das ist kein Mangel des Blattes, sondern die Kette, die es
selbst korrekt benennt — aber es heißt: **Punkt 4 muss in beiden Blättern gelöst werden, sonst
bewegt sich keines.**

### Was hält, und es ist mehr als das, was nicht hält

**Die Nicht-Ziele sind vorbildlich** — fünf, präzise, und zwei davon bemerkenswert:

> *„**Keine Inventur.** Auslösung ist die **Benutzung** — wer eine Aussage anfasst, rechnet sie. Ein
> Eintrag, den nie jemand benutzt, ist in seiner Richtigkeit auch nie eine Gefahr.
> **Selbstskalierend.**"*
> *„**Keine Zahl in einem Kriterium.**"*

**Der Positiv- und der Negativfall stehen getrennt:** A-40-2 verlangt einen **echten Fund**,
A-40-4 verlangt eine **Abweisung** (`GEGENGEPRUEFT` ohne Fundstelle). **Rückweg ist benannt**
(Felder plus ein Prüfschritt, Rücknahme = Commit zurückdrehen).

**Und die Ampel-Zahl trifft:** *„S-Nummern definiert 32, alle 32 ohne Ampel"* — am
SOLAR-REGELWERK zeichengenau nachgemessen.

**Bemerkenswert:** das Blatt verbietet sich selbst die Zahl im Kriterium — **und hält sich daran.**
Die eine falsche Zahl darin (`64 Kennungen`) steht in einem **zitierten Befund von mir**, nicht in
einem Kriterium. *Mein Fehler, nicht seiner.*

### Soll

**Vier Zeilen, drei davon klein:** die Nummer in A-40-2, der Befehl in Z.204-205, die
Stufenmarkierung, die `erstnutzer`-Zeile. **Danach sehe ich A-40 BEREIT-fähig**, sobald A-39 es
ist.

**Ball: planner.** **Kein Zustandsfeld angefasst, kein Bau** — der Eintrag von `dor_beleg` bleibt
beim Integrator.

## Die zwei abwesenden Datensätze unabhängig bestätigt — und ich hatte den Befund vor Stunden gemessen, ohne ihn zu verstehen

*Fremden Fund nachgemessen · 16.08. gegen `2712bf91`*

### Bestätigt, mit denselben zwei Fundstellen

Der Release-Prüfer meldet in `2966ede1`, dass zwei yaml-Blöcke **keinen schließenden Zaun** haben
und deshalb für jedes blockbasierte Werkzeug **abwesend** sind. Selbst gemessen:

```
Bloecke ohne Schliesser: 2
   Z.3215-3255   auftrag: "A-08"   Commit-Tor: unterscheiden, ob ein GIT-Prozess einen Lock haelt
   Z.7876-7889   vorschlag: "Die Auflage wird VORBEDINGUNG an der Stelle, wo ein kuenftiger …"
```

**Dieselben zwei, dieselben Zeilenbereiche.** Und seine drei Zahlen treffen ebenfalls: `bloecke.py`
meldet **A Zaunbilanz 1160 · gerade**, **B 10 Zäune mitten in einer Zeile**, **C Blöcke 442** gegen
**444 Öffner** — Differenz 2.

### Was ich dazu beitrage: seine Unterscheidung ist die eigentliche Leistung

**Ich habe genau diese zwei Blöcke heute Nacht schon gemessen** — im A-42-Zählbefehl-Befund steht:
*„zwei yaml-Blöcke sind nie geschlossen (Z.3215 und Z.7876); ein auf `^```$` verankertes Muster
verliert sie stillschweigend."*

**Ich habe die Tatsache gemessen und die Folgerung nicht gezogen.** Sein Satz ist der Fund:

> **„Ein Block ohne schließenden Zaun ist nicht KAPUTT, er ist ABWESEND — der Unterschied, den die
> Werkzeugkette nicht kannte."**

**Ein kaputter Block wird gezählt und gemeldet. Ein abwesender wird nicht einmal vermisst.** Ich
hatte ihn als Parser-Randfall behandelt, er hat ihn als Datenverlustpfad erkannt. *Dieselbe
Messung, zwei Tiefen.*

### Und ein Zusatzfund, den ich beim Nachfahren gemacht habe

**`scripts/bloecke.py` braucht `NODE_PATH`, und ohne ihn fällt Prüfung C still aus:**

```
ohne NODE_PATH   exit 1   A gerade · B 10 · C "konnte nicht geprueft werden: Cannot find module"
mit  NODE_PATH   exit 0   A gerade · B 10 · C Bloecke 442 · parsen 418 · kaputt 24 (Grundlinie 24)
```

**Beide Exit-Codes ohne Pipe gelesen.** Der Unterschied ist nicht kosmetisch: **ohne `NODE_PATH`
meldet der Lauf `exit 1`, aber der Grund ist ein fehlendes Modul, kein Befund.** Wer die Ausgabe
überfliegt, sieht zwei grüne Prüfungen und eine Umgebungsmeldung — **und hat in Wahrheit eine
Prüfung gar nicht gefahren.**

**Das ist dieselbe Klasse wie A-37-8s `NODE_PATH`-Entlastung**, nur an einem anderen Werkzeug: *ein
Prüfer, der aus Umgebungsgründen schweigt, ist von einem grünen nicht zu unterscheiden, wenn man
nur auf die Zeilen schaut.*

**Soll:** `bloecke.py` sollte den Modulfehler von einem Prüfbefund **unterscheidbar** melden — etwa
mit einem eigenen Rückgabewert oder dem Wort `UNGEPRUEFT` statt einer Fehlermeldung im
Prüfungsblock. *Das ist Werkzeugarbeit und gehört nicht mir.*

### Ball

**integrator** — die zwei fehlenden Zäune stehen in `docs/STATUS.md`, und wer einen Zaun setzt,
entscheidet, wo der Block endet. **Er sagt das selbst und fasst sie richtigerweise nicht an.**

**release-pruefer** — der `NODE_PATH`-Punkt an `bloecke.py`, seinem Werkzeug.

**Kein Zustandsfeld angefasst, kein Bau.**

## Posten (e) über den ganzen Stapel: sechzehn zugestellte Punkte, sechzehn unverändert — und die Ursache ist nicht Nachlässigkeit

*Vollständige Nachverfolgung · gemessen 16.08. gegen `3e797d50`*

### Der Stapel, Punkt für Punkt am Halter gemessen

```
BEIM INTEGRATOR                            Stand
  39 Baelle                                unveraendert 39
  drei zustand: BEFUND                     unveraendert 3
  dor_beleg A-41 / W-17/1                  beide weiter "steht aus"
  dor_beleg A-37 / A-38                    beide weiter "BEREIT — 2. Runde"
  A-09 release_vermerk doppelt             unveraendert 2
  zwei Bloecke ohne Schliesser             unveraendert 2

BEIM PLANNER
  ZURUECKGEZOGEN in ARBEITSREGELN          unveraendert 0 Treffer
  DoR-Baelle in aktiven Blaettern          unveraendert (74 Blaetter)
  Zeiger F-001:53 in A-32                  unveraendert vorhanden
  A-42 basis_sha e802c1f8                  unveraendert
  A-40-2 "Die siebte Innenpruefung"        unveraendert vorhanden
  A-40 Pruefbefehl ueber *.md              unveraendert
  Stufenmarkierung A-39 / A-40 / A-42      unveraendert 0 / 0 / 0
  erstnutzer in A-39 / A-40                unveraendert 0 / 0
  A-42-12 Anker ^...$                      unveraendert zu streng
  A-39 Dateiname traegt "fuenf"            unveraendert
```

**Alter: 3 bis 107 Minuten.**

### Und jetzt die Gegenseite, damit die Zahl nicht lügt

**Der Planner hat in derselben Zeitspanne fünf Commits an meinen Funden gemacht** — die
A-42-Ballgleichung (A-42-11/12), den fehlenden Anker, dessen Überkorrektur, zwei Reihenfolge-Fehler.
**Er arbeitet an meinen Befunden, nur nicht an diesen sechzehn.**

**Das ist kein Vorwurf, sondern eine Warteschlange.** Und ihre zwei Ursachen sind verschieden:

- **Beim Integrator** brauchen **alle sechs** Punkte einen Schreibvorgang in `docs/STATUS.md`, und
  die Datei ist seit **159 Minuten** unberührt. *Ein Halter, eine Datei, eine Hand.*
- **Beim Planner** sind es zehn Punkte an vier Blättern, und er hat in derselben Zeit fünf andere
  von mir abgearbeitet. **Die Schlange ist lang, weil ich schnell bin** — dieselbe Feststellung wie
  vor zwei Stunden, nur jetzt mit der Gegenrechnung daneben.

### Mein zehnter gefangener Fehlbefund, und der billigste

Meine erste Messung ergab für A-40-2 **null Treffer** — ich stand kurz davor zu melden, der Planner
habe die Nummer korrigiert. **Er hat nicht.** Mein Muster suchte `die siebte Innenprüfung`
kleingeschrieben; das Blatt schreibt **`Die siebte Innenprüfung`**, Z.172.

```
grep -c  'die siebte Innenprüfung'   ->  0
grep -ci 'die siebte Innenprüfung'   ->  2
```

**Ein einziger Großbuchstabe** — und ich hätte eine Behebung gemeldet, die nicht stattgefunden hat.
**Das wäre die schlimmere Richtung gewesen:** einen offenen Punkt für erledigt zu erklären, ist
schlimmer, als einen erledigten für offen zu halten. *Der Generator sähe fünf statt zehn Bällen —
derselbe Satz, den der Release-Prüfer heute Nacht über den Anker geschrieben hat.*

### Was ich daraus für die nächste Runde festhalte

**Alle vier ENTWURF-Aufträge meiner Bahn sind durchgeprüft**, meine Wache-Liste ist einmal
vollständig gemessen, und der zugestellte Stapel ist nachverfolgt. **Die nächste Arbeit ist nicht
mehr Finden, sondern Warten** — und Warten ist keine Prüferarbeit.

**Ich verlege den Schwerpunkt** auf die Vorratsprüfung an Blättern, die ich noch nie angefasst
habe, statt den eigenen Stapel weiter zu vermessen. *Ein Befund, der zum vierten Mal
nachgezählt wird, wird davon nicht wahrer.*

**Ball: unverändert — integrator (6), planner (10).** **Kein Zustandsfeld angefasst, kein Bau.**

## W-31 PV-Schnellbelegung durchgerechnet: die Formel ist richtig, die Beschriftung ist vertauscht

*Vorratsprüfung (b) und (c) an einem Blatt, das ich noch nie angefasst hatte · gemessen 16.08. gegen `3e797d50`, Basis `6ace6f3e`*

### Die Zahl trifft, an beiden Ständen

Das Blatt behauptet: *„Der Code EXISTIERT: `geometry/pvBelegung.ts`, **75 Z.**, und er ist
ANGESCHLOSSEN."*

```
Basis 6ace6f3e   75 Zeilen
heute            75 Zeilen
angeschlossen    enginePanels.ts · faehigkeiten.ts · zwei Testdateien
```

### Die Packformel ist mathematisch richtig — physisch nachgerechnet, nicht nur gelesen

```
spalten = max(0, floor((nutzL + gap) / (mW + gap)))
reihen  = max(0, floor((nutzB + gap) / (mH + gap)))
```

Fall: Dachhälfte, Modul 1134 × 1762 mm, Rand 300, Spalt 20, `nutzL = 9400`.

```
floor((9400+20)/(1134+20)) = floor(8,1629) = 8
Probe:  8 Module a 1134 + 7 Spalte a 20 = 9212 mm  <= 9400   passt
        9 Module a 1134 + 8 Spalte a 20 = 10366 mm > 9400    passt NICHT
```

**Die 8 ist maximal — die Formel zählt richtig, und sie zählt die Spalte zwischen den Modulen
korrekt als n−1.** *Das ist die Stelle, an der solche Formeln üblicherweise brechen, und hier
bricht sie nicht.*

### Der Fund: `spalten` und `reihen` sind gegeneinander vertauscht

Die Felddoku sagt: `dachBreite` = **horizontal**, `dachLaenge` = **in Falllinie**. Die Zuweisung
lautet:

```
spalten <- nutzL (FALLLINIE)     / Modul-BREITE
reihen  <- nutzB (HORIZONTALE)   / Modul-HOEHE
```

**Gemessen an einem realistischen Dach — 10 m horizontal, 6 m Falllinie:**

```
Ausgabe des Codes:      4 Spalten · 5 Reihen · 20 Module
uebliche Sprechweise:   5 Spalten · 4 Reihen · 20 Module
```

**Spalten stehen nebeneinander — also entlang der horizontalen Breite. Reihen liegen übereinander
— also entlang der Falllinie.** Der Code zählt genau umgekehrt.

**Und die Beschriftung erreicht den Benutzer:** `enginePanels.ts` Z.395-396 zeigt beide Felder
unter genau diesen Wörtern:

```
{ schluessel: 'spalten', label: 'Spalten' },
{ schluessel: 'reihen',  label: 'Reihen'  },
```

### Was ausdrücklich NICHT falsch ist

**`moduleGesamt` und `kWp` stimmen** — das Produkt ist in beiden Lesarten dasselbe (4·5 = 5·4 = 20).
**Es ist kein Rechenfehler, es ist eine Falschauskunft über die Anordnung.** Dieselbe Klasse wie
A-24s Panel-Zusage und der Auswahl-Drift in W-05/2: *die Zahl ist richtig, das Wort daneben führt
in die Irre.*

**Und ich behaupte NICHT, dass die Orientierungsangabe (`hochkant`/`quer`) falsch ist.** Dafür
müsste ich die Konvention kennen, nach der das Modul seine Breite und Höhe führt — die habe ich
nicht gemessen, und ohne sie wäre es geraten.

### Warum das jemanden trifft

Wer „5 Reihen" liest und daraus die Strings plant, legt sie über die falsche Achse. **Die Modulzahl
stimmt, der Belegungsplan nicht** — und das Panel ist ausdrücklich die *„autarke Schnell-Stufe"*,
also der Ort, an dem jemand ohne Modell eine Belegung ablesen soll.

**Ball: planner.** W-31 steht auf `BETRIEBSBESTAETIGT`; eine Umbenennung ist ein eigener Schnitt.
**Kein Zustandsfeld angefasst, kein Bau.**

## Die NODE_PATH-Behebung nachgefahren: sie hält — der erste Fix heute Nacht, an dem ich nichts finde

*Posten (e) an einer fremden Behebung · gemessen 16.08. gegen `b4c43df9`*

### Beide Richtungen gefahren, Exit-Codes ohne Pipe gelesen

```
OHNE NODE_PATH                                        exit 2
  A  Zaunbilanz 1160 · gerade
  B  Zaun mitten in einer Zeile: 10 (Grundlinie 10, 0)
  C  UNGEPRUEFT — kein Befund, sondern eine fehlende Voraussetzung.
     Grund: Error: Cannot find module 'js-yaml'
     Abhilfe: aus dem Repo-Verzeichnis fahren  oder NODE_PATH setzen
  D  Oeffner ohne Schliesser 2 (Grundlinie 2, 0)
       Z.3215 bis Z.3256 — von A, B und C nicht gesehen
       Z.7876 bis Z.7890 — von A, B und C nicht gesehen

MIT NODE_PATH                                         exit 0
  A · B · C Bloecke 442 · parsen 418 · kaputt 24 · D 2 (Grundlinie 2, 0)
```

**Drei Rückgabewerte, drei Bedeutungen: 0 Grundlinie, 1 Befund, 2 ungeprüft.** Genau das, was
gefehlt hat.

### Und er hat beim Nachmessen etwas Schwereres gefunden als ich gemeldet habe

Ich hatte gemeldet: *C fällt still aus.* Er misst nach und schreibt in den Code:

> *„Beim Nachmessen kam ZWEIERLEI heraus, und das zweite ist schwerer als das gemeldete: **der
> vorzeitige `return` übersprang PRÜFUNG D**, also genau die Kontrolle auf abwesende Datensätze."*

**Am Lauf bestätigt:** ohne `NODE_PATH` läuft D heute und meldet beide abwesenden Blöcke — vorher
kam es dort nie an. **Mein Fund war die Meldung, seiner war die Folge.** *Dieselbe Bewegung wie
gestern in die andere Richtung: ich hatte die zwei Zäune gemessen, er hat das Wort „abwesend"
gefunden.*

**Und er hat sich selbst einen dritten Fall gefangen**, ehe er ihn schrieb: den Grund aus `stderr`
zu nehmen statt der letzten Zeile, weil node seine Versionsnummer anhängt — *„Grund: Node.js
v26.5.0 ist keine Auskunft."*

### Eine Beobachtung, kein Fund

**`D  Oeffner ohne Schliesser 2 (Grundlinie 2, 0)` heißt: die zwei abwesenden Datensätze sind jetzt
GRUNDLINIE.** Der Wächter schützt ab sofort gegen einen **dritten** — er behebt die zwei nicht.

**Das ist die richtige Bauform** (dieselbe wie `kaputt 24 (Grundlinie 24)`), und er sagt es
nirgends anders. **Ich notiere es nur, damit niemand `Grundlinie 2, 0` als „erledigt" liest.** Die
zwei fehlenden Zäune liegen weiter beim Integrator, und das ist richtig so: *wer einen Zaun setzt,
entscheidet, wo der Block endet.*

**Ein Zeichenunterschied ohne Belang:** er nennt die Bereiche `Z.3215 bis Z.3256`, ich hatte
`Z.3215-3255` gemessen — er zählt die Grenzzeile des nächsten Öffners mit, ich nicht. **Derselbe
Block, zwei Konventionen.**

### Was das über die Runde sagt

**Vier fremde Behebungen habe ich heute Nacht nachgefahren.** Bei dreien fand ich einen Rest — dem
Ortungsbefehl fehlte der Anker, dann war er zu streng, und A-42-12 brauchte vier Runden. **Diese
hier hält beim ersten Nachfahren.**

**Der Unterschied ist sichtbar und benennbar:** er hat **beide** Läufe selbst gefahren, bevor er
schrieb, und den dritten Fall (`stderr`-Zeile) dabei gefunden. *Nicht überlegt, sondern gefahren —
genau die Lehre, die wir beide heute Nacht in vier Runden am Anker gelernt haben.*

**Kein Ball.** Der Punkt ist erledigt. **Kein Zustandsfeld angefasst, kein Bau.**

## W-09/1 Treppe: drei Zahlen treffen exakt — und eine der drei DIN-Prüfungen kann im Standardweg nicht anschlagen

*Vorratsprüfung (b) und (c) an einem unberührten Blatt · gemessen 16.08. gegen `8f4ccb97`, Basis `65f3ece4`*

### Die Zahlen treffen, und zwar an beiden Ständen

Das Blatt heißt *„Sieben Module, 698 Zeilen, ZWÖLF Zusagen"*. Nachgezählt:

```
geometry/treppe2D.ts            93      geometry/treppenBauarten.ts     38
geometry/treppe3D.ts            74      geometry/treppenBerechnung.ts  114
geometry/treppeObjekt.ts        84      geometry/treppenTypen.ts       153
geometry/treppeSvg.ts          142
                                        SUMME  698   sieben Module

Basis 65f3ece4:  698      heute:  698
```

**Sieben von sieben, 698 auf 698, an beiden Ständen.** *Das ist die erste Blattzahl heute Nacht,
die ohne jede Auflösung stimmt.*

### Die drei DIN-Regeln sind richtig übersetzt

```
Schrittmass       2s + a   Soll 590..650, ideal 630     DIN 18065: 59..65 cm    trifft
Bequemlichkeit    a - s    Ziel ~120, Toleranz 25       DIN: a - s = 12 cm      trifft
Sicherheit        a + s    Ziel ~460, Toleranz 30       DIN: a + s = 46 cm      trifft
```

### Der Fund: im Standardweg ist das Schrittmaß identisch 630

`treppenBerechnung.ts:73` setzt den Auftritt, **wenn keine Lauflänge vorgegeben ist**:

```
auftrittExakt = 630 - 2 * steigungExakt      // Schrittmaßregel
```

**Damit ist `2s + a` algebraisch immer 630 — unabhängig davon, wie steil die Treppe ist.**
Durchgerechnet an derselben Geschosshöhe von 2600 mm:

```
Steigungen   s (mm)   a (mm)   Schrittmass   Bequemlichkeit   Sicherheit
    15        173,3    283,3      630,0           110,0          456,7
    11        236,4    157,3      630,0           -79,1          393,6
    22        118,2    393,6      630,0           275,5          511,8
```

**Eine Treppe mit 236 mm Steigung und 157 mm Auftritt ist unbegehbar — und die Schrittmaßprüfung
meldet `bestanden`.** Sie prüft nicht die Treppe, sie prüft die Formel, mit der sie den Auftritt
gerade selbst gebildet hat.

### Was ausdrücklich NICHT kaputt ist — und das ist die Hälfte des Befunds

**Die anderen Prüfungen fangen die Fälle:**

```
11 Steigungen:  Bequemlichkeit -79,1 (Ziel 120 ± 25)   schlaegt an
                Sicherheit    393,6 (Ziel 460 ± 30)    schlaegt an
                steigung-max  236,4 mm                 schlaegt als FEHLER an
22 Steigungen:  Bequemlichkeit 275,5 · Sicherheit 511,8  beide schlagen an
```

**Die Rechnung führt niemanden in eine unbegehbare Treppe.** Der Mangel ist nicht, dass etwas
durchrutscht — **der Mangel ist, dass eine der fünf Prüfungen im Standardweg keine Aussage macht
und trotzdem wie eine aussieht.**

**Und mit vorgegebener Lauflänge greift sie sehr wohl:** bei 3800 mm und 15 Steigungen ergibt sich
`Schrittmaß 618,1` — eine echte, nicht vorherbestimmte Zahl.

### Warum das dieselbe Klasse ist wie W-31 und A-24

**Die Zahl ist richtig. Was sie behauptet, ist es nicht.** Wer `Schrittmaß 630,0 mm (Soll 590–650)`
liest, glaubt, die Treppe sei gegen die Regel **geprüft** — sie wurde **aus** der Regel gebildet.
*Dasselbe Muster wie die vertauschten PV-Beschriftungen von vorhin und die Panel-Zusage aus A-24:
nicht falsch gerechnet, sondern falsch ausgesagt.*

**Und das Blatt hat in seiner DoR genau danach gefragt:** *„Was tut sie bei einer Steigung außerhalb
der Grenzmaße?"* **Antwort, gemessen: die Schrittmaßprüfung sagt `bestanden`.** Die Frage war
richtig gestellt und ist an dieser Stelle nicht zu Ende beantwortet worden.

### Soll

**Kein Umbau.** Die Prüfung sollte im Standardweg **sagen, dass sie nichts sagt** — etwa
`Schrittmaß 630,0 mm (aus der Regel gebildet, nicht geprüft)`, oder sie entfällt dort und tritt nur
bei vorgegebener Lauflänge an. *Das ist die A-10-Klasse: sag, was du nicht kannst.*

**Ball: planner.** W-09/1 steht auf `BETRIEBSBESTAETIGT`; die Änderung ist ein eigener Schnitt.
**Kein Zustandsfeld angefasst, kein Bau.**

## W-08/1 Dachfläche: hält an jedem gemessenen Punkt — und ich trage die fehlende Größenordnung nach

*Vorratsprüfung (b) und (c) am dritten unberührten Blatt · gemessen 16.08. gegen `8f4ccb97`, Basis `b202ad7c`*

### Alles, was das Blatt behauptet, trifft

```
polygonFlaeche.ts     48 Zeilen     Basis b202ad7c: 48    heute: 48
                      ZWEI Exporte  Z.19 interface Punkt2D · Z.31 function polygonFlaecheM2
```

**Und die Gaußsche Flächenformel rechnet richtig**, an sieben Fällen gefahren:

```
Quadrat 10x10           100,000    Dreieck 10x10            50,000
dasselbe RUECKWAERTS    100,000    L-Form                   27,000
Trapez (Walmflaeche)     28,000    entartet (2 Punkte)       0,000
kollinear                 0,000
```

**Alle sieben treffen** — einschließlich der drei Zusagen aus dem Dateikopf: umgekehrte
Punktreihenfolge liefert dasselbe positive Ergebnis, weniger als drei Punkte liefern 0, und
kollineare Punkte liefern 0 statt NaN.

### Der „gefährlichste Grenzfall" ist vom Blatt selbst gefunden — und besser benannt, als ich es getan hätte

> **„Die Neigung steckt in der EINGABE, nicht in einer Korrektur. Wer flache Koordinaten übergibt,
> bekommt die Grundfläche statt der Dachfläche — und das Modul kann das nicht erkennen. Zwei
> Punktlisten sehen identisch aus; nur die Bedeutung unterscheidet sie."**

**Ich hatte denselben Punkt aus dem Dateikopf abgeleitet, bevor ich den Abschnitt las.** Das Blatt
war zuerst da und führt ihn im **Titel**. **Kein Fund.**

### Was ich beitrage: das Blatt nennt die Gefahr, beziffert sie aber nicht

Es führt `F-023  A_Dach = A_Grundriss / cos(alpha)` als *„NICHT implementiert — und nicht nötig"*
und quantifiziert den Fehler nirgends. **Gerechnet:**

```
Neigung   1/cos(a)   Fehlmenge, wenn flache Koordinaten uebergeben werden
 15 Grad   1,0353      3,5 % zu wenig
 25 Grad   1,1034     10,3 % zu wenig
 38 Grad   1,2690     26,9 % zu wenig      <- haeufige deutsche Regeldachneigung
 45 Grad   1,4142     41,4 % zu wenig
 60 Grad   2,0000    100,0 % zu wenig

Satteldachhaelfte, Grundflaeche 60 m2:
  25 Grad -> echte Flaeche 66,20 m2   Fehlmenge  6,20 m2
  38 Grad -> echte Flaeche 76,14 m2   Fehlmenge 16,14 m2
  45 Grad -> echte Flaeche 84,85 m2   Fehlmenge 24,85 m2
```

**Bei der gängigsten Neigung fehlen gut ein Viertel der Fläche** — und weil der Fehler *zu wenig*
liefert, fällt er im Angebot als **zu billig** auf, nicht als unmöglich. *Das ist die Richtung, in
der ein Mengenfehler am längsten unentdeckt bleibt.*

**Das ist kein Mangel des Blattes**, sondern eine Ergänzung: es hat den Fall richtig erkannt und
richtig eingeordnet. **Eine Zahl daneben macht aus einer richtigen Warnung eine bemessene** — und
sie steht jetzt hier, falls jemand F-023 doch noch baut.

### Mein elfter gefangener Fehlbefund

Mein erster Exportzähler suchte `^export (function|const)` und fand **einen** — ich stand kurz
davor, die Angabe „ZWEI Exporte" als falsch zu melden. **`export interface` fällt nicht unter
`function|const`.** Korrekt gezählt sind es zwei, wie das Blatt sagt.

*Dieselbe Klasse wie die vier Namensformen, der fehlende Anker und der Großbuchstabe: mein Muster,
nicht ihr Text.*

**Kein Ball** — ich lege die Größenordnung nur dazu. **Kein Zustandsfeld angefasst, kein Bau.**

## Mein „Zeichenunterschied ohne Belang" war einer mit Belang — Fehler 25, und er ist eine neue Klasse

*Fremde Berichtigung nachgefahren, eigene Abtuung geprüft · gemessen 16.08. gegen `2a0d6611`*

### Was ich geschrieben habe

> *„Ein Zeichenunterschied ohne Belang: er nennt die Bereiche `Z.3215 bis Z.3256`, ich hatte
> `Z.3215-3255` gemessen — er zählt die Grenzzeile des nächsten Öffners mit, ich nicht. **Derselbe
> Block, zwei Konventionen.**"*

### Was tatsächlich dort steht

```
Z.3254   ## A-09 — RELEASE_FREI an af8f2054 (Ball bei Yama: main-Ve…)
Z.3255   (leer)                                <- hier endet A-08 wirklich
Z.3256   ```yaml                                <- Oeffner des NAECHSTEN Blocks
Z.3260   auftrag: "A-09"
```

**Seine Angabe reichte über die Überschrift von A-09 hinweg bis in dessen Öffner.** Sein Satz ist
wörtlich belegt: *„Meine Zeilenangabe führt also auf den falschen Block."*

**Es waren nicht zwei Konventionen. Es war ein Off-by-one, und ich hatte die richtige Zahl.**

### Die Berichtigung, beide Kanten selbst gefahren

```
EINGABE       ALT              NEU
ende1.md      Z.2 bis Z.5      Z.2 bis Z.4     <- Datei hat nur 4 Zeilen
ende2.md      Z.2 bis Z.4      Z.2 bis Z.4

echter Bestand
A-08-Block    Z.3215-3256      Z.3215-3255
```

**Die alte Fassung meldete bei abschließendem Zeilenumbruch eine Zeile, die es nicht gibt** — genau
der zweite Off-by-one, den seine Gegenprobe fand und den er ohne sie nie gesehen hätte, *„weil er
im echten Bestand nicht vorkommt"*. **Beide Berichtigungen greifen, beide Varianten geprüft.**

*Meine Kopie trägt die Berichtigung noch nicht — ich habe seine Fassung aus dem Integrationsstand
gefahren und gegen meine alte gehalten. Der Unterschied ist damit gemessen, nicht behauptet.*

### Fehler 25, und er ist eine neue Klasse

**Die bisherigen elf gefangenen Fehlbefunde hatten alle dieselbe Form:** mein Muster fand zu viel
oder zu wenig, ich öffnete die Abweichung, und sie löste sich auf. **Dieser hier ist das
Gegenteil:**

```
gemessen        richtig  (3255)
Abweichung      gesehen
eingeordnet     als belanglos abgetan
```

**Ich habe einen echten Befund selbst weggeredet** — mit einer plausiblen Erklärung, die ich nicht
geprüft habe. *„Zwei Konventionen" klang vernünftig, und es hätte gestimmt, wenn beide Zahlen auf
denselben Block gezeigt hätten. Ich habe nicht nachgesehen, worauf Z.3256 zeigt.*

**Die Lehre ist nicht dieselbe wie bisher.** Bisher hieß sie: *ein Muster kann falsch zählen, also
öffne die Abweichung.* **Diese hier heißt: eine Abweichung, die man geöffnet hat, ist damit noch
nicht erklärt** — eine Erklärung, die man nicht misst, ist eine Vermutung mit besserem Ruf.

**Und sie wiegt schwerer als die elf**, denn ein weggeredeter Befund kommt nicht wieder: er ist
gemeldet worden und ausdrücklich für unerheblich erklärt. **Ohne seine Gegenmessung stünde er
heute so im Bestand.**

### Stand

**Kein Ball.** Beide Berichtigungen sind gefahren und halten. **Kein Zustandsfeld angefasst, kein
Bau.**

## W-22/1: zwei Zahlen treffen exakt, die dritte ist gewandert — und sie steht in einem P1-Kriterium ohne Standbezug

*Vorratsprüfung (b) am vierten unberührten Blatt · gemessen 16.08. gegen `b17b764d`, Basis `95fe1b88`*

### Zwei Zahlen treffen an beiden Ständen

```
gaubeGeometrie.ts    Basis 498 Zeilen / 26 Exporte     heute 498 / 26
auswechslung.ts      Basis 174 Zeilen                  heute 174
```

**Die Exportsumme geht auf:** 2 `const` + 11 `function` + 10 `interface` + 3 `type` = **26**.
*Diesmal habe ich das vollständige Muster von Anfang an benutzt — nach Fehler 11.*

### Die dritte ist gewandert, und die Erklärung ist gemessen

```
                        Basis 95fe1b88    heute
gaubeGeometrie.ts             498          498
aufbauPlatzierung.ts          190          219      <- gewachsen
auswechslung.ts               174          174
aufbauOrientierung.ts          61           61
aufbautenStatus.ts             52           52
                              ---         ----
                              975         1004
```

**Am Basis-Stand stimmt die 975 auf die Zeile.** Das Blatt sagt *„selbst nachgezählt, exakt die
Zahl des Auftrags"* — **und das war richtig.**

**Die Erklärung habe ich gemessen, nicht angenommen** *(die Lehre aus Fehler 25)*:

```
git log 95fe1b88..HEAD -- aufbauPlatzierung.ts   ->  genau EIN Commit: 2a06907d, 13.08. 08:00
git merge-base --is-ancestor 95fe1b88 HEAD       ->  JA
```

**Ein Commit, +29 Zeilen, nach dem Schnitt.** Die Zahl ist gealtert, nicht falsch gewesen.

### Der eigentliche Fund: die Zahl steht in einem P1-Kriterium

```
W-22/1-8 (P1, der Dachaufbauten-Befund steht im Blatt): Das Blatt nennt, dass fuenf Module
(975 Zeilen) das Thema Dachaufbauten bilden …
```

**Kein SHA, kein Zeitstempel, kein „Bau-Stand" im Kriterium.** Und das ist wörtlich der Fall, den
**A-39s P2** melden soll:

> *„Ein Kriterium, das eine Zahl mit einer Bestandsaussage verbindet („genau N", „Suite N",
> „N Treffer"), muss im selben Kriterium einen SHA, einen Zeitstempel oder das Wort ‚Bau-Stand'
> tragen."*

**Und die Folge ist bereits eingetreten:** das Kriterium wurde mit `GRUEN` abgenommen und trägt
975; heute misst dieselbe Zählung **1004**. **Wer es heute nachprüft, findet eine andere Zahl und
kann ohne eine Messung am Basis-Stand nicht entscheiden, ob die Abnahme falsch war oder der Bestand
gewachsen ist.** *Ich konnte es nur, weil ich beide Stände gemessen habe.*

### Warum das für A-39 wertvoll ist

**A-39 sucht historische Positivproben für seine acht Prüfungen** — Fälle, an denen die Prüfung
nachweislich anschlägt. **Hier ist eine für P2, die es noch nicht führt:**

```
Blatt      W-22/1, Kriterium W-22/1-8 (P1)
Stand      95fe1b88 — dort ist die Zahl 975 und richtig
heute      1004
Befund     feste Bestandszahl in einem P1-Kriterium ohne Standbezug
```

**Der Unterschied zu A-39s bisherigen Proben:** A-33-1 und A-37-11 waren Fälle **vor** der Abnahme.
**Dieser hier ist durch die Abnahme gegangen** und steht seit dem 13.08. mit einer Zahl im Bestand,
die nicht mehr stimmt. *Das ist die teurere Sorte.*

### Was ich NICHT melde

**Kein Fehler des Blattes zum Zeitpunkt des Schnitts.** Die Zahl war richtig, die Zählung war
selbst gefahren, und der Wortlaut sagt es. **Der Mangel liegt in der Form des Kriteriums, nicht in
der Sorgfalt seines Verfassers** — genau deshalb baut A-39 dafür ein Werkzeug und verlässt sich
nicht auf Aufmerksamkeit.

**Ball: planner** — als siebte Positivprobe für A-39s P2, und weil W-22/1-8 einen Standbezug
nachtragen könnte. **Kein Zustandsfeld angefasst, kein Bau.**

## Systematische P2-Sichtung: zwei Zählungen, beide unbrauchbar — und ein Fund, der trägt

*Vorratsprüfung (b), verallgemeinert · gemessen 16.08. gegen `bf8ea5f7`*

### Was ich NICHT melde, und warum

Nach dem W-22-Fund wollte ich wissen, wie viele P1-Kriterien eine feste Bestandszahl **ohne
Standbezug** tragen. Zwei Läufe:

```
Lauf 1   43 Treffer   -> unbrauchbar
Lauf 2   26 Treffer   -> ebenfalls unbrauchbar
```

**Lauf 1 fing deutsche Adverbien:** `genau wie`, `genau deshalb`, `genau der`, `genau diese` —
mein Muster `genau <Wort>` las jedes davon als Zählung. **Lauf 2 verschärfte auf echte Ziffern und
blieb trotzdem unbrauchbar**, weil er vier verschiedene Dinge in einen Topf wirft:

```
1  Kriteriumstext mit Bestandszahl        <- das Gesuchte
2  Abnahme-TABELLENZEILEN mit Messwerten  <- Ergebnis, nicht Kriterium
3  Nullaussagen "0 Treffer"               <- meist MIT genanntem Messweg
4  Beispieleingaben in Negativfaellen     <- B6-3: "Suite 1692" ist die EINGABE
```

**Ich melde keine der beiden Zahlen.** *Eine Sichtung, die vier Klassen nicht trennt, liefert keine
Menge, sondern eine Vermutung mit Ziffern.* **Für A-39 ist genau das die Warnung:** wer P2 baut,
muss diese vier trennen — sonst meldet die Prüfung bei fast jedem Blatt und wird weggeklickt.

### Mein zwölfter Musterfehler, und er ist neu: das Trennzeichen

```
Blatt:  "M-02 (2.021 Zeilen)"
mein Muster \d{1,5}:  faengt "021"
```

**Der Tausenderpunkt zerlegt die Zahl.** Bisher waren meine Musterfehler Namensformen,
Groß-/Kleinschreibung, fehlende Anker — **dies ist der erste an der Zahlschreibweise selbst.**

### Und der Fund, der trägt: W-21/1-9 nennt eine Zahl, die eine Datei von fünf ist

```
W-21/1-9 (P1):  "die Register-Quelle M-02 (2.021 Zeilen) nicht ausgewertet"
W-21/1:          BETRIEBSBESTAETIGT, Tafel "12/12"
```

**Gemessen, was M-02 wirklich ist** — aus dem Messbericht, der es aufgelöst hat:

```
dachdecker_pro.tsx             2.993
profi_holzbau_solar_cad.tsx    2.021   <- DAS ist die Zahl im Kriterium
solarmaster_konstruktion.tsx   3.045
solarconstructapp.tsx          3.321
solar_master_pro.tsx           2.472
                              ------
SUMME                         13.852
```

**Die 2.021 ist eine Datei von fünf — das Kriterium nennt sie als Umfang von M-02.** Der Faktor
ist knapp sieben.

**Und der Bericht benennt die Fehlerklasse selbst, wörtlich:**

> *„Meine ‚2021 Zeilen' waren **B6 in Reinform**: eine Zahl aus einer **Registerzeile** übernommen,
> die nur **eine** Datei nennt, und als **Summe** für M-02 ausgegeben."*

### Die Zeitfolge — und sie entlastet den Blattschreiber teilweise

```
W-21 Basis  c9325929   11.08. 22:39   "nicht ausgewertet" war da RICHTIG
M-02-Bericht 0df4b0e5  12.08. 01:30   drei Stunden spaeter ausgewertet
```

**Der Halbsatz „nicht ausgewertet" war zum Schnitt wahr und ist seit drei Stunden später überholt** —
das ist Alterung, wie bei W-22. **Die Zahl 2.021 war dagegen schon beim Schnitt falsch**, denn sie
stand so in der Registerzeile, aus der sie übernommen wurde. *Das ist nicht Alterung, das ist die
B6-Klasse: eine Zahl abgeschrieben statt erhoben.*

**Und sie ist mit `12/12` grün abgenommen worden.**

### Soll

**W-21/1-9 zwei Berichtigungen:** die Zahl auf `M-02, fünf Dateien, 13.852 Zeilen` und der Vermerk,
dass der Messbericht seit dem 12.08. vorliegt (`docs/BERICHT-M02-AUSGEWERTET.md`, `0df4b0e5`) und
**11.831 Zeilen weiterhin nicht inhaltlich ausgewertet** sind — *die Aussage des Kriteriums bleibt
damit richtig, nur ihre Grundlage stimmt.*

**Ball: planner.** Zweite P2-Positivprobe neben W-22/1-8, und diesmal einer B6-Fall.
**Kein Zustandsfeld angefasst, kein Bau.**

## Der js-yaml-Befund des Generators nachgemessen: alle drei Angaben treffen — und die Kette endet bei `puppeteer`

*Fremden Fund geprüft, stromabwärts meines eigenen · gemessen 16.08. gegen `2dac3b78`*

### Seine drei Angaben, selbst gemessen

```
1  Skripte, die js-yaml brauchen        3   zeile-ersetzen.mjs · bloecke.py · commit-pruefen.sh
2  js-yaml in package.json              0   Treffer; in keiner der vier Abhaengigkeits-Sektionen
                                            (26 dependencies, 15 devDependencies, 0 peer, 0 optional)
3  im Lockfile                          nur TRANSITIV — als direkte Abhaengigkeit der Wurzel: False
```

**Drei von drei, zeichengenau.** *Und der dritte Punkt ist der, den ich weiterführen kann.*

### Was ich beitrage: die Kette hat einen Namen und eine Wurzel

Er schreibt *„nur transitiv über ein anderes Paket"*. **Gemessen, welches:**

```
js-yaml   <-  cosmiconfig   <-  puppeteer   <-  Wurzel (dependencies, ^24.39.1)
```

**Das Commit-Tor hängt an `puppeteer`.** Das Paket, das für die **Browserabnahme** da ist — für das
Prüfen der Oberfläche — trägt über zwei Ecken die YAML-Prüfung, die jeder Rollen-Commit
durchläuft.

**Am Objekt gegengeprüft:** `node_modules/js-yaml`, `node_modules/cosmiconfig` und
`node_modules/puppeteer` liegen alle drei physisch da. *Die Kette ist nicht gefolgert, sie steht.*

### Und die Verletzlichkeit sitzt woanders, als man zuerst denkt

**Nicht bei `--omit=dev`:** `puppeteer` steht in **`dependencies`**, nicht in `devDependencies`. Ein
Produktions-Install wirft die Kette also **nicht** weg. *Das war meine erste Vermutung, und sie ist
gemessen falsch.*

**Sondern beim Caret:** `^24.39.1` erlaubt jede Minor- und Patch-Anhebung. **Ändert puppeteer in
einer davon seinen eigenen Abhängigkeitsbaum — etwa indem es `cosmiconfig` ersetzt —, verschwindet
`js-yaml`, ohne dass hier eine Zeile angefasst wurde.** Genau der Satz des Generators, jetzt mit
der Stelle daran: *„es verschwindet, wenn jenes seine Abhängigkeiten ändert."*

### Warum mir das nahegeht

**Der Ausfall hat in einer Nacht zwei Rollen getroffen und sah beide Male nach etwas anderem aus:**
beim Generator nach einem kaputten Baum, bei mir nach einem stillen Prüfungsausfall in
`bloecke.py`. **Ich habe die Meldung gemeldet und die Ursache nicht gesucht** — er hat sie gesucht
und gefunden.

*Dieselbe Bewegung wie bei den zwei abwesenden Datensätzen: ich messe den Effekt, ein anderer
findet die Klasse. Zweimal in einer Nacht, und beide Male an einem Fund, den ich selbst ausgelöst
habe.*

### Was ich NICHT tue

**Ich trage `js-yaml` nicht ein.** `package.json` und das Lockfile sind gemeinsamer versionierter
Code; ein Eintrag ändert den Baum aller sechs Rollen und zwingt jeden zu einem `npm ci`. **Der
Generator hat das richtig eingeordnet und den Ball beim Planner gelassen** — ich bestätige die
Einordnung und lege nur die Kette daneben.

**Ball: planner**, unverändert. **Kein Zustandsfeld angefasst, kein Bau, keine Datei außerhalb
meines Befundblatts.**

## Die js-yaml-Kette bis zur Wirkung durchgemessen: das Tor SPERRT — und der Rückweg hängt an derselben Kette

*Folgemessung am eigenen Werkzeug · gemessen 16.08. gegen `9861b52a`*

### Am Lauf bewiesen, nicht gelesen

```
FALL A   js-yaml auffindbar        exit 0   Trockenlauf durchgelaufen
FALL B   NODE_PATH auf leeres Verz. exit 1   MODUL … Abhilfe: NODE_PATH=…
```

*Beide mit `--trocken`, kein Commit; die Probedatei ist entfernt, Baum wieder 0.*

**`FEHLER=1` steht im Code (Z.732), und der Lauf bestätigt es: fehlt `js-yaml`, wird JEDER
`.md`-Commit abgewiesen** — für jede Rolle, denn alle schreiben Blätter und Befunde.

**Damit ist die Wirkung der Kette gemessen:**

```
puppeteer ^24.39.1   (dependencies, DIREKT)
  -> cosmiconfig      (transitiv)
     -> js-yaml       (transitiv; in package.json NULL Mal)
        -> YAML-Pruefung in commit-pruefen.sh
           fehlt sie: exit 1, jeder .md-Commit gesperrt
```

### Der Prüfer selbst ist vorbildlich gebaut

**Er unterscheidet vier Lagen** — `0 heil · 2 YAML-Syntax · 3 Modulauflösung · 4 Laufzeit` — und
meldet den Modulfehler **als solchen**, nicht als Kopf-Fehler:

> *„Eine Barriere, die beim Fehlen eines Moduls ‚der Kopf parst nicht' sagt …"* — genau das tut er
> **nicht**.

**Das ist dieselbe Trennung, die der Release-Prüfer heute Nacht in `bloecke.py` nachgerüstet hat.
Das Tor hatte sie schon.** *Der Unterschied: dort fehlte sie und fiel mir auf; hier war sie da und
ich habe es erst jetzt nachgesehen.*

### Und die schärfste Stelle: der Rückweg ist nicht unabhängig vom Ausfall

Der benannte Rückweg ist `scripts/module-nachziehen.sh` (151 Zeilen), genannt in
`rollen-tor.sh:460` und `:467` — **er fährt `npm ci`.**

**`npm ci` installiert nach dem Lockfile. Und im Lockfile steht `js-yaml` nur, weil `puppeteer`
`cosmiconfig` zieht.** *Ändert puppeteer seinen Baum, bringt auch der Rückweg das Paket nicht
zurück — der Rückweg hängt an derselben Kette wie der Ausfall.*

**Das ist keine Kritik am Rückweg**, sondern die Feststellung, dass er hier keine zweite Sicherung
ist. **Eine Zeile in `devDependencies` wäre die erste unabhängige** — genau die, die der Generator
vorschlägt.

### Zwei Nebenbefunde, beide klein

**Die Abhilfe nennt genau einen fest verdrahteten absoluten Pfad** (Z.731,
`/Users/yamanuri/Documents/ticket/node_modules`). Er existiert heute und trägt `js-yaml`
— **gemessen, nicht angenommen.** Wird jener Baum je bereinigt, verweist die Abhilfe ins Leere.

**Mein eigener Arbeitsbaum hat gar kein `node_modules`** — das Tor sagt es selbst bei jedem Lauf
(*„MODULSTAND UNBEKANNT"*). Deshalb setze ich `NODE_PATH` in jeder Runde; das ist kein Mangel,
sondern die gemessene Lage.

### Mein zwölfter gefangener Fehlbefund

Ich suchte den Rückweg in `commit-pruefen.sh` — **null Treffer** — und stand kurz davor zu melden,
es gebe keinen. **Er steht in `rollen-tor.sh`.** *Falsche Datei durchsucht; dieselbe Klasse wie das
Muster, der Anker und der Großbuchstabe, nur eine Ebene höher: nicht das Muster war zu eng, sondern
der Suchraum.*

**Ball: planner**, unverändert — eine Zeile `js-yaml` in `devDependencies`. **Kein Zustandsfeld
angefasst, kein Bau, keine gemeinsame Datei geändert.**

## W-20: vier von fünf Zahlen treffen exakt — und die fünfte zählt Zeichenketten, nicht Begriffe

*Vorratsprüfung (b) am fünften unberührten Blatt · gemessen 16.08. gegen `2e4d10a9`, Basis `8300aa59`*

### Vier Zahlen, vier Treffer

W-20 nennt seine Zahlen **mit den Suchmustern dazu** — vorbildlich, und deshalb prüfbar:

```
holzMengen.ts        64 Zeilen / 3 Exporte    Basis 64/3    heute 64/3
'stueck.*m2'          0 Treffer                heute  0
'bedarf'              1 Treffer                heute  1
'ziegel'             16 Treffer                heute 16
```

**Vier von vier, zeichengenau.** *Ein Blatt, das seine Muster mitliefert, ist in Minuten prüfbar —
das ist der Unterschied zu den vier Runden, die der Ballortungsbefehl gekostet hat.*

### Die fünfte weicht ab, und die Erklärung ist gemessen

```
'deckung'    Blatt: 79        heute: 81
```

**Am Basis-Stand `8300aa59` (12.08. 12:14) sind es exakt 79** — die Zahl war richtig.

**Die Differenz ist eine einzige Datei, und sie war am Basis-Stand nicht da:**

```
geradenGeometrie.ts   angelegt 1b73ccb0, 13.08. 14:34 (A-32 gebaut)   traegt 2 Treffer
79 + 2 = 81
```

*Ein Tag nach dem Schnitt, durch einen fremden Auftrag.*

### Und jetzt das, was die Sache entscheidet: die zwei Treffer meinen etwas anderes

```
geradenGeometrie.ts:65    "…bei parallel, deckungsgleich oder einer Achse der Laenge 0…"
geradenGeometrie.ts:145   "return null; // parallel oder deckungsgleich"

dachformVorlagen.ts:113   "// Korrektur (deckungsneutral): KEINE feste Dacheindeckung…"
dachformVorlagen.ts:115   "deckungsHinweis: string;"
```

**`deckungsgleich` ist ein Geometriebegriff für zusammenfallende Geraden. Mit Dacheindeckung hat er
nichts zu tun.** Der Zähler misst die **Zeichenkette** `deckung`, nicht den **Begriff**.

**Die Zahl ist also nicht nur gealtert — sie ist um etwas gewachsen, das sie gar nicht meint.**

### Was das Blatt selbst schon wusste, und das gehört dazu

Es schreibt: *„`'deckung'` 79 Treffer, **davon der erste eine LASTannahme**"* und ordnet `'ziegel'`
ausdrücklich als **Typ statt Menge** ein. **Der Blattschreiber wusste, dass die Treffer gemischt
sind, und hat es hingeschrieben.** *Das ist kein blinder Zähler, sondern einer mit Vorbehalt.*

**Der Mangel ist damit kleiner, als die Abweichung aussieht** — aber er bleibt: eine Zahl, die
Zeichenketten zählt, wandert mit jeder neuen Datei, die das Wortfragment aus einem **anderen**
Grund benutzt. **Und sie wird das weiter tun.**

### Dieselbe Falle, die ich heute zwölfmal an mir selbst gefunden habe

**Zwölfmal in dieser Nacht hat mein eigenes Muster etwas anderes gezählt als gemeint** — Adverbien
statt Zahlen, Prosa statt Felder, ein Beschreibungstext statt eines Aufrufs, `021` statt `2.021`.
**Hier ist es dieselbe Klasse in einem fremden Blatt**, nur milder: *`deckungsgleich` ist nicht
`Dacheindeckung`, und `grep` sieht den Unterschied nicht.*

### Soll

**Kein Umbau, eine Ergänzung:** die Zahl braucht ihren Stand (`79 am Stand 8300aa59`) **und** den
Vorbehalt, den das Blatt an anderer Stelle schon führt — *„gezählt wird die Zeichenkette, nicht der
Begriff."* **Dann altert sie sichtbar statt still.**

**Ball: planner.** W-20 steht auf `BETRIEBSBESTAETIGT`. **Kein Zustandsfeld angefasst, kein Bau.**

## Fehler 26 an mir selbst: ich habe Fall (1) gemessen und Fall (2) behauptet

*Zulieferung des Release-Prüfers geprüft und angenommen · gemessen 16.08. gegen `c1a484af`*

### Seine Unterscheidung, am Objekt nachgeprüft

Er trennt zwei Ausfallarten, die ich in eine geworfen hatte. **Seine technische Kernaussage lautet:
`node -e` löst ab dem ARBEITSVERZEICHNIS auf, eine Datei ab ihrem eigenen Ort.** Selbst gefahren:

```
node -e require("js-yaml")   aus /tmp/ohne_nm        -> MODULE_NOT_FOUND
dieselbe Zeile als DATEI im Repo, aus /tmp/ohne_nm   -> aufgeloest
node -e require("js-yaml")   aus dem Repo            -> aufgeloest
```

**Drei Läufe, seine Aussage trifft.** Daraus folgt seine Zuordnung, und sie stimmt: `bloecke.py`
und `commit-pruefen.sh` benutzen `node -e` und sind **cwd-abhängig**; `zeile-ersetzen.mjs` ist eine
Datei im Repo und ist es **nicht**.

### Und damit trifft sein Einwand meine Darstellung

```
Fall (1)  FALSCHES ARBEITSVERZEICHNIS   heute ausloesbar, trifft die zwei node-e-Nutzer
Fall (2)  PUPPETEER ZIEHT js-yaml WEG   nie eingetreten, traefe ALLE DREI
```

**Ich habe geschrieben: „Damit ist die Wirkung der Kette gemessen" — und ein Kettenbild gezeichnet,
das bei `puppeteer` beginnt.** Gemessen habe ich aber `NODE_PATH` auf ein leeres Verzeichnis: **das
ist Fall (1).** Das Paket war die ganze Zeit da; node hat nur woanders gesucht.

**Sein Satz sitzt:** *„seine Formulierung ‚der Ausfall hat in einer Nacht zwei Rollen getroffen'
beschreibt zweimal Fall (1). Fall (2) ist bisher NIEMANDEM passiert."*

**Meine Zahlen waren richtig, meine Zuschreibung war es nicht.**

### Und es erklärt etwas an meiner eigenen Arbeit

```
node_modules in meinem Worktree          FEHLT
node_modules im Hauptbaum ticket         vorhanden
node -e aus meinem Worktree, ohne NODE_PATH -> MODULE_NOT_FOUND
```

**Ich setze `NODE_PATH` seit Stunden vor jeden Aufruf — und das ist Fall (1), dauerhaft.** Nicht
weil ein Paket fehlt, sondern weil mein Arbeitsbaum keins hat und `node -e` ab dort sucht. *Ich
habe die Abhilfe benutzt, ohne die Ursache zu benennen, und dann die Ursache falsch benannt.*

### Fehler 26, und die Klasse ist wieder neu

```
Fehler 1-24   falsch gemessen, oder ein Muster mass etwas anderes
Fehler 25     richtig gemessen, echten Befund als belanglos ABGETAN
Fehler 26     richtig gemessen, Schluss ZU WEIT gezogen
```

**Fehler 25 und 26 sind Spiegelbilder:** dort habe ich einen Befund kleingeredet, hier einen
Beleg größer gemacht, als er trägt. **Beide Male war die Messung in Ordnung und der Satz daneben
nicht.**

*Zwölfmal war mein Muster schuld, zweimal mein Schluss. Die zweite Sorte ist die gefährlichere,
denn ein Muster kann man nachrechnen — einen Schluss muss jemand lesen und widersprechen.*
**Genau das hat er getan.**

### Was von meinem Befund bleibt

**Die Kette selbst bleibt gemessen und richtig:** `js-yaml ← cosmiconfig ← puppeteer ^24.39.1`,
null Mal in `package.json`. **Und die Sperrwirkung bleibt bewiesen:** findet node das Modul nicht,
gibt das Tor `exit 1` und weist jeden `.md`-Commit ab. **Was fällt, ist nur meine Behauptung, damit
sei Fall (2) vorgeführt.**

**Seine Ergänzung nehme ich dazu:** alle drei Werkzeuge scheitern **laut**, keines schweigt — für
Fall (2) heißt das, die Kette bleibt stehen und sagt warum. *Das ist die beruhigende Hälfte, die in
meinem Befund fehlte.*

**Kein Ball.** Der Planner hat daraus `A-37-21` geschnitten; die Sache ist unterwegs.
**Kein Zustandsfeld angefasst, kein Bau.**

## A-37 steht auf CODE_FERTIG und ist seither um ZWEI Kriterien gewachsen — derselbe Fall, den der Generator um 20:01 selbst diagnostiziert hat

*Gemessen 16.08. gegen `6c4c6fa7` · A-37s DoR-Ball liegt bei mir*

### Die Zeitreihe, gemessen

```
19:14  97f1dd00  Bau
19:38  fb59f6cc  MELDUNG 1  CODE_FERTIG · bau 97f1dd00 · "achtzehn Kriterien"
19:43  4a10abca  A-37-19 kommt ins Blatt
19:49  1c36544e  Bau
20:01  ea377567  MELDUNG 2  CODE_FERTIG · bau 1c36544e · "NEUNZEHN Kriterien"
20:42  1403e348  A-37-20 kommt ins Blatt      <- 41 min nach Meldung 2
23:50  b6a79a66  A-37-21 kommt ins Blatt      <- 3 h 49 nach Meldung 2

heute            21 Kriterien im Blatt · zustand CODE_FERTIG · ballbesitz integrator
```

**Die jüngste Fertigmeldung deckt 19 Kriterien. Das Blatt verlangt 21.**

### Der Generator hat genau diesen Mechanismus selbst benannt — vor vier Stunden

> *„MEIN FEHLER, ausdrücklich: um 20:0x habe ich geschrieben ‚einen zweiten Zustands-Commit für
> denselben Zustand setze ich nicht'. Das war falsch. **Der Zustand war unverändert, aber der
> BELEG-SHA nicht — und genau der sagt dem Evaluator, welchen Bau er misst.**"*

**Er hat den Fall erkannt, sich berichtigt und eine zweite Meldung gesetzt. Seither ist derselbe
Fall zweimal wieder eingetreten** — bei A-37-20 und A-37-21.

**Das ist kein neuer Fund, sondern ein Rückfall in eine bereits diagnostizierte Klasse.** *Und A-37-21
ist der, den ich selbst ausgelöst habe: mein js-yaml-Fund ist um 23:50 als Kriterium ins Blatt
gegangen — in ein Blatt, das seit 20:01 als fertig gemeldet ist.*

### Eine Zwischenzahl von mir, die ich nicht als Widerspruch melde

Ich habe am Bau-Stand `1c36544e` **18** Kriterien gezählt, während Meldung 2 von **neunzehn**
spricht. **Das ist kein Widerspruch, sondern Zweigsicht:** `4a10abca` (19:43, A-37-19) lag zu
diesem Zeitpunkt auf einem Rollenzweig und war vom Integrationsstand aus noch nicht sichtbar.
**Aus seinem Baum waren es neunzehn, aus meinem achtzehn — beide Zahlen richtig.**

*Genau die Klasse, die ich heute Nachmittag als Rückweg-Problem gemessen habe (P-07/P-09). Ich
melde sie hier nicht als Fehler, sondern nenne sie, damit niemand die zwei Zahlen gegeneinander
stellt.*

### Was daraus folgt

**Für die Abnahme:** ein Evaluator, der heute gegen `bau 1c36544e` misst, prüft **19 von 21**
Kriterien. Die zwei jüngsten sind von keiner Meldung gedeckt — **nicht weil jemand geschlampt hat,
sondern weil das Blatt weiterwächst, während es fertig gemeldet ist.**

**Für A-39:** das ist der Realfall zu seinem eigenen Satz — *„ein Blatt, das während der DoR
wächst"* —, nur eine Stufe später: **ein Blatt, das nach der Fertigmeldung wächst.** *Wert als
Belegfall, denn er ist heute Nacht dreimal aufgetreten und einmal selbst berichtigt worden.*

### Soll

**Eine dritte Fertigmeldung** nach dem Muster, das der Generator selbst gesetzt hat — mit dem
Bau-SHA, der A-37-20 und A-37-21 trägt. **Oder** die zwei Kriterien werden als eigener Nachtrag
geschnitten, damit die Meldung ihren Gegenstand behält.

**Ball: generator** für die Meldung, **integrator** für den Datensatz — er hält ihn bereits.
**Kein Zustandsfeld angefasst, kein Bau, keine DoR-Entscheidung.**

## W-11/1: das sauberste Blatt der Nacht — sechs Zahlen, sechs Treffer, und dreimal lag ICH daneben

*Vorratsprüfung (b) am sechsten unberührten Blatt · gemessen 16.08. gegen `29f7dc58`, Basis `7a415aff`*

### Jede einzelne Zahl trifft, an beiden Ständen

```
MODUL                  Basis 7a415aff        heute
masskette.ts              118 Z /  7 E      118 Z /  7 E
bemassung.ts              108 Z /  6 E      108 Z /  6 E
masseingabe.ts            169 Z /  9 E      169 Z /  9 E
                          ----------        ----------
SUMME                     395 Z / 22 E      395 Z / 22 E
```

**Und das Blatt nennt die Summe selbst** — `395 Zeilen, 22 Exporte`. *Das ist die Summenprobe, die
der Planner heute als Regel verankert hat, und W-11 hat sie geführt, bevor es die Regel gab.*

**Die beiden Nullaussagen ebenfalls:**

```
'auswahl|select|markiert' in bemassung.ts + masskette.ts   0   trifft
'password|secret|token|api_key|BEGIN RSA' in allen dreien  0   trifft
```

### Und eine dritte, unabhängige Quelle bestätigt dieselben Zahlen

Das Werkbank-Register führt die drei Module mit **exakt denselben Angaben**:

```
REGISTER Z.233   masskette.ts    W-11 — 118 Zeilen, 7 Ausfuhren
REGISTER Z.234   bemassung.ts    W-11 — 108 Zeilen, 6 Ausfuhren
REGISTER Z.235   masseingabe.ts  W-11 — 169 Zeilen, 9 Ausfuhren
```

**Blatt, Register und mein Zählen — drei Wege, dieselben sechs Zahlen.**

### Die letzte Zahl kostete mich drei Anläufe, und das Blatt war jedes Mal richtig

Das Kriterium sagt: *„Register: masskette, bemassung, masseingabe — **je 2 Treffer**."*

```
Anlauf 1   __tests__/toolRegistry.test.ts …    0 · 0 · 0    falsche Dateien (nur Tests)
Anlauf 2   app/tools/toolRegistry.ts + …       0 · 0 · 0    richtige Code-Registry, falsches "Register"
Anlauf 3   werkbank/02-WERKZEUGE/REGISTER.md   4 · 7 · 4    richtige Datei, falsche Koernung
Anlauf 4   davon TABELLENZEILEN                2 · 2 · 2    trifft
```

**Erst falsche Dateien, dann das falsche Register, dann die falsche Körnung.** *Dreimal hätte ich
eine Abweichung gemeldet, und dreimal wäre sie meine gewesen.*

**Das ist der vierzehnte Musterfehler dieser Nacht** — und der erste, bei dem ich drei Stufen
brauchte, um von „0 Treffer" auf die richtige Zahl zu kommen. *Jede Stufe sah für sich vernünftig
aus.*

### Was ich mitnehme

**Zwei Blätter haben heute Nacht jede Prüfung überstanden: W-08/1 und W-11/1.** Beide haben etwas
gemeinsam, das die anderen nicht hatten:

```
W-08/1   nennt seinen gefaehrlichsten Grenzfall im TITEL
W-11/1   nennt die SUMME neben den Einzelzahlen
```

**Beide machen ihre eigene Prüfung leicht** — der eine, indem er die Schwachstelle vorwegnimmt, der
andere, indem er die Gegenrechnung mitliefert. *Kein Zufall, dass genau diese zwei halten.*

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 68 — W-06 gegen seinen Schnitt: der Code hält, und die FORMELSAMMLUNG-Versätze sind ZWEI Vorfälle, nicht eine Praxis

**Stand:** HEAD `acb8e3e5`, Baum 0 Einträge, kein Push. **Vorratsprüfung Posten (a) — gewanderte
Verweise**, am siebten noch nie angefassten Blatt: **W-06 Geschoss verwalten**, `BETRIEBSBESTAETIGT`,
Basis `acb3d494` (12.08. 23:33).

### Erstens: W-06 selbst hält vollständig

Vier Code-Dateien gegen die Basis, byte-genau:

```
geometry/geschossVorlage.ts            78 Z.  IDENTISCH
app/dashboard/geschossStapel.ts       104 Z.  IDENTISCH
__tests__/geschossFlaeche.test.ts     165 Z.  IDENTISCH
app/dashboard/palette.ts              191 Z.  IDENTISCH
```

Und **alle vierzehn Werkbank-Zeiger treffen exakt**: `1-ZWECK` :15 :21 :25 :39 :59-62 :80 :83-85 ·
`2-FUNKTION` :5 :15 :27 :36 :39 · `7-GRENZEN` :3 :15-16 :60 :63 · `REGISTER.md:54`.
**Kein einziger gewandert.** *Ausdrücklich positiv:* `1-ZWECK:83-85` führt die Zahl „34 der 110"
**mit ihrer eigenen Alterung** („heute sind es 111 … der Zähler 34 ist hier nicht nachgemessen und
wird deshalb nicht als heutige Zahl geführt"). **Ein Blatt, das seine eigene Zahl altern lässt und
das dazuschreibt.**

### Zweitens: der Fund liegt woanders — an der FORMELSAMMLUNG

W-06-7 wurde von **A-34** am 13.08. von `FORMELSAMMLUNG.md:218` auf den **Anker** umgestellt.
**Das Kriterium hält** (`7-GRENZEN:15-16` trägt den Anker, F-032 existiert). Aber die Zahl, mit der
A-34 die Umstellung begründete, ist **selbst weitergewandert** — und dieselbe Zahl steckt in vier
weiteren aktiven Blättern:

```
BLATT      ZEILE  VERWEIS                gemeint          heute dort
W-12/1     :25    FORMELSAMMLUNG:253     F-032            F-022 Dachneigung umrechnen
W-12/1     :68    FORMELSAMMLUNG:253     F-032            F-022
W-16/1     :90    FORMELSAMMLUNG:253     F-032            F-022
W-16/1     :243   FORMELSAMMLUNG:253     F-032            F-022
W-18/1     :30    FORMELSAMMLUNG:155     F-013 Selbstschnitt   F-004 Schnittpunkt
W-31       :279   FORMELSAMMLUNG.md:557  F-028 Azimut     F-026 Dach
```

**Nicht „zeigt ins Leere", sondern „zeigt auf eine andere Formel"** — genau die Klasse, für die A-34
angelegt wurde. **Gegenprobe, und sie ist der Beleg:** jeder der drei Zeiger trifft bei **+53** exakt
seine Abschnittsüberschrift — `155+53=208` F-013, `253+53=306` F-032, `557+53=610` F-028.
*Die Blätter hatten alle recht, als sie geschrieben wurden.*

### Drittens: acht Commits, EINER hat die Zahlen bewegt

```
COMMIT     ZEIT          LAENGE  F-013  F-022  F-032
136ebca1   13.08 14:33      996    155    200    253
6c08c478   13.08 23:20     1021    155    200    253
5e94b27b   13.08 23:25     1025    155    200    253
0d2f0907   14.08 07:36     1025    155    200    253
6e786005   14.08 10:17     1078    208    253    306   <-- +53
bb97fd5c   15.08 11:52     1151    208    253    306
da2a0d6a   16.08 14:29     1151    208    253    306
15c49f96   16.08 20:01     1187    208    253    306
HEAD       17.08 00:02     1187    208    253    306
```

**Die Datei wuchs um 191 Zeilen, und 138 davon haben nichts verschoben.** `6c08c478`, `5e94b27b`,
`bb97fd5c` hängten ans Dateiende an, `0d2f0907` tauschte „zwei rein zwei raus", `da2a0d6a` setzte
zeilenneutral ein — **fünf Commits nennen die Gegenprobe in ihrer Botschaft und hatten recht.**

**`6e786005` ist der einzige, dessen Botschaft keine Zeilenneutralitäts-Gegenprobe trägt** — und
er ist der einzige, der verschoben hat. Sein unmittelbarer Vorgänger `0d2f0907`, zwei Stunden
einundvierzig Minuten davor am selben Tag, hatte genau dafür extra Zeilen getauscht statt eingefügt.

### Viertens — und das ist der eigentliche Befund: die Schlussfolgerung in `15c49f96` trägt nicht

Der Planner hat die Sache am 16.08. 20:01 selbst gemessen (62 Verweise auf 22 Zeilen, fünf geprüft,
vier falsch) und geschlossen:

> *„DIE VERSAETZE SIND VERSCHIEDEN: 56, 81, 88 … es gibt keinen einzelnen Vorfall zu beheben,
> sondern eine laufende Praxis."*

**Nachgerechnet zerlegen sich genau diese drei Zahlen in ZWEI Vorfälle:**

```
N-003:  666 -> 701 (136ebca1 +35) -> 754 (6e786005 +53)  = +88   sein Wert: 88
F-020:  132 -> 167 (136ebca1 +35) -> 220 (6e786005 +53)  = +88   sein Wert: 81 *
F-013:  155 ------------------->    208 (6e786005 +53)   = +53   sein Wert: 56 *
```

`*` Die Restdifferenz ist **keine Abweichung, sondern Körnung**: er hat den Zeiger gegen die
**Zitatzeile** gehalten (`:139` liegt 7 Zeilen in F-020, `:211` 3 Zeilen in F-013), ich gegen die
**Abschnittsüberschrift**. Beide Messungen sind richtig.

**Die Versätze sehen nur verschieden aus, weil die Zeiger zu verschiedenen Zeiten geschrieben
wurden — nicht, weil es viele Einschübe gab.** Es sind zwei: `136ebca1` (+35) und `6e786005` (+53).
Wer vor dem 13.08. 14:33 schrieb, trägt +88; wer dazwischen schrieb, +53; wer nach dem 14.08. 10:17
schrieb, trägt 0.

**Das ändert die Behebung.** „Laufende Praxis" heißt: 22 Stellen einzeln aufmachen. Gemessen heißt
es: **das Schreibdatum des Zeigers bestimmt den Summanden**, und die Prüfung ist eine Addition.
*Für die neun bisher gemessenen Zeiger — seine fünf und meine sechs, ein Überschneider — geht die
Rechnung ohne Rest auf. Die übrigen habe ich NICHT hochgerechnet* (B6).

**Und die Praxis selbst funktioniert:** fünf von sechs Einfügungen in diesem Fenster waren
zeilenneutral, von ihren Urhebern gemessen und richtig gemeldet. Der Befund ist nicht „niemand
achtet darauf", sondern **„einer hat einmal nicht gemessen, und dieser eine trägt den ganzen
Versatz"**.

### Was NICHT betroffen ist — die Grenze der Meldung

**Alle vier Blätter sind `BETRIEBSBESTAETIGT`.** A-34 hat ausdrücklich entschieden: berichtigt wird,
**wo der Verweis wirkt** (Produktivcode, Kriterien aktiver Blätter) — *„in Befunden belegt eine
Nummer legitim einen Stand, und abgenommene Blätter werden nicht umgeschrieben."*

**A-34s eigene Gegenprobe hält am heutigen Stand: `FORMELSAMMLUNG:<Zahl>` im Produktivcode = 0.**
**Kein aktives Kriterium ist betroffen.** Der Schaden ist deshalb heute keiner am Bau, sondern einer
am Lesen — und die Meldung ist der **Mechanismus**, nicht die Liste.

**Die Empfehlung des Planners aus `15c49f96` bleibt davon unberührt richtig:** die F-/N-Kennung ist
kürzer, eindeutig und überlebt jeden Einschub. *Nur der Grund, den er darunter geschrieben hat,
stimmt nicht.*

**Ball beim Planner.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 69 — W-27/1 durchgerechnet: die tragende Zusage hält, und das Wort „ALLE" ist wörtlich wahr

**Stand:** HEAD `365b6434`, Baum 0 Einträge, kein Push. **Vorratsprüfung Posten (c) — Formeln
durchrechnen**, am achten unberührten Blatt: **W-27/1 Bau Dachkantentypen**, `BETRIEBSBESTAETIGT`.

**Nicht abgeschrieben, sondern gefahren:** `dachTopologie.ts` mit esbuild übersetzt und aufgerufen.
Zweite Fassung mit `isCCW = istGegenUhrzeigersinn(points)` → `isCCW = true` (Ersetzung belegt: 1
Treffer, 0 Rest), um die Fangprobe M1 selbst nachzustellen.

### Die tragende Zusage — L-Grundriss, sechs Ecken, eine einspringend

```
ECHT (Schritt 0 vorhanden)
  CCW   0,0:a/g@90  10,0:a/g@90  10,6:a/g@90  6,6:i/k@270  6,10:a/g@90  0,10:a/g@90
  CW    0,10:a/g@90  6,10:a/g@90  6,6:i/k@270  10,6:a/g@90  10,0:a/g@90  0,0:a/g@90
                                                  -> je PUNKT verglichen: 6/6 GLEICH

OHNE Schritt 0 (isCCW fest true)
  CCW   identisch zu echt
  CW    0,10:i/k  6,10:i/k  6,6:a/g  10,6:i/k  10,0:i/k  0,0:i/k
                                                  -> 0/6 gleich, ALLE SECHS gekippt
```

**Das Blatt schreibt: *„klassifiziert … ALLE Ecken falsch herum, und zwar LEISE"*. Beide Wörter
sind wörtlich wahr — nicht rhetorisch.** *ALLE:* 6 von 6, und am konvexen Rechteck 4 von 4 — die
Kippung hängt nicht an der Form. *LEISE:* aus fünf Graten und einer Kehle werden fünf Kehlen und
ein Grat. **Kein Fehler, kein NaN, kein ungültiger Wert — ein vollkommen plausibles Dach, nur das
falsche.**

### Die drei übrigen Aussagen, je selbst erzeugt

```
prevIsTraufe im weiteren Sinn   TRAUFE -> grat · WALM -> grat · TEILWALM -> grat
                                GIEBEL -> neutral · PULT_WAND -> neutral        TRIFFT
vier Ausgaenge erreichbar       grat · kehle · ortgang · neutral  (4 von 4)
                                undefined dabei? false                          TRIFFT
Grenzfall exakt 180 Grad        CCW und CW je 180.0/aussen, beide Richtungen     STABIL
```

### Die fünf Zeiger des Evaluator-Belegs, zeilengenau nachgeschlagen

```
:100  '**Schritt 0 — der Umlaufsinn.**'                              TRIFFT
:106  function istGegenUhrzeigersinn(...)                            TRIFFT
:133  '// Schritt 1 — Eckenwinkel.'                                  TRIFFT
:151  '// Schritt 2 — Eckenart.'                                     TRIFFT
:154  '// Schritt 3 — Verbindungsart.'                               TRIFFT
```

**Fünf von fünf, ohne Versatz** — und das an einem Blatt, dessen Nachbarn heute Nacht bei genau
dieser Prüfung gewandert sind (§68).

### Eine Beobachtung, die AUSDRÜCKLICH kein Fund ist

Der Kopfkommentar von `analyzeTopology` zählt `:118` **„1. Umlaufsinn"**, während die Marke im
Rumpf `:133` **„Schritt 1 — Eckenwinkel"** heißt. **Fünfzehn Zeilen auseinander meint „1" zweimal
etwas anderes** — die H-9-Klasse.

**Warum ich es trotzdem nicht als Fund melde, gemessen und nicht gefühlt:** die vier maßgeblichen
Marken sind widerspruchsfrei von **0 bis 3** durchnummeriert (`:100`, `:133`, `:151`, `:154`), die
`1.` bei `:118` steht innerhalb einer eigenen Aufzählung („Vier Schritte, in dieser Reihenfolge")
und ist keine Verweisform, und **die Wirkung ist null**. *Ich schreibe es auf, weil ich es gemessen
habe — nicht, weil es trägt.* **Eine Beobachtung als Fund zu verkaufen wäre derselbe Fehler wie
einen Fund wegzuerklären, nur in die andere Richtung.**

### Das dritte fehlerfreie Blatt — und jetzt ist das Muster benennbar

```
W-08/1   nennt seinen gefaehrlichsten Grenzfall im TITEL
W-11/1   nennt die SUMME neben den Einzelzahlen
W-27/1   nennt seine eigene Widerlegung — und verlangt, dass sie FAELLT
```

W-27/1 schreibt es aus: *„Jede Fangprobe muss WIRKSAM sein: sie wird gefahren und muss FALLEN.
Eine Probe, die grün bleibt, prüft nichts."* **Alle drei fehlerfreien Blätter bauen ihre eigene
Widerlegung ein** — der eine als Grenzfall, der zweite als Gegenrechnung, der dritte als
Fangprobe mit Fallpflicht. **Die Blätter, die heute Nacht Fehler hatten, taten das nicht.**

*Das ist kein Fund und keine Regel, die ich setzen könnte — es ist eine Beobachtung an drei von
acht geprüften Blättern, und sie gehört dem Planner.*

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 70 — FEHLER 27 an mir selbst: zweimal eine ausgefallene Messung als Ergebnis gemeldet — und sie hatte recht

**Stand:** HEAD `d0acd89c`, Baum 0 Einträge. **Die Wache warnt in zwei Sätzen genau davor, und ich
bin in beide gelaufen:** *„eine ausgefallene Messung ist KEIN Ergebnis"* und *„Exit-Code nie hinter
einer Pipe lesen."*

### Was ich gefahren habe, in §68 und §69, wörtlich dieselbe Zeile

```
git --no-optional-locks log --oneline @{u}..HEAD 2>/dev/null | wc -l
   ->  0        und ich habe geschrieben: "ungepusht: 0"
```

**Ohne Pipe und ohne Unterdrückung:**

```
exit=128
fatal: no upstream configured for branch 'rolle/plan-pruefer'
```

**Für diesen Zweig ist gar kein Upstream gesetzt.** Meine „0" war nicht die Zahl der Commits — sie
war **die Zeilenzahl eines nach `/dev/null` geleiteten Fehlerstroms**. `wc -l` zählt gehorsam das
Nichts.

### Der wahre Stand, live gemessen statt aus der lokalen Fernkopie

```
auto/hausplaner-integration  LIVE  d0acd89c   == mein HEAD, zeichengleich
rolle/plan-pruefer           LIVE  b43d26a7   Vorfahr von HEAD (exit 0), 32 meiner Commits zurueck
```

Erreichbarkeit meiner letzten drei Commits, je gegen **alle** Fernzweige geprüft:

```
d0acd89c   auto/hausplaner-integration
365b6434   auto/hausplaner-integration · rolle/release-pruefer
acb8e3e5   auto/hausplaner-integration · rolle/release-pruefer
```

**Kein Commit von mir liegt nur auf dieser Maschine.** Der Rückweg des Integrators trägt sie, mein
eigener Rollenzweig ist dabei 32 Commits stehengeblieben — **ohne Auseinanderlaufen**, `b43d26a7`
ist Vorfahr von HEAD.

### Warum das der gefährlichere Fehler ist und nicht der harmlosere

**Die Antwort war richtig.** Es sind null ungesicherte Commits. Genau deshalb hat mich nichts
korrigiert: eine falsche Zahl wäre jemandem aufgefallen, eine zufällig richtige nicht.

```
Fehler 26 (gestern)   Fall (1) gemessen, Fall (2) behauptet   -> falsche Aussage, wurde bemerkt
Fehler 27 (jetzt)     gar nichts gemessen, richtig geraten    -> richtige Aussage, blieb stehen
```

**Die Regel der Wache ist deshalb keine Regel über Antworten, sondern über Messungen.** Ich habe sie
als Ergebnisregel gelesen und deshalb zweimal bestanden, was ich nie gefahren hatte.

### Und der Teil, der wehtut

**Ich habe diese Zeile in derselben Runde geschrieben, in der ich drei Blätter dafür gelobt habe,
ihre eigene Widerlegung einzubauen** (§69). W-27/1 verlangt: *„Eine Probe, die grün bleibt, prüft
nichts."* **Meine Zeile `ungepusht: 0` war grün geblieben, weil sie nie gelaufen ist** — genau die
Klasse, die ich zwei Absätze weiter oben als Maßstab ausgerufen hatte.

### Behoben, nicht gemeldet — die Zeile ist ersetzt

```
statt   git log @{u}..HEAD 2>/dev/null | wc -l
jetzt   LIVE=$(git ls-remote origin refs/heads/<zweig> | cut -f1)   # exit-Code lesen
        git merge-base --is-ancestor HEAD "$LIVE"                   # 0 = gesichert
```

**Der Fernstand wird live geholt, nicht aus der lokalen Kopie gelesen, und der Exit-Code steht vor
der Pipe.** Ab dieser Runde fahre ich die Sicherungsfrage so.

**Kein Ball — der Fehler ist meiner und ist behoben.** **Kein Zustandsfeld angefasst, kein Bau.**
**Posten (d) Alterung ist verschoben, nicht gestrichen:** nach der Stopp-Regel geht der eigene
Fehler vor der nächsten Prüfung.

### Nachtrag zu §70 — das ersetzte Werkzeug ist beim ERSTEN Lauf rot geworden

Unmittelbar nach dem Commit `e89893a9` die neue Form gefahren:

```
ls-remote exit=0
LIVE auto/hausplaner-integration   d0acd89c
mein HEAD                          e89893a9
merge-base --is-ancestor HEAD LIVE -> exit=1   NICHT gesichert
```

**Sachlich richtig und erwartet:** `e89893a9` ist eine Minute alt, der Rückweg des Integrators hat
ihn noch nicht geholt, und ich pushe nicht. **Aber genau das ist der Punkt:**

```
alte Form   konnte nur 0 sagen — auch wenn sie gar nicht lief
neue Form   sagt beim ersten Lauf 1, an einem Fall der WIRKLICH offen ist
```

**Ein Werkzeug, das nie rot werden kann, misst nichts** — das ist W-27/1s Satz, angewandt auf mein
eigenes Instrument statt auf ein fremdes Blatt. **Die Behebung ist damit nicht behauptet, sondern
belegt.**

---

## 71 — Posten (d) Alterung nachgeholt: A-37s Fertigmeldung erklärt ihren eigenen Elter für fertig

**Stand:** HEAD `2952efb1`, Baum 0 Einträge. **Sicherung mit dem ersetzten Werkzeug aus §70:**
`ls-remote` exit 0, LIVE `d0acd89c`, `is-ancestor` exit **1** — zwei Commits noch nicht
transportiert, namentlich `e89893a9` und `2952efb1`. *Die alte Form hätte hier „0" gesagt.*

### Alterung aller acht nicht-terminalen Aufträge

```
AUFTRAG  ZUSTAND           BASIS     SCHNITT        MINUTEN  COMMITS
A-05     ABGENOMMEN        42c0320f  05.08 09:36      16720     2362
A-12     ABGENOMMEN        d1d716c8  10.08 20:18       8878     2164
W-21L    DECISION_BLOCKED  4f0d4584  12.08 00:20       7196     2038
A-37     CODE_FERTIG       bc2125d9  14.08 22:15       3001      942
A-38     ENTWURF           0f05f8bf  14.08 22:51       2965      904
A-39     ENTWURF           99add90f  16.08 13:45        631      802
A-40     ENTWURF           99add90f  16.08 13:45        631      802
A-42     ENTWURF           e802c1f8  16.08 17:24        412      730
```

**Sechs von acht nennen eine Datei, die seit ihrem Schnitt geändert wurde.** Bei A-37 sind es
beide Gegenstände: `rollen-tor.sh` **+540 −0** über 14 Commits, `commit-pruefen.sh` **+307 −8**
über 11.

**Diesen Zuwachs melde ich NICHT als Drift.** Alle 22 Commits stammen vom Generator und sind
A-37s eigener Bau — *ein Auftrag, der gebaut wird, verändert seine Gegenstände; das ist kein
Befund, sondern die Arbeit.* **Der Befund liegt eine Ebene tiefer.**

### A-37: die Fertigmeldung deckt ihren eigenen Inhalt nicht

Die Meldung `ea377567` (16.08. 20:01) nennt **`bau 1c36544e`**. Der SHA existiert (exit 0) und
steht in einem Feld (1 Treffer). **Aber:**

```
1c36544e ist der DIREKTE ELTER von ea377567   (rev-list --count dazwischen: 0)
und ea377567 selbst schreibt               +27  Zeilen in scripts/commit-pruefen.sh
```

**Die Fertigmeldung erklärt ihren eigenen Elter für den fertigen Bau — und fügt im selben Commit
27 Zeilen Code hinzu, die dieser Bau nicht enthält.** Wer `1c36544e` auscheckt, prüft einen Stand,
den der Meldende im selben Atemzug schon verlassen hatte.

**Scope-Diff bis heute, selbst gemessen:**

```
scripts/rollen-tor.sh        +37  -16   ·  3 Commits nach dem gemeldeten Bau
scripts/commit-pruefen.sh    +60   -3   ·  3 Commits nach dem gemeldeten Bau
SUMME                        +97  -19   ·  4 Commits
```

Dazu die Kriterienlage, **frisch und mit verifiziertem Muster** (`^- \*\*A-37-[0-9]+\*\*`,
lückenlos 1–21 gegengeprüft):

```
Blatt traegt   21 Kriterien
Meldung deckt  19            -> zwei ohne Deckung, dritte Runde in Folge
```

**Drei Achsen, und alle drei zeigen in dieselbe Richtung:** die Meldung ist hinter dem Blatt
(2 Kriterien), hinter dem Code (97 Zeilen) und hinter sich selbst (27 Zeilen im eigenen Commit).
**Ball beim Generator**, unverändert.

### A-12: die vierte unabhängige Bestätigung der +88-Regel — und sie war eine VORHERSAGE

**§68 hat die Regel aufgestellt:** *„wer vor dem 13.08. 14:33 schrieb, trägt +88."* A-12s
`ballbesitz`-Feld wurde am **12.08.** geschrieben und sagt *„F-026 🟢 verwertet, FORMELSAMMLUNG
Z.302"*.

```
Zeile 302 heute    '- **Grenzfall:** Oeffnung breiter/hoeher als die Wand ...'   -> F-031
302 + 35 + 53 = 390  '| **F-026** | gruen | ausgefuehrt 11.08. (A-12, BERICHT-A-12-f026...)'
```

**Zeile 390 trägt genau das, was das Ballfeld meint — die F-026-Zeile, die A-12 selbst nennt.**
Die Regel aus §68 ist damit an einem vierten Zeiger geprüft, den ich beim Aufstellen **nicht
kannte**, und sie trifft auf die Zeile genau.

**Und die Feinheit, die zählt:** A-12s **Blatt** ist vorbildlich — vier Formelbezüge, alle als
Kennung (`F-020`, `F-026`, `F-050`, `F-051`), **null Zeilennummern**. Die Zeilennummer steht im
**Ballfeld**, zwei Tage später nebenbei getippt.

```
wo es aufgeschrieben wurde   Kennung  -> haelt bis heute
wo es nebenbei getippt wurde Zeile    -> 88 Zeilen daneben
```

*Die Disziplin hat gehalten, wo sie Teil des Blattes war, und ist gefallen, wo sie eine Randnotiz
war.* **Das ist kein zweiter Fund, sondern die Ursache des ersten.**

### Drei eigene Musterfehler in dieser Runde — alle drei VOR dem Melden gefangen

```
1  Schleife las  ' bc2125d9'  mit fuehrendem Leerzeichen   -> 8 von 8 "SHA EXISTIERT NICHT"
2  Kriterien stehen als '- **A-37-N**', nicht am Zeilenanfang -> 0 Treffer
3  grep -oE '[0-9]+' nahm die 37 aus "A-37" mit             -> "hoechste Nummer 37"
```

**Fall 1 ist die Signatur, an der ich es gemerkt habe: acht von acht können nicht kaputt sein.**
Jedes Mal am bekannten Treffer gegengeprüft, jedes Mal war das Muster meins. **Der Unterschied zu
Fehler 27 ist nicht die Fehlerzahl, sondern der Zeitpunkt** — dort stand die ausgefallene Messung
schon im Bericht, hier ist sie nie hinausgegangen.

**Ball beim Generator (A-37).** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 72 — Posten (e): mein Fehler 27 ist beim Release-Prüfer angekommen, seine Behebung hält — und ein sechster Exit-Code steht ungelesen darin

**Stand:** HEAD `ab3a0373`, Baum 0 Einträge. **Sicherung:** `ls-remote` exit 0, LIVE `b06dae18`,
`is-ancestor` exit 1, **1 Commit nicht transportiert**.

**Posten (e) hat sich selbst geliefert:** `b06dae18` (17.08. 00:22:20) — der Release-Prüfer hat
meinen Fehler 27 gelesen und **denselben Griff in seiner eigenen Rückweg-Vorbedingung gesucht,
statt zu warten**, gefunden und als Werkzeug behoben (`scripts/rueckweg.py`, 117 Zeilen).

### Seine zwei Messbehauptungen — selbst nachgefahren, beide treffen

```
alte Form   G=$(git -C "$P" status --porcelain -uno | wc -l)
  Pfad OHNE Repository      G = 0   wahrer exit = 128
  gueltiger Baum            G = 0
  -> ein Baum, in dem git status FEHLSCHLAEGT, galt als sauber. TRIFFT.

rev-list --count an einem Nicht-Repo   Rueckgabe [] leer, exit 128
  -> seine Bedingung greift, weil der String leer ist. "Zufaellig fail-safe" TRIFFT.
```

**Dieselbe Signatur wie bei mir: exit 128, Ausgabe leer, `wc -l` zählt das Nichts als 0.**

### Sein tragender Satz — am Code geprüft, nicht geglaubt

> *„nicht messbar führt immer zum Überspringen und nie zum Merge"*

`lage()` gibt `'unmessbar'` an drei Stellen zurück (HEAD, status, rev-list — je `if rc:`), und
`main()` fängt sie mit `continue` **vor** dem Merge ab. Rückgabewerte `0/1/2` wie beschrieben.
**Der tragende Satz hält.**

### Und der Fund: der sechste Exit-Code

```
Zeile  52  rc, kopf   = git(... rev-parse --short HEAD)      if rc:  GEPRUEFT
Zeile  56  rc, offen  = git(... status --porcelain -uno)     if rc:  GEPRUEFT
Zeile  61  rc, voraus = git(... rev-list --count)            if rc:  GEPRUEFT
Zeile  77  rc, ziel   = git(... rev-parse fork/...)          if rc:  GEPRUEFT
Zeile 100  rc,  _     = git(... merge --ff-only)             if rc:  GEPRUEFT
Zeile 101  rc2, neu   = git(... rev-parse --short HEAD)              NIE GELESEN
```

**`rc2` kommt im ganzen Werkzeug genau einmal vor — bei seiner Zuweisung.** Fünf von sechs
Exit-Codes werden gelesen, der sechste nicht.

**Wirkung, gemessen statt behauptet:** `rev-parse --short HEAD` liefert im Fehlerfall exit 128 und
eine **leere** Ausgabe. Die `else`-Zweig-Zeile druckt dann:

```
  ticket-rolle-planner         ab3a037 ->
  ^ Erfolgszeile, leeres Ziel, fehler-Zaehler unberuehrt -> main() gibt 0 zurueck
```

**Eine Rückmeldung „alle erreichbaren Bäume auf Stand" an einem Baum, dessen Ergebnis niemand
lesen konnte** — genau die Klasse, gegen die das Werkzeug gebaut wurde, **eine Zeile unter der
Behebung**.

### Die Größe des Fundes sage ich dazu

**Klein.** `rev-parse` unmittelbar nach einem erfolgreichen `merge --ff-only` im selben Repository
scheitert praktisch nie. **Ich habe den Fall NICHT gestellt** — dafür müsste ich einen Arbeitsbaum
zerstören, und das ist nicht meine Rolle. Belegt ist die *Form* (exit 128 + leere Ausgabe an einem
Nicht-Repo) und die *Folge* (die Formatzeile, isoliert nachgestellt). **Die Wahrscheinlichkeit ist
gering, die Klasse ist exakt.**

*Ich melde ihn trotzdem, weil das Werkzeug seinen Wert genau aus der Zusage zieht, ALLE Exit-Codes
zu lesen — und weil ich in §70 an mir selbst gelernt habe, dass die zufällig richtige Antwort die
ist, die stehenbleibt.*

### Was diese Runde über die Kette sagt

```
00:16:58  ich melde Fehler 27 an mir selbst
00:22:20  er hat ihn an SICH gesucht, gefunden, behoben und ins Werkzeug gelegt
          — fuenf Minuten, ohne Ball, ohne Aufforderung
```

**Und eine Kleinigkeit, die ich nicht als Widerspruch stehenlasse:** seine Meldung sagt, mein Baum
sei „getrackt offen" übersprungen worden, während ich jede Runde „Baum 0" melde. **Beide sind
richtig.** Mein `ab3a0373` fiel auf 00:22:12, seiner auf 00:22:20 — **acht Sekunden**, und sein
Lauf traf das Fenster zwischen meinem Schreiben und meinem Commit. *Zwei richtige Messungen
desselben Baums, die sich nur widersprechen, solange niemand die Uhrzeit dazuschreibt.*

**Ball beim Release-Prüfer** (Zeile 101, klein). **Kein Zustandsfeld angefasst, kein Bau, sein
Werkzeug nicht gefahren** — es merged, und Merges sind nicht meine Rolle.

---

## 73 — W-34 hält an jeder gemessenen Stelle, und es ist die QUELLE von W-27/1s Regel

**Stand:** HEAD `c207290f`, Baum 0 Einträge. **Sicherung:** `ls-remote` exit 0, LIVE `9f4bfe09`,
`is-ancestor` exit 1, **1 Commit nicht transportiert**.

### Zuerst die Gegenmessung des Release-Prüfers zu §71 — sie trifft, auch in der Abweichung

`9f4bfe09` bestätigt alle vier Größen meines A-37-Fundes unabhängig. **Die eine Zahl, bei der wir
auseinanderliegen, habe ich an meinem eigenen Befehl nachgemessen:**

```
ich:  rev-list --count 1c36544e..ea377567^   = 0     Dach schliesst den Endpunkt AUS
er:   rev-list --count 1c36544e..ea377567    = 1     Spanne zaehlt B mit
```

**Beides richtig, zwei Konventionen, ein Sachverhalt** — und er hat es selbst so eingeordnet.
*Er nennt es „ohne Belang" und benennt es trotzdem. Das ist die richtige Reihenfolge.*

### Posten (a) an W-34, Basis `6682b83c` — neun Zeiger, alle geprüft

```
GuidedView.tsx           IDENTISCH zur Basis
fahrschritte.ts          IDENTISCH zur Basis

GuidedView.tsx:4      import { T, STATUS_LABEL, type SchrittStatus, type Fahrschritt }   TRIFFT
GuidedView.tsx:18     badgeFarbe: Record<SchrittStatus, …>                               TRIFFT
GuidedView.tsx:22     checkFarbe: Record<SchrittStatus, …>                               TRIFFT
fahrschritte.ts:40-41 'Leere Liste => open — ein Schritt ohne pruefbare Aussage …'       TRIFFT
fahrschritte.ts:43-49 statusAus, FUENF return-Zweige selbst gezaehlt                     TRIFFT
fahrschritte.ts:84-88 levels per levelId aus nodes ODER roofs ODER ceilings              TRIFFT
```

**Dazu Posten (b) — die fünf Wächterzahlen, Muster am bekannten Treffer verifiziert** (`^test\(`
liefert für `fahrschritte` 12, genau die Zahl des Blattes):

```
DATEI               BLATT  HEUTE  BASIS   Datei
fahrschritte          12     12     12    IDENTISCH
gefuehrteEhrlich       8      8      8    IDENTISCH
breiten                5      5      5    IDENTISCH
dialogFokus           11     11     11    IDENTISCH
stilschicht           58     58     58    IDENTISCH
```

**Fünf von fünf, und alle fünf Dateien unverändert. Vierzehn Zusagen, vierzehn Treffer.**

### Ein Fehlalarm, den ich vor dem Melden gefangen habe

`fahrschritte.ts:84-88` zählt aus **drei** Quellen, und meine erste Blattzeile nannte nur zwei
(`nodes ODER roofs`). **Der Satz lief über den Zeilenumbruch weiter und endet auf `ODER ceilings`;
das Blatt nennt sie an drei Stellen (`:97`, `:166`, `:281`).** *Mein Zeilenschnitt, nicht sein
Fehler — der fünfzehnte Musterfehler wäre es gewesen, und er ist nie hinausgegangen.*

### Der eigentliche Fund dieser Runde ist kein Defekt, sondern eine Kette

**W-27/1 hielt in §69 an jeder Stelle, weil es eine Regel trägt: *„Jede Fangprobe muss WIRKSAM
sein: sie wird gefahren und muss FALLEN."* Diese Regel hat einen Ursprung, und er ist messbar:**

```
W-34   Schnitt 12.08. 15:18
W-27/1 Schnitt 12.08. 20:11     -> 4 Stunden 53 Minuten spaeter
W-27/1:114  "Eine Probe, die gruen bleibt, prueft nichts — das ist der Befund aus W-34-1"
```

**Und W-34 hat den Befund an sich selbst erhoben, unter eigenem yaml-Schlüssel
`und_die_fangprobe_faengt_nicht`:**

```
die eigene erste Fangprobe gefahren   1698 tests · 1698 pass · 0 fail  -> faengt NICHTS
die beiden anderen zum Vergleich      1 fail (K6) · 4 fail (K5,K4)     -> beide fangen
"von fuenf Fangproben ist genau die eine wirkungslos,
 die zum tragenden P1-Kriterium gehoert"
```

**Und dann der Zug, der es aus dem Verneinen heraushebt** — Schlüssel
`damit_der_befund_nicht_nur_verneint`: er hat den `warn`-Zweig ersatzweise **entfernt** statt
verschoben → **3 fail**. *Also ist der Zweig sehr wohl bewacht; die Probe zielte auf seine
POSITION statt auf den ZWEIG.* **Er hat nicht nur gezeigt, dass die Probe nichts fängt, sondern
dass an derselben Stelle eine wirksame existiert.**

### Was das für §69 bedeutet

In §69 hatte ich beobachtet, dass die fehlerfreien Blätter ihre eigene Widerlegung einbauen — und
dazugeschrieben, es sei *„eine Beobachtung an drei von acht, keine Regel"*. **Jetzt ist es mehr als
eine Beobachtung: die Eigenschaft ist WEITERGEGEBEN worden, und die Weitergabe ist datierbar.**

```
W-34 schreibt sein eigenes Versagen auf, ehrlich und mit Gegenmessung
    -> 4h53 spaeter macht W-27/1 daraus ein KRITERIUM mit Fallpflicht
        -> und W-27/1 haelt in 69 an jeder Stelle, die ich gerechnet habe
```

**Die fehlerfreien Blätter sind nicht zufällig gut. Das eine ist gut, WEIL das andere seinen
Fehler brauchbar aufgeschrieben hat.** *Ein Befund, der nur „falsch" sagt, erzeugt keine Regel;
einer, der die wirksame Alternative mitmisst, erzeugt eine.*

**Viertes fehlerfreies Blatt: W-08/1 · W-11/1 · W-27/1 · W-34.**

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.** *Die Kette gehört dem
Planner — sie ist der erste Beleg, dass die Eigenschaft übertragbar ist und nicht am Autor hängt.*

---

## 74 — FEHLER 28: mein Messstand ist nicht atomar, und diesmal habe ich es LIVE gesehen

**Der Baum ist mir unter der Messung weggewandert.** Drei aufeinanderfolgende Befehle EINES
Blocks, drei verschiedene Zustände desselben Repositories:

```
git rev-parse HEAD                 -> bdcb07df
git status --porcelain | wc -l     -> 3
git log --oneline bdcb07df..HEAD   -> SECHS Commits
```

**Die dritte Zeile ist mit der ersten unvereinbar** — eine Spanne von HEAD zu HEAD kann nicht
sechs Commits enthalten. *Genau daran habe ich es gemerkt.* Das Reflog nennt die Ursache:

```
HEAD@{17.08 00:30:06}  merge fca886bd...: Fast-forward
HEAD@{17.08 00:29:29}  commit: plan-pruefer: W-34 haelt an jeder gemessenen Stelle
```

**37 Sekunden nach meinem eigenen Commit** hat der Rückweg meinen Arbeitsbaum vorgezogen,
`bdcb07df` → `fca886bd`, **reiner Fast-forward** (`is-ancestor` exit 0) — meine Arbeit ist
unversehrt, und die „3" war die Momentaufnahme während des Auscheckens. **Neu gemessen: Baum 0,
`-uno` 0, HEAD nach zwei Sekunden unverändert.**

### Die Klasse ist neu, und sie ist die dritte

```
Fehler 26   falsch gemessen, falsch gesagt        -> falsche Aussage, wurde bemerkt
Fehler 27   gar nicht gemessen, richtig geraten   -> richtige Aussage, blieb stehen
Fehler 28   richtig gemessen, aber NICHT GLEICHZEITIG
                                                  -> jede Zeile fuer sich wahr, der Block falsch
```

**Hätte ich es nicht bemerkt, hätte ich gemeldet: „HEAD `bdcb07df`, Baum 3, sechs neue Commits."
Jede Zahl echt, der Satz insgesamt eine Fiktion** — und er hätte wie eine Repo-Anomalie ausgesehen
statt wie meine eigene Nicht-Gleichzeitigkeit.

**Behoben:** der Messstand wird in eine Variable gelegt, **jeder** Befehl der Runde rechnet gegen
diese Variable, und am Ende steht die Gegenprobe, ob HEAD sich bewegt hat. *Bewegt er sich, sind
die Messwerte der Runde keine Messwerte und werden wiederholt — nicht nachgetragen.*

### Und es ist NICHT der Fehler des Rückwegs — aber es ist auch kein Zufall

Der Rückweg hat richtig gehandelt: mein Baum war in dem Moment sauber, also wurde vorgezogen.
**Die Kollision liegt in der Bauart, nicht im Versehen:**

```
17  Fremd-Fast-forwards in meinen Baum ueber den ganzen Lauf
236 eigene Commits im selben Reflog
```

*Der Rückweg zieht genau die sauberen Bäume vor. Eine Wache-Runde ist über den größten Teil ihrer
Dauer sauber — sie misst, sie schreibt erst am Ende.* **Also trifft der Rückweg eine Wache-Runde
mit hoher Wahrscheinlichkeit, und beide Seiten arbeiten dabei korrekt.** Das ist eine gemessene
Wechselwirkung, kein Vorwurf, und die Abhilfe liegt bei mir: **wer in einem fremd bewegten Baum
misst, muss die Bewegung erkennen können.**

### Mein Ball ist zurück — und die Behebung geht über meinen Fund hinaus

`ed034871` behebt den sechsten Exit-Code aus §72. **Am Code nachgeprüft, nicht geglaubt:**

```
:100  rc, _      = git(merge --ff-only)      :101  if rc:            frueher Ruecksprung
:118  rc_neu, neu = git(rev-parse --short)   :119  if rc_neu or not neu:
:120-121  zaehlt als unmessbar, Meldung 'MERGE LIEF, ERGEBNIS UNMESSBAR  vor -> ?'
:130  return 1 if fehler else (2 if unmessbar else 0)      -> 2 statt 0
```

**`rc_neu` kommt zweimal vor — Zuweisung und Prüfung. Keine Zuweisung bleibt ungelesen.**
Und er ist über meinen Fund hinausgegangen: `if rc_neu or **not neu**` fängt zusätzlich den Fall
**Exit 0 bei leerer Ausgabe** — den hatte ich nicht genannt.

**Live gegengeprüft, ohne sein Werkzeug zu fahren** (`main()` nicht aufgerufen, Wachtposten
`__name__` in Zeile 133 belegt), an zwei VERSCHIEDENEN Ausfallursachen:

```
lage(Pfad ohne Repository)  -> ('unmessbar', 'HEAD nicht lesbar')
lage(gueltiger Baum, Ziel-SHA erfunden) -> ('unmessbar', 'rev-list nicht lesbar')
```

**Beide führen zum Überspringen, keiner zum Merge. Die tragende Zusage hält an zwei Ursachen,
nicht nur an der, die er selbst gestellt hat.** *Ball beim Release-Prüfer geschlossen.*

**Kein Ball offen aus dieser Runde. Kein Zustandsfeld angefasst, kein Bau.** *Die Wechselwirkung
Rückweg ↔ Wache gehört dem Integrator und dem Release-Prüfer zur Kenntnis — gemeldet, nicht als
Mangel.*

### Nachtrag zu §74 — der zweite Baumstand dieser Runde, und er ist meiner

Nach dem Commit meldete mein Baum **1** Eintrag. Nachgesehen statt stehengelassen:

```
?? scripts/__pycache__/        eine einzige Datei: rueckweg.cpython-314.pyc, 00:31
getrackt (-uno): 0             HEAD stabil
```

**Das Nebenprodukt ist meins** — ich hatte `rueckweg.py` importiert, um seine `lage()` gegen zwei
Ausfallursachen zu prüfen. **Ich habe es NICHT entfernt: das Löschen wurde abgelehnt, und ich
setze mich darüber nicht hinweg.** *Es liegt ungetrackt in `scripts/` und wird hiermit gemeldet
statt beseitigt — das ist ohnehin die rollenreinere Form.*

**Die Folge, die dem Release-Prüfer gehört:** `scripts/` trägt jetzt ein Python-Werkzeug, und
**`__pycache__` steht nicht in `.gitignore`** (nachgesehen, kein Treffer). Jeder Lauf legt das
Verzeichnis in dem Baum an, in dem er läuft.

```
sein Rueckweg prueft mit --untracked-files=no   -> sieht es NICHT, blockiert nicht
eine Wache, die status --porcelain zaehlt       -> sieht es SEHR WOHL
```

**Kein Schaden — aber unerklärtes Baumrauschen, und diese Runde zeigt, was das kostet: ich habe in
EINEM Durchgang zweimal einen Baumstand über null gemeldet, aus zwei völlig verschiedenen Gründen**
— die „3" war ein Fast-forward im Vollzug, die „1" ist Bytecode. *Ein Zähler, der aus zwei ganz
verschiedenen Gründen von null abweicht, ist genau so viel wert wie seine Erklärung — und beide
Male musste ich erst nachsehen, bevor die Zahl etwas bedeutete.*

**Vorschlag, kein Bau: ein Eintrag `__pycache__/` in `.gitignore`.** **Ball beim Release-Prüfer**
(sein Werkzeug, seine Datei) — **ich fasse `.gitignore` nicht an**, und die eine `.pyc` bleibt
liegen, bis jemand mit dem Recht dazu sie wegnimmt.

---

## 75 — W-23 durchgerechnet: sieben Zahlen über 5.607 Fälle, alle sieben treffen — und die verworfene Formel log GENAU dort, wo es keine Antwort gibt

**Stand:** HEAD `c6ce2f47`, getrackt 0, ungetrackt weiterhin `?? scripts/__pycache__/` (§74, liegt
gemeldet). **Messstand nach der neuen Form in eine Variable gelegt; Gegenprobe am Ende: HEAD
unbewegt.**

**Posten (c) am neunten unberührten Blatt: W-23 Deckung und Material**, `BETRIEBSBESTAETIGT`,
Schnitt 12.08. 11:22.

### Die entschiedene Rechnung (Vertretungsentscheid F-053)

```
SCHRANKE  Dachneigung >= Regeldachneigung, sonst KEINE Rechnung
TEILUNG   n_min = aufrunden(L / Lattmass_max)
          n_max = abrunden (L / Lattmass_min)
          n_min <= n_max -> TEILBAR, Lattmass = L/n fuer JEDES n im Bereich
          n_min >  n_max -> KEINE gleichmaessige Teilung
VERWORFEN n = aufrunden(L/max), Lattmass = L/n
```

### Erst das genannte Beispiel, dann die ganze Tabelle

```
Harzer Pfanne 7, Bereich 372-405, L=1000
  VERWORFEN  n=ceil(1000/405)=3 -> 333,3 mm   im Bereich? NEIN
  NEU        n_min=3 · n_max=floor(1000/372)=2 -> n_min>n_max -> KEINE Teilung
  die Luecke  n=2 -> 500 mm zu gross · n=3 -> 333 mm zu klein
  7 x 801 = 5607                                              TRIFFT
```

**Und dann alle sieben Modelle unabhängig nachgerechnet** — Längen 1.000–9.000 mm in 10-mm-Schritten
(801, selbst gezählt), **in exakter Bruchrechnung statt Fließkomma**:

```
MODELL             BEREICH      BLATT  MEINE   %BLATT  %MEINE
Rubin 9V           370-400        146    146     18,2    18,2   TRIFFT
Harzer Pfanne 7    372-405        136    136     17,0    17,0   TRIFFT
Achat 12V          330-360        100    100     12,5    12,5   TRIFFT
Rubin 13V          330-360        100    100     12,5    12,5   TRIFFT
Granat 11V         338-380         63     63      7,9     7,9   TRIFFT
Topas 13V          320-360         55     55      6,9     6,9   TRIFFT
Topas 11V          320-380         21     21      2,6     2,6   TRIFFT
```

**Sieben von sieben, auf die Einheit genau, über 5.607 gerechnete Fälle.** *Achat 12V und
Rubin 13V teilen denselben Bereich und liefern beide 100 — die Tabelle ist auch in sich stimmig.*

### Die tragende Zusage der neuen Fassung, erschöpfend geprüft

```
MODELL             verweigert  naiv falsch  gleiche Menge?  je n im Bereich
Rubin 9V                  146          146            JA              JA
Harzer Pfanne 7           136          136            JA              JA
Achat 12V                 100          100            JA              JA
Rubin 13V                 100          100            JA              JA
Granat 11V                 63           63            JA              JA
Topas 13V                  55           55            JA              JA
Topas 11V                  21           21            JA              JA
```

**Zwei Ergebnisse, und das zweite ist mehr als eine Bestätigung:**

1. **Wo die Formel „teilbar" sagt, liegt JEDES n aus `[n_min, n_max]` im Bereich** — nicht am
   Beispiel, sondern an allen 5.607 Fällen. *Die Zusage „für jedes n im Bereich" ist erschöpfend
   eingelöst.*

2. **Die Verweigerungsmenge der richtigen Formel ist IDENTISCH mit der Falschmenge der naiven** —
   bei allen sieben Modellen, Fall für Fall.

**Das schärft die Aussage des Blattes.** Es schreibt, die verworfene Fassung liefere *„in 2,6 % bis
18,2 % einen Wert AUSSERHALB des Bereichs, und zwar leise"*. **Gemessen ist es enger als das: sie
lieferte eine Zahl in genau den Fällen, in denen es KEINE gibt.** *Sie war nicht ungenau — sie
beantwortete ausschließlich die unbeantwortbaren Fragen.* **Und genau dort sieht eine Zahl am
harmlosesten aus, weil kein Vergleichswert existiert, an dem sie auffallen könnte.**

### Fünftes fehlerfreies Blatt — und es trägt die Eigenschaft in ihrer schärfsten Form

W-23 hat die verworfene Fassung **nicht weggelassen, sondern mit ihrer Fehlerquote stehengelassen**:

> *„Die verworfene Formelfassung steht MIT ihrer Fehlerquote im Blatt, statt weggelassen zu werden
> — 2,6 % bis 18,2 % über 5.607 gerechnete Fälle."*

**Und es ist die eigene verworfene Fassung des Planners** — er hat seinen eigenen Vorschlag
gemessen, verworfen und die Messung danebengestellt. *Ohne diese Zahlen hätte ich nichts
nachzurechnen gehabt; die Prüfbarkeit dieses Blattes ist sein eigenes Werk.*

```
W-08/1  nennt seinen gefaehrlichsten Grenzfall im TITEL
W-11/1  nennt die SUMME neben den Einzelzahlen
W-27/1  nennt seine eigene Widerlegung — und verlangt, dass sie FAELLT
W-34    schreibt auf, dass die eigene Fangprobe NICHTS faengt, und misst die wirksame dazu
W-23    laesst die eigene VERWORFENE Formel stehen, mit ihrer Fehlerquote ueber 5.607 Faelle
```

**Alle fünf sind vom 12.–13.08. Alle fünf halten. Alle fünf liefern das Material, mit dem man sie
widerlegen könnte.** *Das ist inzwischen kein Zufall mehr und in §73 ist die Weitergabe datiert —
es gehört dem Planner als Beobachtung, nicht als Regel, die ich setzen könnte.*

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 76 — Die Größenangabe zu Fehler 28 nachgemessen: das Verhältnis trifft, die Abstände sind in SECHS Lesarten nicht reproduzierbar

**Stand:** HEAD `ae866cd0`, getrackt 0, `?? scripts/__pycache__/` liegt weiter (§74). **Messstand
in Variable, Gegenprobe am Ende: unbewegt.** **Erstmals `is-ancestor` exit 0 — alles transportiert.**

`f455223e` liefert die Größe zu meinem §74-Befund nach. **Ich habe jede Zahl selbst gemessen.**

### Das Verhältnis trifft — und der Versatz ist selbst ein Beleg

```
BAUM             FF   COMMITS      seine Angabe
plan-pruefer     18       239      18 / 238
planner          44       117      43 / 117
generator        45        52      44 /  52
evaluator        50        12      49 /  12
```

**Seine Kernaussage trifft: 50 zu 12 sind 4,2 — beim Evaluator bewegt der Rückweg den HEAD
viermal so oft, wie der Evaluator selbst committet.**

**Die drei `+1` sind kein Widerspruch, sondern sein Werkzeug bei der Arbeit:** zwischen seiner
Messung (00:38) und meiner lief der Rückweg erneut und zog die drei zurückliegenden Bäume nach.
**Mein Baum bekam kein weiteres Fast-forward — weil er VORAUS war und deshalb übersprungen wurde**,
genau die Vorbedingung, die er gebaut hat. *Eine Messung darüber, wie oft der Baum sich bewegt,
wird durch die Bewegung des Baums verschoben — Fehler 28, sichtbar in den Zahlen über Fehler 28.*

### Die Abstände: sechs Lesarten, keine trifft

Seine Angabe: **plan-pruefer 10 Paare · Median 100 s · unter 30 s: 3** ·
generator 30 / 1719 / 1 · evaluator 34 / 1587 / 0.

```
LESART                                  plan-pruefer   generator   evaluator
A  Reflog-Zeit, letzter Commit davor     17 /  90 / 0   43/2379/0   48/2019/0
B  Reflog-Zeit, nur direkt benachbart    17 /  90 / 0    8/ 194/0    8/1083/0
C  Reflog-Zeit, vorwaerts zum naechsten  17 / 137 / 2   32/2224/0   40/1258/1
D  COMMIT-Datum, letzter Commit davor    17 /  43 / 7   43/2178/1   48/1850/0
E  Reflog-Zeit, je Commit nur ein Paar   17 /  90 / 0    8/ 194/0    8/1083/0
F  COMMIT-Datum, je Commit nur ein Paar  17 /  43 / 7    8/ 118/1    8/1036/0
                                    SEINE  10 / 100 / 3   30/1719/1   34/1587/0
```

**Und die Zeit erklärt es nicht.** Er maß früher, also müsste er WENIGER Paare haben — aber die
**Zahl der Fast-forwards ist in beiden Messungen identisch 18**. *Bei gleicher Zahl der
Fast-forwards kann die Zahl der Paare nicht von 10 auf 17 wachsen, ohne dass sich das Verfahren
unterscheidet.* **Der Unterschied liegt in der Methode, nicht in der Uhr.**

**Dazu kommt: es gibt zwei Uhren, und sie antworten verschieden.** Der Reflog-Eintrag trägt seinen
eigenen Zeitstempel; `%ct` trägt das Commit-Datum des Ziels. **Für meinen Baum: Median 90 s gegen
43 s, und „unter 30 s" 0 gegen 7.** *Dieselbe Frage, dieselben Daten, zwei Antworten — und welche
gemeint ist, entscheidet den Befund.*

### Der Befund ist nicht die Abweichung, sondern das Fehlen des Befehls

`scripts/rueckweg.py:42-55` trägt die Zahlen **im Kommentar** — und trägt den **Erhebungsbefehl
nicht**. Er schreibt in der Botschaft *„Die Zahlen stehen im Werkzeug"*; **dort stehen die
ERGEBNISSE, nicht die Messung.**

**Das trägt eine Entscheidung:** *„KEIN RUHEFENSTER EINGEBAUT … es finge 4 von 74 Paaren und
verzögerte dafür jeden Befundtransport."* **Die Zahl `4 von 74` ist die Begründung — und sie ist
von niemandem nachrechenbar.** *Das ist B5/B6 an genau der Rolle, die diese Regel heute Nacht am
schärfsten vertreten hat, und ich melde es deshalb ohne Häme: es trifft jeden, mich in §70 und §74
eingeschlossen.*

### Was ich ausdrücklich BESTÄTIGE

**Seine Schlussfolgerung überlebt meine Messung, in allen sechs Lesarten:**

```
evaluator   unter 30 s:  0   in JEDER Lesart
generator   unter 30 s:  0 oder 1
plan-pruefer  der einzige Baum mit kurzen Abstaenden ueberhaupt
```

**Der kritische Fall ist selten und trifft fast nur den aktivsten Baum — das trifft zu.** *Die
Entscheidung gegen ein Ruhefenster ist damit sachlich wahrscheinlich richtig; nachprüfbar ist sie
nicht.* **Und sein Preisargument teile ich vollständig: der Rückweg war heute Nacht mehrfach der
Weg, auf dem ein Befund die andere Rolle überhaupt erreicht hat — auch meiner.**

**Ball beim Release-Prüfer: der Erhebungsbefehl zu `rueckweg.py:48-51` und zu `4 von 74`.**
*Nicht die Zahlen ändern — den Befehl danebenschreiben, mit der Angabe, welche der beiden Uhren
gilt.* **Kein Zustandsfeld angefasst, kein Bau.**

---

## 77 — Posten (d) als Sweep über 84 Aufträge: die Hausregel „nicht löschen, danebenstellen" ist die URSACHE der wandernden Zeiger

**Stand:** HEAD `88e5f0b8`, getrackt 0, `?? scripts/__pycache__/` liegt weiter. **Messstand in
Variable, Gegenprobe: unbewegt.**

**Nicht §71 wiederholt.** Dort waren es die acht nicht-terminalen Aufträge; hier alle **84 mit
`basis_sha`**, gegen die Frage: *unter welchem Blatt hat sich genannter Code seit dem Schnitt
bewegt?* **Muster an zwei bekannten Treffern verifiziert (A-37 → 2, W-06 → 0).**

```
ohne Drift  45     mit Drift  30     kein Blatt  9
```

**Die meisten 30 sind KEIN Befund**: ein Auftrag, der gebaut wird, verändert seine Gegenstände.
Ich habe deshalb getrennt, was **fremd** ist — und dabei sind zwei Dateien übriggeblieben, die von
einem ANDEREN Auftrag berührt wurden: `studioDaten.ts` und `StartView.tsx`, beide durch
**`3ad920b1` — A-23s Bau**.

### A-23 hat vorbildlich gearbeitet, und genau deshalb ist es passiert

A-23s Auftrag war, überholte Begleittexte zu berichtigen. Der Diff zeigt die Hausregel in
Reinform:

```
- * Gefuellt wird sie in **Teil B** (Route + Controller, bei Yama).
+ * **UEBERHOLT (A-23, 13.08.), und nicht geloescht.** *Hier stand: „…"*
+ * Der Weg, jede Stelle geoeffnet: HausplanerController.php:101 -> :55 -> ...
```

**Kein Wort gelöscht, der alte Stand belegt, der neue Weg nachgewiesen.** *Das ist genau, was die
Hausregel verlangt.* **Und es wächst dabei: `studioDaten.ts` +10 Zeilen (257 → 267),
`StartView.tsx` +14 (267 → 281) — beide Einschübe nahe am Dateianfang.**

### Vier abgenommene Blätter zeigen seitdem auf etwas anderes

**`studioDaten.ts`, Einschub bei `:154`, Versatz gleichmäßig +10:**

```
ZEIGER   an der Blatt-BASIS                                  HEUTE
:163     export type SchrittStatus = 'ok'|'prog'|'warn'|'open';   Kommentar ueber web.php:5016
:206     ... cfg: true  (W-38: "die einzige mit cfg: true")        empfehlung: { titel: 'Zu den 4 …
:255     export const STATUS_LABEL: Record<SchrittStatus,string>   /**   (Kommentarbeginn)
```

**GEGENKONTROLLE — und sie ist der Beleg, dass der Versatz sauber lokalisiert ist:** `:97` liegt
**oberhalb** des Einschubs und trägt an der Basis wie heute **zeichengleich**
`export type StudioModus = 'start' | 'guided' | 'expert';`. *Die Zeiger unter dem Einschub sind
gewandert, die darüber nicht.*

**`StartView.tsx`, Einschübe bei `:18` und `:205`, +14:**

```
:18   Basis  '* Gefuellt wird sie in **Teil B** (Route + …'   heute  ' *'  (Leerzeile)
:205  Basis  '**Die echte Liste braucht eine Route…'          heute  <div className="hp-start-wrap">
:206  Basis  '{projekte.length === 0 ? ('                     heute  <div className="hp-start-kicker">
```

**Betroffen, alle vier `BETRIEBSBESTAETIGT`:**

```
W-38   funf Zeiger  :163 · :164-174 · :206 · :255 · :255-257
W-33   funf Stellen :18 (3x) · :205 · :206
W-36   :163
W-40   :163
```

**Anker, die heute stimmen: `SchrittStatus` :173 · `cfg: true` :216 · `STATUS_LABEL` :265** —
jeder exakt +10.

### Warum das mehr ist als der vierte Fall derselben Klasse

§68 fand zwei Einschübe in der FORMELSAMMLUNG und nannte einen davon „ohne Gegenprobe". **Hier ist
das Gegenteil der Fall: A-23 hat alles richtig gemacht.** Die Regel *„nicht löschen, sondern
danebenstellen"* schützt die Wahrheit **an der Stelle** — und **erzwingt dabei Wachstum**. Jedes
Wachstum oberhalb eines Zeigers macht ihn falsch.

```
Regel A  Berichtigungen NICHT loeschen, den alten Stand danebenstellen   -> Datei waechst
Regel B  Fundstellen als datei.ts:zeile belegen                          -> Zeiger bricht
```

**Die beiden Regeln sind einzeln richtig und zusammen unverträglich.** *Das ist keine Nachlässigkeit
irgendeiner Rolle — es ist eine Regelkollision, und sie erklärt, warum dieselbe Klasse heute Nacht
zum fünften Mal auftaucht, jedes Mal bei jemand anderem.*

**Der Planner hat die Abhilfe in `15c49f96` selbst benannt — Kennung statt Zeile.** Meine Messung
liefert das Argument dafür nach, das dort noch fehlte: **nicht „Zeilen veralten", sondern „die
Hausregel LÄSST sie veralten, jedes Mal, wenn sie befolgt wird".**

**Ball beim Planner.** *A-23 trifft keine Schuld und der Befund ist ausdrücklich keiner gegen ihn —
er ist der Beleg, dass die Ursache in der Regel liegt und nicht im Ausführenden.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 78 — Posten (e) am Integrator-Stapel: fünf Punkte unverändert offen, und der sechste ist SCHÄRFER geworden — sieben `dor_beleg`-Felder, sieben Mal überholt

**Stand:** HEAD `f688299f`, getrackt 0, `?? scripts/__pycache__/` liegt weiter. **Messstand in
Variable, Gegenprobe: unbewegt.** **Nicht aus meiner Liste wiederholt — jeder Punkt frisch
gemessen.**

### Die fünf, die unverändert stehen

```
(1) Baelle bei plan-pruefer                      39            unveraendert
(2) zustand: BEFUND                              3    Z.26467 · 26506 · 26591
(3) A-09 doppelter release_vermerk               2    Z.3267 · 3288
(4) nicht geschlossene yaml-Bloecke              2    Z.3215 (A-08) · Z.7876 (Vorschlag)
```

**Bei (4) hätte ich mich fast selbst belogen:** meine erste Zählung war eine **Differenz**
(Öffner minus Schließer) und ergab **1**. Sequenziell geparst sind es **2** — und es sind genau die
beiden, die ich damals gemeldet hatte. *Die Differenz stimmt nur, wenn man alle Zaunformen kennt;
ich hatte `text`-Zäune übersehen. Zwei Zählungen, eine falsch, und die falsche war die bequeme.*

### Der sechste Punkt hat sich verändert — nicht erledigt, sondern präziser

Ich hatte gemeldet: *„`dor_beleg` A-41/W-17/1 leer, A-37/A-38 überholt."* **Leere Felder gibt es
heute keine mehr** (`grep` auf leeren Wert: 0 Treffer). **Stattdessen sieben Felder mit
Platzhaltern — und jedes einzelne ist widerlegbar:**

```
AUFTRAG  ZUSTAND              dor_beleg sagt              im Bestand liegt
A-37     CODE_FERTIG          "2. Runde 15.08."           Runde 3 Z.21476 · Runde 4 Z.22491
A-38     ENTWURF              "2. Runde 15.08."           Runde 3 Z.21476
A-39     ENTWURF              "steht aus"                 Z.21973  a39_dor_runde_1
A-40     ENTWURF              "steht aus"                 Z.22081  a40_dor_runde_1
A-42     ENTWURF              "steht aus"                 Z.25910 · 25964 · 26011 ·
                                                          26063 · 26105 · 27392  (SECHS Bloecke)
W-17/1   BETRIEBSBESTAETIGT   "steht aus"                 Z.23149  w17_1_dor
A-41     BETRIEBSBESTAETIGT   "steht aus"                 (kein Block gefunden — ich behaupte keinen)
```

**Zwei davon sind ein Widerspruch im Datensatz selbst:** `A-41` und `W-17/1` stehen auf
**`BETRIEBSBESTAETIGT`** — die ganze Kette durchlaufen — und tragen zugleich eine DoR, die
*„steht aus"*. **Ein Auftrag kann nicht abgenommen und veröffentlicht sein, während seine
Bereitschaftsprüfung aussteht.**

**Fünf weitere sind nicht widersprüchlich, sondern schlicht alt:** die Arbeit ist getan und liegt
im selben Dokument, ein paar tausend Zeilen weiter unten — **nur das Feld weiß es nicht.**

### Und das ist dieselbe Krankheit wie §77, nur in einem anderen Träger

```
§77   die Wahrheit steht in der DATEI          der Zeiger datei.ts:zeile ist alt
§78   die Wahrheit steht im BEFUNDBLOCK        das Feld dor_beleg ist alt
```

**Beide Male ist der Bestand richtig und der Verweis darauf veraltet.** *In §77 wächst die Datei
und schiebt die Zeile weg; hier wächst der Datensatz und lässt das Feld stehen.* **Die Ursache ist
dieselbe: ein Verweis, der nicht mitwandert, wenn das Ziel sich bewegt — und beide Male merkt es
niemand, weil der Verweis unverändert AUSSIEHT.**

**Ball beim Integrator** (er führt die Felder; §16 — eine Statuswahrheit).
**Kein Zustandsfeld angefasst, kein Bau.** *Ich nenne die Fundstellen, damit das Nachziehen eine
Abschrift ist und keine Suche.*

---

## 79 — Posten (a) an den DOKUMENT-Zeigern: 33 von 33 halten, und REGISTER.md hat die Regelkollision aus §77 bereits GELÖST

**Stand:** HEAD `48225367`, getrackt 0, `?? scripts/__pycache__/` liegt weiter. **Messstand in
Variable, Gegenprobe: unbewegt.**

**§77 hat die CODE-Zeiger systematisch gemessen. Die andere Hälfte war ungemessen:** 121 Zeiger auf
`.md:<zeile>` in den aktiven Blättern, angeführt von **REGISTER.md mit 33**.

### Der Prüfstein: REGISTER.md hat sich nach dem eigenen Unfall eine Regel gegeben

Am 13.08. hat der Planner in diese Datei **oben** eingefügt und damit 41 Verweise verschoben —
sein eigener Befund (`f4bda8e9`, berichtigt). Die Lehre steht seither in der Datei:

> *„oberhalb der letzten Tabellenzeile wird **keine Zeile eingefügt**. Ergänzungen gehen ans
> Dateiende oder in eine bestehende Zeile."*

**Gemessen, ob die Regel hält:**

```
COMMIT     ZEIT          LAENGE   W-12   W-06   W-14
f4bda8e9   13.08 22:52      395     57     73     86   <- der Unfall, Zeiger verschoben
1e1afd1b   16.08 17:47      458     38     54     67   <- berichtigt, Regel in Kraft
43771e3b   16.08 18:40      513     38     54     67
7e9d2566   16.08 19:10      545     38     54     67
dbdd4691   16.08 21:34      580     38     54     67
HEAD       17.08 00:50      580     38     54     67
```

**Seit die Regel gilt, ist die Datei um 122 Zeilen gewachsen — über vier Commits — und KEIN
einziger Tabellenzeiger hat sich bewegt.**

### Alle 33 Zeiger geprüft

```
29 zeigen in die Tabelle (<= Z.273, dem letzten Tabellenzeilen-Anfang)   ALLE TREFFEN
 4 zeigen darunter, in den Fliesstext                                    beide geklaert
```

Die 29 einzeln nachgeschlagen — `:44` W-03 · `:47` W-10 · `:48` W-16 · `:54` W-06 · `:57` W-09 ·
`:67` W-14 · `:70` W-43 · `:98` W-31 · `:122` W-35 · `:124` W-37 · `:127` W-40 · `:128` W-41,
dazu `:6` und `:87` als Legendenzeilen. **Jede trägt genau das, was das zitierende Blatt behauptet.**
*Darunter die vier, die A-34s Evaluator als „genau vier LEER-Werkzeuge" gezählt hat —
`:38 · :48 · :54 · :67`, unverändert.*

**Die vier ungeschützten:**

```
W-31:345  nennt REGISTER.md:390-398   -> :390 traegt heute genau den Abschnitt, den W-31 beschreibt   TRIFFT
W-09 3x   nennt REGISTER.md:373       -> heute LEER, aber die Stelle steht in einem Feld 'war:'
                                          unter dem Schluessel befund_2_ERLEDIGT — ein historischer
                                          Beleg, den A-34s Regel ausdruecklich schuetzt.            KEIN FUND
```

### Und das ist die Antwort auf den §77-Ball

§77 hat die Regelkollision benannt:

```
Regel A  nicht loeschen, danebenstellen  -> Datei waechst
Regel B  Fundstellen als datei:zeile     -> Zeiger bricht
```

**REGISTER.md löst sie — nicht durch Aufgeben einer der beiden Regeln, sondern durch eine dritte:
WO gewachsen wird.**

```
studioDaten.ts   Einschub bei :154   +10 Zeilen   ->  4 Blaetter zeigen ins Falsche   (§77)
REGISTER.md      Wachstum unter :273 +122 Zeilen  ->  0 Zeiger bewegt                 (hier)
```

**Dieselbe Hausregel, zwei Platzierungen, zwei Ergebnisse.** *Der Unterschied ist nicht Sorgfalt —
in §77 war A-23 sorgfältig — sondern die Frage, ob unterhalb der Einfügestelle noch Zeiger liegen.*

**Für den Planner heißt das: es gibt eine zweite, erprobte Abhilfe neben „Kennung statt Zeile".**
*„Wachse nur dort, wo niemand hinzeigt" ist billiger — sie ändert keinen einzigen bestehenden
Verweis — und sie hat in dieser Datei 122 Zeilen lang gehalten.* **Beide zusammen decken auch den
Fall ab, in dem eine Berichtigung mitten im Text stehen MUSS: dort hilft nur der Anker.**

**Kein Ball, kein Fund — der Befund ist ein gefundenes Gegenbeispiel und gehört zum §77-Ball beim
Planner.** **Kein Zustandsfeld angefasst, kein Bau.**

### Nachtrag zu §79 — zweite Baumbewegung mitten in der Runde, und mein §76-Ball ist geschlossen

**Der Baum ist erneut gewandert:** Messstand `48225367`, Elter meines Commits `a1aeaa17`.
Dazwischen `b47a5d5e` (Release-Prüfer) und der Rückweg.

**Und diesmal war der Schaden messbar null — das ist der Unterschied, den die Behebung aus §74
macht:**

```
REGISTER.md bei 48225367   580 Zeilen · W-06 auf 54
REGISTER.md bei a1aeaa17   580 Zeilen · W-06 auf 54
```

**Mein Messgegenstand ist über die Bewegung hinweg zeichengleich, also steht §79.** *Vorher hätte
ich die Runde wiederholen müssen; jetzt kann ich ZEIGEN, dass die Bewegung nicht traf, was ich
gemessen habe.* **Fehler 28 ist damit nicht verhindert, sondern beherrschbar geworden — das ist
der erreichbare Zustand, nicht die Vermeidung.**

### Mein §76-Ball: die Ursache ist gefunden, und sie bestätigt meinen Schluss

`b47a5d5e` trägt den Erhebungsbefehl nach — und beim Nachtragen fiel die Ursache auf:

> *„ich hatte die Fast-forwards OHNE Limit gezählt, die Paare aber mit `-60`. 18 FF aus dem vollen
> Reflog, 10 Paare aus den letzten 60 Einträgen — ZWEI GRUNDMENGEN IN EINEM SATZ."*

**Genau das hatte ich in §76 geschlossen, ohne es benennen zu können:** *„der Unterschied liegt in
der Methode, nicht in der Uhr, denn die Zahl der Fast-forwards ist in beiden Messungen identisch
18."* **Ohne Befehl brauchte es meine sechs Lesarten; mit Befehl wäre es in einer Minute gefunden
worden — er schreibt das selbst.**

### Seine neuen Zahlen nachgemessen — die zwei, die die Entscheidung tragen, treffen exakt

```
BAUM            FF  COM  PAARE   MedC   MedR  <30C  <30R      seine Angabe
plan-pruefer    19  243     18     40     87     7     0      18/241/17/43/90/7/0
planner         46  117     46    775    874     0     0      51/117/51/736/867/0/0
generator       47   52     45   2256   2470     1     0      45/ 52/43/2178/2379/1/0
evaluator       52   12     50   1930   2056     0     0      50/ 12/48/1877/2031/0/0

SUMME  159 Paare · unter 30 s: Commit-Uhr 8 · Reflog-Uhr 0    seine: 8 von 159 · 0 von 159
```

**Die Gesamtzahl 159, die acht und die null treffen zeichengenau — und die `<30`-Spalten stimmen
Baum für Baum.** Die kleinen Abweichungen bei Medianen und Paarzahlen sind Zeit: drei Bäume sind
gewachsen.

**Eine Zahl kann Zeit NICHT erklären:** der Planner-Baum geht von seinen 51 auf meine 46 —
**abwärts**. Nachgesehen: dieser Baum trägt **53 merge-Einträge, davon 46 mit `Fast-forward`**,
dazu **2 `reset`-Einträge** — als einziger der vier. *Mein Filter zählt nur Fast-forwards; welchen
er benutzt hat, weiß ich nicht und diagnostiziere es nicht.* **Auf die Entscheidung wirkt es nicht:
die tragenden Summen sind identisch.**

**Ball beim Release-Prüfer geschlossen** — der Erhebungsbefehl steht, beide Uhren sind benannt, und
er entscheidet ausdrücklich nicht, welche gilt. *Offen bleibt bei ihm nur noch der
`.gitignore`-Eintrag aus §74.*

---

## 80 — Posten (b) an W-39: neun Zusagen, neun Treffer — und die zweite Quelle von W-27/1s Regel ist der SPIEGELFALL zu W-34

**Stand:** HEAD `4323dbf3`, getrackt 0, `?? scripts/__pycache__/` liegt weiter. **Messstand in
Variable, Gegenprobe: unbewegt.**

**W-27/1 nennt zwei Quellen für seine Fangproben-Regel: W-34-1 und W-39-5.** §73 hat W-34
gemessen. **Hier die zweite — und sie versagt in die GEGENRICHTUNG.**

### Neun Zusagen, am heutigen Stand nachgezählt

```
grep -rl 'HausplanerStudio' __tests__/            8 Dateien   Blatt sagt ACHT      TRIFFT
fussleistenEhrlich.test.ts:9                      'Der Massstab ist derselbe: sagen, was
                                                   da ist, statt zu versprechen, was kommt' TRIFFT
fussleistenEhrlich.test.ts:14-15                  'Eine gezaehlte Zahl kann nicht veralten;
                                                   eine abgetippte schon.'                  TRIFFT
stilschicht.test.ts:809                           test('T2/K-05: der Weg in die gefuehrte
                                                   Planung ist direkt erreichbar', …)       TRIFFT
stilschicht.test.ts:814                           assert.match(studio, /modeBtn\('guided',
                                                   'Geführte Planung'/, 'der direkte Weg …') TRIFFT
HausplanerStudio.tsx:111                          {modeBtn('guided', 'Geführte Planung', …)} TRIFFT
W-39-6  import-Zeilen 14 minus React 1 = 13 Module                                          TRIFFT
```

**Die 13 zusätzlich über die NAMEN gegengeprüft**, nicht nur gezählt: `hausplanerStore` ·
`ConfigWizard` · `FachFlaeche` · `GuidedView` · `HausplanerApp` · `StartView` · `dialogFokus` ·
`fachFlaechen` · `fahrschritte` · `speicherAnzeige` · `uiState` · `studioDaten` · `studioUi`.
**Genau dreizehn, einzeln benannt.**

**Und alle drei berührten Dateien sind byte-identisch zur Basis `d53806f6`.**

### Der Spiegelfall — und der Evaluator hat ihn selbst benannt

```
W-34-1   das Blatt behauptete einen WAECHTER   ->  die Probe fing NICHTS   1698 pass · 0 fail
W-39-5   das Blatt behauptete eine LUECKE      ->  der Waechter EXISTIERT  1698 tests · 1 FAIL
```

W-39s Blatt schrieb an **zwei** Stellen *„KEIN TEST — K-05 ist nur im Kommentar belegt"*. Der
Evaluator hat es gefahren, den Schalter aus `:111` entfernt — **1 FAIL**, und der fallende Test
heißt wörtlich `T2/K-05`. **Der Wächter trägt die Kennung sogar im Namen und sitzt in einer Datei,
die derselbe Bau unter seinen acht aufführt.** `urteil: NICHT ERFUELLT`.

**Die Verallgemeinerung steht in seinem eigenen Wortlaut, und ich beanspruche sie nicht:**

> *„Eine behauptete Lücke, die es nicht gibt, ist derselbe Schaden wie ein behaupteter Wächter,
> den es nicht gibt; **W-34 war der andere Fall derselben Klasse**."*

### Warum W-27/1s Regel dann BEIDE fängt

**Weil sie nicht nach dem Wächter fragt, sondern nach der Bewegung des Zählers:**

```
behaupteter Waechter, den es nicht gibt   ->  Probe bleibt GRUEN, obwohl sie fallen muesste
behauptete Luecke, die es gibt            ->  Probe FAELLT, obwohl nichts fallen duerfte
```

**Beide Male weicht der Zählerstand von dem ab, was das Blatt ansagt — und genau das misst
W-27/1s *„sie wird gefahren und muss FALLEN"*.** *Die Regel ist deshalb schärfer als ihre beiden
Anlässe: sie prüft nicht die Behauptung, sondern ihre Vorhersage.*

### Und eine Methode, die ich mir merke

W-39-6 trägt sie ausgeschrieben: *„Ich habe die beiden Registerzahlen SELBST gezählt, **bevor** ich
das Blatt geöffnet habe."* **Nicht nachrechnen, sondern vorrechnen** — dann kann die Zahl des
Blattes das eigene Ergebnis nicht mehr färben. *Das ist die stärkere Form dessen, was ich jede
Runde tue, und sie kostet nichts.*

**Kein Ball, kein Fund.** **Sechstes Blatt ohne Abweichung: W-08/1 · W-11/1 · W-27/1 · W-34 ·
W-23 · W-39** — *wobei W-39 seine eigene Nicht-Erfüllung trägt und gerade deshalb hält.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 81 — Posten (c) an N-003: die Rechnung hält auf elf Werte genau — und die Belegstelle daneben zeigt ins Leere

**Stand:** HEAD `deccf4b5`, getrackt 0, `?? scripts/__pycache__/` liegt weiter. **Messstand in
Variable, Gegenprobe: unbewegt.**

**Nach der Methode aus §80 — erst gerechnet, dann das Blatt geöffnet.** Die Gegenstände habe ich
aus dem **REGISTER** geholt, nicht aus dem Blatt:

```
VORGERECHNET, Blatt ungeoeffnet
  app/EngineFlaeche.tsx           199 Z.   Register sagt 199 (berichtigt von 196)   TRIFFT
  Werkbank W-37 rekursiv          7 Dateien · 512 Z.   Register sagt 512            TRIFFT
```

### Die Rechnung — von Hand und am echten Modul, unabhängig

Fall: Gebäudebreite 10 m · Neigung 40° · Sparrenabstand 0,7 m · 80×200 mm · C24 ·
Schneezone 2 · Geländehöhe 300 m · Eigenlast 0,9 kN/m².

```
                        VON HAND        MODUL (esbuild, echt gefahren)
Sparrenlaenge            6.527 m         6.527 m
Bodenschneelast sk        0.89            0.89
Schneelast s              0.47            0.47
wPerp                     0.944           0.944
Moment M                  5.03 kNm        5.03 kNm
sigma_m,d                 9.43            9.43
f_m,d                    16.62           16.62
Ausnutzung Biegung        0.57            0.57
Durchbiegung             27.3 mm         27.3 mm
Grenze L/300             21.8 mm         21.8 mm
Ausnutzung Durchbiegung   1.25            1.25
bestanden                FALSE           false
```

**Elf Werte, zwei unabhängige Wege, keine Abweichung.** *Ich habe die Formelsammlung gelesen und
selbst gerechnet, bevor ich das Modul übersetzt habe — die Zahlen des Codes konnten meine nicht
färben.*

**Der Fall ist lehrreich, und deshalb habe ich ihn gewählt:** die **Biegung besteht** (0,57), die
**Durchbiegung fällt** (1,25). `bestanden: false` kommt allein aus dem zweiten Nachweis. *Ein
Werkzeug, das nur die Biegung prüfte, hätte hier „hält" gesagt — und N-003s eigener Text nennt für
genau diesen Ausgang das Wort Personenschaden.* **Beide Nachweise sind da und beide wirken.**

**Die A-14-Auflage („keine stille Zahl") ebenfalls geprüft:** jedes Ergebnis trägt
`vorbehalt: "Vorbemessung, ersetzt keine prüffähige Statik"` — unbedingt im Rückgabeobjekt, nicht
an eine Bedingung geknüpft.

### Und der Fund: die Belegstelle derselben Formel zeigt ins Leere

Die FORMELSAMMLUNG schreibt zu N-003:

> **Belegstelle:** `geometry/sparrenBerechnung.ts:86`, `berechneSparren(e)`

```
:86 heute                     ' *'   (leere Kommentarzeile)
berechneSparren liegt auf     :105                        Versatz +19
```

**Der Zeiger war beim Schreiben RICHTIG — und wurde zwei Stunden später ungültig:**

```
717eb11c  12.08 00:30  planner: Gruppe N angelegt, Belegstelle :86 geschrieben
e0722979  12.08 02:46  generator: A-14 Bau — 131 -> 151 Zeilen, Funktion :86 -> :105
```

**Es ist A-14s Bau, der ihn verschoben hat — der Auftrag, dessen Zweck es war, den
N-003-VORBEHALT in die Ausgabe einzubauen.** *Die Arbeit, die die Formel sicherer macht,
entwertet den Verweis auf ihre Belegstelle. Sechster Fall dieser Klasse heute Nacht, und der mit
dem höchsten Einsatz: N-003 ist das Fach-Gate, das bei Yama liegt.*

### Und hier steht der A/B-Versuch zu §79 in EINER Zeile

**Dieselbe Belegstelle nennt BEIDE Formen nebeneinander:**

```
sparrenBerechnung.ts:86     die ZAHL      -> zeigt heute auf eine leere Kommentarzeile
berechneSparren(e)          die KENNUNG   -> loest heute exakt auf, Zeile 105
```

**Ein Leser, der dem Namen folgt, landet richtig; wer der Zahl folgt, landet im Nichts — in
derselben Zeile, geschrieben in derselben Minute.** *§79 hat gezeigt, dass „wachse, wo niemand
hinzeigt" trägt. Das hier ist der andere Beleg: wo beide Formen nebeneinanderstehen, überlebt die
Kennung und die Zahl nicht. Man muss sich nicht zwischen ihnen entscheiden — es genügt, die Zahl
wegzulassen, denn der Name steht schon da.*

**Ball beim Planner** (FORMELSAMMLUNG, N-003-Belegstelle · gehört zum §77/§79-Ball).
*Für Yama ändert sich am Fach-Gate nichts: die Rechnung ist geprüft und hält, der Vorbehalt wird
unbedingt ausgegeben.* **Kein Zustandsfeld angefasst, kein Bau.**

---

## 82 — Posten (d) auf den Yama-Stapel: es sind ZWÖLF Bälle, nicht acht — und acht davon tragen keine Kennung

**Stand:** HEAD `113faf6e`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Die Wache trägt mir acht Yama-Posten aus dem Gedächtnis auf. Ich habe stattdessen den Bestand
gelesen.**

### Drei der acht frisch gemessen

```
N-003 Fach-Gate    FORMELSAMMLUNG:784 'von Yama festgelegt 12.08., DAUERGELB'
                   -> ENTSCHIEDEN, nicht mehr bei Yama.  (Rechnung in §81 auf 11 Werte geprueft)
A-13               zustand BETRIEBSBESTAETIGT · ballbesitz '—  # Kette vollstaendig,
                   P2 vom Evaluator gegengeprueft und geschlossen'
                   -> nicht mehr bei Yama.
W-21L              DECISION_BLOCKED · 7241 min · 2061 Commits seit Schnitt
                   -> BLEIBT bei Yama, mit ZWEI benannten Fachfragen.
```

### W-21Ls Sperre — ich hätte sie fast für aufgelöst erklärt

W-21Ls Ballfeld sagt: *„— # bis Yama die Fachdaten liefert **oder W-23 sie erzeugt**"*. **Und §75
hat W-23 durchgerechnet: sieben Modelle mit verifiziertem Lattmaß-Bereich, alle sieben nachgezählt.**
*Ich war eine Messung davon entfernt, „Weg b ist eingetreten" zu melden.*

**Die Tafelzeile trägt die Korrektur bereits — und sie ist vorsichtiger, als ich gewesen wäre:**

> *„OPERANDEN-GATE STEHT — meine frühere Aussage war zu stark: W-23 trägt die Lattmaß-Spannen im
> **BLATT**, aber im Code steht nur `lattmassAbhaengigVonProdukt` als **boolean**
> (`dachformVorlagen.ts:118`) — das Flag sagt DASS, nicht WIE VIEL."*

**Selbst nachgeprüft statt geglaubt:**

```
dachformVorlagen.ts:118   'lattmassAbhaengigVonProdukt: boolean; // Deckmass/Lattung ist
                           produktabhaengig'                         TRIFFT zeichengenau
Vorkommen im Code         :1380 true · :1406 true   — zwei Flags, KEINE Zahl
```

**Weg b ist wirklich nicht eingetreten.** *Die Daten liegen im Blatt, nicht im Code — und ein
`boolean` beantwortet die Frage „wie viel" nicht.* **W-21L bleibt bei Yama, offen sind
Restausgleich und die Wahl des `n`.**

*Nebenbei: dieser Zeiger `:118` trifft — als einer von wenigen heute Nacht. Er zeigt in eine
Typdeklaration nahe am Dateianfang einer 2402-Zeilen-Datei, also oberhalb praktisch allen
Wachstums. **§79s Platzierungsregel, hier zufällig eingehalten.***

### Der eigentliche Fund: zwölf Bälle, acht ohne Kennung

```
Bloecke mit ballbesitz: yama   12
davon MIT auftrag-Feld          4   die_sicherung_steht_aber… · REGISTER · P-07 · P-09
davon OHNE auftrag-Feld         8   Z.1544 · 1758 · 1814 · 1879 · 2602 · 2685 · 2974 · 17883
```

**Acht Yama-Bälle sind durch keine Auftragssuche auffindbar.** Ihre Gegenstände, aus dem Blockinhalt
gelesen:

```
Z.1544   'auftrag_von_yama'  -> Ball: 'nur noch die eine Zahl bzw. der Mindestwinkel'
Z.1758   kein Rollenbaum sichert die eigenen Commits (aus der A-37/A-38-DoR)
Z.1814   Yamas Anweisung, seine Posten zu uebernehmen, mit der Dauerregel FRISCH zu messen
Z.1879   P2H-12 des Planners: 'der rollende Umzug hat keinen Rueckfluss'
Z.2602   A-05 und A-12 stehen beide (Messauftraege L-Kontur und F-026)
Z.2685   Yama 13.08.: alle Fragen und Aufgaben an ihn zusammenstellen
Z.2974   Vorlage Abschnitt 14: ZoneNode / materialId, zwei Faelle zusammen vorgelegt
Z.17883  Yama 14.08.: 'wie soll ich das loesen, ich moechte dass du mich vertrittst'
         -> Ball: 'FUENF Sachposten bleiben; elf sind mit diesem Eintrag geschlossen'
```

**Meine Wache-Liste nennt acht Posten aus dem Gedächtnis; der Bestand trägt zwölf Bälle, und die
Mehrzahl davon steht an keiner Kennung.** *Das ist derselbe Träger-Fehler wie §78 — dort zeigte ein
Feld auf einen Befund, der woanders lag; hier liegt der Befund an einer Stelle, die kein Feld
benennt.*

### Zwei eigene Fehler, beide vor dem Melden gefangen

```
1  meine erste Zaehlung ordnete SIEBEN Baelle 'A-04' zu — mein Muster nahm den zuletzt
   gesehenen auftrag-Schluessel statt den des BLOCKS. Am Objekt geoeffnet: die Bloecke
   gehoeren nicht zu A-04. Sieben identische Zuordnungen waren die Signatur.
2  Tafelzeile sagt 'Schnitt 717eb11c', Datensatz sagt 'basis_sha 4f0d4584' — SECHS Commits
   auseinander, KEIN Elter-Verhaeltnis. 717eb11c hat das Blatt ANGELEGT (diff-filter=A),
   4f0d4584 ist der gemessene Stand davor. Zwei verschiedene Dinge, aehnliche Namen.
```

**Punkt 2 trifft mich selbst: ich beschrifte `basis_sha` seit §71 mit „SCHNITT".** *Bei 83 von 84
Aufträgen kostet das nichts, weil keine Tafelzeile widerspricht. Bei W-21L widerspricht sie — und
sie hat recht.* **Gemessene Größe: genau eine Tafelzeile von zweien mit dieser Form. Ich melde es
in dieser Größe und nicht größer.**

**Ball bei Yama unverändert** (12 Bälle, gemessen). **Die acht kennungslosen Blöcke gehören dem
Integrator** — sie sind der Grund, warum eine Postenliste aus dem Gedächtnis entstehen musste.
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 83 — Posten (e) auf meine eigenen 39 Bälle: keiner verlangt neue Arbeit, und 31 sind gar keine Bälle

**Stand:** HEAD `6b2a24eb`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**

**Ich melde „39 Bälle" seit Beginn dieser Nacht in jeder Runde. Gemessen habe ich ihren INHALT
nie.** *Eine Zahl, die man dreißigmal wiederholt, ohne sie aufzuschlüsseln, ist eine Notiz und
keine Messung — genau das, was ich anderen vorhalte.* **Blockgenau ausgezählt, nach der Methode
aus §82:**

```
39 Bloecke tragen ballbesitz: plan-pruefer
  alle 39 mit auftrag-Feld (anders als Yamas acht kennungslose, §82)
   8 mit zustand-Feld · 31 Befundbloecke
```

### Die 31 sind keine Bälle, sondern Unterschriften

```
Befundbloecke mit meinem Ball                31
  davon mit  rolle: plan-pruefer             31   von 31
  davon mit  zeit: / mess_stand:             31   von 31
```

**Einunddreißig von einunddreißig tragen die Rolle UND den Zeitstempel des Verfassers.** Beispiel
Z.23588: `rolle: plan-pruefer` · `zeit: "2026-08-16 15:30 CEST"` · `mess_stand: HEAD 8efe568d …` ·
`titel: "FUND 1 bestätigt und die Lösung am Muster bewiesen"`. **Das ist ein ABGELIEFERTER Bericht,
kein offener Posten** — und am Ende desselben Blocks steht `ballbesitz: plan-pruefer`.

**Damit ist H-9 nicht mehr Diagnose, sondern gezählt:**

```
im AUFTRAGSblock    ballbesitz = wer muss handeln     -> eine Zuweisung
im BEFUNDblock      ballbesitz = wer hat geschrieben  -> eine Unterschrift
```

**Die Urheberschaft steht bereits in `rolle:`, 31 von 31 Mal.** *Das Ballfeld fügt als Unterschrift
nichts hinzu und als Zuweisung etwas Falsches: ein abgelieferter Befund wird von seinem Verfasser
nicht geschuldet.* **Acht von ihnen liegen an `A-41` — einem Auftrag, der `BETRIEBSBESTAETIGT` ist.
Ein Auftrag, der die ganze Kette durchlaufen hat, kann keine acht offenen Bälle tragen.**

### Und die acht mit Zustandsfeld sind fünfmal erledigt

```
Z.18691  A-38    ENTWURF   DoR gefahren, liegt Z.21476            sachlich erledigt
Z.18833  A-39    ENTWURF   DoR gefahren, liegt Z.21973            sachlich erledigt
Z.18848  A-40    ENTWURF   DoR gefahren, liegt Z.22081            sachlich erledigt
Z.25569  A-42    ENTWURF   DoR gefahren, liegt in SECHS Bloecken  sachlich erledigt
Z.18933  P-02    VORLAGE   Votum liegt Z.20993                    sachlich erledigt
Z.26461  P-03    BEFUND  ┐
Z.26499  P-04    BEFUND  ├ 'BEFUND' ist kein definierter Zustand — das ist der Befund,
Z.26584  P-04    BEFUND  ┘ den ich dem Integrator seit Runden melde. Er IST der Posten.
```

### Das Ergebnis, und es ist eine Antwort auf Wache-Punkt 3

**Von 39 Bällen verlangt KEINER neue Arbeit von mir.**

```
31  abgelieferte Berichte, als Unterschrift missverstanden
 5  gefahrene Pruefungen, deren Feld nicht nachgezogen wurde   (§78, dieselbe Klasse)
 3  der Integrator-Befund selbst — sie sind der Posten, nicht sein Traeger
```

*Ich habe die Frage „liegt etwas in meiner Bahn?" jede Runde mit einem Filter beantwortet. Diesmal
mit einer Aufzählung — und die Antwort ist dieselbe, aber jetzt belegt.*

### Was daraus folgt, und für wen

**Eine einzige Regel senkt meinen offenen Stand von 39 auf 3:** in einem Block, der `rolle:` und
`zeit:` trägt und kein `zustand:` hat, ist `ballbesitz: <dieselbe Rolle>` eine **Unterschrift** —
sie gehört nicht in die Ballortung. **Die Urheberschaft geht dabei nicht verloren; sie steht
ohnehin in `rolle:`, 31 von 31 Mal nachgemessen.**

**Ball beim Integrator** (§16, er führt die Felder) — *und es ist derselbe Ball wie §78 und §82:
dreimal derselbe Träger-Fehler, dreimal an einer anderen Stelle. In §78 zeigte ein Feld auf einen
Befund, der woanders lag. In §82 lag ein Befund, wo kein Feld ihn benennt. Hier trägt ein Feld
einen Namen, der zweierlei bedeutet.* **Kein Zustandsfeld angefasst, kein Bau.**

---

## 84 — Posten (a) an der Prozessquelle: drei Zeiger treffen, zwei nicht — und einer davon steht 30 Zeilen über seiner eigenen Berichtigung

**Stand:** HEAD `e48b056b`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**

**`docs/ARBEITSREGELN.md` ist nach CLAUDE.md die einzige verbindliche Prozessquelle.** Zehn Zeiger
aus den aktiven Blättern zeigen hinein, auf sechs verschiedene Zeilen. **Erst gemessen, dann die
Blätter gelesen.**

```
1587 Z. (16.08 19:54)  ->  1825 Z. (16.08 21:37)      +238 ueber acht Commits
```

*Und die Botschaften dieser acht handeln fast alle von Ankern:* „zwei tote Zeilenverweise in der
Prozessquelle gefunden" · „meine eigene Ankerprüfung fällt auf genau den F…" · **„VIER von elf
Ankern sind falsch — 36 Prozent"** · „Ankerlage in allen drei Quellen gemessen". **Der Planner hat
an genau dieser Datei genau dieses Problem gearbeitet.**

### Das Ergebnis, Zeiger für Zeiger

```
ZEIGER  BLATT        HEUTE                                                    URTEIL
:145    A-19 (3x)    '**Warum der Ausdruck [A-Z]+-?[0-9]+ und nicht …**'      TRIFFT
:509    A-27 (2x)    'Vor jeder CODE_FERTIG-Meldung wird JEDE beruehrte …'    TRIFFT
:693    A-36         liegt unter '## 14. Git, Commits und Veroeffentlichung'
                     (Ueberschrift Z.690) — A-36 nennt es '§14'              TRIFFT
:812    A-19 (1x)    zitiert als 'hier stand :812' — historischer Beleg      KEIN FUND
:103    A-19         siehe unten                                             FUND
:834    B7           siehe unten                                             FUND
```

### Fund 1 — die Berichtigung sitzt 30 Zeilen unter dem unberichtigten Zwilling

A-19s **Kriterium** wurde am 16.08. berichtigt, und die Berichtigung hält:

> *„ANKER BERICHTIGT 16.08.: hier stand `ARBEITSREGELN.md:103` — der Zählbefehl steht bei **125**,
> bei 103 steht Prosa. **Ein KRITERIUM mit falschem Verweis: wer es abnimmt, misst an der falschen
> Stelle.**"*

```
:125 heute   "Tafelzeile      grep -cE '^\| \*\*[A-Z]+-?[0-9]+[^|]*\| *\*{0,2}`?IN_ARBEIT' …"   TRIFFT
:103 heute   "> Regel nie.* **Derselbe Fehlertyp wie A-20s vier Zustandsorte …**"              Prosa
```

**Aber `A-19:92` — der Textblock in Abschnitt 4 desselben Blattes — trägt weiter `:103`**, mit dem
Zählbefehl daneben. **Berichtigt wurde das Kriterium, nicht der Fließtext — und der unberichtigte
Zwilling steht DREISSIG ZEILEN ÜBER seiner eigenen Berichtigungsnotiz.**

*Das ist zeichengenau die Klasse aus §68: A-34 stellte W-06s Kriterium auf den Anker um und ließ
die Zahl im Erklärtext stehen. Hier dasselbe, in derselben Datei, an einem Blatt, dessen
Berichtigungsnotiz den Schaden selbst benennt: „wer es abnimmt, misst an der falschen Stelle."*

### Fund 2 — B7s Zeiger trägt den Versatz, den der Planner selbst gemessen hat

```
B7:215 sagt   'ARBEITSREGELN.md:834 traegt die Reichweiten-Zeile: kein statischer Aufrufer ist …'
:834 heute    '`RELEASE_FREI → VEROEFFENTLICHT` dokumentieren.'
die Zeile     liegt heute auf :995
              834 + 161 = 995
```

**Und 161 ist keine Zahl von mir.** Der Planner schreibt in `15c49f96` wörtlich — von mir aus dem
Commit geholt, nicht erinnert:

> *„Beim **Regelwerk** war er einheitlich **161**, also EIN Einschub weiter oben"*

**Er hat den Versatz dieser Datei gemessen und benannt. B7s Zeiger trägt ihn exakt — und war nicht
im behobenen Satz.** *Ein bekannter, benannter, gleichmäßiger Versatz, und trotzdem steht ein
Zeiger unbehoben da: nicht weil ihn niemand berechnen konnte, sondern weil niemand die Liste der
Betroffenen vollständig hatte.*

### Was das zur Regelkollision beiträgt

§77 nannte die Kollision, §79 zeigte die Platzierungslösung, §81 den A/B-Versuch in einer Zeile.
**Hier kommt der vierte Baustein: die Behebung selbst ist unvollständig, und zwar systematisch.**

```
A-34 (§68)  Kriterium berichtigt   Erklaertext nicht     -> Zahl lebt weiter
A-19 (hier) Kriterium berichtigt   Abschnitt 4 nicht     -> Zahl lebt weiter, 30 Zeilen entfernt
```

**Zweimal dieselbe halbe Behebung, bei zwei verschiedenen Rollen, an zwei verschiedenen Dateien.**
*Wer einen Anker berichtigt, sucht die Kennung im KRITERIUM — und übersieht, dass dieselbe Zahl im
erklärenden Text daneben steht. Eine Behebung, die nur das Kriterium trifft, ist so weit von
fertig entfernt wie die Zahl von ihrem Ziel.*

**Ball beim Planner** (beide Fundstellen benannt: `A-19:92` und `B7:215`; Ziele `:125` und `:995`).
*Drei von sechs Zeigern treffen, und die eine geprüfte Berichtigung hält — das gehört dazu.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 85 — FEHLER 29: §84s zwei Funde sind KEINE. Richtig gemessen, falsch eingeordnet — und die Regel dagegen hatte ich selbst zitiert

**Stand:** HEAD `96137c83`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Posten (b) sollte §84 nachzählen. Er hat §84 widerlegt.**

### Was ich in §84 gemeldet habe

```
Fund 1  A-19:92 traegt weiter ARBEITSREGELN.md:103, dort steht heute Prosa
Fund 2  B7:215  nennt :834, die Zeile liegt heute auf :995 (+161)
Schluss "zweimal dieselbe halbe Behebung … wer einen Anker berichtigt, sucht die
         Kennung im KRITERIUM und uebersieht dieselbe Zahl im Text daneben"
```

**Die Messungen stimmen alle. Der Schluss ist falsch.**

### Der Berichtigungs-Commit trägt seine Reichweite, und ich habe sie nicht gelesen

`165c8339` (16.08. 20:18), wörtlich aus dem Commit geholt:

> *„alle **26** einzeln geöffnet — **25 sind Belege, EINER war ein Abnahmekriterium**"*

**Es war keine halbe Behebung. Es war eine vollständige Behebung mit erklärter Reichweite:**
26 Fundstellen geöffnet, 25 als Beleg klassifiziert und bewusst stehengelassen, die eine wirkende
berichtigt. **Genau das ist A-34s Regel** — und die habe ich in §68 selbst zitiert:

> *„berichtigt wird, wo der Verweis WIRKT (Produktivcode, Kriterien aktiver Blätter) — nicht alle
> 52: **in Befunden belegt eine Nummer legitim einen Stand**"*

### Meine beiden „Funde" sind Belege — am Objekt nachgesehen

```
A-19:92   steht in einem ```text-Block unter '## 4 · Der zweite Teil — der Fehler ist meiner',
          direkt gefolgt von 'Gemessen am 12.08., beide Fassungen gegen dieselbe Datei'
          -> dokumentiert einen VERGANGENEN Stand.                              BELEG
B7:215    steht unter dem Schluessel  beleg:  innerhalb von 'B7-5: GRUEN'
          -> traegt das Wort im Schluesselnamen.                                BELEG
```

**Zwei von zwei. Kein Fund bleibt übrig.**

### Und W-06 war es auch nicht — es meldet den Rest sogar selbst

Bei der Gegenprobe an W-06 (dem zweiten Fall meines §84-Schlusses) steht im Blatt, Z.324:

> *„Zweitens, **aus demselben Grund ohne Befund**: Abschnitt 6 dieses Blattes (Scope, Z.142) nennt
> weiterhin `FORMELSAMMLUNG.md:218` … **A-34s eigener Scope sagt ausdrücklich ‚Nur Kriterien —
> nicht Befund- und Belegtexte'**, und ein Scope-Abschnitt ist keines von beiden. **Auch das gehört
> gemeldet und nicht still entschieden.**"*

**Der Rest ist dort nicht übersehen, sondern geöffnet, eingeordnet, gemeldet und mit Grund
stehengelassen.** *Beide Fälle, aus denen ich ein Muster gemacht habe, sind das Gegenteil eines
Musters: zwei sauber begrenzte Behebungen.*

### Die Fehlerklasse ist neu — und sie ist die unangenehmste bisher

```
Fehler 26   Fall (1) gemessen, Fall (2) behauptet     falsch gemessen -> falsche Aussage
Fehler 27   gar nicht gemessen, richtig geraten       nicht gemessen  -> zufaellig richtig
Fehler 28   richtig gemessen, nicht gleichzeitig      Block unstimmig -> jede Zeile wahr
Fehler 29   richtig gemessen, FALSCH EINGEORDNET      Zahlen stimmen  -> Schluss falsch
```

**Bei 26 bis 28 war die Messung angreifbar. Hier ist jede Zahl richtig, und der Fehler sitzt allein
im Satz darüber.** *Eine Zahl kann man gegenprüfen; eine Einordnung nur, indem man die Regel sucht,
die sie regelt — und die stand bereit, in A-34, in W-06s eigener Meldung, im Commit-Betreff, und in
meinem eigenen §68.* **Vier Gelegenheiten, sie zu lesen. Ich habe stattdessen gezählt.**

### Was von §84 STEHENBLEIBT

```
:145 · :509 · :693     treffen                                       GILT
:812                   historischer Beleg, kein Fund                 GILT
:103 -> :125           die Berichtigung haelt, :125 traegt den Befehl GILT
Fund 1 · Fund 2        ZURUECKGEZOGEN
Schluss 'halbe Behebung'  ZURUECKGEZOGEN
```

**Und der vierte „Baustein zur Regelkollision" aus §84 fällt mit.** *§77 (Kollision benannt), §79
(Platzierungsregel wirkt), §81 (A/B in einer Zeile) bleiben — sie sind an Messungen belegt, nicht
an einer Einordnung.*

**Ball beim Planner ZURÜCKGEZOGEN**, soweit er aus §84 kam. *Was ich ihm geschickt hätte, wäre die
Aufforderung gewesen, Belege zu zerstören, die A-34 ausdrücklich schützt.* **Kein Zustandsfeld
angefasst, kein Bau.**

---

## 86 — Seine W-21L-Berichtigung bestätigt, und der Lattenabstand steht doch im Code — unter englischem Namen

**Stand:** HEAD `23cd7fdc`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
`4244f70f` zieht einen Satz zurück, **der ursprünglich von mir stammt** („W-21L liegt jetzt beim
Planner"). Er hat recht, und §82 hatte es bereits gemessen.

### Seine Zahlen — alle selbst nachgefahren, alle treffen

```
lattweite unter resources/                       0 Dateien                       TRIFFT
Granat  Code 0 · W-23-Blatt  4                                                   TRIFFT
Rubin   Code 0 · W-23-Blatt 13                                                   TRIFFT
Topas   Code 0 · W-23-Blatt  9                                                   TRIFFT
Harzer  Code 0 · W-23-Blatt 39                                                   TRIFFT
Achat   sein -i-Fehlalarm: resources/js/chat-mensions.js  (saChat…, nicht Achat) TRIFFT
die fuenf Nachbarfelder :1378-1382, zeichengenau                                 TRIFFT
```

**Seine Schlussfolgerung bestätige ich unabhängig: der PRODUKTABHÄNGIGE Lattmaß-Wert steht nicht im
Code. W-21L bleibt `DECISION_BLOCKED` und bleibt bei Yama.**

### Und zwei Zeilen unter seinem Beleg steht eine Zahl

```
:1380  lattmassAbhaengigVonProdukt: true      <- sein Beleg
:1381  rdnGrad: 22
:1382  mindestneigungGrad: 16
:1383  battenDistCm: 34                        <- Lattenabstand, MIT Zahl
```

**`battenDist` = Lattenabstand, englisch geschrieben.** Deshalb liefert eine Suche auf `lattweite`
oder `lattmass` null Treffer, und deshalb sieht das Feld aus wie ein reines Ja/Nein.

**Vier verschiedene Werte, gemessen:**

```
battenDistCm: 0 · 30 · 34 (2x) · 40 (2x)
gebunden an die EINDECKUNGSART: :1758 'schiefer' · :1796 und :1842 'trapezblech'
```

**Und er WIRKT — Verbraucher über den Funktionsnamen belegt, nicht über die Ordnerlage (P7):**

```
roof.blade.php:1093   const numBattens = Math.max(1, Math.floor(slopeLength / dim.battenDist));
                      -> die Lattenzahl entsteht durch Division DURCH diesen Wert
dachWerte.ts:92       pruefe(b.battenDist, DACH_FLOOR_CM.battenDist, "Lattenabstand")
dachWerte.ts:20       battenDist: 0.05  // Lattenabstand  min 5 cm
                      -> Untergrenze 5 cm, mit Nutzertext 'wurde auf den gueltigen
                         Mindestwert (5 cm) gesetzt'
```

### Was das ändert — und was ausdrücklich NICHT

**NICHT geändert:** seine Aussage trägt. `battenDistCm` hängt an der **Eindeckungsart**, nicht am
**Ziegelmodell**; kein einziger Braas-Modellname steht im Code (0 von 5, oben gemessen).
**W-21Ls Operand — der Lattmaß-Bereich JE PRODUKT — fehlt weiterhin.**

**Geändert ist der Schritt von der Messung zur Aussage:** *„`lattweite` 0 Treffer"* → *„keine Zahl
im Code"* ist ein Schluss über die **Schreibweise**, nicht über die **Sache**. **Genau die Klasse,
die der Planner in `acb3d494` an sich selbst benannt hat:** *„die ursprüngliche Messung suchte zwei
feste Zeichenfolgen und maß damit die SCHREIBWEISE statt der Sache."*

**Und es zählt für den nächsten Schritt, um den es ihm ging:** wer W-21L nachschneidet, baut nicht
in leeren Raum. **Es gibt bereits einen Lattenabstand, der gerechnet, geprüft und nach unten
begrenzt wird.** *Ein produktabhängiger Wert müsste sich zu ihm verhalten — ersetzen, überschreiben
oder danebenstehen —, und diese Frage gehört in den Nachschnitt, nicht in den Bau.*

**Ball beim Release-Prüfer** (seine Postenlage, ein Satz zu ergänzen: *nicht* „keine Zahl im Code",
sondern „keine **produktabhängige** Zahl — ein Lattenabstand je Eindeckungsart existiert").
*Sein Urteil zu W-21L bleibt unangetastet; ich habe es bestätigt, nicht bestritten.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 87 — §83 war zu bequem: vier DoRs sind NICHT ERTEILT, die Blätter seither überarbeitet, und die zweite Runde habe ich nie gefahren

**Stand:** HEAD `a3356a5f`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Posten (d) sollte einen Stillstand messen. Er hat einen gefunden, und es ist meiner.**

### Der Reflex, den ich diesmal unterdrückt habe

Ich wollte melden: *„vier ENTWURF-Aufträge, DoR gefahren, Zustand steht seit 12 Stunden —
Stillstand beim Planner."* **Vor dem Schluss die Regel gesucht (Lehre aus §85): eine DoR kann auch
VERWEIGERT werden, und dann ist `ENTWURF` der richtige Zustand.** Gemessen:

```
A-38 / A-37 Runde 3   NICHT ERTEILT
A-39 Runde 1          NICHT ERTEILT
A-40 Runde 1          NICHT ERTEILT
A-37 Runde 4          NICHT ERTEILT
```

**Vier von vier verweigert. Es gibt keinen Stillstand beim Planner — `ENTWURF` ist korrekt.**

### Aber die Blätter sind seither überarbeitet worden

```
AUFTRAG  meine DoR      letzte Blattaenderung          Abstand
A-38     16.08 13:01    e15d3677  16.08 20:57          7 h 56
A-39     16.08 14:18    315f33ee  16.08 21:22          7 h 04
A-40     16.08 14:26    65b83ee9  16.08 22:11          7 h 45
A-42     (Bloecke)      6da4e914  16.08 22:52
```

**Und eine zweite Runde existiert nicht:** `a3[89]_dor_runde_2` / `a4[02]_dor_runde_2` → **0
Treffer**. Die vorhandenen Runden sind `a39_dor_runde_1`, `a40_dor_runde_1`, `dor_runde_3` und
`dor_runde_4` (beide A-37/A-38).

**Alter seit meiner Verweigerung: A-38 737 min / 845 Commits · A-39 660 / 821 · A-40 652 / 818.**

### Damit ist §83 in seiner Kernaussage falsch

§83 schloss: *„Von 39 Bällen verlangt KEINER neue Arbeit von mir"*, mit den vier ENTWURF als
**„sachlich erledigt"**. **Das ist zweifach falsch:**

```
1  'erledigt' ist eine verweigerte DoR nicht — sie ist eine abgeschlossene Pruefung
   mit NEGATIVEM Ergebnis, und der Auftrag bleibt zu Recht offen.
2  die Blaetter haben sich seither geaendert — genau das loest die zweite Runde aus,
   und die schulde ICH.
```

**Der Fehler steckt in der Messgrundlage: ich habe gegen die FELDER gemessen und nicht gegen den
BLATTSTAND.** *Die Felder sagten „DoR liegt vor" — richtig. Sie sagen nicht, mit welchem Ergebnis
und ob das Blatt seither weitergezogen ist.* **Ein Feld beantwortet „ist etwas passiert", nicht „ist
etwas fällig".**

### Was §78 dazu SCHÄRFER macht statt schwächer

§78 meldete `dor_beleg: "steht aus"` an Aufträgen, deren DoR nachweislich lief. **Das gilt weiter —
und jetzt genauer:** *„steht aus" ist nicht nur veraltet, es ist die falsche Kategorie.* **Eine
gefahrene und verweigerte Prüfung steht nicht aus; sie liegt vor und lautet NICHT ERTEILT.**
Das Feld müsste das Ergebnis und sein Datum tragen — dann hätte ich §83 nicht falsch schließen
können, weil die Antwort im Feld gestanden hätte.

### Meine Bahn ist nicht leer — sie war es nie in dieser Nacht

```
faellig: DoR Runde 2 fuer A-38 · A-39 · A-40 · A-42
grund:   Urteil NICHT ERTEILT + Blatt seither geaendert
aeltester Rueckstand: A-38, 737 Minuten und 845 Commits
```

**Die Vorratsprüfung ist Yamas Regel für den Fall, dass nichts offen ist. Seit rund zwölf Stunden
war etwas offen, und ich habe stattdessen Vorrat geprüft.** *Zwanzig Runden lang habe ich „39 Bälle,
nichts in meiner Bahn" gemeldet — die Zahl war richtig, der Satz daneben nicht.*

**Kein Ball an andere. Der Rückstand ist meiner.** **Nächste Runde beginnt die zweite DoR, mit dem
ältesten: A-38.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 88 — DoR Runde 2 für A-38: ERTEILT. Beide Restpunkte behoben, Rot-Lagen halten, und die Grundlage reicht weiter als das Blatt sagt

**Stand:** HEAD `266c0055`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**A-38 `ENTWURF`, `basis_sha 0f05f8bf`, `dor_runde_2` = 0 Treffer — der Rückstand aus §87.**
**Blattstand `e15d3677` (16.08. 20:57), also 7 h 56 nach meiner Verweigerung.**

### Meine zwei Restpunkte aus Runde 3 — beide nachgemessen

```
R6  'A-38 traegt die als falsch berichtigte Zahl in der UEBERSCHRIFT'
    heute Z.1:  '# A-38 — Merges laufen am Tor vorbei, und keiner traegt eine Rollenmarke'
                                                                          BEHOBEN, zahlfrei

R5  'die drei tragenden Zahlen (497/70/59) nennen KEINEN Messbefehl'
    heute Z.79-86: ein bash-Block 'FESTE ERHEBUNG — ein benannter Tag statt eines
    wandernden Fensters' mit --since='2026-08-16 00:00', und der alte 48-h-Befehl
    daneben als Beleg mit 'NICHT mehr benutzen' (A-20-4)                  BEHOBEN
```

**Den Befehl selbst gefahren, im Baum, den er nennt:**

```
Blatt (Kommentar, 16.08. abends)   472 Commits · 188 Merges · 40 %
meine Messung jetzt                855 Commits · 369 Merges · 43 %
alter 48-h-Befehl zum Vergleich    916 Commits   (ausdruecklich nicht mehr zu benutzen)
```

**Die Absolutzahlen sind gewachsen, der Anteil hält — und die Blattzahl trägt ihren eigenen
Zeitstempel („gemessen 16.08. abends").** *Ich war nahe daran, das als veraltete Zahl zu melden;
eine Zahl MIT Standangabe ist aber genau die Form, die P2 verlangt. Zweite Anwendung der §85-Lehre
in derselben Nacht.*

### Die neun Kriterien tragen keine Anlass-Zahl

```
A-38-1 .githooks/commit-msg existiert und ist ausfuehrbar
A-38-2 Negativfall Merge ohne Marke · A-38-3 Positivfall mit Marke
A-38-4 normaler Commit unberuehrt  · A-38-5 core.hooksPath gesetzt
A-38-6 Hook greift im ZWEITEN Worktree ohne dortige Einrichtung
A-38-7 sechs Kanten, K6 ausdruecklich als nicht abfangbar
A-38-8 kein Nicht-Ziel beruehrt   · A-38-9 Suite gruen GEGEN DEN BAU-STAND
```

**Kein Kriterium nennt 59/497/70 oder 188/472. A-38-9 ist an den BAU-STAND gebunden statt an eine
feste Zahl** — genau die Umstellung, die ich in Runde 1 verlangt hatte.

### Rot-Lagen selbst gemessen — beide halten

```
.githooks/                        existiert NICHT          -> A-38-1 kann rot werden
core.hooksPath, sechs Baeume      ueberall leer, exit=1     -> A-38-5s Rot-Beleg haelt
  ticket · planner · plan-pruefer · generator · evaluator · release-pruefung
```

### Und eine Grundlage, die weiter reicht als das Blatt sagt

A-38-6 spricht vom *„ZWEITEN Worktree"*. Gemessen:

```
git rev-parse --git-common-dir  ->  alle Baeume zeigen auf /Users/…/ticket/.git
git worktree list               ->  15 registrierte Worktrees
```

**Eine einzige `core.hooksPath`-Einstellung erreicht damit fünfzehn Bäume, nicht zwei** — darunter
`ticket-rolle-release` in *detached HEAD*. **Das ist kein Einwand, sondern ein Argument für den
Auftrag: die Wirkung ist siebenmal größer als das Kriterium verspricht, und A-38-6 bleibt trotzdem
mit einem Befehl erfüllbar.** *Ich melde es, damit der Bauende die Reichweite kennt, bevor er sie
setzt.*

## VOTUM

```
DoR Runde 2 fuer A-38:  ERTEILT
  R5 behoben, Befehl selbst gefahren und reproduziert
  R6 behoben, Ueberschrift zahlfrei
  neun Kriterien, keines mit Anlass-Zahl, A-38-9 standgebunden
  zwei Rot-Lagen selbst gemessen, beide halten
  kein neuer Restpunkt
```

**Ich fasse den Zustand NICHT an.** *Das Blatt steht auf `ENTWURF`; der Übergang nach `BEREIT`
gehört dem Planner, und meine Rolle endet mit dem Votum.* **Ball beim Planner.**
**Kein Zustandsfeld angefasst, kein Bau.** **Offen bei mir: DoR Runde 2 für A-39 · A-40 · A-42.**

---

## 89 — DoR Runde 2 für A-39: ERTEILT. Der eine Restpunkt ist behoben, und drei Kriterien mehr sind dazugekommen als ich je geprüft hatte

**Stand:** HEAD `23bcf978`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**A-39 `ENTWURF`, `basis_sha 99add90f`. Blattstand `315f33ee` (16.08. 21:22), 7 h 04 nach meiner
Verweigerung.** Zweiter Posten des Rückstands aus §87.

### Mein Restpunkt aus Runde 1 — behoben, und mehr als das

Runde 1 lautete: *„NICHT ERTEILT, ein Restpunkt, ein SHA"* — A-39-3 nannte keinen Stand.

```
heute A-39-3:  P2 findet A-33-1 ('genau EINS', Stand 8559b555)
               und A-37-11 ('Suite 1750', Stand 7ef8f046, 14.08. 22:35)
```

**Beide Stände am Objekt geprüft, nicht am Betreff:**

```
8559b555  A-33-1 steht dort (Z.175) und traegt 'genau EINS'          1 Treffer
heute     dieselbe Datei, dasselbe Muster                            0 Treffer
          -> die tragende Zusage 'dieselbe Datei, zwei Staende, zwei Antworten' HAELT
7ef8f046  A-37-11 (Z.131): 'Suite gruen und Zahl unveraendert
          (Stand bc2125d9: 1750), tsc exit=0'                        zeichengenau
```

**Und `7ef8f046` ist derselbe SHA, den ich in Runde 1 für A-39-4 als FALSCH gemeldet hatte.**
*Er ist für A-39-4 falsch und für A-39-3 richtig — zwei Fälle, ein Stand, zwei Antworten. Das
Blatt trennt sie korrekt.*

**A-39-4 trägt heute BEWUSST keinen SHA**, mit dem Grund im Kriterium: *„Kein SHA im Kriterium,
bevor er am Fall geprüft ist. **Ein falscher Stand ist schlimmer als keiner: er sieht geprüft
aus.**"* **Das ist die richtige Auflösung eines falschen Belegs — nicht ersetzen, sondern die
Ermittlung an den Bau binden.**

### Drei Kriterien mehr, als ich je gesehen habe

```
Runde-1-Pruefstand 2624062b   10 Kriterien · 146 Zeilen
heute                         13 Kriterien · 285 Zeilen   lueckenlos A-39-1 bis -13
```

**A-39-11 (P6), A-39-12 (P7) und A-39-13 (P8) sind nach meiner Runde 1 entstanden — ich hatte sie
nie geprüft. Jetzt geprüft:**

```
A-39-12 (P7)  Positivproben  A-41-4 @ a613100e · A-41-5 @ 74cc04d5 · A-37-18 @ 78841603
              Negativprobe   die heutigen Fassungen derselben drei NICHT
              Grenze         'P7 prueft, ob die Fragen beantwortbar sind — nicht,
                              ob die Antwort klug ist'
A-39-13 (P8)  Positivproben  Regel-Ergaenzung @ e802c1f8 · W-17-1-3 vor d7f0c93d
                             · Baum-Erhebung in A-37-18 @ 78841603
              Negativprobe   ein Pfad ALS BEISPIEL ist kein Fund
              Grenze         'P8 prueft die Suchvorschrift, nicht das Ergebnis'
```

**Alle acht genannten Stände existieren** (`a613100e` · `74cc04d5` · `78841603` · `e802c1f8` ·
`d7f0c93d` · `0ee521f7` · `5db5f8a9` · `5bbc55bf`), **und die zwei, deren Zuordnung an der
Oberfläche gekreuzt aussah, habe ich einzeln geöffnet:**

```
A-41-5 @ 74cc04d5   steht dort auf Z.176   (Commit-Betreff nennt A-41-4 — beides im selben Commit)
A-41-4 @ a613100e   steht dort auf Z.164
```

*Ich hätte hier fast einen Zuordnungsfehler gemeldet. Der Betreff nannte die andere Nummer; die
Datei nennt beide.*

### Rot-Lage und Eigenprüfung

```
A-39-1 Rot-Lage    scripts/blatt-pruefen.sh existiert NICHT       haelt
P2 an A-39 selbst  jedes fallbezogene Kriterium nennt einen Stand ODER bindet ihn
                   an den Bau (A-39-4) · A-39-10 'gegen den Bau-Stand'
                   -> das Blatt, das P2 definiert, besteht P2
```

## VOTUM

```
DoR Runde 2 fuer A-39:  ERTEILT
  Restpunkt aus Runde 1 behoben, beide Staende am OBJEKT geprueft
  drei neue Kriterien geprueft, alle mit Positiv-, Negativprobe und Grenze
  acht Staende existieren, zwei einzeln geoeffnet
  Rot-Lage haelt · A-39 besteht seine eigene Pruefung P2
  kein neuer Restpunkt
```

**Ich fasse den Zustand NICHT an.** *Der Übergang gehört dem Planner.* **Ball beim Planner.**
**Offen bei mir: DoR Runde 2 für A-40 · A-42.** **Kein Zustandsfeld angefasst, kein Bau.**

### Nachtrag zu §89 — meine A-42-Zuordnung aus §87 war falsch, und A-38s trägt trotzdem

**Der Baum wanderte mitten in der Runde** (`23bcf978` → `6148c5df`). **A-39 über die Bewegung
unverändert: 285 Zeilen, 13 Kriterien, `8559b555` an Ort und Stelle — §89 steht.**

Dazwischen kam `99809071`: *„A-42 hat KEINE verweigerte DoR sondern eine unvollständige."*
**Selbst nachgemessen, und er hat recht:**

```
docs/STATUS.md:26054  stand_der_A_42_dor:
  'K4 durch meine eigene Zaehlung bestaetigt (24), K5 noch offen.'
  'Offen: -3, -4 (mit dem Hinweis oben), -5, -6, -7, -8.'
A-42-Blatt: 12 Kriterien
  -> drei geprueft · sechs ausdruecklich offen · drei nicht einmal erwaehnt
```

**§87 hat A-42 in dieselbe Liste gestellt wie A-38/A-39/A-40. Das ist falsch:** *A-42 hat keine
verweigerte Runde 1, sondern eine **begonnene und unvollständige**.* **Was ich schulde, ist nicht
Runde 2, sondern der REST von Runde 1** — sechs benannte Kriterien, drei unerwähnte und K5.

### Seine A-38-Aussage prüfe ich mit, und da liegen wir nur an verschiedenen Trägern

Er misst *„explizite DoR-Ergebnis-NICHT-ERTEILT-Meldung im Commit-Betreff: A-38 null"*.

```
Commit-Betreffe mit A-38 UND 'NICHT ERTEILT' (loseres Muster)        9
docs/STATUS.md Z.21477  titel: 'DoR Runde 3 fuer A-37 und A-38 —
                        NICHT ERTEILT, fuenf Restpunkte'             1, im Datensatz
```

**Kein Widerspruch: er sucht eine bestimmte Meldeform im Betreff, ich lese den Datensatz.** *Nach
§16 ist `docs/STATUS.md` die Statuswahrheit, nicht die Betreffzeile — dort steht A-38s Verweigerung
im Klartext.* **§88 (DoR Runde 2 für A-38) war damit richtig geschnitten.**

### Mein Rückstand, berichtigt

```
A-38   Runde 2   ERTEILT (§88)
A-39   Runde 2   ERTEILT (§89)
A-40   Runde 2   offen — verweigerte DoR, Blatt seither geaendert
A-42   REST VON RUNDE 1 — sechs offene Kriterien, drei unerwaehnte, K5
```

*Die Zahl bleibt gleich, die Arbeit ist eine andere. Er hat mir keinen Posten abgenommen, sondern
einen falsch benannten richtig benannt — und das ist mehr wert.*

---

## 90 — DoR Runde 2 für A-40: NICHT ERTEILT, zwei Restpunkte. R1 ist behoben, R2 nicht, und A-40-5s eigener Zählbefehl fällt in P8

**Stand:** HEAD `ee5ac878`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**A-40 `ENTWURF`, `basis_sha 99add90f`, Blattstand `65b83ee9` (16.08. 22:11).** Dritter Posten
des Rückstands aus §87.

### R1 — behoben

Runde 1: *„der Anlass nennt F-054 als ‚von keiner Prüfstation gefunden' — belegt falsch."*

```
heute Z.35-38:  '⚠ BERICHTIGT nach Befund des Plan-Pruefers: „von keiner Pruefstation
                 gefunden" trifft auf F-054 NICHT zu. Sie ist beim RECHNEN aufgefallen —
                 das ist eine Pruefstation, nur keine formale. Zwei der drei stimmen,
                 der dritte nicht.'
                 Dazu eine Tabelle mit allen drei Faellen und ihrem Fundort.  BEHOBEN
```

### R2 — nicht behoben, mit zehn Mustern gesucht

Runde 1: *„A-40-5 verlangt 21 neue Ampeln, ohne zu sagen, wer den Erstzustand vergibt."*

```
gesucht: erstzustand · wer vergibt · erstbelegung · vergabe · wer setzt · wer traegt ein
         zustaendig · Fach-Pruefer · setzt die Ampel · erste Ampel · initial
Treffer im Blatt: 0
```

**Die Frage ist offen. Wer eine Ampel zum ersten Mal setzt, ist eine Fachentscheidung —
CLAUDE.md verlangt dafür Rückfrage statt stiller Automatisierung.** *Ein Bauender müsste sie
selbst treffen, und genau das darf er nicht.*

### R3 (neu) — die Reduktion 87 → 64 stimmt nicht, und der Befehl fällt in P8

A-40-5 trägt einen Zählbefehl und daneben: *„Der Befehl zählt ZEILEN, nicht Kennungen:
**87 Zeilen → 64 Kennungen**."* **Selbst gefahren:**

```
Zeilen ohne Ampel                          87   TRIFFT (an beiden Staenden)
davon VERSCHIEDENE Kennungen               54
Kennungen MIT Ampel                        10
54 + 10                                  = 64   <- die Zahl des Blattes
Vereinigung beider Mengen                  62   -> zwei doppelt gezaehlt
```

**Die 64 ist keine Reduktion der 87, sondern eine Summe zweier Mengen mit Überschneidung.**
*Die richtige Reduktion lautet 54.*

**Und die Überschneidung ist der eigentliche Fund. Zwei Kennungen tragen beide Formen — eine
davon ist MEIN Musterfehler, die andere echt:**

```
F-020   'F-026 · Dach ueber vorgegebene Grundform (Alternative zu F-020) · 🟢'
        -> mein grep zog F-020 aus F-026s UEBERSCHRIFT.  MEIN FEHLER, kein Fund.
N-003   :754  '### N-003 · Sparren-Vorbemessung … · 🟡 FACH-GATE'
        :784  '### N-003 · Geltungsbereich — von Yama festgelegt 12.08., DAUERGELB'
        :814  '### N-003 · AUFLAGE an die Ausgabe — keine stille Zahl'
        -> DREI Definitionszeilen, eine mit Ampel, zwei ohne.  ECHT.
```

**A-40-5 verlangt „jede Definitionsstelle trägt eine Ampel". Der Befehl zählt damit die zwei
UNTERABSCHNITTE von N-003 als eigene Definitionsstellen und würde zwei Ampeln fordern, wo eine
Kennung eine trägt.** *Geltungsbereich und Auflage sind keine zweite und dritte Formel — sie sind
Teile derselben.*

**Das ist P8 in genau der Form, die A-40 selbst definiert: der ORT ist das Kriterium.** *Der Befehl
misst Überschriftenzeilen und nennt sie Definitionsstellen. A-40-5 hat diesen Fehler schon einmal
an sich selbst gefunden (der Suchraum war eine Datei statt des Verzeichnisses) — dies ist dieselbe
Klasse, eine Ebene tiefer.*

## VOTUM

```
DoR Runde 2 fuer A-40:  NICHT ERTEILT, zwei Restpunkte
  R1  F-054 im Anlass                                        BEHOBEN
  R2  wer vergibt den Erstzustand der 21 Ampeln              OFFEN, zehn Muster gesucht
  R3  '87 Zeilen -> 64 Kennungen' ist 54; der Befehl zaehlt
      Unterabschnitte einer Kennung als Definitionsstellen   NEU
```

**Nicht geprüft und so gekennzeichnet:** die sechs Kanten A-40-7 und A-40-2s *„echter Fall"* —
welcher, steht weiterhin nicht im Kriterium. **Beides bleibt für Runde 3.**

**Ich fasse den Zustand NICHT an. Ball beim Planner.** **Offen bei mir: A-42, Rest von Runde 1.**
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 91 — A-42, Rest von Runde 1: NICHT ERTEILT, ein Restpunkt — und er ist derselbe, den ich am 16.08. gemeldet habe, jetzt am vierten Stand

**Stand:** HEAD `ad5a4b97`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**A-42 `ENTWURF`, `basis_sha e802c1f8`, Blattstand `6da4e914` (16.08. 22:52).**
**Kein Runde 2 — nach der Berichtigung des Release-Prüfers (§89-Nachtrag) schulde ich den REST
von Runde 1: sechs benannte Kriterien, drei unerwähnte, K5.**

### Die sieben bisher ungeprüften Kriterien

```
A-42-4   herkunft: mit Blocknummer und Basis-SHA                        messbar
A-42-6   Bloecke MIT zustand: unberuehrt, Anzahl und Inhalt ueber Hash  messbar (heute 90)
A-42-7   kein Nicht-Ziel, git show --stat, scripts/status-erzeugen.sh
         ausdruecklich ausgenommen                                      messbar
A-42-8   P7: WER (Generator, sein Baum) · DARF er (docs/, kein Loeschen)
         · EXISTIERT die Eigenschaft (Blockstruktur maschinell erfassbar) VOLLSTAENDIG
A-42-10  P8: Suchraum ist die Sache, drei Orte gemessen                 VORBILDLICH
A-42-11  Ballortung ueberlebt den Umzug                                 aus Runde 4 belegt
A-42-12  jede Rolle bekommt ihren Ortungsbefehl                         aus Runde 4 belegt
```

**A-42-10 verdient eine eigene Zeile.** Es nennt die Zahl **77** und schreibt dazu: *„(Die 77 sind
der Stand des Planner-Baums; im Bestand waren es zur selben Zeit **129** — deshalb nennt A-42-1 den
Befehl und keine Zahl.)"*

```
am Basis-Stand e802c1f8   Umzugsmenge  77   TRIFFT zeichengenau
heute                     Umzugsmenge 168
```

**Eine Zahl, ihr Stand, die abweichende Zahl des anderen Baums und die Begründung, warum das
Kriterium trotzdem an den Befehl gebunden ist — alles in einem Satz.** *Das ist die Form, deren
Fehlen ich heute Nacht sechsmal gemeldet habe.*

**Und K5 ist damit kein theoretischer Grenzfall mehr:** die Umzugsmenge wuchs während der Nacht von
**77 auf 168**. *K5 („während des Umzugs kommen neue Notizen dazu") ist die Kante, die mit Sicherheit
feuert — und das Blatt fängt sie, indem der Lauf einmal misst und seinen Stand-SHA nennt.*

### Der Restpunkt: A-42-3 ist weiterhin nicht erfüllbar

**Mein Befund vom 16.08. 19:29** (`Z.27392`): die Messvorschrift des Blattes
(`re.findall(r'```yaml(.*?)```')`) **verliert einen Block** — `A-18` folgt auf den ungeschlossenen
Zaun bei `Z.7876`, die Regex paart 7876 mit dem nächsten Öffner und liest A-18 als außerhalb.

**Heute erneut gemessen — vierter Stand, gleiches Ergebnis:**

```
Bloecke nach der Vorschrift      446
auftrag-Zeilen im VOLLTEXT       258
davon in Bloecken ERFASST        257
DIFFERENZ                          1
UNSICHTBAR                       "A-18"
```

**Das Blatt hat den Fall nicht aufgenommen:**

```
Kantenliste heute      K1 bis K6 — unveraendert sechs, keine siebte
K4 heute               'Ein Block ist kaputtes yaml (24 Altlasten)'
                       -> A-18 ist NICHT kaputt; er wird verschluckt.
                          Ein kaputter Block macht nicht sich selbst unsichtbar,
                          sondern den FOLGENDEN.
meine Gegenprobe       'auftrag-Zeilen im Volltext gegen erfasste'   0 Treffer im Blatt
```

**Und A-42-2s Summenprobe fängt es nicht:** die Gleichung lautet *„Blöcke vorher = Blöcke nachher in
STATUS.md + Einträge in BEFUNDNOTIZEN.md + gemeldete K1/K4-Fälle"*. **A-18 bliebe in `STATUS.md`
liegen und würde auf der Nachher-Seite mitgezählt — die Gleichung geht auf, und der Verlust ist
unsichtbar.** *Das ist der Grund, warum die Differenzprobe eine ANDERE Größe messen muss als die
Summenprobe.*

**Damit sind zwei Kriterien betroffen, aus einer Ursache:** `A-42-3` (für einen Block gibt es keinen
Hash) und `A-42-5` (die sechs Kanten decken den Fall nicht).

## VOTUM

```
A-42, Rest von Runde 1:  NICHT ERTEILT, EIN Restpunkt
  sieben Kriterien geprueft, alle messbar; A-42-10 vorbildlich, A-42-8 vollstaendig
  R1  A-42-3/-5: der Block nach einem ungeschlossenen Zaun ist unsichtbar.
      Vierter Stand, gleiches Ergebnis. Vorschlag steht seit 16.08. 19:29:
      siebte Kante ODER K4-Ergaenzung, plus die Differenzprobe
      'auftrag-Zeilen im Volltext gegen in Bloecken erfasste = 0'
```

**Der ungeschlossene Zaun `Z.7876` ist zugleich einer der zwei Posten, die seit §78 beim Integrator
liegen.** *Wird er geschlossen, verschwindet die Ursache — dann bleibt die Kante trotzdem nötig,
weil der nächste ungeschlossene Zaun denselben Schaden macht.*

**Ich fasse den Zustand NICHT an. Ball beim Planner.** **Kein Zustandsfeld angefasst, kein Bau.**
**Damit ist der Rückstand aus §87 abgearbeitet: A-38 erteilt · A-39 erteilt · A-40 zwei Restpunkte ·
A-42 ein Restpunkt.**

---

## 92 — Seine Meldung gegengemessen, und der Teil, der mir gehört: meine vier DoR-Ergebnisse stehen in NULL Fällen in der Statuswahrheit

**Stand:** HEAD `48d0a1b6`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
`c8e3ba6e` meldet, meine zwei erteilten DoRs kämen in der Statuswahrheit nicht an.

### Seine Zahlen, selbst nachgemessen

```
BEREIT in der ganzen Datei              0        TRIFFT
IN_ARBEIT                               0        TRIFFT
letzter Schreibvorgang an STATUS.md     0f969d5e  16.08 20:39:34   TRIFFT
Abstand                                 299 min  (er: 298, eine Minute frueher)
Commits in der Spanne 0f969d5e..HEAD    274      (er: 273, dito)
davon mit docs/STATUS.md                0        TRIFFT — die tragende Zahl
davon Integrator                        80       TRIFFT
```

**Die eine Zahl, auf die es ankommt, ist zeichengenau: in 274 Commits über 299 Minuten hat kein
einziger `docs/STATUS.md` berührt.** *Bei den Rückweg-Merges liegen wir auseinander (er 78, ich 93)
— mein Muster zählt jede Botschaft mit „Rueckweg", seines offenbar nur die des Integrators. Kein
Widerspruch, zwei Zuschnitte, und die tragende Zahl hängt an keinem von beiden.*

### Und die Sperre kennt die Ausnahme wirklich nicht

```
scripts/rollen-tor.sh:344
  if [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]; then

Wortsuche im ganzen Tor:
  Ballrueckgabe 0 · Zustandswechsel 0 · Reichweite 0 · ballbesitz 0 · zustand 2
```

**Die Freigabe des Planners für einzelne Zustandswechsel ist im Tor an keiner Stelle abgebildet.**
*Ich habe die Live-Probe NICHT gefahren: sie würde im Erfolgsfall schreiben, und `docs/STATUS.md`
gehört nicht mir. Der Wortlaut genügt für die Aussage.*

### Der Teil, den er nicht messen konnte, weil er mir gehört

```
meine vier DoR-Ergebnisse dieser Nacht (§88 A-38 · §89 A-39 · §90 A-40 · §91 A-42)
  in docs/BEFUND-plan-pruefer-rueckweg-und-tor.md   4
  als Commit-Betreff                                6
  in docs/STATUS.md                                 0
```

*Der eine scheinbare Treffer in `STATUS.md` ist `Z.21477` — „DoR Runde 3 … **NICHT** ERTEILT" von
gestern; mein loses Muster hatte „A-38.\*ERTEILT" darin gefunden. **Null von vier.***

**Nach §16 ist `docs/STATUS.md` die eine Statuswahrheit. Ein Prüfergebnis, das dort nicht steht, ist
im Prozesssinn nicht abgeliefert — es ist nur aufgeschrieben.** Und ich kann es dort nicht
hinschreiben; die Sperre lässt genau eine Rolle durch, und das bin ich nicht.

**Damit ist es der dritte Träger-Fehler dieser Nacht, und der erste mit gemessenen Kosten:**

```
§82  ein Befund liegt, wo kein Feld ihn benennt
§83  ein Feld traegt einen Namen, der zweierlei bedeutet
§92  ein Ergebnis liegt in der falschen DATEI — richtig gehandelt, trotzdem unsichtbar
```

### Was einzutragen wäre, damit niemand es suchen muss

```
A-38   DoR Runde 2 ERTEILT (§88)   -> ENTWURF nach BEREIT · dor_beleg auf das Ergebnis
A-39   DoR Runde 2 ERTEILT (§89)   -> ENTWURF nach BEREIT · dor_beleg auf das Ergebnis
A-40   NICHT ERTEILT, R2 + R3 (§90) -> bleibt ENTWURF · dor_beleg 'NICHT ERTEILT 17.08.'
A-42   NICHT ERTEILT, R1 (§91)      -> bleibt ENTWURF · dor_beleg 'NICHT ERTEILT 17.08.'
```

**Zwei davon sind Zustandswechsel, zwei nur Feldpflege** — und alle vier lösen den §78-Befund gleich
mit: *„steht aus" ist die falsche Kategorie für eine gefahrene Prüfung.*

**Ich trage nichts ein.** *Nicht weil ich zögere, sondern weil `docs/STATUS.md` dem Integrator gehört
und ein Zustandswechsel kein Transport ist — dieselbe Grenze, die der Release-Prüfer für sich zieht.*
**Ball beim Integrator** (Schreiblauf für vier Datensätze) **und beim Planner** (die zwei Übergänge
sind seine Entscheidung). **Kein Zustandsfeld angefasst, kein Bau.**

---

## 93 — Posten (a) an A-18: sieben von sieben halten — und dieselbe Datei trägt einen gewanderten und einen haltenden Zeiger, aus rein zeitlichem Grund

**Stand:** HEAD `414c3260`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**A-18 gewählt, weil zwei Fäden dort zusammenlaufen:** sein Block ist der, den A-42s Messvorschrift
verliert (§91), und §77 führte ihn mit zwei geänderten Code-Dateien.

### Sieben Zeiger, alle am Objekt

```
wandaufbau.ts:2      'Konfigurator „Wandaufbau" §11, autark; SPEIST HEIZLAST & DACH'   TRIFFT
wandaufbau.ts:4      'Reine Bauphysik: Waermedurchgangskoeffizient U aus geschichtetem
                      Aufbau' — der Dateikopf nennt seine Norm selbst                  TRIFFT
wandaufbau.ts:9-15   export interface Schicht { name?, dicke, lambda }
                     'GENAU DREI Felder'                                               TRIFFT
faehigkeiten.ts:81   { id: 'engine-uwert', label: 'U-Wert (Wandaufbau)', … }            TRIFFT
fachFlaechen.ts:149  { label: 'Feuchtelast', einheit: 'g/h' },                          TRIFFT
dachformVorlagen.ts:105  holzfeuchteProzent: string;                                   TRIFFT
sparrenBerechnung.ts:100 export const N003_VORBEHALT = 'Vorbemessung, ersetzt keine
                         prueffaehige Statik'                                          TRIFFT
```

**Und A-18-1s Forderung ist dadurch belegt:** der Vorbehalt soll *„als Konstante wie
`sparrenBerechnung.ts:100`, nicht als Zeichenkette im Rückgabeblock"* geführt werden — **er ist
dort genau das.**

### Der eigentliche Fund: zwei Zeiger, eine Datei, gegenteiliges Schicksal

`sparrenBerechnung.ts` trägt beide — den aus §81, der ins Leere zeigt, und diesen, der hält:

```
FORMELSAMMLUNG, N-003 Belegstelle  :86   geschrieben 12.08 00:30   heute Kommentarzeile  GEWANDERT
A-18, N003_VORBEHALT               :100  geschrieben 12.08 08:57   heute die Konstante   HAELT
dazwischen                         e0722979, 12.08 02:46, +20 Zeilen (A-14s Bau)
```

**Am Objekt nachgemessen, und die Erklärung ist rein chronologisch:**

```
N003_VORBEHALT bei e0722979~1   existiert NICHT      Datei 131 Z.
N003_VORBEHALT bei e0722979     Zeile 100            Datei 151 Z.
N003_VORBEHALT heute            Zeile 100            Datei 151 Z.
seit e0722979 byte-identisch · 0 Commits an der Datei in fuenf Tagen
```

**Der eine Zeiger wurde zwei Stunden VOR dem Einschub geschrieben, der andere sechs Stunden
DANACH.** *Beide Autoren haben sorgfältig gearbeitet. Der Unterschied ist keine Sorgfalt, sondern
ein Zeitpunkt.*

### Was das zu §79 hinzufügt

```
§79   WO gewachsen wird entscheidet   — REGISTER.md waechst unter allen Zeigern, +122 ohne Bruch
§93   WANN gezeigt wird entscheidet   — dieselbe Datei, zwei Zeiger, zwei Ergebnisse
```

**Ein Zeilenverweis ist nur so lange gültig, wie oberhalb nichts wächst — und ob das passiert,
weiß der Schreibende im Moment des Schreibens nicht.** *Deshalb ist „Kennung statt Zeile" keine
Stilfrage: `berechneSparren` und `N003_VORBEHALT` hätten beide überlebt, unabhängig vom Zeitpunkt.*
**Und die Gegenprobe steht daneben: seit die Datei nicht mehr wächst, hält der Zeiger seit fünf
Tagen. Nicht die Zahl ist das Problem, sondern die Bewegung darüber.**

**Kein Ball, kein Fund an A-18 — sieben von sieben.** *Der Beleg gehört zum §77/§79/§81-Ball beim
Planner, als dritte Achse: Ort, Zeitpunkt, Form.* **Kein Zustandsfeld angefasst, kein Bau.**

---

## 94 — Posten (b) an W-41: alle Zahlen treffen — und drei Beinahe-Fehlalarme in einer Runde, alle drei meine

**Stand:** HEAD `7af77b69`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Nach der Methode aus §80: die Gegenstände aus dem REGISTER geholt und gemessen, BEVOR ich das
Blatt geöffnet habe.**

### Vorgerechnet, Blatt ungeöffnet

Das Register sagt: *„Von sechs Kandidat-Kanten ist **eine belegt** — Dachfläche → PV-Belegung
(`geometry/pvBelegung.ts:10-14`)."*

```
pvBelegung.ts:10-14   export interface PvEingabe {
                        /** Dachbreite horizontal, mm. */   dachLaenge: number;
                        /** Dachlaenge in Falllinie …  */   dachBreite: number;
                      -> genau die Kante Dachflaeche → PV-Belegung       TRIFFT
```

**Erst danach das Blatt geöffnet — `:188-191` sagt: „Sechs Kanten, davon EINE belegt und fünf
ausdrücklich als Kandidat gekennzeichnet."** *Meine Messung stand vorher fest.*

### Die Null-Aussagen und die Blattlängen

```
invalidier / propagier                0 Dateien                        TRIFFT
markiereVeraltet                      configuratorPackage.ts:125
  Aufrufer AUSSERHALB der Tests       0 — nur :57 und :61 im Test      TRIFFT, beide Zeilen
Kanten / Graph (Abhaengigkeitssinn)   0                                TRIFFT
sieben Werkbank-Blaetter              63 · 103 · 56 · 65 · 67 · 78 · 102   ALLE SIEBEN
Summe                                 534                              TRIFFT
```

### Drei Beinahe-Fehlalarme, alle meine, alle vor dem Melden gefangen

```
1  'Kante' im Inselcode          94 Dateien  -> das ist die GEOMETRIE-Kante (Polygonkante),
                                                nicht die Abhaengigkeitskante.
2  Aufrufer von pvBelegung        1 (enginePanels.ts:32) -> die '0' des Blattes gilt fuer
                                                markiereVeraltet, nicht fuer pvBelegung.
                                                Ich hatte die falsche Groesse gemessen.
3  'Graph' im Inselcode           1 (roomDetection.ts:5) -> 'Wandachsen → Kanten-Graph',
                                                ein GEOMETRIE-Graph. Wieder das Wort, nicht
                                                die Sache.
4  sieben Blaetter, ich fand 6   -> das siebte liegt in 5-CODE/LIESMICH.md, 67 Zeilen.
                                    Mein '*.md' griff nicht in den Unterordner —
                                    und 534 minus 467 war genau diese 67.
```

**Drei der vier sind dieselbe Klasse: ich habe das WORT gemessen und nicht die SACHE.** *„Kante"
und „Graph" gibt es in der Geometrie längst; die Frage war, ob es sie als Abhängigkeit gibt.*
**Und der vierte ist P8 an mir selbst: der Suchraum war der Ort statt der Sache** — genau der
Fehler, den ich in §90 an A-40-5 gemessen habe und den der Release-Prüfer in §86 mit `lattweite`
gemacht hat.

**Dreimal dieselbe Klasse an einem Abend, bei drei verschiedenen Rollen, in beide Richtungen:**
*ein Wort finden, das die Sache nicht ist (94 Kanten) — und die Sache nicht finden, weil man am
falschen Ort sucht (das siebte Blatt, `lattweite`, A-40-5s Suchraum).*

### Was das Blatt richtig macht

**W-41 hat KEINEN Code, und das Blatt sagt es zuerst.** Es führt fünf der sechs Kanten
**ausdrücklich als Kandidat** und belegt nur die eine, die es belegen kann. *Ein Entwurf, der
seine eigene Dünne benennt, statt sie mit Vermutungen zu füllen — und die Registerzeile sagt
dasselbe: „die dünnste Vorgabe".*

**Siebtes Blatt ohne Abweichung: W-08/1 · W-11/1 · W-27/1 · W-34 · W-23 · W-39 · W-41.**

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 95 — D2 nachgemessen: seine Gegenprobe und meine fallen VERSCHIEDEN aus, und genau das belegt seine Bauentscheidung

**Stand:** HEAD `167cf13d`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
`203f308c` rüstet **D2** in `scripts/bloecke.py` nach — die Differenzprobe, die ich am 16.08. 19:29
vorgeschlagen und in §91 erneut belegt habe.

### Seine Zahlen, selbst nachgefahren

```
258 auftrag-Zeilen im Volltext · 257 in Bloecken erfasst · Differenz 1 · A-18   TRIFFT
```

**Die eine Abweichung ist keine:** er nennt A-18 in `Z.7891`, ich in §91 in `Z.7890`.

```
Z.7876   ```yaml     der ungeschlossene Oeffner
Z.7890   ```yaml     A-18s Blockoeffner        <- meine Zahl
Z.7891   auftrag: "A-18"                       <- seine Zahl
```

**Beide richtig — Öffner gegen erste Inhaltszeile.** *Vierte Konventionsabweichung dieser Nacht,
die keine Abweichung ist (nach dem Dach in §73, der Paarbildung in §80, dem Träger in §89).*

### Seine überraschende Gegenprobe — und meine fällt anders aus

Er schreibt: *„ein zweiter künstlich entfernter Schließer trieb D auf 3, **D2 blieb bei 1**… NICHT
JEDER FEHLENDE SCHLIESSER VERSCHLUCKT EINEN DATENSATZ."*

**Ich habe es unabhängig nachgestellt, an einer KOPIE im Kratzverzeichnis — `docs/STATUS.md` bleibt
unberührt:**

```
ORIGINAL             D=2 · D2=1   unsichtbar: A-18
Schliesser Z.9137 entfernt
                     D=3 · D2=2   unsichtbar: A-18 UND W-38
```

**Bei seinem Schließer blieb D2 stehen. Bei meinem stieg es.** *Kein Widerspruch — die Gegenprobe
fällt je nach Stelle anders aus, und genau das ist die Aussage.*

### Und damit ist die Regel messbar statt vermutet

```
Z.9137   ```          der entfernte Schliesser
Z.9138   ---
Z.9140   ## W-38 · Schrittstatus und Pruefpunkte …
         darunter W-38s yaml-Block            -> W-38 wird verschluckt
```

**Ein fehlender Schließer verschluckt den NÄCHSTEN Datensatz — wenn vor dem nächsten nackten
Schließer überhaupt einer steht.** *Steht dort keiner, verschmelzen zwei Zäune und nichts geht
verloren; steht dort einer, ist er weg. Dasselbe Muster erklärt A-18: hinter `Z.7876` folgt sein
Block.*

**Zwei unabhängige Versuche, entgegengesetzte Ergebnisse, eine Schlussfolgerung — und sie ist
seine:**

```
D  zaehlt die URSACHEN   (fehlende Schliesser)      heute 2
D2 misst die FOLGE       (verschluckte Datensaetze) heute 1
```

**Seine Entscheidung, zwei Prüfungen statt einer zu bauen, ist damit nicht begründet, sondern
belegt — von einem Versuch, der bei ihm anders ausging als bei mir.** *Hätte er nur seine
Gegenprobe gehabt, hätte er „D2 folgt D nicht" sagen können. Hätte ich nur meine, hätte ich „D2
folgt D" gesagt. Beide zusammen sagen: die Folge hängt an der Stelle, und eine Zahl kann die
andere nicht ersetzen.*

**Kein Ball. Der Restpunkt aus §91 bleibt beim Planner** (siebte Kante oder K4-Ergänzung in A-42),
**der ungeschlossene Zaun beim Integrator** — *und D2 misst jetzt, was er kostet.*
**Kein Zustandsfeld angefasst, kein Bau, `docs/STATUS.md` nicht berührt.**

---

## 96 — Posten (c) an W-03/1: F-001 rechnet richtig, aber aus einem Grund, den niemand aufgeschrieben hat

**Stand:** HEAD `7d9b27c2`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Nach der §80-Methode: Gegenstände aus dem REGISTER, gemessen vor dem Öffnen des Blattes.**

### Vier Registerzusagen, alle getroffen

```
F-001 an EigenschaftenPanel.tsx:117   const len = Math.hypot(dx, dy);              TRIFFT
F-001 an :339                          Math.round(Math.hypot(selectedWall.end.x …)) TRIFFT
lotAufWand im Panel                    0                                            TRIFFT
geradenGeometrie im Panel              0                                            TRIFFT
wandBaender im Panel                   0                                            TRIFFT
```

### Die Formel durchgerechnet — und der Grenzfall geht auseinander

```
FORMELSAMMLUNG F-001, Grenzfall:
  'd < ε (0,5 mm) → beide Punkte gelten als DERSELBE. Eine Wand mit d < ε darf nicht
   angelegt werden — sie erzeugt spaeter eine Division durch null.'

Code, EigenschaftenPanel.tsx:118:
  if (len === 0) return;
```

**Der Code prüft auf EXAKT null, die Formel auf kleiner 0,5.** *Das sah nach einem Fund aus, und
ich habe ihn nicht gemeldet — sondern zu Ende gerechnet.*

```
fangKern.ts:76   const runde = (p) => ({ x: Math.round(p.x), y: Math.round(p.y) })
Panel :119       const end = { x: Math.round(…), y: Math.round(…) }
Math.round in geometry/ und commands/                       56 Stellen
kleinste ganzzahlige Differenz ungleich 0: dx=1 → len = 1,0
-> zwischen 0 und 0,5 liegt bei GANZZAHLIGEN Koordinaten kein Wert
```

**Bei ganzzahligen Koordinaten ist das Intervall `(0; 0,5)` unerreichbar. `len === 0` und `d < ε`
decken exakt dieselbe Menge ab.** *Der Code ist richtig — enger im Wortlaut, gleich in der
Reichweite.* **Kein Fund am Code.**

### Der Fund ist, dass die Voraussetzung nirgends steht

```
nennt F-001 die Ganzzahligkeit?          0 Treffer
nennt das Panel sie?                     0 Treffer
```

**Die Gleichwertigkeit hängt allein daran, dass alle Koordinaten ganzzahlig sind — und das steht
weder in der Formel noch am Code.** *Wer morgen einen Weg baut, auf dem gebrochene Koordinaten
entstehen (ein Maßstab, ein Import, eine Skalierung), verliert die Gleichwertigkeit lautlos: das
`ε` würde dann greifen müssen, und `len === 0` greift nicht.*

**Und der Bestand kennt die Behebung bereits — an der Nachbarformel:**

```
F-032, Grenzfall 2 — RUNDUNG, ergaenzt 14.08.:
  'F-032 kennt KEIN Runden; wer das Ergebnis auf ganze …'
```

**Genau diese Bedingung wurde für F-032 nachgetragen, auf meinen eigenen Befund vom 14.08.**
*Dieselbe Klasse, dieselbe Sammlung, dieselbe Lösung — und für F-001 ist sie offen.*

**Das ist die Posten-(c)-Aussage in ihrer schärfsten Form:** *„Ein richtig zitierter Wortlaut kann
falsch rechnen"* — hier rechnet er **richtig**, und trotzdem fehlt etwas: **der Grund.**
*Ein Ergebnis, das nur unter einer ungenannten Voraussetzung stimmt, ist so lange gültig, wie
niemand die Voraussetzung ändert — und niemand kann sie schützen, der sie nicht kennt.*

**Ball beim Planner** (FORMELSAMMLUNG, F-001: ein Grenzfall-Zusatz nach dem Muster von F-032s
Grenzfall 2). **Kein Fund am Code, kein Fund am Blatt — die fünf Registerzusagen treffen alle.**
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 97 — Posten (d) an der Statuswahrheit: lange Lücken gab es oft, aber es waren immer PAUSEN. Diese ist die erste unter Volllast

**Stand:** HEAD `fc3fc3c3`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Die Lücke allein noch einmal zu zählen wäre wertlos — §92 hat sie gemessen. Posten (d) verlangt
einen Maßstab, und den hatte niemand.**

### Der Stand, fortgeschrieben

```
letzter Schreibvorgang    0f969d5e  16.08 20:39:34
Abstand                   314 min   (§92: 299)
Commits in der Spanne     279       (§92: 274)
davon mit docs/STATUS.md  0
echte Zustandswechsel     0   — zwei Betreff-Treffer auf 'zustand:' geprueft,
                              beide Fliesstext, keiner fasst STATUS.md an
```

*Die Stauzahl bleibt die aus §92: **vier** DoR-Ergebnisse, davon zwei Zustandswechsel. Lose
Stichwortzählungen habe ich verworfen — „DoR.\*ERTEILT 13" wäre eine Summe ohne Erhebung (B6).*

### Der Maßstab: 1338 Lücken in der Geschichte dieser Datei

```
Schreibvorgaenge an docs/STATUS.md   1339
Median einer Luecke                     1 min
Mittel                                 12 min
laengste je                          2675 min
aktuelle Luecke                       314 min   -> die 12.-laengste von 1338
```

**Gegen einen Median von EINER Minute ist die aktuelle Lücke 314-fach.** *Aber das allein wäre
noch kein Befund: lange Lücken gab es elf Mal.*

### Und das ist der Unterschied — die anderen elf waren still

```
   LUECKE   COMMITS DARIN   pro Minute
      314             287         0,91   <- AKTUELL
     2675               2         0,00
     1684               2         0,00
     1480               3         0,00
     1324               4         0,00
     1228               2         0,00
      751               2         0,00
      722               9         0,01
      644               2         0,00
```

**Jede frühere lange Lücke war eine PAUSE: zwei bis neun Commits, 0,00 bis 0,01 pro Minute.**
**Die aktuelle trägt 287 Commits bei 0,91 pro Minute — rund das Hundertfache jeder anderen.**

*Die Datei stand oft lange still. Sie stand noch nie still, während gearbeitet wurde.*

### Was das der Meldung des Release-Prüfers hinzufügt

Er schrieb: *„die Sperre hat zum ersten Mal eine gemessene Folge."* **Gemessen ist jetzt auch, wie
außergewöhnlich das ist: in 1338 Lücken kommt diese Kombination — lang UND laut — kein zweites
Mal vor.**

```
lang und still   elf Mal, das ist der Normalfall einer Nacht ohne Arbeit
lang und laut    einmal, jetzt
```

**Das ist kein Vorwurf an eine Rolle.** *Der Integrator arbeitet durchgehend — 80 seiner Commits
liegen in dieser Spanne. Er transportiert, und der Transport funktioniert; nur die Datei, die nur
er schreiben darf, ist nicht dabei.* **Die Zahl sagt nicht, wer säumig ist, sondern dass ein
Zustand eingetreten ist, den die Geschichte dieser Datei nicht kennt.**

**Ball unverändert:** **Integrator** (vier Datensätze, §92) · **Planner** (die zwei Übergänge).
*Ich trage nichts ein — die Grenze aus §92 gilt weiter.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 98 — Posten (e) am Planner-Stapel: vier Befunde unverändert offen — und §81 war UNTERMESSEN

**Stand:** HEAD `a0a0a85c`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**

### Die vier, einzeln nachgemessen

```
§81  N-003-Belegstelle sparrenBerechnung.ts:86   steht unveraendert   OFFEN
§96  F-001 Grenzfall Ganzzahligkeit              0 Treffer            OFFEN
§91  A-42 siebte Kante                           6 Kanten, keine 7.   OFFEN
§90  A-40 R2/R3                                  Blatt zuletzt 16.08 22:11,
                                                 meine DoR 17.08 01:35  OFFEN
```

### Und beim Nachmessen fiel auf, dass §81 zu klein war

**Die FORMELSAMMLUNG trägt nicht EINEN Zeiger in `sparrenBerechnung.ts`, sondern DREI — plus eine
dreigliedrige Kette. Ich hatte einen gemessen.**

```
:718   sparrenBerechnung.ts:33   bodenschneelast(zone, gelaendehoeheM)   liegt auf 33   HAELT
:740   sparrenBerechnung.ts:45   formbeiwertSchnee(neigungGrad)          liegt auf 45   HAELT
:757   sparrenBerechnung.ts:86   berechneSparren(e)                      liegt auf 105  +19
:824   sparrenBerechnung.ts:86   dieselbe Zahl, ZWEITE Stelle            liegt auf 105  +19
```

**Und die Einfügestelle erklärt beides — am Diff gemessen, nicht vermutet:**

```
e0722979  @@ -80,0 +81,9 @@   neun Zeilen nach Z.80
          @@ -82,0 +92,10 @@  zehn Zeilen nach Z.82
          @@ -129,0 +149 @@   eine Zeile nach Z.129
-> die Einschuebe liegen bei Z.80-82: UNTERHALB von :33 und :45, OBERHALB von :86
```

**Zwei Zeiger über der Einfügestelle halten, einer darunter wandert — dieselbe Datei, derselbe
Autor, derselbe Augenblick.** *Zusammen mit A-18s `:100` aus §93, der nach dem Einschub geschrieben
wurde und hält, ist die Regel jetzt an vier Punkten belegt:*

```
oberhalb der Einfuegestelle              :33 · :45      haelt
unterhalb, VOR dem Einschub geschrieben  :86 (2x)       wandert +19
unterhalb, NACH dem Einschub geschrieben :100 (A-18)    haelt
```

### Die Kette bei `:824` — zwei von drei

```
DIE ZAHL ENTSTEHT      sparrenBerechnung.ts:86    -> liegt auf 105          GEWANDERT
SIE WIRD GERUFEN VON   enginePanels.ts:210        -> Aufruf liegt auf 227   GEWANDERT
SIE WIRD ANGEZEIGT IN  EngineFlaeche.tsx:56-58    -> 'Die Rechengrundlage
                       steht sichtbar …' + hp-ef-grundlage + Grundlage:    TRIFFT
```

**Und die Sachaussage daneben trifft, obwohl ihr Zeiger wandert:** *„die EINZIGE Aufrufstelle
außerhalb der Tests"*.

```
Nennungen ausserhalb Definition und Tests   4   (Faehigkeitseintrag · import · Aufruf · Kommentar)
echte AUFRUFE mit Klammer                   1   enginePanels.ts:227
```

*Mein erster Zähler lieferte 4 und hätte einen Fund gemeldet — die Zusage spricht von AUFRUFEN,
nicht von Nennungen. Wieder das Wort gegen die Sache, diesmal bei mir.*

### Was §81 dadurch wird

**§81 hatte recht und war zu klein: ich meldete EINEN gewanderten Zeiger, gemessen sind es ZWEI
(dieselbe Zahl an zwei Stellen) plus einer in der Kette — und drei weitere, die HALTEN.**

```
sechs Zeiger in diesem Umfeld    vier halten · zwei wandern (drei Nennungen)
```

*Ein Befund, der eine Stelle nennt, wo sechs zu prüfen waren, ist kein falscher Befund — aber er
lässt den Empfänger glauben, er sei fertig, wenn er die eine behebt.* **Das ist der Grund, warum
ich die Fundstellen jetzt vollständig nenne.**

**Ball beim Planner, unverändert vier Posten — §81 präzisiert statt neu.**
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 99 — Wache-Punkt 2 vollständig gefahren (sauber, nach einem eigenen Fehler) — und W-15 zählt vier, wo immer drei standen

**Stand:** HEAD `14d11a93`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**

### Zuerst: die D2-Regel ist im Code angekommen

`f00e14c0` schreibt meine §95-Regel und meine Zeilenkonvention in `scripts/bloecke.py`.
**Am Objekt geprüft:**

```
bloecke.py:258-259   'Ein fehlender Schliesser verschluckt den NAECHSTEN Datensatz, wenn
                      zwischen ihm und dem naechsten nackten ``` ueberhaupt einer steht.
                      Steht dort keiner, verschmelzen …'                       STEHT
bloecke.py:227       'in Z.7891 — der Block direkt hinter dem ungeschlossenen Zaun von
                      Z.7876'                                                  STEHT
```

**Und seinen Verschmelzungsfall selbst nachgesehen:** hinter dem von ihm entfernten Schließer
`Z.1163` folgen Prosa, `---`, eine Überschrift und **erst dann** A-02s Block — dessen `auftrag:`
liegt INNERHALB der verschmolzenen Spanne und bleibt sichtbar. *Bei meinem `Z.9137` fällt W-38s
Zeile heraus. Zwei Stellen, zwei Ergebnisse, eine Mechanik — jetzt vollständig belegt.*

### Wache-Punkt 2: 89 Blätter gegen ihre Blöcke

```
Blaetter geprueft            89
mit auftrag-Block            74
ohne Block, MIT Tafelzeile   11   W-01 · W-02 · W-04 · W-05 · W-08 · W-09
                                  W-11 · W-13 · W-15 · W-21 · W-22
ohne Block und ohne Tafel     0
```

**Die elf sind die Werkzeug-Grundblätter — genau die „feldlosen Blätter", für die die Wache den
Rückfall auf die Kopfzeile vorsieht.** *Ihre Ablesungen (`W-03/1`, `W-05/1`, `W-01N` …) tragen
sehr wohl Blöcke. Das ist kein Fund, sondern die Bauart.* **Null Blätter ohne beides.**

**Mein Fehler dabei, gefangen:** mein erster Lauf meldete **vier B-Blätter ohne Block und ohne
Tafelzeile**. Nachgesehen: die Dateien heißen `B5-…`, `B5N-…`, `B6-…`, `B7-…`, **der Bestand führt
sie als `B5`, `B5N`, `B6`, `B7` — und meine Kennungsableitung hatte einen Bindestrich eingesetzt,
den es nicht gibt.** *Alle vier haben Block UND Tafelzeile. Achtzehnter Musterfehler dieser Nacht,
und wieder die Signatur: vier gleichartige Treffer auf einmal.*

### Posten (a) an W-15 — und der Fund ist KEIN gewanderter Verweis

**Sechs Zeiger, alle in `werkzeugVertrag.ts`. Fünf treffen:**

```
:886  werkzeugId: 'material-zuweisen'                                   TRIFFT
:887  commandId: 'MaterialCommand'                                      TRIFFT
:891  vorbedingungen: ['project.open', 'selection.count >= 1', …]       TRIFFT
:883 · :895 · :907   je dienstMethode: services.material.execute
                     mit 'paint' · 'material' · 'texture'               ALLE DREI
```

**Der sechste ist eine Zählung, und sie geht nicht auf:**

```
Blatt, Wiederverwendungspruefung §5:
  'werkzeugVertrag.ts:874-908   VORHANDEN — VIER Eintraege, vollstaendig. Die Hauptquelle.'

gemessen in 874-908:  DREI   material-aufnehmen (874) · material-zuweisen (886) · textur (898)
der naechste          daemmung  beginnt auf 910 — zwei Zeilen ausserhalb
der vorige            klinker   steht auf 862 — zwoelf Zeilen davor
```

**Und es ist keine Drift:** `werkzeugVertrag.ts` ist seit der Anlage des Blattes (`a1cda36b`,
12.08. 01:54) **unverändert — 1440 Zeilen damals wie heute, 0 Commits.** *Am Blattstand standen in
874–908 ebenfalls drei.* **Die Zahl hat nie gestimmt.**

**Die tragende Aussage hält trotzdem:** *„VORHANDEN … die Hauptquelle → ZITIEREN, nicht
umformulieren"* — die Quelle ist da und ist die richtige. **Falsch ist nur die Zahl, und sie steht
in einer §5-Wiederverwendungsprüfung.** *Wer nach vier Einträgen sucht und drei findet, hält seine
eigene Messung für unvollständig — das ist der Schaden, nicht die Eins.*

**Ball beim Planner** (W-15, `:142`: entweder `862-908` mit vier oder `874-908` mit drei).
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 100 — Posten (b) an W-21/2: jede Zahl trifft, die eine „falsche" ist als ersetzt gekennzeichnet — und das Blatt trägt MEINEN Fehler von §99, eine Runde früher

**Stand:** HEAD `039b871f`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**W-21/2 `BETRIEBSBESTAETIGT`, Basis `9ea1c3db`.**

### Vier Zahlen nachgerechnet

```
auswechslung.ts        174 Zeilen · 5 Exporte      Blatt: 174 / 5      TRIFFT
                       seit der Basis byte-identisch
W-21/1 'VERGEBEN'      18 Treffer in docs/STATUS.md
                       an der Basis 9ea1c3db: 18                       TRIFFT
                       heute: 28 — die Datei waechst, die Aussage
                       ('vergeben') ist ein Ja/Nein und altert nicht
fuenf Module von W-21   holzBauteile 82 · holzMengen 64 · schifterListe 152
                        sparrenBerechnung 151 · sparrenTrennung 67  = 516
```

### Die 496 — und sie ist kein Fund, sondern ein Beleg

Das Blatt nennt **496 Zeilen** und sagt im selben Atemzug, wofür:

> *„es sind Zählangaben („Fünf Module", „496 Zeilen"), die **durch die neuen ersetzt** wurden"*

**Und die Rechnung geht auf:**

```
496  + 20  = 516
      ^^^^  sparrenBerechnung.ts 131 -> 151, e0722979 (A-14s Bau, 12.08. 02:46)
heute gemessen: 516
```

**Derselbe Einschub, der in §81 und §98 die N-003-Belegstelle um 19 Zeilen verschoben hat, erklärt
hier eine ersetzte Zählangabe — und W-21/2 hat ihn richtig behandelt:** *die Zahl neu erhoben, die
alte als überholt danebengelassen.* **Ein Auftrag, der dieselbe Ursache traf und sie überlebt hat.**

### Und das Blatt trägt meinen §99-Fehler, eine Runde vor mir

W-21/2 bekennt einen eigenen Messfehler:

> *„Für W-21-2-7 suchte ich die Kriterien mit `^W-21-[0-9]+` und bekam **0 an beiden Ständen** —
> daraus las mein Skript ‚zeichengleich: **True**'. Das ist eine Zusage, die **LEERE** vergleicht:
> W-21s Kriterien heißen `W-21/1-1` mit **Schrägstrich**."*

**Selbst nachgemessen:**

```
Muster ^W-21-[0-9]+          0 Treffer
Muster W-21/1-              29 Treffer   (W-21/1-1 … W-21/1-12 …)
```

**Das ist zeichengenau mein Fehler aus §99** — dort hieß es `B-5` statt `B5`, hier `W-21-1` statt
`W-21/1`. *Beide Male eine Kennungsform, die das Muster nicht treffen kann; beide Male ein
ERGEBNIS aus Leere — bei ihm ein grünes „zeichengleich", bei mir vier „fehlende Datensätze".*

**Der Unterschied ist nur die Richtung:** *seine Leere sagte fälschlich JA, meine sagte fälschlich
NEIN.* **Dieselbe Ursache kann beides — und deshalb ist „null Treffer" nie ein Ergebnis, sondern
immer erst eine Frage an das Muster.**

### Achtes Blatt ohne Abweichung

```
W-08/1 · W-11/1 · W-27/1 · W-34 · W-23 · W-39 · W-41 · W-21/2
```

**W-21/2 gehört dazu, und zwar mit derselben Eigenschaft wie die anderen sieben: es liefert das
Material, mit dem man es widerlegen könnte — hier sogar den eigenen Messfehler samt Diagnose.**

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

---

## 101 — Posten (c) an W-10/1: F-011 hält an fünf Flächen und vier Zusagen — und ich hätte fast einen Faktor 1.000.000 gemeldet

**Stand:** HEAD `6a8d0af5`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Gegenstände aus dem REGISTER, gemessen vor dem Öffnen des Blattes.**

### Die Registerzusagen, alle am Objekt

```
polygonFlaeche.ts:44   summe += a.x * b.y - b.x * a.y;          zeichengenau
polygonFlaeche.ts:46   const flaeche = Math.abs(summe) / 2;     zeichengenau
F-030 'zur HAELFTE'    applyCommand.ts:128  const nx = -dy/len, ny = dx/len
                                            -> Normale n = (−r_y, r_x)        TRIFFT
                       :131-136  vier Grundpunkte start/end ± n·h, h = laufbreite/2
                       eine z-Extrusion gibt es NICHT — das Polygon ist 2D     TRIFFT
```

### Und dann hätte ich beinahe einen Riesenfund gemeldet

**Erster Lauf, Eingabe in Millimetern:**

```
Quadrat 1000 x 1000   von Hand 1,000 m²      Modul 1000000      ABWEICHUNG
```

*Ein Faktor 1.000.000 an einer Funktion, die `polygonFlaecheM2` heißt — das sah nach dem größten
Fund der Nacht aus.* **Statt zu melden, den Vertrag gelesen:**

> *„Eingabe sind die 2D-Punkte der Dachfläche in der (geneigten) Flächenebene, **in Metern** (so
> liegt `surf.polygon` vor: lokale u/v-Koordinaten). Damit ist das Ergebnis die echte geneigte
> Dachfläche in m²."*

**Ich hatte Millimeter in einen Meter-Vertrag gefüttert. Der Fehler war meine Eingabe, nicht das
Modul.**

### Neu gerechnet, in Metern — drei Wege, fünf Flächen

```
FALL                        SOLL      HAND     MODUL
Quadrat 1 m x 1 m           1,00    1,0000    1,0000
dasselbe im UHRZEIGERSINN   1,00    1,0000    1,0000
Dreieck 4 m x 3 m           6,00    6,0000    6,0000
L-Form 10x10 minus 4x4     84,00   84,0000   84,0000
Dachflaeche 8,5 x 4,2      35,70   35,7000   35,7000
```

**Und die vier Zusagen des Dateikopfs einzeln geprüft:**

```
< 3 Punkte -> 0        zwei Punkte · leer · null    ->  0 · 0 · 0
NaN -> 0                                            ->  0
Infinity -> 0                                       ->  0
niemals NaN/Infinity   selbst mit 1e308-Koordinaten ->  endlich
```

**Fünf Flächen, drei Wege, vier Zusagen — keine Abweichung.**

### Die Fehlerklasse ist neu und sie ist die verführerischste

```
Fehler 26  falsch gemessen                    -> falsche Aussage
Fehler 27  nicht gemessen, richtig geraten    -> Antwort stimmt zufaellig
Fehler 28  nicht gleichzeitig gemessen        -> Block unstimmig
Fehler 29  richtig gemessen, falsch eingeordnet -> Zahlen stimmen, Schluss nicht
NEU        richtig gemessen, FALSCH GEFUETTERT -> die Abweichung ist meine Eingabe
```

**Bei 26 bis 29 lag der Fehler in meiner Auswertung. Hier lag er in meinem Prüfstand — und der
Prüfstand meldet keine Warnung, er rechnet einfach.** *Ein Faktor 1.000.000 sieht aus wie ein
Befund, gerade weil er so groß ist: niemand vermutet hinter einer so großen Zahl den eigenen
Tippfehler in der Einheit.*

**Die Regel daraus, und sie ist billig:** *bevor eine Abweichung gemeldet wird, wird der
EINHEITEN- und EINGABEVERTRAG gelesen — er stand hier im Dateikopf, sechs Zeilen über der Formel.*

**Kein Ball, kein Fund. Neuntes Blatt-Umfeld ohne Abweichung.**
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 102 — Posten (d) an der WERKBANK: 889 Zeilenzeiger, und der Einschub aus §77 trifft dort 29 statt vier

**Stand:** HEAD `35c97528`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Ein Träger, den ich in dieser Nacht nie gemessen habe:** §77 prüfte die Auftrags-Blätter, §79 die
Dokumentzeiger der aktiven Blätter — **die Werkbank stand nie auf dem Tisch.**

### Der Umfang

```
Werkzeugordner                42
Blattdateien                 295
Zeilen gesamt             21.308
Zeilenzeiger auf Code        889     (aktive Blaetter zum Vergleich: 121, §79)
Werkzeuge, die Code nennen    39
```

**Codedrift roh gemessen: 23 von 41 Ordnern nennen eine Datei, die sich seit dem letzten Schreiben
des Ordners geändert hat.** *Das ist KEIN Fund — es sind fast durchweg die großen geteilten Dateien
(`HausplanerApp.tsx` 48 Zeiger, `toolRegistry.ts` 28, `studioDaten.ts` 34), die sich ständig ändern.
Ein Blatt, das sie nennt, wird davon nicht falsch.* **Die prüfbare Frage ist der Zeilenzeiger.**

### `studioDaten.ts`: der Einschub aus §77, jetzt in der Werkbank gemessen

```
34 Zeiger auf studioDaten.ts
   5 oberhalb der Einfuegestelle :154   ->  HALTEN   (:97 3x · :136 · :137)
  29 unterhalb                          ->  Versatz +10
     davon :163  DREIZEHNMAL
```

**Am Objekt:**

```
:163 heute   ' * `web.php:5016/5018/5020` → `objekt.blade.php:144` → …'   Prosa
:173 heute   export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';  das Ziel
:97  heute   export type StudioModus = 'start' | 'guided' | 'expert';      haelt
```

**Und alle 29 waren beim Schreiben richtig:** die neun Werkbank-Blätter, die `:163` nennen, wurden
am **12.08.** geschrieben (15:02 · 22:09 · 23:23), **A-23s Einschub kam am 13.08. 00:08.**

*Auch hier hätte ich fast danebengegriffen: `REGISTER.md` trägt `:163` und wurde zuletzt am
**16.08. 21:34** geändert — das sah nach „nach dem Einschub geschrieben, also von Anfang an falsch"
aus. **`git log -S` sagt: die Zeilen kamen am 12.08. 15:02 und 22:09.** Der Zeitstempel einer DATEI
sagt nichts über den Zeitpunkt einer ZEILE.*

### Was das für §77 bedeutet

§77 meldete **vier** betroffene Blätter aus diesem einen Einschub (W-38, W-33, W-36, W-40).
**Gemessen sind es in der Werkbank 29 weitere Zeiger derselben Ursache.**

```
§77   Auftrags-Blaetter   4 Zeiger betroffen
§102  Werkbank           29 Zeiger betroffen, aus demselben Einschub
```

**Dieselbe Lehre wie §98, an einer anderen Stelle:** *ein Befund, der einen Träger nennt, lässt den
Empfänger glauben, er sei fertig, wenn er diesen Träger behebt.* **§77 war richtig und um den
Faktor sieben zu klein.**

### Was ich ausdrücklich NICHT gemessen habe

**855 der 889 Zeiger sind ungeprüft.** *Ich habe `studioDaten.ts` genommen, weil dort ein Einschub
BEKANNT war. Ob die übrigen halten, weiß ich nicht und rechne es nicht hoch — vier von fünf wären
achtzig Prozent, und diese Quote auf 889 zu übertragen wäre eine Summe ohne Erhebung (B6).*
**Die Aussage lautet: wer in der Werkbank eine Zeilennummer liest, muss damit rechnen, dass sie
nicht stimmt. Wie viele genau, misst wer sie braucht.**

**Ball beim Planner** — zum §77/§79/§81/§93-Bündel, jetzt mit der Werkbank als viertem und größtem
Träger. *Die Abhilfe ist dieselbe: die Kennung (`SchrittStatus`) überlebt jeden Einschub, die Zahl
nicht.*
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 103 — Posten (e) am Generator: §71s Formulierung war unfair. Beide Fertigmeldungen waren richtig — das Blatt ist danach zweimal gewachsen

**Stand:** HEAD `1aaca355`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**

### Erst die Zahl, die ich fast falsch gemeldet hätte

Mein erstes Muster lieferte *„neue Fertigmeldung seit ea377567: **28**"*. **Das sind keine 28
Meldungen, sondern 28 Commits, die irgendwo „A-37" und „CODE_FERTIG" enthalten — B6.**
Am bekannten Treffer verifiziert und auf die Meldeform verengt:

```
git log --all --grep='^generator: zustand: A-37 · CODE_FERTIG'
  fb59f6cc  16.08 19:38  bau 97f1dd00 — 'achtzehn Kriterien'
  ea377567  16.08 20:01  bau 1c36544e — 'NEUNZEHN Kriterien'
GENAU ZWEI. Keine dritte.
```

### Die Zeitachse, Kriterienzahl je Stand gemessen

```
19:38  fb59f6cc  generator  Blatt 18   Meldung sagt 18     RICHTIG
19:43  4a10abca  planner    Blatt 19   A-37-19 kommt hinzu
19:49  1c36544e  generator             der Bau dazu
20:01  ea377567  generator  Blatt 18*  Meldung sagt 19
20:42  1403e348  planner    Blatt 20   A-37-20
23:50  b6a79a66  planner    Blatt 21   A-37-21
```

**`*` — und das ist der Punkt:** im Baum von `ea377567` trägt das Blatt **18**, weil der
Planner-Commit `4a10abca` diesen Zweig noch nicht erreicht hatte. **Der Generator wusste von
A-37-19 — er hatte es sechs Minuten vorher gebaut — und schrieb es in seine Meldung; sein eigener
Commit kann es nicht belegen.** *Eine Zahl, die die Welt richtig beschreibt und im eigenen Baum
nicht nachprüfbar ist. Genau die Klasse, um die diese ganze Nacht kreist.*

### Und damit ist §71 in seiner Formulierung zu korrigieren

§71 schrieb: *„die Meldung ist hinter dem BLATT um zwei Kriterien"* — das liest sich als Versäumnis
des Meldenden. **Gemessen:**

```
beide Meldungen waren zum Zeitpunkt ihrer Abgabe RICHTIG
die Luecke entstand DANACH: A-37-20 um 20:42, A-37-21 um 23:50
```

**Der Vergleich in §71 stellte das HEUTIGE Blatt neben die GESTRIGE Meldung — Fehler 28 in
Reinform, diesmal über zwei Kalendertage statt über drei Befehle.** *Die Zahlen stimmten; die
Gegenüberstellung war nicht gleichzeitig.*

**Was bleibt und was fällt:**

```
BLEIBT   der Scope-Diff: seit bau 1c36544e sind es +97 -19 Zeilen in den zwei Gegenstaenden
BLEIBT   eine dritte Fertigmeldung ist faellig — A-37-20 und A-37-21 sind ungedeckt
FAELLT   die Lesart, die Meldung sei saeumig gewesen. Sie war es nicht.
```

*Der Ball bleibt beim Generator, aber der Grund ist ein anderer: nicht „du hast zu wenig
gemeldet", sondern „das Blatt ist dir zweimal davongelaufen".*

### Release-Prüfer, unverändert offen

```
__pycache__ in .gitignore     0 Treffer
scripts/__pycache__ im Baum   vorhanden
```

*Seit §74 unverändert. Kein Schaden, aber jede Wache zählt es weiter als Baumeintrag mit.*

**Ball beim Generator (dritte Meldung) und beim Release-Prüfer (`.gitignore`).**
**Kein Zustandsfeld angefasst, kein Bau.**

---

## 104 — Posten (a), zweite Werkbank-Scheibe: fünf weitere gewanderte Zeiger — und einer, der nie stimmte

**Stand:** HEAD `721b75aa`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**§102 ließ 855 Zeiger ungemessen. Diese Runde nimmt die Scheibe, bei der der Einschub BEKANNT ist.**

### `sparrenBerechnung.ts` — Einschub bei Z.80-82, +19

```
oberhalb, halten:   :16 Schneezone · :30 DURCHBIEGUNG_GRENZE=300 · :33 bodenschneelast
                    :45 formbeiwertSchnee · :63 Kommentar staendige Last
unterhalb:          :86 (2x) · :90 · :100 (4x) · :105
```

**Und die unterhalb einzeln geöffnet — nicht pauschal beurteilt (Lehre aus §93):**

```
:100 (4x)  N003_VORBEHALT   TRIFFT  — nach dem Einschub geschrieben
:105       berechneSparren  TRIFFT  — dito
:86  (2x)  heute ' *'       W-21/3-FORMELN nennt es 2x als N-003-Belegstelle
:90        heute '}'        W-21/3-FORMELN: 'benutzt Math.cos fuer die senkrechte
                            Lastkomponente'
```

**Am Stand vor dem Einschub nachgeschlagen — beide waren richtig:**

```
e0722979~1  :86  export function berechneSparren(...)      86 + 19 = 105  heute dort
e0722979~1  :90  const cosA = Math.cos(a);                 90 + 19 = 109  heute dort
```

### `StartView.tsx` — A-23s Einschub, dieselbe Geschichte

```
W-33/7-GRENZEN :18   zitiert 'Gefuellt wird sie in Teil B (Route + Controller, bei Yama)'
W-33/7-GRENZEN :205  zitiert 'Die echte Liste braucht eine Route und ist Teil B'
vor 3ad920b1: beide Saetze stehen exakt dort                        RICHTIG gewesen
heute:        :18 ' *' · :205 '<div className="hp-start-wrap">'      GEWANDERT
```

**Und das ist der bittere Teil:** *die zwei Sätze, die die Werkbank zitiert, sind genau die, die
A-23 als **ÜBERHOLT** gekennzeichnet hat.* **Ein Commit hat beides bewirkt — den Inhalt überholt
gemacht und den Zeiger darauf verschoben.**

### Einer ist keine Drift, sondern war nie richtig

```
W-21/7-GRENZEN nennt sparrenBerechnung.ts:10-12 fuer das Zitat '… Tragwerksplaner.'
'Tragwerksplaner' steht auf Zeile 13 — bei e0722979~1, bei e0722979 und heute
die Werkbank-Zeile wurde 12.08. 00:21 geschrieben (992d5d76)
```

**Die Zeile liegt OBERHALB des Einschubs; dort kann nichts gewandert sein.** *Der Bereich endet
eine Zeile vor dem Zitat, das er trägt — und tat das von Anfang an.*

### Der laufende Stand aus zwei bekannten Einschüben

```
§77    Auftrags-Blaetter                    4 Zeiger
§102   Werkbank, studioDaten.ts            29 Zeiger
§104   Werkbank, sparrenBerechnung + StartView   5 Zeiger  (+1 von Anfang an falsch)
                                          ------
                                            38 aus ZWEI Commits
```

**Zwei Einfügungen — `e0722979` (+20) und `3ad920b1` (+24 über zwei Dateien) — haben 38
nachgewiesene Zeiger entwertet.** *Und beide waren korrekte, sorgfältige Arbeit: die eine baute
N-003s Vorbehalt ein, die andere berichtigte überholte Begleittexte nach der Hausregel.*

**Weiterhin ungemessen: rund 850 Zeiger.** *Ich rechne nicht hoch — ich habe zwei Scheiben mit
BEKANNTEM Einschub genommen, und die sind kein Zufallsschnitt (B6).*

**Ball beim Planner**, zum Zeiger-Bündel. *Die Abhilfe bleibt dieselbe und ist an diesen fünf
Fällen wieder ablesbar: `berechneSparren`, `Math.cos`, `N003_VORBEHALT` — jeder Name hätte
überlebt.* **Kein Zustandsfeld angefasst, kein Bau.**

---

## 105 — Posten (b) an W-13: neun Zahlen, neun Treffer — und meine „Entdeckung" stand längst im Blatt

**Stand:** HEAD `9431ebc5`, getrackt 0. **Messstand in Variable, Gegenprobe: unbewegt.**
**Gegenstände aus der Tafelzeile geholt und gemessen, bevor ich das Blatt geöffnet habe.**

### Die vier Module, je Zeilen und Exporte

```
MODUL                    gemessen        Blatt
auswahlModus.ts          98 Z / 7 E      98 / 7    TRIFFT
auswahlDarstellung.ts    71 Z / 3 E      71 / 3    TRIFFT
auswahlUebersicht.ts     77 Z / 4 E      77 / 4    TRIFFT
trefferSuche.ts          75 Z / 4 E      75 / 4    TRIFFT
SUMME                   321 Z / 18 E    321 / 18   TRIFFT
```

*Mein erster Vergleich meldete **viermal ABWEICHUNG**, obwohl die Spalten sichtbar gleich waren:
`wc -l` liefert führende Leerzeichen, und `[ "      98" = "98" ]` ist falsch. **Derselbe Fehler wie
in §71 — diesmal in einer Sekunde erkannt, weil die Zahlen nebeneinander standen.** Das ist der
Nutzen einer Ausgabe, die beide Werte zeigt statt nur das Urteil.*

### Die drei Sachzusagen der Tafelzeile

```
'waehlbar !== false, nicht === true'
  trefferSuche.ts:58   .filter((k) => k.waehlbar !== false)                zeichengenau

'oben schlaegt nah (Zeichenreihenfolge vor Distanz)'
  trefferSuche.ts:9    '… dann nach Distanz sortiert. Was oben liegt, gewinnt'
  trefferSuche.ts:61-63  .sort((a,b) => ( … b.zeichenreihenfolge - a.zeichenreihenfolge
                       -> absteigend nach Zeichenreihenfolge ZUERST         TRIFFT

'0 dedizierte Zusagen bei 321 Z'
  Testdatei gleichen Namens fuer die vier Module:  NEIN · NEIN · NEIN · NEIN
  das Blatt sagt praezise 'fuer die keine Testdatei ZUSTAENDIG ist'         TRIFFT
```

### Und der Punkt, an dem ich fast einen eigenen Fund gemeldet hätte

**Beim Messen der Aufrufer fiel auf:**

```
auswahlDarstellung.ts   0 Aufrufer ausserhalb der Tests
trefferSuche.ts         0 Aufrufer ausserhalb der Tests
```

*Zwei von vier Modulen laufen produktiv nirgends — das sah nach einem Fund aus.* **Es steht seit
der Abnahme im Blatt, wörtlich:**

> *„Von 18 Ausfuhren sind nur vier produktiv verdrahtet; `auswahlDarstellung.ts` und
> `trefferSuche.ts` haben außerhalb von `markieren.test.ts` **keinen Aufrufer**."*

**Dieselben zwei Module, dieselbe Aussage — vom Release-Prüfer, und korrekt eingeordnet:**

> *„Warum beides den Release nicht hält: **keines der zehn Kriterien verlangt es.** Der Auftrag war,
> den vorhandenen Code zu BESCHREIBEN, nicht ihn zu bewerten — und ein Release-Prüfer, der gegen
> eine ungestellte Anforderung misst, verschiebt die Ziellinie nach der Abnahme."*

**Meine Messung bestätigt sie unabhängig, mehr nicht.** *Und die Einordnung ist die schwerere
Hälfte: einen echten Mangel finden und trotzdem sagen, dass er den Release nicht hält, weil kein
Kriterium ihn verlangt.*

### Neuntes Blatt ohne Abweichung

```
W-08/1 · W-11/1 · W-27/1 · W-34 · W-23 · W-39 · W-41 · W-21/2 · W-13
```

**W-13 gehört dazu mit derselben Eigenschaft:** *es beziffert seine dünne Grundlage selbst
(„0 dedizierte Zusagen bei 321 Z" steht in der eigenen Tafelzeile) und trägt den Verdrahtungsbefund
im eigenen Text.* **Wer das liest, braucht mich nicht, um es zu wissen — er braucht mich nur, um es
nachgerechnet zu bekommen.**

**Kein Ball, kein Fund.** **Kein Zustandsfeld angefasst, kein Bau.**

## §106 — Posten (b/c) an W-01: das Register führt einen Aufrufer, den es nicht gibt

Stand 82c80a51. Ziel war Posten (c), F-040/F-041 durchrechnen. Vor dem Blatt habe ich die
Registerzeile W-01 (`02-WERKZEUGE/REGISTER.md:35`) gemessen — sie trägt fünf prüfbare Zusagen.
Vier halten, die fünfte nicht. Nach der Stopp-Regel geht der Fund vor der Rechnung.

### Was hält

| Zusage der Zeile | Messung | |
|---|---|---|
| „KEIN Werkzeug in der Registry" | `toolRegistry.ts`: 1 Treffer auf `fang\|raster` — und der ist `:159 'Lauflinie **Anfang**→Ende'`, ein Substring-Fehltreffer. Kein Fang-Eintrag. | TRIFFT |
| Zeiger `W-01-raster-und-fang/7-GRENZEN.md:17` | Datei 24 Z., Zeile 17 trägt das Zitat wörtlich, inkl. „der Fang liegt unter anderen Werkzeugen, er ist keines" | TRIFFT |
| `geometry/fangKern.ts:1-6` | 276 Z. / 11 Exporte; :2 „reine Funktion ohne Konva/three", :6 „Eine Wahrheit": genau EINE Fang-Entscheidung, :4-5 Rangfolge Endpunkt > Ortho > Raster | TRIFFT |
| Aufrufer `app/HausplanerApp.tsx` | `:25 import { fange, toleranzAusZoom, FANG_TEXT, type FangArt }`; Wirkung: `fange` 4× · `toleranzAusZoom` 4× · `FANG_TEXT` 2× · `wandFangpunkte` 1× | TRIFFT |

### Der Fund

Die Zeile schreibt: **„Aufrufer: `app/HausplanerApp.tsx` und `app/tools/werkzeugEnde.ts`."**

`app/tools/werkzeugEnde.ts` ist kein Aufrufer:

- **0 Import-Zeilen insgesamt** (nicht nur keine auf `fangKern` — die Datei importiert nichts).
  135 Z., 10 Exporte, 34 Code-Zeilen, erste Codezeile `:51 export interface StartPunkt`.
- **0 Treffer auf jeden echten fangKern-Namen**: `fange` · `toleranzAusZoom` · `FANG_TEXT` ·
  `FANG_PX` · `wandFangpunkte`. Vier dieser fünf Muster habe ich am bekannten Treffer
  (`HausplanerApp.tsx`) verifiziert, sie liefern dort 1–4.
- Das einzige Vorkommen des Wortes `fangKern` in der Datei steht in einem Kommentar und sagt das
  **Gegenteil** — `werkzeugEnde.ts:46`: *„**Und kein Anschluss an `fangKern`** — das ist Z-02 und
  hängt an dieser Scheibe, weil der Fangzustand erst gelöscht werden kann, wenn es einen Ort gibt,
  der löscht."*
- **Z-02 ist ein real geführter Auftrag**, wörtlich benannt:
  `docs/auftraege/generator-auftrag-z02-fangkern-anschliessen.md` (183 Z.), in 10 Dateien genannt.
  Der Anschluss ist offene Arbeit, nicht erledigte.

Klasse: **P7 (Ort ≠ Wirkung) plus B7 (Vorkommen ist kein Beleg)** — in der schärfsten Form, die mir
bisher untergekommen ist. Gemessen wurde die *Nennung* des Wortes, nicht der Aufruf; und die eine
Nennung behauptet genau das Umgekehrte. Ein Vorkommen wurde zum Aufrufer gelesen, obwohl es die
Abwesenheit protokolliert.

Warum das nicht kosmetisch ist: Die Zeile sagt selbst, wozu sie existiert — *„Der Satz steht hier,
weil die Spalte sonst ‚BESCHRIEBEN, 0 GEBAUT' heißt und jemand ein Fang-Werkzeug baut, das es nicht
geben darf."* Sie soll also einen Bau verhindern. Mit dem zweiten Aufrufer verhindert sie einen
zweiten dazu: Wer die Zeile liest, sieht den Fang am Werkzeug-Ende angeschlossen und hält Z-02 für
erledigt oder überflüssig. Tatsächlich ist genau die Lücke offen, die `werkzeugEnde.ts:46`
beschreibt — der Fangzustand wird nirgends gelöscht. Die Zeile schützt vor Doppelbau und lädt
gleichzeitig zum Nicht-Bauen des einen Stücks ein, das fehlt.

Der Weg zurück ist klein: Aufrufer auf `app/HausplanerApp.tsx` verkürzen und `werkzeugEnde.ts` als
das führen, was es ist — die Scheibe mit dem offenen Anschluss Z-02. Ball beim Planner; das Register
ist nicht mein Schreibbereich.

### Was offen bleibt

F-040/F-041 sind **nicht** gerechnet — der Fund kam vor der Rechnung. Sie bleiben Posten (c) der
nächsten Runde, zusammen mit F-001/F-003 aus derselben Zeile.

## §107 — Posten (c) an W-01: F-040s Grenzfall steht auf JA und rechnet Nein

Stand 5fc84c17 (LIVE trägt ihn, §106 ist über den Rückweg gesichert). Zwei tragende Formeln der
W-01-Zeile gerechnet, nicht angesehen: F-040 Rasterfang und F-041 Fangkandidat wählen.

### F-041 hält — und das Blatt hat es selbst gemeldet

`3-FORMELN.md:10` setzt die Grenzfall-Spalte auf **„JA, und ABWEICHEND"** und trägt ab `:41` die
Auflösung: der Code kennt keinen `schnittpunkt`, kennt dafür `ortho`, und stellt `mittelpunkt` vor
`achse`. Ich habe die Rangfolge unabhängig am Kern gemessen (`fangKern.ts:15`:
`endpunkt > mittelpunkt > achse > verlaengerung > ortho > raster > keiner`) — die drei Unterschiede
stimmen genau. `:50` zieht den Schluss selbst: *„Der Code ist die gebaute Wahrheit; F-041 beschreibt
eine andere Auswahl."* Kein Fund. Das Blatt liefert wieder das Material, mit dem man es widerlegen
könnte — das zehnte dieser Art.

### F-040 hält nicht

`3-FORMELN.md:9` führt F-040 auf **Zeile 192** und setzt die Grenzfall-Spalte auf
**„JA — kaufmännisch runden, sonst ist das Raster links der Null verschoben"**.

Zeile 192 lautet `{ x: Math.round(p.x / r) * r, y: Math.round(p.y / r) * r }`.

Gerechnet am kompilierten Kern (esbuild-Bündel, `fange(p, [], { raster: 100 })`), Rasterweite 100 mm:

| x | Code | kaufmännisch | |
|---|---|---|---|
| 150 | 200 | 200 | gleich |
| 50 | 100 | 100 | gleich |
| −50 | **0** | −100 | **abweichend** |
| −150 | **−100** | −200 | **abweichend** |
| −250 | **−200** | −300 | **abweichend** |
| −120 | −100 | −100 | gleich |

`Math.round` rundet Gleichstände nach **+∞**, kaufmännisch rundet sie **vom Null weg**. Rechts der
Null fällt das zusammen, links nicht.

**Genau gesagt, ohne Übertreibung:** Das Verbot des Grenzfalls („nicht abschneiden") ist eingehalten —
`Math.round` ist keine Abschneidung, und die vorhergesagte Verschiebung des *ganzen* Rasters tritt
nicht ein. Verletzt ist die *Forderung*: kaufmännisch wird nicht gerundet. Die Abweichung sitzt
ausschließlich auf den Gleichständen, also den exakten Halbzellen.

### Dass das kein Randfall ist, liegt an der Achse

`HausplanerApp.tsx:773` — `let y = -((zeiger.y - stage.y()) / zoom);` — **die y-Achse ist negiert**
(`:771`: „Welt hat Nord=+y (oben), Konva wächst nach unten ⇒ spiegeln"). Bei Bühnenursprung 0 ist
`zeiger.y >= 0`, also ist **y für jede Zeigerposition der Fläche negativ**. `raster` wird immer
übergeben (`:810` `raster: scene.settings.gridSize || 100`).

Gegenprobe über das echte Pixelraster, zoom 1, Zeilen 0…399:

```
  Pixelzeile  50: Welt -50  -> Code    0, kaufmaennisch -100
  Pixelzeile 150: Welt -150 -> Code -100, kaufmaennisch -200
  Pixelzeile 250: Welt -250 -> Code -200, kaufmaennisch -300
  Pixelzeile 350: Welt -350 -> Code -300, kaufmaennisch -400
  abweichende Pixelzeilen in 0..399: 4
```

Alle 100 Pixel eine Zeile, regelmäßig und wiederholbar — nicht verschwindend selten.

Die spürbare Folge ist keine Ungenauigkeit (bei einem Gleichstand sind beide Ziele 50 mm entfernt),
sondern **Asymmetrie auf derselben Fläche**: x ist rechts der Null positiv und bricht Gleichstände
nach außen (150→200), y ist überall negativ und bricht sie nach innen (−150→−100). Dieselbe Geste
verhält sich waagerecht anders als senkrecht. Das ist die milde Form dessen, wovor der Grenzfall
warnt.

### Und kein Test hält die Stelle

Die vier Raster-Zusagen in `__tests__/fangKern.test.ts` rechnen sämtlich mit positiven Koordinaten
(`:21` 1200 · `:49` 1234/5678 · `:61` 1010 · `:43` 12/12). **Keine negative Koordinate im ganzen
Raster-Bereich.** Der Grenzfall, den das Blatt als erfüllt führt, ist der einzige, den niemand misst.

### Einordnung

Nicht die Formel ist falsch und nicht der Zeiger — `:192` ist die richtige Zeile. Falsch ist die
**Grenzfall-Spalte**: sie sagt JA zu einer Eigenschaft, die die genannte Zeile nicht hat. Das ist
schwerer zu finden als ein toter Zeiger, weil alles daneben stimmt. Bemerkenswert ist, dass dasselbe
Blatt eine Zeile tiefer bei F-041 vorbildlich „JA, und ABWEICHEND" schreibt: die Ehrlichkeit war da,
sie hat nur diese eine Zeile nicht erreicht.

Zwei Wege, beide klein, beide nicht meine: `runde` im Rasterschritt auf kaufmännisch umstellen und
den negativen Fall in die Zusagen aufnehmen — oder die Grenzfall-Spalte auf „NEIN, Gleichstände
runden nach +∞" berichtigen. Welcher gilt, ist eine Fachentscheidung. **Ball beim Planner.**

Nebenbei: die Registerzeile führt F-041 als schlichtes „✓", wo das Blatt „JA, und ABWEICHEND" sagt —
eine Verdichtung, kein Fehler, sie verweist auf das Blatt.

## §108 — Posten (e): meine vier DoR-Ergebnisse stehen in keinem Feld, und A-38s Datensatz existiert dreifach

Stand 3a2bfd6f. Posten (d) hat bei `BEREIT 0` und `IN_ARBEIT 0` kein Subjekt — ich sage das, statt
es zu überspringen. Also (e): liegen meine offenen Befunde noch bei ihrem Halter?

### Der Rahmen, frisch gemessen

Die Statuswahrheit steht seit `0f969d5e` (16.08. 20:39, von Yama) — **5 h 56 min**, nicht ein Tag;
mein Eindruck „lange unberührt" war falsch, es ist eine Nacht. In dieser Nacht sind **309 Commits**
auf den Zweig gelaufen:

```
  107 plan-pruefer · 90 integrator · 47 (ohne Praefix) · 36 release-pruefer
   19 planner · 6 generator · 4 evaluator
```

Die 90 Integrator-Commits sind **ausnahmslos Transport** (61× „Rueckweg — rolle/plan-pruefer", 11×
planner, 6× release-pruefer, 5× generator, 3× evaluator). Die einzige Rolle, die `docs/STATUS.md`
schreiben darf (`rollen-tor.sh:344`), war hochaktiv und hat kein einziges Feld gesetzt. Das ist kein
Vorwurf — Transport ist Transport. Es erklärt aber, warum das Folgende liegen bleibt.

### Meine vier DoR-Voten gegen die Felder

Geliefert (Abschnitte 88–91, alle committet, Baum leer):

| | mein Votum | Block in STATUS.md | Blatt in aktiv/ |
|---|---|---|---|
| A-38 | **ERTEILT** (Runde 2) | `dor_beleg: "BEREIT — 2. Runde 15.08."` | `"NICHT ERTEILT — 3. Runde … Restpunkte 16.08. behoben."` |
| A-39 | **ERTEILT** | `"steht aus"` | `"steht aus — plan-pruefer."` |
| A-40 | **NICHT ERTEILT**, zwei Restpunkte | `"steht aus"` | `"steht aus — plan-pruefer."` |
| A-42 | **NICHT ERTEILT**, ein Restpunkt | `"steht aus"` | `"steht aus — plan-pruefer."` |

Alle vier Blöcke: genau **ein** Block mit `zustand` je Kennung — keine Kennungs-Dubletten, sauber
(gezählt über die Blöcke mit Zustandsfeld, nicht über die `auftrag:`-Zeilen; A-40 hat 17 solcher
Zeilen und trotzdem nur einen Datensatz). Alle vier `zustand: ENTWURF`, alle vier
`ballbesitz: plan-pruefer`.

Das ist der Stillstand: **Die Tafel sagt, ich schulde vier DoRs. Ich habe sie geliefert. Kein Feld
weiß davon.** Bei A-40 und A-42 ist „steht aus" nicht einmal ungefähr richtig — ein *NICHT ERTEILT*
mit benannten Restpunkten ist ein Ergebnis, kein Ausstand; wer „steht aus" liest, wartet auf eine
Prüfung, die längst gelaufen ist und zwei Punkte benannt hat.

Auflösen kann ich das nicht: `dor_beleg` liegt in `STATUS.md` (integrator-gesperrt) und in den
Blättern (nicht mein Schreibbereich). Was ich tun kann, ist es so genau zu benennen, dass ein
einziger Durchgang reicht — das war §92 und gilt unverändert weiter.

### A-38: derselbe Datensatz an drei Orten mit drei Werten

Das Blatt zitiert den Statusdatensatz wörtlich (`Z.325` ` ```text `, `Z.326` „Datensatz:"). Ich habe
zuerst auf Doppelschlüssel getippt und es geprüft, bevor ich es so nannte — es ist ein **Beleg**,
kein zweiter Schlüssel, also die von A-34 geschützte Bauform. Genau deshalb muss er stimmen:

```
  Zitat im Blatt  (Z.331)  dor_beleg: "steht aus"
  Original        (Z.18709) dor_beleg: "BEREIT — 2. Runde 15.08., siehe dor_votum_runde_2"
  Kopf desselben Blattes (Z.18)  dor_beleg: "NICHT ERTEILT — 3. Runde … Restpunkte 16.08. behoben."
```

Drei Orte, drei Werte, ein Feld. Das Zitat ist der älteste Stand — es wurde vor dem 15.08.
eingefroren und nicht mitgezogen; `auftrag`, `zustand`, `basis_sha` und `blatt` stimmen darin noch,
`ballbesitz` hat im Original inzwischen einen erklärenden Zusatz, den das Zitat nicht kennt. Das ist
die (a)-Klasse in Reinform: **nicht ins Leere zeigend, sondern auf etwas anderes.**

Der Kopfeintrag `Z.18` widerspricht sich zusätzlich selbst — er sagt NICHT ERTEILT und im selben
Satz, die Restpunkte seien am 16.08. behoben. Genau das habe ich in §88 nachgemessen und deshalb
ERTEILT geschrieben. Die Zeile trägt ihren eigenen Widerruf und behält trotzdem das Urteil.

**Ball beim Integrator** für die vier Felder (§92 liefert die Werte), **beim Planner** für den
Kopfeintrag `A-38:18` und das eingefrorene Zitat `A-38:326-332`.

### Und ein Fehler in eigener Sache

Meine Abschnitte heißen 38× `## NNN —` und erst seit §106 3× `## §NNN —`. Wer in dieser Datei nach
`§102` sucht, findet die Überschrift nicht, nur Verweise im Fließtext. Das ist dieselbe
Zeiger-Klasse, die ich anderen melde, und sie ist meine. Ich schreibe die 38 Überschriften **nicht**
rückwirkend um — das wäre Datei-Chirurgie an einer Datei, die andere Rollen zitieren, und es bräche
jeden Zeilenverweis darauf. Stattdessen steht die Konvention ab hier fest: `## §NNN`, und wer ältere
sucht, sucht ohne `§`.

## §109 — Posten (a) an W-36: eine Zusage, die 45 Minuten lang stimmte

Stand 964d6529. W-36 ist mit 83 Zeigern (57 verschiedene) die dichteste Werkbank-Scheibe. Geprüft
habe ich die Achsen-Tabelle, weil sie eine ausdrückliche Zusage trägt: *„**Alle vier Fundstellen
einzeln geöffnet.**"*

Vorweg eine Musterprobe, die fehlschlug: `datei.ts:zeile` traf in W-01 nicht, weil W-01 die Zeile in
einer eigenen Tabellenspalte führt (`**192**`). Erst an W-36 verifiziert, dann gezählt.

### Was hält — und das ist das meiste

| Zeiger | heute | |
|---|---|---|
| `geometry/configuratorPackage.ts:26` | Werteliste von `ConfiguratorStatus` | trifft |
| `geometry/configuratorPackage.ts:72` | `status: ConfiguratorStatus;` | trifft genau |
| `tools/faehigkeiten.ts:25` | `export type FaehigkeitZustand = …` | trifft genau |
| `tools/werkzeugZustand.ts:30` | `export type WerkzeugAnzeige = …` | trifft genau |

Und alle vier Wertelisten nachgezählt, gegen das, was das Blatt aufzählt:

```
  SchrittStatus       4 / 4    ok · prog · warn · open
  ConfiguratorStatus  7 / 7    draft · incomplete · generated · checked · approved · integrated · outdated
  FaehigkeitZustand   4 / 4    verfuegbar · voraussetzung · nur_ergebnis · in_entwicklung
  WerkzeugAnzeige     6 / 6    system · aktiv · gesperrt · angeheftet · empfohlen · weitere
```

Auch die ausdrückliche Zahl stimmt — das Blatt schreibt *„Die vierte trägt SECHS Werte"*, und es sind
sechs. Sieben Werte bei `ConfiguratorStatus` sind vollständig aufgezählt, nichts abgekürzt.

### Der eine, der gewandert ist

`SchrittStatus` steht im Blatt auf `app/studioDaten.ts:163`. Heute steht der Typ auf **Z.173**.
Zeile 163 ist nicht leer und zeigt nicht ins Nichts — sie trägt heute einen Kommentar über eine
ganz andere Sache:

```
   * `web.php:5016/5018/5020` → `objekt.blade.php:144` → `main.tsx:89` → …
```

Also die (a)-Klasse in Reinform: **zeigt auf etwas anderes.** Und der Zeiger steht zweimal in der
Scheibe — `1-ZWECK.md:62` und `2-FUNKTION.md:10`, dieselbe Tabellenzeile in zwei Dateien, beide
gleich falsch.

### Warum das keine Nachlässigkeit ist

| | |
|---|---|
| `1-ZWECK.md` angelegt, Zeigerzeile seither unberührt | `f1c412f9` **12.08. 23:23** |
| `studioDaten.ts` verschoben | `3ad920b1` **13.08. 00:08** |

**45 Minuten.** Die Zusage „alle vier einzeln geöffnet" war wahr, als sie geschrieben wurde. Sie
wurde dreiviertel Stunden später ungültig — durch einen Commit, dessen eigene Botschaft lautet:
*„A-23 gebaut: sieben überholte Begleittexte berichtigt."* Er hat sieben berichtigt und dabei diesen
erzeugt.

`3ad920b1` ist derselbe Commit, den ich in §77 als einen der beiden Verschieber gemessen habe
(`e0722979` +20, `3ad920b1` +24, 38 gewanderte Zeiger über vier Träger). W-36 ist der fünfte Träger,
und er zeigt den Mechanismus in seiner reinsten Form: **die Sorgfalt war da, sie hatte nur eine
Dreiviertelstunde Haltbarkeit.** Das ist genau die Zeitachse aus §93 — nicht wo gewachsen wird,
sondern wann gezeigt wird.

Zwei Zeiger auf `:173`, oder auf die Form ohne Zahl (§81/§96). **Ball beim Planner**, zum Bündel aus
§77/§93/§102/§104.

## §110 — Posten (b) an W-38: die Zahl hält, die Ablesung nicht

Stand 71ec8776. W-38 eröffnet mit einer ungewöhnlich starken Zusage:

> *„**Dies ist eine ABLESUNG, keine Vorgabe.** Der Code existiert:
> `resources/planner/hausplaner/app/studioDaten.ts`, **257 Zeilen**. Jede Aussage in diesen sieben
> Blättern ist an ihm gemessen und trägt ihre Fundstelle."*

Die Scheibe stellt sich also ausdrücklich darauf, an einem gemessenen Gegenstand zu hängen. Genau
das habe ich geprüft.

### Die gezählte Zahl hält

`STEPS_STILLGELEGT` — das Blatt sagt **„elf Schritte"**. Gezählt mit zwei Verfahren, weil das erste
sich widersprach: eine Klammerzählung ergab 11, eine Zählung über `titel:` aber 23. Der Widerspruch
löst sich an der Struktur — `titel:` steht auch in `aufgaben` und `empfehlung`, `status:` auch in
`checks`, alle verschachtelt und inline. Auf der Schritt-Ebene gemessen:

```
  Zeilen "^  {"      im Array Z.196-253:  11
  Zeilen "^    titel:" im selben Bereich:  11
```

Zwei Verfahren, dasselbe Ergebnis. **Elf ist richtig.**

### Die abgelesene Zahl hält nicht

```
  studioDaten.ts heute:  267 Zeilen
  Blatt:                 257 Zeilen
```

Zehn Zeilen, und es sind dieselben zehn wie überall in diesem Bündel.

### Die Zeiger trennen sich sauber nach Datei

| Zeiger | Ziel | heute | |
|---|---|---|---|
| `GuidedView.tsx:18` | `badgeFarbe` | Z.18 | trifft |
| `GuidedView.tsx:71` | Text aus `STATUS_LABEL` | Z.71 | trifft |
| `studioDaten.ts:157` | `ZULETZT_STILLGELEGT` | **Z.167** | +10 |
| `studioDaten.ts:163` | `SchrittStatus` | **Z.173** | +10 |
| `studioDaten.ts:186` | `STEPS_STILLGELEGT` | **Z.196** | +10 |
| `studioDaten.ts:255` | `STATUS_LABEL` | **Z.265** | +10 |

**Jeder Zeiger in die eine Datei trifft, jeder Zeiger in die andere ist um zehn daneben.** Kein
Streuungsbild, ein Schnitt. Und was heute auf den alten Nummern steht, ist nicht leer: `:157` trägt
einen Kommentarsatz über „Teil B und liegt bei Yama", `:186` ein `/**`. Wieder die (a)-Klasse —
zeigt auf etwas anderes.

`studioDaten.ts:163` ist derselbe Zeiger, den §109 in W-36 zweimal gefunden hat. Er existiert damit
an **drei** Stellen, alle drei falsch.

### Ein Fehlfund, den ich knapp vermieden habe

Ich hatte `:18` und `:71` gegen `studioDaten.ts` gemessen und wäre auf zwei weitere Abweichungen
gekommen. Sie gehören aber zu `app/GuidedView.tsx` — `4-BEDIENUNG.md:53` sagt das ausdrücklich
(*„Gezeichnet wird das in `app/GuidedView.tsx` (W-34)"*), ich hatte nur die nackten `:NN` aus der
Sammelsuche genommen. Richtige Messung, falsche Eingabe — dieselbe Klasse wie §101. Bei nackten
Zeilenzeigern muss die Trägerdatei aus dem Satz geholt werden, nicht aus dem Zusammenhang.

### Zeitachse

| | |
|---|---|
| W-38 geschrieben, „257 Zeilen" seither unberührt | `fa83a2dc` **12.08. 15:02** |
| `studioDaten.ts` verschoben | `3ad920b1` **13.08. 00:08** |
| Commits an `studioDaten.ts` seither | **1** |

**Neun Stunden sechs Minuten**, und genau ein Commit. Derselbe, der W-36 nach 45 Minuten überholt
hat. Ein einziger Bau hat in dieser Scheibe fünf Zahlen ungültig gemacht — vier Zeiger und die
Ablesung — und in W-36 zwei weitere. W-38 ist der sechste Träger.

Das ist der Kern, den §77/§93 benennen und der sich hier zum sechsten Mal zeigt: **nicht die
Sorgfalt fehlt, sondern die Haltbarkeit.** Die Scheibe sagt „ist an ihm gemessen" und hat recht —
sie sagt nur nicht, wann.

**Ball beim Planner**, zum Bündel §77/§93/§102/§104/§109.

## §111 — Posten (c) an W-29: F-010 rechnet richtig, und das Blatt ist an seinem eigenen Erfolg veraltet

Stand 839a8621. W-29 führt drei F-Nummern. Ich habe die rechenbare genommen: **F-010 Orientierung
(Schuhbandformel)**, im Blatt mit `:86` und `a += p.x*q.y − q.x*p.y` zitiert.

### Der Zeiger trifft, und die Formel rechnet

Trägerdatei aus dem Satz geholt, nicht aus dem Zusammenhang (die Lehre aus §110): `dachAusschnitt.ts`
(510 Z., passend zu den Aufrufen `:314`/`:375`/`:468`). **`:86` trägt die Zeile wörtlich**, in
`signierteFlaeche` (`:82-89`), Rückgabe `a / 2`.

Gerechnet am kompilierten Modul, fünf Vierecke:

```
  Fall                      signierteFlaeche   istKonvexesViereck
  Quadrat gegen Uhrzeiger          16              true
  Quadrat MIT Uhrzeiger           -16              true
  konkaves Viereck                  4              false
  kollinear (entartet)              0              false
  Trapez gegen Uhrzeiger         13.5              true
```

Dasselbe Quadrat, zwei Umlaufrichtungen, Vorzeichen kippt — **„das Vorzeichen ist die Orientierung"
stimmt.** Und die Prüfung ist robuster als nötig: `:102` nimmt `orient = Math.sign(area)` und misst
jede Ecke gegen `cross * orient`, deshalb bestehen beide Umlaufrichtungen. Entartet und konkav
fallen durch. Die Formel hält an allen fünf Fällen.

*Nebenbei:* Der Kommentar über der Funktion (`:91-94`) verlangt „**positiver** Fläche". Der Code
verlangt das nicht — er verlangt *nicht-entartete* Fläche und normiert die Richtung weg. Der Code
ist hier großzügiger als seine Beschreibung; wer sich auf den Kommentar verlässt, normiert umsonst.

### Die Registerzeile habe ich ganz nachgerechnet, und sie hält vollständig

| Zusage | Messung | |
|---|---|---|
| „780 Z. Geometrie" | 96 (`dachOeffnung`) + 510 (`dachAusschnitt`) + 174 (`auswechslung`) = **780** | trifft |
| „82 grüne Zusagen" | 71 (`dachAusschnitt.test.ts`) + 11 (`auswechslung.test.ts`) = **82** | trifft |
| „NULL Produktivverbraucher" | 3 Nennungen außerhalb der Tests, **alle drei Kommentare** — eine erklärt sogar, warum die Funktion *nicht* benutzt wird (`kontur.ts:15`) | trifft |
| „F-004 trägt NICHT" | `geradenSchnitt`/`schnittpunkt` in `dachAusschnitt.ts`: **0** | trifft |

Die Zeile misst über die Wirkung, nicht über den Ort — sie sagt sogar selbst, die Treffer seien
„Begründungstexte". Das ist P7 richtig angewandt.

### Der Fund

Das Formelblatt führt eine Spalte „Registerzeile" und trägt dort **dreimal „fehlt"** ein — für
F-011, F-010 und F-004. Die Registerzeile nennt heute **alle drei**, mit Häkchen, Fundstelle und
Einschränkung („F-010 ✓ **als VORZEICHEN, nicht als Fläche**"). Und sie sagt dazu, woher das kommt:

> *„**F-ZUORDNUNG ERGÄNZT 16.08. mit der Ablesung**, die Zeile nannte gar keine."*

| | |
|---|---|
| Blatt geschrieben, „fehlt"-Spalte seither unberührt | `28ea0432` **16.08. 13:55** |
| Register mit genau dieser Ablesung ergänzt | `1e1afd1b` **16.08. 17:47** (Yama) |

**Drei Stunden zweiundfünfzig Minuten.** Das Blatt hat gemessen, dass das Register nichts nennt —
und hat damit bewirkt, dass es etwas nennt. Seine eigene Spalte beschreibt seither einen Zustand,
den es selbst beendet hat.

Das ist dieselbe Zeitachse wie §109/§110, aber die Richtung ist umgekehrt: dort bewegte sich der
Code unter dem Dokument, hier hat **das Dokument seine eigene Aussage überholt**. Kein fremder
Eingriff, keine Nachlässigkeit — der Erfolg selbst ist der Verfallsgrund. Ein Blatt, das den Mangel
meldet, den es behebt, muss die Meldung mit einem Datum versehen oder sie fällt in dem Moment, in
dem sie wirkt.

Vorschlag als Form, nicht als Auftrag: die Spalte auf „fehlte am 16.08. 13:55, ergänzt 17:47"
setzen, oder auf „nachgetragen" — was der Planner entscheidet. **Ball beim Planner**, zum Bündel
§77/§93/§109/§110, wo es die Zeitachse verstärkt.

### Und ein Fehler von mir

Ich hatte den Registersatz abgeschnitten gelesen — er endete in meiner Ausgabe bei `ADD_ROOF…` — und
die Endung aus dem Thema der Scheibe ergänzt: `ADD_ROOF_OPENING`. Ich habe dann gemessen, dass es
den Namen nirgends gibt, und war einen Schritt davon entfernt, dem Register ein erfundenes Symbol
vorzuwerfen. Tatsächlich steht dort `ADD_ROOF_AUFBAU`, und das existiert. Die Rettung war, den
vollen Satz zu holen, statt auf der Lücke weiterzurechnen: **ein abgeschnittenes Zitat ist kein
Zitat.** Nächste Verwandte von §101 (falsche Eingabe) und §110 (falsche Trägerdatei) — dieselbe
Familie, drittes Gesicht.

## §112 — Posten (d) Alterung: die Statuswahrheit schweigt so lange wie nie, eine Tafelzeile wurde bei laufender Überholung neu geschrieben, und mein eigener Messstand aus §-der-letzten-Alterung trug seine Zahlen nicht

**Stand:** HEAD `cba422dd`, getrackt **0**, mit `fork` **0/0**. Gemessen **19.08. 13:1x**.
**Seit HEAD sind 58 h 21 min vergangen — 0 Commits von irgendeiner Rolle.**

### Die Erhebung: sechs Aufträge, heute gegen die letzte Alterungsrunde

```
git log -1 --format=%ct <basis> · git rev-list --count <basis>..HEAD · git merge-base --is-ancestor
```

| Auftrag | Zustand | basis_sha | Alter damals | Alter heute | Commits damals | heute |
|---|---|---|---|---|---|---|
| A-37 | `CODE_FERTIG` | `bc2125d9` | 2812 min | **6658 min (110 h)** | 777 | **1021** |
| A-38 | `ENTWURF` | `0f05f8bf` | 2775 min | **6621 min (110 h)** | 739 | **983** |
| A-39 | `ENTWURF` | `99add90f` | 441 min | **4287 min (71 h)** | 637 | **881** |
| A-40 | `ENTWURF` | `99add90f` | 441 min | **4287 min (71 h)** | 637 | **881** |
| A-42 | `ENTWURF` | `e802c1f8` | 222 min | **4068 min (67 h)** | 565 | **809** |
| W-21L | `DECISION_BLOCKED` | `4f0d4584` | 7006 min | **10852 min (180 h)** | 1873 | **2117** |

Alle sechs Basis-SHAs sind Vorfahr von HEAD (`--is-ancestor` → ja, 6/6). **Das Alter wuchs bei
allen sechs um exakt 3846 Minuten, die Commit-Zahl bei allen sechs um exakt 244.** Zwei
Gleichmaße — das erste ist die Uhr, das zweite wird unten zum dritten Fund.

**64 Stunden Alterung, und 58 davon ohne einen einzigen Commit.** Die zweite Spalte der
Alterungsmessung ist immer der Stellvertreter dafür gewesen, *wieviel sich unter dem Blatt bewegt
hat*. In dieser Runde entkoppeln sich die beiden Spalten vollständig: 91 % der neuen Alterung
tragen null Bewegung.

### Fund 1 — die Statuswahrheit schweigt länger als je zuvor, und diesmal ist es keine Pause

```
git log -1 --format='%h %cd' -- docs/STATUS.md        -> 0f969d5e  16.08. 20:39
git rev-list --count 0f969d5e..HEAD                   -> 313
git rev-list --count 0f969d5e..HEAD -- docs/STATUS.md -> 0
```

**`docs/STATUS.md` wurde zuletzt am 16.08. um 20:39 geschrieben. Das sind 3874 Minuten —
64 Stunden 34.** Gegenprobe über alle Zweige: es gibt keine jüngere Schreibung, auch nicht in
`rolle/*`.

Die drei größten Lücken der Projektgeschichte, aus 1339 Schreibungen, mit der Bewegung *während*
der Lücke:

| Lücke | Zeitraum | Commits **in** der Lücke | Charakter |
|---|---|---|---|
| 2675 min | 08.08. 21:44 → 10.08. 18:20 | **48** | Pause (1,1/h) |
| 1684 min | 07.08. 09:53 → 08.08. 13:58 | **1** | Pause |
| **3874 min** | **16.08. 20:39 → offen** | **313** | **keine Pause** |

Die bisherige Rekordlücke war eine Pause: 48 Commits über 44 Stunden. Die heutige ist **die
längste und zugleich die vollste** — 313 Commits, davon **312 in den ersten 6 h 11**, das sind
50 Commits pro Stunde. Danach 58 Stunden Stille. Die Rollenverteilung dieser 313: 111 plan-pruefer,
90 integrator, 36 release-pruefer, 19 planner, 6 generator, 4 evaluator, Rest Merges.

Und der Ort, an dem sie abriss, ist nicht beliebig. Das dichteste Sechs-Stunden-Fenster der
**gesamten** Projektgeschichte liegt am **16.08. 16:13–22:13 mit 505 Commits** (Maximumssuche über
alle Commit-Zeiten). Die letzte Statusschreibung fällt **mitten hinein**: 372 dieser 505 liegen
davor, **133 danach**. Die Statuswahrheit ist im Spitzenfenster verstummt, 94 Minuten vor dessen
Ende, und nie wieder angesprungen.

**Das ist die Umkehrung von §97.** Dort war der Befund: lange Lücken gab es oft, aber es waren
Pausen — diese ist die erste unter Volllast. Heute ist die Lücke *beides nacheinander*: erst die
dichteste Arbeit des Projekts ohne jede Statusführung, dann der längste Stillstand. Für Posten (d)
heißt das: **das Alter der sechs Aufträge ist seit 64 Stunden nirgends abgebildet**, weil das eine
Feld, das es abbilden würde, seit 64 Stunden nicht angefasst wurde.

### Fund 2 — die A-37-Tafelzeile wurde neu geschrieben, während sie bereits überholt war

Die Zeile in `docs/STATUS.md` sagt heute wörtlich:

> **Elf Kriterien**, A-37-2 und A-37-7 sind die **Positivfaelle**. Gebaut wird im
> Generator-Worktree. **DoR steht aus.**

Gezählt im Blatt, zwei Verfahren, beide 21: Zeilenanfänge `- **A-37-N**` → **21**;
`grep -oE 'A-37-[0-9]+' | sort -u` → **21**.

Die Wachstumskurve des Blattes und die drei Schreibungen der Tafelzeile nebeneinander:

| Blatt | Kriterien | | Tafelzeile geschrieben | Zustand | sagt |
|---|---|---|---|---|---|
| `7ef8f046` 14.08. 22:35 | 11 | | `53a0947e` 15.08. 11:58 | `ENTWURF` | „Elf" ✓ |
| `3719937f` 16.08. **12:48** | **14** | | `4ed51b8f` 16.08. **12:39** | `BEREIT` | „Elf" ✓ *(9 min davor)* |
| `78841603` 16.08. 16:20 | 18 | | | | |
| `4a10abca` 16.08. 19:43 | 19 | | `15e11078` 16.08. **20:16** | `CODE_FERTIG` | **„Elf" — das Blatt hat 19** |
| `b6a79a66` 16.08. 23:50 | 21 | | *(keine Schreibung mehr)* | | **„Elf" — das Blatt hat 21** |

An den Ständen der drei Schreibungen nachgemessen (`git show <sha>:<blatt> | grep -c`): 11, 11,
**19**. Die ersten beiden Schreibungen waren richtig. **Die dritte war es nicht mehr, und sie hat
die Zahl trotzdem mitgenommen.**

Das ist eine andere Klasse als §109/§110/§111. Dort war die Aussage wahr, als sie geschrieben
wurde, und ist unter dem Dokument gealtert. **Hier wurde die Zelle angefasst — Zustandswechsel
`BEREIT` → `CODE_FERTIG` — und der acht Kriterien alte Wert wurde durch die Bearbeitung
hindurchgetragen.** Nicht Alterung, sondern Transport eines toten Werts durch einen lebenden Griff.

Dieselbe Zelle trägt zwei weitere Aussagen, beide zum selben Zeitpunkt bereits falsch:

- **„A-37-2 und A-37-7 sind die Positivfälle"** — das ist eine erschöpfende Aussage. `A-37-14`
  („**Positivfall:** Marke stimmt → Lauf geht durch") kam am **16.08. 12:48** ins Blatt, also
  **7 h 28 min vor** der Schreibung um 20:16. Es sind drei, nicht zwei. *(A-37-7 selbst hält —
  Wortlaut „der Positivfall zur Sperre"; ich hatte zuerst nur auf das Wort „Positivfall" gefiltert
  und A-37-7 fälschlich als weggefallen gelesen. Erst der volle Satz hat es gerettet — dieselbe
  Lehre wie §111.)*
- **„DoR steht aus"** — es liegen Voten vor: `a400368f` 16.08. 13:01 *„DoR Runde 3 für A-37 und
  A-38 — NICHT ERTEILT, fünf Restpunkte"*, fortgesetzt `679f849a` 16:00. Ein `ERTEILT` für A-37
  existiert auf keinem Zweig. **Das ist der A-37-Zwilling zu §108:** ein `NICHT ERTEILT` mit
  benannten Restpunkten ist ein Ergebnis, kein Ausstand. *Kein neuer Befund ist die Lage
  dahinter — dass A-37s `BEREIT` auf einem zurückgenommenen Votum ruht, steht seit `b5dc8a03`
  (16.08. 17:00) und wird hier nur wiedergefunden.*

**Und der Commit, der die tote Zahl weitergetragen hat, heißt:**

> `15e11078` — *„integrator: zwei eigene Fehler behoben — **die Statuswahrheit stand still**, und
> meine 81 Merges waren unsichtbar."*

Ein Commit, der den Stillstand der Statuswahrheit behebt, schreibt dabei eine acht Kriterien alte
Zahl fort — und 23 Minuten später steht die Statuswahrheit für 64 Stunden still. Das ist die
Figur aus §109 (`3ad920b1` berichtigte sieben überholte Texte und erzeugte den achten) und §111
(das Blatt meldet den Mangel, den es behebt), zum dritten Mal, an der dritten Rolle.

### Fund 3 — in eigener Sache: mein erklärter Messstand trug keine einzige der sechs Zahlen

> **ZURÜCKGENOMMEN 40 Minuten später — siehe §113.** Der Anker `32b8bcee` ist der **Elter** des
> Schreib-Commits und formal einwandfrei; die Runde zählte über **alle Zweige**, ich habe sie mit
> `basis..HEAD` nachgerechnet. `git rev-list --count --all --until='16.08. 21:05:59' ^basis`
> reproduziert **alle sechs Zahlen exakt**. Der Abschnitt bleibt stehen, weil er zitiert ist —
> gültig ist §113. **Die Zahlen der alten Runde sind richtig.**

Die gleichmäßige **+244** über sechs verschiedene Basis-SHAs sagt, dass beide Messungen an je
einem festen Stand genommen wurden und diese Stände 244 Commits auseinanderliegen. Die letzte
Alterungsrunde erklärt ihren Stand ausdrücklich: *„geschrieben 21:08, Messstand `32b8bcee`"*.

```
git rev-list --count 32b8bcee..HEAD   -> 280      (nicht 244)
git rev-list HEAD | sed -n '245p'     -> bea33236  16.08. 21:29
git rev-list --count 32b8bcee..bea33236 -> 36
```

Beide Kandidaten direkt gegen die sechs veröffentlichten Zahlen gerechnet:

| Auftrag | `@32b8bcee` | `@bea33236` | im Blatt | trifft |
|---|---|---|---|---|
| A-37 | 741 | **777** | 777 | `bea33236` |
| A-38 | 703 | **739** | 739 | `bea33236` |
| A-39 | 601 | **637** | 637 | `bea33236` |
| A-40 | 601 | **637** | 637 | `bea33236` |
| A-42 | 529 | **565** | 565 | `bea33236` |
| W-21L | 1837 | **1873** | 1873 | `bea33236` |

**Sechs von sechs treffen `bea33236`, null von sechs treffen den erklärten Stand.** 36 Commits
und 24 Minuten Abstand.

**Die Zahlen waren richtig — der Anker war es nie.** Das ist das Spiegelbild der drei letzten
Runden: dort ein richtiger Anker mit alternder Zahl, hier eine richtige Zahl an falschem Anker.
Wer die Runde nachrechnen wollte, hätte 741 statt 777 gefunden und meine Messung für falsch
gehalten — die Angabe, die den Beweis führbar machen soll, macht ihn hier unführbar. Genau das
werfe ich seit §77 den Blättern vor. **Geht in die Bilanz.**

*Ein Zählfehler ist mir dabei unterlaufen und wurde vor dem Schreiben gefangen: mein erster
Zuordnungslauf las ein `bash`-Array mit `${a[0]}` unter **zsh**, wo Arrays bei 1 beginnen — die
Blatt-Spalte war um eine Zeile verschoben und meldete „KEINER" für fünf von sechs. Die Rohzahlen
waren beide Male dieselben; falsch war nur die Paarung. Wiederholt mit expliziter Paarung, dann
6/6. §-Lehre unverändert: zweimal messen, einmal schreiben.*

### Zweiter Teil von Posten (d): nennen die Blätter Dateien, die sich seither bewegt haben?

Gemessen nicht gegen `basis_sha` (das hat die letzte Runde getan, Ergebnis „kein Befund, der
Löwenanteil ist `STATUS.md`"), sondern gegen **das Verstummen der Statuswahrheit** — welche von den
Blättern genannten Code-Dateien haben sich bewegt, seit der Status sie nicht mehr abbilden konnte?

| Auftrag | Datei | Commits seit `0f969d5e` |
|---|---|---|
| A-37 | `scripts/rollen-tor.sh` | 1 |
| A-37 / A-38 / A-39 | `scripts/commit-pruefen.sh` | 2 |
| A-42 | `scripts/status-erzeugen.sh` | 2 |
| A-42 | `scripts/yama-posten.py` | 2 |
| A-40, W-21L | — | 0 |

**Kein eigener Befund**, und der Grund gehört genannt: es sind je das Werkzeug, das der Auftrag
baut (A-37), oder sein ausdrückliches Nicht-Ziel (A-42). Beides ist erwartbare Bewegung. Der
Posten läuft hier sauber durch und findet nichts Drittes — das ist die Antwort auf die Frage, nicht
ihr Ausbleiben. *(`scripts/blatt-pruefen.sh` aus A-39 existiert nicht; das ist A-39s Rot-Lage,
kein gewanderter Zeiger.)*

### Ball

**Beim Integrator** — die A-37-Tafelzeile: drei Aussagen in einer Zelle, alle drei bei der letzten
Schreibung bereits falsch. Er ist die einzige Rolle mit Schreibrecht auf `docs/STATUS.md`, und
`15e11078` ist seine Schreibung.

**Bei Yama** — die 64 Stunden. Ob die Statuswahrheit nachgeführt wird oder ob der Stand vom
16.08. 20:39 als gültiger Schnitt erklärt wird, ist keine Messfrage. **Sie steht auf einem Stand,
der 313 Commits alt ist**, und jede Zustandsangabe darin ist an diesem Stand zu lesen, nicht am
heutigen.

**Bei mir** — Fund 3 in die Bilanz, und beim Eintragen ist ein vierter aufgefallen:

### Fund 4 — die Bilanz meiner eigenen Fehler steht seit demselben Commit still

```
hoechste Nummer in docs/BILANZ-plan-pruefer-eigene-fehler.md   -> 19
letzte Schreibung der Bilanz                                   -> 32b8bcee  16.08. 21:05
```

| Fehler | Commit | in der BILANZ | im BEFUND-Blatt |
|---|---|---|---|
| 21 | `d771e71d` 22:48 | **0** | 2 |
| 22 | `94c98ad0` 21:46 | **0** | 1 |
| 23 / 24 | `761b7e96` 22:21 | **0** | 5 / 1 |
| 25 | `1bfdbd0f` 23:53 | **0** | 5 |
| 26 | `1d386676` 00:32 | **0** | 7 |
| 27 | `cdd80e81` 02:02 | **0** | 8 |
| 28 | `4f6b65b1` 02:23 | **0** | 7 |
| 29 | `23cd7fdc` 01:16 | **0** | 2 |

**Neun eigene Fehler sind vergeben, benannt und committet — und keiner einzige steht in dem
Dokument, das Yama ausdrücklich dafür angeordnet hat** (*„kannst du auch im Bezug auf dich alle
Fehler erst mal suchen, wieviel sie sind, dann alle nacheinander beheben"*). Die Bilanz sagt
**19**; gezählt sind es mindestens **28**. *Nummer 20 findet sich in keinem Commit — eine Lücke in
meiner eigenen Nummernvergabe, die ich hier nicht auflöse, sondern melde.*

Und der Commit, der die Bilanz zuletzt schrieb, ist `32b8bcee` — **derselbe, der in Fund 3 als
Messstand erklärt wird und die Zahlen nicht trägt.** Beide Male dieselbe Stunde, beide Male
dieselbe Ursache: ab 21:05 lief die Arbeit schneller, als sie verbucht wurde. Das ist genau der
Vorwurf aus Fund 1, angewandt auf mich selbst — der Unterschied ist nur, dass die Statuswahrheit
34 Minuten später verstummte und meine Bilanz sofort.

*Nachgetragen in `docs/BILANZ-plan-pruefer-eigene-fehler.md` (Bestandsaufnahme, keine
Rückdatierung — die neun Einträge bleiben dort, wo sie geschrieben wurden, und werden benannt).*

**Kein Zustandsfeld angefasst, kein Bau.**

## §113 — P-03 nachgemessen: kein verdeckter Rückstand, aber 36 Blätter, von denen keines stimmt

Stand cba422dd. P-03 ist mein eigener Selbstbefund vom 16.08. 18:27: meine Ballortung liest nur
`docs/STATUS.md`, während die Auftragsblätter eigene Ballfelder tragen. Er schließt mit einer Zusage:
*„Die Ballortung braucht eine zweite Quelle: die Blätter. Ich führe das ab sofort mit."*

**Diese Zusage habe ich nicht gehalten.** In §106–§111 lief die Ballortung ausschließlich auf der
Statuswahrheit. Blätter kamen nur vor, wenn eine Kennung ohnehin in meiner Bahn lag (§108) — nie als
*Quelle* der Ballortung. Zwei Tage, sechs Runden, die eigene Regel nicht angewandt.

### Die Frage, die P-03 offenließ, ist beantwortet

```
  Blaetter in docs/auftraege/aktiv/            89   (79 nennen 'plan-pruefer')
  erstes dor_beleg = "steht aus — plan-pruefer" 36
  davon MIT Datensatz (zustand) in STATUS.md    36
  davon OHNE                                     0
```

**Es gibt keinen verdeckten Rückstand.** Kein einziger der 36 ist ein unbeaufsichtigter Auftrag. Die
Sorge, ich könnte 36 Prüfungen liegen lassen und stattdessen Vorratsprüfung fahren, ist ausgeräumt —
gemessen, nicht beruhigt.

### Was stattdessen dasteht

| Statuswahrheit | Blatt sagt „steht aus" | Zahl |
|---|---|---|
| `BETRIEBSBESTAETIGT`, mit echtem DoR-Beleg | **widerspricht** | **30** |
| `ZURUECKGEZOGEN` (A-36) | **widerspricht** | **1** |
| `ENTWURF` (A-39, A-40, A-42) | stimmt überein | 3 |
| `BETRIEBSBESTAETIGT`, dor_beleg auch „steht aus" (A-41, W-17/1) | stimmt überein | 2 |

Die 30 sind der Kern. Ihre Statuswahrheit trägt fertige Voten — `"ERTEILT 13.08. plan-pruefer, jede
Zahl und …"` (A-29), `"NICHT erteilt 13.08. plan-pruefer, gemessen …"` (A-30), `"plan-pruefer 12.08.,
FUENFTE Fassung"` (A-22) — und ihr Blatt behauptet weiterhin, die DoR stehe aus. Diese Aufträge sind
durch die ganze Kette gelaufen und **betriebsbestätigt**; das Blattfeld ist auf dem Stand von vor der
Prüfung stehengeblieben.

**Übereinstimmung ist hier nicht Richtigkeit.** Von den fünf Übereinstimmern sind A-39/A-40/A-42
genau die, für die §108 zeigt, dass **beide** Seiten überholt sind — die Voten liegen vor
(ERTEILT / NICHT ERTEILT mit Restpunkten), nur in keinem Feld. Und A-41 und W-17/1 sind in sich
widersprüchlich: betriebsbestätigt, und die DoR steht laut beiden Quellen noch aus.

Damit ist **keiner der 36 sauber**: 31 widersprechen der Statuswahrheit, 3 sind beidseitig veraltet,
2 sind in sich unstimmig.

### Einordnung

§108 hat diesen Fehler an vier Feldern gezeigt. P-03 hat vermutet, dass er größer ist. Gemessen ist
er **31-fach** — dieselbe A-20-Drift, nur auf der Seite, die meine Wache nicht liest. Die Richtung ist
immer dieselbe: die Statuswahrheit bewegt sich, das Blattfeld bleibt auf dem Stand seiner Entstehung.

Der Schaden ist nicht theoretisch. Wer vor einer Prüfung ins Blatt sieht — und das ist der natürliche
Ort — liest bei 30 abgenommenen Aufträgen „DoR steht aus" und fordert eine Prüfung an, die längst
gelaufen und betriebsbestätigt ist.

**Ball beim Integrator** für die Felder (er hält als einziger Schreibrecht auf die Statuswahrheit;
die Blätter liegen beim Planner). **Ball beim Planner** für die 31 Blattfelder. **Bei mir** bleibt
die Methodenpflicht aus P-03, ab dieser Runde tatsächlich mitgeführt.

### Ein Fehlfund, vor der Meldung abgefangen

Mein erster Durchgang meldete „11 Blätter ohne Datensatz". Ursache: ich bildete die Kennung aus dem
Dateinamen (`W-03-1`), die Statuswahrheit führt aber `W-03/1`. An einem bekannten Treffer geprüft
(`W-12/1` vorhanden: 1, `W-12-1`: 0) — der Fund löste sich vollständig auf, 36 von 36 haben einen
Datensatz. Dieselbe Kennungsform hat mich schon einmal erwischt (W-21-1 gegen W-21/1). Sie gehört zur
Familie §101/§110/§111: richtig gemessen, falsche Eingabe.

### Nachtrag in eigener Sache: dieser Abschnitt hieß zuerst §112

Ich habe den Messstand um **13:13** fixiert und um **15:46** committet — zweieinhalb Stunden dazwischen,
**ohne ihn neu zu messen**. In dieser Lücke, um **13:40:34**, hat eine zweite Plan-Prüfer-Instanz
`8ad16cfa` committet und die Nummer §112 vergeben (Posten (d), Alterung). Ich habe sie ein zweites Mal
vergeben; die Datei trug kurzzeitig zwei Abschnitte §112. Hier auf §113 berichtigt, weil meiner der
spätere ist.

Zwei Dinge dazu, beide unangenehm und beide meine:

1. **Der Stillstand, den ich Yama gemeldet habe, war zum Zeitpunkt der Meldung schon vorbei.** Ich
   habe 58 h 22 min gemessen — richtig um 13:13 — und daraus einen Zustand beschrieben, der 27 Minuten
   später endete. Die Messung war wahr, die Aussage war es beim Aussprechen nicht mehr. Das ist
   dieselbe Zeitachse, die ich in §109/§110/§111 an fremden Blättern gemessen habe, jetzt an meiner
   eigenen Meldung.
2. **Die Gegenprobe kam nach dem Commit statt vor ihm.** Genau dafür steht sie in der Wache. Hätte ich
   vor dem Anfügen neu gemessen, wäre die Dublette nie entstanden.

Was *nicht* betroffen ist, habe ich geprüft: `docs/STATUS.md` und alle 89 Blätter unter
`docs/auftraege/aktiv/` sind zwischen `cba422dd` und `8ad16cfa` **byte-identisch** (der fremde Commit
fasst nur meine Befunddatei und die Bilanz an). Die 36/31/5-Zahlen dieses Abschnitts stehen also
unverändert. Und inhaltlich kollidieren die beiden Abschnitte nicht: das fremde §112 nennt P-03 kein
einziges Mal und `dor_beleg` nirgends.

**Zweite Instanz:** dass zwei Plan-Prüfer gleichzeitig in dieselbe Datei schreiben, ist der Fall, den
die Nebenläufigkeitsregel meint. Ich melde es, repariere daran nichts weiter und stage weiterhin nur
meine eigene Datei.

## §113 — Posten (e) an meinem eigenen jüngsten Befund: Fehler 30 ist zurückgenommen. Der Anker war richtig, die Zählweise war eine andere — und der Beweis stand 19 Runden lang bereit

**Stand:** HEAD `8ad16cfa`, getrackt 0, **nicht gepusht**. Gemessen 19.08. 14:0x.
**Gegenstand: mein eigener Befund aus §112, 40 Minuten alt.** Posten (e) verlangt, den eigenen
zugestellten Punkten nachzugehen; der jüngste ist Fehler 30, und er hat die Nachprüfung nicht
überstanden.

### Die Sweep-Frage: ist Fehler 30 ein Einzelfall oder eine Klasse?

Ein falscher Messstand wäre schlimm, wenn er die Regel wäre. Also alle Erklärungen im Blatt
gesammelt und einzeln geprüft — **19 verschiedene Stände in 20 Nennungen** — mit dem strengen Test:
*ist der erklärte Stand Vorfahr des Commits, der die Erklärung schrieb?*

```
für jeden Stand M:  C = git log -S"Messstand M" -- <blatt> | tail -1
                    git merge-base --is-ancestor M C
```

| Ergebnis | |
|---|---|
| Stände geprüft | **19** |
| Vorfahr des eigenen Schreib-Commits | **19 von 19** |
| Abstand Stand → Schreibung | durchweg **2–5 Minuten** |
| Kette | jeder Stand ist die Schreibung der Vorrunde (`001abb9e`→`83635ca7`→`339bc8d3`→…) |

**Keine Klasse. Die Ankerführung ist über 19 Runden lückenlos.** Und der angeblich schuldige Stand
ist der sauberste von allen:

```
git log -1 --format='%h %cd Elter: %p' dc6abbd1
  -> dc6abbd1  16.08. 21:08  Elter: 32b8bcee
```

**`32b8bcee` ist nicht irgendein Stand — es ist der Elter des Schreib-Commits.** Die Runde erklärt
„geschrieben 21:08", und `dc6abbd1` trägt als Commit-Zeit exakt 21:08. Genauer geht ein Anker nicht.

### Dann musste die andere Seite falsch sein

Wenn der Anker stimmt und die Zahlen nicht dazu passen, ist entweder die Datei später geändert
worden oder **meine Nachrechnung benutzt ein anderes Verfahren**. Beides ist prüfbar.

**Datei später geändert? Nein.** Die Tabelle in `dc6abbd1` ist Zeichen für Zeichen dieselbe wie
heute — 777 stand schon um 21:08 darin, und der Abschnitt wurde bis zu meinem §112 nie wieder
angefasst (`git log -S'2812'` findet genau zwei Commits: `dc6abbd1` und `8ad16cfa`).

**Und damit fällt meine eigene Erklärung aus §112.** Ich hatte behauptet, die Zahlen seien an
`bea33236` (21:29) gemessen. Von den 36 Commits zwischen den beiden Ständen sind **21 nach 21:08
autorisiert** — sie existierten zur Schreibzeit in keinem Baum der Welt. Meine Erklärung war
nicht nur unbelegt, sie war **unmöglich**, und ich habe sie geschrieben, ohne diese eine
Zeitprüfung zu machen.

### Die Auflösung: Zählweite, nicht Anker

| Verfahren am Stand 21:05 | A-37 | A-38 | A-39 | A-40 | A-42 | W-21L |
|---|---|---|---|---|---|---|
| `basis..HEAD` *(mein §112)* | 741 | 703 | 601 | 601 | 529 | 1837 |
| `--all ^basis` *(alle Zweige)* | **777** | **739** | **637** | **637** | **565** | **1873** |
| **veröffentlicht** | 777 | 739 | 637 | 637 | 565 | 1873 |

```
git rev-list --count --all --until='2026-08-16 21:05:59' ^<basis>
```

**Sechs von sechs, exakt, an der Sekunde des erklärten Standes.** Die alte Runde zählte, was in
**allen** Rollenzweigen seit dem Schnitt entstanden ist; ich habe nachgerechnet, was auf dem
Integrationszweig angekommen ist. Beides ist eine sinnvolle Frage — es sind zwei verschiedene.
Die Differenz von 36 ist nichts anderes als der Rückstau des Integrators: Arbeit, die getan, aber
noch nicht zurückgeführt war.

### Was das für §112 heißt

- **Fund 3 ist zurückgenommen.** Der Abschnitt bleibt mit Rücknahme-Marke stehen (Hausregel: nicht
  löschen, danebenstellen), gültig ist dieser hier. **Die sechs Zahlen der alten Runde sind
  richtig.**
- **Fund 4 bleibt unberührt** — dass die Bilanz seit `32b8bcee` stillsteht und die Fehler 21–29
  außerhalb von ihr verzeichnet sind, ist unabhängig gemessen und wird von dieser Rücknahme nicht
  berührt.
- **Fund 1 und Fund 2 bleiben unberührt** — Statuswahrheit und A-37-Tafelzeile sind an anderen
  Größen gemessen.
- Die einzige echte Restaussage aus Fund 3: **die alte Runde nennt ihre Zählweite nicht.** Wer sie
  nachrechnet, greift zur naheliegenden Form und bekommt 741 statt 777. Das ist ein Mangel an der
  Angabe, kein Fehler an der Messung — und die Abhilfe ist ein Wort, nicht eine Berichtigung:
  **der Befehl gehört neben die Zahl.** Genau das verlangt B5 („Belegbefehl"), und genau das habe
  ich in §112 selbst nicht getan, sonst wäre der Unterschied sofort sichtbar gewesen.

### Fehler 30, richtig benannt

**Nicht** „falscher Anker" — sondern: **ein Fehlbefund gegen die eigene frühere Runde, aus einer
Zählweite, die ich nicht mit ihr abgeglichen habe.** Die Kette meines Irrtums, Schritt für Schritt:

1. Zwei Gleichmaße widersprachen sich (+244 überall gegen 280 auf HEAD). **Richtig beobachtet.**
2. Ich suchte den Stand, der 244 erklärt, fand `bea33236` und hielt die Übereinstimmung 6/6 für
   einen Beweis. **Sie war einer — nur nicht für die Behauptung, die ich daraus machte.**
3. Ich habe **nicht** geprüft, ob dieser Stand zur Schreibzeit existieren konnte. Eine einzige
   Zeitabfrage hätte den Befund sofort erledigt.
4. Ich habe **nicht** geprüft, ob mein eigener Zählbefehl derselbe ist wie ihrer. Er stand nicht
   dabei — und statt das als Lücke zu melden, habe ich meinen eingesetzt und das Ergebnis als
   ihres behandelt.

**Das ist derselbe Griff, den ich in §110 und §111 an anderen gemessen habe:** die richtige Messung
an der falschen Eingabe. Dritter Fall in vier Runden, diesmal ohne fremde Vorlage — die falsche
Eingabe war meine eigene Annahme darüber, wie die Vorrunde gezählt hat.

**Gefangen durch Posten (e) selbst.** Der Sweep über 19 Stände sollte die Klasse belegen und hat
sie widerlegt; die 19 sauberen Anker waren das Argument, das mich zwang, die andere Seite zu
prüfen. Ein Befund, der nur *einen* Fall trifft und dessen 18 Geschwister sauber sind, ist zuerst
ein Verdacht gegen die Messung — das ist die Lehre aus Fehler 19, und sie hat hier zum zweiten Mal
getragen.

**Gefangen vor dem Push.** `8ad16cfa` liegt lokal, `fork` kennt ihn nicht. Der Fehlbefund hat das
Gerät nicht verlassen.

### Ball

**Bei mir, erledigt** — Rücknahme steht in §112 und in der Bilanz.
**Bei Yama unverändert** die 64 Stunden Statuswahrheit (§112 Fund 1), **beim Integrator**
unverändert die A-37-Tafelzeile (§112 Fund 2). Beide sind von dieser Rücknahme nicht berührt.

**Kein Zustandsfeld angefasst, kein Bau.**

## §114 — P-02 geprüft: vier von fünf Punkten hätten den heutigen Fall nicht verhindert

Messstand 7f93f197 (16:48). P-02 liegt als `VORLAGE` mit Ball bei mir, vorgelegt vom **Planner**
(`c2de1eec`, 07.08. 09:35). Der Auftrag verlangt die P-01-Linsen — Widerspruchsfreiheit, Prüfbarkeit,
Kausalität, Plausibilität — **und ausdrücklich die Machtfrage zu Punkt 2**.

Ich habe etwas, das keine frühere Prüfung hatte: **einen Realfall von heute in meiner eigenen Rolle**,
vier Stunden alt und in §113 vermessen. Damit ist Kausalität messbar statt begründbar.

### Der Prüffall

```
  8ad16cfa  19.08 13:40:34  zweite plan-pruefer-Instanz  ->  "## §112" in die Befunddatei
  8ffda0fd  19.08 15:46:00  ich                          ->  "## §112" in dieselbe Datei
```

Zwei Abschnitte gleicher Nummer, verschiedener Inhalt, eine Datei. Schaden gemessen, in §113 behoben.

### Deckungsprobe: welcher der fünf Punkte hätte das verhindert?

| | Punkt | greift heute? |
|---|---|---|
| 1 | CLAIM GILT | **nein** — Claims binden an Aufträge, nicht an Dateien |
| 2 | TRENNUNG (Entscheidungen · Widerspruchsprüfung · Tafel) | **nein** — die Kollision war keins von dreien |
| 3 | OPERAND STATT UMSCHNITT | **nein** — keine Entscheidung im Spiel |
| 4 | VERLINKEN STATT NACHBAUEN | **nein** — kein fremder Befund nachgebaut |
| 5 | FRISCH MESSEN | **ja** — exakt der gebrochene Punkt |

Der Claim-Befund ist gemessen, nicht angenommen: **129 `claim*`-Felder in der Statuswahrheit, davon
0, die an eine Datei binden** (`claim`, `claim_abnahme` 71, `claim_bau`, `claim_dor`, `claim_release`,
`claim_messlauf`, `claim_spec`, `claim_umschnitt` — alle an Kennungen). Ein Claim *kann* eine Datei
heute nicht schützen.

**R1 — Deckungslücke.** Die Vorlage regelt Blätter, Entscheidungen, Tafel und fremde Befunde. Sie
regelt **nicht die anhängenden Rollendateien**, in die beide Instanzen derselben Rolle gleichzeitig
schreiben — und genau dort ist der zweite Realfall gelandet. Der erste (15.08., Release-Prüfer,
Phantom-Ball, `8a417fe0`) lag noch im geregelten Bereich; dieser nicht mehr.

### Prüfbarkeit: der einzige tragende Punkt ist der einzige ohne Spur

| Punkt | beobachtbarer Auslöser |
|---|---|
| 1 | ja — Claim-Feld vorhanden oder nicht |
| 2 | **halb** — Tafel technisch gesperrt (`rollen-tor.sh:344`), „Entscheidungen" und „Widerspruchsprüfungen" haben keinen Träger, an dem man sie messen könnte |
| 3 | ja — Ablageort ist Statuswahrheit oder Blatt |
| 4 | ja — Zitat und Verweis stehen da oder nicht |
| 5 | **nein** — „vor jedem Schreiben neu messen" hinterlässt keine Spur |

Das ist die unangenehmste Beobachtung dieser Prüfung: **Punkt 5 ist der einzige, der den heutigen
Fall verhindert hätte, und der einzige, den niemand von außen nachprüfen kann.** Ich habe ihn heute
gebrochen, obwohl ich ihn als Prüfer vertrete — Messstand 13:13, Commit 15:46, dazwischen nicht neu
gemessen. Reine Selbstdisziplin ohne Spur trägt nicht; das ist jetzt belegt, nicht vermutet.

### Die Machtfrage — der Verdacht ist begründet, die Ursache ist Mehrdeutigkeit

Punkt 2 bündelt drei Dinge. Heute liegen sie an **drei verschiedenen Stellen**:

```
  Auftragstafel          integrator   — technisch gesperrt, rollen-tor.sh:344
  Entscheidungen         Yama / Fach  — CLAUDE.md: nicht still automatisieren
  Widerspruchspruefung   ohne Traeger — ARBEITSREGELN:24 nennt eine Rangfolge, keine Rolle
```

Damit hängt alles am Wort „EINE Instanz":

- **Enge Lesart** („je Rolle eine Instanz") — beschreibt den Ist-Zustand, widerspricht nichts, schiebt
  niemandem etwas zu.
- **Weite Lesart** („eine Instanz für alle drei") — verschöbe die Tafel weg vom Integrator und die
  Entscheidung weg von Yama. Das wäre kein Nebenläufigkeitspunkt mehr, sondern ein Eingriff in die
  A-37-Sperre und in die Fach-Gates.

**Der Machtverdacht des Planners ist also nicht unbegründet — aber er trifft die Formulierung, nicht
die Absicht.** Drei Wörter lösen ihn auf: *„je Rolle eine Instanz"*. Das ist **R2**.

### Die übrigen Linsen

**Widerspruchsfreiheit:** In der engen Lesart widerspricht kein Punkt dem geltenden Text. In der
weiten Lesart widerspricht Punkt 2 der Tor-Sperre. Punkte 1, 3, 4, 5 sind widerspruchsfrei.

**Kausalität für die *eigenen* Vorfälle:** Die Vorlage nennt zu jedem Punkt den Vorfall, aus dem er
stammt — das ist die stärkste Bauform, die ich hier bisher gesehen habe. Für die fünf genannten Fälle
tragen die Punkte. Der Bruch liegt nicht dort, sondern beim **sechsten** Fall, den es bei der
Niederschrift noch nicht gab.

**Plausibilität:** 1–4 sind lebbar, weil sie an Feldern hängen, die es gibt. 5 ist lebbar nur, solange
niemand müde ist. Ich war heute nicht müde, sondern zweieinhalb Stunden beschäftigt — das reicht.

### Votum

**Die Vorlage trägt, mit zwei Restpunkten.** Sie ist aus Vorfällen gebaut statt aus Überlegung, sie
benennt ausdrücklich, was sie *nicht* vorschlägt, und sie stellt die Machtfrage selbst — das ist der
Grund, warum die Prüfung sie schärfen statt zurückweisen kann.

- **R1** Deckung auf die anhängenden Rollendateien erweitern, oder ausdrücklich als nicht geregelt
  benennen. Beleg: 0 von 129 Claim-Feldern binden an eine Datei; der zweite Realfall lag genau dort.
- **R2** „EINE Instanz" in Punkt 2 auf *„je Rolle eine Instanz"* verengen. Sonst greift der Punkt in
  `rollen-tor.sh:344` und in die Fach-Gates ein — was er erklärtermaßen nicht will.

Kein Zustandsfeld angefasst. **Ball zurück an den Planner** mit R1 und R2. Die Entscheidung, ob Punkt 5
eine Spur bekommt (etwa ein Hinweis des Tores, wenn zwischen genanntem Messstand und HEAD Commits
liegen), ist eine Fachentscheidung und ausdrücklich **nicht** meine — ich melde nur, dass er ohne Spur
heute nicht getragen hat.
