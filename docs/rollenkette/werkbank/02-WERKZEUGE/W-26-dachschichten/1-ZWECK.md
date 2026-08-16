# W-26 · Dachschichten (Aufbau) — ZWECK

> ***EINORDNUNG: W-26 ist eine ABLESUNG mit einem BEFUND, der größer ist als der Registereintrag.***
> **Das Register nennt `konterlattungMm` einen „toten Vertrag". Gemessen ist es der GANZE Block:
> `VorlagenDachdecker` führt SIEBZEHN Felder, und KEIN EINZIGES wird außerhalb der Vorlagendatei
> gelesen.**

```text
ENTSCHEIDUNG   GEBAUT   „deckungsneutral" — in ACHT Modulkoepfen ausdruecklich
WAECHTER       GEBAUT   dachformVorlagen.test.ts:561 haelt die ABWESENHEIT fest
VERTRAG        TOT      VorlagenDachdecker, 17 Felder, ausserhalb gelesen: 0
RECHNUNG       ZWEI ZAHLEN  rdnGrad + mindestneigungGrad, nur INNERHALB der Datei
MODUL          FEHLT    kein dachschicht/schichtaufbau/unterspannbahn — je 0
```

## Welches Problem des Anwenders löst dieses Werkzeug?

**Keines — und das ist hier eine ENTSCHEIDUNG und kein Versäumnis.** *Der Dachaufbau (Deckung,
Lattung, Unterdeckung) ist bewusst aus der Geometrie herausgehalten worden.*

**Acht Module sagen das in ihrem Kopf, wörtlich und je einzeln:**

```text
geometry/dachAusschnitt.ts:23      „KEINE Dacheindeckung, KEINE Statik"
geometry/aufbauOrientierung.ts:19  „KEINE Dacheindeckung/Material"
geometry/gaubeGeometrie.ts:28      „KEINE Dacheindeckung"
geometry/aufbauPlatzierung.ts:18   „KEINE Dacheindeckung/Material"
geometry/dachOeffnung.ts:14        „KEINE Dacheindeckung/Material/Statik"
geometry/linienBauteile.ts:10      „Material/Produkt, KEINE Dacheindeckung"
geometry/dachformVorlagen.ts:113   KEINE feste Dacheindeckung/kein Material
geometry/grundriss.ts:16           „KEINE Dacheindeckung/Material"
```

> ***Eine Entscheidung, die achtmal an der Stelle steht, an der man sie brechen würde, ist keine
> Notiz — sie ist eine Bauregel.*** **Und der Grund steht daneben** (`dachformVorlagen.ts:113-114`):
> *„Die Dacheindeckung wird ausschließlich über die separate Produktauswahl gewählt."*

## Der Wächter, der eine ABWESENHEIT sichert

`__tests__/dachformVorlagen.test.ts:561`:

> *„Deckungsneutral: `validateVorlage` erzeugt KEINE `EINDECKUNG_KATEGORIE`-Warnung mehr"* — **über
> ALLE verfügbaren Vorlagen gefahren.**

> ***Das ist die seltenere und wertvollere Bauform einer Zusage:*** *sie hält fest, dass etwas NICHT
> passiert.* **Wer die Eindeckungsprüfung versehentlich wieder einschaltet, wird rot** — *und zwar
> nicht an einem Beispiel, sondern an jeder Vorlage.*

## Und daneben liegt der Rest, den niemand liest

**`VorlagenDachdecker` (`:112-125`) — jede Vorlage trägt ihn, jedes Feld einzeln gemessen:**

| Feld | außerhalb gelesen | nur im Test |
|---|---|---|
| `deckungsHinweis` | **0** | 3 |
| `dachdeckungSeparatAuswaehlen` | **0** | 2 |
| `regeldachneigungAbhaengigVonMaterial` | **0** | 1 |
| `lattmassAbhaengigVonProdukt` | **0** | 1 |
| `empfohleneEindeckung` | **0** | 2 |
| `rdnGrad` · `mindestneigungGrad` | **0** | 0 |
| `battenDistCm` · `konterlattungMm` | **0** | 0 |
| `unterdeckungKlasse` · `firstausbildung` | **0** | 0 |
| `gratausbildung` · `kehlausbildung` | **0** | 0 |
| `ortgangausbildung` · `traufausbildung` | **0** | 0 |
| `entwaesserungHinweis` · `schneefangHinweis` · `lueftungHinweis` | **0** | 0 |

> **Siebzehn Felder, siebzehn Nullen.** *Zwei davon werden INNERHALB der Datei ausgewertet
> (`rdnGrad`, `mindestneigungGrad` → `3-FORMELN`); die übrigen fünfzehn werden gefüllt, gepflegt und nie
> gefragt.*

## Die Berichtigung einer eigenen Zahl, bevor sie jemand übernimmt

**Meine erste Messung meldete `validateVorlage` mit „produktiv 0".** *Das war falsch:* **mein Filter
schloss die eigene Datei aus, und der produktive Aufrufer steht genau dort** —
`applyVorlage` (`:1272`). *Die Prüfung läuft also; nur ihr Eindeckungsteil wurde entfernt.*

> ***Ein Filter, der „außerhalb" misst, beantwortet nicht die Frage „wird es gerufen".*** **H-8 in
> der eigenen Messvorschrift, und die zweite Messung trägt.**
