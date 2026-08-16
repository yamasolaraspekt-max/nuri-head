# BAUVORRAT — welche Werkzeuge heute baubar sind

> **Angefordert von Yama am 16.08. abends, Abschnitt 3:** *„von den 37 beschriebenen Zeilen —
> welche erfüllen heute alle drei Bedingungen? Eine Liste, keine Prosa."*
>
> ***Diese Liste ist NICHT aus den Werkbank-Blättern gelesen, sondern aus dem Code gemessen*** —
> und der Grund steht gleich im ersten Abschnitt.

## Warum die Blätter die Antwort nicht hergeben

```text
41 Werkzeugverzeichnisse mit sieben Blaettern
davon mit einem einheitlichen Bestandsblock (RECHNUNG/WAECHTER/WERKZEUG):  5 bis 6
```

**Die Blätter beschreiben ihren Gegenstand in Prosa, nicht in Feldern.** *Die fünf mit Block sind
die von heute.* **Eine Auswertung über alle 41 wäre ein Muster über Fließtext — H-9, und genau die
Klasse Fehler, die diese Woche mehrfach Runden gekostet hat.**

## Die Messvorschrift, die statt dessen gilt

```text
1  RECHNUNG GEBAUT   ein Modul unter geometry/ mit mindestens einer Ausfuhr
2  NACHGERECHNET     es hat einen eigenen Waechter (__tests__/<modul>*)
3  BEDIENWEG FEHLT   KEIN produktiver Verbraucher in
                     app/ renderers/ commands/ store/ domain/
```

**Punkt 3 ist die eigentliche Auswahl:** *ein Modul, das gerechnet und geprüft ist und niemanden
hat, der es ruft, ist genau der Fall, den `A-35 Trimmen` gelöst hat* — **dort hatte
`geradenGeometrie` null Produktivaufrufer und hat jetzt einen.**

> ***Was diese Vorschrift NICHT misst, und es steht hier statt im Kleingedruckten:*** *ob der
> Bedienweg „schon existiert" im Sinne von A7 — also ob eine vorhandene Fläche ihn tragen kann.*
> **Das ist eine Entwurfsfrage und keine Messung; sie gehört an den Planner.** *Die Liste sagt
> „hier fehlt die Leitung", nicht „hier ist sie billig".*
>
> ***Und eine gemessene Feinheit:*** *ein `import type` zählt NICHT als Verbraucher.* **Ein Typ ist
> ein Vertrag, kein Aufruf** — `dachMesh.ts` importiert aus `dachformVorlagen` ausschließlich
> `EngineRoofShape` und ruft keine seiner 51 Ausfuhren.

## Die Liste — 10 von 55 Modulen

| Modul (`geometry/`) | Ausfuhren | Wächter | prod. Verbraucher |
|---|---|---|---|
| `dachformVorlagen` | **51** | ja | **0** |
| `aufbautenStatus` | 3 | ja | **0** |
| `dachVorlage` | 3 | ja | **0** |
| `masskette` | 3 | ja | **0** |
| `auswechslung` | 2 | ja | **0** |
| `integrationAbgleich` | 2 | ja | **0** |
| `sparrenTrennung` | 2 | ja | **0** |
| `dachTopologie` | 1 | ja | **0** |
| `treppenTypen` | 1 | ja | **0** |
| `wandFlaeche` | 1 | ja | **0** |

## Drei Einträge, die schon aus Ablesungen belegt sind

**Die Liste ist nicht neu geraten — drei ihrer Zeilen sind heute unabhängig gemessen worden:**

```text
auswechslung        W-29: analysiereAuswechslung, 11 Zusagen, NULL Produktivaufrufer —
                    und daneben behauptet dachOeffnung.ts:91 fest
                    'auswechslungErforderlich: true'.
dachformVorlagen    W-26 und W-43: drei Fachvertraege (Zimmerer 11 Felder,
                    Dachdecker 13, ZimmererFlags 13), ausserhalb der Datei
                    gelesen: 0.
sparrenTrennung     W-29: nennt auswechslung im Kommentar, ohne sie zu
                    importieren — eine Kante im Text, nicht im Code.
```

> ***Dass drei voneinander unabhängige Ablesungen dieselben Module treffen, ist der stärkste
> Hinweis, den diese Liste hat:*** *die Lücke ist keine Messartefakt, sie ist die Bauform der
> Insel.* **Gerechnet wird viel, angeschlossen wenig.**

## Was die Liste NICHT sagt

- **Keine Reihenfolge.** *Yamas Maßstab lautet „was die meisten anderen Werkzeuge freigibt,
  zuerst" — das ist eine Abhängigkeitsfrage und in dieser Messung nicht enthalten.*
- **Keine Empfehlung.** *`auswechslung` anzuschließen verwandelt ein „immer prüfen" in ein „hier
  nicht nötig" und trägt Verantwortung* (W-29, `7-GRENZEN`).
- **Keine Vollständigkeit über die Insel hinaus.** *Gemessen wurde `resources/planner/hausplaner`;
  Serverseitiges ist nicht enthalten.*

**Befehl, mit dem die Liste jederzeit neu erhoben wird:** *je Modul die Ausfuhren einlesen, jeden
Namen gegen `app/ renderers/ commands/ store/ domain/` halten, Treffer zählen; Wächter über
`__tests__/<modul>*`.* **Keine feste Zahl in diesem Blatt — die 10 und die 55 sind der Stand vom
16.08. und driften mit jedem Anschluss.**
