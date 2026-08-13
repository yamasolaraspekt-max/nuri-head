# W-31 · PV-Schnellbelegung — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Benutzte Formeln: KEINE — am Code erhoben

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| — | — | — |

**Die gesamte Mathematik des Moduls, sieben Aufrufe:**

```text
Math.max    :38 :39   Untergrenze 0 fuer Spalten und Reihen
            :49 :50   Untergrenze 0 fuer die Nutzflaeche
Math.floor  :38 :39   ganze Module, nie ein angefangenes
Math.round  :43       r2(), Rundung auf zwei Nachkommastellen
```

**Kein `Math.hypot`, kein `Math.sqrt`, kein `Math.cos`, kein `Math.atan2`.** *Was das Modul rechnet,
ist eine **Ganzzahl-Teilung mit Spaltkorrektur** (`(nutz + gap) / (mass + gap)`), zwei
Rechteckflächen und ein Prozentsatz.* **Dafür kennt die Sammlung keine Nummer, und sie braucht auch
keine.**

> **Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke.**

## Die drei roten und gelben Nummern in der Nachbarschaft — und warum KEINE zutrifft

*Die Sammlung führt drei Nummern, die man bei einem PV-Werkzeug erwartet. Alle drei einzeln
nachgesehen:*

| F-Nr | Ampel | trifft W-31? |
|---|---|---|
| **F-028** Azimut-Konvention an der Systemgrenze | 🔴 | **NEIN.** Gesperrt ist das **Durchreichen eines Azimut**. `PvEingabe` führt sieben Felder und **keine Richtung** — es gibt nichts durchzureichen. *Siehe `1-ZWECK` und `7-GRENZEN`.* |
| **F-050** Materialkennwerte je Deckung | 🟡 nur Näherung | **NEIN.** Deckungsmaterial ist W-23. W-31 kennt kein Material, nur Modulmaße. |
| **F-051** Zeitwerte je Gewerk | 🔴 gesperrt, unbelegt | **NEIN.** W-31 rechnet keine Montagezeit. |

> ***Die Registerzeile sagt „gesperrt bis F-028 🟢", und das ist richtig — für die VOLLSTÄNDIGE
> Belegung.*** *Für die Schnellstufe ist es kein Fall, und das ist gemessen und nicht behauptet:
> sieben Eingabefelder, kein Winkel, keine Himmelsrichtung.*

## Normative Größen

**Keine.** *Die zwei Vorgabewerte — Randabstand **300 mm**, Modulabstand **20 mm** (`:47-48`) —
sind **Voreinstellungen**, keine Normwerte.* **Beide sind vom Aufrufer überschreibbar**, und das
Modul belegt sie nicht mit einer Quelle.

> *Wer sie als Norm behandelt, macht aus einem Vorgabewert eine Zusage. Sie stehen im Code als
> Bequemlichkeit für den häufigen Fall — nicht als Regel des Fachs.*
