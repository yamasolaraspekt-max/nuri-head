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
