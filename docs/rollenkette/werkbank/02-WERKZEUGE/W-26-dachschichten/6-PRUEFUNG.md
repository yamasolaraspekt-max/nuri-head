# W-26 · Dachschichten (Aufbau) — PRÜFUNG

## Der Wächter, der eine Abwesenheit hält — und warum das die schwierigere Sorte ist

`__tests__/dachformVorlagen.test.ts:561`

```js
test("Deckungsneutral: validateVorlage erzeugt KEINE EINDECKUNG_KATEGORIE-Warnung mehr", () => {
  for (const v of nurVerfuegbare(alle)) {
    const val = validateVorlage(v);
    assert.ok(!val.warnungen.some((w) => w.code === "EINDECKUNG_KATEGORIE"), ...);
  }
});
```

> ***Eine Zusage über etwas, das NICHT passiert, ist schwerer zu schreiben als eine über ein
> Ergebnis*** — *es gibt keinen Wert zum Vergleichen.* **Diese hier löst es richtig: sie fährt über
> ALLE verfügbaren Vorlagen und prüft eine Eigenschaft, nicht ein Beispiel.**
>
> **Sie ist die Bremse gegen einen plausiblen Rückschritt:** *jemand findet
> `eindeckungPasstZuKategorie` ungenutzt herumliegen, schließt sie „aufräumend" wieder an — und
> wird rot.* **Genau dafür steht sie da.**

## Die vier Zusagen zur Funktion, die niemand ruft

`:240-244` — `eindeckungPasstZuKategorie: Kategorie-Trennung`

```text
bitumen  gegen 'pitched'   ->  false
ziegel   gegen 'flat'      ->  false
ziegel   gegen 'pitched'   ->  true
bitumen  gegen 'flat'      ->  true
```

> **Vier Zusagen sichern eine Funktion mit null produktiven Aufrufern.** *Das ist kein
> verschwendeter Test:* **er hält die Fachaussage fest** *(welche Deckung auf welche Dachart
> gehört)*, **während der Weg dorthin abgeschaltet ist.** *Wer sie je wieder anschließt, findet
> ihre Bedeutung geprüft vor.*

## Was KEIN Wächter hält

| ungeprüft | Folge |
|---|---|
| **elf Felder des Dachdecker-Vertrags** | ob `konterlattungMm`, `battenDistCm`, `unterdeckungKlasse`, `firstausbildung` je gefüllt oder plausibel sind, hält keine Zusage fest — sie werden auch nirgends gelesen |
| **`deckungsHinweis` erreicht die Oberfläche** | ein Anzeigetext ohne Anzeige; keine Zusage bemerkt es |
| **`konterlattungMm: [0, 0]` beim Flachdach** | ein sinnloser, aber pflichtiger Wert; keine Zusage fragt nach |
| **RDN gegen `unterdeckungKlasse`** | die Warnung könnte präziser sein; dass sie es nicht ist, ist unsichtbar |

> ***Die erste Zeile ist der Befund dieses Blattes in Prüfform:*** *dreizehn Felder, zwei wirken,
> elf sind ungelesen UND ungeprüft.* **Ein Wächter über sie wäre heute grün und würde einen
> Zustand einfrieren, den niemand entschieden hat** *(F-06)* — **deshalb gibt es ihn zu Recht
> nicht, und deshalb steht der Befund stattdessen hier.**

## Wie diese Ablesung rot werden könnte — und zweimal beinahe wurde

**Erstens: der Wortstamm.** *`dachaufbau` findet 14 Treffer in 11 Dateien.* **Alle gehören zu
W-29** *(aufgesetzte Bauteile).* **Hätte ich sie gezählt, stünde hier ein halbgebautes
Schichtenwerkzeug, das es nicht gibt.**

**Zweitens: mein eigener Filter.** *Meine erste Messung meldete `validateVorlage` mit „produktiv
0".* **Der Aufrufer steht in derselben Datei** (`applyVorlage`, `:1272`), *und mein `grep -v` hatte
genau diese Datei ausgeschlossen.*

> ***Beide Male hätte ein Zwischenstand ein plausibles Blatt ergeben.*** **„Außerhalb gelesen" und
> „wird gerufen" sind zwei Fragen, und wer die eine misst und die andere meldet, schreibt eine
> Falschaussage in ordentlicher Form.**

**Alle Zahlen dieses Blattes tragen ihren Befehl** (`5-CODE`), *gefahren am 16.08. über
`resources/planner/hausplaner` mit `--include='*.ts' --include='*.tsx'`.*
