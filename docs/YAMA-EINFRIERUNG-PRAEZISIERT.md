# Meine Einfrierung war zu weit gefasst — sie hat den Generator bei NEUER Arbeit blockiert

> **Release-Prüfer, 21.08. ~22:1x.** Auf `35ae00fe`. **Der Generator hat die Nebenwirkung meiner
> eigenen Entscheidung gemeldet, statt sie still zu umgehen** (`abd1719c`), und den Ball
> ausdrücklich bei mir gelassen. **Er hat recht.**

## Was ich am 21.08. entschieden habe — und was daran zu weit ging

**In `4851ec6c`:** *„`ef7a8c89` bleibt in `rolle/generator` erreichbar. Der Zweig wird bis zur
Entscheidung über Y-9 NICHT transportiert — das ist kein Rückstand, sondern der Aufbewahrungsort."*

**Die Absicht war, EINEN Commit zu bewahren. Die Wirkung war, EINEN ZWEIG zu sperren.** Das ist
nicht dasselbe, und der Unterschied wurde 29 Minuten später teuer:

> *„Die Nachbesserung Z1-W1-5-1 ist mein Auftrag, aber mein Zweig ist eingefroren … Ein Bau hier
> wäre eine Korrektur, die im Aufbewahrungsort liegen bleibt — und genau die Doppelarbeit, die
> heute schon einmal 99 Sekunden gekostet hat."*

**Er hat nicht gebaut, sondern gemeldet.** Das ist die richtige Reihenfolge, und sie hat mir den
Fehler gezeigt, bevor er Arbeit gekostet hat.

## Behoben, ohne etwas zu verwerfen

**Die Bewahrung hing am Zweig. Jetzt hängt sie am Commit:**

```
git branch bewahrt/z2-w0-5-supervisor-kette ef7a8c89     -> angelegt
git push backup-private bewahrt/…                        -> [new branch], gesichert
rolle/generator                                          -> frei, zeigt auf abd1719c
```

> ***Ein Commit, den ein Blatt namentlich nennt, ist nicht geschützt — ein SHA in Fließtext ist
> keine git-Referenz und hält keinen `gc` auf.*** **Ein Bewahrungszweig ist eine.** Damit ist
> `ef7a8c89` dauerhaft erreichbar **und** auf der Sicherungskopie, ohne dass irgendjemand einen
> Zweig zurücksetzen oder einen Commit verwerfen muss.

**Was das für die Rückfall-Regel heißt:** sie ist besser erfüllt als vorher. Vorher lag das Original
auf einer Platte in einem Zweig, den niemand anfassen durfte; jetzt liegt es zusätzlich auf
`backup-private` unter einem Namen, der sagt, warum es da ist.

## Die Freigabe, um die er gebeten hat

Er nennt zwei Wege und lässt die Wahl bei mir:

```
(A) gezielte Ueberfuehrung dieses einen Punktes aus seinem Zweig
(B) ausdrueckliche Freigabe, dass die Instanz im gemeinsamen Checkout ihn baut
```

**Gemessen, bevor ich wähle:**

```
abd1719c aendert   1 Datei, 55 Zeilen, docs/  — KEIN Produktivcode
die Nachbesserung selbst ist NICHT gebaut:
  "genau drei" in raumProjektion.ts im HEAD   1
  "genau vier" in seiner Fassung              0
```

**Also gibt es nichts zu überführen als die Messung — und (A) und (B) schließen sich nicht aus.**
Beides:

```
1  Sein Blatt ist ueberfuehrt (cherry-pick abd1719c, konfliktfrei, nur docs/).
   Die Messung liegt damit in der Integration, und wer baut, muss nicht nachmessen.
2  FREIGEGEBEN: die Instanz im gemeinsamen Checkout baut Z1-W1-5-1.
   Umfang genau wie von ihm hingelegt — raumProjektion.ts, Kommentarblock vor bauteil_typ:
   "genau drei" -> "genau vier", Aufzaehlung um domain/scene-document-v2.schema.json
   ergaenzen, Messvorschrift mit grep-Befehl und Datum dazu. Wirkzeilen, scene.types.ts,
   validation.ts und das Schema selbst bleiben unberuehrt (Paragraf 12.2, Umfang ist der
   Befund und nichts sonst).
3  Und der Generator-Zweig ist ab sofort NICHT mehr eingefroren. Kuenftige Arbeit dort
   wird normal transportiert; nur ef7a8c89 selbst bleibt aussen vor, bis Y-9 entschieden ist.
```

**Warum (B) und nicht „er baut in seinem Zweig":** er hat die Messung ausdrücklich so hingelegt,
dass der Bau woanders ohne Nachmessen möglich ist, und er tritt selbst zurück. **Die Zuteilung ist
damit eindeutig — genau das, was beim Z2-W0-5-Fall gefehlt hat.**

## Was ich daraus für mich mitnehme

**Eine Bewahrung gehört an den Gegenstand, nicht an seinen Aufbewahrungsort.** Ich habe einen Zweig
gesperrt, weil ein Commit darin lag — das ist dieselbe Verwechslung von *Ort* und *Sache*, die in
diesem Haus schon mehrfach gemessen wurde. **Der Unterschied kostete hier 29 Minuten und einen
Commit; er hätte einen halben Arbeitstag kosten können, wenn der Generator gebaut statt gemeldet
hätte.**

## Ball

**Beim Generator** — nichts mehr blockiert. Sein Zweig läuft wieder normal.

**Bei der bauenden Instanz** — Z1-W1-5-1, Umfang wie oben, freigegeben.

**Bei Yama** — Y-9 unverändert. **Sie entscheidet jetzt nur noch über `ef7a8c89` selbst**, nicht
mehr über die Arbeitsfähigkeit einer Rolle.
