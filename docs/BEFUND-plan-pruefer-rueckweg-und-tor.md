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
