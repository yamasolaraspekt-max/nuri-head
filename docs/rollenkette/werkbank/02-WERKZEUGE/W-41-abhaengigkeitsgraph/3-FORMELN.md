# W-41 · Abhängigkeitsgraph — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Ein Abhängigkeitsgraph rechnet nicht — er verfolgt.** *Was er braucht, ist eine Struktur und eine
Regel, keine Mathematik.*

## Was hier an die Stelle einer Formel tritt

```text
Erreichbarkeit   von einer geaenderten Angabe aus alle Werte finden, die auf ihr beruhen
                 — und dann alle, die auf DIESEN beruhen (Schritt 4).
```

> **Das ist Graphdurchlauf und keine Geometrie.** *Er hat keinen Zahlenbereich, keine Einheit und
> keinen Grenzfall im mathematischen Sinn — aber er hat **einen** Grenzfall, der zählt:*

## Der eine Grenzfall, der benannt gehört: Zyklen

**Wenn A auf B beruht und B auf A, läuft ein naiver Durchlauf ewig.**

```text
Ist das im Bestand moeglich?   NICHT GEMESSEN.
                               Es gibt keine erhobene Struktur, also auch keine Aussage
                               darueber, ob sie kreisfrei waere.
```

> **Ich behaupte weder, dass es Zyklen gibt, noch dass es keine gibt.** *Beides wäre eine Aussage
> über eine Struktur, die niemand erhoben hat.* **Was das Blatt sagen kann: wer den Mechanismus
> baut, muss den Fall behandeln — und wer die Struktur erhebt, muss ihn messen.**

**Ein Hinweis aus dem Bestand, der keine Erhebung ersetzt:** *die bereits gebaute
Übergangstabelle in `geometry/configuratorPackage.ts` ist **azyklisch in der gefährlichen
Richtung** — aus `approved` und `integrated` führt nur `outdated` zurück, und das ist eine
ausdrückliche Entwurfsentscheidung („Freigabe-Schutz — keine stille Rückstufung").* **Das ist ein
Vorbild für die Bauart, kein Beleg über die Kanten.**

## Fehlt eine Formel?

**Nein.** *Und die Versuchung, eine zu erfinden, ist hier klein — die Gefahr liegt woanders: eine
**Struktur** zu erfinden.* **Siehe `7-GRENZEN.md`.**

## Genauigkeit

**Gegenstandslos** — *ein Zustandswechsel hat keine Genauigkeit.*

> **Die einzige Größe, die eine bekommen müsste, ist der Zeitpunkt** (`2-FUNKTION.md`,
> „was erhalten bleibt"). *Ob er auf die Sekunde, auf die Revision oder auf den Vorgang genau sein
> muss, sagt die Quelle nicht.*
