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
