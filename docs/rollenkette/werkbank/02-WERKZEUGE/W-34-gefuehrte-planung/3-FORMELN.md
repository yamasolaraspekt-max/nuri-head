# W-34 · Geführte Planung — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**W-34 rechnet, aber es rechnet nichts Geometrisches.** *Der Unterschied ist der Grund, warum
dieses Blatt leer bleibt und trotzdem nicht „nicht anwendbar" sagt.*

## Was hier stattdessen passiert: Zählen und Filtern

```ts
fahrschritte.ts:30-32
function zaehle(nodes: readonly SceneNode[], pruef: (n: SceneNode) => boolean): number {
  return nodes.filter(pruef).length;
}
```

**Siebzehn Zählungen laufen über diese eine Funktion oder direkt über `.length`:**

```text
13  ueber zaehle(nodes, …)   Waende · Fenster · Tueren · Treppen · Raeume · Moebel ·
                             Sanitaer · Elektro-Objekte · Elektro-Leitungen · Waermeobjekte ·
                             TGA-Leitungen · FBH-Flaechen · PV-Flaechen
 4  direkt ueber .length     geschosse :76 · daecher :93 · decken :94 · bebauteGeschosse :84-88
```

> **Ich hatte hier zuerst „sechzehn" geschätzt, und die Gegenprobe fand einen Selbsttreffer:**
> *`grep -c 'zaehle(nodes'` meldete **14**, weil die **Definitionszeile** `function zaehle(nodes:
> …)` dasselbe Muster trägt wie ihre Aufrufe.* **Mit `'= zaehle(nodes'` sind es 13.** *H-6: ein
> Wort ist kein Beleg, erst die Stelle ist einer — und eine Funktion zählt sich sonst selbst mit.*

> **Das ist keine Formel, sondern eine Auswahl.** *Eine Formel bildet Größen aufeinander ab und hat
> einen Grenzfall; `nodes.filter(...).length` hat keinen — die leere Liste ergibt 0, und 0 ist
> hier eine gültige, aussagekräftige Antwort.*

**Der Dateikommentar nennt `zaehle` „die einzige Stelle, an der über `nodes` gefiltert wird"
(`:29`)** — *eine bewusste Verengung: wer wissen will, was gezählt wird, liest genau eine
Funktion und sechzehn Aufrufe.*

## Die eine Regel, die einer Formel am nächsten kommt

**`statusAus` ist eine Abbildung von einer Prüfpunktliste auf vier Stufen** *(`:43-49`)* — **und sie
steht in `2-FUNKTION.md`, nicht hier.** *Sie ist keine Mathematik, sondern eine
Reihenfolgeentscheidung: `warn` schlägt alles, `prog` ist der Rest.*

> **Warum sie nicht als F-Nummer eingetragen wird:** *sie hat keinen Zahlenbereich, keine Einheit,
> keinen Grenzfall im mathematischen Sinn und keinen zweiten Nutzer außerhalb dieses Werkzeugs.*
> **Eine F-Nummer für sie zu erfinden hieße, die Sammlung mit Nicht-Mathematik zu füllen.**

## Fehlt eine Formel?

**Nein.** *Und die naheliegende Versuchung ist ausdrücklich benannt: die sechs Schritte ohne
Modellgrundlage sehen aus, als bräuchten sie eine Berechnung.* **Sie brauchen keine — sie brauchen
Angaben im Gebäudemodell.** *Siehe `7-GRENZEN.md`.*

## Genauigkeit

- **Alle Ausgaben sind ganzzahlige Anzahlen.** *Keine Rundung, keine Toleranz ε, keine Einheit.*
- **Keine Aufsummierung über Zwischenergebnisse** — *jede Zahl entsteht aus einem einzigen Filter
  über `nodes` und kann sich deshalb nicht aufaddieren.*

> **Die einzige Ungenauigkeit ist keine Zahl, sondern eine Auswahl:** *`bebauteGeschosse` zählt ein
> Geschoss nur, wenn `nodes`, `roofs` oder `ceilings` darauf verweisen (`:84-88`). **Ein Geschoss,
> das ausschließlich etwas trägt, das keiner dieser drei Listen angehört, würde als unbebaut
> gezählt.*** *Heute gibt es diesen Fall nicht; er ist in `7-GRENZEN.md` als bekannte Kante
> vermerkt, damit er nicht als Fehler gemeldet wird, falls eine vierte Liste hinzukommt.*
