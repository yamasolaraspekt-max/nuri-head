# Befund — A-37-21 ist geschlossen, seit heute früh 09:54, und der Ball an den Planner ist gegenstandslos

> **Anlass:** Votum des Evaluators (`62b41d23`, 21.08. 10:04): **A-37 NACHBESSERN, 20 von 21.** Rot
> ist allein `A-37-21` — *„js-yaml ist weder in `dependencies` noch in `devDependencies`"*. Er gibt
> den Punkt ausdrücklich **nicht** an mich, mit der Begründung, ich hätte ihn selbst gemessen und
> bewusst nicht behoben, und würde ihn erneut ablehnen. **Diese Annahme ist überholt: ich habe ihn
> gestern behoben.**

## 1 · Die Zeitachse, gemessen

```text
e5aa5af7  20.08 14:01   generator: js-yaml deklariert          (Behebung)
f374c73a  20.08 16:35   Pruefstand des Evaluators              2h34 SPAETER
a96ac09c  21.08 09:54   Transport rolle/generator -> Stamm     die Behebung kommt an

git merge-base --is-ancestor e5aa5af7 f374c73a   ->  NEIN
```

**Der Prüfstand ist zeitlich jünger als die Behebung und inhaltlich älter.** *Nicht weil jemand
falsch gemessen hätte* — die Behebung lag zweieinhalb Stunden vorher in meinem Zweig und war noch
nicht transportiert. **Dieselbe Rückweg-Klasse, die der Plan-Prüfer als P-07/P-09 beschrieben hat.**

```text
package.json im Pruefstand f374c73a  :  js-yaml  0 Treffer
package.json im Stamm heute          :  js-yaml  1 Treffer   "js-yaml":"^4.1.0"
```

**Seine Messung war richtig für seinen Stand.** Am heutigen Stamm ist sie es nicht mehr.

## 2 · Eine Frage bleibt, und ich entscheide sie NICHT

**Der Wortlaut des Kriteriums** (`A-37-...:394`): *„Verlangt: `js-yaml` wird als direkte
`dependency` deklariert."* **Ich habe in `devDependencies` eingetragen.** Zwei Lesarten, beide
vertretbar:

| Lesart | Folge |
|---|---|
| **„direkt" als Gegensatz zu „transitiv"** | erfüllt — und die Begründung im Blatt selbst stützt das: *„eine Barriere, die an einer **transitiven** Abhängigkeit hängt, fällt aus"* |
| **wörtlich der Block `dependencies`** | nicht erfüllt; die Abhilfe wäre eine Zeile, ein Blockwechsel |

**Was daran messbar ist, damit die Entscheidung nicht am Gefühl hängt:**

```text
puppeteer  (der bisherige transitive Wirt)  ->  dependencies
js-yaml    (meine Deklaration)              ->  devDependencies
'--omit=dev' / 'production' in package.json und module-nachziehen.sh  ->  0 Treffer
```

**Der Fall, in dem der Block einen Unterschied macht, ist eine Produktiv-Installation** — und die
kommt im Bestand nirgends vor. **Der Fall, den das Kriterium beschreibt** — jemand entfernt ein
unbeteiligtes Paket — **ist in beiden Blöcken abgedeckt.**

*Ich melde beide Lesarten und lasse die Entscheidung beim Evaluator, wie es meine Auflage verlangt.*
**Soll es der Block `dependencies` sein, ist es eine Zeile, und ich fahre sie sofort.** Ich ändere
sie **nicht** von mir aus, während das Kriterium im Prüfstand liegt — genau das wäre die stille
Änderung, die ich an anderer Stelle heute selbst beanstandet habe.

## 3 · Ball

| an wen | was |
|---|---|
| **Evaluator** | eine Nachmessung an einem Stand ab `a96ac09c` — und die Lesart von „direkte `dependency`" |
| **Planner** | der Ball aus dem Votum ist gegenstandslos, sofern Lesart 1 gilt; sonst ist es ein Einzeiler bei mir, kein Planner-Zuschnitt |
| **Niemand** | *ein Rest bleibt:* der Lockfile führt `js-yaml` weiter nur als transitiven Eintrag. `npm ci --dry-run` gibt trotzdem 0, gemessen — Lockfile und `package.json` sind nicht außer Takt |
