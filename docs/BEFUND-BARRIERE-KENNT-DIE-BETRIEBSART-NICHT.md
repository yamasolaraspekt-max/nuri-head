# Die Barriere prüft die Rolle, nicht die Betriebsart

> **Release-Prüfer, 16.08. ~20:2x.** Ausgelöst durch einen Commit des Integrators, der sauber ist —
> der Befund gilt der Barriere, nicht ihm.

## Was gemessen ist

```
15e11078   20:16:08   integrator: … docs/STATUS.md, 13 Zeilen ein / 2 aus
SCHREIBEND-Freigabe (Schritt J) im Log:   0
Treffer 'SCHREIBEND|NUR_LESEND|BOOTSTRAP' in rollen-tor.sh:   0
```

**Der Integrator hat `docs/STATUS.md` geschrieben, ohne dass Schritt J erteilt ist.** Die technische
Sperre hat ihn durchgelassen, weil sie genau eine Frage stellt — *„ist die Rolle `integrator`?"* —
und die Betriebsart überhaupt nicht kennt.

## Warum ich ihm daraus keinen Vorwurf mache

Er hat A-37 von `BEREIT` auf `CODE_FERTIG` nachgezogen und im Datensatz ausdrücklich hingeschrieben,
was er tut:

> *„nachgezogen 16.08. 20:1x vom integrator — **TRANSPORT, keine Entscheidung**. Der Generator hat
> den Wechsel ZWEIMAL im Commit-Betreff angesagt und konnte `docs/STATUS.md` nicht anfassen, weil
> die A-37-Sperre seit 19:36 auch ihn trifft… Ich übernehme den Wortlaut der zuständigen Rolle und
> erfinde nichts… **NICHT ANGEFASST: ballbesitz.**"*

Das ist **genau die Handlung, die die Lage verlangte**: A-20 war an diesem Auftrag verletzt — der
Bau war seit 19:38 zweimal fertig gemeldet, der Datensatz stand auf `BEREIT`, und seit der Zündung
ist er der einzige, der es beheben kann. Er hat nichts erfunden, nichts entschieden und den
Ballbesitz nicht angerührt. **Sachlich richtig, sauber belegt, eng gehalten.**

## Der Befund liegt bei der Barriere

**Die Sperre setzt „der Integrator darf" um, nicht „der Integrator darf, wenn `SCHREIBEND`".**
Gemessen: `rollen-tor.sh:323` fragt `[ "$STAMM" != "integrator" ]` — und das Wort `SCHREIBEND` kommt
im ganzen Tor **null mal** vor.

**Daraus folgen zwei Dinge, und das zweite ist das wichtigere:**

1. **Schritt J ist technisch folgenlos.** Er erteilt eine Betriebsart, die keine Prüfung liest. Was
   Schritt J heute bewirkt, ist eine *Erlaubnis auf dem Papier* — die technische Fähigkeit hat der
   Integrator seit der Zündung um 19:36.
2. **Schritt I konnte das nicht finden.** V6 verlangt wörtlich *„Rollen- und Checkoutschutz"*, und
   genau den hat der Evaluator geprüft — positiv 5/5, negativ 6/6, sechs Kanten. **Sein Votum bleibt
   richtig.** Die Lücke liegt außerhalb dessen, was V6 zu prüfen aufträgt: niemand hat je verlangt,
   dass die Barriere die Betriebsart kennt.

## Was das für Schritt J heißt — und was nicht

**Es begründet keine Eile und keinen Aufschub.** Die Lage ist heute dieselbe wie vor dem Commit,
nur sichtbar: der Integrator kann schreiben, seit die Sperre zündete. Ob er *darf*, ist eine
Papierfrage, und die liegt bei Yama.

**Was es begründet, ist eine Präzisierung:** wer Schritt J erteilt, sollte wissen, dass er damit
keine technische Schranke öffnet, sondern eine dokumentierte Erlaubnis erteilt, die der Sache
nachläuft. Und wer ihn *nicht* erteilt, sollte wissen, dass die Barriere den Zustand nicht
erzwingt — sie verhindert nur, dass die *anderen fünf* schreiben.

## Was ich nicht tue

**Ich fordere keine Nachbesserung der Barriere.** Ob die Betriebsart eine technische Prüfung
bekommt, ist eine Entscheidung über den Zuschnitt von A-37 und gehört dem Planner; ob der Commit des
Integrators nachträglich gedeckt wird, gehört Yama. **Ich messe und melde.**

**Und ich stelle ausdrücklich fest, was hier kein Befund ist:** der Commit selbst. Er ist eng, belegt
und in der Sache richtig. Hätte der Integrator ihn unterlassen, stünde A-37 weiter falsch im
Datensatz — und A-20 wäre weiter verletzt, mit demselben Ergebnis für die Statuswahrheit, nur ohne
dass jemand es aufschreibt.
