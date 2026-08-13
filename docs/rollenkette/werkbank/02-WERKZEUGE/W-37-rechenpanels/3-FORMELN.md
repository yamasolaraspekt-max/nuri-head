# W-37 · Rechenpanels — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Benutzte Formeln: KEINE — und hier ist das eine Aussage über die Bauart

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| — | — | — |

**Gemessen über beide Dateien:**

```text
app/dashboard/enginePanels.ts   540 Z.   Math.*  ->  NULL Treffer
app/EngineFlaeche.tsx           199 Z.   Math.*  ->  NULL Treffer
```

> ***Kein einziger Rechenaufruf in 739 Zeilen.*** *Das ist kein Zufall und keine Lücke — es ist die
> Bauform: **die Panels wandeln und rufen auf, sie rechnen nicht.*** Wer hier eine Formel sucht,
> sucht am falschen Ort.

## Wohin die Rechnungen gehen — acht Aufrufe, sieben Engines

```text
:164  berechneTreppe(alsTreppenEingabe(...))
:227  berechneSparren(alsSparrenEingabe(...))        <- N-003, traegt den Vorbehalt
:266  fbhAuslegung(alsFbhEingabe(...))
:301  bewerteDeckung(..., alsBetriebsBedingung(...)) <- abweichender Rumpf, s. u.
:329  berechneUw(alsUwEingabe(...))
:356  pruefeAbwasser(alsAbwasserEingabe(...))
:377  bewerteArbeitsdreieck(alsArbeitsdreieck(...))
:403  pvSchnellBelegung(alsPvEingabe(...))
```

**Sieben der acht sind einzeilig und haben dieselbe Gestalt** — *Engine, Adapter, Typwandlung.*

**Das achte (`:301`, `engine-heizkoerper`) hat einen mehrzeiligen Rumpf** — *es reicht zwei Zahlen
neben der `BetriebsBedingung` durch und benennt ein Feld um (`bestanden` aus `ausreichend`).*
**Der Kommentar darüber zieht dieselbe Grenze ausdrücklich:**

> *„Der Wert wird **unverändert durchgereicht**, nur unter dem Namen, den die Hülle kennt. **Nichts
> wird gerechnet, nichts entschieden** — wäre hier ein eigener Grenzwert, wäre es ein Defekt nach
> AUF-33 §3a."*

> **Genau darum ist die Stelle wichtig für dieses Blatt:** *sie ist die einzige, an der ein Panel
> mehr tut als weiterreichen — und der Code sagt selbst, wo die Grenze läuft.* **Ein eigener
> Grenzwert an dieser Stelle wäre eine zweite Wahrheit neben der Engine.**

## Die Normbezüge, die W-37 TRÄGT statt sie zu rechnen

**Die Registerzeile führt W-37 unter `N-001…N-003`.** *Das ist richtig — aber nicht, weil hier
gerechnet würde:*

| Norm | wo sie wirklich gerechnet wird | was W-37 damit tut |
|---|---|---|
| **N-001** Bodenschneelast · **N-002** Formbeiwert | `geometry/sparrenBerechnung.ts` | reicht die Eingaben hin |
| **N-003** Sparren-Vorbemessung · 🟡 **FACH-GATE** | `geometry/sparrenBerechnung.ts` | **trägt den Vorbehalt in die Ausgabe** (A-14) |

> ***Der Unterschied ist der ganze Punkt dieses Blattes.*** *W-37 rechnet keine Norm — es ist die
> Stelle, an der eine normbehaftete Zahl **den Anwender erreicht**. Und genau dort verlangt A-14,
> dass sie ihren Vorbehalt mitbringt.* Siehe `1-ZWECK` und `6-PRUEFUNG`.

## Normative Größen

**Keine eigenen.** *Die Grundlagen-Zeilen der Panels nennen Normen als Herkunft der aufgerufenen
Engine* — etwa `engine-fensterprodukt` (`:310`): *„DIN EN ISO 10077-1 — Uw = (Ag·Ug + Af·Uf + lg·Psi)
/ (Ag + Af)"*. **Das ist eine Angabe ÜBER die Engine, keine Rechnung in diesem Modul.**
