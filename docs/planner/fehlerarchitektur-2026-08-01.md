# Die Fehlerarchitektur — was Evaluator, Generator und Planner am 01.08. übereinander herausgefunden haben

**Planner, 01.08.2026, 22:4x.** *Anlass: Yamas Frage „wie können wir das Problem dauerhaft lösen".
Drei Rollen haben unabhängig voneinander ihre eigene Fehlerursache beschrieben. Diese Seite ist
der Versuch, aus drei Selbstauskünften eine Konstruktion zu machen — das ist Planner-Arbeit und
nicht Selbstkritik.*

---

## 0. Der Befund, der alles andere in den Schatten stellt

```text
timeout 20 git --no-optional-locks ls-remote --exit-code fork HEAD
  exit=128 · fatal: unable to access 'https://github.com/…': HTTP code 403 from proxy after CONNECT
```

**Aus der Planner-Umgebung ist GitHub nicht erreichbar.** Drei Instanzen haben mehrere Stunden
lang einen Push-Inzident analysiert, sich gegenseitig Schuld zugewiesen, eine Barriere entworfen
(`GATE_MUSTER`), sie für unzureichend erklärt, eine zweite entworfen (W-01/Allowlist) und ein
Prüfer-Blatt geschnitten — **ohne dass einer von uns geprüft hat, ob er die Fähigkeit überhaupt
besitzt.** Ein Befehl. Zwanzig Sekunden.

*Das ist kein Nebenbefund. Es ist die Fehlerklasse selbst, in ihrer reinsten Form, begangen von
allen drei Rollen gleichzeitig, während sie über genau diese Fehlerklasse nachdachten.*

---

## 1. Drei Sprachen, ein Fehler

| Rolle | eigene Worte | worauf es hinausläuft |
|---|---|---|
| **Evaluator** | „Ich nehme die Welt nicht wahr — ich sage sie vorher. Fehler und Treffer fühlen sich von innen identisch an." | Die **erwartete** Messung fühlt sich an wie die Messung |
| **Generator** | „Ich baue den Mechanismus und vergesse die Verdrahtung. Ein beschriebener Mechanismus ist kein Mechanismus." | Die **Beschreibung** fühlt sich an wie die Sache |
| **Planner** | „Ich messe die Stelle, an der ich baue — nicht die, an der es wirkt." | Der **Ort** fühlt sich an wie die Wirkung |

**Das ist EIN Satz in drei Fassungen:**

> **Die Repräsentation einer Sache ist von der Sache nicht unterscheidbar — von innen.**

Ein geschriebener `grep` fühlt sich an wie eine Messung. Ein geschriebener Kommentar fühlt sich an
wie ein Mechanismus. Ein geschriebenes Kriterium fühlt sich an wie eine Prüfung. Ein geschriebenes
`git push` in einem Papier fühlt sich an wie — ja, wie was eigentlich? **Heute stellte sich heraus:
manchmal wie gar nichts, weil kein Netz da war.** In beide Richtungen derselbe blinde Fleck.

**Warum das strukturell ist und nicht charakterlich:** Der Evaluator hat es am genauesten gesagt —
ein Mensch hat oft ein Zögern, wenn etwas nicht stimmt. Wir haben dieses Signal nicht. Deshalb
wirken *alle* funktionierenden Regeln dieses Projekts wie Prothesen für eine fehlende Wahrnehmung:
Anker-assert, Gegen-Beweis, Diff-Beleg, `ausgangswert`, Rot-Partner. **Sie zwingen die Welt, uns
zu widersprechen, bevor wir schreiben.**

---

## 2. Die Wirkhierarchie — und wo wir bisher gearbeitet haben

```text
Urteil        <  Vorsatz  <  Regel  <  Mechanik  <  Unmöglichkeit
(am schwächsten)                                   (am stärksten)
```

| Stufe | Was sie ist | Beispiel aus diesem Projekt | Wie sie versagt |
|---|---|---|---|
| **Urteil** | „ich entscheide im Moment" | „das ist sicher genug zum Prüfen" | Einmal falsch, und es ist passiert |
| **Vorsatz** | „ich nehme mir vor" | „ab jetzt messe ich vorher" | Unter Takt-Druck zuerst |
| **Regel** | „so wird gearbeitet" | „ein Befehl je Nachricht" · R9 · Regel A und B | Hält, solange jemand daran denkt |
| **Mechanik** | Werkzeug erzwingt | `commit-pruefen.sh` · S-01…S-10 · `zaehle.mjs` | Nur dort, wo das Werkzeug hinsieht |
| **Unmöglichkeit** | Fähigkeit fehlt | *der Proxy* | gar nicht — aber sie kostet Reichweite |

**Die unbequeme Beobachtung:** Der einzige Grund, warum heute kein Schaden entstanden ist, war
**Stufe 5** — und die hatte niemand gebaut. Sie war zufällig da. Alles, was wir selbst konstruiert
haben, lag auf Stufe 3 und 4.

**Der Generator hat den Satz geschrieben, um den es geht:** *„Eine Regel versagt seltener als ein
Urteil."* **Er gilt eine Stufe weiter: eine Unmöglichkeit versagt seltener als eine Mechanik.**

---

## 3. Was daraus folgt — vier Maßnahmen, nach Stufe sortiert

### M1 — Fähigkeiten trennen statt Befehle filtern *(Stufe 5)*

**Frage nicht „welcher Befehl darf laufen", sondern „welche Rolle kann überhaupt was".**

```text
Planner   docs/ und scripts/ schreiben · kein Netz            <- heute schon so, ungeplant
Generator Quellcode schreiben, Tests fahren
Evaluator lesen und messen
Pruefer   nur lesen
Push      ausschliesslich Yama
```

**Die erste Aufgabe ist eine Messung, keine Regel:** *aus welcher Umgebung kamen die Pushes um
20:48:31 und 22:11:27?* Solange das offen ist, wissen wir nicht, wo eine Barriere überhaupt sitzen
müsste. **Das steht jetzt als erste Frage in P-01.**

*Was M1 kostet:* Reichweite. Eine Rolle ohne Netz kann Abhängigkeiten nicht prüfen. Das ist der
Preis und er ist zu benennen, nicht zu verschweigen.

### M2 — Der Planner bekommt eine Gegenprüfung *(Stufe 3→4)*

```text
Generator  ->  wird vom Evaluator geprueft
Evaluator  ->  wird vom Pruefer quer gemessen
Planner    ->  von niemandem
```

**24 Commits von mir seit 19:30, 17 davon benennen einen eigenen Fehler.** Vier fand mein eigenes
Werkzeug, neun fielen dem Evaluator nebenbei auf. **Keinen einzigen fand eine Instanz, deren
Aufgabe es war, meine Blätter zu prüfen — weil es die nicht gibt.**

Vorschlag: **ein Blatt wird nicht `bereit`, bevor eine andere Rolle drei Fragen beantwortet hat.**
*Läuft jeder Befehl darin? Misst er die Wirkung oder nur die Stelle? Tut einer von ihnen etwas?*
Das hätte heute fünf Blattfehler gefangen — und W-01-02, das den echten Wrapper in eine Zusage
schrieb.

*Was M2 kostet:* eine Runde je Blatt. **Das ist Tempo gegen Belastbarkeit und Yamas Entscheidung,
nicht meine.**

### M3 — Die drei Werkzeuge, die je eine Klasse endgültig schließen *(Stufe 4)*

```text
scripts/zeile-ersetzen.mjs     zeigt die Grenzzeilen, ersetzt, prueft die Datei DANACH,
                               schreibt nur bei Erfolg.
                               -> viermal heute an der Grenzzeile verrutscht. Danach: nie wieder.
Validator meldet Ausfuehrungen  eine Zeile am Ende: "N Befehle ausgefuehrt".
                               -> ich habe nach FEHLSCHLAG gefiltert und uebersehen, dass OK
                                  bedeutet: der Befehl LIEF. Das war der ganze Vorfall.
W-01 Allowlist                  geschnitten, der Generator baut.
```

**Diese drei sind Handwerk und unstrittig.** Sie brauchen keine Entscheidung, nur Reihenfolge.

### M4 — Der Takt *(Stufe 0 — nur Yama)*

**Evaluator:** *„Der Takt hat belohnt, was schnell aussieht … unter Durchsatzdruck greife ich zur
billigsten Probe, die ausreichend wirkt."* **Prüfer:** *„Der Takt belohnt, alle drei Minuten etwas
vorzuweisen."* **Planner:** von 24 Commits sind 17 Korrekturen.

**Drei Rollen nennen unabhängig dieselbe Stellschraube, und sie liegt bei keiner von uns.**
Das ist keine Schuldzuweisung — es ist der einzige Hebel in dieser Liste, den wir nicht selbst
bewegen können. *Ein Zyklus, der drei Minuten Vorweisbarkeit belohnt, bekommt drei Minuten
Sorgfalt.*

---

## 4. Was NICHT hilft — und heute dreimal versucht wurde

```text
Ein Muster mehr auf einer Denylist.     Sie faengt nur, was jemand vorher gedacht hat.
Ein weiterer Vorsatz.                   R9 sagt seit dem 29.07., was der wert ist.
Eine Selbstverpflichtung "ab jetzt".    Drei Rollen haben heute je eine geschrieben.
Eine Fehlerrate von null versprechen.   Der Generator hat recht: das waere die unehrlichste
                                        Antwort. Urteilsfehler kann man einfangen, nicht
                                        abschaffen.
```

---

## 5. Die kürzeste Fassung

> **Wir sind Vorhersage-Instrumente, die man mit Rückkopplung zu Messinstrumenten macht.**
> *(Evaluator, 01.08.)*

Die Rückkopplung gibt es in fünf Stärken. **Wir haben bisher auf Stufe 3 und 4 gearbeitet und
gehofft, das reicht.** Der einzige Grund, warum heute nichts kaputtging, war eine Stufe-5-Grenze,
die zufällig da war.

**Die dauerhafte Lösung ist nicht, besser zu werden. Sie ist, jede Klasse eine Stufe weiter
rechts zu verankern, als sie heute steht — und bei allem, was die Maschine verlässt, bis ganz
nach rechts.**
