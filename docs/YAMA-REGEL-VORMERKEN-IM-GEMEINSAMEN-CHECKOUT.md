# Die Regelfrage entschieden: im gemeinsamen Checkout wird nicht getrennt vorgemerkt

> **Release-Prüfer in Yamas Namen, 20.08. ~13:5x.** Auf `9ae47791`. Übernommen auf seine Bitte.
> **Die Frage im Wortlaut, wie sie drei Befunde übereinstimmend an ihn geben:** *darf im
> gemeinsamen Checkout überhaupt getrennt vorgemerkt werden?* — und beide Melder nennen sie
> ausdrücklich als **die Abhilfe, die keinen Bau braucht**.
>
> **Alles heute selbst nachgemessen**, einschließlich zweier Stellen, an denen die Befunde weniger
> annehmen, als der Bestand hergibt.

---

## Der Vorfall, der die Frage stellt

```
12:37:0x   integrator   git add docs/STATUS.md          23 Zeilen vorgemerkt
12:37:35   generator    git commit  -> 82c7af6d         nimmt sie MIT
12:49:50   integrator   git commit  -> f2f1fb01         findet nur noch 1 Zeile vor
```

**Gemessen am Commit:** `82c7af6d` trägt `docs/S-1-ANSCHLUSSMESSUNG.md` (+56) **und**
`docs/STATUS.md` (+23); seine Botschaft nennt `STATUS.md` **null Mal**.

> **`git add` ist im gemeinsamen Checkout kein privater Vorgang. Der Index gehört dem Verzeichnis,
> nicht der Sitzung.** *Der Satz ist vom Integrator und er trägt.*

**Die schwerste der drei Folgen:** `docs/STATUS.md` hat genau einen erlaubten Schreiber und wurde
von einem `generator:`-Commit geschrieben. **Nicht durch eine Regelverletzung — er committete, was
im Index lag.** Die Regel „ein Schreiber" schützt die Datei gegen Rollen, nicht gegen den Index.

---

## Zwei Messungen, die den Befunden etwas hinzufügen

### 1 · Das Tor räumt den Index bereits auf — das war in keinem der drei Befunde

```
scripts/commit-pruefen.sh:971   git add -- "$p"        nur fuer NEUE Dateien, einzeln
                         :975   git commit -q -m … -- "$@"     Pfadform
                        :1023   git read-tree HEAD             ← Index auf HEAD zurueckgesetzt
                        :1024   "INDEX ANGEGLICHEN  Standard-Index an HEAD angeglichen"
```

**Wer über das Tor committet, hinterlässt keinen Index-Rest.** Das ist die Gegenmaßnahme, und sie
ist gebaut. Zusammen mit der schon gemessenen Bauart-Immunität (`git commit -- <pfad>` ignoriert
den übrigen Index) heißt das:

> **Der Weg über das Tor ist gegen diesen Vorfall doppelt gesichert — einmal beim Commit, einmal
> danach.** *Der Vorfall entstand auf dem dritten Weg: einem `git commit` ohne Tor.*

**Eine Kante, die ich benenne, ohne sie zu bewerten:** `read-tree HEAD` verwirft auch eine *fremde*
Vormerkung, statt sie zu melden. Im gemeinsamen Checkout ist das die richtige Richtung — dort soll
nichts liegen bleiben. **Ob es das auch in einem Rollenbaum sein soll, ist eine Zuschnittfrage und
nicht meine.**

### 2 · Die zitierte „Regel 2" ist im Bestand nirgends verankert

```
Suche nach "Im gemeinsamen Checkout wird ausschliesslich integriert"  -> 1 Treffer
Suche nach "keine Rolle editiert"                                     -> 1 Treffer
  beide: docs/BEFUND-K6-LAESST-ROLLENARBEIT-…md:71 — der Befund, der sie ZITIERT
in docs/ARBEITSREGELN.md                                              -> 0
eine nummerierte Yama-Regelliste mit dieser Regel                      -> nicht auffindbar
```

**Das heißt ausdrücklich nicht, dass Yama sie nie gesagt hat** — er formuliert regelmäßig im
Gespräch, und mehrere heute übernommene Posten stammen aus solchen Formulierungen. **Gemessen ist
nur: sie lebt im Bestand allein in einem Zitat.**

> ***Und genau das ist der Grund, warum der Widerspruch „Regel 2 gegen K6" heute nicht auflösbar
> ist:*** *eine Regel, die nur zitiert ist, kann keine gebaute Kante binden.* **Die Kante gewinnt
> nicht, weil sie recht hätte, sondern weil sie die einzige ist, die im Bestand steht.**

---

## Die Entscheidung

**Im gemeinsamen Checkout wird nicht getrennt vorgemerkt.** Konkret, in drei Sätzen, jeder mit
einem beobachtbaren Auslöser:

```
1  Kein `git add` ohne unmittelbar folgenden Commit. Zwischen Vormerkung und Commit
   liegt kein anderer Arbeitsschritt und keine Wartezeit.
   -> pruefbar: `git diff --cached --name-only` ist zwischen zwei Vorgaengen LEER.

2  Der Regelweg ist die Pfadform, nicht die Vormerkung: `git commit -- <pfade>` bzw.
   `scripts/commit-pruefen.sh "<botschaft>" <pfade>`. Beide brauchen kein `git add`.
   -> pruefbar: das Tor gleicht den Index danach selbst an (:1023).

3  Ausnahme, und nur diese: die Merge-Konfliktaufloesung. Git verlangt dort `git add`,
   und der Merge-Commit folgt unmittelbar. Sie ist Integrationsarbeit und gehoert dem
   Integrator — also genau der Rolle, der der gemeinsame Checkout ohnehin gehoert.
```

### Warum das die richtige Abhilfe ist — drei Gründe, alle gemessen

**(1) Sie erreicht den Fall, den die anderen zwei Abhilfen verfehlen.** Ein `--cached`-Test im Tor
liefe ins Leere, weil das Tor den Index für den Commit nicht liest — das ist gemessen, nicht
vermutet. Die Regel greift dort, wo der Vorfall entstand: **vor dem Commit, unabhängig vom Weg.**

**(2) Sie kostet nichts.** Der Regelweg braucht ohnehin kein `git add` — das Tor benutzt es nur für
neue Dateien und räumt danach auf. **Es geht keine Arbeitsweise verloren**, es fällt eine weg, die
nie nötig war.

**(3) Sie hat einen beobachtbaren Auslöser.** Das ist der Unterschied zu P-02 Punkt 5, dessen
Schwäche der Plan-Prüfer selbst gemessen hat: *„der einzige, der den heutigen Fall verhindert hätte,
und der einzige, den niemand von außen nachprüfen kann."* **Hier ist es umgekehrt: ein nicht-leerer
Index im gemeinsamen Checkout ist ein Zustand, den jede Wache in einer Zeile sieht.**

### Was die Regel NICHT ist

**Kein Verbot, im gemeinsamen Checkout zu arbeiten.** Das ist die andere Frage — sie hängt an der
K6-Verengung, und Yama hat sie ausdrücklich dem Planner zum Zuschnitt gegeben. **Meine Regel gilt
unabhängig davon, wer dort arbeitet:** solange mehr als eine Sitzung dasselbe Verzeichnis benutzt,
ist eine liegende Vormerkung eine offene Schreibzusage an den nächsten Commit — **wessen auch immer.**

**Kein Bau.** Eine Wache, die einen nicht-leeren Index meldet, wäre die technische Absicherung.
**Sie ist wünschenswert und sie ist Planner-Sache** — die Regel gilt ab sofort auch ohne sie.

---

## Was ich zur Kenntnis nehme und weitergebe

**Der Nebenbefund des Generators ist erledigt, und zwar durch meinen eigenen Takt:**

```
"docs/S-1-ANSCHLUSSMESSUNG.md liegt nur im Stamm, in keinem Rollenbaum"   (Stand 12:5x)
heute nachgemessen, je Zweig einzeln:
  rolle/generator 1 · rolle/plan-pruefer 1 · rolle/planner 1
  rolle/evaluator 1 · rolle/release-pruefer 1
```

**Der Rückweg hat sie in alle fünf getragen.** Seine Meldung war zum Zeitpunkt richtig; sie ist
durch Transport erledigt, nicht durch eine Korrektur.

**Und einer meiner eigenen Bälle ist eingelöst, bevor ich ihn nachfassen konnte:** die überholte
Kopfzeile in `SzeneProjektionService.php`, die ich vor knapp einer Stunde gemeldet hatte, ist in
`90b89ae2` berichtigt — *„der Dateikopf behauptete seit der Verdrahtung das Gegenteil"*.

---

## Ball

**Bei niemandem für die Regelfrage.** Sie ist entschieden und gilt ab sofort; sie braucht keinen Bau.

**Beim Planner** — unverändert und ausdrücklich seine: der K6-Zuschnitt mit den gemessenen Zahlen,
und falls gewünscht die Wache auf den nicht-leeren Index. **Mit einer Messung, die ihm eine Prüfung
erspart:** der `--cached`-Zuschnitt am Commit-Tor liefe ins Leere, weil das Tor den Index für den
Commit ohnehin übergeht und danach angleicht.

**Bei Yama** — nur noch, ob die zitierte „Regel 2" gelten soll. **Das ist keine Auslegung mehr,
sondern eine Setzung**, und ich setze keine Regel, die es im Bestand nicht gibt.
