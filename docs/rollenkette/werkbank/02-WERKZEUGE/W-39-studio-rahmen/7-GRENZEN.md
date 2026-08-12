# W-39 · Studio-Rahmen — GRENZEN

> **Dieses Blatt ist Pflicht.**

## Die härteste Grenze ist zugleich der Zweck: W-39 fasst die App nicht an

```text
:5   "Additiv: die HausplanerApp bleibt unveraendert (nur ein optionales Flag blendet ihre
      Markenzeile aus)."
```

**Der einzige Eingriff ist `imStudio` (`:140`)** — *und er wirkt nicht hier, sondern in
`Kopfrahmen.tsx:142` (`{!imStudio && (`).* **Wer dem Rahmen mehr Wirkung auf die App zuschreibt,
beschreibt ein anderes Werkzeug.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| Projekte auf der Studio-Fläche anzeigen | der Controller reicht die Liste dorthin **bewusst nicht** durch (`:37-38`) | eine leere Liste — *ohne Erklärung am Bildschirm* |
| den Speicherstatus **bewerten** | die Regel lebt in `dashboard/speicherAnzeige.ts` | die Plakette, die von dort kommt |
| eine zweite Navigation stellen | ausgebaut in AUF-83-T2 (`:122-128`) | die Ticket-Navigation, die dieselbe Aufgabe erfüllt |
| einen Modus **erzwingen** | `modus` startet immer auf `'start'` (`:23`) | die Startseite, bei jedem Betreten |
| den Modus **merken** | kein `localStorage`, kein Store-Feld für `modus` | nach dem Neuladen wieder `'start'` |

**Die letzte Zeile ist gemessen, nicht vermutet:** *`modus` ist ein `React.useState` (`:23`) und
steht in keinem der beiden Stores.* **Ein Neuladen im Expertenmodus landet auf der Startseite.**

## Die Absagekette

**Es gibt keine — und das ist begründet:**

```text
Schicht 1/2 wirft benannten Fehler     -> W-39 ruft keine Domaenenfunktion auf
Schicht 3 faengt und uebersetzt        -> es gibt nichts zu fangen
Schicht 4 reicht DURCH                 -> kein catch in der Datei
Schicht 5 zeigt einen Satz             -> die Plakette, aber die kommt aus speicherAnzeige
```

| Fall | Fehlername | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| kein Speicherziel | **kein Fehler** — ein Zustand | `speicherAnzeige` | `4-BEDIENUNG.md` |
| Modul ohne Konfigurator | **kein Fehler** — eine Fläche | `fachFlaecheNach` (`:81`) | `FachFlaeche.tsx` |

> **Die zweite Zeile ist eine abgeschaffte Vertröstung, und der Code sagt es selbst (`:80`):**
> *„Kein Toast mehr: das Modul bekommt seine Fläche mit der Feldstruktur des späteren Panels."*
> **Aus „Konfigurator folgt" wurde etwas, das da ist.**

## Zwei Zusagen ohne Wächter

```text
K-05  „der Rueckweg in die gefuehrte Planung ist in JEDEM Modus sichtbar"
      belegt NUR im Kommentar :138-139 und durch die Bauart (ein gemeinsamer
      Container :109, Kopfzeile modusunabhaengig). KEIN Test.

vierter Modus   StudioModus erzwingt den Typ, aber niemand prueft, dass jeder
                Modus auch einen Schalter und einen Render-Zweig hat.
```

> **Benannt, nicht behoben** — *eine Ablesung baut keine Tests.* **Wer W-39 später erweitert, findet
> hier, wo die Sicherung fehlt.**

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| `projekte` auf der Studio-Fläche | **immer leer**, unabhängig vom Bestand | sobald jemand die Startseite dort als Bestandsanzeige liest |
| Toast-Dauer | fest **2600 ms** (`:70`) | bei langen Meldungen — kein Weg, ihn offen zu halten |

## Offener Anschluss — die Werkzeug-Lücke der Stufe 6

**Von den dreizehn importierten Modulen haben ACHT heute kein eigenes Werkzeug.** *Gemessen gegen
`02-WERKZEUGE/REGISTER.md`, und die Rechnung schließt: 13 − 5 = 8.*

```text
ERFASST — fuenf Module in vier Werkzeugen
                StartView                W-33   LEER
                GuidedView               W-34   BESCHRIEBEN
                dashboard/fahrschritte   W-34   BESCHRIEBEN   (dasselbe Werkzeug)
                ConfigWizard             W-35   LEER
                studioDaten              W-38   BESCHRIEBEN

KEIN WERKZEUG — acht
                HausplanerApp                 die eingebettete Vollansicht
                FachFlaeche                   die Fachplaner-Flaeche
                dashboard/fachFlaechen        19 Flaechen + KONFIGURATOR_NAMEN
                dashboard/speicherAnzeige     die Regel der Plakette
                dashboard/dialogFokus         Fokusfalle + istAusloeser
                studioUi                      Ikon
                state/uiState                 usePlannerUiStore
                store/hausplanerStore         useHausplanerStore
```

> **Ich hatte hier zuerst NEUN geschrieben und die Liste darunter trug acht Zeilen.** *Nachgerechnet
> statt abgeschrieben: fünf Module sind erfasst — `GuidedView` und `fahrschritte` teilen sich W-34,
> deshalb vier Werkzeuge bei fünf Modulen.* **Genau die Sorte Zahl, die dieses Blatt an anderer
> Stelle als „gezählt statt behauptet" lobt.**

> **Das ist keine Vermutung über fehlende Funktionen, sondern die gemessene Anschlusslücke.** *Sie
> kommt **nach** der Ablesung, nicht vorher — erst wenn der Rahmen steht, ist die Liste belegt.*
>
> **Zwei davon stechen hervor, weil sie Regeln tragen und keine Darstellung:**
> *`dashboard/speicherAnzeige` entscheidet, wann „Gespeichert" gesagt werden darf — mit zehn eigenen
> Tests; `dashboard/fachFlaechen` führt 19 Flächen mit Deckungsprüfung in beide Richtungen.*
> **Beide sind Ehrlichkeitskonstruktionen wie W-38 und W-34 und wären die nächsten sinnvollen
> Ablesungen.** *Die Entscheidung gehört dem Planner.*

## Was später kommen könnte

*Absichtlich weggelassen, damit es nicht als Fehler gemeldet wird:*

```text
- den Modus ueber Neuladen hinweg merken   -> waere eine Speicherentscheidung
- die Projektliste auf der Studio-Flaeche  -> haengt am Controller, nicht am Rahmen
- ein vierter Modus                        -> braucht Schalter, Zweig UND einen Waechter
```
