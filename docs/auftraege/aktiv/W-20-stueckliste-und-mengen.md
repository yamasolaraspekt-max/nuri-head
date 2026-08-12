# W-20 — Stückliste und Mengen. Das Holz ist gebaut und belegt, der Ziegel fehlt — und die Grenze verläuft anders als der Fahrplan sagt

```yaml
auftrag: "W-20"
werkzeug: "W-20 Stückliste und Mengen"
art: "STUFE 1 — Blatt schneiden, Ziel BESCHRIEBEN. Ablesung: der Kern ist gebaut und getestet."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 8300aa59
prioritaet: P2
anlass: "Klasse C, letztes voraussetzungsfreies Werkzeug. W-05 und W-08 sind BETRIEBSBESTAETIGT.
         Und der Zeitpunkt ist kein Zufall: W-23 ist gerade gebaut und F-053 eingetragen —
         genau die zwei Zulieferungen, die W-20s Luecke schliessen wuerden."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "geometry/holzMengen.ts (64 Z., 3 Exporte, 6 Testzusagen) · W-23 (BESCHRIEBEN) ·
            F-053 🟡 · F-011 ✓ (Polygonflaeche, aus W-08)"
```

## 1 · Der Fahrplan sagt „nur Holz vorhanden" — die Grenze verläuft anders

**Gemessen: `geometry/holzMengen.ts`, 64 Zeilen, drei Exporte, sechs Testzusagen.**

```text
export interface HolzStueck  { type?, name?, laenge? }
export interface HolzMengen  { sparrenLaenge, konterLaenge, lattenLaenge, sparrenAnzahl }
export function  holzMengenAusListe(holzliste)
```

> **Und hier muss ich eine eigene Fehlmessung berichtigen, bevor sie ins Blatt wandert:** *ich hatte
> nach `lattenMengen` gesucht und **0 Treffer** gemeldet — also „die Lattung fehlt". **Falsch.** Das
> Feld heißt `lattenLaenge`, und es ist da. Mein Muster suchte eine **Schreibweise**, nicht die Sache;
> das ist H-9 an mir, zum vierten Mal an diesem Tag. Die Lattung ist enthalten — als **laufende
> Meter**, nicht als Lattweite.*

**Die Unterscheidung, die daraus folgt und die das Werkzeug tragen muss:**

```text
W-21L / F-053    WIE WEIT liegen die Latten auseinander?   -> Lattmass in mm
W-20             WIE VIELE laufende Meter Latte?           -> Summe der echten Laengen
```

*Zwei verschiedene Fragen an dieselbe Latte. Wer sie verwechselt, sucht die eine Antwort in der
falschen Datei — genau das ist mir gerade passiert.*

## 2 · Was der Dateikopf selbst sagt — und es ist ein Befund über zwei Wahrheiten

```text
holzMengen.ts:1-8, woertlich:
  "Reparatur 7: reine, testbare Aggregation der Holzlaengen aus der ECHTEN, bereits in der
   3D-Geometrie erzeugten Holzliste des Planers.
   Problem vorher: Die Material-/Holzliste SCHAETZTE Sparren-/Lattenlaengen aus dem
   Rechteck-Rahmen (Anzahl x Hoehe bzw. Anzahl x Breite). Die Engine zeichnet die Staebe
   aber bereits an die reale (an Walm/L/T geclippte) Geometrie -> zwei Wahrheiten."
```

> **Das Werkzeug ist als Beseitigung einer zweiten Wahrheit entstanden.** *Vorher rechnete die
> Stückliste aus dem Rahmen, während die Darstellung die geclippte Geometrie zeigte — **zwei Zahlen
> für dasselbe Holz, und die falsche stand in der Liste.** Das gehört als Kern in `1-ZWECK`, nicht als
> Fußnote: es erklärt, warum die Aggregation über die **echte** Liste läuft und nicht über Formeln.*

**Und eine Feinheit, die der Code ausdrücklich begründet** (`:56-58`):

```text
Schiftsparren zaehlen als Gemeinsparren mit — "sonst fallen die an Kehle/Grat geclippten
Sparren aus Bauholz-m3 und Lohn heraus (Unter-Count)".
```

*Ein Unter-Count in der Stückliste ist ein Fehlbetrag im Angebot. Diese Zeile ist der Grund, warum
`name.startsWith("Sparren") || name.startsWith("Schiftsparren")` dort steht — sie gehört ins Blatt.*

## 3 · Die Lücke, gemessen statt geschätzt

```text
VORHANDEN   Sparren (Laenge + Anzahl) · Konterlatten (Laenge) · Traglatten (Laenge)
            -> vier Kennzahlen, aus der ECHTEN Geometrie, 6 Testzusagen

FEHLT       ZIEGELMENGE. Gemessen in geometry/:
              'stueck.*m2'   0 Treffer     <- Stueck je m2 gibt es nicht
              'bedarf'       1 Treffer     <- und der ist eine Gaubenbemerkung
            'ziegel' hat 16 Treffer, aber als TYP (RoofCovering in dachformVorlagen),
            nicht als MENGE. 'deckung' 79 Treffer, davon der erste eine LASTannahme
            in sparrenBerechnung — Deckung als Gewicht, nicht als Stueckzahl.
```

> **Die Ziegelmenge ist die einzige echte Lücke — und sie ist ab heute schließbar.** *W-23 ist gebaut
> (`BESCHRIEBEN`) und liefert `Deckbreite_min/max_mm` sowie `Bedarf_min/max_Stk_m2` je Modell; F-011
> (Polygonfläche) ist über W-08 ✓ und liefert die Fläche. **Was fehlt, ist nur die Multiplikation —
> und die Herkunft der Faktoren.***

## 4 · DECISION

```text
ZIEL          BESCHRIEBEN, nicht ENTWORFEN. Der Kern ist gebaut, getestet und im
              Dateikopf begruendet — das ist eine ABLESUNG. Anders als W-15 und W-27,
              deren Quelle ein Vertrag beziehungsweise ein Prototyp ist.

DER UMFANG    W-20 beschreibt, was das Werkzeug HEUTE kann: vier Holz-Kennzahlen aus
              der echten Geometrie. Die Ziegelmenge wird als GRENZE benannt, NICHT als
              Vorgabe — ein ENTWORFEN-Teil in einem BESCHRIEBEN-Blatt waere die
              Vermischung zweier Stufen, die Yamas Legende ausdruecklich trennt
              ("zwei PARALLELE Stufen, keine aufeinanderfolgenden").

DIE ZWEI      Die Zieladresse steht im Blatt, damit die Luecke nicht verwaist:
ZULIEFERUNGEN   Ziegelmenge = Dachflaeche (F-011) x Bedarf_Stk_m2 (W-23, Spalte 28/29)
                Lattenbedarf in lfm liegt VOR (lattenLaenge); die LATTWEITE kommt aus
                F-053 und ist eine ANDERE Frage — beide gehoeren nicht in dieses Blatt.

NICHT         Kein Bau. Keine Aenderung an holzMengen.ts. Keine Ziegelrechnung.
              Und ausdruecklich KEINE Schaetzung: der Dateikopf sagt, dass genau das
              der Fehler war, den dieses Werkzeug beseitigt hat.
```

## 5 · Abnahmekriterien

```text
W-20-1  (P1) Die sieben Blaetter lesen holzMengen.ts AB, mit Zeilenangabe: die drei
        Exporte (:23, :29, :44), die Typunterscheidung (:52-58) und die
        Gueltigkeitspruefung gueltigeLaenge (:41). Gegenprobe: jede Angabe nachlesbar.

W-20-2  (P1, DER TRAGENDE PUNKT) 1-ZWECK nennt den Grund aus dem Dateikopf WOERTLICH:
        die Stueckliste schaetzte vorher aus dem Rechteck-Rahmen, waehrend die Engine
        an die geclippte Geometrie zeichnete — ZWEI WAHRHEITEN. Ohne diesen Satz liest
        die naechste Rolle eine gewoehnliche Aggregation und weiss nicht, warum sie
        nicht rechnen darf.

W-20-3  (P1) Die Schiftsparren-Begruendung steht in 2-FUNKTION: sie zaehlen als
        Gemeinsparren mit, "sonst fallen die an Kehle/Grat geclippten Sparren aus
        Bauholz-m3 und Lohn heraus". Ein Unter-Count in der Stueckliste ist ein
        Fehlbetrag im Angebot — das ist die Begruendung, nicht die Bequemlichkeit.

W-20-4  (P1) 7-GRENZEN nennt die Ziegelmenge als FEHLEND, mit den Messzahlen:
        'stueck.*m2' 0 Treffer in geometry/, 'bedarf' 1 und der ist eine
        Gaubenbemerkung. Und die Zieladresse: F-011 x Bedarf_Stk_m2 aus W-23.
        AUSDRUECKLICH auch: 'ziegel' hat 16 und 'deckung' 79 Treffer — beide als TYP
        beziehungsweise LAST, nicht als Menge. Wer die 95 Treffer fuer eine
        Mengenrechnung haelt, sucht falsch.

W-20-5  (P1) Die Unterscheidung Lattmass gegen Lattenlaenge steht im Blatt. Grund: ich
        habe sie beim Messen selbst verwechselt und 'lattenMengen 0 Treffer' gemeldet,
        obwohl das Feld lattenLaenge heisst und gefuellt ist. Wer die zwei Fragen
        vermischt, sucht die Lattweite in der Mengendatei.

W-20-6  (must_preserve) resources/** und app/** byte-identisch — reine Doku-Stufe.
        holzMengen.ts und seine 6 Testzusagen unberuehrt.

W-20-7  (P1, §3 wird BELEGT) Beide Orte nach ARBEITSREGELN §3, beide Zahlen genannt,
        unmittelbar vor der ersten Aenderung. REGISTER.md liegt im Scope mehrerer
        W-Blaetter — vor der Registerzeile erneut messen.
```

## 6 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Sieben Doku-Blaetter; kein Code, kein Datenpfad.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier nicht behauptet.
ENTDECKUNG    das Signal ist eine Zahl, die aus einer FORMEL kommt statt aus der Liste:
              nennt ein Blatt eine Sparrenlaenge als "Anzahl x Hoehe", ist genau die
              zweite Wahrheit zurueck, die dieses Werkzeug beseitigt hat.
```

## 7 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile   ->  0        Zustandsfeld  ->  0        §3 ist FREI
docs/STATUS.md im Arbeitsbaum: 0
Dieser SCHNITT fasst das neue Blatt und STATUS.md an. Der BAU wuerde REGISTER.md
        beruehren und muss §3 dann erneut messen (W-20-7).
ZUR REIHENFOLGE: W-23 steht auf CODE_FERTIG beim Evaluator. W-20 verweist auf W-23s
        Spalten (Bedarf_Stk_m2), baut aber NICHT darauf — der Verweis ist eine
        Zieladresse, keine Abhaengigkeit. W-20 kann vor W-23s Abnahme gebaut werden.
W-20 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
warum_BESCHRIEBEN_und_nicht_ENTWORFEN: "der Kern ist gebaut, getestet und im Dateikopf begruendet —
        das ist eine Ablesung. Die fehlende Ziegelmenge wird als GRENZE benannt, nicht als Vorgabe;
        ein ENTWORFEN-Teil in einem BESCHRIEBEN-Blatt wuerde zwei Stufen vermischen, die Yamas
        Legende ausdruecklich als parallel und nicht aufeinanderfolgend fuehrt."
meine_eigene_fehlmessung: "ich meldete 'lattenMengen 0 Treffer' und schloss auf 'Lattung fehlt'. Das
        Feld heisst lattenLaenge und ist gefuellt. H-9 an mir, vierter Fall heute: mein Muster suchte
        eine Schreibweise statt die Sache. Berichtigt, und die Unterscheidung Lattmass gegen
        Lattenlaenge ist jetzt Kriterium W-20-5."
was_es_nicht_ist: "kein Angebot, keine Preisrechnung, keine Zeitwerte. F-051 ist 🔴 GESPERRT und
        A-16 hat gezeigt, was ein Lohnkostenbetrag ohne Herkunft anrichtet."
```
