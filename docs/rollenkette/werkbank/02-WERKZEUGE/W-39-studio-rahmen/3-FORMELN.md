# W-39 · Studio-Rahmen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Ein Rahmen rechnet nicht.** *Und anders als bei W-34, wo wenigstens gezählt wird, ist hier auch
das Zählen fremd: W-39 **liest** vier Zahlen und reicht sie weiter.*

## Die vier Zahlen, die durchgereicht werden

```ts
:40-48   const modell = React.useMemo(() => { … }, [scene]);
           geschosse : scene?.levels.length ?? 0
           fenster   : nodes.filter(n => n.type === 'window').length
           tuer      : nodes.filter(n => n.type === 'door').length
           treppe    : nodes.filter(n => n.type === 'object' && n.objectType === 'stair').length
```

**Sie gehen als `modell` an `GuidedView`** (`:132`) — *W-39 bewertet sie nicht, es stellt sie
bereit.* **Wer wissen will, wie aus ihnen ein Schritt-Status wird, liest W-34s `statusAus`.**

> **Warum das keine Formel ist:** *vier Filter über eine Liste, jeder mit `.length`. Kein
> Zahlenbereich, keine Einheit, kein Grenzfall — die leere Liste ergibt 0, und 0 ist eine gültige
> Antwort.*

## Fehlt eine Formel?

**Nein** — *und die naheliegende Versuchung ist, den Speicherstatus für eine Regel zu halten.* **Er
ist eine:** *„Gespeichert" darf nie auf einer Fläche stehen, die nicht speichern kann.* **Aber sie
steht nicht hier:**

```ts
:57   const anzeige = speicherAnzeige(speicherStatus, kannSpeichern, konfliktRevision);
```

*Die Regel lebt in `dashboard/speicherAnzeige.ts` und wird von `speicherAnzeige.test.ts` bewacht.*
**W-39 hält davon genau eine Zeile: die Farbe je Gewichtung** (`:58`), *und der Dateikommentar
`:49-54` sagt ausdrücklich, warum — es gab einmal **zwei** Statusanzeigen, die einander
widersprachen.*

## Genauigkeit

- **Alle durchgereichten Werte sind ganzzahlige Anzahlen.** *Keine Rundung, keine Toleranz.*
- **Ein Zeitwert:** *der Toast steht **2600 ms** (`:70`). Das ist eine Festlegung, keine Rechnung.*

> **Die einzige Ungenauigkeit ist keine Zahl, sondern eine Leerstelle:** *`projekte` ist auf der
> Studio-Fläche **immer leer**, weil der Controller die Liste dorthin bewusst nicht durchreicht
> (`:37-38`).* **Wer die Startseite ohne diesen Satz liest, hält eine leere Projektliste für einen
> leeren Bestand.**
